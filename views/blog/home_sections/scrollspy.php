    <!-- Fixed Scroll Spy Navigation on the Right -->
    <div id="scrollspy-container" class="fixed right-8 top-1/2 -translate-y-1/2 z-50 hidden xl:flex flex-col items-end w-[180px] h-[220px] overflow-hidden select-none pointer-events-none">
      <!-- Active Center Indicator -->
      <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-orange-600/10 border border-orange-500 rounded-full flex items-center justify-center pointer-events-none z-10 shadow-sm">
        <div class="w-1.5 h-1.5 bg-orange-600 rounded-full animate-ping absolute"></div>
        <div class="w-1.5 h-1.5 bg-orange-600 rounded-full"></div>
      </div>
      
      <!-- Track -->
      <div id="scrollspy-track" class="flex flex-col items-end gap-6 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] absolute right-6 w-full pointer-events-auto">
        <a href="#hero" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="hero">Trang chủ</a>
        <a href="#programs" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="programs">Chương trình</a>
        <a href="#services" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="services">Quy trình</a>
        <a href="#portal" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="portal">Thủ tục</a>
        <a href="#cost" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="cost">Dự toán</a>
        <a href="#blog" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="blog">Tin tức</a>
        <a href="#zoom-schedule" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="zoom-schedule">Lịch Zoom</a>
        <a href="#contact" class="scrollspy-item text-right font-display text-slate-400 select-none block hover:text-orange-600 transition-all duration-500 whitespace-nowrap cursor-pointer" data-target="contact">Liên hệ</a>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('scrollspy-container');
        const track = document.getElementById('scrollspy-track');
        const spyItems = Array.from(document.querySelectorAll('.scrollspy-item'));
        
        // Find matching section elements in the document
        const sections = spyItems.map(item => document.getElementById(item.dataset.target)).filter(el => el !== null);

        function updateScrollSpy() {
          if (sections.length === 0) return;

          // Find current active section
          let activeSection = sections[0];
          const scrollPos = window.scrollY + (window.innerHeight / 3);

          for (let i = 0; i < sections.length; i++) {
            const sec = sections[i];
            if (sec.offsetTop <= scrollPos) {
              activeSection = sec;
            } else {
              break;
            }
          }

          const activeId = activeSection.id;
          let activeIdx = spyItems.findIndex(item => item.dataset.target === activeId);
          if (activeIdx === -1) activeIdx = 0;

          const activeItem = spyItems[activeIdx];
          if (activeItem) {
            // Calculate translate offset to center active item in container
            const containerHeight = container.clientHeight;
            const itemHeight = activeItem.clientHeight;
            const itemOffsetTop = activeItem.offsetTop;
            const offset = (containerHeight / 2) - itemOffsetTop - (itemHeight / 2);

            track.style.transform = `translateY(${offset}px)`;

            // Update item classes based on distance from active index
            spyItems.forEach((item, idx) => {
              const dist = Math.abs(idx - activeIdx);
              item.className = 'scrollspy-item text-right font-display select-none block transition-all duration-500 whitespace-nowrap cursor-pointer';
              
              if (dist === 0) {
                // Active: Large, orange, no blur
                item.classList.add('text-sm', 'font-black', 'text-orange-600', 'scale-110', 'opacity-100');
                item.style.filter = 'none';
              } else if (dist === 1) {
                // Adjacent: Medium, slate, slight blur
                item.classList.add('text-xs', 'font-bold', 'text-slate-400', 'scale-95', 'opacity-60');
                item.style.filter = 'blur(0.5px)';
              } else {
                // Far: Small, faint, blurred
                item.classList.add('text-[10px]', 'font-semibold', 'text-slate-300/40', 'scale-85', 'opacity-25');
                item.style.filter = 'blur(1.5px)';
              }
            });
          }
        }

        // Smooth scroll on click
        spyItems.forEach(item => {
          item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.dataset.target;
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
              targetEl.scrollIntoView({ behavior: 'smooth' });
            }
          });
        });

        // Event listeners
        window.addEventListener('scroll', updateScrollSpy);
        window.addEventListener('resize', updateScrollSpy);

        // Initial setup
        setTimeout(updateScrollSpy, 150);
      });
    </script>