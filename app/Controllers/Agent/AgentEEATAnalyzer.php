<?php
/**
 * Agent E-E-A-T Analyzer — Content quality scoring for SEO
 * Extracted from AgentContentProcessor for single-responsibility.
 * Analyzes Experience, Expertise, Authoritativeness, Trustworthiness signals.
 */

namespace Controllers\Agent;

class AgentEEATAnalyzer {

    /**
     * Analyze E-E-A-T signals in content and return a detailed report.
     */
    public function analyze(string $content, string $title): array {
        $contentLower = mb_strtolower($content, 'UTF-8');

        // 1. Check for external citations (links to external sources)
        $hasCitations = (bool)preg_match('/
            <a[^>]+href=["\']https?:\/\/(?!.*(' . preg_quote($_SERVER['HTTP_HOST'] ?? 'localhost', '/') . '))/ix', $content);

        // 2. Check for statistics/numbers (pattern: digit + unit/percentage)
        $hasStatistics = (bool)preg_match(
            '/\d+[\.,]?\d*\s*(%|phần trăm|tỷ|triệu|nghìn|percent|billion|million|USD|VND|EUR|lần|users|người dùng)/iu',
            $content
        );

        // 3. Check for expert mentions or experience signals
        $expertPatterns = [
            'theo chuyên gia', 'theo nghiên cứu', 'theo báo cáo', 'theo thống kê',
            'kinh nghiệm', 'trải nghiệm thực tế', 'qua quá trình', 'chúng tôi đã',
            'theo ông', 'theo bà', 'chuyên gia', 'giáo sư', 'tiến sĩ',
            'according to', 'based on research', 'our experience', 'expert',
            'case study', 'thực tiễn cho thấy', 'dữ liệu cho thấy'
        ];
        $hasExpertMention = false;
        foreach ($expertPatterns as $pattern) {
            if (mb_stripos($contentLower, $pattern) !== false) {
                $hasExpertMention = true;
                break;
            }
        }

        // 4. Check for images
        $imageCount = preg_match_all('/<img[^>]+>/i', $content);
        $hasImages = $imageCount > 0;

        // 5. Check for structured content (lists, tables, FAQ)
        $hasStructuredContent = (bool)preg_match('/<(ul|ol|table|details)[^>]*>/i', $content);

        // 6. Check for internal links
        $internalLinkCount = preg_match_all('/href=["\']\/(?!\/)/i', $content);

        // Calculate E-E-A-T score
        $eeatScore = 0;
        if ($hasCitations) $eeatScore += 25;
        if ($hasStatistics) $eeatScore += 20;
        if ($hasExpertMention) $eeatScore += 25;
        if ($hasImages) $eeatScore += 15;
        if ($hasStructuredContent) $eeatScore += 10;
        if ($internalLinkCount >= 2) $eeatScore += 5;

        return [
            'eeat_score' => min(100, $eeatScore),
            'has_citations' => $hasCitations,
            'has_statistics' => $hasStatistics,
            'has_expert_mention' => $hasExpertMention,
            'has_images' => $hasImages,
            'image_count' => $imageCount,
            'has_structured_content' => $hasStructuredContent,
            'internal_link_count' => $internalLinkCount,
            'grade' => $eeatScore >= 70 ? 'A' : ($eeatScore >= 50 ? 'B' : ($eeatScore >= 30 ? 'C' : 'D')),
            'recommendations' => $this->getRecommendations($eeatScore, $hasCitations, $hasStatistics, $hasExpertMention, $hasImages)
        ];
    }

    /**
     * Build actionable improvement recommendations based on missing E-E-A-T signals.
     */
    private function getRecommendations(int $score, bool $citations, bool $stats, bool $expert, bool $images): array {
        $recs = [];
        if (!$citations) $recs[] = 'Thêm 2-3 liên kết tham chiếu đến nguồn uy tín (nghiên cứu, báo cáo, trang chính thức).';
        if (!$stats) $recs[] = 'Bổ sung số liệu thống kê hoặc dữ liệu cụ thể (ví dụ: "tăng 150% organic traffic trong 90 ngày").';
        if (!$expert) $recs[] = 'Thêm trích dẫn chuyên gia, kinh nghiệm thực tế hoặc case study.';
        if (!$images) $recs[] = 'Thêm ít nhất 2-3 hình ảnh minh họa (screenshot, infographic, biểu đồ).';
        if ($score >= 70) $recs[] = '✓ Nội dung đạt tiêu chuẩn E-E-A-T cơ bản. Tiếp tục duy trì và cải thiện.';
        return $recs;
    }
}
