// Posicion de los botones flotantes
	var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)-100;
	$( "#totop" ).css( "top", h);
	$( window ).scroll(function() {
		var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)-100;
		$( "#totop" ).css( "top", h);
	});
	$( window ).resize(function() {
		var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)-100;
		$( "#totop" ).css( "top", h);
	});

	$pantalla = $(window);
	$('#arrow-button').addClass("claro");
	$pantalla.scroll(function() {
		if ($pantalla.scrollTop()>300) {
			$('#arrow-button').removeClass("oscuro");
			$('#arrow-button').addClass("claro");
		}else{
			$('#arrow-button').removeClass("claro");
			$('#arrow-button').addClass("oscuro");
		}
	});

// Agregar a favoritos     
	$(".favoritos").click(function(){
		var id = $(this).attr('data-id');
		$.ajax({
			method: "POST",
			url: "includes/acciones.php",
			data: { 
				producto: id,
				addtofavorites: 1
			}
		})
		.done(function( msg ) {
			$(".fav"+id).html(msg);
		});
	})

// Reductor de tamaño de fuente
	$("#fontsmaller").click(function(){
		var size = $("body").css("font-size");
		var sizeNew = (size.substring(0, 2)*1)-2;
		if (sizeNew<10) {sizeNew=10};
		$("body").css("font-size",sizeNew);
	});
	$("#fontbigger").click(function(){
		var size = $("body").css("font-size");
		var sizeNew = (size.substring(0, 2)*1)+2;
		$("body").css("font-size",sizeNew);
		console.log(sizeNew);
	});

// Enfocar el campo de la ventana modal
	$('.modal').on('shown', function () {
		$('.modal  input:text:visible:first').focus();
	});

// Enfocar el campo de la ventana modal
	$('.modal-login').on('shown', function () {
		$('#login-email').focus();
	});

// Alto de fotos cuadradas
	$(document).ready(function() {
		$(document).ready(function() {
		var alto=$(".pic-sq").width();
		$(".pic-sq").height(alto);
	});
	$( window ).resize(function() {
		var alto=$(".pic-sq").width();
		$(".pic-sq").height(alto);
		});
	})

// Envío de correo
	$(document).ready(function() {
		$("#footersend").click(function(){
			var nombre = $("#footernombre").val();
			var apellido = $("#footerapellido").val();
			var email = $("#footeremail").val();
			var telefono = $("#footertelefono").val();
			var comentarios = $("#footercomentarios").val();
			var fallo = 0;
			var alerta = "";
			
			$("input").removeClass("uk-form-danger");
			
			if (comentarios=="") { fallo=1; alerta="Falta comentarios"; id="footercomentarios"; }

			if (email=="") { 
				fallo=1; alerta="Falta email"; id="footeremail";
			}else{
				var n = email.indexOf("@");
				var l = email.indexOf(".");
				if ((n*l)<2) { 
					fallo=1; alerta="Proporcione un email válido"; id="footeremail";
				} 
			}

			if (telefono=="") { fallo=1; alerta="Falta telefono"; id="footertelefono"; }
			if (apellido=="") { fallo=1; alerta="Falta apellido"; id="footerapellido"; }
			if (nombre=="") { fallo=1; alerta="Falta nombre"; id="footernombre"; }

			var parametros = {
				"contacto" : 1,
				"nombre" : nombre,
				"apellido" : apellido,
				"email" : email,
				"telefono" : telefono,
				"comentarios" : comentarios
			};
			if (fallo == 0) {
				$.ajax({
					data:  parametros,
					url:   "includes/acciones.php",
					type:  "post",
					beforeSend: function () {
						$("#footersend").html("<div uk-spinner></div>");
						$("#footersend").prop("disabled",true);
						$("#footersend").disabled = true;
						UIkit.notification.closeAll();
						UIkit.notification('<div class="uk-text-center color-blanco bg-blue padding-10 text-lg"><i  uk-spinner></i> Espere...</div>');
					},
					success:  function (response) {
						$("#footersend").html("Volver a enviar");
						$("#footersend").disabled = false;
						$("#footersend").prop("disabled",false);
						$("#footernombre").val("");
						$("#footerapellido").val("");
						$("#footeremail").val("");
						$("#footertelefono").val("");
						$("#footercomentarios").val("");
						console.log(response);
						datos = JSON.parse(response);
						UIkit.notification.closeAll();
						UIkit.notification(datos.msj);
					}
				})
			}else{
				UIkit.notification.closeAll();
				UIkit.notification('<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; '+alerta+'</div>');
				$("#"+id).focus();
				$("#"+id).addClass("uk-form-danger");
			}
		})
	})

	


	equalheight = function(container){
		var altoMax = 0,
			alto;
			$(container).each(function() {
			  alto=$(this).height();
			  if (alto>altoMax) { altoMax=alto; };
			});
			$(container).height(altoMax);
		}

		setTimeout(function(){
			equalheight('.equal');
		},1000);
		setTimeout(function(){
			equalheight('.equal');
		},5000);


