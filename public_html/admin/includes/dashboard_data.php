<?php
/**
 * ========================================
 * ADMIN - DASHBOARD DATA API
 * ========================================
 * 
 * Endpoint AJAX para datos del dashboard (gráficas)
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../admin/auth_check.php';

// Verificar permisos
if (function_exists('checkPermission')) {
    checkPermission('dashboard', 'ver');
}

// Headers JSON
header('Content-Type: application/json');

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

// Obtener tipo de datos solicitado
$tipo = $_GET['tipo'] ?? '';

try {
    switch ($tipo) {
        case 'cotizaciones_mes':
            // Gráfica de cotizaciones por mes (últimos 12 meses)
            $data = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $fecha = date('Y-m', strtotime("-$i months"));
                $labels[] = date('M Y', strtotime("-$i months"));
                
                // Contar cotizaciones (desde newsletter_subscriptions o cotizaciones si existe)
                $sql = "SELECT COUNT(*) as total 
                        FROM newsletter_subscriptions 
                        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? 
                        AND status = 'active'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fecha]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $data[] = (int)$result['total'];
            }
            
            echo json_encode([
                'labels' => $labels,
                'data' => $data
            ]);
            break;
            
        case 'suscriptores_mes':
            // Gráfica de suscriptores por mes (últimos 12 meses)
            $data = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $fecha = date('Y-m', strtotime("-$i months"));
                $labels[] = date('M Y', strtotime("-$i months"));
                
                $sql = "SELECT COUNT(*) as total 
                        FROM newsletter_simple 
                        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? 
                        AND status = 'activo'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fecha]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $data[] = (int)$result['total'];
            }
            
            echo json_encode([
                'labels' => $labels,
                'data' => $data
            ]);
            break;
            
        case 'contactos_mes':
            // Gráfica de contactos por mes (últimos 12 meses)
            $data = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $fecha = date('Y-m', strtotime("-$i months"));
                $labels[] = date('M Y', strtotime("-$i months"));
                
                $sql = "SELECT COUNT(*) as total 
                        FROM contact_messages 
                        WHERE DATE_FORMAT(created_at, '%Y-%m') = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fecha]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $data[] = (int)$result['total'];
            }
            
            echo json_encode([
                'labels' => $labels,
                'data' => $data
            ]);
            break;
            
        default:
            echo json_encode(['error' => 'Tipo no válido']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

