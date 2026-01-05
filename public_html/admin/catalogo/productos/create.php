<?php
/**
 * ========================================
 * ADMIN - CREAR PRODUCTO
 * ========================================
 * 
 * Formulario completo para crear un nuevo producto
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
    checkPermission('catalogo', 'crear');
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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        
        // Verificar si el código ya existe
        $stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE codigo = ?");
        $stmt->execute([$codigo]);
        if ($stmt->fetch()) {
            throw new Exception('El código/SKU ya existe. Debe ser único.');
        }
        
        // Generar slug
        $slug = generateSlug($nombre);
        
        // Verificar si el slug ya existe
        $stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        // Procesar atributos técnicos (especificaciones)
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
        
        // Insertar producto
        $stmt = $pdo->prepare("
            INSERT INTO catalogo_productos (
                codigo, nombre, slug, descripcion_corta, descripcion_larga,
                marca_id, categoria_id, precio_publico, precio_especial, moneda,
                stock, disponibilidad, destacado, nuevo, promocion,
                especificaciones, videos, meta_titulo, meta_descripcion, meta_keywords,
                estado, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, NOW(), NOW()
            )
        ");
        
        $especificaciones_json = !empty($especificaciones) ? json_encode($especificaciones, JSON_UNESCAPED_UNICODE) : null;
        $videos_json = !empty($videos) ? json_encode($videos, JSON_UNESCAPED_UNICODE) : null;
        
        $stmt->execute([
            $codigo, $nombre, $slug, $descripcion_corta, $descripcion_larga,
            $marca_id, $categoria_id, $precio_publico, $precio_especial, $moneda,
            $stock, $disponibilidad, $destacado, $nuevo, $promocion,
            $especificaciones_json, $videos_json, $meta_titulo, $meta_descripcion, $meta_keywords,
            $estado
        ]);
        
        $producto_id = $pdo->lastInsertId();
        
        // Procesar tags (guardar en caracteristicas JSON por ahora)
        if (!empty($tags)) {
            $tags_array = array_map('trim', explode(',', $tags));
            $caracteristicas = ['tags' => $tags_array];
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET caracteristicas = ? WHERE id = ?");
            $stmt->execute([json_encode($caracteristicas, JSON_UNESCAPED_UNICODE), $producto_id]);
        }
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'crear', 'catalogo', $producto_id, 'producto', [
                'nombre' => $nombre,
                'codigo' => $codigo
            ]);
        }
        
        // Redirigir a editar para agregar imágenes y documentos
        header('Location: edit.php?id=' . $producto_id . '&created=1');
        exit;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener marcas y categorías para los dropdowns
$stmt_marcas = $pdo->query("SELECT id, nombre FROM catalogo_marcas WHERE estado = 'activo' ORDER BY nombre");
$marcas = $stmt_marcas->fetchAll(PDO::FETCH_ASSOC);

$stmt_categorias = $pdo->query("SELECT id, nombre FROM catalogo_categorias WHERE estado = 'activo' ORDER BY nombre");
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'create.php';
$current_dir = 'productos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .spec-row {
            margin-bottom: 0.5rem;
        }
        
        .video-input-group {
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-plus-circle me-2"></i>Crear Nuevo Producto
                            </h2>
                            <p class="mb-0 opacity-75">Completa todos los campos para agregar un producto al catálogo</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Lista
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Formulario -->
                <form method="POST" action="" id="product-form">
                    
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
                                           id="nombre"
                                           required
                                           placeholder="Ej: Simulador de Paciente Adulto">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Código/SKU *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="codigo" 
                                           required
                                           placeholder="Ej: SIM-001">
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
                                        <option value="<?php echo $marca['id']; ?>">
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
                                        <option value="<?php echo $cat['id']; ?>">
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
                                   placeholder="Ej: simulador, adulto, emergencias, trauma">
                            <small class="form-text text-muted">Separa los tags con comas</small>
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
                                      rows="3"
                                      placeholder="Breve descripción del producto (aparece en listados)"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Larga</label>
                            <textarea class="form-control" 
                                      name="descripcion_larga" 
                                      id="descripcion_larga"
                                      rows="10"></textarea>
                        </div>
                    </div>
                    
                    <!-- Sección 3: Precios y Disponibilidad -->
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
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00">
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
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Moneda</label>
                                    <select class="form-select" name="moneda">
                                        <option value="MXN" selected>MXN - Peso Mexicano</option>
                                        <option value="USD">USD - Dólar Americano</option>
                                        <option value="EUR">EUR - Euro</option>
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
                                           min="0"
                                           value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Disponibilidad</label>
                                    <select class="form-select" name="disponibilidad">
                                        <option value="disponible" selected>Disponible</option>
                                        <option value="agotado">Agotado</option>
                                        <option value="por_pedido">Por Pedido</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección 4: Atributos Técnicos -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-gear me-2"></i>Atributos Técnicos
                            </h4>
                        </div>
                        
                        <div id="specs-container">
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
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-spec">
                            <i class="bi bi-plus-circle me-1"></i>Agregar Atributo
                        </button>
                    </div>
                    
                    <!-- Sección 5: Videos -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="mb-0">
                                <i class="bi bi-play-circle me-2"></i>Videos
                            </h4>
                        </div>
                        
                        <div id="videos-container">
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
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-video">
                            <i class="bi bi-plus-circle me-1"></i>Agregar Video
                        </button>
                    </div>
                    
                    <!-- Sección 6: Estado y SEO -->
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
                                        <option value="borrador" selected>Borrador</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="destacado" id="destacado">
                                <label class="form-check-label" for="destacado">Destacado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="nuevo" id="nuevo">
                                <label class="form-check-label" for="nuevo">Nuevo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="promocion" id="promocion">
                                <label class="form-check-label" for="promocion">En Promoción</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Título (SEO)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="meta_titulo" 
                                   placeholder="Título para motores de búsqueda">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Descripción (SEO)</label>
                            <textarea class="form-control" 
                                      name="meta_descripcion" 
                                      rows="2"
                                      placeholder="Descripción para motores de búsqueda (máx. 160 caracteres)"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Keywords (SEO)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="meta_keywords" 
                                   placeholder="Palabras clave separadas por comas">
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Crear Producto
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
            
            specs.forEach((spec, index) => {
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
            
            videos.forEach((video, index) => {
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
    </script>
</body>
</html>

