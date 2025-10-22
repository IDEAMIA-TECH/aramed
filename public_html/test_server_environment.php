<?php
/**
 * Script de diagnóstico del entorno del servidor
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🔍 Diagnóstico del Entorno del Servidor</h1>";

// 1. Información del servidor
echo "<h2>1. Información del Servidor</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'No disponible') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No disponible') . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No disponible') . "</p>";

// 2. Configuración de errores
echo "<h2>2. Configuración de Errores</h2>";
echo "<p><strong>error_reporting:</strong> " . error_reporting() . "</p>";
echo "<p><strong>display_errors:</strong> " . ini_get('display_errors') . "</p>";
echo "<p><strong>log_errors:</strong> " . ini_get('log_errors') . "</p>";
echo "<p><strong>error_log:</strong> " . ini_get('error_log') . "</p>";

// 3. Límites del servidor
echo "<h2>3. Límites del Servidor</h2>";
echo "<p><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "</p>";
echo "<p><strong>max_input_vars:</strong> " . ini_get('max_input_vars') . "</p>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";

// 4. Verificar archivos críticos
echo "<h2>4. Verificación de Archivos</h2>";
$archivos = [
    'blog.php',
    'includes/config.php',
    'includes/functions.php',
    'includes/connection.php',
    'includes/navbar.php',
    'includes/footer.php'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "<p>✅ $archivo - Existe</p>";
        if (is_readable($archivo)) {
            echo "<p>   ✅ Legible</p>";
        } else {
            echo "<p>   ❌ NO legible</p>";
        }
    } else {
        echo "<p>❌ $archivo - NO existe</p>";
    }
}

// 5. Probar carga de archivos paso a paso
echo "<h2>5. Prueba de Carga Paso a Paso</h2>";

try {
    echo "<p>🔄 Definiendo constante ARAMED_SITE...</p>";
    define('ARAMED_SITE', true);
    echo "<p>✅ Constante definida</p>";
    
    echo "<p>🔄 Cargando config.php...</p>";
    require_once __DIR__ . '/includes/config.php';
    echo "<p>✅ config.php cargado</p>";
    echo "<p>   SITE_URL: " . (defined('SITE_URL') ? SITE_URL : 'NO DEFINIDO') . "</p>";
    
    echo "<p>🔄 Cargando functions.php...</p>";
    require_once __DIR__ . '/includes/functions.php';
    echo "<p>✅ functions.php cargado</p>";
    
    echo "<p>🔄 Cargando connection.php...</p>";
    require_once __DIR__ . '/includes/connection.php';
    echo "<p>✅ connection.php cargado</p>";
    
    echo "<p>🔄 Obteniendo conexión PDO...</p>";
    $pdo = getDB();
    if ($pdo) {
        echo "<p>✅ Conexión PDO exitosa</p>";
    } else {
        echo "<p>❌ getDB() retornó false</p>";
    }
    
} catch (ParseError $e) {
    echo "<p>❌ Error de sintaxis: " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Error fatal: " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Excepción: " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
}

// 6. Verificar permisos de directorios
echo "<h2>6. Permisos de Directorios</h2>";
$directorios = [
    '.',
    'includes',
    'assets',
    'assets/images',
    'assets/images/blog'
];

foreach ($directorios as $dir) {
    if (is_dir($dir)) {
        $perms = fileperms($dir);
        $readable = is_readable($dir);
        $writable = is_writable($dir);
        echo "<p>📁 $dir - Permisos: " . substr(sprintf('%o', $perms), -4) . " - Legible: " . ($readable ? 'Sí' : 'No') . " - Escribible: " . ($writable ? 'Sí' : 'No') . "</p>";
    } else {
        echo "<p>❌ $dir - NO es directorio</p>";
    }
}

// 7. Verificar logs de error del servidor
echo "<h2>7. Logs de Error</h2>";
$possible_logs = [
    '/var/log/apache2/error.log',
    '/var/log/httpd/error_log',
    '/var/log/nginx/error.log',
    ini_get('error_log'),
    __DIR__ . '/error.log',
    __DIR__ . '/logs/error.log'
];

foreach ($possible_logs as $log) {
    if ($log && file_exists($log)) {
        echo "<p>📄 Log encontrado: $log</p>";
        $log_content = file_get_contents($log);
        $recent_errors = array_slice(explode("\n", $log_content), -20);
        echo "<p>Últimos 20 errores:</p><pre style='background: #f5f5f5; padding: 10px; border-radius: 4px;'>";
        foreach ($recent_errors as $error) {
            if (!empty(trim($error))) {
                echo htmlspecialchars($error) . "\n";
            }
        }
        echo "</pre>";
        break;
    }
}

// 8. Probar sintaxis de blog.php
echo "<h2>8. Verificación de Sintaxis de blog.php</h2>";
$syntax_check = shell_exec('php -l blog.php 2>&1');
if ($syntax_check) {
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 4px;'>" . htmlspecialchars($syntax_check) . "</pre>";
} else {
    echo "<p>✅ Sintaxis de blog.php correcta</p>";
}

echo "<h2>✅ Diagnóstico completado</h2>";
?>
