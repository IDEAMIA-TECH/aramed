<?php
/**
 * SCRIPT PARA CREAR TABLAS DEL CATÁLOGO
 * Ejecutar desde: https://aramedylaboratorio.com/NUEVO/aramed/public_html/crear_tablas_catalogo.php
 */

// Configuración de base de datos (usando las credenciales del config.php)
$db_config = [
    'host' => '173.231.22.109',
    'dbname' => 'aramed2025_produccion',
    'username' => 'aramed2025_prod',
    'password' => 'pmDLi&PB$zntrzJ4'
];

// Función para conectar a la base de datos
function connectDB($config) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para crear las tablas
function createTables($pdo) {
    echo "<br>🏗️ CREANDO TABLAS DEL CATÁLOGO...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    // Leer el script SQL
    $script_path = __DIR__ . '/../database/nueva_estructura_catalogo.sql';
    if (!file_exists($script_path)) {
        echo "❌ Archivo no encontrado: $script_path<br>";
        return false;
    }
    
    $sql = file_get_contents($script_path);
    if (!$sql) {
        echo "❌ Error al leer el archivo<br>";
        return false;
    }
    
    try {
        // Ejecutar el script completo
        $pdo->exec($sql);
        echo "✅ Script ejecutado exitosamente<br>";
        return true;
    } catch (PDOException $e) {
        echo "❌ Error ejecutando script: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para verificar tablas
function checkTables($pdo) {
    echo "<br>🔍 VERIFICANDO TABLAS CREADAS...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    $tables = [
        'catalogo_marcas',
        'catalogo_categorias',
        'catalogo_productos',
        'catalogo_producto_imagenes',
        'catalogo_producto_documentos',
        'catalogo_filtros',
        'catalogo_producto_stats'
    ];
    
    $all_created = true;
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            $exists = $stmt->rowCount() > 0;
            
            if ($exists) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                echo "✅ $table: $count registros<br>";
            } else {
                echo "❌ $table: NO EXISTE<br>";
                $all_created = false;
            }
        } catch (PDOException $e) {
            echo "❌ $table: ERROR - " . $e->getMessage() . "<br>";
            $all_created = false;
        }
    }
    
    return $all_created;
}

// EJECUTAR CREACIÓN DE TABLAS
echo "<h1>🚀 CREANDO ESTRUCTURA DE TABLAS DEL CATÁLOGO</h1>";
echo "<hr>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Conectar a la base de datos
echo "<br>🔌 CONECTANDO A LA BASE DE DATOS...<br>";
$pdo = connectDB($db_config);
if (!$pdo) {
    echo "❌ No se pudo conectar a la base de datos.<br>";
    echo "Verificar configuración en el archivo.<br>";
    exit(1);
}
echo "✅ Conexión exitosa<br>";

// Crear tablas
$success = createTables($pdo);

if ($success) {
    // Verificar tablas
    $all_created = checkTables($pdo);
    
    echo "<br><hr>";
    echo "<h2>📊 RESULTADO FINAL</h2>";
    echo "<hr>";
    
    if ($all_created) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
        echo "✅ <strong>ÉXITO:</strong> Todas las tablas fueron creadas correctamente<br>";
        echo "</div>";
        echo "<br><strong>📋 Próximos pasos:</strong><br>";
        echo "1. Ejecutar migración de datos (opcional)<br>";
        echo "2. Crear páginas de catálogo<br>";
        echo "3. Implementar funcionalidades<br>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "⚠️ <strong>ADVERTENCIA:</strong> Algunas tablas no se crearon<br>";
        echo "</div>";
        echo "Revisar los errores anteriores<br>";
    }
} else {
    echo "<br><div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "❌ <strong>ERROR:</strong> No se pudieron crear las tablas<br>";
    echo "</div>";
    echo "Revisar la configuración y permisos<br>";
}

echo "<br><hr>";
echo "<p><strong>✅ Proceso completado</strong></p>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

?>
