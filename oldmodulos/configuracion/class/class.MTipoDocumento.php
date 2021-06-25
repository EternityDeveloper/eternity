<?php 
/*
Clase que maneja el Mantenimiento de los  Tipo documento
*/
class MTipoDocumento{
	private $db_link;
	private $_data;
	
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	
	public function getList($search=null){
		$search=mysql_real_escape_string($search); 

		$SQL="SELECT * FROM `tipo_documento` ";
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
		
	public function validateDocExist($doc){
		$SQL="SELECT COUNT(*) AS TOTAL FROM tipo_documento where TIPO_DOC='". mysql_real_escape_string($doc) ."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['TOTAL'];
	} 
	 
	/*CREA EL TIPO DE DOCUMENTO*/
	public function create($_DATA){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		  
		$ob=new ObjectSQL();	
		$ob->TIPO_DOC=$_DATA["TIPO_DOC"];
		$ob->DOCUMENTO=$_DATA["descripcion"];
		$ob->FISCAL=isset($_DATA['fiscal'])?'S':'N';
		$ob->ANULA_MOVI=isset($_DATA['anula_mov'])?'S':'N';
		$ob->REP_VENTA=isset($_DATA['repo_venta'])?'S':'N';
		$ob->IMPRESION=isset($_DATA['imprime'])?'S':'N';
		 
		$ob->setTable("tipo_documento");
		$SQL=$ob->toSQL('insert');
		mysql_query($SQL); 
		$data['error']=false;
		$data['mensaje']='Registro agregado!';
		return $data;
	}	
	
	public function edit($_DATA){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		$doc=json_decode(System::getInstance()->Decrypt($_DATA['TIPO_DOC']));
 		 
		$ob=new ObjectSQL();	
		$ob->DOCUMENTO=trim($_DATA["descripcion"]);
		$ob->FISCAL=isset($_DATA['fiscal'])?'S':'N';
		$ob->ANULA_MOVI=isset($_DATA['anula_mov'])?'S':'N';
		$ob->REP_VENTA=isset($_DATA['repo_venta'])?'S':'N';
		$ob->IMPRESION=isset($_DATA['imprime'])?'S':'N';
		$ob->setTable("tipo_documento");
		$SQL=$ob->toSQL('update',"where TIPO_DOC='".$doc->TIPO_DOC."'");
		mysql_query($SQL); 
		 
		$data['error']=false;
		$data['mensaje']='Registro actualizado!';
		return $data;
	}
	 
}

?>