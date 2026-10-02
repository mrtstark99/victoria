<?php

require_once dirname(__DIR__) . '/config/config.php';

function contractAssert(bool $condition, string $message): void {
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

$library = getPostElementLibrary();
contractAssert(count($library) === 13, 'Expected 13 canonical UI element groups.');

foreach ($library as $element) {
    foreach ($element['html_templates'] as $index => $template) {
        $validation = validatePostElementContent($template);
        contractAssert(
            $validation['valid'],
            "Canonical template {$element['id']}#{$index} failed validation: " . implode(' ', $validation['errors'])
        );
    }
}

$css = '';
foreach (glob(dirname(__DIR__) . '/public/assets/css/post/*.css') as $cssFile) {
    $css .= file_get_contents($cssFile);
}
foreach (getAllowedPostElementClasses() as $className) {
    contractAssert(
        preg_match('/\.' . preg_quote($className, '/') . '(?![-_a-zA-Z0-9])/', $css) === 1,
        "Missing CSS selector for .{$className}."
    );
}

$invalidCases = [
    '```html<p>Markdown wrapper</p>```',
    '<div class="unknown-component">Unknown class</div>',
    '<div class="callout callout-tip"><p>Missing title</p></div>',
    '<p style="color:red">Inline style</p>',
    '<p onclick="alert(1)">Inline handler</p>',
    '<script>alert(1)</script>',
];
foreach ($invalidCases as $invalidContent) {
    contractAssert(!validatePostElementContent($invalidContent)['valid'], 'Invalid content was accepted.');
}

echo "Post element contract tests passed.\n";
