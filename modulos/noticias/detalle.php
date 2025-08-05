<?php 
$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
$row_catalogo = $consulta -> fetch_assoc();

$picTXT='';
$consultaPIC = $CONEXION -> query("SELECT * FROM $seccionpic WHERE producto = $id ORDER BY orden,id");
$numProds=$consultaPIC->num_rows;
while ($row_consultaPIC = $consultaPIC -> fetch_assoc()) {

	$pic='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'-sm.jpg';
	$picLg='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'-lg.jpg';
	if(file_exists($pic)){
		$picTXT.='
			<div class="uk-width-1-4@l uk-width-1-2 uk-margin-bottom" id="'.$row_consultaPIC['id'].'">
				<div class="uk-card uk-card-default uk-card-body uk-text-center">
					<a href="'.$picLg.'" class="uk-icon-button uk-button-white" target="_blank" uk-icon="icon:image"></a>&nbsp;
					<a href="javascript:eliminaPic(picID='.$row_consultaPIC['id'].')" class="uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></a>
					<br>
					<img src="'.$pic.'" class="img-responsive uk-border-rounded margen-top-20"><br>
					'.$row_consultaPIC['titulo'].'
				</div>
			</div>';
	}else{
		$picTXT.='
			<div class="uk-width-1-4@l uk-width-1-2@m uk-width-1-1@s uk-margin-bottom" id="'.$row_consultaPIC['id'].'">
				<div class="uk-card uk-card-default uk-card-body uk-text-center">
					<a href="javascript:eliminaPic(picID='.$row_consultaPIC['id'].')" class="uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></a>
					<br>
					Imagen rota<br>
					<i uk-icon="icon:ban;ratio:2;"></i>
				</div>
			</div>';
	}
}

echo '
<div class="uk-width-1-1 margen-v-20">
	<ul class="uk-breadcrumb">
		<li><a href="index.php?seccion='.$seccion.'&subseccion=contenido">'.$seccion.'</a></li>
		<li><a href="index.php?seccion='.$seccion.'&subseccion=detalle&id='.$id.'" class="color-red">'.$row_catalogo['titulo'].'</a></li>
	</ul>
</div>
<div class="uk-width-1-1 uk-text-right margen-v-20">
	<a href="index.php?seccion='.$seccion.'&subseccion=nuevo&cat='.$cat.'" class="uk-button uk-button-white">Nuevo</a>
	<a href="index.php?seccion='.$seccion.'&subseccion=contenido&cat='.$cat.'" class="uk-button uk-button-primary">Regresar</a>
	<button data-id="'.$row_catalogo['id'].'" class="eliminaprod uk-button uk-button-danger" tabindex="1">Eliminar</button> 
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
					<label for="titulo">Título</label>
					<input type="text" class="uk-input" name="titulo" value="'.$row_catalogo['titulo'].'" required>
				</div>
				<div class="margen-top-20">
					<label for="lugaryfecha">Lugar y fecha</label>
					<input class="uk-input" name="lugaryfecha" value="'.$row_catalogo['lugaryfecha'].'">
				</div>
				<div class="margen-top-20">
					<label for="label1title">Etiqueta 1</label>
					<input class="uk-input" name="label1title" value="'.$row_catalogo['label1title'].'">
				</div>
				<div class="margen-top-20">
					<label for="label1link">Link de la etiqueta 1</label>
					<input class="uk-input" name="label1link" value="'.$row_catalogo['label1link'].'">
				</div>
				<div class="margen-top-20">
					<label for="label2title">Etiqueta 2</label>
					<input class="uk-input" name="label2title" value="'.$row_catalogo['label2title'].'">
				</div>
				<div class="margen-top-20">
					<label for="label2link">Link de la etiqueta 2</label>
					<input class="uk-input" name="label2link" value="'.$row_catalogo['label2link'].'">
				</div>
				<div class="margen-top-20">
					<label class="uk-text-capitalize" for="video">video</label>
					<input type="text" class="uk-input" name="video" value="'.$row_catalogo['video'].'">
				</div>

';
	if (strlen($row_catalogo['video'])>0) {
		$videoUrl=$row_catalogo['video'];
		$videoPic=$videoUrl;
		if (strpos($videoPic, 'youtube')) {
			$pos=strpos($videoPic, 'v');
			$videoPic=substr($videoPic, ($pos+2));
		}elseif (strpos($videoPic, 'youtu.be')) {
			$pos=strrpos($videoPic, '/');
			$videoPic=substr($videoPic, ($pos+1));
		}
		$pic='https://img.youtube.com/vi/'.$videoPic.'/0.jpg';
		$play='<img src="../img/design/play.png" class="uk-width-1-1 uk-position-absolute" style="top:0;left:0;margin-top:-25%;">';
		echo '
				<div>
					<div class="margen-v-20">
						<a href="'.$videoUrl.'" target="_blank" class="uk-position-relative">
							<img src="'.$pic.'" class=" max-height-300px">
							'.$play.'
						</a>
					</div>
				</div>';
	}
	echo '
			</div>
			<div class="uk-width-1-2">
				<div class="margen-top-20">
					<label for="txt">Texto largo</label>
					<textarea class="editor" name="txt">'.$row_catalogo['txt'].'</textarea>
				</div>
				<div class="margen-top-20">
					<a href="index.php?seccion='.$seccion.'&cat='.$cat.'" class="uk-button uk-button-white uk-button-large" tabindex="10">Cancelar</a>					
					<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
				</div>
			</div>
		</div>
	</form>
</div>



<div class="uk-width-1-1">
	<div>
		<div id="fileuploader">
			Cargar
		</div>
	</div>
	<div uk-grid class="uk-grid-small uk-grid-match" id="sortable2">
		'.$picTXT.'
	</div>
</div>

<div id="video" class="modal" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<a href="" class="uk-modal-close uk-close"></a>
		<form action="index.php" method="post" onsubmit="return checkForm(this);">
			<input type="hidden" name="newvideo" value="1">
			<input type="hidden" name="seccion" value="'.$seccion.'">
			<input type="hidden" name="subseccion" value="detalle">
			<input type="hidden" name="id" value="'.$id.'">
			<label for="titulo">Link <span class="uk-text-muted">Video de youtube</span></label>
			<input type="text" class="uk-input" name="titulo" value="" id="titulo" placeholder="https://www.youtube.com/watch?v=QTmSxOe1lg0">
			<br><br>
			<div class="uk-text-center">
				<a href="" class="uk-button uk-button-white uk-modal-close uk-button-large" tabindex="10">Cancelar</a>
				<input type="submit" name="send" value="Guardar" class="uk-button uk-button-primary uk-button-large">
			</div>
		</form>
	</div>
</div>




<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>

';



$scripts='
	$(".editar").click(function(){
		picid=$(this).attr("data-picid");
		titulo=$(this).attr("data-titulo");
		title=$(this).attr("data-title");
		$("#picid").val(picid);
		$("#titulo").val(titulo);
		$("#title").val(title);
		//console.log(picid+"-"+titulo);
	})

	$(document).ready(function() {
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			multiple:true,
			maxFileCount:100,
			showDelete: \'false\',
			allowedTypes: "jpeg,jpg",
			maxFileSize: 6291456,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){ 
				window.location = (\'index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&id='.$id.'&position=gallery&imagen=\'+data);
			}
		});
	});

	// Eliminar producto
	$(".eliminaprod").click(function() {
		var id = $(this).attr(\'data-id\');
		//console.log(id);
		var statusConfirm = confirm("Realmente desea eliminar este Producto?"); 
		if (statusConfirm == false) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion=contenido&borrarPod&id="+id);
		} 
	});
	';
