<?php

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$db = Database::getInstance();
$db->beginTransaction();

try {
    $obsoleteKeys = [
        'ai_anti_hallucination_rule', 'ai_article_outline_model', 'ai_css_custom_guidelines',
        'ai_css_guidelines_enabled',
        'ai_css_supported_elements', 'ai_css_supported_list', 'ai_cta_content',
        'ai_forbidden_words', 'ai_heading_structure', 'ai_image_alt_rule',
        'ai_include_cta', 'ai_include_faq', 'ai_include_table', 'ai_include_tldr',
        'ai_internal_links_rule', 'ai_keyword_density', 'ai_max_words', 'ai_min_words',
        'ai_pronoun_custom', 'ai_pronoun_perspective', 'ai_secondary_keywords_rule',
        'ai_target_audience', 'ai_writing_style_elements', 'ai_writing_tone',
        'ai_writing_tone_custom'
    ];
    $placeholders = implode(',', array_fill(0, count($obsoleteKeys), '?'));
    $db->prepare("DELETE FROM settings WHERE setting_key IN ({$placeholders})")->execute($obsoleteKeys);

    $upsert = $db->prepare("
        INSERT INTO settings (setting_key, setting_value, setting_type, description, updated_at)
        VALUES (?, ?, 'text', ?, datetime('now', 'localtime'))
        ON CONFLICT(setting_key) DO UPDATE SET
            setting_value = excluded.setting_value,
            description = excluded.description,
            updated_at = excluded.updated_at
    ");
    $upsert->execute(['ai_guidelines_enabled', '1', 'AI writing guidelines are always enabled']);
    $upsert->execute(['ai_prompt_mode', 'custom', 'Use the CMS-managed master prompt']);
    $upsert->execute(['ai_custom_system_prompt', getDefaultMasterPrompt(), 'CMS-managed master prompt']);

    $db->commit();
    echo "Post element contract settings migrated successfully.\n";
} catch (Throwable $error) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(1);
}
