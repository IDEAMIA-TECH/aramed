<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Crear Tablas Newsletter
 * ========================================
 * 
 * Script para crear las tablas del newsletter en la base de datos
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

echo "<h2>🔧 Creación de Tablas del Newsletter</h2>";
echo "<hr>";

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

// SQL para crear la tabla newsletter_subscriptions
$sql_newsletter = "
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
  `observaciones` text DEFAULT NULL,
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

// SQL para crear la tabla contact_messages
$sql_contact = "
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `institucion` varchar(255) DEFAULT NULL,
  `asunto` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `status` enum('nuevo','en_proceso','respondido','cerrado') DEFAULT 'nuevo',
  `assigned_to` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_created_at` (`created_at`),
  FULLTEXT KEY `ft_mensaje` (`mensaje`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// SQL para crear la tabla contact_quotes
$sql_quotes = "
CREATE TABLE IF NOT EXISTS `contact_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `institucion` varchar(255) NOT NULL,
  `tipo_institucion` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `ciudad` varchar(150) NOT NULL,
  `productos` text NOT NULL,
  `presupuesto_estimado` varchar(100) DEFAULT NULL,
  `fecha_requerida` date DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `status` enum('nueva','revisada','cotizada','enviada','cerrada') DEFAULT 'nueva',
  `cotizacion_enviada` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Crear tabla newsletter_subscriptions
echo "<strong>1. Creando tabla newsletter_subscriptions...</strong><br>";
try {
    $pdo->exec($sql_newsletter);
    echo "✅ Tabla 'newsletter_subscriptions' creada exitosamente<br><br>";
} catch (Exception $e) {
    echo "❌ Error al crear newsletter_subscriptions: " . $e->getMessage() . "<br><br>";
}

// Crear tabla contact_messages
echo "<strong>2. Creando tabla contact_messages...</strong><br>";
try {
    $pdo->exec($sql_contact);
    echo "✅ Tabla 'contact_messages' creada exitosamente<br><br>";
} catch (Exception $e) {
    echo "❌ Error al crear contact_messages: " . $e->getMessage() . "<br><br>";
}

// Crear tabla contact_quotes
echo "<strong>3. Creando tabla contact_quotes...</strong><br>";
try {
    $pdo->exec($sql_quotes);
    echo "✅ Tabla 'contact_quotes' creada exitosamente<br><br>";
} catch (Exception $e) {
    echo "❌ Error al crear contact_quotes: " . $e->getMessage() . "<br><br>";
}

echo "<hr>";

// Verificar que las tablas se crearon correctamente
echo "<h3>🔍 Verificación Final</h3>";

$tables_to_check = ['newsletter_subscriptions', 'contact_messages', 'contact_quotes'];

foreach ($tables_to_check as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->fetch();
        
        if ($exists) {
            echo "✅ Tabla '$table' existe<br>";
        } else {
            echo "❌ Tabla '$table' NO existe<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error al verificar tabla '$table': " . $e->getMessage() . "<br>";
    }
}

echo "<br><hr>";
echo "<h3>📊 Lista de Todas las Tablas</h3>";

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ Error al listar tablas: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<p><strong>✅ Proceso completado.</strong> Las tablas del newsletter han sido creadas.</p>";
echo "<p><strong>🔗 Ahora puedes:</strong></p>";
echo "<ul>";
echo "<li><a href='verificar_tablas_newsletter.php'>Verificar las tablas</a></li>";
echo "<li><a href='index.php'>Probar el formulario de newsletter</a></li>";
echo "</ul>";

?>
