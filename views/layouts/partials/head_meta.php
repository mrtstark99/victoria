<?php
/**
 * @file views/layouts/partials/head_meta.php
 * @description Head meta tags, SEO schema, Open Graph, fonts, and stylesheets.
 *
 * Layer:
 * - Presentation / View Partial
 *
 * Responsibilities:
 * - Render HTML head elements, dynamic meta tags, and Open Graph headers.
 * - Load Google Fonts (Quicksand, Inter) and Bootstrap Icons.
 * - Inject Tailwind CSS client engine with Bright Education design tokens.
 * - Include Post Element Contract stylesheets and Google Analytics when configured.
 *
 * Security:
 * - HTML entity encoding for all user-controllable meta values to prevent XSS.
 *
 * Dependencies:
 * - App configuration constants and getSetting helper.
 * - SchemaBuilder helper for JSON-LD structured data.
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
?><!DOCTYPE html>
<html lang="vi" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitleFull; ?></title>
    <meta name="description" content="<?php echo $metaDesc; ?>">
    <?php if ($metaKeys !== ''): ?>
    <meta name="keywords" content="<?php echo $metaKeys; ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($currentUrl); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo !empty($victoriaPublicTheme) ? '/assets/images/VICTORIA_LOGO.svg' : (!empty($siteFaviconUrl) ? htmlspecialchars($siteFaviconUrl) : '/assets/images/favicon.png'); ?>">

    <!-- Search Engine & Social Meta Tags -->
    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="<?php echo $ogType; ?>">
    <meta property="og:title" content="<?php echo $pageTitleFull; ?>">
    <meta property="og:description" content="<?php echo $metaDesc; ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($currentUrl); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars(!empty($victoriaPublicTheme) ? 'Victoria Universal' : $siteTitle); ?>">
    <meta property="og:image" content="<?php echo !empty($victoriaPublicTheme) && ($page_css ?? '') === 'victoria' ? htmlspecialchars($siteBaseUrl . '/assets/images/hero-victoria.jpg') : $ogImg; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $pageTitleFull; ?>">
    <meta name="twitter:description" content="<?php echo $metaDesc; ?>">
    <meta name="twitter:image" content="<?php echo $ogImg; ?>">

    <?php if (!empty($gscVerification)): ?>
        <?php if (str_starts_with($gscVerification, '<meta')): ?>
            <?php echo $gscVerification . "\n"; ?>
        <?php else: ?>
            <meta name="google-site-verification" content="<?php echo htmlspecialchars($gscVerification); ?>">
        <?php endif; ?>
    <?php endif; ?>

    <!-- Local Victoria typography and Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Tailwind CSS Client Engine & Theme Configuration -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
              display: ['Quicksand', 'ui-sans-serif', 'system-ui', 'sans-serif']
            },
            colors: {
              primary: {
                DEFAULT: '#006644', 50: '#EBF5F0', 100: '#D6EBE1', 200: '#C3E2D5',
                300: '#95CBB3', 400: '#4AA37D', 500: '#006644', 600: '#00583B',
                700: '#004F34', 800: '#003E2C', 900: '#102A20',
              },
              sage: { 50: '#FAFBF9', 100: '#F1F4F1', 200: '#E2E8E4', 300: '#C3E2D5', 400: '#4AA37D', 500: '#006644', 600: '#00583B', 900: '#102A20' },
              sakura: { 50: '#FFF2EE', 100: '#F0C2B7', 200: '#C05238', 300: '#C05238', 400: '#9F3E2C', 500: '#9F3E2C', 600: '#9F3E2C', 900: '#102A20' },
              sand: { 50: '#ffffff', 100: '#FAFBF9', 200: '#F1F4F1' },
              midnight: '#006644',
              ink: '#102A20',
              muted: '#60736A',
              rice: '#FAFBF9'
            },
            boxShadow: {
              'soft': '0 8px 30px rgba(0, 102, 68, 0.03)',
              'medium': '0 16px 40px rgba(0, 102, 68, 0.06)',
              'hard': '0 24px 60px rgba(0, 102, 68, 0.1)',
              'tinted': '0 20px 40px rgba(11, 43, 31, 0.1)',
            },
            borderRadius: {
              '4xl': '2rem',
              '5xl': '2.5rem',
              'blob': '40% 60% 70% 30% / 40% 50% 60% 50%',
            }
          }
        }
      }
    </script>

    <!-- Global Component Styles & Home Sections Stylesheet -->
    <link rel="stylesheet" href="/assets/css/components.css?v=<?php echo file_exists(APP_ROOT . '/public/assets/css/components.css') ? filemtime(APP_ROOT . '/public/assets/css/components.css') : '1.0'; ?>">
    <link rel="stylesheet" href="/assets/css/home.css?v=<?php echo file_exists(APP_ROOT . '/public/assets/css/home.css') ? filemtime(APP_ROOT . '/public/assets/css/home.css') : '1.0'; ?>">
    <?php $staticPagesStyleFile = APP_ROOT . '/public/assets/css/static_pages.css'; ?>
    <link rel="stylesheet" href="/assets/css/static_pages.css?v=<?php echo is_file($staticPagesStyleFile) ? filemtime($staticPagesStyleFile) : '1'; ?>">

    <!-- Post Element Contract and Blog Stylesheets -->
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo file_exists(APP_ROOT . '/public/assets/css/style.css') ? filemtime(APP_ROOT . '/public/assets/css/style.css') : '1.0'; ?>">
    <?php if (($page_css ?? '') === 'post'): ?>
        <?php foreach ([
            'layout_header', 'hero_article_toc', 'typography_toc', 'ui_takeaways_headings',
            'ui_callouts', 'ui_comparisons_tables', 'ui_steps_timeline_metrics',
            'ui_faq_cta_download', 'sidebar_widgets', 'related_author_footer'
        ] as $postStyle):
            $postStyleFile = APP_ROOT . '/public/assets/css/post/' . $postStyle . '.css';
            if (file_exists($postStyleFile)):
        ?>
    <link rel="stylesheet" href="/assets/css/post/<?php echo $postStyle; ?>.css?v=<?php echo filemtime($postStyleFile); ?>">
        <?php endif; endforeach; ?>
    <?php elseif (isset($page_css)): ?>
    <?php
      $pageStyleName = $page_css === 'category' ? 'home' : $page_css;
      $pageStyleFile = APP_ROOT . '/public/assets/css/' . $pageStyleName . '.css';
    ?>
    <link rel="stylesheet" href="/assets/css/<?php echo htmlspecialchars($pageStyleName); ?>.css?v=<?php echo is_file($pageStyleFile) ? filemtime($pageStyleFile) : '1'; ?>">
      <?php if (in_array($page_css, ['home', 'category'], true)): ?>
        <?php foreach (['hero_spotlight', 'category_pills', 'article_grid_pagination'] as $homeStyle):
          $homeStyleFile = APP_ROOT . '/public/assets/css/home/' . $homeStyle . '.css';
          if (is_file($homeStyleFile)):
        ?>
    <link rel="stylesheet" href="/assets/css/home/<?php echo $homeStyle; ?>.css?v=<?php echo filemtime($homeStyleFile); ?>">
        <?php endif; endforeach; ?>
      <?php endif; ?>
      <?php if ($page_css === 'category'): ?>
        <?php $categoryStyleFile = APP_ROOT . '/public/assets/css/category.css'; ?>
    <link rel="stylesheet" href="/assets/css/category.css?v=<?php echo is_file($categoryStyleFile) ? filemtime($categoryStyleFile) : '1'; ?>">
      <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($victoriaPublicTheme)): ?>
    <?php
      $victoriaStyles = ['01-base','02-components','03-navigation','12-footer','14-responsive'];
      if (($page_css ?? '') === 'victoria') $victoriaStyles = ['01-base','02-components','03-navigation','04-hero-main','05-hero-media','06-hero-details','07-programs-a','08-programs-b','09-programs-c','10-process','11-contact','12-footer','13-floating','14-responsive','15-reveal','16-auth'];
      foreach ($victoriaStyles as $victoriaStyle):
    ?>
    <link rel="stylesheet" href="/assets/css/victoria/<?php echo $victoriaStyle; ?>.css?v=<?php echo filemtime(APP_ROOT . '/public/assets/css/victoria/' . $victoriaStyle . '.css'); ?>">
    <?php endforeach; ?>
    <?php $floatingUiStyle = APP_ROOT . '/public/assets/css/victoria/17-scrollspy-chat.css'; ?>
    <link rel="stylesheet" href="/assets/css/victoria/17-scrollspy-chat.css?v=<?php echo is_file($floatingUiStyle) ? filemtime($floatingUiStyle) : '1'; ?>">
    <?php $navigationEnhancements = APP_ROOT . '/public/assets/css/victoria/19-navigation-dropdowns.css'; ?>
    <link rel="stylesheet" href="/assets/css/victoria/19-navigation-dropdowns.css?v=<?php echo is_file($navigationEnhancements) ? filemtime($navigationEnhancements) : '1'; ?>">
    <link rel="stylesheet" href="/assets/css/victoria/18-code-theme.css?v=<?php echo filemtime(APP_ROOT . '/public/assets/css/victoria/18-code-theme.css'); ?>">
    <?php endif; ?>
    <?php if (($page_css ?? '') !== 'victoria'): ?><style>
      /* Match the Victoria and Bright Education type system. */
      body, input, button, select, textarea { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
      h1, h2, h3, h4, h5, h6 { font-family: 'Quicksand', ui-sans-serif, system-ui, sans-serif !important; }
      table th, table td { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style><?php endif; ?>

    <!-- Structured Data (Schema JSON-LD) -->
    <?php if (isset($schema_json) && !empty($schema_json)): ?>
    <script type="application/ld+json">
    <?php echo json_encode($schema_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
    </script>
    <?php else: ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "Victoria Universal",
      "url": "<?php echo htmlspecialchars($siteBaseUrl, ENT_QUOTES, 'UTF-8'); ?>/",
      "logo": "<?php echo htmlspecialchars($siteBaseUrl, ENT_QUOTES, 'UTF-8'); ?>/assets/images/VICTORIA_LOGO.svg",
      "description": "<?php echo htmlspecialchars($siteSlogan); ?>",
      "inLanguage": "vi-VN"
    }
    </script>
    <?php endif; ?>

    <!-- Google Analytics GA4 -->
    <?php if (!empty($gaId) && preg_match('/^G-[A-Z0-9]+$/', $gaId)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($gaId); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo htmlspecialchars($gaId); ?>');
    </script>
    <?php endif; ?>

    <?php if (!empty($customHeaderCode)): ?>
        <?php
        // The site already loads and configures Tailwind above. Legacy custom
        // header code must not load it again or replace the Victoria color theme.
        $safeHeaderCode = preg_replace(
            '~<script\b[^>]*\bsrc=["\']https://cdn\.tailwindcss\.com[^"\']*["\'][^>]*>\s*</script>~i',
            '',
            $customHeaderCode
        );
        $safeHeaderCode = preg_replace(
            '~<script\b[^>]*>\s*tailwind\.config\s*=.*?</script>~is',
            '',
            $safeHeaderCode
        );
        echo $safeHeaderCode . "\n";
        ?>
    <?php endif; ?>
</head>
