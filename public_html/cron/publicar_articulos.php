<?php
/**
 * ========================================
 * CRON JOB - PUBLICAR ARTÍCULOS PROGRAMADOS
 * ========================================
 * 
 * Script para ejecutar desde cron y publicar artículos programados
 * 
 * Ejecutar cada 5 minutos:
 * */5 * * * * /usr/bin/php /ruta/al/proyecto/public_html/cron/publicar_articulos.php
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';

// Ejecutar publicación automática
if (function_exists('publicarArticulosProgramados')) {
    $publicados = publicarArticulosProgramados();
    
    if ($publicados > 0) {
        echo date('Y-m-d H:i:s') . " - Se publicaron {$publicados} artículo(s) programado(s)\n";
    } else {
        echo date('Y-m-d H:i:s') . " - No hay artículos programados para publicar\n";
    }
} else {
    echo date('Y-m-d H:i:s') . " - Error: Función publicarArticulosProgramados no encontrada\n";
}

