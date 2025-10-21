<?php
/**
 * SCRIPT PARA VERIFICAR EL ESTADO DEL CATÁLOGO
 * Ejecutar desde: https://aramedylaboratorio.com/NUEVO/aramed/public_html/verificar_catalogo.php
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

// Función para verificar estructura de tabla
function checkTableStructure($pdo, $table_name) {
    echo "<br><strong>🔍 Estructura de $table_name:</strong><br>";
    try {
        $stmt = $pdo->query("DESCRIBE $table_name");
        $columns = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "<td>" . $column['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
}

// Función para mostrar datos de muestra
function showSampleData($pdo, $table_name, $limit = 5) {
    echo "<br><strong>📋 Datos de muestra de $table_name:</strong><br>";
    try {
        $stmt = $pdo->query("SELECT * FROM $table_name LIMIT $limit");
        $data = $stmt->fetchAll();
        
        if (empty($data)) {
            echo "No hay datos en esta tabla.<br>";
            return;
        }
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        
        // Headers
        echo "<tr style='background: #f0f0f0;'>";
        foreach (array_keys($data[0]) as $header) {
            echo "<th>$header</th>";
        }
        echo "</tr>";
        
        // Data rows
        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
}

// EJECUTAR VERIFICACIÓN
echo "<h1>🔍 VERIFICACIÓN DEL CATÁLOGO</h1>";
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

// Verificar tablas existentes
echo "<br>📋 VERIFICANDO TABLAS EXISTENTES...<br>";
$tables = [
    'catalogo_marcas',
    'catalogo_categorias',
    'catalogo_productos',
    'catalogo_producto_imagenes',
    'catalogo_producto_documentos',
    'catalogo_filtros',
    'catalogo_producto_stats'
];

$existing_tables = [];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        
        if ($exists) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            $existing_tables[$table] = $count;
            echo "✅ $table: $count registros<br>";
        } else {
            echo "❌ $table: NO EXISTE<br>";
        }
    } catch (PDOException $e) {
        echo "❌ $table: ERROR - " . $e->getMessage() . "<br>";
    }
}

// Verificar estructura de tablas principales
if (isset($existing_tables['catalogo_marcas'])) {
    checkTableStructure($pdo, 'catalogo_marcas');
    showSampleData($pdo, 'catalogo_marcas');
}

if (isset($existing_tables['catalogo_categorias'])) {
    checkTableStructure($pdo, 'catalogo_categorias');
    showSampleData($pdo, 'catalogo_categorias');
}

if (isset($existing_tables['catalogo_productos'])) {
    checkTableStructure($pdo, 'catalogo_productos');
    showSampleData($pdo, 'catalogo_productos');
}

// Verificar tablas del sistema viejo
echo "<br><strong>🔍 VERIFICANDO SISTEMA VIEJO...</strong><br>";
$old_tables = ['marcas', 'productos', 'imagenes_x_producto', 'usos'];
foreach ($old_tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        
        if ($exists) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "✅ $table: $count registros<br>";
        } else {
            echo "❌ $table: NO EXISTE<br>";
        }
    } catch (PDOException $e) {
        echo "❌ $table: ERROR - " . $e->getMessage() . "<br>";
    }
}

// Reporte final
echo "<br><hr>";
echo "<h2>📊 REPORTE DE VERIFICACIÓN</h2>";
echo "<hr>";

echo "<br><strong>✅ Tablas del catálogo creadas:</strong><br>";
foreach ($existing_tables as $table => $count) {
    echo "- $table: $count registros<br>";
}

echo "<br><strong>📋 Próximos pasos:</strong><br>";
echo "1. Verificar que las tablas tienen la estructura correcta<br>";
echo "2. Si es necesario, importar datos del sistema viejo manualmente<br>";
echo "3. Crear páginas de catálogo en el frontend<br>";
echo "4. Implementar sistema de búsqueda y filtros<br>";

echo "<br><hr>";
echo "<p><strong>✅ Verificación completada</strong></p>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

?>
