<div class="uk-width-1-1 margen-v-20 uk-text-left">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?seccion=<?=$seccion?>&subseccion=all">Productos</a></li>

<?php
if (isset($_GET['cat'])) {
	$CATEGORIAS = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
	$row_CATEGORIAS = $CATEGORIAS -> fetch_assoc();
	$catNAME=$row_CATEGORIAS['txt'];
	echo '
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=categorias">categorías</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=catdetalle&cat='.$catParentID.'">'.$catParent.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'seccion='.$seccion.'&subseccion=contenido&cat='.$cat.'">'.$catNAME.'</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'seccion='.$seccion.'&subseccion=nuevo&cat='.$cat.'" class="color-red">Nuevo</a></li>';
}else{
	echo '
		<li><a href="index.php?seccion='.$seccion.'&subseccion=nuevo" class="color-red">Nuevo</a></li>';
}
?>


	</ul>
</div>


<form action="index.php" class="uk-width-1-1" method="post" name="editar" onsubmit="return checkForm(this);">
	<input type="hidden" name="nuevo" value="1">
	<input type="hidden" name="seccion" value="<?=$seccion?>">

	<div uk-grid class="uk-grid-small">

		<div class="uk-width-1-2">
			<label for="titulo" required>Titulo</label>
			<input type="text" class="uk-input" name="titulo" id="titulo" autofocus>
		</div>

		<div class="uk-width-1-2">
			<label for="ttulo1" required>Código</label>
			<input type="text" class="uk-input" name="titulo1" id="titulo1">
		</div>

		<div class="uk-width-1-2">
			<label for="categoria">Categoría</label>
			<div>
				<select name="categoria" data-placeholder="Seleccione una" class="chosen-select uk-select uk-width-1-1" required>
					<option value=""></option>
<?php 
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
?>
				</select>
			</div>
		</div>

		<div class="uk-width-1-2">
			<label for="marca">Marca</label>
			<div>
				<select name="marca" data-placeholder="Seleccione una" class="chosen-select uk-select uk-width-1-1" required>
					<option value=""></option>
				<?php 
				$CONSULTA = $CONEXION -> query("SELECT * FROM empresas WHERE titulo != '' ORDER BY titulo");
				while ($row_CONSULTA = $CONSULTA -> fetch_assoc()) {
					if (isset($cat) AND $cat==$row_CONSULTA['id']) {
						$estatus='selected';
					}else{
						$estatus='';
					}
							$thisName=html_entity_decode($row_CONSULTA['titulo']);

					echo '
						<option value="'.$row_CONSULTA['id'].'" '.$estatus.'>'.$thisName.'</option>';
				}
				?>

				</select>
			</div>
		</div>

		<div class="uk-width-1-1">
			<label for="txt">Descripción</label>
			<textarea class="editor" name="txt" id="txt"></textarea>
		</div>
		<div class="uk-width-1-1">
			<label for="video">Link del video de youtube</label>
			<input type="text" class="uk-input" name="video" id="video">
		</div>


		<div class="uk-width-1-1">
			<p>SEO</p>
		</div>
		<div class="uk-width-1-1">
			<label for="title">titulo google</label>
			<input type="text" class="uk-input" name="title" id="title" placeholder="Término como alguien nos buscaría">
		</div>
		<div class="uk-width-1-1">
			<label for="metadescription">descripción google</label>
			<textarea class="uk-textarea" name="metadescription" id="metadescription" placeholder="Descripción explícita para que google muestre a quienes nos vean en las búsquedas"></textarea>
		</div>
		<div class="uk-width-1-1 uk-text-center">
			<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
		</div>

	</div>
</form>

<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>


<?php 
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
?>