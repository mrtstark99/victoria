<?php
/**
 * Schema.org JSON-LD Builder Helper
 * Generates structured data for FAQPage, HowTo, Article, etc.
 */

namespace Helpers;

class SchemaBuilder {
    /**
     * Build Article / BlogPosting Schema with breadcrumbs and auto FAQs.
     *
     * @param array $post
     * @param string $canonicalUrl
     * @return array
     */
    public static function buildArticleSchema(array $post, string $canonicalUrl): array {
        $baseUrl = rtrim(function_exists('getSystemBaseUrl') ? getSystemBaseUrl() : (defined('APP_URL') && APP_URL ? APP_URL : 'http://localhost'), '/');
        $siteTitle = function_exists('getSetting') ? getSetting('site_name', defined('SITE_NAME') ? SITE_NAME : 'Bright Education') : 'Bright Education';

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BlogPosting',
                    '@id' => "{$canonicalUrl}#article",
                    'isPartOf' => [
                        '@type' => 'WebPage',
                        '@id' => $canonicalUrl
                    ],
                    'headline' => $post['title'] ?? '',
                    'description' => $post['meta_description'] ?? '',
                    'url' => $canonicalUrl,
                    'datePublished' => date('c', strtotime($post['published_at'] ?? $post['created_at'] ?? 'now')),
                    'dateModified' => date('c', strtotime($post['updated_at'] ?? $post['created_at'] ?? 'now')),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $post['author_name'] ?? 'Bright Education',
                        'url' => "{$baseUrl}/"
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => $siteTitle,
                        'url' => "{$baseUrl}/"
                    ],
                    'mainEntityOfPage' => [
                        '@type' => 'WebPage',
                        '@id' => $canonicalUrl
                    ]
                ],
                [
                    '@type' => 'BreadcrumbList',
                    '@id' => "{$canonicalUrl}#breadcrumb",
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Trang chủ',
                            'item' => "{$baseUrl}/"
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $post['category_name'] ?? 'Du học Nhật Bản',
                            'item' => "{$baseUrl}/category/" . ($post['category_slug'] ?? 'tin-tuc')
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $post['title'] ?? '',
                            'item' => $canonicalUrl
                        ]
                    ]
                ]
            ]
        ];

        if (!empty($post['featured_image'])) {
            $schema['@graph'][0]['image'] = strpos($post['featured_image'], 'http') === 0 ? $post['featured_image'] : "{$baseUrl}{$post['featured_image']}";
        }

        if (!empty($post['custom_schema_json'])) {
            $schema = self::mergeSchemas($schema, $post['custom_schema_json']);
        }

        $autoFaqs = self::extractFAQFromContent($post['content'] ?? '');
        if (!empty($autoFaqs)) {
            $faqSchema = self::buildFAQSchema($autoFaqs);
            if (!empty($faqSchema)) {
                $faqSchema['@id'] = "{$canonicalUrl}#faq";
                $schema['@graph'][] = $faqSchema;
            }
        }

        return $schema;
    }
    /**
     * Build FAQPage schema from Q&A pairs
     */
    public static function buildFAQSchema(array $faqs): array {
        if (empty($faqs)) return [];

        $mainEntity = [];
        foreach ($faqs as $faq) {
            $question = trim($faq['question'] ?? $faq['q'] ?? '');
            $answer = trim($faq['answer'] ?? $faq['a'] ?? '');
            if ($question === '' || $answer === '') continue;

            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer
                ]
            ];
        }

        if (empty($mainEntity)) return [];

        return [
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        ];
    }

    /**
     * Build HowTo schema from steps
     */
    public static function buildHowToSchema(string $name, array $steps, array $options = []): array {
        if (empty($steps)) return [];

        $howToSteps = [];
        foreach ($steps as $i => $step) {
            $stepData = [
                '@type' => 'HowToStep',
                'position' => $i + 1,
                'name' => $step['name'] ?? $step['title'] ?? "Bước " . ($i + 1),
                'text' => $step['text'] ?? $step['description'] ?? ''
            ];

            if (!empty($step['image'])) {
                $stepData['image'] = $step['image'];
            }
            if (!empty($step['url'])) {
                $stepData['url'] = $step['url'];
            }

            $howToSteps[] = $stepData;
        }

        $schema = [
            '@type' => 'HowTo',
            'name' => $name,
            'step' => $howToSteps
        ];

        if (!empty($options['description'])) $schema['description'] = $options['description'];
        if (!empty($options['totalTime'])) $schema['totalTime'] = $options['totalTime'];
        if (!empty($options['estimatedCost'])) {
            $schema['estimatedCost'] = [
                '@type' => 'MonetaryAmount',
                'currency' => $options['estimatedCost']['currency'] ?? 'VND',
                'value' => $options['estimatedCost']['value'] ?? '0'
            ];
        }
        if (!empty($options['image'])) $schema['image'] = $options['image'];

        return $schema;
    }

    /**
     * Auto-extract FAQ pairs from HTML content
     * Detects <details>/<summary> patterns and H3 + following paragraph patterns
     */
    public static function extractFAQFromContent(string $html): array {
        $faqs = [];

        // Pattern 1: <details> + <summary> (FAQ accordion pattern)
        if (preg_match_all('/<details[^>]*>\s*<summary[^>]*>(.*?)<\/summary>\s*(.*?)<\/details>/isu', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $q = trim(strip_tags($m[1]));
                $a = trim(strip_tags($m[2]));
                if ($q !== '' && $a !== '') {
                    $faqs[] = ['question' => $q, 'answer' => $a];
                }
            }
        }

        // Pattern 2: FAQ section with H3 questions + P answers
        // Look for heading containing "FAQ" or "Câu hỏi"
        if (preg_match('/<h[23][^>]*>.*?(FAQ|Câu hỏi|Thắc mắc|Hỏi đáp).*?<\/h[23]>/isu', $html)) {
            // After the FAQ heading, match H3 + next P/div
            $faqSection = preg_split('/<h[23][^>]*>.*?(FAQ|Câu hỏi|Thắc mắc|Hỏi đáp).*?<\/h[23]>/isu', $html, 2);
            if (isset($faqSection[1])) {
                $afterFaq = $faqSection[1];
                // Stop at the next H2
                $afterFaq = preg_split('/<h2[^>]*>/iu', $afterFaq, 2)[0] ?? $afterFaq;

                if (preg_match_all('/<h[34][^>]*>(.*?)<\/h[34]>\s*(<p[^>]*>.*?<\/p>)/isu', $afterFaq, $qMatches, PREG_SET_ORDER)) {
                    foreach ($qMatches as $m) {
                        $q = trim(strip_tags($m[1]));
                        $a = trim(strip_tags($m[2]));
                        if ($q !== '' && $a !== '' && !self::isDuplicate($faqs, $q)) {
                            $faqs[] = ['question' => $q, 'answer' => $a];
                        }
                    }
                }
            }
        }

        return $faqs;
    }

    /**
     * Merge custom schema into existing @graph array
     */
    public static function mergeSchemas(array $baseSchema, $customSchema): array {
        if (empty($customSchema)) return $baseSchema;

        // Parse JSON string if needed
        if (is_string($customSchema)) {
            $customSchema = json_decode($customSchema, true);
            if (json_last_error() !== JSON_ERROR_NONE) return $baseSchema;
        }

        if (!is_array($customSchema)) return $baseSchema;

        // If custom schema has @graph, merge into base @graph
        if (isset($customSchema['@graph'])) {
            foreach ($customSchema['@graph'] as $item) {
                $baseSchema['@graph'][] = $item;
            }
        } elseif (isset($customSchema['@type'])) {
            // Single schema object — add to @graph
            $baseSchema['@graph'][] = $customSchema;
        } elseif (isset($customSchema[0]) && is_array($customSchema[0])) {
            // Array of schema objects
            foreach ($customSchema as $item) {
                if (isset($item['@type'])) {
                    $baseSchema['@graph'][] = $item;
                }
            }
        }

        return $baseSchema;
    }

    /**
     * Validate a custom schema JSON string
     */
    public static function validateSchemaJSON(string $jsonString): array {
        if (empty(trim($jsonString))) {
            return ['valid' => true, 'errors' => []];
        }

        $decoded = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['valid' => false, 'errors' => ['Invalid JSON: ' . json_last_error_msg()]];
        }

        $errors = [];

        // Must have @type
        if (is_array($decoded)) {
            if (isset($decoded['@type'])) {
                // Single schema — OK
            } elseif (isset($decoded['@graph'])) {
                foreach ($decoded['@graph'] as $i => $item) {
                    if (!isset($item['@type'])) {
                        $errors[] = "Item [{$i}] in @graph is missing @type.";
                    }
                }
            } elseif (isset($decoded[0])) {
                // Array of schemas
                foreach ($decoded as $i => $item) {
                    if (!isset($item['@type'])) {
                        $errors[] = "Item [{$i}] is missing @type.";
                    }
                }
            } else {
                $errors[] = 'Schema must have @type, @graph, or be an array of typed objects.';
            }
        }

        // Validate known schema types
        $allowedTypes = [
            'FAQPage', 'HowTo', 'Article', 'BlogPosting', 'WebPage', 'BreadcrumbList',
            'Product', 'Review', 'Person', 'Organization', 'VideoObject', 'ImageObject',
            'ItemList', 'ListItem', 'Course', 'Event', 'Recipe', 'SoftwareApplication',
            'Question', 'Answer', 'HowToStep', 'HowToSection', 'AggregateRating'
        ];

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    /**
     * Check for duplicate question in FAQ array
     */
    private static function isDuplicate(array $faqs, string $question): bool {
        $normalized = mb_strtolower(trim($question), 'UTF-8');
        foreach ($faqs as $faq) {
            if (mb_strtolower(trim($faq['question']), 'UTF-8') === $normalized) {
                return true;
            }
        }
        return false;
    }
}
