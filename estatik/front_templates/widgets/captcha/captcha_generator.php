<?php

define("KEY_FOR_RC4", "adadaNchsadagadgakk342eiejfiejifje4234MnUUK25fjiNNBZBZNAkdaasd8sadhHZKZJnGREQhhsdjdksdsde");

class CaptchaCode {

	function generateCode($characters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) 
		{ 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

}

function str_encrypt($str) { 

	$mystr = RC4($str, KEY_FOR_RC4);
    $mystr = rawurlencode(base64_encode($mystr));
	return $mystr;
}

function str_decrypt($str) { 

	$mystr = base64_decode(rawurldecode($str));	
	$mystr =  RC4($mystr, KEY_FOR_RC4);
	return $mystr;
}

function RC4($data, $key) { 

	$x=0; $j=0; $a=0; $temp=""; $Zcrypt=""; 
	for ($i=0; $i<=255; $i++) {
 	 $counter[$i] = "";
	}
 
	$pwd = $key;
	$pwd_length = strlen($pwd); 
		
    for ($i = 0; $i < 255; $i++) { 
          $key[$i] = ord(substr($pwd, ($i % $pwd_length)+1, 1)); 
            $counter[$i] = $i; 
    } 
	for ($i = 0; $i < 255; $i++) { 
		$x = ($x + $counter[$i] + $key[$i]) % 256; 
		$temp_swap = $counter[$i]; 
		$counter[$i] = $counter[$x]; 
		$counter[$x] = $temp_swap; 

	} 
	for ($i = 0; $i < strlen($data); $i++) { 
					$a = ($a + 1) % 256; 
		$j = ($j + $counter[$a]) % 256; 
		$temp = $counter[$a]; 
		$counter[$a] = $counter[$j]; 
		$counter[$j] = $temp; 
		$k = $counter[(($counter[$a] + $counter[$j]) % 256)]; 
		$Zcipher = ord(substr($data, $i, 1)) ^ $k; 
		$Zcrypt .= chr($Zcipher); 
	}
	
	return $Zcrypt; 
}

?>