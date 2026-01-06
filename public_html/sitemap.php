<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Sitemap Generator
 * ========================================
 * 
 * Genera sitemap.xml dinámicamente con el Content-Type correcto
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Establecer headers XML ANTES de cualquier output
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/connection.php';

// Obtener conexión PDO
try {
    $pdo = getDB();
} catch (Exception $e) {
    $pdo = null;
}

// Iniciar XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n\n";

// Página principal
echo "    <url>\n";
echo "        <loc>" . SITE_URL . "/</loc>\n";
echo "        <lastmod>" . date('Y-m-d') . "</lastmod>\n";
echo "        <changefreq>weekly</changefreq>\n";
echo "        <priority>1.0</priority>\n";
echo "    </url>\n\n";

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
    $file_path = __DIR__ . '/' . $page;
    if (file_exists($file_path)) {
        $lastmod = date('Y-m-d', filemtime($file_path));
        echo "    <url>\n";
        echo "        <loc>" . SITE_URL . "/" . $page . "</loc>\n";
        echo "        <lastmod>" . $lastmod . "</lastmod>\n";
        echo "        <changefreq>" . $config['freq'] . "</changefreq>\n";
        echo "        <priority>" . $config['priority'] . "</priority>\n";
        echo "    </url>\n\n";
    }
}

// Productos
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, updated_at FROM catalogo_productos WHERE estado = 'activo' ORDER BY updated_at DESC");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        foreach ($productos as $producto) {
            $lastmod = $producto['updated_at'] ? date('Y-m-d', strtotime($producto['updated_at'])) : date('Y-m-d');
            echo "    <url>\n";
            echo "        <loc>" . SITE_URL . "/producto.php?slug=" . urlencode($producto['slug']) . "</loc>\n";
            echo "        <lastmod>" . $lastmod . "</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.7</priority>\n";
            echo "    </url>\n\n";
        }
    } catch (Exception $e) {
        // Tabla no existe o error
    }
}

// Artículos del blog
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, updated_at FROM blog_articulos WHERE estado = 'publicado' ORDER BY updated_at DESC");
        $articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        foreach ($articulos as $articulo) {
            $lastmod = $articulo['updated_at'] ? date('Y-m-d', strtotime($articulo['updated_at'])) : date('Y-m-d');
            echo "    <url>\n";
            echo "        <loc>" . SITE_URL . "/blog-detalle.php?slug=" . urlencode($articulo['slug']) . "</loc>\n";
            echo "        <lastmod>" . $lastmod . "</lastmod>\n";
            echo "        <changefreq>monthly</changefreq>\n";
            echo "        <priority>0.6</priority>\n";
            echo "    </url>\n\n";
        }
    } catch (Exception $e) {
        // Tabla no existe o error
    }
}

// Proyectos
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, updated_at FROM proyectos WHERE estado = 'publicado' ORDER BY updated_at DESC");
        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        foreach ($proyectos as $proyecto) {
            $lastmod = $proyecto['updated_at'] ? date('Y-m-d', strtotime($proyecto['updated_at'])) : date('Y-m-d');
            echo "    <url>\n";
            echo "        <loc>" . SITE_URL . "/proyecto.php?slug=" . urlencode($proyecto['slug']) . "</loc>\n";
            echo "        <lastmod>" . $lastmod . "</lastmod>\n";
            echo "        <changefreq>monthly</changefreq>\n";
            echo "        <priority>0.6</priority>\n";
            echo "    </url>\n\n";
        }
    } catch (Exception $e) {
        // Tabla no existe o error
    }
}

// Cerrar XML
echo "</urlset>\n";

