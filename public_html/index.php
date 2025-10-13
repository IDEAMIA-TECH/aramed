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
require_once __DIR__ . '/../includes/config.php';

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
    <link rel="apple-touch-icon" href="<?php echo imageUrl('design/logo-og.png'); ?>">
    
    <!-- ========================================
         PRECONNECT & DNS-PREFETCH
         ======================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            "streetAddress": "<?php echo esc(ADDRESS_STREET); ?>",
            "addressLocality": "<?php echo esc(ADDRESS_CITY); ?>",
            "postalCode": "<?php echo esc(ADDRESS_ZIP); ?>",
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
        <div class="hero-placeholder">
            <div class="container text-center py-5">
                <h1 class="display-3 fw-bold mb-4">Aramed y Laboratorio</h1>
                <h2 class="h3 mb-4">Simuladores médicos para la enseñanza</h2>
                <p class="lead mb-5">Distribuidores líderes de tecnología educativa en salud</p>
                <button class="btn btn-primary btn-lg px-5" data-bs-toggle="modal" data-bs-target="#contactModal">
                    Contáctanos
                </button>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         SOCIAL PROOF (ALIADOS)
         ======================================== -->
    <section id="aliados" class="section-aliados py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Nuestros Aliados</h2>
                <p class="section-subtitle">Trabajamos con las mejores marcas del mundo</p>
            </div>
            <div class="aliados-placeholder">
                <p class="text-center text-muted">Carrusel de logos de aliados (Próximamente)</p>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         OFERTA (SERVICES)
         ======================================== -->
    <section id="servicios" class="section-services py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Por la dignidad del paciente, reduciendo el error humano</h2>
                <p class="section-subtitle">Empresa mexicana con +20 años equipando instituciones de salud</p>
            </div>
            <div class="services-placeholder">
                <p class="text-center text-muted">5 Cards de servicios (Próximamente)</p>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         PRODUCTOS DESTACADOS
         ======================================== -->
    <section id="productos" class="section-productos py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Productos Destacados</h2>
                <p class="section-subtitle">Tecnología de vanguardia para la educación médica</p>
            </div>
            <div class="productos-placeholder">
                <p class="text-center text-muted">4 Productos destacados (Próximamente)</p>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         NEWSLETTER
         ======================================== -->
    <section id="newsletter" class="section-newsletter py-5 bg-primary text-white">
        <div class="container">
            <div class="text-center mb-4" data-aos="fade-up">
                <h2 class="section-title text-white">Mantente informado</h2>
                <p class="section-subtitle text-white-50">Conoce todas las soluciones que podemos ofrecerte en tu área de enseñanza médica</p>
            </div>
            <div class="newsletter-placeholder">
                <p class="text-center">Formulario de newsletter (Próximamente)</p>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         FOOTER
         ======================================== -->
    <?php include INCLUDES_PATH . '/footer.php'; ?>
    
    <!-- ========================================
         MODAL: CONTACTO
         ======================================== -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contáctanos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center text-muted">Formulario de contacto (Próximamente)</p>
                </div>
            </div>
        </div>
    </div>
    
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
    
</body>
</html>

