<?php
/**
 * Script de prueba específico para verificar la corrección de filtros aplicados
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
        <title>Test Filtros Aplicados - FIX</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
        <link rel='stylesheet' href='assets/css/catalogo.css'>
        <style>
            /* Estilos adicionales para forzar visibilidad */
            .test-badge {
                color: #ffffff !important;
                background-color: #0066cc !important;
                border: 2px solid #0066cc !important;
                font-weight: 600 !important;
                padding: 0.5rem 0.75rem !important;
                border-radius: 25px !important;
                box-shadow: 0 2px 4px rgba(0, 102, 204, 0.2) !important;
                font-size: 0.8rem !important;
                margin-right: 0.5rem !important;
                margin-bottom: 0.5rem !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
            
            .test-badge * {
                color: #ffffff !important;
                text-shadow: none !important;
            }
            
            .test-badge a {
                color: #ffffff !important;
                text-decoration: none !important;
            }
        </style>
    </head>
    <body>
        <div class='container mt-4'>
            <h1>Test Filtros Aplicados - FIX</h1>
            <p>Verificando que el texto ahora es visible en los filtros aplicados</p>
            
            <!-- Test 1: Filtros con estilos inline -->
            <div class='active-filters mb-4'>
                <h3>Test 1: Con estilos inline (debería funcionar)</h3>
                <div class='d-flex flex-wrap align-items-center'>
                    <span class='me-3 text-muted fw-semibold'>
                        <i class='bi bi-funnel me-1'></i>Filtros aplicados:
                    </span>
                    
                    <span class='badge bg-primary me-2 mb-2' style='color: #ffffff !important; background-color: #0066cc !important;'>
                        <span style='color: #ffffff !important;'>Búsqueda: \"{$busqueda}\"</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                    
                    <span class='badge bg-primary me-2 mb-2' style='color: #ffffff !important; background-color: #0066cc !important;'>
                        <span style='color: #ffffff !important;'>Marca: " . ($marca_seleccionada['nombre'] ?? 'No encontrada') . "</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                    
                    <span class='badge bg-primary me-2 mb-2' style='color: #ffffff !important; background-color: #0066cc !important;'>
                        <span style='color: #ffffff !important;'>Categoría: " . ($categoria_seleccionada['nombre'] ?? 'No encontrada') . "</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                </div>
            </div>
            
            <!-- Test 2: Filtros con clase personalizada -->
            <div class='active-filters mb-4'>
                <h3>Test 2: Con clase personalizada</h3>
                <div class='d-flex flex-wrap align-items-center'>
                    <span class='me-3 text-muted fw-semibold'>
                        <i class='bi bi-funnel me-1'></i>Filtros aplicados:
                    </span>
                    
                    <span class='test-badge'>
                        <span>Búsqueda: \"{$busqueda}\"</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                    
                    <span class='test-badge'>
                        <span>Marca: " . ($marca_seleccionada['nombre'] ?? 'No encontrada') . "</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                    
                    <span class='test-badge'>
                        <span>Categoría: " . ($categoria_seleccionada['nombre'] ?? 'No encontrada') . "</span>
                        <a href='#' class='btn-close btn-close-white ms-2'></a>
                    </span>
                </div>
            </div>
            
            <!-- Test 3: Comparación de colores -->
            <div class='row mt-4'>
                <div class='col-md-6'>
                    <h4>Test de Contraste:</h4>
                    <div class='badge bg-primary me-2 mb-2' style='color: white !important;'>Texto blanco forzado</div>
                    <div class='badge bg-primary me-2 mb-2' style='color: #ffffff !important;'>Texto blanco hex</div>
                    <div class='badge bg-primary me-2 mb-2' style='color: #000000 !important;'>Texto negro (para comparar)</div>
                </div>
                <div class='col-md-6'>
                    <h4>Test de Fondo:</h4>
                    <div class='badge me-2 mb-2' style='background-color: #0066cc !important; color: white !important;'>Fondo azul</div>
                    <div class='badge me-2 mb-2' style='background-color: #28a745 !important; color: white !important;'>Fondo verde</div>
                    <div class='badge me-2 mb-2' style='background-color: #dc3545 !important; color: white !important;'>Fondo rojo</div>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
