<?php
/**
 * Admin Page Controller - CMS Static & Dynamic Pages Management
 */

namespace Controllers;

use Models\Page;

class AdminPageController {
    public function index() {
        requireEditor();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;

        $filters = [];
        if (!empty($_GET['search'])) {
            $filters['search'] = trim($_GET['search']);
        }
        if (!empty($_GET['status'])) {
            $filters['status'] = trim($_GET['status']);
        }

        $pages = Page::getPaginated($page, $perPage, $filters);
        $total = Page::count($filters);
        $stats = Page::getStats();

        view('admin/pages', [
            'pages' => $pages,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'stats' => $stats,
            'filters' => $filters,
            'page_title' => 'Quản lý Trang (Pages)'
        ]);
    }

    public function create() {
        requireEditor();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                $errors[] = 'Phiên làm việc hết hạn.';
            } else {
                $title = sanitizeInput($_POST['title'] ?? '');
                $slug = sanitizeInput($_POST['slug'] ?? '');
                $content = $_POST['content'] ?? '';
                $template = sanitizeInput($_POST['template'] ?? 'default');

                if (empty($title)) $errors[] = 'Tiêu đề trang không được để trống.';
                if (empty($slug)) $slug = createSlug($title);

                if (!$errors) {
                    $slug = createSlug($slug);
                    $existing = Page::findBySlug($slug);
                    if ($existing) {
                        $slug .= '-' . time();
                    }

                    $status = sanitizeInput($_POST['status'] ?? 'draft');
                    if (!in_array($status, ['draft', 'published', 'archived', 'ai_draft', 'pending_review', 'approved'], true)) {
                        $status = 'draft';
                    }

                    $featuredImage = trim($_POST['featured_image'] ?? '');
                    if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
                        $file = $_FILES['featured_image_file'];
                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $file['tmp_name']);
                        finfo_close($finfo);
                        $imageInfo = @getimagesize($file['tmp_name']);

                        if (in_array($ext, ALLOWED_IMAGE_TYPES, true) && 
                            in_array($mime, ALLOWED_IMAGE_MIMES, true) && 
                            $imageInfo !== false && 
                            $file['size'] <= MAX_FILE_SIZE) {
                            
                            $uploadDir = UPLOAD_PATH . 'pages/';
                            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                            $filename = 'page_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                                $featuredImage = '/uploads/pages/' . $filename;
                            }
                        } else {
                            $errors[] = 'File tải lên không hợp lệ hoặc vượt quá dung lượng cho phép.';
                        }
                    }

                    if (!$errors) {
                        $data = [
                            'title' => $title,
                            'slug' => $slug,
                            'excerpt' => !empty($_POST['excerpt']) ? sanitizeInput($_POST['excerpt']) : getExcerpt($content),
                            'content' => $content,
                            'template' => in_array($template, ['default', 'fullwidth', 'contact', 'landing'], true) ? $template : 'default',
                            'featured_image' => $featuredImage,
                            'author_id' => $_SESSION['user_id'] ?? 1,
                            'status' => $status,
                            'sort_order' => (int)($_POST['sort_order'] ?? 0),
                            'meta_title' => sanitizeInput($_POST['meta_title'] ?? ''),
                            'meta_description' => sanitizeInput($_POST['meta_description'] ?? ''),
                            'meta_keywords' => sanitizeInput($_POST['meta_keywords'] ?? ''),
                            'custom_schema_json' => trim($_POST['custom_schema_json'] ?? ''),
                            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
                            'changed_by' => $_SESSION['user_name'] ?? 'Admin'
                        ];

                        Page::create($data);
                        SitemapController::generateSitemapFile();
                        redirect('/admin/pages', 'Tạo trang mới thành công.');
                    }
                }
            }
        }

        view('admin/page_form', [
            'errors' => $errors,
            'is_edit' => false,
            'page_title' => 'Tạo trang mới'
        ]);
    }

    public function edit($id) {
        requireEditor();
        $page = Page::findById($id);
        if (!$page) redirect('/admin/pages', 'Trang không tồn tại.', 'error');

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                $errors[] = 'Phiên làm việc hết hạn.';
            } else {
                // Optimistic Locking check
                if (empty($_POST['expected_updated_at']) || $page['updated_at'] !== $_POST['expected_updated_at']) {
                    $errors[] = 'Dữ liệu đã bị thay đổi bởi người khác. Vui lòng tải lại trang.';
                }

                $title = sanitizeInput($_POST['title'] ?? '');
                $slug = sanitizeInput($_POST['slug'] ?? '');
                $content = $_POST['content'] ?? '';
                $template = sanitizeInput($_POST['template'] ?? 'default');

                if (empty($title)) $errors[] = 'Tiêu đề trang không được để trống.';
                if (empty($slug)) $slug = createSlug($title);

                if (!$errors) {
                    $slug = createSlug($slug);
                    $existing = Page::findBySlug($slug);
                    if ($existing && (int)$existing['id'] !== (int)$id) {
                        $slug .= '-' . time();
                    }

                    $status = sanitizeInput($_POST['status'] ?? 'draft');
                    if (!in_array($status, ['draft', 'published', 'archived', 'ai_draft', 'pending_review', 'approved'], true)) {
                        $status = 'draft';
                    }

                    $oldImage = $page['featured_image'] ?? '';
                    $featuredImage = trim($_POST['featured_image'] ?? $oldImage);
                    $newImageUploaded = false;

                    if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
                        $file = $_FILES['featured_image_file'];
                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $file['tmp_name']);
                        finfo_close($finfo);
                        $imageInfo = @getimagesize($file['tmp_name']);

                        if (in_array($ext, ALLOWED_IMAGE_TYPES, true) && 
                            in_array($mime, ALLOWED_IMAGE_MIMES, true) && 
                            $imageInfo !== false && 
                            $file['size'] <= MAX_FILE_SIZE) {
                            
                            $uploadDir = UPLOAD_PATH . 'pages/';
                            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                            $filename = 'page_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                                $featuredImage = '/uploads/pages/' . $filename;
                                $newImageUploaded = true;
                            }
                        } else {
                            $errors[] = 'File tải lên không hợp lệ hoặc vượt quá dung lượng cho phép.';
                        }
                    }

                    if (!$errors) {
                        $data = [
                            'title' => $title,
                            'slug' => $slug,
                            'excerpt' => !empty($_POST['excerpt']) ? sanitizeInput($_POST['excerpt']) : getExcerpt($content),
                            'content' => $content,
                            'template' => in_array($template, ['default', 'fullwidth', 'contact', 'landing'], true) ? $template : 'default',
                            'featured_image' => $featuredImage,
                            'status' => $status,
                            'sort_order' => (int)($_POST['sort_order'] ?? 0),
                            'meta_title' => sanitizeInput($_POST['meta_title'] ?? ''),
                            'meta_description' => sanitizeInput($_POST['meta_description'] ?? ''),
                            'meta_keywords' => sanitizeInput($_POST['meta_keywords'] ?? ''),
                            'custom_schema_json' => trim($_POST['custom_schema_json'] ?? ''),
                            'changed_by' => $_SESSION['user_name'] ?? 'Admin'
                        ];

                        if (!Page::update($id, $data, $_POST['expected_updated_at'])) {
                            if ($newImageUploaded) self::deleteLocalFile($featuredImage);
                            $errors[] = 'Dữ liệu đã bị thay đổi bởi người khác. Vui lòng tải lại trang.';
                        } else {
                            if ($newImageUploaded || ($featuredImage !== $oldImage && !empty($oldImage))) self::deleteLocalFile($oldImage);
                            SitemapController::generateSitemapFile();
                            redirect('/admin/pages', 'Cập nhật trang thành công.');
                        }
                    }
                }
            }
        }

        $revisions = Page::getRevisions($id, 5);

        view('admin/page_form', [
            'page' => $page,
            'revisions' => $revisions,
            'errors' => $errors,
            'is_edit' => true,
            'page_title' => 'Chỉnh sửa trang: ' . $page['title']
        ]);
    }

    public function delete($id) {
        requireEditor();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/pages', 'Thao tác không hợp lệ. Yêu cầu phương thức POST.', 'error');
        }
        if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            redirect('/admin/pages', 'Phiên làm việc hết hạn.', 'error');
        }

        $page = Page::findById($id);
        if ($page) {
            if (!empty($page['featured_image'])) {
                self::deleteLocalFile($page['featured_image']);
            }
            Page::delete($id);
            SitemapController::generateSitemapFile();
        }

        redirect('/admin/pages', 'Xóa trang thành công.');
    }

    private static function deleteLocalFile($relativePath) {
        if (empty($relativePath) || !is_string($relativePath)) return;
        if (strpos($relativePath, '/uploads/') === 0) {
            $fullPath = APP_ROOT . '/public' . $relativePath;
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    }
}
