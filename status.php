<?php
echo "<h1>Estado del Sistema ARAmed</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Verificar archivos críticos
echo "<h2>Archivos del Sistema</h2>";
$files = ['index.php', 'includes/config.php', 'includes/connection.php', 'includes/widgets.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file existe</p>";
    } else {
        echo "<p style='color: red;'>✗ $file no existe</p>";
    }
}

// Verificar sesión
echo "<h2>Estado de Sesión</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✓ Sesión activa</p>";
} else {
    echo "<p style='color: orange;'>⚠ Sesión no activa</p>";
}

// Verificar base de datos
echo "<h2>Base de Datos</h2>";
try {
    $connection = mysqli_connect('localhost', 'root', 'minimusa2000', 'aramed');
    if ($connection) {
        echo "<p style='color: green;'>✓ Conexión exitosa</p>";
        mysqli_close($connection);
    } else {
        echo "<p style='color: red;'>✗ Error: " . mysqli_connect_error() . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Excepción: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>← Ir al sitio principal</a></p>";
?> 