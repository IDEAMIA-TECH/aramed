<?php
/**
 * Script para crear tabla del boletín informativo simple
 */

// Configuración correcta de la base de datos
$host = '173.231.22.109';
$dbname = 'aramed2025_produccion';
$username = 'aramed2025_prod';
$password = 'pmDLi&PB$zntrzJ4';

echo "<h2>🔧 Creación de Tabla del Boletín Informativo</h2>";
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
    exit;
}

// SQL para crear la tabla newsletter_simple (boletín informativo)
$sql_boletin = "
CREATE TABLE IF NOT EXISTS `newsletter_simple` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `status` enum('active','unsubscribed','bounced') DEFAULT 'active',
  `source` varchar(100) DEFAULT 'boletin' COMMENT 'boletin, footer, popup, etc.',
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_source` (`source`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

echo "<h3>📋 CREANDO TABLA PARA BOLETÍN INFORMATIVO...</h3>";

// Crear tabla newsletter_simple
echo "<strong>🔨 Creando tabla newsletter_simple...</strong><br>";
try {
    $pdo->exec($sql_boletin);
    echo "✅ <strong>Tabla 'newsletter_simple' creada exitosamente</strong><br><br>";
} catch (PDOException $e) {
    echo "❌ <strong>Error al crear newsletter_simple:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<hr>";

// Verificar que la tabla se creó correctamente
echo "<h3>🔍 VERIFICACIÓN</h3>";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_simple'");
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "✅ <strong>Tabla 'newsletter_simple' existe</strong><br>";
        
        // Mostrar estructura de la tabla
        $stmt = $pdo->query("DESCRIBE newsletter_simple");
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
        
    } else {
        echo "❌ <strong>Tabla 'newsletter_simple' NO existe</strong><br>";
    }
} catch (PDOException $e) {
    echo "❌ <strong>Error al verificar tabla:</strong> " . $e->getMessage() . "<br>";
}

echo "<br><hr>";

// Mostrar todas las tablas relacionadas con newsletter
echo "<h3>📊 TABLAS DE NEWSLETTER EN LA BASE DE DATOS</h3>";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE '%newsletter%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>#</th><th>Nombre de la Tabla</th><th>Propósito</th></tr>";
        
        $table_purposes = [
            'newsletter_subscriptions' => 'Formulario completo "Mantente Informado"',
            'newsletter_simple' => 'Suscripción simple al boletín informativo'
        ];
        
        foreach ($tables as $index => $table) {
            echo "<tr>";
            echo "<td>" . ($index + 1) . "</td>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>" . ($table_purposes[$table] ?? 'No especificado') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ <strong>No se encontraron tablas de newsletter</strong><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>Error al listar tablas:</strong> " . $e->getMessage() . "<br>";
}

echo "<br><hr>";

echo "<h3>🎯 DIFERENCIAS ENTRE LAS TABLAS</h3>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Tabla</th><th>Campos</th><th>Uso</th></tr>";
echo "<tr>";
echo "<td><strong>newsletter_subscriptions</strong></td>";
echo "<td>Institución, tipo, estado, ciudad, nombre, puesto, emails, teléfonos, producto interés, etc.</td>";
echo "<td>Formulario completo 'Mantente Informado'</td>";
echo "</tr>";
echo "<tr>";
echo "<td><strong>newsletter_simple</strong></td>";
echo "<td>Email, nombre (opcional), IP, user agent, status, source</td>";
echo "<td>Suscripción simple al boletín informativo</td>";
echo "</tr>";
echo "</table>";

echo "<br><hr>";

echo "<h3>✅ PROCESO COMPLETADO</h3>";
echo "<p><strong>🎉 La tabla para el boletín informativo ha sido creada correctamente.</strong></p>";
echo "<p><strong>📋 Ahora tienes:</strong></p>";
echo "<ul>";
echo "<li>✅ <strong>newsletter_subscriptions</strong> - Para el formulario completo 'Mantente Informado'</li>";
echo "<li>✅ <strong>newsletter_simple</strong> - Para la suscripción simple al boletín</li>";
echo "</ul>";

echo "<br><p><strong>🚀 Próximos pasos:</strong></p>";
echo "<ol>";
echo "<li>Crear un handler PHP para la suscripción simple al boletín</li>";
echo "<li>Implementar el formulario simple en el footer o sidebar</li>";
echo "<li>Probar ambas funcionalidades</li>";
echo "</ol>";

echo "<br><hr>";
echo "<p><strong>📅 Fecha de ejecución:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>🌐 Servidor:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Local') . "</p>";

?>
