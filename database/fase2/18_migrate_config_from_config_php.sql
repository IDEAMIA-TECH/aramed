-- ========================================
-- ARAMED Y LABORATORIOS - Migración de Configuración
-- ========================================
-- 
-- Script para migrar valores de config.php a la tabla configuracion
-- 
-- IMPORTANTE: Este script actualiza valores existentes con los valores
-- de config.php. Si ya hay valores en la BD, se actualizarán.
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- MIGRACIÓN DE CONFIGURACIÓN
-- ========================================

-- Configuración de Empresa
INSERT INTO `configuracion` (`clave`, `valor`, `tipo`, `categoria`, `descripcion`) VALUES
('empresa_nombre', 'Aramed y Laboratorios', 'text', 'empresa', 'Nombre de la empresa'),
('empresa_dominio', 'aramedylaboratorio.com', 'text', 'empresa', 'Dominio del sitio'),
('empresa_tagline', 'Simuladores médicos para la enseñanza', 'text', 'empresa', 'Tagline del sitio'),
('empresa_descripcion', 'Distribuidores líderes de tecnología educativa en salud. Simuladores médicos de alta fidelidad para instituciones educativas y de salud.', 'text', 'empresa', 'Descripción del sitio'),
('empresa_keywords', 'simuladores médicos, educación médica, simulación clínica, tecnología educativa, maniquíes médicos, entrenamiento médico', 'text', 'empresa', 'Palabras clave del sitio'),
('empresa_telefono_principal', '+52 (55) 1234-5678', 'text', 'empresa', 'Teléfono de contacto principal'),
('empresa_telefono_formato', '(800) 999-0407', 'text', 'empresa', 'Teléfono formateado'),
('empresa_telefono_sin_formato', '8009990407', 'text', 'empresa', 'Teléfono sin formato'),
('empresa_horario_semana', 'Lunes a Viernes: 9:00–14:00 y 16:00–19:00', 'text', 'empresa', 'Horario de atención entre semana'),
('empresa_horario_sabado', 'Sábados: 10:00–14:00', 'text', 'empresa', 'Horario de atención sábados'),
('empresa_email_contacto', 'atencionacliente@aramedylaboratorio.com', 'text', 'empresa', 'Email de contacto principal'),
('empresa_email_marketing', 'marketing@aramedylaboratorio.com', 'text', 'empresa', 'Email de marketing'),
('empresa_email_soporte', 'soporte@ideamia.com.mx', 'text', 'empresa', 'Email de soporte'),
('empresa_direccion_calle', '', 'text', 'empresa', 'Calle y número'),
('empresa_direccion_ciudad', '', 'text', 'empresa', 'Ciudad'),
('empresa_direccion_cp', '', 'text', 'empresa', 'Código postal'),
('empresa_direccion_pais', '', 'text', 'empresa', 'País'),

-- Redes Sociales
('social_facebook', 'https://www.facebook.com/aramedylaboratorio', 'text', 'empresa', 'URL de Facebook'),
('social_instagram', 'https://www.instagram.com/aramedylaboratorio', 'text', 'empresa', 'URL de Instagram'),
('social_linkedin', 'https://www.linkedin.com/company/aramedylaboratorio', 'text', 'empresa', 'URL de LinkedIn'),
('social_twitter', 'https://twitter.com/aramedylab', 'text', 'empresa', 'URL de Twitter'),

-- Configuración SMTP
('smtp_host', 'mail.aramedylaboratorio.com', 'text', 'smtp', 'Servidor SMTP'),
('smtp_puerto', '465', 'number', 'smtp', 'Puerto SMTP'),
('smtp_encryption', 'ssl', 'text', 'smtp', 'Tipo de encriptación (tls/ssl)'),
('smtp_auth', '1', 'boolean', 'smtp', 'Requiere autenticación'),
('smtp_usuario', 'web@aramedylaboratorio.com', 'text', 'smtp', 'Usuario SMTP'),
('smtp_password', 'xpC5OS67rVMNvU2(', 'text', 'smtp', 'Contraseña SMTP'),
('smtp_from_email', 'web@aramedylaboratorio.com', 'text', 'smtp', 'Email remitente'),
('smtp_from_name', 'Aramed y Laboratorios', 'text', 'smtp', 'Nombre del remitente'),

-- Integraciones
('google_analytics_id', 'G-3BPRR93ZCY', 'text', 'integraciones', 'ID de Google Analytics'),
('google_analytics_activo', '1', 'boolean', 'integraciones', 'Activar Google Analytics'),
('recaptcha_site_key', '', 'text', 'integraciones', 'Clave pública de reCAPTCHA'),
('recaptcha_secret_key', '', 'text', 'integraciones', 'Clave secreta de reCAPTCHA'),
('recaptcha_enabled', '0', 'boolean', 'integraciones', 'Activar reCAPTCHA'),

-- Configuración de Sesiones
('session_name', 'aramed_session', 'text', 'general', 'Nombre de la sesión'),
('session_lifetime', '7200', 'number', 'general', 'Tiempo de vida de la sesión en segundos'),

-- Paginación
('paginacion_items_por_pagina', '12', 'number', 'general', 'Items por página en listados generales'),
('paginacion_blog_por_pagina', '9', 'number', 'general', 'Artículos por página en blog'),
('paginacion_proyectos_por_pagina', '12', 'number', 'general', 'Proyectos por página'),

-- Uploads
('upload_max_file_size', '10485760', 'number', 'general', 'Tamaño máximo de archivo en bytes (10MB)'),
('upload_allowed_image_types', 'image/jpeg,image/png,image/gif,image/webp', 'text', 'general', 'Tipos de imagen permitidos'),
('upload_allowed_doc_types', 'application/pdf', 'text', 'general', 'Tipos de documento permitidos'),

-- SEO
('seo_title_prefix', 'Aramed y Laboratorios - ', 'text', 'seo', 'Prefijo para títulos de página'),
('seo_title_suffix', '', 'text', 'seo', 'Sufijo para títulos de página'),
('seo_default_description', 'Distribuidores líderes de tecnología educativa en salud. Simuladores médicos de alta fidelidad para instituciones educativas y de salud.', 'text', 'seo', 'Descripción por defecto para SEO'),
('seo_default_keywords', 'simuladores médicos, educación médica, simulación clínica, tecnología educativa, maniquíes médicos, entrenamiento médico', 'text', 'seo', 'Palabras clave por defecto para SEO'),
('seo_og_image', 'assets/images/design/logo-og.jpg', 'text', 'seo', 'Imagen por defecto para Open Graph')

ON DUPLICATE KEY UPDATE 
    `valor` = VALUES(`valor`),
    `tipo` = VALUES(`tipo`),
    `categoria` = VALUES(`categoria`),
    `updated_at` = CURRENT_TIMESTAMP;

-- ========================================
-- NOTAS IMPORTANTES
-- ========================================
-- 
-- 1. Este script NO elimina valores existentes, solo los actualiza
-- 2. Los valores sensibles (como contraseñas SMTP) se migran pero deben
--    verificarse y actualizarse manualmente si es necesario
-- 3. Después de ejecutar este script, se recomienda:
--    - Verificar que todos los valores se migraron correctamente
--    - Actualizar valores sensibles si es necesario
--    - Probar el envío de emails con la configuración SMTP
--    - Verificar que Google Analytics esté funcionando
-- 
-- ========================================

