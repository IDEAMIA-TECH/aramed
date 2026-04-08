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
    // ========================================
    // PROTECCIÓN ANTI-SPAM
    // ========================================
    
    // 1. Honeypot: Si el campo oculto está lleno, es un bot
    if (!empty($_POST['website_url'])) {
        error_log("SPAM DETECTED: Honeypot field filled. IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        throw new Exception("Error de validación. Por favor, recarga la página e intenta de nuevo.");
    }
    
    // 2. Validación de tiempo mínimo (al menos 3 segundos desde carga de página)
    $form_timestamp = intval($_POST['form_timestamp'] ?? 0);
    $current_time = time();
    $time_elapsed = $current_time - $form_timestamp;
    
    if ($form_timestamp === 0) {
        error_log("SPAM DETECTED: Missing form timestamp. IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        throw new Exception("Error de validación. Por favor, recarga la página e intenta de nuevo.");
    }
    
    if ($time_elapsed < 3) {
        error_log("SPAM DETECTED: Form submitted too quickly ({$time_elapsed}s). IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        throw new Exception("Por favor, tómate un momento para completar el formulario antes de enviarlo.");
    }
    
    // Validar que no se envíe demasiado rápido (máximo 30 segundos también es sospechoso si tiene mucho contenido)
    if ($time_elapsed > 3600) {
        error_log("SPAM DETECTED: Form timestamp too old ({$time_elapsed}s). IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        throw new Exception("La sesión ha expirado. Por favor, recarga la página e intenta de nuevo.");
    }
    
    // 3. Rate limiting: Verificar envíos recientes desde la misma IP
    $ip_address = function_exists('getClientIpAddress') ? getClientIpAddress() : ($_SERVER['REMOTE_ADDR'] ?? '');
    $rate_limit_check = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM newsletter_subscriptions 
        WHERE ip_address = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $rate_limit_check->execute([$ip_address]);
    $recent_submissions = $rate_limit_check->fetch();
    
    if ($recent_submissions && intval($recent_submissions['count']) >= 3) {
        error_log("SPAM DETECTED: Rate limit exceeded. IP: {$ip_address} - {$recent_submissions['count']} submissions in last hour");
        throw new Exception("Has enviado demasiadas solicitudes recientemente. Por favor, intenta de nuevo más tarde.");
    }
    
    // 4. Validación de reCAPTCHA si está habilitado
    if (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && !empty(RECAPTCHA_SECRET_KEY)) {
        $recaptcha_token = $_POST['g-recaptcha-response'] ?? '';
        
        if (empty($recaptcha_token)) {
            error_log("SPAM DETECTED: Missing reCAPTCHA token. IP: " . $ip_address);
            throw new Exception("Por favor, completa la verificación de seguridad.");
        }
        
        // Verificar reCAPTCHA con Google
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_data = [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $recaptcha_token,
            'remoteip' => $ip_address
        ];
        
        $recaptcha_options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($recaptcha_data)
            ]
        ];
        
        $recaptcha_context = stream_context_create($recaptcha_options);
        $recaptcha_result = @file_get_contents($recaptcha_url, false, $recaptcha_context);
        
        if ($recaptcha_result === false) {
            error_log("SPAM CHECK: Failed to verify reCAPTCHA. IP: " . $ip_address);
            throw new Exception("Error al verificar la seguridad. Por favor, intenta de nuevo.");
        }
        
        $recaptcha_response = json_decode($recaptcha_result, true);
        
        if (!isset($recaptcha_response['success']) || !$recaptcha_response['success']) {
            error_log("SPAM DETECTED: reCAPTCHA verification failed. IP: " . $ip_address);
            throw new Exception("La verificación de seguridad falló. Por favor, intenta de nuevo.");
        }
        
        // Verificar score si es reCAPTCHA v3 (score < 0.5 es sospechoso)
        if (isset($recaptcha_response['score']) && $recaptcha_response['score'] < 0.5) {
            error_log("SPAM DETECTED: Low reCAPTCHA score ({$recaptcha_response['score']}). IP: " . $ip_address);
            throw new Exception("La verificación de seguridad falló. Por favor, intenta de nuevo.");
        }
    }
    
    // 5. Validación de patrones sospechosos en campos
    $suspicious_patterns = [
        '/http[s]?:\/\//i',  // URLs en campos de texto
        '/www\./i',          // Referencias a sitios web
        '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', // Múltiples emails
        '/\b(viagra|cialis|casino|poker|loan|credit|debt)\b/i', // Palabras spam comunes
    ];
    
    $text_fields_to_check = [
        'institucion' => $_POST['institucion'] ?? '',
        'nombre' => $_POST['nombre'] ?? '',
        'puesto' => $_POST['puesto'] ?? '',
        'observaciones' => $_POST['observaciones'] ?? ''
    ];
    
    foreach ($text_fields_to_check as $field_name => $field_value) {
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $field_value)) {
                error_log("SPAM DETECTED: Suspicious pattern in field '{$field_name}'. IP: " . $ip_address);
                throw new Exception("El formulario contiene información no válida. Por favor, revisa los datos ingresados.");
            }
        }
    }
    
    // 6. Validación de User-Agent (debe existir)
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (empty($user_agent) || strlen($user_agent) < 10) {
        error_log("SPAM DETECTED: Missing or suspicious User-Agent. IP: " . $ip_address);
        throw new Exception("Error de validación. Por favor, recarga la página e intenta de nuevo.");
    }
    
    // 7. Validación de longitud de campos (evitar envíos vacíos o demasiado largos)
    $max_lengths = [
        'institucion' => 200,
        'nombre' => 100,
        'puesto' => 100,
        'observaciones' => 2000
    ];
    
    foreach ($max_lengths as $field => $max_len) {
        if (isset($_POST[$field]) && strlen($_POST[$field]) > $max_len) {
            error_log("SPAM DETECTED: Field '{$field}' exceeds max length. IP: " . $ip_address);
            throw new Exception("El campo contiene demasiado texto. Por favor, acorta el contenido.");
        }
    }
    
    // ========================================
    // VALIDACIÓN NORMAL DE DATOS
    // ========================================
    
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
        'ip_address' => $ip_address,
        'user_agent' => $user_agent
    ];
    
    // Validaciones obligatorias
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
            throw new Exception("El campo '{$label}' es obligatorio.");
        }
    }
    
    // Validar privacidad
    if ($data['privacidad'] !== 1) {
        throw new Exception("Debes aceptar la política de privacidad.");
    }
    
    // Validar email oficial
    if (!filter_var($data['email_oficial'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo oficial no es válido.");
    }
    
    // Validar email alterno si se proporcionó
    if ($data['email_alterno'] && !filter_var($data['email_alterno'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo alterno no es válido.");
    }
    
    // Preparar fecha aproximada de compra
    $fecha_compra = null;
    if (!empty($data['compra_mes']) && !empty($data['compra_anio'])) {
        $fecha_compra = $data['compra_anio'] . '-' . $data['compra_mes'] . '-01';
    }
    
    // COTIZADOR: Permitir múltiples solicitudes (no verificar duplicados)
    // Insertar en base de datos
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
        ':ip_address' => $data['ip_address'],
        ':user_agent' => $data['user_agent']
    ]);
    
    if (!$result) {
        $errorInfo = $stmt->errorInfo();
        
        // Detectar error de clave duplicada (UNIQUE constraint violation)
        if ($errorInfo[0] == '23000' || (isset($errorInfo[1]) && $errorInfo[1] == 1062)) {
            throw new Exception("Error: La base de datos tiene una restricción que impide múltiples solicitudes del mismo correo. Por favor contacta al administrador.");
        }
        
        throw new Exception("Error al guardar la solicitud en la base de datos.");
    }
    
    $insertId = $pdo->lastInsertId();
    if ($insertId) {
        // Verificar que realmente se insertó haciendo una consulta
        $verifyStmt = $pdo->prepare("SELECT id FROM newsletter_subscriptions WHERE id = ?");
        $verifyStmt->execute([$insertId]);
        $verifyRecord = $verifyStmt->fetch();
        
        if (!$verifyRecord) {
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
    $emailResult = sendEmail($to, $subject, $message);
    
    // Respuesta exitosa
    $response = [
        'success' => true,
        'message' => '¡Gracias por tu solicitud! Hemos recibido tu información correctamente. Pronto nos pondremos en contacto contigo.'
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

