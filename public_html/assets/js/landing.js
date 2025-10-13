/**
 * ========================================
 * ARAMED Y LABORATORIOS - Landing Page JS
 * ========================================
 * 
 * Funcionalidades específicas de la landing page
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

(function() {
    'use strict';

    /**
     * ========================================
     * LANDING PAGE MODULE
     * ========================================
     */
    const Landing = {
        init: function() {
            this.initHeroSlider();
            this.initAliadosCarousel();
            this.initTestimonios();
            this.initCounters();
            console.log('✅ Landing Page JS initialized');
        },
        
        /**
         * Hero Slider (Swiper)
         * Placeholder - Se implementará en DÍA 3-4
         */
        initHeroSlider: function() {
            const heroSlider = document.querySelector('.hero-swiper');
            
            if (!heroSlider) {
                console.log('ℹ️ Hero slider pendiente de implementación');
                return;
            }
            
            // Configuración del slider
            const swiper = new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                speed: 1000,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        },
        
        /**
         * Carrusel de Aliados
         * Placeholder - Se implementará en DÍA 5
         */
        initAliadosCarousel: function() {
            const aliadosCarousel = document.querySelector('.aliados-swiper');
            
            if (!aliadosCarousel) {
                console.log('ℹ️ Carrusel de aliados pendiente de implementación');
                return;
            }
            
            // Configuración del carrusel
            const swiper = new Swiper('.aliados-swiper', {
                slidesPerView: 2,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    576: {
                        slidesPerView: 3,
                    },
                    768: {
                        slidesPerView: 4,
                    },
                    992: {
                        slidesPerView: 5,
                    },
                    1200: {
                        slidesPerView: 6,
                    }
                }
            });
        },
        
        /**
         * Sección de Testimonios
         * Placeholder - Se implementará en DÍA 5
         */
        initTestimonios: function() {
            const testimoniosSlider = document.querySelector('.testimonios-swiper');
            
            if (!testimoniosSlider) {
                console.log('ℹ️ Slider de testimonios pendiente de implementación');
                return;
            }
            
            // Configuración del slider de testimonios
            const swiper = new Swiper('.testimonios-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    1200: {
                        slidesPerView: 3,
                    }
                }
            });
        },
        
        /**
         * Animación de Contadores (Números que suben)
         */
        initCounters: function() {
            const counters = document.querySelectorAll('.counter');
            
            if (counters.length === 0) return;
            
            const animateCounter = (counter) => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = parseInt(counter.getAttribute('data-duration') || 2000);
                const increment = target / (duration / 16); // 60 FPS
                let current = 0;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                updateCounter();
            };
            
            // Usar Intersection Observer para animar cuando sea visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                        animateCounter(entry.target);
                        entry.target.classList.add('animated');
                    }
                });
            }, {
                threshold: 0.5
            });
            
            counters.forEach(counter => observer.observe(counter));
        },
        
        /**
         * Parallax Effect (Efecto parallax simple)
         */
        initParallax: function() {
            const parallaxElements = document.querySelectorAll('[data-parallax]');
            
            if (parallaxElements.length === 0) return;
            
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                
                parallaxElements.forEach(element => {
                    const speed = element.getAttribute('data-parallax') || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            });
        }
    };

    /**
     * ========================================
     * INICIALIZACIÓN
     * ========================================
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            Landing.init();
        });
    } else {
        Landing.init();
    }

    // Exponer Landing globalmente
    window.AramedLanding = Landing;

})();

