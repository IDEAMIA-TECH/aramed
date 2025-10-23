<?php
/**
 * ========================================
 * TEST DATOS DEL PERFIL
 * ========================================
 * 
 * Script para verificar que los datos del perfil se muestran correctamente
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

echo "<h2>🧪 TEST DATOS DEL PERFIL</h2><hr>";

echo "<h3>1️⃣ VERIFICANDO CONEXIÓN A BASE DE DATOS</h3>";

try {
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/functions.php';
    require_once __DIR__ . '/includes/connection.php';
    
    $pdo = getDB();
    echo "✅ <strong>Conexión a BD establecida</strong><br>";
    
    echo "<h3>2️⃣ VERIFICANDO ESTRUCTURA DE LA TABLA</h3>";
    
    $stmt = $pdo->query("DESCRIBE admin_usuarios");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<strong>Columnas de la tabla admin_usuarios:</strong><br>";
    foreach ($columnas as $columna) {
        echo "&nbsp;&nbsp;• <strong>{$columna['Field']}</strong> - {$columna['Type']}<br>";
    }
    
    echo "<br>";
    
    echo "<h3>3️⃣ VERIFICANDO DATOS DEL USUARIO</h3>";
    
    // Simular sesión
    session_start();
    $_SESSION['admin_user_id'] = 1;
    
    $stmt = $pdo->prepare("SELECT * FROM admin_usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['admin_user_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo "✅ <strong>Usuario encontrado</strong><br>";
        echo "&nbsp;&nbsp;• <strong>ID:</strong> {$usuario['id']}<br>";
        echo "&nbsp;&nbsp;• <strong>Nombre:</strong> {$usuario['nombre']}<br>";
        echo "&nbsp;&nbsp;• <strong>Email:</strong> {$usuario['email']}<br>";
        echo "&nbsp;&nbsp;• <strong>Rol:</strong> {$usuario['rol']}<br>";
        echo "&nbsp;&nbsp;• <strong>Estado:</strong> {$usuario['estado']}<br>";
        echo "&nbsp;&nbsp;• <strong>Último Login:</strong> " . ($usuario['ultimo_login'] ? $usuario['ultimo_login'] : 'Nunca') . "<br>";
        echo "&nbsp;&nbsp;• <strong>Creado:</strong> {$usuario['created_at']}<br>";
        echo "&nbsp;&nbsp;• <strong>Actualizado:</strong> {$usuario['updated_at']}<br>";
        
        echo "<br>";
        
        echo "<h3>4️⃣ VERIFICANDO CAMPOS ESPECÍFICOS</h3>";
        
        // Verificar campo estado
        if (isset($usuario['estado'])) {
            echo "✅ <strong>Campo 'estado' encontrado:</strong> {$usuario['estado']}<br>";
        } else {
            echo "❌ <strong>Campo 'estado' NO encontrado</strong><br>";
        }
        
        // Verificar campo ultimo_login
        if (isset($usuario['ultimo_login'])) {
            echo "✅ <strong>Campo 'ultimo_login' encontrado:</strong> " . ($usuario['ultimo_login'] ? $usuario['ultimo_login'] : 'NULL') . "<br>";
        } else {
            echo "❌ <strong>Campo 'ultimo_login' NO encontrado</strong><br>";
        }
        
        // Verificar campo password_hash
        if (isset($usuario['password_hash'])) {
            echo "✅ <strong>Campo 'password_hash' encontrado</strong><br>";
        } else {
            echo "❌ <strong>Campo 'password_hash' NO encontrado</strong><br>";
        }
        
        echo "<br>";
        
        echo "<h3>5️⃣ SIMULANDO LÓGICA DEL PERFIL</h3>";
        
        // Simular la lógica de mostrar estado
        $estado_display = $usuario['estado'] === 'activo' ? 'Activo' : 'Inactivo';
        echo "✅ <strong>Estado a mostrar:</strong> $estado_display<br>";
        
        // Simular la lógica de mostrar último acceso
        $ultimo_acceso_display = $usuario['ultimo_login'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_login'])) : 'Nunca';
        echo "✅ <strong>Último acceso a mostrar:</strong> $ultimo_acceso_display<br>";
        
        echo "<br>";
        
        echo "<h3>6️⃣ VERIFICANDO CORRECCIONES APLICADAS</h3>";
        
        $archivo_perfil = __DIR__ . '/admin/perfil.php';
        $contenido_perfil = file_get_contents($archivo_perfil);
        
        // Verificar que se usa 'estado' en lugar de 'activo'
        if (strpos($contenido_perfil, "usuario_actual['estado']") !== false) {
            echo "✅ <strong>Campo 'estado' usado correctamente</strong><br>";
        } else {
            echo "❌ <strong>Campo 'estado' NO usado correctamente</strong><br>";
        }
        
        // Verificar que se usa 'ultimo_login' en lugar de 'last_login'
        if (strpos($contenido_perfil, "usuario_actual['ultimo_login']") !== false) {
            echo "✅ <strong>Campo 'ultimo_login' usado correctamente</strong><br>";
        } else {
            echo "❌ <strong>Campo 'ultimo_login' NO usado correctamente</strong><br>";
        }
        
        // Verificar que se usa 'password_hash' en lugar de 'password'
        if (strpos($contenido_perfil, "usuario_actual['password_hash']") !== false) {
            echo "✅ <strong>Campo 'password_hash' usado correctamente</strong><br>";
        } else {
            echo "❌ <strong>Campo 'password_hash' NO usado correctamente</strong><br>";
        }
        
        echo "<br>";
        
        echo "<h3>✅ RESUMEN</h3>";
        
        $errores = 0;
        if (!isset($usuario['estado'])) $errores++;
        if (!isset($usuario['ultimo_login'])) $errores++;
        if (!isset($usuario['password_hash'])) $errores++;
        if (strpos($contenido_perfil, "usuario_actual['estado']") === false) $errores++;
        if (strpos($contenido_perfil, "usuario_actual['ultimo_login']") === false) $errores++;
        if (strpos($contenido_perfil, "usuario_actual['password_hash']") === false) $errores++;
        
        if ($errores == 0) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>TODAS LAS VERIFICACIONES PASARON</strong><br>";
            echo "Los datos del perfil deberían mostrarse correctamente ahora.<br>";
            echo "<a href='admin/perfil.php' target='_blank' style='color: #155724; font-weight: bold;'>🔗 Probar Página de Perfil</a>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>SE ENCONTRARON $errores ERRORES</strong><br>";
            echo "Revisa los errores anteriores antes de probar la página.";
            echo "</div>";
        }
        
    } else {
        echo "❌ <strong>Usuario no encontrado</strong><br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Este test verifica que los datos del perfil se muestran correctamente.</p>";
?>
