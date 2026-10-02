<?php
/**
 * Sitemap Controller & XML Generator
 */

namespace Controllers;

class SitemapController {

    public static function generateSitemapFile(): string {
        $baseUrl = BlogController::getBaseUrl();
        $db = \Database::getInstance();
        $urls = [];
        $addUrl = static function (string $path, ?string $lastModified = null, string $changeFrequency = 'weekly', string $priority = '0.7') use (&$urls, $baseUrl): void {
            $loc = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
            $key = strtolower($loc);
            $lastmod = $lastModified ? strtotime($lastModified) : false;
            $urls[$key] = [
                'loc' => $loc,
                'lastmod' => $lastmod ? gmdate('c', $lastmod) : null,
                'changefreq' => $changeFrequency,
                'priority' => $priority,
            ];
        };

        // Public routes registered in public/index.php.
        $addUrl('', null, 'daily', '1.0');
        foreach (['blog', 'services', 'contact', 'about', 'schools', 'courses', 'process', 'documents', 'cost', 'consultation'] as $route) {
            $addUrl($route, null, 'monthly', '0.8');
        }

        // Published CMS pages and posts.
        $pages = $db->query("SELECT slug, updated_at, created_at FROM pages WHERE status = 'published' ORDER BY sort_order ASC, updated_at DESC")->fetchAll();
        $posts = $db->query("SELECT slug, updated_at, created_at FROM posts WHERE status = 'published' ORDER BY updated_at DESC")->fetchAll();
        $categories = $db->query("SELECT c.slug, c.updated_at, c.created_at FROM categories c WHERE EXISTS (SELECT 1 FROM posts p WHERE p.category_id = c.id AND p.status = 'published') ORDER BY c.name ASC")->fetchAll();
        $services = $db->query("SELECT slug, updated_at, created_at FROM services WHERE status = 'active' ORDER BY display_order ASC, id ASC")->fetchAll();

        foreach ($pages as $page) {
            $addUrl('page/' . $page['slug'], $page['updated_at'] ?: $page['created_at'], 'weekly', '0.85');
        }
        foreach ($categories as $category) {
            $addUrl('category/' . $category['slug'], $category['updated_at'] ?? $category['created_at'] ?? null, 'weekly', '0.8');
        }
        foreach ($posts as $post) {
            $addUrl('blog/' . $post['slug'], $post['updated_at'] ?: $post['created_at'], 'monthly', '0.9');
        }
        foreach ($services as $service) {
            $addUrl('services/' . $service['slug'], $service['updated_at'] ?? $service['created_at'] ?? null, 'monthly', '0.8');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
            if ($url['lastmod']) $xml .= '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        @file_put_contents(APP_ROOT . '/public/sitemap.xml', $xml, LOCK_EX);
        return $xml;
    }

    public function sitemap() {
        header("Content-Type: application/xml; charset=utf-8");
        header('Cache-Control: public, max-age=300');
        $xml = self::generateSitemapFile();
        echo $xml;
        exit;
    }
}
