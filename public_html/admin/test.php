<?php
/**
 * Archivo de prueba para verificar que el directorio admin funciona
 */

// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test del Directorio Admin</h1>";
echo "<p>Si puedes ver este mensaje, el directorio admin está funcionando correctamente.</p>";

// Verificar configuración PHP
echo "<h2>Información del Servidor</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Verificar si los archivos de configuración existen
echo "<h2>Verificación de Archivos</h2>";

$files_to_check = [
    '../includes/config.php',
    '../includes/functions.php',
    '../includes/connection.php',
    'login.php',
    'auth_check.php',
    'index.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "<p style='color: green;'>✓ $file - Existe</p>";
    } else {
        echo "<p style='color: red;'>✗ $file - No existe</p>";
    }
}

// Verificar permisos de directorio
echo "<h2>Permisos de Directorio</h2>";
$dir_permissions = substr(sprintf('%o', fileperms(__DIR__)), -4);
echo "<p><strong>Permisos del directorio admin:</strong> $dir_permissions</p>";

// Verificar si se puede escribir en el directorio
if (is_writable(__DIR__)) {
    echo "<p style='color: green;'>✓ El directorio es escribible</p>";
} else {
    echo "<p style='color: red;'>✗ El directorio no es escribible</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Ir al Login</a></p>";
echo "<p><a href='index.php'>Ir al Dashboard</a></p>";
?>
