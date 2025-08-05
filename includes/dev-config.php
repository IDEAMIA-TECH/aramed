<?php
/**
 * Configuración específica para entorno de desarrollo
 * Este archivo se carga automáticamente cuando se detecta ideamia-dev.com
 */

// Configuración específica para desarrollo
define('DEV_MODE', true);
define('DEV_DOMAIN', 'ideamia-dev.com');
define('DEV_SUBDIR', '/aramed/');

// Configuración de base de datos para desarrollo
define('DEV_DB_HOST', 'localhost');
define('DEV_DB_NAME', 'ideamiadev_aramed');
define('DEV_DB_USER', 'ideamiadev_aramed');
define('DEV_DB_PASS', 'xS@A5Pm3EDc3BEdj');

// Configuración de URLs para desarrollo
define('DEV_SITE_URL', 'https://' . DEV_DOMAIN . DEV_SUBDIR);
define('DEV_ADMIN_URL', DEV_SITE_URL . 'admin/');

// Configuración de correo para desarrollo
define('DEV_MAIL_HOST', 'localhost');
define('DEV_MAIL_PORT', 587);
define('DEV_MAIL_USERNAME', 'dev@ideamia-dev.com');
define('DEV_MAIL_PASSWORD', '');
define('DEV_MAIL_FROM_NAME', 'ARAMed - Desarrollo');

// Configuración de logging para desarrollo
define('DEV_LOG_PATH', __DIR__ . '/../logs/');
define('DEV_ERROR_LOG', DEV_LOG_PATH . 'error.log');
define('DEV_ACCESS_LOG', DEV_LOG_PATH . 'access.log');
define('DEV_DEBUG_LOG', DEV_LOG_PATH . 'debug.log');

// Crear directorio de logs si no existe
if (!is_dir(DEV_LOG_PATH)) {
    mkdir(DEV_LOG_PATH, 0755, true);
}

// Función para logging específico de desarrollo
function devLog($message, $type = 'INFO') {
    $log_entry = date('Y-m-d H:i:s') . " [DEV] [$type] " . $message . "\n";
    error_log($log_entry, 3, DEV_DEBUG_LOG);
}

// Función para mostrar información de debug
function devDebug($data, $label = 'Debug') {
    if (DEV_MODE) {
        echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 10px; border-radius: 5px;'>";
        echo "<strong>$label:</strong><br>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        echo "</div>";
    }
}

// Función para verificar si estamos en desarrollo
function isDevEnvironment() {
    return ($_SERVER['HTTP_HOST'] === DEV_DOMAIN);
}

// Función para obtener la URL base correcta
function getBaseUrl() {
    if (isDevEnvironment()) {
        return DEV_SITE_URL;
    }
    return SITE_URL;
}

// Función para obtener la ruta de administración
function getAdminUrl() {
    if (isDevEnvironment()) {
        return DEV_ADMIN_URL;
    }
    return SITE_URL . 'admin/';
}

// Configuración de errores específica para desarrollo
if (isDevEnvironment()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', DEV_ERROR_LOG);
    
    // Log de inicio de sesión
    devLog('Sistema iniciado en modo desarrollo', 'SYSTEM');
}

// Función para mostrar información del entorno
function showDevInfo() {
    if (isDevEnvironment()) {
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; margin: 10px; border-radius: 5px;'>";
        echo "<strong>🔧 MODO DESARROLLO ACTIVO</strong><br>";
        echo "Dominio: " . DEV_DOMAIN . "<br>";
        echo "Base de datos: " . DEV_DB_NAME . "<br>";
        echo "Debug: Habilitado<br>";
        echo "Logs: " . DEV_LOG_PATH . "<br>";
        echo "</div>";
    }
}

// Función para limpiar logs antiguos
function cleanOldLogs($days = 7) {
    $log_files = glob(DEV_LOG_PATH . '*.log');
    $cutoff_time = time() - ($days * 24 * 60 * 60);
    
    foreach ($log_files as $file) {
        if (filemtime($file) < $cutoff_time) {
            unlink($file);
            devLog("Log antiguo eliminado: $file", 'CLEANUP');
        }
    }
}

// Función para exportar configuración de desarrollo
function exportDevConfig() {
    $config = [
        'domain' => DEV_DOMAIN,
        'database' => DEV_DB_NAME,
        'site_url' => DEV_SITE_URL,
        'admin_url' => DEV_ADMIN_URL,
        'log_path' => DEV_LOG_PATH,
        'debug_mode' => DEV_MODE
    ];
    
    return json_encode($config, JSON_PRETTY_PRINT);
}

// Función para verificar conectividad de base de datos
function checkDevDatabase() {
    try {
        $connection = mysqli_connect(DEV_DB_HOST, DEV_DB_USER, DEV_DB_PASS, DEV_DB_NAME);
        if ($connection) {
            devLog('Conexión a base de datos exitosa', 'DB');
            mysqli_close($connection);
            return true;
        } else {
            devLog('Error de conexión a base de datos: ' . mysqli_connect_error(), 'DB_ERROR');
            return false;
        }
    } catch (Exception $e) {
        devLog('Excepción en conexión a BD: ' . $e->getMessage(), 'DB_EXCEPTION');
        return false;
    }
}

// Función para mostrar estadísticas de desarrollo
function showDevStats() {
    if (isDevEnvironment()) {
        $stats = [
            'php_version' => phpversion(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'database_connected' => checkDevDatabase()
        ];
        
        echo "<div style='background: #e2e3e5; border: 1px solid #d6d8db; padding: 10px; margin: 10px; border-radius: 5px;'>";
        echo "<strong>📊 Estadísticas de Desarrollo</strong><br>";
        foreach ($stats as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'Sí' : 'No';
            } elseif (is_numeric($value)) {
                $value = number_format($value, 4);
            }
            echo ucfirst(str_replace('_', ' ', $key)) . ": $value<br>";
        }
        echo "</div>";
    }
}

// Inicialización automática para desarrollo
if (isDevEnvironment()) {
    // Limpiar logs antiguos cada 100 requests
    if (rand(1, 100) === 1) {
        cleanOldLogs();
    }
    
    // Log de acceso
    devLog('Acceso desde: ' . $_SERVER['REMOTE_ADDR'] . ' - ' . $_SERVER['REQUEST_URI'], 'ACCESS');
} 