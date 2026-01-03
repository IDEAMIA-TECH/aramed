<?php
/**
 * ========================================
 * ADMIN - EXPORTACIÓN CSV NEWSLETTER
 * ========================================
 * 
 * Exportación de suscriptores a CSV
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
    checkPermission('newsletter', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Procesar exportación
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['export'])) {
    try {
        // Obtener filtros
        $filtro_estado = $_GET['estado'] ?? 'todos';
        $filtro_fuente = $_GET['fuente'] ?? 'todos';
        $busqueda = $_GET['busqueda'] ?? '';
        
        // Construir consulta
        $where_conditions = [];
        $params = [];
        
        if ($filtro_estado !== 'todos') {
            $where_conditions[] = 'status = ?';
            $params[] = $filtro_estado;
        }
        
        if ($filtro_fuente !== 'todos') {
            $where_conditions[] = 'source = ?';
            $params[] = $filtro_fuente;
        }
        
        if (!empty($busqueda)) {
            $where_conditions[] = '(email LIKE ? OR nombre LIKE ?)';
            $search_term = '%' . $busqueda . '%';
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Obtener suscripciones
        $sql = "SELECT * FROM newsletter_simple $where_clause ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $suscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Configurar headers para descarga
        $filename = 'newsletter_export_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Abrir output stream
        $output = fopen('php://output', 'w');
        
        // Agregar BOM para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Escribir encabezados
        $headers = [
            'ID',
            'Email',
            'Nombre',
            'Institución',
            'Tipo de Institución',
            'Estado',
            'Ciudad',
            'Teléfono Oficina',
            'Teléfono Celular',
            'Status',
            'Fuente',
            'Fecha de Registro',
            'Última Actualización'
        ];
        fputcsv($output, $headers);
        
        // Escribir datos
        foreach ($suscripciones as $suscripcion) {
            $row = [
                $suscripcion['id'],
                $suscripcion['email'] ?? '',
                $suscripcion['nombre'] ?? '',
                $suscripcion['institucion'] ?? '',
                $suscripcion['tipo_institucion'] ?? '',
                $suscripcion['estado'] ?? '',
                $suscripcion['ciudad'] ?? '',
                $suscripcion['telefono_oficina'] ?? '',
                $suscripcion['telefono_celular'] ?? '',
                $suscripcion['status'] ?? '',
                $suscripcion['source'] ?? '',
                $suscripcion['created_at'] ?? '',
                $suscripcion['updated_at'] ?? ''
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'exportar', 'newsletter', null, "CSV exportado: " . count($suscripciones) . " registros");
        }
        
        exit;
        
    } catch (Exception $e) {
        die('Error al exportar: ' . $e->getMessage());
    }
}

// Obtener fuentes para filtros
try {
    $stmt = $pdo->query("SELECT DISTINCT source FROM newsletter_simple WHERE source IS NOT NULL AND source != '' ORDER BY source");
    $fuentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $fuentes = [];
}

$current_page = 'export.php';
$current_dir = 'newsletter';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar CSV - Newsletter Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .admin-content {
            background: transparent;
            padding: 2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <div class="col-md-9 admin-content">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">
                                <i class="bi bi-download me-2"></i>Exportar Suscriptores a CSV
                            </h2>
                            <p class="mb-0 opacity-75">Exporta suscriptores con filtros aplicados</p>
                        </div>
                        <a href="../newsletter-simple.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                
                <!-- Formulario de exportación -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-funnel me-2"></i>Filtros de Exportación
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" id="export-form">
                            <input type="hidden" name="export" value="1">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="todos">Todos los estados</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Fuente</label>
                                    <select class="form-select" name="fuente">
                                        <option value="todos">Todas las fuentes</option>
                                        <?php foreach ($fuentes as $fuente): ?>
                                        <option value="<?php echo esc($fuente); ?>"><?php echo esc($fuente); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Búsqueda</label>
                                    <input type="text" class="form-control" name="busqueda" 
                                           placeholder="Email o nombre" value="<?php echo esc($_GET['busqueda'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Nota:</strong> El archivo CSV incluirá todos los campos de las suscripciones y será compatible con Excel.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-download me-2"></i>Exportar CSV
                                </button>
                                <a href="../newsletter-simple.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

