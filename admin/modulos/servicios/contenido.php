

<div class="uk-width-1-1 margen-top-20 uk-text-left">
	<ul class="uk-breadcrumb">
		<?php 
		echo '
		<li><a href="index.php?seccion='.$seccion.'" class="color-red">'.$seccion.'</a></li>
		';
		?>
	</ul>
</div>

<div class="uk-width-medium-1-1 margen-v-20">
	<table class="uk-table uk-table-striped uk-table-hover uk-table-middle uk-table-responsive">
		<thead>
			<tr class="uk-text-muted">
				<th width="30px"></th>
				<th width="30px"></th>
				<th>Título corto</th>
				<th>Título largo</th>
				<th width="50px">Inicio</th>
				<th width="100px"></th>
			</tr>
		</thead>
		<tbody class="sortable" data-tabla="<?=$seccion?>">
		<?php
		$consulta = $CONEXION -> query("SELECT * FROM $seccion ORDER BY orden");
		while ($rowConsulta = $consulta -> fetch_assoc()) {
			$prodID=$rowConsulta['id'];

			$link='index.php?seccion='.$seccion.'&subseccion=detalle&id='.$rowConsulta['id'];
			$estatusIcon=($rowConsulta['estatus']==0)?'off uk-text-muted':'on uk-text-primary';

			$picTxt1='';
			$pic='../img/contenido/'.$seccion.'/'.$rowConsulta['imagen1'];
			if(file_exists($pic) AND strlen($rowConsulta['imagen1'])>0){
				$picTxt1='
					<div >
						<img src="'.$pic.'" style="max-height:30px">
					</div>';
			}

			$picTxt2='';
			$pic='../img/contenido/'.$seccion.'/'.$rowConsulta['imagen2'];
			if(file_exists($pic) AND strlen($rowConsulta['imagen2'])>0){
				$picTxt2='
					<div class="bg-primary">
						<img src="'.$pic.'" style="max-height:30px">
					</div>';
			}

			echo '
			<tr id="'.$rowConsulta['id'].'">
				<td>
					'.$picTxt1.'
				</td>
				<td>
					'.$picTxt2.'
				</td>
				<td>
					'.$rowConsulta['titulo'].'
				</td>
				<td>
					'.$rowConsulta['subtitulo'].'
				</td>
				<td class="uk-text-center@m">
					<span class="uk-text-muted uk-hidden@m">Activo: </span>
					<i class="estatuschange fa fa-lg fa-toggle-'.$estatusIcon.' uk-text-muted pointer" data-tabla="'.$seccion.'" data-campo="estatus" data-id="'.$prodID.'" data-valor="'.$rowConsulta['estatus'].'"></i>
				</td>
				<td class="uk-text-center">
					<a href="'.$link.'" class="uk-icon-button uk-button-primary" uk-icon="icon:pencil"></a>
					<span data-id="'.$rowConsulta['id'].'" class="eliminaprod uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></span>
				</td>
			</tr>';
		}
		?>

		</tbody>
	</table>
</div>


<?php
$CONSULTA = $CONEXION -> query("SELECT servicios FROM configuracion WHERE id = 1");
$rowCONSULTA = $CONSULTA -> fetch_assoc();
echo '
	<div class="uk-width-1-1">
		<div class="uk-container uk-container-xsmall">
			<div>
				<label>Descripción introductoria</label>
				<textarea class="editarajax uk-textarea" data-tabla="configuracion" data-campo="servicios" data-id="1" style="min-height:150px;">'.$rowCONSULTA['servicios'].'</textarea>
			</div>
		</div>
	</div>
	';
?>



<div>
	<div id="buttons">
		<a href="index.php?seccion=<?=$seccion?>&subseccion=nuevo" id="add-button" class="uk-icon-button uk-button-primary uk-box-shadow-large" uk-icon="icon: plus;ratio:1.4"></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>

<?php
$scripts='

	// Eliminar producto
	$(".eliminaprod").click(function() {
		var id = $(this).attr(\'data-id\');
		//console.log(id);
		var statusConfirm = confirm("Realmente desea eliminar este Producto?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion=contenido&borrarPod&id="+id);
		} 
	});

	';



?>