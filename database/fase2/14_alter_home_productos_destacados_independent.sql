-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- ALTER TABLE: home_productos_destacados (INDEPENDIENTE)
-- ========================================
-- 
-- Modifica la tabla para que sea completamente independiente del catálogo.
-- Los productos destacados del home NO están relacionados con catalogo_productos.
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- PASO 1: Eliminar FOREIGN KEY y hacer producto_id opcional
-- ========================================

-- Eliminar la foreign key constraint
ALTER TABLE `home_productos_destacados` 
DROP FOREIGN KEY IF EXISTS `home_productos_destacados_ibfk_1`;

-- Hacer producto_id opcional (NULL permitido)
ALTER TABLE `home_productos_destacados` 
MODIFY COLUMN `producto_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID del producto del catálogo (opcional, puede ser NULL si es independiente)';

-- Eliminar el UNIQUE KEY que depende de producto_id
ALTER TABLE `home_productos_destacados` 
DROP INDEX IF EXISTS `producto_manual`;

-- ========================================
-- PASO 2: Agregar campos para contenido independiente
-- ========================================

-- Título del producto destacado
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `titulo` VARCHAR(255) DEFAULT NULL COMMENT 'Título del producto destacado' AFTER `producto_id`;

-- Nombre de la marca/proveedor
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `marca_nombre` VARCHAR(100) DEFAULT NULL COMMENT 'Nombre de la marca/proveedor' AFTER `titulo`;

-- Logo del proveedor (URL de la imagen)
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `marca_logo_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL del logo del proveedor/marca' AFTER `marca_nombre`;

-- Categoría del producto
ALTER TABLE `home_productos_destacados` 
ADD COLUMN `categoria_nombre` VARCHAR(100) DEFAULT NULL COMMENT 'Nombre de la categoría' AFTER `marca_logo_url`;

-- ========================================
-- PASO 3: Verificar cambios
-- ========================================

DESCRIBE `home_productos_destacados`;

-- ========================================
-- NOTAS
-- ========================================
-- 
-- Después de ejecutar este script:
-- 1. producto_id puede ser NULL (no es obligatorio)
-- 2. Los productos destacados pueden tener su propio título, marca, logo, etc.
-- 3. No hay relación con catalogo_productos
-- 4. Ejecutar 12_migrate_home_productos_destacados.sql para migrar los datos

