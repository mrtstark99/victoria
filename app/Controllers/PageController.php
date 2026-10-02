<?php
/**
 * @file app/Controllers/PageController.php
 * @description Controller delivering study abroad information and specialized community pages.
 *
 * Layer:
 * - Presentation / Controller
 *
 * Responsibilities:
 * - Route and render dedicated institutional and guidance pages.
 * - Serve about, schools directory, courses, 7-step process, documents, cost estimator, Zoom consultation, and Q&A.
 *
 * Security:
 * - Sanitized view rendering and canonical URL generation.
 *
 * Dependencies:
 * - Master layouts and template view helper.
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

class PageController {
    /**
     * About Us Page (/about).
     */
    public function about() {
        if ($this->renderOverride('about')) return;
        view('blog/about', [
            'page_title' => 'Về Chúng Tôi - Victoria Universal',
            'meta_description' => 'Victoria Universal - Đơn vị tư vấn du học Nhật Bản uy tín, chuyên nghiệp và tận tâm.'
        ]);
    }

    /**
     * Partner Japanese Language Schools Directory (/schools).
     */
    public function schools() {
        if ($this->renderOverride('schools')) return;
        view('blog/schools', [
            'page_title' => 'Hệ Thống Trường Nhật Ngữ Đối Tác - Victoria Universal',
            'meta_description' => 'Tra cứu danh sách các trường Nhật ngữ uy tín theo vùng miền, học phí và kỳ tuyển sinh.'
        ]);
    }

    /**
     * Japanese Language Training Programs (/courses).
     */
    public function courses() {
        if ($this->renderOverride('courses')) return;
        view('blog/courses', [
            'page_title' => 'Khóa Học Tiếng Nhật Du Học & JLPT - Victoria Universal',
            'meta_description' => 'Đào tạo tiếng Nhật từ sơ cấp N5 đến N2, luyện thi JLPT và phỏng vấn du học chuyên sâu.'
        ]);
    }

    /**
     * 7-Step Study Abroad Process (/process).
     */
    public function process() {
        if ($this->renderOverride('process')) return;
        view('blog/process', [
            'page_title' => 'Quy Trình Hồ Sơ Du Học 7 Bước - Victoria Universal',
            'meta_description' => 'Tìm hiểu quy trình du học Nhật Bản từ chọn trường, xử lý hồ sơ COE, xin visa đến khi xuất cảnh.'
        ]);
    }

    /**
     * Required Application Documents Repository (/documents).
     */
    public function documents() {
        if ($this->renderOverride('documents')) return;
        view('blog/documents', [
            'page_title' => 'Hồ Sơ & Biểu Mẫu Du Học Cần Chuẩn Bị - Victoria Universal',
            'meta_description' => 'Danh sách hồ sơ, giấy tờ công chứng và biểu mẫu cần chuẩn bị cho học viên và người bảo lãnh.'
        ]);
    }

    /**
     * Comprehensive Cost & Living Estimator (/cost or /pricing).
     */
    public function cost() {
        view('blog/cost', [
            'cost' => \Models\CostPage::get(),
            'page_title' => 'Dự Toán Chi Phí Du Học Nhật Bản - Victoria Universal',
            'meta_description' => 'Bảng dự tính chi phí làm hồ sơ, học phí năm đầu và chi phí sinh hoạt thực tế tại Nhật Bản.'
        ]);
    }

    /**
     * Consultation & Zoom Appointment Scheduling (/consultation).
     */
    public function consultation() {
        if ($this->renderOverride('consultation')) return;
        view('blog/consultation', [
            'page_title' => 'Đăng Ký Tư Vấn Zoom Trực Tiếp - Victoria Universal',
            'meta_description' => 'Đặt lịch tư vấn 1-1 miễn phí cùng chuyên gia giáo dục và đại diện trường Nhật Bản.'
        ]);
    }

    private function renderOverride(string $slug): bool {
        $page = \Models\FixedPage::get($slug);
        if ($page === null) return false;
        view('blog/fixed_page_override', [
            'fixed_page' => $page,
            'page_title' => $page['title'] . ' - Victoria Universal',
            'meta_description' => $page['meta_description']
        ]);
        return true;
    }
}
