<?php 
if(isset($exito)){
  echo '  
	<div class="uk-width-1-1 uk-margin-top">
		<div class="uk-alert-success" uk-alert>
			<a class="uk-alert-close" uk-close></a>
			<p>Los cambios se han realizado con exito'.$legendSuccess.'</p>
		</div>
	</div>
';
}
if(isset($fallo)){
  echo '
	<div class="uk-width-1-1 uk-margin-top">
		<div class="uk-alert-danger" uk-alert>
			<a class="uk-alert-close" uk-close></a>
			<p>Ha ocurrido un error'.$legendFail.'</p>
		</div>
	</div>';
}
