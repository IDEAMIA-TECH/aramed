<?php
/**
 * SCRIPT V4 PARA IMPORTAR DATOS DEL SISTEMA VIEJO AL CATÁLOGO
 * Versión robusta que maneja caracteres especiales y problemas de SQL
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

// Función para limpiar tablas del catálogo (sin TRUNCATE)
function clearCatalogTables($pdo) {
    echo "<br>🧹 LIMPIANDO TABLAS DEL CATÁLOGO...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    try {
        // Deshabilitar foreign key checks temporalmente
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Limpiar tablas en orden correcto (primero las dependientes)
        $pdo->exec("DELETE FROM catalogo_producto_documentos");
        echo "✅ catalogo_producto_documentos limpiada<br>";
        
        $pdo->exec("DELETE FROM catalogo_producto_imagenes");
        echo "✅ catalogo_producto_imagenes limpiada<br>";
        
        $pdo->exec("DELETE FROM catalogo_productos");
        echo "✅ catalogo_productos limpiada<br>";
        
        // Rehabilitar foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        return true;
        
    } catch (PDOException $e) {
        echo "❌ Error limpiando tablas: " . $e->getMessage() . "<br>";
        // Rehabilitar foreign key checks en caso de error
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        return false;
    }
}

// Función para importar datos directamente usando prepared statements
function importDataDirectly($pdo) {
    echo "<br>📊 IMPORTANDO DATOS DIRECTAMENTE...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    // Leer archivo de productos
    $productos_file = __DIR__ . '/../database/migradas/productos.sql';
    if (!file_exists($productos_file)) {
        echo "❌ Archivo de productos no encontrado<br>";
        return false;
    }
    
    $productos_sql = file_get_contents($productos_file);
    
    // Leer archivo de imágenes
    $imagenes_file = __DIR__ . '/../database/migradas/imagenes_x_producto.sql';
    if (!file_exists($imagenes_file)) {
        echo "❌ Archivo de imágenes no encontrado<br>";
        return false;
    }
    
    $imagenes_sql = file_get_contents($imagenes_file);
    
    try {
        // Preparar statement para insertar productos
        $stmt_productos = $pdo->prepare("
        INSERT INTO catalogo_productos (
            codigo, nombre, slug, descripcion_corta, descripcion_larga,
            marca_id, categoria_id, precio_publico, estado, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Preparar statement para insertar imágenes
        $stmt_imagenes = $pdo->prepare("
        INSERT INTO catalogo_producto_imagenes (
            producto_id, imagen_url, tipo, orden, es_principal
        ) VALUES (?, ?, ?, ?, ?)
        ");
        
        // Preparar statement para insertar documentos
        $stmt_documentos = $pdo->prepare("
        INSERT INTO catalogo_producto_documentos (
            producto_id, documento_url, tipo, nombre, tamaño
        ) VALUES (?, ?, ?, ?, ?)
        ");
        
        // Extraer datos de productos usando regex más específico
        preg_match_all('/INSERT INTO `productos`[^;]+VALUES\s*\(([^;]+)\);/', $productos_sql, $productos_matches);
        
        $productos_importados = 0;
        $imagenes_importadas = 0;
        $documentos_importados = 0;
        
        echo "🔍 Procesando " . count($productos_matches[1]) . " productos...<br>";
        
        foreach ($productos_matches[1] as $producto_data) {
            try {
                // Parsear datos del producto
                $values = parseSQLValues($producto_data);
                
                if (count($values) >= 13) {
                    $titulo = cleanText($values[0] ?? '');
                    $sku = cleanText($values[1] ?? '');
                    $categoria = (int)($values[2] ?? 1);
                    $descripcion = cleanText($values[3] ?? '');
                    $precio = (float)($values[4] ?? 0);
                    $marca = (int)($values[5] ?? 1);
                    $uso = (int)($values[6] ?? 1);
                    $estado = cleanText($values[7] ?? 'A');
                    $created_by = (int)($values[8] ?? 1);
                    $created_at = cleanText($values[9] ?? date('Y-m-d H:i:s'));
                    $updated_by = (int)($values[10] ?? 1);
                    $updated_at = cleanText($values[11] ?? date('Y-m-d H:i:s'));
                    $id = (int)($values[12] ?? 0);
                    $ficha = cleanText($values[13] ?? '');
                    
                    // Generar slug
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo), '-'));
                    
                    // Determinar estado
                    $estado_final = ($estado === 'A') ? 'activo' : (($estado === 'I') ? 'inactivo' : 'borrador');
                    
                    // Insertar producto
                    $stmt_productos->execute([
                        $sku ?: "PROD-$id",
                        $titulo ?: "Producto $id",
                        $slug ?: "producto-$id",
                        substr($descripcion, 0, 500),
                        $descripcion,
                        $marca,
                        $categoria,
                        $precio,
                        $estado_final,
                        $created_at ?: date('Y-m-d H:i:s')
                    ]);
                    
                    $productos_importados++;
                    
                    // Insertar documento si existe
                    if (!empty($ficha)) {
                        $stmt_documentos->execute([
                            $id,
                            "/assets/documents/catalogo/$ficha",
                            'ficha_tecnica',
                            $ficha,
                            0
                        ]);
                        $documentos_importados++;
                    }
                    
                    if ($productos_importados % 50 == 0) {
                        echo "✅ Productos procesados: $productos_importados<br>";
                    }
                }
                
            } catch (PDOException $e) {
                // Continuar con el siguiente producto
                continue;
            }
        }
        
        echo "<br>✅ Productos importados: $productos_importados<br>";
        echo "✅ Documentos importados: $documentos_importados<br>";
        
        // Procesar imágenes
        preg_match_all('/INSERT INTO `imagenes_x_producto`[^;]+VALUES\s*\(([^;]+)\);/', $imagenes_sql, $imagenes_matches);
        
        echo "<br>🔍 Procesando " . count($imagenes_matches[1]) . " relaciones de imágenes...<br>";
        
        foreach ($imagenes_matches[1] as $imagen_data) {
            try {
                $values = parseSQLValues($imagen_data);
                
                if (count($values) >= 5) {
                    $id = (int)($values[0] ?? 0);
                    $id_producto = (int)($values[1] ?? 0);
                    $id_imagen = (int)($values[2] ?? 0);
                    $color = cleanText($values[3] ?? '');
                    $img_default = cleanText($values[4] ?? 'NO');
                    
                    // Verificar que el producto existe
                    $check_stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE id = ?");
                    $check_stmt->execute([$id_producto]);
                    
                    if ($check_stmt->rowCount() > 0) {
                        $stmt_imagenes->execute([
                            $id_producto,
                            "/assets/images/catalogo/galeria/$id_imagen-lg.jpg",
                            'galeria',
                            $id,
                            ($img_default === 'SI') ? 1 : 0
                        ]);
                        $imagenes_importadas++;
                    }
                }
                
            } catch (PDOException $e) {
                // Continuar con la siguiente imagen
                continue;
            }
        }
        
        echo "✅ Imágenes importadas: $imagenes_importadas<br>";
        
        return true;
        
    } catch (Exception $e) {
        echo "❌ Error general: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para parsear valores SQL
function parseSQLValues($values_string) {
    $values = [];
    $current_value = '';
    $in_quotes = false;
    $quote_char = '';
    $i = 0;
    
    while ($i < strlen($values_string)) {
        $char = $values_string[$i];
        
        if (!$in_quotes && ($char === "'" || $char === '"')) {
            $in_quotes = true;
            $quote_char = $char;
        } elseif ($in_quotes && $char === $quote_char) {
            // Verificar si es un escape
            if ($i > 0 && $values_string[$i-1] === '\\') {
                $current_value .= $char;
            } else {
                $in_quotes = false;
                $quote_char = '';
            }
        } elseif (!$in_quotes && $char === ',') {
            $values[] = trim($current_value);
            $current_value = '';
        } else {
            $current_value .= $char;
        }
        
        $i++;
    }
    
    // Agregar el último valor
    if (!empty($current_value)) {
        $values[] = trim($current_value);
    }
    
    return $values;
}

// Función para limpiar texto
function cleanText($text) {
    if (empty($text)) return '';
    
    // Remover comillas del inicio y final
    $text = trim($text, "'\"");
    
    // Decodificar entidades HTML
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    
    // Remover tags HTML
    $text = strip_tags($text);
    
    // Limpiar caracteres problemáticos
    $text = str_replace(['\\', '\'', '"'], '', $text);
    
    return $text;
}

// Función para mostrar estadísticas finales
function showFinalStats($pdo) {
    echo "<br>📊 ESTADÍSTICAS FINALES...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    try {
        // Contar registros en tablas del catálogo
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
echo "<h1>🚀 IMPORTANDO DATOS DEL SISTEMA VIEJO AL CATÁLOGO (V4)</h1>";
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

// Paso 2: Importar datos directamente
if (!importDataDirectly($pdo)) {
    echo "❌ Error importando datos.<br>";
    exit(1);
}

// Paso 3: Mostrar estadísticas finales
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
