<?php
/**
 * Admin Category Controller
 */

namespace Controllers;

use Models\Category;

class AdminCategoryController {
    public function index() {
        requireEditor();
        $categories = Category::getAll();
        view('admin/categories', [
            'categories' => $categories,
            'page_title' => 'Quản lý danh mục'
        ]);
    }

    public function create() {
        requireEditor();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                redirect('/admin/categories', 'Phiên làm việc hết hạn.', 'error');
            }
            $name = sanitizeInput($_POST['name'] ?? '');
            $slug = sanitizeInput($_POST['slug'] ?? '');
            $desc = sanitizeInput($_POST['description'] ?? '');

            if (empty($name)) {
                redirect('/admin/categories', 'Tên danh mục không được để trống.', 'error');
            }
            if (empty($slug)) $slug = createSlug($name);

            Category::create($name, $slug, $desc);
            BlogController::generateSitemapFile();
            redirect('/admin/categories', 'Tạo danh mục mới thành công.');
        }
    }

    public function edit($id) {
        requireEditor();
        $category = Category::findById($id);
        if (!$category) redirect('/admin/categories', 'Danh mục không tồn tại.', 'error');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                redirect('/admin/categories', 'Phiên làm việc hết hạn.', 'error');
            }
            $name = sanitizeInput($_POST['name'] ?? '');
            $slug = sanitizeInput($_POST['slug'] ?? '');
            $desc = sanitizeInput($_POST['description'] ?? '');

            if (empty($name)) {
                redirect('/admin/categories', 'Tên danh mục không được để trống.', 'error');
            }
            if (empty($slug)) $slug = createSlug($name);

            Category::update($id, $name, $slug, $desc);
            BlogController::generateSitemapFile();
            redirect('/admin/categories', 'Cập nhật danh mục thành công.');
        }
    }

    public function delete($id) {
        requireEditor();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/categories', 'Thao tác không hợp lệ. Yêu cầu phương thức POST.', 'error');
        }
        if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            redirect('/admin/categories', 'Phiên làm việc hết hạn.', 'error');
        }

        Category::delete($id);
        BlogController::generateSitemapFile();
        redirect('/admin/categories', 'Xóa danh mục thành công.');
    }
}
