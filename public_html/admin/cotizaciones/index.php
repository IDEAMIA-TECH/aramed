<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE COTIZACIONES AVANZADO
 * ========================================
 * 
 * Listado avanzado de cotizaciones con filtros y gestión
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
    checkPermission('cotizaciones', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar acciones masivas
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['selected_ids'])) {
    try {
        if (function_exists('checkPermission')) {
            checkPermission('cotizaciones', 'editar');
        }
        
        $bulk_action = $_POST['bulk_action'];
        $selected_ids = array_map('intval', $_POST['selected_ids']);
        $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
        
        if ($bulk_action === 'cambiar_estado') {
            $nuevo_estado = $_POST['nuevo_estado'] ?? '';
            if ($nuevo_estado) {
                $stmt = $pdo->prepare("UPDATE cotizaciones SET estado_cotizacion = ?, updated_at = NOW() WHERE id IN ($placeholders)");
                $params = array_merge([$nuevo_estado], $selected_ids);
                $stmt->execute($params);
                
                // Registrar en auditoría
                foreach ($selected_ids as $cotizacion_id) {
                    if (function_exists('logActivity')) {
                        logActivity($current_user['id'], 'editar', 'cotizaciones', $cotizacion_id, 'cotizacion', [
                            'accion' => 'cambio_estado_masivo',
                            'nuevo_estado' => $nuevo_estado
                        ]);
                    }
                }
                
                $success_message = count($selected_ids) . ' cotización(es) actualizada(s)';
            }
        } elseif ($bulk_action === 'asignar_ejecutivo') {
            $ejecutivo_id = !empty($_POST['ejecutivo_id']) ? (int)$_POST['ejecutivo_id'] : null;
            $stmt = $pdo->prepare("UPDATE cotizaciones SET assigned_to = ?, updated_at = NOW() WHERE id IN ($placeholders)");
            $params = array_merge([$ejecutivo_id], $selected_ids);
            $stmt->execute($params);
            
            $success_message = count($selected_ids) . ' cotización(es) asignada(s)';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Filtros
$filtro_estado = $_GET['estado'] ?? '';
$filtro_ejecutivo = $_GET['ejecutivo'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';
$filtro_marca = $_GET['marca'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Construir consulta
$where_conditions = [];
$params = [];
$joins = [];

if ($filtro_estado) {
    $where_conditions[] = "c.estado_cotizacion = ?";
    $params[] = $filtro_estado;
}

if ($filtro_ejecutivo) {
    $where_conditions[] = "c.assigned_to = ?";
    $params[] = (int)$filtro_ejecutivo;
}

if ($filtro_fecha_desde) {
    $where_conditions[] = "DATE(c.created_at) >= ?";
    $params[] = $filtro_fecha_desde;
}

if ($filtro_fecha_hasta) {
    $where_conditions[] = "DATE(c.created_at) <= ?";
    $params[] = $filtro_fecha_hasta;
}

if ($filtro_marca || $filtro_categoria) {
    $joins[] = "LEFT JOIN cotizacion_items ci ON c.id = ci.cotizacion_id";
    $joins[] = "LEFT JOIN catalogo_productos p ON ci.producto_id = p.id";
    
    if ($filtro_marca) {
        $where_conditions[] = "p.marca_id = ?";
        $params[] = (int)$filtro_marca;
    }
    
    if ($filtro_categoria) {
        $where_conditions[] = "p.categoria_id = ?";
        $params[] = (int)$filtro_categoria;
    }
}

if ($busqueda) {
    $where_conditions[] = "(c.folio LIKE ? OR c.institucion LIKE ? OR c.nombre LIKE ? OR c.email_oficial LIKE ? OR c.producto_interes LIKE ?)";
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
$join_clause = !empty($joins) ? implode(' ', $joins) : '';

// Obtener total de registros
$count_sql = "SELECT COUNT(DISTINCT c.id) FROM cotizaciones c $join_clause $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_cotizaciones = $count_stmt->fetchColumn();
$total_pages = ceil($total_cotizaciones / $per_page);

// Obtener cotizaciones
$sql = "SELECT DISTINCT c.*, 
               au.nombre as ejecutivo_nombre,
               au.email as ejecutivo_email,
               COUNT(DISTINCT ci.id) as total_items
        FROM cotizaciones c
        LEFT JOIN admin_usuarios au ON c.assigned_to = au.id
        LEFT JOIN cotizacion_items ci ON c.id = ci.cotizacion_id
        $join_clause
        $where_clause
        GROUP BY c.id
        ORDER BY c.created_at DESC
        LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$stats = [];
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado_cotizacion = 'nueva' THEN 1 ELSE 0 END) as nuevas,
    SUM(CASE WHEN estado_cotizacion = 'en_seguimiento' THEN 1 ELSE 0 END) as en_seguimiento,
    SUM(CASE WHEN estado_cotizacion = 'cotizada' THEN 1 ELSE 0 END) as cotizadas,
    SUM(CASE WHEN estado_cotizacion = 'enviada' THEN 1 ELSE 0 END) as enviadas,
    SUM(CASE WHEN estado_cotizacion = 'cerrada_ganada' THEN 1 ELSE 0 END) as ganadas,
    SUM(CASE WHEN estado_cotizacion = 'cerrada_perdida' THEN 1 ELSE 0 END) as perdidas,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as hoy
FROM cotizaciones";
$stats_stmt = $pdo->query($stats_sql);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Obtener ejecutivos para filtro
$stmt = $pdo->query("SELECT id, nombre FROM admin_usuarios WHERE estado = 'activo' ORDER BY nombre");
$ejecutivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener marcas y categorías para filtros
$stmt = $pdo->query("SELECT id, nombre FROM catalogo_marcas WHERE estado = 'activo' ORDER BY nombre");
$marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id, nombre FROM catalogo_categorias WHERE estado = 'activo' ORDER BY nombre");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'index.php';
$current_dir = 'cotizaciones';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .quote-row {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .quote-row:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transform: translateX(5px);
        }
        
        .quote-row.nueva {
            border-left: 4px solid #dc3545;
        }
        
        .quote-row.en_seguimiento {
            border-left: 4px solid #ffc107;
        }
        
        .quote-row.cotizada {
            border-left: 4px solid #0dcaf0;
        }
        
        .quote-row.enviada {
            border-left: 4px solid #198754;
        }
        
        .folio-badge {
            font-family: monospace;
            font-weight: bold;
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
                                <i class="bi bi-file-earmark-text me-2"></i>Cotizaciones
                            </h2>
                            <p class="mb-0 opacity-75">Sistema avanzado de gestión de cotizaciones</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="export.php?<?php echo http_build_query($_GET); ?>" class="btn btn-light">
                                <i class="bi bi-download me-2"></i>Exportar
                            </a>
                        </div>
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
                            <h3 class="mb-1 text-danger"><?php echo number_format($stats['nuevas']); ?></h3>
                            <small class="text-muted">Nuevas</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-warning"><?php echo number_format($stats['en_seguimiento']); ?></h3>
                            <small class="text-muted">En Seguimiento</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-info"><?php echo number_format($stats['cotizadas']); ?></h3>
                            <small class="text-muted">Cotizadas</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-success"><?php echo number_format($stats['ganadas']); ?></h3>
                            <small class="text-muted">Ganadas</small>
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
                                    <option value="nueva" <?php echo $filtro_estado === 'nueva' ? 'selected' : ''; ?>>Nueva</option>
                                    <option value="en_seguimiento" <?php echo $filtro_estado === 'en_seguimiento' ? 'selected' : ''; ?>>En Seguimiento</option>
                                    <option value="cotizada" <?php echo $filtro_estado === 'cotizada' ? 'selected' : ''; ?>>Cotizada</option>
                                    <option value="enviada" <?php echo $filtro_estado === 'enviada' ? 'selected' : ''; ?>>Enviada</option>
                                    <option value="cerrada_ganada" <?php echo $filtro_estado === 'cerrada_ganada' ? 'selected' : ''; ?>>Cerrada (Ganada)</option>
                                    <option value="cerrada_perdida" <?php echo $filtro_estado === 'cerrada_perdida' ? 'selected' : ''; ?>>Cerrada (Perdida)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ejecutivo</label>
                                <select class="form-select" name="ejecutivo">
                                    <option value="">Todos</option>
                                    <?php foreach ($ejecutivos as $ejec): ?>
                                    <option value="<?php echo $ejec['id']; ?>" <?php echo $filtro_ejecutivo == $ejec['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc($ejec['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
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
                                <input type="text" class="form-control" name="busqueda" value="<?php echo esc($busqueda); ?>" placeholder="Folio, cliente...">
                            </div>
                            <?php if (!empty($marcas)): ?>
                            <div class="col-md-3">
                                <label class="form-label">Marca</label>
                                <select class="form-select" name="marca">
                                    <option value="">Todas</option>
                                    <?php foreach ($marcas as $marca): ?>
                                    <option value="<?php echo $marca['id']; ?>" <?php echo $filtro_marca == $marca['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc($marca['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($categorias)): ?>
                            <div class="col-md-3">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" name="categoria">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $filtro_categoria == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc($cat['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
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
                            <h5 class="mb-0">Cotizaciones (<?php echo number_format($total_cotizaciones); ?>)</h5>
                            <?php if (function_exists('hasPermission') && hasPermission('cotizaciones', 'editar')): ?>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" id="bulk-action" style="width: auto;">
                                    <option value="">Acción masiva</option>
                                    <option value="cambiar_estado">Cambiar estado</option>
                                    <option value="asignar_ejecutivo">Asignar ejecutivo</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showBulkAction()">
                                    <i class="bi bi-check-circle me-1"></i>Aplicar
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($cotizaciones)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay cotizaciones</h4>
                            <p class="text-muted">No se encontraron cotizaciones con los filtros aplicados</p>
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
                                            <th>Folio</th>
                                            <th>Cliente</th>
                                            <th>Producto</th>
                                            <th>Estado</th>
                                            <th>Ejecutivo</th>
                                            <th>Items</th>
                                            <th>Fecha</th>
                                            <th width="100">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cotizaciones as $cot): ?>
                                        <tr class="quote-row <?php echo $cot['estado_cotizacion']; ?>" onclick="window.location.href='view.php?id=<?php echo $cot['id']; ?>'">
                                            <td onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="<?php echo $cot['id']; ?>" class="quote-checkbox">
                                            </td>
                                            <td>
                                                <span class="badge bg-primary folio-badge"><?php echo esc($cot['folio']); ?></span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo esc($cot['institucion']); ?></strong>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i><?php echo esc($cot['nombre']); ?>
                                                    | <i class="bi bi-envelope me-1"></i><?php echo esc($cot['email_oficial']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($cot['producto_interes']): ?>
                                                <small><?php echo esc($cot['producto_interes']); ?></small>
                                                <?php else: ?>
                                                <small class="text-muted">Sin especificar</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $estado_colors = [
                                                    'nueva' => 'danger',
                                                    'en_seguimiento' => 'warning',
                                                    'cotizada' => 'info',
                                                    'enviada' => 'success',
                                                    'cerrada_ganada' => 'success',
                                                    'cerrada_perdida' => 'secondary'
                                                ];
                                                $estado_labels = [
                                                    'nueva' => 'Nueva',
                                                    'en_seguimiento' => 'En Seguimiento',
                                                    'cotizada' => 'Cotizada',
                                                    'enviada' => 'Enviada',
                                                    'cerrada_ganada' => 'Ganada',
                                                    'cerrada_perdida' => 'Perdida'
                                                ];
                                                ?>
                                                <span class="badge bg-<?php echo $estado_colors[$cot['estado_cotizacion']] ?? 'secondary'; ?>">
                                                    <?php echo $estado_labels[$cot['estado_cotizacion']] ?? ucfirst($cot['estado_cotizacion']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($cot['ejecutivo_nombre']): ?>
                                                <small>
                                                    <i class="bi bi-person-check me-1"></i><?php echo esc($cot['ejecutivo_nombre']); ?>
                                                </small>
                                                <?php else: ?>
                                                <small class="text-muted">Sin asignar</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo $cot['total_items']; ?> item(s)</span>
                                            </td>
                                            <td>
                                                <small>
                                                    <?php echo date('d/m/Y', strtotime($cot['created_at'])); ?><br>
                                                    <span class="text-muted"><?php echo date('H:i', strtotime($cot['created_at'])); ?></span>
                                                </small>
                                            </td>
                                            <td onclick="event.stopPropagation();">
                                                <a href="view.php?id=<?php echo $cot['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="bulk_action" id="bulk-action-input">
                            <div id="bulk-options" style="display: none;" class="p-3 bg-light border-top">
                                <div id="bulk-estado-option" style="display: none;" class="mb-2">
                                    <label class="form-label">Nuevo Estado:</label>
                                    <select class="form-select form-select-sm" name="nuevo_estado" id="nuevo-estado">
                                        <option value="nueva">Nueva</option>
                                        <option value="en_seguimiento">En Seguimiento</option>
                                        <option value="cotizada">Cotizada</option>
                                        <option value="enviada">Enviada</option>
                                        <option value="cerrada_ganada">Cerrada (Ganada)</option>
                                        <option value="cerrada_perdida">Cerrada (Perdida)</option>
                                    </select>
                                </div>
                                <div id="bulk-ejecutivo-option" style="display: none;" class="mb-2">
                                    <label class="form-label">Ejecutivo:</label>
                                    <select class="form-select form-select-sm" name="ejecutivo_id" id="ejecutivo-id">
                                        <option value="">Sin asignar</option>
                                        <?php foreach ($ejecutivos as $ejec): ?>
                                        <option value="<?php echo $ejec['id']; ?>"><?php echo esc($ejec['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Confirmar</button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="cancelBulkAction()">Cancelar</button>
                            </div>
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
            const checkboxes = document.querySelectorAll('.quote-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }
        
        function showBulkAction() {
            const action = document.getElementById('bulk-action').value;
            const checked = document.querySelectorAll('.quote-checkbox:checked');
            
            if (!action) {
                alert('Selecciona una acción');
                return;
            }
            
            if (checked.length === 0) {
                alert('Selecciona al menos una cotización');
                return;
            }
            
            document.getElementById('bulk-action-input').value = action;
            document.getElementById('bulk-options').style.display = 'block';
            
            // Mostrar opciones según la acción
            if (action === 'cambiar_estado') {
                document.getElementById('bulk-estado-option').style.display = 'block';
                document.getElementById('bulk-ejecutivo-option').style.display = 'none';
            } else if (action === 'asignar_ejecutivo') {
                document.getElementById('bulk-estado-option').style.display = 'none';
                document.getElementById('bulk-ejecutivo-option').style.display = 'block';
            }
        }
        
        function cancelBulkAction() {
            document.getElementById('bulk-options').style.display = 'none';
            document.getElementById('bulk-action').value = '';
        }
    </script>
</body>
</html>

