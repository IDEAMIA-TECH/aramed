<?php
/**
 * ========================================
 * VERIFICACIÓN DE DOCUMENTOS DEL CATÁLOGO
 * ========================================
 * 
 * Script para verificar el estado de los documentos
 * en la tabla catalogo_producto_documentos
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/functions.php';

echo "<h2>🔍 VERIFICACIÓN DE DOCUMENTOS DEL CATÁLOGO</h2><hr>";

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }
    echo "✅ <strong>Conexión a la base de datos establecida</strong><br><br>";

    // Verificar que la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'catalogo_producto_documentos'");
    if (!$stmt->fetch()) {
        throw new Exception("La tabla 'catalogo_producto_documentos' no existe.");
    }
    echo "✅ <strong>Tabla 'catalogo_producto_documentos' encontrada</strong><br><br>";

    // Estadísticas generales
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_producto_documentos");
    $total_documentos = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(DISTINCT producto_id) as total FROM catalogo_producto_documentos");
    $productos_con_documentos = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos");
    $total_productos = $stmt->fetchColumn();
    
    echo "<h3>📊 ESTADÍSTICAS GENERALES</h3>";
    echo "<strong>Total documentos:</strong> $total_documentos<br>";
    echo "<strong>Productos con documentos:</strong> $productos_con_documentos<br>";
    echo "<strong>Total productos:</strong> $total_productos<br>";
    echo "<strong>Porcentaje de productos con documentos:</strong> " . round(($productos_con_documentos / $total_productos) * 100, 2) . "%<br><br>";

    // Estadísticas por tipo
    echo "<h3>📋 DOCUMENTOS POR TIPO</h3>";
    $stmt = $pdo->query("
        SELECT tipo, COUNT(*) as cantidad 
        FROM catalogo_producto_documentos 
        GROUP BY tipo 
        ORDER BY cantidad DESC
    ");
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tipos as $tipo) {
        echo "• <strong>" . ucfirst($tipo['tipo']) . ":</strong> {$tipo['cantidad']} documentos<br>";
    }
    echo "<br>";

    // Estadísticas por formato
    echo "<h3>📄 DOCUMENTOS POR FORMATO</h3>";
    $stmt = $pdo->query("
        SELECT formato, COUNT(*) as cantidad 
        FROM catalogo_producto_documentos 
        GROUP BY formato 
        ORDER BY cantidad DESC
    ");
    $formatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($formatos as $formato) {
        echo "• <strong>" . $formato['formato'] . ":</strong> {$formato['cantidad']} documentos<br>";
    }
    echo "<br>";

    // Verificar archivos físicos
    echo "<h3>🔍 VERIFICACIÓN DE ARCHIVOS FÍSICOS</h3>";
    $dest_dir = __DIR__ . '/assets/documents/catalogo/';
    
    if (!is_dir($dest_dir)) {
        echo "❌ <strong>Directorio de documentos no existe:</strong> $dest_dir<br>";
    } else {
        $archivos_fisicos = glob($dest_dir . '*.pdf');
        echo "✅ <strong>Directorio de documentos encontrado:</strong> $dest_dir<br>";
        echo "<strong>Archivos PDF físicos:</strong> " . count($archivos_fisicos) . "<br><br>";
        
        // Verificar que los archivos de la BD existen físicamente
        $stmt = $pdo->query("SELECT archivo_url FROM catalogo_producto_documentos");
        $documentos_bd = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $archivos_existentes = 0;
        $archivos_faltantes = 0;
        
        foreach ($documentos_bd as $archivo_url) {
            $archivo_path = __DIR__ . $archivo_url;
            if (file_exists($archivo_path)) {
                $archivos_existentes++;
            } else {
                $archivos_faltantes++;
                echo "⚠️ <strong>Archivo faltante:</strong> $archivo_url<br>";
            }
        }
        
        echo "<br><strong>Archivos existentes:</strong> $archivos_existentes<br>";
        echo "<strong>Archivos faltantes:</strong> $archivos_faltantes<br><br>";
    }

    // Productos con más documentos
    echo "<h3>🏆 PRODUCTOS CON MÁS DOCUMENTOS</h3>";
    $stmt = $pdo->query("
        SELECT p.nombre, p.codigo, COUNT(cpd.id) as total_docs
        FROM catalogo_productos p
        LEFT JOIN catalogo_producto_documentos cpd ON p.id = cpd.producto_id
        GROUP BY p.id, p.nombre, p.codigo
        HAVING total_docs > 0
        ORDER BY total_docs DESC
        LIMIT 10
    ");
    $productos_top = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($productos_top as $producto) {
        echo "• <strong>" . esc($producto['nombre']) . "</strong> ({$producto['codigo']}): {$producto['total_docs']} documentos<br>";
    }
    echo "<br>";

    // Productos sin documentos
    echo "<h3>❌ PRODUCTOS SIN DOCUMENTOS</h3>";
    $stmt = $pdo->query("
        SELECT p.nombre, p.codigo
        FROM catalogo_productos p
        LEFT JOIN catalogo_producto_documentos cpd ON p.id = cpd.producto_id
        WHERE cpd.id IS NULL
        ORDER BY p.nombre
        LIMIT 20
    ");
    $productos_sin_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($productos_sin_docs)) {
        echo "✅ <strong>Todos los productos tienen documentos</strong><br>";
    } else {
        echo "<strong>Primeros 20 productos sin documentos:</strong><br>";
        foreach ($productos_sin_docs as $producto) {
            echo "• " . esc($producto['nombre']) . " ({$producto['codigo']})<br>";
        }
    }
    echo "<br>";

    // Ejemplos de documentos
    echo "<h3>📋 EJEMPLOS DE DOCUMENTOS</h3>";
    $stmt = $pdo->query("
        SELECT p.nombre as producto, cpd.nombre as documento, cpd.archivo_url, cpd.tipo, cpd.formato, cpd.tamaño
        FROM catalogo_producto_documentos cpd
        JOIN catalogo_productos p ON cpd.producto_id = p.id
        ORDER BY cpd.id DESC
        LIMIT 10
    ");
    $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($ejemplos as $ejemplo) {
        $tamaño_mb = round($ejemplo['tamaño'] / 1024 / 1024, 2);
        echo "• <strong>" . esc($ejemplo['producto']) . "</strong><br>";
        echo "&nbsp;&nbsp;📄 " . esc($ejemplo['documento']) . " ({$ejemplo['tipo']})<br>";
        echo "&nbsp;&nbsp;🔗 " . $ejemplo['archivo_url'] . "<br>";
        echo "&nbsp;&nbsp;📊 {$ejemplo['formato']} - {$tamaño_mb} MB<br><br>";
    }

} catch (Exception $e) {
    echo "❌ <strong>Error durante la verificación:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Esta verificación te ayuda a entender el estado actual de los documentos en el catálogo.</p>";
?>
