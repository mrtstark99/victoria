<?php
/**
 * Brand Social Media Links Accordion Card Partial
 */
?>
<!-- Card 3: Social Media Links -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleBrandAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Kênh Mạng xã hội (Social Media Links)</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        
        <!-- Facebook -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="social_facebook" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Facebook Fanpage / Profile URL
            </label>
            <input 
                type="url" 
                id="social_facebook" 
                name="social_facebook" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>" 
                placeholder="https://facebook.com/..."
                oninput="updateBrandLivePreview()"
            >
        </div>

        <!-- Twitter / X -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="social_twitter" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Twitter / X Profile URL
            </label>
            <input 
                type="url" 
                id="social_twitter" 
                name="social_twitter" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['social_twitter'] ?? ''); ?>" 
                placeholder="https://twitter.com/..."
                oninput="updateBrandLivePreview()"
            >
        </div>

        <!-- GitHub -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="social_github" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                GitHub Profile / Organization URL
            </label>
            <input 
                type="url" 
                id="social_github" 
                name="social_github" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['social_github'] ?? ''); ?>" 
                placeholder="https://github.com/..."
                oninput="updateBrandLivePreview()"
            >
        </div>

        <!-- LinkedIn -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="social_linkedin" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                LinkedIn Profile / Company URL
            </label>
            <input 
                type="url" 
                id="social_linkedin" 
                name="social_linkedin" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['social_linkedin'] ?? ''); ?>" 
                placeholder="https://linkedin.com/in/..."
                oninput="updateBrandLivePreview()"
            >
        </div>

        <!-- YouTube -->
        <div class="form-group" style="margin-bottom: 0;">
            <label for="social_youtube" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                YouTube Channel URL
            </label>
            <input 
                type="url" 
                id="social_youtube" 
                name="social_youtube" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['social_youtube'] ?? ''); ?>" 
                placeholder="https://youtube.com/@..."
                oninput="updateBrandLivePreview()"
            >
        </div>

    </div>
</div>
