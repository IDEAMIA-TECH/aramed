<?php 

require_once('modulos/'.$seccion.'/acciones.php');

echo $head;
echo $header;

require_once('modulos/varios/mensajes.php');
require_once('modulos/'.$seccion.'/'.$subseccion.'.php'); 

?>
<?=$jquery?>


<script>

	<?=$scripts?>

</script>


<?=$footer?>