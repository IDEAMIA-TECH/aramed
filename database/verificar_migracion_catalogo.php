<?php
/**
 * SCRIPT DE VERIFICACIÓN DE MIGRACIÓN
 * Verifica que la migración del catálogo se completó correctamente
 */

// Configuración de base de datos
$db_config = [
    'host' => 'localhost',
    'dbname' => 'aramed2025_aramed_db',
    'username' => 'tu_usuario',
    'password' => 'tu_password'
];

// Configuración de directorios
$dirs_config = [
    'productos' => '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/productos/',
    'galeria' => '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/galeria/',
    'documentos' => '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/documents/catalogo/'
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

// Función para verificar tablas
function verifyTables($pdo) {
    echo "\n🔍 VERIFICANDO TABLAS DE BASE DE DATOS...\n";
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
    
    $results = [];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            $exists = $stmt->rowCount() > 0;
            
            if ($exists) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                $results[$table] = $count;
                echo "✅ $table: $count registros\n";
            } else {
                $results[$table] = 0;
                echo "❌ $table: NO EXISTE\n";
            }
        } catch (PDOException $e) {
            $results[$table] = -1;
            echo "❌ $table: ERROR - " . $e->getMessage() . "\n";
        }
    }
    
    return $results;
}

// Función para verificar archivos físicos
function verifyFiles($dirs_config) {
    echo "\n📁 VERIFICANDO ARCHIVOS FÍSICOS...\n";
    echo str_repeat("-", 50) . "\n";
    
    $results = [];
    
    foreach ($dirs_config as $type => $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '*');
            $count = count($files);
            $results[$type] = $count;
            echo "✅ $type: $count archivos en $dir\n";
            
            // Mostrar algunos ejemplos
            if ($count > 0) {
                $examples = array_slice($files, 0, 3);
                foreach ($examples as $file) {
                    echo "   📄 " . basename($file) . "\n";
                }
                if ($count > 3) {
                    echo "   ... y " . ($count - 3) . " archivos más\n";
                }
            }
        } else {
            $results[$type] = 0;
            echo "❌ $type: Directorio no existe - $dir\n";
        }
    }
    
    return $results;
}

// Función para verificar integridad de datos
function verifyDataIntegrity($pdo) {
    echo "\n🔗 VERIFICANDO INTEGRIDAD DE DATOS...\n";
    echo str_repeat("-", 50) . "\n";
    
    $checks = [];
    
    // Verificar productos con marcas válidas
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM catalogo_productos cp 
            LEFT JOIN catalogo_marcas cm ON cp.marca_id = cm.id 
            WHERE cm.id IS NULL
        ");
        $orphan_products = $stmt->fetch()['count'];
        $checks['productos_sin_marca'] = $orphan_products;
        echo $orphan_products == 0 ? "✅ Productos con marcas válidas\n" : "❌ $orphan_products productos sin marca válida\n";
    } catch (PDOException $e) {
        echo "❌ Error verificando marcas: " . $e->getMessage() . "\n";
    }
    
    // Verificar productos con categorías válidas
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM catalogo_productos cp 
            LEFT JOIN catalogo_categorias cc ON cp.categoria_id = cc.id 
            WHERE cc.id IS NULL
        ");
        $orphan_categories = $stmt->fetch()['count'];
        $checks['productos_sin_categoria'] = $orphan_categories;
        echo $orphan_categories == 0 ? "✅ Productos con categorías válidas\n" : "❌ $orphan_categories productos sin categoría válida\n";
    } catch (PDOException $e) {
        echo "❌ Error verificando categorías: " . $e->getMessage() . "\n";
    }
    
    // Verificar imágenes con productos válidos
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM catalogo_producto_imagenes cpi 
            LEFT JOIN catalogo_productos cp ON cpi.producto_id = cp.id 
            WHERE cp.id IS NULL
        ");
        $orphan_images = $stmt->fetch()['count'];
        $checks['imagenes_sin_producto'] = $orphan_images;
        echo $orphan_images == 0 ? "✅ Imágenes con productos válidos\n" : "❌ $orphan_images imágenes sin producto válido\n";
    } catch (PDOException $e) {
        echo "❌ Error verificando imágenes: " . $e->getMessage() . "\n";
    }
    
    // Verificar documentos con productos válidos
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM catalogo_producto_documentos cpd 
            LEFT JOIN catalogo_productos cp ON cpd.producto_id = cp.id 
            WHERE cp.id IS NULL
        ");
        $orphan_docs = $stmt->fetch()['count'];
        $checks['documentos_sin_producto'] = $orphan_docs;
        echo $orphan_docs == 0 ? "✅ Documentos con productos válidos\n" : "❌ $orphan_docs documentos sin producto válido\n";
    } catch (PDOException $e) {
        echo "❌ Error verificando documentos: " . $e->getMessage() . "\n";
    }
    
    return $checks;
}

// Función para verificar performance
function verifyPerformance($pdo) {
    echo "\n⚡ VERIFICANDO PERFORMANCE...\n";
    echo str_repeat("-", 50) . "\n";
    
    $performance = [];
    
    // Verificar índices
    try {
        $stmt = $pdo->query("SHOW INDEX FROM catalogo_productos");
        $indexes = $stmt->fetchAll();
        $performance['indices_productos'] = count($indexes);
        echo "✅ Índices en catalogo_productos: " . count($indexes) . "\n";
    } catch (PDOException $e) {
        echo "❌ Error verificando índices: " . $e->getMessage() . "\n";
    }
    
    // Verificar full-text search
    try {
        $stmt = $pdo->query("SHOW INDEX FROM catalogo_productos WHERE Key_name = 'busqueda'");
        $fulltext = $stmt->fetch();
        $performance['fulltext_search'] = $fulltext ? true : false;
        echo $fulltext ? "✅ Full-text search configurado\n" : "❌ Full-text search no configurado\n";
    } catch (PDOException $e) {
        echo "❌ Error verificando full-text: " . $e->getMessage() . "\n";
    }
    
    // Test de búsqueda
    try {
        $start = microtime(true);
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM catalogo_productos WHERE MATCH(nombre, descripcion_corta) AGAINST('simulador' IN NATURAL LANGUAGE MODE)");
        $result = $stmt->fetch();
        $time = microtime(true) - $start;
        $performance['busqueda_tiempo'] = $time;
        echo "✅ Búsqueda full-text: {$result['count']} resultados en " . round($time * 1000, 2) . "ms\n";
    } catch (PDOException $e) {
        echo "❌ Error en test de búsqueda: " . $e->getMessage() . "\n";
    }
    
    return $performance;
}

// Función para generar reporte final
function generateFinalReport($table_results, $file_results, $integrity_results, $performance_results) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 REPORTE FINAL DE VERIFICACIÓN\n";
    echo str_repeat("=", 60) . "\n";
    
    // Resumen de tablas
    echo "\n📋 RESUMEN DE TABLAS:\n";
    $total_records = array_sum($table_results);
    foreach ($table_results as $table => $count) {
        if ($count >= 0) {
            echo "   $table: $count registros\n";
        }
    }
    echo "   TOTAL: $total_records registros\n";
    
    // Resumen de archivos
    echo "\n📁 RESUMEN DE ARCHIVOS:\n";
    $total_files = array_sum($file_results);
    foreach ($file_results as $type => $count) {
        echo "   $type: $count archivos\n";
    }
    echo "   TOTAL: $total_files archivos\n";
    
    // Verificar integridad
    echo "\n🔗 INTEGRIDAD DE DATOS:\n";
    $integrity_ok = true;
    foreach ($integrity_results as $check => $count) {
        if ($count > 0) {
            echo "   ❌ $check: $count problemas encontrados\n";
            $integrity_ok = false;
        } else {
            echo "   ✅ $check: OK\n";
        }
    }
    
    // Performance
    echo "\n⚡ PERFORMANCE:\n";
    if (isset($performance_results['fulltext_search']) && $performance_results['fulltext_search']) {
        echo "   ✅ Full-text search: Configurado\n";
    } else {
        echo "   ❌ Full-text search: No configurado\n";
    }
    
    if (isset($performance_results['busqueda_tiempo'])) {
        $time = $performance_results['busqueda_tiempo'];
        if ($time < 0.1) {
            echo "   ✅ Búsqueda: Rápida (" . round($time * 1000, 2) . "ms)\n";
        } else {
            echo "   ⚠️  Búsqueda: Lenta (" . round($time * 1000, 2) . "ms)\n";
        }
    }
    
    // Estado general
    echo "\n🎯 ESTADO GENERAL:\n";
    if ($integrity_ok && $total_records > 0 && $total_files > 0) {
        echo "   ✅ MIGRACIÓN EXITOSA\n";
        echo "   ✅ Datos íntegros\n";
        echo "   ✅ Archivos migrados\n";
        echo "   ✅ Sistema listo para producción\n";
    } else {
        echo "   ❌ MIGRACIÓN CON PROBLEMAS\n";
        if (!$integrity_ok) echo "   ❌ Problemas de integridad\n";
        if ($total_records == 0) echo "   ❌ Sin datos en tablas\n";
        if ($total_files == 0) echo "   ❌ Sin archivos migrados\n";
    }
    
    echo str_repeat("=", 60) . "\n";
}

// EJECUTAR VERIFICACIÓN
echo "🔍 INICIANDO VERIFICACIÓN DE MIGRACIÓN DEL CATÁLOGO\n";
echo str_repeat("=", 60) . "\n";

// Conectar a la base de datos
$pdo = connectDB($db_config);
if (!$pdo) {
    echo "❌ No se pudo conectar a la base de datos\n";
    exit(1);
}

// Ejecutar verificaciones
$table_results = verifyTables($pdo);
$file_results = verifyFiles($dirs_config);
$integrity_results = verifyDataIntegrity($pdo);
$performance_results = verifyPerformance($pdo);

// Generar reporte final
generateFinalReport($table_results, $file_results, $integrity_results, $performance_results);

echo "\n✅ VERIFICACIÓN COMPLETADA\n";

?>
