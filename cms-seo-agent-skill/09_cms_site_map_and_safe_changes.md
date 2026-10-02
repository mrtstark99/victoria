# 09. BẢN ĐỒ CMS VÀ THAO TÁC AN TOÀN

Tài liệu này là bảng định tuyến bắt buộc trước mọi thay đổi CMS. Xác định đúng mục tiêu theo ý người dùng, không dựa riêng vào từ giống nhau như “giá”, “gói” hoặc “trang”.

## 1. Chọn đúng khu vực website

Khi yêu cầu thay đổi **giao diện/CSS**, không dùng action chỉnh nội dung để thay layout. Xem route, view và stylesheet theo [`10_site_style_css_guidelines.md`](./10_site_style_css_guidelines.md); đây là thay đổi mã giao diện riêng với cập nhật nội dung CMS.

| Người dùng muốn làm gì | Khu vực / dữ liệu | Action đọc | Action ghi | Scope ghi |
| --- | --- | --- | --- | --- |
| Tạo hoặc sửa chương trình du học, dịch vụ, gói hồ sơ, quyền lợi, giá gói | `/services` và `/services/{slug}`; `services.packages` | `services`, `get_service` | `create_service`, `update_service` | `services:write` |
| Công khai/ẩn một chương trình dịch vụ | Trạng thái chương trình ở `/services` | `get_service` | `activate_service`, `deactivate_service` | `services:publish` |
| Xóa chương trình dịch vụ | Bản ghi dịch vụ | `get_service` | `delete_service` | `services:delete` |
| Sửa bảng học phí, phí sinh hoạt, tỷ giá, chi phí dự kiến | `/cost`; cấu hình `CostPage.sections` | `get_cost_page` | `update_cost_page` | `cost:write` |
| Sửa các mức giá và lựa chọn trong công cụ dự toán trang chủ | Calculator trên `/` | `get_home_calculator` | `update_home_calculator` | `calculator:write` |
| Tạo/sửa bài viết, bản nháp | `/blog`, trang bài viết | `posts`, `guidelines` | `create_draft`, `update_post` | `posts:draft` |
| Xuất bản bài viết | Trang bài viết công khai | `posts` | `submit_for_review`, `approve_post`, `publish_post` | `posts:draft` / `posts:publish` theo action |
| Sửa nội dung trang cố định | `/about`, `/schools`, `/courses`, `/process`, `/documents`, `/consultation` | `get_fixed_page` | `update_fixed_page` | `fixed_pages:write` |
| Tạo/sửa trang CMS tùy biến | `/page/{slug}` | `pages`, `get_page` | `create_page`, `update_page`, `publish_page` | `pages:draft`, `pages:publish` |
| Sửa menu header, menu di động hoặc footer | Navigation / Footer toàn site | `get_navigation`, `get_footer` | `update_navigation`, `update_footer` | `navigation:write` |
| Sửa hero, cam kết hoặc section trang chủ | `/` | `get_homepage` | `update_homepage` | `homepage:write` |
| Sửa nhận diện thương hiệu, thông tin liên hệ, logo, social | Cấu hình Brand toàn site | `get_brand` | `update_brand` | `brand:write` |
| Quản lý liên hệ tư vấn | Hồ sơ liên hệ trong admin | `contacts`, `get_contact` | `update_contact`, `delete_contact` | `contacts:write`, `contacts:delete` |
| Quản lý SEO, từ khóa, cụm chủ đề | Bảng kế hoạch SEO | `seo`, `list_clusters` | `create_keyword`, `update_keyword`, `create_cluster`, `update_cluster` | `seo:write` |
| Xem/cập nhật task và lịch làm việc | Task Board | `tasks`, `get_tasks` | `create_task`, `update_task`, `complete_task` | `tasks:read`, `tasks:write` |
| Tạo/sửa chuyên mục blog | `/category/{slug}` | `categories` | `create_category`, `update_category`, `delete_category` | `category:write` |
| Đổi nhận diện, logo, liên hệ hoặc mạng xã hội thương hiệu | Cấu hình Brand toàn site | `get_brand` | `update_brand` | `brand:write` |
| Đặt link hai nút nổi Facebook/Zalo | Nút liên hệ ở góc dưới bên phải toàn site | `get_brand` | `update_brand` với `chat_facebook_url`, `zalo_url` | `brand:write` |
| Sửa bio tác giả hoặc sidebar bài viết | Hồ sơ tác giả / cột bài viết | `get_author_profile`, `get_post_sidebar` | `update_author_profile`, `update_post_sidebar` | `profile:write`, `sidebar:write` |
| Sửa GA4, Search Console, KPI, chi phí SEO | Cấu hình đo lường | `get_analytics_settings` | `update_analytics_settings` | `analytics:settings:write` |
| Tải ảnh cho bài viết | Kho uploads | — | `upload_image` | `media:upload` |

### Phân biệt ba loại “gói/giá”

- **Gói dịch vụ hoặc gói hồ sơ** (ví dụ tên gói, quyền lợi, giá tư vấn): cập nhật chương trình ở `/services`, trong `packages[]` bằng `update_service`.
- **Khoản chi phí du học** (học phí trường, nhà ở, sinh hoạt, tỷ giá): cập nhật bảng `/cost` bằng `update_cost_page`.
- **Giá lựa chọn trong công cụ tính dự toán trang chủ**: cập nhật calculator bằng `update_home_calculator`.

Không dùng action của một khu vực để “làm gần giống” yêu cầu thuộc khu vực khác. Nếu mục tiêu chưa rõ, dừng và hỏi người dùng muốn thay đổi loại dữ liệu nào.

## 2. Bắt buộc xác nhận trước khi ghi

Áp dụng với mọi thay đổi dữ liệu dịch vụ/gói tại `/services` và cấu hình tại `/cost`:

1. Kiểm tra scope bằng `GET ?action=me`; thiếu scope thì báo chính xác quyền cần cấp và dừng.
2. Đọc dữ liệu hiện tại (`get_service` hoặc `get_cost_page`) và xác định record/section cụ thể.
3. Trước khi POST, trình bày: trang đích, record/section, trường cũ và mới, cùng trạng thái công khai/ẩn sau khi sửa.
4. Chờ người dùng xác nhận rõ ràng. Không xem quyền token, task, yêu cầu chưa nêu giá trị/đích, hoặc im lặng là xác nhận.
5. Chỉ sau khi được đồng ý trực tiếp, gửi payload với `user_confirmed: true`. API từ chối nếu cờ thiếu (HTTP 428). Dùng `Idempotency-Key` và `expected_updated_at` khi action hỗ trợ.
6. Báo lại action, ID/slug và URL đã thay đổi. Không báo thành công khi API trả lỗi.

`create_service` luôn tạo dịch vụ ở trạng thái `inactive`. Việc bật/tắt hiển thị hoặc xóa là thao tác riêng; cần xác nhận riêng và scope tương ứng.

### Mẫu hỏi xác nhận

> Tôi sẽ thêm gói **[tên gói]** vào chương trình **[tên chương trình, slug]** tại `/services`, gồm giá **[giá]** và các quyền lợi **[tóm tắt]**. Chương trình hiện **[đang hiển thị/đang ẩn]**; thao tác này **[không đổi trạng thái/đưa chương trình lên công khai]**. Bạn xác nhận cho tôi lưu thay đổi này chứ?

Nếu người dùng chưa xác nhận hoặc muốn thay đổi nội dung, cập nhật đề xuất và hỏi lại. Không gửi POST khi chưa được duyệt.

## 3. Xử lý quyền và lỗi định tuyến

- `403 Missing scope [services:write]`: dừng, báo cần `services:write`. Không thử `cost:write` hoặc ghi vào `/cost`.
- `403 Missing scope [cost:write]`: dừng, báo cần `cost:write`. Không thử `services:write` nếu người dùng đang sửa bảng chi phí.
- `428 Explicit user confirmation is required`: chưa có xác nhận hợp lệ trong luồng; trình bày bản xem trước và hỏi người dùng, sau đó gửi lại cùng action với `user_confirmed: true`.
- `409`: dữ liệu đã bị thay đổi sau lần đọc; đọc lại, dựng đề xuất mới và xác nhận lại trước khi gửi.
- `400` hoặc `422`: sửa payload theo lỗi trả về; nếu sửa làm đổi nội dung đã duyệt, xin xác nhận lại.

## 4. Nguyên tắc tổng quát

- Chỉ gọi action có trong `GET ?action=me` và tài liệu API; token `admin` không thay thế việc phân loại đúng trang.
- Không tự tạo trang mới nếu người dùng yêu cầu sửa một khu vực đã có sẵn.
- Không ghi thẳng vào database hoặc cấu hình bằng phương thức ngoài API được tài liệu hóa.
- Với yêu cầu có nhiều khu vực, lập danh sách từng khu vực và xác nhận từng nhóm thay đổi trước khi ghi.
- Ghi audit/hoàn thành task chỉ sau khi action đã thành công; ghi chính xác URL và các giới hạn còn lại.

### Link nút liên hệ nổi Facebook/Zalo

Đọc các giá trị bằng `get_brand` và cập nhật bằng `update_brand` (scope `brand:write`). Dùng URL đầy đủ `https://...` cho `chat_facebook_url` và `zalo_url`; giá trị rỗng sẽ quay về mặc định. Nếu chưa có link tùy chỉnh, Facebook kế thừa link Facebook site và Zalo lấy số điện thoại Việt Nam. Ví dụ:

```json
{
  "chat_facebook_url": "https://m.me/brighteducation",
  "zalo_url": "https://zalo.me/84971044576"
}
```
