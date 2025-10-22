<?php
/**
 * Script de diagnóstico para error 500 en blog.php
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "🔍 Diagnóstico de Error 500 en Blog\n";
echo "====================================\n\n";

// 1. Verificar configuración de errores
echo "1. Configuración de errores:\n";
echo "error_reporting: " . error_reporting() . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";
echo "error_log: " . ini_get('error_log') . "\n\n";

// 2. Verificar archivos
echo "2. Verificando archivos:\n";
$archivos = [
    'includes/config.php',
    'includes/functions.php', 
    'includes/connection.php',
    'includes/navbar.php',
    'includes/footer.php',
    'blog.php'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ $archivo\n";
    } else {
        echo "❌ $archivo\n";
    }
}

// 3. Probar carga de configuración
echo "\n3. Probando carga de configuración:\n";
try {
    require_once 'includes/config.php';
    echo "✅ config.php cargado\n";
    echo "SITE_URL: " . (defined('SITE_URL') ? SITE_URL : 'NO DEFINIDO') . "\n";
    echo "SITE_NAME: " . (defined('SITE_NAME') ? SITE_NAME : 'NO DEFINIDO') . "\n";
} catch (Exception $e) {
    echo "❌ Error en config.php: " . $e->getMessage() . "\n";
}

// 4. Probar carga de funciones
echo "\n4. Probando carga de funciones:\n";
try {
    require_once 'includes/functions.php';
    echo "✅ functions.php cargado\n";
    $funciones = ['sanitizeInput', 'esc', 'imageUrl', 'siteUrl'];
    foreach ($funciones as $funcion) {
        if (function_exists($funcion)) {
            echo "✅ Función $funcion existe\n";
        } else {
            echo "❌ Función $funcion NO existe\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error en functions.php: " . $e->getMessage() . "\n";
}

// 5. Probar conexión a BD
echo "\n5. Probando conexión a BD:\n";
try {
    require_once 'includes/connection.php';
    $pdo = getDB();
    if ($pdo) {
        echo "✅ Conexión PDO exitosa\n";
        
        // Probar consulta simple
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM blog_articulos");
        $result = $stmt->fetch();
        echo "✅ Consulta de prueba: " . $result['total'] . " artículos\n";
    } else {
        echo "❌ getDB() retornó false\n";
    }
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}

// 6. Probar includes del blog
echo "\n6. Probando includes del blog:\n";
try {
    if (file_exists('includes/navbar.php')) {
        echo "✅ navbar.php existe\n";
    } else {
        echo "❌ navbar.php NO existe\n";
    }
    
    if (file_exists('includes/footer.php')) {
        echo "✅ footer.php existe\n";
    } else {
        echo "❌ footer.php NO existe\n";
    }
} catch (Exception $e) {
    echo "❌ Error verificando includes: " . $e->getMessage() . "\n";
}

// 7. Simular la lógica del blog
echo "\n7. Simulando lógica del blog:\n";
try {
    define('ARAMED_SITE', true);
    
    // Simular parámetros GET
    $_GET['categoria'] = 0;
    $_GET['busqueda'] = '';
    $_GET['page'] = 1;
    
    $categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
    $busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;
    
    echo "✅ Parámetros procesados correctamente\n";
    echo "Categoría: $categoria_id, Búsqueda: '$busqueda', Página: $page\n";
    
    // Construir consulta
    $where_conditions = ['a.estado = "publicado"'];
    $params = [];
    
    if ($categoria_id > 0) {
        $where_conditions[] = 'a.categoria_id = ?';
        $params[] = $categoria_id;
    }
    
    if (!empty($busqueda)) {
        $where_conditions[] = '(a.titulo LIKE ? OR a.resumen LIKE ? OR a.contenido LIKE ?)';
        $search_term = "%{$busqueda}%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    echo "✅ WHERE clause construido: $where_clause\n";
    
    // Probar consulta de artículos
    $sql_articulos = "
        SELECT a.*, c.nombre as categoria_nombre, c.slug as categoria_slug, c.color as categoria_color, c.icono as categoria_icono
        FROM blog_articulos a
        LEFT JOIN blog_categorias c ON a.categoria_id = c.id
        WHERE {$where_clause}
        ORDER BY a.destacado DESC, a.fecha_publicacion DESC
        LIMIT {$per_page} OFFSET {$offset}
    ";
    
    $stmt_articulos = $pdo->prepare($sql_articulos);
    $stmt_articulos->execute($params);
    $articulos = $stmt_articulos->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Consulta de artículos ejecutada: " . count($articulos) . " resultados\n";
    
} catch (Exception $e) {
    echo "❌ Error en lógica del blog: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// 8. Verificar permisos de archivos
echo "\n8. Verificando permisos:\n";
$archivos_importantes = ['blog.php', 'includes/config.php', 'includes/functions.php'];
foreach ($archivos_importantes as $archivo) {
    if (file_exists($archivo)) {
        $perms = fileperms($archivo);
        echo "$archivo: " . substr(sprintf('%o', $perms), -4) . "\n";
    }
}

// 9. Verificar memoria y límites
echo "\n9. Límites del servidor:\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "max_input_vars: " . ini_get('max_input_vars') . "\n";

echo "\n🎯 Diagnóstico completado\n";
?>
