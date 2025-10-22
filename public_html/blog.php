<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Blog
 * ========================================
 * 
 * Página principal del blog con listado paginado y buscador
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

// Obtener parámetros de filtro
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Construir consulta de artículos
$where_conditions = ['a.estado = "publicado"'];
$params = [];

if ($categoria_id > 0) {
    $where_conditions[] = 'a.categoria_id = ?';
    $params[] = $categoria_id;
}

if (!empty($busqueda)) {
    $where_conditions[] = '(a.titulo LIKE ? OR a.resumen LIKE ? OR a.contenido LIKE ?)';
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = implode(' AND ', $where_conditions);

// Obtener artículos
$sql_articulos = "
    SELECT a.*, c.nombre as categoria_nombre, c.slug as categoria_slug, c.color as categoria_color, c.icono as categoria_icono
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    WHERE {$where_clause}
    ORDER BY a.destacado DESC, a.fecha_publicacion DESC
    LIMIT {$per_page} OFFSET {$offset}
";

$stmt_articulos = $pdo->prepare($sql_articulos);
$stmt_articulos->execute($params);
$articulos = $stmt_articulos->fetchAll(PDO::FETCH_ASSOC);

// Contar total de artículos para paginación
$sql_count = "
    SELECT COUNT(*) as total
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    WHERE {$where_clause}
";

$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_articulos = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_articulos / $per_page);

// Obtener categorías para filtros
$sql_categorias = "
    SELECT c.*, COUNT(a.id) as articulos_count
    FROM blog_categorias c
    LEFT JOIN blog_articulos a ON c.id = a.categoria_id AND a.estado = 'publicado'
    WHERE c.estado = 'activo'
    GROUP BY c.id
    HAVING articulos_count > 0
    ORDER BY c.nombre ASC
";

$stmt_categorias = $pdo->prepare($sql_categorias);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

// Obtener artículos destacados
$sql_destacados = "
    SELECT a.*, c.nombre as categoria_nombre, c.slug as categoria_slug, c.color as categoria_color
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    WHERE a.estado = 'publicado' AND a.destacado = 1
    ORDER BY a.fecha_publicacion DESC
    LIMIT 3
";

$stmt_destacados = $pdo->prepare($sql_destacados);
$stmt_destacados->execute();
$articulos_destacados = $stmt_destacados->fetchAll(PDO::FETCH_ASSOC);

// Variables para meta tags
$pageTitle = 'Blog - ' . SITE_NAME;
$pageDescription = 'Descubre las últimas noticias, artículos y casos de éxito sobre simulación médica, tecnología en salud y educación médica. Mantente actualizado con Aramed y Laboratorios.';
$pageKeywords = 'blog, simulación médica, educación médica, tecnología, noticias, casos de éxito, Anatomage, Gaumard';
$pageUrl = SITE_URL . '/blog.php';
$pageImage = imageUrl('design/logo-og.jpg');

// Función para generar slug de URL
function generateBlogSlug($titulo) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
}

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

// Función para truncar texto
function truncateText($texto, $limite = 150) {
    if (strlen($texto) <= $limite) {
        return $texto;
    }
    return substr($texto, 0, $limite) . '...';
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
    <title><?php echo esc($pageTitle); ?></title>
    <meta name="description" content="<?php echo esc($pageDescription); ?>">
    <meta name="keywords" content="<?php echo esc($pageKeywords); ?>">
    <meta name="author" content="<?php echo esc(SITE_NAME); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo esc($pageUrl); ?>">
    
    <!-- ========================================
         OPEN GRAPH (Facebook, LinkedIn)
         ======================================== -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo esc(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo esc($pageTitle); ?>">
    <meta property="og:description" content="<?php echo esc($pageDescription); ?>">
    <meta property="og:url" content="<?php echo esc($pageUrl); ?>">
    <meta property="og:image" content="<?php echo esc($pageImage); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="es_MX">
    
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
        "@type": "Blog",
        "name": "<?php echo esc(SITE_NAME); ?> - Blog",
        "description": "<?php echo esc($pageDescription); ?>",
        "url": "<?php echo esc($pageUrl); ?>",
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo esc(SITE_NAME); ?>",
            "url": "<?php echo esc(SITE_URL); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo imageUrl('design/logo.png'); ?>"
            }
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
         HERO SECTION
         ======================================== -->
    <section class="blog-hero py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold text-primary mb-3" data-aos="fade-up">
                        <i class="bi bi-newspaper me-3"></i>Blog
                    </h1>
                    <p class="lead text-muted mb-4" data-aos="fade-up" data-aos-delay="100">
                        Descubre las últimas noticias, artículos y casos de éxito sobre simulación médica, 
                        tecnología en salud y educación médica.
                    </p>
                    <div class="d-flex flex-wrap gap-2" data-aos="fade-up" data-aos-delay="200">
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="bi bi-cpu me-1"></i>Simulación Médica
                        </span>
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="bi bi-book me-1"></i>Educación
                        </span>
                        <span class="badge bg-info fs-6 px-3 py-2">
                            <i class="bi bi-gear me-1"></i>Tecnología
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <img src="<?php echo imageUrl('design/blog-hero.png'); ?>" 
                         alt="Blog Aramed" 
                         class="img-fluid"
                         style="max-height: 300px;"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22300%22%3E%3Crect width=%22300%22 height=%22300%22 fill=%22%23f8f9fa%22/%3E%3Ctext x=%22150%22 y=%22150%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2224%22 fill=%22%230066CC%22%3EBlog%3C/text%3E%3C/svg%3E';">
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
         BÚSQUEDA Y FILTROS
         ======================================== -->
    <section class="blog-filters py-4 bg-white border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <form method="GET" class="d-flex gap-3">
                        <div class="flex-grow-1">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       name="busqueda" 
                                       value="<?php echo esc($busqueda); ?>"
                                       placeholder="Buscar artículos...">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-search me-2"></i>Buscar
                        </button>
                        <?php if (!empty($busqueda) || $categoria_id > 0): ?>
                        <a href="<?php echo siteUrl('blog.php'); ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle me-2"></i>Limpiar
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-lg-4">
                    <form method="GET" class="d-flex gap-2">
                        <select name="categoria" class="form-select form-select-lg">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo esc($cat['nombre']); ?> (<?php echo $cat['articulos_count']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-funnel"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
         ARTÍCULOS DESTACADOS
         ======================================== -->
    <?php if (!empty($articulos_destacados) && $page == 1): ?>
    <section class="blog-destacados py-5 bg-primary text-white">
        <div class="container">
            <h2 class="h3 fw-bold mb-4 text-center" data-aos="fade-up">
                <i class="bi bi-star-fill me-2"></i>Artículos Destacados
            </h2>
            <div class="row g-4">
                <?php foreach ($articulos_destacados as $index => $articulo): ?>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="card h-100 border-0 shadow-lg">
                        <div class="position-relative">
                            <img src="<?php echo !empty($articulo['imagen_principal']) ? SITE_URL . $articulo['imagen_principal'] : imageUrl('design/placeholder-blog.jpg'); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo esc($articulo['titulo']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                    <i class="bi bi-star-fill me-1"></i>Destacado
                                </span>
                            </div>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge rounded-pill px-3 py-2" 
                                      style="background-color: <?php echo $articulo['categoria_color']; ?>;">
                                    <i class="<?php echo $articulo['categoria_icono']; ?> me-1"></i>
                                    <?php echo esc($articulo['categoria_nombre']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">
                                <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo esc($articulo['titulo']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted mb-3">
                                <?php echo esc(truncateText($articulo['resumen'], 120)); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?php echo formatBlogDate($articulo['fecha_publicacion']); ?>
                                </small>
                                <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>" 
                                   class="btn btn-primary btn-sm">
                                    Leer más <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         LISTADO DE ARTÍCULOS
         ======================================== -->
    <section class="blog-content py-5">
        <div class="container">
            <div class="row">
                <!-- Barra lateral con categorías -->
                <div class="col-lg-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-folder me-2"></i>Categorías
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <a href="<?php echo siteUrl('blog.php'); ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $categoria_id == 0 ? 'active' : ''; ?>">
                                    <span>
                                        <i class="bi bi-grid-3x3-gap me-2"></i>Todas las categorías
                                    </span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $total_articulos; ?></span>
                                </a>
                                <?php foreach ($categorias as $cat): ?>
                                <a href="<?php echo siteUrl('blog.php?categoria=' . $cat['id']); ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $categoria_id == $cat['id'] ? 'active' : ''; ?>">
                                    <span>
                                        <i class="<?php echo $cat['icono']; ?> me-2" style="color: <?php echo $cat['color']; ?>;"></i>
                                        <?php echo esc($cat['nombre']); ?>
                                    </span>
                                    <span class="badge rounded-pill" style="background-color: <?php echo $cat['color']; ?>;">
                                        <?php echo $cat['articulos_count']; ?>
                                    </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de artículos -->
                <div class="col-lg-9">
                    <?php if (!empty($articulos)): ?>
                        <!-- Resultados -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="h5 mb-0">
                                <?php if (!empty($busqueda)): ?>
                                    Resultados para "<?php echo esc($busqueda); ?>" (<?php echo $total_articulos; ?> artículos)
                                <?php elseif ($categoria_id > 0): ?>
                                    <?php 
                                    $categoria_actual = array_filter($categorias, function($cat) use ($categoria_id) {
                                        return $cat['id'] == $categoria_id;
                                    });
                                    $categoria_actual = reset($categoria_actual);
                                    ?>
                                    <?php echo esc($categoria_actual['nombre']); ?> (<?php echo $total_articulos; ?> artículos)
                                <?php else: ?>
                                    Todos los artículos (<?php echo $total_articulos; ?>)
                                <?php endif; ?>
                            </h3>
                        </div>

                        <!-- Grid de artículos -->
                        <div class="row g-4">
                            <?php foreach ($articulos as $index => $articulo): ?>
                            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                                <article class="card h-100 border-0 shadow-sm blog-article-card">
                                    <div class="position-relative">
                                        <img src="<?php echo !empty($articulo['imagen_principal']) ? SITE_URL . $articulo['imagen_principal'] : imageUrl('design/placeholder-blog.jpg'); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo esc($articulo['titulo']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                        <?php if ($articulo['destacado']): ?>
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-star-fill me-1"></i>Destacado
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge rounded-pill px-3 py-2" 
                                                  style="background-color: <?php echo $articulo['categoria_color']; ?>;">
                                                <i class="<?php echo $articulo['categoria_icono']; ?> me-1"></i>
                                                <?php echo esc($articulo['categoria_nombre']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold mb-3">
                                            <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>" 
                                               class="text-decoration-none text-dark">
                                                <?php echo esc($articulo['titulo']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted mb-3 flex-grow-1">
                                            <?php echo esc(truncateText($articulo['resumen'], 120)); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?php echo formatBlogDate($articulo['fecha_publicacion']); ?>
                                            </small>
                                            <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>" 
                                               class="btn btn-primary btn-sm">
                                                Leer más <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Paginación -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Paginación del blog" class="mt-5">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo siteUrl('blog.php?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))); ?>">
                                        <i class="bi bi-chevron-left"></i> Anterior
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo siteUrl('blog.php?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo siteUrl('blog.php?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))); ?>">
                                        Siguiente <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Sin resultados -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-search display-1 text-muted"></i>
                            </div>
                            <h3 class="h4 text-muted mb-3">No se encontraron artículos</h3>
                            <p class="text-muted mb-4">
                                <?php if (!empty($busqueda)): ?>
                                    No hay artículos que coincidan con "<?php echo esc($busqueda); ?>"
                                <?php elseif ($categoria_id > 0): ?>
                                    No hay artículos en esta categoría
                                <?php else: ?>
                                    Aún no hay artículos publicados
                                <?php endif; ?>
                            </p>
                            <a href="<?php echo siteUrl('blog.php'); ?>" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-2"></i>Ver todos los artículos
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
         NEWSLETTER
         ======================================== -->
    <section class="newsletter-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="h3 fw-bold mb-3" data-aos="fade-up">
                        <i class="bi bi-envelope-heart me-2"></i>Mantente Informado
                    </h2>
                    <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">
                        Suscríbete a nuestro boletín y recibe las últimas noticias, 
                        artículos y actualizaciones sobre simulación médica.
                    </p>
                    <form class="newsletter-form d-flex gap-3 justify-content-center" 
                          action="includes/newsletter_handler.php" method="POST" 
                          data-aos="fade-up" data-aos-delay="200">
                        <div class="flex-grow-1" style="max-width: 400px;">
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   name="email_oficial"
                                   placeholder="Tu correo electrónico" 
                                   required>
                        </div>
                        <button type="submit" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-send-fill me-2"></i>Suscribirse
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

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
    </script>
</body>
</html>
