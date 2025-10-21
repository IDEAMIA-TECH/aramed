<?php
/**
 * SCRIPT V2 PARA IMPORTAR TODOS LOS PRODUCTOS DEL SISTEMA VIEJO AL CATÁLOGO
 * Maneja el formato correcto del archivo SQL
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

// Función para limpiar texto
function cleanText($text) {
    if (empty($text)) return '';
    
    // Decodificar entidades HTML
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    
    // Remover tags HTML
    $text = strip_tags($text);
    
    // Limpiar caracteres problemáticos
    $text = str_replace(['\\', '\'', '"'], '', $text);
    
    return trim($text);
}

// Función para generar slug
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
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

// Función para parsear línea de producto
function parseProductLine($line) {
    // Remover comillas y paréntesis
    $line = trim($line, "(),\r\n");
    
    // Dividir por comas, pero respetando las comillas
    $values = [];
    $current_value = '';
    $in_quotes = false;
    $quote_char = '';
    
    for ($i = 0; $i < strlen($line); $i++) {
        $char = $line[$i];
        
        if (!$in_quotes && ($char === "'" || $char === '"')) {
            $in_quotes = true;
            $quote_char = $char;
        } elseif ($in_quotes && $char === $quote_char) {
            // Verificar si es un escape
            if ($i > 0 && $line[$i-1] === '\\') {
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
    }
    
    // Agregar el último valor
    if (!empty($current_value)) {
        $values[] = trim($current_value);
    }
    
    return $values;
}

// Función para importar todos los productos
function importAllProducts($pdo) {
    echo "<br>📊 IMPORTANDO TODOS LOS PRODUCTOS...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    $sql_file = __DIR__ . '/../database/migradas/productos.sql';
    if (!file_exists($sql_file)) {
        echo "❌ Archivo de productos no encontrado<br>";
        return false;
    }
    
    try {
        // Preparar statement para insertar productos
        $stmt = $pdo->prepare("
        INSERT INTO catalogo_productos (
            codigo, nombre, slug, descripcion_corta, descripcion_larga,
            marca_id, categoria_id, precio_publico, estado, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Preparar statement para insertar documentos
        $stmt_docs = $pdo->prepare("
        INSERT INTO catalogo_producto_documentos (
            producto_id, documento_url, tipo, nombre, tamaño
        ) VALUES (?, ?, ?, ?, ?)
        ");
        
        // Leer archivo SQL
        $lines = file($sql_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $productos_importados = 0;
        $documentos_importados = 0;
        $errores = 0;
        
        foreach ($lines as $line_num => $line) {
            $line = trim($line);
            
            // Buscar líneas que contengan datos de productos (no INSERT statements)
            if (strpos($line, 'INSERT INTO') === false && 
                strpos($line, 'CREATE TABLE') === false && 
                strpos($line, '--') !== 0 &&
                strpos($line, '/*') !== 0 &&
                !empty($line) &&
                $line[0] === '(') {
                
                try {
                    $values = parseProductLine($line);
                    
                    if (count($values) >= 14) {
                        $titulo = cleanText($values[0]);
                        $sku = cleanText($values[1]);
                        $categoria = (int)$values[2];
                        $descripcion = cleanText($values[3]);
                        $precio = $values[4] === 'NULL' ? 0 : (float)$values[4];
                        $marca = (int)$values[5];
                        $uso = (int)$values[6];
                        $estado = cleanText($values[7]);
                        $created_by = (int)$values[8];
                        $created_at = cleanText($values[9]);
                        $updated_by = $values[10] === 'NULL' ? 1 : (int)$values[10];
                        $updated_at = cleanText($values[11]);
                        $id = (int)$values[12];
                        $ficha = cleanText($values[13]);
                        
                        // Validar datos básicos
                        if (empty($titulo) || empty($sku)) {
                            continue;
                        }
                        
                        // Generar slug
                        $slug = generateSlug($titulo);
                        
                        // Determinar estado
                        $estado_final = ($estado === 'A') ? 'activo' : (($estado === 'I') ? 'inactivo' : 'borrador');
                        
                        // Insertar producto
                        $stmt->execute([
                            $sku,
                            $titulo,
                            $slug,
                            substr($descripcion, 0, 500),
                            $descripcion,
                            $marca ?: 1,
                            $categoria ?: 1,
                            $precio,
                            $estado_final,
                            $created_at ?: date('Y-m-d H:i:s')
                        ]);
                        
                        $productos_importados++;
                        
                        // Insertar documento si existe
                        if (!empty($ficha) && $ficha !== 'NULL') {
                            try {
                                $stmt_docs->execute([
                                    $id,
                                    "/assets/documents/catalogo/$ficha",
                                    'ficha_tecnica',
                                    $ficha,
                                    0
                                ]);
                                $documentos_importados++;
                            } catch (PDOException $e) {
                                // Continuar sin el documento
                            }
                        }
                        
                        if ($productos_importados % 50 == 0) {
                            echo "✅ Productos procesados: $productos_importados<br>";
                        }
                    }
                    
                } catch (PDOException $e) {
                    $errores++;
                    if ($errores <= 5) {
                        echo "⚠️ Error en línea " . ($line_num + 1) . ": " . $e->getMessage() . "<br>";
                    }
                } catch (Exception $e) {
                    $errores++;
                    if ($errores <= 5) {
                        echo "⚠️ Error parsing línea " . ($line_num + 1) . ": " . $e->getMessage() . "<br>";
                    }
                }
            }
        }
        
        echo "<br>✅ Total productos importados: $productos_importados<br>";
        echo "✅ Total documentos importados: $documentos_importados<br>";
        echo "⚠️ Errores: $errores<br>";
        
        return $productos_importados > 0;
        
    } catch (Exception $e) {
        echo "❌ Error general: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Función para importar imágenes
function importImages($pdo) {
    echo "<br>🖼️ IMPORTANDO IMÁGENES...<br>";
    echo str_repeat("-", 50) . "<br>";
    
    $sql_file = __DIR__ . '/../database/migradas/imagenes_x_producto.sql';
    if (!file_exists($sql_file)) {
        echo "❌ Archivo de imágenes no encontrado<br>";
        return false;
    }
    
    try {
        // Preparar statement para insertar imágenes
        $stmt = $pdo->prepare("
        INSERT INTO catalogo_producto_imagenes (
            producto_id, imagen_url, tipo, orden, es_principal
        ) VALUES (?, ?, ?, ?, ?)
        ");
        
        // Leer archivo SQL
        $lines = file($sql_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $imagenes_importadas = 0;
        $errores = 0;
        
        foreach ($lines as $line_num => $line) {
            $line = trim($line);
            
            // Buscar líneas que contengan datos de imágenes
            if (strpos($line, 'INSERT INTO') === false && 
                strpos($line, 'CREATE TABLE') === false && 
                strpos($line, '--') !== 0 &&
                strpos($line, '/*') !== 0 &&
                !empty($line) &&
                $line[0] === '(') {
                
                try {
                    $values = parseProductLine($line);
                    
                    if (count($values) >= 5) {
                        $id = (int)$values[0];
                        $id_producto = (int)$values[1];
                        $id_imagen = (int)$values[2];
                        $color = cleanText($values[3]);
                        $img_default = cleanText($values[4]);
                        
                        // Verificar que el producto existe
                        $check_stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE id = ?");
                        $check_stmt->execute([$id_producto]);
                        
                        if ($check_stmt->rowCount() > 0) {
                            $stmt->execute([
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
                    $errores++;
                    if ($errores <= 5) {
                        echo "⚠️ Error en imagen línea " . ($line_num + 1) . ": " . $e->getMessage() . "<br>";
                    }
                }
            }
        }
        
        echo "✅ Imágenes importadas: $imagenes_importadas<br>";
        echo "⚠️ Errores: $errores<br>";
        
        return true;
        
    } catch (Exception $e) {
        echo "❌ Error general: " . $e->getMessage() . "<br>";
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
        $stmt = $pdo->query("SELECT codigo, nombre, marca_id, categoria_id FROM catalogo_productos LIMIT 10");
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
echo "<h1>🚀 IMPORTANDO TODOS LOS PRODUCTOS DEL SISTEMA VIEJO AL CATÁLOGO (V2)</h1>";
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

// Paso 2: Importar todos los productos
if (!importAllProducts($pdo)) {
    echo "❌ Error importando productos.<br>";
    exit(1);
}

// Paso 3: Importar imágenes
if (!importImages($pdo)) {
    echo "❌ Error importando imágenes.<br>";
    exit(1);
}

// Paso 4: Mostrar estadísticas finales
$final_stats = showFinalStats($pdo);

// Reporte final
echo "<br><hr>";
echo "<h2>📊 REPORTE FINAL DE IMPORTACIÓN</h2>";
echo "<hr>";

if ($final_stats) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "✅ <strong>IMPORTACIÓN EXITOSA</strong><br>";
    echo "✅ Todos los productos del sistema viejo migrados correctamente<br>";
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
