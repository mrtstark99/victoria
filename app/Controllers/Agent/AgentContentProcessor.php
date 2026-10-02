<?php
/**
 * Agent Content audits, formatting, and file attachments processor
 * E-E-A-T scoring delegated to AgentEEATAnalyzer.
 */

namespace Controllers\Agent;

use Models\AgentToken;
use Helpers\ImageOptimizer;

class AgentContentProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasDraft = in_array('admin', $scopes) || in_array('posts:draft', $scopes);
        if (!$hasDraft) $this->forbidden('Missing scope [posts:draft]');
        $authorId = $agent['default_author_id'] ?? $agent['user_id'] ?? 1;

        switch ($action) {
            case 'process_draft':
                $content = $input['content'] ?? '';
                $title   = trim($input['title'] ?? '');

                $tocData = buildPostTableOfContents($content);
                $clean   = preg_replace('/\s+/', ' ', trim(strip_tags($content)));
                $words   = $clean === '' ? 0 : count(explode(' ', $clean));

                $this->success([
                    'slug'            => createSlug($title),
                    'seo_title'       => seoTitle($title),
                    'seo_description' => seoDescription($content, $title),
                    'toc'             => $tocData['items'],
                    'metrics'         => [
                        'word_count'            => $words,
                        'reading_time_minutes'  => (int)max(1, ceil($words / 200)),
                        'h2_count'              => preg_match_all('/<h2[^>]*>/i', $content),
                        'h3_count'              => preg_match_all('/<h3[^>]*>/i', $content),
                    ]
                ]);
                break;

            case 'validate_post':
                $content   = $input['content'] ?? '';
                $title     = trim($input['title'] ?? '');
                $metaTitle = trim($input['meta_title'] ?? '');
                $metaDesc  = trim($input['meta_description'] ?? '');

                $errors   = [];
                $warnings = [];
                $passed   = true;

                $clean = preg_replace('/\s+/', ' ', trim(strip_tags($content)));
                $words = $clean === '' ? 0 : count(explode(' ', $clean));

                if ($words < 300) $warnings[] = "Bài viết quá ngắn ({$words} từ). Các bài chuẩn SEO thường trên 600 từ.";
                if (!preg_match('/<h2[^>]*>/i', $content)) $warnings[] = 'Thiếu thẻ H2 phân chia bố cục.';
                if (preg_match('/<script\b[^>]*>(.*?)<\/script>/is', $content)) {
                    $errors[] = 'Phát hiện mã độc <script> trong bài viết.';
                    $passed   = false;
                }
                if (preg_match('/on\w+\s*=\s*["\'](.+?)[\'"]/is', $content)) {
                    $errors[] = 'Phát hiện inline event handler trong HTML.';
                    $passed   = false;
                }
                if (empty($title)) {
                    $errors[] = 'Tiêu đề không được để trống.';
                    $passed   = false;
                }
                if (mb_strlen($metaTitle) > 60) $warnings[] = 'Thẻ Meta Title dài hơn 60 ký tự.';
                if (mb_strlen($metaDesc) > 155) $warnings[] = 'Thẻ Meta Description dài hơn 155 ký tự.';

                // ── E-E-A-T Scoring (delegated) ──
                $eeatSignals = (new AgentEEATAnalyzer())->analyze($content, $title);
                if (!$eeatSignals['has_citations'])     $warnings[] = 'E-E-A-T: Bài viết thiếu trích dẫn nguồn bên ngoài (external links). Google ưu tiên bài có tham chiếu đáng tin cậy.';
                if (!$eeatSignals['has_statistics'])    $warnings[] = 'E-E-A-T: Bài viết thiếu số liệu/thống kê cụ thể. Bổ sung dữ liệu gốc giúp tăng uy tín.';
                if (!$eeatSignals['has_expert_mention']) $warnings[] = 'E-E-A-T: Không phát hiện trích dẫn chuyên gia hoặc kinh nghiệm thực tế. Bổ sung góc nhìn chuyên gia giúp tăng E-E-A-T.';
                if (!$eeatSignals['has_images'])        $warnings[] = 'Bài viết thiếu hình ảnh minh họa. Ảnh gốc và infographic giúp tăng chất lượng nội dung.';

                $score = max(0, 100 - (count($errors) * 25) - (count($warnings) * 5));
                $this->success([
                    'passed'        => $passed,
                    'score'         => $score,
                    'errors'        => $errors,
                    'warnings'      => $warnings,
                    'eeat_analysis' => $eeatSignals
                ]);
                break;

            case 'upload_image':
                if (isset($_FILES['image'])) {
                    $res = $this->seoUpload($_FILES['image'], $input);
                    if ($res['success']) {
                        AgentToken::logAudit($authorId, 'agent_upload_image', 'uploads', null, [], [
                            'filename'  => $res['data']['filename'] ?? '',
                            'url'       => $res['data']['url'] ?? '',
                            'format'    => $res['data']['format'] ?? '',
                            'optimized' => $res['data']['optimized'] ?? false
                        ]);
                        $this->success($res['data']);
                    } else {
                        $this->error($res['message']);
                    }
                } elseif (!empty($input['image_url'])) {
                    $res = $this->fetchAndOptimizeRemoteImage($input['image_url'], $input);
                    if ($res['success']) {
                        AgentToken::logAudit($authorId, 'agent_upload_image', 'uploads', null, [], [
                            'source_url' => $input['image_url'],
                            'url'        => $res['data']['url'] ?? '',
                            'format'     => $res['data']['format'] ?? '',
                            'optimized'  => $res['data']['optimized'] ?? false
                        ]);
                        $this->success($res['data']);
                    } else {
                        $this->error($res['message']);
                    }
                } else {
                    $this->error('Missing image file or image_url.');
                }
                break;
        }
    }

    /**
     * SEO-optimized local upload (WebP conversion, SEO filename)
     */
    private function seoUpload($file, $input): array {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Upload failed or the file was not uploaded through the HTTP upload mechanism.'];
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_IMAGE_TYPES, true)) return ['success' => false, 'message' => 'Invalid file extension.'];
        $actualSize = filesize($file['tmp_name']);
        if ($actualSize === false || $actualSize <= 0 || $actualSize > MAX_FILE_SIZE) return ['success' => false, 'message' => 'File size is invalid or exceeds the limit.'];

        $finfo     = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
        if (!$finfo) return ['success' => false, 'message' => 'Image MIME inspection is unavailable on this server.'];
        $mime      = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $imageInfo = @getimagesize($file['tmp_name']);

        if (!in_array($mime, ALLOWED_IMAGE_MIMES, true) || $imageInfo === false
            || strtolower((string)($imageInfo['mime'] ?? '')) !== strtolower($mime)) {
            return ['success' => false, 'message' => 'Uploaded file is not a valid image.'];
        }

        $optimizeResult = ImageOptimizer::optimize($file['tmp_name'], [
            'seo_filename' => $input['seo_filename'] ?? '',
            'keyword'      => $input['keyword'] ?? '',
            'alt_text'     => $input['alt_text'] ?? '',
            'caption'      => $input['caption'] ?? '',
            'quality'      => (int)($input['quality'] ?? 85),
            'force_webp'   => (bool)($input['force_webp'] ?? true)
        ]);

        if ($optimizeResult['success']) {
            return $optimizeResult;
        }

        $dir = UPLOAD_PATH;
        if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
            return ['success' => false, 'message' => 'Failed to create upload directory.'];
        }

        $seoName = ImageOptimizer::buildSeoFilename($input['seo_filename'] ?? '', $input['keyword'] ?? '', $file['name']);
        $name    = $seoName . '-' . time() . '.' . $ext;

        if (move_uploaded_file($file['tmp_name'], $dir . $name)) {
            return ['success' => true, 'data' => [
                'filename'  => $name,
                'filepath'  => $name,
                'url'       => UPLOAD_URL . $name,
                'width'     => $imageInfo[0],
                'height'    => $imageInfo[1],
                'alt_text'  => $input['alt_text'] ?? '',
                'caption'   => $input['caption'] ?? '',
                'optimized' => false
            ]];
        }
        return ['success' => false, 'message' => 'Failed to save file.'];
    }

    /**
     * Fetch remote image and optimize for SEO
     */
    private function fetchAndOptimizeRemoteImage(string $url, array $input): array {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'Invalid image URL format.'];
        }

        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');
        $host   = $parsed['host'] ?? '';

        if (!in_array($scheme, ['http', 'https'], true) || empty($host) || isset($parsed['user']) || isset($parsed['pass'])) {
            return ['success' => false, 'message' => 'Only HTTP and HTTPS protocols are allowed.'];
        }

        // SSRF Protection
        $ip = gethostbyname($host);
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return ['success' => false, 'message' => 'Unable to resolve remote host.'];
        }
        $maxBytes = MAX_FILE_SIZE;
        $data = null;
        $downloadError = '';
        $downloadTooLarge = false;

        // Method 1: cURL (If available)
        if (function_exists('curl_init')) {
            try {
                $ch = curl_init($url);
                $curlOptions = [
                    CURLOPT_RETURNTRANSFER => false,
                    CURLOPT_TIMEOUT        => 15,
                    CURLOPT_CONNECTTIMEOUT => 7,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_PROTOCOLS      => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                    CURLOPT_RESOLVE        => [$host . ':' . (int)($parsed['port'] ?? ($scheme === 'https' ? 443 : 80)) . ':' . $ip],
                    CURLOPT_WRITEFUNCTION  => static function ($handle, string $chunk) use (&$data, &$downloadTooLarge, $maxBytes): int {
                        $length = strlen($chunk);
                        if (strlen((string)$data) + $length > $maxBytes) {
                            $downloadTooLarge = true;
                            return 0;
                        }
                        $data = (string)$data . $chunk;
                        return $length;
                    },
                    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    CURLOPT_HTTPHEADER     => [
                        'Accept: image/jpeg,image/png,image/webp;q=0.9,*/*;q=0.8',
                        'Accept-Language: en-US,en;q=0.9,vi;q=0.8'
                    ]
                ];

                // Redirects stay disabled so unvalidated redirect targets cannot bypass SSRF checks.

                curl_setopt_array($ch, $curlOptions);
                $execData = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $errNo    = curl_errno($ch);
                $errMsg   = curl_error($ch);
                if (function_exists('curl_close')) @curl_close($ch);

                if ($execData !== false && $httpCode >= 200 && $httpCode < 300) {
                    // Response body was collected with a byte limit by CURLOPT_WRITEFUNCTION.
                } else {
                    $downloadError = $errMsg ?: "HTTP status {$httpCode}";
                }
            } catch (\Throwable $e) {
                $downloadError = 'cURL error: ' . $e->getMessage();
            }
        }

        if (!function_exists('curl_init')) {
            return ['success' => false, 'message' => 'Secure remote image fetching requires the cURL extension.'];
        }
        if ($downloadTooLarge) {
            return ['success' => false, 'message' => 'Downloaded image exceeds maximum allowed file size limit.'];
        }

        if (empty($data)) {
            return ['success' => false, 'message' => 'Failed to download remote image: ' . ($downloadError ?: 'Unable to fetch file content.')];
        }

        if (strlen($data) > $maxBytes) {
            return ['success' => false, 'message' => 'Downloaded image exceeds maximum allowed file size limit.'];
        }

        // Determine Mime Type safely
        $mime = null;
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = @finfo_buffer($finfo, $data);
                if (function_exists('finfo_close')) @finfo_close($finfo);
            }
        }

        $imageSizeInfo = @getimagesizefromstring($data);
        if (!$mime && isset($imageSizeInfo['mime'])) {
            $mime = $imageSizeInfo['mime'];
        }

        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            'image/avif' => 'avif',
            'image/bmp'  => 'bmp',
            'image/x-ms-bmp' => 'bmp'
        ];

        if (!$mime || !isset($mimeToExt[$mime]) || $imageSizeInfo === false) {
            return ['success' => false, 'message' => 'Downloaded remote file is not a valid image format.'];
        }

        $ext     = $mimeToExt[$mime];
        $tmpPath = sys_get_temp_dir() . '/blog_remote_' . uniqid() . '.' . $ext;
        if (@file_put_contents($tmpPath, $data) === false) {
            return ['success' => false, 'message' => 'Failed to write temporary image file on server.'];
        }

        $optimizeResult = ImageOptimizer::optimize($tmpPath, [
            'seo_filename' => $input['seo_filename'] ?? '',
            'keyword'      => $input['keyword'] ?? '',
            'alt_text'     => $input['alt_text'] ?? '',
            'caption'      => $input['caption'] ?? '',
            'quality'      => (int)($input['quality'] ?? 85),
            'force_webp'   => (bool)($input['force_webp'] ?? true)
        ]);

        @unlink($tmpPath);

        if ($optimizeResult['success']) {
            return $optimizeResult;
        }

        // Fallback: write original image data directly if optimizer failed
        $dir = UPLOAD_PATH;
        if (!is_dir($dir)) @mkdir($dir, 0777, true);

        $seoName = ImageOptimizer::buildSeoFilename($input['seo_filename'] ?? '', $input['keyword'] ?? '', $url);
        $name    = $seoName . '-' . time() . '.' . $ext;

        if (@file_put_contents($dir . $name, $data) !== false) {
            return ['success' => true, 'data' => [
                'filename'  => $name,
                'filepath'  => $name,
                'url'       => UPLOAD_URL . $name,
                'width'     => $imageSizeInfo[0] ?? 0,
                'height'    => $imageSizeInfo[1] ?? 0,
                'size_bytes'=> strlen($data),
                'format'    => $ext,
                'alt_text'  => $input['alt_text'] ?? '',
                'caption'   => $input['caption'] ?? '',
                'optimized' => false
            ]];
        }

        return ['success' => false, 'message' => 'Failed to write remote image to disk.'];
    }

    private function success($data) {
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function error($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function forbidden($msg) {
        $this->error("Forbidden: " . $msg, 403);
    }
}
