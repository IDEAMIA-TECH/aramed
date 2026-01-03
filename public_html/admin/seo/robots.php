<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE ROBOTS.TXT
 * ========================================
 * 
 * Editor de robots.txt desde el admin
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
    checkPermission('seo', 'editar');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$success_message = '';
$error_message = '';

// Ruta del archivo robots.txt
$robots_file = __DIR__ . '/../../robots.txt';

// Leer contenido actual
$robots_content = '';
if (file_exists($robots_file)) {
    $robots_content = file_get_contents($robots_file);
}

// Procesar guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    try {
        $content = $_POST['content'] ?? '';
        
        // Validación básica
        if (empty($content)) {
            throw new Exception('El contenido no puede estar vacío');
        }
        
        // Guardar archivo
        if (file_put_contents($robots_file, $content) === false) {
            throw new Exception('Error al guardar el archivo robots.txt');
        }
        
        $success_message = 'robots.txt actualizado exitosamente';
        
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'seo', null, 'robots.txt actualizado');
        }
        
        // Recargar contenido
        $robots_content = $content;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'robots.php';
$current_dir = 'seo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robots.txt - SEO Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- CodeMirror -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/shell/shell.min.js"></script>
    
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
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
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
        
        .CodeMirror {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            height: 500px;
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
                                <i class="bi bi-shield-check me-2"></i>Editor de robots.txt
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona el archivo robots.txt desde el admin</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
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
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>Contenido de robots.txt
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="save">
                            
                            <div class="mb-3">
                                <label class="form-label">Editar robots.txt</label>
                                <textarea id="robots-content" name="content" class="form-control" rows="20"><?php echo esc($robots_content); ?></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Nota:</strong> El archivo robots.txt controla qué páginas pueden ser rastreadas por los motores de búsqueda.
                                <br>
                                <small>Ubicación del archivo: <code><?php echo str_replace(__DIR__ . '/../../', '', $robots_file); ?></code></small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Guardar robots.txt
                                </button>
                                <a href="<?php echo SITE_URL; ?>/robots.txt" target="_blank" class="btn btn-outline-secondary">
                                    <i class="bi bi-eye me-2"></i>Ver robots.txt actual
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar CodeMirror
        const editor = CodeMirror.fromTextArea(document.getElementById('robots-content'), {
            lineNumbers: true,
            mode: 'shell',
            theme: 'default',
            lineWrapping: true,
            indentUnit: 2
        });
    </script>
</body>
</html>

