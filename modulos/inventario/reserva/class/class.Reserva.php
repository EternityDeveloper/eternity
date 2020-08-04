<?php

/*
	Esta clase maneja la insercion de datos
	de una reserva
*/
class Reserva{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true,"typeError"=>0);
	private $errorList=array(
		100 => "Reserva registrada correctamente!",
		101 => "Este identficador de cliente existe, el sistema no acepta duplicados!",
		102 => "No se pudo completar la operacion",
		103 => "Debe de llenar todos los campos obligatorios.",
		104 => "Registro actualizado correctamente!",
		105 => "Reserva no existe",
		106 => "Error, no se puede realizar esta reserva debido a que ya ha sido procesada"
	);
  
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	
	public function validateReserva($id_jardin,$id_fases,$lote,$bloque,$osario){
		$SQL="SELECT 
				COUNT(*) AS total 
			FROM 
			`reserva_inventario` 
			INNER JOIN `reserva_ubicaciones` ON(`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva)
			WHERE reserva_ubicaciones.estatus=1 and reserva_ubicaciones.id_jardin='".$id_jardin."' 
			AND reserva_ubicaciones.id_fases='".$id_fases."' AND reserva_ubicaciones.lote='".$lote."' 
			AND reserva_ubicaciones.osario='".$osario."' AND reserva_ubicaciones.bloque='".$bloque."' "; 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);			
		return $row['total']>0?true:false;
	}
	
	public function validateRecibo($recibo){
		$SQL="SELECT 
				COUNT(*) AS total 
			FROM 
			`reserva_inventario` 
			INNER JOIN `reserva_ubicaciones` ON(`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva)
			WHERE reserva_ubicaciones.estatus=1 and reserva_inventario.no_recibo='".$recibo."' ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);			
		return $row['total']>0?true:false;
	}	
	
	public function reservaAbono(){
		$reserva=json_decode($this->data['forma_pago']);
		$data=json_decode($this->data['json']);	
		$asesor_data=$reserva->asesor_data;
		$monto_abono= $this->data['monto_abono'];
		$rExist=false;
		$EM_ID="";
		foreach($data as $key =>$val){
			$producto=json_decode(System::getInstance()->Decrypt($val));
			$EM_ID=$producto->eEM_ID;
		
			if ($this->validateReserva($producto->id_jardin,
										$producto->id_fases,
										$producto->lote,
										$producto->bloque,
										$producto->osario)){
				$rExist=true;
			}
		}
	 		
		if ($rExist){
			$this->message['mensaje']=$this->errorList[106];
			$this->message['error']=true;
			$this->message['typeError']="106";
			return 0;	
		}
	 
		$tb_reserva= new ObjectSQL();
		$tb_reserva->id_reserva=$reserva->tipo_reserva;
		//$tb_reserva->no_recibo=$reserva->no_recibo;
		
		$tb_reserva->id_comercial=$asesor_data->data->id_comercial;
		$tb_reserva->nit_comercial=$asesor_data->data->id_nit; 
		
		$tb_reserva->id_nit=System::getInstance()->Decrypt($reserva->personal_data->id_nit);
		$tb_reserva->fecha_reserva="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$tb_reserva->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		/* AGREGANDO 30 DIAS A LA FORMA DE PAGO EN EFECTIVO*/
		$tb_reserva->fecha_fin="DATE_ADD(CONCAT(CURDATE(),' ',CURRENT_TIME()), INTERVAL 30 DAY)";
		///////////////////////////////////
		$tb_reserva->estatus='1';
		$tb_reserva->EM_ID=$EM_ID;
		$SQL=$tb_reserva->getSQL("insert","reserva_inventario");
	 	mysql_query($SQL);
		$no_reserva=mysql_insert_id($this->db_link->link_id);
		
	//	$no_reserva=5;
		SysLog::getInstance()->Reserva($tb_reserva->id_nit,
									 "RESERVA CLIENTE",
									 $no_reserva,
									 $tb_reserva->id_reserva,
									 json_encode($tb_reserva));
		 
		/*PRECIO MINIMO RESERVA CREACION DE RECIBO PARA RESERVA*/
		/*$monto=count($data)*2000;
		MVFactura::getInstance()->doMovReserva($tb_reserva->id_nit,
											   $tb_reserva->id_reserva,
											   $no_reserva,
											   $monto_abono);*/
									 
 
		//echo $id; 
		if ($no_reserva>0){ 
  
			foreach($data as $key =>$val){
				$row=json_decode(System::getInstance()->Decrypt($val));
				///AGREGO LOS JARDINES QUE SE APARTARON
				$inv_jardines= new ObjectSQL();
				$inv_jardines->id_reserva=$reserva->tipo_reserva;
				$inv_jardines->no_reserva=$no_reserva;
				$inv_jardines->estatus="3"; 
				$SQL=$inv_jardines->getSQL("update","inventario_jardines"," where 
													bloque='". mysql_escape_string($row->bloque) ."' and
													lote='". mysql_escape_string($row->lote) ."' and
													id_fases='". mysql_escape_string($row->id_fases) ."' and
													id_jardin='". mysql_escape_string($row->id_jardin) ."' and 
													osario='". mysql_escape_string($row->osario) ."' ");
				mysql_query($SQL);
				
				///AGREGO LOS JARDINES A LAS RESERVA DE UBICACIONES
				$res_jardines= new ObjectSQL();
				$res_jardines->id_reserva=$reserva->tipo_reserva;
				$res_jardines->no_reserva=$no_reserva;
                                $res_jardines->EM_ID=$EM_ID;
				$res_jardines->id_jardin=$row->id_jardin; 
				$res_jardines->id_fases=$row->id_fases; 
				$res_jardines->lote=$row->lote; 
				$res_jardines->bloque=$row->bloque; 
				$res_jardines->osario=$row->osario;  
				$SQL=$res_jardines->getSQL("insert","reserva_ubicaciones");
				mysql_query($SQL);
				
				SysLog::getInstance()->Log($tb_reserva->id_nit, 
											 "",
											 "",
											 $no_reserva,
											 $tb_reserva->id_reserva,
											 "RESERVA UBICACION",
											 json_encode($inv_jardines));					
		  	
			}
 
			$this->message['mensaje']=$this->errorList[100];
			$this->message['error']=false;
			$this->message['typeError']="100";
			$this->message['no_reserva']=$no_reserva;
			
			
			
		}else{
			$this->message['mensaje']=$this->errorList[102];
			$this->message['error']=true;
			$this->message['typeError']="102";
		}
	}

  	/*OPTIENE EL NUMERO DE PARCELAS EN LA RESERVA*/
	public function getTotalReserva($no_reserva){
		$SQL="SELECT 
				COUNT(*) AS total 
			FROM 
			`reserva_inventario` 
			INNER JOIN `reserva_ubicaciones` ON(`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva)
			WHERE reserva_inventario.no_reserva='". mysql_real_escape_string($no_reserva) ."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);			
		return $row['total'];
	}
	
	/* OPTIENE LOS DATOS DE LA RESERVA X*/
	public function getReserva($no_reserva){
		$SQL="SELECT 
				reserva_inventario.*
			FROM 
			`reserva_inventario` 
			INNER JOIN `reserva_ubicaciones` ON(`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva)
			WHERE reserva_inventario.no_reserva='". mysql_real_escape_string($no_reserva) ."' limit 1";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);			
		return $row;
	}
	
	/* OPTIENE LOS DATOS DE LA RESERVA X*/
	public function getDataReserva($no_reserva){
		$SQL="SELECT  
			`reserva_inventario`.no_reserva, 
			DATEDIFF(reserva_inventario.`fecha_fin`,CURDATE()) AS day_restantes,
			DATE_FORMAT(reserva_inventario.fecha_reserva, '%d-%m-%Y %h:%m:%s') AS  fecha_reserva,
			DATE_FORMAT(reserva_inventario.fecha_fin, '%d-%m-%Y %h:%m:%s') AS  fecha_fin,
			CONCAT(asesor.`primer_nombre`,' ',asesor.`primer_apellido`) AS nombre_asesor,
			CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) AS nombre_cliente,
			CONCAT(asesor.`primer_nombre`,' ',asesor.`segundo_nombre`,' ',asesor.`primer_apellido`,' ',asesor.`segundo_apellido`) AS nombre_asesor,
			asesor.id_nit AS nit_asesor,
			sys_personas.id_nit AS nit_cliente,
			reserva_inventario.EM_ID, 
			reserva_inventario.id_comercial,
			jardines.jardin 
				FROM reserva_inventario
			INNER JOIN `reserva_ubicaciones` ON (`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva) 
			INNER JOIN `jardines` ON (`jardines`.`id_jardin`=reserva_ubicaciones.id_jardin) 
			INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
			INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
			INNER JOIN `sys_personas` ON (sys_personas.`id_nit`=reserva_inventario.`id_nit`)
			INNER JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=reserva_inventario.`nit_comercial`)
		WHERE reserva_ubicaciones.estatus=1 AND reserva_inventario.no_reserva='". mysql_real_escape_string($no_reserva) ."' ";
		$rs=mysql_query($SQL);
		$data=array(
			'no_reserva'=>0,
			'EM_ID'=>'',
			'id_nit'=>0,
			'nit_comercial'=>'', 
			'nombre_cliente'=>'',
			'nombre_asesor'=>'',
			'res'=>array()
		);
		while($row=mysql_fetch_assoc($rs)){
			$data['no_reserva']=$row['no_reserva'];
			$data['EM_ID']=$row['EM_ID'];
			$data['id_nit']=$row['nit_cliente'];
			$data['nit_comercial']=$row['id_comercial'];
			$data['nombre_cliente']=$row['nombre_cliente'];
			$data['nombre_asesor']=$row['nombre_asesor'];
			
			array_push($data['res'],$row);	 
		}
		return $data;
	}
		
	/**/		
	public function reservaXHoras(){
		$reserva=json_decode($this->data['forma_pago']);
		$data=json_decode($this->data['json']);	
	        $asesor_data=$reserva->asesor_data;	
		$tb_reserva= new ObjectSQL();
		$tb_reserva->id_reserva=$reserva->tipo_reserva;
                $tb_reserva->id_comercial=$asesor_data->data->id_comercial;
                $tb_reserva->nit_comercial=$asesor_data->data->id_nit;
	//	$tb_reserva->asesor='';
                $tb_reserva->EM_ID=$EM_ID;
		$tb_reserva->id_nit=System::getInstance()->Decrypt($reserva->personal_data->id_nit);
		$tb_reserva->fecha_reserva="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$tb_reserva->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		/* AGREGANDO 48 HORAS  DE PROROGA PARA PAGAR*/
		$tb_reserva->fecha_fin="DATE_ADD(CONCAT(CURDATE(),' ',CURRENT_TIME()), INTERVAL 48 HOUR)";
		///////////////////////////////////
		$tb_reserva->estatus='1';
		$SQL=$tb_reserva->getSQL("insert","reserva_inventario");
		mysql_query($SQL);
		
		$no_reserva=mysql_insert_id($this->db_link->link_id);
 
		if ($no_reserva>0){
			
			foreach($data as $key =>$val){
				$row=json_decode(System::getInstance()->Decrypt($val));
				/*AGREGO LOS JARDINES QUE SE APARTARON*/
				$inv_jardines= new ObjectSQL();
				$inv_jardines->id_reserva=$reserva->tipo_reserva;
				$inv_jardines->no_reserva=$no_reserva;
				$inv_jardines->estatus="3"; 
				$SQL=$inv_jardines->getSQL("update","inventario_jardines"," where 
				bloque='". mysql_escape_string($row->bloque) ."' and
				lote='". mysql_escape_string($row->lote) ."' and
				id_fases='". mysql_escape_string($row->id_fases) ."' and
				id_jardin='". mysql_escape_string($row->id_jardin) ."' ");
				mysql_query($SQL);
				
				
				/*AGREGO LOS JARDINES A LAS RESERVA DE UBICACIONES*/
				$res_jardines= new ObjectSQL();
				$res_jardines->id_reserva=$reserva->tipo_reserva;
                                $res_jardines->EM_ID=$EM_ID;
				$res_jardines->no_reserva=$no_reserva;
				$res_jardines->id_jardin=$row->id_jardin; 
				$res_jardines->id_fases=$row->id_fases; 
				$res_jardines->lote=$row->lote; 
				$res_jardines->bloque=$row->bloque; 
				$SQL=$res_jardines->getSQL("insert","reserva_ubicaciones");
				mysql_query($SQL);
			}
 
			$this->message['mensaje']=$this->errorList[100];
			$this->message['error']=false;
			$this->message['typeError']="100";
			
			
		}else{
			$this->message['mensaje']=$this->errorList[102];
			$this->message['error']=true;
			$this->message['typeError']="102";
		}
	}
	
	public function reservaXGerencia(){
		$reserva=json_decode($this->data['forma_pago']);
		$data=json_decode($this->data['json']);	
		
		$tb_reserva= new ObjectSQL();
		$tb_reserva->id_reserva=$reserva->tipo_reserva;
                $tb_reserva->id_comercial=$asesor_data->data->id_comercial;
                $tb_reserva->nit_comercial=$asesor_data->data->id_nit;
	//	$tb_reserva->asesor='';
		$tb_reserva->id_nit=System::getInstance()->Decrypt($reserva->personal_data->id_nit);
		$tb_reserva->fecha_reserva="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$tb_reserva->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		/* AGREGANDO 80 MESES DE RESERVA POR SER GERENCIA*/
		$tb_reserva->fecha_fin="DATE_ADD(CONCAT(CURDATE(),' ',CURRENT_TIME()), INTERVAL 80 MONTH)";
		///////////////////////////////////
		$tb_reserva->estatus='1';
		$SQL=$tb_reserva->getSQL("insert","reserva_inventario");
		mysql_query($SQL);
		
		$no_reserva=mysql_insert_id($this->db_link->link_id);
 
		if ($no_reserva>0){
			
			foreach($data as $key =>$val){
				$row=json_decode(System::getInstance()->Decrypt($val));
				/*AGREGO LOS JARDINES QUE SE APARTARON*/
				$inv_jardines= new ObjectSQL();
				$inv_jardines->id_reserva=$reserva->tipo_reserva;
				$inv_jardines->no_reserva=$no_reserva;
				$inv_jardines->estatus="3"; 
				$SQL=$inv_jardines->getSQL("update","inventario_jardines"," where 
				bloque='". mysql_escape_string($row->bloque) ."' and
				lote='". mysql_escape_string($row->lote) ."' and
				id_fases='". mysql_escape_string($row->id_fases) ."' and
				id_jardin='". mysql_escape_string($row->id_jardin) ."' ");
				mysql_query($SQL);
				

				/*AGREGO LOS JARDINES A LAS RESERVA DE UBICACIONES*/
				$res_jardines= new ObjectSQL();
				$res_jardines->id_reserva=$reserva->tipo_reserva;
				$res_jardines->no_reserva=$no_reserva;
                                $res_jardines->EM_ID=$EM_ID;
				$res_jardines->id_jardin=$row->id_jardin; 
				$res_jardines->id_fases=$row->id_fases; 
				$res_jardines->lote=$row->lote; 
				$res_jardines->bloque=$row->bloque; 
				$SQL=$res_jardines->getSQL("insert","reserva_ubicaciones");
				mysql_query($SQL);
			}
 
			$this->message['mensaje']=$this->errorList[100];
			$this->message['error']=false;
			$this->message['typeError']="100";
			
			
		}else{
			$this->message['mensaje']=$this->errorList[102];
			$this->message['error']=true;
			$this->message['typeError']="102";
		}
	} 
	
	public function getMessages(){
		return $this->message;
	}
	
	/* Actualiza la reserva a estatus 18 completado  */
	public function reservaUsada($no_reserva,$id_reserva){
		$rs= new ObjectSQL();
		$rs->estatus='18';
		$rs->setTable('reserva_inventario');
		$SQL=$rs->toSQL("update"," where no_reserva='".$no_reserva."' and id_reserva='".$id_reserva."'");
		mysql_query($SQL);	
	}
	
	/* INACTIVAR LA RESERVA */
	public function inactiveReserva($no_reserva,$id_jardin,$id_fases,$lote,$bloque){
		$SQL="SELECT count(*) as total FROM `reserva_ubicaciones` WHERE `no_reserva`='". mysql_real_escape_string($no_reserva) ."' AND `id_jardin`='". mysql_real_escape_string($id_jardin) ."' AND `id_fases`='". mysql_real_escape_string($id_fases) ."' AND `lote`='". mysql_real_escape_string($lote) ."' AND `bloque` ='". mysql_real_escape_string($bloque) ."' ";
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);			
		if ($row['total']>0){
			$rs= new ObjectSQL();
			$rs->estatus='2';
			$rs->setTable('reserva_ubicaciones');
			$SQL=$rs->toSQL("update"," WHERE `no_reserva`='". mysql_real_escape_string($no_reserva) ."' AND `id_jardin`='". mysql_real_escape_string($id_jardin) ."' AND `id_fases`='". mysql_real_escape_string($id_fases) ."' AND `lote`='". mysql_real_escape_string($lote) ."' AND `bloque` ='". mysql_real_escape_string($bloque) ."' ");
			mysql_query($SQL);
		
		
			$rs= new ObjectSQL();
			$rs->estatus='1';
			$rs->no_reserva='';
			$rs->id_reserva='0';
			$rs->setTable('inventario_jardines');
			$SQL=$rs->toSQL("update"," WHERE `no_reserva`='". mysql_real_escape_string($no_reserva) ."' AND `id_jardin`='". mysql_real_escape_string($id_jardin) ."' AND `id_fases`='". mysql_real_escape_string($id_fases) ."' AND `lote`='". mysql_real_escape_string($lote) ."' AND `bloque` ='". mysql_real_escape_string($bloque) ."'  AND estatus=3 ");		
			
			mysql_query($SQL);
			
			return array("mensaje"=>"Item removido","error"=>true);
		}else{
			return array("mensaje"=>"Error item no fué removido","error"=>false);
		}
		
	}
	
	public function getIfReservaContainTipoMovimiento($tipo,$no_reserva){
		$SQL="
			SELECT 
				count(movimiento_caja.TIPO_MOVIMIENTO) AS tt
			FROM `movimiento_caja` 
				INNER JOIN `forma_pago_caja` ON (forma_pago_caja.`NO_DOCUMENTO_CAJA`=movimiento_caja.NO_DOCUMENTO_CAJA)
				INNER JOIN `formas_pago` ON (formas_pago.`forpago`=forma_pago_caja.forpago)
			WHERE 
				movimiento_caja.TIPO_MOVIMIENTO='".$tipo."' AND 
				movimiento_caja.`NO_RESERVA_CAJA`='". mysql_real_escape_string($no_reserva) ."' limit 1";
	 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);			
		 
		return $row['tt'];
	}
	
	/*OPTENER UN CLIENTE CON RESERVA*/
	public function getClientWithProspecto(){
		
		$data=array(
			'sEcho'=>$_REQUEST['sEcho'],
			'iTotalRecords'=>10,
			'iTotalDisplayRecords'=>$total_row,
			'aaData' =>array()
		);
				
		if (isset($_REQUEST['dt_list'])){
 
				$QUERY="";
				$HAVING="";
				if (isset($_REQUEST['sSearch'])){
				  if (trim($_REQUEST['sSearch'])!=""){
					$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
					$QUERY="  AND  (sys_personas.id_nit LIKE '%".$_REQUEST['sSearch']."%' OR CONCAT(sys_personas.primer_nombre,' ' , sys_personas.segundo_nombre) LIKE '%".$_REQUEST['sSearch']."%' OR sys_personas.fecha_nacimiento LIKE '%".$_REQUEST['sSearch']."%' OR CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) LIKE '%".$_REQUEST['sSearch']."%' 
		OR CONCAT(sys_personas.primer_nombre,' ',sys_personas.primer_apellido) LIKE '%".$_REQUEST['sSearch']."%' 
		OR CONCAT(sys_personas.segundo_nombre,' ',sys_personas.primer_apellido) LIKE '%".$_REQUEST['sSearch']."%' 
		OR CONCAT(sys_documentos_identidad.`descripcion`,' ',sys_personas.id_nit) LIKE '%".$_REQUEST['sSearch']."%'
		) "; 
				
				  }
				}
	
				$SQL=" SELECT count(*) as total FROM sys_personas
			INNER JOIN prospecto_comercial ON (prospecto_comercial.id_nit=sys_personas.id_nit)
			LEFT JOIN sys_clasificacion_persona ON (sys_clasificacion_persona.id_clasificacion=sys_personas.`id_clasificacion`)
			LEFT JOIN `sys_documentos_identidad` ON (`sys_documentos_identidad`.`id_documento`=sys_personas.`id_documento`) 
			 WHERE   prospecto_comercial.estatus IN (6,4)  ";
				
				$SQL.=$QUERY;
				 
	
				$rs=mysql_query($SQL);
				$row=mysql_fetch_assoc($rs);
				$total_row=$row['total'];
			
				$SQL="SELECT  
						sys_personas.id_nit,
						CONCAT(sys_personas.primer_nombre,' ' ,
						sys_personas.segundo_nombre) AS nombre,
						CONCAT(sys_personas.primer_apellido,' ',
						sys_personas.segundo_apellido) AS apellido,
						sys_clasificacion_persona.descripcion AS clasificacion,
						DATE_FORMAT(sys_personas.fecha_nacimiento, '%d-%m-%Y') AS fecha_nacimiento,
						sys_documentos_identidad.`descripcion`,
						prospecto_comercial.codigo_asesor as id_comercial
					 FROM sys_personas
			LEFT JOIN prospecto_comercial ON (prospecto_comercial.id_nit=sys_personas.id_nit)
			LEFT JOIN sys_clasificacion_persona ON (sys_clasificacion_persona.id_clasificacion=sys_personas.`id_clasificacion`)
			LEFT JOIN `sys_documentos_identidad` ON (`sys_documentos_identidad`.`id_documento`=sys_personas.`id_documento`) 
			WHERE  1=1  ";
				$SQL.=$QUERY;
	 			//prospecto_comercial.estatus IN (6,4,5)
				
				$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
				 
			 
				 
				$rs=mysql_query($SQL);
				$result=array();
 
				while($row=mysql_fetch_assoc($rs)){	
					$encriptID=System::getInstance()->Encrypt($row['id_nit']); 
					$id_comercial=System::getInstance()->Encrypt($row['id_comercial']);													
																		  
					array_push($data['aaData'],
						array( 
							"tipo_documento"=>$row['descripcion']."",
							"id_nit"=>$row['id_nit']."",
							"nombre"=>$row['nombre']."",
							"apellido"=>$row['apellido']."",
							"fecha_nacimiento"=>$row['fecha_nacimiento']."",
							"direccion"=>$row['direccion']."",
							"telefono"=>$row['telefono']."",
							"email"=>$row['email']."",
							'codigo_asesor'=>$row['id_comercial'],
							"id_nit_en"=>$encriptID,
							"option2"=>'<a href="#" class="rs_add_link" id="'.$encriptID.'" asesor="'.$id_comercial.'"><img src="images/netvibes.png" width="27" height="28" /></a>',			
						)
					);
				 
				} 
			
		}	
		
		return $data;	
	}
	
}

?>
