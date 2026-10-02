<?php
/**
 * @file app/Controllers/BlogController.php
 * @description Public presentation controller for homepage, blog catalog, and article views.
 *
 * Layer:
 * - Presentation / Controller
 *
 * Responsibilities:
 * - Render Victoria Universal corporate homepage with interactive sections and latest posts.
 * - Render blog magazine archive with search, categories, and pagination.
 * - Render individual article view with Table of Contents and Post Element Contract styles.
 * - Render category archives and custom CMS pages.
 *
 * Security:
 * - Input validation on pagination and slugs.
 * - Records page views without storing sensitive identifiers.
 *
 * Dependencies:
 * - Models (Post, Category, Page, Service, PageView).
 * - Helpers (seo_helper, template_helper, SchemaBuilder).
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

namespace Controllers;

use Models\Post;
use Models\Category;
use Models\PageView;
use Models\Service;
use Models\Page;

class BlogController {
    /**
     * Get validated system base URL.
     *
     * @return string
     */
    public static function getBaseUrl(): string {
        return getSystemBaseUrl();
    }

    /**
     * Corporate Homepage Landing Page (/).
     */
    public function index() {
        Post::publishScheduledPosts();
        PageView::record(null);

        // Fetch latest published posts for homepage preview section
        $latestPosts = Post::getPaginated(1, 6, ['status' => 'published']);
        $services = Service::getActive();
        $categories = Category::getAll();
        $zoomStmt = \Database::getInstance()->prepare("
            SELECT * FROM consultation_slots
            WHERE type = 'group'
              AND status IN ('active', 'full')
              AND scheduled_date >= date('now', 'localtime')
            ORDER BY scheduled_date ASC, time_start ASC
            LIMIT 3
        ");
        $zoomStmt->execute();
        $zoomSlots = $zoomStmt->fetchAll();

        $baseUrl = self::getBaseUrl();
        $siteTitle = 'Victoria Universal';
        $siteSlogan = 'Tư vấn du học Nhật Bản, hỗ trợ giáo dục và chương trình trao đổi sinh viên.';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $siteTitle,
            'url' => "{$baseUrl}/",
            'logo' => "{$baseUrl}/assets/images/VICTORIA_LOGO.svg",
            'description' => $siteSlogan,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => getSetting('site_address', 'Số 45 ngõ 207 Quang Trung, Phường Thành Đông, TP Hải Phòng, Việt Nam'),
                'addressCountry' => 'VN'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => getSetting('site_phone', '0964 808 886'),
                'contactType' => 'customer service',
                'areaServed' => ['VN', 'JP'],
                'availableLanguage' => ['Vietnamese', 'Japanese']
            ]
        ];

        view('blog/home', [
            'latest_posts' => $latestPosts,
            'services' => $services,
            'categories' => $categories,
            'zoom_slots' => $zoomSlots,
            'page_title' => "{$siteTitle} - Tư vấn du học Nhật Bản",
            'meta_description' => $siteSlogan,
            'canonical_url' => "{$baseUrl}/",
            'og_type' => 'website',
            'schema_json' => $schema,
            'page_css' => 'victoria'
        ]);
    }

    /**
     * Blog Magazine & Search Catalog (/blog).
     */
    public function blogList() {
        Post::publishScheduledPosts();
        PageView::record(null);
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = DEFAULT_POSTS_PER_PAGE;
        $search = trim($_GET['q'] ?? $_GET['search'] ?? '');
        
        $filters = ['status' => 'published'];
        if ($search !== '') {
            $filters['search'] = $search;
        }

        $posts = Post::getPaginated($page, $perPage, $filters);
        $total = Post::count($filters);
        $categories = Category::getAll();
        $suggestedSearchTerms = Post::getSuggestedSearchTerms();

        $baseUrl = self::getBaseUrl();
        $canonicalUrl = $page > 1 ? "{$baseUrl}/blog?page={$page}" : "{$baseUrl}/blog";
        if ($search !== '') {
            $canonicalUrl = "{$baseUrl}/blog?q=" . urlencode($search) . ($page > 1 ? "&page={$page}" : '');
        }

        $siteTitle = getSetting('site_name', 'Victoria Universal');
        $pageTitle = $search !== '' 
            ? 'Kết quả tìm kiếm cho "' . htmlspecialchars($search) . '" - ' . $siteTitle 
            : 'Blog & Tin tức Du học - ' . $siteTitle;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $pageTitle,
            'url' => $canonicalUrl,
            'description' => 'Cẩm nang du học Nhật Bản, visa, học bổng và cuộc sống du học sinh.'
        ];

        view('blog/blog_list', [
            'posts' => $posts,
            'categories' => $categories,
            'suggested_search_terms' => $suggestedSearchTerms,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'search_query' => $search,
            'page_title' => $pageTitle,
            'meta_description' => 'Cẩm nang du học Nhật Bản: trường học, visa, học bổng, việc làm thêm và cuộc sống du học sinh.',
            'canonical_url' => $canonicalUrl,
            'og_type' => 'website',
            'schema_json' => $schema,
            'page_css' => 'home'
        ]);
    }

    /**
     * Show single article detail (/blog/{slug}).
     *
     * @param string $slug
     */
    public function showPost($slug) {
        Post::publishScheduledPosts();
        $post = Post::findBySlug($slug);
        if (!$post || $post['status'] !== 'published') {
            header('HTTP/1.0 404 Not Found');
            die('Article not found');
        }

        PageView::record($post['id']);
        Post::incrementViews($post['id']);

        $tocData = buildPostTableOfContents($post['content']);
        $post['content'] = $tocData['content'];
        $toc = $tocData['items'];

        $sidebarSettings = AdminSidebarController::getSidebarSettings();
        $relatedLimit = max(1, (int)($sidebarSettings['post_related_limit'] ?? 3));
        $trendingLimit = max(1, (int)($sidebarSettings['post_sidebar_trending_limit'] ?? 4));

        $db = \Database::getInstance();
        $related = [];
        if (($sidebarSettings['post_related_enabled'] ?? '1') === '1') {
            $stmt = $db->prepare("
                SELECT * FROM posts 
                WHERE category_id = ? AND id != ? AND status = 'published' 
                ORDER BY created_at DESC LIMIT " . $relatedLimit . "
            ");
            $stmt->execute([$post['category_id'], $post['id']]);
            $related = $stmt->fetchAll();
        }

        $trending = [];
        if (($sidebarSettings['post_sidebar_trending_enabled'] ?? '1') === '1') {
            $stmtTrending = $db->prepare("
                SELECT p.id, p.title, p.slug, p.featured_image, p.category_id, p.views, p.created_at, p.published_at, c.name as category_name, c.slug as category_slug
                FROM posts p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' AND p.id != ?
                ORDER BY p.views DESC, p.created_at DESC LIMIT " . $trendingLimit . "
            ");
            $stmtTrending->execute([$post['id']]);
            $trending = $stmtTrending->fetchAll();
        }

        $sidebarCategories = ($sidebarSettings['post_sidebar_categories_enabled'] ?? '0') === '1' 
            ? Category::getAll() 
            : [];

        $readingTime = calculateReadingTime($post['content']);
        $metaDesc = seoDescription($post['meta_description'], $post['content']);
        $pageTitle = seoTitle($post['meta_title'] ?: $post['title']);

        $baseUrl = self::getBaseUrl();
        $canonicalUrl = "{$baseUrl}/blog/{$post['slug']}";
        $schema = \Helpers\SchemaBuilder::buildArticleSchema($post, $canonicalUrl);

        view('blog/post', [
            'post' => $post,
            'toc' => $toc,
            'related' => $related,
            'trending' => $trending,
            'sidebar_categories' => $sidebarCategories,
            'sidebar_settings' => $sidebarSettings,
            'reading_time' => $readingTime,
            'page_title' => $pageTitle,
            'meta_description' => $metaDesc,
            'meta_keywords' => $post['meta_keywords'] ?? '',
            'canonical_url' => $canonicalUrl,
            'og_type' => 'article',
            'og_image' => getPostFeaturedImage($post),
            'schema_json' => $schema,
            'page_css' => 'post'
        ]);
    }

    /**
     * Show category archives (/category/{slug}).
     *
     * @param string $slug
     */
    public function showCategory($slug) {
        $category = Category::findBySlug($slug);
        if (!$category) {
            header('HTTP/1.0 404 Not Found');
            die('Category not found');
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = DEFAULT_POSTS_PER_PAGE;
        $filters = ['category_id' => $category['id'], 'status' => 'published'];
        $posts = Post::getPaginated($page, $perPage, $filters);
        $total = Post::count($filters);
        $categories = Category::getAll();

        $baseUrl = self::getBaseUrl();
        $canonicalUrl = $page > 1 ? "{$baseUrl}/category/{$category['slug']}?page={$page}" : "{$baseUrl}/category/{$category['slug']}";

        view('blog/category', [
            'category' => $category,
            'posts' => $posts,
            'categories' => $categories,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'page_title' => $category['name'] . ' - ' . SITE_NAME,
            'meta_description' => seoDescription($category['description'], "Tất cả bài viết trong chuyên mục {$category['name']} tại " . SITE_NAME),
            'canonical_url' => $canonicalUrl,
            'og_type' => 'website',
            'page_css' => 'category'
        ]);
    }

    /**
     * Show custom static page (/page/{slug}).
     *
     * @param string $slug
     */
    public function showPage($slug) {
        $page = Page::findBySlug($slug);
        if (!$page || $page['status'] !== 'published') {
            header('HTTP/1.0 404 Not Found');
            die('Page not found');
        }

        Page::incrementViews($page['id']);
        $baseUrl = self::getBaseUrl();
        $canonicalUrl = "{$baseUrl}/page/{$page['slug']}";

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page['title'],
            'url' => $canonicalUrl,
            'description' => $page['meta_description'] ?: $page['excerpt']
        ];

        view('blog/page', [
            'page' => $page,
            'page_title' => $page['meta_title'] ?: ($page['title'] . ' - ' . SITE_NAME),
            'meta_description' => $page['meta_description'] ?: $page['excerpt'],
            'meta_keywords' => $page['meta_keywords'] ?? '',
            'canonical_url' => $canonicalUrl,
            'og_type' => 'website',
            'schema_json' => $schema
        ]);
    }
}
