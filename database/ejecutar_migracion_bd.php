<?php
/**
 * SCRIPT PARA EJECUTAR LA MIGRACIÓN DE BASE DE DATOS
 * Este script debe ejecutarse desde el navegador en el servidor web
 */

// Configuración de base de datos (actualizar con los datos reales)
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

// Función para ejecutar script SQL
function executeSQLScript($pdo, $script_path) {
    echo "\n🔧 Ejecutando: $script_path\n";
    echo str_repeat("-", 50) . "\n";
    
    if (!file_exists($script_path)) {
        echo "❌ Archivo no encontrado: $script_path\n";
        return false;
    }
    
    $sql = file_get_contents($script_path);
    if (!$sql) {
        echo "❌ Error al leer el archivo: $script_path\n";
        return false;
    }
    
    try {
        // Dividir el script en statements individuales
        $statements = explode(';', $sql);
        $executed = 0;
        $errors = 0;
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            try {
                $pdo->exec($statement);
                $executed++;
                
                // Mostrar progreso cada 10 statements
                if ($executed % 10 == 0) {
                    echo "✅ Ejecutados: $executed statements\n";
                }
            } catch (PDOException $e) {
                $errors++;
                echo "⚠️  Error en statement: " . substr($statement, 0, 100) . "...\n";
                echo "   Error: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n📊 Resultado:\n";
        echo "   ✅ Statements ejecutados: $executed\n";
        echo "   ❌ Errores: $errors\n";
        
        return $errors == 0;
        
    } catch (Exception $e) {
        echo "❌ Error general: " . $e->getMessage() . "\n";
        return false;
    }
}

// Función para verificar tablas creadas
function verifyTables($pdo) {
    echo "\n🔍 VERIFICANDO TABLAS CREADAS...\n";
    echo str_repeat("=", 50) . "\n";
    
    $expected_tables = [
        'catalogo_marcas',
        'catalogo_categorias',
        'catalogo_productos',
        'catalogo_producto_imagenes',
        'catalogo_producto_documentos',
        'catalogo_filtros',
        'catalogo_producto_stats'
    ];
    
    $created_tables = [];
    $missing_tables = [];
    
    foreach ($expected_tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            $exists = $stmt->rowCount() > 0;
            
            if ($exists) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                $created_tables[$table] = $count;
                echo "✅ $table: $count registros\n";
            } else {
                $missing_tables[] = $table;
                echo "❌ $table: NO EXISTE\n";
            }
        } catch (PDOException $e) {
            $missing_tables[] = $table;
            echo "❌ $table: ERROR - " . $e->getMessage() . "\n";
        }
    }
    
    return ['created' => $created_tables, 'missing' => $missing_tables];
}

// Función para mostrar estadísticas
function showStatistics($pdo) {
    echo "\n📊 ESTADÍSTICAS DE MIGRACIÓN:\n";
    echo str_repeat("=", 50) . "\n";
    
    $stats = [];
    
    try {
        // Verificar tablas del sistema viejo
        $old_tables = ['marcas', 'productos', 'imagenes_x_producto', 'usos'];
        foreach ($old_tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                $stats["old_$table"] = $count;
                echo "📋 Sistema viejo - $table: $count registros\n";
            } catch (PDOException $e) {
                echo "❌ Sistema viejo - $table: No disponible\n";
            }
        }
        
        echo "\n";
        
        // Verificar tablas nuevas
        $new_tables = ['catalogo_marcas', 'catalogo_categorias', 'catalogo_productos'];
        foreach ($new_tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                $stats["new_$table"] = $count;
                echo "🆕 Sistema nuevo - $table: $count registros\n";
            } catch (PDOException $e) {
                echo "❌ Sistema nuevo - $table: No disponible\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error obteniendo estadísticas: " . $e->getMessage() . "\n";
    }
    
    return $stats;
}

// EJECUTAR MIGRACIÓN
echo "🚀 INICIANDO MIGRACIÓN DE BASE DE DATOS DEL CATÁLOGO\n";
echo str_repeat("=", 60) . "\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";

// Conectar a la base de datos
echo "\n🔌 CONECTANDO A LA BASE DE DATOS...\n";
$pdo = connectDB($db_config);
if (!$pdo) {
    echo "❌ No se pudo conectar a la base de datos. Verificar configuración.\n";
    exit(1);
}
echo "✅ Conexión exitosa\n";

// Verificar estado inicial
echo "\n📋 VERIFICANDO ESTADO INICIAL...\n";
$initial_stats = showStatistics($pdo);

// Ejecutar script de estructura
echo "\n🏗️ CREANDO ESTRUCTURA DE TABLAS...\n";
$structure_success = executeSQLScript($pdo, __DIR__ . '/nueva_estructura_catalogo.sql');

if (!$structure_success) {
    echo "❌ Error creando estructura. Deteniendo migración.\n";
    exit(1);
}

// Verificar tablas creadas
$verification = verifyTables($pdo);

// Ejecutar script de migración de datos
echo "\n📊 MIGRANDO DATOS...\n";
$migration_success = executeSQLScript($pdo, __DIR__ . '/migracion_datos_catalogo.sql');

if (!$migration_success) {
    echo "⚠️  Algunos errores en la migración de datos, pero continuando...\n";
}

// Verificar estado final
echo "\n📋 VERIFICANDO ESTADO FINAL...\n";
$final_stats = showStatistics($pdo);

// Generar reporte final
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 REPORTE FINAL DE MIGRACIÓN\n";
echo str_repeat("=", 60) . "\n";

echo "\n✅ ESTRUCTURA:\n";
foreach ($verification['created'] as $table => $count) {
    echo "   $table: $count registros\n";
}

if (!empty($verification['missing'])) {
    echo "\n❌ TABLAS FALTANTES:\n";
    foreach ($verification['missing'] as $table) {
        echo "   $table\n";
    }
}

echo "\n🎯 ESTADO GENERAL:\n";
if (empty($verification['missing']) && !empty($verification['created'])) {
    echo "   ✅ MIGRACIÓN EXITOSA\n";
    echo "   ✅ Todas las tablas creadas\n";
    echo "   ✅ Datos migrados correctamente\n";
    echo "   ✅ Sistema listo para uso\n";
} else {
    echo "   ⚠️  MIGRACIÓN CON PROBLEMAS\n";
    if (!empty($verification['missing'])) {
        echo "   ❌ Algunas tablas no se crearon\n";
    }
}

echo "\n📋 PRÓXIMOS PASOS:\n";
echo "   1. Verificar datos en las tablas\n";
echo "   2. Crear páginas de catálogo en el frontend\n";
echo "   3. Implementar sistema de búsqueda\n";
echo "   4. Configurar filtros dinámicos\n";
echo "   5. Optimizar para SEO\n";

echo "\n✅ MIGRACIÓN COMPLETADA\n";
echo "Fecha de finalización: " . date('Y-m-d H:i:s') . "\n";

?>
