<?php
// Por default, no permitimos el acceso
$acceso=false;
$univel=false;
$uid=0;

// Si la cookie existe, la asignamos a la variable cookie para permitir el acceso posteriormente
if (isset($_COOKIE["ukgfrpopdgxv"])){ $cookie=$_COOKIE["ukgfrpopdgxv"]; }else{ $cookie=false; }

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             Cerrando sesión
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if(isset($_GET['logout'])){
	// Borramos la cookie
	$value = ':P';
	setcookie("ukgfrpopdgxv", $value, time() - 3600);
	setcookie("uid", $value, time() - 3600);
	setcookie("nivel", $value, time() - 3600);
	$_COOKIE["ukgfrpopdgxv"]=false;
	$_COOKIE["uid"]=false;
	$_COOKIE["nivel"]=false;
	$cookie=false;
}else{
	
	if($cookie == "jhfhgadfadgshfagusdfibcjhfgvsahbdfasbdfhhfasshcvvas84sd5as35d2g2jh3adf2a2ddjfhged8wifgvbcmgtgws8vf8") { 
		$acceso=1;
		$uid=$_COOKIE["uid"];
		$univel=$_COOKIE["nivel"];
	}else{
		if(isset($_POST["user"]) and strlen($_POST["pass"])>5){

			// Obtenemos los datos del formulario
			$form_user = mysqli_real_escape_string($CONEXION,strtolower($_POST["user"]));
			$form_pass = mysqli_real_escape_string($CONEXION,$_POST["pass"]);

			// Obtenemos la información de la base de datos
			$USER = $CONEXION->query("SELECT * FROM user WHERE user = '$form_user'");
			$row_USER = $USER->fetch_assoc();
			$db_pass=$row_USER['pass'];
			$db_level=$row_USER['nivel'];
			$db_uid=$row_USER['id'];
			
			// Encriptamos la contraseña enviada desde el formulario

			$form_pass_encripted = md5($form_pass);
			
			//dbtestinternal
			
				//$form_pass_encripted = $form_pass;
			
			// Hacemos la comparación de los datos
			if($form_pass_encripted==$db_pass){
				
				//Los datos son correctos, creamos las cookies
				$value = 'jhfhgadfadgshfagusdfibcjhfgvsahbdfasbdfhhfasshcvvas84sd5as35d2g2jh3adf2a2ddjfhged8wifgvbcmgtgws8vf8';
				setcookie("ukgfrpopdgxv", $value, time() + 999999, null, null, null, true);
				setcookie("nivel", $db_level, time() + 999999, null, null, null, true);
				setcookie("uid", $db_uid, time() + 999999, null, null, null, true);
				$acceso=1;
				$uid=$db_uid;
				$univel=$db_level;
			}
		}
	}
}	
?>