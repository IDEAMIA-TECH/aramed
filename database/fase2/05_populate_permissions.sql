-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- POBLAR PERMISOS RBAC INICIALES
-- ========================================
-- 
-- Este script crea todos los permisos del sistema y los asigna al rol 'admin'
-- 
-- IMPORTANTE: Ejecutar DESPUÉS de crear las tablas permisos y rol_permisos
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- LIMPIAR DATOS EXISTENTES (OPCIONAL)
-- ========================================
-- Descomentar si quieres limpiar y empezar de nuevo:
-- DELETE FROM rol_permisos;
-- DELETE FROM permisos;

-- ========================================
-- INSERTAR PERMISOS POR MÓDULO
-- ========================================

-- Dashboard
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('dashboard', 'ver', 'Ver el dashboard principal'),
('dashboard', 'editar', 'Editar configuración del dashboard')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Home
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('home', 'ver', 'Ver el gestor de home'),
('home', 'crear', 'Crear contenido para el home'),
('home', 'editar', 'Editar contenido del home'),
('home', 'eliminar', 'Eliminar contenido del home')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Catálogo
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('catalogo', 'ver', 'Ver el catálogo de productos'),
('catalogo', 'crear', 'Crear productos, categorías o marcas'),
('catalogo', 'editar', 'Editar productos, categorías o marcas'),
('catalogo', 'eliminar', 'Eliminar productos, categorías o marcas')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Blog
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('blog', 'ver', 'Ver artículos del blog'),
('blog', 'crear', 'Crear artículos del blog'),
('blog', 'editar', 'Editar artículos del blog'),
('blog', 'eliminar', 'Eliminar artículos del blog'),
('blog', 'moderar', 'Moderar comentarios del blog')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Proyectos
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('proyectos', 'ver', 'Ver proyectos'),
('proyectos', 'crear', 'Crear nuevos proyectos'),
('proyectos', 'editar', 'Editar proyectos existentes'),
('proyectos', 'eliminar', 'Eliminar proyectos')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Contacto
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('contacto', 'ver', 'Ver mensajes de contacto'),
('contacto', 'editar', 'Editar estado de mensajes de contacto'),
('contacto', 'asignar', 'Asignar mensajes a responsables'),
('contacto', 'eliminar', 'Eliminar mensajes de contacto')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Cotizaciones
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('cotizaciones', 'ver', 'Ver cotizaciones'),
('cotizaciones', 'editar', 'Editar cotizaciones'),
('cotizaciones', 'asignar', 'Asignar cotizaciones a ejecutivos'),
('cotizaciones', 'exportar', 'Exportar cotizaciones a CSV/Excel')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Newsletter
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('newsletter', 'ver', 'Ver suscriptores del newsletter'),
('newsletter', 'importar', 'Importar suscriptores desde CSV'),
('newsletter', 'exportar', 'Exportar suscriptores a CSV'),
('newsletter', 'editar', 'Editar suscriptores del newsletter')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Analytics
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('analytics', 'ver', 'Ver analytics y métricas'),
('analytics', 'editar', 'Editar configuración de analytics')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SEO
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('seo', 'ver', 'Ver configuración SEO'),
('seo', 'editar', 'Editar configuración SEO y metadatos')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Usuarios
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('usuarios', 'ver', 'Ver lista de usuarios'),
('usuarios', 'crear', 'Crear nuevos usuarios'),
('usuarios', 'editar', 'Editar usuarios existentes'),
('usuarios', 'eliminar', 'Eliminar usuarios')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Configuración
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('configuracion', 'ver', 'Ver configuración del sitio'),
('configuracion', 'editar', 'Editar configuración del sitio')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- ========================================
-- ASIGNAR TODOS LOS PERMISOS AL ROL 'admin'
-- ========================================
-- El rol 'admin' debe tener todos los permisos por defecto

INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'admin', p.id
FROM `permisos` p
WHERE NOT EXISTS (
    SELECT 1 FROM `rol_permisos` rp 
    WHERE rp.rol = 'admin' AND rp.permiso_id = p.id
);

-- ========================================
-- VERIFICACIÓN
-- ========================================
-- Ejecutar estas consultas para verificar:

-- Ver todos los permisos creados:
-- SELECT * FROM permisos ORDER BY modulo, accion;

-- Ver permisos asignados al rol admin:
-- SELECT p.modulo, p.accion, p.descripcion 
-- FROM rol_permisos rp
-- INNER JOIN permisos p ON rp.permiso_id = p.id
-- WHERE rp.rol = 'admin'
-- ORDER BY p.modulo, p.accion;

-- Contar permisos:
-- SELECT COUNT(*) as total_permisos FROM permisos;
-- SELECT COUNT(*) as permisos_admin FROM rol_permisos WHERE rol = 'admin';
