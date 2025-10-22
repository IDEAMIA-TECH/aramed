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
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-0">
                <div class="p-3">
                    <h5 class="text-primary mb-4">
                        <i class="bi bi-newspaper me-2"></i>Blog Admin
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-list-ul me-2"></i>Artículos
                        </a>
                        <a class="nav-link" href="categorias.php">
                            <i class="bi bi-folder me-2"></i>Categorías
                        </a>
                        <a class="nav-link" href="comentarios.php">
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
                        <i class="bi bi-plus-circle me-2"></i>Crear Nuevo Artículo
                    </h2>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver a la Lista
                    </a>
                </div>

                <!-- Mensajes -->
                <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
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
                                    <input type="text" class="form-control" id="titulo" name="titulo" 
                                           value="<?php echo isset($_POST['titulo']) ? esc($_POST['titulo']) : ''; ?>" 
                                           required>
                                    <div class="form-text">El título aparecerá en la URL del artículo</div>
                                </div>

                                <div class="mb-3">
                                    <label for="resumen" class="form-label">Resumen</label>
                                    <textarea class="form-control" id="resumen" name="resumen" rows="3" 
                                              placeholder="Breve descripción del artículo..."><?php echo isset($_POST['resumen']) ? esc($_POST['resumen']) : ''; ?></textarea>
                                    <div class="form-text">Aparecerá en las tarjetas de artículo y meta descripción</div>
                                </div>

                                <div class="mb-3">
                                    <label for="contenido" class="form-label">Contenido *</label>
                                    <textarea class="form-control" id="contenido" name="contenido" rows="15" required><?php echo isset($_POST['contenido']) ? $_POST['contenido'] : ''; ?></textarea>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="form-section">
                                <h5><i class="bi bi-search me-2"></i>SEO y Metadatos</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="meta_title" class="form-label">Meta Título</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="<?php echo isset($_POST['meta_title']) ? esc($_POST['meta_title']) : ''; ?>"
                                               maxlength="60">
                                        <div class="form-text">Máximo 60 caracteres</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="meta_keywords" class="form-label">Palabras Clave</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               value="<?php echo isset($_POST['meta_keywords']) ? esc($_POST['meta_keywords']) : ''; ?>"
                                               placeholder="palabra1, palabra2, palabra3">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Descripción</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="2" 
                                              maxlength="160"><?php echo isset($_POST['meta_description']) ? esc($_POST['meta_description']) : ''; ?></textarea>
                                    <div class="form-text">Máximo 160 caracteres</div>
                                </div>

                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" 
                                           value="<?php echo isset($_POST['tags']) ? esc($_POST['tags']) : ''; ?>"
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
                                        <option value="borrador" <?php echo (isset($_POST['estado']) && $_POST['estado'] === 'borrador') ? 'selected' : ''; ?>>Borrador</option>
                                        <option value="publicado" <?php echo (isset($_POST['estado']) && $_POST['estado'] === 'publicado') ? 'selected' : ''; ?>>Publicado</option>
                                        <option value="archivado" <?php echo (isset($_POST['estado']) && $_POST['estado'] === 'archivado') ? 'selected' : ''; ?>>Archivado</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
                                    <input type="datetime-local" class="form-control" id="fecha_publicacion" name="fecha_publicacion" 
                                           value="<?php echo isset($_POST['fecha_publicacion']) ? $_POST['fecha_publicacion'] : date('Y-m-d\TH:i'); ?>">
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="destacado" name="destacado" 
                                           <?php echo (isset($_POST['destacado']) && $_POST['destacado']) ? 'checked' : ''; ?>>
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
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>>
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
                                           value="<?php echo isset($_POST['autor']) ? esc($_POST['autor']) : 'Aramed y Laboratorios'; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="autor_email" class="form-label">Email del Autor</label>
                                    <input type="email" class="form-control" id="autor_email" name="autor_email" 
                                           value="<?php echo isset($_POST['autor_email']) ? esc($_POST['autor_email']) : 'marketing@aramedylaboratorio.com'; ?>">
                                </div>
                            </div>

                            <!-- Imágenes -->
                            <div class="form-section">
                                <h5><i class="bi bi-image me-2"></i>Imágenes</h5>
                                
                                <div class="mb-3">
                                    <label for="imagen_principal" class="form-label">Imagen Principal</label>
                                    <input type="url" class="form-control" id="imagen_principal" name="imagen_principal" 
                                           value="<?php echo isset($_POST['imagen_principal']) ? esc($_POST['imagen_principal']) : ''; ?>"
                                           placeholder="/assets/images/blog/imagen.jpg">
                                    <div class="form-text">URL de la imagen principal</div>
                                </div>

                                <div class="mb-3">
                                    <label for="imagen_og" class="form-label">Imagen Open Graph</label>
                                    <input type="url" class="form-control" id="imagen_og" name="imagen_og" 
                                           value="<?php echo isset($_POST['imagen_og']) ? esc($_POST['imagen_og']) : ''; ?>"
                                           placeholder="/assets/images/blog/imagen-og.jpg">
                                    <div class="form-text">Imagen para redes sociales (1200x630px)</div>
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

            // Auto-generar slug desde el título
            $('#titulo').on('input', function() {
                const titulo = $(this).val();
                const slug = titulo.toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
                // Aquí podrías mostrar el slug generado si quieres
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

            // Contador de caracteres para meta descripción
            $('#meta_description').on('input', function() {
                const length = $(this).val().length;
                const maxLength = 160;
                const remaining = maxLength - length;
                
                let feedback = $(this).siblings('.form-text');
                if (feedback.length === 0) {
                    feedback = $('<div class="form-text"></div>');
                    $(this).after(feedback);
                }
                
                if (remaining < 0) {
                    feedback.text(`Excedido por ${Math.abs(remaining)} caracteres`).addClass('text-danger');
                } else if (remaining < 20) {
                    feedback.text(`${remaining} caracteres restantes`).removeClass('text-danger').addClass('text-warning');
                } else {
                    feedback.text(`${remaining} caracteres restantes`).removeClass('text-danger text-warning');
                }
            });

            // Contador de caracteres para meta título
            $('#meta_title').on('input', function() {
                const length = $(this).val().length;
                const maxLength = 60;
                const remaining = maxLength - length;
                
                let feedback = $(this).siblings('.form-text');
                if (feedback.length === 0) {
                    feedback = $('<div class="form-text"></div>');
                    $(this).after(feedback);
                }
                
                if (remaining < 0) {
                    feedback.text(`Excedido por ${Math.abs(remaining)} caracteres`).addClass('text-danger');
                } else if (remaining < 10) {
                    feedback.text(`${remaining} caracteres restantes`).removeClass('text-danger').addClass('text-warning');
                } else {
                    feedback.text(`${remaining} caracteres restantes`).removeClass('text-danger text-warning');
                }
            });
        });
    </script>
</body>
</html>
