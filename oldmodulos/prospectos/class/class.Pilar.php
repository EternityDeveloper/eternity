<?php
/*
	Maneja los pilares del sistema
*/
class Pilar{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
  
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	/*AGREGA UN PILAR*/
	public function addPilar($data){
	
		$retur=array("mensaje"=>"Registro agregado","error"=>false);
	 	
		if ((trim($data['idtipo_pilar'])!="") && 
			(trim($data['dscrip_tipopilar'])!="") && 
			(trim($data['dias_proteccion'])!="")){
				
			$obj = new ObjectSQL();
			$obj->idtipo_pilar=$data['idtipo_pilar'];
			$obj->dscrip_tipopilar=$data['dscrip_tipopilar'];
			$obj->dias_proteccion=$data['dias_proteccion'];
			
			$SQL=$obj->getSQL("insert","tipos_pilares");
			mysql_query($SQL);
		}else{
			$retur['mensaje']="Error todos los campos son obligatorios";	
			$retur['error']=true;
		}
		return $retur;
	}
	/*ESTE METODO ACTUALIZA UN PILAR HAY QUE PASARLE UN OBJETO CON EL IDTIPO_PILAR
	Y LA DATA PARA ACTUALIZAR*/
	public function updatePilar($pilar,$data){
		$obj = new ObjectSQL(); 
		$obj->dscrip_tipopilar=$data['dscrip_tipopilar'];
		$obj->dias_proteccion=$data['dias_proteccion'];
		$obj->estatus=$data['estatus'];
		
		$SQL=$obj->getSQL("update","tipos_pilares","where idtipo_pilar='". mysql_escape_string($pilar->idtipo_pilar) ."'");
		mysql_query($SQL);	
		
		return $retur=array("mensaje"=>"Registro actualizado","error"=>false);
	}
	
	public function getListFromQuestion($pilar){
		 
		$SQL=" SELECT count(*) as total FROM `detalle_tipos_pilar` 
				INNER JOIN sys_status ON (detalle_tipos_pilar.estatus=sys_status.id_status) 
		 WHERE detalle_tipos_pilar.`idtipo_pilar`='".$pilar->idtipo_pilar."'"; 
		
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		 
		$SQL=" SELECT * FROM `detalle_tipos_pilar` 
			INNER JOIN sys_status ON (detalle_tipos_pilar.estatus=sys_status.id_status) 
		 WHERE detalle_tipos_pilar.`idtipo_pilar`='".$pilar->idtipo_pilar."' "; 
		 
		$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
		 
		$data=array(
			'sEcho'=>$_REQUEST['sEcho'],
			'iTotalRecords'=>10,
			'iTotalDisplayRecords'=>$total_row,
			'aaData' =>array()
		);
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){	
			$encriptID=System::getInstance()->Encrypt(json_encode($row));
			array_push($data['aaData'],
				array(
					"id_pregunta"=>$row['id_pregunta']."",
					"pregunta"=>$row['pregunta_det_prospec']."",
					"tipo_respuesta"=>$row['tipo_resp_det_prospec']."",
					"estatus"=>$row['descripcion']."",
					"option1"=>'<a href="#" class="prosp_add_link" id="'.$encriptID.'"><img src="images/subtract_from_cart.png" width="27" height="28" /></a>',
					"prospecto_id"=>$encriptID			
				)
			);
		 
		}
		
		return $data;
	}
	
	/*AGREGA UNA PREGUNTA A UN PILAR*/
	public function addPilarQuestion($pilar,$data){
		$retur=array("mensaje"=>"Registro agregado","error"=>false);
	 
		$obj = new ObjectSQL();
		$obj->id_pregunta=$data['id_pregunta'];
		$obj->idtipo_pilar=$pilar->idtipo_pilar;
		$obj->pregunta_det_prospec=$data['pregunta_det_prospec'];
		$obj->tipo_resp_det_prospec=$data['tipo_resp_det_prospec'];
		
		switch($data['tipo_resp_det_prospec']){
			case "valores":
				$obj->valor1_det_prospec=$data['valor1'];
				$obj->valor2_det_prospec=$data['valor2'];
				$obj->valor3_det_prospec=$data['valor3'];
				$obj->valor4_det_prospec=$data['valor4'];
				$obj->valor5_det_prospec=$data['valor5'];
				
			break;		
		}	
		$SQL=$obj->getSQL("insert","detalle_tipos_pilar");
		mysql_query($SQL);
		
		return $retur;
	}
	
	/*EDITA UNA PREGUNTA A UN PILAR*/
	public function editPilarQuestion($question,$data){
		$retur=array("mensaje"=>"Registro actualizado","error"=>false);
	 
		$obj = new ObjectSQL(); 
		$obj->pregunta_det_prospec=$data['pregunta_det_prospec'];
		$obj->tipo_resp_det_prospec=$data['tipo_resp_det_prospec'];
		$obj->estatus=$data['estatus'];
		
		switch($data['tipo_resp_det_prospec']){
			case "valores":
				$obj->valor1_det_prospec=$data['valor1'];
				$obj->valor2_det_prospec=$data['valor2'];
				$obj->valor3_det_prospec=$data['valor3'];
				$obj->valor4_det_prospec=$data['valor4'];
				$obj->valor5_det_prospec=$data['valor5'];
				
			break;		
		}	 
		$SQL=$obj->getSQL("update","detalle_tipos_pilar"," where id_pregunta='".$question->id_pregunta."'");
		mysql_query($SQL);
		
		return $retur;
	}	
	
}
?>