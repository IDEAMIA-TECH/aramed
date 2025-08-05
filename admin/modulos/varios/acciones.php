<?php
	include '../../../includes/connection.php';

	$id		= (isset($_POST['id']))		? $_POST['id']		:'';
	$tabla	= (isset($_POST['tabla']))	? $_POST['tabla']	:'';
	$campo	= (isset($_POST['campo']))	? $_POST['campo']	:'';
	$valor	= (isset($_POST['valor']))	? trim($_POST['valor'])	:'';
	$orden	= (isset($_POST['orden']))	? $_POST['orden']	:'';

//	Editar con AJAX     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if (isset($_POST['editarajax'])) {
		if($actualizar = $CONEXION->query("UPDATE $tabla SET $campo = '$valor' WHERE id = $id")){
			$mensajeClase='success';
			$mensajeIcon='check';
			$mensaje='Guardado';
		}else{
			$mensajeClase='danger';
			$mensajeIcon='ban';
			$mensaje='No se pudo guardar';
		}
	}

//	Relacionar con AJAX    %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($_POST['relajax'])) {
		$CONSULTA = $CONEXION -> query("SELECT * FROM $tabla WHERE producto = $id AND valor = $valor");
		$numRows=$CONSULTA->num_rows;

		if ($numRows>0) {
			$borrar = $CONEXION->query("DELETE FROM $tabla WHERE producto = $id AND valor = $valor");
			$mensajeClase='success';
			$mensajeIcon='check';
			$mensaje='Borrado';				
		}else{
			$sql = "INSERT INTO $tabla (producto,valor) VALUES ($id,$valor)";
			if($insertar = $CONEXION->query($sql)){
				$mensajeClase='success';
				$mensajeIcon='check';
				$mensaje='Guardado';
			}else{
				$mensajeClase='danger';
				$mensajeIcon='ban';
				$mensaje='No se pudo guardar';
			}
		}
	}

//	Ordenar
	if (isset($_POST['orderanarjax'])) {
		foreach ($orden as $key => $value) {
			$actualizar = $CONEXION->query("UPDATE $tabla SET orden = $key WHERE id = '$value'");
			$exito=1;
		}
		if ($exito==1) {
			$mensajeClase='success';
			$mensajeIcon='check';
			$mensaje='Guardado';
		}else{
			$mensajeClase='danger';
			$mensajeIcon='ban';
			$mensaje='No se pudo guardar';
		}
	}

//	Cambiar valor de toda una columna
	if (isset($_POST['changeall'])) {
			$actualizar = $CONEXION->query("UPDATE $tabla SET $campo = '$valor'");
			$exito=1;
		if ($exito==1) {
			$mensajeClase='success';
			$mensajeIcon='check';
			$mensaje='Guardado';
		}else{
			$mensajeClase='danger';
			$mensajeIcon='ban';
			$mensaje='No se pudo guardar';
		}
	}


echo '<div class="uk-text-center color-white bg-'.$mensajeClase.' padding-10 text-lg"><i class="fa fa-'.$mensajeIcon.'"></i> &nbsp; '.$mensaje.'</div>';		



