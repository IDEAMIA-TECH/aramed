<?php
/**
 * ========================================
 * ADMIN - CAMPAÑAS DE NEWSLETTER
 * ========================================
 * 
 * Módulo para crear y enviar campañas de newsletter usando plantillas
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
require_once __DIR__ . '/../../includes/email_functions.php';
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

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'enviar_campana') {
        try {
            $plantilla_id = (int)$_POST['plantilla_id'];
            $filtro_estado = $_POST['filtro_estado'] ?? 'activo';
            $asunto_personalizado = sanitizeInput($_POST['asunto_personalizado'] ?? '');
            $variables_personalizadas = $_POST['variables_personalizadas'] ?? '{}';
            $enviar_inmediatamente = isset($_POST['enviar_inmediatamente']) && $_POST['enviar_inmediatamente'] === '1';
            
            if (empty($plantilla_id)) {
                throw new Exception('Debes seleccionar una plantilla');
            }
            
            // Obtener plantilla
            $stmt = $pdo->prepare("SELECT * FROM newsletter_templates WHERE id = ? AND estado = 'activo'");
            $stmt->execute([$plantilla_id]);
            $plantilla = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$plantilla) {
                throw new Exception('Plantilla no encontrada o no está activa');
            }
            
            // Parsear variables personalizadas
            $variables = json_decode($variables_personalizadas, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $variables = json_decode($plantilla['variables'] ?? '{}', true);
            }
            
            // Obtener destinatarios
            $sql_destinatarios = "SELECT * FROM newsletter_simple WHERE status = ?";
            $stmt_destinatarios = $pdo->prepare($sql_destinatarios);
            $stmt_destinatarios->execute([$filtro_estado]);
            $destinatarios = $stmt_destinatarios->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($destinatarios)) {
                throw new Exception('No hay destinatarios con el estado seleccionado');
            }
            
            // Verificar si las tablas existen
            $stmt_check = $pdo->query("SHOW TABLES LIKE 'newsletter_campanas'");
            if ($stmt_check->rowCount() == 0) {
                throw new Exception('Las tablas de campañas no existen. Por favor ejecuta el script SQL: database/fase2/24_create_newsletter_campanas_tables.sql');
            }
            
            // Registrar campaña
            $asunto_final = !empty($asunto_personalizado) ? $asunto_personalizado : $plantilla['asunto'];
            
            $sql_campana = "INSERT INTO newsletter_campanas (nombre, plantilla_id, asunto, filtro_estado, total_destinatarios, enviados, fallidos, estado, creado_por) 
                           VALUES (?, ?, ?, ?, ?, 0, 0, ?, ?)";
            $estado_inicial = $enviar_inmediatamente ? 'en_proceso' : 'programada';
            $stmt_campana = $pdo->prepare($sql_campana);
            $stmt_campana->execute([
                "Campaña " . date('Y-m-d H:i:s'),
                $plantilla_id,
                $asunto_final,
                $filtro_estado,
                count($destinatarios),
                $estado_inicial,
                $current_user['id']
            ]);
            $campana_id = $pdo->lastInsertId();
            
            // Procesar envío si es inmediato
            if ($enviar_inmediatamente) {
                $enviados = 0;
                $fallidos = 0;
                
                foreach ($destinatarios as $destinatario) {
                    // Crear registro de envío ANTES de enviar (para tener el ID para tracking)
                    $envio_id = null;
                    if ($tabla_envios_existe) {
                        $sql_envio_pre = "INSERT INTO newsletter_envios (campana_id, destinatario_id, email, estado, enviado_at) 
                                         VALUES (?, ?, ?, 'enviado', NOW())";
                        $stmt_envio_pre = $pdo->prepare($sql_envio_pre);
                        $stmt_envio_pre->execute([$campana_id, $destinatario['id'], $destinatario['email']]);
                        $envio_id = $pdo->lastInsertId();
                    }
                    
                    // Reemplazar variables en el contenido
                    $contenido_html = $plantilla['contenido_html'];
                    $asunto_email = $asunto_final;
                    
                    // Reemplazar variables dinámicas
                    $contenido_html = str_replace('{{nombre_contacto}}', $destinatario['nombre'] ?? 'Usuario', $contenido_html);
                    $contenido_html = str_replace('{{email_contacto}}', $destinatario['email'], $contenido_html);
                    $contenido_html = str_replace('{{fecha_actual}}', date('d/m/Y'), $contenido_html);
                    
                    // Link de desuscripción con tracking
                    if ($envio_id) {
                        $link_desuscripcion = siteUrl('track-click.php?e=' . $envio_id . '&c=' . $campana_id . '&url=' . urlencode('desuscribir.php?token=' . md5($destinatario['email'])));
                    } else {
                        // Fallback sin tracking
                        $link_desuscripcion = siteUrl('desuscribir.php?token=' . md5($destinatario['email']));
                    }
                    $contenido_html = str_replace('{{link_desuscripcion}}', $link_desuscripcion, $contenido_html);
                    
                    // Reemplazar variables personalizadas
                    if (is_array($variables)) {
                        foreach ($variables as $key => $value) {
                            $contenido_html = str_replace('{{' . $key . '}}', $value, $contenido_html);
                            $asunto_email = str_replace('{{' . $key . '}}', $value, $asunto_email);
                        }
                    }
                    
                    // Variables del sistema
                    $contenido_html = str_replace('{{nombre_institucion}}', getConfig('empresa_nombre', SITE_NAME), $contenido_html);
                    $contenido_html = str_replace('{{logo_url}}', siteUrl('assets/images/design/logo.png'), $contenido_html);
                    
                    // Agregar tracking de links (reemplazar todos los links con links de tracking)
                    if ($envio_id) {
                        // Encontrar todos los links <a href="...">
                        $contenido_html = preg_replace_callback(
                            '/<a\s+([^>]*\s+)?href=["\']([^"\']+)["\']([^>]*)>/i',
                            function($matches) use ($envio_id, $campana_id) {
                                $before = $matches[1] ?? '';
                                $url = $matches[2];
                                $after = $matches[3] ?? '';
                                
                                // No trackear links de tracking ni mailto: ni anchors
                                if (strpos($url, 'track-') !== false || strpos($url, 'mailto:') === 0 || strpos($url, '#') === 0) {
                                    return $matches[0];
                                }
                                
                                // Crear link de tracking
                                $tracking_url = siteUrl('track-click.php?e=' . $envio_id . '&c=' . $campana_id . '&url=' . urlencode($url));
                                return '<a ' . $before . 'href="' . htmlspecialchars($tracking_url) . '"' . $after . '>';
                            },
                            $contenido_html
                        );
                    }
                    
                    // Agregar pixel de tracking al final del HTML (antes de </body> o al final)
                    if ($envio_id) {
                        $tracking_url = siteUrl('track-email.php?e=' . $envio_id . '&c=' . $campana_id . '&a=open');
                        $tracking_pixel = '<img src="' . htmlspecialchars($tracking_url) . '" width="1" height="1" style="display:none;" alt="" />';
                        
                        // Insertar antes de </body> o al final del HTML
                        if (stripos($contenido_html, '</body>') !== false) {
                            $contenido_html = str_replace('</body>', $tracking_pixel . '</body>', $contenido_html);
                        } else {
                            $contenido_html .= $tracking_pixel;
                        }
                    }
                    
                    // Enviar email
                    $result = sendEmail(
                        $destinatario['email'],
                        $asunto_email,
                        $contenido_html,
                        $destinatario['nombre'] ?? ''
                    );
                    
                    // Actualizar registro de envío con resultado
                    if ($tabla_envios_existe && $envio_id) {
                        if ($result['success']) {
                            $enviados++;
                            // Ya está marcado como enviado arriba, no necesita actualización
                        } else {
                            $fallidos++;
                            $sql_update_envio = "UPDATE newsletter_envios SET estado = 'fallido', mensaje_error = ? WHERE id = ?";
                            $stmt_update_envio = $pdo->prepare($sql_update_envio);
                            $stmt_update_envio->execute([$result['message'], $envio_id]);
                        }
                    } else {
                        // Sin tabla de envíos, solo contar
                        if ($result['success']) {
                            $enviados++;
                        } else {
                            $fallidos++;
                        }
                    }
                    
                    // Pequeña pausa para evitar sobrecarga
                    usleep(100000); // 0.1 segundos
                }
                
                // Actualizar campaña
                $sql_update = "UPDATE newsletter_campanas SET enviados = ?, fallidos = ?, estado = 'completada', completada_at = NOW() WHERE id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$enviados, $fallidos, $campana_id]);
                
                $success_message = "Campaña enviada exitosamente. Enviados: $enviados, Fallidos: $fallidos";
            } else {
                // Programar para envío posterior (aquí podrías implementar un cron job)
                $success_message = "Campaña programada correctamente. Total destinatarios: " . count($destinatarios);
            }
            
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'crear', 'newsletter', $campana_id, "Campaña de newsletter creada");
            }
            
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}

// Obtener plantillas activas
try {
    $stmt = $pdo->query("SELECT * FROM newsletter_templates WHERE estado = 'activo' ORDER BY nombre ASC");
    $plantillas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $plantillas = [];
}

// Verificar si las tablas existen
$tabla_campanas_existe = false;
$tabla_envios_existe = false;

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_campanas'");
    $tabla_campanas_existe = $stmt->rowCount() > 0;
} catch (Exception $e) {
    $tabla_campanas_existe = false;
}

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_envios'");
    $tabla_envios_existe = $stmt->rowCount() > 0;
} catch (Exception $e) {
    $tabla_envios_existe = false;
}

// Obtener estadísticas de destinatarios
try {
    $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM newsletter_simple GROUP BY status");
    $stats_destinatarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $stats_destinatarios = [];
    $error_message = "Error al obtener estadísticas: " . $e->getMessage();
}

// Obtener campañas recientes (solo si la tabla existe)
$campanas = [];
if ($tabla_campanas_existe) {
    try {
        $stmt = $pdo->query("SELECT c.*, u.nombre as creador_nombre 
                            FROM newsletter_campanas c 
                            LEFT JOIN admin_usuarios u ON c.creado_por = u.id 
                            ORDER BY c.created_at DESC 
                            LIMIT 10");
        $campanas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $campanas = [];
    }
}

$current_page = 'campanas.php';
$current_dir = 'newsletter';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campañas de Newsletter - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/4u89qw1ptzfqell0ybjhqth1cc16ilb1y0792h3momw4lk8l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
            margin-bottom: 2rem;
        }
        
        .preview-container {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            background: white;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
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
                                <i class="bi bi-send me-2"></i>Campañas de Newsletter
                            </h2>
                            <p class="mb-0 opacity-75">Crea y envía campañas usando plantillas HTML</p>
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
                
                <?php if (!$tabla_campanas_existe || !$tabla_envios_existe): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Las tablas de campañas no existen. 
                    Por favor ejecuta el script SQL: <code>database/fase2/24_create_newsletter_campanas_tables.sql</code>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Estadísticas de Destinatarios -->
                <div class="row mb-4">
                    <?php foreach ($stats_destinatarios as $stat): ?>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo number_format($stat['total']); ?></div>
                            <div class="text-muted"><?php echo ucfirst($stat['status']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="row">
                    <!-- Formulario de Campaña -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-plus-circle me-2"></i>Nueva Campaña
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="" id="campanaForm">
                                    <input type="hidden" name="action" value="enviar_campana">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Plantilla *</label>
                                        <select class="form-select" name="plantilla_id" id="plantilla_id" required onchange="cargarPlantilla()">
                                            <option value="">Selecciona una plantilla</option>
                                            <?php foreach ($plantillas as $plantilla): ?>
                                            <option value="<?php echo $plantilla['id']; ?>" 
                                                    data-asunto="<?php echo esc($plantilla['asunto']); ?>"
                                                    data-contenido="<?php echo esc($plantilla['contenido_html']); ?>"
                                                    data-variables="<?php echo esc($plantilla['variables'] ?? '{}'); ?>">
                                                <?php echo esc($plantilla['nombre']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Asunto del Email</label>
                                        <input type="text" class="form-control" name="asunto_personalizado" id="asunto_email" 
                                               placeholder="Se llenará automáticamente desde la plantilla">
                                        <small class="form-text text-muted">Puedes personalizar el asunto o dejarlo como está en la plantilla</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Destinatarios *</label>
                                        <select class="form-select" name="filtro_estado" required>
                                            <option value="activo">Activos (<?php echo $stats_destinatarios[0]['total'] ?? 0; ?>)</option>
                                            <?php foreach ($stats_destinatarios as $stat): ?>
                                            <?php if ($stat['status'] !== 'activo'): ?>
                                            <option value="<?php echo $stat['status']; ?>">
                                                <?php echo ucfirst($stat['status']); ?> (<?php echo $stat['total']; ?>)
                                            </option>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Variables Personalizadas (JSON)</label>
                                        <textarea class="form-control font-monospace" name="variables_personalizadas" id="variables_personalizadas" rows="6" 
                                                  placeholder='{"mensaje_personalizado": "Bienvenido a nuestro newsletter"}'></textarea>
                                        <small class="form-text text-muted">Define valores para variables de la plantilla que no sean dinámicas</small>
                                    </div>
                                    
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" name="enviar_inmediatamente" id="enviar_inmediatamente" value="1" checked>
                                        <label class="form-check-label" for="enviar_inmediatamente">
                                            Enviar inmediatamente
                                        </label>
                                        <small class="form-text text-muted d-block">Si no está marcado, la campaña se programará para envío posterior</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-send me-2"></i>Crear y Enviar Campaña
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vista Previa -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-eye me-2"></i>Vista Previa
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="preview_container" class="preview-container">
                                    <p class="text-muted text-center py-5">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Selecciona una plantilla para ver la vista previa
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campañas Recientes -->
                <?php if (!empty($campanas)): ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>Campañas Recientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Plantilla</th>
                                        <th>Destinatarios</th>
                                        <th>Enviados</th>
                                        <th>Fallidos</th>
                                        <th>Estado</th>
                                        <th>Creada por</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($campanas as $campana): ?>
                                    <tr>
                                        <td>
                                            <a href="campana-detalle.php?id=<?php echo $campana['id']; ?>" class="text-decoration-none">
                                                <?php echo esc($campana['nombre']); ?>
                                            </a>
                                        </td>
                                        <td>Plantilla #<?php echo $campana['plantilla_id']; ?></td>
                                        <td><?php echo number_format($campana['total_destinatarios']); ?></td>
                                        <td class="text-success"><?php echo number_format($campana['enviados']); ?></td>
                                        <td class="text-danger"><?php echo number_format($campana['fallidos']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $campana['estado'] === 'completada' ? 'success' : ($campana['estado'] === 'en_proceso' ? 'warning' : 'secondary'); ?>">
                                                <?php echo ucfirst($campana['estado']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc($campana['creador_nombre'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($campana['created_at'])); ?></td>
                                        <td>
                                            <a href="campana-detalle.php?id=<?php echo $campana['id']; ?>" class="btn btn-sm btn-primary" title="Ver Detalle">
                                                <i class="bi bi-eye"></i> Ver Métricas
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarPlantilla() {
            const select = document.getElementById('plantilla_id');
            const selectedOption = select.options[select.selectedIndex];
            
            if (!selectedOption.value) {
                document.getElementById('preview_container').innerHTML = '<p class="text-muted text-center py-5"><i class="bi bi-info-circle me-2"></i>Selecciona una plantilla para ver la vista previa</p>';
                return;
            }
            
            const asunto = selectedOption.getAttribute('data-asunto') || '';
            const contenido = selectedOption.getAttribute('data-contenido') || '';
            const variables = selectedOption.getAttribute('data-variables') || '{}';
            
            // Actualizar asunto
            document.getElementById('asunto_email').value = asunto;
            
            // Actualizar preview
            const preview = document.getElementById('preview_container');
            preview.innerHTML = contenido;
            
            // Cargar variables en el textarea
            try {
                const varsObj = JSON.parse(variables);
                const varsFormatted = JSON.stringify(varsObj, null, 4);
                document.getElementById('variables_personalizadas').value = varsFormatted;
            } catch (e) {
                document.getElementById('variables_personalizadas').value = variables;
            }
        }
    </script>
</body>
</html>

