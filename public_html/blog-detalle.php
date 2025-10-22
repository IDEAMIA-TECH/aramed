<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Detalle del Blog
 * ========================================
 * 
 * Página de detalle de un artículo del blog
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

// Obtener slug del artículo
$slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';

if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Obtener artículo
$sql_articulo = "
    SELECT a.*, c.nombre as categoria_nombre, c.slug as categoria_slug, c.color as categoria_color, c.icono as categoria_icono
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    WHERE a.slug = ? AND a.estado = 'publicado'
";

$stmt_articulo = $pdo->prepare($sql_articulo);
$stmt_articulo->execute([$slug]);
$articulo = $stmt_articulo->fetch(PDO::FETCH_ASSOC);

if (!$articulo) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Incrementar contador de vistas
$sql_vistas = "UPDATE blog_articulos SET vistas = vistas + 1 WHERE id = ?";
$stmt_vistas = $pdo->prepare($sql_vistas);
$stmt_vistas->execute([$articulo['id']]);

// Obtener artículos relacionados
$sql_relacionados = "
    SELECT a.*, c.nombre as categoria_nombre, c.slug as categoria_slug, c.color as categoria_color
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    WHERE a.categoria_id = ? AND a.id != ? AND a.estado = 'publicado'
    ORDER BY a.fecha_publicacion DESC
    LIMIT 3
";

$stmt_relacionados = $pdo->prepare($sql_relacionados);
$stmt_relacionados->execute([$articulo['categoria_id'], $articulo['id']]);
$articulos_relacionados = $stmt_relacionados->fetchAll(PDO::FETCH_ASSOC);

// Obtener comentarios aprobados
$sql_comentarios = "
    SELECT * FROM blog_comentarios 
    WHERE articulo_id = ? AND estado = 'aprobado'
    ORDER BY created_at DESC
";

$stmt_comentarios = $pdo->prepare($sql_comentarios);
$stmt_comentarios->execute([$articulo['id']]);
$comentarios = $stmt_comentarios->fetchAll(PDO::FETCH_ASSOC);

// Función para formatear fecha
function formatBlogDate($fecha) {
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    $fecha_obj = new DateTime($fecha);
    $dia = $fecha_obj->format('d');
    $mes = $meses[(int)$fecha_obj->format('n')];
    $año = $fecha_obj->format('Y');
    
    return "$dia de $mes, $año";
}

// Función para formatear fecha ISO
function formatISODate($fecha) {
    $fecha_obj = new DateTime($fecha);
    return $fecha_obj->format('c');
}

// Variables para meta tags
$pageTitle = $articulo['meta_title'] ?: $articulo['titulo'] . ' - ' . SITE_NAME;
$pageDescription = $articulo['meta_description'] ?: $articulo['resumen'];
$pageKeywords = $articulo['meta_keywords'] ?: 'blog, simulación médica, educación médica, tecnología';
$pageUrl = SITE_URL . '/blog-detalle.php?slug=' . $articulo['slug'];
$pageImage = !empty($articulo['imagen_og']) ? SITE_URL . $articulo['imagen_og'] : 
             (!empty($articulo['imagen_principal']) ? SITE_URL . $articulo['imagen_principal'] : imageUrl('design/logo-og.jpg'));

// Procesar tags
$tags = !empty($articulo['tags']) ? json_decode($articulo['tags'], true) : [];
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <!-- ========================================
         META TAGS BÁSICOS
         ======================================== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    
    <!-- ========================================
         SEO BÁSICO
         ======================================== -->
    <title><?php echo esc($pageTitle); ?></title>
    <meta name="description" content="<?php echo esc($pageDescription); ?>">
    <meta name="keywords" content="<?php echo esc($pageKeywords); ?>">
    <meta name="author" content="<?php echo esc($articulo['autor']); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo esc($pageUrl); ?>">
    
    <!-- ========================================
         OPEN GRAPH (Facebook, LinkedIn)
         ======================================== -->
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="<?php echo esc(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo esc($pageTitle); ?>">
    <meta property="og:description" content="<?php echo esc($pageDescription); ?>">
    <meta property="og:url" content="<?php echo esc($pageUrl); ?>">
    <meta property="og:image" content="<?php echo esc($pageImage); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="es_MX">
    <meta property="article:author" content="<?php echo esc($articulo['autor']); ?>">
    <meta property="article:published_time" content="<?php echo formatISODate($articulo['fecha_publicacion']); ?>">
    <meta property="article:modified_time" content="<?php echo formatISODate($articulo['updated_at']); ?>">
    <?php if (!empty($tags)): ?>
    <?php foreach ($tags as $tag): ?>
    <meta property="article:tag" content="<?php echo esc($tag); ?>">
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- ========================================
         TWITTER CARD
         ======================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@aramedylab">
    <meta name="twitter:title" content="<?php echo esc($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo esc($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo esc($pageImage); ?>">
    
    <!-- ========================================
         FAVICON
         ======================================== -->
    <link rel="shortcut icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    <link rel="icon" href="<?php echo imageUrl('design/favicon.ico'); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo imageUrl('design/favicon-32x32.png'); ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?php echo imageUrl('design/favicon-16x16.png'); ?>" type="image/png" sizes="16x16">
    
    <!-- ========================================
         CSS
         ======================================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="<?php echo assetUrl('css/landing.css'); ?>" rel="stylesheet">
    <link href="<?php echo assetUrl('css/blog.css'); ?>" rel="stylesheet">
    
    <!-- ========================================
         SCHEMA.ORG STRUCTURED DATA
         ======================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "<?php echo esc($articulo['titulo']); ?>",
        "description": "<?php echo esc($pageDescription); ?>",
        "image": "<?php echo esc($pageImage); ?>",
        "author": {
            "@type": "Person",
            "name": "<?php echo esc($articulo['autor']); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo esc(SITE_NAME); ?>",
            "url": "<?php echo esc(SITE_URL); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo imageUrl('design/logo.png'); ?>"
            }
        },
        "datePublished": "<?php echo formatISODate($articulo['fecha_publicacion']); ?>",
        "dateModified": "<?php echo formatISODate($articulo['updated_at']); ?>",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?php echo esc($pageUrl); ?>"
        },
        "articleSection": "<?php echo esc($articulo['categoria_nombre']); ?>",
        "wordCount": "<?php echo str_word_count(strip_tags($articulo['contenido'])); ?>",
        "interactionStatistic": {
            "@type": "InteractionCounter",
            "interactionType": "https://schema.org/ReadAction",
            "userInteractionCount": "<?php echo $articulo['vistas']; ?>"
        }
    }
    </script>
</head>

<body>
    <!-- ========================================
         HEADER
         ======================================== -->
    <?php component('topbar'); ?>
    <?php component('navbar'); ?>
    
    <!-- ========================================
         BREADCRUMB
         ======================================== -->
    <nav aria-label="breadcrumb" class="bg-light py-3">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo siteUrl(); ?>" class="text-decoration-none">
                        <i class="bi bi-house me-1"></i>Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo siteUrl('blog.php'); ?>" class="text-decoration-none">Blog</a>
                </li>
                <?php if ($articulo['categoria_id']): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo siteUrl('blog.php?categoria=' . $articulo['categoria_id']); ?>" class="text-decoration-none">
                        <?php echo esc($articulo['categoria_nombre']); ?>
                    </a>
                </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo esc($articulo['titulo']); ?>
                </li>
            </ol>
        </div>
    </nav>

    <!-- ========================================
         HERO DEL ARTÍCULO
         ======================================== -->
    <section class="blog-article-hero py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Categoría -->
                    <?php if ($articulo['categoria_id']): ?>
                    <div class="text-center mb-4" data-aos="fade-up">
                        <a href="<?php echo siteUrl('blog.php?categoria=' . $articulo['categoria_id']); ?>" 
                           class="badge rounded-pill px-4 py-2 text-decoration-none fs-6"
                           style="background-color: <?php echo $articulo['categoria_color']; ?>;">
                            <i class="<?php echo $articulo['categoria_icono']; ?> me-2"></i>
                            <?php echo esc($articulo['categoria_nombre']); ?>
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Título -->
                    <h1 class="display-5 fw-bold text-center mb-4" data-aos="fade-up" data-aos-delay="100">
                        <?php echo esc($articulo['titulo']); ?>
                    </h1>

                    <!-- Resumen -->
                    <?php if (!empty($articulo['resumen'])): ?>
                    <p class="lead text-center text-muted mb-4" data-aos="fade-up" data-aos-delay="200">
                        <?php echo esc($articulo['resumen']); ?>
                    </p>
                    <?php endif; ?>

                    <!-- Meta información -->
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="d-flex align-items-center text-muted">
                            <i class="bi bi-person-circle me-2"></i>
                            <span><?php echo esc($articulo['autor']); ?></span>
                        </div>
                        <div class="d-flex align-items-center text-muted">
                            <i class="bi bi-calendar3 me-2"></i>
                            <span><?php echo formatBlogDate($articulo['fecha_publicacion']); ?></span>
                        </div>
                        <div class="d-flex align-items-center text-muted">
                            <i class="bi bi-eye me-2"></i>
                            <span><?php echo number_format($articulo['vistas']); ?> vistas</span>
                        </div>
                        <div class="d-flex align-items-center text-muted">
                            <i class="bi bi-clock me-2"></i>
                            <span><?php echo ceil(str_word_count(strip_tags($articulo['contenido'])) / 200); ?> min de lectura</span>
                        </div>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                    <div class="text-center mb-4" data-aos="fade-up" data-aos-delay="400">
                        <?php foreach ($tags as $tag): ?>
                        <span class="badge bg-light text-dark me-2 mb-2">#<?php echo esc($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Botones de compartir -->
                    <div class="text-center" data-aos="fade-up" data-aos-delay="500">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="shareOnFacebook()">
                                <i class="bi bi-facebook me-1"></i>Facebook
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="shareOnTwitter()">
                                <i class="bi bi-twitter me-1"></i>Twitter
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="shareOnLinkedIn()">
                                <i class="bi bi-linkedin me-1"></i>LinkedIn
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="copyLink()">
                                <i class="bi bi-link-45deg me-1"></i>Copiar enlace
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
         IMAGEN PRINCIPAL
         ======================================== -->
    <?php if (!empty($articulo['imagen_principal'])): ?>
    <section class="blog-article-image py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <img src="<?php echo SITE_URL . $articulo['imagen_principal']; ?>" 
                         alt="<?php echo esc($articulo['titulo']); ?>" 
                         class="img-fluid rounded shadow"
                         data-aos="fade-up">
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CONTENIDO DEL ARTÍCULO
         ======================================== -->
    <section class="blog-article-content py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <article class="blog-content" data-aos="fade-up">
                        <?php echo $articulo['contenido']; ?>
                    </article>

                    <!-- Botones de compartir al final -->
                    <div class="text-center mt-5 pt-4 border-top">
                        <h5 class="mb-3">¿Te gustó este artículo? ¡Compártelo!</h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="shareOnFacebook()">
                                <i class="bi bi-facebook me-1"></i>Facebook
                            </button>
                            <button type="button" class="btn btn-info" onclick="shareOnTwitter()">
                                <i class="bi bi-twitter me-1"></i>Twitter
                            </button>
                            <button type="button" class="btn btn-success" onclick="shareOnLinkedIn()">
                                <i class="bi bi-linkedin me-1"></i>LinkedIn
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="copyLink()">
                                <i class="bi bi-link-45deg me-1"></i>Copiar enlace
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
         COMENTARIOS
         ======================================== -->
    <section class="blog-comments py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h3 class="h4 mb-4" data-aos="fade-up">
                        <i class="bi bi-chat-dots me-2"></i>Comentarios (<?php echo count($comentarios); ?>)
                    </h3>

                    <!-- Formulario de comentarios -->
                    <div class="card mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card-body">
                            <h5 class="card-title">Deja tu comentario</h5>
                            <form id="commentForm" action="includes/blog_comment_handler.php" method="POST">
                                <input type="hidden" name="articulo_id" value="<?php echo $articulo['id']; ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comentario" class="form-label">Comentario *</label>
                                    <textarea class="form-control" id="comentario" name="comentario" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Enviar comentario
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de comentarios -->
                    <div class="comments-list" data-aos="fade-up" data-aos-delay="200">
                        <?php if (!empty($comentarios)): ?>
                            <?php foreach ($comentarios as $comentario): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0"><?php echo esc($comentario['nombre']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo formatBlogDate($comentario['created_at']); ?>
                                        </small>
                                    </div>
                                    <p class="card-text"><?php echo nl2br(esc($comentario['comentario'])); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-chat-dots display-4 text-muted mb-3"></i>
                                <p class="text-muted">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
         ARTÍCULOS RELACIONADOS
         ======================================== -->
    <?php if (!empty($articulos_relacionados)): ?>
    <section class="blog-related py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h3 class="h4 text-center mb-5" data-aos="fade-up">
                        <i class="bi bi-collection me-2"></i>Artículos Relacionados
                    </h3>
                    <div class="row g-4">
                        <?php foreach ($articulos_relacionados as $index => $relacionado): ?>
                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <article class="card h-100 border-0 shadow-sm">
                                <img src="<?php echo !empty($relacionado['imagen_principal']) ? SITE_URL . $relacionado['imagen_principal'] : imageUrl('design/placeholder-blog.jpg'); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo esc($relacionado['titulo']); ?>"
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <span class="badge rounded-pill px-3 py-2" 
                                              style="background-color: <?php echo $relacionado['categoria_color']; ?>;">
                                            <?php echo esc($relacionado['categoria_nombre']); ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title">
                                        <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $relacionado['slug']); ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo esc($relacionado['titulo']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php echo esc(truncateText($relacionado['resumen'], 100)); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?php echo formatBlogDate($relacionado['fecha_publicacion']); ?>
                                        </small>
                                        <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $relacionado['slug']); ?>" 
                                           class="btn btn-primary btn-sm">
                                            Leer más
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         FOOTER
         ======================================== -->
    <?php component('footer'); ?>

    <!-- ========================================
         SCRIPTS
         ======================================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?php echo assetUrl('js/landing.js'); ?>"></script>
    <script src="<?php echo assetUrl('js/blog.js'); ?>"></script>
    
    <script>
        // Inicializar AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Funciones para compartir
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('<?php echo esc($articulo['titulo']); ?>');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        }

        function shareOnLinkedIn() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=400');
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                // Mostrar notificación
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check me-1"></i>¡Copiado!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-secondary', 'btn-secondary');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }

        // Manejar formulario de comentarios
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('includes/blog_comment_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Comentario enviado correctamente. Será revisado antes de publicarse.');
                    this.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error de conexión. Por favor, intenta de nuevo.');
            });
        });
    </script>
</body>
</html>
