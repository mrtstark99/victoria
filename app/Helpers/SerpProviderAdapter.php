<?php
/**
 * SERP Provider Adapter — HTTP fetching from SERP provider APIs
 * Supports: SerpAPI, DataForSEO, Google Custom Search JSON API
 * Extracted from SerpService for single-responsibility.
 */

namespace Helpers;

class SerpProviderAdapter {
    private string $apiKey;
    private string $apiSecondary;

    public function __construct(string $apiKey, string $apiSecondary) {
        $this->apiKey = $apiKey;
        $this->apiSecondary = $apiSecondary;
    }

    // ───────────────────────── Provider Adapters ─────────────────────────

    public function fetchSerpAPI(string $keyword, string $country, string $lang, int $num): array {
        $params = http_build_query([
            'q' => $keyword,
            'gl' => $country,
            'hl' => $lang,
            'num' => $num,
            'api_key' => $this->apiKey,
            'engine' => 'google'
        ]);

        $response = $this->httpGet("https://serpapi.com/search.json?{$params}");
        if (!$response['success']) return $response;

        $data = json_decode($response['body'], true);
        if (empty($data['organic_results'])) {
            return ['success' => false, 'error' => 'No organic results from SerpAPI.'];
        }

        $results = [];
        foreach (array_slice($data['organic_results'], 0, $num) as $i => $item) {
            $results[] = [
                'position' => $i + 1,
                'title' => $item['title'] ?? '',
                'url' => $item['link'] ?? '',
                'snippet' => $item['snippet'] ?? '',
                'displayed_url' => $item['displayed_link'] ?? '',
                'headings' => $this->scrapeHeadings($item['link'] ?? ''),
                'word_count' => null
            ];
        }

        return [
            'success' => true,
            'data' => [
                'results' => $results,
                'serp_features' => $this->extractSerpFeatures($data),
                'total_results' => $data['search_information']['total_results'] ?? null,
                'people_also_ask' => array_map(fn($q) => $q['question'] ?? '', $data['related_questions'] ?? []),
                'related_searches' => array_map(fn($s) => $s['query'] ?? '', $data['related_searches'] ?? [])
            ]
        ];
    }

    public function fetchDataForSEO(string $keyword, string $country, string $lang, int $num): array {
        $payload = [[
            'keyword' => $keyword,
            'location_code' => $this->getDataForSEOLocationCode($country),
            'language_code' => $lang,
            'depth' => $num,
            'se_domain' => "google.{$country}"
        ]];

        $response = $this->httpPost(
            'https://api.dataforseo.com/v3/serp/google/organic/live/regular',
            json_encode($payload),
            ['Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecondary)]
        );

        if (!$response['success']) return $response;

        $data = json_decode($response['body'], true);
        $items = $data['tasks'][0]['result'][0]['items'] ?? [];

        $results = [];
        foreach (array_slice($items, 0, $num) as $item) {
            if (($item['type'] ?? '') !== 'organic') continue;
            $results[] = [
                'position' => $item['rank_absolute'] ?? count($results) + 1,
                'title' => $item['title'] ?? '',
                'url' => $item['url'] ?? '',
                'snippet' => $item['description'] ?? '',
                'displayed_url' => $item['breadcrumb'] ?? '',
                'headings' => $this->scrapeHeadings($item['url'] ?? ''),
                'word_count' => null
            ];
        }

        return [
            'success' => true,
            'data' => [
                'results' => $results,
                'serp_features' => [],
                'total_results' => $data['tasks'][0]['result'][0]['se_results_count'] ?? null,
                'people_also_ask' => [],
                'related_searches' => []
            ]
        ];
    }

    public function fetchGoogleCSE(string $keyword, string $country, string $lang, int $num): array {
        if (empty($this->apiSecondary)) {
            return ['success' => false, 'error' => 'Google Custom Search Engine ID (SERP_API_SECONDARY) is not configured.'];
        }

        $results = [];
        $startIndex = 1;
        $remaining = min($num, 10);

        while ($remaining > 0 && $startIndex <= 91) {
            $batchSize = min($remaining, 10);
            $params = http_build_query([
                'q' => $keyword,
                'key' => $this->apiKey,
                'cx' => $this->apiSecondary,
                'gl' => $country,
                'hl' => $lang,
                'num' => $batchSize,
                'start' => $startIndex
            ]);

            $response = $this->httpGet("https://www.googleapis.com/customsearch/v1?{$params}");
            if (!$response['success']) return $response;

            $data = json_decode($response['body'], true);
            if (!empty($data['error'])) {
                return ['success' => false, 'error' => 'Google CSE Error: ' . ($data['error']['message'] ?? 'Unknown')];
            }

            foreach ($data['items'] ?? [] as $i => $item) {
                $results[] = [
                    'position' => $startIndex + $i,
                    'title' => $item['title'] ?? '',
                    'url' => $item['link'] ?? '',
                    'snippet' => $item['snippet'] ?? '',
                    'displayed_url' => $item['displayLink'] ?? '',
                    'headings' => $this->scrapeHeadings($item['link'] ?? ''),
                    'word_count' => null
                ];
            }

            $startIndex += $batchSize;
            $remaining -= $batchSize;

            if (empty($data['items'])) break;
        }

        return [
            'success' => true,
            'data' => [
                'results' => $results,
                'serp_features' => [],
                'total_results' => $data['searchInformation']['totalResults'] ?? null,
                'people_also_ask' => [],
                'related_searches' => []
            ]
        ];
    }

    // ───────────────────────── Utility Methods ─────────────────────────

    /**
     * Scrape H2/H3 headings from a URL (lightweight, with timeout)
     */
    public function scrapeHeadings(string $url): array {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return ['h2' => [], 'h3' => []];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; BlogSEOBot/1.0)',
            CURLOPT_RANGE => '0-524288'
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$html) {
            return ['h2' => [], 'h3' => []];
        }

        $h2 = [];
        $h3 = [];
        if (preg_match_all('/<h2[^>]*>(.*?)<\/h2>/isu', $html, $matches)) {
            $h2 = array_map(fn($h) => trim(strip_tags($h)), $matches[1]);
        }
        if (preg_match_all('/<h3[^>]*>(.*?)<\/h3>/isu', $html, $matches)) {
            $h3 = array_map(fn($h) => trim(strip_tags($h)), $matches[1]);
        }

        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);

        return [
            'h2' => array_slice(array_filter($h2), 0, 20),
            'h3' => array_slice(array_filter($h3), 0, 30),
            'estimated_word_count' => str_word_count($text)
        ];
    }

    /**
     * Extract SERP features (Featured Snippet, PAA, etc.)
     */
    public function extractSerpFeatures(array $serpApiData): array {
        $features = [];
        if (!empty($serpApiData['answer_box'])) $features[] = 'featured_snippet';
        if (!empty($serpApiData['related_questions'])) $features[] = 'people_also_ask';
        if (!empty($serpApiData['knowledge_graph'])) $features[] = 'knowledge_graph';
        if (!empty($serpApiData['local_results'])) $features[] = 'local_pack';
        if (!empty($serpApiData['shopping_results'])) $features[] = 'shopping';
        if (!empty($serpApiData['inline_videos'])) $features[] = 'video_carousel';
        if (!empty($serpApiData['inline_images'])) $features[] = 'image_pack';
        return $features;
    }

    private function getDataForSEOLocationCode(string $country): int {
        $map = [
            'vn' => 2704, 'us' => 2840, 'uk' => 2826, 'sg' => 2702,
            'jp' => 2392, 'kr' => 2410, 'th' => 2764, 'id' => 2360,
            'my' => 2458, 'ph' => 2608, 'au' => 2036, 'de' => 2276,
            'fr' => 2250, 'in' => 2356
        ];
        return $map[strtolower($country)] ?? 2704;
    }

    // ───────────────────────── HTTP Helpers ─────────────────────────

    public function httpGet(string $url): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'BlogCMS-SEO-Agent/1.0'
        ]);

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false || $httpCode >= 400) {
            return ['success' => false, 'error' => "HTTP {$httpCode}: " . ($error ?: 'Request failed')];
        }

        return ['success' => true, 'body' => $body, 'http_code' => $httpCode];
    }

    public function httpPost(string $url, string $body, array $extraHeaders = []): array {
        $ch = curl_init($url);
        $headers = array_merge(['Content-Type: application/json'], $extraHeaders);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'BlogCMS-SEO-Agent/1.0'
        ]);

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false || $httpCode >= 400) {
            return ['success' => false, 'error' => "HTTP {$httpCode}: " . ($error ?: 'Request failed')];
        }

        return ['success' => true, 'body' => $responseBody, 'http_code' => $httpCode];
    }
}
