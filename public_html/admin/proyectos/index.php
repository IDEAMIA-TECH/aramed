<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE PROYECTOS
 * ========================================
 * 
 * Listado de proyectos con filtros y búsqueda
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
    checkPermission('proyectos', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar acciones masivas
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['selected_ids'])) {
    try {
        if (function_exists('checkPermission')) {
            checkPermission('proyectos', 'editar');
        }
        
        $bulk_action = $_POST['bulk_action'];
        $selected_ids = array_map('intval', $_POST['selected_ids']);
        $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
        
        if ($bulk_action === 'publicar') {
            $stmt = $pdo->prepare("UPDATE proyectos SET estado = 'publicado', updated_at = NOW() WHERE id IN ($placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' proyecto(s) publicado(s)';
        } elseif ($bulk_action === 'borrador') {
            $stmt = $pdo->prepare("UPDATE proyectos SET estado = 'borrador', updated_at = NOW() WHERE id IN ($placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' proyecto(s) movido(s) a borrador';
        } elseif ($bulk_action === 'eliminar') {
            $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id IN ($placeholders)");
            $stmt->execute($selected_ids);
            $success_message = count($selected_ids) . ' proyecto(s) eliminado(s)';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Filtros
$filtro_estado = $_GET['estado'] ?? '';
$filtro_ano = $_GET['ano'] ?? '';
$filtro_sector = $_GET['sector'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_estado) {
    $where_conditions[] = "p.estado = ?";
    $params[] = $filtro_estado;
}

if ($filtro_ano) {
    $where_conditions[] = "p.ano = ?";
    $params[] = (int)$filtro_ano;
}

if ($filtro_sector) {
    $where_conditions[] = "p.sector = ?";
    $params[] = $filtro_sector;
}

if ($filtro_categoria) {
    $where_conditions[] = "p.categoria = ?";
    $params[] = $filtro_categoria;
}

if ($busqueda) {
    $where_conditions[] = "(p.titulo LIKE ? OR p.descripcion_corta LIKE ? OR p.descripcion_larga LIKE ?)";
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener total de registros
$count_sql = "SELECT COUNT(*) FROM proyectos p $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_proyectos = $count_stmt->fetchColumn();
$total_pages = ceil($total_proyectos / $per_page);

// Obtener proyectos
$sql = "SELECT p.*, 
               COUNT(DISTINCT pi.id) as total_imagenes,
               COUNT(DISTINCT pv.id) as total_videos,
               COUNT(DISTINCT pd.id) as total_documentos
        FROM proyectos p
        LEFT JOIN proyecto_imagenes pi ON p.id = pi.proyecto_id
        LEFT JOIN proyecto_videos pv ON p.id = pv.proyecto_id
        LEFT JOIN proyecto_documentos pd ON p.id = pd.proyecto_id
        $where_clause
        GROUP BY p.id
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener valores únicos para filtros
$stmt = $pdo->query("SELECT DISTINCT ano FROM proyectos WHERE ano IS NOT NULL ORDER BY ano DESC");
$anos = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT DISTINCT sector FROM proyectos WHERE sector IS NOT NULL ORDER BY sector");
$sectores = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT DISTINCT categoria FROM proyectos WHERE categoria IS NOT NULL ORDER BY categoria");
$categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Estadísticas
$stats = [];
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado = 'publicado' THEN 1 ELSE 0 END) as publicados,
    SUM(CASE WHEN estado = 'borrador' THEN 1 ELSE 0 END) as borradores
FROM proyectos";
$stats_stmt = $pdo->query($stats_sql);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$current_page = 'index.php';
$current_dir = 'proyectos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --dark-color: #212529;
            --border-radius: 8px;
            --shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .project-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .project-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }
        
        .project-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .project-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-folder me-2"></i>Proyectos
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona los proyectos realizados</p>
                        </div>
                        <?php if (function_exists('hasPermission') && hasPermission($current_user['id'] ?? 0, 'proyectos', 'crear')): ?>
                        <a href="create.php" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-primary"><?php echo number_format($stats['total']); ?></h3>
                            <small class="text-muted">Total Proyectos</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-success"><?php echo number_format($stats['publicados']); ?></h3>
                            <small class="text-muted">Publicados</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card text-center">
                            <h3 class="mb-1 text-warning"><?php echo number_format($stats['borradores']); ?></h3>
                            <small class="text-muted">Borradores</small>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="">Todos</option>
                                    <option value="publicado" <?php echo $filtro_estado === 'publicado' ? 'selected' : ''; ?>>Publicado</option>
                                    <option value="borrador" <?php echo $filtro_estado === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Año</label>
                                <select class="form-select" name="ano">
                                    <option value="">Todos</option>
                                    <?php foreach ($anos as $ano): ?>
                                    <option value="<?php echo $ano; ?>" <?php echo $filtro_ano == $ano ? 'selected' : ''; ?>>
                                        <?php echo $ano; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sector</label>
                                <select class="form-select" name="sector">
                                    <option value="">Todos</option>
                                    <?php foreach ($sectores as $sector): ?>
                                    <option value="<?php echo esc($sector); ?>" <?php echo $filtro_sector === $sector ? 'selected' : ''; ?>>
                                        <?php echo esc($sector); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" name="categoria">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo esc($categoria); ?>" <?php echo $filtro_categoria === $categoria ? 'selected' : ''; ?>>
                                        <?php echo esc($categoria); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Búsqueda</label>
                                <input type="text" class="form-control" name="busqueda" value="<?php echo esc($busqueda); ?>" placeholder="Buscar por título, descripción...">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-2"></i>Filtrar
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Listado -->
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Proyectos (<?php echo number_format($total_proyectos); ?>)</h5>
                            <?php if (function_exists('hasPermission') && hasPermission($current_user['id'] ?? 0, 'proyectos', 'editar')): ?>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" id="bulk-action" style="width: auto;">
                                    <option value="">Acción masiva</option>
                                    <option value="publicar">Publicar</option>
                                    <option value="borrador">Mover a borrador</option>
                                    <option value="eliminar">Eliminar</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showBulkAction()">
                                    <i class="bi bi-check-circle me-1"></i>Aplicar
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($proyectos)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-folder text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay proyectos</h4>
                            <p class="text-muted">No se encontraron proyectos con los filtros aplicados</p>
                            <?php if (function_exists('hasPermission') && hasPermission($current_user['id'] ?? 0, 'proyectos', 'crear')): ?>
                            <a href="create.php" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-circle me-2"></i>Crear Primer Proyecto
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <form id="bulk-form" method="POST" action="">
                            <div class="row">
                                <?php foreach ($proyectos as $proyecto): ?>
                                <div class="col-md-4 col-lg-3 mb-4">
                                    <div class="project-card position-relative">
                                        <?php if ($proyecto['imagen_principal']): ?>
                                        <img src="<?php echo SITE_URL . '/' . esc($proyecto['imagen_principal']); ?>" 
                                             alt="<?php echo esc($proyecto['titulo']); ?>" 
                                             class="project-image">
                                        <?php else: ?>
                                        <div class="project-image d-flex align-items-center justify-content-center bg-light">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <span class="badge bg-<?php echo $proyecto['estado'] === 'publicado' ? 'success' : 'warning'; ?> project-badge">
                                            <?php echo ucfirst($proyecto['estado']); ?>
                                        </span>
                                        
                                        <div class="p-3">
                                            <h6 class="mb-2">
                                                <a href="view.php?id=<?php echo $proyecto['id']; ?>" class="text-decoration-none">
                                                    <?php echo esc(truncateText($proyecto['titulo'], 50)); ?>
                                                </a>
                                            </h6>
                                            <?php if ($proyecto['descripcion_corta']): ?>
                                            <p class="text-muted small mb-2"><?php echo esc(truncateText($proyecto['descripcion_corta'], 80)); ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">
                                                    <?php if ($proyecto['ano']): ?>
                                                    <i class="bi bi-calendar me-1"></i><?php echo $proyecto['ano']; ?>
                                                    <?php endif; ?>
                                                    <?php if ($proyecto['pais']): ?>
                                                    | <i class="bi bi-geo-alt me-1"></i><?php echo esc($proyecto['pais']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            
                                            <div class="d-flex gap-1 mb-2">
                                                <span class="badge bg-info"><?php echo $proyecto['total_imagenes']; ?> img</span>
                                                <?php if ($proyecto['total_videos'] > 0): ?>
                                                <span class="badge bg-danger"><?php echo $proyecto['total_videos']; ?> vid</span>
                                                <?php endif; ?>
                                                <?php if ($proyecto['total_documentos'] > 0): ?>
                                                <span class="badge bg-secondary"><?php echo $proyecto['total_documentos']; ?> doc</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="d-flex gap-1">
                                                <input type="checkbox" name="selected_ids[]" value="<?php echo $proyecto['id']; ?>" class="project-checkbox">
                                                <a href="view.php?id=<?php echo $proyecto['id']; ?>" class="btn btn-sm btn-outline-primary flex-grow-1">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                                <?php if (function_exists('hasPermission') && hasPermission($current_user['id'] ?? 0, 'proyectos', 'editar')): ?>
                                                <a href="edit.php?id=<?php echo $proyecto['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="bulk_action" id="bulk-action-input">
                        </form>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <?php if ($total_pages > 1): ?>
                    <div class="card-footer bg-white">
                        <nav aria-label="Paginación">
                            <ul class="pagination mb-0 justify-content-center">
                                <?php
                                $query_params = $_GET;
                                unset($query_params['page']);
                                $base_url = 'index.php?' . http_build_query($query_params) . '&page=';
                                ?>
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $base_url . ($page - 1); ?>">Anterior</a>
                                </li>
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $base_url . $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $base_url . ($page + 1); ?>">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showBulkAction() {
            const action = document.getElementById('bulk-action').value;
            const checked = document.querySelectorAll('.project-checkbox:checked');
            
            if (!action) {
                alert('Selecciona una acción');
                return;
            }
            
            if (checked.length === 0) {
                alert('Selecciona al menos un proyecto');
                return;
            }
            
            if (action === 'eliminar' && !confirm('¿Estás seguro de eliminar ' + checked.length + ' proyecto(s)? Esta acción no se puede deshacer.')) {
                return;
            }
            
            document.getElementById('bulk-action-input').value = action;
            document.getElementById('bulk-form').submit();
        }
    </script>
</body>
</html>

