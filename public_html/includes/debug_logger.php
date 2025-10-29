<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Debug Logger
 * ========================================
 * 
 * Sistema de logging deshabilitado por seguridad
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}

/**
 * Función de logging (no realiza ninguna acción por seguridad)
 * 
 * @param string $message Mensaje a loguear
 * @param mixed $data Datos adicionales (opcional)
 * @return void
 */
function debugLog($message, $data = null) {
    // No logging en producción por seguridad
    return;
}
