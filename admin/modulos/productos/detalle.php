<?php 
$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
$row_catalogo = $consulta -> fetch_assoc();
$cat=$row_catalogo['categoria'];

$fechaSQL=$row_catalogo['fecha'];
$segundos=strtotime($fechaSQL);
$fechaUI=date('m/d/Y',$segundos);


$CATEGORY = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
$row_CATEGORY = $CATEGORY -> fetch_assoc();
$catNAME=$row_CATEGORY['txt'];
$catParentID=$row_CATEGORY['parent'];

$CATEGORY = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $catParentID");
$row_CATEGORY = $CATEGORY -> fetch_assoc();
$catParent=$row_CATEGORY['txt'];

// PDF
	$pdf='';
	$pic='../img/contenido/'.$seccion.'pdf/'.$row_catalogo['pdf'];
	if($row_catalogo['pdf']!='' AND file_exists($pic)){
		$pdf.='
			<a class="uk-button uk-button-primary " href="'.$pic.'" target="_blank"><i uk-icon="download"></i> &nbsp; Descargar PDF</a> &nbsp;&nbsp;
			<button class="uk-button uk-button-danger borrarpdf"><i uk-icon="icon:trash"></i> Eliminar</button>';
	}else{
		$pdf.='<a href="#uploadficha" uk-toggle class="uk-button uk-button-primary"><i uk-icon="upload"></i> &nbsp; Subir</a>';
	}

echo '
<div class="uk-width-1-1 margen-v-20">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=all">Productos</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=categorias">categorías</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=catdetalle&cat='.$catParentID.'">'.$catParent.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=contenido&cat='.$cat.'">'.$catNAME.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=detalle&id='.$id.'" class="color-red">'.$row_catalogo['titulo'].'</a></li>
	</ul>
</div>
<div class="uk-width-1-1 uk-text-right margen-v-20">
	<a href="index.php?seccion='.$seccion.'&subseccion=nuevo&cat='.$cat.'" class="uk-button uk-button-default"><i class="fa fa-lg fa-plus"></i> &nbsp; Nuevo producto</a>
	<a href="index.php?seccion='.$seccion.'&subseccion=editar&id='.$id.'" class="uk-button uk-button-primary"><i class="fas fa-pencil-alt"></i> &nbsp; Editar</a>
	<button data-id="'.$row_catalogo['id'].'" class="eliminaprod uk-button uk-button-danger" tabindex="1"><i class="fa fa-lg fa-trash"></i> &nbsp; Eliminar producto</button> 
</div>';


echo '
<div class="uk-width-1-1 margen-v-20">
	<div class="uk-card uk-card-default uk-card-body">
		<div>
			<span class="uk-text-muted">Titulo:</span>
			'.$row_catalogo['titulo'].'
		</div>
		<div>
			<span class="uk-text-muted">Código:</span>
			'.$row_catalogo['titulo1'].'
		</div>
		<div>
			<span class="uk-text-muted">Marca:</span> ';
$marcaId=$row_catalogo['marca'];
$CONSULTAX = $CONEXION -> query("SELECT * FROM empresas WHERE id = $marcaId");
while ($row_CONSULTAX = $CONSULTAX -> fetch_assoc()) {
	echo html_entity_decode($row_CONSULTAX['titulo']);
}
echo '
		</div>
		<div class="uk-margin">
			<span class="uk-text-muted">Descripción:</span>
			'.$row_catalogo['txt'].'
		</div>

		<div class="uk-margin">
			<span class="uk-text-muted">Video de youtube</span><br>
			<a href="'.$row_catalogo['video'].'" target="_blank">'.$row_catalogo['video'].'</a>
		</div>

		<div class="uk-margin">
			<span class="uk-text-muted">Ficha técnica</span><br>
			'.$pdf.'
		</div>
		
		<div class="uk-width-1-1 uk-text-right">
			<span class="uk-text-muted">Fecha de captura:</span>
			'.$fechaUI.'
		</div>
	</div>
</div>
<div class="uk-width-1-1">
	<div class="uk-card uk-card-default uk-card-body">
		<div class="uk-width-1-1">
			<h4>SEO</h4>
			<span class="uk-text-muted">Titulo google:</span>
			'.$row_catalogo['title'].'
		</div>
		<div class="uk-width-1-1">
			<span class="uk-text-muted">Descripción google:</span>
			'.$row_catalogo['metadescription'].'
		</div>
	</div>
</div>



<div class="uk-width-1-1 margen-v-20">
	<div class="uk-card uk-card-default uk-card-body">
		<ul uk-accordion>
		    <li>
		        <a class="uk-accordion-title" href="#">Productos relacionados</a>
		        <div class="uk-accordion-content">
					<form method="post" action="index.php">
						<input type="hidden" name="seccion" value="'.$seccion.'" >
						<input type="hidden" name="subseccion" value="'.$subseccion.'" >
						<input type="hidden" name="id" value="'.$id.'" >
						<input type="hidden" name="relacionar" value="1" >
						<div uk-grid class="uk-grid-small uk-child-width-1-4@m uk-child-width-1-2@s">';
$rel = $row_catalogo['rel'];
$array=explode(',', $rel);
$CONSULTA3 = $CONEXION -> query("SELECT * FROM $seccion WHERE id != $id");
while ($row_CONSULTA3 = $CONSULTA3 -> fetch_assoc()) {
	if (in_array($row_CONSULTA3['id'], $array)) {
		$status='checked';
	}else{
		$status='';
	}
	echo '<div><label><input '.$status.' name="capa'.$row_CONSULTA3['id'].'" type="checkbox" value="'.$row_CONSULTA3['id'].'" class="uk-checkbox">&nbsp;&nbsp;'
	.$row_CONSULTA3['titulo'].
	'<label></div>'; 
}
echo '
						</div>
						<div class="uk-text-center uk-width-1-1 "style="margin-top:50px">
							<button class="uk-button uk-button-primary">Guardar</button>
						</div>
					</form>
		        </div>
		    </li>
		</ul>
    </div>
</div>



<div class="uk-width-1-1">
	<h3 class="uk-text-center">Fotografías<br><i class="uk-text-muted text-sm">Puede agregar varios archivos a la vez</i></h3>
	<div id="fileuploader">
		Cargar
	</div>
	<div uk-grid class="uk-grid-small uk-grid-match uk-child-width-1-4@m uk-child-width-1-2 sortable" data-tabla="'.$seccionpic.'">';


	$CONSULTAPIC = $CONEXION -> query("SELECT * FROM $seccionpic WHERE producto = $id ORDER BY orden,id");
	$numProds=$CONSULTAPIC->num_rows;
	while ($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()) {

		$pic='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'-sm.jpg';
		$picLg='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'-lg.jpg';
		if(file_exists($pic)){
			echo '
			<div id="'.$row_consultaPIC['id'].'">
				<div class="uk-card uk-card-default uk-card-body uk-text-center">
					<a href="'.$picLg.'" class="uk-icon-button uk-button-default" target="_blank" uk-icon="icon:image"></a> &nbsp;
					<a href="javascript:eliminaPic(picID='.$row_consultaPIC['id'].')" class="uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></a>
					<br>
					<img src="'.$pic.'" class="img-responsive uk-border-rounded margen-v-20">
				</div>
			</div>';
		}else{
			echo '
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
	</div>
</div>


<div id="uploadficha" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">Ficha técnica</h2>
		<div class="margen-bottom-50 uk-text-muted">
			Archivos tipo: PDF<br><br>
		</div>
		<div id="fileuploaderpdf">
			Cargar
		</div>
		<div class="margen-top-50 uk-text-center">
	        <button class="uk-modal-close uk-button uk-button-default uk-button-large" type="button">Cancelar</button>
    	</div>
    </div>
</div>

<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>
';



$scripts='
	// Eliminar foto
	function eliminaPic () { 
		var statusConfirm = confirm("Realmente desea eliminar esta foto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&cat='.$cat.'&borrarPic&id='.$id.'&picID="+picID);
		} 
	};

	$(document).ready(function() {
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			multiple:true,
			fileName:"myfile",
			maxFileCount:100,
			showDelete: \'false\',
			allowedTypes: "jpeg,jpg",
			maxFileSize: 50000000,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){
				window.location = (\'index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&cat='.$cat.'&id='.$id.'&position=gallery&imagen=\'+data);
			}
		});

		$("#fileuploaderpdf").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'false\',
			allowedTypes: "pdf",
			maxFileSize: 50000000,
			showFileCounter: false,
			showPreview:false,
			returnType:\'json\',
			onSuccess:function(data){
				window.location = (\'index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&cat='.$cat.'&id='.$id.'&position=pdf&imagen=\'+data);
			}
		});
	});

	// Eliminar producto
	$(".eliminaprod").click(function() {
		var id = $(this).attr(\'data-id\');
		//console.log(id);
		var statusConfirm = confirm("Realmente desea eliminar este Producto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion=contenido&borrarPod&cat='.$cat.'&id="+id);
		} 
	});


	// Borrar ficha técnica
	$(".borrarpdf").click(function() {
		var statusConfirm = confirm("Realmente desea borrar esto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&id='.$id.'&borrarpdf");
		} 
	});

	// Ordenar fotos
	$(function(){
		$("#sortable").sortable({
			update: function( event, ui ) {
				var PostData = $( "#sortable" ).sortable( "toArray");
				$.ajax({
					method: "POST",
					url: "modulos/'.$seccion.'/acciones.php",
					data: { 
						list2:PostData
					}
				})
				.done(function( msg ) {
					UIkit.notification.closeAll();
					UIkit.notification(msg);
				});
			}
		});
	})

	';

