<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DEL BLOG
 * ========================================
 * 
 * Panel de administración para gestionar artículos del blog
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
    checkPermission('blog', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener parámetros
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener artículos
$sql = "
    SELECT a.*, c.nombre as categoria_nombre, c.color as categoria_color
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    ORDER BY a.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías para filtros
$sql_categorias = "SELECT * FROM blog_categorias WHERE estado = 'activo' ORDER BY nombre";
$stmt_categorias = $pdo->prepare($sql_categorias);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// Construir consulta con filtros
$where_conditions = [];
$params = [];

if ($filtro_estado) {
    $where_conditions[] = 'a.estado = ?';
    $params[] = $filtro_estado;
}

if ($filtro_categoria > 0) {
    $where_conditions[] = 'a.categoria_id = ?';
    $params[] = $filtro_categoria;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener artículos con filtros
$sql = "
    SELECT a.*, c.nombre as categoria_nombre, c.color as categoria_color
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    $where_clause
    ORDER BY a.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$sql_stats = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN estado = 'publicado' THEN 1 ELSE 0 END) as publicados,
        SUM(CASE WHEN estado = 'borrador' THEN 1 ELSE 0 END) as borradores,
        SUM(CASE WHEN estado = 'programado' THEN 1 ELSE 0 END) as programados,
        SUM(CASE WHEN estado = 'archivado' THEN 1 ELSE 0 END) as archivados,
        SUM(CASE WHEN destacado = 1 THEN 1 ELSE 0 END) as destacados,
        SUM(vistas) as total_vistas
    FROM blog_articulos
";

$stmt_stats = $pdo->prepare($sql_stats);
$stmt_stats->execute();
$estadisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);

// La función truncateText está definida en includes/functions.php
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión del Blog - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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

        .stat-card.info::before {
            background: var(--info-color);
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

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #ffc107 100%);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #e74c3c 100%);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
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

        .article-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .article-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .article-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .article-body {
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

        .info-section.info {
            border-left-color: var(--info-color);
        }

        .info-section.info h6 {
            color: var(--info-color);
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

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        .status-badge {
            font-size: 0.75rem;
        }

        .article-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .table-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: var(--dark-bg);
        }

        .table td {
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 102, 204, 0.05);
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
            
            .article-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>

            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-9 admin-content p-4">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2>
                                <i class="bi bi-newspaper me-2"></i>Gestión de Artículos
                            </h2>
                            <p class="mb-0 opacity-75">Administra los artículos del blog</p>
                        </div>
                        <a href="create.php" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Artículo
                        </a>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($estadisticas['total']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-newspaper me-1"></i>Total Artículos
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($estadisticas['publicados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-check-circle me-1"></i>Publicados
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo number_format($estadisticas['borradores']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-pencil me-1"></i>Borradores
                        </div>
                    </div>
                    <?php if (isset($estadisticas['programados']) && $estadisticas['programados'] > 0): ?>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($estadisticas['programados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-clock me-1"></i>Programados
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="stat-card danger">
                        <div class="stat-number"><?php echo number_format($estadisticas['archivados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-archive me-1"></i>Archivados
                        </div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($estadisticas['destacados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-star me-1"></i>Destacados
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($estadisticas['total_vistas']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-eye me-1"></i>Total Vistas
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado" onchange="this.form.submit()">
                                    <option value="">Todos los estados</option>
                                    <option value="publicado" <?php echo $filtro_estado === 'publicado' ? 'selected' : ''; ?>>Publicados</option>
                                    <option value="programado" <?php echo $filtro_estado === 'programado' ? 'selected' : ''; ?>>Programados</option>
                                    <option value="borrador" <?php echo $filtro_estado === 'borrador' ? 'selected' : ''; ?>>Borradores</option>
                                    <option value="archivado" <?php echo $filtro_estado === 'archivado' ? 'selected' : ''; ?>>Archivados</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" name="categoria" onchange="this.form.submit()">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $filtro_categoria == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo esc($cat['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="index.php" class="btn btn-secondary w-100">
                                        <i class="bi bi-x-circle me-2"></i>Limpiar Filtros
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de artículos -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Lista de Artículos (<?php echo count($articulos); ?>)
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Autor</th>
                                        <th>Estado</th>
                                        <th>Vistas</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($articulos)): ?>
                                        <?php foreach ($articulos as $articulo): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($articulo['imagen_principal'])): ?>
                                                    <?php 
                                                    $imagen_url = $articulo['imagen_principal'];
                                                    // Si la URL no es absoluta, agregar SITE_URL
                                                    if (strpos($imagen_url, 'http') !== 0 && strpos($imagen_url, '/') !== 0) {
                                                        $imagen_url = SITE_URL . '/' . $imagen_url;
                                                    } elseif (strpos($imagen_url, '/') === 0) {
                                                        $imagen_url = SITE_URL . $imagen_url;
                                                    }
                                                    ?>
                                                    <img src="<?php echo esc($imagen_url); ?>" 
                                                         alt="<?php echo esc($articulo['titulo']); ?>" 
                                                         class="article-image"
                                                         onerror="this.src='<?php echo SITE_URL; ?>/assets/images/blog/default-article.jpg'">
                                                <?php else: ?>
                                                    <div class="article-image bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo esc($articulo['titulo']); ?></strong>
                                                    <?php if ($articulo['destacado']): ?>
                                                    <span class="badge bg-warning text-dark ms-2 status-badge">
                                                        <i class="bi bi-star-fill me-1"></i>Destacado
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo esc(truncateText($articulo['extracto'] ?? '', 60)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($articulo['categoria_nombre']): ?>
                                                <span class="badge rounded-pill" 
                                                      style="background-color: <?php echo $articulo['categoria_color']; ?>;">
                                                    <?php echo esc($articulo['categoria_nombre']); ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="text-muted">Sin categoría</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div><?php echo esc($articulo['autor']); ?></div>
                                                <small class="text-muted"><?php echo esc($articulo['autor']); ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $estado_classes = [
                                                    'borrador' => 'bg-secondary',
                                                    'programado' => 'bg-info',
                                                    'publicado' => 'bg-success',
                                                    'archivado' => 'bg-warning'
                                                ];
                                                $estado_texts = [
                                                    'borrador' => 'Borrador',
                                                    'programado' => 'Programado',
                                                    'publicado' => 'Publicado',
                                                    'archivado' => 'Archivado'
                                                ];
                                                ?>
                                                <span class="badge <?php echo $estado_classes[$articulo['estado']]; ?> status-badge">
                                                    <?php echo $estado_texts[$articulo['estado']]; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info status-badge">
                                                    <i class="bi bi-eye me-1"></i><?php echo number_format($articulo['vistas']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div><?php echo date('d/m/Y', strtotime($articulo['created_at'])); ?></div>
                                                <small class="text-muted">
                                                    <?php echo date('H:i', strtotime($articulo['created_at'])); ?>
                                                </small>
                                                <?php if (isset($articulo['fecha_programada']) && $articulo['fecha_programada'] && $articulo['estado'] === 'programado'): ?>
                                                <br>
                                                <small class="text-info">
                                                    <i class="bi bi-clock me-1"></i>Programado: <?php echo date('d/m/Y H:i', strtotime($articulo['fecha_programada'])); ?>
                                                </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="../../blog-detalle.php?slug=<?php echo $articulo['slug']; ?>" 
                                                       class="btn btn-outline-primary" 
                                                       target="_blank" 
                                                       title="Ver artículo">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <?php if (function_exists('can') && can('blog', 'editar')): ?>
                                                    <a href="edit.php?id=<?php echo $articulo['id']; ?>" 
                                                       class="btn btn-outline-warning" 
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?action=toggle_status&id=<?php echo $articulo['id']; ?>" 
                                                       class="btn btn-outline-<?php echo $articulo['estado'] === 'publicado' ? 'secondary' : 'success'; ?>" 
                                                       title="<?php echo $articulo['estado'] === 'publicado' ? 'Despublicar' : 'Publicar'; ?>">
                                                        <i class="bi bi-<?php echo $articulo['estado'] === 'publicado' ? 'eye-slash' : 'eye'; ?>"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if (function_exists('can') && can('blog', 'eliminar')): ?>
                                                    <a href="?action=delete&id=<?php echo $articulo['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       title="Eliminar"
                                                       onclick="return confirm('¿Estás seguro de eliminar este artículo?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="bi bi-newspaper display-4 text-muted mb-3"></i>
                                                <p class="text-muted">No hay artículos disponibles</p>
                                                <a href="create.php" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle me-2"></i>Crear primer artículo
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <?php echo count(array_filter($articulos, function($a) { return $a['estado'] === 'publicado'; })); ?>
                                </h5>
                                <p class="card-text">Publicados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-warning">
                                    <?php echo count(array_filter($articulos, function($a) { return $a['estado'] === 'borrador'; })); ?>
                                </h5>
                                <p class="card-text">Borradores</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-info">
                                    <?php echo array_sum(array_column($articulos, 'vistas')); ?>
                                </h5>
                                <p class="card-text">Total Vistas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <?php echo count(array_filter($articulos, function($a) { return $a['destacado'] == 1; })); ?>
                                </h5>
                                <p class="card-text">Destacados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .article-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .article-image:hover {
            border-color: #0066cc;
            transform: scale(1.05);
            transition: all 0.3s ease;
        }
        
        .status-badge {
            font-size: 0.75rem;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .admin-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</body>
</html>