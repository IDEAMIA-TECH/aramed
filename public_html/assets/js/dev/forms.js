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
         */
        setupContactForm: function() {
            const contactForm = document.getElementById('contactForm');
            
            if (!contactForm) {
                console.log('ℹ️ Formulario de contacto no encontrado');
                return;
            }

            const contactModal = document.getElementById('contactModal');
            const tsField = document.getElementById('contact_form_timestamp');
            if (contactModal && tsField) {
                contactModal.addEventListener('shown.bs.modal', function() {
                    tsField.value = Math.floor(Date.now() / 1000);
                });
            }
            
            // Elementos de UI
            const submitBtn = document.getElementById('contact-submit-btn');
            const loadingBtn = document.getElementById('contact-loading-btn');
            const successAlert = document.getElementById('contact-success');
            const errorAlert = document.getElementById('contact-error');
            const errorMessage = document.getElementById('contact-error-message');
            
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Ocultar alertas previas
                if (successAlert) successAlert.classList.add('d-none');
                if (errorAlert) errorAlert.classList.add('d-none');
                
                // Validar formulario
                if (!contactForm.checkValidity()) {
                    contactForm.classList.add('was-validated');
                    
                    // Scroll al primer campo inválido
                    const firstInvalid = contactForm.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                    return;
                }

                const siteKey = contactForm.getAttribute('data-recaptcha-site-key');

                async function sendContact(formData) {
                    const response = await fetch(contactForm.action, {
                        method: 'POST',
                        body: formData
                    });
                    return response.json();
                }
                
                const formData = new FormData(contactForm);
                
                // Mostrar estado de carga
                if (submitBtn) submitBtn.classList.add('d-none');
                if (loadingBtn) loadingBtn.classList.remove('d-none');
                
                try {
                    let data;
                    if (siteKey && typeof grecaptcha !== 'undefined') {
                        const token = await new Promise(function(resolve, reject) {
                            grecaptcha.ready(function() {
                                grecaptcha.execute(siteKey, { action: 'contact_modal' }).then(resolve).catch(reject);
                            });
                        });
                        if (typeof formData.set === 'function') {
                            formData.set('g-recaptcha-response', token);
                        } else {
                            formData.append('g-recaptcha-response', token);
                        }
                        data = await sendContact(formData);
                    } else {
                        data = await sendContact(formData);
                    }
                    
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        if (successAlert) {
                            successAlert.classList.remove('d-none');
                        }
                        
                        // Limpiar formulario
                        contactForm.reset();
                        contactForm.classList.remove('was-validated');
                        
                        // Cerrar modal después de 2 segundos
                        const modalElement = document.getElementById('contactModal');
                        if (modalElement) {
                            setTimeout(() => {
                                const modal = bootstrap.Modal.getInstance(modalElement);
                                if (modal) {
                                    modal.hide();
                                }
                                // Ocultar success alert
                                if (successAlert) successAlert.classList.add('d-none');
                            }, 2000);
                        }
                    } else {
                        // Mostrar error
                        if (errorMessage) errorMessage.textContent = data.message || 'Hubo un error al enviar tu mensaje.';
                        if (errorAlert) errorAlert.classList.remove('d-none');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    if (errorMessage) errorMessage.textContent = 'Error de conexión. Por favor, intenta de nuevo.';
                    if (errorAlert) errorAlert.classList.remove('d-none');
                } finally {
                    // Restaurar botón
                    if (submitBtn) submitBtn.classList.remove('d-none');
                    if (loadingBtn) loadingBtn.classList.add('d-none');
                }
            });
            
            console.log('✅ Contact form configured');
        },
        
        /**
         * Formulario de Newsletter
         */
        setupNewsletterForm: function() {
            const newsletterForm = document.getElementById('newsletterForm');
            
            if (!newsletterForm) {
                console.log('ℹ️ Formulario de newsletter no encontrado');
                return;
            }
            
            // Tipo de institución - mostrar campo adicional dinámico
            const tipoInstitucion = document.getElementById('tipo_institucion');
            const campoAdicionalWrapper = document.getElementById('campo_adicional_wrapper');
            
            if (tipoInstitucion && campoAdicionalWrapper) {
                tipoInstitucion.addEventListener('change', function() {
                    const showAdicional = ['Escuela de salud', 'Institución gubernamental'].includes(this.value);
                    if (showAdicional) {
                        campoAdicionalWrapper.classList.remove('d-none');
                    } else {
                        campoAdicionalWrapper.classList.add('d-none');
                        document.getElementById('campo_adicional').value = '';
                    }
                });
            }
            
            // Elementos de UI
            const submitBtn = document.getElementById('newsletter-submit-btn');
            const loadingBtn = document.getElementById('newsletter-loading-btn');
            const successAlert = document.getElementById('newsletter-success');
            const errorAlert = document.getElementById('newsletter-error');
            const errorMessage = document.getElementById('newsletter-error-message');
            
            newsletterForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Ocultar alertas previas
                successAlert.classList.add('d-none');
                errorAlert.classList.add('d-none');
                
                // Validar formulario
                if (!newsletterForm.checkValidity()) {
                    newsletterForm.classList.add('was-validated');
                    
                    // Scroll al primer campo inválido
                    const firstInvalid = newsletterForm.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                    return;
                }
                
                const formData = new FormData(newsletterForm);
                
                // Mostrar estado de carga
                submitBtn.classList.add('d-none');
                loadingBtn.classList.remove('d-none');
                
                try {
                    const response = await fetch(newsletterForm.action, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        successAlert.classList.remove('d-none');
                        successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Limpiar formulario
                        newsletterForm.reset();
                        newsletterForm.classList.remove('was-validated');
                        
                        // Ocultar campo adicional si estaba visible
                        if (campoAdicionalWrapper) {
                            campoAdicionalWrapper.classList.add('d-none');
                        }
                        
                        // Ocultar alerta después de 5 segundos
                        setTimeout(() => {
                            successAlert.classList.add('d-none');
                        }, 5000);
                    } else {
                        // Mostrar error
                        errorMessage.textContent = data.message || 'Hubo un error al procesar tu solicitud.';
                        errorAlert.classList.remove('d-none');
                        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Ocultar alerta después de 7 segundos
                        setTimeout(() => {
                            errorAlert.classList.add('d-none');
                        }, 7000);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    errorMessage.textContent = 'Error de conexión. Por favor, intenta de nuevo.';
                    errorAlert.classList.remove('d-none');
                    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    setTimeout(() => {
                        errorAlert.classList.add('d-none');
                    }, 7000);
                } finally {
                    // Restaurar botón
                    submitBtn.classList.remove('d-none');
                    loadingBtn.classList.add('d-none');
                }
            });
            
            console.log('✅ Newsletter form configured');
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

