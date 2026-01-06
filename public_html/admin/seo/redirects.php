<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DE REDIRECCIONES
 * ========================================
 * 
 * CRUD de redirecciones 301/302
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

// Verificar que el usuario sea admin (SEO es solo para admin)
$user_role = $_SESSION['admin_rol'] ?? 'editor';
if ($user_role !== 'admin') {
    header('Location: ../sin-permiso.php?modulo=seo&accion=editar');
    exit;
}

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('seo', 'editar');
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
    
    if ($action === 'create' || $action === 'update') {
        try {
            $url_antigua = trim($_POST['url_antigua'] ?? '');
            $url_nueva = trim($_POST['url_nueva'] ?? '');
            $tipo = $_POST['tipo'] ?? '301';
            $estado = $_POST['estado'] ?? 'activo';
            $id = $_POST['id'] ?? null;
            
            // Validar URLs
            if (empty($url_antigua) || empty($url_nueva)) {
                throw new Exception('Las URLs son obligatorias');
            }
            
            // Normalizar URLs (agregar / al inicio si no tiene)
            if (strpos($url_antigua, '/') !== 0 && strpos($url_antigua, 'http') !== 0) {
                $url_antigua = '/' . $url_antigua;
            }
            if (strpos($url_nueva, '/') !== 0 && strpos($url_nueva, 'http') !== 0) {
                $url_nueva = '/' . $url_nueva;
            }
            
            if ($action === 'create') {
                $sql = "INSERT INTO redirects (url_antigua, url_nueva, tipo, estado) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$url_antigua, $url_nueva, $tipo, $estado]);
                $success_message = 'Redirección creada exitosamente';
            } else {
                $sql = "UPDATE redirects SET url_antigua = ?, url_nueva = ?, tipo = ?, estado = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$url_antigua, $url_nueva, $tipo, $estado, $id]);
                $success_message = 'Redirección actualizada exitosamente';
            }
            
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], $action === 'create' ? 'crear' : 'editar', 'seo', $id ?? null, "Redirección: {$url_antigua} -> {$url_nueva}");
            }
            
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    } elseif ($action === 'delete') {
        try {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM redirects WHERE id = ?");
            $stmt->execute([$id]);
            $success_message = 'Redirección eliminada exitosamente';
            
            if (function_exists('logActivity')) {
                logActivity($current_user['id'], 'eliminar', 'seo', $id, 'Redirección eliminada');
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    } elseif ($action === 'toggle_status') {
        try {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE redirects SET estado = IF(estado = 'activo', 'inactivo', 'activo') WHERE id = ?");
            $stmt->execute([$id]);
            $success_message = 'Estado actualizado';
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}

// Obtener redirección para editar
$editing = null;
if (isset($_GET['edit'])) {
    try {
        $id = (int)$_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM redirects WHERE id = ?");
        $stmt->execute([$id]);
        $editing = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener todas las redirecciones
try {
    $stmt = $pdo->query("SELECT * FROM redirects ORDER BY created_at DESC");
    $redirects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $redirects = [];
}

$current_page = 'redirects.php';
$current_dir = 'seo';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecciones - SEO Admin <?php echo SITE_NAME; ?></title>
    
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
                                <i class="bi bi-arrow-left-right me-2"></i>Gestión de Redirecciones
                            </h2>
                            <p class="mb-0 opacity-75">Gestiona redirecciones 301/302 para URLs antiguas</p>
                        </div>
                        <a href="index.php" class="btn btn-light">
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
                
                <div class="row">
                    <!-- Formulario -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    <?php echo $editing ? 'Editar' : 'Nueva'; ?> Redirección
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">
                                    <?php if ($editing): ?>
                                    <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">URL Antigua *</label>
                                        <input type="text" class="form-control" name="url_antigua" 
                                               value="<?php echo esc($editing['url_antigua'] ?? ''); ?>" required>
                                        <small class="form-text text-muted">Ej: /pagina-antigua.html</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">URL Nueva *</label>
                                        <input type="text" class="form-control" name="url_nueva" 
                                               value="<?php echo esc($editing['url_nueva'] ?? ''); ?>" required>
                                        <small class="form-text text-muted">Ej: /nueva-pagina o URL completa</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Redirección</label>
                                        <select class="form-select" name="tipo">
                                            <option value="301" <?php echo ($editing['tipo'] ?? '301') === '301' ? 'selected' : ''; ?>>301 - Permanente</option>
                                            <option value="302" <?php echo ($editing['tipo'] ?? '') === '302' ? 'selected' : ''; ?>>302 - Temporal</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" name="estado">
                                            <option value="activo" <?php echo ($editing['estado'] ?? 'activo') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                            <option value="inactivo" <?php echo ($editing['estado'] ?? '') === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $editing ? 'Actualizar' : 'Crear'; ?> Redirección
                                    </button>
                                    
                                    <?php if ($editing): ?>
                                    <a href="redirects.php" class="btn btn-secondary w-100 mt-2">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>Lista de Redirecciones (<?php echo count($redirects); ?>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($redirects)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                                    <p class="text-muted">No hay redirecciones configuradas</p>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>URL Antigua</th>
                                                <th>URL Nueva</th>
                                                <th>Tipo</th>
                                                <th>Estado</th>
                                                <th>Hits</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($redirects as $redirect): ?>
                                            <tr>
                                                <td><code><?php echo esc($redirect['url_antigua']); ?></code></td>
                                                <td><code><?php echo esc($redirect['url_nueva']); ?></code></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $redirect['tipo'] === '301' ? 'primary' : 'warning'; ?>">
                                                        <?php echo $redirect['tipo']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $redirect['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($redirect['estado']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo number_format($redirect['hits']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?edit=<?php echo $redirect['id']; ?>" class="btn btn-outline-primary" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Cambiar estado?');">
                                                            <input type="hidden" name="action" value="toggle_status">
                                                            <input type="hidden" name="id" value="<?php echo $redirect['id']; ?>">
                                                            <button type="submit" class="btn btn-outline-<?php echo $redirect['estado'] === 'activo' ? 'warning' : 'success'; ?>" title="Cambiar estado">
                                                                <i class="bi bi-<?php echo $redirect['estado'] === 'activo' ? 'pause' : 'play'; ?>"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta redirección?');">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?php echo $redirect['id']; ?>">
                                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
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

