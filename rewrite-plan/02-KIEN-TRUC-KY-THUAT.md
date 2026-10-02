# Kiến trúc kỹ thuật

## Mô hình modular monolith

Chọn modular monolith để giữ triển khai đơn giản nhưng tránh controller/model khổng lồ. Mỗi module có domain, application service, persistence và UI riêng; giao tiếp qua service interface hoặc domain event.

Không tách microservice ở giai đoạn đầu. Queue worker là process riêng nhưng dùng chung codebase.

```text
HTTP / Console / Queue
        |
Application use cases
        |
Domain models + policies + events
        |
Repositories / DB / storage / external integrations
```

- Controller chỉ parse request, authorize và gọi use case.
- Validation dùng Form Request/DTO.
- Business rule nằm trong service/domain, không nằm trong Blade hoặc route.
- Model không tự gọi API bên ngoài.
- Side effect như indexing, email, image optimization chạy qua event + queue.

## Module đề xuất

- **Core:** sites, users, roles, settings typed, feature registry, audit, workflow, outbox, idempotency.
- **Content:** pages, posts, categories, tags, sections, navigation, redirects và revisions.
- **Company:** profile, services, team, projects, testimonials, partners, careers và locations.
- **Media:** assets, metadata, focal point, variants, MIME verification và storage abstraction.
- **SEO:** metadata, canonical, sitemap, schema graph, briefs, clusters, internal links và indexing adapter.
- **Analytics:** provider adapters và aggregate cache; không lưu IP raw mặc định.
- **Agent:** principals/tokens, scopes, capabilities, tasks, runs, approvals và audit.

## Cấu trúc source code dự kiến

```text
app/
  Modules/
    Core/
      Domain/
      Application/
      Infrastructure/
      Http/
    Content/
    Company/
    Media/
    Seo/
    Analytics/
    Agent/
  Support/
resources/
  views/
    public/
    admin/
    components/
  css/
    settings/
    generic/
    elements/
    objects/
    components/
    utilities/
  js/
routes/
  web.php
  admin.php
  api.php
database/
  migrations/
  seeders/
tests/
  Unit/
  Feature/
  Integration/
  Architecture/
openapi/
skills/
  operate-business-cms/
```

## Site isolation và đường nâng cấp multi-site

- Bảng nghiệp vụ chính có `site_id` và composite index.
- Query đi qua SiteContext; architecture test cấm query content không scope site.
- Slug unique theo `(site_id, content_type, slug)` thay vì global.
- Storage prefix, cache key và idempotency key bao gồm site.
- Domain mapping nằm ở bảng site domains.

Bản đầu không cần tenant billing. Việc giữ `site_id` nhằm tránh migration đau đớn về sau, không có nghĩa đã là SaaS multi-tenant hoàn chỉnh.

## Tích hợp bên ngoài

Mọi provider dùng adapter interface: analytics, search console, indexing, object storage, image processing và email. Credential lưu trong secret manager hoặc encrypted storage với master key chỉ ở environment.

## Event và queue

Event chính gồm `ContentDrafted`, `ContentSubmittedForReview`, `ContentApproved`, `ContentPublished`, `ContentUnpublished`, `MediaProcessed`, `SiteThemeChanged` và `FeatureEnabled`.

`ContentPublished` kích hoạt qua outbox:

1. Regenerate sitemap phần liên quan.
2. Purge cache.
3. Update search index.
4. Gửi indexing request nếu bật.
5. Ghi analytics annotation/audit.

## Quy ước kỹ thuật

- Mọi mutation có authorization policy, validation và audit phù hợp.
- Không catch exception rồi bỏ qua.
- Không chạy migration trong HTTP request.
- Không xóa file trước khi DB transaction commit.
- Không ghép SQL động từ input.
- Không lưu config text tùy ý nếu có typed schema.
- Không render raw HTML từ author/editor.
