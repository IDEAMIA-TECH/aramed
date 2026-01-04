<?php
/**
 * ========================================
 * ADMIN - VISOR DE LOGS
 * ========================================
 * 
 * Permite ver los logs de errores PHP desde el navegador
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/auth_check.php';

// Verificar permisos (solo admin)
if (!isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] !== 'admin') {
    header('Location: sin-permiso.php');
    exit;
}

$log_file = __DIR__ . '/../logs/php-errors.log';
$lines_to_show = isset($_GET['lines']) ? (int)$_GET['lines'] : 100;
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';

// Leer logs
$logs = [];
$log_exists = file_exists($log_file);

if ($log_exists && is_readable($log_file)) {
    $file_content = file_get_contents($log_file);
    $all_lines = explode("\n", $file_content);
    
    // Aplicar filtro si existe
    if (!empty($filter)) {
        $all_lines = array_filter($all_lines, function($line) use ($filter) {
            return stripos($line, $filter) !== false;
        });
    }
    
    // Obtener últimas N líneas
    $all_lines = array_values($all_lines);
    $logs = array_slice($all_lines, -$lines_to_show);
    $logs = array_reverse($logs); // Más recientes primero
} else {
    $logs = ["⚠ El archivo de logs no existe o no es legible: " . $log_file];
}

// Limpiar logs (solo admin)
if (isset($_POST['clear_logs']) && $_POST['clear_logs'] === '1') {
    if (file_exists($log_file) && is_writable($log_file)) {
        file_put_contents($log_file, "# Logs limpiados el " . date('Y-m-d H:i:s') . "\n\n");
        header('Location: view-logs.php?cleared=1');
        exit;
    }
}

$file_size = $log_exists ? filesize($log_file) : 0;
$file_size_mb = round($file_size / 1024 / 1024, 2);
$file_modified = $log_exists ? date('Y-m-d H:i:s', filemtime($log_file)) : 'N/A';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Logs - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        .log-line {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-left: 3px solid transparent;
            margin-bottom: 0.25rem;
        }
        .log-line:hover {
            background-color: #f8f9fa;
        }
        .log-error {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }
        .log-warning {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }
        .log-info {
            border-left-color: #0dcaf0;
            background-color: #f0f9ff;
        }
        .log-success {
            border-left-color: #198754;
            background-color: #f0fdf4;
        }
        .log-container {
            max-height: 70vh;
            overflow-y: auto;
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
        }
        pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/admin_menu.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-file-text me-2"></i>
                        Logs de Errores PHP
                    </h1>
                    <div>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
                
                <?php if (isset($_GET['cleared'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    Logs limpiados correctamente
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Información del archivo -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Estado:</strong><br>
                                <?php if ($log_exists): ?>
                                    <span class="badge bg-success">Archivo existe</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Archivo no existe</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Tamaño:</strong><br>
                                <?php echo $file_size_mb; ?> MB (<?php echo number_format($file_size); ?> bytes)
                            </div>
                            <div class="col-md-3">
                                <strong>Última modificación:</strong><br>
                                <?php echo $file_modified; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Ubicación:</strong><br>
                                <code style="font-size: 0.75rem;"><?php echo $log_file; ?></code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros y controles -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="lines" class="form-label">Líneas a mostrar:</label>
                                <select class="form-select" id="lines" name="lines">
                                    <option value="50" <?php echo $lines_to_show == 50 ? 'selected' : ''; ?>>50</option>
                                    <option value="100" <?php echo $lines_to_show == 100 ? 'selected' : ''; ?>>100</option>
                                    <option value="200" <?php echo $lines_to_show == 200 ? 'selected' : ''; ?>>200</option>
                                    <option value="500" <?php echo $lines_to_show == 500 ? 'selected' : ''; ?>>500</option>
                                    <option value="1000" <?php echo $lines_to_show == 1000 ? 'selected' : ''; ?>>1000</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="filter" class="form-label">Filtrar por texto:</label>
                                <input type="text" class="form-control" id="filter" name="filter" 
                                       value="<?php echo esc($filter); ?>" 
                                       placeholder="Buscar en los logs...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>
                                    Filtrar
                                </button>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <div class="d-flex gap-2">
                            <a href="view-logs.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Actualizar
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de limpiar todos los logs?');">
                                <input type="hidden" name="clear_logs" value="1">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash me-2"></i>
                                    Limpiar Logs
                                </button>
                            </form>
                            <a href="view-logs.php?download=1" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download me-2"></i>
                                Descargar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Logs -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Logs (<?php echo count($logs); ?> líneas)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="log-container">
                            <?php if (empty($logs)): ?>
                                <div class="text-center text-muted p-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No hay logs para mostrar</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($logs as $line): ?>
                                    <?php
                                    $line_class = '';
                                    if (stripos($line, 'error') !== false || stripos($line, 'fatal') !== false) {
                                        $line_class = 'log-error';
                                    } elseif (stripos($line, 'warning') !== false) {
                                        $line_class = 'log-warning';
                                    } elseif (stripos($line, 'info') !== false || stripos($line, '=== ') !== false) {
                                        $line_class = 'log-info';
                                    } elseif (stripos($line, 'success') !== false || stripos($line, '✓') !== false) {
                                        $line_class = 'log-success';
                                    }
                                    ?>
                                    <div class="log-line <?php echo $line_class; ?>">
                                        <pre><?php echo esc($line); ?></pre>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-scroll al final (logs más recientes)
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.log-container');
            if (container) {
                container.scrollTop = 0; // Los logs más recientes están arriba
            }
        });
        
        // Auto-refresh cada 30 segundos
        setTimeout(function() {
            if (!document.querySelector('.log-container').querySelector('.log-line')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>

