<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Funciones RBAC
 * ========================================
 * 
 * Funciones para gestión de permisos basada en roles (RBAC)
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}

/**
 * Verificar si un usuario tiene un permiso específico
 * 
 * @param int $usuario_id ID del usuario
 * @param string $modulo Nombre del módulo
 * @param string $accion Acción a verificar
 * @return bool True si tiene permiso, False si no
 */
// Verificar si la función ya existe (puede estar en auth_check.php)
if (!function_exists('hasPermission')) {
    function hasPermission($usuario_id, $modulo, $accion) {
        $pdo = getDB();
        if (!$pdo) {
            return false;
        }
        
        // Obtener rol del usuario
        $sql_usuario = "SELECT rol FROM admin_usuarios WHERE id = ? AND estado = 'activo'";
        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->execute([$usuario_id]);
        $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
        $stmt_usuario->closeCursor(); // Cerrar cursor antes de la siguiente consulta
        
        if (!$usuario) {
            return false;
        }
        
        $rol = $usuario['rol'];
        
        // Si es admin, tiene todos los permisos
        if ($rol === 'admin') {
            return true;
        }
        
        // Verificar permiso específico
        $sql = "
            SELECT COUNT(*) as tiene_permiso
            FROM rol_permisos rp
            INNER JOIN permisos p ON rp.permiso_id = p.id
            WHERE rp.rol = ? AND p.modulo = ? AND p.accion = ?
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rol, $modulo, $accion]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); // Cerrar cursor
        
        return ($result['tiene_permiso'] > 0);
    }
}

/**
 * Verificar permiso del usuario actual (desde sesión)
 * Si no tiene permiso, redirige o muestra error
 * 
 * @param string $modulo Nombre del módulo
 * @param string $accion Acción requerida
 * @param bool $redirect Si true, redirige al dashboard. Si false, muestra error
 * @return bool True si tiene permiso
 */
function checkPermission($modulo, $accion, $redirect = true) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $usuario_id = $_SESSION['admin_user_id'] ?? null;
    
    if (!$usuario_id) {
        if ($redirect) {
            header('Location: login.php');
            exit;
        }
        return false;
    }
    
    // Verificar permiso
    $tiene_permiso = hasPermission($usuario_id, $modulo, $accion);
    
    if (!$tiene_permiso) {
        if ($redirect) {
            // Registrar intento de acceso no autorizado
            logActivity($usuario_id, 'acceso_denegado', $modulo, null, null, [
                'accion_intentada' => $accion,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            header('Location: index.php?error=sin_permiso');
            exit;
        }
        return false;
    }
    
    return true;
}

/**
 * Obtener todos los permisos de un usuario
 * 
 * @param int $usuario_id ID del usuario
 * @return array Array de permisos ['modulo' => ['accion1', 'accion2', ...]]
 */
function getUserPermissions($usuario_id) {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        // Obtener rol del usuario
        $sql_usuario = "SELECT rol FROM admin_usuarios WHERE id = ? AND estado = 'activo'";
        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->execute([$usuario_id]);
        $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
        
        // Cerrar la consulta anterior antes de continuar
        $stmt_usuario->closeCursor();
        
        if (!$usuario) {
            return [];
        }
        
        $rol = $usuario['rol'];
        
        // Si es admin, retornar todos los permisos
        if ($rol === 'admin') {
            $sql = "SELECT modulo, accion FROM permisos ORDER BY modulo, accion";
            $stmt = $pdo->query($sql);
            $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } else {
            // Obtener permisos del rol
            $sql = "
                SELECT p.modulo, p.accion
                FROM rol_permisos rp
                INNER JOIN permisos p ON rp.permiso_id = p.id
                WHERE rp.rol = ?
                ORDER BY p.modulo, p.accion
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$rol]);
            $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        }
    } catch (PDOException $e) {
        error_log("Error en getUserPermissions: " . $e->getMessage());
        return [];
    }
    
    // Organizar por módulo
    $resultado = [];
    foreach ($permisos as $permiso) {
        $modulo = $permiso['modulo'];
        $accion = $permiso['accion'];
        
        if (!isset($resultado[$modulo])) {
            $resultado[$modulo] = [];
        }
        
        $resultado[$modulo][] = $accion;
    }
    
    return $resultado;
}

/**
 * Obtener todos los permisos disponibles en el sistema
 * 
 * @return array Array de permisos organizados por módulo
 */
function getAllPermissions() {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    $sql = "SELECT id, modulo, accion, descripcion FROM permisos ORDER BY modulo, accion";
    $stmt = $pdo->query($sql);
    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar por módulo
    $resultado = [];
    foreach ($permisos as $permiso) {
        $modulo = $permiso['modulo'];
        
        if (!isset($resultado[$modulo])) {
            $resultado[$modulo] = [];
        }
        
        $resultado[$modulo][] = [
            'id' => $permiso['id'],
            'accion' => $permiso['accion'],
            'descripcion' => $permiso['descripcion']
        ];
    }
    
    return $resultado;
}

/**
 * Obtener permisos de un rol específico
 * 
 * @param string $rol Nombre del rol
 * @return array Array de IDs de permisos
 */
function getRolePermissions($rol) {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    $sql = "SELECT permiso_id FROM rol_permisos WHERE rol = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rol]);
    $permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    return $permisos;
}

/**
 * Asignar permisos a un rol
 * 
 * @param string $rol Nombre del rol
 * @param array $permiso_ids Array de IDs de permisos
 * @return bool True si se asignaron correctamente
 */
function assignPermissionsToRole($rol, $permiso_ids) {
    $pdo = getDB();
    if (!$pdo) {
        return false;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Eliminar permisos actuales del rol
        $sql_delete = "DELETE FROM rol_permisos WHERE rol = ?";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->execute([$rol]);
        
        // Insertar nuevos permisos
        if (!empty($permiso_ids)) {
            $sql_insert = "INSERT INTO rol_permisos (rol, permiso_id) VALUES (?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);
            
            foreach ($permiso_ids as $permiso_id) {
                $stmt_insert->execute([$rol, $permiso_id]);
            }
        }
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Verificar si el usuario actual puede realizar una acción
 * (Wrapper para usar desde páginas admin)
 * 
 * @param string $modulo Nombre del módulo
 * @param string $accion Acción requerida
 * @return bool
 */
function can($modulo, $accion) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $usuario_id = $_SESSION['admin_user_id'] ?? null;
    
    if (!$usuario_id) {
        return false;
    }
    
    return hasPermission($usuario_id, $modulo, $accion);
}

