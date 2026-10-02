# Bảo mật, vận hành và chất lượng

## Security baseline

- Session cookie Secure, HttpOnly, SameSite; proxy/HTTPS detection rõ.
- CSRF cho browser mutation; logout dùng POST.
- Password policy, login throttle account + IP, optional MFA cho admin.
- CSP, frame-ancestors, nosniff, referrer policy và HSTS ở production.
- HTML sanitize bằng thư viện chuyên dụng; editor không được raw script.
- URL field validate protocol và purpose.
- Upload kiểm tra MIME, decode/re-encode, random name, storage ngoài web root khi có thể.
- SVG bị cấm mặc định hoặc sanitize qua pipeline riêng.
- SSRF protection kiểm tra DNS/IP sau redirect và trước connect; chặn private/reserved IPv4/IPv6.
- Secret chỉ ở environment/secret manager và rotate được.
- Audit log append-only theo quyền ứng dụng.

## Privacy

- Không lưu raw IP cho analytics nếu không cần.
- Nếu cần chống abuse, pseudonymize với rotating salt và retention ngắn.
- Có retention cho audit, agent runs, analytics và deleted content.
- Có chức năng export/delete dữ liệu phù hợp phạm vi pháp lý.

## Test pyramid

- **Unit:** slug, metadata, state transitions, permissions, theme token validation.
- **Feature:** auth, CRUD, revision conflict, approval, scheduling, upload, page builder.
- **Integration:** PostgreSQL migrations, queue, storage, adapters, Agent API.
- **Architecture:** module boundaries và site scoping.
- **E2E/smoke:** tạo site -> page -> approve -> publish -> public 200; Agent draft -> review -> publish.
- **Visual/accessibility:** screenshot regression, automated scan và keyboard QA.

## CI gates

Mỗi pull request chạy:

1. Composer validation và dependency audit.
2. PHP lint, formatter và static analysis.
3. Unit/feature/integration tests.
4. Migration test trên DB trống và upgrade fixture.
5. JS/CSS lint, build và bundle budget.
6. Agent OpenAPI/skill contract tests.
7. Secret scan và dependency scan.

Không deploy nếu test, migration hoặc health check fail.

## Deployment

- Build artifact bất biến.
- Migration là release step và fail deployment nếu lỗi.
- Least-privilege filesystem; không dùng `777`.
- Bật host key verification.
- Liveness và readiness tách nhau.
- Release directory + symlink hoặc blue/green để rollback.
- Queue worker restart có kiểm soát.
- Backup trước migration rủi ro cao.

## Observability

- Structured JSON log với request ID, site ID, principal type và job ID.
- Không log secret, credential hoặc content nhạy cảm không cần thiết.
- Metrics: latency, error rate, DB/queue depth, publish failures, Agent run result.
- Alert cho 5xx, queue backlog, scheduled content trễ và backup fail.
- Audit UI filter theo user/agent/resource/action.

## Backup và recovery

- PostgreSQL backup tự động và point-in-time recovery theo môi trường.
- Object storage versioning/lifecycle.
- Restore drill định kỳ.
- RPO/RTO được chốt ở Part 0.
