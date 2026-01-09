<?php
/**
 * TEST - Búsqueda de Productos
 * Para depurar problemas con la búsqueda de productos
 */

define('ARAMED_SITE', true);
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/connection.php';
require_once __DIR__ . '/../auth_check.php';

$pdo = getDB();
if (!$pdo) {
    die('Error de conexión');
}

// Verificar que la tabla existe
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'catalogo_productos'");
    $table_exists = $stmt->rowCount() > 0;
    
    echo "=== TEST DE BÚSQUEDA DE PRODUCTOS ===<br><br>";
    echo "1. Tabla catalogo_productos existe: " . ($table_exists ? "SÍ" : "NO") . "<br>";
    
    if ($table_exists) {
        // Contar productos publicados
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos WHERE estado = 'publicado'");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "2. Productos publicados: " . $count['total'] . "<br>";
        
        // Listar primeros 5 productos
        $stmt = $pdo->query("SELECT id, nombre, codigo, estado FROM catalogo_productos LIMIT 5");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "3. Primeros 5 productos:<br>";
        foreach ($products as $p) {
            echo "   - ID: {$p['id']}, Nombre: {$p['nombre']}, Código: {$p['codigo']}, Estado: {$p['estado']}<br>";
        }
        
        // Test de búsqueda
        $test_query = 'a';
        $sql = "SELECT p.id, p.nombre, p.codigo, p.estado
                FROM catalogo_productos p
                WHERE p.estado = 'publicado' 
                AND (p.nombre LIKE :query OR p.codigo LIKE :query)
                LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':query' => '%' . $test_query . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<br>4. Test de búsqueda con 'a':<br>";
        echo "   Resultados encontrados: " . count($results) . "<br>";
        foreach ($results as $r) {
            echo "   - {$r['nombre']} ({$r['codigo']})<br>";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}

