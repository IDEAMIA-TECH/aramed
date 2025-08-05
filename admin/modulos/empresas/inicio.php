<?=$head?>
<?=$header?>

<?php 
require_once('modulos/varios/mensajes.php');
require_once('modulos/'.$seccion.'/'.$subseccion.'.php'); 
?>

<?=$jquery?>

<script type="text/javascript">
	<?=$scripts?>

		// Ordenar fotos
	$(function(){
		$("#sortable2").sortable({
			update: function( event, ui ) {
				var PostData = $( "#sortable2" ).sortable( "toArray");
				$.post("modulos/<?=$seccion?>/acciones.php", {list2: PostData}, function(guardar){
				}, "json")
			}
		});
	})
</script>

<?=$footer?>
