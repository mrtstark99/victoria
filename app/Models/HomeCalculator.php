<?php
/** Prices used by the home page study abroad estimator. */
namespace Models;

class HomeCalculator {
    public static function defaults(): array {
        return [
            'package_prices' => [
                'Trường Nhật Ngữ' => 15000000,
                'Trường Senmon' => 30000000,
                'Trường Đại Học' => 30000000,
                'Chương Trình Học Bổng' => 15000000,
                'Hệ Đại Học Tiếng Anh' => 30000000
            ],
            'course_prices' => [0, 10000000, 15000000],
            'school_prices' => [110000000, 125000000, 135000000, 145000000],
            'living_prices' => [30000000, 45000000, 60000000],
            'other_prices' => [8650000, 13000000, 17000000]
        ];
    }
    public static function get(): array {
        $stmt = \Database::getInstance()->prepare("SELECT setting_value FROM settings WHERE setting_key = 'home_calculator_config' LIMIT 1");
        $stmt->execute();
        $raw = $stmt->fetchColumn();
        $saved = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : null);
        return is_array($saved) ? $saved : self::defaults();
    }
    public static function save(array $data): void {
        updateSetting('home_calculator_config', $data, 'json');
    }
}
