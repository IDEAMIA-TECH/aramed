<?php
// usuarios.php - Administración de Usuarios
define('ARAMED_SITE', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('usuarios', 'ver');
}

$current_page = 'usuarios.php';
$current_dir = 'admin';

// Procesar acciones
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

$success_message = '';
$error_message = '';

// Acciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDB();
        
        if ($action === 'create') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $rol = $_POST['rol'];
            $estado = isset($_POST['activo']) ? 'activo' : 'inactivo';
            
            if (empty($nombre) || empty($email) || empty($password)) {
                throw new Exception('Todos los campos son obligatorios');
            }
            
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM admin_usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está registrado');
            }
            
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO admin_usuarios (nombre, email, password_hash, rol, estado, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$nombre, $email, $password_hash, $rol, $estado]);
            
            $success_message = 'Usuario creado exitosamente';
            $action = 'list';
            
        } elseif ($action === 'update') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $rol = $_POST['rol'];
            $estado = isset($_POST['activo']) ? 'activo' : 'inactivo';
            
            if (empty($nombre) || empty($email)) {
                throw new Exception('Nombre y email son obligatorios');
            }
            
            // Verificar si el email ya existe en otro usuario
            $stmt = $pdo->prepare("SELECT id FROM admin_usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está registrado por otro usuario');
            }
            
            $stmt = $pdo->prepare("UPDATE admin_usuarios SET nombre = ?, email = ?, rol = ?, estado = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$nombre, $email, $rol, $estado, $id]);
            
            $success_message = 'Usuario actualizado exitosamente';
            $action = 'list';
            
        } elseif ($action === 'delete') {
            // No permitir eliminar el usuario actual
            if ($id == $_SESSION['admin_user_id']) {
                throw new Exception('No puedes eliminar tu propio usuario');
            }
            
            $stmt = $pdo->prepare("DELETE FROM admin_usuarios WHERE id = ?");
            $stmt->execute([$id]);
            
            $success_message = 'Usuario eliminado exitosamente';
            $action = 'list';
            
        } elseif ($action === 'update_permissions') {
            // Verificar permisos para gestionar permisos
            if (function_exists('checkPermission')) {
                checkPermission('usuarios', 'editar');
            }
            
            $permisos_seleccionados = isset($_POST['permisos']) ? $_POST['permisos'] : [];
            
            // Eliminar permisos actuales del usuario
            $stmt = $pdo->prepare("DELETE FROM usuario_permisos WHERE usuario_id = ?");
            $stmt->execute([$id]);
            
            // Insertar nuevos permisos
            if (!empty($permisos_seleccionados)) {
                $stmt = $pdo->prepare("INSERT INTO usuario_permisos (usuario_id, permiso_id) VALUES (?, ?)");
                foreach ($permisos_seleccionados as $permiso_id) {
                    $stmt->execute([$id, (int)$permiso_id]);
                }
            }
            
            // Registrar actividad
            if (function_exists('logActivity')) {
                logActivity($_SESSION['admin_user_id'], 'editar', 'usuarios', $id, 'permisos', [
                    'permisos_asignados' => count($permisos_seleccionados)
                ]);
            }
            
            $success_message = 'Permisos actualizados exitosamente';
            $action = 'permisos';
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener datos para formularios
$usuario = null;
$permisos_usuario = [];
$permisos_disponibles = [];

if (($action === 'edit' || $action === 'permisos') && $id) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admin_usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            $error_message = 'Usuario no encontrado';
            $action = 'list';
        } else {
            // Obtener permisos del usuario
            $stmt = $pdo->prepare("SELECT permiso_id FROM usuario_permisos WHERE usuario_id = ?");
            $stmt->execute([$id]);
            $permisos_usuario = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Obtener todos los permisos disponibles
            $stmt = $pdo->query("
                SELECT p.*, 
                       rp.rol_id,
                       GROUP_CONCAT(DISTINCT r.nombre) as roles_con_permiso
                FROM permisos p
                LEFT JOIN rol_permisos rp ON p.id = rp.permiso_id
                LEFT JOIN roles r ON rp.rol_id = r.id
                GROUP BY p.id
                ORDER BY p.modulo, p.nombre
            ");
            $permisos_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $action = 'list';
    }
}

// Obtener lista de usuarios
$usuarios = [];
if ($action === 'list') {
    try {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM admin_usuarios ORDER BY created_at DESC");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Estadísticas
$stats = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_usuarios");
    $stats['total'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as activos FROM admin_usuarios WHERE estado = 'activo'");
    $stats['activos'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as inactivos FROM admin_usuarios WHERE estado = 'inactivo'");
    $stats['inactivos'] = $stmt->fetchColumn();
} catch (Exception $e) {
    $stats = ['total' => 0, 'activos' => 0, 'inactivos' => 0];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios - Aramed Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow);
        }

        .admin-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .admin-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .user-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--success-color);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .user-card.inactive {
            border-left-color: var(--danger-color);
            opacity: 0.7;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .user-info h5 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .user-info p {
            margin: 0.25rem 0 0 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .user-role {
            background: var(--light-color);
            color: var(--dark-color);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .user-role.admin {
            background: var(--danger-color);
            color: white;
        }

        .user-role.editor {
            background: var(--warning-color);
            color: white;
        }

        .form-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge.bg-success {
            background: var(--success-color) !important;
        }

        .badge.bg-danger {
            background: var(--danger-color) !important;
        }

        .badge.bg-primary {
            background: var(--primary-color) !important;
        }

        .badge.bg-warning {
            background: var(--warning-color) !important;
        }

        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
           
                <?php include 'includes/admin_menu.php'; ?>
           
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9">
                <!-- Header -->
                <div class="admin-header">
                    <div class="container-fluid">
                        <h1><i class="bi bi-people-fill me-3"></i>Administración de Usuarios</h1>
                        <p>Gestiona los usuarios del sistema administrativo</p>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo esc($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo esc($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo $stats['total']; ?></div>
                            <div class="stats-label">Total Usuarios</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number text-success"><?php echo $stats['activos']; ?></div>
                            <div class="stats-label">Usuarios Activos</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number text-danger"><?php echo $stats['inactivos']; ?></div>
                            <div class="stats-label">Usuarios Inactivos</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="action-buttons">
                        <a href="?action=create" class="btn btn-primary btn-action">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Usuario
                        </a>
                        <a href="?action=list" class="btn btn-secondary btn-action">
                            <i class="bi bi-list me-2"></i>Ver Todos
                        </a>
                    </div>
                </div>

                <!-- Content -->
                <?php if ($action === 'permisos' && $usuario): ?>
                    
                    <div class="form-card">
                        <h3 class="mb-4">
                            <i class="bi bi-shield-check me-2"></i>
                            Gestionar Permisos: <?php echo esc($usuario['nombre']); ?>
                        </h3>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Los permisos asignados directamente al usuario tienen prioridad sobre los permisos del rol.
                        </div>
                        
                        <form method="POST" action="?action=update_permissions&id=<?php echo $id; ?>">
                            <?php 
                            // Agrupar permisos por módulo
                            $permisos_por_modulo = [];
                            foreach ($permisos_disponibles as $permiso) {
                                $modulo = $permiso['modulo'] ?? 'general';
                                if (!isset($permisos_por_modulo[$modulo])) {
                                    $permisos_por_modulo[$modulo] = [];
                                }
                                $permisos_por_modulo[$modulo][] = $permiso;
                            }
                            
                            foreach ($permisos_por_modulo as $modulo => $permisos_modulo): 
                            ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-folder me-2"></i>
                                        <?php echo ucfirst(str_replace('_', ' ', $modulo)); ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($permisos_modulo as $permiso): ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permisos[]" 
                                                       value="<?php echo $permiso['id']; ?>"
                                                       id="permiso_<?php echo $permiso['id']; ?>"
                                                       <?php echo in_array($permiso['id'], $permisos_usuario) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="permiso_<?php echo $permiso['id']; ?>">
                                                    <strong><?php echo esc($permiso['nombre']); ?></strong>
                                                    <?php if ($permiso['descripcion']): ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo esc($permiso['descripcion']); ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($permiso['roles_con_permiso']): ?>
                                                    <br>
                                                    <small class="text-info">
                                                        <i class="bi bi-tag me-1"></i>
                                                        Roles: <?php echo esc($permiso['roles_con_permiso']); ?>
                                                    </small>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Permisos
                                </button>
                                <a href="?action=list" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Volver a Lista
                                </a>
                            </div>
                        </form>
                    </div>
                    
                <?php elseif ($action === 'create' || $action === 'edit'): ?>
                    <!-- Form -->
                    <div class="form-card">
                        <h3 class="mb-4">
                            <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil-square'; ?> me-2"></i>
                            <?php echo $action === 'create' ? 'Crear Nuevo Usuario' : 'Editar Usuario'; ?>
                        </h3>
                        
                        <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nombre Completo *</label>
                                        <input type="text" class="form-control" name="nombre" 
                                               value="<?php echo $usuario ? esc($usuario['nombre']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo $usuario ? esc($usuario['email']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($action === 'create'): ?>
                            <div class="form-group">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Rol</label>
                                        <select class="form-control" name="rol" required>
                                            <option value="admin" <?php echo ($usuario && $usuario['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                            <option value="editor" <?php echo ($usuario && $usuario['rol'] === 'editor') ? 'selected' : ''; ?>>Editor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" class="form-check-input" name="activo" id="activo" 
                                                   <?php echo ($usuario && $usuario['estado'] === 'activo') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="activo">
                                                Usuario Activo
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <?php echo $action === 'create' ? 'Crear Usuario' : 'Actualizar Usuario'; ?>
                                </button>
                                <a href="?action=list" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                    
                <?php else: ?>
                    <!-- List -->
                    <div class="form-card">
                        <h3 class="mb-4">
                            <i class="bi bi-list me-2"></i>Lista de Usuarios
                        </h3>
                        
                        <?php if (empty($usuarios)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <h4 class="text-muted mt-3">No hay usuarios registrados</h4>
                                <p class="text-muted">Comienza creando el primer usuario del sistema</p>
                                <a href="?action=create" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Crear Primer Usuario
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th>Último Acceso</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <?php echo strtoupper(substr($user['nombre'], 0, 2)); ?>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo esc($user['nombre']); ?></strong>
                                                        <br>
                                                        <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo esc($user['email']); ?></td>
                                            <td>
                                                <span class="user-role <?php echo $user['rol']; ?>">
                                                    <?php echo ucfirst($user['rol']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $user['estado'] === 'activo' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ucfirst($user['estado']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $user['ultimo_login'] ? date('d/m/Y H:i', strtotime($user['ultimo_login'])) : 'Nunca'; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="?action=edit&id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if (function_exists('checkPermission') && can('usuarios', 'editar')): ?>
                                                    <a href="?action=permisos&id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-sm btn-outline-info" title="Gestionar Permisos">
                                                        <i class="bi bi-shield-check"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if ($user['id'] != ($_SESSION['admin_user_id'] ?? 0)): ?>
                                                    <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                       onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
