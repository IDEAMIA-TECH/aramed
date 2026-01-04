<?php
/**
 * Script para probar que los logs funcionan
 * Genera errores de prueba y los escribe en el log
 */

// Configurar logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$log_file = __DIR__ . '/../logs/php-errors.log';
ini_set('error_log', $log_file);

echo "<h1>Test de Error Log</h1>";
echo "<pre>";

echo "=== CONFIGURACIÓN ===\n";
echo "Log file: $log_file\n";
echo "File exists: " . (file_exists($log_file) ? 'SÍ' : 'NO') . "\n";
echo "File writable: " . (is_writable($log_file) ? 'SÍ' : 'NO') . "\n";
echo "error_log config: " . ini_get('error_log') . "\n";
echo "\n";

echo "=== GENERANDO ERRORES DE PRUEBA ===\n";

// Error de prueba 1: error_log directo
$test1 = error_log("=== TEST 1 - " . date('Y-m-d H:i:s') . " - Mensaje de prueba directo ===");
echo "Test 1 (error_log directo): " . ($test1 ? "✓ Enviado" : "✗ Falló") . "\n";

// Error de prueba 2: trigger_error
trigger_error("=== TEST 2 - " . date('Y-m-d H:i:s') . " - Trigger error de prueba ===", E_USER_WARNING);
echo "Test 2 (trigger_error): ✓ Enviado\n";

// Error de prueba 3: Exception
try {
    throw new Exception("=== TEST 3 - " . date('Y-m-d H:i:s') . " - Exception de prueba ===");
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "Test 3 (Exception): ✓ Enviado\n";
}

// Error de prueba 4: Variable indefinida (generará warning)
$undefined_var = $no_existe;
echo "Test 4 (variable indefinida): ✓ Generado\n";

echo "\n=== CONTENIDO ACTUAL DEL LOG ===\n";
if (file_exists($log_file) && is_readable($log_file)) {
    $content = file_get_contents($log_file);
    echo $content;
    echo "\n--- FIN DEL CONTENIDO ---\n";
} else {
    echo "✗ No se puede leer el archivo de logs\n";
}

echo "\n=== VERIFICACIÓN ===\n";
echo "Tamaño del archivo: " . filesize($log_file) . " bytes\n";
echo "Última modificación: " . date('Y-m-d H:i:s', filemtime($log_file)) . "\n";

echo "\n</pre>";

echo "<hr>";
echo "<p><a href='view-logs-simple.php'>Ver logs en el visor</a> | ";
echo "<a href='index.php'>Volver al Dashboard</a></p>";
echo "<p><small>⚠ Elimina este archivo después de usar por seguridad.</small></p>";
?>

