<?php

echo '
	<div class="margen-top-50 uk-text-center">
		Archivos tipo: JPG de 900px x 500px<br><br>
		<div uk-grid class="uk-child-width-1-3@s">';
	for ($i=2; $i < 5; $i++) { 
		$pic=$rutaFinal.$rowCONSULTA['imagen'.$i];
		if(strlen($rowCONSULTA['imagen'.$i])>0 AND file_exists($pic)){
			echo '
				<div>
					<div class="uk-panel uk-text-center">
						<a href="'.$pic.'" target="_blank">
							<img src="'.$pic.'">
						</a><br><br>
						<button class="uk-button uk-button-danger uk-button-large borrarpic" data-campo="'.$i.'"><i uk-icon="icon:trash"></i> Eliminar</button>
					</div>
				</div>';
		}else{
			echo '
				<div>
					<div class="uk-panel uk-text-center">
						<p class="uk-scrollable-box">
							<a href="#modalupload" uk-toggle data-i="'.$i.'" class="subirimagen">
								<i uk-icon="icon:image;ratio:5;"></i><br><br>
								<i uk-icon="icon:upload;ratio:2;"></i> &nbsp; Subir imagen<br><br>
							</a>
						</p>
					</div>
				</div>';
		}
	}
		echo '
		</div>
	</div>';

	echo '
	<div id="modalupload" uk-modal>
		<div class="uk-modal-dialog uk-modal-body">
			<button class="uk-modal-close-default" type="button" uk-close></button>
			<div class="uk-width-1-1">
				<input type="hidden" id="imagencampo">
				<div id="fileuploader">
					Cargar
				</div>
			</div>
		</div>
	</div>';


echo '
<div>
	<form action="index.php" method="post">
		<input type="hidden" name="seccion" value="'.$seccion.'">
		<input type="hidden" name="editartextosconformato" value="1">
		<input type="hidden" name="frame" value="about">
		
		<div uk-grid class="uk-grid-large">

			<div class="uk-width-1-2@l margen-v-50 uk-text-left">
				Acerca de
				<textarea class="editor min-height-150" name="about1">'.$rowCONSULTA['about1'].'</textarea>
			</div>

			<div class="uk-width-1-2@l margen-v-50 uk-text-left">
				Valores
				<textarea class="editor min-height-150" name="about2">'.$rowCONSULTA['about2'].'</textarea>
			</div>

			<div class="uk-width-1-2@l margen-v-50 uk-text-left">
				Misión
				<textarea class="editor min-height-150" name="about3">'.$rowCONSULTA['about3'].'</textarea>
			</div>

			<div class="uk-width-1-2@l margen-v-50 uk-text-left">
				Visión
				<textarea class="editor min-height-150" name="about4">'.$rowCONSULTA['about4'].'</textarea>
			</div>
			<div class="uk-width-1-1 uk-text-center">
				<button class="uk-button uk-button-primary uk-button-large">Guardar</button>
			</div>
		</div>
	</form>
</div>';



$scripts.='
	$(document).ready(function() {
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'false\',
			allowedTypes: "jpg,jpeg",
			maxFileSize: 6291456,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){
				var campo = $("#imagencampo").val()
				window.location = (\'index.php?seccion='.$seccion.'&frame='.$frame.'&campo=imagen\'+campo+\'&fileuploaded=\'+data);
			}
		});
	});	

	$(".subirimagen").click(function(){
		var i = $(this).attr("data-i")
		$("#imagencampo").val(i);
	});


	// Borrar imagen
	$(".borrarpic").click(function() {
		var campo = $(this).attr("data-campo");
		var statusConfirm = confirm("Realmente desea borrar esto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&frame='.$frame.'&borrarpic=1&campo=imagen"+campo);
		} 
	});

';