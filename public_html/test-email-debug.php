<?php
/**
 * SCRIPT DE DEBUG PARA EMAIL
 * 
 * ⚠️ IMPORTANTE: ELIMINAR ESTE ARCHIVO DESPUÉS DE DEBUGGING
 * Este script muestra información sensible y NO debe estar en producción
 */

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Email Debug - Aramed</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a1a;
            color: #0f0;
            padding: 20px;
            line-height: 1.6;
        }
        .section {
            background: #2a2a2a;
            border: 2px solid #0f0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success { color: #0f0; }
        .error { color: #f00; }
        .warning { color: #ff0; }
        .info { color: #0ff; }
        h1, h2 { color: #0ff; }
        pre {
            background: #000;
            padding: 10px;
            border-left: 3px solid #0f0;
            overflow-x: auto;
        }
        .status-ok { background: #0f0; color: #000; padding: 2px 8px; border-radius: 3px; }
        .status-fail { background: #f00; color: #fff; padding: 2px 8px; border-radius: 3px; }
    </style>
</head>
<body>
<h1>🔧 EMAIL DEBUG - ARAMED Y LABORATORIOS</h1>
<p class='warning'>⚠️ ESTE ARCHIVO DEBE SER ELIMINADO DESPUÉS DEL DEBUG</p>
";

// Configuración
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');

try {
    require_once ROOT_PATH . '/includes/config.php';
    echo "<div class='section'><h2 class='success'>✅ Config.php cargado correctamente</h2></div>";
} catch (Exception $e) {
    echo "<div class='section'><h2 class='error'>❌ Error al cargar config.php</h2>";
    echo "<pre>" . $e->getMessage() . "</pre></div>";
    die();
}

try {
    require_once ROOT_PATH . '/includes/email_functions.php';
    echo "<div class='section'><h2 class='success'>✅ email_functions.php cargado correctamente</h2></div>";
} catch (Exception $e) {
    echo "<div class='section'><h2 class='error'>❌ Error al cargar email_functions.php</h2>";
    echo "<pre>" . $e->getMessage() . "</pre></div>";
    die();
}

// Test 1: Configuración SMTP
echo "<div class='section'>";
echo "<h2 class='info'>📋 CONFIGURACIÓN SMTP</h2>";
echo "<table style='width: 100%; color: #fff;'>";
echo "<tr><td><strong>SMTP_HOST:</strong></td><td>" . (defined('SMTP_HOST') ? SMTP_HOST : '<span class=\'error\'>NO DEFINIDO</span>') . "</td></tr>";
echo "<tr><td><strong>SMTP_PORT:</strong></td><td>" . (defined('SMTP_PORT') ? SMTP_PORT : '<span class=\'error\'>NO DEFINIDO</span>') . "</td></tr>";
echo "<tr><td><strong>SMTP_SECURE:</strong></td><td>" . (defined('SMTP_SECURE') ? SMTP_SECURE : '<span class=\'error\'>NO DEFINIDO</span>') . "</td></tr>";
echo "<tr><td><strong>SMTP_USERNAME:</strong></td><td>" . (defined('SMTP_USERNAME') ? SMTP_USERNAME : '<span class=\'error\'>NO DEFINIDO</span>') . "</td></tr>";
echo "<tr><td><strong>SMTP_PASSWORD:</strong></td><td>" . (defined('SMTP_PASSWORD') && !empty(SMTP_PASSWORD) ? '<span class=\'success\'>✅ Configurado (' . strlen(SMTP_PASSWORD) . ' caracteres)</span>' : '<span class=\'error\'>❌ NO CONFIGURADO</span>') . "</td></tr>";
echo "<tr><td><strong>MAIL_FROM_EMAIL:</strong></td><td>" . (defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : '<span class=\'error\'>NO DEFINIDO</span>') . "</td></tr>";
echo "<tr><td><strong>MAIL_FROM_NAME:</strong></td><td>" . (defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : '<span class=\'error\'>NO DEFINIDO</span>') . "</td></tr>";
echo "</table>";
echo "</div>";

// Test 2: Validar configuración
echo "<div class='section'>";
echo "<h2 class='info'>🔍 VALIDACIÓN DE CONFIGURACIÓN</h2>";
$validation = validateSMTPConfig();
if ($validation['valid']) {
    echo "<p class='success'>✅ Configuración válida</p>";
} else {
    echo "<p class='error'>❌ Problemas encontrados:</p>";
    echo "<ul>";
    foreach ($validation['issues'] as $issue) {
        echo "<li class='error'>" . htmlspecialchars($issue) . "</li>";
    }
    echo "</ul>";
}
echo "</div>";

// Test 3: PHPMailer disponible?
echo "<div class='section'>";
echo "<h2 class='info'>📦 PHPMAILER</h2>";
if (class_exists('PHPMailer')) {
    echo "<p class='success'>✅ PHPMailer está disponible</p>";
    echo "<p>Versión: " . PHPMailer::VERSION . "</p>";
} else {
    echo "<p class='warning'>⚠️ PHPMailer NO disponible</p>";
    echo "<p class='info'>ℹ️ Se usará mail() nativo de PHP</p>";
}
echo "</div>";

// Test 4: Funciones de PHP
echo "<div class='section'>";
echo "<h2 class='info'>🐘 PHP FUNCTIONS</h2>";
echo "<table style='width: 100%; color: #fff;'>";
echo "<tr><td><strong>mail() function:</strong></td><td>" . (function_exists('mail') ? '<span class=\'status-ok\'>OK</span>' : '<span class=\'status-fail\'>NO DISPONIBLE</span>') . "</td></tr>";
echo "<tr><td><strong>fsockopen():</strong></td><td>" . (function_exists('fsockopen') ? '<span class=\'status-ok\'>OK</span>' : '<span class=\'status-fail\'>NO DISPONIBLE</span>') . "</td></tr>";
echo "<tr><td><strong>stream_socket_client():</strong></td><td>" . (function_exists('stream_socket_client') ? '<span class=\'status-ok\'>OK</span>' : '<span class=\'status-fail\'>NO DISPONIBLE</span>') . "</td></tr>";
echo "</table>";
echo "</div>";

// Test 5: Test de conexión (sin enviar email)
echo "<div class='section'>";
echo "<h2 class='info'>🔌 TEST DE CONEXIÓN SMTP</h2>";
echo "<p class='warning'>⚠️ Conectando al servidor SMTP...</p>";
$connectionTest = testSMTPConnection();
if ($connectionTest['success']) {
    echo "<p class='success'>✅ " . htmlspecialchars($connectionTest['message']) . "</p>";
} else {
    echo "<p class='error'>❌ " . htmlspecialchars($connectionTest['message']) . "</p>";
}
echo "</div>";

// Test 6: Variables de entorno PHP
echo "<div class='section'>";
echo "<h2 class='info'>⚙️ PHP ENVIRONMENT</h2>";
echo "<table style='width: 100%; color: #fff;'>";
echo "<tr><td><strong>PHP Version:</strong></td><td>" . PHP_VERSION . "</td></tr>";
echo "<tr><td><strong>SAPI:</strong></td><td>" . php_sapi_name() . "</td></tr>";
echo "<tr><td><strong>OS:</strong></td><td>" . PHP_OS . "</td></tr>";
echo "<tr><td><strong>Sendmail path:</strong></td><td>" . ini_get('sendmail_path') . "</td></tr>";
echo "<tr><td><strong>SMTP (php.ini):</strong></td><td>" . ini_get('SMTP') . "</td></tr>";
echo "<tr><td><strong>smtp_port (php.ini):</strong></td><td>" . ini_get('smtp_port') . "</td></tr>";
echo "</table>";
echo "</div>";

// Instrucciones finales
echo "<div class='section'>";
echo "<h2 class='warning'>📝 PRÓXIMOS PASOS</h2>";
echo "<ol>";
echo "<li>Subir estos archivos al servidor si aún no lo hiciste:</li>";
echo "<ul>";
echo "<li><code>includes/config.php</code></li>";
echo "<li><code>includes/email_functions.php</code></li>";
echo "<li><code>includes/newsletter_handler.php</code></li>";
echo "<li><code>includes/contact_handler.php</code></li>";
echo "</ul>";
echo "<li>Intentar enviar el formulario de newsletter</li>";
echo "<li>Ver los logs del servidor en cPanel > Error Log</li>";
echo "<li>Buscar líneas que empiecen con:</li>";
echo "<ul>";
echo "<li><code>===== EMAIL SEND ATTEMPT =====</code></li>";
echo "<li><code>NEWSLETTER HANDLER - START</code></li>";
echo "<li><code>❌</code> para errores</li>";
echo "<li><code>✅</code> para éxitos</li>";
echo "</ul>";
echo "<li><strong class='error'>ELIMINAR ESTE ARCHIVO (test-email-debug.php) CUANDO TERMINES</strong></li>";
echo "</ol>";
echo "</div>";

echo "<div class='section'>";
echo "<h2 class='info'>📞 ¿NECESITAS MÁS AYUDA?</h2>";
echo "<p>Si después de revisar los logs el problema persiste:</p>";
echo "<ul>";
echo "<li>Copia el error exacto de los logs</li>";
echo "<li>Verifica con tu proveedor de hosting que el puerto 465 esté abierto</li>";
echo "<li>Verifica que el servidor SMTP permita conexiones desde tu servidor web</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?>

