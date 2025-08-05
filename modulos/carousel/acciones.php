<?php
//%%%%%%%%%%%%%%%%%%%%%%%%%%    Borrar Imagen      	 %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_GET['borrar'])){
		// Borramos de la base de datos
		if($borrar = $CONEXION->query("DELETE FROM carousel WHERE id = $id")){
			$legendSuccess.= "<br> Imagen eliminada";
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

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Editar   		     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_POST['editar'])){
		$url=htmlentities($_POST['url']);

		if(
			$actualizar = $CONEXION->query("UPDATE carousel SET url 	= '$url' WHERE id = $id")
			)
		{
			$exito=1;
			$legendSuccess .= "<br>Actualizar";
		}else{
			$fallo=1;  
			$legendFail .= "<br>No se pudo modificar la base de datos";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%    Subir Imagen     	 %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_GET['imagen'])){

		// Imagenes a crear
		$orig=0;
		$xs=1;
		$sm=0;
		$med=0;
		$og=0;
		$nat400=0;
		$nat800=1;
		$nat1500=1;
		$Otra=0;

		// Dimensiones
		// Small
		$anchoXS=100;
		$altoXS =100;
		// Small
		$anchoSM=250;
		$altoSM =250;
		// Mediana
		$anchoMED=500;
		$altoMED =500;
		// OG
		$anchoOG=1000;
		$altoOG =700;
		// Otra
		$anchoOtra=1920;
		$altoOtra =780;


		//Obtenemos la extensión de la imagen
		$imagenName=$_GET['imagen'];
		$i = strrpos($imagenName,'.');
		$l = strlen($imagenName) - $i;
		$ext = strtolower(substr($imagenName,$i+1,$l));

		// Si no es JPG cancelamos
		if ($ext!='jpg' and $ext!='jpeg') {
			$fallo=1;
			$legendFail='<br>El archivo debe ser JPG';
		}

		$rutaInicial="../library/upload-file/php/uploads/";
		if(!file_exists($rutaInicial.$imagenName)){
			$fallo=1;
			$legendFail='<br>No se permite refrescar la página.';
		}

		if (!isset($fallo)) {
			$sql = "INSERT INTO carousel (orden) VALUES ('99')";
			if($insertar = $CONEXION->query($sql)){

				$id = $CONEXION->insert_id;

				$pic=$id;
				$imagenName=$_REQUEST['imagen'];

				$rutaFinal="../img/contenido/".$seccion."/";
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
					if ($ext=='jpg' or $ext=='jpeg') $original = imagecreatefromjpeg($imgAux);
					//if ($ext=='png') $new_pic = imagecreatefrompng($imgAux); 	Para futuro
					//if ($ext=='gif') $new_pic = imagecreatefromgif($imgAux);	para futuro

					// Tomamos las dimensiones de la imagen original
					$ancho  = imagesx($original);
					$alto   = imagesy($original);


					if ($orig==1) {
						//  Imagen nat400
						$newName=$pic."-orig.jpg";
						$anchoNuevo = $ancho;
						$altoNuevo  = $alto;

						// Creamos la imagen
						$imagenAux = imagecreatetruecolor($anchoNuevo,$altoNuevo); 
						// Copiamos el contenido de la original para pegarlo en el archivo nuevo
						imagecopyresampled($imagenAux,$original,0,0,0,0,$anchoNuevo,$altoNuevo,$ancho,$alto);
						// Pegamos el contenido de la imagen
						if(imagejpeg($imagenAux,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
							$exito=1;
						}
					}

					if ($xs==1) {
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						//  Imagen Pequeña
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						$newName=$pic."-xs.jpg";

						$anchoNew=$anchoXS;
						$altoNew =$altoXS;

						// Proporcionalmente, la imagen es más ancha que la de destino
						if($ancho/$alto>$anchoNew/$altoNew){
							// Ancho proporcional
							$anchoProporcional=$ancho/$alto*$altoNew;
							// Excedente 
							$excedente=$anchoProporcional-$anchoNew;
							// Posición inicial de la coordenada x
							$xinicial= -$excedente/2;
						}else{
							// Alto proporcional
							$altoProporcional=$alto/$ancho*$anchoNew;
							// Excedente
							$excedente=$altoProporcional-$altoNew;
							// Posición inicial de la coordenada y
							$yinicial= -$excedente/2;
						}

						// Copiamos el contenido de la original para pegarlo en el archivo New
						$New = imagecreatetruecolor($anchoNew,$altoNew); 

						if(isset($xinicial)){
							imagecopyresampled($New,$original,$xinicial,0,0,0,$anchoProporcional,$altoNew,$ancho,$alto);
						}else{
							imagecopyresampled($New,$original,0,$yinicial,0,0,$anchoNew,$altoProporcional,$ancho,$alto);
						}

						// Pegamos el contenido de la imagen
						if(imagejpeg($New,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
							$exito=1;
							//$legendSuccess .= "<br>Imagen pequeña agregada";
						}
					}

					if ($sm==1) {
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						//  Imagen Pequeña
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						$newName=$pic."-sm.jpg";

						$anchoNew=$anchoSM;
						$altoNew =$altoSM;

						// Proporcionalmente, la imagen es más ancha que la de destino
						if($ancho/$alto>$anchoNew/$altoNew){
							// Ancho proporcional
							$anchoProporcional=$ancho/$alto*$altoNew;
							// Excedente 
							$excedente=$anchoProporcional-$anchoNew;
							// Posición inicial de la coordenada x
							$xinicial= -$excedente/2;
						}else{
							// Alto proporcional
							$altoProporcional=$alto/$ancho*$anchoNew;
							// Excedente
							$excedente=$altoProporcional-$altoNew;
							// Posición inicial de la coordenada y
							$yinicial= -$excedente/2;
						}

						// Copiamos el contenido de la original para pegarlo en el archivo New
						$New = imagecreatetruecolor($anchoNew,$altoNew); 

						if(isset($xinicial)){
							imagecopyresampled($New,$original,$xinicial,0,0,0,$anchoProporcional,$altoNew,$ancho,$alto);
						}else{
							imagecopyresampled($New,$original,0,$yinicial,0,0,$anchoNew,$altoProporcional,$ancho,$alto);
						}

						// Pegamos el contenido de la imagen
						if(imagejpeg($New,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
							$exito=1;
							//$legendSuccess .= "<br>Imagen pequeña agregada";
						}
					}

					if ($med==1) {
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						//  Imagen Mediana
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						$newName=$pic."-crop.jpg";

						$anchoNew=$anchoMED;
						$altoNew =$altoMED;

						// Proporcionalmente, la imagen es más ancha que la de destino
						if($ancho/$alto>$anchoNew/$altoNew){
							// Ancho proporcional
							$anchoProporcional=$ancho/$alto*$altoNew;
							// Excedente 
							$excedente=$anchoProporcional-$anchoNew;
							// Posición inicial de la coordenada x
							$xinicial= -$excedente/2;
						}else{
							// Alto proporcional
							$altoProporcional=$alto/$ancho*$anchoNew;
							// Excedente
							$excedente=$altoProporcional-$altoNew;
							// Posición inicial de la coordenada y
							$yinicial= -$excedente/2;
						}

						// Copiamos el contenido de la original para pegarlo en el archivo New
						$New = imagecreatetruecolor($anchoNew,$altoNew); 

						if(isset($xinicial)){
							imagecopyresampled($New,$original,$xinicial,0,0,0,$anchoProporcional,$altoNew,$ancho,$alto);
						}else{
							imagecopyresampled($New,$original,0,$yinicial,0,0,$anchoNew,$altoProporcional,$ancho,$alto);
						}

						// Pegamos el contenido de la imagen
						if(imagejpeg($New,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
							$exito=1;
							//$legendSuccess .= "<br>Imagen pequeña agregada";
						}
					}

					if ($og==1) {
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						//  Imagen OG
						// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						$newName=$pic."-og.jpg";

						$anchoNew=$anchoOG;
						$altoNew =$altoOG;

						// Proporcionalmente, la imagen es más ancha que la de destino
						if($alto/$ancho>(.7)){
							// Ancho proporcional
							$anchoProporcional=$ancho/$alto*$altoNew;
							// Excedente 
							$excedente=$anchoProporcional-$anchoNew;
							// Posición inicial de la coordenada x
							$xinicial= -$excedente/2;
						}else{
							// Alto proporcional
							$altoProporcional=$alto/$ancho*$anchoNew;
							// Excedente
							$excedente=$altoProporcional-$altoNew;
							// Posición inicial de la coordenada y
							$yinicial= -$excedente/2;
						}

						// Copiamos el contenido de la original para pegarlo en el archivo New
						$New = imagecreatetruecolor($anchoNew,$altoNew); 

						if(isset($xinicial)){
							imagecopyresampled($New,$original,$xinicial,0,0,0,$anchoProporcional,$altoNew,$ancho,$alto);
						}else{
							imagecopyresampled($New,$original,0,$yinicial,0,0,$anchoNew,$altoProporcional,$ancho,$alto);
						}

						// Pegamos el contenido de la imagen
						if(imagejpeg($New,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
							$exito=1;
							//$legendSuccess .= "<br>Imagen OG agregada";
						}
					}

					if ($nat400==1) {
						//  Imagen nat400
						$newName=$pic."-nat400.jpg";
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

					if ($nat800==1) {
						//  Imagen nat1000
						$newName    = $pic."-nat800.jpg";
						$anchoNuevo = 800;
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

					if ($nat1500==1) {
						//  Imagen nat1500
						$newName=$pic.".jpg";
						if ($ancho>$alto) {
							$anchoNuevo = 1500;
							$altoNuevo  = $anchoNuevo*$alto/$ancho;
						}else{
							$altoNuevo  = 1500;
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

					if ($Otra==1) {
						//  Imagen Otra
						$newName=$pic."-otra.jpg";
						$anchoNew=$anchoOtra;
						$altoNew =$altoOtra;
						$dst_x=0;
						$dst_y=0;
						$src_x=0;
						$src_y=0;
						$dst_w=$ancho;
						$dst_h=$alto;
						$src_w=$ancho;
						$src_h=$alto;

						// Proporcionalmente, la imagen es más ancha que la de destino
						if($ancho/$alto>$anchoNew/$altoNew){
							// Ancho proporcional
							$anchoProporcional=$ancho/$alto*$altoNew;
							// Corregimos el ancho
							$dst_w=$anchoProporcional;
							// Corregimos el ancho
							$dst_h=$altoNew;
							// Excedente 
							$excedente=$anchoProporcional-$anchoNew;
							// Posición inicial de la coordenada x
							$src_x= $excedente/2;
							//$legendSuccess.='<br>Opt 2';
						}else{
							// Ancho proporcional
							$altoProporcional=$alto/$ancho*$anchoNew;
							// Corregimos el alto
							$dst_h=$altoProporcional;
							// Corregimos el ancho
							$dst_w=$anchoNew;
							// Excedente
							$excedente=$altoProporcional-$altoNew;
							// Posición inicial de la coordenada y
							$src_y= $excedente/4;
							//$legendSuccess.='<br>Opt 2';
							//$legendSuccess.='<br>Alto Original: '.$alto;
							//$legendSuccess.='<br>Alto Nuevo: '.$altoNew;
							//$legendSuccess.='<br>Alto Proporcional: '.$altoProporcional;
							//$legendSuccess.='<br>Excedente: '.$excedente;
							//$legendSuccess.='<br>Src Y: '.$src_y;
						}

						// Copiamos el contenido de la original para pegarlo en el archivo New
						$New = imagecreatetruecolor($anchoNew,$altoNew); 

						imagecopyresampled($New,$original,$dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h);

						// Pegamos el contenido de la imagen
						if(imagejpeg($New,$rutaFinal.$newName,90)){ // 90 es la calidad de compresión
							$exito=1;
							//$legendSuccess .= "<br>Imagen Otra agregada";
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
		}else{
			$legendFail.= '
			<br>No pudo subirse la imagen';
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



