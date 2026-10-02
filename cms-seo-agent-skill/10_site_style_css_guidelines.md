# 10. QUY CHUẨN STYLE CSS VÀ GIAO DIỆN WEBSITE

Tài liệu này áp dụng khi Agent được giao sửa mã giao diện/CSS. Các action nội dung CMS không cấp quyền sửa file mã nguồn.

## 1. Nhận diện và token thiết kế

- Màu chủ đạo: navy `#0d243e`; nền nội dung `#f8fafc` hoặc trắng; màu chữ chính `#111827`, chữ phụ `#6b7280`.
- Font body: Inter. Tiêu đề: Quicksand.
- Khung nội dung thông thường tối đa khoảng `80rem` (1280px); gutter khoảng 20px trên mobile, 32px trên desktop.
- Spacing section co giãn theo kích thước màn hình; tránh tự đặt padding riêng tùy tiện giữa các trang cùng nhóm.
- Dùng hero chung `views/layouts/partials/page_hero.php` cho trang CMS phù hợp. Hero tự có khoảng đệm cho header; không thêm `padding-top` vào main làm hero bị đẩy xuống lần nữa.

## 2. Bản đồ template và stylesheet

| Route | View/controller | CSS/layout cần đọc trước khi sửa |
| --- | --- | --- |
| `/about`, `/schools`, `/courses`, `/process`, `/documents`, `/consultation` | `PageController` và `views/blog/{slug}.php`; có thể được thay bằng nội dung `FixedPage` | `public/assets/css/static_pages.css`, `views/layouts/partials/head_meta.php`; từng view có thể dùng Tailwind class riêng |
| `/page/{slug}` | `BlogController::showPage()` và `views/blog/page.php` | `static_pages.css`, partial `page_hero.php`, `head_meta.php` |
| `/services`, `/services/{slug}` | `ServiceController` và các view `views/blog/services.php`, `service_detail.php` | CSS thực tế được nạp qua `page_css` trong `head_meta.php`, cộng stylesheet thành phần liên quan |
| `/cost` | `ServiceController` và `views/blog/cost.php` | CSS thực tế được nạp qua `page_css` trong `head_meta.php` |
| Trang chủ `/` | `BlogController` và `views/blog/home.php` cùng các section partial | `home.css`, `home/*.css`, `components.css` |

Tên trang gần giống nhau không có nghĩa là cùng template hoặc stylesheet. Luôn lần theo route/controller/view và kiểm tra markup đang chạy trước khi chỉnh.

## 3. Quy tắc sửa CSS

1. Chỉ sửa CSS khi nhiệm vụ yêu cầu thay đổi giao diện. API cập nhật nội dung (`update_fixed_page`, `update_page`, `update_service`) không phải API sửa CSS.
2. Không nhúng `<style>`, JavaScript hoặc inline style vào `content_html`. Layout và CSS thuộc view/stylesheet trong repository.
3. Ưu tiên class có namespace theo khu vực, ví dụ `be-static-page`, và stylesheet theo nhóm trang. Không vá một route bằng selector toàn cục `body`, `section`, `h1`, `.container`, hoặc `.grid`.
4. Giữ đúng hệ màu, font, hero, bề rộng nội dung và nhịp khoảng cách hiện có. Tái sử dụng partial/component nếu cấu trúc tương đồng; tránh tạo một bộ token cạnh tranh.
5. Không xóa nội dung, tiêu đề, link, form hoặc chức năng để giải quyết vấn đề trình bày. Không lặp H1 nếu hero đã render tiêu đề.
6. Khi thay đổi cần hỗ trợ mobile: kiểm tra tối thiểu 320px, 375px và desktop; grid phải co hợp lý; form chuyển một cột khi hẹp; ảnh và bảng không tràn khung; decorative blur/transform không tạo thanh cuộn ngang.
7. Tôn trọng `prefers-reduced-motion`. Không dùng scale trên thẻ nếu nó làm lệch grid hoặc tràn khung.
8. Thêm file CSS mới phải nạp đúng route/layout và dùng cache-busting theo quy ước `filemtime` trong `head_meta.php`.
9. Chỉ báo đã kiểm tra trực quan khi thực sự mở/đối chiếu trang ở kích thước liên quan. Nếu chỉ lint hoặc đọc mã, hãy mô tả đúng giới hạn đó.

## 4. Checklist trước khi hoàn tất

- [ ] Đúng route và đúng template; không sửa nhầm `/cost` khi yêu cầu về `/services`.
- [ ] CSS mới không ghi đè toàn site.
- [ ] Hero không rỗng, không bị che và không bị cộng padding top hai lần.
- [ ] Padding nhất quán, nội dung không dính mép, thẻ có chiều rộng hợp lý.
- [ ] Không có horizontal overflow; form/bảng/ảnh hoạt động trên mobile.
- [ ] Stylesheet được nạp và cache version thay đổi.
- [ ] Báo đúng file/route đã đổi và mức độ kiểm chứng.
