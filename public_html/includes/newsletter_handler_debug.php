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

// Sanitizar datos (IGUAL que el handler real)
simpleLog("→ Sanitizando datos...");
try {
    $data = [
        'institucion' => sanitizeInput($_POST['institucion'] ?? ''),
        'tipo_institucion' => sanitizeInput($_POST['tipo_institucion'] ?? ''),
        'campo_adicional' => sanitizeInput($_POST['campo_adicional'] ?? ''),
        'estado' => sanitizeInput($_POST['estado'] ?? ''),
        'ciudad' => sanitizeInput($_POST['ciudad'] ?? ''),
        'nombre' => sanitizeInput($_POST['nombre'] ?? ''),
        'puesto' => sanitizeInput($_POST['puesto'] ?? ''),
        'email_oficial' => sanitizeEmail($_POST['email_oficial'] ?? ''),
        'email_alterno' => !empty($_POST['email_alterno']) ? sanitizeEmail($_POST['email_alterno']) : null,
        'telefono_oficina' => sanitizeInput($_POST['telefono_oficina'] ?? ''),
        'extension' => sanitizeInput($_POST['extension'] ?? ''),
        'telefono_celular' => !empty($_POST['telefono_celular']) ? sanitizeInput($_POST['telefono_celular']) : null,
        'producto_interes' => sanitizeInput($_POST['producto_interes'] ?? ''),
        'compra_mes' => sanitizeInput($_POST['compra_mes'] ?? ''),
        'compra_anio' => sanitizeInput($_POST['compra_anio'] ?? ''),
        'observaciones' => sanitizeInput($_POST['observaciones'] ?? ''),
        'privacidad' => isset($_POST['privacidad']) ? 1 : 0,
    ];
    simpleLog("✅ Datos sanitizados correctamente");
    simpleLog("  - Email: " . $data['email_oficial']);
    simpleLog("  - Privacidad: " . ($data['privacidad'] ? 'SI' : 'NO'));
} catch (Exception $e) {
    simpleLog("❌ Error sanitizando datos: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al sanitizar datos']);
    exit;
}

// Validar campos obligatorios
simpleLog("→ Validando campos obligatorios...");
$requiredFields = [
    'institucion' => 'Institución',
    'tipo_institucion' => 'Tipo de institución',
    'estado' => 'Estado',
    'ciudad' => 'Ciudad',
    'nombre' => 'Nombre',
    'puesto' => 'Puesto',
    'email_oficial' => 'Correo oficial',
    'telefono_oficina' => 'Teléfono de oficina'
];

foreach ($requiredFields as $field => $label) {
    if (empty($data[$field])) {
        simpleLog("❌ Campo vacío: $label");
        echo json_encode(['success' => false, 'message' => "El campo '$label' es obligatorio"]);
        exit;
    }
}
simpleLog("✅ Todos los campos obligatorios presentes");

// Validar privacidad
if ($data['privacidad'] !== 1) {
    simpleLog("❌ Privacidad NO aceptada");
    echo json_encode(['success' => false, 'message' => 'Debes aceptar la política de privacidad']);
    exit;
}
simpleLog("✅ Privacidad aceptada");

// Intentar INSERT en BD
simpleLog("→ Intentando INSERT en base de datos...");
try {
    // Preparar fecha de compra
    $fecha_compra = null;
    if (!empty($data['compra_mes']) && !empty($data['compra_anio'])) {
        $fecha_compra = $data['compra_anio'] . '-' . str_pad($data['compra_mes'], 2, '0', STR_PAD_LEFT) . '-01';
    }
    
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
    
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        ':institucion' => $data['institucion'],
        ':tipo_institucion' => $data['tipo_institucion'],
        ':campo_adicional' => $data['campo_adicional'],
        ':estado' => $data['estado'],
        ':ciudad' => $data['ciudad'],
        ':nombre' => $data['nombre'],
        ':puesto' => $data['puesto'],
        ':email_oficial' => $data['email_oficial'],
        ':email_alterno' => $data['email_alterno'],
        ':telefono_oficina' => $data['telefono_oficina'],
        ':extension' => $data['extension'],
        ':telefono_celular' => $data['telefono_celular'],
        ':producto_interes' => $data['producto_interes'],
        ':fecha_compra' => $fecha_compra,
        ':observaciones' => $data['observaciones'],
        ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    if ($result) {
        $insert_id = $pdo->lastInsertId();
        simpleLog("✅ INSERT exitoso - ID: $insert_id");
    } else {
        simpleLog("❌ INSERT falló");
        throw new Exception("Error al insertar en base de datos");
    }
    
} catch (Exception $e) {
    simpleLog("❌ Error en INSERT: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
    exit;
}

simpleLog("✅ Handler completado exitosamente");

echo json_encode([
    'success' => true,
    'message' => '¡Gracias! Te hemos agregado a nuestra lista.',
    'data' => [
        'insert_id' => $insert_id ?? 0,
        'timestamp' => date('Y-m-d H:i:s')
    ]
]);

simpleLog("========== HANDLER DEBUG END ==========");
?>

