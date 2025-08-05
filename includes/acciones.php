<?php
	session_start();
	include 'connection.php';
	include 'widgets.php';
	$msjClase = 'danger';
	$msjIcon  = 'warning';
	$msjTxt   = '<br>Error';
	$xtras    = '';
	$fallo    = 1;


//    Contacto                           
	if (isset($_POST['contacto'])) {
		$fallo = 0;
		// Obtener variables
		if(isset($_POST['nombre']) 	and $_POST['nombre']!='' ){ 	$nombre=htmlentities($_POST['nombre']); }else{ $nombre=false; $fallo=1; $msjTxt.='<br>Falta nombre'; }
		if(isset($_POST['apellido']) 	and $_POST['apellido']!='' ){ 	$apellido=htmlentities($_POST['apellido']); }else{ $apellido=false; $fallo=1; $msjTxt.='<br>Falta apellido'; }
		if(isset($_POST['telefono']) and $_POST['telefono']!='' ){ 	$telefono=htmlentities($_POST['telefono']); }else{ $telefono=false; $fallo=1; $msjTxt.='<br>Falta telefono'; }
		if(isset($_POST['email']) and $_POST['email']!='' ){ 	$email=htmlentities($_POST['email']); }else{ $email=false; $fallo=1; $msjTxt.='<br>Falta email'; }
		if(isset($_POST['comentarios']) and $_POST['comentarios']!='' ){ 	$comentarios=htmlentities($_POST['comentarios']); }else{ $comentarios=false; $fallo=1; $msjTxt.='<br>Falta comentarios'; }

		if ($fallo==0) {
			$sendMail     = 1;
			$asuntoCorreo = 'Formulario de contacto';
			$cuerpoCorreo = '
				<div style="width:700px;font-size:18;text-align:center;padding-top:50px;padding-bottom:50px;">
					Nombre: <b>'.$nombre.'</b><br><br>
					Apellido: <b>'.$apellido.'</b><br><br>
					Telefono: <b>'.$telefono.'</b><br><br>
					Email: <b>'.$email.'</b><br><br>
					Comentarios: <b>'.$comentarios.'</b><br><br><br>
					<div>'.fechaDisplay($hoy).'<div>
				</div>';
		}
	}

//    Enviar cotizacion                  
	if (isset($_POST['enviarcotizacion'])) {
		$fallo = 0;
		// Obtener variables
		if (isset($_POST['institucion']) AND strlen($_POST['institucion'])>0) { $institucion=htmlentities($_POST['institucion']); }else{ $institucion=''; $fallo=1; $msjTxt.='<br>Falta institucion'; }
		if (isset($_POST['escuela']) AND strlen($_POST['escuela'])>0) { $escuela=htmlentities($_POST['escuela']); }else{ $escuela=''; $fallo=1; $msjTxt.='<br>Falta escuela'; }
		if (isset($_POST['campus']) AND strlen($_POST['campus'])>0) { $campus=htmlentities($_POST['campus']); }else{ $campus=''; $fallo=1; $msjTxt.='<br>Falta campus'; }
		if (isset($_POST['cargo']) AND strlen($_POST['cargo'])>0) { $cargo=htmlentities($_POST['cargo']); }else{ $cargo=''; $fallo=1; $msjTxt.='<br>Falta cargo'; }
		if (isset($_POST['nombre']) AND strlen($_POST['nombre'])>0) { $nombre=htmlentities($_POST['nombre']); }else{ $nombre=''; $fallo=1; $msjTxt.='<br>Falta nombre'; }
		if (isset($_POST['email']) AND strlen($_POST['email'])>0) { $email=htmlentities($_POST['email']); }else{ $email=''; $fallo=1; $msjTxt.='<br>Falta email'; }
		if (isset($_POST['telefono']) AND strlen($_POST['telefono'])>0) { $telefono=htmlentities($_POST['telefono']); }else{ $telefono=''; $fallo=1; $msjTxt.='<br>Falta telefono'; }
		if (isset($_POST['ext']) AND strlen($_POST['ext'])>0) { $ext=htmlentities($_POST['ext']); }else{ $ext=''; }
		if (isset($_POST['ciudad']) AND strlen($_POST['ciudad'])>0) { $ciudad=htmlentities($_POST['ciudad']); }else{ $ciudad=''; }
		if (isset($_POST['comentarios']) AND strlen($_POST['comentarios'])>0) { $comentarios=htmlentities($_POST['comentarios']); }else{ $comentarios=''; }


		if ($fallo==0) {
			$sendMail     = 1;
			unset($_SESSION['carro']);

			$productosTxt='';
			foreach ($arreglo as $key => $value) {
				$id=$value['Id'];
				$CONSULTA= $CONEXION -> query("SELECT * FROM productos WHERE id = $id");
				while($row_CONSULTA = $CONSULTA -> fetch_assoc()){
					$productosTxt.='<li style="padding-top:10px;padding-bottom:10px;text-align:left;">';
					$productosTxt.='<a href="'.$ruta.$id.'_producto-.html" style="background:#3b669f;color:white;padding:5px;border-radius:4px;">';
					$productosTxt.='Ver producto';
					$productosTxt.='</a>';
					$productosTxt.='&nbsp;&nbsp;&nbsp;&nbsp;';
					$productosTxt.=$row_CONSULTA['titulo'];
					$productosTxt.='</li>';
				}
			}

			$asuntoCorreo = 'Formulario de contacto';
			$cuerpoCorreo = '
				<div style="width:700px;font-size:18;text-align:center;padding-top:50px;padding-bottom:50px;">
					Institucion: <b>'.$institucion.'</b><br><br>
					Escuela: <b>'.$escuela.'</b><br><br>
					Campus: <b>'.$campus.'</b><br><br>
					Cargo: <b>'.$cargo.'</b><br><br>
					Nombre: <b>'.$nombre.'</b><br><br>
					Email: <b>'.$email.'</b><br><br>
					Telefono: <b>'.$telefono.'</b><br><br>
					Ext: <b>'.$ext.'</b><br><br>
					Ciudad: <b>'.$ciudad.'</b><br><br>
					Comentarios: <b>'.$comentarios.'</b><br><br>
					<ul>'.$productosTxt.'</ul>
					<div>'.fechaDisplay($hoy).'<div>
				</div>';
		}
	}

//    Cargar productos en la cotizacion  
	if (isset($_POST['cargarproductos'])) {
		$xtras='';
		foreach ($arreglo as $key => $value) {
			$id=$value['Id'];
			$CONSULTA= $CONEXION -> query("SELECT * FROM productos WHERE id = $id");
			while($row_CONSULTA = $CONSULTA -> fetch_assoc()){
				$xtras.='<li id=\'producto'.$id.'\'>';
				$xtras.='<div uk-grid>';
				$xtras.='<div>';
				$xtras.='<a href=\'javascript:quitar(\"'.$id.'\")\' class=\'uk-icon-button uk-button-danger\' uk-icon=\'trash\'></a>';
				$xtras.='</div>';
				$xtras.='<div class=\'uk-width-expand\'>';
				$xtras.=$row_CONSULTA['titulo'];
				$xtras.='</div>';
				$xtras.='</div>';
				$xtras.='</li>';

				$fallo    = 0;
				$msjIcon  = 'check';
				$msjClase = 'success';
				$msjTxt   = 'Exito';
			}
		}
	}

//    Envío de correos
	if (isset($sendMail) AND $fallo==0) {
		include 'sendmail.php';
	}

//    Mostrar mensaje
	echo '{ "msj":"<div class=\'uk-text-center color-blanco bg-'.$msjClase.' padding-10 text-lg\'><i uk-icon=\'icon:'.$msjIcon.';ratio:3;\'></i> &nbsp; '.$msjTxt.'</div>", "estatus":"'.$fallo.'", "xtras":"'.$xtras.'"}';

//    Cerrar conexión y Borrar Error_log         
	mysqli_close($CONEXION);
	if (file_exists('error_log')) {
		unlink('error_log');
	}
