<?php
/**
 * Archivo de debug para config.php
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Debug de config.php</h1>";
echo "<hr>";

echo "<h2>1. Sesión</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Admin logged in: " . (isset($_SESSION['admin_logged_in']) ? 'YES' : 'NO') . "<br>";
echo "User role: " . ($_SESSION['admin_rol'] ?? 'NOT SET') . "<br>";
echo "<hr>";

echo "<h2>2. Cargar config.php</h2>";
try {
    require_once __DIR__ . '/../../includes/config.php';
    echo "✓ config.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    die();
}

echo "<h2>3. Cargar functions.php</h2>";
try {
    require_once __DIR__ . '/../../includes/functions.php';
    echo "✓ functions.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    die();
}

echo "<h2>4. Cargar connection.php</h2>";
try {
    require_once __DIR__ . '/../../includes/connection.php';
    echo "✓ connection.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    die();
}

echo "<h2>5. Verificar getDB()</h2>";
if (function_exists('getDB')) {
    echo "✓ getDB() existe<br>";
    try {
        $pdo = getDB();
        if ($pdo) {
            echo "✓ Conexión PDO exitosa<br>";
        } else {
            echo "✗ getDB() retornó null<br>";
        }
    } catch (Exception $e) {
        echo "✗ Error en getDB(): " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ getDB() no existe<br>";
}

echo "<h2>6. Cargar auth_check.php</h2>";
try {
    require_once __DIR__ . '/../auth_check.php';
    echo "✓ auth_check.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    die();
}

echo "<h2>7. Verificar rol</h2>";
$user_role = $_SESSION['admin_rol'] ?? 'editor';
echo "User role: " . $user_role . "<br>";
if ($user_role !== 'admin') {
    echo "✗ Usuario no es admin<br>";
} else {
    echo "✓ Usuario es admin<br>";
}

echo "<h2>8. Verificar checkPermission()</h2>";
if (function_exists('checkPermission')) {
    echo "✓ checkPermission() existe<br>";
    try {
        $result = checkPermission('seo', 'editar', false);
        echo "checkPermission('seo', 'editar', false) = " . ($result ? 'true' : 'false') . "<br>";
    } catch (Exception $e) {
        echo "✗ Error en checkPermission(): " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ checkPermission() no existe<br>";
}

echo "<h2>9. Verificar getCurrentUser()</h2>";
if (function_exists('getCurrentUser')) {
    echo "✓ getCurrentUser() existe<br>";
    try {
        $current_user = getCurrentUser();
        echo "✓ getCurrentUser() ejecutado exitosamente<br>";
        echo "User ID: " . ($current_user['id'] ?? 'NOT SET') . "<br>";
    } catch (Exception $e) {
        echo "✗ Error en getCurrentUser(): " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ getCurrentUser() no existe<br>";
}

echo "<h2>10. Verificar tabla seo_config</h2>";
if (function_exists('getDB')) {
    try {
        $pdo = getDB();
        if ($pdo) {
            $stmt = $pdo->query("SHOW TABLES LIKE 'seo_config'");
            $table_exists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            if (!empty($table_exists)) {
                echo "✓ Tabla seo_config existe<br>";
            } else {
                echo "✗ Tabla seo_config NO existe<br>";
            }
        }
    } catch (Exception $e) {
        echo "✗ Error verificando tabla: " . $e->getMessage() . "<br>";
    }
}

echo "<hr>";
echo "<h2>✓ Todas las verificaciones completadas</h2>";
echo "<p>Si todas las verificaciones pasaron, el problema podría estar en el HTML o en alguna parte del código que no se ejecutó aquí.</p>";

