<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Landing Page
 * ========================================
 * 
 * Página principal del sitio
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';

// Cargar funciones
require_once INCLUDES_PATH . '/functions.php';

// Variables para meta tags
$pageTitle = SITE_NAME . ' - ' . SITE_TAGLINE;
$pageDescription = SITE_DESCRIPTION;
$pageKeywords = SITE_KEYWORDS;
$pageUrl = SITE_URL;
$pageImage = imageUrl('design/logo-og.jpg');
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <!-- ========================================
         META TAGS BÁSICOS
         ======================================== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    
    <!-- ========================================
         SEO BÁSICO
         ======================================== -->
    <title><?php echo esc($pageTitle); ?></title>
    <meta name="description" content="<?php echo esc($pageDescription); ?>">
    <meta name="keywords" content="<?php echo esc($pageKeywords); ?>">
    <meta name="author" content="<?php echo esc(SITE_NAME); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo esc($pageUrl); ?>">
    
    <!-- ========================================
         OPEN GRAPH (Facebook, LinkedIn)
         ======================================== -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo esc(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo esc($pageTitle); ?>">
    <meta property="og:description" content="<?php echo esc($pageDescription); ?>">
    <meta property="og:url" content="<?php echo esc($pageUrl); ?>">
    <meta property="og:image" content="<?php echo esc($pageImage); ?>">
    <meta property="og:locale" content="es_MX">
    
    <!-- ========================================
         TWITTER CARD
         ======================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo esc($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo esc($pageImage); ?>">
    <meta name="twitter:site" content="@aramedylab">
    
    <!-- ========================================
         FAVICON & TOUCH ICONS
         ======================================== -->
    <link rel="icon" type="image/x-icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo imageUrl('design/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo imageUrl('design/favicon-16x16.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo imageUrl('design/apple-touch-icon.png'); ?>">
    <link rel="manifest" href="<?php echo siteUrl('site.webmanifest'); ?>">
    
    <!-- Favicon para diferentes navegadores -->
    <link rel="shortcut icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    <link rel="icon" href="<?php echo imageUrl('design/favicon.ico'); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo imageUrl('design/favicon-32x32.png'); ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?php echo imageUrl('design/favicon-16x16.png'); ?>" type="image/png" sizes="16x16">
    
    <!-- ========================================
         PRECONNECT & DNS-PREFETCH
         ======================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">
    <link rel="dns-prefetch" href="https://www.google.com">
    
    <!-- ========================================
         GOOGLE FONTS
         ======================================== -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- ========================================
         BOOTSTRAP 5
         ======================================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- ========================================
         BOOTSTRAP ICONS
         ======================================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- ========================================
         SWIPER JS (Sliders)
         ======================================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- ========================================
         AOS (Animate On Scroll)
         ======================================== -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- ========================================
         CUSTOM CSS
         ======================================== -->
    <link rel="stylesheet" href="<?php echo assetUrl('css/main.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/landing.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/responsive.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- ========================================
         STRUCTURED DATA (Schema.org)
         ======================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo esc(SITE_NAME); ?>",
        "url": "<?php echo esc(SITE_URL); ?>",
        "logo": "<?php echo esc($pageImage); ?>",
        "description": "<?php echo esc($pageDescription); ?>",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "<?php echo esc(ADDRESS_COUNTRY); ?>"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo esc(PHONE_FORMATTED); ?>",
            "contactType": "customer service",
            "email": "<?php echo esc(CONTACT_EMAIL); ?>",
            "areaServed": "MX",
            "availableLanguage": "Spanish"
        },
        "sameAs": [
            "<?php echo esc(SOCIAL_FACEBOOK); ?>",
            "<?php echo esc(SOCIAL_INSTAGRAM); ?>",
            "<?php echo esc(SOCIAL_LINKEDIN); ?>",
            "<?php echo esc(SOCIAL_TWITTER); ?>"
        ]
    }
    </script>
    
    <?php if (RECAPTCHA_ENABLED): ?>
    <!-- ========================================
         GOOGLE reCAPTCHA v3
         ======================================== -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo RECAPTCHA_SITE_KEY; ?>" defer></script>
    <?php endif; ?>
    
    <!-- ========================================
         STRUCTURED DATA (Schema.org JSON-LD)
         ======================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "@id": "<?php echo SITE_URL; ?>#organization",
                "name": "<?php echo esc(SITE_NAME); ?>",
                "url": "<?php echo SITE_URL; ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo imageUrl('design/logo.png'); ?>",
                    "width": 250,
                    "height": 100
                },
                "image": "<?php echo esc($pageImage); ?>",
                "description": "<?php echo esc($pageDescription); ?>",
                "address": {
                    "@type": "PostalAddress",
                    "addressCountry": "MX",
                    "addressRegion": "Ciudad de México"
                },
                "contactPoint": {
                    "@type": "ContactPoint",
                    "telephone": "<?php echo PHONE_MAIN; ?>",
                    "contactType": "customer service",
                    "areaServed": "MX",
                    "availableLanguage": ["Spanish"]
                },
                "sameAs": [
                    "<?php echo SOCIAL_FACEBOOK; ?>",
                    "<?php echo SOCIAL_INSTAGRAM; ?>",
                    "<?php echo SOCIAL_LINKEDIN; ?>"
                ]
            },
            {
                "@type": "WebSite",
                "@id": "<?php echo SITE_URL; ?>#website",
                "url": "<?php echo SITE_URL; ?>",
                "name": "<?php echo esc(SITE_NAME); ?>",
                "description": "<?php echo esc($pageDescription); ?>",
                "publisher": {
                    "@id": "<?php echo SITE_URL; ?>#organization"
                },
                "inLanguage": "es-MX"
            },
            {
                "@type": "WebPage",
                "@id": "<?php echo esc($pageUrl); ?>#webpage",
                "url": "<?php echo esc($pageUrl); ?>",
                "name": "<?php echo esc($pageTitle); ?>",
                "isPartOf": {
                    "@id": "<?php echo SITE_URL; ?>#website"
                },
                "about": {
                    "@id": "<?php echo SITE_URL; ?>#organization"
                },
                "description": "<?php echo esc($pageDescription); ?>",
                "inLanguage": "es-MX"
            },
            {
                "@type": "LocalBusiness",
                "@id": "<?php echo SITE_URL; ?>#localbusiness",
                "name": "<?php echo esc(SITE_NAME); ?>",
                "image": "<?php echo esc($pageImage); ?>",
                "priceRange": "$$",
                "address": {
                    "@type": "PostalAddress",
                    "addressCountry": "MX",
                    "addressRegion": "Ciudad de México"
                },
                "geo": {
                    "@type": "GeoCoordinates",
                    "latitude": 19.4326,
                    "longitude": -99.1332
                },
                "url": "<?php echo SITE_URL; ?>",
                "telephone": "<?php echo PHONE_MAIN; ?>",
                "email": "<?php echo CONTACT_EMAIL; ?>",
                "openingHoursSpecification": {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                    "opens": "09:00",
                    "closes": "18:00"
                },
                "hasOfferCatalog": {
                    "@type": "OfferCatalog",
                    "name": "Simuladores Médicos",
                    "itemListElement": [
                        {
                            "@type": "OfferCatalog",
                            "name": "Simuladores Maternales",
                            "itemListElement": [
                                {
                                    "@type": "Offer",
                                    "itemOffered": {
                                        "@type": "Product",
                                        "name": "Simulador Maternal Avanzado"
                                    }
                                }
                            ]
                        },
                        {
                            "@type": "OfferCatalog",
                            "name": "Simuladores RCP y Emergencias",
                            "itemListElement": [
                                {
                                    "@type": "Offer",
                                    "itemOffered": {
                                        "@type": "Product",
                                        "name": "Simulador RCP Profesional"
                                    }
                                }
                            ]
                        },
                        {
                            "@type": "OfferCatalog",
                            "name": "Simuladores Pediátricos",
                            "itemListElement": [
                                {
                                    "@type": "Offer",
                                    "itemOffered": {
                                        "@type": "Product",
                                        "name": "Simulador Pediátrico de Alta Fidelidad"
                                    }
                                }
                            ]
                        },
                        {
                            "@type": "OfferCatalog",
                            "name": "Anatomage Table",
                            "itemListElement": [
                                {
                                    "@type": "Offer",
                                    "itemOffered": {
                                        "@type": "Product",
                                        "name": "Mesa de Disección Virtual Anatomage"
                                    }
                                }
                            ]
                        }
                    ]
                }
            }
        ]
    }
    </script>
</head>

<body id="home" class="landing-page">
    
    <!-- ========================================
         TOPBAR
         ======================================== -->
    <?php include INCLUDES_PATH . '/topbar.php'; ?>
    
    <!-- ========================================
         NAVBAR
         ======================================== -->
    <?php include INCLUDES_PATH . '/navbar.php'; ?>
    
    <!-- ========================================
         HERO / SLIDESHOW
         ======================================== -->
    <section id="hero" class="hero-section">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                
                <!-- Slide 1: Principal -->
                <div class="swiper-slide hero-slide">
                    <picture class="hero-slide-image">
                        <source srcset="<?php echo imageUrl('hero/aramedylaboratorio.webp'); ?>" type="image/webp">
                        <img id="hero-main-image" src="<?php echo imageUrl('hero/aramedylaboratorio.jpg'); ?>" alt="Aramed y Laboratorios - Simuladores médicos" loading="lazy">
                    </picture>
                    <div class="hero-slide-bg" style="background: transparent;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center justify-content-center">
                                <div class="col-lg-8 col-xl-6">
                                    <div class="hero-content text-center" data-aos="fade-up">
                                        <!-- Logo arriba -->
                                        <div class="hero-logo mb-4" data-aos="fade-down" data-aos-delay="200">
                                            <img src="<?php echo imageUrl('design/logo.png'); ?>" alt="Aramed y Laboratorios" height="120" class="img-fluid">
                                        </div>
                                        
                                        <!-- Contenido de texto -->
                                        <div class="hero-text-wrapper p-4 rounded-3" style="background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(0px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                                            <h2 class="hero-subtitle h2 mb-3 fw-bold text-white" id="hero-subtitle">
                                                Simuladores médicos para la enseñanza
                                            </h2>
                                            <p class="hero-description mb-4 text-white" id="hero-description">
                                                Distribuidores líderes de tecnología educativa en salud. 
                                                Equipamos universidades, hospitales e instituciones con 
                                                simuladores de última generación.
                                            </p>
                                            
                                            <!-- Botón principal -->
                                            <div class="hero-actions mb-3">
                                                <a href="#newsletter" class="btn btn-lg px-5" id="hero-btn-primary">
                                                    <i class="bi bi-envelope me-2"></i>
                                                    Contáctanos
                                                </a>
                                            </div>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 2: VICTORIA® S2200 -->
                <div class="swiper-slide hero-slide">
                    <picture class="hero-slide-image">
                        <source srcset="<?php echo imageUrl('hero/hero-victoria-s2200.webp'); ?>" type="image/webp">
                        <img src="<?php echo imageUrl('hero/hero-victoria-s2200.jpg'); ?>" alt="VICTORIA S2200 Simulador Obstétrico" loading="lazy">
                    </picture>
                    <div class="hero-slide-bg" style="background: transparent;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
                                        <div class="hero-text-wrapper p-4 rounded-3" style="background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(0px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                                            <span class="hero-badge badge bg-white text-dark mb-3 px-3 py-2">
                                                <i class="bi bi-star-fill me-2"></i>
                                                Simulador Obstétrico
                                            </span>
                                            <h2 class="hero-title display-3 fw-bold text-white mb-3">
                                                VICTORIA<sup>®</sup> S2200
                                            </h2>
                                            <h3 class="hero-subtitle h4 text-white mb-4">
                                                El simulador de parto más avanzado del mundo
                                            </h3>
                                            <p class="hero-description lead text-white mb-4">
                                                Entrenamiento realista para partos, emergencias y cuidados maternos
                                            </p>
                                            <ul class="hero-features list-unstyled text-white mb-4">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Ojos interactivos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 4 abdómenes: vientre para procedimientos de cesárea, trabajo de parto, hemorragia post parto, maniobras de leopold</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Compatible con monitores y equipos clínicos reales</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Neonato con signos vitales y respuesta realista</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Dos simuladores en un solo modelo: simulación obstétrica y ginecológica</li>
                                            </ul>
                                            <div class="hero-actions d-flex flex-wrap gap-3">
                                                <a href="#newsletter" class="btn btn-light btn-lg px-5">
                                                    Solicitar Información
                                                </a>
                                                <a href="#productos" class="btn btn-outline-light btn-lg px-5">
                                                    Ver Detalles
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 3: HAL® S5301 -->
                <div class="swiper-slide hero-slide">
                    <picture class="hero-slide-image">
                        <source srcset="<?php echo imageUrl('hero/hero-hal-s5301.webp'); ?>" type="image/webp">
                        <img src="<?php echo imageUrl('hero/hero-hal-s5301.jpg'); ?>" alt="HAL S5301 Simulador Avanzado" loading="lazy">
                    </picture>
                    <div class="hero-slide-bg" style="background: transparent;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
                                        <div class="hero-text-wrapper p-4 rounded-3" style="background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(0px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                                            <span class="hero-badge badge bg-white text-dark mb-3 px-3 py-2">
                                                <i class="bi bi-cpu-fill me-2"></i>
                                                Simulación Avanzada
                                            </span>
                                            <h2 class="hero-title display-3 fw-bold text-white mb-3">
                                                HAL<sup>®</sup> S5301
                                            </h2>
                                            <h3 class="hero-subtitle h4 text-white mb-4">
                                                Donde la simulación se convierte en experiencia real
                                            </h3>
                                            <ul class="hero-features list-unstyled text-white mb-4">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Audio, expresiones faciales y movimientos realistas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Simulación de problemas neurológicos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Respuesta activa al dolor y presión</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Reconocimiento automático de fármacos y fluidos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Escenarios SLE™ interdisciplinarios preinstalados</li>
                                            </ul>
                                            <div class="hero-actions d-flex flex-wrap gap-3">
                                                <a href="#newsletter" class="btn btn-light btn-lg px-5">
                                                    Solicitar Demo
                                                </a>
                                                <a href="#productos" class="btn btn-outline-light btn-lg px-5">
                                                    Conocer Más
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 4: HAL® S3201 -->
                <div class="swiper-slide hero-slide">
                    <picture class="hero-slide-image">
                        <source srcset="<?php echo imageUrl('hero/hero-hal-s3201.webp'); ?>" type="image/webp">
                        <img src="<?php echo imageUrl('hero/hero-hal-s3201.jpg'); ?>" alt="HAL S3201 UCI y Emergencias" loading="lazy">
                    </picture>
                    <div class="hero-slide-bg" style="background: transparent;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
                                        <div class="hero-text-wrapper p-4 rounded-3" style="background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(0px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                                            <span class="hero-badge badge bg-white text-dark mb-3 px-3 py-2">
                                                <i class="bi bi-heart-pulse-fill me-2"></i>
                                                UCI y Emergencias
                                            </span>
                                            <h2 class="hero-title display-3 fw-bold text-white mb-3">
                                                HAL<sup>®</sup> S3201
                                            </h2>
                                            <h3 class="hero-subtitle h4 text-white mb-4">
                                                Realismo clínico en cada entrenamiento
                                            </h3>
                                            <ul class="hero-features list-unstyled text-white mb-4">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-info me-2"></i> Simulación para emergencias, UCI y medicina general</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-info me-2"></i> Fisiología dinámica y respuesta automática</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-info me-2"></i> Compatible con ventiladores y equipo clínico real</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-info me-2"></i> Control mediante UNI® 3</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-info me-2"></i> Monitoreo ECG, SpO₂, presión y CO₂ en tiempo real</li>
                                            </ul>
                                            <div class="hero-actions d-flex flex-wrap gap-3">
                                                <a href="#newsletter" class="btn btn-light btn-lg px-5">
                                                    Solicitar Cotización
                                                </a>
                                                <a href="#productos" class="btn btn-outline-light btn-lg px-5">
                                                    Ver Características
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 5: Super TORY® S2220 -->
                <div class="swiper-slide hero-slide">
                    <picture class="hero-slide-image">
                        <source srcset="<?php echo imageUrl('hero/hero-super-tory-s2220.webp'); ?>" type="image/webp">
                        <img src="<?php echo imageUrl('hero/hero-super-tory-s2220.jpg'); ?>" alt="Super TORY S2220 Simulador Neonatal" loading="lazy">
                    </picture>
                    <div class="hero-slide-bg" style="background: transparent;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
                                        <div class="hero-text-wrapper p-4 rounded-3" style="background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(0px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                                            <span class="hero-badge badge bg-white text-dark mb-3 px-3 py-2">
                                                <i class="bi bi-emoji-smile-fill me-2"></i>
                                                Neonatología
                                            </span>
                                            <h2 class="hero-title display-3 fw-bold text-white mb-3">
                                                Super TORY<sup>®</sup> S2220
                                            </h2>
                                            <h3 class="hero-subtitle h4 text-white mb-4">
                                                Realismo neonatal al máximo
                                            </h3>
                                            <ul class="hero-features list-unstyled text-white mb-4">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-light me-2"></i> Simulación avanzada del recién nacido</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-light me-2"></i> Movimientos faciales y expresiones realistas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-light me-2"></i> Signos vitales y reacciones en tiempo real</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-light me-2"></i> Respuesta real al soporte ventilatorio</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-light me-2"></i> Operación totalmente inalámbrica y portátil</li>
                                            </ul>
                                            <div class="hero-actions d-flex flex-wrap gap-3">
                                                <a href="#newsletter" class="btn btn-light btn-lg px-5">
                                                    Contactar Ahora
                                                </a>
                                                <a href="#productos" class="btn btn-outline-light btn-lg px-5">
                                                    Más Información
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 6: SUSIE® S2400 -->
                <div class="swiper-slide hero-slide">
                    <picture class="hero-slide-image">
                        <source srcset="<?php echo imageUrl('hero/hero-susie-s2400.webp'); ?>" type="image/webp">
                        <img src="<?php echo imageUrl('hero/hero-susie-s2400.jpg'); ?>" alt="SUSIE S2400 Simulador de Enfermería" loading="lazy">
                    </picture>
                    <div class="hero-slide-bg" style="background: transparent;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
                                        <div class="hero-text-wrapper p-4 rounded-3" style="background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(0px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                                            <span class="hero-badge badge bg-white text-dark mb-3 px-3 py-2">
                                                <i class="bi bi-person-hearts me-2"></i>
                                                Enfermería
                                            </span>
                                            <h2 class="hero-title display-3 fw-bold text-white mb-3">
                                                SUSIE<sup>®</sup> S2400
                                            </h2>
                                            <h3 class="hero-subtitle h4 text-white mb-4">
                                                Simulación integral para el cuidado del paciente
                                            </h3>
                                            <ul class="hero-features list-unstyled text-white mb-4">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i> Entrenamiento en enfermería y salud aliada</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i> Exploración ginecológica y 7 senos con patologías</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i> Escenarios SLE™ multidisciplinarios</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i> Fisiología y signos vitales dinámicos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i> Reconocimiento automático de medicamentos</li>
                                            </ul>
                                            <div class="hero-actions d-flex flex-wrap gap-3">
                                                <a href="#newsletter" class="btn btn-light btn-lg px-5">
                                                    Agendar Demo
                                                </a>
                                                <a href="#productos" class="btn btn-outline-light btn-lg px-5">
                                                    Especificaciones
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
            
            <!-- Scroll Indicator -->
            <div class="hero-scroll-indicator">
                <a href="#aliados" class="scroll-down">
                    <span></span>
                    <span></span>
                    <span></span>
                </a>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         SOCIAL PROOF (ALIADOS)
         ======================================== -->
    <section id="aliados" class="section-aliados py-5 bg-light">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge bg-primary text-white px-3 py-2 mb-3">
                    <i class="bi bi-building-fill-check me-2"></i>
                    Partners Globales
                </span>
                <h2 class="section-title mb-3">Nuestros Aliados Estratégicos</h2>
                <p class="section-subtitle text-muted">
                    Trabajamos con las marcas más reconocidas en tecnología de simulación médica
                </p>
            </div>
            
            <!-- Logos Carousel -->
            <div class="aliados-carousel-wrapper mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper aliados-swiper">
                    <div class="swiper-wrapper align-items-center">
                        
                        <!-- Logo 1: Gaumard Scientific -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/1-Gaumard.webp'); ?>" 
                                         alt="Gaumard Scientific" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 2: Kyoto Kagaku -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/2-Kyoto-Kagaku.webp'); ?>" 
                                         alt="Kyoto Kagaku" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 3: Anatomage -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/3-Anatomage.webp'); ?>" 
                                         alt="Anatomage" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 4: Rudiger Anatomie -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/4-Rudiger.webp'); ?>" 
                                         alt="Rudiger Anatomie" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 5: Simulab -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/5-Simulab.webp'); ?>" 
                                         alt="Simulab" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 6: 3D Med -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/6-3D-Med.webp'); ?>" 
                                         alt="3D Med" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 7: 3B Scientific -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/7-3B Scientific.webp'); ?>" 
                                         alt="3B Scientific" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 8: Adam Rouilly -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/8-Adam Rouilly.webp'); ?>" 
                                         alt="Adam Rouilly" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 9: Erler Zimmer -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/9-Erler-Zimmer.webp'); ?>" 
                                         alt="Erler Zimmer" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 10: TruCorp -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/10-TrueCorp.webp'); ?>" 
                                         alt="TruCorp" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 11: SimX -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/11-SimX.webp'); ?>" 
                                         alt="SimX" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 12: VATA -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/12-VATA.webp'); ?>" 
                                         alt="VATA Inc" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 13: Medical X -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/13-Medical X.webp'); ?>" 
                                         alt="Medical-X" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 14: Immersive -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/14-immersive.webp'); ?>" 
                                         alt="Immersive Healthcare" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 15: Saratoga -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/15-Saratoga.webp'); ?>" 
                                         alt="Saratoga Dental" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 16: Nasco Healthcare -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/16-Nasco Healthcare.webp'); ?>" 
                                         alt="Nasco Healthcare" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 17: Safeguard Medical -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/17-Safeguard Medical (Simbodies).webp'); ?>" 
                                         alt="Safeguard Medical - SimBodies" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo 18: Lifecast -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/18-Lifecast.webp'); ?>" 
                                         alt="Lifecast" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                    
                        
                        <!-- Logo 19: Keklikoğlu -->
                        <div class="swiper-slide">
                            <div class="aliado-card">
                                <div class="aliado-logo-wrapper">
                                    <img src="<?php echo imageUrl('aliados/19-KEKLIGOKLU.webp'); ?>" 
                                         alt="Keklikoğlu" 
                                         class="aliado-logo"
                                         loading="lazy">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Testimonios -->
            <div class="testimonios-section mt-5 pt-4" data-aos="fade-up" data-aos-delay="200">
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8 text-center">
                        <h3 class="h4 fw-bold mb-3">Lo que dicen nuestros clientes</h3>
                        <p class="text-muted">Experiencias reales de instituciones que confían en nosotros</p>
                    </div>
                </div>
                
                <div class="swiper testimonios-swiper">
                    <div class="swiper-wrapper pb-4">
                        
                        <!-- Testimonio 1 -->
                        <div class="swiper-slide">
                            <div class="testimonio-card">
                                <div class="testimonio-header mb-3">
                                    <div class="testimonio-stars mb-2">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <i class="bi bi-quote quote-icon text-primary"></i>
                                </div>
                                <p class="testimonio-text">
                                    "Los simuladores de Aramed han transformado completamente nuestra forma de enseñar medicina. 
                                    El nivel de realismo y la calidad del soporte técnico son excepcionales. 
                                    Nuestros estudiantes ahora tienen la confianza necesaria antes de practicar con pacientes reales."
                                </p>
                                <div class="testimonio-footer">
                                    <div class="testimonio-author">
                                        <strong class="d-block">Dr. Roberto Martínez</strong>
                                        <span class="text-muted small">Director de Simulación Clínica</span>
                                        <span class="text-primary small d-block mt-1">Universidad Nacional Autónoma de México</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonio 2 -->
                        <div class="swiper-slide">
                            <div class="testimonio-card">
                                <div class="testimonio-header mb-3">
                                    <div class="testimonio-stars mb-2">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <i class="bi bi-quote quote-icon text-primary"></i>
                                </div>
                                <p class="testimonio-text">
                                    "La asesoría y el acompañamiento de Aramed fueron fundamentales para el desarrollo de nuestro 
                                    centro de simulación. No solo nos vendieron equipos, nos ayudaron a crear un programa educativo 
                                    completo. Su experiencia de más de 20 años marca la diferencia."
                                </p>
                                <div class="testimonio-footer">
                                    <div class="testimonio-author">
                                        <strong class="d-block">Dra. Ana Gutiérrez</strong>
                                        <span class="text-muted small">Coordinadora de Enfermería</span>
                                        <span class="text-primary small d-block mt-1">Instituto Tecnológico de Monterrey</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonio 3 -->
                        <div class="swiper-slide">
                            <div class="testimonio-card">
                                <div class="testimonio-header mb-3">
                                    <div class="testimonio-stars mb-2">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <i class="bi bi-quote quote-icon text-primary"></i>
                                </div>
                                <p class="testimonio-text">
                                    "El mantenimiento preventivo y la capacitación continua que ofrece Aramed garantizan que nuestros 
                                    simuladores siempre estén en óptimas condiciones. Su servicio posventa es incomparable. 
                                    Son verdaderos socios en la educación médica."
                                </p>
                                <div class="testimonio-footer">
                                    <div class="testimonio-author">
                                        <strong class="d-block">Dr. Carlos Hernández</strong>
                                        <span class="text-muted small">Jefe de Enseñanza</span>
                                        <span class="text-primary small d-block mt-1">Hospital General de México</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            
            <!-- Stats Bar -->
            <div class="stats-bar mt-5 pt-4" data-aos="fade-up" data-aos-delay="300">
                <div class="row g-4 text-center">
                    <div class="col-6 col-md-4">
                        <div class="stat-box">
                            <h3 class="stat-number text-primary mb-2" data-target="20">0</h3>
                            <p class="stat-label text-muted mb-0">Años de Experiencia</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="stat-box">
                            <h3 class="stat-number text-primary mb-2" data-target="21">0</h3>
                            <p class="stat-label text-muted mb-0">Marcas Representadas</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="stat-box">
                            <h3 class="stat-number text-primary mb-2" data-target="100">0</h3>
                            <p class="stat-label text-muted mb-0">% Satisfacción</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
    
    <!-- ========================================
         OFERTA (SERVICES)
         ======================================== -->
    <section id="servicios" class="section-services py-5">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge bg-primary text-white px-3 py-2 mb-3">
                    <i class="bi bi-gear-fill me-2"></i>
                    Nuestros Servicios
                </span>
                <h2 class="section-title mb-3">Soluciones Integrales para Educación Médica</h2>
                <p class="section-subtitle text-muted mx-auto" style="max-width: 700px;">
                    Más de 20 años de experiencia ofreciendo servicios especializados que garantizan 
                    el éxito de su centro de simulación médica
                </p>
            </div>
            
            <!-- Services Grid -->
            <div class="row g-4">
                
                <!-- Service 1: Diseño y Desarrollo -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card h-100">
                        <div class="service-icon-wrapper">
                            <div class="service-icon bg-primary">
                                <img src="<?php echo imageUrl('iconos/iconos-01.png'); ?>" alt="Diseño y Desarrollo" class="service-icon-image">
                            </div>
                        </div>
                        <h3 class="service-title">Diseño y Desarrollo</h3>
                        <p class="service-description">
                            Diseñamos y planificamos centros de simulación médica completos, desde la conceptualización hasta la implementación. Incluye planificación arquitectónica, distribución de espacios y selección de equipamiento.
                        </p>
                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill text-primary"></i> Diseño Arquitectónico especializado</li>
                            <li><i class="bi bi-check-circle-fill text-primary"></i> Distribución óptima de espacios</li>
                            <li><i class="bi bi-check-circle-fill text-primary"></i> Selección de equipamiento</li>
                            <li><i class="bi bi-check-circle-fill text-primary"></i> Instalación y puesta en marcha</li>
                        </ul>
                        <a href="#newsletter" class="btn btn-outline-primary w-100 mt-auto service-cta">
                            Solicitar Cotización
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 2: Mantenimiento Preventivo -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card h-100 featured">
                        <div class="featured-badge">
                            <i class="bi bi-star-fill me-1"></i> Más Solicitado
                        </div>
                        <div class="service-icon-wrapper">
                            <div class="service-icon bg-success">
                                <img src="<?php echo imageUrl('iconos/iconos-02.png'); ?>" alt="Mantenimiento Preventivo" class="service-icon-image">
                            </div>
                        </div>
                        <h3 class="service-title">Mantenimiento Preventivo</h3>
                        <p class="service-description">
                            Programas de mantenimiento preventivo y correctivo que aseguran el funcionamiento óptimo de sus simuladores. Extendemos la vida útil de su inversión con servicios técnicos especializados.
                        </p>
                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill text-success"></i> Revisiones periódicas programadas</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Reparaciones y refacciones (correctivo)</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Actualización de software</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Soporte técnico prioritario</li>
                        </ul>
                        <a href="#newsletter" class="btn btn-success w-100 mt-auto service-cta">
                            Agendar Mantenimiento
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 3: Mantenimiento Correctivo -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="service-card h-100">
                        <div class="service-icon-wrapper">
                            <div class="service-icon bg-warning">
                                <img src="<?php echo imageUrl('iconos/iconos-04.png'); ?>" alt="Mantenimiento Correctivo" class="service-icon-image">
                            </div>
                        </div>
                        <h3 class="service-title">Mantenimiento Correctivo</h3>
                        <p class="service-description">
                            Servicio especializado que soluciona fallas o averías en equipos de simulación médica. Brindamos atención ágil y profesional, utilizando refacciones originales y procedimientos certificados para restablecer el funcionamiento óptimo del sistema.
                        </p>
                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Reparación de módulos y componentes electrónicos</li>
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Sustitución de piezas y refacciones originales</li>
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Diagnóstico técnico especializado en sitio</li>
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Soporte técnico prioritario hasta la resolución completa</li>
                        </ul>
                        <a href="#newsletter" class="btn btn-outline-warning w-100 mt-auto service-cta">
                            Solicitar Servicio
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 4: Asesoría en Simulación -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card h-100">
                        <div class="service-icon-wrapper">
                            <div class="service-icon bg-secondary">
                                <img src="<?php echo imageUrl('iconos/iconos-05.png'); ?>" alt="Asesoría en Simulación" class="service-icon-image">
                            </div>
                        </div>
                        <h3 class="service-title">Asesoría en Simulación</h3>
                        <p class="service-description">
                            Brindamos apoyo integral en la planeación, selección e implementación de programas educativos de simulación. Asesoramos en la elección de simuladores según cada especialidad médica y en la modernización o ampliación de centros existentes.
                        </p>
                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill text-secondary"></i> Selección de simuladores por especialidad médica</li>
                            <li><i class="bi bi-check-circle-fill text-secondary"></i> Diseño y optimización de centros de simulación</li>
                            <li><i class="bi bi-check-circle-fill text-secondary"></i> Integración de nuevas tecnologías y equipos</li>
                            <li><i class="bi bi-check-circle-fill text-secondary"></i> Actualización y modernización de laboratorios existentes</li>
                        </ul>
                        <a href="#newsletter" class="btn btn-outline-secondary w-100 mt-auto service-cta">
                            Solicitar Asesoría
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 5: Capacitación -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-card h-100">
                        <div class="service-icon-wrapper">
                            <div class="service-icon bg-warning">
                                <img src="<?php echo imageUrl('iconos/iconos-04.png'); ?>" alt="Capacitación y Entrenamiento" class="service-icon-image">
                            </div>
                        </div>
                        <h3 class="service-title">Capacitación y Entrenamiento</h3>
                        <p class="service-description">
                            Ofrecemos programas de capacitación dirigidos a docentes y personal técnico, orientados al uso eficiente de simuladores y a la aplicación de estrategias didácticas en entornos de simulación médica.
                        </p>
                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Capacitación técnica especializada en simuladores</li>
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Entrenamiento en operación y mantenimiento de equipos</li>
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Asesoría en prácticas de enseñanza con simulación</li>
                            <li><i class="bi bi-check-circle-fill text-warning"></i> Exposiciones en eventos académicos y de innovación médica</li>
                        </ul>
                        <a href="#newsletter" class="btn btn-outline-warning w-100 mt-auto service-cta">
                            Ver Calendario
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 6: Atención a Cliente -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="450">
                    <div class="service-card h-100">
                        <div class="service-icon-wrapper">
                            <div class="service-icon bg-dark">
                                <img src="<?php echo imageUrl('iconos/iconos-01.png'); ?>" alt="Atención a Cliente" class="service-icon-image">
                            </div>
                        </div>
                        <h3 class="service-title">Atención a Cliente</h3>
                        <p class="service-description">
                            Ofrecemos acompañamiento personalizado para orientar a cada institución en la selección de simuladores y soluciones educativas que respondan a sus necesidades académicas.
                        </p>
                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill text-dark"></i> Cotizaciones personalizadas</li>
                            <li><i class="bi bi-check-circle-fill text-dark"></i> Asesoría técnica y comercial</li>
                            <li><i class="bi bi-check-circle-fill text-dark"></i> Seguimiento de proyectos</li>
                            <li><i class="bi bi-check-circle-fill text-dark"></i> Soporte postventa continuo</li>
                        </ul>
                        <a href="#newsletter" class="btn btn-outline-dark w-100 mt-auto service-cta">
                            Contactar Asesor
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 7: Financiamiento -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="service-card h-100">
                        <div class="service-icon-wrapper">
                            <div class="service-icon bg-danger">
                                <img src="<?php echo imageUrl('iconos/iconos-05.png'); ?>" alt="Opciones de Financiamiento" class="service-icon-image">
                            </div>
                        </div>
                        <h3 class="service-title">Opciones de Financiamiento</h3>
                        <p class="service-description">
                            Facilitamos el acceso a tecnología de simulación médica con opciones de financiamiento flexibles. 
                            Planes de pago personalizados que se adaptan al presupuesto de su institución.
                        </p>
                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill text-danger"></i> Planes de pago flexibles</li>
                            <li><i class="bi bi-check-circle-fill text-danger"></i> Arrendamiento de equipos</li>
                            <li><i class="bi bi-check-circle-fill text-danger"></i> Financiamiento a largo plazo</li>
                            <li><i class="bi bi-check-circle-fill text-danger"></i> Asesoría financiera especializada</li>
                        </ul>
                        <a href="#newsletter" class="btn btn-outline-danger w-100 mt-auto service-cta">
                            Solicitar Información
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                
            </div>
            
            <!-- CTA Section -->
            <div class="row mt-5 pt-4">
                <div class="col-lg-10 mx-auto" data-aos="fade-up" data-aos-delay="600">
                    <div class="services-cta-box">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <h3 class="h4 mb-2 fw-bold">¿Necesitas una solución personalizada?</h3>
                                <p class="text-muted mb-0">
                                    Contáctanos para diseñar un paquete de servicios adaptado a las necesidades 
                                    específicas de tu institución educativa.
                                </p>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <a href="#newsletter" class="btn btn-primary btn-lg px-4">
                                    <i class="bi bi-chat-dots-fill me-2"></i>
                                    Hablar con un Asesor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
    
    <!-- ========================================
         PRODUCTOS DESTACADOS
         ======================================== -->
    <section id="productos" class="section-productos py-5 bg-light">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge bg-primary text-white px-3 py-2 mb-3">
                    <i class="bi bi-box-seam me-2"></i>
                    Productos Destacados
                </span>
                <h2 class="section-title mb-3">Tecnología de Simulación de Vanguardia</h2>
                <p class="section-subtitle text-muted mx-auto" style="max-width: 700px;">
                    Descubre nuestra selección de simuladores médicos de alta fidelidad, 
                    diseñados para transformar la educación en ciencias de la salud
                </p>
            </div>
            
            <!-- Product 1: ANATOMAGE TABLE (Imagen Izquierda) -->
            <div class="product-showcase mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 order-lg-1">
                        <div class="product-image-wrapper">
                            <div class="product-badge">
                                <i class="bi bi-star-fill me-1"></i>
                                Más Vendido
                            </div>
                            <picture>
                                <source srcset="<?php echo imageUrl('productos/anatomage-table.webp'); ?>" type="image/webp">
                                <img src="<?php echo imageUrl('productos/anatomage-table.jpg'); ?>" 
                                     alt="Anatomage Table - Plataforma de educación médica" 
                                     class="product-image"
                                     loading="lazy">
                            </picture>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-2">
                        <div class="product-content">
                            <div class="d-flex align-items-center mb-3">
                                <span class="product-category me-3">Plataforma Educativa</span>
                                <img src="<?php echo imageUrl('aliados/3-Anatomage.webp'); ?>" 
                                     alt="Anatomage" 
                                     class="company-logo"
                                     style="height: 30px; width: auto;">
                            </div>
                            <h3 class="product-title">ANATOMAGE TABLE</h3>
                            <p class="product-subtitle">Revoluciona la enseñanza médica con Anatomage Table</p>
                            <p class="product-description">
                                La Anatomage Table es la plataforma de educación médica más avanzada basada en cuerpos humanos reales digitalizados. Su tecnología de visualización 3D permite a los estudiantes explorar la anatomía, la fisiología y las patologías en tamaño real, con una precisión sumamente realista.
                            </p>
                            <ul class="product-features-list">
                                <li><i class="bi bi-check-circle-fill text-primary"></i> Visualización 3D de cuerpos humanos reales digitalizados</li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> Herramientas interactivas de disección virtual</li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> Simulaciones clínicas avanzadas</li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> Visor DICOM integrado</li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> Aprendizaje práctico sin laboratorios tradicionales</li>
                            </ul>
                            <div class="product-actions">
                                <a href="#newsletter" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Solicitar Cotización
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 2: IMMERSE INTERACTIVE (Imagen Derecha) -->
            <div class="product-showcase mb-5 product-reverse" data-aos="fade-up" data-aos-delay="200">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 order-lg-2">
                        <div class="product-image-wrapper">
                            <div class="product-badge bg-info">
                                <i class="bi bi-vr me-1"></i>
                                Inmersivo
                            </div>
                            <picture>
                                <source srcset="<?php echo imageUrl('productos/immersive-echo.webp'); ?>" type="image/webp">
                                <img src="<?php echo imageUrl('productos/immersive-echo.jpg'); ?>" 
                                     alt="Immersive Interactive - Entornos inmersivos" 
                                     class="product-image"
                                     loading="lazy">
                            </picture>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="product-content">
                            <div class="d-flex align-items-center mb-3">
                                <span class="product-category me-3">Realidad Inmersiva</span>
                                <img src="<?php echo imageUrl('aliados/14-immersive.webp'); ?>" 
                                     alt="Immersive Interactive" 
                                     class="company-logo"
                                     style="height: 30px; width: auto;">
                            </div>
                            <h3 class="product-title">IMMERSE INTERACTIVE</h3>
                            <p class="product-subtitle">Transforma la educación médica con entornos inmersivos y realistas</p>
                            <p class="product-description">
                                El sistema Immersive Interactive de Echo Healthcare convierte cualquier aula o espacio en un entorno virtual envolvente, multisensorial e interactivo, diseñado para fomentar el aprendizaje activo en estudiantes de medicina.
                            </p>
                            <ul class="product-features-list">
                                <li><i class="bi bi-check-circle-fill text-info"></i> Entorno virtual envolvente y multisensorial</li>
                                <li><i class="bi bi-check-circle-fill text-info"></i> Tecnología sin gafas ni auriculares</li>
                                <li><i class="bi bi-check-circle-fill text-info"></i> Escenarios clínicos realistas</li>
                                <li><i class="bi bi-check-circle-fill text-info"></i> Estimula la toma de decisiones y colaboración</li>
                                <li><i class="bi bi-check-circle-fill text-info"></i> Mejora la retención del conocimiento</li>
                            </ul>
                            <div class="product-actions">
                                <a href="#newsletter" class="btn btn-info text-white btn-lg">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Solicitar Cotización
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 3: LIFECAST (Imagen Izquierda) -->
            <div class="product-showcase mb-5" data-aos="fade-up" data-aos-delay="300">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 order-lg-1">
                        <div class="product-image-wrapper">
                            <div class="product-badge bg-warning">
                                <i class="bi bi-heart-fill me-1"></i>
                                Pediátrico
                            </div>
                            <picture>
                                <source srcset="<?php echo imageUrl('productos/lifecast.webp'); ?>" type="image/webp">
                                <img src="<?php echo imageUrl('productos/lifecast.jpg'); ?>" 
                                     alt="Lifecast - Simulación pediátrica realista" 
                                     class="product-image"
                                     loading="lazy">
                            </picture>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-2">
                        <div class="product-content">
                            <div class="d-flex align-items-center mb-3">
                                <span class="product-category me-3">Simulación Pediátrica</span>
                                <img src="<?php echo imageUrl('aliados/7-Lifecast.webp'); ?>" 
                                     alt="Lifecast" 
                                     class="company-logo"
                                     style="height: 30px; width: auto;">
                            </div>
                            <h3 class="product-title">LIFECAST</h3>
                            <p class="product-subtitle">Realismo y precisión en simulación pediátrica</p>
                            <p class="product-description">
                                Diseñados para ofrecer una experiencia de capacitación médica inigualable, los maniquíes Lifecast para niños pequeños y niños brindan un nivel de realismo anatómico y funcional que transforma la enseñanza y la práctica clínica.
                            </p>
                            <ul class="product-features-list">
                                <li><i class="bi bi-check-circle-fill text-warning"></i> Realismo anatómico y funcional superior</li>
                                <li><i class="bi bi-check-circle-fill text-warning"></i> Ahogamiento pulmonar húmedo y seco</li>
                                <li><i class="bi bi-check-circle-fill text-warning"></i> Hemorragia torácica y sangría</li>
                                <li><i class="bi bi-check-circle-fill text-warning"></i> Efectos de vómito realistas</li>
                                <li><i class="bi bi-check-circle-fill text-warning"></i> Escenarios de rescate acuático</li>
                                <li><i class="bi bi-check-circle-fill text-warning"></i> Emergencias pediátricas complejas</li>
                            </ul>
                            <div class="product-actions">
                                <a href="#newsletter" class="btn btn-warning text-white btn-lg">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Solicitar Cotización
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 4: ADAM-X (Imagen Derecha) -->
            <div class="product-showcase product-reverse" data-aos="fade-up" data-aos-delay="400">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 order-lg-2">
                        <div class="product-image-wrapper">
                            <div class="product-badge bg-danger">
                                <i class="bi bi-person-fill me-1"></i>
                                Adulto
                            </div>
                            <picture>
                                <source srcset="<?php echo imageUrl('productos/adam-x.webp'); ?>" type="image/webp">
                                <img src="<?php echo imageUrl('productos/adam-x.jpg'); ?>" 
                                     alt="ADAM-X - Simulador de paciente adulto" 
                                     class="product-image"
                                     loading="lazy">
                            </picture>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="product-content">
                            <div class="d-flex align-items-center mb-3">
                                <span class="product-category me-3">Simulación Clínica</span>
                                <img src="<?php echo imageUrl('aliados/13-Medical X.webp'); ?>" 
                                     alt="Medical X" 
                                     class="company-logo"
                                     style="height: 30px; width: auto;">
                            </div>
                            <h3 class="product-title">ADAM-X</h3>
                            <p class="product-subtitle">Simulación clínica avanzada con el realismo total de ADAM-X</p>
                            <p class="product-description">
                                ADAM-X Xtreme es un simulador de paciente adulto de alta fidelidad que reproduce fielmente la anatomía y fisiología humana. Destaca por su realismo extremo, con parpadeo, sudoración, secreciones, respiración espontánea y pulsos sincronizados.
                            </p>
                            <ul class="product-features-list">
                                <li><i class="bi bi-check-circle-fill text-danger"></i> Realismo extremo con parpadeo y sudoración</li>
                                <li><i class="bi bi-check-circle-fill text-danger"></i> Secreciones y respiración espontánea</li>
                                <li><i class="bi bi-check-circle-fill text-danger"></i> Pulsos sincronizados y realistas</li>
                                <li><i class="bi bi-check-circle-fill text-danger"></i> Control táctil Command-X</li>
                                <li><i class="bi bi-check-circle-fill text-danger"></i> Escenarios clínicos personalizados</li>
                                <li><i class="bi bi-check-circle-fill text-danger"></i> Entrenamiento integral en vía aérea, RCP y ventilación</li>
                            </ul>
                            <div class="product-actions">
                                <a href="#newsletter" class="btn btn-danger btn-lg">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Solicitar Cotización
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Ver Más Productos -->
            <div class="row mt-5 pt-4">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up" data-aos-delay="500">
                    <div class="productos-cta-box">
                        <h3 class="h4 mb-3 fw-bold">¿Buscas algo más específico?</h3>
                        <p class="text-muted mb-4">
                            Contamos con más de 500 simuladores médicos para diferentes especialidades. 
                            Explora nuestro catálogo completo o consúltanos para encontrar la solución perfecta.
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="#" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-grid-3x3-gap me-2"></i>
                                Ver Catálogo Completo
                            </a>
                            <a href="#newsletter" class="btn btn-outline-primary btn-lg px-4">
                                <i class="bi bi-chat-left-text me-2"></i>
                                Consultar Asesor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
    
    <!-- ========================================
         ALIADOS ESTRATÉGICOS - DETALLE
         ======================================== -->
    <section id="aliados-detalle" class="section-aliados-detalle py-5 bg-light">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">
                    <i class="bi bi-award-fill me-2"></i>
                    Nuestros Aliados
                </span>
                <h2 class="section-title mb-3">Aliados Estratégicos</h2>
                <p class="section-subtitle mx-auto" style="max-width: 800px;">
                    Trabajamos con los líderes mundiales en simulación médica para ofrecerte 
                    las mejores soluciones educativas y tecnología de vanguardia.
                </p>
            </div>
            
            <!-- Carrusel de Aliados -->
            <div class="aliados-detalle-swiper swiper">
                <div class="swiper-wrapper">
                    
                    <!-- Aliado 1: GAUMARD -->
                    <div class="swiper-slide">
                        <div class="aliado-detalle-card h-100">
                            <div class="aliado-logo-wrapper">
                                <img src="<?php echo imageUrl('aliados/1-Gaumard.webp'); ?>" 
                                     alt="Gaumard Scientific" 
                                     class="aliado-logo"
                                     loading="lazy">
                            </div>
                            <div class="aliado-info">
                                <h4 class="aliado-name">GAUMARD</h4>
                                <p class="aliado-description">
                                    Gaumard Scientific desarrolla simuladores médicos de alta fidelidad que transforman 
                                    la enseñanza clínica. Su innovación tecnológica complementa nuestra misión de ofrecer 
                                    experiencias de aprendizaje realistas y seguras en salud.
                                </p>
                            </div>
                        </div>
                    </div>
                
                    <!-- Aliado 2: MEDICAL X -->
                    <div class="swiper-slide">
                        <div class="aliado-detalle-card h-100">
                            <div class="aliado-logo-wrapper">
                                <img src="<?php echo imageUrl('aliados/13-Medical X.webp'); ?>" 
                                     alt="Medical-X" 
                                     class="aliado-logo"
                                     loading="lazy">
                            </div>
                            <div class="aliado-info">
                                <h4 class="aliado-name">MEDICAL X</h4>
                                <p class="aliado-description">
                                    Medical-X desarrolla simuladores médicos de alta fidelidad para entrenamiento clínico. 
                                    Su tecnología avanzada potencia a Aramed en formación realista y segura.
                                </p>
                            </div>
                        </div>
                    </div>
                
                    <!-- Aliado 3: ANATOMAGE -->
                    <div class="swiper-slide">
                        <div class="aliado-detalle-card h-100">
                            <div class="aliado-logo-wrapper">
                                <img src="<?php echo imageUrl('aliados/3-Anatomage.webp'); ?>" 
                                     alt="Anatomage" 
                                     class="aliado-logo"
                                     loading="lazy">
                            </div>
                            <div class="aliado-info">
                                <h4 class="aliado-name">ANATOMAGE</h4>
                                <p class="aliado-description">
                                    Anatomage crea plataformas 3D interactivas que revolucionan la enseñanza anatómica 
                                    mediante visualizaciones precisas del cuerpo humano. Su innovación eleva nuestros 
                                    estándares en simulación médica educativa.
                                </p>
                            </div>
                        </div>
                    </div>
                
                <!-- Aliado 4: SARATOGA -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/15-Saratoga.webp'); ?>" 
                                 alt="Saratoga Dental" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">SARATOGA</h4>
                            <p class="aliado-description">
                                Saratoga Dental diseña y fabrica equipos dentales, laboratorios técnicos y 
                                simuladores formativos. Su enfoque "a medida" refuerza nuestra oferta educativa 
                                con soluciones profesionales y personalizadas.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 5: 3B SCIENTIFIC -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/7-3B Scientific.webp'); ?>" 
                                 alt="3B Scientific" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">3B SCIENTIFIC</h4>
                            <p class="aliado-description">
                                3B Scientific fabrica modelos anatómicos y simuladores médicos para educación en salud. 
                                Su calidad global refuerza nuestra oferta educativa y credibilidad como aliado estratégico.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 6: 3D Med -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/6-3D-Med.webp'); ?>" 
                                 alt="3-Dmed" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">3D MED</h4>
                            <p class="aliado-description">
                                3-Dmed diseña simuladores quirúrgicos y entrenadores médicos de alta precisión. 
                                Su enfoque en realismo y desempeño mejora nuestras soluciones para la práctica 
                                clínica y educativa.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 7: SIMBODIES (SafeGuard) -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/17-Safeguard Medical (Simbodies).webp'); ?>" 
                                 alt="Safeguard Medical - SimBodies" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">SAFEGUARD / SIMBODIES</h4>
                            <p class="aliado-description">
                                Safeguard Medical provee tecnología, equipamiento y entrenamiento en medicina de emergencia. 
                                Su enfoque en salvamento y realismo fortalece nuestro respaldo en formación crítica.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 8: STRATEGIC OPERATIONS -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/21-strategic-operations.webp'); ?>" 
                                 alt="Strategic Operations" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">STRATEGIC OPERATIONS</h4>
                            <p class="aliado-description">
                                Strategic Operations desarrolla simuladores quirúrgicos de alta fidelidad que replican 
                                con exactitud la anatomía humana y las condiciones del quirófano. Gracias a esta alianza, 
                                potenciamos nuestra capacidad para brindar capacitación avanzada en entornos controlados.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 9: KYOTO KAGAKU -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/2-Kyoto-Kagaku.webp'); ?>" 
                                 alt="Kyoto Kagaku" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">KYOTO KAGAKU</h4>
                            <p class="aliado-description">
                                Kyoto Kagaku fabrica modelos anatómicos, simuladores y "phantoms" para imagen médica. 
                                Su precisión e innovación fortalecen nuestra excelencia educativa y liderazgo en simulación.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 10: SIMX -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/11-SimX.webp'); ?>" 
                                 alt="SimX" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">SIMX</h4>
                            <p class="aliado-description">
                                SimX desarrolla simulaciones médicas en realidad virtual inmersiva que entrenan juicio 
                                clínico realista. Su innovación potencia nuestra oferta formativa de alto impacto.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 11: NASCO -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/16-Nasco Healthcare.webp'); ?>" 
                                 alt="Nasco Healthcare" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">NASCO</h4>
                            <p class="aliado-description">
                                Nasco Healthcare provee simuladores clínicos, maniquíes y herramientas de entrenamiento 
                                para emergencias y cuidados avanzados. Su oferta robustece nuestra formación con 
                                tecnología confiable.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 12: TRUCORP -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/10-TrueCorp.webp'); ?>" 
                                 alt="TruCorp" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">TRUCORP</h4>
                            <p class="aliado-description">
                                TruCorp fabrica maniquíes y simuladores médicos con retroalimentación en tiempo real 
                                para entrenamiento clínico. Su realismo y precisión elevan nuestra formación práctica 
                                y eficacia educativa.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 13: ERLER ZIMMER -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/9-Erler-Zimmer.webp'); ?>" 
                                 alt="Erler-Zimmer" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">ERLER ZIMMER</h4>
                            <p class="aliado-description">
                                Erler-Zimmer diseña modelos anatómicos y simuladores médicos con altísima calidad histórica. 
                                Su innovación y rigor elevan nuestra formación práctica con precisión educativa.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 14: VATA -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/12-VATA.webp'); ?>" 
                                 alt="VATA Inc." 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">VATA</h4>
                            <p class="aliado-description">
                                VATA Inc. desarrolla herramientas de simulación médica realistas (acceso vascular, heridas, 
                                modelos de ultrasonido). Su precisión eleva nuestras prácticas clínicas y fortalece 
                                nuestra formación.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 15: ADAM ROUILLY -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/8-Adam Rouilly.webp'); ?>" 
                                 alt="Adam Rouilly" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">ADAM ROUILLY</h4>
                            <p class="aliado-description">
                                AdamRouilly diseña desde 1918 modelos anatómicos, simuladores clínicos y herramientas 
                                formativas. Su legado, innovación y versatilidad enriquecen nuestro portafolio educativo.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 16: RUDIGER -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/4-Rudiger.webp'); ?>" 
                                 alt="Rüdiger Anatomie" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">RUDIGER</h4>
                            <p class="aliado-description">
                                Rüdiger Anatomie produce modelos anatómicos y pósters educativos "Made in Germany" con 
                                manufactura artesanal. Su precisión y autenticidad enriquecen nuestra enseñanza de 
                                ciencias de la salud.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 17: ECHO HEALTHCARE -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/7-Echo Healthcare.webp'); ?>" 
                                 alt="Echo Healthcare" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">ECHO HEALTHCARE</h4>
                            <p class="aliado-description">
                                Echo Healthcare desarrolla soluciones inmersivas y realistas para simulación médica 
                                (maniquíes, máscaras, entornos interactivos). Su innovación eleva nuestra oferta 
                                formativa con un enfoque de alto impacto.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 18: LIFECAST -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/18-Lifecast.webp'); ?>" 
                                 alt="Lifecast" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">LIFECAST</h4>
                            <p class="aliado-description">
                                Lifecast desarrolla modelos anatómicos y simuladores médicos de alta fidelidad para 
                                educación en salud. Su compromiso con la calidad y realismo fortalece nuestra oferta 
                                educativa con soluciones innovadoras.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 19: IMMERSIVE -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/20-immersive.webp'); ?>" 
                                 alt="Immersive Healthcare" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">IMMERSIVE</h4>
                            <p class="aliado-description">
                                Immersive Healthcare desarrolla soluciones de simulación médica inmersiva que 
                                transforman la educación clínica mediante tecnología de realidad virtual y aumentada. 
                                Su innovación potencia nuestra formación con experiencias de aprendizaje únicas.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Aliado 20: KEKLIKOĞLU -->
                    <div class="swiper-slide">
                    <div class="aliado-detalle-card h-100">
                        <div class="aliado-logo-wrapper">
                            <img src="<?php echo imageUrl('aliados/19-KEKLIGOKLU.webp'); ?>" 
                                 alt="Keklikoğlu" 
                                 class="aliado-logo"
                                 loading="lazy">
                        </div>
                        <div class="aliado-info">
                            <h4 class="aliado-name">KEKLIKOĞLU</h4>
                            <p class="aliado-description">
                                Keklikoğlu desarrolla modelos anatómicos de alta fidelidad que elevan la enseñanza 
                                clínica y veterinaria. Su compromiso con calidad e innovación fortalece nuestra 
                                misión de aprendizaje seguro y realista.
                            </p>
                        </div>
                    </div>
                </div>
                
                </div>
                
                <!-- Navegación del carrusel -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
            
            <!-- CTA -->
            <div class="text-center mt-5" data-aos="fade-up">
                <p class="lead text-muted mb-4">
                    ¿Quieres conocer más sobre nuestras alianzas y productos?
                </p>
                <a href="#newsletter" 
                        class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-envelope-fill me-2"></i>
                    Contáctanos
                </a>
            </div>
            
        </div>
    </section>
    
    <!-- ========================================
         NEWSLETTER
         ======================================== -->
    <section id="newsletter" class="section-newsletter py-5 bg-primary text-white">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge bg-white text-primary px-3 py-2 mb-3">
                    <i class="bi bi-envelope-fill me-2"></i>
                    Contacto
                </span>
                <h2 class="section-title text-white mb-3">Mantente Informado</h2>
                <p class="section-subtitle text-white-75 mx-auto" style="max-width: 700px;">
                    Conoce todas las soluciones que podemos ofrecerte en tu área de enseñanza médica. 
                    Recibe información exclusiva sobre productos, eventos y capacitaciones.
                </p>
            </div>
            
            <!-- Newsletter Form -->
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="newsletter-form-wrapper" data-aos="fade-up" data-aos-delay="100">
                        
                        <!-- Success Message (hidden by default) -->
                        <div id="newsletter-success" class="alert alert-success d-none" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>¡Gracias por suscribirte!</strong> Pronto recibirás información relevante en tu correo.
                        </div>
                        
                        <!-- Error Message (hidden by default) -->
                        <div id="newsletter-error" class="alert alert-danger d-none" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error:</strong> <span id="newsletter-error-message">Hubo un problema al procesar tu solicitud.</span>
                        </div>
                        
                        <form id="newsletterForm" action="includes/newsletter_handler.php" method="POST" novalidate>
                            
                            <div class="row g-4">
                                
                                <!-- Institución -->
                                <div class="col-md-6">
                                    <label for="institucion" class="form-label">
                                        Institución <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="institucion" 
                                           name="institucion" 
                                           placeholder="Nombre de la institución" 
                                           required>
                                    <div class="invalid-feedback">Por favor ingresa el nombre de la institución.</div>
                                </div>
                                
                                <!-- Tipo de Institución -->
                                <div class="col-md-6">
                                    <label for="tipo_institucion" class="form-label">
                                        Tipo de Institución <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" 
                                            id="tipo_institucion" 
                                            name="tipo_institucion" 
                                            required>
                                        <option value="" selected disabled>Selecciona una opción</option>
                                        <option value="Hospital">Hospital</option>
                                        <option value="Escuela de salud">Escuela de salud</option>
                                        <option value="Enfermería">Enfermería</option>
                                        <option value="Institución gubernamental">Institución gubernamental</option>
                                    </select>
                                    <div class="invalid-feedback">Por favor selecciona el tipo de institución.</div>
                                </div>
                                
                                <!-- Campo Adicional (dinámico) -->
                                <div class="col-12 d-none" id="campo_adicional_wrapper">
                                    <label for="campo_adicional" class="form-label">
                                        Especifica el tipo de institución
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="campo_adicional" 
                                           name="campo_adicional" 
                                           placeholder="Ej: Universidad, Secretaría de Salud, etc.">
                                </div>
                                
                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label for="estado" class="form-label">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" 
                                            id="estado" 
                                            name="estado" 
                                            required>
                                        <option value="" selected disabled>Selecciona un estado</option>
                                        <option value="Aguascalientes">Aguascalientes</option>
                                        <option value="Baja California">Baja California</option>
                                        <option value="Baja California Sur">Baja California Sur</option>
                                        <option value="Campeche">Campeche</option>
                                        <option value="Chiapas">Chiapas</option>
                                        <option value="Chihuahua">Chihuahua</option>
                                        <option value="Ciudad de México">Ciudad de México</option>
                                        <option value="Coahuila">Coahuila</option>
                                        <option value="Colima">Colima</option>
                                        <option value="Durango">Durango</option>
                                        <option value="Guanajuato">Guanajuato</option>
                                        <option value="Guerrero">Guerrero</option>
                                        <option value="Hidalgo">Hidalgo</option>
                                        <option value="Jalisco">Jalisco</option>
                                        <option value="México">México</option>
                                        <option value="Michoacán">Michoacán</option>
                                        <option value="Morelos">Morelos</option>
                                        <option value="Nayarit">Nayarit</option>
                                        <option value="Nuevo León">Nuevo León</option>
                                        <option value="Oaxaca">Oaxaca</option>
                                        <option value="Puebla">Puebla</option>
                                        <option value="Querétaro">Querétaro</option>
                                        <option value="Quintana Roo">Quintana Roo</option>
                                        <option value="San Luis Potosí">San Luis Potosí</option>
                                        <option value="Sinaloa">Sinaloa</option>
                                        <option value="Sonora">Sonora</option>
                                        <option value="Tabasco">Tabasco</option>
                                        <option value="Tamaulipas">Tamaulipas</option>
                                        <option value="Tlaxcala">Tlaxcala</option>
                                        <option value="Veracruz">Veracruz</option>
                                        <option value="Yucatán">Yucatán</option>
                                        <option value="Zacatecas">Zacatecas</option>
                                    </select>
                                    <div class="invalid-feedback">Por favor selecciona un estado.</div>
                                </div>
                                
                                <!-- Ciudad -->
                                <div class="col-md-6">
                                    <label for="ciudad" class="form-label">
                                        Ciudad <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="ciudad" 
                                           name="ciudad" 
                                           placeholder="Ciudad" 
                                           required>
                                    <div class="invalid-feedback">Por favor ingresa la ciudad.</div>
                                </div>
                                
                                <!-- Nombre del Interesado -->
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">
                                        Nombre Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="nombre" 
                                           name="nombre" 
                                           placeholder="Tu nombre completo" 
                                           required>
                                    <div class="invalid-feedback">Por favor ingresa tu nombre completo.</div>
                                </div>
                                
                                <!-- Puesto -->
                                <div class="col-md-6">
                                    <label for="puesto" class="form-label">
                                        Puesto <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="puesto" 
                                           name="puesto" 
                                           placeholder="Tu puesto o cargo" 
                                           required>
                                    <div class="invalid-feedback">Por favor ingresa tu puesto.</div>
                                </div>
                                
                                <!-- Correo Oficial -->
                                <div class="col-md-6">
                                    <label for="email_oficial" class="form-label">
                                        Correo Oficial <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-lg" 
                                           id="email_oficial" 
                                           name="email_oficial" 
                                           placeholder="correo@institucion.com" 
                                           required>
                                    <div class="invalid-feedback">Por favor ingresa un correo oficial válido.</div>
                                </div>
                                
                                <!-- Correo Alterno -->
                                <div class="col-md-6">
                                    <label for="email_alterno" class="form-label">
                                        Correo Alterno <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-lg" 
                                           id="email_alterno" 
                                           name="email_alterno" 
                                           placeholder="correo@personal.com">
                                    <div class="invalid-feedback">Por favor ingresa un correo alterno válido.</div>
                                </div>
                                
                                <!-- Teléfono Oficina -->
                                <div class="col-md-6">
                                    <label for="telefono_oficina" class="form-label">
                                        Teléfono Oficina <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <input type="tel" 
                                               class="form-control" 
                                               id="telefono_oficina" 
                                               name="telefono_oficina" 
                                               placeholder="(555) 123-4567" 
                                               required>
                                        <input type="text" 
                                               class="form-control" 
                                               id="extension" 
                                               name="extension" 
                                               placeholder="Ext." 
                                               style="max-width: 80px;">
                                    </div>
                                    <div class="invalid-feedback">Por favor ingresa un teléfono de oficina válido.</div>
                                </div>
                                
                                <!-- Teléfono Celular -->
                                <div class="col-md-6">
                                    <label for="telefono_celular" class="form-label">
                                        Teléfono Celular <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control form-control-lg" 
                                           id="telefono_celular" 
                                           name="telefono_celular" 
                                           placeholder="(555) 987-6543">
                                    <div class="invalid-feedback">Por favor ingresa un teléfono celular válido.</div>
                                </div>
                                
                                <!-- Producto de Interés -->
                                <div class="col-md-6">
                                    <label for="producto_interes" class="form-label">
                                        Producto de Interés <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <select class="form-select form-select-lg" 
                                            id="producto_interes" 
                                            name="producto_interes">
                                        <option value="" selected>Selecciona un producto</option>
                                        <option value="Simuladores Maternales">Simuladores Maternales</option>
                                        <option value="Simuladores RCP">Simuladores RCP y Emergencias</option>
                                        <option value="Simuladores Pediátricos">Simuladores Pediátricos</option>
                                        <option value="Simuladores Adulto">Simuladores Adulto</option>
                                        <option value="Anatomage Table">Anatomage Table</option>
                                        <option value="Realidad Virtual">Realidad Virtual / Inmersiva</option>
                                        <option value="Centro de Simulación">Centro de Simulación Completo</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                
                                <!-- Fecha Aproximada de Compra -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Fecha Aproximada de Compra <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <select class="form-select form-select-lg" 
                                                    id="compra_mes" 
                                                    name="compra_mes">
                                                <option value="" selected>Mes</option>
                                                <option value="01">Enero</option>
                                                <option value="02">Febrero</option>
                                                <option value="03">Marzo</option>
                                                <option value="04">Abril</option>
                                                <option value="05">Mayo</option>
                                                <option value="06">Junio</option>
                                                <option value="07">Julio</option>
                                                <option value="08">Agosto</option>
                                                <option value="09">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select form-select-lg" 
                                                    id="compra_anio" 
                                                    name="compra_anio">
                                                <option value="" selected>Año</option>
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Observaciones -->
                                <div class="col-12">
                                    <label for="observaciones" class="form-label">
                                        Observaciones <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <textarea class="form-control form-control-lg" 
                                              id="observaciones" 
                                              name="observaciones" 
                                              rows="4" 
                                              placeholder="Cuéntanos más sobre tus necesidades o proyectos..."></textarea>
                                </div>
                                
                                <!-- Privacy Policy -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="privacidad" 
                                               name="privacidad" 
                                               required>
                                        <label class="form-check-label" for="privacidad">
                                            Acepto la <a href="#">política de privacidad</a> 
                                            y el tratamiento de mis datos personales. <span class="text-danger">*</span>
                                        </label>
                                        <div class="invalid-feedback">Debes aceptar la política de privacidad.</div>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-light btn-lg px-5 py-3" id="newsletter-submit-btn">
                                        <i class="bi bi-send-fill me-2"></i>
                                        Contáctanos
                                    </button>
                                    <button type="button" class="btn btn-light btn-lg px-5 py-3 d-none" id="newsletter-loading-btn" disabled>
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Enviando...
                                    </button>
                                </div>
                                
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </section>
    
    <!-- ========================================
         CONTACT MODAL
         ======================================== -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">
                        <i class="bi bi-envelope-fill me-2"></i>
                        Contáctanos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Success Alert -->
                    <div id="contact-success" class="alert alert-success d-none" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>¡Mensaje enviado!</strong> Gracias por contactarnos, te responderemos pronto.
                    </div>
                    
                    <!-- Error Alert -->
                    <div id="contact-error" class="alert alert-danger d-none" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Error:</strong> <span id="contact-error-message"></span>
                    </div>
                    
                    <!-- Contact Form -->
                    <form id="contactForm" action="includes/contact_handler.php" method="POST" novalidate>
                        <div class="row g-3">
                            
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="contact_nombre" class="form-label">
                                    Nombre Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contact_nombre" 
                                       name="nombre" 
                                       required>
                                <div class="invalid-feedback">Por favor ingresa tu nombre.</div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">
                                    Correo Electrónico <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="contact_email" 
                                       name="email" 
                                       required>
                                <div class="invalid-feedback">Por favor ingresa un correo válido.</div>
                            </div>
                            
                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="contact_telefono" class="form-label">
                                    Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="contact_telefono" 
                                       name="telefono" 
                                       required>
                                <div class="invalid-feedback">Por favor ingresa tu teléfono.</div>
                            </div>
                            
                            <!-- Institución -->
                            <div class="col-md-6">
                                <label for="contact_institucion" class="form-label">
                                    Institución <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contact_institucion" 
                                       name="institucion">
                            </div>
                            
                            <!-- Asunto -->
                            <div class="col-12">
                                <label for="contact_asunto" class="form-label">
                                    Asunto <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" 
                                        id="contact_asunto" 
                                        name="asunto" 
                                        required>
                                    <option value="" selected disabled>Selecciona un asunto</option>
                                    <option value="Cotización">Solicitar Cotización</option>
                                    <option value="Información de Productos">Información de Productos</option>
                                    <option value="Soporte Técnico">Soporte Técnico</option>
                                    <option value="Asesoría">Asesoría para Centro de Simulación</option>
                                    <option value="Capacitación">Capacitación y Entrenamiento</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <div class="invalid-feedback">Por favor selecciona un asunto.</div>
                            </div>
                            
                            <!-- Mensaje -->
                            <div class="col-12">
                                <label for="contact_mensaje" class="form-label">
                                    Mensaje <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="contact_mensaje" 
                                          name="mensaje" 
                                          rows="5" 
                                          required></textarea>
                                <div class="invalid-feedback">Por favor ingresa tu mensaje.</div>
                            </div>
                            
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="contactForm" class="btn btn-primary" id="contact-submit-btn">
                        <i class="bi bi-send-fill me-2"></i>
                        Enviar Mensaje
                    </button>
                    <button type="button" class="btn btn-primary d-none" id="contact-loading-btn" disabled>
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Enviando...
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ========================================
         FOOTER
         ======================================== -->
    <?php include INCLUDES_PATH . '/footer.php'; ?>
    
    <!-- ========================================
         JAVASCRIPT LIBRARIES
         ======================================== -->
    
    <!-- Bootstrap Bundle (Popper incluido) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- AOS (Animate On Scroll) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo assetUrl('js/main.js'); ?>?v=<?php echo time(); ?>"></script>
    <script src="<?php echo assetUrl('js/landing.js'); ?>?v=<?php echo time(); ?>"></script>
    <script src="<?php echo assetUrl('js/forms.js'); ?>?v=<?php echo time(); ?>"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    
    <!-- Dynamic Text Color System -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heroImage = document.getElementById('hero-main-image');
            const heroElements = {
                badge: document.getElementById('hero-badge'),
                subtitle: document.getElementById('hero-subtitle'),
                description: document.getElementById('hero-description'),
                btnPrimary: document.getElementById('hero-btn-primary')
            };

            function getDominantColor(imageElement) {
                return new Promise((resolve) => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    // Reducir el tamaño para análisis más rápido
                    const size = 50;
                    canvas.width = size;
                    canvas.height = size;
                    
                    ctx.drawImage(imageElement, 0, 0, size, size);
                    
                    const imageData = ctx.getImageData(0, 0, size, size);
                    const data = imageData.data;
                    
                    let r = 0, g = 0, b = 0;
                    let pixelCount = 0;
                    
                    // Muestrear cada 4 píxeles para mejor rendimiento
                    for (let i = 0; i < data.length; i += 16) {
                        r += data[i];
                        g += data[i + 1];
                        b += data[i + 2];
                        pixelCount++;
                    }
                    
                    r = Math.floor(r / pixelCount);
                    g = Math.floor(g / pixelCount);
                    b = Math.floor(b / pixelCount);
                    
                    resolve({ r, g, b });
                });
            }

            function getBrightness(r, g, b) {
                // Fórmula de luminancia relativa
                return (0.299 * r + 0.587 * g + 0.114 * b);
            }

            function adjustTextColors(dominantColor) {
                const brightness = getBrightness(dominantColor.r, dominantColor.g, dominantColor.b);
                const isLight = brightness > 128;
                
                // Colores base
                const lightTextColor = '#ffffff';
                const darkTextColor = '#1a1a1a';
                const lightBgColor = 'rgba(255, 255, 255, 0.9)';
                const darkBgColor = 'rgba(0, 0, 0, 0.7)';
                
                // Color de texto principal
                const textColor = isLight ? darkTextColor : lightTextColor;
                const bgColor = isLight ? lightBgColor : darkBgColor;
                
                // Aplicar estilos
                if (heroElements.badge) {
                    heroElements.badge.style.backgroundColor = bgColor;
                    heroElements.badge.style.color = textColor;
                    heroElements.badge.style.border = `1px solid ${isLight ? '#e0e0e0' : '#333'}`;
                }
                
                if (heroElements.subtitle) {
                    heroElements.subtitle.style.color = textColor;
                    heroElements.subtitle.style.textShadow = 'none';
                }
                
                if (heroElements.description) {
                    heroElements.description.style.color = textColor;
                    heroElements.description.style.textShadow = 'none';
                }
                
                // Botón principal
                if (heroElements.btnPrimary) {
                    heroElements.btnPrimary.style.backgroundColor = isLight ? '#0066cc' : '#ffffff';
                    heroElements.btnPrimary.style.color = isLight ? '#ffffff' : '#0066cc';
                    heroElements.btnPrimary.style.border = 'none';
                    heroElements.btnPrimary.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
                }
            }

            function initializeDynamicColors() {
                if (heroImage && heroImage.complete) {
                    getDominantColor(heroImage).then(adjustTextColors);
                } else if (heroImage) {
                    heroImage.addEventListener('load', function() {
                        getDominantColor(heroImage).then(adjustTextColors);
                    });
                }
            }

            // Inicializar cuando la imagen esté lista
            initializeDynamicColors();
            
            // Re-analizar cuando cambie la imagen (para el slider)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                        setTimeout(initializeDynamicColors, 100);
                    }
                });
            });
            
            if (heroImage) {
                observer.observe(heroImage, { attributes: true });
            }
        });
    </script>
    
</body>
</html>


