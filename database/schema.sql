-- SQLite database schema for blog system
PRAGMA journal_mode = WAL;
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    full_name TEXT NOT NULL,
    bio TEXT,
    role TEXT NOT NULL DEFAULT 'subscriber' CHECK(role IN ('admin','editor','subscriber')),
    status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','inactive')),
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    excerpt TEXT,
    content TEXT,
    featured_image TEXT,
    category_id INTEGER,
    author_id INTEGER NOT NULL,
    status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft','published','archived','ai_draft','pending_review','approved','scheduled')),
    featured INTEGER NOT NULL DEFAULT 0,
    views INTEGER NOT NULL DEFAULT 0,
    meta_title TEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    custom_schema_json TEXT,
    published_at TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS post_revisions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    slug TEXT NOT NULL,
    excerpt TEXT,
    content TEXT,
    meta_title TEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    author_id INTEGER,
    action TEXT,
    changed_by TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ai_agent_tokens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    token_name TEXT NOT NULL,
    token_hash TEXT NOT NULL UNIQUE,
    permissions TEXT NOT NULL DEFAULT 'seo:read,posts:draft',
    default_author_id INTEGER,
    expires_at TEXT,
    revoked_at TEXT,
    last_ip TEXT,
    last_user_agent TEXT,
    request_count INTEGER DEFAULT 0,
    allowed_ips TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (default_author_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS ai_agent_rate_limits (
    token_id INTEGER NOT NULL,
    minute_bucket TEXT NOT NULL,
    request_count INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (token_id, minute_bucket)
);

CREATE TABLE IF NOT EXISTS api_idempotency_keys (
    idempotency_key TEXT PRIMARY KEY,
    response_payload TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS ai_agent_tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    priority TEXT NOT NULL DEFAULT 'medium' CHECK(priority IN ('urgent','high','medium','low')),
    category TEXT NOT NULL DEFAULT 'Khác',
    cycle_type TEXT NOT NULL DEFAULT 'daily',
    session_slot TEXT NOT NULL DEFAULT 'morning',
    scheduled_date TEXT,
    month_num INTEGER DEFAULT 1,
    week_num INTEGER,
    phase TEXT,
    content TEXT NOT NULL,
    deadline TEXT,
    is_completed INTEGER NOT NULL DEFAULT 0,
    is_ad_hoc INTEGER NOT NULL DEFAULT 0,
    parent_id INTEGER,
    notes TEXT,
    created_by TEXT NOT NULL DEFAULT 'agent' CHECK(created_by IN ('agent','admin')),
    completed_at TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (parent_id) REFERENCES ai_agent_tasks(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS seo_topic_clusters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    planning_month TEXT NOT NULL,
    name TEXT NOT NULL,
    pillar_title TEXT NOT NULL,
    pillar_url TEXT,
    description TEXT,
    status TEXT NOT NULL DEFAULT 'planned' CHECK(status IN ('planned','in_progress','published')),
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    UNIQUE(planning_month, name)
);

CREATE TABLE IF NOT EXISTS seo_keyword_map (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    planning_month TEXT NOT NULL,
    keyword TEXT NOT NULL,
    intent TEXT NOT NULL CHECK(intent IN ('informational','navigational','commercial','transactional')),
    target_url TEXT,
    cluster_id INTEGER,
    content_role TEXT NOT NULL DEFAULT 'satellite' CHECK(content_role IN ('pillar','satellite','standalone')),
    priority TEXT NOT NULL DEFAULT 'medium' CHECK(priority IN ('high','medium','low')),
    status TEXT NOT NULL DEFAULT 'idea' CHECK(status IN ('idea','brief','writing','published')),
    notes TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (cluster_id) REFERENCES seo_topic_clusters(id) ON DELETE SET NULL,
    UNIQUE(planning_month, keyword)
);

CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key TEXT NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type TEXT NOT NULL DEFAULT 'text' CHECK(setting_type IN ('text','json','boolean','number')),
    description TEXT,
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action TEXT NOT NULL,
    table_name TEXT,
    record_id INTEGER,
    old_values TEXT,
    new_values TEXT,
    ip_address TEXT,
    user_agent TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS analytics_cache (
    cache_key  TEXT PRIMARY KEY,
    payload    TEXT NOT NULL,
    expires_at TEXT NOT NULL,
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS page_views (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER,
    url TEXT NOT NULL,
    ip_address TEXT,
    user_agent TEXT,
    referer TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL
);

-- Event-Driven Pipeline tables
CREATE TABLE IF NOT EXISTS agent_event_hooks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_name TEXT NOT NULL,
    hook_action TEXT NOT NULL,
    hook_config TEXT,
    is_enabled INTEGER NOT NULL DEFAULT 1,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS agent_event_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_name TEXT NOT NULL,
    payload TEXT,
    triggered_actions TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

-- Static & Dynamic Pages
CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    excerpt TEXT,
    content TEXT,
    template TEXT NOT NULL DEFAULT 'default',
    featured_image TEXT,
    author_id INTEGER NOT NULL,
    status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft','published','archived','ai_draft','pending_review','approved')),
    views INTEGER NOT NULL DEFAULT 0,
    sort_order INTEGER NOT NULL DEFAULT 0,
    meta_title TEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    custom_schema_json TEXT,
    published_at TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS page_revisions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    page_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    slug TEXT NOT NULL,
    excerpt TEXT,
    content TEXT,
    meta_title TEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    author_id INTEGER,
    action TEXT,
    changed_by TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);




-- Bright Education Services
CREATE TABLE IF NOT EXISTS services (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    name           TEXT,
    title          TEXT NOT NULL,
    slug           TEXT UNIQUE NOT NULL,
    description    TEXT,
    content        TEXT,
    icon           TEXT,
    price          REAL DEFAULT 0,
    packages_json  TEXT NOT NULL DEFAULT '[]',
    display_order  INTEGER NOT NULL DEFAULT 0,
    status         TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','inactive')),
    created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);
CREATE INDEX IF NOT EXISTS idx_services_slug   ON services(slug);
CREATE INDEX IF NOT EXISTS idx_services_status ON services(status);
CREATE INDEX IF NOT EXISTS idx_services_order  ON services(display_order);

CREATE TABLE IF NOT EXISTS service_slug_redirects (
    old_slug TEXT PRIMARY KEY,
    service_id INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);


-- Bright Education Contact Submissions
CREATE TABLE IF NOT EXISTS contacts (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    name           TEXT NOT NULL,
    email          TEXT NOT NULL,
    phone          TEXT,
    subject        TEXT,
    message        TEXT,
    intake_period  TEXT,
    japanese_level TEXT,
    status         TEXT NOT NULL DEFAULT 'new' CHECK(status IN ('new','read','replied','processing','completed','archived')),
    assigned_to    INTEGER,
    notes          TEXT,
    ip_address     TEXT,
    user_agent     TEXT,
    created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_contacts_status  ON contacts(status);
CREATE INDEX IF NOT EXISTS idx_contacts_created ON contacts(created_at);
-- ----------------------------------------------------
-- Bright Education Specialized Extension Tables
-- ----------------------------------------------------
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

CREATE TABLE IF NOT EXISTS community_groups (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    platform       TEXT NOT NULL DEFAULT 'facebook' CHECK(platform IN ('facebook','zalo','youtube','telegram','other')),
    name           TEXT NOT NULL,
    description    TEXT,
    url            TEXT NOT NULL,
    member_count   TEXT,
    display_order  INTEGER NOT NULL DEFAULT 0,
    status         TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','inactive')),
    created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS qa_questions (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id        INTEGER,
    author_name    TEXT NOT NULL,
    content        TEXT NOT NULL,
    likes_count    INTEGER NOT NULL DEFAULT 0,
    status         TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','hidden')),
    created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS qa_answers (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    question_id    INTEGER NOT NULL,
    user_id        INTEGER,
    author_name    TEXT NOT NULL,
    content        TEXT NOT NULL,
    likes_count    INTEGER NOT NULL DEFAULT 0,
    status         TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','hidden')),
    created_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (question_id) REFERENCES qa_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
