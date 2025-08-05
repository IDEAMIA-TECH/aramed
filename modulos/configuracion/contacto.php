<?php
echo'
<div class="uk-width-1-1">
	<div class="uk-container uk-container-xsmall">
		<div>
			<div class="uk-margin">
				<div uk-grid>
					<div>
						<label class="uk-form-label">Teléfono</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="telefono" data-id="1" value="'.$rowCONSULTA['telefono'].'">
					</div>
				</div>
			</div>

			<div class="margen-v-50">
				<h3>Redes sociales</h3>
			</div>

			<div class="uk-margin">
				<div uk-grid>
					<div>
						<label for="facebook" class="uk-form-label">Facebook</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="facebook" data-id="1" value="'.$rowCONSULTA['facebook'].'">
					</div>
				</div>
			</div>
			<div class="uk-margin">
				<div uk-grid>
					<div>
						<label for="linkedin" class="uk-form-label">linkedin</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="linkedin" data-id="1" value="'.$rowCONSULTA['linkedin'].'">
					</div>
				</div>
			</div>
			<div class="uk-margin">
				<div uk-grid>
					<div>
						<label for="instagram" class="uk-form-label">Instagram</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="instagram" data-id="1" value="'.$rowCONSULTA['instagram'].'">
					</div>
				</div>
			</div>
			<div class="uk-margin">
				<div uk-grid>
					<div>
						<label for="youtube" class="uk-form-label">YouTube</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="youtube" data-id="1" value="'.$rowCONSULTA['youtube'].'">
					</div>
				</div>
			</div>

		</div>
		<div>

			<div class="margen-v-50">
				<h3>Envío de correo</h3>
			</div>

			<div class="uk-margin">
				<div uk-grid>
					<div>
						<label for="destinatario1" class="uk-form-label">Destinatario 1</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="destinatario1" data-id="1" value="'.$rowCONSULTA['destinatario1'].'" placeholder="Obligatorio">
					</div>
				</div>
			</div>
			<div class="uk-margin">
				<div uk-grid>
					<div>
						<label for="destinatario2" class="uk-form-label">Destinatario 2</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="destinatario2" data-id="1" value="'.$rowCONSULTA['destinatario2'].'" placeholder="Opcional">
					</div>
				</div>
			</div>

			<div class="uk-width-1-1@m uk-margin">
				<div uk-grid>
					<div>
						<label for="remitente" class="uk-form-label">Remitente</label>
					</div>
					<div class="uk-width-expand">
						<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="remitente" data-id="1" value="'.$rowCONSULTA['remitente'].'">
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
';


$obsoleto='
		<div class="uk-margin">
			<div uk-grid>
				<div>
					<label for="remitente" class="uk-form-label">Remitente</label>
				</div>
				<div class="uk-width-expand">
					<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="remitente" data-id="1" value="'.$rowCONSULTA['remitente'].'" placeholder="">
				</div>
			</div>
		</div>
		<div class="uk-margin">
			<div uk-grid>
				<div>
					<label for="pass" class="uk-form-label">Contraseña</label>
				</div>
				<div class="uk-width-expand">
					<input type="password" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="pass" data-id="1" value="'.$rowCONSULTA['pass'].'">
				</div>
			</div>
		</div>
		<div class="uk-margin">
			<div uk-grid>
				<div>
					<label for="server" class="uk-form-label">Servidor</label>
				</div>
				<div class="uk-width-expand">
					<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="server" data-id="1" value="'.$rowCONSULTA['server'].'" placeholder="">
				</div>
			</div>
		</div>
		<div class="uk-margin">
			<div uk-grid>
				<div>
					<label for="port" class="uk-form-label">Puerto</label>
				</div>
				<div class="uk-width-expand">
					<input type="text" class="editarajax uk-input" data-tabla="'.$seccion.'" data-campo="port" data-id="1" value="'.$rowCONSULTA['port'].'" placeholder="">
				</div>
			</div>
		</div>';