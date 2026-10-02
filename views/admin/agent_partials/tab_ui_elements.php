<?php
/**
 * UI Elements showcase generated from the canonical post element contract.
 */
$postElements = getPostElementLibrary();
?>
<div class="agent-tab-panel" id="tab-main-ui-elements">
    <div class="panel-header-card">
        <div class="panel-header-info">
            <h2 class="panel-title">
                <span>Thư Viện UI Elements Chuẩn Hóa</span>
                <span class="badge badge-info"><?php echo count($postElements); ?> Nhóm Post Ready</span>
            </h2>
            <p class="panel-desc">
                Nội dung bên dưới được tạo trực tiếp từ UI contract mà API và bộ kiểm tra bài viết đang sử dụng.
            </p>
        </div>
        <div class="panel-header-actions">
            <a href="/admin/agent/download-manual?format=zip" class="btn btn-secondary btn-sm" style="text-decoration: none;">
                <span>Tải Skill ZIP</span>
            </a>
        </div>
    </div>

    <div class="ui-elements-container">
        <div class="ui-group-section">
            <div class="ui-group-title">HTML mẫu và class hợp lệ</div>
            <div class="ui-elements-grid">
                <?php foreach ($postElements as $element):
                    $snippetId = 'snippet_element_' . $element['id'];
                    $primaryTemplate = $element['html_templates'][0] ?? '';
                ?>
                    <div class="card ui-element-card">
                        <div class="ui-card-head">
                            <strong><?php echo htmlspecialchars($element['id'] . '. ' . $element['name']); ?></strong>
                            <button type="button" class="btn btn-secondary btn-xs" onclick="copySnippet('<?php echo $snippetId; ?>', this)">Sao chép HTML</button>
                        </div>
                        <div class="ui-card-preview article-content-body">
                            <?php echo sanitizeHtml($primaryTemplate); ?>
                        </div>
                        <div class="ui-class-list">
                            <?php foreach ($element['classes'] as $className): ?>
                                <code>.<?php echo htmlspecialchars($className); ?></code>
                            <?php endforeach; ?>
                        </div>
                        <textarea id="<?php echo $snippetId; ?>" style="display:none;"><?php echo htmlspecialchars($primaryTemplate); ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
