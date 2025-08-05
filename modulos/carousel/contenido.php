<?php 
$anim0='';
$anim1='';
$anim2='';
$anim3='';
$anim4='';
$anim5='';
$anim6='';
$anim7='';
$ANIM = $CONEXION -> query("SELECT * FROM configuracion WHERE id = 1");
$rowCONSULTA = $ANIM -> fetch_assoc();
switch ($rowCONSULTA['slideranim']) {
	case 0:
		$anim0=' selected';
		break;
	case 1:
		$anim1=' selected';
		break;
	case 2:
		$anim2=' selected';
		break;
	case 3:
		$anim3=' selected';
		break;
	case 4:
		$anim4=' selected';
		break;
	case 5:
		$anim5=' selected';
		break;
	case 6:
		$anim6=' selected';
		break;
	case 7:
		$anim7=' selected';
		break;
}
echo '
<div class="uk-width-1-2 margen-top-20 uk-text-left">
	<ul class="uk-breadcrumb">
		<li><a href="index.php?seccion='.$seccion.'" class="color-red">'.$seccion.'</a></li>
	</ul>
	<div uk-grid>
		<div>
			<label class="uk-form-label">Animación</label>
		</div>
		<div class="uk-width-expand">
			<select class="uk-select selector" data-tabla="configuracion" data-id="1" data-campo="slideranim">
				<option value="0" '.$anim0.'>Desvanecer</option>
				<option value="1" '.$anim1.'>Desplazar</option>
				<option value="2" '.$anim2.'>Agrandar</option>
				<option value="3" '.$anim3.'>Jalar</option>
				<option value="4" '.$anim4.'>Empujar</option>
			</select>
		</div>
	</div>
	<div uk-grid>
		<div>
			<label class="uk-form-label">Proporción</label>
		</div>
		<div class="uk-width-expand">
			<select class="uk-select selector" data-tabla="configuracion" data-id="1" data-campo="sliderproporcion">
				<option value="1:1" '; if ($rowCONSULTA['sliderproporcion']=="1:1") { echo 'selected'; } echo '>1:1 (1)</option>
				<option value="5:4" '; if ($rowCONSULTA['sliderproporcion']=="5:4") { echo 'selected'; } echo '>5:4 (.8)</option>
				<option value="5:3" '; if ($rowCONSULTA['sliderproporcion']=="5:3") { echo 'selected'; } echo '>5:3 (.6)</option>
				<option value="2:1" '; if ($rowCONSULTA['sliderproporcion']=="2:1") { echo 'selected'; } echo '>2:1 (.5)</option>
				<option value="5:2" '; if ($rowCONSULTA['sliderproporcion']=="5:2") { echo 'selected'; } echo '>5:2 (.4)</option>
				<option value="3:1" '; if ($rowCONSULTA['sliderproporcion']=="3:1") { echo 'selected'; } echo '>3:1 (.33)</option>
				<option value="7:2" '; if ($rowCONSULTA['sliderproporcion']=="7:2") { echo 'selected'; } echo '>7:2 (.29)</option>
				<option value="4:1" '; if ($rowCONSULTA['sliderproporcion']=="4:1") { echo 'selected'; } echo '>4:1 (.25)</option>
				<option value="5:1" '; if ($rowCONSULTA['sliderproporcion']=="5:1") { echo 'selected'; } echo '>5:1 (.2)</option>
			</select>
		</div>
	</div>
	<div uk-grid>
		<div>
			<label for="minimo" class="uk-form-label">Alto mínimo</label>
		</div>
		<div class="uk-width-expand">
			<input type="text" class="editarajax uk-input" data-tabla="configuracion" data-campo="sliderhmin" data-id="1" value="'.$rowCONSULTA['sliderhmin'].'" placeholder="200">
		</div>
	</div>
	<div uk-grid>
		<div>
			<label for="maximo" class="uk-form-label">Alto máximo</label>
		</div>
		<div class="uk-width-expand">
			<input type="text" class="editarajax uk-input" data-tabla="configuracion" data-campo="sliderhmax" data-id="1" value="'.$rowCONSULTA['sliderhmax'].'" placeholder="600">
		</div>
	</div>
	<div uk-grid>
		<div class="uk-width-1-3">
			<label class="uk-form-label">Mostrar botón</label>
		</div>
		<div class="uk-width-1-3">
			<label><input type="radio" name="caption" class="selector uk-radio selector" data-tabla="configuracion" data-id="1" data-campo="slidertextos" value="0" '; if ($rowCONSULTA['slidertextos']=="0") { echo 'checked'; } echo '> No</label>
		</div>
		<div class="uk-width-1-3">
			<label><input type="radio" name="caption" class="selector uk-radio selector" data-tabla="configuracion" data-id="1" data-campo="slidertextos" value="1"'; if ($rowCONSULTA['slidertextos']=="1") { echo 'checked'; } echo '> Sí</label>
		</div>
	</div>
</div>
';
?>

<div class="uk-width-1-2 margen-top-20 uk-text-left">
	<p class="uk-text-muted">(Dimensiones aconsejadas: 1200 x 700 px)</p>
	<div id="fileuploader">
		Cargar
	</div>
</div>

<div class="uk-width-1-1 margen-top-20">
	<div uk-grid class="uk-grid-match uk-grid-small sortable" data-tabla="<?=$seccion?>">

<?php 
$ruta="../img/contenido/".$seccion."/";

$images = $CONEXION -> query("SELECT * FROM carousel ORDER BY orden");
while ($row_images = $images -> fetch_assoc()) {
echo '
		<div id="'.$row_images['id'].'" class="uk-width-1-5@l uk-width-1-4@m uk-width-1-3@s">
			<div id="'.$row_images['id'].'" class="uk-card uk-card-default uk-card-body uk-text-center">
				<a 
					href="#config"
					uk-toggle
					class="cfg uk-icon-button uk-button-primary" 
					data-cfgid="'.$row_images['id'].'" 
					data-url="'.$row_images['url'].'"
					uk-icon="icon:cog">
				</a>
				&nbsp;
				<button data-id="'.$row_images['id'].'" class="eliminapic uk-icon-button uk-button-danger" uk-icon="icon:trash"></button>
				<br><br>
				<a href="'.$ruta.$row_images['id'].'.jpg" target="_blank">
					<img src="'.$ruta.$row_images['id'].'.jpg" class="img-responsive uk-border-rounded margen-bottom-20">
				</a>
			</div>
		</div>
';
}
?>

	</div>
</div>


<div id="config" class="modal" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-outside" type="button" uk-close></button>
		<div class="uk-modal-header">
			Configurar imagen
		</div>
		<form action="index.php" method="post">
			<input type="hidden" name="editar" value="1">
			<input type="hidden" name="seccion" value="<?=$seccion?>">
			<input type="hidden" name="id" id="cfgid" value="">

			<div class="uk-margin">
				<label for="url">Link</label>
				<input type="text" id="url" name="url" class="uk-input">
			</div>
			<div class="uk-margin uk-text-center">
				<a href="" class="uk-button uk-button-default uk-modal-close uk-button-large" tabindex="10">Cancelar</a>
				<button class="uk-button uk-button-primary uk-button-large">Guardar</button>
			</div>
		</form>
	</div>
</div>


<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>

<?php
$scripts='

	// Eliminar foto
	$(".eliminapic").click(function() {
		var id = $(this).attr(\'data-id\');
		UIkit.modal.confirm("Desea eliminar esto?").then(function() {
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&borrar=1&id="+id);
		});
	});

	var imagenesArray = [];
	$("#fileuploader").uploadFile({
		url:"../library/upload-file/php/upload.php",
		fileName:"myfile",
		multiple:true,
		maxFileCount:100,
		showDelete: "false",
		allowedTypes: "jpeg,jpg",
		maxFileSize: 6291456,
		showFileCounter: false,
		showPreview:false,
		returnType:"json",
		onSuccess:function(data){ 
			window.location = ("index.php?seccion='.$seccion.'&imagen="+data);
		}
	});

	$(".cfg").click(function(){
		cfgid=$(this).attr("data-cfgid");
		url=$(this).attr("data-url");
		$("#cfgid").val(cfgid);
		$("#url").val(url);
	})

';



?>
