<?php
/**
 * Agent Category Management Processor
 */

namespace Controllers\Agent;

use Models\Category;
use Models\AgentToken;

class AgentCategoryProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || in_array('category:read', $scopes) || in_array('posts:read', $scopes);
        $hasWrite = in_array('admin', $scopes) || in_array('category:write', $scopes);
        $authorId = $agent['default_author_id'] ?? $agent['user_id'] ?? 1;

        switch ($action) {
            case 'categories':
            case 'list_categories':
                if (!$hasRead) $this->forbidden('Missing scope [category:read]');
                $categories = Category::getAll();
                $this->success($categories, 'Categories retrieved successfully.');
                break;

            case 'create_category':
                if (!$hasWrite) $this->forbidden('Missing scope [category:write]');
                $name = trim($input['name'] ?? '');
                $slug = trim($input['slug'] ?? '');
                $description = trim($input['description'] ?? '');

                if ($name === '') {
                    $this->error('Category name is required.');
                }

                if ($slug === '') {
                    $slug = createSlug($name);
                }

                $existing = Category::findBySlug($slug);
                if ($existing) {
                    $this->error('A category with this slug already exists.');
                }

                Category::create($name, $slug, $description);
                $newCat = Category::findBySlug($slug);

                AgentToken::logAudit($authorId, 'agent_create_category', 'categories', $newCat['id'] ?? null, [], ['name' => $name, 'slug' => $slug]);
                $this->success($newCat, 'Category created successfully.');
                break;

            case 'update_category':
                if (!$hasWrite) $this->forbidden('Missing scope [category:write]');
                $id = (int)($input['id'] ?? 0);
                $cat = Category::findById($id);
                if (!$cat) {
                    $this->error('Category not found.', 404);
                }

                $name = isset($input['name']) ? trim($input['name']) : $cat['name'];
                $slug = isset($input['slug']) ? trim($input['slug']) : $cat['slug'];
                $description = isset($input['description']) ? trim($input['description']) : ($cat['description'] ?? '');

                if ($name === '') {
                    $this->error('Category name cannot be empty.');
                }

                if ($slug === '') {
                    $slug = createSlug($name);
                }

                // Check slug conflict with other categories
                $slugCheck = Category::findBySlug($slug);
                if ($slugCheck && (int)$slugCheck['id'] !== $id) {
                    $this->error('Another category with this slug already exists.');
                }

                Category::update($id, $name, $slug, $description);
                $updatedCat = Category::findById($id);

                $changedOld = [];
                $changedNew = [];
                foreach (['name', 'slug', 'description'] as $k) {
                    if (($cat[$k] ?? '') !== ($updatedCat[$k] ?? '')) {
                        $changedOld[$k] = $cat[$k] ?? '';
                        $changedNew[$k] = $updatedCat[$k] ?? '';
                    }
                }
                if (empty($changedNew)) {
                    $changedOld = ['name' => $cat['name']];
                    $changedNew = ['name' => $updatedCat['name']];
                }

                AgentToken::logAudit($authorId, 'agent_update_category', 'categories', $id, $changedOld, $changedNew);
                $this->success($updatedCat, 'Category updated successfully.');
                break;

            case 'delete_category':
                if (!$hasWrite) $this->forbidden('Missing scope [category:write]');
                $id = (int)($input['id'] ?? 0);
                $cat = Category::findById($id);
                if (!$cat) {
                    $this->error('Category not found.', 404);
                }

                Category::delete($id);
                AgentToken::logAudit($authorId, 'agent_delete_category', 'categories', $id, $cat, ['deleted' => true, 'name' => $cat['name']]);
                $this->success(['id' => $id, 'deleted' => true], 'Category deleted successfully.');
                break;

            default:
                $this->error('Unsupported category action.');
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
