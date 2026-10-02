# Lộ trình triển khai theo part

Ước lượng mang tính tương đối cho một developer chính có hỗ trợ AI, chưa tính thời gian chờ feedback nội dung/thiết kế.

Mọi agent tham gia triển khai phải nhận work item và cập nhật trạng thái tại `10-QUAN-LY-TIEN-DO-AI-AGENTS.md`.

## Part 0 — Chốt quyết định và prototype rủi ro

Đầu ra:

- Trả lời `09-CAU-HOI-CAN-CHOT.md`.
- Architecture Decision Records cho stack, tenancy, frontend và storage.
- Prototype site context, page section registry và theme token render.
- Browser matrix, security baseline và definition of done.

Hoàn thành khi stakeholder duyệt các quyết định khó đảo ngược.

## Part 1 — Repository và CI

- Project mới, coding standards, env example và local development.
- PostgreSQL, queue, mail/storage fake.
- CI lint, static analysis, test và build.
- Health endpoint và structured logging cơ bản.

## Part 2 — Core site, auth và permission

- Site/domain/context.
- User, role, permission, session, password reset.
- Typed settings, audit log và feature registry.
- Test chống cross-site data access.

## Part 3 — Design system và admin shell

- Token contract, ba brand preset kiểm thử.
- CSS layers, layout objects và component primitives.
- Admin shell, form, table, modal, toast và status.
- Component gallery và visual regression baseline.

Có thể chạy song song có kiểm soát với Part 2 sau khi token contract được chốt.

## Part 4 — Content core và workflow

- Page/post/category/tag.
- Revision, optimistic concurrency và preview.
- State machine review/approve/schedule/publish.
- Navigation, redirect, publish queue và outbox.

Acceptance: author draft -> reviewer approve -> scheduler publish -> public URL 200.

## Part 5 — Media library

- Secure upload, variants, alt/focal point và usage tracking.
- Local/S3 adapter.
- Không xóa asset revision live đang dùng.
- Image markup có `srcset`, sizes và dimensions.

## Part 6 — Page builder và Corporate profile

- Section registry/version/schema và 10–12 section MVP.
- Company, service, team, project, testimonial và partner entities.
- Corporate homepage preset.
- Application-slot cho module doanh nghiệp tương lai.

Corporate được ưu tiên nếu mục tiêu gần nhất là website doanh nghiệp.

## Part 7 — Blog và News profiles

- Blog listing, author/category/tag page và related content.
- News category tree, featured/breaking và editorial metadata.
- Homepage presets.
- Bật Blog trên Corporate site đã có mà không mất dữ liệu.

## Part 8 — SEO foundation

- Metadata, canonical, robots, XML sitemap và redirects.
- Typed schema graph cho Organization, WebSite, Article, Breadcrumb, Service và FAQ.
- Internal link data/validation và indexing adapter qua queue.
- Không nhận arbitrary schema JSON trong MVP.

## Part 9 — Agent API

- Agent principal/token/scope/rate limit/idempotency.
- Capabilities và context endpoints.
- Draft/update/validate/submit-review APIs.
- Agent run/step/audit, OpenAPI và mock contract tests.

Publish scope chỉ mở sau khi workflow và permission tests ổn định.

## Part 10 — Agent Skill

- Khởi tạo `operate-business-cms` bằng skill tooling chuẩn.
- SKILL.md, references, scripts và schemas theo `05-AGENT-SKILL.md`.
- Structural validation.
- Forward tests Blog, Corporate, News, conflict và insufficient facts.
- Version compatibility với Agent API.

Skill làm sau API contract để không viết tài liệu cho endpoint chưa ổn định.

## Part 11 — Analytics và content operations

- Provider adapters, aggregate/cache và dashboard tối thiểu.
- Content calendar, task assignment và performance annotations.
- Retention/privacy; Agent chỉ đọc aggregate cần thiết.

## Part 12 — Hardening và production launch

- Security review, load test, accessibility audit và restore drill.
- Migration/import dữ liệu hệ thống cũ.
- Redirect map và SEO parity check.
- Runbook, monitoring, alert, rollback và soft launch.

## Thứ tự phụ thuộc

```text
Part 0 -> Part 1 -> Part 2 -> Part 4 -> Part 5 -> Part 6 -> Part 7
                   |          |                 |
                   +-> Part 3 +                 +-> Part 8
                                              Part 9 -> Part 10
                                                        |
                                              Part 11 -> Part 12
```

## Quy chuẩn cho mỗi part

- Scope và out-of-scope.
- Data/API/UI contract trước code.
- Threat considerations.
- Test cases và acceptance scenario.
- Migration/rollback nếu thay dữ liệu.
- Demo bằng dữ liệu thực tế, không chỉ lorem ipsum.
- Debt được chấp nhận phải có owner và hạn xử lý.
