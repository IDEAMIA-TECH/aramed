<?php
/**
 * Diagnóstico para el blog
 */

echo "🔍 Diagnóstico del Blog\n";
echo "======================\n\n";

// Verificar archivos
echo "1. Verificando archivos:\n";
$archivos = [
    'includes/config.php',
    'includes/connection.php',
    'includes/functions.php',
    'blog.php',
    'blog-detalle.php',
    'assets/css/blog.css',
    'assets/js/blog.js',
    'admin/blog/index.php'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ $archivo\n";
    } else {
        echo "❌ $archivo\n";
    }
}

echo "\n2. Verificando configuración:\n";
if (file_exists('includes/config.php')) {
    require_once 'includes/config.php';
    echo "✅ Config cargado\n";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO') . "\n";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO') . "\n";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDO') . "\n";
} else {
    echo "❌ No se pudo cargar config.php\n";
}

echo "\n3. Verificando conexión a BD:\n";
if (file_exists('includes/connection.php')) {
    try {
        require_once 'includes/connection.php';
        echo "✅ Conexión exitosa\n";
        
        // Verificar si las tablas existen
        $tablas = ['blog_categorias', 'blog_articulos', 'blog_comentarios'];
        foreach ($tablas as $tabla) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Tabla $tabla existe\n";
            } else {
                echo "❌ Tabla $tabla NO existe\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ No se pudo cargar connection.php\n";
}

echo "\n4. Verificando directorios:\n";
$directorios = [
    'assets/images/blog',
    'admin/blog/views'
];

foreach ($directorios as $dir) {
    if (is_dir($dir)) {
        echo "✅ $dir\n";
    } else {
        echo "❌ $dir (creando...)\n";
        if (mkdir($dir, 0755, true)) {
            echo "✅ $dir creado\n";
        } else {
            echo "❌ No se pudo crear $dir\n";
        }
    }
}

echo "\n🎯 Diagnóstico completado\n";
?>
