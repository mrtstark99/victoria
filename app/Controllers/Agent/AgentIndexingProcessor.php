<?php
/**
 * Agent Indexing Processor
 * Actions: ping_index, check_index_status, regenerate_sitemap
 */

namespace Controllers\Agent;

use Helpers\IndexingService;
use Models\Post;
use Models\AgentToken;

class AgentIndexingProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || in_array('seo:read', $scopes);
        $hasWrite = in_array('admin', $scopes) || in_array('seo:write', $scopes) || in_array('posts:publish', $scopes);
        $authorId = $agent['default_author_id'] ?? $agent['user_id'] ?? 1;

        switch ($action) {
            case 'ping_index':
                if (!$hasWrite) $this->forbidden('Missing scope [seo:write] or [posts:publish]');

                $postId = (int)($input['post_id'] ?? 0);
                $urls = $input['urls'] ?? [];

                // If post_id provided, resolve URL
                if ($postId > 0) {
                    $post = Post::findById($postId);
                    if (!$post) $this->error('Post not found.', 404);

                    $baseUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : 'http://localhost:5000';
                    $urls[] = $baseUrl . '/blog/' . $post['slug'];
                }

                if (empty($urls)) {
                    $this->error('Provide post_id or urls array.');
                }

                // Regenerate sitemap first
                IndexingService::regenerateSitemap();

                // Ping IndexNow
                $result = IndexingService::pingIndexNow(array_unique($urls));

                // Ping sitemap
                $sitemapResult = IndexingService::pingSitemap();

                AgentToken::logAudit($authorId, 'agent_ping_index', 'posts', $postId ?: null, [], [
                    'urls' => $urls,
                    'indexnow_success' => $result['success'],
                    'sitemap_ping_success' => $sitemapResult['success']
                ]);

                $this->success([
                    'indexnow' => $result['data'] ?? $result,
                    'sitemap_ping' => $sitemapResult['data'] ?? $sitemapResult,
                    'sitemap_regenerated' => true
                ], 'Index ping completed.');
                break;

            case 'check_index_status':
                if (!$hasRead) $this->forbidden('Missing scope [seo:read]');

                $postId = (int)($input['post_id'] ?? $_GET['post_id'] ?? 0);
                if ($postId <= 0) $this->error('Missing required parameter: post_id');

                $post = Post::findById($postId);
                if (!$post) $this->error('Post not found.', 404);

                $baseUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : 'http://localhost:5000';
                $postUrl = $baseUrl . '/blog/' . $post['slug'];

                // Basic check: try to see if Google has cached the page
                // (Note: full index check requires Google Search Console API credentials)
                $daysSincePublish = null;
                if (!empty($post['published_at'])) {
                    $daysSincePublish = (int)round((time() - strtotime($post['published_at'])) / 86400);
                }

                $this->success([
                    'post_id' => $postId,
                    'url' => $postUrl,
                    'status' => $post['status'],
                    'published_at' => $post['published_at'],
                    'days_since_publish' => $daysSincePublish,
                    'recommendation' => $daysSincePublish !== null && $daysSincePublish > 7
                        ? 'Bài đã xuất bản hơn 7 ngày. Nếu chưa được index, hãy gọi ping_index hoặc submit URL qua Google Search Console.'
                        : 'Chờ 3-7 ngày để Google crawl và index bài viết.',
                    'note' => 'Để kiểm tra chính xác, cấu hình Google Search Console API credentials trong config.'
                ], 'Index status check completed.');
                break;

            case 'regenerate_sitemap':
                if (!$hasWrite) $this->forbidden('Missing scope [seo:write]');

                $result = IndexingService::regenerateSitemap();

                AgentToken::logAudit($authorId, 'agent_regenerate_sitemap', 'settings', null, [], [
                    'regenerated_at' => date('c')
                ]);

                if ($result['success']) {
                    $this->success($result['data'], 'Sitemap regenerated successfully.');
                } else {
                    $this->error($result['error'] ?? 'Sitemap regeneration failed.');
                }
                break;
        }
    }

    private function success($data, $message = '') {
        $resp = ['success' => true, 'data' => $data];
        if ($message) $resp['message'] = $message;
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
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
