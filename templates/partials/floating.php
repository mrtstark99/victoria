  <!-- ==========================================
       9. FLOATING ACTION PANEL (FAB)
       ========================================== -->
  <div class="floating-action-buttons">
    <!-- Chat Notification Bubble -->
    <div class="fab-notification" id="fab-notif">
      <button class="notif-close flex-center" id="notif-close" aria-label="Close Notification">
        <i class="bi bi-x-lg"></i>
      </button>
      <p style="font-size: 0.8rem; line-height: 1.4; color: var(--ink);">
        Chào bạn! Victoria có thể hỗ trợ giải đáp thắc mắc gì về du học không? Chat ngay nhé!
      </p>
    </div>

    <!-- Hidden Sub-buttons -->
    <div class="fab-list" id="fab-list">
      <a href="https://zalo.me/0964808886" target="_blank" rel="noopener noreferrer" class="fab-btn zalo-btn">
        <span class="zalo-text">Z</span>
        <span class="fab-text">Zalo</span>
      </a>
      <a href="https://www.facebook.com/Tuvanduhocvictoriauniversal" target="_blank" rel="noopener noreferrer" class="fab-btn messenger-btn">
        <i class="bi bi-facebook"></i>
        <span class="fab-text">Facebook</span>
      </a>
    </div>

    <!-- Main Trigger Button -->
    <button class="fab-main-trigger flex-center" id="fab-trigger" aria-label="Quick Actions Menu">
      <i class="bi bi-chat-text-fill open-icon"></i>
      <i class="bi bi-x-lg close-icon"></i>
    </button>
  </div>

