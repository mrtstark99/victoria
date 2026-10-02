<?php
/**
 * Agent Page Management Processor (MCP API Layer)
 */

namespace Controllers\Agent;

use Models\Page;
use Models\AgentToken;
use Controllers\SitemapController;

class AgentPageProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || in_array('pages:read', $scopes) || in_array('posts:read', $scopes);
        $hasDraft = in_array('admin', $scopes) || in_array('pages:draft', $scopes) || in_array('posts:draft', $scopes);
        $hasPublish = in_array('admin', $scopes) || in_array('pages:publish', $scopes) || in_array('posts:publish', $scopes);

        switch ($action) {
            case 'pages':
            case 'list_pages':
                if (!$hasRead) $this->forbidden('Missing scope [pages:read]');
                $page = max(1, (int)($_GET['page'] ?? ($input['page'] ?? 1)));
                $perPage = min(50, max(1, (int)($_GET['per_page'] ?? ($input['per_page'] ?? 15))));
                
                $filters = [];
                if (!empty($_GET['status']) || !empty($input['status'])) {
                    $filters['status'] = sanitizeInput($_GET['status'] ?? $input['status']);
                }
                if (!empty($_GET['search']) || !empty($input['search'])) {
                    $filters['search'] = sanitizeInput($_GET['search'] ?? $input['search']);
                }

                $pages = Page::getPaginated($page, $perPage, $filters);
                $total = Page::count($filters);
                $stats = Page::getStats();

                $this->success([
                    'pages' => $pages,
                    'total' => $total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage),
                    'stats' => $stats
                ], 'Pages retrieved successfully.');
                break;

            case 'get_page':
                if (!$hasRead) $this->forbidden('Missing scope [pages:read]');
                $id = (int)($_GET['id'] ?? ($input['id'] ?? 0));
                $slug = sanitizeInput($_GET['slug'] ?? ($input['slug'] ?? ''));

                $pageItem = null;
                if ($id > 0) {
                    $pageItem = Page::findById($id);
                } elseif ($slug !== '') {
                    $pageItem = Page::findBySlug($slug);
                }

                if (!$pageItem) {
                    $this->error('Page not found.', 404);
                }

                $revisions = Page::getRevisions($pageItem['id'], 5);
                $this->success([
                    'page' => $pageItem,
                    'revisions' => $revisions
                ], 'Page details retrieved successfully.');
                break;

            case 'create_page':
            case 'create_page_draft':
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') $this->error('Method not allowed.', 405);
                if (!$hasDraft) $this->forbidden('Missing scope [pages:draft]');
                if (empty($input) || !is_array($input)) {
                    $this->error('Payload must be a JSON object.');
                }

                $title = trim($input['title'] ?? '');
                if ($title === '') {
                    $this->error('Missing required field: title');
                }

                $slug = trim($input['slug'] ?? '');
                if ($slug === '') {
                    $slug = createSlug($title);
                } else {
                    $slug = createSlug($slug);
                }

                $existing = Page::findBySlug($slug);
                if ($existing) {
                    $slug .= '-' . time();
                }

                $status = $input['status'] ?? 'draft';
                if ($status === 'published' && !$hasPublish) {
                    $status = 'pending_review';
                }
                if (!in_array($status, ['draft', 'published', 'archived', 'ai_draft', 'pending_review', 'approved'], true)) {
                    $status = 'ai_draft';
                }

                $template = in_array($input['template'] ?? '', ['default', 'fullwidth', 'contact', 'landing'], true) ? $input['template'] : 'default';

                $authorId = (int)($agent['default_author_id'] ?? 1);
                if ($authorId <= 0) $authorId = 1;

                $data = [
                    'title' => $title,
                    'slug' => $slug,
                    'excerpt' => trim($input['excerpt'] ?? ''),
                    'content' => sanitizeHtml((string)($input['content'] ?? '')),
                    'template' => $template,
                    'featured_image' => trim($input['featured_image'] ?? ''),
                    'author_id' => $authorId,
                    'status' => $status,
                    'sort_order' => (int)($input['sort_order'] ?? 0),
                    'meta_title' => trim($input['meta_title'] ?? ''),
                    'meta_description' => trim($input['meta_description'] ?? ''),
                    'meta_keywords' => trim($input['meta_keywords'] ?? ''),
                    'custom_schema_json' => is_array($input['custom_schema_json'] ?? null) ? json_encode($input['custom_schema_json'], JSON_UNESCAPED_UNICODE) : trim($input['custom_schema_json'] ?? ''),
                    'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
                    'changed_by' => 'Agent: ' . ($agent['token_name'] ?? 'AI')
                ];

                $pageId = Page::create($data);
                if ($status === 'published') {
                    SitemapController::generateSitemapFile();
                }

                AgentToken::logAudit($agent['default_author_id'], 'agent_create_page', 'pages', $pageId, [], $data);

                $createdPage = Page::findById($pageId);
                $this->success([
                    'page_id' => $pageId,
                    'page' => $createdPage,
                    'url' => '/page/' . $createdPage['slug']
                ], 'Page created successfully.');
                break;

            case 'update_page':
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') $this->error('Method not allowed.', 405);
                if (!$hasDraft) $this->forbidden('Missing scope [pages:draft]');
                $id = (int)($input['id'] ?? ($_GET['id'] ?? 0));
                if ($id <= 0) {
                    $this->error('Missing required field: id');
                }

                $existing = Page::findById($id);
                if (!$existing) {
                    $this->error('Page not found.', 404);
                }
                $expectedUpdatedAt = (string)($input['expected_updated_at'] ?? '');
                if ($expectedUpdatedAt === '') $this->error('Missing required field: expected_updated_at');
                if ($existing['updated_at'] !== $expectedUpdatedAt) {
                    $this->error('Conflict: Page has been modified by another editor.', 409);
                }
                if (($existing['status'] ?? '') === 'published' && !$hasPublish) {
                    $this->forbidden('Editing a published page requires [pages:publish]; submit changes through review instead.');
                }

                $data = [];
                if (isset($input['title'])) $data['title'] = trim($input['title']);
                if (isset($input['slug'])) {
                    $newSlug = createSlug($input['slug']);
                    $slugCheck = Page::findBySlug($newSlug);
                    if ($slugCheck && (int)$slugCheck['id'] !== $id) {
                        $newSlug .= '-' . time();
                    }
                    $data['slug'] = $newSlug;
                }
                if (isset($input['excerpt'])) $data['excerpt'] = trim($input['excerpt']);
                if (isset($input['content'])) $data['content'] = sanitizeHtml((string)$input['content']);
                if (isset($input['template'])) {
                    if (in_array($input['template'], ['default', 'fullwidth', 'contact', 'landing'], true)) {
                        $data['template'] = $input['template'];
                    }
                }
                if (isset($input['featured_image'])) $data['featured_image'] = trim($input['featured_image']);
                if (isset($input['sort_order'])) $data['sort_order'] = (int)$input['sort_order'];
                if (isset($input['meta_title'])) $data['meta_title'] = trim($input['meta_title']);
                if (isset($input['meta_description'])) $data['meta_description'] = trim($input['meta_description']);
                if (isset($input['meta_keywords'])) $data['meta_keywords'] = trim($input['meta_keywords']);
                if (isset($input['custom_schema_json'])) {
                    $data['custom_schema_json'] = is_array($input['custom_schema_json']) ? json_encode($input['custom_schema_json'], JSON_UNESCAPED_UNICODE) : trim($input['custom_schema_json']);
                }

                if (isset($input['status'])) {
                    $reqStatus = $input['status'];
                    if ($reqStatus === 'published' && !$hasPublish) {
                        $reqStatus = 'pending_review';
                    }
                    if (in_array($reqStatus, ['draft', 'published', 'archived', 'ai_draft', 'pending_review', 'approved'], true)) {
                        $data['status'] = $reqStatus;
                    }
                }

                $data['changed_by'] = 'Agent: ' . ($agent['token_name'] ?? 'AI');

                if (!Page::update($id, $data, $expectedUpdatedAt)) {
                    $this->error('Conflict: Page has been modified by another editor.', 409);
                }
                SitemapController::generateSitemapFile();

                AgentToken::logAudit($agent['default_author_id'], 'agent_update_page', 'pages', $id, $existing, $data);

                $updatedPage = Page::findById($id);
                $this->success([
                    'page' => $updatedPage,
                    'url' => '/page/' . $updatedPage['slug']
                ], 'Page updated successfully.');
                break;

            case 'publish_page':
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') $this->error('Method not allowed.', 405);
                if (!$hasPublish) $this->forbidden('Missing scope [pages:publish]');
                $id = (int)($input['id'] ?? ($_GET['id'] ?? 0));
                if ($id <= 0) $this->error('Missing required field: id');

                $existing = Page::findById($id);
                if (!$existing) $this->error('Page not found.', 404);

                Page::update($id, [
                    'status' => 'published',
                    'published_at' => date('Y-m-d H:i:s'),
                    'changed_by' => 'Agent: ' . ($agent['token_name'] ?? 'AI')
                ]);

                SitemapController::generateSitemapFile();
                AgentToken::logAudit($agent['default_author_id'], 'agent_publish_page', 'pages', $id, ['status' => $existing['status']], ['status' => 'published']);

                $this->success(['page' => Page::findById($id)], 'Page published successfully.');
                break;

            case 'delete_page':
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') $this->error('Method not allowed.', 405);
                if (!in_array('admin', $scopes) && !$hasPublish) {
                    $this->forbidden('Missing scope [pages:publish] or [admin]');
                }
                $id = (int)($input['id'] ?? ($_GET['id'] ?? 0));
                if ($id <= 0) $this->error('Missing required field: id');

                $existing = Page::findById($id);
                if (!$existing) $this->error('Page not found.', 404);

                Page::delete($id);
                SitemapController::generateSitemapFile();
                AgentToken::logAudit($agent['default_author_id'], 'agent_delete_page', 'pages', $id, $existing, []);

                $this->success(['id' => $id], 'Page deleted successfully.');
                break;

            default:
                $this->error('Unsupported page action.');
        }
    }

    private function success($data, $message = 'Success') {
        echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function error($message, $code = 400) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function forbidden($message) {
        $this->error("Forbidden: {$message}", 403);
    }
}
