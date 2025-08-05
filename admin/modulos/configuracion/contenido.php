<?php 
$CONSULTA = $CONEXION -> query("SELECT * FROM configuracion WHERE id = 1");
$rowCONSULTA = $CONSULTA -> fetch_assoc();

$frame=(isset($_REQUEST['frame']))?$_REQUEST['frame']:'general';

$estatusAbout		='white';
$estatusHome	    ='white';
$estatusContacto	='white';
$estatusFaq			='white';
$estatusGeneral		='white';
$estatusPoliticas	='white';
$estatusUsuarios	='white';

switch ($frame) {
	case 'about':
		$estatusAbout='primary';
		break;
	case 'home':
		$estatusHome='primary';
		break;
	case 'contacto':
		$estatusContacto='primary';
		break;
	case 'faq':
		$estatusFaq='primary';
		break;
	case 'faqnuevo':
		$estatusFaq='primary';
		break;
	case 'faqdetalle':
		$estatusFaq='primary';
		break;
	case 'general':
		$estatusGeneral='primary';
		break;
	case 'politicas':
		$estatusPoliticas='primary';
		break;
	case 'usuarios':
		$estatusUsuarios='primary';
		break;
	default:
		$estatusGeneral='primary';
		break;
}


echo '
<div class="uk-width-auto@m margen-top-20">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'" class="color-red">Configuración</a></li>
	</ul>
</div>

<div class="uk-width-expand@s margen-top-20">
	<div uk-grid class="uk-grid-small uk-flex-right">
		<div>
			<a class="uk-button uk-button-'.$estatusContacto.'" href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&frame=contacto">Contacto</a>
		</div>
		<div>
			<a class="uk-button uk-button-'.$estatusGeneral.'" href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'">General</a>
		</div>
		<div>
			<a class="uk-button uk-button-'.$estatusHome.'" href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&frame=home">Inicio</a>
		</div>
		<div>
			<a class="uk-button uk-button-'.$estatusAbout.'" href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&frame=about">Nosotros</a>
		</div>
		<div>
			<a class="uk-button uk-button-'.$estatusUsuarios.'" href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&frame=usuarios">Usuarios</a>
		</div>
	</div>
</div>

<div class="uk-width-1-1">
';

include 'modulos/'.$seccion.'/'.$frame.'.php';

echo'

</div>

<div style="min-height:300px;">
</div>';


