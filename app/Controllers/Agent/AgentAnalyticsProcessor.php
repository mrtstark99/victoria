<?php
/**
 * Agent Analytics and Opportunities Processor
 */

namespace Controllers\Agent;

class AgentAnalyticsProcessor {
    public function process($action, $scopes) {
        $hasRead = in_array('admin', $scopes) || in_array('analytics:read', $scopes);
        if (!$hasRead) {
            $this->forbidden('Missing scope [analytics:read]');
        }

        $days = isset($_GET['days']) ? (int)$_GET['days'] : 28;
        $refresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';

        switch ($action) {
            case 'analytics':
                $data = analyticsDashboardData($days, $refresh);
                $this->success($data);
                break;

            case 'page_performance':
                $data = analyticsPagePerformanceData($days, $refresh);
                $this->success($data);
                break;

            case 'opportunities':
                $perfData = analyticsPagePerformanceData($days, false);
                $opportunities = [];
                
                foreach ($perfData['performance'] ?? [] as $item) {
                    $url = $item['url'];
                    $gsc = $item['gsc'] ?? [];
                    $ga4 = $item['ga4'] ?? [];
                    
                    $clicks = $gsc['clicks'] ?? 0;
                    $impressions = $gsc['impressions'] ?? 0;
                    $ctr = $gsc['ctr'] ?? 0;
                    $position = $gsc['position'] ?? 0;
                    $sessions = $ga4['organic_sessions'] ?? 0;
                    $engagement = $ga4['engagement_rate'] ?? 1.0;
                    $leads = $ga4['leads'] ?? 0;

                    // Rule 1: low_ctr (in Top 10 but low CTR)
                    if ($position > 0 && $position <= 10 && $impressions > 200 && $ctr < 0.02) {
                        $score = min(100, (int)round((1 - $ctr) * ($impressions / 200) + 20));
                        $opportunities[] = [
                            'type' => 'low_ctr', 'page' => $url, 'position' => $position, 'impressions' => $impressions, 'ctr' => $ctr,
                            'priority_score' => $score, 'recommended_action' => 'rewrite_title_description',
                            'details' => 'Trang xếp hạng tốt trong Top 10 nhưng CTR thấp. Cần viết lại thẻ H1 và Meta Description để thu hút nhấp chuột.'
                        ];
                    }

                    // Rule 2: striking_distance (vị trí 4-15)
                    if ($position >= 4 && $position <= 15 && $impressions > 100) {
                        $score = min(100, (int)round((16 - $position) * 5 + ($impressions / 400)));
                        $opportunities[] = [
                            'type' => 'striking_distance', 'page' => $url, 'position' => $position, 'impressions' => $impressions, 'ctr' => $ctr,
                            'priority_score' => $score, 'recommended_action' => 'optimize_content_and_links',
                            'details' => 'Bài viết gần Top 3. Nên tối ưu hóa Heading, thêm các câu hỏi FAQ và liên kết nội bộ.'
                        ];
                    }

                    // Rule 3: low_conversion (Sessions cao nhưng không có Leads)
                    if ($sessions > 50 && $leads === 0) {
                        $score = min(100, (int)round($sessions / 2));
                        $opportunities[] = [
                            'type' => 'low_conversion', 'page' => $url, 'sessions' => $sessions, 'leads' => $leads,
                            'priority_score' => $score, 'recommended_action' => 'improve_cta_and_forms',
                            'details' => 'Trang có lưu lượng truy cập tốt nhưng tỷ lệ chuyển đổi kém. Cần đưa nút CTA nổi bật lên đầu trang.'
                        ];
                    }
                }

                // Sort opportunities by priority_score descending
                usort($opportunities, function($a, $b) {
                    return $b['priority_score'] <=> $a['priority_score'];
                });

                $this->success($opportunities);
                break;
        }
    }

    private function success($data) {
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function error($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function forbidden($msg) {
        $this->error("Forbidden: " . $msg, 403);
    }
}
