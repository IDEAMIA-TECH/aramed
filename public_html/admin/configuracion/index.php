<?php
/**
 * ========================================
 * ADMIN - CONFIGURACIÓN GENERAL
 * ========================================
 * 
 * Panel de administración para configuración del sitio
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
    checkPermission('configuracion', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar formulario
$success_message = '';
$error_message = '';
$active_tab = $_GET['tab'] ?? 'empresa';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $categoria = $_POST['categoria'] ?? '';
        $configuraciones = $_POST['config'] ?? [];
        
        foreach ($configuraciones as $clave => $valor) {
            // Determinar tipo según la clave
            $tipo = 'text';
            if (strpos($clave, '_activo') !== false || strpos($clave, '_enabled') !== false) {
                $tipo = 'boolean';
            } elseif (strpos($clave, '_puerto') !== false || strpos($clave, '_id') !== false) {
                $tipo = 'number';
            } elseif (in_array($clave, ['legal_privacidad', 'legal_terminos', 'legal_cookies'])) {
                $tipo = 'html';
            }
            
            setConfig($clave, $valor, $tipo, $categoria);
        }
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'configuracion', null, 'configuracion', [
                'categoria' => $categoria
            ]);
        }
        
        $success_message = 'Configuración actualizada exitosamente';
        $active_tab = $categoria;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Cargar configuraciones por categoría
$config_empresa = getConfigByCategory('empresa');
$config_smtp = getConfigByCategory('smtp');
$config_integraciones = getConfigByCategory('integraciones');
$config_legal = getConfigByCategory('legal');
$config_seo = getConfigByCategory('seo');

$current_page = 'index.php';
$current_dir = 'configuracion';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Admin <?php echo SITE_NAME; ?></title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .config-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
                                <i class="bi bi-gear me-2"></i>Configuración General
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona la configuración del sitio</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="analyze-tables.php" class="btn btn-light">
                                <i class="bi bi-speedometer2 me-2"></i>Analizar Tablas
                            </a>
                        </div>
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
                
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link <?php echo $active_tab === 'empresa' ? 'active' : ''; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#empresa">
                            <i class="bi bi-building me-2"></i>Empresa
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?php echo $active_tab === 'smtp' ? 'active' : ''; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#smtp">
                            <i class="bi bi-envelope me-2"></i>SMTP
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?php echo $active_tab === 'integraciones' ? 'active' : ''; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#integraciones">
                            <i class="bi bi-plug me-2"></i>Integraciones
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?php echo $active_tab === 'legal' ? 'active' : ''; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#legal">
                            <i class="bi bi-shield-check me-2"></i>Textos Legales
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?php echo $active_tab === 'seo' ? 'active' : ''; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#seo">
                            <i class="bi bi-search me-2"></i>SEO
                        </button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Tab: Empresa -->
                    <div class="tab-pane fade <?php echo $active_tab === 'empresa' ? 'show active' : ''; ?>" id="empresa">
                        <div class="config-card">
                            <form method="POST" action="?tab=empresa">
                                <input type="hidden" name="categoria" value="empresa">
                                <h4 class="mb-4">
                                    <i class="bi bi-building me-2"></i>Información de la Empresa
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre de la Empresa</label>
                                        <input type="text" class="form-control" name="config[empresa_nombre]" 
                                               value="<?php echo esc($config_empresa['empresa_nombre'] ?? SITE_NAME); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Razón Social</label>
                                        <input type="text" class="form-control" name="config[empresa_razon_social]" 
                                               value="<?php echo esc($config_empresa['empresa_razon_social'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Dirección</label>
                                        <textarea class="form-control" name="config[empresa_direccion]" rows="2"><?php echo esc($config_empresa['empresa_direccion'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono Principal</label>
                                        <input type="text" class="form-control" name="config[empresa_telefono]" 
                                               value="<?php echo esc($config_empresa['empresa_telefono'] ?? CONTACT_PHONE); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Email Principal</label>
                                        <input type="email" class="form-control" name="config[empresa_email]" 
                                               value="<?php echo esc($config_empresa['empresa_email'] ?? CONTACT_EMAIL); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Sitio Web</label>
                                        <input type="url" class="form-control" name="config[empresa_website]" 
                                               value="<?php echo esc($config_empresa['empresa_website'] ?? SITE_URL); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Email de Ventas</label>
                                        <input type="email" class="form-control" name="config[empresa_email_ventas]" 
                                               value="<?php echo esc($config_empresa['empresa_email_ventas'] ?? MARKETING_EMAIL); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Email de Soporte</label>
                                        <input type="email" class="form-control" name="config[empresa_email_soporte]" 
                                               value="<?php echo esc($config_empresa['empresa_email_soporte'] ?? SUPPORT_EMAIL); ?>">
                                    </div>
                                </div>
                                
                                <h5 class="mt-4 mb-3">Redes Sociales</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Facebook</label>
                                        <input type="url" class="form-control" name="config[empresa_facebook]" 
                                               value="<?php echo esc($config_empresa['empresa_facebook'] ?? SOCIAL_FACEBOOK); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" class="form-control" name="config[empresa_instagram]" 
                                               value="<?php echo esc($config_empresa['empresa_instagram'] ?? SOCIAL_INSTAGRAM); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">LinkedIn</label>
                                        <input type="url" class="form-control" name="config[empresa_linkedin]" 
                                               value="<?php echo esc($config_empresa['empresa_linkedin'] ?? SOCIAL_LINKEDIN); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Twitter</label>
                                        <input type="url" class="form-control" name="config[empresa_twitter]" 
                                               value="<?php echo esc($config_empresa['empresa_twitter'] ?? SOCIAL_TWITTER); ?>">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tab: SMTP -->
                    <div class="tab-pane fade <?php echo $active_tab === 'smtp' ? 'show active' : ''; ?>" id="smtp">
                        <div class="config-card">
                            <form method="POST" action="?tab=smtp">
                                <input type="hidden" name="categoria" value="smtp">
                                <h4 class="mb-4">
                                    <i class="bi bi-envelope me-2"></i>Configuración SMTP
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Servidor SMTP</label>
                                        <input type="text" class="form-control" name="config[smtp_host]" 
                                               value="<?php echo esc($config_smtp['smtp_host'] ?? SMTP_HOST); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Puerto</label>
                                        <input type="number" class="form-control" name="config[smtp_puerto]" 
                                               value="<?php echo esc($config_smtp['smtp_puerto'] ?? SMTP_PORT); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" class="form-control" name="config[smtp_usuario]" 
                                               value="<?php echo esc($config_smtp['smtp_usuario'] ?? SMTP_USERNAME); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" name="config[smtp_password]" 
                                               value="<?php echo esc($config_smtp['smtp_password'] ?? SMTP_PASSWORD); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Encriptación</label>
                                        <select class="form-select" name="config[smtp_encryption]">
                                            <option value="tls" <?php echo ($config_smtp['smtp_encryption'] ?? SMTP_SECURE) === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo ($config_smtp['smtp_encryption'] ?? SMTP_SECURE) === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Remitente</label>
                                        <input type="email" class="form-control" name="config[smtp_from_email]" 
                                               value="<?php echo esc($config_smtp['smtp_from_email'] ?? MAIL_FROM_EMAIL); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre Remitente</label>
                                        <input type="text" class="form-control" name="config[smtp_from_name]" 
                                               value="<?php echo esc($config_smtp['smtp_from_name'] ?? MAIL_FROM_NAME); ?>">
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Puedes probar la configuración SMTP desde <a href="test-smtp.php" class="alert-link">aquí</a>.
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tab: Integraciones -->
                    <div class="tab-pane fade <?php echo $active_tab === 'integraciones' ? 'show active' : ''; ?>" id="integraciones">
                        <div class="config-card">
                            <form method="POST" action="?tab=integraciones">
                                <input type="hidden" name="categoria" value="integraciones">
                                <h4 class="mb-4">
                                    <i class="bi bi-plug me-2"></i>Integraciones
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Google Analytics ID</label>
                                        <input type="text" class="form-control" name="config[google_analytics_id]" 
                                               value="<?php echo esc($config_integraciones['google_analytics_id'] ?? 'G-3BPRR93ZCY'); ?>">
                                        <small class="form-text text-muted">Ejemplo: G-3BPRR93ZCY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" name="config[google_analytics_activo]">
                                            <option value="1" <?php echo ($config_integraciones['google_analytics_activo'] ?? true) ? 'selected' : ''; ?>>Activo</option>
                                            <option value="0" <?php echo ($config_integraciones['google_analytics_activo'] ?? true) ? '' : 'selected'; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tab: Textos Legales -->
                    <div class="tab-pane fade <?php echo $active_tab === 'legal' ? 'show active' : ''; ?>" id="legal">
                        <div class="config-card">
                            <form method="POST" action="?tab=legal">
                                <input type="hidden" name="categoria" value="legal">
                                <h4 class="mb-4">
                                    <i class="bi bi-shield-check me-2"></i>Textos Legales
                                </h4>
                                
                                <div class="mb-4">
                                    <label class="form-label">Política de Privacidad</label>
                                    <textarea class="form-control" name="config[legal_privacidad]" id="legal_privacidad" rows="15"><?php echo $config_legal['legal_privacidad'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Términos y Condiciones</label>
                                    <textarea class="form-control" name="config[legal_terminos]" id="legal_terminos" rows="15"><?php echo $config_legal['legal_terminos'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Política de Cookies</label>
                                    <textarea class="form-control" name="config[legal_cookies]" id="legal_cookies" rows="15"><?php echo $config_legal['legal_cookies'] ?? ''; ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tab: SEO -->
                    <div class="tab-pane fade <?php echo $active_tab === 'seo' ? 'show active' : ''; ?>" id="seo">
                        <div class="config-card">
                            <form method="POST" action="?tab=seo">
                                <input type="hidden" name="categoria" value="seo">
                                <h4 class="mb-4">
                                    <i class="bi bi-search me-2"></i>Configuración SEO
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prefijo para Títulos</label>
                                        <input type="text" class="form-control" name="config[seo_title_prefix]" 
                                               value="<?php echo esc($config_seo['seo_title_prefix'] ?? 'Aramed y Laboratorios - '); ?>">
                                        <small class="form-text text-muted">Se agregará antes del título de cada página</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sufijo para Títulos</label>
                                        <input type="text" class="form-control" name="config[seo_title_suffix]" 
                                               value="<?php echo esc($config_seo['seo_title_suffix'] ?? ''); ?>">
                                        <small class="form-text text-muted">Se agregará después del título de cada página</small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Descripción por Defecto</label>
                                        <textarea class="form-control" name="config[seo_default_description]" rows="3"><?php echo esc($config_seo['seo_default_description'] ?? SITE_DESCRIPTION); ?></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Palabras Clave por Defecto</label>
                                        <input type="text" class="form-control" name="config[seo_default_keywords]" 
                                               value="<?php echo esc($config_seo['seo_default_keywords'] ?? SITE_KEYWORDS); ?>">
                                        <small class="form-text text-muted">Separadas por comas</small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Imagen Open Graph por Defecto</label>
                                        <input type="text" class="form-control" name="config[seo_og_image]" 
                                               value="<?php echo esc($config_seo['seo_og_image'] ?? 'assets/images/design/logo-og.jpg'); ?>">
                                        <small class="form-text text-muted">Ruta relativa desde la raíz del sitio</small>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TinyMCE para textos legales
        tinymce.init({
            selector: '#legal_privacidad, #legal_terminos, #legal_cookies',
            height: 400,
            menubar: false,
            plugins: 'lists link table code',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code',
            language: 'es',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
        
        // Activar tab según parámetro
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const tabElement = document.querySelector(`[data-bs-target="#${tab}"]`);
            if (tabElement) {
                new bootstrap.Tab(tabElement).show();
            }
        }
    </script>
</body>
</html>

