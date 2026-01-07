<?php
/**
 * Versión de prueba de config.php sin el menú
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Iniciar sesión si no está iniciada (antes de cualquier redirección)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración y verificar autenticación
try {
    require_once __DIR__ . '/../../includes/config.php';
} catch (Exception $e) {
    error_log("Error cargando config.php: " . $e->getMessage());
    die('Error de configuración');
}

try {
    require_once __DIR__ . '/../../includes/functions.php';
} catch (Exception $e) {
    error_log("Error cargando functions.php: " . $e->getMessage());
    die('Error cargando funciones');
}

try {
    require_once __DIR__ . '/../../includes/connection.php';
} catch (Exception $e) {
    error_log("Error cargando connection.php: " . $e->getMessage());
    die('Error de conexión');
}

try {
    require_once __DIR__ . '/../auth_check.php';
} catch (Exception $e) {
    error_log("Error cargando auth_check.php: " . $e->getMessage());
    // Si auth_check falla, verificar manualmente
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: /admin/login.php');
        exit;
    }
}

// Verificar que el usuario sea admin (SEO es solo para admin)
$user_role = $_SESSION['admin_rol'] ?? 'editor';
if ($user_role !== 'admin') {
    // Construir URL absoluta para evitar problemas con subdirectorios
    $sin_permiso_url = '/admin/sin-permiso.php?modulo=' . urlencode('seo') . '&accion=' . urlencode('editar');
    header('Location: ' . $sin_permiso_url);
    exit;
}

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    try {
        checkPermission('seo', 'editar');
    } catch (Exception $e) {
        error_log("Error en checkPermission: " . $e->getMessage());
    } catch (Error $e) {
        error_log("Error fatal en checkPermission: " . $e->getMessage());
    }
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = null;
if (function_exists('getCurrentUser')) {
    try {
        $current_user = getCurrentUser();
    } catch (Exception $e) {
        error_log("Error obteniendo usuario: " . $e->getMessage());
        $current_user = [
            'id' => $_SESSION['admin_user_id'] ?? 0,
            'nombre' => $_SESSION['admin_nombre'] ?? 'Administrador',
            'username' => $_SESSION['admin_username'] ?? 'admin',
            'rol' => $_SESSION['admin_rol'] ?? 'admin'
        ];
    }
} else {
    $current_user = [
        'id' => $_SESSION['admin_user_id'] ?? 0,
        'nombre' => $_SESSION['admin_nombre'] ?? 'Administrador',
        'username' => $_SESSION['admin_username'] ?? 'admin',
        'rol' => $_SESSION['admin_rol'] ?? 'admin'
    ];
}

$success_message = '';
$error_message = '';
$active_tab = $_GET['tab'] ?? 'global';

// Cargar configuraciones
$config_global = null;
$config_paginas = [];

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'seo_config'");
    $table_exists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    if (!empty($table_exists)) {
        $stmt = $pdo->prepare("SELECT * FROM seo_config WHERE tipo = 'global' LIMIT 1");
        $stmt->execute();
        $config_global = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        $stmt = $pdo->query("SELECT * FROM seo_config WHERE tipo = 'pagina' ORDER BY pagina");
        $config_paginas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
    }
} catch (PDOException $e) {
    error_log("Error cargando configuración SEO: " . $e->getMessage());
    $error_message = 'Error al cargar la configuración SEO: ' . $e->getMessage();
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
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIN MENÚ - SOLO PARA PRUEBA -->
            <div class="col-12">
                <h1>TEST: Config.php sin menú</h1>
                <p>Si esta página carga, el problema está en admin_menu.php</p>
                <p>Si esta página también da 403, el problema está en otra parte del código.</p>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo esc($error_message); ?>
                </div>
                <?php endif; ?>
                
                <div class="alert alert-success">
                    ✓ Todas las verificaciones pasaron<br>
                    ✓ Configuración cargada<br>
                    ✓ Usuario: <?php echo esc($current_user['nombre']); ?><br>
                    ✓ Rol: <?php echo esc($current_user['rol']); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

