<?php 

require_once('modulos/'.$seccion.'/acciones.php');

echo $head;
echo $header;

require_once('modulos/varios/mensajes.php');
require_once('modulos/'.$seccion.'/'.$subseccion.'.php'); 

?>
<?=$jquery?>


<script>

	// Eliminar foto
	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion=<?=$seccion?>&borrarPic&id=<?=$id?>&picID="+picID);
		} 
	};

	// Botón formulario
	function checkForm(form)
	{
		form.send.value = "Espere...";
		form.send.disabled = true;
		return true;
	};

	// Ordenar productos
	$(function(){
		$("#sortable").sortable({
			update: function( event, ui ) {
				var PostData = $( "#sortable" ).sortable( "toArray");
				$.post("modulos/<?=$seccion?>/acciones.php", {list1: PostData}, function(guardar){
				}, "json")
			}
		});
	})

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

	<?=$scripts?>

</script>


<?=$footer?>