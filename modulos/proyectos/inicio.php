<?php 

if ($cat!=false && $cat !='') {
	$CATEGORIAS = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
	$row_CATEGORIAS = $CATEGORIAS -> fetch_assoc();
	$catNAME=$row_CATEGORIAS['txt'];
	$catParentID=$row_CATEGORIAS['parent'];

	if ($catParentID!=0) {
		$CATEGORY = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $catParentID");
		$row_CATEGORY = $CATEGORY -> fetch_assoc();
		$catParent=$row_CATEGORY['txt'];
	}

}

require_once('modulos/'.$seccion.'/acciones.php');

echo $head;
echo $header;

require_once('modulos/varios/mensajes.php');
require_once('modulos/'.$seccion.'/'.$subseccion.'.php'); 

?>
<?=$jquery?>


<script>

	// Eliminar producto
	function eliminaCat () { 
		var statusConfirm = confirm("Realmente desea eliminar esta categoria?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?rand=<?=rand(1,1000)?>&seccion=<?=$seccion?>&subseccion=categorias&eliminarCat&cat="+id);
		} 
	};

	// Eliminar foto
	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?rand=<?=rand(1,1000)?>&seccion=<?=$seccion?>&cat=<?=$cat?>&borrarPic&id=<?=$id?>&picID="+picID);
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
				$.ajax({
					method: "POST",
					url: "modulos/<?=$seccion?>/acciones.php",
					data: { 
						list2:PostData
					}
				})
				.done(function( msg ) {
					UIkit.notification.closeAll();
					UIkit.notification(msg);
				});
			}
		});
	})

	// Ordenar categorías
	$(function(){
		$("#sortable3").sortable({
			update: function( event, ui ) {
				var PostData = $( "#sortable3" ).sortable( "toArray");
				$.post("modulos/<?=$seccion?>/acciones.php", {list3: PostData}, function(guardar){
				}, "json")
			}
		});
	})

	$('.cambiarcat').click(function(){
		var newcat = $(this).data('newcat');
		var statusConfirm = confirm("Realmente desea cambiar este producto de categoría?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?rand=<?=rand(1,1000)?>&seccion=<?=$seccion?>&subseccion=detalle&trasladar&newcat="+newcat+"&id=<?=$id?>");
		};
	});

	<?=$scripts?>

</script>


<?=$footer?>