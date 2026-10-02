# Hệ thống Style CSS

## 1. Mục tiêu

CSS phải giải quyết đồng thời bốn nhu cầu:

1. Public site đẹp, nhanh, responsive và SEO-friendly.
2. Thay brand cho doanh nghiệp khác mà không fork stylesheet.
3. Module mới dùng lại cùng ngôn ngữ thiết kế.
4. Admin và public tách theme nhưng dùng chung primitives khi hợp lý.

Không coi CSS là một file `style.css` lớn. CSS là design system có contract, layer, token, component state và visual regression test.

## 2. Kiến trúc layer

Dùng CSS Cascade Layers theo thứ tự cố định:

```css
@layer reset, tokens, base, objects, components, utilities, overrides;
```

### `reset`

- Normalize box sizing, margin và media behavior.
- Không xóa outline focus.
- Form controls kế thừa font nhưng vẫn giữ semantic state.

### `tokens`

- Primitive tokens và semantic tokens.
- Theme, color mode, density và site overrides.

### `base`

- `html`, `body`, heading, paragraph, link, list, table và form defaults.
- Chỉ style element toàn cục; không chứa layout trang cụ thể.

### `objects`

- Layout không mang màu sắc: container, stack, cluster, grid, sidebar, cover.
- Có thể kết hợp với mọi component.

### `components`

- Button, card, navigation, hero, article, form, modal và section.
- Component sở hữu visual style và state của chính nó.

### `utilities`

- Helper đơn nhiệm có kiểm soát: visually-hidden, text-align, display, spacing hiếm dùng.
- Không biến toàn dự án thành utility-first thiếu component contract.

### `overrides`

- Third-party fixes và migration bridge có chú thích/expiry.
- Không dùng làm nơi vá CSS tùy tiện.

## 3. Token model

### Primitive tokens

```css
:root {
  --palette-blue-600: oklch(55% 0.18 250);
  --palette-neutral-950: oklch(18% 0.02 250);
  --space-1: 0.25rem;
  --space-2: 0.5rem;
  --space-3: 0.75rem;
  --space-4: 1rem;
  --space-6: 1.5rem;
  --space-8: 2rem;
  --radius-sm: 0.375rem;
  --radius-md: 0.75rem;
  --radius-lg: 1.25rem;
}
```

Primitive không được dùng trực tiếp quá nhiều trong component. Component ưu tiên semantic token.

### Semantic tokens

```css
:root {
  --color-bg-page: var(--palette-neutral-0);
  --color-bg-surface: var(--palette-neutral-0);
  --color-text-primary: var(--palette-neutral-950);
  --color-text-muted: var(--palette-neutral-600);
  --color-border-default: var(--palette-neutral-200);
  --color-action-primary: var(--brand-primary-600);
  --color-focus-ring: var(--brand-accent-500);
}
```

Semantic token giúp đổi palette mà không sửa component. Phải có token cho background, surface, text, border, action, feedback, overlay và focus.

### Component tokens

Chỉ tạo khi component có nhiều variant:

```css
.c-button {
  --button-bg: var(--color-action-primary);
  --button-fg: var(--color-on-action);
  background: var(--button-bg);
  color: var(--button-fg);
}
```

## 4. Brand/theme contract

Mỗi doanh nghiệp cung cấp một theme manifest typed:

- Logo, favicon, wordmark.
- Brand primary/secondary/accent.
- Neutral tone: cool, warm hoặc true neutral.
- Font heading/body/mono từ allowlist hoặc self-hosted asset.
- Radius scale: square, soft, rounded.
- Shadow intensity.
- Content width và wide width.
- Button style và card style preset.

Backend validate màu, font và numeric range rồi sinh stylesheet token nhỏ theo site. Không cho admin nhập arbitrary CSS trong MVP.

```css
[data-site-theme="acme"] {
  --brand-primary-600: oklch(49% 0.16 255);
  --font-family-heading: "Acme Sans", system-ui, sans-serif;
  --radius-card: 1rem;
}
```

Theme manifest có version để migrate khi token contract thay đổi.

## 5. Typography

- Dùng `rem`, không dùng pixel cứng cho body text.
- Fluid scale với `clamp()` nhưng có min/max rõ ràng.
- Body line-height khoảng 1.55–1.75; article content có thể 1.7–1.85.
- Measure của article khoảng 65–75 ký tự mỗi dòng.
- Heading có hierarchy thật, không chọn heading chỉ vì kích thước.
- Font self-host khi license cho phép; preload tối đa font critical.
- Có fallback metric gần tương đương để giảm CLS.

```css
--font-size-sm: clamp(0.875rem, 0.84rem + 0.1vw, 0.9375rem);
--font-size-body: clamp(1rem, 0.96rem + 0.15vw, 1.0625rem);
--font-size-h1: clamp(2.25rem, 1.5rem + 3vw, 4.5rem);
--line-height-body: 1.65;
--measure-reading: 70ch;
```

## 6. Spacing và layout objects

Spacing dùng scale cố định, không rải `13px`, `27px`, `43px` tùy ý.

Objects cốt lõi:

- `.o-container`: giới hạn chiều rộng và gutter.
- `.o-stack`: vertical rhythm qua `gap`.
- `.o-cluster`: hàng item tự wrap.
- `.o-grid`: responsive grid bằng `minmax()`.
- `.o-sidebar`: content/sidebar tự chuyển một cột.
- `.o-section`: vertical section spacing.
- `.o-reel`: horizontal overflow có kiểm soát.

Ưu tiên container query cho component phụ thuộc vùng chứa; media query dành cho layout toàn viewport.

## 7. Component contract

Naming đề xuất:

- `c-` component: `.c-card`, `.c-button`.
- `o-` object: `.o-grid`.
- `u-` utility: `.u-visually-hidden`.
- `is-`/`has-` state: `.is-loading`, `.has-error`.
- `js-` hook chỉ cho JavaScript, không style.

Mỗi component mô tả:

- Markup semantic chuẩn.
- Variants và sizes hữu hạn.
- Hover, focus-visible, active, disabled, loading và error state.
- Responsive/container behavior.
- Light/dark/high-contrast behavior nếu hỗ trợ.
- Content stress: title dài, thiếu ảnh, số lớn, tiếng Việt dài.
- Accessibility: keyboard, focus, contrast và reduced motion.

## 8. Page-builder section CSS

Mỗi section có wrapper chung:

```html
<section class="c-section c-section--hero" data-theme="brand" data-layout="split">
  <div class="o-container">...</div>
</section>
```

Quy tắc:

- Section không biết vị trí của nó trên trang.
- Khoảng cách giữa section do `.c-section` và page rhythm quản lý.
- Variant dùng data attribute hoặc modifier hữu hạn, không style inline.
- Màu nền dùng semantic theme: `default`, `subtle`, `brand`, `inverse`.
- Section không ghi đè component con bằng selector sâu.
- Ảnh có aspect ratio, sizes/srcset và focal point.

## 9. Responsive strategy

- Mobile-first.
- Breakpoint dựa trên lúc layout bị hỏng, không theo tên thiết bị.
- Baseline kiểm thử: 320, 375, 768, 1024, 1440px và zoom 200%.
- Navigation hoạt động bằng keyboard và có mức cơ bản khi JS không tải.
- Không giấu nội dung quan trọng chỉ vì màn hình nhỏ.
- Tap target tối thiểu khoảng 44x44 CSS pixels.

## 10. Accessibility và motion

- Contrast đạt WCAG AA cho text và control.
- `:focus-visible` luôn rõ.
- Dùng `prefers-reduced-motion` để tắt parallax/animation không cần thiết.
- Animation ưu tiên opacity/transform.
- Error không chỉ biểu diễn bằng màu.
- Dark mode chỉ triển khai khi có use case và được QA đầy đủ.

## 11. CSS cho admin

Admin có token namespace riêng, mật độ cao hơn public nhưng dùng cùng primitive scale.

- Layout admin không phụ thuộc theme public.
- Form field, table, filter, status badge và editor toolbar là component chuẩn.
- Trạng thái workflow có semantic color cố định kèm label/icon.
- Page-builder preview render trong iframe hoặc preview boundary để CSS admin không rò sang public component.

## 12. Hiệu năng và chất lượng

- Critical CSS cho shell/hero chỉ khi đo lường chứng minh cần.
- Không dùng `@import` runtime.
- Purge/minify dựa trên source nhưng safelist class sinh từ registry.
- Giới hạn specificity; không dùng ID selector cho style.
- Stylelint kiểm tra layer, token usage, duplicate selector và forbidden patterns.
- Bundle budget ban đầu: public CSS gzip dưới khoảng 50 KB cho core; module CSS lazy theo trang khi hợp lý.
- Visual regression cho mỗi component, theme preset và viewport chính.

## 13. Browser support

Chốt browser matrix ở Part 0. Mặc định đề xuất hai phiên bản gần nhất của Chrome, Edge, Firefox, Safari và Safari iOS hiện hành. Tính năng mới như container queries phải có graceful fallback hoặc được xác nhận trong matrix.

## 14. Definition of Done cho component CSS

- Có component story/demo với dữ liệu thường và dữ liệu stress.
- Không dùng màu/spacing tùy ý ngoài token nếu không có lý do ghi chú.
- Keyboard và focus state hoạt động.
- Đạt contrast target.
- Qua Stylelint, visual regression và responsive checks.
- Hoạt động với ít nhất ba brand preset.
- Không dựa vào vị trí page hoặc selector cha cụ thể.
