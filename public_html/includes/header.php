<?php
/**
 * Header compartido para páginas estáticas y otras que lo incluyan.
 * Usa siempre URLs absolutas (assetUrl, imageUrl, siteUrl) para que funcione con URLs amigables (/slug).
 */
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}
$pageTitle = isset($pageTitle) ? $pageTitle : (defined('SITE_NAME') ? SITE_NAME : 'Página');
$pageDescription = isset($pageDescription) ? $pageDescription : (defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : '');
$pageKeywords = isset($pageKeywords) ? $pageKeywords : (defined('SITE_KEYWORDS') ? SITE_KEYWORDS : '');
$pageUrl = isset($pageUrl) ? $pageUrl : (defined('SITE_URL') ? SITE_URL : '');
$pageImage = isset($pageImage) ? $pageImage : (function_exists('imageUrl') ? imageUrl('design/logo-og.jpg') : '');
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <?php $base_url = defined('SITE_URL') ? rtrim(SITE_URL, '/') . '/' : '/'; ?>
    <base href="<?php echo htmlspecialchars($base_url); ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
    <meta name="author" content="<?php echo defined('SITE_NAME') ? htmlspecialchars(SITE_NAME) : ''; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($pageUrl); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo defined('SITE_NAME') ? htmlspecialchars(SITE_NAME) : ''; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($pageUrl); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($pageImage); ?>">
    <meta property="og:locale" content="es_MX">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($pageImage); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars(function_exists('imageUrl') ? imageUrl('design/favicon.ico') : ''); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(function_exists('assetUrl') ? assetUrl('css/main.css') : (defined('ASSETS_URL') ? ASSETS_URL . '/css/main.css' : '')); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(function_exists('assetUrl') ? assetUrl('css/landing.css') : (defined('ASSETS_URL') ? ASSETS_URL . '/css/landing.css' : '')); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(function_exists('assetUrl') ? assetUrl('css/responsive.css') : (defined('ASSETS_URL') ? ASSETS_URL . '/css/responsive.css' : '')); ?>?v=<?php echo time(); ?>">
    <?php if (file_exists(__DIR__ . '/analytics.php')) { include __DIR__ . '/analytics.php'; } ?>
    <style>
        .page-header, .page-hero { padding: 3rem 0; }
        .page-header h1, .page-hero h1 { margin: 0; font-weight: 700; }
        .page-hero { background-size: cover; background-position: center; color: #fff; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
        .page-content { padding: 2rem 0 4rem; }
        .page-body { font-size: 1.05rem; line-height: 1.7; }
        .page-body img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
