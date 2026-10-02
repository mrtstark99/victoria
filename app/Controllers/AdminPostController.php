<?php
/**
 * Admin Post Controller
 */

namespace Controllers;

use Models\Post;
use Models\Category;

class AdminPostController {
    public function index() {
        requireEditor();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $filters = [];
        if (!empty($_GET['search'])) {
            $filters['search'] = trim($_GET['search']);
        }
        if (!empty($_GET['category_id'])) {
            $filters['category_id'] = (int)$_GET['category_id'];
        }
        if (!empty($_GET['status'])) {
            $filters['status'] = trim($_GET['status']);
        }
        
        $posts = Post::getPaginated($page, $perPage, $filters);
        $total = Post::count($filters);
        $stats = Post::getStats();
        $categories = Category::getAll();

        view('admin/posts', [
            'posts' => $posts,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'stats' => $stats,
            'categories' => $categories,
            'filters' => $filters,
            'page_title' => 'Quản lý bài viết'
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
                $category_id = (int)($_POST['category_id'] ?? 0);
                $content = $_POST['content'] ?? '';
                
                if (empty($title)) $errors[] = 'Tiêu đề không được trống.';
                if ($category_id <= 0) $errors[] = 'Vui lòng chọn danh mục.';
                $contentValidation = validatePostElementContent((string)$content);
                if (!$contentValidation['valid']) {
                    $errors = array_merge($errors, $contentValidation['errors']);
                }

                if (!$errors) {
                    $slug = createSlug($title);
                    // Check duplicate slug
                    $db = \Database::getInstance();
                    $stmt = $db->prepare("SELECT id FROM posts WHERE slug = ?");
                    $stmt->execute([$slug]);
                    if ($stmt->fetch()) {
                        $slug .= '-' . time();
                    }

                    $status = sanitizeInput($_POST['status'] ?? 'draft');
                    if (!in_array($status, ALLOWED_POST_STATUSES, true)) {
                        $status = 'draft';
                    }

                    $featuredImage = trim($_POST['featured_image'] ?? '');
                    if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
                        $file = $_FILES['featured_image_file'];
                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        
                        // Validate extension, size, and real MIME
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $file['tmp_name']);
                        finfo_close($finfo);
                        $imageInfo = @getimagesize($file['tmp_name']);

                        if (in_array($ext, ALLOWED_IMAGE_TYPES, true) && 
                            in_array($mime, ALLOWED_IMAGE_MIMES, true) && 
                            $imageInfo !== false && 
                            $file['size'] <= MAX_FILE_SIZE) {
                            
                            $uploadDir = UPLOAD_PATH . 'posts/';
                            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                            $filename = 'post_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                                $featuredImage = '/uploads/posts/' . $filename;
                            }
                        } else {
                            $errors[] = 'File tải lên không phải ảnh hợp lệ hoặc vượt quá dung lượng cho phép.';
                        }
                    }

                    if (!$errors) {
                        $data = [
                            'title' => $title,
                            'slug' => $slug,
                            'excerpt' => !empty($_POST['excerpt']) ? sanitizeInput($_POST['excerpt']) : getExcerpt($content),
                            'content' => sanitizeHtml($content),
                            'featured_image' => $featuredImage,
                            'category_id' => $category_id,
                            'author_id' => $_SESSION['user_id'],
                            'status' => $status,
                            'featured' => isset($_POST['featured']) ? 1 : 0,
                            'meta_title' => sanitizeInput($_POST['meta_title'] ?? ''),
                            'meta_description' => sanitizeInput($_POST['meta_description'] ?? ''),
                            'meta_keywords' => sanitizeInput($_POST['meta_keywords'] ?? ''),
                            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : ($_POST['published_at'] ?? null),
                            'changed_by' => $_SESSION['user_name']
                        ];
                        
                        Post::create($data);
                        // Refresh sitemap
                        BlogController::generateSitemapFile();
                        redirect('/admin/posts', 'Thêm bài viết mới thành công.');
                    }
                }
            }
        }

        $categories = Category::getAll();
        view('admin/post_form', [
            'categories' => $categories,
            'errors' => $errors,
            'is_edit' => false,
            'page_title' => 'Viết bài mới'
        ]);
    }

    public function edit($id) {
        requireEditor();
        $post = Post::findById($id);
        if (!$post) redirect('/admin/posts', 'Bài viết không tồn tại.', 'error');

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                $errors[] = 'Phiên làm việc hết hạn.';
            } else {
                // Optimistic Locking check
                if (empty($_POST['expected_updated_at']) || $post['updated_at'] !== $_POST['expected_updated_at']) {
                    $errors[] = 'Dữ liệu đã bị thay đổi bởi người khác. Vui lòng tải lại trang và cập nhật lại.';
                }

                $title = sanitizeInput($_POST['title'] ?? '');
                $category_id = (int)($_POST['category_id'] ?? 0);
                $content = $_POST['content'] ?? '';

                if (empty($title)) $errors[] = 'Tiêu đề không được trống.';
                $contentValidation = validatePostElementContent((string)$content);
                if (!$contentValidation['valid']) {
                    $errors = array_merge($errors, $contentValidation['errors']);
                }

                if (!$errors) {
                    $status = sanitizeInput($_POST['status'] ?? 'draft');
                    if (!in_array($status, ALLOWED_POST_STATUSES, true)) {
                        $status = 'draft';
                    }

                    $oldImage = $post['featured_image'] ?? '';
                    $featuredImage = trim($_POST['featured_image'] ?? $oldImage);
                    $newImageUploaded = false;

                    if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
                        $file = $_FILES['featured_image_file'];
                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                        // Validate extension, size, and real MIME
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $file['tmp_name']);
                        finfo_close($finfo);
                        $imageInfo = @getimagesize($file['tmp_name']);

                        if (in_array($ext, ALLOWED_IMAGE_TYPES, true) && 
                            in_array($mime, ALLOWED_IMAGE_MIMES, true) && 
                            $imageInfo !== false && 
                            $file['size'] <= MAX_FILE_SIZE) {
                            
                            $uploadDir = UPLOAD_PATH . 'posts/';
                            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                            $filename = 'post_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                                $featuredImage = '/uploads/posts/' . $filename;
                                $newImageUploaded = true;
                            }
                        } else {
                            $errors[] = 'File tải lên không phải ảnh hợp lệ hoặc vượt quá dung lượng cho phép.';
                        }
                    }

                    if (!$errors) {
                        $data = [
                            'title' => $title,
                            'slug' => createSlug($_POST['slug'] ?: $title),
                            'excerpt' => !empty($_POST['excerpt']) ? sanitizeInput($_POST['excerpt']) : getExcerpt($content),
                            'content' => sanitizeHtml($content),
                            'category_id' => $category_id ?: null,
                            'status' => $status,
                            'featured' => isset($_POST['featured']) ? 1 : 0,
                            'meta_title' => sanitizeInput($_POST['meta_title'] ?? ''),
                            'meta_description' => sanitizeInput($_POST['meta_description'] ?? ''),
                            'meta_keywords' => sanitizeInput($_POST['meta_keywords'] ?? ''),
                            'featured_image' => $featuredImage,
                            'published_at' => $status === 'published' ? ($post['published_at'] ?: date('Y-m-d H:i:s')) : $post['published_at'],
                            'author_id' => $post['author_id'],
                            'changed_by' => $_SESSION['user_name']
                        ];
                        
                        if (!Post::update($id, $data, $_POST['expected_updated_at'])) {
                            if ($newImageUploaded) self::deleteLocalFile($featuredImage);
                            $errors[] = 'Dữ liệu đã bị thay đổi bởi người khác. Vui lòng tải lại trang.';
                        } else {
                            if ($newImageUploaded || ($featuredImage !== $oldImage && !empty($oldImage))) self::deleteLocalFile($oldImage);
                            BlogController::generateSitemapFile();
                            redirect('/admin/posts', 'Cập nhật bài viết thành công.');
                        }
                    }
                }
            }
        }

        $categories = Category::getAll();
        view('admin/post_form', [
            'post' => $post,
            'categories' => $categories,
            'errors' => $errors,
            'is_edit' => true,
            'page_title' => 'Chỉnh sửa bài viết'
        ]);
    }

    public function delete($id) {
        requireEditor();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/posts', 'Thao tác không hợp lệ. Yêu cầu phương thức POST.', 'error');
        }
        if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            redirect('/admin/posts', 'Phiên làm việc hết hạn.', 'error');
        }

        $post = Post::findById($id);
        if ($post) {
            if (!empty($post['featured_image'])) {
                self::deleteLocalFile($post['featured_image']);
            }
            $db = \Database::getInstance();
            $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            // Refresh sitemap
            BlogController::generateSitemapFile();
        }

        redirect('/admin/posts', 'Xóa bài viết thành công.');
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
