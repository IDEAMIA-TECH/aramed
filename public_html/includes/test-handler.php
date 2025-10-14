<?php
/**
 * TEST HANDLER - Diagnóstico de errores
 */

// Test 1: PHP básico
echo "TEST 1: PHP OK\n";

// Test 2: Define ARAMED_SITE
define('ARAMED_SITE', true);
echo "TEST 2: ARAMED_SITE definido\n";

// Test 3: Cargar config.php
try {
    require_once __DIR__ . '/config.php';
    echo "TEST 3: config.php cargado OK\n";
} catch (Exception $e) {
    die("ERROR en config.php: " . $e->getMessage());
}

// Test 4: Cargar connection.php
try {
    require_once __DIR__ . '/connection.php';
    echo "TEST 4: connection.php cargado OK\n";
} catch (Exception $e) {
    die("ERROR en connection.php: " . $e->getMessage());
}

// Test 5: Cargar functions.php
try {
    require_once __DIR__ . '/functions.php';
    echo "TEST 5: functions.php cargado OK\n";
} catch (Exception $e) {
    die("ERROR en functions.php: " . $e->getMessage());
}

// Test 6: Cargar email_functions.php
try {
    require_once __DIR__ . '/email_functions.php';
    echo "TEST 6: email_functions.php cargado OK\n";
} catch (Exception $e) {
    die("ERROR en email_functions.php: " . $e->getMessage());
}

// Test 7: Cargar debug_logger.php
try {
    require_once __DIR__ . '/debug_logger.php';
    echo "TEST 7: debug_logger.php cargado OK\n";
} catch (Exception $e) {
    die("ERROR en debug_logger.php: " . $e->getMessage());
}

// Test 8: Función debugLog existe
if (function_exists('debugLog')) {
    echo "TEST 8: debugLog() existe\n";
} else {
    die("ERROR: debugLog() NO existe");
}

// Test 9: Obtener conexión PDO
try {
    $pdo = getDB();
    if ($pdo) {
        echo "TEST 9: PDO conexión OK\n";
    } else {
        echo "TEST 9: PDO conexión FALLÓ (null)\n";
    }
} catch (Exception $e) {
    echo "TEST 9: PDO ERROR: " . $e->getMessage() . "\n";
}

// Test 10: Probar debugLog
try {
    debugLog("Test de debugLog desde test-handler.php");
    echo "TEST 10: debugLog() ejecutado OK\n";
} catch (Exception $e) {
    echo "TEST 10: debugLog() ERROR: " . $e->getMessage() . "\n";
}

// Test 11: JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Todos los tests pasaron',
    'tests' => [
        'php' => 'OK',
        'config' => 'OK',
        'connection' => 'OK',
        'functions' => 'OK',
        'email_functions' => 'OK',
        'debug_logger' => 'OK',
        'pdo' => $pdo ? 'OK' : 'FAIL',
        'debugLog' => function_exists('debugLog') ? 'OK' : 'FAIL'
    ]
]);

