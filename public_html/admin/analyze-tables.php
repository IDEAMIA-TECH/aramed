<?php
/**
 * ========================================
 * ADMIN - ANALIZAR TABLAS
 * ========================================
 * 
 * Script para ejecutar ANALYZE TABLE en todas las tablas principales
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('configuracion', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Lista de tablas a analizar
$tables = [
    // Catálogo
    'catalogo_productos',
    'catalogo_categorias',
    'catalogo_marcas',
    
    // Blog
    'blog_articulos',
    'blog_categorias',
    'blog_comentarios',
    
    // Contacto
    'contact_messages',
    
    // Cotizaciones
    'cotizaciones',
    'cotizacion_items',
    
    // Newsletter
    'newsletter_subscriptions',
    'newsletter_simple',
    
    // Proyectos
    'proyectos',
    'proyecto_imagenes',
    'proyecto_documentos',
    
    // Home
    'home_banners',
    'home_productos_destacados',
    'home_servicios',
    'home_aliados',
    'home_mision_vision',
    'home_categorias_destacadas',
    'home_secciones',
    
    // SEO
    'seo_config',
    'seo_metadatos',
    'redirects',
    
    // Apariencia
    'paginas_estaticas',
    
    // Sistema
    'admin_usuarios',
    'permisos',
    'rol_permisos',
    'audit_logs',
    'configuracion'
];

$results = [];
$total_tables = 0;
$successful = 0;
$failed = 0;
$skipped = 0;

// Procesar cada tabla
foreach ($tables as $table) {
    $total_tables++;
    
    try {
        // Verificar que la tabla existe
        $stmt_check = $pdo->query("SHOW TABLES LIKE '$table'");
        $check_result = $stmt_check->fetchAll(PDO::FETCH_ASSOC);
        $stmt_check->closeCursor(); // Cerrar cursor antes de continuar
        
        if (empty($check_result)) {
            $results[] = [
                'table' => $table,
                'status' => 'skipped',
                'message' => 'Tabla no existe'
            ];
            $skipped++;
            continue;
        }
        
        // Ejecutar ANALYZE TABLE
        // IMPORTANTE: ANALYZE TABLE devuelve un resultado que DEBE leerse
        // Si no se lee, la consulta queda activa y causa el error
        $start_time = microtime(true);
        $stmt_analyze = $pdo->query("ANALYZE TABLE `$table`");
        $analyze_result = $stmt_analyze->fetchAll(PDO::FETCH_ASSOC); // Leer el resultado para cerrar la consulta
        $stmt_analyze->closeCursor(); // Cerrar cursor explícitamente
        $end_time = microtime(true);
        $duration = round(($end_time - $start_time) * 1000, 2); // en milisegundos
        
        $results[] = [
            'table' => $table,
            'status' => 'success',
            'message' => 'Analizada correctamente',
            'duration' => $duration . ' ms'
        ];
        $successful++;
        
    } catch (Exception $e) {
        $results[] = [
            'table' => $table,
            'status' => 'error',
            'message' => $e->getMessage()
        ];
        $failed++;
    }
}

// Registrar actividad
if (function_exists('logActivity')) {
    logActivity($current_user['id'], 'editar', 'configuracion', null, 'analyze_tables', [
        'total' => $total_tables,
        'successful' => $successful,
        'failed' => $failed,
        'skipped' => $skipped
    ]);
}

$current_page = 'analyze-tables.php';
$current_dir = 'configuracion';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analizar Tablas - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0066cc;
            --dark-color: #212529;
            --border-radius: 12px;
            --shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .result-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-left: 4px solid #dee2e6;
        }
        
        .result-card.success {
            border-left-color: #28a745;
        }
        
        .result-card.error {
            border-left-color: #dc3545;
        }
        
        .result-card.skipped {
            border-left-color: #ffc107;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-speedometer2 me-2"></i>Analizar Tablas
                            </h2>
                            <p class="mb-0 opacity-75">Actualizar estadísticas de índices para optimizar consultas</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Configuración
                        </a>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="stats-card">
                    <div class="row">
                        <div class="col-md-3 stat-item">
                            <div class="stat-number"><?php echo $total_tables; ?></div>
                            <div class="stat-label">Total de Tablas</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-number text-success"><?php echo $successful; ?></div>
                            <div class="stat-label">Exitosas</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-number text-danger"><?php echo $failed; ?></div>
                            <div class="stat-label">Fallidas</div>
                        </div>
                        <div class="col-md-3 stat-item">
                            <div class="stat-number text-warning"><?php echo $skipped; ?></div>
                            <div class="stat-label">Omitidas</div>
                        </div>
                    </div>
                </div>
                
                <!-- Resultados -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Resultados del Análisis</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($results as $result): ?>
                        <div class="result-card <?php echo $result['status']; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo esc($result['table']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo esc($result['message']); ?></small>
                                    <?php if (isset($result['duration'])): ?>
                                    <br>
                                    <small class="text-muted">Duración: <?php echo esc($result['duration']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($result['status'] === 'success'): ?>
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                    <?php elseif ($result['status'] === 'error'): ?>
                                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 1.5rem;"></i>
                                    <?php else: ?>
                                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 1.5rem;"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Acciones -->
                <div class="mt-3">
                    <a href="?reload=1" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Ejecutar Nuevamente
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver a Configuración
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

