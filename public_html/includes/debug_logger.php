<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Debug Logger
 * ========================================
 * 
 * Sistema de logging para debug
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
 * Función de logging para debug
 * 
 * @param string $message Mensaje a loguear
 * @param mixed $data Datos adicionales (opcional)
 * @return void
 */
function debugLog($message, $data = null) {
    // Solo loguear en desarrollo o si está habilitado el debug
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        $logDir = defined('ROOT_PATH') ? ROOT_PATH . '/logs' : __DIR__ . '/../logs';
        
        // Crear directorio de logs si no existe
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/debug.log';
        $timestamp = date('Y-m-d H:i:s');
        
        $logMessage = "[$timestamp] $message";
        
        if ($data !== null) {
            $logMessage .= "\nData: " . print_r($data, true);
        }
        
        $logMessage .= "\n";
        
        // Escribir al log
        @file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    // También loguear errores críticos en error_log de PHP
    if (strpos($message, '❌') !== false || strpos($message, 'EXCEPTION') !== false) {
        error_log($message);
    }
}

