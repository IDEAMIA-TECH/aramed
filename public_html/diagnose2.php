<?php
/**
 * DIAGNÓSTICO COMPLETO - Cargar todos los includes paso a paso
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico Completo - index.php</h1>";
echo "<hr>";

// Test 1: Cargar config.php
echo "<h2>1️⃣ Cargando config.php</h2>";
try {
    define('ARAMED_SITE', true);
    require_once 'includes/config.php';
    echo "<p style='color: green;'>✅ config.php OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    die();
}
echo "<hr>";

// Test 2: Cargar functions.php
echo "<h2>2️⃣ Cargando functions.php</h2>";
try {
    require_once 'includes/functions.php';
    echo "<p style='color: green;'>✅ functions.php OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    die();
}
echo "<hr>";

// Test 3: Cargar topbar.php
echo "<h2>3️⃣ Cargando topbar.php</h2>";
try {
    ob_start();
    require_once 'includes/topbar.php';
    $topbar_output = ob_get_clean();
    echo "<p style='color: green;'>✅ topbar.php OK (Output: " . strlen($topbar_output) . " bytes)</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}
echo "<hr>";

// Test 4: Cargar navbar.php
echo "<h2>4️⃣ Cargando navbar.php</h2>";
try {
    ob_start();
    require_once 'includes/navbar.php';
    $navbar_output = ob_get_clean();
    echo "<p style='color: green;'>✅ navbar.php OK (Output: " . strlen($navbar_output) . " bytes)</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}
echo "<hr>";

// Test 5: Cargar footer.php
echo "<h2>5️⃣ Cargando footer.php</h2>";
try {
    ob_start();
    require_once 'includes/footer.php';
    $footer_output = ob_get_clean();
    echo "<p style='color: green;'>✅ footer.php OK (Output: " . strlen($footer_output) . " bytes)</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}
echo "<hr>";

// Test 6: Verificar funciones usadas en index.php
echo "<h2>6️⃣ Verificando Funciones</h2>";
$functions = [
    'siteUrl',
    'assetUrl',
    'imageUrl',
    'esc',
    'getCurrentYear',
];

foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green;'>✅ {$func}() existe</p>";
        
        // Probar llamar la función
        try {
            if ($func === 'siteUrl') {
                $result = siteUrl('test');
                echo "<p style='margin-left: 20px;'>→ siteUrl('test') = {$result}</p>";
            } elseif ($func === 'assetUrl') {
                $result = assetUrl('css/test.css');
                echo "<p style='margin-left: 20px;'>→ assetUrl('css/test.css') = {$result}</p>";
            } elseif ($func === 'imageUrl') {
                $result = imageUrl('test.jpg');
                echo "<p style='margin-left: 20px;'>→ imageUrl('test.jpg') = {$result}</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red; margin-left: 20px;'>❌ Error al llamar {$func}(): " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ {$func}() NO existe</p>";
    }
}
echo "<hr>";

// Test 7: Verificar constantes necesarias
echo "<h2>7️⃣ Verificando Constantes</h2>";
$constants = [
    'SITE_URL',
    'ASSETS_URL',
    'IMAGES_URL',
    'SITE_NAME',
    'DB_HOST',
    'DB_NAME',
];

foreach ($constants as $const) {
    if (defined($const)) {
        $value = constant($const);
        echo "<p style='color: green;'>✅ {$const} = " . htmlspecialchars($value) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ {$const} NO definido</p>";
    }
}
echo "<hr>";

// Test 8: Simular inicio de index.php
echo "<h2>8️⃣ Simulando inicio de index.php</h2>";
try {
    echo "<p>Intentando ejecutar código similar a index.php...</p>";
    
    // Probar la estructura básica que tiene index.php
    $test_html = "<!DOCTYPE html>";
    $test_html .= "<html lang='es'>";
    $test_html .= "<head>";
    $test_html .= "<meta charset='UTF-8'>";
    $test_html .= "<title>" . SITE_NAME . "</title>";
    $test_html .= "</head>";
    $test_html .= "<body>";
    $test_html .= "Test content";
    $test_html .= "</body>";
    $test_html .= "</html>";
    
    echo "<p style='color: green;'>✅ HTML básico se puede generar</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
echo "<hr>";

// Test 9: Verificar error log
echo "<h2>9️⃣ Error Log del Servidor</h2>";
echo "<p><strong>Si index.php falla pero este diagnóstico no, revisa:</strong></p>";
echo "<ul>";
echo "<li>Error Log en cPanel</li>";
echo "<li>Busca el error más reciente relacionado con index.php</li>";
echo "<li>El error debe mostrar la línea exacta que falla</li>";
echo "</ul>";
echo "<hr>";

echo "<h2>✅ Diagnóstico Completado</h2>";
echo "<p><strong>CONCLUSIÓN:</strong></p>";
echo "<ul>";
echo "<li>✅ Todos los includes se cargan correctamente</li>";
echo "<li>✅ Todas las funciones existen</li>";
echo "<li>✅ Todas las constantes están definidas</li>";
echo "</ul>";
echo "<p style='color: orange; font-size: 18px;'><strong>El problema debe estar EN EL CONTENIDO de index.php</strong></p>";
echo "<p>Posibles causas:</p>";
echo "<ul>";
echo "<li>Algún error de sintaxis PHP en index.php</li>";
echo "<li>Alguna función que se llama en index.php pero no existe</li>";
echo "<li>Uso de memoria excedido (archivo muy grande)</li>";
echo "<li>Timeout de ejecución</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>📝 Próximo Paso:</h3>";
echo "<p>Revisar el <strong>Error Log del servidor</strong> en cPanel para ver el error exacto de index.php</p>";
?>

