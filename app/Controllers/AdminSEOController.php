<?php
/**
 * Admin SEO keywords planner controller
 */

namespace Controllers;

use Models\SEO;

class AdminSEOController {
    public function index() {
        requireEditor();
        $clusters = SEO::getClusters();
        $keywords = SEO::getKeywords();

        view('admin/seo', [
            'clusters' => $clusters,
            'keywords' => $keywords,
            'page_title' => 'Kế hoạch SEO & Từ khóa'
        ]);
    }

    public function createCluster() {
        requireEditor();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                redirect('/admin/seo', 'Phiên làm việc hết hạn.', 'error');
            }
            $month = sanitizeInput($_POST['planning_month'] ?? '');
            $name = sanitizeInput($_POST['name'] ?? '');
            $pillarTitle = sanitizeInput($_POST['pillar_title'] ?? '');
            $pillarUrl = sanitizeInput($_POST['pillar_url'] ?? '');
            $desc = sanitizeInput($_POST['description'] ?? '');

            if (empty($month) || empty($name) || empty($pillarTitle)) {
                redirect('/admin/seo', 'Vui lòng điền đầy đủ thông tin bắt buộc.', 'error');
            }

            SEO::createCluster($month, $name, $pillarTitle, $pillarUrl, $desc);
            redirect('/admin/seo', 'Đã thêm cụm chủ đề Topic Cluster mới thành công.');
        }
    }

    public function createKeyword() {
        requireEditor();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                redirect('/admin/seo', 'Phiên làm việc hết hạn.', 'error');
            }
            $month = sanitizeInput($_POST['planning_month'] ?? '');
            $keyword = sanitizeInput($_POST['keyword'] ?? '');
            $clusterId = (int)($_POST['cluster_id'] ?? 0);
            
            if (empty($month) || empty($keyword)) {
                redirect('/admin/seo', 'Tháng lập kế hoạch và Từ khóa không được bỏ trống.', 'error');
            }

            $data = [
                'planning_month' => $month,
                'keyword' => $keyword,
                'cluster_id' => $clusterId > 0 ? $clusterId : null,
                'intent' => sanitizeInput($_POST['intent'] ?? 'informational'),
                'target_url' => sanitizeInput($_POST['target_url'] ?? ''),
                'content_role' => sanitizeInput($_POST['content_role'] ?? 'satellite'),
                'priority' => sanitizeInput($_POST['priority'] ?? 'medium'),
                'status' => sanitizeInput($_POST['status'] ?? 'idea'),
                'notes' => sanitizeInput($_POST['notes'] ?? '')
            ];

            SEO::createKeyword($data);
            redirect('/admin/seo', 'Đã thêm từ khóa mới thành công.');
        }
    }

    public function deleteKeyword($id) {
        requireEditor();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/seo', 'Thao tác không hợp lệ. Yêu cầu phương thức POST.', 'error');
        }
        if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            redirect('/admin/seo', 'Phiên làm việc hết hạn.', 'error');
        }
        SEO::deleteKeyword($id);
        redirect('/admin/seo', 'Xóa từ khóa thành công.');
    }
}
