
<form action="index.php" class="uk-width-1-1" method="post" name="editar" onsubmit="return checkForm(this);">

	<div uk-grid>
		<div class="uk-width-1-1 margen-v-20 uk-text-left">
			<ul class="uk-breadcrumb">
				<?php
				echo '
				<li><a href="index.php?seccion='.$seccion.'&subseccion=contenido">'.$seccion.'</a></li>
				<li class="color-red">Nuevo</li>
';
				?>
			</ul>
		</div>

		<input type="hidden" name="nuevo" value="1">
		<input type="hidden" name="seccion" value="<?=$seccion?>">
		
		<div class="uk-width-1-2">
			<div class="uk-margin-top">
				<label for="titulo">Título</label>
				<input type="text" class="uk-input" name="titulo" value="" autofocus required>
			</div>
			<div class="uk-margin-top">
				<label for="subtitulo">Texto corto</label>
				<textarea class="uk-textarea min-height-150" name="subtitulo"></textarea>
			</div>
			<div class="margen-top-20">
				<label for="label1title">Etiqueta 1</label>
				<input class="uk-input" name="label1title">
			</div>
			<div class="margen-top-20">
				<label for="label1link">Link de la etiqueta 1</label>
				<input class="uk-input" name="label1link">
			</div>
			<div class="margen-top-20">
				<label for="label2title">Etiqueta 2</label>
				<input class="uk-input" name="label2title">
			</div>
			<div class="margen-top-20">
				<label for="label2link">Link de la etiqueta 2</label>
				<input class="uk-input" name="label2link">
			</div>
		</div>
		<div class="uk-width-1-2">
			<div class="margen-top-20">
				<label for="lugaryfecha">Lugar y fecha</label>
				<input class="uk-input" name="lugaryfecha">
			</div>
			<div class="uk-margin-top">
				<label for="txt">Texto largo</label>
				<textarea class="editor" name="txt"></textarea>
			</div>
		</div>
		<div class="uk-width-1-1 uk-margin-top uk-text-center">
			<a href="index.php?seccion=<?=$seccion?>&subseccion=contenido" class="uk-button uk-button-white uk-button-large" tabindex="10">Cancelar</a>					
			<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
		</div>
	</div>
</form>


<?php 
$scripts='
'; ?>