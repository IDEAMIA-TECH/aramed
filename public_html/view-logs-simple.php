<?php
$log_file = __DIR__ . '/logs/simple_debug.log';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Debug Logs</title>
    <style>
        body {
            font-family: 'Monaco', 'Consolas', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            color: #4ec9b0;
            margin-bottom: 20px;
        }
        .info {
            background: #252526;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .buttons {
            margin-bottom: 20px;
        }
        .btn {
            background: #0e639c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .btn:hover {
            background: #1177bb;
        }
        .btn-danger {
            background: #c5402f;
        }
        .log-container {
            background: #1e1e1e;
            border: 1px solid #3c3c3c;
            border-radius: 5px;
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }
        .log-line {
            padding: 4px 0;
            border-bottom: 1px solid #2d2d2d;
            font-size: 13px;
            line-height: 1.5;
        }
        .log-line:last-child {
            border-bottom: none;
        }
        .error {
            color: #f48771;
            background: rgba(244, 135, 113, 0.1);
            padding: 4px 8px;
        }
        .success {
            color: #89d185;
        }
        .timestamp {
            color: #858585;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #858585;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Simple Debug Logs</h1>
        
        <div class="info">
            <p><strong>Archivo:</strong> <?php echo $log_file; ?></p>
            <p><strong>Estado:</strong> 
                <?php if (file_exists($log_file)): ?>
                    <span style="color: #89d185;">✅ Existe</span>
                    (<?php echo number_format(filesize($log_file)); ?> bytes)
                <?php else: ?>
                    <span style="color: #f48771;">❌ No existe</span>
                <?php endif; ?>
            </p>
            <p><strong>Fecha/Hora:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>

        <div class="buttons">
            <a href="?refresh=1" class="btn">🔄 Refrescar</a>
            <a href="?clear=1" class="btn btn-danger" onclick="return confirm('¿Borrar logs?')">🗑️ Limpiar</a>
            <a href="test-form-debug.html" class="btn">🧪 Volver al Form</a>
        </div>

        <div class="log-container">
            <?php
            if (isset($_GET['clear']) && file_exists($log_file)) {
                unlink($log_file);
                echo '<div class="empty">🗑️ Logs limpiados</div>';
                echo '<script>setTimeout(() => window.location.href = "view-logs-simple.php", 1000);</script>';
            } elseif (file_exists($log_file)) {
                $content = file_get_contents($log_file);
                if (empty($content)) {
                    echo '<div class="empty">📝 El archivo existe pero está vacío</div>';
                } else {
                    $lines = explode("\n", $content);
                    foreach ($lines as $line) {
                        if (empty(trim($line))) continue;
                        
                        $class = '';
                        if (stripos($line, '❌') !== false || stripos($line, 'error') !== false) {
                            $class = 'error';
                        } elseif (stripos($line, '✅') !== false || stripos($line, 'success') !== false) {
                            $class = 'success';
                        }
                        
                        $line = htmlspecialchars($line);
                        $line = preg_replace('/\[([\d\-\s:]+)\]/', '<span class="timestamp">[$1]</span>', $line);
                        
                        echo "<div class='log-line $class'>$line</div>";
                    }
                }
            } else {
                echo '<div class="empty">';
                echo '<p>📝 No hay logs todavía</p>';
                echo '<p>El archivo se creará cuando envíes el formulario de test.</p>';
                echo '<p><a href="test-form-debug.html" style="color: #4ec9b0;">Ir al formulario de test →</a></p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script>
        // Auto-scroll al final
        const container = document.querySelector('.log-container');
        container.scrollTop = container.scrollHeight;
    </script>
</body>
</html>

