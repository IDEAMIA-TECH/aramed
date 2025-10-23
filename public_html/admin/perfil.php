<?php
// perfil.php - Edición de Perfil del Usuario
define('ARAMED_SITE', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/auth_check.php';

$current_page = 'perfil.php';
$current_dir = 'admin';

$success_message = '';
$error_message = '';

// Obtener datos del usuario actual
$usuario_actual = null;
try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM admin_usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['admin_user_id']]);
    $usuario_actual = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario_actual) {
        $error_message = 'Usuario no encontrado';
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDB();
        
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $password_actual = trim($_POST['password_actual']);
        $password_nueva = trim($_POST['password_nueva']);
        $password_confirmar = trim($_POST['password_confirmar']);
        
        if (empty($nombre) || empty($email)) {
            throw new Exception('Nombre y email son obligatorios');
        }
        
        // Verificar si el email ya existe en otro usuario
        $stmt = $pdo->prepare("SELECT id FROM admin_usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['admin_user_id']]);
        if ($stmt->fetch()) {
            throw new Exception('El email ya está registrado por otro usuario');
        }
        
        // Si se quiere cambiar la contraseña
        if (!empty($password_nueva)) {
            if (empty($password_actual)) {
                throw new Exception('Debes ingresar tu contraseña actual para cambiarla');
            }
            
            // Verificar contraseña actual
            if (!password_verify($password_actual, $usuario_actual['password'])) {
                throw new Exception('La contraseña actual es incorrecta');
            }
            
            if ($password_nueva !== $password_confirmar) {
                throw new Exception('Las contraseñas nuevas no coinciden');
            }
            
            if (strlen($password_nueva) < 6) {
                throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
            }
            
            $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE admin_usuarios SET nombre = ?, email = ?, password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$nombre, $email, $password_hash, $_SESSION['admin_user_id']]);
            
            $success_message = 'Perfil y contraseña actualizados exitosamente';
        } else {
            // Solo actualizar datos básicos
            $stmt = $pdo->prepare("UPDATE admin_usuarios SET nombre = ?, email = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$nombre, $email, $_SESSION['admin_user_id']]);
            
            $success_message = 'Perfil actualizado exitosamente';
        }
        
        // Actualizar datos en sesión
        $_SESSION['admin_user_name'] = $nombre;
        $_SESSION['admin_user_email'] = $email;
        
        // Recargar datos del usuario
        $stmt = $pdo->prepare("SELECT * FROM admin_usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['admin_user_id']]);
        $usuario_actual = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Aramed Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow);
        }

        .admin-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .admin-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 3rem;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .profile-info h3 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .profile-info p {
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .profile-role {
            background: var(--light-color);
            color: var(--dark-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            margin-top: 1rem;
        }

        .profile-role.admin {
            background: var(--danger-color);
            color: white;
        }

        .profile-role.editor {
            background: var(--warning-color);
            color: white;
        }

        .form-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .password-section {
            background: #f8f9fa;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .password-section h5 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <?php include 'includes/admin_menu.php'; ?>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9">
                <!-- Header -->
                <div class="admin-header">
                    <div class="container-fluid">
                        <h1><i class="bi bi-person-circle me-3"></i>Mi Perfil</h1>
                        <p>Gestiona tu información personal y configuración de cuenta</p>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo esc($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo esc($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($usuario_actual): ?>
                <!-- Profile Info -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="profile-card text-center">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($usuario_actual['nombre'], 0, 2)); ?>
                            </div>
                            <div class="profile-info">
                                <h3><?php echo esc($usuario_actual['nombre']); ?></h3>
                                <p><i class="bi bi-envelope me-2"></i><?php echo esc($usuario_actual['email']); ?></p>
                                <p><i class="bi bi-shield-check me-2"></i>ID: <?php echo $usuario_actual['id']; ?></p>
                                <div class="profile-role <?php echo $usuario_actual['rol']; ?>">
                                    <?php echo ucfirst($usuario_actual['rol']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo $usuario_actual['activo'] ? 'Activo' : 'Inactivo'; ?></div>
                                    <div class="stats-label">Estado de Cuenta</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo date('d/m/Y', strtotime($usuario_actual['created_at'])); ?></div>
                                    <div class="stats-label">Miembro Desde</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo $usuario_actual['last_login'] ? date('d/m/Y', strtotime($usuario_actual['last_login'])) : 'Nunca'; ?></div>
                                    <div class="stats-label">Último Acceso</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="form-card">
                    <h3 class="mb-4">
                        <i class="bi bi-pencil-square me-2"></i>Editar Perfil
                    </h3>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" name="nombre" 
                                           value="<?php echo esc($usuario_actual['nombre']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?php echo esc($usuario_actual['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="password-section">
                            <h5><i class="bi bi-key me-2"></i>Cambiar Contraseña</h5>
                            <p class="text-muted">Deja en blanco si no quieres cambiar tu contraseña</p>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Contraseña Actual</label>
                                        <input type="password" class="form-control" name="password_actual" 
                                               placeholder="Ingresa tu contraseña actual">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password_nueva" 
                                               placeholder="Mínimo 6 caracteres">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password_confirmar" 
                                               placeholder="Repite la nueva contraseña">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Actualizar Perfil
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                            </a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
