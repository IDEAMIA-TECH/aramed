<?php
/**
 * ========================================
 * ADMIN - CONFIGURACIÓN AVANZADA NEWSLETTER
 * ========================================
 * 
 * Configuración avanzada del newsletter
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
    checkPermission('newsletter', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$success_message = '';
$error_message = '';

// Cargar configuración actual
$config = [
    'campos_obligatorios' => getConfig('newsletter_campos_obligatorios', 'email,nombre') ?: 'email,nombre',
    'texto_legal' => getConfig('newsletter_texto_legal', 'Al suscribirte, aceptas recibir comunicaciones de nuestra empresa. Puedes cancelar tu suscripción en cualquier momento.'),
    'texto_bienvenida' => getConfig('newsletter_texto_bienvenida', 'Gracias por suscribirte a nuestro newsletter.'),
    'email_remitente' => getConfig('newsletter_email_remitente', CONTACT_EMAIL),
    'nombre_remitente' => getConfig('newsletter_nombre_remitente', SITE_NAME),
    'activar_doble_optin' => getConfig('newsletter_doble_optin', '0') === '1',
    'activar_notificaciones' => getConfig('newsletter_notificaciones', '1') === '1'
];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        setConfig('newsletter_campos_obligatorios', $_POST['campos_obligatorios'] ?? 'email,nombre', 'text', 'newsletter');
        setConfig('newsletter_texto_legal', $_POST['texto_legal'] ?? '', 'html', 'newsletter');
        setConfig('newsletter_texto_bienvenida', $_POST['texto_bienvenida'] ?? '', 'text', 'newsletter');
        setConfig('newsletter_email_remitente', $_POST['email_remitente'] ?? '', 'text', 'newsletter');
        setConfig('newsletter_nombre_remitente', $_POST['nombre_remitente'] ?? '', 'text', 'newsletter');
        setConfig('newsletter_doble_optin', isset($_POST['activar_doble_optin']) ? '1' : '0', 'boolean', 'newsletter');
        setConfig('newsletter_notificaciones', isset($_POST['activar_notificaciones']) ? '1' : '0', 'boolean', 'newsletter');
        
        $success_message = 'Configuración guardada exitosamente';
        
        // Recargar configuración
        $config = [
            'campos_obligatorios' => getConfig('newsletter_campos_obligatorios', 'email,nombre'),
            'texto_legal' => getConfig('newsletter_texto_legal', ''),
            'texto_bienvenida' => getConfig('newsletter_texto_bienvenida', ''),
            'email_remitente' => getConfig('newsletter_email_remitente', CONTACT_EMAIL),
            'nombre_remitente' => getConfig('newsletter_nombre_remitente', SITE_NAME),
            'activar_doble_optin' => getConfig('newsletter_doble_optin', '0') === '1',
            'activar_notificaciones' => getConfig('newsletter_notificaciones', '1') === '1'
        ];
        
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'newsletter', null, 'Configuración avanzada actualizada');
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'config.php';
$current_dir = 'newsletter';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Avanzada - Newsletter Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/4u89qw1ptzfqell0ybjhqth1cc16ilb1y0792h3momw4lk8l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
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
                                <i class="bi bi-gear me-2"></i>Configuración Avanzada
                            </h2>
                            <p class="mb-0 opacity-75">Configuración avanzada del sistema de newsletter</p>
                        </div>
                        <a href="../newsletter-simple.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <!-- Campos Obligatorios -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2"></i>Campos Obligatorios
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Campos Obligatorios</label>
                                <input type="text" class="form-control" name="campos_obligatorios" 
                                       value="<?php echo esc($config['campos_obligatorios']); ?>" 
                                       placeholder="email,nombre,institucion">
                                <small class="form-text text-muted">Separados por comas. Ejemplo: email,nombre,institucion</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Textos -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-file-text me-2"></i>Textos del Formulario
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Texto de Bienvenida</label>
                                <textarea class="form-control" name="texto_bienvenida" rows="3"><?php echo esc($config['texto_bienvenida']); ?></textarea>
                                <small class="form-text text-muted">Mensaje que se muestra después de suscribirse</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Texto Legal</label>
                                <textarea class="form-control" name="texto_legal" id="texto_legal" rows="5"><?php echo esc($config['texto_legal']); ?></textarea>
                                <small class="form-text text-muted">Texto legal que aparece en el formulario de suscripción</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de Email -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-envelope me-2"></i>Configuración de Email
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Remitente</label>
                                    <input type="email" class="form-control" name="email_remitente" 
                                           value="<?php echo esc($config['email_remitente']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre Remitente</label>
                                    <input type="text" class="form-control" name="nombre_remitente" 
                                           value="<?php echo esc($config['nombre_remitente']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Opciones Avanzadas -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-sliders me-2"></i>Opciones Avanzadas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="activar_doble_optin" 
                                       name="activar_doble_optin" <?php echo $config['activar_doble_optin'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activar_doble_optin">
                                    <strong>Activar Doble Opt-in</strong>
                                    <br><small class="text-muted">Requiere confirmación por email antes de activar la suscripción</small>
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="activar_notificaciones" 
                                       name="activar_notificaciones" <?php echo $config['activar_notificaciones'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activar_notificaciones">
                                    <strong>Activar Notificaciones</strong>
                                    <br><small class="text-muted">Enviar notificación por email cuando haya una nueva suscripción</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar TinyMCE para texto legal
        tinymce.init({
            selector: '#texto_legal',
            height: 200,
            menubar: false,
            plugins: 'code',
            toolbar: 'undo redo | formatselect | bold italic | bullist numlist | code',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
    </script>
</body>
</html>

