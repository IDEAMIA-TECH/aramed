<?php
/**
 * ========================================
 * MIGRACIÓN DE DOCUMENTOS PDF DEL CATÁLOGO
 * ========================================
 * 
 * Script para migrar documentos PDF desde DOCS/productos-pdf/
 * hacia la tabla catalogo_producto_documentos
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

echo "<h2>🔄 MIGRACIÓN DE DOCUMENTOS PDF DEL CATÁLOGO</h2><hr>";

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }
    echo "✅ <strong>Conexión a la base de datos establecida</strong><br><br>";

    // Verificar que la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'catalogo_producto_documentos'");
    if (!$stmt->fetch()) {
        throw new Exception("La tabla 'catalogo_producto_documentos' no existe. Ejecuta primero la migración de la estructura.");
    }
    echo "✅ <strong>Tabla 'catalogo_producto_documentos' encontrada</strong><br><br>";

    // Verificar que hay productos en la tabla
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos");
    $total_productos = $stmt->fetchColumn();
    
    if ($total_productos == 0) {
        throw new Exception("No hay productos en la tabla 'catalogo_productos'. Ejecuta primero la migración de productos.");
    }
    echo "✅ <strong>Se encontraron $total_productos productos en el catálogo</strong><br><br>";

    // Directorio de documentos PDF
    $docs_dir = __DIR__ . '/../DOCS/productos-pdf/';
    $dest_dir = __DIR__ . '/assets/documents/catalogo/';
    
    // Crear directorio de destino si no existe
    if (!is_dir($dest_dir)) {
        mkdir($dest_dir, 0755, true);
        echo "✅ <strong>Directorio de destino creado: $dest_dir</strong><br><br>";
    }

    // Obtener lista de archivos PDF
    $pdf_files = glob($docs_dir . '*.pdf');
    $total_pdfs = count($pdf_files);
    
    echo "📁 <strong>Se encontraron $total_pdfs archivos PDF en $docs_dir</strong><br><br>";

    if ($total_pdfs == 0) {
        echo "⚠️ <strong>No se encontraron archivos PDF para migrar</strong><br><br>";
        exit;
    }

    // Obtener productos del catálogo
    $stmt = $pdo->query("SELECT id, codigo, nombre FROM catalogo_productos ORDER BY id");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 <strong>Productos disponibles para mapear:</strong><br>";
    foreach ($productos as $producto) {
        echo "&nbsp;&nbsp;• ID: {$producto['id']} - Código: {$producto['codigo']} - Nombre: " . esc($producto['nombre']) . "<br>";
    }
    echo "<br>";

    // Mapeo de archivos PDF a productos
    // Estrategia: Intentar mapear por ID del producto
    $documentos_migrados = 0;
    $errores = 0;

    echo "🔄 <strong>Iniciando migración de documentos...</strong><br><br>";

    foreach ($pdf_files as $pdf_file) {
        $filename = basename($pdf_file);
        $file_id = pathinfo($filename, PATHINFO_FILENAME); // Obtener ID del nombre del archivo
        
        echo "📄 <strong>Procesando:</strong> $filename (ID: $file_id)<br>";
        
        // Buscar producto por ID
        $producto_encontrado = null;
        foreach ($productos as $producto) {
            if ($producto['id'] == $file_id) {
                $producto_encontrado = $producto;
                break;
            }
        }
        
        if (!$producto_encontrado) {
            echo "&nbsp;&nbsp;⚠️ <strong>No se encontró producto con ID $file_id</strong><br>";
            $errores++;
            continue;
        }
        
        echo "&nbsp;&nbsp;✅ <strong>Producto encontrado:</strong> {$producto_encontrado['nombre']}<br>";
        
        // Copiar archivo al directorio de destino
        $dest_file = $dest_dir . $filename;
        if (copy($pdf_file, $dest_file)) {
            echo "&nbsp;&nbsp;✅ <strong>Archivo copiado a:</strong> $dest_file<br>";
        } else {
            echo "&nbsp;&nbsp;❌ <strong>Error al copiar archivo</strong><br>";
            $errores++;
            continue;
        }
        
        // Obtener información del archivo
        $file_size = filesize($dest_file);
        $file_extension = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Determinar tipo de documento basado en el nombre
        $tipo = 'ficha_tecnica'; // Por defecto
        $nombre_documento = "Ficha Técnica - " . $producto_encontrado['nombre'];
        
        if (stripos($filename, 'manual') !== false) {
            $tipo = 'manual';
            $nombre_documento = "Manual - " . $producto_encontrado['nombre'];
        } elseif (stripos($filename, 'brochure') !== false || stripos($filename, 'catalogo') !== false) {
            $tipo = 'brochure';
            $nombre_documento = "Brochure - " . $producto_encontrado['nombre'];
        } elseif (stripos($filename, 'certificado') !== false) {
            $tipo = 'certificado';
            $nombre_documento = "Certificado - " . $producto_encontrado['nombre'];
        }
        
        // Insertar en la base de datos
        try {
            $stmt = $pdo->prepare("
                INSERT INTO catalogo_producto_documentos 
                (producto_id, nombre, archivo_url, tipo, tamaño, formato, idioma, orden) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $archivo_url = '/assets/documents/catalogo/' . $filename;
            $orden = $documentos_migrados + 1;
            
            $stmt->execute([
                $producto_encontrado['id'],
                $nombre_documento,
                $archivo_url,
                $tipo,
                $file_size,
                $file_extension,
                'es',
                $orden
            ]);
            
            echo "&nbsp;&nbsp;✅ <strong>Documento insertado en BD:</strong> $nombre_documento<br>";
            $documentos_migrados++;
            
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;❌ <strong>Error al insertar en BD:</strong> " . $e->getMessage() . "<br>";
            $errores++;
        }
        
        echo "<br>";
    }

    echo "<hr>";
    echo "<h3>📊 RESUMEN DE MIGRACIÓN</h3>";
    echo "<strong>Documentos migrados:</strong> $documentos_migrados<br>";
    echo "<strong>Errores:</strong> $errores<br>";
    echo "<strong>Total archivos procesados:</strong> $total_pdfs<br><br>";

    if ($documentos_migrados > 0) {
        echo "✅ <strong>Migración completada exitosamente</strong><br><br>";
        
        // Actualizar campo documentos en la tabla de productos
        echo "🔄 <strong>Actualizando campo 'documentos' en productos...</strong><br>";
        
        $stmt = $pdo->query("
            UPDATE catalogo_productos cp
            SET documentos = (
                SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'nombre', cpd.nombre,
                        'url', cpd.archivo_url,
                        'tipo', cpd.tipo,
                        'formato', cpd.formato,
                        'idioma', cpd.idioma,
                        'orden', cpd.orden
                    )
                )
                FROM catalogo_producto_documentos cpd 
                WHERE cpd.producto_id = cp.id
                ORDER BY cpd.orden
            )
            WHERE EXISTS (
                SELECT 1 FROM catalogo_producto_documentos cpd 
                WHERE cpd.producto_id = cp.id
            )
        ");
        
        $productos_actualizados = $stmt->rowCount();
        echo "✅ <strong>Campo 'documentos' actualizado en $productos_actualizados productos</strong><br><br>";
        
        // Mostrar algunos ejemplos
        echo "<h4>📋 EJEMPLOS DE DOCUMENTOS MIGRADOS:</h4>";
        $stmt = $pdo->query("
            SELECT p.nombre as producto, cpd.nombre as documento, cpd.archivo_url, cpd.tipo
            FROM catalogo_producto_documentos cpd
            JOIN catalogo_productos p ON cpd.producto_id = p.id
            ORDER BY cpd.id DESC
            LIMIT 10
        ");
        
        $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($ejemplos as $ejemplo) {
            echo "• <strong>" . esc($ejemplo['producto']) . "</strong> - " . esc($ejemplo['documento']) . " (" . $ejemplo['tipo'] . ")<br>";
        }
    } else {
        echo "❌ <strong>No se migraron documentos. Revisa los errores anteriores.</strong><br>";
    }

} catch (Exception $e) {
    echo "❌ <strong>Error durante la migración:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Los documentos PDF se han copiado a <code>/assets/documents/catalogo/</code> y se han registrado en la tabla <code>catalogo_producto_documentos</code>.</p>";
?>
