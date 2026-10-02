# Phạm vi sản phẩm

## Vai trò người dùng

### Super Admin

- Khởi tạo site, bật/tắt module, quản lý theme và tích hợp.
- Quản lý người dùng, role, permission, API token và Agent.
- Xem audit log, health status, queue và cấu hình bảo mật.

### Site Admin

- Quản lý thương hiệu, navigation, redirect và SEO mặc định.
- Phê duyệt/xuất bản nội dung.
- Quản lý cấu trúc trang chủ và trang doanh nghiệp.

### Editor/Reviewer

- Biên tập, kiểm tra SEO, yêu cầu sửa và duyệt nội dung.
- Không được chèn script, custom HTML hay thay đổi integration secret.

### Author

- Tạo và sửa draft do mình phụ trách.
- Submit review; không publish nếu không có permission riêng.

### Analyst

- Chỉ đọc dashboard, performance và content reports.

### AI Agent

- Là principal riêng, không giả danh user.
- Có scope cụ thể, quota, expiry, allowlist và audit trail.
- Mặc định chỉ được tạo draft và submit review.

## Site profile

Profile là preset khi khởi tạo, không phải loại site bất biến.

### Blog profile

- Posts, categories, tags, authors.
- Homepage feed, featured post, popular/recent content.
- Topic cluster, related posts, internal link suggestions.
- Newsletter CTA và author page.

### Corporate profile

- Pages và section-based page builder.
- Company profile, services, team, projects/case studies.
- Partners/clients, testimonials, FAQs, careers, contact forms.
- Blog là module tùy chọn.

### News profile

- Posts với category tree, newsroom desk và nhiều tác giả.
- Breaking/featured news, scheduled publishing, embargo.
- Revision và editorial approval chặt.
- Homepage theo block tin, section/category landing.

### Custom profile

Chọn module thủ công; chỉ mở sau khi ba preset đã ổn định.

## Feature registry

Mỗi module đăng ký:

- `key`, migration và model thuộc module.
- Permission, route public/admin/API và menu admin.
- Loại content và page-builder section.
- Search index mapping.
- Event và Agent capability.
- Dependency, ví dụ `case-studies` phụ thuộc `company` và `media`.

Feature flag chỉ bật giao diện sau khi migration/config thành công. Không dùng flag để che database chưa sẵn sàng.

## Use case trọng yếu

1. Tạo Corporate site, nhập brand, dựng homepage và xuất bản trang dịch vụ.
2. Bật Blog sau sáu tháng mà không đổi URL hoặc theme hiện tại.
3. Agent đọc brand voice, keyword brief và tạo bài draft kèm nguồn.
4. Editor sửa, reviewer duyệt và scheduler xuất bản đúng giờ.
5. Khi xuất bản, hệ thống cập nhật sitemap, purge cache và gửi indexing event.
6. Đổi theme preset hoặc màu thương hiệu mà không sửa markup nội dung.
7. Sao chép cấu hình site để khởi tạo website doanh nghiệp khác.

## Ngoài phạm vi MVP

- Multi-tenant billing/subscription.
- Ecommerce/order/payment và CRM đầy đủ.
- Arbitrary plugin code upload hoặc raw PHP/JS editor.
- Drag-and-drop pixel-perfect page builder.
- Realtime collaborative editing kiểu Google Docs.
