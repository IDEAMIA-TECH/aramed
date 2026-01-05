<?php
/**
 * ========================================
 * ADMIN - METADATOS PERSONALIZADOS
 * ========================================
 * 
 * Gestión de metadatos específicos por entidad
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
    checkPermission('seo', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$success_message = '';
$error_message = '';

// Filtros
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_entidad_id = $_GET['entidad_id'] ?? '';

// Verificar si la tabla existe
$tabla_existe = false;
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'seo_metadatos'");
    $tabla_existe = $stmt->rowCount() > 0;
} catch (Exception $e) {
    $error_message = 'Error verificando la tabla: ' . $e->getMessage();
}

// Obtener metadatos
$where_conditions = [];
$params = [];

if ($filtro_tipo) {
    $where_conditions[] = 'tipo_entidad = ?';
    $params[] = $filtro_tipo;
}

if ($filtro_entidad_id) {
    $where_conditions[] = 'entidad_id = ?';
    $params[] = $filtro_entidad_id;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$metadatos = [];
if ($tabla_existe) {
    try {
        $sql = "SELECT * FROM seo_metadatos $where_clause ORDER BY updated_at DESC LIMIT 100";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $metadatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = 'Error consultando metadatos: ' . $e->getMessage();
        error_log("Error en metadatos.php: " . $e->getMessage());
    }
} else {
    $error_message = 'La tabla seo_metadatos no existe. Por favor ejecuta el script database/fase2/08_create_seo_tables.sql o database/fase2/00_create_all_tables.sql';
}

// Obtener estadísticas
$stats = ['total' => 0, 'por_tipo' => []];
if ($tabla_existe) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM seo_metadatos");
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->query("SELECT tipo_entidad, COUNT(*) as count FROM seo_metadatos GROUP BY tipo_entidad");
        $stats['por_tipo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = 'Error obteniendo estadísticas: ' . $e->getMessage();
        error_log("Error obteniendo estadísticas en metadatos.php: " . $e->getMessage());
    }
}

$current_page = 'metadatos.php';
$current_dir = 'seo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metadatos Personalizados - SEO Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-tags me-2"></i>Metadatos Personalizados
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona metadatos específicos por producto, artículo, proyecto, etc.</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
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
                        <div class="stat-card">
                            <h3 class="mb-1 text-primary"><?php echo number_format($stats['total']); ?></h3>
                            <small class="text-muted">Total de Metadatos</small>
                        </div>
                    </div>
                    <?php foreach ($stats['por_tipo'] as $tipo_stat): ?>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-1 text-info"><?php echo number_format($tipo_stat['count']); ?></h3>
                            <small class="text-muted"><?php echo ucfirst($tipo_stat['tipo_entidad']); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Filtros -->
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Entidad</label>
                                <select class="form-select" name="tipo" onchange="this.form.submit()">
                                    <option value="">Todos los tipos</option>
                                    <option value="producto" <?php echo $filtro_tipo === 'producto' ? 'selected' : ''; ?>>Productos</option>
                                    <option value="articulo" <?php echo $filtro_tipo === 'articulo' ? 'selected' : ''; ?>>Artículos</option>
                                    <option value="proyecto" <?php echo $filtro_tipo === 'proyecto' ? 'selected' : ''; ?>>Proyectos</option>
                                    <option value="categoria" <?php echo $filtro_tipo === 'categoria' ? 'selected' : ''; ?>>Categorías</option>
                                    <option value="pagina" <?php echo $filtro_tipo === 'pagina' ? 'selected' : ''; ?>>Páginas</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ID de Entidad</label>
                                <input type="number" class="form-control" name="entidad_id" 
                                       value="<?php echo esc($filtro_entidad_id); ?>" 
                                       placeholder="Filtrar por ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="metadatos.php" class="btn btn-secondary w-100">
                                        <i class="bi bi-x-circle me-2"></i>Limpiar Filtros
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Lista de Metadatos (<?php echo count($metadatos); ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!$tabla_existe): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Tabla no encontrada:</strong> La tabla <code>seo_metadatos</code> no existe en la base de datos.
                            <br><br>
                            <strong>Para solucionarlo:</strong>
                            <ol>
                                <li>Ejecuta el script SQL: <code>database/fase2/08_create_seo_tables.sql</code></li>
                                <li>O ejecuta el script consolidado: <code>database/fase2/00_create_all_tables.sql</code></li>
                            </ol>
                        </div>
                        <?php elseif (empty($metadatos)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                            <p class="text-muted">No hay metadatos personalizados configurados</p>
                            <small class="text-muted">Los metadatos se pueden configurar desde las páginas de edición de cada entidad (productos, artículos, proyectos, etc.)</small>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>ID Entidad</th>
                                        <th>Título</th>
                                        <th>Descripción</th>
                                        <th>Robots</th>
                                        <th>Actualizado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($metadatos as $meta): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary"><?php echo ucfirst($meta['tipo_entidad']); ?></span>
                                        </td>
                                        <td><?php echo $meta['entidad_id']; ?></td>
                                        <td>
                                            <strong><?php echo esc(substr($meta['meta_titulo'] ?? 'N/A', 0, 50)); ?></strong>
                                        </td>
                                        <td>
                                            <small><?php echo esc(substr($meta['meta_descripcion'] ?? 'N/A', 0, 80)); ?>...</small>
                                        </td>
                                        <td>
                                            <code><?php echo esc($meta['robots'] ?? 'index, follow'); ?></code>
                                        </td>
                                        <td>
                                            <small><?php echo $meta['updated_at'] ? date('d/m/Y H:i', strtotime($meta['updated_at'])) : 'N/A'; ?></small>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Información -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Nota:</strong> Los metadatos personalizados se pueden configurar desde las páginas de edición de cada entidad (productos, artículos, proyectos, etc.).
                    Esta página muestra un resumen de todos los metadatos configurados.
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

