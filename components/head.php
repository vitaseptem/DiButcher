<?php
/**
 * <head> completo — Di Bútcher Premium Burger
 * SEO · Open Graph · Twitter Card · Schema.org · Fonts
 */
if (!defined('BRAND_NAME')) {
    require_once __DIR__ . '/../config.php';
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- ─── SEO ─── -->
    <title><?= htmlspecialchars(SEO_TITLE, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars(SEO_DESCRIPTION, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="keywords" content="<?= htmlspecialchars(SEO_KEYWORDS, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="author" content="<?= htmlspecialchars(BRAND_FULL_NAME, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#0D0D0D">
    <link rel="canonical" href="<?= htmlspecialchars(CANONICAL_URL, ENT_QUOTES, 'UTF-8') ?>">

    <!-- ─── Open Graph ─── -->
    <meta property="og:type" content="restaurant.restaurant">
    <meta property="og:site_name" content="<?= htmlspecialchars(BRAND_FULL_NAME, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars(OG_TITLE, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars(OG_DESCRIPTION, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars(OG_IMAGE_URL, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="<?= htmlspecialchars(CANONICAL_URL, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="pt_BR">

    <!-- ─── Twitter Card ─── -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars(OG_TITLE, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars(OG_DESCRIPTION, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars(OG_IMAGE_URL, ENT_QUOTES, 'UTF-8') ?>">

    <!-- ─── Favicon ─── -->
    <link rel="icon" href="/assets/img/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" href="/assets/img/logo.png">
    <link rel="apple-touch-icon" href="/assets/img/logo.png">

    <!-- ─── Schema.org JSON-LD: Restaurant ─── -->
    <script type="application/ld+json">
    <?= json_encode([
        '@context'      => 'https://schema.org',
        '@type'         => 'Restaurant',
        'name'          => BRAND_FULL_NAME,
        'description'   => SEO_DESCRIPTION,
        'image'         => OG_IMAGE_URL,
        'url'           => CANONICAL_URL,
        'telephone'     => '+' . WHATSAPP_NUMBER,
        'servesCuisine' => ['Hambúrguer', 'Smash Burger', 'Comida Artesanal'],
        'priceRange'    => 'R$ 5,00 - R$ 42,00',
        'foundingDate'  => BRAND_YEAR,
        'slogan'        => BRAND_SLOGAN_MAIN,
        'address'       => [
            '@type'           => 'PostalAddress',
            'addressLocality' => BRAND_CITY,
            'addressRegion'   => BRAND_STATE,
            'addressCountry'  => 'BR',
        ],
        'openingHoursSpecification' => [
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens'     => '18:00',
            'closes'    => '23:00',
        ],
        'sameAs'        => [INSTAGRAM_URL, TIKTOK_URL],
        'acceptsReservations' => false,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>

    <!-- ─── Fonts (preconnect + display=swap) ─── -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Oswald:wght@500;700&family=Inter:wght@400;500;600&display=swap">

    <!-- ─── Preload assets críticos ─── -->
    <link rel="preload" as="image" href="/assets/img/logo.png">

    <!-- ─── Stylesheets ─── -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
</head>
