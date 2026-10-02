<?php
/**
 * Admin Agent Controller
 * Handles AI Agent token management, task planning, and guidelines.
 */

namespace Controllers;

use Models\AgentToken;
use Models\AgentTask;
use Models\User;
use Controllers\Agent\AgentActionHandler;

class AdminAgentController {

    public function manageAgent() {
        requireAdmin();
        $errors = [];
        $newToken = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = AgentActionHandler::handle();
        }

        // Fetch Tokens & Authors
        $tokens = AgentToken::getActiveTokens();
        $authors = User::getAll();

        // Fetch Agent Tasks & Filters
        $taskFilters = [];
        if (!empty($_GET['task_status']) && in_array($_GET['task_status'], ['pending', 'completed'], true)) {
            $taskFilters['status'] = $_GET['task_status'];
        }
        if (!empty($_GET['task_priority']) && in_array($_GET['task_priority'], ['urgent', 'high', 'medium', 'low'], true)) {
            $taskFilters['priority'] = $_GET['task_priority'];
        }
        if (!empty($_GET['task_category']) && $_GET['task_category'] !== 'all') {
            $taskFilters['category'] = $_GET['task_category'];
        }
        if (!empty($_GET['task_search'])) {
            $taskFilters['search'] = trim($_GET['task_search']);
        }

        $tasks = AgentTask::getAll();
        $taskStats = AgentTask::getStats();
        $taskCategories = AgentTask::getCategories();

        // Fetch Activities
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $totalActivities = AgentToken::countActivities();
        $activities = AgentToken::getRecentActivities($page, $perPage);

        // Fetch Guidelines & System Prompt
        $guidelines = getAIGuidelines();
        $generatedSystemPrompt = buildAISystemPrompt($guidelines);
        $systemPromptPreview = $generatedSystemPrompt;

        $skillDocs = $this->loadSkillDocs();

        if (isset($_SESSION['new_agent_raw'])) {
            $newToken = [
                'raw' => $_SESSION['new_agent_raw'],
                'name' => $_SESSION['new_agent_name']
            ];
            unset($_SESSION['new_agent_raw'], $_SESSION['new_agent_name']);
        }

        view('admin/agent', [
            'tokens' => $tokens,
            'authors' => $authors,
            'tasks' => $tasks,
            'task_stats' => $taskStats,
            'task_categories' => $taskCategories,
            'task_filters' => $taskFilters,
            'activities' => $activities,
            'total_activities' => $totalActivities,
            'current_page' => $page,
            'per_page' => $perPage,
            'guidelines' => $guidelines,
            'generated_prompt' => $generatedSystemPrompt,
            'system_prompt_preview' => $systemPromptPreview,
            'skill_docs' => $skillDocs,
            'new_token' => $newToken,
            'errors' => $errors,
            'page_title' => 'Cấu hình AI Agent'
        ]);
    }

    private function loadSkillDocs(): array {
        $skillDir = APP_ROOT . '/cms-seo-agent-skill';
        if (!is_dir($skillDir)) {
            $skillDir = APP_ROOT . '/public/uploads/skill';
        }
        $skillDocs = [];
        $orderedFiles = [
            'SKILL.md' => ['title' => 'SKILL.md — Cấu hình & Metadata Workspace Agent', 'icon' => '📦', 'badge' => 'Entry Point'],
            'AGENT.md' => ['title' => 'AGENT.md — Hướng Dẫn & Ngữ Cảnh Hoạt Động Cốt Lõi', 'icon' => '🤖', 'badge' => 'Context'],
            '01_authentication_and_api.md' => ['title' => '01. Authentication & API Gateway — Xác Thực & Endpoint', 'icon' => '🔑', 'badge' => 'Security'],
            '02_workflow_and_task_lifecycle.md' => ['title' => '02. Workflow & Task Lifecycle — Vòng Đời Tác Vụ', 'icon' => '🔄', 'badge' => 'Workflow'],
            '03_ai_content_writer_prompt.md' => ['title' => '03. AI Content Writer Prompt — Master Prompt Chuẩn E-E-A-T', 'icon' => '✍️', 'badge' => 'E-E-A-T'],
            '04_ui_elements_library.md' => ['title' => '04. UI Elements Library — 13 Nhóm Giao Diện Bài Viết', 'icon' => '🎨', 'badge' => 'UI Kit'],
            '05_serp_and_intent_analysis.md' => ['title' => '05. SERP & Intent Analysis — Soi Top 10 SERP & Khóa Intent', 'icon' => '🎯', 'badge' => 'SERP'],
            '06_internal_linking_and_seo_schema.md' => ['title' => '06. Internal Linking & Schema — Liên Kết Nội Bộ & Schema JSON-LD', 'icon' => '🔗', 'badge' => 'SEO'],
            '07_indexing_and_automation.md' => ['title' => '07. Indexing & Automation — Google Indexing & Sitemap Ping', 'icon' => '⚡', 'badge' => 'Indexing'],
            '08_examples_and_payloads.md' => ['title' => '08. Examples & Payloads — Mẫu JSON Request & Response Thực Tế', 'icon' => '📋', 'badge' => 'Payloads'],
        ];

        if (is_dir($skillDir)) {
            foreach ($orderedFiles as $fileKey => $meta) {
                $filePath = $skillDir . '/' . $fileKey;
                if (file_exists($filePath)) {
                    $raw = file_get_contents($filePath);
                    $skillDocs[$fileKey] = [
                        'filename' => $fileKey,
                        'title' => $meta['title'],
                        'icon' => $meta['icon'],
                        'badge' => $meta['badge'],
                        'content' => compileSkillTemplate($raw)
                    ];
                }
            }
        }

        return $skillDocs;
    }
}
