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
            this.initAliadosDetalleCarousel();
            this.initTestimonios();
            this.initCounters();
            // console.log('✅ Landing Page JS initialized');
        },
        
        /**
         * Hero Slider (Swiper)
         * Configuración completa del slider principal
         */
        initHeroSlider: function() {
            const heroSlider = document.querySelector('.hero-swiper');
            
            if (!heroSlider) {
                // console.log('ℹ️ Hero slider no encontrado');
                return;
            }
            
            // Configuración del slider
            const heroSwiper = new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 7000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                speed: 1200,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                    dynamicMainBullets: 3,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                keyboard: {
                    enabled: true,
                    onlyInViewport: true,
                },
                mousewheel: {
                    enabled: false,
                },
                // A11y
                a11y: {
                    prevSlideMessage: 'Slide anterior',
                    nextSlideMessage: 'Siguiente slide',
                    paginationBulletMessage: 'Ir al slide {{index}}',
                },
                // Callbacks
                on: {
                    init: function() {
                        // console.log('✅ Hero Swiper initialized');
                        // Animar el primer slide
                        const firstSlide = document.querySelector('.hero-swiper .swiper-slide-active .hero-content');
                        if (firstSlide) {
                            firstSlide.style.opacity = '0';
                            firstSlide.style.transform = 'translateY(30px)';
                            setTimeout(() => {
                                firstSlide.style.transition = 'all 0.8s ease';
                                firstSlide.style.opacity = '1';
                                firstSlide.style.transform = 'translateY(0)';
                            }, 300);
                        }
                    },
                    slideChange: function() {
                        // Animar contenido del slide activo
                        const activeSlide = document.querySelector('.hero-swiper .swiper-slide-active .hero-content');
                        if (activeSlide) {
                            activeSlide.style.opacity = '0';
                            activeSlide.style.transform = 'translateY(30px)';
                            setTimeout(() => {
                                activeSlide.style.transition = 'all 0.8s ease';
                                activeSlide.style.opacity = '1';
                                activeSlide.style.transform = 'translateY(0)';
                            }, 300);
                        }
                    },
                    autoplayTimeLeft: function(s, time, progress) {
                        // Opcional: mostrar progreso del autoplay
                        const pagination = document.querySelector('.swiper-pagination');
                        if (pagination) {
                            pagination.style.setProperty('--progress', 1 - progress);
                        }
                    }
                }
            });
            
            // Pausar autoplay cuando el usuario interactúa con el slider
            const heroSection = document.querySelector('.hero-section');
            if (heroSection) {
                heroSection.addEventListener('mouseenter', () => {
                    if (heroSwiper.autoplay.running) {
                        heroSwiper.autoplay.stop();
                    }
                });
                
                heroSection.addEventListener('mouseleave', () => {
                    if (!heroSwiper.autoplay.running) {
                        heroSwiper.autoplay.start();
                    }
                });
            }
            
            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    heroSwiper.slidePrev();
                } else if (e.key === 'ArrowRight') {
                    heroSwiper.slideNext();
                }
            });
            
            return heroSwiper;
        },
        
        /**
         * Carrusel de Aliados
         * Placeholder - Se implementará en DÍA 5
         */
        initAliadosCarousel: function() {
            const aliadosCarousel = document.querySelector('.aliados-swiper');
            
            if (!aliadosCarousel) {
                // console.log('ℹ️ Carrusel de aliados pendiente de implementación');
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
                // console.log('ℹ️ Slider de testimonios pendiente de implementación');
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
            // Buscar todos los contadores (.counter y .stat-number con data-target)
            const counters = document.querySelectorAll('.counter, .stat-number[data-target]');
            
            if (counters.length === 0) {
                // console.log('ℹ️ No se encontraron contadores para animar');
                return;
            }
            
            const animateCounter = (counter) => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = parseInt(counter.getAttribute('data-duration') || 2000);
                const increment = target / (duration / 16); // 60 FPS
                let current = 0;
                
                // Detectar si hay sufijo (ej: "20+", "100%")
                const originalText = counter.textContent;
                const hasSuffix = originalText.match(/[+%]/);
                const suffix = hasSuffix ? hasSuffix[0] : '';
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.ceil(current) + suffix;
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target + suffix;
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
                        // console.log('✅ Contador animado:', entry.target.getAttribute('data-target'));
                    }
                });
            }, {
                threshold: 0.5
            });
            
            counters.forEach(counter => observer.observe(counter));
            // console.log(`✅ ${counters.length} contadores inicializados`);
        },
        
        /**
         * Aliados Detalle Carousel (Swiper)
         * Carrusel rotatorio automático para la sección de aliados con descripciones
         */
        initAliadosDetalleCarousel: function() {
            const aliadosDetalleCarousel = document.querySelector('.aliados-detalle-swiper');
            
            if (!aliadosDetalleCarousel) {
                // console.log('ℹ️ Aliados detalle carousel no encontrado');
                return;
            }
            
            // Configuración del carrusel
            const aliadosDetalleSwiper = new Swiper('.aliados-detalle-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 40,
                    }
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                // Efectos
                effect: 'slide',
                speed: 800,
                // Accesibilidad
                a11y: {
                    enabled: true,
                    prevSlideMessage: 'Aliado anterior',
                    nextSlideMessage: 'Aliado siguiente',
                    firstSlideMessage: 'Primer aliado',
                    lastSlideMessage: 'Último aliado',
                }
            });
            
            // console.log('✅ Aliados detalle carousel initialized');
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

