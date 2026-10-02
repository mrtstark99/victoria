<?php
/**
 * SEO & String Helpers
 */

if (!function_exists('mb_strlen')) {
    function mb_strlen($str, $encoding = 'UTF-8') {
        if (function_exists('iconv_strlen')) {
            $len = @iconv_strlen((string)$str, $encoding);
            if ($len !== false) return $len;
        }
        preg_match_all('/./u', (string)$str, $matches);
        return count($matches[0]);
    }
}
if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $length = null, $encoding = 'UTF-8') {
        if (function_exists('iconv_substr')) {
            $strLen = mb_strlen($str, $encoding);
            if ($length === null) {
                $length = $strLen;
            }
            $res = @iconv_substr((string)$str, $start, $length, $encoding);
            if ($res !== false) return $res;
        }
        $chars = preg_split('//u', (string)$str, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) return '';
        $sliced = array_slice($chars, $start, $length);
        return implode('', $sliced);
    }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($str, $encoding = 'UTF-8') {
        return strtolower((string)$str);
    }
}
if (!function_exists('mb_strrpos')) {
    function mb_strrpos($haystack, $needle, $offset = 0, $encoding = 'UTF-8') {
        if (function_exists('iconv_strrpos')) {
            $pos = @iconv_strrpos((string)$haystack, (string)$needle, $encoding);
            if ($pos !== false) return $pos;
        }
        $haystackChars = preg_split('//u', (string)$haystack, -1, PREG_SPLIT_NO_EMPTY);
        $needleChars = preg_split('//u', (string)$needle, -1, PREG_SPLIT_NO_EMPTY);
        if ($haystackChars === false || empty($needleChars)) return false;
        $hCount = count($haystackChars);
        $nCount = count($needleChars);
        for ($i = $hCount - $nCount; $i >= $offset; $i--) {
            if (array_slice($haystackChars, $i, $nCount) === $needleChars) {
                return $i;
            }
        }
        return false;
    }
}

function createSlug($string) {
    $slug = mb_strtolower($string, 'UTF-8');
    $slug = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $slug);
    $slug = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $slug);
    $slug = preg_replace('/[íìỉĩị]/u', 'i', $slug);
    $slug = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $slug);
    $slug = preg_replace('/[úùủũụưứừửữự]/u', 'u', $slug);
    $slug = preg_replace('/[ýỳỷỹỵ]/u', 'y', $slug);
    $slug = preg_replace('/đ/u', 'd', $slug);
    $slug = preg_replace('/[^a-z0-9-]/u', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

function formatDate($date, $format = 'd/m/Y H:i') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

function getExcerpt($content, $length = 160) {
    $content = strip_tags($content);
    $content = preg_replace('/\s+/', ' ', $content);
    return truncateText($content, $length);
}

function seoTitle($title, $brand = 'My SEO Blog', $maxLength = 60) {
    $title = html_entity_decode((string)$title, ENT_QUOTES, 'UTF-8');
    $title = trim(strip_tags($title));
    $title = preg_replace('/\s*[-|]\s*' . preg_quote($brand, '/') . '\s*$/iu', '', $title);
    $suffix = ' | ' . $brand;
    $available = max(20, $maxLength - mb_strlen($suffix, 'UTF-8'));

    if (mb_strlen($title, 'UTF-8') > $available) {
        $title = mb_substr($title, 0, $available, 'UTF-8');
        $lastSpace = mb_strrpos($title, ' ', 0, 'UTF-8');
        if ($lastSpace !== false && $lastSpace >= (int)($available * 0.7)) {
            $title = mb_substr($title, 0, $lastSpace, 'UTF-8');
        }
    }
    return rtrim($title, " -|,.") . $suffix;
}

function seoDescription($description, $fallback = '', $maxLength = 155) {
    $description = html_entity_decode((string)$description, ENT_QUOTES, 'UTF-8');
    $description = trim(preg_replace('/\s+/u', ' ', strip_tags($description)));
    if ($description === '') {
        $fallback = html_entity_decode((string)$fallback, ENT_QUOTES, 'UTF-8');
        $description = trim(preg_replace('/\s+/u', ' ', strip_tags($fallback)));
    }
    if (mb_strlen($description, 'UTF-8') <= $maxLength) {
        return $description;
    }

    $cut = mb_substr($description, 0, $maxLength - 3, 'UTF-8');
    $lastSpace = mb_strrpos($cut, ' ', 0, 'UTF-8');
    if ($lastSpace !== false && $lastSpace >= (int)($maxLength * 0.75)) {
        $cut = mb_substr($cut, 0, $lastSpace, 'UTF-8');
    }
    return rtrim($cut, " ,.;:-") . '...';
}

function stripEmbeddedTableOfContents($html) {
    if (empty($html)) {
        return '';
    }
    // Strip redundant in-content TOC blocks: <div/nav class="table-of-contents|toc-container|in-post-toc"...>...</div>
    $pattern = '/<(?:div|nav)[^>]*\b(?:table-of-contents|toc-container|in-post-toc)\b[^>]*>.*?<\/(?:div|nav)>\s*/isu';
    $cleaned = preg_replace($pattern, '', (string)$html);
    return $cleaned !== null ? $cleaned : (string)$html;
}

function buildPostTableOfContents($html) {
    $cleanedHtml = stripEmbeddedTableOfContents($html);
    $items = [];
    $usedIds = [];
    $content = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h\1>/isu', function ($match) use (&$items, &$usedIds) {
        $level = (int)$match[1];
        $attributes = $match[2];
        $headingHtml = $match[3];
        $label = trim(preg_replace('/\s+/u', ' ', strip_tags($headingHtml)));
        $label = html_entity_decode($label, ENT_QUOTES, 'UTF-8');
        if ($label === '') {
            return $match[0];
        }

        if (preg_match('/\sid=["\']([^"\']+)["\']/iu', $attributes, $idMatch)) {
            $id = $idMatch[1];
        } else {
            $baseId = createSlug($label) ?: 'section';
            $id = $baseId;
            $counter = 2;
            while (isset($usedIds[$id])) {
                $id = $baseId . '-' . $counter++;
            }
            $attributes .= ' id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '"';
        }

        $usedIds[$id] = true;
        $items[] = ['level' => $level, 'id' => $id, 'label' => $label];
        return '<h' . $level . $attributes . '>' . $headingHtml . '</h' . $level . '>';
    }, (string)$cleanedHtml);

    return ['content' => $content, 'items' => $items];
}

function calculateReadingTime($content, $wpm = 200) {
    $clean = preg_replace('/\s+/u', ' ', trim(strip_tags((string)$content)));
    $words = $clean === '' ? 0 : count(explode(' ', $clean));
    return (int)max(1, ceil($words / $wpm));
}

