<?php
/**
 * ========================================
 * ADMIN - DASHBOARD ALERTS
 * ========================================
 * 
 * Genera alertas automáticas para el dashboard
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
 * Obtiene alertas automáticas para el dashboard
 * @param PDO $pdo Conexión a la base de datos
 * @return array Lista de alertas
 */
function getDashboardAlerts($pdo) {
    $alerts = [];
    
    try {
        // Mensajes de contacto "Abiertos" con más de 3 días
        $sql = "SELECT COUNT(*) as total 
                FROM contact_messages 
                WHERE status IN ('nuevo', 'en_proceso') 
                AND DATE(created_at) < DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'bi-exclamation-triangle',
                'title' => 'Mensajes de contacto pendientes',
                'message' => "Hay {$result['total']} mensaje(s) de contacto sin resolver por más de 3 días",
                'link' => 'contacto/index.php?estado=nuevo',
                'link_text' => 'Ver mensajes'
            ];
        }
        
        // Cotizaciones "Nuevas" sin asignar
        $sql = "SELECT COUNT(*) as total 
                FROM newsletter_subscriptions 
                WHERE status = 'active' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'bi-envelope',
                'title' => 'Nuevas cotizaciones',
                'message' => "Hay {$result['total']} nueva(s) cotización(es) en los últimos 7 días",
                'link' => 'cotizaciones/index.php?estado=nueva',
                'link_text' => 'Ver cotizaciones'
            ];
        }
        
        // Verificar si existe tabla cotizaciones
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM cotizaciones 
                    WHERE estado_cotizacion = 'nueva' 
                    AND assigned_to IS NULL";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => 'bi-file-earmark-text',
                    'title' => 'Cotizaciones sin asignar',
                    'message' => "Hay {$result['total']} cotización(es) nuevas sin asignar a un ejecutivo",
                    'link' => 'cotizaciones/index.php?estado=nueva',
                    'link_text' => 'Asignar cotizaciones'
                ];
            }
        } catch (Exception $e) {
            // Tabla no existe aún, ignorar
        }
        
        // Productos sin categoría o sin ficha técnica
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM catalogo_productos 
                    WHERE estado = 'activo' 
                    AND (categoria_id IS NULL OR categoria_id = 0)";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'bi-box-seam',
                    'title' => 'Productos sin categoría',
                    'message' => "Hay {$result['total']} producto(s) activo(s) sin categoría asignada",
                    'link' => 'catalogo/productos/index.php',
                    'link_text' => 'Gestionar productos'
                ];
            }
        } catch (Exception $e) {
            // Tabla no existe aún, ignorar
        }
        
        // Comentarios pendientes de moderación
        $sql = "SELECT COUNT(*) as total 
                FROM blog_comentarios 
                WHERE estado = 'pendiente'";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'bi-chat-dots',
                'title' => 'Comentarios pendientes',
                'message' => "Hay {$result['total']} comentario(s) pendiente(s) de moderación",
                'link' => 'blog/comentarios.php?estado=pendiente',
                'link_text' => 'Moderar comentarios'
            ];
        }
        
    } catch (Exception $e) {
        // Ignorar errores
    }
    
    return $alerts;
}

