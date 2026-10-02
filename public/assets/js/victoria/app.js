document.documentElement.classList.add('js');

/**
 * VICTORIA HOMEPAGE DEMO — JAVASCRIPT FUNCTIONS
 */

document.addEventListener('DOMContentLoaded', () => {
  const toasts = document.querySelectorAll('[data-toast]');
  toasts.forEach((toast) => {
    let dismissTimer;
    let startedAt = Date.now();
    let remaining;
    const dismiss = () => {
      if (toast.classList.contains('is-hiding')) return;
      toast.classList.add('is-hiding');
      window.setTimeout(() => toast.remove(), 280);
    };
    const closeButton = toast.querySelector('[data-toast-close]');
    if (closeButton) closeButton.addEventListener('click', dismiss);
    const duration = Number(toast.dataset.toastDuration || 5200);
    remaining = duration;
    dismissTimer = window.setTimeout(dismiss, duration);
    toast.addEventListener('mouseenter', () => {
      window.clearTimeout(dismissTimer);
      remaining -= Date.now() - startedAt;
      toast.classList.add('is-paused');
    });
    toast.addEventListener('mouseleave', () => {
      startedAt = Date.now();
      toast.classList.remove('is-paused');
      dismissTimer = window.setTimeout(dismiss, Math.max(remaining, 0));
    });
  });

  // ==========================================
  // 1. STICKY HEADER SCROLL EFFECT
  // ==========================================
  const header = document.getElementById('main-header');
  const handleScroll = () => {
    if (!header) return;
    if (window.scrollY > 20) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  };
  window.addEventListener('scroll', handleScroll);
  handleScroll(); // Initial check

  // ==========================================
  // 2. MOBILE MENU OVERLAY
  // ==========================================
  const menuToggle = document.getElementById('menu-toggle');
  const mobileOverlay = document.getElementById('mobile-overlay');
  const mobileLinks = document.querySelectorAll('.mobile-nav-link');

  if (menuToggle && mobileOverlay) {
    const toggleMenu = () => {
      menuToggle.classList.toggle('active');
      mobileOverlay.classList.toggle('active');
      menuToggle.setAttribute('aria-expanded', mobileOverlay.classList.contains('active'));
      document.body.style.overflow = mobileOverlay.classList.contains('active') ? 'hidden' : '';
    };

    menuToggle.addEventListener('click', toggleMenu);

    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        menuToggle.classList.remove('active');
        mobileOverlay.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      });
    });
  }

  // ==========================================
  // 3. INTERSECTION OBSERVER FOR REVEAL ANIMATIONS
  // ==========================================
  const revealElements = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          observer.unobserve(entry.target); // Animates once
        }
      });
    }, {
      threshold: 0.15,
      rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => observer.observe(el));
  } else {
    // Fallback if IntersectionObserver is not supported
    revealElements.forEach(el => el.classList.add('show'));
  }

  // ==========================================
  // 4. FAQ ACCORDION COMPONENT
  // ==========================================
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const trigger = item.querySelector('.faq-trigger');
    const content = item.querySelector('.faq-content');

    if (trigger && content) {
      trigger.addEventListener('click', () => {
        const isActive = item.classList.contains('active');

        // Close other items
        faqItems.forEach(otherItem => {
          if (otherItem !== item) {
            otherItem.classList.remove('active');
            otherItem.querySelector('.faq-content').style.maxHeight = null;
          }
        });

        // Toggle current item
        if (isActive) {
          item.classList.remove('active');
          content.style.maxHeight = null;
        } else {
          item.classList.add('active');
          content.style.maxHeight = content.scrollHeight + 'px';
        }
      });
    }
  });



  // ==========================================
  // 6. FLOATING ACTION PANEL
  // ==========================================
  const fabTrigger = document.getElementById('fab-trigger');
  const fabList = document.getElementById('fab-list');
  const notifClose = document.getElementById('notif-close');
  const fabNotif = document.getElementById('fab-notif');

  if (fabTrigger && fabList) {
    fabTrigger.addEventListener('click', () => {
      fabTrigger.classList.toggle('active');
      fabList.classList.toggle('active');
      // Hide notification when clicked
      if (fabNotif) {
        fabNotif.style.display = 'none';
      }
    });
  }

  if (notifClose && fabNotif) {
    notifClose.addEventListener('click', (e) => {
      e.stopPropagation(); // Stop trigger toggle
      fabNotif.style.display = 'none';
    });
  }

  const chat = document.getElementById('victoria-chat');
  const chatTrigger = document.getElementById('victoria-chat-trigger');
  const chatLinks = document.getElementById('victoria-chat-links');
  if (chat && chatTrigger && chatLinks) {
    const closeChat = () => {
      chat.classList.remove('is-open');
      chatTrigger.setAttribute('aria-expanded', 'false');
      chatLinks.setAttribute('aria-hidden', 'true');
    };
    chatTrigger.addEventListener('click', () => {
      const isOpen = chat.classList.toggle('is-open');
      chatTrigger.setAttribute('aria-expanded', String(isOpen));
      chatLinks.setAttribute('aria-hidden', String(!isOpen));
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') closeChat();
    });
    document.addEventListener('click', event => {
      if (!chat.contains(event.target)) closeChat();
    });
  }

  const spyContainer = document.getElementById('scrollspy-container');
  const spyTrack = document.getElementById('scrollspy-track');
  const spyItems = Array.from(document.querySelectorAll('.victoria-scrollspy__item'));
  const spySections = spyItems.map(item => document.getElementById(item.dataset.target)).filter(Boolean);
  if (spyContainer && spyTrack && spyItems.length && spySections.length) {
    const updateScrollSpy = () => {
      const cursor = window.scrollY + window.innerHeight / 3;
      let activeIndex = 0;
      spySections.forEach((section, index) => {
        if (section.getBoundingClientRect().top + window.scrollY <= cursor) activeIndex = index;
      });
      const activeItem = spyItems[activeIndex];
      const offset = spyContainer.clientHeight / 2 - activeItem.offsetTop - activeItem.clientHeight / 2;
      spyTrack.style.transform = `translateY(${offset}px)`;
      spyItems.forEach((item, index) => {
        const distance = Math.abs(index - activeIndex);
        item.classList.toggle('is-active', distance === 0);
        item.classList.toggle('is-near', distance === 1);
        item.classList.toggle('is-far', distance > 1);
      });
    };
    spyItems.forEach(item => item.addEventListener('click', event => {
      const target = document.getElementById(item.dataset.target);
      if (!target) return;
      event.preventDefault();
      target.scrollIntoView({behavior: 'smooth', block: 'start'});
    }));
    window.addEventListener('scroll', updateScrollSpy, {passive: true});
    window.addEventListener('resize', updateScrollSpy);
    updateScrollSpy();
  }
});
