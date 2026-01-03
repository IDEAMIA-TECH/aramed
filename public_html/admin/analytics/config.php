<?php
/**
 * ========================================
 * ADMIN - CONFIGURACIÓN ANALYTICS
 * ========================================
 * 
 * Configuración de Google Analytics desde el admin
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('analytics', 'editar');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$success_message = '';
$error_message = '';

// Cargar configuración actual
$config = [
    'measurement_id' => getConfig('analytics_measurement_id', 'G-3BPRR93ZCY'),
    'activar_tracking' => getConfig('analytics_activar_tracking', '1') === '1',
    'activar_eventos' => getConfig('analytics_activar_eventos', '1') === '1',
    'activar_ecommerce' => getConfig('analytics_activar_ecommerce', '0') === '1'
];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        setConfig('analytics_measurement_id', $_POST['measurement_id'] ?? '', 'text', 'analytics');
        setConfig('analytics_activar_tracking', isset($_POST['activar_tracking']) ? '1' : '0', 'boolean', 'analytics');
        setConfig('analytics_activar_eventos', isset($_POST['activar_eventos']) ? '1' : '0', 'boolean', 'analytics');
        setConfig('analytics_activar_ecommerce', isset($_POST['activar_ecommerce']) ? '1' : '0', 'boolean', 'analytics');
        
        $success_message = 'Configuración de Analytics guardada exitosamente';
        
        // Recargar configuración
        $config = [
            'measurement_id' => getConfig('analytics_measurement_id', 'G-3BPRR93ZCY'),
            'activar_tracking' => getConfig('analytics_activar_tracking', '1') === '1',
            'activar_eventos' => getConfig('analytics_activar_eventos', '1') === '1',
            'activar_ecommerce' => getConfig('analytics_activar_ecommerce', '0') === '1'
        ];
        
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'analytics', null, 'Configuración Analytics actualizada');
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'config.php';
$current_dir = 'analytics';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Analytics - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-graph-up me-2"></i>Configuración de Analytics
                            </h2>
                            <p class="mb-0 opacity-75">Configura Google Analytics 4 (GA4)</p>
                        </div>
                        <a href="dashboard.php" class="btn btn-light">
                            <i class="bi bi-speedometer2 me-2"></i>Ver Dashboard
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <!-- Configuración Principal -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-gear me-2"></i>Configuración Principal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Measurement ID (GA4) *</label>
                                <input type="text" class="form-control" name="measurement_id" 
                                       value="<?php echo esc($config['measurement_id']); ?>" 
                                       placeholder="G-XXXXXXXXXX" required>
                                <small class="form-text text-muted">
                                    Encuentra tu Measurement ID en: 
                                    <a href="https://analytics.google.com/" target="_blank">Google Analytics</a> → 
                                    Administrar → Flujo de datos
                                </small>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="activar_tracking" 
                                       name="activar_tracking" <?php echo $config['activar_tracking'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activar_tracking">
                                    <strong>Activar Tracking</strong>
                                    <br><small class="text-muted">Habilita el seguimiento de Google Analytics en el sitio</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Eventos Personalizados -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-bullseye me-2"></i>Eventos Personalizados
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="activar_eventos" 
                                       name="activar_eventos" <?php echo $config['activar_eventos'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activar_eventos">
                                    <strong>Activar Eventos Personalizados</strong>
                                    <br><small class="text-muted">Rastrea eventos como: submit_quote, submit_contact, subscribe_newsletter</small>
                                </label>
                            </div>
                            
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle me-2"></i>Eventos Disponibles:</h6>
                                <ul class="mb-0">
                                    <li><code>submit_quote</code> - Envío de cotización</li>
                                    <li><code>submit_contact</code> - Envío de formulario de contacto</li>
                                    <li><code>subscribe_newsletter</code> - Suscripción al newsletter</li>
                                    <li><code>view_product</code> - Visualización de producto</li>
                                    <li><code>download_document</code> - Descarga de documento</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- E-commerce -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-cart me-2"></i>E-commerce (Opcional)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="activar_ecommerce" 
                                       name="activar_ecommerce" <?php echo $config['activar_ecommerce'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activar_ecommerce">
                                    <strong>Activar Enhanced E-commerce</strong>
                                    <br><small class="text-muted">Rastrea transacciones y conversiones (requiere configuración adicional)</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

