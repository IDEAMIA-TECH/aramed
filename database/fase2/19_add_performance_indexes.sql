-- ========================================
-- ARAMED Y LABORATORIOS - Índices de Performance
-- ========================================
-- 
-- Script para agregar índices que mejoran el rendimiento de consultas comunes
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- ÍNDICES PARA MEJORAR PERFORMANCE
-- ========================================

-- Catálogo de Productos
-- Índice compuesto para búsquedas por estado y categoría
-- Nota: Si el índice ya existe, MySQL mostrará un error pero continuará con el resto
ALTER TABLE `catalogo_productos` 
ADD INDEX `idx_estado_categoria` (`estado`, `categoria_id`);

-- Índice para búsquedas por marca y estado
ALTER TABLE `catalogo_productos` 
ADD INDEX `idx_marca_estado` (`marca_id`, `estado`);

-- Índice para productos destacados
ALTER TABLE `catalogo_productos` 
ADD INDEX `idx_destacado_estado` (`destacado`, `estado`);

-- Índice para ordenamiento por fecha
ALTER TABLE `catalogo_productos` 
ADD INDEX `idx_created_at` (`created_at`);

-- Índice para búsqueda full-text (si no existe)
-- ALTER TABLE `catalogo_productos` ADD FULLTEXT INDEX `ft_nombre_descripcion` (`nombre`, `descripcion_corta`);

-- Blog
-- Índice compuesto para listado de artículos publicados
ALTER TABLE `blog_articulos` 
ADD INDEX `idx_estado_fecha` (`estado`, `fecha_publicacion`);

-- Índice para artículos programados
ALTER TABLE `blog_articulos` 
ADD INDEX `idx_programado_fecha` (`estado`, `fecha_programada`);

-- Índice para búsqueda por categoría
ALTER TABLE `blog_articulos` 
ADD INDEX `idx_categoria_estado` (`categoria_id`, `estado`);

-- Contacto
-- Índice para mensajes por estado
ALTER TABLE `contact_messages` 
ADD INDEX `idx_estado_fecha` (`estado`, `created_at`);

-- Índice para mensajes no leídos
ALTER TABLE `contact_messages` 
ADD INDEX `idx_leido_estado` (`leido`, `estado`);

-- Cotizaciones
-- Índice para cotizaciones por estado
ALTER TABLE `cotizaciones` 
ADD INDEX `idx_estado_fecha` (`estado`, `created_at`);

-- Índice para cotizaciones asignadas
ALTER TABLE `cotizaciones` 
ADD INDEX `idx_asignado_a` (`asignado_a`);

-- Índice para búsqueda por folio
ALTER TABLE `cotizaciones` 
ADD INDEX `idx_folio` (`folio`);

-- Newsletter
-- Índice para suscriptores activos
ALTER TABLE `newsletter_subscriptions` 
ADD INDEX `idx_status_fecha` (`status`, `created_at`);

-- Índice para búsqueda por email
ALTER TABLE `newsletter_subscriptions` 
ADD INDEX `idx_email` (`email`);

-- Proyectos
-- Índice para proyectos publicados
ALTER TABLE `proyectos` 
ADD INDEX `idx_estado_ano` (`estado`, `ano`);

-- Índice para búsqueda por sector
ALTER TABLE `proyectos` 
ADD INDEX `idx_sector_estado` (`sector`, `estado`);

-- Home
-- Índice para banners activos
ALTER TABLE `home_banners` 
ADD INDEX `idx_estado_orden` (`estado`, `orden`);

-- Índice para banners por fechas de vigencia
ALTER TABLE `home_banners` 
ADD INDEX `idx_fechas_vigencia` (`fecha_inicio`, `fecha_fin`);

-- Índice para productos destacados
ALTER TABLE `home_productos_destacados` 
ADD INDEX `idx_estado_orden` (`estado`, `orden`);

-- Índice para servicios activos
ALTER TABLE `home_servicios` 
ADD INDEX `idx_estado_orden` (`estado`, `orden`);

-- Índice para aliados activos
ALTER TABLE `home_aliados` 
ADD INDEX `idx_estado_orden` (`estado`, `orden`);

-- Audit Logs
-- Índice para búsqueda por usuario
ALTER TABLE `audit_logs` 
ADD INDEX `idx_usuario_id` (`usuario_id`);

-- Índice para búsqueda por módulo
ALTER TABLE `audit_logs` 
ADD INDEX `idx_modulo_accion` (`modulo`, `accion`);

-- Índice para búsqueda por fecha
ALTER TABLE `audit_logs` 
ADD INDEX `idx_created_at` (`created_at`);

-- Configuración
-- Índice para búsqueda por categoría (ya existe, pero lo verificamos)
-- ALTER TABLE `configuracion` ADD INDEX `idx_categoria` (`categoria`);

-- ========================================
-- NOTAS IMPORTANTES
-- ========================================
-- 
-- ⚠️ IMPORTANTE: MySQL no soporta "IF NOT EXISTS" en ALTER TABLE ADD INDEX
-- Si un índice ya existe, MySQL mostrará un error (Error 1061: Duplicate key name)
-- pero continuará ejecutando el resto del script.
-- 
-- Para evitar errores, puedes verificar si los índices existen antes de ejecutar:
-- 
-- SELECT COUNT(*) FROM information_schema.statistics 
-- WHERE table_schema = 'tu_base_de_datos' 
-- AND table_name = 'nombre_tabla' 
-- AND index_name = 'nombre_indice';
-- 
-- 1. Estos índices mejoran significativamente el rendimiento de consultas
--    comunes como listados filtrados, búsquedas, etc.
-- 
-- 2. Los índices FULLTEXT requieren que la tabla use MyISAM o InnoDB
--    con versión 5.6+ (InnoDB soporta FULLTEXT desde MySQL 5.6.4)
-- 
-- 3. Se recomienda ejecutar ANALYZE TABLE después de agregar índices:
--    ANALYZE TABLE catalogo_productos;
--    ANALYZE TABLE blog_articulos;
--    ANALYZE TABLE contact_messages;
--    ANALYZE TABLE cotizaciones;
--    ANALYZE TABLE proyectos;
--    ANALYZE TABLE home_banners;
--    ANALYZE TABLE home_productos_destacados;
--    ANALYZE TABLE home_servicios;
--    ANALYZE TABLE home_aliados;
--    ANALYZE TABLE audit_logs;
-- 
-- ========================================

