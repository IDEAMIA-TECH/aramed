<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE CATEGORÍAS DEL BLOG
 * ========================================
 * 
 * Panel de administración para gestionar categorías del blog
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar autenticación (simplificado para demo)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true; // Para demo, siempre logueado
}

// Obtener parámetros
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Manejar acciones
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_categoria'])) {
        $nombre = sanitizeInput($_POST['nombre']);
        $slug = generateSlug($nombre);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $icono = sanitizeInput($_POST['icono']);
        $color = sanitizeInput($_POST['color']);
        $estado = sanitizeInput($_POST['estado']);
        
        if (!empty($nombre)) {
            $sql = "INSERT INTO blog_categorias (nombre, slug, descripcion, icono, color, estado) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$nombre, $slug, $descripcion, $icono, $color, $estado]);
            
            if ($resultado) {
                $mensaje = 'Categoría creada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al crear la categoría';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = 'El nombre es requerido';
            $tipo_mensaje = 'warning';
        }
    } elseif (isset($_POST['editar_categoria'])) {
        $categoria_id = (int)$_POST['categoria_id'];
        $nombre = sanitizeInput($_POST['nombre']);
        $slug = generateSlug($nombre);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $icono = sanitizeInput($_POST['icono']);
        $color = sanitizeInput($_POST['color']);
        $estado = sanitizeInput($_POST['estado']);
        
        if (!empty($nombre)) {
            $sql = "UPDATE blog_categorias SET nombre = ?, slug = ?, descripcion = ?, icono = ?, color = ?, estado = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$nombre, $slug, $descripcion, $icono, $color, $estado, $categoria_id]);
            
            if ($resultado) {
                $mensaje = 'Categoría actualizada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar la categoría';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = 'El nombre es requerido';
            $tipo_mensaje = 'warning';
        }
    } elseif (isset($_POST['eliminar_categoria'])) {
        $categoria_id = (int)$_POST['categoria_id'];
        
        // Verificar si hay artículos asociados
        $sql_check = "SELECT COUNT(*) as count FROM blog_articulos WHERE categoria_id = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$categoria_id]);
        $count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            $mensaje = 'No se puede eliminar la categoría porque tiene artículos asociados';
            $tipo_mensaje = 'warning';
        } else {
            $sql = "DELETE FROM blog_categorias WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$categoria_id]);
            
            if ($resultado) {
                $mensaje = 'Categoría eliminada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar la categoría';
                $tipo_mensaje = 'danger';
            }
        }
    }
}

// Obtener categorías
$sql = "
    SELECT c.*, COUNT(a.id) as articulos_count
    FROM blog_categorias c
    LEFT JOIN blog_articulos a ON c.id = a.categoria_id
    GROUP BY c.id
    ORDER BY c.nombre ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categoría para editar
$categoria_editar = null;
if ($action === 'edit' && $id > 0) {
    $sql = "SELECT * FROM blog_categorias WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $categoria_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Admin Blog</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        .admin-sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        .admin-content {
            background-color: #ffffff;
            min-height: 100vh;
        }
        .category-card {
            border-left: 4px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .category-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-0">
                <div class="p-3">
                    <h5 class="mb-4">
                        <i class="bi bi-gear me-2"></i>Admin Blog
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-list-ul me-2"></i>Artículos
                        </a>
                        <a class="nav-link active" href="categorias.php">
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
                        <i class="bi bi-folder me-2"></i>Gestión de Categorías
                    </h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoriaModal">
                        <i class="bi bi-plus-circle me-1"></i>Nueva Categoría
                    </button>
                </div>

                <!-- Mensajes -->
                <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo esc($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Lista de categorías -->
                <div class="row">
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card category-card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="category-icon me-3" style="background-color: <?php echo $categoria['color'] ?: '#6c757d'; ?>">
                                            <i class="bi bi-<?php echo $categoria['icono'] ?: 'folder'; ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-0"><?php echo esc($categoria['nombre']); ?></h6>
                                            <small class="text-muted"><?php echo $categoria['articulos_count']; ?> artículos</small>
                                        </div>
                                        <span class="badge bg-<?php echo $categoria['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                            <?php echo $categoria['estado'] === 'activo' ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($categoria['descripcion'])): ?>
                                    <p class="card-text text-muted small"><?php echo esc(truncateText($categoria['descripcion'], 80)); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editarCategoria(<?php echo htmlspecialchars(json_encode($categoria)); ?>)">
                                            <i class="bi bi-pencil me-1"></i>Editar
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                            <input type="hidden" name="categoria_id" value="<?php echo $categoria['id']; ?>">
                                            <button type="submit" name="eliminar_categoria" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash me-1"></i>Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-folder display-1 text-muted mb-3"></i>
                                <h3>No hay categorías</h3>
                                <p class="text-muted">Crea tu primera categoría para organizar los artículos del blog.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva/Editar Categoría -->
    <div class="modal fade" id="categoriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoriaModalTitle">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="categoriaForm">
                    <div class="modal-body">
                        <input type="hidden" name="categoria_id" id="categoria_id">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="icono" class="form-label">Icono</label>
                                <select class="form-select" id="icono" name="icono">
                                    <option value="folder">Carpeta</option>
                                    <option value="heart-pulse">Salud</option>
                                    <option value="book">Educación</option>
                                    <option value="lightbulb">Tecnología</option>
                                    <option value="award">Éxito</option>
                                    <option value="calendar-event">Eventos</option>
                                    <option value="people">Personas</option>
                                    <option value="gear">Configuración</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear_categoria" id="submitBtn" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Crear Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarCategoria(categoria) {
            document.getElementById('categoriaModalTitle').textContent = 'Editar Categoría';
            document.getElementById('categoria_id').value = categoria.id;
            document.getElementById('nombre').value = categoria.nombre;
            document.getElementById('descripcion').value = categoria.descripcion || '';
            document.getElementById('icono').value = categoria.icono || 'folder';
            document.getElementById('color').value = categoria.color || '#007bff';
            document.getElementById('estado').value = categoria.estado || 'activo';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-save me-1"></i>Guardar Cambios';
            document.getElementById('submitBtn').name = 'editar_categoria';
            
            const modal = new bootstrap.Modal(document.getElementById('categoriaModal'));
            modal.show();
        }
        
        // Reset modal when closed
        document.getElementById('categoriaModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('categoriaModalTitle').textContent = 'Nueva Categoría';
            document.getElementById('categoriaForm').reset();
            document.getElementById('categoria_id').value = '';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Crear Categoría';
            document.getElementById('submitBtn').name = 'crear_categoria';
        });
    </script>
</body>
</html>
