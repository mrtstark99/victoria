# Mô hình nội dung và trang chủ tùy biến

## Content primitives

### Page

Dùng cho trang tĩnh hoặc landing page: giới thiệu, liên hệ, chính sách, dịch vụ tổng quan. Page có title, slug, SEO, layout, sections và workflow.

### Post

Dùng cho blog/news. Có subtype/profile-specific fields nhưng không tạo bảng riêng nếu nghiệp vụ cốt lõi giống nhau.

### Structured business entities

Service, team member, project/case study, testimonial, partner/client, job opening, office/location và FAQ set nên có entity riêng. Không lưu tất cả thành JSON page block vì sẽ khó search, validate, reuse và expose qua API.

## Section-based page builder

Trang chủ và landing page lưu danh sách section instance:

```json
{
  "type": "hero",
  "version": 2,
  "settings": {
    "variant": "split",
    "theme": "brand",
    "container": "wide"
  },
  "content": {
    "eyebrow": "Giải pháp cho doanh nghiệp",
    "heading": "Nội dung tiêu đề",
    "primary_action": {"label": "Liên hệ", "url": "/lien-he"}
  }
}
```

Mỗi section đăng ký JSON schema/DTO, version/migration, Blade component, CSS component, preview, allowed profiles, feature dependencies, SEO/accessibility rules và Agent editing contract.

## Section registry MVP

1. Hero: centered, split, image-background.
2. Logo cloud/partners.
3. Company introduction.
4. Services grid/list.
5. Featured projects/case studies.
6. Statistics/counters.
7. Testimonials.
8. Team preview.
9. Latest posts/news.
10. FAQ accordion.
11. CTA banner.
12. Contact/location block.

Section dùng reference tới structured entity khi phù hợp, không copy dữ liệu sang JSON.

## Homepage theo profile

- **Blog:** `Hero/FeaturedPost -> TopicNavigation -> LatestPosts -> PopularPosts -> NewsletterCTA`.
- **Corporate:** `Hero -> Partners -> CompanyIntro -> Services -> Projects -> Metrics -> Testimonials -> LatestInsights -> CTA`.
- **News:** `BreakingStrip -> LeadStories -> CategoryBlocks -> MostRead -> NewsletterCTA`.

Preset chỉ tạo dữ liệu khởi đầu. Admin được thêm/bớt/sắp xếp section trong giới hạn registry.

## Mở rộng chức năng doanh nghiệp

Page builder có `application-slot` để gắn module nghiệp vụ mà không nhúng code vào nội dung, ví dụ tra cứu hồ sơ, đặt lịch, form báo giá, calculator, portal khách hàng hoặc danh mục sản phẩm.

Section này chỉ lưu `module_key`, `view_variant` và typed config. Module sở hữu route, service, authorization và data. Nhờ vậy website có thể phát triển thành cổng doanh nghiệp trong khi CMS vẫn là backend nội dung.

## Navigation, URL và preview

- Navigation là tree có draft/published state.
- URL không phụ thuộc trực tiếp database ID.
- Đổi slug đề xuất/tạo redirect 301.
- Preview URL dùng signed token và không được index.
- Profile switch không tự đổi URL đang tồn tại.

## Đa ngôn ngữ

MVP có thể chỉ triển khai tiếng Việt, nhưng thiết kế có `default_locale`, `enabled_locales`, translation record và slug/canonical/hreflang theo locale. Cần chốt workflow xuất bản dùng chung hay riêng cho từng locale.
