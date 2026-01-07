<?php
/**
 * ========================================
 * ADMIN - EXPORTAR PRODUCTOS A EXCEL
 * ========================================
 * 
 * Exporta todos los productos a un archivo Excel/CSV
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/connection.php';
require_once __DIR__ . '/../../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('catalogo', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener todos los productos con información relacionada
$sql = "
    SELECT 
        p.id,
        p.codigo,
        p.nombre,
        p.descripcion_corta,
        p.descripcion_larga,
        p.marca_id,
        m.nombre as marca_nombre,
        p.categoria_id,
        c.nombre as categoria_nombre,
        p.precio_publico,
        p.precio_especial,
        p.disponibilidad,
        p.estado,
        p.destacado,
        p.nuevo,
        p.promocion,
        p.caracteristicas,
        p.especificaciones,
        p.created_at,
        p.updated_at
    FROM catalogo_productos p
    LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
    LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
    ORDER BY p.id ASC
";

$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si PhpSpreadsheet está disponible
$phpspreadsheet_available = false;
if (file_exists(__DIR__ . '/../../../includes/library/phpspreadsheet/vendor/autoload.php')) {
    require_once __DIR__ . '/../../../includes/library/phpspreadsheet/vendor/autoload.php';
    $phpspreadsheet_available = true;
}

// Determinar formato de salida
$format = isset($_GET['format']) ? $_GET['format'] : 'xlsx';
if (!in_array($format, ['xlsx', 'csv'])) {
    $format = 'xlsx';
}

if ($phpspreadsheet_available && $format === 'xlsx') {
    // Exportar a Excel usando PhpSpreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Productos');
    
    // Encabezados
    $headers = [
        'ID',
        'Código',
        'Nombre',
        'Descripción Corta',
        'Descripción Larga',
        'Marca ID',
        'Marca Nombre',
        'Categoría ID',
        'Categoría Nombre',
        'Precio Público',
        'Precio Especial',
        'Disponibilidad',
        'Estado',
        'Destacado',
        'Nuevo',
        'Promoción',
        'Características',
        'Especificaciones',
        'Fecha Creación',
        'Fecha Actualización'
    ];
    
    // Estilo para encabezados
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '2c3e50'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];
    
    // Escribir encabezados
    $col = 1;
    foreach ($headers as $header) {
        $sheet->setCellValueByColumnAndRow($col, 1, $header);
        $sheet->getStyleByColumnAndRow($col, 1)->applyFromArray($headerStyle);
        $col++;
    }
    
    // Escribir datos
    $row = 2;
    foreach ($productos as $producto) {
        $col = 1;
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['id']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['codigo']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['nombre']);
        $sheet->setCellValueByColumnAndRow($col++, $row, strip_tags($producto['descripcion_corta'] ?? ''));
        $sheet->setCellValueByColumnAndRow($col++, $row, strip_tags($producto['descripcion_larga'] ?? ''));
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['marca_id']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['marca_nombre'] ?? '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['categoria_id']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['categoria_nombre'] ?? '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['precio_publico'] ?? '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['precio_especial'] ?? '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['disponibilidad'] ?? '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['estado']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['destacado'] ? 'Sí' : 'No');
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['nuevo'] ? 'Sí' : 'No');
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['promocion'] ? 'Sí' : 'No');
        $sheet->setCellValueByColumnAndRow($col++, $row, is_string($producto['caracteristicas'] ?? null) ? strip_tags($producto['caracteristicas']) : json_encode($producto['caracteristicas'] ?? []));
        $sheet->setCellValueByColumnAndRow($col++, $row, is_string($producto['especificaciones'] ?? null) ? strip_tags($producto['especificaciones']) : json_encode($producto['especificaciones'] ?? []));
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['created_at']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $producto['updated_at']);
        $row++;
    }
    
    // Ajustar ancho de columnas
    foreach (range(1, count($headers)) as $col) {
        $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
    }
    
    // Configurar headers para descarga
    $filename = 'productos_export_' . date('Y-m-d_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
    
} else {
    // Exportar a CSV (fallback o formato solicitado)
    $filename = 'productos_export_' . date('Y-m-d_His') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Abrir output stream
    $output = fopen('php://output', 'w');
    
    // Agregar BOM para UTF-8 (Excel)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Encabezados
    $headers = [
        'ID',
        'Código',
        'Nombre',
        'Descripción Corta',
        'Descripción Larga',
        'Marca ID',
        'Marca Nombre',
        'Categoría ID',
        'Categoría Nombre',
        'Precio Público',
        'Precio Especial',
        'Disponibilidad',
        'Estado',
        'Destacado',
        'Nuevo',
        'Promoción',
        'Características',
        'Especificaciones',
        'Fecha Creación',
        'Fecha Actualización'
    ];
    
    fputcsv($output, $headers);
    
    // Datos
    foreach ($productos as $producto) {
        $row = [
            $producto['id'],
            $producto['codigo'],
            $producto['nombre'],
            strip_tags($producto['descripcion_corta'] ?? ''),
            strip_tags($producto['descripcion_larga'] ?? ''),
            $producto['marca_id'],
            $producto['marca_nombre'] ?? '',
            $producto['categoria_id'],
            $producto['categoria_nombre'] ?? '',
            $producto['precio_publico'] ?? '',
            $producto['precio_especial'] ?? '',
            $producto['disponibilidad'] ?? '',
            $producto['estado'],
            $producto['destacado'] ? 'Sí' : 'No',
            $producto['nuevo'] ? 'Sí' : 'No',
            $producto['promocion'] ? 'Sí' : 'No',
            is_string($producto['caracteristicas'] ?? null) ? strip_tags($producto['caracteristicas']) : json_encode($producto['caracteristicas'] ?? []),
            is_string($producto['especificaciones'] ?? null) ? strip_tags($producto['especificaciones']) : json_encode($producto['especificaciones'] ?? []),
            $producto['created_at'],
            $producto['updated_at']
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

