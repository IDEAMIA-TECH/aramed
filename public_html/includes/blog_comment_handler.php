<?php
/**
 * ========================================
 * HANDLER DE COMENTARIOS DEL BLOG
 * ========================================
 * 
 * Procesa el envío de comentarios en artículos del blog
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Cargar configuración
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/email_functions.php';

// Configurar headers para JSON
header('Content-Type: application/json');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception('Error de conexión a la base de datos');
    }

    $ip_address = function_exists('getClientIpAddress') ? getClientIpAddress() : ($_SERVER['REMOTE_ADDR'] ?? '');

    if (!empty($_POST['comment_company_fax'])) {
        error_log("SPAM [blog_comment]: honeypot. IP: {$ip_address}");
        throw new Exception('No se pudo enviar el comentario.');
    }

    $form_ts = isset($_POST['form_timestamp']) ? (int) $_POST['form_timestamp'] : 0;
    $elapsed = time() - $form_ts;
    if ($form_ts > 0 && ($elapsed < 3 || $elapsed > 7200)) {
        error_log("SPAM [blog_comment]: timestamp. IP: {$ip_address}");
        throw new Exception('Sesión inválida. Recarga el artículo e intenta de nuevo.');
    }

    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (strlen($ua) < 12) {
        error_log("SPAM [blog_comment]: UA. IP: {$ip_address}");
        throw new Exception('No se pudo validar el envío.');
    }

    $rc_cm = aramed_verify_recaptcha_v3($_POST['g-recaptcha-response'] ?? '', $ip_address);
    if (!$rc_cm['ok']) {
        error_log('SPAM [blog_comment]: reCAPTCHA ' . ($rc_cm['reason'] ?? '') . " IP: {$ip_address}");
        throw new Exception('La verificación de seguridad falló. Recarga e intenta de nuevo.');
    }

    // Obtener y validar datos
    $articulo_id = isset($_POST['articulo_id']) ? (int)$_POST['articulo_id'] : 0;
    $nombre = isset($_POST['nombre']) ? sanitizeInput($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? sanitizeEmail($_POST['email']) : '';
    $comentario = isset($_POST['comentario']) ? sanitizeInput($_POST['comentario']) : '';

    // Validaciones
    if (empty($articulo_id) || $articulo_id <= 0) {
        throw new Exception('ID de artículo inválido');
    }

    if (empty($nombre) || strlen($nombre) < 2) {
        throw new Exception('El nombre debe tener al menos 2 caracteres');
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    if (aramed_is_disposable_email_domain($email)) {
        error_log("SPAM [blog_comment]: disposable. IP: {$ip_address}");
        throw new Exception('Usa un correo válido (no temporal).');
    }

    if (empty($comentario) || strlen($comentario) < 10) {
        throw new Exception('El comentario debe tener al menos 10 caracteres');
    }

    if (aramed_text_has_obvious_spam_patterns($comentario) || aramed_text_has_obvious_spam_patterns($nombre)) {
        error_log("SPAM [blog_comment]: patrones. IP: {$ip_address}");
        throw new Exception('El comentario contiene texto no permitido.');
    }

    $stmt_rate = $pdo->prepare('SELECT COUNT(*) FROM blog_comentarios WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
    $stmt_rate->execute([$ip_address]);
    if ((int) $stmt_rate->fetchColumn() >= 5) {
        error_log("SPAM [blog_comment]: límite hora. IP: {$ip_address}");
        throw new Exception('Demasiados comentarios recientes. Intenta más tarde.');
    }

    // Verificar que el artículo existe y está publicado
    $sql_articulo = "SELECT id, titulo FROM blog_articulos WHERE id = ? AND estado = 'publicado'";
    $stmt_articulo = $pdo->prepare($sql_articulo);
    $stmt_articulo->execute([$articulo_id]);
    $articulo = $stmt_articulo->fetch(PDO::FETCH_ASSOC);

    if (!$articulo) {
        throw new Exception('Artículo no encontrado o no disponible');
    }

    $user_agent = $ua;

    // Insertar comentario
    $sql_comentario = "
        INSERT INTO blog_comentarios (
            articulo_id, nombre, email, comentario, 
            ip_address, user_agent, estado, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'pendiente', NOW())
    ";

    $stmt_comentario = $pdo->prepare($sql_comentario);
    $result = $stmt_comentario->execute([
        $articulo_id,
        $nombre,
        $email,
        $comentario,
        $ip_address,
        $user_agent
    ]);

    if (!$result) {
        throw new Exception('Error al guardar el comentario');
    }

    $comentario_id = $pdo->lastInsertId();

    if (defined('CONTACT_EMAIL') && !empty(CONTACT_EMAIL)) {
        sendCommentNotification($articulo, $nombre, $email, $comentario, $comentario_id);
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Comentario enviado correctamente. Será revisado antes de publicarse.',
        'comentario_id' => $comentario_id
    ]);

} catch (Exception $e) {
    // Log del error
    error_log("Error en blog_comment_handler: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Enviar notificación de nuevo comentario
 */
function sendCommentNotification($articulo, $nombre, $email, $comentario, $comentario_id) {
    try {
        $subject = "Nuevo comentario en: " . $articulo['titulo'];
        
        $message = "
        <h2>Nuevo comentario recibido</h2>
        <p><strong>Artículo:</strong> " . esc($articulo['titulo']) . "</p>
        <p><strong>Nombre:</strong> " . esc($nombre) . "</p>
        <p><strong>Email:</strong> " . esc($email) . "</p>
        <p><strong>Comentario:</strong></p>
        <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #0066cc; margin: 10px 0;'>
            " . nl2br(esc($comentario)) . "
        </div>
        <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
        <p><strong>ID del comentario:</strong> " . $comentario_id . "</p>
        <hr>
        <p><em>Este comentario está pendiente de aprobación.</em></p>
        ";

        // Usar la función de email existente
        if (function_exists('sendEmail')) {
            sendEmail(
                CONTACT_EMAIL,
                $subject,
                $message,
                $nombre . ' <' . $email . '>'
            );
        }
    } catch (Exception $e) {
        error_log("Error enviando notificación de comentario: " . $e->getMessage());
    }
}
?>
