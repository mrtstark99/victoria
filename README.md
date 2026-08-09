# Victoria Universal PHP

Website PHP 8.2+ dùng SQLite, giao diện được tách thành các thành phần nhỏ và không có tệp mã nguồn nào vượt quá 200 dòng.

## Chạy dự án

```bash
php -S localhost:2000 -t public
```

PHP cần bật extension `pdo_sqlite`. Trên Windows có thể chạy `./serve.ps1`; script sẽ tự nạp extension SQLite nếu bản PHP chưa có `php.ini`.

Mở `http://localhost:2000`. Cơ sở dữ liệu và các bảng được tạo tự động trong `database/victoria.sqlite` ở lần chạy đầu tiên.

## Cấu trúc

- `app/Core`: kết nối dữ liệu, xác thực, CSRF và thông báo phiên.
- `config`: cấu hình ứng dụng.
- `database`: lược đồ SQLite và tệp dữ liệu cục bộ.
- `public`: các điểm vào HTTP cùng CSS, JavaScript và hình ảnh công khai.
- `templates`: layout, thành phần trang chủ và màn hình tài khoản.

## Bảo mật

Ứng dụng dùng prepared statements, `password_hash`, session cookie HttpOnly/SameSite, đổi session ID sau đăng nhập, CSRF cho mọi form ghi dữ liệu, CSP/header bảo mật, kiểm tra đầu vào và giới hạn tần suất đăng nhập/form tư vấn.

Khi triển khai, đặt document root vào thư mục `public`, bật HTTPS, để `APP_DEBUG=false` và không phục vụ trực tiếp thư mục `database`.
