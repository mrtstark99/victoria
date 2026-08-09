/**
 * VICTORIA HOMEPAGE DEMO — JAVASCRIPT FUNCTIONS
 */

document.addEventListener('DOMContentLoaded', () => {
  // ==========================================
  // 1. STICKY HEADER SCROLL EFFECT
  // ==========================================
  const header = document.getElementById('main-header');
  const handleScroll = () => {
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
      document.body.style.overflow = mobileOverlay.classList.contains('active') ? 'hidden' : '';
    };

    menuToggle.addEventListener('click', toggleMenu);

    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        menuToggle.classList.remove('active');
        mobileOverlay.classList.remove('active');
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
});
