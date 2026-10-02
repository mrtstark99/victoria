# 07. TỰ ĐỘNG HÓA LẬP CHỈ MỤC & GIÁM SÁT THỨ HẠNG (INDEXING & AUTOMATION)

Tài liệu này hướng dẫn các công cụ tự động hóa chu kỳ lập chỉ mục (Indexing Cycle) và giám sát thứ hạng thời gian thực.

---

## 1. GIAO THỨC INDEXNOW & PING SITEMAP

Khi bài viết được xuất bản (`publish_post`), hệ thống tự động thực hiện chuỗi hành động:
1. Tạo lại file `public/sitemap.xml` với thẻ `<lastmod>` mới nhất.
2. Gửi tín hiệu trực tiếp đến giao thức **IndexNow** (Bing, Yandex, Naver, Seznam).
3. Ping URL sitemap đến Google (`https://www.google.com/ping?sitemap=...`).

### Kích Hoạt Thủ Công Bằng API (`action=ping_index`)

```http
POST {{API_ENDPOINT}}?action=ping_index
Content-Type: application/json
Authorization: Bearer <TOKEN>

{
  "post_id": 15
}
```

*Hoặc gửi nhiều URLs tùy biến:*
```json
{
  "urls": [
    "{{BASE_URL}}/blog/bai-viet-1",
    "{{BASE_URL}}/blog/bai-viet-2"
  ]
}
```

---

## 2. KIỂM TRA TÌNH TRẠNG LẬP CHỈ MỤC (`action=check_index_status`)

```http
GET {{API_ENDPOINT}}?action=check_index_status&post_id=15
Authorization: Bearer <TOKEN>
```


* **Dữ liệu trả về**: `days_since_publish` (số ngày kể từ khi xuất bản), trạng thái hiện tại và khuyến nghị hành động (nếu quá 7 ngày chưa được index $\rightarrow$ đề xuất submit lại).

---

## 3. TỰ ĐỘNG HÓA TÁC VỤ KHI BÀI VIẾT DROP RANK (CRON EVENT MONITOR)

Hệ thống tích hợp tập lệnh cron giám sát thứ hạng định kỳ (`cron/event_monitor.php`):
- **Phát hiện Drop Rank**: Khi một bài viết rớt khỏi Top 15 nhưng vẫn có lượng Impressions cao $\rightarrow$ Tự động kích hoạt sự kiện `rank_dropped`.
- **Hành động tự động**: Hệ thống tự động tạo một nhiệm vụ khẩn cấp (`priority: urgent`) vào Bảng Kế Hoạch với nội dung:
  > *"Tối ưu lại nội dung bài viết bị giảm thứ hạng: /blog/slug-bai-viet (Cần làm mới H2, bổ sung số liệu & FAQ)"*
- AI Agent khi đọc bảng task đầu ca sẽ nhận ngay nhiệm vụ này để xử lý kịp thời.
