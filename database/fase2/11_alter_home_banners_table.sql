-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- ALTER TABLE: home_banners
-- ========================================
-- 
-- Agrega campos adicionales para soportar todos los datos
-- de los banners hardcodeados (badge, descripción, características, segundo CTA)
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

-- Badge (etiqueta/categoría del banner)
-- Nota: Si la columna ya existe, se ignorará el error "Duplicate column name"
ALTER TABLE `home_banners` 
ADD COLUMN `badge_texto` VARCHAR(100) DEFAULT NULL COMMENT 'Texto del badge/etiqueta (ej: Simulador Obstétrico)' AFTER `subtitulo`;

-- Descripción (diferente del subtítulo, más detallada)
ALTER TABLE `home_banners` 
ADD COLUMN `descripcion` TEXT DEFAULT NULL COMMENT 'Descripción detallada del banner' AFTER `badge_texto`;

-- Características (lista de features en formato JSON o texto)
ALTER TABLE `home_banners` 
ADD COLUMN `caracteristicas` TEXT DEFAULT NULL COMMENT 'Lista de características en formato JSON o texto separado por saltos de línea' AFTER `descripcion`;

-- Segundo botón CTA
ALTER TABLE `home_banners` 
ADD COLUMN `cta2_texto` VARCHAR(100) DEFAULT NULL COMMENT 'Texto del segundo botón CTA' AFTER `cta_url`;

ALTER TABLE `home_banners` 
ADD COLUMN `cta2_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL del segundo botón CTA' AFTER `cta2_texto`;

-- Verificar cambios
DESCRIBE `home_banners`;

