<?php 
/* %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% 
						LOGIN USING EMAIL
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  */
	// Modal para logueo
	$fallo			= 0;
	$rutaMiCta		= $ruta.'mi-cuenta';
	$rutaLogin		= $ruta.'login';
	$rutaCotizar	= $ruta.'cotizar';
	$loginButton    = '
		<div class="padding-v-10 uk-width-1-1">
			<div class="uk-text-right@s">
				<a href="'.$rutaLogin.'" class="uk-button uk-button-primary">
					Inicia sesión
				</a>
			</div>
			<div class="padding-top-10 color-primary">
				Tel: 01 (800) 999-0407<!-- <a href="'.$rutaCotizar.'" style="color: #52a7a6;">COTIZA AQUI</a>-->
			</div>
		</div>
		';

	// Ventana modal de logueo
	$loginModal='
	<div id="login" uk-modal class="modal-login">
		<div class="uk-modal-dialog">
			<button class="uk-modal-close-default" type="button" uk-close></button>
			<div class="uk-modal-header">
				<div class="uk-text-center">
					<img src="'.$logo.'" style="max-height:100px;" alt="'.$Brand.'">
				</div>
			</div>
			<div class="padding-20">
				<div class="">
					<form action="'.$rutaEstaPagina.'" method="post">
						<input type="hidden" name="login" value="1">
						<label for="email">*Email:</label>
						<div class="input-container">
							<input name="email" class="uk-input input-personal" type="email" required>
						</div>
						<label for="pass">*Contrase&ntilde;a:</label>
						<div class="input-container">
							<input name="password" class="uk-input input-personal" type="password" required>
						</div>
						<div class="uk-margin-top">
							<button class="uk-button uk-button-primary uk-width-1-1">Entrar</button>
						</div>
					</form>
				</div>
				<div class="uk-text-center margen-v-20">
					<fb:login-button 
						scope="public_profile,email" 
						onlogin="checkLoginState();"
						class="fb-login-button"
						data-size="large"
						data-button-type="continue_with"
						data-show-faces="false"
						>
					</fb:login-button>
					<div class="fbstatus">
					</div>
				</div>
				<div uk-grid class="uk-text-center padding-top-50">
					<div class="uk-width-1-2">
						<div>
							¿Nuevo en el sitio?
						</div>
						<div class="uk-width-1-1">
							<div class="uk-margin-top">
								<a href="Registro" class="uk-button uk-button-default">Regístrate</a>
							</div>
						</div>
					</div>
					<div class="uk-width-1-2">
						<div>
							¿Olvidaste tu contraseña?
						</div>
						<div class="uk-width-1-1">
							<div class="uk-margin-top">
								<a href="password-recovery" class="uk-button uk-button-default">Recuperar</a>
							</div>
						</div>
					</div>
				</div>
				<div class="uk-width-1-1">
					<br>
				</div>
			</div>
		</div>
	</div>';


	// Obtener usuario
	$unombre='&nbsp;';
	if (isset($_SESSION['uid'])) {
		$uid  = $_SESSION['uid'];
		$USER = $CONEXION -> query("SELECT * FROM usuarios WHERE id = '$uid'");
		$row_USER = $USER -> fetch_assoc();
		$unombre  = $row_USER['nombre'];
		$uemail   = $row_USER['email'];
		$ulevel   = $row_USER['nivel'];
		$nombreCortoEspacio=strpos($unombre, ' ');
		$nombreCorto=($nombreCortoEspacio==0)?$unombre:substr($unombre,0,(strpos($unombre, ' ')));
		$loginModal = '';
	}else{
		if(isset($_POST['alta']) and $_POST['alta']!='') { $alta = $_POST['alta']; }
		if(isset($_POST['login']) and $_POST['login']!='') { $login = $_POST['login']; }
		if(isset($_POST['email']) and $_POST['email']!='') { $email = htmlentities($_POST['email']); }else{ $fallo=1; }
		if(isset($_POST['password']) and $_POST['password']!='') { $password = md5($_POST['password']); }else{ $fallo=1; }
		if(isset($_POST['nombre']) and $_POST['nombre']!='') { $nombre = htmlentities($_POST['nombre']); }
		if(isset($_POST['pass1']) and $_POST['pass1']!='') { $pass1 = md5($_POST['pass1']); }else{ $pass1=''; }
		if(isset($_POST['pass1']) and $_POST['pass1']!='') { $passLen = strlen($_POST['pass1']); }else{ $passLen=0; }

		if ($fallo==0) {
			// Comprobar si el usuario existe
			$USER = $CONEXION -> query("SELECT * FROM usuarios WHERE email = '$email'");
			$numUser=$USER->num_rows;

			// Si no existe, verificamos que no esté registrando
			if ($numUser>0) {
				$row_USER = $USER -> fetch_assoc();
				if ($row_USER['pass']===$password OR $row_USER['pass']=='') {
					$_SESSION['uid'] = $row_USER['id'];
					$uid=$_SESSION['uid'];
					$unombre=$row_USER['nombre'];
					$uemail=$row_USER['email'];
					$ulevel=$row_USER['nivel'];
					$nombreCortoEspacio=strpos($unombre, ' ');
					$nombreCorto=($nombreCortoEspacio==0)?$unombre:substr($unombre,0,(strpos($unombre, ' ')));
					$loginModal='';
					$mensajeClase='success';
					$mensaje='Bienvenido '.$unombre;
				}else{
					$mensajeClase='danger';
					$mensaje='<br>Contraseña incorrecta
							<form action="'.$rutaEstaPagina.'" method="post">
								<legend>Escriba sus datos de acceso</legend>
								<input type="text" class="uk-input" name="email" placeholder="Email">
								<input type="password" class="uk-input" name="password" placeholder="Contraseña">
								<button type="submit" class="uk-button uk-button-primary">Entrar</button>
							</form>';
				}
			}
		}
	}
	// Existe el usuraio
	if (isset($uid)) {
		$loginButton='
		<div class="padding-v-10 uk-width-1-1">
			<div class="uk-text-right@s">
				<a href="'.$rutaMiCta.'" class="uk-button uk-button-primary uk-text-center">
					<i class="fa fa-user"></i> &nbsp; '.$nombreCorto.'
				</a>
				<a href="logout" class="uk-button uk-button-primary uk-text-center">
					<i class="fa fa-unlock"></i> &nbsp; Salir
				</a>
			</div>
			<div class="padding-top-10 color-primary">
				Tel: 01 (800) 999-0407<!-- <a href="'.$rutaCotizar.'" style="color: #52a7a6;">COTIZA AQUI</a>-->
			</div>
		</div>
		';
		// Ventana modal de logueo
		$loginModal='';
	}
