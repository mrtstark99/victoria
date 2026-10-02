<?php
/**
 * Google Analytics 4 + Search Console reporting helpers.
 * Depends on analytics_crypto.php for credential management & OAuth.
 */

require_once __DIR__ . '/analytics_crypto.php';

function analyticsGoogleApiPost(string $url, string $token, array $payload): array
{
    $response = analyticsHttpRequest(
        'POST',
        $url,
        [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        json_encode($payload, JSON_UNESCAPED_SLASHES)
    );
    $decoded = json_decode($response['body'], true);
    if (!$response['ok']) {
        $message = $decoded['error']['message'] ?? $response['error'] ?? ('Google API HTTP ' . $response['status']);
        return ['ok' => false, 'error' => $message, 'data' => []];
    }

    return ['ok' => true, 'data' => is_array($decoded) ? $decoded : []];
}

function analyticsMetricMap(array $report, int $rowIndex = 0): array
{
    $headers = array_column($report['metricHeaders'] ?? [], 'name');
    $values = $report['rows'][$rowIndex]['metricValues'] ?? [];
    $mapped = [];
    foreach ($headers as $index => $name) {
        $mapped[$name] = (float)($values[$index]['value'] ?? 0);
    }
    return $mapped;
}

function analyticsFetchGa4(string $propertyId, string $token, int $days): array
{
    $baseUrl = 'https://analyticsdata.googleapis.com/v1beta/properties/' . rawurlencode($propertyId) . ':runReport';
    $dateRanges = [['startDate' => $days . 'daysAgo', 'endDate' => 'yesterday']];
    $summary = analyticsGoogleApiPost($baseUrl, $token, [
        'dateRanges' => $dateRanges,
        'dimensions' => [['name' => 'sessionDefaultChannelGroup']],
        'metrics' => array_map(static fn($name) => ['name' => $name], [
            'sessions',
            'engagedSessions',
            'engagementRate',
            'userEngagementDuration',
            'keyEvents',
            'sessionKeyEventRate',
        ]),
        'dimensionFilter' => [
            'filter' => [
                'fieldName' => 'sessionDefaultChannelGroup',
                'stringFilter' => ['matchType' => 'EXACT', 'value' => 'Organic Search'],
            ],
        ],
        'limit' => 1,
    ]);
    if (!$summary['ok']) {
        return $summary;
    }

    $events = analyticsGoogleApiPost($baseUrl, $token, [
        'dateRanges' => $dateRanges,
        'dimensions' => [['name' => 'eventName']],
        'metrics' => [['name' => 'eventCount']],
        'dimensionFilter' => [
            'andGroup' => [
                'expressions' => [
                    [
                        'filter' => [
                            'fieldName' => 'eventName',
                            'inListFilter' => [
                                'values' => ['click_to_call', 'copy_email', 'scroll_depth', 'lead_form_start', 'form_submit_attempt', 'generate_lead'],
                            ],
                        ],
                    ],
                    [
                        'filter' => [
                            'fieldName' => 'sessionDefaultChannelGroup',
                            'stringFilter' => ['matchType' => 'EXACT', 'value' => 'Organic Search'],
                        ],
                    ],
                ],
            ],
        ],
        'orderBys' => [['metric' => ['metricName' => 'eventCount'], 'desc' => true]],
    ]);

    $eventCounts = [];
    if ($events['ok']) {
        foreach ($events['data']['rows'] ?? [] as $row) {
            $eventCounts[$row['dimensionValues'][0]['value'] ?? ''] = (int)($row['metricValues'][0]['value'] ?? 0);
        }
    }

    $summaryMetrics = analyticsMetricMap($summary['data']);
    $summaryMetrics['averageEngagementTimePerSession'] = ($summaryMetrics['sessions'] ?? 0) > 0
        ? ($summaryMetrics['userEngagementDuration'] ?? 0) / $summaryMetrics['sessions']
        : 0;

    return [
        'ok' => true,
        'summary' => $summaryMetrics,
        'events' => $eventCounts,
        'event_error' => $events['ok'] ? '' : $events['error'],
    ];
}

function analyticsFetchSearchConsole(string $siteUrl, string $token, int $days): array
{
    $endDate = date('Y-m-d', strtotime('-1 day'));
    $startDate = date('Y-m-d', strtotime('-' . $days . ' days'));
    $url = 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($siteUrl) . '/searchAnalytics/query';
    $basePayload = [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'type' => 'web',
        'dataState' => 'final',
    ];

    $summary = analyticsGoogleApiPost($url, $token, $basePayload + ['rowLimit' => 1]);
    if (!$summary['ok']) {
        return $summary;
    }

    $keywords = analyticsGoogleApiPost($url, $token, $basePayload + [
        'dimensions' => ['query'],
        'rowLimit' => 20,
    ]);

    $summaryRow = $summary['data']['rows'][0] ?? [];
    $keywordRows = [];
    if ($keywords['ok']) {
        foreach ($keywords['data']['rows'] ?? [] as $row) {
            $keywordRows[] = [
                'keyword' => $row['keys'][0] ?? '',
                'clicks' => (int)round($row['clicks'] ?? 0),
                'impressions' => (int)round($row['impressions'] ?? 0),
                'ctr' => (float)($row['ctr'] ?? 0),
                'position' => (float)($row['position'] ?? 0),
            ];
        }
    }

    return [
        'ok' => true,
        'summary' => [
            'clicks' => (int)round($summaryRow['clicks'] ?? 0),
            'impressions' => (int)round($summaryRow['impressions'] ?? 0),
            'ctr' => (float)($summaryRow['ctr'] ?? 0),
            'position' => (float)($summaryRow['position'] ?? 0),
        ],
        'keywords' => $keywordRows,
        'keyword_error' => $keywords['ok'] ? '' : $keywords['error'],
    ];
}

function analyticsCacheGet(string $key): ?array
{
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT payload, updated_at FROM analytics_cache WHERE cache_key = ? AND expires_at > datetime('now','localtime')");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $payload = json_decode($row['payload'], true);
    if (!is_array($payload)) {
        return null;
    }
    $payload['cache_updated_at'] = $row['updated_at'];
    return $payload;
}

function analyticsCacheSet(string $key, array $payload, int $minutes = 30): void
{
    $db = Database::getInstance();
    $stmt = $db->prepare("INSERT OR REPLACE INTO analytics_cache (cache_key, payload, expires_at, updated_at) VALUES (?, ?, datetime('now','localtime', ?), datetime('now','localtime'))");
    $stmt->execute([$key, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '+' . $minutes . ' minutes']);
}

function analyticsClearCache(): void
{
    Database::getInstance()->exec('DELETE FROM analytics_cache');
}
