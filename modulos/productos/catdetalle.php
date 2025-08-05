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


echo '

<div class="uk-width-1-2@m margen-top-20">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=all">Productos</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=categorias">categorías</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=catdetalle&cat='.$cat.'" class="color-red">'.$catNAME.'</a></li>
	</ul>
</div>


<div class="uk-width-1-2@m">
	<div class="uk-margin">
		<form action="index.php" method="post" name="fomrulario'.$cat.'" onsubmit="return checkForm(this);">
			<input type="hidden" name="editarcategoria" value="1">
			<input type="hidden" name="seccion" value="'.$seccion.'">
			<input type="hidden" name="subseccion" value="'.$subseccion.'">
			<input type="hidden" name="cat" value="'.$cat.'">
			<input type="hidden" name="id" value="'.$cat.'">
			<input type="hidden" name="subid" id="subid" value="0">
			<div class="uk-width-1-1@s uk-margin-top uk-button-group">
				<input type="text" id="subtxt" class="uk-width-5-6 uk-input" name="txt" value="'.$catNAME.'" required>
				<button name="send" class="uk-button uk-button-primary uk-width-1-6"><span uk-icon="icon:check"></span></button>
			</div>
		</form>
	</div>	
</div>


<div class="uk-width-medium-1-1 margen-v-20">
	<div class="uk-grid">
		<div class="uk-width-1-1 margen-bottom-50">
			<table class="uk-table uk-table-striped uk-table-small uk-table-middle uk-table-responsive" id="tablaproductos">
				<thead>
					<tr class="uk-text-muted">
						<th width="30px;"></th>
						<th onclick="sortTable(0)" class="uk-text-left">Subcategoría</th>
						<th onclick="sortTable(1)" class="uk-text-center">Productos</th>
						<th onclick="sortTable(2)" width="50px">Activo</th>
						<th width="90px"></th>
					</tr>
				</thead>
				<tbody class="sortable" data-tabla="'.$seccioncat.'">
				';
// Obtener subcategorías
$numeroProds=0;
$subcatsNum=0;
$productos_subcat = $CONEXION -> query("SELECT * FROM $seccioncat WHERE parent = $cat ORDER BY orden,id");
$numeroSubcats = $productos_subcat->num_rows;
while ($row_productos_subcat = $productos_subcat -> fetch_assoc()) {
	$actual=$row_productos_subcat['id'];
	$filas = $CONEXION -> query("SELECT * FROM $seccion WHERE categoria = '$actual'");
	$numeroProdsThis = $filas->num_rows;
	$numeroProds+=$numeroProdsThis;
	$row_Filas = $filas -> fetch_assoc();
	$subcatsNum=$row_Filas;

	$picTxt='';
	$pic='../img/contenido/'.$seccioncat.'/'.$row_productos_subcat['imagen'];
	if(strlen($row_productos_subcat['imagen'])>0 AND file_exists($pic)){
		$picTxt='
			<div class="uk-inline">
				<i uk-icon="camera"></i>
				<div uk-drop="pos: right-justify">
					<img src="'.$pic.'" class="uk-border-rounded">
				</div>
			</div>';
	}


	$borrarSubcat='<a href="javascript:eliminaCat(id='.$actual.')" class="uk-icon-button uk-button-danger" uk-icon="icon:trash"></a>';
	if ($numeroProdsThis>0) {
		$borrarSubcat='<a class="uk-icon-button uk-button-default" uk-tooltip title="No puede eliminar<br>Elimine antes su contenido" uk-icon="icon:trash"></a>';
	}

	$estatusIcon=($row_productos_subcat['estatus']==0)?'off uk-text-muted':'on uk-text-primary';

	echo '
					<tr id="'.$actual.'">
						<td>'.$picTxt.'</td>
						<td class="uk-text-left">'.$row_productos_subcat['txt'].'</td>
						<td class="uk-text-center"><span class="uk-hidden">'.(10000+$numeroProdsThis).'</span>'.$numeroProdsThis.'</td>
						<td class="uk-text-center@m">
							<span class="uk-text-muted uk-hidden@m">Activo: </span>
							<i class="estatuschange estatuschangecat fa fa-lg fa-toggle-'.$estatusIcon.' uk-text-muted pointer" data-tabla="'.$seccioncat.'" data-campo="estatus" data-id="'.$actual.'" data-valor="'.$row_productos_subcat['estatus'].'"></i>
						</td>
						<td class="uk-text-right">
							'.$borrarSubcat.'&nbsp;&nbsp;
							<a href="index.php?seccion='.$seccion.'&subseccion=contenido&cat='.$actual.'" class="uk-icon-button uk-button-primary"><i class="fa fa-search-plus"></i></a>
						</td>
					</tr>';
}


echo '
				</tbody>
			</table>
		</div>
	</div>
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
	'.$catPic.'
	<div class="uk-width-1-1 uk-margin uk-text-center">
		'.$deletePic.'
	</div>
</div>

<div>
	<div id="buttons">
		<a href="#nuevacat" class="uk-icon-button uk-button-primary" uk-toggle uk-icon="icon:plus"></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>



<div id="nuevacat" uk-modal="center: true" class="modal">
	<div class="uk-modal-dialog uk-modal-body">
		<a class="uk-modal-close uk-close"></a>
		<form action="index.php" class="uk-width-1-1 uk-text-center uk-form" method="post" name="editar" onsubmit="return checkForm(this);">

			<input type="hidden" name="nuevasubcategoria" value="1">
			<input type="hidden" name="seccion" value="'.$seccion.'">
			<input type="hidden" name="subseccion" value="contenido">
			<input type="hidden" name="cat" value="'.$cat.'">

			<label for="categoria">Nombre de la nueva submarca</label><br><br>
			<input type="text" name="categoria" class="uk-input" required><br><br>
			<input type="submit" name="send" value="Agregar" class="uk-button uk-button-primary">
		</form>
	</div>
</div>
';


$scripts='
	// Eliminar categoria
	function eliminaCat () { 
		var statusConfirm = confirm("Realmente desea eliminar esta submarca?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&eliminarCat&cat="+id);
		} 
	};

	// Ordenar categorías
	$(function(){
		$("#sortable").sortable({
			update: function( event, ui ) {
				var PostData = $( "#sortable" ).sortable( "toArray");
				console.log(PostData);
				$.ajax({
					method: "POST",
					url: "modulos/'.$seccion.'/acciones.php",
					data: { 
						catlist:PostData
					}
				})
				.done(function( msg ) {
					UIkit.notification.closeAll();
					UIkit.notification(msg);
				});
			}
		});
	});

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

	$(".estatuschangecat").click(function(){
		var id = $(this).attr("data-id");
		var estatus = $(this).attr("data-valor");

		$.ajax({
			method: "POST",
			url: "modulos/'.$seccion.'/acciones.php",
			data: { 
				estatuscat: 1,
				id: id,
				estatus: estatus
			}
		})
		.done(function( msg ) {
			console.log( msg );
		});
	});
';

