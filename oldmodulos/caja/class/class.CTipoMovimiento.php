<?php 
/*
Clase que maneja los Tipo Movimiento en el area de caja
*/
class CTipoMovimiento{
	private $db_link;
	private $_data;
	private $_session_name="TMOV"; 
	private $_session_select="TMOVSELECT"; 
	private $type_select="";
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	/*setea un Objecto Tipo de movimiento*/
	public function setTMov($tmov){
	 	if (!isset($_SESSION[$this->_session_name])){
			$_SESSION[$this->_session_name]=$tmov;
		}else{
			$_SESSION[$this->_session_name]=$tmov;
		}
	}
	public function getTIPO_MOV($mov){
		$SQL="SELECT * FROM `tipo_movimiento` WHERE `TIPO_MOV`='". mysql_real_escape_string($mov) ."'";  
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row; 
		}
		return $data;
	} 	
	public function getTipoMov(){ 
		if (!isset($_SESSION[$this->_session_name])){
			return array();
		}  
		return $_SESSION[$this->_session_name]; 
	} 
	/*setea */
	public function setTMovSelected($tmov){
		$_SESSION[$this->_session_select]=array();
		$_SESSION[$this->_session_select]['selected']=$tmov;
	}
	public function getTMovSelected(){
		return $_SESSION[$this->_session_select]['selected'];
	}
	/**/
	public function getLabelToSearch(){
		$tipo_mov=$this->getTipoMov();
 
		$ret="";
		if ($tipo_mov->AFEC_CLIENTE=="S"){
			$ret.='<label> <input type="radio" name="search" value="CLIENTE" id="search1">&nbsp;Cliente</label><label>&nbsp;&nbsp;';
		}		
		if ($tipo_mov->AFEC_CONTRATO=="S"){
			$ret.='<label> <input type="radio" name="search" value="CONTRATO" id="search2">&nbsp;Contrato</label><label>&nbsp;&nbsp;';
		}
		if ($tipo_mov->AFEC_RESERVA=="S"){
			$ret.='<label> <input type="radio" name="search" value="RESERVA" id="search3">&nbsp;Reserva</label><label>';
		}	
		
		return $ret;	
	}
	  
}

?>