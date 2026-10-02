<?php
/** Editable content and section layout for the public homepage. */
namespace Models;

class HomePage {
    public const SECTION_KEYS = ['trust', 'programs', 'process', 'info_portal', 'cost_calculator', 'blog_preview', 'zoom_sessions', 'contact_form'];

    public static function defaults(): array {
        return [
            'hero' => [
                'eyebrow' => 'Chắp cánh tương lai',
                'title' => 'Du học Nhật Bản cùng',
                'highlight' => 'Victoria',
                'description' => 'Định hướng lộ trình học tập cá nhân hóa, dự toán chi phí minh bạch rõ ràng ngay từ đầu, và sự đồng hành trọn vẹn của đội ngũ chuyên gia trước và sau khi nhập cảnh.',
                'primary_cta_label' => 'Đăng ký tư vấn',
                'primary_cta_url' => '/consultation',
                'secondary_cta_label' => 'Xem quy trình',
                'secondary_cta_url' => '#programs',
                'image_url' => '/assets/images/hero-victoria.jpg',
            ],
            'sections' => self::SECTION_KEYS,
            'visible_sections' => array_fill_keys(self::SECTION_KEYS, true),
            'trust_items' => [
                ['icon' => 'bi-people-fill', 'title' => 'Tư vấn chuyên sâu 1-1', 'description' => 'Thiết lập lộ trình riêng biệt'],
                ['icon' => 'bi-cash-stack', 'title' => 'Chi phí minh bạch', 'description' => 'Dự toán cụ thể từng kỳ'],
                ['icon' => 'bi-file-earmark-check-fill', 'title' => 'Hồ sơ chất lượng', 'description' => 'Tối ưu tỷ lệ đỗ COE'],
                ['icon' => 'bi-globe-americas', 'title' => 'Đồng hành Việt - Nhật', 'description' => 'Hỗ trợ trọn vẹn sau bay'],
            ],
        ];
    }

    public static function get(): array {
        $stmt = \Database::getInstance()->prepare("SELECT setting_value FROM settings WHERE setting_key = 'homepage_config' LIMIT 1");
        $stmt->execute();
        $raw = $stmt->fetchColumn();
        $saved = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : null);
        if (!is_array($saved)) return self::defaults();
        return array_replace_recursive(self::defaults(), $saved);
    }

    public static function save(array $data): void {
        updateSetting('homepage_config', $data, 'json');
    }
}
