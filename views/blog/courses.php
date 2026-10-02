<?php
$page_title = "Khóa học Tiếng Nhật - Victoria Universal";
include APP_ROOT . '/views/layouts/header.php';
?>

<main class="be-static-page be-courses-page">
  <!-- Hero Section -->
  <section class="relative overflow-hidden bg-primary pt-20 pb-14 sm:pt-24 sm:pb-16 lg:pt-28 lg:pb-20">
    <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-white/[.06]"></div>
    <div class="absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-primary-400/10"></div>
    <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
      <p class="text-[11px] font-extrabold uppercase tracking-[.2em] text-primary-300">Japanese language courses</p>
      <h1 class="mt-3 text-4xl font-black tracking-tight text-white font-display sm:text-5xl">Khóa học tiếng Nhật</h1>
      <nav class="mt-6 flex items-center gap-2 text-xs font-semibold text-white/65" aria-label="Breadcrumb">
        <a href="/" class="transition hover:text-white">Trang chủ</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-white">Khóa học tiếng Nhật</span>
      </nav>
    </div>
  </section>

  <!-- Lợi thế cạnh tranh (Why us?) -->
  <section class="py-24 bg-white relative">
    <div class="container mx-auto px-6 lg:px-12">
      <div class="text-center mb-16 max-w-2xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-black text-midnight font-display mb-4">Tại sao chọn học tại Victoria?</h2>
        <p class="text-muted text-lg">Môi trường học tập tiêu chuẩn Nhật Bản ngay tại Việt Nam.</p>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:shadow-soft transition-all hover:-translate-y-1">
          <div class="w-14 h-14 rounded-2xl bg-sky-100 flex items-center justify-center text-sky-500 text-2xl mb-6">
            <i class="bi bi-person-video3"></i>
          </div>
          <h3 class="text-xl font-bold text-midnight mb-3">Giáo viên bản xứ</h3>
          <p class="text-muted">Đội ngũ Sensei người Nhật trực tiếp giảng dạy giao tiếp và luyện phát âm chuẩn giọng Tokyo.</p>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:shadow-soft transition-all hover:-translate-y-1">
          <div class="w-14 h-14 rounded-2xl bg-sage-100 flex items-center justify-center text-sage-500 text-2xl mb-6">
            <i class="bi bi-award"></i>
          </div>
          <h3 class="text-xl font-bold text-midnight mb-3">Cam kết đầu ra JLPT</h3>
          <p class="text-muted">Chương trình bám sát đề thi năng lực tiếng Nhật, cam kết học viên đạt N5/N4 đúng lộ trình.</p>
        </div>

        <!-- Card 3 -->
        <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:shadow-soft transition-all hover:-translate-y-1">
          <div class="w-14 h-14 rounded-2xl bg-sakura-100 flex items-center justify-center text-sakura-500 text-2xl mb-6">
            <i class="bi bi-people"></i>
          </div>
          <h3 class="text-xl font-bold text-midnight mb-3">Lớp học sĩ số nhỏ</h3>
          <p class="text-muted">Tối đa 15 học viên/lớp đảm bảo giáo viên có thể kèm cặp và tương tác với từng cá nhân.</p>
        </div>

        <!-- Card 4 -->
        <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:shadow-soft transition-all hover:-translate-y-1">
          <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-500 text-2xl mb-6">
            <i class="bi bi-mic"></i>
          </div>
          <h3 class="text-xl font-bold text-midnight mb-3">Luyện phỏng vấn 1-1</h3>
          <p class="text-muted">Rèn luyện kỹ năng trả lời phỏng vấn Cục xuất nhập cảnh và trường Nhật ngữ tự tin nhất.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Danh sách khóa học -->
  <section id="khoa-hoc" class="py-24 bg-slate-50 border-t border-slate-100 relative overflow-hidden">
    <!-- Decorative -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-sage-100/50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-sky-100/50 rounded-full blur-3xl translate-y-1/2 -translate-x-1/3"></div>

    <div class="container mx-auto px-6 lg:px-12 relative z-10">
      <div class="text-center mb-16 max-w-2xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-black text-midnight font-display mb-4">Các Khóa Học Tiêu Biểu</h2>
        <p class="text-muted text-lg">Lựa chọn lộ trình phù hợp nhất với mục tiêu của bạn.</p>
      </div>

      <div class="grid md:grid-cols-3 gap-8">
        
        <!-- Course 1 -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-medium transition-all group flex flex-col">
          <div class="p-8 pb-6 bg-slate-50/50 border-b border-slate-100">
            <span class="inline-block bg-sky-100 text-sky-600 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-lg mb-4">Cho người mới bắt đầu</span>
            <h3 class="text-2xl font-bold text-midnight mb-2 group-hover:text-sky-500 transition-colors">Khóa N5 - Nền Tảng</h3>
            <p class="text-muted text-sm line-clamp-2">Làm quen với bảng chữ cái, ngữ pháp cơ bản và giao tiếp đời sống hàng ngày.</p>
          </div>
          <div class="p-8 flex flex-col flex-grow">
            <div class="flex items-end gap-1 mb-6">
              <span class="text-3xl font-black text-midnight">10.000.000</span>
              <span class="text-muted font-medium mb-1">VNĐ</span>
            </div>
            <ul class="space-y-4 mb-8 text-sm text-slate-600 flex-grow">
              <li class="flex items-start gap-3"><i class="bi bi-clock text-sky-500 mt-0.5"></i> <span>Thời lượng: <strong>3 tháng</strong> (5 buổi/tuần)</span></li>
              <li class="flex items-start gap-3"><i class="bi bi-book text-sky-500 mt-0.5"></i> <span>Giáo trình: Minna no Nihongo I</span></li>
              <li class="flex items-start gap-3"><i class="bi bi-bullseye text-sky-500 mt-0.5"></i> <span>Mục tiêu: Đạt chứng chỉ N5, giao tiếp cơ bản</span></li>
            </ul>
            <a href="/contact" class="block w-full py-3.5 px-4 text-center rounded-xl bg-slate-100 hover:bg-sky-50 text-midnight hover:text-sky-600 font-bold transition-colors">
              Đăng ký học
            </a>
          </div>
        </div>

        <!-- Course 2 -->
        <div class="bg-white rounded-3xl border-2 border-sage-500 overflow-hidden shadow-md hover:shadow-xl hover:shadow-sage-500/20 transition-all group flex flex-col relative z-10">
          <div class="absolute top-0 right-0 bg-sage-500 text-white text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-bl-xl">Bán chạy nhất</div>
          <div class="p-8 pb-6 bg-sage-50 border-b border-sage-100">
            <span class="inline-block bg-white text-sage-600 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-lg mb-4 shadow-sm">Tiêu chuẩn du học</span>
            <h3 class="text-2xl font-bold text-midnight mb-2">Khóa N4 - Chuyên Sâu</h3>
            <p class="text-muted text-sm line-clamp-2">Đào tạo chuyên sâu ngữ pháp, từ vựng và kỹ năng nghe hiểu đáp ứng kỳ thi năng lực.</p>
          </div>
          <div class="p-8 flex flex-col flex-grow">
            <div class="flex items-end gap-1 mb-6">
              <span class="text-4xl font-black text-midnight">15.000.000</span>
              <span class="text-muted font-medium mb-1">VNĐ</span>
            </div>
            <ul class="space-y-4 mb-8 text-sm text-slate-600 flex-grow">
              <li class="flex items-start gap-3"><i class="bi bi-clock text-sage-500 mt-0.5 text-lg"></i> <span>Thời lượng: <strong>6 tháng</strong> (5 buổi/tuần)</span></li>
              <li class="flex items-start gap-3"><i class="bi bi-book text-sage-500 mt-0.5 text-lg"></i> <span>Giáo trình: Minna no Nihongo I & II</span></li>
              <li class="flex items-start gap-3"><i class="bi bi-check2-circle text-sage-500 mt-0.5 text-lg"></i> <span>Cam kết đầu ra JLPT/NAT-TEST N4</span></li>
              <li class="flex items-start gap-3"><i class="bi bi-chat-text text-sage-500 mt-0.5 text-lg"></i> <span>Tặng kèm khóa luyện giao tiếp 1 tháng</span></li>
            </ul>
            <a href="/contact" class="block w-full py-4 px-4 text-center rounded-xl bg-sage-500 hover:bg-sage-400 text-white font-bold transition-colors shadow-md">
              Đăng ký ngay
            </a>
          </div>
        </div>

        <!-- Course 3 -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-medium transition-all group flex flex-col">
          <div class="p-8 pb-6 bg-slate-50/50 border-b border-slate-100">
            <span class="inline-block bg-sakura-100 text-sakura-600 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-lg mb-4">Khóa ngắn hạn</span>
            <h3 class="text-2xl font-bold text-midnight mb-2 group-hover:text-sakura-500 transition-colors">Luyện Phỏng Vấn</h3>
            <p class="text-muted text-sm line-clamp-2">Trang bị kỹ năng tác phong, cách trả lời phỏng vấn Cục XNC và Trường Nhật Ngữ.</p>
          </div>
          <div class="p-8 flex flex-col flex-grow">
            <div class="flex items-end gap-1 mb-6">
              <span class="text-3xl font-black text-midnight">3.500.000</span>
              <span class="text-muted font-medium mb-1">VNĐ</span>
            </div>
            <ul class="space-y-4 mb-8 text-sm text-slate-600 flex-grow">
              <li class="flex items-start gap-3"><i class="bi bi-clock text-sakura-500 mt-0.5"></i> <span>Thời lượng: <strong>1 tháng</strong> (3 buổi/tuần)</span></li>
              <li class="flex items-start gap-3"><i class="bi bi-mic text-sakura-500 mt-0.5"></i> <span>Thực hành mock-interview 1-1 liên tục</span></li>
              <li class="flex items-start gap-3"><i class="bi bi-award text-sakura-500 mt-0.5"></i> <span>Chỉnh sửa phát âm, tác phong chuẩn Nhật</span></li>
            </ul>
            <a href="/contact" class="block w-full py-3.5 px-4 text-center rounded-xl bg-slate-100 hover:bg-sakura-50 text-midnight hover:text-sakura-600 font-bold transition-colors">
              Đăng ký học
            </a>
          </div>
        </div>

      </div>

    </div>
  </section>



</main>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
