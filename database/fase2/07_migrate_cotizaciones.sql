-- ========================================
-- ARAMED Y LABORATORIOS - Migración de Cotizaciones
-- ========================================
-- 
-- Script para migrar datos de newsletter_subscriptions a cotizaciones
-- 
-- IMPORTANTE: Ejecutar después de crear las tablas de cotizaciones
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- Verificar que las tablas existan
-- Si no existen, ejecutar primero: 03_create_cotizaciones_tables.sql

-- Migrar datos de newsletter_subscriptions a cotizaciones
INSERT INTO `cotizaciones` (
    `institucion`,
    `tipo_institucion`,
    `campo_adicional`,
    `estado`,
    `ciudad`,
    `nombre`,
    `puesto`,
    `email_oficial`,
    `email_alterno`,
    `telefono_oficina`,
    `extension`,
    `telefono_celular`,
    `producto_interes`,
    `fecha_compra_aprox`,
    `observaciones`,
    `ip_address`,
    `user_agent`,
    `estado_cotizacion`,
    `created_at`,
    `updated_at`
)
SELECT 
    `institucion`,
    `tipo_institucion`,
    `campo_adicional`,
    `estado`,
    `ciudad`,
    `nombre`,
    `puesto`,
    `email_oficial`,
    `email_alterno`,
    `telefono_oficina`,
    `extension`,
    `telefono_celular`,
    `producto_interes`,
    `fecha_compra_aprox`,
    `observaciones`,
    `ip_address`,
    `user_agent`,
    CASE 
        WHEN `status` = 'active' THEN 'nueva'
        WHEN `status` = 'inactive' THEN 'cerrada_perdida'
        ELSE 'nueva'
    END as `estado_cotizacion`,
    `created_at`,
    `updated_at`
FROM `newsletter_subscriptions`
WHERE `status` IN ('active', 'inactive')
ORDER BY `created_at` ASC;

-- Crear items básicos para cada cotización migrada
-- (Si hay producto_interes, crear un item)
INSERT INTO `cotizacion_items` (
    `cotizacion_id`,
    `producto_nombre`,
    `cantidad`,
    `notas`
)
SELECT 
    c.`id`,
    COALESCE(c.`producto_interes`, 'Producto de interés'),
    1,
    'Migrado desde newsletter_subscriptions'
FROM `cotizaciones` c
WHERE c.`producto_interes` IS NOT NULL 
  AND c.`producto_interes` != ''
  AND NOT EXISTS (
    SELECT 1 FROM `cotizacion_items` ci WHERE ci.`cotizacion_id` = c.`id`
  );

-- Registrar en auditoría que fueron migrados
INSERT INTO `cotizacion_auditoria` (
    `cotizacion_id`,
    `usuario_id`,
    `accion`,
    `detalles`
)
SELECT 
    c.`id`,
    1, -- Usuario admin (ajustar según corresponda)
    'migrado',
    CONCAT('Migrado desde newsletter_subscriptions el ', NOW())
FROM `cotizaciones` c
WHERE NOT EXISTS (
    SELECT 1 FROM `cotizacion_auditoria` ca 
    WHERE ca.`cotizacion_id` = c.`id` AND ca.`accion` = 'migrado'
);

-- Verificar migración
SELECT 
    'Migración completada' as status,
    COUNT(*) as total_cotizaciones_migradas
FROM `cotizaciones`;

SELECT 
    'Items creados' as status,
    COUNT(*) as total_items
FROM `cotizacion_items`;

