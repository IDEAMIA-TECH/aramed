<?php
/**
 * ========================================
 * ADMIN - PLANTILLAS HTML NEWSLETTER
 * ========================================
 * 
 * CRUD de plantillas HTML para newsletter
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
    checkPermission('newsletter', 'editar');
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

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        try {
            $nombre = sanitizeInput($_POST['nombre'] ?? '');
            $asunto = sanitizeInput($_POST['asunto'] ?? '');
            $contenido_html = $_POST['contenido_html'] ?? '';
            $contenido_texto = $_POST['contenido_texto'] ?? '';
            $variables = $_POST['variables'] ?? '{}';
            $estado = $_POST['estado'] ?? 'borrador';
            $id = $_POST['id'] ?? null;
            
            if (empty($nombre) || empty($asunto) || empty($contenido_html)) {
                throw new Exception('Nombre, asunto y contenido HTML son obligatorios');
            }
            
            if ($action === 'create') {
                $sql = "INSERT INTO newsletter_templates (nombre, asunto, contenido_html, contenido_texto, variables, estado) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $asunto, $contenido_html, $contenido_texto, $variables, $estado]);
                $success_message = 'Plantilla creada exitosamente';
            } else {
                $sql = "UPDATE newsletter_templates SET 
                        nombre = ?, asunto = ?, contenido_html = ?, contenido_texto = ?, variables = ?, estado = ?, updated_at = NOW()
                        WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $asunto, $contenido_html, $contenido_texto, $variables, $estado, $id]);
                $success_message = 'Plantilla actualizada exitosamente';
            }
            
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], $action === 'create' ? 'crear' : 'editar', 'newsletter', $id ?? null, "Plantilla: $nombre");
            }
            
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    } elseif ($action === 'delete') {
        try {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM newsletter_templates WHERE id = ?");
            $stmt->execute([$id]);
            $success_message = 'Plantilla eliminada exitosamente';
            
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'newsletter', $id, 'Plantilla eliminada');
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}

// Obtener plantilla para editar
$editing = null;
if (isset($_GET['edit'])) {
    try {
        $id = (int)$_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM newsletter_templates WHERE id = ?");
        $stmt->execute([$id]);
        $editing = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener todas las plantillas
try {
    $stmt = $pdo->query("SELECT * FROM newsletter_templates ORDER BY created_at DESC");
    $plantillas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $plantillas = [];
}

$current_page = 'plantillas.php';
$current_dir = 'newsletter';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantillas HTML - Newsletter Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .template-preview {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            background: white;
            max-height: 300px;
            overflow-y: auto;
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
                                <i class="bi bi-file-earmark-code me-2"></i>Plantillas HTML
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona plantillas HTML para emails del newsletter</p>
                        </div>
                        <a href="../newsletter-simple.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
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
                
                <div class="row">
                    <!-- Formulario -->
                    <div class="col-md-5 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    <?php echo $editing ? 'Editar' : 'Nueva'; ?> Plantilla
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">
                                    <?php if ($editing): ?>
                                    <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de la Plantilla *</label>
                                        <input type="text" class="form-control" name="nombre" 
                                               value="<?php echo esc($editing['nombre'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Asunto del Email *</label>
                                        <input type="text" class="form-control" name="asunto" 
                                               value="<?php echo esc($editing['asunto'] ?? ''); ?>" required>
                                        <small class="form-text text-muted">Puedes usar variables como {{nombre_contacto}}</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Contenido HTML *</label>
                                        <textarea class="form-control" name="contenido_html" id="contenido_html" rows="10" required><?php echo esc($editing['contenido_html'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Contenido Texto Plano (opcional)</label>
                                        <textarea class="form-control" name="contenido_texto" rows="5"><?php echo esc($editing['contenido_texto'] ?? ''); ?></textarea>
                                        <small class="form-text text-muted">Versión texto para clientes que no soportan HTML</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Variables Disponibles (JSON)</label>
                                        <textarea class="form-control" name="variables" rows="3" 
                                                  placeholder='{"variable1": "Descripción", "variable2": "Descripción"}'><?php echo esc($editing['variables'] ?? '{}'); ?></textarea>
                                        <small class="form-text text-muted">Define las variables que se pueden usar en la plantilla</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" name="estado">
                                            <option value="borrador" <?php echo ($editing['estado'] ?? 'borrador') === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                            <option value="activo" <?php echo ($editing['estado'] ?? '') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                            <option value="inactivo" <?php echo ($editing['estado'] ?? '') === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $editing ? 'Actualizar' : 'Crear'; ?> Plantilla
                                    </button>
                                    
                                    <?php if ($editing): ?>
                                    <a href="plantillas.php" class="btn btn-secondary w-100 mt-2">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista -->
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>Plantillas (<?php echo count($plantillas); ?>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($plantillas)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                                    <p class="text-muted">No hay plantillas creadas</p>
                                </div>
                                <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($plantillas as $plantilla): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <?php echo esc($plantilla['nombre']); ?>
                                                    <span class="badge bg-<?php echo $plantilla['estado'] === 'activo' ? 'success' : ($plantilla['estado'] === 'inactivo' ? 'secondary' : 'warning'); ?> ms-2">
                                                        <?php echo ucfirst($plantilla['estado']); ?>
                                                    </span>
                                                </h6>
                                                <p class="mb-1 text-muted">
                                                    <small>Asunto: <?php echo esc($plantilla['asunto']); ?></small>
                                                </p>
                                                <div class="template-preview mt-2">
                                                    <?php echo substr(strip_tags($plantilla['contenido_html']), 0, 150); ?>...
                                                </div>
                                            </div>
                                            <div class="btn-group btn-group-sm ms-3">
                                                <a href="?edit=<?php echo $plantilla['id']; ?>" class="btn btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta plantilla?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $plantilla['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar TinyMCE
        tinymce.init({
            selector: '#contenido_html',
            height: 400,
            menubar: false,
            plugins: 'code preview',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | code preview',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
    </script>
</body>
</html>

