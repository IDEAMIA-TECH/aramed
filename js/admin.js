// Chosen
	var config = {
		".chosen-select": {disable_search_threshold:10,width:"100%",no_results_text:"¡Nada encontrado!"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
	
// Editor ajax
	$(".editarajaxenter").keypress(function(e) {
		if(e.which == 13) {
			var id = $(this).attr("data-id");
			var tabla = $(this).attr("data-tabla");
			var campo = $(this).attr("data-campo");
			var valor = $(this).val();

			$.ajax({
				method: "POST",
				url: "modulos/varios/acciones.php",
				data: { 
					editarajax: 1,
					id: id,
					tabla: tabla,
					campo: campo,
					valor: valor
				}
			})
			.done(function( msg ) {
				UIkit.notification.closeAll();
				UIkit.notification(msg);
			});
		}
	});
	
// Editor Ajax Focus
	$(".editarajax").focusout(function() {
		var id = $(this).attr("data-id");
		var tabla = $(this).attr("data-tabla");
		var campo = $(this).attr("data-campo");
		var valor = $(this).val();

		$.ajax({
			method: "POST",
			url: "modulos/varios/acciones.php",
			data: { 
				editarajax: 1,
				id: id,
				tabla: tabla,
				campo: campo,
				valor: valor
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);
		});
	});

// Relacionar con ajax
	$(".relajax").click(function() {
		var id      = $(this).attr("data-id");
		var tabla   = $(this).attr("data-tabla");
		var valor   = $(this).attr("data-valor");
		var estatus = $(this).attr("data-estatus");
		console.log(tabla+" - "+id+" - "+valor+" - "+estatus);

		if(estatus==1) {
			estatus=0;
			$(this).addClass("fa-toggle-off");
			$(this).addClass("uk-text-muted");
			$(this).removeClass("fa-toggle-on");
			$(this).removeClass("uk-text-primary");
		}else{
			estatus=1;
			$(this).addClass("fa-toggle-on");
			$(this).addClass("uk-text-primary");
			$(this).removeClass("fa-toggle-off");
			$(this).removeClass("uk-text-muted");
		}
		$(this).attr("data-estatus",estatus);

		$.ajax({
			method: "POST",
			url: "modulos/varios/acciones.php",
			data: { 
				relajax: 1,
				id: id,
				tabla: tabla,
				valor: valor
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);
		});
	});

// Selector ajax
	$(".selector").change(function() {
		var datos = $(this).data();
		var valor = $(this).val();
		$.ajax({
			method: "POST",
			url: "modulos/varios/acciones.php",
			data: { 
				editarajax: 1,
				id: datos.id,
				tabla: datos.tabla,
				campo: datos.campo,
				valor: valor
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);
		});
	});

// Comprobar descuentos
	$(".descuento").keypress(function(e) {
		var valor = $(this).val();
		if(valor>100){
			$(this).val(9);
		}
		if(valor<0){
			$(this).val(0);
		}
	});
	$(".descuento").focusout(function(e) {
		var valor = $(this).val();
		if(valor>100 || valor<0){
			alert("El descuento no es válido");
		}
	});

// Eliminar una fila de la base de datos
	$(".elimina-single").click(function() {
		var id=$(this).attr("data-id");
		var tabla=$(this).attr("data-tabla");
		UIkit.modal.confirm("Desea eliminar esto?").then(function() {
			$.ajax({
				method: "POST",
				url: "modulos/varios/acciones.php",
				data: {
					eliminafila: 1,
					tabla: tabla,
					id: id
				}
			})
			.done(function( msg ) {
				UIkit.notification.closeAll();
				UIkit.notification(msg);
			});
		});
	});

// Cambiar estatus
	$(".estatuschange").click(function(){
		var tabla = $(this).attr("data-tabla");
		var campo = $(this).attr("data-campo");
		var id = $(this).attr("data-id");
		var valor = $(this).attr("data-valor");

		if(valor==1) {
			valor=0;
			$(this).addClass("fa-toggle-off");
			$(this).addClass("uk-text-muted");
			$(this).removeClass("fa-toggle-on");
			$(this).removeClass("uk-text-primary");
		}else{
			valor=1;
			$(this).addClass("fa-toggle-on");
			$(this).addClass("uk-text-primary");
			$(this).removeClass("fa-toggle-off");
			$(this).removeClass("uk-text-muted");
		}

		$(this).attr("data-valor",valor);

		$.ajax({
			method: "POST",
			url: "modulos/varios/acciones.php",
			data: {
				editarajax: 1,
				tabla: tabla,
				id: id,
				campo: campo,
				valor: valor
			}
		})
		.done(function( msg ) {
			UIkit.notification.closeAll();
			UIkit.notification(msg);
		});
	});

// Activar todo
	$(".changeall").click(function() {
		var tabla=$(this).attr("data-tabla");
		var campo=$(this).attr("data-campo");
		var valor=$(this).attr("data-valor");
		$.ajax({
			method: "POST",
			url: "modulos/varios/acciones.php",
			data: { 
				changeall: 1,
				tabla: tabla,
				campo: campo,
				valor: valor
			}
		})
		.done(function( msg ) {
			if(valor==1) {
				$(".fa-toggle-off").addClass("uk-text-primary");
				$(".fa-toggle-off").addClass("fa-toggle-on");
				$(".fa-toggle-off").removeClass("fa-toggle-off");
				$(".fa-toggle-on").attr("data-valor",valor);
				$(".apagado").removeClass("uk-text-primary");
				$(".apagado").removeClass("fa-toggle-on");
				$(".apagado").addClass("fa-toggle-off");
				$(".apagado").attr("data-valor",0);
			}else{
				$(".fa-toggle-on").addClass("fa-toggle-off");
				$(".fa-toggle-on").removeClass("uk-text-primary");
				$(".fa-toggle-on").removeClass("fa-toggle-on");
				$(".fa-toggle-off").attr("data-valor",valor);
				$(".encendido").addClass("uk-text-primary");
				$(".encendido").removeClass("fa-toggle-off");
				$(".encendido").addClass("fa-toggle-on");
				$(".encendido").attr("data-valor",1);
			}
			UIkit.notification.closeAll();
			UIkit.notification(msg);
		});
	});

// Posicion de los botones flotantes
	var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)-100;
	$( "#buttons" ).css( "top", h);
	$( window ).scroll(function() {
		var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)-100;
		$( "#buttons" ).css( "top", h);
	});
	$( window ).resize(function() {
		var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)-100;
		$( "#buttons" ).css( "top", h);
	});

// Enfocar la primer campo de una modal
	$('.modal').on('shown', function () {
		$('.modal  input:text:visible:first').focus();
	});

// Contraseñas
	$('.password-revelar').click(function(){
		$('.pass').attr('type','text');
		$('.password-revelar').addClass('uk-hidden');
		$('.password-ocultar').removeClass('uk-hidden');
	});
	$('.password-ocultar').click(function(){
		$('.pass').attr('type','password');
		$('.password-ocultar').addClass('uk-hidden');
		$('.password-revelar').removeClass('uk-hidden');
	});

// Ordenar arrastrando
	$(".sortable").sortable({
		update: function( event, ui ) {
			var tabla = $(this).attr("data-tabla");
			var orden = $(this).sortable( "toArray");
			$.ajax({
				method: "POST",
				url: "modulos/varios/acciones.php",
				data: { 
					orderanarjax: 1,
					tabla: tabla,
					orden: orden
				}
			})
			.done(function(msg) {
				UIkit.notification.closeAll();
				UIkit.notification(msg);
			});
		}
	});

// Ordenar tabla
	function sortTable(n) {
	  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
	  table = document.getElementById("ordenar");
	  switching = true;
	  // Set the sorting direction to ascending:
	  dir = "asc"; 
	  /* Make a loop that will continue until
	  no switching has been done: */
	  while (switching) {
	    // Start by saying: no switching is done:
	    switching = false;
	    rows = table.getElementsByTagName("TR");
	    /* Loop through all table rows (except the
	    first, which contains table headers): */
	    for (i = 1; i < (rows.length - 1); i++) {
	      // Start by saying there should be no switching:
	      shouldSwitch = false;
	      /* Get the two elements you want to compare,
	      one from current row and one from the next: */
	      x = rows[i].getElementsByTagName("TD")[n];
	      y = rows[i + 1].getElementsByTagName("TD")[n];
	      /* Check if the two rows should switch place,
	      based on the direction, asc or desc: */
	      if (dir == "asc") {
	        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
	          // If so, mark as a switch and break the loop:
	          shouldSwitch= true;
	          break;
	        }
	      } else if (dir == "desc") {
	        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
	          // If so, mark as a switch and break the loop:
	          shouldSwitch= true;
	          break;
	        }
	      }
	    }
	    if (shouldSwitch) {
	      /* If a switch has been marked, make the switch
	      and mark that a switch has been done: */
	      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
	      switching = true;
	      // Each time a switch is done, increase this count by 1:
	      switchcount ++; 
	    } else {
	      /* If no switching has been done AND the direction is "asc",
	      set the direction to "desc" and run the while loop again. */
	      if (switchcount == 0 && dir == "asc") {
	        dir = "desc";
	        switching = true;
	      }
	    }
	  }
	}

	$("th.pointer").click(function(){
		$("th.pointer").children().addClass("uk-hidden");
		$(this).children().removeClass("uk-hidden");
	})


// Editor de texto V5                  
  tinymce.init({
    selector: '.editor',
    height: 500,
    plugins: 'lists advlist autolink link image media charmap print hr anchor pagebreak table wordcount searchreplace code',
    menubar: 'edit insert table tools',
    toolbar: "forecolor backcolor | bold italic underline | formatselect | alignleft aligncenter alignright alignjustify | numlist bullist | hr | code",
    toolbar_mode: 'floating',
    color_map: [
      '#BFEDD2', 'Light Green',
      '#FBEEB8', 'Light Yellow',
      '#F8CAC6', 'Light Red',
      '#ECCAFA', 'Light Purple',
      '#C2E0F4', 'Light Blue',

      '#2DC26B', 'Green',
      '#F1C40F', 'Yellow',
      '#E03E2D', 'Red',
      '#B96AD9', 'Purple',
      '#3598DB', 'Blue',

      '#169179', 'Dark Turquoise',
      '#E67E23', 'Orange',
      '#BA372A', 'Dark Red',
      '#843FA1', 'Dark Purple',
      '#236FA1', 'Dark Blue',

      '#ECF0F1', 'Light Gray',
      '#CED4D9', 'Medium Gray',
      '#95A5A6', 'Gray',
      '#7E8C8D', 'Dark Gray',
      '#34495E', 'Navy Blue',

      '#000000', 'Black',
      '#ffffff', 'White'
    ]
  });










