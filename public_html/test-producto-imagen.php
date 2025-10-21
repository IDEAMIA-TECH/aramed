<?php
/**
 * Script de prueba para verificar imágenes de productos
 */
require_once 'includes/config.php';
require_once 'includes/connection.php';

$product_id = 45;

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
    
    echo "<h1>Producto ID {$product_id}</h1>";
    echo "<p><strong>Nombre:</strong> {$product['nombre']}</p>";
    echo "<p><strong>Código:</strong> {$product['codigo']}</p>";
    echo "<p><strong>Categoría:</strong> {$product['categoria_nombre']}</p>";
    echo "<p><strong>Marca:</strong> {$product['marca_nombre']}</p>";
    
    // Obtener imágenes del producto
    $images_sql = "
        SELECT * FROM catalogo_producto_imagenes 
        WHERE producto_id = ? 
        ORDER BY es_principal DESC, orden ASC
    ";
    $images_stmt = $pdo->prepare($images_sql);
    $images_stmt->execute([$product_id]);
    $images = $images_stmt->fetchAll();
    
    echo "<h2>Imágenes del producto:</h2>";
    echo "<p>Total de imágenes: " . count($images) . "</p>";
    
    foreach ($images as $index => $image) {
        echo "<div style='margin: 20px 0; padding: 20px; border: 1px solid #ccc;'>";
        echo "<h3>Imagen " . ($index + 1) . "</h3>";
        echo "<p><strong>URL original:</strong> {$image['imagen_url']}</p>";
        
        // Convertir ruta relativa a URL completa
        $imagen_url = $image['imagen_url'];
        if (strpos($imagen_url, '/assets/') === 0) {
            $imagen_url = SITE_URL . $imagen_url;
        }
        
        echo "<p><strong>URL completa:</strong> {$imagen_url}</p>";
        echo "<p><strong>Es principal:</strong> " . ($image['es_principal'] ? 'SÍ' : 'NO') . "</p>";
        echo "<p><strong>Tipo:</strong> {$image['tipo']}</p>";
        
        // Mostrar la imagen
        echo "<img src='{$imagen_url}' alt='{$product['nombre']}' style='max-width: 300px; height: auto; border: 1px solid #ddd;'>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
