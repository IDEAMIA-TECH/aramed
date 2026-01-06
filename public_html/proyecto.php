<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Detalle de Proyecto
 * ========================================
 * 
 * Página de detalles de un proyecto individual
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';

// Cargar funciones
require_once INCLUDES_PATH . '/functions.php';

// Cargar conexión a la base de datos
require_once INCLUDES_PATH . '/connection.php';

// Obtener slug del proyecto
$slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: proyectos.php');
    exit;
}

// Obtener información del proyecto
try {
    $pdo = getDB();
    
    // Consulta principal del proyecto
    $proyecto_sql = "
        SELECT * FROM proyectos
        WHERE slug = ? AND estado = 'publicado'
    ";
    
    $proyecto_stmt = $pdo->prepare($proyecto_sql);
    $proyecto_stmt->execute([$slug]);
    $proyecto = $proyecto_stmt->fetch();
    
    if (!$proyecto) {
        header('Location: proyectos.php');
        exit;
    }
    
    // Obtener imágenes del proyecto
    $images_sql = "
        SELECT * FROM proyecto_imagenes 
        WHERE proyecto_id = ? 
        ORDER BY es_principal DESC, orden ASC
    ";
    $images_stmt = $pdo->prepare($images_sql);
    $images_stmt->execute([$proyecto['id']]);
    $images = $images_stmt->fetchAll();
    
    // Obtener videos del proyecto
    $videos_sql = "
        SELECT * FROM proyecto_videos 
        WHERE proyecto_id = ? 
        ORDER BY orden ASC
    ";
    $videos_stmt = $pdo->prepare($videos_sql);
    $videos_stmt->execute([$proyecto['id']]);
    $videos = $videos_stmt->fetchAll();
    
    // Obtener documentos del proyecto
    $documents_sql = "
        SELECT * FROM proyecto_documentos 
        WHERE proyecto_id = ? 
        ORDER BY orden ASC
    ";
    $documents_stmt = $pdo->prepare($documents_sql);
    $documents_stmt->execute([$proyecto['id']]);
    $documents = $documents_stmt->fetchAll();
    
    // Obtener proyectos relacionados (mismo sector o categoría)
    $related_sql = "
        SELECT p.*
        FROM proyectos p
        WHERE p.estado = 'publicado' 
        AND p.id != ?
        AND (p.sector = ? OR p.categoria = ?)
        ORDER BY p.ano DESC, RAND()
        LIMIT 3
    ";
    $related_stmt = $pdo->prepare($related_sql);
    $related_stmt->execute([$proyecto['id'], $proyecto['sector'], $proyecto['categoria']]);
    $related_proyectos = $related_stmt->fetchAll();
    
    // Si no hay relacionados, obtener los más recientes
    if (empty($related_proyectos)) {
        $related_sql = "
            SELECT * FROM proyectos
            WHERE estado = 'publicado' AND id != ?
            ORDER BY ano DESC, created_at DESC
            LIMIT 3
        ";
        $related_stmt = $pdo->prepare($related_sql);
        $related_stmt->execute([$proyecto['id']]);
        $related_proyectos = $related_stmt->fetchAll();
    }
    
} catch (Exception $e) {
    error_log("Error en proyecto.php: " . $e->getMessage());
    header('Location: proyectos.php');
    exit;
}

// Variables para meta tags
$pageTitle = ($proyecto['meta_titulo'] ?: $proyecto['titulo']) . ' - ' . SITE_NAME;
$pageDescription = $proyecto['meta_descripcion'] ?: ($proyecto['descripcion_corta'] ?: substr(strip_tags($proyecto['descripcion_larga']), 0, 160));
$pageKeywords = $proyecto['titulo'] . ', proyectos, ' . ($proyecto['sector'] ?: '') . ', ' . ($proyecto['categoria'] ?: '');
$pageUrl = SITE_URL . '/proyecto.php?slug=' . $slug;

// Incluir header
include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="hero-section-proyecto py-5 text-white position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>" class="text-white-50">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="proyectos.php" class="text-white-50">Proyectos</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo esc(truncateText($proyecto['titulo'], 50)); ?></li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-3"><?php echo esc($proyecto['titulo']); ?></h1>
                <?php if ($proyecto['descripcion_corta']): ?>
                <p class="lead"><?php echo esc($proyecto['descripcion_corta']); ?></p>
                <?php endif; ?>
                
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <?php if ($proyecto['ano']): ?>
                    <div>
                        <i class="bi bi-calendar me-2"></i>
                        <strong><?php echo $proyecto['ano']; ?></strong>
                    </div>
                    <?php endif; ?>
                    <?php if ($proyecto['pais'] || $proyecto['ubicacion']): ?>
                    <div>
                        <i class="bi bi-geo-alt me-2"></i>
                        <strong>
                            <?php if ($proyecto['ubicacion']): ?>
                            <?php echo esc($proyecto['ubicacion']); ?>
                            <?php endif; ?>
                            <?php if ($proyecto['pais']): ?>
                            <?php if ($proyecto['ubicacion']): ?>, <?php endif; ?>
                            <?php echo esc($proyecto['pais']); ?>
                            <?php endif; ?>
                        </strong>
                    </div>
                    <?php endif; ?>
                    <?php if ($proyecto['sector']): ?>
                    <div>
                        <i class="bi bi-building me-2"></i>
                        <strong><?php echo esc($proyecto['sector']); ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contenido Principal -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contenido -->
            <div class="col-lg-8">
                <!-- Imagen Principal -->
                <?php if ($proyecto['imagen_principal']): ?>
                <div class="mb-4">
                    <img src="<?php echo imageUrl($proyecto['imagen_principal']); ?>" 
                         alt="<?php echo esc($proyecto['titulo']); ?>" 
                         class="img-fluid rounded shadow">
                </div>
                <?php endif; ?>
                
                <!-- Descripción Larga -->
                <?php if ($proyecto['descripcion_larga']): ?>
                <div class="mb-5">
                    <?php echo $proyecto['descripcion_larga']; ?>
                </div>
                <?php endif; ?>
                
                <!-- Galería de Imágenes -->
                <?php if (!empty($images) && count($images) > 1): ?>
                <div class="mb-5">
                    <h3 class="mb-4">Galería de Imágenes</h3>
                    <div class="row g-3">
                        <?php foreach ($images as $img): ?>
                        <div class="col-md-6">
                            <a href="<?php echo imageUrl($img['imagen_url']); ?>" 
                               data-lightbox="gallery" 
                               data-title="<?php echo esc($img['titulo'] ?? $proyecto['titulo']); ?>">
                                <img src="<?php echo imageUrl($img['imagen_url']); ?>" 
                                     alt="<?php echo esc($img['titulo'] ?? ''); ?>" 
                                     class="img-fluid rounded shadow-sm">
                            </a>
                            <?php if ($img['titulo']): ?>
                            <p class="text-muted small mt-2 mb-0"><?php echo esc($img['titulo']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Videos -->
                <?php if (!empty($videos)): ?>
                <div class="mb-5">
                    <h3 class="mb-4">Videos</h3>
                    <?php foreach ($videos as $video): ?>
                    <div class="mb-4">
                        <?php
                        $embed_url = '';
                        if ($video['tipo'] === 'youtube') {
                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video['url'], $matches);
                            if (!empty($matches[1])) {
                                $embed_url = 'https://www.youtube.com/embed/' . $matches[1];
                            }
                        } elseif ($video['tipo'] === 'vimeo') {
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
                        <a href="<?php echo esc($video['url']); ?>" target="_blank" class="btn btn-primary">
                            <i class="bi bi-play-circle me-2"></i>Ver Video
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Documentos -->
                <?php if (!empty($documents)): ?>
                <div class="mb-5">
                    <h3 class="mb-4">Documentos</h3>
                    <div class="list-group">
                        <?php foreach ($documents as $doc): ?>
                        <a href="<?php echo SITE_URL . '/' . ltrim(esc($doc['archivo_url']), '/'); ?>" 
                           target="_blank" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-file-pdf me-2 text-danger"></i>
                                <strong><?php echo esc($doc['nombre']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo number_format($doc['tamaño'] / 1024, 2); ?> KB</small>
                            </div>
                            <i class="bi bi-download"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Información del Proyecto -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Información del Proyecto
                        </h5>
                    </div>
                    <div class="card-body">
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
                        
                        <?php if ($proyecto['ano']): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Año</label>
                            <div><?php echo $proyecto['ano']; ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($proyecto['pais'] || $proyecto['ubicacion']): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Ubicación</label>
                            <div>
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
                    </div>
                </div>
                
                <!-- CTA -->
                <div class="card shadow-sm bg-primary text-white">
                    <div class="card-body text-center">
                        <h5 class="mb-3">¿Interesado en un proyecto similar?</h5>
                        <p class="mb-3">Contáctanos y te ayudaremos a encontrar la solución perfecta para tu institución.</p>
                        <a href="<?php echo SITE_URL; ?>#contacto" class="btn btn-light">
                            <i class="bi bi-envelope me-2"></i>Contactar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Proyectos Relacionados -->
        <?php if (!empty($related_proyectos)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Proyectos Relacionados</h3>
                <div class="row">
                    <?php foreach ($related_proyectos as $rel): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($rel['imagen_principal']): ?>
                            <img src="<?php echo imageUrl($rel['imagen_principal']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo esc($rel['titulo']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="proyecto.php?slug=<?php echo esc($rel['slug']); ?>" class="text-decoration-none">
                                        <?php echo esc(truncateText($rel['titulo'], 50)); ?>
                                    </a>
                                </h5>
                                <?php if ($rel['descripcion_corta']): ?>
                                <p class="card-text text-muted small">
                                    <?php echo esc(truncateText($rel['descripcion_corta'], 100)); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="proyecto.php?slug=<?php echo esc($rel['slug']); ?>" class="btn btn-sm btn-primary w-100">
                                    Ver Proyecto
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<style>
.hero-section-proyecto {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<?php
// Incluir footer
include INCLUDES_PATH . '/footer.php';
?>

