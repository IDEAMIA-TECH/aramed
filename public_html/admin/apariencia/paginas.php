<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE PÁGINAS ESTÁTICAS
 * ========================================
 * 
 * CRUD completo para páginas estáticas personalizadas
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

// Verificar que el usuario sea admin (Apariencia es solo para admin)
$user_role = $_SESSION['admin_rol'] ?? 'editor';
if ($user_role !== 'admin') {
    header('Location: ../sin-permiso.php?modulo=configuracion&accion=editar');
    exit;
}

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('configuracion', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

$current_user = getCurrentUser();
$success_message = '';
$error_message = '';

// Procesar acciones
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create' || $action === 'edit') {
            $titulo = trim($_POST['titulo'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $contenido = $_POST['contenido'] ?? '';
            $meta_titulo = trim($_POST['meta_titulo'] ?? '');
            $meta_descripcion = trim($_POST['meta_descripcion'] ?? '');
            $meta_keywords = trim($_POST['meta_keywords'] ?? '');
            $imagen_principal = trim($_POST['imagen_principal'] ?? '');
            $plantilla = $_POST['plantilla'] ?? 'default';
            $estado = $_POST['estado'] ?? 'borrador';
            $mostrar_en_menu = isset($_POST['mostrar_en_menu']) ? 1 : 0;
            $menu_label = trim($_POST['menu_label'] ?? '');
            
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
            
            if ($action === 'create') {
                // Verificar que el slug sea único
                $stmt = $pdo->prepare("SELECT id FROM paginas_estaticas WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetch()) {
                    $slug .= '-' . time();
                }
                
                // Insertar
                $sql = "INSERT INTO paginas_estaticas (
                    titulo, slug, contenido, meta_titulo, meta_descripcion, meta_keywords,
                    imagen_principal, plantilla, estado, mostrar_en_menu, menu_label
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $titulo, $slug, $contenido, $meta_titulo, $meta_descripcion, $meta_keywords,
                    $imagen_principal, $plantilla, $estado, $mostrar_en_menu, $menu_label
                ]);
                
                $id = $pdo->lastInsertId();
                $success_message = 'Página creada exitosamente';
                $action = 'edit';
            } else {
                // Actualizar
                $publicado_at = ($estado === 'publicado') ? 'NOW()' : 'NULL';
                
                $sql = "UPDATE paginas_estaticas SET
                    titulo = ?, slug = ?, contenido = ?, meta_titulo = ?, meta_descripcion = ?,
                    meta_keywords = ?, imagen_principal = ?, plantilla = ?, estado = ?,
                    mostrar_en_menu = ?, menu_label = ?,
                    publicado_at = CASE WHEN ? = 'publicado' AND publicado_at IS NULL THEN NOW() ELSE publicado_at END
                    WHERE id = ?";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $titulo, $slug, $contenido, $meta_titulo, $meta_descripcion,
                    $meta_keywords, $imagen_principal, $plantilla, $estado,
                    $mostrar_en_menu, $menu_label, $estado, $id
                ]);
                
                $success_message = 'Página actualizada exitosamente';
            }
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM paginas_estaticas WHERE id = ?");
            $stmt->execute([$id]);
            $success_message = 'Página eliminada exitosamente';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = 'Error: ' . $e->getMessage();
    }
}

// Obtener datos
$pagina = null;
$paginas = [];

if (($action === 'edit' || $action === 'view') && $id) {
    $stmt = $pdo->prepare("SELECT * FROM paginas_estaticas WHERE id = ?");
    $stmt->execute([$id]);
    $pagina = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pagina) {
        $error_message = 'Página no encontrada';
        $action = 'list';
    }
}

if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM paginas_estaticas ORDER BY created_at DESC");
    $paginas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// El menú calculará automáticamente $current_dir y $current_page
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Páginas Estáticas - Aramed Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/4u89qw1ptzfqell0ybjhqth1cc16ilb1y0792h3momw4lk8l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow);
        }

        .page-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-color);
        }

        .form-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .admin-content {
            background: transparent;
            padding: 2rem;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9 admin-content">
                <!-- Header -->
                <div class="admin-header">
                    <div class="container-fluid">
                        <h1><i class="bi bi-file-earmark-text me-3"></i>Páginas Estáticas</h1>
                        <p>Gestiona páginas estáticas personalizadas del sitio</p>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo esc($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo esc($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Content -->
                <?php if ($action === 'create' || $action === 'edit'): ?>
                    <!-- Form -->
                    <div class="form-card">
                        <h3 class="mb-4">
                            <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil-square'; ?> me-2"></i>
                            <?php echo $action === 'create' ? 'Crear Nueva Página' : 'Editar Página'; ?>
                        </h3>
                        
                        <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Título *</label>
                                        <input type="text" class="form-control" name="titulo" 
                                               value="<?php echo $pagina ? esc($pagina['titulo']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Slug (URL)</label>
                                        <input type="text" class="form-control" name="slug" 
                                               value="<?php echo $pagina ? esc($pagina['slug']) : ''; ?>"
                                               placeholder="auto-generado">
                                        <small class="text-muted">Se generará automáticamente si está vacío</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contenido</label>
                                <textarea id="contenido" name="contenido" class="form-control" rows="15">
                                    <?php echo $pagina ? htmlspecialchars($pagina['contenido']) : ''; ?>
                                </textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Título</label>
                                        <input type="text" class="form-control" name="meta_titulo" 
                                               value="<?php echo $pagina ? esc($pagina['meta_titulo']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-control" name="estado">
                                            <option value="borrador" <?php echo ($pagina && $pagina['estado'] === 'borrador') ? 'selected' : ''; ?>>Borrador</option>
                                            <option value="publicado" <?php echo ($pagina && $pagina['estado'] === 'publicado') ? 'selected' : ''; ?>>Publicado</option>
                                            <option value="archivado" <?php echo ($pagina && $pagina['estado'] === 'archivado') ? 'selected' : ''; ?>>Archivado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Meta Descripción</label>
                                <textarea class="form-control" name="meta_descripcion" rows="2"><?php echo $pagina ? esc($pagina['meta_descripcion']) : ''; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Plantilla</label>
                                        <select class="form-control" name="plantilla">
                                            <option value="default" <?php echo (!$pagina || $pagina['plantilla'] === 'default') ? 'selected' : ''; ?>>Default</option>
                                            <option value="full-width" <?php echo ($pagina && $pagina['plantilla'] === 'full-width') ? 'selected' : ''; ?>>Ancho Completo</option>
                                            <option value="sidebar" <?php echo ($pagina && $pagina['plantilla'] === 'sidebar') ? 'selected' : ''; ?>>Con Sidebar</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" class="form-check-input" name="mostrar_en_menu" id="mostrar_en_menu"
                                                   <?php echo ($pagina && $pagina['mostrar_en_menu']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="mostrar_en_menu">
                                                Mostrar en menú principal
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Etiqueta del Menú</label>
                                <input type="text" class="form-control" name="menu_label" 
                                       value="<?php echo $pagina ? esc($pagina['menu_label']) : ''; ?>"
                                       placeholder="Si está vacío, se usará el título">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <?php echo $action === 'create' ? 'Crear Página' : 'Guardar Cambios'; ?>
                                </button>
                                <a href="paginas.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                    
                <?php else: ?>
                    <!-- List -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Lista de Páginas</h3>
                        <a href="?action=create" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Página
                        </a>
                    </div>
                    
                    <?php if (empty($paginas)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No hay páginas estáticas creadas. <a href="?action=create">Crea la primera página</a>.
                        </div>
                    <?php else: ?>
                        <?php foreach ($paginas as $pag): ?>
                            <div class="page-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">
                                            <?php echo esc($pag['titulo']); ?>
                                            <span class="badge bg-<?php echo $pag['estado'] === 'publicado' ? 'success' : ($pag['estado'] === 'borrador' ? 'warning' : 'secondary'); ?> ms-2">
                                                <?php echo esc(ucfirst($pag['estado'])); ?>
                                            </span>
                                        </h5>
                                        <p class="text-muted mb-1">
                                            <strong>Slug:</strong> /<?php echo esc($pag['slug']); ?>
                                            <?php if ($pag['mostrar_en_menu']): ?>
                                                <span class="badge bg-info ms-2">En menú</span>
                                            <?php endif; ?>
                                        </p>
                                        <small class="text-muted">
                                            Creada: <?php echo date('d/m/Y H:i', strtotime($pag['created_at'])); ?>
                                            <?php if ($pag['updated_at'] !== $pag['created_at']): ?>
                                                | Actualizada: <?php echo date('d/m/Y H:i', strtotime($pag['updated_at'])); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="?action=edit&id=<?php echo $pag['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="/<?php echo esc($pag['slug']); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('¿Eliminar esta página?')) window.location='?action=delete&id=<?php echo $pag['id']; ?>'">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar TinyMCE
        tinymce.init({
            selector: '#contenido',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
    </script>
</body>
</html>

