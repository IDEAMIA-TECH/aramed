<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Newsletter Handler
 * ========================================
 * 
 * Procesa suscripciones al newsletter (COTIZADOR)
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Configurar manejo de errores ANTES de cualquier cosa
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        // Solo responder JSON si no se ha enviado contenido aún
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $error['message'] . ' en línea ' . $error['line']
            ]);
            exit;
        }
    }
});

// Configuración
define('ARAMED_SITE', true);

// Establecer headers JSON inmediatamente para asegurar respuesta JSON
header('Content-Type: application/json');

// Verificar que los archivos existan antes de cargar
$requiredFiles = [
    'config.php',
    'connection.php',
    'functions.php',
    'email_functions.php'
];

foreach ($requiredFiles as $file) {
    $filePath = __DIR__ . '/' . $file;
    if (!file_exists($filePath)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Error: Archivo faltante: {$file}. Ruta esperada: {$filePath}"
        ]);
        exit;
    }
}

// Cargar archivos con manejo de errores
try {
    require_once __DIR__ . '/config.php';
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de configuración: ' . $e->getMessage()]);
    exit;
}

try {
    require_once __DIR__ . '/connection.php';
    require_once __DIR__ . '/functions.php';
    require_once __DIR__ . '/email_functions.php';
    
    // Cargar debug logger si existe, sino usar fallback
    if (file_exists(__DIR__ . '/debug_logger.php')) {
        require_once __DIR__ . '/debug_logger.php';
    } else {
        // Fallback: función básica de logging
        if (!function_exists('debugLog')) {
            function debugLog($message, $data = null) {
                error_log("[Newsletter] $message");
                if ($data !== null) {
                    error_log("[Newsletter Data] " . print_r($data, true));
                }
            }
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al cargar dependencias: ' . $e->getMessage()]);
    exit;
}

// Headers para JSON (ya se establecieron arriba, pero por si acaso)
if (!headers_sent()) {
    header('Content-Type: application/json');
}

// Obtener conexión a la base de datos
try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
} catch (Exception $e) {
    debugLog("❌ Database connection failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit;
}

// LOG: Inicio del handler
debugLog("========================================");
debugLog("NEWSLETTER HANDLER - START");
debugLog("Request Method: " . $_SERVER['REQUEST_METHOD']);
debugLog("Request Time: " . date('Y-m-d H:i:s'));
debugLog("Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
debugLog("Remote IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A'));
debugLog("========================================");

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debugLog("❌ Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

debugLog("✅ POST request received");

// Inicializar respuesta
$response = [
    'success' => false,
    'message' => ''
];

try {
    debugLog("--- Data Sanitization ---");
    debugLog("POST Data received: " . count($_POST) . " fields");
    
    // Sanitizar y validar datos
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
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    debugLog("Received data:");
    debugLog("  - Institución: " . $data['institucion']);
    debugLog("  - Email: " . $data['email_oficial']);
    debugLog("  - Nombre: " . $data['nombre']);
    debugLog("  - Privacidad: " . ($data['privacidad'] ? 'Accepted' : 'NOT accepted'));
    
    // Validaciones obligatorias
    debugLog("--- Field Validation ---");
    
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
            debugLog("❌ Validation failed: {$label} is empty");
            throw new Exception("El campo '{$label}' es obligatorio.");
        }
    }
    
    debugLog("✅ All required fields present");
    
    // Validar privacidad
    if ($data['privacidad'] !== 1) {
        debugLog("❌ Privacy policy not accepted");
        throw new Exception("Debes aceptar la política de privacidad.");
    }
    
    debugLog("✅ Privacy policy accepted");
    
    // Validar email oficial
    if (!filter_var($data['email_oficial'], FILTER_VALIDATE_EMAIL)) {
        debugLog("❌ Invalid email: " . $data['email_oficial']);
        throw new Exception("El correo oficial no es válido.");
    }
    
    debugLog("✅ Email validation passed");
    
    // Validar email alterno si se proporcionó
    if ($data['email_alterno'] && !filter_var($data['email_alterno'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo alterno no es válido.");
    }
    
    // Preparar fecha aproximada de compra
    $fecha_compra = null;
    if (!empty($data['compra_mes']) && !empty($data['compra_anio'])) {
        $fecha_compra = $data['compra_anio'] . '-' . $data['compra_mes'] . '-01';
        debugLog("Purchase date: " . $fecha_compra);
    }
    
    // COTIZADOR: Permitir múltiples solicitudes (no verificar duplicados)
    debugLog("--- Database Operations ---");
    debugLog("COTIZADOR: Proceeding with INSERT (allowing multiple submissions)");
    
    debugLog("✅ Proceeding with NEW INSERT (cotizador allows multiple submissions)");
    debugLog("========================================");
    debugLog("STARTING INSERT OPERATION");
    debugLog("========================================");
    
    // Insertar en base de datos
    debugLog("Step 1: Preparing INSERT query...");
    
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
    debugLog("Step 2: Query prepared successfully");
    
    // Log de datos antes del INSERT
    debugLog("Step 3: Data to INSERT:");
    debugLog("  - Institución: " . $data['institucion']);
    debugLog("  - Email: " . $data['email_oficial']);
    debugLog("  - Nombre: " . $data['nombre']);
    debugLog("  - Estado: " . $data['estado']);
    debugLog("  - Ciudad: " . $data['ciudad']);
    debugLog("  - Teléfono: " . $data['telefono_oficina']);
    debugLog("  - Fecha compra: " . ($fecha_compra ?? 'N/A'));
    
    debugLog("Step 4: Executing INSERT statement...");
    
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
        ':ip_address' => $data['ip_address'],
        ':user_agent' => $data['user_agent']
    ]);
    
    if (!$result) {
        $errorInfo = $stmt->errorInfo();
        debugLog("❌ INSERT FAILED");
        debugLog("PDO Error Code: " . $errorInfo[0]);
        debugLog("PDO SQL State: " . ($errorInfo[1] ?? 'N/A'));
        debugLog("PDO Error Message: " . ($errorInfo[2] ?? 'Unknown error'));
        debugLog("Full Error Info: " . print_r($errorInfo, true));
        
        // Detectar error de clave duplicada (UNIQUE constraint violation)
        if ($errorInfo[0] == '23000' || (isset($errorInfo[1]) && $errorInfo[1] == 1062)) {
            debugLog("❌ CRITICAL: UNIQUE KEY constraint violation detected!");
            debugLog("La tabla tiene UNIQUE KEY en email_oficial que impide múltiples solicitudes");
            debugLog("SOLUCIÓN: Ejecutar el script: database/remove_unique_email_cotizador.sql");
            throw new Exception("Error: La base de datos tiene una restricción que impide múltiples solicitudes del mismo correo. Por favor contacta al administrador.");
        }
        
        throw new Exception("Error al guardar la solicitud en la base de datos: " . ($errorInfo[2] ?? 'Error desconocido (Código: ' . ($errorInfo[0] ?? 'N/A') . ')'));
    }
    
    $insertId = $pdo->lastInsertId();
    if (!$insertId) {
        debugLog("⚠️ WARNING: INSERT executed but lastInsertId() returned NULL");
        debugLog("This might mean the INSERT didn't actually create a record");
        // Continuar de todos modos, pero registrar el warning
    } else {
        debugLog("✅ INSERT successful! New record ID: " . $insertId);
        
        // Verificar que realmente se insertó haciendo una consulta
        $verifyStmt = $pdo->prepare("SELECT id, email_oficial, nombre, institucion FROM newsletter_subscriptions WHERE id = ?");
        $verifyStmt->execute([$insertId]);
        $verifyRecord = $verifyStmt->fetch();
        
        if ($verifyRecord) {
            debugLog("✅ VERIFICATION SUCCESSFUL: Record confirmed in database:");
            debugLog("  - ID: " . $verifyRecord['id']);
            debugLog("  - Email: " . $verifyRecord['email_oficial']);
            debugLog("  - Nombre: " . $verifyRecord['nombre']);
            debugLog("  - Institución: " . $verifyRecord['institucion']);
            debugLog("========================================");
            debugLog("INSERT OPERATION COMPLETED SUCCESSFULLY");
            debugLog("========================================");
        } else {
            debugLog("❌ VERIFICATION FAILED: Record not found after INSERT!");
            debugLog("This is a CRITICAL ERROR - data was not saved!");
            throw new Exception("Error crítico: La suscripción no se guardó correctamente en la base de datos.");
        }
    }
    
    // Enviar notificación por email
    $to = CONTACT_EMAIL; // Definido en config.php
    $subject = "Nueva solicitud de cotización - {$data['institucion']}";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f8f9fa; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #555; }
            .value { color: #000; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nueva Solicitud de Cotización</h2>
            </div>
            <div class='content'>
                <h3>Información de la Institución</h3>
                <div class='field'><span class='label'>Institución:</span> <span class='value'>{$data['institucion']}</span></div>
                <div class='field'><span class='label'>Tipo:</span> <span class='value'>{$data['tipo_institucion']}</span></div>
                " . (!empty($data['campo_adicional']) ? "<div class='field'><span class='label'>Especificación:</span> <span class='value'>{$data['campo_adicional']}</span></div>" : "") . "
                <div class='field'><span class='label'>Estado:</span> <span class='value'>{$data['estado']}</span></div>
                <div class='field'><span class='label'>Ciudad:</span> <span class='value'>{$data['ciudad']}</span></div>
                
                <h3>Información del Contacto</h3>
                <div class='field'><span class='label'>Nombre:</span> <span class='value'>{$data['nombre']}</span></div>
                <div class='field'><span class='label'>Puesto:</span> <span class='value'>{$data['puesto']}</span></div>
                <div class='field'><span class='label'>Email Oficial:</span> <span class='value'>{$data['email_oficial']}</span></div>
                " . (!empty($data['email_alterno']) ? "<div class='field'><span class='label'>Email Alterno:</span> <span class='value'>{$data['email_alterno']}</span></div>" : "") . "
                <div class='field'><span class='label'>Teléfono Oficina:</span> <span class='value'>{$data['telefono_oficina']}" . (!empty($data['extension']) ? " Ext. {$data['extension']}" : "") . "</span></div>
                " . (!empty($data['telefono_celular']) ? "<div class='field'><span class='label'>Teléfono Celular:</span> <span class='value'>{$data['telefono_celular']}</span></div>" : "") . "
                
                <h3>Información de Interés</h3>
                " . (!empty($data['producto_interes']) ? "<div class='field'><span class='label'>Producto de Interés:</span> <span class='value'>{$data['producto_interes']}</span></div>" : "") . "
                " . (!empty($fecha_compra) ? "<div class='field'><span class='label'>Fecha Aprox. de Compra:</span> <span class='value'>" . date('F Y', strtotime($fecha_compra)) . "</span></div>" : "") . "
                " . (!empty($data['observaciones']) ? "<div class='field'><span class='label'>Observaciones:</span> <span class='value'>" . nl2br($data['observaciones']) . "</span></div>" : "") . "
            </div>
            <div class='footer'>
                <p>Notificación automática de Aramed y Laboratorios</p>
                <p>IP: {$data['ip_address']} | " . date('Y-m-d H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Enviar email usando PHPMailer
    debugLog("--- Email Sending ---");
    debugLog("Sending notification email to: " . $to);
    
    $emailResult = sendEmail($to, $subject, $message);
    
    // Log si hay error en el envío de email
    if (!$emailResult['success']) {
        debugLog("❌ Newsletter Email Error: " . $emailResult['message']);
    } else {
        debugLog("✅ Newsletter Email sent successfully");
    }
    
    // Respuesta exitosa
    debugLog("✅ Newsletter subscription completed successfully");
    debugLog("========================================");
    
    $response = [
        'success' => true,
        'message' => '¡Gracias por tu solicitud! Hemos recibido tu información correctamente. Pronto nos pondremos en contacto contigo.'
    ];
    
} catch (Exception $e) {
    // Log error
    debugLog("❌❌❌ EXCEPTION CAUGHT ❌❌❌");
    debugLog("Error Message: " . $e->getMessage());
    debugLog("Error File: " . $e->getFile());
    debugLog("Error Line: " . $e->getLine());
    debugLog("Stack Trace: " . $e->getTraceAsString());
    debugLog("========================================");
    
    // Respuesta de error
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Enviar respuesta JSON
echo json_encode($response);
exit;

