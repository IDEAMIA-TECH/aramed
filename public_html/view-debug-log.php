<?php
/**
 * Visor de logs de debug
 */

// Configuración
$log_file = __DIR__ . '/logs/debug.log';
$lines_to_show = 100; // Mostrar últimas 100 líneas

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Debug Log - Aramed</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Monaco', 'Consolas', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        h1 {
            color: #4ec9b0;
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .info {
            background: #252526;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4ec9b0;
        }
        
        .info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .actions {
            margin-bottom: 20px;
        }
        
        .btn {
            background: #0e639c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #1177bb;
        }
        
        .btn-danger {
            background: #c5402f;
        }
        
        .btn-danger:hover {
            background: #d64a38;
        }
        
        .log-container {
            background: #1e1e1e;
            border: 1px solid #3c3c3c;
            border-radius: 5px;
            padding: 20px;
            overflow-x: auto;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .log-line {
            padding: 4px 0;
            border-bottom: 1px solid #2d2d2d;
            font-size: 13px;
        }
        
        .log-line:last-child {
            border-bottom: none;
        }
        
        .log-line.error {
            color: #f48771;
            background: rgba(244, 135, 113, 0.1);
            padding: 4px 8px;
        }
        
        .log-line.success {
            color: #89d185;
        }
        
        .log-line.warning {
            color: #dcdcaa;
        }
        
        .timestamp {
            color: #858585;
            margin-right: 10px;
        }
        
        .empty {
            text-align: center;
            padding: 40px;
            color: #858585;
            font-size: 16px;
        }
        
        .stats {
            background: #252526;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #4ec9b0;
        }
        
        .stat-label {
            font-size: 12px;
            color: #858585;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Log Viewer</h1>
        
        <div class="info">
            <p><strong>Archivo:</strong> <?php echo $log_file; ?></p>
            <p><strong>Fecha/Hora:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Estado:</strong> 
                <?php if (file_exists($log_file)): ?>
                    <span style="color: #89d185;">✅ Archivo existe</span>
                <?php else: ?>
                    <span style="color: #f48771;">❌ Archivo no existe</span>
                <?php endif; ?>
            </p>
        </div>

        <?php if (file_exists($log_file)): ?>
            <?php
                $all_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $total_lines = count($all_lines);
                $lines = array_slice($all_lines, -$lines_to_show);
                
                // Contar tipos de mensajes
                $errors = 0;
                $success = 0;
                foreach ($lines as $line) {
                    if (stripos($line, '❌') !== false || stripos($line, 'error') !== false) {
                        $errors++;
                    } else if (stripos($line, '✅') !== false || stripos($line, 'success') !== false) {
                        $success++;
                    }
                }
            ?>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_lines; ?></div>
                    <div class="stat-label">Total de líneas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #f48771;"><?php echo $errors; ?></div>
                    <div class="stat-label">Errores</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #89d185;"><?php echo $success; ?></div>
                    <div class="stat-label">Éxitos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($lines); ?></div>
                    <div class="stat-label">Mostrando últimas</div>
                </div>
            </div>

            <div class="actions">
                <a href="?refresh=1" class="btn">🔄 Refrescar</a>
                <a href="?clear=1" class="btn btn-danger" onclick="return confirm('¿Estás seguro de borrar el log?')">🗑️ Limpiar Log</a>
            </div>

            <div class="log-container">
                <?php if (empty($lines)): ?>
                    <div class="empty">📝 No hay logs disponibles</div>
                <?php else: ?>
                    <?php foreach ($lines as $line): ?>
                        <?php
                            $class = '';
                            if (stripos($line, '❌') !== false || stripos($line, 'error') !== false || stripos($line, 'failed') !== false) {
                                $class = 'error';
                            } else if (stripos($line, '✅') !== false || stripos($line, 'success') !== false || stripos($line, 'ok') !== false) {
                                $class = 'success';
                            } else if (stripos($line, '⚠️') !== false || stripos($line, 'warning') !== false) {
                                $class = 'warning';
                            }
                            
                            // Resaltar timestamp
                            $line = htmlspecialchars($line);
                            $line = preg_replace('/\[([\d\-\s:]+)\]/', '<span class="timestamp">[$1]</span>', $line);
                        ?>
                        <div class="log-line <?php echo $class; ?>"><?php echo $line; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <script>
                // Auto-scroll al final
                window.onload = function() {
                    const container = document.querySelector('.log-container');
                    container.scrollTop = container.scrollHeight;
                };
            </script>

        <?php else: ?>
            <div class="empty">
                <p>📝 El archivo de log no existe todavía.</p>
                <p style="margin-top: 10px;">Se creará automáticamente cuando haya eventos que registrar.</p>
            </div>
        <?php endif; ?>

        <?php
        // Limpiar log si se solicita
        if (isset($_GET['clear']) && file_exists($log_file)) {
            unlink($log_file);
            echo '<script>window.location.href = window.location.pathname;</script>';
        }
        ?>
    </div>
</body>
</html>

