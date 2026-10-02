<?php
/**
 * Admin Agent Manual Download Controller
 * Handles: Skill ZIP download and single Markdown download.
 * Extracted from AdminAgentController.
 */

namespace Controllers;

class AdminAgentManualController {

    public function download() {
        requireAdmin();

        $format   = $_GET['format'] ?? $_GET['type'] ?? 'zip';
        $skillDir = APP_ROOT . '/cms-seo-agent-skill';
        if (!is_dir($skillDir)) {
            $skillDir = APP_ROOT . '/public/uploads/skill';
        }

        if ($format === 'zip') {
            $zipFiles = [];
            if (is_dir($skillDir)) {
                $files = scandir($skillDir);
                foreach ($files as $f) {
                    if ($f === '.' || $f === '..' || str_starts_with($f, '.') || !str_ends_with(strtolower($f), '.md')) continue;
                    $fullPath = $skillDir . '/' . $f;
                    if (is_file($fullPath)) {
                        $raw = file_get_contents($fullPath);
                        $zipFiles[$f] = compileSkillTemplate($raw);
                    }
                }

            }

            $config = getAIGuidelines();
            $prompt = buildAISystemPrompt($config);
            $zipFiles['03_ai_content_writer_prompt.md'] = "# Current Master System Prompt\n\n" . $prompt;

            if (!empty($zipFiles)) {
                $zipBinary = $this->buildZip($zipFiles);

                if ($zipBinary !== '') {
                    $cacheZipPath = APP_ROOT . '/public/uploads/cms_seo_agent_skill.zip';
                    @file_put_contents($cacheZipPath, $zipBinary);

                    $uploadSkillDir = APP_ROOT . '/public/uploads/skill';
                    if (!is_dir($uploadSkillDir)) @mkdir($uploadSkillDir, 0777, true);
                    foreach ($zipFiles as $fileName => $fileContent) {
                        @file_put_contents($uploadSkillDir . '/' . $fileName, $fileContent);
                    }

                    while (ob_get_level() > 0) ob_end_clean();

                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="cms_seo_agent_skill.zip"');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . strlen($zipBinary));
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    echo $zipBinary;
                    exit;
                }
            }
        }

        // Fallback: single Markdown download
        $filePath = APP_ROOT . '/app/Resources/agent_manual.md';
        if (!file_exists($filePath)) {
            $filePath = APP_ROOT . '/cms-seo-agent-skill/SKILL.md';
        }
        if (!file_exists($filePath)) {
            header('HTTP/1.0 404 Not Found');
            die('Không tìm thấy tệp hướng dẫn.');
        }

        $content = compileSkillTemplate(file_get_contents($filePath));
        header('Content-Type: text/markdown; charset=utf-8');
        header('Content-Disposition: attachment; filename="ai_agent_integration_manual.md"');
        echo $content;
        exit;
    }

    private function buildZip(array $files): string {
        // Try native ZipArchive
        if (class_exists('\ZipArchive')) {
            $tmpZip = tempnam(sys_get_temp_dir(), 'agent_skill_');
            $zip    = new \ZipArchive();
            if ($zip->open($tmpZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($files as $name => $content) $zip->addFromString($name, $content);
                $zip->close();
                if (file_exists($tmpZip) && filesize($tmpZip) > 0) {
                    $binary = file_get_contents($tmpZip);
                    @unlink($tmpZip);
                    return $binary;
                }
            }
        }

        // Fallback: SimpleZip
        if (class_exists('\SimpleZip')) {
            $simpleZip = new \SimpleZip();
            foreach ($files as $name => $content) $simpleZip->addFile($name, $content);
            return $simpleZip->build();
        }

        return '';
    }
}
