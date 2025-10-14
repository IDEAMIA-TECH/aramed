<?php
/**
 * Test directo de newsletter_handler.php
 * Simula un envío POST para verificar qué está pasando
 */

echo "<h1>🧪 Test Directo de Newsletter Handler</h1>";
echo "<hr>";

// Simular datos POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'nombre_completo' => 'Test Usuario',
    'email_oficial' => 'test@example.com',
    'telefono' => '1234567890',
    'institucion' => 'Test Institution',
    'cargo' => 'Test Cargo',
    'pais' => 'México',
    'estado' => 'CDMX',
    'interes_productos' => 'Maniquíes',
    'interes_servicios' => 'Capacitación',
    'acepto_privacidad' => '1'
];

echo "<h2>1️⃣ Datos de prueba configurados</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<hr>";
echo "<h2>2️⃣ Verificando archivos requeridos...</h2>";

$required_files = [
    'includes/config.php',
    'includes/connection.php',
    'includes/functions.php',
    'includes/email_functions.php',
    'includes/debug_logger.php',
    'includes/newsletter_handler.php'
];

$all_exist = true;
foreach ($required_files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file NO EXISTE<br>";
        $all_exist = false;
    }
}

if (!$all_exist) {
    die("<br><strong style='color: red;'>❌ Faltan archivos requeridos. No se puede continuar.</strong>");
}

echo "<hr>";
echo "<h2>3️⃣ Verificando directorio de logs...</h2>";

$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    echo "⚠️ Directorio logs/ NO existe. Intentando crear...<br>";
    if (@mkdir($log_dir, 0755, true)) {
        echo "✅ Directorio logs/ creado exitosamente<br>";
    } else {
        echo "❌ NO se pudo crear el directorio logs/<br>";
    }
} else {
    echo "✅ Directorio logs/ existe<br>";
    if (is_writable($log_dir)) {
        echo "✅ Directorio logs/ es escribible<br>";
    } else {
        echo "❌ Directorio logs/ NO es escribible<br>";
    }
}

echo "<hr>";
echo "<h2>4️⃣ Ejecutando newsletter_handler.php...</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border: 1px solid #ccc; margin: 10px 0;'>";

// Capturar la salida del handler
ob_start();

try {
    include __DIR__ . '/includes/newsletter_handler.php';
    $output = ob_get_clean();
    
    echo "<h3>✅ Handler ejecutado</h3>";
    echo "<h4>Salida del handler:</h4>";
    echo "<pre style='background: white; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($output);
    echo "</pre>";
    
    // Intentar decodificar como JSON
    $json = json_decode($output, true);
    if ($json) {
        echo "<h4>JSON decodificado:</h4>";
        echo "<pre style='background: white; padding: 10px; border: 1px solid #ddd;'>";
        print_r($json);
        echo "</pre>";
        
        if (isset($json['success'])) {
            if ($json['success']) {
                echo "<p style='color: green; font-size: 20px; font-weight: bold;'>✅ ÉXITO</p>";
            } else {
                echo "<p style='color: red; font-size: 20px; font-weight: bold;'>❌ ERROR</p>";
                echo "<p><strong>Mensaje:</strong> " . ($json['message'] ?? 'Sin mensaje') . "</p>";
            }
        }
    }
    
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "<h3 style='color: red;'>❌ Excepción capturada</h3>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    
    if ($output) {
        echo "<h4>Salida antes de la excepción:</h4>";
        echo "<pre style='background: white; padding: 10px; border: 1px solid #ddd;'>";
        echo htmlspecialchars($output);
        echo "</pre>";
    }
}

echo "</div>";

echo "<hr>";
echo "<h2>5️⃣ Verificando si se creó el log...</h2>";

$log_file = __DIR__ . '/logs/debug.log';
if (file_exists($log_file)) {
    echo "✅ Archivo debug.log fue creado<br>";
    echo "<h4>Últimas 20 líneas del log:</h4>";
    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
    $last_lines = array_slice($lines, -20);
    echo "<pre style='background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px;'>";
    foreach ($last_lines as $line) {
        echo htmlspecialchars($line) . "\n";
    }
    echo "</pre>";
} else {
    echo "❌ Archivo debug.log NO fue creado<br>";
    echo "<p>Esto significa que debugLog() no está funcionando o no se ejecutó.</p>";
}

echo "<hr>";
echo "<h2>📋 RESUMEN</h2>";
echo "<p><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='view-debug-log.php'>Ver Debug Log completo</a></p>";
?>

