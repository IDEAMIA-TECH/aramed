<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE BANNERS/HERO
 * ========================================
 * 
 * CRUD completo para banners del inicio
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

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create' || $action === 'edit') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', $action === 'create' ? 'crear' : 'editar');
            }
            
            $titulo = trim($_POST['titulo'] ?? '');
            $subtitulo = trim($_POST['subtitulo'] ?? '');
            $video_url = trim($_POST['video_url'] ?? '');
            $cta_texto = trim($_POST['cta_texto'] ?? '');
            $cta_url = trim($_POST['cta_url'] ?? '');
            $estado = $_POST['estado'] ?? 'borrador';
            $orden = (int)($_POST['orden'] ?? 0);
            $fecha_inicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
            $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
            
            if (empty($titulo)) {
                throw new Exception('El título es obligatorio');
            }
            
            // Procesar imagen si se subió
            $imagen_url = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../../assets/images/home/banners/';
                
                // Crear directorio si no existe
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file = $_FILES['imagen'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                // Validar tipo
                if (!in_array($file['type'], $allowed_types)) {
                    throw new Exception('Tipo de archivo no permitido. Solo se permiten imágenes (JPG, PNG, GIF, WebP)');
                }
                
                // Validar tamaño
                if ($file['size'] > $max_size) {
                    throw new Exception('El archivo es demasiado grande. Máximo 5MB');
                }
                
                // Generar nombre único
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'banner-' . time() . '.' . $extension;
                $filepath = $upload_dir . $filename;
                
                // Mover archivo
                if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                    throw new Exception('Error al subir el archivo');
                }
                
                // Si hay una imagen anterior, eliminarla
                if ($action === 'edit' && $id) {
                    $stmt = $pdo->prepare("SELECT imagen_url FROM home_banners WHERE id = ?");
                    $stmt->execute([$id]);
                    $banner_actual = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($banner_actual && $banner_actual['imagen_url']) {
                        $old_image_path = __DIR__ . '/../../../' . $banner_actual['imagen_url'];
                        if (file_exists($old_image_path)) {
                            @unlink($old_image_path);
                        }
                    }
                }
                
                $imagen_url = 'assets/images/home/banners/' . $filename;
            } elseif ($action === 'edit' && $id) {
                // Mantener la imagen existente si no se subió una nueva
                $stmt = $pdo->prepare("SELECT imagen_url FROM home_banners WHERE id = ?");
                $stmt->execute([$id]);
                $banner_actual = $stmt->fetch(PDO::FETCH_ASSOC);
                $imagen_url = $banner_actual['imagen_url'] ?? null;
            }
            
            if ($action === 'create') {
                $stmt = $pdo->prepare("
                    INSERT INTO home_banners 
                    (titulo, subtitulo, imagen_url, video_url, cta_texto, cta_url, orden, estado, fecha_inicio, fecha_fin, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$titulo, $subtitulo, $imagen_url, $video_url, $cta_texto, $cta_url, $orden, $estado, $fecha_inicio, $fecha_fin]);
                
                $banner_id = $pdo->lastInsertId();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'crear', 'home', $banner_id, 'banner', [
                        'titulo' => $titulo
                    ]);
                }
                
                $success_message = 'Banner creado exitosamente';
                $action = 'list';
                
            } else { // edit
                if ($imagen_url) {
                    $stmt = $pdo->prepare("
                        UPDATE home_banners 
                        SET titulo = ?, subtitulo = ?, imagen_url = ?, video_url = ?, cta_texto = ?, cta_url = ?, orden = ?, estado = ?, fecha_inicio = ?, fecha_fin = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$titulo, $subtitulo, $imagen_url, $video_url, $cta_texto, $cta_url, $orden, $estado, $fecha_inicio, $fecha_fin, $id]);
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE home_banners 
                        SET titulo = ?, subtitulo = ?, video_url = ?, cta_texto = ?, cta_url = ?, orden = ?, estado = ?, fecha_inicio = ?, fecha_fin = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$titulo, $subtitulo, $video_url, $cta_texto, $cta_url, $orden, $estado, $fecha_inicio, $fecha_fin, $id]);
                }
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'home', $id, 'banner', [
                        'titulo' => $titulo
                    ]);
                }
                
                $success_message = 'Banner actualizado exitosamente';
                $action = 'list';
            }
            
        } elseif ($action === 'delete' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'eliminar');
            }
            
            // Obtener imagen antes de eliminar
            $stmt = $pdo->prepare("SELECT titulo, imagen_url FROM home_banners WHERE id = ?");
            $stmt->execute([$id]);
            $banner = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Eliminar imagen si existe
            if ($banner && $banner['imagen_url']) {
                $image_path = __DIR__ . '/../../../' . $banner['imagen_url'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM home_banners WHERE id = ?");
            $stmt->execute([$id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'home', $id, 'banner', [
                    'titulo' => $banner['titulo'] ?? ''
                ]);
            }
            
            $success_message = 'Banner eliminado exitosamente';
            $action = 'list';
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener datos para formularios
$banner = null;
if (($action === 'edit' || $action === 'delete') && $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM home_banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$banner) {
            $error_message = 'Banner no encontrado';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Obtener lista de banners
$banners = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT * FROM home_banners 
            ORDER BY orden ASC, created_at DESC
        ");
        $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'banners.php';
$current_dir = 'home';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Banners - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .banner-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .banner-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .banner-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .banner-image-preview {
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
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
                                <i class="bi bi-image me-2"></i>Gestión de Banners
                            </h2>
                            <p class="mb-0 opacity-75">Administra los banners del inicio (carrusel hero)</p>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <a href="?action=create" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Banner
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
                                <?php echo $action === 'create' ? 'Crear Nuevo Banner' : 'Editar Banner'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Título *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="titulo" 
                                                   value="<?php echo $banner ? esc($banner['titulo']) : ''; ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Orden</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="orden" 
                                                   value="<?php echo $banner ? $banner['orden'] : 0; ?>" 
                                                   min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Subtítulo</label>
                                    <textarea class="form-control" 
                                              name="subtitulo" 
                                              rows="2"><?php echo $banner ? esc($banner['subtitulo']) : ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">CTA Texto</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="cta_texto" 
                                                   value="<?php echo $banner ? esc($banner['cta_texto']) : ''; ?>" 
                                                   placeholder="Ej: Ver más">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">CTA URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   name="cta_url" 
                                                   value="<?php echo $banner ? esc($banner['cta_url']) : ''; ?>" 
                                                   placeholder="https://ejemplo.com">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Imagen</label>
                                            <input type="file" 
                                                   class="form-control" 
                                                   name="imagen" 
                                                   accept="image/*"
                                                   onchange="previewImage(this)">
                                            <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WebP. Máximo 5MB.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($banner && $banner['imagen_url']): ?>
                                        <div class="mb-3">
                                            <label class="form-label">Imagen Actual</label>
                                            <div>
                                                <img src="<?php echo SITE_URL . '/' . esc($banner['imagen_url']); ?>" 
                                                     alt="Banner actual" 
                                                     class="banner-image-preview"
                                                     id="current-image">
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div id="image-preview-container" style="display: none;">
                                            <label class="form-label">Vista Previa</label>
                                            <div>
                                                <img id="image-preview" src="" alt="Vista previa" class="banner-image-preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Video URL (Opcional)</label>
                                    <input type="url" 
                                           class="form-control" 
                                           name="video_url" 
                                           value="<?php echo $banner ? esc($banner['video_url']) : ''; ?>" 
                                           placeholder="URL de YouTube o Vimeo">
                                    <small class="form-text text-muted">Si se proporciona un video, este tendrá prioridad sobre la imagen.</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" name="estado" required>
                                                <option value="borrador" <?php echo ($banner && $banner['estado'] === 'borrador') ? 'selected' : ''; ?>>Borrador</option>
                                                <option value="publicado" <?php echo ($banner && $banner['estado'] === 'publicado') ? 'selected' : ''; ?>>Publicado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha Inicio (Opcional)</label>
                                            <input type="datetime-local" 
                                                   class="form-control" 
                                                   name="fecha_inicio" 
                                                   value="<?php echo $banner && $banner['fecha_inicio'] ? date('Y-m-d\TH:i', strtotime($banner['fecha_inicio'])) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha Fin (Opcional)</label>
                                            <input type="datetime-local" 
                                                   class="form-control" 
                                                   name="fecha_fin" 
                                                   value="<?php echo $banner && $banner['fecha_fin'] ? date('Y-m-d\TH:i', strtotime($banner['fecha_fin'])) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $action === 'create' ? 'Crear Banner' : 'Actualizar Banner'; ?>
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'delete' && $banner): ?>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Banner
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>¿Estás seguro de que deseas eliminar el banner <strong><?php echo esc($banner['titulo']); ?></strong>?</p>
                            
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
                    <!-- Lista de banners -->
                    <?php if (empty($banners)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay banners registrados</h4>
                            <p class="text-muted">Comienza creando el primer banner del inicio</p>
                            <a href="?action=create" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Crear Primer Banner
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($banners as $b): ?>
                        <div class="col-md-6 mb-3">
                            <div class="banner-card">
                                <div class="d-flex align-items-start">
                                    <?php if ($b['imagen_url']): ?>
                                    <img src="<?php echo SITE_URL . '/' . esc($b['imagen_url']); ?>" 
                                         alt="<?php echo esc($b['titulo']); ?>" 
                                         class="banner-preview me-3"
                                         style="width: 150px; height: 100px; object-fit: cover;"
                                         onerror="this.style.display='none';">
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2"><?php echo esc($b['titulo']); ?></h5>
                                        <?php if ($b['subtitulo']): ?>
                                        <p class="text-muted mb-2 small"><?php echo esc($b['subtitulo']); ?></p>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                            <span class="badge <?php echo $b['estado'] === 'publicado' ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo ucfirst($b['estado']); ?>
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-sort-numeric-down me-1"></i>
                                                Orden: <?php echo $b['orden']; ?>
                                            </small>
                                            <?php if ($b['cta_texto']): ?>
                                            <small class="text-info">
                                                <i class="bi bi-link-45deg me-1"></i>
                                                CTA: <?php echo esc($b['cta_texto']); ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($b['fecha_inicio'] || $b['fecha_fin']): ?>
                                        <small class="text-muted d-block">
                                            <?php if ($b['fecha_inicio']): ?>
                                            Desde: <?php echo date('d/m/Y H:i', strtotime($b['fecha_inicio'])); ?>
                                            <?php endif; ?>
                                            <?php if ($b['fecha_fin']): ?>
                                            | Hasta: <?php echo date('d/m/Y H:i', strtotime($b['fecha_fin'])); ?>
                                            <?php endif; ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="?action=edit&id=<?php echo $b['id']; ?>">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="?action=delete&id=<?php echo $b['id']; ?>">
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
        function previewImage(input) {
            const previewContainer = document.getElementById('image-preview-container');
            const preview = document.getElementById('image-preview');
            const currentImage = document.getElementById('current-image');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    if (currentImage) {
                        currentImage.style.display = 'none';
                    }
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
                if (currentImage) {
                    currentImage.style.display = 'block';
                }
            }
        }
    </script>
</body>
</html>

