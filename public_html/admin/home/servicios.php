<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE SERVICIOS
 * ========================================
 * 
 * CRUD completo para servicios mostrados en el home
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
    checkPermission('home', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar acciones
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$success_message = '';
$error_message = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create' || $action === 'edit') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', $action === 'create' ? 'crear' : 'editar');
            }
            
            $icono = trim($_POST['icono'] ?? '');
            $titulo = trim($_POST['titulo'] ?? '');
            $resumen = trim($_POST['resumen'] ?? '');
            $texto_largo = $_POST['texto_largo'] ?? '';
            $cta_texto = trim($_POST['cta_texto'] ?? '');
            $cta_url = trim($_POST['cta_url'] ?? '');
            $estado = $_POST['estado'] ?? 'activo';
            $orden = (int)($_POST['orden'] ?? 0);
            
            if (empty($titulo)) {
                throw new Exception('El título es obligatorio');
            }
            
            if ($action === 'create') {
                $stmt = $pdo->prepare("
                    INSERT INTO home_servicios 
                    (icono, titulo, resumen, texto_largo, cta_texto, cta_url, orden, estado, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$icono, $titulo, $resumen, $texto_largo, $cta_texto, $cta_url, $orden, $estado]);
                
                $servicio_id = $pdo->lastInsertId();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'crear', 'home', $servicio_id, 'servicio', [
                        'titulo' => $titulo
                    ]);
                }
                
                $success_message = 'Servicio creado exitosamente';
                $action = 'list';
                
            } else { // edit
                $stmt = $pdo->prepare("
                    UPDATE home_servicios 
                    SET icono = ?, titulo = ?, resumen = ?, texto_largo = ?, cta_texto = ?, cta_url = ?, orden = ?, estado = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$icono, $titulo, $resumen, $texto_largo, $cta_texto, $cta_url, $orden, $estado, $id]);
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'home', $id, 'servicio', [
                        'titulo' => $titulo
                    ]);
                }
                
                $success_message = 'Servicio actualizado exitosamente';
                $action = 'list';
            }
            
        } elseif ($action === 'delete' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'eliminar');
            }
            
            // Obtener título antes de eliminar
            $stmt = $pdo->prepare("SELECT titulo FROM home_servicios WHERE id = ?");
            $stmt->execute([$id]);
            $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->prepare("DELETE FROM home_servicios WHERE id = ?");
            $stmt->execute([$id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'home', $id, 'servicio', [
                    'titulo' => $servicio['titulo'] ?? ''
                ]);
            }
            
            $success_message = 'Servicio eliminado exitosamente';
            $action = 'list';
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener datos para formularios
$servicio = null;
if (($action === 'edit' || $action === 'delete') && $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM home_servicios WHERE id = ?");
        $stmt->execute([$id]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$servicio) {
            $error_message = 'Servicio no encontrado';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Obtener lista de servicios
$servicios = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT * FROM home_servicios 
            ORDER BY orden ASC, created_at DESC
        ");
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Iconos Bootstrap disponibles (ejemplos comunes)
$iconos_disponibles = [
    'bi-gear', 'bi-tools', 'bi-wrench', 'bi-hammer', 'bi-screwdriver',
    'bi-clipboard-check', 'bi-check-circle', 'bi-star', 'bi-award',
    'bi-shield-check', 'bi-lightning', 'bi-bolt', 'bi-fire',
    'bi-heart', 'bi-heart-pulse', 'bi-hospital', 'bi-activity',
    'bi-graph-up', 'bi-trending-up', 'bi-chart-line', 'bi-bar-chart',
    'bi-people', 'bi-person-check', 'bi-person-workspace', 'bi-users',
    'bi-book', 'bi-journal-text', 'bi-file-earmark-text', 'bi-mortarboard',
    'bi-camera', 'bi-camera-video', 'bi-image', 'bi-images',
    'bi-phone', 'bi-envelope', 'bi-chat', 'bi-headset',
    'bi-truck', 'bi-box-seam', 'bi-cart', 'bi-bag'
];

$current_page = 'servicios.php';
$current_dir = 'home';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/4u89qw1ptzfqell0ybjhqth1cc16ilb1y0792h3momw4lk8l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <style>
        :root {
            --primary-color: #0066cc;
            --dark-color: #212529;
            --border-radius: 12px;
            --shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
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
        
        .service-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .service-icon {
            font-size: 3rem;
            color: #667eea;
        }
        
        .icon-selector {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .icon-option {
            padding: 0.5rem;
            text-align: center;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .icon-option:hover {
            background: white;
            border-color: #667eea;
        }
        
        .icon-option.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
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
                                <i class="bi bi-gear me-2"></i>Gestión de Servicios
                            </h2>
                            <p class="mb-0 opacity-75">Administra los servicios mostrados en el inicio</p>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <a href="?action=create" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
                        </a>
                        <?php else: ?>
                        <a href="?action=list" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Lista
                        </a>
                        <?php endif; ?>
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
                
                <!-- Contenido -->
                <?php if ($action === 'create' || $action === 'edit'): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil-square'; ?> me-2"></i>
                                <?php echo $action === 'create' ? 'Crear Nuevo Servicio' : 'Editar Servicio'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Título *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="titulo" 
                                                   value="<?php echo $servicio ? esc($servicio['titulo']) : ''; ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Orden</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="orden" 
                                                   value="<?php echo $servicio ? $servicio['orden'] : 0; ?>" 
                                                   min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ícono (Bootstrap Icons)</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="icono" 
                                           id="icono-input"
                                           value="<?php echo $servicio ? esc($servicio['icono']) : ''; ?>" 
                                           placeholder="bi-gear">
                                    <small class="form-text text-muted">Escribe el nombre del icono o selecciona uno de abajo</small>
                                    
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#icon-selector">
                                            <i class="bi bi-grid me-1"></i>Seleccionar Ícono
                                        </button>
                                    </div>
                                    
                                    <div class="collapse mt-2" id="icon-selector">
                                        <div class="icon-selector">
                                            <?php foreach ($iconos_disponibles as $icono): ?>
                                            <div class="icon-option <?php echo ($servicio && $servicio['icono'] === $icono) ? 'selected' : ''; ?>" 
                                                 data-icon="<?php echo $icono; ?>"
                                                 onclick="selectIcon('<?php echo $icono; ?>')">
                                                <i class="bi <?php echo $icono; ?>"></i>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($servicio && $servicio['icono']): ?>
                                    <div class="mt-2">
                                        <label class="form-label">Vista Previa:</label>
                                        <div>
                                            <i class="bi <?php echo esc($servicio['icono']); ?> service-icon" id="icon-preview"></i>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Resumen</label>
                                    <textarea class="form-control" 
                                              name="resumen" 
                                              rows="3"
                                              placeholder="Resumen corto del servicio (aparece en tarjetas)"><?php echo $servicio ? esc($servicio['resumen']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Texto Largo</label>
                                    <textarea class="form-control" 
                                              name="texto_largo" 
                                              id="texto_largo"
                                              rows="10"><?php echo $servicio ? $servicio['texto_largo'] : ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">CTA Texto</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="cta_texto" 
                                                   value="<?php echo $servicio ? esc($servicio['cta_texto']) : ''; ?>" 
                                                   placeholder="Ej: Ver más">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">CTA URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   name="cta_url" 
                                                   value="<?php echo $servicio ? esc($servicio['cta_url']) : ''; ?>" 
                                                   placeholder="https://ejemplo.com">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado" required>
                                        <option value="activo" <?php echo ($servicio && $servicio['estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo ($servicio && $servicio['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $action === 'create' ? 'Crear Servicio' : 'Actualizar Servicio'; ?>
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'delete' && $servicio): ?>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Servicio
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>¿Estás seguro de que deseas eliminar el servicio <strong><?php echo esc($servicio['titulo']); ?></strong>?</p>
                            
                            <form method="POST" action="?action=delete&id=<?php echo $id; ?>">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Sí, Eliminar
                                </button>
                                <a href="?action=list" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Lista de servicios -->
                    <?php if (empty($servicios)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-gear text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay servicios registrados</h4>
                            <p class="text-muted">Comienza creando el primer servicio</p>
                            <a href="?action=create" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Crear Primer Servicio
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($servicios as $s): ?>
                        <div class="col-md-6 mb-3">
                            <div class="service-card">
                                <div class="d-flex align-items-start">
                                    <?php if ($s['icono']): ?>
                                    <div class="me-3">
                                        <i class="bi <?php echo esc($s['icono']); ?> service-icon"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2"><?php echo esc($s['titulo']); ?></h5>
                                        <?php if ($s['resumen']): ?>
                                        <p class="text-muted mb-2 small"><?php echo esc($s['resumen']); ?></p>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge <?php echo $s['estado'] === 'activo' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($s['estado']); ?>
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-sort-numeric-down me-1"></i>
                                                Orden: <?php echo $s['orden']; ?>
                                            </small>
                                            <?php if ($s['cta_texto']): ?>
                                            <small class="text-info">
                                                <i class="bi bi-link-45deg me-1"></i>
                                                CTA: <?php echo esc($s['cta_texto']); ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="?action=edit&id=<?php echo $s['id']; ?>">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="?action=delete&id=<?php echo $s['id']; ?>">
                                                    <i class="bi bi-trash me-2"></i>Eliminar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TinyMCE
        tinymce.init({
            selector: '#texto_largo',
            height: 300,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
            language: 'es',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
        
        // Seleccionar icono
        function selectIcon(icono) {
            document.getElementById('icono-input').value = icono;
            
            // Actualizar vista previa
            const preview = document.getElementById('icon-preview');
            if (preview) {
                preview.className = 'bi ' + icono + ' service-icon';
            } else {
                // Crear vista previa si no existe
                const previewContainer = document.querySelector('.mb-3:has(#icono-input)');
                if (previewContainer) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'mt-2';
                    previewDiv.innerHTML = '<label class="form-label">Vista Previa:</label><div><i class="bi ' + icono + ' service-icon" id="icon-preview"></i></div>';
                    previewContainer.appendChild(previewDiv);
                }
            }
            
            // Actualizar selección visual
            document.querySelectorAll('.icon-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            document.querySelector(`[data-icon="${icono}"]`).classList.add('selected');
        }
        
        // Actualizar vista previa cuando se escribe manualmente
        document.getElementById('icono-input')?.addEventListener('input', function() {
            const icono = this.value.trim();
            const preview = document.getElementById('icon-preview');
            if (preview && icono) {
                preview.className = 'bi ' + icono + ' service-icon';
            }
        });
    </script>
</body>
</html>

