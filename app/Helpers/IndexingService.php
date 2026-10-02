<?php
/**
 * Indexing Service — IndexNow, Sitemap Ping, Google Indexing API
 */

namespace Helpers;

class IndexingService {
    /**
     * Ping IndexNow API (supports Bing, Yandex, Naver, Cloudflare)
     */
    public static function pingIndexNow(array $urls): array {
        if (!defined('INDEXNOW_ENABLED') || !INDEXNOW_ENABLED) {
            return ['success' => false, 'error' => 'IndexNow is disabled. Set INDEXNOW_ENABLED=true in config.'];
        }

        $apiKey = defined('INDEXNOW_API_KEY') ? INDEXNOW_API_KEY : '';
        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'IndexNow API key not configured.'];
        }

        $host = self::getSiteHost();
        if (empty($host)) {
            return ['success' => false, 'error' => 'Could not determine site host.'];
        }

        // Ensure key file exists
        self::ensureKeyFile($apiKey);

        $payload = json_encode([
            'host' => $host,
            'key' => $apiKey,
            'keyLocation' => "https://{$host}/{$apiKey}.txt",
            'urlList' => $urls
        ]);

        $results = [];
        $endpoints = [
            'bing' => 'https://api.indexnow.org/indexnow',
            'yandex' => 'https://yandex.com/indexnow',
        ];

        foreach ($endpoints as $engine => $endpoint) {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            $results[$engine] = [
                'http_code' => $httpCode,
                'success' => $httpCode >= 200 && $httpCode < 300,
                'error' => $error ?: ($httpCode >= 400 ? "HTTP {$httpCode}" : null)
            ];
        }

        return [
            'success' => true,
            'data' => [
                'urls_submitted' => $urls,
                'count' => count($urls),
                'results' => $results,
                'submitted_at' => date('c')
            ]
        ];
    }

    /**
     * Ping Google & Bing that sitemap has been updated
     */
    public static function pingSitemap(): array {
        $sitemapUrl = self::getBaseUrl() . '/sitemap.xml';
        $results = [];

        $pingUrls = [
            'google' => 'https://www.google.com/ping?sitemap=' . urlencode($sitemapUrl),
            'bing' => 'https://www.bing.com/ping?sitemap=' . urlencode($sitemapUrl),
        ];

        foreach ($pingUrls as $engine => $url) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $results[$engine] = [
                'http_code' => $httpCode,
                'success' => $httpCode >= 200 && $httpCode < 300,
                'ping_url' => $url
            ];
        }

        return [
            'success' => true,
            'data' => [
                'sitemap_url' => $sitemapUrl,
                'results' => $results,
                'pinged_at' => date('c')
            ]
        ];
    }

    /**
     * Regenerate sitemap.xml and return URL
     */
    public static function regenerateSitemap(): array {
        try {
            if (class_exists('\Controllers\BlogController')) {
                \Controllers\BlogController::generateSitemapFile();
                return [
                    'success' => true,
                    'data' => [
                        'sitemap_url' => self::getBaseUrl() . '/sitemap.xml',
                        'regenerated_at' => date('c')
                    ]
                ];
            }
            return ['success' => false, 'error' => 'BlogController not available.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Sitemap regeneration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Full publish pipeline: regenerate sitemap + ping IndexNow + ping sitemap
     */
    public static function onPostPublished(int $postId): array {
        $post = \Models\Post::findById($postId);
        if (!$post) {
            return ['success' => false, 'error' => 'Post not found.'];
        }

        $baseUrl = self::getBaseUrl();
        $postUrl = $baseUrl . '/blog/' . $post['slug'];

        $results = [
            'post_id' => $postId,
            'post_url' => $postUrl,
            'actions' => []
        ];

        // 1. Regenerate sitemap
        $sitemap = self::regenerateSitemap();
        $results['actions']['regenerate_sitemap'] = $sitemap['success'];

        // 2. Ping IndexNow
        if (defined('INDEXNOW_ENABLED') && INDEXNOW_ENABLED) {
            $indexNow = self::pingIndexNow([$postUrl]);
            $results['actions']['index_now'] = $indexNow['success'];
            $results['index_now_details'] = $indexNow['data'] ?? $indexNow['error'] ?? null;
        }

        // 3. Ping sitemap to search engines
        $sitemapPing = self::pingSitemap();
        $results['actions']['sitemap_ping'] = $sitemapPing['success'];

        $results['completed_at'] = date('c');
        return ['success' => true, 'data' => $results];
    }

    // ─────────────── Helpers ───────────────

    private static function getSiteHost(): string {
        if (defined('APP_URL') && !empty(APP_URL)) {
            $parsed = parse_url(APP_URL);
            return $parsed['host'] ?? '';
        }
        return $_SERVER['HTTP_HOST'] ?? 'localhost';
    }

    private static function getBaseUrl(): string {
        if (defined('APP_URL') && !empty(APP_URL)) {
            return rtrim(APP_URL, '/');
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:5000';
        return $protocol . '://' . $host;
    }

    /**
     * Ensure the IndexNow key verification file exists
     */
    private static function ensureKeyFile(string $apiKey): void {
        $keyFilePath = (defined('APP_ROOT') ? APP_ROOT : dirname(dirname(__DIR__))) . '/public/' . $apiKey . '.txt';
        if (!file_exists($keyFilePath)) {
            @file_put_contents($keyFilePath, $apiKey);
        }
    }
}
