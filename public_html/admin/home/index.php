<?php
/**
 * ========================================
 * ADMIN - DASHBOARD DEL GESTOR DE HOME
 * ========================================
 * 
 * Panel principal para gestionar el contenido del inicio
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('home', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Estadísticas del home
$stats = [];

try {
    // Verificar si las tablas existen
    $tablas_existen = true;
    $tablas_requeridas = ['home_banners', 'home_productos_destacados', 'home_servicios', 'home_mision_vision', 'home_categorias_destacadas'];
    
    foreach ($tablas_requeridas as $tabla) {
        $stmt = $pdo->query("SHOW TABLES LIKE '{$tabla}'");
        if ($stmt->rowCount() === 0) {
            $tablas_existen = false;
            break;
        }
    }
    
    if ($tablas_existen) {
        // Total de banners
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM home_banners");
        $stats['total_banners'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Banners publicados
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM home_banners WHERE estado = 'publicado'");
        $stats['banners_publicados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Productos destacados
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM home_productos_destacados WHERE estado = 'activo'");
        $stats['productos_destacados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Servicios activos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM home_servicios WHERE estado = 'activo'");
        $stats['servicios_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Categorías destacadas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM home_categorias_destacadas WHERE estado = 'activo'");
        $stats['categorias_destacadas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Verificar misión y visión
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM home_mision_vision WHERE estado = 'activo'");
        $stats['mision_vision'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
} catch (Exception $e) {
    $error_message = 'Error al cargar estadísticas: ' . $e->getMessage();
}

$current_page = 'index.php';
$current_dir = 'home';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Home - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --dark-color: #212529;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: var(--shadow);
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .section-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .section-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .section-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-house-door me-2"></i>Gestor de Inicio
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona el contenido de la página de inicio</p>
                        </div>
                        <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-light">
                            <i class="bi bi-eye me-2"></i>Ver Inicio
                        </a>
                    </div>
                </div>
                
                <?php if (!$tablas_existen): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Las tablas de Home no existen.</strong> Ejecuta el script SQL: 
                    <code>database/fase2/01_create_home_tables.sql</code>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <?php if ($tablas_existen): ?>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo number_format($stats['total_banners'] ?? 0); ?></div>
                            <div class="stats-label">Total Banners</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-success"><?php echo number_format($stats['banners_publicados'] ?? 0); ?></div>
                            <div class="stats-label">Banners Publicados</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-info"><?php echo number_format($stats['productos_destacados'] ?? 0); ?></div>
                            <div class="stats-label">Productos Destacados</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-primary"><?php echo number_format($stats['servicios_activos'] ?? 0); ?></div>
                            <div class="stats-label">Servicios Activos</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Secciones -->
                <div class="row">
                    <!-- Banners/Hero -->
                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="text-center">
                                <div class="section-icon">
                                    <i class="bi bi-image"></i>
                                </div>
                                <h4 class="mb-3">Banners / Hero</h4>
                                <p class="text-muted mb-3">Gestiona los banners del inicio (carrusel hero)</p>
                                <a href="banners.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-right me-2"></i>Gestionar Banners
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Productos Destacados -->
                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="text-center">
                                <div class="section-icon">
                                    <i class="bi bi-star"></i>
                                </div>
                                <h4 class="mb-3">Productos Destacados</h4>
                                <p class="text-muted mb-3">Selecciona y ordena los productos destacados</p>
                                <a href="productos-destacados.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-right me-2"></i>Gestionar Productos
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Servicios -->
                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="text-center">
                                <div class="section-icon">
                                    <i class="bi bi-gear"></i>
                                </div>
                                <h4 class="mb-3">Servicios</h4>
                                <p class="text-muted mb-3">Gestiona los servicios mostrados en el inicio</p>
                                <a href="servicios.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-right me-2"></i>Gestionar Servicios
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Misión y Visión -->
                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="text-center">
                                <div class="section-icon">
                                    <i class="bi bi-bullseye"></i>
                                </div>
                                <h4 class="mb-3">Misión y Visión</h4>
                                <p class="text-muted mb-3">Edita el contenido de misión y visión</p>
                                <a href="mision-vision.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-right me-2"></i>Editar Misión/Visión
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Categorías Destacadas -->
                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="text-center">
                                <div class="section-icon">
                                    <i class="bi bi-folder"></i>
                                </div>
                                <h4 class="mb-3">Categorías Destacadas</h4>
                                <p class="text-muted mb-3">Selecciona categorías para destacar</p>
                                <a href="categorias-destacadas.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-right me-2"></i>Gestionar Categorías
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

