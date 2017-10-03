<?php

// Replace sender@example.com with your "From" address. 
// This address must be verified with Amazon SES.
define('SENDER', 'cristian.amoretti@ratifica.cl');        

// Replace recipient@example.com with a "To" address. If your account 
// is still in the sandbox, this address must be verified.
define('RECIPIENT', 'cristian.amoretti@gmail.com');  
                                                      
// Replace smtp_username with your Amazon SES SMTP user name.
define('USERNAME','AKIAJSA26FOSYHOCHEFQ');  

// Replace smtp_password with your Amazon SES SMTP password.
define('PASSWORD','AlzS8A14qg2NeIjpsJqoEzEYTE+ERBTrdeWP5Qbymnfr');  

// If you're using Amazon SES in a region other than US West (Oregon), 
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP  
// endpoint in the appropriate region.
define('HOST', 'email-smtp.us-west-2.amazonaws.com');  

 // The port you will connect to on the Amazon SES SMTP endpoint.
define('PORT', '587');     

// Other message information                                               
define('SUBJECT','Solicitud acceso a DEC');
define('BODY','Verificar la dirección de correo electrónico que se ha añadido<br>
 <br>
Hola, $nombre:<br>
Se ha realizado una solicitud para añadir $correoElectronico a tu cuenta de DEC.<br>
<br>
¿Te sorprende haber recibido este correo electrónico?<br>
Ignora este correo electrónico, Puede que alguien haya escrito mal su dirección de correo electrónico y que haya añadido accidentalmente tratado la tuya. En este caso, tu dirección de correo electrónico no se añadirá a la otra cuenta.<br>
Verificar la dirección de correo electrónico que se ha añadido<br>
Esta dirección de correo electrónico no admite respuestas. Para obtener más información, visita el DEC - UI Bienvenida a DECCentro de ayuda de Cuentas de DEC.');

require_once 'Mail.php';

$headers = array (
  'From' => SENDER,
  'To' => RECIPIENT,
  'Subject' => SUBJECT);

$smtpParams = array (
  'host' => HOST,
  'port' => PORT,
  'auth' => true,
  'username' => USERNAME,
  'password' => PASSWORD
);

 // Create an SMTP client.
$mail = Mail::factory('smtp', $smtpParams);

// Send the email.
$result = $mail->send(RECIPIENT, $headers, BODY);

if (PEAR::isError($result)) {
  return false;
} else {
  return true;
}

?>