<?php
/**
 * ========================================
 * ADMIN - LOGS DE AUDITORÍA
 * ========================================
 * 
 * Vista de bitácora de actividad de usuarios
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
    checkPermission('usuarios', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Verificar si existe la tabla audit_logs
$tabla_existe = false;
try {
    $sql_check = "SHOW TABLES LIKE 'audit_logs'";
    $stmt_check = $pdo->query($sql_check);
    $tabla_existe = $stmt_check->rowCount() > 0;
} catch (Exception $e) {
    $tabla_existe = false;
}

// Obtener filtros
$filtro_usuario = isset($_GET['usuario']) ? (int)$_GET['usuario'] : 0;
$filtro_accion = isset($_GET['accion']) ? sanitizeInput($_GET['accion']) : '';
$filtro_modulo = isset($_GET['modulo']) ? sanitizeInput($_GET['modulo']) : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? sanitizeInput($_GET['fecha_desde']) : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? sanitizeInput($_GET['fecha_hasta']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_usuario > 0) {
    $where_conditions[] = 'al.usuario_id = ?';
    $params[] = $filtro_usuario;
}

if (!empty($filtro_accion)) {
    $where_conditions[] = 'al.accion = ?';
    $params[] = $filtro_accion;
}

if (!empty($filtro_modulo)) {
    $where_conditions[] = 'al.modulo = ?';
    $params[] = $filtro_modulo;
}

if (!empty($filtro_fecha_desde)) {
    $where_conditions[] = 'DATE(al.created_at) >= ?';
    $params[] = $filtro_fecha_desde;
}

if (!empty($filtro_fecha_hasta)) {
    $where_conditions[] = 'DATE(al.created_at) <= ?';
    $params[] = $filtro_fecha_hasta;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener logs
$logs = [];
$total_logs = 0;

if ($tabla_existe) {
    // Contar total
    $sql_count = "
        SELECT COUNT(*) as total
        FROM audit_logs al
        {$where_clause}
    ";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_logs = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Obtener logs con paginación
    $sql_logs = "
        SELECT al.*, u.nombre as usuario_nombre, u.username as usuario_username
        FROM audit_logs al
        LEFT JOIN admin_usuarios u ON al.usuario_id = u.id
        {$where_clause}
        ORDER BY al.created_at DESC
        LIMIT {$per_page} OFFSET {$offset}
    ";
    $stmt_logs = $pdo->prepare($sql_logs);
    $stmt_logs->execute($params);
    $logs = $stmt_logs->fetchAll(PDO::FETCH_ASSOC);
}

$total_pages = ceil($total_logs / $per_page);

// Obtener usuarios para filtro
$sql_usuarios = "SELECT id, nombre, username FROM admin_usuarios ORDER BY nombre";
$stmt_usuarios = $pdo->query($sql_usuarios);
$usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

// Obtener acciones únicas para filtro
$acciones_unicas = [];
if ($tabla_existe) {
    $sql_acciones = "SELECT DISTINCT accion FROM audit_logs ORDER BY accion";
    $stmt_acciones = $pdo->query($sql_acciones);
    $acciones_unicas = $stmt_acciones->fetchAll(PDO::FETCH_COLUMN);
}

// Obtener módulos únicos para filtro
$modulos_unicos = [];
if ($tabla_existe) {
    $sql_modulos = "SELECT DISTINCT modulo FROM audit_logs WHERE modulo IS NOT NULL ORDER BY modulo";
    $stmt_modulos = $pdo->query($sql_modulos);
    $modulos_unicos = $stmt_modulos->fetchAll(PDO::FETCH_COLUMN);
}

$current_page = 'logs.php';
$current_dir = 'usuarios';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Auditoría - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .log-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #0066cc;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .log-item.login {
            border-left-color: #28a745;
        }
        
        .log-item.logout {
            border-left-color: #6c757d;
        }
        
        .log-item.acceso_denegado {
            border-left-color: #dc3545;
        }
        
        .log-item.crear, .log-item.editar, .log-item.eliminar {
            border-left-color: #ffc107;
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
                                <i class="bi bi-journal-text me-2"></i>Logs de Auditoría
                            </h2>
                            <p class="mb-0 opacity-75">Bitácora de actividad del sistema</p>
                        </div>
                        <a href="../usuarios.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Usuarios
                        </a>
                    </div>
                </div>
                
                <?php if (!$tabla_existe): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    La tabla <code>audit_logs</code> no existe. Ejecuta el script SQL: <code>database/fase2/05_create_rbac_tables.sql</code>
                </div>
                <?php else: ?>
                
                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Usuario</label>
                                <select name="usuario" class="form-select">
                                    <option value="0">Todos</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo $usuario['id']; ?>" <?php echo $filtro_usuario == $usuario['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc($usuario['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Acción</label>
                                <select name="accion" class="form-select">
                                    <option value="">Todas</option>
                                    <?php foreach ($acciones_unicas as $accion): ?>
                                    <option value="<?php echo esc($accion); ?>" <?php echo $filtro_accion === $accion ? 'selected' : ''; ?>>
                                        <?php echo esc($accion); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Módulo</label>
                                <select name="modulo" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($modulos_unicos as $modulo): ?>
                                    <option value="<?php echo esc($modulo); ?>" <?php echo $filtro_modulo === $modulo ? 'selected' : ''; ?>>
                                        <?php echo esc($modulo); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Desde</label>
                                <input type="date" name="fecha_desde" class="form-control" value="<?php echo esc($filtro_fecha_desde); ?>">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control" value="<?php echo esc($filtro_fecha_hasta); ?>">
                            </div>
                            
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total de registros: <?php echo number_format($total_logs); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de logs -->
                <?php if (!empty($logs)): ?>
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($logs as $log): ?>
                        <div class="log-item <?php echo esc($log['accion']); ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="me-2"><?php echo esc($log['usuario_nombre'] ?? 'Usuario eliminado'); ?></strong>
                                        <span class="badge bg-secondary me-2"><?php echo esc($log['accion']); ?></span>
                                        <?php if ($log['modulo']): ?>
                                        <span class="badge bg-info"><?php echo esc($log['modulo']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($log['entidad_tipo'] && $log['entidad_id']): ?>
                                    <small class="text-muted">
                                        <?php echo esc($log['entidad_tipo']); ?> #<?php echo $log['entidad_id']; ?>
                                    </small>
                                    <?php endif; ?>
                                    
                                    <?php if ($log['detalles']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?php 
                                            $detalles = json_decode($log['detalles'], true);
                                            if ($detalles) {
                                                echo '<pre class="mb-0" style="font-size: 0.8rem;">' . esc(json_encode($detalles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                                            } else {
                                                echo esc($log['detalles']);
                                            }
                                            ?>
                                        </small>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?>
                                            <?php if ($log['ip_address']): ?>
                                            | <i class="bi bi-geo-alt me-1"></i><?php echo esc($log['ip_address']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Paginación" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&usuario=<?php echo $filtro_usuario; ?>&accion=<?php echo urlencode($filtro_accion); ?>&modulo=<?php echo urlencode($filtro_modulo); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>">
                                Anterior
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&usuario=<?php echo $filtro_usuario; ?>&accion=<?php echo urlencode($filtro_accion); ?>&modulo=<?php echo urlencode($filtro_modulo); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&usuario=<?php echo $filtro_usuario; ?>&accion=<?php echo urlencode($filtro_accion); ?>&modulo=<?php echo urlencode($filtro_modulo); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>">
                                Siguiente
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No se encontraron registros de actividad con los filtros seleccionados.
                </div>
                <?php endif; ?>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

