<?php
$page_title = "Danh sách Trường Nhật Ngữ - Victoria Universal";
include APP_ROOT . '/views/layouts/header.php';

// Read data
$json_data = file_get_contents(APP_ROOT . '/schools_data.json');
$data = json_decode($json_data, true);
$schools = $data['schools'];

// Extract unique regions for initial dropdown state
$macroRegions = [];
foreach ($schools as $school) {
    if (!empty($school['macro_region'])) {
        $macroRegions[$school['macro_region']] = true;
    }
}
$macroRegions = array_keys($macroRegions);
sort($macroRegions);

// Escape JSON for use in JS
$schools_json = json_encode($schools, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<main class="be-static-page pb-24 bg-slate-50 min-h-screen">
  <!-- Hero Section -->
  <section class="relative overflow-hidden bg-primary pt-20 pb-14 sm:pt-24 sm:pb-16 lg:pt-28 lg:pb-20">
    <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-white/[.06]"></div>
    <div class="absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-primary-400/10"></div>
    <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
      <p class="text-[11px] font-extrabold uppercase tracking-[.2em] text-primary-300">Japanese language schools</p>
      <h1 class="mt-3 text-4xl font-black tracking-tight text-white font-display sm:text-5xl">Hệ thống trường Nhật ngữ</h1>
      <nav class="mt-6 flex items-center gap-2 text-xs font-semibold text-white/65" aria-label="Breadcrumb">
        <a href="/" class="transition hover:text-white">Trang chủ</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-white">Trường Nhật ngữ</span>
      </nav>
    </div>
  </section>

  <section class="py-10 sm:py-14 relative">
    <div class="container mx-auto px-6 lg:px-12">
        <!-- Interactive Filter Panel -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col items-center justify-between gap-6">
            <div class="w-full relative mb-2">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-search"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Tìm kiếm tên trường..." class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-midnight font-medium focus:bg-white focus:border-sage-500 focus:ring-4 focus:ring-sage-500/10 transition-all outline-none">
            </div>
            <div class="w-full flex flex-col md:flex-row items-center gap-4">
                <div class="flex flex-col gap-2 w-full md:w-1/3">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider shrink-0">Vùng:</span>
                    <select id="macroRegionSelect" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-midnight font-medium focus:bg-white focus:border-sage-500 focus:ring-4 focus:ring-sage-500/10 transition-all outline-none appearance-none cursor-pointer">
                        <option value="">Tất cả vùng</option>
                        <?php foreach ($macroRegions as $region): ?>
                            <option value="<?= htmlspecialchars($region) ?>"><?= htmlspecialchars($region) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex flex-col gap-2 w-full md:w-1/3">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider shrink-0">Tỉnh:</span>
                    <select id="prefectureSelect" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-midnight font-medium focus:bg-white focus:border-sage-500 focus:ring-4 focus:ring-sage-500/10 transition-all outline-none appearance-none cursor-pointer" disabled>
                        <option value="">Tất cả tỉnh</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2 w-full md:w-1/3">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider shrink-0">Thành phố:</span>
                    <select id="areaSelect" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-midnight font-medium focus:bg-white focus:border-sage-500 focus:ring-4 focus:ring-sage-500/10 transition-all outline-none appearance-none cursor-pointer" disabled>
                        <option value="">Tất cả thành phố</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
  </section>

  <!-- Schools Grid -->
  <section class="pb-24">
    <div class="container mx-auto px-6 lg:px-12">
      <!-- Active Filters Display -->
      <div id="activeFilterDisplay" class="flex items-center gap-2 mb-8 hidden">
          <span class="text-sm text-muted">Đang hiển thị kết quả cho:</span>
          <span id="activeRegionBadge" class="inline-flex items-center gap-1.5 bg-sage-50 text-sage-700 py-1 px-3 rounded-full text-xs font-bold whitespace-nowrap">
            <!-- Filled by JS -->
          </span>
      </div>

      <div id="schoolsGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <!-- Schools will be rendered here by JS -->
      </div>
      
      <!-- Pagination -->
      <div id="pagination" class="flex justify-center items-center mt-12 gap-2 hidden">
          <!-- Pagination buttons will be rendered here -->
      </div>

      <!-- Empty State -->
      <div id="emptyState" class="hidden text-center py-20">
          <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400 text-4xl">
              <i class="bi bi-search"></i>
          </div>
          <h3 class="text-xl font-bold text-midnight mb-2">Không tìm thấy trường nào</h3>
          <p class="text-muted max-w-md mx-auto">Không có kết quả phù hợp với từ khóa hoặc khu vực bạn chọn. Vui lòng thử lại với tiêu chí khác.</p>
          <button id="resetFilters" class="mt-6 text-sage-600 font-bold hover:text-sage-700">Xóa bộ lọc</button>
      </div>

    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const schools = <?= $schools_json ?>;
    
    // Check if Seiko exists from our manual rename previously (if it got overwritten by recrawl)
    schools.forEach(s => {
        if (s.name_en === "Naruyuki Japanese School") {
            s.name_en = "Seiko Japanese School";
        }
    });

    const grid = document.getElementById('schoolsGrid');
    const paginationContainer = document.getElementById('pagination');
    const searchInput = document.getElementById('searchInput');
    const macroRegionSelect = document.getElementById('macroRegionSelect');
    const prefectureSelect = document.getElementById('prefectureSelect');
    const areaSelect = document.getElementById('areaSelect');
    const emptyState = document.getElementById('emptyState');
    const activeFilterDisplay = document.getElementById('activeFilterDisplay');
    const activeRegionBadge = document.getElementById('activeRegionBadge');
    const resetFiltersBtn = document.getElementById('resetFilters');

    let currentFilteredSchools = schools;
    let currentPage = 1;
    const itemsPerPage = 20;

    function renderSchools(schoolsToRender) {
        grid.innerHTML = '';
        
        if (schoolsToRender.length === 0) {
            grid.classList.add('hidden');
            paginationContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        grid.classList.remove('hidden');
        emptyState.classList.add('hidden');

        // Pagination slicing
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedSchools = schoolsToRender.slice(startIndex, endIndex);

        paginatedSchools.forEach(school => {
            const el = document.createElement('div');
            el.className = 'bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group flex flex-col h-full';
            
            let websiteHtml = school.website ? `<a href="${school.website}" target="_blank" class="text-sky-600 hover:text-sky-700 underline truncate block"><i class="bi bi-link-45deg"></i> Website trường</a>` : '<span class="text-slate-400 text-sm">Đang cập nhật</span>';
            
            el.innerHTML = `
                <div class="p-6 pb-5 bg-slate-50/50 border-b border-slate-100 flex-grow">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <span class="inline-flex items-center gap-1.5 bg-sky-50 text-sky-600 py-1 px-3 rounded-xl text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-geo-alt-fill"></i> ${school.prefecture}
                        </span>
                        <div class="w-8 h-8 rounded-full bg-slate-200/50 flex items-center justify-center text-slate-400 group-hover:bg-sage-100 group-hover:text-sage-500 transition-colors shrink-0">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-midnight mb-1 group-hover:text-sage-600 transition-colors leading-snug">
                        ${school.name_en}
                    </h3>
                    ${school.name_jp && school.name_jp !== school.name_en ? `<div class="text-xs text-slate-400 font-medium font-japanese mb-2">${school.name_jp}</div>` : ''}
                    ${school.area ? `<div class="text-sm text-slate-500 mt-2"><i class="bi bi-geo"></i> Khu vực chi tiết: ${school.area}${school.area_ja && school.area_ja !== school.area ? ` (${school.area_ja})` : ''}</div>` : ''}
                </div>
                
                <div class="p-6 bg-white shrink-0 border-t border-slate-50 flex items-center justify-between">
                    <div class="text-sm font-medium">
                        ${websiteHtml}
                    </div>
                </div>
            `;
            grid.appendChild(el);
        });
        
        renderPagination(schoolsToRender.length);
    }
    
    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        paginationContainer.innerHTML = '';
        
        if (totalPages <= 1) {
            paginationContainer.classList.add('hidden');
            return;
        }
        
        paginationContainer.classList.remove('hidden');
        
        // Prev button
        const prevBtn = document.createElement('button');
        prevBtn.className = `px-4 py-2 rounded-xl text-sm font-medium transition-colors ${currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-200 text-midnight hover:bg-slate-50'}`;
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i> Trước';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderSchools(currentFilteredSchools);
                window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
            }
        });
        paginationContainer.appendChild(prevBtn);
        
        // Page numbers
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `w-10 h-10 rounded-xl text-sm font-medium transition-colors ${i === currentPage ? 'bg-sage-500 text-white shadow-md' : 'bg-white border border-slate-200 text-midnight hover:bg-slate-50'}`;
            pageBtn.innerText = i;
            pageBtn.addEventListener('click', () => {
                currentPage = i;
                renderSchools(currentFilteredSchools);
                window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
            });
            paginationContainer.appendChild(pageBtn);
        }
        
        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = `px-4 py-2 rounded-xl text-sm font-medium transition-colors ${currentPage === totalPages ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-200 text-midnight hover:bg-slate-50'}`;
        nextBtn.innerHTML = 'Sau <i class="bi bi-chevron-right"></i>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderSchools(currentFilteredSchools);
                window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
            }
        });
        paginationContainer.appendChild(nextBtn);
    }

    function populateSelect(selectElement, options, placeholder) {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        options.forEach(opt => {
            if (opt) {
                const el = document.createElement('option');
                el.value = opt;
                el.innerText = opt;
                selectElement.appendChild(el);
            }
        });
        selectElement.disabled = options.length === 0;
    }

    macroRegionSelect.addEventListener('change', function() {
        const selectedMacro = this.value;
        if (selectedMacro) {
            const prefs = [...new Set(schools.filter(s => s.macro_region === selectedMacro).map(s => s.prefecture))].sort();
            populateSelect(prefectureSelect, prefs, "Tất cả tỉnh");
        } else {
            populateSelect(prefectureSelect, [], "Tất cả tỉnh");
        }
        populateSelect(areaSelect, [], "Tất cả thành phố");
        applyFilters();
    });

    prefectureSelect.addEventListener('change', function() {
        const selectedPref = this.value;
        const selectedMacro = macroRegionSelect.value;
        if (selectedPref) {
            const areas = [...new Set(schools.filter(s => s.prefecture === selectedPref && s.macro_region === selectedMacro).map(s => s.area))].sort();
            populateSelect(areaSelect, areas, "Tất cả thành phố");
        } else {
            populateSelect(areaSelect, [], "Tất cả thành phố");
        }
        applyFilters();
    });

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const macro = macroRegionSelect.value;
        const pref = prefectureSelect.value;
        const area = areaSelect.value;
        
        currentFilteredSchools = schools;

        if (macro) {
            currentFilteredSchools = currentFilteredSchools.filter(s => s.macro_region === macro);
        }
        if (pref) {
            currentFilteredSchools = currentFilteredSchools.filter(s => s.prefecture === pref);
        }
        if (area) {
            currentFilteredSchools = currentFilteredSchools.filter(s => s.area === area);
        }

        if (macro || pref || area) {
            activeFilterDisplay.classList.remove('hidden');
            let badgeText = [];
            if (macro) badgeText.push(`Vùng: ${macro}`);
            if (pref) badgeText.push(`Tỉnh: ${pref}`);
            if (area) badgeText.push(`Thành phố: ${area}`);
            activeRegionBadge.innerHTML = `<i class="bi bi-geo-alt-fill"></i> ${badgeText.join(' - ')}`;
        } else {
            activeFilterDisplay.classList.add('hidden');
        }

        if (searchTerm) {
            currentFilteredSchools = currentFilteredSchools.filter(s => {
                const searchStr = `${s.name_en} ${s.name_jp || ''} ${s.area || ''} ${s.area_ja || ''} ${s.prefecture} ${s.macro_region}`.toLowerCase();
                return searchStr.includes(searchTerm);
            });
        }

        // Reset to first page on new filter
        currentPage = 1;
        renderSchools(currentFilteredSchools);
    }

    searchInput.addEventListener('input', applyFilters);
    areaSelect.addEventListener('change', applyFilters);
    
    resetFiltersBtn.addEventListener('click', () => {
        searchInput.value = '';
        macroRegionSelect.value = '';
        populateSelect(prefectureSelect, [], "Tất cả tỉnh");
        populateSelect(areaSelect, [], "Tất cả thành phố");
        applyFilters();
    });

    // Initial render
    renderSchools(schools);
});
</script>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
