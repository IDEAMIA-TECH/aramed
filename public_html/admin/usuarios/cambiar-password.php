<?php
/**
 * ========================================
 * ADMIN - CAMBIAR CONTRASEÑA
 * ========================================
 * 
 * Página para cambiar contraseña (obligatorio o voluntario)
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

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();
$usuario_id = $current_user['id'];

if (!$usuario_id) {
    header('Location: ../login.php');
    exit;
}

// Verificar si es cambio forzado
$forzar = isset($_GET['forzar']) && $_GET['forzar'] == '1';

// Manejar cambio de contraseña
$mensaje = '';
$tipo_mensaje = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';
    
    // Validaciones
    if (empty($password_nueva)) {
        $mensaje = 'La nueva contraseña es requerida';
        $tipo_mensaje = 'danger';
    } elseif (strlen($password_nueva) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres';
        $tipo_mensaje = 'danger';
    } elseif ($password_nueva !== $password_confirmar) {
        $mensaje = 'Las contraseñas no coinciden';
        $tipo_mensaje = 'danger';
    } else {
        // Obtener usuario actual
        $sql_usuario = "SELECT password_hash, forzar_cambio_password FROM admin_usuarios WHERE id = ?";
        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->execute([$usuario_id]);
        $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            $mensaje = 'Usuario no encontrado';
            $tipo_mensaje = 'danger';
        } else {
            // Si no es cambio forzado, verificar contraseña actual
            if (!$forzar) {
                if (empty($password_actual)) {
                    $mensaje = 'La contraseña actual es requerida';
                    $tipo_mensaje = 'danger';
                } elseif (!password_verify($password_actual, $usuario['password_hash'])) {
                    $mensaje = 'La contraseña actual es incorrecta';
                    $tipo_mensaje = 'danger';
                }
            }
            
            // Si no hay errores, cambiar contraseña
            if (empty($mensaje)) {
                $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                
                $sql_update = "
                    UPDATE admin_usuarios 
                    SET password_hash = ?, 
                        forzar_cambio_password = 0,
                        ultimo_cambio_password = NOW(),
                        intentos_fallidos = 0,
                        bloqueado_hasta = NULL,
                        updated_at = NOW()
                    WHERE id = ?
                ";
                
                $stmt_update = $pdo->prepare($sql_update);
                $resultado = $stmt_update->execute([$password_hash, $usuario_id]);
                
                if ($resultado) {
                    // Registrar actividad
                    if (function_exists('logActivity')) {
                        logActivity($usuario_id, 'cambiar_password', 'usuarios', $usuario_id, 'usuario', [
                            'forzado' => $forzar
                        ]);
                    }
                    
                    $mensaje = 'Contraseña actualizada correctamente';
                    $tipo_mensaje = 'success';
                    $success = true;
                    
                    // Si era cambio forzado, redirigir al dashboard después de 2 segundos
                    if ($forzar) {
                        header('Refresh: 2; url=../index.php');
                    }
                } else {
                    $mensaje = 'Error al actualizar la contraseña';
                    $tipo_mensaje = 'danger';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .password-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        
        .alert-forzar {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #000;
            border: none;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-container">
            <div class="text-center mb-4">
                <h2 class="mb-2">
                    <i class="bi bi-shield-lock"></i> Cambiar Contraseña
                </h2>
                <p class="text-muted"><?php echo esc($current_user['nombre']); ?></p>
            </div>
            
            <?php if ($forzar): ?>
            <div class="alert alert-forzar">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Cambio de contraseña requerido:</strong> Debe cambiar su contraseña antes de continuar.
            </div>
            <?php endif; ?>
            
            <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo esc($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (!$success || !$forzar): ?>
            <form method="POST" action="">
                <?php if (!$forzar): ?>
                <div class="mb-3">
                    <label for="password_actual" class="form-label">Contraseña Actual</label>
                    <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="password_nueva" class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control" id="password_nueva" name="password_nueva" required minlength="8">
                    <small class="form-text text-muted">Mínimo 8 caracteres</small>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required minlength="8">
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" name="cambiar_password" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Cambiar Contraseña
                    </button>
                </div>
            </form>
            <?php endif; ?>
            
            <?php if (!$forzar): ?>
            <div class="mt-3 text-center">
                <a href="../index.php" class="text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

