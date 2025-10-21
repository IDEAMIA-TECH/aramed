<?php
/**
 * SCRIPT DE MIGRACIÓN DE ARCHIVOS
 * Migra archivos físicos del sistema viejo al nuevo catálogo
 */

// Configuración
$config = [
    'source_dirs' => [
        'productos_cat' => '/Users/gorila/Desktop/CLONE/GIT/aramed/DOCS/productos-cat/',
        'productos_fotos' => '/Users/gorila/Desktop/CLONE/GIT/aramed/DOCS/productos-fotos/',
        'productos_pdf' => '/Users/gorila/Desktop/CLONE/GIT/aramed/DOCS/productos-pdf/'
    ],
    'dest_dirs' => [
        'productos' => '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/productos/',
        'documentos' => '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/documents/catalogo/',
        'galeria' => '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/galeria/'
    ],
    'create_dirs' => true,
    'copy_files' => true,
    'optimize_images' => true,
    'generate_webp' => true
];

// Función para crear directorios
function createDirectories($dirs) {
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "✅ Directorio creado: $dir\n";
        } else {
            echo "📁 Directorio existe: $dir\n";
        }
    }
}

// Función para optimizar imágenes
function optimizeImage($source, $dest, $quality = 85) {
    $info = getimagesize($source);
    if (!$info) return false;
    
    $width = $info[0];
    $height = $info[1];
    $type = $info[2];
    
    // Redimensionar si es muy grande
    $maxWidth = 1200;
    $maxHeight = 1200;
    
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }
    
    // Crear imagen
    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImg = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $sourceImg = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $sourceImg = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    // Redimensionar
    $destImg = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($destImg, $sourceImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Guardar
    $result = imagejpeg($destImg, $dest, $quality);
    
    // Limpiar memoria
    imagedestroy($sourceImg);
    imagedestroy($destImg);
    
    return $result;
}

// Función para generar WebP
function generateWebP($source, $dest, $quality = 85) {
    if (!function_exists('imagewebp')) {
        echo "⚠️  WebP no disponible en este servidor\n";
        return false;
    }
    
    $info = getimagesize($source);
    if (!$info) return false;
    
    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            $img = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $img = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $img = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    $result = imagewebp($img, $dest, $quality);
    imagedestroy($img);
    
    return $result;
}

// Función para migrar archivos
function migrateFiles($config) {
    $stats = [
        'productos_cat' => ['copied' => 0, 'optimized' => 0, 'webp' => 0],
        'productos_fotos' => ['copied' => 0, 'optimized' => 0, 'webp' => 0],
        'productos_pdf' => ['copied' => 0]
    ];
    
    // Migrar productos-cat (imágenes principales)
    echo "\n🖼️  MIGRANDO IMÁGENES DE PRODUCTOS-CAT...\n";
    $productosCatDir = $config['source_dirs']['productos_cat'];
    if (is_dir($productosCatDir)) {
        $files = glob($productosCatDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        foreach ($files as $file) {
            $filename = basename($file);
            $destFile = $config['dest_dirs']['productos'] . $filename;
            
            if ($config['copy_files']) {
                if ($config['optimize_images']) {
                    if (optimizeImage($file, $destFile)) {
                        $stats['productos_cat']['optimized']++;
                        echo "✅ Optimizada: $filename\n";
                    }
                } else {
                    if (copy($file, $destFile)) {
                        $stats['productos_cat']['copied']++;
                        echo "✅ Copiada: $filename\n";
                    }
                }
                
                // Generar WebP
                if ($config['generate_webp']) {
                    $webpFile = str_replace(['.jpg', '.jpeg', '.png', '.gif'], '.webp', $destFile);
                    if (generateWebP($file, $webpFile)) {
                        $stats['productos_cat']['webp']++;
                        echo "✅ WebP generado: " . basename($webpFile) . "\n";
                    }
                }
            }
        }
    }
    
    // Migrar productos-fotos (galería)
    echo "\n📸 MIGRANDO FOTOS DE PRODUCTOS...\n";
    $productosFotosDir = $config['source_dirs']['productos_fotos'];
    if (is_dir($productosFotosDir)) {
        $files = glob($productosFotosDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        foreach ($files as $file) {
            $filename = basename($file);
            $destFile = $config['dest_dirs']['galeria'] . $filename;
            
            if ($config['copy_files']) {
                if ($config['optimize_images']) {
                    if (optimizeImage($file, $destFile)) {
                        $stats['productos_fotos']['optimized']++;
                        echo "✅ Optimizada: $filename\n";
                    }
                } else {
                    if (copy($file, $destFile)) {
                        $stats['productos_fotos']['copied']++;
                        echo "✅ Copiada: $filename\n";
                    }
                }
                
                // Generar WebP
                if ($config['generate_webp']) {
                    $webpFile = str_replace(['.jpg', '.jpeg', '.png', '.gif'], '.webp', $destFile);
                    if (generateWebP($file, $webpFile)) {
                        $stats['productos_fotos']['webp']++;
                        echo "✅ WebP generado: " . basename($webpFile) . "\n";
                    }
                }
            }
        }
    }
    
    // Migrar productos-pdf (documentos)
    echo "\n📄 MIGRANDO DOCUMENTOS PDF...\n";
    $productosPdfDir = $config['source_dirs']['productos_pdf'];
    if (is_dir($productosPdfDir)) {
        $files = glob($productosPdfDir . '*.pdf');
        foreach ($files as $file) {
            $filename = basename($file);
            $destFile = $config['dest_dirs']['documentos'] . $filename;
            
            if ($config['copy_files']) {
                if (copy($file, $destFile)) {
                    $stats['productos_pdf']['copied']++;
                    echo "✅ Copiado: $filename\n";
                }
            }
        }
    }
    
    return $stats;
}

// Función para generar reporte
function generateReport($stats) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 REPORTE DE MIGRACIÓN\n";
    echo str_repeat("=", 60) . "\n";
    
    echo "\n🖼️  PRODUCTOS-CAT:\n";
    echo "   - Imágenes optimizadas: " . $stats['productos_cat']['optimized'] . "\n";
    echo "   - WebP generados: " . $stats['productos_cat']['webp'] . "\n";
    
    echo "\n📸 PRODUCTOS-FOTOS:\n";
    echo "   - Imágenes optimizadas: " . $stats['productos_fotos']['optimized'] . "\n";
    echo "   - WebP generados: " . $stats['productos_fotos']['webp'] . "\n";
    
    echo "\n📄 PRODUCTOS-PDF:\n";
    echo "   - Documentos copiados: " . $stats['productos_pdf']['copied'] . "\n";
    
    $total = $stats['productos_cat']['optimized'] + $stats['productos_fotos']['optimized'] + $stats['productos_pdf']['copied'];
    echo "\n🎯 TOTAL DE ARCHIVOS MIGRADOS: $total\n";
    
    echo str_repeat("=", 60) . "\n";
}

// Función para crear estructura de directorios
function createDirectoryStructure() {
    $dirs = [
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/',
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/productos/',
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/galeria/',
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/images/catalogo/marcas/',
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/documents/catalogo/',
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/documents/catalogo/manuales/',
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/documents/catalogo/fichas/',
        '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/documents/catalogo/certificados/'
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "✅ Directorio creado: $dir\n";
        }
    }
}

// Función para generar archivo .htaccess para el catálogo
function generateHtaccess() {
    $htaccessContent = '
# Configuración para catálogo de productos
Options -Indexes

# Compresión
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache para imágenes
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
</IfModule>

# Headers de seguridad
<IfModule mod_headers.c>
    Header set X-Content-Type-Options nosniff
    Header set X-Frame-Options DENY
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
';
    
    $htaccessFile = '/Users/gorila/Desktop/CLONE/GIT/aramed/public_html/assets/.htaccess';
    file_put_contents($htaccessFile, $htaccessContent);
    echo "✅ Archivo .htaccess generado: $htaccessFile\n";
}

// EJECUTAR MIGRACIÓN
echo "🚀 INICIANDO MIGRACIÓN DE ARCHIVOS DEL CATÁLOGO\n";
echo str_repeat("=", 60) . "\n";

// Crear estructura de directorios
if ($config['create_dirs']) {
    echo "\n📁 CREANDO ESTRUCTURA DE DIRECTORIOS...\n";
    createDirectoryStructure();
}

// Generar .htaccess
generateHtaccess();

// Migrar archivos
if ($config['copy_files']) {
    $stats = migrateFiles($config);
    generateReport($stats);
} else {
    echo "\n⚠️  MIGRACIÓN DE ARCHIVOS DESHABILITADA\n";
    echo "   Cambiar 'copy_files' => true en la configuración\n";
}

echo "\n✅ MIGRACIÓN COMPLETADA\n";
echo "\n📋 PRÓXIMOS PASOS:\n";
echo "   1. Ejecutar: nueva_estructura_catalogo.sql\n";
echo "   2. Ejecutar: migracion_datos_catalogo.sql\n";
echo "   3. Verificar archivos migrados\n";
echo "   4. Crear páginas de catálogo en el frontend\n";
echo "   5. Configurar sistema de búsqueda y filtros\n";

?>
