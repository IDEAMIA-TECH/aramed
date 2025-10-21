<?php
/**
 * Script de prueba para verificar las imágenes de productos relacionados
 */
require_once 'includes/config.php';
require_once 'includes/connection.php';

$product_id = 83; // HAL 1 AÑO

try {
    $pdo = getDB();
    
    // Obtener información del producto principal
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
    
    // Obtener productos relacionados con sus imágenes
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
    
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Test Productos Relacionados - {$product['nombre']}</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='assets/css/producto.css'>
    </head>
    <body>
        <div class='container mt-4'>
            <h1>Test Productos Relacionados</h1>
            <p><strong>Producto principal:</strong> {$product['nombre']} (ID: {$product_id})</p>
            <p><strong>Categoría:</strong> {$product['categoria_nombre']}</p>
            <p><strong>Total productos relacionados:</strong> " . count($related_products) . "</p>";
    
    if (!empty($related_products)) {
        echo "
        <div class='row g-4 mt-4'>
            <h3>Productos Relacionados:</h3>";
        
        foreach ($related_products as $related) {
            echo "
            <div class='col-lg-3 col-md-6'>
                <div class='related-product-card'>
                    <div class='related-image-wrapper'>
                        <a href='producto.php?id={$related['id']}'>";
            
            // Usar imagen real de la base de datos si existe
            if (!empty($related['imagen_url'])) {
                $imagen_real = $related['imagen_url'];
                // Convertir ruta relativa a URL completa
                if (strpos($imagen_real, '/assets/') === 0) {
                    $imagen_real = SITE_URL . $imagen_real;
                }
                echo "<img src='{$imagen_real}' 
                         alt='{$related['nombre']}' 
                         class='related-image'
                         loading='lazy'
                         onerror=\"this.src='" . imageUrl('design/placeholder-product.jpg') . "'\">";
            } else {
                // Fallback: usar imagen placeholder
                echo "<img src='" . imageUrl('design/placeholder-product.jpg') . "' 
                         alt='{$related['nombre']}' 
                         class='related-image'
                         loading='lazy'>";
            }
            
            echo "
                        </a>
                    </div>
                    <div class='related-info'>
                        <h5 class='related-title'>
                            <a href='producto.php?id={$related['id']}' class='text-decoration-none'>
                                {$related['nombre']}
                            </a>
                        </h5>
                        <p class='related-brand text-muted small'>{$related['marca_nombre']}</p>
                        <p><strong>Imagen URL:</strong> " . ($related['imagen_url'] ?: 'No disponible') . "</p>
                        <a href='producto.php?id={$related['id']}' class='btn btn-outline-primary btn-sm'>
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>";
        }
        
        echo "
        </div>";
    } else {
        echo "<p>No hay productos relacionados</p>";
    }
    
    echo "
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
