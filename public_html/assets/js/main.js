/**
 * ========================================
 * ARAMED Y LABORATORIOS - JavaScript Principal
 * ========================================
 * 
 * Funcionalidades globales del sitio
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

(function() {
    'use strict';

    /**
     * ========================================
     * CONFIGURACIÓN GLOBAL
     * ========================================
     */
    const ARAMED = {
        config: {
            scrollOffset: 80,
            animationDuration: 300,
            debounceDelay: 250
        },
        
        init: function() {
            this.setupNavbar();
            this.setupSmoothScroll();
            this.setupLazyLoading();
            this.setupBackToTop();
            this.setupExternalLinks();
            console.log('✅ ARAMED Main JS initialized');
        },
        
        /**
         * Configuración del Navbar Sticky
         */
        setupNavbar: function() {
            const navbar = document.querySelector('.navbar');
            if (!navbar) return;
            
            let lastScrollTop = 0;
            const navbarHeight = navbar.offsetHeight;
            
            window.addEventListener('scroll', this.debounce(function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Agregar sombra cuando se hace scroll
                if (scrollTop > 50) {
                    navbar.classList.add('shadow');
                } else {
                    navbar.classList.remove('shadow');
                }
                
                // Actualizar active link según la sección visible
                ARAMED.updateActiveNavLink();
                
                lastScrollTop = scrollTop;
            }, this.config.debounceDelay));
        },
        
        /**
         * Actualizar link activo en el navbar según la sección visible
         */
        updateActiveNavLink: function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            let currentSection = '';
            const scrollY = window.pageYOffset;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                const sectionHeight = section.offsetHeight;
                
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    currentSection = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                if (href && href.includes('#' + currentSection)) {
                    link.classList.add('active');
                }
            });
        },
        
        /**
         * Smooth Scroll para enlaces internos
         */
        setupSmoothScroll: function() {
            const links = document.querySelectorAll('a[href^="#"]');
            
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    // Ignorar enlaces que solo son "#" o para modales/collapse
                    if (href === '#' || 
                        this.getAttribute('data-bs-toggle') || 
                        this.getAttribute('data-toggle')) {
                        return;
                    }
                    
                    e.preventDefault();
                    
                    const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - ARAMED.config.scrollOffset;
                        
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                        
                        // Cerrar navbar móvil si está abierto
                        const navbarCollapse = document.querySelector('.navbar-collapse');
                        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                                toggle: true
                            });
                        }
                    }
                });
            });
        },
        
        /**
         * Lazy Loading de imágenes
         */
        setupLazyLoading: function() {
            // Usar Intersection Observer si está disponible
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            const src = img.getAttribute('data-src');
                            const srcset = img.getAttribute('data-srcset');
                            
                            if (src) {
                                img.src = src;
                                img.removeAttribute('data-src');
                            }
                            
                            if (srcset) {
                                img.srcset = srcset;
                                img.removeAttribute('data-srcset');
                            }
                            
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px 0px',
                    threshold: 0.01
                });
                
                const lazyImages = document.querySelectorAll('img[data-src]');
                lazyImages.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback para navegadores sin IntersectionObserver
                const lazyImages = document.querySelectorAll('img[data-src]');
                lazyImages.forEach(img => {
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                });
            }
        },
        
        /**
         * Botón Back to Top
         */
        setupBackToTop: function() {
            // Crear botón si no existe
            let backToTopBtn = document.getElementById('backToTop');
            
            if (!backToTopBtn) {
                backToTopBtn = document.createElement('button');
                backToTopBtn.id = 'backToTop';
                backToTopBtn.className = 'btn btn-primary btn-back-to-top';
                backToTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
                backToTopBtn.setAttribute('aria-label', 'Volver arriba');
                document.body.appendChild(backToTopBtn);
                
                // Agregar estilos inline (temporal)
                backToTopBtn.style.cssText = `
                    position: fixed;
                    bottom: 30px;
                    right: 30px;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    display: none;
                    align-items: center;
                    justify-content: center;
                    z-index: 1000;
                    opacity: 0;
                    transition: opacity 0.3s, transform 0.3s;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                `;
            }
            
            // Mostrar/ocultar según scroll
            window.addEventListener('scroll', this.debounce(function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.style.display = 'flex';
                    setTimeout(() => backToTopBtn.style.opacity = '1', 10);
                } else {
                    backToTopBtn.style.opacity = '0';
                    setTimeout(() => backToTopBtn.style.display = 'none', 300);
                }
            }, 100));
            
            // Click handler
            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },
        
        /**
         * Enlaces externos abren en nueva pestaña
         */
        setupExternalLinks: function() {
            const links = document.querySelectorAll('a[href^="http"]');
            
            links.forEach(link => {
                const url = new URL(link.href);
                
                // Si el enlace no es del mismo dominio
                if (url.host !== window.location.host) {
                    link.setAttribute('target', '_blank');
                    link.setAttribute('rel', 'noopener noreferrer');
                }
            });
        },
        
        /**
         * Utilidad: Debounce
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        /**
         * Utilidad: Throttle
         */
        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },
        
        /**
         * Utilidad: Get Cookie
         */
        getCookie: function(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        },
        
        /**
         * Utilidad: Set Cookie
         */
        setCookie: function(name, value, days = 30) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }
    };

    /**
     * ========================================
     * INICIALIZACIÓN AL CARGAR EL DOM
     * ========================================
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            ARAMED.init();
        });
    } else {
        ARAMED.init();
    }

    // Exponer ARAMED globalmente para uso en otros scripts
    window.ARAMED = ARAMED;

})();

