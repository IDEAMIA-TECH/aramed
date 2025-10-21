<?php
/**
 * SCRIPT PARA MIGRAR DATOS DEL CATÁLOGO
 * Ejecutar desde: https://aramedylaboratorio.com/NUEVO/aramed/public_html/migrar_datos_catalogo.php
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
        echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para ejecutar script SQL
function executeSQLScript($pdo, $script_path) {
    echo "<br>🔧 Ejecutando: $script_path<br>";
    echo str_repeat("-", 50) . "<br>";
    
    if (!file_exists($script_path)) {
        echo "❌ Archivo no encontrado: $script_path<br>";
        return false;
    }
    
    $sql = file_get_contents($script_path);
    if (!$sql) {
        echo "❌ Error al leer el archivo: $script_path<br>";
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
                    echo "✅ Ejecutados: $executed statements<br>";
                }
            } catch (PDOException $e) {
                $errors++;
                echo "⚠️ Error en statement: " . substr($statement, 0, 100) . "...<br>";
                echo "   Error: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<br><strong>📊 Resultado:</strong><br>";
        echo "   ✅ Statements ejecutados: $executed<br>";
        echo "   ❌ Errores: $errors<br>";
        
        return $errors == 0;
        
    } catch (Exception $e) {
        echo "❌ Error general: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para mostrar estadísticas
function showStatistics($pdo) {
    echo "<br>📊 ESTADÍSTICAS DE MIGRACIÓN:<br>";
    echo str_repeat("-", 50) . "<br>";
    
    $stats = [];
    
    try {
        // Verificar tablas del sistema viejo
        $old_tables = ['marcas', 'productos', 'imagenes_x_producto', 'usos'];
        foreach ($old_tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                $stats["old_$table"] = $count;
                echo "📋 Sistema viejo - $table: $count registros<br>";
            } catch (PDOException $e) {
                echo "❌ Sistema viejo - $table: No disponible<br>";
            }
        }
        
        echo "<br>";
        
        // Verificar tablas nuevas
        $new_tables = ['catalogo_marcas', 'catalogo_categorias', 'catalogo_productos'];
        foreach ($new_tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                $stats["new_$table"] = $count;
                echo "🆕 Sistema nuevo - $table: $count registros<br>";
            } catch (PDOException $e) {
                echo "❌ Sistema nuevo - $table: No disponible<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error obteniendo estadísticas: " . $e->getMessage() . "<br>";
    }
    
    return $stats;
}

// EJECUTAR MIGRACIÓN
echo "<h1>🚀 MIGRANDO DATOS DEL CATÁLOGO</h1>";
echo "<hr>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Conectar a la base de datos
echo "<br>🔌 CONECTANDO A LA BASE DE DATOS...<br>";
$pdo = connectDB($db_config);
if (!$pdo) {
    echo "❌ No se pudo conectar a la base de datos.<br>";
    exit(1);
}
echo "✅ Conexión exitosa<br>";

// Verificar estado inicial
echo "<br>📋 VERIFICANDO ESTADO INICIAL...<br>";
$initial_stats = showStatistics($pdo);

// Ejecutar script de migración de datos
echo "<br>📊 MIGRANDO DATOS...<br>";
$migration_success = executeSQLScript($pdo, __DIR__ . '/../database/migracion_datos_catalogo.sql');

if (!$migration_success) {
    echo "<br>⚠️ Algunos errores en la migración de datos, pero continuando...<br>";
}

// Verificar estado final
echo "<br>📋 VERIFICANDO ESTADO FINAL...<br>";
$final_stats = showStatistics($pdo);

// Generar reporte final
echo "<br><hr>";
echo "<h2>📊 REPORTE FINAL DE MIGRACIÓN</h2>";
echo "<hr>";

echo "<br><strong>🎯 ESTADO GENERAL:</strong><br>";
if ($migration_success) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "✅ <strong>MIGRACIÓN EXITOSA</strong><br>";
    echo "✅ Datos migrados correctamente<br>";
    echo "✅ Sistema listo para uso<br>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721967;'>";
    echo "⚠️ <strong>MIGRACIÓN CON PROBLEMAS</strong><br>";
    echo "❌ Algunos datos no se migraron<br>";
    echo "</div>";
}

echo "<br><strong>📋 Próximos pasos:</strong><br>";
echo "1. Verificar datos en las tablas<br>";
echo "2. Crear páginas de catálogo en el frontend<br>";
echo "3. Implementar sistema de búsqueda<br>";
echo "4. Configurar filtros dinámicos<br>";
echo "5. Optimizar para SEO<br>";

echo "<br><hr>";
echo "<p><strong>✅ MIGRACIÓN COMPLETADA</strong></p>";
echo "<p><strong>Fecha de finalización:</strong> " . date('Y-m-d H:i:s') . "</p>";

?>
