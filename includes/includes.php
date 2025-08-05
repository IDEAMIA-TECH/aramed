<?php
/* %%%%%%%%%%%%%%%%%%%% MENSAJES               */
	if($mensaje!=''){
		$mensajes='
			<div class="uk-container">
				<div uk-grid>
					<div class="uk-width-1-1 margen-v-20">
						<div class="uk-alert-'.$mensajeClase.'" uk-alert>
							<a class="uk-alert-close" uk-close></a>
							'.$mensaje.'
						</div>					
					</div>
				</div>
			</div>';
	}

/* %%%%%%%%%%%%%%%%%%%% RUTAS AMIGABLES        */
		$rutaInicio			=	$ruta;
		$rutaAbout			=	$ruta.'about';
		$rutaCategoria		=	$ruta.'categorias';
		$rutaCatalogos		=	$ruta.'catalogos';
		$rutaCotizar		= 	$ruta.'cotizar';
		$rutaServicios		=	$ruta.'servicios';
		$rutaProyectos		=	$ruta.'proyectos';
		$rutaNoticias		= 	$ruta.'noticias';
		$rutaContacto		=	$ruta.'contacto';
		$rutaProducto		= 	$ruta.'productos';
		$rutaDetProducto	= 	$ruta.'detalle_producto';
		$rutaDetNoticias	= 	$ruta.'detalle_noticias';
		$rutaDetProyecto	= 	$ruta.'detalle_proyecto';
		$rutaLogin			=	$ruta.'login';
		$rutaSubcategoria	=	$ruta.'subcategoria';


/* %%%%%%%%%%%%%%%%%%%% MENU                   */
	$menu='
		<li class="'.$nav1.'"><a href="'.$rutaInicio.'">Inicio</a></li>
		<li class="'.$nav2.'"><a href="'.$rutaAbout.'">Acerca de Aramed</a></li>
		<li class="'.$nav3.'"><a href="'.$rutaCategoria.'">Productos</a></li>
		<li class="'.$nav9.'"><a href="'.$rutaCatalogos.'">Catálogos</a></li>
		<li class="'.$nav5.'"><a href="'.$rutaServicios.'">Servicios</a></li>
		<li class="'.$nav6.'"><a href="'.$rutaProyectos.'">Proyectos</a></li>
		<li class="'.$nav7.'"><a href="'.$rutaNoticias.'">Noticias</a></li>
		<li class="'.$nav8.'"><a href="'.$rutaContacto.'">Contacto</a></li>

		';

	$menuMovil='
		<li><a class="'.$nav1.'" href="'.$rutaInicio.'">Inicio</a></li>
		<li><a class="'.$nav2.'" href="'.$rutaAbout.'">Acerca de Aramed</a></li>
		<li><a class="'.$nav3.'" href="'.$rutaCategoria.'">Productos</a></li>
		<li><a class="'.$nav9.'" href="'.$rutaCatalogos.'">Catálogos</a></li>
		<li><a class="'.$nav5.'" href="'.$rutaServicios.'">Servicios</a></li>
		<li><a class="'.$nav6.'" href="'.$rutaProyectos.'">Proyectos</a></li>
		<li><a class="'.$nav7.'" href="'.$rutaNoticias.'">Noticias</a></li>
		<li><a class="'.$nav8.'" href="'.$rutaContacto.'">Contacto</a></li>
		';

/* %%%%%%%%%%%%%%%%%%%% HEADER                 */
	$header='
		<header class="padding-v-5">
		  <div uk-grid class="uk-grid-collapse">
		    <div class="uk-width-expand" style="margin-left: 40px;">
		      <img src="img/design/logo.png" style="max-height: 100px; padding: 10px 0;">
		    </div>
		    <div class="uk-width-auto uk-hidden@s" style="padding: 20px 20px 0 0;">
		    	<button type="button" uk-toggle="target: #offcanvas-flip" uk-icon="icon: menu; ratio: 2;"></button>
		    </div>
		    <div class="uk-width-auto@s uk-flex uk-text-right@s uk-text-center">
			    <div class="uk-width-1-1 login-padding">
					<div class="padding-v-20 uk-width-1-1">
						<div class="padding-top-10 color-primary text-lg">
							Tel: '.$telefono.'
						</div>
					</div>
			    </div>
		    </div>
		  </div>
		  <div class="uk-width-1-1 uk-visible@s" style="background: #0066ad;" uk-sticky>
		    <nav class="uk-navbar-container uk-navbar-transparent" uk-navbar>
		      <div class="uk-navbar-right ">
		        <ul class="uk-navbar-nav">
		          '.$menu.'
		        </ul>
		      </div>
		    </nav>
		  </div>
		  <div id="offcanvas-flip" uk-offcanvas="flip: true; overlay: true">
			    <div class="uk-offcanvas-bar">
			        <button class="uk-offcanvas-close" type="button" uk-close></button>
					<ul class="uk-nav uk-nav-primary uk-nav-parent-icon uk-nav-center uk-margin-auto-vertical" style="padding-top: 20px;" uk-nav>
						'.$menuMovil.'
			        </ul>
			    </div>
			</div>
		</header>
      	'.$mensajes;

/* %%%%%%%%%%%%%%%%%%%% FOOTER                 */
	$footer = '
	<footer style="background: #0063a0; min-height:300px; color: white; overflow:hidden;">
		<div class="uk-flex uk-flex-center padding-top-50">
			<a class="uk-width-1-3@m margin-20 uk-text-center boton-mapa-sitio padding-v-5" href="sitemap">
				Navega en nuestro mapa de sitio
			</a>
		</div>
	    <div uk-grid class="uk-child-width-1-3@m uk-text-center padding-top-50">
	      <div>
	        <div style="font-size: 20px; font-weight: 600; padding-bottom: 30px;">
	          HORARIOS
	        </div>
	        <div>
	          Lunes - Viernes: 9:00am - 7:00pm<br>
	          Sábados: 10:00am - 2:00pm
	        </div>
	        <div>
	          
	        </div>
	      </div>
	      <div>
	        <div style="font-size: 20px; font-weight: 600; padding-bottom: 30px;">
	          ¿NECESITAS AYUDA?
	        </div>
	        <div>
	          Escribenos a <br>
	          atencionacliente@aramedylaboratorio.com
	        </div>
	      </div>
	      <div>
	        <div style="font-size: 20px; font-weight: 600; padding-bottom: 30px;">
	          SIGUENOS EN NUESTRAS REDES 
	        </div>
	        <div class="uk-flex uk-flex-center">
	          <div uk-grid>
	                <div class="uk-width-auto"><a href="'.$socialFace.'" target="_blank" class="uk-icon-link color-blanco redes-facebook " uk-icon="icon: facebook; ratio: 1.5"></a></div>
	                <div class="uk-width-auto"><a href="#" target="_blank" class="uk-icon-link color-blanco redes-linkedin" uk-icon="icon: linkedin; ratio: 1.5" ></a></div>
	                <div class="uk-width-auto"><a href="'.$socialInst.'" target="_blank"><div class="redes-instagram transicion"></div></a></div>
	                <div class="uk-width-auto"><a href="'.$socialYout.'" target="_blank" class="uk-icon-link color-blanco redes-youtube" uk-icon="icon: youtube; ratio: 1.5" ></a></div>
              	</div>
	        </div>
	      </div>
	    </div>

	    <div class="uk-width-1-1 uk-text-center">
	      <div class="padding-v-50">
	        Aramed '.date('Y').' todos los derechos reservados Diseño por <a href="https://wozial.com/" target="_blank" class="color-white">Wozial Marketing Lovers</a>
	      </div>
	    </div>
	</footer>

    <div id="totop">
      <a href="#top" uk-scroll id="arrow-button" class="uk-icon-button uk-button-totop uk-box-shadow-large oscuro"><i class="fa fa-arrow-up fa-1x" aria-hidden="true"></i></a>
    </div>
  </div>
    ';

/* %%%%%%%%%%%%%%%%%%%% SCRIPTS                */
	$headGNRL='
		<meta name="viewport"       content="width=device-width, initial-scale=1">

		<link rel="icon"            href="'.$ruta.'img/design/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon"   href="img/design/favicon.ico" type="image/x-icon">
    	<link rel="stylesheet"      href="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/css/uikit.min.css" />
		<link rel="stylesheet"      href="css/general.css">
		<link rel="stylesheet"		href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,600,700,800,900">
		<link rel="stylesheet"      href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">


		<!-- jQuery is required -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<!-- UIkit JS -->
   		<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/js/uikit.min.js"></script>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/js/uikit-icons.min.js"></script>

		';

	$scriptGNRL='
		<script src="js/general.js"></script>
		';



/* %%%%%%%%%%%%%%%%%%%% BUSQUEDA               */
	$scriptGNRL.='
		<script>
			$(document).ready(function(){
				$("#search").keyup(function(e){
					if(e.which==13){
						var consulta=$(this).val();
						var l = consulta.length;
						if(l>2){
							window.location = ("'.$ruta.'"+consulta+"-aramed.php");
						}else{
							UIkit.notification.closeAll();
							UIkit.notification("<div class=\'uk-text-center color-blanco bg-danger padding-10 text-lg\'><i class=\'fa fa-exclamation-triangle fa-lg\'></i> &nbsp; Se requiren al menos 3 caracteres</div>");
						}
					}
				})
				$("#search-button").click(function(){
					var consulta=$("#search").val();
					console.log(consulta);
					var l = consulta.length;
					if(l>2){
						window.location = ("'.$ruta.'"+consulta+"-aramed.php");
					}else{
						UIkit.notification.closeAll();
						UIkit.notification("<div class=\'uk-text-center color-blanco bg-danger padding-10 text-lg\'><i class=\'fa fa-exclamation-triangle fa-lg\'></i> &nbsp; Se requiren al menos 3 caracteres</div>");
					}
				})
			});
		</script>';
