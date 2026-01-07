<?php
/**
 * ========================================
 * ADMIN - IMPORTAR PRODUCTOS DESDE EXCEL
 * ========================================
 * 
 * Permite importar productos desde un archivo Excel/CSV
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
    checkPermission('catalogo', 'crear');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar importación
$success_message = '';
$error_message = '';
$import_results = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    try {
        if (function_exists('checkPermission')) {
            checkPermission('catalogo', 'crear');
        }
        
        $archivo = $_FILES['archivo'];
        $modo = $_POST['modo'] ?? 'update'; // 'update' o 'create'
        $skip_errors = isset($_POST['skip_errors']);
        
        // Validar archivo
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo: ' . $archivo['error']);
        }
        
        $file_ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, ['xlsx', 'xls', 'csv'])) {
            throw new Exception('Formato de archivo no válido. Use Excel (.xlsx, .xls) o CSV (.csv)');
        }
        
        // Leer archivo
        $phpspreadsheet_available = false;
        if (file_exists(__DIR__ . '/../../../includes/library/phpspreadsheet/vendor/autoload.php')) {
            require_once __DIR__ . '/../../../includes/library/phpspreadsheet/vendor/autoload.php';
            $phpspreadsheet_available = true;
        }
        
        $data = [];
        
        if ($phpspreadsheet_available && in_array($file_ext, ['xlsx', 'xls'])) {
            // Leer Excel con PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Primera fila son encabezados
            $headers = array_shift($rows);
            
            // Normalizar encabezados
            $header_map = [
                'ID' => 'id',
                'Código' => 'codigo',
                'Nombre' => 'nombre',
                'Descripción Corta' => 'descripcion_corta',
                'Descripción Larga' => 'descripcion_larga',
                'Marca ID' => 'marca_id',
                'Marca Nombre' => 'marca_nombre',
                'Categoría ID' => 'categoria_id',
                'Categoría Nombre' => 'categoria_nombre',
                'Precio Público' => 'precio_publico',
                'Precio Especial' => 'precio_especial',
                'Disponibilidad' => 'disponibilidad',
                'Estado' => 'estado',
                'Destacado' => 'destacado',
                'Nuevo' => 'nuevo',
                'Promoción' => 'promocion',
                'Características' => 'caracteristicas',
                'Especificaciones Técnicas' => 'especificaciones_tecnicas'
            ];
            
            foreach ($rows as $row) {
                if (empty(array_filter($row))) continue; // Saltar filas vacías
                
                $item = [];
                foreach ($headers as $idx => $header) {
                    $key = $header_map[$header] ?? strtolower(str_replace(' ', '_', $header));
                    $item[$key] = $row[$idx] ?? '';
                }
                $data[] = $item;
            }
            
        } else {
            // Leer CSV
            $handle = fopen($archivo['tmp_name'], 'r');
            if ($handle === false) {
                throw new Exception('No se pudo abrir el archivo CSV');
            }
            
            // Leer BOM si existe
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }
            
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new Exception('El archivo CSV está vacío o es inválido');
            }
            
            // Normalizar encabezados
            $header_map = [
                'ID' => 'id',
                'Código' => 'codigo',
                'Nombre' => 'nombre',
                'Descripción Corta' => 'descripcion_corta',
                'Descripción Larga' => 'descripcion_larga',
                'Marca ID' => 'marca_id',
                'Marca Nombre' => 'marca_nombre',
                'Categoría ID' => 'categoria_id',
                'Categoría Nombre' => 'categoria_nombre',
                'Precio Público' => 'precio_publico',
                'Precio Especial' => 'precio_especial',
                'Disponibilidad' => 'disponibilidad',
                'Estado' => 'estado',
                'Destacado' => 'destacado',
                'Nuevo' => 'nuevo',
                'Promoción' => 'promocion',
                'Características' => 'caracteristicas',
                'Especificaciones Técnicas' => 'especificaciones_tecnicas'
            ];
            
            while (($row = fgetcsv($handle)) !== false) {
                if (empty(array_filter($row))) continue; // Saltar filas vacías
                
                $item = [];
                foreach ($headers as $idx => $header) {
                    $key = $header_map[$header] ?? strtolower(str_replace(' ', '_', $header));
                    $item[$key] = $row[$idx] ?? '';
                }
                $data[] = $item;
            }
            
            fclose($handle);
        }
        
        if (empty($data)) {
            throw new Exception('No se encontraron datos válidos en el archivo');
        }
        
        // Obtener marcas y categorías para mapeo
        $marcas_map = [];
        $stmt = $pdo->query("SELECT id, nombre FROM catalogo_marcas");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $marca) {
            $marcas_map[strtolower(trim($marca['nombre']))] = $marca['id'];
        }
        
        $categorias_map = [];
        $stmt = $pdo->query("SELECT id, nombre FROM catalogo_categorias");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $cat) {
            $categorias_map[strtolower(trim($cat['nombre']))] = $cat['id'];
        }
        
        // Procesar datos
        $results = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];
        
        $pdo->beginTransaction();
        
        try {
            foreach ($data as $row_num => $item) {
                $line_num = $row_num + 2; // +2 porque la fila 1 son encabezados y empezamos desde 0
                
                try {
                    // Validar campos requeridos
                    if (empty($item['codigo']) || empty($item['nombre'])) {
                        throw new Exception("Fila {$line_num}: Código y Nombre son obligatorios");
                    }
                    
                    // Mapear marca
                    $marca_id = null;
                    if (!empty($item['marca_id'])) {
                        $marca_id = (int)$item['marca_id'];
                    } elseif (!empty($item['marca_nombre'])) {
                        $marca_nombre_lower = strtolower(trim($item['marca_nombre']));
                        $marca_id = $marcas_map[$marca_nombre_lower] ?? null;
                        if (!$marca_id) {
                            throw new Exception("Fila {$line_num}: Marca '{$item['marca_nombre']}' no encontrada");
                        }
                    }
                    
                    // Mapear categoría
                    $categoria_id = null;
                    if (!empty($item['categoria_id'])) {
                        $categoria_id = (int)$item['categoria_id'];
                    } elseif (!empty($item['categoria_nombre'])) {
                        $categoria_nombre_lower = strtolower(trim($item['categoria_nombre']));
                        $categoria_id = $categorias_map[$categoria_nombre_lower] ?? null;
                        if (!$categoria_id) {
                            throw new Exception("Fila {$line_num}: Categoría '{$item['categoria_nombre']}' no encontrada");
                        }
                    }
                    
                    // Generar slug
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $item['nombre'])));
                    $slug = preg_replace('/-+/', '-', $slug);
                    $slug = trim($slug, '-');
                    
                    // Verificar si el producto existe
                    $producto_id = null;
                    if (!empty($item['id'])) {
                        $stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE id = ?");
                        $stmt->execute([(int)$item['id']]);
                        $existing = $stmt->fetch();
                        if ($existing) {
                            $producto_id = $existing['id'];
                        }
                    }
                    
                    // Verificar por código
                    if (!$producto_id) {
                        $stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE codigo = ?");
                        $stmt->execute([$item['codigo']]);
                        $existing = $stmt->fetch();
                        if ($existing) {
                            $producto_id = $existing['id'];
                        }
                    }
                    
                    // Normalizar valores booleanos
                    $destacado = in_array(strtolower($item['destacado'] ?? ''), ['sí', 'si', 'yes', '1', 'true', 's']);
                    $nuevo = in_array(strtolower($item['nuevo'] ?? ''), ['sí', 'si', 'yes', '1', 'true', 's']);
                    $promocion = in_array(strtolower($item['promocion'] ?? ''), ['sí', 'si', 'yes', '1', 'true', 's']);
                    
                    // Normalizar estado
                    $estado = strtolower(trim($item['estado'] ?? 'borrador'));
                    if (!in_array($estado, ['activo', 'inactivo', 'borrador'])) {
                        $estado = 'borrador';
                    }
                    
                    // Normalizar disponibilidad
                    $disponibilidad = strtolower(trim($item['disponibilidad'] ?? 'disponible'));
                    if (!in_array($disponibilidad, ['disponible', 'agotado', 'por_pedido'])) {
                        $disponibilidad = 'disponible';
                    }
                    
                    if ($producto_id && $modo === 'update') {
                        // Actualizar producto existente
                        $sql = "
                            UPDATE catalogo_productos SET
                                codigo = ?,
                                nombre = ?,
                                slug = ?,
                                descripcion_corta = ?,
                                descripcion_larga = ?,
                                marca_id = ?,
                                categoria_id = ?,
                                precio_publico = ?,
                                precio_especial = ?,
                                disponibilidad = ?,
                                estado = ?,
                                destacado = ?,
                                nuevo = ?,
                                promocion = ?,
                                caracteristicas = ?,
                                especificaciones = ?,
                                updated_at = NOW()
                            WHERE id = ?
                        ";
                        
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            $item['codigo'],
                            $item['nombre'],
                            $slug,
                            $item['descripcion_corta'] ?? null,
                            $item['descripcion_larga'] ?? null,
                            $marca_id,
                            $categoria_id,
                            !empty($item['precio_publico']) ? (float)$item['precio_publico'] : null,
                            !empty($item['precio_especial']) ? (float)$item['precio_especial'] : null,
                            $disponibilidad,
                            $estado,
                            $destacado ? 1 : 0,
                            $nuevo ? 1 : 0,
                            $promocion ? 1 : 0,
                            !empty($item['caracteristicas']) ? (is_string($item['caracteristicas']) ? $item['caracteristicas'] : json_encode($item['caracteristicas'])) : null,
                            !empty($item['especificaciones']) ? (is_string($item['especificaciones']) ? $item['especificaciones'] : json_encode($item['especificaciones'])) : null,
                            $producto_id
                        ]);
                        
                        $results['updated']++;
                        
                    } elseif ($modo === 'create' || !$producto_id) {
                        // Crear nuevo producto
                        $sql = "
                            INSERT INTO catalogo_productos (
                                codigo, nombre, slug,
                                descripcion_corta, descripcion_larga,
                                marca_id, categoria_id,
                                precio_publico, precio_especial,
                                disponibilidad, estado,
                                destacado, nuevo, promocion,
                                caracteristicas, especificaciones,
                                created_at, updated_at
                            ) VALUES (
                                ?, ?, ?,
                                ?, ?,
                                ?, ?,
                                ?, ?,
                                ?, ?,
                                ?, ?, ?,
                                ?, ?,
                                NOW(), NOW()
                            )
                        ";
                        
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            $item['codigo'],
                            $item['nombre'],
                            $slug,
                            $item['descripcion_corta'] ?? null,
                            $item['descripcion_larga'] ?? null,
                            $marca_id,
                            $categoria_id,
                            !empty($item['precio_publico']) ? (float)$item['precio_publico'] : null,
                            !empty($item['precio_especial']) ? (float)$item['precio_especial'] : null,
                            $disponibilidad,
                            $estado,
                            $destacado ? 1 : 0,
                            $nuevo ? 1 : 0,
                            $promocion ? 1 : 0,
                            !empty($item['caracteristicas']) ? (is_string($item['caracteristicas']) ? $item['caracteristicas'] : json_encode($item['caracteristicas'])) : null,
                            !empty($item['especificaciones']) ? (is_string($item['especificaciones']) ? $item['especificaciones'] : json_encode($item['especificaciones'])) : null
                        ]);
                        
                        $results['created']++;
                    } else {
                        $results['skipped']++;
                    }
                    
                } catch (Exception $e) {
                    $results['errors'][] = $e->getMessage();
                    if (!$skip_errors) {
                        throw $e;
                    }
                }
            }
            
            $pdo->commit();
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'editar', 'catalogo', null, 'productos', [
                    'accion' => 'importacion_masiva',
                    'creados' => $results['created'],
                    'actualizados' => $results['updated'],
                    'omitidos' => $results['skipped']
                ]);
            }
            
            $success_message = "Importación completada: {$results['created']} creados, {$results['updated']} actualizados, {$results['skipped']} omitidos";
            if (!empty($results['errors'])) {
                $error_message = count($results['errors']) . " error(es) encontrado(s)";
            }
            $import_results = $results;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener marcas y categorías para mostrar en la ayuda
$stmt = $pdo->query("SELECT id, nombre FROM catalogo_marcas WHERE estado = 'activo' ORDER BY nombre");
$marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id, nombre FROM catalogo_categorias WHERE estado = 'activo' ORDER BY nombre");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'import.php';
$current_dir = 'productos';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Productos - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: var(--border-radius);
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: var(--shadow);
        }
        
        .page-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem;
        }
        
        .upload-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: var(--border-radius);
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: var(--secondary-color);
            background: #f8f9fa;
        }
        
        .upload-area.dragover {
            border-color: var(--secondary-color);
            background: #e3f2fd;
        }
        
        .info-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-card h5 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .info-card ul {
            margin: 0;
            padding-left: 1.5rem;
        }
        
        .info-card li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-upload me-2"></i>Importar Productos
                            </h2>
                            <p class="mb-0 opacity-75">Importa productos desde Excel o CSV</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($import_results && !empty($import_results['errors'])): ?>
                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>Errores encontrados:</h6>
                    <ul class="mb-0">
                        <?php foreach ($import_results['errors'] as $error): ?>
                        <li><?php echo esc($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Formulario de Importación -->
                        <div class="upload-card">
                            <h4 class="mb-4">
                                <i class="bi bi-file-earmark-spreadsheet me-2"></i>Subir Archivo
                            </h4>
                            
                            <form method="POST" enctype="multipart/form-data" id="import-form">
                                <div class="upload-area" id="upload-area">
                                    <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                                    <h5 class="mt-3">Arrastra tu archivo aquí o haz clic para seleccionar</h5>
                                    <p class="text-muted mb-3">
                                        Formatos soportados: Excel (.xlsx, .xls) o CSV (.csv)
                                    </p>
                                    <input type="file" 
                                           name="archivo" 
                                           id="archivo" 
                                           accept=".xlsx,.xls,.csv"
                                           class="d-none" 
                                           required>
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('archivo').click()">
                                        <i class="bi bi-folder2-open me-2"></i>Seleccionar Archivo
                                    </button>
                                    <div id="file-name" class="mt-3 text-muted"></div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="form-label"><strong>Modo de Importación</strong></label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="modo" id="modo_update" value="update" checked>
                                        <label class="form-check-label" for="modo_update">
                                            <strong>Actualizar existentes y crear nuevos</strong>
                                            <small class="d-block text-muted">Si el producto existe (por ID o código), se actualiza. Si no existe, se crea.</small>
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="modo" id="modo_create" value="create">
                                        <label class="form-check-label" for="modo_create">
                                            <strong>Solo crear nuevos</strong>
                                            <small class="d-block text-muted">Solo crea productos nuevos. Si ya existe, se omite.</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="skip_errors" id="skip_errors">
                                        <label class="form-check-label" for="skip_errors">
                                            Continuar aunque haya errores
                                            <small class="d-block text-muted">Omite las filas con errores y continúa con el resto.</small>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-upload me-2"></i>Importar Productos
                                    </button>
                                    <a href="export.php" class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-download me-2"></i>Descargar Plantilla
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Información -->
                        <div class="info-card">
                            <h5><i class="bi bi-info-circle me-2"></i>Instrucciones</h5>
                            <ul>
                                <li>Descarga la plantilla de ejemplo</li>
                                <li>Completa los datos de los productos</li>
                                <li>Sube el archivo Excel o CSV</li>
                                <li>Revisa los resultados de la importación</li>
                            </ul>
                        </div>
                        
                        <div class="info-card">
                            <h5><i class="bi bi-file-text me-2"></i>Campos Requeridos</h5>
                            <ul class="small">
                                <li><strong>Código:</strong> Código único del producto</li>
                                <li><strong>Nombre:</strong> Nombre del producto</li>
                                <li><strong>Marca:</strong> ID o Nombre de la marca</li>
                                <li><strong>Categoría:</strong> ID o Nombre de la categoría</li>
                            </ul>
                        </div>
                        
                        <div class="info-card">
                            <h5><i class="bi bi-list-check me-2"></i>Marcas Disponibles</h5>
                            <div class="small" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($marcas as $marca): ?>
                                <div class="mb-1">
                                    <code><?php echo $marca['id']; ?></code> - <?php echo esc($marca['nombre']); ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <h5><i class="bi bi-tags me-2"></i>Categorías Disponibles</h5>
                            <div class="small" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($categorias as $cat): ?>
                                <div class="mb-1">
                                    <code><?php echo $cat['id']; ?></code> - <?php echo esc($cat['nombre']); ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Drag and drop
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('archivo');
        const fileName = document.getElementById('file-name');
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function() {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileName();
            }
        });
        
        fileInput.addEventListener('change', updateFileName);
        
        function updateFileName() {
            if (fileInput.files.length > 0) {
                fileName.innerHTML = '<i class="bi bi-file-earmark me-2"></i><strong>' + fileInput.files[0].name + '</strong> (' + formatFileSize(fileInput.files[0].size) + ')';
            } else {
                fileName.innerHTML = '';
            }
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
        
        // Validar formulario
        document.getElementById('import-form').addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Por favor selecciona un archivo');
                return false;
            }
            
            if (!confirm('¿Estás seguro de importar los productos? Esta acción puede crear o actualizar múltiples productos.')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>

