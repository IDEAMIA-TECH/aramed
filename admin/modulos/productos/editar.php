<?php 

$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
$row_catalogo = $consulta -> fetch_assoc();
$cat=$row_catalogo['categoria'];
$marca=$row_catalogo['marca'];


$CATEGORY = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
$row_CATEGORY = $CATEGORY -> fetch_assoc();
$catNAME=$row_CATEGORY['txt'];
$catParentID=$row_CATEGORY['parent'];

$CATEGORY = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $catParentID");
$row_CATEGORY = $CATEGORY -> fetch_assoc();
$catParent=$row_CATEGORY['txt'];


echo '
<div class="uk-width-1-1 margen-v-20">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=all">Productos</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=categorias">Categorías</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=catdetalle&cat='.$catParentID.'">'.$catParent.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=contenido&cat='.$cat.'">'.$catNAME.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=detalle&id='.$id.'">'.$row_catalogo['titulo'].'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=editar&id='.$id.'" class="color-red">Editar</a></li>
	</ul>
</div>

<div class="uk-width-1-1 margen-top-20 uk-form">
	<form action="index.php" method="post" enctype="multipart/form-data" name="datos" onsubmit="return checkForm(this);">
		<input type="hidden" name="editar" value="1">
		<input type="hidden" name="seccion" value="'.$seccion.'">
		<input type="hidden" name="subseccion" value="detalle">
		<input type="hidden" name="cat" value="'.$cat.'">
		<input type="hidden" name="id" value="'.$id.'">
		<div uk-grid class="uk-grid-small">
			<div class="uk-width-1-2@s">
				<label for="titulo">Título</label>
				<input type="text" class="uk-input" name="titulo" value="'.$row_catalogo['titulo'].'" autofocus required>
			</div>
			<div class="uk-width-1-2@s">
				<label for="titulo1">Código</label>
				<input type="text" class="uk-input" name="titulo1" value="'.$row_catalogo['titulo1'].'" autofocus required>
			</div>
			<div class="uk-width-1-2@s">
				<label class="uk-text-capitalize" for="categoria">Categoría</label>
				<div>
					<select name="categoria" data-placeholder="Seleccione una" class="chosen-select uk-select uk-width-1-1" required>
						<option value=""></option>';
$CONSULTA = $CONEXION -> query("SELECT * FROM productoscat WHERE parent = 0 ORDER BY txt");
while ($row_CONSULTA = $CONSULTA -> fetch_assoc()) {
	$parentId=$row_CONSULTA['id'];
	$parentTxt=$row_CONSULTA['txt'];
	echo '
						<optgroup label="'.$parentTxt.'">';
	$CONSULTA1 = $CONEXION -> query("SELECT * FROM productoscat WHERE parent = $parentId ORDER BY txt");
	while ($row_CONSULTA1 = $CONSULTA1 -> fetch_assoc()) {
		if (isset($cat) AND $cat==$row_CONSULTA1['id']) {
			$estatus='selected';
		}else{
			$estatus='';
		}
		echo '
							<option value="'.$row_CONSULTA1['id'].'" '.$estatus.'>'.$row_CONSULTA1['txt'].'</option>';
	}
	echo '
						</optgroup>';
}

						echo '
					</select>
				</div>
			</div>
			<div class="uk-width-1-2@s">
				<label class="uk-text-capitalize" for="marca">marca</label>
				<div>
					<select name="marca" data-placeholder="Seleccione una" class="chosen-select uk-select uk-width-1-1" required>
						<option value=""></option>';

					 
					$CONSULTA = $CONEXION -> query("SELECT * FROM empresas WHERE titulo != '' ORDER BY titulo");
					while ($row_CONSULTA = $CONSULTA -> fetch_assoc()) {
						if (isset($marca) AND $marca==$row_CONSULTA['id']) {
							$estatus='selected';
						}else{
							$estatus='';
						}
								$thisName=html_entity_decode($row_CONSULTA['titulo']);

						echo '
						<option value="'.$row_CONSULTA['id'].'" '.$estatus.'>'.$thisName.'</option>';
					}
				echo '

					</select>
				</div>
			</div>
			<div class="uk-width-1-1">
				<label for="txt">Primera parte del texto</label>
				<textarea class="editor" name="txt" id="txt">'.$row_catalogo['txt'].'</textarea>
			</div>
			<div class="uk-width-1-1">
				<label for="video">Pegar el link del video</label>
				<input type="text" class="uk-input" name="video" value="'.$row_catalogo['video'].'">
			</div>
			<div class="uk-width-1-1">
				<h3>SEO</h3>
			</div>
			<div class="uk-width-1-1">
				<label class="uk-text-capitalize" for="title">titulo google</label>
				<input type="text" class="uk-input" name="title" value="'.$row_catalogo['title'].'">
			</div>
			<div class="uk-width-1-1">
				<label class="uk-text-capitalize" for="metadescription">descripción google</label>
				<textarea class="uk-textarea" name="metadescription">'.$row_catalogo['metadescription'].'</textarea>
			</div>
			<div class="uk-width-1-1 uk-text-center">
				<a href="index.php?seccion='.$seccion.'&subseccion=detalle&id='.$id.'" class="uk-button uk-button-default uk-button-large" tabindex="10">Cancelar</a>					
				<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
			</div>
		</div>
	</form>
</div>

<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>

';

$scripts='
</script>



<link rel="stylesheet" href="../library/chosen/chosen.css">
<style type="text/css" media="all">
	/* fix rtl for demo */
	.chosen-rtl .chosen-drop { left: -9000px; }
</style>

<script src="../library/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="../library/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
<script>
	var config = {
		".chosen-select"           : {},
		".chosen-select-deselect"  : {allow_single_deselect:true},
		".chosen-select-no-single" : {disable_search_threshold:10},
		".chosen-select-no-results": {no_results_text:"Oops, nothing found!"},
		".chosen-select-width"     : {width:"95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
	$(".chosen-select").change(function(){
		$("#agregar").attr("disabled",false);
	});
	$("#agregar").click(function(){
		num = $("#num").val();
		num++;
		$("#num").val(num);
		$("#siguiente").attr("disabled",false);
		valorID = $(".chosen-select").val();
		valorName = $(".chosen-single").text();
		$(".result").append("Hi");
	});
';