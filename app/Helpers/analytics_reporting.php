<?php
/**
 * Analytics Reporting — Dashboard & Page Performance Data Aggregation
 * Extracted from analytics_helper.php for single-responsibility.
 * Depends on: analytics_helper.php (must be loaded first).
 */

/**
 * Fetch combined GA4 + Search Console data for the admin dashboard.
 */
function analyticsDashboardData(int $days = 28, bool $forceRefresh = false): array
{
    $days = in_array($days, [7, 28, 90], true) ? $days : 28;
    $cacheKey = 'google_dashboard_' . $days;
    if (!$forceRefresh) {
        $cached = analyticsCacheGet($cacheKey);
        if ($cached !== null) {
            $cached['from_cache'] = true;
            return $cached;
        }
    }

    $propertyId = preg_replace('/\D+/', '', (string)getSetting('ga_property_id', ''));
    $siteUrl = trim((string)getSetting('gsc_site_url', ''));
    $credentials = analyticsGetCredentials();

    $data = [
        'days' => $days,
        'from_cache' => false,
        'ga' => null,
        'gsc' => null,
        'errors' => [],
        'configured' => [
            'ga' => $propertyId !== '',
            'gsc' => $siteUrl !== '',
            'credentials' => $credentials !== null,
        ],
    ];

    if ($credentials === null || $propertyId === '' || $siteUrl === '') {
        $data['errors'][] = 'Hệ thống chưa được kết nối API Google. Vui lòng tải lên tệp xác thực Google Cloud Service Account và cấu hình thông tin ở bên dưới để lấy dữ liệu thực tế.';
        return $data;
    }

    $tokenResult = analyticsGoogleAccessToken($credentials);
    if (!$tokenResult['ok']) {
        $data['errors'][] = 'Xác thực Google lỗi: ' . $tokenResult['error'];
        return $data;
    }

    $gaResult = analyticsFetchGa4($propertyId, $tokenResult['token'], $days);
    if ($gaResult['ok']) {
        $data['ga'] = $gaResult;
    } else {
        $data['errors'][] = 'Lỗi kết nối GA4: ' . $gaResult['error'];
    }

    $gscResult = analyticsFetchSearchConsole($siteUrl, $tokenResult['token'], $days);
    if ($gscResult['ok']) {
        $data['gsc'] = $gscResult;
    } else {
        $data['errors'][] = 'Lỗi kết nối Search Console: ' . $gscResult['error'];
    }

    if ($data['ga'] !== null || $data['gsc'] !== null) {
        analyticsCacheSet($cacheKey, $data, 30);
    }
    return $data;
}

/**
 * Fetch per-page GA4 + Search Console performance data for all published posts.
 */
function analyticsPagePerformanceData(int $days = 28, bool $forceRefresh = false): array
{
    $days = in_array($days, [7, 28, 90], true) ? $days : 28;
    $cacheKey = 'google_page_performance_' . $days;
    if (!$forceRefresh) {
        $cached = analyticsCacheGet($cacheKey);
        if ($cached !== null) {
            $cached['from_cache'] = true;
            return $cached;
        }
    }

    $propertyId = preg_replace('/\D+/', '', (string)getSetting('ga_property_id', ''));
    $siteUrl = trim((string)getSetting('gsc_site_url', ''));
    $credentials = analyticsGetCredentials();

    $data = [
        'days' => $days,
        'from_cache' => false,
        'performance' => [],
        'errors' => [],
        'configured' => [
            'ga' => $propertyId !== '',
            'gsc' => $siteUrl !== '',
            'credentials' => $credentials !== null,
        ]
    ];

    $db = Database::getInstance();
    $stmt = $db->query("SELECT slug, title FROM posts WHERE status = 'published'");
    $posts = $stmt->fetchAll();

    if ($credentials === null || $propertyId === '' || $siteUrl === '') {
        foreach ($posts as $post) {
            $url = "/blog/" . $post['slug'];
            $data['performance'][] = [
                'url' => $url,
                'title' => $post['title'],
                'gsc' => ['clicks' => 0, 'impressions' => 0, 'ctr' => 0.0, 'position' => 0.0],
                'ga4' => ['organic_sessions' => 0, 'engagement_rate' => 0.0, 'leads' => 0, 'conversion_rate' => 0.0]
            ];
        }
        return $data;
    }

    $tokenResult = analyticsGoogleAccessToken($credentials);
    if (!$tokenResult['ok']) {
        $data['errors'][] = $tokenResult['error'];
        return $data;
    }

    $token = $tokenResult['token'];
    $normalizePath = static function (string $url): string {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $path = trim($path);
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        return $path === '' ? '/' : $path;
    };

    $pages = [];
    $gaData = [];

    if ($siteUrl !== '') {
        $gscUrl = 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($siteUrl) . '/searchAnalytics/query';
        $gscPayload = [
            'startDate' => date('Y-m-d', strtotime('-' . $days . ' days')),
            'endDate' => date('Y-m-d', strtotime('-1 day')),
            'type' => 'web',
            'dataState' => 'final',
            'dimensions' => ['page', 'query'],
            'rowLimit' => 1000
        ];
        $gscResult = analyticsGoogleApiPost($gscUrl, $token, $gscPayload);
        if ($gscResult['ok'] && isset($gscResult['data']['rows'])) {
            foreach ($gscResult['data']['rows'] as $row) {
                $pageUrl = $row['keys'][0] ?? '';
                $query = $row['keys'][1] ?? '';
                $clicks = (int)round($row['clicks'] ?? 0);
                $impressions = (int)round($row['impressions'] ?? 0);
                $ctr = (float)($row['ctr'] ?? 0);
                $position = (float)($row['position'] ?? 0);

                $normPath = $normalizePath($pageUrl);

                if (!isset($pages[$normPath])) {
                    $pages[$normPath] = ['clicks' => 0, 'impressions' => 0, 'position_sum' => 0, 'top_queries' => []];
                }
                $pages[$normPath]['clicks'] += $clicks;
                $pages[$normPath]['impressions'] += $impressions;
                $pages[$normPath]['position_sum'] += ($position * $impressions);

                if (count($pages[$normPath]['top_queries']) < 5) {
                    $pages[$normPath]['top_queries'][] = [
                        'query' => $query,
                        'clicks' => $clicks,
                        'impressions' => $impressions,
                        'ctr' => $ctr,
                        'position' => $position
                    ];
                }
            }
            foreach ($pages as &$p) {
                $p['ctr'] = $p['impressions'] > 0 ? $p['clicks'] / $p['impressions'] : 0;
                $p['position'] = $p['impressions'] > 0 ? $p['position_sum'] / $p['impressions'] : 0;
                unset($p['position_sum']);
            }
        }
    }

    if ($propertyId !== '') {
        $gaUrl = 'https://analyticsdata.googleapis.com/v1beta/properties/' . rawurlencode($propertyId) . ':runReport';
        $gaPayload = [
            'dateRanges' => [['startDate' => $days . 'daysAgo', 'endDate' => 'yesterday']],
            'dimensions' => [['name' => 'landingPagePlusQueryString']],
            'metrics' => [
                ['name' => 'sessions'],
                ['name' => 'engagedSessions'],
                ['name' => 'engagementRate'],
                ['name' => 'userEngagementDuration'],
                ['name' => 'keyEvents'],
                ['name' => 'sessionKeyEventRate']
            ],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName' => 'sessionDefaultChannelGroup',
                    'stringFilter' => ['matchType' => 'EXACT', 'value' => 'Organic Search'],
                ]
            ],
            'limit' => 500
        ];
        $gaResult = analyticsGoogleApiPost($gaUrl, $token, $gaPayload);
        if ($gaResult['ok'] && isset($gaResult['data']['rows'])) {
            foreach ($gaResult['data']['rows'] as $row) {
                $landingPage = $row['dimensionValues'][0]['value'] ?? '';
                $normPath = $normalizePath($landingPage);

                $metrics = $row['metricValues'] ?? [];
                $sessions = (float)($metrics[0]['value'] ?? 0);
                $engagedSessions = (float)($metrics[1]['value'] ?? 0);
                $userDuration = (float)($metrics[3]['value'] ?? 0);
                $keyEvents = (float)($metrics[4]['value'] ?? 0);

                if (!isset($gaData[$normPath])) {
                    $gaData[$normPath] = ['organic_sessions' => 0, 'engaged_sessions' => 0, 'user_duration' => 0, 'leads' => 0];
                }
                $gaData[$normPath]['organic_sessions'] += $sessions;
                $gaData[$normPath]['engaged_sessions'] += $engagedSessions;
                $gaData[$normPath]['user_duration'] += $userDuration;
                $gaData[$normPath]['leads'] += $keyEvents;
            }
            foreach ($gaData as &$g) {
                $g['engagement_rate'] = $g['organic_sessions'] > 0 ? $g['engaged_sessions'] / $g['organic_sessions'] : 0;
                $g['avg_engagement_time'] = $g['organic_sessions'] > 0 ? $g['user_duration'] / $g['organic_sessions'] : 0;
                $g['conversion_rate'] = $g['organic_sessions'] > 0 ? $g['leads'] / $g['organic_sessions'] : 0;
                unset($g['engaged_sessions'], $g['user_duration']);
            }
        }
    }

    $performance = [];
    foreach ($posts as $post) {
        $url = "/blog/" . $post['slug'];
        $normPath = $normalizePath($url);

        $gscData = $pages[$normPath] ?? ['clicks' => 0, 'impressions' => 0, 'ctr' => 0.0, 'position' => 0.0];
        $ga4Data = $gaData[$normPath] ?? ['organic_sessions' => 0, 'engagement_rate' => 0.0, 'leads' => 0, 'conversion_rate' => 0.0];

        $performance[] = [
            'url' => $url,
            'title' => $post['title'],
            'gsc' => $gscData,
            'ga4' => $ga4Data
        ];
    }

    $data['performance'] = $performance;
    analyticsCacheSet($cacheKey, $data, 30);

    return $data;
}
