<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión del Blog - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .admin-content {
            min-height: 100vh;
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .article-image {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
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
                        <a class="nav-link active" href="index.php">
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
                        <i class="bi bi-newspaper me-2"></i>Gestión de Artículos
                    </h2>
                    <a href="index.php?action=create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Artículo
                    </a>
                </div>

                <!-- Mensajes -->
                <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="busqueda" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                                       value="<?php echo isset($_GET['busqueda']) ? esc($_GET['busqueda']) : ''; ?>"
                                       placeholder="Título, autor...">
                            </div>
                            <div class="col-md-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo esc($cat['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos</option>
                                    <option value="borrador" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'borrador') ? 'selected' : ''; ?>>Borrador</option>
                                    <option value="publicado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'publicado') ? 'selected' : ''; ?>>Publicado</option>
                                    <option value="archivado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'archivado') ? 'selected' : ''; ?>>Archivado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search me-1"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de artículos -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Autor</th>
                                        <th>Estado</th>
                                        <th>Vistas</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($articulos)): ?>
                                        <?php foreach ($articulos as $articulo): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($articulo['imagen_principal'])): ?>
                                                <img src="<?php echo SITE_URL . $articulo['imagen_principal']; ?>" 
                                                     alt="<?php echo esc($articulo['titulo']); ?>" 
                                                     class="article-image">
                                                <?php else: ?>
                                                <div class="article-image bg-light d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo esc($articulo['titulo']); ?></strong>
                                                    <?php if ($articulo['destacado']): ?>
                                                    <span class="badge bg-warning text-dark ms-2 status-badge">
                                                        <i class="bi bi-star-fill me-1"></i>Destacado
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo esc(truncateText($articulo['resumen'], 60)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($articulo['categoria_nombre']): ?>
                                                <span class="badge rounded-pill" 
                                                      style="background-color: <?php echo $articulo['categoria_color']; ?>;">
                                                    <?php echo esc($articulo['categoria_nombre']); ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="text-muted">Sin categoría</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div><?php echo esc($articulo['autor']); ?></div>
                                                <small class="text-muted"><?php echo esc($articulo['autor_email']); ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $estado_classes = [
                                                    'borrador' => 'bg-secondary',
                                                    'publicado' => 'bg-success',
                                                    'archivado' => 'bg-warning'
                                                ];
                                                $estado_texts = [
                                                    'borrador' => 'Borrador',
                                                    'publicado' => 'Publicado',
                                                    'archivado' => 'Archivado'
                                                ];
                                                ?>
                                                <span class="badge <?php echo $estado_classes[$articulo['estado']]; ?> status-badge">
                                                    <?php echo $estado_texts[$articulo['estado']]; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info status-badge">
                                                    <i class="bi bi-eye me-1"></i><?php echo number_format($articulo['vistas']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div><?php echo date('d/m/Y', strtotime($articulo['created_at'])); ?></div>
                                                <small class="text-muted">
                                                    <?php echo date('H:i', strtotime($articulo['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="../../blog-detalle.php?slug=<?php echo $articulo['slug']; ?>" 
                                                       class="btn btn-outline-primary" 
                                                       target="_blank" 
                                                       title="Ver artículo">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="index.php?action=edit&id=<?php echo $articulo['id']; ?>" 
                                                       class="btn btn-outline-warning" 
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="index.php?action=toggle_status&id=<?php echo $articulo['id']; ?>" 
                                                       class="btn btn-outline-<?php echo $articulo['estado'] === 'publicado' ? 'secondary' : 'success'; ?>" 
                                                       title="<?php echo $articulo['estado'] === 'publicado' ? 'Despublicar' : 'Publicar'; ?>">
                                                        <i class="bi bi-<?php echo $articulo['estado'] === 'publicado' ? 'eye-slash' : 'eye'; ?>"></i>
                                                    </a>
                                                    <a href="index.php?action=delete&id=<?php echo $articulo['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       title="Eliminar"
                                                       onclick="return confirm('¿Estás seguro de eliminar este artículo?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="bi bi-newspaper display-4 text-muted mb-3"></i>
                                                <p class="text-muted">No hay artículos disponibles</p>
                                                <a href="index.php?action=create" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle me-2"></i>Crear primer artículo
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <?php echo count(array_filter($articulos, function($a) { return $a['estado'] === 'publicado'; })); ?>
                                </h5>
                                <p class="card-text">Publicados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-warning">
                                    <?php echo count(array_filter($articulos, function($a) { return $a['estado'] === 'borrador'; })); ?>
                                </h5>
                                <p class="card-text">Borradores</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-info">
                                    <?php echo array_sum(array_column($articulos, 'vistas')); ?>
                                </h5>
                                <p class="card-text">Total Vistas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <?php echo count(array_filter($articulos, function($a) { return $a['destacado'] == 1; })); ?>
                                </h5>
                                <p class="card-text">Destacados</p>
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
