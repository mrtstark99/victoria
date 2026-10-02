<?php
/**
 * Agent Task Scheduler — Calendar Schedule Generation & Data Reset
 * Extracted from AgentTask model for single-responsibility.
 * Handles: generateRealCalendarSchedule(), resetAllTestingData()
 */

namespace Models;

use Database;

class AgentTaskScheduler {

    /**
     * Generate Real Calendar Schedule for 6 Months & Month 1 Tasks
     * Rule:
     * - Month at start is Month 1.
     * - 6 Months schedule created at Month 1 (Monthly tasks).
     * - Weekly & Daily tasks created for Month 1 based on real dates.
     */
    public static function generateRealCalendarSchedule(?string $startMonth = null): array {
        $startStr = $startMonth ?: date('Y-m'); // e.g. "2026-08"
        $startDate = strtotime($startStr . '-01');
        if (!$startDate) {
            $startDate = strtotime(date('Y-m-01'));
        }

        $createdTasks = 0;
        $db = Database::getInstance();
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM ai_agent_tasks WHERE content = ? AND scheduled_date = ?");

        // 1. GENERATE 6 STRATEGIC MONTHLY TASKS (LỊCH 6 THÁNG TỚI)
        $monthMilestones = [
            1 => [
                'name' => 'Tháng 1 - Khung & Đổ Móng',
                'category' => 'Nền tảng & Cấu trúc',
                'priority' => 'urgent',
                'content' => 'Tháng 1: Khung & Đổ móng - Lập bản đồ từ khóa, hoàn thành 2-3 bài Pillar đầu tiên và mạng lưới Cluster vệ tinh',
                'notes' => 'Tập trung tối đa vào cấu trúc silo, hoàn thiện trang E-E-A-T, cài đặt GA4/GSC và hoàn tất hạ tầng On-page.'
            ],
            2 => [
                'name' => 'Tháng 2 - Mở Rộng Độ Phủ',
                'category' => 'Nội dung & Viết bài',
                'priority' => 'high',
                'content' => 'Tháng 2: Mở rộng độ phủ - Vận hành cỗ máy sản xuất 4-5 bài/tuần bằng AI, audit và tối ưu kỹ thuật cuối tháng',
                'notes' => 'Tăng tốc xuất bản đều đặn, đi Internal Links đa chiều giữa các bài viết mới và bài Pillar.'
            ],
            3 => [
                'name' => 'Tháng 3 - Đọc Data & Phân Phối',
                'category' => 'Phân tích & Đo lường',
                'priority' => 'high',
                'content' => 'Tháng 3: Đọc Data GSC & Phân phối - Tối ưu tiêu đề/CTR, tái chế nội dung đa kênh, viết bài Opinionated',
                'notes' => 'Phân tích các từ khóa có impression cao để tối ưu title/meta, bắt đầu thử nghiệm tái chế nội dung sang Social.'
            ],
            4 => [
                'name' => 'Tháng 4 - Nâng Cấp E-E-A-T & Lead Gen',
                'category' => 'Tối ưu On-page',
                'priority' => 'medium',
                'content' => 'Tháng 4: Nâng cấp E-E-A-T & Lead Gen - Bổ sung hình ảnh thực tế, viết bài bám theo từ khóa tự lên top, thu Lead Email',
                'notes' => 'Thêm tài liệu tải về (Lead Magnet Checklist/Ebook) để chuyển đổi độc giả thành Lead tiềm năng.'
            ],
            5 => [
                'name' => 'Tháng 5 - Hái Quả Thấp & Tỉa Cành (Pruning)',
                'category' => 'Phân tích & Đo lường',
                'priority' => 'medium',
                'content' => 'Tháng 5: Hái quả thấp & Tỉa cành (Pruning) - Tối ưu mạnh các bài trang 2 lên trang 1 Google, xóa/gộp bài không có view',
                'notes' => 'Dồn link juice cho các bài tiềm năng vị trí 11-20, loại bỏ hoặc gộp các nội dung mỏng không có traffic.'
            ],
            6 => [
                'name' => 'Tháng 6 - Tối Ưu Chuyển Đổi & Guest Post',
                'category' => 'Chuyển đổi & Off-page',
                'priority' => 'high',
                'content' => 'Tháng 6: Tối ưu chuyển đổi (CRO) & Guest Post Backlink - Đặt nút mua hàng/Affiliate, kéo Backlink Guest Post chất lượng',
                'notes' => 'Tổng kết KPI 6 tháng đầu tiên, tối ưu phễu chuyển đổi và lập chiến lược mở rộng Authority cho giai đoạn tiếp theo.'
            ]
        ];

        for ($m = 1; $m <= 6; $m++) {
            $mTime = strtotime("+".($m - 1)." month", $startDate);
            $yearMonth = date('Y-m', $mTime);
            $endOfMonth = date('Y-m-t', $mTime);
            $meta = $monthMilestones[$m];

            $checkStmt->execute([$meta['content'], $endOfMonth]);
            if ((int)$checkStmt->fetchColumn() === 0) {
                AgentTask::create([
                    'priority' => $meta['priority'],
                    'category' => $meta['category'],
                    'cycle_type' => 'monthly',
                    'session_slot' => 'full_day',
                    'scheduled_date' => $yearMonth . '-01',
                    'deadline' => $endOfMonth,
                    'month_num' => $m,
                    'phase' => "Tháng $m ($yearMonth) - " . $meta['name'],
                    'content' => $meta['content'],
                    'notes' => $meta['notes'],
                    'is_ad_hoc' => 0,
                    'created_by' => 'admin'
                ]);
                $createdTasks++;
            }
        }

        // 2. GENERATE WEEKLY TASKS FOR MONTH 1 (CÁC TASK TUẦN THÁNG 1 THEO LỊCH THỰC TẾ)
        $m1YearMonth = date('Y-m', $startDate);
        $m1DaysInMonth = (int)date('t', $startDate);

        $weeklyPlan = [
            1 => [
                'start_day' => 1,
                'end_day' => min(7, $m1DaysInMonth),
                'priority' => 'urgent',
                'category' => 'Nền tảng & Cấu trúc',
                'content' => "Tuần 1: Nghiên cứu ngách, gom nhóm từ khóa & lên Content Calendar tháng $m1YearMonth; Cài đặt GA4/GSC và xây dựng trang Giới thiệu E-E-A-T",
                'notes' => 'Xác định chân dung độc giả, Top 3 đối thủ và thiết lập cấu trúc Topic Cluster nền tảng.'
            ],
            2 => [
                'start_day' => min(8, $m1DaysInMonth),
                'end_day' => min(14, $m1DaysInMonth),
                'priority' => 'high',
                'category' => 'Nội dung & Viết bài',
                'content' => "Tuần 2: Viết 2-3 bài Pillar Posts (Trụ cột 2500+ từ) và các bài Cluster vệ tinh đầu tiên, đi Internal Link trỏ về Pillar",
                'notes' => 'Triển khai bài viết chuyên sâu có bố cục chuẩn PAS/AIDA, bảng so sánh và FAQ schema.'
            ],
            3 => [
                'start_day' => min(15, $m1DaysInMonth),
                'end_day' => min(21, $m1DaysInMonth),
                'priority' => 'high',
                'category' => 'Kỹ thuật SEO & Index',
                'content' => "Tuần 3: Submit Sitemap, ép Index toàn bộ bài viết qua GSC API; tạo hệ thống Social vệ tinh thu hút Social Signals",
                'notes' => 'Chia sẻ bài viết lên các kênh mạng xã hội, kiểm tra Core Web Vitals và mobile usability.'
            ],
            4 => [
                'start_day' => min(22, $m1DaysInMonth),
                'end_day' => $m1DaysInMonth,
                'priority' => 'medium',
                'category' => 'Phân tích & Đo lường',
                'content' => "Tuần 4: Đánh giá dữ liệu Search Console ban đầu (từ khóa có impression), chèn CTA chuyển đổi và tìm kiếm cơ hội Guest Post",
                'notes' => 'Rà soát các trang có lượt hiển thị đầu tiên, tinh chỉnh thẻ Title/Meta Description để tăng CTR.'
            ]
        ];

        foreach ($weeklyPlan as $w => $wp) {
            $wStart = sprintf('%s-%02d', $m1YearMonth, $wp['start_day']);
            $wEnd = sprintf('%s-%02d', $m1YearMonth, $wp['end_day']);

            $checkStmt->execute([$wp['content'], $wEnd]);
            if ((int)$checkStmt->fetchColumn() === 0) {
                AgentTask::create([
                    'priority' => $wp['priority'],
                    'category' => $wp['category'],
                    'cycle_type' => 'weekly',
                    'session_slot' => 'full_day',
                    'scheduled_date' => $wStart,
                    'deadline' => $wEnd,
                    'month_num' => 1,
                    'week_num' => $w,
                    'phase' => "Tuần $w ($wStart ➔ $wEnd)",
                    'content' => $wp['content'],
                    'notes' => $wp['notes'],
                    'is_ad_hoc' => 0,
                    'created_by' => 'admin'
                ]);
                $createdTasks++;
            }
        }

        // 3. GENERATE DAILY SESSIONS FOR TODAY & CURRENT WEEK (CÁC PHIÊN LÀM VIỆC NGÀY THỰC TẾ)
        $today = date('Y-m-d');
        $dailySessions = [
            'morning' => [
                'priority' => 'high',
                'category' => 'Phân tích & Đo lường',
                'content' => 'Phiên Sáng: Kiểm tra biến động traffic & cảnh báo lỗi server trên Google Analytics & Search Console',
                'notes' => 'Theo dõi các chỉ số traffic, tỷ lệ thoát, lỗi thu thập dữ liệu và từ khóa mới bắt đầu có thứ hạng.'
            ],
            'afternoon' => [
                'priority' => 'high',
                'category' => 'Nội dung & Viết bài',
                'content' => 'Phiên Chiều: Dùng AI lên dàn ý, viết nháp bài viết mới, biên tập bổ sung trải nghiệm thực tế E-E-A-T & hình ảnh',
                'notes' => 'Triển khai nội dung theo quy chuẩn hệ thống, bổ sung case study thực tế và hình ảnh chuẩn SEO.'
            ],
            'evening' => [
                'priority' => 'medium',
                'category' => 'Tối ưu On-page',
                'content' => 'Phiên Tối: Tối ưu On-page toàn diện, chèn liên kết nội bộ (Internal Link) và lên lịch xuất bản rải rác',
                'notes' => 'Kiểm tra mật độ từ khóa, thực thể LSI, Schema markup và lên lịch đăng bài rải rác đều đặn.'
            ]
        ];

        foreach ($dailySessions as $slot => $ds) {
            $contentDaily = $ds['content'] . ' (' . date('d/m/Y', strtotime($today)) . ')';
            $checkStmt->execute([$contentDaily, $today]);
            if ((int)$checkStmt->fetchColumn() === 0) {
                AgentTask::create([
                    'priority' => $ds['priority'],
                    'category' => $ds['category'],
                    'cycle_type' => 'daily',
                    'session_slot' => $slot,
                    'scheduled_date' => $today,
                    'deadline' => $today,
                    'month_num' => 1,
                    'phase' => 'Hôm nay (' . date('d/m/Y', strtotime($today)) . ')',
                    'content' => $contentDaily,
                    'notes' => $ds['notes'],
                    'is_ad_hoc' => 0,
                    'created_by' => 'admin'
                ]);
                $createdTasks++;
            }
        }

        return [
            'start_month' => $m1YearMonth,
            'created_tasks' => $createdTasks
        ];
    }

    /**
     * Completely reset all testing data across posts, tasks, seo, logs.
     * Clean slate for new testing rounds.
     */
    public static function resetAllTestingData(): array {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $db->exec("DELETE FROM post_revisions;");
            $db->exec("DELETE FROM page_views;");
            $db->exec("DELETE FROM posts;");
            $db->exec("DELETE FROM ai_agent_tasks;");
            $db->exec("DELETE FROM seo_keyword_map;");
            $db->exec("DELETE FROM seo_topic_clusters;");
            $db->exec("DELETE FROM audit_logs;");
            $db->exec("DELETE FROM analytics_cache;");
            $db->exec("DELETE FROM ai_agent_rate_limits;");
            $db->exec("DELETE FROM login_attempts;");

            try {
                $db->exec("DELETE FROM sqlite_sequence WHERE name IN ('posts','post_revisions','ai_agent_tasks','seo_keyword_map','seo_topic_clusters','audit_logs','page_views','login_attempts');");
            } catch (\Exception $e) {
                // Ignore if sqlite_sequence not found
            }

            $catCount = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
            if ($catCount === 0) {
                $db->exec("
                    INSERT INTO categories (name, slug, description) VALUES
                    ('Kiến thức SEO', 'kien-thuc-seo', 'Kiến thức và kỹ thuật tối ưu hóa công cụ tìm kiếm thực chiến.'),
                    ('AI Automation', 'ai-automation', 'Ứng dụng AI, tự động hóa quy trình và xây dựng AI Agent.'),
                    ('Case Study & Thực chiến', 'case-study', 'Phân tích các dự án thực tế và số liệu tăng trưởng.');
                ");
            }

            $db->commit();
            return [
                'success' => true,
                'message' => 'Toàn bộ dữ liệu bài viết, task, SEO và nhật ký đã được xóa sạch. Hệ thống sẵn sàng cho phiên test mới!'
            ];
        } catch (\Throwable $e) {
            $db->rollBack();
            return [
                'success' => false,
                'message' => 'Lỗi khi reset CSDL: ' . $e->getMessage()
            ];
        }
    }
}
