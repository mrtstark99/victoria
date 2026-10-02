<?php
/**
 * Agent Post Operations Processor
 * Enhanced: featured_image support, custom schema, review enforcement, event dispatch, auto-indexing
 */

namespace Controllers\Agent;

use Models\Post;
use Models\AgentToken;
use Helpers\EventDispatcher;
use Helpers\IndexingService;
use Helpers\SchemaBuilder;

class AgentPostProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || in_array('posts:read', $scopes);
        $hasDraft = in_array('admin', $scopes) || in_array('posts:draft', $scopes);
        $hasPublish = in_array('admin', $scopes) || in_array('posts:publish', $scopes);

        switch ($action) {
            case 'posts':
                if (!$hasRead) $this->forbidden('Missing scope [posts:read]');
                $page = max(1, (int)($_GET['page'] ?? $input['page'] ?? 1));
                $limit = max(1, min(100, (int)($_GET['limit'] ?? $_GET['per_page'] ?? $input['limit'] ?? $input['per_page'] ?? 50)));
                $filters = [];
                if (!empty($_GET['status']) || !empty($input['status'])) {
                    $filters['status'] = trim($_GET['status'] ?? $input['status']);
                }
                if (!empty($_GET['category_id']) || !empty($input['category_id'])) {
                    $filters['category_id'] = (int)($_GET['category_id'] ?? $input['category_id']);
                }
                if (!empty($_GET['search']) || !empty($input['search'])) {
                    $filters['search'] = trim($_GET['search'] ?? $input['search']);
                }
                $total = Post::count($filters);
                $posts = Post::getPaginated($page, $limit, $filters);
                $this->success([
                    'items' => $posts,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total_items' => $total,
                        'total_pages' => (int)ceil($total / $limit)
                    ]
                ]);
                break;

            case 'create_draft':
                if (!$hasDraft) $this->forbidden('Missing scope [posts:draft]');
                $title = trim($input['title'] ?? '');
                $category_id = (int)($input['category_id'] ?? 0);
                if ($title === '' || $category_id <= 0) {
                    $this->error('Missing title or category_id');
                }

                $content = (string)($input['content'] ?? '');
                $contentValidation = validatePostElementContent($content);
                if (!$contentValidation['valid']) {
                    $this->error('Invalid post content: ' . implode(' ', $contentValidation['errors']), 422);
                }

                $slug = createSlug($title);
                $db = \Database::getInstance();
                $stmt = $db->prepare("SELECT id FROM posts WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetch()) $slug .= '-' . time();

                // Validate custom_schema_json if provided
                $customSchema = null;
                if (!empty($input['custom_schema_json'])) {
                    $schemaStr = is_string($input['custom_schema_json'])
                        ? $input['custom_schema_json']
                        : json_encode($input['custom_schema_json'], JSON_UNESCAPED_UNICODE);
                    $validation = SchemaBuilder::validateSchemaJSON($schemaStr);
                    if (!$validation['valid']) {
                        $this->error('Invalid custom_schema_json: ' . implode('; ', $validation['errors']));
                    }
                    $customSchema = $schemaStr;
                }

                $data = [
                    'title' => $title, 'slug' => $slug,
                    'excerpt' => trim($input['excerpt'] ?? getExcerpt($content)),
                    'content' => sanitizeHtml($content),
                    'category_id' => $category_id,
                    'author_id' => $agent['default_author_id'] ?: 1,
                    'status' => 'ai_draft',
                    'featured' => (int)($input['featured'] ?? 0),
                    'featured_image' => $input['featured_image'] ?? null,
                    'meta_title' => trim($input['meta_title'] ?? seoTitle($title)),
                    'meta_description' => trim($input['meta_description'] ?? seoDescription($content)),
                    'meta_keywords' => $input['meta_keywords'] ?? '',
                    'custom_schema_json' => $customSchema,
                    'published_at' => $input['published_at'] ?? null,
                    'changed_by' => 'agent_token_' . $agent['id']
                ];
                
                $id = Post::create($data);
                AgentToken::logAudit($agent['default_author_id'], 'agent_create_draft', 'posts', $id, [], [
                    'title' => $title,
                    'slug' => $slug,
                    'status' => 'ai_draft',
                    'category_id' => $category_id,
                    'excerpt' => $data['excerpt'],
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'has_featured_image' => !empty($data['featured_image']),
                    'has_custom_schema' => !empty($customSchema),
                    'content_preview' => mb_substr(strip_tags($data['content']), 0, 250) . (mb_strlen($data['content']) > 250 ? '...' : '')
                ]);

                // Dispatch draft_created event
                EventDispatcher::dispatch('draft_created', [
                    'post_id' => $id,
                    'title' => $title,
                    'slug' => $slug,
                    'category_id' => $category_id
                ]);

                $this->success(['id' => $id, 'slug' => $slug, 'status' => 'ai_draft', 'url' => '/blog/' . $slug, 'content_validation' => validateAIContent($data['content'])]);
                break;

            case 'update_post':
                if (!$hasDraft) $this->forbidden('Missing scope [posts:draft]');
                $id = (int)($input['id'] ?? 0);
                $post = Post::findById($id);
                if (!$post) $this->error('Post not found', 404);

                // Require an atomic optimistic-lock token so concurrent agent edits cannot silently overwrite each other.
                $expectedUpdatedAt = (string)($input['expected_updated_at'] ?? '');
                if ($expectedUpdatedAt === '') {
                    $this->error('Missing required field: expected_updated_at');
                }
                if ($post['updated_at'] !== $expectedUpdatedAt) {
                    $this->error('Conflict: Post has been modified by another editor.', 409);
                }

                $status = $input['status'] ?? $post['status'];
                if ($status === 'published' && !$hasPublish) {
                    $this->error('Forbidden: Direct publishing not permitted. Choose [pending_review] status.', 403);
                }
                $content = (string)($input['content'] ?? $post['content']);
                $contentValidation = validatePostElementContent($content);
                if (!$contentValidation['valid']) {
                    $this->error('Invalid post content: ' . implode(' ', $contentValidation['errors']), 422);
                }

                // Validate custom_schema_json if provided
                $customSchema = $post['custom_schema_json'] ?? null;
                if (array_key_exists('custom_schema_json', $input)) {
                    if (!empty($input['custom_schema_json'])) {
                        $schemaStr = is_string($input['custom_schema_json'])
                            ? $input['custom_schema_json']
                            : json_encode($input['custom_schema_json'], JSON_UNESCAPED_UNICODE);
                        $validation = SchemaBuilder::validateSchemaJSON($schemaStr);
                        if (!$validation['valid']) {
                            $this->error('Invalid custom_schema_json: ' . implode('; ', $validation['errors']));
                        }
                        $customSchema = $schemaStr;
                    } else {
                        $customSchema = null; // Explicitly cleared
                    }
                }

                $data = [
                    'title' => trim($input['title'] ?? $post['title']),
                    'slug' => trim($input['slug'] ?? $post['slug']),
                    'excerpt' => trim($input['excerpt'] ?? $post['excerpt']),
                    'content' => sanitizeHtml($content),
                    'featured_image' => $input['featured_image'] ?? $post['featured_image'],
                    'category_id' => isset($input['category_id']) ? (int)$input['category_id'] : $post['category_id'],
                    'status' => $status,
                    'featured' => isset($input['featured']) ? (int)$input['featured'] : $post['featured'],
                    'meta_title' => trim($input['meta_title'] ?? $post['meta_title']),
                    'meta_description' => trim($input['meta_description'] ?? $post['meta_description']),
                    'meta_keywords' => trim($input['meta_keywords'] ?? $post['meta_keywords']),
                    'custom_schema_json' => $customSchema,
                    'published_at' => $input['published_at'] ?? $post['published_at'],
                    'author_id' => $post['author_id'],
                    'changed_by' => 'agent_token_' . $agent['id']
                ];
                
                if (!Post::update($id, $data, $expectedUpdatedAt)) {
                    $this->error('Conflict: Post has been modified by another editor.', 409);
                }

                $changedOld = [];
                $changedNew = [];
                $compareKeys = ['title', 'slug', 'excerpt', 'category_id', 'status', 'featured', 'meta_title', 'meta_description', 'meta_keywords', 'published_at'];
                foreach ($compareKeys as $k) {
                    $oldVal = $post[$k] ?? null;
                    $newVal = $data[$k] ?? null;
                    if ((string)$oldVal !== (string)$newVal) {
                        $changedOld[$k] = $oldVal;
                        $changedNew[$k] = $newVal;
                    }
                }
                if (($post['content'] ?? '') !== ($data['content'] ?? '')) {
                    $changedOld['content'] = mb_substr(strip_tags($post['content'] ?? ''), 0, 120) . '...';
                    $changedNew['content'] = mb_substr(strip_tags($data['content'] ?? ''), 0, 120) . '...';
                }
                if (empty($changedNew)) {
                    $changedOld = ['title' => $post['title'], 'status' => $post['status']];
                    $changedNew = ['title' => $data['title'], 'status' => $status];
                }

                AgentToken::logAudit($agent['default_author_id'], 'agent_update_post', 'posts', $id, $changedOld, $changedNew);
                $this->success(['id' => $id, 'slug' => $data['slug'], 'status' => $status, 'content_validation' => validateAIContent($data['content'])]);
                break;

            case 'submit_for_review':
            case 'approve_post':
            case 'publish_post':
                $id = (int)($input['id'] ?? 0);
                $post = Post::findById($id);
                if (!$post) $this->error('Post not found', 404);
                
                $statusMap = ['submit_for_review' => 'pending_review', 'approve_post' => 'approved', 'publish_post' => 'published'];
                $requiredScope = ['submit_for_review' => $hasDraft, 'approve_post' => $hasPublish, 'publish_post' => $hasPublish];
                
                if (!$requiredScope[$action]) $this->forbidden("Insufficient scope permissions.");

                $contentValidation = validatePostElementContent((string)$post['content']);
                if (!$contentValidation['valid']) {
                    $this->error('Post content must be fixed before workflow transition: ' . implode(' ', $contentValidation['errors']), 422);
                }

                $uiValidation = validateAIContent((string)$post['content']);
                if (!$uiValidation['valid']) {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'error' => 'Fix UI warnings before review or publication.',
                        'data' => ['content_validation' => $uiValidation]], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                // ── Human-in-the-loop enforcement ──
                if ($action === 'publish_post' && defined('AGENT_REQUIRE_REVIEW_BEFORE_PUBLISH') && AGENT_REQUIRE_REVIEW_BEFORE_PUBLISH) {
                    $currentStatus = $post['status'];
                    if ($currentStatus !== 'approved') {
                        $this->error(
                            "Review Required: Cannot publish directly from status [{$currentStatus}]. " .
                            "The post must be approved first. Flow: ai_draft → pending_review → approved → published. " .
                            "Use submit_for_review and approve_post actions first.",
                            403
                        );
                    }
                }
                
                $newStatus = $statusMap[$action];
                $db = \Database::getInstance();

                if ($action === 'publish_post' && empty($post['published_at'])) {
                    // Set published_at timestamp when first published
                    $db->prepare("UPDATE posts SET status = ?, published_at = datetime('now','localtime'), updated_at = datetime('now','localtime') WHERE id = ?")
                       ->execute([$newStatus, $id]);
                } else {
                    $db->prepare("UPDATE posts SET status = ?, updated_at = datetime('now','localtime') WHERE id = ?")
                       ->execute([$newStatus, $id]);
                }
                   
                AgentToken::logAudit($agent['default_author_id'], 'agent_' . $action, 'posts', $id, ['status' => $post['status']], ['status' => $newStatus, 'title' => $post['title']]);

                $responseData = ['id' => $id, 'status' => $newStatus];

                // ── Post-publish automation: Event dispatch + Indexing ──
                if ($action === 'publish_post') {
                    $eventResults = EventDispatcher::dispatch('post_published', [
                        'post_id' => $id,
                        'title' => $post['title'],
                        'slug' => $post['slug']
                    ]);

                    $responseData['automation'] = [
                        'events_triggered' => count($eventResults),
                        'actions' => array_map(fn($r) => $r['action'] ?? 'unknown', $eventResults)
                    ];
                }

                $this->success($responseData);
                break;

            case 'list_revisions':
                if (!$hasRead) $this->forbidden('Missing scope [posts:read]');
                $id = (int)($_GET['post_id'] ?? ($input['post_id'] ?? 0));
                $this->success(Post::getRevisions($id));
                break;

            case 'restore_revision':
                if (!$hasDraft) $this->forbidden('Missing scope [posts:draft]');
                $revisionId = (int)($input['revision_id'] ?? 0);
                if ($revisionId <= 0) {
                    $this->error('Missing required parameter: revision_id');
                }
                $db = \Database::getInstance();
                $stmtRev = $db->prepare("SELECT * FROM post_revisions WHERE id = ?");
                $stmtRev->execute([$revisionId]);
                $rev = $stmtRev->fetch();
                if (!$rev) {
                    $this->error('Revision not found', 404);
                }

                $postId = (int)$rev['post_id'];
                $post = Post::findById($postId);
                if (!$post) {
                    $this->error('Associated post not found', 404);
                }

                $data = [
                    'title' => $rev['title'],
                    'slug' => $rev['slug'],
                    'excerpt' => $rev['excerpt'] ?? '',
                    'content' => $rev['content'] ?? '',
                    'featured_image' => $post['featured_image'],
                    'category_id' => $post['category_id'],
                    'status' => $post['status'],
                    'featured' => $post['featured'],
                    'meta_title' => $rev['meta_title'] ?? $post['meta_title'],
                    'meta_description' => $rev['meta_description'] ?? $post['meta_description'],
                    'meta_keywords' => $rev['meta_keywords'] ?? $post['meta_keywords'],
                    'custom_schema_json' => $post['custom_schema_json'] ?? null,
                    'published_at' => $post['published_at'],
                    'author_id' => $post['author_id'],
                    'changed_by' => 'agent_token_' . $agent['id'] . '_restore_rev_' . $revisionId
                ];

                Post::update($postId, $data);
                $authorId = $agent['default_author_id'] ?? $agent['user_id'] ?? 1;
                AgentToken::logAudit($authorId, 'agent_restore_revision', 'posts', $postId, ['title' => $post['title'], 'revision_id' => $revisionId], ['title' => $data['title'], 'status' => $post['status']]);
                $this->success(['id' => $postId, 'restored_from_revision' => $revisionId, 'title' => $data['title']]);
                break;
        }
    }

    private function success($data) {
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    private function error($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    private function forbidden($msg) {
        $this->error("Forbidden: " . $msg, 403);
    }
}
