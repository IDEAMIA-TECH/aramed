-- ========================================
-- ARAMED Y LABORATORIOS - Blog Programación
-- ========================================
-- 
-- Agregar campo para programación de publicación de artículos
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- Agregar campo fecha_programada si no existe
ALTER TABLE `blog_articulos` 
ADD COLUMN IF NOT EXISTS `fecha_programada` DATETIME NULL COMMENT 'Fecha y hora programada para publicación automática' 
AFTER `fecha_publicacion`;

-- Agregar índice para búsquedas eficientes
ALTER TABLE `blog_articulos` 
ADD INDEX IF NOT EXISTS `idx_fecha_programada` (`fecha_programada`);

-- IMPORTANTE: Si el estado 'programado' no existe en el ENUM, ejecutar manualmente:
-- ALTER TABLE `blog_articulos` MODIFY COLUMN `estado` ENUM('borrador', 'programado', 'publicado', 'archivado') DEFAULT 'borrador';

