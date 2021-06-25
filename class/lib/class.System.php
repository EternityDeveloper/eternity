<?php


class System{
	private static $instance;
	private $encrytp;
	public function __construct(){
		$this->encrytp = new Security();		
	}
	public static function getInstance(){
		 if (!System::$instance instanceof self) {
             System::$instance = new System();
        }
        return System::$instance;
	}

	public function getCurrentModulo(){
		if (isset($_REQUEST)){
			foreach($_REQUEST as $key =>$val ){
				if (substr($key,0,3)=="mod"){
					return substr($key,4,strlen($key));
					break;
				}
			}
		}
		return "";
	}
	public function getEncrypt(){
		return $this->encrytp;
	}
	public function Decrypt($val){
		return $this->encrytp->decrypt($val,UserAccess::getInstance()->getSessionID());
	}
	public function Encrypt($val){
		return $this->encrytp->encrypt($val,UserAccess::getInstance()->getSessionID());
	}	
	public function Request($name){
		//echo $_REQUEST[$name]."\n";
		return System::getInstance()->getEncrypt()->decrypt($_REQUEST[$name],UserAccess::getInstance()->getSessionID());
	}
}



?>