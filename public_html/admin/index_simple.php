<?php
/**
 * Dashboard simplificado para debugging
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir constante del sitio
define('ARAMED_SITE', true);

try {
    // Cargar configuración
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/connection.php';
    
    echo "<h1>Dashboard Admin - Versión Simple</h1>";
    echo "<p>Configuración cargada correctamente.</p>";
    
    // Verificar conexión a base de datos
    $pdo = getDB();
    if ($pdo) {
        echo "<p style='color: green;'>✓ Conexión a base de datos exitosa</p>";
        
        // Verificar si existe la tabla de usuarios admin
        $sql = "SHOW TABLES LIKE 'admin_usuarios'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            echo "<p style='color: green;'>✓ Tabla admin_usuarios existe</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Tabla admin_usuarios no existe</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Error de conexión a base de datos</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Ir al Login</a></p>";
echo "<p><a href='test.php'>Archivo de Prueba</a></p>";
?>
