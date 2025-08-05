

<div class="uk-width-1-1 margen-top-20 uk-text-left">
	<ul class="uk-breadcrumb">
		<?php 
		echo '
		<li><a href="index.php?seccion='.$seccion.'" class="color-red">'.$seccion.'</a></li>
		';
		?>
	</ul>
</div>

<div class="uk-width-medium-1-1 margen-v-20">
	<table class="uk-table uk-table-striped uk-table-hover uk-table-middle uk-table-small uk-table-responsive">
		<thead>
			<tr>
				<th width="30px"></th>
				<th >Título</th>
				<th width="100px"></th>
			</tr>
		</thead>
		<tbody id="sortable">
		<?php
		$productos = $CONEXION -> query("SELECT * FROM $seccion ORDER BY orden");
		while ($row_productos = $productos -> fetch_assoc()) {
			$prodID=$row_productos['id'];

			$inicioButton=($row_productos['inicio']==1)?'success':'white';

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

			$inicioIcon=($row_productos['inicio']==1)?'off uk-text-muted':'on color-primary';

			echo '
			<tr id="'.$row_productos['id'].'">
				<td class="uk-text-center">
					'.$picTxt.'
				</td>
				<td>
					'.$row_productos['titulo'].'
				</td>
				<td class="uk-text-center">
					<span data-id="'.$row_productos['id'].'" class="eliminaprod uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></span> &nbsp;&nbsp;
					<a href="'.$link.'" class="uk-icon-button uk-button-primary" uk-icon="icon:pencil"></a>
				</td>
			</tr>';
		}
		?>

		</tbody>
	</table>
</div>



<div>
	<div id="buttons">
		<a href="index.php?seccion=<?=$seccion?>&subseccion=nuevo" id="add-button" class="uk-icon-button uk-button-primary uk-box-shadow-large" uk-icon="icon: plus;ratio:1.4"></a>
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
			window.location = ("index.php?seccion='.$seccion.'&subseccion=contenido&borrarPod&id="+id);
		} 
	});


	$(".inicio").click(function(){
		var id = $(this).attr("data-id");
		var inicio = $(this).attr("data-inicio");

		if(inicio==0) {
			console.log(inicio);
			inicio=1;
			$("#button" +id + " > i ").addClass("fa-toggle-off");
			$("#button" +id + " > i ").removeClass("fa-toggle-on");
			$("#button" +id + " > i ").removeClass("color-primary");
			$("#button" +id + " > i ").addClass("uk-text-muted");
		}else{
			inicio=0;
			console.log(inicio);
			$("#button" +id + " > i ").addClass("fa-toggle-on");
			$("#button" +id + " > i ").removeClass("fa-toggle-off");
			$("#button" +id + " > i ").addClass("color-primary");
			$("#button" +id + " > i ").removeClass("uk-text-muted");
		}
		$(this).attr("data-inicio",inicio);

		$.ajax({
			method: "POST",
			url: "modulos/'.$seccion.'/acciones.php",
			data: { 
				id: id,
				seccion: "'.$seccion.'",
				eninicio: 1,
				estado: inicio
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);
		});
	})

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