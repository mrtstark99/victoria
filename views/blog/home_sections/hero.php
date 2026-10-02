  
  <style>
    /* Premium Entry Animations */
    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .slide-up-element {
        opacity: 0;
        animation: slideUpFade 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }
    
    .delay-1 { animation-delay: 100ms; }
    .delay-2 { animation-delay: 250ms; }
    .delay-3 { animation-delay: 400ms; }
    .delay-4 { animation-delay: 550ms; }
  </style>

  <section class="home-hero hero-bg-faded relative pt-[140px] pb-28 lg:pb-36 w-full min-h-[70vh] lg:min-h-[85vh] flex items-center overflow-hidden">
    
    <!-- Background SVG curves for premium layered look -->
    <div class="absolute inset-0 z-0 opacity-15 pointer-events-none">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
            <path d="M0,0 C30,40 70,10 100,50 L100,100 L0,100 Z" fill="#ffffff"></path>
            <path d="M0,50 C40,80 60,30 100,60 L100,100 L0,100 Z" fill="#c5d3df"></path>
        </svg>
    </div>

    <div class="relative mx-auto max-w-7xl px-5 lg:px-8 w-full z-10">
      <div class="grid gap-12 lg:gap-8 lg:grid-cols-2 items-center w-full">
        <!-- Left: Content -->
        <div class="relative z-10 self-center">
          <div class="space-y-6 relative z-10 text-left">
            <span class="home-eyebrow font-bold tracking-wider uppercase text-xs mb-3 block slide-up-element delay-1 text-primary-300"><?= htmlspecialchars($heroConfig['eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
            <h1 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold leading-[1.2] tracking-tight text-white font-display slide-up-element delay-2">
              <?= htmlspecialchars($heroConfig['title'], ENT_QUOTES, 'UTF-8') ?> <span class="block mt-2 text-primary-300 text-[1.15em] drop-shadow-sm"><?= htmlspecialchars($heroConfig['highlight'], ENT_QUOTES, 'UTF-8') ?></span>
            </h1>
            <p class="text-base sm:text-lg text-slate-300 max-w-lg leading-relaxed font-medium slide-up-element delay-3">
              <?= htmlspecialchars($heroConfig['description'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <div class="flex flex-wrap items-center gap-4 pt-4 slide-up-element delay-4">
              <a class="home-hero-primary inline-flex items-center justify-center rounded-2xl px-6 py-3.5 text-[15px] font-semibold text-primary transition-all shadow-medium" href="<?= htmlspecialchars($heroConfig['primary_cta_url'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($heroConfig['primary_cta_label'], ENT_QUOTES, 'UTF-8') ?> <i class="bi bi-arrow-right ml-2"></i>
              </a>
              <a class="home-hero-secondary inline-flex items-center justify-center rounded-2xl px-6 py-3.5 text-[15px] font-semibold text-white transition" href="<?= htmlspecialchars($heroConfig['secondary_cta_url'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($heroConfig['secondary_cta_label'], ENT_QUOTES, 'UTF-8') ?> <i class="bi bi-play-circle ml-2 text-primary"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Right: Image & Floating Badges -->
        <div class="relative z-10 mt-12 lg:mt-0 flex justify-center lg:justify-end py-8 slide-up-element delay-2">
          <div class="relative w-fit lg:-left-[40px]">
            <div class="absolute top-1/2 left-1/2 w-[280px] h-[280px] md:w-[420px] md:h-[420px] bg-orange-600/10 blur-3xl z-0 animate-morph-glow"></div>

            <!-- Floating Card 1 -->
            <div class="absolute top-[2%] left-[2%] md:left-[-15%] z-20 bg-white border border-slate-100 rounded-2xl p-2.5 md:p-3 shadow-soft hidden md:flex items-center gap-2 md:gap-3 animate-float-1">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary text-sm md:text-lg font-black shadow-sm">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="text-left">
                    <p class="text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-1">Hồ sơ</p>
                    <p class="text-[11px] md:text-sm font-black text-slate-800 leading-none">Đỗ COE 99.9%</p>
                </div>
            </div>

            <!-- Floating Card 2 -->
            <div class="absolute top-[18%] right-[2%] md:right-[-25%] z-20 bg-orange-600 text-white border border-orange-500/20 rounded-2xl p-2.5 md:p-3 shadow-xl hidden md:flex items-center gap-2 md:gap-3 animate-float-2">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-full flex items-center justify-center text-white text-sm md:text-lg font-black shadow-sm">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="text-left">
                    <p class="text-[8px] md:text-[9px] font-bold text-orange-200 uppercase tracking-wider leading-none mb-1">Học phí</p>
                    <p class="text-[11px] md:text-sm font-black text-white leading-none">Minh bạch 100%</p>
                </div>
            </div>

            <!-- Floating Card 3 -->
            <div class="absolute top-[43%] -left-[5%] md:-left-[32%] z-20 bg-primary text-white border-2 border-primary-800/20 rounded-2xl p-2.5 md:p-3 shadow-xl hidden md:flex items-center gap-2 md:gap-3 animate-float-3">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-full flex items-center justify-center text-white text-sm md:text-lg font-black shadow-sm">
                    <i class="bi bi-building"></i>
                </div>
                <div class="text-left">
                    <p class="text-[8px] md:text-[9px] font-bold text-primary-200 uppercase tracking-wider leading-none mb-1">Đối tác</p>
                    <p class="text-[11px] md:text-sm font-black text-white leading-none">500+ Trường Nhật</p>
                </div>
            </div>

            <!-- Floating Card 4 -->
            <div class="absolute bottom-[6%] left-[2%] md:left-[-15%] z-20 bg-white border border-slate-100 rounded-2xl p-2.5 md:p-3 shadow-soft hidden md:flex items-center gap-2 md:gap-3 animate-float-4">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary text-sm md:text-lg font-black shadow-sm">
                    <i class="bi bi-headset"></i>
                </div>
                <div class="text-left">
                    <p class="text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-1">Hỗ trợ</p>
                    <p class="text-[11px] md:text-sm font-black text-slate-800 leading-none">Tư vấn 24/7</p>
                </div>
            </div>

            <!-- Floating Card 5 -->
            <div class="absolute bottom-[10%] right-[2%] md:right-[-20%] z-20 bg-orange-600 text-white border border-orange-500/20 rounded-2xl p-2.5 md:p-3 shadow-xl hidden md:flex items-center gap-2 md:gap-3 animate-float-5">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-full flex items-center justify-center text-white text-sm md:text-lg font-black">
                    <i class="bi bi-suitcase-lg"></i>
                </div>
                <div class="text-left">
                    <p class="text-[8px] md:text-[9px] font-bold text-orange-200 uppercase tracking-wider leading-none mb-1">Việc làm</p>
                    <p class="text-[11px] md:text-sm font-black text-white leading-none">Hỗ trợ làm thêm</p>
                </div>
            </div>

            <!-- Mascot/Student Image -->
            <img src="<?= htmlspecialchars($heroConfig['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Sinh viên Victoria Universal" class="w-auto object-contain max-h-[320px] md:max-h-[480px] lg:max-h-[520px] relative z-10 transform scale-[1.05] drop-shadow-2xl">
          </div>
        </div>
      </div>
    </div>

    <!-- Wave Bottom Curve -->
    <div class="wave-bottom">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
        </svg>
    </div>
  </section>
