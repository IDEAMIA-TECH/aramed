<?php
/**
 * ========================================
 * ADMIN - CONFIGURACIÓN SCHEMA.ORG
 * ========================================
 * 
 * Configuración de datos estructurados JSON-LD
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

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$success_message = '';
$error_message = '';

// Cargar configuración de Schema desde configuracion table
$schema_config = [
    'organization_enabled' => getConfig('schema_organization_enabled', '1') === '1',
    'organization_name' => getConfig('schema_organization_name', SITE_NAME),
    'organization_logo' => getConfig('schema_organization_logo', SITE_URL . '/assets/images/design/logo.png'),
    'organization_url' => getConfig('schema_organization_url', SITE_URL),
    'organization_contact' => getConfig('schema_organization_contact', CONTACT_EMAIL),
    'website_enabled' => getConfig('schema_website_enabled', '1') === '1',
    'product_enabled' => getConfig('schema_product_enabled', '1') === '1',
    'blog_enabled' => getConfig('schema_blog_enabled', '1') === '1',
    'breadcrumb_enabled' => getConfig('schema_breadcrumb_enabled', '1') === '1'
];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Guardar configuración
        setConfig('schema_organization_enabled', isset($_POST['organization_enabled']) ? '1' : '0', 'boolean', 'seo');
        setConfig('schema_organization_name', $_POST['organization_name'] ?? '', 'text', 'seo');
        setConfig('schema_organization_logo', $_POST['organization_logo'] ?? '', 'text', 'seo');
        setConfig('schema_organization_url', $_POST['organization_url'] ?? '', 'text', 'seo');
        setConfig('schema_organization_contact', $_POST['organization_contact'] ?? '', 'text', 'seo');
        setConfig('schema_website_enabled', isset($_POST['website_enabled']) ? '1' : '0', 'boolean', 'seo');
        setConfig('schema_product_enabled', isset($_POST['product_enabled']) ? '1' : '0', 'boolean', 'seo');
        setConfig('schema_blog_enabled', isset($_POST['blog_enabled']) ? '1' : '0', 'boolean', 'seo');
        setConfig('schema_breadcrumb_enabled', isset($_POST['breadcrumb_enabled']) ? '1' : '0', 'boolean', 'seo');
        
        $success_message = 'Configuración de Schema.org guardada exitosamente';
        
        // Recargar configuración
        $schema_config = [
            'organization_enabled' => getConfig('schema_organization_enabled', '1') === '1',
            'organization_name' => getConfig('schema_organization_name', SITE_NAME),
            'organization_logo' => getConfig('schema_organization_logo', SITE_URL . '/assets/images/design/logo.png'),
            'organization_url' => getConfig('schema_organization_url', SITE_URL),
            'organization_contact' => getConfig('schema_organization_contact', CONTACT_EMAIL),
            'website_enabled' => getConfig('schema_website_enabled', '1') === '1',
            'product_enabled' => getConfig('schema_product_enabled', '1') === '1',
            'blog_enabled' => getConfig('schema_blog_enabled', '1') === '1',
            'breadcrumb_enabled' => getConfig('schema_breadcrumb_enabled', '1') === '1'
        ];
        
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'seo', null, 'Schema.org configurado');
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Generar preview de JSON-LD
$preview_json = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $schema_config['organization_name'],
    'url' => $schema_config['organization_url'],
    'logo' => $schema_config['organization_logo'],
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'email' => $schema_config['organization_contact']
    ]
];

$current_page = 'schema.php';
$current_dir = 'seo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schema.org - SEO Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
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
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }
        
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            max-height: 400px;
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
                                <i class="bi bi-code-square me-2"></i>Configuración Schema.org
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona datos estructurados JSON-LD para motores de búsqueda</p>
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
                
                <form method="POST" action="">
                    <!-- Tipos de Schema -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-toggle-on me-2"></i>Activar/Desactivar Tipos de Schema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="organization_enabled" 
                                               name="organization_enabled" <?php echo $schema_config['organization_enabled'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="organization_enabled">
                                            <strong>Organization</strong>
                                            <br><small class="text-muted">Datos de la organización</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="website_enabled" 
                                               name="website_enabled" <?php echo $schema_config['website_enabled'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="website_enabled">
                                            <strong>WebSite</strong>
                                            <br><small class="text-muted">Información del sitio web</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="product_enabled" 
                                               name="product_enabled" <?php echo $schema_config['product_enabled'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="product_enabled">
                                            <strong>Product</strong>
                                            <br><small class="text-muted">Datos de productos</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="blog_enabled" 
                                               name="blog_enabled" <?php echo $schema_config['blog_enabled'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="blog_enabled">
                                            <strong>BlogPosting</strong>
                                            <br><small class="text-muted">Datos de artículos del blog</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="breadcrumb_enabled" 
                                               name="breadcrumb_enabled" <?php echo $schema_config['breadcrumb_enabled'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="breadcrumb_enabled">
                                            <strong>BreadcrumbList</strong>
                                            <br><small class="text-muted">Navegación breadcrumb</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de Organization -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-building me-2"></i>Datos de la Organización
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre de la Organización *</label>
                                    <input type="text" class="form-control" name="organization_name" 
                                           value="<?php echo esc($schema_config['organization_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">URL de la Organización *</label>
                                    <input type="url" class="form-control" name="organization_url" 
                                           value="<?php echo esc($schema_config['organization_url']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Logo de la Organización *</label>
                                    <input type="url" class="form-control" name="organization_logo" 
                                           value="<?php echo esc($schema_config['organization_logo']); ?>" required>
                                    <small class="form-text text-muted">URL completa de la imagen del logo</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email de Contacto *</label>
                                    <input type="email" class="form-control" name="organization_contact" 
                                           value="<?php echo esc($schema_config['organization_contact']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-eye me-2"></i>Vista Previa JSON-LD
                            </h5>
                        </div>
                        <div class="card-body">
                            <pre id="json-preview"><?php echo json_encode($preview_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></pre>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar Configuración
                        </button>
                        <a href="https://search.google.com/test/rich-results" target="_blank" class="btn btn-outline-info">
                            <i class="bi bi-google me-2"></i>Probar en Google Rich Results
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar preview al cambiar campos
        document.querySelectorAll('input[name^="organization_"]').forEach(input => {
            input.addEventListener('input', updatePreview);
        });
        
        function updatePreview() {
            const preview = {
                '@context': 'https://schema.org',
                '@type': 'Organization',
                'name': document.querySelector('input[name="organization_name"]').value,
                'url': document.querySelector('input[name="organization_url"]').value,
                'logo': document.querySelector('input[name="organization_logo"]').value,
                'contactPoint': {
                    '@type': 'ContactPoint',
                    'email': document.querySelector('input[name="organization_contact"]').value
                }
            };
            
            document.getElementById('json-preview').textContent = JSON.stringify(preview, null, 2);
        }
    </script>
</body>
</html>

