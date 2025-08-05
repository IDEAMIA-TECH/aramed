<?php
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             Configuración del sistema
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	require_once('includes/config.php');
	
	// Cargar configuración específica de desarrollo si es necesario
	if (IS_DEVELOPMENT) {
		require_once('includes/dev-config.php');
		showDevInfo(); // Mostrar información de desarrollo
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             Conectando con la base de datos
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	require_once('includes/connection.php');
	require_once('includes/widgets.php');


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             Server data 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$dominio=$_SERVER["SERVER_NAME"];
	$dominio=str_replace('www.', '', $dominio);

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             Constantes 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$originalPic=0; // 1: Conservar imagen original --- 0: Emilinar imagen original
	$legendSuccess='';
	$legendFail='';
	$debug=0;
	if($dominio=='localhost' or $dominio=='ideamia-dev.com'){
		$debug=1;
	}


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             Obteniendo variables
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$id = (isset($_REQUEST['id'])) ? cleanInput($_REQUEST['id']) : $id=false;
	$seccion = (isset($_REQUEST['seccion'])) ? cleanInput($_REQUEST['seccion']) : $seccion='noticias';
	$subseccion = (isset($_REQUEST['subseccion'])) ? cleanInput($_REQUEST['subseccion']) : $subseccion='contenido';
	$cat = (isset($_REQUEST['cat'])) ? cleanInput($_REQUEST['cat']) : $cat=false;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             LOGIN 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	try {
		require_once("modulos/varios/login_proceso.php");
		require_once('modulos/varios/includes.php');
		if(!isset($acceso) or $acceso==false){ 
			require_once("modulos/varios/login.php");
		} 
	} catch (Exception $e) {
		logActivity('ERROR_LOGIN', $e->getMessage());
		if (IS_DEVELOPMENT) {
			devLog('Error en login: ' . $e->getMessage(), 'ERROR');
		}
		header("HTTP/1.1 500 Internal Server Error");
		include_once('pages/500.php');
		exit;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//                             Mostrando el diseño interior
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if(isset($acceso) and $acceso==1){ 
		try {
			require_once('modulos/'.$seccion.'/acciones.php');
			require_once('modulos/'.$seccion.'/inicio.php');
		} catch (Exception $e) {
			logActivity('ERROR_MODULE', $e->getMessage() . ' - Sección: ' . $seccion);
			if (IS_DEVELOPMENT) {
				devLog('Error en módulo: ' . $e->getMessage() . ' - Sección: ' . $seccion, 'ERROR');
			}
			header("HTTP/1.1 500 Internal Server Error");
			include_once('pages/500.php');
			exit;
		}
	} 

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//							Borrar Error_log si existe
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//if (file_exists('error_log')) {
		//unlink('error_log');
	//}
	
	if (isset($CONEXION)) {
		mysqli_close($CONEXION);
	}
	
	// Mostrar estadísticas de desarrollo al final si es necesario
	if (IS_DEVELOPMENT) {
		showDevStats();
	}
	
	flush();