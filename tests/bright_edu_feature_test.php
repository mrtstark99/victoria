<?php
/**
 * @file tests/bright_edu_feature_test.php
 * @description Comprehensive automated test suite for Bright Education upgrade features.
 *
 * Layer:
 * - Tests / Feature & Integration
 *
 * Responsibilities:
 * - Validate Service and Contact database models.
 * - Verify template existence and structural integrity of Bright Education sections.
 * - Test contact submission validation rules.
 * - Verify partner schools data source parses correctly.
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function assertFeature($condition, $description) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] $description\n";
    } else {
        $failedTests++;
        echo "  [FAIL] $description\n";
    }
}

echo "====================================================\n";
echo "   BRIGHT EDUCATION v1 FEATURE TEST SUITE           \n";
echo "====================================================\n\n";

// 1. Service Model & Database
echo "1. Service Model & Seed Data:\n";
$services = \Models\Service::getAllActive();
assertFeature(is_array($services), "Service::getAllActive() returns array");
assertFeature(count($services) >= 4, "Service table contains at least 4 active study abroad programs");

$firstSlug = $services[0]['slug'] ?? '';
$serviceBySlug = \Models\Service::getBySlug($firstSlug);
assertFeature(!empty($serviceBySlug) && $serviceBySlug['slug'] === $firstSlug, "Service::getBySlug() finds service by slug '$firstSlug'");

// 2. Contact Model & Inquiry Submission
echo "\n2. Contact & Consultation Inquiries:\n";
$testContact = [
    'name' => 'Nguyen Van Test',
    'phone' => '0987654321',
    'email' => 'test@brighteducation.vn',
    'target_year' => '2027',
    'preferred_city' => 'Tokyo',
    'japanese_level' => 'N5',
    'message' => 'Automated test consultation inquiry message.'
];

$contactId = \Models\Contact::create($testContact);
assertFeature($contactId > 0, "Contact::create() successfully creates inquiry record with ID: $contactId");

$fetchedContact = \Models\Contact::findById($contactId);
assertFeature(!empty($fetchedContact) && $fetchedContact['name'] === 'Nguyen Van Test', "Contact::findById() retrieves created contact");
assertFeature($fetchedContact['status'] === 'new', "Contact initial status defaults to 'new'");

$updatedStatus = \Models\Contact::updateStatus($contactId, 'replied', 'Follow-up call completed');
assertFeature($updatedStatus === true, "Contact::updateStatus() successfully changes status to 'replied'");

$updatedContact = \Models\Contact::findById($contactId);
assertFeature($updatedContact['status'] === 'replied', "Contact status reflects 'replied'");
assertFeature(strpos($updatedContact['notes'], 'Follow-up call completed') !== false, "Contact admin_notes updated");

// Clean up test contact
$deleted = \Models\Contact::delete($contactId);
assertFeature($deleted === true, "Contact::delete() removes temporary test record");

// 3. Homepage Sections & Views Integrity
echo "\n3. Homepage Sections & Specialized Views:\n";
$expectedSections = [
    'hero.php',
    'programs.php',
    'process_steps.php',
    'info_portal.php',
    'cost_calculator.php',
    'blog_preview.php',
    'zoom_sessions.php',
    'contact_form.php',
    'scrollspy.php'
];

foreach ($expectedSections as $sec) {
    $secPath = APP_ROOT . '/views/blog/home_sections/' . $sec;
    assertFeature(file_exists($secPath), "Home section exists: home_sections/$sec");
}

$expectedViews = [
    'views/blog/home.php',
    'views/blog/blog_list.php',
    'views/blog/services.php',
    'views/blog/service_detail.php',
    'views/blog/contact.php',
    'views/blog/about.php',
    'views/blog/schools.php',
    'views/blog/courses.php',
    'views/blog/process.php',
    'views/blog/documents.php',
    'views/blog/cost.php',
    'views/blog/consultation.php',
    'views/blog/qa.php',
    'views/admin/services.php',
    'views/admin/service_form.php',
    'views/admin/contacts.php'
];

foreach ($expectedViews as $v) {
    $vPath = APP_ROOT . '/' . $v;
    assertFeature(file_exists($vPath), "View template exists: $v");
}

// 4. Partner Schools Dataset
echo "\n4. Schools Dataset:\n";
$schoolsFile = APP_ROOT . '/schools_data.json';
assertFeature(file_exists($schoolsFile), "schools_data.json exists");
$schoolsContent = file_get_contents($schoolsFile);
$schoolsData = json_decode($schoolsContent, true);
assertFeature(json_last_error() === JSON_ERROR_NONE, "schools_data.json parses as valid JSON");
assertFeature(is_array($schoolsData) && count($schoolsData) > 0, "schools_data.json contains partner institutions (" . count($schoolsData) . " loaded)");

echo "\n====================================================\n";
echo "SUMMARY: Total: $totalTests | Passed: $passedTests | Failed: $failedTests\n";
echo "====================================================\n";

if ($failedTests > 0) {
    exit(1);
}