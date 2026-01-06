-- ========================================
-- ARAMED Y LABORATORIOS - Migración de Contactos
-- ========================================
-- 
-- Script para migrar datos de newsletter_subscriptions a contact_messages
-- 
-- Este script convierte los registros antiguos de newsletter_subscriptions
-- al formato de contact_messages para que aparezcan en el admin de contacto
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    2026-01-06
-- ========================================

-- ========================================
-- PASO 1: Verificar que las tablas existen
-- ========================================

-- Verificar que newsletter_subscriptions existe
SELECT COUNT(*) as total_newsletter FROM newsletter_subscriptions;

-- Verificar que contact_messages existe
SELECT COUNT(*) as total_contact_messages FROM contact_messages;

-- ========================================
-- PASO 2: Migrar datos de newsletter_subscriptions a contact_messages
-- ========================================
-- 
-- Mapeo de campos:
-- - nombre -> nombre
-- - email_oficial -> email
-- - telefono_oficina (o telefono_celular si no hay) -> telefono
-- - institucion -> institucion
-- - asunto: "Consulta desde Newsletter" o basado en producto_interes
-- - mensaje: observaciones + producto_interes + información adicional
-- - ip_address -> ip_address
-- - user_agent -> user_agent
-- - status: 'active' -> 'nuevo', 'inactive'/'unsubscribed' -> 'cerrado'
-- - created_at -> created_at
-- - updated_at -> updated_at

INSERT INTO `contact_messages` (
    `nombre`,
    `email`,
    `telefono`,
    `institucion`,
    `asunto`,
    `mensaje`,
    `ip_address`,
    `user_agent`,
    `status`,
    `notes`,
    `created_at`,
    `updated_at`
)
SELECT 
    ns.`nombre`,
    ns.`email_oficial` as `email`,
    -- Usar telefono_oficina, si no hay usar telefono_celular, si no hay usar 'N/A'
    COALESCE(
        NULLIF(CONCAT(ns.`telefono_oficina`, IF(ns.`extension` IS NOT NULL AND ns.`extension` != '', CONCAT(' ext. ', ns.`extension`), '')), ''),
        NULLIF(ns.`telefono_celular`, ''),
        'N/A'
    ) as `telefono`,
    ns.`institucion`,
    -- Asunto: basado en producto_interes o genérico (máximo 150 caracteres)
    LEFT(COALESCE(
        NULLIF(CONCAT('Consulta sobre: ', ns.`producto_interes`), 'Consulta sobre: '),
        'Consulta desde Newsletter'
    ), 150) as `asunto`,
    -- Mensaje: combinar observaciones, producto_interes y otros datos relevantes
    -- Usar CONCAT_WS para evitar problemas con valores NULL
    -- Asegurar que siempre tenga contenido (mínimo el mensaje de migración)
    COALESCE(
        NULLIF(TRIM(CONCAT_WS('\n',
            IF(ns.`observaciones` IS NOT NULL AND ns.`observaciones` != '', 
                CONCAT('Observaciones: ', ns.`observaciones`), 
                NULL
            ),
            IF(ns.`producto_interes` IS NOT NULL AND ns.`producto_interes` != '', 
                CONCAT('Producto de interés: ', ns.`producto_interes`), 
                NULL
            ),
            IF(ns.`fecha_compra_aprox` IS NOT NULL, 
                CONCAT('Fecha de compra aproximada: ', ns.`fecha_compra_aprox`), 
                NULL
            ),
            IF(ns.`tipo_institucion` IS NOT NULL AND ns.`tipo_institucion` != '', 
                CONCAT('Tipo de institución: ', ns.`tipo_institucion`), 
                NULL
            ),
            IF(ns.`estado` IS NOT NULL AND ns.`estado` != '', 
                CONCAT('Estado: ', ns.`estado`, IF(ns.`ciudad` IS NOT NULL AND ns.`ciudad` != '', CONCAT(' - ', ns.`ciudad`), '')), 
                NULL
            ),
            IF(ns.`puesto` IS NOT NULL AND ns.`puesto` != '', 
                CONCAT('Puesto: ', ns.`puesto`), 
                NULL
            ),
            IF(ns.`email_alterno` IS NOT NULL AND ns.`email_alterno` != '', 
                CONCAT('Email alterno: ', ns.`email_alterno`), 
                NULL
            ),
            '--- Registro migrado desde newsletter_subscriptions ---'
        )), ''),
        'Registro migrado desde newsletter_subscriptions'
    ) as `mensaje`,
    ns.`ip_address`,
    ns.`user_agent`,
    -- Mapear status: 'active' -> 'nuevo', otros -> 'cerrado'
    CASE 
        WHEN ns.`status` = 'active' THEN 'nuevo'
        ELSE 'cerrado'
    END as `status`,
    -- Notes: información adicional para referencia
    CONCAT(
        'Migrado desde newsletter_subscriptions (ID: ', ns.`id`, ')\n',
        'Tipo de institución: ', COALESCE(ns.`tipo_institucion`, 'N/A'), '\n',
        'Estado: ', COALESCE(ns.`estado`, 'N/A'), '\n',
        'Ciudad: ', COALESCE(ns.`ciudad`, 'N/A'), '\n',
        'Puesto: ', COALESCE(ns.`puesto`, 'N/A'), '\n',
        'Email alterno: ', COALESCE(ns.`email_alterno`, 'N/A'), '\n',
        'Campo adicional: ', COALESCE(ns.`campo_adicional`, 'N/A')
    ) as `notes`,
    ns.`created_at`,
    ns.`updated_at`
FROM `newsletter_subscriptions` ns
-- Solo migrar si no existe ya en contact_messages (evitar duplicados)
-- Comparar por email y fecha de creación similar (mismo día)
WHERE NOT EXISTS (
    SELECT 1 
    FROM `contact_messages` cm 
    WHERE cm.`email` = ns.`email_oficial`
    AND DATE(cm.`created_at`) = DATE(ns.`created_at`)
    AND cm.`notes` LIKE CONCAT('%Migrado desde newsletter_subscriptions (ID: ', ns.`id`, ')%')
)
ORDER BY ns.`created_at` ASC;

-- ========================================
-- PASO 3: Verificar resultados
-- ========================================

-- Contar registros migrados
SELECT 
    COUNT(*) as total_migrados,
    SUM(CASE WHEN status = 'nuevo' THEN 1 ELSE 0 END) as nuevos,
    SUM(CASE WHEN status = 'cerrado' THEN 1 ELSE 0 END) as cerrados
FROM `contact_messages`
WHERE `notes` LIKE '%Migrado desde newsletter_subscriptions%';

-- Mostrar algunos ejemplos de registros migrados
SELECT 
    id,
    nombre,
    email,
    asunto,
    status,
    created_at
FROM `contact_messages`
WHERE `notes` LIKE '%Migrado desde newsletter_subscriptions%'
ORDER BY created_at DESC
LIMIT 10;

-- ========================================
-- NOTAS IMPORTANTES:
-- ========================================
-- 
-- 1. Este script NO elimina los datos de newsletter_subscriptions
--    Los datos originales se mantienen intactos
-- 
-- 2. El script evita duplicados verificando si ya existe un registro
--    con el mismo email y fecha de creación
-- 
-- 3. Los registros con status 'active' se migran como 'nuevo'
--    Los registros con status 'inactive' o 'unsubscribed' se migran como 'cerrado'
-- 
-- 4. Toda la información adicional se guarda en el campo 'notes'
--    para referencia futura
-- 
-- 5. Si necesitas migrar nuevamente, puedes ejecutar este script
--    sin riesgo de duplicados (gracias a la verificación WHERE NOT EXISTS)
-- 
-- ========================================
-- FIN DEL SCRIPT
-- ========================================

