<?php 
/*
Clase que maneja el Tipo documento
*/
class CTipoMovimiento{
	private $db_link;
	private $_data;
	
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	
	public function getList($search=null){
		$search=mysql_real_escape_string($search); 

		$SQL="SELECT * FROM `tipo_movimiento` ";
		if ($search!=""){
			
		} 
		$rs=mysql_query($SQL);
		 
		$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>mysql_num_rows($rs),
				'aaData' =>array()
			);
		while($row=mysql_fetch_assoc($rs)){
			$row['enc_id']=System::getInstance()->Encrypt(json_encode($row));
			$row['settings']='<a href="#" id="'.$row['enc_id'].'" class="edit_mante_caja"><img src="images/subtract_from_cart.png"  /></a>'; 
			array_push($data['aaData'],$row); 
		}
		
		return $data;
	}
		
	public function validateMovExist($mov){
		$SQL="SELECT COUNT(*) AS TOTAL FROM tipo_movimiento where TIPO_MOV='". mysql_real_escape_string($mov) ."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['TOTAL'];
	} 
	 
	/*CREA EL TIPO DE DOCUMENTO*/
	public function create($_DATA){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		  
		$ob=new ObjectSQL();	
		$ob->TIPO_MOV=$_DATA["TIPO_MOV"];
		$ob->DESCRIPCION=$_DATA["DESCRIPCION"];
		$ob->CTA_CONTABLE=$_DATA["CTA_CONTABLE"];
		$ob->INTERNO=isset($_DATA['INTERNO'])?'S':'N';
		$ob->CASH=isset($_DATA['CASH'])?'S':'N';
		$ob->AUTORIZACION=isset($_DATA['AUTORIZACION'])?'S':'N';
		$ob->AFEC_CONTRATO=isset($_DATA['AFEC_CONTRATO'])?'S':'N';
		$ob->AFEC_RESERVA=isset($_DATA['AFEC_RESERVA'])?'S':'N';
		$ob->OPERACION=isset($_DATA['OPERACION'])?$_DATA['OPERACION']:'N'; 
		$ob->AFEC_CUOTA=isset($_DATA['AFEC_CUOTA'])?'S':'N'; 
		$ob->AFEC_MORA=isset($_DATA['AFEC_MORA'])?'S':'N'; 
		$ob->AFEC_MANTE=isset($_DATA['AFEC_MANTE'])?'S':'N';
		$ob->AFEC_CLIENTE=isset($_DATA['AFEC_CLIENTE'])?'S':'N';
		 
		 
		$ob->setTable("tipo_movimiento");
		$SQL=$ob->toSQL('insert');
		mysql_query($SQL); 
		$data['error']=false;
		$data['mensaje']='Registro agregado!';
		return $data;
	}	
	
	public function edit($_DATA){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		$doc=json_decode(System::getInstance()->Decrypt($_DATA['TIPO_MOV']));
 		 
		$ob=new ObjectSQL();	
	//	$ob->TIPO_MOV=$_DATA["TIPO_MOV"];
		$ob->DESCRIPCION=$_DATA["DESCRIPCION"];
		$ob->CTA_CONTABLE=$_DATA["CTA_CONTABLE"];
		$ob->INTERNO=isset($_DATA['INTERNO'])?'S':'N';
		$ob->CASH=isset($_DATA['CASH'])?'S':'N';
		$ob->AUTORIZACION=isset($_DATA['AUTORIZACION'])?'S':'N';
		$ob->AFEC_CONTRATO=isset($_DATA['AFEC_CONTRATO'])?'S':'N';
		$ob->AFEC_RESERVA=isset($_DATA['AFEC_RESERVA'])?'S':'N';
		$ob->OPERACION=isset($_DATA['OPERACION'])?$_DATA['OPERACION']:'N'; 
		$ob->AFEC_CUOTA=isset($_DATA['AFEC_CUOTA'])?'S':'N'; 
		$ob->AFEC_MORA=isset($_DATA['AFEC_MORA'])?'S':'N'; 
		$ob->AFEC_MANTE=isset($_DATA['AFEC_MANTE'])?'S':'N'; 
		$ob->AFEC_CLIENTE=isset($_DATA['AFEC_CLIENTE'])?'S':'N';
		$ob->setTable("tipo_movimiento");
		$SQL=$ob->toSQL('update',"where TIPO_MOV='".$doc->TIPO_MOV."'");
		mysql_query($SQL); 
		  
		$data['error']=false;
		$data['mensaje']='Registro actualizado!';
		return $data;
	}
	 
}

?>