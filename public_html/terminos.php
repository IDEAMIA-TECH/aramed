<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Términos y Condiciones
 * ========================================
 * 
 * Página de términos y condiciones de uso
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
$pageTitle = 'Términos y Condiciones - ' . SITE_NAME;
$pageDescription = 'Términos y condiciones de uso del sitio web de Aramed y Laboratorios. Conoce nuestras políticas y regulaciones para el uso de nuestros servicios.';
$pageKeywords = 'términos, condiciones, uso, políticas, Aramed, laboratorios, simuladores médicos';
$pageUrl = SITE_URL . '/terminos.php';
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
                    <h1 class="display-4 fw-bold text-primary mb-3">Términos y Condiciones de Uso</h1>
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
                        <?php
                        // Cargar conexión si no está cargada
                        if (!function_exists('getDB')) {
                            require_once __DIR__ . '/includes/connection.php';
                        }
                        
                        // Obtener contenido desde la base de datos
                        $legal_content = function_exists('getConfig') ? getConfig('legal_terminos', '') : '';
                        
                        if (!empty($legal_content)) {
                            // Mostrar contenido desde la BD (HTML)
                            echo $legal_content;
                        } else {
                            // Fallback al contenido hardcodeado
                            ?>
                            <p class="lead mb-4">
                                Estos Términos y Condiciones regulan el acceso y uso del sitio web <strong><?php echo SITE_URL; ?></strong> (el "Sitio"), 
                                propiedad de <strong><?php echo SITE_NAME; ?></strong> 
                                y correo electrónico <strong><?php echo CONTACT_EMAIL; ?></strong>.
                            </p>
                            
                            <p class="mb-4">
                                Al acceder o usar el Sitio, usted acepta quedar vinculado por estos Términos. Si no está de acuerdo, debe abstenerse de utilizarlo.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">1. Servicios y productos</h2>
                            <p class="mb-4">
                                Descritos en el Sitio, nuestros servicios y/o productos están sujetos a disponibilidad, condiciones específicas y precios vigentes en el momento de la compra o contratación.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">2. Registro y cuenta de usuario</h2>
                            <p class="mb-4">
                                Para acceder a ciertos servicios, podrá registrarse con un usuario y contraseña. Usted es responsable de mantener la confidencialidad de su cuenta, así como de toda actividad que se realice bajo ella. Debe notificarnos inmediatamente si detecta acceso no autorizado.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">3. Pagos y facturación</h2>
                            <p class="mb-4">
                                El pago de los servicios/productos se efectuará mediante los métodos disponibles en el Sitio. Usted garantiza que dispone de los derechos necesarios de los medios de pago utilizados. Nos reservamos el derecho de suspender, cancelar o rechazar pedidos ante posibles irregularidades.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">4. Envíos, devoluciones y cancelaciones</h2>
                            <p class="mb-4">
                                Los envíos, plazos, costos, cambios y devoluciones estarán sujetos a las condiciones específicas que se publiquen o acuerden al momento de la compra. Cada caso será evaluado individualmente según las políticas vigentes.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">5. Propiedad intelectual</h2>
                            <p class="mb-4">
                                Todo el contenido del Sitio (textos, imágenes, logotipos, marcas, programas, etc.) es propiedad de <?php echo SITE_NAME; ?> o de terceros que han autorizado su uso. Queda prohibida su reproducción, distribución, transformación o comercialización sin autorización expresa.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">6. Uso permitido</h2>
                            <p class="mb-4">
                                El Usuario se compromete a utilizar el Sitio de conformidad con la ley, la moral, el orden público y estos Términos. Queda prohibido:
                            </p>
                            <ul class="mb-4">
                                <li>Realizar actividades fraudulentas, ilegales o maliciosas</li>
                                <li>Difamar, acosar o fomentar violencia</li>
                                <li>Introducir virus, malware, o alterar el funcionamiento del Sitio</li>
                                <li>Suplantar identidad o usar datos de terceros sin consentimiento</li>
                            </ul>
                            
                            <h2 class="h3 text-primary mb-3">7. Limitación de responsabilidad</h2>
                            <p class="mb-4">
                                En la medida permitida por la ley, <?php echo SITE_NAME; ?> no será responsable por daños indirectos, incidentales, lucro cesante, pérdida de datos o interrupción del servicio, derivados del uso o imposibilidad de uso del Sitio, salvo en casos de dolo o culpa grave.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">8. Modificaciones del Sitio y de los Términos</h2>
                            <p class="mb-4">
                                <?php echo SITE_NAME; ?> se reserva el derecho de modificar, suspender o interrumpir el Sitio, total o parcialmente, así como de actualizar estos Términos en cualquier momento. Las modificaciones se publicarán en esta página y entrarán en vigor desde su publicación.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">9. Legislación aplicable y jurisdicción</h2>
                            <p class="mb-4">
                                Estos Términos se regirán e interpretarán conforme a las leyes de los Estados Unidos Mexicanos. Para la resolución de controversias, las partes se someten a la jurisdicción de los tribunales competentes de Ciudad de México.
                            </p>
                            
                            <h2 class="h3 text-primary mb-3">10. Contacto</h2>
                            <p class="mb-4">
                                Para dudas o aclaraciones sobre estos Términos y sobre el Sitio, puede contactarnos en:
                            </p>
                            <div class="contact-info bg-light p-4 rounded">
                                <?php
                                $empresa_email = function_exists('getConfig') ? getConfig('empresa_email', CONTACT_EMAIL) : CONTACT_EMAIL;
                                $empresa_telefono = function_exists('getConfig') ? getConfig('empresa_telefono', PHONE_MAIN) : PHONE_MAIN;
                                ?>
                                <p class="mb-2"><strong>Correo electrónico:</strong> <a href="mailto:<?php echo esc($empresa_email); ?>"><?php echo esc($empresa_email); ?></a></p>
                                <p class="mb-2"><strong>Teléfono:</strong> <a href="tel:<?php echo esc(preg_replace('/[^0-9+]/', '', $empresa_telefono)); ?>"><?php echo esc($empresa_telefono); ?></a></p>
                            </div>
                            <?php
                        }
                        ?>
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
