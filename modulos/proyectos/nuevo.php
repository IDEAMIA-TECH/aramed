
<div class="uk-width-1-1 margen-v-20 uk-text-left">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<?php
		echo '
		<li><a href="index.php?seccion='.$seccion.'">'.$seccion.'</a></li>
		<li class="color-red">Nuevo</li>';
		?>
	</ul>
</div>


<form action="index.php" class="uk-width-1-1" method="post" name="editar" onsubmit="return checkForm(this);">
	<input type="hidden" name="nuevo" value="1">
	<input type="hidden" name="seccion" value="<?=$seccion?>">

	<div uk-grid class="uk-grid-small uk-child-width-1-2@m">


		<div>
			<label class="uk-text-capitalize" for="titulo">título</label>
			<input type="text" class="uk-input" name="titulo" value="">
		</div>
		<div>
			<label class="uk-text-capitalize" for="subtitulo">subtítulo</label>
			<input type="text" class="uk-input" name="subtitulo" value="">
		</div>
		<div class="uk-width-1-1">
			<label class="uk-text-capitalize" for="txt">descripción</label>
			<textarea class="editor" name="txt" id="txt"></textarea>
		</div>
		<div class="uk-width-1-1">
			<label class="uk-text-capitalize" for="video">video de youtube</label>
			<input type="text" class="uk-input" name="video" value="">
		</div>
		<div class="uk-width-1-1">
			<label class="uk-text-capitalize" for="title">título google</label>
			<input type="text" class="uk-input" name="title" value="">
		</div>
		<div class="uk-width-1-1">
			<label class="uk-text-capitalize" for="metadescription">descripción google</label>
			<textarea class="uk-textarea" name="metadescription"></textarea>
		</div>
		<div class="uk-width-1-1 uk-text-center">
			<a href="index.php?rand=<?=rand(1,1000)?>&seccion=<?=$seccion?>&subseccion=contenido&cat=<?=$cat?>" class="uk-button uk-button-default uk-button-large" tabindex="10">Cancelar</a>					
			<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
		</div>
	</div>
</form>

<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>


<?php $scripts='
$(function(){
	$("#datepicker").datepicker();
	$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
});
'; ?>