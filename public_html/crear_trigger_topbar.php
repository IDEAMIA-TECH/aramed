<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Crear Trigger para Topbar
 * ========================================
 * 
 * Script para crear un trigger que desactive automáticamente mensajes expirados
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/connection.php';

echo "<h2>🔧 CREANDO: Trigger para Desactivación Automática de Mensajes</h2>";

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    echo "✅ <strong>Conexión a la base de datos establecida</strong><br>";
    
    // Eliminar trigger existente si existe
    $drop_sql = "DROP TRIGGER IF EXISTS expire_topbar_messages";
    $pdo->exec($drop_sql);
    echo "✅ <strong>Trigger anterior eliminado (si existía)</strong><br>";
    
    // Crear trigger que se ejecuta cada vez que se consulta la tabla
    $trigger_sql = "
    CREATE TRIGGER expire_topbar_messages
    BEFORE SELECT ON topbar_messages
    FOR EACH ROW
    BEGIN
        -- Desactivar mensajes expirados
        UPDATE topbar_messages 
        SET status = 'inactive', updated_at = NOW() 
        WHERE status = 'active' 
        AND end_date IS NOT NULL 
        AND end_date < NOW();
    END
    ";
    
    // Nota: MySQL no soporta triggers en SELECT, así que usaremos un evento programado
    $event_sql = "
    CREATE EVENT IF NOT EXISTS expire_topbar_messages_event
    ON SCHEDULE EVERY 1 HOUR
    STARTS CURRENT_TIMESTAMP
    DO
    BEGIN
        UPDATE topbar_messages 
        SET status = 'inactive', updated_at = NOW() 
        WHERE status = 'active' 
        AND end_date IS NOT NULL 
        AND end_date < NOW();
    END
    ";
    
    $pdo->exec($event_sql);
    echo "✅ <strong>Evento programado creado (cada hora)</strong><br>";
    
    // Habilitar el planificador de eventos
    $pdo->exec("SET GLOBAL event_scheduler = ON");
    echo "✅ <strong>Planificador de eventos habilitado</strong><br>";
    
    // Verificar que el evento se creó
    $stmt = $pdo->query("SHOW EVENTS LIKE 'expire_topbar_messages_event'");
    $event = $stmt->fetch();
    
    if ($event) {
        echo "✅ <strong>Evento verificado y funcionando</strong><br>";
        echo "<br><strong>📋 Detalles del evento:</strong><br>";
        echo "- Nombre: " . $event['Name'] . "<br>";
        echo "- Estado: " . $event['Status'] . "<br>";
        echo "- Frecuencia: Cada hora<br>";
        echo "- Próxima ejecución: " . $event['Next execution time'] . "<br>";
    } else {
        echo "❌ <strong>No se pudo verificar el evento</strong><br>";
    }
    
    echo "<br><h3>🎉 ¡Trigger/Evento Creado Exitosamente!</h3>";
    echo "<p><strong>El sistema ahora desactivará automáticamente los mensajes expirados cada hora.</strong></p>";
    
    echo "<br><strong>🔧 Configuración del servidor:</strong><br>";
    echo "- <code>event_scheduler = ON</code> (ya habilitado)<br>";
    echo "- Frecuencia: Cada 1 hora<br>";
    echo "- Acción: Desactivar mensajes con end_date < NOW()<br>";
    
} catch (Exception $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<br><strong>Detalles del error:</strong><br>";
    echo "<pre>" . print_r($e, true) . "</pre>";
}

echo "<br><hr>";
echo "<p><strong>📅 Fecha de ejecución:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
