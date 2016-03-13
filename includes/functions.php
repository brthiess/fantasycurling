<?php

//Sanitize form data
if(!function_exists('sanitize_form_data')){
	function sanitize_form_data(){
		$keeptags = false;
		
		if(count($_REQUEST) > 0){
			foreach($_REQUEST as $key=>$data){
				if(is_array($data)){
					foreach($data as $key2=>$data2){
						if(is_array($data2)){
							foreach($data2 as $key3=>$data3){
								$keeptags = (isset($_POST['keep_tags']) && in_array($key2, $_POST['keep_tags']) ? true : false);
								$_REQUEST[$key][$key2][$key3] = strip_data($data3,$keeptags);
							}
						}else{
							$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
							$_REQUEST[$key][$key2] = strip_data($data2,$keeptags);
						}
					}
				}else{
					$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
					$_REQUEST[$key] = strip_data($data,$keeptags);
				}
			}
		}
		if(count($_GET) > 0){
			foreach($_GET as $key=>$data){
				if(is_array($data)){
					foreach($data as $key2=>$data2){
						if(is_array($data2)){
							foreach($data2 as $key3=>$data3){
								$keeptags = (isset($_POST['keep_tags']) && in_array($key2, $_POST['keep_tags']) ? true : false);
								$_GET[$key][$key2][$key3] = strip_data($data3,$keeptags);
							}
						}else{
							$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
							$_GET[$key][$key2] = strip_data($data2,$keeptags);
						}
					}
				}else{
					$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
					$_GET[$key] = strip_data($data,$keeptags);
				}
			}
		}
		if(count($_POST) > 0){
			foreach($_POST as $key=>$data){
				if(is_array($data)){
					foreach($data as $key2=>$data2){
						if(is_array($data2)){
							foreach($data2 as $key3=>$data3){
								$keeptags = (isset($_POST['keep_tags']) && in_array($key2, $_POST['keep_tags']) ? true : false);
								$_POST[$key][$key2][$key3] = strip_data($data3,$keeptags);
							}
						}else{
							$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
							$_POST[$key][$key2] = strip_data($data2,$keeptags);
						}
					}
				}else{
					$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
					$_POST[$key] = strip_data($data,$keeptags);
				}
			}
		}
	}
}

//Strip harmful elements
if(!function_exists('strip_data')){
	function strip_data($string, $keeptags=false){
		$string = (trim($string) != '' ? str_replace("'", "&rsquo;", stripslashes(trim($string))) : '');
		return (!$keeptags ? strip_tags($string) : $string);
	}
}

//generate xid
if(!function_exists('gen_random_string')){
	function gen_random_string(){
		$length = 50;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';    
		for ($p = 0; $p < $length; $p++) {
			@$string .= $characters[mt_rand(0, strlen($characters))];
		}
		return md5($string);
	}
}

//Limit words function
if(!function_exists('string_limit_words')){
	function string_limit_words($string, $word_limit) {
		 $words = explode(' ', $string);
		 return implode(' ', array_slice($words, 0, $word_limit));
	}
}

//Format phone numbers
if(!function_exists('formatPhoneNumber')){
	function formatPhoneNumber($number){
		$return = str_replace(array('+','-','.',')','(',' '), "", stripslashes($number));		
		return trim($return);
	}
}

//Format international phone number
if(!function_exists('formatIntlNumber')){
	function formatIntlNumber($number){
			
		//get ext
		$ext = trim(substr(strrchr($number, 'ext'), 4));
		
		//remove ext. if exists
		if($ext != ""){
			$number = strstr($number, 'ext', true);
		}
				
		//format number for blank canvas
		$number = formatPhoneNumber($number);
		
		//check if number starts with 1
		if(substr($number, 0, 1) == '1'){
			//remove it
			$number = substr($number, 1);
		}
		
		//grab last 7 digits (for canada/us numbers)
		$number = "+1-".substr($number, 0, 3)."-".substr($number, 3, 3)."-".substr($number, 6, 4).($ext != "" ? ";ext=".$ext : "");
		
		return trim($number);
	}
}

//Check email
if(!function_exists('checkmail')){
	function checkmail($string){
		return preg_match("/^[^\s()<>@,;:\"\/\[\]?=]+@\w[\w-]*(\.\w[\w-]*)*\.[a-z]{2,}$/i",$string);
	}
}

//Send email
if(!function_exists('smtpEmail')){
	function smtpEmail($to, $subject, $message){
		require_once "Mail.php";
		global $global;
			
		$from = str_replace(".", "", $global['company_name']). " <inquiries@website.com>";	
		$host = "mail.emailsrvr.com";
		$username = "inquiries@website.com";
		$password = "password";
		
		$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject,
		  'MIME-Version' => '1.0', 'Content-Type' => 'text/html;charset=UTF-8', 'Content-Transfer-Encoding' => '8bit', 'X-Priority' => '3', 'Importance' => 'Normal');
		
		$smtp = Mail::factory('smtp',
		  array ('host' => $host,
			'auth' => true,
			'username' => $username,
			'password' => $password));
		
		
		$mail = $smtp->send($to, $headers, $message);
		
		if (PEAR::isError($mail)) {
		  //echo("<p><b>" . $mail->getMessage() . "</b></p>");
		  return false;
		} else {
		  //echo("<p>Message successfully sent!</p>");
		  return true;
		}
	}
}

?>