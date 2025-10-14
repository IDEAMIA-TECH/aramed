<?php
/**
 * Test V2 - Más robusto para capturar errores
 */

// Activar visualización de TODOS los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>🧪 Test Newsletter V2 - Diagnóstico Profundo</h1>";
echo "<hr>";

// Simular datos POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'institucion' => 'Test Institution',
    'tipo_institucion' => 'Hospital',
    'campo_adicional' => '',
    'estado' => 'CDMX',
    'ciudad' => 'Ciudad de México',
    'nombre' => 'Test Usuario',
    'puesto' => 'Director',
    'email_oficial' => 'test@example.com',
    'email_alterno' => '',
    'telefono_oficina' => '5555555555',
    'extension' => '1234',
    'telefono_celular' => '5512345678',
    'producto_interes' => 'Maniquíes de Simulación',
    'compra_mes' => '12',
    'compra_anio' => '2025',
    'observaciones' => 'Test desde script directo',
    'privacidad' => '1'
];

echo "<h2>TEST 1: Cargar archivos manualmente</h2>";

// Intentar cargar cada archivo uno por uno
try {
    echo "→ Cargando config.php...<br>";
    define('ARAMED_SITE', true);
    require_once __DIR__ . '/includes/config.php';
    echo "✅ config.php cargado<br>";
    
    echo "→ Cargando connection.php...<br>";
    require_once __DIR__ . '/includes/connection.php';
    echo "✅ connection.php cargado<br>";
    
    echo "→ Cargando functions.php...<br>";
    require_once __DIR__ . '/includes/functions.php';
    echo "✅ functions.php cargado<br>";
    
    echo "→ Cargando email_functions.php...<br>";
    require_once __DIR__ . '/includes/email_functions.php';
    echo "✅ email_functions.php cargado<br>";
    
    echo "→ Cargando debug_logger.php...<br>";
    require_once __DIR__ . '/includes/debug_logger.php';
    echo "✅ debug_logger.php cargado<br>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ ERROR AL CARGAR ARCHIVOS</p>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    die();
}

echo "<hr>";
echo "<h2>TEST 2: Verificar conexión PDO</h2>";

try {
    $pdo = getDB();
    if ($pdo) {
        echo "✅ Conexión PDO exitosa<br>";
        echo "→ Tipo: " . get_class($pdo) . "<br>";
    } else {
        echo "<p style='color: red;'>❌ getDB() retornó false/null</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ ERROR EN CONEXIÓN PDO</p>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>TEST 3: Verificar funciones</h2>";

$functions_to_check = ['debugLog', 'esc', 'sanitizeInput', 'sanitizeEmail'];
foreach ($functions_to_check as $func) {
    if (function_exists($func)) {
        echo "✅ $func() existe<br>";
    } else {
        echo "❌ $func() NO existe<br>";
    }
}

echo "<hr>";
echo "<h2>TEST 4: Verificar tabla newsletter_subscriptions</h2>";

try {
    $pdo = getDB();
    if ($pdo) {
        $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_subscriptions'");
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            echo "✅ Tabla newsletter_subscriptions existe<br>";
            
            // Verificar columnas
            $stmt = $pdo->query("DESCRIBE newsletter_subscriptions");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "→ Columnas: " . implode(', ', $columns) . "<br>";
        } else {
            echo "<p style='color: red;'>❌ Tabla newsletter_subscriptions NO existe</p>";
            echo "<p>⚠️ Necesitas ejecutar el script SQL de la base de datos</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al verificar tabla: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>TEST 5: Ejecutar lógica del handler manualmente</h2>";

try {
    echo "→ Iniciando procesamiento...<br>";
    
    // Obtener PDO
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo obtener conexión PDO");
    }
    echo "✅ PDO obtenido<br>";
    
    // Sanitizar datos
    $data = [
        'institucion' => sanitizeInput($_POST['institucion']),
        'tipo_institucion' => sanitizeInput($_POST['tipo_institucion']),
        'campo_adicional' => sanitizeInput($_POST['campo_adicional']),
        'estado' => sanitizeInput($_POST['estado']),
        'ciudad' => sanitizeInput($_POST['ciudad']),
        'nombre' => sanitizeInput($_POST['nombre']),
        'puesto' => sanitizeInput($_POST['puesto']),
        'email_oficial' => sanitizeEmail($_POST['email_oficial']),
        'email_alterno' => sanitizeInput($_POST['email_alterno']),
        'telefono_oficina' => sanitizeInput($_POST['telefono_oficina']),
        'extension' => sanitizeInput($_POST['extension']),
        'telefono_celular' => sanitizeInput($_POST['telefono_celular']),
        'producto_interes' => sanitizeInput($_POST['producto_interes']),
        'observaciones' => sanitizeInput($_POST['observaciones'])
    ];
    echo "✅ Datos sanitizados<br>";
    
    // Verificar tabla antes de insertar
    $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_subscriptions'");
    if (!$stmt->fetch()) {
        throw new Exception("Tabla newsletter_subscriptions no existe");
    }
    
    // Preparar fecha de compra
    $fecha_compra = null;
    if (!empty($_POST['compra_mes']) && !empty($_POST['compra_anio'])) {
        $fecha_compra = $_POST['compra_anio'] . '-' . str_pad($_POST['compra_mes'], 2, '0', STR_PAD_LEFT) . '-01';
    }
    
    // Preparar INSERT
    $sql = "INSERT INTO newsletter_subscriptions (
        institucion, tipo_institucion, campo_adicional, estado, ciudad,
        nombre, puesto, email_oficial, email_alterno,
        telefono_oficina, extension, telefono_celular,
        producto_interes, fecha_compra_aprox, observaciones,
        ip_address, user_agent, status, created_at
    ) VALUES (
        :institucion, :tipo_institucion, :campo_adicional, :estado, :ciudad,
        :nombre, :puesto, :email_oficial, :email_alterno,
        :telefono_oficina, :extension, :telefono_celular,
        :producto_interes, :fecha_compra, :observaciones,
        :ip_address, :user_agent, 'active', NOW()
    )";
    
    echo "→ Preparando INSERT...<br>";
    $stmt = $pdo->prepare($sql);
    
    $params = array_merge($data, [
        'fecha_compra' => $fecha_compra,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'test',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'test'
    ]);
    
    echo "→ Ejecutando INSERT...<br>";
    if ($stmt->execute($params)) {
        $insert_id = $pdo->lastInsertId();
        echo "<p style='color: green; font-size: 20px; font-weight: bold;'>✅ INSERCIÓN EXITOSA</p>";
        echo "<p><strong>ID insertado:</strong> $insert_id</p>";
        
        // Verificar que se insertó
        $stmt = $pdo->prepare("SELECT * FROM newsletter_subscriptions WHERE id = ?");
        $stmt->execute([$insert_id]);
        $row = $stmt->fetch();
        
        if ($row) {
            echo "<h4>Datos insertados:</h4>";
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error en INSERT</p>";
        print_r($stmt->errorInfo());
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ ERROR EN TEST 5</p>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
}

echo "<hr>";
echo "<h2>TEST 6: Ver logs generados</h2>";

$log_file = __DIR__ . '/logs/debug.log';
if (file_exists($log_file)) {
    echo "✅ Archivo debug.log existe<br>";
    $content = file_get_contents($log_file);
    if ($content) {
        echo "<h4>Contenido del log:</h4>";
        echo "<pre style='background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px;'>";
        echo htmlspecialchars($content);
        echo "</pre>";
    } else {
        echo "⚠️ El archivo existe pero está vacío<br>";
    }
} else {
    echo "⚠️ Archivo debug.log NO existe<br>";
}

echo "<hr>";
echo "<h2>📋 RESUMEN FINAL</h2>";
echo "<p><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Directorio:</strong> " . __DIR__ . "</p>";
?>

