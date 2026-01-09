<?php
/**
 * ========================================
 * TRACKING DE CLICKS EN EMAILS
 * ========================================
 * 
 * Script para rastrear clicks en links de emails
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/connection.php';

// Obtener parámetros
$envio_id = isset($_GET['e']) ? (int)$_GET['e'] : 0;
$campana_id = isset($_GET['c']) ? (int)$_GET['c'] : 0;
$url = isset($_GET['url']) ? urldecode($_GET['url']) : '';

if (!$envio_id && !$campana_id) {
    // Si no hay parámetros válidos, redirigir a URL si existe
    if ($url) {
        header('Location: ' . $url);
    } else {
        header('Location: /');
    }
    exit;
}

try {
    $pdo = getDB();
    
    // Registrar click
    if ($envio_id) {
        // Actualizar por ID de envío
        $stmt = $pdo->prepare("UPDATE newsletter_envios 
                               SET clic_at = NOW(), 
                                   estado = CASE 
                                       WHEN estado IN ('enviado', 'abierto') THEN 'clic' 
                                       WHEN estado = 'clic' THEN 'clic'
                                       ELSE estado 
                                   END,
                                   updated_at = NOW()
                               WHERE id = ? AND (clic_at IS NULL OR clic_at = '0000-00-00 00:00:00' OR DATE(clic_at) = '0000-00-00')");
        $stmt->execute([$envio_id]);
    } elseif ($campana_id) {
        // Si solo tenemos campana_id, buscar por email
        $email = isset($_GET['email']) ? sanitizeInput($_GET['email']) : '';
        if ($email) {
            $stmt = $pdo->prepare("UPDATE newsletter_envios 
                                   SET clic_at = NOW(), 
                                       estado = CASE 
                                           WHEN estado IN ('enviado', 'abierto') THEN 'clic' 
                                           WHEN estado = 'clic' THEN 'clic'
                                           ELSE estado 
                                       END,
                                       updated_at = NOW()
                                   WHERE campana_id = ? AND email = ? 
                                   AND estado != 'fallido'
                                   AND (clic_at IS NULL OR clic_at = '0000-00-00 00:00:00' OR DATE(clic_at) = '0000-00-00')
                                   ORDER BY enviado_at DESC
                                   LIMIT 1");
            $stmt->execute([$campana_id, $email]);
        }
    }
    
    // Redirigir a la URL original
    if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
        header('Location: ' . $url);
    } elseif ($url && (strpos($url, '/') === 0 || strpos($url, 'http') === false)) {
        // URL relativa o sin protocolo
        header('Location: ' . siteUrl($url));
    } else {
        header('Location: /');
    }
    
} catch (Exception $e) {
    // En caso de error, redirigir de todas formas
    if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
        header('Location: ' . $url);
    } else {
        header('Location: /');
    }
}

