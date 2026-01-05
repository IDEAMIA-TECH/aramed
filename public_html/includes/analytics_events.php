<?php
/**
 * ========================================
 * ARAMED - ANALYTICS EVENTS
 * ========================================
 * 
 * Funciones para enviar eventos personalizados a Google Analytics 4
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}

/**
 * Obtener el Measurement ID de Google Analytics desde la configuración
 * 
 * @return string|null Measurement ID o null si no está configurado
 */
function getAnalyticsMeasurementId() {
    if (function_exists('getConfig')) {
        $measurement_id = getConfig('analytics_measurement_id');
        if (!empty($measurement_id)) {
            return $measurement_id;
        }
    }
    
    // Fallback: buscar en config.php
    if (defined('GA_MEASUREMENT_ID')) {
        return GA_MEASUREMENT_ID;
    }
    
    return null;
}

/**
 * Verificar si Analytics está habilitado
 * 
 * @return bool True si Analytics está habilitado
 */
function isAnalyticsEnabled() {
    if (function_exists('getConfig')) {
        $enabled = getConfig('analytics_enabled', '0');
        return $enabled === '1' || $enabled === 1;
    }
    
    return true; // Por defecto habilitado
}

/**
 * Generar el script de Google Analytics 4
 * 
 * @return string HTML con el script de GA4
 */
function getAnalyticsScript() {
    $measurement_id = getAnalyticsMeasurementId();
    
    if (empty($measurement_id) || !isAnalyticsEnabled()) {
        return '';
    }
    
    $script = <<<HTML
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$measurement_id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$measurement_id}');
</script>
HTML;
    
    return $script;
}

/**
 * Generar código JavaScript para enviar un evento personalizado
 * 
 * @param string $event_name Nombre del evento
 * @param array $event_params Parámetros adicionales del evento
 * @return string Código JavaScript
 */
function generateAnalyticsEvent($event_name, $event_params = []) {
    if (!isAnalyticsEnabled()) {
        return '';
    }
    
    $params_json = json_encode($event_params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    return "gtag('event', '{$event_name}', {$params_json});";
}

/**
 * Evento: Agregar producto a cotización
 * 
 * @param int $producto_id ID del producto
 * @param string $producto_nombre Nombre del producto
 * @param float $precio Precio del producto
 * @return string Código JavaScript
 */
function trackAddToQuote($producto_id, $producto_nombre, $precio = null) {
    $params = [
        'event_category' => 'Cotización',
        'event_label' => $producto_nombre,
        'value' => $precio,
        'product_id' => $producto_id
    ];
    
    if ($precio !== null) {
        $params['currency'] = 'MXN';
    }
    
    return generateAnalyticsEvent('add_to_quote', $params);
}

/**
 * Evento: Enviar cotización
 * 
 * @param string $cliente_nombre Nombre del cliente
 * @param int $total_items Total de productos en la cotización
 * @param float $total_precio Precio total (opcional)
 * @return string Código JavaScript
 */
function trackSubmitQuote($cliente_nombre, $total_items, $total_precio = null) {
    $params = [
        'event_category' => 'Cotización',
        'event_label' => $cliente_nombre,
        'items_count' => $total_items
    ];
    
    if ($total_precio !== null) {
        $params['value'] = $total_precio;
        $params['currency'] = 'MXN';
    }
    
    return generateAnalyticsEvent('submit_quote', $params);
}

/**
 * Evento: Enviar formulario de contacto
 * 
 * @param string $asunto Asunto del mensaje
 * @param string $motivo Motivo del contacto
 * @return string Código JavaScript
 */
function trackSubmitContact($asunto, $motivo = null) {
    $params = [
        'event_category' => 'Contacto',
        'event_label' => $asunto
    ];
    
    if ($motivo !== null) {
        $params['contact_reason'] = $motivo;
    }
    
    return generateAnalyticsEvent('submit_contact', $params);
}

/**
 * Evento: Suscribirse al newsletter
 * 
 * @param string $email Email del suscriptor
 * @param string $origen Origen de la suscripción (ej: 'home', 'footer', 'popup')
 * @return string Código JavaScript
 */
function trackSubscribeNewsletter($email, $origen = 'unknown') {
    $params = [
        'event_category' => 'Newsletter',
        'event_label' => $origen,
        'method' => 'email'
    ];
    
    return generateAnalyticsEvent('subscribe_newsletter', $params);
}

/**
 * Evento: Ver producto
 * 
 * @param int $producto_id ID del producto
 * @param string $producto_nombre Nombre del producto
 * @param string $categoria Categoría del producto
 * @return string Código JavaScript
 */
function trackViewProduct($producto_id, $producto_nombre, $categoria = null) {
    $params = [
        'event_category' => 'Producto',
        'event_label' => $producto_nombre,
        'product_id' => $producto_id
    ];
    
    if ($categoria !== null) {
        $params['product_category'] = $categoria;
    }
    
    return generateAnalyticsEvent('view_product', $params);
}

/**
 * Evento: Ver proyecto
 * 
 * @param int $proyecto_id ID del proyecto
 * @param string $proyecto_titulo Título del proyecto
 * @param string $sector Sector del proyecto
 * @return string Código JavaScript
 */
function trackViewProject($proyecto_id, $proyecto_titulo, $sector = null) {
    $params = [
        'event_category' => 'Proyecto',
        'event_label' => $proyecto_titulo,
        'project_id' => $proyecto_id
    ];
    
    if ($sector !== null) {
        $params['project_sector'] = $sector;
    }
    
    return generateAnalyticsEvent('view_project', $params);
}

/**
 * Evento: Descargar documento
 * 
 * @param string $documento_nombre Nombre del documento
 * @param string $tipo Tipo de documento (ficha_tecnica, manual, brochure, etc.)
 * @param int $producto_id ID del producto relacionado (opcional)
 * @return string Código JavaScript
 */
function trackDownloadDocument($documento_nombre, $tipo, $producto_id = null) {
    $params = [
        'event_category' => 'Descarga',
        'event_label' => $documento_nombre,
        'document_type' => $tipo
    ];
    
    if ($producto_id !== null) {
        $params['product_id'] = $producto_id;
    }
    
    return generateAnalyticsEvent('download_document', $params);
}

/**
 * Evento: Buscar en catálogo
 * 
 * @param string $busqueda Término de búsqueda
 * @param int $resultados_count Cantidad de resultados
 * @return string Código JavaScript
 */
function trackCatalogSearch($busqueda, $resultados_count = 0) {
    $params = [
        'event_category' => 'Búsqueda',
        'event_label' => $busqueda,
        'search_term' => $busqueda,
        'results_count' => $resultados_count
    ];
    
    return generateAnalyticsEvent('search', $params);
}

/**
 * Generar código JavaScript completo para eventos comunes
 * Debe incluirse en el footer del sitio
 * 
 * @return string Código JavaScript completo
 */
function getAnalyticsEventsScript() {
    if (!isAnalyticsEnabled()) {
        return '';
    }
    
    $script = <<<'JS'
<script>
// Función helper para enviar eventos de Analytics
function sendAnalyticsEvent(eventName, eventParams) {
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, eventParams);
    }
}

// Eventos automáticos en formularios
document.addEventListener('DOMContentLoaded', function() {
    // Formulario de contacto
    const contactForm = document.getElementById('contact-form') || document.querySelector('form[action*="contact"]');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const asunto = this.querySelector('[name="asunto"]')?.value || 'Sin asunto';
            const motivo = this.querySelector('[name="motivo"]')?.value || null;
            sendAnalyticsEvent('submit_contact', {
                event_category: 'Contacto',
                event_label: asunto,
                contact_reason: motivo
            });
        });
    }
    
    // Formulario de cotización
    const quoteForm = document.getElementById('quote-form') || document.querySelector('form[action*="cotizacion"]');
    if (quoteForm) {
        quoteForm.addEventListener('submit', function(e) {
            const items = this.querySelectorAll('[data-product-id]').length || 0;
            sendAnalyticsEvent('submit_quote', {
                event_category: 'Cotización',
                items_count: items
            });
        });
    }
    
    // Formulario de newsletter
    const newsletterForm = document.getElementById('newsletter-form') || document.querySelector('form[action*="newsletter"]');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            const origen = this.dataset.origin || 'unknown';
            sendAnalyticsEvent('subscribe_newsletter', {
                event_category: 'Newsletter',
                event_label: origen,
                method: 'email'
            });
        });
    }
    
    // Botones de agregar a cotización
    document.querySelectorAll('[data-action="add-to-quote"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName || 'Producto';
            sendAnalyticsEvent('add_to_quote', {
                event_category: 'Cotización',
                event_label: productName,
                product_id: productId
            });
        });
    });
    
    // Enlaces de descarga de documentos
    document.querySelectorAll('a[href*=".pdf"], a[href*=".doc"], a[download]').forEach(function(link) {
        link.addEventListener('click', function() {
            const fileName = this.getAttribute('download') || this.href.split('/').pop();
            sendAnalyticsEvent('download_document', {
                event_category: 'Descarga',
                event_label: fileName
            });
        });
    });
});
</script>
JS;
    
    return $script;
}
