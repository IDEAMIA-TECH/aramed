<?php
/**
 * ========================================
 * ADMIN - DETALLE DE CAMPAÑA DE NEWSLETTER
 * ========================================
 * 
 * Vista detallada de una campaña con métricas completas
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('newsletter', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener ID de campaña
$campana_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$campana_id) {
    header('Location: campanas.php');
    exit;
}

// Obtener información de la campaña
try {
    $stmt = $pdo->prepare("SELECT c.*, u.nombre as creador_nombre, u.email as creador_email,
                           pt.nombre as plantilla_nombre
                           FROM newsletter_campanas c
                           LEFT JOIN admin_usuarios u ON c.creado_por = u.id
                           LEFT JOIN newsletter_templates pt ON c.plantilla_id = pt.id
                           WHERE c.id = ?");
    $stmt->execute([$campana_id]);
    $campana = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$campana) {
        header('Location: campanas.php');
        exit;
    }
} catch (Exception $e) {
    die('Error al obtener la campaña: ' . $e->getMessage());
}

// Obtener estadísticas de envíos
try {
    $sql_stats = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as enviados,
                    SUM(CASE WHEN estado = 'fallido' THEN 1 ELSE 0 END) as fallidos,
                    SUM(CASE WHEN estado = 'rebotado' THEN 1 ELSE 0 END) as rebotados,
                    SUM(CASE WHEN abierto_at IS NOT NULL THEN 1 ELSE 0 END) as abiertos,
                    SUM(CASE WHEN clic_at IS NOT NULL THEN 1 ELSE 0 END) as clicks
                  FROM newsletter_envios
                  WHERE campana_id = ?";
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute([$campana_id]);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
    
    // Calcular tasas
    $tasa_apertura = $stats['enviados'] > 0 ? ($stats['abiertos'] / $stats['enviados']) * 100 : 0;
    $tasa_clicks = $stats['enviados'] > 0 ? ($stats['clicks'] / $stats['enviados']) * 100 : 0;
    $tasa_clicks_sobre_apertura = $stats['abiertos'] > 0 ? ($stats['clicks'] / $stats['abiertos']) * 100 : 0;
    $tasa_rebote = $stats['total'] > 0 ? ($stats['rebotados'] / $stats['total']) * 100 : 0;
    
} catch (Exception $e) {
    $stats = [
        'total' => 0,
        'enviados' => 0,
        'fallidos' => 0,
        'rebotados' => 0,
        'abiertos' => 0,
        'clicks' => 0
    ];
    $tasa_apertura = 0;
    $tasa_clicks = 0;
    $tasa_clicks_sobre_apertura = 0;
    $tasa_rebote = 0;
}

// Obtener envíos recientes
try {
    $sql_envios = "SELECT e.*, ns.nombre as destinatario_nombre
                   FROM newsletter_envios e
                   LEFT JOIN newsletter_simple ns ON e.destinatario_id = ns.id
                   WHERE e.campana_id = ?
                   ORDER BY e.enviado_at DESC
                   LIMIT 100";
    $stmt_envios = $pdo->prepare($sql_envios);
    $stmt_envios->execute([$campana_id]);
    $envios = $stmt_envios->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $envios = [];
}

// Obtener gráfica de aperturas por hora/día
try {
    $sql_grafica = "SELECT 
                      DATE(enviado_at) as fecha,
                      HOUR(enviado_at) as hora,
                      COUNT(*) as total_envios,
                      SUM(CASE WHEN abierto_at IS NOT NULL THEN 1 ELSE 0 END) as total_aperturas,
                      SUM(CASE WHEN clic_at IS NOT NULL THEN 1 ELSE 0 END) as total_clicks
                    FROM newsletter_envios
                    WHERE campana_id = ? AND enviado_at IS NOT NULL
                    GROUP BY DATE(enviado_at), HOUR(enviado_at)
                    ORDER BY fecha DESC, hora DESC
                    LIMIT 48";
    $stmt_grafica = $pdo->prepare($sql_grafica);
    $stmt_grafica->execute([$campana_id]);
    $datos_grafica = $stmt_grafica->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $datos_grafica = [];
}

$current_page = 'campana-detalle.php';
$current_dir = 'newsletter';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Campaña - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }
        
        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            height: 100%;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
        }
        
        .metric-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .metric-tasa {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .progress-bar-custom {
            height: 30px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>Detalle de Campaña
                            </h2>
                            <p class="mb-0 opacity-75"><?php echo esc($campana['nombre']); ?></p>
                        </div>
                        <a href="campanas.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Campañas
                        </a>
                    </div>
                </div>
                
                <!-- Información General -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Nombre:</strong> <?php echo esc($campana['nombre']); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Plantilla:</strong> <?php echo esc($campana['plantilla_nombre'] ?? 'N/A'); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Asunto:</strong> <?php echo esc($campana['asunto']); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Estado:</strong> 
                                <span class="badge-status bg-<?php echo $campana['estado'] === 'completada' ? 'success' : ($campana['estado'] === 'en_proceso' ? 'warning' : ($campana['estado'] === 'cancelada' ? 'danger' : 'secondary')); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $campana['estado'])); ?>
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Destinatarios:</strong> <?php echo number_format($campana['total_destinatarios']); ?> (<?php echo esc(ucfirst($campana['filtro_estado'])); ?>)
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Creado por:</strong> <?php echo esc($campana['creador_nombre'] ?? 'N/A'); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Fecha de creación:</strong> <?php echo date('d/m/Y H:i:s', strtotime($campana['created_at'])); ?>
                            </div>
                            <?php if ($campana['completada_at']): ?>
                            <div class="col-md-6 mb-3">
                                <strong>Fecha de finalización:</strong> <?php echo date('d/m/Y H:i:s', strtotime($campana['completada_at'])); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Métricas Principales -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="metric-card">
                            <div class="metric-number text-primary"><?php echo number_format($stats['enviados']); ?></div>
                            <div class="metric-label">Enviados</div>
                            <div class="mt-2">
                                <small class="text-muted">de <?php echo number_format($campana['total_destinatarios']); ?> total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="metric-card">
                            <div class="metric-number text-success"><?php echo number_format($stats['abiertos']); ?></div>
                            <div class="metric-label">Aperturas</div>
                            <div class="metric-tasa mt-2"><?php echo number_format($tasa_apertura, 2); ?>%</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="metric-card">
                            <div class="metric-number text-info"><?php echo number_format($stats['clicks']); ?></div>
                            <div class="metric-label">Clicks</div>
                            <div class="metric-tasa mt-2"><?php echo number_format($tasa_clicks, 2); ?>%</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="metric-card">
                            <div class="metric-number text-danger"><?php echo number_format($stats['fallidos']); ?></div>
                            <div class="metric-label">Fallidos</div>
                            <div class="metric-tasa mt-2"><?php echo $stats['enviados'] > 0 ? number_format(($stats['fallidos'] / $stats['enviados']) * 100, 2) : 0; ?>%</div>
                        </div>
                    </div>
                </div>
                
                <!-- Tasas de Conversión -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Tasa de Apertura</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress" style="height: 40px;">
                                    <div class="progress-bar bg-success progress-bar-custom" role="progressbar" 
                                         style="width: <?php echo min($tasa_apertura, 100); ?>%" 
                                         aria-valuenow="<?php echo $tasa_apertura; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo number_format($tasa_apertura, 2); ?>%
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <?php echo number_format($stats['abiertos']); ?> de <?php echo number_format($stats['enviados']); ?> emails abiertos
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="bi bi-cursor me-2"></i>Tasa de Clicks</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress" style="height: 40px;">
                                    <div class="progress-bar bg-info progress-bar-custom" role="progressbar" 
                                         style="width: <?php echo min($tasa_clicks, 100); ?>%" 
                                         aria-valuenow="<?php echo $tasa_clicks; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo number_format($tasa_clicks, 2); ?>%
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <?php echo number_format($stats['clicks']); ?> de <?php echo number_format($stats['enviados']); ?> emails con clicks
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Métricas Adicionales -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Tasa de Clicks sobre Apertura</h6>
                                <div class="metric-tasa"><?php echo number_format($tasa_clicks_sobre_apertura, 2); ?>%</div>
                                <small class="text-muted">
                                    <?php echo number_format($stats['clicks']); ?> clicks de <?php echo number_format($stats['abiertos']); ?> aperturas
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Tasa de Rebote</h6>
                                <div class="metric-tasa text-danger"><?php echo number_format($tasa_rebote, 2); ?>%</div>
                                <small class="text-muted">
                                    <?php echo number_format($stats['rebotados']); ?> rebotados
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Tasa de Entrega</h6>
                                <div class="metric-tasa text-success">
                                    <?php 
                                    $tasa_entrega = $campana['total_destinatarios'] > 0 ? 
                                        (($stats['enviados'] / $campana['total_destinatarios']) * 100) : 0;
                                    echo number_format($tasa_entrega, 2); 
                                    ?>%
                                </div>
                                <small class="text-muted">
                                    <?php echo number_format($stats['enviados']); ?> de <?php echo number_format($campana['total_destinatarios']); ?> entregados
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfica de Actividad -->
                <?php if (!empty($datos_grafica)): ?>
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Actividad en el Tiempo</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" height="100"></canvas>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Lista de Envíos -->
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Envíos Individuales</h5>
                            <span class="badge bg-primary"><?php echo count($envios); ?> registros</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Destinatario</th>
                                        <th>Estado</th>
                                        <th>Enviado</th>
                                        <th>Abierto</th>
                                        <th>Click</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($envios)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay envíos registrados aún
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($envios as $envio): ?>
                                    <tr>
                                        <td><?php echo esc($envio['email']); ?></td>
                                        <td><?php echo esc($envio['destinatario_nombre'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $envio['estado'] === 'enviado' ? 'success' : 
                                                    ($envio['estado'] === 'fallido' ? 'danger' : 
                                                    ($envio['estado'] === 'rebotado' ? 'warning' : 
                                                    ($envio['estado'] === 'abierto' ? 'info' : 'secondary'))); 
                                            ?>">
                                                <?php echo ucfirst($envio['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($envio['enviado_at']): ?>
                                                <small><?php echo date('d/m/Y H:i', strtotime($envio['enviado_at'])); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($envio['abierto_at']): ?>
                                                <span class="text-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    <small><?php echo date('d/m/Y H:i', strtotime($envio['abierto_at'])); ?></small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($envio['clic_at']): ?>
                                                <span class="text-info">
                                                    <i class="bi bi-cursor me-1"></i>
                                                    <small><?php echo date('d/m/Y H:i', strtotime($envio['clic_at'])); ?></small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($envio['mensaje_error']): ?>
                                                <small class="text-danger" title="<?php echo esc($envio['mensaje_error']); ?>">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                    <?php echo esc(truncateText($envio['mensaje_error'], 50)); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (!empty($datos_grafica)): ?>
        // Gráfica de actividad
        const ctx = document.getElementById('activityChart');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($d) { return date('d/m H:i', strtotime($d['fecha'] . ' ' . $d['hora'] . ':00:00')); }, array_reverse($datos_grafica))); ?>,
                datasets: [{
                    label: 'Enviados',
                    data: <?php echo json_encode(array_map(function($d) { return $d['total_envios']; }, array_reverse($datos_grafica))); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Aperturas',
                    data: <?php echo json_encode(array_map(function($d) { return $d['total_aperturas']; }, array_reverse($datos_grafica))); ?>,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Clicks',
                    data: <?php echo json_encode(array_map(function($d) { return $d['total_clicks']; }, array_reverse($datos_grafica))); ?>,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>

