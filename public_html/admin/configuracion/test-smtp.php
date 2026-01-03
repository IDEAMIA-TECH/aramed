<?php
/**
 * ========================================
 * ADMIN - TEST SMTP
 * ========================================
 * 
 * Prueba la configuración SMTP
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('configuracion', 'editar');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$test_result = null;
$test_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    $test_email = $_POST['test_email'];
    
    // Obtener configuración SMTP desde BD o constantes
    $smtp_host = getConfig('smtp_host', SMTP_HOST);
    $smtp_port = getConfig('smtp_puerto', SMTP_PORT);
    $smtp_user = getConfig('smtp_usuario', SMTP_USERNAME);
    $smtp_pass = getConfig('smtp_password', SMTP_PASSWORD);
    $smtp_encryption = getConfig('smtp_encryption', SMTP_SECURE);
    $smtp_from_email = getConfig('smtp_from_email', MAIL_FROM_EMAIL);
    $smtp_from_name = getConfig('smtp_from_name', MAIL_FROM_NAME);
    
    // Intentar enviar email de prueba
    require_once __DIR__ . '/../../includes/email_functions.php';
    
    $subject = 'Prueba de Configuración SMTP - ' . SITE_NAME;
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='success'>
                <h2>✅ Configuración SMTP Exitosa</h2>
                <p>Este es un email de prueba enviado desde el panel de administración de <strong>" . SITE_NAME . "</strong>.</p>
                <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                <p>Si recibes este mensaje, la configuración SMTP está funcionando correctamente.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $result = sendEmail($test_email, $subject, $message, 'Usuario de Prueba');
    
    if ($result) {
        $test_result = 'success';
        $test_message = 'Email de prueba enviado exitosamente a ' . $test_email;
    } else {
        $test_result = 'error';
        $test_message = 'Error al enviar el email de prueba. Verifica la configuración SMTP.';
    }
}

$current_page = 'test-smtp.php';
$current_dir = 'configuracion';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test SMTP - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .config-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-envelope-check me-2"></i>Test de Configuración SMTP
                            </h2>
                            <p class="mb-0 opacity-75">Prueba la configuración de correo electrónico</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Configuración
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($test_result === 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($test_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($test_result === 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($test_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Formulario de Prueba -->
                <div class="config-card">
                    <h4 class="mb-4">
                        <i class="bi bi-envelope me-2"></i>Enviar Email de Prueba
                    </h4>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Se enviará un email de prueba a la dirección que especifiques para verificar que la configuración SMTP está funcionando correctamente.
                    </div>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email de Destino</label>
                            <input type="email" 
                                   class="form-control" 
                                   name="test_email" 
                                   value="<?php echo esc($current_user['email']); ?>" 
                                   required
                                   placeholder="email@ejemplo.com">
                            <small class="form-text text-muted">Ingresa el email donde quieres recibir el mensaje de prueba</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Configuración Actual</label>
                            <div class="bg-light p-3 rounded">
                                <small>
                                    <strong>Servidor:</strong> <?php echo esc(getConfig('smtp_host', SMTP_HOST)); ?><br>
                                    <strong>Puerto:</strong> <?php echo esc(getConfig('smtp_puerto', SMTP_PORT)); ?><br>
                                    <strong>Encriptación:</strong> <?php echo esc(getConfig('smtp_encryption', SMTP_SECURE)); ?><br>
                                    <strong>Usuario:</strong> <?php echo esc(getConfig('smtp_usuario', SMTP_USERNAME)); ?><br>
                                    <strong>Remitente:</strong> <?php echo esc(getConfig('smtp_from_email', MAIL_FROM_EMAIL)); ?>
                                </small>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Enviar Email de Prueba
                        </button>
                        <a href="index.php?tab=smtp" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

