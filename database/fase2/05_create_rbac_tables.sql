-- ========================================
-- SCRIPT DE CREACIÓN DE TABLAS RBAC
-- Fase 2 - Usuarios & Roles
-- ========================================
-- Fecha: Enero 2025
-- Descripción: Crea las tablas necesarias para el sistema RBAC granular

-- ========================================
-- 1. TABLA: permisos
-- Almacena todos los permisos disponibles en el sistema
-- ========================================
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `modulo` VARCHAR(100) NOT NULL COMMENT 'Nombre del módulo (ej: catalogo, blog, cotizaciones)',
  `accion` VARCHAR(100) NOT NULL COMMENT 'Acción permitida (ej: ver, crear, editar, eliminar)',
  `descripcion` TEXT COMMENT 'Descripción del permiso',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `modulo_accion` (`modulo`, `accion`),
  INDEX `idx_modulo` (`modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos disponibles en el sistema';

-- ========================================
-- 2. TABLA: rol_permisos
-- Relación entre roles y permisos
-- ========================================
CREATE TABLE IF NOT EXISTS `rol_permisos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `rol` VARCHAR(50) NOT NULL COMMENT 'Nombre del rol (admin, editor, marketing, ventas, etc.)',
  `permiso_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `rol_permiso` (`rol`, `permiso_id`),
  INDEX `idx_rol` (`rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos asignados a cada rol';

-- ========================================
-- 3. ALTERAR TABLA: admin_usuarios
-- Agregar campos de seguridad y control
-- ========================================
ALTER TABLE `admin_usuarios`
ADD COLUMN IF NOT EXISTS `forzar_cambio_password` TINYINT(1) DEFAULT 0 COMMENT 'Forzar cambio de contraseña en próximo login',
ADD COLUMN IF NOT EXISTS `intentos_fallidos` INT DEFAULT 0 COMMENT 'Contador de intentos fallidos de login',
ADD COLUMN IF NOT EXISTS `bloqueado_hasta` DATETIME NULL COMMENT 'Fecha/hora hasta la cual el usuario está bloqueado',
ADD COLUMN IF NOT EXISTS `ultimo_cambio_password` TIMESTAMP NULL COMMENT 'Fecha del último cambio de contraseña',
ADD COLUMN IF NOT EXISTS `token_recuperacion` VARCHAR(255) NULL COMMENT 'Token para recuperación de contraseña',
ADD COLUMN IF NOT EXISTS `token_expira` DATETIME NULL COMMENT 'Fecha de expiración del token de recuperación';

-- Agregar índices para mejorar performance
ALTER TABLE `admin_usuarios`
ADD INDEX IF NOT EXISTS `idx_estado` (`estado`),
ADD INDEX IF NOT EXISTS `idx_rol` (`rol`),
ADD INDEX IF NOT EXISTS `idx_bloqueado` (`bloqueado_hasta`);

-- ========================================
-- 4. TABLA: audit_logs
-- Bitácora de actividad de usuarios
-- ========================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL COMMENT 'ID del usuario que realizó la acción',
  `accion` VARCHAR(100) NOT NULL COMMENT 'Tipo de acción (login, logout, crear, editar, eliminar, etc.)',
  `modulo` VARCHAR(100) COMMENT 'Módulo donde se realizó la acción',
  `entidad_id` INT COMMENT 'ID de la entidad afectada (producto, artículo, etc.)',
  `entidad_tipo` VARCHAR(100) COMMENT 'Tipo de entidad (producto, articulo, usuario, etc.)',
  `detalles` TEXT COMMENT 'Detalles adicionales de la acción (JSON o texto)',
  `ip_address` VARCHAR(45) COMMENT 'IP desde donde se realizó la acción',
  `user_agent` VARCHAR(500) COMMENT 'User agent del navegador',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE,
  INDEX `idx_usuario` (`usuario_id`),
  INDEX `idx_accion` (`accion`),
  INDEX `idx_modulo` (`modulo`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_entidad` (`entidad_tipo`, `entidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bitácora de actividad de usuarios del sistema';

-- ========================================
-- VERIFICACIÓN
-- ========================================
SELECT 'Tablas RBAC creadas exitosamente' AS status;

