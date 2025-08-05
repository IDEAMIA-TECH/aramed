<?php
	date_default_timezone_set('America/Mexico_City');
	$seccion='productos';
	$seccioncat=$seccion.'cat';
	$seccionpic=$seccion.'pic';
	$seccionmain=$seccion.'main';
	$hoy=date('Y-m-d');
	$rutaInicial="../library/upload-file/php/uploads/";

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Nuevo Artículo     %%%%%%%%%%%%%
	if(isset($_POST['nuevo'])){ 
		// Actualizamos la base de datos
		$sql = "INSERT INTO productos (fecha) VALUES ('$hoy')";
		if($insertar = $CONEXION->query($sql)){
			$exito=1;
			$legendSuccess .= "<br>Producto nuevo";
			$editarNuevo=1;
			$id=$CONEXION->insert_id;
			$subseccion='detalle';
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo agregar a la base de datos - $hoy";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Editar Artículo     %%%%%%%%%%%%
	if(isset($_REQUEST['editar']) OR isset($editarNuevo)) {
		// Obtenemos los valores enviados

		$fallo=1;  
		$legendFail .= "<br>No se pudo modificar la base de datos";
		foreach ($_POST as $key => $value) {
			if ($key=='txt' OR $key=='txt1' OR $key=='txt2' OR $key=='txt3') {
				$dato = str_replace("'", "&#039;", $value);
			}else{
				$dato = htmlentities($value, ENT_QUOTES);
			}
			$actualizar = $CONEXION->query("UPDATE $seccion SET $key = '$dato' WHERE id = $id");
			$exito=1;
			unset($fallo);
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Productos relacionados    %%%%%%
	if(isset($_POST['relacionar'])) {
		// Obtenemos los valores enviados
		foreach ($_POST as $key => $value) {
			if (strpos($key, 'apa')) {
				if (isset($rel)) {
					$rel.=','.$value;
				}else{
					$rel=$value;
				}
			}
		}
		$actualizar = $CONEXION->query("UPDATE $seccion SET rel = '$rel' WHERE id = $id");
		$exito=1;
		$legendSuccess.='<br>Relaciones guardadas';
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Artículo Borrar        %%%%%%%%%
	if(isset($_REQUEST['borrarPod'])){
		if($borrar = $CONEXION->query("DELETE FROM $seccion WHERE id = $id")){
			$exito=1;
			$legendSuccess .= "<br>Producto eliminado";
		}else{
			$legendFail .= "<br>No se pudo borrar de la base de datos";
			$fallo=1;  
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Editar        %%%%%%%%%%%%%
	if(isset($_REQUEST['editarpic'])){

	    // Obtenemos los valores enviados
		if (isset($_POST['titulo'])){ 	$titulo=$_POST['titulo'];	}else{	$titulo=''; }
		if (isset($_POST['title'])){ 	$title=$_POST['title'];	}else{	$title=''; }
		if (isset($_POST['picid'])){ 	$picid=$_POST['picid'];		}else{	$picid=''; }

	    // Sustituimos los caracteres inválidos
		$titulo=htmlentities($titulo, ENT_QUOTES);

	    $subseccion='detalle';

		if(
				$actualizar = $CONEXION->query("UPDATE $seccionpic SET titulo = '$titulo' WHERE id = $picid")
			and	$actualizar = $CONEXION->query("UPDATE $seccionpic SET title = '$title' WHERE id = $picid")
			)
		{
			$exito=1;
			$legendSuccess.='<br>Editar título de foto';
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo modificar la base de datos";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Borrar        %%%%%%%%%%%%%
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

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Redes Borrar     %%%%%%%%%%
	if(isset($_REQUEST['borrarpicredes'])){
		$rutaFinal="../img/contenido/".$seccionmain."/";
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

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Logo 1 Categoría Borrar  %%
	if(isset($_REQUEST['borrarpiccat'])){
		$rutaFinal="../img/contenido/".$seccioncat."/";
		$CONSULTA = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
		$row_CONSULTA = $CONSULTA -> fetch_assoc();
		if (strlen($row_CONSULTA['imagen'])>0) {
			unlink($rutaFinal.$row_CONSULTA['imagen']);
			$actualizar = $CONEXION->query("UPDATE $seccioncat SET imagen = '' WHERE id = $cat");
			$exito=1;
			$legendSuccess.='<br>Eliminar';
		}else{
			$legendFail .= "<br>No se encontró la imagen en la base de datos";
			$fallo=1;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Logo 2 Categoría Borrar  %%
	if(isset($_REQUEST['borrarpiccat2'])){
		$rutaFinal="../img/contenido/".$seccioncat."/";
		$CONSULTA = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
		$row_CONSULTA = $CONSULTA -> fetch_assoc();
		if (strlen($row_CONSULTA['imagen2'])>0) {
			unlink($rutaFinal.$row_CONSULTA['imagen2']);
			$actualizar = $CONEXION->query("UPDATE $seccioncat SET imagen2 = '' WHERE id = $cat");
			$exito=1;
			$legendSuccess.='<br>Logo eliminado';
		}else{
			$legendFail .= "<br>No se encontró la imagen en la base de datos";
			$fallo=1;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Foto Redes Borrar     %%%%%%%%%%
	if(isset($_REQUEST['borrarpdf'])){
		$rutaFinal="../img/contenido/".$seccion."pdf/";
		$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
		$row_CONSULTA = $CONSULTA -> fetch_assoc();
		if (strlen($row_CONSULTA['pdf'])>0) {
			unlink($rutaFinal.$row_CONSULTA['pdf']);
			$actualizar = $CONEXION->query("UPDATE $seccion SET pdf = '' WHERE id = $id");
			$exito=1;
			$legendSuccess.='<br>PDF eliminado';
		}else{
			$legendFail .= "<br>No se encontró la imagen en la base de datos";
			$fallo=1;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Categoria Nueva        %%%%%%%%%
	if(isset($_POST['nuevacategoria'])){ 
		// Obtenemos los valores enviados
		if (isset($_POST['categoria'])) { $categoria=$_POST['categoria'];   }else{	$categoria=false; $fallo=1; }

		// Sustituimos los caracteres inválidos
		$categoria=(htmlentities($categoria, ENT_QUOTES));

		// Actualizamos la base de datos
		if($categoria!=""){
			$sql = "INSERT INTO $seccioncat (txt) VALUES ('$categoria')";
			if($insertar = $CONEXION->query($sql)){
				$cat = $CONEXION->insert_id;
				$exito=1;
				$legendSuccess .= "<br>Nueva categoria";
			}
		}else{
			$fallo=1;  
			$legendFail .= "<br>El campo está vacío";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Categoría Editar        %%%%%%%%
	if(isset($_POST['editarcategoria'])){
		$txt=htmlentities($_POST['txt'], ENT_QUOTES);
		$change=$id;		
		if(
				$actualizar = $CONEXION->query("UPDATE $seccioncat SET txt = '$txt' WHERE id = $change")
		){
			$exito=1;
			$legendSuccess .= "<br>Editar categoría";
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo modificar la base de datos";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Categoría Borrar        %%%%%%%%
	if(isset($_REQUEST['eliminarCat'])){

		if($borrar = $CONEXION->query("DELETE FROM $seccioncat WHERE id = $cat")){
			$subseccion='categorias';
			$exito=1;
			$legendSuccess .= "<br>Categoria eliminada";

			$rutaIMG="../img/contenido/".$seccioncat."/";
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
					if($imagenID==$cat){
						$pic=$rutaIMG.$file;
						$exito=1;
						unlink($pic);
					}
				}
			}

		}else{
			$fallo=1;
			$legendFail .= "<br>No se pudo borrar de la base de datos";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subategoria Nueva        %%%%%%%
	if(isset($_POST['nuevasubcategoria'])){ 
		// Obtenemos los valores enviados
		if (isset($_POST['categoria'])) { $categoria=$_POST['categoria'];   }else{	$categoria=false; $fallo=1; }

		// Sustituimos los caracteres inválidos
		$categoria=htmlentities($categoria, ENT_QUOTES);

		// Actualizamos la base de datos
		if($categoria!=""){
			$sql = "INSERT INTO $seccioncat (txt,parent) VALUES ('$categoria',$cat)";
			if($insertar = $CONEXION->query($sql)){
				$cat = $CONEXION->insert_id;
				$exito=1;
				$legendSuccess .= "<br>Nueva subcategoria";
			}else{
				$fallo=1;  
				$legendFail .= "<br>No pudo agregarse a la base de datos ".$seccioncat.'-'.$cat.'-'.$categoria;
			}
		}else{
			$fallo=1;  
			$legendFail .= "<br>El campo está vacío";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Activar todo    %%%%%%%%%%%%%%%%
	if (isset($_POST['activalotodo'])) {
		include '../../../includes/connection.php';

		if($actualizar = $CONEXION->query("UPDATE $seccion SET estatus = 1")){
			$actualizar = $CONEXION->query("UPDATE $seccioncat SET destacado = 1");
			$mensajeClase='success';
			$mensajeIcon='check';
			$mensaje='Guardado';
		}else{
			$mensajeClase='danger';
			$mensajeIcon='ban';
			$mensaje='No se pudo guardar';
		}
		echo '<div class="uk-text-center color-white bg-'.$mensajeClase.' padding-10 text-lg"><i class="fa fa-'.$mensajeIcon.'"></i> &nbsp; '.$mensaje.'</div>';		
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Activa desactiva categorías %%%%
	if (isset($_POST['estatuscat'])) {
		include '../../../includes/connection.php';

		$id=$_POST['id'];
		$estatus=$_POST['estatus'];

		if($actualizar = $CONEXION->query("UPDATE $seccion SET estatus = $estatus WHERE categoria = $id")){
			$exito=1;
		}

		$consulta = $CONEXION -> query("SELECT * FROM $seccioncat WHERE parent = $id");
		while ($rowConsulta = $consulta -> fetch_assoc()) {
			$actual=$rowConsulta['id'];
			$actualizar = $CONEXION->query("UPDATE $seccioncat SET estatus = $estatus WHERE id = $actual");
			$actualizar = $CONEXION->query("UPDATE $seccion SET estatus = $estatus WHERE categoria = $actual");
		}

		if(isset($exito)){
			$mensajeClase='success';
			$mensajeIcon='check';
			$mensaje='Guardado';
		}else{
			$mensajeClase='danger';
			$mensajeIcon='ban';
			$mensaje='No se pudo guardar - id:'.$id.' - estatus:'.$estatus.' -  seccion:'.$seccion;
		}
		echo '<div class="uk-text-center color-white bg-'.$mensajeClase.' padding-10 text-lg"><i class="fa fa-'.$mensajeIcon.'"></i> &nbsp; '.$mensaje.'</div>';
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir ficha tácnica     %%%%%%%%
	if(isset($_GET['ficha'])){
		$rutaFinal='../img/contenido/'.$seccion.'ficha/';
		$rutaInicial="../library/upload-file/php/uploads/";

		//Obtenemos la extensión de la imagen
		$imagenName=$_GET['ficha'];
		$i = strrpos($imagenName,'.');
		$l = strlen($imagenName) - $i;
		$ext = strtolower(substr($imagenName,$i+1,$l));

		// Guardar en la base de datos
		if(file_exists($rutaInicial.$imagenName)){
			$imgFinal=rand(111111111,999999999).'.'.$ext;
			$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
			$row_CONSULTA = $CONSULTA -> fetch_assoc();
			if ($row_CONSULTA['video']!='' AND file_exists($rutaFinal.$row_CONSULTA['video'])) {
				unlink($rutaFinal.$row_CONSULTA['video']);
			}
			copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
			$actualizar = $CONEXION->query("UPDATE $seccion SET video = '$imgFinal' WHERE id = $id");
		}else{
			$fallo=1;
			$legendFail='<br>No existe el archivo';
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir Imagen       %%%%%%%%%%%%%
	if(isset($_REQUEST['imagen'])){
		$position=$_GET['position'];


		$xs=0;
		$sm=0;
		$md=0;
		$lg=0;

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
					$sm=1;
					$lg=1;
				}elseif($position=='pdf'){
					$rutaFinal='../img/contenido/'.$seccion.'pdf/';
					$imgFinal=rand(111111111,999999999).'.'.$ext;
					if(file_exists($rutaFinal.$imgFinal)){
						$imgFinal=rand(111111111,999999999).'.'.$ext;
					}
					$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
					$row_CONSULTA = $CONSULTA -> fetch_assoc();
					if ($row_CONSULTA['pdf']!='' AND file_exists($rutaFinal.$row_CONSULTA['pdf'])) {
						unlink($rutaFinal.$row_CONSULTA['pdf']);
					}
					$legendFail.='<br>Fail - '.$position;
					copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
					$actualizar = $CONEXION->query("UPDATE $seccion SET pdf = '$imgFinal' WHERE id = $id");
					$crear=0;

				}elseif($position=='categoria'){
					$md=1;
					$rutaFinal='../img/contenido/'.$seccioncat.'/';
					$imgFinal=rand(111111111,999999999).'.'.$ext;
					$CONSULTA = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
					$row_CONSULTA = $CONSULTA -> fetch_assoc();
					if ($row_CONSULTA['imagen']!='' AND file_exists($rutaFinal.$row_CONSULTA['imagen'])) {
						unlink($rutaFinal.$row_CONSULTA['imagen']);
					}
					copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
					$actualizar = $CONEXION->query("UPDATE $seccioncat SET imagen = '$imgFinal' WHERE id = $cat");
					$crear=1;

				}elseif($position=='categoria2'){
					$rutaFinal='../img/contenido/'.$seccioncat.'/';
					$imgFinal=rand(111111111,999999999).'.'.$ext;
					$CONSULTA = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $cat");
					$row_CONSULTA = $CONSULTA -> fetch_assoc();
					if ($row_CONSULTA['imagen2']!='' AND file_exists($rutaFinal.$row_CONSULTA['imagen2'])) {
						unlink($rutaFinal.$row_CONSULTA['imagen2']);
					}
					copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
					$actualizar = $CONEXION->query("UPDATE $seccioncat SET imagen2 = '$imgFinal' WHERE id = $cat");
					$crear=0;
				}elseif($position=='main'){
					$rutaFinal='../img/contenido/'.$seccionmain.'/';
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

		if (!isset($fallo) and $crear==1) {

			$imgAux=$rutaInicial.$imagenName;

			//check extension of the file
			$i = strrpos($imagenName,'.');
			$l = strlen($imagenName) - $i;
			$ext = strtolower(substr($imagenName,$i+1,$l));

			if($original = imagecreatefromjpeg($imgAux)){

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


				if ($md==1) {
					//  Imagen cat
					$newName=$imgFinal;
					if ($ancho>$alto) {
						$anchoNuevo = 520;
						$altoNuevo  = $anchoNuevo*$alto/$ancho;
					}else{
						$altoNuevo  = 400;
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
		}
	}

	if (file_exists('error_log')) {
		unlink('error_log');
	}
