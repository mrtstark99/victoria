<?php
/** @file views/blog/home.php
 * @description Victoria branded public homepage connected to the Victoria CMS data.
 */
$homeConfig = \Models\HomePage::get();
$visibleHomeSections = $homeConfig['visible_sections'] ?? [];
$homeSectionViews = [
    'trust' => APP_ROOT . '/views/victoria/home/trust.php',
    'programs' => APP_ROOT . '/views/blog/home_sections/programs.php',
    'process' => APP_ROOT . '/views/blog/home_sections/process_steps.php',
    'info_portal' => APP_ROOT . '/views/blog/home_sections/info_portal.php',
    'cost_calculator' => APP_ROOT . '/views/blog/home_sections/cost_calculator.php',
    'blog_preview' => APP_ROOT . '/views/blog/home_sections/blog_preview.php',
    'contact_form' => APP_ROOT . '/views/blog/home_sections/contact_form.php',
    'zoom_sessions' => APP_ROOT . '/views/blog/home_sections/zoom_sessions.php',
];
include APP_ROOT . '/views/layouts/header.php';
?>
<main id="hero" class="victoria-home"><?php include APP_ROOT . '/views/victoria/home/hero.php'; ?>
  <?php foreach (($homeConfig['sections'] ?? []) as $sectionKey): ?>
    <?php if (!empty($visibleHomeSections[$sectionKey]) && isset($homeSectionViews[$sectionKey])) include $homeSectionViews[$sectionKey]; ?>
  <?php endforeach; ?>
  <?php include APP_ROOT . '/views/victoria/scrollspy.php'; ?>
</main>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
