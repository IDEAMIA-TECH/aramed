<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE SUSCRIPCIONES NEWSLETTER
 * ========================================
 * 
 * Panel para gestionar suscripciones del newsletter principal
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
        
        $sql = "UPDATE newsletter_subscriptions SET status = ? WHERE id = ?";
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
        
        $sql = "DELETE FROM newsletter_subscriptions WHERE id = ?";
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
$filtro_tipo_institucion = isset($_GET['tipo_institucion']) ? $_GET['tipo_institucion'] : 'todos';
$filtro_estado_geo = isset($_GET['estado_geo']) ? $_GET['estado_geo'] : 'todos';
$busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_estado !== 'todos') {
    $where_conditions[] = 'status = ?';
    $params[] = $filtro_estado;
}

if ($filtro_tipo_institucion !== 'todos') {
    $where_conditions[] = 'tipo_institucion = ?';
    $params[] = $filtro_tipo_institucion;
}

if ($filtro_estado_geo !== 'todos') {
    $where_conditions[] = 'estado = ?';
    $params[] = $filtro_estado_geo;
}

if (!empty($busqueda)) {
    $where_conditions[] = '(institucion LIKE ? OR nombre LIKE ? OR email_oficial LIKE ? OR email_alterno LIKE ? OR telefono_oficina LIKE ? OR telefono_celular LIKE ? OR producto_interes LIKE ? OR observaciones LIKE ?)';
    $search_term = '%' . $busqueda . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener suscripciones
$sql = "
    SELECT * FROM newsletter_subscriptions 
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
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactivos,
        SUM(CASE WHEN status = 'unsubscribed' THEN 1 ELSE 0 END) as cancelados
    FROM newsletter_subscriptions
";

$stmt_stats = $pdo->prepare($sql_stats);
$stmt_stats->execute();
$estadisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);

// Obtener opciones para filtros
$sql_tipos = "SELECT DISTINCT tipo_institucion FROM newsletter_subscriptions WHERE tipo_institucion IS NOT NULL AND tipo_institucion != '' ORDER BY tipo_institucion";
$stmt_tipos = $pdo->prepare($sql_tipos);
$stmt_tipos->execute();
$tipos_institucion = $stmt_tipos->fetchAll(PDO::FETCH_COLUMN);

$sql_estados = "SELECT DISTINCT estado FROM newsletter_subscriptions WHERE estado IS NOT NULL AND estado != '' ORDER BY estado";
$stmt_estados = $pdo->prepare($sql_estados);
$stmt_estados->execute();
$estados_geo = $stmt_estados->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscriptions - Admin <?php echo SITE_NAME; ?></title>
    
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
                        <a class="nav-link active" href="newsletter-subscriptions.php">
                            <i class="bi bi-envelope me-2"></i>Newsletter
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
                    <h2>
                        <i class="bi bi-envelope me-2"></i>Newsletter Subscriptions
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
                                    <option value="active" <?php echo $filtro_estado === 'active' ? 'selected' : ''; ?>>Activos</option>
                                    <option value="inactive" <?php echo $filtro_estado === 'inactive' ? 'selected' : ''; ?>>Inactivos</option>
                                    <option value="unsubscribed" <?php echo $filtro_estado === 'unsubscribed' ? 'selected' : ''; ?>>Cancelados</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tipo_institucion" class="form-label">Tipo Institución</label>
                                <select class="form-select" id="tipo_institucion" name="tipo_institucion">
                                    <option value="todos" <?php echo $filtro_tipo_institucion === 'todos' ? 'selected' : ''; ?>>Todos</option>
                                    <?php foreach ($tipos_institucion as $tipo): ?>
                                    <option value="<?php echo esc($tipo); ?>" <?php echo $filtro_tipo_institucion === $tipo ? 'selected' : ''; ?>>
                                        <?php echo esc($tipo); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="estado_geo" class="form-label">Estado (Geográfico)</label>
                                <select class="form-select" id="estado_geo" name="estado_geo">
                                    <option value="todos" <?php echo $filtro_estado_geo === 'todos' ? 'selected' : ''; ?>>Todos</option>
                                    <?php foreach ($estados_geo as $estado): ?>
                                    <option value="<?php echo esc($estado); ?>" <?php echo $filtro_estado_geo === $estado ? 'selected' : ''; ?>>
                                        <?php echo esc($estado); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="busqueda" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                                       value="<?php echo esc($busqueda); ?>" placeholder="Institución, nombre, email, teléfono, producto o observaciones">
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel me-1"></i>Filtrar
                                </button>
                                <a href="newsletter-subscriptions.php" class="btn btn-outline-secondary">
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
                                                    <?php echo esc($suscripcion['nombre']); ?>
                                                </h6>
                                                <span class="badge bg-<?php echo $suscripcion['status'] === 'active' ? 'success' : ($suscripcion['status'] === 'inactive' ? 'secondary' : 'danger'); ?>">
                                                    <?php echo $suscripcion['status'] === 'active' ? 'Activo' : ($suscripcion['status'] === 'inactive' ? 'Inactivo' : 'Cancelado'); ?>
                                                </span>
                                            </div>
                                            
                                            <!-- Información de la Institución -->
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <h6 class="text-primary mb-2">
                                                        <i class="bi bi-building me-1"></i>Información de la Institución
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Institución:</strong> <?php echo esc($suscripcion['institucion']); ?></p>
                                                            <p class="mb-1"><strong>Tipo:</strong> <?php echo esc($suscripcion['tipo_institucion']); ?></p>
                                                            <?php if (!empty($suscripcion['campo_adicional'])): ?>
                                                            <p class="mb-1"><strong>Campo Adicional:</strong> <?php echo esc($suscripcion['campo_adicional']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Estado:</strong> <?php echo esc($suscripcion['estado']); ?></p>
                                                            <p class="mb-1"><strong>Ciudad:</strong> <?php echo esc($suscripcion['ciudad']); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Información del Contacto -->
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <h6 class="text-success mb-2">
                                                        <i class="bi bi-person me-1"></i>Información del Contacto
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Nombre:</strong> <?php echo esc($suscripcion['nombre']); ?></p>
                                                            <p class="mb-1"><strong>Puesto:</strong> <?php echo esc($suscripcion['puesto']); ?></p>
                                                            <p class="mb-1"><strong>Email Oficial:</strong> 
                                                                <a href="mailto:<?php echo esc($suscripcion['email_oficial']); ?>" class="text-decoration-none">
                                                                    <?php echo esc($suscripcion['email_oficial']); ?>
                                                                </a>
                                                            </p>
                                                            <?php if (!empty($suscripcion['email_alterno'])): ?>
                                                            <p class="mb-1"><strong>Email Alterno:</strong> 
                                                                <a href="mailto:<?php echo esc($suscripcion['email_alterno']); ?>" class="text-decoration-none">
                                                                    <?php echo esc($suscripcion['email_alterno']); ?>
                                                                </a>
                                                            </p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Teléfono Oficina:</strong> <?php echo esc($suscripcion['telefono_oficina']); ?></p>
                                                            <?php if (!empty($suscripcion['extension'])): ?>
                                                            <p class="mb-1"><strong>Extensión:</strong> <?php echo esc($suscripcion['extension']); ?></p>
                                                            <?php endif; ?>
                                                            <?php if (!empty($suscripcion['telefono_celular'])): ?>
                                                            <p class="mb-1"><strong>Celular:</strong> <?php echo esc($suscripcion['telefono_celular']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Información de Interés -->
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <h6 class="text-warning mb-2">
                                                        <i class="bi bi-heart me-1"></i>Información de Interés
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <?php if (!empty($suscripcion['producto_interes'])): ?>
                                                            <p class="mb-1"><strong>Producto de Interés:</strong> 
                                                                <span class="badge bg-info"><?php echo esc($suscripcion['producto_interes']); ?></span>
                                                            </p>
                                                            <?php endif; ?>
                                                            <?php if (!empty($suscripcion['fecha_compra_aprox'])): ?>
                                                            <p class="mb-1"><strong>Fecha Compra Aprox:</strong> <?php echo date('d M Y', strtotime($suscripcion['fecha_compra_aprox'])); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php if (!empty($suscripcion['observaciones'])): ?>
                                                            <p class="mb-1"><strong>Observaciones:</strong></p>
                                                            <p class="mb-0 text-muted small"><?php echo esc($suscripcion['observaciones']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Metadata -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6 class="text-secondary mb-2">
                                                        <i class="bi bi-info-circle me-1"></i>Metadata
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Fecha Registro:</strong> <?php echo date('d M Y H:i', strtotime($suscripcion['created_at'])); ?></p>
                                                            <p class="mb-1"><strong>IP:</strong> <?php echo esc($suscripcion['ip_address']); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php if (!empty($suscripcion['updated_at']) && $suscripcion['updated_at'] !== $suscripcion['created_at']): ?>
                                                            <p class="mb-1"><strong>Última Actualización:</strong> <?php echo date('d M Y H:i', strtotime($suscripcion['updated_at'])); ?></p>
                                                            <?php endif; ?>
                                                            <?php if (!empty($suscripcion['user_agent'])): ?>
                                                            <p class="mb-1"><strong>User Agent:</strong> 
                                                                <small class="text-muted"><?php echo esc(truncateText($suscripcion['user_agent'], 50)); ?></small>
                                                            </p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="d-grid gap-2">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?php echo $suscripcion['id']; ?>">
                                                    <select name="estado" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
                                                        <option value="active" <?php echo $suscripcion['status'] === 'active' ? 'selected' : ''; ?>>Activo</option>
                                                        <option value="inactive" <?php echo $suscripcion['status'] === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                                                        <option value="unsubscribed" <?php echo $suscripcion['status'] === 'unsubscribed' ? 'selected' : ''; ?>>Cancelado</option>
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
                                <i class="bi bi-envelope display-1 text-muted mb-3"></i>
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
