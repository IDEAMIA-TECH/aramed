<?php
/**
 * ========================================
 * DEBUG - PÁGINA DE USUARIOS
 * ========================================
 * 
 * Script de diagnóstico para la página de usuarios
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/functions.php';

echo "<h2>🔍 DEBUG - PÁGINA DE USUARIOS</h2><hr>";

try {
    // Verificar conexión a la base de datos
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }
    echo "✅ <strong>Conexión a la base de datos establecida</strong><br><br>";

    // Verificar que la tabla admin_usuarios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_usuarios'");
    if (!$stmt->fetch()) {
        throw new Exception("La tabla 'admin_usuarios' no existe.");
    }
    echo "✅ <strong>Tabla 'admin_usuarios' encontrada</strong><br><br>";

    // Verificar estructura de la tabla
    $stmt = $pdo->query("DESCRIBE admin_usuarios");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📋 ESTRUCTURA DE LA TABLA admin_usuarios</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columnas as $columna) {
        echo "<tr>";
        echo "<td>" . $columna['Field'] . "</td>";
        echo "<td>" . $columna['Type'] . "</td>";
        echo "<td>" . $columna['Null'] . "</td>";
        echo "<td>" . $columna['Key'] . "</td>";
        echo "<td>" . $columna['Default'] . "</td>";
        echo "<td>" . $columna['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";

    // Verificar datos en la tabla
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
    $total_usuarios = $stmt->fetchColumn();
    
    echo "<h3>📊 DATOS EN LA TABLA</h3>";
    echo "<strong>Total de usuarios:</strong> $total_usuarios<br><br>";

    if ($total_usuarios > 0) {
        $stmt = $pdo->query("SELECT * FROM admin_usuarios LIMIT 5");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<strong>Primeros 5 usuarios:</strong><br>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Activo</th><th>Created At</th></tr>";
        foreach ($usuarios as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . esc($usuario['nombre']) . "</td>";
            echo "<td>" . esc($usuario['email']) . "</td>";
            echo "<td>" . $usuario['rol'] . "</td>";
            echo "<td>" . ($usuario['activo'] ? 'Sí' : 'No') . "</td>";
            echo "<td>" . $usuario['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }

    // Verificar archivos de includes
    echo "<h3>📁 ARCHIVOS DE INCLUDES</h3>";
    
    $archivos_includes = [
        'includes/config.php',
        'includes/connection.php',
        'includes/functions.php',
        'admin/auth_check.php',
        'admin/includes/admin_menu.php'
    ];
    
    foreach ($archivos_includes as $archivo) {
        $ruta = __DIR__ . '/' . $archivo;
        if (file_exists($ruta)) {
            echo "✅ <strong>$archivo:</strong> Existe<br>";
        } else {
            echo "❌ <strong>$archivo:</strong> No existe<br>";
        }
    }
    echo "<br>";

    // Verificar funciones necesarias
    echo "<h3>🔧 FUNCIONES NECESARIAS</h3>";
    
    $funciones_requeridas = [
        'getDB',
        'esc',
        'isActive',
        'getNavLinkClass'
    ];
    
    foreach ($funciones_requeridas as $funcion) {
        if (function_exists($funcion)) {
            echo "✅ <strong>$funcion:</strong> Existe<br>";
        } else {
            echo "❌ <strong>$funcion:</strong> No existe<br>";
        }
    }
    echo "<br>";

    // Verificar variables de sesión
    echo "<h3>🔐 VARIABLES DE SESIÓN</h3>";
    session_start();
    
    $variables_sesion = [
        'admin_user_id',
        'admin_username',
        'admin_user_name',
        'admin_user_email'
    ];
    
    foreach ($variables_sesion as $variable) {
        if (isset($_SESSION[$variable])) {
            echo "✅ <strong>$_SESSION[$variable]:</strong> " . esc($_SESSION[$variable]) . "<br>";
        } else {
            echo "❌ <strong>$_SESSION[$variable]:</strong> No definida<br>";
        }
    }
    echo "<br>";

    // Probar la lógica de la página de usuarios
    echo "<h3>🧪 PRUEBA DE LÓGICA</h3>";
    
    // Simular la lógica de la página
    $action = $_GET['action'] ?? 'list';
    $id = $_GET['id'] ?? null;
    
    echo "<strong>Action:</strong> $action<br>";
    echo "<strong>ID:</strong> " . ($id ?: 'No definido') . "<br><br>";
    
    // Probar consulta de estadísticas
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
        $stats['total'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) as activos FROM admin_usuarios WHERE activo = 1");
        $stats['activos'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) as inactivos FROM admin_usuarios WHERE activo = 0");
        $stats['inactivos'] = $stmt->fetchColumn();
        
        echo "<strong>Estadísticas calculadas:</strong><br>";
        echo "• Total: {$stats['total']}<br>";
        echo "• Activos: {$stats['activos']}<br>";
        echo "• Inactivos: {$stats['inactivos']}<br><br>";
        
    } catch (Exception $e) {
        echo "❌ <strong>Error al calcular estadísticas:</strong> " . $e->getMessage() . "<br><br>";
    }

} catch (Exception $e) {
    echo "❌ <strong>Error durante el diagnóstico:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Este script te ayuda a diagnosticar problemas en la página de usuarios.</p>";
?>
