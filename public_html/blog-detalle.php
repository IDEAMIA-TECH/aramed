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
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/connection.php';

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener slug del artículo
$slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: ' . siteUrl('blog.php'));
    exit;
}

// Obtener artículo por slug
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
    header('Location: ' . siteUrl('blog.php'));
    exit;
}

// Incrementar contador de vistas
$sql_vistas = "UPDATE blog_articulos SET vistas = vistas + 1 WHERE id = ?";
$stmt_vistas = $pdo->prepare($sql_vistas);
$stmt_vistas->execute([$articulo['id']]);

// Obtener artículos relacionados
$sql_relacionados = "
    SELECT a.*, c.nombre as categoria_nombre, c.color as categoria_color
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    WHERE a.categoria_id = ? AND a.id != ? AND a.estado = 'publicado'
    ORDER BY a.destacado DESC, a.fecha_publicacion DESC
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

// Procesar envío de comentario
$mensaje_comentario = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_comentario'])) {
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $comentario = sanitizeInput($_POST['comentario'] ?? '');
    
    if (!empty($nombre) && !empty($email) && !empty($comentario)) {
        $sql_insert_comentario = "
            INSERT INTO blog_comentarios (articulo_id, nombre, email, comentario, ip_address, user_agent, estado, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pendiente', NOW())
        ";
        
        $stmt_insert = $pdo->prepare($sql_insert_comentario);
        $resultado = $stmt_insert->execute([
            $articulo['id'],
            $nombre,
            $email,
            $comentario,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        if ($resultado) {
            $mensaje_comentario = 'Tu comentario ha sido enviado y está pendiente de moderación.';
            $tipo_mensaje = 'success';
        } else {
            $mensaje_comentario = 'Error al enviar el comentario. Inténtalo de nuevo.';
            $tipo_mensaje = 'danger';
        }
    } else {
        $mensaje_comentario = 'Por favor completa todos los campos.';
        $tipo_mensaje = 'warning';
    }
}
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
    <title><?php echo esc($articulo['titulo']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo esc($articulo['meta_descripcion'] ?? truncateText($articulo['extracto'] ?? '', 160)); ?>">
    <meta name="keywords" content="<?php echo esc($articulo['tags'] ?? 'blog, simulación médica, educación en salud'); ?>">
    <meta name="author" content="<?php echo esc($articulo['autor']); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>">
    
    <!-- ========================================
         OPEN GRAPH
         ======================================== -->
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="<?php echo esc(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo esc($articulo['titulo']); ?>">
    <meta property="og:description" content="<?php echo esc($articulo['meta_descripcion'] ?? truncateText($articulo['extracto'] ?? '', 160)); ?>">
    <meta property="og:image" content="<?php echo SITE_URL . ($articulo['imagen_og'] ?? $articulo['imagen_principal'] ?? '/assets/images/blog/default-article-og.jpg'); ?>">
    <meta property="og:url" content="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>">
    <meta property="og:locale" content="es_MX">
    <meta property="article:author" content="<?php echo esc($articulo['autor']); ?>">
    <meta property="article:published_time" content="<?php echo date('c', strtotime($articulo['fecha_publicacion'])); ?>">
    
    <!-- ========================================
         TWITTER CARD
         ======================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc($articulo['titulo']); ?>">
    <meta name="twitter:description" content="<?php echo esc($articulo['meta_descripcion'] ?? truncateText($articulo['extracto'] ?? '', 160)); ?>">
    <meta name="twitter:image" content="<?php echo SITE_URL . ($articulo['imagen_og'] ?? $articulo['imagen_principal'] ?? '/assets/images/blog/default-article-og.jpg'); ?>">
    
    <!-- ========================================
         FAVICON & TOUCH ICONS
         ======================================== -->
    <link rel="icon" type="image/x-icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo imageUrl('design/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo imageUrl('design/favicon-16x16.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo imageUrl('design/apple-touch-icon.png'); ?>">
    
    <!-- ========================================
         GOOGLE FONTS
         ======================================== -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- ========================================
         BOOTSTRAP 5
         ======================================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- ========================================
         BOOTSTRAP ICONS
         ======================================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- ========================================
         AOS (Animate On Scroll)
         ======================================== -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- ========================================
         CUSTOM CSS
         ======================================== -->
    <link rel="stylesheet" href="<?php echo assetUrl('css/main.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/blog.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/responsive.css'); ?>?v=<?php echo time(); ?>">
    
    <style>
        .article-hero {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            color: white;
            padding: 60px 0 40px;
        }
        .article-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .article-content h1, .article-content h2, .article-content h3 {
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .article-content h2 {
            color: #0066cc;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 0.5rem;
        }
        .article-content blockquote {
            border-left: 4px solid #0066cc;
            padding-left: 1rem;
            margin: 2rem 0;
            font-style: italic;
            background: #f8f9fa;
            padding: 1rem;
        }
        .article-meta {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .related-article {
            transition: transform 0.3s ease;
        }
        .related-article:hover {
            transform: translateY(-5px);
        }
        .comment-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
        }
        .social-share {
            position: sticky;
            top: 100px;
        }
        .social-share .btn {
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="blog-page">
    
    <!-- ========================================
         NAVBAR
         ======================================== -->
    <?php include INCLUDES_PATH . '/navbar.php'; ?>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="bg-light py-3">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo siteUrl('index.php'); ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?php echo siteUrl('blog.php'); ?>">Blog</a></li>
                <?php if ($articulo['categoria_nombre']): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo siteUrl('blog.php?categoria=' . $articulo['categoria_id']); ?>">
                        <?php echo esc($articulo['categoria_nombre']); ?>
                    </a>
                </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo esc(truncateText($articulo['titulo'], 50)); ?></li>
            </ol>
        </div>
    </nav>
    
    <!-- Article Hero -->
    <section class="article-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <?php if ($articulo['categoria_nombre']): ?>
                    <span class="badge bg-light text-primary mb-3" style="font-size: 0.9rem;">
                        <i class="bi bi-<?php echo $articulo['categoria_icono'] ?? 'folder'; ?> me-1"></i>
                        <?php echo esc($articulo['categoria_nombre']); ?>
                    </span>
                    <?php endif; ?>
                    
                    <h1 class="display-5 fw-bold mb-4"><?php echo esc($articulo['titulo']); ?></h1>
                    
                    <div class="d-flex justify-content-center align-items-center text-white-75">
                        <small class="me-4">
                            <i class="bi bi-person me-1"></i>
                            <?php echo esc($articulo['autor']); ?>
                        </small>
                        <small class="me-4">
                            <i class="bi bi-calendar me-1"></i>
                            <?php echo date('d M Y', strtotime($articulo['fecha_publicacion'])); ?>
                        </small>
                        <small>
                            <i class="bi bi-eye me-1"></i>
                            <?php echo $articulo['vistas'] + 1; ?> vistas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Article Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Article Image -->
                    <?php if (!empty($articulo['imagen_principal'])): ?>
                        <?php 
                        $imagen_url = $articulo['imagen_principal'];
                        if (strpos($imagen_url, 'http') !== 0 && strpos($imagen_url, '/') !== 0) {
                            $imagen_url = SITE_URL . '/' . $imagen_url;
                        } elseif (strpos($imagen_url, '/') === 0) {
                            $imagen_url = SITE_URL . $imagen_url;
                        }
                        ?>
                        <img src="<?php echo esc($imagen_url); ?>" 
                             alt="<?php echo esc($articulo['titulo']); ?>" 
                             class="img-fluid rounded mb-4"
                             onerror="this.src='<?php echo SITE_URL; ?>/assets/images/blog/default-article.jpg'">
                    <?php endif; ?>
                    
                    <!-- Article Meta -->
                    <div class="article-meta">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Autor:</strong> <?php echo esc($articulo['autor']); ?><br>
                                <strong>Fecha:</strong> <?php echo date('d M Y', strtotime($articulo['fecha_publicacion'])); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Categoría:</strong> <?php echo esc($articulo['categoria_nombre'] ?? 'Sin categoría'); ?><br>
                                <strong>Vistas:</strong> <?php echo $articulo['vistas'] + 1; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article Content -->
                    <div class="article-content">
                        <?php echo $articulo['contenido']; ?>
                    </div>
                    
                    <!-- Tags -->
                    <?php if (!empty($articulo['tags'])): ?>
                    <div class="mt-4">
                        <h6>Etiquetas:</h6>
                        <?php 
                        $tags = json_decode($articulo['tags'], true);
                        if (is_array($tags)):
                        ?>
                            <?php foreach ($tags as $tag): ?>
                            <span class="badge bg-secondary me-2"><?php echo esc($tag); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Social Share -->
                    <div class="mt-4 pt-4 border-top">
                        <h6>Compartir:</h6>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(siteUrl('blog-detalle.php?slug=' . $articulo['slug'])); ?>" 
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-facebook me-1"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(siteUrl('blog-detalle.php?slug=' . $articulo['slug'])); ?>&text=<?php echo urlencode($articulo['titulo']); ?>" 
                               target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-twitter me-1"></i>Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(siteUrl('blog-detalle.php?slug=' . $articulo['slug'])); ?>" 
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-linkedin me-1"></i>LinkedIn
                            </a>
                            <button onclick="navigator.clipboard.writeText('<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>')" 
                                    class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-link-45deg me-1"></i>Copiar enlace
                            </button>
                        </div>
                    </div>
                    
                    <!-- Comments Section -->
                    <div class="mt-5">
                        <h4>Comentarios (<?php echo count($comentarios); ?>)</h4>
                        
                        <?php if (!empty($comentarios)): ?>
                            <?php foreach ($comentarios as $comentario): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0"><?php echo esc($comentario['nombre']); ?></h6>
                                        <small class="text-muted"><?php echo date('d M Y', strtotime($comentario['created_at'])); ?></small>
                                    </div>
                                    <p class="card-text"><?php echo nl2br(esc($comentario['comentario'])); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                        <?php endif; ?>
                        
                        <!-- Comment Form -->
                        <div class="comment-form">
                            <h5>Dejar un comentario</h5>
                            
                            <?php if ($mensaje_comentario): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                                <?php echo esc($mensaje_comentario); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST">
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
                                <button type="submit" name="enviar_comentario" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>Enviar comentario
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Social Share -->
                    <div class="social-share">
                        <h6>Compartir</h6>
                        <div class="d-grid gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(siteUrl('blog-detalle.php?slug=' . $articulo['slug'])); ?>" 
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-facebook me-1"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(siteUrl('blog-detalle.php?slug=' . $articulo['slug'])); ?>&text=<?php echo urlencode($articulo['titulo']); ?>" 
                               target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-twitter me-1"></i>Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(siteUrl('blog-detalle.php?slug=' . $articulo['slug'])); ?>" 
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-linkedin me-1"></i>LinkedIn
                            </a>
                        </div>
                    </div>
                    
                    <!-- Related Articles -->
                    <?php if (!empty($articulos_relacionados)): ?>
                    <div class="mt-4">
                        <h6>Artículos relacionados</h6>
                        <?php foreach ($articulos_relacionados as $relacionado): ?>
                        <div class="card related-article mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $relacionado['slug']); ?>" 
                                       class="text-decoration-none">
                                        <?php echo esc(truncateText($relacionado['titulo'], 60)); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php echo date('d M Y', strtotime($relacionado['fecha_publicacion'])); ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         FOOTER
         ======================================== -->
    <?php include INCLUDES_PATH . '/footer.php'; ?>
    
    <!-- ========================================
         JAVASCRIPT LIBRARIES
         ======================================== -->
    
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo assetUrl('js/main.js'); ?>?v=<?php echo time(); ?>"></script>
    <script src="<?php echo assetUrl('js/blog.js'); ?>?v=<?php echo time(); ?>"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 600,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    
</body>
</html>