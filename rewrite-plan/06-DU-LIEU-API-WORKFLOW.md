# Dữ liệu, API và workflow

## Bảng/domain chính

### Core

- `sites`, `site_domains`, `site_features`.
- `users`, `roles`, `permissions`, role assignments.
- `settings` với key, type, schema version và encrypted flag.
- `audit_logs`, `outbox_events`, `idempotency_records`.

### Content

- `content_items`: identity chung, site, type, owner, workflow state.
- `content_revisions`: snapshot/version, author/agent, change summary.
- `pages`, `posts`: field riêng theo type.
- `page_sections`: order, type, schema version, validated payload.
- `categories`, `tags`, pivots.
- `navigation_menus`, `navigation_items`, `redirects`.

### Business entities

- `services`, `team_members`, `projects`, `testimonials`, `partners`, `job_openings`, `locations`.

### Media và Agent

- `media_assets`, `media_variants`, `media_usages`.
- `agent_principals`, `agent_tokens`, `agent_runs`, `agent_steps`, `agent_approvals`.

## Workflow state machine

```text
draft -> in_review -> changes_requested -> draft
in_review -> approved -> scheduled -> published
approved -> published
published -> archived
published -> draft (tạo revision mới; bản live giữ đến lần publish sau)
```

- State transition nằm trong service/state machine, không update cột trực tiếp.
- Publish chọn revision được duyệt làm live revision trong transaction.
- Scheduled job dùng cùng use case với manual publish.
- Preview luôn chỉ rõ draft/live revision.
- Delete mặc định là soft delete; purge có quyền riêng và retention.

## Revision model

- Mỗi save có revision tăng đơn điệu trong phạm vi content item.
- `live_revision_id` và `working_revision_id` tách nhau.
- Update yêu cầu `expected_revision_id` để tránh lost update.
- Revision không ghi đè; rollback tạo revision mới từ snapshot cũ.
- Media usage version theo revision để không xóa asset bản live còn dùng.

## API resource chính

- `/api/v1/sites/{site}` và `/capabilities`.
- `/api/v1/sites/{site}/pages`, `/posts`, `/media`.
- `/api/v1/sites/{site}/navigation`, `/seo`.
- `/api/v1/sites/{site}/agent/context`, `/agent/runs`.

Workflow có endpoint rõ:

- `POST /content/{id}/submit-review`.
- `POST /content/{id}/approve`.
- `POST /content/{id}/schedule`.
- `POST /content/{id}/publish`.

Không điều khiển mọi hành động bằng một endpoint `?action=...`.

## API standards

- OpenAPI là source of truth.
- JSON error theo problem-details style.
- Filtering/sorting dùng allowlist.
- ETag/If-Match hoặc revision ID cho concurrency.
- Request size limit và field length rõ.
- Idempotency gồm site, principal, route, payload hash, response và expiry.
- Audit không lưu bearer token hoặc secret field.

## Permission matrix tối thiểu

| Action | Author | Editor | Reviewer | Admin | Agent draft |
|---|---:|---:|---:|---:|---:|
| Tạo draft | Có | Có | Có | Có | Có |
| Sửa draft của mình | Có | Có | Có | Có | Theo scope |
| Sửa mọi draft | Không | Có | Có | Có | Không mặc định |
| Submit review | Có | Có | Có | Có | Có |
| Approve | Không | Không | Có | Có | Không mặc định |
| Publish | Không | Không | Theo policy | Có | Không mặc định |
| Theme/integration | Không | Không | Không | Có | Không |

Policy phải được test, không chỉ ẩn nút trong UI.
