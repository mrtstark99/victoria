<section class="section-padding contact-section" id="contact">
  <div class="container">
    <div class="contact-grid">
      <div class="reveal">
        <span class="home-kicker">HỎI ĐÁP PHỔ BIẾN</span>
        <h2 class="section-title contact-title">Giải đáp thắc mắc thường gặp</h2>
        <p class="text-muted">Những câu hỏi quan trọng giúp bạn hiểu rõ hơn về lộ trình du học.</p>
        <div class="faq-list">
          <div class="faq-item">
            <button class="faq-trigger" type="button" aria-expanded="false">
              <span>Hồ sơ cơ bản đi du học Nhật Bản cần những gì?</span>
              <i class="bi bi-plus faq-icon"></i>
            </button>
            <div class="faq-content">
              <div class="faq-content-inner">Hồ sơ gồm học bạ, bằng cấp, CCCD, giấy khai sinh và ảnh thẻ. Victoria sẽ hướng dẫn dịch thuật, công chứng đầy đủ.</div>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-trigger" type="button" aria-expanded="false">
              <span>Thời gian xin tư cách lưu trú (COE) mất bao lâu?</span>
              <i class="bi bi-plus faq-icon"></i>
            </button>
            <div class="faq-content">
              <div class="faq-content-inner">Thời gian thẩm định thường khoảng 2,5 đến 3 tháng kể từ khi nhà trường nhận đủ hồ sơ.</div>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-trigger" type="button" aria-expanded="false">
              <span>Chưa biết tiếng Nhật có kịp kỳ bay gần nhất không?</span>
              <i class="bi bi-plus faq-icon"></i>
            </button>
            <div class="faq-content">
              <div class="faq-content-inner">Bạn cần năng lực tối thiểu N5. Lộ trình từ số 0 thường cần khoảng 6 tháng và lớp học được khai giảng liên tục.</div>
            </div>
          </div>
        </div>
      </div>
      <div class="reveal" id="lien-he">
        <form class="contact-form" action="/api/contact" method="post" data-contact-form>
          <?= csrfField() ?>
          <h3 class="form-title">Đăng ký nhận lộ trình du học 1-1</h3>
          <div class="form-group">
            <label class="form-label" for="form-name">Họ và tên học viên</label>
            <input name="name" type="text" id="form-name" required maxlength="80" class="form-control" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="grid grid-2 contact-fields">
            <div>
              <label class="form-label" for="form-phone">Số điện thoại</label>
              <input name="phone" type="tel" id="form-phone" required maxlength="20" pattern="[0-9+ .-]{8,20}" class="form-control" placeholder="0987654321">
            </div>
            <div>
              <label class="form-label" for="form-email">Email liên hệ</label>
              <input name="email" type="email" id="form-email" required maxlength="190" class="form-control" placeholder="name@domain.com" value="<?= htmlspecialchars($_SESSION['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="form-program">Chương trình quan tâm</label>
            <select name="subject" id="form-program" class="form-control">
              <option value="tu-tuc">Du học tự túc Nhật Bản</option>
              <option value="hoc-bong">Học bổng Điều dưỡng / Báo chí</option>
              <option value="tokutei">Du học chuyển đổi Tokutei</option>
              <option value="khoa-tieng">Khóa tiếng Nhật giao tiếp/JLPT</option>
            </select>
          </div>
          <div class="grid grid-2 contact-fields">
            <div>
              <label class="form-label" for="form-intake">Kỳ nhập học</label>
              <select name="intake_period" id="form-intake" class="form-control">
                <option>Tháng 4 năm 2027</option><option>Tháng 7 năm 2027</option>
                <option>Tháng 10 năm 2027</option><option>Khác / Đang cân nhắc</option>
              </select>
            </div>
            <div>
              <label class="form-label" for="form-japanese">Trình độ tiếng Nhật</label>
              <select name="japanese_level" id="form-japanese" class="form-control">
                <option>Chưa học</option><option>N5</option><option>N4</option><option>N3</option><option>N2 trở lên</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="form-message">Ghi chú yêu cầu (nếu có)</label>
            <textarea name="message" id="form-message" maxlength="1000" class="form-control" placeholder="Nhập câu hỏi hoặc thông tin cần tư vấn..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary form-submit">Gửi yêu cầu tư vấn ngay</button>
          <p class="contact-form-status" data-contact-status role="status" aria-live="polite"></p>
        </form>
      </div>
    </div>
  </div>
</section>
