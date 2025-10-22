<?php
/**
 * Script de diagnóstico para el newsletter
 */

echo "<h2>🔍 Diagnóstico del Sistema de Newsletter</h2>";
echo "<hr>";

// Información básica
echo "<h3>📊 Información del Sistema</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Servidor:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Local') . "</p>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";

echo "<hr>";

// Verificar archivos
echo "<h3>📁 Verificación de Archivos</h3>";

$files_to_check = [
    'includes/config.php',
    'includes/functions.php',
    'includes/connection.php',
    'includes/newsletter_handler.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "✅ <strong>$file</strong> - Existe<br>";
    } else {
        echo "❌ <strong>$file</strong> - NO existe<br>";
    }
}

echo "<hr>";

// Verificar configuración
echo "<h3>⚙️ Verificación de Configuración</h3>";

try {
    if (file_exists(__DIR__ . '/includes/config.php')) {
        require_once __DIR__ . '/includes/config.php';
        echo "✅ <strong>config.php</strong> - Cargado correctamente<br>";
        
        // Verificar constantes
        $constants_to_check = [
            'DB_HOST',
            'DB_NAME', 
            'DB_USER',
            'DB_PASS'
        ];
        
        foreach ($constants_to_check as $constant) {
            if (defined($constant)) {
                echo "✅ <strong>$constant</strong> - Definida<br>";
            } else {
                echo "❌ <strong>$constant</strong> - NO definida<br>";
            }
        }
    } else {
        echo "❌ <strong>config.php</strong> - No se pudo cargar<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Error al cargar config.php:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Verificar conexión a base de datos
echo "<h3>🗄️ Verificación de Base de Datos</h3>";

try {
    if (file_exists(__DIR__ . '/includes/functions.php')) {
        require_once __DIR__ . '/includes/functions.php';
        echo "✅ <strong>functions.php</strong> - Cargado correctamente<br>";
        
        // Intentar conexión
        $pdo = getDB();
        if ($pdo) {
            echo "✅ <strong>Conexión a BD</strong> - Exitosa<br>";
            
            // Verificar tablas existentes
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<br><strong>📋 Tablas existentes:</strong><br>";
            if (count($tables) > 0) {
                echo "<ul>";
                foreach ($tables as $table) {
                    echo "<li>$table</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>❌ No se encontraron tablas</p>";
            }
            
        } else {
            echo "❌ <strong>Conexión a BD</strong> - Falló<br>";
        }
    } else {
        echo "❌ <strong>functions.php</strong> - No se pudo cargar<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Scripts disponibles
echo "<h3>📋 Scripts Disponibles</h3>";
echo "<ul>";
echo "<li><a href='crear_tablas_simple.php' target='_blank'>🔨 crear_tablas_simple.php</a> - Script simple para crear tablas</li>";
echo "<li><a href='verificar_tablas_newsletter.php' target='_blank'>🔍 verificar_tablas_newsletter.php</a> - Verificar tablas</li>";
echo "<li><a href='index.php' target='_blank'>📝 index.php</a> - Página principal con formulario</li>";
echo "</ul>";

echo "<hr>";

echo "<h3>🎯 Recomendaciones</h3>";
echo "<ol>";
echo "<li><strong>Si hay errores de conexión:</strong> Verifica las credenciales de la base de datos</li>";
echo "<li><strong>Si faltan archivos:</strong> Verifica que todos los archivos estén subidos correctamente</li>";
echo "<li><strong>Si las tablas no existen:</strong> Ejecuta el script de creación de tablas</li>";
echo "</ol>";

echo "<br><hr>";
echo "<p><strong>📅 Diagnóstico completado:</strong> " . date('Y-m-d H:i:s') . "</p>";

?>
