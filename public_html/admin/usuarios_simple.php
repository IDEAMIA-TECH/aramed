<?php
/**
 * ========================================
 * USUARIOS SIMPLE - VERSIÓN DE PRUEBA
 * ========================================
 * 
 * Versión simplificada para identificar problemas
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir constante del sitio
define('ARAMED_SITE', true);

echo "<h1>🔍 PÁGINA DE USUARIOS - VERSIÓN SIMPLE</h1>";
echo "<p>Si ves esto, el PHP está funcionando correctamente.</p>";

try {
    // Cargar configuración paso a paso
    echo "<h2>Paso 1: Cargando configuración</h2>";
    
    require_once __DIR__ . '/../includes/config.php';
    echo "✅ Config cargado<br>";
    
    require_once __DIR__ . '/../includes/connection.php';
    echo "✅ Connection cargado<br>";
    
    require_once __DIR__ . '/../includes/functions.php';
    echo "✅ Functions cargado<br>";
    
    // Simular autenticación
    session_start();
    $_SESSION['admin_user_id'] = 1;
    $_SESSION['admin_username'] = 'admin';
    $_SESSION['admin_user_name'] = 'Administrador';
    $_SESSION['admin_user_email'] = 'admin@aramedylaboratorio.com';
    echo "✅ Variables de sesión simuladas<br>";
    
    echo "<h2>Paso 2: Conectando a la base de datos</h2>";
    
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    echo "✅ Conexión a BD establecida<br>";
    
    echo "<h2>Paso 3: Consultando datos</h2>";
    
    // Consultar usuarios
    $stmt = $pdo->query("SELECT * FROM admin_usuarios ORDER BY created_at DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Usuarios consultados: " . count($usuarios) . "<br>";
    
    // Consultar estadísticas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
    $total = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as activos FROM admin_usuarios WHERE estado = 'activo'");
    $activos = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as inactivos FROM admin_usuarios WHERE estado = 'inactivo'");
    $inactivos = $stmt->fetchColumn();
    
    echo "✅ Estadísticas calculadas<br>";
    
    echo "<h2>Paso 4: Mostrando datos</h2>";
    
    echo "<h3>Estadísticas:</h3>";
    echo "<ul>";
    echo "<li>Total usuarios: $total</li>";
    echo "<li>Usuarios activos: $activos</li>";
    echo "<li>Usuarios inactivos: $inactivos</li>";
    echo "</ul>";
    
    echo "<h3>Lista de usuarios:</h3>";
    if (empty($usuarios)) {
        echo "<p>No hay usuarios registrados.</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . esc($user['nombre']) . "</td>";
            echo "<td>" . esc($user['email']) . "</td>";
            echo "<td>" . $user['rol'] . "</td>";
            echo "<td>" . $user['estado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Paso 5: Probando menú</h2>";
    
    // Probar incluir el menú
    try {
        $current_page = 'usuarios_simple.php';
        $current_dir = 'admin';
        
        // Definir funciones necesarias
        function isActive($page, $current_page, $current_dir = null) {
            return $page === $current_page;
        }
        
        function getNavLinkClass($page, $current_page, $current_dir = null) {
            $classes = ['nav-link'];
            if (isActive($page, $current_page, $current_dir)) {
                $classes[] = 'active';
            }
            return implode(' ', $classes);
        }
        
        echo "✅ Funciones del menú definidas<br>";
        
        // Incluir el menú
        include __DIR__ . '/includes/admin_menu.php';
        echo "✅ Menú incluido<br>";
        
    } catch (Exception $e) {
        echo "❌ Error con el menú: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>✅ TODOS LOS PASOS COMPLETADOS EXITOSAMENTE</h2>";
    echo "<p>Si ves este mensaje, todos los componentes están funcionando correctamente.</p>";
    echo "<p>El problema debe estar en el archivo usuarios.php original.</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ ERROR ENCONTRADO</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Esta es una versión simplificada para diagnosticar problemas.</p>";
?>
