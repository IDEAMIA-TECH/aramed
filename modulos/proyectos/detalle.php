<?php 
$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
$row_catalogo = $consulta -> fetch_assoc();

$fechaSQL=$row_catalogo['fecha'];
$segundos=strtotime($fechaSQL);
$fechaUI=date('m/d/Y',$segundos);

echo '
<div class="uk-width-1-1 margen-v-20">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'">'.$seccion.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=detalle&id='.$id.'" class="color-red">'.$row_catalogo['titulo'].'</a></li>
	</ul>
</div>
<div class="uk-width-1-1 uk-text-right margen-v-20">
	<a href="index.php?seccion='.$seccion.'&subseccion=nuevo" class="uk-button uk-button-default"><i class="fa fa-lg fa-plus"></i> &nbsp; Nuevo proyecto</a>
		<a href="index.php?seccion='.$seccion.'&subseccion=editar&id='.$id.'" class="uk-button uk-button-primary"><i class="fas fa-pencil-alt"></i> &nbsp; Editar</a>
</div>



<div class="uk-width-1-1 margen-v-20">
	<div class="uk-card uk-card-default uk-card-body">
		<div uk-grid class="uk-child-width-1-2@m">
			<div>
				<div>
					<span class="uk-text-capitalize uk-text-muted">titulo</span>
					'.$row_catalogo['titulo'].'
				</div>
				<div>
					<span class="uk-text-capitalize uk-text-muted">subtitulo</span>
					'.$row_catalogo['subtitulo'].'
				</div>
				<div>
					<span class="uk-text-capitalize uk-text-muted">descripción:</span>
					'.$row_catalogo['txt'].'
				</div>
				<div class="uk-text-right">
					<span class="uk-text-muted">Fecha</span>
					'.fechaCorta($row_catalogo['fecha']).'
				</div>';
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
			<div>
				<div>
					<span class="uk-text-capitalize uk-text-muted">titulo google</span>
					'.$row_catalogo['title'].'
				</div>
				<div>
					<span class="uk-text-capitalize uk-text-muted">descripción google</span><br>
					'.$row_catalogo['metadescription'].'
				</div>
			</div>
		</div>		
	</div>
</div>';





echo '

	<div class="uk-width-1-1">
		<h3 class="uk-text-center">Fotografías<br><i class="uk-text-muted text-sm">Puede agregar varios archivos a la vez</i></h3>
		<div id="fileuploader">
			Cargar
		</div>
	</div>
	<div class="uk-width-1-1">
		<div uk-grid class="uk-child-width-1-4@l uk-child-width-1-2uk-grid-small uk-grid-match sortable" data-tabla="'.$seccionpic.'">';

		$CONSULTAPIC = $CONEXION -> query("SELECT * FROM $seccionpic WHERE producto = $id ORDER BY orden,id");
		$numProds=$CONSULTAPIC->num_rows;
		while ($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()) {

			$pic='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'-sm.jpg';
			$picLg='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'.jpg';
			if(file_exists($pic)){
				echo '
				<div class="uk-margin-bottom" id="'.$row_consultaPIC['id'].'">
					<div class="uk-card uk-card-default uk-card-body uk-text-center">
						<a href="'.$picLg.'" class="uk-icon-button uk-button-default" target="_blank" uk-icon="icon:image"></a> &nbsp;
						<a href="javascript:eliminaPic(picID='.$row_consultaPIC['id'].')" class="uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></a>
						<br>
						<img src="'.$pic.'" class="img-responsive uk-border-rounded margen-v-20">
					</div>
				</div>';
			}else{
				echo '
				<div class="uk-margin-bottom" id="'.$row_consultaPIC['id'].'">
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
	</div>
</div>';


$scripts='
	// Eliminar foto
	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&borrarPic&id='.$id.'&picID="+picID);
		} 
	};

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
				window.location = (\'index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&cat='.$cat.'&id='.$id.'&position=gallery&imagen=\'+data);
			}
		});
	});


	';
