<?php
$fecha=date('m/d/Y');
?>

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
				<label for="titulo">Título corto</label>
				<input type="text" class="uk-input" name="titulo" value="" autofocus required>
			</div>
			<div class="uk-margin-top">
				<label for="subtitulo">Título largo</label>
				<input type="text" class="uk-input" name="subtitulo" value="" required>
			</div>
		</div>
		<div class="uk-width-1-2">
			<div class="uk-margin-top">
				<label for="txt">Descripción</label>
				<textarea class="editor" name="txt"></textarea>
			</div>
		</div>
		<div class="uk-width-1-1 uk-margin-top uk-text-center">
			<a href="index.php?seccion=<?=$seccion?>" class="uk-button uk-button-white uk-button-large" tabindex="10">Cancelar</a>					
			<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
		</div>
	</div>
</form>


<?php 
$scripts='
	$(function(){
		$( "#datepicker" ).datepicker({
			changeMonth: true,
			changeYear: true
		});
		$("#datepicker").datepicker( "option", "dateFormat", "yy-mm-dd" );
	});
';
?>