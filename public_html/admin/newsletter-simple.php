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
        .admin-sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        .admin-content {
            background-color: #ffffff;
            min-height: 100vh;
        }
        .subscription-card {
            border-left: 4px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .subscription-card.activo {
            border-left-color: #198754;
        }
        .subscription-card.inactivo {
            border-left-color: #6c757d;
        }
        .subscription-card.cancelado {
            border-left-color: #dc3545;
        }
        .subscription-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        .stat-card.danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
                        <a class="nav-link" href="index.php">
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
                            <i class="bi bi-envelope me-2"></i>Newsletter
                        </a>
                        <a class="nav-link active" href="newsletter-simple.php">
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
                    <h2>
                        <i class="bi bi-envelope-open me-2"></i>Newsletter Simple
                    </h2>
                    <div class="text-end">
                        <small class="text-muted">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo esc($current_user['nombre']); ?>
                        </small>
                    </div>
                </div>

                <!-- Mensajes -->
                <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo esc($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="h3 mb-0"><?php echo number_format($estadisticas['total']); ?></div>
                            <div class="small">Total Suscripciones</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <div class="h3 mb-0"><?php echo number_format($estadisticas['activos']); ?></div>
                            <div class="small">Activos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <div class="h3 mb-0"><?php echo number_format($estadisticas['inactivos']); ?></div>
                            <div class="small">Inactivos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card danger">
                            <div class="h3 mb-0"><?php echo number_format($estadisticas['cancelados']); ?></div>
                            <div class="small">Cancelados</div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="todos" <?php echo $filtro_estado === 'todos' ? 'selected' : ''; ?>>Todos</option>
                                    <option value="activo" <?php echo $filtro_estado === 'activo' ? 'selected' : ''; ?>>Activos</option>
                                    <option value="inactivo" <?php echo $filtro_estado === 'inactivo' ? 'selected' : ''; ?>>Inactivos</option>
                                    <option value="cancelado" <?php echo $filtro_estado === 'cancelado' ? 'selected' : ''; ?>>Cancelados</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fuente" class="form-label">Fuente</label>
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
                                <label for="busqueda" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                                       value="<?php echo esc($busqueda); ?>" placeholder="Email o nombre">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel me-1"></i>Filtrar
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
                            <div class="card subscription-card <?php echo $suscripcion['status']; ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">
                                                    <i class="bi bi-person-circle me-1"></i>
                                                    <?php echo esc($suscripcion['nombre'] ?: 'Sin nombre'); ?>
                                                </h6>
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
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Email:</strong> <?php echo esc($suscripcion['email']); ?></p>
                                                    <p class="mb-1"><strong>Fecha:</strong> <?php echo date('d M Y H:i', strtotime($suscripcion['created_at'])); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>IP:</strong> <?php echo esc($suscripcion['ip_address']); ?></p>
                                                    <p class="mb-1"><strong>User Agent:</strong> 
                                                        <small class="text-muted"><?php echo esc(truncateText($suscripcion['user_agent'], 50)); ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="d-grid gap-2">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?php echo $suscripcion['id']; ?>">
                                                    <select name="estado" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
                                                        <option value="activo" <?php echo $suscripcion['status'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                                        <option value="inactivo" <?php echo $suscripcion['status'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                                        <option value="cancelado" <?php echo $suscripcion['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                    </select>
                                                    <input type="hidden" name="cambiar_estado" value="1">
                                                </form>
                                                
                                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta suscripción?')">
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
                            <div class="text-center py-5">
                                <i class="bi bi-envelope-open display-1 text-muted mb-3"></i>
                                <h3>No hay suscripciones</h3>
                                <p class="text-muted">No se encontraron suscripciones con los filtros seleccionados.</p>
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
