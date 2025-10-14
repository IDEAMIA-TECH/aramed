<?php
/**
 * VISOR DE LOGS DE DEBUG
 * 
 * ⚠️ ELIMINAR ESTE ARCHIVO DESPUÉS DE DEBUGGING
 */

// Configuración
define('ROOT_PATH', __DIR__);
define('DEBUG_LOG_FILE', ROOT_PATH . '/logs/debug.log');

// Acción de limpiar
if (isset($_GET['clear'])) {
    if (file_exists(DEBUG_LOG_FILE)) {
        unlink(DEBUG_LOG_FILE);
    }
    header('Location: view-debug-log.php');
    exit;
}

// Leer log
$logContent = '';
if (file_exists(DEBUG_LOG_FILE)) {
    $logContent = file_get_contents(DEBUG_LOG_FILE);
    if (empty($logContent)) {
        $logContent = "El archivo de log está vacío.";
    }
} else {
    $logContent = "No hay archivo de log. Se creará cuando se ejecute el formulario.";
}

// Contar líneas
$lineCount = substr_count($logContent, "\n");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Log Viewer - Aramed</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a1a;
            color: #0f0;
            padding: 20px;
            margin: 0;
        }
        .header {
            background: #2a2a2a;
            border: 2px solid #0f0;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        h1 {
            color: #0ff;
            margin: 0 0 10px 0;
        }
        .warning {
            color: #ff0;
            font-weight: bold;
        }
        .controls {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        .btn {
            background: #0f0;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #0ff;
        }
        .btn-danger {
            background: #f00;
            color: #fff;
        }
        .btn-danger:hover {
            background: #ff4444;
        }
        .log-container {
            background: #000;
            border: 2px solid #0f0;
            padding: 20px;
            border-radius: 5px;
            overflow-x: auto;
            max-height: 80vh;
            overflow-y: auto;
        }
        .log-content {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 13px;
            line-height: 1.6;
        }
        .success {
            color: #0f0;
        }
        .error {
            color: #f00;
            font-weight: bold;
        }
        .info {
            color: #0ff;
        }
        .stats {
            background: #2a2a2a;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #0ff;
        }
        .highlight {
            background: #333;
            padding: 2px 4px;
        }
        /* Colores para diferentes tipos de mensajes */
        .log-content {
            color: #0f0;
        }
        .log-line:has(.error-marker) {
            background: rgba(255, 0, 0, 0.1);
            padding: 2px;
        }
    </style>
    <script>
        // Auto-refresh cada 5 segundos
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
</head>
<body>
    <div class="header">
        <h1>🔍 DEBUG LOG VIEWER</h1>
        <p class="warning">⚠️ ESTE ARCHIVO DEBE SER ELIMINADO DESPUÉS DE DEBUGGING</p>
        <p>Auto-refresh cada 5 segundos | Última actualización: <?php echo date('H:i:s'); ?></p>
    </div>

    <div class="stats">
        <strong>📊 Estadísticas:</strong><br>
        • Líneas totales: <span class="highlight"><?php echo $lineCount; ?></span><br>
        • Archivo: <span class="highlight"><?php echo file_exists(DEBUG_LOG_FILE) ? 'Existe' : 'No existe'; ?></span><br>
        • Tamaño: <span class="highlight"><?php echo file_exists(DEBUG_LOG_FILE) ? number_format(filesize(DEBUG_LOG_FILE) / 1024, 2) . ' KB' : '0 KB'; ?></span><br>
        • Última modificación: <span class="highlight"><?php echo file_exists(DEBUG_LOG_FILE) ? date('Y-m-d H:i:s', filemtime(DEBUG_LOG_FILE)) : 'N/A'; ?></span>
    </div>

    <div class="controls">
        <button onclick="location.reload()" class="btn">🔄 Recargar Ahora</button>
        <a href="?clear=1" class="btn btn-danger" onclick="return confirm('¿Seguro que quieres limpiar el log?')">🗑️ Limpiar Log</a>
        <a href="../public_html/" class="btn">🏠 Ir a la página principal</a>
    </div>

    <div class="log-container">
        <div class="log-content"><?php 
            // Colorear mensajes importantes
            $colored = $logContent;
            $colored = preg_replace('/✅([^\n]*)/m', '<span class="success">✅$1</span>', $colored);
            $colored = preg_replace('/❌([^\n]*)/m', '<span class="error">❌$1</span>', $colored);
            $colored = preg_replace('/(===== [^=]+ =====)/m', '<span class="info">$1</span>', $colored);
            $colored = preg_replace('/(--- [^-]+ ---)/m', '<span class="info">$1</span>', $colored);
            echo $colored;
        ?></div>
    </div>

    <div style="margin-top: 20px; padding: 20px; background: #2a2a2a; border-radius: 5px;">
        <h2 class="info">💡 Instrucciones:</h2>
        <ol style="color: #fff;">
            <li>Esta página se actualiza automáticamente cada 5 segundos</li>
            <li>Ve a la página principal y llena el formulario</li>
            <li>Regresa aquí para ver los logs en tiempo real</li>
            <li>Busca líneas con ❌ para identificar errores</li>
            <li>Busca líneas con ✅ para ver el progreso</li>
            <li><strong class="warning">ELIMINA ESTE ARCHIVO DESPUÉS</strong></li>
        </ol>
    </div>
</body>
</html>

