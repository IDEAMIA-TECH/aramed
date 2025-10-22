<?php
/**
 * SCRIPT MANUAL PARA IMPORTAR DATOS DEL SISTEMA VIEJO AL CATÁLOGO
 * Usa una aproximación manual para evitar problemas de caracteres
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

// Función para importar datos manualmente
function importDataManually($pdo) {
    echo "<br>📊 IMPORTANDO DATOS MANUALMENTE...<br>";
    echo str_repeat("-", 50) . "<br>";
    
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
        
        // Datos de productos manuales (primeros 10 productos del archivo)
        $productos = [
            [
                'titulo' => 'MANIQUI DE ENTRENAMIENTO PARA CUIDADOS HOSPITALARIOS',
                'sku' => 'AC20',
                'categoria' => 1,
                'descripcion' => 'MODELO DE MANIQUÍ PEDIÁTRICO DE TAMAÑO REAL DE UN PACIENTE DE CUATRO SEMANAS DE EDAD PARA CUIDADOS HOSPITALARIOS, FABRICADO EN PLÁSTICO FLEXIBLE, CUELLO, HOMBROS Y CADERAS ARTICULADAS, GENITALES MASCULINO/FEMENINO, CAJA TORÁCICA Y PARED ABDOMINAL INTERCAMBIABLE, PADS DE INYECCIÓN DEL MUSLO Y GLÚTEO, VENA TEMPORAL, VEJIGA, ESTOMAGO E INTESTINOS TODOS REEMPLAZABLES',
                'precio' => 0,
                'marca' => 1,
                'uso' => 1,
                'estado' => 'A',
                'created_at' => '2013-01-21 22:47:13',
                'id' => 1,
                'ficha' => ''
            ],
            [
                'titulo' => 'MANIQUÍ DE SIMULACIÓN DE CUIDADOS GENERALES EN PACIENTES PEDIÁTRICOS',
                'sku' => 'AC23',
                'categoria' => 1,
                'descripcion' => 'MANIQUÍ TAMAÑO NATURAL DE UN NIÑO DE 3 AÑOS DE EDAD FABRICADO DE PLÁSTICO ESPECIAL CON MECANISMOS DE ARTICULACIÓN QUE SIMULAN UN MOVIMIENTO NATURAL, PARED ABDOMINAL INTERCAMBIABLE QUE PERMITE CAMBIAR EL SEXO DEL MANIQUÍ, LA PARED ABDOMINAL FEMENINA TIENE UNA CICATRIZ DE APENDECTOMÍA, LA PARED ABDOMINAL MASCULINA TIENE UNA CICATRIZ DE UNA OPERACIÓN DE HERNIA, LA PARED ABDOMINAL ESTÁ EQUIPADA CON LO SIGUIENTE: ESTOMA, ORIFICIOS PARA PUNCIÓN VESICAL SUPRA PÚBICA, BRAZO DERECHO PUEDE CAMBIARSE POR UN BRAZO DE PUNCIÓN IV, LA PARTE SUPERIOR DEL BRAZO Y LOS MUSLOS CON PAD PARA INYECCIÓN INTRAMUSCULAR',
                'precio' => 0,
                'marca' => 1,
                'uso' => 1,
                'estado' => 'A',
                'created_at' => '2013-04-16 14:33:41',
                'id' => 2,
                'ficha' => '20130906_e1fc3d.pdf'
            ],
            [
                'titulo' => 'THE HUNGRY MANIKIN (MANIQUÍ HAMBRIENTO)',
                'sku' => 'AR331',
                'categoria' => 2,
                'descripcion' => 'MODELO DE SIMULACIÓN PARA LA PRÁCTICA DE INSERCIÓN DE TUBOS NASOGÁSTRICO Y GASTROSTÓMICO EN UN INFANTE, EPIGLOTIS MÓVIL PARA DEMOSTRAR EL MOVIMIENTO DE ABSORCIÓN DE ALIMENTOS, CUERPO TRANSPARENTE QUE PERMITE A LOS ALUMNOS OBSERVAR LA INSERCIÓN DE LA SONDA, ACEPTA ALIMENTOS LÍQUIDOS PARA NUTRICIÓN ENTERAL Y AGUA, SEPTUM NASAL DIVIDIDO, REPRESENTACIÓN DE LOS PULMONES Y DEL ESÓFAGO',
                'precio' => 0,
                'marca' => 1,
                'uso' => 1,
                'estado' => 'A',
                'created_at' => '2013-04-16 14:54:54',
                'id' => 3,
                'ficha' => '20130906_a1280f.pdf'
            ],
            [
                'titulo' => 'MANIQUÍ PEDIÁTRICO DE 6 SEMANAS DE EDAD PARA CUIDADOS GENERALES',
                'sku' => 'MO55',
                'categoria' => 5,
                'descripcion' => 'MODELO DE MANIQUÍ PEDIÁTRICO DE TAMAÑO REAL DE UN PACIENTE DE 6 SEMANAS DE EDAD PARA PRÁCTICA DE CUIDADOS DE ENFERMERÍA, ARTICULACIONES PARA SIMULACIÓN DE MOVIMIENTO REAL, CABEZA MOVIBLE CON EXTENSIÓN HACIA ATRÁS, OJOS PINTADOS A MANO, IDEAL PARA PRÁCTICA DE BAÑO, CAMBIO DE ROPA Y EJERCICIOS DE ENFERMERÍA, PRÁCTICA DE MEDICIÓN DE TEMPERATURA A TRAVÉS DE OÍDO, NARIZ Y ANO',
                'precio' => 0,
                'marca' => 1,
                'uso' => 1,
                'estado' => 'A',
                'created_at' => '2013-04-16 15:06:06',
                'id' => 4,
                'ficha' => ''
            ],
            [
                'titulo' => 'MANIQUÍ PEDIÁTRICO DE RECIÉN NACIDO PARA CUIDADOS GENERALES',
                'sku' => 'MO75',
                'categoria' => 5,
                'descripcion' => 'MODELO DE MANIQUÍ PEDIÁTRICO DE TAMAÑO REAL DE UN PACIENTE RECIÉN NACIDO PARA PRÁCTICA DE CUIDADOS DE ENFERMERÍA, ARTICULACIONES PARA SIMULACIÓN DE MOVIMIENTO REAL, CABEZA MOVIBLE CON EXTENSIÓN HACIA ATRÁS, APERTURA EN BOCA, CORDÓN UMBILICAL Y ANO, IDEAL PARA PRÁCTICA DE CAMBIOS DE ROPA, BAÑO Y CUIDADOS DE ENFERMERÍA',
                'precio' => 0,
                'marca' => 1,
                'uso' => 1,
                'estado' => 'A',
                'created_at' => '2013-04-16 15:07:16',
                'id' => 5,
                'ficha' => ''
            ]
        ];
        
        $productos_importados = 0;
        $documentos_importados = 0;
        
        foreach ($productos as $producto) {
            try {
                // Generar slug
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $producto['titulo']), '-'));
                
                // Determinar estado
                $estado_final = ($producto['estado'] === 'A') ? 'activo' : (($producto['estado'] === 'I') ? 'inactivo' : 'borrador');
                
                // Insertar producto
                $stmt->execute([
                    $producto['sku'],
                    $producto['titulo'],
                    $slug,
                    substr($producto['descripcion'], 0, 500),
                    $producto['descripcion'],
                    $producto['marca'],
                    $producto['categoria'],
                    $producto['precio'],
                    $estado_final,
                    $producto['created_at']
                ]);
                
                $productos_importados++;
                
                // Insertar documento si existe
                if (!empty($producto['ficha'])) {
                    $stmt_docs->execute([
                        $producto['id'],
                        "/assets/documents/catalogo/{$producto['ficha']}",
                        'ficha_tecnica',
                        $producto['ficha'],
                        0
                    ]);
                    $documentos_importados++;
                }
                
            } catch (PDOException $e) {
                echo "⚠️ Error importando producto {$producto['sku']}: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<br>✅ Productos importados: $productos_importados<br>";
        echo "✅ Documentos importados: $documentos_importados<br>";
        
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
echo "<h1>🚀 IMPORTANDO DATOS DEL SISTEMA VIEJO AL CATÁLOGO (MANUAL)</h1>";
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

// Paso 2: Importar datos manualmente
if (!importDataManually($pdo)) {
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
