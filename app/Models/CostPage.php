<?php
/** Structured content for the public study abroad cost page. */
namespace Models;

use Database;
use PDO;

class CostPage {
    public static function defaults(): array {
        return [
            'title' => 'Chi Phí Du Học Nhật Bản',
            'intro' => 'Kế hoạch tài chính chi tiết, rõ ràng và tối ưu nhất cho hành trình du học của bạn. Bright Education cam kết không phát sinh bất kỳ khoản phí ngoài hợp đồng nào.',
            'exchange_rate' => 175,
            'sections' => [
                ['title' => 'Phí Dịch Vụ Hồ Sơ Tại Việt Nam', 'description' => 'Mức phí dịch vụ trọn gói xử lý toàn bộ hồ sơ tại Việt Nam của Bright Education.', 'columns' => ['Danh mục chi phí', 'Mô tả chi tiết', 'Giá thị trường (VNĐ)', 'Bright Education (VNĐ)'], 'rows' => [
                    ['Xử lý hồ sơ & Dịch thuật', 'Kiểm tra hồ sơ gốc, dịch thuật công chứng, hoàn thiện hồ sơ gửi trường', '~10.000.000đ - 15.000.000đ', '15.000.000đ / 30.000.000đ'],
                    ['Phí chứng thực bằng cấp', 'Xác thực văn bằng tốt nghiệp', '~1.500.000đ', 'Miễn phí'],
                    ['Chi phí hỗ trợ du học sinh', 'Tư vấn, kết nối gia đình và nhà trường', '~5.000.000đ - 10.000.000đ', 'Miễn phí'],
                    ['Chứng minh tài chính', 'Hoàn thiện hồ sơ bảo lãnh tài chính', '~6.000.000đ', 'Miễn phí'],
                    ['Phí xử lý hồ sơ COE', 'Dịch thuật, công chứng và làm hồ sơ xin COE', '~5.000.000đ - 10.000.000đ', 'Miễn phí'],
                    ['Phí chuyển phát hồ sơ', 'Chuyển phát hồ sơ gốc sang Nhật', '~1.000.000đ', 'Miễn phí'],
                    ['Chi phí xin visa', 'Hoàn thiện tờ khai và nộp visa', '~1.500.000đ', 'Miễn phí'],
                    ['TỔNG PHÍ DỊCH VỤ', 'Chưa bao gồm vé máy bay', '~40.000.000đ - 60.000.000đ', '15.000.000đ / 30.000.000đ']
                ]],
                ['title' => 'Học Phí Năm Đầu Tại Nhật', 'description' => 'Học phí năm đầu tiên đóng trực tiếp cho trường tại Nhật Bản sau khi được cấp COE.', 'columns' => ['Khu vực / Đặc điểm trường', 'Học phí trung bình (JPY / Năm)', 'Chi phí quy đổi (VNĐ)'], 'rows' => [
                    ['Trường ở tỉnh xa', '600.000 - 700.000 JPY', '~105.000.000đ - 122.500.000đ'],
                    ['Thành phố cỡ trung', '700.000 - 750.000 JPY', '~122.500.000đ - 131.250.000đ'],
                    ['Ngoại ô Tokyo / Osaka', '750.000 - 800.000 JPY', '~131.250.000đ - 140.000.000đ'],
                    ['Trung tâm Tokyo / Osaka', '> 800.000 JPY', '> 140.000.000đ']
                ]],
                ['title' => 'Chi Phí Ký Túc Xá (3 Tháng đầu)', 'description' => 'Các trường Nhật thường yêu cầu đóng trước 3 tháng ký túc xá.', 'columns' => ['Khu vực', 'Phí KTX trung bình (JPY / Tháng)', 'Chi phí 3 tháng quy đổi (VNĐ)'], 'rows' => [
                    ['Osaka / Fukuoka & Các tỉnh khác', '30.000 - 45.000 JPY', '~15.750.000đ - 23.625.000đ'],
                    ['Tokyo (Trung tâm)', '45.000 - 60.000 JPY', '~23.625.000đ - 31.500.000đ']
                ]],
                ['title' => 'Vé Máy Bay & Thủ Tục Bay', 'description' => 'Hỗ trợ đặt vé máy bay một chiều sang Nhật và làm thủ tục xuất nhập cảnh.', 'columns' => ['Hạng mục hỗ trợ', 'Nội dung', 'Chi phí trung bình (VNĐ)'], 'rows' => [
                    ['Vé Máy Bay', 'Vé một chiều sang Nhật', '~10.000.000đ'],
                    ['Khám lao phổi', 'Khám tại bệnh viện được chỉ định', '~1.500.000đ'],
                    ['Đăng ký thi chứng chỉ tiếng Nhật', 'JLPT, NAT-TEST hoặc TOPJ', '~800.000đ'],
                    ['Học tiếng Nhật tại Việt Nam', 'Khóa học đến trình độ N4', '~12.000.000đ'],
                    ['Đón sân bay & Hướng dẫn nhập học', 'Hỗ trợ đón tại sân bay Nhật Bản', 'Miễn phí']
                ]],
                ['title' => 'Sinh Hoạt Phí Hàng Tháng (Tham Khảo)', 'description' => 'Mức sinh hoạt phí dự kiến hàng tháng của du học sinh tại Nhật Bản.', 'columns' => ['Khoản chi tiêu hàng tháng', 'Chi phí trung bình (JPY)', 'Quy đổi VNĐ'], 'rows' => [
                    ['Tiền ăn uống (Tự nấu ăn)', '25.000 - 30.000 JPY', '~4.375.000đ - 5.250.000đ'],
                    ['Tiền Ký túc xá / Thuê phòng', '30.000 - 45.000 JPY', '~5.250.000đ - 7.875.000đ'],
                    ['Tiền điện, nước, ga, internet', '10.000 JPY', '~1.750.000đ'],
                    ['Bảo hiểm quốc dân & Chi phí khác', '10.000 JPY', '~1.750.000đ'],
                    ['TỔNG CHI TIÊU HÀNG THÁNG', '75.000 - 95.000 JPY', '~13.125.000đ - 16.625.000đ']
                ]]
            ]
        ];
    }

    public static function get(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT setting_value, updated_at FROM settings WHERE setting_key = 'cost_page_config' LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $value = $row ? json_decode($row['setting_value'], true) : null;
        $result = is_array($value) ? $value : self::defaults();
        $result['updated_at'] = $row['updated_at'] ?? null;
        return $result;
    }

    public static function save(array $data): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES ('cost_page_config', ?, 'json', 'Study abroad cost page') ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value, updated_at = datetime('now','localtime')");
        $stmt->execute([json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]);
    }
}
