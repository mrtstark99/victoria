<?php
$footerDesc = 'Công ty TNHH Toàn Cầu Victoria (Victoria Universal Company Limited) chuyên tư vấn du học, hỗ trợ giáo dục và tổ chức chương trình trao đổi sinh viên.';
$phone = '0964 808 886';
$email = 'info.duhocvictoria@gmail.com';
$address = 'Số 45 ngõ 207 Quang Trung, TP. Hải Dương';
?>
<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-brand"><div class="footer-brand-intro"><a href="/" class="footer-logo-box"><span class="footer-logo-art" role="img" aria-label="Victoria Logo Footer"><img src="/assets/images/VICTORIA_LOGO.svg" class="logo-img" alt=""></span></a><p class="footer-desc"><?= htmlspecialchars($footerDesc, ENT_QUOTES, 'UTF-8') ?></p></div><div class="footer-socials"><a href="https://www.facebook.com/Tuvanduhocvictoriauniversal" target="_blank" rel="noopener noreferrer" class="social-link flex-center" aria-label="Facebook Victoria"><i class="bi bi-facebook"></i></a><a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer" class="social-link flex-center" aria-label="YouTube"><i class="bi bi-youtube"></i></a><a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer" class="social-link flex-center" aria-label="TikTok"><i class="bi bi-tiktok"></i></a></div></div>
    <div><h3 class="footer-title">Liên kết nhanh</h3><ul class="footer-links"><li><a class="footer-link" href="/">Trang chủ</a></li><li><a class="footer-link" href="/#programs">Chương trình đào tạo</a></li><li><a class="footer-link" href="/#services">Quy trình hồ sơ</a></li><li><a class="footer-link" href="/qa">Câu hỏi thường gặp</a></li></ul></div>
    <div><h3 class="footer-title">Chương trình</h3><ul class="footer-links"><li><a class="footer-link" href="/#programs">Du học tự túc Nhật Bản</a></li><li><a class="footer-link" href="/#programs">Học bổng Điều dưỡng</a></li><li><a class="footer-link" href="/#programs">Học bổng Báo chí</a></li><li><a class="footer-link" href="/#programs">Visa Tokutei đặc định</a></li><li><a class="footer-link" href="/courses">Đào tạo tiếng Nhật</a></li></ul></div>
    <div><h3 class="footer-title">Thông tin liên hệ</h3><div class="footer-contact-item"><span><?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></span></div><div class="footer-contact-item"><span>Điện thoại: <a href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', $phone), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></a></span></div><div class="footer-contact-item"><span>Email: <a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></a></span></div><div class="footer-contact-item"><span>Mã số thuế: 0801226400</span></div><div class="footer-contact-item"><span>Người đại diện pháp luật: Bùi Thị Hằng</span></div><div class="footer-contact-item"><span>Hoạt động từ: 20/11/2017</span></div></div>
  </div>
  <div class="container footer-bottom"><p>© <?= date('Y') ?> Victoria Universal Co., Ltd. Bảo lưu mọi quyền.</p><div class="footer-legal"><a href="/about">Giới thiệu</a><a href="/contact">Liên hệ</a></div></div>
</footer>
<aside class="victoria-chat" id="victoria-chat">
  <div class="victoria-chat__links" id="victoria-chat-links" aria-hidden="true">
    <a class="is-zalo" href="https://zalo.me/0964808886" target="_blank" rel="noopener noreferrer"><span class="zalo-text">Z</span> Chat Zalo</a>
    <a class="is-facebook" href="https://www.facebook.com/Tuvanduhocvictoriauniversal" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i> Facebook</a>
    <a href="tel:0964808886"><i class="bi bi-telephone-fill"></i> 0964 808 886</a>
  </div>
  <button class="victoria-chat__trigger" id="victoria-chat-trigger" type="button" aria-label="Mở chat hỗ trợ" aria-expanded="false" aria-controls="victoria-chat-links">
    <i class="bi bi-chat-dots-fill victoria-chat__open" aria-hidden="true"></i><i class="bi bi-x-lg victoria-chat__close" aria-hidden="true"></i>
  </button>
</aside>
<script src="/assets/js/victoria/app.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('[data-contact-form]').forEach(function(form){
    form.addEventListener('submit',async function(event){
      event.preventDefault();const button=form.querySelector('[type="submit"]');const status=form.querySelector('[data-contact-status]');
      button.disabled=true;status.textContent='Đang gửi yêu cầu…';
      try{const response=await fetch(form.action,{method:'POST',body:new FormData(form),headers:{'Accept':'application/json'}});const result=await response.json();status.textContent=result.message||'Không thể gửi yêu cầu.';status.dataset.state=result.success?'success':'error';if(result.success)form.reset();}
      catch(error){status.textContent='Kết nối bị gián đoạn. Vui lòng thử lại.';status.dataset.state='error';}
      finally{button.disabled=false;}
    });
  });
});
</script>
</body></html>
