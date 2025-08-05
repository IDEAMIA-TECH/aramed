<?php 
echo '
<div class="uk-width-1-1">
	<div class="uk-container">
		<div class="uk-width-1-1">
			<ul class="uk-breadcrumb margen-v-20">
				<li><a href="index.php?seccion='.$seccion.'" class="color-red">Marcas</a></li>
			</ul>
		</div>
	</div>
</div>


';

$picTXT='';
$consultaPIC = $CONEXION -> query("SELECT * FROM $seccion ORDER BY orden");
$numProds=$consultaPIC->num_rows;

while ($row_consultaPIC = $consultaPIC -> fetch_assoc()) {
	$estatusIcon=($row_consultaPIC['estatus']==1)?'off uk-text-muted':'on uk-text-primary';

	$pic='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'.png';
	$picLg='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'.png';
	if(file_exists($pic)){

		$picTXT.='

				<div class="uk-width-1-4@l uk-width-1-2@m uk-width-1-1@s uk-margin-bottom" id="'.$row_consultaPIC['id'].'">
					<div class="uk-card uk-card-default uk-card-body uk-text-center">
						<span class="estatuschange" data-estatus="'.$row_consultaPIC['estatus'].'" id="button'.$row_consultaPIC['id'].'" data-id="'.$row_consultaPIC['id'].'"><i class="fa fa-lg fa-toggle-'.$estatusIcon.'"></i></span> &nbsp;&nbsp;
						<a href="'.$picLg.'" class="uk-icon-button uk-button-default" target="_blank" uk-icon="icon:image"></a> &nbsp;
						<a href="javascript:eliminaPic(picID='.$row_consultaPIC['id'].')" class="uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></a>
						<br>
						<img src="'.$pic.'" class="img-responsive uk-border-rounded margen-top-20"><br>
						'.$row_consultaPIC['url'].'
								<input type="text" class="url uk-input" data-id="'.$row_consultaPIC['id'].'" value="'.$row_consultaPIC['titulo'].'" placeholder="Nombre">
									
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
<div class="uk-width-1-1">
	<div id="fileuploader">
		Cargar
	</div>
</div>
<div class="uk-width-1-1 uk-text-center">
	<div uk-grid class="uk-grid-small uk-grid-match" id="sortable2">
		'.$picTXT.'
	</div>
</div>

<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>


<div class="margen-v-50">
</div>

<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>';


$scripts='
	$(document).ready(function() {
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'false\',
			allowedTypes: "png",
			maxFileSize: 6291456,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){ 
				window.location = (\'index.php?seccion='.$seccion.'&position=gallery&imagen=\'+data);
			}
		});
	});

	
	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&borrarPic&id="+picID);
		} 
	};



	$(".estatuschange").click(function(){
		var id = $(this).attr("data-id");
		var estatus = $(this).attr("data-estatus");

		if(estatus==0) {
			estatus=1;
			$("#button" +id + " > i ").addClass("fa-toggle-off");
			$("#button" +id + " > i ").addClass("uk-text-muted");
			$("#button" +id + " > i ").removeClass("fa-toggle-on");
			$("#button" +id + " > i ").removeClass("uk-text-primary");
		}else{
			estatus=0;
			$("#button" +id + " > i ").addClass("fa-toggle-on");
			$("#button" +id + " > i ").addClass("uk-text-primary");
			$("#button" +id + " > i ").removeClass("fa-toggle-off");
			$("#button" +id + " > i ").removeClass("uk-text-muted");
		}
		$(this).attr("data-estatus",estatus);
		console.log(estatus);

		$.ajax({
			method: "POST",
			url: "modulos/'.$seccion.'/acciones.php",
			data: { 
				id: id,
				estatuschange: 1,
				estatus: estatus
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);
		});

	})


	$(".url").keypress(function(e) {
		//console.log("1");
		if(e.which == 13) {
			var url = $(this).val();
			var id = $(this).attr("data-id");
			$.ajax({
				method: "POST",
				url: "modulos/'.$seccion.'/acciones.php",
				data: { 
					editaurl : 1,
					id : id,
					url : url
				}
			})
			.done(function( msg ) {
				UIkit.notification.closeAll();
				UIkit.notification(msg);
			});
		}
	});
';

