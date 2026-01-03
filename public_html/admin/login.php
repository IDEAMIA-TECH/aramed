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
    error_log("=== LOGIN INTENTO ===");
    error_log("Username recibido: " . ($_POST['username'] ?? 'NO DEFINIDO'));
    
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    error_log("Username sanitizado: " . $username);
    error_log("Password recibido: " . (empty($password) ? 'VACÍO' : 'PRESENTE'));
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor completa todos los campos';
        error_log("ERROR: Campos vacíos");
    } else {
        // Buscar usuario
        error_log("Buscando usuario en BD...");
        $sql = "SELECT * FROM admin_usuarios WHERE (username = ? OR email = ?) AND estado = 'activo'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Usuario encontrado: " . ($usuario ? 'SÍ (ID: ' . $usuario['id'] . ')' : 'NO'));
        if ($usuario) {
            error_log("Username BD: " . $usuario['username']);
            error_log("Email BD: " . $usuario['email']);
            error_log("Estado: " . $usuario['estado']);
            error_log("Password hash presente: " . (isset($usuario['password_hash']) ? 'SÍ' : 'NO'));
        }
        
        if ($usuario) {
            // Verificar si el usuario está bloqueado (si el campo existe)
            $bloqueado = false;
            if (isset($usuario['bloqueado_hasta']) && $usuario['bloqueado_hasta'] && strtotime($usuario['bloqueado_hasta']) > time()) {
                $bloqueado = true;
                $error = 'Usuario bloqueado temporalmente. Intente más tarde.';
            }
            
            if (!$bloqueado) {
                // Verificar contraseña
                error_log("Verificando contraseña...");
                $password_ok = password_verify($password, $usuario['password_hash']);
                error_log("Password verify resultado: " . ($password_ok ? 'CORRECTO' : 'INCORRECTO'));
                
                if ($password_ok) {
                    // Login exitoso - Actualizar último login
                    try {
                        // Intentar actualizar campos de seguridad si existen
                        $sql_reset = "UPDATE admin_usuarios SET ultimo_login = NOW()";
                        $params = [];
                        
                        // Verificar si los campos de seguridad existen
                        $columns_check = $pdo->query("SHOW COLUMNS FROM admin_usuarios LIKE 'intentos_fallidos'")->fetch();
                        if ($columns_check) {
                            $sql_reset .= ", intentos_fallidos = 0, bloqueado_hasta = NULL";
                        }
                        
                        $sql_reset .= " WHERE id = ?";
                        $params[] = $usuario['id'];
                        
                        $stmt_reset = $pdo->prepare($sql_reset);
                        $stmt_reset->execute($params);
                    } catch (PDOException $e) {
                        // Si falla, intentar solo con ultimo_login
                        try {
                            $sql_simple = "UPDATE admin_usuarios SET ultimo_login = NOW() WHERE id = ?";
                            $stmt_simple = $pdo->prepare($sql_simple);
                            $stmt_simple->execute([$usuario['id']]);
                        } catch (PDOException $e2) {
                            // Ignorar error de actualización, continuar con login
                            error_log("Error actualizando último login: " . $e2->getMessage());
                        }
                    }
                    
                    // Iniciar sesión
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user_id'] = $usuario['id'];
                    $_SESSION['admin_username'] = $usuario['username'];
                    $_SESSION['admin_nombre'] = $usuario['nombre'];
                    $_SESSION['admin_rol'] = $usuario['rol'];
                    
                    // Registrar actividad
                    if (function_exists('logActivity')) {
                        try {
                            logActivity($usuario['id'], 'login', null, null, null, [
                                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
                            ]);
                        } catch (Exception $e) {
                            // Ignorar error de logging, continuar con login
                            error_log("Error en logActivity: " . $e->getMessage());
                        }
                    }
                    
                    // Regenerar session ID por seguridad
                    session_regenerate_id(true);
                    
                    header('Location: index.php');
                    exit;
                } else {
                    // Contraseña incorrecta
                    // Intentar manejar intentos fallidos si los campos existen
                    try {
                        $columns_check = $pdo->query("SHOW COLUMNS FROM admin_usuarios LIKE 'intentos_fallidos'")->fetch();
                        if ($columns_check) {
                            // Los campos existen, usar lógica de bloqueo
                            $intentos_fallidos = (isset($usuario['intentos_fallidos']) ? (int)$usuario['intentos_fallidos'] : 0) + 1;
                            $max_intentos = 5;
                            $bloqueo_minutos = 30;
                            
                            if ($intentos_fallidos >= $max_intentos) {
                                // Bloquear usuario por 30 minutos
                                $bloqueado_hasta = date('Y-m-d H:i:s', time() + ($bloqueo_minutos * 60));
                                $sql_bloqueo = "UPDATE admin_usuarios SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id = ?";
                                $stmt_bloqueo = $pdo->prepare($sql_bloqueo);
                                $stmt_bloqueo->execute([$intentos_fallidos, $bloqueado_hasta, $usuario['id']]);
                                
                                $error = "Usuario bloqueado temporalmente por {$bloqueo_minutos} minutos debido a múltiples intentos fallidos.";
                            } else {
                                // Solo incrementar contador
                                $sql_intentos = "UPDATE admin_usuarios SET intentos_fallidos = ? WHERE id = ?";
                                $stmt_intentos = $pdo->prepare($sql_intentos);
                                $stmt_intentos->execute([$intentos_fallidos, $usuario['id']]);
                                
                                $intentos_restantes = $max_intentos - $intentos_fallidos;
                                $error = "Usuario o contraseña incorrectos. Intentos restantes: {$intentos_restantes}";
                            }
                        } else {
                            // Los campos no existen, solo mostrar error simple
                            $error = 'Usuario o contraseña incorrectos';
                        }
                    } catch (PDOException $e) {
                        // Si hay error, mostrar mensaje simple
                        error_log("Error en manejo de intentos fallidos: " . $e->getMessage());
                        $error = 'Usuario o contraseña incorrectos';
                    }
                }
            }
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
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/design/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/design/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/design/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/design/favicon-32x32.png">
    
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
            
            <div class="text-center mt-3">
                <a href="recuperar-password.php" class="text-muted text-decoration-none">
                    <i class="bi bi-question-circle me-1"></i>¿Olvidaste tu contraseña?
                </a>
            </div>
            
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
