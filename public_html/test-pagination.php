<?php
/**
 * Script de prueba para verificar la paginación
 */
require_once 'includes/config.php';
require_once 'includes/connection.php';

// Función para construir URL de filtros (copiada del catálogo)
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

try {
    $pdo = getDB();
    
    // Parámetros de paginación
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 12;
    $offset = ($page - 1) * $per_page;
    
    // Contar total de productos
    $count_sql = 'SELECT COUNT(*) as total FROM catalogo_productos p WHERE p.estado = "activo"';
    $count_stmt = $pdo->query($count_sql);
    $total_products = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_products / $per_page);
    
    // Obtener productos de la página actual
    $products_sql = "SELECT p.*, m.nombre as marca_nombre, c.nombre as categoria_nombre 
                     FROM catalogo_productos p
                     LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
                     LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
                     WHERE p.estado = 'activo'
                     ORDER BY p.nombre ASC
                     LIMIT {$per_page} OFFSET {$offset}";
    $products_stmt = $pdo->query($products_sql);
    $products = $products_stmt->fetchAll();
    
    echo "<h1>Prueba de Paginación</h1>";
    echo "<p>Página actual: {$page} de {$total_pages}</p>";
    echo "<p>Total productos: {$total_products}</p>";
    echo "<p>Productos en esta página: " . count($products) . "</p>";
    
    echo "<h2>Productos en página {$page}:</h2>";
    echo "<ul>";
    foreach ($products as $product) {
        echo "<li>{$product['codigo']} - {$product['nombre']} ({$product['categoria_nombre']})</li>";
    }
    echo "</ul>";
    
    // Generar paginación
    if ($total_pages > 1) {
        echo "<h2>Paginación:</h2>";
        echo "<nav><ul class='pagination'>";
        
        // Página anterior
        if ($page > 1) {
            echo "<li class='page-item'><a class='page-link' href='" . buildFilterUrl(['page' => $page - 1]) . "'>Anterior</a></li>";
        }
        
        // Números de página
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            $active_class = ($i == $page) ? 'active' : '';
            echo "<li class='page-item {$active_class}'><a class='page-link' href='" . buildFilterUrl(['page' => $i]) . "'>{$i}</a></li>";
        }
        
        // Página siguiente
        if ($page < $total_pages) {
            echo "<li class='page-item'><a class='page-link' href='" . buildFilterUrl(['page' => $page + 1]) . "'>Siguiente</a></li>";
        }
        
        echo "</ul></nav>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
