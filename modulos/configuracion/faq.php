
	<table class="uk-table uk-table-hover uk-table-striped uk-table-small uk-table-middle">
		<thead>
			<tr>
				<th>Pregunta</th>
				<th width="100px"></th>
			</tr>
		</thead>
		<tbody class="sortable" data-tabla="faq">
		<?php
		$faq = $CONEXION -> query("SELECT * FROM faq ORDER BY orden");
		while ($row_faq = $faq -> fetch_assoc()) {

			$prodID=$row_faq['id'];

			$link='index.php?seccion='.$seccion.'&frame=faqdetalle&id='.$row_faq['id'];

			echo '
			<tr id="'.$row_faq['id'].'">
				<td>
					<a href="'.$link.'">'.$row_faq['pregunta'].'</a>
				</td>
				<td class="uk-text-right">
					<a href="'.$link.'" class="uk-icon-button uk-button-primary" uk-icon="icon:pencil"></i></a>
					<a href="javascript:eliminaProd(id='.$row_faq['id'].')" class="uk-icon-button uk-button-danger" uk-icon="icon:trash"></i></a> 
				</td>
			</tr>';
		$picROW='';
		}
		?>

		</tbody>
	</table>
</div>


<?php
echo '
<div>
	<div id="buttons">
		<a href="index.php?rand='.rand(1,1000).'&seccion='.$seccion.'&frame=faqnuevo" class="uk-icon-button uk-button-primary uk-box-shadow-large" uk-icon="icon:plus;ratio:1.4;"></a>
		<a href="#menu-movil" class="uk-icon-button uk-button-primary uk-box-shadow-large uk-hidden@l" uk-icon="icon:menu;ratio:1.4;" uk-toggle></a>
	</div>
</div>';



$scripts='
	// Eliminar producto
	function eliminaProd () { 
		var statusConfirm = confirm("Realmente desea eliminar esta Pregunta?"); 
		if (statusConfirm == true) { 
			window.location = ("index.php?seccion='.$seccion.'&frame='.$frame.'&borrarFaq&id="+id);
		} 
	};'
?>