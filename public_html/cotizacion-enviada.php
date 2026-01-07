<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Cotización Enviada
 * ========================================
 * 
 * Página de confirmación después de enviar la cotización
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Obtener folio de la URL
$folio = isset($_GET['folio']) ? sanitizeInput($_GET['folio']) : '';

// Variables para meta tags
$pageTitle = 'Cotización Enviada - ' . SITE_NAME;
$pageDescription = 'Tu solicitud de cotización ha sido enviada exitosamente.';
$pageUrl = SITE_URL . '/cotizacion-enviada.php';

?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <?php include INCLUDES_PATH . '/analytics.php'; ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo esc($pageTitle); ?></title>
    <meta name="description" content="<?php echo esc($pageDescription); ?>">
    <link rel="canonical" href="<?php echo esc($pageUrl); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo assetUrl('css/main.css'); ?>?v=<?php echo time(); ?>">
</head>
<body>
    
    <!-- Navbar -->
    <?php include INCLUDES_PATH . '/navbar.php'; ?>
    
    <!-- Contenido Principal -->
    <section class="py-5 min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow-lg border-0 text-center">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                            </div>
                            
                            <h1 class="h2 mb-3">¡Cotización Enviada!</h1>
                            
                            <?php if ($folio): ?>
                            <p class="lead mb-3">
                                Tu solicitud de cotización ha sido recibida exitosamente.
                            </p>
                            <p class="text-muted mb-4">
                                <strong>Folio:</strong> <code><?php echo esc($folio); ?></code>
                            </p>
                            <?php else: ?>
                            <p class="lead mb-4">
                                Tu solicitud de cotización ha sido recibida exitosamente.
                            </p>
                            <?php endif; ?>
                            
                            <p class="mb-4">
                                Nuestro equipo de ventas se pondrá en contacto contigo a la brevedad posible 
                                para proporcionarte la información y cotización que necesitas.
                            </p>
                            
                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <a href="catalogo.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>Volver al Catálogo
                                </a>
                                <a href="<?php echo siteUrl(); ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-house me-2"></i>Ir al Inicio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php include INCLUDES_PATH . '/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

