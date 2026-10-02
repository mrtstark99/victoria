<?php
/**
 * Agent Guidelines & Master System Prompt API Processor
 */

namespace Controllers\Agent;

use Models\AgentToken;

class AgentGuidelinesProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || 
                   in_array('posts:read', $scopes) || 
                   in_array('posts:draft', $scopes) || 
                   in_array('brand:read', $scopes) || 
                   in_array('settings:read', $scopes);

        $hasWrite = in_array('admin', $scopes) || 
                    in_array('brand:write', $scopes) || 
                    in_array('settings:write', $scopes);

        switch ($action) {
            case 'guidelines':
            case 'get_guidelines':
                if (!$hasRead) {
                    $this->forbidden('Missing scope [posts:read], [posts:draft], [brand:read], or [settings:read]');
                }

                $guidelines = getAIGuidelines();
                $generatedPrompt = buildAISystemPrompt($guidelines);
                $data = [
                    'enabled' => true,
                    'mode' => $guidelines['ai_prompt_mode'] ?? 'auto',
                    'system_prompt' => $generatedPrompt,
                    'library_version' => getUIElementsLibrary()['version'],
                    'ui_rules' => getUIElementsLibrary()['rules'],
                    'elements_library' => getPostElementLibrary(),
                    'guidelines' => [
                        'css_components' => [
                            'enabled' => true,
                            'supported_elements' => array_column(getPostElementLibrary(), 'id'),
                        ],
                        'content_format' => 'raw_html',
                        'unknown_classes' => 'rejected',
                        'validation_failure_status' => 422,
                    ]
                ];

                $this->success($data, 'AI Content Guidelines & Master System Prompt retrieved successfully.');
                break;

            case 'system_prompt':
            case 'get_system_prompt':
            case 'prompt':
                if (!$hasRead) {
                    $this->forbidden('Missing scope [posts:read], [posts:draft], [brand:read], or [settings:read]');
                }

                $guidelines = getAIGuidelines();
                $generatedPrompt = buildAISystemPrompt($guidelines);
                $data = [
                    'enabled' => true,
                    'mode' => $guidelines['ai_prompt_mode'] ?? 'auto',
                    'system_prompt' => $generatedPrompt,
                    'library_version' => getUIElementsLibrary()['version'],
                    'ui_rules' => getUIElementsLibrary()['rules'],
                    'elements_library' => getPostElementLibrary()
                ];

                $this->success($data, 'Master System Prompt retrieved successfully.');
                break;

            case 'update_guidelines':
                if (!$hasWrite) {
                    $this->forbidden('Missing scope [brand:write] or [settings:write]');
                }

                if (empty($input) || !is_array($input)) {
                    $this->error('Payload must be a JSON object containing guidelines settings to update.');
                }

                $errors = [];
                $saved = saveAIGuidelines($input, $errors);

                if (!$saved) {
                    $this->error('Validation Failed: ' . implode(' | ', $errors), 422);
                }

                $authorId = $agent['user_id'] ?? 0;
                AgentToken::logAudit($authorId, 'agent_update_guidelines', 'settings', null, [], $input);

                $updatedGuidelines = getAIGuidelines();
                $generatedPrompt = buildAISystemPrompt($updatedGuidelines);
                $this->success([
                    'enabled' => true,
                    'mode' => $updatedGuidelines['ai_prompt_mode'] ?? 'auto',
                    'system_prompt' => $generatedPrompt,
                    'library_version' => getUIElementsLibrary()['version'],
                    'ui_rules' => getUIElementsLibrary()['rules'],
                    'elements_library' => getPostElementLibrary()
                ], 'AI guidelines updated successfully.');
                break;

            default:
                $this->error('Unsupported guidelines action.');
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

    private function forbidden($message = 'Forbidden') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
