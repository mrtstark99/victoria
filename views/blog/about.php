<?php
$page_title = 'Về Victoria Universal';
$page_description = 'Thông tin doanh nghiệp và liên hệ Victoria Universal.';
include APP_ROOT . '/views/layouts/header.php';
$companyDetails = [
  ['label' => 'Tên doanh nghiệp', 'value' => 'Công ty TNHH Toàn Cầu Victoria (Victoria Universal Company Limited)'],
  ['label' => 'Địa chỉ', 'value' => 'Số 45 ngõ 207 Quang Trung, TP. Hải Dương'],
  ['label' => 'Điện thoại', 'value' => '0964 808 886', 'href' => 'tel:0964808886'],
  ['label' => 'Email', 'value' => 'info.duhocvictoria@gmail.com', 'href' => 'mailto:info.duhocvictoria@gmail.com'],
  ['label' => 'Mã số thuế', 'value' => '0801226400'],
  ['label' => 'Người đại diện pháp luật', 'value' => 'Bùi Thị Hằng'],
  ['label' => 'Hoạt động từ', 'value' => '20/11/2017'],
];
?>

<main class="be-static-page bg-slate-50 min-h-screen">
  <section class="relative overflow-hidden bg-primary pt-20 pb-14 sm:pt-24 sm:pb-16 lg:pt-28 lg:pb-20">
    <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-white/[.06]"></div>
    <div class="absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-primary-400/10"></div>
    <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
      <p class="text-[11px] font-extrabold uppercase tracking-[.2em] text-white/80">Victoria Universal</p>
      <h1 class="mt-3 text-4xl font-black tracking-tight text-white font-display sm:text-5xl">Về Victoria Universal</h1>
      <nav class="mt-6 flex items-center gap-2 text-xs font-semibold text-white/80" aria-label="Breadcrumb">
        <a href="/" class="transition hover:text-white">Trang chủ</a>
        <i class="bi bi-chevron-right text-[10px] text-white" aria-hidden="true"></i>
        <span class="text-white">Về Victoria</span>
      </nav>
    </div>
  </section>

  <section class="py-14 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-5xl px-5 lg:px-8">
      <div class="mb-8 rounded-2xl bg-white p-6 shadow-[0_16px_45px_rgba(0,102,68,.08)] sm:p-9">
        <h2 class="font-display text-2xl font-bold text-primary sm:text-3xl">Công ty TNHH Toàn Cầu Victoria</h2>
        <p class="mt-4 leading-7 text-slate-600">Victoria Universal chuyên tư vấn du học, hỗ trợ giáo dục và tổ chức chương trình trao đổi sinh viên.</p>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_16px_45px_rgba(0,102,68,.08)]">
        <dl class="divide-y divide-slate-200">
          <?php foreach ($companyDetails as $detail): ?>
            <div class="grid gap-2 px-5 py-5 sm:grid-cols-[minmax(190px,.7fr)_1.3fr] sm:gap-6 sm:px-8">
              <dt class="font-bold text-primary"><?= htmlspecialchars($detail['label'], ENT_QUOTES, 'UTF-8') ?></dt>
              <dd class="text-slate-700"><?php if (!empty($detail['href'])): ?><a class="font-semibold text-primary hover:underline" href="<?= htmlspecialchars($detail['href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($detail['value'], ENT_QUOTES, 'UTF-8') ?></a><?php else: ?><?= htmlspecialchars($detail['value'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </div>
      <div class="mt-8 text-center"><a href="/consultation" class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 font-bold text-white transition hover:bg-primary-700"><i class="bi bi-chat-dots-fill text-white" aria-hidden="true"></i>Đăng ký tư vấn</a></div>
    </div>
  </section>
</main>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
