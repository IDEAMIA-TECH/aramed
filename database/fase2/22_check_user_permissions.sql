-- ========================================
-- ARAMED Y LABORATORIOS - VERIFICAR PERMISOS DE USUARIO
-- ========================================
-- 
-- Script para verificar los permisos de un usuario específico
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- VERIFICAR PERMISOS DEL USUARIO ID: 3
-- ========================================

-- Información del usuario
SELECT 
    id,
    username,
    email,
    nombre,
    rol,
    estado
FROM admin_usuarios
WHERE id = 3;

-- Permisos asignados al rol del usuario
SELECT 
    p.id,
    p.modulo,
    p.accion,
    p.descripcion,
    rp.rol
FROM permisos p
INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
INNER JOIN admin_usuarios u ON rp.rol = u.rol
WHERE u.id = 3
ORDER BY p.modulo, p.accion;

-- Resumen de permisos por módulo
SELECT 
    p.modulo,
    GROUP_CONCAT(DISTINCT p.accion ORDER BY p.accion SEPARATOR ', ') as acciones
FROM permisos p
INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
INNER JOIN admin_usuarios u ON rp.rol = u.rol
WHERE u.id = 3
GROUP BY p.modulo
ORDER BY p.modulo;

-- Verificar si el usuario tiene permisos específicos
SELECT 
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM rol_permisos rp
            INNER JOIN permisos p ON rp.permiso_id = p.id
            INNER JOIN admin_usuarios u ON rp.rol = u.rol
            WHERE u.id = 3 AND p.modulo = 'catalogo' AND p.accion = 'crear'
        ) THEN 'SÍ' 
        ELSE 'NO' 
    END as tiene_crear_catalogo,
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM rol_permisos rp
            INNER JOIN permisos p ON rp.permiso_id = p.id
            INNER JOIN admin_usuarios u ON rp.rol = u.rol
            WHERE u.id = 3 AND p.modulo = 'catalogo' AND p.accion = 'editar'
        ) THEN 'SÍ' 
        ELSE 'NO' 
    END as tiene_editar_catalogo,
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM rol_permisos rp
            INNER JOIN permisos p ON rp.permiso_id = p.id
            INNER JOIN admin_usuarios u ON rp.rol = u.rol
            WHERE u.id = 3 AND p.modulo = 'catalogo' AND p.accion = 'eliminar'
        ) THEN 'SÍ' 
        ELSE 'NO' 
    END as tiene_eliminar_catalogo,
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM rol_permisos rp
            INNER JOIN permisos p ON rp.permiso_id = p.id
            INNER JOIN admin_usuarios u ON rp.rol = u.rol
            WHERE u.id = 3 AND p.modulo = 'catalogo' AND p.accion = 'ver'
        ) THEN 'SÍ' 
        ELSE 'NO' 
    END as tiene_ver_catalogo;

