<?php
/**
 * Configuración del Sistema ARAmed
 * Archivo de configuración central
 */

// Detección del entorno
$dominio = $_SERVER["SERVER_NAME"];
$ip = $_SERVER["SERVER_ADDR"] ?? '';
$isDevelopment = ($dominio == 'localhost' || $dominio == 'ideamia-dev.com' || strpos($dominio, '192.168.') === 0 || $ip == '216.18.195.84');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', $isDevelopment ? 1 : 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 3600);

// Configuración de timezone
date_default_timezone_set('America/Mexico_City');

// Configuración de caracteres
ini_set('default_charset', 'UTF-8');

// Configuración de memoria y tiempo de ejecución
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
ini_set('max_input_vars', 3000);

// Configuración de subida de archivos
ini_set('upload_max_filesize', '64M');
ini_set('post_max_size', '64M');

// Constantes del sistema
define('SITE_URL', 'http' . ($isDevelopment ? '' : 's') . '://' . $_SERVER['HTTP_HOST']);
define('SITE_PATH', __DIR__ . '/../');
define('UPLOAD_PATH', SITE_PATH . 'img/contenido/');
define('ADMIN_PATH', SITE_PATH . 'admin/');
define('IS_DEVELOPMENT', $isDevelopment);

// Configuración de base de datos según entorno
if ($isDevelopment) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ideamiadev_aramed');
    define('DB_USER', 'ideamiadev_aramed');
    define('DB_PASS', 'xS@A5Pm3EDc3BEdj');
} else {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ideamiadev_aramed');
    define('DB_USER', 'ideamiadev_aramed');
    define('DB_PASS', 'xS@A5Pm3EDc3BEdj');
}

// Configuración de seguridad
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_NAME', 'ARAMED_SESSION');

// Configuración de paginación
define('ITEMS_PER_PAGE', 12);

// Configuración de correo
define('MAIL_HOST', 'localhost');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'info@aramed.com');
define('MAIL_PASSWORD', '');
define('MAIL_FROM_NAME', 'ARAMed');

// Función para manejar errores
function handleError($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . " Error: [$errno] $errstr in $errfile on line $errline\n";
    error_log($error_message, 3, __DIR__ . '/../error_log');
    
    if (ini_get('display_errors')) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<strong>Error:</strong> $errstr<br>";
        echo "<strong>Archivo:</strong> $errfile<br>";
        echo "<strong>Línea:</strong> $errline";
        echo "</div>";
    }
    
    return true;
}

// Función para manejar excepciones
function handleException($exception) {
    $error_message = date('Y-m-d H:i:s') . " Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($error_message, 3, __DIR__ . '/../error_log');
    
    if (ini_get('display_errors')) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<strong>Excepción:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>Archivo:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Línea:</strong> " . $exception->getLine();
        echo "</div>";
    } else {
        // Redirigir a página de error 500
        header("HTTP/1.1 500 Internal Server Error");
        include_once(__DIR__ . '/../pages/500.php');
        exit;
    }
}

// Función para manejar errores fatales
function handleFatalError() {
    $error = error_get_last();
    if ($error !== null && $error['type'] === E_ERROR) {
        $error_message = date('Y-m-d H:i:s') . " Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "\n";
        error_log($error_message, 3, __DIR__ . '/../error_log');
        
        if (!ini_get('display_errors')) {
            header("HTTP/1.1 500 Internal Server Error");
            include_once(__DIR__ . '/../pages/500.php');
            exit;
        }
    }
}

// Configurar manejadores de errores
set_error_handler('handleError');
set_exception_handler('handleException');
register_shutdown_function('handleFatalError');

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Función para generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Función para verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Función para redirigir
function redirect($url) {
    header("Location: $url");
    exit;
}

// Función para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para generar slug
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Función para formatear fecha
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

// Función para formatear moneda
function formatCurrency($amount, $currency = 'MXN') {
    return '$' . number_format($amount, 2, '.', ',');
}

// Función para verificar si es móvil
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

// Función para obtener IP del cliente
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Función para registrar actividad
function logActivity($action, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " | " . getClientIP() . " | " . $action . " | " . $details . "\n";
    error_log($log_entry, 3, __DIR__ . '/../activity_log');
}

// Configuración de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Configuración de headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Configuración de caracteres
header('Content-Type: text/html; charset=UTF-8'); 