<?php
/**
 * Script de prueba para verificar la carga del CSS
 */
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test CSS Loading</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
    <link rel='stylesheet' href='assets/css/catalogo.css'>
    <style>
        /* Test adicional */
        .test-filter-badge {
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
        
        .test-filter-badge * {
            color: #ffffff !important;
        }
    </style>
</head>
<body>
    <div class='container mt-4'>
        <h1>Test CSS Loading</h1>
        <p>Verificando que el CSS se carga correctamente</p>
        
        <!-- Test 1: Con clase del CSS externo -->
        <div class='mb-4'>
            <h3>Test 1: Con clase filter-badge del CSS externo</h3>
            <span class='badge filter-badge me-2 mb-2'>
                <span>Filtro de prueba</span>
                <a href='#' class='btn-close btn-close-white ms-2'></a>
            </span>
        </div>
        
        <!-- Test 2: Con clase del CSS interno -->
        <div class='mb-4'>
            <h3>Test 2: Con clase test-filter-badge del CSS interno</h3>
            <span class='badge test-filter-badge me-2 mb-2'>
                <span>Filtro de prueba</span>
                <a href='#' class='btn-close btn-close-white ms-2'></a>
            </span>
        </div>
        
        <!-- Test 3: Con estilos inline -->
        <div class='mb-4'>
            <h3>Test 3: Con estilos inline</h3>
            <span class='badge me-2 mb-2' style='color: #ffffff !important; background-color: #0066cc !important; padding: 0.5rem 0.75rem !important; border-radius: 25px !important;'>
                <span style='color: #ffffff !important;'>Filtro de prueba</span>
                <a href='#' class='btn-close btn-close-white ms-2'></a>
            </span>
        </div>
        
        <!-- Test 4: Información de debug -->
        <div class='alert alert-info'>
            <h5>Información de Debug:</h5>
            <ul>
                <li><strong>CSS Externo:</strong> assets/css/catalogo.css</li>
                <li><strong>Clase Externa:</strong> .filter-badge</li>
                <li><strong>Clase Interna:</strong> .test-filter-badge</li>
                <li><strong>Estilos Inline:</strong> style='...'</li>
            </ul>
        </div>
    </div>
</body>
</html>";
?>
