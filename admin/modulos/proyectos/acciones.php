<?php
	date_default_timezone_set('America/Mexico_City');
	$seccion='proyectos';
	$seccionpic=$seccion.'pic';
	$hoy=date('Y-m-d');
	$rutaInicial="../library/upload-file/php/uploads/";

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Nuevo Artículo     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_POST['nuevo'])){ 

		if (isset($_POST['titulo'])){ $titulo=htmlentities($_POST['titulo'], ENT_QUOTES); }else{ $titulo=''; $fallo=1; }

		// Actualizamos la base de datos
		if(!isset($fallo)){
			$sql = "INSERT INTO $seccion (titulo,fecha)".
				"VALUES ('$titulo','$hoy')";
			if($insertar = $CONEXION->query($sql)){
				$exito=1;
				$editarNuevo=1;
				$legendSuccess .= "<br>Promoción nueva";
				$id=$CONEXION->insert_id;
				$subseccion='detalle';
			}else{
				$fallo=1;  
				$legendFail .= "<br>No se pudo agregar a la base de datos";
			}
		}else{
			$legendFail .= "<br>El título está vacío.";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Editar Artículo     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_REQUEST['editar']) OR isset($editarNuevo)) {
		// Obtenemos los valores enviados

		$fallo=1;  
		$legendFail .= "<br>No se pudo modificar la base de datos";
		foreach ($_POST as $key => $value) {
			if ($key=='txt') {
				$dato = str_replace("'", "&#039;", $value);
			}else{
				$dato = htmlentities($value, ENT_QUOTES);
			}
			$actualizar = $CONEXION->query("UPDATE $seccion SET $key = '$dato' WHERE id = $id");
			$exito=1;
			unset($fallo);
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Artículo     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_REQUEST['borrarPod'])){
		if($borrar = $CONEXION->query("DELETE FROM $seccion WHERE id = $id")){
			$exito=1;
			$legendSuccess .= "<br>Eliminado";
		}else{
			$legendFail .= "<br>No se pudo borrar de la base de datos";
			$fallo=1;  
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Foto Redes    %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_REQUEST['borrarpicmain'])){
		$rutaFinal="../img/contenido/".$seccion."main/";
		$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
		$row_CONSULTA = $CONSULTA -> fetch_assoc();
		if (strlen($row_CONSULTA['imagen'])>0) {
			unlink($rutaFinal.$row_CONSULTA['imagen']);
			$actualizar = $CONEXION->query("UPDATE $seccion SET imagen = NULL WHERE id = $id");
			$exito=1;
			$legendSuccess.='<br>Imagen eliminada';
		}else{
			$legendFail .= "<br>No se encontró la imagen en la base de datos";
			$fallo=1;
		}
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Borrar        %%%%%%%%%%%%
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
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar mail   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_REQUEST['borrarmail'])){
		$rutaFinal="../img/contenido/".$seccion."mail/";
		$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
		$row_CONSULTA = $CONSULTA -> fetch_assoc();
		if (strlen($row_CONSULTA['mail'])>0) {
			unlink($rutaFinal.$row_CONSULTA['mail']);
			$actualizar = $CONEXION->query("UPDATE $seccion SET mail = NULL WHERE id = $id");
			$exito=1;
			$legendSuccess.='<br>Imagen eliminada';
		}else{
			$legendFail .= "<br>No se encontró la imagen en la base de datos";
			$fallo=1;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Publicar u ocultar     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['estatuschange'])) {
		include '../../../includes/connection.php';

		$id = $_POST['id'];
		$estatus = $_POST['estatus'];

		if($actualizar = $CONEXION->query("UPDATE $seccion SET estatus = $estatus WHERE id = $id")){
			echo '<span class="uk-text-success">Guardado</span>';
		}else{
			echo '<span class="uk-text-danger">Ocurrió un error</span>';
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Ordenar $seccion     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['list1'])) {
		include '../../../includes/connection.php';

		$list = $_POST['list1'];
		$num=1;

		foreach ($list as $lista) {
			$actualizar = $CONEXION->query("UPDATE $seccion SET orden = $num WHERE id = '$lista'");

			$num++;
		}
		echo '<span class="uk-text-success">Guardado</span>';
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Ordenar Fotos     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['list2'])) {
		include '../../../includes/connection.php';

		$list = $_POST['list2'];
		$num=1;

		foreach ($list as $lista) {
			$actualizar = $CONEXION->query("UPDATE $seccionpic SET orden = $num WHERE id = '$lista'");

			$num++;
		}
		echo '<span class="uk-text-success">Guardado</span>';
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Redes Borrar     %%%%%%%%%
	if(isset($_REQUEST['borrarpicredes'])){
		$rutaFinal="../img/contenido/".$seccion."/";
		$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
		$row_CONSULTA = $CONSULTA -> fetch_assoc();
		if (strlen($row_CONSULTA['imagen'])>0) {
			unlink($rutaFinal.$row_CONSULTA['imagen']);
			$actualizar = $CONEXION->query("UPDATE $seccion SET imagen = '' WHERE id = $id");
			$exito=1;
			$legendSuccess.='<br>Foto eliminada';
		}else{
			$legendFail .= "<br>No se encontró la imagen en la base de datos";
			$fallo=1;
		}
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir Imagen       %%%%%%%%%%%%%
	if(isset($_REQUEST['imagen'])){
		$position=$_GET['position'];


		$xs=0;
		$sm=1;
		$lg=1;
		$rutaFinal='../img/contenido/'.$seccion.'/';


		//Obtenemos la extensión de la imagen
		$imagenName=$_REQUEST['imagen'];
		$i = strrpos($imagenName,'.');
		$l = strlen($imagenName) - $i;
		$ext = strtolower(substr($imagenName,$i+1,$l));


		// Guardar en la base de datos
		if (!isset($fallo)) {
			if(file_exists($rutaInicial.$imagenName)){
				$pic=$id;
				if ($position=='gallery') {
					$sql = "INSERT INTO $seccionpic (producto) VALUES ($id)";
					$insertar = $CONEXION->query($sql);
					$pic=$CONEXION->insert_id;
					$crear=1;
		


	
				}elseif($position=='main'){
					$rutaFinal='../img/contenido/'.$seccion.'/';
					$imgFinal=rand(111111111,999999999).'.'.$ext;
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
					$anchoNuevo = 400;
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
						$anchoNuevo = 1000;
						$altoNuevo  = $anchoNuevo*$alto/$ancho;
					}else{
						$altoNuevo  = 1000;
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

		if($position=='main' or $position=='categoria' or $position=='icono1' or $position=='icono2'){
			$exito=1;
			$legendSuccess .= "<br>Imagen actualizada";
			unset($fallo);
		}
	}

	//if (file_exists('error_log')) {
		//unlink('error_log');
	//}
