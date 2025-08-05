<?php
echo '
<div uk-grid class="uk-child-width-1-2@m uk-flex-center">
	<div>

		<div class="padding-v-20">
			<h3>Metadatos</h3>
			<div uk-grid>
				<div>
					<label for="envio" class="uk-form-label">Título del sitio</label>
				</div>
				<div class="uk-width-expand">
					<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="title" data-id="1" value="'.$rowCONSULTA['title'].'" placeholder="'.$Brand.'">
				</div>
			</div>
		</div>
		<div class="padding-v-20">
			<form action="index.php" method="post">
				<label for="envio" class="uk-form-label">Descripción del sitio</label>
				<textarea class="editarajax uk-textarea min-height-150" data-tabla="configuracion" data-campo="description" data-id="1">'.$rowCONSULTA['description'].'</textarea>
			</form>
		</div>

		<div class="padding-v-20">
			<h3>Diseño</h3>
			<div uk-grid>
				<div>
					<label for="num2" class="uk-form-label">Productos por página</label>
				</div>
				<div class="uk-width-expand">
					<input type="text" class="editarajax uk-input" id="num2"  data-tabla="'.$seccion.'" data-campo="prodspag" data-id="1" value="'.$rowCONSULTA['prodspag'].'">
				</div>
			</div>
		</div>


		<div class="margen-v-50">
			<input value="'.$rowCONSULTA['tyct1'].'" data-tabla="configuracion" data-campo="tyct1" data-id="1" class="editarajax uk-input uk-form-blank">
			<form action="index.php" method="post">
				<input type="hidden" name="seccion" value="'.$seccion.'">
				<input type="hidden" name="editartextosconformato" value="1">
				<input type="hidden" name="frame" value="politicas">
				<textarea class="editor min-height-150" name="tyc1">'.$rowCONSULTA['tyc1'].'</textarea>
				<br>
				<div class="uk-text-center">
					<button class="uk-button uk-button-primary">Guardar</button>
				</div>
			</form>
		</div>

	</div>
</div>';






