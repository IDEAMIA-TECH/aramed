/**
 * ========================================
 * ARAMED Y LABORATORIOS - Forms JS
 * ========================================
 * 
 * Validación y manejo de formularios
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

(function() {
    'use strict';

    /**
     * ========================================
     * FORMS MODULE
     * ========================================
     */
    const Forms = {
        init: function() {
            this.setupFormValidation();
            this.setupContactForm();
            this.setupNewsletterForm();
            console.log('✅ Forms JS initialized');
        },
        
        /**
         * Configurar validación de formularios
         */
        setupFormValidation: function() {
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        },
        
        /**
         * Formulario de Contacto
         * Placeholder - Se implementará en DÍA 9
         */
        setupContactForm: function() {
            const contactForm = document.getElementById('contactForm');
            
            if (!contactForm) {
                console.log('ℹ️ Formulario de contacto pendiente de implementación');
                return;
            }
            
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Validar formulario
                if (!contactForm.checkValidity()) {
                    contactForm.classList.add('was-validated');
                    return;
                }
                
                // Obtener datos del formulario
                const formData = new FormData(contactForm);
                
                // Verificar reCAPTCHA si está habilitado
                if (typeof grecaptcha !== 'undefined') {
                    try {
                        const token = await grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'contact' });
                        formData.append('recaptcha_token', token);
                    } catch (error) {
                        console.error('Error reCAPTCHA:', error);
                        Forms.showAlert('Error al validar reCAPTCHA', 'danger');
                        return;
                    }
                }
                
                // Deshabilitar botón y mostrar loading
                const submitBtn = contactForm.querySelector('[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
                
                try {
                    // Enviar formulario
                    const response = await fetch('/api/send-contact.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Forms.showAlert('¡Mensaje enviado correctamente! Te contactaremos pronto.', 'success');
                        contactForm.reset();
                        contactForm.classList.remove('was-validated');
                        
                        // Cerrar modal si existe
                        const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
                        if (modal) {
                            setTimeout(() => modal.hide(), 2000);
                        }
                    } else {
                        Forms.showAlert(data.message || 'Error al enviar el mensaje', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Forms.showAlert('Error al enviar el mensaje. Por favor, intenta nuevamente.', 'danger');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        },
        
        /**
         * Formulario de Newsletter
         * Placeholder - Se implementará en DÍA 8
         */
        setupNewsletterForm: function() {
            const newsletterForm = document.getElementById('newsletterForm');
            
            if (!newsletterForm) {
                console.log('ℹ️ Formulario de newsletter pendiente de implementación');
                return;
            }
            
            newsletterForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Validar formulario
                if (!newsletterForm.checkValidity()) {
                    newsletterForm.classList.add('was-validated');
                    return;
                }
                
                const formData = new FormData(newsletterForm);
                
                // Deshabilitar botón y mostrar loading
                const submitBtn = newsletterForm.querySelector('[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
                
                try {
                    const response = await fetch('/api/send-newsletter.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Forms.showAlert('¡Gracias por suscribirte! Te mantendremos informado.', 'success');
                        newsletterForm.reset();
                        newsletterForm.classList.remove('was-validated');
                    } else {
                        Forms.showAlert(data.message || 'Error al suscribirse', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Forms.showAlert('Error al procesar la solicitud. Por favor, intenta nuevamente.', 'danger');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        },
        
        /**
         * Validación personalizada de email
         */
        validateEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },
        
        /**
         * Validación personalizada de teléfono (México)
         */
        validatePhone: function(phone) {
            // Acepta formatos: 1234567890, 123-456-7890, (123) 456-7890
            const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
            return re.test(phone);
        },
        
        /**
         * Mostrar alerta
         */
        showAlert: function(message, type = 'info') {
            // Buscar contenedor de alertas
            let alertContainer = document.getElementById('alertContainer');
            
            // Crear contenedor si no existe
            if (!alertContainer) {
                alertContainer = document.createElement('div');
                alertContainer.id = 'alertContainer';
                alertContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
                document.body.appendChild(alertContainer);
            }
            
            // Crear alerta
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
            alert.setAttribute('role', 'alert');
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Agregar alerta al contenedor
            alertContainer.appendChild(alert);
            
            // Auto-cerrar después de 5 segundos
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }, 5000);
        },
        
        /**
         * Limpiar errores de formulario
         */
        clearFormErrors: function(form) {
            form.classList.remove('was-validated');
            const invalidFeedbacks = form.querySelectorAll('.invalid-feedback');
            invalidFeedbacks.forEach(feedback => feedback.style.display = 'none');
        },
        
        /**
         * Mostrar error en campo específico
         */
        showFieldError: function(field, message) {
            field.classList.add('is-invalid');
            
            let feedback = field.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                field.parentNode.insertBefore(feedback, field.nextSibling);
            }
            
            feedback.textContent = message;
            feedback.style.display = 'block';
        },
        
        /**
         * Limpiar error de campo específico
         */
        clearFieldError: function(field) {
            field.classList.remove('is-invalid');
            const feedback = field.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.style.display = 'none';
            }
        }
    };

    /**
     * ========================================
     * INICIALIZACIÓN
     * ========================================
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            Forms.init();
        });
    } else {
        Forms.init();
    }

    // Exponer Forms globalmente
    window.AramedForms = Forms;

})();

