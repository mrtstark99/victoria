<?php
/**
 * Agent Link Auditor — Full Internal Link Audit Logic
 * Extracted from AgentLinkingProcessor for single-responsibility.
 */

namespace Controllers\Agent;

class AgentLinkAuditor {

    /**
     * Perform a full internal link audit across all published posts.
     */
    public function audit(): array {
        $db = \Database::getInstance();

        $stmt = $db->query("SELECT id, title, slug, content, views FROM posts WHERE status = 'published' ORDER BY id");
        $allPosts = $stmt->fetchAll();

        // Build link maps
        $linkMap = [];    // post_id => [linked_to_post_ids]
        $inboundMap = []; // post_id => count of posts linking to it
        $outboundMap = []; // post_id => count of outbound internal links

        $slugToId = [];
        foreach ($allPosts as $p) {
            $slugToId[$p['slug']] = (int)$p['id'];
            $linkMap[(int)$p['id']] = [];
            $inboundMap[(int)$p['id']] = 0;
            $outboundMap[(int)$p['id']] = 0;
        }

        foreach ($allPosts as $p) {
            $postId = (int)$p['id'];
            if (preg_match_all('/href=["\']\/blog\/([^"\'#?]+)/i', $p['content'] ?? '', $matches)) {
                foreach ($matches[1] as $linkedSlug) {
                    $linkedSlug = trim($linkedSlug, '/');
                    if (isset($slugToId[$linkedSlug]) && $slugToId[$linkedSlug] !== $postId) {
                        $targetId = $slugToId[$linkedSlug];
                        $linkMap[$postId][] = $targetId;
                        $inboundMap[$targetId] = ($inboundMap[$targetId] ?? 0) + 1;
                        $outboundMap[$postId] = ($outboundMap[$postId] ?? 0) + 1;
                    }
                }
            }
        }

        // Classify pages
        $orphanPages = [];
        $overLinked = [];
        $underLinked = [];

        foreach ($allPosts as $p) {
            $pid = (int)$p['id'];
            $inbound = $inboundMap[$pid] ?? 0;
            $outbound = $outboundMap[$pid] ?? 0;

            if ($inbound === 0) {
                $orphanPages[] = [
                    'id' => $pid,
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'url' => '/blog/' . $p['slug'],
                    'outbound_links' => $outbound,
                    'views' => (int)($p['views'] ?? 0),
                    'severity' => 'high',
                    'recommendation' => 'Trang mồ côi — không có bài nào trỏ đến. Cần thêm internal link từ các bài liên quan.'
                ];
            }

            if ($outbound === 0) {
                $underLinked[] = [
                    'id' => $pid,
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'url' => '/blog/' . $p['slug'],
                    'inbound_links' => $inbound,
                    'severity' => 'medium',
                    'recommendation' => 'Bài viết không chứa internal link nào. Nên thêm 2-5 link đến bài viết liên quan.'
                ];
            }

            if ($outbound > 15) {
                $overLinked[] = [
                    'id' => $pid,
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'url' => '/blog/' . $p['slug'],
                    'outbound_links' => $outbound,
                    'severity' => 'low',
                    'recommendation' => "Bài viết chứa {$outbound} internal link — quá nhiều có thể gây loãng link juice."
                ];
            }
        }

        return [
            'total_published_posts' => count($allPosts),
            'orphan_pages' => $orphanPages,
            'under_linked_pages' => $underLinked,
            'over_linked_pages' => $overLinked,
            'summary' => [
                'orphan_count' => count($orphanPages),
                'under_linked_count' => count($underLinked),
                'over_linked_count' => count($overLinked),
                'avg_inbound_links' => count($allPosts) > 0 ? round(array_sum($inboundMap) / count($allPosts), 1) : 0,
                'avg_outbound_links' => count($allPosts) > 0 ? round(array_sum($outboundMap) / count($allPosts), 1) : 0
            ]
        ];
    }
}
