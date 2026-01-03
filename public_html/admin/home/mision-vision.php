<?php
/**
 * ========================================
 * ADMIN - EDITOR DE MISIÓN Y VISIÓN
 * ========================================
 * 
 * Editor para gestionar el contenido de misión y visión
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
    checkPermission('home', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$success_message = '';
$error_message = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $mision_titulo = trim($_POST['mision_titulo'] ?? '');
        $mision_contenido = $_POST['mision_contenido'] ?? '';
        $mision_imagen_url = null;
        
        $vision_titulo = trim($_POST['vision_titulo'] ?? '');
        $vision_contenido = $_POST['vision_contenido'] ?? '';
        $vision_imagen_url = null;
        
        // Procesar imagen de misión
        if (isset($_FILES['mision_imagen']) && $_FILES['mision_imagen']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../../assets/images/home/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file = $_FILES['mision_imagen'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Tipo de archivo no permitido para imagen de misión');
            }
            
            if ($file['size'] > $max_size) {
                throw new Exception('La imagen de misión es demasiado grande. Máximo 5MB');
            }
            
            // Obtener imagen anterior
            $stmt = $pdo->prepare("SELECT imagen_url FROM home_mision_vision WHERE tipo = 'mision'");
            $stmt->execute();
            $mision_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'mision-' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Error al subir la imagen de misión');
            }
            
            // Eliminar imagen anterior
            if ($mision_actual && $mision_actual['imagen_url']) {
                $old_path = __DIR__ . '/../../../' . $mision_actual['imagen_url'];
                if (file_exists($old_path)) {
                    @unlink($old_path);
                }
            }
            
            $mision_imagen_url = 'assets/images/home/' . $filename;
        } else {
            // Mantener imagen existente
            $stmt = $pdo->prepare("SELECT imagen_url FROM home_mision_vision WHERE tipo = 'mision'");
            $stmt->execute();
            $mision_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            $mision_imagen_url = $mision_actual['imagen_url'] ?? null;
        }
        
        // Procesar imagen de visión
        if (isset($_FILES['vision_imagen']) && $_FILES['vision_imagen']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../../assets/images/home/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file = $_FILES['vision_imagen'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Tipo de archivo no permitido para imagen de visión');
            }
            
            if ($file['size'] > $max_size) {
                throw new Exception('La imagen de visión es demasiado grande. Máximo 5MB');
            }
            
            // Obtener imagen anterior
            $stmt = $pdo->prepare("SELECT imagen_url FROM home_mision_vision WHERE tipo = 'vision'");
            $stmt->execute();
            $vision_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'vision-' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Error al subir la imagen de visión');
            }
            
            // Eliminar imagen anterior
            if ($vision_actual && $vision_actual['imagen_url']) {
                $old_path = __DIR__ . '/../../../' . $vision_actual['imagen_url'];
                if (file_exists($old_path)) {
                    @unlink($old_path);
                }
            }
            
            $vision_imagen_url = 'assets/images/home/' . $filename;
        } else {
            // Mantener imagen existente
            $stmt = $pdo->prepare("SELECT imagen_url FROM home_mision_vision WHERE tipo = 'vision'");
            $stmt->execute();
            $vision_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            $vision_imagen_url = $vision_actual['imagen_url'] ?? null;
        }
        
        // Actualizar o insertar misión
        $stmt = $pdo->prepare("
            INSERT INTO home_mision_vision (tipo, titulo, contenido, imagen_url, estado, updated_at)
            VALUES ('mision', ?, ?, ?, 'activo', NOW())
            ON DUPLICATE KEY UPDATE
                titulo = VALUES(titulo),
                contenido = VALUES(contenido),
                imagen_url = VALUES(imagen_url),
                updated_at = NOW()
        ");
        $stmt->execute([$mision_titulo, $mision_contenido, $mision_imagen_url]);
        
        // Actualizar o insertar visión
        $stmt = $pdo->prepare("
            INSERT INTO home_mision_vision (tipo, titulo, contenido, imagen_url, estado, updated_at)
            VALUES ('vision', ?, ?, ?, 'activo', NOW())
            ON DUPLICATE KEY UPDATE
                titulo = VALUES(titulo),
                contenido = VALUES(contenido),
                imagen_url = VALUES(imagen_url),
                updated_at = NOW()
        ");
        $stmt->execute([$vision_titulo, $vision_contenido, $vision_imagen_url]);
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'home', null, 'mision_vision', []);
        }
        
        $success_message = 'Misión y Visión actualizadas exitosamente';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Cargar misión y visión
$mision = null;
$vision = null;

try {
    $stmt = $pdo->query("SELECT * FROM home_mision_vision WHERE tipo = 'mision'");
    $mision = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT * FROM home_mision_vision WHERE tipo = 'vision'");
    $vision = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si las tablas no existen, crear registros vacíos
    $mision = ['titulo' => '', 'contenido' => '', 'imagen_url' => null];
    $vision = ['titulo' => '', 'contenido' => '', 'imagen_url' => null];
}

$current_page = 'mision-vision.php';
$current_dir = 'home';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misión y Visión - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
        
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .section-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .image-preview {
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
                                <i class="bi bi-bullseye me-2"></i>Misión y Visión
                            </h2>
                            <p class="mb-0 opacity-75">Edita el contenido de misión y visión de la empresa</p>
                        </div>
                        <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-light">
                            <i class="bi bi-eye me-2"></i>Ver Inicio
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
                
                <!-- Formulario -->
                <form method="POST" action="" enctype="multipart/form-data">
                    
                    <!-- Misión -->
                    <div class="section-card">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-bullseye me-2"></i>Misión
                            </h4>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Título (Opcional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="mision_titulo" 
                                   value="<?php echo $mision ? esc($mision['titulo'] ?? '') : ''; ?>" 
                                   placeholder="Ej: Nuestra Misión">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Contenido *</label>
                            <textarea class="form-control" 
                                      name="mision_contenido" 
                                      id="mision_contenido"
                                      rows="10" required><?php echo $mision ? ($mision['contenido'] ?? '') : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Imagen (Opcional)</label>
                                    <input type="file" 
                                           class="form-control" 
                                           name="mision_imagen" 
                                           accept="image/*"
                                           onchange="previewImage(this, 'mision-preview')">
                                    <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WebP. Máximo 5MB.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if ($mision && $mision['imagen_url']): ?>
                                <div class="mb-3">
                                    <label class="form-label">Imagen Actual</label>
                                    <div>
                                        <img src="<?php echo SITE_URL . '/' . esc($mision['imagen_url']); ?>" 
                                             alt="Misión" 
                                             class="image-preview"
                                             id="mision-preview-current">
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div id="mision-preview-container" style="display: none;">
                                    <label class="form-label">Vista Previa</label>
                                    <div>
                                        <img id="mision-preview" src="" alt="Vista previa" class="image-preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Visión -->
                    <div class="section-card">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-eye me-2"></i>Visión
                            </h4>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Título (Opcional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="vision_titulo" 
                                   value="<?php echo $vision ? esc($vision['titulo'] ?? '') : ''; ?>" 
                                   placeholder="Ej: Nuestra Visión">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Contenido *</label>
                            <textarea class="form-control" 
                                      name="vision_contenido" 
                                      id="vision_contenido"
                                      rows="10" required><?php echo $vision ? ($vision['contenido'] ?? '') : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Imagen (Opcional)</label>
                                    <input type="file" 
                                           class="form-control" 
                                           name="vision_imagen" 
                                           accept="image/*"
                                           onchange="previewImage(this, 'vision-preview')">
                                    <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WebP. Máximo 5MB.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if ($vision && $vision['imagen_url']): ?>
                                <div class="mb-3">
                                    <label class="form-label">Imagen Actual</label>
                                    <div>
                                        <img src="<?php echo SITE_URL . '/' . esc($vision['imagen_url']); ?>" 
                                             alt="Visión" 
                                             class="image-preview"
                                             id="vision-preview-current">
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div id="vision-preview-container" style="display: none;">
                                    <label class="form-label">Vista Previa</label>
                                    <div>
                                        <img id="vision-preview" src="" alt="Vista previa" class="image-preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                        </button>
                        <a href="index.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TinyMCE para Misión
        tinymce.init({
            selector: '#mision_contenido',
            height: 300,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
            language: 'es',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
        
        // TinyMCE para Visión
        tinymce.init({
            selector: '#vision_contenido',
            height: 300,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
            language: 'es',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
        
        // Preview de imágenes
        function previewImage(input, previewId) {
            const previewContainer = document.getElementById(previewId + '-container');
            const preview = document.getElementById(previewId);
            const currentImage = document.getElementById(previewId + '-current');
            
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

