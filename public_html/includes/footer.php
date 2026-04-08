<?php
/**
 * Footer - Pie de página mejorado
 */
if (!defined('ARAMED_SITE')) die('Acceso directo no permitido');
?>

<footer class="footer bg-gradient-dark text-white">
    <!-- Newsletter Section -->
    <div class="footer-newsletter bg-primary py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <h5 class="mb-1 fw-bold">¿Quieres recibir nuestras novedades?</h5>
                    <p class="mb-0 small text-white-75">Suscríbete a nuestro boletín informativo</p>
                </div>
                <div class="col-lg-6">
                    <!-- Mensajes de respuesta -->
                    <div id="footer-newsletter-success" class="alert alert-success d-none mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>¡Gracias!</strong> <span id="footer-newsletter-success-message"></span>
                    </div>
                    
                    <div id="footer-newsletter-error" class="alert alert-danger d-none mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Error:</strong> <span id="footer-newsletter-error-message"></span>
                    </div>
                    
                    <form class="newsletter-form d-flex gap-2 flex-wrap" id="footerNewsletterForm" action="<?php echo esc(siteUrl('includes/newsletter_simple_handler.php')); ?>" method="POST" data-recaptcha="<?php echo (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && !empty(RECAPTCHA_SITE_KEY)) ? '1' : '0'; ?>">
                        <input type="text" name="footer_company_fax" id="footer_company_fax" value="" tabindex="-1" autocomplete="off" class="position-absolute" style="left:-9999px;width:1px;height:1px;opacity:0;" aria-hidden="true">
                        <input type="hidden" name="form_timestamp" id="footer_form_timestamp" value="<?php echo (int) time(); ?>">
                        <input type="email" 
                               class="form-control" 
                               name="email"
                               id="footer-newsletter-email"
                               placeholder="Tu correo electrónico" 
                               required 
                               aria-label="Email">
                        <input type="hidden" name="source" value="footer">
                        <button type="submit" class="btn btn-light text-primary fw-semibold px-4 flex-shrink-0" id="footer-newsletter-submit">
                            Suscribirse
                        </button>
                        <button type="button" class="btn btn-light text-primary fw-semibold px-4 flex-shrink-0 d-none" id="footer-newsletter-loading" disabled>
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Suscribiendo...
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Footer Content -->
    <div class="footer-main py-5">
        <div class="container">
            <div class="row g-4">
                
                <!-- Columna 1: Sobre Nosotros -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-about">
                        <img src="<?php echo imageUrl('design/logo-obscuro.png'); ?>" 
                             alt="<?php echo esc(SITE_NAME); ?>" 
                             height="50" 
                             class="mb-3 footer-logo"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%2250%22%3E%3Ctext x=%2210%22 y=%2235%22 font-family=%22Arial%22 font-size=%2224%22 font-weight=%22bold%22 fill=%22%23ffffff%22%3EAramed%3C/text%3E%3C/svg%3E';">
                        <p class="text-white-75 mb-3">
                            <?php echo esc(function_exists('getConfig') ? getConfig('empresa_descripcion', SITE_DESCRIPTION) : SITE_DESCRIPTION); ?>
                        </p>
                        <div class="footer-certifications">
                            <p class="small text-white-50 mb-2"><i class="bi bi-award-fill text-primary me-2"></i>+20 años de experiencia</p>
                            <p class="small text-white-50 mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Distribuidor autorizado</p>
                        </div>
                    </div>
                </div>
                
                <!-- Columna 2: Enlaces Rápidos -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title mb-3">Enlaces Rápidos</h6>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#home">Inicio</a></li>
                        <li><a href="#catalogos">Catálogos</a></li>
                        <li><a href="#proyectos">Proyectos</a></li>
                        <li><a href="#aliados">Aliados</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#contacto">Contacto</a></li>
                    </ul>
                </div>
                
                <!-- Columna 3: Servicios -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title mb-3">Nuestros Servicios</h6>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#servicios">Desarrollo de Áreas</a></li>
                        <li><a href="#servicios">Mantenimiento Preventivo</a></li>
                        <li><a href="#servicios">Capacitación</a></li>
                        <li><a href="#servicios">Consultoría</a></li>
                        <li><a href="#servicios">Soporte Técnico</a></li>
                    </ul>
                    
                    <h6 class="footer-title mb-3 mt-4">Horario</h6>
                    <?php
                    $footer_horario_semana = function_exists('getConfig') ? getConfig('empresa_horario_semana', defined('SCHEDULE_WEEKDAY') ? SCHEDULE_WEEKDAY : 'Lun - Vie: 9:00 - 19:00') : (defined('SCHEDULE_WEEKDAY') ? SCHEDULE_WEEKDAY : 'Lun - Vie: 9:00 - 19:00');
                    $footer_horario_sabado = function_exists('getConfig') ? getConfig('empresa_horario_sabado', defined('SCHEDULE_SATURDAY') ? SCHEDULE_SATURDAY : 'Sábados: 10:00 - 14:00') : (defined('SCHEDULE_SATURDAY') ? SCHEDULE_SATURDAY : 'Sábados: 10:00 - 14:00');
                    ?>
                    <?php if (!empty($footer_horario_semana)): ?>
                    <p class="small text-white-75 mb-1">
                        <i class="bi bi-clock text-primary me-2"></i>
                        <?php echo esc($footer_horario_semana); ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($footer_horario_sabado)): ?>
                    <p class="small text-white-75 mb-0">
                        <i class="bi bi-clock text-primary me-2"></i>
                        <?php echo esc($footer_horario_sabado); ?>
                    </p>
                    <?php endif; ?>
                </div>
                
                <!-- Columna 4: Contacto -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title mb-3">Contacto</h6>
                    
                    <div class="footer-contact mb-3">
                        <?php
                        // Obtener configuración de contacto desde BD, con fallback a constantes
                        $empresa_telefono = function_exists('getConfig') ? getConfig('empresa_telefono', PHONE_MAIN) : PHONE_MAIN;
                        $empresa_email = function_exists('getConfig') ? getConfig('empresa_email', CONTACT_EMAIL) : CONTACT_EMAIL;
                        ?>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone-fill text-primary me-2"></i>
                            <a href="tel:<?php echo esc(preg_replace('/[^0-9+]/', '', $empresa_telefono)); ?>" class="text-white-75 text-decoration-none small">
                                <?php echo esc($empresa_telefono); ?>
                            </a>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                            <a href="mailto:<?php echo esc($empresa_email); ?>" class="text-white-75 text-decoration-none small">
                                <?php echo esc($empresa_email); ?>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Redes Sociales -->
                    <div class="footer-social text-center">
                        <?php
                        // Obtener configuración de redes sociales desde BD, con fallback a constantes
                        $empresa_linkedin = function_exists('getConfig') ? getConfig('empresa_linkedin', SOCIAL_LINKEDIN) : SOCIAL_LINKEDIN;
                        $empresa_facebook = function_exists('getConfig') ? getConfig('empresa_facebook', SOCIAL_FACEBOOK) : SOCIAL_FACEBOOK;
                        $empresa_instagram = function_exists('getConfig') ? getConfig('empresa_instagram', SOCIAL_INSTAGRAM) : SOCIAL_INSTAGRAM;
                        $empresa_twitter = function_exists('getConfig') ? getConfig('empresa_twitter', SOCIAL_TWITTER) : SOCIAL_TWITTER;
                        ?>
                        <p class="small text-white-50 mb-2">Síguenos:</p>
                        <div class="social-links d-flex justify-content-center gap-2">
                            <?php if (!empty($empresa_linkedin)): ?>
                            <a href="<?php echo esc($empresa_linkedin); ?>" target="_blank" rel="noopener noreferrer" class="social-link" title="LinkedIn" aria-label="LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($empresa_facebook)): ?>
                            <a href="<?php echo esc($empresa_facebook); ?>" target="_blank" rel="noopener noreferrer" class="social-link" title="Facebook" aria-label="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($empresa_instagram)): ?>
                            <a href="<?php echo esc($empresa_instagram); ?>" target="_blank" rel="noopener noreferrer" class="social-link" title="Instagram" aria-label="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($empresa_twitter)): ?>
                            <a href="<?php echo esc($empresa_twitter); ?>" target="_blank" rel="noopener noreferrer" class="social-link" title="Twitter/X" aria-label="Twitter/X">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="footer-bottom py-3 border-top border-white border-opacity-10">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <p class="small text-white-50 mb-0">
                        © <?php echo date('Y'); ?> <?php echo esc(SITE_NAME); ?>. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="<?php echo siteUrl('privacidad.php'); ?>" class="footer-legal-link me-3">Aviso de Privacidad</a>
                    <a href="<?php echo siteUrl('terminos.php'); ?>" class="footer-legal-link me-3">Términos de Uso</a>
                    <a href="<?php echo siteUrl('cookies.php'); ?>" class="footer-legal-link">Política de Cookies</a>
                </div>
            </div>
            
            <!-- Developer Credit -->
            <div class="row mt-2">
                <div class="col-12 text-center">
                    <p class="small text-white-50 mb-0">
                        Desarrollado por <a href="https://ideamia.com.mx" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none fw-semibold">IDEAMIA – Tech</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* ========================================
   FOOTER STYLES
   ======================================== */

.footer {
    background: linear-gradient(180deg, #1a252f 0%, #0f1419 100%);
    margin-top: auto;
}

/* Newsletter Section */
.footer-newsletter {
    background: var(--bs-primary);
}

.footer-newsletter .form-control {
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
}

.footer-newsletter .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
}

.footer-newsletter .btn-light {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.footer-newsletter .btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Logo */
.footer-logo {
    max-width: 200px;
    height: auto;
}

/* Titles */
.footer-title {
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    padding-bottom: 0.5rem;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background: var(--bs-primary);
}

/* Links */
.footer-links li {
    margin-bottom: 0.75rem;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-links a:hover {
    color: #ffffff;
    padding-left: 8px;
}

.footer-links a::before {
    content: '→';
    opacity: 0;
    margin-right: -8px;
    transition: all 0.3s ease;
}

.footer-links a:hover::before {
    opacity: 1;
    margin-right: 8px;
}

/* Contact */
.footer-contact a {
    transition: color 0.3s ease;
}

.footer-contact a:hover {
    color: #ffffff !important;
}

/* Social Links */
.social-links {
    display: flex;
    gap: 0.5rem;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.25rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-link:hover {
    background: var(--bs-primary);
    color: #ffffff;
    transform: translateY(-4px);
}

/* Legal Links */
.footer-legal-link {
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

.footer-legal-link:hover {
    color: #ffffff;
}

/* Responsive */
@media (max-width: 767.98px) {
    .footer-newsletter .form-control {
        font-size: 0.875rem;
    }
    
    .footer-newsletter .btn-light {
        font-size: 0.875rem;
        padding: 0.625rem 1rem;
    }
    
    .footer-main {
        padding: 2rem 0 !important;
    }
    
    .footer-title {
        margin-top: 1.5rem;
    }
    
    .footer-title:first-of-type {
        margin-top: 0;
    }
}

/* Text colors */
.text-white-75 {
    color: rgba(255, 255, 255, 0.75);
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.5);
}

/* Animations */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.footer-certifications i {
    animation: pulse 2s infinite;
}
</style>

<?php if (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && !empty(RECAPTCHA_SITE_KEY)): ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc(RECAPTCHA_SITE_KEY); ?>"></script>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const footerNewsletterForm = document.getElementById('footerNewsletterForm');
    if (!footerNewsletterForm) {
        return;
    }

    const tsField = document.getElementById('footer_form_timestamp');
    if (tsField) {
        tsField.value = Math.floor(Date.now() / 1000);
    }

    const endpoint = <?php echo json_encode(siteUrl('includes/newsletter_simple_handler.php'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const recaptchaOn = footerNewsletterForm.getAttribute('data-recaptcha') === '1';
    const recaptchaSiteKey = <?php echo json_encode(defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    footerNewsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = document.getElementById('footer-newsletter-submit');
        const loadingBtn = document.getElementById('footer-newsletter-loading');
        const successAlert = document.getElementById('footer-newsletter-success');
        const errorAlert = document.getElementById('footer-newsletter-error');
        const successMessage = document.getElementById('footer-newsletter-success-message');
        const errorMessage = document.getElementById('footer-newsletter-error-message');

        successAlert.classList.add('d-none');
        errorAlert.classList.add('d-none');

        submitBtn.classList.add('d-none');
        loadingBtn.classList.remove('d-none');

        function sendForm(formData) {
            return fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    successMessage.textContent = data.message;
                    successAlert.classList.remove('d-none');
                    form.reset();
                    if (tsField) {
                        tsField.value = Math.floor(Date.now() / 1000);
                    }
                } else {
                    errorMessage.textContent = data.message || 'No se pudo completar la suscripción.';
                    errorAlert.classList.remove('d-none');
                }
            })
            .catch(function() {
                errorMessage.textContent = 'Error de conexión. Por favor, intenta de nuevo.';
                errorAlert.classList.remove('d-none');
            })
            .finally(function() {
                submitBtn.classList.remove('d-none');
                loadingBtn.classList.add('d-none');
            });
        }

        const formData = new FormData(form);

        if (recaptchaOn && recaptchaSiteKey && typeof grecaptcha !== 'undefined') {
            grecaptcha.ready(function() {
                grecaptcha.execute(recaptchaSiteKey, { action: 'footer_newsletter' }).then(function(token) {
                    formData.append('g-recaptcha-response', token);
                    sendForm(formData);
                }).catch(function() {
                    errorMessage.textContent = 'Error de verificación. Recarga la página e intenta de nuevo.';
                    errorAlert.classList.remove('d-none');
                    submitBtn.classList.remove('d-none');
                    loadingBtn.classList.add('d-none');
                });
            });
        } else {
            sendForm(formData);
        }
    });
});
</script>

