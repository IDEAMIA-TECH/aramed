<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Newsletter Handler
 * ========================================
 * 
 * Procesa suscripciones al newsletter
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Configuración
define('ARAMED_SITE', true);

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

// Headers para JSON
header('Content-Type: application/json');

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
    
    // Preparar fecha aproximada de compra (necesaria para actualizaciones)
    $fecha_compra = null;
    if (!empty($data['compra_mes']) && !empty($data['compra_anio'])) {
        $fecha_compra = $data['compra_anio'] . '-' . $data['compra_mes'] . '-01';
        debugLog("Purchase date: " . $fecha_compra);
    }
    
    // Verificar si ya está suscrito
    debugLog("--- Database Operations ---");
    debugLog("Checking for existing subscription...");
    
    $stmt = $pdo->prepare("SELECT id, status, nombre, institucion FROM newsletter_subscriptions WHERE email_oficial = ?");
    $stmt->execute([$data['email_oficial']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Si existe y está activo, intentar actualizar la información
        if ($existing['status'] === 'active') {
            debugLog("⚠️ Email already subscribed, updating information...");
            
            // Actualizar información existente
            $updateSql = "UPDATE newsletter_subscriptions SET 
                institucion = :institucion,
                tipo_institucion = :tipo_institucion,
                campo_adicional = :campo_adicional,
                estado = :estado,
                ciudad = :ciudad,
                nombre = :nombre,
                puesto = :puesto,
                email_alterno = :email_alterno,
                telefono_oficina = :telefono_oficina,
                extension = :extension,
                telefono_celular = :telefono_celular,
                producto_interes = :producto_interes,
                fecha_compra_aprox = :fecha_compra,
                observaciones = :observaciones,
                ip_address = :ip_address,
                user_agent = :user_agent,
                updated_at = NOW()
                WHERE email_oficial = :email_oficial AND status = 'active'";
            
            $updateStmt = $pdo->prepare($updateSql);
            $updateResult = $updateStmt->execute([
                ':institucion' => $data['institucion'],
                ':tipo_institucion' => $data['tipo_institucion'],
                ':campo_adicional' => $data['campo_adicional'],
                ':estado' => $data['estado'],
                ':ciudad' => $data['ciudad'],
                ':nombre' => $data['nombre'],
                ':puesto' => $data['puesto'],
                ':email_alterno' => $data['email_alterno'],
                ':telefono_oficina' => $data['telefono_oficina'],
                ':extension' => $data['extension'],
                ':telefono_celular' => $data['telefono_celular'],
                ':producto_interes' => $data['producto_interes'],
                ':fecha_compra' => $fecha_compra,
                ':observaciones' => $data['observaciones'],
                ':ip_address' => $data['ip_address'],
                ':user_agent' => $data['user_agent'],
                ':email_oficial' => $data['email_oficial']
            ]);
            
            if ($updateResult) {
                debugLog("✅ Subscription information updated successfully");
                
                // Respuesta exitosa de actualización
                $response = [
                    'success' => true,
                    'message' => '¡Gracias! Hemos actualizado tu información de suscripción.'
                ];
                
                // Enviar notificación por email
                $to = CONTACT_EMAIL;
                $subject = "Actualización de suscripción - {$data['institucion']}";
                $message = "
                <html>
                <body>
                    <h2>Actualización de Suscripción</h2>
                    <p>El correo <strong>{$data['email_oficial']}</strong> ha actualizado su información de suscripción.</p>
                    <p><strong>Institución:</strong> {$data['institucion']}</p>
                    <p><strong>Nombre:</strong> {$data['nombre']}</p>
                    <p>Fecha: " . date('Y-m-d H:i:s') . "</p>
                </body>
                </html>
                ";
                
                sendEmail($to, $subject, $message);
                
                echo json_encode($response);
                exit;
            } else {
                debugLog("❌ Failed to update subscription");
                throw new Exception("No se pudo actualizar la información. Por favor, intenta nuevamente.");
            }
        } else {
            // Si existe pero está inactivo o cancelado, reactivar y actualizar
            debugLog("⚠️ Email exists but inactive, reactivating...");
            
            $reactivateSql = "UPDATE newsletter_subscriptions SET 
                institucion = :institucion,
                tipo_institucion = :tipo_institucion,
                campo_adicional = :campo_adicional,
                estado = :estado,
                ciudad = :ciudad,
                nombre = :nombre,
                puesto = :puesto,
                email_alterno = :email_alterno,
                telefono_oficina = :telefono_oficina,
                extension = :extension,
                telefono_celular = :telefono_celular,
                producto_interes = :producto_interes,
                fecha_compra_aprox = :fecha_compra,
                observaciones = :observaciones,
                ip_address = :ip_address,
                user_agent = :user_agent,
                status = 'active',
                unsubscribed_at = NULL,
                updated_at = NOW()
                WHERE email_oficial = :email_oficial";
            
            $reactivateStmt = $pdo->prepare($reactivateSql);
            $reactivateResult = $reactivateStmt->execute([
                ':institucion' => $data['institucion'],
                ':tipo_institucion' => $data['tipo_institucion'],
                ':campo_adicional' => $data['campo_adicional'],
                ':estado' => $data['estado'],
                ':ciudad' => $data['ciudad'],
                ':nombre' => $data['nombre'],
                ':puesto' => $data['puesto'],
                ':email_alterno' => $data['email_alterno'],
                ':telefono_oficina' => $data['telefono_oficina'],
                ':extension' => $data['extension'],
                ':telefono_celular' => $data['telefono_celular'],
                ':producto_interes' => $data['producto_interes'],
                ':fecha_compra' => $fecha_compra,
                ':observaciones' => $data['observaciones'],
                ':ip_address' => $data['ip_address'],
                ':user_agent' => $data['user_agent'],
                ':email_oficial' => $data['email_oficial']
            ]);
            
            if ($reactivateResult) {
                debugLog("✅ Subscription reactivated successfully");
                
                $response = [
                    'success' => true,
                    'message' => '¡Bienvenido de nuevo! Hemos reactivado tu suscripción con la información actualizada.'
                ];
                
                // Enviar notificación por email
                $to = CONTACT_EMAIL;
                $subject = "Reactivación de suscripción - {$data['institucion']}";
                $message = "
                <html>
                <body>
                    <h2>Reactivación de Suscripción</h2>
                    <p>El correo <strong>{$data['email_oficial']}</strong> ha reactivado su suscripción.</p>
                    <p><strong>Institución:</strong> {$data['institucion']}</p>
                    <p><strong>Nombre:</strong> {$data['nombre']}</p>
                    <p>Fecha: " . date('Y-m-d H:i:s') . "</p>
                </body>
                </html>
                ";
                
                sendEmail($to, $subject, $message);
                
                echo json_encode($response);
                exit;
            } else {
                debugLog("❌ Failed to reactivate subscription");
                throw new Exception("No se pudo reactivar la suscripción. Por favor, intenta nuevamente.");
            }
        }
    }
    
    debugLog("✅ Email not subscribed yet, proceeding with new subscription");
    
    // Insertar en base de datos
    debugLog("Preparing INSERT query...");
    
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
    
    debugLog("Executing INSERT...");
    
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
        debugLog("❌ INSERT failed");
        debugLog("PDO Error: " . print_r($stmt->errorInfo(), true));
        throw new Exception("Error al guardar la suscripción.");
    }
    
    $insertId = $pdo->lastInsertId();
    debugLog("✅ INSERT successful. ID: " . $insertId);
    
    // Enviar notificación por email
    $to = CONTACT_EMAIL; // Definido en config.php
    $subject = "Nueva suscripción al Newsletter - {$data['institucion']}";
    
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
                <h2>Nueva Suscripción al Newsletter</h2>
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
        'message' => '¡Gracias por suscribirte! Pronto recibirás información relevante en tu correo.'
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

