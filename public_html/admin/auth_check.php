<?php
/**
 * ========================================
 * ADMIN - VERIFICACIÓN DE AUTENTICACIÓN
 * ========================================
 * 
 * Archivo para verificar la autenticación del administrador
 * Incluir en todas las páginas del panel de administración
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Guardar la URL actual para redirigir después del login
    if (isset($_SERVER['REQUEST_URI'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    }
    
    // Redirigir al login
    $login_url = 'login.php';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $login_url = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/login.php';
    } else {
        $login_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/login.php';
    }
    
    header('Location: ' . $login_url);
    exit;
}

// Verificar que la sesión no haya expirado (opcional - 8 horas)
$session_timeout = 8 * 60 * 60; // 8 horas en segundos
if (isset($_SESSION['admin_last_activity']) && 
    (time() - $_SESSION['admin_last_activity']) > $session_timeout) {
    
    // Limpiar sesión
    session_unset();
    session_destroy();
    
    // Redirigir al login
    header('Location: login.php?expired=1');
    exit;
}

// Actualizar tiempo de última actividad
$_SESSION['admin_last_activity'] = time();

// Función para verificar permisos de rol
function hasPermission($required_role = 'editor') {
    $user_role = $_SESSION['admin_rol'] ?? 'editor';
    
    $role_hierarchy = [
        'editor' => 1,
        'admin' => 2
    ];
    
    $user_level = $role_hierarchy[$user_role] ?? 1;
    $required_level = $role_hierarchy[$required_role] ?? 1;
    
    return $user_level >= $required_level;
}

// Función para obtener información del usuario actual
function getCurrentUser() {
    return [
        'id' => $_SESSION['admin_user_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? '',
        'nombre' => $_SESSION['admin_nombre'] ?? '',
        'rol' => $_SESSION['admin_rol'] ?? 'editor'
    ];
}

// Función para logout
function adminLogout() {
    session_unset();
    session_destroy();
    header('Location: login.php?logout=1');
    exit;
}
?>
