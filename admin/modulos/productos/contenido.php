<?php
// catPic
	$deletePic='';
	$catPic='';
	$pic='../img/contenido/'.$seccioncat.'/'.$row_CATEGORIAS['imagen'];
	if($row_CATEGORIAS['imagen']!='' AND file_exists($pic)){
		$deletePic='<button class="uk-icon-button uk-button-danger borrarpiccat" uk-icon="icon:trash"></button>';
		$catPic.='
		<div class="uk-panel uk-text-center">
			<a href="'.$pic.'" target="_blank">
				<img src="'.$pic.'" class="uk-border-rounded margen-top-20" style="max-width:300px;">
			</a>
		</div>';
	}else{
		$catPic.='
		<div class="uk-panel uk-text-center">
			<p class="uk-scrollable-box color-white"><i uk-icon="icon:image;ratio:5;"></i><br><br>
				Falta logo<br><br>
			</p>
		</div>';
	}
?>

<div class="uk-width-1-1 margen-top-20 uk-text-left">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<?php 
		echo '
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=all">Productos</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=categorias">categorías</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=catdetalle&cat='.$catParentID.'">'.$catParent.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=contenido&cat='.$cat.'" class="color-red">'.$catNAME.'</a></li>';
		?>
	</ul>
</div>

<div class="uk-width-1-2@m">
</div>

<div class="uk-width-1-2@m">
	<div class="uk-form">
		<form action="index.php" method="post" name="fomrulario<?=$cat?>" onsubmit="return checkForm(this);">

			<input type="hidden" name="editarcategoria" value="1">
			<input type="hidden" name="seccion" value="<?=$seccion?>">
			<input type="hidden" name="subseccion" value="contenido">
			<input type="hidden" name="cat" value="<?=$cat?>">
			<input type="hidden" name="id" value="<?=$cat?>">

			<div class="uk-width-1-1 uk-margin-top uk-button-group">
				<input type="text" id="subtxt" class="uk-width-5-6 uk-input" name="txt" value="<?=$catNAME?>" required>
				<button name="send" class="uk-button uk-button-primary uk-width-1-6"><span uk-icon="icon:check"></span></button>
			</div>
		</form>
	</div>
</div>



<div class="uk-width-medium-1-1 margen-v-50">
	<table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-middle uk-table-responsive" id="tablaproductos">
		<thead>
			<tr class="uk-text-muted">
				<th width="30px"></th>
				<th onclick="sortTable(1)">Titulo</th>
				<th width="80px" onclick="sortTable(2)">Activo</th>
				<th width="90px" class="uk-text-center"><i class="fa fa-2x fa-toggle-on color-blue pointer" id="activalotodocat" uk-tooltip="Activar todo"></i></th>
			</tr>
		</thead>
		<tbody class="sortable" data-tabla="<?=$seccion?>">
		<?php
		$productos = $CONEXION -> query("SELECT * FROM $seccion WHERE categoria = $cat ORDER BY orden");
		while ($row_Consulta1 = $productos -> fetch_assoc()) {
			$prodID=$row_Consulta1['id'];

			$picTxt='';
			$CONSULTAPIC = $CONEXION -> query("SELECT * FROM $seccionpic WHERE producto = $prodID ORDER BY orden,id LIMIT 1");
			$numProds=$CONSULTAPIC->num_rows;
			while ($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()) {
				$pic='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'-sm.jpg';
				if(file_exists($pic)){
					$picTxt='
						<div class="uk-inline">
							<i uk-icon="camera"></i>
							<div uk-drop="pos: right-justify">
								<img src="'.$pic.'" class="uk-border-rounded">
							</div>
						</div>';
				}
			}

			$link='index.php?seccion='.$seccion.'&subseccion=detalle&id='.$row_Consulta1['id'].'&random='.rand(1,9999);
			$linkEditar='index.php?seccion='.$seccion.'&subseccion=editar&id='.$row_Consulta1['id'];

			$estatusIcon=($row_Consulta1['estatus']==0)?'off uk-text-muted':'on uk-text-primary';

			echo '
			<tr id="'.$prodID.'">
				<td>
					'.$picTxt.'
				</td>
				<td>
					'.$row_Consulta1['titulo'].'
				</td>
				<td class="uk-text-center@m">
					<span class="uk-text-muted uk-hidden@m">Activo: </span>
					<i class="estatuschange fa fa-lg fa-toggle-'.$estatusIcon.' uk-text-muted pointer" data-tabla="'.$seccion.'" data-campo="estatus" data-id="'.$prodID.'" data-valor="'.$row_Consulta1['estatus'].'"></i>
				</td>
				<td class="uk-text-center">
					<span data-id="'.$row_Consulta1['id'].'" class="eliminaprod uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></span>&nbsp;&nbsp;
					<a href="'.$link.'" class="uk-icon-button uk-button-primary"><i class="fa fa-search-plus"></i></a>
				</td>
			</tr>';
		}
		?>

		</tbody>
	</table>
</div>

<div class="uk-width-1-2@s">
	<div class="uk-width-1-1 uk-margin">
		<label for="txt">Foto <span class="uk-text-muted">JPG de 520 X 350 px</span></label>
	</div>
	<div id="fileuploader">
		Cargar
	</div>
</div>

<div class="uk-width-1-2">
	<?=$catPic?>
	<div class="uk-width-1-1 uk-margin uk-text-center">
		<?=$deletePic?>
	</div>
</div>


<div>
	<div id="buttons">
		<a href="index.php?seccion=<?=$seccion?>&subseccion=nuevo&cat=<?=$cat?>" class="uk-icon-button uk-button-primary" uk-toggle uk-icon="icon:plus"></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>


<?php
$scripts='

	// Borrar logo
	$(".borrarpiccat").click(function() {
		var statusConfirm = confirm("Realmente desea borrar esto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&cat='.$cat.'&borrarpiccat");
		} 
	});

	$(document).ready(function() {
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'true\',
			allowedTypes: "jpg,jpeg",
			maxFileSize: 6291456,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){ 
				window.location = (\'index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&cat='.$cat.'&position=categoria&imagen=\'+data);
			}
		});
	});


	// Eliminar producto
	$(".eliminaprod").click(function() {
		var id = $(this).attr(\'data-id\');
		//console.log(id);
		var statusConfirm = confirm("Realmente desea eliminar este Producto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&borrarPod&cat='.$cat.'&id="+id);
		} 
	});


	// Activar todos los productos de la categoría
	$("#activalotodocat").click(function() {
		$.ajax({
			method: "POST",
			url: "modulos/'.$seccion.'/acciones.php",
			data: { 
				id: "'.$cat.'",
				activalotodocat: 1
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);

			$(".fa-toggle-off").addClass("fa-toggle-on");
			$(".fa-toggle-off").removeClass("fa-toggle-off");
			$(".fa-toggle-on").addClass("color-primary");
			$(".destacado").attr("data-destacado","0");
		});
	});

	';



?>