<?php
/**
 * @file database/migrate_consultations_and_qa.php
 * @description Migration script creating consultation bookings, slots, and community groups.
 *
 * Layer:
 * - Database / Migration
 *
 * Responsibilities:
 * - Execute DDL for consultation_slots, consultation_bookings, and community_groups.
 * - Seed initial default consultation slots and community groups if their tables are empty.
 *
 * Security:
 * - Parameterized inserts and schema isolation.
 *
 * Dependencies:
 * - config/config.php, config/database.php
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

echo "==> Running Victoria Consultation Migration...\n";

$db = Database::getInstance();

$db->exec("
CREATE TABLE IF NOT EXISTS consultation_slots (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    type                TEXT NOT NULL DEFAULT 'group' CHECK(type IN ('group','individual')),
    title               TEXT NOT NULL,
    description         TEXT,
    zoom_link           TEXT,
    scheduled_date      TEXT NOT NULL,
    time_start          TEXT NOT NULL,
    time_end            TEXT NOT NULL,
    max_participants    INTEGER NOT NULL DEFAULT 30,
    current_participants INTEGER NOT NULL DEFAULT 0,
    is_free             INTEGER NOT NULL DEFAULT 1,
    status              TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','full','cancelled','completed')),
    created_by          INTEGER,
    created_at          TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at          TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS consultation_bookings (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    slot_id         INTEGER,
    name            TEXT NOT NULL,
    email           TEXT NOT NULL,
    phone           TEXT NOT NULL,
    japanese_level  TEXT,
    booking_type    TEXT NOT NULL DEFAULT 'individual' CHECK(booking_type IN ('group','individual')),
    topic           TEXT,
    preferred_date  TEXT,
    preferred_time  TEXT,
    message         TEXT,
    status          TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','confirmed','cancelled','completed','no_show')),
    zoom_link       TEXT,
    admin_notes     TEXT,
    ip_address      TEXT,
    created_at      TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at      TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (slot_id) REFERENCES consultation_slots(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_consultation_slots_date   ON consultation_slots(scheduled_date);
CREATE INDEX IF NOT EXISTS idx_consultation_slots_status ON consultation_slots(status);
CREATE INDEX IF NOT EXISTS idx_consultation_slots_type   ON consultation_slots(type);
CREATE INDEX IF NOT EXISTS idx_consultation_bookings_slot   ON consultation_bookings(slot_id);
CREATE INDEX IF NOT EXISTS idx_consultation_bookings_status ON consultation_bookings(status);
CREATE INDEX IF NOT EXISTS idx_consultation_bookings_type   ON consultation_bookings(booking_type);

CREATE TABLE IF NOT EXISTS community_groups (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    platform       TEXT NOT NULL DEFAULT 'facebook' CHECK(platform IN ('facebook','zalo','youtube','telegram','other')),
    name           TEXT NOT NULL,
    description    TEXT,
    url            TEXT NOT NULL,
    member_count   TEXT,
    display_order  INTEGER NOT NULL DEFAULT 0,
    status         TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','inactive')),
    image          TEXT,
    created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);
CREATE INDEX IF NOT EXISTS idx_community_groups_status   ON community_groups(status);
CREATE INDEX IF NOT EXISTS idx_community_groups_platform ON community_groups(platform);
CREATE INDEX IF NOT EXISTS idx_community_groups_order    ON community_groups(display_order);

");

// Check if slots exist
$slotsCount = (int)$db->query("SELECT COUNT(*) FROM consultation_slots")->fetchColumn();
if ($slotsCount === 0) {
    echo "==> Seeding consultation slots...\n";
    $stmt = $db->prepare("
        INSERT INTO consultation_slots (type, title, description, scheduled_date, time_start, time_end, max_participants, current_participants, is_free, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $slots = [
        ['group', 'Định Hướng Du Học & Chọn Trường Nhật Ngữ 2026', 'Buổi chia sẻ trực tuyến cùng đại diện trường Tokyo & Osaka về lộ trình du học tiết kiệm.', date('Y-m-d', strtotime('+3 days')), '19:30', '21:00', 50, 12, 1, 'active'],
        ['group', 'Bí Quyết Săn Học Bổng & Phỏng Vấn COE Tỉ Lệ Đỗ 100%', 'Chiến lược chuẩn bị hồ sơ tài chính và luyện phỏng vấn trực tiếp cùng chuyên gia.', date('Y-m-d', strtotime('+7 days')), '14:00', '15:30', 30, 8, 1, 'active'],
        ['individual', 'Tư Vấn 1-1 Chuyên Sâu Cùng Trưởng Đại Diện', 'Tư vấn cá nhân hóa lộ trình, ngành học và việc làm sau tốt nghiệp.', date('Y-m-d', strtotime('+2 days')), '10:00', '11:00', 1, 0, 1, 'active']
    ];
    foreach ($slots as $s) {
        $stmt->execute($s);
    }
}

// Check community groups
$groupsCount = (int)$db->query("SELECT COUNT(*) FROM community_groups")->fetchColumn();
if ($groupsCount === 0) {
    echo "==> Seeding community groups...\n";
    $stmt = $db->prepare("
        INSERT INTO community_groups (platform, name, description, url, member_count, display_order, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $groups = [
        ['facebook', 'Cộng Đồng Du Học Sinh Victoria Universal Nhật Bản', 'Nơi kết nối du học sinh, chia sẻ nhà ở, việc làm baito và kinh nghiệm sống.', 'https://facebook.com/Tuvanduhocvictoriauniversal', '12.500+', 1, 'active'],
        ['zalo', 'Nhóm Zalo Hỗ Trợ Hồ Sơ COE Kỳ Tháng 4 & Tháng 10', 'Hỏi đáp nhanh cùng ban chuyên môn xử lý hồ sơ cục xuất nhập cảnh Nhật Bản.', 'https://zalo.me/g/brightedu', '1.800+', 2, 'active']
    ];
    foreach ($groups as $g) {
        $stmt->execute($g);
    }
}

echo "==> Migration completed successfully!\n";
