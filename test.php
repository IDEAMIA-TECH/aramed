<?php
/**
 * Archivo de prueba para verificar el funcionamiento del sistema
 */

// Incluir configuración
require_once('includes/config.php');

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test - ARAmed</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .env-info { background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test del Sistema ARAmed</h1>
    <p>Fecha y hora: " . date('Y-m-d H:i:s') . "</p>
    
    <div class='env-info'>
        <h3>Información del Entorno</h3>
        <p><strong>Dominio:</strong> " . $_SERVER['HTTP_HOST'] . "</p>
        <p><strong>Entorno:</strong> " . (IS_DEVELOPMENT ? 'DESARROLLO' : 'PRODUCCIÓN') . "</p>
        <p><strong>Base de Datos:</strong> " . DB_NAME . "</p>
        <p><strong>Usuario DB:</strong> " . DB_USER . "</p>
        <p><strong>Debug:</strong> " . (ini_get('display_errors') ? 'HABILITADO' : 'DESHABILITADO') . "</p>
    </div>
";

// Test 1: Verificar PHP
echo "<div class='test-section'>
    <h2>1. Verificación de PHP</h2>
    <p><strong>Versión de PHP:</strong> " . phpversion() . "</p>
    <p><strong>Extensiones cargadas:</strong></p>
    <ul>";
$required_extensions = ['mysqli', 'session', 'json', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li class='success'>✓ $ext</li>";
    } else {
        echo "<li class='error'>✗ $ext</li>";
    }
}
echo "</ul></div>";

// Test 2: Verificar archivos críticos
echo "<div class='test-section'>
    <h2>2. Verificación de Archivos</h2>
    <ul>";
$critical_files = [
    'includes/config.php',
    'includes/connection.php',
    'includes/widgets.php',
    'css/modern-styles.css',
    '.htaccess'
];

foreach ($critical_files as $file) {
    if (file_exists($file)) {
        echo "<li class='success'>✓ $file</li>";
    } else {
        echo "<li class='error'>✗ $file</li>";
    }
}
echo "</ul></div>";

// Test 3: Verificar conexión a base de datos
echo "<div class='test-section'>
    <h2>3. Verificación de Base de Datos</h2>";

try {
    require_once('includes/connection.php');
    if (isset($CONEXION) && $CONEXION) {
        echo "<p class='success'>✓ Conexión a base de datos exitosa</p>";
        echo "<p><strong>Base de datos conectada:</strong> " . DB_NAME . "</p>";
        
        // Verificar tablas principales
        $tables = ['configuracion', 'productos', 'productoscat', 'user'];
        echo "<p><strong>Verificando tablas principales:</strong></p><ul>";
        foreach ($tables as $table) {
            $result = $CONEXION->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<li class='success'>✓ Tabla $table existe</li>";
            } else {
                echo "<li class='error'>✗ Tabla $table no existe</li>";
            }
        }
        echo "</ul>";
        
        mysqli_close($CONEXION);
    } else {
        echo "<p class='error'>✗ Error en la conexión a base de datos</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 4: Verificar permisos de directorios
echo "<div class='test-section'>
    <h2>4. Verificación de Permisos</h2>
    <ul>";
$directories = [
    'img/contenido' => 'Escritura de imágenes',
    'admin' => 'Acceso administrativo',
    'includes' => 'Archivos de configuración',
    'pages' => 'Páginas del sitio'
];

foreach ($directories as $dir => $description) {
    if (is_dir($dir)) {
        if (is_readable($dir)) {
            echo "<li class='success'>✓ $description ($dir) - Legible</li>";
        } else {
            echo "<li class='error'>✗ $description ($dir) - No legible</li>";
        }
    } else {
        echo "<li class='error'>✗ $description ($dir) - No existe</li>";
    }
}
echo "</ul></div>";

// Test 5: Verificar configuración del servidor
echo "<div class='test-section'>
    <h2>5. Configuración del Servidor</h2>
    <ul>
        <li><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</li>
        <li><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</li>
        <li><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>
        <li><strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "</li>
        <li><strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "</li>
        <li><strong>Protocolo:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP') . "</li>
    </ul>
</div>";

// Test 6: Verificar funciones personalizadas
echo "<div class='test-section'>
    <h2>6. Verificación de Funciones</h2>
    <ul>";
$functions = ['cleanInput', 'validateEmail', 'generateSlug', 'formatDate', 'getClientIP'];
foreach ($functions as $function) {
    if (function_exists($function)) {
        echo "<li class='success'>✓ Función $function disponible</li>";
    } else {
        echo "<li class='error'>✗ Función $function no disponible</li>";
    }
}
echo "</ul></div>";

// Test 7: Verificar sesión
echo "<div class='test-section'>
    <h2>7. Verificación de Sesión</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p class='success'>✓ Sesión iniciada correctamente</p>";
    echo "<p><strong>ID de sesión:</strong> " . session_id() . "</p>";
    echo "<p><strong>Nombre de sesión:</strong> " . session_name() . "</p>";
} else {
    echo "<p class='error'>✗ Sesión no iniciada</p>";
}
echo "</div>";

// Test 8: Verificar headers de seguridad (versión corregida)
echo "<div class='test-section'>
    <h2>8. Headers de Seguridad</h2>
    <ul>";
$security_headers = [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block'
];

foreach ($security_headers as $header => $expected_value) {
    // Usar getallheaders() en lugar de apache_getenv()
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $header_value = isset($headers[$header]) ? $headers[$header] : 'No configurado';
    
    if ($header_value === $expected_value) {
        echo "<li class='success'>✓ $header: $header_value</li>";
    } else {
        echo "<li class='warning'>⚠ $header: $header_value (esperado: $expected_value)</li>";
    }
}
echo "</ul></div>";

// Test 9: Verificar configuración específica del entorno
echo "<div class='test-section'>
    <h2>9. Configuración del Entorno</h2>
    <ul>
        <li><strong>IS_DEVELOPMENT:</strong> " . (IS_DEVELOPMENT ? 'true' : 'false') . "</li>
        <li><strong>SITE_URL:</strong> " . SITE_URL . "</li>
        <li><strong>SITE_PATH:</strong> " . SITE_PATH . "</li>
        <li><strong>DB_HOST:</strong> " . DB_HOST . "</li>
        <li><strong>DB_NAME:</strong> " . DB_NAME . "</li>
        <li><strong>DB_USER:</strong> " . DB_USER . "</li>
    </ul>
</div>";

echo "<div class='test-section'>
    <h2>Resumen</h2>
    <p>Si todos los tests muestran ✓ verde, el sistema está funcionando correctamente.</p>
    <p>Si hay errores (✗ rojo), revisa la configuración del servidor.</p>
    <p><strong>Entorno actual:</strong> " . (IS_DEVELOPMENT ? 'DESARROLLO (ideamia-dev.com)' : 'PRODUCCIÓN') . "</p>
    <p><a href='index.php'>← Volver al sitio principal</a></p>
</div>

</body>
</html>"; 