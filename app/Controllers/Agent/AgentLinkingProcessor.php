<?php
/**
 * Agent Internal Linking Processor
 * Actions: link_suggestions, backlink_candidates, link_audit
 * Link audit logic delegated to AgentLinkAuditor.
 */

namespace Controllers\Agent;

use Models\Post;
use Models\AgentToken;

class AgentLinkingProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || in_array('posts:read', $scopes);

        if (!$hasRead) $this->forbidden('Missing scope [posts:read]');

        $authorId = $agent['default_author_id'] ?? $agent['user_id'] ?? 1;

        switch ($action) {
            case 'link_suggestions':
                $postId  = (int)($input['post_id'] ?? $_GET['post_id'] ?? 0);
                $keyword = trim($input['keyword'] ?? $_GET['keyword'] ?? '');
                $limit   = min(20, max(1, (int)($input['limit'] ?? $_GET['limit'] ?? 10)));

                if ($postId <= 0 && $keyword === '') {
                    $this->error('Provide either post_id or keyword parameter.');
                }

                $suggestions = $this->findLinkSuggestions($postId, $keyword, $limit);

                AgentToken::logAudit($authorId, 'agent_link_suggestions', 'posts', $postId ?: null, [], [
                    'keyword'           => $keyword,
                    'suggestions_count' => count($suggestions)
                ]);

                $this->success([
                    'suggestions' => $suggestions,
                    'total'       => count($suggestions),
                    'direction'   => 'outbound',
                    'usage_hint'  => 'Insert these links into your new/current post content to improve internal linking.'
                ], 'Internal link suggestions generated.');
                break;

            case 'backlink_candidates':
                $postId = (int)($input['post_id'] ?? $_GET['post_id'] ?? 0);
                if ($postId <= 0) {
                    $this->error('Missing required parameter: post_id');
                }

                $post = Post::findById($postId);
                if (!$post) $this->error('Post not found.', 404);

                $candidates = $this->findBacklinkCandidates($post);

                AgentToken::logAudit($authorId, 'agent_backlink_candidates', 'posts', $postId, [], [
                    'title'            => $post['title'],
                    'candidates_count' => count($candidates)
                ]);

                $this->success([
                    'target_post' => [
                        'id'    => $post['id'],
                        'title' => $post['title'],
                        'slug'  => $post['slug'],
                        'url'   => '/blog/' . $post['slug']
                    ],
                    'candidates' => $candidates,
                    'total'      => count($candidates),
                    'direction'  => 'inbound',
                    'usage_hint' => 'These old posts should be updated to include a link pointing back to the target post.'
                ], 'Backlink candidates identified.');
                break;

            case 'link_audit':
                $audit = (new AgentLinkAuditor())->audit();

                AgentToken::logAudit($authorId, 'agent_link_audit', 'posts', null, [], [
                    'orphan_pages'   => count($audit['orphan_pages']),
                    'over_linked'    => count($audit['over_linked_pages']),
                    'under_linked'   => count($audit['under_linked_pages'])
                ]);

                $this->success($audit, 'Internal link audit completed.');
                break;
        }
    }

    // ─────────────── Private Helpers ───────────────

    /**
     * Find internal link suggestions for outbound linking
     */
    private function findLinkSuggestions(int $excludePostId, string $keyword, int $limit): array {
        $db = \Database::getInstance();

        $searchTerms = [];
        if ($keyword !== '') {
            $searchTerms = array_filter(explode(' ', $keyword), fn($w) => mb_strlen($w) > 2);
        } elseif ($excludePostId > 0) {
            $post = Post::findById($excludePostId);
            if ($post) {
                $searchTerms = array_filter(explode(' ', $post['title']), fn($w) => mb_strlen($w) > 2);
                if (!empty($post['meta_keywords'])) {
                    $searchTerms = array_merge($searchTerms, array_map('trim', explode(',', $post['meta_keywords'])));
                }
            }
        }

        if (empty($searchTerms)) return [];

        $conditions = [];
        $params     = [];
        foreach (array_slice($searchTerms, 0, 8) as $term) {
            $term = trim($term);
            if (mb_strlen($term) > 2) {
                $conditions[] = "(p.title LIKE ? OR p.content LIKE ? OR p.meta_keywords LIKE ?)";
                $params[]     = "%{$term}%";
                $params[]     = "%{$term}%";
                $params[]     = "%{$term}%";
            }
        }

        if (empty($conditions)) return [];

        $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.category_id, p.meta_keywords, c.name as category_name
                FROM posts p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                AND p.id != ?
                AND (" . implode(' OR ', $conditions) . ")
                ORDER BY p.views DESC, p.created_at DESC
                LIMIT ?";

        $params = array_merge([$excludePostId], $params, [$limit]);
        $stmt   = $db->prepare($sql);
        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        $posts = $stmt->fetchAll();

        $suggestions = [];
        foreach ($posts as $post) {
            $relevance  = $this->calculateRelevance($post, $searchTerms);
            $anchorText = $this->suggestAnchorText($post, $keyword);

            $suggestions[] = [
                'post_id'              => (int)$post['id'],
                'title'                => $post['title'],
                'slug'                 => $post['slug'],
                'url'                  => '/blog/' . $post['slug'],
                'category'             => $post['category_name'] ?? '',
                'relevance_score'      => $relevance,
                'suggested_anchor_text' => $anchorText,
                'excerpt'              => mb_substr(strip_tags($post['excerpt'] ?? ''), 0, 120) . '...'
            ];
        }

        usort($suggestions, fn($a, $b) => $b['relevance_score'] <=> $a['relevance_score']);

        return $suggestions;
    }

    /**
     * Find old posts that should link back to a new post
     */
    private function findBacklinkCandidates(array $targetPost): array {
        $db = \Database::getInstance();

        $keywords = array_filter(explode(' ', $targetPost['title']), fn($w) => mb_strlen(trim($w)) > 2);
        if (!empty($targetPost['meta_keywords'])) {
            $keywords = array_merge($keywords, array_map('trim', explode(',', $targetPost['meta_keywords'])));
        }
        $keywords  = array_unique(array_filter($keywords));
        if (empty($keywords)) return [];

        $targetUrl  = '/blog/' . $targetPost['slug'];
        $conditions = [];
        $params     = [];
        foreach (array_slice($keywords, 0, 6) as $kw) {
            $kw = trim($kw);
            if (mb_strlen($kw) > 2) {
                $conditions[] = "(p.title LIKE ? OR p.content LIKE ?)";
                $params[]     = "%{$kw}%";
                $params[]     = "%{$kw}%";
            }
        }

        if (empty($conditions)) return [];

        $sql = "SELECT p.id, p.title, p.slug, p.content, p.category_id, c.name as category_name
                FROM posts p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                AND p.id != ?
                AND p.content NOT LIKE ?
                AND (" . implode(' OR ', $conditions) . ")
                ORDER BY p.views DESC
                LIMIT 15";

        $params = array_merge([(int)$targetPost['id'], "%{$targetUrl}%"], $params);
        $stmt   = $db->prepare($sql);
        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        $posts = $stmt->fetchAll();

        $candidates = [];
        foreach ($posts as $post) {
            $insertionContext = $this->findInsertionContext($post['content'], $keywords);
            $candidates[] = [
                'post_id'               => (int)$post['id'],
                'title'                 => $post['title'],
                'slug'                  => $post['slug'],
                'url'                   => '/blog/' . $post['slug'],
                'category'              => $post['category_name'] ?? '',
                'suggested_anchor_text' => $this->suggestAnchorText($targetPost, ''),
                'insertion_context'     => $insertionContext,
                'action_required'       => 'Add a link to "' . $targetPost['title'] . '" in this post via update_post API.'
            ];
        }

        return $candidates;
    }

    private function calculateRelevance(array $post, array $searchTerms): int {
        $score    = 0;
        $title    = mb_strtolower($post['title'] ?? '', 'UTF-8');
        $keywords = mb_strtolower($post['meta_keywords'] ?? '', 'UTF-8');

        foreach ($searchTerms as $term) {
            $term = mb_strtolower(trim($term), 'UTF-8');
            if (mb_strlen($term) < 3) continue;
            if (mb_strpos($title, $term) !== false)    $score += 30;
            if (mb_strpos($keywords, $term) !== false) $score += 20;
        }

        return min(100, $score);
    }

    private function suggestAnchorText(array $post, string $keyword): string {
        if ($keyword !== '' && mb_strlen($keyword) <= 60) {
            return $keyword;
        }
        $title = $post['title'] ?? '';
        if (mb_strlen($title) > 60) {
            $title = mb_substr($title, 0, 57, 'UTF-8') . '...';
        }
        return $title;
    }

    private function findInsertionContext(string $content, array $keywords): string {
        foreach ($keywords as $kw) {
            $kw = trim($kw);
            if (mb_strlen($kw) < 3) continue;
            $pos = mb_stripos($content, $kw, 0, 'UTF-8');
            if ($pos !== false) {
                $start   = max(0, $pos - 50);
                $snippet = mb_substr(strip_tags($content), $start, 120, 'UTF-8');
                return '...' . trim($snippet) . '...';
            }
        }
        return 'Near a semantically related paragraph.';
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
