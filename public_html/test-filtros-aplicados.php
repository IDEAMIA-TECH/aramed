<?php
/**
 * Script de prueba para verificar la visibilidad de los filtros aplicados
 */
require_once 'includes/config.php';
require_once 'includes/connection.php';

// Simular filtros aplicados
$marca_id = 1; // Simular marca seleccionada
$categoria_id = 2; // Simular categoría seleccionada
$busqueda = "simulador"; // Simular búsqueda

try {
    $pdo = getDB();
    
    // Obtener marca seleccionada
    $marca_sql = "SELECT * FROM catalogo_marcas WHERE id = ?";
    $marca_stmt = $pdo->prepare($marca_sql);
    $marca_stmt->execute([$marca_id]);
    $marca_seleccionada = $marca_stmt->fetch();
    
    // Obtener categoría seleccionada
    $categoria_sql = "SELECT * FROM catalogo_categorias WHERE id = ?";
    $categoria_stmt = $pdo->prepare($categoria_sql);
    $categoria_stmt->execute([$categoria_id]);
    $categoria_seleccionada = $categoria_stmt->fetch();
    
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Test Filtros Aplicados</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
        <link rel='stylesheet' href='assets/css/catalogo.css'>
    </head>
    <body>
        <div class='container mt-4'>
            <h1>Test Filtros Aplicados</h1>
            <p>Verificando visibilidad del texto en los filtros aplicados</p>
            
            <!-- Filtros Activos -->
            <div class='active-filters mb-4'>
                <div class='d-flex flex-wrap align-items-center'>
                    <span class='me-3 text-muted fw-semibold'>
                        <i class='bi bi-funnel me-1'></i>Filtros aplicados:
                    </span>
                    
                    <!-- Filtro de búsqueda -->
                    <span class='badge bg-primary me-2 mb-2'>
                        Búsqueda: \"{$busqueda}\"
                        <a href='#' class='btn-close btn-close-white ms-2' aria-label='Eliminar filtro de búsqueda'></a>
                    </span>
                    
                    <!-- Filtro de marca -->
                    <span class='badge bg-primary me-2 mb-2'>
                        Marca: " . ($marca_seleccionada['nombre'] ?? 'No encontrada') . "
                        <a href='#' class='btn-close btn-close-white ms-2' aria-label='Eliminar filtro de marca'></a>
                    </span>
                    
                    <!-- Filtro de categoría -->
                    <span class='badge bg-primary me-2 mb-2'>
                        Categoría: " . ($categoria_seleccionada['nombre'] ?? 'No encontrada') . "
                        <a href='#' class='btn-close btn-close-white ms-2' aria-label='Eliminar filtro de categoría'></a>
                    </span>
                    
                    <!-- Botón limpiar filtros -->
                    <a href='#' class='btn btn-sm clear-filters'>
                        <i class='bi bi-x-circle me-1'></i>Limpiar filtros
                    </a>
                </div>
            </div>
            
            <!-- Información de debug -->
            <div class='alert alert-info'>
                <h5>Información de Debug:</h5>
                <ul>
                    <li><strong>Marca ID:</strong> {$marca_id} - " . ($marca_seleccionada['nombre'] ?? 'No encontrada') . "</li>
                    <li><strong>Categoría ID:</strong> {$categoria_id} - " . ($categoria_seleccionada['nombre'] ?? 'No encontrada') . "</li>
                    <li><strong>Búsqueda:</strong> \"{$busqueda}\"</li>
                </ul>
            </div>
            
            <!-- Test de colores -->
            <div class='row mt-4'>
                <div class='col-md-6'>
                    <h5>Test de Colores de Texto:</h5>
                    <div class='badge bg-primary me-2 mb-2'>Texto normal</div>
                    <div class='badge bg-primary me-2 mb-2'><span>Texto en span</span></div>
                    <div class='badge bg-primary me-2 mb-2'><div>Texto en div</div></div>
                </div>
                <div class='col-md-6'>
                    <h5>Test de Contraste:</h5>
                    <div class='badge bg-primary me-2 mb-2' style='color: white !important;'>Texto blanco forzado</div>
                    <div class='badge bg-primary me-2 mb-2' style='color: #ffffff !important;'>Texto blanco hex</div>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
