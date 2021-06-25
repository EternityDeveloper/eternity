<?php
class Security {
 	public static function encrypt($str, $key){
		 $key=md5($key);
		 $block = mcrypt_get_block_size('rijndael_128', 'ecb');
		 $pad = $block - (strlen($str) % $block);
		 $str .= str_repeat(chr($pad), $pad);
		 return base64_encode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB)));
	} 

	public static function decrypt($str, $key){ 
		 $str = base64_decode(base64_decode($str));
		 $key=md5($key);
		// echo " dato --> ".$str." <--aqui test";
		 $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
		 $block = mcrypt_get_block_size('rijndael_128', 'ecb');
		 $pad = ord($str[($len = strlen($str)) - 1]);
		 $len = strlen($str);
		 $pad = ord($str[$len-1]);
		 return substr($str, 0, strlen($str) - $pad);
	}/*	
	 
	
	public static function encrypt($string, $key) {
	   $result = '';
	   for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
		  $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)+ord($keychar));
		  $result.=$char;
	   }
	   return base64_encode($result);
	}
	public static function decrypt($string, $key) {
	   $result = '';
	   $string = base64_decode($string);
	   for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
		  $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)-ord($keychar));
		  $result.=$char;
	   }
	   return $result;
	}	*/
	

}
?>