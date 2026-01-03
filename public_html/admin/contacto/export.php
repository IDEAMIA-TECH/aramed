<?php
/**
 * ========================================
 * ADMIN - EXPORTAR MENSAJES A CSV
 * ========================================
 * 
 * Exporta mensajes de contacto a CSV
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
    checkPermission('contacto', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Aplicar mismos filtros que en el listado
$filtro_estado = $_GET['estado'] ?? '';
$filtro_asunto = $_GET['asunto'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_estado) {
    $where_conditions[] = "cm.status = ?";
    $params[] = $filtro_estado;
}

if ($filtro_asunto) {
    $where_conditions[] = "cm.asunto LIKE ?";
    $params[] = "%{$filtro_asunto}%";
}

if ($filtro_fecha_desde) {
    $where_conditions[] = "DATE(cm.created_at) >= ?";
    $params[] = $filtro_fecha_desde;
}

if ($filtro_fecha_hasta) {
    $where_conditions[] = "DATE(cm.created_at) <= ?";
    $params[] = $filtro_fecha_hasta;
}

if ($busqueda) {
    $where_conditions[] = "(cm.nombre LIKE ? OR cm.email LIKE ? OR cm.mensaje LIKE ? OR cm.asunto LIKE ?)";
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener mensajes
$sql = "SELECT cm.*, 
               au.nombre as asignado_nombre
        FROM contact_messages cm
        LEFT JOIN admin_usuarios au ON cm.assigned_to = au.id
        $where_clause
        ORDER BY cm.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configurar headers para descarga CSV
$filename = 'mensajes_contacto_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Crear output stream
$output = fopen('php://output', 'w');

// BOM para UTF-8 (Excel)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Encabezados
$headers = [
    'ID',
    'Fecha',
    'Nombre',
    'Email',
    'Teléfono',
    'Institución',
    'Asunto',
    'Mensaje',
    'Estado',
    'Asignado a',
    'Notas',
    'IP Address',
    'User Agent'
];
fputcsv($output, $headers, ';');

// Datos
foreach ($mensajes as $msg) {
    $row = [
        $msg['id'],
        date('d/m/Y H:i:s', strtotime($msg['created_at'])),
        $msg['nombre'],
        $msg['email'],
        $msg['telefono'],
        $msg['institucion'] ?? '',
        $msg['asunto'],
        $msg['mensaje'],
        $msg['status'],
        $msg['asignado_nombre'] ?? '',
        $msg['notes'] ?? '',
        $msg['ip_address'] ?? '',
        $msg['user_agent'] ?? ''
    ];
    fputcsv($output, $row, ';');
}

fclose($output);
exit;

