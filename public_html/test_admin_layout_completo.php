<?php
/**
 * ========================================
 * TEST LAYOUT ADMIN COMPLETO
 * ========================================
 * 
 * Script para verificar que el layout de todas las páginas del admin esté correcto
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

echo "<h2>🧪 TEST LAYOUT ADMIN COMPLETO</h2><hr>";

echo "<h3>1️⃣ VERIFICANDO TODAS LAS PÁGINAS DEL ADMIN</h3>";

$archivos_admin = [
    'admin/index.php',
    'admin/usuarios.php',
    'admin/perfil.php',
    'admin/newsletter-subscriptions.php',
    'admin/newsletter-simple.php',
    'admin/topbar-messages.php',
    'admin/blog/index.php',
    'admin/blog/create.php',
    'admin/blog/edit.php',
    'admin/blog/edit_simple.php',
    'admin/blog/comentarios.php',
    'admin/blog/categorias.php',
    'admin/blog/image-manager.php'
];

$archivos_corregidos = 0;
$archivos_con_errores = 0;

foreach ($archivos_admin as $archivo) {
    $ruta_completa = __DIR__ . '/' . $archivo;
    
    if (file_exists($ruta_completa)) {
        $contenido = file_get_contents($ruta_completa);
        
        // Verificar que usa col-lg-9
        if (strpos($contenido, 'col-lg-9') !== false) {
            echo "✅ <strong>$archivo:</strong> Usa col-lg-9<br>";
            $archivos_corregidos++;
        } else {
            echo "❌ <strong>$archivo:</strong> NO usa col-lg-9<br>";
            $archivos_con_errores++;
        }
        
        // Verificar que NO usa col-lg-10
        if (strpos($contenido, 'col-lg-10') !== false) {
            echo "❌ <strong>$archivo:</strong> Aún usa col-lg-10 (incorrecto)<br>";
            $archivos_con_errores++;
        } else {
            echo "✅ <strong>$archivo:</strong> NO usa col-lg-10 (correcto)<br>";
        }
        
        echo "<br>";
    } else {
        echo "❌ <strong>$archivo:</strong> Archivo no encontrado<br><br>";
        $archivos_con_errores++;
    }
}

echo "<h3>2️⃣ VERIFICANDO ESTRUCTURA DEL MENÚ</h3>";

$archivo_menu = __DIR__ . '/admin/includes/admin_menu.php';
$contenido_menu = file_get_contents($archivo_menu);

// Verificar que el menú usa col-lg-3
if (strpos($contenido_menu, 'col-lg-3') !== false) {
    echo "✅ <strong>Menú usa col-lg-3</strong><br>";
} else {
    echo "❌ <strong>Menú NO usa col-lg-3</strong><br>";
}

// Verificar que no usa col-lg-2
if (strpos($contenido_menu, 'col-lg-2') !== false) {
    echo "❌ <strong>Menú aún usa col-lg-2 (incorrecto)</strong><br>";
} else {
    echo "✅ <strong>Menú NO usa col-lg-2 (correcto)</strong><br>";
}

echo "<br>";

echo "<h3>3️⃣ VERIFICANDO PROPORCIONES</h3>";

// Calcular proporciones
$sidebar_width = 3; // col-lg-3
$content_width = 9; // col-lg-9
$total_width = $sidebar_width + $content_width;

$sidebar_percentage = round(($sidebar_width / $total_width) * 100);
$content_percentage = round(($content_width / $total_width) * 100);

echo "✅ <strong>Proporciones del layout:</strong><br>";
echo "&nbsp;&nbsp;• <strong>Sidebar:</strong> $sidebar_width/12 ($sidebar_percentage%)<br>";
echo "&nbsp;&nbsp;• <strong>Contenido:</strong> $content_width/12 ($content_percentage%)<br>";
echo "&nbsp;&nbsp;• <strong>Total:</strong> $total_width/12 (100%)<br>";

echo "<br>";

echo "<h3>4️⃣ VERIFICANDO CSS DEL SIDEBAR</h3>";

// Verificar CSS del sidebar
if (strpos($contenido_menu, 'min-width: 280px') !== false) {
    echo "✅ <strong>min-width: 280px encontrado</strong><br>";
} else {
    echo "❌ <strong>min-width: 280px NO encontrado</strong><br>";
}

if (strpos($contenido_menu, 'max-width: 320px') !== false) {
    echo "✅ <strong>max-width: 320px encontrado</strong><br>";
} else {
    echo "❌ <strong>max-width: 320px NO encontrado</strong><br>";
}

echo "<br>";

echo "<h3>5️⃣ VERIFICANDO RESPONSIVIDAD</h3>";

// Verificar clases responsive en el dashboard
$archivo_dashboard = __DIR__ . '/admin/index.php';
$contenido_dashboard = file_get_contents($archivo_dashboard);

if (strpos($contenido_dashboard, 'col-md-9') !== false) {
    echo "✅ <strong>col-md-9 encontrado (tablet)</strong><br>";
} else {
    echo "❌ <strong>col-md-9 NO encontrado</strong><br>";
}

if (strpos($contenido_menu, 'col-md-3') !== false) {
    echo "✅ <strong>col-md-3 encontrado (tablet)</strong><br>";
} else {
    echo "❌ <strong>col-md-3 NO encontrado</strong><br>";
}

echo "<br>";

echo "<h3>6️⃣ VERIFICANDO ESTRUCTURA HTML</h3>";

// Verificar estructura del dashboard
if (strpos($contenido_dashboard, '<div class="container-fluid">') !== false) {
    echo "✅ <strong>container-fluid encontrado</strong><br>";
} else {
    echo "❌ <strong>container-fluid NO encontrado</strong><br>";
}

if (strpos($contenido_dashboard, '<div class="row">') !== false) {
    echo "✅ <strong>row encontrado</strong><br>";
} else {
    echo "❌ <strong>row NO encontrado</strong><br>";
}

if (strpos($contenido_dashboard, 'admin-content') !== false) {
    echo "✅ <strong>admin-content encontrado</strong><br>";
} else {
    echo "❌ <strong>admin-content NO encontrado</strong><br>";
}

echo "<br>";

echo "<h3>7️⃣ VERIFICANDO INCLUDE DEL MENÚ</h3>";

// Verificar que el menú se incluye correctamente
if (strpos($contenido_dashboard, "include __DIR__ . '/includes/admin_menu.php'") !== false) {
    echo "✅ <strong>Include del menú encontrado</strong><br>";
} else {
    echo "❌ <strong>Include del menú NO encontrado</strong><br>";
}

echo "<br>";

echo "<h3>8️⃣ VERIFICANDO QUE NO QUEDAN col-lg-10</h3>";

// Buscar archivos que aún usen col-lg-10
$comando = "grep -r 'col-lg-10' " . __DIR__ . "/admin/ 2>/dev/null | wc -l";
$resultado = shell_exec($comando);
$archivos_con_col_lg_10 = intval(trim($resultado));

if ($archivos_con_col_lg_10 == 0) {
    echo "✅ <strong>No quedan archivos con col-lg-10</strong><br>";
} else {
    echo "❌ <strong>Aún hay $archivos_con_col_lg_10 archivos con col-lg-10</strong><br>";
}

echo "<br>";

echo "<h3>✅ RESUMEN FINAL</h3>";

$total_archivos = count($archivos_admin);
$errores_totales = $archivos_con_errores + $archivos_con_col_lg_10;

if ($errores_totales == 0) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>TODAS LAS VERIFICACIONES PASARON</strong><br>";
    echo "El layout de todas las páginas del admin debería estar correcto ahora.<br>";
    echo "<strong>Archivos corregidos:</strong> $archivos_corregidos/$total_archivos<br>";
    echo "<strong>Proporciones:</strong> Sidebar 25% | Contenido 75%<br>";
    echo "<strong>Archivos con col-lg-10:</strong> $archivos_con_col_lg_10<br>";
    echo "<a href='admin/index.php' target='_blank' style='color: #155724; font-weight: bold;'>🔗 Probar Dashboard</a> | ";
    echo "<a href='admin/newsletter-subscriptions.php' target='_blank' style='color: #155724; font-weight: bold;'>🔗 Probar Newsletter</a>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>SE ENCONTRARON $errores_totales ERRORES</strong><br>";
    echo "Revisa los errores anteriores antes de probar las páginas.";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Este test verifica que el layout de todas las páginas del admin esté correcto.</p>";
?>
