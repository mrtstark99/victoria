<?php
/**
 * @file app/Controllers/AdminServiceController.php
 * @description Administration controller for CRUD operations on study abroad services.
 *
 * Layer:
 * - Presentation / Admin Controller
 *
 * Responsibilities:
 * - Manage services list with ordering and search in admin dashboard.
 * - Handle creation and editing of service titles, pricing, descriptions, and icons.
 * - Handle deletion of services.
 *
 * Security:
 * - Restricted to authenticated editors and administrators.
 * - CSRF verification on form submissions.
 *
 * Dependencies:
 * - Models\Service
 * - Auth helpers (requireEditor, isAdmin, verifyCSRFToken)
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

namespace Controllers;

use Models\Service;

class AdminServiceController {
    /**
     * Display services table list (/admin/services).
     */
    public function index() {
        requireEditor();

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = defined('ADMIN_POSTS_PER_PAGE') ? ADMIN_POSTS_PER_PAGE : 20;
        $search = trim($_GET['search'] ?? '');

        $result = Service::getPaginated($page, $perPage, $search);

        view('admin/services', [
            'services' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages'],
            'search' => $search,
            'page_title' => 'Quản lý Dịch vụ - Victoria Universal'
        ]);
    }

    /**
     * Show service creation form (/admin/services/create).
     */
    public function create() {
        requireEditor();

        view('admin/service_form', [
            'service' => null,
            'page_title' => 'Thêm Dịch vụ Mới - Victoria Universal'
        ]);
    }

    /**
     * Store newly created service in database.
     */
    public function store() {
        requireEditor();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/services', 'Phương thức không được hỗ trợ', 'error');
        }

        $csrfToken = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCSRFToken($csrfToken)) {
            redirect('/admin/services', 'Mã bảo mật không hợp lệ', 'error');
        }

        $title = trim(strip_tags($_POST['title'] ?? ''));
        $slug = trim(strip_tags($_POST['slug'] ?? ''));
        $name = trim(strip_tags($_POST['name'] ?? $title));
        $description = trim(strip_tags($_POST['description'] ?? ''));
        $content = trim($_POST['content'] ?? '');
        $icon = trim(strip_tags($_POST['icon'] ?? 'bi-briefcase'));
        $price = (float)($_POST['price'] ?? 0);
        $packages = $this->readPackagesOrRedirect($_POST['packages_json'] ?? '[]', '/admin/services/create');
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';

        if (empty($title)) {
            redirect('/admin/services/create', 'Tiêu đề dịch vụ không được để trống', 'error');
        }

        try {
            $slug = Service::validateEnglishSlug($slug);
        } catch (\InvalidArgumentException $exception) {
            redirect('/admin/services/create', 'Slug phải viết bằng chữ tiếng Anh thường, số và dấu gạch nối.', 'error');
        }

        // Check unique slug
        $existing = Service::findBySlug($slug);
        if ($existing) redirect('/admin/services/create', 'Slug đã tồn tại, vui lòng chọn slug khác.', 'error');

        Service::create([
            'name' => $name,
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'content' => $content,
            'icon' => $icon,
            'price' => $price,
            'packages' => $packages,
            'display_order' => $displayOrder,
            'status' => $status
        ]);

        redirect('/admin/services', 'Thêm dịch vụ thành công!', 'success');
    }

    /**
     * Show service edit form (/admin/services/edit/{id}).
     *
     * @param int $id
     */
    public function edit($id) {
        requireEditor();

        $service = Service::findById((int)$id);
        if (!$service) {
            redirect('/admin/services', 'Dịch vụ không tồn tại', 'error');
        }

        view('admin/service_form', [
            'service' => $service,
            'page_title' => 'Chỉnh sửa Dịch vụ - ' . htmlspecialchars($service['title'])
        ]);
    }

    /**
     * Update existing service in database.
     *
     * @param int $id
     */
    public function update($id) {
        requireEditor();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/services', 'Phương thức không được hỗ trợ', 'error');
        }

        $csrfToken = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCSRFToken($csrfToken)) {
            redirect('/admin/services', 'Mã bảo mật không hợp lệ', 'error');
        }

        $service = Service::findById((int)$id);
        if (!$service) {
            redirect('/admin/services', 'Dịch vụ không tồn tại', 'error');
        }

        $title = trim(strip_tags($_POST['title'] ?? ''));
        $slug = trim(strip_tags($_POST['slug'] ?? ''));
        $name = trim(strip_tags($_POST['name'] ?? $title));
        $description = trim(strip_tags($_POST['description'] ?? ''));
        $content = trim($_POST['content'] ?? '');
        $icon = trim(strip_tags($_POST['icon'] ?? 'bi-briefcase'));
        $price = (float)($service['price'] ?? 0);
        $packages = $this->readPackagesOrRedirect($_POST['packages_json'] ?? '[]', '/admin/services/edit/' . $id);
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';

        if (empty($title)) {
            redirect('/admin/services/edit/' . $id, 'Tiêu đề không được để trống', 'error');
        }

        try {
            $slug = Service::validateEnglishSlug($slug);
        } catch (\InvalidArgumentException $exception) {
            redirect('/admin/services/edit/' . $id, 'Slug phải viết bằng chữ tiếng Anh thường, số và dấu gạch nối.', 'error');
        }

        // Verify slug uniqueness if changed
        if ($slug !== $service['slug']) {
            $existing = Service::findBySlug($slug);
            if ($existing && (int)$existing['id'] !== (int)$id) {
                redirect('/admin/services/edit/' . $id, 'Slug đã tồn tại, vui lòng chọn slug khác.', 'error');
            }
        }

        Service::update((int)$id, [
            'name' => $name,
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'content' => $content,
            'icon' => $icon,
            'price' => $price,
            'packages' => $packages,
            'display_order' => $displayOrder,
            'status' => $status
        ]);

        redirect('/admin/services', 'Cập nhật dịch vụ thành công!', 'success');
    }

    /**
     * Delete service.
     *
     * @param int $id
     */
    public function delete($id) {
        requireAdmin();

        $id = (int)$id;
        if ($id > 0) {
            Service::delete($id);
            redirect('/admin/services', 'Đã xóa dịch vụ thành công.', 'success');
        } else {
            redirect('/admin/services', 'ID không hợp lệ.', 'error');
        }
    }

    private function readPackagesOrRedirect(string $json, string $backUrl): array {
        $packages = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) redirect($backUrl, 'JSON các gói hồ sơ không hợp lệ.', 'error');
        try {
            return Service::normalizePackages($packages);
        } catch (\InvalidArgumentException $exception) {
            redirect($backUrl, 'Dữ liệu gói hồ sơ không hợp lệ: ' . $exception->getMessage(), 'error');
        }
    }
}
