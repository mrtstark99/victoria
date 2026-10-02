# 01. AUTHENTICATION & API REFERENCE

Tài liệu này cung cấp đặc tả kỹ thuật chi tiết của hệ thống API Gateway dành cho AI Agent (`{{API_ENDPOINT}}`).

### 🌐 Thông Số Kết Nối Hệ Thống:
- **Base URL Website**: `{{BASE_URL}}`
- **Cổng API Endpoint**: `{{API_ENDPOINT}}`
- **Host / Server**: `{{SITE_HOST}}`

---

## 1. PHƯƠNG THỨC XÁC THỰC (AUTHENTICATION)

Mọi yêu cầu từ AI Agent phải gửi qua giao thức HTTP/HTTPS với **Bearer Token** trong phần Header:

```http
POST {{API_ENDPOINT}}?action=create_draft HTTP/1.1
Host: {{SITE_HOST}}
Authorization: Bearer <YOUR_AGENT_TOKEN>
Content-Type: application/json; charset=utf-8
Idempotency-Key: 9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d
```


### Các Headers Tiêu Chuẩn:

| Header | Bắt buộc | Ý nghĩa / Giá trị |
| :--- | :--- | :--- |
| `Authorization` | **Có** | Định dạng: `Bearer <YOUR_AGENT_TOKEN>`. |
| `Content-Type` | **Có** (cho POST) | Luôn là `application/json; charset=utf-8`. |
| `Idempotency-Key` | Khuyên dùng | Chuỗi UUID ngẫu nhiên cho mỗi thao tác tạo/sửa để chống tạo bài trùng khi lag mạng. |

> [!WARNING]
> **Quy định bảo mật**: Tuyệt đối không truyền token qua URL `?token=...`. Request sẽ bị trả về lỗi `400 Bad Request`.

---

## 2. GIỚI HẠN AN TOÀN & ĐIỀU PHỐI (RATE LIMITS & SECURITY)

- **Rate Limit**: Tối đa **60 requests/phút** cho mỗi token. Vượt quá sẽ nhận `429 Too Many Requests`.
- **IP Allowlist**: Nếu token được giới hạn IP, chỉ các IP trong danh sách mới được phép gọi API (`403 Forbidden` nếu sai IP).
- **Idempotency Mechanism**: Nếu cùng 1 `Idempotency-Key` được gửi lại trong vòng 24h, hệ thống trả về ngay kết quả đã lưu trữ kèm header `X-Cache-Lookup: HIT - Idempotent Request`.

---

## 3. BẢNG DANH MỤC TẤT CẢ CÁC QUYỀN (SCOPES / PERMISSIONS)

Khi Admin cấp hoặc cấu hình Bearer Token cho AI Agent, mỗi Token sẽ được gán một hoặc nhiều **Scopes** để giới hạn phạm vi truy cập:

| Nhóm Quyền | Mã Quyền (Scope) | Mô tả Quyền Hạn | Mở Khóa Các Actions |
| :--- | :--- | :--- | :--- |
| **Hệ Thống** | `admin` | **Toàn quyền tối cao** — Cho phép thực thi mọi action, bỏ qua mọi giới hạn scope. | *Tất cả các actions* |
| **Bài Viết** | `posts:read` | Xem danh sách bài viết, xem chi tiết bài, lịch sử revisions, audit liên kết nội bộ. | `posts`, `list_revisions`, `link_suggestions`, `backlink_candidates`, `link_audit` |
| | `posts:draft` | Tạo bài nháp (`ai_draft`), cập nhật bài viết, submit duyệt, phân tích SEO & E-E-A-T, khôi phục revision. | `create_draft`, `update_post`, `submit_for_review`, `restore_revision`, `process_draft`, `validate_post` |
| | `posts:publish` | Phê duyệt (`approved`) và xuất bản bài viết (`published`), tự động ping sitemap & IndexNow. | `approve_post`, `publish_post` |
| **Trang Tĩnh** | `pages:read` | Xem danh sách các trang tĩnh (About, Contact, Privacy...), xem chi tiết và lịch sử sửa đổi. | `pages`, `list_pages`, `get_page` |
| | `pages:draft` | Tạo trang mới (`create_page`), cập nhật nội dung, template, SEO metadata, custom schema của trang. | `create_page`, `create_page_draft`, `update_page` |
| | `pages:publish` | Xuất bản trang tĩnh (`publish_page`) và xóa trang khỏi hệ thống (`delete_page`). | `publish_page`, `delete_page` |
| **Dịch vụ du học** | `services:read` | Xem danh mục chương trình và chi tiết chương trình/gói hồ sơ. | `services`, `list_services`, `get_service` |
| | `services:write` | Tạo chương trình ẩn hoặc sửa chương trình/gói hồ sơ tại `/services`. Mọi POST cần xác nhận trước (`user_confirmed: true`). | `create_service`, `update_service` |
| | `services:publish` | Hiện/ẩn chương trình; sửa chương trình đang hoạt động; cần xác nhận trước. | `activate_service`, `deactivate_service`, `update_service` |
| | `services:delete` | Xóa chương trình; cần xác nhận trước. | `delete_service` |
| **Chi phí & Dự toán** | `cost:read`, `cost:write` | Đọc/cập nhật riêng bảng ước tính chi phí `/cost`; ghi cần xác nhận trước (`user_confirmed: true`). | `get_cost_page`, `update_cost_page` |
| | `calculator:read`, `calculator:write` | Đọc/cập nhật các mức giá trong công cụ dự toán trang chủ. | `get_home_calculator`, `update_home_calculator` |
| **Trang chủ** | `homepage:read`, `homepage:write` | Đọc/cập nhật hero, cam kết, thứ tự và trạng thái các section trang chủ. | `get_homepage`, `update_homepage` |
| **Trang nội dung cố định** | `fixed_pages:read`, `fixed_pages:write` | Đọc/cập nhật nội dung trang about, schools, courses, process, documents, consultation, qa. | `get_fixed_page`, `update_fixed_page`, `reset_fixed_page` |
| **Liên hệ tư vấn** | `contacts:read`, `contacts:write`, `contacts:delete` | Xem, cập nhật trạng thái/ghi chú hoặc xóa yêu cầu tư vấn. | `contacts`, `get_contact`, `update_contact`, `delete_contact` |
| **Hồ sơ tác giả** | `profile:read`, `profile:write` | Đọc/cập nhật bio của tác giả được gán cho Agent. | `get_author_profile`, `update_author_profile` |
| **Sidebar bài viết** | `sidebar:read`, `sidebar:write` | Đọc/cập nhật các widget cột bên phải bài viết. | `get_post_sidebar`, `update_post_sidebar` |
| **Cấu hình đo lường** | `analytics:settings:read`, `analytics:settings:write` | Đọc/cập nhật GA4, Search Console, KPI và chi phí SEO. | `get_analytics_settings`, `update_analytics_settings` |
| **Menu & Footer** | `navigation:read` | Đọc cấu trúc Header Navigation, Mobile Drawer, Cột Footer 2, 3 và Bottom Bar Links. | `get_navigation`, `navigation`, `get_menus`, `get_footer`, `footer` |
| | `navigation:write` | Tùy biến / Cập nhật Menu Header (tối đa 5 mục), các cột Footer và link chân trang. | `update_navigation`, `update_menus`, `update_footer` |
| **Hình Ảnh** | `media:upload` | Upload ảnh trực tiếp hoặc fetch ảnh từ URL, tự động chuyển WebP và tối ưu SEO. | `upload_image` |
| **SEO & Index** | `seo:read` | Xem Keyword Map, Topic Clusters, KPI kế hoạch và tình trạng index. | `seo`, `check_index_status` |
| | `seo:write` | Tạo / sửa / xóa từ khóa, Topic Cluster, ping IndexNow và tạo lại sitemap. | `create_keyword`, `update_keyword`, `delete_keyword`, `ping_index`, `regenerate_sitemap` |
| | `serp:read` | Cào dữ liệu Top 10 Google SERP, phân tích Search Intent và tự sinh dàn ý bài viết. | `analyze_serp`, `serp_outline` |
| **Nhiệm Vụ** | `tasks:read` | Xem danh sách việc cần làm (Task Board), xem danh sách Event Hooks và Event Log. | `tasks`, `list_tasks`, `get_tasks`, `list_hooks`, `event_log` |
| | `tasks:write` | Tạo nhiệm vụ mới, tạo ad-hoc task, cập nhật, hoàn thành task, cấu hình Event Hooks. | `create_task`, `create_adhoc_task`, `update_task`, `complete_task`, `delete_task`, `create_hook`, `update_hook` |
| **Chuyên Mục** | `category:read` | Lấy danh sách chuyên mục bài viết. | `categories`, `list_categories` |
| | `category:write` | Tạo, chỉnh sửa hoặc xóa chuyên mục. | `create_category`, `update_category`, `delete_category` |
| **Thương Hiệu** | `brand:read` | Xem thông tin thương hiệu, logo, slogan, mạng xã hội, meta SEO mặc định. | `brand`, `get_brand`, `default_seo`, `get_default_seo`, `guidelines`, `get_guidelines` |
| | `brand:write` | Cập nhật thông tin thương hiệu, SEO mặc định, cấu hình Guidelines & System Prompt. | `update_brand`, `update_default_seo`, `update_guidelines` |
| **Hiệu Suất** | `analytics:read` | Xem thống kê lượt xem, hiệu suất bài viết và các cơ hội tối ưu nội dung. | `analytics`, `page_performance`, `opportunities` |

---

## 4. DANH MỤC TOÀN BỘ API ACTIONS & SCOPES

API sử dụng kiến trúc Single Endpoint, điều hướng qua tham số `?action=<ten_action>`:

### Nhóm 1: Nhận Diện & Quyền Hạn (Identity & Capabilities)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `me` / `capabilities` | `GET` | *(Mọi Token)* | Xem thông tin token, quyền scopes, danh sách actions được phép, cờ `review_enforcement`, `indexnow_enabled`. |

### Nhóm 2: Quản Lý Kế Hoạch & Nhiệm Vụ (Tasks Management)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `tasks` / `list_tasks` | `GET` | `tasks:read` / `posts:read` | Lấy danh sách task, hỗ trợ filter: `status`, `priority`, `cycle_type`, `scheduled_date`, `category`. |
| `create_task` | `POST` | `tasks:write` / `posts:draft` | Tạo nhiệm vụ mới vào lịch trình. |
| `create_adhoc_task` | `POST` | `tasks:write` / `posts:draft` | Tạo nhanh nhiệm vụ phát sinh vào ngày hôm nay (`is_ad_hoc: 1`). |
| `update_task` | `POST` | `tasks:write` / `posts:draft` | Cập nhật nội dung, deadline, priority, notes của task. |
| `complete_task` | `POST` | `tasks:write` / `posts:draft` | Đánh dấu hoàn thành task kèm ghi chú kết quả. |
| `delete_task` | `POST` | `tasks:write` | Xóa task khỏi bảng kế hoạch. |

### Nhóm 3: Phân Tích SERP & Ý Định Tìm Kiếm (SERP & Intent Analysis)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `analyze_serp` | `POST` | `serp:read` / `seo:read` | Cào dữ liệu Top 10 Google cho từ khóa: titles, snippets, headings H2/H3, phân loại Intent. |
| `serp_outline` | `POST` | `serp:read` / `seo:read` | Tự động tạo dàn ý bài viết và khuyến nghị độ dài từ phân tích đối thủ SERP. |

### Nhóm 4: Bài Viết & Nội Dung (Posts & Content Operations)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `posts` | `GET` | `posts:read` | Lấy danh sách bài viết trên website có phân trang và bộ lọc. |
| `create_draft` | `POST` | `posts:draft` | Tạo bài nháp mới (`ai_draft`), hỗ trợ `featured_image`, `custom_schema_json`, `meta_title`, `meta_description`. |
| `update_post` | `POST` | `posts:draft` | Cập nhật bài viết, hỗ trợ kiểm tra xung đột `expected_updated_at`. |
| `submit_for_review`| `POST` | `posts:draft` | Chuyển bài từ `ai_draft` sang `pending_review` để Admin duyệt. |
| `approve_post` | `POST` | `posts:publish` | Phê duyệt bài viết (chuyển sang `approved`). |
| `publish_post` | `POST` | `posts:publish` | Xuất bản bài viết (`published`), kích hoạt tự động hóa IndexNow và internal link task. |
| `list_revisions` | `GET` | `posts:read` | Xem lịch sử các bản sửa đổi của bài viết. |
| `restore_revision` | `POST` | `posts:draft` | Khôi phục bài viết về một revision lịch sử. |
| `process_draft` | `POST` | `posts:draft` | Phân tích bài nháp: đếm từ, tính thời gian đọc, sinh TOC động, slug & SEO meta. |
| `validate_post` | `POST` | `posts:draft` | Audit bài viết: kiểm tra thẻ H2, link bẩn/mã độc, độ dài, chấm điểm E-E-A-T. |
| `upload_image` | `POST` | `posts:draft` | Upload hoặc fetch ảnh từ URL, tự động chuyển WebP, nén tối ưu và đặt tên file chuẩn SEO. |

### Nhóm 5: Liên Kết Nội Bộ & Schema (Internal Linking & Schema)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `link_suggestions` | `GET`/`POST` | `posts:read` | Gợi ý danh sách bài viết cũ liên quan để chèn link đi (Outbound Internal Link). |
| `backlink_candidates` | `GET`/`POST` | `posts:read` | Tìm danh sách bài viết cũ cần chèn link ngược trỏ về bài mới xuất bản (Inbound Link). |
| `link_audit` | `GET` | `posts:read` | Audit toàn bộ website: phát hiện trang mồ côi (Orphan pages), trang thiếu/thừa link. |

### Nhóm 6: Lập Chỉ Mục & Sitemap (Indexing & Technical SEO)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `ping_index` | `POST` | `seo:write` / `posts:publish` | Gửi URL qua giao thức IndexNow (Bing/Yandex) và ping sitemap Google. |
| `check_index_status`| `GET`/`POST` | `seo:read` | Kiểm tra thời gian xuất bản và tình trạng index của bài viết. |
| `regenerate_sitemap`| `POST` | `seo:write` | Tạo lại file `public/sitemap.xml` với các bài viết mới nhất. |

### Nhóm 7: Kế Hoạch SEO & Từ Khóa (SEO Keyword Map)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `seo` | `GET` | `seo:read` | Lấy danh sách Topic Clusters, Keyword Map, KPI targets và số liệu GA4/GSC thực tế. |
| `create_keyword` | `POST` | `seo:write` | Thêm từ khóa vào kế hoạch SEO (tự động kích hoạt task nghiên cứu SERP). |
| `update_keyword` | `POST` | `seo:write` | Cập nhật trạng thái từ khóa (`idea`, `brief`, `writing`, `published`). |
| `delete_keyword` | `POST` | `seo:write` | Xóa từ khóa khỏi bảng kế hoạch. |

### Nhóm 8: Pipeline Sự Kiện (Event-Driven Hooks)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `list_hooks` | `GET` | `tasks:read` | Xem danh sách các Event Hooks đang kích hoạt trong hệ thống. |
| `create_hook` | `POST` | `tasks:write` | Tạo webhook nội bộ kích hoạt khi có event (vd: `post_published` $\rightarrow$ `ping_index`). |
| `update_hook` | `POST` | `tasks:write` | Bật/tắt hoặc chỉnh sửa cấu hình event hook. |
| `event_log` | `GET` | `tasks:read` | Xem nhật ký các sự kiện đã được kích hoạt trong hệ thống. |

### Nhóm 9: Quản Lý Trang Tĩnh & Dynamic Pages (Pages Operations)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `pages` / `list_pages` | `GET` | `pages:read` / `posts:read` | Lấy danh sách trang tĩnh trên website kèm bộ lọc `status`, `search` và thống kê. |
| `get_page` | `GET`/`POST` | `pages:read` / `posts:read` | Lấy chi tiết nội dung trang theo `id` hoặc `slug`, kèm lịch sử revisions. |
| `create_page` / `create_page_draft` | `POST` | `pages:draft` / `posts:draft` | Tạo trang mới (hỗ trợ templates: `default`, `fullwidth`, `contact`, `landing`, SEO meta, Schema). |
| `update_page` | `POST` | `pages:draft` / `posts:draft` | Chỉnh sửa nội dung, slug, template, SEO metadata và trạng thái của trang. |
| `publish_page` | `POST` | `pages:publish` / `posts:publish` | Xuất bản trang (`published`), tự động cập nhật vào `sitemap.xml`. |
| `delete_page` | `POST` | `pages:publish` / `admin` | Xóa trang khỏi hệ thống. |

### Nhóm 10: Điều Khiển Menu & Chân Trang (Navigation & Footer Controls)

| Action | Method | Scope Yêu cầu | Mô tả |
| :--- | :--- | :--- | :--- |
| `get_navigation` / `get_menus` | `GET` | `navigation:read` / `brand:read` | Lấy toàn bộ cấu trúc Header Menu, Mobile Drawer, các cột Footer và link bản quyền. |
| `update_navigation` / `update_menus` | `POST` | `navigation:write` / `brand:write` | Cập nhật toàn bộ liên kết Header Navigation, link Footer Col 2, Col 3 và Bottom links. |
| `get_footer` / `footer` | `GET` | `navigation:read` / `brand:read` | Xem thông tin văn bản giới thiệu Footer, bản quyền và các liên kết chân trang. |
| `update_footer` | `POST` | `navigation:write` / `brand:write` | Cập nhật văn bản giới thiệu, bản quyền và các cột liên kết Footer. |

### Nhóm 11: Dịch vụ du học và chi phí

| Action | Method | Scope Yêu cầu | Dữ liệu / trang bị tác động |
| :--- | :--- | :--- | :--- |
| `services` / `list_services` | `GET` | `services:read` | Danh mục chương trình trên `/services`. |
| `get_service` | `GET` | `services:read` | Chi tiết chương trình, bao gồm `packages[]`. |
| `create_service` / `update_service` | `POST` | `services:write` | Tạo/sửa chương trình và gói hồ sơ trên `/services`; JSON phải có `user_confirmed: true` sau khi người dùng duyệt. Tạo mới luôn ẩn. |
| `activate_service` / `deactivate_service` | `POST` | `services:publish` | Hiện/ẩn chương trình; JSON phải có `id`, `user_confirmed: true`. |
| `delete_service` | `POST` | `services:delete` | Xóa chương trình; JSON phải có `id`, `user_confirmed: true`. |
| `get_cost_page` | `GET` | `cost:read` | Đọc bảng chi phí `/cost`. |
| `update_cost_page` | `POST` | `cost:write` | Cập nhật bảng chi phí `/cost`; JSON phải có `user_confirmed: true` sau khi người dùng duyệt. |

Gói dịch vụ thuộc `/services`; học phí/sinh hoạt phí thuộc `/cost`; calculator trang chủ thuộc `calculator`. Không dùng các action thay thế cho nhau.

---

## 4. BẢNG MÃ LỖI CHUẨN (HTTP STATUS CODES)

| Code | Tên lỗi | Nguyên nhân & Cách xử lý |
| :--- | :--- | :--- |
| `200` | **OK** | Yêu cầu xử lý thành công. Dữ liệu nằm trong trường `data`. |
| `400` | **Bad Request** | Thiếu tham số bắt buộc hoặc truyền Token qua query string. Kiểm tra lại payload. |
| `401` | **Unauthorized** | Token không hợp lệ, bị thu hồi hoặc đã hết hạn. |
| `403` | **Forbidden** | Token thiếu Scope quyền hạn hoặc vi phạm quy tắc review trước publish. |
| `428` | **Precondition Required** | Thiếu `user_confirmed: true` cho thay đổi Services/Cost; chưa có xác nhận trực tiếp thì phải trình bày preview và chờ người dùng. |
| `404` | **Not Found** | Không tìm thấy bài viết, task hoặc từ khóa tương ứng với ID. |
| `409` | **Conflict** | Dữ liệu bị xung đột phiên bản (Optimistic Locking) hoặc từ khóa đã tồn tại trong tháng. |
| `429` | **Too Many Requests** | Vượt quá giới hạn 60 requests/phút. Cần dừng và đợi 60s. |
| `500` | **Internal Server Error** | Lỗi xử lý máy chủ. Xem chi tiết lỗi trong trường `error`. |
