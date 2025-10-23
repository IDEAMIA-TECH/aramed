<?php
/**
 * ========================================
 * MIGRACIÓN MANUAL DE DOCUMENTOS PDF
 * ========================================
 * 
 * Script para migrar documentos PDF con mapeo manual
 * de archivos a productos específicos
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

echo "<h2>🔄 MIGRACIÓN MANUAL DE DOCUMENTOS PDF</h2><hr>";

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

    // Obtener productos del catálogo
    $stmt = $pdo->query("SELECT id, codigo, nombre FROM catalogo_productos ORDER BY id LIMIT 20");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 <strong>Primeros 20 productos disponibles:</strong><br>";
    foreach ($productos as $producto) {
        echo "&nbsp;&nbsp;• ID: {$producto['id']} - Código: {$producto['codigo']} - Nombre: " . esc($producto['nombre']) . "<br>";
    }
    echo "<br>";

    // Directorio de documentos PDF
    $docs_dir = __DIR__ . '/../DOCS/productos-pdf/';
    $dest_dir = __DIR__ . '/assets/documents/catalogo/';
    
    // Crear directorio de destino si no existe
    if (!is_dir($dest_dir)) {
        mkdir($dest_dir, 0755, true);
        echo "✅ <strong>Directorio de destino creado: $dest_dir</strong><br><br>";
    }

    // Obtener lista de archivos PDF (primeros 20 para prueba)
    $pdf_files = glob($docs_dir . '*.pdf');
    $pdf_files = array_slice($pdf_files, 0, 20); // Solo primeros 20 para prueba
    
    echo "📁 <strong>Procesando primeros 20 archivos PDF:</strong><br><br>";

    $documentos_migrados = 0;
    $errores = 0;

    // Mapeo manual: asignar cada PDF a un producto secuencialmente
    foreach ($pdf_files as $index => $pdf_file) {
        $filename = basename($pdf_file);
        
        // Asignar producto por índice (circular)
        $producto_index = $index % count($productos);
        $producto = $productos[$producto_index];
        
        echo "📄 <strong>Procesando:</strong> $filename<br>";
        echo "&nbsp;&nbsp;🔗 <strong>Asignado a:</strong> {$producto['nombre']} (ID: {$producto['id']})<br>";
        
        // Copiar archivo al directorio de destino
        $dest_file = $dest_dir . $filename;
        if (copy($pdf_file, $dest_file)) {
            echo "&nbsp;&nbsp;✅ <strong>Archivo copiado</strong><br>";
        } else {
            echo "&nbsp;&nbsp;❌ <strong>Error al copiar archivo</strong><br>";
            $errores++;
            continue;
        }
        
        // Obtener información del archivo
        $file_size = filesize($dest_file);
        $file_extension = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Determinar tipo de documento
        $tipo = 'ficha_tecnica';
        $nombre_documento = "Ficha Técnica - " . $producto['nombre'];
        
        if (stripos($filename, 'manual') !== false) {
            $tipo = 'manual';
            $nombre_documento = "Manual - " . $producto['nombre'];
        } elseif (stripos($filename, 'brochure') !== false || stripos($filename, 'catalogo') !== false) {
            $tipo = 'brochure';
            $nombre_documento = "Brochure - " . $producto['nombre'];
        } elseif (stripos($filename, 'certificado') !== false) {
            $tipo = 'certificado';
            $nombre_documento = "Certificado - " . $producto['nombre'];
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
                $producto['id'],
                $nombre_documento,
                $archivo_url,
                $tipo,
                $file_size,
                $file_extension,
                'es',
                $orden
            ]);
            
            echo "&nbsp;&nbsp;✅ <strong>Documento insertado en BD</strong><br>";
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
    echo "<strong>Total archivos procesados:</strong> " . count($pdf_files) . "<br><br>";

    if ($documentos_migrados > 0) {
        echo "✅ <strong>Migración completada exitosamente</strong><br><br>";
        
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
    }

} catch (Exception $e) {
    echo "❌ <strong>Error durante la migración:</strong> " . $e->getMessage() . "<br><br>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Este script asigna documentos a productos de forma secuencial. Para un mapeo más preciso, edita el script o usa la interfaz de administración.</p>";
?>
