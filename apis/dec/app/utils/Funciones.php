<?php
namespace Dec\utils;
use PHPMailer as PHPMailer;
/**
 * Comprueba si el rut ingresado es valido
 * @param string $rut RUT
 * @return boolean
 */
 class Funciones {
	public function valida_rut($rut){
		if (!preg_match("/^[0-9.]+[-]?+[0-9kK]{1}/", $rut)) {
			return false;
		}
		$rut = preg_replace('/[\.\-]/i', '', $rut);
		$dv = substr($rut, -1);
		$numero = substr($rut, 0, strlen($rut) - 1);
		$i = 2;
		$suma = 0;
		foreach (array_reverse(str_split($numero)) as $v) {
			if ($i == 8)
				$i = 2;
			$suma += $v * $i;
			++$i;
		}
		$dvr = 11 - ($suma % 11);
		if ($dvr == 11)
			$dvr = 0;
		if ($dvr == 10)
			$dvr = 'K';
		if ($dvr == strtoupper($dv))
			return true;
		else
			return false;
	}
	
	public function getURL(){
//		return "http://34.208.241.57/apis/dec/app/archivos/";
		return "http://localhost/apis/dec/app/archivos/";
	}
	
	public function validaEmail($email){
		// Check the formatting is correct
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
			return FALSE;
		}
		// Next check the domain is real.
		$domain = explode("@", $email, 2);
		return checkdnsrr($domain[1]); // returns TRUE/FALSE;
	}
	
	public function isJSON($string){
   		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
	
	public function strictEmpty($var) {
		$var = trim($var);
		if(isset($var) === true && $var === '') {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function is_multiArrayEmpty($multiarray) { 
		if(is_array($multiarray) and !empty($multiarray)){ 
			$tmp = array_shift($multiarray); 
				if(!is_multiArrayEmpty($multiarray) or !is_multiArrayEmpty($tmp)){ 
					return false; 
				} 
				return true; 
		} 
		if(empty($multiarray)){ 
			return true; 
		} 
		return false; 
	} 
	
	public function validaDocumento($document){
		if (!is_array($document)){
			return false;
		}
		else{
			return true;
		}
	}

	public function array_delete($del_val, $array) {
		if ($array == null){
			return array();
		}
	    if(is_array($del_val)) {
	         foreach ($del_val as $del_key => $del_value) {
	            foreach ($array as $key => $value){
	                if ($value == $del_value) {
	                    unset($array[$key]);
	                }
	            }
	        }
	    } else {
	        foreach ($array as $key => $value){
	            if ($value == $del_val) {
	            	unset($array[$key]);
	            }
	        }
	    }
	    return array_values($array);
	}

	public function remove_duplicates_array($array){
		$unique = array();
		if(is_array($array)) {
			if (count($array)>1){
				foreach($array as $v){
				  isset($k[$v]) || ($k[$v]=1) && $unique[] = $v;
				}
			}
			else{
				$unique = $array;
			}
		}
		return $unique;
	}

	public function arrayMerge($a, $b, $unique = true) {
	    if (empty($b)) {
	        return $a;  // No changes to be made to $a
	    }
	    if (empty($a)) {
	        $a = $b;
	    }
	    $a = array_merge($a, $b);
	    if ($unique) {
	        $a = $this->remove_duplicates_array($a);
	    }
	    return $a;
	}
	public function object_to_array($obj) {
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($_arr as $key => $val) {
                $val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
                $arr[$key] = $val;
        }
        return $_arr;
	}

	public function object_into_array($data) 
	{
		//if ((! is_array($data)) and (! is_object($data))) 
		//	return "pruebjsbdab"; //$data;
		
		$result = array();
		$data = (array) $data;
		foreach ($data as $key => $value) {
	    	if (is_object($value)) 
	    		$value = (array) $value;
	    	if (is_array($value)) 
	    		$result[$key] = object_to_array($value);
	    	else
	        	$result[$key] = $value;
		}
		return $result;
	}

	public function object_to_array_recusive ( $object, $assoc=TRUE, $empty='' ) 
	{ 
	    $res_arr = array(); 
	    if (!empty($object)) { 
	        $arrObj = is_object($object) ? get_object_vars($object) : $object;
	        $i=0; 
	        foreach ($arrObj as $key => $val) { 
	            $akey = ($assoc !== FALSE) ? $key : $i; 
	            if (is_array($val) || is_object($val)) { 
	                $res_arr[$akey] = (empty($val)) ? $empty : object_to_array_recusive($val); 
	            } 
	            else { 
	                $res_arr[$akey] = (empty($val)) ? $empty : (string)$val; 
	            } 
	        	$i++; 
	        }
	    } 
	    return $res_arr;
	}

	public function objectToArray($d) {
	    if (is_object($d)) {
	        // Gets the properties of the given object
	        // with get_object_vars function
	        $d = get_object_vars($d);
	    }

	    if (is_array($d)) {
	        /*
	        * Return array converted to object
	        * Using __FUNCTION__ (Magic constant)
	        * for recursive call
	        */
	        return array_map(__FUNCTION__, $d);
	    } else {
	        // Return array
	        return $d;
	    }
	}
	public function arrayToObject($d) {
	    if (is_array($d)) {
	        /*
	        * Return array converted to object
	        * Using __FUNCTION__ (Magic constant)
	        * for recursive call
	        */
	        return (object) array_map(__FUNCTION__, $d);
	    } else {
	        // Return object
	        return $d;
	    }
	}

	public function arrayCastRecursive($array)
	{
	    if (is_array($array)) {
	        foreach ($array as $key => $value) {
	            if (is_array($value)) {
	                $array[$key] = arrayCastRecursive($value);
	            }
	            if ($value instanceof stdClass) {
	                $array[$key] = arrayCastRecursive((array)$value);
	            }
	        }
	    }
	    if ($array instanceof stdClass) {
	        return arrayCastRecursive((array)$array);
	    }
	    return $array;
	}

	public function cvf_convert_object_to_array($data) {
	    if (is_object($data)) {
	        $data = get_object_vars($data);
	    }
	    if (is_array($data)) {
	        return array_map(__FUNCTION__, $data);
	    }
	    else {
	        return $data;
	    }
	}



	/**
	 * Returns an encrypted & utf8-encoded
	 */
	public function encrypt($pure_string, $encryption_key) {
	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
	    return $encrypted_string;
	}

	/**
	 * Returns decrypted original string
	 */
	public function decrypt($encrypted_string, $encryption_key) {
	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
	    return $decrypted_string;
	}

	public function encrypt_decrypt($action, $string) {
	/**
	 * simple method to encrypt or decrypt a plain text string
	 * initialization vector(IV) has to be the same when encrypting and decrypting
	 * PHP 5.4.9 ( check your PHP version for function definition changes )
	 *
	 * this is a beginners template for simple encryption decryption
	 * before using this in production environments, please read about encryption
	 * use at your own risk
	 *
	 * @param string $action: can be 'encrypt' or 'decrypt'
	 * @param string $string: string to encrypt or decrypt
	 *
	 * @return string
	 */
	
	    $output = false;

	    $encrypt_method = "AES-256-CBC";
	    $secret_key = 'RatificaEncryptKey';
	    $secret_iv = 'RatificaEncryptIV';

	    // hash
	    $key = hash('sha256', $secret_key);

	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);

	    if( $action == 'encrypt' ) {
	        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	        $output = base64_encode($output);
	    }
	    else if( $action == 'decrypt' ){
	        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }

	    return $output;
	}

	public function sendEmail($subject,$to_email,$to_name, $body ){
		$mail = new PHPMailer();
		$mail->isSMTP();
		//$mail->SMTPDebug = 2;
		//$mail->Debugoutput = 'html';
		$mail->Host = 'email-smtp.us-west-2.amazonaws.com';
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = "AKIAIK2HP2DWBHKN4Y7A";
		$mail->Password = "AnI4JvuMbELKZdWczdfi2jatUhkffcbreyKxX0NgXZL9";
		$mail->setFrom('No-Reply@ratifica.cl', 'DEC Ratifica');
		$mail->addAddress($to_email, $to_name);
		$mail->Subject = $subject;
		$mail->Body = $body ;
		$mail->isHTML(true);  
		//$mail->AltBody = $body;
		if( ! $mail->Send() ) {
			return false;
		}
		return true;

	}

 }
?>