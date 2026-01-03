-- ========================================
-- SCRIPT DE CREACIÓN DE TABLAS PARA HOME
-- Fase 2 - Gestor de Inicio
-- ========================================
-- Fecha: Enero 2025
-- Descripción: Crea las tablas necesarias para gestionar el contenido del home

-- ========================================
-- 1. TABLA: home_banners
-- Banners/Hero del inicio
-- ========================================
CREATE TABLE IF NOT EXISTS `home_banners` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Título principal del banner',
  `subtitulo` VARCHAR(500) DEFAULT NULL COMMENT 'Subtítulo del banner',
  `imagen_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL de la imagen del banner',
  `video_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL del video (opcional)',
  `cta_texto` VARCHAR(100) DEFAULT NULL COMMENT 'Texto del botón CTA',
  `cta_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL del botón CTA',
  `orden` INT DEFAULT 0 COMMENT 'Orden de visualización',
  `estado` ENUM('publicado', 'borrador') DEFAULT 'borrador',
  `fecha_inicio` DATETIME DEFAULT NULL COMMENT 'Fecha de inicio de vigencia',
  `fecha_fin` DATETIME DEFAULT NULL COMMENT 'Fecha de fin de vigencia',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_estado` (`estado`),
  INDEX `idx_orden` (`orden`),
  INDEX `idx_fechas` (`fecha_inicio`, `fecha_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Banners/Hero del inicio';

-- ========================================
-- 2. TABLA: home_productos_destacados
-- Productos destacados en el home
-- ========================================
CREATE TABLE IF NOT EXISTS `home_productos_destacados` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `producto_id` INT UNSIGNED NOT NULL COMMENT 'ID del producto del catálogo',
  `modo` ENUM('manual', 'automatico') DEFAULT 'manual' COMMENT 'Modo de selección',
  `regla_automatica` VARCHAR(100) DEFAULT NULL COMMENT 'Regla para modo automático (nuevos, destacados, etc.)',
  `orden` INT DEFAULT 0 COMMENT 'Orden de visualización',
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `producto_manual` (`producto_id`, `modo`) COMMENT 'Un producto solo puede estar una vez en modo manual',
  INDEX `idx_estado` (`estado`),
  INDEX `idx_orden` (`orden`),
  INDEX `idx_modo` (`modo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Productos destacados en el home';

-- ========================================
-- 3. TABLA: home_servicios
-- Servicios mostrados en el home
-- ========================================
CREATE TABLE IF NOT EXISTS `home_servicios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `icono` VARCHAR(100) DEFAULT NULL COMMENT 'Clase de icono (Bootstrap Icons) o URL de imagen',
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Título del servicio',
  `resumen` TEXT DEFAULT NULL COMMENT 'Resumen corto del servicio',
  `texto_largo` LONGTEXT DEFAULT NULL COMMENT 'Texto completo del servicio (WYSIWYG)',
  `cta_texto` VARCHAR(100) DEFAULT NULL COMMENT 'Texto del botón CTA',
  `cta_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL del botón CTA',
  `orden` INT DEFAULT 0 COMMENT 'Orden de visualización',
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_estado` (`estado`),
  INDEX `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Servicios mostrados en el home';

-- ========================================
-- 4. TABLA: home_mision_vision
-- Misión y Visión de la empresa
-- ========================================
CREATE TABLE IF NOT EXISTS `home_mision_vision` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo` ENUM('mision', 'vision') NOT NULL COMMENT 'Tipo de contenido',
  `titulo` VARCHAR(255) DEFAULT NULL COMMENT 'Título opcional',
  `contenido` LONGTEXT NOT NULL COMMENT 'Contenido de misión o visión (WYSIWYG)',
  `imagen_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL de imagen opcional',
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `tipo_unico` (`tipo`) COMMENT 'Solo una misión y una visión',
  INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Misión y Visión de la empresa';

-- ========================================
-- 5. TABLA: home_categorias_destacadas
-- Categorías destacadas en el home
-- ========================================
CREATE TABLE IF NOT EXISTS `home_categorias_destacadas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `categoria_id` INT UNSIGNED NOT NULL COMMENT 'ID de la categoría del catálogo',
  `orden` INT DEFAULT 0 COMMENT 'Orden de visualización',
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`categoria_id`) REFERENCES `catalogo_categorias`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `categoria_unica` (`categoria_id`) COMMENT 'Una categoría solo puede estar una vez',
  INDEX `idx_estado` (`estado`),
  INDEX `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorías destacadas en el home';

-- ========================================
-- DATOS INICIALES (Opcional)
-- ========================================

-- Insertar misión y visión por defecto (vacías, para que se puedan editar)
INSERT IGNORE INTO `home_mision_vision` (`tipo`, `contenido`, `estado`) VALUES
('mision', '<p>Nuestra misión es proporcionar soluciones innovadoras en educación médica...</p>', 'activo'),
('vision', '<p>Ser líderes en simulación médica en Latinoamérica...</p>', 'activo');

-- ========================================
-- VERIFICACIÓN
-- ========================================
SELECT 'Tablas de Home creadas exitosamente' AS status;

