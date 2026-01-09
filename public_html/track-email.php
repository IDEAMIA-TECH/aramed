<?php
/**
 * ========================================
 * TRACKING DE EMAILS - PIXEL DE SEGUIMIENTO
 * ========================================
 * 
 * Script para rastrear aperturas de emails (pixel de 1x1)
 * y clicks en links
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
$action = isset($_GET['a']) ? $_GET['a'] : 'open'; // 'open' o 'click'

if (!$envio_id && !$campana_id) {
    // Si no hay parámetros válidos, mostrar pixel transparente
    header('Content-Type: image/png');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    // Imagen PNG 1x1 transparente
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    exit;
}

try {
    $pdo = getDB();
    
    if ($action === 'open') {
        // Registrar apertura
        if ($envio_id) {
            $stmt = $pdo->prepare("UPDATE newsletter_envios 
                                   SET abierto_at = NOW(), 
                                       estado = CASE 
                                           WHEN estado = 'enviado' THEN 'abierto' 
                                           WHEN estado = 'clic' THEN 'clic'
                                           ELSE estado 
                                       END,
                                       updated_at = NOW()
                                   WHERE id = ? 
                                   AND estado != 'fallido'
                                   AND (abierto_at IS NULL OR abierto_at = '0000-00-00 00:00:00' OR DATE(abierto_at) = '0000-00-00')");
            $stmt->execute([$envio_id]);
        } elseif ($campana_id) {
            // Si solo tenemos campana_id, buscar por email
            $email = isset($_GET['email']) ? sanitizeInput($_GET['email']) : '';
            if ($email) {
                $stmt = $pdo->prepare("UPDATE newsletter_envios 
                                       SET abierto_at = NOW(), 
                                           estado = CASE 
                                               WHEN estado = 'enviado' THEN 'abierto' 
                                               WHEN estado = 'clic' THEN 'clic'
                                               ELSE estado 
                                           END,
                                           updated_at = NOW()
                                       WHERE campana_id = ? 
                                       AND email = ? 
                                       AND estado != 'fallido'
                                       AND (abierto_at IS NULL OR abierto_at = '0000-00-00 00:00:00' OR DATE(abierto_at) = '0000-00-00')
                                       ORDER BY enviado_at DESC
                                       LIMIT 1");
                $stmt->execute([$campana_id, $email]);
            }
        }
    } elseif ($action === 'click') {
        // Registrar click (se maneja principalmente en track-click.php)
        // Este caso es solo para compatibilidad
    }
    
    // Devolver pixel transparente de 1x1
    header('Content-Type: image/png');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Pragma: no-cache');
    
    // Imagen PNG 1x1 transparente
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    
} catch (Exception $e) {
    // En caso de error, devolver pixel transparente de todas formas
    header('Content-Type: image/png');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
}

