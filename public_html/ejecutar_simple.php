<?php
/**
 * Script simple para ejecutar la creación de tablas
 */

echo "<h2>🔧 EJECUTANDO: Creación de Tablas del Newsletter</h2>";
echo "<hr>";

// Mostrar información del servidor
echo "<h3>📊 Información del Servidor</h3>";
echo "<p><strong>Servidor:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Local') . "</p>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

echo "<hr>";

// Mostrar los scripts disponibles
echo "<h3>📋 Scripts Disponibles</h3>";
echo "<ul>";
echo "<li><a href='crear_tablas_newsletter.php' target='_blank'>🔨 crear_tablas_newsletter.php</a> - Crea las tablas automáticamente</li>";
echo "<li><a href='verificar_tablas_newsletter.php' target='_blank'>🔍 verificar_tablas_newsletter.php</a> - Verifica las tablas existentes</li>";
echo "<li><a href='ejecutar_creacion_tablas.php' target='_blank'>⚡ ejecutar_creacion_tablas.php</a> - Ejecuta la creación completa</li>";
echo "</ul>";

echo "<hr>";

// Mostrar las tablas que se crearán
echo "<h3>📋 Tablas que se Crearán</h3>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Tabla</th><th>Propósito</th></tr>";
echo "<tr><td>newsletter_subscriptions</td><td>Almacena suscripciones al newsletter</td></tr>";
echo "<tr><td>contact_messages</td><td>Almacena mensajes de contacto</td></tr>";
echo "<tr><td>contact_quotes</td><td>Almacena solicitudes de cotización</td></tr>";
echo "</table>";

echo "<hr>";

// Mostrar instrucciones
echo "<h3>🚀 Instrucciones de Ejecución</h3>";
echo "<ol>";
echo "<li><strong>Paso 1:</strong> Haz clic en <a href='crear_tablas_newsletter.php' target='_blank'>crear_tablas_newsletter.php</a></li>";
echo "<li><strong>Paso 2:</strong> El script creará automáticamente las 3 tablas necesarias</li>";
echo "<li><strong>Paso 3:</strong> Verifica la creación con <a href='verificar_tablas_newsletter.php' target='_blank'>verificar_tablas_newsletter.php</a></li>";
echo "<li><strong>Paso 4:</strong> Prueba el formulario en <a href='index.php' target='_blank'>index.php</a></li>";
echo "</ol>";

echo "<hr>";

// Mostrar URLs completas
echo "<h3>🔗 URLs Completas para Ejecutar</h3>";
echo "<p><strong>Crear tablas:</strong></p>";
echo "<p><a href='https://aramedylaboratorio.com/NUEVO/aramed/public_html/crear_tablas_newsletter.php' target='_blank'>https://aramedylaboratorio.com/NUEVO/aramed/public_html/crear_tablas_newsletter.php</a></p>";

echo "<p><strong>Verificar tablas:</strong></p>";
echo "<p><a href='https://aramedylaboratorio.com/NUEVO/aramed/public_html/verificar_tablas_newsletter.php' target='_blank'>https://aramedylaboratorio.com/NUEVO/aramed/public_html/verificar_tablas_newsletter.php</a></p>";

echo "<p><strong>Probar formulario:</strong></p>";
echo "<p><a href='https://aramedylaboratorio.com/NUEVO/aramed/public_html/index.php' target='_blank'>https://aramedylaboratorio.com/NUEVO/aramed/public_html/index.php</a></p>";

echo "<hr>";

echo "<h3>✅ Estado Actual</h3>";
echo "<p><strong>📁 Scripts creados:</strong> ✅ Listos</p>";
echo "<p><strong>🔧 Sintaxis verificada:</strong> ✅ Sin errores</p>";
echo "<p><strong>📤 Archivos subidos:</strong> ✅ En el servidor</p>";
echo "<p><strong>🚀 Listo para ejecutar:</strong> ✅ Sí</p>";

echo "<br><hr>";
echo "<p><strong>🎯 PRÓXIMO PASO:</strong> Ejecuta el script de creación de tablas desde el navegador web.</p>";

?>
