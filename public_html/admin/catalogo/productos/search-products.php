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

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('newsletter', 'editar');
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

// Configurar headers JSON
header('Content-Type: application/json');

if (empty($query) || strlen($query) < 2) {
    echo json_encode(['success' => false, 'message' => 'La búsqueda debe tener al menos 2 caracteres']);
    exit;
}

try {
    // Buscar productos
    $sql = "SELECT p.id, p.nombre, p.codigo, p.slug, p.descripcion_corta, 
                   p.imagen_principal, p.estado,
                   m.nombre as marca_nombre,
                   c.nombre as categoria_nombre
            FROM catalogo_productos p
            LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
            LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
            WHERE p.estado = 'publicado' 
            AND (
                p.nombre LIKE :query 
                OR p.codigo LIKE :query 
                OR p.descripcion_corta LIKE :query
                OR m.nombre LIKE :query
                OR c.nombre LIKE :query
            )
            ORDER BY p.destacado DESC, p.created_at DESC
            LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $search_term = '%' . $query . '%';
    $stmt->bindValue(':query', $search_term, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
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
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar productos: ' . $e->getMessage()
    ]);
}

