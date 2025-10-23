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

// Obtener información del usuario actual
$current_user = [
    'nombre' => $_SESSION['admin_username'] ?? 'Administrador',
    'username' => $_SESSION['admin_username'] ?? 'admin'
];

// Determinar la página actual
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

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
?>

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 admin-sidebar p-0">
    <div class="p-3">
        <h5 class="mb-4">
            <i class="bi bi-shield-lock me-2"></i>Admin Panel
        </h5>
        
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
            <a class="<?php echo getNavLinkClassPages('index.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../index.php' : 'index.php'; ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            
            <!-- Blog -->
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
            
            <!-- Newsletter -->
            <a class="<?php echo getNavLinkClass('newsletter-subscriptions.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../newsletter-subscriptions.php' : 'newsletter-subscriptions.php'; ?>">
                <i class="bi bi-envelope me-2"></i>Cotización Simple
            </a>
            
            <a class="<?php echo getNavLinkClass('newsletter-simple.php', $current_page, $current_dir); ?>" href="<?php echo ($current_dir === 'blog') ? '../newsletter-simple.php' : 'newsletter-simple.php'; ?>">
                <i class="bi bi-newspaper me-2"></i>Newsletter Simple
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

<?php
// Función auxiliar para manejar las páginas principales del admin
function getNavLinkClassPages($page, $current_page, $current_dir) {
    $classes = ['nav-link'];
    
    // Si estamos en el directorio blog, solo activar si es la página exacta
    if ($current_dir === 'blog') {
        if ($page === 'index.php' && $current_page === 'index.php') {
            $classes[] = 'active';
        }
    } else {
        // Para páginas principales del admin
        if ($page === $current_page) {
            $classes[] = 'active';
        }
    }
    
    return implode(' ', $classes);
}
?>

<style>
/* Estilos adicionales para el menú */
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
</style>
