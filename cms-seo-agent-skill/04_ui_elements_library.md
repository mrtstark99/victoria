# 04. THƯ VIỆN CÁC KHỐI HTML/CSS ĐỊNH DẠNG CHUẨN (UI ELEMENTS LIBRARY)

> [!IMPORTANT]
> **QUY TẮC SỬ DỤNG CHO AI AGENT**:
> 1. Bộ CSS đã được biên dịch và tích hợp sẵn trong hệ thống (`public/assets/css/post/`), hỗ trợ hoàn hảo cả **Light Mode** và **Dark Mode**.
> 2. Mỗi bài viết chỉ nên chọn lọc **2 – 4 khối đặc biệt thực tế (gồm takeaway, FAQ, CTA; không tính heading và tag)** phù hợp nhất với Search Intent.
> 3. Không đặt các khối đặc biệt dính liền kề nhau; luôn có các đoạn văn `<p>` giải thích xen kẽ.
> 4. Sao chép chính xác cấu trúc HTML và tên class dưới đây. Không dùng emoji trong khối; mọi khung viền phải đều bốn phía, không nhấn viền dày ở một cạnh.

---

## BẢNG TỔNG HỢP CÁC KHỐI GIAO DIỆN (UI ELEMENTS)

---

### 01. Mục Lục Bài Viết (Table of Contents - Tự động từ CMS)
* **Lưu ý**: Hệ thống CMS **tự động quét các thẻ H2/H3 và sinh Mục lục tương tác** ở đầu bài viết. AI Agent **KHÔNG CẦN** chèn thủ công khối `.table-of-contents` vào nội dung bài viết nữa để tránh trùng lặp 2 mục lục.
* **HTML Snippet (chỉ dùng khi cần tùy biến mục lục thủ công ngoài luồng CMS)**:
```html
<div class="table-of-contents">
  <div class="toc-title">Mục Lục Nội Dung</div>
  <ul>
    <li><a href="#phan-1-tong-quan">1. Tổng quan về tối ưu On-Page SEO</a></li>
    <li><a href="#phan-2-quy-trinh">2. Quy trình 5 bước triển khai thực chiến</a>
      <ul>
        <li><a href="#buoc-2-1">2.1. Phân tích Search Intent</a></li>
        <li><a href="#buoc-2-2">2.2. Tối ưu thẻ Heading & Meta</a></li>
      </ul>
    </li>
    <li><a href="#phan-3-faq">3. Các câu hỏi thường gặp (FAQ)</a></li>
  </ul>
</div>
```

---

### 02. Điểm Cốt Lõi (Key Takeaways)
* **Công dụng**: Tóm lược 3–4 giá trị đắt giá nhất của bài viết.
* **HTML Snippet**:
```html
<div class="key-takeaways">
  <div class="takeaway-badge">ĐIỂM CỐT LÕI (KEY TAKEAWAYS)</div>
  <ul class="takeaway-list">
    <li><strong>Khóa chặt Intent:</strong> Đáp ứng chính xác câu hỏi người dùng trước khi mở rộng chuyên sâu.</li>
    <li><strong>Cấu trúc E-E-A-T:</strong> Bổ sung số liệu thực nghiệm và dẫn chứng để vượt qua các đợt Core Update.</li>
    <li><strong>Tối ưu trải nghiệm:</strong> Ứng dụng các khối giao diện chuẩn để tăng thời gian lưu trang (Time on Page).</li>
  </ul>
</div>
```

---

### 03. Hệ Thống Tiêu Đề Phong Cách (Heading Styles)
* **Công dụng**: Làm nổi bật các section lớn H2/H3.
* **HTML Snippets**:

```html
<!-- Kiểu 1: Badge Hồng Nổi Bật -->
<h2 class="heading-badge"><span>PHẦN 1</span> Bản Chất Của Thuật Toán Tìm Kiếm</h2>

<!-- Kiểu 2: Số Thứ Tự Tròn -->
<h2 class="heading-number"><span class="num-circle">01</span> Nghiên Cứu Từ Khóa & Phân Tích SERP</h2>

<!-- Kiểu 3: Tiêu Đề Nổi Bật -->
<h2 class="heading-underline">Quy Chuẩn Xây Dựng Topic Cluster Chuyên Sâu</h2>

<!-- Kiểu 4: Tiêu Đề Phụ Nổi Bật -->
<h3 class="heading-bar">Các lưu ý cốt lõi khi tối ưu thẻ Meta Title</h3>
```

---

### 04. Hộp Ghi Chú & Cảnh Báo (Callout Boxes)
* **Công dụng**: Nhấn mạnh thông tin quan trọng (Tối đa 2–4 hộp/bài).
* **HTML Snippets**:

```html
<!-- Mẹo thực chiến (Tip Box - Xanh lá) -->
<div class="callout callout-tip">
  <div class="callout-title">MẸO THỰC CHIẾN</div>
  <p>Hãy đặt từ khóa chính trong 100 từ đầu tiên để giúp Google bot định danh chủ đề nhanh nhất.</p>
</div>

<!-- Cảnh báo quan trọng (Warning Box - Cam/Đỏ) -->
<div class="callout callout-warning">
  <div class="callout-title">LƯU Ý QUAN TRỌNG</div>
  <p>Tránh nhồi nhét từ khóa (Keyword Stuffing). Mật độ từ khóa lý tưởng nên giữ trong khoảng 1.0% – 2.0%.</p>
</div>

<!-- Thông tin hữu ích (Info Box - Hồng/Xanh dương) -->
<div class="callout callout-info">
  <div class="callout-title">THÔNG TIN BỔ TRỢ</div>
  <p>Dữ liệu được cập nhật theo tài liệu hướng dẫn mới nhất từ Google Search Central.</p>
</div>

<!-- Khối nền tối chuyên sâu (Dark Callout) -->
<div class="callout callout-dark">
  <div class="callout-title">ĐIỂM TỰA CHIẾN LƯỢC</div>
  <p>Nội dung chất lượng kết hợp phân phối Internal Link 2 chiều là chìa khóa duy trì Top 1 bền vững.</p>
</div>
```

---

### 05. Bảng Ưu & Nhược Điểm (Pros & Cons)
* **Công dụng**: So sánh giải pháp, công cụ hoặc phương pháp tiếp cận.
* **HTML Snippet**:
```html
<div class="pros-cons-container">
  <div class="pros-box">
    <div class="box-title">ƯU ĐIỂM</div>
    <ul>
      <li>Tự động hóa 100% quy trình sản xuất nội dung</li>
      <li>Đảm bảo đồng nhất cấu trúc On-page và Schema</li>
      <li>Tiết kiệm 80% thời gian nghiên cứu và lập dàn ý</li>
    </ul>
  </div>
  <div class="cons-box">
    <div class="box-title">HẠN CHẾ</div>
    <ul>
      <li>Vẫn cần chuyên gia rà soát số liệu thực tế (Fact-check)</li>
      <li>Cần thiết lập ban đầu cho API và Token phân quyền</li>
    </ul>
  </div>
</div>
```

---

### 06. Bảng Dữ Liệu Chuẩn UI (Responsive Table)
* **Công dụng**: So sánh thông số, tính năng, số liệu đo lường.
* **HTML Snippet**:
```html
<div class="table-responsive">
  <table class="content-table">
    <thead>
      <tr>
        <th>Tiêu chí so sánh</th>
        <th>Phương pháp thủ công</th>
        <th>Tự động hóa với AI Agent</th>
        <th>Trạng thái</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Thời gian viết bài</strong></td>
        <td>4 – 6 giờ/bài</td>
        <td>15 – 30 phút/bài</td>
        <td><span class="status-badge status-success">Vượt trội</span></td>
      </tr>
      <tr>
        <td><strong>Độ phủ Topic Cluster</strong></td>
        <td>Chậm, dễ đứt gãy</td>
        <td>Quy hoạch chuẩn 100%</td>
        <td><span class="status-badge status-primary">Tối ưu</span></td>
      </tr>
      <tr>
        <td><strong>Chi phí vận hành</strong></td>
        <td>Cao</td>
        <td>Tiết kiệm 75%</td>
        <td><span class="status-badge status-success">Hiệu quả</span></td>
      </tr>
    </tbody>
  </table>
</div>
```

---

### 07. Danh Sách & Quy Trình Từng Bước (Lists & Steps)
* **HTML Snippets**:

```html
<!-- Quy trình từng bước (Step Cards) -->
<div class="step-cards">
  <div class="step-card">
    <div class="step-num">BƯỚC 01</div>
    <div class="step-title">Nghiên cứu SERP & Khóa Intent</div>
    <p>Phân tích Top 10 đối thủ để xác định dàn ý và độ dài mục tiêu.</p>
  </div>
  <div class="step-card">
    <div class="step-num">BƯỚC 02</div>
    <div class="step-title">Viết bài chuẩn E-E-A-T</div>
    <p>Áp dụng mô hình PAS/AIDA với số liệu thực tế và UI Elements.</p>
  </div>
  <div class="step-card">
    <div class="step-num">BƯỚC 03</div>
    <div class="step-title">Phân phối Link & Push Index</div>
    <p>Chèn Internal Link 2 chiều và kích hoạt IndexNow tự động.</p>
  </div>
</div>

<!-- Danh sách Checklist có icon đánh dấu -->
<div class="checklist-grid">
  <div class="check-item"><span class="check-icon" aria-hidden="true"></span> Tiêu đề H1 chứa từ khóa chính</div>
  <div class="check-item"><span class="check-icon" aria-hidden="true"></span> Có thẻ Key Takeaways sau mở bài</div>
  <div class="check-item"><span class="check-icon" aria-hidden="true"></span> Tối thiểu 2 liên kết nội bộ tự nhiên</div>
  <div class="check-item"><span class="check-icon" aria-hidden="true"></span> Khối FAQ có thẻ Schema tương ứng</div>
</div>
```

---

### 08. Lộ Trình Thời Gian Dọc (Vertical Timeline)
* **Công dụng**: Trình bày lộ trình triển khai theo giai đoạn/tháng.
* **HTML Snippet**:
```html
<div class="vertical-timeline">
  <div class="timeline-item">
    <div class="timeline-dot"></div>
    <div class="timeline-content">
      <div class="timeline-time">Giai đoạn 1: Tuần 1 - 4</div>
      <div class="timeline-title">Xây Dựng Nền Móng & Entity</div>
      <p>Xuất bản bài viết Pillar chính và hoàn thiện cấu trúc Silo website.</p>
    </div>
  </div>
  <div class="timeline-item is-active">
    <div class="timeline-dot"></div>
    <div class="timeline-content">
      <div class="timeline-time">Giai đoạn 2: Tuần 5 - 8 (Hiện tại)</div>
      <div class="timeline-title">Phát Triển Cụm Bài Viết Vệ Tinh</div>
      <p>Mở rộng 15-20 bài Cluster bao phủ toàn bộ Long-tail keywords.</p>
    </div>
  </div>
</div>
```

---

### 09. Trích Dẫn & Phát Biểu (Blockquotes)
* **HTML Snippet**:
```html
<div class="quote-card">
  <div class="quote-text">"Google không phạt nội dung do AI tạo ra, Google chỉ phạt nội dung chất lượng thấp không mang lại giá trị thực tế cho người đọc."</div>
  <div class="quote-author">— <strong>Google Search Central Guidelines</strong></div>
</div>
```

---

### 10. Thống Kê & Chỉ Số Nổi Bật (Metrics Grid)
* **Công dụng**: Trình bày kết quả đo lường, case study.
* **HTML Snippet**:
```html
<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-value">+300%</div>
    <div class="metric-label">Tăng trưởng Organic Traffic</div>
  </div>
  <div class="metric-card">
    <div class="metric-value">Top 1-3</div>
    <div class="metric-label">85% Từ khóa mục tiêu</div>
  </div>
  <div class="metric-card">
    <div class="metric-value">4.2x</div>
    <div class="metric-label">Tỷ lệ chuyển đổi Form Lead</div>
  </div>
</div>
```

---

### 11. Câu Hỏi FAQ Đóng Mở (FAQ Accordion)
* **Công dụng**: Tối ưu hiển thị câu hỏi thường gặp và tự động trích xuất **Schema FAQPage**.
* **HTML Snippet**:
```html
<div class="faq-accordion">
  <details class="faq-item">
    <summary class="faq-question">Bài viết AI có được Google lập chỉ mục nhanh không?</summary>
    <div class="faq-answer">
      <p>Có. Khi bài viết đáp ứng đúng Search Intent, có cấu trúc E-E-A-T rõ ràng và được gửi qua giao thức IndexNow, thời gian lập chỉ mục thường chỉ từ 24–48 giờ.</p>
    </div>
  </details>
  <details class="faq-item">
    <summary class="faq-question">Cần bao nhiêu Internal Link trong một bài viết?</summary>
    <div class="faq-answer">
      <p>Khuyến nghị nên có từ 2–5 liên kết nội bộ tự nhiên trỏ đến các bài viết cùng Topic Cluster để tối ưu luồng chuyển giao sức mạnh (Link Juice).</p>
    </div>
  </details>
</div>
```

---

### 12. Khối Kêu Gọi Hành Động & Tải Tài Liệu (CTA & Download)
* **Vị trí**: Đặt ở cuối bài viết để chốt chuyển đổi.
* **HTML Snippets**:

```html
<!-- Khối CTA Đăng Ký Tư Vấn -->
<div class="cta-box">
  <div class="cta-title">Sẵn Sàng Tự Động Hóa Hệ Thống SEO Của Bạn?</div>
  <p class="cta-desc">Liên hệ ngay với đội ngũ chuyên gia của chúng tôi để nhận bản kế hoạch Topic Cluster độc quyền miễn phí.</p>
  <a href="/lien-he" class="btn btn-primary">Đăng Ký Tư Vấn Ngay</a>
</div>

<!-- Khối Tải Tài Liệu PDF -->
<div class="download-card">
  <div class="download-icon" aria-hidden="true">PDF</div>
  <div class="download-info">
    <div class="download-title">Tải Về: Checklist 10 Tiêu Chuẩn Đánh Giá Bài Viết SEO (PDF)</div>
    <div class="download-meta">Dung lượng: 2.4 MB • Định dạng: PDF • Cập nhật: 2026</div>
  </div>
  <a href="/uploads/checklist-seo-2026.pdf" class="btn btn-download">Tải Xuống</a>
</div>
```

---

### 13. Khung Tác Giả & Thẻ Tag (Author Bio & Tags)
* **HTML Snippet**:
```html
<div class="tag-cloud">
  <span class="tag-title">Chủ đề:</span>
  <a href="/category/seo" class="tag-item">On-page SEO</a>
  <a href="/category/ai" class="tag-item">AI Agent</a>
  <a href="/category/technical-seo" class="tag-item">Schema Markup</a>
</div>
```

> Đầu mỗi phiên gọi GET /api/agent.php?action=guidelines. Dùng data.elements_library làm nguồn hiện hành; kiểm tra version. Ví dụ số liệu, trích dẫn và URL dưới đây chỉ minh họa, phải xác minh trước khi sử dụng.
