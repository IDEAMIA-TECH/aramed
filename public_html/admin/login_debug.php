<?php
/**
 * Login con debugging para identificar problemas
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug del Login</h1>";

try {
    // Definir constante del sitio
    define('ARAMED_SITE', true);
    echo "<p>✓ Constante ARAMED_SITE definida</p>";
    
    // Cargar configuración
    echo "<p>Intentando cargar config.php...</p>";
    require_once __DIR__ . '/../includes/config.php';
    echo "<p style='color: green;'>✓ config.php cargado</p>";
    
    echo "<p>Intentando cargar functions.php...</p>";
    require_once __DIR__ . '/../includes/functions.php';
    echo "<p style='color: green;'>✓ functions.php cargado</p>";
    
    echo "<p>Intentando cargar connection.php...</p>";
    require_once __DIR__ . '/../includes/connection.php';
    echo "<p style='color: green;'>✓ connection.php cargado</p>";
    
    // Verificar conexión a base de datos
    echo "<p>Intentando conectar a la base de datos...</p>";
    $pdo = getDB();
    if ($pdo) {
        echo "<p style='color: green;'>✓ Conexión a base de datos exitosa</p>";
        
        // Crear tabla de usuarios admin
        echo "<p>Creando tabla admin_usuarios...</p>";
        $sql_create_table = "
            CREATE TABLE IF NOT EXISTS admin_usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                nombre VARCHAR(100) NOT NULL,
                rol ENUM('admin', 'editor') DEFAULT 'editor',
                estado ENUM('activo', 'inactivo') DEFAULT 'activo',
                ultimo_login TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ";
        
        $pdo->exec($sql_create_table);
        echo "<p style='color: green;'>✓ Tabla admin_usuarios creada/verificada</p>";
        
        // Verificar si existe algún administrador
        $sql_check_admin = "SELECT COUNT(*) as count FROM admin_usuarios WHERE rol = 'admin'";
        $stmt_check = $pdo->prepare($sql_check_admin);
        $stmt_check->execute();
        $admin_count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "<p>Administradores encontrados: $admin_count</p>";
        
        // Si no hay administradores, crear uno por defecto
        if ($admin_count == 0) {
            echo "<p>Creando usuario administrador por defecto...</p>";
            $default_password = 'admin123';
            $password_hash = password_hash($default_password, PASSWORD_DEFAULT);
            
            $sql_insert_admin = "
                INSERT INTO admin_usuarios (username, email, password_hash, nombre, rol, estado) 
                VALUES (?, ?, ?, ?, ?, ?)
            ";
            
            $stmt_insert = $pdo->prepare($sql_insert_admin);
            $result = $stmt_insert->execute([
                'admin',
                'admin@aramedylaboratorio.com',
                $password_hash,
                'Administrador Principal',
                'admin',
                'activo'
            ]);
            
            if ($result) {
                echo "<p style='color: green;'>✓ Usuario administrador creado</p>";
                echo "<p><strong>Usuario:</strong> admin</p>";
                echo "<p><strong>Contraseña:</strong> admin123</p>";
            } else {
                echo "<p style='color: red;'>✗ Error al crear usuario administrador</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>✗ Error de conexión a base de datos</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Ir al Login Real</a></p>";
echo "<p><a href='index_simple.php'>Dashboard Simple</a></p>";
echo "<p><a href='test.php'>Archivo de Prueba</a></p>";
?>
