<?php
/**
 * VISOR DE LOGS - Versión Simplificada
 * Sin autenticación para debugging
 */

// Configurar logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Intentar usar el archivo de log configurado en PHP primero
$log_file_php = ini_get('error_log');
$log_file_local = __DIR__ . '/../logs/php-errors.log';

// Usar el archivo de PHP si existe y es legible, sino usar el local
if (!empty($log_file_php) && file_exists($log_file_php) && is_readable($log_file_php)) {
    $log_file = $log_file_php;
} else {
    $log_file = $log_file_local;
}

// Leer logs
$logs = [];
$log_exists = file_exists($log_file);
$lines_to_show = isset($_GET['lines']) ? (int)$_GET['lines'] : 100;
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';

if ($log_exists && is_readable($log_file)) {
    try {
        $file_content = @file_get_contents($log_file);
        if ($file_content !== false) {
            $all_lines = explode("\n", $file_content);
            
            // Aplicar filtro si existe
            if (!empty($filter)) {
                $all_lines = array_filter($all_lines, function($line) use ($filter) {
                    return stripos($line, $filter) !== false;
                });
            }
            
            // Obtener últimas N líneas
            $all_lines = array_values($all_lines);
            $total_lines = count($all_lines);
            $start = max(0, $total_lines - $lines_to_show);
            $logs = array_slice($all_lines, $start);
            $logs = array_reverse($logs);
        }
    } catch (Exception $e) {
        $logs = ["Error: " . $e->getMessage()];
    }
} else {
    $logs = ["El archivo de logs no existe o no es legible"];
}

$file_size = $log_exists ? @filesize($log_file) : 0;
$file_size_mb = $file_size > 0 ? round($file_size / 1024 / 1024, 2) : 0;
$file_modified = $log_exists ? @date('Y-m-d H:i:s', filemtime($log_file)) : 'N/A';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Logs - Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .log-line {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-left: 3px solid #ddd;
            margin-bottom: 0.25rem;
        }
        .log-error { border-left-color: #dc3545; background-color: #fff5f5; }
        .log-warning { border-left-color: #ffc107; background-color: #fffbf0; }
        .log-info { border-left-color: #0dcaf0; background-color: #f0f9ff; }
        .log-container {
            max-height: 70vh;
            overflow-y: auto;
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
        }
        pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">📋 Logs de Errores PHP</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Estado:</strong> 
                                <?php echo $log_exists ? '<span class="badge bg-success">Existe</span>' : '<span class="badge bg-danger">No existe</span>'; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Tamaño:</strong> <?php echo $file_size_mb; ?> MB
                            </div>
                            <div class="col-md-3">
                                <strong>Modificado:</strong> <?php echo $file_modified; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Ubicación:</strong><br>
                                <code style="font-size: 0.7rem;" title="<?php echo htmlspecialchars($log_file, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars(basename($log_file), ENT_QUOTES, 'UTF-8'); ?>
                                </code>
                                <?php if ($log_file !== $log_file_local): ?>
                                <br><small class="text-muted">(Log del servidor)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <form method="GET" class="mb-3">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <select name="lines" class="form-select">
                                        <option value="50" <?php echo $lines_to_show == 50 ? 'selected' : ''; ?>>50 líneas</option>
                                        <option value="100" <?php echo $lines_to_show == 100 ? 'selected' : ''; ?>>100 líneas</option>
                                        <option value="200" <?php echo $lines_to_show == 200 ? 'selected' : ''; ?>>200 líneas</option>
                                        <option value="500" <?php echo $lines_to_show == 500 ? 'selected' : ''; ?>>500 líneas</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="filter" class="form-control" 
                                           value="<?php echo htmlspecialchars($filter, ENT_QUOTES, 'UTF-8'); ?>" 
                                           placeholder="Filtrar por texto...">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="log-container">
                            <?php if (empty($logs)): ?>
                                <div class="text-center text-muted p-4">
                                    <p>No hay logs para mostrar</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($logs as $line): ?>
                                    <?php
                                    $line_class = '';
                                    if (stripos($line, 'error') !== false || stripos($line, 'fatal') !== false) {
                                        $line_class = 'log-error';
                                    } elseif (stripos($line, 'warning') !== false) {
                                        $line_class = 'log-warning';
                                    } elseif (stripos($line, 'info') !== false || stripos($line, '===') !== false) {
                                        $line_class = 'log-info';
                                    }
                                    ?>
                                    <div class="log-line <?php echo $line_class; ?>">
                                        <pre><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></pre>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="index.php" class="btn btn-secondary">Volver al Dashboard</a>
                    <a href="view-logs.php" class="btn btn-outline-primary">Versión Completa (con auth)</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

