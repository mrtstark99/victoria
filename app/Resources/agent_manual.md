# HƯỚNG DẪN TÍCH HỢP AI AGENT (CMS BLOG SEO SYSTEM)

Tài liệu này cung cấp hướng dẫn đầy đủ để tích hợp, kết nối và vận hành các AI Agent tự động (như Cursor, Claude, AutoGPT, Gemini hoặc các tập lệnh LLM tùy chỉnh) với hệ thống quản trị nội dung CMS Blog SEO.

---

## 1. Phương thức Xác thực (Authentication)

Để giao tiếp với API, AI Agent cần sử dụng **Bearer Token** được tạo từ trang quản trị Token. Token này phải được gửi kèm trong Header của mỗi yêu cầu HTTP.

* **Cổng kết nối API**: `{{BASE_URL}}/api/agent.php` (hoặc `{{BASE_URL}}/api/agent` tùy thuộc cấu hình rewrite)

## Bản đồ khu vực và quy tắc chọn đích

| Người dùng muốn sửa | Trang bị tác động | Action đọc / ghi | Scope ghi |
| --- | --- | --- | --- |
| Chương trình, gói hồ sơ/dịch vụ, quyền lợi hoặc giá của gói | `/services` và trang chi tiết `/services/{slug}` | `get_service`; `create_service` / `update_service` | `services:write` |
| Bảng học phí, chi phí sinh hoạt, tỷ giá, dự toán chi phí du học | `/cost` | `get_cost_page`; `update_cost_page` | `cost:write` |
| Các lựa chọn giá của công cụ dự toán trên trang chủ | Calculator trang chủ | `get_home_calculator`; `update_home_calculator` | `calculator:write` |
| Bài viết, chuyên mục blog | `/blog`, `/category/{slug}` | `posts`, `categories`; action bài viết/chuyên mục tương ứng | `posts:draft`, `category:write` |
| Trang nội dung cố định như giới thiệu, trường, quy trình, hồ sơ, tư vấn, hỏi đáp | Route trang cố định theo slug | `get_fixed_page`; `update_fixed_page` | `fixed_pages:write` |
| Header menu, menu di động, footer | Điều hướng đầu/cuối trang | `get_navigation`; `update_navigation` | `navigation:write` |
| Hero, thứ tự/ẩn hiện section trang chủ | `/` | `get_homepage`; `update_homepage` | `homepage:write` |
| Nhận diện thương hiệu và liên hệ | Cấu hình toàn site | `get_brand`; `update_brand` | `brand:write` |
| Link Facebook chat và Zalo của nút chat nổi | Hai nút liên hệ góc phải cuối trang | `get_brand`; `update_brand` với `chat_facebook_url`, `zalo_url` | `brand:write` |
| Tác giả và sidebar bài viết | Bio tác giả; widget cột phải bài viết | `get_author_profile`, `get_post_sidebar`; action update tương ứng | `profile:write`, `sidebar:write` |
| Theo dõi GA4, Search Console, KPI, chi phí SEO | Cấu hình đo lường | `get_analytics_settings`; `update_analytics_settings` | `analytics:settings:write` |
| Chuyên mục blog | `/category/{slug}` | `categories`; action category tương ứng | `category:write` |
| Ảnh bài viết | Kho uploads dùng cho nội dung | — | `upload_image` (`media:upload`) |

**Không được suy luận chéo:** từ “gói dịch vụ” luôn bắt đầu ở `/services`. `/cost` là bảng ước tính chi phí, không phải danh mục dịch vụ. Nếu người dùng chỉ nói “giá”, “gói” hoặc chưa nêu đích/đối tượng, hỏi một câu ngắn để phân biệt trước khi ghi. Không có scope nghĩa là dừng và báo quyền cần cấp, tuyệt đối không chuyển sang trang khác.

Để đổi link hai nút nổi Facebook/Zalo, đọc `GET ?action=get_brand`, sau đó dùng `POST ?action=update_brand` với scope `brand:write`, ví dụ `{"chat_facebook_url":"https://m.me/brighteducation","zalo_url":"https://zalo.me/84971044576"}`. Cả hai giá trị phải là URL đầy đủ dùng HTTP hoặc HTTPS. Giá trị rỗng sẽ trở về mặc định: Facebook dùng link Facebook site hiện tại và Zalo lấy số điện thoại Việt Nam.

## Xác nhận bắt buộc trước khi ghi

Với mọi POST làm thay đổi chương trình/gói dịch vụ hoặc trang chi phí, trước tiên đọc dữ liệu hiện tại bằng action GET tương ứng. Sau đó trình bày đích trang, chương trình/bảng, trường và giá trị mới, cùng việc nội dung có được công khai hay không; chờ người dùng xác nhận rõ ràng. Chỉ gửi POST sau khi người dùng đồng ý và đặt `user_confirmed: true`. API trả HTTP `428` nếu thiếu cờ này. Không được coi task, quyền token, yêu cầu ban đầu chung chung hoặc sự im lặng là xác nhận. Việc kích hoạt dịch vụ để công khai cần xác nhận riêng qua `activate_service`.

### Quản lý Dịch vụ Du học

API dùng Bearer token như các action khác. Cấp riêng các quyền `services:read` (xem), `services:write` (tạo và sửa bản nháp), `services:publish` (kích hoạt, ngừng hiển thị và sửa chương trình đang hoạt động), `services:delete` (xóa). Quyền `admin` bao gồm tất cả. Gọi `GET ?action=me` để kiểm tra action được cấp. Slug chương trình và slug của từng gói hồ sơ bắt buộc viết bằng tiếng Anh thường, chỉ gồm chữ a-z, số và dấu gạch nối đơn (ví dụ `japanese-language-school-program`, `standard`); không bỏ trống và không tạo slug từ tiêu đề tiếng Việt. Các chương trình chứa mảng `packages`; mỗi gói có `name`, `slug`, `description`, `price` (VNĐ), `features` (mảng chuỗi) và tùy chọn `featured`. Agent có thể gửi `packages` trong `create_service` hoặc `update_service`; tối đa 12 gói/chương trình và 20 quyền lợi/gói. Mọi POST ghi dữ liệu dịch vụ cần `user_confirmed: true` sau khi đã có xác nhận trực tiếp của người dùng; thiếu cờ trả HTTP `428`.

| Action | Method | Dữ liệu |
| --- | --- | --- |
| `services` / `list_services` | GET | `page`, `per_page` (tối đa 50), `search` |
| `get_service` | GET | `id` hoặc `slug` |
| `create_service` | POST | JSON: `title` (bắt buộc), `name`, `slug`, `description`, `content`, `icon`, `packages`, `display_order`; luôn tạo ở trạng thái `inactive` |
| `update_service` | POST | JSON: `id` và các trường cần sửa, gồm `packages`; nên gửi `expected_updated_at` từ lần đọc gần nhất để phát hiện xung đột |
| `activate_service` / `deactivate_service` | POST | JSON: `id`, `user_confirmed: true`, tùy chọn `expected_updated_at` |
| `delete_service` | POST | JSON: `id`, `user_confirmed: true`, tùy chọn `expected_updated_at` |

Ví dụ: chỉ sau khi người dùng duyệt nội dung và đích `/services`, gửi `POST {{BASE_URL}}/api/agent.php?action=create_service` với `Authorization: Bearer <TOKEN>`, `Content-Type: application/json`, `Idempotency-Key: <unique-key>` và body `{"title":"Tư vấn du học Nhật Bản","slug":"japanese-study-consulting","price":0,"user_confirmed":true}`. Dịch vụ được tạo ở trạng thái ẩn. Chỉ gọi `activate_service` với `user_confirmed: true` sau khi người dùng duyệt riêng việc công khai. API ghi audit log cho mọi thay đổi; slug trùng trả HTTP 409, dữ liệu sai trả 400, thiếu quyền trả 403, thiếu xác nhận trả 428.

### Thiết lập trang Chi Phí Du Học Nhật Bản (`/cost`)

`GET ?action=get_cost_page` yêu cầu `cost:read` và trả về `title`, `intro`, `exchange_rate`, `sections`, `updated_at`. Mỗi section có `title`, `description`, `columns` (2-6 nhãn cột) và `rows` (mỗi hàng có đúng số ô bằng số cột). `POST ?action=update_cost_page` yêu cầu `cost:write` và `user_confirmed: true`; gửi JSON chứa một hoặc nhiều trường trên để cập nhật. Thiếu xác nhận trả HTTP 428. Nên gửi `expected_updated_at` từ GET để phát hiện chỉnh sửa đồng thời (HTTP 409). Dùng `Idempotency-Key` cho POST. Mọi thay đổi có hiệu lực ngay trên `/cost` và được ghi audit log. `exchange_rate` là chú thích tỷ giá; khi đổi tỷ giá, agent cần đồng thời cập nhật các số quy đổi trong bảng để chúng nhất quán. Công cụ dự toán trên trang chủ có cấu hình API riêng ở phần dưới.

### Các chức năng quản trị bổ sung

Mọi action dưới đây dùng `Authorization: Bearer <TOKEN>` tại `/api/agent.php?action=<action>`. Các action đọc dùng GET, action thay đổi dùng POST JSON và nên gửi `Idempotency-Key`. Mọi thay đổi được ghi audit log.

| Nhóm | Scope | Action | Dữ liệu chính |
| --- | --- | --- | --- |
| Liên hệ và yêu cầu tư vấn | `contacts:read` | `contacts`, `get_contact` | GET: `page`, `per_page`, `status`, `search` hoặc `id` |
| Liên hệ và yêu cầu tư vấn | `contacts:write`, `contacts:delete` | `update_contact`, `delete_contact` | POST: `id`, `status`, `notes`; xóa cần scope riêng |
| Dự toán trang chủ | `calculator:read`, `calculator:write` | `get_home_calculator`, `update_home_calculator` | POST một hoặc nhiều nhóm: `package_prices`, `course_prices`, `school_prices`, `living_prices`, `other_prices`. Giữ nguyên số lựa chọn và tên hệ du học; giá là số nguyên VNĐ. |
| Giao diện trang chủ | `homepage:read`, `homepage:write` | `get_homepage`, `update_homepage` | Sửa nội dung hero, 4 thẻ cam kết, thứ tự và trạng thái hiển thị các section. `sections` phải chứa đủ khóa: `trust`, `programs`, `process`, `info_portal`, `cost_calculator`, `blog_preview`, `zoom_sessions`, `contact_form`. |
| Bio tác giả | `profile:read`, `profile:write` | `get_author_profile`, `update_author_profile` | Chỉ đọc/sửa bio của tài khoản tác giả được gán cho Agent; POST JSON `{"bio":"..."}` tối đa 1.000 ký tự. |
| Cột phải bài viết | `sidebar:read`, `sidebar:write` | `get_post_sidebar`, `update_post_sidebar` | POST các khóa `post_*` có trong phản hồi GET; `post_sidebar_custom_html_widgets` là chuỗi JSON. |
| Đo lường | `analytics:settings:read`, `analytics:settings:write` | `get_analytics_settings`, `update_analytics_settings` | GA4 ID, Property ID, GSC site URL/verification, KPI và chi phí SEO. Có thể POST `service_account_json` để lưu mã hóa hoặc `remove_credentials: true` để xóa; GET chỉ trả `credentials_configured`, không trả khóa. |
| Trang nội dung cố định | `fixed_pages:read`, `fixed_pages:write` | `get_fixed_page`, `update_fixed_page`, `reset_fixed_page` | `slug` thuộc `about`, `schools`, `courses`, `process`, `documents`, `consultation`, `qa`. POST `title`, `meta_description`, `content_html`; reset trả lại giao diện gốc. |
| Kế hoạch SEO | `seo:read`, `seo:write` | `list_clusters`, `get_cluster`, `create_cluster`, `update_cluster`, `delete_cluster` | POST `planning_month` dạng `YYYY-MM`, `name`, `pillar_title`, `pillar_url`, `description`, `status`. Update/delete cần `id`; xóa cluster giữ từ khóa nhưng bỏ liên kết cluster. |

Ví dụ: `POST ?action=update_contact` với body `{"id":12,"status":"processing","notes":"Đã phân công tư vấn viên"}`. `POST ?action=update_home_calculator` với body `{"course_prices":[0,12000000,17000000]}`. `POST ?action=update_fixed_page` với body `{"slug":"about","title":"Về Bright Education","content_html":"<p>Nội dung giới thiệu mới.</p>"}`.
Ví dụ: `POST ?action=update_homepage` với body `{"hero":{"title":"Bắt đầu hành trình du học","highlight":"Nhật Bản ngay hôm nay"},"sections":["trust","programs","blog_preview","process","info_portal","cost_calculator","zoom_sessions","contact_form"],"visible_sections":{"zoom_sessions":false}}`.
* **Phương thức**: `POST` hoặc `GET`
* **Header bắt buộc**:
  * `Authorization: Bearer <YOUR_AGENT_TOKEN>`
  * `Content-Type: application/json`
  * `Idempotency-Key: <UUID>` (Khuyên dùng cho các tác vụ thay đổi dữ liệu như tạo bài, sửa bài, cấu hình brand để tránh trùng lặp khi mất kết nối mạng)

> [!WARNING]
> Vì lý do bảo mật, hệ thống cấm truyền Token trực tiếp qua tham số trên thanh địa chỉ (Query String) như `?token=...`. Mọi yêu cầu sử dụng Query String chứa token sẽ bị từ chối bằng lỗi `400 Bad Request`.

---

## 2. QUY TRÌNH BẮT BUỘC KHI LÀM VIỆC (MANDATORY WORKFLOW)

> [!CRITICAL]
> **RÀNG BUỘC ĐỒNG BỘ TIẾN ĐỘ & BỘ NHỚ LÊN HỆ THỐNG TRUNG TÂM:**
> Các AI Agent thường có bộ nhớ phiên (Local Context / Memory) và dễ tự lên kế hoạch ngầm tại đó mà không cập nhật về CMS Blog. **HỆ THỐNG NGHIÊM CẤM HÀNH VI NÀY.**
> 
> * **Bắt đầu ca làm:** BẮT BUỘC gọi `GET /api/agent.php?action=tasks` để đọc danh sách nhiệm vụ được giao trên hệ thống trung tâm theo lịch ngày/tháng thực tế.
> * **Khi phát sinh công việc con / lỗi / cập nhật admin:** BẮT BUỘC tạo task bổ sung vào lịch ngày hôm nay qua `POST /api/agent.php?action=create_adhoc_task`.
> * **Khi bắt đầu làm việc:** Cập nhật task với trạng thái đang thực thi qua `POST /api/agent.php?action=update_task`.
> * **Khi hoàn thành:** BẮT BUỘC gọi `POST /api/agent.php?action=complete_task` đính kèm đường link bài viết, từ khóa hoặc kết quả đo lường trong trường `notes`.

---

### 📅 QUY TẮC LÊN LỊCH BIỂU THỰC TẾ (REAL CALENDAR RULES):
1. **Tháng bắt đầu là Tháng 1 (Month 1):** Tháng tại thời điểm bắt đầu triển khai dự án được quy ước là Tháng Thứ Nhất.
2. **Lên lịch 6 Tháng tới (Monthly Tasks):** Được lên vào tháng đầu tiên, với deadline là ngày cuối cùng của từng tháng tương ứng.
3. **Lên lịch Tuần & Ngày (Weekly & Daily Tasks):** Được lên vào ngày đầu mỗi tháng gắn với các ngày thực tế (`scheduled_date` / `deadline`):
   - *Task Tuần:* Chia đều theo 4 tuần trong tháng (Tuần 1: Ngày 01-07, Tuần 2: 08-14, Tuần 3: 15-21, Tuần 4: 22-hết tháng).
   - *Task Ngày:* Chia 3 phiên làm việc thực tế (🌅 Sáng: Audit GA4/GSC, 🌤️ Chiều: Viết bài & E-E-A-T, 🌙 Tối: Tối ưu On-page & Schedule).
4. **Task Bổ Sung & Phát Sinh (Ad-hoc Tasks):** Các công việc phát sinh đột xuất, fix bug, cập nhật từ Admin được đẩy ngay vào lịch ngày hôm nay (`cycle_type: adhoc`, `is_ad_hoc: 1`).

---

### 📖 TÀI LIỆU QUY CHUẨN THAM CHIẾU (REFERENCE FRAMEWORK):
AI Agent sử dụng các tài liệu quy chuẩn sau làm kim chỉ nam khi lập kế hoạch:
- **Khung Định Kỳ:** Sáng Audit $\rightarrow$ Chiều Viết bài $\rightarrow$ Tối On-page; Hàng tuần Topic Cluster; Hàng tháng Content Calendar; Hàng quý Audit & Pruning.
- **Kế Hoạch 30 Ngày Đầu:** Tuần 1 Nền móng $\rightarrow$ Tuần 2 Pillar & Cluster $\rightarrow$ Tuần 3 Sitemap & Social Signals $\rightarrow$ Tuần 4 CTA & Guest Post.
- **Lộ Trình 6 Tháng Batching AI:** Tháng 1 Đổ móng $\rightarrow$ Tháng 2 Mở rộng $\rightarrow$ Tháng 3 Đọc Data $\rightarrow$ Tháng 4 Nâng cấp E-E-A-T $\rightarrow$ Tháng 5 Tỉa cành $\rightarrow$ Tháng 6 Chuyển đổi.
- **Sau 6 Tháng:** Topical Authority, Repurposing, CRO, Content Maintenance, Digital PR.

```mermaid
sequenceDiagram
    autonumber
    actor Agent as AI Agent (Cursor / Claude / Gemini)
    participant API as CMS Agent API (/api/agent.php)
    participant DB as System Database

    Agent->>API: 1. GET ?action=tasks&cycle_type=daily (Bearer Token)
    API-->>Agent: Trả về danh sách nhiệm vụ ngày theo phiên (Sáng / Chiều / Tối)
    
    Agent->>API: 2. GET ?action=guidelines (Bearer Token)
    API-->>Agent: Trả về Master System Prompt & Quy chuẩn SEO/Style hiện hành
    
    Note over Agent: AI nạp System Prompt làm System Role.<br/>Triển khai bài viết hoặc tối ưu theo lịch thực tế.
    
    Agent->>API: 3. POST ?action=create_draft (Nội dung chuẩn SEO & Style)
    API-->>Agent: 201 Created { post_id: 12, slug: "...", status: "draft" }
    
    Agent->>API: 4. POST ?action=complete_task { task_id: 5, notes: "Đã tạo bài viết post_id=12" }
    API-->>Agent: 200 OK { is_completed: 1 } (Đồng bộ tiến độ lên Dashboard)
```
    
    Agent->>API: 4. POST ?action=complete_task { task_id: 5, notes: "Đã tạo bài nháp ID #12" }
    API-->>Agent: 200 OK { success: true, message: "Task completed" }
```

### Các bước triển khai chuẩn cho AI Agent:
1. **Bước 1 (Đọc bảng kế hoạch)**: Gửi `GET {{BASE_URL}}/api/agent.php?action=tasks&status=pending` để lấy các nhiệm vụ chưa hoàn thành, ưu tiên thực hiện các việc có mức độ `urgent` hoặc `high`.
2. **Bước 2 (Đọc Master System Prompt)**: Gửi `GET {{BASE_URL}}/api/agent.php?action=guidelines` để nạp quy chuẩn viết bài mới nhất.
3. **Bước 3 (Thực thi & Tạo bài viết)**: Sử dụng đúng HTML mẫu và class trong `data.elements_library` trả về từ API `action=guidelines`, rồi gửi `POST {{BASE_URL}}/api/agent.php?action=create_draft`. Không tự tạo class mới.
4. **Bước 4 (Cập nhật tiến độ)**: Gửi `POST {{BASE_URL}}/api/agent.php?action=complete_task` để tick hoàn thành nhiệm vụ trên Bảng Kế hoạch.

---

## 3. Quy chuẩn Định dạng CSS & UI Elements Hỗ trợ (Styling Guidelines)

Hệ thống tải trực tiếp các module trong `public/assets/css/post/` kèm cache version, hỗ trợ đầy đủ chế độ Sáng (Light) và Tối (Dark).

Quy tắc trên áp dụng cho **nội dung bài viết**. Khi người dùng yêu cầu chỉnh layout/CSS của một trang, đó là thay đổi mã giao diện, không phải thao tác tạo/sửa nội dung qua API. Agent phải đọc `cms-seo-agent-skill/10_site_style_css_guidelines.md`, lần theo route → controller → view → stylesheet được nạp; giữ palette xanh Victoria (`--primary: #006644`, hover `#004F34`), nền sáng, Inter/Quicksand, padding nhất quán và responsive. CSS phải giới hạn trong trang/nhóm trang, không vá selector toàn cục hoặc chèn `<style>`/inline CSS vào `content_html`. Các route `/services`, `/cost`, trang tĩnh và `/page/{slug}` dùng view/CSS khác nhau; không đoán file từ tên route.

> [!IMPORTANT]
> **AI Agent BẮT BUỘC đọc quy ước bài viết và danh mục UI Elements từ API, KHÔNG đọc file `.md` tĩnh:**
> Gọi `GET /api/agent.php?action=guidelines` ở đầu mỗi phiên làm việc để nhận toàn bộ hướng dẫn định dạng, tông giọng và danh sách UI Elements hiện hành từ trường `data.guidelines` trong phản hồi JSON. Dữ liệu trả về từ API luôn là phiên bản mới nhất và có thể được Admin cập nhật bất kỳ lúc nào.

AI Agent được phép và **khuyến khích** sử dụng các thành phần từ **thư viện UI Elements thiết kế sẵn** để tối ưu trải nghiệm đọc. Chọn lọc **2 – 4 elements/bài**, không lạm dụng, không đặt liền kề.

### Các UI Elements Được Phép Sử Dụng

API `guidelines` trả về 13 nhóm element hiện hành trong `data.elements_library`, bao gồm tên, toàn bộ class hợp lệ và HTML mẫu. Đây là hợp đồng runtime duy nhất; tài liệu tích hợp không sao chép danh sách class để tránh lệch phiên bản.

---

## 4. Danh sách các Hành động (Actions) & Scopes

API hoạt động dưới dạng Single Endpoint, phân luồng chức năng qua tham số URL `?action=<ten_action>`.

| Scope | Action | Phương thức | Mô tả |
| :--- | :--- | :--- | :--- |
| *(Mọi Token)* | `me` / `capabilities` | `GET` | **Tự kiểm tra quyền (Self-Discovery)**: Xem danh sách Scopes và toàn bộ API Actions mà Agent được phép gọi. |
| **tasks:read** | `tasks` / `list_tasks` / `get_tasks` | `GET` | **Đọc Bảng Kế hoạch Làm việc**: Lấy danh sách nhiệm vụ của AI Agent, hỗ trợ lọc theo `status=pending/completed`, `priority=urgent/high/medium/low`, `category`. |
| **tasks:write** | `create_task` | `POST` | **Tự tạo kế hoạch mới**: AI Agent tự động lên kế hoạch làm việc hoặc chia nhỏ các nhiệm vụ con. |
| | `update_task` | `POST` | Cập nhật nội dung, mức ưu tiên, hạn chót hoặc ghi chú của kế hoạch. |
| | `complete_task` | `POST` | **Đánh dấu hoàn thành nhiệm vụ**: Báo cáo nhiệm vụ đã làm xong kèm ghi chú kết quả/URL bài viết. |
| | `delete_task` | `POST` | Xóa một kế hoạch khỏi bảng nhiệm vụ. |
| **posts:read** / **posts:draft** | `guidelines` / `get_guidelines` | `GET` | **Lấy Master System Prompt & Quy chuẩn viết bài**: Lấy trực tiếp toàn bộ Master System Prompt động và hướng dẫn chi tiết (Tông giọng, độ dài, bố cục PAS/AIDA, CSS components, từ khóa, TL;DR, FAQ, CTA, từ cấm). |
| | `system_prompt` / `prompt` | `GET` | **Lấy nhanh Master System Prompt**: Trả về trực tiếp chuỗi Master System Prompt đang hoạt động để AI nạp làm System Instruction. |
| **brand:write** / **admin** | `update_guidelines` | `POST` | Cập nhật cấu hình hướng dẫn viết bài hoặc Master System Prompt tùy chỉnh qua API. |
| **brand:read** | `brand` / `get_brand` | `GET` | Lấy toàn bộ cấu hình Brand & Website (Tên, Slogan, Logo, Liên hệ, Mạng xã hội, Footer). |
| **brand:write** | `update_brand` | `POST` | Cập nhật cấu hình nhận diện thương hiệu, liên hệ, mạng xã hội và chân trang. |
| | `default_seo` | `GET` | Xem cấu hình SEO Mặc định & Thẻ Open Graph chia sẻ mạng xã hội. |
| | `update_default_seo` | `POST` | Cập nhật mô tả SEO mặc định, từ khóa SEO mặc định, ảnh OG image, Google Analytics ID, GSC. |
| **category:read** | `categories` | `GET` | Lấy danh sách toàn bộ chuyên mục/danh mục bài viết kèm số lượng bài viết. |
| **category:write** | `create_category` | `POST` | Tạo chuyên mục/danh mục bài viết mới. |
| | `update_category` | `POST` | Cập nhật tên, slug, mô tả của chuyên mục. |
| | `delete_category` | `POST` | Xóa chuyên mục khỏi hệ thống. |
| **seo:read** | `seo` | `GET` / `POST` | Lấy danh mục, từ khóa lập kế hoạch, các chỉ tiêu KPI và số liệu đo lường thực tế (GA4/GSC). |
| **seo:write** | `create_keyword` | `POST` | Thêm từ khóa mới vào kế hoạch SEO tháng. |
| | `update_keyword` | `POST` | Cập nhật trạng thái từ khóa (`idea`, `writing`, `published`). |
| | `delete_keyword` | `POST` | Xóa từ khóa khỏi kế hoạch. |
| **analytics:read**| `analytics` | `GET` | Xem tổng quan báo cáo lượt xem, thiết bị và các lỗi kết nối. |
| | `page_performance`| `GET` | Xem hiệu suất chi tiết (Clicks, Impressions, CTR, Vị trí, Leads) của từng bài viết đã xuất bản. |
| | `opportunities` | `GET` | Lấy danh sách các đề xuất tối ưu hóa (những bài viết có CTR thấp hoặc vị trí mấp mé Top 10). |
| **posts:read** | `posts` | `GET` | Xem danh sách toàn bộ bài viết, trạng thái, tác giả và lượt xem. |
| | `list_revisions` | `GET` | Xem lịch sử các phiên bản chỉnh sửa của bài viết. |
| **posts:draft** | `create_draft` | `POST` | Tạo bài viết nháp mới dựa trên từ khóa mục tiêu. |
| | `update_post` | `POST` | Cập nhật tiêu đề, nội dung, slug, ảnh đại diện hoặc mô tả. Bắt buộc gửi `expected_updated_at` lấy từ lần đọc gần nhất; mismatch trả HTTP 409. |
| | `submit_for_review`| `POST` | Gửi bài viết nháp lên trạng thái chờ duyệt (`pending`). |
| | `restore_revision` | `POST` | Khôi phục bài viết về một phiên bản lịch sử. |
| **posts:publish** | `approve_post` | `POST` | Phê duyệt bài viết (Chuyển từ chờ duyệt sang nháp hoặc xuất bản). |
| | `publish_post` | `POST` | Xuất bản bài viết trực tiếp lên trang chủ công khai. |

---

## 5. Cú pháp & Ví dụ Yêu cầu (Request & Response Examples)

### Ví dụ 1: Đọc Bảng Kế hoạch Làm việc của AI Agent
* **URL**: `{{BASE_URL}}/api/agent.php?action=tasks&status=pending`
* **Method**: `GET`
* **Headers**: `Authorization: Bearer <TOKEN>`
* **Kết quả trả về (JSON)**:
```json
{
  "success": true,
  "message": "Tasks retrieved successfully",
  "data": {
    "tasks": [
      {
        "id": 1,
        "priority": "urgent",
        "category": "SEO & Bài viết",
        "content": "Viết bài nháp chuẩn SEO cho từ khóa 'tối ưu On-Page 2026', chèn bảng so sánh và FAQ",
        "deadline": "2026-08-20",
        "is_completed": 0,
        "notes": "Tham khảo mục tiêu Top 3 Google",
        "created_by": "admin",
        "created_at": "2026-08-16 10:00:00"
      }
    ],
    "stats": {
      "total": 5,
      "pending": 2,
      "completed": 3,
      "urgent_high": 1,
      "completion_rate": 60
    }
  }
}
```

### Ví dụ 2: AI Agent tự động tạo nhiệm vụ mới vào Bảng Kế hoạch
* **URL**: `{{BASE_URL}}/api/agent.php?action=create_task`
* **Method**: `POST`
* **Payload**:
```json
{
  "content": "Kiểm tra và bổ sung Internal Links cho 5 bài viết chuyên mục Hướng dẫn",
  "priority": "high",
  "category": "Audit On-Page",
  "deadline": "2026-08-18",
  "notes": "Phát hiện CTR mấp mé Top 10 qua API Opportunities"
}
```

### Ví dụ 3: AI Agent báo cáo hoàn thành nhiệm vụ
* **URL**: `{{BASE_URL}}/api/agent.php?action=complete_task`
* **Method**: `POST`
* **Payload**:
```json
{
  "task_id": 1,
  "notes": "Đã tạo bài viết nháp ID #15, slug: 'huong-dan-toi-uu-on-page-2026'. Điểm SEO On-page đạt 95/100."
}
```

### Ví dụ 4: Đọc Master System Prompt & Hướng dẫn Viết bài qua API
* **URL**: `{{BASE_URL}}/api/agent.php?action=guidelines`
* **Method**: `GET`
* **Headers**: `Authorization: Bearer <TOKEN>`
* **Kết quả trả về (JSON)**:
```json
{
  "success": true,
  "message": "AI Content Guidelines & Master System Prompt retrieved successfully.",
  "data": {
    "enabled": true,
    "mode": "auto",
    "system_prompt": "# VAI TRÒ & NHIỆM VỤ (ROLE & MISSION)\nBạn là Chuyên gia Sáng tạo Nội dung & Cố vấn SEO Cao cấp...",
    "elements_library": [
      { "id": "04", "name": "Hộp Ghi Chú & Cảnh Báo", "classes": ["callout", "callout-tip", "callout-title"], "html_templates": ["<div class=\"callout callout-tip\">...</div>"] }
    ],
    "guidelines": {
      "tone": "chuyen_gia",
      "target_audience": "Chủ doanh nghiệp, Marketers, SEO Content Creators...",
      "outline_model": "PAS",
      "word_count": { "min": 1200, "max": 2500 },
      "special_blocks": { "include_tldr": true, "include_faq": true, "include_table": true, "include_cta": true }
    }
  }
}
```

### Ví dụ 5: Tạo bài viết nháp mới có tích hợp CSS Elements
* **URL**: `{{BASE_URL}}/api/agent.php?action=create_draft`
* **Method**: `POST`
* **Payload**:
```json
{
  "title": "Hướng dẫn tối ưu On-Page SEO toàn diện với AI Agent",
  "content": "<div class=\"callout callout-info\"><div class=\"callout-title\">💡 Tóm tắt cốt lõi (TL;DR)</div><p>Bài viết hướng dẫn lộ trình 5 bước áp dụng mô hình AI Agent vào việc tự động hóa On-Page SEO.</p></div><h2>1. Tổng quan quy trình</h2><p>Dưới đây là bảng so sánh hiệu quả:</p><div class=\"table-responsive\"><table class=\"content-table\"><thead><tr><th>Chỉ số</th><th>Phương pháp cũ</th><th>Với AI Agent</th></tr></thead><tbody><tr><td>Thời gian tạo bài</td><td>4 giờ</td><td>15 phút</td></tr></tbody></table></div>",
  "category_id": 2,
  "meta_description": "Khám phá các bước tối ưu On-Page SEO kết hợp UI components và AI Agent.",
  "slug": "huong-dan-toi-uu-on-page-seo-toan-dien-voi-ai-agent"
}
```

---

## 6. Các Giới hạn An toàn (Security & Rate Limits)

1. **IP Allowlist (Bộ lọc IP)**: Nếu được thiết lập, hệ thống chỉ chấp nhận yêu cầu từ các địa chỉ IP được khai báo trước. Yêu cầu từ IP lạ sẽ nhận phản hồi `403 Forbidden`.
2. **Rate Limit (Tần suất yêu cầu)**: Giới hạn tối đa **60 yêu cầu mỗi phút (60 requests/min)** cho mỗi Token. Nếu vượt quá, API sẽ trả về mã lỗi `429 Too Many Requests`.
3. **Idempotency (Đảm bảo tính duy nhất)**: Khi gửi khóa `Idempotency-Key` trong Header, nếu yêu cầu gặp lỗi gián đoạn mạng, gửi lại với cùng khóa này sẽ trả về ngay kết quả trước đó mà không xử lý tạo bản ghi mới trong database.


### Kiểm tra đầu ra hiện hành
API create_draft/update_post trả data.content_validation (valid, block_count, warnings, library_version).
Agent phải sửa các cảnh báo trước khi gửi duyệt. Số 2–4 tính theo số khối đặc biệt thực tế, bao gồm takeaway, FAQ, CTA; không tính heading và tag.
Master System Prompt và quy chuẩn thư viện UI luôn được áp dụng khi AI Agent tạo bài.
