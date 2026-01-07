<?php
/**
 * Ver logs de debug de config.php y admin_menu.php
 */

// Verificar autenticación básica
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Acceso denegado');
}

$config_log = __DIR__ . '/../../logs/config-debug.log';
$menu_log = __DIR__ . '/../../logs/menu-debug.log';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Debug - SEO Config</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            max-height: 500px;
            overflow-y: auto;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Logs de Debug</h1>
        <p><a href="config.php" class="btn btn-primary">Volver a Config</a></p>
        
        <div class="row">
            <div class="col-md-6">
                <h2>config.php Log</h2>
                <pre><?php
                if (file_exists($config_log)) {
                    echo htmlspecialchars(file_get_contents($config_log));
                } else {
                    echo "Log no existe aún. Intenta acceder a config.php primero.";
                }
                ?></pre>
            </div>
            <div class="col-md-6">
                <h2>admin_menu.php Log</h2>
                <pre><?php
                if (file_exists($menu_log)) {
                    echo htmlspecialchars(file_get_contents($menu_log));
                } else {
                    echo "Log no existe aún. Intenta acceder a config.php primero.";
                }
                ?></pre>
            </div>
        </div>
        
        <div class="mt-3">
            <form method="POST">
                <button type="submit" name="clear_logs" class="btn btn-danger">Limpiar Logs</button>
            </form>
        </div>
    </div>
    
    <?php
    if (isset($_POST['clear_logs'])) {
        @unlink($config_log);
        @unlink($menu_log);
        echo "<div class='alert alert-success mt-3'>Logs limpiados. Recarga la página.</div>";
    }
    ?>
</body>
</html>

