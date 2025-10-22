<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Detalle de Producto
 * ========================================
 * 
 * Página de detalles de un producto individual
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

// Obtener ID del producto
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: catalogo.php');
    exit;
}

// Obtener información del producto
try {
    $pdo = getDB();
    
    // Consulta principal del producto
    $product_sql = "
        SELECT p.*, m.nombre as marca_nombre, m.logo as marca_logo, c.nombre as categoria_nombre
        FROM catalogo_productos p
        LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
        LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
        WHERE p.id = ? AND p.estado = 'activo'
    ";
    
    $product_stmt = $pdo->prepare($product_sql);
    $product_stmt->execute([$product_id]);
    $product = $product_stmt->fetch();
    
    if (!$product) {
        header('Location: catalogo.php');
        exit;
    }
    
    // Obtener imágenes del producto
    $images_sql = "
        SELECT * FROM catalogo_producto_imagenes 
        WHERE producto_id = ? 
        ORDER BY es_principal DESC, orden ASC
    ";
    $images_stmt = $pdo->prepare($images_sql);
    $images_stmt->execute([$product_id]);
    $images = $images_stmt->fetchAll();
    
    // Obtener documentos del producto
    $documents_sql = "
        SELECT * FROM catalogo_producto_documentos 
        WHERE producto_id = ? 
        ORDER BY tipo ASC, nombre ASC
    ";
    $documents_stmt = $pdo->prepare($documents_sql);
    $documents_stmt->execute([$product_id]);
    $documents = $documents_stmt->fetchAll();
    
    // Obtener productos relacionados (misma categoría) con imágenes
    $related_sql = "
        SELECT p.*, m.nombre as marca_nombre, i.imagen_url
        FROM catalogo_productos p
        LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
        LEFT JOIN catalogo_producto_imagenes i ON p.id = i.producto_id AND i.es_principal = 1
        WHERE p.categoria_id = ? AND p.id != ? AND p.estado = 'activo'
        ORDER BY p.destacado DESC, RAND()
        LIMIT 4
    ";
    $related_stmt = $pdo->prepare($related_sql);
    $related_stmt->execute([$product['categoria_id'], $product_id]);
    $related_products = $related_stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Error en producto.php: " . $e->getMessage());
    header('Location: catalogo.php');
    exit;
}

// Variables para meta tags
$pageTitle = $product['nombre'] . ' - ' . SITE_NAME;
$pageDescription = $product['descripcion_corta'] ?: substr(strip_tags($product['descripcion_larga']), 0, 160);
$pageKeywords = $product['nombre'] . ', ' . $product['marca_nombre'] . ', ' . $product['categoria_nombre'] . ', simuladores médicos';
$pageUrl = SITE_URL . '/producto.php?id=' . $product_id;

// Generar breadcrumb
$breadcrumb = [
    ['name' => 'Inicio', 'url' => siteUrl()],
    ['name' => 'Catálogo', 'url' => 'catalogo.php'],
    ['name' => $product['categoria_nombre'], 'url' => 'catalogo.php?categoria=' . $product['categoria_id']],
    ['name' => $product['nombre'], 'url' => '']
];

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
         OPEN GRAPH
         ======================================== -->
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="<?php echo esc(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo esc($pageTitle); ?>">
    <meta property="og:description" content="<?php echo esc($pageDescription); ?>">
    <meta property="og:url" content="<?php echo esc($pageUrl); ?>">
    <meta property="og:image" content="<?php echo imageUrl('productos/' . strtolower($product['codigo']) . '.jpg'); ?>">
    <meta property="og:locale" content="es_MX">
    
    <!-- ========================================
         PRODUCT SPECIFIC META
         ======================================== -->
    <meta property="product:brand" content="<?php echo esc($product['marca_nombre']); ?>">
    <meta property="product:category" content="<?php echo esc($product['categoria_nombre']); ?>">
    <meta property="product:availability" content="in stock">
    <meta property="product:condition" content="new">
    
    <!-- ========================================
         TWITTER CARD
         ======================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo esc($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo imageUrl('productos/' . strtolower($product['codigo']) . '.jpg'); ?>">
    
    <!-- ========================================
         SCHEMA.ORG JSON-LD
         ======================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "<?php echo esc($product['titulo']); ?>",
        "description": "<?php echo esc($product['descripcion']); ?>",
        "image": "<?php echo imageUrl('productos/' . strtolower($product['codigo']) . '.jpg'); ?>",
        "brand": {
            "@type": "Brand",
            "name": "<?php echo esc($product['marca_nombre']); ?>"
        },
        "category": "<?php echo esc($product['categoria_nombre']); ?>",
        "sku": "<?php echo esc($product['sku']); ?>",
        "url": "<?php echo siteUrl('/producto.php?id=' . $product['id']); ?>",
        "offers": {
            "@type": "Offer",
            "price": "<?php echo $product['precio']; ?>",
            "priceCurrency": "MXN",
            "availability": "https://schema.org/InStock",
            "seller": {
                "@type": "Organization",
                "name": "<?php echo esc(SITE_NAME); ?>",
                "url": "<?php echo siteUrl(); ?>"
            }
        },
        "manufacturer": {
            "@type": "Organization",
            "name": "<?php echo esc($product['marca_nombre']); ?>"
        },
        "additionalProperty": [
            {
                "@type": "PropertyValue",
                "name": "Uso",
                "value": "<?php echo esc($product['uso_nombre']); ?>"
            },
            {
                "@type": "PropertyValue",
                "name": "Estado",
                "value": "<?php echo esc($product['estado']); ?>"
            }
        ],
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
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "<?php echo esc($product['titulo']); ?>",
                    "item": "<?php echo siteUrl('/producto.php?id=' . $product['id']); ?>"
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
         SWIPER JS (Para galería de imágenes)
         ======================================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- ========================================
         AOS (Animate On Scroll)
         ======================================== -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- ========================================
         CUSTOM CSS
         ======================================== -->
    <link rel="stylesheet" href="<?php echo assetUrl('css/main.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/producto.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/responsive.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- ========================================
         STRUCTURED DATA
         ======================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "<?php echo esc($product['nombre']); ?>",
        "description": "<?php echo esc($pageDescription); ?>",
        "image": "<?php echo imageUrl('productos/' . strtolower($product['codigo']) . '.jpg'); ?>",
        "brand": {
            "@type": "Brand",
            "name": "<?php echo esc($product['marca_nombre']); ?>"
        },
        "category": "<?php echo esc($product['categoria_nombre']); ?>",
        "sku": "<?php echo esc($product['codigo']); ?>",
        "offers": {
            "@type": "Offer",
            "availability": "https://schema.org/InStock",
            "priceCurrency": "MXN",
            "price": "<?php echo $product['precio_publico']; ?>",
            "seller": {
                "@type": "Organization",
                "name": "<?php echo esc(SITE_NAME); ?>"
            }
        },
        "manufacturer": {
            "@type": "Organization",
            "name": "<?php echo esc($product['marca_nombre']); ?>"
        }
    }
    </script>
</head>

<body class="producto-page">
    
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
                    <?php foreach ($breadcrumb as $index => $item): ?>
                        <?php if ($index === count($breadcrumb) - 1): ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo esc($item['name']); ?>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo esc($item['url']); ?>" class="text-decoration-none">
                                    <?php echo esc($item['name']); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        </div>
    </section>
    
    <!-- ========================================
         CONTENIDO PRINCIPAL DEL PRODUCTO
         ======================================== -->
    <section class="producto-main py-5">
        <div class="container">
            <div class="row">
                
                <!-- ========================================
                     GALERÍA DE IMÁGENES
                     ======================================== -->
                <div class="col-lg-6 mb-4">
                    <div class="producto-gallery">
                        
                        <?php if (!empty($images)): ?>
                        
                        <!-- Swiper principal -->
                        <div class="swiper producto-swiper-main">
                            <div class="swiper-wrapper">
                                <?php foreach ($images as $image): ?>
                                <div class="swiper-slide">
                                    <div class="producto-image-wrapper">
                                        <?php
                                        $imagen_url = $image['imagen_url'];
                                        // Convertir ruta relativa a URL completa
                                        if (strpos($imagen_url, '/assets/') === 0) {
                                            $imagen_url = SITE_URL . $imagen_url;
                                        }
                                        ?>
                                        <img src="<?php echo esc($imagen_url); ?>" 
                                             alt="<?php echo esc($product['nombre']); ?>" 
                                             class="producto-image"
                                             loading="lazy"
                                             onerror="this.src='<?php echo imageUrl('design/placeholder-product.jpg'); ?>'">
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Navegación -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            
                            <!-- Paginación -->
                            <div class="swiper-pagination"></div>
                        </div>
                        
                        <!-- Swiper thumbnails -->
                        <?php if (count($images) > 1): ?>
                        <div class="swiper producto-swiper-thumbs mt-3">
                            <div class="swiper-wrapper">
                                <?php foreach ($images as $image): ?>
                                <div class="swiper-slide">
                                    <div class="producto-thumbnail">
                                        <?php
                                        $imagen_url = $image['imagen_url'];
                                        // Convertir ruta relativa a URL completa
                                        if (strpos($imagen_url, '/assets/') === 0) {
                                            $imagen_url = SITE_URL . $imagen_url;
                                        }
                                        ?>
                                        <img src="<?php echo esc($imagen_url); ?>" 
                                             alt="<?php echo esc($product['nombre']); ?>" 
                                             class="thumbnail-image"
                                             onerror="this.src='<?php echo imageUrl('design/placeholder-product.jpg'); ?>'">
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        
                        <!-- Imagen por defecto -->
                        <div class="producto-image-wrapper">
                            <img src="<?php echo imageUrl('productos/' . strtolower($product['codigo']) . '.jpg'); ?>" 
                                 alt="<?php echo esc($product['nombre']); ?>" 
                                 class="producto-image"
                                 onerror="this.src='<?php echo imageUrl('design/placeholder-product.jpg'); ?>'">
                        </div>
                        
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <!-- ========================================
                     INFORMACIÓN DEL PRODUCTO
                     ======================================== -->
                <div class="col-lg-6">
                    <div class="producto-info">
                        
                        <!-- Badges -->
                        <div class="producto-badges mb-3">
                            <?php if ($product['destacado']): ?>
                            <span class="badge bg-warning text-dark me-2">
                                <i class="bi bi-star-fill me-1"></i>
                                Destacado
                            </span>
                            <?php endif; ?>
                            <?php if ($product['nuevo']): ?>
                            <span class="badge bg-success me-2">
                                <i class="bi bi-sparkles me-1"></i>
                                Nuevo
                            </span>
                            <?php endif; ?>
                            <?php if ($product['promocion']): ?>
                            <span class="badge bg-danger me-2">
                                <i class="bi bi-percent me-1"></i>
                                Oferta
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Categoría y Marca -->
                        <div class="producto-meta mb-3">
                            <span class="producto-category"><?php echo esc($product['categoria_nombre']); ?></span>
                            <span class="producto-brand">
                                <?php if ($product['marca_logo']): ?>
                                <img src="<?php echo esc($product['marca_logo']); ?>" 
                                     alt="<?php echo esc($product['marca_nombre']); ?>" 
                                     class="marca-logo">
                                <?php else: ?>
                                <?php echo esc($product['marca_nombre']); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <!-- Título -->
                        <h1 class="producto-title mb-3"><?php echo esc($product['nombre']); ?></h1>
                        
                        <!-- Código -->
                        <div class="producto-code mb-3">
                            <small class="text-muted">Código: <strong><?php echo esc($product['codigo']); ?></strong></small>
                        </div>
                        
                        <!-- Descripción corta -->
                        <?php if ($product['descripcion_corta']): ?>
                        <div class="producto-description-short mb-4">
                            <p class="lead"><?php echo esc($product['descripcion_corta']); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Precio -->
                        <?php if ($product['precio_publico'] > 0): ?>
                        <div class="producto-pricing mb-4">
                            <div class="price-main">
                                <?php if ($product['precio_especial'] && $product['precio_especial'] < $product['precio_publico']): ?>
                                <span class="price-original text-muted text-decoration-line-through me-2">
                                    $<?php echo number_format($product['precio_publico'], 2); ?>
                                </span>
                                <span class="price-special h4 text-danger fw-bold">
                                    $<?php echo number_format($product['precio_especial'], 2); ?>
                                </span>
                                <?php else: ?>
                                <span class="price-current h4 text-primary fw-bold">
                                    $<?php echo number_format($product['precio_publico'], 2); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">Precio sujeto a cambio sin previo aviso</small>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Disponibilidad -->
                        <div class="producto-availability mb-4">
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                <?php echo ucfirst($product['disponibilidad']); ?>
                            </span>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="producto-actions mb-4">
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="#newsletter" class="btn btn-primary btn-lg flex-fill">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Solicitar Cotización
                                </a>
                                <a href="#newsletter" class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Consultar
                                </a>
                            </div>
                        </div>
                        
                        <!-- Características principales -->
                        <?php if ($product['caracteristicas']): ?>
                        <div class="producto-features mb-4">
                            <h5 class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Características Principales
                            </h5>
                            <div class="features-content">
                                <?php echo $product['caracteristicas']; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         DETALLES DEL PRODUCTO
         ======================================== -->
    <?php if ($product['descripcion_larga'] || $product['especificaciones']): ?>
    <section class="producto-details py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    
                    <!-- Descripción completa -->
                    <?php if ($product['descripcion_larga']): ?>
                    <div class="producto-description-full mb-5" data-aos="fade-up">
                        <h3 class="mb-4">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            Descripción del Producto
                        </h3>
                        <div class="description-content">
                            <?php echo $product['descripcion_larga']; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Especificaciones -->
                    <?php if ($product['especificaciones']): ?>
                    <div class="producto-specifications" data-aos="fade-up" data-aos-delay="100">
                        <h3 class="mb-4">
                            <i class="bi bi-gear-fill text-primary me-2"></i>
                            Especificaciones Técnicas
                        </h3>
                        <div class="specifications-content">
                            <?php echo $product['especificaciones']; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Sidebar con documentos -->
                <?php if (!empty($documents)): ?>
                <div class="col-lg-4">
                    <div class="producto-documents" data-aos="fade-up" data-aos-delay="200">
                        <h4 class="mb-4">
                            <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>
                            Documentos
                        </h4>
                        <div class="documents-list">
                            <?php foreach ($documents as $document): ?>
                            <div class="document-item mb-3">
                                <a href="<?php echo esc($document['documento_url']); ?>" 
                                   class="btn btn-outline-primary w-100 text-start" 
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>
                                    <?php echo esc($document['nombre']); ?>
                                    <i class="bi bi-download float-end"></i>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- ========================================
         PRODUCTOS RELACIONADOS
         ======================================== -->
    <?php if (!empty($related_products)): ?>
    <section class="producto-related py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="h3 mb-3">Productos Relacionados</h2>
                <p class="text-muted">Otros productos que podrían interesarte</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($loop->iteration * 100); ?>">
                    <div class="related-product-card">
                        <div class="related-image-wrapper">
                            <a href="producto.php?id=<?php echo $related['id']; ?>">
                                <?php
                                if (!empty($related['imagen_url'])) {
                                    $imagen_url = $related['imagen_url'];
                                    // Convertir ruta relativa a URL completa
                                    if (strpos($imagen_url, '/assets/') === 0) {
                                        $imagen_url = SITE_URL . $imagen_url;
                                    }
                                    echo '<img src="' . esc($imagen_url) . '" 
                                             alt="' . esc($related['nombre']) . '" 
                                             class="related-image"
                                             onerror="this.src=\'' . imageUrl('design/placeholder-product.jpg') . '\'">';
                                } else {
                                    echo '<img src="' . imageUrl('design/placeholder-product.jpg') . '" 
                                             alt="' . esc($related['nombre']) . '" 
                                             class="related-image">';
                                }
                                ?>
                            </a>
                        </div>
                        <div class="related-info">
                            <h5 class="related-title">
                                <a href="producto.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                    <?php echo esc($related['nombre']); ?>
                                </a>
                            </h5>
                            <p class="related-brand text-muted small"><?php echo esc($related['marca_nombre']); ?></p>
                            <a href="producto.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary btn-sm">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- ========================================
         CTA SECTION
         ======================================== -->
    <section class="producto-cta py-5 bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="h3 mb-3">¿Interesado en este producto?</h2>
                    <p class="lead mb-4">
                        Nuestros asesores están listos para ayudarte con información detallada, 
                        cotizaciones personalizadas y asesoría técnica especializada.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="#newsletter" class="btn btn-light btn-lg">
                            <i class="bi bi-envelope-fill me-2"></i>
                            Solicitar Información
                        </a>
                        <a href="catalogo.php" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-grid-3x3-gap me-2"></i>
                            Ver Catálogo
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
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo assetUrl('js/main.js'); ?>?v=<?php echo time(); ?>"></script>
    <script src="<?php echo assetUrl('js/producto.js'); ?>?v=<?php echo time(); ?>"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 600,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    
    <!-- Initialize Product JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar carrusel directamente
            if (typeof Swiper !== 'undefined') {
                // Swiper principal
                const mainSwiper = new Swiper('.producto-swiper-main', {
                    spaceBetween: 10,
                    navigation: {
                        nextEl: '.producto-swiper-main .swiper-button-next',
                        prevEl: '.producto-swiper-main .swiper-button-prev',
                    },
                    pagination: {
                        el: '.producto-swiper-main .swiper-pagination',
                        clickable: true,
                    },
                    keyboard: {
                        enabled: true,
                    },
                    loop: false,
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    thumbs: {
                        swiper: {
                            el: '.producto-swiper-thumbs',
                            slidesPerView: 4,
                            spaceBetween: 10,
                            freeMode: true,
                            watchSlidesProgress: true,
                        },
                    },
                });
                
                // Swiper thumbnails
                const thumbsSwiper = new Swiper('.producto-swiper-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                    breakpoints: {
                        320: {
                            slidesPerView: 3,
                            spaceBetween: 8,
                        },
                        768: {
                            slidesPerView: 4,
                            spaceBetween: 10,
                        },
                        1024: {
                            slidesPerView: 4,
                            spaceBetween: 10,
                        },
                    }
                });
                
                // Conectar swipers
                mainSwiper.thumbs.swiper = thumbsSwiper;
                thumbsSwiper.controller.control = mainSwiper;
                
                console.log('✅ Carrusel de producto inicializado correctamente');
            } else {
                console.error('Swiper no está disponible');
            }
        });
    </script>
    
</body>
</html>
