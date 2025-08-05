
<form action="index.php" class="uk-width-1-1" method="post" name="editar" onsubmit="return checkForm(this);">
	<input type="hidden" name="nuevo" value="1">
	<input type="hidden" name="seccion" value="<?=$seccion?>">
	<input type="hidden" name="frame" value="faqdetalle">
	
	<div class="uk-width-medium-2-3 uk-width-small-1-1 uk-container-center">
		<div class="uk-form-row">
			<label for="pregunta">Pregunta</label>
			<input type="text" class="uk-input" name="pregunta" autofocus>
		</div>
		<div class="uk-form-row">
			<label for="respuesta">Respuesta</label>
			<textarea class="editor uk-width-1-1" name="respuesta"></textarea>
		</div>
		<div class="uk-width-1-1 uk-text-center margen-top-20">
			<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
		</div>
	</div>
</form>




<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>