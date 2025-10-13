<?php
/**
 * Footer - Pie de página
 */
if (!defined('ARAMED_SITE')) die('Acceso directo no permitido');
?>

<footer class="footer bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            
            <!-- Columna 1: Logo y descripción -->
            <div class="col-lg-4 col-md-6">
                <img src="<?php echo imageUrl('design/logo.png'); ?>" 
                     alt="<?php echo esc(SITE_NAME); ?>" 
                     height="50" 
                     class="mb-3"
                     style="filter: brightness(0) invert(1);">
                <p class="text-white-50 small">
                    <?php echo esc(SITE_DESCRIPTION); ?>
                </p>
            </div>
            
            <!-- Columna 2: Menú rápido -->
            <div class="col-lg-2 col-md-6">
                <h5 class="mb-3">Menú</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#home" class="text-white-50 text-decoration-none">Inicio</a></li>
                    <li class="mb-2"><a href="#catalogos" class="text-white-50 text-decoration-none">Catálogos</a></li>
                    <li class="mb-2"><a href="#proyectos" class="text-white-50 text-decoration-none">Proyectos</a></li>
                    <li class="mb-2"><a href="#aliados" class="text-white-50 text-decoration-none">Aliados</a></li>
                    <li class="mb-2"><a href="#blog" class="text-white-50 text-decoration-none">Blogs</a></li>
                </ul>
            </div>
            
            <!-- Columna 3: Horarios -->
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-3">Horarios</h5>
                <p class="text-white-50 small mb-2">
                    <i class="bi bi-clock me-2"></i>
                    <?php echo esc(SCHEDULE_WEEKDAY); ?>
                </p>
                <p class="text-white-50 small">
                    <i class="bi bi-clock me-2"></i>
                    <?php echo esc(SCHEDULE_SATURDAY); ?>
                </p>
            </div>
            
            <!-- Columna 4: Contacto -->
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-3">Contacto</h5>
                <p class="text-white-50 small mb-2">
                    <i class="bi bi-envelope me-2"></i>
                    <a href="mailto:<?php echo esc(CONTACT_EMAIL); ?>" class="text-white-50 text-decoration-none">
                        <?php echo esc(CONTACT_EMAIL); ?>
                    </a>
                </p>
                <p class="text-white-50 small mb-3">
                    <i class="bi bi-telephone me-2"></i>
                    <a href="tel:<?php echo esc(PHONE_FORMATTED); ?>" class="text-white-50 text-decoration-none">
                        <?php echo esc(PHONE_MAIN); ?>
                    </a>
                </p>
                
                <!-- Redes sociales -->
                <div class="social-links">
                    <a href="<?php echo esc(SOCIAL_LINKEDIN); ?>" target="_blank" rel="noopener noreferrer" class="text-white me-3" title="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    <a href="<?php echo esc(SOCIAL_FACEBOOK); ?>" target="_blank" rel="noopener noreferrer" class="text-white me-3" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="<?php echo esc(SOCIAL_INSTAGRAM); ?>" target="_blank" rel="noopener noreferrer" class="text-white me-3" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="<?php echo esc(SOCIAL_TWITTER); ?>" target="_blank" rel="noopener noreferrer" class="text-white" title="Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                </div>
            </div>
            
        </div>
        
        <!-- Línea divisoria -->
        <hr class="my-4 bg-white opacity-25">
        
        <!-- Copyright y legal -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-white-50 small mb-0">
                    © <?php echo date('Y'); ?> <?php echo esc(SITE_NAME); ?>. Todos los derechos reservados.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="#" class="text-white-50 small text-decoration-none me-3">Aviso de Privacidad</a>
                <a href="#" class="text-white-50 small text-decoration-none">Términos de Uso</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Icons (CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* Estilos temporales para el footer */
.footer {
    margin-top: auto;
}

.footer a:hover {
    color: #ffffff !important;
}

.social-links a {
    font-size: 1.5rem;
    transition: transform 0.3s ease, color 0.3s ease;
}

.social-links a:hover {
    transform: translateY(-3px);
    color: var(--bs-primary) !important;
}
</style>

