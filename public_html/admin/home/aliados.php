<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE ALIADOS / PARTNERS GLOBALES
 * ========================================
 * 
 * CRUD completo para aliados estratégicos mostrados en el home
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

// Procesar acciones GET (toggle_status)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'toggle_status' && $id) {
    try {
        // Verificar permisos
        if (function_exists('checkPermission')) {
            checkPermission('home', 'editar');
        }
        
        // Obtener el aliado actual
        $stmt = $pdo->prepare("SELECT estado FROM home_aliados WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            throw new Exception('Aliado no encontrado');
        }
        
        // Cambiar estado
        $new_status = $current['estado'] === 'activo' ? 'inactivo' : 'activo';
        
        $stmt = $pdo->prepare("UPDATE home_aliados SET estado = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'home', $id, 'aliado', [
                'accion' => 'cambiar_estado',
                'estado_anterior' => $current['estado'],
                'estado_nuevo' => $new_status
            ]);
        }
        
        $success_message = 'Estado del aliado actualizado exitosamente';
        $action = 'list';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create' || $action === 'edit') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', $action === 'create' ? 'crear' : 'editar');
            }
            
            $nombre = trim($_POST['nombre'] ?? '');
            $logo_url = trim($_POST['logo_url'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $url_website = trim($_POST['url_website'] ?? '');
            $mostrar_en_carrusel = isset($_POST['mostrar_en_carrusel']) ? 1 : 0;
            $mostrar_en_detalle = isset($_POST['mostrar_en_detalle']) ? 1 : 0;
            $estado = $_POST['estado'] ?? 'activo';
            $orden = (int)($_POST['orden'] ?? 0);
            
            if (empty($nombre)) {
                throw new Exception('El nombre es obligatorio');
            }
            
            // Procesar imagen si se subió
            if (isset($_FILES['logo_imagen']) && $_FILES['logo_imagen']['error'] === UPLOAD_ERR_OK) {
                // Ruta del directorio de upload usando constante ASSETS_PATH o fallback
                if (defined('ASSETS_PATH')) {
                    $upload_dir = ASSETS_PATH . '/images/aliados/';
                } else {
                    // Fallback: calcular desde __DIR__
                    $upload_dir = __DIR__ . '/../../assets/images/aliados/';
                }
                
                // Crear directorio si no existe
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        throw new Exception('No se pudo crear el directorio de imágenes. Verifica los permisos.');
                    }
                }
                
                // Verificar que el directorio sea escribible
                if (!is_writable($upload_dir)) {
                    throw new Exception('El directorio de imágenes no tiene permisos de escritura. Contacta al administrador.');
                }
                
                $file = $_FILES['logo_imagen'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                // Validar tipo
                if (!in_array($file['type'], $allowed_types)) {
                    throw new Exception('Tipo de archivo no permitido. Solo se permiten imágenes (JPG, PNG, GIF, WebP)');
                }
                
                // Validar tamaño
                if ($file['size'] > $max_size) {
                    throw new Exception('El archivo es demasiado grande. Máximo 5MB');
                }
                
                // Generar nombre único
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = 'aliado-' . time() . '-' . uniqid() . '.' . $extension;
                $filepath = $upload_dir . $filename;
                
                // Mover archivo
                if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                    $upload_error = $_FILES['logo_imagen']['error'];
                    $error_messages = [
                        UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP (upload_max_filesize)',
                        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
                        UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                        UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
                        UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
                        UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
                        UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo'
                    ];
                    
                    $error_msg = 'Error al subir el archivo';
                    if (isset($error_messages[$upload_error])) {
                        $error_msg .= ': ' . $error_messages[$upload_error];
                    } else {
                        $error_msg .= ' (Error código: ' . $upload_error . ')';
                    }
                    
                    // Log adicional para debugging
                    error_log("Error al subir logo de aliado: " . $error_msg);
                    error_log("Upload dir: " . $upload_dir);
                    error_log("Filepath: " . $filepath);
                    error_log("Is writable: " . (is_writable($upload_dir) ? 'YES' : 'NO'));
                    
                    throw new Exception($error_msg);
                }
                
                // Verificar que el archivo se haya guardado correctamente
                if (!file_exists($filepath)) {
                    error_log("Archivo no existe después de move_uploaded_file: " . $filepath);
                    throw new Exception('El archivo no se guardó correctamente en el servidor. Verifica los permisos del directorio.');
                }
                
                // Log de éxito
                error_log("Logo de aliado subido exitosamente: " . $filepath);
                
                // Si hay una imagen anterior, eliminarla
                if ($action === 'edit' && $id) {
                    $stmt = $pdo->prepare("SELECT logo_url FROM home_aliados WHERE id = ?");
                    $stmt->execute([$id]);
                    $aliado_actual = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($aliado_actual && $aliado_actual['logo_url']) {
                        // Solo eliminar si es una imagen subida (no una URL externa)
                        $old_logo = $aliado_actual['logo_url'];
                        // Puede ser 'aliados/filename.ext' o 'assets/images/aliados/filename.ext'
                        if (strpos($old_logo, 'aliados/') !== false || strpos($old_logo, 'assets/images/aliados/') !== false) {
                        // Normalizar la ruta usando ASSETS_PATH o fallback
                        $assets_path = defined('ASSETS_PATH') ? ASSETS_PATH : (__DIR__ . '/../../assets');
                        if (strpos($old_logo, 'assets/images/') === 0) {
                            $old_image_path = $assets_path . '/' . str_replace('assets/images/', 'images/', $old_logo);
                        } else {
                            $old_image_path = $assets_path . '/images/' . $old_logo;
                        }
                            if (file_exists($old_image_path)) {
                                @unlink($old_image_path);
                            }
                        }
                    }
                }
                
                // Guardar ruta relativa a assets/images/ (sin el prefijo assets/images/)
                $logo_url = 'aliados/' . $filename;
            } elseif ($action === 'edit' && $id && empty($logo_url)) {
                // Mantener la imagen existente si no se subió una nueva y no se especificó URL manual
                $stmt = $pdo->prepare("SELECT logo_url FROM home_aliados WHERE id = ?");
                $stmt->execute([$id]);
                $aliado_actual = $stmt->fetch(PDO::FETCH_ASSOC);
                $logo_url = $aliado_actual['logo_url'] ?? null;
            } elseif (empty($logo_url)) {
                // Si no hay imagen subida ni URL manual, dejar null
                $logo_url = null;
            }
            
            if ($action === 'create') {
                // Calcular orden automáticamente si es 0 o no se especificó
                if ($orden <= 0) {
                    $stmt = $pdo->query("SELECT COALESCE(MAX(orden), 0) as max_orden FROM home_aliados");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $orden = ($result['max_orden'] ?? 0) + 1;
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO home_aliados 
                    (nombre, logo_url, descripcion, url_website, mostrar_en_carrusel, mostrar_en_detalle, orden, estado, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$nombre, $logo_url ?: null, $descripcion ?: null, $url_website ?: null, $mostrar_en_carrusel, $mostrar_en_detalle, $orden, $estado]);
                
                $aliado_id = $pdo->lastInsertId();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'crear', 'home', $aliado_id, 'aliado', [
                        'nombre' => $nombre
                    ]);
                }
                
                $success_message = 'Aliado creado exitosamente';
                $action = 'list';
                
            } else { // edit
                $stmt = $pdo->prepare("
                    UPDATE home_aliados 
                    SET nombre = ?, logo_url = ?, descripcion = ?, url_website = ?, 
                        mostrar_en_carrusel = ?, mostrar_en_detalle = ?, orden = ?, estado = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$nombre, $logo_url ?: null, $descripcion ?: null, $url_website ?: null, $mostrar_en_carrusel, $mostrar_en_detalle, $orden, $estado, $id]);
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'home', $id, 'aliado', [
                        'nombre' => $nombre
                    ]);
                }
                
                $success_message = 'Aliado actualizado exitosamente';
                $action = 'list';
            }
            
        } elseif ($action === 'delete' && $id) {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'eliminar');
            }
            
            // Obtener datos antes de eliminar
            $stmt = $pdo->prepare("SELECT nombre, logo_url FROM home_aliados WHERE id = ?");
            $stmt->execute([$id]);
            $aliado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Eliminar imagen si existe
            if ($aliado && $aliado['logo_url']) {
                // Solo eliminar si es una imagen subida (no una URL externa)
                $old_logo = $aliado['logo_url'];
                if (strpos($old_logo, 'aliados/') !== false || strpos($old_logo, 'assets/images/aliados/') !== false) {
                    // Normalizar la ruta usando ASSETS_PATH o fallback
                    $assets_path = defined('ASSETS_PATH') ? ASSETS_PATH : (__DIR__ . '/../../assets');
                    if (strpos($old_logo, 'assets/images/') === 0) {
                        $image_path = $assets_path . '/' . str_replace('assets/images/', 'images/', $old_logo);
                    } else {
                        $image_path = $assets_path . '/images/' . $old_logo;
                    }
                    if (file_exists($image_path)) {
                        @unlink($image_path);
                    }
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM home_aliados WHERE id = ?");
            $stmt->execute([$id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'home', $id, 'aliado', [
                    'nombre' => $aliado['nombre'] ?? ''
                ]);
            }
            
            $success_message = 'Aliado eliminado exitosamente';
            $action = 'list';
            
        } elseif ($action === 'update_order') {
            // Verificar permisos
            if (function_exists('checkPermission')) {
                checkPermission('home', 'editar');
            }
            
            header('Content-Type: application/json');
            
            $updates = json_decode($_POST['updates'] ?? '[]', true);
            
            if (empty($updates)) {
                echo json_encode(['success' => false, 'error' => 'No se recibieron actualizaciones']);
                exit;
            }
            
            try {
                $pdo->beginTransaction();
                
                foreach ($updates as $update) {
                    $stmt = $pdo->prepare("UPDATE home_aliados SET orden = ? WHERE id = ?");
                    $stmt->execute([$update['orden'], $update['id']]);
                }
                
                $pdo->commit();
                
                // Registrar actividad
                if (function_exists('logActivity')) {
                    logActivity($current_user['id'], 'editar', 'home', null, 'aliados', [
                        'accion' => 'reordenar',
                        'aliados_afectados' => count($updates)
                    ]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Orden actualizado exitosamente']);
                exit;
                
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener datos para formularios
$aliado = null;
if (($action === 'edit' || $action === 'delete' || $action === 'toggle_status') && $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM home_aliados WHERE id = ?");
        $stmt->execute([$id]);
        $aliado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$aliado) {
            $error_message = 'Aliado no encontrado';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Obtener lista de aliados
$aliados = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT * FROM home_aliados 
            ORDER BY orden ASC, created_at DESC
        ");
        $aliados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'aliados.php';
$current_dir = 'home';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partners Globales - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
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
        
        .aliado-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: move;
        }
        
        .aliado-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .aliado-card.sortable-ghost {
            opacity: 0.4;
        }
        
        .aliado-logo-preview {
            width: 100px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 0.5rem;
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-building-fill-check me-2"></i>Partners Globales
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona los aliados estratégicos mostrados en el inicio</p>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <a href="?action=create" class="btn btn-light">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Aliado
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
                <?php if ($action === 'create'): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-plus-circle me-2"></i>Agregar Aliado
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=create" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Aliado *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="nombre" 
                                           placeholder="Ej: GAUMARD, ANATOMAGE"
                                           required>
                                    <small class="form-text text-muted">Nombre del aliado estratégico</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Logo</label>
                                    <div class="mb-2">
                                        <input type="file" 
                                               class="form-control" 
                                               name="logo_imagen" 
                                               id="logo_imagen"
                                               accept="image/jpeg,image/png,image/gif,image/webp"
                                               onchange="previewLogo(this)">
                                        <small class="form-text text-muted">Sube una imagen del logo (JPG, PNG, GIF, WebP - Máx. 5MB)</small>
                                    </div>
                                    <div id="logo_preview_container" class="mt-2" style="display: none;">
                                        <img id="logo_preview" src="" alt="Preview" style="max-width: 200px; max-height: 120px; object-fit: contain; border: 1px solid #dee2e6; border-radius: 8px; padding: 0.5rem; background: #f8f9fa;">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearLogoPreview()">
                                            <i class="bi bi-x-circle me-1"></i>Quitar imagen
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">O ingresa una URL manual:</small>
                                        <input type="text" 
                                               class="form-control mt-1" 
                                               name="logo_url" 
                                               id="logo_url"
                                               placeholder="aliados/1-Gaumard.webp">
                                        <small class="form-text text-muted">Ruta relativa a assets/images/ (si no subes imagen)</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" 
                                              name="descripcion" 
                                              rows="4" 
                                              placeholder="Descripción del aliado para la sección detallada"></textarea>
                                    <small class="form-text text-muted">Descripción que aparecerá en el carrusel detallado</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">URL del Sitio Web</label>
                                    <input type="url" 
                                           class="form-control" 
                                           name="url_website" 
                                           placeholder="https://www.ejemplo.com">
                                    <small class="form-text text-muted">URL del sitio web del aliado (opcional)</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Orden</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="orden" 
                                               value="0" 
                                               min="0"
                                               placeholder="0 (automático)">
                                        <small class="form-text text-muted">Se calculará automáticamente al final de la lista. Puedes ajustarlo después arrastrando los aliados.</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" name="estado" required>
                                            <option value="activo" selected>Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="mostrar_en_carrusel" 
                                                   id="mostrar_en_carrusel" 
                                                   value="1" 
                                                   checked>
                                            <label class="form-check-label" for="mostrar_en_carrusel">
                                                Mostrar en Carrusel Simple
                                            </label>
                                            <small class="form-text text-muted d-block">Aparece en el carrusel de logos</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="mostrar_en_detalle" 
                                                   id="mostrar_en_detalle" 
                                                   value="1" 
                                                   checked>
                                            <label class="form-check-label" for="mostrar_en_detalle">
                                                Mostrar en Carrusel Detallado
                                            </label>
                                            <small class="form-text text-muted d-block">Aparece en el carrusel con descripción</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Agregar Aliado
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'edit' && $aliado): ?>
                    
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-pencil-square me-2"></i>Editar Aliado
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="?action=edit&id=<?php echo $id; ?>" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Aliado *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="nombre" 
                                           value="<?php echo esc($aliado['nombre'] ?? ''); ?>" 
                                           placeholder="Ej: GAUMARD, ANATOMAGE"
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Logo</label>
                                    <?php if ($aliado['logo_url']): ?>
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-2">Logo actual:</small>
                                        <img src="<?php echo imageUrl($aliado['logo_url']); ?>" 
                                             alt="Logo actual" 
                                             id="current_logo_preview"
                                             class="aliado-logo-preview" 
                                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'60\'%3E%3Crect fill=\'%23f8f9fa\' width=\'100\' height=\'60\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ESin logo%3C/text%3E%3C/svg%3E';">
                                    </div>
                                    <?php endif; ?>
                                    <div class="mb-2">
                                        <input type="file" 
                                               class="form-control" 
                                               name="logo_imagen" 
                                               id="logo_imagen"
                                               accept="image/jpeg,image/png,image/gif,image/webp"
                                               onchange="previewLogo(this)">
                                        <small class="form-text text-muted">Sube una nueva imagen para reemplazar el logo actual (JPG, PNG, GIF, WebP - Máx. 5MB)</small>
                                    </div>
                                    <div id="logo_preview_container" class="mt-2" style="display: none;">
                                        <small class="text-muted d-block mb-2">Nueva imagen:</small>
                                        <img id="logo_preview" src="" alt="Preview" style="max-width: 200px; max-height: 120px; object-fit: contain; border: 1px solid #dee2e6; border-radius: 8px; padding: 0.5rem; background: #f8f9fa;">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearLogoPreview()">
                                            <i class="bi bi-x-circle me-1"></i>Quitar nueva imagen
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">O ingresa una URL manual:</small>
                                        <input type="text" 
                                               class="form-control mt-1" 
                                               name="logo_url" 
                                               id="logo_url"
                                               value="<?php echo esc($aliado['logo_url'] ?? ''); ?>" 
                                               placeholder="aliados/1-Gaumard.webp">
                                        <small class="form-text text-muted">Ruta relativa a assets/images/ (si no subes imagen nueva)</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" 
                                              name="descripcion" 
                                              rows="4" 
                                              placeholder="Descripción del aliado para la sección detallada"><?php echo esc($aliado['descripcion'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Descripción que aparecerá en el carrusel detallado</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">URL del Sitio Web</label>
                                    <input type="url" 
                                           class="form-control" 
                                           name="url_website" 
                                           value="<?php echo esc($aliado['url_website'] ?? ''); ?>" 
                                           placeholder="https://www.ejemplo.com">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Orden</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="orden" 
                                               value="<?php echo $aliado['orden']; ?>" 
                                               min="0">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" name="estado" required>
                                            <option value="activo" <?php echo $aliado['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                            <option value="inactivo" <?php echo $aliado['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="mostrar_en_carrusel" 
                                                   id="mostrar_en_carrusel" 
                                                   value="1" 
                                                   <?php echo $aliado['mostrar_en_carrusel'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="mostrar_en_carrusel">
                                                Mostrar en Carrusel Simple
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="mostrar_en_detalle" 
                                                   id="mostrar_en_detalle" 
                                                   value="1" 
                                                   <?php echo $aliado['mostrar_en_detalle'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="mostrar_en_detalle">
                                                Mostrar en Carrusel Detallado
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Actualizar
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'delete' && $aliado): ?>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Aliado
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>¿Estás seguro de que deseas eliminar el aliado <strong><?php echo esc($aliado['nombre']); ?></strong>?</p>
                            
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
                    <!-- Lista de aliados -->
                    <?php if (empty($aliados)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No hay aliados registrados</h4>
                            <p class="text-muted">Agrega aliados para mostrarlos en el inicio</p>
                            <a href="?action=create" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Agregar Primer Aliado
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Arrastra y suelta</strong> los aliados para reordenarlos. Los cambios se guardarán automáticamente.
                    </div>
                    
                    <div id="aliados-list">
                        <?php foreach ($aliados as $a): ?>
                        <div class="aliado-card" data-id="<?php echo $a['id']; ?>">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-grip-vertical text-muted" style="font-size: 1.5rem; cursor: grab;"></i>
                                </div>
                                
                                <?php if ($a['logo_url']): ?>
                                <img src="<?php echo imageUrl($a['logo_url']); ?>" 
                                     alt="<?php echo esc($a['nombre']); ?>" 
                                     class="aliado-logo-preview me-3"
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'60\'%3E%3Crect fill=\'%23f8f9fa\' width=\'100\' height=\'60\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ESin logo%3C/text%3E%3C/svg%3E'">
                                <?php else: ?>
                                <div class="aliado-logo-preview me-3 d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-image" style="font-size: 2rem;"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?php echo esc($a['nombre']); ?></h5>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <?php if ($a['mostrar_en_carrusel']): ?>
                                        <span class="badge bg-primary">
                                            <i class="bi bi-images me-1"></i>Carrusel
                                        </span>
                                        <?php endif; ?>
                                        <?php if ($a['mostrar_en_detalle']): ?>
                                        <span class="badge bg-info">
                                            <i class="bi bi-card-text me-1"></i>Detalle
                                        </span>
                                        <?php endif; ?>
                                        <span class="badge <?php echo $a['estado'] === 'activo' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($a['estado']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <i class="bi bi-sort-numeric-down me-1"></i>
                                            Orden: <?php echo $a['orden']; ?>
                                        </small>
                                    </div>
                                    <?php if ($a['descripcion']): ?>
                                    <p class="text-muted small mt-2 mb-0">
                                        <?php echo esc(substr($a['descripcion'], 0, 100)); ?><?php echo strlen($a['descripcion']) > 100 ? '...' : ''; ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="?action=edit&id=<?php echo $a['id']; ?>">
                                                <i class="bi bi-pencil me-2"></i>Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item <?php echo $a['estado'] === 'activo' ? 'text-warning' : 'text-success'; ?>" 
                                               href="?action=toggle_status&id=<?php echo $a['id']; ?>"
                                               onclick="return confirm('¿Estás seguro de <?php echo $a['estado'] === 'activo' ? 'inactivar' : 'activar'; ?> este aliado?');">
                                                <i class="bi <?php echo $a['estado'] === 'activo' ? 'bi-eye-slash' : 'bi-eye'; ?> me-2"></i>
                                                <?php echo $a['estado'] === 'activo' ? 'Inactivar' : 'Activar'; ?>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="?action=delete&id=<?php echo $a['id']; ?>">
                                                <i class="bi bi-trash me-2"></i>Eliminar
                                            </a>
                                        </li>
                                    </ul>
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
        // Sortable para reordenar aliados
        const aliadosList = document.getElementById('aliados-list');
        if (aliadosList) {
            const sortable = Sortable.create(aliadosList, {
                animation: 150,
                handle: '.bi-grip-vertical',
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Obtener nuevo orden
                    const items = aliadosList.querySelectorAll('.aliado-card');
                    const updates = [];
                    
                    items.forEach((item, index) => {
                        const id = item.dataset.id;
                        updates.push({
                            id: id,
                            orden: index + 1
                        });
                    });
                    
                    // Enviar actualización al servidor
                    fetch('aliados.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=update_order&updates=' + encodeURIComponent(JSON.stringify(updates))
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mostrar mensaje de éxito temporal
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = '<i class="bi bi-check-circle me-2"></i>Orden actualizado exitosamente';
                            document.querySelector('.admin-content').insertBefore(alert, document.querySelector('.page-header').nextSibling);
                            
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        }
                    });
                }
            });
        }
        
        // Preview de logo al seleccionar archivo
        function previewLogo(input) {
            const previewContainer = document.getElementById('logo_preview_container');
            const preview = document.getElementById('logo_preview');
            const logoUrlInput = document.getElementById('logo_url');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    // Limpiar el campo de URL manual si se sube una imagen
                    if (logoUrlInput) {
                        logoUrlInput.value = '';
                    }
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
        
        // Limpiar preview de logo
        function clearLogoPreview() {
            const input = document.getElementById('logo_imagen');
            const previewContainer = document.getElementById('logo_preview_container');
            const preview = document.getElementById('logo_preview');
            
            if (input) {
                input.value = '';
            }
            if (preview) {
                preview.src = '';
            }
            if (previewContainer) {
                previewContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>

