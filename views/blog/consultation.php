<?php


$db = Database::getInstance();

// Lấy các buổi tư vấn nhóm đang active, chưa qua ngày
$stmt = $db->prepare("
    SELECT * FROM consultation_slots
    WHERE type = 'group' AND status IN ('active','full')
      AND scheduled_date >= date('now','localtime')
    ORDER BY scheduled_date ASC, time_start ASC
    LIMIT 10
");
$stmt->execute();
$group_slots = $stmt->fetchAll();

$page_title = 'Đặt lịch tư vấn - Victoria Universal';
$page_description = 'Đặt lịch tư vấn du học Nhật Bản miễn phí qua Zoom - nhóm hoặc cá nhân 1-1';
include APP_ROOT . '/views/layouts/header.php';
?>

<main class="be-static-page bg-slate-50 min-h-screen">

  <!-- Hero -->
  <section class="relative overflow-hidden bg-primary pt-20 pb-14 sm:pt-24 sm:pb-16 lg:pt-28 lg:pb-20">
    <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-white/[.06]"></div>
    <div class="absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-primary-400/10"></div>
    <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
      <p class="text-[11px] font-extrabold uppercase tracking-[.2em] text-primary-300">Online consultation</p>
      <h1 class="mt-3 text-4xl font-black tracking-tight text-white font-display sm:text-5xl">Tư vấn du học Nhật Bản</h1>
      <nav class="mt-6 flex items-center gap-2 text-xs font-semibold text-white/65" aria-label="Breadcrumb">
        <a href="/" class="transition hover:text-white">Trang chủ</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-white">Tư vấn Zoom</span>
      </nav>
    </div>
  </section>

  <!-- Two Options Banner -->
  <section class="max-w-7xl mx-auto px-5 lg:px-8 mt-10 sm:mt-14 mb-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 reveal">

      <!-- Group -->
      <button onclick="switchTab('group')" id="tab-btn-group"
        class="tab-btn group bg-white border-2 border-primary rounded-3xl p-7 text-left shadow-medium hover:shadow-hard transition-all cursor-pointer active-tab">
        <div class="flex items-center gap-4 mb-3">
          <div class="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-white text-xl flex-shrink-0">
            <i class="bi bi-people-fill"></i>
          </div>
          <div>
            <h2 class="text-lg font-bold text-midnight font-display">Tư vấn nhóm · Zoom</h2>
            <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">Miễn phí 100%</span>
          </div>
        </div>
        <p class="text-sm text-muted leading-relaxed">Buổi tư vấn nhóm qua Zoom mỗi tuần. Nghe chia sẻ về lộ trình, hồ sơ, học phí — đặt câu hỏi trực tiếp với chuyên viên.</p>
      </button>

      <!-- Individual -->
      <button onclick="switchTab('individual')" id="tab-btn-individual"
        class="tab-btn group bg-white border-2 border-slate-200 rounded-3xl p-7 text-left shadow-soft hover:shadow-medium hover:border-primary transition-all cursor-pointer">
        <div class="flex items-center gap-4 mb-3">
          <div class="w-12 h-12 rounded-2xl bg-slate-100 group-hover:bg-primary flex items-center justify-center text-slate-500 group-hover:text-white text-xl flex-shrink-0 transition-all">
            <i class="bi bi-person-video3"></i>
          </div>
          <div>
            <h2 class="text-lg font-bold text-midnight font-display">Tư vấn 1-1 cá nhân</h2>
            <span class="inline-block bg-blue-50 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">Theo lịch hẹn</span>
          </div>
        </div>
        <p class="text-sm text-muted leading-relaxed">Buổi tư vấn riêng tư, tập trung hoàn toàn vào trường hợp của bạn. Chuyên viên sẽ xây dựng lộ trình phù hợp nhất.</p>
      </button>

    </div>
  </section>

  <!-- TAB: Group Sessions -->
  <section id="tab-group" class="max-w-7xl mx-auto px-6 lg:px-12 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

      <!-- Left: Available sessions -->
      <div class="reveal">
        <h3 class="text-xl font-bold text-midnight font-display mb-1">Buổi tư vấn nhóm sắp tới</h3>
        <p class="text-sm text-muted mb-6">Chọn một buổi để đăng ký tham gia.</p>

        <?php if (empty($group_slots)): ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-soft p-8 text-center text-muted">
          <i class="bi bi-calendar-x text-4xl text-slate-300 block mb-3"></i>
          <p class="font-semibold">Hiện chưa có buổi nào được lên lịch</p>
          <p class="text-sm mt-1">Bạn có thể điền form bên phải để yêu cầu thêm buổi mới.</p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
          <?php foreach ($group_slots as $slot):
            $isFull = $slot['status'] === 'full' || $slot['current_participants'] >= $slot['max_participants'];
            $dateFormatted = date('d/m/Y', strtotime($slot['scheduled_date']));
            $dayOfWeek = ['CN','T2','T3','T4','T5','T6','T7'][date('w', strtotime($slot['scheduled_date']))];
          ?>
          <div class="slot-card bg-white rounded-2xl border-2 <?php echo $isFull ? 'border-slate-200 opacity-60' : 'border-slate-100 hover:border-primary cursor-pointer'; ?> shadow-soft p-5 transition-all <?php echo $isFull ? '' : 'hover:shadow-medium'; ?>"
               <?php echo $isFull ? '' : "onclick=\"selectSlot(this, {$slot['id']}, '" . addslashes($slot['title']) . "', '$dateFormatted {$dayOfWeek}', '{$slot['time_start']} - {$slot['time_end']}')\""; ?>>
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <span class="text-xs font-bold text-primary bg-primary-50 px-2 py-0.5 rounded-full"><?php echo $dayOfWeek; ?></span>
                  <span class="text-sm font-semibold text-midnight"><?php echo $dateFormatted; ?></span>
                  <span class="text-sm text-muted">· <?php echo $slot['time_start']; ?> – <?php echo $slot['time_end']; ?></span>
                </div>
                <h4 class="font-bold text-midnight text-[15px]"><?php echo htmlspecialchars($slot['title']); ?></h4>
                <?php if ($slot['description']): ?>
                <p class="text-xs text-muted mt-1 leading-relaxed"><?php echo htmlspecialchars($slot['description']); ?></p>
                <?php endif; ?>
              </div>
              <div class="text-right flex-shrink-0">
                <?php if ($isFull): ?>
                <span class="inline-block bg-slate-100 text-slate-500 text-xs font-bold px-3 py-1 rounded-full">Đã đủ chỗ</span>
                <?php else: ?>
                <span class="inline-block bg-green-50 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                  Còn <?php echo $slot['max_participants'] - $slot['current_participants']; ?> chỗ
                </span>
                <?php endif; ?>
                <div class="text-xs text-slate-400 mt-1.5">
                  <i class="bi bi-people"></i> <?php echo $slot['current_participants']; ?>/<?php echo $slot['max_participants']; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right: Registration form -->
      <div class="reveal">
        <div class="bg-white rounded-3xl shadow-medium border border-slate-100 p-8">
          <div id="selected-slot-info" class="hidden mb-6 bg-primary-50 border border-primary-100 rounded-2xl p-4">
            <p class="text-xs font-bold text-primary uppercase tracking-wider mb-1">Buổi đã chọn</p>
            <p id="selected-slot-title" class="font-bold text-midnight text-sm"></p>
            <p id="selected-slot-time" class="text-xs text-muted mt-0.5"></p>
          </div>
          <p id="no-slot-notice" class="mb-5 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <i class="bi bi-info-circle mr-1"></i> Vui lòng chọn một buổi ở bên trái để đăng ký.
          </p>

          <h3 class="text-lg font-bold text-midnight font-display mb-5">Thông tin đăng ký</h3>
          <form id="group-booking-form" class="space-y-4">
            <input type="hidden" name="booking_type" value="group">
            <input type="hidden" name="slot_id" id="slot_id_input" value="">

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Nguyễn Văn A" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm transition-all">
              </div>
              <div class="space-y-1.5">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" required placeholder="0981 xxx xxx" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm transition-all">
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Email <span class="text-red-500">*</span></label>
              <input type="email" name="email" required placeholder="email@example.com" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm transition-all">
            </div>

            <div class="space-y-1.5">
              <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Trình độ tiếng Nhật</label>
              <div class="relative">
                <select name="japanese_level" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm appearance-none transition-all">
                  <option value="Chưa học">Chưa học tiếng Nhật</option>
                  <option value="N5">N5</option>
                  <option value="N4">N4</option>
                  <option value="N3">N3</option>
                  <option value="N2 trở lên">N2 trở lên</option>
                </select>
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400"><i class="bi bi-chevron-down text-sm"></i></div>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Câu hỏi muốn đặt (tuỳ chọn)</label>
              <textarea name="message" rows="3" placeholder="Bạn muốn tư vấn về vấn đề gì?" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm resize-none transition-all"></textarea>
            </div>

            <button type="submit" id="group-submit-btn" disabled
              class="w-full bg-slate-300 text-slate-500 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 cursor-not-allowed" style="transition: all 0.3s">
              <i class="bi bi-camera-video"></i> Đăng ký tham gia
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- TAB: Individual Session -->
  <section id="tab-individual" class="max-w-7xl mx-auto px-6 lg:px-12 pb-20 hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

      <!-- Left: Info -->
      <div class="reveal">
        <div class="bg-primary rounded-3xl p-10 text-white h-full flex flex-col justify-between">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-2xl mb-6">
              <i class="bi bi-person-video3"></i>
            </div>
            <h3 class="text-2xl font-bold font-display mb-4">Tư vấn 1-1 riêng tư</h3>
            <p class="text-primary-100 leading-relaxed mb-8">
              Buổi tư vấn cá nhân tập trung hoàn toàn vào hoàn cảnh và mục tiêu của bạn. Chuyên viên sẽ xây dựng lộ trình du học phù hợp nhất.
            </p>
            <ul class="space-y-4">
              <li class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="bi bi-check-lg text-green-300"></i></div>
                <span class="text-[15px] text-white/90">Phân tích hồ sơ cá nhân chi tiết</span>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="bi bi-check-lg text-green-300"></i></div>
                <span class="text-[15px] text-white/90">Tư vấn chọn trường phù hợp</span>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="bi bi-check-lg text-green-300"></i></div>
                <span class="text-[15px] text-white/90">Lộ trình học tiếng Nhật theo năng lực</span>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="bi bi-check-lg text-green-300"></i></div>
                <span class="text-[15px] text-white/90">Dự toán học phí, sinh hoạt phí tại Nhật</span>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="bi bi-check-lg text-green-300"></i></div>
                <span class="text-[15px] text-white/90">Giải đáp mọi câu hỏi của bạn và gia đình</span>
              </li>
            </ul>
          </div>
          <div class="mt-10 pt-8 border-t border-white/10">
            <p class="text-white/60 text-sm">Thời gian mỗi buổi: <strong class="text-white">45–60 phút</strong></p>
            <p class="text-white/60 text-sm mt-1">Hình thức: <strong class="text-white">Zoom Video Call</strong></p>
          </div>
        </div>
      </div>

      <!-- Right: Booking form -->
      <div class="reveal">
        <div class="bg-white rounded-3xl shadow-medium border border-slate-100 p-8">
          <h3 class="text-lg font-bold text-midnight font-display mb-1">Đặt lịch hẹn</h3>
          <p class="text-sm text-muted mb-6">Điền thông tin, chúng tôi sẽ xác nhận và gửi link Zoom cho bạn.</p>

          <form id="individual-booking-form" class="space-y-4">
            <input type="hidden" name="booking_type" value="individual">

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Nguyễn Văn A" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm transition-all">
              </div>
              <div class="space-y-1.5">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" required placeholder="0981 xxx xxx" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm transition-all">
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Email <span class="text-red-500">*</span></label>
              <input type="email" name="email" required placeholder="email@example.com" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Ngày mong muốn <span class="text-red-500">*</span></label>
                <input type="date" name="preferred_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                  class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm transition-all">
              </div>
              <div class="space-y-1.5">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Khung giờ mong muốn</label>
                <div class="relative">
                  <select name="preferred_time" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm appearance-none transition-all">
                    <option value="08:00 - 09:00">08:00 – 09:00</option>
                    <option value="09:00 - 10:00">09:00 – 10:00</option>
                    <option value="10:00 - 11:00">10:00 – 11:00</option>
                    <option value="14:00 - 15:00">14:00 – 15:00</option>
                    <option value="15:00 - 16:00">15:00 – 16:00</option>
                    <option value="16:00 - 17:00">16:00 – 17:00</option>
                    <option value="19:00 - 20:00">19:00 – 20:00 (tối)</option>
                    <option value="20:00 - 21:00">20:00 – 21:00 (tối)</option>
                  </select>
                  <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400"><i class="bi bi-chevron-down text-sm"></i></div>
                </div>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Chủ đề tư vấn</label>
              <div class="relative">
                <select name="topic" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm appearance-none transition-all">
                  <option value="Chọn trường và ngành học">Chọn trường và ngành học</option>
                  <option value="Quy trình hồ sơ và Visa">Quy trình hồ sơ và Visa</option>
                  <option value="Học phí và chi phí sinh hoạt">Học phí và chi phí sinh hoạt</option>
                  <option value="Học bổng và hỗ trợ tài chính">Học bổng và hỗ trợ tài chính</option>
                  <option value="Lộ trình học tiếng Nhật">Lộ trình học tiếng Nhật</option>
                  <option value="Khác">Khác</option>
                </select>
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400"><i class="bi bi-chevron-down text-sm"></i></div>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Trình độ tiếng Nhật</label>
              <div class="relative">
                <select name="japanese_level" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm appearance-none transition-all">
                  <option value="Chưa học">Chưa học tiếng Nhật</option>
                  <option value="N5">N5</option>
                  <option value="N4">N4</option>
                  <option value="N3">N3</option>
                  <option value="N2 trở lên">N2 trở lên</option>
                </select>
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400"><i class="bi bi-chevron-down text-sm"></i></div>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Thông tin thêm (tuỳ chọn)</label>
              <textarea name="message" rows="3" placeholder="Bạn muốn chia sẻ thêm điều gì?" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none text-sm resize-none transition-all"></textarea>
            </div>

            <button type="submit"
              class="w-full bg-primary text-white py-3.5 rounded-xl font-bold hover:bg-ink transition-all flex items-center justify-center gap-2 shadow-medium hover:shadow-hard">
              <i class="bi bi-calendar-check"></i> Gửi yêu cầu đặt lịch
            </button>
          </form>
        </div>
      </div>

    </div>
  </section>

</main>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-3xl shadow-hard p-10 max-w-md w-full mx-4 text-center">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
      <i class="bi bi-check-circle-fill text-green-500 text-3xl"></i>
    </div>
    <h3 class="text-xl font-bold text-midnight font-display mb-2" id="modal-title">Đăng ký thành công!</h3>
    <p class="text-muted text-sm leading-relaxed mb-6" id="modal-msg"></p>
    <button onclick="document.getElementById('success-modal').classList.add('hidden')" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-ink transition-colors">Đóng</button>
  </div>
</div>

<script>
function switchTab(tab) {
  document.getElementById('tab-group').classList.toggle('hidden', tab !== 'group');
  document.getElementById('tab-individual').classList.toggle('hidden', tab !== 'individual');

  document.getElementById('tab-btn-group').classList.toggle('active-tab', tab === 'group');
  document.getElementById('tab-btn-group').classList.toggle('border-primary', tab === 'group');
  document.getElementById('tab-btn-group').classList.toggle('border-slate-200', tab !== 'group');

  document.getElementById('tab-btn-individual').classList.toggle('active-tab', tab === 'individual');
  document.getElementById('tab-btn-individual').classList.toggle('border-primary', tab === 'individual');
  document.getElementById('tab-btn-individual').classList.toggle('border-slate-200', tab !== 'individual');
}

function selectSlot(el, slotId, title, date, time) {
  // Deselect all
  document.querySelectorAll('.slot-card').forEach(c => {
    c.classList.remove('border-primary', 'bg-primary-50');
    c.classList.add('border-slate-100');
  });
  // Select this one
  el.classList.add('border-primary', 'bg-primary-50');
  el.classList.remove('border-slate-100');

  document.getElementById('slot_id_input').value = slotId;
  document.getElementById('selected-slot-title').textContent = title;
  document.getElementById('selected-slot-time').textContent = date + ' · ' + time;
  document.getElementById('selected-slot-info').classList.remove('hidden');
  document.getElementById('no-slot-notice').classList.add('hidden');

  const btn = document.getElementById('group-submit-btn');
  btn.disabled = false;
  btn.className = 'w-full bg-primary text-white py-3.5 rounded-xl font-bold hover:bg-ink transition-all flex items-center justify-center gap-2 shadow-medium cursor-pointer';
}

async function submitBooking(formEl, type) {
  const data = new FormData(formEl);

  // Basic validation
  for (const [key, val] of data.entries()) {
    if (['name','phone','email'].includes(key) && !val.trim()) {
      alert('Vui lòng điền đầy đủ thông tin bắt buộc.');
      return;
    }
  }

  const submitBtn = formEl.querySelector('[type=submit]');
  const origText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Đang gửi...';

  try {
    const res = await fetch('/api/consultation.php', { method: 'POST', body: data });
    const json = await res.json();
    if (json.success) {
      window.BrightAnalytics?.leadSuccess(formEl, type === 'group' ? 'group_consultation' : 'individual_consultation');
      document.getElementById('modal-title').textContent = type === 'group' ? 'Đăng ký thành công!' : 'Yêu cầu đã gửi!';
      document.getElementById('modal-msg').textContent = json.message;
      document.getElementById('success-modal').classList.remove('hidden');
      formEl.reset();
      if (type === 'group') {
        document.getElementById('slot_id_input').value = '';
        document.getElementById('selected-slot-info').classList.add('hidden');
        document.getElementById('no-slot-notice').classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.className = 'w-full bg-slate-300 text-slate-500 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 cursor-not-allowed';
        submitBtn.innerHTML = '<i class="bi bi-camera-video"></i> Đăng ký tham gia';
        return;
      }
    } else {
      alert('Lỗi: ' + (json.message || 'Vui lòng thử lại.'));
    }
  } catch(e) {
    alert('Có lỗi xảy ra. Vui lòng thử lại.');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = origText;
  }
}

document.getElementById('group-booking-form').addEventListener('submit', function(e) {
  e.preventDefault();
  if (!document.getElementById('slot_id_input').value) {
    alert('Vui lòng chọn một buổi tư vấn nhóm trước.');
    return;
  }
  submitBooking(this, 'group');
});

document.getElementById('individual-booking-form').addEventListener('submit', function(e) {
  e.preventDefault();
  submitBooking(this, 'individual');
});
</script>

<style>
.active-tab { border-color: #0d243e !important; }
</style>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
