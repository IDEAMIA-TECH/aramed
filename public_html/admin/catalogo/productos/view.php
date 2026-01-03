<?php
/**
 * ========================================
 * ADMIN - VISTA DETALLADA DEL PRODUCTO
 * ========================================
 * 
 * Vista completa de un producto con toda su información
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/connection.php';
require_once __DIR__ . '/../../auth_check.php';

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

// Obtener ID del producto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Cargar producto con relaciones
$stmt = $pdo->prepare("
    SELECT p.*, 
           m.nombre as marca_nombre, m.logo as marca_logo,
           c.nombre as categoria_nombre, c.color as categoria_color
    FROM catalogo_productos p
    LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
    LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header('Location: index.php?error=not_found');
    exit;
}

// Cargar imágenes
$stmt = $pdo->prepare("SELECT * FROM catalogo_producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
$stmt->execute([$id]);
$imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cargar documentos
$stmt = $pdo->prepare("SELECT * FROM catalogo_producto_documentos WHERE producto_id = ? ORDER BY orden ASC");
$stmt->execute([$id]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar datos JSON
$especificaciones = [];
if ($producto['especificaciones']) {
    $especificaciones = json_decode($producto['especificaciones'], true) ?: [];
}

$videos = [];
if ($producto['videos']) {
    $videos = json_decode($producto['videos'], true) ?: [];
}

$tags = [];
if ($producto['caracteristicas']) {
    $caracteristicas = json_decode($producto['caracteristicas'], true);
    if (isset($caracteristicas['tags'])) {
        $tags = $caracteristicas['tags'];
    }
}

$current_page = 'view.php';
$current_dir = 'productos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Producto - Admin <?php echo SITE_NAME; ?></title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .info-section {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .info-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .product-image-main {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .product-image-thumb {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        .product-image-thumb:hover {
            opacity: 0.7;
        }
        
        .spec-table {
            width: 100%;
        }
        
        .spec-table td {
            padding: 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .spec-table td:first-child {
            font-weight: 600;
            width: 40%;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-eye me-2"></i>Vista del Producto
                            </h2>
                            <p class="mb-0 opacity-75"><?php echo esc($producto['nombre']); ?></p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-light">
                                <i class="bi bi-pencil me-2"></i>Editar
                            </a>
                            <a href="index.php" class="btn btn-light">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Columna izquierda: Imágenes y Media -->
                    <div class="col-md-5">
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-images me-2"></i>Imágenes
                            </h5>
                            
                            <?php if (!empty($imagenes)): ?>
                                <?php 
                                $imagen_principal = null;
                                foreach ($imagenes as $img) {
                                    if ($img['es_principal']) {
                                        $imagen_principal = $img;
                                        break;
                                    }
                                }
                                if (!$imagen_principal && !empty($imagenes)) {
                                    $imagen_principal = $imagenes[0];
                                }
                                ?>
                                
                                <?php if ($imagen_principal): ?>
                                <div class="mb-3">
                                    <img src="<?php echo SITE_URL . '/' . esc($imagen_principal['imagen_url']); ?>" 
                                         alt="<?php echo esc($producto['nombre']); ?>" 
                                         class="product-image-main"
                                         id="main-image"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23f8f9fa\' width=\'400\' height=\'400\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ESin imagen%3C/text%3E%3C/svg%3E'">
                                </div>
                                <?php endif; ?>
                                
                                <?php if (count($imagenes) > 1): ?>
                                <div class="row g-2">
                                    <?php foreach ($imagenes as $img): ?>
                                    <div class="col-3">
                                        <img src="<?php echo SITE_URL . '/' . esc($img['imagen_url']); ?>" 
                                             alt="<?php echo esc($img['imagen_alt'] ?? ''); ?>" 
                                             class="product-image-thumb"
                                             onclick="document.getElementById('main-image').src = this.src"
                                             onerror="this.style.display='none'">
                                        <?php if ($img['es_principal']): ?>
                                        <small class="text-success d-block text-center mt-1">
                                            <i class="bi bi-star-fill"></i> Principal
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    <p class="mt-2">No hay imágenes</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Videos -->
                        <?php if (!empty($videos)): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-play-circle me-2"></i>Videos
                            </h5>
                            <?php foreach ($videos as $video): ?>
                            <div class="mb-3">
                                <a href="<?php echo esc($video); ?>" target="_blank" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-play-circle me-2"></i>Ver Video
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Columna derecha: Información -->
                    <div class="col-md-7">
                        <!-- Información Básica -->
                        <div class="info-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h3 class="mb-2"><?php echo esc($producto['nombre']); ?></h3>
                                    <div class="d-flex gap-2 flex-wrap mb-2">
                                        <?php if ($producto['marca_nombre']): ?>
                                        <span class="badge bg-info">
                                            <?php echo esc($producto['marca_nombre']); ?>
                                        </span>
                                        <?php endif; ?>
                                        <?php if ($producto['categoria_nombre']): ?>
                                        <span class="badge" style="background-color: <?php echo esc($producto['categoria_color'] ?? '#0066CC'); ?>;">
                                            <?php echo esc($producto['categoria_nombre']); ?>
                                        </span>
                                        <?php endif; ?>
                                        <span class="badge <?php 
                                            echo $producto['estado'] === 'activo' ? 'bg-success' : 
                                                ($producto['estado'] === 'inactivo' ? 'bg-secondary' : 'bg-warning'); 
                                        ?>">
                                            <?php echo ucfirst($producto['estado']); ?>
                                        </span>
                                        <?php if ($producto['destacado']): ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-star-fill"></i> Destacado
                                        </span>
                                        <?php endif; ?>
                                        <?php if ($producto['nuevo']): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-newspaper"></i> Nuevo
                                        </span>
                                        <?php endif; ?>
                                        <?php if ($producto['promocion']): ?>
                                        <span class="badge bg-warning">
                                            <i class="bi bi-tag-fill"></i> Promoción
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-section">
                                <strong>Código/SKU:</strong> <?php echo esc($producto['codigo']); ?>
                            </div>
                            
                            <?php if ($producto['descripcion_corta']): ?>
                            <div class="info-section">
                                <strong>Descripción Corta:</strong>
                                <p class="mb-0"><?php echo esc($producto['descripcion_corta']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($producto['descripcion_larga']): ?>
                            <div class="info-section">
                                <strong>Descripción Larga:</strong>
                                <div><?php echo $producto['descripcion_larga']; ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($tags)): ?>
                            <div class="info-section">
                                <strong>Tags:</strong>
                                <div>
                                    <?php foreach ($tags as $tag): ?>
                                    <span class="badge bg-secondary me-1"><?php echo esc($tag); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Precios y Disponibilidad -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-currency-dollar me-2"></i>Precios y Disponibilidad
                            </h5>
                            
                            <div class="row">
                                <?php if ($producto['precio_publico']): ?>
                                <div class="col-md-6 mb-3">
                                    <strong>Precio Público:</strong><br>
                                    <span class="h4"><?php echo $producto['moneda']; ?> $<?php echo number_format($producto['precio_publico'], 2); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($producto['precio_especial']): ?>
                                <div class="col-md-6 mb-3">
                                    <strong>Precio Especial:</strong><br>
                                    <span class="h4 text-danger"><?php echo $producto['moneda']; ?> $<?php echo number_format($producto['precio_especial'], 2); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="col-md-6 mb-3">
                                    <strong>Stock:</strong><br>
                                    <span class="h5"><?php echo number_format($producto['stock']); ?> unidades</span>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <strong>Disponibilidad:</strong><br>
                                    <span class="badge <?php 
                                        echo $producto['disponibilidad'] === 'disponible' ? 'bg-success' : 
                                            ($producto['disponibilidad'] === 'agotado' ? 'bg-danger' : 'bg-warning'); 
                                    ?>">
                                        <?php 
                                        echo $producto['disponibilidad'] === 'disponible' ? 'Disponible' : 
                                            ($producto['disponibilidad'] === 'agotado' ? 'Agotado' : 'Por Pedido'); 
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Especificaciones Técnicas -->
                        <?php if (!empty($especificaciones)): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-gear me-2"></i>Especificaciones Técnicas
                            </h5>
                            
                            <table class="spec-table">
                                <?php foreach ($especificaciones as $key => $value): ?>
                                <tr>
                                    <td><?php echo esc($key); ?></td>
                                    <td><?php echo esc($value); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Documentos -->
                        <?php if (!empty($documentos)): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-file-pdf me-2"></i>Documentos
                            </h5>
                            
                            <div class="list-group">
                                <?php foreach ($documentos as $doc): ?>
                                <a href="<?php echo SITE_URL . '/' . esc($doc['archivo_url']); ?>" 
                                   target="_blank" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-pdf me-2 text-danger"></i>
                                            <strong><?php echo esc($doc['nombre']); ?></strong>
                                            <small class="text-muted ms-2">(<?php echo esc($doc['tipo']); ?>)</small>
                                        </div>
                                        <i class="bi bi-download"></i>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- SEO -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-search me-2"></i>SEO
                            </h5>
                            
                            <?php if ($producto['meta_titulo']): ?>
                            <div class="info-section">
                                <strong>Meta Título:</strong><br>
                                <small><?php echo esc($producto['meta_titulo']); ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($producto['meta_descripcion']): ?>
                            <div class="info-section">
                                <strong>Meta Descripción:</strong><br>
                                <small><?php echo esc($producto['meta_descripcion']); ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($producto['meta_keywords']): ?>
                            <div class="info-section">
                                <strong>Meta Keywords:</strong><br>
                                <small><?php echo esc($producto['meta_keywords']); ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <div class="info-section">
                                <strong>URL:</strong><br>
                                <small>
                                    <a href="<?php echo SITE_URL . '/producto.php?slug=' . esc($producto['slug']); ?>" 
                                       target="_blank">
                                        <?php echo SITE_URL . '/producto.php?slug=' . esc($producto['slug']); ?>
                                    </a>
                                </small>
                            </div>
                        </div>
                        
                        <!-- Estadísticas -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-bar-chart me-2"></i>Estadísticas
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Visitas:</strong> <?php echo number_format($producto['visitas']); ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Creado:</strong> <?php echo date('d/m/Y H:i', strtotime($producto['created_at'])); ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Actualizado:</strong> <?php echo $producto['updated_at'] ? date('d/m/Y H:i', strtotime($producto['updated_at'])) : 'Nunca'; ?>
                                </div>
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

