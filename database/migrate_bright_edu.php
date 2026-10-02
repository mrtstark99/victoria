<?php
/**
 * @file database/migrate_bright_edu.php
 * @description Migration script creating services and contacts tables with baseline seeds.
 *
 * Layer:
 * - Persistence / Database Migration
 *
 * Responsibilities:
 * - Provision `services` table schema and indexes.
 * - Provision `contacts` table schema and indexes.
 * - Seed default study abroad service packages.
 * - Ensure idempotent execution for subsequent runs.
 *
 * Security:
 * - Safe SQLite DDL execution with parameter binding for seed inserts.
 *
 * Dependencies:
 * - PDO SQLite database instance from Database::getInstance().
 *
 * Constraints:
 * - Keep this file focused on a single responsibility.
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

try {
    $db = Database::getInstance();
    echo "Starting Bright-Education-v1 schema migration...\n";

    // 1. Create services table
    $db->exec("
        CREATE TABLE IF NOT EXISTS services (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            name           TEXT,
            title          TEXT NOT NULL,
            slug           TEXT UNIQUE NOT NULL,
            description    TEXT,
            content        TEXT,
            icon           TEXT,
            price          REAL DEFAULT 0,
            packages_json  TEXT NOT NULL DEFAULT '[]',
            display_order  INTEGER NOT NULL DEFAULT 0,
            status         TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','inactive')),
            created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );
        CREATE INDEX IF NOT EXISTS idx_services_slug   ON services(slug);
        CREATE INDEX IF NOT EXISTS idx_services_status ON services(status);
        CREATE INDEX IF NOT EXISTS idx_services_order  ON services(display_order);
        CREATE TABLE IF NOT EXISTS service_slug_redirects (
            old_slug TEXT PRIMARY KEY,
            service_id INTEGER NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        );
    ");
    echo "[OK] Table 'services' checked/created.\n";

    try {
        $db->exec("ALTER TABLE services ADD COLUMN packages_json TEXT NOT NULL DEFAULT '[]'");
    } catch (\Exception $e) {
        // Column already exists.
    }

    $defaultPackages = json_encode([
        ['name' => 'Tiêu Chuẩn', 'slug' => 'standard', 'description' => 'Đầy đủ thủ tục cơ bản, giải pháp an toàn và tiết kiệm.', 'price' => 15000000, 'features' => ['Tư vấn chọn trường và ngành học', 'Dịch thuật và công chứng hồ sơ', 'Nộp hồ sơ xin tư cách lưu trú (COE)', 'Hỗ trợ xin visa tại Đại sứ quán'], 'featured' => false],
        ['name' => 'An Tâm', 'slug' => 'assisted', 'description' => 'Trọn gói từ A đến Z, đồng hành trước và sau khi nhập cảnh.', 'price' => 20000000, 'features' => ['Bao gồm quyền lợi gói Tiêu Chuẩn', 'Luyện phỏng vấn 1-1', 'Hỗ trợ tìm nhà ở, ký túc xá tại Nhật', 'Hướng dẫn sau khi đến Nhật'], 'featured' => true],
        ['name' => 'Chuyên Sâu', 'slug' => 'comprehensive', 'description' => 'Tư vấn chuyên sâu theo mục tiêu học tập và nghề nghiệp.', 'price' => 25000000, 'features' => ['Bao gồm quyền lợi gói An Tâm', 'Hướng dẫn hồ sơ săn học bổng MEXT/JASSO', 'Định hướng nghề nghiệp và phỏng vấn việc làm', 'Hỗ trợ tư vấn thủ tục visa'], 'featured' => false],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // 2. Create contacts table
    $db->exec("
        CREATE TABLE IF NOT EXISTS contacts (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            name           TEXT NOT NULL,
            email          TEXT NOT NULL,
            phone          TEXT,
            subject        TEXT,
            message        TEXT,
            intake_period  TEXT,
            japanese_level TEXT,
            status         TEXT NOT NULL DEFAULT 'new' CHECK(status IN ('new','read','replied','processing','completed','archived')),
            assigned_to    INTEGER,
            notes          TEXT,
            ip_address     TEXT,
            user_agent     TEXT,
            created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
        );
        CREATE INDEX IF NOT EXISTS idx_contacts_status  ON contacts(status);
        CREATE INDEX IF NOT EXISTS idx_contacts_created ON contacts(created_at);
    ");
    echo "[OK] Table 'contacts' checked/created.\n";

    // 3. Seed baseline services if table is empty
    $count = (int)$db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    if ($count === 0) {
        $stmt = $db->prepare("
            INSERT INTO services (name, title, slug, description, content, icon, price, display_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ");

        $defaultServices = [
            [
                'Du học Trường Nhật ngữ',
                'Chương trình Du học Trường Nhật ngữ',
                'japanese-language-school-program',
                'Khóa học tiếng Nhật tập trung từ 1.5 - 2 năm tại các thành phố lớn của Nhật Bản (Tokyo, Osaka, Fukuoka, Nagoya).',
                '<p>Chương trình phù hợp cho các bạn học sinh vừa tốt nghiệp THPT, sinh viên đại học mong muốn nâng cao năng lực tiếng Nhật đạt chuẩn N2 - N1 để chuyển tiếp lên chuyên ngành hoặc đi làm tại Nhật Bản.</p>',
                'bi-translate',
                15000000,
                1
            ],
            [
                'Du học Trường Chuyên môn (Senmon)',
                'Chương trình Du học Trường Chuyên môn (Senmon)',
                'vocational-school-program',
                'Đào tạo nghề thực hành 2-3 năm với các chuyên ngành CNTT, Điều dưỡng, Cơ khí ô tô, Du lịch khách sạn.',
                '<p>Cấp bằng Chuyên môn gia (Senmonshi) có giá trị quốc tế, hỗ trợ 100% giới thiệu việc làm chính thức tại các tập đoàn Nhật Bản ngay sau khi tốt nghiệp.</p>',
                'bi-briefcase',
                20000000,
                2
            ],
            [
                'Du học Kỹ năng đặc định (SSW)',
                'Chương trình Du học Kỹ năng đặc định (SSW)',
                'specified-skilled-worker-program',
                'Chuyển đổi visa kỹ năng đặc định diện 1 và 2, làm việc dài hạn với mức thu nhập tương đương người bản xứ.',
                '<p>Dành cho học viên đã có chứng chỉ tiếng Nhật N4 và đỗ kỳ thi kỹ năng tay nghề tương ứng theo quy định của Cục Quản lý Xuất nhập cảnh Nhật Bản.</p>',
                'bi-tools',
                18000000,
                3
            ],
            [
                'Du học Đại học & Cao học Nhật Bản',
                'Chương trình Du học Đại học & Cao học Nhật Bản',
                'university-and-graduate-program',
                'Luyện thi EJU, săn học bổng MEXT, JASSO và ứng tuyển trực tiếp vào các trường đại học quốc lập và tư thục hàng đầu.',
                '<p>Hỗ trợ chuẩn bị đề cương nghiên cứu, kết nối giáo sư hướng dẫn, phỏng vấn và hoàn thiện hồ sơ học bổng toàn phần/bán phần.</p>',
                'bi-mortarboard',
                25000000,
                4
            ]
        ];

        foreach ($defaultServices as $svc) {
            $stmt->execute($svc);
        }
        echo "[OK] Seeded 4 default study abroad services.\n";
    } else {
        echo "[INFO] Table 'services' already has $count records, skipping seed.\n";
    }

    $seedMarker = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'service_packages_seeded' LIMIT 1");
    $seedMarker->execute();
    if ($seedMarker->fetchColumn() !== '1') {
        $fillPackages = $db->prepare("UPDATE services SET packages_json = ? WHERE packages_json IS NULL OR packages_json = '' OR packages_json = '[]'");
        $fillPackages->execute([$defaultPackages]);
        $saveMarker = $db->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value, setting_type, description) VALUES ('service_packages_seeded', '1', 'text', 'Default service packages seeded')");
        $saveMarker->execute();
    }
    echo "[OK] Program dossier packages are available.\n";

    $legacyProgramSlugs = [
        'du-hoc-truong-nhat-ngu' => 'japanese-language-school-program',
        'du-hoc-truong-chuyen-mon-senmon' => 'vocational-school-program',
        'du-hoc-ky-nang-dac-dinh-ssw' => 'specified-skilled-worker-program',
        'du-hoc-dai-hoc-va-cao-hoc' => 'university-and-graduate-program',
    ];
    $redirect = $db->prepare('INSERT OR REPLACE INTO service_slug_redirects (old_slug, service_id) VALUES (?, ?)');
    $rename = $db->prepare('UPDATE services SET slug = ?, updated_at = datetime(\'now\', \'localtime\') WHERE id = ?');
    foreach ($legacyProgramSlugs as $oldSlug => $englishSlug) {
        $find = $db->prepare('SELECT id FROM services WHERE slug = ? LIMIT 1');
        $find->execute([$oldSlug]);
        $serviceId = $find->fetchColumn();
        if ($serviceId) {
            $collision = $db->prepare('SELECT id FROM services WHERE slug = ? AND id <> ? LIMIT 1');
            $collision->execute([$englishSlug, (int)$serviceId]);
            if ($collision->fetchColumn()) $englishSlug .= '-' . (int)$serviceId;
            $redirect->execute([$oldSlug, (int)$serviceId]);
            $rename->execute([$englishSlug, (int)$serviceId]);
        }
    }

    $packageSlugMap = ['tieu-chuan' => 'standard', 'an-tam' => 'assisted', 'chuyen-sau' => 'comprehensive'];
    $servicePackages = $db->query('SELECT id, packages_json FROM services WHERE packages_json IS NOT NULL AND packages_json <> \'\'');
    $savePackages = $db->prepare('UPDATE services SET packages_json = ? WHERE id = ?');
    foreach ($servicePackages->fetchAll(PDO::FETCH_ASSOC) as $serviceRow) {
        $packages = json_decode((string)$serviceRow['packages_json'], true);
        if (!is_array($packages)) continue;
        $changed = false;
        foreach ($packages as $index => &$package) {
            if (!is_array($package)) continue;
            $currentSlug = (string)($package['slug'] ?? '');
            if (isset($packageSlugMap[$currentSlug])) {
                $package['slug'] = $packageSlugMap[$currentSlug];
                $changed = true;
            } elseif ($currentSlug === '' || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $currentSlug)) {
                $package['slug'] = 'package-' . ((int)$index + 1);
                $changed = true;
            }
        }
        unset($package);
        if ($changed) $savePackages->execute([json_encode($packages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), (int)$serviceRow['id']]);
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "[ERROR] Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
