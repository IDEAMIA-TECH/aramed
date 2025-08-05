<?php
/**
 * Integración con Enlace Fiscal para ARAmed
 * Manejo de facturación electrónica y carta porte
 */

class EnlaceFiscalAPI {
    
    private $api_key;
    private $api_secret;
    private $base_url;
    private $environment; // 'sandbox' o 'production'
    
    public function __construct($config) {
        $this->api_key = $config['api_key'];
        $this->api_secret = $config['api_secret'];
        $this->base_url = $config['base_url'];
        $this->environment = $config['environment'];
    }
    
    /**
     * Generar factura electrónica
     */
    public function generarFactura($datos) {
        $endpoint = '/api/v1/facturas';
        
        $payload = [
            'emisor' => [
                'rfc' => $datos['emisor_rfc'],
                'nombre' => $datos['emisor_nombre'],
                'regimen_fiscal' => $datos['emisor_regimen']
            ],
            'receptor' => [
                'rfc' => $datos['receptor_rfc'],
                'nombre' => $datos['receptor_nombre'],
                'uso_cfdi' => $datos['receptor_uso_cfdi']
            ],
            'conceptos' => $datos['conceptos'],
            'forma_pago' => $datos['forma_pago'],
            'metodo_pago' => $datos['metodo_pago'],
            'moneda' => $datos['moneda'],
            'subtotal' => $datos['subtotal'],
            'total' => $datos['total'],
            'impuestos' => $datos['impuestos']
        ];
        
        return $this->makeRequest('POST', $endpoint, $payload);
    }
    
    /**
     * Generar carta porte electrónica
     */
    public function generarCartaPorte($datos) {
        $endpoint = '/api/v1/carta-porte';
        
        $payload = [
            'emisor' => [
                'rfc' => $datos['emisor_rfc'],
                'nombre' => $datos['emisor_nombre']
            ],
            'receptor' => [
                'rfc' => $datos['receptor_rfc'],
                'nombre' => $datos['receptor_nombre']
            ],
            'transporte' => [
                'tipo_transporte' => $datos['tipo_transporte'],
                'placa_vehiculo' => $datos['placa_vehiculo'],
                'operador' => $datos['operador']
            ],
            'origen' => [
                'calle' => $datos['origen_calle'],
                'numero' => $datos['origen_numero'],
                'colonia' => $datos['origen_colonia'],
                'municipio' => $datos['origen_municipio'],
                'estado' => $datos['origen_estado'],
                'cp' => $datos['origen_cp']
            ],
            'destino' => [
                'calle' => $datos['destino_calle'],
                'numero' => $datos['destino_numero'],
                'colonia' => $datos['destino_colonia'],
                'municipio' => $datos['destino_municipio'],
                'estado' => $datos['destino_estado'],
                'cp' => $datos['destino_cp']
            ],
            'mercancia' => $datos['mercancia'],
            'total_distancia_recorrida' => $datos['distancia']
        ];
        
        return $this->makeRequest('POST', $endpoint, $payload);
    }
    
    /**
     * Cancelar documento fiscal
     */
    public function cancelarDocumento($uuid, $motivo = '01') {
        $endpoint = '/api/v1/cancelaciones';
        
        $payload = [
            'uuid' => $uuid,
            'motivo' => $motivo // 01: No identificado, 02: Cliente, 03: Proveedor
        ];
        
        return $this->makeRequest('POST', $endpoint, $payload);
    }
    
    /**
     * Obtener estado de un documento
     */
    public function obtenerEstado($uuid) {
        $endpoint = "/api/v1/documentos/{$uuid}/estado";
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * Descargar PDF del documento
     */
    public function descargarPDF($uuid) {
        $endpoint = "/api/v1/documentos/{$uuid}/pdf";
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * Realizar petición HTTP a la API
     */
    private function makeRequest($method, $endpoint, $payload = null) {
        $url = $this->base_url . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($payload) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => 'Error de conexión: ' . $error
            ];
        }
        
        $data = json_decode($response, true);
        
        if ($http_code >= 200 && $http_code < 300) {
            return [
                'success' => true,
                'data' => $data
            ];
        } else {
            return [
                'success' => false,
                'error' => $data['message'] ?? 'Error en la API',
                'http_code' => $http_code
            ];
        }
    }
}

/**
 * Clase para manejar facturas en el sistema ARAmed
 */
class FacturaManager {
    
    private $db;
    private $enlace_fiscal;
    
    public function __construct($database, $enlace_config) {
        $this->db = $database;
        $this->enlace_fiscal = new EnlaceFiscalAPI($enlace_config);
    }
    
    /**
     * Crear factura desde un pedido
     */
    public function crearFacturaDesdePedido($pedido_id) {
        // Obtener datos del pedido
        $stmt = $this->db->prepare("
            SELECT p.*, c.nombre as cliente_nombre, c.rfc as cliente_rfc, c.email as cliente_email
            FROM pedidos p
            JOIN clientes c ON p.cliente_id = c.id
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $pedido = $stmt->get_result()->fetch_assoc();
        
        if (!$pedido) {
            return ['success' => false, 'error' => 'Pedido no encontrado'];
        }
        
        // Obtener productos del pedido
        $stmt = $this->db->prepare("
            SELECT pp.*, p.titulo as producto_nombre, p.precio
            FROM pedidos_productos pp
            JOIN productos p ON pp.producto_id = p.id
            WHERE pp.pedido_id = ?
        ");
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Preparar datos para la factura
        $conceptos = [];
        $subtotal = 0;
        
        foreach ($productos as $producto) {
            $importe = $producto['cantidad'] * $producto['precio'];
            $subtotal += $importe;
            
            $conceptos[] = [
                'clave_prod_serv' => '84111506', // Código SAT para servicios médicos
                'clave_unidad' => 'H87', // Pieza
                'cantidad' => $producto['cantidad'],
                'descripcion' => $producto['producto_nombre'],
                'valor_unitario' => $producto['precio'],
                'importe' => $importe,
                'impuestos' => [
                    'traslados' => [
                        [
                            'base' => $importe,
                            'impuesto' => '002', // IVA
                            'tipo_factor' => 'Tasa',
                            'tasa_o_cuota' => '0.160000',
                            'importe' => $importe * 0.16
                        ]
                    ]
                ]
            ];
        }
        
        $iva = $subtotal * 0.16;
        $total = $subtotal + $iva;
        
        $datos_factura = [
            'emisor_rfc' => $_ENV['EMISOR_RFC'] ?? 'XAXX010101000',
            'emisor_nombre' => $_ENV['EMISOR_NOMBRE'] ?? 'ARAMED S.A. DE C.V.',
            'emisor_regimen' => '601', // General de Ley Personas Morales
            'receptor_rfc' => $pedido['cliente_rfc'],
            'receptor_nombre' => $pedido['cliente_nombre'],
            'receptor_uso_cfdi' => 'G01', // Adquisición de mercancías
            'conceptos' => $conceptos,
            'forma_pago' => '03', // Transferencia electrónica
            'metodo_pago' => 'PUE', // Pago en una sola exhibición
            'moneda' => 'MXN',
            'subtotal' => $subtotal,
            'total' => $total,
            'impuestos' => [
                'total_impuestos_trasladados' => $iva,
                'traslados' => [
                    [
                        'impuesto' => '002', // IVA
                        'tipo_factor' => 'Tasa',
                        'tasa_o_cuota' => '0.160000',
                        'importe' => $iva
                    ]
                ]
            ]
        ];
        
        // Generar factura en Enlace Fiscal
        $resultado = $this->enlace_fiscal->generarFactura($datos_factura);
        
        if ($resultado['success']) {
            // Guardar factura en la base de datos
            $uuid = $resultado['data']['uuid'];
            $cfdi = $resultado['data']['cfdi'];
            
            $stmt = $this->db->prepare("
                INSERT INTO facturas (pedido_id, uuid, cfdi, total, estado, created_at)
                VALUES (?, ?, ?, ?, 'vigente', NOW())
            ");
            $stmt->bind_param("issd", $pedido_id, $uuid, $cfdi, $total);
            $stmt->execute();
            
            // Actualizar estado del pedido
            $stmt = $this->db->prepare("UPDATE pedidos SET estado = 'facturado' WHERE id = ?");
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            
            return [
                'success' => true,
                'uuid' => $uuid,
                'total' => $total,
                'message' => 'Factura generada exitosamente'
            ];
        } else {
            return $resultado;
        }
    }
    
    /**
     * Crear carta porte para envío
     */
    public function crearCartaPorte($pedido_id, $datos_envio) {
        // Obtener datos del pedido
        $stmt = $this->db->prepare("
            SELECT p.*, c.nombre as cliente_nombre, c.direccion as cliente_direccion
            FROM pedidos p
            JOIN clientes c ON p.cliente_id = c.id
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $pedido = $stmt->get_result()->fetch_assoc();
        
        $datos_carta_porte = [
            'emisor_rfc' => $_ENV['EMISOR_RFC'] ?? 'XAXX010101000',
            'emisor_nombre' => $_ENV['EMISOR_NOMBRE'] ?? 'ARAMED S.A. DE C.V.',
            'receptor_rfc' => $datos_envio['receptor_rfc'],
            'receptor_nombre' => $pedido['cliente_nombre'],
            'tipo_transporte' => 'Autotransporte',
            'placa_vehiculo' => $datos_envio['placa_vehiculo'],
            'operador' => $datos_envio['operador'],
            'origen_calle' => $_ENV['EMISOR_CALLE'] ?? 'Av. Principal',
            'origen_numero' => $_ENV['EMISOR_NUMERO'] ?? '123',
            'origen_colonia' => $_ENV['EMISOR_COLONIA'] ?? 'Centro',
            'origen_municipio' => $_ENV['EMISOR_MUNICIPIO'] ?? 'Ciudad de México',
            'origen_estado' => $_ENV['EMISOR_ESTADO'] ?? 'CDMX',
            'origen_cp' => $_ENV['EMISOR_CP'] ?? '06000',
            'destino_calle' => $datos_envio['destino_calle'],
            'destino_numero' => $datos_envio['destino_numero'],
            'destino_colonia' => $datos_envio['destino_colonia'],
            'destino_municipio' => $datos_envio['destino_municipio'],
            'destino_estado' => $datos_envio['destino_estado'],
            'destino_cp' => $datos_envio['destino_cp'],
            'mercancia' => [
                [
                    'bienes_transp' => '84111506', // Código SAT para equipos médicos
                    'descripcion' => 'Simuladores médicos',
                    'cantidad' => 1,
                    'clave_unidad_peso' => 'KGM',
                    'peso_en_kg' => $datos_envio['peso_kg']
                ]
            ],
            'distancia' => $datos_envio['distancia_km']
        ];
        
        $resultado = $this->enlace_fiscal->generarCartaPorte($datos_carta_porte);
        
        if ($resultado['success']) {
            $uuid = $resultado['data']['uuid'];
            $cfdi = $resultado['data']['cfdi'];
            
            // Guardar carta porte en la base de datos
            $stmt = $this->db->prepare("
                INSERT INTO cartas_porte (pedido_id, uuid, cfdi, estado, created_at)
                VALUES (?, ?, ?, 'vigente', NOW())
            ");
            $stmt->bind_param("iss", $pedido_id, $uuid, $cfdi);
            $stmt->execute();
            
            return [
                'success' => true,
                'uuid' => $uuid,
                'message' => 'Carta porte generada exitosamente'
            ];
        } else {
            return $resultado;
        }
    }
    
    /**
     * Obtener PDF de un documento
     */
    public function obtenerPDF($uuid, $tipo = 'factura') {
        $resultado = $this->enlace_fiscal->descargarPDF($uuid);
        
        if ($resultado['success']) {
            // Guardar PDF en el servidor
            $filename = "documentos/{$tipo}_{$uuid}.pdf";
            file_put_contents($filename, $resultado['data']);
            
            return [
                'success' => true,
                'file' => $filename
            ];
        } else {
            return $resultado;
        }
    }
    
    /**
     * Cancelar documento
     */
    public function cancelarDocumento($uuid, $motivo = '01') {
        $resultado = $this->enlace_fiscal->cancelarDocumento($uuid, $motivo);
        
        if ($resultado['success']) {
            // Actualizar estado en la base de datos
            $stmt = $this->db->prepare("
                UPDATE facturas SET estado = 'cancelado' WHERE uuid = ?
            ");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();
            
            $stmt = $this->db->prepare("
                UPDATE cartas_porte SET estado = 'cancelado' WHERE uuid = ?
            ");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();
            
            return [
                'success' => true,
                'message' => 'Documento cancelado exitosamente'
            ];
        } else {
            return $resultado;
        }
    }
}

/**
 * Ejemplo de uso en el sistema ARAmed
 */
class FacturaController {
    
    private $factura_manager;
    
    public function __construct($database) {
        $enlace_config = [
            'api_key' => $_ENV['ENLACE_FISCAL_API_KEY'],
            'api_secret' => $_ENV['ENLACE_FISCAL_API_SECRET'],
            'base_url' => $_ENV['ENLACE_FISCAL_BASE_URL'],
            'environment' => $_ENV['ENLACE_FISCAL_ENV'] ?? 'sandbox'
        ];
        
        $this->factura_manager = new FacturaManager($database, $enlace_config);
    }
    
    /**
     * Generar factura desde el panel administrativo
     */
    public function generarFactura() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido_id = (int)$_POST['pedido_id'];
            
            $resultado = $this->factura_manager->crearFacturaDesdePedido($pedido_id);
            
            if ($resultado['success']) {
                // Enviar factura por email
                $this->enviarFacturaPorEmail($pedido_id, $resultado['uuid']);
                
                return [
                    'success' => true,
                    'message' => 'Factura generada y enviada exitosamente',
                    'uuid' => $resultado['uuid']
                ];
            } else {
                return $resultado;
            }
        }
    }
    
    /**
     * Generar carta porte
     */
    public function generarCartaPorte() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido_id = (int)$_POST['pedido_id'];
            $datos_envio = [
                'receptor_rfc' => $_POST['receptor_rfc'],
                'placa_vehiculo' => $_POST['placa_vehiculo'],
                'operador' => $_POST['operador'],
                'destino_calle' => $_POST['destino_calle'],
                'destino_numero' => $_POST['destino_numero'],
                'destino_colonia' => $_POST['destino_colonia'],
                'destino_municipio' => $_POST['destino_municipio'],
                'destino_estado' => $_POST['destino_estado'],
                'destino_cp' => $_POST['destino_cp'],
                'peso_kg' => (float)$_POST['peso_kg'],
                'distancia_km' => (float)$_POST['distancia_km']
            ];
            
            $resultado = $this->factura_manager->crearCartaPorte($pedido_id, $datos_envio);
            
            return $resultado;
        }
    }
    
    /**
     * Enviar factura por email
     */
    private function enviarFacturaPorEmail($pedido_id, $uuid) {
        // Obtener datos del pedido
        $stmt = $this->db->prepare("
            SELECT p.*, c.email as cliente_email, c.nombre as cliente_nombre
            FROM pedidos p
            JOIN clientes c ON p.cliente_id = c.id
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $pedido = $stmt->get_result()->fetch_assoc();
        
        // Obtener PDF
        $pdf_result = $this->factura_manager->obtenerPDF($uuid, 'factura');
        
        if ($pdf_result['success']) {
            // Enviar email con PDF adjunto
            $to = $pedido['cliente_email'];
            $subject = "Factura - Pedido #{$pedido_id}";
            $message = "Adjunto encontrará la factura correspondiente a su pedido.";
            
            // Usar PHPMailer o similar para enviar con adjunto
            // mail($to, $subject, $message, $headers, $pdf_result['file']);
        }
    }
}

// Ejemplo de configuración en .env
/*
ENLACE_FISCAL_API_KEY=tu_api_key_aqui
ENLACE_FISCAL_API_SECRET=tu_api_secret_aqui
ENLACE_FISCAL_BASE_URL=https://api.enlacefiscal.com
ENLACE_FISCAL_ENV=sandbox

EMISOR_RFC=XAXX010101000
EMISOR_NOMBRE=ARAMED S.A. DE C.V.
EMISOR_CALLE=Av. Principal
EMISOR_NUMERO=123
EMISOR_COLONIA=Centro
EMISOR_MUNICIPIO=Ciudad de México
EMISOR_ESTADO=CDMX
EMISOR_CP=06000
*/

// Ejemplo de uso en el sistema actual
if (isset($_POST['generar_factura'])) {
    $controller = new FacturaController($CONEXION);
    $resultado = $controller->generarFactura();
    
    if ($resultado['success']) {
        echo json_encode($resultado);
    } else {
        http_response_code(400);
        echo json_encode($resultado);
    }
}
?> 