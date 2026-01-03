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

// Iniciar logging
error_log("=== ADMIN INDEX.PHP INICIADO ===");

// Cargar configuración y verificar autenticación
try {
    error_log("Cargando config.php...");
    require_once __DIR__ . '/../includes/config.php';
    error_log("config.php cargado");
} catch (Exception $e) {
    error_log("ERROR cargando config.php: " . $e->getMessage());
    die("Error de configuración");
}

try {
    error_log("Cargando functions.php...");
    require_once __DIR__ . '/../includes/functions.php';
    error_log("functions.php cargado");
} catch (Exception $e) {
    error_log("ERROR cargando functions.php: " . $e->getMessage());
    die("Error cargando funciones");
}

try {
    error_log("Cargando connection.php...");
    require_once __DIR__ . '/../includes/connection.php';
    error_log("connection.php cargado");
} catch (Exception $e) {
    error_log("ERROR cargando connection.php: " . $e->getMessage());
    die("Error cargando conexión");
}

try {
    error_log("Cargando auth_check.php...");
    require_once __DIR__ . '/auth_check.php';
    error_log("auth_check.php cargado");
} catch (Exception $e) {
    error_log("ERROR cargando auth_check.php: " . $e->getMessage());
    die("Error de autenticación");
}

// Obtener conexión PDO
error_log("Obteniendo conexión PDO...");
$pdo = getDB();
if (!$pdo) {
    error_log("ERROR: No se pudo obtener conexión PDO");
    die('Error de conexión a la base de datos');
}
error_log("Conexión PDO obtenida exitosamente");

// Obtener información del usuario actual
$current_user = null;
if (function_exists('getCurrentUser')) {
    $current_user = getCurrentUser();
} else {
    // Fallback: obtener usuario desde sesión
    if (isset($_SESSION['admin_user_id'])) {
        try {
            $sql_user = "SELECT * FROM admin_usuarios WHERE id = ?";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([$_SESSION['admin_user_id']]);
            $current_user = $stmt_user->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error obteniendo usuario: " . $e->getMessage());
            $current_user = [
                'id' => $_SESSION['admin_user_id'] ?? 0,
                'username' => $_SESSION['admin_username'] ?? 'Usuario',
                'nombre' => $_SESSION['admin_nombre'] ?? 'Usuario',
                'rol' => $_SESSION['admin_rol'] ?? 'editor'
            ];
        }
    }
}

// Publicar artículos programados automáticamente
if (function_exists('publicarArticulosProgramados')) {
    publicarArticulosProgramados();
}

// Obtener estadísticas del blog
$stats = [];

try {
    // Total de artículos
    $sql = "SELECT COUNT(*) as total FROM blog_articulos";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['total_articulos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo total artículos: " . $e->getMessage());
    $stats['total_articulos'] = 0;
}

try {
    // Artículos publicados
    $sql = "SELECT COUNT(*) as total FROM blog_articulos WHERE estado = 'publicado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['articulos_publicados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo artículos publicados: " . $e->getMessage());
    $stats['articulos_publicados'] = 0;
}

try {
    // Artículos borradores
    $sql = "SELECT COUNT(*) as total FROM blog_articulos WHERE estado = 'borrador'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['articulos_borradores'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo artículos borradores: " . $e->getMessage());
    $stats['articulos_borradores'] = 0;
}

try {
    // Total de categorías
    $sql = "SELECT COUNT(*) as total FROM blog_categorias WHERE estado = 'activo'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['total_categorias'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo categorías: " . $e->getMessage());
    $stats['total_categorias'] = 0;
}

try {
    // Total de comentarios
    $sql = "SELECT COUNT(*) as total FROM blog_comentarios";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['total_comentarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo comentarios: " . $e->getMessage());
    $stats['total_comentarios'] = 0;
}

try {
    // Comentarios pendientes
    $sql = "SELECT COUNT(*) as total FROM blog_comentarios WHERE estado = 'pendiente'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['comentarios_pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo comentarios pendientes: " . $e->getMessage());
    $stats['comentarios_pendientes'] = 0;
}

try {
    // Total de vistas
    $sql = "SELECT SUM(vistas) as total FROM blog_articulos";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['total_vistas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
} catch (Exception $e) {
    error_log("Error obteniendo vistas: " . $e->getMessage());
    $stats['total_vistas'] = 0;
}

// Obtener artículos recientes
try {
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
} catch (Exception $e) {
    error_log("Error obteniendo artículos recientes: " . $e->getMessage());
    $articulos_recientes = [];
}

// Obtener comentarios recientes
try {
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
} catch (Exception $e) {
    error_log("Error obteniendo comentarios recientes: " . $e->getMessage());
    $comentarios_recientes = [];
}

// Obtener estadísticas de newsletter
try {
    $sql_newsletter = "SELECT COUNT(*) as total FROM newsletter_subscriptions WHERE status = 'activo'";
    $stmt_newsletter = $pdo->prepare($sql_newsletter);
    $stmt_newsletter->execute();
    $stats['newsletter_activos'] = $stmt_newsletter->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo newsletter: " . $e->getMessage());
    $stats['newsletter_activos'] = 0;
}

try {
    $sql_newsletter_simple = "SELECT COUNT(*) as total FROM newsletter_simple WHERE status = 'activo'";
    $stmt_newsletter_simple = $pdo->prepare($sql_newsletter_simple);
    $stmt_newsletter_simple->execute();
    $stats['newsletter_simple_activos'] = $stmt_newsletter_simple->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo newsletter_simple: " . $e->getMessage());
    $stats['newsletter_simple_activos'] = 0;
}

// Obtener estadísticas de usuarios
try {
    $sql_usuarios = "SELECT COUNT(*) as total FROM admin_usuarios";
    $stmt_usuarios = $pdo->prepare($sql_usuarios);
    $stmt_usuarios->execute();
    $stats['usuarios'] = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo usuarios: " . $e->getMessage());
    $stats['usuarios'] = 0;
}

// Obtener estadísticas de topbar
try {
    $sql_topbar = "SELECT COUNT(*) as total FROM topbar_messages WHERE status = 'active'";
    $stmt_topbar = $pdo->prepare($sql_topbar);
    $stmt_topbar->execute();
    $stats['topbar_messages'] = $stmt_topbar->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error obteniendo topbar_messages: " . $e->getMessage());
    $stats['topbar_messages'] = 0;
}

// Obtener KPIs adicionales
try {
    // Productos publicados
    $sql = "SELECT COUNT(*) as total FROM catalogo_productos WHERE estado = 'activo'";
    $stmt = $pdo->query($sql);
    $stats['productos_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    $stats['productos_activos'] = 0;
}

try {
    // Mensajes de contacto por estado
    $sql = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'nuevo' THEN 1 ELSE 0 END) as nuevos,
        SUM(CASE WHEN status = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso
    FROM contact_messages";
    $stmt = $pdo->query($sql);
    $contact_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['contactos_total'] = $contact_stats['total'] ?? 0;
    $stats['contactos_nuevos'] = $contact_stats['nuevos'] ?? 0;
} catch (Exception $e) {
    $stats['contactos_total'] = 0;
    $stats['contactos_nuevos'] = 0;
}

// Cotizaciones: hoy/semana/mes/acumulado
try {
    $sql = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as hoy,
        SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as semana,
        SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as mes
    FROM newsletter_subscriptions 
    WHERE status = 'active'";
    $stmt = $pdo->query($sql);
    $cotiz_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['cotizaciones_hoy'] = $cotiz_stats['hoy'] ?? 0;
    $stats['cotizaciones_semana'] = $cotiz_stats['semana'] ?? 0;
    $stats['cotizaciones_mes'] = $cotiz_stats['mes'] ?? 0;
    $stats['cotizaciones_total'] = $cotiz_stats['total'] ?? 0;
} catch (Exception $e) {
    $stats['cotizaciones_hoy'] = 0;
    $stats['cotizaciones_semana'] = 0;
    $stats['cotizaciones_mes'] = 0;
    $stats['cotizaciones_total'] = 0;
}

// Cargar helper de alertas
$alerts = [];
try {
    if (file_exists(__DIR__ . '/includes/dashboard_alerts.php')) {
        require_once __DIR__ . '/includes/dashboard_alerts.php';
        if (function_exists('getDashboardAlerts')) {
            $alerts = getDashboardAlerts($pdo);
        }
    }
} catch (Exception $e) {
    error_log("Error cargando alertas: " . $e->getMessage());
    $alerts = [];
}

// Obtener últimas cotizaciones
try {
    $sql = "SELECT * FROM newsletter_subscriptions 
            WHERE status = 'active' 
            ORDER BY created_at DESC 
            LIMIT 5";
    $stmt = $pdo->query($sql);
    $ultimas_cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $ultimas_cotizaciones = [];
}

// Obtener últimos contactos
try {
    $sql = "SELECT * FROM contact_messages 
            ORDER BY created_at DESC 
            LIMIT 5";
    $stmt = $pdo->query($sql);
    $ultimos_contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $ultimos_contactos = [];
}
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
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
            <?php include __DIR__ . '/includes/admin_menu.php'; ?>

            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-9 admin-content p-4">
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
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($stats['usuarios']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-people me-1"></i>Usuarios Admin
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo number_format($stats['topbar_messages']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-megaphone me-1"></i>Mensajes Topbar
                        </div>
                    </div>
                    <?php if ($stats['productos_activos'] > 0): ?>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($stats['productos_activos']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-box-seam me-1"></i>Productos Activos
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($stats['contactos_total'] > 0): ?>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($stats['contactos_total']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-chat-dots me-1"></i>Mensajes Contacto
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['cotizaciones_hoy']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-calendar-day me-1"></i>Cotizaciones Hoy
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo number_format($stats['cotizaciones_semana']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-calendar-week me-1"></i>Esta Semana
                        </div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo number_format($stats['cotizaciones_mes']); ?></div>
                        <div class="stat-label">
                            <i class="bi bi-calendar-month me-1"></i>Este Mes
                        </div>
                    </div>
                </div>
                
                <!-- Alertas Automáticas -->
                <?php if (!empty($alerts)): ?>
                <div class="dashboard-section mb-4">
                    <div class="dashboard-header">
                        <h5 class="mb-0">
                            <i class="bi bi-bell me-2"></i>Alertas y Notificaciones
                        </h5>
                    </div>
                    <div class="dashboard-body">
                        <?php foreach ($alerts as $alert): ?>
                        <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
                            <i class="bi <?php echo $alert['icon']; ?> me-2"></i>
                            <strong><?php echo esc($alert['title']); ?>:</strong>
                            <?php echo esc($alert['message']); ?>
                            <?php if (isset($alert['link'])): ?>
                            <a href="<?php echo esc($alert['link']); ?>" class="alert-link ms-2">
                                <?php echo esc($alert['link_text']); ?>
                            </a>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Gráficas de Tendencias -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="dashboard-section">
                            <div class="dashboard-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-graph-up me-2"></i>Cotizaciones por Mes
                                </h5>
                            </div>
                            <div class="dashboard-body">
                                <canvas id="chartCotizaciones" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dashboard-section">
                            <div class="dashboard-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-graph-up me-2"></i>Contactos por Mes
                                </h5>
                            </div>
                            <div class="dashboard-body">
                                <canvas id="chartContactos" height="200"></canvas>
                            </div>
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
                    <a href="usuarios.php" class="action-card info">
                        <div class="action-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h6 class="mb-2">Gestionar Usuarios</h6>
                        <p class="text-muted mb-0">Administrar usuarios del sistema</p>
                    </a>
                    <a href="perfil.php" class="action-card success">
                        <div class="action-icon">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <h6 class="mb-2">Mi Perfil</h6>
                        <p class="text-muted mb-0">Editar información personal</p>
                    </a>
                    <a href="topbar-messages.php" class="action-card warning">
                        <div class="action-icon">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <h6 class="mb-2">Mensajes Topbar</h6>
                        <p class="text-muted mb-0">Gestionar mensajes del topbar</p>
                    </a>
                </div>

                <div class="row">
                    <!-- Últimas Cotizaciones -->
                    <?php if (!empty($ultimas_cotizaciones)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="dashboard-section">
                            <div class="dashboard-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-envelope me-2"></i>Últimas Cotizaciones
                                </h5>
                            </div>
                            <div class="dashboard-body">
                                <?php foreach ($ultimas_cotizaciones as $cot): ?>
                                <div class="recent-item info">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo esc($cot['institucion']); ?></h6>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i><?php echo esc($cot['nombre']); ?>
                                                | <i class="bi bi-envelope me-1"></i><?php echo esc($cot['email_oficial']); ?>
                                            </small>
                                            <?php if ($cot['producto_interes']): ?>
                                            <div class="mt-1">
                                                <small><strong>Producto:</strong> <?php echo esc($cot['producto_interes']); ?></small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($cot['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="text-center mt-3">
                                    <a href="cotizaciones/index.php" class="btn btn-sm btn-outline-primary">
                                        Ver todas las cotizaciones
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Últimos Contactos -->
                    <?php if (!empty($ultimos_contactos)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="dashboard-section">
                            <div class="dashboard-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-chat-dots me-2"></i>Últimos Mensajes de Contacto
                                </h5>
                            </div>
                            <div class="dashboard-body">
                                <?php foreach ($ultimos_contactos as $contact): ?>
                                <div class="recent-item <?php echo $contact['status'] === 'nuevo' ? 'warning' : 'success'; ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo esc($contact['nombre']); ?></h6>
                                            <p class="mb-1 small"><?php echo esc(truncateText($contact['asunto'], 40)); ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i><?php echo esc($contact['email']); ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?php echo $contact['status'] === 'nuevo' ? 'danger' : 'success'; ?>">
                                                <?php echo ucfirst($contact['status']); ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($contact['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="text-center mt-3">
                                    <a href="contacto/index.php" class="btn btn-sm btn-outline-primary">
                                        Ver todos los mensajes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
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
    <script>
        // Cargar datos para gráficas
        async function loadChartData(chartId, tipo, label, color) {
            try {
                const response = await fetch(`includes/dashboard_data.php?tipo=${tipo}`);
                const data = await response.json();
                
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                
                const ctx = document.getElementById(chartId);
                if (!ctx) return;
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: label,
                            data: data.data,
                            borderColor: color,
                            backgroundColor: color + '20',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error cargando gráfica:', error);
            }
        }
        
        // Cargar gráficas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadChartData('chartCotizaciones', 'cotizaciones_mes', 'Cotizaciones', 'rgb(102, 126, 234)');
            loadChartData('chartContactos', 'contactos_mes', 'Contactos', 'rgb(23, 162, 184)');
        });
    </script>
</body>
</html>
