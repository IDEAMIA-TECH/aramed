<?php
/**
 * Navbar - Menú de navegación principal
 */
if (!defined('ARAMED_SITE')) die('Acceso directo no permitido');
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="<?php echo siteUrl(); ?>">
            <img src="<?php echo imageUrl('design/logo.png'); ?>" 
                 alt="<?php echo esc(SITE_NAME); ?>" 
                 height="50"
                 class="d-inline-block align-text-top">
        </a>
        
        <!-- Toggler para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menú -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link active" href="#home">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#catalogos">Catálogos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#proyectos">Proyectos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#aliados">Aliados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#blog">Blogs</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#contactModal">
                        Contáctanos
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Estilos temporales para el navbar */
.navbar {
    transition: all 0.3s ease;
}

.navbar-brand img {
    transition: all 0.3s ease;
}

.navbar .nav-link {
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: color 0.3s ease;
}

.navbar .nav-link:hover {
    color: var(--bs-primary) !important;
}

.navbar .nav-link.active {
    color: var(--bs-primary) !important;
}
</style>

