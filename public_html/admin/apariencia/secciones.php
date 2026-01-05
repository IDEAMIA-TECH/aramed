<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE SECCIONES DEL HOME
 * ========================================
 * 
 * Activa, desactiva y reordena las secciones del Home
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
    checkPermission('configuracion', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

$success_message = '';
$error_message = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'toggle') {
                // Activar/desactivar sección
                $id = (int)$_POST['id'];
                $activa = (int)$_POST['activa'];
                
                $stmt = $pdo->prepare("UPDATE home_secciones SET activa = ? WHERE id = ?");
                $stmt->execute([$activa, $id]);
                
                $success_message = 'Sección actualizada exitosamente';
            } elseif ($_POST['action'] === 'update_order') {
                // Actualizar orden
                $ordenes = json_decode($_POST['ordenes'], true);
                
                if ($ordenes && is_array($ordenes)) {
                    $pdo->beginTransaction();
                    try {
                        $stmt = $pdo->prepare("UPDATE home_secciones SET orden = ? WHERE id = ?");
                        foreach ($ordenes as $item) {
                            $stmt->execute([(int)$item['orden'], (int)$item['id']]);
                        }
                        $pdo->commit();
                        $success_message = 'Orden actualizado exitosamente';
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                }
            }
        }
    } catch (Exception $e) {
        $error_message = 'Error: ' . $e->getMessage();
    }
}

// Obtener secciones
$secciones = [];
try {
    $stmt = $pdo->query("SELECT * FROM home_secciones ORDER BY orden ASC");
    $secciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Tabla puede no existir aún
    $error_message = 'Error: La tabla home_secciones no existe. Ejecuta el script database/fase2/09_create_apariencia_tables.sql';
}

// El menú calculará automáticamente $current_dir y $current_page
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Secciones - Aramed Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow);
        }

        .section-item {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-color);
            cursor: move;
            transition: all 0.3s ease;
        }

        .section-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .section-item.inactive {
            opacity: 0.6;
            border-left-color: #ccc;
        }

        .section-item.sortable-ghost {
            opacity: 0.4;
        }

        .section-handle {
            color: #6c757d;
            font-size: 1.5rem;
            margin-right: 1rem;
            cursor: grab;
        }

        .section-handle:active {
            cursor: grabbing;
        }

        .form-switch {
            font-size: 1.2rem;
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
        }

        .admin-content {
            background: transparent;
            padding: 2rem;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9 admin-content">
                <!-- Header -->
                <div class="admin-header">
                    <div class="container-fluid">
                        <h1><i class="bi bi-layout-text-window me-3"></i>Gestión de Secciones del Home</h1>
                        <p>Activa, desactiva y reordena las secciones que se muestran en la página principal</p>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo esc($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo esc($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Instrucciones:</strong> Arrastra las secciones para cambiar su orden. Usa los switches para activar o desactivar secciones. Los cambios se guardan automáticamente.
                </div>

                <!-- Secciones -->
                <?php if (empty($secciones)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No hay secciones configuradas. Ejecuta el script <code>database/fase2/09_create_apariencia_tables.sql</code> para crear las secciones por defecto.
                    </div>
                <?php else: ?>
                    <div id="secciones-list">
                        <?php foreach ($secciones as $seccion): ?>
                            <div class="section-item <?php echo $seccion['activa'] ? '' : 'inactive'; ?>" data-id="<?php echo $seccion['id']; ?>">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-grip-vertical section-handle"></i>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">
                                            <?php echo esc($seccion['nombre']); ?>
                                            <small class="text-muted">(<?php echo esc($seccion['seccion']); ?>)</small>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            Orden: <?php echo $seccion['orden']; ?>
                                        </p>
                                    </div>
                                    <div class="form-check form-switch ms-3">
                                        <input class="form-check-input section-toggle" 
                                               type="checkbox" 
                                               data-id="<?php echo $seccion['id']; ?>"
                                               <?php echo $seccion['activa'] ? 'checked' : ''; ?>
                                               style="width: 3rem; height: 1.5rem;">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Inicializar Sortable
        const seccionesList = document.getElementById('secciones-list');
        if (seccionesList) {
            const sortable = Sortable.create(seccionesList, {
                handle: '.section-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Actualizar orden
                    const items = seccionesList.querySelectorAll('.section-item');
                    const ordenes = [];
                    
                    items.forEach((item, index) => {
                        ordenes.push({
                            id: item.dataset.id,
                            orden: index + 1
                        });
                    });
                    
                    // Enviar actualización
                    const formData = new FormData();
                    formData.append('action', 'update_order');
                    formData.append('ordenes', JSON.stringify(ordenes));
                    
                    fetch('secciones.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Recargar página para mostrar mensaje de éxito
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar el orden');
                    });
                }
            });
        }
        
        // Toggle de activación
        document.querySelectorAll('.section-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const id = this.dataset.id;
                const activa = this.checked ? 1 : 0;
                
                const formData = new FormData();
                formData.append('action', 'toggle');
                formData.append('id', id);
                formData.append('activa', activa);
                
                fetch('secciones.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    // Recargar página para mostrar mensaje de éxito
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar la sección');
                    this.checked = !this.checked; // Revertir cambio
                });
            });
        });
    </script>
</body>
</html>

