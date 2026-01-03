<?php
/**
 * Script de diagnóstico para verificar usuarios en la BD
 * ELIMINAR DESPUÉS DE USAR
 */

define('ARAMED_SITE', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/connection.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNÓSTICO DE USUARIOS ===\n\n";

$pdo = getDB();
if (!$pdo) {
    die("ERROR: No se pudo conectar a la base de datos\n");
}

echo "✓ Conexión a BD exitosa\n\n";

// Verificar si la tabla existe
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_usuarios'");
    if ($stmt->rowCount() === 0) {
        die("ERROR: La tabla admin_usuarios no existe\n");
    }
    echo "✓ Tabla admin_usuarios existe\n\n";
} catch (Exception $e) {
    die("ERROR verificando tabla: " . $e->getMessage() . "\n");
}

// Listar todos los usuarios
echo "=== USUARIOS EN LA BD ===\n";
try {
    $stmt = $pdo->query("SELECT id, username, email, nombre, rol, estado, ultimo_login FROM admin_usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($usuarios)) {
        echo "⚠ No hay usuarios en la base de datos\n";
    } else {
        foreach ($usuarios as $user) {
            echo "ID: {$user['id']}\n";
            echo "  Username: {$user['username']}\n";
            echo "  Email: {$user['email']}\n";
            echo "  Nombre: {$user['nombre']}\n";
            echo "  Rol: {$user['rol']}\n";
            echo "  Estado: {$user['estado']}\n";
            echo "  Último login: " . ($user['ultimo_login'] ?? 'Nunca') . "\n";
            echo "\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR listando usuarios: " . $e->getMessage() . "\n";
}

// Verificar usuario específico
echo "\n=== VERIFICANDO USUARIO 'admin' ===\n";
try {
    $stmt = $pdo->prepare("SELECT * FROM admin_usuarios WHERE username = ? OR email = ?");
    $stmt->execute(['admin', 'admin']);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo "✓ Usuario encontrado\n";
        echo "  ID: {$usuario['id']}\n";
        echo "  Username: {$usuario['username']}\n";
        echo "  Email: {$usuario['email']}\n";
        echo "  Estado: {$usuario['estado']}\n";
        echo "  Password hash: " . (isset($usuario['password_hash']) ? 'PRESENTE (' . strlen($usuario['password_hash']) . ' caracteres)' : 'NO EXISTE') . "\n";
        
        // Verificar contraseña
        if (isset($usuario['password_hash'])) {
            $test_password = 'admin123';
            $verify = password_verify($test_password, $usuario['password_hash']);
            echo "  Verificación password 'admin123': " . ($verify ? '✓ CORRECTO' : '✗ INCORRECTO') . "\n";
            
            if (!$verify) {
                echo "\n⚠ La contraseña no coincide. Regenerando hash...\n";
                $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE admin_usuarios SET password_hash = ? WHERE id = ?");
                $update_stmt->execute([$new_hash, $usuario['id']]);
                echo "✓ Hash actualizado. Intenta iniciar sesión nuevamente.\n";
            }
        }
    } else {
        echo "✗ Usuario 'admin' NO encontrado\n";
        echo "\nCreando usuario admin...\n";
        
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_stmt = $pdo->prepare("
            INSERT INTO admin_usuarios (username, email, password_hash, nombre, rol, estado) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert_stmt->execute([
            'admin',
            'admin@aramedylaboratorio.com',
            $password_hash,
            'Administrador Principal',
            'admin',
            'activo'
        ]);
        
        echo "✓ Usuario 'admin' creado con contraseña 'admin123'\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Verificar estructura de la tabla
echo "\n=== ESTRUCTURA DE LA TABLA ===\n";
try {
    $stmt = $pdo->query("DESCRIBE admin_usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
echo "\n⚠ IMPORTANTE: Elimina este archivo después de usarlo por seguridad.\n";
?>

