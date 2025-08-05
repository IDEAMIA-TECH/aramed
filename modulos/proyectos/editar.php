<?php 

$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
$row_catalogo = $consulta -> fetch_assoc();




echo '
<div class="uk-width-1-1 margen-v-20">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'">'.$seccion.'</a></li>
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
		<div uk-grid class="uk-grid-small uk-child-width-1-2@m">


			<div>
				<label class="uk-text-capitalize" for="titulo">titulo</label>
				<input type="text" class="uk-input" name="titulo" value="'.$row_catalogo['titulo'].'">
			</div>
			<div>
				<label class="uk-text-capitalize" for="subtitulo">subtitulo</label>
				<input type="text" class="uk-input" name="subtitulo" value="'.$row_catalogo['subtitulo'].'">
			</div>
			<div class="uk-width-1-1">
				<label class="uk-text-capitalize" for="txt">descripción</label>
				<textarea class="editor" name="txt" id="txt">'.$row_catalogo['txt'].'</textarea>
			</div>	
			<div class="uk-width-1-1">
				<label class="uk-text-capitalize" for="video">video</label>
				<input type="text" class="uk-input" name="video" value="'.$row_catalogo['video'].'">
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