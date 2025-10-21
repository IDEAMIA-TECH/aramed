<?php
/**
 * Script de prueba específico para verificar la clase filter-badge
 */
require_once 'includes/config.php';
require_once 'includes/connection.php';

// Simular filtros aplicados
$marca_id = 1;
$categoria_id = 2;
$busqueda = "simulador";

try {
    $pdo = getDB();
    
    // Obtener datos reales
    $marca_sql = "SELECT * FROM catalogo_marcas WHERE id = ?";
    $marca_stmt = $pdo->prepare($marca_sql);
    $marca_stmt->execute([$marca_id]);
    $marca_seleccionada = $marca_stmt->fetch();
    
    $categoria_sql = "SELECT * FROM catalogo_categorias WHERE id = ?";
    $categoria_stmt = $pdo->prepare($categoria_sql);
    $categoria_stmt->execute([$categoria_id]);
    $categoria_seleccionada = $categoria_stmt->fetch();
    
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Test Filter Badge - Clase Personalizada</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
        <link rel='stylesheet' href='assets/css/catalogo.css'>
    </head>
    <body>
        <div class='container mt-4'>
            <h1>Test Filter Badge - Clase Personalizada</h1>
            <p>Verificando que la clase <code>.filter-badge</code> funciona correctamente</p>
            
            <!-- Test 1: Filtros con clase filter-badge -->
            <div class='active-filters mb-4'>
                <h3>Test 1: Con clase filter-badge (debería funcionar)</h3>
                <div class='d-flex flex-wrap align-items-center'>
                    <span class='me-3 text-muted fw-semibold'>
                        <i class='bi bi-funnel me-1'></i>Filtros aplicados:
                    </span>
                    
                    <span class='badge filter-badge me-2 mb-2'>
                        <span>Búsqueda: \"{$busqueda}\"</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                    
                    <span class='badge filter-badge me-2 mb-2'>
                        <span>Marca: " . ($marca_seleccionada['nombre'] ?? 'No encontrada') . "</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                    
                    <span class='badge filter-badge me-2 mb-2'>
                        <span>Categoría: " . ($categoria_seleccionada['nombre'] ?? 'No encontrada') . "</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                </div>
            </div>
            
            <!-- Test 2: Comparación con bg-primary -->
            <div class='active-filters mb-4'>
                <h3>Test 2: Comparación con bg-primary (puede no funcionar)</h3>
                <div class='d-flex flex-wrap align-items-center'>
                    <span class='me-3 text-muted fw-semibold'>
                        <i class='bi bi-funnel me-1'></i>Filtros aplicados:
                    </span>
                    
                    <span class='badge bg-primary me-2 mb-2'>
                        <span>Búsqueda: \"{$busqueda}\"</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                    
                    <span class='badge bg-primary me-2 mb-2'>
                        <span>Marca: " . ($marca_seleccionada['nombre'] ?? 'No encontrada') . "</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                </div>
            </div>
            
            <!-- Test 3: Información de debug -->
            <div class='alert alert-info'>
                <h5>Información de Debug:</h5>
                <ul>
                    <li><strong>Marca ID:</strong> {$marca_id} - " . ($marca_seleccionada['nombre'] ?? 'No encontrada') . "</li>
                    <li><strong>Categoría ID:</strong> {$categoria_id} - " . ($categoria_seleccionada['nombre'] ?? 'No encontrada') . "</li>
                    <li><strong>Búsqueda:</strong> \"{$busqueda}\"</li>
                </ul>
                <p><strong>Clase CSS:</strong> <code>.filter-badge</code></p>
                <p><strong>Color de fondo:</strong> #0066cc (azul)</p>
                <p><strong>Color de texto:</strong> #ffffff (blanco)</p>
            </div>
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
