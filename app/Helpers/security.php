<?php
/**
 * Security Helper Functions
 */

function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || !hash_equals($_SESSION[CSRF_TOKEN_NAME], $token)) {
        return false;
    }
    return true;
}

function validateCsrfToken($token) {
    return verifyCSRFToken($token);
}

function csrfField() {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . generateCSRFToken() . '">';
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isEditor() {
    return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'editor']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        redirect('/login', 'Vui lòng đăng nhập để tiếp tục.', 'warning');
    }
}

function requireAdmin() {
    if (!isLoggedIn()) {
        redirect('/login', 'Vui lòng đăng nhập để tiếp tục.', 'warning');
    }
    if (!isAdmin()) {
        redirect('/admin/dashboard', 'Bạn cần quyền Quản trị viên (Admin) để thực hiện tính năng này.', 'error');
    }
}

function requireEditor() {
    if (!isLoggedIn()) {
        redirect('/login', 'Vui lòng đăng nhập để tiếp tục.', 'warning');
    }
    if (!isEditor()) {
        redirect('/', 'Tài khoản của bạn không có quyền truy cập khu vực quản trị.', 'error');
    }
}

function getClientIP() {
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $trustedProxies = defined('TRUSTED_PROXIES') ? TRUSTED_PROXIES : [];
    
    if (!empty($trustedProxies) && in_array($remoteAddr, $trustedProxies, true)) {
        $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($forwardedFor !== '') {
            $ips = array_map('trim', explode(',', $forwardedFor));
            // Walk from the trusted edge toward the client. Ignore malformed entries.
            for ($i = count($ips) - 1; $i >= 0; $i--) {
                if (!filter_var($ips[$i], FILTER_VALIDATE_IP)) continue;
                if (!in_array($ips[$i], $trustedProxies, true)) return $ips[$i];
            }
        }
        // Cloudflare's client IP header is trusted only when the direct peer is
        // explicitly configured as a trusted proxy.
        $cloudflareIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '';
        if ($cloudflareIp !== '' && filter_var($cloudflareIp, FILTER_VALIDATE_IP)) {
            return $cloudflareIp;
        }
    }
    
    return filter_var($remoteAddr, FILTER_VALIDATE_IP) ? $remoteAddr : '127.0.0.1';
}

function sanitizeHtml($html) {
    if (empty($html) || !is_string($html)) return '';
    $allowedTags = array_fill_keys(explode(' ', 'p br hr h1 h2 h3 h4 h5 h6 strong em b i u s strike blockquote pre code ul ol li a img table thead tbody tfoot tr th td figure figcaption span div section article mark small del ins sub sup details summary'), true);
    $allowedAttributes = array_fill_keys(explode(' ', 'class id title aria-label role'), true);
    $document = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="UTF-8"><div id="sanitized-root">' . str_replace(chr(0), '', $html) . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $root = $document->getElementById('sanitized-root');
    if (!$root) return '';

    $clean = function ($node, bool $insideUiBlock = false) use (&$clean, $allowedTags, $allowedAttributes) {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText && $insideUiBlock) {
                $child->nodeValue = preg_replace('/[\x{2600}-\x{27BF}\x{1F000}-\x{1FAFF}\x{FE0F}\x{200D}]/u', '', $child->nodeValue);
                continue;
            }
            if (!$child instanceof DOMElement) continue;
            $tag = strtolower($child->tagName);
            if (!isset($allowedTags[$tag])) {
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math', 'form'], true)) {
                    $node->removeChild($child);
                } else {
                    $clean($child, $insideUiBlock);
                    while ($child->firstChild) $node->insertBefore($child->firstChild, $child);
                    $node->removeChild($child);
                }
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attribute) {
                $name = strtolower($attribute->name);
                $value = trim($attribute->value);
                $allowed = isset($allowedAttributes[$name])
                    || ($tag === 'a' && in_array($name, ['href', 'target', 'rel'], true))
                    || ($tag === 'img' && in_array($name, ['src', 'alt', 'width', 'height', 'loading'], true))
                    || (in_array($tag, ['td', 'th'], true) && in_array($name, ['colspan', 'rowspan'], true));
                if (!$allowed) {
                    $child->removeAttributeNode($attribute);
                    continue;
                }
                if (in_array($name, ['href', 'src'], true)) {
                    $url = preg_replace('/[\x00-\x20\x7f]+/u', '', html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    if ((preg_match('/^[a-z][a-z0-9+.-]*:/i', $url) && !preg_match('/^(https?:|mailto:|tel:)/i', $url))
                        || str_starts_with($url, '//') || str_starts_with($url, '\\')) {
                        $child->removeAttributeNode($attribute);
                    }
                }
                if ($name === 'target' && !in_array($value, ['_blank', '_self'], true)) $child->removeAttributeNode($attribute);
            }
            if ($tag === 'a' && $child->getAttribute('target') === '_blank') $child->setAttribute('rel', 'noopener noreferrer');
            $isUiBlock = (bool)preg_match('/(?:^|\s)(?:key-takeaways|callout|pros-cons-container|table-responsive|step-cards|checklist-grid|vertical-timeline|quote-card|metrics-grid|faq-accordion|cta-box|download-card)(?:\s|$)/', $child->getAttribute('class'));
            $clean($child, $insideUiBlock || $isUiBlock);
        }
    };
    $clean($root);
    $output = '';
    foreach ($root->childNodes as $child) $output .= $document->saveHTML($child);
    return $output;
}

function xss_clean($data) {
    return sanitizeHtml($data);
}
