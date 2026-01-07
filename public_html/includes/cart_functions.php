<?php
/**
 * ========================================
 * FUNCIONES DE CARRITO DE COTIZACIONES
 * ========================================
 * 
 * Funciones para manejar el carrito de productos para cotizaciones
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Inicializar el carrito si no existe
 */
function initCart() {
    if (!isset($_SESSION['quote_cart'])) {
        $_SESSION['quote_cart'] = [];
    }
}

/**
 * Obtener todos los items del carrito
 * 
 * @return array Array de items del carrito
 */
function getCartItems() {
    initCart();
    return $_SESSION['quote_cart'] ?? [];
}

/**
 * Obtener el número total de items en el carrito
 * 
 * @return int Número total de items
 */
function getCartCount() {
    $items = getCartItems();
    $count = 0;
    foreach ($items as $item) {
        $count += $item['cantidad'] ?? 1;
    }
    return $count;
}

/**
 * Agregar un producto al carrito
 * 
 * @param int $producto_id ID del producto
 * @param string $producto_nombre Nombre del producto
 * @param string $producto_codigo Código del producto
 * @param int $cantidad Cantidad (default: 1)
 * @return bool True si se agregó correctamente
 */
function addToCart($producto_id, $producto_nombre, $producto_codigo = '', $cantidad = 1) {
    initCart();
    
    // Verificar si el producto ya está en el carrito
    $found = false;
    foreach ($_SESSION['quote_cart'] as $key => $item) {
        if ($item['producto_id'] == $producto_id) {
            // Incrementar cantidad
            $_SESSION['quote_cart'][$key]['cantidad'] += $cantidad;
            $found = true;
            break;
        }
    }
    
    // Si no está, agregarlo
    if (!$found) {
        $_SESSION['quote_cart'][] = [
            'producto_id' => (int)$producto_id,
            'producto_nombre' => $producto_nombre,
            'producto_codigo' => $producto_codigo,
            'cantidad' => (int)$cantidad
        ];
    }
    
    return true;
}

/**
 * Remover un producto del carrito
 * 
 * @param int $producto_id ID del producto a remover
 * @return bool True si se removió correctamente
 */
function removeFromCart($producto_id) {
    initCart();
    
    foreach ($_SESSION['quote_cart'] as $key => $item) {
        if ($item['producto_id'] == $producto_id) {
            unset($_SESSION['quote_cart'][$key]);
            $_SESSION['quote_cart'] = array_values($_SESSION['quote_cart']); // Reindexar
            return true;
        }
    }
    
    return false;
}

/**
 * Actualizar la cantidad de un producto en el carrito
 * 
 * @param int $producto_id ID del producto
 * @param int $cantidad Nueva cantidad
 * @return bool True si se actualizó correctamente
 */
function updateCartQuantity($producto_id, $cantidad) {
    initCart();
    
    if ($cantidad <= 0) {
        return removeFromCart($producto_id);
    }
    
    foreach ($_SESSION['quote_cart'] as $key => $item) {
        if ($item['producto_id'] == $producto_id) {
            $_SESSION['quote_cart'][$key]['cantidad'] = (int)$cantidad;
            return true;
        }
    }
    
    return false;
}

/**
 * Limpiar el carrito completamente
 * 
 * @return bool True si se limpió correctamente
 */
function clearCart() {
    initCart();
    $_SESSION['quote_cart'] = [];
    return true;
}

/**
 * Obtener información completa de los productos del carrito desde la BD
 * 
 * @return array Array de productos con información completa
 */
function getCartProductsInfo() {
    $items = getCartItems();
    
    if (empty($items)) {
        return [];
    }
    
    try {
        // Cargar connection.php si getDB no existe
        if (!function_exists('getDB')) {
            if (file_exists(__DIR__ . '/connection.php')) {
                require_once __DIR__ . '/connection.php';
            } else {
                return [];
            }
        }
        
        $pdo = getDB();
        if (!$pdo) {
            return [];
        }
        
        $product_ids = array_column($items, 'producto_id');
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        
        $sql = "
            SELECT p.*, m.nombre as marca_nombre, c.nombre as categoria_nombre,
                   i.imagen_url
            FROM catalogo_productos p
            LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
            LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
            LEFT JOIN catalogo_producto_imagenes i ON p.id = i.producto_id AND i.es_principal = 1
            WHERE p.id IN ($placeholders) AND p.estado = 'activo'
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combinar información del carrito con información de la BD
        $result = [];
        foreach ($items as $cart_item) {
            foreach ($products as $product) {
                if ($product['id'] == $cart_item['producto_id']) {
                    $result[] = array_merge($product, [
                        'cantidad' => $cart_item['cantidad'],
                        'cart_key' => $cart_item['producto_id']
                    ]);
                    break;
                }
            }
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Error obteniendo información del carrito: " . $e->getMessage());
        return [];
    }
}

