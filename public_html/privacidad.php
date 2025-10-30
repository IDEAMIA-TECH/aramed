<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Política de Privacidad
 * ========================================
 * 
 * Página de política de privacidad
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
$pageTitle = 'Política de Privacidad - ' . SITE_NAME;
$pageDescription = 'Política de privacidad de Aramed y Laboratorios. Conoce cómo protegemos y manejamos tus datos personales de acuerdo con la ley mexicana.';
$pageKeywords = 'privacidad, protección de datos, LFPDPPP, datos personales, Aramed, laboratorios';
$pageUrl = SITE_URL . '/privacidad.php';
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

    <?php // Google Analytics ?>
    <?php include INCLUDES_PATH . '/analytics.php'; ?>
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
                    <h1 class="display-4 fw-bold text-primary mb-3">Política de Privacidad</h1>
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
                            En <strong><?php echo SITE_NAME; ?></strong> (en adelante "nosotros", "nuestro/a", "la Empresa"), 
                            somos responsables de recoger, utilizar, almacenar y proteger los datos personales que usted nos proporciona 
                            a través de nuestro sitio web <strong><?php echo SITE_URL; ?></strong> (el "Sitio"). 
                            Esta Política de Privacidad explica qué datos recogemos, con qué finalidad, cómo los protegemos y los derechos que usted tiene.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">1. Datos que recogemos</h2>
                        <p class="mb-3">Podemos recoger los siguientes datos personales:</p>
                        <ul class="mb-4">
                            <li>Nombre completo</li>
                            <li>Correo electrónico</li>
                            <li>Teléfono</li>
                            <li>Dirección</li>
                            <li>Datos de facturación (si aplica)</li>
                            <li>Información sobre su dispositivo, navegación y cookies (por ejemplo: Dirección IP, tipo de navegador, páginas visitadas, tiempo en el sitio)</li>
                            <li>Cualquier otro dato que usted nos proporcione voluntariamente (por ejemplo al registrarse, suscribirse, hacer un pedido, enviar un formulario de contacto)</li>
                        </ul>
                        
                        <h2 class="h3 text-primary mb-3">2. Finalidad del tratamiento</h2>
                        <p class="mb-3">Usaremos sus datos personales para las siguientes finalidades:</p>
                        <ul class="mb-4">
                            <li>Proveerle los productos o servicios solicitados</li>
                            <li>Gestionar y responder a sus consultas o solicitudes</li>
                            <li>Enviarle comunicaciones comerciales, promociones o novedades (cuando usted haya dado su consentimiento)</li>
                            <li>Mejorar nuestro Sitio web, optimizar su experiencia de usuario y administrar cookies y rastreadores</li>
                            <li>Cumplir con obligaciones legales, contables o fiscales</li>
                        </ul>
                        
                        <h2 class="h3 text-primary mb-3">3. Bases legales</h2>
                        <p class="mb-4">
                            De conformidad con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares de México (LFPDPPP), 
                            el tratamiento de datos personales se basa en su consentimiento o en otros supuestos previstos por la ley.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">4. Compartir, transferir o revelar datos</h2>
                        <p class="mb-3">No venderemos, alquilaremos ni compartiremos sus datos personales con terceros, salvo en los siguientes casos:</p>
                        <ul class="mb-4">
                            <li>Cuando sea necesario para entregar un producto o servicio, con prestadores de servicios a nuestro cargo (por ejemplo, envío, pasarela de pagos)</li>
                            <li>Cuando expresamente usted lo autorice</li>
                            <li>Cuando exista obligación legal o requerimiento de autoridad competente</li>
                        </ul>
                        
                        <h2 class="h3 text-primary mb-3">5. Uso de cookies y rastreadores</h2>
                        <p class="mb-4">
                            Utilizamos cookies y otros mecanismos de rastreo para mejorar la experiencia del usuario, analizar el comportamiento en el Sitio, 
                            personalizar contenido y publicidad. Usted puede desactivar las cookies desde su navegador; sin embargo, esto puede afectar el correcto funcionamiento del Sitio.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">6. Medidas de seguridad</h2>
                        <p class="mb-4">
                            Hemos implementado medidas técnicas, físicas y organizativas para proteger los datos personales contra acceso no autorizado, 
                            divulgación, alteración o destrucción. No obstante, ningún método de transmisión por Internet o de almacenamiento electrónico es 100% seguro.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">7. Conservación de los datos</h2>
                        <p class="mb-4">
                            Conservaremos sus datos personales durante el tiempo necesario para cumplir con las finalidades antes mencionadas, 
                            mientras su cuenta esté activa, o hasta que solicite su eliminación, salvo que prevalezca una obligación legal para su conservación.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">8. Derechos ARCO y revocación</h2>
                        <p class="mb-3">
                            Usted tiene los derechos de Acceso, Rectificación, Cancelación y Oposición (ARCO) al tratamiento de sus datos, 
                            así como a revocar el consentimiento otorgado. Para ejercitarlos, envíe su solicitud al correo electrónico 
                            <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a> o al domicilio antes señalado. 
                            Deberá incluir: nombre completo, domicilio u otro medio para comunicar la respuesta, documento que acredite su identidad, 
                            descripción de los datos respecto de los que busca ejercer el derecho y la modalidad de respuesta deseada.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">9. Modificaciones del aviso de privacidad</h2>
                        <p class="mb-4">
                            Nos reservamos el derecho de modificar esta Política de Privacidad en cualquier momento. 
                            La nueva versión estará disponible en esta página con la fecha de actualización correspondiente.
                        </p>
                        
                        <h2 class="h3 text-primary mb-3">10. Contacto</h2>
                        <p class="mb-4">
                            Si tiene preguntas o comentarios sobre esta Política de Privacidad o el tratamiento de sus datos, puede contactarnos en:
                        </p>
                        <div class="contact-info bg-light p-4 rounded">
                            <p class="mb-2"><strong>Correo electrónico:</strong> <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a></p>
                            <p class="mb-2"><strong>Teléfono:</strong> <a href="tel:<?php echo PHONE_MAIN; ?>"><?php echo PHONE_FORMATTED; ?></a></p>
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
