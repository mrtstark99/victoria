# Victoria v1

Website PHP 8.2+ cho Victoria Universal. Dự án kết hợp giao diện Victoria với các chức năng CMS, blog, dịch vụ, tư vấn/liên hệ, trang thông tin, quản trị, SEO, analytics và AI Agent của hệ thống hiện tại.

## Chạy cục bộ

Yêu cầu PHP 8.2+ với `pdo_sqlite`, `mbstring` và `curl`.

```powershell
php -S localhost:8080 -t public
```

Để router áp dụng rewrite cho sitemap và mọi route đẹp trên PHP built-in server:

```powershell
php -S localhost:6000 -t public public/router.php
```

Mở `http://localhost:8080`. Cơ sở dữ liệu SQLite được khởi tạo tự động trong `database/blog.db`. Không đưa cơ sở dữ liệu, khóa bí mật hoặc thông tin đăng nhập vào Git.

## Cấu trúc

- `app/Controllers`, `app/Models`, `app/Helpers`: chức năng website, CMS và AI Agent.
- `views/victoria`: giao diện, điều hướng và footer Victoria.
- `views/blog`, `views/admin`, `views/auth`: trang public và giao diện quản trị.
- `public/assets/css/victoria`: màu sắc, typography và component lấy từ giao diện mẫu.
- `database`: schema và migration SQLite.

## Tư vấn

Biểu mẫu trang chủ gửi tới `/api/contact` và tiếp tục dùng kiểm tra CSRF, giới hạn gửi, lưu SQLite và màn hình quản lý liên hệ hiện có.

## Triển khai

Chưa cấu hình máy chủ cho dự án mới. Quy trình triển khai cũ đã được vô hiệu hóa để không ghi nhầm lên máy chủ Bright Education. Cần cấu hình riêng host, document root, bí mật CI và miền của Victoria trước khi triển khai.
