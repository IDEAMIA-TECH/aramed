<?php
/**
 * Formulario simple para suscripción al boletín informativo
 */

// Configuración básica
define('ARAMED_SITE', true);
require_once __DIR__ . '/includes/config.php';
?>

<div class="newsletter-simple-form">
    <div class="newsletter-header">
        <h4 class="newsletter-title">
            <i class="bi bi-envelope-fill me-2"></i>
            ¿Quieres recibir nuestras novedades?
        </h4>
        <p class="newsletter-subtitle">Suscríbete a nuestro boletín informativo</p>
    </div>
    
    <!-- Mensajes de respuesta -->
    <div id="newsletter-simple-success" class="alert alert-success d-none" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>¡Gracias!</strong> <span id="newsletter-simple-success-message"></span>
    </div>
    
    <div id="newsletter-simple-error" class="alert alert-danger d-none" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error:</strong> <span id="newsletter-simple-error-message"></span>
    </div>
    
    <form id="newsletterSimpleForm" action="includes/newsletter_simple_handler.php" method="POST" novalidate>
        <div class="newsletter-fields">
            <div class="mb-3">
                <input type="email" 
                       class="form-control form-control-lg" 
                       id="newsletter-email" 
                       name="email" 
                       placeholder="Tu correo electrónico" 
                       required>
                <div class="invalid-feedback">Por favor ingresa un correo válido.</div>
            </div>
            
            <div class="mb-3">
                <input type="text" 
                       class="form-control form-control-lg" 
                       id="newsletter-nombre" 
                       name="nombre" 
                       placeholder="Tu nombre (opcional)">
            </div>
            
            <input type="hidden" name="source" value="boletin">
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" id="newsletter-simple-submit-btn">
                    <i class="bi bi-send-fill me-2"></i>
                    Suscribirse
                </button>
                <button type="button" class="btn btn-primary btn-lg d-none" id="newsletter-simple-loading-btn" disabled>
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Suscribiendo...
                </button>
            </div>
        </div>
        
        <div class="newsletter-footer">
            <small class="text-muted">
                Al suscribirte, aceptas recibir comunicaciones de <?php echo SITE_NAME; ?>. 
                Puedes darte de baja en cualquier momento.
            </small>
        </div>
    </form>
</div>

<style>
.newsletter-simple-form {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    margin: 1rem 0;
}

.newsletter-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.newsletter-title {
    color: var(--aramed-primary, #0066cc);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.newsletter-subtitle {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.newsletter-fields .form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.newsletter-fields .form-control:focus {
    border-color: var(--aramed-primary, #0066cc);
    box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
}

.newsletter-footer {
    margin-top: 1rem;
    text-align: center;
}

.newsletter-footer small {
    font-size: 0.8rem;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .newsletter-simple-form {
        padding: 1rem;
        margin: 0.5rem 0;
    }
    
    .newsletter-title {
        font-size: 1.1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newsletterSimpleForm');
    const submitBtn = document.getElementById('newsletter-simple-submit-btn');
    const loadingBtn = document.getElementById('newsletter-simple-loading-btn');
    const successAlert = document.getElementById('newsletter-simple-success');
    const errorAlert = document.getElementById('newsletter-simple-error');
    const successMessage = document.getElementById('newsletter-simple-success-message');
    const errorMessage = document.getElementById('newsletter-simple-error-message');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Ocultar mensajes anteriores
            successAlert.classList.add('d-none');
            errorAlert.classList.add('d-none');
            
            // Mostrar loading
            submitBtn.classList.add('d-none');
            loadingBtn.classList.remove('d-none');
            
            // Obtener datos del formulario
            const formData = new FormData(form);
            
            // Enviar petición
            fetch('includes/newsletter_simple_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successMessage.textContent = data.message;
                    successAlert.classList.remove('d-none');
                    form.reset(); // Limpiar formulario
                } else {
                    errorMessage.textContent = data.message;
                    errorAlert.classList.remove('d-none');
                }
            })
            .catch(error => {
                errorMessage.textContent = 'Error de conexión. Por favor, intenta de nuevo.';
                errorAlert.classList.remove('d-none');
            })
            .finally(() => {
                // Ocultar loading
                submitBtn.classList.remove('d-none');
                loadingBtn.classList.add('d-none');
            });
        });
    }
});
</script>
