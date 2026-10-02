# 05. PHÂN TÍCH SERP & SEARCH INTENT THỜI GIAN THỰC

Tài liệu này hướng dẫn AI Agent cách phân tích Top 10 kết quả tìm kiếm Google (SERP Scraping) và khóa chặt Ý định tìm kiếm (Search Intent) trước khi viết bài.

---

## 1. TẠI SAO PHẢI SOI SERP TRƯỚC KHI VIẾT?

Mỗi từ khóa trên Google đều có một **Search Intent** chủ đạo:
- Nếu người dùng tìm *"checklist onpage seo"* (Search Intent: Checklist/Template) mà AI viết bài *"Lịch sử phát triển SEO"* $\rightarrow$ Bài viết sẽ **không bao giờ lên Top**.
- Do đó, bước bắt buộc trước khi tạo bài viết là gọi API `analyze_serp` để xem Top 10 đối thủ đang dùng bố cục gì, số lượng từ trung bình là bao nhiêu và các thẻ H2/H3 họ đang đề cập.

---

## 2. API CÀO DỮ LIỆU SERP (`action=analyze_serp`)

### Request:
```http
POST {{API_ENDPOINT}}?action=analyze_serp
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "keyword": "tối ưu on-page seo 2026",
  "country": "vn",
  "language": "vi",
  "num_results": 10
}
```

### Dữ Liệu Trả Về (Response Schema):
```json
{
  "success": true,
  "data": {
    "keyword": "tối ưu on-page seo 2026",
    "search_intent": "informational",
    "results": [
      {
        "position": 1,
        "title": "Hướng Dẫn Tối Ưu On-Page SEO 2026 Toàn Diện A-Z",
        "url": "https://example.com/onpage-seo-guide",
        "snippet": "Khám phá 10 bước tối ưu On-Page SEO chuẩn Google E-E-A-T...",
        "headings": {
          "h2": ["1. On-Page SEO là gì?", "2. Checklist 10 yếu tố On-Page", "3. Các công cụ hỗ trợ"],
          "h3": ["2.1. Thẻ Title", "2.2. Thẻ Meta Description", "2.3. Cấu trúc URL"],
          "estimated_word_count": 2100
        }
      }
    ],
    "people_also_ask": [
      "On-page SEO gồm những công việc gì?",
      "Mật độ từ khóa bao nhiêu là chuẩn?"
    ],
    "related_searches": ["checklist onpage seo", "tối ưu onpage nâng cao"]
  }
}
```

---

## 3. TỰ ĐỘNG TẠO DÀN Ý TỪ SERP (`action=serp_outline`)

Gọi trực tiếp `serp_outline` để AI phân tích và tổng hợp dàn ý khuyến nghị:

### Request:
```http
POST {{API_ENDPOINT}}?action=serp_outline
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "keyword": "tối ưu on-page seo 2026",
  "country": "vn",
  "language": "vi"
}
```

### Kết Quả Trả Về:
- `search_intent`: Ý định tìm kiếm được phân loại (`informational`, `commercial`, `transactional`, `navigational`).
- `avg_competitor_word_count`: Số từ trung bình của Top 10 đối thủ (vd: 1,650 từ).
- `recommended_word_count`: Số từ mục tiêu khuyến nghị (+20% so với đối thủ, vd: 2,000 từ).
- `common_h2_topics`: Danh sách các chủ đề H2 xuất hiện nhiều nhất ở các bài Top 1.
- `content_gap_hints`: Các chủ đề ngách đối thủ bỏ quên mà bạn có thể khai thác để tạo **Information Gain**.

---

## 4. QUY TẮC GHÉP DÀN Ý VÀO MASTER PROMPT

1. **Lấy các H2 bắt buộc**: Phải bao gồm các chủ đề có trong `common_h2_topics`.
2. **Khai thác Content Gaps**: Bổ sung thêm 1–2 section H2 độc quyền từ `content_gap_hints`.
3. **Giải đáp People Also Ask**: Lấy các câu hỏi trong `people_also_ask` để đưa vào khối **FAQ Accordion** ở cuối bài viết.
