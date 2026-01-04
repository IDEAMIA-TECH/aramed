<?php
// Script de información del servidor
// NO requiere autenticación para debugging

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Info</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        h2 { color: #333; border-bottom: 2px solid #0066cc; padding-bottom: 5px; }
        pre { background: #f8f8f8; padding: 10px; border-left: 3px solid #0066cc; overflow-x: auto; }
        .ok { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>🔍 Información del Servidor</h1>
    
    <div class="section">
        <h2>📁 Rutas y Archivos</h2>
        <pre>
__DIR__: <?php echo __DIR__; ?>

__FILE__: <?php echo __FILE__; ?>

Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'NO DEFINIDO'; ?>

Script Name: <?php echo $_SERVER['SCRIPT_NAME'] ?? 'NO DEFINIDO'; ?>

Request URI: <?php echo $_SERVER['REQUEST_URI'] ?? 'NO DEFINIDO'; ?>

PHP Self: <?php echo $_SERVER['PHP_SELF'] ?? 'NO DEFINIDO'; ?>
        </pre>
    </div>
    
    <div class="section">
        <h2>🌐 URLs</h2>
        <pre>
HTTP Host: <?php echo $_SERVER['HTTP_HOST'] ?? 'NO DEFINIDO'; ?>

HTTPS: <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'SÍ' : 'NO'; ?>

URL Completa: <?php 
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    echo $protocol . '://' . $host . $uri;
?>
        </pre>
    </div>
    
    <div class="section">
        <h2>✅ Verificación de Archivos</h2>
        <pre>
<?php
$files_to_check = [
    'test-minimal.php' => __DIR__ . '/test-minimal.php',
    'test-echo.php' => __DIR__ . '/test-echo.php',
    'test-simple.php' => __DIR__ . '/test-simple.php',
    'test-logs.php' => __DIR__ . '/test-logs.php',
    'view-logs.php' => __DIR__ . '/view-logs.php',
    'index.php' => __DIR__ . '/index.php',
    'login.php' => __DIR__ . '/login.php',
];

foreach ($files_to_check as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    $status = $exists && $readable ? '<span class="ok">✓ Existe y es legible</span>' : '<span class="error">✗ No existe o no es legible</span>';
    echo "$name: $status\n";
    if ($exists) {
        echo "  Ruta: $path\n";
        echo "  Tamaño: " . filesize($path) . " bytes\n";
        echo "  Permisos: " . substr(sprintf('%o', fileperms($path)), -4) . "\n";
    }
    echo "\n";
}
?>
        </pre>
    </div>
    
    <div class="section">
        <h2>🐘 PHP Info</h2>
        <pre>
PHP Version: <?php echo phpversion(); ?>

SAPI: <?php echo php_sapi_name(); ?>

Error Reporting: <?php echo error_reporting(); ?>

Display Errors: <?php echo ini_get('display_errors') ? 'On' : 'Off'; ?>

Log Errors: <?php echo ini_get('log_errors') ? 'On' : 'Off'; ?>

Error Log: <?php echo ini_get('error_log') ?: 'NO CONFIGURADO'; ?>
        </pre>
    </div>
    
    <div class="section">
        <h2>📂 Directorio Actual</h2>
        <pre>
<?php
$files = scandir(__DIR__);
echo "Archivos en " . __DIR__ . ":\n\n";
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $full_path = __DIR__ . '/' . $file;
        $type = is_dir($full_path) ? '[DIR]' : '[FILE]';
        $size = is_file($full_path) ? filesize($full_path) . ' bytes' : '';
        echo "$type $file $size\n";
    }
}
?>
        </pre>
    </div>
    
    <div class="section">
        <h2>🔗 Enlaces de Prueba</h2>
        <pre>
<?php
$base_url = $protocol . '://' . $host;
$admin_path = dirname($_SERVER['PHP_SELF']);

$test_files = ['test-minimal.php', 'test-echo.php', 'test-simple.php', 'test-logs.php', 'view-logs.php', 'index.php'];
foreach ($test_files as $file) {
    $url = $base_url . $admin_path . '/' . $file;
    echo "<a href='$url' target='_blank'>$file</a>\n";
}
?>
        </pre>
    </div>
    
    <hr>
    <p><small>⚠ Este archivo debe eliminarse después de usar por seguridad.</small></p>
</body>
</html>

