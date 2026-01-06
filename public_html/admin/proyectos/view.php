<?php
/**
 * ========================================
 * ADMIN - VISTA DETALLADA DE PROYECTO
 * ========================================
 * 
 * Vista detallada de un proyecto
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
    checkPermission('proyectos', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener ID del proyecto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Obtener proyecto
$stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
$stmt->execute([$id]);
$proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proyecto) {
    header('Location: index.php?error=not_found');
    exit;
}

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

$current_page = 'view.php';
$current_dir = 'proyectos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Proyecto - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .gallery-item {
            margin-bottom: 1rem;
        }
        
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
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
                                <i class="bi bi-folder me-2"></i><?php echo esc($proyecto['titulo']); ?>
                            </h2>
                            <p class="mb-0 opacity-75">
                                <span class="badge bg-<?php echo $proyecto['estado'] === 'publicado' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($proyecto['estado']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <?php if (function_exists('hasPermission') && hasPermission('proyectos', 'editar')): ?>
                            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-light">
                                <i class="bi bi-pencil me-2"></i>Editar
                            </a>
                            <?php endif; ?>
                            <a href="index.php" class="btn btn-light">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Información Principal -->
                    <div class="col-lg-8">
                        <!-- Imagen Principal -->
                        <?php if ($proyecto['imagen_principal']): ?>
                        <div class="info-card">
                            <img src="<?php echo SITE_URL . '/' . esc($proyecto['imagen_principal']); ?>" 
                                 alt="<?php echo esc($proyecto['titulo']); ?>" 
                                 class="img-fluid rounded">
                        </div>
                        <?php endif; ?>
                        
                        <!-- Descripción -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-file-text me-2"></i>Descripción
                            </h5>
                            <?php if ($proyecto['descripcion_corta']): ?>
                            <p class="lead"><?php echo nl2br(esc($proyecto['descripcion_corta'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($proyecto['descripcion_larga']): ?>
                            <div class="mt-3">
                                <?php echo $proyecto['descripcion_larga']; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Galería de Imágenes -->
                        <?php if (!empty($imagenes)): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-images me-2"></i>Galería de Imágenes (<?php echo count($imagenes); ?>)
                            </h5>
                            <div class="row">
                                <?php foreach ($imagenes as $img): ?>
                                <div class="col-md-4 gallery-item">
                                    <img src="<?php echo SITE_URL . '/' . esc($img['imagen_url']); ?>" 
                                         alt="<?php echo esc($img['titulo'] ?? ''); ?>"
                                         onclick="openLightbox('<?php echo SITE_URL . '/' . esc($img['imagen_url']); ?>')">
                                    <?php if ($img['titulo']): ?>
                                    <p class="small text-muted mt-2 mb-0"><?php echo esc($img['titulo']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Videos -->
                        <?php if (!empty($videos)): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-play-circle me-2"></i>Videos (<?php echo count($videos); ?>)
                            </h5>
                            <?php foreach ($videos as $video): ?>
                            <div class="mb-3">
                                <?php
                                $embed_url = '';
                                if ($video['tipo'] === 'youtube') {
                                    // Extraer ID de YouTube
                                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video['url'], $matches);
                                    if (!empty($matches[1])) {
                                        $embed_url = 'https://www.youtube.com/embed/' . $matches[1];
                                    }
                                } elseif ($video['tipo'] === 'vimeo') {
                                    // Extraer ID de Vimeo
                                    preg_match('/vimeo\.com\/(\d+)/', $video['url'], $matches);
                                    if (!empty($matches[1])) {
                                        $embed_url = 'https://player.vimeo.com/video/' . $matches[1];
                                    }
                                }
                                ?>
                                <?php if ($embed_url): ?>
                                <div class="ratio ratio-16x9">
                                    <iframe src="<?php echo esc($embed_url); ?>" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen></iframe>
                                </div>
                                <?php else: ?>
                                <a href="<?php echo esc($video['url']); ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-play-circle me-2"></i>Ver Video
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Información del Proyecto -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-info-circle me-2"></i>Información del Proyecto
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Estado</label>
                                <div>
                                    <span class="badge bg-<?php echo $proyecto['estado'] === 'publicado' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($proyecto['estado']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($proyecto['ano']): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Año</label>
                                <div><i class="bi bi-calendar me-1"></i><?php echo $proyecto['ano']; ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto['sector']): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Sector</label>
                                <div><?php echo esc($proyecto['sector']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto['categoria']): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Categoría</label>
                                <div><?php echo esc($proyecto['categoria']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto['pais'] || $proyecto['ubicacion']): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Ubicación</label>
                                <div>
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?php if ($proyecto['ubicacion']): ?>
                                    <?php echo esc($proyecto['ubicacion']); ?>
                                    <?php endif; ?>
                                    <?php if ($proyecto['pais']): ?>
                                    <?php if ($proyecto['ubicacion']): ?>, <?php endif; ?>
                                    <?php echo esc($proyecto['pais']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Slug (URL)</label>
                                <div>
                                    <code><?php echo esc($proyecto['slug']); ?></code>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Fecha de Creación</label>
                                <div>
                                    <i class="bi bi-clock me-1"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($proyecto['created_at'])); ?>
                                </div>
                            </div>
                            
                            <?php if ($proyecto['updated_at']): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Última Actualización</label>
                                <div>
                                    <i class="bi bi-clock-history me-1"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($proyecto['updated_at'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Documentos -->
                        <?php if (!empty($documentos)): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Documentos (<?php echo count($documentos); ?>)
                            </h5>
                            <?php foreach ($documentos as $doc): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <i class="bi bi-file-pdf me-2"></i>
                                    <strong><?php echo esc($doc['nombre']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo number_format($doc['tamaño'] / 1024, 2); ?> KB</small>
                                </div>
                                <a href="<?php echo SITE_URL . '/' . esc($doc['archivo_url']); ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- SEO -->
                        <?php if ($proyecto['meta_titulo'] || $proyecto['meta_descripcion']): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-search me-2"></i>SEO
                            </h5>
                            
                            <?php if ($proyecto['meta_titulo']): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Meta Título</label>
                                <div><?php echo esc($proyecto['meta_titulo']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto['meta_descripcion']): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Meta Descripción</label>
                                <div><?php echo esc($proyecto['meta_descripcion']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lightbox Modal -->
    <div class="modal fade" id="lightboxModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="lightbox-image" src="" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openLightbox(imageUrl) {
            document.getElementById('lightbox-image').src = imageUrl;
            const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
            modal.show();
        }
    </script>
</body>
</html>

