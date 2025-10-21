<?php
/**
 * SCRIPT SIMPLE PARA CREAR SOLO LAS TABLAS DEL CATÁLOGO
 * Este script solo crea la estructura, no migra datos
 */

// Configuración de base de datos
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
        echo "❌ Error de conexión: " . $e->getMessage() . "\n";
        return false;
    }
}

// Función para crear las tablas
function createTables($pdo) {
    echo "\n🏗️ CREANDO TABLAS DEL CATÁLOGO...\n";
    echo str_repeat("-", 50) . "\n";
    
    // Leer el script SQL
    $script_path = __DIR__ . '/nueva_estructura_catalogo.sql';
    if (!file_exists($script_path)) {
        echo "❌ Archivo no encontrado: $script_path\n";
        return false;
    }
    
    $sql = file_get_contents($script_path);
    if (!$sql) {
        echo "❌ Error al leer el archivo\n";
        return false;
    }
    
    try {
        // Ejecutar el script completo
        $pdo->exec($sql);
        echo "✅ Script ejecutado exitosamente\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ Error ejecutando script: " . $e->getMessage() . "\n";
        return false;
    }
}

// Función para verificar tablas
function checkTables($pdo) {
    echo "\n🔍 VERIFICANDO TABLAS CREADAS...\n";
    echo str_repeat("-", 50) . "\n";
    
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
                echo "✅ $table: $count registros\n";
            } else {
                echo "❌ $table: NO EXISTE\n";
                $all_created = false;
            }
        } catch (PDOException $e) {
            echo "❌ $table: ERROR - " . $e->getMessage() . "\n";
            $all_created = false;
        }
    }
    
    return $all_created;
}

// EJECUTAR CREACIÓN DE TABLAS
echo "🚀 CREANDO ESTRUCTURA DE TABLAS DEL CATÁLOGO\n";
echo str_repeat("=", 50) . "\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 50) . "\n";

// Conectar a la base de datos
echo "\n🔌 CONECTANDO A LA BASE DE DATOS...\n";
$pdo = connectDB($db_config);
if (!$pdo) {
    echo "❌ No se pudo conectar a la base de datos.\n";
    echo "Verificar configuración en el archivo.\n";
    exit(1);
}
echo "✅ Conexión exitosa\n";

// Crear tablas
$success = createTables($pdo);

if ($success) {
    // Verificar tablas
    $all_created = checkTables($pdo);
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 RESULTADO FINAL\n";
    echo str_repeat("=", 50) . "\n";
    
    if ($all_created) {
        echo "✅ ÉXITO: Todas las tablas fueron creadas correctamente\n";
        echo "\n📋 Próximos pasos:\n";
        echo "   1. Ejecutar migración de datos (opcional)\n";
        echo "   2. Crear páginas de catálogo\n";
        echo "   3. Implementar funcionalidades\n";
    } else {
        echo "⚠️  ADVERTENCIA: Algunas tablas no se crearon\n";
        echo "   Revisar los errores anteriores\n";
    }
} else {
    echo "\n❌ ERROR: No se pudieron crear las tablas\n";
    echo "   Revisar la configuración y permisos\n";
}

echo "\n✅ Proceso completado\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";

?>
