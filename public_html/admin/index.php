<?php
/**
 * ========================================
 * ADMIN - DASHBOARD PRINCIPAL
 * ========================================
 * 
 * Dashboard principal del panel de administración
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

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Obtener estadísticas del blog
$stats = [];

// Total de artículos
$sql = "SELECT COUNT(*) as total FROM blog_articulos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['total_articulos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Artículos publicados
$sql = "SELECT COUNT(*) as total FROM blog_articulos WHERE estado = 'publicado'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['articulos_publicados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Artículos borradores
$sql = "SELECT COUNT(*) as total FROM blog_articulos WHERE estado = 'borrador'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['articulos_borradores'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de categorías
$sql = "SELECT COUNT(*) as total FROM blog_categorias WHERE estado = 'activo'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['total_categorias'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de comentarios
$sql = "SELECT COUNT(*) as total FROM blog_comentarios";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['total_comentarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Comentarios pendientes
$sql = "SELECT COUNT(*) as total FROM blog_comentarios WHERE estado = 'pendiente'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['comentarios_pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de vistas
$sql = "SELECT SUM(vistas) as total FROM blog_articulos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['total_vistas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;

// Obtener artículos recientes
$sql = "
    SELECT a.*, c.nombre as categoria_nombre, c.color as categoria_color
    FROM blog_articulos a
    LEFT JOIN blog_categorias c ON a.categoria_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 5
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$articulos_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener comentarios recientes
$sql = "
    SELECT c.*, a.titulo as articulo_titulo, a.slug as articulo_slug
    FROM blog_comentarios c
    LEFT JOIN blog_articulos a ON c.articulo_id = a.id
    ORDER BY c.created_at DESC
    LIMIT 5
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$comentarios_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener estadísticas de newsletter
$sql_newsletter = "SELECT COUNT(*) as total FROM newsletter_subscriptions WHERE status = 'activo'";
$stmt_newsletter = $pdo->prepare($sql_newsletter);
$stmt_newsletter->execute();
$stats['newsletter_activos'] = $stmt_newsletter->fetch(PDO::FETCH_ASSOC)['total'];

$sql_newsletter_simple = "SELECT COUNT(*) as total FROM newsletter_simple WHERE status = 'activo'";
$stmt_newsletter_simple = $pdo->prepare($sql_newsletter_simple);
$stmt_newsletter_simple->execute();
$stats['newsletter_simple_activos'] = $stmt_newsletter_simple->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin <?php echo SITE_NAME; ?></title>
    
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

        .dashboard-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .dashboard-body {
            padding: 1.5rem;
        }

        .recent-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
            transition: var(--transition);
        }

        .recent-item:hover {
            transform: translateX(5px);
            box-shadow: var(--shadow);
        }

        .recent-item.success {
            border-left-color: var(--success-color);
        }

        .recent-item.warning {
            border-left-color: var(--warning-color);
        }

        .recent-item.info {
            border-left-color: var(--info-color);
        }

        .user-info {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            text-decoration: none;
            color: inherit;
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            box-shadow: var(--shadow);
        }

        .action-card.success .action-icon {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
        }

        .action-card.warning .action-icon {
            background: linear-gradient(135deg, var(--warning-color) 0%, #ffc107 100%);
        }

        .action-card.info .action-icon {
            background: linear-gradient(135deg, var(--info-color) 0%, #20c997 100%);
        }

        .action-card.danger .action-icon {
            background: linear-gradient(135deg, var(--danger-color) 0%, #e74c3c 100%);
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

        @media (max-width: 768px) {
            .admin-content {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
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
                        <i class="bi bi-shield-lock me-2"></i>Admin Panel
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="blog/index.php">
                            <i class="bi bi-newspaper me-2"></i>Blog
                        </a>
                        <a class="nav-link" href="blog/categorias.php">
                            <i class="bi bi-folder me-2"></i>Categorías
                        </a>
                        <a class="nav-link" href="blog/comentarios.php">
                            <i class="bi bi-chat-dots me-2"></i>Comentarios
                        </a>
                        <a class="nav-link" href="newsletter-subscriptions.php">
                            <i class="bi bi-envelope me-2"></i>Cotización Simple
                        </a>
                        <a class="nav-link" href="newsletter-simple.php">
                            <i class="bi bi-envelope-open me-2"></i>Newsletter Simple
                        </a>
                        <hr>
                        <a class="nav-link" href="../blog.php" target="_blank">
                            <i class="bi bi-eye me-2"></i>Ver Blog
                        </a>
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="bi bi-house me-2"></i>Ver Sitio
                        </a>
                        <hr>
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
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
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </h2>
                            <p class="mb-0 opacity-75">Bienvenido, <?php echo esc($current_user['nombre']); ?></p>
                        </div>
                        <div class="text-end">
                            <small class="opacity-75">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo esc($current_user['username']); ?> 
                                <span class="badge bg-<?php echo $current_user['rol'] === 'admin' ? 'danger' : 'primary'; ?> ms-1">
                                    <?php echo ucfirst($current_user['rol']); ?>
                                </span>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas principales -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['total_articulos']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-newspaper me-1"></i>Total Artículos
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($stats['articulos_publicados']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-check-circle me-1"></i>Publicados
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo number_format($stats['articulos_borradores']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-pencil me-1"></i>Borradores
                        </div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($stats['total_comentarios']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-chat-dots me-1"></i>Comentarios
                        </div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-number"><?php echo number_format($stats['comentarios_pendientes']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-clock me-1"></i>Pendientes
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['total_vistas']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-eye me-1"></i>Total Vistas
                        </div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($stats['newsletter_activos']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-envelope me-1"></i>Cotizaciones
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($stats['newsletter_simple_activos']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-newspaper me-1"></i>Newsletter
                        </div>
                    </div>
                </div>


                <!-- Acciones rápidas -->
                <div class="quick-actions">
                    <a href="blog/create.php" class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <h6 class="mb-2">Nuevo Artículo</h6>
                        <p class="text-muted mb-0">Crear un nuevo artículo para el blog</p>
                    </a>
                    <a href="blog/categorias.php" class="action-card success">
                        <div class="action-icon">
                            <i class="bi bi-folder-plus"></i>
                        </div>
                        <h6 class="mb-2">Nueva Categoría</h6>
                        <p class="text-muted mb-0">Organizar artículos por categorías</p>
                    </a>
                    <a href="blog/comentarios.php" class="action-card warning">
                        <div class="action-icon">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h6 class="mb-2">Gestionar Comentarios</h6>
                        <p class="text-muted mb-0">Moderar comentarios del blog</p>
                    </a>
                    <a href="newsletter-subscriptions.php" class="action-card info">
                        <div class="action-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h6 class="mb-2">Gestionar Cotizaciones</h6>
                        <p class="text-muted mb-0">Ver solicitudes de cotización</p>
                    </a>
                    <a href="newsletter-simple.php" class="action-card success">
                        <div class="action-icon">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <h6 class="mb-2">Gestionar Newsletter</h6>
                        <p class="text-muted mb-0">Ver suscripciones del newsletter</p>
                    </a>
                    <a href="../blog.php" target="_blank" class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h6 class="mb-2">Ver Blog</h6>
                        <p class="text-muted mb-0">Ver el blog público</p>
                    </a>
                </div>

                <div class="row">
                    <!-- Artículos recientes -->
                    <div class="col-md-6">
                        <div class="dashboard-section">
                            <div class="dashboard-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-newspaper me-2"></i>Artículos Recientes
                                </h5>
                            </div>
                            <div class="dashboard-body">
                                <?php if (!empty($articulos_recientes)): ?>
                                    <?php foreach ($articulos_recientes as $articulo): ?>
                                    <div class="recent-item success">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="blog/edit.php?id=<?php echo $articulo['id']; ?>" 
                                                       class="text-decoration-none">
                                                        <?php echo esc(truncateText($articulo['titulo'], 50)); ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    <?php echo date('d M Y H:i', strtotime($articulo['created_at'])); ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-<?php echo $articulo['estado'] === 'publicado' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($articulo['estado']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="bi bi-newspaper"></i>
                                        <h3>No hay artículos</h3>
                                        <p>No se han encontrado artículos recientes.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios recientes -->
                    <div class="col-md-6">
                        <div class="dashboard-section">
                            <div class="dashboard-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-chat-dots me-2"></i>Comentarios Recientes
                                </h5>
                            </div>
                            <div class="dashboard-body">
                                <?php if (!empty($comentarios_recientes)): ?>
                                    <?php foreach ($comentarios_recientes as $comentario): ?>
                                    <div class="recent-item info">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo esc($comentario['nombre']); ?></h6>
                                                <p class="mb-1 small"><?php echo esc(truncateText($comentario['comentario'], 60)); ?></p>
                                                <small class="text-muted">
                                                    En: <?php echo esc(truncateText($comentario['articulo_titulo'], 30)); ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-<?php echo $comentario['estado'] === 'aprobado' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($comentario['estado']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="bi bi-chat-dots"></i>
                                        <h3>No hay comentarios</h3>
                                        <p>No se han encontrado comentarios recientes.</p>
                                    </div>
                                <?php endif; ?>
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
