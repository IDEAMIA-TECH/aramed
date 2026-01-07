<?php
/**
 * ========================================
 * HANDLER PARA PROCESAR COTIZACIÓN
 * ========================================
 * 
 * Procesa el formulario de cotización y guarda en la base de datos
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Verificar que las funciones de sanitización existan
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sanitizeEmail')) {
    function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
}

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/cart_functions.php';

// Respuesta JSON
header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'message' => ''
];

try {
    // Verificar que es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Verificar que hay productos en el carrito
    $cart_products = getCartProductsInfo();
    if (empty($cart_products)) {
        throw new Exception('No hay productos en el carrito');
    }
    
    // Sanitizar y validar datos
    $data = [
        'institucion' => sanitizeInput($_POST['institucion'] ?? ''),
        'tipo_institucion' => sanitizeInput($_POST['tipo_institucion'] ?? ''),
        'campo_adicional' => sanitizeInput($_POST['campo_adicional'] ?? ''),
        'estado' => sanitizeInput($_POST['estado'] ?? ''),
        'ciudad' => sanitizeInput($_POST['ciudad'] ?? ''),
        'nombre' => sanitizeInput($_POST['nombre'] ?? ''),
        'puesto' => sanitizeInput($_POST['puesto'] ?? ''),
        'email_oficial' => sanitizeEmail($_POST['email_oficial'] ?? ''),
        'email_alterno' => !empty($_POST['email_alterno']) ? sanitizeEmail($_POST['email_alterno']) : null,
        'telefono_oficina' => sanitizeInput($_POST['telefono_oficina'] ?? ''),
        'extension' => sanitizeInput($_POST['extension'] ?? ''),
        'telefono_celular' => sanitizeInput($_POST['telefono_celular'] ?? ''),
        'fecha_compra_aprox' => !empty($_POST['fecha_compra_aprox']) ? $_POST['fecha_compra_aprox'] : null,
        'presupuesto_estimado' => !empty($_POST['presupuesto_estimado']) ? (float)$_POST['presupuesto_estimado'] : null,
        'observaciones' => sanitizeInput($_POST['observaciones'] ?? ''),
        'privacidad' => isset($_POST['privacidad']) ? 1 : 0,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    // Validaciones obligatorias
    $requiredFields = [
        'institucion' => 'Institución',
        'tipo_institucion' => 'Tipo de institución',
        'estado' => 'Estado',
        'ciudad' => 'Ciudad',
        'nombre' => 'Nombre',
        'puesto' => 'Puesto',
        'email_oficial' => 'Correo oficial',
        'telefono_oficina' => 'Teléfono de oficina'
    ];
    
    foreach ($requiredFields as $field => $label) {
        if (empty($data[$field])) {
            throw new Exception("El campo '{$label}' es obligatorio.");
        }
        }
    
    // Validar privacidad
    if ($data['privacidad'] !== 1) {
        throw new Exception("Debes aceptar la política de privacidad.");
    }
    
    // Validar email oficial
    if (!filter_var($data['email_oficial'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo oficial no es válido.");
    }
    
    // Validar email alterno si se proporcionó
    if ($data['email_alterno'] && !filter_var($data['email_alterno'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo alterno no es válido.");
    }
    
    // Obtener conexión
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    try {
        // Generar folio único
        $year = date('Y');
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM cotizaciones WHERE folio LIKE ?");
        $stmt->execute(["COT-{$year}-%"]);
        $count = $stmt->fetch()['total'];
        $folio = sprintf("COT-%s-%03d", $year, $count + 1);
        
        // Insertar cotización
        $sql = "
            INSERT INTO cotizaciones (
                folio, institucion, tipo_institucion, campo_adicional, estado, ciudad,
                nombre, puesto, email_oficial, email_alterno,
                telefono_oficina, extension, telefono_celular,
                producto_interes, fecha_compra_aprox, presupuesto_estimado, observaciones,
                estado_cotizacion, ip_address, user_agent, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                'nueva', ?, ?, NOW()
            )
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $folio,
            $data['institucion'],
            $data['tipo_institucion'],
            $data['campo_adicional'],
            $data['estado'],
            $data['ciudad'],
            $data['nombre'],
            $data['puesto'],
            $data['email_oficial'],
            $data['email_alterno'],
            $data['telefono_oficina'],
            $data['extension'],
            $data['telefono_celular'],
            $cart_products[0]['nombre'] ?? null, // Producto principal de interés
            $data['fecha_compra_aprox'],
            $data['presupuesto_estimado'],
            $data['observaciones'],
            $data['ip_address'],
            $data['user_agent']
        ]);
        
        $cotizacion_id = $pdo->lastInsertId();
        
        // Insertar items de la cotización
        $items_sql = "
            INSERT INTO cotizacion_items (
                cotizacion_id, producto_id, producto_nombre, producto_codigo,
                cantidad, created_at
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ";
        
        $items_stmt = $pdo->prepare($items_sql);
        
        foreach ($cart_products as $product) {
            $items_stmt->execute([
                $cotizacion_id,
                $product['id'],
                $product['nombre'],
                $product['codigo'] ?? '',
                $product['cantidad']
            ]);
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Limpiar carrito
        clearCart();
        
        // Enviar email de notificación (opcional)
        if (function_exists('sendEmail') && defined('CONTACT_EMAIL')) {
            $subject = "Nueva Solicitud de Cotización - {$folio}";
            $message = "
                Nueva solicitud de cotización recibida:
                
                Folio: {$folio}
                Institución: {$data['institucion']}
                Contacto: {$data['nombre']} ({$data['email_oficial']})
                Productos: " . count($cart_products) . " producto(s)
                
                Ver detalles: " . SITE_URL . "/admin/cotizaciones/view.php?id={$cotizacion_id}
            ";
            
            try {
                sendEmail(CONTACT_EMAIL, $subject, $message);
            } catch (Exception $e) {
                error_log("Error enviando email de cotización: " . $e->getMessage());
            }
        }
        
        $response = [
            'success' => true,
            'message' => 'Tu solicitud de cotización ha sido enviada exitosamente. Nos pondremos en contacto contigo pronto.',
            'folio' => $folio,
            'redirect' => 'cotizacion-enviada.php?folio=' . urlencode($folio)
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error procesando cotización: " . $e->getMessage());
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

