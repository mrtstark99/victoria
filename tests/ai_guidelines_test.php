<?php
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/app/Helpers/post_element_library.php';
require APP_ROOT . '/app/Helpers/ui_elements_helper.php';
require APP_ROOT . '/app/Helpers/ai_guidelines_helper.php';
function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$library = getUIElementsLibrary();
check(count($library['elements']) === 13, 'All documented component groups are exposed');
check(count($library['allowed_classes']) === 64, 'All library classes are exposed');
$html = '<h2>Guide</h2><div class="callout callout-tip"><div class="callout-title">Tip</div><p>Advice</p></div>' .
    '<p>Explanation</p><div class="table-responsive"><table class="content-table"><tr><td>Comparison</td></tr></table></div>';
check(validateAIContent($html)['valid'], 'Valid article accepted');
check(!validateAIContent(str_replace('callout-title', 'invented-title', $html))['valid'], 'Unknown classes and missing children detected');
check(!validateAIContent(str_replace('<p>Explanation</p>', '', $html))['valid'], 'Adjacent blocks detected');
check(!validateAIContent('<h1>Duplicate</h1>' . $html)['valid'], 'Duplicate H1 detected');
check(validateAIContent(str_repeat($html, 3))['block_count'] === 6, 'Count instances rather than distinct block types');
check(!validateAIContent(str_repeat($html, 3))['valid'], 'Excessive blocks detected');
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE settings (setting_key TEXT PRIMARY KEY, setting_value TEXT, updated_at TEXT)');
$errors = [];
saveAIGuidelines(['ai_custom_system_prompt' => 'Original', 'ai_guidelines_enabled' => true], $errors, $db);
saveAIGuidelines(['ai_guidelines_enabled' => 'false'], $errors, $db);
$saved = getAIGuidelines($db);
check($saved['ai_guidelines_enabled'] === '1', 'Prompt remains enabled after legacy false input');
check($saved['ai_custom_system_prompt'] === 'Original', 'Partial update preserves prompt');
saveAIGuidelines(['ai_custom_system_prompt' => 'Updated'], $errors, $db);
check(getAIGuidelines($db)['ai_guidelines_enabled'] === '1', 'Partial prompt update preserves required state');
check(str_contains(buildAISystemPrompt(getAIGuidelines($db)), 'data.elements_library'), 'Active prompt includes runtime library contract');
echo "AI guidelines checks passed\n";
