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
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                
                <!-- Slide 1: Principal -->
                <div class="swiper-slide hero-slide">
                    <div class="hero-slide-bg" style="background: linear-gradient(135deg, rgba(0, 102, 204, 0.95) 0%, rgba(44, 62, 80, 0.95) 100%), url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22><rect fill=%22%23f0f0f0%22 width=%221920%22 height=%221080%22/></svg>') center/cover;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-8 col-xl-7">
                                    <div class="hero-content" data-aos="fade-up">
                                        <span class="hero-badge badge bg-white text-primary mb-3 px-3 py-2">
                                            <i class="bi bi-award-fill me-2"></i>
                                            +20 Años de Experiencia
                                        </span>
                                        <h1 class="hero-title display-2 fw-bold text-white mb-4">
                                            Aramed y Laboratorio
                                        </h1>
                                        <h2 class="hero-subtitle h3 text-white mb-4 fw-normal">
                                            Simuladores médicos para la enseñanza
                                        </h2>
                                        <p class="hero-description lead text-white-75 mb-5">
                                            Distribuidores líderes de tecnología educativa en salud. 
                                            Equipamos universidades, hospitales e instituciones con 
                                            simuladores de última generación.
                                        </p>
                                        <div class="hero-actions d-flex flex-wrap gap-3">
                                            <button class="btn btn-light btn-lg px-5 shadow" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                <i class="bi bi-envelope me-2"></i>
                                                Contáctanos
                                            </button>
                                            <a href="#catalogos" class="btn btn-outline-light btn-lg px-5">
                                                <i class="bi bi-book me-2"></i>
                                                Ver Catálogos
                                            </a>
                                        </div>
                                        
                                        <!-- Stats -->
                                        <div class="hero-stats row g-4 mt-4">
                                            <div class="col-4">
                                                <div class="stat-item">
                                                    <h3 class="stat-number text-white fw-bold mb-1">20+</h3>
                                                    <p class="stat-label text-white-75 small mb-0">Años</p>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-item">
                                                    <h3 class="stat-number text-white fw-bold mb-1">500+</h3>
                                                    <p class="stat-label text-white-75 small mb-0">Clientes</p>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-item">
                                                    <h3 class="stat-number text-white fw-bold mb-1">100%</h3>
                                                    <p class="stat-label text-white-75 small mb-0">Satisfacción</p>
                                                </div>
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
                    <div class="hero-slide-bg" style="background: linear-gradient(135deg, rgba(142, 68, 173, 0.9) 0%, rgba(74, 35, 90, 0.9) 100%), url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22><rect fill=%22%23e8d5f2%22 width=%221920%22 height=%221080%22/></svg>') center/cover;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
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
                                        <ul class="hero-features list-unstyled text-white mb-4">
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Entrenamiento realista para partos y emergencias</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Ojos interactivos con respuesta fisiológica</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 4 abdómenes intercambiables: cesárea, trabajo de parto, hemorragia</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Compatible con monitores y equipos clínicos reales</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Neonato con signos vitales y respuesta realista</li>
                                        </ul>
                                        <div class="hero-actions d-flex flex-wrap gap-3">
                                            <button class="btn btn-light btn-lg px-5" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                Solicitar Información
                                            </button>
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
                
                <!-- Slide 3: HAL® S5301 -->
                <div class="swiper-slide hero-slide">
                    <div class="hero-slide-bg" style="background: linear-gradient(135deg, rgba(52, 152, 219, 0.9) 0%, rgba(41, 128, 185, 0.9) 100%), url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22><rect fill=%22%23d6eaf8%22 width=%221920%22 height=%221080%22/></svg>') center/cover;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
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
                                            <button class="btn btn-light btn-lg px-5" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                Solicitar Demo
                                            </button>
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
                
                <!-- Slide 4: HAL® S3201 -->
                <div class="swiper-slide hero-slide">
                    <div class="hero-slide-bg" style="background: linear-gradient(135deg, rgba(46, 204, 113, 0.9) 0%, rgba(39, 174, 96, 0.9) 100%), url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22><rect fill=%22%23d5f4e6%22 width=%221920%22 height=%221080%22/></svg>') center/cover;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
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
                                            <button class="btn btn-light btn-lg px-5" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                Solicitar Cotización
                                            </button>
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
                
                <!-- Slide 5: Super TORY® S2220 -->
                <div class="swiper-slide hero-slide">
                    <div class="hero-slide-bg" style="background: linear-gradient(135deg, rgba(230, 126, 34, 0.9) 0%, rgba(211, 84, 0, 0.9) 100%), url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22><rect fill=%22%23fdebd0%22 width=%221920%22 height=%221080%22/></svg>') center/cover;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
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
                                            <button class="btn btn-light btn-lg px-5" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                Contactar Ahora
                                            </button>
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
                
                <!-- Slide 6: SUSIE® S2400 -->
                <div class="swiper-slide hero-slide">
                    <div class="hero-slide-bg" style="background: linear-gradient(135deg, rgba(231, 76, 60, 0.9) 0%, rgba(192, 57, 43, 0.9) 100%), url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22><rect fill=%22%23fadbd8%22 width=%221920%22 height=%221080%22/></svg>') center/cover;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-lg-7">
                                    <div class="hero-content" data-aos="fade-right">
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
                                            <button class="btn btn-light btn-lg px-5" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                Agendar Demo
                                            </button>
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

