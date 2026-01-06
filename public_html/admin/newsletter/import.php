<?php
/**
 * ========================================
 * ADMIN - IMPORTACIÓN CSV NEWSLETTER
 * ========================================
 * 
 * Importación masiva de suscriptores desde CSV
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
    checkPermission('newsletter', 'editar');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

$success_message = '';
$error_message = '';
$import_results = null;

// Procesar importación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    try {
        $file = $_FILES['csv_file'];
        
        // Validar archivo
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo');
        }
        
        if ($file['type'] !== 'text/csv' && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
            throw new Exception('El archivo debe ser CSV');
        }
        
        // Leer archivo CSV
        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            throw new Exception('No se pudo leer el archivo');
        }
        
        // Leer primera línea (encabezados)
        $headers = fgetcsv($handle);
        if (!$headers) {
            throw new Exception('El archivo CSV está vacío');
        }
        
        // Normalizar encabezados
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);
        
        // Mapeo de columnas (flexible)
        $column_map = [
            'email' => ['email', 'email_oficial', 'correo', 'e-mail'],
            'nombre' => ['nombre', 'name', 'contacto'],
            'institucion' => ['institucion', 'institution', 'empresa', 'organizacion'],
            'tipo_institucion' => ['tipo_institucion', 'tipo', 'tipo de institucion'],
            'estado' => ['estado', 'state'],
            'ciudad' => ['ciudad', 'city', 'ciudad'],
            'telefono' => ['telefono', 'telefono_oficina', 'phone', 'tel'],
            'status' => ['status', 'estado_suscripcion', 'estado']
        ];
        
        // Encontrar índices de columnas
        $column_indices = [];
        foreach ($column_map as $db_field => $possible_names) {
            foreach ($possible_names as $name) {
                $index = array_search($name, $headers);
                if ($index !== false) {
                    $column_indices[$db_field] = $index;
                    break;
                }
            }
        }
        
        if (empty($column_indices['email'])) {
            throw new Exception('No se encontró la columna de email en el CSV');
        }
        
        // Procesar filas
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $row_num = 1;
        
        while (($row = fgetcsv($handle)) !== false) {
            $row_num++;
            
            if (count($row) < count($headers)) {
                continue; // Fila incompleta
            }
            
            $email = trim($row[$column_indices['email']] ?? '');
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                $errors[] = "Fila $row_num: Email inválido o vacío";
                continue;
            }
            
            // Preparar datos
            $data = [
                'email_oficial' => $email,
                'nombre' => trim($row[$column_indices['nombre']] ?? 'Usuario'),
                'institucion' => trim($row[$column_indices['institucion']] ?? ''),
                'tipo_institucion' => trim($row[$column_indices['tipo_institucion']] ?? 'Otro'),
                'estado' => trim($row[$column_indices['estado']] ?? ''),
                'ciudad' => trim($row[$column_indices['ciudad']] ?? ''),
                'telefono_oficina' => trim($row[$column_indices['telefono']] ?? ''),
                'status' => trim($row[$column_indices['status']] ?? 'active')
            ];
            
            // Validar status
            if (!in_array($data['status'], ['active', 'inactive', 'unsubscribed'])) {
                $data['status'] = 'active';
            }
            
            try {
                // Verificar si existe (usar email como campo principal)
                $stmt = $pdo->prepare("SELECT id FROM newsletter_simple WHERE email = ?");
                $stmt->execute([$email]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Actualizar
                    $sql = "UPDATE newsletter_simple SET 
                            nombre = ?, institucion = ?, tipo_institucion = ?, 
                            estado = ?, ciudad = ?, telefono_oficina = ?, status = ?, updated_at = NOW()
                            WHERE email = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        $data['nombre'], $data['institucion'], $data['tipo_institucion'],
                        $data['estado'], $data['ciudad'], $data['telefono_oficina'],
                        $data['status'], $email
                    ]);
                    $updated++;
                } else {
                    // Insertar
                    $sql = "INSERT INTO newsletter_simple 
                            (email, nombre, institucion, tipo_institucion, estado, ciudad, telefono_oficina, status, source, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'import', NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        $email, $data['nombre'], $data['institucion'],
                        $data['tipo_institucion'], $data['estado'], $data['ciudad'],
                        $data['telefono_oficina'], $data['status']
                    ]);
                    $imported++;
                }
            } catch (Exception $e) {
                $skipped++;
                $errors[] = "Fila $row_num: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        $import_results = [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors
        ];
        
        $success_message = "Importación completada: $imported nuevos, $updated actualizados, $skipped omitidos";
        
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'importar', 'newsletter', null, "CSV importado: $imported nuevos, $updated actualizados");
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$current_page = 'import.php';
$current_dir = 'newsletter';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar CSV - Newsletter Admin <?php echo SITE_NAME; ?></title>
    
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .upload-area:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }
        
        .upload-area.dragover {
            border-color: #667eea;
            background: #e7f3ff;
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
                                <i class="bi bi-upload me-2"></i>Importar Suscriptores desde CSV
                            </h2>
                            <p class="mb-0 opacity-75">Importación masiva de suscriptores desde archivo CSV</p>
                        </div>
                        <a href="../newsletter-simple.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo esc($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Resultados de importación -->
                <?php if ($import_results): ?>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Resultados de la Importación
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                    <h3 class="text-success mb-1"><?php echo $import_results['imported']; ?></h3>
                                    <small class="text-muted">Nuevos Importados</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                    <h3 class="text-info mb-1"><?php echo $import_results['updated']; ?></h3>
                                    <small class="text-muted">Actualizados</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                    <h3 class="text-warning mb-1"><?php echo $import_results['skipped']; ?></h3>
                                    <small class="text-muted">Omitidos</small>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($import_results['errors'])): ?>
                        <div class="mt-3">
                            <h6>Errores encontrados:</h6>
                            <ul class="list-unstyled">
                                <?php foreach (array_slice($import_results['errors'], 0, 10) as $error): ?>
                                <li class="text-danger"><small><?php echo esc($error); ?></small></li>
                                <?php endforeach; ?>
                                <?php if (count($import_results['errors']) > 10): ?>
                                <li class="text-muted"><small>... y <?php echo count($import_results['errors']) - 10; ?> errores más</small></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Formulario de importación -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Seleccionar Archivo CSV
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="import-form">
                            <div class="upload-area" id="upload-area">
                                <i class="bi bi-cloud-upload display-4 text-muted mb-3"></i>
                                <h5>Arrastra y suelta tu archivo CSV aquí</h5>
                                <p class="text-muted">o</p>
                                <input type="file" name="csv_file" id="csv_file" accept=".csv" class="d-none" required>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('csv_file').click()">
                                    <i class="bi bi-folder2-open me-2"></i>Seleccionar Archivo
                                </button>
                                <p class="mt-3 mb-0">
                                    <small class="text-muted">Archivo seleccionado: <span id="file-name">Ninguno</span></small>
                                </p>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <h6><i class="bi bi-info-circle me-2"></i>Formato del CSV</h6>
                                <p class="mb-2">El archivo CSV debe contener las siguientes columnas (el orden no importa):</p>
                                <ul class="mb-0">
                                    <li><strong>email</strong> o <strong>email_oficial</strong> (obligatorio)</li>
                                    <li><strong>nombre</strong> o <strong>name</strong></li>
                                    <li><strong>institucion</strong> o <strong>institution</strong></li>
                                    <li><strong>tipo_institucion</strong> o <strong>tipo</strong></li>
                                    <li><strong>estado</strong> o <strong>state</strong></li>
                                    <li><strong>ciudad</strong> o <strong>city</strong></li>
                                    <li><strong>telefono</strong> o <strong>telefono_oficina</strong></li>
                                    <li><strong>status</strong> (active, inactive, unsubscribed)</li>
                                </ul>
                            </div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-2"></i>Importar CSV
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
    <script>
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('csv_file');
        const fileName = document.getElementById('file-name');
        
        // Mostrar nombre del archivo
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'Ninguno';
            }
        });
        
        // Drag and drop
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
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = e.dataTransfer.files[0].name;
            }
        });
    </script>
</body>
</html>

