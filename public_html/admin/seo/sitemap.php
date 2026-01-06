<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE SITEMAP.XML
 * ========================================
 * 
 * Generación dinámica de sitemap.xml
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

// Verificar que el usuario sea admin (SEO es solo para admin)
$user_role = $_SESSION['admin_rol'] ?? 'editor';
if ($user_role !== 'admin') {
    header('Location: ../sin-permiso.php?modulo=seo&accion=ver');
    exit;
}

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

$success_message = '';
$error_message = '';

// Generar sitemap
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    try {
        $sitemap_file = __DIR__ . '/../../sitemap.xml';
        
        // Iniciar XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n\n";
        
        // Página principal
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . SITE_URL . "/</loc>\n";
        $xml .= "        <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "        <changefreq>weekly</changefreq>\n";
        $xml .= "        <priority>1.0</priority>\n";
        $xml .= "    </url>\n\n";
        
        // Páginas estáticas
        $static_pages = [
            'catalogo.php' => ['freq' => 'weekly', 'priority' => '0.9'],
            'blog.php' => ['freq' => 'daily', 'priority' => '0.8'],
            'proyectos.php' => ['freq' => 'weekly', 'priority' => '0.8'],
            'contacto.php' => ['freq' => 'monthly', 'priority' => '0.5'],
            'privacidad.php' => ['freq' => 'yearly', 'priority' => '0.3'],
            'terminos.php' => ['freq' => 'yearly', 'priority' => '0.3'],
            'cookies.php' => ['freq' => 'yearly', 'priority' => '0.3']
        ];
        
        foreach ($static_pages as $page => $config) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>" . SITE_URL . "/" . $page . "</loc>\n";
            $xml .= "        <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "        <changefreq>" . $config['freq'] . "</changefreq>\n";
            $xml .= "        <priority>" . $config['priority'] . "</priority>\n";
            $xml .= "    </url>\n\n";
        }
        
        // Productos
        try {
            $stmt = $pdo->query("SELECT slug, updated_at FROM catalogo_productos WHERE estado = 'activo' ORDER BY updated_at DESC");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($productos as $producto) {
                $xml .= "    <url>\n";
                $xml .= "        <loc>" . SITE_URL . "/producto.php?slug=" . urlencode($producto['slug']) . "</loc>\n";
                $xml .= "        <lastmod>" . date('Y-m-d', strtotime($producto['updated_at'])) . "</lastmod>\n";
                $xml .= "        <changefreq>weekly</changefreq>\n";
                $xml .= "        <priority>0.7</priority>\n";
                $xml .= "    </url>\n\n";
            }
        } catch (Exception $e) {
            // Tabla no existe
        }
        
        // Artículos del blog
        try {
            $stmt = $pdo->query("SELECT slug, updated_at FROM blog_articulos WHERE estado = 'publicado' ORDER BY updated_at DESC");
            $articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($articulos as $articulo) {
                $xml .= "    <url>\n";
                $xml .= "        <loc>" . SITE_URL . "/blog-detalle.php?slug=" . urlencode($articulo['slug']) . "</loc>\n";
                $xml .= "        <lastmod>" . date('Y-m-d', strtotime($articulo['updated_at'])) . "</lastmod>\n";
                $xml .= "        <changefreq>monthly</changefreq>\n";
                $xml .= "        <priority>0.6</priority>\n";
                $xml .= "    </url>\n\n";
            }
        } catch (Exception $e) {
            // Tabla no existe
        }
        
        // Proyectos
        try {
            $stmt = $pdo->query("SELECT slug, updated_at FROM proyectos WHERE estado = 'publicado' ORDER BY updated_at DESC");
            $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($proyectos as $proyecto) {
                $xml .= "    <url>\n";
                $xml .= "        <loc>" . SITE_URL . "/proyecto.php?slug=" . urlencode($proyecto['slug']) . "</loc>\n";
                $xml .= "        <lastmod>" . date('Y-m-d', strtotime($proyecto['updated_at'])) . "</lastmod>\n";
                $xml .= "        <changefreq>monthly</changefreq>\n";
                $xml .= "        <priority>0.6</priority>\n";
                $xml .= "    </url>\n\n";
            }
        } catch (Exception $e) {
            // Tabla no existe
        }
        
        // Cerrar XML
        $xml .= "</urlset>\n";
        
        // Guardar archivo
        if (file_put_contents($sitemap_file, $xml) === false) {
            throw new Exception('Error al guardar el archivo sitemap.xml');
        }
        
        $success_message = 'Sitemap.xml generado exitosamente';
        
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'generar', 'seo', null, 'sitemap.xml generado');
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Leer sitemap actual
$sitemap_content = '';
$sitemap_file = __DIR__ . '/../../sitemap.xml';
if (file_exists($sitemap_file)) {
    $sitemap_content = file_get_contents($sitemap_file);
}

// Contar URLs
$url_count = 0;
if ($sitemap_content) {
    $url_count = substr_count($sitemap_content, '<url>');
}

$current_page = 'sitemap.php';
$current_dir = 'seo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitemap XML - SEO Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
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
                                <i class="bi bi-diagram-3 me-2"></i>Gestión de Sitemap XML
                            </h2>
                            <p class="mb-0 opacity-75">Genera y gestiona el sitemap.xml dinámicamente</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-primary"><?php echo number_format($url_count); ?></h3>
                            <small class="text-muted">URLs en Sitemap</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-info">
                                <?php echo file_exists($sitemap_file) ? date('d/m/Y H:i', filemtime($sitemap_file)) : 'N/A'; ?>
                            </h3>
                            <small class="text-muted">Última Generación</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-success">
                                <?php echo file_exists($sitemap_file) ? number_format(filesize($sitemap_file) / 1024, 2) . ' KB' : 'N/A'; ?>
                            </h3>
                            <small class="text-muted">Tamaño del Archivo</small>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST" action="" class="d-inline">
                            <input type="hidden" name="action" value="generate">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Regenerar Sitemap
                            </button>
                        </form>
                        <a href="<?php echo SITE_URL; ?>/sitemap.xml" target="_blank" class="btn btn-outline-secondary">
                            <i class="bi bi-eye me-2"></i>Ver Sitemap Actual
                        </a>
                        <a href="https://search.google.com/search-console" target="_blank" class="btn btn-outline-info">
                            <i class="bi bi-google me-2"></i>Enviar a Google Search Console
                        </a>
                    </div>
                </div>
                
                <!-- Vista Previa -->
                <?php if ($sitemap_content): ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>Vista Previa del Sitemap
                        </h5>
                    </div>
                    <div class="card-body">
                        <pre><?php echo esc($sitemap_content); ?></pre>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

