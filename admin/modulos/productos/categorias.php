

<div class="uk-width-1-1 margen-top-20">
	<ul class="uk-breadcrumb uk-text-capitalize">
		<?php 
		echo '
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=all">Productos</a></li>
		<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=categorias" class="color-red">Categorías</a></li>
		';
		?>

	</ul>
</div>

<div class="uk-width-1-1 margen-top-20 uk-text-left">
	<table class="uk-table uk-table-striped uk-table-small uk-table-middle uk-table-responsive" id="tablaproductos">
		<thead>
			<tr class="uk-text-muted">
				<th width="30px"></th>
				<th onclick="sortTable(1)">Categoría</th>
				<th onclick="sortTable(2)" class="uk-text-center">Subcategorías</th>
				<th onclick="sortTable(3)" class="uk-text-center">Productos</th>
				<th onclick="sortTable(4)" width="50px">Activo</th>
				<th width="90px"></th>
			</tr>
		</thead>
		<tbody class="sortable" data-tabla="<?=$seccioncat?>">
<?php 
$productos_cat = $CONEXION -> query("SELECT * FROM $seccioncat WHERE parent = 0 ORDER BY orden,txt");
while ($row_productos_cat = $productos_cat -> fetch_assoc()) {
	$actual=$row_productos_cat['id'];

	$borrar='<a href="javascript:eliminaCat(id='.$row_productos_cat['id'].')" class="uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></a>';

	$numeroProds=0;
	$productos_subcat = $CONEXION -> query("SELECT * FROM $seccioncat WHERE parent = $actual");
	$numeroSubcats = $productos_subcat->num_rows;
	while ($row_productos_subcat = $productos_subcat -> fetch_assoc()) {
		$categoriaMENU=$row_productos_subcat['id'];

		$filas = $CONEXION -> query("SELECT * FROM $seccion WHERE categoria = '$categoriaMENU'");
		$numeroProdsThis = $filas->num_rows;
		$numeroProds+=$numeroProdsThis;
		$row_Filas = $filas -> fetch_assoc();
	}


	$picTxt='';
	$pic='../img/contenido/'.$seccioncat.'/'.$row_productos_cat['imagen'];
	if(strlen($row_productos_cat['imagen'])>0 AND file_exists($pic)){
		$picTxt='
			<div class="uk-inline">
				<i uk-icon="camera"></i>
				<div uk-drop="pos: right-justify">
					<img src="'.$pic.'" class="uk-border-rounded">
				</div>
			</div>';
	}



	if ($numeroSubcats>0) {
		$borrar='<span class="uk-icon-button uk-button-default" uk-tooltip title="No puede eliminar<br>Elimine antes su contenido" uk-icon="icon:trash"></span>';
	}

	$estatusIcon=($row_productos_cat['estatus']==0)?'off uk-text-muted':'on uk-text-primary';


	echo '
			<tr id="'.$actual.'">
				<td>'.$picTxt.'</td>
				<td>'.$row_productos_cat['txt'].'</td>
				<td class="uk-text-center"><span class="uk-hidden">'.(10000+$numeroSubcats).'</span>'.$numeroSubcats.'</td>
				<td class="uk-text-center"><span class="uk-hidden">'.(10000+$numeroProds).'</span>'.$numeroProds.'</td>
				<td class="uk-text-center@m">
					<span class="uk-text-muted uk-hidden@m">Activo: </span>
					<i class="estatuschange estatuschangecat fa fa-lg fa-toggle-'.$estatusIcon.' uk-text-muted pointer" data-tabla="'.$seccioncat.'" data-campo="estatus" data-id="'.$actual.'" data-valor="'.$row_productos_cat['estatus'].'"></i>
				</td>
				<td class="uk-text-center">
					'.$borrar.'&nbsp;&nbsp;
					<a href="index.php?seccion='.$seccion.'&subseccion=catdetalle&cat='.$actual.'" class="uk-icon-button uk-button-primary"><i class="fa fa-search-plus"></i></a>
				</td>
			</tr>';

}
?>

		</tbody>
	</table>
</div>

<div style="min-height: 100px;">
</div>

<div>
	<div id="buttons">
		<a href="#nuevacat" class="uk-icon-button uk-button-primary" uk-toggle uk-icon="icon:plus"></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>


<div id="nuevacat" uk-modal="center: true" class="modal">
	<div class="uk-modal-dialog uk-modal-body">
		<a class="uk-modal-close uk-close"></a>
		<form action="index.php" class="uk-width-1-1 uk-text-center uk-form" method="post" name="editar" onsubmit="return checkForm(this);">
			<input type="hidden" name="nuevacategoria" value="1">
			<input type="hidden" name="seccion" value="<?=$seccion?>">
			<input type="hidden" name="subseccion" value="categorias">

			<label for="categoria">Nueva categoría</label><br><br>
			<input type="text" class="uk-input" name="categoria" required><br><br>
			<input type="submit" name="send" value="Agregar" class="uk-button uk-button-primary">
		</form>
	</div>
</div>

<?php
$scripts='
	// Eliminar categoria
	function eliminaCat () { 
		var statusConfirm = confirm("Realmente desea eliminar esta marca?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&eliminarCat&cat="+id);
		} 
	};

	// Ordenar categorías
	$(function(){
		$("#sortable").sortable({
			update: function( event, ui ) {
				var PostData = $( "#sortable" ).sortable( "toArray");
				console.log(PostData);
				$.ajax({
					method: "POST",
					url: "modulos/'.$seccion.'/acciones.php",
					data: { 
						catlist:PostData
					}
				})
				.done(function( msg ) {
					UIkit.notification.closeAll();
					UIkit.notification(msg);
				});
			}
		});
	});

	$(".estatuschangecat").click(function(){
		var id = $(this).attr("data-id");
		var estatus = $(this).attr("data-valor");

		$.ajax({
			method: "POST",
			url: "modulos/'.$seccion.'/acciones.php",
			data: { 
				estatuscat: 1,
				id: id,
				estatus: estatus
			}
		})
		.done(function( msg ) {
			console.log( msg );
		});
	});
';
?>

