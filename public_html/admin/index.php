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
        .admin-sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        .admin-content {
            background-color: #ffffff;
            min-height: 100vh;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .recent-item {
            border-left: 4px solid #0066cc;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        .user-info {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </h2>
                        <p class="text-muted mb-0">Bienvenido, <?php echo esc($current_user['nombre']); ?></p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo esc($current_user['username']); ?> 
                            <span class="badge bg-<?php echo $current_user['rol'] === 'admin' ? 'danger' : 'primary'; ?> ms-1">
                                <?php echo ucfirst($current_user['rol']); ?>
                            </span>
                        </small>
                    </div>
                </div>

                <!-- Estadísticas principales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <div class="stat-number"><?php echo number_format($stats['articulos_publicados']); ?></div>
                            <div class="stat-label">
                                <i class="bi bi-check-circle me-1"></i>Artículos Publicados
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <div class="stat-number"><?php echo number_format($stats['articulos_borradores']); ?></div>
                            <div class="stat-label">
                                <i class="bi bi-file-earmark-text me-1"></i>Borradores
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card info">
                            <div class="stat-number"><?php echo number_format($stats['total_vistas']); ?></div>
                            <div class="stat-label">
                                <i class="bi bi-eye me-1"></i>Total Vistas
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card danger">
                            <div class="stat-number"><?php echo number_format($stats['comentarios_pendientes']); ?></div>
                            <div class="stat-label">
                                <i class="bi bi-chat-dots me-1"></i>Comentarios Pendientes
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Newsletter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stat-card success">
                            <div class="stat-number"><?php echo number_format($stats['newsletter_activos']); ?></div>
                            <div class="stat-label">
                                <i class="bi bi-envelope me-1"></i>Cotizaciones Activas
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card info">
                            <div class="stat-number"><?php echo number_format($stats['newsletter_simple_activos']); ?></div>
                            <div class="stat-label">
                                <i class="bi bi-envelope-open me-1"></i>Newsletter Simple Activos
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-lightning me-2"></i>Acciones Rápidas
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <a href="blog/create.php" class="btn btn-primary w-100">
                                            <i class="bi bi-plus-circle me-2"></i>Nuevo Artículo
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="blog/categorias.php" class="btn btn-success w-100">
                                            <i class="bi bi-folder-plus me-2"></i>Nueva Categoría
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="blog/comentarios.php" class="btn btn-warning w-100">
                                            <i class="bi bi-chat-dots me-2"></i>Moderar Comentarios
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="../blog.php" target="_blank" class="btn btn-info w-100">
                                            <i class="bi bi-eye me-2"></i>Ver Blog
                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-2">
                                        <a href="newsletter-subscriptions.php" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-envelope me-2"></i>Gestionar Cotizaciones
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <a href="newsletter-simple.php" class="btn btn-outline-info w-100">
                                            <i class="bi bi-envelope-open me-2"></i>Gestionar Newsletter Simple
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Artículos recientes -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-newspaper me-2"></i>Artículos Recientes
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($articulos_recientes)): ?>
                                    <?php foreach ($articulos_recientes as $articulo): ?>
                                    <div class="recent-item">
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
                                    <p class="text-muted text-center py-3">No hay artículos recientes</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios recientes -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-chat-dots me-2"></i>Comentarios Recientes
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($comentarios_recientes)): ?>
                                    <?php foreach ($comentarios_recientes as $comentario): ?>
                                    <div class="recent-item">
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
                                    <p class="text-muted text-center py-3">No hay comentarios recientes</p>
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
