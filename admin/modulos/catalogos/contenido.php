<?php 
echo '
<div class="uk-width-1-2 margen-top-20 uk-text-left">
	<ul class="uk-breadcrumb">
		<li><a href="index.php?seccion='.$seccion.'" class="color-red">'.$seccion.'</a></li>
	</ul>
</div>
';
?>

<div class="uk-width-1-1 margen-top-20">
	<table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-middle uk-table-responsive uk-text-center" id="ordenar">
		<thead>
			<tr>
				<th onclick="sortTable(0)">Título</th>
				<th width="100px" onclick="sortTable(1)" class="uk-text-center">PDF</th>
				<th width="100px" onclick="sortTable(2)" class="uk-text-center">Imágen</th>
				<th width="100px"></th>
			</tr>
		</thead>
		<tbody class="sortable" data-tabla="<?=$seccion?>">

<?php 
$fileRuta="../img/contenido/".$seccion."/";

$consulta = $CONEXION -> query("SELECT * FROM $seccion ORDER BY orden");
while ($rowConsulta = $consulta -> fetch_assoc()) {
	$id=$rowConsulta['id'];

	$clasepdf='uk-text-muted';
	$claseimg='uk-text-muted';

	$filePdf=$fileRuta.$rowConsulta['pdf'];
	if(file_exists($filePdf) AND strlen($rowConsulta['pdf'])>0){
		$clasepdf='uk-text-primary';
	}

	$fileImg=$fileRuta.$rowConsulta['imagen'];
	if(file_exists($fileImg) AND strlen($rowConsulta['imagen'])>0){
		$claseimg='uk-text-primary';
	}

	echo '
			<tr id="'.$id.'">
				<td>
					<input class="uk-input uk-form-blank editarajax" data-tabla="'.$seccion.'" data-campo="titulo" data-id="'.$rowConsulta['id'].'" placeholder="Título" value="'.$rowConsulta['titulo'].'" tabindex="2">
				</td>
				<td>
					<a href="#upload" uk-toggle data-id="'.$id.'" data-campo="pdf" class="filebutton '.$clasepdf.'"><i class="far fa-2x fa-file-pdf"></i></a>
				</td>
				<td>
					<a href="#upload" uk-toggle data-id="'.$id.'" data-campo="imagen" class="filebutton '.$claseimg.'"><i class="far fa-2x fa-image"></i></a>
				</td>
				<td>
					<a href="javascript:eliminaPic(id='.$rowConsulta['id'].')" class="uk-icon-button uk-button-danger" uk-icon="icon:trash;"></a>
				</td>
			</tr>';
}
?>

		</tbody>
	</table>
</div>



<div id="add" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
		<h3>Nuevo catálogo</h3>
		<form action="index.php" method="post">
			<input type="hidden" name="seccion" value="<?=$seccion?>">
			<input type="hidden" name="nuevocatalogo" value="1">
			<div class="uk-margin">
				<label for="titulo">Título</label>
				<input class="uk-input" type="text" name="titulo" tabindex="19">
			</div>
			<div class="uk-margin uk-text-center">
				<button class="uk-button uk-button-primary" tabindex="19">Guardar</button>
			</div>
		</form>
	</div>
</div>

<div id="upload" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-outside" type="button" uk-close></button>
		<input type="hidden" id="itemid">
		<input type="hidden" id="itemcampo">
		<div id="fileuploader">
			Cargar
		</div>
    </div>
</div>

<div>
	<div id="buttons">
		<a href="#add" class="uk-icon-button uk-button-primary uk-box-shadow-large" uk-icon="icon:plus;ratio:1.4;" uk-toggle></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>

<?php
$scripts='
	$(".filebutton").click(function(){
		var id = $(this).attr("data-id");
		var campo = $(this).attr("data-campo");
		$("#itemid").val(id);
		$("#itemcampo").val(campo);
	});

	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) 
		{ 
			window.location = ("index.php?seccion='.$seccion.'&borrar=1&id="+id);
		} 
	};

	$(function(){
		$(\'#sort\').sortable({
			update: function( event, ui ) {
				var PostData = $( "#sort" ).sortable( "toArray");
				$.post(\'modulos/'.$seccion.'/acciones.php\', {list: PostData}, function(guardar){
				}, \'json\')
			}
		});
	});

	$("#fileuploader").uploadFile({
		url:"../library/upload-file/php/upload.php",
		fileName:"myfile",
		maxFileCount:1,
		showDelete: \'false\',
		allowedTypes: "pdf,jpg,jpeg,png,gif",
		maxFileSize: 9999999999,
		showFileCounter: false,
		showPreview:false,
		returnType:\'json\',
		onSuccess:function(data){
			var id = $("#itemid").val();
			var campo = $("#itemcampo").val();
			window.location = ("index.php?rand='.rand(0,999999).'&seccion='.$seccion.'&campo="+campo+"&id="+id+"&uploadedfile="+data);
		}
	});


	';
?>