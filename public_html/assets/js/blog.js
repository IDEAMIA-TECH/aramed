/**
 * ========================================
 * ARAMED Y LABORATORIOS - Blog JavaScript
 * ========================================
 * 
 * Funcionalidades específicas para el blog
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

const AramedBlog = {
    // Elementos del DOM
    elements: {
        searchForm: null,
        categoryFilter: null,
        articlesContainer: null,
        pagination: null,
        commentForm: null
    },

    // Inicialización
    init: function() {
        this.cacheElements();
        this.bindEvents();
        this.initLazyLoading();
        this.initShareButtons();
        this.initCommentForm();
        this.initSearch();
        console.log('✅ Blog JavaScript inicializado');
    },

    // Cachear elementos del DOM
    cacheElements: function() {
        this.elements.searchForm = document.querySelector('.blog-filters form');
        this.elements.categoryFilter = document.querySelector('select[name="categoria"]');
        this.elements.articlesContainer = document.querySelector('.row.g-4');
        this.elements.pagination = document.querySelector('.pagination');
        this.elements.commentForm = document.getElementById('commentForm');
    },

    // Vincular eventos
    bindEvents: function() {
        // Búsqueda en tiempo real
        if (this.elements.searchForm) {
            const searchInput = this.elements.searchForm.querySelector('input[name="busqueda"]');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.performSearch(e.target.value);
                    }, 500);
                });
            }
        }

        // Filtro de categorías
        if (this.elements.categoryFilter) {
            this.elements.categoryFilter.addEventListener('change', (e) => {
                this.filterByCategory(e.target.value);
            });
        }

        // Paginación
        if (this.elements.pagination) {
            this.elements.pagination.addEventListener('click', (e) => {
                if (e.target.classList.contains('page-link')) {
                    this.handlePagination(e);
                }
            });
        }

        // Scroll suave para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    },

    // Inicializar lazy loading
    initLazyLoading: function() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    },

    // Inicializar botones de compartir
    initShareButtons: function() {
        // Funciones de compartir ya están definidas globalmente en blog-detalle.php
        // Aquí podemos agregar funcionalidades adicionales si es necesario
    },

    // Inicializar formulario de comentarios
    initCommentForm: function() {
        if (this.elements.commentForm) {
            this.elements.commentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitComment();
            });
        }
    },

    // Inicializar búsqueda
    initSearch: function() {
        // La búsqueda se maneja del lado del servidor
        // Aquí podemos agregar funcionalidades de autocompletado o sugerencias
    },

    // Realizar búsqueda
    performSearch: function(query) {
        if (query.length < 2) return;

        // Mostrar indicador de carga
        this.showLoadingIndicator();

        // Realizar búsqueda AJAX
        const formData = new FormData();
        formData.append('busqueda', query);
        formData.append('ajax', '1');

        fetch('blog.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            this.updateArticlesContainer(html);
            this.hideLoadingIndicator();
        })
        .catch(error => {
            console.error('Error en búsqueda:', error);
            this.hideLoadingIndicator();
        });
    },

    // Filtrar por categoría
    filterByCategory: function(categoryId) {
        this.showLoadingIndicator();

        const formData = new FormData();
        formData.append('categoria', categoryId);
        formData.append('ajax', '1');

        fetch('blog.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            this.updateArticlesContainer(html);
            this.hideLoadingIndicator();
        })
        .catch(error => {
            console.error('Error en filtro:', error);
            this.hideLoadingIndicator();
        });
    },

    // Manejar paginación
    handlePagination: function(e) {
        e.preventDefault();
        const page = e.target.getAttribute('href');
        if (page) {
            this.showLoadingIndicator();
            window.location.href = page;
        }
    },

    // Actualizar contenedor de artículos
    updateArticlesContainer: function(html) {
        if (this.elements.articlesContainer) {
            // Crear un elemento temporal para parsear el HTML
            const temp = document.createElement('div');
            temp.innerHTML = html;
            
            // Extraer solo los artículos
            const newArticles = temp.querySelector('.row.g-4');
            if (newArticles) {
                this.elements.articlesContainer.innerHTML = newArticles.innerHTML;
                
                // Reinicializar AOS para las nuevas animaciones
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }
        }
    },

    // Mostrar indicador de carga
    showLoadingIndicator: function() {
        if (this.elements.articlesContainer) {
            const loadingHtml = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Buscando artículos...</p>
                </div>
            `;
            this.elements.articlesContainer.innerHTML = loadingHtml;
        }
    },

    // Ocultar indicador de carga
    hideLoadingIndicator: function() {
        // Se oculta cuando se actualiza el contenido
    },

    // Enviar comentario
    submitComment: function() {
        if (!this.elements.commentForm) return;

        const formData = new FormData(this.elements.commentForm);
        const submitBtn = this.elements.commentForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Mostrar estado de carga
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        submitBtn.disabled = true;

        fetch('includes/blog_comment_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showCommentSuccess();
                this.elements.commentForm.reset();
            } else {
                this.showCommentError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showCommentError('Error de conexión. Por favor, intenta de nuevo.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    },

    // Mostrar éxito de comentario
    showCommentSuccess: function() {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                Comentario enviado correctamente. Será revisado antes de publicarse.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = this.elements.commentForm.closest('.card-body');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    },

    // Mostrar error de comentario
    showCommentError: function(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = this.elements.commentForm.closest('.card-body');
        container.insertAdjacentHTML('afterbegin', alertHtml);
    },

    // Funciones de utilidad
    utils: {
        // Debounce function
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction() {
                const context = this;
                const args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        // Formatear fecha
        formatDate: function(dateString) {
            const date = new Date(dateString);
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            return date.toLocaleDateString('es-MX', options);
        },

        // Truncar texto
        truncateText: function(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substr(0, maxLength) + '...';
        },

        // Scroll suave
        smoothScrollTo: function(element, offset = 0) {
            const targetPosition = element.offsetTop - offset;
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    AramedBlog.init();
});

// Exportar para uso global
window.AramedBlog = AramedBlog;
