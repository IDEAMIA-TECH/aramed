<?php
/**
 * VERSIÓN DEBUG DEL HANDLER
 * Con logs detallados línea por línea
 */

// Activar TODOS los errores
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar en pantalla
ini_set('log_errors', 1);

// Función simple de log
function simpleLog($msg) {
    $log_file = __DIR__ . '/../logs/simple_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($log_file, "[$timestamp] $msg\n", FILE_APPEND);
    error_log($msg);
}

simpleLog("========== HANDLER DEBUG START ==========");
simpleLog("Script: " . __FILE__);
simpleLog("Time: " . date('Y-m-d H:i:s'));

// Línea 15: Define ARAMED_SITE
simpleLog("→ Definiendo ARAMED_SITE...");
try {
    define('ARAMED_SITE', true);
    simpleLog("✅ ARAMED_SITE definido");
} catch (Exception $e) {
    simpleLog("❌ Error definiendo ARAMED_SITE: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Error en línea 15']));
}

// Línea 16: config.php
simpleLog("→ Cargando config.php...");
try {
    require_once __DIR__ . '/config.php';
    simpleLog("✅ config.php cargado");
} catch (Exception $e) {
    simpleLog("❌ Error cargando config.php: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Error en línea 16']));
}

// Línea 17: connection.php
simpleLog("→ Cargando connection.php...");
try {
    require_once __DIR__ . '/connection.php';
    simpleLog("✅ connection.php cargado");
} catch (Exception $e) {
    simpleLog("❌ Error cargando connection.php: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Error en línea 17']));
}

// Línea 18: functions.php
simpleLog("→ Cargando functions.php...");
try {
    require_once __DIR__ . '/functions.php';
    simpleLog("✅ functions.php cargado");
} catch (Exception $e) {
    simpleLog("❌ Error cargando functions.php: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Error en línea 18']));
}

// Línea 19: email_functions.php
simpleLog("→ Cargando email_functions.php...");
try {
    require_once __DIR__ . '/email_functions.php';
    simpleLog("✅ email_functions.php cargado");
} catch (Exception $e) {
    simpleLog("❌ Error cargando email_functions.php: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Error en línea 19']));
}

// Línea 20: debug_logger.php
simpleLog("→ Cargando debug_logger.php...");
try {
    require_once __DIR__ . '/debug_logger.php';
    simpleLog("✅ debug_logger.php cargado");
} catch (Exception $e) {
    simpleLog("❌ Error cargando debug_logger.php: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Error en línea 20']));
}

// Línea 23: Headers
simpleLog("→ Enviando headers JSON...");
try {
    header('Content-Type: application/json');
    simpleLog("✅ Headers enviados");
} catch (Exception $e) {
    simpleLog("❌ Error enviando headers: " . $e->getMessage());
}

// Línea 26-31: Obtener PDO
simpleLog("→ Obteniendo conexión PDO...");
try {
    $pdo = getDB();
    if (!$pdo) {
        simpleLog("❌ getDB() retornó false/null");
        throw new Exception("No se pudo conectar a la base de datos");
    }
    simpleLog("✅ PDO obtenido correctamente");
} catch (Exception $e) {
    simpleLog("❌ Error en PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()
    ]);
    exit;
}

// Si llegamos aquí, todo está bien
simpleLog("✅ TODAS LAS CARGAS EXITOSAS - Handler funcionando");

// Verificar método
simpleLog("→ Verificando método HTTP...");
simpleLog("Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    simpleLog("❌ Método no es POST");
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

simpleLog("✅ Método POST confirmado");

// Procesar datos
simpleLog("→ Procesando datos POST...");
simpleLog("POST data keys: " . implode(', ', array_keys($_POST)));

// Aquí puedes continuar con la lógica normal del handler
// Por ahora, solo retornamos éxito para confirmar que llegamos hasta aquí

simpleLog("✅ Handler completado exitosamente");

echo json_encode([
    'success' => true,
    'message' => 'Handler debug funcionando correctamente',
    'data' => [
        'post_keys' => array_keys($_POST),
        'timestamp' => date('Y-m-d H:i:s')
    ]
]);

simpleLog("========== HANDLER DEBUG END ==========");
?>

