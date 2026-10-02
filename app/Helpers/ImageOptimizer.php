<?php
/**
 * Image Optimizer — WebP conversion, SEO filename, compression
 */

namespace Helpers;

class ImageOptimizer {
    /**
     * Optimize an uploaded image for SEO
     *
     * @param string $sourcePath Full path to source image
     * @param array  $options    Options: seo_filename, alt_text, quality, max_width, max_height, force_webp
     * @return array Result with optimized file info
     */
    public static function optimize(string $sourcePath, array $options = []): array {
        if (!file_exists($sourcePath)) {
            return ['success' => false, 'error' => 'Source file not found.'];
        }

        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            return ['success' => false, 'error' => 'File is not a valid image.'];
        }

        $origWidth = $imageInfo[0];
        $origHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        $quality = (int)($options['quality'] ?? 85);
        $maxWidth = (int)($options['max_width'] ?? 1920);
        $maxHeight = (int)($options['max_height'] ?? 1080);
        $forceWebp = (bool)($options['force_webp'] ?? true);

        // Build SEO-friendly filename
        $seoFilename = self::buildSeoFilename(
            $options['seo_filename'] ?? '',
            $options['keyword'] ?? '',
            $sourcePath
        );

        // Determine target format
        $targetFormat = $mimeType;
        $targetExt = pathinfo($sourcePath, PATHINFO_EXTENSION);

        if ($forceWebp && self::supportsWebP()) {
            $targetFormat = 'image/webp';
            $targetExt = 'webp';
        }

        $outputFilename = $seoFilename . '-' . time() . '.' . $targetExt;
        $outputDir = defined('UPLOAD_PATH') ? UPLOAD_PATH : dirname($sourcePath) . '/';
        $outputPath = $outputDir . $outputFilename;

        // Process image
        $result = self::processImage($sourcePath, $outputPath, $mimeType, $targetFormat, $quality, $maxWidth, $maxHeight);

        if (!$result['success']) {
            // Fallback: copy original file with SEO filename
            $fallbackExt = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $outputFilename = $seoFilename . '-' . time() . '.' . $fallbackExt;
            $outputPath = $outputDir . $outputFilename;

            if (copy($sourcePath, $outputPath)) {
                return [
                    'success' => true,
                    'data' => [
                        'filename' => $outputFilename,
                        'filepath' => $outputFilename,
                        'url' => (defined('UPLOAD_URL') ? UPLOAD_URL : '/uploads/') . $outputFilename,
                        'width' => $origWidth,
                        'height' => $origHeight,
                        'size_bytes' => filesize($outputPath),
                        'format' => $fallbackExt,
                        'alt_text' => $options['alt_text'] ?? '',
                        'caption' => $options['caption'] ?? '',
                        'optimized' => false
                    ]
                ];
            }

            return ['success' => false, 'error' => 'Failed to save optimized image.'];
        }

        // Get final dimensions
        $finalInfo = @getimagesize($outputPath);

        return [
            'success' => true,
            'data' => [
                'filename' => $outputFilename,
                'filepath' => $outputFilename,
                'url' => (defined('UPLOAD_URL') ? UPLOAD_URL : '/uploads/') . $outputFilename,
                'width' => $finalInfo[0] ?? $origWidth,
                'height' => $finalInfo[1] ?? $origHeight,
                'size_bytes' => filesize($outputPath),
                'original_size_bytes' => filesize($sourcePath),
                'format' => $targetExt,
                'alt_text' => $options['alt_text'] ?? '',
                'caption' => $options['caption'] ?? '',
                'optimized' => true
            ]
        ];
    }

    /**
     * Build SEO-friendly filename from keyword/title
     */
    public static function buildSeoFilename(string $seoFilename, string $keyword = '', string $fallbackPath = ''): string {
        $name = $seoFilename ?: $keyword;

        if (empty($name) && $fallbackPath) {
            $name = pathinfo($fallbackPath, PATHINFO_FILENAME);
        }

        if (empty($name)) {
            return 'image-' . uniqid();
        }

        // Use createSlug if available, otherwise basic slugify
        if (function_exists('createSlug')) {
            return createSlug($name);
        }

        $slug = mb_strtolower($name, 'UTF-8');
        $slug = preg_replace('/[^a-z0-9-]/u', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-') ?: 'image-' . uniqid();
    }

    /**
     * Check if PHP supports WebP conversion
     */
    public static function supportsWebP(): bool {
        if (extension_loaded('imagick') && class_exists('\Imagick')) {
            try {
                $formats = \Imagick::queryFormats('WEBP');
                if (!empty($formats)) return true;
            } catch (\Throwable $e) {}
        }

        if (extension_loaded('gd')) {
            return function_exists('imagewebp');
        }

        return false;
    }

    /**
     * Process image: resize + convert format + compress
     */
    private static function processImage(string $src, string $dst, string $srcMime, string $dstFormat, int $quality, int $maxW, int $maxH): array {
        try {
            // Try Imagick first
            if (extension_loaded('imagick') && class_exists('\Imagick')) {
                $res = self::processWithImagick($src, $dst, $dstFormat, $quality, $maxW, $maxH);
                if ($res['success']) return $res;
            }

            // Fall back to GD
            if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
                return self::processWithGD($src, $dst, $srcMime, $dstFormat, $quality, $maxW, $maxH);
            }

            return ['success' => false, 'error' => 'Neither GD nor Imagick extension is available on server.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Image processing exception: ' . $e->getMessage()];
        }
    }

    private static function processWithImagick(string $src, string $dst, string $dstFormat, int $quality, int $maxW, int $maxH): array {
        try {
            $img = new \Imagick($src);
            if (method_exists($img, 'stripImage')) {
                @$img->stripImage(); // Remove metadata
            }

            // Resize if needed
            $w = $img->getImageWidth();
            $h = $img->getImageHeight();
            if ($w > $maxW || $h > $maxH) {
                $img->resizeImage($maxW, $maxH, \Imagick::FILTER_LANCZOS, 1, true);
            }

            // Set format
            if ($dstFormat === 'image/webp') {
                $img->setImageFormat('webp');
                $img->setImageCompressionQuality($quality);
            } else {
                $img->setImageCompressionQuality($quality);
            }

            $img->writeImage($dst);
            $img->destroy();

            return ['success' => true];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Imagick error: ' . $e->getMessage()];
        }
    }

    private static function processWithGD(string $src, string $dst, string $srcMime, string $dstFormat, int $quality, int $maxW, int $maxH): array {
        try {
            $srcImage = match ($srcMime) {
                'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($src) : false,
                'image/png'  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($src)  : false,
                'image/gif'  => function_exists('imagecreatefromgif')  ? @imagecreatefromgif($src)  : false,
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($src) : false,
                default => false
            };

            if (!$srcImage && function_exists('imagecreatefromstring')) {
                $raw = @file_get_contents($src);
                if ($raw !== false) {
                    $srcImage = @imagecreatefromstring($raw);
                }
            }

            if (!$srcImage) {
                return ['success' => false, 'error' => 'GD cannot read source image format.'];
            }

            $w = function_exists('imagesx') ? imagesx($srcImage) : 0;
            $h = function_exists('imagesy') ? imagesy($srcImage) : 0;

            if ($w <= 0 || $h <= 0) {
                if (function_exists('imagedestroy')) @imagedestroy($srcImage);
                return ['success' => false, 'error' => 'Invalid image dimensions.'];
            }

            // Resize if needed
            if (($w > $maxW || $h > $maxH) && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
                $ratio = min($maxW / $w, $maxH / $h);
                $newW = (int)round($w * $ratio);
                $newH = (int)round($h * $ratio);
                $resized = @imagecreatetruecolor($newW, $newH);

                if ($resized) {
                    // Preserve transparency for PNG/WebP
                    if (in_array($srcMime, ['image/png', 'image/webp'])) {
                        if (function_exists('imagealphablending')) @imagealphablending($resized, false);
                        if (function_exists('imagesavealpha')) @imagesavealpha($resized, true);
                    }

                    @imagecopyresampled($resized, $srcImage, 0, 0, 0, 0, $newW, $newH, $w, $h);
                    if (function_exists('imagedestroy')) @imagedestroy($srcImage);
                    $srcImage = $resized;
                }
            }

            // Write output
            $written = false;
            if ($dstFormat === 'image/webp' && function_exists('imagewebp')) {
                $written = @imagewebp($srcImage, $dst, $quality);
            } elseif ($dstFormat === 'image/jpeg' && function_exists('imagejpeg')) {
                $written = @imagejpeg($srcImage, $dst, $quality);
            } elseif ($dstFormat === 'image/png' && function_exists('imagepng')) {
                $written = @imagepng($srcImage, $dst, (int)round(9 - ($quality / 100 * 9)));
            }

            // Fallback: if WebP writing failed (e.g. imagewebp missing on GD), write JPEG/PNG instead
            if (!$written && function_exists('imagejpeg')) {
                $dstJpg = preg_replace('/\.(webp|png|gif)$/i', '.jpg', $dst);
                $written = @imagejpeg($srcImage, $dstJpg, $quality);
            }

            if (function_exists('imagedestroy')) @imagedestroy($srcImage);

            return $written ? ['success' => true] : ['success' => false, 'error' => 'GD failed to write output image.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'GD processing exception: ' . $e->getMessage()];
        }
    }
}
