<?php
/**
 * Debug del menú - TEMPORAL
 * Eliminar después de usar
 */

define('ARAMED_SITE', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/auth_check.php';

// Simular estar en catalogo/index.php
$_SERVER['PHP_SELF'] = '/admin/catalogo/index.php';

// Código de detección del menú
$php_self = $_SERVER['PHP_SELF'] ?? '';
$current_page = basename($php_self);

$admin_path = '/admin/';
$pos = strpos($php_self, $admin_path);

if ($pos !== false) {
    $relative_path = substr($php_self, $pos + strlen($admin_path));
    $path_parts = explode('/', $relative_path);
    
    if (count($path_parts) > 1 && !empty($path_parts[0])) {
        $current_dir = $path_parts[0];
        $base_path = '../';
    } else {
        $current_dir = '';
        $base_path = '';
    }
} else {
    $script_dir = dirname($php_self);
    $current_dir = basename($script_dir);
    
    if ($current_dir === '.' || $current_dir === '/' || $current_dir === 'admin' || empty($current_dir)) {
        $current_dir = '';
        $base_path = '';
    } else {
        $base_path = '../';
    }
}

echo "<h1>Debug del Menú</h1>";
echo "<pre>";
echo "PHP_SELF: " . htmlspecialchars($php_self) . "\n";
echo "current_page: " . htmlspecialchars($current_page) . "\n";
echo "current_dir: " . htmlspecialchars($current_dir) . "\n";
echo "base_path: " . htmlspecialchars($base_path) . "\n";
echo "path_parts: " . print_r($path_parts ?? [], true) . "\n";
echo "¿current_dir === 'catalogo'? " . ($current_dir === 'catalogo' ? 'SÍ' : 'NO') . "\n";
echo "</pre>";
?>

