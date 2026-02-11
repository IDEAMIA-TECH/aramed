<?php
/**
 * ========================================
 * ADMIN - RECUPERACIÓN DE CONTRASEÑA
 * ========================================
 * 
 * Sistema de recuperación de contraseña para usuarios admin
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
if (file_exists(__DIR__ . '/../includes/email_functions.php')) {
    require_once __DIR__ . '/../includes/email_functions.php';
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener parámetros
$token = $_GET['token'] ?? '';
$action = $_GET['action'] ?? 'request';

$mensaje = '';
$tipo_mensaje = '';
$email = '';

// Procesar solicitud de recuperación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'request') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $mensaje = 'Por favor ingresa tu email';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $pdo = getDB();
            
            // Buscar usuario por email
            $stmt = $pdo->prepare("SELECT id, nombre, email FROM admin_usuarios WHERE email = ? AND estado = 'activo'");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Generar token de recuperación
                $token = bin2hex(random_bytes(32));
                $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Guardar token en la base de datos
                $stmt = $pdo->prepare("
                    UPDATE admin_usuarios 
                    SET token_recuperacion = ?, 
                        token_expira = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$token, $expira, $usuario['id']]);
                
                // Enviar email con el enlace de recuperación
                $reset_link = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : 'https://aramedylaboratorio.com') . '/admin/recuperar-password.php?token=' . urlencode($token);
                $nombre_usuario = $usuario['nombre'] ?? $usuario['email'];
                
                $email_enviado = false;
                if (function_exists('sendEmail')) {
                    $asunto = 'Recuperar contraseña - ' . (defined('SITE_NAME') ? SITE_NAME : 'Admin');
                    $cuerpo = '
                        <p>Hola ' . htmlspecialchars($nombre_usuario) . ',</p>
                        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta de administración.</p>
                        <p><strong>Haz clic en el siguiente enlace para elegir una nueva contraseña:</strong></p>
                        <p style="margin: 20px 0;"><a href="' . $reset_link . '" style="display: inline-block; padding: 12px 24px; background: #667eea; color: #fff; text-decoration: none; border-radius: 8px;">Restablecer contraseña</a></p>
                        <p>O copia y pega este enlace en tu navegador:</p>
                        <p style="word-break: break-all; color: #666;">' . htmlspecialchars($reset_link) . '</p>
                        <p>Este enlace expira en <strong>1 hora</strong>. Si no solicitaste este cambio, puedes ignorar este correo.</p>
                        <p>Saludos,<br>' . (defined('SITE_NAME') ? SITE_NAME : 'El equipo') . '</p>
                    ';
                    $resultado = sendEmail($usuario['email'], $asunto, $cuerpo, $nombre_usuario, [], true, []);
                    $email_enviado = !empty($resultado['success']);
                    if (!$email_enviado) {
                        error_log('Recuperar contraseña: fallo envío email a ' . $usuario['email'] . ' - ' . ($resultado['message'] ?? 'unknown'));
                    }
                }
                
                if ($email_enviado) {
                    $mensaje = 'Se ha enviado un enlace de recuperación a tu email. Revisa tu bandeja (y carpeta de spam). El enlace expira en 1 hora.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'No pudimos enviar el correo en este momento. Por favor intenta más tarde o contacta al administrador.';
                    $tipo_mensaje = 'warning';
                }
                
                // En desarrollo, mostrar el enlace aunque falle el envío
                if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                    $mensaje .= '<br><br><strong>Enlace de recuperación (solo en desarrollo):</strong><br>';
                    $mensaje .= '<a href="' . $reset_link . '">' . $reset_link . '</a>';
                }
                
            } else {
                // Por seguridad, no revelar si el email existe o no
                $mensaje = 'Si el email existe en nuestro sistema, recibirás un enlace de recuperación.';
                $tipo_mensaje = 'info';
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error al procesar la solicitud. Por favor intenta más tarde.';
            $tipo_mensaje = 'danger';
        }
    }
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'reset' && $token) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($password) || empty($password_confirm)) {
        $mensaje = 'Por favor completa todos los campos';
        $tipo_mensaje = 'danger';
    } elseif ($password !== $password_confirm) {
        $mensaje = 'Las contraseñas no coinciden';
        $tipo_mensaje = 'danger';
    } elseif (strlen($password) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $pdo = getDB();
            
            // Verificar token válido
            $stmt = $pdo->prepare("
                SELECT id, nombre, email 
                FROM admin_usuarios 
                WHERE token_recuperacion = ? 
                AND token_expira > NOW()
                AND estado = 'activo'
            ");
            $stmt->execute([$token]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Actualizar contraseña
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    UPDATE admin_usuarios 
                    SET password_hash = ?,
                        token_recuperacion = NULL,
                        token_expira = NULL,
                        forzar_cambio_password = 0,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$password_hash, $usuario['id']]);
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($usuario['id'], 'recuperar_password', null, null, null, [
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                    ]);
                }
                
                $mensaje = 'Contraseña actualizada exitosamente. Puedes iniciar sesión ahora.';
                $tipo_mensaje = 'success';
                $action = 'success';
                
            } else {
                $mensaje = 'El enlace de recuperación ha expirado o es inválido. Por favor solicita uno nuevo.';
                $tipo_mensaje = 'danger';
                $action = 'request';
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error al actualizar la contraseña. Por favor intenta más tarde.';
            $tipo_mensaje = 'danger';
        }
    }
}

// Si hay token en GET, verificar si es válido
if ($token && $action !== 'reset' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT id, nombre, email 
            FROM admin_usuarios 
            WHERE token_recuperacion = ? 
            AND token_expira > NOW()
            AND estado = 'activo'
        ");
        $stmt->execute([$token]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            $action = 'reset';
        } else {
            $mensaje = 'El enlace de recuperación ha expirado o es inválido.';
            $tipo_mensaje = 'danger';
            $action = 'request';
        }
    } catch (Exception $e) {
        $action = 'request';
    }
}
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .recovery-container {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 3rem;
        }
        
        .recovery-icon {
            font-size: 4rem;
            color: #667eea;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="recovery-container">
        <div class="recovery-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        
        <h2 class="text-center mb-4">Recuperar Contraseña</h2>
        
        <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($action === 'success'): ?>
            <div class="text-center">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <p class="mt-3">Tu contraseña ha sido actualizada exitosamente.</p>
                <a href="login.php" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </a>
            </div>
            
        <?php elseif ($action === 'reset'): ?>
            <form method="POST" action="?action=reset&token=<?php echo esc($token); ?>">
                <div class="mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" 
                           class="form-control" 
                           name="password" 
                           required 
                           minlength="8"
                           placeholder="Mínimo 8 caracteres">
                    <small class="form-text text-muted">La contraseña debe tener al menos 8 caracteres</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" 
                           class="form-control" 
                           name="password_confirm" 
                           required 
                           minlength="8"
                           placeholder="Repite la contraseña">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle me-2"></i>Actualizar Contraseña
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-muted">Volver al Login</a>
            </div>
            
        <?php else: ?>
            <p class="text-muted text-center mb-4">
                Ingresa tu email y te enviaremos un enlace para recuperar tu contraseña.
            </p>
            
            <form method="POST" action="?action=request">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" 
                           class="form-control" 
                           name="email" 
                           value="<?php echo esc($email); ?>"
                           required 
                           placeholder="tu@email.com">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-envelope me-2"></i>Enviar Enlace de Recuperación
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-muted">Volver al Login</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

