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
    
    // Si PHPMailer está disponible, usarlo
    if ($phpmailerAvailable && class_exists('PHPMailer')) {
        return sendEmailWithPHPMailer($to, $subject, $body, $toName, $attachments);
    }
    
    // Fallback: usar mail() nativo con headers SMTP
    try {
        // Configurar headers
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">";
        $headers[] = "Reply-To: " . MAIL_FROM_EMAIL;
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $headers[] = "X-Priority: 3";
        
        // Configurar parámetros adicionales para SMTP
        $additional_parameters = "-f" . MAIL_FROM_EMAIL;
        
        // Enviar email
        $result = mail($to, $subject, $body, implode("\r\n", $headers), $additional_parameters);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Email enviado correctamente'
            ];
        } else {
            throw new Exception('Error al enviar email con mail()');
        }
        
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        
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
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = SMTP_AUTH;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        
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
        
        // Configurar timeout
        $mail->Timeout = 30;
        
        // Remitente
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        
        // Destinatario
        if (empty($toName)) {
            $mail->addAddress($to);
        } else {
            $mail->addAddress($to, $toName);
        }
        
        // Reply-To
        $mail->addReplyTo(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        
        // Archivos adjuntos (si los hay)
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $mail->addAttachment($attachment);
                }
            }
        }
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        // Versión texto plano
        $mail->AltBody = strip_tags($body);
        
        // Enviar
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Email enviado correctamente con PHPMailer'
        ];
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        
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
    $mail = new PHPMailer(true);
    
    try {
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
            'message' => 'Conexión SMTP exitosa'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error de conexión: ' . $e->getMessage()
        ];
    }
}

