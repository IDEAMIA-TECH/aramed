<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Política de Cookies
 * ========================================
 * 
 * Página de política de cookies
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
$pageTitle = 'Política de Cookies - ' . SITE_NAME;
$pageDescription = 'Política de cookies de Aramed y Laboratorios. Conoce cómo utilizamos las cookies para mejorar tu experiencia de navegación y personalizar nuestro sitio web.';
$pageKeywords = 'cookies, política de cookies, navegación, experiencia de usuario, Aramed, laboratorios';
$pageUrl = SITE_URL . '/cookies.php';
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
    
    <!-- ========================================
         FAVICON & TOUCH ICONS
         ======================================== -->
    <link rel="icon" type="image/x-icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo imageUrl('design/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo imageUrl('design/favicon-16x16.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo imageUrl('design/apple-touch-icon.png'); ?>">
    
    <!-- ========================================
         PRECONNECT & DNS-PREFETCH
         ======================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
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
         CUSTOM CSS
         ======================================== -->
    <link rel="stylesheet" href="<?php echo assetUrl('css/main.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/responsive.css'); ?>?v=<?php echo time(); ?>">
</head>

<body class="legal-page">
    
    <!-- ========================================
         NAVBAR
         ======================================== -->
    <?php include INCLUDES_PATH . '/navbar.php'; ?>
    
    <!-- ========================================
         HERO SECTION
         ======================================== -->
    <section class="legal-hero py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold text-primary mb-3">Política de Cookies</h1>
                    <p class="lead text-muted">Última actualización: <?php echo date('d/m/Y'); ?></p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         CONTENT SECTION
         ======================================== -->
    <section class="legal-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="legal-text">
                        
                        <p class="lead mb-4">
                            En <strong><?php echo SITE_NAME; ?></strong> (en adelante "nosotros", "nuestro" o "la Empresa"), 
                            utilizamos cookies y tecnologías similares para mejorar su experiencia de navegación en el sitio web 
                            <strong><?php echo SITE_URL; ?></strong> (en adelante, el "Sitio").
                        </p>
                        
                        <p class="mb-4">
                            Esta Política explica qué son las cookies, qué tipos utilizamos, con qué finalidad, y cómo puede gestionarlas o desactivarlas.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">1. ¿Qué son las cookies?</h2>
                        <p class="mb-4">
                            Las cookies son pequeños archivos de texto que se almacenan en su dispositivo (ordenador, tablet, teléfono móvil, etc.) 
                            cuando visita un sitio web. Permiten que el sitio recuerde sus acciones y preferencias (como idioma, inicio de sesión o 
                            contenido del carrito) durante un periodo de tiempo, para ofrecerle una experiencia más personalizada y eficiente.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">2. Tipos de cookies que utilizamos</h2>
                        
                        <h3 class="h4 text-secondary mb-3">a) Cookies necesarias (esenciales)</h3>
                        <p class="mb-3">
                            Estas cookies son indispensables para el funcionamiento del Sitio y le permiten navegar y usar sus funciones básicas 
                            (por ejemplo, acceder a áreas seguras o formularios). No pueden desactivarse en nuestros sistemas.
                        </p>
                        <p class="mb-4"><strong>Ejemplos:</strong></p>
                        <ul class="mb-4">
                            <li>Cookies de sesión</li>
                            <li>Cookies de seguridad</li>
                            <li>Cookies de autenticación</li>
                        </ul>
                        
                        <h3 class="h4 text-secondary mb-3">b) Cookies de rendimiento y analítica</h3>
                        <p class="mb-3">
                            Nos ayudan a entender cómo los usuarios interactúan con el Sitio, recopilan información anónima (como páginas visitadas, 
                            tiempo de navegación o errores). Esto nos permite mejorar la estructura, contenido y rendimiento de nuestro sitio.
                        </p>
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">Ejemplo: Google Analytics</h5>
                            <p class="mb-2"><strong>Proveedor:</strong> Google LLC</p>
                            <p class="mb-2"><strong>Información recopilada:</strong> IP anonimizada, tiempo en la página, dispositivo, navegador</p>
                            <p class="mb-0">
                                <strong>Política de privacidad de Google:</strong> 
                                <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">https://policies.google.com/privacy</a>
                            </p>
                        </div>
                        
                        <h3 class="h4 text-secondary mb-3">c) Cookies de personalización (preferencias)</h3>
                        <p class="mb-4">
                            Permiten recordar sus elecciones, como el idioma o la región, para ofrecerle una experiencia adaptada a sus preferencias.
                        </p>
                        
                        <h3 class="h4 text-secondary mb-3">d) Cookies de publicidad o marketing</h3>
                        <p class="mb-4">
                            Se utilizan para mostrarle anuncios relevantes y medir la efectividad de las campañas publicitarias. 
                            También pueden ser utilizadas por terceros autorizados (por ejemplo, Google Ads, Meta Pixel) para mostrar 
                            publicidad personalizada en función de sus intereses.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">3. Cookies de terceros</h2>
                        <p class="mb-3">
                            En algunos casos, colaboramos con empresas externas que también pueden colocar cookies en su dispositivo para recopilar 
                            información sobre su navegación. Estas cookies están sujetas a las políticas de privacidad de dichos terceros.
                        </p>
                        <p class="mb-4"><strong>Ejemplos posibles:</strong></p>
                        <ul class="mb-4">
                            <li>Google Ads / DoubleClick</li>
                            <li>Facebook Pixel (Meta Platforms, Inc.)</li>
                            <li>YouTube (Google LLC)</li>
                        </ul>
                        
                        <h2 class="h3 text-primary mb-3">4. Consentimiento</h2>
                        <p class="mb-4">
                            Cuando accede al Sitio por primera vez, le mostramos un aviso o banner de cookies donde puede aceptar todas las cookies 
                            o configurar sus preferencias. Al hacer clic en "Aceptar todas las cookies", usted consiente el uso de las mismas 
                            conforme a esta Política. Puede retirar su consentimiento o cambiar su configuración en cualquier momento mediante 
                            el enlace "Configuración de cookies" disponible en el pie de página del Sitio.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">5. Cómo desactivar o eliminar cookies</h2>
                        <p class="mb-3">
                            Puede configurar su navegador para bloquear o eliminar cookies en cualquier momento. Tenga en cuenta que si las desactiva, 
                            algunas secciones del Sitio podrían no funcionar correctamente.
                        </p>
                        <p class="mb-3"><strong>Enlaces de ayuda según su navegador:</strong></p>
                        <ul class="mb-4">
                            <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
                            <li><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" rel="noopener noreferrer">Mozilla Firefox</a></li>
                            <li><a href="https://support.apple.com/es-es/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Safari</a></li>
                            <li><a href="https://support.microsoft.com/es-es/microsoft-edge/eliminar-las-cookies-en-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
                        </ul>
                        
                        <h2 class="h3 text-primary mb-3">6. Actualizaciones de esta Política</h2>
                        <p class="mb-4">
                            Podemos actualizar esta Política de Cookies en cualquier momento para reflejar cambios en nuestras prácticas o en la 
                            legislación vigente. Le recomendamos revisar periódicamente esta página para mantenerse informado.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">7. Contacto</h2>
                        <p class="mb-4">
                            Si tiene dudas o comentarios sobre esta Política de Cookies, puede contactarnos en:
                        </p>
                        <div class="contact-info bg-light p-4 rounded">
                            <p class="mb-2">
                                <strong><i class="bi bi-envelope-fill text-primary me-2"></i>Correo electrónico:</strong> 
                                <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a>
                            </p>
                            <p class="mb-2">
                                <strong><i class="bi bi-telephone-fill text-primary me-2"></i>Teléfono:</strong> 
                                <a href="tel:<?php echo PHONE_MAIN; ?>"><?php echo PHONE_FORMATTED; ?></a>
                            </p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         FOOTER
         ======================================== -->
    <?php include INCLUDES_PATH . '/footer.php'; ?>
    
    <!-- ========================================
         JAVASCRIPT LIBRARIES
         ======================================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="<?php echo assetUrl('js/main.js'); ?>?v=<?php echo time(); ?>"></script>
    
</body>
</html>
