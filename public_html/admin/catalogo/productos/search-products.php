<?php
/**
 * ========================================
 * ADMIN - BÚSQUEDA DE PRODUCTOS PARA EMAILS
 * ========================================
 * 
 * Endpoint AJAX para buscar productos y usarlos en plantillas de email
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/connection.php';
require_once __DIR__ . '/../../auth_check.php';

// Verificar permisos RBAC (permitir también si tiene permiso de catálogo)
$has_permission = false;
if (function_exists('checkPermission')) {
    try {
        if (function_exists('hasPermission')) {
            $user_id = $_SESSION['admin_user_id'] ?? 0;
            $has_permission = hasPermission($user_id, 'newsletter', 'editar') || hasPermission($user_id, 'catalogo', 'ver');
        } else {
            checkPermission('newsletter', 'editar');
            $has_permission = true;
        }
    } catch (Exception $e) {
        // Si falla la verificación, permitir si es admin
        $user_role = $_SESSION['admin_rol'] ?? 'editor';
        $has_permission = ($user_role === 'admin');
    }
} else {
    // Si no hay sistema RBAC, permitir a todos los usuarios autenticados
    $has_permission = isset($_SESSION['admin_user_id']);
}

if (!$has_permission) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para acceder a esta funcionalidad', 'products' => [], 'count' => 0]);
    exit;
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

// Obtener parámetro de búsqueda
$query = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

// Configurar headers JSON primero para evitar errores
header('Content-Type: application/json; charset=utf-8');

// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    if (empty($query) || strlen($query) < 2) {
        echo json_encode(['success' => false, 'message' => 'La búsqueda debe tener al menos 2 caracteres', 'products' => [], 'count' => 0]);
        exit;
    }

    // Buscar productos - Usar CONCAT para simplificar
    $search_term = '%' . $query . '%';
    
    // Primero verificar si hay productos publicados
    $check_stmt = $pdo->query("SELECT COUNT(*) as total FROM catalogo_productos WHERE estado = 'publicado'");
    $check_result = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($check_result['total'] == 0) {
        echo json_encode([
            'success' => true,
            'products' => [],
            'count' => 0,
            'message' => 'No hay productos publicados en el catálogo',
            'query' => $query
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    // Buscar productos usando múltiples parámetros
    $sql = "SELECT p.id, p.nombre, p.codigo, p.slug, p.descripcion_corta, 
                   p.imagen_principal, p.estado,
                   COALESCE(m.nombre, '') as marca_nombre,
                   COALESCE(c.nombre, '') as categoria_nombre
            FROM catalogo_productos p
            LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
            LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
            WHERE p.estado = 'publicado' 
            AND (
                p.nombre LIKE ? 
                OR p.codigo LIKE ? 
                OR p.descripcion_corta LIKE ?
                OR m.nombre LIKE ?
                OR c.nombre LIKE ?
            )
            ORDER BY p.destacado DESC, p.created_at DESC
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $limit
    ]);
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Normalizar URLs de imágenes
    foreach ($products as &$product) {
        if (!empty($product['imagen_principal'])) {
            if (function_exists('imageUrl')) {
                $product['imagen_principal'] = imageUrl($product['imagen_principal']);
            } else {
                // Fallback: construir URL manualmente
                if (strpos($product['imagen_principal'], 'http') === 0) {
                    // Ya es una URL completa
                } elseif (strpos($product['imagen_principal'], '/assets/') === 0) {
                    // Ya tiene la ruta correcta
                    $product['imagen_principal'] = (defined('SITE_URL') ? SITE_URL : 'https://aramedylaboratorio.com') . $product['imagen_principal'];
                } else {
                    $product['imagen_principal'] = (defined('SITE_URL') ? SITE_URL : 'https://aramedylaboratorio.com') . '/assets/images/' . ltrim($product['imagen_principal'], '/');
                }
            }
        }
    }
    
    // Asegurar que los valores NULL sean strings vacíos para JSON
    foreach ($products as &$product) {
        $product['nombre'] = $product['nombre'] ?? '';
        $product['codigo'] = $product['codigo'] ?? '';
        $product['descripcion_corta'] = $product['descripcion_corta'] ?? '';
        $product['imagen_principal'] = $product['imagen_principal'] ?? '';
        $product['marca_nombre'] = $product['marca_nombre'] ?? '';
        $product['categoria_nombre'] = $product['categoria_nombre'] ?? '';
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products),
        'query' => $query
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    error_log('Error en search-products.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar productos: ' . $e->getMessage(),
        'products' => [],
        'count' => 0,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    error_log('Error fatal en search-products.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fatal al buscar productos: ' . $e->getMessage(),
        'products' => [],
        'count' => 0,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

