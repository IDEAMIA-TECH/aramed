<?php
/**
 * ========================================
 * ADMIN - CREAR PROYECTO
 * ========================================
 * 
 * Formulario para crear un nuevo proyecto
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
    checkPermission('proyectos', 'crear');
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
        $titulo = trim($_POST['titulo'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $sector = trim($_POST['sector'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $ano = !empty($_POST['ano']) ? (int)$_POST['ano'] : null;
        $pais = trim($_POST['pais'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $descripcion_corta = trim($_POST['descripcion_corta'] ?? '');
        $descripcion_larga = $_POST['descripcion_larga'] ?? '';
        $imagen_principal = trim($_POST['imagen_principal'] ?? '');
        $meta_titulo = trim($_POST['meta_titulo'] ?? '');
        $meta_descripcion = trim($_POST['meta_descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'borrador';
        
        // Validaciones
        if (empty($titulo)) {
            throw new Exception('El título es obligatorio');
        }
        
        // Generar slug si no se proporcionó
        if (empty($slug)) {
            $slug = generateSlug($titulo);
        } else {
            $slug = generateSlug($slug);
        }
        
        // Verificar que el slug sea único
        $stmt = $pdo->prepare("SELECT id FROM proyectos WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        // Insertar proyecto
        $sql = "INSERT INTO proyectos (
            titulo, slug, sector, categoria, ano, pais, ubicacion,
            descripcion_corta, descripcion_larga, imagen_principal,
            meta_titulo, meta_descripcion, estado
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $titulo, $slug, $sector, $categoria, $ano, $pais, $ubicacion,
            $descripcion_corta, $descripcion_larga, $imagen_principal,
            $meta_titulo, $meta_descripcion, $estado
        ]);
        
        $proyecto_id = $pdo->lastInsertId();
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'crear', 'proyectos', $proyecto_id, 'proyecto', [
                'titulo' => $titulo
            ]);
        }
        
        // Redirigir a edición para agregar medios
        header('Location: edit.php?id=' . $proyecto_id . '&created=1');
        exit;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'create.php';
$current_dir = 'proyectos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Proyecto - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
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
                                <i class="bi bi-plus-circle me-2"></i>Crear Nuevo Proyecto
                            </h2>
                            <p class="mb-0 opacity-75">Completa el formulario para crear un nuevo proyecto</p>
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
                
                <form method="POST" action="" id="project-form">
                    <!-- Información Básica -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-info-circle me-2"></i>Información Básica
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="titulo" id="titulo" required 
                                       value="<?php echo esc($_POST['titulo'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Año</label>
                                <input type="number" class="form-control" name="ano" min="2000" max="2099" 
                                       value="<?php echo esc($_POST['ano'] ?? date('Y')); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug (URL)</label>
                                <input type="text" class="form-control" name="slug" id="slug" 
                                       value="<?php echo esc($_POST['slug'] ?? ''); ?>">
                                <small class="form-text text-muted">Se generará automáticamente si se deja vacío</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="borrador" <?php echo ($_POST['estado'] ?? 'borrador') === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                    <option value="publicado" <?php echo ($_POST['estado'] ?? '') === 'publicado' ? 'selected' : ''; ?>>Publicado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sector</label>
                                <input type="text" class="form-control" name="sector" 
                                       value="<?php echo esc($_POST['sector'] ?? ''); ?>" 
                                       placeholder="Ej: Salud, Educación">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" class="form-control" name="categoria" 
                                       value="<?php echo esc($_POST['categoria'] ?? ''); ?>" 
                                       placeholder="Ej: Instalación, Capacitación">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">País</label>
                                <input type="text" class="form-control" name="pais" 
                                       value="<?php echo esc($_POST['pais'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ubicación</label>
                                <input type="text" class="form-control" name="ubicacion" 
                                       value="<?php echo esc($_POST['ubicacion'] ?? ''); ?>" 
                                       placeholder="Ciudad, Estado, etc.">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-file-text me-2"></i>Descripción
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Corta</label>
                            <textarea class="form-control" name="descripcion_corta" rows="3" 
                                      placeholder="Breve descripción del proyecto (aparecerá en listados)"><?php echo esc($_POST['descripcion_corta'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Larga</label>
                            <textarea class="form-control" name="descripcion_larga" id="descripcion_larga" rows="15"><?php echo esc($_POST['descripcion_larga'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Imagen Principal -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-image me-2"></i>Imagen Principal
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label">URL de Imagen</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="imagen_principal" id="imagen_principal" 
                                       value="<?php echo esc($_POST['imagen_principal'] ?? ''); ?>" 
                                       placeholder="uploads/proyectos/imagen.jpg">
                                <button type="button" class="btn btn-outline-secondary" onclick="openImageManager()">
                                    <i class="bi bi-folder"></i> Seleccionar
                                </button>
                            </div>
                            <small class="form-text text-muted">Ruta relativa desde la raíz del sitio</small>
                        </div>
                        
                        <div id="imagen-preview" class="mt-3" style="display: none;">
                            <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                        </div>
                    </div>
                    
                    <!-- SEO -->
                    <div class="form-card">
                        <h4 class="mb-4">
                            <i class="bi bi-search me-2"></i>SEO
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Título</label>
                            <input type="text" class="form-control" name="meta_titulo" 
                                   value="<?php echo esc($_POST['meta_titulo'] ?? ''); ?>" 
                                   maxlength="255" placeholder="Título para SEO (opcional)">
                            <small class="form-text text-muted">Si se deja vacío, se usará el título del proyecto</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Descripción</label>
                            <textarea class="form-control" name="meta_descripcion" rows="3" 
                                      maxlength="500" placeholder="Descripción para SEO (opcional)"><?php echo esc($_POST['meta_descripcion'] ?? ''); ?></textarea>
                            <small class="form-text text-muted">Máximo 500 caracteres</small>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="form-card">
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Crear Proyecto
                            </button>
                        </div>
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
            plugins: 'lists link table code image',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code | image',
            language: 'es',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
        
        // Generar slug automáticamente desde el título
        document.getElementById('titulo').addEventListener('input', function() {
            const slugInput = document.getElementById('slug');
            if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
                const slug = this.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });
        
        // Permitir edición manual del slug
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });
        
        // Preview de imagen
        document.getElementById('imagen_principal').addEventListener('input', function() {
            const preview = document.getElementById('imagen-preview');
            const img = document.getElementById('preview-img');
            if (this.value) {
                img.src = '<?php echo SITE_URL; ?>/' + this.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
        
        function openImageManager() {
            // Redirigir al gestor de imágenes del blog (reutilizar)
            window.open('../blog/image-manager.php?callback=setProjectImage', 'ImageManager', 'width=900,height=600');
        }
        
        // Callback desde el gestor de imágenes
        function setProjectImage(imageUrl) {
            document.getElementById('imagen_principal').value = imageUrl;
            document.getElementById('imagen_principal').dispatchEvent(new Event('input'));
        }
    </script>
</body>
</html>

