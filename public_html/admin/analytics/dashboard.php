<?php
/**
 * ========================================
 * ADMIN - DASHBOARD ANALYTICS
 * ========================================
 * 
 * Dashboard de métricas de Google Analytics
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
    checkPermission('analytics', 'ver');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Cargar configuración
$measurement_id = getConfig('analytics_measurement_id', 'G-3BPRR93ZCY');
$activar_tracking = getConfig('analytics_activar_tracking', '1') === '1';

$current_page = 'dashboard.php';
$current_dir = 'analytics';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analytics - Admin <?php echo SITE_NAME; ?></title>
    
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
        
        .analytics-iframe {
            width: 100%;
            height: 800px;
            border: none;
            border-radius: 8px;
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
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard de Analytics
                            </h2>
                            <p class="mb-0 opacity-75">Métricas y estadísticas de Google Analytics</p>
                        </div>
                        <div>
                            <a href="config.php" class="btn btn-light me-2">
                                <i class="bi bi-gear me-2"></i>Configuración
                            </a>
                            <a href="https://analytics.google.com/" target="_blank" class="btn btn-light">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Abrir GA4
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php if (!$activar_tracking): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Tracking desactivado:</strong> Activa el tracking en la configuración para ver las métricas.
                </div>
                <?php endif; ?>
                
                <!-- Información -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Información
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>
                            Para ver métricas detalladas, accede directamente a 
                            <a href="https://analytics.google.com/" target="_blank">Google Analytics</a>.
                        </p>
                        <p class="mb-0">
                            <strong>Measurement ID actual:</strong> <code><?php echo esc($measurement_id); ?></code>
                        </p>
                    </div>
                </div>
                
                <!-- Enlaces Rápidos -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up display-4 text-primary mb-3"></i>
                                <h5>Reportes</h5>
                                <p class="text-muted">Ver reportes detallados en Google Analytics</p>
                                <a href="https://analytics.google.com/" target="_blank" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>Abrir Reportes
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-bullseye display-4 text-success mb-3"></i>
                                <h5>Eventos</h5>
                                <p class="text-muted">Ver eventos personalizados rastreados</p>
                                <a href="https://analytics.google.com/" target="_blank" class="btn btn-success">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>Ver Eventos
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-people display-4 text-info mb-3"></i>
                                <h5>Audiencias</h5>
                                <p class="text-muted">Analizar comportamiento de usuarios</p>
                                <a href="https://analytics.google.com/" target="_blank" class="btn btn-info">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>Ver Audiencias
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Eventos Personalizados -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Eventos Personalizados Configurados
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Evento</th>
                                        <th>Descripción</th>
                                        <th>Dónde se Dispara</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>submit_quote</code></td>
                                        <td>Envío de formulario de cotización</td>
                                        <td>Formulario "Mantente Informado"</td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>submit_contact</code></td>
                                        <td>Envío de formulario de contacto</td>
                                        <td>Formulario de contacto</td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>subscribe_newsletter</code></td>
                                        <td>Suscripción al newsletter</td>
                                        <td>Formulario de newsletter</td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>view_product</code></td>
                                        <td>Visualización de producto</td>
                                        <td>Página de detalle de producto</td>
                                        <td><span class="badge bg-secondary">Pendiente</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>download_document</code></td>
                                        <td>Descarga de documento</td>
                                        <td>Enlaces de descarga</td>
                                        <td><span class="badge bg-secondary">Pendiente</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

