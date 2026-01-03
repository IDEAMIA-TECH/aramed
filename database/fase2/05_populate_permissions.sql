-- ========================================
-- SCRIPT PARA POBLAR PERMISOS INICIALES
-- Fase 2 - Usuarios & Roles
-- ========================================
-- Fecha: Enero 2025
-- Descripción: Inserta todos los permisos del sistema y los asigna a roles

-- ========================================
-- 1. INSERTAR PERMISOS POR MÓDULO
-- ========================================

-- Dashboard
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('dashboard', 'ver', 'Ver el dashboard principal'),
('dashboard', 'editar', 'Editar configuración del dashboard')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Home / Gestor de Inicio
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('home', 'ver', 'Ver contenido del gestor de Home'),
('home', 'crear', 'Crear banners, servicios, etc.'),
('home', 'editar', 'Editar contenido del Home'),
('home', 'eliminar', 'Eliminar contenido del Home')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Catálogo
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('catalogo', 'ver', 'Ver productos, categorías y marcas'),
('catalogo', 'crear', 'Crear productos, categorías y marcas'),
('catalogo', 'editar', 'Editar productos, categorías y marcas'),
('catalogo', 'eliminar', 'Eliminar productos, categorías y marcas'),
('catalogo', 'exportar', 'Exportar catálogo a CSV/Excel')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Proyectos
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('proyectos', 'ver', 'Ver proyectos'),
('proyectos', 'crear', 'Crear proyectos'),
('proyectos', 'editar', 'Editar proyectos'),
('proyectos', 'eliminar', 'Eliminar proyectos')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Blog
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('blog', 'ver', 'Ver artículos del blog'),
('blog', 'crear', 'Crear artículos del blog'),
('blog', 'editar', 'Editar artículos del blog'),
('blog', 'eliminar', 'Eliminar artículos del blog'),
('blog', 'moderar', 'Moderar comentarios del blog')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Cotizaciones
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('cotizaciones', 'ver', 'Ver cotizaciones'),
('cotizaciones', 'editar', 'Editar cotizaciones'),
('cotizaciones', 'asignar', 'Asignar cotizaciones a ejecutivos'),
('cotizaciones', 'exportar', 'Exportar cotizaciones a CSV/Excel')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Contacto
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('contacto', 'ver', 'Ver mensajes de contacto'),
('contacto', 'editar', 'Editar estado de mensajes'),
('contacto', 'asignar', 'Asignar mensajes a responsables')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Newsletter
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('newsletter', 'ver', 'Ver suscriptores'),
('newsletter', 'importar', 'Importar suscriptores desde CSV'),
('newsletter', 'exportar', 'Exportar suscriptores a CSV')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- SEO
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('seo', 'ver', 'Ver configuración SEO'),
('seo', 'editar', 'Editar configuración SEO')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Usuarios
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('usuarios', 'ver', 'Ver usuarios del sistema'),
('usuarios', 'crear', 'Crear nuevos usuarios'),
('usuarios', 'editar', 'Editar usuarios'),
('usuarios', 'eliminar', 'Eliminar usuarios')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Configuración
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('configuracion', 'ver', 'Ver configuración del sistema'),
('configuracion', 'editar', 'Editar configuración del sistema')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- ========================================
-- 2. ASIGNAR PERMISOS A ROLES
-- ========================================

-- ADMIN: Todos los permisos
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'admin', `id` FROM `permisos`
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- MARKETING: Banners, Home, marcas, servicios, blog, SEO, newsletter
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'marketing', `id` FROM `permisos`
WHERE `modulo` IN ('home', 'blog', 'newsletter', 'seo')
   OR (`modulo` = 'catalogo' AND `accion` IN ('ver', 'editar'))
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- VENTAS: Catálogo (parcial), cotizaciones, clientes
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'ventas', `id` FROM `permisos`
WHERE `modulo` IN ('catalogo', 'cotizaciones')
   OR (`modulo` = 'contacto' AND `accion` = 'ver')
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- SOPORTE: Cotizaciones y contacto (bandejas, seguimiento)
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'soporte', `id` FROM `permisos`
WHERE `modulo` IN ('cotizaciones', 'contacto')
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- ANALISTA: Solo lectura (ver) en reportes y dashboards
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'analista', `id` FROM `permisos`
WHERE `accion` = 'ver'
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- EDITOR: Similar a marketing pero sin configuración
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'editor', `id` FROM `permisos`
WHERE `modulo` IN ('home', 'blog', 'catalogo', 'proyectos')
   AND `modulo` != 'configuracion'
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- ========================================
-- VERIFICACIÓN
-- ========================================
SELECT 
    COUNT(*) as total_permisos,
    (SELECT COUNT(*) FROM `rol_permisos` WHERE `rol` = 'admin') as permisos_admin,
    (SELECT COUNT(*) FROM `rol_permisos` WHERE `rol` = 'marketing') as permisos_marketing,
    (SELECT COUNT(*) FROM `rol_permisos` WHERE `rol` = 'ventas') as permisos_ventas,
    (SELECT COUNT(*) FROM `rol_permisos` WHERE `rol` = 'soporte') as permisos_soporte,
    (SELECT COUNT(*) FROM `rol_permisos` WHERE `rol` = 'analista') as permisos_analista,
    (SELECT COUNT(*) FROM `rol_permisos` WHERE `rol` = 'editor') as permisos_editor
FROM `permisos`;

