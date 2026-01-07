<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Catálogo de Productos
 * ========================================
 * 
 * Página del catálogo de productos con filtros
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

// Variables para meta tags
$pageTitle = 'Catálogo de Productos - ' . SITE_NAME;
$pageDescription = 'Explora nuestro catálogo completo de simuladores médicos y equipos de simulación para educación en salud. Filtra por marca, categoría y encuentra el producto perfecto para tu institución.';
$pageKeywords = 'catálogo, simuladores médicos, equipos simulación, educación médica, maniquíes, anatomage, gaumard, kyoto kagaku';
$pageUrl = SITE_URL . '/catalogo.php';

// Obtener parámetros de filtro
$marca_id = isset($_GET['marca']) ? (int)$_GET['marca'] : 0;
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Construir consulta de productos
$where_conditions = ['p.estado = "activo"'];
$params = [];

if ($marca_id > 0) {
    $where_conditions[] = 'p.marca_id = ?';
    $params[] = $marca_id;
}

if ($categoria_id > 0) {
    $where_conditions[] = 'p.categoria_id = ?';
    $params[] = $categoria_id;
}

if (!empty($busqueda)) {
    $where_conditions[] = '(p.nombre LIKE ? OR p.descripcion_corta LIKE ? OR p.descripcion_larga LIKE ?)';
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = implode(' AND ', $where_conditions);

// Obtener productos
try {
    $pdo = getDB();
    
    // Contar total de productos
    $count_sql = "
        SELECT COUNT(*) as total 
        FROM catalogo_productos p 
        WHERE {$where_clause}
    ";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_products = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_products / $per_page);
    
    // Obtener productos con paginación e imágenes
    $products_sql = "
        SELECT p.*, m.nombre as marca_nombre, c.nombre as categoria_nombre, i.imagen_url
        FROM catalogo_productos p
        LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
        LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
        LEFT JOIN catalogo_producto_imagenes i ON p.id = i.producto_id AND i.es_principal = 1
        WHERE {$where_clause}
        ORDER BY p.destacado DESC, p.nombre ASC
        LIMIT {$per_page} OFFSET {$offset}
    ";
    $products_stmt = $pdo->prepare($products_sql);
    $products_stmt->execute($params);
    $products = $products_stmt->fetchAll();
    
    // Obtener marcas para filtro (solo las que tienen productos con filtros aplicados)
    $marcas_where = ['m.estado = "activo"', 'p.estado = "activo"'];
    $marcas_params = [];
    
    // Aplicar filtros contextuales para marcas
    if ($categoria_id > 0) {
        $marcas_where[] = 'p.categoria_id = ?';
        $marcas_params[] = $categoria_id;
    }
    
    if (!empty($busqueda)) {
        $marcas_where[] = '(p.nombre LIKE ? OR p.descripcion_corta LIKE ? OR p.descripcion_larga LIKE ?)';
        $search_term = "%{$busqueda}%";
        $marcas_params[] = $search_term;
        $marcas_params[] = $search_term;
        $marcas_params[] = $search_term;
    }
    
    $marcas_where_clause = implode(' AND ', $marcas_where);
    $marcas_sql = "
        SELECT DISTINCT m.*, COUNT(p.id) as productos_count
        FROM catalogo_marcas m
        INNER JOIN catalogo_productos p ON m.id = p.marca_id
        WHERE {$marcas_where_clause}
        GROUP BY m.id
        HAVING productos_count > 0
        ORDER BY m.nombre ASC
    ";
    $marcas_stmt = $pdo->prepare($marcas_sql);
    $marcas_stmt->execute($marcas_params);
    $marcas = $marcas_stmt->fetchAll();
    
    // Obtener categorías para filtro (solo las que tienen productos con filtros aplicados)
    $categorias_where = ['c.estado = "activo"', 'p.estado = "activo"'];
    $categorias_params = [];
    
    // Aplicar filtros contextuales para categorías
    if ($marca_id > 0) {
        $categorias_where[] = 'p.marca_id = ?';
        $categorias_params[] = $marca_id;
    }
    
    if (!empty($busqueda)) {
        $categorias_where[] = '(p.nombre LIKE ? OR p.descripcion_corta LIKE ? OR p.descripcion_larga LIKE ?)';
        $search_term = "%{$busqueda}%";
        $categorias_params[] = $search_term;
        $categorias_params[] = $search_term;
        $categorias_params[] = $search_term;
    }
    
    $categorias_where_clause = implode(' AND ', $categorias_where);
    $categorias_sql = "
        SELECT DISTINCT c.*, COUNT(p.id) as productos_count
        FROM catalogo_categorias c
        INNER JOIN catalogo_productos p ON c.id = p.categoria_id
        WHERE {$categorias_where_clause}
        GROUP BY c.id
        HAVING productos_count > 0
        ORDER BY c.nombre ASC
    ";
    $categorias_stmt = $pdo->prepare($categorias_sql);
    $categorias_stmt->execute($categorias_params);
    $categorias = $categorias_stmt->fetchAll();
    
    // Obtener contadores totales para mostrar cuando no hay filtros
    $total_marcas_sql = "
        SELECT DISTINCT m.*, COUNT(p.id) as productos_count
        FROM catalogo_marcas m
        INNER JOIN catalogo_productos p ON m.id = p.marca_id
        WHERE m.estado = 'activo' AND p.estado = 'activo'
        GROUP BY m.id
        HAVING productos_count > 0
        ORDER BY m.nombre ASC
    ";
    $total_marcas_stmt = $pdo->query($total_marcas_sql);
    $total_marcas = $total_marcas_stmt->fetchAll();
    
    $total_categorias_sql = "
        SELECT DISTINCT c.*, COUNT(p.id) as productos_count
        FROM catalogo_categorias c
        INNER JOIN catalogo_productos p ON c.id = p.categoria_id
        WHERE c.estado = 'activo' AND p.estado = 'activo'
        GROUP BY c.id
        HAVING productos_count > 0
        ORDER BY c.nombre ASC
    ";
    $total_categorias_stmt = $pdo->query($total_categorias_sql);
    $total_categorias = $total_categorias_stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Error en catálogo: " . $e->getMessage());
    $products = [];
    $marcas = [];
    $categorias = [];
    $total_products = 0;
    $total_pages = 0;
}

// Función para generar URL de filtros
function buildFilterUrl($params = []) {
    $current_params = $_GET;
    foreach ($params as $key => $value) {
        if ($value === '') {
            unset($current_params[$key]);
        } else {
            $current_params[$key] = $value;
        }
    }
    return '?' . http_build_query($current_params);
}

?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <?php // Google Analytics ?>
    <?php include INCLUDES_PATH . '/analytics.php'; ?>
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
         SCHEMA.ORG JSON-LD
         ======================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Catálogo de Productos Médicos y Simuladores",
        "description": "<?php echo esc($pageDescription); ?>",
        "url": "<?php echo esc($pageUrl); ?>",
        "mainEntity": {
            "@type": "ItemList",
            "name": "Productos Médicos y Simuladores",
            "description": "Lista completa de productos médicos, simuladores y equipos de simulación",
            "numberOfItems": "400+",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "item": {
                        "@type": "Product",
                        "name": "Simuladores de Alta Fidelidad",
                        "description": "Simuladores médicos de última generación para entrenamiento médico avanzado",
                        "category": "Simuladores Médicos",
                        "brand": "Gaumard",
                        "offers": {
                            "@type": "Offer",
                            "availability": "https://schema.org/InStock"
                        }
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "item": {
                        "@type": "Product",
                        "name": "Anatomage Table",
                        "description": "Mesa de anatomía virtual 3D para enseñanza médica",
                        "category": "Tecnología Médica",
                        "brand": "Anatomage"
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "item": {
                        "@type": "Product",
                        "name": "Simuladores Neonatales",
                        "description": "Simuladores especializados para entrenamiento neonatal y pediátrico",
                        "category": "Simuladores Especializados",
                        "brand": "Gaumard"
                    }
                }
            ]
        },
        "breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Inicio",
                    "item": "<?php echo siteUrl(); ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Catálogo",
                    "item": "<?php echo siteUrl('/catalogo.php'); ?>"
                }
            ]
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo esc(SITE_NAME); ?>",
            "url": "<?php echo siteUrl(); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo imageUrl('design/logo.png'); ?>"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "<?php echo esc(CONTACT_PHONE); ?>",
                "contactType": "customer service",
                "availableLanguage": "Spanish"
            }
        }
    }
    </script>
    
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
    <link rel="stylesheet" href="<?php echo assetUrl('css/catalogo.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/responsive.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- ========================================
         STRUCTURED DATA
         ======================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Catálogo de Simuladores Médicos",
        "description": "<?php echo esc($pageDescription); ?>",
        "url": "<?php echo esc($pageUrl); ?>",
        "mainEntity": {
            "@type": "ItemList",
            "numberOfItems": <?php echo $total_products; ?>,
            "itemListElement": [
                <?php foreach ($products as $index => $product): ?>
                {
                    "@type": "Product",
                    "position": <?php echo $index + 1; ?>,
                    "name": "<?php echo esc($product['nombre']); ?>",
                    "description": "<?php echo esc(substr(strip_tags($product['descripcion_corta']), 0, 200)); ?>",
                    "brand": {
                        "@type": "Brand",
                        "name": "<?php echo esc($product['marca_nombre']); ?>"
                    },
                    "category": "<?php echo esc($product['categoria_nombre']); ?>",
                    "url": "<?php echo SITE_URL; ?>/producto.php?id=<?php echo $product['id']; ?>",
                    "image": "<?php echo imageUrl('productos/' . $product['codigo'] . '.jpg'); ?>"
                }<?php echo ($index < count($products) - 1) ? ',' : ''; ?>
                <?php endforeach; ?>
            ]
        }
    }
    </script>
</head>

<body class="catalogo-page">
    
    <!-- ========================================
         NAVBAR
         ======================================== -->
    <?php include INCLUDES_PATH . '/navbar.php'; ?>
    
    <!-- ========================================
         BREADCRUMB
         ======================================== -->
    <section class="breadcrumb-section py-3 bg-light border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo siteUrl(); ?>" class="text-decoration-none">
                            <i class="bi bi-house-fill me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Catálogo</li>
                </ol>
            </nav>
        </div>
    </section>
    
    
    <!-- ========================================
         FILTROS Y PRODUCTOS
         ======================================== -->
    <section id="filtros" class="catalogo-content py-5">
        <div class="container">
            <div class="row">
                
                <!-- ========================================
                     SIDEBAR FILTROS
                     ======================================== -->
                <div class="col-lg-3 mb-4">
                    <div class="filters-sidebar">
                        
                        <!-- Botón para mostrar/ocultar filtros en móvil -->
                        <button class="btn btn-primary w-100 d-lg-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                            <i class="bi bi-funnel me-2"></i>
                            Filtros
                        </button>
                        
                        <div class="collapse d-lg-block" id="filtersCollapse">
                            
                            <!-- Formulario de Filtros -->
                            <form method="GET" class="filters-form">
                                <input type="hidden" name="page" value="1">
                                
                                <!-- Búsqueda -->
                                <div class="filter-section mb-4">
                                    <h5 class="filter-title">
                                        <i class="bi bi-search me-2"></i>
                                        Búsqueda
                                    </h5>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               name="busqueda" 
                                               value="<?php echo esc($busqueda); ?>" 
                                               placeholder="Buscar productos...">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Limpiar Filtros -->
                                <?php if ($marca_id || $categoria_id || $busqueda): ?>
                                <div class="mb-4">
                                    <a href="catalogo.php" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Limpiar Filtros
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Filtro por Marca -->
                                <div class="filter-section mb-4">
                                    <h5 class="filter-title">
                                        <i class="bi bi-building me-2"></i>
                                        Marca
                                    </h5>
                                    <div class="filter-options">
                                        <?php foreach ($marcas as $marca): ?>
                                        <?php 
                                        // Mostrar el contador contextual (filtrado)
                                        $marca_count = $marca['productos_count'];
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="marca" 
                                                   id="marca_<?php echo $marca['id']; ?>" 
                                                   value="<?php echo $marca['id']; ?>"
                                                   <?php echo ($marca_id == $marca['id']) ? 'checked' : ''; ?>
                                                   onchange="this.form.submit()">
                                            <label class="form-check-label <?php echo ($marca_id == $marca['id']) ? 'text-primary fw-semibold' : ''; ?>" for="marca_<?php echo $marca['id']; ?>">
                                                <?php echo esc($marca['nombre']); ?>
                                                <span class="text-muted ms-2">(<?php echo $marca_count; ?>)</span>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="marca" 
                                                   id="marca_all" 
                                                   value=""
                                                   <?php echo ($marca_id == 0 || empty($marca_id)) ? 'checked' : ''; ?>
                                                   onchange="this.form.submit()">
                                            <label class="form-check-label <?php echo ($marca_id == 0 || empty($marca_id)) ? 'text-primary fw-semibold' : ''; ?>" for="marca_all">
                                                <strong>Todas las marcas</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Filtro por Categoría -->
                                <div class="filter-section mb-4">
                                    <h5 class="filter-title">
                                        <i class="bi bi-tags me-2"></i>
                                        Categoría
                                    </h5>
                                    <div class="filter-options">
                                        <?php foreach ($categorias as $categoria): ?>
                                        <?php 
                                        // Mostrar el contador contextual (filtrado)
                                        $categoria_count = $categoria['productos_count'];
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="categoria" 
                                                   id="categoria_<?php echo $categoria['id']; ?>" 
                                                   value="<?php echo $categoria['id']; ?>"
                                                   <?php echo ($categoria_id == $categoria['id']) ? 'checked' : ''; ?>
                                                   onchange="this.form.submit()">
                                            <label class="form-check-label <?php echo ($categoria_id == $categoria['id']) ? 'text-primary fw-semibold' : ''; ?>" for="categoria_<?php echo $categoria['id']; ?>">
                                                <?php echo esc($categoria['nombre']); ?>
                                                <span class="text-muted ms-2">(<?php echo $categoria_count; ?>)</span>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="categoria" 
                                                   id="categoria_all" 
                                                   value=""
                                                   <?php echo ($categoria_id == 0 || empty($categoria_id)) ? 'checked' : ''; ?>
                                                   onchange="this.form.submit()">
                                            <label class="form-check-label <?php echo ($categoria_id == 0 || empty($categoria_id)) ? 'text-primary fw-semibold' : ''; ?>" for="categoria_all">
                                                <strong>Todas las categorías</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- ========================================
                     CONTENIDO PRINCIPAL
                     ======================================== -->
                <div class="col-lg-9">
                    
                    <!-- Filtros Activos -->
                    <?php if ($marca_id || $categoria_id || $busqueda): ?>
                    <div class="active-filters mb-4">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-3 text-muted fw-semibold">
                                <i class="bi bi-funnel me-1"></i>Filtros aplicados:
                            </span>
                            
                            <?php if ($busqueda): ?>
                            <span class="badge bg-primary me-2 mb-2">
                                Búsqueda: "<?php echo esc($busqueda); ?>"
                                <a href="<?php echo buildFilterUrl(['busqueda' => '']); ?>" class="btn-close btn-close-white ms-2" aria-label="Eliminar filtro de búsqueda"></a>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ($marca_id): ?>
                            <?php 
                            $marca_seleccionada = array_filter($marcas, function($m) use ($marca_id) { return $m['id'] == $marca_id; });
                            $marca_seleccionada = reset($marca_seleccionada);
                            ?>
                            <span class="badge bg-primary me-2 mb-2">
                                Marca: <?php echo esc($marca_seleccionada['nombre']); ?>
                                <a href="<?php echo buildFilterUrl(['marca' => '']); ?>" class="btn-close btn-close-white ms-2" aria-label="Eliminar filtro de marca"></a>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ($categoria_id): ?>
                            <?php 
                            $categoria_seleccionada = array_filter($categorias, function($c) use ($categoria_id) { return $c['id'] == $categoria_id; });
                            $categoria_seleccionada = reset($categoria_seleccionada);
                            ?>
                            <span class="badge bg-primary me-2 mb-2">
                                Categoría: <?php echo esc($categoria_seleccionada['nombre']); ?>
                                <a href="<?php echo buildFilterUrl(['categoria' => '']); ?>" class="btn-close btn-close-white ms-2" aria-label="Eliminar filtro de categoría"></a>
                            </span>
                            <?php endif; ?>
                            
                            <a href="catalogo.php" class="btn btn-sm btn-outline-secondary clear-filters">
                                <i class="bi bi-x-circle me-1"></i>Limpiar todos
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Barra de Resultados -->
                    <div class="results-bar mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="mb-0 text-muted">
                                    Mostrando <?php echo count($products); ?> de <?php echo number_format($total_products); ?> productos
                                    <?php if ($marca_id || $categoria_id || $busqueda): ?>
                                    <span class="text-primary">(filtrados)</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary active" data-view="grid">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-view="list">
                                        <i class="bi bi-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grid de Productos -->
                    <div class="products-grid" data-aos="fade-up">
                        <?php if (empty($products)): ?>
                        
                        <!-- Sin Resultados -->
                        <div class="text-center py-5">
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h3 class="mt-3">No se encontraron productos</h3>
                            <p class="text-muted">Intenta ajustar tus filtros de búsqueda</p>
                            <a href="catalogo.php" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Ver Todos los Productos
                            </a>
                        </div>
                        
                        <?php else: ?>
                        
                        <!-- Productos -->
                        <div class="row g-4">
                            <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-md-6 product-item">
                                <div class="product-card h-100">
                                    
                                    <!-- Imagen del Producto -->
                                    <div class="product-image-wrapper">
                                        <?php if ($product['destacado']): ?>
                                        <div class="product-badge">
                                            <i class="bi bi-star-fill me-1"></i>
                                            Destacado
                                        </div>
                                        <?php endif; ?>
                                        
                                        <a href="producto.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                                            <?php
                                            // Usar imagen real de la base de datos si existe
                                            if (!empty($product['imagen_url'])) {
                                                $imagen_real = $product['imagen_url'];
                                                // Convertir ruta relativa a URL completa
                                                if (strpos($imagen_real, '/assets/') === 0) {
                                                    $imagen_real = SITE_URL . $imagen_real;
                                                }
                                                echo '<img src="' . esc($imagen_real) . '" 
                                                         alt="' . esc($product['nombre']) . '" 
                                                         class="product-image"
                                                         loading="lazy"
                                                         onerror="this.src=\'' . imageUrl('design/placeholder-product.jpg') . '\'">';
                                            } else {
                                                // Fallback: usar imagen placeholder
                                                echo '<img src="' . imageUrl('design/placeholder-product.jpg') . '" 
                                                         alt="' . esc($product['nombre']) . '" 
                                                         class="product-image"
                                                         loading="lazy">';
                                            }
                                            ?>
                                        </a>
                                        
                                        <!-- Overlay con acciones -->
                                        <div class="product-overlay">
                                            <div class="product-actions">
                                                <a href="producto.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-light btn-sm" 
                                                   title="Ver Detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm add-to-cart-btn" 
                                                        data-product-id="<?php echo $product['id']; ?>"
                                                        data-product-nombre="<?php echo esc($product['nombre']); ?>"
                                                        data-product-codigo="<?php echo esc($product['codigo'] ?? ''); ?>"
                                                        title="Agregar a Cotización">
                                                    <i class="bi bi-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información del Producto -->
                                    <div class="product-info">
                                        <div class="product-meta mb-2">
                                            <span class="product-category"><?php echo esc($product['categoria_nombre']); ?></span>
                                            <span class="product-brand"><?php echo esc($product['marca_nombre']); ?></span>
                                        </div>
                                        
                                        <h3 class="product-title">
                                            <a href="producto.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                                <?php echo esc($product['nombre']); ?>
                                            </a>
                                        </h3>
                                        
                                        <p class="product-description">
                                            <?php echo esc(substr(strip_tags($product['descripcion_corta']), 0, 120)); ?>
                                            <?php if (strlen(strip_tags($product['descripcion_corta'])) > 120): ?>...<?php endif; ?>
                                        </p>
                                        
                                        <div class="product-footer">
                                            <div class="product-code">
                                                <small class="text-muted">Código: <?php echo esc($product['codigo']); ?></small>
                                            </div>
                                            <a href="producto.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                Ver Detalles
                                                <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php endif; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Paginación del catálogo" class="mt-5">
                        <ul class="pagination justify-content-center">
                            
                            <!-- Página Anterior -->
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo buildFilterUrl(['page' => $page - 1]); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                    Anterior
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <!-- Números de Página -->
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo buildFilterUrl(['page' => 1]); ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo buildFilterUrl(['page' => $i]); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo buildFilterUrl(['page' => $total_pages]); ?>"><?php echo $total_pages; ?></a>
                            </li>
                            <?php endif; ?>
                            
                            <!-- Página Siguiente -->
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo buildFilterUrl(['page' => $page + 1]); ?>">
                                    Siguiente
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         CTA SECTION
         ======================================== -->
    <section class="catalogo-cta py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="h3 mb-3">¿No encuentras lo que buscas?</h2>
                    <p class="text-muted mb-4">
                        Contamos con más de 500 productos en nuestro catálogo completo. 
                        Nuestros asesores pueden ayudarte a encontrar la solución perfecta.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="<?php echo siteUrl('index.php#newsletter'); ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-chat-left-text me-2"></i>
                            Hablar con un Asesor
                        </a>
                        <a href="<?php echo siteUrl(); ?>" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-house me-2"></i>
                            Volver al Inicio
                        </a>
                    </div>
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
    <script src="<?php echo assetUrl('js/catalogo.js'); ?>?v=<?php echo time(); ?>"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 600,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    
    <!-- Initialize Catalog JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AramedCatalogo !== 'undefined') {
                AramedCatalogo.init();
            }
            
            // Manejar botones de agregar al carrito
            document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productNombre = this.dataset.productNombre;
                    const productCodigo = this.dataset.productCodigo || '';
                    
                    // Deshabilitar botón temporalmente
                    const originalHTML = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                    
                    fetch('includes/cart_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'add',
                            producto_id: productId,
                            producto_nombre: productNombre,
                            producto_codigo: productCodigo,
                            cantidad: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mostrar notificación
                            this.innerHTML = '<i class="bi bi-check-circle"></i>';
                            this.classList.remove('btn-primary');
                            this.classList.add('btn-success');
                            
                            // Actualizar contador del carrito en navbar
                            updateCartBadge(data.cart_count);
                            
                            // Mostrar mensaje
                            setTimeout(() => {
                                this.innerHTML = originalHTML;
                                this.classList.remove('btn-success');
                                this.classList.add('btn-primary');
                                this.disabled = false;
                            }, 2000);
                            
                            // Opcional: mostrar toast
                            if (typeof showToast !== 'undefined') {
                                showToast('Producto agregado al carrito', 'success');
                            }
                        } else {
                            alert('Error: ' + data.message);
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al agregar producto al carrito');
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                    });
                });
            });
            
            // Función para actualizar badge del carrito
            function updateCartBadge(count) {
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-block' : 'none';
                }
            }
            
            // Cargar contador inicial del carrito
            fetch('includes/cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ action: 'get' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartBadge(data.cart_count);
                }
            });
        });
    </script>
    
</body>
</html>
