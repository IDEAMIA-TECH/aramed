<?php
	$seccion 	='noticias';
	$seccionpic	='noticiaspic';
	$seccionmain='noticiasmain';
	$rutaInicial="../library/upload-file/php/uploads/";

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Nuevo Artículo     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_POST['nuevo'])){ 
		// Obtenemos los valores enviados
		if (isset($_POST['titulo'])){ $titulo=htmlentities($_POST['titulo'], ENT_QUOTES); }else{ $titulo=''; $fallo=1; }

		// Actualizamos la base de datos
		if($titulo!=""){
			$sql = "INSERT INTO $seccion (titulo)".
				"VALUES ('$titulo')";
			if($insertar = $CONEXION->query($sql)){
				$exito=1;
				$legendSuccess .= "<br>Artículo nuevo";
				$editarNuevo = 1;
				$subseccion = 'detalle';
				$id=$CONEXION->insert_id;
			}else{
				$fallo=1;  
				$legendFail .= "<br>No se pudo agregar a la base de datos";
			}
		}else{
			$fallo=1;  
			$legendFail .= "<br>El título está vacío";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Editar Artículo     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if(isset($_REQUEST['editar']) && isset($_REQUEST['titulo']) OR isset($editarNuevo)){
		$fallo=1;  
		$legendFail .= "<br>No se pudo modificar la base de datos";
		foreach ($_POST as $key => $value) {
			if ($key=='txt') {
				$dato = str_replace("'", "&#039;", $value);
			}else{
				$dato = trim(htmlentities($value, ENT_QUOTES));
			}
			$actualizar = $CONEXION->query("UPDATE $seccion SET $key = '$dato' WHERE id = $id");
			$exito=1;
			unset($fallo);
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Artículo     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if(isset($_REQUEST['borrarPod'])){
		$consulta= $CONEXION -> query("SELECT * FROM $seccionpic WHERE producto = $id");
		while ($rowConsulta = $consulta-> fetch_assoc()) {
			$picID=$rowConsulta['id'];
			// Borramos el archivo de imagen
			$rutaIMG="../img/contenido/".$seccion."/";
			$filehandle = opendir($rutaIMG); // Abrir archivos
			while ($file = readdir($filehandle)) {
				if ($file != "." && $file != "..") {
					// Id de la imagen
					if (strpos($file,'-')===false) {
						$imagenID = strstr($file,'.',TRUE);
					}else{
						$imagenID = strstr($file,'-',TRUE);
					}
					// Comprobamos que sean iguales
					if($imagenID==$picID){
						$pic=$rutaIMG.$file;
						$exito=1;
						unlink($pic);
					}
				}
			}
	    }

		if($borrar = $CONEXION->query("DELETE FROM $seccion WHERE id = $id")){
			$borrar = $CONEXION->query("DELETE FROM $seccionpic WHERE producto = $id");
			$exito=1;
			$legendSuccess .= "<br>Producto eliminado";
		}else{
			$legendFail .= "<br>No se pudo borrar de la base de datos";
			$fallo=1;  
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Editar Foto     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if(isset($_REQUEST['editarpic'])){

		// Obtenemos los valores enviados
		if (isset($_POST['titulo'])){ 	$titulo=$_POST['titulo'];	}else{	$titulo=''; }
		if (isset($_POST['picid'])){ 	$picid=$_POST['picid'];		}else{	$picid=''; }

		// Sustituimos los caracteres inválidos
		$titulo=htmlentities($titulo, ENT_QUOTES);

		$subseccion='detalle';

		if(
				$actualizar = $CONEXION->query("UPDATE $seccionpic SET titulo = '$titulo' WHERE id = $picid")
			)
		{
			$exito=1;
			$legendSuccess.='<br>Editar título de foto';
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo modificar la base de datos";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Agregar video     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if(isset($_REQUEST['newvideo'])){

		// Obtenemos los valores enviados
		if (isset($_POST['titulo'])){ 	$titulo=$_POST['titulo'];	}else{	$titulo=''; }

		// Sustituimos los caracteres inválidos
		$titulo=htmlentities($titulo, ENT_QUOTES);

		$subseccion='detalle';

		$sql = "INSERT INTO $seccionpic (producto,title,orden)".
			"VALUES ($id,'$titulo',100)";
		if($insertar = $CONEXION->query($sql)){
			$exito=1;
			$legendSuccess.='<br>Nuevo video agregado';
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo modificar la base de datos";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Foto     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if(isset($_REQUEST['borrarPic'])){
		$picID=$_GET['picID'];
		$subseccion='detalle';
		if($borrar = $CONEXION->query("DELETE FROM $seccionpic WHERE id = $picID")){
			// Borramos el archivo de imagen
			$rutaIMG="../img/contenido/".$seccion."/";
			$filehandle = opendir($rutaIMG); // Abrir archivos
			while ($file = readdir($filehandle)) {
				if ($file != "." && $file != "..") {
					// Id de la imagen
					if (strpos($file,'-')===false) {
						$imagenID = strstr($file,'.',TRUE);
					}else{
						$imagenID = strstr($file,'-',TRUE);
					}
					// Comprobamos que sean iguales
					if($imagenID==$picID){
						$pic=$rutaIMG.$file;
						$exito=1;
						unlink($pic);
					}
				}
			}
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Publicar en inicio     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if (isset($_POST['eninicio'])) {
		include '../../../includes/connection.php';

		$id = $_POST['id'];
		$seccion = $_POST['seccion'];
		$estado = $_POST['estado'];

		if(
			$actualizar = $CONEXION->query("UPDATE $seccion SET inicio = $estado WHERE id = $id")
			){
			echo '
					<span class="uk-notification-message uk-notification-message-success">
						<a href="#" class="uk-notification-close" uk-close></a>
						<span uk-icon=\'icon: check;ratio:2;\'></span> &nbsp; Guardado
					</span>';
		}else{
			echo '
					<span class="uk-notification-message uk-notification-message-danger">
						<a href="#" class="uk-notification-close" uk-close></a>
						<span uk-icon=\'icon: close;ratio:2;\'></span> &nbsp; Ocurrió un error
					</span>';
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Ordenar $seccion     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if (isset($_POST['list1'])) {
		include '../../../includes/connection.php';

		$list = $_POST['list1'];
		$num=1;

		foreach ($list as $lista) {
			$actualizar = $CONEXION->query("UPDATE $seccion SET orden = $num WHERE id = '$lista'");

			$num++;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Ordenar Fotos     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if (isset($_POST['list2'])) {
		include '../../../includes/connection.php';

		$list = $_POST['list2'];
		$num=1;

		foreach ($list as $lista) {
			$actualizar = $CONEXION->query("UPDATE $seccionpic SET orden = $num WHERE id = '$lista'");

			$num++;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir Imagen     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_REQUEST['imagen'])){
		$position=$_GET['position'];

		$xs=0;
		$sm=1;
		$lg=1;
		$rutaFinal=($position=='main')?'../img/contenido/'.$seccion.'main/':'../img/contenido/'.$seccion.'/';


		//Obtenemos la extensión de la imagen
		$imagenName=$_REQUEST['imagen'];
		$i = strrpos($imagenName,'.');
		$l = strlen($imagenName) - $i;
		$ext = strtolower(substr($imagenName,$i+1,$l));

		// Si no es JPG cancelamos
		if ($ext!='jpg' and $ext!='jpeg') {
			$fallo=1;
			$legendFail='<br>El archivo debe ser JPG';
		}

		// Guardar en la base de datos
		if (!isset($fallo)) {
			if(file_exists($rutaInicial.$imagenName)){
				$pic=$id;
				if ($position=='gallery') {
					$sql = "INSERT INTO $seccionpic (producto,orden) VALUES ($id,100)";
					$insertar = $CONEXION->query($sql);
					$pic=$CONEXION->insert_id;
					$crear=1;
				}elseif($position=='main'){
					if(file_exists($rutaFinal.$imagenName)){
						$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE imagen = '$imagenName' AND id != $id");
						$numPics=$CONSULTA->num_rows;
						$imgFinal=rand(111111111,999999999).'.'.$ext;
						if ($numPics==0) {
							unlink($rutaFinal.$imagenName);
						}
					}else{
						$imgFinal=$imagenName;
					}
					$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
					$row_CONSULTA = $CONSULTA -> fetch_assoc();
					if ($row_CONSULTA['imagen']!='' AND file_exists($rutaFinal.$row_CONSULTA['imagen'])) {
						unlink($rutaFinal.$row_CONSULTA['imagen']);
					}
					copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
					$actualizar = $CONEXION->query("UPDATE $seccion SET imagen = '$imgFinal' WHERE id = $id");
					$crear=0;
				}
			}else{
				$fallo=1;
				$legendFail='<br>No se permite refrescar la página.';
			}
		}

		// crear las diferentes versiones de la imagen 
		if (!isset($fallo) and $position=='gallery') {

			$imagenName=$_REQUEST['imagen'];

			$imgAux=$rutaFinal.$pic."-aux.jpg";

			//check extension of the file
			$i = strrpos($imagenName,'.');
			$l = strlen($imagenName) - $i;
			$ext = strtolower(substr($imagenName,$i+1,$l));

			// Comprobamos que el archivo realmente se haya subido
			if(file_exists($rutaInicial.$imagenName)){

				// Lo movemos al directorio final
				copy($rutaInicial.$imagenName, $imgAux);    

				// Leer el archivo para hacer la nueva imagen
				$original = imagecreatefromjpeg($imgAux);

				// Tomamos las dimensiones de la imagen original
				$ancho  = imagesx($original);
				$alto   = imagesy($original);


				if ($xs==1) {
					//  Imagen xs
					$newName=$pic."-xs.jpg";
					$anchoNuevo = 80;
					$altoNuevo  = $anchoNuevo*$alto/$ancho;

					// Creamos la imagen
					$imagenAux = imagecreatetruecolor($anchoNuevo,$altoNuevo); 
					// Copiamos el contenido de la original para pegarlo en el archivo nuevo
					imagecopyresampled($imagenAux,$original,0,0,0,0,$anchoNuevo,$altoNuevo,$ancho,$alto);
					// Pegamos el contenido de la imagen
					if(imagejpeg($imagenAux,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
						$exito=1;
					}
				}

				if ($sm==1) {
					//  Imagen sm
					$newName=$pic."-sm.jpg";
					$anchoNuevo = 300;
					$altoNuevo  = $anchoNuevo*$alto/$ancho;

					// Creamos la imagen
					$imagenAux = imagecreatetruecolor($anchoNuevo,$altoNuevo); 
					// Copiamos el contenido de la original para pegarlo en el archivo nuevo
					imagecopyresampled($imagenAux,$original,0,0,0,0,$anchoNuevo,$altoNuevo,$ancho,$alto);
					// Pegamos el contenido de la imagen
					if(imagejpeg($imagenAux,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
						$exito=1;
					}
				}

				if ($lg==1) {
					//  Imagen lg
					$newName=$pic."-lg.jpg";
					if ($ancho>$alto) {
						$anchoNuevo = 1200;
						$altoNuevo  = $anchoNuevo*$alto/$ancho;
					}else{
						$altoNuevo  = 1200;
						$anchoNuevo = $altoNuevo*$ancho/$alto;
					}

					// Creamos la imagen
					$imagenAux = imagecreatetruecolor($anchoNuevo,$altoNuevo); 
					// Copiamos el contenido de la original para pegarlo en el archivo nuevo
					imagecopyresampled($imagenAux,$original,0,0,0,0,$anchoNuevo,$altoNuevo,$ancho,$alto);
					// Pegamos el contenido de la imagen
					if(imagejpeg($imagenAux,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
						$exito=1;
					}
				}

				if ($originalPic==0) {
					unlink($imgAux);
				}else{
					rename ($imgAux, $rutaFinal.$pic."-orig.jpg");
				}

				if($exito=1){
					$legendSuccess .= "<br>Imagen actualizada";
				}
			}
		}else{
			$fallo=1;
			$legendFail.= '<br>No pudo subirse la imagen';
		}

		if($position=='main' or $position=='categoria'){
			$exito=1;
			$legendSuccess .= "<br>Imagen actualizada";
			unset($fallo);
		}
	}

