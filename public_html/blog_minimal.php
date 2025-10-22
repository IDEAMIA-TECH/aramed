<?php
/**
 * Versión mínima de blog.php para identificar el problema
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Función para capturar errores
function errorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
    echo "<strong>Error:</strong> $errstr<br>";
    echo "<strong>Archivo:</strong> $errfile<br>";
    echo "<strong>Línea:</strong> $errline<br>";
    echo "</div>";
    return true;
}

set_error_handler('errorHandler');

echo "<h1>🧪 Blog Mínimo - Diagnóstico</h1>";

try {
    echo "<p>Paso 1: Definiendo constante...</p>";
    define('ARAMED_SITE', true);
    
    echo "<p>Paso 2: Cargando config.php...</p>";
    require_once __DIR__ . '/includes/config.php';
    
    echo "<p>Paso 3: Cargando functions.php...</p>";
    require_once __DIR__ . '/includes/functions.php';
    
    echo "<p>Paso 4: Cargando connection.php...</p>";
    require_once __DIR__ . '/includes/connection.php';
    
    echo "<p>Paso 5: Obteniendo conexión...</p>";
    $pdo = getDB();
    
    echo "<p>Paso 6: Ejecutando consulta simple...</p>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM blog_articulos WHERE estado = 'publicado'");
    $result = $stmt->fetch();
    echo "<p>Artículos publicados: " . $result['total'] . "</p>";
    
    echo "<p>✅ Todos los pasos completados exitosamente</p>";
    
} catch (ParseError $e) {
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
    echo "<strong>Error de Sintaxis:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "</div>";
} catch (Error $e) {
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
    echo "<strong>Error Fatal:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
    echo "<strong>Excepción:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "</div>";
}
?>
