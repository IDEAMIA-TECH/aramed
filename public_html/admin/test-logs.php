<?php
/**
 * Script de prueba para verificar que los logs funcionan
 * ELIMINAR DESPUÉS DE USAR
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Configurar logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

$log_file = __DIR__ . '/../logs/php-errors.log';
ini_set('error_log', $log_file);

echo "<h1>Test de Logs</h1>";
echo "<pre>";

echo "=== INFORMACIÓN DEL SISTEMA ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Archivo de log: " . $log_file . "\n";
echo "Archivo existe: " . (file_exists($log_file) ? 'SÍ' : 'NO') . "\n";
echo "Archivo es legible: " . (is_readable($log_file) ? 'SÍ' : 'NO') . "\n";
echo "Archivo es escribible: " . (is_writable($log_file) ? 'SÍ' : 'NO') . "\n";
echo "Permisos del archivo: " . substr(sprintf('%o', fileperms($log_file)), -4) . "\n";
echo "Tamaño del archivo: " . filesize($log_file) . " bytes\n";
echo "\n";

echo "=== PRUEBA DE ESCRITURA ===\n";
$test_message = "=== TEST LOG - " . date('Y-m-d H:i:s') . " ===\n";
if (error_log($test_message)) {
    echo "✓ error_log() ejecutado correctamente\n";
} else {
    echo "✗ error_log() falló\n";
}

echo "\n=== CONTENIDO DEL ARCHIVO DE LOGS ===\n";
if (file_exists($log_file) && is_readable($log_file)) {
    $content = file_get_contents($log_file);
    echo $content;
    echo "\n--- FIN DEL CONTENIDO ---\n";
} else {
    echo "✗ No se puede leer el archivo de logs\n";
}

echo "\n=== PRUEBA DE FUNCIONES ===\n";
echo "function_exists('esc'): " . (function_exists('esc') ? 'SÍ' : 'NO') . "\n";
echo "defined('SITE_NAME'): " . (defined('SITE_NAME') ? 'SÍ (' . SITE_NAME . ')' : 'NO') . "\n";

echo "\n=== PRUEBA DE CARGA DE ARCHIVOS ===\n";
$files_to_check = [
    '../includes/config.php',
    '../includes/functions.php',
    '../includes/connection.php',
    'auth_check.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    echo $file . ": " . (file_exists($full_path) ? '✓ Existe' : '✗ No existe') . "\n";
}

echo "\n=== PRUEBA DE SESIÓN ===\n";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Sesión iniciada: " . (session_status() === PHP_SESSION_ACTIVE ? 'SÍ' : 'NO') . "\n";
echo "admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? 'SÍ' : 'NO') . "\n";
echo "admin_rol: " . ($_SESSION['admin_rol'] ?? 'NO DEFINIDO') . "\n";

echo "</pre>";

echo "<hr>";
echo "<p><strong>⚠ IMPORTANTE:</strong> Elimina este archivo después de usarlo por seguridad.</p>";
echo "<p><a href='view-logs.php'>Ver logs desde el visor</a> | <a href='index.php'>Volver al Dashboard</a></p>";
?>

