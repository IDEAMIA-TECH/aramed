<?php
$seccion='empresas';
$seccionpic=$seccion.'pic';


//%%%%%%%%%%%%%%%%%%%%%%%%%%    Textos del edito   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['textos'])) {

		$id = $_POST['id'];
		$txt1 = $_POST['txt1'];

		if ($actualizar = $CONEXION->query("UPDATE configuracion SET txt1 = '$txt1' WHERE id = $id")) {
			$exito=1;
			$legendSuccess .= "<br>Editado";
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo guardar";
		}

	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Editar URL de video     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['editaurl'])) {
		include '../../../includes/connection.php';

		$id = $_POST['id'];
		$url = $_POST['url'];

		if($actualizar = $CONEXION->query("UPDATE $seccion SET titulo = '$url' WHERE id = $id")){
			echo '<span class="uk-text-success">Guardado</span>';
		}else{
			echo '<span class="uk-text-danger">Ocurrió un error</span>';
		}
	}		
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Estatus change     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	if (isset($_POST['estatuschange'])) {
		include '../../../includes/connection.php';

		$id = $_POST['id'];
		$estatus = $_POST['estatus'];


		if($actualizar = $CONEXION->query("UPDATE empresas SET estatus = $estatus WHERE id = $id")){
			echo '<span class="uk-text-success">Guardado</span>';
		}else{
			echo '<span class="uk-text-danger">Ocurrió un error</span>';
		}
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Foto     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_REQUEST['borrarPic'])){
		if($borrar = $CONEXION->query("DELETE FROM $seccion WHERE id = $id")){
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
					if($imagenID==$id){
						$pic=$rutaIMG.$file;
						$exito=1;
						unlink($pic);
					}
				}
			}
		}
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Ordenar $seccionpic     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['list2'])) {
		include '../../../includes/connection.php';

		$list = $_POST['list2'];
		$num=1;

		foreach ($list as $lista) {
			$actualizar = $CONEXION->query("UPDATE $seccion SET orden = $num WHERE id = '$lista'");

			$num++;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir Imagen     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	if(isset($_REQUEST['imagen'])){
		$position=$_GET['position'];
		$xs=1;
		$sm=1;
		$lg=1;


		//Obtenemos la extensión de la imagen
		$rutaInicial="../library/upload-file/php/uploads/";
		$imagenName=$_REQUEST['imagen'];
		$i = strrpos($imagenName,'.');
		$l = strlen($imagenName) - $i;
		$ext = strtolower(substr($imagenName,$i+1,$l));


		// Guardar en la base de datos
		
		if(file_exists($rutaInicial.$imagenName)){
			if ($position=='gallery') {
				$rutaFinal='../img/contenido/'.$seccion.'/';
				$sql = "INSERT INTO $seccion (orden) VALUES (99)";
				$insertar = $CONEXION->query($sql);
				$pic=$CONEXION->insert_id;
				$imgFinal=$pic.'.'.$ext;
				copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
			}else{
				$rutaFinal='../img/contenido/'.$seccion.'/';
				$imgFinal=rand(111111111,999999999).'.'.$ext;
				if(file_exists($rutaFinal.$imgFinal)){
					$imgFinal=rand(111111111,999999999).'.'.$ext;
				}
				$CONSULTA = $CONEXION -> query("SELECT imagen1 FROM configuracion WHERE id = $id");
				$row_CONSULTA = $CONSULTA -> fetch_assoc();
				if ($row_CONSULTA['imagen1']!='' AND file_exists($rutaFinal.$row_CONSULTA['imagen1'])) {
					unlink($rutaFinal.$row_CONSULTA['imagen1']);
				}
				copy($rutaInicial.$imagenName, $rutaFinal.$imgFinal);
				$actualizar = $CONEXION->query("UPDATE configuracion SET imagen1 = '$imgFinal' WHERE id = $id");
			}
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



