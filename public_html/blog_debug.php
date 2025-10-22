<?php
/**
 * Versión de debug de blog.php con manejo de errores robusto
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Función para manejar errores
function handleError($errno, $errstr, $errfile, $errline) {
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
    echo "<strong>Error:</strong> $errstr<br>";
    echo "<strong>Archivo:</strong> $errfile<br>";
    echo "<strong>Línea:</strong> $errline<br>";
    echo "</div>";
    return true;
}

// Función para manejar excepciones
function handleException($exception) {
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
    echo "<strong>Excepción:</strong> " . $exception->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $exception->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $exception->getLine() . "<br>";
    echo "<strong>Stack trace:</strong><br><pre>" . $exception->getTraceAsString() . "</pre>";
    echo "</div>";
}

// Configurar manejadores de errores
set_error_handler('handleError');
set_exception_handler('handleException');

echo "<h1>Debug de blog.php</h1>";

try {
    // Definir constante del sitio
    define('ARAMED_SITE', true);
    echo "<p>✅ Constante ARAMED_SITE definida</p>";

    // Cargar configuración
    echo "<p>🔄 Cargando config.php...</p>";
    require_once __DIR__ . '/includes/config.php';
    echo "<p>✅ config.php cargado</p>";

    echo "<p>🔄 Cargando functions.php...</p>";
    require_once __DIR__ . '/includes/functions.php';
    echo "<p>✅ functions.php cargado</p>";

    echo "<p>🔄 Cargando connection.php...</p>";
    require_once __DIR__ . '/includes/connection.php';
    echo "<p>✅ connection.php cargado</p>";

    // Obtener conexión PDO
    echo "<p>🔄 Obteniendo conexión PDO...</p>";
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception('Error de conexión a la base de datos');
    }
    echo "<p>✅ Conexión PDO exitosa</p>";

    // Obtener parámetros de filtro
    $categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
    $busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;

    echo "<p>✅ Parámetros procesados: categoría=$categoria_id, búsqueda='$busqueda', página=$page</p>";

    // Construir consulta de artículos
    $where_conditions = ['a.estado = "publicado"'];
    $params = [];

    if ($categoria_id > 0) {
        $where_conditions[] = 'a.categoria_id = ?';
        $params[] = $categoria_id;
    }

    if (!empty($busqueda)) {
        $where_conditions[] = '(a.titulo LIKE ? OR a.extracto LIKE ? OR a.contenido LIKE ?)';
        $search_term = "%{$busqueda}%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }

    $where_clause = implode(' AND ', $where_conditions);
    echo "<p>✅ WHERE clause: $where_clause</p>";

    // Obtener artículos
    $sql_articulos = "
        SELECT a.*, c.nombre as categoria_nombre, c.slug as categoria_slug, c.color as categoria_color, c.icono as categoria_icono
        FROM blog_articulos a
        LEFT JOIN blog_categorias c ON a.categoria_id = c.id
        WHERE {$where_clause}
        ORDER BY a.destacado DESC, a.fecha_publicacion DESC
        LIMIT {$per_page} OFFSET {$offset}
    ";

    echo "<p>🔄 Ejecutando consulta de artículos...</p>";
    $stmt_articulos = $pdo->prepare($sql_articulos);
    $stmt_articulos->execute($params);
    $articulos = $stmt_articulos->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>✅ Artículos obtenidos: " . count($articulos) . "</p>";

    // Contar total de artículos para paginación
    $sql_count = "
        SELECT COUNT(*) as total
        FROM blog_articulos a
        LEFT JOIN blog_categorias c ON a.categoria_id = c.id
        WHERE {$where_clause}
    ";

    echo "<p>🔄 Ejecutando consulta de conteo...</p>";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_articulos = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_articulos / $per_page);
    echo "<p>✅ Total de artículos: $total_articulos, Total de páginas: $total_pages</p>";

    // Obtener categorías para filtros
    $sql_categorias = "
        SELECT c.*, COUNT(a.id) as articulos_count
        FROM blog_categorias c
        LEFT JOIN blog_articulos a ON c.id = a.categoria_id AND a.estado = 'publicado'
        WHERE c.estado = 'activo'
        GROUP BY c.id
        HAVING articulos_count > 0
        ORDER BY c.nombre ASC
    ";

    echo "<p>🔄 Ejecutando consulta de categorías...</p>";
    $stmt_categorias = $pdo->prepare($sql_categorias);
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>✅ Categorías obtenidas: " . count($categorias) . "</p>";

    // Mostrar algunos datos
    echo "<h2>Datos obtenidos:</h2>";
    echo "<h3>Artículos:</h3>";
    foreach ($articulos as $articulo) {
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px; border-radius: 4px;'>";
        echo "<strong>Título:</strong> " . esc($articulo['titulo']) . "<br>";
        echo "<strong>Extracto:</strong> " . esc(truncateText($articulo['extracto'] ?? '', 100)) . "<br>";
        echo "<strong>Fecha:</strong> " . $articulo['fecha_publicacion'] . "<br>";
        echo "<strong>Estado:</strong> " . $articulo['estado'] . "<br>";
        echo "</div>";
    }

    echo "<h3>Categorías:</h3>";
    foreach ($categorias as $categoria) {
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px; border-radius: 4px;'>";
        echo "<strong>Nombre:</strong> " . esc($categoria['nombre']) . "<br>";
        echo "<strong>Artículos:</strong> " . $categoria['articulos_count'] . "<br>";
        echo "</div>";
    }

    echo "<h2>✅ Todas las operaciones completadas exitosamente</h2>";
    echo "<p>blog.php debería funcionar correctamente.</p>";

} catch (Exception $e) {
    echo "<h2>❌ Error capturado:</h2>";
    handleException($e);
}
?>
