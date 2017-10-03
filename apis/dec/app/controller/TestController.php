<?php
namespace Dec\controller;
use Dec\model\Salida as _Salida;
use Dec\error\MensajeError as MensajeError;
use PHPMailer as PHPMailer;

class TestController{
	private $valid;
	private $objSalida;
	private $salida;
	
	public function __construct(){
		$this->objSalida = new _Salida();
	}
	
	public function pruebaMail(){
		//$this->salida = $this->objSalida->seteaSalida("pruebaMail",$document);
		//require 'PHPMailerAutoload.php';
		$mail = new PHPMailer();
		 
		// $body = 'An email test!';
		 
		// $mail->AddReplyTo('cristian@amoretti.cl', 'Cristian Amoretti');
		// $mail->SetFrom('cristian@amoretti.cl', 'Cristian Amoretti');
		// $mail->AddAddress('cristian.amoretti@gmail.com', 'Amoretti');
		// $mail->Subject = 'Test email';
		// $mail->MsgHTML( $body );
		 
		// if( ! $mail->Send() ) {
		// 	$this->salida['mensaje_dec']['mensaje'] = "Mailer Error: " . $mail->ErrorInfo;
		// }
		// else{
		//     $this->salida['mensaje_dec']['mensaje'] ="Mail enviado con exito";
		// }


		$mail = new PHPMailer();
		$mail->isSMTP();

		//Enable SMTP debugging
		$mail->SMTPDebug = 2;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$mail->Host = 'email-smtp.us-west-2.amazonaws.com';

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		// I tried PORT 25, 465 too
		$mail->Port = 587;

		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';

		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = "AKIAIK2HP2DWBHKN4Y7A";

		//Password to use for SMTP authentication
		$mail->Password = "AnI4JvuMbELKZdWczdfi2jatUhkffcbreyKxX0NgXZL9";

		//Set who the message is to be sent from
		$mail->setFrom('cristian@amoretti.cl', 'Cristian Amoretti');

		//Set who the message is to be sent to
		$mail->addAddress('cristian.amoretti@gmail.com', 'Amoretti');

		//Set the subject line
		$mail->Subject = 'PHPMailer GMail SMTP test';


		$mail->Body = 'This is a plain-text message body';
		//Replace the plain text body with one created manually
		$mail->AltBody = 'This is a plain-text message body';

		//send the message, check for errors
		if( ! $mail->Send() ) {
			$this->salida['mensaje_dec']['mensaje'] = "Mailer Error: " . $mail->ErrorInfo;
		}
		else{
		    $this->salida['mensaje_dec']['mensaje'] ="Mail enviado con exito";
		}


		return $this->salida;
	}

}

?>