<?php
$page_title = 'Danh mục Hồ sơ du học | Victoria Universal';
include APP_ROOT . '/views/layouts/header.php';
?>

<main class="be-static-page bg-slate-50 min-h-screen">
  <!-- Hero Section -->
  <section class="relative overflow-hidden bg-primary pt-20 pb-14 sm:pt-24 sm:pb-16 lg:pt-28 lg:pb-20">
    <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-white/[.06]"></div>
    <div class="absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-primary-400/10"></div>
    <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
      <p class="text-[11px] font-extrabold uppercase tracking-[.2em] text-primary-300">Documentation</p>
      <h1 class="mt-3 text-4xl font-black tracking-tight text-white font-display sm:text-5xl">Hồ sơ du học cần chuẩn bị</h1>
      <nav class="mt-6 flex items-center gap-2 text-xs font-semibold text-white/65" aria-label="Breadcrumb">
        <a href="/" class="transition hover:text-white">Trang chủ</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-white">Chuẩn bị hồ sơ</span>
      </nav>
    </div>
  </section>

  <!-- Document Lists Section -->
  <section class="py-14 sm:py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      
      <!-- Important Note -->
      <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 sm:p-8 mb-12 shadow-sm reveal flex gap-4 items-start max-w-5xl mx-auto">
         <i class="bi bi-exclamation-triangle-fill text-amber-500 text-2xl mt-1"></i>
         <div>
            <h3 class="text-amber-800 font-bold text-lg mb-2">Lưu ý quan trọng trước khi chuẩn bị</h3>
            <ul class="list-disc list-outside ml-4 text-amber-700/90 text-[15px] space-y-1.5 marker:text-amber-400">
               <li>Tất cả các bản sao công chứng phải được thực hiện trên <strong class="font-bold">giấy A4 một mặt</strong> và không được cắt viền.</li>
               <li>Các bản sao công chứng và giấy tờ xác nhận chỉ có giá trị trong vòng <strong class="font-bold">3 tháng</strong> tính đến thời điểm nộp hồ sơ lên Cục Xuất nhập cảnh.</li>
               <li>Thông tin họ tên, ngày tháng năm sinh trên các loại giấy tờ (CCCD, Giấy khai sinh, Sổ hộ khẩu...) phải hoàn toàn khớp nhau.</li>
            </ul>
         </div>
      </div>

      <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 max-w-6xl mx-auto">
         
         <!-- Hồ sơ Học sinh -->
         <div class="bg-white rounded-[2.5rem] shadow-soft p-8 sm:p-12 border border-slate-100 reveal relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 text-midnight transition-transform duration-700 group-hover:scale-110 pointer-events-none">
               <i class="bi bi-person-badge text-9xl"></i>
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-bold text-midnight font-display mb-8 relative z-10 flex items-center gap-4">
               <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-primary text-white shadow-md">
                  <i class="bi bi-person-fill"></i>
               </span>
               1. Hồ sơ của Học sinh
            </h2>
            
            <div class="space-y-6 relative z-10">
               <!-- Giấy tờ học vấn -->
               <div>
                  <h4 class="text-sage-600 font-bold uppercase tracking-wider text-xs mb-3 border-b border-slate-100 pb-2">Bằng cấp & Học vấn</h4>
                  <ul class="space-y-3">
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao công chứng <strong>Bằng tốt nghiệp THPT</strong> (hoặc Giấy chứng nhận TN tạm thời).</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao công chứng <strong>Học bạ THPT</strong>.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản gốc + Bản sao công chứng <strong>Bằng tốt nghiệp Đại học/Cao đẳng/Trung cấp</strong> (nếu có).</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản gốc + Bản sao công chứng <strong>Bảng điểm Đại học/Cao đẳng/Trung cấp</strong> (nếu có).</span>
                     </li>
                  </ul>
               </div>

               <!-- Giấy tờ cá nhân -->
               <div>
                  <h4 class="text-sage-600 font-bold uppercase tracking-wider text-xs mb-3 border-b border-slate-100 pb-2">Giấy tờ cá nhân</h4>
                  <ul class="space-y-3">
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao công chứng <strong>Căn cước công dân</strong>.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao trích lục <strong>Giấy khai sinh</strong>.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao công chứng <strong>Sổ hộ khẩu</strong> (hoặc Giấy xác nhận thông tin cư trú CT07).</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span><strong>Hộ chiếu</strong> (Bản gốc hoặc Bản sao công chứng tất cả các trang có thông tin). Có thể bổ sung sau.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span><strong>10 Ảnh thẻ 3x4 và 10 Ảnh thẻ 4.5x4.5</strong> (Áo sơ mi trắng có cổ, nền trắng, không đeo kính, chụp trong vòng 3 tháng).</span>
                     </li>
                  </ul>
               </div>

               <!-- Năng lực ngoại ngữ -->
               <div>
                  <h4 class="text-sage-600 font-bold uppercase tracking-wider text-xs mb-3 border-b border-slate-100 pb-2">Năng lực ngoại ngữ</h4>
                  <ul class="space-y-3">
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản gốc + Bản sao <strong>Chứng chỉ tiếng Nhật</strong> (JLPT, NAT-TEST, J-TEST...) nếu đã thi đỗ.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Giấy xác nhận giờ học tiếng Nhật (tối thiểu 150 giờ) tại trung tâm.</span>
                     </li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Hồ sơ Người bảo lãnh -->
         <div class="bg-white rounded-[2.5rem] shadow-soft p-8 sm:p-12 border border-slate-100 reveal relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 text-midnight transition-transform duration-700 group-hover:scale-110 pointer-events-none">
               <i class="bi bi-shield-check text-9xl"></i>
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-bold text-midnight font-display mb-8 relative z-10 flex items-center gap-4">
               <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-sage-500 text-white shadow-md">
                  <i class="bi bi-cash-coin"></i>
               </span>
               2. Hồ sơ Người bảo lãnh
            </h2>
            
            <div class="space-y-6 relative z-10">
               <p class="text-[14px] text-muted italic mb-4">Người bảo lãnh thường là Bố hoặc Mẹ của học sinh. Hồ sơ của người bảo lãnh đóng vai trò quyết định trong việc chứng minh tài chính với Cục XNC.</p>
               
               <!-- Giấy tờ cá nhân -->
               <div>
                  <h4 class="text-sage-600 font-bold uppercase tracking-wider text-xs mb-3 border-b border-slate-100 pb-2">Giấy tờ cá nhân</h4>
                  <ul class="space-y-3">
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao công chứng <strong>Căn cước công dân</strong> của người bảo lãnh.</span>
                     </li>
                  </ul>
               </div>

               <!-- Chứng minh tài sản -->
               <div>
                  <h4 class="text-sage-600 font-bold uppercase tracking-wider text-xs mb-3 border-b border-slate-100 pb-2">Chứng minh tài sản</h4>
                  <ul class="space-y-3">
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản gốc <strong>Giấy xác nhận số dư tài khoản ngân hàng</strong> (Sổ tiết kiệm). Số tiền thường yêu cầu từ 500.000.000 VNĐ đến 600.000.000 VNĐ.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao sổ tiết kiệm (có đóng dấu treo của ngân hàng).</span>
                     </li>
                  </ul>
               </div>

               <!-- Chứng minh thu nhập -->
               <div>
                  <h4 class="text-sage-600 font-bold uppercase tracking-wider text-xs mb-3 border-b border-slate-100 pb-2">Chứng minh thu nhập</h4>
                  <ul class="space-y-3">
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản gốc <strong>Giấy xác nhận công việc và mức lương</strong> (từ 3 năm gần nhất) có mộc đỏ của cơ quan/công ty.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Bản sao công chứng <strong>Hợp đồng lao động</strong> hoặc Quyết định bổ nhiệm.</span>
                     </li>
                     <li class="flex gap-3 text-slate-600 text-[15px]">
                        <i class="bi bi-check-circle-fill text-sage-500 shrink-0 mt-0.5"></i>
                        <span>Giấy tờ nộp thuế thu nhập cá nhân / BHXH (nếu có).</span>
                     </li>
                  </ul>
               </div>
               
               <!-- Hộ kinh doanh -->
               <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 mt-4">
                  <h4 class="text-midnight font-bold text-[13px] mb-2 flex items-center gap-2"><i class="bi bi-shop text-sage-500"></i> Đối với Hộ kinh doanh / Chủ doanh nghiệp:</h4>
                  <ul class="space-y-2">
                     <li class="flex gap-2 text-slate-600 text-[13px]">
                        <i class="bi bi-arrow-right-short text-sage-400 shrink-0 mt-0.5"></i>
                        <span>Bản sao công chứng Giấy phép ĐKKD.</span>
                     </li>
                     <li class="flex gap-2 text-slate-600 text-[13px]">
                        <i class="bi bi-arrow-right-short text-sage-400 shrink-0 mt-0.5"></i>
                        <span>Biên lai nộp thuế môn bài, thuế khoán (1-3 năm gần nhất).</span>
                     </li>
                     <li class="flex gap-2 text-slate-600 text-[13px]">
                        <i class="bi bi-arrow-right-short text-sage-400 shrink-0 mt-0.5"></i>
                        <span>Hình ảnh minh chứng cơ sở kinh doanh.</span>
                     </li>
                  </ul>
               </div>
            </div>
         </div>

      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-20 relative overflow-hidden bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-5 relative z-10 text-center reveal">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-6">
         <i class="bi bi-life-preserver text-3xl"></i>
      </div>
      <h2 class="text-3xl md:text-4xl font-bold text-midnight font-display mb-6">Gặp khó khăn với hồ sơ?</h2>
      <p class="mb-10 text-lg text-muted max-w-2xl mx-auto">Đừng quá lo lắng! Việc chứng minh thu nhập và xử lý hồ sơ là công việc chuyên môn của Victoria Universal. Chúng tôi sẽ hướng dẫn và thay bạn chuẩn bị mọi thứ chuẩn xác nhất.</p>
      <a href="/contact" class="inline-flex items-center justify-center rounded-xl bg-primary text-white px-10 py-4 text-[15px] font-bold tracking-wide hover:bg-midnight transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
        Nhận tư vấn hồ sơ miễn phí <i class="bi bi-arrow-right ml-2"></i>
      </a>
    </div>
  </section>
</main>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
