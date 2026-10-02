<?php
/**
 * Q&A Page - Premium Social Media Feed Style with Collapsible Inline Comments
 */
$page_title = 'Góc chia sẻ kiến thức du học - Victoria Universal';
$page_description = 'Cộng đồng hỏi đáp du học Nhật Bản. Chia sẻ kinh nghiệm, giải đáp thắc mắc tuyển sinh, chi phí, học bổng và cuộc sống.';

// Build FAQ Schema from active QA questions

try {
    $db = Database::getInstance();
    $q_stmt = $db->prepare("
        SELECT q.*, (SELECT a.content FROM qa_answers a WHERE a.question_id = q.id AND a.status = 'active' ORDER BY a.likes_count DESC, a.created_at ASC LIMIT 1) as best_answer
        FROM qa_questions q
        WHERE q.status = 'active'
        ORDER BY q.likes_count DESC, q.created_at DESC
        LIMIT 10
    ");
    $q_stmt->execute();
    $faq_questions = $q_stmt->fetchAll();

    if (!empty($faq_questions)) {
        $main_entity = [];
        foreach ($faq_questions as $fq) {
            if (!empty($fq['best_answer'])) {
                $main_entity[] = [
                    "@type" => "Question",
                    "name" => truncateText(strip_tags($fq['content']), 120),
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => strip_tags($fq['best_answer'])
                    ]
                ];
            }
        }
        if (!empty($main_entity)) {
            $faq_schema = [
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => $main_entity
            ];
        }
    }
} catch (Exception $e) {
    // Ignore database errors
}

include APP_ROOT . '/views/layouts/header.php';

$is_logged_in = isLoggedIn();
$current_user_name = $is_logged_in ? $_SESSION['user_name'] ?? 'Thành viên' : '';
$is_admin = $is_logged_in && (isAdmin() || isEditor());

$db = Database::getInstance();
$comm_stmt = $db->prepare("
    SELECT * FROM community_groups
    WHERE status = 'active'
    ORDER BY display_order ASC, platform ASC, id ASC
    LIMIT 6
");
$comm_stmt->execute();
$community_groups = $comm_stmt->fetchAll();

$platform_meta = [
    'facebook'  => ['label' => 'Facebook', 'color' => '#1877f2', 'icon' => 'bi-facebook'],
    'zalo'      => ['label' => 'Zalo',     'color' => '#0068ff', 'icon' => 'bi-chat-dots-fill'],
    'youtube'   => ['label' => 'YouTube',  'color' => '#ff0000', 'icon' => 'bi-youtube'],
    'telegram'  => ['label' => 'Telegram', 'color' => '#229ed9', 'icon' => 'bi-telegram'],
    'other'     => ['label' => 'Khác',     'color' => '#6b7280', 'icon' => 'bi-people-fill'],
];
?>

<!-- Custom Premium Feed Styles -->
<style>
    /* Styling scrollbar for tags bar */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    /* Gradients for cards background */
    .bg-threads-dark {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    }
    .bg-threads-sunrise {
        background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
    }
    .bg-threads-neon {
        background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%);
    }
    .bg-threads-ocean {
        background: linear-gradient(135deg, #0ea5e9 0%, #059669 100%);
    }
    .bg-threads-sakura {
        background: linear-gradient(135deg, #f472b6 0%, #f43f5e 100%);
    }
    .bg-threads-lavender {
        background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
    }

    /* Social Action Button Transitions */
    .social-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 13.5px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 99px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        background: transparent;
        border: none;
        cursor: pointer;
    }
    .social-action-btn:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .social-action-btn.liked {
        color: #ef4444;
        background-color: #fef2f2;
    }
    .social-action-btn.active-comment {
        color: #0d243e;
        background-color: #f0f5fa;
    }

    /* Heartbeat animation for like */
    @keyframes heartBeat {
        0% { transform: scale(1); }
        30% { transform: scale(1.25); }
        60% { transform: scale(0.85); }
        100% { transform: scale(1); }
    }
    .animate-heart {
        animation: heartBeat 0.35s ease-in-out;
    }

    /* Highlight class for shared post */
    .highlight-card {
        animation: cardHighlight 2.5s ease-in-out;
    }
    @keyframes cardHighlight {
        0%, 100% { border-color: rgba(226, 232, 240, 0.8); box-shadow: 0 4px 20px -2px rgba(1, 53, 103, 0.05); }
        20%, 80% { border-color: #0d243e; box-shadow: 0 0 0 4px rgba(13, 36, 62, 0.15), 0 12px 32px -4px rgba(1, 53, 103, 0.1); }
    }
</style>

<main class="be-static-page bg-slate-50 min-h-screen">

    <!-- Hero Section -->
    <section class="bg-primary text-white pt-24 pb-24 relative overflow-hidden lg:pt-28">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="mx-auto max-w-7xl px-5 lg:px-8 relative z-10 text-center">
            
            <!-- Breadcrumb -->
            <nav class="flex items-center justify-center gap-2 text-xs font-semibold text-white/60 mb-6 uppercase tracking-wider" aria-label="Breadcrumb">
                <a href="/" class="hover:text-white transition-colors">Trang chủ</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-white">Hỏi đáp cộng đồng</span>
            </nav>

            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1 text-xs font-bold text-white/90 mb-4 border border-white/10 uppercase tracking-widest">
                <i class="bi bi-chat-left-text text-[10px] text-amber-400"></i> Trao đổi kiến thức
            </span>
            <h1 class="text-3xl sm:text-[2.75rem] font-black font-display tracking-tight leading-tight mb-4">Hỏi Đáp Du Học Nhật Bản</h1>
            <p class="text-base sm:text-lg text-white/85 max-w-3xl leading-relaxed mx-auto">
                Cộng đồng chia sẻ kinh nghiệm, giải đáp thắc mắc tuyển sinh, chi phí, học bổng và cuộc sống sinh hoạt tại Nhật Bản.
            </p>
        </div>
    </section>

    <section class="py-12 -mt-10 relative z-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Categories Slider -->
            <div class="flex items-center justify-start sm:justify-center gap-2 overflow-x-auto pb-4 mb-8 scrollbar-hide select-none max-w-5xl mx-auto" id="tag_filters_bar">
                <button class="tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 active bg-primary text-white border-primary shadow-sm" data-tag="">
                    <i class="bi bi-grid-fill mr-1.5"></i>Tất cả chủ đề
                </button>
                <button class="tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 bg-white text-slate-600 border-slate-200 hover:bg-slate-50" data-tag="#tuyensinh">
                    #tuyensinh
                </button>
                <button class="tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 bg-white text-slate-600 border-slate-200 hover:bg-slate-50" data-tag="#hocphi">
                    #hocphi
                </button>
                <button class="tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 bg-white text-slate-600 border-slate-200 hover:bg-slate-50" data-tag="#visanhat">
                    #visanhat
                </button>
                <button class="tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 bg-white text-slate-600 border-slate-200 hover:bg-slate-50" data-tag="#vieclam">
                    #vieclam
                </button>
                <button class="tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 bg-white text-slate-600 border-slate-200 hover:bg-slate-50" data-tag="#nhatngu">
                    #nhatngu
                </button>
                <button class="tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 bg-white text-slate-600 border-slate-200 hover:bg-slate-50" data-tag="#cuocsong">
                    #cuocsong
                </button>
            </div>

            <!-- Two-Column Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Main Q&A Feed (Left Column - 70% width on Desktop) -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Admin Post Creator Card -->
                    <?php if ($is_admin): ?>
                    <div id="admin_creator_card" class="bg-white rounded-[2rem] border border-slate-100/80 shadow-soft p-5 sm:p-6 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <!-- User dynamic Avatar -->
                            <div id="admin_main_avatar" class="w-10 h-10 rounded-full flex-shrink-0"></div>
                            <button id="btn_open_post_modal" class="flex-1 bg-slate-50 hover:bg-slate-100/75 border border-slate-200/50 text-slate-500 rounded-full px-5 py-3 text-left text-[13.5px] font-medium flex items-center justify-between transition-all duration-200 shadow-xs focus:outline-none">
                                <span>Chia sẻ thông tin hoặc kiến thức du học mới...</span>
                                <i class="bi bi-pencil-square text-slate-400 text-base"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4 pt-3.5 border-t border-slate-100">
                            <button onclick="document.getElementById('btn_open_post_modal').click(); document.getElementById('btn_trigger_file').click();" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 rounded-xl transition-all text-xs font-bold text-slate-600">
                                <i class="bi bi-image text-emerald-500 text-lg"></i>
                                <span>Đính kèm ảnh</span>
                            </button>
                            <button onclick="document.getElementById('btn_open_post_modal').click(); document.getElementById('post_tag').focus();" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 rounded-xl transition-all text-xs font-bold text-slate-600">
                                <i class="bi bi-tags-fill text-orange-500 text-lg"></i>
                                <span>Chọn chủ đề</span>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Feed Content Wrapper -->
                    <div id="qa_feed_container" class="space-y-6">
                        <!-- Skeletons will load here -->
                    </div>

                    <!-- Pagination Controls -->
                    <div id="qa_pagination_container" class="flex items-center justify-center gap-2 mt-8 select-none">
                        <!-- Pagination buttons will load here -->
                    </div>

                </div>

                <!-- Sidebar Information (Right Column - 30% width on Desktop) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- User Profile & Status Card -->
                    <div class="bg-white rounded-[2rem] border border-slate-100/80 shadow-soft p-6">
                        <div class="flex items-center gap-4 mb-5 pb-5 border-b border-slate-100">
                            <div id="sidebar_user_avatar" class="w-12 h-12 rounded-full flex-shrink-0"></div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-base text-slate-900 truncate leading-snug">
                                    <?= $is_logged_in ? htmlspecialchars($current_user_name) : 'Cộng đồng Victoria' ?>
                                </h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">
                                    <?= $is_logged_in ? ($is_admin ? 'Ban quản trị' : 'Thành viên') : 'Khách truy cập' ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($is_logged_in): ?>
                        <div class="space-y-3">
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Chào mừng bạn quay trở lại! Bạn có thể bình luận và thích các bài chia sẻ kiến thức hữu ích từ ban quản trị.
                            </p>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-2">
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Đăng nhập tài khoản của bạn để bình luận, trao đổi ý kiến và tương tác cùng các cố vấn du học Nhật Bản.
                            </p>
                            <a href="/login?redirect=/qa" class="inline-flex w-full justify-center items-center py-2.5 bg-primary text-white rounded-full text-xs font-bold hover:bg-slate-800 transition-all shadow-sm">
                                Đăng nhập tài khoản
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Victoria Community List Card -->
                    <?php if (!empty($community_groups)): ?>
                    <div class="bg-white rounded-[2rem] border border-slate-100/80 shadow-soft p-6">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                            <i class="bi bi-people-fill text-primary text-lg"></i>
                            <h3 class="text-sm font-extrabold uppercase tracking-wide text-primary">Cộng đồng Victoria</h3>
                        </div>
                        <div class="flex flex-col gap-3">
                            <?php foreach ($community_groups as $g): 
                                $meta = $platform_meta[$g['platform']] ?? $platform_meta['other'];
                            ?>
                            <a href="<?= htmlspecialchars($g['url']) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-2xl transition-all group">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs flex-shrink-0" style="background: <?= $meta['color'] ?>;">
                                    <i class="bi <?= $meta['icon'] ?>"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-[13px] text-slate-800 truncate group-hover:text-primary transition-colors"><?= htmlspecialchars($g['name']) ?></h4>
                                    <?php if ($g['member_count']): ?>
                                        <p class="text-[10px] text-slate-400 font-medium"><?= htmlspecialchars($g['member_count']) ?> thành viên</p>
                                    <?php endif; ?>
                                </div>
                                <i class="bi bi-box-arrow-up-right text-slate-400 group-hover:text-primary transition-colors text-[10px]"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Community Regulations Card -->
                    <div class="bg-white rounded-[2rem] border border-slate-100/80 shadow-soft p-6">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                            <i class="bi bi-shield-check text-primary text-lg"></i>
                            <h3 class="text-sm font-extrabold uppercase tracking-wide text-primary">Quy định cộng đồng</h3>
                        </div>
                        <ul class="space-y-3 text-xs text-slate-500 leading-relaxed">
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></span>
                                <span>Thông tin được đăng tải từ cố vấn chuyên môn của Victoria Universal.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></span>
                                <span>Vui lòng bình luận lịch sự, tôn trọng các thành viên khác.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></span>
                                <span>Nội dung bình luận tập trung vào các câu hỏi về học tập, sinh sống tại Nhật.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Consultation Quick Action -->
                    <div class="bg-gradient-to-br from-primary to-slate-800 rounded-[2rem] p-6 text-white shadow-soft relative overflow-hidden">
                        <div class="absolute -right-10 -bottom-10 opacity-10">
                            <i class="bi bi-zoom text-9xl"></i>
                        </div>
                        <h4 class="font-extrabold text-base mb-2 font-display">Cần tư vấn trực tiếp 1-1?</h4>
                        <p class="text-xs text-white/80 leading-relaxed mb-5">
                            Đặt lịch hẹn Zoom miễn phí với cố vấn tuyển sinh của chúng tôi để được giải đáp thắc mắc chi tiết về hồ sơ của bạn.
                        </p>
                        <a href="/consultation" class="inline-flex px-5 py-2.5 bg-white text-primary rounded-full text-xs font-bold hover:bg-slate-100 hover:scale-102 transition-all shadow-sm">
                            Đăng ký Tư vấn Zoom miễn phí
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </section>
</main>

<!-- Modal: Create Post (Admin only) -->
<?php if ($is_admin): ?>
<div id="post_modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <!-- Backdrop -->
    <div id="post_modal_backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>
    
    <!-- Modal Dialog -->
    <div id="post_modal_card" class="bg-white w-full max-w-[580px] rounded-[2rem] p-6 sm:p-7 shadow-hard relative z-10 mx-4 transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-y-auto">
        <!-- Close button -->
        <button id="btn_close_post_modal" class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-black transition-colors focus:outline-none">
            <i class="bi bi-x-lg text-xs"></i>
        </button>

        <h3 class="text-lg font-extrabold text-slate-900 font-display mb-4 pb-3 border-b border-slate-100">Đăng bài viết chia sẻ kiến thức</h3>
        
        <div class="flex gap-4 items-start">
            <div id="modal_user_avatar" class="w-10 h-10 rounded-full flex-shrink-0"></div>
            
            <div class="flex-1 min-w-0">
                <div class="font-bold text-[14.5px] text-slate-900 mb-2"><?= htmlspecialchars($current_user_name) ?></div>
                
                <!-- Background Preview Wrapper -->
                <div id="editor_bg_wrapper" class="rounded-2xl transition-all duration-300 p-0 border border-slate-100 bg-slate-50/50">
                    <textarea id="post_content" rows="5" placeholder="Chia sẻ thông tin, kinh nghiệm học tập hoặc đời sống tại Nhật Bản..." class="w-full bg-transparent text-[14.5px] placeholder-slate-400 focus:outline-none resize-none p-4 leading-relaxed transition-all duration-300"></textarea>
                </div>
                
                <!-- Background Gradient Preset Selector -->
                <div class="flex flex-wrap items-center gap-2 mt-4 select-none">
                    <span class="text-[11px] font-bold uppercase text-slate-400 mr-1">Hình nền:</span>
                    <button class="w-5 h-5 rounded-full border border-slate-350 bg-white flex items-center justify-center text-[10px] text-slate-400 hover:scale-110 active:scale-95 transition-all btn-bg-preset active ring-2 ring-primary ring-offset-1" data-bg="" title="Mặc định">
                        <i class="bi bi-slash-circle text-[8px]"></i>
                    </button>
                    <button class="w-5 h-5 rounded-full bg-threads-dark hover:scale-110 active:scale-95 transition-all btn-bg-preset" data-bg="bg-threads-dark" title="Tối giản"></button>
                    <button class="w-5 h-5 rounded-full bg-threads-sunrise hover:scale-110 active:scale-95 transition-all btn-bg-preset" data-bg="bg-threads-sunrise" title="Bình minh"></button>
                    <button class="w-5 h-5 rounded-full bg-threads-neon hover:scale-110 active:scale-95 transition-all btn-bg-preset" data-bg="bg-threads-neon" title="Neon"></button>
                    <button class="w-5 h-5 rounded-full bg-threads-ocean hover:scale-110 active:scale-95 transition-all btn-bg-preset" data-bg="bg-threads-ocean" title="Đại dương"></button>
                    <button class="w-5 h-5 rounded-full bg-threads-sakura hover:scale-110 active:scale-95 transition-all btn-bg-preset" data-bg="bg-threads-sakura" title="Sakura"></button>
                    <button class="w-5 h-5 rounded-full bg-threads-lavender hover:scale-110 active:scale-95 transition-all btn-bg-preset" data-bg="bg-threads-lavender" title="Lavender"></button>
                </div>

                <!-- Attachment Image Preview -->
                <div id="image_preview_container" class="hidden relative mt-4 rounded-2xl overflow-hidden border border-slate-100 max-h-56 shadow-xs select-none">
                    <img id="image_preview" src="" alt="Xem trước ảnh đính kèm" class="w-full h-full object-cover">
                    <button id="btn_remove_image" class="absolute top-3 right-3 bg-black/60 hover:bg-black/80 text-white rounded-full p-1.5 w-7 h-7 flex items-center justify-center transition-colors">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>

                <!-- Actions Footer -->
                <div class="flex flex-wrap justify-between items-center mt-5 pt-4 border-t border-slate-100 gap-3">
                    <div class="flex items-center gap-3">
                        <button id="btn_trigger_file" class="text-slate-400 hover:text-primary transition-colors p-1" title="Đính kèm ảnh">
                            <i class="bi bi-image text-xl"></i>
                        </button>
                        <input type="file" id="file_input" class="hidden" accept="image/*">
                        
                        <select id="post_tag" class="bg-slate-50 border border-slate-200 text-[11px] font-bold text-slate-500 rounded-full px-3.5 py-1.5 focus:outline-none focus:border-slate-350 transition-colors">
                            <option value="">Chọn chủ đề</option>
                            <option value="#tuyensinh">#tuyensinh</option>
                            <option value="#hocphi">#hocphi</option>
                            <option value="#visanhat">#visanhat</option>
                            <option value="#vieclam">#vieclam</option>
                            <option value="#nhatngu">#nhatngu</option>
                            <option value="#cuocsong">#cuocsong</option>
                        </select>
                    </div>
                    <button id="btn_post_question" class="bg-primary text-white font-bold text-xs px-6 py-3 rounded-full hover:bg-slate-800 active:scale-95 transition-all shadow-sm">
                        Đăng bài viết
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Floating Alert Container for Share notifications -->
<div id="floating_notifications" class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[100] pointer-events-none flex flex-col gap-2"></div>

<!-- Q&A Front-end Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const feedContainer = document.getElementById('qa_feed_container');
    const filtersBar = document.getElementById('tag_filters_bar');
    const paginationContainer = document.getElementById('qa_pagination_container');
    const adminCreatorCard = document.getElementById('admin_creator_card');
    
    // Auth context details
    const isLoggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;
    const isAdmin = <?= $is_admin ? 'true' : 'false' ?>;
    const currentUserName = <?= json_encode($current_user_name) ?>;

    // Load URL state parameter (qid)
    const getUrlQid = () => {
        const params = new URLSearchParams(window.location.search);
        return params.get('qid') ? parseInt(params.get('qid')) : null;
    };

    let selectedTag = '';
    let currentBgStyle = '';
    let selectedFile = null;
    let viewingQuestionId = getUrlQid();
    let currentPage = 1;
    let totalPages = 1;

    // Create a beautiful avatar dynamically using a unique gradient
    function generateAvatarHtml(name, size = '10') {
        const initial = name ? name.trim().charAt(0).toUpperCase() : 'C';
        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }
        
        const gradients = [
            'linear-gradient(135deg, #0d243e 0%, #345b7b 100%)', // primary darks
            'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)', // blues
            'linear-gradient(135deg, #10b981 0%, #047857 100%)', // emeralds
            'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)', // indigos
            'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)', // violets
            'linear-gradient(135deg, #f59e0b 0%, #b45309 100%)', // ambers
            'linear-gradient(135deg, #f43f5e 0%, #be123c 100%)', // roses
            'linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%)'  // skies
        ];
        const gradient = gradients[Math.abs(hash) % gradients.length];
        
        let sizeClass = 'w-10 h-10 text-sm';
        if (size === '12') sizeClass = 'w-12 h-12 text-base';
        if (size === '8') sizeClass = 'w-8 h-8 text-[11px]';
        
        return `<div class="${sizeClass} rounded-full text-white font-extrabold flex items-center justify-center shadow-xs border border-white/50 uppercase select-none flex-shrink-0" style="background: ${gradient};">
            <span>${initial}</span>
        </div>`;
    }

    // Load initial user avatars on UI
    if (isLoggedIn) {
        const sidebarAvatar = document.getElementById('sidebar_user_avatar');
        if (sidebarAvatar) sidebarAvatar.innerHTML = generateAvatarHtml(currentUserName, '12');

        const adminMainAvatar = document.getElementById('admin_main_avatar');
        if (adminMainAvatar) adminMainAvatar.innerHTML = generateAvatarHtml(currentUserName, '10');
        
        const modalAvatar = document.getElementById('modal_user_avatar');
        if (modalAvatar) modalAvatar.innerHTML = generateAvatarHtml(currentUserName, '10');
    } else {
        const sidebarAvatar = document.getElementById('sidebar_user_avatar');
        if (sidebarAvatar) sidebarAvatar.innerHTML = `<div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 flex-shrink-0"><i class="bi bi-person-fill text-xl"></i></div>`;
    }

    // HTML Skeleton loaders
    function showFeedSkeleton() {
        let html = '';
        for (let i = 0; i < 3; i++) {
            html += `
            <div class="bg-white rounded-[2rem] border border-slate-100/80 p-6 md:p-8 space-y-4 animate-pulse">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-200"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-slate-200 rounded w-1/4"></div>
                        <div class="h-2.5 bg-slate-200 rounded w-12"></div>
                    </div>
                </div>
                <div class="space-y-2.5 pt-2">
                    <div class="h-3 bg-slate-200 rounded w-full"></div>
                    <div class="h-3 bg-slate-200 rounded w-5/6"></div>
                    <div class="h-3 bg-slate-200 rounded w-2/3"></div>
                </div>
                <div class="h-px bg-slate-100 my-4"></div>
                <div class="flex justify-between items-center pt-2">
                    <div class="flex gap-4">
                        <div class="h-6 bg-slate-100 rounded-full w-20"></div>
                        <div class="h-6 bg-slate-100 rounded-full w-24"></div>
                    </div>
                    <div class="h-6 bg-slate-100 rounded-full w-16"></div>
                </div>
            </div>
            `;
        }
        feedContainer.innerHTML = html;
    }

    // Role badge
    function getRoleBadgeHtml(authorName) {
        const name = authorName.toLowerCase();
        if (name.includes('admin') || name.includes('editor') || name.includes('bright education') || name.includes('victoria universal')) {
            return `<span class="inline-flex items-center gap-1 bg-slate-900 text-[10px] font-black text-white px-2 py-0.5 rounded-full uppercase tracking-wider scale-90">
                <i class="bi bi-shield-check text-orange-400 text-[10px]"></i> Admin
            </span>`;
        }
        return `<span class="bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-500 px-2 py-0.5 rounded-full uppercase tracking-wider scale-90">Thành viên</span>`;
    }

    // Time ago calculator
    function formatTimeAgo(dateStr) {
        const date = new Date(dateStr.replace(' ', 'T'));
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        
        if (diffMins < 1) return 'Vừa xong';
        if (diffMins < 60) return `${diffMins} phút trước`;
        const diffHours = Math.floor(diffMins / 60);
        if (diffHours < 24) return `${diffHours} giờ trước`;
        const diffDays = Math.floor(diffHours / 24);
        if (diffDays < 7) return `${diffDays} ngày trước`;
        return date.toLocaleDateString('vi-VN', { day: 'numeric', month: 'short' });
    }

    // Escape raw HTML strings
    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Show floating alert
    function showNotification(message) {
        const container = document.getElementById('floating_notifications');
        const notif = document.createElement('div');
        notif.className = 'bg-primary text-white text-xs font-bold px-5 py-3 rounded-full shadow-hard z-50 flex items-center gap-2 animate-bounce pointer-events-auto transition-opacity duration-300';
        notif.innerHTML = `<i class="bi bi-check-circle-fill text-emerald-400"></i> ${message}`;
        container.appendChild(notif);
        setTimeout(() => {
            notif.classList.add('opacity-0');
            setTimeout(() => notif.remove(), 300);
        }, 2200);
    }

    // Load and render feed
    function loadFeed() {
        showFeedSkeleton();
        
        // Hide creator card, filter bar and pagination by default if viewing single question
        if (viewingQuestionId) {
            if (filtersBar) filtersBar.classList.add('hidden');
            if (adminCreatorCard) adminCreatorCard.classList.add('hidden');
            if (paginationContainer) paginationContainer.classList.add('hidden');
            
            const params = new URLSearchParams();
            params.append('action', 'get_question');
            params.append('question_id', viewingQuestionId);

            fetch('/api/qa_action', { method: 'POST', body: params })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    feedContainer.innerHTML = '';
                    
                    // Add Back Button
                    const backBar = document.createElement('div');
                    backBar.className = 'mb-6 select-none';
                    backBar.innerHTML = `
                        <button id="btn_back_to_feed" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-black border border-slate-200/80 rounded-full text-xs font-bold transition-all shadow-soft focus:outline-none">
                            <i class="bi bi-arrow-left text-sm"></i>
                            <span>Quay lại danh sách bài viết</span>
                        </button>
                    `;
                    backBar.querySelector('#btn_back_to_feed').addEventListener('click', () => {
                        viewingQuestionId = null;
                        const cleanUrl = window.location.origin + window.location.pathname;
                        window.history.pushState({}, '', cleanUrl);
                        loadFeed();
                    });
                    feedContainer.appendChild(backBar);
                    
                    // Render single post
                    const card = createQuestionCardNode(res.data);
                    
                    // Auto-open comments section
                    const commSec = card.querySelector('.comments-section');
                    const commentBtn = card.querySelector('.comment-toggle-btn');
                    if (commSec) commSec.classList.remove('hidden');
                    if (commentBtn) commentBtn.classList.add('active-comment');
                    
                    feedContainer.appendChild(card);
                    window.scrollTo({ top: feedContainer.offsetTop - 120, behavior: 'smooth' });
                } else {
                    showErrorState();
                }
            })
            .catch(err => {
                console.error('Error loading QA single question:', err);
                showErrorState();
            });
            return;
        }

        // List view mode: show filter bar and creator card
        if (filtersBar) filtersBar.classList.remove('hidden');
        if (adminCreatorCard) adminCreatorCard.classList.remove('hidden');
        
        let url = `/api/qa_action?action=get_feed&page=${currentPage}`;
        if (selectedTag) {
            url += '&tag=' + encodeURIComponent(selectedTag);
        }

        fetch(url)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                const questions = res.data;
                currentPage = res.current_page;
                totalPages = res.total_pages;
                
                renderFeedList(questions);
                renderPagination();
                window.scrollTo({ top: feedContainer.offsetTop - 120, behavior: 'smooth' });
            } else {
                showErrorState();
            }
        })
        .catch(err => {
            console.error('Error loading QA feed:', err);
            showErrorState();
        });
    }

    function showErrorState() {
        feedContainer.innerHTML = `
            <div class="text-center py-16 bg-white rounded-[2rem] border border-slate-100 shadow-soft">
                <i class="bi bi-exclamation-triangle text-3xl text-red-500 mb-3 block"></i>
                <p class="text-slate-600 font-bold text-sm">Đã xảy ra lỗi khi tải luồng thảo luận.</p>
                <button onclick="window.location.reload()" class="mt-4 px-4 py-2 bg-primary text-white text-xs font-bold rounded-full hover:bg-slate-800 transition-all shadow-sm">Thử lại</button>
            </div>
        `;
    }

    function renderFeedList(questions) {
        feedContainer.innerHTML = '';
        
        if (questions.length === 0) {
            feedContainer.innerHTML = `
                <div class="empty-state text-center text-slate-400 py-16 bg-white rounded-[2rem] border border-slate-100 shadow-soft">
                    <i class="bi bi-chat-dots text-5xl text-slate-200 mb-4 block"></i>
                    <p class="text-xs font-bold text-slate-500">Chưa có bài viết chia sẻ nào trong chuyên mục này.</p>
                </div>
            `;
            return;
        }

        questions.forEach(q => {
            const card = createQuestionCardNode(q);
            feedContainer.appendChild(card);
            
            // If this is the newly created card, highlight it
            if (newlyCreatedQid && q.id === newlyCreatedQid) {
                setTimeout(() => {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    card.classList.add('highlight-card');
                    newlyCreatedQid = null; // Clear
                }, 400);
            }
        });
    }

    // Render pagination controls
    function renderPagination() {
        if (!paginationContainer) return;
        paginationContainer.innerHTML = '';
        
        if (viewingQuestionId || totalPages <= 1) {
            paginationContainer.classList.add('hidden');
            return;
        }
        paginationContainer.classList.remove('hidden');

        // Prev page button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'px-4 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 font-bold text-xs disabled:opacity-50 disabled:pointer-events-none transition-all flex items-center gap-1.5 focus:outline-none select-none';
        prevBtn.innerHTML = `<i class="bi bi-chevron-left text-[10px]"></i> Trước`;
        prevBtn.disabled = (currentPage === 1);
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                loadFeed();
            }
        });
        paginationContainer.appendChild(prevBtn);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            if (i === currentPage) {
                pageBtn.className = 'w-9 h-9 rounded-full bg-primary text-white border border-primary font-bold text-xs shadow-sm transition-all focus:outline-none select-none';
            } else {
                pageBtn.className = 'w-9 h-9 rounded-full bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 font-bold text-xs transition-all focus:outline-none select-none';
            }
            pageBtn.textContent = i;
            pageBtn.addEventListener('click', () => {
                currentPage = i;
                loadFeed();
            });
            paginationContainer.appendChild(pageBtn);
        }

        // Next page button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'px-4 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 font-bold text-xs disabled:opacity-50 disabled:pointer-events-none transition-all flex items-center gap-1.5 focus:outline-none select-none';
        nextBtn.innerHTML = `Sau <i class="bi bi-chevron-right text-[10px]"></i>`;
        nextBtn.disabled = (currentPage === totalPages);
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                loadFeed();
            }
        });
        paginationContainer.appendChild(nextBtn);
    }

    // HTML Category Badge
    function getTagBadgeHtml(tag) {
        if (!tag) return '';
        const cleanTag = tag.trim().toLowerCase();
        const colors = {
            '#tuyensinh': 'bg-orange-50 text-orange-600 border border-orange-100',
            '#hocphi': 'bg-emerald-50 text-emerald-600 border border-emerald-100',
            '#visanhat': 'bg-blue-50 text-blue-600 border border-blue-100',
            '#vieclam': 'bg-amber-50 text-amber-600 border border-amber-100',
            '#nhatngu': 'bg-indigo-50 text-indigo-600 border border-indigo-100',
            '#cuocsong': 'bg-rose-50 text-rose-600 border border-rose-100'
        };
        const style = colors[cleanTag] || 'bg-slate-50 text-slate-500 border border-slate-200';
        return `<span class="inline-block text-[11px] font-bold px-2.5 py-1 rounded-full ${style} select-none cursor-pointer tag-click-filter" data-tag="${tag}">${tag}</span>`;
    }

    // Build the question card DOM Node
    function createQuestionCardNode(q) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-[2rem] border border-slate-100/80 shadow-soft p-5 sm:p-6 md:p-8 flex flex-col relative transition-all duration-300 hover:shadow-medium';
        card.dataset.id = q.id;

        // Tags and header structure
        const tagHtml = getTagBadgeHtml(q.tags);
        const avatarHtml = generateAvatarHtml(q.author_name, '10');
        const roleBadgeHtml = getRoleBadgeHtml(q.author_name);
        const timeAgo = formatTimeAgo(q.created_at);

        // Render card content
        let contentBody = '';
        if (q.bg_style) {
            contentBody = `
                <div class="${q.bg_style} text-white font-extrabold text-base sm:text-lg flex items-center justify-center text-center p-8 rounded-2xl min-h-[180px] shadow-soft leading-relaxed my-4 whitespace-pre-wrap select-text">
                    <span>${escapeHtml(q.content)}</span>
                </div>
            `;
        } else {
            contentBody = `
                <div class="text-[14.5px] text-slate-800 leading-relaxed my-4 whitespace-pre-wrap select-text">${escapeHtml(q.content)}</div>
            `;
        }

        // Attached image representation
        let imageHtml = '';
        if (q.image && !q.bg_style) {
            imageHtml = `
                <div class="mt-2 mb-4 rounded-2xl overflow-hidden border border-slate-100 max-h-96 hover:shadow-soft transition-all duration-300">
                    <img src="/uploads/${q.image}" class="w-full object-cover max-h-96 hover:scale-[1.01] transition-transform duration-300" alt="Ảnh đính kèm">
                </div>
            `;
        }

        // Render Answers
        let answersListHtml = '';
        if (q.answers && q.answers.length > 0) {
            answersListHtml = q.answers.map(ans => {
                const ansAvatar = generateAvatarHtml(ans.author_name, '8');
                const ansBadge = getRoleBadgeHtml(ans.author_name);
                const ansTime = formatTimeAgo(ans.created_at);
                
                return `
                <div class="flex gap-3 py-4 border-b border-slate-50 last:border-0 items-start">
                    <div class="flex-shrink-0 mt-0.5">${ansAvatar}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 mb-1 text-xs">
                            <span class="font-bold text-slate-800">${escapeHtml(ans.author_name)}</span>
                            ${ansBadge}
                            <span class="text-slate-400 font-medium ml-1">${ansTime}</span>
                        </div>
                        <p class="text-[13.5px] text-slate-600 leading-relaxed whitespace-pre-wrap select-text">${escapeHtml(ans.content)}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <button class="flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-red-500 transition-colors btn-like-answer" data-id="${ans.id}">
                                <i class="bi bi-heart-fill text-[9.5px]"></i>
                                <span class="ans-like-count">${ans.likes_count > 0 ? `Thích (${ans.likes_count})` : 'Thích'}</span>
                            </button>
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        } else {
            answersListHtml = `
                <div class="empty-replies text-center py-6 text-slate-400 select-none">
                    <i class="bi bi-chat-dots text-xl mb-1 text-slate-200 block"></i>
                    <p class="text-[11px] font-semibold text-slate-400">Hãy là người đầu tiên đưa ra phản hồi cho bài viết này.</p>
                </div>
            `;
        }

        // Comments Collapsible Section structure
        const answersCount = q.answers ? q.answers.length : 0;
        const commentsBlock = `
            <div class="comments-section hidden border-t border-slate-100 pt-5 mt-4">
                <h4 class="text-xs font-extrabold uppercase tracking-wide text-slate-400 mb-3 select-none">Các bình luận và phản hồi</h4>
                
                <!-- List of replies -->
                <div class="comments-list space-y-1 mb-5">
                    ${answersListHtml}
                </div>

                <!-- Input Reply Form -->
                ${isLoggedIn ? `
                <div class="flex gap-3 pt-4 border-t border-slate-100 items-start">
                    <div class="flex-shrink-0">${generateAvatarHtml(currentUserName, '8')}</div>
                    <div class="flex-1 flex gap-2">
                        <input type="text" class="comment-input-field flex-1 bg-slate-50 hover:bg-slate-100/75 focus:bg-white border border-slate-200/80 rounded-full px-4 py-2 text-[12.5px] placeholder-slate-400 focus:outline-none focus:border-slate-350 transition-all shadow-xs" placeholder="Viết bình luận của bạn..." data-qid="${q.id}">
                        <button class="btn-submit-comment bg-primary text-white hover:bg-slate-800 rounded-full px-5 py-2 text-[11px] font-bold transition-all shadow-sm focus:outline-none focus:scale-95" data-qid="${q.id}">
                            Gửi
                        </button>
                    </div>
                </div>
                ` : `
                <div class="text-center py-3 bg-slate-50 border border-slate-100 rounded-2xl select-none">
                    <span class="text-[11px] font-bold text-slate-500">Bạn cần <a href="/login?redirect=/qa" class="text-primary hover:underline font-extrabold">đăng nhập</a> để tham gia bình luận thảo luận.</span>
                </div>
                `}
            </div>
        `;

        card.innerHTML = `
            <!-- Top Row header -->
            <div class="flex justify-between items-start select-none">
                <div class="flex items-center gap-3">
                    ${avatarHtml}
                    <div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-bold text-sm text-slate-900">${escapeHtml(q.author_name)}</span>
                            ${roleBadgeHtml}
                        </div>
                        <span class="text-[11.5px] text-slate-400 font-medium">${timeAgo}</span>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    ${tagHtml}
                </div>
            </div>

            <!-- Content Area -->
            ${contentBody}
            ${imageHtml}

            <!-- Bottom Row Actions -->
            <div class="flex items-center justify-between mt-4 border-t border-slate-100 pt-3 select-none">
                <div class="flex items-center gap-1">
                    <!-- Like Post -->
                    <button class="social-action-btn like-post-btn" data-id="${q.id}">
                        <i class="bi bi-heart"></i>
                        <span class="q-like-count">${q.likes_count || '0'} Thích</span>
                    </button>
                    <!-- Toggle Comments -->
                    <button class="social-action-btn comment-toggle-btn" data-id="${q.id}">
                        <i class="bi bi-chat"></i>
                        <span class="q-comm-count">${answersCount} Bình luận</span>
                    </button>
                    <!-- View details button (only in list view) -->
                    ${!viewingQuestionId ? `
                    <button class="social-action-btn view-details-btn" data-id="${q.id}">
                        <i class="bi bi-box-arrow-in-up-right"></i>
                        <span>Chi tiết</span>
                    </button>
                    ` : ''}
                </div>
                <!-- Share link -->
                <button class="social-action-btn share-post-btn" data-id="${q.id}" title="Chia sẻ liên kết bài viết">
                    <i class="bi bi-share"></i>
                    <span class="hidden sm:inline">Chia sẻ</span>
                </button>
            </div>

            <!-- Comments Collapsible -->
            ${commentsBlock}
        `;

        // Register action triggers for this card
        // 0. View details trigger
        const viewDetailsBtn = card.querySelector('.view-details-btn');
        if (viewDetailsBtn) {
            viewDetailsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                viewingQuestionId = q.id;
                const shareUrl = window.location.origin + window.location.pathname + '?qid=' + q.id;
                window.history.pushState({ qid: q.id }, '', shareUrl);
                loadFeed();
            });
        }

        // 1. Like Question action
        const likeBtn = card.querySelector('.like-post-btn');
        likeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (!isLoggedIn) {
                window.location.href = '/login?redirect=/qa';
                return;
            }
            const icon = likeBtn.querySelector('i');
            const counter = likeBtn.querySelector('.q-like-count');
            
            icon.className = 'bi bi-heart-fill text-red-500 animate-heart';
            likeBtn.classList.add('liked');
            
            const params = new URLSearchParams();
            params.append('action', 'like_question');
            params.append('question_id', q.id);

            fetch('/api/qa_action', { method: 'POST', body: params })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    counter.textContent = `${res.likes} Thích`;
                }
            });
        });

        // 2. Toggle Comments Section visibility
        const commentBtn = card.querySelector('.comment-toggle-btn');
        const commSec = card.querySelector('.comments-section');
        commentBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !commSec.classList.contains('hidden');
            if (isOpen) {
                commSec.classList.add('hidden');
                commentBtn.classList.remove('active-comment');
            } else {
                commSec.classList.remove('hidden');
                commentBtn.classList.add('active-comment');
                const inp = commSec.querySelector('.comment-input-field');
                if (inp) inp.focus();
            }
        });

        // 3. Like Answer buttons inside card
        card.querySelectorAll('.btn-like-answer').forEach(ansBtn => {
            ansBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!isLoggedIn) {
                    window.location.href = '/login?redirect=/qa';
                    return;
                }
                const ansId = ansBtn.dataset.id;
                const counter = ansBtn.querySelector('.ans-like-count');
                const heart = ansBtn.querySelector('i');
                
                heart.classList.remove('text-slate-400');
                heart.classList.add('text-red-500', 'scale-110');
                
                const params = new URLSearchParams();
                params.append('action', 'like_answer');
                params.append('answer_id', ansId);

                fetch('/api/qa_action', { method: 'POST', body: params })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        counter.textContent = `Thích (${res.likes})`;
                    }
                });
            });
        });

        // 4. Submit new comment trigger
        const submitCommBtn = card.querySelector('.btn-submit-comment');
        const commInputField = card.querySelector('.comment-input-field');
        
        const executeSubmitComment = () => {
            const content = commInputField.value.trim();
            if (!content) return;

            submitCommBtn.disabled = true;
            submitCommBtn.textContent = 'Gửi...';

            const params = new URLSearchParams();
            params.append('action', 'post_answer');
            params.append('question_id', q.id);
            params.append('content', content);

            fetch('/api/qa_action', {
                method: 'POST',
                body: params
            })
            .then(res => res.json())
            .then(res => {
                submitCommBtn.disabled = false;
                submitCommBtn.textContent = 'Gửi';
                if (res.status === 'success') {
                    commInputField.value = '';
                    // Reload this card's details silently to update comments listing
                    refreshCardSilent(q.id, card);
                }
            })
            .catch(() => {
                submitCommBtn.disabled = false;
                submitCommBtn.textContent = 'Gửi';
            });
        };

        if (submitCommBtn && commInputField) {
            submitCommBtn.addEventListener('click', executeSubmitComment);
            commInputField.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    executeSubmitComment();
                }
            });
        }

        // 5. Share link handler
        const shareBtn = card.querySelector('.share-post-btn');
        shareBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const shareUrl = window.location.origin + window.location.pathname + '?qid=' + q.id;
            navigator.clipboard.writeText(shareUrl).then(() => {
                showNotification('Đã sao chép liên kết chia sẻ!');
            });
        });

        // 6. Direct click tag badges on card to filter
        card.querySelectorAll('.tag-click-filter').forEach(badge => {
            badge.addEventListener('click', (e) => {
                e.stopPropagation();
                const filterTag = badge.dataset.tag;
                const matchBtn = Array.from(document.querySelectorAll('.tag-filter-btn'))
                                     .find(b => b.dataset.tag === filterTag);
                if (matchBtn) {
                    matchBtn.click();
                } else {
                    selectedTag = filterTag;
                    loadFeed();
                }
            });
        });

        return card;
    }

    // Refresh details of a single card after actions
    function refreshCardSilent(qid, cardNode) {
        const params = new URLSearchParams();
        params.append('action', 'get_question');
        params.append('question_id', qid);

        fetch('/api/qa_action', {
            method: 'POST',
            body: params
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                const refreshedNode = createQuestionCardNode(res.data);
                // Keep the comments expanded
                refreshedNode.querySelector('.comments-section').classList.remove('hidden');
                refreshedNode.querySelector('.comment-toggle-btn').classList.add('active-comment');
                
                // Replace old node in DOM
                cardNode.replaceWith(refreshedNode);
            }
        });
    }

    // Tag filter triggers at top
    document.querySelectorAll('.tag-filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            document.querySelectorAll('.tag-filter-btn').forEach(b => {
                b.className = 'tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 bg-white text-slate-600 border-slate-200 hover:bg-slate-50';
            });

            this.className = 'tag-filter-btn px-5 py-2.5 rounded-full text-[13px] font-bold border whitespace-nowrap transition-all duration-200 active bg-primary text-white border-primary shadow-sm';
            
            selectedTag = this.dataset.tag;
            currentPage = 1; // Reset to page 1 on tag switch
            
            // Remove qid parameter from browser history on new tag select
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.pushState({}, '', cleanUrl);

            loadFeed();
        });
    });

    // Admin Popup Modal functionality
    if (isAdmin) {
        const postModal = document.getElementById('post_modal');
        const postModalCard = document.getElementById('post_modal_card');
        const btnOpenPostModal = document.getElementById('btn_open_post_modal');
        const btnClosePostModal = document.getElementById('btn_close_post_modal');
        const postModalBackdrop = document.getElementById('post_modal_backdrop');
        
        const btnPost = document.getElementById('btn_post_question');
        const postContent = document.getElementById('post_content');
        const postTag = document.getElementById('post_tag');
        const fileInput = document.getElementById('file_input');
        const btnTriggerFile = document.getElementById('btn_trigger_file');
        const imagePreviewContainer = document.getElementById('image_preview_container');
        const imagePreview = document.getElementById('image_preview');
        const btnRemoveImage = document.getElementById('btn_remove_image');
        const editorBgWrapper = document.getElementById('editor_bg_wrapper');

        const showModal = () => {
            postModal.classList.remove('hidden');
            setTimeout(() => {
                postModalCard.classList.remove('scale-95', 'opacity-0');
                postModalCard.classList.add('scale-100', 'opacity-100');
            }, 50);
        };

        const hideModal = () => {
            postModalCard.classList.remove('scale-100', 'opacity-100');
            postModalCard.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                postModal.classList.add('hidden');
            }, 200);
        };

        if (btnOpenPostModal) btnOpenPostModal.addEventListener('click', showModal);
        if (btnClosePostModal) btnClosePostModal.addEventListener('click', hideModal);
        if (postModalBackdrop) postModalBackdrop.addEventListener('click', hideModal);

        // Color gradient preset preview triggers
        document.querySelectorAll('.btn-bg-preset').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const bgClass = this.dataset.bg;
                
                editorBgWrapper.className = 'rounded-2xl transition-all duration-300 p-0 border border-slate-100';
                postContent.className = 'w-full bg-transparent text-[14.5px] placeholder-slate-400 focus:outline-none resize-none p-4 leading-relaxed transition-all duration-300';
                
                document.querySelectorAll('.btn-bg-preset').forEach(b => b.classList.remove('ring-2', 'ring-primary', 'ring-offset-1'));
                this.classList.add('ring-2', 'ring-primary', 'ring-offset-1');

                if (bgClass) {
                    editorBgWrapper.classList.add(bgClass, 'p-6', 'min-h-[180px]', 'flex', 'items-center', 'justify-center', 'text-center');
                    postContent.classList.add('text-white', 'font-extrabold', 'text-base', 'sm:text-lg', 'text-center', 'placeholder-white/60');
                    currentBgStyle = bgClass;
                    if (btnRemoveImage) btnRemoveImage.click(); // Gradients do not support attachments in design
                } else {
                    editorBgWrapper.classList.add('bg-slate-50/50');
                    currentBgStyle = '';
                }
            });
        });

        // Image attach handlers
        if (btnTriggerFile && fileInput) {
            btnTriggerFile.addEventListener('click', (e) => {
                e.preventDefault();
                fileInput.click();
            });

            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                if (file) {
                    selectedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.classList.remove('hidden');
                        
                        // Switch content template back to standard white (no gradient style supported with images)
                        const defaultBgBtn = document.querySelector('.btn-bg-preset[data-bg=""]');
                        if (defaultBgBtn) defaultBgBtn.click();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (btnRemoveImage) {
            btnRemoveImage.addEventListener('click', (e) => {
                e.preventDefault();
                selectedFile = null;
                fileInput.value = '';
                imagePreview.src = '';
                imagePreviewContainer.classList.add('hidden');
            });
        }

        // Post creation request
        if (btnPost) {
            btnPost.addEventListener('click', () => {
                const content = postContent.value.trim();
                const tagValue = postTag ? postTag.value : '';

                if (!content) {
                    alert('Nội dung không được để trống.');
                    return;
                }

                btnPost.disabled = true;
                btnPost.textContent = 'Đang đăng tải...';

                const formData = new FormData();
                formData.append('action', 'post_question');
                formData.append('content', content);
                formData.append('tags', tagValue);
                formData.append('bg_style', currentBgStyle);
                if (selectedFile) {
                    formData.append('image', selectedFile);
                }

                fetch('/api/qa_action', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    btnPost.disabled = false;
                    btnPost.textContent = 'Đăng bài viết';
                    if (res.status === 'success') {
                        postContent.value = '';
                        if (postTag) postTag.value = '';
                        
                        const defaultBgBtn = document.querySelector('.btn-bg-preset[data-bg=""]');
                        if (defaultBgBtn) defaultBgBtn.click();
                        if (btnRemoveImage) btnRemoveImage.click();
                        
                        hideModal();
                        
                        // Reload Feed & Highlight new post
                        currentPage = 1;
                        newlyCreatedQid = res.id;
                        loadFeed();
                    } else {
                        alert(res.message);
                    }
                })
                .catch(err => {
                    console.error('Error posting question:', err);
                    btnPost.disabled = false;
                    btnPost.textContent = 'Đăng bài viết';
                });
            });
        }
    }

    // Initialize initial feed load
    let newlyCreatedQid = null;
    loadFeed();

    // Watch browser history popstate to reload correctly
    window.addEventListener('popstate', () => {
        viewingQuestionId = getUrlQid();
        loadFeed();
    });
});
</script>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
