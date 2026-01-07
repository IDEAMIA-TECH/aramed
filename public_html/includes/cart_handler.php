<?php
/**
 * ========================================
 * HANDLER AJAX PARA CARRITO
 * ========================================
 * 
 * Maneja las peticiones AJAX para agregar/quitar productos del carrito
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante para permitir carga de funciones
if (!defined('ARAMED_SITE')) {
    define('ARAMED_SITE', true);
}

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar funciones del carrito
require_once __DIR__ . '/cart_functions.php';

// Cargar funciones de sanitización si no están cargadas
if (!function_exists('sanitizeInput')) {
    if (file_exists(__DIR__ . '/functions.php')) {
        require_once __DIR__ . '/functions.php';
    } else {
        function sanitizeInput($input) {
            return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        }
    }
}

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener acción
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            // Agregar producto al carrito
            $producto_id = (int)($_POST['producto_id'] ?? 0);
            $producto_nombre = sanitizeInput($_POST['producto_nombre'] ?? '');
            $producto_codigo = sanitizeInput($_POST['producto_codigo'] ?? '');
            $cantidad = (int)($_POST['cantidad'] ?? 1);
            
            if (!$producto_id || !$producto_nombre) {
                throw new Exception('Datos incompletos');
            }
            
            addToCart($producto_id, $producto_nombre, $producto_codigo, $cantidad);
            
            echo json_encode([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart_count' => getCartCount()
            ]);
            break;
            
        case 'remove':
            // Remover producto del carrito
            $producto_id = (int)($_POST['producto_id'] ?? 0);
            
            if (!$producto_id) {
                throw new Exception('ID de producto requerido');
            }
            
            removeFromCart($producto_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Producto removido del carrito',
                'cart_count' => getCartCount()
            ]);
            break;
            
        case 'update':
            // Actualizar cantidad
            $producto_id = (int)($_POST['producto_id'] ?? 0);
            $cantidad = (int)($_POST['cantidad'] ?? 1);
            
            if (!$producto_id) {
                throw new Exception('ID de producto requerido');
            }
            
            updateCartQuantity($producto_id, $cantidad);
            
            echo json_encode([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'cart_count' => getCartCount()
            ]);
            break;
            
        case 'clear':
            // Limpiar carrito
            clearCart();
            
            echo json_encode([
                'success' => true,
                'message' => 'Carrito limpiado',
                'cart_count' => 0
            ]);
            break;
            
        case 'get':
            // Obtener items del carrito
            $items = getCartItems();
            
            echo json_encode([
                'success' => true,
                'items' => $items,
                'cart_count' => getCartCount()
            ]);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

