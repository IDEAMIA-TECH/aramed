<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE MENSAJES DE CONTACTO
 * ========================================
 * 
 * Listado y gestión de mensajes de contacto
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
    checkPermission('contacto', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar acciones
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$success_message = '';
$error_message = '';

// Procesar acciones rápidas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['bulk_action']) && isset($_POST['selected_ids'])) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('contacto', 'editar');
            }
            
            $bulk_action = $_POST['bulk_action'];
            $selected_ids = array_map('intval', $_POST['selected_ids']);
            $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
            
            if ($bulk_action === 'marcar_leido') {
                $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'en_proceso' WHERE id IN ($placeholders) AND status = 'nuevo'");
                $stmt->execute($selected_ids);
                $success_message = count($selected_ids) . ' mensaje(s) marcado(s) como leído(s)';
                
            } elseif ($bulk_action === 'marcar_cerrado') {
                $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'cerrado' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                $success_message = count($selected_ids) . ' mensaje(s) cerrado(s)';
                
            } elseif ($bulk_action === 'eliminar') {
                // Verificar permisos
                if (function_exists('checkPermission')) {
                    checkPermission('contacto', 'eliminar');
                }
                $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                $success_message = count($selected_ids) . ' mensaje(s) eliminado(s)';
            }
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'editar', 'contacto', null, 'mensajes', [
                    'accion' => $bulk_action,
                    'mensajes_afectados' => count($selected_ids)
                ]);
            }
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Filtros
$filtro_estado = $_GET['estado'] ?? '';
$filtro_asunto = $_GET['asunto'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_estado) {
    $where_conditions[] = "cm.status = ?";
    $params[] = $filtro_estado;
}

if ($filtro_asunto) {
    $where_conditions[] = "cm.asunto LIKE ?";
    $params[] = "%{$filtro_asunto}%";
}

if ($filtro_fecha_desde) {
    $where_conditions[] = "DATE(cm.created_at) >= ?";
    $params[] = $filtro_fecha_desde;
}

if ($filtro_fecha_hasta) {
    $where_conditions[] = "DATE(cm.created_at) <= ?";
    $params[] = $filtro_fecha_hasta;
}

if ($busqueda) {
    $where_conditions[] = "(cm.nombre LIKE ? OR cm.email LIKE ? OR cm.mensaje LIKE ? OR cm.asunto LIKE ?)";
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener total de registros
$count_sql = "SELECT COUNT(*) FROM contact_messages cm $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_messages = $count_stmt->fetchColumn();
$total_pages = ceil($total_messages / $per_page);

// Obtener mensajes
$sql = "SELECT cm.*, 
               au.nombre as asignado_nombre,
               au.email as asignado_email
        FROM contact_messages cm
        LEFT JOIN admin_usuarios au ON cm.assigned_to = au.id
        $where_clause
        ORDER BY cm.created_at DESC
        LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$stats = [];
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'nuevo' THEN 1 ELSE 0 END) as nuevos,
    SUM(CASE WHEN status = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
    SUM(CASE WHEN status = 'respondido' THEN 1 ELSE 0 END) as respondidos,
    SUM(CASE WHEN status = 'cerrado' THEN 1 ELSE 0 END) as cerrados,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as hoy
FROM contact_messages";
$stats_stmt = $pdo->query($stats_sql);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$current_page = 'index.php';
$current_dir = 'contacto';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes de Contacto - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .message-row {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .message-row:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transform: translateX(5px);
        }
        
        .message-row.unread {
            border-left: 4px solid #007bff;
            background: #f8f9ff;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-envelope me-2"></i>Mensajes de Contacto
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona los mensajes recibidos del formulario de contacto</p>
                        </div>
                        <a href="export.php?<?php echo http_build_query($_GET); ?>" class="btn btn-light">
                            <i class="bi bi-download me-2"></i>Exportar CSV
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
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-primary"><?php echo number_format($stats['total']); ?></h3>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-danger"><?php echo number_format($stats['nuevos']); ?></h3>
                            <small class="text-muted">Nuevos</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-warning"><?php echo number_format($stats['en_proceso']); ?></h3>
                            <small class="text-muted">En Proceso</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-info"><?php echo number_format($stats['respondidos']); ?></h3>
                            <small class="text-muted">Respondidos</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-success"><?php echo number_format($stats['cerrados']); ?></h3>
                            <small class="text-muted">Cerrados</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-primary"><?php echo number_format($stats['hoy']); ?></h3>
                            <small class="text-muted">Hoy</small>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="">Todos</option>
                                    <option value="nuevo" <?php echo $filtro_estado === 'nuevo' ? 'selected' : ''; ?>>Nuevo</option>
                                    <option value="en_proceso" <?php echo $filtro_estado === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                    <option value="respondido" <?php echo $filtro_estado === 'respondido' ? 'selected' : ''; ?>>Respondido</option>
                                    <option value="cerrado" <?php echo $filtro_estado === 'cerrado' ? 'selected' : ''; ?>>Cerrado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Asunto</label>
                                <input type="text" class="form-control" name="asunto" value="<?php echo esc($filtro_asunto); ?>" placeholder="Buscar por asunto">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Desde</label>
                                <input type="date" class="form-control" name="fecha_desde" value="<?php echo esc($filtro_fecha_desde); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Hasta</label>
                                <input type="date" class="form-control" name="fecha_hasta" value="<?php echo esc($filtro_fecha_hasta); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Búsqueda</label>
                                <input type="text" class="form-control" name="busqueda" value="<?php echo esc($busqueda); ?>" placeholder="Buscar...">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-2"></i>Filtrar
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Listado -->
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Mensajes (<?php echo number_format($total_messages); ?>)</h5>
                            <?php if (function_exists('hasPermission') && hasPermission('contacto', 'editar')): ?>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" id="bulk-action" style="width: auto;">
                                    <option value="">Acción masiva</option>
                                    <option value="marcar_leido">Marcar como leído</option>
                                    <option value="marcar_cerrado">Cerrar</option>
                                    <?php if (function_exists('hasPermission') && hasPermission('contacto', 'eliminar')): ?>
                                    <option value="eliminar">Eliminar</option>
                                    <?php endif; ?>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary" onclick="applyBulkAction()">
                                    <i class="bi bi-check-circle me-1"></i>Aplicar
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($mensajes)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay mensajes</h4>
                            <p class="text-muted">No se encontraron mensajes con los filtros aplicados</p>
                        </div>
                        <?php else: ?>
                        <form id="bulk-form" method="POST" action="">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                            </th>
                                            <th>Contacto</th>
                                            <th>Asunto</th>
                                            <th>Estado</th>
                                            <th>Asignado</th>
                                            <th>Fecha</th>
                                            <th width="100">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mensajes as $msg): ?>
                                        <tr class="message-row <?php echo $msg['status'] === 'nuevo' ? 'unread' : ''; ?>" onclick="window.location.href='view.php?id=<?php echo $msg['id']; ?>'">
                                            <td onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="<?php echo $msg['id']; ?>" class="message-checkbox">
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo esc($msg['nombre']); ?></strong>
                                                    <?php if ($msg['status'] === 'nuevo'): ?>
                                                    <span class="badge bg-danger status-badge ms-2">Nuevo</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bi bi-envelope me-1"></i><?php echo esc($msg['email']); ?>
                                                    <?php if ($msg['telefono']): ?>
                                                    | <i class="bi bi-telephone me-1"></i><?php echo esc($msg['telefono']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div><?php echo esc($msg['asunto']); ?></div>
                                                <?php if ($msg['institucion']): ?>
                                                <small class="text-muted">
                                                    <i class="bi bi-building me-1"></i><?php echo esc($msg['institucion']); ?>
                                                </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_colors = [
                                                    'nuevo' => 'danger',
                                                    'en_proceso' => 'warning',
                                                    'respondido' => 'info',
                                                    'cerrado' => 'success'
                                                ];
                                                $status_labels = [
                                                    'nuevo' => 'Nuevo',
                                                    'en_proceso' => 'En Proceso',
                                                    'respondido' => 'Respondido',
                                                    'cerrado' => 'Cerrado'
                                                ];
                                                ?>
                                                <span class="badge bg-<?php echo $status_colors[$msg['status']] ?? 'secondary'; ?>">
                                                    <?php echo $status_labels[$msg['status']] ?? ucfirst($msg['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($msg['asignado_nombre']): ?>
                                                <small>
                                                    <i class="bi bi-person-check me-1"></i><?php echo esc($msg['asignado_nombre']); ?>
                                                </small>
                                                <?php else: ?>
                                                <small class="text-muted">Sin asignar</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <?php echo date('d/m/Y', strtotime($msg['created_at'])); ?><br>
                                                    <span class="text-muted"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                                                </small>
                                            </td>
                                            <td onclick="event.stopPropagation();">
                                                <a href="view.php?id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="bulk_action" id="bulk-action-input">
                        </form>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <?php if ($total_pages > 1): ?>
                    <div class="card-footer bg-white">
                        <nav aria-label="Paginación">
                            <ul class="pagination mb-0 justify-content-center">
                                <?php
                                $query_params = $_GET;
                                unset($query_params['page']);
                                $base_url = 'index.php?' . http_build_query($query_params) . '&page=';
                                ?>
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $base_url . ($page - 1); ?>">Anterior</a>
                                </li>
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $base_url . $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $base_url . ($page + 1); ?>">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.message-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }
        
        function applyBulkAction() {
            const action = document.getElementById('bulk-action').value;
            const checked = document.querySelectorAll('.message-checkbox:checked');
            
            if (!action) {
                alert('Selecciona una acción');
                return;
            }
            
            if (checked.length === 0) {
                alert('Selecciona al menos un mensaje');
                return;
            }
            
            if (action === 'eliminar' && !confirm('¿Estás seguro de eliminar ' + checked.length + ' mensaje(s)?')) {
                return;
            }
            
            document.getElementById('bulk-action-input').value = action;
            document.getElementById('bulk-form').submit();
        }
    </script>
</body>
</html>

