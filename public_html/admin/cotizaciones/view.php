<?php
/**
 * ========================================
 * ADMIN - VISTA DETALLADA DE COTIZACIÓN
 * ========================================
 * 
 * Vista y gestión detallada de una cotización
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

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Obtener ID de la cotización
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

$success_message = '';
$error_message = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar permisos
        if (function_exists('checkPermission')) {
            checkPermission('cotizaciones', 'editar');
        }
        
        $estado_cotizacion = $_POST['estado_cotizacion'] ?? '';
        $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
        $notas_internas = trim($_POST['notas_internas'] ?? '');
        
        // Obtener estado anterior para auditoría
        $stmt = $pdo->prepare("SELECT estado_cotizacion, assigned_to FROM cotizaciones WHERE id = ?");
        $stmt->execute([$id]);
        $cotizacion_anterior = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Actualizar cotización
        $stmt = $pdo->prepare("
            UPDATE cotizaciones 
            SET estado_cotizacion = ?, assigned_to = ?, notas_internas = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$estado_cotizacion, $assigned_to, $notas_internas, $id]);
        
        // Registrar cambios en auditoría
        if ($cotizacion_anterior['estado_cotizacion'] !== $estado_cotizacion) {
            $stmt = $pdo->prepare("
                INSERT INTO cotizacion_auditoria 
                (cotizacion_id, usuario_id, accion, campo_anterior, valor_anterior, campo_nuevo, valor_nuevo)
                VALUES (?, ?, 'estado_cambiado', 'estado_cotizacion', ?, 'estado_cotizacion', ?)
            ");
            $stmt->execute([$id, $current_user['id'], $cotizacion_anterior['estado_cotizacion'], $estado_cotizacion]);
        }
        
        if ($cotizacion_anterior['assigned_to'] != $assigned_to) {
            $stmt = $pdo->prepare("
                INSERT INTO cotizacion_auditoria 
                (cotizacion_id, usuario_id, accion, campo_anterior, valor_anterior, campo_nuevo, valor_nuevo)
                VALUES (?, ?, 'asignado', 'assigned_to', ?, 'assigned_to', ?)
            ");
            $stmt->execute([$id, $current_user['id'], $cotizacion_anterior['assigned_to'] ?? 'null', $assigned_to ?? 'null']);
        }
        
        // Registrar actividad general
        if (function_exists('logActivity')) {
            logActivity($current_user['id'], 'editar', 'cotizaciones', $id, 'cotizacion', [
                'estado' => $estado_cotizacion,
                'assigned_to' => $assigned_to
            ]);
        }
        
        $success_message = 'Cotización actualizada exitosamente';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener cotización
$stmt = $pdo->prepare("
    SELECT c.*, 
           au.nombre as ejecutivo_nombre,
           au.email as ejecutivo_email
    FROM cotizaciones c
    LEFT JOIN admin_usuarios au ON c.assigned_to = au.id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    header('Location: index.php');
    exit;
}

// Obtener items de la cotización
$stmt = $pdo->prepare("
    SELECT ci.*, 
           p.nombre as producto_catalogo_nombre,
           p.codigo as producto_catalogo_codigo,
           m.nombre as marca_nombre
    FROM cotizacion_items ci
    LEFT JOIN catalogo_productos p ON ci.producto_id = p.id
    LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
    WHERE ci.cotizacion_id = ?
    ORDER BY ci.id ASC
");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totales
$total_items = count($items);
$subtotal_total = 0;
foreach ($items as $item) {
    $subtotal_total += (float)($item['subtotal'] ?? 0);
}

// Obtener historial de auditoría
$stmt = $pdo->prepare("
    SELECT ca.*, 
           au.nombre as usuario_nombre
    FROM cotizacion_auditoria ca
    LEFT JOIN admin_usuarios au ON ca.usuario_id = au.id
    WHERE ca.cotizacion_id = ?
    ORDER BY ca.created_at DESC
    LIMIT 50
");
$stmt->execute([$id]);
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios para asignación
$stmt = $pdo->query("SELECT id, nombre, email FROM admin_usuarios WHERE estado = 'activo' ORDER BY nombre");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'view.php';
$current_dir = 'cotizaciones';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cotización - Admin <?php echo SITE_NAME; ?></title>
    
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
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .folio-display {
            font-family: monospace;
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .item-row {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
        }
        
        .item-row:last-child {
            border-bottom: none;
        }
        
        .historial-item {
            border-left: 3px solid #667eea;
            padding-left: 1rem;
            margin-bottom: 1rem;
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <div class="folio-display mb-2"><?php echo esc($cotizacion['folio']); ?></div>
                            <p class="mb-0 opacity-75">Cotización #<?php echo $cotizacion['id']; ?></p>
                        </div>
                        <a href="index.php" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Lista
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
                
                <div class="row">
                    <!-- Información Principal -->
                    <div class="col-lg-8">
                        <!-- Datos del Cliente -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-building me-2"></i>Información del Cliente
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Institución</label>
                                    <div class="fw-bold"><?php echo esc($cotizacion['institucion']); ?></div>
                                    <small class="text-muted"><?php echo esc($cotizacion['tipo_institucion']); ?></small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Ubicación</label>
                                    <div>
                                        <i class="bi bi-geo-alt me-1"></i><?php echo esc($cotizacion['ciudad']); ?>, <?php echo esc($cotizacion['estado']); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Contacto</label>
                                    <div class="fw-bold"><?php echo esc($cotizacion['nombre']); ?></div>
                                    <small class="text-muted"><?php echo esc($cotizacion['puesto']); ?></small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Email</label>
                                    <div>
                                        <a href="mailto:<?php echo esc($cotizacion['email_oficial']); ?>" class="text-decoration-none">
                                            <i class="bi bi-envelope me-1"></i><?php echo esc($cotizacion['email_oficial']); ?>
                                        </a>
                                    </div>
                                    <?php if ($cotizacion['email_alterno']): ?>
                                    <small class="text-muted">
                                        <a href="mailto:<?php echo esc($cotizacion['email_alterno']); ?>">
                                            <?php echo esc($cotizacion['email_alterno']); ?>
                                        </a>
                                    </small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Teléfono</label>
                                    <div>
                                        <a href="tel:<?php echo esc($cotizacion['telefono_oficina']); ?>" class="text-decoration-none">
                                            <i class="bi bi-telephone me-1"></i><?php echo esc($cotizacion['telefono_oficina']); ?>
                                        </a>
                                        <?php if ($cotizacion['extension']): ?>
                                        <span class="text-muted">Ext. <?php echo esc($cotizacion['extension']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($cotizacion['telefono_celular']): ?>
                                    <div>
                                        <a href="tel:<?php echo esc($cotizacion['telefono_celular']); ?>" class="text-decoration-none">
                                            <i class="bi bi-phone me-1"></i><?php echo esc($cotizacion['telefono_celular']); ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Items de la Cotización -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-box-seam me-2"></i>Productos Cotizados (<?php echo $total_items; ?>)
                            </h5>
                            <?php if (empty($items)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Esta cotización no tiene items. Fue migrada desde el sistema anterior.
                                <?php if ($cotizacion['producto_interes']): ?>
                                <br><strong>Producto de interés:</strong> <?php echo esc($cotizacion['producto_interes']); ?>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unit.</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong><?php echo esc($item['producto_nombre']); ?></strong>
                                                    <?php if ($item['producto_codigo']): ?>
                                                    <br><small class="text-muted">Código: <?php echo esc($item['producto_codigo']); ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($item['marca_nombre']): ?>
                                                    <br><small class="text-muted">Marca: <?php echo esc($item['marca_nombre']); ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($item['notas']): ?>
                                                    <br><small class="text-info"><?php echo esc($item['notas']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?php echo $item['cantidad']; ?></td>
                                            <td>
                                                <?php if ($item['precio_unitario']): ?>
                                                $<?php echo number_format($item['precio_unitario'], 2); ?>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($item['descuento'] > 0): ?>
                                                <?php echo number_format($item['descuento'], 2); ?>%
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($item['subtotal']): ?>
                                                <strong>$<?php echo number_format($item['subtotal'], 2); ?></strong>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total:</th>
                                            <th>$<?php echo number_format($subtotal_total, 2); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Información Adicional -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-info-circle me-2"></i>Información Adicional
                            </h5>
                            <div class="row">
                                <?php if ($cotizacion['producto_interes']): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Producto de Interés</label>
                                    <div><?php echo esc($cotizacion['producto_interes']); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($cotizacion['fecha_compra_aprox']): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Fecha Aproximada de Compra</label>
                                    <div><?php echo date('d/m/Y', strtotime($cotizacion['fecha_compra_aprox'])); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($cotizacion['presupuesto_estimado']): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Presupuesto Estimado</label>
                                    <div class="fw-bold">$<?php echo number_format($cotizacion['presupuesto_estimado'], 2); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($cotizacion['observaciones']): ?>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted small">Observaciones del Cliente</label>
                                    <div class="p-3 bg-light rounded"><?php echo nl2br(esc($cotizacion['observaciones'])); ?></div>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Fecha de recepción:</small><br>
                                    <strong><?php echo date('d/m/Y H:i:s', strtotime($cotizacion['created_at'])); ?></strong>
                                </div>
                                <?php if ($cotizacion['updated_at']): ?>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Última actualización:</small><br>
                                    <strong><?php echo date('d/m/Y H:i:s', strtotime($cotizacion['updated_at'])); ?></strong>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Historial de Auditoría -->
                        <?php if (!empty($historial)): ?>
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-clock-history me-2"></i>Historial de Cambios
                            </h5>
                            <?php foreach ($historial as $hist): ?>
                            <div class="historial-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong><?php echo esc($hist['usuario_nombre'] ?? 'Sistema'); ?></strong>
                                        <span class="badge bg-secondary ms-2"><?php echo esc($hist['accion']); ?></span>
                                        <div class="mt-1">
                                            <?php if ($hist['campo_anterior'] && $hist['valor_anterior']): ?>
                                            <small class="text-muted">
                                                <?php echo esc($hist['campo_anterior']); ?>: 
                                                <span class="text-danger"><?php echo esc($hist['valor_anterior']); ?></span>
                                                →
                                                <span class="text-success"><?php echo esc($hist['valor_nuevo'] ?? ''); ?></span>
                                            </small>
                                            <?php endif; ?>
                                            <?php if ($hist['detalles']): ?>
                                            <br><small class="text-muted"><?php echo esc($hist['detalles']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($hist['created_at'])); ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Panel de Gestión -->
                    <div class="col-lg-4">
                        <form method="POST" action="">
                            <div class="info-card">
                                <h5 class="mb-3">
                                    <i class="bi bi-gear me-2"></i>Gestión de Cotización
                                </h5>
                                
                                <!-- Estado -->
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado_cotizacion" required>
                                        <option value="nueva" <?php echo $cotizacion['estado_cotizacion'] === 'nueva' ? 'selected' : ''; ?>>Nueva</option>
                                        <option value="en_seguimiento" <?php echo $cotizacion['estado_cotizacion'] === 'en_seguimiento' ? 'selected' : ''; ?>>En Seguimiento</option>
                                        <option value="cotizada" <?php echo $cotizacion['estado_cotizacion'] === 'cotizada' ? 'selected' : ''; ?>>Cotizada</option>
                                        <option value="enviada" <?php echo $cotizacion['estado_cotizacion'] === 'enviada' ? 'selected' : ''; ?>>Enviada</option>
                                        <option value="cerrada_ganada" <?php echo $cotizacion['estado_cotizacion'] === 'cerrada_ganada' ? 'selected' : ''; ?>>Cerrada (Ganada)</option>
                                        <option value="cerrada_perdida" <?php echo $cotizacion['estado_cotizacion'] === 'cerrada_perdida' ? 'selected' : ''; ?>>Cerrada (Perdida)</option>
                                    </select>
                                </div>
                                
                                <!-- Asignar a -->
                                <div class="mb-3">
                                    <label class="form-label">Asignar a Ejecutivo</label>
                                    <select class="form-select" name="assigned_to">
                                        <option value="">Sin asignar</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['id']; ?>" <?php echo $cotizacion['assigned_to'] == $usuario['id'] ? 'selected' : ''; ?>>
                                            <?php echo esc($usuario['nombre']); ?> (<?php echo esc($usuario['email']); ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Notas internas -->
                                <div class="mb-3">
                                    <label class="form-label">Notas Internas</label>
                                    <textarea class="form-control" name="notas_internas" rows="6" placeholder="Notas sobre esta cotización..."><?php echo esc($cotizacion['notas_internas'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Solo visible para administradores</small>
                                </div>
                                
                                <!-- PDF Propuesta -->
                                <?php if ($cotizacion['pdf_propuesta']): ?>
                                <div class="mb-3">
                                    <label class="form-label">PDF de Propuesta</label>
                                    <div>
                                        <a href="<?php echo SITE_URL . '/' . esc($cotizacion['pdf_propuesta']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-pdf me-1"></i>Ver PDF
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                        
                        <!-- Acciones Rápidas -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-lightning me-2"></i>Acciones Rápidas
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="mailto:<?php echo esc($cotizacion['email_oficial']); ?>?subject=<?php echo urlencode('Cotización ' . $cotizacion['folio']); ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-envelope me-1"></i>Enviar Email
                                </a>
                                <a href="tel:<?php echo esc($cotizacion['telefono_oficina']); ?>" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-telephone me-1"></i>Llamar
                                </a>
                                <?php if (function_exists('hasPermission') && hasPermission($current_user['id'] ?? 0, 'cotizaciones', 'exportar')): ?>
                                <a href="export.php?id=<?php echo $id; ?>" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-download me-1"></i>Exportar
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

