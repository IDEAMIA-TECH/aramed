<?php
/**
 * ========================================
 * FUNCIONES PARA EVENTOS DE ANALYTICS
 * ========================================
 * 
 * Funciones para enviar eventos personalizados a Google Analytics
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
 * Verifica si los eventos de Analytics están activados
 * @return bool
 */
function analyticsEventosActivos() {
    return getConfig('analytics_activar_eventos', '1') === '1';
}

/**
 * Genera código JavaScript para enviar un evento a GA4
 * @param string $event_name Nombre del evento
 * @param array $event_params Parámetros adicionales del evento
 * @return string Código JavaScript
 */
function generateAnalyticsEvent($event_name, $event_params = []) {
    if (!analyticsEventosActivos()) {
        return '';
    }
    
    $params_json = json_encode($event_params, JSON_UNESCAPED_UNICODE);
    
    return "
    <script>
        if (typeof gtag !== 'undefined') {
            gtag('event', '{$event_name}', {$params_json});
        }
    </script>
    ";
}

/**
 * Registra evento de envío de cotización
 * @param array $data Datos de la cotización
 * @return string Código JavaScript
 */
function trackQuoteSubmit($data = []) {
    $params = [
        'event_category' => 'Form',
        'event_label' => 'Quote Request',
        'value' => 1
    ];
    
    if (!empty($data['institucion'])) {
        $params['institution'] = $data['institucion'];
    }
    
    return generateAnalyticsEvent('submit_quote', $params);
}

/**
 * Registra evento de envío de contacto
 * @param array $data Datos del contacto
 * @return string Código JavaScript
 */
function trackContactSubmit($data = []) {
    $params = [
        'event_category' => 'Form',
        'event_label' => 'Contact Form',
        'value' => 1
    ];
    
    if (!empty($data['asunto'])) {
        $params['subject'] = $data['asunto'];
    }
    
    return generateAnalyticsEvent('submit_contact', $params);
}

/**
 * Registra evento de suscripción al newsletter
 * @param array $data Datos de la suscripción
 * @return string Código JavaScript
 */
function trackNewsletterSubscribe($data = []) {
    $params = [
        'event_category' => 'Newsletter',
        'event_label' => 'Subscribe',
        'value' => 1
    ];
    
    return generateAnalyticsEvent('subscribe_newsletter', $params);
}

/**
 * Registra evento de visualización de producto
 * @param int $product_id ID del producto
 * @param string $product_name Nombre del producto
 * @return string Código JavaScript
 */
function trackProductView($product_id, $product_name = '') {
    $params = [
        'event_category' => 'Ecommerce',
        'event_label' => 'Product View',
        'product_id' => $product_id,
        'product_name' => $product_name,
        'value' => 1
    ];
    
    return generateAnalyticsEvent('view_product', $params);
}

/**
 * Registra evento de descarga de documento
 * @param string $document_name Nombre del documento
 * @param string $document_type Tipo de documento
 * @return string Código JavaScript
 */
function trackDocumentDownload($document_name, $document_type = 'pdf') {
    $params = [
        'event_category' => 'Download',
        'event_label' => $document_name,
        'document_type' => $document_type,
        'value' => 1
    ];
    
    return generateAnalyticsEvent('download_document', $params);
}

