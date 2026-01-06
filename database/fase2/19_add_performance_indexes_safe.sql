-- ========================================
-- ARAMED Y LABORATORIOS - Índices de Performance (Versión Segura)
-- ========================================
-- 
-- Script para agregar índices que mejoran el rendimiento de consultas comunes
-- Esta versión verifica si los índices existen antes de crearlos
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- FUNCIÓN HELPER PARA VERIFICAR ÍNDICES
-- ========================================

-- Esta función verifica si un índice existe antes de crearlo
-- Uso: CALL add_index_if_not_exists('tabla', 'nombre_indice', 'columnas');

DELIMITER $$

DROP PROCEDURE IF EXISTS add_index_if_not_exists$$

CREATE PROCEDURE add_index_if_not_exists(
    IN table_name VARCHAR(64),
    IN index_name VARCHAR(64),
    IN index_columns VARCHAR(255)
)
BEGIN
    DECLARE index_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO index_exists
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
    AND table_name = table_name
    AND index_name = index_name;
    
    IF index_exists = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', table_name, '` ADD INDEX `', index_name, '` (', index_columns, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- ========================================
-- ÍNDICES PARA MEJORAR PERFORMANCE
-- ========================================

-- Catálogo de Productos
CALL add_index_if_not_exists('catalogo_productos', 'idx_estado_categoria', '`estado`, `categoria_id`');
CALL add_index_if_not_exists('catalogo_productos', 'idx_marca_estado', '`marca_id`, `estado`');
CALL add_index_if_not_exists('catalogo_productos', 'idx_destacado_estado', '`destacado`, `estado`');
CALL add_index_if_not_exists('catalogo_productos', 'idx_created_at', '`created_at`');

-- Blog
CALL add_index_if_not_exists('blog_articulos', 'idx_estado_fecha', '`estado`, `fecha_publicacion`');
CALL add_index_if_not_exists('blog_articulos', 'idx_programado_fecha', '`estado`, `fecha_programada`');
CALL add_index_if_not_exists('blog_articulos', 'idx_categoria_estado', '`categoria_id`, `estado`');

-- Contacto
CALL add_index_if_not_exists('contact_messages', 'idx_estado_fecha', '`estado`, `created_at`');
CALL add_index_if_not_exists('contact_messages', 'idx_leido_estado', '`leido`, `estado`');

-- Cotizaciones
CALL add_index_if_not_exists('cotizaciones', 'idx_estado_fecha', '`estado`, `created_at`');
CALL add_index_if_not_exists('cotizaciones', 'idx_asignado_a', '`asignado_a`');
CALL add_index_if_not_exists('cotizaciones', 'idx_folio', '`folio`');

-- Newsletter
CALL add_index_if_not_exists('newsletter_subscriptions', 'idx_status_fecha', '`status`, `created_at`');
CALL add_index_if_not_exists('newsletter_subscriptions', 'idx_email', '`email`');

-- Proyectos
CALL add_index_if_not_exists('proyectos', 'idx_estado_ano', '`estado`, `ano`');
CALL add_index_if_not_exists('proyectos', 'idx_sector_estado', '`sector`, `estado`');

-- Home
CALL add_index_if_not_exists('home_banners', 'idx_estado_orden', '`estado`, `orden`');
CALL add_index_if_not_exists('home_banners', 'idx_fechas_vigencia', '`fecha_inicio`, `fecha_fin`');
CALL add_index_if_not_exists('home_productos_destacados', 'idx_estado_orden', '`estado`, `orden`');
CALL add_index_if_not_exists('home_servicios', 'idx_estado_orden', '`estado`, `orden`');
CALL add_index_if_not_exists('home_aliados', 'idx_estado_orden', '`estado`, `orden`');

-- Audit Logs
CALL add_index_if_not_exists('audit_logs', 'idx_usuario_id', '`usuario_id`');
CALL add_index_if_not_exists('audit_logs', 'idx_modulo_accion', '`modulo`, `accion`');
CALL add_index_if_not_exists('audit_logs', 'idx_created_at', '`created_at`');

-- Limpiar procedimiento temporal
DROP PROCEDURE IF EXISTS add_index_if_not_exists;

-- ========================================
-- OPTIMIZAR TABLAS DESPUÉS DE AGREGAR ÍNDICES
-- ========================================

ANALYZE TABLE `catalogo_productos`;
ANALYZE TABLE `blog_articulos`;
ANALYZE TABLE `contact_messages`;
ANALYZE TABLE `cotizaciones`;
ANALYZE TABLE `newsletter_subscriptions`;
ANALYZE TABLE `proyectos`;
ANALYZE TABLE `home_banners`;
ANALYZE TABLE `home_productos_destacados`;
ANALYZE TABLE `home_servicios`;
ANALYZE TABLE `home_aliados`;
ANALYZE TABLE `audit_logs`;

-- ========================================
-- NOTAS
-- ========================================
-- 
-- Esta versión del script usa un procedimiento almacenado temporal
-- para verificar si los índices existen antes de crearlos, evitando
-- errores de "Duplicate key name".
-- 
-- El procedimiento se elimina al final del script.
-- 
-- Si prefieres la versión simple sin verificación, usa:
-- database/fase2/19_add_performance_indexes.sql
-- 
-- ========================================

