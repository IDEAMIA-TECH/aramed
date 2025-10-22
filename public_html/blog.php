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
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/connection.php';

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

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

// Función para truncar texto
function truncateText($texto, $limite = 150) {
    if (strlen($texto) <= $limite) {
        return $texto;
    }
    return substr($texto, 0, $limite) . '...';
}

// Función para formatear fecha
function formatDate($fecha) {
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    $fecha_obj = new DateTime($fecha);
    $dia = $fecha_obj->format('d');
    $mes = $meses[(int)$fecha_obj->format('n')];
    $año = $fecha_obj->format('Y');
    
    return "$dia de $mes de $año";
}
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Descubre las últimas noticias, artículos y tendencias en simulación médica, educación en salud y tecnología innovadora con Aramed y Laboratorios.">
    <meta name="keywords" content="blog, simulación médica, educación en salud, tecnología médica, Aramed, laboratorios">
    
    <!-- Open Graph -->
    <meta property="og:title" content="Blog - <?php echo SITE_NAME; ?>">
    <meta property="og:description" content="Descubre las últimas noticias, artículos y tendencias en simulación médica, educación en salud y tecnología innovadora.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>/blog.php">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/design/logo-og.jpg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Blog - <?php echo SITE_NAME; ?>">
    <meta name="twitter:description" content="Descubre las últimas noticias, artículos y tendencias en simulación médica, educación en salud y tecnología innovadora.">
    <meta name="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/design/logo-og.jpg">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    <link rel="icon" href="<?php echo imageUrl('design/favicon.ico'); ?>" type="image/x-icon">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/landing.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/blog.css" rel="stylesheet">
    
    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Blog",
        "name": "Blog de <?php echo SITE_NAME; ?>",
        "description": "Blog especializado en simulación médica, educación en salud y tecnología innovadora",
        "url": "<?php echo SITE_URL; ?>/blog.php",
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo SITE_NAME; ?>",
            "url": "<?php echo SITE_URL; ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo SITE_URL; ?>/assets/images/design/logo.png"
            }
        }
    }
    </script>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="blog-hero py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold text-primary mb-3">
                        <i class="bi bi-newspaper me-3"></i>Blog Aramed
                    </h1>
                    <p class="lead text-muted mb-4">
                        Descubre las últimas noticias, artículos y tendencias en simulación médica, 
                        educación en salud y tecnología innovadora.
                    </p>
                    
                    <!-- Buscador -->
                    <form method="GET" class="search-form">
                        <div class="input-group input-group-lg">
                            <input type="text" 
                                   class="form-control" 
                                   name="busqueda" 
                                   placeholder="Buscar artículos..." 
                                   value="<?php echo esc($busqueda); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="<?php echo imageUrl('design/blog-hero.svg'); ?>" 
                         alt="Blog Aramed" 
                         class="img-fluid" 
                         style="max-height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Filtros -->
    <section class="blog-filters py-4 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filtrar por categoría:
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="blog.php" 
                           class="btn btn-outline-primary <?php echo $categoria_id == 0 ? 'active' : ''; ?>">
                            Todas (<?php echo $total_articulos; ?>)
                        </a>
                        <?php foreach ($categorias as $categoria): ?>
                        <a href="blog.php?categoria=<?php echo $categoria['id']; ?>" 
                           class="btn btn-outline-primary <?php echo $categoria_id == $categoria['id'] ? 'active' : ''; ?>">
                            <i class="bi bi-<?php echo $categoria['icono']; ?> me-1"></i>
                            <?php echo esc($categoria['nombre']); ?> (<?php echo $categoria['articulos_count']; ?>)
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Artículos Destacados -->
    <?php if (!empty($articulos_destacados) && $page == 1): ?>
    <section class="blog-destacados py-5">
        <div class="container">
            <h2 class="text-center mb-5">
                <i class="bi bi-star-fill text-warning me-2"></i>Artículos Destacados
            </h2>
            <div class="row">
                <?php foreach ($articulos_destacados as $index => $articulo): ?>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <article class="card h-100 shadow-sm border-0">
                        <?php if (!empty($articulo['imagen_principal'])): ?>
                        <div class="card-img-top" style="height: 200px; overflow: hidden;">
                            <img src="<?php echo SITE_URL . $articulo['imagen_principal']; ?>" 
                                 alt="<?php echo esc($articulo['titulo']); ?>" 
                                 class="w-100 h-100" 
                                 style="object-fit: cover;">
                        </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <?php if ($articulo['categoria_nombre']): ?>
                                <span class="badge rounded-pill" 
                                      style="background-color: <?php echo $articulo['categoria_color']; ?>;">
                                    <i class="bi bi-<?php echo $articulo['categoria_icono']; ?> me-1"></i>
                                    <?php echo esc($articulo['categoria_nombre']); ?>
                                </span>
                                <?php endif; ?>
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="bi bi-star-fill me-1"></i>Destacado
                                </span>
                            </div>
                            <h3 class="card-title h5">
                                <a href="blog-detalle.php?slug=<?php echo $articulo['slug']; ?>" 
                                   class="text-decoration-none">
                                    <?php echo esc($articulo['titulo']); ?>
                                </a>
                            </h3>
                            <p class="card-text text-muted flex-grow-1">
                                <?php echo esc(truncateText($articulo['resumen'], 120)); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php echo formatDate($articulo['fecha_publicacion']); ?>
                                </small>
                                <a href="blog-detalle.php?slug=<?php echo $articulo['slug']; ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    Leer más <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Lista de Artículos -->
    <section class="blog-articulos py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php if (!empty($articulos)): ?>
                        <div class="row">
                            <?php foreach ($articulos as $index => $articulo): ?>
                            <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                                <article class="card h-100 shadow-sm border-0">
                                    <?php if (!empty($articulo['imagen_principal'])): ?>
                                    <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                        <img src="<?php echo SITE_URL . $articulo['imagen_principal']; ?>" 
                                             alt="<?php echo esc($articulo['titulo']); ?>" 
                                             class="w-100 h-100" 
                                             style="object-fit: cover;">
                                    </div>
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-2">
                                            <?php if ($articulo['categoria_nombre']): ?>
                                            <span class="badge rounded-pill" 
                                                  style="background-color: <?php echo $articulo['categoria_color']; ?>;">
                                                <i class="bi bi-<?php echo $articulo['categoria_icono']; ?> me-1"></i>
                                                <?php echo esc($articulo['categoria_nombre']); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="card-title h5">
                                            <a href="blog-detalle.php?slug=<?php echo $articulo['slug']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo esc($articulo['titulo']); ?>
                                            </a>
                                        </h3>
                                        <p class="card-text text-muted flex-grow-1">
                                            <?php echo esc(truncateText($articulo['resumen'], 120)); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?php echo formatDate($articulo['fecha_publicacion']); ?>
                                            </small>
                                            <a href="blog-detalle.php?slug=<?php echo $articulo['slug']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
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
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $categoria_id ? '&categoria=' . $categoria_id : ''; ?><?php echo $busqueda ? '&busqueda=' . urlencode($busqueda) : ''; ?>">
                                        <i class="bi bi-chevron-left"></i> Anterior
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $categoria_id ? '&categoria=' . $categoria_id : ''; ?><?php echo $busqueda ? '&busqueda=' . urlencode($busqueda) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $categoria_id ? '&categoria=' . $categoria_id : ''; ?><?php echo $busqueda ? '&busqueda=' . urlencode($busqueda) : ''; ?>">
                                        Siguiente <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Sin artículos -->
                        <div class="text-center py-5">
                            <i class="bi bi-newspaper display-1 text-muted mb-3"></i>
                            <h3 class="text-muted">No se encontraron artículos</h3>
                            <p class="text-muted">
                                <?php if ($categoria_id > 0 || !empty($busqueda)): ?>
                                    No hay artículos que coincidan con los filtros aplicados.
                                    <a href="blog.php" class="btn btn-outline-primary ms-2">Ver todos los artículos</a>
                                <?php else: ?>
                                    Aún no hay artículos publicados. ¡Vuelve pronto!
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 2rem;">
                        <!-- Categorías -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-folder me-2"></i>Categorías
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <a href="blog.php" 
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $categoria_id == 0 ? 'active' : ''; ?>">
                                        Todas
                                        <span class="badge bg-primary rounded-pill"><?php echo $total_articulos; ?></span>
                                    </a>
                                    <?php foreach ($categorias as $categoria): ?>
                                    <a href="blog.php?categoria=<?php echo $categoria['id']; ?>" 
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $categoria_id == $categoria['id'] ? 'active' : ''; ?>">
                                        <span>
                                            <i class="bi bi-<?php echo $categoria['icono']; ?> me-2"></i>
                                            <?php echo esc($categoria['nombre']); ?>
                                        </span>
                                        <span class="badge bg-primary rounded-pill"><?php echo $categoria['articulos_count']; ?></span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Newsletter -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-envelope me-2"></i>Newsletter
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Mantente al día con las últimas noticias y artículos.</p>
                                <form action="includes/newsletter_simple_handler.php" method="POST" class="newsletter-form">
                                    <div class="mb-3">
                                        <input type="email" 
                                               class="form-control" 
                                               name="email" 
                                               placeholder="Tu correo electrónico" 
                                               required>
                                        <input type="hidden" name="source" value="blog">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-send me-2"></i>Suscribirse
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        // Inicializar AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Manejar formulario de newsletter
        document.querySelectorAll('.newsletter-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const button = this.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;
                
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Suscribiendo...';
                button.disabled = true;
                
                fetch('includes/newsletter_simple_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.innerHTML = '<i class="bi bi-check me-2"></i>¡Suscrito!';
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-success');
                        this.reset();
                    } else {
                        button.innerHTML = originalText;
                        button.disabled = false;
                        alert(data.message);
                    }
                })
                .catch(error => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    alert('Error de conexión. Por favor, intenta de nuevo.');
                });
            });
        });
    </script>
</body>
</html>