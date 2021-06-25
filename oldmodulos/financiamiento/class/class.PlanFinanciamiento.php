<?php

/*
	Esta clase maneja los planes de financiamientos
*/
class PlanFinanciamiento{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
	private $plazos= array(48,36,24,12,6);
 
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	
	/*CREA UNA TABLA DE PRECIO*/
	public function createTablaPrecio(){
		$enganche=$_REQUEST['enganche'];
		$empresa_id=$_REQUEST['empresa']['id'];
	//	$tipo_moneda=$_REQUEST['empresa']['moneda'];
		$form_data=$_REQUEST['form_data'];
		
		$enganche_array=array();
		foreach($enganche  as $key =>$val){
			$enganche_concat.=$val.".";
			array_push($enganche_array,$val);
		}
		$enganche_concat=substr($enganche_concat,0,strlen($enganche_concat)-1);
		$empresa=json_decode(System::getInstance()->Decrypt($empresa_id));
		 
		$form_convert=array();
		foreach($form_data  as $key =>$val){
			$form_convert[$val['name']]=$val['value'];
		}	
		$tb_precio= new ObjectSQL();
		$tb_precio->CODIGO_TP=$form_convert['CODIGO_TP'];
		$tb_precio->MONEDA_TP=$form_convert['MONEDA_TP'];
		$tb_precio->PRECIO_TP=$form_convert['PRECIO_TP'];
		$tb_precio->CAPITAL_TP=$form_convert['CAPITAL_TP'];
		
		if ($tb_precio->MONEDA_TP=="LOCAL"){
			$tb_precio->POR_INTERES=$empresa->por_interes_local;
		}else if ($tb_precio->MONEDA_TP=="DOLARES"){
			$tb_precio->POR_INTERES=$empresa->por_interes_dolares;
		}
		$tb_precio->IMPUESTO_TP=($empresa->por_impuesto*$tb_precio->PRECIO_TP);
		$tb_precio->POR_IMPUESTO_TP=($empresa->por_impuesto*$tb_precio->PRECIO_TP);
		$tb_precio->CAPITAL_TP=$form_convert['CAPITAL_TP'];
		$tb_precio->ENGACHE=$enganche_concat;
		
		$SQL=$tb_precio->getSQL("insert","tabla_precios");
		mysql_query($SQL);
		//AGREGO LOS PLANES DE LA TABLA DE PRECIOS
		$this->addPlan($tb_precio,$enganche_array);	  
  
		return json_encode(array(
								"mensaje"=> "Registro agregado",
								"error"=>false )
								);	
	}

	/*CREA UNA TABLA DE PRECIO*/
	public function updateTablaPrecio(){
		$enganche=$_REQUEST['enganche'];
		$form_data=$_REQUEST['form_data'];
	 	/*FORMATEO EL FORMULARIO PARA VOLVERLO UN ARRAY*/
	 	$form_convert=array();
		foreach($form_data  as $key =>$val){
			$form_convert[$val['name']]=$val['value'];
		}	
		/*CARGO LA ULTIMA TABLA DE PRECIOS CONOCIDA*/
		$last_tb_precio=json_decode(System::getInstance()->Decrypt($form_convert['tb_precio_id']));
		$sp=explode(".",$last_tb_precio->ENGACHE);
		
		$enganche_array=array();
		$enganche_new_array=array();
		foreach($enganche  as $key =>$val){
			$enganche_concat.=$val.".";
			array_push($enganche_array,$val);
			/*DETERMINO SI HAY ENGANCHES NUEVOS 
			EN CASO DE QUE ENCUENTRE ENTONCES LO APARTO EN UN ARRAY*/
			$exist=array_search($val,$sp);
			if (trim($exist)==""){
				array_push($enganche_new_array,$val);
			}
		}
		 

		$enganche_concat=substr($enganche_concat,0,strlen($enganche_concat)-1);
 		  
		$tb_precio= new ObjectSQL();
		$tb_precio->ESTATUS=System::getInstance()->Decrypt($form_convert['estado']);
	//	$tb_precio->MONEDA_TP=$form_convert['MONEDA_TP'];
		$tb_precio->PRECIO_TP=$form_convert['PRECIO_TP'];
		$tb_precio->CAPITAL_TP=$form_convert['CAPITAL_TP'];
		
		$tb_precio->POR_INTERES=$form_convert['plan_por_interes'];
		
		$tb_precio->IMPUESTO_TP=($form_convert['IMPUESTO_TP']*$tb_precio->PRECIO_TP);
		$tb_precio->POR_IMPUESTO_TP=($form_convert['por_impuesto']*$tb_precio->PRECIO_TP);
		$tb_precio->CAPITAL_TP=$form_convert['CAPITAL_TP'];
		$tb_precio->ENGACHE=$enganche_concat;
		
		$SQL=$tb_precio->getSQL("update","tabla_precios"," where CODIGO_TP='". mysql_real_escape_string($last_tb_precio->CODIGO_TP)."'");
		mysql_query($SQL);
 		
		/*ASIGNO EL VALOR PARA PODER PASARLO COMO PARAMETROS A LAS FUNCIONES Y PODER FILTRAR*/
		$tb_precio->CODIGO_TP=$last_tb_precio->CODIGO_TP;
		/*SI HAY ENGANCHES NUEVOS ENTONCES PROCEDE A AGREGAR*/
		if (count($enganche_new_array)>0){
			//AGREGO LOS PLANES DE LA TABLA DE PRECIOS
			$this->addPlan($tb_precio,$enganche_new_array);	
		}
		$this->updatePlan($tb_precio,$enganche_array);	
 
		return json_encode(array(
								"mensaje"=> "Registro Actualizado",
								"error"=>false )
								);	
	}
	
	/*AGREGA PLANES A LA TABLA DE PRECIOS*/
	public function addPlan($tb_precio,$enganche_array){
		 foreach($enganche_array as $eng_key => $porciento_enganche){
			foreach($this->plazos as $k => $plazo){
				$plan=$this->getPlanFinanciamientoObject($tb_precio,$plazo,$porciento_enganche);
				$SQL=$plan->getSQL("insert","planes_financiamiento");
				mysql_query($SQL);	
			}
		 }
	}
	
	/*ACTUALIZA LOS PLANES A LA TABLA DE PRECIOS*/
	public function updatePlan($tb_precio,$enganche_array){
		 foreach($enganche_array as $eng_key => $porciento_enganche){
			foreach($this->plazos as $k => $plazo){
				$plan=$this->getPlanFinanciamientoObject($tb_precio,$plazo,$porciento_enganche);
				$SQL=$plan->getSQL("update","planes_financiamiento"," where 
							CODIGO_TP='". mysql_real_escape_string($tb_precio->CODIGO_TP) ."' AND 
							PLAZO_CUOTAS_PF='".mysql_real_escape_string($plan->PLAZO_CUOTAS_PF)."' AND
							POR_ENGANCHE='".mysql_real_escape_string($plan->POR_ENGANCHE)."'");
				mysql_query($SQL);	
			}
		 }
	}
	
	/*DEVUELVE EL OBJETO SQL CON LOS CALCULOS REALIZADOS*/
	private function getPlanFinanciamientoObject($tb_precio,$plazo,$porciento_enganche){  
		
		$plan= new ObjectSQL();
		//CODIGO DE LA TABLA DE PRECIO
		$plan->CODIGO_TP=$tb_precio->CODIGO_TP;
		//PLAZO DE LAS CUOTAS 48,36,24,12 o 6 meses para pagar
		$plan->PLAZO_CUOTAS_PF=$plazo; 
		//PORCIENTO DE ENGANCHE 10% 25% o el 50% que desea pagar para entrar en el plan
		$plan->POR_ENGANCHE=$porciento_enganche;
		///MONTO ENGANCHE = (PRECIO DE LA TABLA DE PRECIO ) X (porciento de enganche)/100
		$plan->MONTO_ENGANCHE_PF=($tb_precio->PRECIO_TP*$porciento_enganche)/100;
		//CAPITAL A FINANCIAR = (PRECIO DE LA TABLA DE PRECIO) - (EL MONTO DE ENGANCHE)
		$plan->CAPITAL_FINANCIAR=$tb_precio->PRECIO_TP-$plan->MONTO_ENGANCHE_PF;
 		 
		$plan->INTERES_MENSUAL=($plan->CAPITAL_FINANCIAR*($tb_precio->POR_INTERES/12)/100);
		$plan->CAPITAL_MENSUAL=0;
		if ($plan->PLAZO_CUOTAS_PF>0){
			$plan->CAPITAL_MENSUAL=($plan->CAPITAL_FINANCIAR/$plan->PLAZO_CUOTAS_PF);
		}
		$plan->VALOR_CUOTA=($plan->INTERES_MENSUAL+$plan->CAPITAL_MENSUAL);
		$plan->TOTAL_FINANCIAR=($plan->VALOR_CUOTA*$plan->PLAZO_CUOTAS_PF);
		$plan->TOTAL_INTERES=($plan->INTERES_MENSUAL*$plan->PLAZO_CUOTAS_PF);
		$plan->PORC_INTERES=$tb_precio->POR_INTERES;
		$plan->IMPUESTO_ENGANCHE="0";
		$plan->IMPUESTO_CUOTA="0";
		$plan->PORC_IMPUESTO="0";
 
		return $plan;	
	}
	
	/*Retorna el listado de la tabla de prcios */
	public function getListTablaPrecio(){
		$QUERY="";
		$HAVING="";

		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			  $_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
			  $QUERY=" AND (CODIGO_TP LIKE '%".$_REQUEST['sSearch']."%' or MONEDA_TP LIKE '%".$_REQUEST['sSearch']."%') ";
		  }
		}
 
		$SQL="SELECT count(*) as total from tabla_precios where ESTATUS=1  ".$QUERY;
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
			$SQL="SELECT * FROM `tabla_precios` where ESTATUS=1 ". $QUERY;		  
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
 
 			//echo $SQL;
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			
			while($row=mysql_fetch_assoc($rs)){	
				$encriptID=System::getInstance()->Encrypt(json_encode($row));
				
				$row['PRECIO_TP_F']=number_format($row['PRECIO_TP'],2);
				$row['CAPITAL_TP_F']=number_format($row['CAPITAL_TP'],2);
				
				$row['bt_view_plan']='<a href="#" class="bt_view_plan" id="'.$encriptID.'"><img src="images/view.png"  /></a>';
				$row['bt_editar']='<a href="#" class="edit_plan" id="'.$encriptID.'"><img src="images/subtract_from_cart.png"  /></a>';
				array_push($data['aaData'],$row);
			}
			
			return json_encode($data);	
	}
	
	/*Retorna el listado de planes de financiamientos */
	public function getListPlanesFinanciamiento(){
 
		$plan=json_decode(System::getInstance()->Decrypt($this->data['tb_precio']));
  
		$SQL="SELECT * FROM `planes_financiamiento` where  CODIGO_TP='".$plan->CODIGO_TP."' AND ESTATUS=1  order by POR_ENGANCHE,PLAZO_CUOTAS_PF desc ";
  
		$rs=mysql_query($SQL);
		$result=array();
		$data=array();
		while($row=mysql_fetch_assoc($rs)){	
			$encriptID=System::getInstance()->Encrypt(json_encode($row));
			$row['bt_editar']='<a href="#" class="edit_plan" id="'.$encriptID.'"><img src="images/view.png"  /></a>';
			array_push($data,$row);
		}
		
		return $data;	
	}
	
	/*Retorna el listado de planes de financiamientos agrupados */
	public function getListPlanesFinanciamientoGroup(){
   
		$SQL="SELECT 
				tabla_precios.MONEDA_TP as moneda,
				planes_financiamiento.`PLAZO_CUOTAS_PF` as plazo,
				planes_financiamiento.`POR_ENGANCHE` as enganche,
				tabla_precios.CODIGO_TP as plan
			FROM `planes_financiamiento`
			INNER JOIN tabla_precios ON (tabla_precios.`CODIGO_TP`=planes_financiamiento.CODIGO_TP)
			  WHERE planes_financiamiento.ESTATUS=1 
			GROUP BY 
				tabla_precios.MONEDA_TP,
				planes_financiamiento.`PLAZO_CUOTAS_PF`,
				planes_financiamiento.`POR_ENGANCHE`
			ORDER BY tabla_precios.MONEDA_TP,
				planes_financiamiento.`PLAZO_CUOTAS_PF`,
				planes_financiamiento.`POR_ENGANCHE` ";
  
		$rs=mysql_query($SQL);
		$result=array();
		$data=array();
		while($row=mysql_fetch_assoc($rs)){	
			$encriptID=System::getInstance()->Encrypt(json_encode($row));
			$row['bt_editar']='<a href="#" class="edit_plan" id="'.$encriptID.'"><img src="images/view.png"  /></a>';
			array_push($data,$row);
		}
		
		return $data;	
	}	
	
	/*Retorna el listado de planes de financiamientos en Pesos*/
	public function getPlanFinanByProductMonedaLocalCustom($producto,$filter=array()){
   
   		$SSQL=""; 
		$SQL_IN="";
		if (isset($filter['situacion'])){
			$field="";
			$field_interes="";
			if ($filter['MONEDA']=="LOCAL"){
				$field_interes="interes_local";
				/*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){
					/*SI EL CAMPO OSARIO ESTA VACIO NO ES UN OSARIO*/
					if (trim($producto->osario)==""){
						$field="precio_venta_local_pre";
					}else{
						$field="precio_osario_local_pre";
					}
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){ 
					if (trim($producto->osario)==""){
						$field="precio_venta_local_nec";
					}else{
						$field="precio_osario_local_nec";
					}					
				}			
			}
			if ($filter['MONEDA']=="DOLARES"){
				$field_interes="interes_dolares";
				/*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){ 
					if (trim($producto->osario)==""){
						$field="precio_venta_dolares_pre";
					}else{
						$field="precio_osario_dolares_pre";
					}					
					
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){
					if (trim($producto->osario)==""){
						$field="precio_venta_dolares_nec";
					}else{
						$field="precio_osario_dolares_nec";
					}	 
				}			
			}			 
			
		}  
		$SQL="SELECT * FROM `tabla_precios` WHERE `CODIGO_TP` IN (SELECT  ".$field." as tb_precio  FROM `jardines_activos` WHERE id_jardin='".$producto->id_jardin."' AND id_fases='".$producto->id_fases."') ";

		$rs=mysql_query($SQL);
		$result=array();
		$data=array();
		while($row=@mysql_fetch_object($rs)){ 
			$PIC=$this->getPlazoInteresC($filter['situacion'],$producto->EM_ID,$filter['plazo']); 
			$row->POR_INTERES=$PIC[$field_interes]; 	
			$inf=$this->getPlanFinanciamientoObject($row,$filter['plazo'],$filter['enganche']); 
			array_push($data,$inf->toArray());
 		}
	 	
		
		 
		return $data;	
	}
	 /*Retorna el listado de planes de financiamientos en Pesos*/
	public function getPlanFinanByProductMonedaLocal($producto,$filter=array()){
   
   		$SSQL="";
		if (count($filter)>=3){
			$SSQL=" AND `PLAZO_CUOTAS_PF`='".$filter['plazo']."' AND  `POR_ENGANCHE`='".$filter['enganche']."' ";
		}
		$SQL_IN="";
		if (isset($filter['situacion'])){
			$field="";
			if ($filter['MONEDA']=="LOCAL"){
				/*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){
					$field="precio_venta_local_pre";
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){
					$field="precio_venta_local_nec";
				}			
			}
			if ($filter['MONEDA']=="DOLARES"){
				/*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){
					$field="precio_venta_dolares_pre";
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){
					$field="precio_venta_dolares_nec";
				}			
			}			
					
			
			$SQL_IN="SELECT  
						".$field."  
					FROM `jardines_activos`  
					WHERE id_jardin='".$producto->id_jardin."' 
				AND id_fases='".$producto->id_fases."')";
			
		}
		
		$SQL="SELECT * FROM `planes_financiamiento` WHERE  
			CODIGO_TP IN (SELECT `CODIGO_TP` FROM `tabla_precios`  WHERE `CODIGO_TP` IN (".$SQL_IN.") AND ESTATUS=1   ";
 
 		 $SQL.=$SSQL; 
		 $SQL.="  order by POR_ENGANCHE,PLAZO_CUOTAS_PF desc "; 
	
		  
		$rs=mysql_query($SQL);
		$result=array();
		$data=array();
		while($row=mysql_fetch_assoc($rs)){	
			//$encriptID=System::getInstance()->Encrypt(json_encode($row));
			//$row['bt_editar']='<a href="#" class="edit_plan" id="'.$encriptID.'"></a>';
			array_push($data,$row);
		}
		return $data;	
	}
	
	
	/*Retorna el listado de planes de financiamientos en dolares */
	public function getPlanFinanByProductMonedaDolar($producto,$filter=array()){
		
   		$SSQL="";
		if (count($filter)>=3){
			$SSQL=" AND `PLAZO_CUOTAS_PF`='".$filter['plazo']."' AND  `POR_ENGANCHE`='".$filter['enganche']."' ";
		}
		

		$SQL_IN="";
		if (isset($filter['situacion'])){
			$field=""; 
			if ($filter['MONEDA']=="LOCAL"){
				/*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){
					$field="precio_venta_local_pre";
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){
					$field="precio_venta_local_nec";
				}			
			}
			if ($filter['MONEDA']=="DOLARES"){
				/*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){
					$field="precio_venta_dolares_pre";
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){
					$field="precio_venta_dolares_nec";
				}			
			}			
			
			
			$SQL_IN="SELECT  
						".$field."  
					FROM `jardines_activos`  
					WHERE id_jardin='".$producto->id_jardin."' 
				AND id_fases='".$producto->id_fases."')";
			
		}

		$SQL="SELECT * FROM `planes_financiamiento` WHERE  CODIGO_TP IN (SELECT `CODIGO_TP` FROM `tabla_precios`
		 WHERE `CODIGO_TP` IN (".$SQL_IN.") AND ESTATUS=1   ";
		 
		 $SQL.=$SSQL; 
		 $SQL.="  order by POR_ENGANCHE,PLAZO_CUOTAS_PF desc ";
		   
		$rs=mysql_query($SQL);
		$result=array();
		$data=array();
		while($row=mysql_fetch_assoc($rs)){	 
			//$encriptID=System::getInstance()->Encrypt(json_encode($row));
			//$row['bt_editar']='<a href="#" class="edit_plan" id="'.$encriptID.'"></a>';
			array_push($data,$row);
		}
		
		return $data;	
	}

	/*Retorna el listado de planes de financiamientos en Pesos*/
	public function getPlanFinanByServicioMonedaLocal($producto,$filter=array()){
   
   		$SSQL="";
		
		if (count($filter)>=3){
			$SSQL=" AND `PLAZO_CUOTAS_PF`='".$filter['plazo']."' AND  `POR_ENGANCHE`='".$filter['enganche']."' ";
		}

		$SQL_IN="";
		if (isset($filter['situacion'])){
			$field="";
			/*SI ES PRE-NECESIDAD */
			if ($filter['situacion']=="PRE"){
				$field="serv_precio_venta_local_pre";
			}
			/*SI ES NECESIDAD */
			if ($filter['situacion']=="NSD"){
				$field="serv_precio_venta_local_nec";
			}			
			
			$SQL_IN="SELECT ".$field." FROM `servicios`  WHERE `serv_codigo`='".$producto->serv_codigo."'";
			
		}


		$SQL="SELECT * FROM `planes_financiamiento` WHERE  CODIGO_TP IN (".$SQL_IN.") AND ESTATUS=1   ";
 
 		$SQL.=$SSQL;
		 
		$SQL.="  order by POR_ENGANCHE,PLAZO_CUOTAS_PF desc ";
		 
		$rs=mysql_query($SQL);
		$result=array();
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			//$encriptID=System::getInstance()->Encrypt(json_encode($row));
			//$row['bt_editar']='<a href="#" class="edit_plan" id="'.$encriptID.'"></a>';
			array_push($data,$row);
		}
		return $data;	
	} 
	
		
	/*Retorna el listado de planes de financiamientos en Dolares*/
	public function getPlanFinanByServicioMonedaLocalCustom($producto,$filter=array()){
   
   		$SSQL="";
		 
		if (count($filter)>=3){
			$SSQL=" AND `PLAZO_CUOTAS_PF`='".$filter['plazo']."' AND  `POR_ENGANCHE`='".$filter['enganche']."' ";
		}

		$SQL_IN="";
		if (isset($filter['situacion'])){
			$field="";
			$field_interes="";
			if ($filter['MONEDA']=="LOCAL"){
				$field_interes="interes_local";
				/*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){
					$field="serv_precio_venta_local_pre";
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){
					$field="serv_precio_venta_local_nec";
				}			
			}
			if ($filter['MONEDA']=="DOLARES"){
				$field_interes="interes_dolares";
				 /*SI ES PRE-NECESIDAD */
				if ($filter['situacion']=="PRE"){
					$field="serv_precio_venta_dolares_pre";
				}
				/*SI ES NECESIDAD */
				if ($filter['situacion']=="NSD"){
					$field="serv_precio_venta_dolares_nec";
				}	
			}
			///$SQL_IN="SELECT ".$field." FROM `servicios`  WHERE `serv_codigo`='".$producto->serv_codigo."'";
			
		}
		  
		$SQL="SELECT * FROM `tabla_precios` WHERE `CODIGO_TP` IN (SELECT ".$field." FROM `servicios`  WHERE `serv_codigo`='".$producto->serv_codigo."') ";

		$rs=mysql_query($SQL);
		$result=array();
		$data=array();

		while($row=@mysql_fetch_object($rs)){	 
			//$encriptID=System::getInstance()->Encrypt(json_encode($row)); 
			$PIC=$this->getPlazoInteresC($filter['situacion'],$producto->EM_ID,$filter['plazo']); 
			$row->POR_INTERES=$PIC[$field_interes]; 
			
			$inf=$this->getPlanFinanciamientoObject($row,$filter['plazo'],$filter['enganche']); 
			array_push($data,$inf->toArray());
 		}
		return $data;	
	} 
	 
	/*Retorna el listado de planes de financiamientos en Pesos*/
	public function getPlanFinanByServicioMonedaDolar($producto,$filter=array()){
  	
   		$SSQL="";
		if (count($filter)>=3){
			$SSQL=" AND `PLAZO_CUOTAS_PF`='".$filter['plazo']."' AND  `POR_ENGANCHE`='".$filter['enganche']."' ";
		}
		
		$SQL_IN="";
		if (isset($filter['situacion'])){
			$field="";
			/*SI ES PRE-NECESIDAD */
			if ($filter['situacion']=="PRE"){
				$field="serv_precio_venta_dolares_pre";
			}
			/*SI ES NECESIDAD */
			if ($filter['situacion']=="NSD"){
				$field="serv_precio_venta_dolares_nec";
			}			
			
			$SQL_IN="SELECT ".$field." FROM `servicios`  WHERE `serv_codigo`='".$producto->serv_codigo."'";
			
		}
		
		$SQL="SELECT * FROM `planes_financiamiento` WHERE  CODIGO_TP IN (".$SQL_IN.") AND ESTATUS=1   ";
 
 		 $SQL.=$SSQL;
		 
		 $SQL.="  order by POR_ENGANCHE,PLAZO_CUOTAS_PF desc "; 
		 
	  
		$rs=mysql_query($SQL);
		$result=array();
		$data=array();
		while($row=mysql_fetch_assoc($rs)){	
			//$encriptID=System::getInstance()->Encrypt(json_encode($row));
			//$row['bt_editar']='<a href="#" class="edit_plan" id="'.$encriptID.'"></a>';
			array_push($data,$row);
		}
		return $data;	
	}
	/*Desabilita un plan de financiamiento*/
	public function removePlan(){
		$plan=json_decode(System::getInstance()->Decrypt($_REQUEST['plan_id']));
		$obj = new ObjectSQL();
		$obj->ESTATUS=2;
		$SQL=$obj->getSQL("update","planes_financiamiento"," where 
							CODIGO_TP='". mysql_real_escape_string($plan->CODIGO_TP) ."' AND 
							PLAZO_CUOTAS_PF='".mysql_real_escape_string($plan->PLAZO_CUOTAS_PF)."' AND
							POR_ENGANCHE='".mysql_real_escape_string($plan->POR_ENGANCHE)."'");
		mysql_query($SQL);
		return json_encode(array(
								"mensaje"=> "Registro desabilitado",
								"error"=>false )
								);
	}
	
	public function getPlazoInteresComision(){ 
		$SQL="SELECT * FROM `plazo_interes_comision`
				INNER JOIN `empresa` ON (empresa.EM_ID=plazo_interes_comision.EM_ID)
				 ORDER BY plazo_interes_comision.EM_ID "; 
 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=@mysql_fetch_object($rs)){	  
			array_push($data,$row);
 		}	 
		return $data;	
	}
	
	public function addPagoInteresComision($data){
		$empresa=json_decode(System::getInstance()->Decrypt($data['EM_ID']));
		$inf=array("mensaje"=>"","valid"=>false);
		
		if (!isset($empresa->EM_ID)){  
			$inf['valid']=false;
			$inf['mensaje']='Error, debe de seleccionar una empresa valida';
		}else{ 		
			$obj= new ObjectSQL();
			$obj->necesidad_pre=$data['necesidad_pre'];
			$obj->EM_ID=$empresa->EM_ID;
			$obj->plazo_desde=$data['plazo_desde'];
			$obj->plazo_hasta=$data['plazo_hasta'];
			$obj->interes_local=$data['interes_local'];
			$obj->interes_dolares=$data['interes_dolares'];
			$obj->Comision=$data['Comision'];	
			$obj->setTable("plazo_interes_comision");
			if ($this->getIfExistPlazoInteresC($obj->necesidad_pre,
												$obj->EM_ID,
												$obj->plazo_desde,
												$obj->plazo_hasta)==0){
											
				$SQL=$obj->toSQL("insert"); 
				mysql_query($SQL);	
				$inf['valid']=true;
				$inf['mensaje']='Registro ingresado';				
				
			}else{
				$inf['valid']=false;
				$inf['mensaje']='Error, registro duplicado ';
			}		
										
		}
		
		return $inf;
	}
	
	public function updatePagoInteresComision($data){
		$empresa=json_decode(System::getInstance()->Decrypt($data['EM_ID']));
		$inf=array("mensaje"=>"","valid"=>false);
		
		$pic=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
		
		if (!isset($pic->necesidad_pre)){
			$inf['valid']=false;
			$inf['mensaje']='Error, debe de seleccionar una empresa valida';
			return $inf;
		}
		 
		if (!isset($empresa->EM_ID)){  
			$inf['valid']=false;
			$inf['mensaje']='Error, debe de seleccionar una empresa valida';
		}else{ 		
			$obj= new ObjectSQL();
			$obj->necesidad_pre=$data['necesidad_pre'];
			$obj->EM_ID=$empresa->EM_ID;
			$obj->plazo_desde=$data['plazo_desde'];
			$obj->plazo_hasta=$data['plazo_hasta'];
			$obj->interes_local=$data['interes_local'];
			$obj->interes_dolares=$data['interes_dolares'];
			$obj->Comision=$data['Comision'];	
			$obj->setTable("plazo_interes_comision");
			if ($this->getIfExistPlazoInteresC($obj->necesidad_pre,
												$obj->EM_ID,
												$obj->plazo_desde,
												$obj->plazo_hasta)>0){
													
				$SQL=$obj->toSQL("update"," where EM_ID='".$pic->EM_ID."'  
														and necesidad_pre='".$pic->necesidad_pre."'  
														and plazo_desde='".$pic->plazo_desde."'  
														and plazo_hasta='".$pic->plazo_hasta."' ");
		 
				mysql_query($SQL);											
				$inf['valid']=true;
				$inf['mensaje']='Registro Actualizado';				
				
			}else{
				$inf['valid']=false;
				$inf['mensaje']='Error, Interes y comision no existe!';
			}		
										
		}
		
		return $inf;
	}	
 
 	public function getIfExistPlazoInteresC($necesidad_pre,$EM_ID,$plazo_desde,$plazo_hasta){
  		$SQL="SELECT COUNT(*) AS tt FROM plazo_interes_comision WHERE necesidad_pre='".$necesidad_pre."' AND EM_ID='".$EM_ID."' AND plazo_desde='".$plazo_desde."' AND plazo_hasta='".$plazo_hasta."'";
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs); 
		return $row['tt'];	
	}	
	
	public function getPlazoInteresC($necesidad_pre,$EM_ID,$plazo){
  		$SQL="SELECT * FROM plazo_interes_comision WHERE necesidad_pre='".$necesidad_pre."' AND EM_ID='".$EM_ID."' AND '".mysql_real_escape_string($plazo)."' BETWEEN plazo_desde AND plazo_hasta ";
		$rs=mysql_query($SQL); 
		$dt=array();
		while($row=mysql_fetch_assoc($rs)){
			$dt=$row;	
		}
		return $dt;	
	}		
	
}

?>
