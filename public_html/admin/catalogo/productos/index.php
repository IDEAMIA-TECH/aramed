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
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .product-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }
        
        .product-badges {
            position: absolute;
            top: 10px;
            right: 10px;
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
                        <a href="create.php" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Producto
                        </a>
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
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Búsqueda</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="busqueda" 
                                       value="<?php echo esc($busqueda); ?>" 
                                       placeholder="Nombre, código, descripción...">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Marca</label>
                                <select name="marca" class="form-select">
                                    <option value="0">Todas</option>
                                    <?php foreach ($marcas as $marca): ?>
                                    <option value="<?php echo $marca['id']; ?>" <?php echo $filtro_marca == $marca['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc($marca['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Categoría</label>
                                <select name="categoria" class="form-select">
                                    <option value="0">Todas</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $filtro_categoria == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc($cat['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="activo" <?php echo $filtro_estado === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="inactivo" <?php echo $filtro_estado === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                    <option value="borrador" <?php echo $filtro_estado === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Destacado</label>
                                <select name="destacado" class="form-select">
                                    <option value="0">Todos</option>
                                    <option value="1" <?php echo $filtro_destacado == 1 ? 'selected' : ''; ?>>Solo Destacados</option>
                                </select>
                            </div>
                            
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Acciones masivas -->
                <?php if (!empty($productos)): ?>
                <form method="POST" action="" id="bulk-form" class="mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                    <label class="form-check-label" for="select-all">
                                        Seleccionar todos
                                    </label>
                                </div>
                                
                                <div class="d-flex gap-2 flex-wrap">
                                    <select name="bulk_action" class="form-select form-select-sm" style="width: auto;" required>
                                        <option value="">Acción...</option>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Mostrando <strong><?php echo count($productos); ?></strong> de <strong><?php echo number_format($total_productos); ?></strong> producto(s)
                </div>
                
                <!-- Lista de productos -->
                <?php if (empty($productos)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">No se encontraron productos</h4>
                        <p class="text-muted"><?php echo !empty($busqueda) || $filtro_marca || $filtro_categoria || $filtro_estado ? 'Intenta ajustar los filtros' : 'Comienza creando el primer producto'; ?></p>
                        <?php if (empty($busqueda) && !$filtro_marca && !$filtro_categoria && !$filtro_estado): ?>
                        <a href="create.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Crear Primer Producto
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="product-card">
                            <div class="position-relative">
                                <?php if ($producto['imagen_principal']): ?>
                                <img src="<?php echo SITE_URL . '/' . esc($producto['imagen_principal']); ?>" 
                                     alt="<?php echo esc($producto['nombre']); ?>" 
                                     class="product-image"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'200\'%3E%3Crect fill=\'%23f8f9fa\' width=\'300\' height=\'200\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ESin imagen%3C/text%3E%3C/svg%3E'">
                                <?php else: ?>
                                <div class="product-image d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="product-badges">
                                    <?php if ($producto['destacado']): ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-star-fill"></i>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($producto['nuevo']): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-newspaper"></i>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($producto['promocion']): ?>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-tag-fill"></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <h5 class="card-title mb-2">
                                    <a href="edit.php?id=<?php echo $producto['id']; ?>" class="text-decoration-none">
                                        <?php echo esc($producto['nombre']); ?>
                                    </a>
                                </h5>
                                
                                <p class="text-muted small mb-2">
                                    <strong>Código:</strong> <?php echo esc($producto['codigo']); ?>
                                </p>
                                
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <?php if ($producto['marca_nombre']): ?>
                                    <span class="badge bg-info"><?php echo esc($producto['marca_nombre']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($producto['categoria_nombre']): ?>
                                    <span class="badge bg-secondary"><?php echo esc($producto['categoria_nombre']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge <?php 
                                        echo $producto['estado'] === 'activo' ? 'bg-success' : 
                                            ($producto['estado'] === 'inactivo' ? 'bg-secondary' : 'bg-warning'); 
                                    ?>">
                                        <?php echo ucfirst($producto['estado']); ?>
                                    </span>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input product-checkbox" 
                                               type="checkbox" 
                                               name="selected_products[]" 
                                               value="<?php echo $producto['id']; ?>"
                                               form="bulk-form">
                                    </div>
                                </div>
                                
                                <div class="mt-3 d-flex gap-2">
                                    <a href="edit.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="bi bi-pencil me-1"></i>Editar
                                    </a>
                                    <a href="view.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Paginación" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&marca=<?php echo $filtro_marca; ?>&categoria=<?php echo $filtro_categoria; ?>&estado=<?php echo urlencode($filtro_estado); ?>&destacado=<?php echo $filtro_destacado; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                Anterior
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&marca=<?php echo $filtro_marca; ?>&categoria=<?php echo $filtro_categoria; ?>&estado=<?php echo urlencode($filtro_estado); ?>&destacado=<?php echo $filtro_destacado; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&marca=<?php echo $filtro_marca; ?>&categoria=<?php echo $filtro_categoria; ?>&estado=<?php echo urlencode($filtro_estado); ?>&destacado=<?php echo $filtro_destacado; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                Siguiente
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Seleccionar todos
        document.getElementById('select-all')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
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
    </script>
</body>
</html>

