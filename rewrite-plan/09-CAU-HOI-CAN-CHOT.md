# Các quyết định kiến trúc và sản phẩm đã chốt

> **Trạng thái**: `HOÀN CHẤT (RESOLVED)`  
> **Áp dụng cho**: Part 0 — Đóng Part 0 và mở đường triển khai Part 1 (`08-LO-TRINH-THEO-PART.md`).

---

## Ba câu hỏi tiên quyết đã khóa

1. **Tenancy Model**: **Single-site ngay bản đầu**, nhưng toàn bộ bảng nghiệp vụ (posts, categories, pages, settings) đều giữ sẵn cột `site_id` (mặc định = 1) để sẵn sàng mở rộng multi-site sau này mà không phải sửa cấu trúc cơ sở dữ liệu.
2. **Profile ra mắt đầu tiên**: **Profile Blog** (kế thừa và chuyển đổi toàn bộ dữ liệu, nghiệp vụ SEO, topic cluster và AI workflow từ hệ thống hiện tại). Sau đó mới bật thêm module **Corporate** và **News**.
3. **Công nghệ cốt lõi**: Chấp thuận toàn diện đề xuất: **PHP 8.3+ / Laravel 11 + PostgreSQL 16 + Blade / Livewire 3 + Alpine.js + Native CSS Design System**.

---

## Nhóm A — Kiến trúc

* **1. Tenancy**: Single-site trong giai đoạn MVP. Admin quản lý 1 site duy nhất, domain model có `site_id`.
* **2. Tech Stack**: Laravel 11, PostgreSQL, Blade SSR cho public site, Livewire 3 cho Admin CMS.
* **3. Site Profile**: Blog là profile số 1 (ra mắt cùng MVP). Corporate và News là profile kế tiếp ở Part 6 & 7.
* **4. Đa ngôn ngữ**: MVP tập trung tiếng Việt (`vi_VN`). Thiết kế schema trường đa ngữ dạng `JSONB` hoặc chuẩn hóa localization để hỗ trợ i18n khi mở rộng.
* **5. Môi trường Production**: VPS Linux Ubuntu hiện hữu (`BrHub VPS`), triển khai tự động qua GitHub Actions và Cloudflared SSH Tunnel.

---

## Nhóm B — Sản phẩm và giao diện

* **6. Page Builder**: Xây dựng dạng **Form Section có schema định nghĩa sẵn** và sắp xếp thứ tự (không dùng visual drag-and-drop tự do để tránh tạo mã HTML rác và vỡ responsive).
* **7. Custom CSS/JS**: **KHÔNG** cho phép nhập custom script tùy tiện trong MVP để ngăn chặn rủi ro XSS. Giao diện thay đổi thông qua Design Tokens (màu sắc, typography, theme presets).
* **8. Phong cách nhận diện**: **Modern Editorial & Tối giản**, ưu tiên Core Web Vitals, tốc độ tải trang, độ tương phản đọc tốt và SEO On-page.
* **9. Module doanh nghiệp tiếp theo**: Module **Dịch vụ & Tư vấn (Lead Capture Form)** kết hợp với Blog.

---

## Nhóm C — AI Agent

* **10. Tích hợp Agent**: Tiếp tục chuẩn hóa giao thức **Agent API / MCP Protocol** qua REST JSON, hỗ trợ đa client (Cursor, Antigravity, Claude, n8n, Custom CLI).
* **11. Luồng duyệt bài của Agent**: **Human-in-the-loop**. Mặc định Agent chỉ tạo `ai_draft` và gửi duyệt `pending_review`. Quyền `posts:publish` chỉ kích hoạt khi có cờ cấu hình ghi đè từ Admin.
* **12. Nguồn dữ liệu Brand**: Lưu trữ tại CMS (`settings` & `ai_guidelines`), cung cấp cho Agent qua API endpoint `/api/v1/brand` và `/api/v1/guidelines`.
* **13. Nguồn trích dẫn (Citations)**: Khuyến khích gắn nguồn kiểm chứng cho các thông tin chính sách, luật, học bổng và số liệu tài chính.

---

## Nhóm D — Migration và Vận hành

* **14. Kế thừa dữ liệu**: Có lệnh migration/seeder nhập 100% bài viết, danh mục, revision, task và settings từ `blog.db` (SQLite) sang PostgreSQL.
* **15. Bảo toàn SEO URL**: Bắt buộc giữ **100% cấu trúc URL hiện tại** (`/blog/{slug}`, `/category/{slug}`, `/sitemap.xml`) để bảo toàn xếp hạng từ khóa trên Google.
* **16. Quy mô dự kiến (12 tháng)**: 50.000 – 150.000 sessions/tháng, 1–3 quản trị viên, 100–300 bài viết chuyên sâu.
* **17. Chỉ tiêu an toàn dữ liệu (RPO/RTO)**:
  * RPO: < 24 giờ (tự động sao lưu database hàng đêm lên Cloud Storage).
  * RTO: < 1 giờ (quy trình phục hồi qua script tự động).
