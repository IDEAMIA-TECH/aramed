<?php
	$rutaInicial="../library/upload-file/php/uploads/";
	$rutaFinal='../img/contenido/varios/';

// *****************************
// Textos del editor    
// *****************************
	if (isset($_POST['editartextosconformato'])) {
		foreach ($_POST as $key => $value) {
			$dato = trim(str_replace("'", "&#039;", $value));
			$actualizar = $CONEXION->query("UPDATE $seccion SET $key = '$dato' WHERE id = 1");
			$exito=1;
			unset($fallo);
		}
	}
	if (isset($_POST['editartextos'])) {
		foreach ($_POST as $key => $value) {
			$dato = trim(htmlentities($value, ENT_QUOTES));
			$actualizar = $CONEXION->query("UPDATE $seccion SET $key = '$dato' WHERE id = 1");
			$exito=1;
			unset($fallo);
		}
	}




// *****************************
//	Archivos
// *****************************
// Borrar archivos      
	if(isset($_REQUEST['borrarpic'])){
		$campo=$_GET['campo'];

		$CONSULTA = $CONEXION -> query("SELECT $campo FROM $seccion WHERE id = 1");
		$row_CONSULTA = $CONSULTA -> fetch_assoc();

		if (strlen($row_CONSULTA[$campo])>0) {
			$pic=$rutaFinal.$row_CONSULTA[$campo];
			if(file_exists($pic)){
				unlink($pic);
				$legendSuccess.='<br>Archivo eliminado: '.$row_CONSULTA[$campo];
			}else{
				$legendSuccess.='<br>Archivo no encontrado';
			}
			$actualizar = $CONEXION->query("UPDATE $seccion SET $campo = NULL WHERE id = 1");
			$exito=1;
		}else{
			$legendFail .= "<br>No se encontró el archivo en la base de datos: ".$row_CONSULTA[$campo];
			$fallo=1;
		}
	}

// Subir archivos       
	if(isset($_GET['fileuploaded'])){

		$imagenName=$_GET['fileuploaded'];

		$campo=$_GET['campo'];

		// Verificar que la imagen existe
		if(!file_exists($rutaInicial.$imagenName)){
			$fallo=1;
			$legendFail='<br>No se permite refrescar la página.';
		}

		// Extensión de la imagen
		if (!isset($fallo)) {
			$i = strrpos($imagenName,'.');
			$l = strlen($imagenName) - $i;
			$ext = strtolower(substr($imagenName,$i+1,$l));
		}

		// Guardar en la base de datos
		if (!isset($fallo)) {

			// Nombre del nuevo archivo
			$rand=rand(111111111,999999999);
			$imgFinal=$rand.'.'.$ext;
			// Si el nombre ya está en usado, definir otro
			if(file_exists($rutaFinal.$imgFinal)){
				$imgFinal=$rand.'.'.$ext;
			}

			// Obtenemos el nombre del archivo anterior
			$CONSULTA = $CONEXION -> query("SELECT $campo FROM $seccion WHERE id = 1");
			$row_CONSULTA = $CONSULTA -> fetch_assoc();
			// Si existe, lo borramos
			if ($row_CONSULTA[$campo]!='' AND file_exists($rutaFinal.$row_CONSULTA[$campo])) {
				unlink($rutaFinal.$row_CONSULTA[$campo]);
			}

			// Copiar el archivo a su nueva ubicación
			copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
			$actualizar = $CONEXION->query("UPDATE $seccion SET $campo = '$imgFinal' WHERE id = 1");
			
		}

		// Borramos las imágenes que estén remanentes en el directorio de subida
		$filehandle = opendir($rutaInicial); // Abrir archivos
		while ($file = readdir($filehandle)) {
			if ($file != "." && $file != "..") {
				if(file_exists($rutaInicial.$file)){
					unlink($rutaInicial.$file);
				}
			}
		} 
		// Fin lectura archivos
		closedir($filehandle); 

	}





// *****************************
//	FAQ
// *****************************
//	Nuevo Artículo      
	if(isset($_POST['nuevo']) && isset($_POST['pregunta'])){ 
		// Obtenemos los valores enviados
		if (isset($_POST['pregunta']))	{ $pregunta=htmlentities($_POST['pregunta'], ENT_QUOTES);	}else{	$pregunta=''; }
		if (isset($_POST['respuesta']))	{ $respuesta=str_replace("'", "&#039;", $_POST['respuesta']);	}else{	$respuesta=''; }

		// Actualizamos la base de datos
		if($pregunta!=""){

			$legendFail.='<br>'.$pregunta;

			$sql = "INSERT INTO faq (pregunta,respuesta,orden)".
				"VALUES ('$pregunta','$respuesta','99')";
			if($insertar = $CONEXION->query($sql)){
				$exito=1;
				$id=$CONEXION->insert_id;
				$legendSuccess .= '<br>Pregunta nueva';
			}else{
				$fallo=1;  
				$legendFail .= "<br>No se pudo agregar a la base de datos";
			}
		}else{
			$fallo=1;  
			$legendFail .= "<br>Está vacío";
		}
	}

//	Editar Artículo     
	if(isset($_POST['editar']) && isset($_POST['pregunta'])){

	    // Obtenemos los valores enviados
		if (isset($_POST['pregunta']))	{ $pregunta=htmlentities($_POST['pregunta'], ENT_QUOTES);	}else{	$pregunta=''; }
		if (isset($_POST['respuesta']))	{ $respuesta=str_replace("'", "&#039;", $_POST['respuesta']);	}else{	$respuesta=''; }


		if(
				$actualizar = $CONEXION->query("UPDATE faq SET 
					pregunta = '$pregunta',
					respuesta  = '$respuesta' 
					WHERE id = $id")
			){
			$exito=1;
			$legendSuccess.='<br>'.$pregunta;
		    $subseccion='contenido';
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo modificar la base de datos";
		}
	}

//	Borrar Artículo     
	if(isset($_REQUEST['borrarFaq'])){
		if($borrar = $CONEXION->query("DELETE FROM faq WHERE id = $id")){
			$exito=1;
			$legendSuccess .= "<br>Pregunta eliminada";
		}else{
			$legendFail .= "<br>No se pudo borrar de la base de datos";
			$fallo=1;  
		}
	}




// *****************************
//	USUARIOS
// *****************************

//	Nuevo Administrador 
	if(isset($_REQUEST['new-user'])){
		if(isset($_REQUEST['user'])){ $user=strtolower($_REQUEST['user']); }else{ $user=false; $legendFail.="<br><br>Proporcione nombre de usuario";}
		if(isset($_REQUEST['pass'])){ $pass=$_REQUEST['pass']; }else{ $pass=false; $legendFail.="<br><br>Proporcione contraseña";}
		if(isset($_REQUEST['pass1'])){ $pass1=$_REQUEST['pass1']; }else{ $pass1=false; $legendFail.="<br><br>Confirme su contraseña";}
		if(strlen($pass)>5){
			if($pass==$pass1 and $user!=false){
				$pass_encripted = md5($pass);

				$USER = $CONEXION -> query("SELECT * FROM user WHERE user = '$user'");
				$numRows = $USER ->num_rows;
				if ($numRows==0) {

					$sql = "INSERT INTO user (pass,user,nivel)".
						"VALUES ('$pass_encripted','$user',1)";
					if($insertar = $CONEXION->query($sql))
					{
						$exito='success';
						$legendSuccess.="<br>Administrador agregado";
					}else{
						$fallo='danger';  
						$legendFail.="<br>No se pudo agregar el Administrador";
					}
				}else{
					$fallo='danger';  
					$legendFail.="<br>El usuario ya existe";
				}
			}else{
				$fallo='danger';  
				$legendFail.="<br>Las contraseñas no coinciden ";
			}
		}else{
			$fallo='danger';  
			$legendFail.="<br>La contraseña es demasiado débil ";
		}
	}

//	Editar Administrador
	if(isset($_REQUEST['edit-user'])){
		if(isset($_REQUEST['user'])){ $user=strtolower($_REQUEST['user']); }else{ $user=false; $legendFail.="<br><br>Proporcione nombre de usuario";}
		if(isset($_REQUEST['pass'])){ $pass=$_REQUEST['pass']; }else{ $pass=false; $legendFail.="<br><br>Proporcione contraseña";}
		if(isset($_REQUEST['pass1'])){ $pass1=$_REQUEST['pass1']; }else{ $pass1=false; $legendFail.="<br><br>Confirme su contraseña";}
		if(strlen($pass)>5){
			if($pass==$pass1){
				$pass_encripted = md5($pass);

				if(
					$actualizar = $CONEXION->query("UPDATE user SET user = '$user' WHERE id = $id")
				and	$actualizar = $CONEXION->query("UPDATE user SET pass = '$pass_encripted' WHERE id = $id")
					)
				{
					$exito='success';
					$legendSuccess.="<br>Administrador editado";
				}else{
					$fallo='danger';  
					$legendFail.="<br>No se pudo modificar el Administrador";
				}
			}else{
				$fallo='danger';  
				$legendFail.="<br>Contraseñas no coinciden";
			}
		}else{
			$fallo='danger';  
			$legendFail.="<br>Contraseña demasiado débil";
		}
	}

//	Borrar Administrador
	if(isset($_REQUEST['borrarUser'])){
		if($borrar = $CONEXION->query("DELETE FROM user WHERE id = $id"))
		{
			$exito='success';
			$legendSuccess.="<br>Administrador eliminado";
		}else{
			$fallo='danger';  
			$legendFail.="<br>No se pudo eliminar el Administrador";
		}
	} 

//	Editar nivel        
	if (isset($_POST['editanivel'])) {
		include '../../../includes/connection.php';
		
		$id = $_POST['id'];
		$nivel = $_POST['nivel'];

		$actualizar = $CONEXION->query("UPDATE user SET nivel = $nivel WHERE id = $id");
	}
