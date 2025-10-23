<?php
/**
 * ========================================
 * ADMIN - CREAR ARTÍCULO
 * ========================================
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

// Obtener categorías
$sql_categorias = "SELECT * FROM blog_categorias WHERE estado = 'activo' ORDER BY nombre";
$stmt_categorias = $pdo->prepare($sql_categorias);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

// Función para generar slug
function generateSlug($titulo) {
    global $pdo;
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
    $original_slug = $slug;
    $counter = 1;
    
    // Verificar si el slug ya existe
    while (true) {
        $sql = "SELECT id FROM blog_articulos WHERE slug = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$slug]);
        
        if (!$stmt->fetch()) {
            break;
        }
        
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'titulo' => sanitizeInput($_POST['titulo']),
            'slug' => generateSlug($_POST['titulo']),
            'resumen' => sanitizeInput($_POST['resumen']),
            'contenido' => $_POST['contenido'],
            'imagen_principal' => sanitizeInput($_POST['imagen_principal']),
            'imagen_og' => sanitizeInput($_POST['imagen_og']),
            'categoria_id' => (int)$_POST['categoria_id'],
            'autor' => sanitizeInput($_POST['autor']),
            'autor_email' => sanitizeEmail($_POST['autor_email']),
            'tags' => json_encode(explode(',', $_POST['tags'])),
            'meta_title' => sanitizeInput($_POST['meta_title']),
            'meta_description' => sanitizeInput($_POST['meta_description']),
            'meta_keywords' => sanitizeInput($_POST['meta_keywords']),
            'estado' => $_POST['estado'],
            'destacado' => isset($_POST['destacado']) ? 1 : 0,
            'fecha_publicacion' => $_POST['fecha_publicacion'] ?: null
        ];
        
        $sql = "
            INSERT INTO blog_articulos (
                titulo, slug, resumen, contenido, imagen_principal, imagen_og,
                categoria_id, autor, autor_email, tags, meta_title, meta_description,
                meta_keywords, estado, destacado, fecha_publicacion, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array_values($data));
        
        if ($result) {
            header('Location: index.php?success=1');
            exit;
        } else {
            $error = 'Error al crear el artículo';
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Artículo - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .admin-content {
            min-height: 100vh;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-section h5 {
            color: #0066cc;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>

            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-10 admin-content p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="bi bi-plus-circle me-2"></i>Crear Nuevo Artículo
                    </h2>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver a la Lista
                    </a>
                </div>

                <!-- Mensajes -->
                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Columna principal -->
                        <div class="col-lg-8">
                            <!-- Información básica -->
                            <div class="form-section">
                                <h5><i class="bi bi-info-circle me-2"></i>Información Básica</h5>
                                
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título del Artículo *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                    <div class="form-text">El título aparecerá en la URL del artículo</div>
                                </div>

                                <div class="mb-3">
                                    <label for="resumen" class="form-label">Resumen</label>
                                    <textarea class="form-control" id="resumen" name="resumen" rows="3" 
                                              placeholder="Breve descripción del artículo..."></textarea>
                                    <div class="form-text">Aparecerá en las tarjetas de artículo y meta descripción</div>
                                </div>

                                <div class="mb-3">
                                    <label for="contenido" class="form-label">Contenido *</label>
                                    <textarea class="form-control" id="contenido" name="contenido" rows="15" required></textarea>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="form-section">
                                <h5><i class="bi bi-search me-2"></i>SEO y Metadatos</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="meta_title" class="form-label">Meta Título</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="60">
                                        <div class="form-text">Máximo 60 caracteres</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="meta_keywords" class="form-label">Palabras Clave</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               placeholder="palabra1, palabra2, palabra3">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Descripción</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="2" maxlength="160"></textarea>
                                    <div class="form-text">Máximo 160 caracteres</div>
                                </div>

                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" 
                                           placeholder="tag1, tag2, tag3">
                                    <div class="form-text">Separados por comas</div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna lateral -->
                        <div class="col-lg-4">
                            <!-- Publicación -->
                            <div class="form-section">
                                <h5><i class="bi bi-calendar me-2"></i>Publicación</h5>
                                
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado *</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="borrador">Borrador</option>
                                        <option value="publicado">Publicado</option>
                                        <option value="archivado">Archivado</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
                                    <input type="datetime-local" class="form-control" id="fecha_publicacion" name="fecha_publicacion" 
                                           value="<?php echo date('Y-m-d\TH:i'); ?>">
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="destacado" name="destacado">
                                    <label class="form-check-label" for="destacado">
                                        <i class="bi bi-star-fill me-1"></i>Artículo Destacado
                                    </label>
                                </div>
                            </div>

                            <!-- Categoría -->
                            <div class="form-section">
                                <h5><i class="bi bi-folder me-2"></i>Categoría</h5>
                                
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoría</label>
                                    <select class="form-select" id="categoria_id" name="categoria_id">
                                        <option value="">Sin categoría</option>
                                        <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo esc($cat['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Autor -->
                            <div class="form-section">
                                <h5><i class="bi bi-person me-2"></i>Autor</h5>
                                
                                <div class="mb-3">
                                    <label for="autor" class="form-label">Nombre del Autor</label>
                                    <input type="text" class="form-control" id="autor" name="autor" 
                                           value="Aramed y Laboratorios">
                                </div>

                                <div class="mb-3">
                                    <label for="autor_email" class="form-label">Email del Autor</label>
                                    <input type="email" class="form-control" id="autor_email" name="autor_email" 
                                           value="marketing@aramedylaboratorio.com">
                                </div>
                            </div>

                            <!-- Imágenes -->
                            <div class="form-section">
                                <h5><i class="bi bi-image me-2"></i>Imágenes</h5>
                                
                                <div class="mb-3">
                                    <label for="imagen_principal" class="form-label">Imagen Principal</label>
                                    <div class="input-group">
                                        <input type="url" class="form-control" id="imagen_principal" name="imagen_principal" 
                                               placeholder="/assets/images/blog/imagen.jpg">
                                        <button class="btn btn-outline-secondary" type="button" onclick="openImageManager('imagen_principal')">
                                            <i class="bi bi-images me-1"></i>Seleccionar
                                        </button>
                                    </div>
                                    <div class="form-text">URL de la imagen principal</div>
                                    <div id="imagen_principal_preview" class="mt-2" style="display: none;">
                                        <img id="imagen_principal_img" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="imagen_og" class="form-label">Imagen Open Graph</label>
                                    <div class="input-group">
                                        <input type="url" class="form-control" id="imagen_og" name="imagen_og" 
                                               placeholder="/assets/images/blog/imagen-og.jpg">
                                        <button class="btn btn-outline-secondary" type="button" onclick="openImageManager('imagen_og')">
                                            <i class="bi bi-images me-1"></i>Seleccionar
                                        </button>
                                    </div>
                                    <div class="form-text">Imagen para redes sociales (1200x630px)</div>
                                    <div id="imagen_og_preview" class="mt-2" style="display: none;">
                                        <img id="imagen_og_img" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <a href="image-manager.php" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-images me-1"></i>Gestionar Imágenes
                                    </a>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-save me-2"></i>Guardar Artículo
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar editor de texto
            $('#contenido').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'Escribe el contenido del artículo aquí...'
            });

            // Auto-generar meta título desde el título
            $('#titulo').on('input', function() {
                const titulo = $(this).val();
                if (titulo.length <= 60) {
                    $('#meta_title').val(titulo);
                }
            });

            // Auto-generar meta descripción desde el resumen
            $('#resumen').on('input', function() {
                const resumen = $(this).val();
                if (resumen.length <= 160) {
                    $('#meta_description').val(resumen);
                }
            });
            
            // Mostrar vista previa de imágenes
            $('#imagen_principal').on('input', function() {
                const url = $(this).val();
                if (url) {
                    $('#imagen_principal_img').attr('src', url);
                    $('#imagen_principal_preview').show();
                } else {
                    $('#imagen_principal_preview').hide();
                }
            });
            
            $('#imagen_og').on('input', function() {
                const url = $(this).val();
                if (url) {
                    $('#imagen_og_img').attr('src', url);
                    $('#imagen_og_preview').show();
                } else {
                    $('#imagen_og_preview').hide();
                }
            });
        });
        
        // Función para abrir el gestor de imágenes
        function openImageManager(fieldId) {
            const url = `image-manager.php?field=${fieldId}`;
            const popup = window.open(url, 'imageManager', 'width=1000,height=700,scrollbars=yes,resizable=yes');
            
            // Escuchar mensaje del popup
            window.addEventListener('message', function(event) {
                if (event.data.type === 'imageSelected') {
                    const field = document.getElementById(fieldId);
                    const preview = document.getElementById(fieldId + '_preview');
                    const img = document.getElementById(fieldId + '_img');
                    
                    field.value = event.data.url;
                    img.src = event.data.url;
                    preview.style.display = 'block';
                    
                    popup.close();
                }
            });
        }
    </script>
</body>
</html>
