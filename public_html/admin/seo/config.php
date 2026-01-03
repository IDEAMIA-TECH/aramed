<?php
/**
 * ========================================
 * ADMIN - CONFIGURACIÓN SEO GLOBAL
 * ========================================
 * 
 * Configuración SEO global y por página
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
$active_tab = $_GET['tab'] ?? 'global';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $tipo = $_POST['tipo'] ?? 'global';
        $pagina = !empty($_POST['pagina']) ? $_POST['pagina'] : null;
        
        $data = [
            'titulo_prefijo' => trim($_POST['titulo_prefijo'] ?? ''),
            'titulo_sufijo' => trim($_POST['titulo_sufijo'] ?? ''),
            'meta_descripcion_default' => trim($_POST['meta_descripcion_default'] ?? ''),
            'meta_keywords_default' => trim($_POST['meta_keywords_default'] ?? ''),
            'favicon' => trim($_POST['favicon'] ?? ''),
            'og_image' => trim($_POST['og_image'] ?? ''),
            'twitter_card_type' => $_POST['twitter_card_type'] ?? 'summary_large_image'
        ];
        
        // Insertar o actualizar
        $sql = "
            INSERT INTO seo_config (tipo, pagina, titulo_prefijo, titulo_sufijo, meta_descripcion_default, 
                                   meta_keywords_default, favicon, og_image, twitter_card_type, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                titulo_prefijo = VALUES(titulo_prefijo),
                titulo_sufijo = VALUES(titulo_sufijo),
                meta_descripcion_default = VALUES(meta_descripcion_default),
                meta_keywords_default = VALUES(meta_keywords_default),
                favicon = VALUES(favicon),
                og_image = VALUES(og_image),
                twitter_card_type = VALUES(twitter_card_type),
                updated_at = NOW()
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $tipo, $pagina, $data['titulo_prefijo'], $data['titulo_sufijo'],
            $data['meta_descripcion_default'], $data['meta_keywords_default'],
            $data['favicon'], $data['og_image'], $data['twitter_card_type']
        ]);
        
        // Registrar actividad
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'seo', null, 'seo', [
                'tipo' => $tipo,
                'pagina' => $pagina
            ]);
        }
        
        $success_message = 'Configuración SEO guardada exitosamente';
        $active_tab = $tipo === 'global' ? 'global' : $pagina;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Cargar configuraciones
$config_global = null;
$config_paginas = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM seo_config WHERE tipo = 'global' LIMIT 1");
    $stmt->execute();
    $config_global = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT * FROM seo_config WHERE tipo = 'pagina' ORDER BY pagina");
    $config_paginas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Tabla no existe aún
}

// Si no existe configuración global, usar valores por defecto
if (!$config_global) {
    $config_global = [
        'titulo_prefijo' => 'Aramed y Laboratorios - ',
        'titulo_sufijo' => '',
        'meta_descripcion_default' => SITE_DESCRIPTION,
        'meta_keywords_default' => SITE_KEYWORDS,
        'favicon' => 'assets/images/design/favicon.ico',
        'og_image' => 'assets/images/design/logo-og.jpg',
        'twitter_card_type' => 'summary_large_image'
    ];
}

$paginas_disponibles = [
    'home' => 'Página Principal',
    'catalogo' => 'Catálogo',
    'blog' => 'Blog',
    'proyectos' => 'Proyectos',
    'contacto' => 'Contacto'
];

$current_page = 'config.php';
$current_dir = 'seo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración SEO - Admin <?php echo SITE_NAME; ?></title>
    
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
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
                                <i class="bi bi-gear me-2"></i>Configuración SEO
                            </h2>
                            <p class="mb-0 opacity-75">Configuración global y por página</p>
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
                
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link <?php echo $active_tab === 'global' ? 'active' : ''; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#global">
                            <i class="bi bi-globe me-2"></i>Global
                        </button>
                    </li>
                    <?php foreach ($paginas_disponibles as $key => $label): ?>
                    <li class="nav-item">
                        <button class="nav-link <?php echo $active_tab === $key ? 'active' : ''; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#<?php echo $key; ?>">
                            <?php echo esc($label); ?>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Tab: Global -->
                    <div class="tab-pane fade <?php echo $active_tab === 'global' ? 'show active' : ''; ?>" id="global">
                        <div class="config-card">
                            <form method="POST" action="?tab=global">
                                <input type="hidden" name="tipo" value="global">
                                <h4 class="mb-4">
                                    <i class="bi bi-globe me-2"></i>Configuración SEO Global
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prefijo para Títulos</label>
                                        <input type="text" class="form-control" name="titulo_prefijo" 
                                               value="<?php echo esc($config_global['titulo_prefijo'] ?? ''); ?>" 
                                               maxlength="100">
                                        <small class="form-text text-muted">Se agregará antes del título de cada página</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sufijo para Títulos</label>
                                        <input type="text" class="form-control" name="titulo_sufijo" 
                                               value="<?php echo esc($config_global['titulo_sufijo'] ?? ''); ?>" 
                                               maxlength="100">
                                        <small class="form-text text-muted">Se agregará después del título de cada página</small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Descripción por Defecto</label>
                                        <textarea class="form-control" name="meta_descripcion_default" rows="3" 
                                                  maxlength="500"><?php echo esc($config_global['meta_descripcion_default'] ?? ''); ?></textarea>
                                        <small class="form-text text-muted">Máximo 500 caracteres</small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Palabras Clave por Defecto</label>
                                        <input type="text" class="form-control" name="meta_keywords_default" 
                                               value="<?php echo esc($config_global['meta_keywords_default'] ?? ''); ?>">
                                        <small class="form-text text-muted">Separadas por comas</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Favicon</label>
                                        <input type="text" class="form-control" name="favicon" 
                                               value="<?php echo esc($config_global['favicon'] ?? ''); ?>" 
                                               placeholder="assets/images/design/favicon.ico">
                                        <small class="form-text text-muted">Ruta relativa desde la raíz</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Imagen Open Graph por Defecto</label>
                                        <input type="text" class="form-control" name="og_image" 
                                               value="<?php echo esc($config_global['og_image'] ?? ''); ?>" 
                                               placeholder="assets/images/design/logo-og.jpg">
                                        <small class="form-text text-muted">Ruta relativa desde la raíz</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de Twitter Card</label>
                                        <select class="form-select" name="twitter_card_type">
                                            <option value="summary" <?php echo ($config_global['twitter_card_type'] ?? '') === 'summary' ? 'selected' : ''; ?>>Summary</option>
                                            <option value="summary_large_image" <?php echo ($config_global['twitter_card_type'] ?? '') === 'summary_large_image' ? 'selected' : ''; ?>>Summary Large Image</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración Global
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabs por Página -->
                    <?php foreach ($paginas_disponibles as $key => $label): ?>
                    <?php
                    $config_pagina = null;
                    foreach ($config_paginas as $cp) {
                        if ($cp['pagina'] === $key) {
                            $config_pagina = $cp;
                            break;
                        }
                    }
                    if (!$config_pagina) {
                        $config_pagina = [
                            'titulo_prefijo' => '',
                            'titulo_sufijo' => '',
                            'meta_descripcion_default' => '',
                            'meta_keywords_default' => '',
                            'og_image' => ''
                        ];
                    }
                    ?>
                    <div class="tab-pane fade <?php echo $active_tab === $key ? 'show active' : ''; ?>" id="<?php echo $key; ?>">
                        <div class="config-card">
                            <form method="POST" action="?tab=<?php echo $key; ?>">
                                <input type="hidden" name="tipo" value="pagina">
                                <input type="hidden" name="pagina" value="<?php echo $key; ?>">
                                <h4 class="mb-4">
                                    <i class="bi bi-file-text me-2"></i>Configuración SEO: <?php echo esc($label); ?>
                                </h4>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Esta configuración sobrescribe la configuración global solo para esta página.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prefijo para Título</label>
                                        <input type="text" class="form-control" name="titulo_prefijo" 
                                               value="<?php echo esc($config_pagina['titulo_prefijo'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sufijo para Título</label>
                                        <input type="text" class="form-control" name="titulo_sufijo" 
                                               value="<?php echo esc($config_pagina['titulo_sufijo'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-control" name="meta_descripcion_default" rows="3" 
                                                  maxlength="500"><?php echo esc($config_pagina['meta_descripcion_default'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Palabras Clave</label>
                                        <input type="text" class="form-control" name="meta_keywords_default" 
                                               value="<?php echo esc($config_pagina['meta_keywords_default'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Imagen Open Graph</label>
                                        <input type="text" class="form-control" name="og_image" 
                                               value="<?php echo esc($config_pagina['og_image'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

