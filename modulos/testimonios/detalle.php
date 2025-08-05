<?php 
//$dominio='//localhost/sites/cleaningbrands/site';

$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
$row_catalogo = $consulta -> fetch_assoc();

$fecha=$row_catalogo['fecha'];

echo '
<div class="uk-width-1-2@s margen-v-20">
	<ul class="uk-breadcrumb">
		<li><a href="index.php?seccion='.$seccion.'&subseccion=contenido">'.$seccion.'</a></li>
		<li><a href="index.php?seccion='.$seccion.'&subseccion=detalle&id='.$id.'" class="color-red">'.$row_catalogo['titulo'].'</a></li>
	</ul>
</div>
<div class="uk-width-1-2@s uk-text-right margen-v-20">
	<a href="index.php?seccion='.$seccion.'&subseccion=nuevo&cat='.$cat.'" class="uk-button uk-button-white">Nuevo</a>
	<a href="index.php?seccion='.$seccion.'&subseccion=contenido&cat='.$cat.'" class="uk-button uk-button-primary">Regresar</a>
</div>


<div class="uk-width-1-1 margen-top-20 uk-form">
	<form action="index.php" method="post" enctype="multipart/form-data" name="datos" onsubmit="return checkForm(this);">
		<input type="hidden" name="editar" value="1">
		<input type="hidden" name="seccion" value="'.$seccion.'">
		<input type="hidden" name="subseccion" value="detalle">
		<input type="hidden" name="cat" value="'.$cat.'">
		<input type="hidden" name="id" value="'.$id.'">
		<div uk-grid>
			<div class="uk-width-1-2">
				<div class="margen-top-20">
					<label for="titulo">Nombre</label>
					<input type="text" class="uk-input" name="titulo" value="'.$row_catalogo['titulo'].'" required>
				</div>
				<div class="margen-top-20">
					<label for="email">Email</label>
					<input type="text" class="uk-input" name="email" value="'.$row_catalogo['email'].'" required>
				</div>
				<div class="uk-margin-top">
					<label for="fecha">Fecha</label>
					<input type="date" name="fecha" class="uk-input" value="'.$fecha.'">
				</div>
			</div>
			<div class="uk-width-1-2">
				<div class="margen-top-20">
					<label for="txt">Testimonio</label>
					<textarea class="editor" name="txt">'.$row_catalogo['txt'].'</textarea>
				</div>
			</div>
			<div class="uk-width-1-1 margen-top-20 uk-text-center">
				<a href="index.php?seccion='.$seccion.'&cat='.$cat.'" class="uk-button uk-button-white uk-button-large" tabindex="10">Cancelar</a>					
				<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
			</div>
		</div>
	</form>
</div>


<div class="uk-width-1-2@s padding-v-50">
	<div class="uk-text-muted">
		Archivos tipo: JPG
	</div>
	<div id="fileuploader">
		Cargar
	</div>
</div>



<div class="uk-width-1-2 uk-flex-center uk-flex uk-flex-middle">';
	$pic='../img/contenido/'.$seccion.'/'.$row_catalogo['imagen'];
	if(file_exists($pic) AND strlen($row_catalogo['imagen'])>0){
		echo '
		<div class="uk-card uk-card-default uk-card-body uk-text-center">
			<a href="'.$pic.'" class="uk-icon-button uk-button-default" target="_blank" uk-icon="icon:image"></a> &nbsp;
			<a href="javascript:eliminaPic()" class="uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></a>
			<br>
			<img src="'.$pic.'" class="uk-border-rounded margen-v-20">
		</div>';
	}else{
		echo '
		<div>
			<i uk-icon="icon:warning;ratio:4;"></i>
			<br>
			Sin imagen
		</div>';
	}
	echo '
	</div>
</div>
';


$scripts='
	$(document).ready(function() {
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'false\',
			allowedTypes: "jpeg,jpg",
			maxFileSize: 6291456,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){
				window.location = (\'index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&id='.$id.'&imagen=\'+data);
			}
		});
	});

	// Eliminar foto
	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion='.$subseccion.'&id='.$id.'&borrarPic=1");
		} 
	};


	';



