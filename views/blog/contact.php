<?php
/**
 * @file views/blog/contact.php
 * @description Contact and student inquiry page with split-screen branding card and validation form.
 *
 * Layer:
 * - Presentation / View Page
 *
 * Responsibilities:
 * - Render Victoria Universal office and hotline contact cards.
 * - Render student consultation request form with CSRF protection.
 * - Provide client-side AJAX submission with friendly status alerts.
 *
 * Security:
 * - Anti-CSRF token verification included in form.
 *
 * Dependencies:
 * - Master layout header/footer and Bootstrap Icons.
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

$siteAddress = getSetting('site_address', 'Số 45 ngõ 207 Quang Trung, Phường Thành Đông, TP Hải Phòng, Việt Nam');
$sitePhoneVN = getSetting('site_phone', '0964 808 886');
$siteEmail = getSetting('site_email', 'info.duhocvictoria@gmail.com');
$workingHours = getSetting('working_hours', 'Thứ 2 - Thứ 7: 8:00 - 17:30');
?>

<?php $heroTitle = 'Liên hệ Victoria Universal'; $heroEyebrow = 'Contact us'; $heroCurrent = 'Liên hệ'; include APP_ROOT . '/views/layouts/partials/page_hero.php'; ?>
<main class="pb-16 bg-slate-50 min-h-[calc(100vh-100px)] flex items-center">
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 w-full">
    <div class="bg-white rounded-[2.5rem] shadow-hard overflow-hidden flex flex-col lg:flex-row border border-slate-100">
      
      <!-- Left Column: Corporate Contact Information -->
      <div class="lg:w-2/5 bg-primary p-8 sm:p-10 lg:p-12 text-white relative overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-[60px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-sage-500/20 rounded-full blur-[80px] pointer-events-none"></div>
        
        <div class="relative z-10">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 rounded-full text-xs font-bold uppercase tracking-wider text-sage-200 mb-6">
            <i class="bi bi-headset"></i> Hỗ trợ 24/7
          </span>
          <h2 class="text-3xl lg:text-4xl font-bold font-display mb-4 text-white leading-tight">
            Kết nối với <br/>Victoria Universal
          </h2>
          <p class="text-white/80 text-sm sm:text-[15px] leading-relaxed mb-10">
            Hãy để lại thông tin, chuyên viên của chúng tôi sẽ liên hệ với bạn trong vòng 24 giờ để tư vấn lộ trình du học phù hợp nhất.
          </p>
          
          <div class="space-y-6">
            <!-- Hotline -->
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0 border border-white/10 shadow-sm text-sage-200 text-lg">
                <i class="bi bi-telephone-fill"></i>
              </div>
              <div>
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-white/60 mb-0.5">Hotline</h3>
                <a href="tel:<?php echo preg_replace('/[^\d+]/', '', $sitePhoneVN); ?>" class="block text-sm sm:text-base font-semibold text-white hover:text-sage-200 transition-colors">
                  <?php echo htmlspecialchars($sitePhoneVN); ?>
                </a>
              </div>
            </div>
            
            <!-- Email -->
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0 border border-white/10 shadow-sm text-sakura-200 text-lg">
                <i class="bi bi-envelope-fill"></i>
              </div>
              <div>
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-white/60 mb-0.5">Email</h3>
                <a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>" class="text-sm sm:text-base font-semibold text-white hover:text-sakura-200 transition-colors">
                  <?php echo htmlspecialchars($siteEmail); ?>
                </a>
              </div>
            </div>
            
            <!-- Address -->
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0 border border-white/10 shadow-sm text-sage-200 text-lg">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
              <div>
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-white/60 mb-0.5">Văn phòng đại diện</h3>
                <p class="text-xs sm:text-sm font-medium text-white/90 leading-relaxed">
                  <?php echo htmlspecialchars($siteAddress); ?>
                </p>
              </div>
            </div>
            
            <!-- Working Hours -->
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0 border border-white/10 shadow-sm text-sakura-200 text-lg">
                <i class="bi bi-clock-fill"></i>
              </div>
              <div>
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-white/60 mb-0.5">Thời gian làm việc</h3>
                <p class="text-xs sm:text-sm font-medium text-white/90">
                  <?php echo htmlspecialchars($workingHours); ?>
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Bottom Trust Badge -->
        <div class="relative z-10 mt-10 pt-6 border-t border-white/10 flex items-center gap-3 text-xs text-white/70">
          <i class="bi bi-shield-check text-green-400 text-lg"></i>
          <span>Bảo mật 100% dữ liệu hồ sơ theo quy định.</span>
        </div>
      </div>
      
      <!-- Right Column: Student Consultation Form -->
      <div class="lg:w-3/5 p-8 sm:p-10 lg:p-12 bg-white flex flex-col justify-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-primary font-display mb-2">Gửi thông tin tư vấn</h2>
        <p class="text-sm text-muted mb-8">Điền thông tin bên dưới để chuyên viên chuẩn bị lộ trình chi tiết và bảng học phí miễn phí.</p>
        
        <form method="POST" action="/api/contact.php" id="contact-page-form" class="space-y-5">
          <?php echo csrfField(); ?>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-primary uppercase tracking-wider">Họ và tên <span class="text-red-500">*</span></label>
              <input type="text" name="name" required placeholder="Nguyễn Văn A" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none text-sm font-medium text-ink">
            </div>
            
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-primary uppercase tracking-wider">Số điện thoại <span class="text-red-500">*</span></label>
              <input type="tel" name="phone" required placeholder="0981 xxx xxx" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none text-sm font-medium text-ink">
            </div>
          </div>
          
          <div class="space-y-1.5">
            <label class="text-[12px] font-bold text-primary uppercase tracking-wider">Địa chỉ Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" required placeholder="email@example.com" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none text-sm font-medium text-ink">
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-primary uppercase tracking-wider">Kỳ nhập học dự kiến</label>
              <div class="relative">
                <select name="intake_period" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none text-sm font-medium text-ink appearance-none">
                  <option value="Tháng 4">Tháng 4 (Kỳ chính)</option>
                  <option value="Tháng 7">Tháng 7</option>
                  <option value="Tháng 10">Tháng 10 (Kỳ chính)</option>
                  <option value="Tháng 1">Tháng 1</option>
                  <option value="Đang tìm hiểu">Chưa xác định / Đang tìm hiểu</option>
                </select>
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-chevron-down text-xs"></i>
                </div>
              </div>
            </div>
            
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-primary uppercase tracking-wider">Trình độ tiếng Nhật hiện tại</label>
              <div class="relative">
                <select name="japanese_level" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none text-sm font-medium text-ink appearance-none">
                  <option value="Chưa học">Chưa học tiếng Nhật</option>
                  <option value="N5">Sơ cấp (N5)</option>
                  <option value="N4">Cơ bản (N4)</option>
                  <option value="N3">Trung cấp (N3)</option>
                  <option value="N2 trở lên">Cao cấp (N2 - N1)</option>
                </select>
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-chevron-down text-xs"></i>
                </div>
              </div>
            </div>
          </div>
          
          <div class="space-y-1.5">
            <label class="text-[12px] font-bold text-primary uppercase tracking-wider">Nhu cầu / Câu hỏi cụ thể</label>
            <textarea name="message" rows="3" placeholder="Ví dụ: Em muốn tìm hiểu trường Nhật ngữ khu vực Tokyo có học bổng..." class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none text-sm font-medium text-ink resize-none"></textarea>
          </div>
          
          <div id="contact-form-feedback" class="hidden p-4 rounded-xl text-sm font-semibold"></div>

          <button type="submit" id="contact-submit-btn" class="w-full py-4 rounded-2xl bg-primary hover:bg-slate-800 text-white font-bold text-sm tracking-wide shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
            <span>Gửi Yêu Cầu Tư Vấn Ngay</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>
      </div>

    </div>
  </section>
</main>

<script>
  // Contact Form AJAX Handler
  document.getElementById('contact-page-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('contact-submit-btn');
    const feedback = document.getElementById('contact-form-feedback');
    const form = this;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block animate-spin mr-2"><i class="bi bi-arrow-repeat"></i></span> Đang gửi thông tin...';
    feedback.className = 'hidden';

    try {
      const formData = new FormData(form);
      const res = await fetch('/api/contact.php', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();

      if (data.success) {
        feedback.className = 'p-4 rounded-xl text-sm font-semibold bg-green-50 text-green-700 border border-green-200 block';
        feedback.textContent = data.message;
        form.reset();
      } else {
        feedback.className = 'p-4 rounded-xl text-sm font-semibold bg-red-50 text-red-700 border border-red-200 block';
        feedback.textContent = data.message || 'Không thể gửi biểu mẫu. Vui lòng thử lại sau.';
      }
    } catch (err) {
      feedback.className = 'p-4 rounded-xl text-sm font-semibold bg-red-50 text-red-700 border border-red-200 block';
      feedback.textContent = 'Đã có lỗi mạng xảy ra. Vui lòng gọi trực tiếp hotline để được hỗ trợ.';
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<span>Gửi Yêu Cầu Tư Vấn Ngay</span> <i class="bi bi-arrow-right"></i>';
    }
  });
</script>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
