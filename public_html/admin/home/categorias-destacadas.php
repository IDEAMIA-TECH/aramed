<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE CATEGORÍAS DESTACADAS
 * ========================================
 * 
 * CRUD para gestionar categorías destacadas en el home
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
    checkPermission('home', 'ver');
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
if ($action === 'add' || $action === 'create') {
    if (function_exists('checkPermission')) {
        checkPermission('home', 'crear');
    }
} elseif ($action === 'edit') {
    if (function_exists('checkPermission')) {
        checkPermission('home', 'editar');
    }
} elseif ($action === 'delete') {
    if (function_exists('checkPermission')) {
        checkPermission('home', 'eliminar');
    }
}

$success_message = '';
$error_message = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'crear');
            }
            
            $categoria_id = (int)($_POST['categoria_id'] ?? 0);
            $orden = (int)($_POST['orden'] ?? 0);
            
            if ($categoria_id <= 0) {
                throw new Exception('Debes seleccionar una categoría');
            }
            
            // Verificar si la categoría ya está destacada
            $stmt = $pdo->prepare("SELECT id FROM home_categorias_destacadas WHERE categoria_id = ?");
            $stmt->execute([$categoria_id]);
            if ($stmt->fetch()) {
                throw new Exception('Esta categoría ya está en la lista de destacadas');
            }
            
            // Insertar
            $stmt = $pdo->prepare("
                INSERT INTO home_categorias_destacadas 
                (categoria_id, orden, estado, created_at, updated_at)
                VALUES (?, ?, 'activo', NOW(), NOW())
            ");
            $stmt->execute([$categoria_id, $orden]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'crear', 'home', $pdo->lastInsertId(), 'categoria_destacada', [
                    'categoria_id' => $categoria_id
                ]);
            }
            
            $success_message = 'Categoría agregada a destacadas exitosamente';
            $action = 'list';
            
        } elseif ($action === 'update' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'editar');
            }
            
            $orden = (int)($_POST['orden'] ?? 0);
            $estado = $_POST['estado'] ?? 'activo';
            
            $stmt = $pdo->prepare("
                UPDATE home_categorias_destacadas 
                SET orden = ?, estado = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$orden, $estado, $id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'editar', 'home', $id, 'categoria_destacada', [
                    'orden' => $orden,
                    'estado' => $estado
                ]);
            }
            
            $success_message = 'Categoría destacada actualizada exitosamente';
            $action = 'list';
            
        } elseif ($action === 'delete' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'eliminar');
            }
            
            $stmt = $pdo->prepare("DELETE FROM home_categorias_destacadas WHERE id = ?");
            $stmt->execute([$id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'home', $id, 'categoria_destacada', []);
            }
            
            $success_message = 'Categoría eliminada de destacadas exitosamente';
            $action = 'list';
            
        } elseif ($action === 'update_order') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'editar');
            }
            
            header('Content-Type: application/json');
            
            $updates = json_decode($_POST['updates'] ?? '[]', true);
            
            if (empty($updates)) {
                echo json_encode(['success' => false, 'error' => 'No se recibieron actualizaciones']);
                exit;
            }
            
            try {
                $pdo->beginTransaction();
                
                foreach ($updates as $update) {
                    $stmt = $pdo->prepare("UPDATE home_categorias_destacadas SET orden = ? WHERE id = ?");
                    $stmt->execute([$update['orden'], $update['id']]);
                }
                
                $pdo->commit();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'home', null, 'categorias_destacadas', [
                        'accion' => 'reordenar',
                        'categorias_afectadas' => count($updates)
                    ]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Orden actualizado exitosamente']);
                exit;
                
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener categorías destacadas
$categorias_destacadas = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT hcd.*, 
                   c.nombre as categoria_nombre,
                   c.icono as categoria_icono,
                   c.color as categoria_color,
                   COUNT(p.id) as productos_count
            FROM home_categorias_destacadas hcd
            LEFT JOIN catalogo_categorias c ON hcd.categoria_id = c.id
            LEFT JOIN catalogo_productos p ON c.id = p.categoria_id AND p.estado = 'activo'
            GROUP BY hcd.id
            ORDER BY hcd.orden ASC, hcd.created_at DESC
        ");
        $categorias_destacadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener categoría para editar
$categoria_destacada = null;
if (($action === 'edit' || $action === 'delete') && $id) {
    try {
        $stmt = $pdo->prepare("
            SELECT hcd.*, 
                   c.nombre as categoria_nombre
            FROM home_categorias_destacadas hcd
            LEFT JOIN catalogo_categorias c ON hcd.categoria_id = c.id
            WHERE hcd.id = ?
        ");
        $stmt->execute([$id]);
        $categoria_destacada = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$categoria_destacada) {
            $error_message = 'Categoría destacada no encontrada';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Obtener categorías disponibles para agregar
$categorias_disponibles = [];
if ($action === 'add' || $action === 'list') {
    try {
        // Obtener IDs de categorías ya destacadas
        $stmt = $pdo->query("SELECT categoria_id FROM home_categorias_destacadas WHERE estado = 'activo'");
        $categorias_destacadas_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $where_clause = '';
        $params = [];
        
        if (!empty($categorias_destacadas_ids)) {
            $placeholders = implode(',', array_fill(0, count($categorias_destacadas_ids), '?'));
            $where_clause = "WHERE c.id NOT IN ($placeholders) AND c.estado = 'activo'";
            $params = $categorias_destacadas_ids;
        } else {
            $where_clause = "WHERE c.estado = 'activo'";
        }
        
        $sql = "
            SELECT c.id, c.nombre, c.icono, c.color
            FROM catalogo_categorias c
            {$where_clause}
            ORDER BY c.nombre ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $categorias_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Ignorar error si la tabla no existe
    }
}

$current_page = 'categorias-destacadas.php';
$current_dir = 'home';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías Destacadas - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --dark-color: #212529;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: #f8f9fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .category-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: move;
        }
        
        .category-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .category-card.sortable-ghost {
            opacity: 0.4;
        }
        
        .category-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
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
                                <i class="bi bi-folder me-2"></i>Categorías Destacadas
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona las categorías destacadas en el inicio</p>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <a href="?action=add" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Categoría
                        </a>
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
                <?php if ($action === 'add'): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-plus-circle me-2"></i>Agregar Categoría Destacada
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=add">
                                <div class="mb-3">
                                    <label class="form-label">Categoría *</label>
                                    <select class="form-select" name="categoria_id" required>
                                        <option value="">Selecciona una categoría</option>
                                        <?php foreach ($categorias_disponibles as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo esc($cat['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (empty($categorias_disponibles)): ?>
                                    <small class="form-text text-muted">No hay categorías disponibles para agregar</small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Orden</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="orden" 
                                           value="0" 
                                           min="0">
                                    <small class="form-text text-muted">El orden se puede ajustar después arrastrando las categorías</small>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" <?php echo empty($categorias_disponibles) ? 'disabled' : ''; ?>>
                                        <i class="bi bi-check-circle me-2"></i>Agregar Categoría
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'edit' && $categoria_destacada): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-pencil-square me-2"></i>Editar Categoría Destacada
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <strong>Categoría:</strong> <?php echo esc($categoria_destacada['categoria_nombre']); ?>
                            </div>
                            
                            <form method="POST" action="?action=update&id=<?php echo $id; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Orden</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="orden" 
                                           value="<?php echo $categoria_destacada['orden']; ?>" 
                                           min="0">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado" required>
                                        <option value="activo" <?php echo $categoria_destacada['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo $categoria_destacada['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Actualizar
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'delete' && $categoria_destacada): ?>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Categoría Destacada
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>¿Estás seguro de que deseas eliminar la categoría <strong><?php echo esc($categoria_destacada['categoria_nombre']); ?></strong> de las destacadas?</p>
                            
                            <form method="POST" action="?action=delete&id=<?php echo $id; ?>">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Sí, Eliminar
                                </button>
                                <a href="?action=list" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Lista de categorías destacadas -->
                    <?php if (empty($categorias_destacadas)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-folder text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay categorías destacadas</h4>
                            <p class="text-muted">Agrega categorías para mostrarlas en el inicio</p>
                            <a href="?action=add" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Agregar Primera Categoría
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Arrastra y suelta</strong> las categorías para reordenarlas. Los cambios se guardarán automáticamente.
                    </div>
                    
                    <div id="categorias-list">
                        <?php foreach ($categorias_destacadas as $cd): ?>
                        <div class="category-card" data-id="<?php echo $cd['id']; ?>">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-grip-vertical text-muted" style="font-size: 1.5rem; cursor: grab;"></i>
                                </div>
                                
                                <?php if ($cd['categoria_icono']): ?>
                                <div class="category-icon me-3" style="background-color: <?php echo esc($cd['categoria_color'] ?? '#0066CC'); ?>;">
                                    <i class="bi <?php echo esc($cd['categoria_icono']); ?>"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?php echo esc($cd['categoria_nombre']); ?></h5>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge <?php echo $cd['estado'] === 'activo' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($cd['estado']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <i class="bi bi-box me-1"></i>
                                            <?php echo $cd['productos_count']; ?> producto(s)
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-sort-numeric-down me-1"></i>
                                            Orden: <?php echo $cd['orden']; ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="?action=edit&id=<?php echo $cd['id']; ?>">
                                                <i class="bi bi-pencil me-2"></i>Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="?action=delete&id=<?php echo $cd['id']; ?>">
                                                <i class="bi bi-trash me-2"></i>Eliminar
                                            </a>
                                        </li>
                                    </ul>
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
    <script>
        // Sortable para reordenar categorías
        const categoriasList = document.getElementById('categorias-list');
        if (categoriasList) {
            const sortable = Sortable.create(categoriasList, {
                animation: 150,
                handle: '.bi-grip-vertical',
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Obtener nuevo orden
                    const items = categoriasList.querySelectorAll('.category-card');
                    const updates = [];
                    
                    items.forEach((item, index) => {
                        const id = item.dataset.id;
                        updates.push({
                            id: id,
                            orden: index + 1
                        });
                    });
                    
                    // Enviar actualización al servidor
                    fetch('categorias-destacadas.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=update_order&updates=' + encodeURIComponent(JSON.stringify(updates))
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mostrar mensaje de éxito temporal
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = '<i class="bi bi-check-circle me-2"></i>Orden actualizado exitosamente';
                            document.querySelector('.admin-content').insertBefore(alert, document.querySelector('.page-header').nextSibling);
                            
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>

