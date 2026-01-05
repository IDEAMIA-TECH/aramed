-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- CREAR TABLA: home_aliados
-- ========================================
-- 
-- Este script crea la tabla para gestionar los aliados estratégicos
-- (Partners Globales) mostrados en el home
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- TABLA: home_aliados
-- Aliados estratégicos / Partners Globales
-- ========================================
CREATE TABLE IF NOT EXISTS `home_aliados` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre del aliado/partner',
  `logo_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL del logo (ruta relativa a assets/images/)',
  `descripcion` TEXT DEFAULT NULL COMMENT 'Descripción del aliado (para sección detallada)',
  `url_website` VARCHAR(500) DEFAULT NULL COMMENT 'URL del sitio web del aliado (opcional)',
  `mostrar_en_carrusel` TINYINT(1) DEFAULT 1 COMMENT 'Mostrar en carrusel simple de logos',
  `mostrar_en_detalle` TINYINT(1) DEFAULT 1 COMMENT 'Mostrar en carrusel detallado con descripción',
  `orden` INT(11) DEFAULT 0 COMMENT 'Orden de visualización',
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_orden` (`orden`),
  INDEX `idx_carrusel` (`mostrar_en_carrusel`, `estado`),
  INDEX `idx_detalle` (`mostrar_en_detalle`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aliados estratégicos / Partners Globales';

