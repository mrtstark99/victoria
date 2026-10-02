    <section id="cost" class="home-section home-cost bg-slate-50 py-20 lg:py-28 relative">
      <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-sand-200 to-transparent"></div>
      <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="home-section-heading mx-auto max-w-2xl text-center mb-16">
          <span class="home-kicker">04 — Minh bạch chi phí</span>
          <h2 class="text-3xl sm:text-4xl font-bold text-primary font-display">Tổng chi phí dự kiến năm đầu</h2>
          <p class="mt-4 text-lg text-muted">Hãy tùy chỉnh các lựa chọn dưới đây để xem chi tiết dự toán và chuẩn bị tài chính vững vàng cho lộ trình du học của bạn.</p>
        </div>

        <style>
          .wiz-panel { display: none; }
          .wiz-panel.wiz-active { display: flex; flex-direction: column; gap: 12px; animation: wizFade 0.22s ease; }
          @keyframes wizFade { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

          .calc-item input:checked + .calc-box {
            border-color: var(--color-primary, #0d243e);
            background-color: rgba(13, 36, 62, 0.04);
          }
          .calc-item input:checked + .calc-box .radio-dot {
            transform: scale(1);
          }

          .calc-item input:checked + .calc-box .calc-details {
            display: block;
            animation: slideDown 0.22s ease-out;
          }
          @keyframes slideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
          }
        </style>

        <div id="calculator">
          <div class="flex flex-col lg:flex-row gap-8 items-stretch">

            <!-- Left: Step Wizard -->
            <div class="w-full lg:w-2/3 flex">
              <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col w-full reveal">

                <!-- Progress -->
                <div class="flex items-center justify-between mb-5">
                  <span class="text-xs font-bold text-muted uppercase tracking-widest">Bước <span id="wiz-num-label">1</span> / 6</span>
                  <div class="flex gap-1.5" id="wiz-pips">
                    <div class="h-1.5 rounded-full transition-all duration-300 bg-primary" style="width:2rem" data-pip="0"></div>
                    <div class="h-1.5 rounded-full transition-all duration-300 bg-slate-200" style="width:1rem" data-pip="1"></div>
                    <div class="h-1.5 rounded-full transition-all duration-300 bg-slate-200" style="width:1rem" data-pip="2"></div>
                    <div class="h-1.5 rounded-full transition-all duration-300 bg-slate-200" style="width:1rem" data-pip="3"></div>
                    <div class="h-1.5 rounded-full transition-all duration-300 bg-slate-200" style="width:1rem" data-pip="4"></div>
                    <div class="h-1.5 rounded-full transition-all duration-300 bg-slate-200" style="width:1rem" data-pip="5"></div>
                  </div>
                </div>

                <!-- Step heading -->
                <h4 class="text-lg font-bold text-midnight flex items-center gap-3 mb-1">
                  <span class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-bold shrink-0" id="wiz-badge">1</span>
                  <span id="wiz-title-text">Chọn Hệ du học Nhật Bản</span>
                </h4>
                <p class="text-sm text-muted mb-5 pl-11" style="min-height:18px" id="wiz-subtitle"></p>

                <!-- Panels -->
                <div class="flex-1" id="wiz-panels">

                  <!-- Panel 0: Hệ du học -->
                  <div class="wiz-panel wiz-active" data-panel="0">
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_system" value="Trường Nhật Ngữ" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Trường Nhật Ngữ</div>
                              <div class="text-xs text-muted mt-0.5">Lộ trình học tiếng Nhật tập trung từ 1.5 - 2 năm tại Nhật Bản</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">Phổ biến nhất</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Lộ trình:</strong> Học tiếng tại VN (6-8 tháng) → Sang Nhật học Trường Nhật ngữ (1-2 năm) → Học lên Senmon/Đại học hoặc đổi visa đi làm.</p>
                          <p><strong>Yêu cầu:</strong> Tốt nghiệp THPT (trống dưới 5 năm), GPA &gt; 6.0, tiếng Nhật tối thiểu N5 (hoặc học cấp tốc tại Victoria).</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_system" value="Trường Senmon" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Trường Senmon</div>
                              <div class="text-xs text-muted mt-0.5">Đào tạo nghề thực chiến 2 năm chuyên sâu (IT, Du lịch...)</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">Hướng nghiệp</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Lộ trình:</strong> Đào tạo nghề thực hành 2 năm chuyên sâu (IT, Du lịch, Thiết kế, Khách sạn...) → Đi làm ngay với visa kỹ sư/nhân văn quốc tế.</p>
                          <p><strong>Yêu cầu:</strong> Tốt nghiệp THPT, trình độ tiếng Nhật tối thiểu N2 hoặc tốt nghiệp khóa học tại Trường Nhật ngữ bên Nhật.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_system" value="Trường Đại Học" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Trường Đại Học</div>
                              <div class="text-xs text-muted mt-0.5">Hệ cử nhân chính quy tại các trường đại học hàng đầu Nhật Bản</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">Chính quy</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Lộ trình:</strong> Học cử nhân chính quy 4 năm tại Nhật → Nhận bằng Cử nhân quốc tế → Cơ hội thăng tiến cao và định cư lâu dài.</p>
                          <p><strong>Yêu cầu:</strong> Tốt nghiệp THPT, điểm GPA &gt; 6.5, thi kỳ thi EJU hoặc chứng chỉ tiếng Nhật tối thiểu N2.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_system" value="Chương Trình Học Bổng" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Chương Trình Học Bổng</div>
                              <div class="text-xs text-muted mt-0.5">Học bổng báo, điều dưỡng... Miễn 100% học phí & KTX</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">Học bổng</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Lộ trình:</strong> Doanh nghiệp tài trợ 100% học phí & KTX → Vừa học vừa làm bán thời gian tại doanh nghiệp bảo trợ → Làm việc chính thức từ 3-5 năm sau tốt nghiệp.</p>
                          <p><strong>Yêu cầu:</strong> Tốt nghiệp THPT, sức khỏe tốt, cam kết tuân thủ hợp đồng lao động và học tập của đơn vị tài trợ học bổng.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_system" value="Hệ Đại Học Tiếng Anh" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Hệ Đại Học Tiếng Anh</div>
                              <div class="text-xs text-muted mt-0.5">Chương trình E-Track giảng dạy 100% bằng tiếng Anh</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">E-Track</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Lộ trình:</strong> Học cử nhân chính quy 4 năm bằng 100% tiếng Anh tại các trường đại học quốc tế ở Nhật → Học thêm tiếng Nhật song song.</p>
                          <p><strong>Yêu cầu:</strong> Tốt nghiệp THPT, điểm GPA &gt; 7.0, chứng chỉ tiếng Anh (IELTS &gt; 5.5 hoặc TOEFL iBT &gt; 75 hoặc tương đương).</p>
                        </div>
                      </div>
                    </label>
                  </div>

                  <!-- Panel 1: Gói dịch vụ -->
                  <div class="wiz-panel" data-panel="1">
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_package" value="15000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Tiêu Chuẩn</div>
                              <div class="text-xs text-muted mt-0.5">Xử lý hồ sơ cơ bản</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0" id="calc-package-price-text">15.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Dịch vụ bao gồm:</strong> Dịch thuật công chứng hồ sơ, giải trình tài chính chặt chẽ, luyện phỏng vấn visa và trường, hỗ trợ nộp COE và đặt vé máy bay.</p>
                        </div>
                      </div>
                    </label>
                  </div>

                  <!-- Panel 2: Khóa học tiếng Nhật -->
                  <div class="wiz-panel" data-panel="2">
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_course" value="0" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Tự học / Đã có N4</div>
                              <div class="text-xs text-muted mt-0.5">Dành cho học sinh đã đủ trình độ</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">0đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Nội dung hỗ trợ:</strong> Victoria Universal hỗ trợ kiểm tra năng lực đầu vào miễn phí. Thích hợp cho học sinh tự học tại nhà hoặc đã có chứng chỉ tiếng Nhật JLPT N4 trở lên.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_course" value="10000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Cơ bản 3 tháng</div>
                              <div class="text-xs text-muted mt-0.5">Chương trình chuẩn N5</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">10.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Chi tiết khóa học:</strong> Đào tạo cấp tốc 3 tháng (5 buổi/tuần). Học bảng chữ cái, ngữ pháp nền tảng N5, phát âm chuẩn và phản xạ giao tiếp cơ bản.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_course" value="15000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Chuyên sâu 6 tháng</div>
                              <div class="text-xs text-muted mt-0.5">Luyện thi JLPT N4</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">15.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Chi tiết khóa học:</strong> Đào tạo bán trú 6 tháng từ N5 lên N4. Tích hợp luyện thi các chứng chỉ JLPT/NAT-TEST, kỹ năng phỏng vấn học bổng và xin việc làm thêm tại Nhật.</p>
                        </div>
                      </div>
                    </label>
                  </div>

                  <!-- Panel 3: Trường Nhật Ngữ -->
                  <div class="wiz-panel" data-panel="3">
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_school" value="110000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Trường ở tỉnh xa</div>
                              <div class="text-xs text-muted mt-0.5">Hokkaido, Ibaraki, Oita... Học phí và sinh hoạt phí đều rất rẻ.</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">~ 110.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Đặc điểm vùng:</strong> Học phí chỉ khoảng 60 - 70 Man/năm. Tiền thuê phòng chỉ khoảng 2 - 3 Man/tháng. Thích hợp cho học viên muốn tiết kiệm ngân sách ban đầu tối đa.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_school" value="125000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Thành phố cỡ trung</div>
                              <div class="text-xs text-muted mt-0.5">Fukuoka, Chiba, Saitama... Dễ tìm việc làm, chi phí vừa phải.</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">~ 125.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Đặc điểm vùng:</strong> Học phí 70 - 75 Man/năm. Đầy đủ việc làm thêm phong phú nhưng giá cả sinh hoạt nhẹ nhàng hơn nhiều so với nội đô Tokyo.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_school" value="135000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="flex items-center gap-2">
                                <span class="font-bold text-midnight text-sm sm:text-base">Ngoại ô Tokyo / Osaka</span>
                                <span class="bg-amber-400 text-white text-[9px] font-bold uppercase px-2 py-0.5 rounded-full">Phổ biến</span>
                              </div>
                              <div class="text-xs text-muted mt-0.5">Cách trung tâm 30-40p tàu. Cân bằng tốt giữa chi phí và cơ hội.</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">~ 135.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Đặc điểm vùng:</strong> Học phí 75 - 80 Man/năm. Lựa chọn tối ưu để tiếp cận cơ hội việc làm lớn tại trung tâm nhưng vẫn giữ mức sinh hoạt phí ở mức dễ chịu.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_school" value="145000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Trung tâm Tokyo / Osaka</div>
                              <div class="text-xs text-muted mt-0.5">Sầm uất, nhiều cơ hội việc làm lương cao nhưng học phí đắt.</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">~ 145.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Đặc điểm vùng:</strong> Học phí &gt; 80 Man/năm. Chi phí thuê nhà đắt đỏ nhất. Bù lại, đây là trung tâm sầm uất với vô vàn cơ hội làm thêm lương cao và dễ tìm việc dài hạn.</p>
                        </div>
                      </div>
                    </label>
                  </div>

                  <!-- Panel 4: Sinh hoạt ban đầu -->
                  <div class="wiz-panel" data-panel="4">
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_living" value="30000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Tiết Kiệm</div>
                              <div class="text-xs text-muted mt-0.5">KTX chung 4 người + 10 Man tiền mặt phòng thân</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">~ 30.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Chi tiết ngân sách:</strong> Khoảng 8 Man đóng trước KTX chung cọc và tiền nhà 2-3 tháng + 10 Man chi tiêu ăn uống tối thiểu tháng đầu tiên.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_living" value="45000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                          <div class="flex items-center gap-4">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                              <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                            </div>
                            <div>
                              <div class="font-bold text-midnight text-sm sm:text-base">Cơ Bản</div>
                              <div class="text-xs text-muted mt-0.5">KTX tiêu chuẩn 2 người + 12 Man tiền mặt phòng thân</div>
                            </div>
                          </div>
                          <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">~ 45.000.000đ</div>
                        </div>
                        <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                          <p><strong>Chi tiết ngân sách:</strong> Khoảng 15 Man đóng trước KTX phòng đôi tiêu chuẩn 3 tháng + 12 Man chi tiêu ăn uống, đi lại thoải mái hơn trong thời gian đầu.</p>
                        </div>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_living" value="60000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-355 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                          <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                            <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                          </div>
                          <div>
                            <div class="font-bold text-midnight text-sm sm:text-base">Thoải Mái</div>
                            <div class="text-xs text-muted mt-0.5">Thuê phòng riêng + 15 Man tiền mặt phòng thân</div>
                          </div>
                        </div>
                        <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">~ 60.000.000đ</div>
                      </div>
                      <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                        <p><strong>Chi tiết ngân sách:</strong> Khoảng 25 Man đóng trước tiền thuê phòng riêng (cọc + lễ + tiền nhà 1 tháng) + 15 Man chi tiêu dư dả.</p>
                      </div>
                    </label>
                  </div>

                  <!-- Panel 5: Thủ tục khác -->
                  <div class="wiz-panel" data-panel="5">
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_other" value="8650000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                          <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                            <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                          </div>
                          <div>
                            <div class="font-bold text-midnight text-sm sm:text-base">Thấp Nhất</div>
                            <div class="text-xs text-muted mt-0.5">Tổng các mức thấp nhất (Săn vé giá rẻ)</div>
                          </div>
                        </div>
                        <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">8.650.000đ</div>
                      </div>
                      <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                        <p><strong>Bao gồm:</strong> Phí khám lao (~1.5M), lệ phí visa (~1.3M) cộng thêm vé máy bay giá rẻ một chiều (bay transit hoặc hãng giá rẻ).</p>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_other" value="13000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                          <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                            <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                          </div>
                          <div>
                            <div class="font-bold text-midnight text-sm sm:text-base">Trung Bình</div>
                            <div class="text-xs text-muted mt-0.5">Chi tiêu hợp lý, vé máy bay phổ thông</div>
                          </div>
                        </div>
                        <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">13.000.000đ</div>
                      </div>
                      <div class="calc-details border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5 hidden">
                        <p><strong>Bao gồm:</strong> Lệ phí bắt buộc và vé máy bay bay thẳng phổ thông của các hãng hàng không uy tín như Vietnam Airlines.</p>
                      </div>
                    </label>
                    <label class="calc-item cursor-pointer w-full block">
                      <input type="radio" name="calc_other" value="17000000" class="sr-only">
                      <div class="calc-box p-4 rounded-2xl border-2 border-slate-200 transition-all hover:border-slate-350 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                          <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                            <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform radio-dot"></div>
                          </div>
                          <div>
                            <div class="font-bold text-midnight text-sm sm:text-base">Dự Tính An Toàn</div>
                            <div class="text-xs text-muted mt-0.5">Tổng mức cao nhất, bay thẳng giờ đẹp</div>
                          </div>
                        </div>
                        <div class="text-sm sm:text-base text-primary font-black text-right shrink-0">17.000.000đ</div>
                      </div>
                    </label>
                  </div>

                </div><!-- /wiz-panels -->

                <!-- Footer nav -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between" style="min-height:36px">
                  <button id="wiz-back" class="text-sm text-muted hover:text-primary transition-colors flex items-center gap-1.5" type="button" style="visibility:hidden">
                    <i class="bi bi-arrow-left text-xs"></i> Quay lại
                  </button>
                  <div class="flex items-center gap-4">
                    <span class="text-xs text-slate-400 italic hidden sm:inline" id="wiz-hint">Chọn một mục để tiếp tục →</span>
                    <button id="wiz-next" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-slate-800 transition-all flex items-center gap-1 opacity-50 pointer-events-none" type="button">
                      Tiếp tục <i class="bi bi-arrow-right text-[10px]"></i>
                    </button>
                  </div>
                </div>

              </div>
            </div><!-- /left -->

            <!-- Right: Sticky Receipt -->
            <div class="w-full lg:w-1/3 reveal reveal-delay-200">
              <div class="sticky top-24 bg-midnight text-white rounded-3xl p-8 shadow-xl border-t-4 border-sage-500 relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -left-16 -bottom-16 w-40 h-40 bg-sage-500/10 rounded-full blur-2xl"></div>
                <h4 class="text-xl font-bold font-display mb-6 border-b border-white/10 pb-4 flex items-center gap-2 relative z-10">
                  <i class="bi bi-receipt text-sage-400"></i> Phiếu Dự Toán
                </h4>
                <div class="space-y-4 mb-8 text-[15px] relative z-10">
                  <div class="flex justify-between items-start gap-4">
                    <span class="text-white/70">Hệ du học:</span>
                    <span class="font-semibold text-right text-sage-300" id="summary_system">Chưa chọn</span>
                  </div>
                  <div class="flex justify-between items-start gap-4">
                    <span class="text-white/70">1. Dịch vụ Victoria:</span>
                    <span class="font-semibold text-right text-slate-300" id="summary_package">Chưa chọn</span>
                  </div>
                  <div class="flex justify-between items-start gap-4">
                    <span class="text-white/70">2. Khóa học VN:</span>
                    <span class="font-semibold text-right text-slate-300" id="summary_course">Chưa chọn</span>
                  </div>
                  <div class="flex justify-between items-start gap-4">
                    <span class="text-white/70">3. Học phí trường:</span>
                    <span class="font-semibold text-right text-slate-300" id="summary_school">Chưa chọn</span>
                  </div>
                  <div class="flex justify-between items-start gap-4">
                    <span class="text-white/70">4. Ăn ở ban đầu:</span>
                    <span class="font-semibold text-right text-slate-300" id="summary_living">Chưa chọn</span>
                  </div>
                  <div class="flex justify-between items-start gap-4">
                    <span class="text-white/70">5. Thủ tục khác:</span>
                    <span class="font-semibold text-right text-slate-300" id="summary_other">Chưa chọn</span>
                  </div>
                </div>
                <div class="border-t border-white/10 pt-6 mt-6 relative z-10">
                  <div class="text-sm text-sage-300 font-bold tracking-widest uppercase mb-1">Tổng Cần Chuẩn Bị</div>
                  <div class="text-4xl font-black text-white font-display tracking-tight break-words">
                    <span id="summary_total">0</span><span class="text-xl ml-1 text-white/70 font-medium">VNĐ</span>
                  </div>
                  <p class="text-xs text-white/50 mt-3 italic">*Bảng dự toán mang tính tham khảo. Chi phí thực tế phụ thuộc tỷ giá Yên và nhu cầu tiêu dùng.</p>
                </div>
                <a href="/contact" class="w-full mt-8 block text-center bg-sage-500 hover:bg-sage-400 text-white rounded-xl py-4 font-bold transition-colors shadow-lg relative z-10">
                  Đăng ký tư vấn lộ trình này <i class="bi bi-arrow-right ml-1"></i>
                </a>
              </div>
            </div>

          </div>
        </div>

        <script>
          document.addEventListener('DOMContentLoaded', function() {
            const calculatorConfig = <?= json_encode(\Models\HomeCalculator::get(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            const priceGroups = {
              calc_course: calculatorConfig.course_prices,
              calc_school: calculatorConfig.school_prices,
              calc_living: calculatorConfig.living_prices,
              calc_other: calculatorConfig.other_prices
            };
            for (const [name, prices] of Object.entries(priceGroups)) {
              document.querySelectorAll(`input[name="${name}"]`).forEach((input, index) => {
                const amount = prices[index];
                input.value = amount;
                const box = input.closest('label')?.querySelector('.calc-box');
                const priceLabel = box?.querySelector('.text-right');
                if (priceLabel) priceLabel.textContent = new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
              });
            }

            const STEPS = [
              { title: 'Chọn Hệ du học Nhật Bản',                    sub: '',                                                              name: 'calc_system'  },
              { title: 'Chọn gói Dịch vụ Victoria Universal',          sub: '',                                                              name: 'calc_package' },
              { title: 'Chương trình học Tiếng Nhật tại Việt Nam',    sub: '',                                                              name: 'calc_course'  },
              { title: 'Lựa chọn Trường Nhật Ngữ (Năm đầu tiên)',    sub: '',                                                              name: 'calc_school'  },
              { title: 'Chi phí sinh hoạt ban đầu tại Nhật',          sub: '',                                                              name: 'calc_living'  },
              { title: 'Chi phí thủ tục khác tại VN',                 sub: 'Gồm: Khám lao phổi, Thi JLPT, Hộ chiếu, Vé máy bay',         name: 'calc_other'   },
            ];

            let currentStep = 0;

            const fmt   = v => new Intl.NumberFormat('vi-VN').format(v) + 'đ';
            const fmtNS = v => new Intl.NumberFormat('vi-VN').format(v);

            function updateSummary() {
              const systemEl = document.querySelector('input[name="calc_system"]:checked');
              const systemVal = systemEl ? systemEl.value : 'Chưa chọn';
              document.getElementById('summary_system').textContent = systemVal;

              const get = name => { const el = document.querySelector(`input[name="${name}"]:checked`); return el ? parseInt(el.value) : -1; };
              const vals = STEPS.slice(1).map(s => get(s.name));
              const ids  = ['summary_package','summary_course','summary_school','summary_living','summary_other'];
              vals.forEach((v, i) => {
                document.getElementById(ids[i]).textContent = v === -1 ? 'Chưa chọn' : fmt(v);
              });
              const total = vals.filter(v => v !== -1).reduce((a, b) => a + b, 0);
              const el    = document.getElementById('summary_total');
              const cur   = parseInt(el.textContent.replace(/\./g, '')) || 0;
              animateValue(el, cur, total, 400);
            }

            function animateValue(obj, start, end, dur) {
              let t0 = null;
              const tick = ts => {
                if (!t0) t0 = ts;
                const p = Math.min((ts - t0) / dur, 1);
                const e = 1 - Math.pow(1 - p, 3);
                obj.innerHTML = fmtNS(Math.floor(e * (end - start) + start));
                if (p < 1) requestAnimationFrame(tick); else obj.innerHTML = fmtNS(end);
              };
              requestAnimationFrame(tick);
            }

            function goToStep(idx) {
              document.querySelectorAll('.wiz-panel').forEach(p => p.classList.remove('wiz-active'));
              document.querySelector(`.wiz-panel[data-panel="${idx}"]`).classList.add('wiz-active');
              currentStep = idx;

              document.getElementById('wiz-num-label').textContent  = idx + 1;
              document.getElementById('wiz-badge').textContent       = idx + 1;
              document.getElementById('wiz-title-text').textContent  = STEPS[idx].title;
              document.getElementById('wiz-subtitle').textContent    = STEPS[idx].sub;

              document.querySelectorAll('[data-pip]').forEach(pip => {
                const i = parseInt(pip.dataset.pip);
                pip.style.backgroundColor = i <= idx ? 'var(--color-primary, #0d243e)' : '#e2e8f0';
                pip.style.width = i === idx ? '2rem' : (i < idx ? '1.5rem' : '1rem');
              });

              document.getElementById('wiz-back').style.visibility  = idx === 0 ? 'hidden' : 'visible';
              document.getElementById('wiz-hint').textContent        = idx < 5 ? 'Chọn một mục để tiếp tục →' : 'Đã hoàn thành tất cả các bước ✓';

              // Check if a radio is checked in the active step to enable/disable Next button
              const hasChecked = document.querySelector(`.wiz-panel[data-panel="${idx}"] input[type="radio"]:checked`) !== null;
              const nextBtn = document.getElementById('wiz-next');
              if (hasChecked) {
                nextBtn.classList.remove('opacity-50', 'pointer-events-none');
              } else {
                nextBtn.classList.add('opacity-50', 'pointer-events-none');
              }

              if (idx === 5) {
                nextBtn.innerHTML = 'Hoàn thành <i class="bi bi-check-circle ml-1"></i>';
              } else {
                nextBtn.innerHTML = 'Tiếp tục <i class="bi bi-arrow-right text-[10px]"></i>';
              }
            }

            const PRICES = calculatorConfig.package_prices;

            document.querySelectorAll('input[name="calc_system"]').forEach(input => {
              input.addEventListener('change', function() {
                const val = this.value;
                const price = PRICES[val] || 15000000;
                
                // Update Step 1 (calc_package) input value and price text
                const packageInput = document.querySelector('input[name="calc_package"]');
                if (packageInput) {
                  packageInput.value = price;
                  // Reset it to force re-selection of Step 1 if the user navigates back & changes
                  packageInput.checked = false;
                }
                const priceText = document.getElementById('calc-package-price-text');
                if (priceText) {
                  priceText.textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
                }
                
                updateSummary();
              });
            });

            document.querySelectorAll('#calculator input[type="radio"]').forEach(input => {
              input.addEventListener('change', function() {
                updateSummary();
                const nextBtn = document.getElementById('wiz-next');
                nextBtn.classList.remove('opacity-50', 'pointer-events-none');
              });
            });

            document.getElementById('wiz-back').addEventListener('click', () => {
              if (currentStep > 0) goToStep(currentStep - 1);
            });

            document.getElementById('wiz-next').addEventListener('click', () => {
              if (currentStep < 5) {
                goToStep(currentStep + 1);
              } else {
                // Last step: scroll to contact
                const contactSec = document.getElementById('contact');
                if (contactSec) {
                  contactSec.scrollIntoView({ behavior: 'smooth' });
                }
              }
            });

            goToStep(0);
            updateSummary();
          });
        </script>
      </div>
    </section>
