<?php
/**
 * Funciones de Email usando PHPMailer
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('INCLUDES_PATH')) {
    die('Acceso no autorizado');
}

// Cargar debug logger si está disponible
if (file_exists(INCLUDES_PATH . '/debug_logger.php')) {
    require_once INCLUDES_PATH . '/debug_logger.php';
} else {
    // Fallback: definir función básica si no está disponible
    if (!function_exists('debugLog')) {
        function debugLog($message, $data = null) {
            error_log($message);
            if ($data !== null) {
                error_log(print_r($data, true));
            }
        }
    }
}

// Intentar cargar PHPMailer si está disponible
$phpmailerAvailable = false;
if (file_exists(INCLUDES_PATH . '/library/phpmailer/class.phpmailer.php')) {
    require_once INCLUDES_PATH . '/library/phpmailer/class.phpmailer.php';
    require_once INCLUDES_PATH . '/library/phpmailer/class.smtp.php';
    $phpmailerAvailable = true;
}

/**
 * Enviar email usando PHPMailer con configuración SMTP o mail() nativo
 * 
 * @param string $to Email del destinatario
 * @param string $subject Asunto del email
 * @param string $body Cuerpo del mensaje (HTML)
 * @param string $toName Nombre del destinatario (opcional)
 * @param array $attachments Array de archivos adjuntos (opcional)
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $toName = '', $attachments = []) {
    global $phpmailerAvailable;
    
    // LOG: Inicio del envío
    debugLog("===== EMAIL SEND ATTEMPT =====");
    debugLog("To: " . $to);
    debugLog("Subject: " . $subject);
    debugLog("PHPMailer Available: " . ($phpmailerAvailable ? 'YES' : 'NO'));
    debugLog("SMTP Host: " . SMTP_HOST);
    debugLog("SMTP Port: " . SMTP_PORT);
    debugLog("SMTP User: " . SMTP_USERNAME);
    debugLog("SMTP Pass Set: " . (defined('SMTP_PASSWORD') && !empty(SMTP_PASSWORD) ? 'YES' : 'NO'));
    
    // Si PHPMailer está disponible, usarlo
    if ($phpmailerAvailable && class_exists('PHPMailer')) {
        debugLog("Using: PHPMailer");
        return sendEmailWithPHPMailer($to, $subject, $body, $toName, $attachments);
    }
    
    // Fallback: usar mail() nativo con headers SMTP
    debugLog("Using: Native mail()");
    
    try {
        // Verificar configuración
        if (!defined('MAIL_FROM_EMAIL') || empty(MAIL_FROM_EMAIL)) {
            throw new Exception('MAIL_FROM_EMAIL no configurado');
        }
        
        // Configurar headers
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">";
        $headers[] = "Reply-To: " . MAIL_FROM_EMAIL;
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $headers[] = "X-Priority: 3";
        
        debugLog("Headers: " . implode(" | ", $headers));
        
        // Configurar parámetros adicionales para SMTP
        $additional_parameters = "-f" . MAIL_FROM_EMAIL;
        
        debugLog("Additional Params: " . $additional_parameters);
        debugLog("Attempting mail() function...");
        
        // Enviar email
        $result = @mail($to, $subject, $body, implode("\r\n", $headers), $additional_parameters);
        
        debugLog("mail() result: " . ($result ? 'TRUE' : 'FALSE'));
        
        if ($result) {
            debugLog("✅ Email sent successfully via mail()");
            return [
                'success' => true,
                'message' => 'Email enviado correctamente'
            ];
        } else {
            // Capturar el último error de PHP
            $lastError = error_get_last();
            $errorMsg = $lastError ? $lastError['message'] : 'Unknown error';
            debugLog("❌ mail() failed: " . $errorMsg);
            throw new Exception('Error al enviar email con mail(): ' . $errorMsg);
        }
        
    } catch (Exception $e) {
        debugLog("❌ Exception caught: " . $e->getMessage());
        debugLog("===== EMAIL SEND FAILED =====");
        
        return [
            'success' => false,
            'message' => 'Error al enviar email: ' . $e->getMessage()
        ];
    }
}

/**
 * Enviar email usando PHPMailer (función interna)
 * 
 * @param string $to Email del destinatario
 * @param string $subject Asunto del email
 * @param string $body Cuerpo del mensaje (HTML)
 * @param string $toName Nombre del destinatario
 * @param array $attachments Archivos adjuntos
 * @return array Resultado del envío
 */
function sendEmailWithPHPMailer($to, $subject, $body, $toName = '', $attachments = []) {
    $mail = new PHPMailer(true);
    
    try {
        debugLog("--- PHPMailer Configuration ---");
        
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = SMTP_AUTH;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        
        // Debug output (nivel 2 = mensajes de cliente y servidor)
        $mail->SMTPDebug  = 2;
        $mail->Debugoutput = function($str, $level) {
            debugLog("PHPMailer DEBUG: $str");
        };
        
        debugLog("SMTP Host: " . $mail->Host);
        debugLog("SMTP Port: " . $mail->Port);
        debugLog("SMTP Secure: " . $mail->SMTPSecure);
        debugLog("SMTP Auth: " . ($mail->SMTPAuth ? 'YES' : 'NO'));
        
        // Configuración del charset
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        
        // Configuración adicional para mejorar compatibilidad
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        debugLog("SSL Options: verify_peer=false, verify_peer_name=false, allow_self_signed=true");
        
        // Configurar timeout
        $mail->Timeout = 30;
        debugLog("Timeout: 30 seconds");
        
        // Remitente
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        debugLog("From: " . MAIL_FROM_EMAIL);
        
        // Destinatario
        if (empty($toName)) {
            $mail->addAddress($to);
        } else {
            $mail->addAddress($to, $toName);
        }
        debugLog("To: " . $to);
        
        // Reply-To
        $mail->addReplyTo(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        
        // Archivos adjuntos (si los hay)
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $mail->addAttachment($attachment);
                    debugLog("Attachment: " . $attachment);
                }
            }
        }
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        // Versión texto plano
        $mail->AltBody = strip_tags($body);
        
        debugLog("Subject: " . $subject);
        debugLog("Body length: " . strlen($body) . " chars");
        debugLog("Attempting to send via PHPMailer...");
        
        // Enviar
        $mail->send();
        
        debugLog("✅ PHPMailer: Email sent successfully!");
        debugLog("===== EMAIL SEND SUCCESS =====");
        
        return [
            'success' => true,
            'message' => 'Email enviado correctamente con PHPMailer'
        ];
        
    } catch (Exception $e) {
        debugLog("❌ PHPMailer Exception: " . $e->getMessage());
        debugLog("❌ PHPMailer ErrorInfo: " . $mail->ErrorInfo);
        debugLog("===== EMAIL SEND FAILED =====");
        
        return [
            'success' => false,
            'message' => 'Error PHPMailer: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Enviar múltiples emails (por ejemplo, notificación + confirmación)
 * 
 * @param array $emails Array de arrays con ['to', 'subject', 'body', 'toName']
 * @return array Resumen de envíos
 */
function sendMultipleEmails($emails) {
    $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];
    
    foreach ($emails as $email) {
        $result = sendEmail(
            $email['to'],
            $email['subject'],
            $email['body'],
            isset($email['toName']) ? $email['toName'] : ''
        );
        
        if ($result['success']) {
            $results['success']++;
        } else {
            $results['failed']++;
            $results['errors'][] = [
                'to' => $email['to'],
                'error' => $result['message']
            ];
        }
    }
    
    return $results;
}

/**
 * Validar configuración SMTP
 * Útil para debugging
 * 
 * @return array Estado de la configuración
 */
function validateSMTPConfig() {
    $config = [
        'smtp_host' => SMTP_HOST,
        'smtp_port' => SMTP_PORT,
        'smtp_secure' => SMTP_SECURE,
        'smtp_username' => SMTP_USERNAME,
        'smtp_password_set' => !empty(SMTP_PASSWORD),
        'mail_from' => MAIL_FROM_EMAIL
    ];
    
    $issues = [];
    
    if (empty(SMTP_HOST)) {
        $issues[] = 'SMTP_HOST no configurado';
    }
    
    if (empty(SMTP_USERNAME)) {
        $issues[] = 'SMTP_USERNAME no configurado';
    }
    
    if (empty(SMTP_PASSWORD)) {
        $issues[] = 'SMTP_PASSWORD no configurado';
    }
    
    if (empty(MAIL_FROM_EMAIL)) {
        $issues[] = 'MAIL_FROM_EMAIL no configurado';
    }
    
    return [
        'valid' => empty($issues),
        'config' => $config,
        'issues' => $issues
    ];
}

/**
 * Test de conexión SMTP
 * NO usar en producción, solo para debugging
 * 
 * @return array Resultado del test
 */
function testSMTPConnection() {
    global $phpmailerAvailable;
    
    // Si PHPMailer está disponible, usarlo
    if ($phpmailerAvailable && class_exists('PHPMailer')) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = SMTP_AUTH;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port       = SMTP_PORT;
            $mail->SMTPDebug  = 0;
            $mail->Timeout    = 10;
            
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Intentar conectar
            $mail->smtpConnect();
            $mail->smtpClose();
            
            return [
                'success' => true,
                'message' => 'Conexión SMTP exitosa (usando PHPMailer)'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error PHPMailer: ' . $e->getMessage()
            ];
        }
    }
    
    // Fallback: Test manual sin PHPMailer
    try {
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $timeout = 10;
        
        debugLog("Testing SMTP connection to {$host}:{$port}");
        
        // Intentar conexión con fsockopen
        if (SMTP_SECURE === 'ssl') {
            $host = 'ssl://' . $host;
        }
        
        $errno = 0;
        $errstr = '';
        
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        
        if ($socket) {
            fclose($socket);
            return [
                'success' => true,
                'message' => "Conexión exitosa a {$host}:{$port} (test manual)"
            ];
        } else {
            return [
                'success' => false,
                'message' => "No se pudo conectar a {$host}:{$port}. Error: [{$errno}] {$errstr}"
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error en test de conexión: ' . $e->getMessage()
        ];
    }
}

