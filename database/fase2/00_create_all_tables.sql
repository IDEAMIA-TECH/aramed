-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- SCRIPT CONSOLIDADO DE CREACIÓN DE TABLAS
-- ========================================
-- 
-- Este archivo contiene todas las tablas necesarias para la Fase 2
-- Ejecutar este script creará todas las tablas en el orden correcto
-- 
-- IMPORTANTE: Asegúrate de tener las tablas base creadas:
--   - admin_usuarios
--   - catalogo_productos
--   - catalogo_categorias
--   - blog_articulos
--   - newsletter_simple
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- PARTE 1: RBAC (Usuarios & Roles)
-- ========================================

-- ========================================
-- 1. TABLA: permisos
-- Almacena todos los permisos disponibles en el sistema
-- ========================================
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `modulo` VARCHAR(100) NOT NULL COMMENT 'Nombre del módulo (ej: catalogo, blog, cotizaciones)',
  `accion` VARCHAR(100) NOT NULL COMMENT 'Acción permitida (ej: ver, crear, editar, eliminar)',
  `descripcion` TEXT COMMENT 'Descripción del permiso',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modulo_accion` (`modulo`, `accion`),
  INDEX `idx_modulo` (`modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos disponibles en el sistema';

-- ========================================
-- 2. TABLA: rol_permisos
-- Relación entre roles y permisos
-- ========================================
CREATE TABLE IF NOT EXISTS `rol_permisos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rol` VARCHAR(50) NOT NULL COMMENT 'Nombre del rol (admin, editor, marketing, ventas, etc.)',
  `permiso_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `rol_permiso` (`rol`, `permiso_id`),
  INDEX `idx_rol` (`rol`),
  KEY `permiso_id` (`permiso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos asignados a cada rol';

-- ========================================
-- 3. ALTERAR TABLA: admin_usuarios
-- Agregar campos de seguridad y control
-- ========================================
-- NOTA: Estos campos ya pueden existir en producción.
-- Si obtienes errores de "Duplicate column name", ignóralos - significa que ya existen.
-- Descomenta las líneas que necesites si faltan campos específicos:

/*
ALTER TABLE `admin_usuarios`
ADD COLUMN `forzar_cambio_password` TINYINT(1) DEFAULT 0 COMMENT 'Forzar cambio de contraseña en próximo login',
ADD COLUMN `intentos_fallidos` INT DEFAULT 0 COMMENT 'Contador de intentos fallidos de login',
ADD COLUMN `bloqueado_hasta` DATETIME NULL COMMENT 'Fecha/hora hasta la cual el usuario está bloqueado',
ADD COLUMN `ultimo_cambio_password` TIMESTAMP NULL COMMENT 'Fecha del último cambio de contraseña',
ADD COLUMN `token_recuperacion` VARCHAR(255) NULL COMMENT 'Token para recuperación de contraseña',
ADD COLUMN `token_expira` DATETIME NULL COMMENT 'Fecha de expiración del token de recuperación';

-- Agregar índices para mejorar performance
ALTER TABLE `admin_usuarios`
ADD INDEX `idx_estado` (`estado`),
ADD INDEX `idx_rol` (`rol`),
ADD INDEX `idx_bloqueado` (`bloqueado_hasta`);
*/

-- ========================================
-- 4. TABLA: audit_logs
-- Bitácora de actividad de usuarios
-- ========================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) NOT NULL COMMENT 'ID del usuario que realizó la acción',
  `accion` VARCHAR(100) NOT NULL COMMENT 'Tipo de acción (login, logout, crear, editar, eliminar, etc.)',
  `modulo` VARCHAR(100) DEFAULT NULL COMMENT 'Módulo donde se realizó la acción',
  `entidad_id` INT(11) DEFAULT NULL COMMENT 'ID de la entidad afectada (producto, artículo, etc.)',
  `entidad_tipo` VARCHAR(100) DEFAULT NULL COMMENT 'Tipo de entidad (producto, articulo, usuario, etc.)',
  `detalles` TEXT DEFAULT NULL COMMENT 'Detalles adicionales de la acción (JSON o texto)',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'IP desde donde se realizó la acción',
  `user_agent` VARCHAR(500) DEFAULT NULL COMMENT 'User agent del navegador',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE,
  INDEX `idx_usuario` (`usuario_id`),
  INDEX `idx_accion` (`accion`),
  INDEX `idx_modulo` (`modulo`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_entidad` (`entidad_tipo`, `entidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bitácora de actividad de usuarios del sistema';

-- ========================================
-- PARTE 2: CONFIGURACIÓN
-- ========================================

-- ========================================
-- 5. TABLA: configuracion
-- Almacena configuración del sitio
-- ========================================
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL COMMENT 'Clave única de configuración',
  `valor` TEXT DEFAULT NULL COMMENT 'Valor de la configuración',
  `tipo` ENUM('text', 'number', 'boolean', 'json', 'html') DEFAULT 'text' COMMENT 'Tipo de dato',
  `categoria` VARCHAR(50) DEFAULT 'general' COMMENT 'Categoría: empresa, smtp, integraciones, legal, seo',
  `descripcion` TEXT DEFAULT NULL COMMENT 'Descripción de la configuración',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_clave` (`clave`),
  KEY `idx_categoria` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuraciones iniciales
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
('smtp_host', '', 'text', 'smtp', 'Servidor SMTP'),
('smtp_puerto', '587', 'number', 'smtp', 'Puerto SMTP'),
('smtp_usuario', '', 'text', 'smtp', 'Usuario SMTP'),
('smtp_password', '', 'text', 'smtp', 'Contraseña SMTP'),
('smtp_encryption', 'tls', 'text', 'smtp', 'Tipo de encriptación (tls/ssl)'),
('smtp_from_email', '', 'text', 'smtp', 'Email remitente'),
('smtp_from_name', 'Aramed y Laboratorios', 'text', 'smtp', 'Nombre del remitente'),
('google_analytics_id', 'G-3BPRR93ZCY', 'text', 'integraciones', 'ID de Google Analytics'),
('google_analytics_activo', '1', 'boolean', 'integraciones', 'Activar Google Analytics'),
('legal_privacidad', '', 'html', 'legal', 'Texto de política de privacidad'),
('legal_terminos', '', 'html', 'legal', 'Texto de términos y condiciones'),
('legal_cookies', '', 'html', 'legal', 'Texto de política de cookies'),
('seo_title_prefix', 'Aramed y Laboratorios - ', 'text', 'seo', 'Prefijo para títulos de página'),
('seo_title_suffix', '', 'text', 'seo', 'Sufijo para títulos de página'),
('seo_default_description', 'Distribuidores líderes de tecnología educativa en salud', 'text', 'seo', 'Descripción por defecto'),
('seo_default_keywords', 'simuladores médicos, educación médica, tecnología educativa', 'text', 'seo', 'Palabras clave por defecto'),
('seo_og_image', 'assets/images/design/logo-og.jpg', 'text', 'seo', 'Imagen por defecto para Open Graph')
ON DUPLICATE KEY UPDATE 
    `updated_at` = CURRENT_TIMESTAMP;

-- ========================================
-- PARTE 3: HOME (Gestor de Inicio)
-- ========================================

-- ========================================
-- 6. TABLA: home_banners
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
-- 7. TABLA: home_productos_destacados
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
-- 8. TABLA: home_servicios
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
-- 9. TABLA: home_mision_vision
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
-- 10. TABLA: home_categorias_destacadas
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

-- Insertar misión y visión por defecto
INSERT IGNORE INTO `home_mision_vision` (`tipo`, `contenido`, `estado`) VALUES
('mision', '<p>Nuestra misión es proporcionar soluciones innovadoras en educación médica...</p>', 'activo'),
('vision', '<p>Ser líderes en simulación médica en Latinoamérica...</p>', 'activo');

-- ========================================
-- PARTE 4: PROYECTOS
-- ========================================

-- ========================================
-- 11. TABLA: proyectos
-- Almacena información principal de proyectos
-- ========================================
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
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
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_estado` (`estado`),
  KEY `idx_ano` (`ano`),
  KEY `idx_sector` (`sector`),
  KEY `idx_categoria` (`categoria`),
  FULLTEXT KEY `ft_busqueda` (`titulo`, `descripcion_corta`, `descripcion_larga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 12. TABLA: proyecto_imagenes
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
-- 13. TABLA: proyecto_videos
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
-- 14. TABLA: proyecto_documentos
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

-- ========================================
-- PARTE 5: COTIZACIONES AVANZADO
-- ========================================

-- ========================================
-- 15. TABLA: cotizaciones
-- Almacena las cotizaciones principales
-- ========================================
CREATE TABLE IF NOT EXISTS `cotizaciones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  
  -- Folio único
  `folio` VARCHAR(50) NOT NULL COMMENT 'Folio único: COT-2025-001',
  
  -- Información de la Institución
  `institucion` VARCHAR(255) NOT NULL,
  `tipo_institucion` VARCHAR(100) NOT NULL,
  `campo_adicional` VARCHAR(255) DEFAULT NULL,
  `estado` VARCHAR(100) NOT NULL,
  `ciudad` VARCHAR(150) NOT NULL,
  
  -- Información del Contacto
  `nombre` VARCHAR(255) NOT NULL,
  `puesto` VARCHAR(150) NOT NULL,
  `email_oficial` VARCHAR(255) NOT NULL,
  `email_alterno` VARCHAR(255) DEFAULT NULL,
  `telefono_oficina` VARCHAR(50) NOT NULL,
  `extension` VARCHAR(20) DEFAULT NULL,
  `telefono_celular` VARCHAR(50) DEFAULT NULL,
  
  -- Información de la Cotización
  `producto_interes` VARCHAR(255) DEFAULT NULL COMMENT 'Producto principal de interés',
  `fecha_compra_aprox` DATE DEFAULT NULL,
  `presupuesto_estimado` DECIMAL(15,2) DEFAULT NULL,
  `observaciones` TEXT DEFAULT NULL,
  
  -- Gestión
  `estado_cotizacion` ENUM('nueva', 'en_seguimiento', 'cotizada', 'enviada', 'cerrada_ganada', 'cerrada_perdida') DEFAULT 'nueva',
  `assigned_to` INT(11) DEFAULT NULL COMMENT 'ID del ejecutivo asignado',
  `notas_internas` TEXT DEFAULT NULL COMMENT 'Notas internas del equipo',
  `pdf_propuesta` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta al PDF de propuesta',
  
  -- Metadata
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_folio` (`folio`),
  KEY `idx_estado_cotizacion` (`estado_cotizacion`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_email_oficial` (`email_oficial`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_institucion` (`institucion`),
  FOREIGN KEY (`assigned_to`) REFERENCES `admin_usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 16. TABLA: cotizacion_items
-- Almacena los productos/items de cada cotización
-- ========================================
CREATE TABLE IF NOT EXISTS `cotizacion_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cotizacion_id` INT(11) NOT NULL,
  `producto_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'ID del producto del catálogo (opcional)',
  `producto_nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre del producto',
  `producto_codigo` VARCHAR(100) DEFAULT NULL COMMENT 'Código/SKU del producto',
  `cantidad` INT(11) DEFAULT 1,
  `precio_unitario` DECIMAL(15,2) DEFAULT NULL,
  `descuento` DECIMAL(5,2) DEFAULT 0 COMMENT 'Porcentaje de descuento',
  `subtotal` DECIMAL(15,2) DEFAULT NULL,
  `notas` TEXT DEFAULT NULL COMMENT 'Notas específicas del item',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_cotizacion_id` (`cotizacion_id`),
  KEY `idx_producto_id` (`producto_id`),
  FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 17. TABLA: cotizacion_auditoria
-- Almacena el historial de cambios y acciones
-- ========================================
CREATE TABLE IF NOT EXISTS `cotizacion_auditoria` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cotizacion_id` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `accion` VARCHAR(100) NOT NULL COMMENT 'Ej: estado_cambiado, asignado, nota_agregada, pdf_subido',
  `campo_anterior` VARCHAR(100) DEFAULT NULL,
  `valor_anterior` TEXT DEFAULT NULL,
  `campo_nuevo` VARCHAR(100) DEFAULT NULL,
  `valor_nuevo` TEXT DEFAULT NULL,
  `detalles` TEXT DEFAULT NULL COMMENT 'Información adicional',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_cotizacion_id` (`cotizacion_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Trigger para generar folio automáticamente
-- ========================================
DELIMITER $$

DROP TRIGGER IF EXISTS `generar_folio_cotizacion`$$

CREATE TRIGGER `generar_folio_cotizacion` 
BEFORE INSERT ON `cotizaciones`
FOR EACH ROW
BEGIN
  IF NEW.folio IS NULL OR NEW.folio = '' THEN
    SET @year = YEAR(NOW());
    SET @numero = (SELECT COALESCE(MAX(CAST(SUBSTRING(folio, 10) AS UNSIGNED)), 0) + 1 
                   FROM cotizaciones 
                   WHERE folio LIKE CONCAT('COT-', @year, '-%'));
    SET NEW.folio = CONCAT('COT-', @year, '-', LPAD(@numero, 4, '0'));
  END IF;
END$$

DELIMITER ;

-- ========================================
-- PARTE 6: BLOG - PROGRAMACIÓN
-- ========================================

-- Agregar campo fecha_programada si no existe
-- NOTA: Si obtienes error "Duplicate column name", ignóralo - significa que ya existe.
-- Descomenta si necesitas agregar este campo:

/*
ALTER TABLE `blog_articulos` 
ADD COLUMN `fecha_programada` DATETIME NULL COMMENT 'Fecha y hora programada para publicación automática' 
AFTER `fecha_publicacion`;

-- Agregar índice para búsquedas eficientes
ALTER TABLE `blog_articulos` 
ADD INDEX `idx_fecha_programada` (`fecha_programada`);
*/

-- IMPORTANTE: Si el estado 'programado' no existe en el ENUM, ejecutar manualmente:
-- ALTER TABLE `blog_articulos` MODIFY COLUMN `estado` ENUM('borrador', 'programado', 'publicado', 'archivado') DEFAULT 'borrador';

-- ========================================
-- PARTE 7: SEO & METADATOS
-- ========================================

-- ========================================
-- 18. TABLA: seo_config
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
-- 19. TABLA: seo_metadatos
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
-- 20. TABLA: redirects
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

-- Insertar configuración SEO global inicial
INSERT INTO `seo_config` (`tipo`, `titulo_prefijo`, `titulo_sufijo`, `meta_descripcion_default`, `meta_keywords_default`, `twitter_card_type`)
VALUES (
  'global',
  'Aramed y Laboratorios - ',
  '',
  'Distribuidores líderes de tecnología educativa en salud. Simuladores médicos de alta fidelidad para instituciones educativas y de salud.',
  'simuladores médicos, educación médica, simulación clínica, tecnología educativa, maniquíes médicos, entrenamiento médico',
  'summary_large_image'
) ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- ========================================
-- PARTE 8: NEWSLETTER TEMPLATES
-- ========================================

-- ========================================
-- 21. TABLA: newsletter_templates
-- Almacena plantillas HTML de newsletter
-- ========================================
CREATE TABLE IF NOT EXISTS `newsletter_templates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL,
  `asunto` VARCHAR(255) NOT NULL,
  `contenido_html` TEXT NOT NULL,
  `contenido_texto` TEXT DEFAULT NULL,
  `variables` TEXT DEFAULT NULL COMMENT 'JSON con variables disponibles',
  `estado` ENUM('activo', 'inactivo', 'borrador') DEFAULT 'borrador',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar plantilla por defecto
INSERT INTO `newsletter_templates` (`nombre`, `asunto`, `contenido_html`, `variables`, `estado`)
VALUES (
  'Plantilla Básica',
  'Bienvenido a {{nombre_institucion}}',
  '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{asunto}}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0066cc; color: white; padding: 20px; text-align: center;">
        <h1 style="margin: 0;">{{nombre_institucion}}</h1>
    </div>
    <div style="background: #f8f9fa; padding: 20px; margin-top: 20px;">
        <h2>Hola {{nombre_contacto}},</h2>
        <p>{{mensaje_personalizado}}</p>
        <p>Gracias por suscribirte a nuestro newsletter.</p>
        <p>Saludos,<br>El equipo de {{nombre_institucion}}</p>
    </div>
    <div style="text-align: center; margin-top: 20px; padding: 20px; background: #f8f9fa; font-size: 12px; color: #666;">
        <p>Este es un email automático, por favor no respondas a este mensaje.</p>
        <p><a href="{{link_desuscripcion}}">Cancelar suscripción</a></p>
    </div>
</body>
</html>',
  '{"nombre_institucion": "Nombre de la institución", "nombre_contacto": "Nombre del contacto", "mensaje_personalizado": "Mensaje personalizado", "asunto": "Asunto del email", "link_desuscripcion": "Link para desuscripción"}',
  'activo'
) ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- ========================================
-- PARTE 9: POBLAR PERMISOS
-- ========================================

-- Insertar permisos por módulo
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
-- Dashboard
('dashboard', 'ver', 'Ver el dashboard principal'),
('dashboard', 'editar', 'Editar configuración del dashboard'),
-- Home / Gestor de Inicio
('home', 'ver', 'Ver contenido del gestor de Home'),
('home', 'crear', 'Crear banners, servicios, etc.'),
('home', 'editar', 'Editar contenido del Home'),
('home', 'eliminar', 'Eliminar contenido del Home'),
-- Catálogo
('catalogo', 'ver', 'Ver productos, categorías y marcas'),
('catalogo', 'crear', 'Crear productos, categorías y marcas'),
('catalogo', 'editar', 'Editar productos, categorías y marcas'),
('catalogo', 'eliminar', 'Eliminar productos, categorías y marcas'),
('catalogo', 'exportar', 'Exportar catálogo a CSV/Excel'),
-- Proyectos
('proyectos', 'ver', 'Ver proyectos'),
('proyectos', 'crear', 'Crear proyectos'),
('proyectos', 'editar', 'Editar proyectos'),
('proyectos', 'eliminar', 'Eliminar proyectos'),
-- Blog
('blog', 'ver', 'Ver artículos del blog'),
('blog', 'crear', 'Crear artículos del blog'),
('blog', 'editar', 'Editar artículos del blog'),
('blog', 'eliminar', 'Eliminar artículos del blog'),
('blog', 'moderar', 'Moderar comentarios del blog'),
-- Cotizaciones
('cotizaciones', 'ver', 'Ver cotizaciones'),
('cotizaciones', 'editar', 'Editar cotizaciones'),
('cotizaciones', 'asignar', 'Asignar cotizaciones a ejecutivos'),
('cotizaciones', 'exportar', 'Exportar cotizaciones a CSV/Excel'),
-- Contacto
('contacto', 'ver', 'Ver mensajes de contacto'),
('contacto', 'editar', 'Editar estado de mensajes'),
('contacto', 'asignar', 'Asignar mensajes a responsables'),
-- Newsletter
('newsletter', 'ver', 'Ver suscriptores'),
('newsletter', 'editar', 'Editar suscriptores'),
('newsletter', 'importar', 'Importar suscriptores desde CSV'),
('newsletter', 'exportar', 'Exportar suscriptores a CSV'),
-- SEO
('seo', 'ver', 'Ver configuración SEO'),
('seo', 'editar', 'Editar configuración SEO'),
-- Analytics
('analytics', 'ver', 'Ver dashboard de Analytics'),
('analytics', 'editar', 'Editar configuración de Analytics'),
-- Usuarios
('usuarios', 'ver', 'Ver usuarios del sistema'),
('usuarios', 'crear', 'Crear nuevos usuarios'),
('usuarios', 'editar', 'Editar usuarios'),
('usuarios', 'eliminar', 'Eliminar usuarios'),
-- Configuración
('configuracion', 'ver', 'Ver configuración del sistema'),
('configuracion', 'editar', 'Editar configuración del sistema')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- Asignar permisos a roles
-- ADMIN: Todos los permisos
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'admin', `id` FROM `permisos`
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- MARKETING: Banners, Home, marcas, servicios, blog, SEO, newsletter
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'marketing', `id` FROM `permisos`
WHERE `modulo` IN ('home', 'blog', 'newsletter', 'seo', 'analytics')
   OR (`modulo` = 'catalogo' AND `accion` IN ('ver', 'editar'))
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- VENTAS: Catálogo (parcial), cotizaciones, clientes
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'ventas', `id` FROM `permisos`
WHERE `modulo` IN ('catalogo', 'cotizaciones')
   OR (`modulo` = 'contacto' AND `accion` = 'ver')
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- SOPORTE: Cotizaciones y contacto (bandejas, seguimiento)
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'soporte', `id` FROM `permisos`
WHERE `modulo` IN ('cotizaciones', 'contacto')
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- ANALISTA: Solo lectura (ver) en reportes y dashboards
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'analista', `id` FROM `permisos`
WHERE `accion` = 'ver'
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- EDITOR: Similar a marketing pero sin configuración
INSERT INTO `rol_permisos` (`rol`, `permiso_id`)
SELECT 'editor', `id` FROM `permisos`
WHERE `modulo` IN ('home', 'blog', 'catalogo', 'proyectos')
   AND `modulo` != 'configuracion'
ON DUPLICATE KEY UPDATE `rol` = VALUES(`rol`);

-- ========================================
-- VERIFICACIÓN FINAL
-- ========================================
SELECT 'Script de creación de tablas Fase 2 completado exitosamente' AS status;

-- Verificación simple (sin usar information_schema)
-- Descomentar si tienes permisos para SHOW TABLES
/*
SHOW TABLES LIKE 'permisos';
SHOW TABLES LIKE 'rol_permisos';
SHOW TABLES LIKE 'audit_logs';
SHOW TABLES LIKE 'configuracion';
SHOW TABLES LIKE 'home_banners';
SHOW TABLES LIKE 'home_productos_destacados';
SHOW TABLES LIKE 'home_servicios';
SHOW TABLES LIKE 'home_mision_vision';
SHOW TABLES LIKE 'home_categorias_destacadas';
SHOW TABLES LIKE 'proyectos';
SHOW TABLES LIKE 'proyecto_imagenes';
SHOW TABLES LIKE 'proyecto_videos';
SHOW TABLES LIKE 'proyecto_documentos';
SHOW TABLES LIKE 'cotizaciones';
SHOW TABLES LIKE 'cotizacion_items';
SHOW TABLES LIKE 'cotizacion_auditoria';
SHOW TABLES LIKE 'seo_config';
SHOW TABLES LIKE 'seo_metadatos';
SHOW TABLES LIKE 'redirects';
SHOW TABLES LIKE 'newsletter_templates';
*/

