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

// Cargar funciones RBAC si existen
if (file_exists(__DIR__ . '/../../includes/rbac_functions.php')) {
    require_once __DIR__ . '/../../includes/rbac_functions.php';
}

// Obtener información del usuario actual
$current_user = [
    'nombre' => $_SESSION['admin_username'] ?? 'Administrador',
    'username' => $_SESSION['admin_username'] ?? 'admin',
    'id' => $_SESSION['admin_user_id'] ?? null,
    'rol' => $_SESSION['admin_rol'] ?? 'editor'
];

// Obtener permisos del usuario actual (si RBAC está disponible)
$user_permissions = [];
if (function_exists('getUserPermissions') && isset($current_user['id'])) {
    $user_permissions = getUserPermissions($current_user['id']);
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

// Función para determinar si un enlace está activo
function isActive($page, $current_page, $current_dir = null) {
    if ($current_dir === 'blog') {
        // Para páginas del blog
        return $page === $current_page;
    } else {
        // Para páginas principales del admin
        return $page === $current_page;
    }
}

// Función para generar clases CSS del enlace
function getNavLinkClass($page, $current_page, $current_dir = null) {
    $classes = ['nav-link'];
    if (isActive($page, $current_page, $current_dir)) {
        $classes[] = 'active';
    }
    return implode(' ', $classes);
}

// Determinar la página actual
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Sidebar -->
<div class="col-md-3 col-lg-3 admin-sidebar p-0">
    <div class="p-3" style="max-width: 100%; overflow-x: hidden;">
        <div class="admin-logo mb-4">
            <img src="<?php echo ($current_dir === 'blog') ? '../../assets/images/design/logo.png' : '../assets/images/design/logo.png'; ?>" 
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
            <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../index.php' : 'index.php'; ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            
            <!-- Home -->
            <?php if (canSeeModule('home', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('home/index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'home') ? '../home/index.php' : 'home/index.php'; ?>">
                    <i class="bi bi-house-door me-2"></i>Home
                </a>
                <?php if ($current_dir === 'home'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('banners.php', $current_page, $current_dir); ?> nav-link-sm" href="banners.php">
                        <i class="bi bi-image me-2"></i>Banners
                    </a>
                    <a class="<?php echo getNavLinkClass('productos-destacados.php', $current_page, $current_dir); ?> nav-link-sm" href="productos-destacados.php">
                        <i class="bi bi-star me-2"></i>Productos Destacados
                    </a>
                    <a class="<?php echo getNavLinkClass('servicios.php', $current_page, $current_dir); ?> nav-link-sm" href="servicios.php">
                        <i class="bi bi-gear me-2"></i>Servicios
                    </a>
                    <a class="<?php echo getNavLinkClass('mision-vision.php', $current_page, $current_dir); ?> nav-link-sm" href="mision-vision.php">
                        <i class="bi bi-bullseye me-2"></i>Misión/Visión
                    </a>
                    <a class="<?php echo getNavLinkClass('categorias-destacadas.php', $current_page, $current_dir); ?> nav-link-sm" href="categorias-destacadas.php">
                        <i class="bi bi-folder me-2"></i>Categorías
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Catálogo -->
            <?php if (canSeeModule('catalogo', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('catalogo/index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'catalogo') ? '../catalogo/index.php' : 'catalogo/index.php'; ?>">
                    <i class="bi bi-box-seam me-2"></i>Catálogo
                </a>
                <?php if ($current_dir === 'catalogo'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('productos/index.php', $current_page, $current_dir); ?> nav-link-sm" href="productos/index.php">
                        <i class="bi bi-box me-2"></i>Productos
                    </a>
                    <a class="<?php echo getNavLinkClass('categorias.php', $current_page, $current_dir); ?> nav-link-sm" href="categorias.php">
                        <i class="bi bi-folder me-2"></i>Categorías
                    </a>
                    <a class="<?php echo getNavLinkClass('marcas.php', $current_page, $current_dir); ?> nav-link-sm" href="marcas.php">
                        <i class="bi bi-tags me-2"></i>Marcas
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Blog -->
            <?php if (canSeeModule('blog', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('blog/index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../blog/index.php' : 'blog/index.php'; ?>">
                    <i class="bi bi-newspaper me-2"></i>Blog
                </a>
                <?php if ($current_dir === 'blog'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php">
                        <i class="bi bi-list-ul me-2"></i>Artículos
                    </a>
                    <a class="<?php echo getNavLinkClass('create.php', $current_page, $current_dir); ?> nav-link-sm" href="create.php">
                        <i class="bi bi-plus-circle me-2"></i>Crear Artículo
                    </a>
                    <a class="<?php echo getNavLinkClass('categorias.php', $current_page, $current_dir); ?> nav-link-sm" href="categorias.php">
                        <i class="bi bi-folder me-2"></i>Categorías
                    </a>
                    <a class="<?php echo getNavLinkClass('comentarios.php', $current_page, $current_dir); ?> nav-link-sm" href="comentarios.php">
                        <i class="bi bi-chat-dots me-2"></i>Comentarios
                    </a>
                    <a class="<?php echo getNavLinkClass('image-manager.php', $current_page, $current_dir); ?> nav-link-sm" href="image-manager.php">
                        <i class="bi bi-images me-2"></i>Imágenes
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Proyectos -->
            <?php if (canSeeModule('proyectos', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog' || $current_dir === 'catalogo' || $current_dir === 'home' || $current_dir === 'contacto') ? '../proyectos/index.php' : 'proyectos/index.php'; ?>">
                    <i class="bi bi-folder me-2"></i>Proyectos
                </a>
                <?php if ($current_dir === 'proyectos'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php">
                        <i class="bi bi-list me-2"></i>Listado
                    </a>
                    <?php if (hasPermission('proyectos', 'crear')): ?>
                    <a class="<?php echo getNavLinkClass('create.php', $current_page, $current_dir); ?> nav-link-sm" href="create.php">
                        <i class="bi bi-plus-circle me-2"></i>Crear
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Contacto -->
            <?php if (canSeeModule('contacto', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog' || $current_dir === 'catalogo' || $current_dir === 'home' || $current_dir === 'proyectos') ? '../contacto/index.php' : 'contacto/index.php'; ?>">
                    <i class="bi bi-chat-dots me-2"></i>Contacto
                </a>
                <?php if ($current_dir === 'contacto'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php">
                        <i class="bi bi-list me-2"></i>Listado
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Cotizaciones -->
            <?php if (canSeeModule('cotizaciones', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog' || $current_dir === 'catalogo' || $current_dir === 'home' || $current_dir === 'contacto') ? '../cotizaciones/index.php' : 'cotizaciones/index.php'; ?>">
                    <i class="bi bi-file-earmark-text me-2"></i>Cotizaciones
                </a>
                <?php if ($current_dir === 'cotizaciones'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php">
                        <i class="bi bi-list me-2"></i>Listado
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Newsletter -->
            <?php if (hasPermission('newsletter', 'ver')): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('newsletter-simple.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog' || $current_dir === 'newsletter') ? '../newsletter-simple.php' : 'newsletter-simple.php'; ?>">
                    <i class="bi bi-newspaper me-2"></i>Newsletter
                </a>
                <?php if ($current_dir === 'newsletter'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('import.php', $current_page, $current_dir); ?> nav-link-sm" href="import.php">
                        <i class="bi bi-upload me-2"></i>Importar CSV
                    </a>
                    <a class="<?php echo getNavLinkClass('export.php', $current_page, $current_dir); ?> nav-link-sm" href="export.php">
                        <i class="bi bi-download me-2"></i>Exportar CSV
                    </a>
                    <a class="<?php echo getNavLinkClass('plantillas.php', $current_page, $current_dir); ?> nav-link-sm" href="plantillas.php">
                        <i class="bi bi-file-earmark-code me-2"></i>Plantillas
                    </a>
                    <a class="<?php echo getNavLinkClass('config.php', $current_page, $current_dir); ?> nav-link-sm" href="config.php">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Analytics -->
            <?php if (hasPermission('analytics', 'ver')): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('dashboard.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog' || $current_dir === 'newsletter' || $current_dir === 'analytics') ? '../analytics/dashboard.php' : 'analytics/dashboard.php'; ?>">
                    <i class="bi bi-graph-up me-2"></i>Analytics
                </a>
                <?php if ($current_dir === 'analytics'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('dashboard.php', $current_page, $current_dir); ?> nav-link-sm" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('config.php', $current_page, $current_dir); ?> nav-link-sm" href="config.php">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Topbar Messages -->
            <?php if (canSeeModule('home', $user_permissions) || canSeeModule('configuracion', $user_permissions)): ?>
            <a class="<?php echo getNavLinkClass('topbar-messages.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../topbar-messages.php' : 'topbar-messages.php'; ?>">
                <i class="bi bi-megaphone me-2"></i>Mensajes Topbar
            </a>
            <?php endif; ?>
            
            <!-- Usuarios -->
            <?php if (canSeeModule('usuarios', $user_permissions)): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('usuarios.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../usuarios.php' : 'usuarios.php'; ?>">
                    <i class="bi bi-people-fill me-2"></i>Usuarios
                </a>
                <?php if ($current_dir === 'usuarios'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('logs.php', $current_page, $current_dir); ?> nav-link-sm" href="logs.php">
                        <i class="bi bi-journal-text me-2"></i>Logs de Auditoría
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- SEO -->
            <?php if (hasPermission('seo', 'ver')): ?>
            <div class="nav-item">
                <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog' || $current_dir === 'catalogo' || $current_dir === 'home' || $current_dir === 'contacto' || $current_dir === 'cotizaciones' || $current_dir === 'proyectos') ? '../seo/index.php' : 'seo/index.php'; ?>">
                    <i class="bi bi-search me-2"></i>SEO & Metadatos
                </a>
                <?php if ($current_dir === 'seo'): ?>
                <div class="ms-3 mt-1">
                    <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?> nav-link-sm" href="index.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="<?php echo getNavLinkClass('config.php', $current_page, $current_dir); ?> nav-link-sm" href="config.php">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </a>
                    <a class="<?php echo getNavLinkClass('redirects.php', $current_page, $current_dir); ?> nav-link-sm" href="redirects.php">
                        <i class="bi bi-arrow-left-right me-2"></i>Redirecciones
                    </a>
                    <a class="<?php echo getNavLinkClass('sitemap.php', $current_page, $current_dir); ?> nav-link-sm" href="sitemap.php">
                        <i class="bi bi-diagram-3 me-2"></i>Sitemap
                    </a>
                    <a class="<?php echo getNavLinkClass('robots.php', $current_page, $current_dir); ?> nav-link-sm" href="robots.php">
                        <i class="bi bi-shield-check me-2"></i>Robots.txt
                    </a>
                    <a class="<?php echo getNavLinkClass('schema.php', $current_page, $current_dir); ?> nav-link-sm" href="schema.php">
                        <i class="bi bi-code-square me-2"></i>Schema.org
                    </a>
                    <a class="<?php echo getNavLinkClass('metadatos.php', $current_page, $current_dir); ?> nav-link-sm" href="metadatos.php">
                        <i class="bi bi-tags me-2"></i>Metadatos
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Configuración -->
            <?php if (canSeeModule('configuracion', $user_permissions)): ?>
            <a class="<?php echo getNavLinkClass('index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog' || $current_dir === 'catalogo' || $current_dir === 'home' || $current_dir === 'contacto' || $current_dir === 'cotizaciones' || $current_dir === 'proyectos' || $current_dir === 'seo') ? '../configuracion/index.php' : 'configuracion/index.php'; ?>">
                <i class="bi bi-gear me-2"></i>Configuración
            </a>
            <?php endif; ?>
            
        <a class="<?php echo getNavLinkClass('perfil.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../perfil.php' : 'perfil.php'; ?>">
            <i class="bi bi-person-circle me-2"></i>Mi Perfil
        </a>
            
            <hr>
            
            <!-- Enlaces externos -->
            <a class="nav-link" href="<?php echo ($current_dir === 'blog') ? '../blog.php' : '../blog.php'; ?>" target="_blank">
                <i class="bi bi-eye me-2"></i>Ver Blog
            </a>
            
            <a class="nav-link" href="<?php echo ($current_dir === 'blog') ? '../index.php' : '../index.php'; ?>">
                <i class="bi bi-house me-2"></i>Volver al Sitio
            </a>
            
            <a class="nav-link" href="<?php echo ($current_dir === 'blog') ? '../logout.php' : 'logout.php'; ?>">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
            </a>
        </nav>
    </div>
</div>


<style>
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
}

.nav-link-sm:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    transform: translateX(3px);
}

.nav-link-sm.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(0, 102, 204, 0.2);
}

.user-info {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
    color: white;
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.user-info .fw-bold {
    font-size: 0.9rem;
}

.user-info small {
    font-size: 0.75rem;
}

/* Enlaces del menú */
.nav-link {
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
    color: var(--dark-color);
    text-decoration: none;
    display: flex;
    align-items: center;
    font-weight: 500;
}

.nav-link:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    transform: translateX(3px);
    color: var(--primary-color);
}

.nav-link.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(0, 102, 204, 0.2);
}

.nav-link i {
    width: 20px;
    text-align: center;
}
</style>
