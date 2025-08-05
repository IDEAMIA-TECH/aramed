<?php
$i=1;
echo '
<h3 class="uk-text-center">Imagen parallax</h3>
<div uk-grid class="uk-child-width-1-2">
	<div class="margen-top-50 uk-text-center">
		Archivos tipo: JPG de 1900px x 1000px<br><br>';
		$pic=$rutaFinal.$rowCONSULTA['imagen'.$i];
		if(strlen($rowCONSULTA['imagen'.$i])>0 AND file_exists($pic)){
			echo '

				<div class="uk-flex uk-flex-middle">
					<div class="uk-panel uk-text-center uk-width-1-1">
						<a href="'.$pic.'" target="_blank">
							<img src="'.$pic.'">
						</a><br><br>
						<button class="uk-button uk-button-danger uk-button-large borrarpic" data-campo="'.$i.'"><i uk-icon="icon:trash"></i> Eliminar</button>
					</div>
				</div>';
		}else{
			echo '
				<div class="uk-flex uk-flex-middle">
					<div class="uk-panel uk-text-center uk-width-1-1">
						<p class="uk-scrollable-box">
							<i uk-icon="icon:image;ratio:5;"></i><br><br>
						</p>
					</div>
				</div>';
		}
		echo '
	</div>';

	echo '
	<div class="uk-flex uk-flex-middle">
		<div class="uk-width-1-1">
			<div id="fileuploader">
				Cargar
			</div>
		</div>
	</div>
</div>';

	if (strlen($rowCONSULTA['video'])>0) {
		$videoUrl=$rowCONSULTA['video'];
		$videoPic=$videoUrl;
		if (strpos($videoPic, 'youtube')) {
			$pos=strpos($videoPic, 'v');
			$videoPic=substr($videoPic, ($pos+2));
		}elseif (strpos($videoPic, 'youtu.be')) {
			$pos=strrpos($videoPic, '/');
			$videoPic=substr($videoPic, ($pos+1));
		}
		$pic='https://img.youtube.com/vi/'.$videoPic.'/0.jpg';

		$picPersonal=$rutaFinal.$rowCONSULTA['imagen5'];
		if(strlen($rowCONSULTA['imagen'.$i])>0 AND file_exists($picPersonal)){
			$pic=$picPersonal;
		}
		$play='<img src="../img/design/play.png" class="uk-width-1-1 uk-position-absolute" style="top:0;left:0;margin-top:-25%;">';
		$videoHtml = '
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
	<h3 class="uk-text-center">Video</h3>
	<div class="uk-container uk-container-xsmall">
		<div>
			<label>Link de youtube</label>
			<input type="text" class="editarajax uk-input" data-tabla="configuracion" data-campo="video" data-id="1" value="'.$rowCONSULTA['video'].'" id="video">
		</div>
		<div>
			<label>Texto</label>
			<input type="text" class="editarajax uk-input" data-tabla="configuracion" data-campo="videotxt" data-id="1" value="'.$rowCONSULTA['videotxt'].'">
		</div>
	</div>
	';


$i=5;
echo '
	<div class="uk-container uk-container-xsmall">
		<div class="uk-text-center" uk-lightbox>
			'.$videoHtml.'
		</div>
		<div class="uk-width-1-1">
			<h3 class="uk-text-center">Imagen vista previa</h3>
			<div id="fileuploader'.$i.'">
				Cargar
			</div>
		</div>
	</div>';

$scripts.='
	$(document).ready(function() {
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'false\',
			allowedTypes: "jpg,jpeg,png,gif",
			maxFileSize: 6291456,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){ 
				window.location = (\'index.php?seccion='.$seccion.'&frame='.$frame.'&campo=imagen1&fileuploaded=\'+data);
			}
		});

		$("#fileuploader5").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'false\',
			allowedTypes: "jpg,jpeg,png,gif",
			maxFileSize: 6291456,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){ 
				window.location = (\'index.php?seccion='.$seccion.'&frame='.$frame.'&campo=imagen5&fileuploaded=\'+data);
			}
		});
	});	




	// Borrar imagen
	$(".borrarpic").click(function() {
		var campo = $(this).attr("data-campo");
		var statusConfirm = confirm("Realmente desea borrar esto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&frame='.$frame.'&borrarpic=1&id='.$id.'&campo=imagen"+campo);
		} 
	});


	$("#video").focusout(function(){
		setTimeout(function(){
			window.location = ("index.php?rand='.rand(1,9999).'&seccion='.$seccion.'&frame='.$frame.'");
		},1000);
	});
';