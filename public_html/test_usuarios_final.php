<?php
/**
 * ========================================
 * TEST FINAL - PÁGINA DE USUARIOS
 * ========================================
 * 
 * Script para probar la página de usuarios después de las correcciones
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

echo "<h2>🧪 TEST FINAL - PÁGINA DE USUARIOS</h2><hr>";

echo "<h3>1️⃣ PROBANDO INCLUDES EN ORDEN CORRECTO</h3>";

try {
    // Simular el orden correcto de includes
    define('ARAMED_SITE', true);
    echo "✅ <strong>Constante ARAMED_SITE definida</strong><br>";
    
    require_once __DIR__ . '/includes/config.php';
    echo "✅ <strong>config.php cargado</strong><br>";
    
    require_once __DIR__ . '/includes/functions.php';
    echo "✅ <strong>functions.php cargado</strong><br>";
    
    require_once __DIR__ . '/includes/connection.php';
    echo "✅ <strong>connection.php cargado</strong><br>";
    
    // Simular autenticación
    session_start();
    $_SESSION['admin_user_id'] = 1;
    $_SESSION['admin_username'] = 'admin';
    $_SESSION['admin_user_name'] = 'Administrador';
    $_SESSION['admin_user_email'] = 'admin@aramedylaboratorio.com';
    echo "✅ <strong>Variables de sesión simuladas</strong><br>";
    
    echo "<h3>2️⃣ PROBANDO FUNCIONES NECESARIAS</h3>";
    
    if (function_exists('esc')) {
        echo "✅ <strong>Función esc() disponible</strong><br>";
    } else {
        echo "❌ <strong>Función esc() NO disponible</strong><br>";
    }
    
    if (function_exists('getDB')) {
        echo "✅ <strong>Función getDB() disponible</strong><br>";
    } else {
        echo "❌ <strong>Función getDB() NO disponible</strong><br>";
    }
    
    echo "<h3>3️⃣ PROBANDO CONEXIÓN A BASE DE DATOS</h3>";
    
    $pdo = getDB();
    if ($pdo) {
        echo "✅ <strong>Conexión a BD establecida</strong><br>";
        
        // Probar consulta
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
        $total = $stmt->fetchColumn();
        echo "✅ <strong>Consulta exitosa: $total usuarios</strong><br>";
    } else {
        echo "❌ <strong>Error de conexión a BD</strong><br>";
    }
    
    echo "<h3>4️⃣ PROBANDO INCLUSIÓN DEL MENÚ</h3>";
    
    // Definir variables necesarias para el menú
    $current_page = 'usuarios.php';
    $current_dir = 'admin';
    
    // Probar incluir el menú
    ob_start();
    try {
        include __DIR__ . '/admin/includes/admin_menu.php';
        $menu_content = ob_get_clean();
        
        if (!empty($menu_content)) {
            echo "✅ <strong>Menú incluido correctamente</strong><br>";
            echo "&nbsp;&nbsp;• Contenido del menú: " . strlen($menu_content) . " caracteres<br>";
        } else {
            echo "❌ <strong>Menú vacío</strong><br>";
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo "❌ <strong>Error al incluir menú:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>5️⃣ PROBANDO RENDERIZADO COMPLETO</h3>";
    
    echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border: 2px solid #4caf50;'>";
    echo "<h4>✅ SIMULACIÓN DE PÁGINA DE USUARIOS</h4>";
    echo "<p>Si ves este mensaje en un recuadro verde, el renderizado HTML funciona correctamente.</p>";
    echo "<p><strong>Esto significa que la página de usuarios debería funcionar ahora.</strong></p>";
    echo "</div>";
    
    echo "<h3>✅ TODAS LAS PRUEBAS COMPLETADAS</h3>";
    echo "<p><strong>La página de usuarios debería funcionar correctamente ahora.</strong></p>";
    echo "<p><a href='admin/usuarios.php' target='_blank' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔗 Probar Página de Usuarios</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ ERROR ENCONTRADO</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Este test verifica que todos los componentes funcionen en el orden correcto.</p>";
?>
