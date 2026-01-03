-- ========================================
-- ARAMED Y LABORATORIOS - Módulo Proyectos
-- ========================================
-- 
-- Estructura de base de datos para gestión de proyectos
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- Tabla: proyectos
-- Almacena información principal de proyectos
-- ========================================
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `sector` VARCHAR(100) DEFAULT NULL COMMENT 'Sector: Salud, Educación, etc.',
  `categoria` VARCHAR(100) DEFAULT NULL COMMENT 'Categoría del proyecto',
  `ano` YEAR(4) DEFAULT NULL,
  `pais` VARCHAR(100) DEFAULT NULL,
  `ubicacion` VARCHAR(255) DEFAULT NULL,
  `descripcion_corta` TEXT DEFAULT NULL,
  `descripcion_larga` TEXT DEFAULT NULL COMMENT 'Descripción completa con HTML',
  `imagen_principal` VARCHAR(255) DEFAULT NULL,
  `meta_titulo` VARCHAR(255) DEFAULT NULL,
  `meta_descripcion` TEXT DEFAULT NULL,
  `estado` ENUM('borrador', 'publicado') DEFAULT 'borrador',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_estado` (`estado`),
  KEY `idx_ano` (`ano`),
  KEY `idx_sector` (`sector`),
  KEY `idx_categoria` (`categoria`),
  FULLTEXT KEY `ft_busqueda` (`titulo`, `descripcion_corta`, `descripcion_larga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: proyecto_imagenes
-- Almacena imágenes de la galería de proyectos
-- ========================================
CREATE TABLE IF NOT EXISTS `proyecto_imagenes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proyecto_id` INT(11) NOT NULL,
  `imagen_url` VARCHAR(255) NOT NULL,
  `titulo` VARCHAR(255) DEFAULT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `orden` INT(11) DEFAULT 0,
  `es_principal` TINYINT(1) DEFAULT 0 COMMENT '1 si es la imagen principal',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_proyecto_id` (`proyecto_id`),
  KEY `idx_orden` (`orden`),
  FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: proyecto_videos
-- Almacena videos embebidos de proyectos
-- ========================================
CREATE TABLE IF NOT EXISTS `proyecto_videos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proyecto_id` INT(11) NOT NULL,
  `url` VARCHAR(500) NOT NULL COMMENT 'URL del video (YouTube, Vimeo, etc.)',
  `titulo` VARCHAR(255) DEFAULT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `tipo` ENUM('youtube', 'vimeo', 'otro') DEFAULT 'youtube',
  `orden` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_proyecto_id` (`proyecto_id`),
  KEY `idx_orden` (`orden`),
  FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: proyecto_documentos
-- Almacena documentos adjuntos (PDFs, etc.)
-- ========================================
CREATE TABLE IF NOT EXISTS `proyecto_documentos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proyecto_id` INT(11) NOT NULL,
  `archivo_url` VARCHAR(255) NOT NULL,
  `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre del archivo',
  `tipo` VARCHAR(50) DEFAULT 'pdf' COMMENT 'Tipo de archivo: pdf, doc, etc.',
  `tamaño` INT(11) DEFAULT NULL COMMENT 'Tamaño en bytes',
  `descripcion` TEXT DEFAULT NULL,
  `orden` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_proyecto_id` (`proyecto_id`),
  KEY `idx_orden` (`orden`),
  FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

