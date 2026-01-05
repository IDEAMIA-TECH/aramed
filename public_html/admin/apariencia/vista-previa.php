<?php
/**
 * ========================================
 * ADMIN - VISTA PREVIA DEL HOME
 * ========================================
 * 
 * Vista previa del Home con las secciones activas
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
    checkPermission('configuracion', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener secciones activas ordenadas
$secciones_activas = [];
try {
    $stmt = $pdo->query("SELECT * FROM home_secciones WHERE activa = 1 ORDER BY orden ASC");
    $secciones_activas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Tabla puede no existir
}

$current_page = 'vista-previa.php';
$current_dir = 'apariencia';

include __DIR__ . '/../includes/admin_menu.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa del Home - Aramed Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .preview-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .preview-container {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-preview {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            background: #f8f9fa;
        }

        .section-preview.active {
            border-color: #27ae60;
            background: #d4edda;
        }

        .section-preview h4 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .section-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .badge-active {
            background: #27ae60;
            color: white;
        }

        .badge-inactive {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9">
                <!-- Header -->
                <div class="preview-header">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="mb-0"><i class="bi bi-eye me-3"></i>Vista Previa del Home</h1>
                                <p class="mb-0">Visualiza cómo se verá el Home con las secciones activas</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-light">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>Ver en sitio real
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="preview-container">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Esta es una vista previa de las secciones que se mostrarán en el Home. Las secciones están ordenadas según su configuración.
                    </div>

                    <?php if (empty($secciones_activas)): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No hay secciones activas. <a href="secciones.php">Activa algunas secciones</a> para ver la vista previa.
                        </div>
                    <?php else: ?>
                        <h3 class="mb-4">Secciones Activas (<?php echo count($secciones_activas); ?>)</h3>
                        
                        <?php foreach ($secciones_activas as $seccion): ?>
                            <div class="section-preview active">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">
                                        <span class="section-badge badge-active">Orden <?php echo $seccion['orden']; ?></span>
                                        <?php echo esc($seccion['nombre']); ?>
                                    </h4>
                                    <span class="badge bg-success">Activa</span>
                                </div>
                                <p class="text-muted mb-0">
                                    <strong>ID:</strong> <?php echo esc($seccion['seccion']); ?>
                                </p>
                                <?php if (!empty($seccion['configuracion'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Configuración:</strong> <?php echo esc($seccion['configuracion']); ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="secciones.php" class="btn btn-primary">
                            <i class="bi bi-gear me-2"></i>Gestionar Secciones
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

