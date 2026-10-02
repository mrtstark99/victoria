# Quản lý tiến độ xây dựng bởi AI Agents

> Đây là nguồn sự thật duy nhất về tiến độ của dự án viết lại. Mọi AI agent phải đọc toàn bộ file này trước khi nhận việc và cập nhật file trước khi kết thúc lượt làm việc có thay đổi mã nguồn.

## 1. Mục đích

File này dùng để:

- Xác định phần nào chưa làm, đang làm, bị chặn hoặc đã hoàn thành.
- Tránh nhiều agent sửa cùng một phạm vi hoặc cùng một file.
- Ghi rõ đầu ra, quyết định, migration và kiểm tra đã chạy.
- Cho agent tiếp theo tiếp tục công việc mà không phải đoán trạng thái.
- Không cho phép đánh dấu hoàn thành chỉ vì mã đã được viết; phải có bằng chứng kiểm tra và tiêu chí nghiệm thu.

## 2. Quy tắc bắt buộc cho mọi Agent

### Trước khi bắt đầu

1. Đọc `00-KE-HOACH-TONG-QUAN.md`, part liên quan và toàn bộ file này.
2. Kiểm tra `git status` và không ghi đè thay đổi chưa rõ chủ sở hữu.
3. Chọn một work item có trạng thái `READY`.
4. Kiểm tra dependency của item đã `DONE` chưa.
5. Ghi tên agent, thời điểm, phạm vi file và đổi trạng thái thành `IN_PROGRESS` trước khi sửa code.
6. Nếu công việc thay đổi API, schema, permission hoặc design token, phải ghi quyết định/contract trước khi triển khai.

### Trong khi làm

- Chỉ sửa file nằm trong phạm vi đã đăng ký.
- Nếu cần mở rộng phạm vi, cập nhật mục `Phạm vi đang được giữ` trước.
- Không tự thay đổi quyết định kiến trúc đã khóa. Tạo decision proposal và đánh dấu `BLOCKED` nếu cần người dùng quyết định.
- Không chạy migration/destructive command trên production hoặc dữ liệu thật.
- Thêm test cùng thay đổi; không dồn toàn bộ test về cuối dự án.
- Ghi lại lỗi hoặc technical debt được phát hiện, kể cả khi chưa xử lý trong item hiện tại.

### Trước khi kết thúc

1. Chạy các kiểm tra phù hợp và ghi đúng command + kết quả.
2. Đối chiếu acceptance criteria của work item.
3. Cập nhật file đã thay đổi, migration, API và quyết định liên quan.
4. Đổi trạng thái:
   - `DONE`: tất cả tiêu chí đạt, kiểm tra xanh, không còn việc bắt buộc.
   - `BLOCKED`: không thể tiếp tục nếu thiếu quyết định/quyền/phụ thuộc.
   - `READY`: trả việc lại hàng đợi, chưa có thay đổi dở dang.
   - Giữ `IN_PROGRESS`: chỉ khi agent kế tiếp được handoff rõ và worktree ở trạng thái có thể tiếp tục.
5. Giải phóng phạm vi file đã giữ khi item `DONE` hoặc trả về `READY`.
6. Thêm một dòng vào `Nhật ký hoạt động`.

## 3. Trạng thái hợp lệ

| Trạng thái | Ý nghĩa |
|---|---|
| `NOT_READY` | Chưa đủ dependency hoặc quyết định để bắt đầu |
| `READY` | Có thể nhận làm ngay |
| `IN_PROGRESS` | Đã có agent nhận và đang triển khai |
| `IN_REVIEW` | Đã triển khai, đang chờ review hoặc QA độc lập |
| `BLOCKED` | Có blocker cụ thể cần được giải quyết |
| `DONE` | Đạt toàn bộ Definition of Done và có bằng chứng |
| `DEFERRED` | Chủ động dời khỏi phạm vi hiện tại |

Không dùng phần trăm cảm tính để thay cho trạng thái. Nếu cần phần trăm tổng hợp, tính theo số work item `DONE` có trọng số.

## 4. Quy ước định danh work item

Định dạng:

`P{part}-{số thứ tự}`

Ví dụ:

- `P0-01`: chốt tenancy model.
- `P3-04`: xây dựng button component.
- `P9-06`: triển khai idempotency cho Agent API.

Work item phải đủ nhỏ để hoàn thành và kiểm tra trong một chu kỳ làm việc hợp lý. Nếu item kéo dài hoặc chạm quá nhiều module, tách item con trước khi nhận.

## 5. Bảng tiến độ cấp Part

| Part | Nội dung | Trạng thái | Phụ thuộc | Owner hiện tại | Bằng chứng/ghi chú |
|---|---|---|---|---|---|
| Part 0 | Quyết định và prototype rủi ro | `IN_PROGRESS` | Không | Antigravity | Đã chốt `09-CAU-HOI-CAN-CHOT.md` (DEC-001..003) |
| Part 1 | Repository và CI | `NOT_READY` | Part 0 | Chưa có | Chưa khởi tạo source mới |
| Part 2 | Core site, auth, permission | `NOT_READY` | Part 1 | Chưa có | — |
| Part 3 | Design system và admin shell | `NOT_READY` | Part 0, Part 1 | Chưa có | Có thể song song có kiểm soát với Part 2 |
| Part 4 | Content core và workflow | `NOT_READY` | Part 2 | Chưa có | — |
| Part 5 | Media library | `NOT_READY` | Part 4 | Chưa có | — |
| Part 6 | Page builder và Corporate | `NOT_READY` | Part 3, Part 4, Part 5 | Chưa có | — |
| Part 7 | Blog và News profiles | `NOT_READY` | Part 4, Part 6 | Chưa có | — |
| Part 8 | SEO foundation | `NOT_READY` | Part 4, Part 6 | Chưa có | — |
| Part 9 | Agent API | `NOT_READY` | Part 2, Part 4 | Chưa có | — |
| Part 10 | Agent Skill | `NOT_READY` | Part 9 | Chưa có | Tuân theo `05-AGENT-SKILL.md` |
| Part 11 | Analytics và content operations | `NOT_READY` | Part 4, Part 9 | Chưa có | — |
| Part 12 | Hardening và production launch | `NOT_READY` | Part 1–11 thuộc MVP | Chưa có | — |

## 6. Backlog đang hoạt động

Chỉ đặt ở đây các item của Part hiện tại và Part kế tiếp. Khi một Part hoàn thành, chuyển item đã `DONE` xuống phần lưu trữ hoặc giữ bản tóm tắt để file không phình quá lớn.

| ID | Work item | Trạng thái | Owner | Phạm vi file/module | Phụ thuộc | Acceptance criteria |
|---|---|---|---|---|---|---|
| P0-01 | Chốt single-site hay multi-site trong một admin | `DONE` | Antigravity | `09-CAU-HOI-CAN-CHOT.md` | Không | Single-site MVP, có site_id mở đường multi-site |
| P0-02 | Chốt stack triển khai | `DONE` | Antigravity | `09-CAU-HOI-CAN-CHOT.md` | Không | Laravel 11 + PostgreSQL 16 + Blade/Livewire |
| P0-03 | Chốt profile ra mắt đầu tiên | `DONE` | Antigravity | `09-CAU-HOI-CAN-CHOT.md` | Không | Blog Profile (kế thừa dữ liệu và SEO từ site hiện tại) |
| P0-04 | Chốt đa ngôn ngữ và workflow locale | `DONE` | Antigravity | `09-CAU-HOI-CAN-CHOT.md` | P0-03 | Tiếng Việt cho MVP, schema sẵn sàng cho i18n |
| P0-05 | Prototype SiteContext | `READY` | Chưa có | Prototype source | P0-01, P0-02 | Site resolution có test và không rò dữ liệu |
| P0-06 | Prototype section registry | `READY` | Chưa có | Prototype source | P0-02, P0-03 | Render được section typed, invalid payload bị từ chối |
| P0-07 | Prototype theme token | `READY` | Chưa có | Prototype CSS/theme | P0-02 | Ba preset dùng chung component mà không fork CSS |
| P0-08 | Chốt browser matrix và quality gates | `READY` | Chưa có | ADR quality | P0-02 | Browser, accessibility và CI gates được xác nhận |

## 7. Phạm vi đang được giữ

Agent phải thêm một dòng trước khi sửa. Không nhận phạm vi đang có agent khác giữ trừ khi có handoff rõ ràng.

| Owner/Agent | Work item | File hoặc module đang giữ | Bắt đầu lúc | Hết hạn dự kiến | Ghi chú |
|---|---|---|---|---|---|
| Chưa có | — | — | — | — | — |

Nếu agent không cập nhật trong thời gian dài, agent mới không tự ý xóa lock. Trước tiên phải kiểm tra worktree, nhật ký và trạng thái agent; sau đó ghi rõ lý do tiếp quản.

## 8. Blocker và quyết định đang chờ

| ID | Loại | Mô tả | Ảnh hưởng | Người cần quyết định | Trạng thái |
|---|---|---|---|---|---|
| DEC-001 | Product | Single-site hay multi-site trong một admin | SiteContext, database scope, deployment | Chủ dự án | `RESOLVED` (Single-site MVP + site_id) |
| DEC-002 | Architecture | Chấp thuận Laravel + PostgreSQL + Blade/Livewire | Toàn bộ foundation | Chủ dự án | `RESOLVED` (Chấp thuận toàn bộ) |
| DEC-003 | Product | Profile triển khai đầu tiên | MVP và thứ tự module | Chủ dự án | `RESOLVED` (Blog Profile) |

Khi quyết định được chốt, không xóa dòng. Đổi thành `RESOLVED`, ghi kết luận, ngày và link ADR.

## 9. Mẫu nhận việc

Sao chép block này vào nhật ký khi nhận item:

```text
Work item: P?-??
Agent: <tên/ID>
Thời điểm bắt đầu: YYYY-MM-DD HH:mm TZ
Mục tiêu: <một câu>
Phạm vi file/module: <danh sách>
Dependency đã kiểm tra: <ID/status>
Kiểm tra dự kiến: <commands/tests>
```

## 10. Mẫu handoff

```text
Work item: P?-??
Trạng thái: IN_PROGRESS | IN_REVIEW | BLOCKED
Đã hoàn thành:
- ...

Còn lại:
- ...

Files đã thay đổi:
- ...

Migration/API/contract thay đổi:
- ...

Kiểm tra đã chạy:
- `<command>` -> PASS/FAIL + tóm tắt

Blocker/rủi ro:
- ...

Bước tiếp theo chính xác:
1. ...
```

Không dùng handoff chung chung như “tiếp tục hoàn thiện”. Bước tiếp theo phải đủ cụ thể để agent khác thực hiện ngay.

## 11. Bằng chứng hoàn thành

Một work item chỉ được `DONE` khi có đủ bằng chứng phù hợp:

- Link/file thay đổi.
- Test mới hoặc lý do hợp lệ nếu không cần test.
- Command kiểm tra và kết quả.
- Acceptance criteria đã đối chiếu.
- Migration/rollback note nếu đổi schema.
- OpenAPI/schema/version note nếu đổi contract.
- Screenshot/visual regression nếu đổi UI/CSS.
- Security consideration nếu thêm mutation, upload, external request hoặc permission.

## 12. Nhật ký hoạt động

Nhật ký append-only, dòng mới ở trên cùng. Không ghi secret, token hoặc dữ liệu người dùng.

| Thời điểm | Agent | Work item | Hành động | Kết quả | Kiểm tra/bằng chứng |
|---|---|---|---|---|---|
| 2026-08-21 | Codex `/root` | Planning | Tạo file quản lý tiến độ multi-agent | Hoàn thành cấu trúc điều phối ban đầu; chưa nhận work item triển khai | `rewrite-plan/10-QUAN-LY-TIEN-DO-AI-AGENTS.md` |

## 13. Technical debt và vấn đề phát hiện

| ID | Mức độ | Mô tả | Phát hiện bởi | Work item xử lý | Trạng thái |
|---|---|---|---|---|---|
| Chưa có | — | — | — | — | — |

## 14. Báo cáo tiến độ tổng hợp

Chỉ cập nhật khi trạng thái Part thay đổi.

- Part hoàn thành: `0/13`.
- Work item hoạt động hoàn thành: `0/8`.
- Part đang triển khai: `Không có`.
- Blocker mở: `3`.
- Lần cập nhật gần nhất: `2026-08-21`.

## 15. Quy tắc bảo trì file

- Không thay đổi tên trạng thái hoặc cột bảng tùy ý; agent khác phụ thuộc cấu trúc này.
- Không xóa lịch sử quyết định, blocker đã giải quyết hoặc nhật ký.
- Khi file vượt khoảng 800 dòng, chuyển work item `DONE` cũ sang `progress-archive/YYYY-MM.md` và giữ summary tại đây.
- Không dùng file này để chứa thiết kế chi tiết; liên kết sang tài liệu part tương ứng.
- Nếu tiến độ trong file mâu thuẫn với repository/test, repository và bằng chứng kiểm tra là thực tế; agent phải sửa file tiến độ ngay.
