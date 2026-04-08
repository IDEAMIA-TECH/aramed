<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Contact Handler
 * ========================================
 * 
 * Procesa mensajes de contacto
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
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit;
}

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Inicializar respuesta
$response = [
    'success' => false,
    'message' => ''
];

try {
    $ip_address = function_exists('getClientIpAddress') ? getClientIpAddress() : ($_SERVER['REMOTE_ADDR'] ?? '');

    if (!empty($_POST['contact_company_fax'])) {
        error_log("SPAM [contact]: honeypot. IP: {$ip_address}");
        throw new Exception('No se pudo enviar el mensaje. Intenta de nuevo.');
    }

    $form_timestamp = intval($_POST['form_timestamp'] ?? 0);
    $time_elapsed = time() - $form_timestamp;
    if ($form_timestamp === 0 || $time_elapsed < 3) {
        error_log("SPAM [contact]: tiempo inválido ({$time_elapsed}s). IP: {$ip_address}");
        throw new Exception('Por favor espera unos segundos antes de enviar.');
    }
    if ($time_elapsed > 7200) {
        throw new Exception('La página lleva mucho tiempo abierta. Recarga e intenta de nuevo.');
    }

    $user_agent_check = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (strlen($user_agent_check) < 12) {
        error_log("SPAM [contact]: User-Agent sospechoso. IP: {$ip_address}");
        throw new Exception('No se pudo validar el envío. Intenta con otro navegador.');
    }

    $recaptcha_check = aramed_verify_recaptcha_v3($_POST['g-recaptcha-response'] ?? '', $ip_address);
    if (!$recaptcha_check['ok']) {
        error_log('SPAM [contact]: reCAPTCHA ' . ($recaptcha_check['reason'] ?? '') . ' score=' . ($recaptcha_check['score'] ?? 'n/a') . " IP: {$ip_address}");
        throw new Exception('La verificación de seguridad falló. Recarga la página e intenta de nuevo.');
    }

    $stmt_rate_h = $pdo->prepare('SELECT COUNT(*) FROM contact_messages WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
    $stmt_rate_h->execute([$ip_address]);
    if ((int) $stmt_rate_h->fetchColumn() >= 4) {
        error_log("SPAM [contact]: límite por hora. IP: {$ip_address}");
        throw new Exception('Has enviado demasiados mensajes recientemente. Intenta más tarde.');
    }
    $stmt_rate_d = $pdo->prepare('SELECT COUNT(*) FROM contact_messages WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)');
    $stmt_rate_d->execute([$ip_address]);
    if ((int) $stmt_rate_d->fetchColumn() >= 15) {
        error_log("SPAM [contact]: límite diario. IP: {$ip_address}");
        throw new Exception('Has alcanzado el límite de mensajes por hoy. Contacta por teléfono si es urgente.');
    }

    // Sanitizar y validar datos
    $data = [
        'nombre' => sanitizeInput($_POST['nombre'] ?? ''),
        'email' => sanitizeEmail($_POST['email'] ?? ''),
        'telefono' => sanitizePhone($_POST['telefono'] ?? ''),
        'institucion' => sanitizeInput($_POST['institucion'] ?? ''),
        'asunto' => sanitizeInput($_POST['asunto'] ?? ''),
        'mensaje' => sanitizeInput($_POST['mensaje'] ?? ''),
        'ip_address' => $ip_address,
        'user_agent' => $user_agent_check
    ];
    
    // Validaciones obligatorias
    $requiredFields = [
        'nombre' => 'Nombre',
        'email' => 'Correo electrónico',
        'telefono' => 'Teléfono',
        'asunto' => 'Asunto',
        'mensaje' => 'Mensaje'
    ];
    
    foreach ($requiredFields as $field => $label) {
        if (empty($data[$field])) {
            throw new Exception("El campo '{$label}' es obligatorio.");
        }
    }
    
    // Validar email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo electrónico no es válido.");
    }

    if (aramed_is_disposable_email_domain($data['email'])) {
        error_log("SPAM [contact]: email desechable. IP: {$ip_address}");
        throw new Exception('Usa un correo válido (no servicios de correo temporal).');
    }

    foreach (['nombre' => $data['nombre'], 'asunto' => $data['asunto'], 'mensaje' => $data['mensaje'], 'institucion' => $data['institucion']] as $field => $val) {
        if (aramed_text_has_obvious_spam_patterns($val)) {
            error_log("SPAM [contact]: patrón en {$field}. IP: {$ip_address}");
            throw new Exception('El mensaje contiene texto no permitido. Revísalo e intenta de nuevo.');
        }
    }
    
    // Validar longitud del mensaje
    if (strlen($data['mensaje']) < 10) {
        throw new Exception("El mensaje debe tener al menos 10 caracteres.");
    }
    
    // Insertar en base de datos
    $sql = "INSERT INTO contact_messages (
        nombre, email, telefono, institucion, asunto, mensaje,
        ip_address, user_agent, status, created_at
    ) VALUES (
        :nombre, :email, :telefono, :institucion, :asunto, :mensaje,
        :ip_address, :user_agent, 'nuevo', NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':nombre' => $data['nombre'],
        ':email' => $data['email'],
        ':telefono' => $data['telefono'],
        ':institucion' => $data['institucion'],
        ':asunto' => $data['asunto'],
        ':mensaje' => $data['mensaje'],
        ':ip_address' => $data['ip_address'],
        ':user_agent' => $data['user_agent']
    ]);
    
    if (!$result) {
        throw new Exception("Error al guardar el mensaje.");
    }
    
    // Enviar notificación por email al administrador
    $to = CONTACT_EMAIL;
    $subject = "Nuevo mensaje de contacto - {$data['asunto']}";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { padding: 30px; background: #f8f9fa; border-radius: 0 0 5px 5px; }
            .field { margin-bottom: 20px; padding: 15px; background: white; border-left: 4px solid #007bff; }
            .label { font-weight: bold; color: #555; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
            .value { color: #000; font-size: 14px; margin-top: 5px; }
            .mensaje-box { background: #fff; padding: 20px; border: 1px solid #dee2e6; border-radius: 5px; margin-top: 10px; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; margin-top: 20px; }
            .btn { display: inline-block; padding: 12px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2 style='margin: 0;'>✉️ Nuevo Mensaje de Contacto</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Asunto</div>
                    <div class='value'><strong>{$data['asunto']}</strong></div>
                </div>
                
                <div class='field'>
                    <div class='label'>Nombre</div>
                    <div class='value'>{$data['nombre']}</div>
                </div>
                
                <div class='field'>
                    <div class='label'>Correo Electrónico</div>
                    <div class='value'><a href='mailto:{$data['email']}'>{$data['email']}</a></div>
                </div>
                
                <div class='field'>
                    <div class='label'>Teléfono</div>
                    <div class='value'><a href='tel:{$data['telefono']}'>{$data['telefono']}</a></div>
                </div>
                
                " . (!empty($data['institucion']) ? "<div class='field'><div class='label'>Institución</div><div class='value'>{$data['institucion']}</div></div>" : "") . "
                
                <div class='field'>
                    <div class='label'>Mensaje</div>
                    <div class='mensaje-box'>" . nl2br($data['mensaje']) . "</div>
                </div>
                
                <div style='text-align: center;'>
                    <a href='mailto:{$data['email']}' class='btn'>📧 Responder a {$data['nombre']}</a>
                </div>
            </div>
            <div class='footer'>
                <p><strong>Aramed y Laboratorios</strong> - Sistema de Contacto</p>
                <p style='font-size: 10px; color: #999;'>
                    IP: {$data['ip_address']} | Fecha: " . date('d/m/Y H:i:s') . "
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Enviar email al admin usando PHPMailer
    $emailResult = sendEmail($to, $subject, $message);
    
    // Email de confirmación al cliente
    $clientSubject = "Hemos recibido tu mensaje - Aramed y Laboratorios";
    $clientMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; text-align: center; }
            .content { padding: 40px 30px; background: #f8f9fa; }
            .message-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; padding: 30px; background: #2c3e50; color: #ecf0f1; }
            .footer a { color: #3498db; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1 style='margin: 0; font-size: 28px;'>¡Gracias por contactarnos!</h1>
            </div>
            <div class='content'>
                <p>Hola <strong>{$data['nombre']}</strong>,</p>
                
                <p>Hemos recibido tu mensaje correctamente. Nuestro equipo lo revisará y te responderá a la brevedad posible.</p>
                
                <div class='message-box'>
                    <h3 style='color: #667eea; margin-top: 0;'>Resumen de tu mensaje:</h3>
                    <p><strong>Asunto:</strong> {$data['asunto']}</p>
                    <p><strong>Mensaje:</strong></p>
                    <p style='background: #f8f9fa; padding: 15px; border-left: 4px solid #667eea;'>" . nl2br($data['mensaje']) . "</p>
                </div>
                
                <p>Mientras tanto, puedes:</p>
                <ul>
                    <li>📱 Llamarnos al: <strong>" . CONTACT_PHONE . "</strong></li>
                    <li>📧 Escribirnos a: <strong>" . CONTACT_EMAIL . "</strong></li>
                    <li>🌐 Visitar nuestra web: <strong>www." . SITE_DOMAIN . "</strong></li>
                </ul>
                
                <p style='margin-top: 30px;'><em>Tiempo estimado de respuesta: 24-48 horas hábiles</em></p>
            </div>
            <div class='footer'>
                <p style='margin: 0 0 10px 0;'><strong>Aramed y Laboratorios</strong></p>
                <p style='margin: 0;'>Simuladores médicos para la enseñanza</p>
                <p style='margin: 15px 0 0 0; font-size: 12px;'>
                    <a href='#'>Facebook</a> | <a href='#'>Instagram</a> | <a href='#'>LinkedIn</a>
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Enviar confirmación al cliente usando PHPMailer
    $clientEmailResult = sendEmail($data['email'], $clientSubject, $clientMessage, $data['nombre']);
    
    // Respuesta exitosa
    $response = [
        'success' => true,
        'message' => '¡Mensaje enviado! Gracias por contactarnos, te responderemos pronto.'
    ];
    
} catch (Exception $e) {
    // Respuesta de error
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Enviar respuesta JSON
echo json_encode($response);
exit;


