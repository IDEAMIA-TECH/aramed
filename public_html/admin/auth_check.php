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
    
    // Redirigir al login (usar ruta absoluta desde /admin/)
    header('Location: /admin/login.php');
    exit;
}

// Verificar que la sesión no haya expirado (opcional - 8 horas)
$session_timeout = 8 * 60 * 60; // 8 horas en segundos
if (isset($_SESSION['admin_last_activity']) && 
    (time() - $_SESSION['admin_last_activity']) > $session_timeout) {
    
    // Limpiar sesión
    session_unset();
    session_destroy();
    
    // Redirigir al login (usar ruta absoluta desde /admin/)
    header('Location: /admin/login.php?expired=1');
    exit;
}

// Actualizar tiempo de última actividad
$_SESSION['admin_last_activity'] = time();

// Cargar funciones RBAC si existen y getDB() está disponible
// IMPORTANTE: Cargar ANTES de declarar funciones locales para evitar conflictos
// Pero solo si connection.php ya fue cargado (getDB() disponible)
if (function_exists('getDB') && file_exists(__DIR__ . '/../includes/rbac_functions.php')) {
    require_once __DIR__ . '/../includes/rbac_functions.php';
}

// Verificar si el usuario debe cambiar su contraseña
// Solo si getDB() está disponible (connection.php ya cargado)
if (isset($_SESSION['admin_user_id']) && function_exists('getDB')) {
    try {
        $pdo = getDB();
        if ($pdo) {
            // Verificar si la columna existe antes de consultarla
            $columns_check = $pdo->query("SHOW COLUMNS FROM admin_usuarios LIKE 'forzar_cambio_password'")->fetch();
            if ($columns_check) {
                $sql = "SELECT forzar_cambio_password FROM admin_usuarios WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_SESSION['admin_user_id']]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario && isset($usuario['forzar_cambio_password']) && $usuario['forzar_cambio_password'] == 1) {
                    // No redirigir si ya está en la página de cambiar contraseña
                    $current_page = basename($_SERVER['PHP_SELF']);
                    if ($current_page !== 'cambiar-password.php' && $current_page !== 'logout.php') {
                        header('Location: usuarios/cambiar-password.php?forzar=1');
                        exit;
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Ignorar errores de BD en auth_check
        error_log("Error en auth_check (forzar_cambio_password): " . $e->getMessage());
    }
}

// Verificar si el usuario está bloqueado
// Solo si getDB() está disponible (connection.php ya cargado)
if (isset($_SESSION['admin_user_id']) && function_exists('getDB')) {
    try {
        $pdo = getDB();
        if ($pdo) {
            // Verificar si la columna existe antes de consultarla
            $columns_check = $pdo->query("SHOW COLUMNS FROM admin_usuarios LIKE 'bloqueado_hasta'")->fetch();
            if ($columns_check) {
                $sql = "SELECT bloqueado_hasta FROM admin_usuarios WHERE id = ? AND bloqueado_hasta IS NOT NULL AND bloqueado_hasta > NOW()";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_SESSION['admin_user_id']]);
                $bloqueado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($bloqueado) {
                    session_unset();
                    session_destroy();
                    header('Location: /admin/login.php?bloqueado=1');
                    exit;
                }
            }
        }
    } catch (Exception $e) {
        // Ignorar errores de BD en auth_check
        error_log("Error en auth_check (bloqueado_hasta): " . $e->getMessage());
    }
}

// Función para verificar permisos de rol (mantener compatibilidad)
// NOTA: hasPermission() está definida en rbac_functions.php con diferente firma
// Esta función es solo para verificación simple de roles (sin BD)
if (!function_exists('hasRolePermission')) {
    function hasRolePermission($required_role = 'editor') {
        $user_role = $_SESSION['admin_rol'] ?? 'editor';
        
        $role_hierarchy = [
            'editor' => 1,
            'admin' => 2
        ];
        
        $user_level = $role_hierarchy[$user_role] ?? 1;
        $required_level = $role_hierarchy[$required_role] ?? 1;
        
        return $user_level >= $required_level;
    }
}

// NO declarar hasPermission aquí - está en rbac_functions.php
// Si se necesita antes de cargar rbac_functions.php, usar hasRolePermission()

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
    header('Location: /admin/login.php?logout=1');
    exit;
}
?>
