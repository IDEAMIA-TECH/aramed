<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Newsletter Simple Handler
 * ========================================
 * 
 * Procesa suscripciones simples al boletín informativo
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Configuración
define('ARAMED_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/email_functions.php';

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
    // Obtener y sanitizar datos
    $email = sanitizeEmail($_POST['email'] ?? '');
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $source = sanitizeInput($_POST['source'] ?? 'boletin');
    
    // Validar email
    if (empty($email)) {
        throw new Exception("El correo electrónico es obligatorio.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo electrónico no es válido.");
    }
    
    // Verificar si ya está suscrito
    $stmt = $pdo->prepare("SELECT id, status FROM newsletter_simple WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        if ($existing['status'] === 'active') {
            throw new Exception("Este correo ya está suscrito a nuestro boletín.");
        } else {
            // Reactivar suscripción
            $stmt = $pdo->prepare("UPDATE newsletter_simple SET status = 'active', source = ?, updated_at = NOW() WHERE email = ?");
            $stmt->execute([$source, $email]);
            
            $response = [
                'success' => true,
                'message' => '¡Bienvenido de nuevo! Te has vuelto a suscribir a nuestro boletín.'
            ];
        }
    } else {
        // Insertar nueva suscripción
        $sql = "INSERT INTO newsletter_simple (email, nombre, source, ip_address, user_agent, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())";
        $stmt = $pdo->prepare($sql);
        
        $result = $stmt->execute([
            $email,
            $nombre,
            $source,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        if (!$result) {
            throw new Exception("Error al guardar la suscripción.");
        }
        
        $response = [
            'success' => true,
            'message' => '¡Gracias por suscribirte! Recibirás nuestras novedades en tu correo.'
        ];
    }
    
    // Enviar notificación por email (opcional)
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
                            <span class='value'>" . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "</span>
                        </div>
                    </div>
                </div>
            </body>
            </html>";
            
            sendEmail($to, $subject, $message);
        } catch (Exception $e) {
            // No fallar si el email no se puede enviar
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
?>
