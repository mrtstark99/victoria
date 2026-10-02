# 08. PAYLOAD MẪU & KỊCH BẢN GỌI API (EXAMPLES & PAYLOADS)

Tài liệu này cung cấp toàn bộ mẫu Payload JSON chuẩn cho từng API Action.

---

## 1. TỰ KIỂM TRA QUYỀN HẠN (`action=me`)

```http
GET {{API_ENDPOINT}}?action=me
Authorization: Bearer <YOUR_AGENT_TOKEN>
```

### Response (200 OK):
```json
{
  "success": true,
  "message": "Agent identity and capabilities retrieved.",
  "data": {
    "token_name": "SEO Content Specialist Token",
    "default_author": "SEO Specialist",
    "scopes": ["admin", "posts:draft", "posts:publish", "seo:read", "seo:write", "tasks:read", "tasks:write"],
    "allowed_actions": [
      "me", "capabilities", "tasks", "create_task", "create_adhoc_task", "update_task", "complete_task",
      "analyze_serp", "serp_outline", "posts", "create_draft", "update_post", "submit_for_review", "publish_post",
      "validate_post", "upload_image", "link_suggestions", "backlink_candidates", "link_audit",
      "ping_index", "check_index_status", "regenerate_sitemap"
    ],
    "rate_limit": "60 requests per minute",
    "review_enforcement": true,
    "indexnow_enabled": true
  }
}
```

---

## 2. QUẢN LÝ NHIỆM VỤ (TASKS)

### Lấy Danh Sách Nhiệm Vụ Chưa Làm:
```http
GET {{API_ENDPOINT}}?action=tasks&status=pending
Authorization: Bearer <TOKEN>
```

### Tạo Task Phát Sinh Trong Ngày:
```http
POST {{API_ENDPOINT}}?action=create_adhoc_task
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "content": "Audit và chèn thêm 3 internal links cho bài viết Silo Structure",
  "priority": "high",
  "category": "SEO On-page",
  "notes": "Phát hiện trang này có ít hơn 2 liên kết trỏ về"
}
```

### Báo Cáo Hoàn Thành Nhiệm Vụ:
```http
POST {{API_ENDPOINT}}?action=complete_task
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "task_id": 12,
  "notes": "Đã tạo bài nháp ID #25, slug: 'huong-dan-silo-structure'. Điểm E-E-A-T đạt 85/100, SEO score 96/100."
}
```

---

## 3. UPLOAD ẢNH CHUẨN SEO (`action=upload_image`)

```http
POST {{API_ENDPOINT}}?action=upload_image
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "image_url": "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200",
  "seo_filename": "mo-hinh-topic-cluster-silo-structure",
  "keyword": "topic cluster silo structure",
  "alt_text": "Mô hình kiến trúc Topic Cluster và Silo Structure chuẩn SEO 2026",
  "caption": "Sơ đồ luồng phân phối PageRank giữa bài Pillar và các bài Cluster",
  "force_webp": true,
  "quality": 85
}
```

### Response:
```json
{
  "success": true,
  "data": {
    "filename": "mo-hinh-topic-cluster-silo-structure-1724000000.webp",
    "filepath": "mo-hinh-topic-cluster-silo-structure-1724000000.webp",
    "url": "/uploads/mo-hinh-topic-cluster-silo-structure-1724000000.webp",
    "width": 1200,
    "height": 675,
    "size_bytes": 74500,
    "format": "webp",
    "alt_text": "Mô hình kiến trúc Topic Cluster và Silo Structure chuẩn SEO 2026",
    "optimized": true
  }
}
```

---

## 4. TẠO BÀI VIẾT NHÁP HOÀN CHỈNH (`action=create_draft`)

```http
POST {{API_ENDPOINT}}?action=create_draft
Content-Type: application/json
Authorization: Bearer <TOKEN>
Idempotency-Key: c8a3d5b2-9a1f-4f8a-bc3e-7b19a8f2c001

{
  "title": "Hướng Dẫn Xây Dựng Topic Cluster & Silo Structure Thực Chiến 2026",
  "category_id": 1,
  "featured_image": "/uploads/mo-hinh-topic-cluster-silo-structure-1724000000.webp",
  "meta_title": "Hướng Dẫn Xây Dựng Topic Cluster Thực Chiến 2026 | My SEO Blog",
  "meta_description": "Nắm vững quy trình 5 bước xây dựng Topic Cluster và cấu trúc Silo bền vững giúp website thống trị Top Google bền vững.",
  "meta_keywords": "topic cluster, silo structure, onpage seo, internal linking",
  "content": "<div class=\"key-takeaways\"><div class=\"takeaway-badge\">⭐ ĐIỂM CỐT LÕI (KEY TAKEAWAYS)</div><ul class=\"takeaway-list\"><li>Topic Cluster giúp website thống trị Topical Authority trong ngành.</li><li>Liên kết nội bộ 2 chiều bảo toàn và dàn đều PageRank.</li></ul></div><h2 class=\"heading-number\"><span class=\"num-circle\">01</span> Bản Chất Của Mô Hình Topic Cluster</h2><p>Topic Cluster là phương pháp nhóm các bài viết có liên quan chặt chẽ về mặt ngữ nghĩa xung quanh một trang trụ cột (Pillar Page)...</p><div class=\"callout callout-tip\"><div class=\"callout-title\">💡 MẸO THỰC CHIẾN</div><p>Mỗi cụm chủ đề nên có tối thiểu từ 5 đến 15 bài vệ tinh để tạo sức bật mạnh nhất.</p></div><h2 class=\"heading-badge\"><span>PHẦN 2</span> Quy Trình 3 Bước Triển Khai</h2><div class=\"step-cards\"><div class=\"step-card\"><div class=\"step-num\">BƯỚC 1</div><div class=\"step-title\">Nghiên cứu từ khóa</div><p>Gom nhóm Search Intent bằng Topic Modeling.</p></div><div class=\"step-card\"><div class=\"step-num\">BƯỚC 2</div><div class=\"step-title\">Sản xuất nội dung</div><p>Viết bài Pillar trước rồi đến các bài vệ tinh.</p></div></div><h2 class=\"heading-underline\">Hỏi Đáp Thường Gặp (FAQ)</h2><div class=\"faq-accordion\"><details class=\"faq-item\"><summary class=\"faq-question\">Mất bao lâu để hoàn thành một Topic Cluster?</summary><div class=\"faq-answer\"><p>Thông thường một cụm từ 10 bài viết có thể xuất bản hoàn thiện trong 2 đến 4 tuần.</p></div></details></div><div class=\"cta-box\"><div class=\"cta-title\">🚀 Bắt Đầu Quy Hoạch Cụm Chủ Đề Ngay Hôm Nay</div><p class=\"cta-desc\">Tham khảo tài liệu hướng dẫn và template lập kế hoạch SEO độc quyền.</p><a href=\"/lien-he\" class=\"btn btn-primary\">Nhận Template Miễn Phí</a></div>",
  "custom_schema_json": {
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "Mất bao lâu để hoàn thành một Topic Cluster?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Thông thường một cụm từ 10 bài viết có thể xuất bản hoàn thiện trong 2 đến 4 tuần."
        }
      }
    ]
  }
}
```

---

## 5. AUDIT & CHẤM ĐIỂM E-E-A-T BÀI VIẾT (`action=validate_post`)

```http
POST {{API_ENDPOINT}}?action=validate_post
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "title": "Hướng Dẫn Xây Dựng Topic Cluster & Silo Structure Thực Chiến 2026",
  "content": "<p>Nội dung bài viết...</p>",
  "meta_title": "Hướng Dẫn Xây Dựng Topic Cluster Thực Chiến 2026 | My SEO Blog",
  "meta_description": "Nắm vững quy trình 5 bước xây dựng Topic Cluster..."
}
```

### Response:
```json
{
  "success": true,
  "data": {
    "passed": true,
    "score": 95,
    "errors": [],
    "warnings": [],
    "eeat_analysis": {
      "eeat_score": 85,
      "has_citations": true,
      "has_statistics": true,
      "has_expert_mention": true,
      "has_images": true,
      "has_structured_content": true,
      "internal_link_count": 4,
      "grade": "A",
  }
}
```

---

## 6. QUẢN LÝ TRANG TĨNH & DYNAMIC PAGES (`action=create_page`)

### Tạo Trang Mới:
```http
POST {{API_ENDPOINT}}?action=create_page
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "title": "Bảng Giá Dịch Vụ Tư Vấn SEO & AI",
  "slug": "bang-gia-dich-vu",
  "excerpt": "Tổng hợp các gói dịch vụ tư vấn triển khai SEO Semantic và tích hợp AI Agent toàn diện cho doanh nghiệp.",
  "content": "<h2>Bảng giá dịch vụ</h2><p>Lựa chọn gói giải pháp tối ưu cho doanh nghiệp...</p>",
  "template": "default",
  "status": "published",
  "meta_title": "Bảng Giá Dịch Vụ SEO & AI 2026 | MinimaList",
  "meta_description": "Khám phá các gói tư vấn SEO On-page và AI Agent tăng trưởng thứ hạng bền vững.",
  "custom_schema_json": {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Bảng Giá Dịch Vụ SEO"
  }
}
```

### Response:
```json
{
  "success": true,
  "message": "Page created successfully.",
  "data": {
    "page_id": 4,
    "url": "/page/bang-gia-dich-vu",
    "page": {
      "id": 4,
      "title": "Bảng Giá Dịch Vụ Tư Vấn SEO & AI",
      "slug": "bang-gia-dich-vu",
      "status": "published"
    }
  }
}
```

---

## 7. ĐIỀU KHIỂN MENU & FOOTER (`action=update_navigation`)

### Cập Nhật Menu Header và Các Cột Footer:
```http
POST {{API_ENDPOINT}}?action=update_navigation
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "nav_items": [
    {"label": "Trang chủ", "url": "/", "target": "_self", "is_active": true},
    {"label": "Tối ưu SEO", "url": "/category/toi-uu-seo", "target": "_self", "is_active": true},
    {"label": "Hướng dẫn AI", "url": "/category/huong-dan", "target": "_self", "is_active": true},
    {"label": "Bảng giá", "url": "/page/bang-gia-dich-vu", "target": "_self", "is_active": true},
    {"label": "Liên hệ", "url": "/page/lien-he", "target": "_self", "is_active": true}
  ],
  "footer_col3": {
    "title": "Thông Tin & Dịch Vụ",
    "links": [
      {"label": "Về chúng tôi", "url": "/page/gioi-thieu"},
      {"label": "Bảng giá dịch vụ", "url": "/page/bang-gia-dich-vu"},
      {"label": "Chính sách bảo mật", "url": "/page/chinh-sach-bao-mat"},
      {"label": "Liên hệ hợp tác", "url": "/page/lien-he"}
    ]
  }
}
```

### Response:
```json
{
  "success": true,
  "message": "Navigation settings updated successfully.",
  "data": {
    "updated": {
      "nav_items": [...],
      "footer_col3": {...}
    }
  }
}
```

```
