<?php
/**
 * ========================================
 * ADMIN - LOGIN
 * ========================================
 * 
 * Página de login para el panel de administración
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

// Iniciar sesión
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Crear tabla de administradores si no existe
$sql_create_table = "
    CREATE TABLE IF NOT EXISTS admin_usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        rol ENUM('admin', 'editor') DEFAULT 'editor',
        estado ENUM('activo', 'inactivo') DEFAULT 'activo',
        ultimo_login TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
";

$pdo->exec($sql_create_table);

// Verificar si existe algún administrador
$sql_check_admin = "SELECT COUNT(*) as count FROM admin_usuarios WHERE rol = 'admin'";
$stmt_check = $pdo->prepare($sql_check_admin);
$stmt_check->execute();
$admin_count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];

// Si no hay administradores, crear uno por defecto
if ($admin_count == 0) {
    $default_password = 'admin123'; // Cambiar en producción
    $password_hash = password_hash($default_password, PASSWORD_DEFAULT);
    
    $sql_insert_admin = "
        INSERT INTO admin_usuarios (username, email, password_hash, nombre, rol, estado) 
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    
    $stmt_insert = $pdo->prepare($sql_insert_admin);
    $stmt_insert->execute([
        'admin',
        'admin@aramedylaboratorio.com',
        $password_hash,
        'Administrador Principal',
        'admin',
        'activo'
    ]);
}

$error = '';
$success = '';

// Verificar mensajes de URL
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $success = 'Sesión cerrada correctamente';
}
if (isset($_GET['expired']) && $_GET['expired'] == '1') {
    $error = 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.';
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor completa todos los campos';
    } else {
        // Buscar usuario
        $sql = "SELECT * FROM admin_usuarios WHERE (username = ? OR email = ?) AND estado = 'activo'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // Login exitoso
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $usuario['id'];
            $_SESSION['admin_username'] = $usuario['username'];
            $_SESSION['admin_nombre'] = $usuario['nombre'];
            $_SESSION['admin_rol'] = $usuario['rol'];
            
            // Actualizar último login
            $sql_update_login = "UPDATE admin_usuarios SET ultimo_login = NOW() WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update_login);
            $stmt_update->execute([$usuario['id']]);
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 102, 204, 0.4);
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: #0066cc;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            padding: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: brightness(1.1) contrast(1.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <img src="../assets/images/design/logo.png" alt="<?php echo SITE_NAME; ?>" class="logo-image" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-shield-lock"></i>
                </div>
            </div>
            <h3 class="mb-0">Panel de Administración</h3>
            <p class="mb-0 opacity-75"><?php echo SITE_NAME; ?></p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo esc($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo esc($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario o Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo esc($_POST['username'] ?? ''); ?>" 
                               placeholder="Ingresa tu usuario o email" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Ingresa tu contraseña" required>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Iniciar Sesión
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="bi bi-shield-check me-1"></i>
                    Acceso seguro y encriptado
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Mostrar/ocultar contraseña
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.id === 'password') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>
