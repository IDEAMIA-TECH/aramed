<?php 
/* %%%%%%%%%%%%%%%%%%%% CONFIGURACIÓN          */
	$Brand			= 'Aramed';
	$logo 			= 'img/design/logo.png';
	$mailBGcolor 	= '#DDD';
	$mailButton 	= '#ff8b00';
	$uikitVersion	= '3.0.3';
	$languaje		= 'es';
	$appID			= '298662384195873';
	$googleMaps		= 'AIzaSyAA_lgquFXipMbdNCXe5EFDxbUdoHZBBlg';
	$efra			= 'info@efra.biz';

	$database = 'ideamiadev_aramed';
	$username = 'ideamiadev_aramed';
	$password = 'xS@A5Pm3EDc3BEdj';

	$databaseLocal 	= 'aramed';
	$usernameLocal 	= 'root';
	$passwordLocal 	= 'minimusa2000';

	$previewDomain   = 'wozialmarketinglovers.com';
	$databasePreview = 'wozialma_aramed';
	$usernamePreview = 'wozialma_wozial';
	$passwordPreview = 'LjI)en}p5RDa';

/* %%%%%%%%%%%%%%%%%%%% OTRAS VARIABLES        */
	global $ruta;
	global $rutaEstaPagina;
	global $CONEXION;
    global $uid;
	global $caracteres_si_validos;
	global $caracteres_no_validos;
	global $linkToShare;
	global $es_movil;

	$caracteres_si_validos  = array('','','','','','','','a','A','e','E','i','I','o','O','u','U','n','N');
	$caracteres_no_validos  = array('_','|','/','®','¿','"',':','á','Á','é','É','í','Í','ó','Ó','ú','Ú','ñ','Ñ');
	$noPic='img/design/camara.jpg';

	$mensajeClase='';
	$mensajes='';
	$mensaje='';

	date_default_timezone_set('America/Mexico_City');
	$hoy=date('Y-m-d');
	$ahora=date('Y-m-d H:i:s');

	$dominio=str_replace('www.', '', $_SERVER["SERVER_NAME"]);
	$ip=substr($dominio,0,-2);
	$debug=($dominio=='localhost')?1:0;
	$protocolo=($debug==1)?'http://':'https://';
	$raiz=$protocolo.$dominio;
	$urlSufijo=$_SERVER["REQUEST_URI"];
	$slash=(strrpos($urlSufijo,'/'))+1;
	$ruta=$raiz.substr($urlSufijo,0,$slash);
	$ruta = (str_replace('admin/', '', $ruta));
	$ruta = (str_replace('includes/', '', $ruta));
	$rutaEstaPagina=$raiz.$_SERVER["REQUEST_URI"];
	$linkToShare=str_replace('+', '%2B', $rutaEstaPagina);
	$linkToShare=str_replace(':', '%3A', $linkToShare);
	$logo=$ruta.$logo;

	$hostname = 'localhost';
	
	// Detección mejorada del entorno
	if($dominio=='localhost' or $ip=='216.18.195.84' or $dominio=='ideamia-dev.com'){
		$database 	= $databaseLocal;
		$username 	= $usernameLocal;
		$password 	= $passwordLocal;
		$debug = 1; // Habilitar debug para desarrollo
	}elseif($dominio==$previewDomain){
		$database = $databasePreview;
		$username = $usernamePreview;
		$password = $passwordPreview;
	}

	$CONEXION = mysqli_init();
	mysqli_real_connect($CONEXION, $hostname, $username, $password, $database) or die("Error: " . mysqli_connect_error($CONEXION)); 
	mysqli_set_charset($CONEXION,'utf8');


	// Metadatos
		$CONSULTA          = $CONEXION -> query("SELECT * FROM configuracion WHERE id = 1");
		$row_CONSULTA      = $CONSULTA -> fetch_assoc();

		$title		       = $row_CONSULTA['title'];
		$description       = $row_CONSULTA['description'];
		$logoOg		       = 'img/contenido/varios/'.$row_CONSULTA['imagen1'];
		$logoOg 	       = (file_exists($logoOg)) ? $logoOg:'img/design/logo-og.jpg';

		$prodsPagina       = $row_CONSULTA['prodspag'];
		$shipping          = $row_CONSULTA['envio'];
		$shippingGlobal    = $row_CONSULTA['envioglobal'];
		$payPalCliente     = $row_CONSULTA['paypalemail'];

		$telefono1         = $row_CONSULTA['telefono1'];
		$telefonoSeparado1 = substr($telefono1, 0,2).' '.substr($telefono1, 2,4).' '.substr($telefono1, 6,4);

		$telefono          = $row_CONSULTA['telefono'];
		$telefonoSeparado  = substr($telefono, 0,2).' '.substr($telefono, 2,4).' '.substr($telefono, 6,4);
		
		$socialFace        = $row_CONSULTA['facebook'];
		$socialInst        = $row_CONSULTA['instagram'];
		$socialYout        = $row_CONSULTA['youtube'];
		//$socialLink        = $row_CONSULTA['linkedin'];
		$socialWhats       = 'https://api.whatsapp.com/send?phone=521'.$telefono1.'&amp;text=Me%20gustar%C3%ADa%20saber%20...';

		$RemitenteMail 	   = $row_CONSULTA['remitente'];
		$RemitenteServ 	   = $row_CONSULTA['server'];
		$RemitentePort 	   = $row_CONSULTA['port'];
		$RemitentePass 	   = $row_CONSULTA['pass'];
		$destinatario1 	   = $row_CONSULTA['destinatario1'];
		$destinatario2 	   = $row_CONSULTA['destinatario2'];

	mysqli_free_result($CONSULTA);

/* %%%%%%%%%%%%%%%%%%%% MÓVIL O ESCRITORIO     */
	// Detectamos si es móvil o escritorio
	$navegadorUser = $_SERVER['HTTP_USER_AGENT'];		// Info del navegador
	// Lista de navegadores móviles
	$navegadores_moviles = "Android, AvantGo, Blackberry, Blazer, Cellphone, Danger, DoCoMo, EPOC, EudoraWeb, Handspring, HTC, Kyocera, LG, MMEF20, MMP, MOT-V, Mot, Motorola, NetFront, Newt, Nokia, Opera Mini, Palm, Palm, PalmOS, PlayStation Portable, ProxiNet, Proxinet, SHARP-TQ-GX10, Samsung, Small, Smartphone, SonyEricsson, SonyEricsson, Symbian, SymbianOS, TS21i-10, UP.Browser, UP.Link, WAP, webOS, Windows CE, hiptop, iPhone, iPod, portalmmm, Elaine, OPWV";
	// Almacenar como array
	$navegadores_moviles_array = explode(',',$navegadores_moviles);
	// Ciclo comparativo
	$es_movil=FALSE;
	foreach($navegadores_moviles_array AS $navegadorList){
		if ($es_movil===FALSE) {
			$es_movil=(preg_match("/".trim($navegadorList)."/i",$navegadorUser))?TRUE:FALSE;
		}
	}

/* %%%%%%%%%%%%%%%%%%%% Explorer < 8           */
	if(preg_match('/(?i)msie [1-8]/',$navegadorUser)){
		$mensaje='Este sitio está diseñado para navegadores modernos.<br>Vuelva a visitarnos desde Google Chrome, Firefox o su Dispositivo Movil.';
		$mensajeClase='danger';
	}





