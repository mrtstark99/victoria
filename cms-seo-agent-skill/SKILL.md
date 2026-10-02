---
name: cms-seo-agent-skill
description: "Bộ kỹ năng toàn diện cho AI Agent tích hợp với hệ thống CMS Blog SEO: Nghiên cứu SERP, Viết bài chuẩn E-E-A-T, UI Elements Library, Internal Linking 2 chiều, Schema JSON-LD, Indexing Push, Quản lý Trang tĩnh (Pages), Menu Navigation & Footer và Quản lý Task tự động."
version: "4.0.0"
author: "CMS Blog SEO Team"
tags: ["seo", "ai-agent", "cms", "content-writing", "schema-markup", "internal-linking", "serp-analysis", "pages-management", "navigation"]
---

# CMS BLOG SEO AGENT SKILL (V4.0)

Bộ Skill này đóng gói toàn bộ quy trình, công cụ API, hướng dẫn viết bài thuần Prompt, quản trị Trang tĩnh (Pages), Menu Điều Hướng & Footer và thư viện UI Elements cho AI Agent tự động hóa 100% nghiệp vụ SEO Content & Website Management.

### 🌐 Thông Số Kết Nối Hệ Thống:
- **Base URL Website**: `{{BASE_URL}}`
- **Cổng API Endpoint**: `{{API_ENDPOINT}}`
- **Host / Server**: `{{SITE_HOST}}`

---

## 📂 CẤU TRÚC BỘ SKILL (MODULAR ARCHITECTURE)

Bộ kỹ năng được chia nhỏ thành các tài liệu chuyên biệt để AI Agent nạp ngữ cảnh nhanh và chính xác nhất:

| File | Tên tài liệu | Mục đích & Trách nhiệm |
| :--- | :--- | :--- |
| [`AGENT.md`](./AGENT.md) | **Agent Manifest & Verification** | File xác nhận danh tính, kiểm tra quyền và các ràng buộc cốt lõi trước khi chạy. |
| [`01_authentication_and_api.md`](./01_authentication_and_api.md) | **API Reference & Giao thức Kết nối** | Danh mục đầy đủ 24+ API actions, Bearer Auth, Scopes, Rate limit, Idempotency. |
| [`02_workflow_and_task_lifecycle.md`](./02_workflow_and_task_lifecycle.md) | **Vòng đời Nhiệm vụ & Event Pipeline** | Quy trình nhận việc, cập nhật tiến độ, hoàn thành task và kích hoạt Event Hooks. |
| [`03_ai_content_writer_prompt.md`](./03_ai_content_writer_prompt.md) | **Quy Chuẩn Viết Bài Thuần Prompt** | Master Prompt, tiêu chuẩn Google E-E-A-T, Search Intent, cấu trúc PAS/AIDA, chống AI Slop. |
| [`04_ui_elements_library.md`](./04_ui_elements_library.md) | **Thư viện UI Elements Chuẩn HTML/CSS** | 13 nhóm giao diện chuẩn hóa (TOC, Callout, Pros/Cons, Table, FAQ, CTA...). |
| [`05_serp_and_intent_analysis.md`](./05_serp_and_intent_analysis.md) | **Phân tích SERP & Đối thủ Thời Gian Thực** | Công cụ cào dữ liệu Top 10 Google, bóc tách Heading đối thủ, phát hiện Content Gap. |
| [`06_internal_linking_and_seo_schema.md`](./06_internal_linking_and_seo_schema.md) | **Internal Linking 2 Chiều & Schema JSON-LD** | Gợi ý link đi/về, vá Orphan Pages, chèn JSON-LD (FAQPage, HowTo, BlogPosting). |
| [`07_indexing_and_automation.md`](./07_indexing_and_automation.md) | **Tự động hóa Indexing & Giám sát Rank** | Giao thức IndexNow, Ping Sitemap, Event Monitor phát hiện rớt hạng. |
| [`08_examples_and_payloads.md`](./08_examples_and_payloads.md) | **Payload Mẫu & Kịch Bản Gọi API** | Toàn bộ mẫu Request / Response JSON thực chiến cho mọi tình huống. |
| [`09_cms_site_map_and_safe_changes.md`](./09_cms_site_map_and_safe_changes.md) | **Bản đồ CMS & Xác nhận** | Chọn đúng khu vực website, action, quyền và xác nhận trước khi ghi dữ liệu. |
| [`10_site_style_css_guidelines.md`](./10_site_style_css_guidelines.md) | **Quy chuẩn CSS toàn site** | Design tokens, route/template, stylesheet đúng trang, responsive và kiểm tra giao diện. |

---

## 🚀 QUY TRÌNH THỰC THI 8 BƯỚC (QUICK WORKFLOW)

```mermaid
flowchart TD
    A[1. Đọc AGENT.md & Xác thực Token] --> B[2. Lấy Task từ GET {{API_ENDPOINT}}?action=tasks]
    B --> C[3. Cào SERP qua action=analyze_serp & Khóa Intent]
    C --> D[4. Nạp Master Prompt từ 03_ai_content_writer_prompt.md]
    D --> E[5. Ghép 2-4 UI Elements từ 04_ui_elements_library.md]
    E --> F[6. Lấy link nội bộ & Build Schema JSON-LD]
    F --> G[7. Gửi bản nháp qua action=create_draft]
    G --> H[8. Báo cáo hoàn thành qua action=complete_task]
```

---

## 🔒 NGUYÊN TẮC BẤT DI BẤT DỊCH (NON-NEGOTIABLE RULES)

1. **Bảo mật**: Chỉ truyền Token qua Header `Authorization: Bearer <TOKEN>`. Nghiêm cấm truyền qua URL query string.
2. **Đồng bộ tiến độ**: Không tự giữ task ngầm trong bộ nhớ cục bộ (Local Context). Mọi công việc phải được ghi nhận và tick hoàn thành trên CMS.
3. **Nội dung thực chiến**: Tuân thủ nghiêm ngặt Master Prompt tại [`03_ai_content_writer_prompt.md`](./03_ai_content_writer_prompt.md) để không tạo ra AI Slop (nội dung rác).
4. **UI Elements**: Chỉ sử dụng đúng các class CSS và thẻ HTML được quy định tại [`04_ui_elements_library.md`](./04_ui_elements_library.md).
5. **Định tuyến và xác nhận**: Đọc [`09_cms_site_map_and_safe_changes.md`](./09_cms_site_map_and_safe_changes.md) trước thao tác CMS. Không ghi khi chưa phân loại rõ đích và chưa có xác nhận rõ ràng của người dùng.
6. **Style CSS trang**: Khi sửa giao diện, đọc [`10_site_style_css_guidelines.md`](./10_site_style_css_guidelines.md); kiểm tra đúng template/CSS đang nạp, giữ nhận diện Bright, responsive, không vá global CSS hay chèn style vào nội dung CMS.


## Hợp đồng vận hành hiện hành (ưu tiên hơn ví dụ tĩnh)
- Đầu phiên gọi action=me và action=guidelines. data.system_prompt là hướng dẫn hiệu lực; data.elements_library chứa HTML và version. File tĩnh chỉ để tham khảo ngoại tuyến.
- Master System Prompt và hợp đồng UI luôn được áp dụng khi AI Agent tạo bài.
- Chọn 2–4 khối đặc biệt thực tế theo nhu cầu độc giả; takeaway, FAQ, CTA cũng tính vào tổng. Không tính heading/tag. CMS tự tạo H1 và mục lục.
- Không bắt buộc nhồi mọi loại khối vào mọi bài. Bảng cho so sánh, steps cho quy trình, callout cho ngoại lệ. Không sao chép số liệu, trích dẫn hoặc URL mẫu mà chưa xác minh.
- Chỉ gọi action trong danh sách me và với scopes phù hợp. Thiếu SERP/API key: ghi giới hạn vào task notes, không bịa phân tích đối thủ; giữ bản nháp để bổ sung nghiên cứu.
- create_draft/update_post trả data.content_validation. Sửa warnings qua update_post, đọc bài mới nhất và gửi expected_updated_at để tránh ghi đè thay đổi của người khác.
- submit_for_review/approve_post/publish_post trả 422 nếu nội dung chưa đạt kiểm tra UI. Không tự động approve/publish trừ khi nhiệm vụ và quyền hiện hành cho phép.
- Các kiểm tra cấu trúc UI không phải chấm điểm E-E-A-T hoặc xác minh sự thật. Tự kiểm nguồn, intent, link và tính hữu ích trước khi gửi duyệt.
- complete_task chỉ sau khi có kết quả đúng yêu cầu; notes ghi ID bài, trạng thái, nguồn dữ liệu, kiểm tra và giới hạn còn lại.
