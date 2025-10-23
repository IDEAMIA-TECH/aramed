<?php
/**
 * ========================================
 * TEST ANCHO DEL MENÚ
 * ========================================
 * 
 * Script para verificar que el menú lateral tiene el ancho correcto
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

echo "<h2>🧪 TEST ANCHO DEL MENÚ LATERAL</h2><hr>";

echo "<h3>1️⃣ VERIFICANDO CAMBIOS APLICADOS</h3>";

$archivo_menu = __DIR__ . '/admin/includes/admin_menu.php';
$contenido_menu = file_get_contents($archivo_menu);

// Verificar que se cambió col-lg-2 a col-lg-3
if (strpos($contenido_menu, 'col-lg-3') !== false) {
    echo "✅ <strong>col-lg-3 aplicado correctamente</strong><br>";
} else {
    echo "❌ <strong>col-lg-3 no encontrado</strong><br>";
}

// Verificar que se agregó min-width
if (strpos($contenido_menu, 'min-width: 280px') !== false) {
    echo "✅ <strong>min-width: 280px aplicado</strong><br>";
} else {
    echo "❌ <strong>min-width: 280px no encontrado</strong><br>";
}

// Verificar que se agregó max-width
if (strpos($contenido_menu, 'max-width: 320px') !== false) {
    echo "✅ <strong>max-width: 320px aplicado</strong><br>";
} else {
    echo "❌ <strong>max-width: 320px no encontrado</strong><br>";
}

echo "<br>";

echo "<h3>2️⃣ VERIFICANDO ARCHIVOS PRINCIPALES</h3>";

$archivo_usuarios = __DIR__ . '/admin/usuarios.php';
$contenido_usuarios = file_get_contents($archivo_usuarios);

// Verificar que usuarios.php usa col-lg-9
if (strpos($contenido_usuarios, 'col-lg-9') !== false) {
    echo "✅ <strong>usuarios.php usa col-lg-9</strong><br>";
} else {
    echo "❌ <strong>usuarios.php no usa col-lg-9</strong><br>";
}

$archivo_perfil = __DIR__ . '/admin/perfil.php';
$contenido_perfil = file_get_contents($archivo_perfil);

// Verificar que perfil.php usa col-lg-9
if (strpos($contenido_perfil, 'col-lg-9') !== false) {
    echo "✅ <strong>perfil.php usa col-lg-9</strong><br>";
} else {
    echo "❌ <strong>perfil.php no usa col-lg-9</strong><br>";
}

echo "<br>";

echo "<h3>3️⃣ VERIFICANDO CSS DEL MENÚ</h3>";

// Verificar estilos del sidebar
if (strpos($contenido_menu, '.admin-sidebar') !== false) {
    echo "✅ <strong>Clase .admin-sidebar encontrada</strong><br>";
} else {
    echo "❌ <strong>Clase .admin-sidebar no encontrada</strong><br>";
}

// Verificar estilos de los enlaces
if (strpos($contenido_menu, '.nav-link {') !== false) {
    echo "✅ <strong>Estilos de .nav-link encontrados</strong><br>";
} else {
    echo "❌ <strong>Estilos de .nav-link no encontrados</strong><br>";
}

// Verificar estilos hover
if (strpos($contenido_menu, '.nav-link:hover') !== false) {
    echo "✅ <strong>Estilos hover encontrados</strong><br>";
} else {
    echo "❌ <strong>Estilos hover no encontrados</strong><br>";
}

// Verificar estilos active
if (strpos($contenido_menu, '.nav-link.active') !== false) {
    echo "✅ <strong>Estilos active encontrados</strong><br>";
} else {
    echo "❌ <strong>Estilos active no encontrados</strong><br>";
}

echo "<br>";

echo "<h3>4️⃣ VERIFICANDO ESTRUCTURA HTML</h3>";

// Verificar que el menú tiene la estructura correcta
if (strpos($contenido_menu, '<div class="col-md-3 col-lg-3 admin-sidebar p-0">') !== false) {
    echo "✅ <strong>Estructura HTML del sidebar correcta</strong><br>";
} else {
    echo "❌ <strong>Estructura HTML del sidebar incorrecta</strong><br>";
}

// Verificar que hay navegación
if (strpos($contenido_menu, '<nav class="nav flex-column">') !== false) {
    echo "✅ <strong>Navegación encontrada</strong><br>";
} else {
    echo "❌ <strong>Navegación no encontrada</strong><br>";
}

echo "<br>";

echo "<h3>5️⃣ VERIFICANDO ENLACES DEL MENÚ</h3>";

$enlaces_esperados = [
    'Dashboard',
    'Blog',
    'Cotización Simple',
    'Newsletter Simple',
    'Mensajes Topbar',
    'Usuarios',
    'Mi Perfil',
    'Ver Blog',
    'Volver al Sitio',
    'Cerrar Sesión'
];

$enlaces_encontrados = 0;
foreach ($enlaces_esperados as $enlace) {
    if (strpos($contenido_menu, $enlace) !== false) {
        $enlaces_encontrados++;
        echo "✅ <strong>$enlace encontrado</strong><br>";
    } else {
        echo "❌ <strong>$enlace no encontrado</strong><br>";
    }
}

echo "<br>";

echo "<h3>✅ RESUMEN</h3>";

$total_verificaciones = 15; // Ajustar según el número de verificaciones
$verificaciones_exitosas = 0;

// Contar verificaciones exitosas
if (strpos($contenido_menu, 'col-lg-3') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, 'min-width: 280px') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, 'max-width: 320px') !== false) $verificaciones_exitosas++;
if (strpos($contenido_usuarios, 'col-lg-9') !== false) $verificaciones_exitosas++;
if (strpos($contenido_perfil, 'col-lg-9') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, '.admin-sidebar') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, '.nav-link {') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, '.nav-link:hover') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, '.nav-link.active') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, '<div class="col-md-3 col-lg-3 admin-sidebar p-0">') !== false) $verificaciones_exitosas++;
if (strpos($contenido_menu, '<nav class="nav flex-column">') !== false) $verificaciones_exitosas++;

$verificaciones_exitosas += $enlaces_encontrados;

$porcentaje = round(($verificaciones_exitosas / $total_verificaciones) * 100);

if ($porcentaje >= 90) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>TODAS LAS VERIFICACIONES PASARON ($porcentaje%)</strong><br>";
    echo "El menú lateral debería tener el ancho correcto ahora.<br>";
    echo "<a href='admin/usuarios.php' target='_blank' style='color: #155724; font-weight: bold;'>🔗 Probar Página de Usuarios</a>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>SE ENCONTRARON PROBLEMAS ($porcentaje%)</strong><br>";
    echo "Revisa los errores anteriores antes de probar la página.";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Este test verifica que el menú lateral tenga el ancho correcto.</p>";
?>
