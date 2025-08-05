<?php
$jquery='
	<!-- JQUERY UI -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


	<!-- Upload Image -->
	<link href="../library/upload-file/css/uploadfile.css" rel="stylesheet">
	<script src="../library/upload-file/js/jquery.uploadfile.js"></script>

	<!-- Editor de texto -->
  <script src="https://cdn.tiny.cloud/1/anll46py9qfyt3e7wz76540x53kz24nt1bn10cqzv5o8fs6e/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

	<!-- Botones fijos -->
	<script src="../js/admin.js"></script>

	';

$navCatalogos=($seccion=='catalogos')?'uk-active':'';
$navCal=($seccion=='calendario')?'uk-active':'';
$navClientes=($seccion=='clientes')?'uk-active':'';
$navConfiguracion=($seccion=='configuracion')?'uk-active':'';
$navEmp=($seccion=='empresas')?'uk-active':'';
$navnoti=($seccion=='noticias')?'uk-active':'';
$navPedidos=($seccion=='pedidos')?'uk-active':'';
$navProductos=($seccion=='productos')?'uk-active':'';
$navPro=($seccion=='proyectos')?'uk-active':'';
$navCarousel=($seccion=='carousel')?'uk-active':'';
$navUsuarios=($seccion=='usuarios')?'uk-active':'';
$navtestimonios=($seccion=='testimonios')?'uk-active':'';

$menu = '
	<li class="'.$navCatalogos.'"><a href="index.php?rand='.rand(1,1000).'&seccion=catalogos">Catálogos</a></li>
	<li class="'.$navConfiguracion.'"><a href="index.php?rand='.rand(1,1000).'&seccion=configuracion">Configuración</a></li>
	<li class="'.$navEmp.'"><a href="index.php?rand='.rand(1,1000).'&seccion=empresas">Marcas</a></li>
	<li class="'.$navnoti.'"><a href="index.php?rand='.rand(1,1000).'&seccion=noticias">Noticias</a></li>
	<li class="'.$navProductos.'"><a href="index.php?rand='.rand(1,1000).'&seccion=productos&subseccion=all">productos</a></li>
	<li class="'.$navPro.'"><a href="index.php?rand='.rand(1,1000).'&seccion=proyectos">Proyectos</a></li>
	<li class="'.$navs.'"><a href="index.php?rand='.rand(1,1000).'&seccion=servicios">Servicios</a></li>
	<li class="'.$navCarousel.'"><a href="index.php?rand='.rand(1,1000).'&seccion=carousel">Slider</a></li>
    <li class="'.$navtestimonios.'"><a href="index.php?rand='.rand(1,1000).'&seccion=testimonios">Testimonios</a></li>
';

$menuBig='
	<div class="uk-visible@l">
		<div class="uk-height-viewport uk-flex uk-flex-middle">
			<div class="uk-width-1-1">
				<nav class="uk-width-1-1">
					<ul class="uk-nav-default uk-nav-parent-icon uk-text-uppercase" id="menu-large" uk-nav>
						'.$menu.'
					</ul>
				</nav>
				<div class="padding-20">
					<a href="index.php?logout=1" class="uk-button uk-button-default"><i uk-icon="icon:unlock;"></i> &nbsp; Salir</a>
				</div>
			</div>
		</div>
	</div>
		';
$menuSmall='
	<div id="menu-movil" uk-offcanvas="mode: push; overlay: true">
		<div class="uk-offcanvas-bar uk-flex uk-flex-column">
			<button class="uk-offcanvas-close" type="button" uk-close></button>
			<ul class="uk-nav uk-nav-primary uk-nav-parent-icon uk-nav-center uk-margin-auto-vertical menu-movil uk-text-uppercase" uk-nav>
				'.$menu.'
			</ul>
			<div class="uk-text-center">
				<a href="index.php?logout=1" class="uk-icon-button uk-button-danger" uk-icon="icon:unlock;"></a>
			</div>
		</div>
	</div>';

$head='
	<!DOCTYPE html>
	<html lang="es">
		<head>
			<meta charset="utf-8">

			<title>Administración</title>

			<meta name="viewport" content="width=device-width, initial-scale=1.0">

			<link rel="shortcut icon" href="../img/design/favicon.ico">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/css/uikit.css">

			<!-- jQuery is required -->
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

			<!-- UIkit JS -->
			<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/js/uikit.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/js/uikit-icons.min.js"></script>

			<!-- Chosen -->
			<link  href="../library/chosen/chosen.admin.css"    rel="stylesheet">
			<script src="../library/chosen/chosen.jquery.js"    type="text/javascript"></script>
			<script src="../library/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>

			<link rel="stylesheet" type="text/css"  href="../css/admin.css">
			<link rel="stylesheet" type="text/css"  href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">

		</head>';
$header='
		<body>
			<div id="admin" class="uk-offcanvas-content">
				<div id="adminmenu">
					<div id="menudisplay" class="uk-height-viewport" uk-sticky>
						'.$menuBig.'
					</div>
					'.$menuSmall.'
				</div>
				<div id="admincuerpo">
					<div class="uk-container uk-container-expand">
						<div uk-grid>
							<!-- ///////////////////////////////////////////////// -->
							<!-- /////////////  COMIENZA  CONTENIDO   //////////// -->
							<!-- ///////////////////////////////////////////////// -->
							';
$footer='

							<!-- ///////////////////////////////////////////////// -->
							<!-- /////////////   TERMINA  CONTENIDO   //////////// -->
							<!-- ///////////////////////////////////////////////// -->
						</div>
					</div>
				</div>
			</div>
		</body>
	</html>';

