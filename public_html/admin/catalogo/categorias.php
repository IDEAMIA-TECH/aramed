<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE CATEGORÍAS DEL CATÁLOGO
 * ========================================
 * 
 * CRUD completo para categorías de productos
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
    checkPermission('catalogo', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar acciones
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar permisos para acciones específicas (GET)
if ($action === 'create') {
    if (function_exists('checkPermission')) {
        checkPermission('catalogo', 'crear');
    }
} elseif ($action === 'edit') {
    if (function_exists('checkPermission')) {
        checkPermission('catalogo', 'editar');
    }
} elseif ($action === 'delete') {
    if (function_exists('checkPermission')) {
        checkPermission('catalogo', 'eliminar');
    }
}

$success_message = '';
$error_message = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create' || $action === 'edit') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('catalogo', $action === 'create' ? 'crear' : 'editar');
            }
            
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $icono = trim($_POST['icono'] ?? '');
            $color = trim($_POST['color'] ?? '#0066CC');
            $estado = $_POST['estado'] ?? 'activo';
            $orden = (int)($_POST['orden'] ?? 0);
            
            if (empty($nombre)) {
                throw new Exception('El nombre es obligatorio');
            }
            
            // Generar slug
            $slug = generateSlug($nombre);
            
            // Verificar si el slug ya existe (excepto para el registro actual)
            $stmt = $pdo->prepare("SELECT id FROM catalogo_categorias WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            if ($stmt->fetch()) {
                $slug = $slug . '-' . time();
            }
            
            if ($action === 'create') {
                $stmt = $pdo->prepare("
                    INSERT INTO catalogo_categorias (nombre, slug, descripcion, icono, color, estado, orden, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$nombre, $slug, $descripcion, $icono, $color, $estado, $orden]);
                
                $categoria_id = $pdo->lastInsertId();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'crear', 'catalogo', $categoria_id, 'categoria', [
                        'nombre' => $nombre
                    ]);
                }
                
                $success_message = 'Categoría creada exitosamente';
                $action = 'list';
                
            } else { // edit
                $stmt = $pdo->prepare("
                    UPDATE catalogo_categorias 
                    SET nombre = ?, slug = ?, descripcion = ?, icono = ?, color = ?, estado = ?, orden = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$nombre, $slug, $descripcion, $icono, $color, $estado, $orden, $id]);
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'catalogo', $id, 'categoria', [
                        'nombre' => $nombre
                    ]);
                }
                
                $success_message = 'Categoría actualizada exitosamente';
                $action = 'list';
            }
            
        } elseif ($action === 'delete' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('catalogo', 'eliminar');
            }
            
            // Verificar si tiene productos asociados
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM catalogo_productos WHERE categoria_id = ?");
            $stmt->execute([$id]);
            $productos_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($productos_count > 0) {
                throw new Exception("No se puede eliminar la categoría porque tiene {$productos_count} producto(s) asociado(s)");
            }
            
            // Obtener nombre antes de eliminar
            $stmt = $pdo->prepare("SELECT nombre FROM catalogo_categorias WHERE id = ?");
            $stmt->execute([$id]);
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->prepare("DELETE FROM catalogo_categorias WHERE id = ?");
            $stmt->execute([$id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'catalogo', $id, 'categoria', [
                    'nombre' => $categoria['nombre'] ?? ''
                ]);
            }
            
            $success_message = 'Categoría eliminada exitosamente';
            $action = 'list';
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener datos para formularios
$categoria = null;
if (($action === 'edit' || $action === 'delete') && $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM catalogo_categorias WHERE id = ?");
        $stmt->execute([$id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$categoria) {
            $error_message = 'Categoría no encontrada';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Obtener lista de categorías
$categorias = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT c.*, 
                   COUNT(p.id) as productos_count
            FROM catalogo_categorias c
            LEFT JOIN catalogo_productos p ON c.id = p.categoria_id
            GROUP BY c.id
            ORDER BY c.orden ASC, c.nombre ASC
        ");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'categorias.php';
$current_dir = 'catalogo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .category-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .category-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            border: 2px solid #dee2e6;
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
                                <i class="bi bi-folder me-2"></i>Gestión de Categorías
                            </h2>
                            <p class="mb-0 opacity-75">Organiza las categorías del catálogo de productos</p>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <?php if (function_exists('can') && can('catalogo', 'crear')): ?>
                        <a href="?action=create" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Categoría
                        </a>
                        <?php endif; ?>
                        <?php else: ?>
                        <a href="?action=list" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Lista
                        </a>
                        <?php endif; ?>
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
                
                <!-- Contenido -->
                <?php if ($action === 'create' || $action === 'edit'): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil-square'; ?> me-2"></i>
                                <?php echo $action === 'create' ? 'Crear Nueva Categoría' : 'Editar Categoría'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="nombre" 
                                                   value="<?php echo $categoria ? esc($categoria['nombre']) : ''; ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Orden</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="orden" 
                                                   value="<?php echo $categoria ? $categoria['orden'] : 0; ?>" 
                                                   min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" 
                                              name="descripcion" 
                                              rows="3"><?php echo $categoria ? esc($categoria['descripcion']) : ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ícono (Bootstrap Icons)</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="icono" 
                                                   value="<?php echo $categoria ? esc($categoria['icono']) : ''; ?>" 
                                                   placeholder="bi-folder">
                                            <small class="form-text text-muted">Ejemplo: bi-folder, bi-person, bi-heart</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Color</label>
                                            <div class="input-group">
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       name="color" 
                                                       value="<?php echo $categoria ? esc($categoria['color']) : '#0066CC'; ?>">
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="<?php echo $categoria ? esc($categoria['color']) : '#0066CC'; ?>" 
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado" required>
                                        <option value="activo" <?php echo ($categoria && $categoria['estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo ($categoria && $categoria['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $action === 'create' ? 'Crear Categoría' : 'Actualizar Categoría'; ?>
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'delete' && $categoria): ?>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Categoría
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>¿Estás seguro de que deseas eliminar la categoría <strong><?php echo esc($categoria['nombre']); ?></strong>?</p>
                            
                            <?php
                            // Verificar productos asociados
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM catalogo_productos WHERE categoria_id = ?");
                            $stmt->execute([$id]);
                            $productos_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            
                            <?php if ($productos_count > 0): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Esta categoría tiene <strong><?php echo $productos_count; ?></strong> producto(s) asociado(s). 
                                No se puede eliminar hasta que se reasignen o eliminen esos productos.
                            </div>
                            <a href="?action=list" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                            <?php else: ?>
                            <form method="POST" action="?action=delete&id=<?php echo $id; ?>">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Sí, Eliminar
                                </button>
                                <a href="?action=list" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                </a>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Lista de categorías -->
                    <?php if (empty($categorias)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-folder text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay categorías registradas</h4>
                            <p class="text-muted">Comienza creando la primera categoría del catálogo</p>
                            <?php if (function_exists('can') && can('catalogo', 'crear')): ?>
                            <a href="?action=create" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Crear Primera Categoría
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($categorias as $cat): ?>
                        <div class="col-md-6 mb-3">
                            <div class="category-card">
                                <div class="d-flex align-items-start">
                                    <div class="category-icon me-3" style="background-color: <?php echo esc($cat['color']); ?>;">
                                        <?php if ($cat['icono']): ?>
                                        <i class="bi <?php echo esc($cat['icono']); ?>"></i>
                                        <?php else: ?>
                                        <i class="bi bi-folder"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1"><?php echo esc($cat['nombre']); ?></h5>
                                        <?php if ($cat['descripcion']): ?>
                                        <p class="text-muted mb-2 small"><?php echo esc($cat['descripcion']); ?></p>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center gap-3">
                                            <small class="text-muted">
                                                <i class="bi bi-box me-1"></i>
                                                <?php echo $cat['productos_count']; ?> producto(s)
                                            </small>
                                            <span class="badge <?php echo $cat['estado'] === 'activo' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($cat['estado']); ?>
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-sort-numeric-down me-1"></i>
                                                Orden: <?php echo $cat['orden']; ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="?action=edit&id=<?php echo $cat['id']; ?>">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="?action=delete&id=<?php echo $cat['id']; ?>">
                                                    <i class="bi bi-trash me-2"></i>Eliminar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

