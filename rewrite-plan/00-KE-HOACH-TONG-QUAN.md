# Kế hoạch viết lại hệ thống CMS doanh nghiệp

## Mục tiêu

Viết lại hệ thống hiện tại thành một nền tảng quản trị nội dung có thể dùng cho nhiều loại website doanh nghiệp. Website public là lớp trình bày; CMS, workflow nội dung, SEO, analytics và AI Agent vận hành phía sau.

Hệ thống hỗ trợ ba cấu hình khởi tạo:

1. **Blog**: bài viết, danh mục, tác giả, topic cluster, lịch nội dung.
2. **Trang doanh nghiệp**: giới thiệu, dịch vụ, đội ngũ, dự án, đối tác, tuyển dụng, liên hệ và landing page.
3. **Trang tin tức**: chuyên mục nhiều cấp, newsroom, lịch xuất bản, bài nổi bật, tác giả và luồng duyệt chặt hơn.

Một website có thể bắt đầu bằng một cấu hình rồi bật thêm module sau, không phải cài lại.

## Nguyên tắc sản phẩm

- Không viết ba CMS riêng. Dùng một core và các module có thể bật/tắt.
- Không hard-code trang chủ. Trang chủ là một trang dựng bằng các section đã đăng ký.
- Không cho phép HTML tùy ý trong page builder mặc định. Mỗi section có schema dữ liệu, validation và component render riêng.
- Nội dung, giao diện và cấu hình vận hành tách khỏi nhau.
- Public site ưu tiên SSR, SEO, accessibility và tốc độ tải.
- Agent chỉ thao tác qua API/service contract, không truy cập database trực tiếp.
- Nội dung do Agent tạo phải đi qua workflow và audit giống người dùng.
- Bản đầu triển khai single-site, nhưng domain model giữ `site_id` để mở đường cho multi-site.

## Kiến trúc mục tiêu đề xuất

- Backend: PHP 8.3+ và Laravel stable phù hợp thời điểm triển khai.
- Database: PostgreSQL; SQLite chỉ dùng cho test hoặc local nhẹ.
- Public frontend: Blade SSR, component hóa, Alpine.js cho tương tác nhỏ.
- Admin: Livewire + Alpine.js; không tạo SPA nếu chưa có nhu cầu rõ ràng.
- Asset build: Vite + PostCSS + native CSS design system.
- Queue/cache: Redis ở production; database queue có thể dùng trong local/MVP.
- File storage: local ở development, S3-compatible storage ở production.
- Search: PostgreSQL full-text ban đầu; Meilisearch/OpenSearch là module nâng cấp.
- API: REST JSON versioned dưới `/api/v1`; OpenAPI là hợp đồng chuẩn.

## Cấu trúc bộ kế hoạch

| File | Nội dung |
|---|---|
| `01-PHAM-VI-SAN-PHAM.md` | Vai trò, use case, site profile và phạm vi MVP |
| `02-KIEN-TRUC-KY-THUAT.md` | Kiến trúc, module, ranh giới và cấu trúc source code |
| `03-MO-HINH-NOI-DUNG-VA-TRANG-CHU.md` | Content model, page builder và tùy biến doanh nghiệp |
| `04-HE-THONG-STYLE-CSS.md` | CSS, design token, theme và component |
| `05-AGENT-SKILL.md` | Agent Skill, workflow, API contract và guardrail |
| `06-DU-LIEU-API-WORKFLOW.md` | Database, API, trạng thái nội dung và quyền hạn |
| `07-BAO-MAT-VAN-HANH-CHAT-LUONG.md` | Security, test, observability, backup và deployment |
| `08-LO-TRINH-THEO-PART.md` | Các part triển khai, phụ thuộc và tiêu chí hoàn thành |
| `09-CAU-HOI-CAN-CHOT.md` | Các quyết định cần người sở hữu sản phẩm trả lời |
| `10-QUAN-LY-TIEN-DO-AI-AGENTS.md` | Nguồn sự thật về tiến độ, nhận việc, handoff và bằng chứng của các AI agent |

## Phạm vi MVP khuyến nghị

- Khởi tạo site và chọn profile.
- User, role, permission và audit log.
- Trang, bài viết, danh mục, media library.
- Page builder với 10–12 section an toàn.
- Site settings, navigation, SEO metadata, sitemap và redirects.
- Workflow `draft -> review -> approved -> scheduled/published`.
- Design token, theme preset và responsive components.
- Agent API cho đọc context, tạo/cập nhật draft, validate và submit review.
- Queue cho publish, image processing, sitemap và indexing.
- Test cho các đường nghiệp vụ trọng yếu.

Multi-tenant SaaS, A/B testing, CRM, commerce, advanced personalization và visual drag-and-drop builder nằm ngoài MVP.

## Definition of Done toàn dự án

- Tạo site mới từ CLI/UI và chọn Blog, Corporate hoặc News.
- Bật thêm module sau khởi tạo mà không mất dữ liệu.
- Dựng trang chủ bằng section, không sửa template PHP.
- Thay theme doanh nghiệp bằng token, không fork CSS.
- Nội dung do người hoặc Agent tạo đều có revision, author, audit và workflow.
- Không có raw HTML/script từ editor được render trên public site.
- API có OpenAPI, versioning, scope, rate limit và idempotency đúng phạm vi.
- Có unit, feature, integration và smoke test trong CI.
- Migration chạy một lần, có rollback; không ALTER schema trong request.
- Production có backup, log, metrics, health check và deploy rollback được.

## Giả định đang dùng

- Một website trên mỗi deployment trong giai đoạn đầu.
- Ngôn ngữ chính là tiếng Việt; schema giữ khả năng bổ sung đa ngôn ngữ.
- SEO và tốc độ public site quan trọng hơn hiệu ứng frontend phức tạp.
- Nhóm vận hành gồm admin, editor, author/reviewer và AI Agent.
- Hệ thống mới dựng trong thư mục/repository riêng, không sửa dần core cũ.

Các giả định phải được xác nhận ở Part 0 trước khi viết code nền tảng.
