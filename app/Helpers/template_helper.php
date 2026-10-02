<?php
/**
 * View Template Helpers
 */

function view($path, $data = []) {
    extract($data);
    $viewFile = APP_ROOT . '/views/' . $path . '.php';
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        die("View file [views/{$path}.php] not found.");
    }
}

function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header('Location: ' . $url);
    exit;
}

function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);

        $icons = [
            'success' => '<svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
            'error'   => '<svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            'warning' => '<svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            'info'    => '<svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        ];
        
        $alertClass = 'alert-' . ($type === 'danger' ? 'error' : $type);
        $icon = $icons[$type] ?? $icons['success'];
        $escapedMsg = htmlspecialchars($message);

        echo "
        <div class='alert {$alertClass}' role='alert'>
            {$icon}
            <div class='alert-content'>{$escapedMsg}</div>
            <button type='button' class='alert-close' onclick='this.parentElement.remove()' aria-label='Đóng'>
                <svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='6' x2='6' y2='18'/><line x1='6' y1='6' x2='18' y2='18'/></svg>
            </button>
        </div>
        ";
    }
}

function getSetting($key, $default = null) {
    static $settings = null;
    if ($settings === null) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT setting_key, setting_value, setting_type FROM settings");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $value = $row['setting_value'];
            switch ($row['setting_type']) {
                case 'json':
                    $value = json_decode($value, true);
                    break;
                case 'boolean':
                    $value = (bool)$value;
                    break;
                case 'number':
                    $value = is_numeric($value) ? (int)$value : $value;
                    break;
            }
            $settings[$row['setting_key']] = $value;
        }
    }
    return $settings[$key] ?? $default;
}

function updateSetting($key, $value, $type = 'text', $description = null) {
    $db = Database::getInstance();
    if ($type === 'json') {
        $value = is_array($value) || is_object($value) ? json_encode($value) : $value;
    } elseif ($type === 'boolean') {
        $value = $value ? '1' : '0';
    } else {
        $value = (string)$value;
    }

    $stmt = $db->prepare("
        INSERT INTO settings (setting_key, setting_value, setting_type, description, updated_at)
        VALUES (?, ?, ?, ?, datetime('now','localtime'))
        ON CONFLICT(setting_key) DO UPDATE SET
            setting_value = excluded.setting_value,
            setting_type = excluded.setting_type,
            description = COALESCE(excluded.description, settings.description),
            updated_at = datetime('now','localtime')
    ");
    return $stmt->execute([$key, $value, $type, $description]);
}

function safeNavigationUrl($url) {
    if (!is_string($url)) return null;
    $url = trim($url);
    if ($url === '' || preg_match('/[\x00-\x20\\\\]/', $url)) return null;
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) return $url;
    if (str_starts_with($url, '#')) return $url;
    if (preg_match('/^(https?:\/\/|mailto:|tel:)/i', $url)) return $url;
    return null;
}

/**
 * Get Author / Admin Avatar URL dynamically with real avatar fallback
 */
function getAuthorAvatar($avatar = null, $name = 'Author') {
    if (!empty($avatar)) {
        if (strpos($avatar, '/uploads/') === 0) {
            $localFile = APP_ROOT . '/public' . $avatar;
            if (file_exists($localFile) && is_file($localFile)) {
                return $avatar;
            }
        } else {
            return $avatar;
        }
    }
    
    static $defaultAvatar = null;
    if ($defaultAvatar === null) {
        try {
            $db = Database::getInstance();
            $u = $db->query("SELECT avatar FROM users WHERE role = 'admin' AND avatar IS NOT NULL AND avatar != '' LIMIT 1")->fetch();
            if (!empty($u['avatar'])) {
                $localFile = APP_ROOT . '/public' . $u['avatar'];
                if (file_exists($localFile) && is_file($localFile)) {
                    $defaultAvatar = $u['avatar'];
                }
            }
        } catch (\Throwable $e) {}

        if ($defaultAvatar === null) {
            $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0D8ABC&color=fff&size=128';
        }
    }
    return $defaultAvatar;
}

/**
 * Get Post Featured Image with fallback
 */
function getPostFeaturedImage($post) {
    if (!empty($post['featured_image'])) {
        return $post['featured_image'];
    }
    $catImages = [
        1 => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1200&q=80',
        2 => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
        3 => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
    ];
    $catId = $post['category_id'] ?? 1;
    return $catImages[$catId] ?? 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80';
}

/**
 * Get accurate Base URL of current system (including Scheme, Host, Port, and Subdirectory / Virtual Path)
 */
function getSystemBaseUrl(): string {
    if (defined('APP_URL') && APP_URL !== '') {
        return rtrim(APP_URL, '/');
    }

    if (php_sapi_name() === 'cli') {
        return 'http://localhost:5000';
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
        || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false);
    $scheme = $isHttps ? 'https' : 'http';

    $host = trim((string)($_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '')));
    $hostIsSafe = $host !== ''
        && !preg_match('~[\s\\/@]~', $host)
        && filter_var($scheme . '://' . $host, FILTER_VALIDATE_URL) !== false;
    $allowedHosts = defined('ALLOWED_HOSTS') ? ALLOWED_HOSTS : [];
    if (!$hostIsSafe || ($allowedHosts && !in_array($host, $allowedHosts, true))) {
        $host = $allowedHosts[0] ?? 'localhost:5000';
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($scriptName));
    // Remove trailing /public, /api or /admin if scriptName is located in sub-controllers
    $dir = preg_replace('#/(public|api|admin)$#i', '', $dir);
    $dir = rtrim($dir, '/');

    $baseUrl = $scheme . '://' . $host . ($dir !== '' && $dir !== '/' ? $dir : '');
    return rtrim($baseUrl, '/');
}

/**
 * Get accurate Agent REST API Gateway Endpoint URL
 */
function getSystemApiEndpoint(): string {
    return getSystemBaseUrl() . '/api/agent.php';
}

/**
 * Get system Host with optional port
 */
function getSystemSiteHost(): string {
    $baseUrl = getSystemBaseUrl();
    $parsed = parse_url($baseUrl);
    $host = $parsed['host'] ?? 'localhost';
    if (!empty($parsed['port']) && $parsed['port'] != 80 && $parsed['port'] != 443) {
        $host .= ':' . $parsed['port'];
    }
    return $host;
}

/**
 * Compile markdown template with real system URLs and host
 */
function compileSkillTemplate(string $content): string {
    $baseUrl = getSystemBaseUrl();
    $apiEndpoint = getSystemApiEndpoint();
    $siteHost = getSystemSiteHost();

    // 1. Strip absolute local file links (e.g. file:///c:/Projects/... or file:///c:/Users/...) and convert to relative ./
    $content = preg_replace('#file:///[a-zA-Z]:/[^)]*?/cms-seo-agent-skill/#iu', './', $content);
    $content = preg_replace('#file:///[a-zA-Z]:/[^)]*?/#iu', './', $content);
    $content = preg_replace('#file:///[^)]*?/#iu', './', $content);

    // 2. Map standard placeholders & mock domains
    $replacements = [
        '{{BASE_URL}}' => $baseUrl,
        '{{API_ENDPOINT}}' => $apiEndpoint,
        '{{SITE_HOST}}' => $siteHost,
        'https://your-domain.com' => $baseUrl,
        'http://your-domain.com' => $baseUrl,
        'https://yourdomain.com' => $baseUrl,
        'http://yourdomain.com' => $baseUrl,
        'your-domain.com' => $siteHost,
        'yourdomain.com' => $siteHost,
        'your-system-domain.com' => $siteHost,
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $content);
}

/**
 * Pure PHP Zip Archiver (RFC 1951 / PKZIP standard)
 * 100% compatible with Windows Compressed Folders, .NET ZipFile, WinRAR, 7-Zip.
 */
class SimpleZip {
    private array $files = [];

    public function addFile(string $name, string $data): void {
        $name = str_replace('\\', '/', $name);
        $this->files[$name] = $data;
    }

    public function build(): string {
        $dataBlock = '';
        $dirBlock = '';
        $offset = 0;

        $time = time();
        $mtime = ((int)date('H', $time) << 11) | ((int)date('i', $time) << 5) | ((int)date('s', $time) >> 1);
        $mdate = (((int)date('Y', $time) - 1980) << 9) | ((int)date('m', $time) << 5) | (int)date('d', $time);

        foreach ($this->files as $name => $content) {
            $crc = crc32($content);
            $uncLen = strlen($content);
            $cContent = function_exists('gzdeflate') ? gzdeflate($content, 6) : false;
            if ($cContent !== false && strlen($cContent) < $uncLen) {
                $cLen = strlen($cContent);
                $method = 8; // DEFLATE
                $payload = $cContent;
            } else {
                $cLen = $uncLen;
                $method = 0; // STORE
                $payload = $content;
            }

            $nameLen = strlen($name);

            // Local file header (30 bytes)
            $local = pack('VvvvvvVVVvv',
                0x04034b50, // signature
                20,         // version needed to extract
                0,          // general purpose flag
                $method,    // compression method
                $mtime,     // last mod file time
                $mdate,     // last mod file date
                $crc,       // crc-32
                $cLen,      // compressed size
                $uncLen,    // uncompressed size
                $nameLen,   // file name length
                0           // extra field length
            ) . $name . $payload;

            // Central directory file header (46 bytes)
            $dir = pack('VvvvvvvVVVvvvvvVV',
                0x02014b50, // signature
                20,         // version made by
                20,         // version needed to extract
                0,          // general purpose flag
                $method,    // compression method
                $mtime,     // last mod file time
                $mdate,     // last mod file date
                $crc,       // crc-32
                $cLen,      // compressed size
                $uncLen,    // uncompressed size
                $nameLen,   // file name length
                0,          // extra field length
                0,          // file comment length
                0,          // disk number start
                0,          // internal file attributes
                32,         // external file attributes (MS-DOS Archive)
                $offset     // relative offset of local header
            ) . $name;

            $offset += strlen($local);
            $dataBlock .= $local;
            $dirBlock .= $dir;
        }

        $dirLen = strlen($dirBlock);
        $count = count($this->files);

        // End of central directory record (22 bytes)
        $eocd = pack('VvvvvVVv',
            0x06054b50, // signature
            0,          // number of this disk
            0,          // disk with start of central directory
            $count,     // total entries on this disk
            $count,     // total entries in central directory
            $dirLen,    // size of central directory
            $offset,    // offset of start of central directory
            0           // zipfile comment length
        );

        return $dataBlock . $dirBlock . $eocd;
    }
}

if (!function_exists('getPostImage')) {
    /**
     * Format post image URL (handles external URLs and local uploads).
     *
     * @param string|null $path
     * @return string
     */
    function getPostImage($path) {
        if (empty($path)) {
            return 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1000&q=80';
        }
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        return UPLOAD_URL . ltrim($path, '/');
    }
}

if (!function_exists('truncateText')) {
    /**
     * Truncate UTF-8 text safely with suffix.
     *
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    function truncateText($text, $length = 100, $suffix = '...') {
        if (mb_strlen($text, 'UTF-8') <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format currency in VND.
     *
     * @param float|int $amount
     * @return string
     */
    function formatMoney($amount) {
        return number_format((float)$amount, 0, ',', '.') . ' ₫';
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format number with thousand separator.
     *
     * @param float|int $number
     * @return string
     */
    function formatNumber($number) {
        return number_format((float)$number, 0, ',', '.');
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Convert datetime string to relative human-readable time.
     *
     * @param string $datetime
     * @return string
     */
    function timeAgo($datetime) {
        $time = strtotime($datetime);
        $current = time();
        $diff = $current - $time;
        if ($diff < 60) {
            return 'Vừa xong';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' phút trước';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' giờ trước';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' ngày trước';
        } else {
            return date('d/m/Y', $time);
        }
    }
}
