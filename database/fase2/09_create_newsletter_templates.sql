-- ========================================
-- ARAMED Y LABORATORIOS - Newsletter Templates
-- ========================================
-- 
-- Tabla para almacenar plantillas HTML de newsletter
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

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

