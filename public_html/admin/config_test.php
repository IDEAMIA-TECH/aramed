<?php
/**
 * Prueba de configuración de base de datos
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Prueba de Configuración</h1>";

// Verificar si existe el archivo de configuración
$config_file = __DIR__ . '/../includes/config.php';
if (file_exists($config_file)) {
    echo "<p style='color: green;'>✓ Archivo config.php existe</p>";
    
    // Leer el archivo de configuración
    $config_content = file_get_contents($config_file);
    echo "<h3>Contenido del archivo config.php:</h3>";
    echo "<pre>" . htmlspecialchars($config_content) . "</pre>";
    
} else {
    echo "<p style='color: red;'>✗ Archivo config.php no existe</p>";
}

// Verificar si existe el archivo de conexión
$connection_file = __DIR__ . '/../includes/connection.php';
if (file_exists($connection_file)) {
    echo "<p style='color: green;'>✓ Archivo connection.php existe</p>";
    
    // Leer el archivo de conexión
    $connection_content = file_get_contents($connection_file);
    echo "<h3>Contenido del archivo connection.php:</h3>";
    echo "<pre>" . htmlspecialchars($connection_content) . "</pre>";
    
} else {
    echo "<p style='color: red;'>✗ Archivo connection.php no existe</p>";
}

// Verificar variables de entorno
echo "<h3>Variables de entorno:</h3>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>SCRIPT_FILENAME:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p><strong>HTTP_HOST:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

// Verificar permisos
echo "<h3>Permisos de archivos:</h3>";
$files_to_check = [
    '../includes/config.php',
    '../includes/connection.php',
    '../includes/functions.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        $permissions = substr(sprintf('%o', fileperms($full_path)), -4);
        echo "<p><strong>$file:</strong> $permissions</p>";
    }
}

echo "<hr>";
echo "<p><a href='login_debug.php'>Debug del Login</a></p>";
echo "<p><a href='test.php'>Archivo de Prueba</a></p>";
?>
