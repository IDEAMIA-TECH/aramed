<?php
/**
 * Ejemplo de implementación de seguridad moderna para ARAmed
 * Reemplaza el sistema de autenticación actual
 */

// Configuración de seguridad
class SecurityConfig {
    const HASH_ALGO = PASSWORD_ARGON2ID;
    const SESSION_LIFETIME = 3600; // 1 hora
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15 minutos
    
    public static function getConfig() {
        return [
            'hash_algo' => self::HASH_ALGO,
            'session_lifetime' => self::SESSION_LIFETIME,
            'max_login_attempts' => self::MAX_LOGIN_ATTEMPTS,
            'lockout_time' => self::LOCKOUT_TIME,
            'csrf_token_name' => 'csrf_token',
            'jwt_secret' => $_ENV['JWT_SECRET'] ?? 'your-secret-key',
            'jwt_expiry' => 3600
        ];
    }
}

// Clase para manejo de contraseñas
class PasswordManager {
    
    public static function hashPassword($password) {
        return password_hash($password, SecurityConfig::HASH_ALGO, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, SecurityConfig::HASH_ALGO);
    }
}

// Clase para manejo de sesiones seguras
class SecureSession {
    
    public static function start() {
        // Configuración segura de sesiones
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        session_start();
        
        // Regenerar ID de sesión periódicamente
        if (!isset($_SESSION['last_regeneration'])) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    public static function createUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_level'] = $user['nivel'];
        $_SESSION['user_name'] = $user['user'];
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
    
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && 
               $_SESSION['ip_address'] === $_SERVER['REMOTE_ADDR'] &&
               $_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT'];
    }
    
    public static function logout() {
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

// Clase para protección CSRF
class CSRFProtection {
    
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function verifyToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function getTokenField() {
        return '<input type="hidden" name="csrf_token" value="' . 
               self::generateToken() . '">';
    }
}

// Clase para rate limiting
class RateLimiter {
    
    public static function checkLoginAttempts($username) {
        $attempts_file = sys_get_temp_dir() . '/login_attempts.json';
        $attempts = [];
        
        if (file_exists($attempts_file)) {
            $attempts = json_decode(file_get_contents($attempts_file), true);
        }
        
        // Limpiar intentos antiguos
        $attempts = array_filter($attempts, function($attempt) {
            return time() - $attempt['time'] < SecurityConfig::LOCKOUT_TIME;
        });
        
        // Contar intentos para este usuario
        $user_attempts = array_filter($attempts, function($attempt) use ($username) {
            return $attempt['username'] === $username;
        });
        
        if (count($user_attempts) >= SecurityConfig::MAX_LOGIN_ATTEMPTS) {
            return false; // Usuario bloqueado
        }
        
        return true;
    }
    
    public static function recordLoginAttempt($username, $success = false) {
        $attempts_file = sys_get_temp_dir() . '/login_attempts.json';
        $attempts = [];
        
        if (file_exists($attempts_file)) {
            $attempts = json_decode(file_get_contents($attempts_file), true);
        }
        
        $attempts[] = [
            'username' => $username,
            'time' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'success' => $success
        ];
        
        file_put_contents($attempts_file, json_encode($attempts));
    }
}

// Clase para validación de entrada
class InputValidator {
    
    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }
    
    public static function validatePassword($password) {
        // Mínimo 8 caracteres, al menos una letra y un número
        return strlen($password) >= 8 && 
               preg_match('/[a-zA-Z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
}

// Clase principal de autenticación
class AuthManager {
    
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function login($username, $password) {
        // Validar entrada
        if (!InputValidator::validateUsername($username)) {
            return ['success' => false, 'message' => 'Usuario inválido'];
        }
        
        // Verificar rate limiting
        if (!RateLimiter::checkLoginAttempts($username)) {
            return ['success' => false, 'message' => 'Demasiados intentos. Intente más tarde.'];
        }
        
        // Buscar usuario en la base de datos
        $stmt = $this->db->prepare("SELECT * FROM user WHERE user = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            RateLimiter::recordLoginAttempt($username, false);
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }
        
        // Verificar contraseña
        if (PasswordManager::verifyPassword($password, $user['pass'])) {
            // Verificar si necesita rehash
            if (PasswordManager::needsRehash($user['pass'])) {
                $new_hash = PasswordManager::hashPassword($password);
                $update_stmt = $this->db->prepare("UPDATE user SET pass = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_hash, $user['id']);
                $update_stmt->execute();
            }
            
            // Crear sesión
            SecureSession::createUserSession($user);
            RateLimiter::recordLoginAttempt($username, true);
            
            // Log de auditoría
            $this->logAudit($user['id'], 'login', 'Login exitoso');
            
            return ['success' => true, 'user' => $user];
        } else {
            RateLimiter::recordLoginAttempt($username, false);
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }
    }
    
    public function logout() {
        if (SecureSession::isAuthenticated()) {
            $this->logAudit($_SESSION['user_id'], 'logout', 'Logout');
        }
        SecureSession::logout();
    }
    
    private function logAudit($user_id, $action, $description) {
        $stmt = $this->db->prepare("INSERT INTO audit_log (user_id, action, description, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $stmt->bind_param("issss", $user_id, $action, $description, $ip, $user_agent);
        $stmt->execute();
    }
}

// Ejemplo de uso en el sistema actual
class ModernLogin {
    
    private $auth;
    
    public function __construct($database) {
        $this->auth = new AuthManager($database);
    }
    
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar CSRF token
            if (!CSRFProtection::verifyToken($_POST['csrf_token'] ?? '')) {
                return ['success' => false, 'message' => 'Token CSRF inválido'];
            }
            
            $username = InputValidator::sanitizeString($_POST['user'] ?? '');
            $password = $_POST['pass'] ?? '';
            
            return $this->auth->login($username, $password);
        }
        
        return ['success' => false, 'message' => 'Método no permitido'];
    }
}

// Ejemplo de formulario de login moderno
function renderModernLoginForm() {
    $csrf_token = CSRFProtection::getTokenField();
    
    return <<<HTML
    <form action="login.php" method="post" class="login-form">
        {$csrf_token}
        
        <div class="form-group">
            <label for="username">Usuario</label>
            <input type="text" id="username" name="user" required 
                   pattern="[a-zA-Z0-9_]{3,20}" 
                   title="Usuario debe tener entre 3 y 20 caracteres, solo letras, números y guiones bajos">
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="pass" required 
                   minlength="8" 
                   title="Contraseña debe tener al menos 8 caracteres">
        </div>
        
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
    
    <script>
    // Validación en tiempo real
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const hasLetter = /[a-zA-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const isLongEnough = password.length >= 8;
        
        if (hasLetter && hasNumber && isLongEnough) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity('La contraseña debe tener al menos 8 caracteres, una letra y un número');
        }
    });
    </script>
HTML;
}

// Ejemplo de middleware de autenticación
function requireAuth() {
    SecureSession::start();
    
    if (!SecureSession::isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
    
    // Verificar si la sesión ha expirado
    if (time() - $_SESSION['login_time'] > SecurityConfig::SESSION_LIFETIME) {
        SecureSession::logout();
        header('Location: login.php?expired=1');
        exit;
    }
}

// Ejemplo de uso en el sistema actual
if (basename($_SERVER['PHP_SELF']) === 'login.php') {
    SecureSession::start();
    
    $login = new ModernLogin($CONEXION);
    $result = $login->handleLogin();
    
    if ($result['success']) {
        header('Location: index.php');
        exit;
    } else {
        $error_message = $result['message'];
    }
}
?> 