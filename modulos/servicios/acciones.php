<?
	$seccion 	 = 'servicios';
	$seccionpic	 = $seccion.'pic';
	$rutaFinal   = '../img/contenido/'.$seccion.'/';

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
		if($borrar = $CONEXION->query("DELETE FROM $seccion WHERE id = $id")){
			$exito=1;
			$legendSuccess .= "<br>Producto eliminado";
		}else{
			$legendFail .= "<br>No se pudo borrar de la base de datos";
			$fallo=1;  
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Foto     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%			
	if(isset($_GET['borrarPic'])){
		$campo=$_GET['borrarPic'];
		$consulta = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
		$numItems=$consulta->num_rows;
		if ($numItems>0) {
			$rowConsulta = $consulta -> fetch_assoc();
			$rutaIMG="../img/contenido/".$seccion."/";
			$pic=$rutaIMG.$rowConsulta[$campo];
			if (file_exists($pic) AND strlen($rowConsulta[$campo])>0) {
				unlink($pic);
			}

			if($actualizar = $CONEXION->query("UPDATE $seccion SET $campo = NULL WHERE id = $id")){
				$exito=1;
			}
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir Imagen     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_GET['uploadadfile'])){
		$rutaInicial="../library/upload-file/php/uploads/";
		
		$imagenName=$_GET['uploadadfile'];
		$campo=$_GET['campo'];

		$i = strrpos($imagenName,'.');
		$l = strlen($imagenName) - $i;
		$ext = strtolower(substr($imagenName,$i+1,$l));

		// Guardar en la base de datos
		if(file_exists($rutaInicial.$imagenName)){
			$imgFinal=rand(111111111,999999999).'.'.$ext;
			if(file_exists($rutaFinal.$imagenName)){
				$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE imagen = '$imagenName' AND id != $id");
				$numPics=$CONSULTA->num_rows;
				if ($numPics==0) {
					unlink($rutaFinal.$imagenName);
				}
			}
			$CONSULTA = $CONEXION -> query("SELECT * FROM $seccion WHERE id = $id");
			$row_CONSULTA = $CONSULTA -> fetch_assoc();
			if ($row_CONSULTA['imagen']!='' AND file_exists($rutaFinal.$row_CONSULTA['imagen'])) {
				unlink($rutaFinal.$row_CONSULTA['imagen']);
			}
			copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
			$actualizar = $CONEXION->query("UPDATE $seccion SET $campo = '$imgFinal' WHERE id = $id");
		}else{
			$fallo=1;
			$legendFail='<br>No se permite refrescar la página.';
		}

		// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		// Borramos las imágenes que estén remanentes en el directorio files
		// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		$filehandle = opendir($rutaInicial); // Abrir archivos
		while ($file = readdir($filehandle)) {
			if ($file != "." && $file != ".." && $file != ".gitignore" && $file != ".htaccess" && $file != "thumbnail") {
				if(file_exists($rutaInicial.$file)){
					//echo $ruta.$file.'<br>';
					unlink($rutaInicial.$file);
				}
			}
		} 
		closedir($filehandle); 
	}

