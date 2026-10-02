<?php
/**
 * SQLite Database Connection & Initializer
 */

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dbPath = DB_PATH;
        $dbDir = dirname($dbPath);

        // Ensure database directory exists
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }

        $dbExists = file_exists($dbPath);

        try {
            $this->pdo = new PDO("sqlite:" . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec("PRAGMA foreign_keys = ON;");

            // Check and add avatar column to users table if missing
            try {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN avatar TEXT;");
            } catch (\Exception $e) {
                // Column already exists
            }
            try {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT;");
            } catch (\Exception $e) {
                // Column already exists
            }

            // Ensure ai_agent_tasks has new calendar columns
            $colsToAdd = [
                "ALTER TABLE ai_agent_tasks ADD COLUMN scheduled_date TEXT;",
                "ALTER TABLE ai_agent_tasks ADD COLUMN month_num INTEGER DEFAULT 1;",
                "ALTER TABLE ai_agent_tasks ADD COLUMN week_num INTEGER;",
                "ALTER TABLE ai_agent_tasks ADD COLUMN is_ad_hoc INTEGER DEFAULT 0;",
                "ALTER TABLE ai_agent_tasks ADD COLUMN parent_id INTEGER;"
            ];
            foreach ($colsToAdd as $sql) {
                try {
                    $this->pdo->exec($sql);
                } catch (\Exception $e) {
                    // Column already exists
                }
            }

            if (!$dbExists || filesize($dbPath) === 0) {
                $this->initializeDatabase();
            } else {
                $this->pdo->exec("
                    CREATE TABLE IF NOT EXISTS pages (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title TEXT NOT NULL,
                        slug TEXT NOT NULL UNIQUE,
                        excerpt TEXT,
                        content TEXT,
                        template TEXT NOT NULL DEFAULT 'default',
                        featured_image TEXT,
                        author_id INTEGER NOT NULL,
                        status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft','published','archived','ai_draft','pending_review','approved')),
                        views INTEGER NOT NULL DEFAULT 0,
                        sort_order INTEGER NOT NULL DEFAULT 0,
                        meta_title TEXT,
                        meta_description TEXT,
                        meta_keywords TEXT,
                        custom_schema_json TEXT,
                        published_at TEXT,
                        created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                        updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
                    );
                    CREATE TABLE IF NOT EXISTS page_revisions (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        page_id INTEGER NOT NULL,
                        title TEXT NOT NULL,
                        slug TEXT NOT NULL,
                        excerpt TEXT,
                        content TEXT,
                        meta_title TEXT,
                        meta_description TEXT,
                        meta_keywords TEXT,
                        author_id INTEGER,
                        action TEXT,
                        changed_by TEXT,
                        created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                        FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
                    );
                    CREATE TABLE IF NOT EXISTS page_views (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        post_id INTEGER,
                        url TEXT NOT NULL,
                        ip_address TEXT,
                        user_agent TEXT,
                        referer TEXT,
                        created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL
                    );
                    CREATE TABLE IF NOT EXISTS ai_agent_tasks (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        priority TEXT NOT NULL DEFAULT 'medium' CHECK(priority IN ('urgent','high','medium','low')),
                        category TEXT NOT NULL DEFAULT 'Khác',
                        cycle_type TEXT NOT NULL DEFAULT 'daily',
                        session_slot TEXT NOT NULL DEFAULT 'morning',
                        phase TEXT,
                        content TEXT NOT NULL,
                        deadline TEXT,
                        is_completed INTEGER NOT NULL DEFAULT 0,
                        notes TEXT,
                        created_by TEXT NOT NULL DEFAULT 'agent' CHECK(created_by IN ('agent','admin')),
                        completed_at TEXT,
                        created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                        updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
                    );
                    CREATE TABLE IF NOT EXISTS login_attempts (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        ip_address TEXT NOT NULL,
                        username TEXT,
                        attempted_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
                    );
                ");

                $defaultNav = json_encode([
                    ['label' => 'Trang chủ', 'url' => '/', 'target' => '_self', 'is_active' => true],
                    ['label' => 'Tối ưu SEO', 'url' => '/category/toi-uu-seo', 'target' => '_self', 'is_active' => true],
                    ['label' => 'Hướng dẫn AI', 'url' => '/category/huong-dan', 'target' => '_self', 'is_active' => true],
                    ['label' => 'Tin tức', 'url' => '/category/tin-tuc', 'target' => '_self', 'is_active' => true],
                    ['label' => 'Giới thiệu', 'url' => '/page/gioi-thieu', 'target' => '_self', 'is_active' => true],
                    ['label' => 'Liên hệ', 'url' => '/page/lien-he', 'target' => '_self', 'is_active' => true],
                ], JSON_UNESCAPED_UNICODE);

                $defaultFooterCol2 = json_encode([
                    'title' => 'Chuyên Mục',
                    'links' => [
                        ['label' => 'Tối ưu SEO On-page', 'url' => '/category/toi-uu-seo'],
                        ['label' => 'Hướng dẫn AI & MCP', 'url' => '/category/huong-dan'],
                        ['label' => 'Tin tức Google Search', 'url' => '/category/tin-tuc'],
                        ['label' => 'Bài viết mới cập nhật', 'url' => '/#latest']
                    ]
                ], JSON_UNESCAPED_UNICODE);

                $defaultFooterCol3 = json_encode([
                    'title' => 'Thông Tin & Chính Sách',
                    'links' => [
                        ['label' => 'Về chúng tôi', 'url' => '/page/gioi-thieu'],
                        ['label' => 'Chính sách bảo mật', 'url' => '/page/chinh-sach-bao-mat'],
                        ['label' => 'Điều khoản sử dụng', 'url' => '/page/dieu-khoan-su-dung'],
                        ['label' => 'Liên hệ hợp tác', 'url' => '/page/lien-he']
                    ]
                ], JSON_UNESCAPED_UNICODE);

                $defaultBottomLinks = json_encode([
                    ['label' => 'Trang chủ', 'url' => '/'],
                    ['label' => 'Giới thiệu', 'url' => '/page/gioi-thieu'],
                    ['label' => 'Chính sách', 'url' => '/page/chinh-sach-bao-mat'],
                    ['label' => 'Sitemap', 'url' => '/sitemap.xml']
                ], JSON_UNESCAPED_UNICODE);

                $this->pdo->exec("
                    INSERT OR IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES
                    ('nav_menu_items', '{$defaultNav}', 'json', 'Header Navigation Menu Items'),
                    ('footer_col2_json', '{$defaultFooterCol2}', 'json', 'Footer Column 2 Data'),
                    ('footer_col3_json', '{$defaultFooterCol3}', 'json', 'Footer Column 3 Data'),
                    ('footer_bottom_links', '{$defaultBottomLinks}', 'json', 'Footer Bottom Copyright Links'),
                    ('ga_id', '', 'text', 'GA4 Measurement ID'),
                    ('ga_property_id', '', 'text', 'GA4 Property ID'),
                    ('gsc_site_url', '', 'text', 'Search Console Site URL'),
                    ('gsc_verification', '', 'text', 'Search Console HTML Verification Tag'),
                    ('google_service_account_enc', '', 'text', 'Google Service Account Encrypted JSON Credentials'),
                    ('seo_monthly_cost', '0', 'number', 'Chi phí SEO hàng tháng (VND)'),
                    ('organic_lead_value', '0', 'number', 'Giá trị ước tính trên mỗi Lead (VND)'),
                    ('kpi_organic_sessions_target', '0', 'number', 'KPI Traffic truy cập tự nhiên'),
                    ('kpi_impressions_target', '0', 'number', 'KPI Lượt hiển thị tìm kiếm tự nhiên'),
                    ('kpi_position_target', '0', 'number', 'KPI Vị trí từ khóa trung bình (Top)'),
                    ('kpi_ctr_target', '0.0', 'number', 'KPI Tỷ lệ Click CTR (%)'),
                    ('kpi_engagement_rate_target', '0.0', 'number', 'KPI Tỷ lệ tương tác (%)'),
                    ('kpi_avg_engagement_time_target', '0', 'number', 'KPI Thời gian tương tác trung bình (giây)'),
                    ('kpi_conversion_rate_target', '0.0', 'number', 'KPI Tỷ lệ chuyển đổi (%)'),
                    ('kpi_roi_target', '0.0', 'number', 'KPI Tỷ lệ hoàn vốn đầu tư ROI (%)')
                ");

                // Seed sample pages if table is empty
                $pageCount = (int)$this->pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
                if ($pageCount === 0) {
                    $stmtPage = $this->pdo->prepare("
                        INSERT INTO pages (title, slug, excerpt, content, template, author_id, status, meta_title, meta_description, published_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now','localtime'))
                    ");
                    $stmtPage->execute([
                        'Giới thiệu',
                        'gioi-thieu',
                        'Tìm hiểu về sứ mệnh, tầm nhìn và đội ngũ phát triển đằng sau hệ thống MinimaList SEO & AI.',
                        '<h2>Chào mừng bạn đến với MinimaList</h2><p>Chúng tôi là nền tảng chia sẻ kiến thức chuyên sâu về <strong>Tối ưu hóa Công cụ Tìm kiếm (SEO)</strong> và ứng dụng <strong>Trí tuệ nhân tạo (AI Agents)</strong> trong việc tự động hóa, tăng trưởng nội dung chất lượng cao.</p><h3>Sứ mệnh của chúng tôi</h3><p>Giúp các cá nhân, doanh nghiệp và nhà phát triển tiếp cận các chiến lược SEO On-page hiện đại, cấu trúc Topic Cluster chuẩn Semantic Search và tích hợp các công cụ AI hỗ trợ sáng tạo nội dung bền vững.</p><h3>Giá trị cốt lõi</h3><ul><li><strong>Chất lượng hàng đầu (E-E-A-T):</strong> Mọi bài viết đều được nghiên cứu, phân tích dữ liệu kỹ lưỡng.</li><li><strong>Tự động hóa thông minh:</strong> Tối ưu quy trình sáng tạo với AI Agents và MCP Servers.</li><li><strong>Thân thiện & Tối giản:</strong> Trải nghiệm đọc trực quan, hiện đại và tốc độ cao.</li></ul>',
                        'default',
                        1,
                        'published',
                        'Giới thiệu về chúng tôi - ' . SITE_NAME,
                        'Tìm hiểu về sứ mệnh, tầm nhìn và đội ngũ phát triển đằng sau nền tảng MinimaList.'
                    ]);
                    $stmtPage->execute([
                        'Liên hệ',
                        'lien-he',
                        'Kết nối với đội ngũ phát triển và chuyên gia SEO & AI để hợp tác, tư vấn và đóng góp ý kiến.',
                        '<h2>Liên hệ với chúng tôi</h2><p>Nếu bạn có bất kỳ câu hỏi, đề xuất hợp tác hoặc cần tư vấn về giải pháp SEO & AI, xin vui lòng gửi thông tin cho chúng tôi:</p><ul><li><strong>Email:</strong> contact@example.com</li><li><strong>Hotline:</strong> +84 123 456 789</li><li><strong>Địa chỉ:</strong> Hà Nội, Việt Nam</li></ul><p>Chúng tôi sẽ phản hồi bạn trong vòng 24 giờ làm việc.</p>',
                        'contact',
                        1,
                        'published',
                        'Liên hệ với ban quản trị - ' . SITE_NAME,
                        'Kết nối và gửi thông tin liên hệ, phản hồi tới ban quản trị.'
                    ]);
                    $stmtPage->execute([
                        'Chính sách bảo mật',
                        'chinh-sach-bao-mat',
                        'Cam kết bảo vệ quyền riêng tư và dữ liệu cá nhân của người dùng khi truy cập website.',
                        '<h2>Chính sách bảo mật thông tin</h2><p>Chúng tôi cam kết tôn trọng và bảo vệ quyền riêng tư của khách truy cập. Chính sách này giải thích cách chúng tôi thu thập, sử dụng và bảo vệ dữ liệu của bạn khi sử dụng trang web.</p><h3>1. Thu thập thông tin</h3><p>Chúng tôi chỉ thu thập các thông tin ẩn danh như lượt truy cập, thiết bị và thời gian truy cập nhằm mục đích cải thiện chất lượng nội dung và trải nghiệm người dùng.</p><h3>2. Bảo mật dữ liệu</h3><p>Mọi dữ liệu đều được lưu trữ an toàn và tuân thủ các tiêu chuẩn bảo mật hiện đại.</p>',
                        'default',
                        1,
                        'published',
                        'Chính sách bảo mật thông tin - ' . SITE_NAME,
                        'Quy định và cam kết về bảo mật thông tin cá nhân trên website.'
                    ]);
                }

            }

            $serviceColumns = $this->pdo->query('PRAGMA table_info(services)')->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array('packages_json', $serviceColumns, true)) {
                $this->pdo->exec("ALTER TABLE services ADD COLUMN packages_json TEXT NOT NULL DEFAULT '[]'");
            }
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS service_slug_redirects (old_slug TEXT PRIMARY KEY, service_id INTEGER NOT NULL, created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')), FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE)");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->getConnection();
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function initializeDatabase() {
        $schemaFile = APP_ROOT . '/database/schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            $this->pdo->exec($sql);
            $this->seedDatabase();
        }
    }

    private function seedDatabase() {
        // Seed default admin user
        $initPassword = getenv('ADMIN_INITIAL_PASSWORD') ?: ($_ENV['ADMIN_INITIAL_PASSWORD'] ?? 'Admin@' . bin2hex(random_bytes(4)));
        // Save initial password to local credentials file if generated
        $credFile = APP_ROOT . '/database/.initial_credentials';
        if (!file_exists($credFile)) {
            @file_put_contents($credFile, "admin / {$initPassword}\nCreated: " . date('Y-m-d H:i:s'));
        }
        @chmod($credFile, 0600);
        $passwordHash = password_hash($initPassword, PASSWORD_DEFAULT);
        $stmtUser = $this->pdo->prepare("
            INSERT OR IGNORE INTO users (username, email, password, full_name, role, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmtUser->execute(['admin', 'admin@example.com', $passwordHash, 'Administrator', 'admin', 'active']);

        // Seed default categories
        $categories = [
            ['Tin tức', 'tin-tuc', 'Cập nhật tin tức mới nhất'],
            ['Hướng dẫn', 'huong-dan', 'Các bài viết hướng dẫn chi tiết'],
            ['Tối ưu SEO', 'toi-uu-seo', 'Thủ thuật tối ưu hóa công cụ tìm kiếm']
        ];
        $stmtCat = $this->pdo->prepare("INSERT OR IGNORE INTO categories (name, slug, description) VALUES (?, ?, ?)");
        foreach ($categories as $cat) {
            $stmtCat->execute($cat);
        }

        // Seed default settings
        $settings = [
            ['site_name', SITE_NAME, 'text', 'Tên website'],
            ['site_slogan', SITE_SLOGAN, 'text', 'Slogan website'],
            ['site_email', SITE_EMAIL, 'text', 'Email liên hệ'],
            ['site_phone', SITE_PHONE, 'text', 'Số điện thoại'],
            ['posts_per_page', '6', 'number', 'Số bài viết mỗi trang'],
            ['ga_id', '', 'text', 'GA4 Measurement ID'],
            ['ga_property_id', '', 'text', 'GA4 Property ID'],
            ['gsc_site_url', '', 'text', 'Search Console Site URL'],
            ['gsc_verification', '', 'text', 'Search Console HTML Verification Tag'],
            ['google_service_account_enc', '', 'text', 'Google Service Account Encrypted JSON Credentials'],
            ['seo_monthly_cost', '0', 'number', 'Chi phí SEO hàng tháng (VND)'],
            ['organic_lead_value', '0', 'number', 'Giá trị ước tính trên mỗi Lead (VND)'],
            ['kpi_organic_sessions_target', '0', 'number', 'KPI Traffic truy cập tự nhiên'],
            ['kpi_impressions_target', '0', 'number', 'KPI Lượt hiển thị tìm kiếm tự nhiên'],
            ['kpi_position_target', '0', 'number', 'KPI Vị trí từ khóa trung bình (Top)'],
            ['kpi_ctr_target', '0.0', 'number', 'KPI Tỷ lệ Click CTR (%)'],
            ['kpi_engagement_rate_target', '0.0', 'number', 'KPI Tỷ lệ tương tác (%)'],
            ['kpi_avg_engagement_time_target', '0', 'number', 'KPI Thời gian tương tác trung bình (giây)'],
            ['kpi_conversion_rate_target', '0.0', 'number', 'KPI Tỷ lệ chuyển đổi (%)'],
            ['kpi_roi_target', '0.0', 'number', 'KPI Tỷ lệ hoàn vốn đầu tư ROI (%)']
        ];
        $stmtSet = $this->pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)");
        foreach ($settings as $set) {
            $stmtSet->execute($set);
        }

        // Apply Victoria's brand defaults once while preserving later admin edits.
        $brandMigration = $this->pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'victoria_brand_migration'")->fetchColumn();
        if ($brandMigration === false) {
            $victoriaSettings = [
                'site_name' => 'Victoria Universal',
                'site_slogan' => 'Tư vấn du học Nhật Bản, hỗ trợ giáo dục và chương trình trao đổi sinh viên.',
                'site_email' => 'info.duhocvictoria@gmail.com',
                'site_phone' => '0964 808 886',
                'site_address' => 'Số 45 ngõ 207 Quang Trung, Thành phố Hải Dương, tỉnh Hải Dương',
                'site_logo_url' => '/assets/images/VICTORIA_LOGO.svg',
                'site_favicon_url' => '/assets/images/VICTORIA_LOGO.svg',
                'site_footer_desc' => 'Công ty TNHH Toàn Cầu Victoria đồng hành cùng học viên trên hành trình học tập và xây dựng tương lai tại Nhật Bản.',
                'default_og_image' => '/assets/images/hero-new.webp',
                'default_meta_description' => 'Tư vấn du học Nhật Bản, hỗ trợ giáo dục và chương trình trao đổi sinh viên.',
                'default_meta_keywords' => 'du học Nhật Bản, Victoria Universal, học bổng Nhật Bản',
                'facebook_url' => 'https://www.facebook.com/Tuvanduhocvictoriauniversal',
            ];
            $brandUpdate = $this->pdo->prepare("UPDATE settings SET setting_value = ?, updated_at = datetime('now','localtime') WHERE setting_key = ?");
            foreach ($victoriaSettings as $key => $value) {
                $brandUpdate->execute([$value, $key]);
            }
            $this->pdo->exec("INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES ('victoria_brand_migration', '1', 'number', 'Victoria brand defaults applied')");
        }
    }
}
