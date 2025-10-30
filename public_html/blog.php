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

// Variables para meta tags
$pageTitle = 'Blog - ' . SITE_NAME;
$pageDescription = 'Blog de Aramed y Laboratorios - Artículos sobre simulación médica, educación en salud y tecnología innovadora para profesionales de la salud.';
$pageKeywords = 'blog, simulación médica, educación en salud, tecnología médica, artículos, noticias, Aramed';
$pageUrl = SITE_URL . '/blog.php';
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
    
    <?php // Google Analytics ?>
    <?php include INCLUDES_PATH . '/analytics.php'; ?>
    
    <!-- ========================================
         OPEN GRAPH
         ======================================== -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo esc(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo esc($pageTitle); ?>">
    <meta property="og:description" content="<?php echo esc($pageDescription); ?>">
    <meta property="og:url" content="<?php echo esc($pageUrl); ?>">
    <meta property="og:image" content="<?php echo imageUrl('design/logo-og.jpg'); ?>">
    <meta property="og:locale" content="es_MX">
    
    <!-- ========================================
         TWITTER CARD
         ======================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo esc($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo imageUrl('design/logo-og.jpg'); ?>">
    
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
        .blog-hero {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            color: white;
            padding: 80px 0;
        }
        .article-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .article-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .category-badge {
            background: var(--aramed-primary);
            color: white;
        }
    </style>
</head>

<body class="blog-page">
    
    <!-- ========================================
         NAVBAR
         ======================================== -->
    <?php include INCLUDES_PATH . '/navbar.php'; ?>
    

    
    <!-- Blog Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Filtros</h5>
                        </div>
                        <div class="card-body">
                            <!-- Búsqueda -->
                            <form method="GET" class="mb-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="busqueda" 
                                           value="<?php echo esc($busqueda); ?>" 
                                           placeholder="Buscar artículos...">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Categorías -->
                            <h6>Categorías</h6>
                            <div class="list-group list-group-flush">
                                <a href="?categoria=0" class="list-group-item list-group-item-action <?php echo $categoria_id == 0 ? 'active' : ''; ?>">
                                    Todas (<?php echo $total_articulos; ?>)
                                </a>
                                <?php foreach ($categorias as $categoria): ?>
                                <a href="?categoria=<?php echo $categoria['id']; ?>" 
                                   class="list-group-item list-group-item-action <?php echo $categoria_id == $categoria['id'] ? 'active' : ''; ?>">
                                    <?php echo esc($categoria['nombre']); ?> (<?php echo $categoria['articulos_count']; ?>)
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Artículos -->
                <div class="col-lg-9">
                    <?php if (!empty($articulos)): ?>
                        <div class="row">
                            <?php foreach ($articulos as $articulo): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card article-card h-100">
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
                                             class="article-image card-img-top"
                                             onerror="this.src='<?php echo SITE_URL; ?>/assets/images/blog/default-article.jpg'">
                                    <?php else: ?>
                                        <img src="<?php echo SITE_URL; ?>/assets/images/blog/default-article.jpg" 
                                             alt="<?php echo esc($articulo['titulo']); ?>" 
                                             class="article-image card-img-top">
                                    <?php endif; ?>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-2">
                                            <?php if ($articulo['categoria_nombre']): ?>
                                            <span class="badge category-badge"><?php echo esc($articulo['categoria_nombre']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h5 class="card-title">
                                            <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>" 
                                               class="text-decoration-none">
                                                <?php echo esc($articulo['titulo']); ?>
                                            </a>
                                        </h5>
                                        
                                        <p class="card-text text-muted flex-grow-1">
                                            <?php echo esc(truncateText($articulo['resumen'] ?? '', 120)); ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?php echo date('d M Y', strtotime($articulo['fecha_publicacion'])); ?>
                                            </small>
                                            <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $articulo['slug']); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                Leer más
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Paginación -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Paginación del blog">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&categoria=<?php echo $categoria_id; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&categoria=<?php echo $categoria_id; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&categoria=<?php echo $categoria_id; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-newspaper display-1 text-muted mb-3"></i>
                            <h3>No se encontraron artículos</h3>
                            <p class="text-muted">No hay artículos disponibles con los filtros seleccionados.</p>
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