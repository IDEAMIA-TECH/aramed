<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE COMENTARIOS DEL BLOG
 * ========================================
 * 
 * Panel de administración para gestionar comentarios del blog
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

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener parámetros
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Manejar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['aprobar_comentario'])) {
        $comentario_id = (int)$_POST['comentario_id'];
        $sql = "UPDATE blog_comentarios SET estado = 'aprobado' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$comentario_id]);
        $mensaje = 'Comentario aprobado correctamente';
    } elseif (isset($_POST['rechazar_comentario'])) {
        $comentario_id = (int)$_POST['comentario_id'];
        $sql = "UPDATE blog_comentarios SET estado = 'rechazado' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$comentario_id]);
        $mensaje = 'Comentario rechazado correctamente';
    } elseif (isset($_POST['eliminar_comentario'])) {
        $comentario_id = (int)$_POST['comentario_id'];
        $sql = "DELETE FROM blog_comentarios WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$comentario_id]);
        $mensaje = 'Comentario eliminado correctamente';
    }
}

// Obtener filtros
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
$articulo_filtro = isset($_GET['articulo']) ? (int)$_GET['articulo'] : 0;

// Construir consulta de comentarios
$where_conditions = [];
$params = [];

if ($estado_filtro !== 'todos') {
    $where_conditions[] = 'c.estado = ?';
    $params[] = $estado_filtro;
}

if ($articulo_filtro > 0) {
    $where_conditions[] = 'c.articulo_id = ?';
    $params[] = $articulo_filtro;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener comentarios
$sql = "
    SELECT c.*, a.titulo as articulo_titulo, a.slug as articulo_slug
    FROM blog_comentarios c
    LEFT JOIN blog_articulos a ON c.articulo_id = a.id
    {$where_clause}
    ORDER BY c.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener artículos para filtro
$sql_articulos = "SELECT id, titulo FROM blog_articulos ORDER BY titulo ASC";
$stmt_articulos = $pdo->prepare($sql_articulos);
$stmt_articulos->execute();
$articulos = $stmt_articulos->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$sql_stats = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
        SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados
    FROM blog_comentarios
";

$stmt_stats = $pdo->prepare($sql_stats);
$stmt_stats->execute();
$estadisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Comentarios - Admin Blog</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
            --border-color: #dee2e6;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
            --border-radius: 12px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
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

        .comment-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .comment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--border-color);
            transition: var(--transition);
        }

        .comment-card.pendiente::before {
            background: var(--warning-color);
        }

        .comment-card.aprobado::before {
            background: var(--success-color);
        }

        .comment-card.rechazado::before {
            background: var(--danger-color);
        }

        .comment-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .comment-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .comment-body {
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

        .badge.bg-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #ffc107 100%) !important;
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
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
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

        .comment-meta {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }

        .status-badge {
            font-size: 0.75rem;
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
            
            .comment-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-0">
                <div class="p-3">
                    <h5 class="mb-4">
                        <i class="bi bi-gear me-2"></i>Admin Blog
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-list-ul me-2"></i>Artículos
                        </a>
                        <a class="nav-link" href="categorias.php">
                            <i class="bi bi-folder me-2"></i>Categorías
                        </a>
                        <a class="nav-link active" href="comentarios.php">
                            <i class="bi bi-chat-dots me-2"></i>Comentarios
                        </a>
                        <a class="nav-link" href="../../blog.php" target="_blank">
                            <i class="bi bi-eye me-2"></i>Ver Blog
                        </a>
                        <hr>
                        <a class="nav-link" href="../../index.php">
                            <i class="bi bi-house me-2"></i>Volver al Sitio
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-10 admin-content p-4">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2>
                                <i class="bi bi-chat-dots me-2"></i>Gestión de Comentarios
                            </h2>
                            <p class="mb-0 opacity-75">Administra y modera los comentarios del blog</p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-warning"><?php echo $estadisticas['pendientes']; ?> Pendientes</span>
                            <span class="badge bg-success"><?php echo $estadisticas['aprobados']; ?> Aprobados</span>
                            <span class="badge bg-danger"><?php echo $estadisticas['rechazados']; ?> Rechazados</span>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($estadisticas['total']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-chat-dots me-1"></i>Total Comentarios
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo number_format($estadisticas['pendientes']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-clock me-1"></i>Pendientes
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($estadisticas['aprobados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-check-circle me-1"></i>Aprobados
                        </div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-number"><?php echo number_format($estadisticas['rechazados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-x-circle me-1"></i>Rechazados
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
                            <div class="col-md-4">
                                <label for="estado" class="form-label fw-bold">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="todos" <?php echo $estado_filtro === 'todos' ? 'selected' : ''; ?>>Todos los estados</option>
                                    <option value="pendiente" <?php echo $estado_filtro === 'pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                                    <option value="aprobado" <?php echo $estado_filtro === 'aprobado' ? 'selected' : ''; ?>>Aprobados</option>
                                    <option value="rechazado" <?php echo $estado_filtro === 'rechazado' ? 'selected' : ''; ?>>Rechazados</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="articulo" class="form-label fw-bold">Artículo</label>
                                <select class="form-select" id="articulo" name="articulo">
                                    <option value="0">Todos los artículos</option>
                                    <?php foreach ($articulos as $articulo): ?>
                                    <option value="<?php echo $articulo['id']; ?>" <?php echo $articulo_filtro == $articulo['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc(truncateText($articulo['titulo'], 50)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel me-1"></i>Aplicar
                                </button>
                                <a href="comentarios.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de comentarios -->
                <div class="row">
                    <?php if (!empty($comentarios)): ?>
                        <?php foreach ($comentarios as $comentario): ?>
                        <div class="col-12 mb-3">
                            <div class="comment-card <?php echo $comentario['estado']; ?>">
                                <div class="comment-header">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <div>
                                            <h6 class="mb-1 fw-bold">
                                                <i class="bi bi-person-circle me-2"></i>
                                                <?php echo esc($comentario['nombre']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i>
                                                <?php echo esc($comentario['email']); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge 
                                                <?php 
                                                echo $comentario['estado'] === 'pendiente' ? 'bg-warning' : 
                                                     ($comentario['estado'] === 'aprobado' ? 'bg-success' : 'bg-danger'); 
                                                ?>">
                                                <?php 
                                                echo $comentario['estado'] === 'pendiente' ? 'Pendiente' : 
                                                     ($comentario['estado'] === 'aprobado' ? 'Aprobado' : 'Rechazado'); 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Comentario -->
                                            <div class="info-section">
                                                <h6>
                                                    <i class="bi bi-chat-dots me-1"></i>Comentario
                                                </h6>
                                                <div class="comment-content" style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                                                    <?php echo nl2br(esc($comentario['comentario'])); ?>
                                                </div>
                                            </div>

                                            <!-- Información del Comentario -->
                                            <div class="info-section success">
                                                <h6>
                                                    <i class="bi bi-info-circle me-1"></i>Información del Comentario
                                                </h6>
                                                <div class="info-row">
                                                    <div class="info-item">
                                                        <strong>Email:</strong>
                                                        <p>
                                                            <a href="mailto:<?php echo esc($comentario['email']); ?>" class="text-decoration-none">
                                                                <?php echo esc($comentario['email']); ?>
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>Fecha:</strong>
                                                        <p><?php echo date('d M Y H:i', strtotime($comentario['created_at'])); ?></p>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>IP Address:</strong>
                                                        <p><?php echo esc($comentario['ip_address']); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Artículo Relacionado -->
                                            <div class="info-section secondary">
                                                <h6>
                                                    <i class="bi bi-newspaper me-1"></i>Artículo Relacionado
                                                </h6>
                                                <div class="info-item">
                                                    <strong>Título:</strong>
                                                    <p>
                                                        <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $comentario['articulo_slug']); ?>" 
                                                           target="_blank" class="text-decoration-none fw-bold">
                                                            <?php echo esc(truncateText($comentario['articulo_titulo'], 60)); ?>
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="actions-panel">
                                                <h6 class="mb-3 fw-bold">
                                                    <i class="bi bi-gear me-1"></i>Acciones
                                                </h6>
                                                
                                                <?php if ($comentario['estado'] === 'pendiente'): ?>
                                                <div class="d-grid gap-2 mb-3">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="comentario_id" value="<?php echo $comentario['id']; ?>">
                                                        <button type="submit" name="aprobar_comentario" class="btn btn-success btn-sm w-100">
                                                            <i class="bi bi-check-circle me-1"></i>Aprobar
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="comentario_id" value="<?php echo $comentario['id']; ?>">
                                                        <button type="submit" name="rechazar_comentario" class="btn btn-warning btn-sm w-100">
                                                            <i class="bi bi-x-circle me-1"></i>Rechazar
                                                        </button>
                                                    </form>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este comentario?')">
                                                    <input type="hidden" name="comentario_id" value="<?php echo $comentario['id']; ?>">
                                                    <button type="submit" name="eliminar_comentario" class="btn btn-danger btn-sm w-100">
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
                                <i class="bi bi-chat-dots"></i>
                                <h3>No hay comentarios</h3>
                                <p>No se encontraron comentarios con los filtros seleccionados.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Estadísticas -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo $estadisticas['total']; ?></h5>
                                <p class="card-text">Total Comentarios</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-warning"><?php echo $estadisticas['pendientes']; ?></h5>
                                <p class="card-text">Pendientes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-success"><?php echo $estadisticas['aprobados']; ?></h5>
                                <p class="card-text">Aprobados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-danger"><?php echo $estadisticas['rechazados']; ?></h5>
                                <p class="card-text">Rechazados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
