<?php
/**
 * Script de prueba para verificar el carrusel de imágenes del producto
 */
require_once 'includes/config.php';
require_once 'includes/connection.php';

$product_id = 8; // Producto con múltiples imágenes

try {
    $pdo = getDB();
    
    // Obtener información del producto
    $product_sql = "
        SELECT p.*, m.nombre as marca_nombre, c.nombre as categoria_nombre
        FROM catalogo_productos p
        LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
        LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
        WHERE p.id = ? AND p.estado = 'activo'
    ";
    
    $product_stmt = $pdo->prepare($product_sql);
    $product_stmt->execute([$product_id]);
    $product = $product_stmt->fetch();
    
    if (!$product) {
        echo "Producto no encontrado";
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
    
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Test Swiper - {$product['nombre']}</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css'>
        <link rel='stylesheet' href='assets/css/producto.css'>
    </head>
    <body>
        <div class='container mt-4'>
            <h1>Test Swiper - Producto ID {$product_id}</h1>
            <p><strong>Nombre:</strong> {$product['nombre']}</p>
            <p><strong>Total de imágenes:</strong> " . count($images) . "</p>";
    
    if (!empty($images)) {
        echo "
        <div class='producto-gallery'>
            <!-- Swiper principal -->
            <div class='swiper producto-swiper-main'>
                <div class='swiper-wrapper'>";
        
        foreach ($images as $image) {
            $imagen_url = $image['imagen_url'];
            if (strpos($imagen_url, '/assets/') === 0) {
                $imagen_url = SITE_URL . $imagen_url;
            }
            
            echo "
                    <div class='swiper-slide'>
                        <div class='producto-image-wrapper'>
                            <img src='{$imagen_url}' alt='{$product['nombre']}' class='producto-image'>
                        </div>
                    </div>";
        }
        
        echo "
                </div>
                
                <!-- Navegación -->
                <div class='swiper-button-next'></div>
                <div class='swiper-button-prev'></div>
                
                <!-- Paginación -->
                <div class='swiper-pagination'></div>
            </div>
            
            <!-- Swiper thumbnails -->
            <div class='swiper producto-swiper-thumbs mt-3'>
                <div class='swiper-wrapper'>";
        
        foreach ($images as $image) {
            $imagen_url = $image['imagen_url'];
            if (strpos($imagen_url, '/assets/') === 0) {
                $imagen_url = SITE_URL . $imagen_url;
            }
            
            echo "
                    <div class='swiper-slide'>
                        <div class='producto-thumbnail'>
                            <img src='{$imagen_url}' alt='{$product['nombre']}' class='thumbnail-image'>
                        </div>
                    </div>";
        }
        
        echo "
                </div>
            </div>
        </div>";
    } else {
        echo "<p>No hay imágenes para este producto</p>";
    }
    
    echo "
        </div>
        
        <!-- Scripts -->
        <script src='https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Inicializando Swiper...');
                
                // Swiper thumbnails
                const thumbsSwiper = new Swiper('.producto-swiper-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                });
                
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
                    effect: 'slide',
                    speed: 300,
                    thumbs: {
                        swiper: thumbsSwiper,
                    },
                });
                
                console.log('Swiper inicializado correctamente');
            });
        </script>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
