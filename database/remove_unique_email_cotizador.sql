-- ========================================
-- Script para remover UNIQUE KEY de email_oficial
-- Permite múltiples solicitudes del mismo email (COTIZADOR)
-- ========================================

-- Verificar y eliminar el índice UNIQUE si existe
ALTER TABLE `newsletter_subscriptions` 
DROP INDEX IF EXISTS `email_oficial`;

-- Verificar si existe como UNIQUE KEY con otro nombre
ALTER TABLE `newsletter_subscriptions` 
DROP INDEX IF EXISTS `UNIQUE email_oficial`;

-- Mantener el índice normal (no único) para búsquedas rápidas
ALTER TABLE `newsletter_subscriptions`
ADD INDEX `idx_email_oficial` (`email_oficial`);

-- Verificar resultado
SHOW INDEXES FROM `newsletter_subscriptions` WHERE Column_name = 'email_oficial';

