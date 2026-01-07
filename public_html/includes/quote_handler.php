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

// Definir constante para permitir carga de funciones
if (!defined('ARAMED_SITE')) {
    define('ARAMED_SITE', true);
}

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
        
        // Cargar funciones de email
        if (!defined('INCLUDES_PATH')) {
            define('INCLUDES_PATH', __DIR__);
        }
        if (file_exists(__DIR__ . '/email_functions.php')) {
            require_once __DIR__ . '/email_functions.php';
        }
        
        // ========================================
        // ENVIAR EMAIL 1: CONFIRMACIÓN AL CLIENTE
        // ========================================
        try {
            // URL del logo de la empresa
            $logo_url = SITE_URL . '/assets/images/design/logo.png';
            
            $clientSubject = "Confirmación de Solicitud de Cotización - {$folio}";
            $clientMessage = "
                <!DOCTYPE html>
                <html lang='es-MX'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                </head>
                <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                        <img src='{$logo_url}' 
                             alt='" . SITE_NAME . "' 
                             style='max-width: 200px; height: auto; margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px;'
                             onerror='this.style.display=\"none\"'>
                        <h1 style='color: white; margin: 0; font-size: 24px;'>¡Solicitud Recibida!</h1>
                    </div>
                    
                    <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;'>
                        <p style='font-size: 16px;'>Estimado/a <strong>{$data['nombre']}</strong>,</p>
                        
                        <p>Gracias por contactarnos. Hemos recibido tu solicitud de cotización con el siguiente folio:</p>
                        
                        <div style='background: white; padding: 20px; border-left: 4px solid #667eea; margin: 20px 0;'>
                            <p style='margin: 0; font-size: 18px; font-weight: bold; color: #667eea;'>{$folio}</p>
                        </div>
                        
                        <p>Nuestro equipo de ventas está revisando tu solicitud y se pondrá en contacto contigo a la brevedad posible para proporcionarte la información y cotización que necesitas.</p>
                        
                        <div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px;'>
                            <h3 style='margin-top: 0; color: #667eea;'>Resumen de tu solicitud:</h3>
                            <ul style='list-style: none; padding: 0;'>
                                <li style='padding: 5px 0;'><strong>Institución:</strong> {$data['institucion']}</li>
                                <li style='padding: 5px 0;'><strong>Productos solicitados:</strong> " . count($cart_products) . " producto(s)</li>
                                <li style='padding: 5px 0;'><strong>Fecha de solicitud:</strong> " . date('d/m/Y H:i') . "</li>
                            </ul>
                        </div>
                        
                        <p>Si tienes alguna pregunta o necesitas información adicional, no dudes en contactarnos.</p>
                        
                        <p style='margin-top: 30px;'>Saludos cordiales,<br>
                        <strong>Equipo de Aramed y Laboratorios</strong></p>
                        
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                        
                        <p style='font-size: 12px; color: #666; text-align: center;'>
                            Este es un correo automático, por favor no respondas a este mensaje.<br>
                            Si necesitas contactarnos, escribe a: " . CONTACT_EMAIL . "
                        </p>
                    </div>
                </body>
                </html>
            ";
            
            $emailResult = sendEmail($data['email_oficial'], $clientSubject, $clientMessage, $data['nombre']);
            if (!$emailResult['success']) {
                error_log("Error enviando email de confirmación al cliente: " . $emailResult['message']);
            }
        } catch (Exception $e) {
            error_log("Error enviando email de confirmación al cliente: " . $e->getMessage());
        }
        
        // ========================================
        // ENVIAR EMAIL 2: NOTIFICACIÓN AL ADMIN
        // ========================================
        try {
            // Obtener detalles completos de la cotización
            $cotizacion_sql = "
                SELECT c.*, 
                       GROUP_CONCAT(CONCAT(ci.producto_nombre, ' (x', ci.cantidad, ')') SEPARATOR ', ') as productos_lista
                FROM cotizaciones c
                LEFT JOIN cotizacion_items ci ON c.id = ci.cotizacion_id
                WHERE c.id = ?
                GROUP BY c.id
            ";
            $cotizacion_stmt = $pdo->prepare($cotizacion_sql);
            $cotizacion_stmt->execute([$cotizacion_id]);
            $cotizacion_detalle = $cotizacion_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Construir lista de productos
            $productos_html = '<ul style="list-style: none; padding: 0;">';
            foreach ($cart_products as $product) {
                $productos_html .= "<li style='padding: 8px 0; border-bottom: 1px solid #eee;'>";
                $productos_html .= "<strong>{$product['nombre']}</strong> (Cantidad: {$product['cantidad']})";
                if (!empty($product['codigo'])) {
                    $productos_html .= "<br><small style='color: #666;'>Código: {$product['codigo']}</small>";
                }
                $productos_html .= "</li>";
            }
            $productos_html .= '</ul>';
            
            $adminSubject = "Nueva Solicitud de Cotización - {$folio}";
            $adminMessage = "
                <!DOCTYPE html>
                <html lang='es-MX'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                </head>
                <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 700px; margin: 0 auto; padding: 20px;'>
                    <div style='background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                        <h1 style='color: white; margin: 0;'>Nueva Solicitud de Cotización</h1>
                        <p style='color: white; margin: 10px 0 0 0; font-size: 18px;'>Folio: <strong>{$folio}</strong></p>
                    </div>
                    
                    <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;'>
                        <div style='background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; border-left: 4px solid #e74c3c;'>
                            <h2 style='margin-top: 0; color: #e74c3c;'>Información de la Institución</h2>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; width: 40%;'><strong>Institución:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['institucion']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Tipo:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['tipo_institucion']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Estado:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['estado']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Ciudad:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['ciudad']}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div style='background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; border-left: 4px solid #3498db;'>
                            <h2 style='margin-top: 0; color: #3498db;'>Información de Contacto</h2>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; width: 40%;'><strong>Nombre:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['nombre']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Puesto:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['puesto']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Email Oficial:</strong></td>
                                    <td style='padding: 8px 0;'><a href='mailto:{$data['email_oficial']}'>{$data['email_oficial']}</a></td>
                                </tr>
                                " . (!empty($data['email_alterno']) ? "
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Email Alterno:</strong></td>
                                    <td style='padding: 8px 0;'><a href='mailto:{$data['email_alterno']}'>{$data['email_alterno']}</a></td>
                                </tr>
                                " : "") . "
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Teléfono Oficina:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['telefono_oficina']}" . (!empty($data['extension']) ? " Ext. {$data['extension']}" : "") . "</td>
                                </tr>
                                " . (!empty($data['telefono_celular']) ? "
                                <tr>
                                    <td style='padding: 8px 0;'><strong>Teléfono Celular:</strong></td>
                                    <td style='padding: 8px 0;'>{$data['telefono_celular']}</td>
                                </tr>
                                " : "") . "
                            </table>
                        </div>
                        
                        <div style='background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; border-left: 4px solid #27ae60;'>
                            <h2 style='margin-top: 0; color: #27ae60;'>Productos Solicitados</h2>
                            <p style='margin-top: 0;'><strong>Total de productos:</strong> " . count($cart_products) . "</p>
                            {$productos_html}
                        </div>
                        
                        " . (!empty($data['fecha_compra_aprox']) || !empty($data['presupuesto_estimado']) || !empty($data['observaciones']) ? "
                        <div style='background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; border-left: 4px solid #f39c12;'>
                            <h2 style='margin-top: 0; color: #f39c12;'>Información Adicional</h2>
                            " . (!empty($data['fecha_compra_aprox']) ? "
                            <p><strong>Fecha Aproximada de Compra:</strong> " . date('d/m/Y', strtotime($data['fecha_compra_aprox'])) . "</p>
                            " : "") . "
                            " . (!empty($data['presupuesto_estimado']) ? "
                            <p><strong>Presupuesto Estimado:</strong> $" . number_format($data['presupuesto_estimado'], 2) . " MXN</p>
                            " : "") . "
                            " . (!empty($data['observaciones']) ? "
                            <p><strong>Observaciones:</strong></p>
                            <p style='background: #f9f9f9; padding: 10px; border-radius: 5px;'>{$data['observaciones']}</p>
                            " : "") . "
                        </div>
                        " : "") . "
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='" . SITE_URL . "/admin/cotizaciones/view.php?id={$cotizacion_id}' 
                               style='background: #e74c3c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>
                                Ver Cotización en el Sistema
                            </a>
                        </div>
                        
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                        
                        <p style='font-size: 12px; color: #666; text-align: center;'>
                            Este es un correo automático generado por el sistema de cotizaciones de Aramed y Laboratorios.<br>
                            Fecha y hora: " . date('d/m/Y H:i:s') . "
                        </p>
                    </div>
                </body>
                </html>
            ";
            
            $emailResult = sendEmail(CONTACT_EMAIL, $adminSubject, $adminMessage);
            if (!$emailResult['success']) {
                error_log("Error enviando email de notificación al admin: " . $emailResult['message']);
            }
        } catch (Exception $e) {
            error_log("Error enviando email de notificación al admin: " . $e->getMessage());
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

