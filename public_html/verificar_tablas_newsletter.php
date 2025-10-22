<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Verificador de Tablas Newsletter
 * ========================================
 * 
 * Script para verificar y crear las tablas del newsletter
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';

// Cargar funciones
require_once INCLUDES_PATH . '/functions.php';

// Obtener conexión a la base de datos
try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    echo "✅ Conexión a la base de datos establecida<br><br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>🔍 Verificación de Tablas del Newsletter</h2>";
echo "<hr>";

// Verificar si la tabla newsletter_subscriptions existe
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_subscriptions'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "✅ La tabla 'newsletter_subscriptions' EXISTE<br>";
        
        // Verificar estructura de la tabla
        $stmt = $pdo->query("DESCRIBE newsletter_subscriptions");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<br><strong>📋 Estructura de la tabla:</strong><br>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar registros existentes
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM newsletter_subscriptions");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<br>📊 <strong>Registros existentes:</strong> " . $count['total'] . "<br>";
        
        if ($count['total'] > 0) {
            echo "<br><strong>📝 Últimos 5 registros:</strong><br>";
            $stmt = $pdo->query("SELECT id, institucion, nombre, email_oficial, created_at FROM newsletter_subscriptions ORDER BY created_at DESC LIMIT 5");
            $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Institución</th><th>Nombre</th><th>Email</th><th>Fecha</th></tr>";
            
            foreach ($recent as $record) {
                echo "<tr>";
                echo "<td>" . $record['id'] . "</td>";
                echo "<td>" . htmlspecialchars($record['institucion']) . "</td>";
                echo "<td>" . htmlspecialchars($record['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($record['email_oficial']) . "</td>";
                echo "<td>" . $record['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "❌ La tabla 'newsletter_subscriptions' NO EXISTE<br>";
        echo "<br><strong>🔧 Creando la tabla...</strong><br>";
        
        // Crear la tabla
        $sql = "
        CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `institucion` varchar(255) NOT NULL,
          `tipo_institucion` varchar(100) NOT NULL,
          `campo_adicional` varchar(255) DEFAULT NULL,
          `estado` varchar(100) NOT NULL,
          `ciudad` varchar(150) NOT NULL,
          `nombre` varchar(255) NOT NULL,
          `puesto` varchar(150) NOT NULL,
          `email_oficial` varchar(255) NOT NULL,
          `email_alterno` varchar(255) DEFAULT NULL,
          `telefono_oficina` varchar(50) NOT NULL,
          `extension` varchar(20) DEFAULT NULL,
          `telefono_celular` varchar(50) DEFAULT NULL,
          `producto_interes` varchar(150) DEFAULT NULL,
          `fecha_compra_aprox` date DEFAULT NULL,
         ,
          `ip_address` varchar(45) DEFAULT NULL,
          `user_agent` varchar(500) DEFAULT NULL,
          `status` enum('active','inactive','unsubscribed') DEFAULT 'active',
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `email_oficial` (`email_oficial`),
          KEY `idx_status` (`status`),
          KEY `idx_estado` (`estado`),
          KEY `idx_tipo_institucion` (`tipo_institucion`),
          KEY `idx_producto_interes` (`producto_interes`),
          KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $pdo->exec($sql);
        echo "✅ Tabla 'newsletter_subscriptions' creada exitosamente<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error al verificar/crear la tabla: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<h3>🔍 Verificación de Otras Tablas</h3>";

// Verificar tabla contact_messages
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "✅ La tabla 'contact_messages' EXISTE<br>";
    } else {
        echo "❌ La tabla 'contact_messages' NO EXISTE<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al verificar contact_messages: " . $e->getMessage() . "<br>";
}

// Verificar tabla contact_quotes
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'contact_quotes'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "✅ La tabla 'contact_quotes' EXISTE<br>";
    } else {
        echo "❌ La tabla 'contact_quotes' NO EXISTE<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al verificar contact_quotes: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<h3>📊 Resumen de Base de Datos</h3>";

try {
    $stmt = $pdo->query("SELECT TABLE_NAME, TABLE_ROWS FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Tabla</th><th>Registros</th></tr>";
    
    foreach ($tables as $table) {
        echo "<tr>";
        echo "<td>" . $table['TABLE_NAME'] . "</td>";
        echo "<td>" . ($table['TABLE_ROWS'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "❌ Error al obtener resumen: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<p><strong>📝 Nota:</strong> Si las tablas no existen, ejecuta el script <code>database/setup_database_simple.sql</code> en tu base de datos.</p>";
echo "<p><strong>🔗 URL del script:</strong> <a href='database/setup_database_simple.sql' target='_blank'>setup_database_simple.sql</a></p>";

?>
