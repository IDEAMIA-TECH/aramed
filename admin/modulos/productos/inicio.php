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

	// Eliminar foto
	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion=<?=$seccion?>&cat=<?=$cat?>&borrarPic&id=<?=$id?>&picID="+picID);
		} 
	};

	// Botón formulario
	function checkForm(form)
	{
		form.send.value = "Espere...";
		form.send.disabled = true;
		return true;
	};

	<?=$scripts?>

</script>


<?=$footer?>