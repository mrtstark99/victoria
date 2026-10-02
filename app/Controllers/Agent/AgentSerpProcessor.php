<?php
/**
 * Agent SERP Analysis Processor
 * Actions: analyze_serp, serp_outline
 */

namespace Controllers\Agent;

use Helpers\SerpService;
use Models\AgentToken;

class AgentSerpProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasRead = in_array('admin', $scopes) || in_array('serp:read', $scopes) || in_array('seo:read', $scopes);
        if (!$hasRead) $this->forbidden('Missing scope [serp:read] or [seo:read]');

        $authorId = $agent['default_author_id'] ?? $agent['user_id'] ?? 1;

        switch ($action) {
            case 'analyze_serp':
                $keyword = trim($input['keyword'] ?? '');
                if ($keyword === '') {
                    $this->error('Missing required parameter: keyword');
                }

                $country = trim($input['country'] ?? 'vn');
                $language = trim($input['language'] ?? 'vi');
                $numResults = min(20, max(3, (int)($input['num_results'] ?? 10)));

                $serp = new SerpService();
                $result = $serp->analyzeSERP($keyword, $country, $language, $numResults);

                if (!$result['success']) {
                    $this->error($result['error'] ?? 'SERP analysis failed.');
                }

                AgentToken::logAudit($authorId, 'agent_analyze_serp', 'seo_keyword_map', null, [], [
                    'keyword' => $keyword,
                    'country' => $country,
                    'results_count' => count($result['data']['results'] ?? []),
                    'search_intent' => $result['data']['search_intent'] ?? 'unknown'
                ]);

                $this->success($result['data'], 'SERP analysis completed.');
                break;

            case 'serp_outline':
                $keyword = trim($input['keyword'] ?? '');
                if ($keyword === '') {
                    $this->error('Missing required parameter: keyword');
                }

                $country = trim($input['country'] ?? 'vn');
                $language = trim($input['language'] ?? 'vi');

                $serp = new SerpService();
                $serpResult = $serp->analyzeSERP($keyword, $country, $language, 10);

                if (!$serpResult['success']) {
                    $this->error($serpResult['error'] ?? 'SERP analysis failed.');
                }

                $outline = $serp->generateOutline($serpResult['data']);
                if (!$outline['success']) {
                    $this->error($outline['error'] ?? 'Outline generation failed.');
                }

                AgentToken::logAudit($authorId, 'agent_serp_outline', 'seo_keyword_map', null, [], [
                    'keyword' => $keyword,
                    'recommended_word_count' => $outline['data']['recommended_word_count'] ?? 0,
                    'search_intent' => $outline['data']['search_intent'] ?? 'unknown'
                ]);

                $this->success($outline['data'], 'Content outline generated from SERP analysis.');
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
