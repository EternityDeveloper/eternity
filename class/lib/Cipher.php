<?php

class Cipher {
    private $securekey, $iv;
 /*
    function __construct($textkey) {
        $this->securekey = md5($textkey);//hash('sha256',$textkey,TRUE);
        $this->iv = mcrypt_create_iv(32);
    }
   	function encrypt($input,$key) {
		$this->securekey = md5($textkey);
		$this->iv = mcrypt_create_iv(32);
        return base64_encode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv)));
    }
    function decrypt($input,$key) {
		$this->securekey = md5($textkey);
		$this->iv = mcrypt_create_iv(32);		
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->securekey, base64_decode(base64_decode($input)), MCRYPT_MODE_ECB, $this->iv));
    }
	*/
	
	function encrypt($str, $key){
		 $block = mcrypt_get_block_size('rijndael_128', 'ecb');
		 $pad = $block - (strlen($str) % $block);
		 $str .= str_repeat(chr($pad), $pad);
		 return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB));
	}
	
	function decrypt($str, $key){ 
		 $str = base64_decode($str);
		 //echo " dato --> ".$str." <--aqui test";
		 $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
		 $block = mcrypt_get_block_size('rijndael_128', 'ecb');
		 $pad = ord($str[($len = strlen($str)) - 1]);
		 $len = strlen($str);
		 $pad = ord($str[$len-1]);
		 return substr($str, 0, strlen($str) - $pad);
	}
	
}

?>