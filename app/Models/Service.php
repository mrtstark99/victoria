<?php
/**
 * @file app/Models/Service.php
 * @description Service model for study abroad packages and consulting programs.
 *
 * Layer:
 * - Domain / Persistence Model
 *
 * Responsibilities:
 * - Query active service packages for public frontend presentation.
 * - Retrieve individual service details by ID or slug.
 * - Support administration CRUD workflows for services.
 *
 * Security:
 * - All queries use parameterized PDO prepared statements to eliminate SQL injection.
 *
 * Dependencies:
 * - Database singleton for SQLite PDO connection.
 *
 * Constraints:
 * - Keep this file focused on a single responsibility.
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

namespace Models;

use Database;
use PDO;

class Service {
    /**
     * Retrieve all active services for frontend display.
     *
     * @return array
     */
    /**
     * Alias for getActive().
     *
     * @return array
     */
    public static function getAllActive() {
        return self::getActive();
    }

    /**
     * Alias for findBySlug().
     *
     * @param string $slug
     * @return array|null
     */
    public static function getBySlug($slug) {
        return self::findBySlug($slug);
    }

    public static function getActive() {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM services 
            WHERE status = 'active' 
            ORDER BY display_order ASC, id ASC
        ");
        $stmt->execute();
        return array_map([self::class, 'attachPackages'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Retrieve all services for administrative dashboard with pagination.
     *
     * @param int $page
     * @param int $perPage
     * @param string $search
     * @return array
     */
    public static function getPaginated($page = 1, $perPage = 20, $search = '') {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(title LIKE ? OR name LIKE ? OR description LIKE ?)";
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $db->prepare("SELECT COUNT(*) FROM services $whereClause");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $query = "
            SELECT * FROM services 
            $whereClause 
            ORDER BY display_order ASC, id ASC 
            LIMIT $perPage OFFSET $offset
        ";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $items = array_map([self::class, 'attachPackages'], $stmt->fetchAll(PDO::FETCH_ASSOC));

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int)ceil($total / $perPage)
        ];
    }

    /**
     * Find service by primary ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? self::attachPackages($result) : null;
    }

    /**
     * Find service by unique URL slug.
     *
     * @param string $slug
     * @return array|null
     */
    public static function findBySlug($slug) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM services WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? self::attachPackages($result) : null;
    }

    public static function findRedirectTarget(string $oldSlug): ?string {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT s.slug FROM service_slug_redirects r JOIN services s ON s.id = r.service_id WHERE r.old_slug = ? LIMIT 1");
        $stmt->execute([$oldSlug]);
        $slug = $stmt->fetchColumn();
        return $slug === false ? null : (string)$slug;
    }

    public static function validateEnglishSlug(string $slug): string {
        $slug = trim($slug);
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new \InvalidArgumentException('Slug must use lowercase English letters, numbers, and single hyphens only.');
        }
        return $slug;
    }

    /**
     * Create a new service record.
     *
     * @param array $data
     * @return int Inserted ID
     */
    public static function create(array $data) {
        $data['slug'] = self::validateEnglishSlug((string)($data['slug'] ?? ''));
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO services (name, title, slug, description, content, icon, price, packages_json, display_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'] ?? '',
            $data['title'],
            $data['slug'],
            $data['description'] ?? '',
            $data['content'] ?? '',
            $data['icon'] ?? 'bi-briefcase',
            (float)($data['price'] ?? 0),
            json_encode(self::normalizePackages($data['packages'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            (int)($data['display_order'] ?? 0),
            $data['status'] ?? 'active'
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Update an existing service record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public static function update($id, array $data) {
        $data['slug'] = self::validateEnglishSlug((string)($data['slug'] ?? ''));
        $db = Database::getInstance();
        $oldStmt = $db->prepare("SELECT slug FROM services WHERE id = ? LIMIT 1");
        $oldStmt->execute([(int)$id]);
        $oldSlug = $oldStmt->fetchColumn();
        $stmt = $db->prepare("
            UPDATE services SET 
                name = ?, title = ?, slug = ?, description = ?, content = ?,
                icon = ?, price = ?, packages_json = ?, display_order = ?, status = ?,
                updated_at = datetime('now','localtime')
            WHERE id = ?
        ");
        $updated = $stmt->execute([
            $data['name'] ?? '',
            $data['title'],
            $data['slug'],
            $data['description'] ?? '',
            $data['content'] ?? '',
            $data['icon'] ?? 'bi-briefcase',
            (float)($data['price'] ?? 0),
            json_encode(self::normalizePackages($data['packages'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            (int)($data['display_order'] ?? 0),
            $data['status'] ?? 'active',
            (int)$id
        ]);
        if ($updated && $oldSlug && $oldSlug !== $data['slug']) {
            $redirect = $db->prepare("INSERT OR REPLACE INTO service_slug_redirects (old_slug, service_id) VALUES (?, ?)");
            $redirect->execute([$oldSlug, (int)$id]);
        }
        return $updated;
    }

    /**
     * Delete service by ID.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public static function attachPackages(array $service): array {
        $packages = json_decode((string)($service['packages_json'] ?? '[]'), true);
        $service['packages'] = is_array($packages) ? $packages : [];
        unset($service['packages_json']);
        return $service;
    }

    public static function normalizePackages($packages): array {
        if (!is_array($packages) || !array_is_list($packages) || count($packages) > 12) throw new \InvalidArgumentException('packages must be a list of up to 12 items.');
        $normalized = [];
        foreach ($packages as $package) {
            if (!is_array($package) || !is_string($package['name'] ?? null) || !is_string($package['description'] ?? null) || !is_numeric($package['price'] ?? null) || (float)$package['price'] < 0 || !is_array($package['features'] ?? null) || !array_is_list($package['features']) || count($package['features']) > 20 || (isset($package['featured']) && !is_bool($package['featured']))) throw new \InvalidArgumentException('Each package needs a name, description, non-negative price, and feature list.');
            $name = trim(strip_tags($package['name']));
            $description = trim(strip_tags($package['description']));
            if ($name === '' || mb_strlen($name) > 100 || mb_strlen($description) > 500) throw new \InvalidArgumentException('Package name or description is invalid.');
            $features = [];
            foreach ($package['features'] as $feature) {
                if (!is_string($feature) || mb_strlen($feature) > 240) throw new \InvalidArgumentException('Package features must be strings of up to 240 characters.');
                $feature = trim(strip_tags($feature));
                if ($feature !== '') $features[] = $feature;
            }
            $slug = self::validateEnglishSlug((string)($package['slug'] ?? ''));
            $normalized[] = [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'price' => (float)$package['price'],
                'features' => $features,
                'featured' => (bool)($package['featured'] ?? false),
            ];
        }
        return $normalized;
    }
}
