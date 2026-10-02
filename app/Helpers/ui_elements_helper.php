<?php
/** The source document is shared by API, admin snippets and skill downloads. */
function getUIElementsLibrary(): array {
    $markdown = file_get_contents(APP_ROOT . '/cms-seo-agent-skill/04_ui_elements_library.md');
    if ($markdown === false) throw new RuntimeException('UI library unavailable');
    $elements = array_map(static function ($element) {
        $element['html'] = $element['html_templates'];
        return $element;
    }, getPostElementLibrary());
    $classes = getAllowedPostElementClasses();
    return ['version' => hash('sha256', $markdown), 'elements' => $elements,
        'allowed_classes' => array_values(array_unique($classes)),
        'rules' => ['min_blocks' => 2, 'max_blocks' => 4, 'automatic_toc' => true,
            'content_headings' => ['h2', 'h3'], 'separate_blocks_with_paragraphs' => true]];
}

function aiLibraryInstructions(): string {
    return "\n\n## UI Library Contract\nRead GET /api/agent.php?action=guidelines at the start of each session. " .
        "Use data.elements_library HTML exactly; check the library version. " .
        "Use 2–4 special block instances total (including takeaway, FAQ and CTA); headings and tags do not count. " .
        "Separate special blocks with explanatory paragraphs. CMS supplies H1 and TOC: do not add either. " .
        "Do not use emoji in UI blocks. Use borders of equal width on all four sides; never emphasize one edge. " .
        "Never copy illustrative statistics, quotes or URLs as factual content without verification. " .
        "The master system prompt and UI library contract always apply.\n\n" .
        "## Execution and evidence\n" .
        "Read action=me for scopes and available actions before calling tools. Reuse successful research within the task; " .
        "do not repeatedly fetch identical data. For 401/403 stop the affected operation and report missing access; " .
        "for missing SERP configuration record the limitation and keep only a draft, never invent competitor findings. " .
        "Distinguish sourced facts from illustrative examples; do not claim personal testing or guarantees of rankings/indexing. " .
        "Answer the reader's intent first; word count is a guide, not a reason to add filler. Choose tables for comparisons, " .
        "steps for procedures, callouts for exceptions and FAQ only for unanswered questions. " .
        "Use only existing internal links and verified CTA/download destinations. " .
        "Inspect data.content_validation after create_draft/update_post, repair warnings using update_post " .
        "with expected_updated_at from the latest post. Fetch current post before editing; preserve unrelated fields. " .
        "Submit for review only when validation is valid. Complete the task only when its requested deliverable exists, " .
        "with post ID, status, evidence sources and unresolved limitations in notes. Never equate draft creation with publication.";
}

function validateAIContent(string $html): array {
    $library = getUIElementsLibrary();
    $previous = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new DOMXPath($dom);
    $roots = ['key-takeaways', 'callout', 'pros-cons-container', 'table-responsive',
        'step-cards', 'checklist-grid', 'vertical-timeline', 'quote-card', 'metrics-grid',
        'faq-accordion', 'cta-box', 'download-card'];
    $required = ['key-takeaways' => ['takeaway-list'], 'callout' => ['callout-title'],
        'pros-cons-container' => ['pros-box', 'cons-box'], 'table-responsive' => ['content-table'],
        'step-cards' => ['step-card'], 'checklist-grid' => ['check-item'],
        'vertical-timeline' => ['timeline-item'], 'quote-card' => ['quote-text', 'quote-author'],
        'metrics-grid' => ['metric-card'], 'faq-accordion' => ['faq-item', 'faq-question', 'faq-answer'],
        'cta-box' => ['cta-title'], 'download-card' => ['download-info']];
    $warnings = []; $blocks = 0;
    if (trim(strip_tags($html)) === '') $warnings[] = 'Content is empty.';
    if ($dom->getElementsByTagName('h1')->length) $warnings[] = 'CMS supplies H1; remove H1 from content.';
    foreach ($xpath->query('//*[@class]') as $node) {
        $classes = preg_split('/\s+/', trim($node->getAttribute('class')));
        foreach ($classes as $class) {
            if (!in_array($class, $library['allowed_classes'], true)) $warnings[] = "Unsupported UI class: {$class}";
            if ($class === 'table-of-contents') $warnings[] = 'CMS supplies TOC; remove manual TOC.';
            if (!in_array($class, $roots, true)) continue;
            $blocks++;
            if (preg_match('/[\x{2600}-\x{27BF}\x{1F000}-\x{1FAFF}]/u', $node->textContent))
                $warnings[] = 'UI blocks must not contain emoji.';
            foreach ($xpath->query('descendant-or-self::*[@style]', $node) as $styledNode) {
                if (preg_match('/border-(?:left|top|bottom|right)(?:-width)?\s*:/i', $styledNode->getAttribute('style')))
                    $warnings[] = 'UI block borders must have equal width on all four sides.';
            }
            foreach ($required[$class] as $child) {
                if (!$xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ' . $child . ' ")]', $node)->length)
                    $warnings[] = "{$class} requires {$child}.";
            }
            if ($class === 'faq-accordion') {
                foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " faq-item ")]', $node) as $item) {
                    if ($item->tagName !== 'details' || !$xpath->query('./summary', $item)->length)
                        $warnings[] = 'FAQ items require details with a direct summary child.';
                }
            }
            $next = $node->nextSibling;
            while ($next && !($next instanceof DOMElement)) $next = $next->nextSibling;
            if ($next && array_intersect($roots, preg_split('/\s+/', $next->getAttribute('class'))))
                $warnings[] = 'Separate adjacent UI blocks with explanatory paragraphs.';
        }
    }
    if ($blocks < 2 || $blocks > 4) $warnings[] = "Expected 2–4 UI blocks; found {$blocks}.";
    return ['valid' => !$warnings, 'block_count' => $blocks, 'library_version' => $library['version'],
        'warnings' => array_values(array_unique($warnings))];
}
