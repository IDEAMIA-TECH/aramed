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
            
            // Validar que variables sea un JSON válido
            if (!empty($variables) && $variables !== '{}') {
                $decoded = json_decode($variables, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('El campo Variables debe ser un JSON válido. Error: ' . json_last_error_msg());
                }
                // Re-encode para asegurar formato consistente
                $variables = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
    <script src="https://cdn.tiny.cloud/1/4u89qw1ptzfqell0ybjhqth1cc16ilb1y0792h3momw4lk8l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
                                        <label class="form-label">
                                            Contenido HTML *
                                            <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#productModal">
                                                <i class="bi bi-plus-circle me-1"></i>Insertar Producto
                                            </button>
                                        </label>
                                        <textarea class="form-control" name="contenido_html" id="contenido_html" rows="15"><?php echo esc($editing['contenido_html'] ?? ''); ?></textarea>
                                        <div class="invalid-feedback" id="contenido_html_error" style="display: none;">
                                            El contenido HTML es obligatorio
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Puedes insertar productos del catálogo usando el botón "Insertar Producto" arriba.
                                        </small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Contenido Texto Plano (opcional)</label>
                                        <textarea class="form-control" name="contenido_texto" rows="5"><?php echo esc($editing['contenido_texto'] ?? ''); ?></textarea>
                                        <small class="form-text text-muted">Versión texto para clientes que no soportan HTML</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Variables Disponibles (JSON)
                                            <button type="button" class="btn btn-sm btn-outline-info ms-2" data-bs-toggle="collapse" data-bs-target="#variablesHelp" aria-expanded="false">
                                                <i class="bi bi-question-circle me-1"></i>Ayuda
                                            </button>
                                        </label>
                                        
                                        <!-- Ayuda colapsable -->
                                        <div class="collapse mb-2" id="variablesHelp">
                                            <div class="card card-body bg-light">
                                                <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>¿Cómo usar las variables?</h6>
                                                <p class="mb-2">Define las variables que puedes usar en tu plantilla HTML usando la sintaxis <code>{{nombre_variable}}</code></p>
                                                <p class="mb-2"><strong>Ejemplo en HTML:</strong></p>
                                                <pre class="bg-white p-2 rounded"><code>&lt;h1&gt;Hola {{nombre_contacto}}&lt;/h1&gt;
&lt;p&gt;Bienvenido a {{nombre_institucion}}&lt;/p&gt;</code></pre>
                                                <p class="mb-0"><strong>Formato JSON:</strong> Cada variable debe tener un nombre y una descripción.</p>
                                            </div>
                                        </div>
                                        
                                        <?php
                                        // Variables por defecto si está vacío
                                        $default_variables_json = [
                                            "nombre_contacto" => "Nombre del contacto",
                                            "email_contacto" => "Email del contacto",
                                            "nombre_institucion" => "Nombre de la institución",
                                            "mensaje_personalizado" => "Mensaje personalizado",
                                            "asunto" => "Asunto del email",
                                            "link_desuscripcion" => "Link para desuscripción",
                                            "fecha_actual" => "Fecha actual",
                                            "logo_url" => "URL del logo"
                                        ];
                                        $default_variables = json_encode($default_variables_json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                                        
                                        $variables_value = $editing['variables'] ?? '';
                                        
                                        // Si está vacío o es null, usar valores por defecto
                                        if (empty($variables_value) || $variables_value === '{}' || $variables_value === 'null' || trim($variables_value) === '') {
                                            $variables_value = $default_variables;
                                        } else {
                                            // Intentar formatear el JSON si es válido
                                            $decoded = json_decode($variables_value, true);
                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                $variables_value = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                                            }
                                        }
                                        ?>
                                        <textarea class="form-control font-monospace" name="variables" id="variables_field" rows="10" 
                                                  placeholder='{"variable1": "Descripción", "variable2": "Descripción"}'><?php echo esc($variables_value); ?></textarea>
                                        <small class="form-text text-muted">
                                            <strong>Variables comunes:</strong> Usa <code>{{nombre_variable}}</code> en el contenido HTML para reemplazar valores dinámicos.
                                            <br>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="loadDefaultVariables()">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Cargar Variables Predefinidas
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="validateJSON()">
                                                <i class="bi bi-check-circle me-1"></i>Validar JSON
                                            </button>
                                        </small>
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
    
    <!-- Modal para Seleccionar Productos -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="productModalLabel">
                        <i class="bi bi-box-seam me-2"></i>Seleccionar Producto para Promocionar
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="productSearch" placeholder="Buscar producto por nombre, código o SKU...">
                    </div>
                    <div id="productsList" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-4">
                            <i class="bi bi-search display-4 text-muted mb-3"></i>
                            <p class="text-muted">Busca un producto para insertarlo en la plantilla</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let tinyMCEEditor = null;
        
        // Inicializar TinyMCE
        tinymce.init({
            selector: '#contenido_html',
            height: 500,
            menubar: false,
            plugins: 'code preview',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | code preview',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            setup: function(editor) {
                tinyMCEEditor = editor;
            }
        });
        
        // Variables predefinidas
        const defaultVariables = {
            "nombre_contacto": "Nombre del contacto",
            "email_contacto": "Email del contacto",
            "nombre_institucion": "Nombre de la institución",
            "mensaje_personalizado": "Mensaje personalizado",
            "asunto": "Asunto del email",
            "link_desuscripcion": "Link para desuscripción",
            "fecha_actual": "Fecha actual",
            "logo_url": "URL del logo"
        };
        
        // Función para cargar variables predefinidas
        function loadDefaultVariables() {
            const variablesField = document.getElementById('variables_field');
            const formatted = JSON.stringify(defaultVariables, null, 4);
            variablesField.value = formatted;
            
            // Mostrar mensaje de confirmación
            showAlert('Variables predefinidas cargadas. Puedes editarlas según tus necesidades.', 'info');
        }
        
        // Función para validar JSON
        function validateJSON() {
            const variablesField = document.getElementById('variables_field');
            const value = variablesField.value.trim();
            
            if (!value || value === '{}') {
                showAlert('El campo está vacío. Usa "Cargar Variables Predefinidas" para empezar.', 'warning');
                return false;
            }
            
            try {
                const parsed = JSON.parse(value);
                // Re-formatear con indentación
                variablesField.value = JSON.stringify(parsed, null, 4);
                showAlert('✓ JSON válido y formateado correctamente.', 'success');
                return true;
            } catch (e) {
                showAlert('✗ Error en JSON: ' + e.message, 'danger');
                return false;
            }
        }
        
        // Función para mostrar alertas
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-2`;
            alertDiv.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            
            const variablesField = document.getElementById('variables_field');
            const parent = variablesField.parentElement;
            parent.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        // Validar formulario antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validar JSON
            if (!validateJSON()) {
                isValid = false;
            }
            
            // Validar contenido HTML (TinyMCE)
            const contenidoHTML = tinymce.get('contenido_html');
            if (contenidoHTML) {
                const content = contenidoHTML.getContent();
                if (!content || content.trim() === '' || content === '<p></p>' || content === '<p><br></p>') {
                    isValid = false;
                    const errorDiv = document.getElementById('contenido_html_error');
                    if (errorDiv) {
                        errorDiv.style.display = 'block';
                    }
                    // Enfocar el editor de TinyMCE
                    contenidoHTML.focus();
                } else {
                    // Sincronizar contenido con textarea original
                    contenidoHTML.save();
                    const errorDiv = document.getElementById('contenido_html_error');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }
                }
            } else {
                // Si TinyMCE no está cargado, validar textarea directamente
                const textarea = document.getElementById('contenido_html');
                if (textarea && (!textarea.value || textarea.value.trim() === '')) {
                    isValid = false;
                    const errorDiv = document.getElementById('contenido_html_error');
                    if (errorDiv) {
                        errorDiv.style.display = 'block';
                    }
                    textarea.focus();
                }
            }
            
            // Validar nombre
            const nombre = document.querySelector('input[name="nombre"]');
            if (nombre && (!nombre.value || nombre.value.trim() === '')) {
                isValid = false;
                nombre.focus();
            }
            
            // Validar asunto
            const asunto = document.querySelector('input[name="asunto"]');
            if (asunto && (!asunto.value || asunto.value.trim() === '')) {
                isValid = false;
                asunto.focus();
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
        
        // ========================================
        // FUNCIONALIDAD DE BÚSQUEDA DE PRODUCTOS
        // ========================================
        let searchTimeout;
        
        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📋 DOM cargado, inicializando búsqueda de productos...');
            
            // Registrar event listener cuando el modal se muestre
            const productModal = document.getElementById('productModal');
            if (productModal) {
                console.log('✅ Modal encontrado');
                
                productModal.addEventListener('shown.bs.modal', function() {
                    console.log('✅ Modal de productos abierto');
                    const productSearchInput = document.getElementById('productSearch');
                    if (productSearchInput) {
                        // Enfocar el input
                        productSearchInput.focus();
                        console.log('✅ Input de búsqueda encontrado y enfocado');
                    } else {
                        console.error('❌ productSearchInput no encontrado');
                    }
                });
                
                productModal.addEventListener('hidden.bs.modal', function() {
                    console.log('🔒 Modal cerrado, limpiando...');
                    // Limpiar al cerrar el modal
                    const productSearchInput = document.getElementById('productSearch');
                    if (productSearchInput) {
                        productSearchInput.value = '';
                    }
                    const productsList = document.getElementById('productsList');
                    if (productsList) {
                        productsList.innerHTML = '<div class="text-center py-4"><i class="bi bi-search display-4 text-muted mb-3"></i><p class="text-muted">Busca un producto para insertarlo en la plantilla</p></div>';
                    }
                });
            } else {
                console.error('❌ Modal productModal no encontrado');
            }
            
            // Event listener para búsqueda (usar delegación de eventos)
            document.addEventListener('input', function(e) {
                if (e.target && e.target.id === 'productSearch') {
                    clearTimeout(searchTimeout);
                    const query = e.target.value.trim();
                    
                    const productsList = document.getElementById('productsList');
                    if (!productsList) {
                        console.error('❌ productsList no encontrado');
                        return;
                    }
                    
                    if (query.length < 2) {
                        productsList.innerHTML = '<div class="text-center py-4"><i class="bi bi-search display-4 text-muted mb-3"></i><p class="text-muted">Escribe al menos 2 caracteres para buscar</p></div>';
                        return;
                    }
                    
                    console.log('🔍 Iniciando búsqueda de productos con query:', query);
                    searchTimeout = setTimeout(() => {
                        searchProducts(query);
                    }, 300);
                }
            });
        });
        
        // También registrar directamente (por si el DOM ya está listo)
        const productSearchInputDirect = document.getElementById('productSearch');
        if (productSearchInputDirect) {
            productSearchInputDirect.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();
                
                const productsList = document.getElementById('productsList');
                if (!productsList) return;
                
                if (query.length < 2) {
                    productsList.innerHTML = '<div class="text-center py-4"><i class="bi bi-search display-4 text-muted mb-3"></i><p class="text-muted">Escribe al menos 2 caracteres para buscar</p></div>';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    searchProducts(query);
                }, 300);
            });
        }
        
        // Función para buscar productos
        function searchProducts(query) {
            console.log('🔍 Buscando productos con query:', query);
            const productsList = document.getElementById('productsList');
            
            if (!productsList) {
                console.error('❌ productsList no encontrado');
                return;
            }
            
            productsList.innerHTML = '<div class="text-center py-4"><i class="bi bi-hourglass-split display-4 text-muted mb-3"></i><p class="text-muted">Buscando productos...</p></div>';
            
            const url = '/admin/catalogo/productos/search-products.php?q=' + encodeURIComponent(query);
            console.log('📡 URL de búsqueda:', url);
            
            fetch(url)
                .then(response => {
                    console.log('📥 Respuesta recibida:', response.status, response.statusText);
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.text(); // Primero obtener como texto para verificar
                })
                .then(text => {
                    console.log('📄 Respuesta raw:', text.substring(0, 200));
                    try {
                        const data = JSON.parse(text);
                        console.log('✅ JSON parseado:', data);
                        
                        if (data.success && data.products && data.products.length > 0) {
                            console.log('✅ Productos encontrados:', data.products.length);
                            let html = '<div class="row g-3">';
                            data.products.forEach(product => {
                                const productName = (product.nombre || 'Sin nombre').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ');
                                const productImage = (product.imagen_principal || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                                const productDesc = (product.descripcion_corta || '').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ').substring(0, 100);
                                const productCode = (product.codigo || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                                const productMarca = (product.marca_nombre || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                                
                                html += `
                                    <div class="col-md-6">
                                        <div class="card product-card h-100" style="cursor: pointer; transition: transform 0.2s; border: 1px solid #dee2e6;" 
                                             onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'" 
                                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'"
                                             onclick="insertProduct(${product.id}, '${productName}', '${productImage}', '${productDesc}')">
                                            <div class="card-body">
                                                ${productImage ? `<img src="${productImage}" class="img-fluid mb-2" style="max-height: 100px; object-fit: cover; width: 100%; border-radius: 4px;" alt="${productName}" onerror="this.style.display='none'">` : '<div style="height: 100px; background: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;"><i class="bi bi-image text-muted" style="font-size: 2rem;"></i></div>'}
                                                <h6 class="card-title mb-2">${product.nombre || 'Sin nombre'}</h6>
                                                ${productCode ? `<small class="text-muted d-block mb-1"><strong>Código:</strong> ${productCode}</small>` : ''}
                                                ${productMarca ? `<small class="text-muted d-block mb-1"><strong>Marca:</strong> ${productMarca}</small>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            productsList.innerHTML = html;
                        } else {
                            console.log('⚠️ No se encontraron productos');
                            productsList.innerHTML = '<div class="text-center py-4"><i class="bi bi-inbox display-4 text-muted mb-3"></i><p class="text-muted">No se encontraron productos</p><small class="text-muted">Intenta con otro término de búsqueda</small></div>';
                        }
                    } catch (e) {
                        console.error('❌ Error parseando JSON:', e);
                        console.error('📄 Texto recibido:', text);
                        productsList.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al procesar respuesta del servidor. Revisa la consola para más detalles.</div>';
                    }
                })
                .catch(error => {
                    console.error('❌ Error en fetch:', error);
                    productsList.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al buscar productos: ' + error.message + '</div>';
                });
        }
        
        // Función para insertar producto en el editor
        function insertProduct(productId, productName, productImage, productDescription) {
            if (!tinyMCEEditor) {
                alert('El editor no está listo. Por favor espera un momento.');
                return;
            }
            
            // Escapar comillas y caracteres especiales
            const safeName = (productName || 'Producto').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const safeImage = (productImage || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const safeDesc = (productDescription || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ');
            
            // Generar HTML del producto para email (compatible con email clients)
            const productHTML = `
<div style="border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; margin: 20px 0; background-color: #ffffff;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            ${safeImage ? `
            <td style="width: 150px; padding-right: 20px; vertical-align: top;">
                <img src="${safeImage}" alt="${safeName}" style="max-width: 150px; height: auto; border-radius: 4px; display: block;" />
            </td>
            ` : ''}
            <td style="vertical-align: top;">
                <h3 style="margin: 0 0 10px 0; color: #0066cc; font-size: 18px; font-weight: bold;">${safeName}</h3>
                ${safeDesc ? `<p style="margin: 0 0 15px 0; color: #666666; font-size: 14px; line-height: 1.6;">${safeDesc}</p>` : ''}
                <a href="https://aramedylaboratorio.com/producto.php?id=${productId}" style="display: inline-block; padding: 10px 20px; background-color: #0066cc; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 14px;">Ver Producto</a>
            </td>
        </tr>
    </table>
</div>
            `.trim();
            
            // Insertar en TinyMCE
            tinyMCEEditor.insertContent(productHTML);
            
            // Cerrar modal
            const modalElement = document.getElementById('productModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // Limpiar búsqueda
            if (productSearchInput) {
                productSearchInput.value = '';
            }
            const productsList = document.getElementById('productsList');
            if (productsList) {
                productsList.innerHTML = '<div class="text-center py-4"><i class="bi bi-search display-4 text-muted mb-3"></i><p class="text-muted">Busca un producto para insertarlo en la plantilla</p></div>';
            }
            
            showAlert('Producto insertado correctamente en la plantilla.', 'success');
        }
    </script>
</body>
</html>

