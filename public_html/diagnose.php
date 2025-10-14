<?php
/**
 * DIAGNÓSTICO RÁPIDO
 * Identificar qué está fallando
 */

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico del Sistema</h1>";
echo "<hr>";

// Test 1: PHP funciona
echo "<h2>✅ PHP está funcionando</h2>";
echo "<p>Versión: " . PHP_VERSION . "</p>";
echo "<hr>";

// Test 2: Verificar archivos
echo "<h2>📁 Verificando Archivos</h2>";
$files = [
    'includes/config.php',
    'includes/connection.php',
    'includes/functions.php',
    'includes/topbar.php',
    'includes/navbar.php',
    'includes/footer.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ {$file} existe</p>";
    } else {
        echo "<p style='color: red;'>❌ {$file} NO EXISTE</p>";
    }
}
echo "<hr>";

// Test 3: Intentar cargar config.php
echo "<h2>⚙️ Cargando config.php</h2>";
try {
    if (file_exists('includes/config.php')) {
        require_once 'includes/config.php';
        echo "<p style='color: green;'>✅ config.php cargado correctamente</p>";
        
        // Verificar constantes
        $constants = ['SITE_NAME', 'DB_HOST', 'DB_NAME', 'SMTP_HOST'];
        foreach ($constants as $const) {
            if (defined($const)) {
                echo "<p>✅ {$const} = " . constant($const) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ {$const} NO definido</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ config.php no existe</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "<hr>";

// Test 4: Intentar cargar functions.php
echo "<h2>🔧 Cargando functions.php</h2>";
try {
    if (file_exists('includes/functions.php')) {
        require_once 'includes/functions.php';
        echo "<p style='color: green;'>✅ functions.php cargado correctamente</p>";
        
        // Verificar funciones
        $functions = ['esc', 'siteUrl', 'assetUrl'];
        foreach ($functions as $func) {
            if (function_exists($func)) {
                echo "<p>✅ {$func}() existe</p>";
            } else {
                echo "<p style='color: red;'>❌ {$func}() NO existe</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ functions.php no existe</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "<hr>";

// Test 5: Directorio actual
echo "<h2>📍 Información del Sistema</h2>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Script actual:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Server software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
echo "<hr>";

// Test 6: Listar archivos en includes/
echo "<h2>📂 Contenido de includes/</h2>";
if (is_dir('includes')) {
    $files = scandir('includes');
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>{$file}</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ El directorio includes/ NO existe</p>";
}

echo "<hr>";
echo "<h2>✅ Diagnóstico Completado</h2>";
echo "<p>Si ves este mensaje, PHP está funcionando correctamente.</p>";
echo "<p>Revisa los errores arriba para identificar el problema.</p>";
?>

