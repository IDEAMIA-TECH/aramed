<?php
/**
 * Navbar - Menú de navegación principal mejorado
 */
if (!defined('ARAMED_SITE')) die('Acceso directo no permitido');

// Iniciar sesión si no está iniciada (para el carrito)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar funciones del carrito si existe
$cart_count = 0;
if (file_exists(__DIR__ . '/cart_functions.php')) {
    require_once __DIR__ . '/cart_functions.php';
    $cart_count = getCartCount();
}

// Detectar página actual
$current_page = basename($_SERVER['PHP_SELF']);
$current_section = '';

// Determinar sección activa basada en la página actual
switch ($current_page) {
    case 'index.php':
        $current_section = 'home';
        break;
    case 'catalogo.php':
        $current_section = 'catalogo';
        break;
    case 'blog.php':
    case 'blog-detalle.php':
        $current_section = 'blog';
        break;
    case 'proyectos.php':
    case 'proyecto.php':
        $current_section = 'proyectos';
        break;
    default:
        $current_section = 'home';
}

// Configurar menú de navegación
$nav_items = [
    ['label' => 'Inicio', 'href' => siteUrl(), 'icon' => 'house', 'section' => 'home'],
    ['label' => 'Catálogo', 'href' => siteUrl('catalogo.php'), 'icon' => 'grid-3x3-gap', 'section' => 'catalogo'],
    ['label' => 'Blog', 'href' => siteUrl('blog.php'), 'icon' => 'newspaper', 'section' => 'blog'],
    ['label' => 'Proyectos', 'href' => siteUrl('proyectos.php'), 'icon' => 'folder', 'section' => 'proyectos'],
    ['label' => 'Aliados', 'href' => siteUrl() . '#aliados', 'icon' => 'people', 'section' => 'aliados'],
];

// Función para generar enlaces que funcionen en todas las páginas
function getPageLink($href, $current_page) {
    // Si es un enlace con ancla (#) y no estamos en index.php, redirigir a index.php con la ancla
    if (strpos($href, '#') !== false && $current_page !== 'index.php') {
        // Si el href ya contiene la URL completa, solo devolverlo
        if (strpos($href, 'http') === 0 || strpos($href, siteUrl()) === 0) {
            return $href;
        }
        // Si es solo una ancla, agregar la URL base
        return siteUrl() . $href;
    }
    return $href;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top" id="mainNavbar">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="<?php echo siteUrl(); ?>" aria-label="<?php echo esc(SITE_NAME); ?> - Inicio">
            <img src="<?php echo imageUrl('design/logo.png'); ?>" 
                 alt="<?php echo esc(SITE_NAME); ?>" 
                 height="50"
                 width="auto"
                 class="navbar-logo"
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%2250%22%3E%3Ctext x=%2210%22 y=%2235%22 font-family=%22Arial%22 font-size=%2224%22 font-weight=%22bold%22 fill=%22%230066CC%22%3EAramed%3C/text%3E%3C/svg%3E';">
        </a>
        
        <!-- Toggler para móvil con animación -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menú de navegación -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Divider en móvil -->
            <hr class="d-lg-none my-3">
            
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php foreach ($nav_items as $item): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_section === $item['section']) ? 'active' : ''; ?>" 
                       href="<?php echo esc(getPageLink($item['href'], $current_page)); ?>"
                       <?php if (!empty($item['icon'])): ?>
                       data-icon="<?php echo esc($item['icon']); ?>"
                       <?php endif; ?>>
                        <?php if (!empty($item['icon'])): ?>
                        <i class="bi bi-<?php echo esc($item['icon']); ?> d-lg-none me-2"></i>
                        <?php endif; ?>
                        <?php echo esc($item['label']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
                
                <!-- Divider antes del CTA en móvil -->
                <li class="d-lg-none">
                    <hr class="my-3">
                </li>
                
                <!-- Carrito de Cotización -->
                <li class="nav-item ms-lg-3">
                    <a href="<?php echo siteUrl('cotizacion.php'); ?>" class="btn btn-outline-primary px-3 py-2 position-relative">
                        <i class="bi bi-cart me-2"></i>
                        <span class="d-none d-lg-inline">Cotización</span>
                        <?php if ($cart_count > 0): ?>
                        <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $cart_count; ?>
                        </span>
                        <?php else: ?>
                        <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                            0
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- CTA Button -->
                <li class="nav-item ms-lg-2">
                    <a href="<?php echo getPageLink('#newsletter', $current_page); ?>" class="btn btn-primary px-4 py-2 w-100 w-lg-auto shadow-sm">
                        <i class="bi bi-envelope me-2"></i>
                        Contáctanos
                    </a>
                </li>
            </ul>
            
            <!-- Redes sociales en móvil -->
            <div class="navbar-social d-lg-none mt-4 pt-3 border-top text-center">
                <?php
                // Obtener configuración de redes sociales desde BD, con fallback a constantes
                $empresa_linkedin = function_exists('getConfig') ? getConfig('empresa_linkedin', SOCIAL_LINKEDIN) : SOCIAL_LINKEDIN;
                $empresa_facebook = function_exists('getConfig') ? getConfig('empresa_facebook', SOCIAL_FACEBOOK) : SOCIAL_FACEBOOK;
                $empresa_instagram = function_exists('getConfig') ? getConfig('empresa_instagram', SOCIAL_INSTAGRAM) : SOCIAL_INSTAGRAM;
                $empresa_twitter = function_exists('getConfig') ? getConfig('empresa_twitter', SOCIAL_TWITTER) : SOCIAL_TWITTER;
                ?>
                <p class="small text-muted mb-2">Síguenos en:</p>
                <div class="social-links d-flex justify-content-center gap-3">
                    <?php if (!empty($empresa_linkedin)): ?>
                    <a href="<?php echo esc($empresa_linkedin); ?>" target="_blank" rel="noopener noreferrer" class="text-primary" title="LinkedIn">
                        <i class="bi bi-linkedin fs-5"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($empresa_facebook)): ?>
                    <a href="<?php echo esc($empresa_facebook); ?>" target="_blank" rel="noopener noreferrer" class="text-primary" title="Facebook">
                        <i class="bi bi-facebook fs-5"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($empresa_instagram)): ?>
                    <a href="<?php echo esc($empresa_instagram); ?>" target="_blank" rel="noopener noreferrer" class="text-primary" title="Instagram">
                        <i class="bi bi-instagram fs-5"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($empresa_twitter)): ?>
                    <a href="<?php echo esc($empresa_twitter); ?>" target="_blank" rel="noopener noreferrer" class="text-primary" title="Twitter">
                        <i class="bi bi-twitter fs-5"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
/* ========================================
   NAVBAR STYLES
   ======================================== */

.navbar {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.75rem 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Navbar scrolled state */
.navbar.scrolled {
    padding: 0.5rem 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.navbar.scrolled .navbar-logo {
    height: 40px;
}

/* Logo */
.navbar-brand {
    transition: all 0.3s ease;
}

.navbar-logo {
    height: 50px;
    width: auto;
    transition: all 0.3s ease;
    max-width: 200px;
    object-fit: contain;
}

.navbar-brand-text {
    font-size: 1.1rem;
    letter-spacing: -0.5px;
    transition: opacity 0.3s ease;
}

/* Contact info */
.navbar-contact a {
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

.navbar-contact a:hover {
    color: var(--bs-primary) !important;
}

/* Nav links */
.navbar-nav .nav-link {
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    position: relative;
    color: var(--color-gray-700);
}

.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--bs-primary);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
    color: var(--bs-primary) !important;
}

.navbar-nav .nav-link:hover::after,
.navbar-nav .nav-link.active::after {
    width: 60%;
}

/* CTA Button */
.navbar .btn-primary {
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.navbar .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 102, 204, 0.3) !important;
}

/* Toggler */
.navbar-toggler {
    padding: 0.5rem;
    font-size: 1.25rem;
}

.navbar-toggler:focus {
    box-shadow: none;
}

.navbar-toggler-icon {
    width: 1.5em;
    height: 1.5em;
}

/* Mobile menu */
@media (max-width: 991.98px) {
    .navbar-collapse {
        margin-top: 1rem;
        padding: 1rem 0;
    }
    
    .navbar-nav .nav-item {
        margin: 0.25rem 0;
    }
    
    .navbar-nav .nav-link {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
    }
    
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        background-color: rgba(0, 102, 204, 0.08);
    }
    
    .navbar-nav .nav-link::after {
        display: none;
    }
    
    .navbar .btn-primary {
        margin-top: 0.5rem;
        border-radius: 8px;
    }
    
    .navbar-social {
        animation: fadeInUp 0.4s ease;
    }
}

/* Responsive logo */
@media (max-width: 575.98px) {
    .navbar-logo {
        height: 40px;
    }
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover effects for social */
.navbar-social .social-links a {
    transition: all 0.3s ease;
}

.navbar-social .social-links a:hover {
    transform: translateY(-3px);
}
</style>

<script>
// Navbar scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('mainNavbar');
    let lastScrollTop = 0;
    let scrollThreshold = 50;
    
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Add/remove scrolled class
        if (scrollTop > scrollThreshold) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
    });
});
</script>

