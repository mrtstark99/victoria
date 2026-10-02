<?php

/**
 * Canonical UI element contract for AI-authored post content.
 *
 * The public skill document is the single source of truth. Both the Agent API
 * and post-content validator consume the same parsed contract, so documented
 * HTML and accepted CSS classes cannot drift independently.
 */

if (!function_exists('getPostElementLibrary')) {
    function getPostElementLibrary(): array {
        static $library = null;
        if ($library !== null) {
            return $library;
        }

        $source = APP_ROOT . '/cms-seo-agent-skill/04_ui_elements_library.md';
        if (!is_file($source)) {
            throw new RuntimeException('Canonical post UI element library is missing.');
        }

        $markdown = file_get_contents($source);
        preg_match_all('/^###\s+(\d+)\.\s+(.+?)\R(.*?)(?=^###\s+\d+\.|\z)/msu', $markdown, $sections, PREG_SET_ORDER);

        $elements = [];
        foreach ($sections as $section) {
            preg_match_all('/```html\R(.*?)```/su', $section[3], $blocks);
            $templates = array_values(array_filter(array_map('trim', $blocks[1] ?? [])));
            if (!$templates) {
                continue;
            }

            $classes = [];
            foreach ($templates as $template) {
                preg_match_all('/\bclass\s*=\s*["\']([^"\']+)["\']/iu', $template, $matches);
                foreach ($matches[1] as $classList) {
                    foreach (preg_split('/\s+/', trim($classList)) as $className) {
                        if ($className !== '') {
                            $classes[$className] = true;
                        }
                    }
                }
            }

            $elements[] = [
                'id' => str_pad($section[1], 2, '0', STR_PAD_LEFT),
                'name' => trim(preg_replace('/\s*\([^)]*\)\s*$/u', '', $section[2])),
                'classes' => array_keys($classes),
                'html_templates' => $templates,
            ];
        }

        if (!$elements) {
            throw new RuntimeException('Canonical post UI element library could not be parsed.');
        }

        return $library = $elements;
    }
}

if (!function_exists('getAllowedPostElementClasses')) {
    function getAllowedPostElementClasses(): array {
        $allowed = [];
        foreach (getPostElementLibrary() as $element) {
            foreach ($element['classes'] as $className) {
                $allowed[$className] = true;
            }
        }
        return array_keys($allowed);
    }
}

if (!function_exists('validatePostElementContent')) {
    function validatePostElementContent(string $content): array {
        $errors = [];
        if (trim($content) === '') {
            return ['valid' => false, 'errors' => ['Post content is required.']];
        }
        if (preg_match('/```(?:html|markdown)?/i', $content)) {
            $errors[] = 'Content must be raw HTML and must not contain Markdown code fences.';
        }
        if (preg_match('/<\/?(?:script|style|iframe|object|embed|form)\b/i', $content)) {
            $errors[] = 'Content contains a prohibited HTML element.';
        }
        if (preg_match('/\son[a-z]+\s*=/i', $content)) {
            $errors[] = 'Inline event handlers are not allowed.';
        }
        if (preg_match('/\sstyle\s*=/i', $content)) {
            $errors[] = 'Inline style attributes are not allowed; use the UI contract classes.';
        }

        $allowed = array_fill_keys(getAllowedPostElementClasses(), true);
        preg_match_all('/\bclass\s*=\s*["\']([^"\']*)["\']/iu', $content, $classAttributes);
        $unknown = [];
        foreach ($classAttributes[1] as $classList) {
            foreach (preg_split('/\s+/', trim($classList)) as $className) {
                if ($className !== '' && !isset($allowed[$className])) {
                    $unknown[$className] = true;
                }
            }
        }
        if ($unknown) {
            $errors[] = 'Unsupported CSS classes: ' . implode(', ', array_keys($unknown)) . '.';
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $loaded = $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="post-content-root">' . $content . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if (!$loaded) {
            $errors[] = 'Content contains malformed HTML.';
        } else {
            $xpath = new DOMXPath($dom);
            $requiredDescendants = [
                'key-takeaways' => ['takeaway-badge', 'takeaway-list'],
                'heading-number' => ['num-circle'],
                'callout' => ['callout-title'],
                'pros-cons-container' => ['pros-box', 'cons-box'],
                'table-responsive' => ['content-table'],
                'step-cards' => ['step-card'],
                'checklist-grid' => ['check-item'],
                'vertical-timeline' => ['timeline-item'],
                'quote-card' => ['quote-text', 'quote-author'],
                'metrics-grid' => ['metric-card'],
                'faq-accordion' => ['faq-item'],
                'cta-box' => ['cta-title', 'cta-desc'],
                'download-card' => ['download-info', 'btn-download'],
                'tag-cloud' => ['tag-title', 'tag-item'],
            ];
            $hasClass = static fn(string $name): string =>
                "contains(concat(' ', normalize-space(@class), ' '), ' {$name} ')";
            foreach ($requiredDescendants as $rootClass => $childClasses) {
                foreach ($xpath->query('//*[' . $hasClass($rootClass) . ']') as $rootNode) {
                    if (preg_match('/[\x{2600}-\x{27BF}\x{1F000}-\x{1FAFF}]/u', $rootNode->textContent)) {
                        $errors[] = 'UI blocks must not contain emoji.';
                    }
                    foreach ($childClasses as $childClass) {
                        if ($xpath->query('.//*[' . $hasClass($childClass) . ']', $rootNode)->length === 0) {
                            $errors[] = "UI element .{$rootClass} requires descendant .{$childClass}.";
                        }
                    }
                }
            }
        }

        return ['valid' => !$errors, 'errors' => array_values(array_unique($errors))];
    }
}
