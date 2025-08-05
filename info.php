<?php
// Archivo de información PHP para diagnóstico
echo "<!DOCTYPE html>
<html>
<head>
    <title>PHP Info - ARAmed</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>Información del Sistema ARAmed</h1>
    <p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>
    <p><strong>Servidor:</strong> " . $_SERVER['SERVER_NAME'] . "</p>
    <p><strong>IP del servidor:</strong> " . ($_SERVER['SERVER_ADDR'] ?? 'No disponible') . "</p>
    <p><strong>Versión de PHP:</strong> " . phpversion() . "</p>
    <p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>
    <p><strong>Script Path:</strong> " . __FILE__ . "</p>
    
    <h2>Extensiones PHP</h2>
    <ul>
        <li class='" . (extension_loaded('mysqli') ? 'success' : 'error') . "'>mysqli: " . (extension_loaded('mysqli') ? '✓ Cargada' : '✗ No disponible') . "</li>
        <li class='" . (extension_loaded('session') ? 'success' : 'error') . "'>session: " . (extension_loaded('session') ? '✓ Cargada' : '✗ No disponible') . "</li>
        <li class='" . (extension_loaded('json') ? 'success' : 'error') . "'>json: " . (extension_loaded('json') ? '✓ Cargada' : '✗ No disponible') . "</li>
        <li class='" . (extension_loaded('mbstring') ? 'success' : 'error') . "'>mbstring: " . (extension_loaded('mbstring') ? '✓ Cargada' : '✗ No disponible') . "</li>
    </ul>
    
    <h2>Configuración PHP</h2>
    <ul>
        <li><strong>display_errors:</strong> " . (ini_get('display_errors') ? 'On' : 'Off') . "</li>
        <li><strong>error_reporting:</strong> " . ini_get('error_reporting') . "</li>
        <li><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</li>
        <li><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "</li>
        <li><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</li>
    </ul>
    
    <h2>Archivos del Sistema</h2>
    <ul>";

$files = [
    'includes/config.php',
    'includes/connection.php',
    'includes/widgets.php',
    'index.php',
    '.htaccess'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<li class='success'>✓ $file existe</li>";
    } else {
        echo "<li class='error'>✗ $file no existe</li>";
    }
}

echo "</ul>
    
    <h2>Prueba de Conexión a Base de Datos</h2>";

try {
    // Intentar conectar con configuración básica
    $host = 'localhost';
    $user = 'root';
    $pass = 'minimusa2000';
    $db = 'aramed';
    
    $connection = mysqli_connect($host, $user, $pass, $db);
    if ($connection) {
        echo "<p class='success'>✓ Conexión exitosa a base de datos</p>";
        echo "<p><strong>Base de datos:</strong> $db</p>";
        echo "<p><strong>Usuario:</strong> $user</p>";
        
        // Verificar tablas
        $tables = ['configuracion', 'productos', 'user'];
        echo "<p><strong>Tablas verificadas:</strong></p><ul>";
        foreach ($tables as $table) {
            $result = $connection->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<li class='success'>✓ Tabla $table existe</li>";
            } else {
                echo "<li class='error'>✗ Tabla $table no existe</li>";
            }
        }
        echo "</ul>";
        
        mysqli_close($connection);
    } else {
        echo "<p class='error'>✗ Error de conexión: " . mysqli_connect_error() . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Excepción: " . $e->getMessage() . "</p>";
}

echo "<h2>Variables de Entorno</h2>
    <ul>
        <li><strong>HTTP_HOST:</strong> " . $_SERVER['HTTP_HOST'] . "</li>
        <li><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</li>
        <li><strong>SERVER_ADDR:</strong> " . ($_SERVER['SERVER_ADDR'] ?? 'No disponible') . "</li>
        <li><strong>REMOTE_ADDR:</strong> " . ($_SERVER['REMOTE_ADDR'] ?? 'No disponible') . "</li>
    </ul>
    
    <p><a href='index.php'>← Volver al sitio principal</a></p>
</body>
</html>";
?> 