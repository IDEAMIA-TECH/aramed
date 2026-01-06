<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE NEWSLETTER SIMPLE
 * ========================================
 * 
 * Panel para gestionar suscripciones del newsletter simple
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

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('newsletter', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Manejar acciones
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cambiar_estado'])) {
        $id = (int)$_POST['id'];
        $nuevo_estado = $_POST['estado'];
        
        $sql = "UPDATE newsletter_simple SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([$nuevo_estado, $id]);
        
        if ($resultado) {
            $mensaje = 'Estado actualizado correctamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar el estado';
            $tipo_mensaje = 'danger';
        }
    } elseif (isset($_POST['eliminar_suscripcion'])) {
        $id = (int)$_POST['id'];
        
        $sql = "DELETE FROM newsletter_simple WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([$id]);
        
        if ($resultado) {
            $mensaje = 'Suscripción eliminada correctamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al eliminar la suscripción';
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
$filtro_fuente = isset($_GET['fuente']) ? $_GET['fuente'] : 'todos';
$busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_estado !== 'todos') {
    $where_conditions[] = 'status = ?';
    $params[] = $filtro_estado;
}

if ($filtro_fuente !== 'todos') {
    $where_conditions[] = 'source = ?';
    $params[] = $filtro_fuente;
}

if (!empty($busqueda)) {
    $where_conditions[] = '(email LIKE ? OR nombre LIKE ?)';
    $search_term = '%' . $busqueda . '%';
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener suscripciones
$sql = "
    SELECT * FROM newsletter_simple 
    {$where_clause}
    ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$suscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$sql_stats = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'activo' THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN status = 'inactivo' THEN 1 ELSE 0 END) as inactivos,
        SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados
    FROM newsletter_simple
";

$stmt_stats = $pdo->prepare($sql_stats);
$stmt_stats->execute();
$estadisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);

// Obtener fuentes únicas para filtro
$sql_fuentes = "SELECT DISTINCT source FROM newsletter_simple WHERE source IS NOT NULL AND source != ''";
$stmt_fuentes = $pdo->prepare($sql_fuentes);
$stmt_fuentes->execute();
$fuentes = $stmt_fuentes->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Simple - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/design/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/design/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/design/favicon-32x32.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
            --border-color: #dee2e6;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
            --border-radius: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            min-height: 100vh;
            border-right: 1px solid var(--border-color);
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
        }

        .admin-content {
            background: transparent;
            min-height: 100vh;
            padding: 2rem;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: var(--shadow);
        }

        .page-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-color);
        }

        .stat-card.success::before {
            background: var(--success-color);
        }

        .stat-card.warning::before {
            background: var(--warning-color);
        }

        .stat-card.danger::before {
            background: var(--danger-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--secondary-color);
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filters-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .filters-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .filters-body {
            padding: 1.5rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }

        .btn-outline-secondary {
            border: 2px solid var(--border-color);
            color: var(--secondary-color);
        }

        .btn-outline-secondary:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .subscription-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .subscription-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--border-color);
            transition: var(--transition);
        }

        .subscription-card.active::before {
            background: var(--success-color);
        }

        .subscription-card.inactive::before {
            background: var(--secondary-color);
        }

        .subscription-card.unsubscribed::before {
            background: var(--danger-color);
        }

        .subscription-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .subscription-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .subscription-body {
            padding: 1.5rem;
        }

        .info-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .info-section h6 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-section.success {
            border-left-color: var(--success-color);
        }

        .info-section.success h6 {
            color: var(--success-color);
        }

        .info-section.warning {
            border-left-color: var(--warning-color);
        }

        .info-section.warning h6 {
            color: var(--warning-color);
        }

        .info-section.secondary {
            border-left-color: var(--secondary-color);
        }

        .info-section.secondary h6 {
            color: var(--secondary-color);
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            margin-bottom: 0.5rem;
        }

        .info-item strong {
            color: var(--dark-bg);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-item p {
            margin: 0;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%) !important;
        }

        .badge.bg-secondary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #6c757d 100%) !important;
        }

        .badge.bg-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #e74c3c 100%) !important;
        }

        .badge.bg-info {
            background: linear-gradient(135deg, var(--info-color) 0%, #20c997 100%) !important;
        }

        .actions-panel {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .nav-link {
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: var(--transition);
            font-weight: 500;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateX(5px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow);
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        @media (max-width: 768px) {
            .admin-content {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .info-row {
                grid-template-columns: 1fr;
            }
            
            .subscription-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/includes/admin_menu.php'; ?>

            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-9 admin-content p-4">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2>
                                <i class="bi bi-envelope-open me-2"></i>Newsletter Simple
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona las suscripciones del formulario simple</p>
                        </div>
                        <div class="text-end">
                            <small class="opacity-75">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo esc($current_user['nombre']); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Mensajes -->
                <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo esc($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Acciones Rápidas -->
                <div class="card mb-4" style="background: white; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-lightning-charge me-2"></i>Acciones Rápidas</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="newsletter/import.php" class="btn btn-outline-primary">
                                <i class="bi bi-upload me-2"></i>Importar CSV
                            </a>
                            <a href="newsletter/export.php" class="btn btn-outline-success">
                                <i class="bi bi-download me-2"></i>Exportar CSV
                            </a>
                            <a href="newsletter/plantillas.php" class="btn btn-outline-info">
                                <i class="bi bi-file-earmark-code me-2"></i>Plantillas HTML
                            </a>
                            <a href="newsletter/config.php" class="btn btn-outline-secondary">
                                <i class="bi bi-gear me-2"></i>Configuración
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($estadisticas['total']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-envelope-open me-1"></i>Total Suscripciones
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($estadisticas['activos']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-check-circle me-1"></i>Activas
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo number_format($estadisticas['inactivos']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-pause-circle me-1"></i>Inactivas
                        </div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-number"><?php echo number_format($estadisticas['cancelados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-x-circle me-1"></i>Canceladas
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filters-card">
                    <div class="filters-header">
                        <h5 class="mb-0">
                            <i class="bi bi-funnel me-2"></i>Filtros de Búsqueda
                        </h5>
                    </div>
                    <div class="filters-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="estado" class="form-label fw-bold">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="todos" <?php echo $filtro_estado === 'todos' ? 'selected' : ''; ?>>Todos los estados</option>
                                    <option value="activo" <?php echo $filtro_estado === 'activo' ? 'selected' : ''; ?>>Activas</option>
                                    <option value="inactivo" <?php echo $filtro_estado === 'inactivo' ? 'selected' : ''; ?>>Inactivas</option>
                                    <option value="cancelado" <?php echo $filtro_estado === 'cancelado' ? 'selected' : ''; ?>>Canceladas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fuente" class="form-label fw-bold">Fuente</label>
                                <select class="form-select" id="fuente" name="fuente">
                                    <option value="todos" <?php echo $filtro_fuente === 'todos' ? 'selected' : ''; ?>>Todas las fuentes</option>
                                    <?php foreach ($fuentes as $fuente): ?>
                                    <option value="<?php echo esc($fuente); ?>" <?php echo $filtro_fuente === $fuente ? 'selected' : ''; ?>>
                                        <?php echo esc(ucfirst($fuente)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="busqueda" class="form-label fw-bold">Búsqueda</label>
                                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                                       value="<?php echo esc($busqueda); ?>" placeholder="Email o nombre">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel me-1"></i>Aplicar
                                </button>
                                <a href="newsletter-simple.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de suscripciones -->
                <div class="row">
                    <?php if (!empty($suscripciones)): ?>
                        <?php foreach ($suscripciones as $suscripcion): ?>
                        <div class="col-12 mb-3">
                            <div class="subscription-card <?php echo $suscripcion['status']; ?>">
                                <div class="subscription-header">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <div>
                                            <h6 class="mb-1 fw-bold">
                                                <i class="bi bi-person-circle me-2"></i>
                                                <?php echo esc($suscripcion['nombre'] ?: 'Sin nombre'); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i>
                                                <?php echo esc($suscripcion['email']); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?php echo $suscripcion['status'] === 'activo' ? 'success' : ($suscripcion['status'] === 'inactivo' ? 'secondary' : 'danger'); ?> me-1">
                                                <?php echo ucfirst($suscripcion['status']); ?>
                                            </span>
                                            <?php if (!empty($suscripcion['source'])): ?>
                                            <span class="badge bg-info">
                                                <?php echo esc(ucfirst($suscripcion['source'])); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="subscription-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Información Básica -->
                                            <div class="info-section">
                                                <h6>
                                                    <i class="bi bi-info-circle me-1"></i>Información Básica
                                                </h6>
                                                <div class="info-row">
                                                    <div class="info-item">
                                                        <strong>Email:</strong>
                                                        <p>
                                                            <a href="mailto:<?php echo esc($suscripcion['email']); ?>" class="text-decoration-none">
                                                                <?php echo esc($suscripcion['email']); ?>
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>Fecha Registro:</strong>
                                                        <p><?php echo date('d M Y H:i', strtotime($suscripcion['created_at'])); ?></p>
                                                    </div>
                                                    <?php if (!empty($suscripcion['source'])): ?>
                                                    <div class="info-item">
                                                        <strong>Fuente:</strong>
                                                        <p><span class="badge bg-info"><?php echo esc(ucfirst($suscripcion['source'])); ?></span></p>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Metadata -->
                                            <div class="info-section secondary">
                                                <h6>
                                                    <i class="bi bi-gear me-1"></i>Metadata
                                                </h6>
                                                <div class="info-row">
                                                    <div class="info-item">
                                                        <strong>IP Address:</strong>
                                                        <p><?php echo esc($suscripcion['ip_address']); ?></p>
                                                    </div>
                                                    <?php if (!empty($suscripcion['user_agent'])): ?>
                                                    <div class="info-item" style="grid-column: 1 / -1;">
                                                        <strong>User Agent:</strong>
                                                        <p class="text-muted small"><?php echo esc(truncateText($suscripcion['user_agent'], 50)); ?></p>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="actions-panel">
                                                <h6 class="mb-3 fw-bold">
                                                    <i class="bi bi-gear me-1"></i>Acciones
                                                </h6>
                                                
                                                <form method="POST" class="mb-3">
                                                    <input type="hidden" name="id" value="<?php echo $suscripcion['id']; ?>">
                                                    <label class="form-label fw-bold small">Cambiar Estado:</label>
                                                    <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="activo" <?php echo $suscripcion['status'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                                        <option value="inactivo" <?php echo $suscripcion['status'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                                        <option value="cancelado" <?php echo $suscripcion['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                    </select>
                                                    <input type="hidden" name="cambiar_estado" value="1">
                                                </form>
                                                
                                                <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta suscripción?')">
                                                    <input type="hidden" name="id" value="<?php echo $suscripcion['id']; ?>">
                                                    <button type="submit" name="eliminar_suscripcion" class="btn btn-danger btn-sm w-100">
                                                        <i class="bi bi-trash me-1"></i>Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="bi bi-envelope-open"></i>
                                <h3>No hay suscripciones</h3>
                                <p>No se encontraron suscripciones con los filtros seleccionados.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
