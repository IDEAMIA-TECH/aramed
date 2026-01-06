<?php
/**
 * ========================================
 * ADMIN - SEO & METADATOS
 * ========================================
 * 
 * Panel principal de gestión SEO
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
    checkPermission('seo', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Obtener estadísticas
$stats = [];

try {
    // Total de redirecciones
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM redirects WHERE estado = 'activo'");
    $stats['redirects_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de metadatos personalizados
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM seo_metadatos");
    $stats['metadatos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos sin metadatos
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM catalogo_productos p
        LEFT JOIN seo_metadatos m ON m.tipo_entidad = 'producto' AND m.entidad_id = p.id
        WHERE p.estado = 'activo' AND m.id IS NULL
    ");
    $stats['productos_sin_seo'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Artículos sin metadatos
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM blog_articulos a
        LEFT JOIN seo_metadatos m ON m.tipo_entidad = 'articulo' AND m.entidad_id = a.id
        WHERE a.estado = 'publicado' AND m.id IS NULL
    ");
    $stats['articulos_sin_seo'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch (Exception $e) {
    // Si las tablas no existen aún, usar valores por defecto
    $stats = [
        'redirects_activos' => 0,
        'metadatos' => 0,
        'productos_sin_seo' => 0,
        'articulos_sin_seo' => 0
    ];
}

$current_page = 'index.php';
$current_dir = 'seo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO & Metadatos - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --dark-color: #212529;
            --border-radius: 8px;
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .seo-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .seo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            color: inherit;
        }
        
        .seo-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
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
                                <i class="bi bi-search me-2"></i>SEO & Metadatos
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona la optimización para motores de búsqueda</p>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-primary"><?php echo number_format($stats['redirects_activos']); ?></h3>
                            <small class="text-muted">Redirecciones Activas</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-info"><?php echo number_format($stats['metadatos']); ?></h3>
                            <small class="text-muted">Metadatos Personalizados</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-warning"><?php echo number_format($stats['productos_sin_seo']); ?></h3>
                            <small class="text-muted">Productos sin SEO</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-warning"><?php echo number_format($stats['articulos_sin_seo']); ?></h3>
                            <small class="text-muted">Artículos sin SEO</small>
                        </div>
                    </div>
                </div>
                
                <!-- Opciones SEO -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <a href="config.php" class="seo-card">
                            <div class="seo-icon">
                                <i class="bi bi-gear"></i>
                            </div>
                            <h5>Configuración Global</h5>
                            <p class="text-muted mb-0">Configuración SEO global, títulos por defecto, favicon, Open Graph</p>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <a href="redirects.php" class="seo-card">
                            <div class="seo-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                            <h5>Redirecciones</h5>
                            <p class="text-muted mb-0">Gestiona redirecciones 301/302 para URLs antiguas</p>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <a href="sitemap.php" class="seo-card">
                            <div class="seo-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <h5>Sitemap XML</h5>
                            <p class="text-muted mb-0">Genera y gestiona el sitemap.xml dinámicamente</p>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <a href="robots.php" class="seo-card">
                            <div class="seo-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h5>Robots.txt</h5>
                            <p class="text-muted mb-0">Edita el archivo robots.txt desde el admin</p>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <a href="schema.php" class="seo-card">
                            <div class="seo-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="bi bi-code-square"></i>
                            </div>
                            <h5>Schema.org</h5>
                            <p class="text-muted mb-0">Configura datos estructurados JSON-LD</p>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <a href="metadatos.php" class="seo-card">
                            <div class="seo-icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <i class="bi bi-tags"></i>
                            </div>
                            <h5>Metadatos Personalizados</h5>
                            <p class="text-muted mb-0">Gestiona metadatos específicos por producto, artículo, etc.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

