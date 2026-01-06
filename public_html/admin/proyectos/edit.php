<?php
/**
 * ========================================
 * ADMIN - EDITAR PROYECTO
 * ========================================
 * 
 * Formulario para editar un proyecto existente
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
    checkPermission('proyectos', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Obtener ID del proyecto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Cargar proyecto
$stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
$stmt->execute([$id]);
$proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proyecto) {
    header('Location: index.php?error=not_found');
    exit;
}

$success_message = '';
$error_message = '';

// Mensaje de creación exitosa
if (isset($_GET['created'])) {
    $success_message = 'Proyecto creado exitosamente. Ahora puedes agregar imágenes, videos y documentos.';
}

// Procesar acciones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    try {
        if ($_POST['ajax_action'] === 'delete_image') {
            $image_id = (int)$_POST['image_id'];
            $stmt = $pdo->prepare("SELECT imagen_url FROM proyecto_imagenes WHERE id = ? AND proyecto_id = ?");
            $stmt->execute([$image_id, $id]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($image) {
                $image_path = __DIR__ . '/../../' . $image['imagen_url'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
                
                $stmt = $pdo->prepare("DELETE FROM proyecto_imagenes WHERE id = ? AND proyecto_id = ?");
                $stmt->execute([$image_id, $id]);
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Imagen no encontrada']);
            }
            exit;
            
        } elseif ($_POST['ajax_action'] === 'set_main_image') {
            $image_id = (int)$_POST['image_id'];
            
            // Quitar principal de todas las imágenes
            $stmt = $pdo->prepare("UPDATE proyecto_imagenes SET es_principal = 0 WHERE proyecto_id = ?");
            $stmt->execute([$id]);
            
            // Marcar esta como principal
            $stmt = $pdo->prepare("UPDATE proyecto_imagenes SET es_principal = 1 WHERE id = ? AND proyecto_id = ?");
            $stmt->execute([$image_id, $id]);
            
            // Actualizar imagen_principal en proyecto
            $stmt = $pdo->prepare("SELECT imagen_url FROM proyecto_imagenes WHERE id = ?");
            $stmt->execute([$image_id]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($img) {
                $stmt = $pdo->prepare("UPDATE proyectos SET imagen_principal = ? WHERE id = ?");
                $stmt->execute([$img['imagen_url'], $id]);
            }
            
            echo json_encode(['success' => true]);
            exit;
            
        } elseif ($_POST['ajax_action'] === 'delete_video') {
            $video_id = (int)$_POST['video_id'];
            $stmt = $pdo->prepare("DELETE FROM proyecto_videos WHERE id = ? AND proyecto_id = ?");
            $stmt->execute([$video_id, $id]);
            echo json_encode(['success' => true]);
            exit;
            
        } elseif ($_POST['ajax_action'] === 'delete_document') {
            $doc_id = (int)$_POST['doc_id'];
            $stmt = $pdo->prepare("SELECT archivo_url FROM proyecto_documentos WHERE id = ? AND proyecto_id = ?");
            $stmt->execute([$doc_id, $id]);
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($doc) {
                $doc_path = __DIR__ . '/../../' . $doc['archivo_url'];
                if (file_exists($doc_path)) {
                    @unlink($doc_path);
                }
                
                $stmt = $pdo->prepare("DELETE FROM proyecto_documentos WHERE id = ? AND proyecto_id = ?");
                $stmt->execute([$doc_id, $id]);
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Documento no encontrado']);
            }
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Procesar formulario principal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_action'])) {
    try {
        $titulo = trim($_POST['titulo'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $sector = trim($_POST['sector'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $ano = !empty($_POST['ano']) ? (int)$_POST['ano'] : null;
        $pais = trim($_POST['pais'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $descripcion_corta = trim($_POST['descripcion_corta'] ?? '');
        $descripcion_larga = $_POST['descripcion_larga'] ?? '';
        $imagen_principal = trim($_POST['imagen_principal'] ?? '');
        $meta_titulo = trim($_POST['meta_titulo'] ?? '');
        $meta_descripcion = trim($_POST['meta_descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'borrador';
        
        // Validaciones
        if (empty($titulo)) {
            throw new Exception('El título es obligatorio');
        }
        
        // Generar slug si no se proporcionó
        if (empty($slug)) {
            $slug = generateSlug($titulo);
        } else {
            $slug = generateSlug($slug);
        }
        
        // Verificar que el slug sea único (excepto el actual)
        $stmt = $pdo->prepare("SELECT id FROM proyectos WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        // Procesar videos (URLs)
        if (isset($_POST['video_urls'])) {
            // Eliminar videos existentes
            $stmt = $pdo->prepare("DELETE FROM proyecto_videos WHERE proyecto_id = ?");
            $stmt->execute([$id]);
            
            // Insertar nuevos videos
            $video_urls = $_POST['video_urls'];
            $orden = 1;
            foreach ($video_urls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    // Determinar tipo de video
                    $tipo = 'youtube';
                    if (strpos($url, 'vimeo.com') !== false) {
                        $tipo = 'vimeo';
                    }
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO proyecto_videos (proyecto_id, url, tipo, orden, created_at)
                        VALUES (?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$id, $url, $tipo, $orden++]);
                }
            }
        }
        
        // Actualizar proyecto
        $sql = "UPDATE proyectos SET
            titulo = ?, slug = ?, sector = ?, categoria = ?, ano = ?, pais = ?, ubicacion = ?,
            descripcion_corta = ?, descripcion_larga = ?, imagen_principal = ?,
            meta_titulo = ?, meta_descripcion = ?, estado = ?, updated_at = NOW()
            WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $titulo, $slug, $sector, $categoria, $ano, $pais, $ubicacion,
            $descripcion_corta, $descripcion_larga, $imagen_principal,
            $meta_titulo, $meta_descripcion, $estado, $id
        ]);
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'proyectos', $id, 'proyecto', [
                'titulo' => $titulo
            ]);
        }
        
        $success_message = 'Proyecto actualizado exitosamente';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Recargar proyecto
$stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
$stmt->execute([$id]);
$proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener imágenes del proyecto
$stmt = $pdo->prepare("SELECT * FROM proyecto_imagenes WHERE proyecto_id = ? ORDER BY es_principal DESC, orden ASC");
$stmt->execute([$id]);
$imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener videos del proyecto
$stmt = $pdo->prepare("SELECT * FROM proyecto_videos WHERE proyecto_id = ? ORDER BY orden ASC");
$stmt->execute([$id]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener documentos del proyecto
$stmt = $pdo->prepare("SELECT * FROM proyecto_documentos WHERE proyecto_id = ? ORDER BY orden ASC");
$stmt->execute([$id]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'edit.php';
$current_dir = 'proyectos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/4u89qw1ptzfqell0ybjhqth1cc16ilb1y0792h3momw4lk8l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .image-item {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .image-actions {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
        }
        
        .image-badge {
            position: absolute;
            top: 5px;
            left: 5px;
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
                                <i class="bi bi-pencil-square me-2"></i>Editar Proyecto
                            </h2>
                            <p class="mb-0 opacity-75"><?php echo esc($proyecto['titulo']); ?></p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="view.php?id=<?php echo $id; ?>" class="btn btn-light">
                                <i class="bi bi-eye me-2"></i>Ver Proyecto
                            </a>
                            <a href="index.php" class="btn btn-light">
                                <i class="bi bi-arrow-left me-2"></i>Volver
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
                
                <form method="POST" action="" id="project-form">
                    <!-- Información Básica -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-info-circle me-2"></i>Información Básica
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="titulo" id="titulo" required 
                                       value="<?php echo esc($proyecto['titulo']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Año</label>
                                <input type="number" class="form-control" name="ano" min="2000" max="2099" 
                                       value="<?php echo esc($proyecto['ano'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug (URL)</label>
                                <input type="text" class="form-control" name="slug" id="slug" 
                                       value="<?php echo esc($proyecto['slug']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="borrador" <?php echo $proyecto['estado'] === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                    <option value="publicado" <?php echo $proyecto['estado'] === 'publicado' ? 'selected' : ''; ?>>Publicado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sector</label>
                                <input type="text" class="form-control" name="sector" 
                                       value="<?php echo esc($proyecto['sector'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" class="form-control" name="categoria" 
                                       value="<?php echo esc($proyecto['categoria'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">País</label>
                                <input type="text" class="form-control" name="pais" 
                                       value="<?php echo esc($proyecto['pais'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ubicación</label>
                                <input type="text" class="form-control" name="ubicacion" 
                                       value="<?php echo esc($proyecto['ubicacion'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-file-text me-2"></i>Descripción
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Corta</label>
                            <textarea class="form-control" name="descripcion_corta" rows="3"><?php echo esc($proyecto['descripcion_corta'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Larga</label>
                            <textarea class="form-control" name="descripcion_larga" id="descripcion_larga" rows="15"><?php echo $proyecto['descripcion_larga'] ?? ''; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Imagen Principal -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-image me-2"></i>Imagen Principal
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label">URL de Imagen</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="imagen_principal" id="imagen_principal" 
                                       value="<?php echo esc($proyecto['imagen_principal'] ?? ''); ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="openImageManager()">
                                    <i class="bi bi-folder"></i> Seleccionar
                                </button>
                            </div>
                        </div>
                        
                        <?php if ($proyecto['imagen_principal']): ?>
                        <div class="mt-3">
                            <img src="<?php echo SITE_URL . '/' . esc($proyecto['imagen_principal']); ?>" 
                                 alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Galería de Imágenes -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-images me-2"></i>Galería de Imágenes
                        </h4>
                        
                        <?php if (!empty($imagenes)): ?>
                        <div class="row mb-3" id="existing-images">
                            <?php foreach ($imagenes as $img): ?>
                            <div class="col-md-3 image-item" data-image-id="<?php echo $img['id']; ?>">
                                <img src="<?php echo SITE_URL . '/' . esc($img['imagen_url']); ?>" 
                                     alt="<?php echo esc($img['titulo'] ?? ''); ?>">
                                <?php if ($img['es_principal']): ?>
                                <span class="badge bg-success image-badge">Principal</span>
                                <?php endif; ?>
                                <div class="image-actions">
                                    <?php if (!$img['es_principal']): ?>
                                    <button type="button" class="btn btn-sm btn-success set-main-image" 
                                            data-image-id="<?php echo $img['id']; ?>" title="Marcar como principal">
                                        <i class="bi bi-star"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-danger delete-image" 
                                            data-image-id="<?php echo $img['id']; ?>" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Agregar Nuevas Imágenes</label>
                            <input type="file" class="form-control" id="nuevas_imagenes_input" accept="image/*" multiple>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-sm" id="upload-images-btn" disabled>
                            <i class="bi bi-upload me-1"></i>Subir Imágenes
                        </button>
                    </div>
                    
                    <!-- Videos -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-play-circle me-2"></i>Videos
                        </h4>
                        
                        <div id="videos-container">
                            <?php if (!empty($videos)): ?>
                            <?php foreach ($videos as $idx => $video): ?>
                            <div class="mb-2 video-item" data-video-id="<?php echo $video['id']; ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="video_urls[]" 
                                           value="<?php echo esc($video['url']); ?>" 
                                           placeholder="URL de YouTube o Vimeo">
                                    <button type="button" class="btn btn-danger delete-video" 
                                            data-video-id="<?php echo $video['id']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-video">
                            <i class="bi bi-plus-circle me-1"></i>Agregar Video
                        </button>
                    </div>
                    
                    <!-- Documentos -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Documentos
                        </h4>
                        
                        <?php if (!empty($documentos)): ?>
                        <div class="mb-3" id="existing-documents">
                            <?php foreach ($documentos as $doc): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 document-item" data-doc-id="<?php echo $doc['id']; ?>">
                                <div>
                                    <i class="bi bi-file-pdf me-2"></i>
                                    <strong><?php echo esc($doc['nombre']); ?></strong>
                                    <small class="text-muted">(<?php echo number_format($doc['tamaño'] / 1024, 2); ?> KB)</small>
                                </div>
                                <div>
                                    <a href="<?php echo SITE_URL . '/' . esc($doc['archivo_url']); ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-document" 
                                            data-doc-id="<?php echo $doc['id']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Agregar Nuevos Documentos</label>
                            <input type="file" class="form-control" id="nuevos_documentos_input" 
                                   accept=".pdf,.doc,.docx" multiple>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-sm" id="upload-documents-btn" disabled>
                            <i class="bi bi-upload me-1"></i>Subir Documentos
                        </button>
                    </div>
                    
                    <!-- SEO -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-search me-2"></i>SEO
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Título</label>
                            <input type="text" class="form-control" name="meta_titulo" 
                                   value="<?php echo esc($proyecto['meta_titulo'] ?? ''); ?>" maxlength="255">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Descripción</label>
                            <textarea class="form-control" name="meta_descripcion" rows="3" 
                                      maxlength="500"><?php echo esc($proyecto['meta_descripcion'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="form-card">
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TinyMCE
        tinymce.init({
            selector: '#descripcion_larga',
            height: 400,
            menubar: false,
            plugins: 'lists link table code image',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code | image',
            language: 'es'
        });
        
        // Generar slug automáticamente
        document.getElementById('titulo').addEventListener('input', function() {
            const slugInput = document.getElementById('slug');
            if (!slugInput.dataset.manualEdit) {
                const slug = this.value.toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });
        
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.manualEdit = 'true';
        });
        
        // Eliminar imagen
        document.querySelectorAll('.delete-image').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.imageId;
                if (confirm('¿Estás seguro de eliminar esta imagen?')) {
                    fetch('', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `ajax_action=delete_image&image_id=${imageId}`
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-image-id="${imageId}"]`).remove();
                        } else {
                            alert('Error al eliminar la imagen');
                        }
                    });
                }
            });
        });
        
        // Marcar imagen como principal
        document.querySelectorAll('.set-main-image').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.imageId;
                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `ajax_action=set_main_image&image_id=${imageId}`
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al marcar como principal');
                    }
                });
            });
        });
        
        // Eliminar video
        document.querySelectorAll('.delete-video').forEach(btn => {
            btn.addEventListener('click', function() {
                const videoId = this.dataset.videoId;
                if (confirm('¿Estás seguro de eliminar este video?')) {
                    fetch('', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `ajax_action=delete_video&video_id=${videoId}`
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-video-id="${videoId}"]`).remove();
                        } else {
                            alert('Error al eliminar el video');
                        }
                    });
                }
            });
        });
        
        // Agregar video
        document.getElementById('add-video').addEventListener('click', function() {
            const container = document.getElementById('videos-container');
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="video_urls[]" placeholder="URL de YouTube o Vimeo">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
        });
        
        // Eliminar documento
        document.querySelectorAll('.delete-document').forEach(btn => {
            btn.addEventListener('click', function() {
                const docId = this.dataset.docId;
                if (confirm('¿Estás seguro de eliminar este documento?')) {
                    fetch('', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `ajax_action=delete_document&doc_id=${docId}`
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-doc-id="${docId}"]`).remove();
                        } else {
                            alert('Error al eliminar el documento');
                        }
                    });
                }
            });
        });
        
        // Upload imágenes
        document.getElementById('nuevas_imagenes_input').addEventListener('change', function() {
            document.getElementById('upload-images-btn').disabled = this.files.length === 0;
        });
        
        document.getElementById('upload-images-btn').addEventListener('click', function() {
            const input = document.getElementById('nuevas_imagenes_input');
            const files = input.files;
            
            if (files.length === 0) {
                alert('Por favor selecciona al menos una imagen');
                return;
            }
            
            const formData = new FormData();
            formData.append('proyecto_id', <?php echo $id; ?>);
            for (let i = 0; i < files.length; i++) {
                formData.append('imagenes[]', files[i]);
            }
            
            this.disabled = true;
            fetch('upload-image.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    input.value = '';
                    this.disabled = true;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.error || 'Error desconocido'));
                    this.disabled = false;
                }
            });
        });
        
        // Upload documentos
        document.getElementById('nuevos_documentos_input').addEventListener('change', function() {
            document.getElementById('upload-documents-btn').disabled = this.files.length === 0;
        });
        
        document.getElementById('upload-documents-btn').addEventListener('click', function() {
            const input = document.getElementById('nuevos_documentos_input');
            const files = input.files;
            
            if (files.length === 0) {
                alert('Por favor selecciona al menos un documento');
                return;
            }
            
            const formData = new FormData();
            formData.append('proyecto_id', <?php echo $id; ?>);
            for (let i = 0; i < files.length; i++) {
                formData.append('documentos[]', files[i]);
            }
            
            this.disabled = true;
            fetch('upload-document.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    input.value = '';
                    this.disabled = true;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.error || 'Error desconocido'));
                    this.disabled = false;
                }
            });
        });
        
        function openImageManager() {
            window.open('../blog/image-manager.php?callback=setProjectImage', 'ImageManager', 'width=900,height=600');
        }
        
        function setProjectImage(imageUrl) {
            document.getElementById('imagen_principal').value = imageUrl;
        }
    </script>
</body>
</html>

