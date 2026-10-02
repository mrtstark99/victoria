<?php
/**
 * @file app/Controllers/ContactController.php
 * @description Controller managing public contact consultation requests and API submission.
 *
 * Layer:
 * - Presentation / Controller
 *
 * Responsibilities:
 * - Render public contact page with split-screen branding and contact form.
 * - Handle POST submission to /api/contact securely with CSRF verification and rate limiting.
 * - Validate required fields (name, email, phone) and record inquiry in database.
 * - Return JSON responses for AJAX form submissions.
 *
 * Security:
 * - CSRF verification via verifyCSRFToken().
 * - Input sanitization to prevent XSS and SQL injection.
 * - Client IP tracking for rate limiting and fraud prevention.
 *
 * Dependencies:
 * - Models\Contact
 * - Security helpers (verifyCSRFToken, sanitizeInput, validateEmail)
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

class ContactController {
    /**
     * Render public contact page (/contact).
     */
    public function index() {
        $baseUrl = getSystemBaseUrl();
        $siteTitle = getSetting('site_name', 'Victoria Universal');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => 'Liên hệ - ' . $siteTitle,
            'url' => "{$baseUrl}/contact",
            'description' => 'Liên hệ Victoria Universal để được tư vấn lộ trình, chi phí, trường học và học bổng du học Nhật Bản.'
        ];

        view('blog/contact', [
            'page_title' => 'Liên hệ tư vấn du học - ' . $siteTitle,
            'meta_description' => 'Liên hệ Victoria Universal để nhận lộ trình du học cá nhân hóa, bảng dự toán chi phí và tư vấn chọn trường miễn phí.',
            'canonical_url' => "{$baseUrl}/contact",
            'og_type' => 'website',
            'schema_json' => $schema,
            'page_css' => 'home'
        ]);
    }

    /**
     * Handle AJAX or POST form submission (/api/contact).
     */
    public function submit() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
            exit;
        }

        // Verify CSRF token
        $csrfToken = $_POST[CSRF_TOKEN_NAME] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!verifyCSRFToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Mã bảo mật không hợp lệ hoặc đã hết hạn']);
            exit;
        }

        // Retrieve and sanitize form inputs
        $name = trim(strip_tags($_POST['name'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $phone = trim(preg_replace('/[^\d+]/', '', $_POST['phone'] ?? ''));
        $intakePeriod = trim(strip_tags($_POST['intake_period'] ?? ''));
        $japaneseLevel = trim(strip_tags($_POST['japanese_level'] ?? ''));
        $message = trim(strip_tags($_POST['message'] ?? ''));
        $subject = trim(strip_tags($_POST['subject'] ?? 'Đăng ký tư vấn du học Nhật Bản'));

        // Validate required fields
        if (empty($name) || empty($email) || empty($phone)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ họ tên, email và số điện thoại']);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Địa chỉ email không đúng định dạng']);
            exit;
        }

        // Validate phone number format (at least 9 digits)
        if (strlen($phone) < 9) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Số điện thoại không hợp lệ']);
            exit;
        }

        try {
            $clientIp = getClientIP();
            if (!Contact::allowSubmission($clientIp, 5, 3600)) {
                http_response_code(429);
                header('Retry-After: 3600');
                echo json_encode(['success' => false, 'message' => 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.']);
                exit;
            }
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

            $contactId = Contact::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message,
                'intake_period' => $intakePeriod,
                'japanese_level' => $japaneseLevel,
                'ip_address' => $clientIp,
                'user_agent' => $userAgent
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Cảm ơn bạn! Thông tin đăng ký đã được gửi thành công. Chuyên viên Victoria sẽ liên hệ với bạn trong vòng 24 giờ làm việc.',
                'contact_id' => $contactId
            ]);
            exit;
        } catch (\Throwable $e) {
            error_log('Contact form submission error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Đã có lỗi xảy ra trong quá trình xử lý. Vui lòng liên hệ hotline trực tiếp.']);
            exit;
        }
    }
}
