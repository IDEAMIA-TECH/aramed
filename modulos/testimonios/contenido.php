

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
				<th width="150px">Fecha</th>
				<th>Nombre</th>
				<th width="250px" >Email</th>
				<th width="120px"></th>
			</tr>
		</thead>
		<tbody id="sortable">
		<?php
		$productos = $CONEXION -> query("SELECT * FROM $seccion ORDER BY orden");
		while ($row_productos = $productos -> fetch_assoc()) {
			$prodID=$row_productos['id'];

			$inicioButton=($row_productos['inicio']==1)?'success':'white';

			$link='index.php?seccion='.$seccion.'&subseccion=detalle&id='.$row_productos['id'];


			$picTxt='';
			$pic='../img/contenido/'.$seccion.'/'.$row_productos['imagen'];
			if(file_exists($pic) AND strlen($row_productos['imagen'])>0){
				$picTxt='
					<div class="uk-inline">
						<i uk-icon="camera"></i>
						<div uk-drop="pos: right-justify">
							<img src="'.$pic.'" class="uk-border-rounded">
						</div>
					</div>';
			}

			echo '
			<tr id="'.$row_productos['id'].'">
				<td>
					'.$picTxt.'
				</td>
				<td class="uk-text-center">
					'.$row_productos['fecha'].'
				</td>
				<td>
					'.$row_productos['titulo'].'
				</td>
				<td>
					'.$row_productos['email'].'
				</td>
				<td class="uk-text-center">
					<span data-id="'.$row_productos['id'].'" class="eliminaprod uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="'.$link.'" class="uk-icon-button uk-button-primary" uk-icon="icon:pencil"></a>
				</td>
			</tr>';
		}
		?>

		</tbody>
	</table>
</div>

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