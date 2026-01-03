<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Gestión de Mensajes Topbar
 * ========================================
 * 
 * Panel de administración para gestionar mensajes del topbar
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Verificar autenticación
require_once __DIR__ . '/auth_check.php';

// Cargar configuración
define('ARAMED_SITE', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';

// Verificar permisos RBAC (topbar puede ser parte de home o configuración)
if (function_exists('checkPermission')) {
    // Intentar con home primero, si no tiene permiso, verificar configuración
    if (!can('home', 'editar') && !can('configuracion', 'editar')) {
        checkPermission('home', 'editar', true);
    }
}

// Procesar acciones
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$message_id = $_GET['id'] ?? $_POST['id'] ?? '';

// Acción de limpieza automática
if ($action === 'cleanup') {
    try {
        $pdo = getDB();
        $sql = "UPDATE topbar_messages 
                SET status = 'inactive', updated_at = NOW() 
                WHERE status = 'active' 
                AND end_date IS NOT NULL 
                AND end_date < NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $affected = $stmt->rowCount();
        $success_message = "Limpieza completada: $affected mensajes desactivados";
    } catch (Exception $e) {
        $error_message = "Error en limpieza: " . $e->getMessage();
    }
}

// Acción de eliminación (puede ser GET o POST)
if ($action === 'delete' && $message_id) {
    try {
        $pdo = getDB();
        $sql = "DELETE FROM topbar_messages WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$message_id]);
        $success_message = "Mensaje eliminado exitosamente";
        // Redirigir para evitar reenvío del formulario
        header("Location: topbar-messages.php?deleted=1");
        exit;
    } catch (Exception $e) {
        $error_message = "Error al eliminar mensaje: " . $e->getMessage();
    }
}

// Manejar mensaje de éxito después de redirección
if (isset($_GET['deleted'])) {
    $success_message = "Mensaje eliminado exitosamente";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $icon = sanitizeInput($_POST['icon'] ?? '');
        $text = sanitizeInput($_POST['text'] ?? '');
        $link = sanitizeInput($_POST['link'] ?? '');
        $status = sanitizeInput($_POST['status'] ?? 'active');
        $priority = (int)($_POST['priority'] ?? 0);
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        
        try {
            $pdo = getDB();
            $sql = "INSERT INTO topbar_messages (icon, text, link, status, priority, start_date, end_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$icon, $text, $link, $status, $priority, $start_date, $end_date]);
            $success_message = "Mensaje creado exitosamente";
        } catch (Exception $e) {
            $error_message = "Error al crear mensaje: " . $e->getMessage();
        }
    } elseif ($action === 'update') {
        $icon = sanitizeInput($_POST['icon'] ?? '');
        $text = sanitizeInput($_POST['text'] ?? '');
        $link = sanitizeInput($_POST['link'] ?? '');
        $status = sanitizeInput($_POST['status'] ?? 'active');
        $priority = (int)($_POST['priority'] ?? 0);
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        
        try {
            $pdo = getDB();
            $sql = "UPDATE topbar_messages SET icon=?, text=?, link=?, status=?, priority=?, start_date=?, end_date=? 
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$icon, $text, $link, $status, $priority, $start_date, $end_date, $message_id]);
            $success_message = "Mensaje actualizado exitosamente";
        } catch (Exception $e) {
            $error_message = "Error al actualizar mensaje: " . $e->getMessage();
        }
    }
}

// Obtener mensajes
try {
    $pdo = getDB();
    $sql = "SELECT * FROM topbar_messages ORDER BY priority ASC, created_at DESC";
    $stmt = $pdo->query($sql);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $messages = [];
    $error_message = "Error al cargar mensajes: " . $e->getMessage();
}

// Obtener mensaje para editar
$edit_message = null;
if ($action === 'edit' && $message_id) {
    try {
        $pdo = getDB();
        $sql = "SELECT * FROM topbar_messages WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$message_id]);
        $edit_message = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = "Error al cargar mensaje: " . $e->getMessage();
    }
}

// Obtener estadísticas
try {
    $pdo = getDB();
    $stats = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM topbar_messages");
    $stats['total'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM topbar_messages WHERE status = 'active'");
    $stats['active'] = $stmt->fetch()['active'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as inactive FROM topbar_messages WHERE status = 'inactive'");
    $stats['inactive'] = $stmt->fetch()['inactive'];
    
} catch (Exception $e) {
    $stats = ['total' => 0, 'active' => 0, 'inactive' => 0];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mensajes Topbar - Admin Aramed</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
    :root {
        --primary-color: #0066cc;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --border-radius: 8px;
        --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        text-align: center;
        border-left: 4px solid var(--primary-color);
    }

    .stat-card.success { border-left-color: var(--success-color); }
    .stat-card.warning { border-left-color: var(--warning-color); }
    .stat-card.danger { border-left-color: var(--danger-color); }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .message-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 1rem;
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    .message-card:hover {
        transform: translateY(-2px);
    }

    .message-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .message-content {
        padding: 1rem;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .priority-badge {
        background: var(--primary-color);
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }

    .form-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin: 0 0.25rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--secondary-color);
    }

    .icon-preview {
        display: inline-block;
        width: 20px;
        text-align: center;
    }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/includes/admin_menu.php'; ?>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9 ms-auto">
                
                <!-- Page Header -->
                <div class="page-header">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col">
                                <h1 class="h3 mb-0">
                                    <i class="bi bi-megaphone me-2"></i>
                                    Gestión de Mensajes Topbar
                                </h1>
                                <p class="mb-0 opacity-75">Administra los mensajes que aparecen en la barra superior del sitio</p>
                            </div>
                            <div class="col-auto">
                                <a href="?action=cleanup" class="btn btn-warning me-2" 
                                   onclick="return confirm('¿Desactivar todos los mensajes expirados?')">
                                    <i class="bi bi-broom me-2"></i>
                                    Limpiar Expirados
                                </a>
                                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#messageModal">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Nuevo Mensaje
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    
                    <!-- Messages -->
                    <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo esc($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo esc($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number text-primary"><?php echo $stats['total']; ?></div>
                            <div class="text-muted">Total Mensajes</div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-number text-success"><?php echo $stats['active']; ?></div>
                            <div class="text-muted">Activos</div>
                        </div>
                        <div class="stat-card warning">
                            <div class="stat-number text-warning"><?php echo $stats['inactive']; ?></div>
                            <div class="text-muted">Inactivos</div>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-list-ul me-2"></i>
                                        Lista de Mensajes
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (empty($messages)): ?>
                                    <div class="empty-state">
                                        <i class="bi bi-megaphone display-1 text-muted"></i>
                                        <h4 class="mt-3">No hay mensajes</h4>
                                        <p class="text-muted">Crea tu primer mensaje para el topbar</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#messageModal">
                                            <i class="bi bi-plus-circle me-2"></i>
                                            Crear Mensaje
                                        </button>
                                    </div>
                                    <?php else: ?>
                                    <?php foreach ($messages as $message): ?>
                                    <div class="message-card">
                                        <div class="message-header">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-<?php echo esc($message['icon']); ?> me-2 text-primary"></i>
                                                <span class="fw-bold"><?php echo esc($message['text']); ?></span>
                                                <span class="priority-badge ms-2"><?php echo $message['priority']; ?></span>
                                                <span class="status-badge badge bg-<?php echo $message['status'] === 'active' ? 'success' : 'secondary'; ?> ms-2">
                                                    <?php echo $message['status'] === 'active' ? 'Activo' : 'Inactivo'; ?>
                                                </span>
                                            </div>
                                            <div class="btn-group">
                                                <a href="?action=edit&id=<?php echo $message['id']; ?>" class="btn btn-sm btn-outline-primary btn-action">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="?action=delete&id=<?php echo $message['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-action"
                                                   onclick="return confirm('¿Estás seguro de eliminar este mensaje?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="message-content">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">Enlace:</small>
                                                    <div><?php echo esc($message['link'] ?: 'Sin enlace'); ?></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Fecha inicio:</small>
                                                    <div><?php echo $message['start_date'] ? date('d/m/Y H:i', strtotime($message['start_date'])) : 'Sin fecha'; ?></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Fecha fin:</small>
                                                    <div><?php echo $message['end_date'] ? date('d/m/Y H:i', strtotime($message['end_date'])) : 'Sin fecha'; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-megaphone me-2"></i>
                        <?php echo $edit_message ? 'Editar Mensaje' : 'Nuevo Mensaje'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="?action=<?php echo $edit_message ? 'update&id=' . $edit_message['id'] : 'create'; ?>">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icono</label>
                                <select class="form-select" id="icon" name="icon" required>
                                    <option value="">Seleccionar icono</option>
                                    <option value="megaphone-fill" <?php echo ($edit_message['icon'] ?? '') === 'megaphone-fill' ? 'selected' : ''; ?>>Megáfono</option>
                                    <option value="calendar-event" <?php echo ($edit_message['icon'] ?? '') === 'calendar-event' ? 'selected' : ''; ?>>Calendario</option>
                                    <option value="award-fill" <?php echo ($edit_message['icon'] ?? '') === 'award-fill' ? 'selected' : ''; ?>>Premio</option>
                                    <option value="truck" <?php echo ($edit_message['icon'] ?? '') === 'truck' ? 'selected' : ''; ?>>Camión</option>
                                    <option value="star-fill" <?php echo ($edit_message['icon'] ?? '') === 'star-fill' ? 'selected' : ''; ?>>Estrella</option>
                                    <option value="heart-fill" <?php echo ($edit_message['icon'] ?? '') === 'heart-fill' ? 'selected' : ''; ?>>Corazón</option>
                                    <option value="info-circle-fill" <?php echo ($edit_message['icon'] ?? '') === 'info-circle-fill' ? 'selected' : ''; ?>>Información</option>
                                    <option value="exclamation-triangle-fill" <?php echo ($edit_message['icon'] ?? '') === 'exclamation-triangle-fill' ? 'selected' : ''; ?>>Advertencia</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Prioridad</label>
                                <input type="number" class="form-control" id="priority" name="priority" 
                                       value="<?php echo esc($edit_message['priority'] ?? '0'); ?>" min="0" max="999">
                                <div class="form-text">Menor número = mayor prioridad</div>
                            </div>
                            <div class="col-12">
                                <label for="text" class="form-label">Texto del mensaje</label>
                                <textarea class="form-control" id="text" name="text" rows="3" required 
                                          placeholder="Escribe el mensaje que aparecerá en el topbar..."><?php echo esc($edit_message['text'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="link" class="form-label">Enlace (opcional)</label>
                                <input type="text" class="form-control" id="link" name="link" 
                                       value="<?php echo esc($edit_message['link'] ?? ''); ?>" 
                                       placeholder="#seccion o https://ejemplo.com">
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($edit_message['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="inactive" <?php echo ($edit_message['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Fecha de inicio (opcional)</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                       value="<?php echo $edit_message['start_date'] ? date('Y-m-d\TH:i', strtotime($edit_message['start_date'])) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Fecha de fin (opcional)</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                       value="<?php echo $edit_message['end_date'] ? date('Y-m-d\TH:i', strtotime($edit_message['end_date'])) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            <?php echo $edit_message ? 'Actualizar' : 'Crear'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Auto-open modal if editing
    <?php if ($action === 'edit' && $edit_message): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('messageModal'));
        modal.show();
    });
    <?php endif; ?>

    // Icon preview
    document.getElementById('icon').addEventListener('change', function() {
        var icon = this.value;
        var preview = document.querySelector('.icon-preview');
        if (preview) {
            preview.innerHTML = '<i class="bi bi-' + icon + '"></i>';
        }
    });
    </script>
</body>
</html>
