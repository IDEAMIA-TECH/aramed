-- ========================================
-- ARAMED Y LABORATORIOS - Tabla de Configuración
-- ========================================
-- 
-- Tabla para almacenar configuración del sitio de forma dinámica
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- Tabla: configuracion
-- Almacena configuración del sitio
-- ========================================
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Clave única de configuración',
  `valor` TEXT DEFAULT NULL COMMENT 'Valor de la configuración',
  `tipo` ENUM('text', 'number', 'boolean', 'json', 'html') DEFAULT 'text' COMMENT 'Tipo de dato',
  `categoria` VARCHAR(50) DEFAULT 'general' COMMENT 'Categoría: empresa, smtp, integraciones, legal, seo',
  `descripcion` TEXT DEFAULT NULL COMMENT 'Descripción de la configuración',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`),
  KEY `idx_categoria` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Insertar configuraciones iniciales
-- ========================================

-- Configuración de Empresa
INSERT INTO `configuracion` (`clave`, `valor`, `tipo`, `categoria`, `descripcion`) VALUES
('empresa_nombre', 'Aramed y Laboratorios', 'text', 'empresa', 'Nombre de la empresa'),
('empresa_razon_social', 'Aramed y Laboratorios S.A. de C.V.', 'text', 'empresa', 'Razón social'),
('empresa_direccion', '', 'text', 'empresa', 'Dirección completa'),
('empresa_telefono', '', 'text', 'empresa', 'Teléfono principal'),
('empresa_email', '', 'text', 'empresa', 'Email de contacto principal'),
('empresa_email_ventas', '', 'text', 'empresa', 'Email de ventas'),
('empresa_email_soporte', '', 'text', 'empresa', 'Email de soporte'),
('empresa_website', 'https://aramedylaboratorio.com', 'text', 'empresa', 'Sitio web'),
('empresa_facebook', '', 'text', 'empresa', 'URL de Facebook'),
('empresa_instagram', '', 'text', 'empresa', 'URL de Instagram'),
('empresa_linkedin', '', 'text', 'empresa', 'URL de LinkedIn'),
('empresa_twitter', '', 'text', 'empresa', 'URL de Twitter'),

-- Configuración SMTP
('smtp_host', '', 'text', 'smtp', 'Servidor SMTP'),
('smtp_puerto', '587', 'number', 'smtp', 'Puerto SMTP'),
('smtp_usuario', '', 'text', 'smtp', 'Usuario SMTP'),
('smtp_password', '', 'text', 'smtp', 'Contraseña SMTP'),
('smtp_encryption', 'tls', 'text', 'smtp', 'Tipo de encriptación (tls/ssl)'),
('smtp_from_email', '', 'text', 'smtp', 'Email remitente'),
('smtp_from_name', 'Aramed y Laboratorios', 'text', 'smtp', 'Nombre del remitente'),

-- Integraciones
('google_analytics_id', 'G-3BPRR93ZCY', 'text', 'integraciones', 'ID de Google Analytics'),
('google_analytics_activo', '1', 'boolean', 'integraciones', 'Activar Google Analytics'),

-- Textos Legales
('legal_privacidad', '', 'html', 'legal', 'Texto de política de privacidad'),
('legal_terminos', '', 'html', 'legal', 'Texto de términos y condiciones'),
('legal_cookies', '', 'html', 'legal', 'Texto de política de cookies'),

-- SEO
('seo_title_prefix', 'Aramed y Laboratorios - ', 'text', 'seo', 'Prefijo para títulos de página'),
('seo_title_suffix', '', 'text', 'seo', 'Sufijo para títulos de página'),
('seo_default_description', 'Distribuidores líderes de tecnología educativa en salud', 'text', 'seo', 'Descripción por defecto'),
('seo_default_keywords', 'simuladores médicos, educación médica, tecnología educativa', 'text', 'seo', 'Palabras clave por defecto'),
('seo_og_image', 'assets/images/design/logo-og.jpg', 'text', 'seo', 'Imagen por defecto para Open Graph')

ON DUPLICATE KEY UPDATE 
    `updated_at` = CURRENT_TIMESTAMP;

