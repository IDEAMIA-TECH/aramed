<?php
/**
 * Sistema de Logging para Debug
 * 
 * Guarda logs en un archivo visible para debugging
 */

// Archivo de log
define('DEBUG_LOG_FILE', ROOT_PATH . '/logs/debug.log');

/**
 * Escribir log de debug
 */
function debugLog($message, $data = null) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}";
    
    if ($data !== null) {
        $logMessage .= " | Data: " . print_r($data, true);
    }
    
    $logMessage .= "\n";
    
    // Escribir al error log del servidor
    error_log($message);
    
    // Escribir a archivo de debug
    @file_put_contents(DEBUG_LOG_FILE, $logMessage, FILE_APPEND);
}

/**
 * Leer últimas líneas del log de debug
 */
function getDebugLog($lines = 50) {
    if (!file_exists(DEBUG_LOG_FILE)) {
        return "No hay logs disponibles";
    }
    
    $content = file(DEBUG_LOG_FILE);
    $lastLines = array_slice($content, -$lines);
    return implode('', $lastLines);
}

/**
 * Limpiar log de debug
 */
function clearDebugLog() {
    if (file_exists(DEBUG_LOG_FILE)) {
        @unlink(DEBUG_LOG_FILE);
    }
}

