<?php
/**
 * @file app/Controllers/AdminContactController.php
 * @description Administration controller for reviewing, updating, and managing student contact inquiries.
 *
 * Layer:
 * - Presentation / Admin Controller
 *
 * Responsibilities:
 * - Render paginated contact inquiries dashboard for editors and admins.
 * - Update inquiry status (new, read, replied, processing, completed, archived).
 * - Save internal consultation notes.
 * - Securely delete obsolete contact records.
 *
 * Security:
 * - Access restricted to authenticated editors and administrators.
 * - CSRF verification on all status updates and deletion actions.
 *
 * Dependencies:
 * - Models\Contact
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

use Models\Contact;

class AdminContactController {
    /**
     * Display paginated contact inquiries list (/admin/contacts).
     */
    public function index() {
        requireEditor();

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = defined('ADMIN_POSTS_PER_PAGE') ? ADMIN_POSTS_PER_PAGE : 20;
        $statusFilter = trim($_GET['status'] ?? '');
        $search = trim($_GET['search'] ?? '');

        $result = Contact::getPaginated($page, $perPage, $statusFilter, $search);

        view('admin/contacts', [
            'contacts' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages'],
            'status_filter' => $statusFilter,
            'search' => $search,
            'page_title' => 'Quản lý Liên hệ & Tư vấn - Victoria Universal'
        ]);
    }

    /**
     * Update contact status and internal notes.
     */
    public function updateStatus() {
        requireEditor();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/contacts', 'Phương thức không được hỗ trợ', 'error');
        }

        $csrfToken = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCSRFToken($csrfToken)) {
            redirect('/admin/contacts', 'Mã bảo mật không hợp lệ', 'error');
        }

        $id = (int)($_POST['contact_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'new');
        $notes = trim($_POST['notes'] ?? '');

        $allowedStatuses = ['new', 'read', 'replied', 'processing', 'completed', 'archived'];
        if (!in_array($status, $allowedStatuses, true)) {
            redirect('/admin/contacts', 'Trạng thái không hợp lệ', 'error');
        }

        Contact::updateStatus($id, $status, $notes !== '' ? $notes : null);
        redirect('/admin/contacts', 'Đã cập nhật trạng thái liên hệ thành công.', 'success');
    }

    /**
     * Delete contact inquiry.
     *
     * @param int $id
     */
    public function delete($id) {
        requireAdmin();

        $id = (int)$id;
        if ($id > 0) {
            Contact::delete($id);
            redirect('/admin/contacts', 'Đã xóa liên hệ thành công.', 'success');
        } else {
            redirect('/admin/contacts', 'ID không hợp lệ.', 'error');
        }
    }
}
