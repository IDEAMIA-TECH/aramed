<?php
/**
 * Google Analytics (gtag.js)
 * Configuración dinámica desde admin
 */

// Cargar configuración si las funciones están disponibles
$measurement_id = 'G-3BPRR93ZCY'; // Valor por defecto
$activar_tracking = true;

if (function_exists('getConfig')) {
    $measurement_id = getConfig('analytics_measurement_id', 'G-3BPRR93ZCY');
    $activar_tracking = getConfig('analytics_activar_tracking', '1') === '1';
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


