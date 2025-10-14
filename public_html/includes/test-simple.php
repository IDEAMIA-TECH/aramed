<?php
/**
 * TEST SIMPLE - Sin conexión PDO
 */

// Test 1: PHP básico
echo "TEST 1: PHP OK<br>\n";

// Test 2: Define ARAMED_SITE
define('ARAMED_SITE', true);
echo "TEST 2: ARAMED_SITE definido<br>\n";

// Test 3: Cargar config.php
try {
    require_once __DIR__ . '/config.php';
    echo "TEST 3: config.php cargado OK<br>\n";
    echo "  → DB_HOST: " . DB_HOST . "<br>\n";
    echo "  → DB_NAME: " . DB_NAME . "<br>\n";
    echo "  → DB_USER: " . DB_USER . "<br>\n";
    echo "  → ENVIRONMENT: " . ENVIRONMENT . "<br>\n";
} catch (Exception $e) {
    die("ERROR en config.php: " . $e->getMessage());
}

// Test 4: Cargar functions.php (sin connection.php)
try {
    require_once __DIR__ . '/functions.php';
    echo "TEST 4: functions.php cargado OK<br>\n";
} catch (Exception $e) {
    die("ERROR en functions.php: " . $e->getMessage());
}

// Test 5: Verificar funciones
if (function_exists('esc')) {
    echo "TEST 5: esc() existe<br>\n";
} else {
    echo "TEST 5: esc() NO existe<br>\n";
}

// Test 6: Probar conexión PDO manualmente (sin cargar connection.php)
echo "<br>TEST 6: Probando conexión PDO manualmente...<br>\n";
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "  ✅ Conexión PDO exitosa<br>\n";
    echo "  → Servidor: " . DB_HOST . "<br>\n";
    echo "  → Base de datos: " . DB_NAME . "<br>\n";
    
} catch (PDOException $e) {
    echo "  ❌ Error de conexión PDO:<br>\n";
    echo "  → Mensaje: " . $e->getMessage() . "<br>\n";
    echo "  → Código: " . $e->getCode() . "<br>\n";
}

echo "<br>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>\n";
echo "DIAGNÓSTICO COMPLETO<br>\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>\n";

