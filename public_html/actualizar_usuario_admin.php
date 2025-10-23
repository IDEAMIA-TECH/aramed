<?php
/**
 * ========================================
 * ACTUALIZAR USUARIO ADMIN
 * ========================================
 * 
 * Script para actualizar el usuario admin existente
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

echo "<h2>🔄 ACTUALIZANDO USUARIO ADMIN</h2><hr>";

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }
    echo "✅ <strong>Conexión a la base de datos establecida</strong><br><br>";

    // Actualizar el usuario admin para que esté activo
    $stmt = $pdo->prepare("UPDATE admin_usuarios SET estado = 'activo' WHERE id = 1");
    $stmt->execute();
    
    echo "✅ <strong>Usuario admin actualizado a estado 'activo'</strong><br><br>";

    // Verificar el usuario actualizado
    $stmt = $pdo->prepare("SELECT * FROM admin_usuarios WHERE id = 1");
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo "<h3>📋 USUARIO ADMIN ACTUALIZADO</h3>";
        echo "<strong>ID:</strong> {$usuario['id']}<br>";
        echo "<strong>Nombre:</strong> " . esc($usuario['nombre']) . "<br>";
        echo "<strong>Email:</strong> " . esc($usuario['email']) . "<br>";
        echo "<strong>Rol:</strong> {$usuario['rol']}<br>";
        echo "<strong>Estado:</strong> {$usuario['estado']}<br>";
        echo "<strong>Creado:</strong> {$usuario['created_at']}<br><br>";
    }

    // Verificar estadísticas actualizadas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
    $total = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as activos FROM admin_usuarios WHERE estado = 'activo'");
    $activos = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as inactivos FROM admin_usuarios WHERE estado = 'inactivo'");
    $inactivos = $stmt->fetchColumn();
    
    echo "<h3>📊 ESTADÍSTICAS ACTUALIZADAS</h3>";
    echo "<strong>Total usuarios:</strong> $total<br>";
    echo "<strong>Usuarios activos:</strong> $activos<br>";
    echo "<strong>Usuarios inactivos:</strong> $inactivos<br><br>";

    echo "✅ <strong>Actualización completada exitosamente</strong><br>";

} catch (Exception $e) {
    echo "❌ <strong>Error durante la actualización:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> El usuario admin ahora está activo y la página de usuarios debería funcionar correctamente.</p>";
?>
