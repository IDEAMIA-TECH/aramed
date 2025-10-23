<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE CATEGORÍAS DEL BLOG
 * ========================================
 * 
 * Panel de administración para gestionar categorías del blog
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
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_categoria'])) {
        $nombre = sanitizeInput($_POST['nombre']);
        $slug = generateSlug($nombre);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $icono = sanitizeInput($_POST['icono']);
        $color = sanitizeInput($_POST['color']);
        $estado = sanitizeInput($_POST['estado']);
        
        if (!empty($nombre)) {
            $sql = "INSERT INTO blog_categorias (nombre, slug, descripcion, icono, color, estado) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$nombre, $slug, $descripcion, $icono, $color, $estado]);
            
            if ($resultado) {
                $mensaje = 'Categoría creada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al crear la categoría';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = 'El nombre es requerido';
            $tipo_mensaje = 'warning';
        }
    } elseif (isset($_POST['editar_categoria'])) {
        $categoria_id = (int)$_POST['categoria_id'];
        $nombre = sanitizeInput($_POST['nombre']);
        $slug = generateSlug($nombre);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $icono = sanitizeInput($_POST['icono']);
        $color = sanitizeInput($_POST['color']);
        $estado = sanitizeInput($_POST['estado']);
        
        if (!empty($nombre)) {
            $sql = "UPDATE blog_categorias SET nombre = ?, slug = ?, descripcion = ?, icono = ?, color = ?, estado = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$nombre, $slug, $descripcion, $icono, $color, $estado, $categoria_id]);
            
            if ($resultado) {
                $mensaje = 'Categoría actualizada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar la categoría';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = 'El nombre es requerido';
            $tipo_mensaje = 'warning';
        }
    } elseif (isset($_POST['eliminar_categoria'])) {
        $categoria_id = (int)$_POST['categoria_id'];
        
        // Verificar si hay artículos asociados
        $sql_check = "SELECT COUNT(*) as count FROM blog_articulos WHERE categoria_id = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$categoria_id]);
        $count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            $mensaje = 'No se puede eliminar la categoría porque tiene artículos asociados';
            $tipo_mensaje = 'warning';
        } else {
            $sql = "DELETE FROM blog_categorias WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$categoria_id]);
            
            if ($resultado) {
                $mensaje = 'Categoría eliminada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar la categoría';
                $tipo_mensaje = 'danger';
            }
        }
    }
}

// Obtener categorías
$sql = "
    SELECT c.*, COUNT(a.id) as articulos_count
    FROM blog_categorias c
    LEFT JOIN blog_articulos a ON c.id = a.categoria_id
    GROUP BY c.id
    ORDER BY c.nombre ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categoría para editar
$categoria_editar = null;
if ($action === 'edit' && $id > 0) {
    $sql = "SELECT * FROM blog_categorias WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $categoria_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Estadísticas
$sql_stats = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activas,
        SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivas,
        SUM(articulos_count) as total_articulos
    FROM (
        SELECT c.*, COUNT(a.id) as articulos_count
        FROM blog_categorias c
        LEFT JOIN blog_articulos a ON c.id = a.categoria_id
        GROUP BY c.id
    ) as stats
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
    <title>Gestión de Categorías - Admin Blog</title>
    
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

        .category-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .category-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .category-body {
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

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        .category-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            box-shadow: var(--shadow);
        }

        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-hover);
        }

        .modal-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid var(--border-color);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 1px solid var(--border-color);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
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
            
            .category-card {
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
                        <a class="nav-link active" href="categorias.php">
                            <i class="bi bi-folder me-2"></i>Categorías
                        </a>
                        <a class="nav-link" href="comentarios.php">
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
                                <i class="bi bi-folder me-2"></i>Gestión de Categorías
                            </h2>
                            <p class="mb-0 opacity-75">Administra las categorías del blog</p>
                        </div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoriaModal">
                            <i class="bi bi-plus-circle me-1"></i>Nueva Categoría
                        </button>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($estadisticas['total']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-folder me-1"></i>Total Categorías
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($estadisticas['activas']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-check-circle me-1"></i>Activas
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo number_format($estadisticas['inactivas']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-pause-circle me-1"></i>Inactivas
                        </div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($estadisticas['total_articulos']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-newspaper me-1"></i>Total Artículos
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

                <!-- Lista de categorías -->
                <div class="row">
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                        <div class="col-12 mb-3">
                            <div class="category-card">
                                <div class="category-header">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <div class="d-flex align-items-center">
                                            <div class="category-icon" style="background-color: <?php echo $categoria['color'] ?: '#6c757d'; ?>">
                                                <i class="bi bi-<?php echo $categoria['icono'] ?: 'folder'; ?>"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold"><?php echo esc($categoria['nombre']); ?></h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-newspaper me-1"></i>
                                                    <?php echo $categoria['articulos_count']; ?> artículos
                                                </small>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?php echo $categoria['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                                <?php echo $categoria['estado'] === 'activo' ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="category-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Información Básica -->
                                            <div class="info-section">
                                                <h6>
                                                    <i class="bi bi-info-circle me-1"></i>Información Básica
                                                </h6>
                                                <div class="info-row">
                                                    <div class="info-item">
                                                        <strong>Nombre:</strong>
                                                        <p><?php echo esc($categoria['nombre']); ?></p>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>Slug:</strong>
                                                        <p><code><?php echo esc($categoria['slug']); ?></code></p>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>Artículos:</strong>
                                                        <p><span class="badge bg-info"><?php echo $categoria['articulos_count']; ?> artículos</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!empty($categoria['descripcion'])): ?>
                                            <!-- Descripción -->
                                            <div class="info-section success">
                                                <h6>
                                                    <i class="bi bi-file-text me-1"></i>Descripción
                                                </h6>
                                                <div class="info-item">
                                                    <p><?php echo esc($categoria['descripcion']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Configuración Visual -->
                                            <div class="info-section secondary">
                                                <h6>
                                                    <i class="bi bi-palette me-1"></i>Configuración Visual
                                                </h6>
                                                <div class="info-row">
                                                    <div class="info-item">
                                                        <strong>Icono:</strong>
                                                        <p><i class="bi bi-<?php echo $categoria['icono'] ?: 'folder'; ?>"></i> <?php echo esc($categoria['icono'] ?: 'folder'); ?></p>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>Color:</strong>
                                                        <p>
                                                            <span class="badge" style="background-color: <?php echo $categoria['color'] ?: '#6c757d'; ?>; color: white;">
                                                                <?php echo esc($categoria['color'] ?: '#6c757d'); ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="actions-panel">
                                                <h6 class="mb-3 fw-bold">
                                                    <i class="bi bi-gear me-1"></i>Acciones
                                                </h6>
                                                
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-warning btn-sm" 
                                                            onclick="editarCategoria(<?php echo htmlspecialchars(json_encode($categoria)); ?>)">
                                                        <i class="bi bi-pencil me-1"></i>Editar
                                                    </button>
                                                    <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                                        <input type="hidden" name="categoria_id" value="<?php echo $categoria['id']; ?>">
                                                        <button type="submit" name="eliminar_categoria" class="btn btn-danger btn-sm w-100">
                                                            <i class="bi bi-trash me-1"></i>Eliminar
                                                        </button>
                                                    </form>
                                                </div>
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
                                <i class="bi bi-folder"></i>
                                <h3>No hay categorías</h3>
                                <p>Crea tu primera categoría para organizar los artículos del blog.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva/Editar Categoría -->
    <div class="modal fade" id="categoriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoriaModalTitle">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="categoriaForm">
                    <div class="modal-body">
                        <input type="hidden" name="categoria_id" id="categoria_id">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="icono" class="form-label">Icono</label>
                                <select class="form-select" id="icono" name="icono">
                                    <option value="folder">Carpeta</option>
                                    <option value="heart-pulse">Salud</option>
                                    <option value="book">Educación</option>
                                    <option value="lightbulb">Tecnología</option>
                                    <option value="award">Éxito</option>
                                    <option value="calendar-event">Eventos</option>
                                    <option value="people">Personas</option>
                                    <option value="gear">Configuración</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear_categoria" id="submitBtn" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Crear Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarCategoria(categoria) {
            document.getElementById('categoriaModalTitle').textContent = 'Editar Categoría';
            document.getElementById('categoria_id').value = categoria.id;
            document.getElementById('nombre').value = categoria.nombre;
            document.getElementById('descripcion').value = categoria.descripcion || '';
            document.getElementById('icono').value = categoria.icono || 'folder';
            document.getElementById('color').value = categoria.color || '#007bff';
            document.getElementById('estado').value = categoria.estado || 'activo';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-save me-1"></i>Guardar Cambios';
            document.getElementById('submitBtn').name = 'editar_categoria';
            
            const modal = new bootstrap.Modal(document.getElementById('categoriaModal'));
            modal.show();
        }
        
        // Reset modal when closed
        document.getElementById('categoriaModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('categoriaModalTitle').textContent = 'Nueva Categoría';
            document.getElementById('categoriaForm').reset();
            document.getElementById('categoria_id').value = '';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Crear Categoría';
            document.getElementById('submitBtn').name = 'crear_categoria';
        });
    </script>
</body>
</html>
