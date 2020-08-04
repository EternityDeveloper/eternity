<?php
/*
	Controla que no pase un loop desde javascript cuando se utliza post o get
	a la hora de insertar algun item
*/
class STCSession{
	private static $db_link;
	private $_data;
	private $token;
	private $name="STC_CONTTROLER";
	private static $instance;
	
	public function __construct(){
 		STCSession::$instance = $this;
 	}  
	public static function getInstance(){
		 if (!STCSession::$instance instanceof self) {
             STCSession::$instance = new STCSession();
        }
        return STCSession::$instance;
	}
	public static function GI(){
        return STCSession::getInstance();
	}	 
	public function setSubmit($session,$val){
		if (!isset($_SESSION[$this->name])){
			$_SESSION[$this->name]=array();	
		}
		$_SESSION[$this->name][$session]=$val;
	}
	public function isSubmit($session){
		return $_SESSION[$this->name][$session];
	}
	public function getSubmit($session){
		return $_SESSION[$this->name][$session];
	}	
}
?>