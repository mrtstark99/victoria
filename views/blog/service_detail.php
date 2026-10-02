<?php
/**
 * @file views/blog/service_detail.php
 * @description Single service detail presentation view with program overview and consultation intake.
 *
 * Layer:
 * - Presentation / View Page
 *
 * Responsibilities:
 * - Display specific study abroad service overview and detailed curriculum.
 * - Render sidebar linking to other services and advisory contact box.
 *
 * Security:
 * - Entity-encoded outputs preventing XSS.
 *
 * Dependencies:
 * - Master layout header/footer.
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

include APP_ROOT . '/views/layouts/header.php';
?>

<main class="pt-24 bg-slate-50 min-h-screen pb-20">
  <!-- Breadcrumb and Hero Banner -->
  <section class="bg-primary text-white pt-20 pb-12 sm:pt-24 lg:pt-28 lg:pb-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full blur-[80px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-5 lg:px-8 relative z-10">
      <nav class="flex items-center gap-2 text-xs font-semibold text-white/60 mb-4 uppercase tracking-wider" aria-label="Breadcrumb">
        <a href="/" class="hover:text-white transition-colors">Trang chủ</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <a href="/services" class="hover:text-white transition-colors">Dịch vụ</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-white"><?php echo htmlspecialchars($service['title']); ?></span>
      </nav>
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white font-display leading-tight">
        <?php echo htmlspecialchars($service['title']); ?>
      </h1>
      <p class="text-slate-300 text-sm sm:text-base mt-3 max-w-2xl">
        <?php echo htmlspecialchars($service['description']); ?>
      </p>
    </div>
  </section>

  <!-- Service Detail Body -->
  <div class="max-w-7xl mx-auto px-5 lg:px-8 mt-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
      
      <!-- Main Content Area -->
      <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-soft">
          <div class="prose max-w-none text-slate-700 leading-relaxed text-sm sm:text-base">
            <?php if (!empty($service['content'])): ?>
              <?php echo $service['content']; ?>
            <?php else: ?>
              <h2 class="text-xl font-bold text-primary mb-4 font-display">Thông tin chương trình</h2>
              <p class="mb-4">Chương trình <strong><?php echo htmlspecialchars($service['title']); ?></strong> tại Victoria Universal được thiết kế chuyên biệt nhằm đồng hành cùng học viên Việt Nam từ bước định hướng ban đầu, chuẩn bị hồ sơ cho tới khi nhập học và ổn định cuộc sống tại Nhật Bản.</p>
              
              <h3 class="text-lg font-bold text-primary mt-6 mb-3 font-display">Quyền lợi học viên nhận được</h3>
              <ul class="space-y-2 list-disc pl-5 text-slate-600">
                <li>Đánh giá năng lực học tập và điều kiện tài chính để xây dựng lộ trình du học tối ưu nhất.</li>
                <li>Hỗ trợ 100% dịch thuật công chứng và hoàn thiện bộ hồ sơ xin tư cách lưu trú (COE).</li>
                <li>Luyện kỹ năng phỏng vấn cùng chuyên gia người Nhật và giáo viên nhiều năm kinh nghiệm.</li>
                <li>Đồng hành hỗ trợ tìm ký túc xá, đưa đón sân bay và hướng dẫn đăng ký sim, thẻ cư trú, tài khoản ngân hàng bên Nhật.</li>
              </ul>

              <h3 class="text-lg font-bold text-primary mt-6 mb-3 font-display">Điều kiện đăng ký tham gia</h3>
              <p>Học viên cần tốt nghiệp THPT trở lên, có mong muốn học tập và phát triển sự nghiệp tại Nhật Bản. Trình độ tiếng Nhật tối thiểu N5 (nếu chưa có sẽ được đào tạo cấp tốc trước khi nộp hồ sơ).</p>
            <?php endif; ?>
          </div>

          <div class="mt-10 border-t border-slate-100 pt-8">
            <div class="mb-6">
              <span class="text-xs font-bold uppercase tracking-wider text-primary">Lựa chọn dịch vụ</span>
              <h2 class="mt-2 font-display text-2xl font-black text-primary">Các gói hồ sơ của chương trình</h2>
              <p class="mt-2 text-sm text-slate-500">Mỗi gói được thiết kế theo mức độ hỗ trợ và nhu cầu của học viên.</p>
            </div>
            <?php if (empty($service['packages'])): ?>
              <p class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">Các gói hồ sơ của chương trình đang được cập nhật. Vui lòng liên hệ để được tư vấn.</p>
            <?php else: ?>
              <div class="grid gap-5 md:grid-cols-2">
                <?php foreach ($service['packages'] as $package): $featured = !empty($package['featured']); ?>
                  <article class="relative flex flex-col rounded-2xl border <?= $featured ? 'border-primary bg-primary text-white shadow-lg' : 'border-slate-200 bg-white text-slate-800' ?> p-6">
                    <?php if ($featured): ?><span class="absolute -top-3 right-5 rounded-full bg-amber-400 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-slate-900">Khuyên dùng</span><?php endif; ?>
                    <h3 class="font-display text-xl font-extrabold <?= $featured ? 'text-white' : 'text-primary' ?>"><?= htmlspecialchars($package['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="mt-2 min-h-10 text-sm leading-relaxed <?= $featured ? 'text-white/75' : 'text-slate-500' ?>"><?= htmlspecialchars($package['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="mt-5 font-display text-2xl font-black <?= $featured ? 'text-white' : 'text-primary' ?>"><?= (float)$package['price'] > 0 ? formatMoney($package['price']) : 'Liên hệ' ?></p>
                    <ul class="my-5 flex-1 space-y-3 text-sm <?= $featured ? 'text-white/90' : 'text-slate-600' ?>">
                      <?php foreach (($package['features'] ?? []) as $feature): ?>
                        <li class="flex items-start gap-2"><i class="bi bi-check-circle-fill mt-0.5 <?= $featured ? 'text-amber-300' : 'text-primary' ?>"></i><span><?= htmlspecialchars($feature, ENT_QUOTES, 'UTF-8') ?></span></li>
                      <?php endforeach; ?>
                    </ul>
                    <a href="/contact?service=<?= rawurlencode($service['title']) ?>&amp;package=<?= rawurlencode($package['name']) ?>" class="mt-2 inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm font-bold <?= $featured ? 'bg-white text-primary hover:bg-slate-100' : 'bg-primary text-white hover:bg-slate-800' ?> transition">
                      Tư vấn gói này <i class="bi bi-arrow-right ml-2"></i>
                    </a>
                  </article>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Right Sidebar -->
      <div class="space-y-6">
        <!-- Other Services Navigation -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-soft">
          <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 font-display">Chương trình khác</h3>
          <div class="space-y-2">
            <?php foreach ($all_services as $s): 
              $isCurrent = $s['slug'] === $service['slug'];
            ?>
              <a href="/services/<?php echo htmlspecialchars($s['slug']); ?>" class="flex items-center justify-between p-3 rounded-xl text-xs font-semibold transition-colors <?php echo $isCurrent ? 'bg-primary text-white' : 'text-slate-700 hover:bg-slate-50'; ?>">
                <span class="truncate"><?php echo htmlspecialchars($s['title']); ?></span>
                <i class="bi bi-chevron-right text-[10px] ml-2"></i>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Quick Advisory Box -->
        <div class="bg-primary text-white rounded-3xl p-6 shadow-medium text-center relative overflow-hidden">
          <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center mx-auto mb-4 text-xl text-amber-400">
            <i class="bi bi-headset"></i>
          </div>
          <h4 class="text-base font-bold font-display mb-2">Cần tư vấn trực tiếp?</h4>
          <p class="text-xs text-slate-300 mb-6 leading-relaxed">Để lại thông tin hoặc liên hệ hotline để được chuyên viên hướng dẫn hồ sơ hoàn toàn miễn phí.</p>
          <a href="/contact" class="service-contact-link block w-full py-3 bg-white text-primary rounded-xl font-bold text-xs hover:bg-slate-100 transition-colors shadow-sm">
            Liên hệ chuyên viên
          </a>
        </div>
      </div>

    </div>
  </div>
</main>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
