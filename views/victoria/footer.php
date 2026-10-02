<?php
$footerDesc = (string)getSetting('footer_about_text', getSetting('site_footer_desc', 'Công ty TNHH Toàn Cầu Victoria (Victoria Universal Company Limited) chuyên tư vấn du học, hỗ trợ giáo dục và tổ chức chương trình trao đổi sinh viên.'));
$footerCopyright = (string)getSetting('footer_copyright', '© {year} Victoria Universal Co., Ltd. Bảo lưu mọi quyền.');
$footerCopyright = str_replace('{year}', date('Y'), $footerCopyright);
$phone = (string)getSetting('site_phone', '0964 808 886');
$email = (string)getSetting('site_email', 'info.duhocvictoria@gmail.com');
$address = (string)getSetting('site_address', 'Số 45 ngõ 207 Quang Trung, TP. Hải Dương');
$navigation = \Controllers\AdminNavigationController::getNavigationSettings();
$footerCol2 = $navigation['footer_col2'] ?? ['title' => 'Liên kết nhanh', 'links' => []];
$footerCol3 = $navigation['footer_col3'] ?? ['title' => 'Chương trình', 'links' => []];
$bottomLinks = $navigation['bottom_links'] ?? [];
$siteName = (string)getSetting('site_name', 'Victoria Universal');
$logoUrl = (string)getSetting('site_logo_url', '/assets/images/VICTORIA_LOGO.svg');
$taxCode = (string)getSetting('company_tax_code', '0801226400');
$legalRepresentative = (string)getSetting('company_legal_representative', 'Bùi Thị Hằng');
$companySince = (string)getSetting('company_since', '20/11/2017');
$zaloUrl = trim((string)getSetting('zalo_url', ''));
if ($zaloUrl === '') $zaloUrl = 'https://zalo.me/' . preg_replace('/\D/', '', $phone);
$socials = [
  ['label' => 'Facebook', 'url' => getSetting('social_facebook', 'https://www.facebook.com/Tuvanduhocvictoriauniversal'), 'icon' => 'bi-facebook'],
  ['label' => 'YouTube', 'url' => getSetting('social_youtube', 'https://www.youtube.com/'), 'icon' => 'bi-youtube'],
  ['label' => 'TikTok', 'url' => getSetting('social_tiktok', 'https://www.tiktok.com/'), 'icon' => 'bi-tiktok'],
];
$renderFooterLinks = static function (array $links): void {
  foreach ($links as $link) {
    if (!is_array($link)) continue;
    $url = safeNavigationUrl($link['url'] ?? '');
    $label = trim((string)($link['label'] ?? ''));
    if ($url === null || $label === '') continue;
    echo '<li><a class="footer-link" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a></li>';
  }
};
?>
<footer class="footer">
  <div class="container footer-grid">
    <div>
      <div class="footer-brand-intro"><a href="/" class="footer-logo-box"><span class="footer-logo-art" role="img" aria-label="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>"><img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" class="logo-img" alt="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>"></span></a><p class="footer-desc"><?= htmlspecialchars($footerDesc, ENT_QUOTES, 'UTF-8') ?></p></div>
      <div class="footer-socials"><?php foreach ($socials as $social): $socialUrl = safeNavigationUrl($social['url']); if ($socialUrl === null || $socialUrl === '#') continue; ?><a href="<?= htmlspecialchars($socialUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="social-link flex-center" aria-label="<?= htmlspecialchars($social['label'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi <?= htmlspecialchars($social['icon'], ENT_QUOTES, 'UTF-8') ?>"></i></a><?php endforeach; ?></div>
    </div>
    <div><h3 class="footer-title"><?= htmlspecialchars($footerCol2['title'] ?? 'Liên kết nhanh', ENT_QUOTES, 'UTF-8') ?></h3><ul class="footer-links"><?php $renderFooterLinks($footerCol2['links'] ?? []); ?></ul></div>
    <div><h3 class="footer-title"><?= htmlspecialchars($footerCol3['title'] ?? 'Chương trình', ENT_QUOTES, 'UTF-8') ?></h3><ul class="footer-links"><?php $renderFooterLinks($footerCol3['links'] ?? []); ?></ul></div>
    <div>
      <h3 class="footer-title">Thông tin liên hệ</h3>
      <div class="footer-contact-item"><span><?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="footer-contact-item"><span>Điện thoại: <a href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', $phone), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></a></span></div>
      <div class="footer-contact-item"><span>Email: <a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></a></span></div>
      <div class="footer-contact-item"><span>Mã số thuế: <?= htmlspecialchars($taxCode, ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="footer-contact-item"><span>Người đại diện pháp luật: <?= htmlspecialchars($legalRepresentative, ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="footer-contact-item"><span>Hoạt động từ: <?= htmlspecialchars($companySince, ENT_QUOTES, 'UTF-8') ?></span></div>
    </div>
  </div>
  <div class="container footer-bottom"><p><?= htmlspecialchars($footerCopyright, ENT_QUOTES, 'UTF-8') ?></p><div class="footer-legal"><?php foreach ($bottomLinks as $link): if (!is_array($link)) continue; $url = safeNavigationUrl($link['url'] ?? ''); $label = trim((string)($link['label'] ?? '')); if ($url === null || $label === '') continue; ?><a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?></div></div>
</footer>
<?php
$customFooterCode = (string)getSetting('custom_footer_code', '');
if ($customFooterCode !== '') echo $customFooterCode . "\n";
?>
<aside class="victoria-chat" id="victoria-chat">
  <div class="victoria-chat__links" id="victoria-chat-links" aria-hidden="true">
    <a class="is-zalo" href="<?= htmlspecialchars($zaloUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><span class="zalo-text">Z</span> Chat Zalo</a>
    <a class="is-facebook" href="<?= htmlspecialchars((string)getSetting('chat_facebook_url', 'https://www.facebook.com/Tuvanduhocvictoriauniversal'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i> Facebook</a>
    <a href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', $phone), ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></a>
  </div>
  <button class="victoria-chat__trigger" id="victoria-chat-trigger" type="button" aria-label="Mở chat hỗ trợ" aria-expanded="false" aria-controls="victoria-chat-links"><i class="bi bi-chat-dots-fill victoria-chat__open" aria-hidden="true"></i><i class="bi bi-x-lg victoria-chat__close" aria-hidden="true"></i></button>
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
</body>
</html>
