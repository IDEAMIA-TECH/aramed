/**
 * ========================================
 * CATÁLOGO JAVASCRIPT
 * ========================================
 * 
 * Funcionalidades específicas para la página del catálogo
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Namespace para el catálogo
const AramedCatalogo = {
    
    // Estado actual de filtros
    currentFilters: {
        search: '',
        brand: '',
        category: '',
        sort: 'nombre',
        page: 1,
        limit: 12
    },
    
    // Datos de productos cargados
    products: [],
    filteredProducts: [],
    
    // Elementos del DOM
    elements: {
        searchInput: null,
        brandFilter: null,
        categoryFilter: null,
        sortSelect: null,
        productsGrid: null,
        pagination: null,
        resultsCount: null,
        loadingIndicator: null
    },
    
    /**
     * Inicialización principal
     */
    init: function() {
        this.initElements();
        this.initFilters();
        this.initSearch();
        this.initPagination();
        this.initLazyLoading();
        this.initViewToggle();
        this.initTooltips();
        this.loadProducts();
        
        // console.log('✅ AramedCatalogo initialized');
    },
    
    /**
     * Inicializar elementos del DOM
     */
    initElements: function() {
        this.elements.searchInput = document.querySelector('#searchInput');
        this.elements.brandFilter = document.querySelector('#brandFilter');
        this.elements.categoryFilter = document.querySelector('#categoryFilter');
        this.elements.sortSelect = document.querySelector('#sortSelect');
        this.elements.productsGrid = document.querySelector('.productos-grid');
        this.elements.pagination = document.querySelector('.pagination');
        this.elements.resultsCount = document.querySelector('.results-count');
        this.elements.loadingIndicator = document.querySelector('.loading-indicator');
    },
    
    /**
     * Inicializar sistema de filtros
     */
    initFilters: function() {
        // Filtro por marca
        if (this.elements.brandFilter) {
            this.elements.brandFilter.addEventListener('change', (e) => {
                this.currentFilters.brand = e.target.value;
                this.currentFilters.page = 1;
                this.applyFilters();
            });
        }
        
        // Filtro por categoría
        if (this.elements.categoryFilter) {
            this.elements.categoryFilter.addEventListener('change', (e) => {
                this.currentFilters.category = e.target.value;
                this.currentFilters.page = 1;
                this.applyFilters();
            });
        }
        
        // Ordenamiento
        if (this.elements.sortSelect) {
            this.elements.sortSelect.addEventListener('change', (e) => {
                this.currentFilters.sort = e.target.value;
                this.currentFilters.page = 1;
                this.applyFilters();
            });
        }
        
        // Botones de limpiar filtros
        const clearFiltersBtn = document.querySelector('.clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearFilters();
            });
        }
    },
    
    /**
     * Inicializar búsqueda
     */
    initSearch: function() {
        if (this.elements.searchInput) {
            let searchTimeout;
            
            this.elements.searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.currentFilters.search = e.target.value.toLowerCase().trim();
                    this.currentFilters.page = 1;
                    this.applyFilters();
                }, 300); // Debounce de 300ms
            });
            
            // Buscar al presionar Enter
            this.elements.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.currentFilters.search = e.target.value.toLowerCase().trim();
                    this.currentFilters.page = 1;
                    this.applyFilters();
                }
            });
        }
    },
    
    /**
     * Inicializar paginación
     * NOTA: Paginación manejada por el servidor, no por JavaScript
     */
    initPagination: function() {
        // La paginación se maneja del lado del servidor
        // No interceptamos los clics para permitir navegación normal
        if (this.elements.pagination) {
            this.elements.pagination.addEventListener('click', (e) => {
                // Solo agregamos smooth scroll, no prevenimos el comportamiento por defecto
                if (e.target.classList.contains('page-link')) {
                    this.scrollToProducts();
                }
            });
        }
    },
    
    /**
     * Inicializar lazy loading
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
            
            document.querySelectorAll('.producto-image').forEach(img => {
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
     * Toggle entre vista de grid y lista
     */
    initViewToggle: function() {
        const gridBtn = document.querySelector('[data-view="grid"]');
        const listBtn = document.querySelector('[data-view="list"]');
        const productsGrid = document.querySelector('.productos-grid');
        
        if (!gridBtn || !listBtn || !productsGrid) return;
        
        // Función para cambiar vista
        function toggleView(view) {
            // Actualizar botones
            document.querySelectorAll('[data-view]').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-view="${view}"]`).classList.add('active');
            
            // Cambiar clases del grid
            const row = productsGrid.querySelector('.row');
            if (row) {
                if (view === 'list') {
                    row.classList.add('list-view');
                    row.querySelectorAll('.producto-card').forEach(item => {
                        item.classList.add('col-12');
                        item.classList.remove('col-lg-4', 'col-md-6');
                    });
                } else {
                    row.classList.remove('list-view');
                    row.querySelectorAll('.producto-card').forEach(item => {
                        item.classList.remove('col-12');
                        item.classList.add('col-lg-4', 'col-md-6');
                    });
                }
            }
        }
        
        // Event listeners
        gridBtn.addEventListener('click', () => toggleView('grid'));
        listBtn.addEventListener('click', () => toggleView('list'));
        
        // Guardar preferencia en localStorage
        const savedView = localStorage.getItem('catalogo-view');
        if (savedView) {
            toggleView(savedView);
        }
        
        // Guardar al cambiar
        document.querySelectorAll('[data-view]').forEach(btn => {
            btn.addEventListener('click', () => {
                localStorage.setItem('catalogo-view', btn.dataset.view);
            });
        });
    },
    
    /**
     * Cargar productos desde el servidor
     */
    loadProducts: function() {
        // Los productos ya se cargan del lado del servidor en PHP
        // No necesitamos hacer llamadas AJAX adicionales
        console.log('Productos cargados del lado del servidor');
    },
    
    /**
     * Aplicar filtros a los productos
     */
    applyFilters: function() {
        let filtered = [...this.products];
        
        // Filtro por búsqueda
        if (this.currentFilters.search) {
            filtered = filtered.filter(producto => 
                producto.nombre.toLowerCase().includes(this.currentFilters.search) ||
                producto.descripcion.toLowerCase().includes(this.currentFilters.search) ||
                producto.marca.toLowerCase().includes(this.currentFilters.search)
            );
        }
        
        // Filtro por marca
        if (this.currentFilters.brand) {
            filtered = filtered.filter(producto => 
                producto.marca_id === parseInt(this.currentFilters.brand)
            );
        }
        
        // Filtro por categoría
        if (this.currentFilters.category) {
            filtered = filtered.filter(producto => 
                producto.categoria_id === parseInt(this.currentFilters.category)
            );
        }
        
        // Ordenamiento
        filtered = this.sortProducts(filtered, this.currentFilters.sort);
        
        this.filteredProducts = filtered;
        this.renderProducts();
        this.renderPagination();
        this.updateResultsCount();
        this.updateFiltersUI();
    },
    
    /**
     * Ordenar productos
     */
    sortProducts: function(products, sortBy) {
        switch (sortBy) {
            case 'nombre':
                return products.sort((a, b) => a.nombre.localeCompare(b.nombre));
            case 'precio_asc':
                return products.sort((a, b) => a.precio - b.precio);
            case 'precio_desc':
                return products.sort((a, b) => b.precio - a.precio);
            case 'marca':
                return products.sort((a, b) => a.marca.localeCompare(b.marca));
            case 'categoria':
                return products.sort((a, b) => a.categoria.localeCompare(b.categoria));
            default:
                return products;
        }
    },
    
    /**
     * Renderizar productos en el grid
     */
    renderProducts: function() {
        if (!this.elements.productsGrid) return;
        
        const startIndex = (this.currentFilters.page - 1) * this.currentFilters.limit;
        const endIndex = startIndex + this.currentFilters.limit;
        const pageProducts = this.filteredProducts.slice(startIndex, endIndex);
        
        if (pageProducts.length === 0) {
            this.elements.productsGrid.innerHTML = this.getNoResultsHTML();
            return;
        }
        
        const productsHTML = pageProducts.map(producto => this.getProductCardHTML(producto)).join('');
        this.elements.productsGrid.innerHTML = `
            <div class="row">
                ${productsHTML}
            </div>
        `;
        
        // Reinicializar lazy loading para las nuevas imágenes
        this.initLazyLoading();
        
        // Animación de entrada
        this.animateProducts();
    },
    
    /**
     * Obtener HTML de una tarjeta de producto
     */
    getProductCardHTML: function(producto) {
        const imageUrl = producto.imagen_principal || '/assets/images/productos/placeholder.jpg';
        const productUrl = `/producto.php?id=${producto.id}`;
        
        return `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="producto-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="producto-image-wrapper">
                        <a href="${productUrl}">
                            <img src="${imageUrl}" 
                                 alt="${producto.nombre}" 
                                 class="producto-image loading-skeleton"
                                 loading="lazy">
                        </a>
                        <div class="producto-badges">
                            ${producto.es_nuevo ? '<span class="badge bg-success">Nuevo</span>' : ''}
                            ${producto.es_destacado ? '<span class="badge bg-warning">Destacado</span>' : ''}
                        </div>
                    </div>
                    <div class="producto-info">
                        <div class="producto-brand">${producto.marca}</div>
                        <h3 class="producto-title">
                            <a href="${productUrl}">${producto.nombre}</a>
                        </h3>
                        <p class="producto-description">${producto.descripcion_corta}</p>
                        <div class="producto-price">${this.formatPrice(producto.precio)}</div>
                        <div class="producto-actions">
                            <a href="${productUrl}" class="btn btn-primary">
                                <i class="bi bi-eye me-1"></i>Ver Detalles
                            </a>
                            <a href="#newsletter" class="btn btn-outline-primary">
                                <i class="bi bi-envelope me-1"></i>Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },
    
    /**
     * Obtener HTML cuando no hay resultados
     */
    getNoResultsHTML: function() {
        return `
            <div class="no-results text-center py-5">
                <i class="bi bi-search display-1 text-muted mb-3"></i>
                <h3 class="text-muted">No se encontraron productos</h3>
                <p class="text-muted mb-4">Intenta ajustar tus filtros de búsqueda</p>
                <button class="btn btn-primary clear-filters">
                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar Filtros
                </button>
            </div>
        `;
    },
    
    /**
     * Renderizar paginación
     */
    renderPagination: function() {
        if (!this.elements.pagination) return;
        
        const totalPages = Math.ceil(this.filteredProducts.length / this.currentFilters.limit);
        
        if (totalPages <= 1) {
            this.elements.pagination.innerHTML = '';
            return;
        }
        
        let paginationHTML = '<nav aria-label="Paginación de productos"><ul class="pagination justify-content-center">';
        
        // Botón anterior
        if (this.currentFilters.page > 1) {
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${this.currentFilters.page - 1}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;
        }
        
        // Páginas
        const startPage = Math.max(1, this.currentFilters.page - 2);
        const endPage = Math.min(totalPages, this.currentFilters.page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentFilters.page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Botón siguiente
        if (this.currentFilters.page < totalPages) {
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${this.currentFilters.page + 1}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;
        }
        
        paginationHTML += '</ul></nav>';
        this.elements.pagination.innerHTML = paginationHTML;
    },
    
    /**
     * Actualizar contador de resultados
     */
    updateResultsCount: function() {
        if (!this.elements.resultsCount) return;
        
        const total = this.filteredProducts.length;
        const start = (this.currentFilters.page - 1) * this.currentFilters.limit + 1;
        const end = Math.min(start + this.currentFilters.limit - 1, total);
        
        if (total === 0) {
            this.elements.resultsCount.textContent = 'No se encontraron productos';
        } else if (total <= this.currentFilters.limit) {
            this.elements.resultsCount.textContent = `${total} producto${total !== 1 ? 's' : ''} encontrado${total !== 1 ? 's' : ''}`;
        } else {
            this.elements.resultsCount.textContent = `Mostrando ${start}-${end} de ${total} productos`;
        }
    },
    
    /**
     * Actualizar UI de filtros
     */
    updateFiltersUI: function() {
        // Actualizar contadores en filtros
        this.updateFilterCounts();
        
        // Mostrar filtros activos
        this.showActiveFilters();
    },
    
    /**
     * Actualizar contadores en filtros
     */
    updateFilterCounts: function() {
        // Contar productos por marca
        const brandCounts = {};
        const categoryCounts = {};
        
        this.products.forEach(producto => {
            brandCounts[producto.marca_id] = (brandCounts[producto.marca_id] || 0) + 1;
            categoryCounts[producto.categoria_id] = (categoryCounts[producto.categoria_id] || 0) + 1;
        });
        
        // Actualizar contadores en el DOM
        document.querySelectorAll('.filter-count').forEach(element => {
            const filterType = element.dataset.filterType;
            const filterId = element.dataset.filterId;
            
            if (filterType === 'brand' && brandCounts[filterId]) {
                element.textContent = `(${brandCounts[filterId]})`;
            } else if (filterType === 'category' && categoryCounts[filterId]) {
                element.textContent = `(${categoryCounts[filterId]})`;
            }
        });
    },
    
    /**
     * Mostrar filtros activos
     */
    showActiveFilters: function() {
        const activeFiltersContainer = document.querySelector('.active-filters');
        if (!activeFiltersContainer) return;
        
        const activeFilters = [];
        
        if (this.currentFilters.search) {
            activeFilters.push({
                type: 'search',
                label: `Búsqueda: "${this.currentFilters.search}"`,
                value: this.currentFilters.search
            });
        }
        
        if (this.currentFilters.brand) {
            const brandOption = this.elements.brandFilter?.querySelector(`option[value="${this.currentFilters.brand}"]`);
            if (brandOption) {
                activeFilters.push({
                    type: 'brand',
                    label: `Marca: ${brandOption.textContent}`,
                    value: this.currentFilters.brand
                });
            }
        }
        
        if (this.currentFilters.category) {
            const categoryOption = this.elements.categoryFilter?.querySelector(`option[value="${this.currentFilters.category}"]`);
            if (categoryOption) {
                activeFilters.push({
                    type: 'category',
                    label: `Categoría: ${categoryOption.textContent}`,
                    value: this.currentFilters.category
                });
            }
        }
        
        if (activeFilters.length === 0) {
            activeFiltersContainer.innerHTML = '';
            return;
        }
        
        const filtersHTML = activeFilters.map(filter => `
            <span class="badge bg-primary me-2 mb-2">
                ${filter.label}
                <button type="button" class="btn-close btn-close-white ms-2" 
                        onclick="AramedCatalogo.removeFilter('${filter.type}', '${filter.value}')"
                        aria-label="Eliminar filtro"></button>
            </span>
        `).join('');
        
        activeFiltersContainer.innerHTML = `
            <div class="d-flex flex-wrap align-items-center">
                <span class="me-2 text-muted">Filtros activos:</span>
                ${filtersHTML}
                <button type="button" class="btn btn-sm btn-outline-secondary clear-filters">
                    Limpiar todos
                </button>
            </div>
        `;
    },
    
    /**
     * Remover filtro específico
     */
    removeFilter: function(type, value) {
        switch (type) {
            case 'search':
                this.currentFilters.search = '';
                if (this.elements.searchInput) {
                    this.elements.searchInput.value = '';
                }
                break;
            case 'brand':
                this.currentFilters.brand = '';
                if (this.elements.brandFilter) {
                    this.elements.brandFilter.value = '';
                }
                break;
            case 'category':
                this.currentFilters.category = '';
                if (this.elements.categoryFilter) {
                    this.elements.categoryFilter.value = '';
                }
                break;
        }
        
        this.currentFilters.page = 1;
        this.applyFilters();
    },
    
    /**
     * Limpiar todos los filtros
     */
    clearFilters: function() {
        this.currentFilters = {
            search: '',
            brand: '',
            category: '',
            sort: 'nombre',
            page: 1,
            limit: 12
        };
        
        // Limpiar inputs
        if (this.elements.searchInput) this.elements.searchInput.value = '';
        if (this.elements.brandFilter) this.elements.brandFilter.value = '';
        if (this.elements.categoryFilter) this.elements.categoryFilter.value = '';
        if (this.elements.sortSelect) this.elements.sortSelect.value = 'nombre';
        
        this.applyFilters();
    },
    
    /**
     * Mostrar indicador de carga
     */
    showLoading: function() {
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = 'block';
        }
    },
    
    /**
     * Ocultar indicador de carga
     */
    hideLoading: function() {
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = 'none';
        }
    },
    
    /**
     * Mostrar error
     */
    showError: function(message) {
        if (this.elements.productsGrid) {
            this.elements.productsGrid.innerHTML = `
                <div class="error-message text-center py-5">
                    <i class="bi bi-exclamation-triangle display-1 text-danger mb-3"></i>
                    <h3 class="text-danger">Error</h3>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary" onclick="AramedCatalogo.loadProducts()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                    </button>
                </div>
            `;
        }
    },
    
    /**
     * Animar productos al cargar
     */
    animateProducts: function() {
        const cards = document.querySelectorAll('.producto-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
    },
    
    /**
     * Scroll a la sección de productos
     */
    scrollToProducts: function() {
        if (this.elements.productsGrid) {
            this.elements.productsGrid.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    },
    
    /**
     * Formatear precio
     */
    formatPrice: function(price) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(price);
    }
};

// ========================================
// UTILIDADES ADICIONALES
// ========================================

/**
 * Función para formatear números
 */
function formatNumber(num) {
    return new Intl.NumberFormat('es-MX').format(num);
}

/**
 * Función para formatear moneda
 */
function formatCurrency(amount, currency = 'MXN') {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Función para truncar texto
 */
function truncateText(text, maxLength = 100) {
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength) + '...';
}

/**
 * Función para generar URL de filtros
 */
function buildFilterURL(params) {
    const currentParams = new URLSearchParams(window.location.search);
    
    Object.keys(params).forEach(key => {
        if (params[key] === '' || params[key] === null || params[key] === undefined) {
            currentParams.delete(key);
        } else {
            currentParams.set(key, params[key]);
        }
    });
    
    return '?' + currentParams.toString();
}

/**
 * Función para mostrar notificación
 */
function showNotification(message, type = 'info') {
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
    
    // Auto-remove después de 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// ========================================
// EVENT LISTENERS GLOBALES
// ========================================

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    AramedCatalogo.init();
});

// Manejar cambios de tamaño de ventana
window.addEventListener('resize', function() {
    clearTimeout(window.resizeTimeout);
    window.resizeTimeout = setTimeout(() => {
        // Recalcular layout si es necesario
        AramedCatalogo.initTooltips();
    }, 250);
});

// Manejar navegación del navegador
window.addEventListener('popstate', function() {
    // Recargar página si es necesario para filtros
    if (window.location.pathname.includes('catalogo.php')) {
        window.location.reload();
    }
});

    /**
     * Inicializar toggle de vista (grid/list)
     */
    initViewToggle: function() {
        const viewButtons = document.querySelectorAll('[data-view]');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const view = btn.dataset.view;
                this.toggleView(view);
            });
        });
    },
    
    /**
     * Cambiar vista entre grid y lista
     */
    toggleView: function(view) {
        const productsGrid = document.querySelector('.products-grid');
        const viewButtons = document.querySelectorAll('[data-view]');
        
        if (!productsGrid) return;
        
        // Remover clases activas
        viewButtons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Activar botón seleccionado
        const activeBtn = document.querySelector(`[data-view="${view}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
        
        // Cambiar vista
        if (view === 'list') {
            productsGrid.classList.add('list-view');
            productsGrid.classList.remove('grid-view');
        } else {
            productsGrid.classList.add('grid-view');
            productsGrid.classList.remove('list-view');
        }
        
        // Guardar preferencia
        localStorage.setItem('catalogo-view', view);
    }
};

// ========================================
// EXPORT PARA USO EN OTROS SCRIPTS
// ========================================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AramedCatalogo;
}