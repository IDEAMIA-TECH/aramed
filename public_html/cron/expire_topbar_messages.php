<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Cron Job para Expirar Mensajes Topbar
 * ========================================
 * 
 * Script que se ejecuta periódicamente para desactivar mensajes expirados
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Solo permitir ejecución desde línea de comandos o cron
if (php_sapi_name() !== 'cli' && !isset($_GET['cron_key'])) {
    die('Acceso no permitido');
}

// Verificar clave de cron (opcional, para seguridad)
$cron_key = 'aramed_topbar_cron_2025';
if (isset($_GET['cron_key']) && $_GET['cron_key'] !== $cron_key) {
    die('Clave de cron inválida');
}

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';

// Log de ejecución
$log_file = __DIR__ . '/../logs/topbar_cron.log';
$log_message = "[" . date('Y-m-d H:i:s') . "] Iniciando verificación de mensajes expirados\n";

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    // Buscar mensajes que han expirado
    $sql = "SELECT id, text, end_date FROM topbar_messages 
            WHERE status = 'active' 
            AND end_date IS NOT NULL 
            AND end_date < NOW()";
    
    $stmt = $pdo->query($sql);
    $expired_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($expired_messages)) {
        $log_message .= "No hay mensajes expirados\n";
    } else {
        // Desactivar mensajes expirados
        $update_sql = "UPDATE topbar_messages 
                       SET status = 'inactive', updated_at = NOW() 
                       WHERE status = 'active' 
                       AND end_date IS NOT NULL 
                       AND end_date < NOW()";
        
        $stmt = $pdo->prepare($update_sql);
        $result = $stmt->execute();
        $affected_rows = $stmt->rowCount();
        
        $log_message .= "Mensajes desactivados: $affected_rows\n";
        
        foreach ($expired_messages as $message) {
            $log_message .= "- ID {$message['id']}: {$message['text']} (expiró: {$message['end_date']})\n";
        }
    }
    
    // Verificar mensajes que están por expirar (próximas 24 horas)
    $sql = "SELECT id, text, end_date FROM topbar_messages 
            WHERE status = 'active' 
            AND end_date IS NOT NULL 
            AND end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)";
    
    $stmt = $pdo->query($sql);
    $expiring_soon = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($expiring_soon)) {
        $log_message .= "Mensajes que expiran en 24 horas: " . count($expiring_soon) . "\n";
        foreach ($expiring_soon as $message) {
            $log_message .= "- ID {$message['id']}: {$message['text']} (expira: {$message['end_date']})\n";
        }
    }
    
    $log_message .= "Verificación completada exitosamente\n";
    
} catch (Exception $e) {
    $log_message .= "ERROR: " . $e->getMessage() . "\n";
}

// Escribir al log
file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);

// Si se ejecuta desde web, mostrar resultado
if (php_sapi_name() !== 'cli') {
    echo "<h2>🕒 Cron Job - Verificación de Mensajes Expirados</h2>";
    echo "<pre>" . htmlspecialchars($log_message) . "</pre>";
    echo "<p><strong>Log guardado en:</strong> " . $log_file . "</p>";
}
?>
