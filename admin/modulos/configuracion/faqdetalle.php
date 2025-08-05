<?php
$faq = $CONEXION -> query("SELECT * FROM faq WHERE id = $id");
$row_catalogo = $faq -> fetch_assoc();

echo '

<div class="uk-width-1-1 margen-v-50">
	<form action="index.php" method="post" name="datos" onsubmit="return checkForm(this);">
		<input type="hidden" name="editar" value="1">
		<input type="hidden" name="seccion" value="'.$seccion.'">
		<input type="hidden" name="frame" value="'.$frame.'">
		<input type="hidden" name="id" value="'.$id.'">
		<div class="uk-form uk-form-row margen-top-20 uk-width-1-1">
			<label for="pregunta">Pregunta</label>
			<input type="text" class="uk-input" name="pregunta" value="'.$row_catalogo['pregunta'].'" autofocus>
		</div>
		<div class="uk-form uk-form-row margen-top-20 uk-width-1-1">
			<label for="respuesta">Respuesta</label>
			<textarea class="editor" name="respuesta">'.$row_catalogo['respuesta'].'</textarea>
		</div>
		<div class="uk-form uk-form-row margen-top-20 uk-width-1-1 uk-text-center">
			<button name="send" class="uk-button uk-button-primary uk-button-large">Guardar</button>
		</div>
	</form>
</div>



<div>
	<div id="buttons">
		<a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&frame=faqnuevo" class="uk-icon-button uk-button-primary uk-box-shadow-large" uk-icon="icon:plus;ratio:1.4;"></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>';
