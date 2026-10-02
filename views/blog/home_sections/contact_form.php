    <?php $victoriaHotline = getSetting('site_phone', '0964 808 886'); $victoriaEmail = getSetting('site_email', 'info.duhocvictoria@gmail.com'); ?>
    <section id="contact" class="home-contact bg-primary relative overflow-hidden">
      <!-- Background decorators -->
      <div class="absolute top-0 right-0 w-96 h-96 bg-slate-500/10 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-slate-500/10 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none"></div>

      <div class="mx-auto max-w-7xl px-5 lg:px-8 py-20 lg:py-28 relative z-10">
        <div class="grid gap-12 lg:grid-cols-[1.1fr_1fr] items-center">
          <div class="pr-0 lg:pr-10">
            <span class="text-primary-300 font-bold tracking-wider uppercase text-xs mb-3 block">Bắt đầu hành trình</span>
            <h2 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-white font-display leading-[1.1]">Đặt lịch tư vấn <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-primary-300">du học Nhật Bản</span></h2>
            <p class="mt-6 text-[17px] text-slate-300 leading-relaxed max-w-lg">
              Chúng tôi sẽ liên hệ trong vòng 24 giờ để đánh giá hồ sơ sơ bộ và đề xuất lộ trình phù hợp với năng lực của bạn.
            </p>
            
            <div class="mt-10 space-y-5">
                <div class="flex items-center gap-4 group">
                    <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-primary-300 group-hover:bg-white/10 transition-colors">
                        <i class="bi bi-telephone-fill text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[12px] text-slate-400 font-semibold uppercase tracking-wider">Hotline</p>
                        <a href="tel:<?php echo preg_replace('/[^+\d]/', '', $victoriaHotline); ?>" class="block text-[15px] font-bold text-white mt-0.5 hover:text-primary-200"><?php echo htmlspecialchars($victoriaHotline, ENT_QUOTES, 'UTF-8'); ?></a>
                    </div>
                </div>
                <div class="flex items-center gap-4 group">
                    <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-primary-300 group-hover:bg-white/10 transition-colors">
                        <i class="bi bi-envelope-fill text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[12px] text-slate-400 font-semibold uppercase tracking-wider">Email</p>
                        <p class="text-[15px] font-bold text-white mt-0.5"><?php echo htmlspecialchars($victoriaEmail, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4 group">
                    <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-primary-300 group-hover:bg-white/10 transition-colors">
                        <i class="bi bi-geo-alt-fill text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[12px] text-slate-400 font-semibold uppercase tracking-wider">Văn phòng</p>
                        <p class="text-[15px] font-bold text-white mt-0.5"><?php echo getSetting('site_address', 'Số 45 ngõ 207 Quang Trung, Phường Thành Đông, TP Hải Phòng, Việt Nam'); ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-10 rounded-3xl bg-gradient-to-br from-white/5 to-transparent border border-white/10 p-6 backdrop-blur-md">
              <h4 class="text-white font-bold mb-2">Lịch tư vấn trực tiếp tháng này:</h4>
              <p class="text-sm text-slate-300 leading-relaxed">
                <span class="inline-block mr-3"><i class="bi bi-check2 text-primary-300 mr-1"></i>Thứ 3 & Thứ 6 (Hà Nội)</span>
                <span class="inline-block mr-3"><i class="bi bi-check2 text-primary-300 mr-1"></i>Thứ 5 (TP.HCM)</span>
                <span class="inline-block"><i class="bi bi-camera-video text-primary-300 mr-1"></i>Thứ 7 (Online qua Zoom)</span>
              </p>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="relative">
            <form class="space-y-5 rounded-[2rem] bg-white p-8 sm:p-10 text-sm text-primary shadow-2xl reveal" method="POST" action="/api/contact.php">
                <?php echo csrfField(); ?>
                <h3 class="text-2xl font-bold text-primary font-display mb-2">Nhận tư vấn cá nhân</h3>
                <p class="text-muted mb-6">Miễn phí • Bảo mật thông tin</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <label class="block">
                    <span class="text-[12px] font-bold text-slate-700">Họ và tên <span class="text-red-500">*</span></span>
                    <input class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all" name="name" placeholder="Nguyễn Văn A" required />
                    </label>
                    <label class="block">
                    <span class="text-[12px] font-bold text-slate-700">Số điện thoại <span class="text-red-500">*</span></span>
                    <input class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all" name="phone" placeholder="Nhập số điện thoại" required />
                    </label>
                </div>
                
                <label class="block">
                <span class="text-[12px] font-bold text-slate-700">Email</span>
                <input class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all" name="email" type="email" placeholder="email@example.com" />
                </label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <label class="block">
                    <span class="text-[12px] font-bold text-slate-700">Kỳ nhập học</span>
                    <select class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all" name="intake_period">
                        <option>Tháng 10 năm 2026</option>
                        <option>Tháng 4 năm 2027</option>
                        <option>Tháng 7 năm 2027</option>
                        <option>Khác / Đang cân nhắc</option>
                    </select>
                    </label>
                    <label class="block">
                    <span class="text-[12px] font-bold text-slate-700">Trình độ tiếng Nhật</span>
                    <select class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all" name="japanese_level">
                        <option>Chưa học</option>
                        <option>N5</option>
                        <option>N4</option>
                        <option>N3</option>
                        <option>N2 trở lên</option>
                    </select>
                    </label>
                </div>
                
                <label class="block">
                <span class="text-[12px] font-bold text-slate-700">Ghi chú thêm</span>
                <textarea class="mt-2 h-28 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all resize-none" name="message" placeholder="Ví dụ: Mục tiêu muốn học IT ở Tokyo..."></textarea>
                </label>
                
                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-primary to-primary-800 px-4 py-4 text-[15px] font-bold text-white transition-transform hover:-translate-y-1 hover:shadow-tinted shadow-medium flex justify-center items-center gap-2">
                    Gửi yêu cầu tư vấn <i class="bi bi-send-fill text-white/80"></i>
                </button>
            </form>
          </div>
        </div>
      </div>
    </section>
