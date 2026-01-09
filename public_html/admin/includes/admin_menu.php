<?php
/**
 * ========================================
 * ADMIN - MENÚ MAESTRO
 * ========================================
 * 
 * Menú de navegación reutilizable para todas las páginas de administración
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Iniciar logging de debug
$menu_debug_log_file = __DIR__ . '/../../logs/menu-debug.log';
$menu_debug_log = function($message) use ($menu_debug_log_file) {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    @file_put_contents($menu_debug_log_file, $log_message, FILE_APPEND);
};

$menu_debug_log("=== INICIO admin_menu.php ===");
$menu_debug_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET'));
$menu_debug_log("PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET'));

// Cargar funciones RBAC si existen
if (file_exists(__DIR__ . '/../../includes/rbac_functions.php')) {
    $menu_debug_log("Cargando rbac_functions.php...");
    try {
        require_once __DIR__ . '/../../includes/rbac_functions.php';
        $menu_debug_log("rbac_functions.php cargado exitosamente");
    } catch (Exception $e) {
        $menu_debug_log("Error cargando rbac_functions.php: " . $e->getMessage());
    } catch (Error $e) {
        $menu_debug_log("Error fatal cargando rbac_functions.php: " . $e->getMessage());
    }
} else {
    $menu_debug_log("rbac_functions.php no existe");
}

// Obtener información del usuario actual
$current_user = [
    'nombre' => $_SESSION['admin_username'] ?? 'Administrador',
    'username' => $_SESSION['admin_username'] ?? 'admin',
    'id' => $_SESSION['admin_user_id'] ?? null,
    'rol' => $_SESSION['admin_rol'] ?? 'editor'
];

// Determinar si el usuario es admin
$is_admin = ($current_user['rol'] === 'admin');

// Obtener permisos del usuario actual (si RBAC está disponible)
$user_permissions = [];
if (function_exists('getUserPermissions') && isset($current_user['id'])) {
    $menu_debug_log("Obteniendo permisos del usuario ID: " . $current_user['id']);
    try {
        $user_permissions = getUserPermissions($current_user['id']);
        $menu_debug_log("Permisos obtenidos: " . count($user_permissions) . " módulos");
    } catch (Exception $e) {
        $menu_debug_log("Error obteniendo permisos: " . $e->getMessage());
    } catch (Error $e) {
        $menu_debug_log("Error fatal obteniendo permisos: " . $e->getMessage());
    }
} else {
    $menu_debug_log("getUserPermissions no disponible o user_id no establecido");
}

// Función helper para verificar si el usuario puede ver un módulo
function canSeeModule($modulo, $user_permissions) {
    // Si no hay permisos cargados, mostrar todo (compatibilidad)
    if (empty($user_permissions)) {
        return true;
    }
    
    // Si tiene permiso de "ver" en el módulo, mostrar
    return isset($user_permissions[$modulo]) && in_array('ver', $user_permissions[$modulo]);
}

// Determinar la página y directorio actual
// Usar $_SERVER['PHP_SELF'] que es más confiable
$php_self = $_SERVER['PHP_SELF'] ?? '';
$current_page = basename($php_self);

// Extraer el path relativo desde /admin/
// Ejemplo: /admin/catalogo/index.php -> catalogo
// Ejemplo: /admin/catalogo/productos/edit.php -> catalogo (primer nivel)
$admin_path = '/admin/';
$pos = strpos($php_self, $admin_path);

if ($pos !== false) {
    $relative_path = substr($php_self, $pos + strlen($admin_path));
    $path_parts = explode('/', $relative_path);
    
    // Filtrar partes vacías
    $path_parts = array_filter($path_parts, function($part) {
        return !empty($part);
    });
    $path_parts = array_values($path_parts);
    
    // Separar directorios del archivo
    $directorios = [];
    $archivo = null;
    
    foreach ($path_parts as $part) {
        // Si tiene extensión, es un archivo
        if (strpos($part, '.') !== false && strpos($part, '.') !== 0) {
            $archivo = $part;
        } else {
            $directorios[] = $part;
        }
    }
    
    // Calcular profundidad (número de subdirectorios, sin contar el archivo)
    // Si estamos en admin/catalogo/productos/edit.php:
    // - directorios = ['catalogo', 'productos']
    // - profundidad = 2 (necesitamos ../../ para volver a admin/)
    $profundidad = count($directorios);
    
    // Si hay al menos un directorio, usarlo como current_dir
    // path_parts será ['catalogo', 'index.php'] para /admin/catalogo/index.php -> current_dir = 'catalogo'
    // path_parts será ['catalogo', 'productos', 'edit.php'] para /admin/catalogo/productos/edit.php -> current_dir = 'catalogo'
    if (count($directorios) > 0 && !empty($directorios[0])) {
        $current_dir = $directorios[0]; // Primer nivel siempre
        // Calcular base_path basado en la profundidad
        // Si estamos en admin/catalogo/productos/edit.php, profundidad = 2
        // Necesitamos ../../ para volver a admin/
        $base_path = str_repeat('../', $profundidad);
    } else {
        // Estamos en admin/ directamente
        $current_dir = '';
        $base_path = '';
    }
} else {
    // Fallback: usar dirname
    $script_dir = dirname($php_self);
    $current_dir = basename($script_dir);
    
    // Si estamos en la raíz del admin
    if ($current_dir === '.' || $current_dir === '/' || $current_dir === 'admin' || empty($current_dir)) {
        $current_dir = '';
        $base_path = '';
    } else {
        // Calcular profundidad basado en el número de / en el path
        $profundidad = substr_count($script_dir, '/') - substr_count('/admin/', '/');
        $base_path = str_repeat('../', max(1, $profundidad));
    }
}

// Debug temporal
if (isset($_GET['debug_menu'])) {
    error_log("MENU DEBUG - php_self: $php_self | current_dir: '$current_dir' | current_page: '$current_page' | path_parts: " . print_r($path_parts ?? [], true));
}

// Función helper para generar rutas correctas
// SIEMPRE genera rutas absolutas desde /admin/ para evitar problemas con subdirectorios anidados
function adminUrl($path, $base_path = '') {
    // Si la ruta ya comienza con http:// o https://, devolverla tal cual
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    // Si la ruta comienza con /admin/, devolverla tal cual (ya es correcta)
    if (strpos($path, '/admin/') === 0) {
        return $path;
    }
    
    // Si la ruta comienza con ../, es un enlace fuera del admin (ej: ../blog.php, ../index.php)
    // Convertir a ruta absoluta desde la raíz del sitio
    if (strpos($path, '../') === 0) {
        // Remover ../ y agregar / al inicio
        $clean_path = ltrim(str_replace('../', '', $path), '/');
        return '/' . $clean_path;
    }
    
    // Si la ruta comienza con / pero no es /admin/, convertirla a /admin/
    if (strpos($path, '/') === 0) {
        // Si es solo /, devolver /admin/
        if ($path === '/') {
            return '/admin/';
        }
        // Si es /seo/index.php, convertir a /admin/seo/index.php
        return '/admin' . $path;
    }
    
    // REGLA PRINCIPAL: Si la ruta contiene un subdirectorio (ej: 'seo/index.php' o 'catalogo/index.php')
    // SIEMPRE generar ruta absoluta desde /admin/
    // Esto evita problemas cuando estamos en subdirectorios anidados
    // Ejemplo: desde admin/catalogo/productos/edit.php, 'catalogo/index.php' -> '/admin/catalogo/index.php'
    if (strpos($path, '/') !== false) {
        return '/admin/' . $path;
    }
    
    // Archivos que están en la raíz del admin (no en subdirectorios)
    // Estos SIEMPRE deben usar ruta absoluta desde /admin/ para evitar problemas
    // cuando estamos navegando desde subdirectorios
    $root_admin_files = [
        'index.php',
        'newsletter-simple.php',
        'usuarios.php',
        'perfil.php',
        'logout.php',
        'topbar-messages.php',
        'newsletter-subscriptions.php',
        'analyze-tables.php',
        'view-logs.php',
        'view-logs-simple.php'
    ];
    
    // Si es un archivo de la raíz del admin, usar ruta absoluta
    if (in_array($path, $root_admin_files)) {
        return '/admin/' . $path;
    }
    
    // Si es solo un archivo (ej: 'edit.php', 'create.php', 'view.php')
    // y NO está en la lista de archivos de la raíz, usar ruta relativa
    // Esto funciona para archivos en el mismo directorio (ej: edit.php -> create.php)
    return $path;
}

// Función para determinar si un enlace está activo
function isActive($target_page, $current_page, $current_dir, $root_only = false) {
    // Normalizar las rutas para comparación
    $target_normalized = str_replace('\\', '/', $target_page);
    $target_normalized = trim($target_normalized, '/');
    $target_basename = basename($target_normalized);
    $target_has_path = (strpos($target_normalized, '/') !== false);
    
    // Si root_only es true, este enlace solo puede estar activo en la raíz
    if ($root_only) {
        return empty($current_dir) && !$target_has_path && ($target_basename === $current_page);
    }
    
    // CASO 1: Estamos en la raíz del admin (current_dir está vacío)
    // Ejemplo: admin/index.php
    if (empty($current_dir)) {
        // Solo los enlaces sin path (ej: 'index.php') pueden estar activos
        // Los enlaces con path (ej: 'home/index.php') NO pueden estar activos en la raíz
        return !$target_has_path && ($target_basename === $current_page);
    }
    
    // CASO 2: Estamos en un subdirectorio (ej: admin/home/index.php)
    // current_dir = 'home', current_page = 'index.php'
    
    // SUBCASO 2A: Target tiene un path con directorio (ej: 'home/index.php', 'catalogo/index.php')
    // Este es un enlace principal del menú (no un submenú)
    if ($target_has_path) {
        // Extraer el directorio del target
        $target_dir = dirname($target_normalized);
        $target_file = basename($target_normalized);
        
        // Solo está activo si:
        // 1. El directorio del target coincide con current_dir
        // 2. El archivo del target coincide con current_page
        return ($target_dir === $current_dir) && ($target_file === $current_page);
    }
    
    // SUBCASO 2B: Target es solo un archivo sin path (ej: 'banners.php', 'index.php')
    // Esto es un enlace de submenú dentro del directorio actual
    // Solo está activo si el nombre del archivo coincide con current_page
    // (ya sabemos que estamos en el directorio correcto porque current_dir no está vacío)
    return ($target_basename === $current_page);
}

// Función para generar clases CSS del enlace
function getNavLinkClass($target_page, $current_page, $current_dir, $root_only = false) {
    $classes = ['nav-link'];
    if (isActive($target_page, $current_page, $current_dir, $root_only)) {
        $classes[] = 'active';
    }
    return implode(' ', $classes);
}

// Función helper para obtener la ruta del logo
function getLogoPath($current_dir, $base_path) {
    if (empty($current_dir)) {
        return '../assets/images/design/logo.png';
    }
    return '../../assets/images/design/logo.png';
}

// Debug temporal - eliminar después
// error_log("DEBUG MENU - current_dir: " . $current_dir . " | current_page: " . $current_page . " | PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A'));
?>

<!-- Sidebar -->
<div class="col-md-3 col-lg-3 admin-sidebar p-0">
    <div class="p-3" style="max-width: 100%; overflow-x: hidden;">
        <!-- Debug temporal - eliminar después -->
        <?php if (isset($_GET['debug_menu'])): ?>
        <div style="background: yellow; padding: 5px; margin-bottom: 10px; font-size: 10px;">
            DEBUG: current_dir="<?php echo htmlspecialchars($current_dir); ?>" | 
            current_page="<?php echo htmlspecialchars($current_page); ?>" | 
            PHP_SELF="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] ?? ''); ?>"
        </div>
        <?php endif; ?>
        <div class="admin-logo mb-4">
            <img src="<?php echo getLogoPath($current_dir, $base_path); ?>" 
                 alt="Aramed y Laboratorio" 
                 class="logo-image me-2"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
            <i class="bi bi-shield-lock me-2" style="display: none;"></i>
            <span class="logo-text">Admin Panel</span>
        </div>
        
        <!-- Información del usuario -->
        <div class="user-info mb-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle me-2"></i>
                <div>
                    <div class="fw-bold"><?php echo esc($current_user['nombre']); ?></div>
                    <small class="opacity-75"><?php echo esc($current_user['username']); ?></small>
                </div>
            </div>
        </div>
        
        <nav class="nav flex-column">
            <!-- Dashboard -->
            <!-- Solo activo si estamos en la raíz del admin -->
            <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir, true); ?>" href="<?php echo adminUrl('index.php', $base_path); ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            
            <!-- Home -->
            <?php if (canSeeModule('home', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('home/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('home/index.php', $base_path); ?>">
                    <i class="bi bi-house-door me-2"></i>Home
                </a>
                <?php if ($current_dir === 'home'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php" style="color: #212529 !important;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('banners.php', $current_page, $current_dir); ?> nav-link-sm" href="banners.php" style="color: #212529 !important;">
                        <i class="bi bi-image me-2"></i>Banners
                    </a>
                    <a class="<?php echo getNavLinkClass('productos-destacados.php', $current_page, $current_dir); ?> nav-link-sm" href="productos-destacados.php" style="color: #212529 !important;">
                        <i class="bi bi-star me-2"></i>Productos Destacados
                    </a>
                    <a class="<?php echo getNavLinkClass('servicios.php', $current_page, $current_dir); ?> nav-link-sm" href="servicios.php" style="color: #212529 !important;">
                        <i class="bi bi-gear me-2"></i>Servicios
                    </a>
                    <a class="<?php echo getNavLinkClass('aliados.php', $current_page, $current_dir); ?> nav-link-sm" href="aliados.php" style="color: #212529 !important;">
                        <i class="bi bi-building-fill-check me-2"></i>Partners Globales
                    </a>
                    <a class="<?php echo getNavLinkClass('mision-vision.php', $current_page, $current_dir); ?> nav-link-sm" href="mision-vision.php" style="color: #212529 !important;">
                        <i class="bi bi-bullseye me-2"></i>Misión/Visión
                    </a>
                    <a class="<?php echo getNavLinkClass('categorias-destacadas.php', $current_page, $current_dir); ?> nav-link-sm" href="categorias-destacadas.php" style="color: #212529 !important;">
                        <i class="bi bi-folder me-2"></i>Categorías
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Catálogo -->
            <?php if (canSeeModule('catalogo', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('catalogo/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('catalogo/index.php', $base_path); ?>">
                    <i class="bi bi-box-seam me-2"></i>Catálogo
                </a>
                <?php 
                // Debug: verificar valores
                $is_catalogo = ($current_dir === 'catalogo');
                if (isset($_GET['debug_menu'])) {
                    error_log("CATALOGO DEBUG - current_dir: '$current_dir' | is_catalogo: " . ($is_catalogo ? 'true' : 'false'));
                }
                ?>
                <?php if ($is_catalogo): ?>
                <!-- SUBMENÚ CATÁLOGO - DEBERÍA SER VISIBLE -->
                <div class="ms-3 mt-1 catalogo-submenu" style="display: block !important; visibility: visible !important; opacity: 1 !important; height: auto !important; padding: 0.5rem 0 !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('catalogo/index.php', $base_path); ?>" style="display: block !important; visibility: visible !important; color: #212529 !important;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('productos/index.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('catalogo/productos/index.php', $base_path); ?>" style="display: block !important; visibility: visible !important; color: #212529 !important;">
                        <i class="bi bi-box me-2"></i>Productos
                    </a>
                    <a class="<?php echo getNavLinkClass('categorias.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('catalogo/categorias.php', $base_path); ?>" style="display: block !important; visibility: visible !important; color: #212529 !important;">
                        <i class="bi bi-folder me-2"></i>Categorías
                    </a>
                    <a class="<?php echo getNavLinkClass('marcas.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('catalogo/marcas.php', $base_path); ?>" style="display: block !important; visibility: visible !important; color: #212529 !important;">
                        <i class="bi bi-tags me-2"></i>Marcas
                    </a>
                </div>
                <?php else: ?>
                <!-- DEBUG: No se muestra submenú. current_dir='<?php echo htmlspecialchars($current_dir); ?>' ?> -->
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Blog -->
            <?php if (canSeeModule('blog', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('blog/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('blog/index.php', $base_path); ?>">
                    <i class="bi bi-newspaper me-2"></i>Blog
                </a>
                <?php if ($current_dir === 'blog'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php" style="color: #212529 !important;">
                        <i class="bi bi-list-ul me-2"></i>Artículos
                    </a>
                    <a class="<?php echo getNavLinkClass('create.php', $current_page, $current_dir); ?> nav-link-sm" href="create.php" style="color: #212529 !important;">
                        <i class="bi bi-plus-circle me-2"></i>Crear Artículo
                    </a>
                    <a class="<?php echo getNavLinkClass('categorias.php', $current_page, $current_dir); ?> nav-link-sm" href="categorias.php" style="color: #212529 !important;">
                        <i class="bi bi-folder me-2"></i>Categorías
                    </a>
                    <a class="<?php echo getNavLinkClass('comentarios.php', $current_page, $current_dir); ?> nav-link-sm" href="comentarios.php" style="color: #212529 !important;">
                        <i class="bi bi-chat-dots me-2"></i>Comentarios
                    </a>
                    <a class="<?php echo getNavLinkClass('image-manager.php', $current_page, $current_dir); ?> nav-link-sm" href="image-manager.php" style="color: #212529 !important;">
                        <i class="bi bi-images me-2"></i>Imágenes
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Proyectos -->
            <?php if (canSeeModule('proyectos', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('proyectos/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('proyectos/index.php', $base_path); ?>">
                    <i class="bi bi-folder me-2"></i>Proyectos
                </a>
                <?php if ($current_dir === 'proyectos'): ?>
                <!-- DEBUG: Submenú de proyectos debería mostrarse aquí -->
                <div class="ms-3 mt-1 proyectos-submenu" style="display: block !important; visibility: visible !important; opacity: 1 !important; height: auto !important; padding: 0.5rem 0 !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php" style="display: block !important;">
                        <i class="bi bi-list me-2"></i>Listado
                    </a>
                    <?php if (function_exists('hasPermission') && hasPermission($_SESSION['admin_user_id'] ?? 0, 'proyectos', 'crear')): ?>
                    <a class="<?php echo getNavLinkClass('create.php', $current_page, $current_dir); ?> nav-link-sm" href="create.php" style="display: block !important;">
                        <i class="bi bi-plus-circle me-2"></i>Crear
                    </a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <!-- DEBUG: current_dir no es 'proyectos', es: <?php echo htmlspecialchars($current_dir); ?> -->
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Contacto -->
            <?php if (canSeeModule('contacto', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('contacto/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('contacto/index.php', $base_path); ?>">
                    <i class="bi bi-chat-dots me-2"></i>Contacto
                </a>
                <?php if ($current_dir === 'contacto'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php" style="color: #212529 !important;">
                        <i class="bi bi-list me-2"></i>Listado
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Cotizaciones -->
            <?php if (canSeeModule('cotizaciones', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('cotizaciones/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('cotizaciones/index.php', $base_path); ?>">
                    <i class="bi bi-file-earmark-text me-2"></i>Cotizaciones
                </a>
                <?php if ($current_dir === 'cotizaciones'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php" style="color: #212529 !important;">
                        <i class="bi bi-list me-2"></i>Listado
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Newsletter -->
            <?php if (function_exists('hasPermission') && hasPermission($_SESSION['admin_user_id'] ?? 0, 'newsletter', 'ver')): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('newsletter-simple.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('newsletter-simple.php', $base_path); ?>">
                    <i class="bi bi-newspaper me-2"></i>Newsletter
                </a>
                <?php if ($current_dir === 'newsletter'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('import.php', $current_page, $current_dir); ?> nav-link-sm" href="import.php" style="color: #212529 !important;">
                        <i class="bi bi-upload me-2"></i>Importar CSV
                    </a>
                    <a class="<?php echo getNavLinkClass('export.php', $current_page, $current_dir); ?> nav-link-sm" href="export.php" style="color: #212529 !important;">
                        <i class="bi bi-download me-2"></i>Exportar CSV
                    </a>
                    <a class="<?php echo getNavLinkClass('plantillas.php', $current_page, $current_dir); ?> nav-link-sm" href="plantillas.php" style="color: #212529 !important;">
                        <i class="bi bi-file-earmark-code me-2"></i>Plantillas
                    </a>
                    <a class="<?php echo getNavLinkClass('config.php', $current_page, $current_dir); ?> nav-link-sm" href="config.php" style="color: #212529 !important;">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Analytics (Solo Admin) -->
            <?php if ($is_admin && function_exists('hasPermission') && hasPermission($_SESSION['admin_user_id'] ?? 0, 'analytics', 'ver')): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('analytics/dashboard.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('analytics/dashboard.php', $base_path); ?>">
                    <i class="bi bi-graph-up me-2"></i>Analytics
                </a>
                <?php if ($current_dir === 'analytics'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('dashboard.php', $current_page, $current_dir); ?> nav-link-sm" href="dashboard.php" style="color: #212529 !important;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('config.php', $current_page, $current_dir); ?> nav-link-sm" href="config.php" style="color: #212529 !important;">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Topbar Messages -->
            <?php if (canSeeModule('home', $user_permissions) || canSeeModule('configuracion', $user_permissions)): ?>
            <a class="<?php echo getNavLinkClass('topbar-messages.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('topbar-messages.php', $base_path); ?>">
                <i class="bi bi-megaphone me-2"></i>Mensajes Topbar
            </a>
            <?php endif; ?>
            
            <!-- Usuarios (Solo Admin) -->
            <?php if ($is_admin && canSeeModule('usuarios', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('usuarios.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('usuarios.php', $base_path); ?>">
                    <i class="bi bi-people-fill me-2"></i>Usuarios
                </a>
                <?php if ($current_dir === 'usuarios'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('logs.php', $current_page, $current_dir); ?> nav-link-sm" href="logs.php" style="color: #212529 !important;">
                        <i class="bi bi-journal-text me-2"></i>Logs de Auditoría
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- SEO (Solo Admin) -->
            <?php 
            // Verificar permisos SEO de forma segura
            $menu_debug_log("Verificando permisos SEO...");
            $can_see_seo = false;
            if ($is_admin) {
                // Si es admin, siempre puede ver SEO
                $can_see_seo = true;
                $menu_debug_log("Usuario es admin, puede ver SEO");
            } elseif (function_exists('hasPermission') && isset($_SESSION['admin_user_id'])) {
                // Si no es admin, verificar permisos (pero solo si la función existe y hay user_id)
                $menu_debug_log("Verificando permiso SEO con hasPermission...");
                try {
                    $can_see_seo = hasPermission($_SESSION['admin_user_id'], 'seo', 'ver');
                    $menu_debug_log("hasPermission retornó: " . ($can_see_seo ? 'true' : 'false'));
                } catch (Exception $e) {
                    $error_msg = "Error verificando permiso SEO en menu: " . $e->getMessage();
                    $menu_debug_log($error_msg);
                    error_log($error_msg);
                    $can_see_seo = false;
                } catch (Error $e) {
                    $error_msg = "Error fatal verificando permiso SEO en menu: " . $e->getMessage();
                    $menu_debug_log($error_msg);
                    error_log($error_msg);
                    $can_see_seo = false;
                }
            } else {
                $menu_debug_log("hasPermission no disponible o user_id no establecido");
            }
            ?>
            <?php if ($can_see_seo): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('seo/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('seo/index.php', $base_path); ?>">
                    <i class="bi bi-search me-2"></i>SEO & Metadatos
                </a>
                <?php if ($current_dir === 'seo'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php" style="color: #212529 !important;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('config.php', $current_page, $current_dir); ?> nav-link-sm" href="config.php" style="color: #212529 !important;">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </a>
                    <a class="<?php echo getNavLinkClass('redirects.php', $current_page, $current_dir); ?> nav-link-sm" href="redirects.php" style="color: #212529 !important;">
                        <i class="bi bi-arrow-left-right me-2"></i>Redirecciones
                    </a>
                    <a class="<?php echo getNavLinkClass('sitemap.php', $current_page, $current_dir); ?> nav-link-sm" href="sitemap.php" style="color: #212529 !important;">
                        <i class="bi bi-diagram-3 me-2"></i>Sitemap
                    </a>
                    <a class="<?php echo getNavLinkClass('robots.php', $current_page, $current_dir); ?> nav-link-sm" href="robots.php" style="color: #212529 !important;">
                        <i class="bi bi-shield-check me-2"></i>Robots.txt
                    </a>
                    <a class="<?php echo getNavLinkClass('schema.php', $current_page, $current_dir); ?> nav-link-sm" href="schema.php" style="color: #212529 !important;">
                        <i class="bi bi-code-square me-2"></i>Schema.org
                    </a>
                    <a class="<?php echo getNavLinkClass('metadatos.php', $current_page, $current_dir); ?> nav-link-sm" href="metadatos.php" style="color: #212529 !important;">
                        <i class="bi bi-tags me-2"></i>Metadatos
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Apariencia & Módulos (Solo Admin) -->
            <?php if ($is_admin && canSeeModule('apariencia', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('apariencia/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('apariencia/index.php', $base_path); ?>">
                    <i class="bi bi-palette me-2"></i>Apariencia & Módulos
                </a>
                <?php if ($current_dir === 'apariencia' || $current_dir === 'menu'): ?>
                <div class="ms-3 mt-1" style="display: block !important; visibility: visible !important;">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('apariencia/index.php', $base_path); ?>" style="color: #212529 !important;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('secciones.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('apariencia/secciones.php', $base_path); ?>" style="color: #212529 !important;">
                        <i class="bi bi-layout-text-window me-2"></i>Secciones
                    </a>
                    <a class="<?php echo getNavLinkClass('paginas.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('apariencia/paginas.php', $base_path); ?>" style="color: #212529 !important;">
                        <i class="bi bi-file-earmark-text me-2"></i>Páginas Estáticas
                    </a>
                    <a class="<?php echo getNavLinkClass('vista-previa.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('apariencia/vista-previa.php', $base_path); ?>" style="color: #212529 !important;">
                        <i class="bi bi-eye me-2"></i>Vista Previa
                    </a>
                    <a class="<?php echo getNavLinkClass('menu/index.php', $current_page, $current_dir); ?> nav-link-sm" href="<?php echo adminUrl('menu/index.php', $base_path); ?>" style="color: #212529 !important;">
                        <i class="bi bi-list-ul me-2"></i>Menú Principal
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Configuración (Solo Admin) -->
            <?php if ($is_admin && canSeeModule('configuracion', $user_permissions)): ?>
            <a class="<?php echo getNavLinkClass('configuracion/index.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('configuracion/index.php', $base_path); ?>">
                <i class="bi bi-gear me-2"></i>Configuración
            </a>
            <?php endif; ?>
            
            <a class="<?php echo getNavLinkClass('perfil.php', $current_page, $current_dir); ?>" href="<?php echo adminUrl('perfil.php', $base_path); ?>">
                <i class="bi bi-person-circle me-2"></i>Mi Perfil
            </a>
            
            <hr>
            
            <!-- Enlaces externos -->
            <a class="nav-link" href="<?php echo adminUrl('../blog.php', $base_path); ?>" target="_blank">
                <i class="bi bi-eye me-2"></i>Ver Blog
            </a>
            
            <a class="nav-link" href="<?php echo adminUrl('../index.php', $base_path); ?>">
                <i class="bi bi-house me-2"></i>Volver al Sitio
            </a>
            
            <a class="nav-link" href="<?php echo adminUrl('logout.php', $base_path); ?>">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
            </a>
        </nav>
    </div>
</div>

<style>
/* Variables CSS elegantes por defecto para todas las páginas del admin */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --success-color: #27ae60;
    --danger-color: #e74c3c;
    --border-radius: 8px;
    --shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Estilos adicionales para el menú */

/* Ancho del sidebar */
.admin-sidebar {
    min-width: 280px !important;
    max-width: 320px !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-right: 1px solid #e9ecef;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    overflow-x: hidden;
    overflow-y: auto;
    position: relative;
    z-index: 100;
}

/* Logo del admin */
.admin-logo {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    border: 1px solid #e9ecef;
    max-width: 100%;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

.logo-image {
    height: 40px;
    max-height: 40px;
    width: auto;
    max-width: 150px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    transition: transform 0.3s ease;
    flex-shrink: 0;
}

.logo-image:hover {
    transform: scale(1.05);
}

.logo-text {
    font-weight: 600;
    color: var(--dark-color);
    font-size: 1.1rem;
    margin-left: 0.5rem;
}

.nav-link-sm {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    margin-bottom: 0.25rem;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    color: #212529 !important; /* Color de texto oscuro para visibilidad */
    text-decoration: none !important;
}

.nav-link-sm:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    transform: translateX(3px);
    color: var(--primary-color) !important;
}

.nav-link-sm.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white !important;
    box-shadow: 0 2px 8px rgba(44, 62, 80, 0.2);
}

/* Asegurar que los submenús se muestren */
.nav-item .ms-3,
.nav-item > .ms-3,
div.nav-item div.ms-3 {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    margin-left: 1rem !important;
    margin-top: 0.5rem !important;
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
}

/* Forzar visualización de todos los submenús */
.admin-sidebar .nav-item div[class*="ms-3"],
.admin-sidebar .nav-item .proyectos-submenu,
.admin-sidebar .nav-item .catalogo-submenu {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
    padding: 0.5rem 0 !important;
}

/* Asegurar que los enlaces dentro de los submenús sean visibles */
.nav-item .ms-3 a,
.nav-item .proyectos-submenu a,
.nav-item .catalogo-submenu a {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    color: #212529 !important; /* Color de texto oscuro */
    text-decoration: none !important;
}

.nav-item .ms-3 a:hover,
.nav-item .proyectos-submenu a:hover,
.nav-item .catalogo-submenu a:hover {
    color: var(--primary-color) !important;
}

.user-info {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white !important;
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

/* Solo afectar elementos directos dentro de .user-info, no los enlaces del menú */
.user-info > div,
.user-info .fw-bold,
.user-info small,
.user-info i.bi-person-circle {
    color: white !important;
}

.user-info .fw-bold {
    font-size: 0.9rem;
    color: white !important;
}

.user-info small {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.9) !important;
}

.user-info i.bi-person-circle {
    color: white !important;
}

/* Enlaces del menú - Estilos consistentes para todas las páginas */
/* Estilo elegante: sin color de fondo por defecto, solo el activo tiene color */
.admin-sidebar .nav-link,
.admin-sidebar nav .nav-link,
.nav-link {
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    border-radius: var(--border-radius, 12px);
    transition: all 0.3s ease;
    background: transparent !important; /* Sin fondo por defecto - elegante */
    color: #212529 !important; /* Color oscuro explícito */
    text-decoration: none !important;
    display: flex !important;
    align-items: center;
    font-weight: 500;
    border: none !important;
}

.admin-sidebar .nav-link:hover,
.admin-sidebar nav .nav-link:hover,
.nav-link:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%) !important; /* Fondo gris claro al hover */
    transform: translateX(3px);
    color: var(--primary-color) !important;
}

.admin-sidebar .nav-link.active,
.admin-sidebar nav .nav-link.active,
.nav-link.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important; /* Solo el activo tiene color */
    color: white !important;
    box-shadow: 0 2px 8px rgba(44, 62, 80, 0.2);
}

.admin-sidebar .nav-link i,
.nav-link i {
    width: 20px;
    text-align: center;
    color: inherit !important; /* Heredar el color del enlace padre */
}

/* Asegurar que los enlaces del menú NO sean afectados por .user-info */
.admin-sidebar .nav-link,
.admin-sidebar .nav-link *:not(.user-info *):not(.user-info) {
    color: #212529 !important;
}

.admin-sidebar .nav-link.active,
.admin-sidebar .nav-link.active * {
    color: white !important;
}
</style>
<?php
// Finalizar logging del menú
if (isset($menu_debug_log)) {
    $menu_debug_log("=== FIN admin_menu.php ===");
}
?>
