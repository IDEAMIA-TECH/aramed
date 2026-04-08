<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Newsletter Simple Handler
 * ========================================
 *
 * Procesa suscripciones simples al boletín informativo (pie de página)
 *
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

define('ARAMED_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/email_functions.php';

header('Content-Type: application/json');

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

$response = [
    'success' => false,
    'message' => ''
];

/** Dominios de correo temporal / desechable frecuentes en spam */
function newsletter_simple_is_disposable_domain($email) {
    $email = strtolower(trim($email));
    if (strpos($email, '@') === false) {
        return false;
    }
    $domain = substr(strrchr($email, '@'), 1);
    $blocked = [
        'mailinator.com', 'guerrillamail.com', 'guerrillamail.org', 'sharklasers.com',
        'yopmail.com', 'yopmail.fr', 'tempmail.com', 'temp-mail.org', 'dispostable.com',
        'maildrop.cc', 'trashmail.com', 'getnada.com', '10minutemail.com', '10minutemail.net',
        'fakeinbox.com', 'mohmal.com', 'throwaway.email', 'emailondeck.com', 'mailnesia.com',
        'tempail.com', 'mintemail.com', 'spam4.me', 'bccto.me', 'discard.email',
        'mailcatch.com', 'mailnull.com', 'spamgourmet.com', 'mytrashmail.com',
    ];
    foreach ($blocked as $b) {
        if ($domain === $b) {
            return true;
        }
        if (strlen($domain) > strlen($b) && substr($domain, -(strlen($b) + 1)) === '.' . $b) {
            return true;
        }
    }
    return false;
}

try {
    $ip_address = function_exists('getClientIpAddress') ? getClientIpAddress() : ($_SERVER['REMOTE_ADDR'] ?? '');

    // --- Anti-spam: honeypot (debe ir vacío) ---
    if (!empty($_POST['footer_company_fax'])) {
        error_log("SPAM [newsletter_simple]: honeypot. IP: {$ip_address}");
        throw new Exception("No se pudo completar la suscripción. Intenta de nuevo.");
    }

    // --- Anti-spam: tiempo mínimo desde carga del formulario ---
    $form_timestamp = intval($_POST['form_timestamp'] ?? 0);
    $time_elapsed = time() - $form_timestamp;
    if ($form_timestamp === 0 || $time_elapsed < 3) {
        error_log("SPAM [newsletter_simple]: tiempo inválido ({$time_elapsed}s). IP: {$ip_address}");
        throw new Exception("Por favor espera unos segundos antes de suscribirte e intenta de nuevo.");
    }
    if ($time_elapsed > 7200) {
        error_log("SPAM [newsletter_simple]: timestamp caducado. IP: {$ip_address}");
        throw new Exception("La página lleva mucho tiempo abierta. Recarga e intenta de nuevo.");
    }

    // --- Anti-spam: user-agent ---
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (strlen($user_agent) < 12) {
        error_log("SPAM [newsletter_simple]: User-Agent sospechoso. IP: {$ip_address}");
        throw new Exception("No se pudo completar la suscripción. Intenta con otro navegador.");
    }

    // --- Anti-spam: límites por IP (tabla newsletter_simple) ---
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS c FROM newsletter_simple
        WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([$ip_address]);
    $hourly = (int) $stmt->fetchColumn();
    if ($hourly >= 5) {
        error_log("SPAM [newsletter_simple]: límite hora. IP: {$ip_address} ({$hourly})");
        throw new Exception("Has enviado demasiadas solicitudes. Intenta más tarde.");
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS c FROM newsletter_simple
        WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute([$ip_address]);
    $daily = (int) $stmt->fetchColumn();
    if ($daily >= 25) {
        error_log("SPAM [newsletter_simple]: límite 24h. IP: {$ip_address} ({$daily})");
        throw new Exception("Has enviado demasiadas solicitudes. Intenta mañana.");
    }

    // --- reCAPTCHA v3 (recomendado en producción: activar en config.php) ---
    if (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && !empty(RECAPTCHA_SECRET_KEY)) {
        $recaptcha_token = $_POST['g-recaptcha-response'] ?? '';
        if ($recaptcha_token === '') {
            error_log("SPAM [newsletter_simple]: sin token reCAPTCHA. IP: {$ip_address}");
            throw new Exception("Verificación de seguridad incompleta. Recarga la página.");
        }
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_data = [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $recaptcha_token,
            'remoteip' => $ip_address,
        ];
        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($recaptcha_data),
                'timeout' => 8,
            ],
        ]);
        $recaptcha_result = @file_get_contents($recaptcha_url, false, $ctx);
        if ($recaptcha_result === false) {
            error_log("SPAM [newsletter_simple]: fallo al verificar reCAPTCHA. IP: {$ip_address}");
            throw new Exception("Error de verificación. Intenta de nuevo en un momento.");
        }
        $recaptcha_response = json_decode($recaptcha_result, true);
        if (empty($recaptcha_response['success'])) {
            error_log("SPAM [newsletter_simple]: reCAPTCHA rechazado. IP: {$ip_address}");
            throw new Exception("La verificación de seguridad falló. Intenta de nuevo.");
        }
        if (isset($recaptcha_response['score']) && $recaptcha_response['score'] < 0.35) {
            error_log("SPAM [newsletter_simple]: score bajo ({$recaptcha_response['score']}). IP: {$ip_address}");
            throw new Exception("No pudimos validar tu solicitud. Intenta de nuevo más tarde.");
        }
    }

    $email = sanitizeEmail($_POST['email'] ?? '');
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $source = sanitizeInput($_POST['source'] ?? 'boletin');

    if (strlen($email) > 254) {
        throw new Exception("El correo electrónico no es válido.");
    }

    if ($email === '') {
        throw new Exception("El correo electrónico es obligatorio.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo electrónico no es válido.");
    }

    if (newsletter_simple_is_disposable_domain($email)) {
        error_log("SPAM [newsletter_simple]: dominio desechable. IP: {$ip_address} Email: {$email}");
        throw new Exception("Usa un correo corporativo o personal válido (no correos temporales).");
    }

    $stmt = $pdo->prepare("SELECT id, status FROM newsletter_simple WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        if ($existing['status'] === 'active') {
            throw new Exception("Este correo ya está suscrito a nuestro boletín.");
        }
        $stmt = $pdo->prepare("UPDATE newsletter_simple SET status = 'active', source = ?, ip_address = ?, user_agent = ?, updated_at = NOW() WHERE email = ?");
        $stmt->execute([$source, $ip_address, $user_agent, $email]);

        $response = [
            'success' => true,
            'message' => '¡Bienvenido de nuevo! Te has vuelto a suscribir a nuestro boletín.'
        ];
    } else {
        $sql = "INSERT INTO newsletter_simple (email, nombre, source, ip_address, user_agent, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())";
        $stmt = $pdo->prepare($sql);

        $result = $stmt->execute([
            $email,
            $nombre,
            $source,
            $ip_address,
            $user_agent
        ]);

        if (!$result) {
            throw new Exception("Error al guardar la suscripción.");
        }

        $response = [
            'success' => true,
            'message' => '¡Gracias por suscribirte! Recibirás nuestras novedades en tu correo.'
        ];
    }

    if ($response['success']) {
        try {
            $to = CONTACT_EMAIL;
            $subject = "Nueva suscripción al Boletín - " . SITE_NAME;

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
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Nueva Suscripción al Boletín</h2>
                    </div>
                    <div class='content'>
                        <div class='field'>
                            <span class='label'>Email:</span>
                            <span class='value'>" . htmlspecialchars($email) . "</span>
                        </div>";

            if (!empty($nombre)) {
                $message .= "
                        <div class='field'>
                            <span class='label'>Nombre:</span>
                            <span class='value'>" . htmlspecialchars($nombre) . "</span>
                        </div>";
            }

            $message .= "
                        <div class='field'>
                            <span class='label'>Fuente:</span>
                            <span class='value'>" . htmlspecialchars($source) . "</span>
                        </div>
                        <div class='field'>
                            <span class='label'>Fecha:</span>
                            <span class='value'>" . date('Y-m-d H:i:s') . "</span>
                        </div>
                        <div class='field'>
                            <span class='label'>IP:</span>
                            <span class='value'>" . htmlspecialchars($ip_address) . "</span>
                        </div>
                    </div>
                </div>
            </body>
            </html>";

            sendEmail($to, $subject, $message);
        } catch (Exception $e) {
            error_log("Error enviando notificación de suscripción simple: " . $e->getMessage());
        }
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
