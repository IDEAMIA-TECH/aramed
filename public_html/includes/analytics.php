<?php
/**
 * Google Analytics (gtag.js)
 * Configuración dinámica desde admin
 */

// Cargar configuración si las funciones están disponibles
$measurement_id = 'G-3BPRR93ZCY'; // Valor por defecto
$activar_tracking = true;

// Verificar que getDB() esté disponible antes de usar getConfig()
if (function_exists('getConfig') && function_exists('getDB')) {
    try {
        $measurement_id = getConfig('analytics_measurement_id', 'G-3BPRR93ZCY');
        $activar_tracking = getConfig('analytics_activar_tracking', '1') === '1';
    } catch (Exception $e) {
        // Si hay error, usar valores por defecto
        error_log("Error en analytics.php al obtener configuración: " . $e->getMessage());
    }
}

// Solo mostrar si el tracking está activado
if ($activar_tracking && !empty($measurement_id)):
?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc($measurement_id); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);} 
  gtag('js', new Date());

  gtag('config', '<?php echo esc($measurement_id); ?>');
</script>
<?php endif; ?>


