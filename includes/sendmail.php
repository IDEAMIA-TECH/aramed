<?php
	$cuerpo = ' 
		<html> 
		<head> 
			<meta content="text/html;charset=UTF-8" http-equiv="Content-Type">
			<title style="margin-left:50px">'.$asuntoCorreo.'</title>
		</head> 
		<body> 
		<div style="width:100%;background-color:'.$mailBGcolor.';color:#333;padding-bottom:50px;">
			<br /><br /><br />

			<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:760px;background-color:white;">
				<tr>
					<td style="width:700px;">

						<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:700px;color:#333;">
							<tr>
								<td style="text-align:center;padding-top:20px;">
									<img src="'.$logo.'" style="width:150px;">
								</td>
							</tr>
							<tr>
								<td>
									&nbsp;
								</td>
							</tr>
							<tr>
								<td style="text-align:center;font-weight:700;">
									'.$asuntoCorreo.'
								</td>
							</tr>
							<tr>
								<td>
									&nbsp;
								</td>
							</tr>
							<tr>
								<td>
									'.$cuerpoCorreo.'
								</td>
							</tr>
							<tr>
								<td>
									<br /><br />
								</td>
							</tr>

						</table>

					</td>
				</tr>
				<tr style="background-color:#333;">
					<td style="text-align:center;color:white;">
						<br /><br />
						<a href="'.$ruta.'" style="color:white;">www.'.$dominio.'</a>
						<br /><br />
						Tel: '.$telefono.'
						<br /><br />
					</td>
				</tr>
			</table>
		</div>
		</body> 
		</html> 
	'; 

		

	if(file_exists('library/phpmailer/class.phpmailer.php')){
		require 'library/phpmailer/class.phpmailer.php';
		require 'library/phpmailer/class.smtp.php';
		$fallo=0;
	}elseif(file_exists('../library/phpmailer/class.phpmailer.php')){
		require '../library/phpmailer/class.phpmailer.php';
		require '../library/phpmailer/class.smtp.php';
		$fallo=0;
	}else{
		$msjTxt.="<br>No se encontro PHPmailer";
	}

	// Envío
	if($fallo==0){
		$fallo=1;
		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		//Debug SMTP
		$mail->SMTPDebug = 0;
		//SMTP SECURE
		$mail->SMTPSecure = 'ssl';
		//Set who the message is to be sent from
		$mail->setFrom($RemitenteMail, $Brand);

		if ($debug==1) {
			//Set an alternative reply-to address
			$mail->addAddress($efra, 'Efra');
			$xtras=$efra;
		}elseif (isset($send2user) AND $send2user==1) {
			//Set an alternative reply-to address
			$mail->addAddress($email, $nombre);
			$mail->AddBCC($destinatario1, $Brand);
			$xtras=$destinatario1.' y '.$email;
			if (strlen($destinatario2)>0) {
				$mail->AddBCC($destinatario2, $Brand);
				$xtras.=' y '.$destinatario2;
			}
		}elseif (isset($send2user) AND $send2user==2) {
			//Set an alternative reply-to address
			$mail->addAddress($email, $nombre);
			$xtras=$email;
		}else{
			//Set an alternative reply-to address
			$mail->addAddress($destinatario1, $Brand);
			$xtras=$destinatario1;
			if (strlen($destinatario2)>0) {
				$mail->AddBCC($destinatario2, $Brand);
				$xtras.='y '.$destinatario2;
			}
		}

		//CONTENT HTML & UTF-8
		$mail->IsHTML(true);
		$mail->CharSet = 'UTF-8';
		//Set the subject line
		$mail->Subject = $asuntoCorreo;
		//CONTENT BODY
		$body = $cuerpo;
		//CONVER HTML BODY
		$mail->MsgHTML($body);
		//BODY MAIL
		$mail->Body = $body;
		//send the message, check for errors
		if($mail->Send()){
				$fallo=0;
				$msjIcon  = 'check';
				$msjClase = 'success';
				$msjTxt   = 'Enviado';
		}else{
			$msjTxt.="<br>No se pudo enviar<br>Codigo: 12<br>Brand: $Brand <br>Dominio: $dominio <br>Remitente: $RemitenteMail";
		}
	}
