<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DEL MENÚ PRINCIPAL
 * ========================================
 * 
 * Permite gestionar la visibilidad y orden de los elementos del menú principal
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
    // Intentar con permiso específico de menu, fallback a apariencia
    if (function_exists('hasPermission') && isset($_SESSION['admin_user_id'])) {
        $has_menu_permission = hasPermission($_SESSION['admin_user_id'], 'menu', 'editar');
        if (!$has_menu_permission) {
            checkPermission('apariencia', 'editar');
        }
    } else {
        checkPermission('apariencia', 'editar');
    }
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
    try {
        if (function_exists('checkPermission')) {
            checkPermission('apariencia', 'editar');
        }
        
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_visibility') {
            // Actualizar visibilidad de elementos
            $items = $_POST['items'] ?? [];
            
            $pdo->beginTransaction();
            
            foreach ($items as $item_id => $data) {
                $visible = isset($data['visible']) ? 1 : 0;
                $orden = isset($data['orden']) ? (int)$data['orden'] : 0;
                
                $stmt = $pdo->prepare("UPDATE menu_config SET visible = ?, orden = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$visible, $orden, $item_id]);
            }
            
            $pdo->commit();
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'editar', 'apariencia', null, 'menu', [
                    'accion' => 'actualizar_visibilidad_menu'
                ]);
            }
            
            $success_message = 'Configuración del menú actualizada correctamente';
            
        } elseif ($action === 'add_item') {
            // Agregar nuevo elemento
            $item_key = trim($_POST['item_key'] ?? '');
            $label = trim($_POST['label'] ?? '');
            $href = trim($_POST['href'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $section = trim($_POST['section'] ?? '');
            $orden = (int)($_POST['orden'] ?? 0);
            $visible = isset($_POST['visible']) ? 1 : 0;
            
            if (empty($item_key) || empty($label)) {
                throw new Exception('La clave y la etiqueta son obligatorias');
            }
            
            // Verificar si ya existe
            $stmt = $pdo->prepare("SELECT id FROM menu_config WHERE item_key = ?");
            $stmt->execute([$item_key]);
            if ($stmt->fetch()) {
                throw new Exception('Ya existe un elemento con esa clave');
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO menu_config (item_key, label, href, icon, section, orden, visible, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$item_key, $label, $href, $icon, $section, $orden, $visible]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'crear', 'apariencia', null, 'menu', [
                    'accion' => 'agregar_elemento_menu',
                    'item_key' => $item_key
                ]);
            }
            
            $success_message = 'Elemento agregado correctamente';
            
        } elseif ($action === 'delete_item') {
            // Eliminar elemento
            $item_id = (int)($_POST['item_id'] ?? 0);
            
            if ($item_id <= 0) {
                throw new Exception('ID inválido');
            }
            
            $stmt = $pdo->prepare("DELETE FROM menu_config WHERE id = ?");
            $stmt->execute([$item_id]);
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'apariencia', null, 'menu', [
                    'accion' => 'eliminar_elemento_menu',
                    'item_id' => $item_id
                ]);
            }
            
            $success_message = 'Elemento eliminado correctamente';
        }
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error_message = $e->getMessage();
    }
}

// Obtener elementos del menú
$stmt = $pdo->query("SELECT * FROM menu_config ORDER BY orden ASC, id ASC");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'index.php';
$current_dir = 'menu';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión del Menú - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: var(--border-radius);
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: var(--shadow);
        }
        
        .page-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem;
        }
        
        .menu-item-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .menu-item-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .menu-item-card.inactive {
            opacity: 0.6;
            background: #f8f9fa;
        }
        
        .drag-handle {
            cursor: move;
            color: #6c757d;
            font-size: 1.2rem;
        }
        
        .drag-handle:hover {
            color: var(--primary-color);
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
                                <i class="bi bi-list-ul me-2"></i>Gestión del Menú Principal
                            </h2>
                            <p class="mb-0 opacity-75">Configura la visibilidad y orden de los elementos del menú</p>
                        </div>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Elemento
                        </button>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Formulario de configuración -->
                <form method="POST" action="" id="menuForm">
                    <input type="hidden" name="action" value="update_visibility">
                    
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2"></i>Elementos del Menú
                            </h5>
                            <small class="text-muted">Arrastra para reordenar, marca/desmarca para mostrar/ocultar</small>
                        </div>
                        <div class="card-body" id="menuItemsList">
                            <?php if (empty($menu_items)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-3">No hay elementos configurados</p>
                            </div>
                            <?php else: ?>
                            <?php foreach ($menu_items as $item): ?>
                            <div class="menu-item-card <?php echo $item['visible'] ? '' : 'inactive'; ?>" data-id="<?php echo $item['id']; ?>">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="bi bi-grip-vertical drag-handle"></i>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($item['icon'])): ?>
                                            <i class="bi bi-<?php echo esc($item['icon']); ?> fs-4 text-primary"></i>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0"><?php echo esc($item['label']); ?></h6>
                                                <small class="text-muted">
                                                    <code><?php echo esc($item['item_key']); ?></code> | 
                                                    <?php echo esc($item['href']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="input-group" style="width: 100px;">
                                            <span class="input-group-text">Orden</span>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="items[<?php echo $item['id']; ?>][orden]" 
                                                   value="<?php echo $item['orden']; ?>"
                                                   min="0">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="items[<?php echo $item['id']; ?>][visible]" 
                                                   id="visible_<?php echo $item['id']; ?>"
                                                   value="1"
                                                   <?php echo $item['visible'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="visible_<?php echo $item['id']; ?>">
                                                Visible
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-item" 
                                                data-id="<?php echo $item['id']; ?>"
                                                data-label="<?php echo esc($item['label']); ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="<?php echo siteUrl(); ?>" target="_blank" class="btn btn-outline-secondary">
                                <i class="bi bi-eye me-2"></i>Ver Sitio
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para agregar elemento -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Agregar Elemento al Menú
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_item">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="item_key" class="form-label">Clave única <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="item_key" 
                                   name="item_key" 
                                   required
                                   placeholder="ej: contacto">
                            <small class="form-text text-muted">Sin espacios ni caracteres especiales</small>
                        </div>
                        <div class="mb-3">
                            <label for="label" class="form-label">Etiqueta <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="label" 
                                   name="label" 
                                   required
                                   placeholder="ej: Contacto">
                        </div>
                        <div class="mb-3">
                            <label for="href" class="form-label">URL</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="href" 
                                   name="href"
                                   placeholder="/contacto.php o #contacto">
                        </div>
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icono (Bootstrap Icons)</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="icon" 
                                   name="icon"
                                   placeholder="ej: envelope (sin bi-)">
                            <small class="form-text text-muted">Ver iconos en: <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></small>
                        </div>
                        <div class="mb-3">
                            <label for="section" class="form-label">Sección</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="section" 
                                   name="section"
                                   placeholder="ej: contacto">
                            <small class="form-text text-muted">Para identificar la página activa</small>
                        </div>
                        <div class="mb-3">
                            <label for="orden" class="form-label">Orden</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="orden" 
                                   name="orden" 
                                   value="0"
                                   min="0">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="visible" 
                                   name="visible" 
                                   value="1"
                                   checked>
                            <label class="form-check-label" for="visible">
                                Visible por defecto
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Formulario oculto para eliminar -->
    <form method="POST" action="" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete_item">
        <input type="hidden" name="item_id" id="delete_item_id">
    </form>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Hacer la lista ordenable
        const list = document.getElementById('menuItemsList');
        if (list) {
            new Sortable(list, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function(evt) {
                    // Actualizar ordenes automáticamente
                    const items = list.querySelectorAll('.menu-item-card');
                    items.forEach((item, index) => {
                        const input = item.querySelector('input[type="number"]');
                        if (input) {
                            input.value = index + 1;
                        }
                    });
                }
            });
        }
        
        // Eliminar elemento
        document.querySelectorAll('.delete-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const label = this.dataset.label;
                
                if (confirm(`¿Estás seguro de eliminar el elemento "${label}"?`)) {
                    document.getElementById('delete_item_id').value = id;
                    document.getElementById('deleteForm').submit();
                }
            });
        });
    </script>
</body>
</html>

