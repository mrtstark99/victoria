<?php
/**
 * @file public/api/contact.php
 * @description Direct API endpoint for contact inquiries.
 *
 * Layer:
 * - Presentation / API Endpoint
 *
 * Responsibilities:
 * - Bridge incoming contact form requests directly to ContactController.
 *
 * Security:
 * - CSRF verification and input sanitization enforced inside ContactController.
 *
 * Dependencies:
 * - Controllers\ContactController
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 */

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

(new Controllers\ContactController())->submit();
