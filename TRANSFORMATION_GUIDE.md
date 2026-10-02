# Bright-Education-v1 System Transformation & Engineering Guide

## 1. Overview
This guide documents the architecture, transformation steps, and quality standards for migrating `blog-system` into `Bright-Education-v1`.
The goal of this system is to combine the enterprise-grade AI Agent automation, SEO architecture, E-E-A-T analysis, and blog publishing workflow of `blog-system` with the specialized du-hoc frontend design, responsive landing page components, service catalog, and consultation/contact pipeline of `bright-edu`.

## 2. Engineering Standards Compliance (AI_AGENT_ENGINEERING_RULES.md)
All code created, modified, or refactored in this repository must comply with the engineering contract defined in `AI_AGENT_ENGINEERING_RULES.md`:
1. **Mandatory File Header**: Standardized English header comment with `@file`, `@description`, `Layer`, `Responsibilities`, `Security`, `Dependencies`, `Constraints`, `AI Maintenance Rules`.
2. **File Size Limit**: Keep files under 300 lines whenever practical by splitting responsibilities cleanly into partials/services.
3. **Layer Separation**: Strict boundaries between Presentation (Views), Controllers, Models, and Helpers/Services.
4. **Security & Data Integrity**: Prepared statements for all SQL operations, CSRF token verification on state-changing forms, input sanitization, rate limiting, and secure sessions.
5. **English Code Comments**: Code comments and technical documentation must be written in English.

## 3. Transformation Milestones & Execution Plan

### Milestone 1: Asset Pipeline & Design System Synchronization
- **Source**: `C:\Projects\bright-edu\assets\`
- **Target**: `C:\Projects\Bright-Education-v1\public\assets\`
- **Actions**:
  - Copy WebP/PNG/SVG imagery into `public/assets/images/` (logos, hero graphics, 7-step process illustrations, program banners).
  - Deploy `components.css` and `home.css` into `public/assets/css/`.
  - Configure Tailwind theme tokens:
    - Primary: `#0d243e` (Navy)
    - Accents: Sage, Sakura, Sand, Rice, Ink, Muted
    - Typography: Headings in `Quicksand`, body in `Inter`
    - Shadow tokens: `shadow-soft`, `shadow-medium`, `shadow-hard`, `shadow-tinted`
    - Icon library: Bootstrap Icons CDN (`bi-*`)

### Milestone 2: Database Schema & Migration for Services and Contacts
- **Actions**:
  - Create table `services`:
    `id`, `name`, `title`, `slug`, `description`, `content`, `icon`, `price`, `display_order`, `status`, `created_at`, `updated_at`.
  - Create table `contacts`:
    `id`, `name`, `email`, `phone`, `intake_period`, `japanese_level`, `message`, `status`, `notes`, `ip_address`, `user_agent`, `created_at`, `updated_at`.
  - Seed baseline du-hoc service packages into `database/blog.db`.
  - Create `app/Models/Service.php` and `app/Models/Contact.php`.

### Milestone 3: Layout & Homepage Modernization
- **Layout Modernization**:
  - Re-engineer `views/layouts/header.php`, `navbar.php`, and `mobile_drawer.php`:
    - Glassmorphic sticky header with backdrop blur.
    - Full navigation with active indicator (`/`, `/services`, `/courses`, `/schools`, `/process`, `/documents`, `/cost`, `/blog`, `/contact`, `/consultation`, `/qa`).
    - CTA button "Tư vấn miễn phí".
    - User account dropdown / profile link.
    - Smooth mobile navigation drawer.
  - Re-engineer `views/layouts/footer.php`:
    - 4-column corporate footer with social icons, quick links, service links, and bilateral hotline/address (VN & JP).
- **Homepage Structure (`views/blog/home.php`)**:
  - Modularized sections:
    1. `hero.php`: Main banner, statistics counter, primary CTA.
    2. `trust.php`: 4 core brand commitments.
    3. `programs.php`: Study abroad program pathways (Language schools, Senmon, SSW, Universities).
    4. `process_steps.php`: 7-step application journey.
    5. `info_portal.php`: Resource hub and FAQ accordion.
    6. `cost_calculator.php`: Interactive tuition & living expense estimator.
    7. `blog_preview.php`: Dynamically loaded recent blog posts from SQLite database.
    8. `zoom_sessions.php`: 1-on-1 and group consultation scheduling.
    9. `contact_form.php`: Interactive intake consultation form.
    10. `scrollspy.php`: Floating quick-jump navigation bar.

### Milestone 4: Services & Contact Modules (Frontend & Admin)
- **Services Module**:
  - `app/Controllers/ServiceController.php`
  - `views/blog/services.php` (package overview, comparison matrix, pricing breakdown)
  - `views/blog/service_detail.php` (individual program details)
  - `app/Controllers/AdminServiceController.php` (CRUD administration)
  - `views/admin/services.php` & `views/admin/service_form.php`
- **Contact & Consultation Module**:
  - `app/Controllers/ContactController.php` (GET `/contact`, POST `/api/contact`)
  - `views/blog/contact.php` (Split-screen layout)
  - `app/Controllers/AdminContactController.php` (management dashboard at `/admin/contacts`)
  - `views/admin/contacts.php`
- **Specialized Information Pages**:
  - Dedicated views for `/about`, `/schools`, `/courses`, `/process`, `/documents`, `/cost`, `/consultation`, `/qa`.

### Milestone 5: Routing & URL Architecture
- `/` -> Homepage Landing Page (Corporate + Interactive Sections + Blog Preview)
- `/blog` -> Blog Archive & Magazine (Search, Category Filter, Pagination, AI Content)
- `/blog/{slug}` -> Single Post View (AI Table of Contents, Schema JSON-LD, Post Element Contract)
- `/services` & `/services/{slug}` -> Service Directory & Details
- `/contact` & `/api/contact` -> Contact Page & Form Handler
- `/admin/services` & `/admin/contacts` -> Admin Operations
- `/api/agent` -> Fully preserved AI Agent endpoint (16 actions intact)

### Milestone 6: Automated Server Deployment (`blog.dev-br.xyz`)
- Server Target: VM101 (`192.168.0.110`), Webroot: `/www/wwwroot/blog`
- Automated deployment via:
  - Local push-deploy script (`deploy.ps1`)
  - GitHub Actions CI/CD workflow (`.github/workflows/deploy.yml`)
  - Database migration auto-execution on deployment
  - Service reload (`systemctl reload php-fpm-82`)

## 4. Verification Checklist
- [ ] No regression on existing AI Agent endpoints (`/api/agent`).
- [ ] SEO schemas (Article, Organization, Breadcrumb, Service) validate properly.
- [ ] All forms (Contact, Consultation, Newsletter) pass CSRF and input validation.
- [ ] All routes return HTTP 200 without PHP errors or warnings.
- [ ] Visual design exactly mirrors the aesthetic quality of `bright-edu`.


## 4. Deployment Architecture & Operations

### Production Environment Specifications
- **Target Server**: VM101 (`brhub-web`, `192.168.0.110`) on Proxmox host (`192.168.0.109`).
- **Live URL**: `https://blog.dev-br.xyz` (Proxied via Cloudflare Tunnel on `192.168.0.111`).
- **Web Server**: Nginx with PHP 8.2 FastCGI (`php-fpm-82`).
- **Webroot**: `/www/wwwroot/blog` (Public docroot: `/www/wwwroot/blog/public`).
- **Repository**: `https://github.com/mrtstark99/Bright-Education-v1`

### Automated Deployment Mechanisms
1. **GitHub Actions CI/CD (`.github/workflows/deploy.yml`)**:
   - Triggers automatically upon pushing commits to the `main` branch.
   - Executes full regression suite (`php tests/run_tests.php`, `php tests/bright_edu_feature_test.php`, `php tests/post_element_contract_test.php`, `php tests/ai_guidelines_test.php`).
   - Connects securely via SSH using Cloudflare Access gateway (`ssh.dev-br.xyz`).
   - Packages application code, backs up existing database (`database/backups/blog.db.<timestamp>.bak`), unpacks new code, executes migrations (`migrate_bright_edu.php` and `migrate_consultations_and_qa.php`), adjusts permissions (`www:www`, `755`/`775`), reloads `php-fpm-82` and `nginx`, and performs automated HTTP health checks.

2. **Local 1-Click Deployment (`deploy.ps1` / `scripts/deploy.sh`)**:
   - For rapid operator deployments directly from developer workstations:
     ```powershell
     # From C:\Projects\Bright-Education-v1:
     .\deploy.ps1
     ```
   - Automatically runs local test batteries, backs up the remote SQLite database, transfers code archives, runs migrations, reloads web services, and verifies live HTTP status.

### Production Verification Audit
All 15 endpoints verified live on `https://blog.dev-br.xyz`:
- `https://blog.dev-br.xyz/` (HTTP 200, 159 KB - Full 9-section Bright Education homepage)
- `https://blog.dev-br.xyz/blog` (HTTP 200, 46 KB - Blog magazine catalog)
- `https://blog.dev-br.xyz/services` (HTTP 200, 48 KB - Services directory)
- `https://blog.dev-br.xyz/services/du-hoc-truong-nhat-ngu` (HTTP 200, 31 KB - Service detail)
- `https://blog.dev-br.xyz/contact` (HTTP 200, 37 KB - Contact inquiry form)
- `https://blog.dev-br.xyz/about` (HTTP 200, 33 KB - About us)
- `https://blog.dev-br.xyz/schools` (HTTP 200, 229 KB - Interactive partner schools database)
- `https://blog.dev-br.xyz/courses` (HTTP 200, 37 KB - JLPT courses)
- `https://blog.dev-br.xyz/process` (HTTP 200, 46 KB - 7-Step COE & Visa process)
- `https://blog.dev-br.xyz/documents` (HTTP 200, 39 KB - Application documents)
- `https://blog.dev-br.xyz/cost` (HTTP 200, 48 KB - Cost & living estimator)
- `https://blog.dev-br.xyz/consultation` (HTTP 200, 51 KB - Zoom consultation booking & schedule)
- `https://blog.dev-br.xyz/qa` (HTTP 200, 80 KB - Community Q&A & support groups)
- `https://blog.dev-br.xyz/sitemap.xml` (HTTP 200, 3.7 KB - Dynamic XML sitemap)
- `https://blog.dev-br.xyz/api/agent.php` (HTTP 401 - AI Agent endpoint active and guarded by Bearer token auth)