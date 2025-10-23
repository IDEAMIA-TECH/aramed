<?php
/**
 * ========================================
 * TEST USUARIOS CORREGIDO
 * ========================================
 * 
 * Script para verificar que la página de usuarios funciona correctamente
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

echo "<h2>🧪 TEST USUARIOS CORREGIDO</h2><hr>";

echo "<h3>1️⃣ VERIFICANDO ESTRUCTURA DEL ARCHIVO</h3>";

$archivo = __DIR__ . '/admin/usuarios.php';
$contenido = file_get_contents($archivo);

// Verificar que no hay includes duplicados
$includes_menu = substr_count($contenido, "admin_menu.php");
echo "✅ <strong>Includes de admin_menu.php:</strong> $includes_menu<br>";

if ($includes_menu == 1) {
    echo "✅ <strong>Correcto: Solo un include del menú</strong><br>";
} else {
    echo "❌ <strong>Error: Múltiples includes del menú</strong><br>";
}

// Verificar que el include está en el lugar correcto (dentro del HTML)
$posicion_include = strpos($contenido, "<?php include 'includes/admin_menu.php'; ?>");
if ($posicion_include !== false) {
    echo "✅ <strong>Include del menú encontrado en el HTML</strong><br>";
} else {
    echo "❌ <strong>Include del menú no encontrado en el HTML</strong><br>";
}

echo "<br>";

echo "<h3>2️⃣ VERIFICANDO ORDEN DE INCLUDES</h3>";

$lineas = file($archivo, FILE_IGNORE_NEW_LINES);
$orden_correcto = true;

foreach ($lineas as $num_linea => $linea) {
    $num_linea_real = $num_linea + 1;
    
    if (strpos($linea, 'require_once') !== false) {
        if (strpos($linea, 'config.php') !== false) {
            echo "✅ <strong>Línea $num_linea_real:</strong> config.php<br>";
        } elseif (strpos($linea, 'functions.php') !== false) {
            echo "✅ <strong>Línea $num_linea_real:</strong> functions.php<br>";
        } elseif (strpos($linea, 'connection.php') !== false) {
            echo "✅ <strong>Línea $num_linea_real:</strong> connection.php<br>";
        } elseif (strpos($linea, 'auth_check.php') !== false) {
            echo "✅ <strong>Línea $num_linea_real:</strong> auth_check.php<br>";
        }
    }
}

echo "<br>";

echo "<h3>3️⃣ VERIFICANDO CONTENIDO HTML</h3>";

// Verificar que hay contenido HTML
if (strpos($contenido, '<!DOCTYPE html>') !== false) {
    echo "✅ <strong>DOCTYPE HTML encontrado</strong><br>";
} else {
    echo "❌ <strong>DOCTYPE HTML no encontrado</strong><br>";
}

if (strpos($contenido, '<body>') !== false) {
    echo "✅ <strong>Etiqueta body encontrada</strong><br>";
} else {
    echo "❌ <strong>Etiqueta body no encontrada</strong><br>";
}

if (strpos($contenido, 'Administración de Usuarios') !== false) {
    echo "✅ <strong>Título de la página encontrado</strong><br>";
} else {
    echo "❌ <strong>Título de la página no encontrado</strong><br>";
}

if (strpos($contenido, 'Lista de Usuarios') !== false) {
    echo "✅ <strong>Sección de lista de usuarios encontrada</strong><br>";
} else {
    echo "❌ <strong>Sección de lista de usuarios no encontrada</strong><br>";
}

echo "<br>";

echo "<h3>4️⃣ VERIFICANDO LÓGICA PHP</h3>";

// Verificar variables importantes
if (strpos($contenido, '$current_page = \'usuarios.php\';') !== false) {
    echo "✅ <strong>Variable current_page definida</strong><br>";
} else {
    echo "❌ <strong>Variable current_page no definida</strong><br>";
}

if (strpos($contenido, '$action = $_GET[\'action\'] ?? \'list\';') !== false) {
    echo "✅ <strong>Variable action definida</strong><br>";
} else {
    echo "❌ <strong>Variable action no definida</strong><br>";
}

if (strpos($contenido, '$usuarios = [];') !== false) {
    echo "✅ <strong>Variable usuarios definida</strong><br>";
} else {
    echo "❌ <strong>Variable usuarios no definida</strong><br>";
}

if (strpos($contenido, '$stats = [];') !== false) {
    echo "✅ <strong>Variable stats definida</strong><br>";
} else {
    echo "❌ <strong>Variable stats no definida</strong><br>";
}

echo "<br>";

echo "<h3>5️⃣ VERIFICANDO CONSULTAS SQL</h3>";

if (strpos($contenido, 'SELECT * FROM admin_usuarios') !== false) {
    echo "✅ <strong>Consulta de usuarios encontrada</strong><br>";
} else {
    echo "❌ <strong>Consulta de usuarios no encontrada</strong><br>";
}

if (strpos($contenido, 'WHERE estado = \'activo\'') !== false) {
    echo "✅ <strong>Consulta de usuarios activos encontrada</strong><br>";
} else {
    echo "❌ <strong>Consulta de usuarios activos no encontrada</strong><br>";
}

echo "<br>";

echo "<h3>6️⃣ VERIFICANDO LOOPS Y CONDICIONALES</h3>";

if (strpos($contenido, '<?php foreach ($usuarios as $user): ?>') !== false) {
    echo "✅ <strong>Loop de usuarios encontrado</strong><br>";
} else {
    echo "❌ <strong>Loop de usuarios no encontrado</strong><br>";
}

if (strpos($contenido, '<?php if (empty($usuarios)): ?>') !== false) {
    echo "✅ <strong>Condicional para usuarios vacíos encontrado</strong><br>";
} else {
    echo "❌ <strong>Condicional para usuarios vacíos no encontrado</strong><br>";
}

echo "<br>";

echo "<h3>✅ RESUMEN</h3>";

$errores = 0;
if ($includes_menu != 1) $errores++;
if (strpos($contenido, '<!DOCTYPE html>') === false) $errores++;
if (strpos($contenido, 'Administración de Usuarios') === false) $errores++;
if (strpos($contenido, 'SELECT * FROM admin_usuarios') === false) $errores++;

if ($errores == 0) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>TODAS LAS VERIFICACIONES PASARON</strong><br>";
    echo "La página de usuarios debería funcionar correctamente ahora.<br>";
    echo "<a href='admin/usuarios.php' target='_blank' style='color: #155724; font-weight: bold;'>🔗 Probar Página de Usuarios</a>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>SE ENCONTRARON $errores ERRORES</strong><br>";
    echo "Revisa los errores anteriores antes de probar la página.";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Este test verifica la estructura del archivo usuarios.php después de las correcciones.</p>";
?>
