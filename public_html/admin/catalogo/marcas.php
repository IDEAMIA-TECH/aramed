<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE MARCAS DEL CATÁLOGO
 * ========================================
 * 
 * CRUD completo para marcas de productos
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
            $website = trim($_POST['website'] ?? '');
            $estado = $_POST['estado'] ?? 'activo';
            $orden = (int)($_POST['orden'] ?? 0);
            
            if (empty($nombre)) {
                throw new Exception('El nombre es obligatorio');
            }
            
            // Generar slug
            $slug = generateSlug($nombre);
            
            // Verificar si el slug ya existe (excepto para el registro actual)
            $stmt = $pdo->prepare("SELECT id FROM catalogo_marcas WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            if ($stmt->fetch()) {
                $slug = $slug . '-' . time();
            }
            
            // Procesar logo si se subió
            $logo = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../../assets/images/marcas/';
                
                // Crear directorio si no existe
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file = $_FILES['logo'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 2 * 1024 * 1024; // 2MB
                
                // Validar tipo
                if (!in_array($file['type'], $allowed_types)) {
                    throw new Exception('Tipo de archivo no permitido. Solo se permiten imágenes (JPG, PNG, GIF, WebP)');
                }
                
                // Validar tamaño
                if ($file['size'] > $max_size) {
                    throw new Exception('El archivo es demasiado grande. Máximo 2MB');
                }
                
                // Generar nombre único
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $slug . '-' . time() . '.' . $extension;
                $filepath = $upload_dir . $filename;
                
                // Mover archivo
                if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                    throw new Exception('Error al subir el archivo');
                }
                
                // Si hay un logo anterior, eliminarlo
                if ($action === 'edit' && $id) {
                    $stmt = $pdo->prepare("SELECT logo FROM catalogo_marcas WHERE id = ?");
                    $stmt->execute([$id]);
                    $marca_actual = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($marca_actual && $marca_actual['logo']) {
                        $old_logo_path = __DIR__ . '/../../../' . $marca_actual['logo'];
                        if (file_exists($old_logo_path)) {
                            @unlink($old_logo_path);
                        }
                    }
                }
                
                $logo = 'assets/images/marcas/' . $filename;
            } elseif ($action === 'edit' && $id) {
                // Mantener el logo existente si no se subió uno nuevo
                $stmt = $pdo->prepare("SELECT logo FROM catalogo_marcas WHERE id = ?");
                $stmt->execute([$id]);
                $marca_actual = $stmt->fetch(PDO::FETCH_ASSOC);
                $logo = $marca_actual['logo'] ?? null;
            }
            
            if ($action === 'create') {
                $stmt = $pdo->prepare("
                    INSERT INTO catalogo_marcas (nombre, slug, descripcion, logo, website, estado, orden, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$nombre, $slug, $descripcion, $logo, $website, $estado, $orden]);
                
                $marca_id = $pdo->lastInsertId();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'crear', 'catalogo', $marca_id, 'marca', [
                        'nombre' => $nombre
                    ]);
                }
                
                $success_message = 'Marca creada exitosamente';
                $action = 'list';
                
            } else { // edit
                if ($logo) {
                    $stmt = $pdo->prepare("
                        UPDATE catalogo_marcas 
                        SET nombre = ?, slug = ?, descripcion = ?, logo = ?, website = ?, estado = ?, orden = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$nombre, $slug, $descripcion, $logo, $website, $estado, $orden, $id]);
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE catalogo_marcas 
                        SET nombre = ?, slug = ?, descripcion = ?, website = ?, estado = ?, orden = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$nombre, $slug, $descripcion, $website, $estado, $orden, $id]);
                }
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'catalogo', $id, 'marca', [
                        'nombre' => $nombre
                    ]);
                }
                
                $success_message = 'Marca actualizada exitosamente';
                $action = 'list';
            }
            
        } elseif ($action === 'delete' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('catalogo', 'eliminar');
            }
            
            // Verificar si tiene productos asociados
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM catalogo_productos WHERE marca_id = ?");
            $stmt->execute([$id]);
            $productos_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($productos_count > 0) {
                throw new Exception("No se puede eliminar la marca porque tiene {$productos_count} producto(s) asociado(s)");
            }
            
            // Obtener logo antes de eliminar
            $stmt = $pdo->prepare("SELECT nombre, logo FROM catalogo_marcas WHERE id = ?");
            $stmt->execute([$id]);
            $marca = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Eliminar logo si existe
            if ($marca && $marca['logo']) {
                $logo_path = __DIR__ . '/../../../' . $marca['logo'];
                if (file_exists($logo_path)) {
                    @unlink($logo_path);
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM catalogo_marcas WHERE id = ?");
            $stmt->execute([$id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'catalogo', $id, 'marca', [
                    'nombre' => $marca['nombre'] ?? ''
                ]);
            }
            
            $success_message = 'Marca eliminada exitosamente';
            $action = 'list';
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener datos para formularios
$marca = null;
if (($action === 'edit' || $action === 'delete') && $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM catalogo_marcas WHERE id = ?");
        $stmt->execute([$id]);
        $marca = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$marca) {
            $error_message = 'Marca no encontrada';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Obtener lista de marcas
$marcas = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT m.*, 
                   COUNT(p.id) as productos_count
            FROM catalogo_marcas m
            LEFT JOIN catalogo_productos p ON m.id = p.marca_id
            GROUP BY m.id
            ORDER BY m.orden ASC, m.nombre ASC
        ");
        $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'marcas.php';
$current_dir = 'catalogo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Marcas - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .brand-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .brand-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .brand-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 0.5rem;
            border: 1px solid #dee2e6;
        }
        
        .logo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            padding: 0.5rem;
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-tags me-2"></i>Gestión de Marcas
                            </h2>
                            <p class="mb-0 opacity-75">Administra las marcas del catálogo de productos</p>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <a href="?action=create" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Marca
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
                <?php if ($action === 'create' || $action === 'edit'): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil-square'; ?> me-2"></i>
                                <?php echo $action === 'create' ? 'Crear Nueva Marca' : 'Editar Marca'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="nombre" 
                                                   value="<?php echo $marca ? esc($marca['nombre']) : ''; ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Orden</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="orden" 
                                                   value="<?php echo $marca ? $marca['orden'] : 0; ?>" 
                                                   min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" 
                                              name="descripcion" 
                                              rows="3"><?php echo $marca ? esc($marca['descripcion']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" 
                                           class="form-control" 
                                           name="website" 
                                           value="<?php echo $marca ? esc($marca['website']) : ''; ?>" 
                                           placeholder="https://ejemplo.com">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Logo</label>
                                            <input type="file" 
                                                   class="form-control" 
                                                   name="logo" 
                                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                                   onchange="previewLogo(this)">
                                            <small class="form-text text-muted">
                                                Formatos permitidos: JPG, PNG, GIF, WebP. Máximo 2MB.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($marca && $marca['logo']): ?>
                                        <div class="mb-3">
                                            <label class="form-label">Logo Actual</label>
                                            <div>
                                                <img src="<?php echo SITE_URL . '/' . esc($marca['logo']); ?>" 
                                                     alt="Logo actual" 
                                                     class="logo-preview"
                                                     id="current-logo">
                                            </div>
                                            <small class="form-text text-muted">
                                                Sube un nuevo logo para reemplazarlo
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                        <div id="logo-preview-container" style="display: none;">
                                            <label class="form-label">Vista Previa</label>
                                            <div>
                                                <img id="logo-preview" src="" alt="Vista previa" class="logo-preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado" required>
                                        <option value="activo" <?php echo ($marca && $marca['estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo ($marca && $marca['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $action === 'create' ? 'Crear Marca' : 'Actualizar Marca'; ?>
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'delete' && $marca): ?>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Marca
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>¿Estás seguro de que deseas eliminar la marca <strong><?php echo esc($marca['nombre']); ?></strong>?</p>
                            
                            <?php
                            // Verificar productos asociados
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM catalogo_productos WHERE marca_id = ?");
                            $stmt->execute([$id]);
                            $productos_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            
                            <?php if ($productos_count > 0): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Esta marca tiene <strong><?php echo $productos_count; ?></strong> producto(s) asociado(s). 
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
                    <!-- Lista de marcas -->
                    <?php if (empty($marcas)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-tags text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay marcas registradas</h4>
                            <p class="text-muted">Comienza creando la primera marca del catálogo</p>
                            <a href="?action=create" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Crear Primera Marca
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($marcas as $m): ?>
                        <div class="col-md-6 mb-3">
                            <div class="brand-card">
                                <div class="d-flex align-items-start">
                                    <?php if ($m['logo']): ?>
                                    <img src="<?php echo SITE_URL . '/' . esc($m['logo']); ?>" 
                                         alt="<?php echo esc($m['nombre']); ?>" 
                                         class="brand-logo me-3"
                                         onerror="this.style.display='none';">
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1"><?php echo esc($m['nombre']); ?></h5>
                                        <?php if ($m['descripcion']): ?>
                                        <p class="text-muted mb-2 small"><?php echo esc($m['descripcion']); ?></p>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <small class="text-muted">
                                                <i class="bi bi-box me-1"></i>
                                                <?php echo $m['productos_count']; ?> producto(s)
                                            </small>
                                            <span class="badge <?php echo $m['estado'] === 'activo' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($m['estado']); ?>
                                            </span>
                                            <?php if ($m['website']): ?>
                                            <a href="<?php echo esc($m['website']); ?>" 
                                               target="_blank" 
                                               class="text-decoration-none">
                                                <i class="bi bi-globe me-1"></i>Website
                                            </a>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                <i class="bi bi-sort-numeric-down me-1"></i>
                                                Orden: <?php echo $m['orden']; ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="?action=edit&id=<?php echo $m['id']; ?>">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="?action=delete&id=<?php echo $m['id']; ?>">
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
    <script>
        function previewLogo(input) {
            const previewContainer = document.getElementById('logo-preview-container');
            const preview = document.getElementById('logo-preview');
            const currentLogo = document.getElementById('current-logo');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    if (currentLogo) {
                        currentLogo.style.display = 'none';
                    }
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
                if (currentLogo) {
                    currentLogo.style.display = 'block';
                }
            }
        }
    </script>
</body>
</html>

