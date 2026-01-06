<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE PRODUCTOS DESTACADOS
 * ========================================
 * 
 * CRUD para gestionar productos destacados en el home
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

$success_message = '';
$error_message = '';

// Procesar acciones GET (toggle_status)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'toggle_status' && $id) {
    try {
        // Verificar permisos
        if (function_exists('checkPermission')) {
            checkPermission('home', 'editar');
        }
        
        // Obtener el producto actual
        $stmt = $pdo->prepare("SELECT estado FROM home_productos_destacados WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            throw new Exception('Producto destacado no encontrado');
        }
        
        // Cambiar estado
        $new_status = $current['estado'] === 'activo' ? 'inactivo' : 'activo';
        
        $stmt = $pdo->prepare("UPDATE home_productos_destacados SET estado = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'home', $id, 'producto_destacado', [
                'accion' => 'cambiar_estado',
                'estado_anterior' => $current['estado'],
                'estado_nuevo' => $new_status
            ]);
        }
        
        $success_message = 'Estado del producto actualizado exitosamente';
        $action = 'list';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'crear');
            }
            
            $titulo = trim($_POST['titulo'] ?? '');
            $orden = (int)($_POST['orden'] ?? 0);
            
            if (empty($titulo)) {
                throw new Exception('El título es obligatorio');
            }
            
            // Insertar con todos los campos (producto destacado independiente)
            $producto_id = null; // No relacionado con catálogo
            $marca_nombre = trim($_POST['marca_nombre'] ?? '');
            $marca_logo_url = trim($_POST['marca_logo_url'] ?? '');
            $categoria_nombre = trim($_POST['categoria_nombre'] ?? '');
            $badge_texto = trim($_POST['badge_texto'] ?? '');
            $subtitulo = trim($_POST['subtitulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $caracteristicas = trim($_POST['caracteristicas'] ?? '');
            $imagen_url = trim($_POST['imagen_url'] ?? '');
            $cta_texto = trim($_POST['cta_texto'] ?? '');
            $cta_url = trim($_POST['cta_url'] ?? '');
            
            $stmt = $pdo->prepare("
                INSERT INTO home_productos_destacados 
                (producto_id, titulo, marca_nombre, marca_logo_url, categoria_nombre, badge_texto, subtitulo, descripcion, caracteristicas, imagen_url, cta_texto, cta_url, modo, orden, estado, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'manual', ?, 'activo', NOW(), NOW())
            ");
            $stmt->execute([
                $producto_id, 
                $titulo,
                $marca_nombre ?: null,
                $marca_logo_url ?: null,
                $categoria_nombre ?: null,
                $badge_texto ?: null, 
                $subtitulo ?: null, 
                $descripcion ?: null, 
                $caracteristicas ?: null, 
                $imagen_url ?: null, 
                $cta_texto ?: null, 
                $cta_url ?: null, 
                $orden
            ]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'crear', 'home', $pdo->lastInsertId(), 'producto_destacado', [
                    'producto_id' => $producto_id
                ]);
            }
            
            $success_message = 'Producto agregado a destacados exitosamente';
            $action = 'list';
            
        } elseif ($action === 'update' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'editar');
            }
            
            $titulo = trim($_POST['titulo'] ?? '');
            $orden = (int)($_POST['orden'] ?? 0);
            $estado = $_POST['estado'] ?? 'activo';
            $marca_nombre = trim($_POST['marca_nombre'] ?? '');
            $marca_logo_url = trim($_POST['marca_logo_url'] ?? '');
            $categoria_nombre = trim($_POST['categoria_nombre'] ?? '');
            $badge_texto = trim($_POST['badge_texto'] ?? '');
            $subtitulo = trim($_POST['subtitulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $caracteristicas = trim($_POST['caracteristicas'] ?? '');
            $imagen_url = trim($_POST['imagen_url'] ?? '');
            $cta_texto = trim($_POST['cta_texto'] ?? '');
            $cta_url = trim($_POST['cta_url'] ?? '');
            
            if (empty($titulo)) {
                throw new Exception('El título es obligatorio');
            }
            
            $stmt = $pdo->prepare("
                UPDATE home_productos_destacados 
                SET titulo = ?, orden = ?, estado = ?, 
                    marca_nombre = ?, marca_logo_url = ?, categoria_nombre = ?,
                    badge_texto = ?, subtitulo = ?, descripcion = ?, 
                    caracteristicas = ?, imagen_url = ?, cta_texto = ?, cta_url = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $titulo,
                $orden, $estado, 
                $marca_nombre ?: null, $marca_logo_url ?: null, $categoria_nombre ?: null,
                $badge_texto ?: null, $subtitulo ?: null, $descripcion ?: null, 
                $caracteristicas ?: null, $imagen_url ?: null, 
                $cta_texto ?: null, $cta_url ?: null, 
                $id
            ]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'editar', 'home', $id, 'producto_destacado', [
                    'orden' => $orden,
                    'estado' => $estado
                ]);
            }
            
            $success_message = 'Producto destacado actualizado exitosamente';
            $action = 'list';
            
        } elseif ($action === 'delete' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'eliminar');
            }
            
            $stmt = $pdo->prepare("DELETE FROM home_productos_destacados WHERE id = ?");
            $stmt->execute([$id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'home', $id, 'producto_destacado', []);
            }
            
            $success_message = 'Producto eliminado de destacados exitosamente';
            $action = 'list';
            
        } elseif ($action === 'toggle_status' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'editar');
            }
            
            // Obtener el producto actual
            $stmt = $pdo->prepare("SELECT estado FROM home_productos_destacados WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$current) {
                throw new Exception('Producto destacado no encontrado');
            }
            
            // Cambiar estado
            $new_status = $current['estado'] === 'activo' ? 'inactivo' : 'activo';
            
            $stmt = $pdo->prepare("UPDATE home_productos_destacados SET estado = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'editar', 'home', $id, 'producto_destacado', [
                    'accion' => 'cambiar_estado',
                    'estado_anterior' => $current['estado'],
                    'estado_nuevo' => $new_status
                ]);
            }
            
            $success_message = 'Estado del producto actualizado exitosamente';
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
                    $stmt = $pdo->prepare("UPDATE home_productos_destacados SET orden = ? WHERE id = ?");
                    $stmt->execute([$update['orden'], $update['id']]);
                }
                
                $pdo->commit();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'home', null, 'productos_destacados', [
                        'accion' => 'reordenar',
                        'productos_afectados' => count($updates)
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

// Obtener productos destacados
$productos_destacados = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT hpd.*
            FROM home_productos_destacados hpd
            WHERE hpd.modo = 'manual'
            ORDER BY hpd.orden ASC, hpd.created_at DESC
        ");
        $productos_destacados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener producto para editar, eliminar o cambiar estado
$producto_destacado = null;
if (($action === 'edit' || $action === 'delete' || $action === 'toggle_status') && $id) {
    try {
        $stmt = $pdo->prepare("
            SELECT hpd.*
            FROM home_productos_destacados hpd
            WHERE hpd.id = ?
        ");
        $stmt->execute([$id]);
        $producto_destacado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto_destacado) {
            $error_message = 'Producto destacado no encontrado';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Los productos destacados son independientes del catálogo
// No necesitamos obtener productos del catálogo

$current_page = 'productos-destacados.php';
$current_dir = 'home';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Destacados - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .product-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: move;
        }
        
        .product-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .product-card.sortable-ghost {
            opacity: 0.4;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background: #f8f9fa;
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
                                <i class="bi bi-star me-2"></i>Productos Destacados
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona los productos destacados en el inicio</p>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <a href="?action=add" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Producto
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
                                <i class="bi bi-plus-circle me-2"></i>Agregar Producto Destacado
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=add">
                                <div class="mb-3">
                                    <label class="form-label">Título *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="titulo" 
                                           placeholder="Ej: ANATOMAGE TABLE"
                                           required>
                                    <small class="form-text text-muted">Título principal del producto destacado</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Orden</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="orden" 
                                               value="0" 
                                               min="0">
                                        <small class="form-text text-muted">El orden se puede ajustar después arrastrando los productos</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Badge/Etiqueta</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="badge_texto" 
                                               placeholder="Ej: Más Vendido, Inmersivo">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marca/Proveedor</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="marca_nombre" 
                                               placeholder="Ej: Anatomage">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Logo del Proveedor (URL)</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="marca_logo_url" 
                                               placeholder="aliados/3-Anatomage.webp">
                                        <small class="form-text text-muted">Ruta relativa a assets/images/</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="categoria_nombre" 
                                           placeholder="Ej: Plataforma Educativa, Realidad Inmersiva">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Subtítulo</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="subtitulo" 
                                           placeholder="Ej: Revoluciona la enseñanza médica">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" 
                                              name="descripcion" 
                                              rows="4" 
                                              placeholder="Descripción personalizada para el home"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Características (Bullets)</label>
                                    <textarea class="form-control" 
                                              name="caracteristicas" 
                                              rows="6" 
                                              placeholder="Una característica por línea&#10;Ej:&#10;Visualización 3D de cuerpos humanos reales&#10;Herramientas interactivas de disección virtual"></textarea>
                                    <small class="form-text text-muted">Lista de características, una por línea</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Imagen URL</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="imagen_url" 
                                           placeholder="productos/anatomage-table.jpg">
                                    <small class="form-text text-muted">Ruta relativa a assets/images/</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Texto del Botón CTA</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="cta_texto" 
                                               placeholder="Ej: Solicitar Cotización">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">URL del Botón CTA</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="cta_url" 
                                               placeholder="Ej: #newsletter">
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Agregar Producto
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'edit' && $producto_destacado): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-pencil-square me-2"></i>Editar Producto Destacado
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=update&id=<?php echo $id; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Título *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="titulo" 
                                           value="<?php echo esc($producto_destacado['titulo'] ?? ''); ?>" 
                                           placeholder="Ej: ANATOMAGE TABLE"
                                           required>
                                    <small class="form-text text-muted">Título principal del producto destacado</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Orden</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="orden" 
                                               value="<?php echo $producto_destacado['orden']; ?>" 
                                               min="0">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" name="estado" required>
                                            <option value="activo" <?php echo $producto_destacado['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                            <option value="inactivo" <?php echo $producto_destacado['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marca/Proveedor</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="marca_nombre" 
                                               value="<?php echo esc($producto_destacado['marca_nombre'] ?? ''); ?>" 
                                               placeholder="Ej: Anatomage">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Logo del Proveedor (URL)</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="marca_logo_url" 
                                               value="<?php echo esc($producto_destacado['marca_logo_url'] ?? ''); ?>" 
                                               placeholder="aliados/3-Anatomage.webp">
                                        <small class="form-text text-muted">Ruta relativa a assets/images/</small>
                                        <?php if ($producto_destacado['marca_logo_url']): ?>
                                        <div class="mt-2">
                                            <img src="<?php echo imageUrl($producto_destacado['marca_logo_url']); ?>" 
                                                 alt="Logo preview" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 100px; max-height: 50px; object-fit: contain;">
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="categoria_nombre" 
                                           value="<?php echo esc($producto_destacado['categoria_nombre'] ?? ''); ?>" 
                                           placeholder="Ej: Plataforma Educativa, Realidad Inmersiva">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Badge/Etiqueta</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="badge_texto" 
                                           value="<?php echo esc($producto_destacado['badge_texto'] ?? ''); ?>" 
                                           placeholder="Ej: Más Vendido, Inmersivo, Pediátrico, Adulto">
                                    <small class="form-text text-muted">Texto que aparece en el badge del producto</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Subtítulo</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="subtitulo" 
                                           value="<?php echo esc($producto_destacado['subtitulo'] ?? ''); ?>" 
                                           placeholder="Ej: Revoluciona la enseñanza médica con Anatomage Table">
                                    <small class="form-text text-muted">Subtítulo que aparece debajo del título del producto</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" 
                                              name="descripcion" 
                                              rows="4" 
                                              placeholder="Descripción personalizada para el home"><?php echo esc($producto_destacado['descripcion'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Descripción que se mostrará en el home</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Características (Bullets)</label>
                                    <textarea class="form-control" 
                                              name="caracteristicas" 
                                              rows="6" 
                                              placeholder="Una característica por línea&#10;Ej:&#10;Visualización 3D de cuerpos humanos reales&#10;Herramientas interactivas de disección virtual"><?php echo esc($producto_destacado['caracteristicas'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Lista de características, una por línea. Se mostrarán como bullets con checkmarks.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Imagen URL</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="imagen_url" 
                                           value="<?php echo esc($producto_destacado['imagen_url'] ?? ''); ?>" 
                                           placeholder="productos/anatomage-table.jpg">
                                    <small class="form-text text-muted">Ruta relativa a assets/images/</small>
                                    <?php if ($producto_destacado['imagen_url']): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo imageUrl($producto_destacado['imagen_url']); ?>" 
                                             alt="Preview" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px; max-height: 150px; object-fit: cover;">
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Texto del Botón CTA</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="cta_texto" 
                                               value="<?php echo esc($producto_destacado['cta_texto'] ?? ''); ?>" 
                                               placeholder="Ej: Solicitar Cotización">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">URL del Botón CTA</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="cta_url" 
                                               value="<?php echo esc($producto_destacado['cta_url'] ?? ''); ?>" 
                                               placeholder="Ej: #newsletter o /catalogo/producto-slug">
                                    </div>
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
                    
                <?php elseif ($action === 'delete' && $producto_destacado): ?>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Producto Destacado
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>¿Estás seguro de que deseas eliminar el producto destacado <strong><?php echo esc($producto_destacado['titulo'] ?? $producto_destacado['producto_nombre'] ?? 'sin título'); ?></strong>?</p>
                            
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
                    <!-- Lista de productos destacados -->
                    <?php if (empty($productos_destacados)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-star text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay productos destacados</h4>
                            <p class="text-muted">Agrega productos para mostrarlos en el inicio</p>
                            <a href="?action=add" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Agregar Primer Producto
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Arrastra y suelta</strong> los productos para reordenarlos. Los cambios se guardarán automáticamente.
                    </div>
                    
                    <div id="productos-list">
                        <?php foreach ($productos_destacados as $pd): ?>
                        <div class="product-card" data-id="<?php echo $pd['id']; ?>">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-grip-vertical text-muted" style="font-size: 1.5rem; cursor: grab;"></i>
                                </div>
                                
                                <?php if ($pd['imagen_url']): ?>
                                <img src="<?php echo imageUrl($pd['imagen_url']); ?>" 
                                     alt="<?php echo esc($pd['titulo'] ?? 'Producto'); ?>" 
                                     class="product-image me-3"
                                     style="width: 80px; height: 80px; object-fit: cover;"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23f8f9fa\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ESin imagen%3C/text%3E%3C/svg%3E'">
                                <?php else: ?>
                                <div class="product-image me-3 d-flex align-items-center justify-content-center text-muted" style="width: 80px; height: 80px;">
                                    <i class="bi bi-image" style="font-size: 2rem;"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?php echo esc($pd['titulo'] ?? 'Sin título'); ?></h5>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <?php if ($pd['categoria_nombre']): ?>
                                        <span class="badge bg-secondary"><?php echo esc($pd['categoria_nombre']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($pd['marca_nombre']): ?>
                                        <span class="badge bg-info"><?php echo esc($pd['marca_nombre']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($pd['badge_texto']): ?>
                                        <span class="badge bg-primary"><?php echo esc($pd['badge_texto']); ?></span>
                                        <?php endif; ?>
                                        <span class="badge <?php echo $pd['estado'] === 'activo' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($pd['estado']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <i class="bi bi-sort-numeric-down me-1"></i>
                                            Orden: <?php echo $pd['orden']; ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="?action=edit&id=<?php echo $pd['id']; ?>">
                                                <i class="bi bi-pencil me-2"></i>Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item <?php echo $pd['estado'] === 'activo' ? 'text-warning' : 'text-success'; ?>" 
                                               href="?action=toggle_status&id=<?php echo $pd['id']; ?>"
                                               onclick="return confirm('¿Estás seguro de <?php echo $pd['estado'] === 'activo' ? 'inactivar' : 'activar'; ?> este producto destacado?');">
                                                <i class="bi <?php echo $pd['estado'] === 'activo' ? 'bi-eye-slash' : 'bi-eye'; ?> me-2"></i>
                                                <?php echo $pd['estado'] === 'activo' ? 'Inactivar' : 'Activar'; ?>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="?action=delete&id=<?php echo $pd['id']; ?>">
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
        // Sortable para reordenar productos
        const productosList = document.getElementById('productos-list');
        if (productosList) {
            const sortable = Sortable.create(productosList, {
                animation: 150,
                handle: '.bi-grip-vertical',
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Obtener nuevo orden
                    const items = productosList.querySelectorAll('.product-card');
                    const updates = [];
                    
                    items.forEach((item, index) => {
                        const id = item.dataset.id;
                        updates.push({
                            id: id,
                            orden: index + 1
                        });
                    });
                    
                    // Enviar actualización al servidor
                    fetch('productos-destacados.php', {
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

