<?php
$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = 1");
$row_catalogo = $consulta -> fetch_assoc();

echo '
<div class="uk-width-1-1 margen-top-20 uk-text-left">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'" class="color-red">proyectos</a></li>
	</ul>
</div>
';
?>


<div class="uk-width-medium-1-1 margen-v-50">
	<table class="uk-table uk-table-striped uk-table-hover uk-table-middle uk-table-responsive" id="tablaproductos">
		<thead>
			<tr>
				<th width="30px"></th>
				<th >Título</th>
				<th width="100px"></th>
			</tr>
		</thead>
		<tbody class="sortable" data-tabla="<?=$seccion?>">
		<?php
		$productos = $CONEXION -> query("SELECT * FROM $seccion ORDER BY orden");
		while ($row_productos = $productos -> fetch_assoc()) {
			$prodID=$row_productos['id'];
			$imagen=$row_productos['imagen'];
			$mail=$row_productos['mail'];


			$CONSULTA3 = $CONEXION -> query("SELECT * FROM $seccionpic WHERE producto = $prodID ORDER BY orden");
			$row_CONSULTA3 = $CONSULTA3 -> fetch_assoc();
			$picTxt='';
			$pic='../img/contenido/'.$seccion.'/'.$row_CONSULTA3['id'].'-sm.jpg';
			if(file_exists($pic)){
				$picTxt='
					<div class="uk-inline">
						<i uk-icon="camera"></i>
						<div uk-drop="pos: right-justify">
							<img src="'.$pic.'" class="uk-border-rounded">
						</div>
					</div>';
			}

			$link='index.php?seccion='.$seccion.'&subseccion=detalle&id='.$row_productos['id'];

			$estatusIcon=($row_productos['estatus']==0)?'off uk-text-muted':'on uk-text-primary';

			echo '
			<tr id="'.$row_productos['id'].'">
				<td class="uk-text-center">
					'.$picTxt.'
				</td>
				<td>
					'.$row_productos['titulo'].'
				</td>
				<td class="uk-text-center">
					<a href="'.$link.'" class="uk-icon-button uk-button-primary"><i class="fa fa-search-plus"></i></a> &nbsp;
					<span data-id="'.$row_productos['id'].'" class="eliminaprod uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></span>
				</td>
			</tr>';
		}


		?>

		</tbody>
	</table>

</div>



<div>
	<div id="buttons">
		<a href="index.php?seccion=<?=$seccion?>&subseccion=nuevo" class="uk-icon-button uk-button-primary uk-box-shadow-large" uk-icon="icon:plus;ratio:1.4;"></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>


<?php
$scripts='

	// Eliminar producto
	$(".eliminaprod").click(function() {
		var id = $(this).attr(\'data-id\');
		//console.log(id);
		var statusConfirm = confirm("Realmente desea eliminar este Producto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&borrarPod&cat='.$cat.'&id="+id);
		} 
	});





	';


	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Borramos las imágenes que estén remanentes en el directorio files
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$filehandle = opendir($rutaInicial); // Abrir archivos
	while ($file = readdir($filehandle)) {
		if ($file != "." && $file != ".." && $file != ".gitignore" && $file != ".htaccess" && $file != "thumbnail") {
			if(file_exists($rutaInicial.$file)){
				//echo $ruta.$file.'<br>';
				unlink($rutaInicial.$file);
			}
		}
	} 
	closedir($filehandle); 

?>