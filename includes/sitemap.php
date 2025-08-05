<?php
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>
<?xml-stylesheet href="css/general.css"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url><loc>'.$ruta.'</loc></url>
	<url><loc>'.$rutaCatalogo.'</loc></url>
	<url><loc>'.$rutaContacto.'</loc></url>
	<url><loc>'.$rutaAbout.'</loc></url>
	<url><loc>'.$rutaProyectos.'</loc></url>
	<url><loc>'.$rutaContacto.'</loc></url>
';

$CONSULTA1 = $CONEXION -> query("SELECT * FROM productoscat WHERE parent != 0 ORDER BY orden");
while($row_CONSULTA1 = $CONSULTA1 -> fetch_assoc()){
	$thisId=$row_CONSULTA1['id'];
    $link = $thisId.'_0_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_CONSULTA1['txt'])))).'-.php';
	echo '
	<url><loc>'.$ruta.$link.'</loc></url>';
}

$CONSULTA1 = $CONEXION -> query("SELECT * FROM productos");
while($row_CONSULTA1 = $CONSULTA1 -> fetch_assoc()){
	$thisId=$row_CONSULTA1['id'];
    $link=$thisId.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_CONSULTA1['modelo'])))).'-.html';
	echo '
	<url><loc>'.$ruta.$link.'</loc></url>';
}

$CONSULTA1 = $CONEXION -> query("SELECT * FROM proyectos");
while($row_CONSULTA1 = $CONSULTA1 -> fetch_assoc()){
	$thisId=$row_CONSULTA1['id'];
    $link=$thisId.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_CONSULTA1['titulo'])))).'_.html';
	echo '
	<url><loc>'.$ruta.$link.'</loc></url>';
}


echo '
</urlset>';
?>