-- ========================================
-- ARAMED Y LABORATORIOS - SEO & Metadatos
-- ========================================
-- 
-- Estructura de base de datos para gestión SEO
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- Tabla: seo_config
-- Configuración SEO global y por página
-- ========================================
CREATE TABLE IF NOT EXISTS `seo_config` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('global', 'pagina') NOT NULL DEFAULT 'global',
  `pagina` VARCHAR(100) DEFAULT NULL COMMENT 'Identificador de página (home, catalogo, blog, etc.)',
  `titulo_prefijo` VARCHAR(100) DEFAULT NULL,
  `titulo_sufijo` VARCHAR(100) DEFAULT NULL,
  `meta_descripcion_default` TEXT DEFAULT NULL,
  `meta_keywords_default` TEXT DEFAULT NULL,
  `favicon` VARCHAR(255) DEFAULT NULL,
  `og_image` VARCHAR(255) DEFAULT NULL,
  `twitter_card_type` ENUM('summary', 'summary_large_image') DEFAULT 'summary_large_image',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo_pagina` (`tipo`, `pagina`),
  KEY `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: seo_metadatos
-- Metadatos específicos por entidad (producto, artículo, proyecto)
-- ========================================
CREATE TABLE IF NOT EXISTS `seo_metadatos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tipo_entidad` ENUM('producto', 'articulo', 'proyecto', 'categoria', 'pagina') NOT NULL,
  `entidad_id` INT(11) NOT NULL,
  `meta_titulo` VARCHAR(255) DEFAULT NULL,
  `meta_descripcion` TEXT DEFAULT NULL,
  `meta_keywords` TEXT DEFAULT NULL,
  `og_title` VARCHAR(255) DEFAULT NULL,
  `og_description` TEXT DEFAULT NULL,
  `og_image` VARCHAR(255) DEFAULT NULL,
  `twitter_title` VARCHAR(255) DEFAULT NULL,
  `twitter_description` TEXT DEFAULT NULL,
  `twitter_image` VARCHAR(255) DEFAULT NULL,
  `canonical_url` VARCHAR(500) DEFAULT NULL,
  `robots` VARCHAR(100) DEFAULT 'index, follow' COMMENT 'Directivas robots (noindex, nofollow, etc.)',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `entidad` (`tipo_entidad`, `entidad_id`),
  KEY `idx_tipo_entidad` (`tipo_entidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: redirects
-- Redirecciones 301/302
-- ========================================
CREATE TABLE IF NOT EXISTS `redirects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `url_antigua` VARCHAR(500) NOT NULL,
  `url_nueva` VARCHAR(500) NOT NULL,
  `tipo` ENUM('301', '302') DEFAULT '301',
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `hits` INT(11) DEFAULT 0 COMMENT 'Contador de redirecciones',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_antigua` (`url_antigua`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Insertar configuración SEO global inicial
-- ========================================
INSERT INTO `seo_config` (`tipo`, `titulo_prefijo`, `titulo_sufijo`, `meta_descripcion_default`, `meta_keywords_default`, `twitter_card_type`)
VALUES (
  'global',
  'Aramed y Laboratorios - ',
  '',
  'Distribuidores líderes de tecnología educativa en salud. Simuladores médicos de alta fidelidad para instituciones educativas y de salud.',
  'simuladores médicos, educación médica, simulación clínica, tecnología educativa, maniquíes médicos, entrenamiento médico',
  'summary_large_image'
) ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

