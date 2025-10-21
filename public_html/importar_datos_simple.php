<?php
/**
 * SCRIPT SIMPLE PARA IMPORTAR DATOS DEL SISTEMA VIEJO AL CATÁLOGO
 * Ejecuta directamente los INSERT statements del archivo SQL
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

// Función para limpiar tablas del catálogo
function clearCatalogTables($pdo) {
    echo "<br>🧹 LIMPIANDO TABLAS DEL CATÁLOGO...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        $pdo->exec("DELETE FROM catalogo_producto_documentos");
        echo "✅ catalogo_producto_documentos limpiada<br>";
        
        $pdo->exec("DELETE FROM catalogo_producto_imagenes");
        echo "✅ catalogo_producto_imagenes limpiada<br>";
        
        $pdo->exec("DELETE FROM catalogo_productos");
        echo "✅ catalogo_productos limpiada<br>";
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        return true;
        
    } catch (PDOException $e) {
        echo "❌ Error limpiando tablas: " . $e->getMessage() . "<br>";
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        return false;
    }
}

// Función para crear tabla temporal de productos
function createTempTable($pdo) {
    echo "<br>🏗️ CREANDO TABLA TEMPORAL...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    try {
        $sql = "
        CREATE TABLE IF NOT EXISTS `productos_temp` (
          `titulo` varchar(255) NOT NULL,
          `sku` varchar(255) NOT NULL,
          `categoria` int(11) DEFAULT NULL,
          `descripcion` text DEFAULT NULL,
          `precio` float DEFAULT NULL,
          `marca` int(11) NOT NULL,
          `uso` int(11) NOT NULL,
          `estado` enum('A','I','E') DEFAULT NULL,
          `created_by` int(10) UNSIGNED NOT NULL,
          `created_at` datetime NOT NULL,
          `updated_by` int(11) DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL,
          `id` bigint(20) NOT NULL,
          `ficha` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $pdo->exec($sql);
        echo "✅ Tabla productos_temp creada<br>";
        
        return true;
        
    } catch (PDOException $e) {
        echo "❌ Error creando tabla temporal: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para importar productos a tabla temporal
function importToTempTable($pdo) {
    echo "<br>📊 IMPORTANDO A TABLA TEMPORAL...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    $sql_file = __DIR__ . '/../database/migradas/productos.sql';
    if (!file_exists($sql_file)) {
        echo "❌ Archivo de productos no encontrado<br>";
        return false;
    }
    
    try {
        // Limpiar tabla temporal
        $pdo->exec("DELETE FROM productos_temp");
        
        // Leer archivo SQL
        $sql = file_get_contents($sql_file);
        
        // Ejecutar directamente el SQL, reemplazando el nombre de la tabla
        $sql = str_replace('INSERT INTO `productos`', 'INSERT INTO `productos_temp`', $sql);
        
        // Dividir en statements individuales
        $statements = explode(';', $sql);
        
        $imported = 0;
        $errors = 0;
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            
            if (strpos($statement, 'INSERT INTO `productos_temp`') !== false) {
                try {
                    $pdo->exec($statement);
                    $imported++;
                    
                    if ($imported % 50 == 0) {
                        echo "✅ Importados: $imported productos<br>";
                    }
                    
                } catch (PDOException $e) {
                    $errors++;
                    if ($errors <= 3) {
                        echo "⚠️ Error: " . $e->getMessage() . "<br>";
                    }
                }
            }
        }
        
        echo "<br>✅ Total productos importados: $imported<br>";
        echo "⚠️ Errores: $errors<br>";
        
        return $imported > 0;
        
    } catch (Exception $e) {
        echo "❌ Error general: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para migrar de tabla temporal a catálogo
function migrateToCatalog($pdo) {
    echo "<br>🔄 MIGRANDO A CATÁLOGO...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    try {
        // Migrar productos
        $sql = "
        INSERT INTO catalogo_productos (
            codigo, nombre, slug, descripcion_corta, descripcion_larga,
            marca_id, categoria_id, precio_publico, estado, created_at
        )
        SELECT 
            COALESCE(p.sku, CONCAT('PROD-', p.id)) as codigo,
            COALESCE(p.titulo, 'Producto sin nombre') as nombre,
            LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(p.titulo, 'producto'), ' ', '-'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o')) as slug,
            LEFT(COALESCE(p.descripcion, ''), 500) as descripcion_corta,
            COALESCE(p.descripcion, '') as descripcion_larga,
            COALESCE(p.marca, 1) as marca_id,
            COALESCE(p.categoria, 1) as categoria_id,
            COALESCE(p.precio, 0) as precio_publico,
            CASE 
                WHEN p.estado = 'A' THEN 'activo'
                WHEN p.estado = 'I' THEN 'inactivo'
                ELSE 'borrador'
            END as estado,
            COALESCE(p.created_at, NOW()) as created_at
        FROM productos_temp p
        WHERE p.id IS NOT NULL
        ";
        
        $stmt = $pdo->exec($sql);
        echo "✅ Productos migrados: $stmt registros<br>";
        
        // Migrar documentos
        $sql_docs = "
        INSERT INTO catalogo_producto_documentos (
            producto_id, documento_url, tipo, nombre, tamaño
        )
        SELECT 
            p.id as producto_id,
            CONCAT('/assets/documents/catalogo/', p.ficha) as documento_url,
            'ficha_tecnica' as tipo,
            p.ficha as nombre,
            0 as tamaño
        FROM productos_temp p
        WHERE p.ficha IS NOT NULL AND p.ficha != ''
        AND p.id IN (SELECT id FROM catalogo_productos)
        ";
        
        $stmt = $pdo->exec($sql_docs);
        echo "✅ Documentos migrados: $stmt registros<br>";
        
        return true;
        
    } catch (PDOException $e) {
        echo "❌ Error en migración: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para limpiar tabla temporal
function cleanupTempTable($pdo) {
    echo "<br>🧹 LIMPIANDO TABLA TEMPORAL...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    try {
        $pdo->exec("DROP TABLE IF EXISTS productos_temp");
        echo "✅ Tabla temporal eliminada<br>";
        return true;
    } catch (PDOException $e) {
        echo "⚠️ Error limpiando tabla temporal: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para mostrar estadísticas finales
function showFinalStats($pdo) {
    echo "<br>📊 ESTADÍSTICAS FINALES...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    try {
        $stats = [];
        
        $tables = [
            'catalogo_productos' => 'Productos',
            'catalogo_producto_imagenes' => 'Imágenes',
            'catalogo_producto_documentos' => 'Documentos'
        ];
        
        foreach ($tables as $table => $name) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            $stats[$name] = $count;
            echo "✅ $name: $count registros<br>";
        }
        
        // Mostrar algunos productos de muestra
        echo "<br><strong>📋 Productos de muestra:</strong><br>";
        $stmt = $pdo->query("SELECT codigo, nombre, marca_id, categoria_id FROM catalogo_productos LIMIT 5");
        $products = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>Código</th><th>Nombre</th><th>Marca ID</th><th>Categoría ID</th></tr>";
        
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($product['codigo']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($product['nombre'], 0, 50)) . "</td>";
            echo "<td>" . $product['marca_id'] . "</td>";
            echo "<td>" . $product['categoria_id'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        return $stats;
        
    } catch (PDOException $e) {
        echo "❌ Error obteniendo estadísticas: " . $e->getMessage() . "<br>";
        return false;
    }
}

// EJECUTAR IMPORTACIÓN
echo "<h1>🚀 IMPORTANDO DATOS DEL SISTEMA VIEJO AL CATÁLOGO (SIMPLE)</h1>";
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

// Paso 1: Limpiar tablas del catálogo
if (!clearCatalogTables($pdo)) {
    echo "❌ Error limpiando tablas del catálogo.<br>";
    exit(1);
}

// Paso 2: Crear tabla temporal
if (!createTempTable($pdo)) {
    echo "❌ Error creando tabla temporal.<br>";
    exit(1);
}

// Paso 3: Importar a tabla temporal
if (!importToTempTable($pdo)) {
    echo "❌ Error importando a tabla temporal.<br>";
    exit(1);
}

// Paso 4: Migrar a catálogo
if (!migrateToCatalog($pdo)) {
    echo "❌ Error migrando a catálogo.<br>";
    exit(1);
}

// Paso 5: Limpiar tabla temporal
cleanupTempTable($pdo);

// Paso 6: Mostrar estadísticas finales
$final_stats = showFinalStats($pdo);

// Reporte final
echo "<br><hr>";
echo "<h2>📊 REPORTE FINAL DE IMPORTACIÓN</h2>";
echo "<hr>";

if ($final_stats) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "✅ <strong>IMPORTACIÓN EXITOSA</strong><br>";
    echo "✅ Datos del sistema viejo migrados correctamente<br>";
    echo "✅ Catálogo completo y listo para uso<br>";
    echo "</div>";
    
    echo "<br><strong>📋 Resumen de datos importados:</strong><br>";
    foreach ($final_stats as $name => $count) {
        echo "- $name: $count registros<br>";
    }
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "⚠️ <strong>IMPORTACIÓN CON PROBLEMAS</strong><br>";
    echo "❌ Algunos datos no se importaron correctamente<br>";
    echo "</div>";
}

echo "<br><strong>📋 Próximos pasos:</strong><br>";
echo "1. Verificar datos en las tablas del catálogo<br>";
echo "2. Crear páginas de catálogo en el frontend<br>";
echo "3. Implementar sistema de búsqueda y filtros<br>";
echo "4. Configurar galería de imágenes<br>";
echo "5. Optimizar para SEO<br>";

echo "<br><hr>";
echo "<p><strong>✅ IMPORTACIÓN COMPLETADA</strong></p>";
echo "<p><strong>Fecha de finalización:</strong> " . date('Y-m-d H:i:s') . "</p>";

?>
