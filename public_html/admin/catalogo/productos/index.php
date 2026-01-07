<?php
/**
 * ========================================
 * ADMIN - LISTADO DE PRODUCTOS
 * ========================================
 * 
 * Listado completo de productos con filtros, búsqueda y paginación
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/connection.php';
require_once __DIR__ . '/../../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('catalogo', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar acciones masivas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    try {
        if (function_exists('checkPermission')) {
            checkPermission('catalogo', 'editar');
        }
        
        $bulk_action = $_POST['bulk_action'];
        $selected_ids = $_POST['selected_products'] ?? [];
        
        if (empty($selected_ids)) {
            throw new Exception('No se seleccionaron productos');
        }
        
        $ids_placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
        
        if ($bulk_action === 'activate') {
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET estado = 'activo', updated_at = NOW() WHERE id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' producto(s) activado(s) exitosamente';
            
        } elseif ($bulk_action === 'deactivate') {
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET estado = 'inactivo', updated_at = NOW() WHERE id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' producto(s) desactivado(s) exitosamente';
            
        } elseif ($bulk_action === 'draft') {
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET estado = 'borrador', updated_at = NOW() WHERE id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' producto(s) movido(s) a borrador';
            
        } elseif ($bulk_action === 'feature') {
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET destacado = 1, updated_at = NOW() WHERE id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' producto(s) marcado(s) como destacado(s)';
            
        } elseif ($bulk_action === 'unfeature') {
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET destacado = 0, updated_at = NOW() WHERE id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' producto(s) desmarcado(s) como destacado(s)';
            
        } elseif ($bulk_action === 'delete') {
            if (function_exists('checkPermission')) {
                checkPermission('catalogo', 'eliminar');
            }
            
            // Eliminar imágenes asociadas
            $stmt = $pdo->prepare("SELECT imagen_url FROM catalogo_producto_imagenes WHERE producto_id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $imagenes = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($imagenes as $imagen) {
                $imagen_path = __DIR__ . '/../../../' . $imagen;
                if (file_exists($imagen_path)) {
                    @unlink($imagen_path);
                }
            }
            
            // Eliminar documentos asociados
            $stmt = $pdo->prepare("SELECT archivo_url FROM catalogo_producto_documentos WHERE producto_id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $documentos = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($documentos as $documento) {
                $doc_path = __DIR__ . '/../../../' . $documento;
                if (file_exists($doc_path)) {
                    @unlink($doc_path);
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM catalogo_productos WHERE id IN ($ids_placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' producto(s) eliminado(s) exitosamente';
        }
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'catalogo', null, 'productos', [
                'accion' => $bulk_action,
                'productos_afectados' => count($selected_ids)
            ]);
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener filtros
$filtro_marca = isset($_GET['marca']) ? (int)$_GET['marca'] : 0;
$filtro_categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$filtro_estado = isset($_GET['estado']) ? sanitizeInput($_GET['estado']) : '';
$filtro_destacado = isset($_GET['destacado']) ? (int)$_GET['destacado'] : 0;
$busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_marca > 0) {
    $where_conditions[] = 'p.marca_id = ?';
    $params[] = $filtro_marca;
}

if ($filtro_categoria > 0) {
    $where_conditions[] = 'p.categoria_id = ?';
    $params[] = $filtro_categoria;
}

if (!empty($filtro_estado)) {
    $where_conditions[] = 'p.estado = ?';
    $params[] = $filtro_estado;
}

if ($filtro_destacado > 0) {
    $where_conditions[] = 'p.destacado = 1';
}

if (!empty($busqueda)) {
    $where_conditions[] = '(p.nombre LIKE ? OR p.codigo LIKE ? OR p.descripcion_corta LIKE ?)';
    $search_term = '%' . $busqueda . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Contar total
$sql_count = "
    SELECT COUNT(*) as total
    FROM catalogo_productos p
    {$where_clause}
";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_productos = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

$total_pages = ceil($total_productos / $per_page);

// Obtener productos
$sql_productos = "
    SELECT p.*, 
           m.nombre as marca_nombre, 
           m.logo as marca_logo,
           c.nombre as categoria_nombre,
           (SELECT imagen_url FROM catalogo_producto_imagenes WHERE producto_id = p.id AND es_principal = 1 LIMIT 1) as imagen_principal
    FROM catalogo_productos p
    LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
    LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
    {$where_clause}
    ORDER BY p.created_at DESC
    LIMIT {$per_page} OFFSET {$offset}
";
$stmt_productos = $pdo->prepare($sql_productos);
$stmt_productos->execute($params);
$productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);

// Obtener marcas para filtro
$stmt_marcas = $pdo->query("SELECT id, nombre FROM catalogo_marcas WHERE estado = 'activo' ORDER BY nombre");
$marcas = $stmt_marcas->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías para filtro
$stmt_categorias = $pdo->query("SELECT id, nombre FROM catalogo_categorias WHERE estado = 'activo' ORDER BY nombre");
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'index.php';
$current_dir = 'productos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: var(--border-radius);
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: var(--shadow);
        }
        
        .page-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem;
        }
        
        .filters-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .filters-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            cursor: pointer;
            user-select: none;
        }
        
        .filters-header:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }
        
        .table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .table-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 2px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #495057;
            padding: 1rem;
            white-space: nowrap;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
            transform: scale(1.001);
        }
        
        .product-image-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
            text-decoration: none;
        }
        
        .product-name:hover {
            color: var(--secondary-color);
        }
        
        .badge-sm {
            font-size: 0.7rem;
            padding: 0.35em 0.65em;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-buttons .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .stats-bar {
            background: white;
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .bulk-actions {
            background: white;
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .pagination-wrapper {
            background: white;
            border-radius: var(--border-radius);
            padding: 1rem;
            box-shadow: var(--shadow);
        }
        
        .pagination {
            margin: 0;
        }
        
        .empty-state {
            background: white;
            border-radius: var(--border-radius);
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: var(--shadow);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .admin-content {
                padding: 1rem;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-box-seam me-2"></i>Productos
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona el catálogo de productos</p>
                        </div>
                        <?php if (function_exists('can') && can('catalogo', 'crear')): ?>
                        <a href="create.php" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Producto
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Filtros -->
                <div class="filters-card">
                    <div class="filters-header" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-funnel me-2"></i>
                                <strong>Filtros de Búsqueda</strong>
                                <?php if ($busqueda || $filtro_marca || $filtro_categoria || $filtro_estado || $filtro_destacado): ?>
                                <span class="badge bg-primary ms-2">Activos</span>
                                <?php endif; ?>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    <div class="collapse" id="filtersCollapse">
                        <div class="card-body">
                            <form method="GET" action="" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Búsqueda</label>
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           name="busqueda" 
                                           value="<?php echo esc($busqueda); ?>" 
                                           placeholder="Nombre, código, descripción...">
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Marca</label>
                                    <select name="marca" class="form-select form-select-sm">
                                        <option value="0">Todas</option>
                                        <?php foreach ($marcas as $marca): ?>
                                        <option value="<?php echo $marca['id']; ?>" <?php echo $filtro_marca == $marca['id'] ? 'selected' : ''; ?>>
                                            <?php echo esc($marca['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Categoría</label>
                                    <select name="categoria" class="form-select form-select-sm">
                                        <option value="0">Todas</option>
                                        <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $filtro_categoria == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo esc($cat['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Estado</label>
                                    <select name="estado" class="form-select form-select-sm">
                                        <option value="">Todos</option>
                                        <option value="activo" <?php echo $filtro_estado === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo $filtro_estado === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        <option value="borrador" <?php echo $filtro_estado === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Destacado</label>
                                    <select name="destacado" class="form-select form-select-sm">
                                        <option value="0">Todos</option>
                                        <option value="1" <?php echo $filtro_destacado == 1 ? 'selected' : ''; ?>>Solo Destacados</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-search me-1"></i>Buscar
                                    </button>
                                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-circle me-1"></i>Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas y Acciones -->
                <?php if (!empty($productos)): ?>
                <div class="stats-bar">
                    <div>
                        <span class="text-muted small">Mostrando</span>
                        <strong><?php echo count($productos); ?></strong>
                        <span class="text-muted small">de</span>
                        <strong><?php echo number_format($total_productos); ?></strong>
                        <span class="text-muted small">producto(s)</span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <form method="POST" action="" id="bulk-form" class="d-flex gap-2 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label small" for="select-all">
                                    Seleccionar todos
                                </label>
                            </div>
                            
                            <select name="bulk_action" class="form-select form-select-sm" style="width: auto; min-width: 150px;" required>
                                <option value="">Acción masiva...</option>
                                <option value="activate">Activar</option>
                                <option value="deactivate">Desactivar</option>
                                <option value="draft">Mover a Borrador</option>
                                <option value="feature">Marcar como Destacado</option>
                                <option value="unfeature">Quitar Destacado</option>
                                <option value="delete">Eliminar</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Aplicar
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Lista de productos -->
                <?php if (empty($productos)): ?>
                <div class="empty-state">
                    <i class="bi bi-box"></i>
                    <h4 class="text-muted mt-3">No se encontraron productos</h4>
                    <p class="text-muted"><?php echo !empty($busqueda) || $filtro_marca || $filtro_categoria || $filtro_estado ? 'Intenta ajustar los filtros' : 'Comienza creando el primer producto'; ?></p>
                    <?php if (empty($busqueda) && !$filtro_marca && !$filtro_categoria && !$filtro_estado): ?>
                    <?php if (function_exists('can') && can('catalogo', 'crear')): ?>
                    <a href="create.php" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Crear Primer Producto
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="table-container">
                    <div class="table-header">
                        <div>
                            <strong>Lista de Productos</strong>
                            <span class="text-muted small ms-2">(<?php echo number_format($total_productos); ?> total)</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="select-all-table">
                                    </th>
                                    <th style="width: 80px;">Imagen</th>
                                    <th>Producto</th>
                                    <th style="width: 120px;">Código</th>
                                    <th style="width: 150px;">Marca</th>
                                    <th style="width: 150px;">Categoría</th>
                                    <th style="width: 100px;">Estado</th>
                                    <th style="width: 120px;" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td>
                                        <input class="form-check-input product-checkbox" 
                                               type="checkbox" 
                                               name="selected_products[]" 
                                               value="<?php echo $producto['id']; ?>"
                                               form="bulk-form">
                                    </td>
                                    <td>
                                        <?php if ($producto['imagen_principal']): ?>
                                        <img src="<?php echo SITE_URL . '/' . esc($producto['imagen_principal']); ?>" 
                                             alt="<?php echo esc($producto['nombre']); ?>" 
                                             class="product-image-thumb"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\'%3E%3Crect fill=\'%23f8f9fa\' width=\'60\' height=\'60\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'10\'%3EN/A%3C/text%3E%3C/svg%3E'">
                                        <?php else: ?>
                                        <div class="product-image-thumb d-flex align-items-center justify-content-center bg-light text-muted">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <a href="edit.php?id=<?php echo $producto['id']; ?>" class="product-name">
                                                <?php echo esc($producto['nombre']); ?>
                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <?php if ($producto['destacado']): ?>
                                            <span class="badge bg-danger badge-sm me-1">
                                                <i class="bi bi-star-fill"></i> Destacado
                                            </span>
                                            <?php endif; ?>
                                            <?php if ($producto['nuevo']): ?>
                                            <span class="badge bg-success badge-sm me-1">
                                                <i class="bi bi-sparkles"></i> Nuevo
                                            </span>
                                            <?php endif; ?>
                                            <?php if ($producto['promocion']): ?>
                                            <span class="badge bg-warning badge-sm">
                                                <i class="bi bi-tag-fill"></i> Promoción
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="small"><?php echo esc($producto['codigo']); ?></code>
                                    </td>
                                    <td>
                                        <?php if ($producto['marca_nombre']): ?>
                                        <span class="badge bg-info"><?php echo esc($producto['marca_nombre']); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($producto['categoria_nombre']): ?>
                                        <span class="badge bg-secondary"><?php echo esc($producto['categoria_nombre']); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $producto['estado'] === 'activo' ? 'bg-success' : 
                                                ($producto['estado'] === 'inactivo' ? 'bg-secondary' : 'bg-warning'); 
                                        ?>">
                                            <?php echo ucfirst($producto['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (function_exists('can') && can('catalogo', 'editar')): ?>
                                            <a href="edit.php?id=<?php echo $producto['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="view.php?id=<?php echo $producto['id']; ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                <?php
                // Construir URL base con filtros
                $url_params = [];
                if ($filtro_marca) $url_params['marca'] = $filtro_marca;
                if ($filtro_categoria) $url_params['categoria'] = $filtro_categoria;
                if ($filtro_estado) $url_params['estado'] = $filtro_estado;
                if ($filtro_destacado) $url_params['destacado'] = $filtro_destacado;
                if ($busqueda) $url_params['busqueda'] = $busqueda;
                $url_base = '?' . http_build_query($url_params);
                ?>
                <div class="pagination-wrapper">
                    <nav aria-label="Paginación">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $url_base . '&page=' . ($page - 1); ?>">
                                    <i class="bi bi-chevron-left"></i> Anterior
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $url_base . '&page=1'; ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo $url_base . '&page=' . $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $url_base . '&page=' . $total_pages; ?>">
                                    <?php echo $total_pages; ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $url_base . '&page=' . ($page + 1); ?>">
                                    Siguiente <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Seleccionar todos (ambos checkboxes)
        const selectAllCheckboxes = document.querySelectorAll('#select-all, #select-all-table');
        selectAllCheckboxes.forEach(function(checkbox) {
            checkbox?.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.product-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                // Sincronizar ambos checkboxes
                selectAllCheckboxes.forEach(cb => {
                    if (cb !== this) cb.checked = this.checked;
                });
            });
        });
        
        // Sincronizar checkboxes individuales
        document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const allChecked = document.querySelectorAll('.product-checkbox:checked').length;
                const total = document.querySelectorAll('.product-checkbox').length;
                selectAllCheckboxes.forEach(cb => {
                    cb.checked = allChecked === total;
                    cb.indeterminate = allChecked > 0 && allChecked < total;
                });
            });
        });
        
        // Validar acción masiva
        document.getElementById('bulk-form')?.addEventListener('submit', function(e) {
            const selected = document.querySelectorAll('.product-checkbox:checked');
            const action = this.querySelector('[name="bulk_action"]').value;
            
            if (selected.length === 0) {
                e.preventDefault();
                alert('Por favor selecciona al menos un producto');
                return false;
            }
            
            if (!action) {
                e.preventDefault();
                alert('Por favor selecciona una acción');
                return false;
            }
            
            if (action === 'delete' && !confirm('¿Estás seguro de eliminar ' + selected.length + ' producto(s)? Esta acción no se puede deshacer.')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Auto-expandir filtros si hay filtros activos
        <?php if ($busqueda || $filtro_marca || $filtro_categoria || $filtro_estado || $filtro_destacado): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const filtersCollapse = document.getElementById('filtersCollapse');
            if (filtersCollapse) {
                const bsCollapse = new bootstrap.Collapse(filtersCollapse, {
                    toggle: true
                });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>

