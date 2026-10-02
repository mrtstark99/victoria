<?php
/**
 * SERP Analysis Service — Multi-adapter SERP scraping
 * Supports: SerpAPI, DataForSEO, Google Custom Search JSON API
 * Provider adapters are in SerpProviderAdapter.php
 */

namespace Helpers;

class SerpService {
    private string $provider;
    private string $apiKey;
    private string $apiSecondary;
    private int $cacheTtl;
    private SerpProviderAdapter $adapter;

    public function __construct() {
        $this->provider     = defined('SERP_API_PROVIDER') ? SERP_API_PROVIDER : 'google_cse';
        $this->apiKey       = defined('SERP_API_KEY')      ? SERP_API_KEY      : '';
        $this->apiSecondary = defined('SERP_API_SECONDARY') ? SERP_API_SECONDARY : '';
        $this->cacheTtl     = defined('SERP_CACHE_TTL')    ? (int)SERP_CACHE_TTL : 86400;
        $this->adapter      = new SerpProviderAdapter($this->apiKey, $this->apiSecondary);
    }

    /**
     * Analyze SERP for a keyword — returns structured competitor data
     */
    public function analyzeSERP(string $keyword, string $country = 'vn', string $language = 'vi', int $numResults = 10): array {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'SERP API key is not configured. Set SERP_API_KEY in config.php.'];
        }

        $cacheKey = 'serp_' . md5($keyword . $country . $language . $numResults);
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return ['success' => true, 'data' => $cached, 'source' => 'cache'];
        }

        $result = match ($this->provider) {
            'serpapi'    => $this->adapter->fetchSerpAPI($keyword, $country, $language, $numResults),
            'dataforseo' => $this->adapter->fetchDataForSEO($keyword, $country, $language, $numResults),
            'google_cse' => $this->adapter->fetchGoogleCSE($keyword, $country, $language, $numResults),
            default      => ['success' => false, 'error' => "Unknown SERP provider: {$this->provider}"]
        };

        if ($result['success'] && !empty($result['data'])) {
            $result['data']['search_intent'] = $this->classifyIntent($keyword, $result['data']['results'] ?? []);
            $result['data']['keyword']        = $keyword;
            $result['data']['country']        = $country;
            $result['data']['language']       = $language;
            $result['data']['analyzed_at']    = date('c');

            $this->setCache($cacheKey, $result['data']);
        }

        return $result;
    }

    /**
     * Generate content outline from SERP analysis
     */
    public function generateOutline(array $serpData): array {
        $results = $serpData['results'] ?? [];
        if (empty($results)) {
            return ['success' => false, 'error' => 'No SERP results to generate outline from.'];
        }

        $allH2 = [];
        $allH3 = [];
        $wordCounts = [];

        foreach ($results as $r) {
            foreach ($r['headings']['h2'] ?? [] as $h) {
                $normalized = mb_strtolower(trim($h), 'UTF-8');
                $allH2[$normalized] = ($allH2[$normalized] ?? 0) + 1;
            }
            foreach ($r['headings']['h3'] ?? [] as $h) {
                $normalized = mb_strtolower(trim($h), 'UTF-8');
                $allH3[$normalized] = ($allH3[$normalized] ?? 0) + 1;
            }
            if (isset($r['word_count']) && $r['word_count'] > 0) {
                $wordCounts[] = $r['word_count'];
            }
        }

        arsort($allH2);
        arsort($allH3);

        $avgWordCount         = !empty($wordCounts) ? (int)round(array_sum($wordCounts) / count($wordCounts)) : 1500;
        $recommendedWordCount = (int)round($avgWordCount * 1.2);

        return [
            'success' => true,
            'data' => [
                'search_intent'            => $serpData['search_intent'] ?? 'informational',
                'recommended_word_count'   => $recommendedWordCount,
                'avg_competitor_word_count' => $avgWordCount,
                'common_h2_topics'         => array_slice($allH2, 0, 15, true),
                'common_h3_subtopics'      => array_slice($allH3, 0, 20, true),
                'competitor_count'         => count($results),
                'content_gap_hints'        => $this->findContentGaps($results)
            ]
        ];
    }

    // ───────────────────────── Analysis Methods ─────────────────────────

    /**
     * Classify search intent from SERP signals
     */
    private function classifyIntent(string $keyword, array $results): string {
        $kw = mb_strtolower($keyword, 'UTF-8');

        $transactional = ['mua', 'giá', 'bán', 'đặt hàng', 'khuyến mãi', 'giảm giá', 'rẻ nhất', 'order', 'buy', 'price', 'cheap', 'deal', 'coupon', 'discount'];
        foreach ($transactional as $signal) {
            if (mb_strpos($kw, $signal) !== false) return 'transactional';
        }

        $commercial = ['so sánh', 'review', 'đánh giá', 'tốt nhất', 'top', 'nên mua', 'vs', 'versus', 'best', 'compare', 'alternative'];
        foreach ($commercial as $signal) {
            if (mb_strpos($kw, $signal) !== false) return 'commercial';
        }

        $navigational = ['login', 'đăng nhập', 'trang chủ', 'official', 'website'];
        foreach ($navigational as $signal) {
            if (mb_strpos($kw, $signal) !== false) return 'navigational';
        }

        $infoPatterns = ['là gì', 'cách', 'hướng dẫn', 'tại sao', 'bao nhiêu', 'khi nào', 'ở đâu', 'what is', 'how to', 'why', 'guide', 'tutorial', 'checklist'];
        foreach ($infoPatterns as $signal) {
            if (mb_strpos($kw, $signal) !== false) return 'informational';
        }

        $blogCount = 0;
        $ecomCount = 0;
        foreach ($results as $r) {
            $url = $r['url'] ?? '';
            if (preg_match('/blog|article|post|wiki|guide|huong-dan/i', $url)) $blogCount++;
            if (preg_match('/product|shop|store|gia|mua|deal/i', $url)) $ecomCount++;
        }

        if ($ecomCount > $blogCount) return 'commercial';
        return 'informational';
    }

    /**
     * Find content gaps — topics covered by few competitors
     */
    private function findContentGaps(array $results): array {
        $allTopics = [];
        foreach ($results as $r) {
            foreach ($r['headings']['h2'] ?? [] as $h) {
                $normalized = mb_strtolower(trim($h), 'UTF-8');
                if (mb_strlen($normalized) > 5) {
                    $allTopics[$normalized] = ($allTopics[$normalized] ?? 0) + 1;
                }
            }
        }

        $gaps = [];
        foreach ($allTopics as $topic => $count) {
            if ($count <= 2 && $count >= 1) {
                $gaps[] = $topic;
            }
        }

        return array_slice($gaps, 0, 10);
    }

    // ───────────────────────── Cache Layer ─────────────────────────

    private function getCache(string $key): ?array {
        try {
            $db   = \Database::getInstance();
            $stmt = $db->prepare("SELECT payload, expires_at FROM analytics_cache WHERE cache_key = ? AND expires_at > datetime('now','localtime') LIMIT 1");
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            if ($row) {
                return json_decode($row['payload'], true);
            }
        } catch (\Exception $e) {
            // Silently fail cache lookup
        }
        return null;
    }

    private function setCache(string $key, array $data): void {
        try {
            $db        = \Database::getInstance();
            $expiresAt = date('Y-m-d H:i:s', time() + $this->cacheTtl);
            $payload   = json_encode($data, JSON_UNESCAPED_UNICODE);
            $stmt      = $db->prepare("INSERT OR REPLACE INTO analytics_cache (cache_key, payload, expires_at, updated_at) VALUES (?, ?, ?, datetime('now','localtime'))");
            $stmt->execute([$key, $payload, $expiresAt]);
        } catch (\Exception $e) {
            // Silently fail cache write
        }
    }
}
