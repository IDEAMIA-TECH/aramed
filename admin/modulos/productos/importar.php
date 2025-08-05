<?php
echo '
<div class="uk-width-1-1 margen-top-20 uk-text-left">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=all">Llantas</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=importar" class="color-red">Importar</a></li>
	</ul>
</div>';
?>


<div class="uk-width-1-2@m margen-v-50">
	<div id="fileuploader">
		Cargar
	</div>
</div>

<div class="uk-width-1-2@m margen-v-50">
	El archivo debe ser formato CSV<br>
	CSV = Valores separados por comas<br>
	No se deben poner comas adicionles dentro de los campos
</div>

<div class="uk-width-1-1">
	<p>Ejemplo:</p>
	<p>
		categoria, app, titulo, precio, preciodescuento, inventario, ancho, alto, radio, rin, carga, velocidad, utqg, txt, imagen<br>
		2, 1, Bridgestone Potenza S001, 1000.00, 900.00, 4, 225, 40, R, 18, 92, Y, 440 A B, Descripción, 392857400.jpg
	</p>
	<a href="../img/contenido/importar/ejemplo.csv" download class="uk-button uk-button-white"><i class="fa fa-download"></i> Ejemplo</a>
</div>


<?php
if (isset($showTable)) {
	echo '
	<div class="uk-width-1-1">
		<table class="uk-table uk-table-striped uk-table-hover uk-table-small" id="tablaproductos">
			<thead>
				<tr>
					<th>Submarca</th>
					<th>Aplicación</th>
					<th>Título</th>
					<th>Precio</th>
					<th>Precio con<br>descuento</th>
					<th>Inventario</th>
					<th>Ancho</th>
					<th>Alto</th>
					<th>Radio</th>
					<th>Rin</th>
					<th>Carga</th>
					<th>Velocidad</th>
					<th>UTQG</th>
					<th>Descripción</th>
					<th>Imagen</th>
				</tr>
			</thead>';
	foreach ($infoImportar as $key => $value) {
		$CONSULTA = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $value[0]");
		$rowCONSULTA = $CONSULTA -> fetch_assoc();
		$catName=$rowCONSULTA['txt'];

		$CONSULTA = $CONEXION -> query("SELECT * FROM productosapps WHERE id = $value[1]");
		$rowCONSULTA = $CONSULTA -> fetch_assoc();
		$appName=$rowCONSULTA['txt'];

		echo "
			<tbody>
				<tr>
					<td>$catName</td>
					<td>$appName</td>
					<td>$value[2]</td>
					<td>$value[3]</td>
					<td>$value[4]</td>
					<td>$value[5]</td>
					<td>$value[6]</td>
					<td>$value[7]</td>
					<td>$value[8]</td>
					<td>$value[9]</td>
					<td>$value[10]</td>
					<td>$value[11]</td>
					<td>$value[12]</td>
					<td>$value[13]</td>
					<td>$value[14]</td>
				</tr>
			</tbody>
			";
	}
	echo '
		</table>
		<div class="uk-margin uk-text-center">
			<a href="index.php?seccion=productos&subseccion=importar&importardatos&file='.$fileFinal.'" class="uk-button uk-button-primary">Continuar</a>
		</div>
	</div>';
}
?>



<div>
	<div id="buttons">
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>


<?php
$scripts='
	$(document).ready(function() {
		var imagenesArray = [];
		$("#fileuploader").uploadFile({
			url:"../library/upload-file/php/upload.php",
			fileName:"myfile",
			maxFileCount:1,
			showDelete: \'true\',
			allowedTypes: "csv",
			maxFileSize: 6291456,
			showFileCounter: true,
			showPreview:true,
			returnType:\'json\',
			onSuccess:function(files,data,xhr){ 
				imagenesArray.push(data);
				window.location = (\'index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion='.$subseccion.'&csv=\'+data);
			}
		});
	});	
	';



?>