-- ========================================
-- ARAMED Y LABORATORIOS - Analizar Tablas
-- ========================================
-- 
-- Script para analizar tablas y actualizar estadísticas de índices
-- Ejecutar después de agregar nuevos índices o después de cambios masivos de datos
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- ANALIZAR TABLAS PRINCIPALES
-- ========================================

-- Catálogo
ANALYZE TABLE `catalogo_productos`;
ANALYZE TABLE `catalogo_categorias`;
ANALYZE TABLE `catalogo_marcas`;

-- Blog
ANALYZE TABLE `blog_articulos`;
ANALYZE TABLE `blog_categorias`;
ANALYZE TABLE `blog_comentarios`;

-- Contacto
ANALYZE TABLE `contact_messages`;

-- Cotizaciones
ANALYZE TABLE `cotizaciones`;
ANALYZE TABLE `cotizacion_items`;

-- Newsletter
ANALYZE TABLE `newsletter_subscriptions`;
ANALYZE TABLE `newsletter_simple`;

-- Proyectos
ANALYZE TABLE `proyectos`;
ANALYZE TABLE `proyecto_imagenes`;
ANALYZE TABLE `proyecto_documentos`;

-- Home
ANALYZE TABLE `home_banners`;
ANALYZE TABLE `home_productos_destacados`;
ANALYZE TABLE `home_servicios`;
ANALYZE TABLE `home_aliados`;
ANALYZE TABLE `home_mision_vision`;
ANALYZE TABLE `home_categorias_destacadas`;
ANALYZE TABLE `home_secciones`;

-- SEO
ANALYZE TABLE `seo_config`;
ANALYZE TABLE `seo_metadatos`;
ANALYZE TABLE `redirects`;

-- Apariencia
ANALYZE TABLE `paginas_estaticas`;

-- Sistema
ANALYZE TABLE `admin_usuarios`;
ANALYZE TABLE `permisos`;
ANALYZE TABLE `rol_permisos`;
ANALYZE TABLE `audit_logs`;
ANALYZE TABLE `configuracion`;

-- ========================================
-- NOTAS
-- ========================================
-- 
-- ANALYZE TABLE actualiza las estadísticas de las tablas que el optimizador
-- de consultas de MySQL usa para decidir cómo ejecutar las consultas.
-- 
-- Se recomienda ejecutar ANALYZE TABLE:
-- - Después de agregar nuevos índices
-- - Después de cambios masivos de datos (INSERT, UPDATE, DELETE)
-- - Periódicamente (semanal o mensual) para mantener estadísticas actualizadas
-- 
-- Este proceso puede tomar algunos minutos dependiendo del tamaño de las tablas.
-- 
-- ========================================

