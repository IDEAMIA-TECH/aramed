<?php
/**
 * ========================================
 * ADMIN - EXPORTAR COTIZACIONES
 * ========================================
 * 
 * Exporta cotizaciones a CSV/Excel
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('cotizaciones', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Si se solicita una cotización específica
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    // Exportar una sola cotización con sus items
    $stmt = $pdo->prepare("
        SELECT c.*, 
               au.nombre as ejecutivo_nombre,
               GROUP_CONCAT(CONCAT(ci.producto_nombre, ' (x', ci.cantidad, ')') SEPARATOR '; ') as productos
        FROM cotizaciones c
        LEFT JOIN admin_usuarios au ON c.assigned_to = au.id
        LEFT JOIN cotizacion_items ci ON c.id = ci.cotizacion_id
        WHERE c.id = ?
        GROUP BY c.id
    ");
    $stmt->execute([$id]);
    $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cotizacion) {
        header('Location: index.php');
        exit;
    }
    
    $filename = 'cotizacion_' . $cotizacion['folio'] . '_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    
    // Encabezados
    fputcsv($output, ['Campo', 'Valor'], ';');
    
    // Datos
    $datos = [
        ['Folio', $cotizacion['folio']],
        ['Fecha', date('d/m/Y H:i:s', strtotime($cotizacion['created_at']))],
        ['Estado', $cotizacion['estado_cotizacion']],
        ['Institución', $cotizacion['institucion']],
        ['Tipo Institución', $cotizacion['tipo_institucion']],
        ['Estado (Geo)', $cotizacion['estado']],
        ['Ciudad', $cotizacion['ciudad']],
        ['Nombre Contacto', $cotizacion['nombre']],
        ['Puesto', $cotizacion['puesto']],
        ['Email Oficial', $cotizacion['email_oficial']],
        ['Email Alterno', $cotizacion['email_alterno'] ?? ''],
        ['Teléfono Oficina', $cotizacion['telefono_oficina']],
        ['Extensión', $cotizacion['extension'] ?? ''],
        ['Teléfono Celular', $cotizacion['telefono_celular'] ?? ''],
        ['Producto de Interés', $cotizacion['producto_interes'] ?? ''],
        ['Productos Cotizados', $cotizacion['productos'] ?? ''],
        ['Fecha Compra Aprox', $cotizacion['fecha_compra_aprox'] ? date('d/m/Y', strtotime($cotizacion['fecha_compra_aprox'])) : ''],
        ['Presupuesto Estimado', $cotizacion['presupuesto_estimado'] ? '$' . number_format($cotizacion['presupuesto_estimado'], 2) : ''],
        ['Observaciones', $cotizacion['observaciones'] ?? ''],
        ['Ejecutivo Asignado', $cotizacion['ejecutivo_nombre'] ?? ''],
        ['Notas Internas', $cotizacion['notas_internas'] ?? ''],
    ];
    
    foreach ($datos as $dato) {
        fputcsv($output, $dato, ';');
    }
    
    fclose($output);
    exit;
}

// Exportar múltiples cotizaciones (con filtros)
$filtro_estado = $_GET['estado'] ?? '';
$filtro_ejecutivo = $_GET['ejecutivo'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

// Construir consulta (mismo código que en index.php)
$where_conditions = [];
$params = [];

if ($filtro_estado) {
    $where_conditions[] = "c.estado_cotizacion = ?";
    $params[] = $filtro_estado;
}

if ($filtro_ejecutivo) {
    $where_conditions[] = "c.assigned_to = ?";
    $params[] = (int)$filtro_ejecutivo;
}

if ($filtro_fecha_desde) {
    $where_conditions[] = "DATE(c.created_at) >= ?";
    $params[] = $filtro_fecha_desde;
}

if ($filtro_fecha_hasta) {
    $where_conditions[] = "DATE(c.created_at) <= ?";
    $params[] = $filtro_fecha_hasta;
}

if ($busqueda) {
    $where_conditions[] = "(c.folio LIKE ? OR c.institucion LIKE ? OR c.nombre LIKE ? OR c.email_oficial LIKE ?)";
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener cotizaciones
$sql = "SELECT c.*, 
               au.nombre as ejecutivo_nombre
        FROM cotizaciones c
        LEFT JOIN admin_usuarios au ON c.assigned_to = au.id
        $where_clause
        ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configurar headers
$filename = 'cotizaciones_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

// Encabezados
$headers = [
    'Folio',
    'Fecha',
    'Estado',
    'Institución',
    'Tipo Institución',
    'Estado (Geo)',
    'Ciudad',
    'Nombre Contacto',
    'Puesto',
    'Email Oficial',
    'Email Alterno',
    'Teléfono Oficina',
    'Extensión',
    'Teléfono Celular',
    'Producto de Interés',
    'Fecha Compra Aprox',
    'Presupuesto Estimado',
    'Observaciones',
    'Ejecutivo Asignado',
    'IP Address',
    'Created At'
];
fputcsv($output, $headers, ';');

// Datos
foreach ($cotizaciones as $cot) {
    $row = [
        $cot['folio'],
        date('d/m/Y H:i:s', strtotime($cot['created_at'])),
        $cot['estado_cotizacion'],
        $cot['institucion'],
        $cot['tipo_institucion'],
        $cot['estado'],
        $cot['ciudad'],
        $cot['nombre'],
        $cot['puesto'],
        $cot['email_oficial'],
        $cot['email_alterno'] ?? '',
        $cot['telefono_oficina'],
        $cot['extension'] ?? '',
        $cot['telefono_celular'] ?? '',
        $cot['producto_interes'] ?? '',
        $cot['fecha_compra_aprox'] ? date('d/m/Y', strtotime($cot['fecha_compra_aprox'])) : '',
        $cot['presupuesto_estimado'] ? '$' . number_format($cot['presupuesto_estimado'], 2) : '',
        $cot['observaciones'] ?? '',
        $cot['ejecutivo_nombre'] ?? '',
        $cot['ip_address'] ?? '',
        date('d/m/Y H:i:s', strtotime($cot['created_at']))
    ];
    fputcsv($output, $row, ';');
}

fclose($output);
exit;

