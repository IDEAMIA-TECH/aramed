<?php
/**
 * ========================================
 * ARAMED - ROUTER PARA PÁGINAS ESTÁTICAS
 * ========================================
 * 
 * Maneja el routing de páginas estáticas personalizadas
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/connection.php';

// Obtener slug de la URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    // Si no hay slug, intentar obtenerlo de la URL
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $request_uri = parse_url($request_uri, PHP_URL_PATH);
    $request_uri = trim($request_uri, '/');
    
    // Remover query string
    $slug = explode('?', $request_uri)[0];
    $slug = trim($slug, '/');
}

if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    header('HTTP/1.0 500 Internal Server Error');
    die('Error de conexión a la base de datos');
}

// Buscar página estática
try {
    $stmt = $pdo->prepare("SELECT * FROM paginas_estaticas WHERE slug = ? AND estado = 'publicado'");
    $stmt->execute([$slug]);
    $pagina = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pagina) {
        header('HTTP/1.0 404 Not Found');
        include __DIR__ . '/404.php';
        exit;
    }
} catch (Exception $e) {
    // Tabla puede no existir
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

// Variables para meta tags
$pageTitle = !empty($pagina['meta_titulo']) ? $pagina['meta_titulo'] : $pagina['titulo'] . ' - ' . SITE_NAME;
$pageDescription = !empty($pagina['meta_descripcion']) ? $pagina['meta_descripcion'] : SITE_DESCRIPTION;
$pageKeywords = !empty($pagina['meta_keywords']) ? $pagina['meta_keywords'] : SITE_KEYWORDS;
$pageUrl = SITE_URL . '/' . $pagina['slug'];
$pageImage = !empty($pagina['imagen_principal']) ? imageUrl($pagina['imagen_principal']) : imageUrl('design/logo-og.jpg');

// Cargar header
include __DIR__ . '/includes/header.php';
?>

<!-- Página Estática -->
<main id="main">
    <?php if (!empty($pagina['imagen_principal'])): ?>
    <section class="page-hero" style="background-image: url('<?php echo imageUrl($pagina['imagen_principal']); ?>');">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1><?php echo esc($pagina['titulo']); ?></h1>
                </div>
            </div>
        </div>
    </section>
    <?php else: ?>
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1><?php echo esc($pagina['titulo']); ?></h1>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="page-content">
        <div class="container">
            <div class="row">
                <?php if ($pagina['plantilla'] === 'sidebar'): ?>
                    <div class="col-lg-8">
                        <div class="page-body">
                            <?php echo $pagina['contenido']; ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <aside class="page-sidebar">
                            <!-- Sidebar content puede agregarse aquí -->
                        </aside>
                    </div>
                <?php elseif ($pagina['plantilla'] === 'full-width'): ?>
                    <div class="col-lg-12">
                        <div class="page-body">
                            <?php echo $pagina['contenido']; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-lg-10 offset-lg-1">
                        <div class="page-body">
                            <?php echo $pagina['contenido']; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php
// Cargar footer
include __DIR__ . '/includes/footer.php';
?>

