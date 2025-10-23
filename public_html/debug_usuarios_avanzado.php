<?php
/**
 * ========================================
 * DEBUG AVANZADO - PÁGINA DE USUARIOS
 * ========================================
 * 
 * Script de diagnóstico detallado para identificar problemas
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 DEBUG AVANZADO - PÁGINA DE USUARIOS</h2><hr>";

// Paso 1: Verificar constantes y includes
echo "<h3>1️⃣ VERIFICANDO CONSTANTES E INCLUDES</h3>";

try {
    define('ARAMED_SITE', true);
    echo "✅ <strong>Constante ARAMED_SITE definida</strong><br>";
} catch (Exception $e) {
    echo "❌ <strong>Error definiendo constante:</strong> " . $e->getMessage() . "<br>";
}

// Verificar archivos de includes
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
        
        // Intentar incluir el archivo
        try {
            if ($archivo === 'admin/auth_check.php') {
                // Simular autenticación para el debug
                session_start();
                $_SESSION['admin_user_id'] = 1;
                $_SESSION['admin_username'] = 'admin';
                $_SESSION['admin_user_name'] = 'Administrador';
                $_SESSION['admin_user_email'] = 'admin@aramedylaboratorio.com';
                echo "&nbsp;&nbsp;🔐 <strong>Variables de sesión simuladas</strong><br>";
            } else {
                require_once $ruta;
                echo "&nbsp;&nbsp;✅ <strong>Incluido correctamente</strong><br>";
            }
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;❌ <strong>Error al incluir:</strong> " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ <strong>$archivo:</strong> No existe<br>";
    }
}

echo "<br>";

// Paso 2: Verificar conexión a la base de datos
echo "<h3>2️⃣ VERIFICANDO CONEXIÓN A BASE DE DATOS</h3>";

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("getDB() retornó null");
    }
    echo "✅ <strong>Conexión a la base de datos establecida</strong><br>";
    
    // Probar consulta simple
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    if ($result['test'] == 1) {
        echo "✅ <strong>Consulta de prueba exitosa</strong><br>";
    } else {
        echo "❌ <strong>Consulta de prueba falló</strong><br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br>";
}

echo "<br>";

// Paso 3: Verificar tabla admin_usuarios
echo "<h3>3️⃣ VERIFICANDO TABLA admin_usuarios</h3>";

try {
    $pdo = getDB();
    
    // Verificar que la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_usuarios'");
    if (!$stmt->fetch()) {
        throw new Exception("Tabla admin_usuarios no existe");
    }
    echo "✅ <strong>Tabla admin_usuarios existe</strong><br>";
    
    // Verificar estructura
    $stmt = $pdo->query("DESCRIBE admin_usuarios");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ <strong>Estructura de tabla obtenida (" . count($columnas) . " columnas)</strong><br>";
    
    // Verificar datos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
    $total = $stmt->fetchColumn();
    echo "✅ <strong>Total de usuarios: $total</strong><br>";
    
    if ($total > 0) {
        $stmt = $pdo->query("SELECT * FROM admin_usuarios LIMIT 1");
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ <strong>Primer usuario obtenido: " . esc($usuario['nombre']) . "</strong><br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error con tabla:</strong> " . $e->getMessage() . "<br>";
}

echo "<br>";

// Paso 4: Verificar funciones necesarias
echo "<h3>4️⃣ VERIFICANDO FUNCIONES NECESARIAS</h3>";

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

// Paso 5: Simular la lógica de la página de usuarios
echo "<h3>5️⃣ SIMULANDO LÓGICA DE LA PÁGINA</h3>";

try {
    // Simular variables
    $current_page = 'usuarios.php';
    $current_dir = 'admin';
    $action = $_GET['action'] ?? 'list';
    $id = $_GET['id'] ?? null;
    
    echo "✅ <strong>Variables simuladas:</strong><br>";
    echo "&nbsp;&nbsp;• current_page: $current_page<br>";
    echo "&nbsp;&nbsp;• current_dir: $current_dir<br>";
    echo "&nbsp;&nbsp;• action: $action<br>";
    echo "&nbsp;&nbsp;• id: " . ($id ?: 'No definido') . "<br><br>";
    
    // Simular consulta de estadísticas
    $pdo = getDB();
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
    $stats['total'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as activos FROM admin_usuarios WHERE estado = 'activo'");
    $stats['activos'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as inactivos FROM admin_usuarios WHERE estado = 'inactivo'");
    $stats['inactivos'] = $stmt->fetchColumn();
    
    echo "✅ <strong>Estadísticas calculadas:</strong><br>";
    echo "&nbsp;&nbsp;• Total: {$stats['total']}<br>";
    echo "&nbsp;&nbsp;• Activos: {$stats['activos']}<br>";
    echo "&nbsp;&nbsp;• Inactivos: {$stats['inactivos']}<br><br>";
    
    // Simular consulta de usuarios
    $stmt = $pdo->query("SELECT * FROM admin_usuarios ORDER BY created_at DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ <strong>Usuarios obtenidos: " . count($usuarios) . "</strong><br>";
    
    if (!empty($usuarios)) {
        echo "&nbsp;&nbsp;• Primer usuario: " . esc($usuarios[0]['nombre']) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error en simulación:</strong> " . $e->getMessage() . "<br>";
}

echo "<br>";

// Paso 6: Verificar si hay errores de PHP
echo "<h3>6️⃣ VERIFICANDO ERRORES DE PHP</h3>";

$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $errors = file_get_contents($error_log);
    $recent_errors = array_slice(explode("\n", $errors), -10);
    echo "✅ <strong>Log de errores encontrado</strong><br>";
    echo "<strong>Últimos 10 errores:</strong><br>";
    foreach ($recent_errors as $error) {
        if (!empty(trim($error))) {
            echo "&nbsp;&nbsp;• " . esc($error) . "<br>";
        }
    }
} else {
    echo "⚠️ <strong>No se encontró log de errores</strong><br>";
}

echo "<br>";

// Paso 7: Probar renderizado HTML básico
echo "<h3>7️⃣ PROBANDO RENDERIZADO HTML</h3>";

echo "<strong>Probando HTML básico:</strong><br>";
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";
echo "✅ <strong>Este div debería ser visible</strong><br>";
echo "✅ <strong>Si ves esto, el renderizado HTML funciona</strong><br>";
echo "</div>";

echo "<br>";

// Paso 8: Verificar si hay output buffering o problemas de headers
echo "<h3>8️⃣ VERIFICANDO OUTPUT BUFFERING</h3>";

$ob_level = ob_get_level();
echo "✅ <strong>Nivel de output buffering: $ob_level</strong><br>";

if (ob_get_length() > 0) {
    echo "⚠️ <strong>Hay contenido en el buffer de salida</strong><br>";
    $buffer_content = ob_get_contents();
    echo "&nbsp;&nbsp;• Contenido del buffer: " . esc(substr($buffer_content, 0, 100)) . "...<br>";
} else {
    echo "✅ <strong>No hay contenido en el buffer de salida</strong><br>";
}

echo "<br>";

// Paso 9: Verificar headers enviados
echo "<h3>9️⃣ VERIFICANDO HEADERS</h3>";

if (headers_sent($file, $line)) {
    echo "❌ <strong>Headers ya enviados desde $file línea $line</strong><br>";
} else {
    echo "✅ <strong>Headers no enviados aún</strong><br>";
}

echo "<br>";

echo "<hr>";
echo "<h3>🎯 RESUMEN DEL DIAGNÓSTICO</h3>";
echo "<p><strong>Si todos los pasos anteriores muestran ✅, entonces el problema podría estar en:</strong></p>";
echo "<ul>";
echo "<li>Problemas de CSS que ocultan el contenido</li>";
echo "<li>JavaScript que interfiere con el renderizado</li>";
echo "<li>Problemas de autenticación que redirigen la página</li>";
echo "<li>Errores en el archivo usuarios.php que no se están mostrando</li>";
echo "</ul>";

echo "<p><strong>Próximo paso:</strong> Si este diagnóstico muestra todo correcto, necesitaremos revisar el archivo usuarios.php línea por línea.</p>";
?>
