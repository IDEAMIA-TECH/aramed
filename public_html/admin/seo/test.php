<?php
/**
 * Archivo de prueba para verificar acceso al directorio seo/
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "OK - El directorio seo/ es accesible<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Admin logged in: " . (isset($_SESSION['admin_logged_in']) ? 'YES' : 'NO') . "<br>";
echo "User role: " . ($_SESSION['admin_rol'] ?? 'NOT SET') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current file: " . __FILE__ . "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "<br>";

