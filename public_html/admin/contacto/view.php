<?php
/**
 * ========================================
 * ADMIN - VISTA DETALLADA DE MENSAJE
 * ========================================
 * 
 * Vista y gestión detallada de un mensaje de contacto
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
    checkPermission('contacto', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Obtener ID del mensaje
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

$success_message = '';
$error_message = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar permisos
        if (function_exists('checkPermission')) {
            checkPermission('contacto', 'editar');
        }
        
        $status = $_POST['status'] ?? '';
        $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
        $notes = trim($_POST['notes'] ?? '');
        
        $stmt = $pdo->prepare("
            UPDATE contact_messages 
            SET status = ?, assigned_to = ?, notes = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$status, $assigned_to, $notes, $id]);
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'contacto', $id, 'mensaje', [
                'status' => $status,
                'assigned_to' => $assigned_to
            ]);
        }
        
        $success_message = 'Mensaje actualizado exitosamente';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener mensaje
$stmt = $pdo->prepare("
    SELECT cm.*, 
           au.nombre as asignado_nombre,
           au.email as asignado_email,
           au_creator.nombre as creador_nombre
    FROM contact_messages cm
    LEFT JOIN admin_usuarios au ON cm.assigned_to = au.id
    LEFT JOIN admin_usuarios au_creator ON cm.assigned_to = au_creator.id
    WHERE cm.id = ?
");
$stmt->execute([$id]);
$mensaje = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mensaje) {
    header('Location: index.php');
    exit;
}

// Obtener usuarios para asignación
$stmt = $pdo->query("SELECT id, nombre, email FROM admin_usuarios WHERE estado = 'activo' ORDER BY nombre");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Respuestas rápidas (plantillas)
$respuestas_rapidas = [
    'agradecimiento' => [
        'asunto' => 'Gracias por contactarnos',
        'mensaje' => "Estimado/a {$mensaje['nombre']},\n\nGracias por contactarnos. Hemos recibido su mensaje y nos pondremos en contacto con usted a la brevedad posible.\n\nSaludos cordiales,\nEquipo Aramed y Laboratorios"
    ],
    'informacion' => [
        'asunto' => 'Información solicitada',
        'mensaje' => "Estimado/a {$mensaje['nombre']},\n\nEn respuesta a su consulta, le proporcionamos la siguiente información:\n\n[Información específica]\n\nSi tiene alguna pregunta adicional, no dude en contactarnos.\n\nSaludos cordiales,\nEquipo Aramed y Laboratorios"
    ],
    'cotizacion' => [
        'asunto' => 'Cotización solicitada',
        'mensaje' => "Estimado/a {$mensaje['nombre']},\n\nHemos recibido su solicitud de cotización. Nuestro equipo de ventas se pondrá en contacto con usted en las próximas 24-48 horas para proporcionarle una cotización detallada.\n\nSaludos cordiales,\nEquipo Aramed y Laboratorios"
    ]
];

$current_page = 'view.php';
$current_dir = 'contacto';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Mensaje - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .message-content {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            border-radius: 8px;
            white-space: pre-wrap;
            line-height: 1.6;
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-envelope me-2"></i>Mensaje de Contacto
                            </h2>
                            <p class="mb-0 opacity-75">ID: #<?php echo $mensaje['id']; ?></p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Lista
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
                
                <div class="row">
                    <!-- Información del Mensaje -->
                    <div class="col-lg-8">
                        <!-- Datos del Contacto -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-person me-2"></i>Información del Contacto
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Nombre</label>
                                    <div class="fw-bold"><?php echo esc($mensaje['nombre']); ?></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Email</label>
                                    <div>
                                        <a href="mailto:<?php echo esc($mensaje['email']); ?>" class="text-decoration-none">
                                            <i class="bi bi-envelope me-1"></i><?php echo esc($mensaje['email']); ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Teléfono</label>
                                    <div>
                                        <a href="tel:<?php echo esc($mensaje['telefono']); ?>" class="text-decoration-none">
                                            <i class="bi bi-telephone me-1"></i><?php echo esc($mensaje['telefono']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php if ($mensaje['institucion']): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Institución</label>
                                    <div>
                                        <i class="bi bi-building me-1"></i><?php echo esc($mensaje['institucion']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Mensaje -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-chat-left-text me-2"></i>Mensaje
                            </h5>
                            <div class="mb-2">
                                <span class="badge bg-primary"><?php echo esc($mensaje['asunto']); ?></span>
                            </div>
                            <div class="message-content">
                                <?php echo esc($mensaje['mensaje']); ?>
                            </div>
                        </div>
                        
                        <!-- Metadata -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-info-circle me-2"></i>Información Adicional
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Fecha de recepción:</small><br>
                                    <strong><?php echo date('d/m/Y H:i:s', strtotime($mensaje['created_at'])); ?></strong>
                                </div>
                                <?php if ($mensaje['updated_at']): ?>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Última actualización:</small><br>
                                    <strong><?php echo date('d/m/Y H:i:s', strtotime($mensaje['updated_at'])); ?></strong>
                                </div>
                                <?php endif; ?>
                                <?php if ($mensaje['ip_address']): ?>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">IP Address:</small><br>
                                    <code><?php echo esc($mensaje['ip_address']); ?></code>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel de Gestión -->
                    <div class="col-lg-4">
                        <form method="POST" action="">
                            <div class="info-card">
                                <h5 class="mb-3">
                                    <i class="bi bi-gear me-2"></i>Gestión del Mensaje
                                </h5>
                                
                                <!-- Estado -->
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="status" required>
                                        <option value="nuevo" <?php echo $mensaje['status'] === 'nuevo' ? 'selected' : ''; ?>>Nuevo</option>
                                        <option value="en_proceso" <?php echo $mensaje['status'] === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                        <option value="respondido" <?php echo $mensaje['status'] === 'respondido' ? 'selected' : ''; ?>>Respondido</option>
                                        <option value="cerrado" <?php echo $mensaje['status'] === 'cerrado' ? 'selected' : ''; ?>>Cerrado</option>
                                    </select>
                                </div>
                                
                                <!-- Asignar a -->
                                <div class="mb-3">
                                    <label class="form-label">Asignar a</label>
                                    <select class="form-select" name="assigned_to">
                                        <option value="">Sin asignar</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['id']; ?>" <?php echo $mensaje['assigned_to'] == $usuario['id'] ? 'selected' : ''; ?>>
                                            <?php echo esc($usuario['nombre']); ?> (<?php echo esc($usuario['email']); ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Notas internas -->
                                <div class="mb-3">
                                    <label class="form-label">Notas Internas</label>
                                    <textarea class="form-control" name="notes" rows="4" placeholder="Notas sobre este mensaje..."><?php echo esc($mensaje['notes'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Solo visible para administradores</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                        
                        <!-- Respuestas Rápidas -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-lightning me-2"></i>Respuestas Rápidas
                            </h5>
                            <div class="d-grid gap-2">
                                <?php foreach ($respuestas_rapidas as $key => $respuesta): ?>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="usarRespuestaRapida('<?php echo $key; ?>')">
                                    <i class="bi bi-envelope-paper me-1"></i><?php echo ucfirst(str_replace('_', ' ', $key)); ?>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3">
                                <a href="mailto:<?php echo esc($mensaje['email']); ?>?subject=<?php echo urlencode('Re: ' . $mensaje['asunto']); ?>" class="btn btn-success w-100">
                                    <i class="bi bi-envelope me-2"></i>Responder por Email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const respuestasRapidas = <?php echo json_encode($respuestas_rapidas); ?>;
        
        function usarRespuestaRapida(key) {
            const respuesta = respuestasRapidas[key];
            const email = '<?php echo esc($mensaje['email']); ?>';
            const subject = encodeURIComponent(respuesta.asunto);
            const body = encodeURIComponent(respuesta.mensaje);
            
            window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
        }
    </script>
</body>
</html>

