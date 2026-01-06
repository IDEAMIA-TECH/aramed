<?php
/**
 * ========================================
 * ADMIN - DASHBOARD DEL CATÁLOGO
 * ========================================
 * 
 * Panel principal de gestión del catálogo de productos
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
    checkPermission('catalogo', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Estadísticas del catálogo
$stats = [];

try {
    // Total de productos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos");
    $stats['total_productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos por estado
    $stmt = $pdo->query("
        SELECT estado, COUNT(*) as cantidad 
        FROM catalogo_productos 
        GROUP BY estado
    ");
    $productos_por_estado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['activos'] = 0;
    $stats['inactivos'] = 0;
    $stats['borradores'] = 0;
    
    foreach ($productos_por_estado as $row) {
        if ($row['estado'] === 'activo') {
            $stats['activos'] = $row['cantidad'];
        } elseif ($row['estado'] === 'inactivo') {
            $stats['inactivos'] = $row['cantidad'];
        } elseif ($row['estado'] === 'borrador') {
            $stats['borradores'] = $row['cantidad'];
        }
    }
    
    // Total de marcas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_marcas WHERE estado = 'activo'");
    $stats['total_marcas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de categorías
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_categorias WHERE estado = 'activo'");
    $stats['total_categorias'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos destacados
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos WHERE destacado = 1 AND estado = 'activo'");
    $stats['productos_destacados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos nuevos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos WHERE nuevo = 1 AND estado = 'activo'");
    $stats['productos_nuevos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos con promoción
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos WHERE promocion = 1 AND estado = 'activo'");
    $stats['productos_promocion'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos más visitados (últimos 30 días)
    $stmt = $pdo->query("
        SELECT p.id, p.nombre, p.visitas, m.nombre as marca_nombre
        FROM catalogo_productos p
        LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
        WHERE p.estado = 'activo'
        ORDER BY p.visitas DESC
        LIMIT 5
    ");
    $stats['mas_visitados'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Productos recientes
    $stmt = $pdo->query("
        SELECT p.id, p.nombre, p.created_at, m.nombre as marca_nombre
        FROM catalogo_productos p
        LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
        ORDER BY p.created_at DESC
        LIMIT 5
    ");
    $stats['recientes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = 'Error al cargar estadísticas: ' . $e->getMessage();
}

$current_page = 'index.php';
$current_dir = 'catalogo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Catálogo - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .quick-action-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            color: inherit;
        }
        
        .quick-action-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .list-group-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 0;
        }
        
        .list-group-item:last-child {
            border-bottom: none;
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
                                <i class="bi bi-box-seam me-2"></i>Dashboard del Catálogo
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona productos, categorías y marcas</p>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo number_format($stats['total_productos'] ?? 0); ?></div>
                            <div class="stats-label">Total Productos</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-success"><?php echo number_format($stats['activos'] ?? 0); ?></div>
                            <div class="stats-label">Productos Activos</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-warning"><?php echo number_format($stats['borradores'] ?? 0); ?></div>
                            <div class="stats-label">Borradores</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-info"><?php echo number_format($stats['total_marcas'] ?? 0); ?></div>
                            <div class="stats-label">Marcas Activas</div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-primary"><?php echo number_format($stats['total_categorias'] ?? 0); ?></div>
                            <div class="stats-label">Categorías Activas</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-danger"><?php echo number_format($stats['productos_destacados'] ?? 0); ?></div>
                            <div class="stats-label">Productos Destacados</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-success"><?php echo number_format($stats['productos_nuevos'] ?? 0); ?></div>
                            <div class="stats-label">Productos Nuevos</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <div class="stats-number text-warning"><?php echo number_format($stats['productos_promocion'] ?? 0); ?></div>
                            <div class="stats-label">En Promoción</div>
                        </div>
                    </div>
                </div>
                
                <!-- Accesos Rápidos -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4 class="mb-3">
                            <i class="bi bi-lightning-charge me-2"></i>Accesos Rápidos
                        </h4>
                    </div>
                    <?php if (function_exists('can') && can('catalogo', 'crear')): ?>
                    <div class="col-md-4 mb-3">
                        <a href="productos/create.php" class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <h5>Crear Producto</h5>
                            <p class="text-muted mb-0">Agregar un nuevo producto al catálogo</p>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (function_exists('can') && can('catalogo', 'crear')): ?>
                    <div class="col-md-4 mb-3">
                        <a href="categorias.php?action=create" class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="bi bi-folder-plus"></i>
                            </div>
                            <h5>Crear Categoría</h5>
                            <p class="text-muted mb-0">Agregar una nueva categoría</p>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (function_exists('can') && can('catalogo', 'crear')): ?>
                    <div class="col-md-4 mb-3">
                        <a href="marcas.php?action=create" class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="bi bi-tag"></i>
                            </div>
                            <h5>Crear Marca</h5>
                            <p class="text-muted mb-0">Agregar una nueva marca</p>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Enlaces de Navegación -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-box-seam me-2"></i>Productos
                                </h5>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="productos/index.php" class="list-group-item list-group-item-action">
                                    <i class="bi bi-list-ul me-2"></i>Ver Todos los Productos
                                </a>
                                <a href="productos/index.php?estado=activo" class="list-group-item list-group-item-action">
                                    <i class="bi bi-check-circle me-2 text-success"></i>Productos Activos
                                </a>
                                <a href="productos/index.php?estado=borrador" class="list-group-item list-group-item-action">
                                    <i class="bi bi-file-earmark me-2 text-warning"></i>Borradores
                                </a>
                                <a href="productos/index.php?destacado=1" class="list-group-item list-group-item-action">
                                    <i class="bi bi-star me-2 text-danger"></i>Productos Destacados
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-gear me-2"></i>Configuración
                                </h5>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="categorias.php" class="list-group-item list-group-item-action">
                                    <i class="bi bi-folder me-2"></i>Gestionar Categorías
                                </a>
                                <a href="marcas.php" class="list-group-item list-group-item-action">
                                    <i class="bi bi-tags me-2"></i>Gestionar Marcas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Productos Más Visitados -->
                <?php if (!empty($stats['mas_visitados'])): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-eye me-2"></i>Productos Más Visitados
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($stats['mas_visitados'] as $producto): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo esc($producto['nombre']); ?></strong>
                                                <?php if ($producto['marca_nombre']): ?>
                                                <br>
                                                <small class="text-muted"><?php echo esc($producto['marca_nombre']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <span class="badge bg-info"><?php echo number_format($producto['visitas']); ?> visitas</span>
                                                <?php if (function_exists('can') && can('catalogo', 'editar')): ?>
                                                <a href="productos/edit.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-outline-primary ms-2">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Productos Recientes -->
                <?php if (!empty($stats['recientes'])): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock me-2"></i>Productos Recientes
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($stats['recientes'] as $producto): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo esc($producto['nombre']); ?></strong>
                                                <?php if ($producto['marca_nombre']): ?>
                                                <br>
                                                <small class="text-muted"><?php echo esc($producto['marca_nombre']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($producto['created_at'])); ?></small>
                                                <?php if (function_exists('can') && can('catalogo', 'editar')): ?>
                                                <a href="productos/edit.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-outline-primary ms-2">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

