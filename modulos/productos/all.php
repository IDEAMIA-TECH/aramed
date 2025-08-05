<?php
$pag=(isset($_GET['pag']))?$_GET['pag']:0;
$prodspagina=(isset($_GET['prodspagina']))?$_GET['prodspagina']:20;
$consulta = $CONEXION -> query("SELECT * FROM $seccion ORDER BY categoria");

$numItems=$consulta->num_rows;
$prodInicial=$pag*$prodspagina;


echo '
	<div class="uk-width-1-3@s margen-top-20">
		<ul class="uk-breadcrumb uk-text-capitalize">
			<li><a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=all" class="color-red">Productos</a></li>
		</ul>
	</div>

	<div class="uk-width-1-3@s margen-top-20">
		<a href="index.php?seccion='.$seccion.'&subseccion=categorias" class="uk-button uk-button-primary">Categorias</a>
	</div>

	<div class="uk-width-1-3@s margen-top-20">
		<input type="text" class="uk-input search" data-campo="titulo" placeholder="Buscar...">
	</div>';
?>


<div class="uk-width-1-1 margen-v-50">
	<table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-middle uk-table-responsive" id="ordenar">
		<thead>
			<tr class="uk-text-muted">
				<th width="30px"></th>
				<th onclick="sortTable(1)">Producto</th>
				<th onclick="sortTable(2)">Categoria</th>
				<th onclick="sortTable(3)">Subcategoria</th>
				<th width="80px" onclick="sortTable(4)" class="uk-text-center">Best<br>seller</th>
				<th width="80px" onclick="sortTable(5)" class="uk-text-center">Activo</th>
				<th width="100px"><i class="fa fa-2x fa-toggle-on color-blue pointer" id="activalotodo" uk-tooltip="Activar todo"></i></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$productos = $CONEXION -> query("SELECT * FROM $seccion ORDER BY categoria LIMIT $prodInicial,$prodspagina");
		while ($row_Consulta1 = $productos -> fetch_assoc()) {
			$prodID=$row_Consulta1['id'];
			$catId=$row_Consulta1['categoria'];

			$CONSULTA4 = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $catId");
			$row_CONSULTA4 = $CONSULTA4 -> fetch_assoc();
			$categoriaTxt=$row_CONSULTA4['txt'];
			$parent=$row_CONSULTA4['parent'];

			$CONSULTA5 = $CONEXION -> query("SELECT * FROM $seccioncat WHERE id = $parent");
			$row_CONSULTA5 = $CONSULTA5 -> fetch_assoc();
			$marcaTxt=$row_CONSULTA5['txt'];

			$link='index.php?seccion='.$seccion.'&subseccion=detalle&id='.$row_Consulta1['id'];
			$linkEditar='index.php?seccion='.$seccion.'&subseccion=editar&id='.$row_Consulta1['id'];

			$destacadoIcon=($row_Consulta1['destacado']==0)?'off uk-text-muted':'on color-primary';
			$estatusIcon=($row_Consulta1['estatus']==0)?'off uk-text-muted':'on uk-text-primary';

			$picTxt='';
			$CONSULTAPIC = $CONEXION -> query("SELECT * FROM $seccionpic WHERE producto = $prodID ORDER BY orden,id LIMIT 1");
			$numProds=$CONSULTAPIC->num_rows;
			while ($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()) {
				$pic='../img/contenido/'.$seccion.'/'.$row_consultaPIC['id'].'-sm.jpg';
				if(file_exists($pic)){
					$picTxt='
						<div class="uk-inline">
							<i uk-icon="camera"></i>
							<div uk-drop="pos: right-justify">
								<img src="'.$pic.'" class="uk-border-rounded">
							</div>
						</div>';
				}
			}


			echo '
			<tr>
				<td>
					'.$picTxt.'
				</td>
				<td>
					'.$row_Consulta1['titulo'].'
				</td>
				<td>
					'.$marcaTxt.'
				</td>
				<td>
					'.$categoriaTxt.'
				</td>
				<td class="uk-text-center">
					<span class="uk-text-muted uk-hidden@m">Activo: </span>
					<i class="estatuschange fa fa-lg fa-toggle-'.$destacadoIcon.' uk-text-muted pointer" data-tabla="'.$seccion.'" data-campo="destacado" data-id="'.$prodID.'" data-valor="'.$row_Consulta1['destacado'].'"></i>
				</td>
				<td class="uk-text-center@m">
					<span class="uk-text-muted uk-hidden@m">Activo: </span>
					<i class="estatuschange fa fa-lg fa-toggle-'.$estatusIcon.' uk-text-muted pointer" data-tabla="'.$seccion.'" data-campo="estatus" data-id="'.$prodID.'" data-valor="'.$row_Consulta1['estatus'].'"></i>
				</td>
				<td class="uk-text-center">
					<span data-id="'.$row_Consulta1['id'].'" class="eliminaprod uk-icon-button uk-button-danger" tabindex="1" uk-icon="icon:trash"></span> &nbsp;&nbsp;
					<a href="'.$link.'" class="uk-icon-button uk-button-primary"><i class="fa fa-search-plus"></i></a>
				</td>
			</tr>';
		}
		?>

		</tbody>
	</table>
</div>






<!-- PAGINATION -->
<div class="uk-width-1-1 padding-top-50">
	<div uk-grid class="uk-flex-center">
		<div>
			<ul class="uk-pagination uk-flex-center uk-text-center">
			<?php
			if ($pag!=0) {
				$link='index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion='.$subseccion.'&pag='.($pag-1).'&prodspagina='.$prodspagina;
				echo'
				<li><a href="'.$link.'"><i class="fa fa-lg fa-angle-left"></i> &nbsp;&nbsp; Anterior</a></li>';
			}
			$pagTotal=intval($numItems/$prodspagina);
			$modulo=$numItems % $prodspagina;
			if (($modulo) == 0){
				$pagTotal=($numItems/$prodspagina)-1;
			}
			for ($i=0; $i <= $pagTotal; $i++) { 
				$clase='';
				if ($pag==$i) {
					$clase='uk-badge bg-primary color-white';
				}
				$link='index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion='.$subseccion.'&pag='.($i).'&prodspagina='.$prodspagina;
				echo '<li><a href="'.$link.'" class="'.$clase.'">'.($i+1).'</a></li>';
			}
			if ($pag!=$pagTotal AND $numItems!=0) {
				$link='index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion='.$subseccion.'&pag='.($pag+1).'&prodspagina='.$prodspagina;
				echo'
				<li><a href="'.$link.'">Siguiente &nbsp;&nbsp; <i class="fa fa-lg fa-angle-right"></i></a></li>';
			}
			?>

			</ul>
		</div>
		<div class="uk-text-right" style="margin-top: -10px; width:120px;">
			<select name="prodspagina" data-placeholder="Productos por página" id="prodspagina" class="chosen-select uk-select" style="width:120px;">
				<?php
				$arreglo = array(5=>5,20=>20,50=>50,100=>100,500=>500,9999=>"Todos");
				foreach ($arreglo as $key => $value) {
					$checked='';
					if ($key==$prodspagina) {
						$checked='selected';
					}
					echo '
					<option value="'.$key.'" '.$checked.'>'.$value.'</option>';
				}
				?>
				
			</select>
		</div>
	</div>
</div><!-- PAGINATION -->







<div>
	<div id="buttons">
		<a href="index.php?seccion=<?=$seccion?>&subseccion=nuevo" class="uk-icon-button uk-button-primary uk-box-shadow-large" uk-icon="icon:plus;ratio:1.4;"></a>
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
			window.location = ("index.php?seccion='.$seccion.'&subseccion='.$subseccion.'&borrarPod&cat='.$cat.'&id="+id);
		} 
	});

	// Activar todo
	$("#activalotodo").click(function() {
		$.ajax({
			method: "POST",
			url: "modulos/'.$seccion.'/acciones.php",
			data: { 
				activalotodo: 1
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);
			location.reload();
		});
	});

	$("#prodspagina").change(function(){
		var prodspagina = $(this).val();
		window.location = ("index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion='.$subseccion.'&prodspagina="+prodspagina);
	})

	$(".search").keypress(function(e) {
		if(e.which == 13) {
			var campo = $(this).attr("data-campo");
			var valor = $(this).val();
			window.location = ("index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&subseccion=search&campo="+campo+"&valor="+valor);
		}
	});
	';



	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Borramos las imágenes que estén remanentes en el directorio files
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$filehandle = opendir($rutaInicial); // Abrir archivos
	while ($file = readdir($filehandle)) {
		if ($file != "." && $file != ".." && $file != ".gitignore" && $file != ".htaccess" && $file != "thumbnail") {
			if(file_exists($rutaInicial.$file)){
				//echo $ruta.$file.'<br>';
				unlink($rutaInicial.$file);
			}
		}
	} 
	closedir($filehandle); 


?>
