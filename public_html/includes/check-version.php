<?php
/**
 * Verificar versión de los archivos en el servidor
 */

echo "<h1>🔍 Verificación de Archivos en el Servidor</h1>";
echo "<hr>";

// Verificar connection.php
echo "<h2>1. connection.php</h2>";
$connection_path = __DIR__ . '/connection.php';
if (file_exists($connection_path)) {
    $size = filesize($connection_path);
    $modified = date('Y-m-d H:i:s', filemtime($connection_path));
    $content = file_get_contents($connection_path);
    
    echo "✅ Archivo existe<br>";
    echo "📏 Tamaño: " . number_format($size) . " bytes<br>";
    echo "🕐 Última modificación: $modified<br>";
    
    // Verificar si tiene el código correcto (sin Singleton)
    if (strpos($content, 'class Database') !== false) {
        echo "❌ <strong>VERSIÓN ANTIGUA - Tiene clase Database (Singleton)</strong><br>";
    } else if (strpos($content, 'function getDB()') !== false) {
        echo "✅ <strong>VERSIÓN NUEVA - Usa función getDB() simple</strong><br>";
    } else {
        echo "⚠️ Versión desconocida<br>";
    }
} else {
    echo "❌ Archivo NO existe<br>";
}

echo "<hr>";

// Verificar newsletter_handler.php
echo "<h2>2. newsletter_handler.php</h2>";
$newsletter_path = __DIR__ . '/newsletter_handler.php';
if (file_exists($newsletter_path)) {
    $size = filesize($newsletter_path);
    $modified = date('Y-m-d H:i:s', filemtime($newsletter_path));
    $content = file_get_contents($newsletter_path);
    
    echo "✅ Archivo existe<br>";
    echo "📏 Tamaño: " . number_format($size) . " bytes<br>";
    echo "🕐 Última modificación: $modified<br>";
    
    // Verificar si tiene el try-catch para getDB()
    if (preg_match('/try\s*\{[^}]*\$pdo\s*=\s*getDB\(\);/s', $content)) {
        echo "✅ <strong>VERSIÓN NUEVA - Tiene try-catch para getDB()</strong><br>";
    } else {
        echo "❌ <strong>VERSIÓN ANTIGUA - NO tiene try-catch para getDB()</strong><br>";
    }
    
    // Verificar si usa debugLog
    if (strpos($content, 'debugLog(') !== false) {
        echo "✅ Usa debugLog()<br>";
    } else {
        echo "⚠️ NO usa debugLog()<br>";
    }
} else {
    echo "❌ Archivo NO existe<br>";
}

echo "<hr>";

// Verificar contact_handler.php
echo "<h2>3. contact_handler.php</h2>";
$contact_path = __DIR__ . '/contact_handler.php';
if (file_exists($contact_path)) {
    $size = filesize($contact_path);
    $modified = date('Y-m-d H:i:s', filemtime($contact_path));
    $content = file_get_contents($contact_path);
    
    echo "✅ Archivo existe<br>";
    echo "📏 Tamaño: " . number_format($size) . " bytes<br>";
    echo "🕐 Última modificación: $modified<br>";
    
    // Verificar si tiene el try-catch para getDB()
    if (preg_match('/try\s*\{[^}]*\$pdo\s*=\s*getDB\(\);/s', $content)) {
        echo "✅ <strong>VERSIÓN NUEVA - Tiene try-catch para getDB()</strong><br>";
    } else {
        echo "❌ <strong>VERSIÓN ANTIGUA - NO tiene try-catch para getDB()</strong><br>";
    }
} else {
    echo "❌ Archivo NO existe<br>";
}

echo "<hr>";
echo "<h2>📋 RESUMEN</h2>";

// Resumen
$connection_ok = file_exists($connection_path) && strpos(file_get_contents($connection_path), 'function getDB()') !== false;
$newsletter_ok = file_exists($newsletter_path) && preg_match('/try\s*\{[^}]*\$pdo\s*=\s*getDB\(\);/s', file_get_contents($newsletter_path));
$contact_ok = file_exists($contact_path) && preg_match('/try\s*\{[^}]*\$pdo\s*=\s*getDB\(\);/s', file_get_contents($contact_path));

if ($connection_ok && $newsletter_ok && $contact_ok) {
    echo "<p style='color: green; font-size: 20px; font-weight: bold;'>✅ TODOS LOS ARCHIVOS ESTÁN ACTUALIZADOS</p>";
    echo "<p>El formulario debería funcionar correctamente.</p>";
} else {
    echo "<p style='color: red; font-size: 20px; font-weight: bold;'>❌ ALGUNOS ARCHIVOS NECESITAN ACTUALIZARSE</p>";
    echo "<p>Archivos que necesitan actualizarse:</p>";
    echo "<ul>";
    if (!$connection_ok) echo "<li>❌ connection.php</li>";
    if (!$newsletter_ok) echo "<li>❌ newsletter_handler.php</li>";
    if (!$contact_ok) echo "<li>❌ contact_handler.php</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Fecha y hora del servidor:</strong> " . date('Y-m-d H:i:s') . "</p>";

