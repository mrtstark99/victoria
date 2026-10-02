<?php
/** @file views/blog/home.php
 * @description Victoria branded public homepage connected to the Victoria CMS data.
 */
$homeConfig = \Models\HomePage::get();
include APP_ROOT . '/views/layouts/header.php';
?>
<main id="hero" class="victoria-home"><?php include APP_ROOT . '/views/victoria/home/hero.php'; ?>
  <?php include APP_ROOT . '/views/victoria/home/trust.php'; ?>
  <?php include APP_ROOT . '/views/victoria/home/process.php'; ?>
  <?php include APP_ROOT . '/views/blog/home_sections/blog_preview.php'; ?>
  <?php include APP_ROOT . '/views/victoria/home/contact.php'; ?>
  <?php include APP_ROOT . '/views/victoria/scrollspy.php'; ?>
</main>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
