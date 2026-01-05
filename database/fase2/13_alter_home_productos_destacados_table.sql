-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- ALTER TABLE: home_productos_destacados
-- ========================================
-- 
-- Agrega campos adicionales para soportar todos los datos
-- de los productos destacados hardcodeados (badge, subtítulo, descripción, bullets, imagen, CTA)
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- AGREGAR CAMPOS ADICIONALES
-- ========================================

-- Badge (etiqueta del producto destacado, ej: "Más Vendido", "Inmersivo", "Pediátrico", "Adulto")
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `badge_texto` VARCHAR(100) DEFAULT NULL COMMENT 'Texto del badge/etiqueta (ej: Más Vendido, Inmersivo)' AFTER `producto_id`;

-- Subtítulo personalizado para el home
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `subtitulo` VARCHAR(500) DEFAULT NULL COMMENT 'Subtítulo personalizado para el home' AFTER `badge_texto`;

-- Descripción personalizada para el home
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `descripcion` TEXT DEFAULT NULL COMMENT 'Descripción personalizada para el home' AFTER `subtitulo`;

-- Características/Bullets (lista de features en formato texto)
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `caracteristicas` TEXT DEFAULT NULL COMMENT 'Lista de características en formato texto separado por saltos de línea' AFTER `descripcion`;

-- Imagen personalizada para el home (opcional, si no se especifica usa la del producto)
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `imagen_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL de imagen personalizada para el home' AFTER `caracteristicas`;

-- CTA personalizado
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `cta_texto` VARCHAR(100) DEFAULT NULL COMMENT 'Texto del botón CTA' AFTER `imagen_url`;

ALTER TABLE `home_productos_destacados` 
ADD COLUMN `cta_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL del botón CTA' AFTER `cta_texto`;

-- Verificar cambios
DESCRIBE `home_productos_destacados`;

