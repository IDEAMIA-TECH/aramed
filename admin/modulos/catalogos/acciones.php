<?php
$seccion='catalogos';

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Nuevo catálogo     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_POST['nuevocatalogo']) AND isset($_POST['titulo'])){
		$titulo	= trim(htmlentities($_POST['titulo']));

		$sql = "INSERT INTO $seccion (titulo) VALUES ('$titulo')";
		if($insertar = $CONEXION->query($sql)){
			$id = $CONEXION->insert_id;
			$legendSuccess.= "<br> Agregado";
			$exito='success';
		}else{
			$legendFail .= "<br>No se pudo borrar de la base de datos";
			$fallo='danger';  
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Imagen     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_GET['borrar'])){
		// Borramos de la base de datos
		if($borrar = $CONEXION->query("DELETE FROM $seccion WHERE id = $id")){
			$legendSuccess.= "<br>Catálogo eliminado";
			$exito='success';
		}else{
			$legendFail .= "<br>No se pudo borrar de la base de datos";
			$fallo='danger';  
		}
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

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Ordenar $seccion     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['list'])) {
		include '../../../includes/connection.php';

		$list = $_POST['list'];
		$num=1;

		foreach ($list as $lista) {
			$actualizar = $CONEXION->query("UPDATE $seccion SET orden = $num WHERE id = '$lista'");
			$num++;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Ordenar $seccion     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['editar'])) {
		include '../../../includes/connection.php';

		$id = $_POST['id'];
		$titulo = $_POST['titulo'];

		if ($actualizar = $CONEXION->query("UPDATE $seccion SET titulo = '$titulo' WHERE id = '$id'")) {
			echo '<span class="uk-text-success"><span uk-icon="icon: check"></span> Guardado</span>';
		}else{
			echo '<span class="uk-text-danger"><span uk-icon="icon: warning"></span> No se pudo guardar</span>';
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir Imagen     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_GET['uploadedfile'])){
		$campo = $_GET['campo'];

		//Obtenemos la extensión del archivo
		$fileInicial=$_GET['uploadedfile'];
		$i = strrpos($fileInicial,'.');
		$l = strlen($fileInicial) - $i;
		$ext = strtolower(substr($fileInicial,$i+1,$l));

		if ($ext!='pdf' AND $campo=='pdf') {
			$fallo=1;
			$legendFail='<br>El archivo debe ser PDF';
		}elseif ($campo=='imagen') {
			if ($ext!='jpg' AND $ext!='jpeg' AND $ext!='png' AND $ext!='gif') {
				$fallo=1;
				$legendFail='<br>Debe subir una imagen - '.$ext;
			}
		}

		$rutaInicial="../library/upload-file/php/uploads/";
		if(!file_exists($rutaInicial.$fileInicial)){
			$fallo=1;
			$legendFail='<br>No se subió el archivo';
		}

		if (!isset($fallo)) {
			$fileFinal=date('Ymd').rand(1000,9999).'.'.$ext;
			$rutaFinal="../img/contenido/".$seccion."/";

			if ($actualizar = $CONEXION->query("UPDATE $seccion SET $campo = '$fileFinal' WHERE id = $id")) {
				$exito=1;
			}
				
			if (copy($rutaInicial.$fileInicial, $rutaFinal.$fileFinal)) {
				$exito=2;
			}

			if($exito=2){
				$legendSuccess .= "<br>Archivo actualizado";
			}elseif ($exito==1) {
				unset($exito);
				$legendFail .= "<br>No se pudo mover el archivo a su destino final";
			}
		}


		// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		// Borramos las imágenes que estén remanentes en el directorio de carga
		// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		$filehandle = opendir($rutaInicial); // Abrir archivos
		while ($file = readdir($filehandle)) {
			if ($file != "." && $file != ".." && $file != ".gitignore" && $file != ".htaccess" && $file != "thumbnail") {
				if(file_exists($rutaInicial.$file)){
					//echo $rutaInicial.$file.'<br>';
					unlink($rutaInicial.$file);
				}
			}
		} 
		closedir($filehandle); 
	}



