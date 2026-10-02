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
                'highlight' => 'Bright Education',
                'description' => 'Quy trình linh động và minh bạch sẽ giúp các bước chuẩn bị du học của bạn thuận lợi hơn khi đồng hành cùng Bright Education.',
                'primary_cta_label' => 'Đặt lịch tư vấn miễn phí',
                'primary_cta_url' => '/consultation',
                'secondary_cta_label' => 'Xem quy trình',
                'secondary_cta_url' => '/services',
                'image_url' => '/assets/images/hero-new.webp',
            ],
            'sections' => self::SECTION_KEYS,
            'visible_sections' => array_fill_keys(self::SECTION_KEYS, true),
            'trust_items' => [
                ['icon' => 'bi-person-check', 'title' => 'Tư vấn 1–1', 'description' => 'Lộ trình theo từng hồ sơ'],
                ['icon' => 'bi-receipt', 'title' => 'Chi phí minh bạch', 'description' => 'Dự toán rõ ngay từ đầu'],
                ['icon' => 'bi-file-earmark-check', 'title' => 'Hồ sơ trọn gói', 'description' => 'Theo sát từng cột mốc'],
                ['icon' => 'bi-globe2', 'title' => 'Hỗ trợ Việt – Nhật', 'description' => 'Đồng hành trước và sau nhập cảnh'],
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
