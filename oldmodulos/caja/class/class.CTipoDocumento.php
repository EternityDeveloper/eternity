<?php 
/*
Clase que maneja los Tipo Movimiento en el area de caja
*/
class CTipoDocumento{
	private $db_link;
	private $_data;
	private $_session_name="CDOC"; 
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	/*setea un Objecto Tipo de movimiento*/
	public function setTMov($tmov){
	 	if (!isset($_SESSION[$_session_name])){
			$_SESSION[$_session_name]=$tmov;
		}else{
			$_SESSION[$_session_name]=$tmov;
		}
	}
	public function getTipoDoc($doc){
		$SQL="SELECT * FROM `tipo_documento` WHERE `TIPO_DOC`='". mysql_real_escape_string($doc) ."'";
 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
 			$row['encode']=System::getInstance()->Encrypt(json_encode($row));
			array_push($data,$row); 
		}
		return $data;
	}
	public function getSerieDoc($doc){
		$SQL="SELECT * FROM `correlativo_doc` WHERE `TIPO_DOC`='". mysql_real_escape_string($doc) ."'";
 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row); 
		}
		return $data;
	} 
	/**/
	public function getLabelToSearch(){
		$tipo_mov=$this->getTipoMov();
		$ret="";
		if ($tipo_mov->AFEC_CLIENTE=="S"){
			$ret.='<label> <input type="radio" name="search" value="CLIENTE" id="search">&nbsp;Cliente</label><label>&nbsp;&nbsp;';
		}		
		if ($tipo_mov->AFEC_CONTRATO=="S"){
			$ret.='<label> <input type="radio" name="search" value="CONTRATO" id="search">&nbsp;Contrato</label><label>&nbsp;&nbsp;';
		}
		if ($tipo_mov->AFEC_RESERVA=="S"){
			$ret.='<label> <input type="radio" name="search" value="RESERVA" id="search">&nbsp;Reserva</label><label>';
		}	
		
		return $ret;	
	}
	  
}

?>