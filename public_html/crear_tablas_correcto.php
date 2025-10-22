<?php
/**
 * Script para crear tablas del newsletter con las credenciales correctas
 */

// Configuración correcta de la base de datos
$host = '173.231.22.109';
$dbname = 'aramed2025_produccion';
$username = 'aramed2025_prod';
$password = 'pmDLi&PB$zntrzJ4';

echo "<h2>🔧 Creación de Tablas del Newsletter</h2>";
echo "<hr>";

// Intentar conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong>Conexión a la base de datos establecida</strong><br>";
    echo "<p><strong>Host:</strong> $host</p>";
    echo "<p><strong>Base de datos:</strong> $dbname</p>";
    echo "<p><strong>Usuario:</strong> $username</p>";
    echo "<br>";
} catch (PDOException $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br>";
    echo "<p><strong>Configuración intentada:</strong></p>";
    echo "<ul>";
    echo "<li>Host: $host</li>";
    echo "<li>Base de datos: $dbname</li>";
    echo "<li>Usuario: $username</li>";
    echo "</ul>";
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

echo "<h3>📋 EJECUTANDO CREACIÓN DE TABLAS...</h3>";

// Crear tabla newsletter_subscriptions
echo "<strong>1. 🔨 Creando tabla newsletter_subscriptions...</strong><br>";
try {
    $pdo->exec($sql_newsletter);
    echo "✅ <strong>Tabla 'newsletter_subscriptions' creada exitosamente</strong><br><br>";
} catch (PDOException $e) {
    echo "❌ <strong>Error al crear newsletter_subscriptions:</strong> " . $e->getMessage() . "<br><br>";
}

// Crear tabla contact_messages
echo "<strong>2. 🔨 Creando tabla contact_messages...</strong><br>";
try {
    $pdo->exec($sql_contact);
    echo "✅ <strong>Tabla 'contact_messages' creada exitosamente</strong><br><br>";
} catch (PDOException $e) {
    echo "❌ <strong>Error al crear contact_messages:</strong> " . $e->getMessage() . "<br><br>";
}

// Crear tabla contact_quotes
echo "<strong>3. 🔨 Creando tabla contact_quotes...</strong><br>";
try {
    $pdo->exec($sql_quotes);
    echo "✅ <strong>Tabla 'contact_quotes' creada exitosamente</strong><br><br>";
} catch (PDOException $e) {
    echo "❌ <strong>Error al crear contact_quotes:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<hr>";

// Verificar que las tablas se crearon correctamente
echo "<h3>🔍 VERIFICACIÓN FINAL</h3>";

$tables_to_check = ['newsletter_subscriptions', 'contact_messages', 'contact_quotes'];
$all_created = true;

foreach ($tables_to_check as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->fetch();
        
        if ($exists) {
            echo "✅ <strong>Tabla '$table' existe</strong><br>";
        } else {
            echo "❌ <strong>Tabla '$table' NO existe</strong><br>";
            $all_created = false;
        }
    } catch (PDOException $e) {
        echo "❌ <strong>Error al verificar tabla '$table':</strong> " . $e->getMessage() . "<br>";
        $all_created = false;
    }
}

echo "<br><hr>";

// Mostrar todas las tablas
echo "<h3>📊 LISTA DE TODAS LAS TABLAS EN LA BASE DE DATOS</h3>";

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>#</th><th>Nombre de la Tabla</th></tr>";
        
        foreach ($tables as $index => $table) {
            echo "<tr>";
            echo "<td>" . ($index + 1) . "</td>";
            echo "<td><strong>$table</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ <strong>No se encontraron tablas en la base de datos</strong><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>Error al listar tablas:</strong> " . $e->getMessage() . "<br>";
}

echo "<br><hr>";

if ($all_created) {
    echo "<h3>🎉 ¡PROCESO COMPLETADO EXITOSAMENTE!</h3>";
    echo "<p><strong>✅ Todas las tablas del newsletter han sido creadas correctamente.</strong></p>";
    echo "<p><strong>🚀 Ahora puedes:</strong></p>";
    echo "<ul>";
    echo "<li><a href='index.php' target='_blank'>📝 Probar el formulario de newsletter</a></li>";
    echo "<li><a href='verificar_tablas_newsletter.php' target='_blank'>🔍 Verificar las tablas creadas</a></li>";
    echo "</ul>";
    
    echo "<br><p><strong>🎯 Prueba el formulario:</strong></p>";
    echo "<p>Ve a la página principal y llena el formulario de newsletter para verificar que funciona correctamente.</p>";
    
} else {
    echo "<h3>⚠️ PROCESO COMPLETADO CON ERRORES</h3>";
    echo "<p><strong>❌ Algunas tablas no se pudieron crear correctamente.</strong></p>";
    echo "<p><strong>🔧 Recomendación:</strong> Revisa los errores anteriores y ejecuta el script nuevamente.</p>";
}

echo "<br><hr>";
echo "<p><strong>📅 Fecha de ejecución:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>🌐 Servidor:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Local') . "</p>";

?>
