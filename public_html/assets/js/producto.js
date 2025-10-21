/**
 * ========================================
 * PRODUCTO JAVASCRIPT
 * ========================================
 * 
 * Funcionalidades específicas para la página de detalles del producto
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Namespace para el producto
const AramedProducto = {
    
    /**
     * Inicialización principal
     */
    init: function() {
        this.initImageGallery();
        this.initScrollToTop();
        this.initSmoothScrolling();
        this.initLazyLoading();
        this.initTooltips();
        this.initShareButtons();
        this.initPrintFunction();
        
        // console.log('✅ AramedProducto initialized');
    },
    
    /**
     * Inicializar galería de imágenes con Swiper
     */
    initImageGallery: function() {
        // Verificar que existen los elementos del swiper
        const mainSwiperEl = document.querySelector('.producto-swiper-main');
        const thumbsSwiperEl = document.querySelector('.producto-swiper-thumbs');
        
        if (!mainSwiperEl) {
            console.log('No se encontró el swiper principal');
            return;
        }
        
        // Swiper thumbnails primero
        let thumbsSwiper = null;
        if (thumbsSwiperEl) {
            thumbsSwiper = new Swiper('.producto-swiper-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: {
                    320: {
                        slidesPerView: 3,
                        spaceBetween: 8,
                    },
                    768: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                }
            });
        }
        
        // Swiper principal
        const mainSwiper = new Swiper('.producto-swiper-main', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.producto-swiper-main .swiper-button-next',
                prevEl: '.producto-swiper-main .swiper-button-prev',
            },
            pagination: {
                el: '.producto-swiper-main .swiper-pagination',
                clickable: true,
            },
            keyboard: {
                enabled: true,
            },
            loop: false,
            effect: 'slide',
            speed: 300,
            // Conectar con thumbs si existe
            ...(thumbsSwiper && {
                thumbs: {
                    swiper: thumbsSwiper,
                },
            }),
        });
        
        // Zoom en imagen principal (futuro)
        this.initImageZoom();
    },
    
    /**
     * Inicializar zoom en imágenes (funcionalidad futura)
     */
    initImageZoom: function() {
        const mainImages = document.querySelectorAll('.producto-swiper-main .producto-image');
        
        mainImages.forEach(img => {
            img.addEventListener('click', function() {
                // Aquí se puede implementar un modal con zoom
                console.log('Zoom en imagen:', this.src);
            });
        });
    },
    
    /**
     * Botón de scroll to top
     */
    initScrollToTop: function() {
        // Crear botón si no existe
        if (!document.querySelector('.scroll-to-top')) {
            const scrollBtn = document.createElement('button');
            scrollBtn.className = 'scroll-to-top btn btn-primary';
            scrollBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
            scrollBtn.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: none;
                box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
            `;
            
            scrollBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            document.body.appendChild(scrollBtn);
        }
        
        // Mostrar/ocultar según scroll
        window.addEventListener('scroll', () => {
            const scrollBtn = document.querySelector('.scroll-to-top');
            if (scrollBtn) {
                if (window.scrollY > 300) {
                    scrollBtn.style.display = 'block';
                } else {
                    scrollBtn.style.display = 'none';
                }
            }
        });
    },
    
    /**
     * Smooth scrolling para enlaces internos
     */
    initSmoothScrolling: function() {
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                if (href === '#newsletter') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    },
    
    /**
     * Lazy loading para imágenes
     */
    initLazyLoading: function() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('loading-skeleton');
                            img.classList.add('loaded');
                        }
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            // Observar imágenes relacionadas
            document.querySelectorAll('.related-image').forEach(img => {
                imageObserver.observe(img);
            });
        }
    },
    
    /**
     * Inicializar tooltips
     */
    initTooltips: function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },
    
    /**
     * Botones de compartir (futuro)
     */
    initShareButtons: function() {
        // Crear botones de compartir si no existen
        const shareContainer = document.querySelector('.producto-share');
        if (!shareContainer) {
            const productInfo = document.querySelector('.producto-info');
            if (productInfo) {
                const shareHtml = `
                    <div class="producto-share mt-4">
                        <h6 class="mb-2">Compartir:</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm share-btn" data-share="facebook" title="Compartir en Facebook">
                                <i class="bi bi-facebook"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm share-btn" data-share="twitter" title="Compartir en Twitter">
                                <i class="bi bi-twitter"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm share-btn" data-share="linkedin" title="Compartir en LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm share-btn" data-share="whatsapp" title="Compartir en WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </button>
                        </div>
                    </div>
                `;
                productInfo.insertAdjacentHTML('beforeend', shareHtml);
            }
        }
        
        // Event listeners para compartir
        document.querySelectorAll('.share-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.shareProduct(btn.dataset.share);
            });
        });
    },
    
    /**
     * Función para compartir producto
     */
    shareProduct: function(platform) {
        const url = window.location.href;
        const title = document.querySelector('.producto-title')?.textContent || '';
        const description = document.querySelector('.producto-description-short p')?.textContent || '';
        
        let shareUrl = '';
        
        switch (platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
                break;
            case 'linkedin':
                shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                break;
            case 'whatsapp':
                shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' - ' + url)}`;
                break;
        }
        
        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    },
    
    /**
     * Función de impresión
     */
    initPrintFunction: function() {
        // Crear botón de imprimir si no existe
        if (!document.querySelector('.print-btn')) {
            const printBtn = document.createElement('button');
            printBtn.className = 'print-btn btn btn-outline-secondary btn-sm';
            printBtn.innerHTML = '<i class="bi bi-printer me-1"></i>Imprimir';
            printBtn.title = 'Imprimir información del producto';
            
            printBtn.addEventListener('click', () => {
                this.printProduct();
            });
            
            // Agregar al área de acciones
            const actions = document.querySelector('.producto-actions');
            if (actions) {
                actions.appendChild(printBtn);
            }
        }
    },
    
    /**
     * Función para imprimir producto
     */
    printProduct: function() {
        // Crear ventana de impresión
        const printWindow = window.open('', '_blank');
        const productTitle = document.querySelector('.producto-title')?.textContent || '';
        const productInfo = document.querySelector('.producto-info')?.innerHTML || '';
        const productDetails = document.querySelector('.producto-details')?.innerHTML || '';
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${productTitle} - ${document.title}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        .no-print { display: none !important; }
                        .btn { display: none !important; }
                        body { font-size: 12px; }
                    }
                </style>
            </head>
            <body>
                <div class="container mt-4">
                    <h1>${productTitle}</h1>
                    <div class="producto-info">${productInfo}</div>
                    <div class="producto-details">${productDetails}</div>
                </div>
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    },
    
    /**
     * Función para agregar a favoritos (futuro)
     */
    addToFavorites: function(productId) {
        // Esta función se puede expandir para funcionalidad de favoritos
        console.log('Agregando a favoritos:', productId);
        
        // Mostrar notificación
        this.showNotification('Producto agregado a favoritos', 'success');
    },
    
    /**
     * Función para comparar productos (futuro)
     */
    addToCompare: function(productId) {
        // Esta función se puede expandir para comparación de productos
        console.log('Agregando a comparación:', productId);
        
        // Mostrar notificación
        this.showNotification('Producto agregado para comparar', 'info');
    },
    
    /**
     * Función para mostrar notificación
     */
    showNotification: function(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove después de 3 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    },
    
    /**
     * Función para cargar productos relacionados dinámicamente
     */
    loadRelatedProducts: function(categoryId) {
        // Esta función se puede expandir para cargar productos relacionados via AJAX
        console.log('Cargando productos relacionados para categoría:', categoryId);
    },
    
    /**
     * Función para mostrar modal de imagen grande
     */
    showImageModal: function(imageSrc, imageAlt) {
        // Crear modal si no existe
        let modal = document.getElementById('imageModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'imageModal';
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${imageAlt}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${imageSrc}" alt="${imageAlt}" class="img-fluid">
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        // Actualizar contenido del modal
        const modalImg = modal.querySelector('img');
        const modalTitle = modal.querySelector('.modal-title');
        modalImg.src = imageSrc;
        modalImg.alt = imageAlt;
        modalTitle.textContent = imageAlt;
        
        // Mostrar modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
};

// ========================================
// UTILIDADES ADICIONALES
// ========================================

/**
 * Función para formatear precios
 */
function formatPrice(price, currency = 'MXN') {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: currency
    }).format(price);
}

/**
 * Función para copiar enlace al portapapeles
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            AramedProducto.showNotification('Enlace copiado al portapapeles', 'success');
        });
    } else {
        // Fallback para navegadores más antiguos
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        AramedProducto.showNotification('Enlace copiado al portapapeles', 'success');
    }
}

/**
 * Función para generar enlace de producto
 */
function generateProductLink(productId, productName) {
    const baseUrl = window.location.origin + window.location.pathname.replace('producto.php', '');
    return `${baseUrl}producto.php?id=${productId}`;
}

// ========================================
// EVENT LISTENERS GLOBALES
// ========================================

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    AramedProducto.init();
});

// Manejar cambios de tamaño de ventana
window.addEventListener('resize', function() {
    clearTimeout(window.resizeTimeout);
    window.resizeTimeout = setTimeout(() => {
        // Recalcular layout si es necesario
        AramedProducto.initTooltips();
    }, 250);
});

// Manejar teclado para navegación
document.addEventListener('keydown', function(e) {
    // ESC para cerrar modales
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    }
});

// ========================================
// EXPORT PARA USO EN OTROS SCRIPTS
// ========================================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AramedProducto;
}
