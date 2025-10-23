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
        .admin-sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        .admin-content {
            background-color: #ffffff;
            min-height: 100vh;
        }
        .comment-card {
            border-left: 4px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .comment-card.pendiente {
            border-left-color: #ffc107;
        }
        .comment-card.aprobado {
            border-left-color: #198754;
        }
        .comment-card.rechazado {
            border-left-color: #dc3545;
        }
        .comment-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .comment-meta {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .status-badge {
            font-size: 0.75rem;
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="bi bi-chat-dots me-2"></i>Gestión de Comentarios
                    </h2>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning"><?php echo $estadisticas['pendientes']; ?> Pendientes</span>
                        <span class="badge bg-success"><?php echo $estadisticas['aprobados']; ?> Aprobados</span>
                        <span class="badge bg-danger"><?php echo $estadisticas['rechazados']; ?> Rechazados</span>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="todos" <?php echo $estado_filtro === 'todos' ? 'selected' : ''; ?>>Todos</option>
                                    <option value="pendiente" <?php echo $estado_filtro === 'pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                                    <option value="aprobado" <?php echo $estado_filtro === 'aprobado' ? 'selected' : ''; ?>>Aprobados</option>
                                    <option value="rechazado" <?php echo $estado_filtro === 'rechazado' ? 'selected' : ''; ?>>Rechazados</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="articulo" class="form-label">Artículo</label>
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
                                    <i class="bi bi-funnel me-1"></i>Filtrar
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
                            <div class="card comment-card <?php echo $comentario['estado']; ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">
                                                    <i class="bi bi-person-circle me-1"></i>
                                                    <?php echo esc($comentario['nombre']); ?>
                                                </h6>
                                                <span class="badge status-badge 
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
                                            
                                            <p class="card-text"><?php echo nl2br(esc($comentario['comentario'])); ?></p>
                                            
                                            <div class="comment-meta">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Email:</strong> <?php echo esc($comentario['email']); ?><br>
                                                        <strong>IP:</strong> <?php echo esc($comentario['ip_address']); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Fecha:</strong> <?php echo date('d M Y H:i', strtotime($comentario['created_at'])); ?><br>
                                                        <strong>Artículo:</strong> 
                                                        <a href="<?php echo siteUrl('blog-detalle.php?slug=' . $comentario['articulo_slug']); ?>" 
                                                           target="_blank" class="text-decoration-none">
                                                            <?php echo esc(truncateText($comentario['articulo_titulo'], 40)); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="d-grid gap-2">
                                                <?php if ($comentario['estado'] === 'pendiente'): ?>
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
                                                <?php endif; ?>
                                                
                                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este comentario?')">
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
                            <div class="text-center py-5">
                                <i class="bi bi-chat-dots display-1 text-muted mb-3"></i>
                                <h3>No hay comentarios</h3>
                                <p class="text-muted">No se encontraron comentarios con los filtros seleccionados.</p>
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
