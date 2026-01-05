<?php
/**
 * ========================================
 * ADMIN - EDITAR PRODUCTO
 * ========================================
 * 
 * Formulario completo para editar un producto existente
 * Incluye gestión de imágenes y documentos
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
    checkPermission('catalogo', 'editar');
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

// Cargar producto
$stmt = $pdo->prepare("SELECT * FROM catalogo_productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header('Location: index.php?error=not_found');
    exit;
}

$success_message = '';
$error_message = '';

// Mensaje de creación exitosa
if (isset($_GET['created'])) {
    $success_message = 'Producto creado exitosamente. Ahora puedes agregar imágenes y documentos.';
}

// Procesar acciones AJAX para imágenes y documentos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    try {
        if ($_POST['ajax_action'] === 'delete_image') {
            $image_id = (int)$_POST['image_id'];
            $stmt = $pdo->prepare("SELECT imagen_url FROM catalogo_producto_imagenes WHERE id = ? AND producto_id = ?");
            $stmt->execute([$image_id, $id]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($image) {
                $image_path = __DIR__ . '/../../../' . $image['imagen_url'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
                
                $stmt = $pdo->prepare("DELETE FROM catalogo_producto_imagenes WHERE id = ? AND producto_id = ?");
                $stmt->execute([$image_id, $id]);
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Imagen no encontrada']);
            }
            exit;
            
        } elseif ($_POST['ajax_action'] === 'set_main_image') {
            $image_id = (int)$_POST['image_id'];
            
            // Quitar principal de todas las imágenes
            $stmt = $pdo->prepare("UPDATE catalogo_producto_imagenes SET es_principal = 0 WHERE producto_id = ?");
            $stmt->execute([$id]);
            
            // Marcar esta como principal
            $stmt = $pdo->prepare("UPDATE catalogo_producto_imagenes SET es_principal = 1 WHERE id = ? AND producto_id = ?");
            $stmt->execute([$image_id, $id]);
            
            // Actualizar imagen_principal en producto
            $stmt = $pdo->prepare("SELECT imagen_url FROM catalogo_producto_imagenes WHERE id = ?");
            $stmt->execute([$image_id]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($image) {
                $stmt = $pdo->prepare("UPDATE catalogo_productos SET imagen_principal = ? WHERE id = ?");
                $stmt->execute([$image['imagen_url'], $id]);
            }
            
            echo json_encode(['success' => true]);
            exit;
            
        } elseif ($_POST['ajax_action'] === 'delete_document') {
            $doc_id = (int)$_POST['doc_id'];
            $stmt = $pdo->prepare("SELECT archivo_url FROM catalogo_producto_documentos WHERE id = ? AND producto_id = ?");
            $stmt->execute([$doc_id, $id]);
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($doc) {
                $doc_path = __DIR__ . '/../../../' . $doc['archivo_url'];
                if (file_exists($doc_path)) {
                    @unlink($doc_path);
                }
                
                $stmt = $pdo->prepare("DELETE FROM catalogo_producto_documentos WHERE id = ? AND producto_id = ?");
                $stmt->execute([$doc_id, $id]);
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Documento no encontrado']);
            }
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Procesar formulario principal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_action'])) {
    try {
        // Datos básicos
        $nombre = trim($_POST['nombre'] ?? '');
        $codigo = trim($_POST['codigo'] ?? '');
        $marca_id = (int)($_POST['marca_id'] ?? 0);
        $categoria_id = (int)($_POST['categoria_id'] ?? 0);
        $tags = trim($_POST['tags'] ?? '');
        
        // Contenido
        $descripcion_corta = trim($_POST['descripcion_corta'] ?? '');
        $descripcion_larga = $_POST['descripcion_larga'] ?? '';
        
        // Precios y disponibilidad
        $precio_publico = !empty($_POST['precio_publico']) ? (float)$_POST['precio_publico'] : null;
        $precio_especial = !empty($_POST['precio_especial']) ? (float)$_POST['precio_especial'] : null;
        $moneda = $_POST['moneda'] ?? 'MXN';
        $stock = (int)($_POST['stock'] ?? 0);
        $disponibilidad = $_POST['disponibilidad'] ?? 'disponible';
        
        // Flags
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        $nuevo = isset($_POST['nuevo']) ? 1 : 0;
        $promocion = isset($_POST['promocion']) ? 1 : 0;
        
        // Estado y SEO
        $estado = $_POST['estado'] ?? 'borrador';
        $meta_titulo = trim($_POST['meta_titulo'] ?? '');
        $meta_descripcion = trim($_POST['meta_descripcion'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        
        // Validaciones
        if (empty($nombre)) {
            throw new Exception('El nombre es obligatorio');
        }
        
        if (empty($codigo)) {
            throw new Exception('El código/SKU es obligatorio');
        }
        
        if ($marca_id <= 0) {
            throw new Exception('Debes seleccionar una marca');
        }
        
        if ($categoria_id <= 0) {
            throw new Exception('Debes seleccionar una categoría');
        }
        
        // Verificar si el código ya existe (excepto el actual)
        $stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE codigo = ? AND id != ?");
        $stmt->execute([$codigo, $id]);
        if ($stmt->fetch()) {
            throw new Exception('El código/SKU ya existe. Debe ser único.');
        }
        
        // Generar slug
        $slug = generateSlug($nombre);
        
        // Verificar si el slug ya existe (excepto el actual)
        $stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        // Procesar atributos técnicos
        $especificaciones = [];
        if (isset($_POST['spec_key']) && isset($_POST['spec_value'])) {
            $spec_keys = $_POST['spec_key'];
            $spec_values = $_POST['spec_value'];
            for ($i = 0; $i < count($spec_keys); $i++) {
                $key = trim($spec_keys[$i] ?? '');
                $value = trim($spec_values[$i] ?? '');
                if (!empty($key) && !empty($value)) {
                    $especificaciones[$key] = $value;
                }
            }
        }
        
        // Procesar videos
        $videos = [];
        if (isset($_POST['video_urls'])) {
            $video_urls = $_POST['video_urls'];
            foreach ($video_urls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    $videos[] = $url;
                }
            }
        }
        
        // Actualizar producto
        $stmt = $pdo->prepare("
            UPDATE catalogo_productos SET
                codigo = ?, nombre = ?, slug = ?, descripcion_corta = ?, descripcion_larga = ?,
                marca_id = ?, categoria_id = ?, precio_publico = ?, precio_especial = ?, moneda = ?,
                stock = ?, disponibilidad = ?, destacado = ?, nuevo = ?, promocion = ?,
                especificaciones = ?, videos = ?, meta_titulo = ?, meta_descripcion = ?, meta_keywords = ?,
                estado = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $especificaciones_json = !empty($especificaciones) ? json_encode($especificaciones, JSON_UNESCAPED_UNICODE) : null;
        $videos_json = !empty($videos) ? json_encode($videos, JSON_UNESCAPED_UNICODE) : null;
        
        $stmt->execute([
            $codigo, $nombre, $slug, $descripcion_corta, $descripcion_larga,
            $marca_id, $categoria_id, $precio_publico, $precio_especial, $moneda,
            $stock, $disponibilidad, $destacado, $nuevo, $promocion,
            $especificaciones_json, $videos_json, $meta_titulo, $meta_descripcion, $meta_keywords,
            $estado, $id
        ]);
        
        // Procesar tags
        if (!empty($tags)) {
            $tags_array = array_map('trim', explode(',', $tags));
            $caracteristicas = ['tags' => $tags_array];
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET caracteristicas = ? WHERE id = ?");
            $stmt->execute([json_encode($caracteristicas, JSON_UNESCAPED_UNICODE), $id]);
        }
        
        // Recargar producto
        $stmt = $pdo->prepare("SELECT * FROM catalogo_productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'catalogo', $id, 'producto', [
                'nombre' => $nombre,
                'codigo' => $codigo
            ]);
        }
        
        $success_message = 'Producto actualizado exitosamente';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Cargar datos relacionados
$especificaciones = [];
if ($producto['especificaciones']) {
    $especificaciones = json_decode($producto['especificaciones'], true) ?: [];
}

$videos = [];
if ($producto['videos']) {
    $videos = json_decode($producto['videos'], true) ?: [];
}

$tags = '';
if ($producto['caracteristicas']) {
    $caracteristicas = json_decode($producto['caracteristicas'], true);
    if (isset($caracteristicas['tags'])) {
        $tags = implode(', ', $caracteristicas['tags']);
    }
}

// Obtener imágenes del producto
$stmt = $pdo->prepare("SELECT * FROM catalogo_producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, orden ASC");
$stmt->execute([$id]);
$imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener documentos del producto
$stmt = $pdo->prepare("SELECT * FROM catalogo_producto_documentos WHERE producto_id = ? ORDER BY orden ASC");
$stmt->execute([$id]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener marcas y categorías
$stmt_marcas = $pdo->query("SELECT id, nombre FROM catalogo_marcas WHERE estado = 'activo' ORDER BY nombre");
$marcas = $stmt_marcas->fetchAll(PDO::FETCH_ASSOC);

$stmt_categorias = $pdo->query("SELECT id, nombre FROM catalogo_categorias WHERE estado = 'activo' ORDER BY nombre");
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'edit.php';
$current_dir = 'productos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/4u89qw1ptzfqell0ybjhqth1cc16ilb1y0792h3momw4lk8l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
        
        .form-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .section-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .image-item {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .image-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .image-actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .image-badge {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        
        .document-item {
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 0.5rem;
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
                                <i class="bi bi-pencil-square me-2"></i>Editar Producto
                            </h2>
                            <p class="mb-0 opacity-75"><?php echo esc($producto['nombre']); ?></p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="view.php?id=<?php echo $id; ?>" class="btn btn-light">
                                <i class="bi bi-eye me-2"></i>Ver Producto
                            </a>
                            <a href="index.php" class="btn btn-light">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                        </div>
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
                
                <!-- Formulario -->
                <form method="POST" action="" id="product-form">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    
                    <!-- Sección 1: Datos Básicos -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Datos Básicos
                            </h4>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Producto *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="nombre" 
                                           value="<?php echo esc($producto['nombre']); ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Código/SKU *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="codigo" 
                                           value="<?php echo esc($producto['codigo']); ?>"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Marca *</label>
                                    <select class="form-select" name="marca_id" required>
                                        <option value="">Selecciona una marca</option>
                                        <?php foreach ($marcas as $marca): ?>
                                        <option value="<?php echo $marca['id']; ?>" <?php echo $producto['marca_id'] == $marca['id'] ? 'selected' : ''; ?>>
                                            <?php echo esc($marca['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoría *</label>
                                    <select class="form-select" name="categoria_id" required>
                                        <option value="">Selecciona una categoría</option>
                                        <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $producto['categoria_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo esc($cat['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tags (separados por comas)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="tags" 
                                   value="<?php echo esc($tags); ?>"
                                   placeholder="Ej: simulador, adulto, emergencias">
                        </div>
                    </div>
                    
                    <!-- Sección 2: Contenido -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-file-text me-2"></i>Contenido
                            </h4>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Corta</label>
                            <textarea class="form-control" 
                                      name="descripcion_corta" 
                                      rows="3"><?php echo esc($producto['descripcion_corta']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Larga</label>
                            <textarea class="form-control" 
                                      name="descripcion_larga" 
                                      id="descripcion_larga"
                                      rows="10"><?php echo $producto['descripcion_larga']; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Sección 3: Imágenes -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-images me-2"></i>Imágenes
                            </h4>
                        </div>
                        
                        <!-- Imágenes existentes -->
                        <?php if (!empty($imagenes)): ?>
                        <div class="row mb-3" id="existing-images">
                            <?php foreach ($imagenes as $img): ?>
                            <div class="col-md-3 mb-3 image-item" data-image-id="<?php echo $img['id']; ?>">
                                <img src="<?php echo SITE_URL . '/' . esc($img['imagen_url']); ?>" 
                                     alt="<?php echo esc($img['imagen_alt'] ?? ''); ?>"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'200\'%3E%3Crect fill=\'%23f8f9fa\' width=\'300\' height=\'200\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ESin imagen%3C/text%3E%3C/svg%3E'">
                                <?php if ($img['es_principal']): ?>
                                <span class="badge bg-success image-badge">Principal</span>
                                <?php endif; ?>
                                <div class="image-actions">
                                    <?php if (!$img['es_principal']): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-success set-main-image" 
                                            data-image-id="<?php echo $img['id']; ?>"
                                            title="Marcar como principal">
                                        <i class="bi bi-star"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-image" 
                                            data-image-id="<?php echo $img['id']; ?>"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Upload de nuevas imágenes -->
                        <div class="mb-3">
                            <label class="form-label">Agregar Nuevas Imágenes</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="nuevas_imagenes_input"
                                   accept="image/*"
                                   multiple>
                            <small class="form-text text-muted">Puedes seleccionar múltiples imágenes (JPG, PNG, GIF, WebP - máx. 5MB cada una)</small>
                        </div>
                        
                        <button type="button" 
                                class="btn btn-primary btn-sm" 
                                id="upload-images-btn"
                                disabled>
                            <i class="bi bi-upload me-1"></i>Subir Imágenes
                        </button>
                        
                        <div id="upload-images-progress" class="mt-2" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">Subiendo imágenes...</small>
                        </div>
                    </div>
                    
                    <!-- Sección 4: Documentos -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-file-pdf me-2"></i>Documentos
                            </h4>
                        </div>
                        
                        <!-- Documentos existentes -->
                        <?php if (!empty($documentos)): ?>
                        <div class="mb-3" id="existing-documents">
                            <?php foreach ($documentos as $doc): ?>
                            <div class="document-item" data-doc-id="<?php echo $doc['id']; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-file-pdf me-2 text-danger"></i>
                                        <strong><?php echo esc($doc['nombre']); ?></strong>
                                        <small class="text-muted ms-2">
                                            (<?php echo esc($doc['tipo']); ?>)
                                        </small>
                                    </div>
                                    <div>
                                        <a href="<?php echo SITE_URL . '/' . esc($doc['archivo_url']); ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-document" 
                                                data-doc-id="<?php echo $doc['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Upload de nuevos documentos -->
                        <div class="mb-3">
                            <label class="form-label">Agregar Nuevos Documentos</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="nuevos_documentos_input"
                                   accept=".pdf,.doc,.docx"
                                   multiple>
                            <small class="form-text text-muted">Formatos permitidos: PDF, DOC, DOCX (máx. 10MB cada uno)</small>
                        </div>
                        
                        <button type="button" 
                                class="btn btn-primary btn-sm" 
                                id="upload-documents-btn"
                                disabled>
                            <i class="bi bi-upload me-1"></i>Subir Documentos
                        </button>
                        
                        <div id="upload-documents-progress" class="mt-2" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">Subiendo documentos...</small>
                        </div>
                    </div>
                    
                    <!-- Sección 5: Precios y Disponibilidad -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-currency-dollar me-2"></i>Precios y Disponibilidad
                            </h4>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Precio Público</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control" 
                                               name="precio_publico" 
                                               value="<?php echo $producto['precio_publico'] ?? ''; ?>"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Precio Especial</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control" 
                                               name="precio_especial" 
                                               value="<?php echo $producto['precio_especial'] ?? ''; ?>"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Moneda</label>
                                    <select class="form-select" name="moneda">
                                        <option value="MXN" <?php echo $producto['moneda'] === 'MXN' ? 'selected' : ''; ?>>MXN</option>
                                        <option value="USD" <?php echo $producto['moneda'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                                        <option value="EUR" <?php echo $producto['moneda'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="stock" 
                                           value="<?php echo $producto['stock']; ?>"
                                           min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Disponibilidad</label>
                                    <select class="form-select" name="disponibilidad">
                                        <option value="disponible" <?php echo $producto['disponibilidad'] === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                                        <option value="agotado" <?php echo $producto['disponibilidad'] === 'agotado' ? 'selected' : ''; ?>>Agotado</option>
                                        <option value="por_pedido" <?php echo $producto['disponibilidad'] === 'por_pedido' ? 'selected' : ''; ?>>Por Pedido</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección 6: Atributos Técnicos -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-gear me-2"></i>Atributos Técnicos
                            </h4>
                        </div>
                        
                        <div id="specs-container">
                            <?php if (!empty($especificaciones)): ?>
                                <?php foreach ($especificaciones as $key => $value): ?>
                                <div class="spec-row">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="spec_key[]" 
                                                   value="<?php echo esc($key); ?>"
                                                   placeholder="Ej: Dimensiones">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="spec_value[]" 
                                                   value="<?php echo esc($value); ?>"
                                                   placeholder="Ej: 180cm x 60cm x 40cm">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-spec">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="spec-row">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="spec_key[]" 
                                                   placeholder="Ej: Dimensiones">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="spec_value[]" 
                                                   placeholder="Ej: 180cm x 60cm x 40cm">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-spec" style="display: none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-spec">
                            <i class="bi bi-plus-circle me-1"></i>Agregar Atributo
                        </button>
                    </div>
                    
                    <!-- Sección 7: Videos -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-play-circle me-2"></i>Videos
                            </h4>
                        </div>
                        
                        <div id="videos-container">
                            <?php if (!empty($videos)): ?>
                                <?php foreach ($videos as $video): ?>
                                <div class="video-input-group">
                                    <div class="input-group">
                                        <input type="url" 
                                               class="form-control" 
                                               name="video_urls[]" 
                                               value="<?php echo esc($video); ?>"
                                               placeholder="URL de YouTube o Vimeo">
                                        <button type="button" class="btn btn-danger remove-video">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="video-input-group">
                                    <div class="input-group">
                                        <input type="url" 
                                               class="form-control" 
                                               name="video_urls[]" 
                                               placeholder="URL de YouTube o Vimeo">
                                        <button type="button" class="btn btn-danger remove-video" style="display: none;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-video">
                            <i class="bi bi-plus-circle me-1"></i>Agregar Video
                        </button>
                    </div>
                    
                    <!-- Sección 8: Estado y SEO -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-search me-2"></i>Estado y SEO
                            </h4>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="borrador" <?php echo $producto['estado'] === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                        <option value="activo" <?php echo $producto['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo $producto['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="destacado" id="destacado" <?php echo $producto['destacado'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="destacado">Destacado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="nuevo" id="nuevo" <?php echo $producto['nuevo'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="nuevo">Nuevo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="promocion" id="promocion" <?php echo $producto['promocion'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="promocion">En Promoción</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Título (SEO)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="meta_titulo" 
                                   value="<?php echo esc($producto['meta_titulo'] ?? ''); ?>"
                                   placeholder="Título para motores de búsqueda">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Descripción (SEO)</label>
                            <textarea class="form-control" 
                                      name="meta_descripcion" 
                                      rows="2"
                                      placeholder="Descripción para motores de búsqueda"><?php echo esc($producto['meta_descripcion'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Keywords (SEO)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="meta_keywords" 
                                   value="<?php echo esc($producto['meta_keywords'] ?? ''); ?>"
                                   placeholder="Palabras clave separadas por comas">
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                        </button>
                        <a href="index.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TinyMCE
        tinymce.init({
            selector: '#descripcion_larga',
            height: 400,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
            language: 'es',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
        
        // Eliminar imagen
        document.querySelectorAll('.delete-image').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.imageId;
                if (confirm('¿Estás seguro de eliminar esta imagen?')) {
                    fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `ajax_action=delete_image&image_id=${imageId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-image-id="${imageId}"]`).remove();
                        } else {
                            alert('Error al eliminar la imagen');
                        }
                    });
                }
            });
        });
        
        // Marcar imagen como principal
        document.querySelectorAll('.set-main-image').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.imageId;
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ajax_action=set_main_image&image_id=${imageId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al marcar como principal');
                    }
                });
            });
        });
        
        // Eliminar documento
        document.querySelectorAll('.delete-document').forEach(btn => {
            btn.addEventListener('click', function() {
                const docId = this.dataset.docId;
                if (confirm('¿Estás seguro de eliminar este documento?')) {
                    fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `ajax_action=delete_document&doc_id=${docId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-doc-id="${docId}"]`).remove();
                        } else {
                            alert('Error al eliminar el documento');
                        }
                    });
                }
            });
        });
        
        // Agregar atributo técnico
        document.getElementById('add-spec').addEventListener('click', function() {
            const container = document.getElementById('specs-container');
            const newRow = document.createElement('div');
            newRow.className = 'spec-row';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="spec_key[]" placeholder="Ej: Dimensiones">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="spec_value[]" placeholder="Ej: 180cm x 60cm x 40cm">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-spec">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            updateRemoveButtons();
        });
        
        // Agregar video
        document.getElementById('add-video').addEventListener('click', function() {
            const container = document.getElementById('videos-container');
            const newVideo = document.createElement('div');
            newVideo.className = 'video-input-group';
            newVideo.innerHTML = `
                <div class="input-group">
                    <input type="url" class="form-control" name="video_urls[]" placeholder="URL de YouTube o Vimeo">
                    <button type="button" class="btn btn-danger remove-video">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newVideo);
            updateRemoveButtons();
        });
        
        // Actualizar botones de eliminar
        function updateRemoveButtons() {
            const specs = document.querySelectorAll('.spec-row');
            const videos = document.querySelectorAll('.video-input-group');
            
            specs.forEach((spec) => {
                const btn = spec.querySelector('.remove-spec');
                if (specs.length > 1) {
                    btn.style.display = 'block';
                } else {
                    btn.style.display = 'none';
                }
                btn.onclick = () => {
                    spec.remove();
                    updateRemoveButtons();
                };
            });
            
            videos.forEach((video) => {
                const btn = video.querySelector('.remove-video');
                if (videos.length > 1) {
                    btn.style.display = 'block';
                } else {
                    btn.style.display = 'none';
                }
                btn.onclick = () => {
                    video.remove();
                    updateRemoveButtons();
                };
            });
        }
        
        updateRemoveButtons();
        
        // Habilitar botón de upload de imágenes cuando se seleccionen archivos
        document.getElementById('nuevas_imagenes_input').addEventListener('change', function() {
            document.getElementById('upload-images-btn').disabled = this.files.length === 0;
        });
        
        // Habilitar botón de upload de documentos cuando se seleccionen archivos
        document.getElementById('nuevos_documentos_input').addEventListener('change', function() {
            document.getElementById('upload-documents-btn').disabled = this.files.length === 0;
        });
        
        // Upload de imágenes
        document.getElementById('upload-images-btn').addEventListener('click', function() {
            const input = document.getElementById('nuevas_imagenes_input');
            const files = input.files;
            
            if (files.length === 0) {
                alert('Por favor selecciona al menos una imagen');
                return;
            }
            
            const formData = new FormData();
            formData.append('producto_id', <?php echo $id; ?>);
            
            for (let i = 0; i < files.length; i++) {
                formData.append('imagenes[]', files[i]);
            }
            
            const progressDiv = document.getElementById('upload-images-progress');
            const progressBar = progressDiv.querySelector('.progress-bar');
            const btn = this;
            
            btn.disabled = true;
            progressDiv.style.display = 'block';
            progressBar.style.width = '0%';
            
            fetch('upload-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressBar.style.width = '100%';
                
                if (data.success) {
                    alert(data.message);
                    input.value = '';
                    btn.disabled = true;
                    
                    // Recargar página para mostrar nuevas imágenes
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + (data.error || 'Error desconocido'));
                    btn.disabled = false;
                }
                
                setTimeout(() => {
                    progressDiv.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2000);
            })
            .catch(error => {
                alert('Error al subir imágenes: ' + error.message);
                btn.disabled = false;
                progressDiv.style.display = 'none';
            });
        });
        
        // Upload de documentos
        document.getElementById('upload-documents-btn').addEventListener('click', function() {
            const input = document.getElementById('nuevos_documentos_input');
            const files = input.files;
            
            if (files.length === 0) {
                alert('Por favor selecciona al menos un documento');
                return;
            }
            
            const formData = new FormData();
            formData.append('producto_id', <?php echo $id; ?>);
            
            for (let i = 0; i < files.length; i++) {
                formData.append('documentos[]', files[i]);
            }
            
            const progressDiv = document.getElementById('upload-documents-progress');
            const progressBar = progressDiv.querySelector('.progress-bar');
            const btn = this;
            
            btn.disabled = true;
            progressDiv.style.display = 'block';
            progressBar.style.width = '0%';
            
            fetch('upload-document.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressBar.style.width = '100%';
                
                if (data.success) {
                    alert(data.message);
                    input.value = '';
                    btn.disabled = true;
                    
                    // Recargar página para mostrar nuevos documentos
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + (data.error || 'Error desconocido'));
                    btn.disabled = false;
                }
                
                setTimeout(() => {
                    progressDiv.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2000);
            })
            .catch(error => {
                alert('Error al subir documentos: ' + error.message);
                btn.disabled = false;
                progressDiv.style.display = 'none';
            });
        });
    </script>
</body>
</html>

