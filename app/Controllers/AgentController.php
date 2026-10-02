<?php
/**
 * AI Agent Gatekeeper Controller (MCP API Layer)
 * Enhanced: Routes for SERP analysis, internal linking, indexing, and event pipeline
 */

namespace Controllers;

use Models\AgentToken;

class AgentController {
    public function handle() {
        header('Content-Type: application/json; charset=utf-8');
        
        // Block token parameter in query strings
        if (isset($_GET['token'])) {
            $this->error(400, 'Security Constraint: Token parameters in query strings are disabled. Use Authorization Bearer headers instead.');
        }

        // Parse Bearer Token
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        $token = '';
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = trim($matches[1]);
        }

        if (!$token) {
            $this->error(401, 'Unauthorized: Bearer token is missing.');
        }

        // Hash and authenticate
        $hash = hash('sha256', $token);
        $agent = AgentToken::findByHash($hash);
        if (!$agent || !empty($agent['revoked_at'])) {
            $this->error(401, 'Unauthorized: Invalid or revoked token.');
        }

        if (!empty($agent['expires_at']) && strtotime($agent['expires_at']) < time()) {
            $this->error(401, 'Unauthorized: Token has expired.');
        }

        // IP verification
        $clientIp = getClientIP();
        if (!empty($agent['allowed_ips'])) {
            $allowed = array_map('trim', explode(',', $agent['allowed_ips']));
            if (!in_array($clientIp, $allowed, true)) {
                $this->error(403, "Forbidden: IP address {$clientIp} is not in the allowlist.");
            }
        }

        // Rate limiting
        if (!AgentToken::checkRateLimit($agent['id'], 60)) {
            $this->error(429, 'Too Many Requests: Rate limit exceeded (60 requests per minute).');
        }

        // Record API request count and client metrics
        AgentToken::recordUsage($agent['id']);

        // Idempotency check for POST
        $idemKey = $_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? $_SERVER['REDIRECT_HTTP_IDEMPOTENCY_KEY'] ?? $headers['Idempotency-Key'] ?? $headers['idempotency-key'] ?? '';
        $action = $_GET['action'] ?? '';
        $requestBody = file_get_contents('php://input');
        if ($idemKey !== '') {
            if (strlen($idemKey) > 200) $this->error(400, 'Idempotency-Key is too long.');
            $idemKey = self::idempotencyCacheKey((int)$agent['id'], $action, $idemKey, $requestBody);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idemKey !== '') {
            $cached = AgentToken::getIdempotentResponse($idemKey);
            if ($cached) {
                header('X-Cache-Lookup: HIT - Idempotent Request');
                echo $cached;
                exit;
            }

            // Register shutdown handler to save idempotent response even if processor calls exit()
            register_shutdown_function(function() use ($idemKey) {
                if (ob_get_level() > 0) {
                    $response = ob_get_contents();
                    if ($response && http_response_code() < 400) {
                        AgentToken::saveIdempotentResponse($idemKey, $response);
                    }
                }
            });
        }

        $input = json_decode($requestBody, true) ?? $_POST;
        
        $this->delegate($action, $agent, $input, $idemKey);
    }

    public static function idempotencyCacheKey(int $agentId, string $action, string $key, string $body): string {
        return hash('sha256', $agentId . "\n" . $action . "\n" . $key . "\n" . $body);
    }

    private function delegate($action, $agent, $input, $idemKey) {
        $scopes = array_map('trim', explode(',', strtolower($agent['permissions'])));
        
        try {
            ob_start();
            
            // Delegate routing
            if (in_array($action, ['me', 'whoami', 'profile', 'capabilities', 'scopes'])) {
                $authorId = $agent['default_author_id'] ?? $agent['user_id'] ?? 1;
                $author = \Models\User::findById($authorId);
                
                // Calculate allowed actions based on scopes
                $allowedActions = ['me', 'capabilities'];
                if (in_array('admin', $scopes) || in_array('posts:draft', $scopes) || in_array('posts:read', $scopes) || in_array('brand:read', $scopes) || in_array('settings:read', $scopes)) $allowedActions = array_merge($allowedActions, ['guidelines', 'get_guidelines', 'system_prompt', 'prompt']);
                if (in_array('admin', $scopes) || in_array('brand:write', $scopes) || in_array('settings:write', $scopes)) $allowedActions[] = 'update_guidelines';
                if (in_array('admin', $scopes) || in_array('brand:read', $scopes)) $allowedActions = array_merge($allowedActions, ['brand', 'get_brand', 'default_seo', 'get_default_seo']);
                if (in_array('admin', $scopes) || in_array('brand:write', $scopes)) $allowedActions = array_merge($allowedActions, ['update_brand', 'update_default_seo']);
                if (in_array('admin', $scopes) || in_array('category:read', $scopes) || in_array('posts:read', $scopes)) $allowedActions[] = 'categories';
                if (in_array('admin', $scopes) || in_array('category:write', $scopes)) $allowedActions = array_merge($allowedActions, ['create_category', 'update_category', 'delete_category']);
                if (in_array('admin', $scopes) || in_array('seo:read', $scopes)) $allowedActions[] = 'seo';
                if (in_array('admin', $scopes) || in_array('seo:write', $scopes)) $allowedActions = array_merge($allowedActions, ['create_keyword', 'update_keyword', 'delete_keyword']);
                if (in_array('admin', $scopes) || in_array('analytics:read', $scopes)) $allowedActions = array_merge($allowedActions, ['analytics', 'page_performance', 'opportunities']);
                if (in_array('admin', $scopes) || in_array('posts:read', $scopes)) $allowedActions = array_merge($allowedActions, ['posts', 'list_revisions']);
                if (in_array('admin', $scopes) || in_array('posts:draft', $scopes)) $allowedActions = array_merge($allowedActions, ['create_draft', 'update_post', 'submit_for_review', 'restore_revision', 'process_draft', 'validate_post', 'upload_image']);
                if (in_array('admin', $scopes) || in_array('posts:publish', $scopes)) $allowedActions = array_merge($allowedActions, ['approve_post', 'publish_post']);
                if (in_array('admin', $scopes) || in_array('tasks:read', $scopes) || in_array('posts:read', $scopes) || in_array('posts:draft', $scopes)) $allowedActions = array_merge($allowedActions, ['tasks', 'list_tasks', 'get_tasks']);
                if (in_array('admin', $scopes) || in_array('tasks:write', $scopes) || in_array('posts:draft', $scopes)) $allowedActions = array_merge($allowedActions, ['create_task', 'create_adhoc_task', 'update_task', 'complete_task', 'delete_task']);
                // ── New SEO Enhancement Actions ──
                if (in_array('admin', $scopes) || in_array('serp:read', $scopes) || in_array('seo:read', $scopes)) $allowedActions = array_merge($allowedActions, ['analyze_serp', 'serp_outline']);
                if (in_array('admin', $scopes) || in_array('posts:read', $scopes)) $allowedActions = array_merge($allowedActions, ['link_suggestions', 'backlink_candidates', 'link_audit']);
                if (in_array('admin', $scopes) || in_array('seo:write', $scopes) || in_array('posts:publish', $scopes)) $allowedActions = array_merge($allowedActions, ['ping_index', 'check_index_status', 'regenerate_sitemap']);
                if (in_array('admin', $scopes) || in_array('tasks:read', $scopes)) $allowedActions = array_merge($allowedActions, ['list_hooks', 'event_log']);
                if (in_array('admin', $scopes) || in_array('tasks:write', $scopes)) $allowedActions = array_merge($allowedActions, ['create_hook', 'update_hook']);
                // ── Static Pages Actions ──
                if (in_array('admin', $scopes) || in_array('pages:read', $scopes) || in_array('posts:read', $scopes)) $allowedActions = array_merge($allowedActions, ['pages', 'list_pages', 'get_page']);
                if (in_array('admin', $scopes) || in_array('pages:draft', $scopes) || in_array('posts:draft', $scopes)) $allowedActions = array_merge($allowedActions, ['create_page', 'create_page_draft', 'update_page']);
                if (in_array('admin', $scopes) || in_array('pages:publish', $scopes) || in_array('posts:publish', $scopes)) $allowedActions = array_merge($allowedActions, ['publish_page', 'delete_page']);
                if (in_array('admin', $scopes) || in_array('services:read', $scopes)) $allowedActions = array_merge($allowedActions, ['services', 'list_services', 'get_service']);
                if (in_array('admin', $scopes) || in_array('services:write', $scopes)) $allowedActions = array_merge($allowedActions, ['create_service', 'update_service']);
                if (in_array('admin', $scopes) || in_array('services:publish', $scopes)) $allowedActions = array_merge($allowedActions, ['activate_service', 'deactivate_service']);
                if (in_array('admin', $scopes) || in_array('services:delete', $scopes)) $allowedActions[] = 'delete_service';
                if (in_array('admin', $scopes) || in_array('cost:read', $scopes)) $allowedActions[] = 'get_cost_page';
                if (in_array('admin', $scopes) || in_array('cost:write', $scopes)) $allowedActions[] = 'update_cost_page';
                if (in_array('admin', $scopes) || in_array('contacts:read', $scopes)) $allowedActions = array_merge($allowedActions, ['contacts', 'get_contact']);
                if (in_array('admin', $scopes) || in_array('contacts:write', $scopes)) $allowedActions[] = 'update_contact';
                if (in_array('admin', $scopes) || in_array('contacts:delete', $scopes)) $allowedActions[] = 'delete_contact';
                if (in_array('admin', $scopes) || in_array('calculator:read', $scopes)) $allowedActions[] = 'get_home_calculator';
                if (in_array('admin', $scopes) || in_array('calculator:write', $scopes)) $allowedActions[] = 'update_home_calculator';
                if (in_array('admin', $scopes) || in_array('homepage:read', $scopes)) $allowedActions[] = 'get_homepage';
                if (in_array('admin', $scopes) || in_array('homepage:write', $scopes)) $allowedActions[] = 'update_homepage';
                if (in_array('admin', $scopes) || in_array('profile:read', $scopes)) $allowedActions[] = 'get_author_profile';
                if (in_array('admin', $scopes) || in_array('profile:write', $scopes)) $allowedActions[] = 'update_author_profile';
                if (in_array('admin', $scopes) || in_array('sidebar:read', $scopes)) $allowedActions[] = 'get_post_sidebar';
                if (in_array('admin', $scopes) || in_array('sidebar:write', $scopes)) $allowedActions[] = 'update_post_sidebar';
                if (in_array('admin', $scopes) || in_array('analytics:settings:read', $scopes)) $allowedActions[] = 'get_analytics_settings';
                if (in_array('admin', $scopes) || in_array('analytics:settings:write', $scopes)) $allowedActions[] = 'update_analytics_settings';
                if (in_array('admin', $scopes) || in_array('fixed_pages:read', $scopes)) $allowedActions[] = 'get_fixed_page';
                if (in_array('admin', $scopes) || in_array('fixed_pages:write', $scopes)) $allowedActions = array_merge($allowedActions, ['update_fixed_page', 'reset_fixed_page']);
                if (in_array('admin', $scopes) || in_array('seo:read', $scopes)) $allowedActions = array_merge($allowedActions, ['list_clusters', 'get_cluster']);
                if (in_array('admin', $scopes) || in_array('seo:write', $scopes)) $allowedActions = array_merge($allowedActions, ['create_cluster', 'update_cluster', 'delete_cluster']);
                // ── Navigation & Footer Actions ──
                if (in_array('admin', $scopes) || in_array('navigation:read', $scopes) || in_array('brand:read', $scopes) || in_array('settings:read', $scopes)) $allowedActions = array_merge($allowedActions, ['get_navigation', 'navigation', 'get_menus', 'get_footer', 'footer']);
                if (in_array('admin', $scopes) || in_array('navigation:write', $scopes) || in_array('brand:write', $scopes) || in_array('settings:write', $scopes)) $allowedActions = array_merge($allowedActions, ['update_navigation', 'update_menus', 'update_footer']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Agent identity and capabilities retrieved.',
                    'data' => [
                        'token_name' => $agent['token_name'],
                        'default_author' => $author['full_name'] ?? 'Admin',
                        'scopes' => $scopes,
                        'allowed_actions' => array_values(array_unique($allowedActions)),
                        'rate_limit' => '60 requests per minute',
                        'review_enforcement' => defined('AGENT_REQUIRE_REVIEW_BEFORE_PUBLISH') ? AGENT_REQUIRE_REVIEW_BEFORE_PUBLISH : false,
                        'indexnow_enabled' => defined('INDEXNOW_ENABLED') ? INDEXNOW_ENABLED : false,
                        'created_at' => $agent['created_at']
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            } elseif (in_array($action, ['guidelines', 'get_guidelines', 'system_prompt', 'get_system_prompt', 'prompt', 'update_guidelines'])) {
                (new Agent\AgentGuidelinesProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['tasks', 'list_tasks', 'get_tasks', 'create_task', 'create_adhoc_task', 'update_task', 'complete_task', 'delete_task'])) {
                (new Agent\AgentTaskProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['seo', 'create_keyword', 'update_keyword', 'delete_keyword'])) {
                (new Agent\AgentSEOProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['brand', 'get_brand', 'update_brand', 'default_seo', 'get_default_seo', 'update_default_seo'])) {
                (new Agent\AgentBrandProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['categories', 'list_categories', 'create_category', 'update_category', 'delete_category'])) {
                (new Agent\AgentCategoryProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['analytics', 'page_performance', 'opportunities'])) {
                (new Agent\AgentAnalyticsProcessor())->process($action, $scopes);
            } elseif (in_array($action, ['posts', 'create_draft', 'update_post', 'submit_for_review', 'approve_post', 'publish_post', 'list_revisions', 'restore_revision'])) {
                (new Agent\AgentPostProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['process_draft', 'validate_post', 'upload_image'])) {
                (new Agent\AgentContentProcessor())->process($action, $scopes, $agent, $input);
            // ── Static Pages Routes ──
            } elseif (in_array($action, ['pages', 'list_pages', 'get_page', 'create_page', 'create_page_draft', 'update_page', 'delete_page', 'publish_page'])) {
                (new Agent\AgentPageProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['services', 'list_services', 'get_service', 'create_service', 'update_service', 'activate_service', 'deactivate_service', 'delete_service'])) {
                (new Agent\AgentServiceProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['get_cost_page', 'update_cost_page'])) {
                (new Agent\AgentCostProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['contacts', 'get_contact', 'update_contact', 'delete_contact'])) {
                (new Agent\AgentContactProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['get_homepage', 'update_homepage'])) {
                (new Agent\AgentHomePageProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['get_author_profile', 'update_author_profile'])) {
                (new Agent\AgentAuthorProfileProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['get_home_calculator', 'update_home_calculator', 'get_post_sidebar', 'update_post_sidebar', 'get_analytics_settings', 'update_analytics_settings'])) {
                (new Agent\AgentSettingsProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['get_fixed_page', 'update_fixed_page', 'reset_fixed_page'])) {
                (new Agent\AgentFixedPageProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['list_clusters', 'get_cluster', 'create_cluster', 'update_cluster', 'delete_cluster'])) {
                (new Agent\AgentClusterProcessor())->process($action, $scopes, $agent, $input);
            // ── Navigation & Footer Routes ──
            } elseif (in_array($action, ['get_navigation', 'navigation', 'get_menus', 'update_navigation', 'update_menus', 'get_footer', 'footer', 'update_footer'])) {
                (new Agent\AgentNavigationProcessor())->process($action, $scopes, $agent, $input);
            // ── New SEO Enhancement Routes ──
            } elseif (in_array($action, ['analyze_serp', 'serp_outline'])) {
                (new Agent\AgentSerpProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['link_suggestions', 'backlink_candidates', 'link_audit'])) {
                (new Agent\AgentLinkingProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['ping_index', 'check_index_status', 'regenerate_sitemap'])) {
                (new Agent\AgentIndexingProcessor())->process($action, $scopes, $agent, $input);
            } elseif (in_array($action, ['list_hooks', 'create_hook', 'update_hook', 'event_log'])) {
                $this->processEventActions($action, $scopes, $agent, $input);
            } else {
                $this->error(400, 'Invalid action parameter.');
            }

            $response = ob_get_clean();
            
            // Save idempotency key
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idemKey !== '' && http_response_code() < 400) {
                AgentToken::saveIdempotentResponse($idemKey, $response);
            }
            echo $response;

        } catch (\Throwable $e) {
            if (ob_get_level() > 0) ob_end_clean();
            error_log('Agent API error: ' . get_class($e) . ': ' . $e->getMessage());
            $this->error(500, 'Internal Server Error');
        }
    }

    /**
     * Handle event pipeline management actions
     */
    private function processEventActions($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || in_array('tasks:read', $scopes);
        $hasWrite = in_array('admin', $scopes) || in_array('tasks:write', $scopes);

        switch ($action) {
            case 'list_hooks':
                if (!$hasRead) $this->error(403, 'Forbidden: Missing scope [tasks:read]');
                $eventFilter = $_GET['event_name'] ?? ($input['event_name'] ?? null);
                $hooks = \Helpers\EventDispatcher::getHooks($eventFilter);
                echo json_encode(['success' => true, 'data' => $hooks], JSON_UNESCAPED_UNICODE);
                exit;

            case 'create_hook':
                if (!$hasWrite) $this->error(403, 'Forbidden: Missing scope [tasks:write]');
                $eventName = trim($input['event_name'] ?? '');
                $hookAction = trim($input['hook_action'] ?? '');
                if ($eventName === '' || $hookAction === '') {
                    $this->error(400, 'Missing required: event_name, hook_action');
                }
                $hookConfig = $input['hook_config'] ?? [];
                $enabled = (bool)($input['is_enabled'] ?? true);
                $id = \Helpers\EventDispatcher::createHook($eventName, $hookAction, $hookConfig, $enabled);
                AgentToken::logAudit($agent['default_author_id'] ?? 1, 'agent_create_hook', 'agent_event_hooks', $id, [], ['event_name' => $eventName, 'hook_action' => $hookAction]);
                echo json_encode(['success' => true, 'data' => ['id' => $id, 'event_name' => $eventName, 'hook_action' => $hookAction], 'message' => 'Event hook created.'], JSON_UNESCAPED_UNICODE);
                exit;

            case 'update_hook':
                if (!$hasWrite) $this->error(403, 'Forbidden: Missing scope [tasks:write]');
                $hookId = (int)($input['id'] ?? 0);
                if ($hookId <= 0) $this->error(400, 'Missing required: id');
                $fields = [];
                if (isset($input['event_name'])) $fields['event_name'] = $input['event_name'];
                if (isset($input['hook_action'])) $fields['hook_action'] = $input['hook_action'];
                if (isset($input['hook_config'])) $fields['hook_config'] = $input['hook_config'];
                if (isset($input['is_enabled'])) $fields['is_enabled'] = (int)(bool)$input['is_enabled'];
                \Helpers\EventDispatcher::updateHook($hookId, $fields);
                echo json_encode(['success' => true, 'data' => ['id' => $hookId], 'message' => 'Event hook updated.'], JSON_UNESCAPED_UNICODE);
                exit;

            case 'event_log':
                if (!$hasRead) $this->error(403, 'Forbidden: Missing scope [tasks:read]');
                $limit = min(100, max(1, (int)($_GET['limit'] ?? ($input['limit'] ?? 50))));
                $eventFilter = $_GET['event_name'] ?? ($input['event_name'] ?? null);
                $log = \Helpers\EventDispatcher::getEventLog($limit, $eventFilter);
                echo json_encode(['success' => true, 'data' => $log], JSON_UNESCAPED_UNICODE);
                exit;
        }
    }

    public function error($code, $message) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
