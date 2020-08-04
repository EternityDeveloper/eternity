<?php

class Contratos{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
	private $_EMP_ID="";
	private $_serie_contrato="";
	private $_no_contrato="";
	private $_prosp_parentesco="";
	private $_prospecto=array("valid"=>false,"data"=>array());
	private $_contratante=array("valid"=>false,"data"=>array());
	private $_beneficiario=array("valid"=>false,"data"=>array());
	private $_representante=array("valid"=>false,"data"=>array());
	private $_producto=array("valid"=>false,"data"=>array());
	private $_asesor=array("asesor"=>"", "director"=>"", "gerente"=>"");
	private $_product_list=array();
	private $_descuento_list=array();
	private $_beneficiario_list=array();
	private $_representante_list=array();
	private $_fecha="";
	  
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	
	public function session_restart(){
		$_SESSION['CONTRATO_DATA']=array("product_list"=>array(),"servicio_list"=>array(),"financiamiento"=>array(),"document"=>array());
	}
        
	public function setFecha($fecha){
		$this->_fecha=$fecha;	
	}
	public function getListOfertas(){
        
		if (!validateField($_REQUEST,"iDisplayStart")){
			$_REQUEST['iDisplayStart']=0;
			$_REQUEST['iDisplayLength']=10;
		}

		// && isset($_REQUEST['iDisplayLength']))
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
			$QUERY="  AND (contratos.id_nit_cliente LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,' ',contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or  contratos.no_contrato LIKE '%".$_REQUEST['sSearch']."%' )   ";
		  }
		}

		$SQL="SELECT count(*) as total
				FROM `contratos`
				INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
				INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				WHERE contratos.`estatus` in ('13','32') ";
			$SQL.=$QUERY;
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$total_row=$row['total'];
		
			$SQL="SELECT contratos.*,
				CONCAT(`sys_personas`.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',
				`sys_personas`.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS nombre_cliente,
				empresa.`EM_NOMBRE`,
				
				contratos.no_productos AS producto_total,
				sys_status.`descripcion` AS estatus,
				(SELECT 
				CONCAT(`asesor`.`primer_nombre`,' ',asesor.`segundo_nombre`,
				`asesor`.`primer_apellido`,' ',asesor.`segundo_apellido`) AS nombre_asesor
				 FROM `view_estructura_comercial`
				INNER JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=view_estructura_comercial.`id_nit`)
				  WHERE view_estructura_comercial.id_comercial=contratos.codigo_asesor AND view_estructura_comercial.tabla='Asesor de Familia') AS nombre_asesor,
				CONCAT( `serie_contrato`,' ',`no_contrato`) AS contrato_numero
				
				FROM `contratos`
				INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
				INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				 
				WHERE contratos.`estatus` in ('13','32')
				 ";
		 	$SQL.=$QUERY;
			 
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			   
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
				$id_nit=System::getInstance()->Encrypt($data_p['serie_contrato']."_".$data_p['no_contrato']);
								  
			//	$row['nombre_asesor']=$data_p['primer_nombre']." ".$data_p['primer_apellido'];
				$row['contrato']=$data_p['serie_contrato']." ".$data_p['no_contrato'];
				$row['bt_reserva']='';
				$row['bt_editar']='<a href="./?mod_contratos/listar&edit_contrato=1&id='.$encriptID.'"  id="'.$encriptID.'" class="edit_solicitud"><img src="images/subtract_from_cart.png"  /></a>';
				$row['bt_editar_user']='<a href="./?mod_contratos/list_contratos&view_contrato=1&id='.$encriptID.'" id="'.$encriptID.'" class="edit_solicitud"><img src="images/edit_user.png"  /></a>'; 
				array_push($data['aaData'],$row);
			}
			
			return $data;		
	}
	
	public function getListContratos(){
		 
		if (!validateField($_REQUEST,"iDisplayStart")){
			$_REQUEST['iDisplayStart']=0;
			$_REQUEST['iDisplayLength']=10;
		}
		
		// && isset($_REQUEST['iDisplayLength']))
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
			$QUERY="  AND (contratos.id_nit_cliente LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,' ',contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or  contratos.no_contrato LIKE '%".$_REQUEST['sSearch']."%' )   ";
		  }
		}

			$SQL="SELECT count(*) as total
				FROM `contratos`
				INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
				INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				INNER JOIN `sys_asesor` AS `asesores_g_d_gg_view` ON (`asesores_g_d_gg_view`.`codigo_asesor`=contratos.`codigo_asesor`)
				INNER JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=asesores_g_d_gg_view.`id_nit`)
				WHERE contratos.`estatus`='1' ";
			$SQL.=$QUERY;
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$total_row=$row['total'];
		
			$SQL="SELECT contratos.*,
					CONCAT(`sys_personas`.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ', 
					`sys_personas`.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS nombre_cliente,
					empresa.`EM_NOMBRE`, 
					`no_productos` AS producto_total,
					sys_status.`descripcion` AS estatus,
					CONCAT(`asesor`.`primer_nombre`,' ',asesor.`segundo_nombre`,
					`asesor`.`primer_apellido`,' ',asesor.`segundo_apellido`) AS nombre_asesor,
					CONCAT( `serie_contrato`,' ',`no_contrato`) AS contrato_numero,
					formas_pago.descripcion_pago AS forma_de_pago
				
				FROM `contratos`
				INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
				LEFT JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
				INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				LEFT JOIN `sys_asesor` AS `asesores_g_d_gg_view` ON (`asesores_g_d_gg_view`.`codigo_asesor`=contratos.`codigo_asesor`)
				LEFT JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=asesores_g_d_gg_view.`id_nit`)
				LEFT JOIN `formas_pago` ON (formas_pago.`forpago`=contratos.forpago)
				WHERE 1=1  ";
		 	$SQL.=$QUERY;
			 
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			   
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
				$id_nit=System::getInstance()->Encrypt($data_p['serie_contrato']."_".$data_p['no_contrato']);
								  
			//	$row['nombre_asesor']=$data_p['primer_nombre']." ".$data_p['primer_apellido'];
				$row['contrato']=$data_p['serie_contrato']." ".$data_p['no_contrato'];
				$row['bt_reserva']='';
				$row['bt_editar']='<a href="./?mod_contratos/list_contratos&view_contrato=1&id='.$encriptID.'"  id="'.$encriptID.'" class="edit_solicitud"><img src="images/subtract_from_cart.png"  /></a>';
				$row['bt_editar_user']='<a href="./?mod_contratos/list_contratos&view_contrato=1&id='.$encriptID.'" id="'.$encriptID.'" class="edit_solicitud"><img src="images/edit_user.png"  /></a>'; 
				array_push($data['aaData'],$row);
			}
			
			return $data;		
	}	

	public function printr(){
		print_r($_SESSION['CONTRATO_DATA']);	
	}
	public function addProspecto($prospecto,$documento){
		$_SESSION['CONTRATO_DATA']['prospecto']=array(
														"idnit"=>$prospecto,
														"id_documento"=>$documento
													);		 																	
	}

	public function getTasa(){
		$SQL="SELECT * FROM `tasa_cambio` "; 
		$rs=mysql_query($SQL);
		$tasa=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($tasa,$row);
		} 
		return $tasa; 
	} 
	public function getListSelectContrato($text){
	 
		$SQL="SELECT 
			serie_contrato,
			no_contrato,
			CONCAT('(',serie_contrato,' ',no_contrato,') ',ofi.primer_nombre,' ',ofi.segundo_nombre,' ',ofi.primer_apellido,' ',
						ofi.segundo_apellido) AS NOMBRE_CLIENTE
		 FROM `contratos`
		INNER JOIN `sys_personas` AS ofi ON (`ofi`.id_nit=contratos.id_nit_cliente)
		WHERE contratos.estatus IN (1) and (concat(serie_contrato,' ',no_contrato) like '%".$text."%' or concat(serie_contrato,no_contrato) like '%".$text."%')
		LIMIT 20";
		$rs=mysql_query($SQL);
		$result=array("results"=>array());   
		while($row=mysql_fetch_assoc($rs)){	 
			$row['nombre_completo']=$row['NOMBRE_CLIENTE'];  
			$eID=System::getInstance()->Encrypt(json_encode($row));
			$data=array("id"=>$eID,"text"=>utf8_encode($row['NOMBRE_CLIENTE']));
			array_push($result['results'],$data);
		}
		return $result; 
	}		
	public function getParentesco($idnit,$idnit_parentesco){
		$SQL="SELECT sys_parentesco.id_parentesco,tipos_parentescos.parentesco FROM `sys_parentesco`
			INNER JOIN `tipos_parentescos` ON (`tipos_parentescos`.`id_parentesco`=sys_parentesco.id_parentesco)
 WHERE sys_parentesco.`id_nit`='".$idnit."' AND sys_parentesco.`id_nit_parentesco`='".$idnit_parentesco."' ";
 
		$rs=mysql_query($SQL);
		$parent=array();
		while($row=mysql_fetch_assoc($rs)){
			$row['id_parentesco']=System::getInstance()->Encrypt($row['id_parentesco']);
			$parent=$row; 
		}
		
		return $parent;
		
	}
	public function addCobroAddress($data){ 
		$rt=array("error"=>true,"mensaje"=>"No se pudo procesar la informacion");
		$serie_contrato=System::getInstance()->Decrypt($data['serie_contrato']);
		$no_contrato=System::getInstance()->Decrypt($data['no_contrato']);		
		$cc=$this->getInfoContrato($serie_contrato,$no_contrato);
		 
 		/*VERIFICO SI EXISTE EL ID_NIT DE LA SOLICITUD O CONTRATO
		CON ESO DETERMINO SI EL CONTRATO/SOLICITUD EXISTE*/ 
 		if (isset($cc->id_nit_cliente)){ 
			$obj= new ObjectSQL();
			$obj->id_nit=$cc->id_nit_cliente;
			$obj->idciudad=System::getInstance()->Decrypt($data['cuidad_id']);
			$obj->idprovincia=System::getInstance()->Decrypt($data['provincia_id']);
			$obj->idmunicipio=System::getInstance()->Decrypt($data['municipio_id']);
			$obj->idsector=System::getInstance()->Decrypt($data['sector_id']);
			$obj->tipo_direccion=7;//TIPO DIRECCION cobro
			$obj->status="1";
			$obj->numero=$data['direccion_numero'];
			$obj->manzana=$data['direccion_manzana'];
			$obj->residencia_colonia_condominio=$data['direccion_recidencia'];
			$obj->referencia=$data['direccion_referencia'];
			$obj->observaciones=$data['direccion_observacion'];
			$obj->avenida=$data['direccion_avenida'];
			$obj->calle=$data['direccion_calle'];
			$obj->zona=$data['direccion_zona'];
			$obj->departamento=$data['direccion_departamento'];
			$obj->serie_contrato=strtoupper($serie_contrato);
			$obj->no_contrato=$no_contrato; 
			 
			$SQL="SELECT count(*)as total FROM 
					`sys_direcciones` WHERE `id_nit`='".$cc->id_nit_cliente."' 
					AND `serie_contrato`='".$serie_contrato."' AND `no_contrato`='".$no_contrato."'";
			  
			$rs=mysql_query($SQL);
			$parent=array();
			$row=mysql_fetch_assoc($rs);
			if ($row['total']>0){
				unset($obj->no_contrato);
				unset($obj->serie_contrato);
				unset($obj->id_nit);
				$SQL=$obj->getSQL("update","sys_direcciones"," where `id_nit`='".$cc->id_nit_cliente."' 
					AND `serie_contrato`='".$serie_contrato."' AND `no_contrato`='".$no_contrato."'");
					
				$rt['mensaje']="Direccion actualizada";
			}else{
				$rt['mensaje']="Direccion agregada";
				$SQL=$obj->getSQL("insert","sys_direcciones");
			}  
			$rt['error']=false; 
			/*DEVUELVO LA DIRECCION*/
			$rt['address']=$this->getFomateAddress($obj->idsector).", ".$obj->avenida
						  .", ".$obj->calle.", ".$obj->zona.", ".$obj->departamento;
						  
			$rt['address_id']=System::getInstance()->Encrypt(mysql_insert_id());
			
		  	mysql_query($SQL);	
		}
		return $rt;
	}	
	
	/*
		Proceso que desiste un contrato
	*/
	public function doPosibleDesistir($serie_contrato,$no_contrato){
		$rt=array("error"=>true,"mensaje"=>"No se pudo procesar la informacion"); 	
		$cc=$this->getInfoContrato($serie_contrato,$no_contrato); 
 		/*VERIFICO SI EXISTE EL ID_NIT DE LA SOLICITUD O CONTRATO
		CON ESO DETERMINO SI EL CONTRATO/SOLICITUD EXISTE*/ 
 		if (isset($cc->id_nit_cliente)){ 
			$obj= new ObjectSQL();
	 		$obj->estatus=24;
			$obj->setTable("contratos");
			$SQL=$obj->toSQL("update"," WHERE serie_contrato='".$serie_contrato."' 
									AND no_contrato='".$no_contrato."' and estatus=1 ");
			
		    mysql_query($SQL);	

 			$obj= new ObjectSQL();	
			$obj->tipo="D";//TIPO DESISTIDOS
			$obj->idacta="DES-".date('m')."-".date('Y');
			$obj->id_status=24;
			$obj->motivo='DESESTIMIENTO';			
			$obj->no_contrato=$no_contrato;
			$obj->serie_contrato=$serie_contrato;	
			$obj->comentarios=mysql_real_escape_string($comentarios);		
			$obj->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->creado_por =UserAccess::getInstance()->getIDNIT();
			$obj->setTable("actas_desistidos_anulados");			
 			mysql_query($obj->toSQL("insert")); 
			
			STCSession::GI()->setSubmit("anular_form",false);
						
			$rt['error']=false;
			$rt['mensaje']="Proceso realizado";
		}
		return $rt;	
	}
	/*
		proceso que deja un comentario a un cliente
	*/
	public function doDejarComentario($id_nit_cliente,$contrato,$comentarios=""){
		$rt=array("error"=>true,"mensaje"=>"No se pudo procesar la informacion"); 	

		SystemHtml::getInstance()->includeClass("client","PersonalData"); 
		$person= new PersonalData($this->db_link,$_REQUEST); 
 		if (!$person->existClient($id_nit_cliente)){ 
			$rt=array("error"=>true,
							"mensaje"=>"No se puede realizar esta 
							operacion el contrato ya ha sido cambiado de estatus!"); 
		}else{
		 	 
			/*INSERTO LA LABOR DE COBRO*/
			$obj= new ObjectSQL(); 
			$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->EM_ID=$contrato->EM_ID;
			$obj->id_nit_cliente=$id_nit_cliente;
			$obj->no_contrato=$contrato->no_contrato;
			$obj->serie_contrato=$contrato->serie_contrato; 	
			$obj->observaciones="";	
			$obj->comentario_cliente=$comentarios;
			$obj->idaccion='CMRIO'; 	
			$obj->estatus=18; 
			$obj->oficial_cobro=UserAccess::getInstance()->getIDNIT(); 
			$obj->setTable('labor_cobro');
			$SQL=$obj->toSQL("insert");  
		 	mysql_query($SQL);	
	  
			$rt['error']=false;
			$rt['mensaje']="Comentario procesado";
		}
		return $rt;	
	}	

	/*
		Proceso que pone un contrato en posible a anular
	*/
	public function doPosibleAnular($serie_contrato,$no_contrato,$motivo,$comentarios=""){
		$rt=array("error"=>true,"mensaje"=>"No se pudo procesar la informacion"); 	
		$cc=$this->getInfoContrato($serie_contrato,$no_contrato); 
 		/*VERIFICO SI EXISTE EL ID_NIT DE LA SOLICITUD O CONTRATO
		CON ESO DETERMINO SI EL CONTRATO/SOLICITUD EXISTE*/ 
 
 		if (isset($cc->id_nit_cliente)){
			if ($cc->estatus!="1"){
				$rt=array("error"=>false,
							"mensaje"=>"No se puede realizar esta operacion el contrato ya ha sido cambiado de estatus!"); 	
				return $rt;
			} 
			$obj= new ObjectSQL();
	 		$obj->estatus=28;
			$obj->setTable("contratos");
			$SQL=$obj->toSQL("update"," WHERE serie_contrato='".$serie_contrato."' 
									AND no_contrato='".$no_contrato."' and estatus=1 ");
			

		    mysql_query($SQL); 
 			$obj= new ObjectSQL();	
			$obj->tipo="A";//TIPO ANULACION
			$obj->idacta=$obj->tipo."NU-".date('m')."-".date('Y');
			$obj->id_status=28;
			$obj->motivo=$motivo;			
			$obj->no_contrato=$no_contrato;
			$obj->serie_contrato=$serie_contrato;	
			$obj->comentarios=mysql_real_escape_string($comentarios);		
			$obj->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->creado_por =UserAccess::getInstance()->getIDNIT();
			$obj->setTable("actas_desistidos_anulados");			
 			mysql_query($obj->toSQL("insert"));  
			STCSession::GI()->setSubmit("anular_form",false);
			$rt['error']=false;
			$rt['mensaje']="Proceso realizado";
		}
		return $rt;	
	}	
	/*
		AnularOrDesistirProductos
	*/
	public function AnularOrDesistirProductos($serie_contrato,$no_contrato,$estatus){	
		SystemHtml::getInstance()->includeClass("inventario","Inventario"); 		
		SystemHtml::getInstance()->includeClass("caja","Caja"); 		
		$inv= new Inventario($this->db_link); 						
						  	
		$product=$this->getDetalleProductsFromContrato($serie_contrato,$no_contrato);
		$cn=$this->getDetalleGeneralFromContrato($serie_contrato,$no_contrato);
		foreach($product as $key=>$row){
			$ob=new ObjectSQL();
			$ob->id_estatus=$estatus;
			$ob->setTable("producto_contrato");
			$SQL=$ob->toSQL("update"," where serie_contrato='".$serie_contrato."' and 
										no_contrato='".$no_contrato."' and id_estatus=1 ");	
			mysql_query($SQL);	
			$row['id_nit_cliente']=$cn['id_nit_cliente'];
			$row['_estatus']=$estatus;
			$inv->liberar_parecela_contrato($row);						
		}
		$caja=new Caja($this->db_link);		
		$caja->Reestablecer_pagos($serie_contrato,$no_contrato);
	}
	private function validateAddressCobro($serie_contrato,$no_contrato){
		$SQL="SELECT count(*)as total FROM 
				`sys_direcciones` 
			WHERE 
				 `serie_contrato`='".$serie_contrato."' AND `no_contrato`='".$no_contrato."'"; 
 
		$rs=mysql_query($SQL);
		$parent=array();
		$row=mysql_fetch_assoc($rs);
		return $row['total'];	
	}
	
	private function getFomateAddress($sector){
		$SQL="SELECT  sys_sector.idsector,
			CONCAT(sys_provincia.`descripcion`,',',sys_ciudad.Descripcion,',',sys_sector.`descripcion`) AS direccion
		FROM   sys_sector  
		INNER JOIN `sys_ciudad` ON (`sys_sector`.`idciudad`=sys_ciudad.idciudad)
		INNER JOIN `sys_municipio` ON (`sys_municipio`.`idmunicipio`=sys_ciudad.idmunicipio) 
		INNER JOIN `sys_provincia` ON (`sys_provincia`.`idprovincia`=sys_municipio.idprovincia)
		WHERE sys_sector.idsector='". mysql_real_escape_string($sector)."'";
		$rs= mysql_query($SQL);
		$address="";
		while($row=mysql_fetch_assoc($rs)){     
			$address=$row['direccion'];
		}
		return $address;
	}
	
	public function addAsesor($idnit){
		$_SESSION['CONTRATO_DATA']['asesor']=array("idnit"=>$idnit);		 																	
	}		
	
	public function addContratante($contratante,$documento){
		$_SESSION['CONTRATO_DATA']['contratante']=array(
														"idnit"=>$contratante,
														"id_documento"=>$documento
													);												
	}
	
	public function getContratante(){
		return $_SESSION['CONTRATO_DATA']['contratante'];											
	}
	
	public function addRepresentante1($representante,$documento,$parentesco){
		
		$_SESSION['CONTRATO_DATA']['representante1']=array(
														"idnit"=>$representante,
														"id_documento"=>$documento,
														"id_parentesco"=>$parentesco
													);
	}
	
	public function addRepresentante2($representante,$documento,$parentesco){
	 
		$_SESSION['CONTRATO_DATA']['representante2']=array(
														"idnit"=>$representante,
														"id_documento"=>$documento,
														"id_parentesco"=>$parentesco
													);
	}
	
	public function addBeneficiario1($beneficiario){
		$_SESSION['CONTRATO_DATA']['beneficiario1']=$beneficiario;
	}
	
	public function changeRepresentante($type,$last_idnit,$no_contrato,$serie_contrato){
		if (isset($_SESSION['CONTRATO_DATA'][$type])){
			$_data=$_SESSION['CONTRATO_DATA'][$type]; 
			$this->_representante=array("valid"=>true,"data"=>array());	
			array_push($this->_representante['data'],$_data); 
			$this->fillReresentantes();
			 
			$_contrato=$this->getInfoContrato($serie_contrato,$no_contrato); 
			foreach($this->_representante_list as $key => $val){
				$rep=$val['representante'];
				if (!$this->containParentesco($_contrato->id_nit_cliente,$rep->id_nit)){
					$parent=$val['parentesco'];	
					$parent->id_nit=$_contrato->id_nit_cliente;
					$SQL=$parent->toSQL("insert");  
					mysql_query($SQL); 
				} 
				$SQL="SELECT COUNT(*) AS tt FROM representantes where no_contrato='". mysql_real_escape_string($no_contrato)."' and serie_contrato='".mysql_real_escape_string($serie_contrato)."' and  id_nit_representante='".mysql_real_escape_string($last_idnit)."'";
				 
				$rs=mysql_query($SQL);
				$row=mysql_fetch_assoc($rs); 
				if ($row['tt']>0){
					unset($rep->no_contrato);
					unset($rep->serie_contrato);
					unset($rep->empresa);			 
					$SQL=$rep->toSQL("update"," where no_contrato='".$no_contrato."' and serie_contrato='".$serie_contrato."' and  id_nit_representante='".$last_idnit."'"); 
					$this->message['error']=false;
					$this->message['mensaje']="Cambio realizado!";
				}else{
					$rep->no_contrato=$_contrato->no_contrato;
					$rep->serie_contrato=$_contrato->serie_contrato;
					$rep->empresa=$_contrato->EM_ID;		
					$SQL=$rep->toSQL("insert"); 
					$this->message['error']=false;
					$this->message['mensaje']="Registro agregado!";
				} 
				mysql_query($SQL);	
			}

			
		}
		
		return $this->message;
	}
	
	public function changeBeneficiario($type_beneficiario,$lastBeneficiario,$no_contrato,$serie_contrato){
		//beneficiario1
		/*$type_beneficiario = EN CASO QUE SEA EL BENEFICIARIO 1 O 2*/
		if (isset($_SESSION['CONTRATO_DATA'][$type_beneficiario])){
			$_data=$_SESSION['CONTRATO_DATA'];
			$_beneficiario=array("valid"=>true,"data"=>array());
			array_push($_beneficiario['data'],$_data[$type_beneficiario]);			
			$this->_beneficiario=$_beneficiario;
			
			$this->fillBeneficiarios();
			$_contrato=$this->getInfoContrato($serie_contrato,$no_contrato);
		 
			$SQL="SELECT COUNT(*) AS tt FROM beneficiario where no_contrato='". mysql_real_escape_string($no_contrato)."' and serie_contrato='".mysql_real_escape_string($serie_contrato)."' and  id_nit='".mysql_real_escape_string($lastBeneficiario)."'";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			if ($row['tt']>0){
				/*EN CASO DE QUE EL BENEFICIARIO NO EXISTA!*/
				foreach($this->_beneficiario_list as $key => $val){
					if ($val['isMayor']){ 
						$ben=$val['beneficiario']; 
						if (!$this->containParentesco($_contrato->id_nit_cliente,$ben->id_nit)){
							$parent=$val['parentesco'];	 
							$parent->id_nit=$_contrato->id_nit_cliente;
							$SQL=$parent->toSQL("insert"); 
							mysql_query($SQL);		
						}  
						unset($ben->no_contrato);
						unset($ben->serie_contrato);
						unset($ben->empresa); 	 
						$SQL=$ben->toSQL("update","where no_contrato='".$no_contrato."' and serie_contrato='".$serie_contrato."' and  id_nit='".$lastBeneficiario."'"); 
						mysql_query($SQL);	
						
						SysLog::getInstance()->Log($_contrato->id_nit_cliente, 
															 $serie_contrato,
															 $no_contrato,
															 '',
															 '',
															 "CAMBIO DE BENEFICIARIO ",
															 json_encode($ben),
															 'INFO');	 							
					}else{
						$ben=$val['beneficiario']; 
						$ben->id_nit="";
						unset($ben->no_contrato);
						unset($ben->serie_contrato);
						unset($ben->empresa); 	
						$SQL=$ben->toSQL("update","where no_contrato='".$no_contrato."' and serie_contrato='".$serie_contrato."' and  id_nit='".$lastBeneficiario."'");  
						mysql_query($SQL);	   
						SysLog::getInstance()->Log($_contrato->id_nit_cliente, 
															 $serie_contrato,
															 $no_contrato,
															 '',
															 '',
															 "ACTUALIZANDO BENEFICIARIO ",
															 json_encode($ben),
															 'INFO');							
					}			
				}
				$this->message['error']=false;
				$this->message['mensaje']="Cambio realizado!";
			}else{
				/*SI NO EXISTE EL BENEFICIARIO LO CREO*/
				foreach($this->_beneficiario_list as $key => $val){
					if ($val['isMayor']){
						$parent=$val['parentesco'];	 
						$parent->id_nit=$_contrato->id_nit_cliente;
						$SQL=$parent->toSQL("insert");
						mysql_query($SQL);				
					} 
					$ben=$val['beneficiario'];
					$ben->no_contrato=$_contrato->no_contrato;
					$ben->serie_contrato=$_contrato->serie_contrato;
					$ben->empresa=$_contrato->EM_ID;				 
					$SQL=$ben->toSQL("insert"); 
					mysql_query($SQL);	
					SysLog::getInstance()->Log($_contrato->id_nit_cliente, 
														 $_contrato->serie_contrato,
														 $_contrato->no_contrato,
														 '',
														 '',
														 "AGREGANDO BENEFICIARIO ",
														 json_encode($ben),
														 'INFO');						
							
				}
				$this->message['error']=false;
				$this->message['mensaje']="Beneficario agregado!";
			} 
			
		}
		
		return $this->message;
	}
	
	public function updateBeneficiarioMenor($data){
		
		if ((validateField($data,"primer_nombre") && 
			validateField($data,"primer_apellido")&& 
			validateField($data,"fecha_nacimiento")&& 
			validateField($data,"lugar_nacimiento")&& 
			validateField($data,"parentesco_id")&& 
			validateField($data,"serie_contrato")&& 
			validateField($data,"no_contrato"))){
			
			$obj=new ObjectSQL();
			$obj->nombre_1=$data['primer_nombre'];
			$obj->nombre_2=$data['segundo_nombre'];
			$obj->apellido_1=$data['primer_apellido'];
			$obj->apelllido_2=$data['segundo_apellido'];
			$obj->fecha_nacimiento="STR_TO_DATE('".$data['fecha_nacimiento']."','%d-%m-%Y')";
			$obj->lugar_nacimiento=$data['lugar_nacimiento'];
			$obj->setTable('beneficiario');
			$SQL=$obj->toSQL('update'," where 
									no_contrato='".System::getInstance()->Decrypt($data['serie_contrato'])."' AND
									serie_contrato='".System::getInstance()->Decrypt($data['no_contrato'])."' AND
									id_beneficiario='".System::getInstance()->Decrypt($data['id_beneficiario'])."'
									");
 	
			mysql_query($SQL); 
			$this->message['error']=false;
			$this->message['mensaje']="Beneficario Acutalizado!"; 
		}else{
			$this->message['error']=true;
			$this->message['mensaje']="Debe de completar los datos obligatorios!"; 
		}
		
		
		return $this->message;
	}
	
	public function containParentesco($idnit,$parent_nit){
		$SQL="SELECT COUNT(*) AS tt FROM `sys_parentesco` WHERE id_nit='".mysql_real_escape_string($idnit)."' AND  id_nit_parentesco='".mysql_real_escape_string($parent_nit)."' ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['tt']>0){
			return true;	
		}
		
		return false;
	}
	
	public function addBeneficiario2($beneficiario){
		$_SESSION['CONTRATO_DATA']['beneficiario2']=$beneficiario;
	}	
	
	public function addProducto($data){
		/*RECORRIENDO LA LISTA DE PRODUCTOS*/
	 	
		if (count($_SESSION['CONTRATO_DATA']['product_list'])>0){
			$valid=true;
			foreach($_SESSION['CONTRATO_DATA']['product_list'] as $key =>$val){
				if ($val['producto']['product_id']==$data['producto']['product_id']){
					$valid=false;
				}
			}
			
			if ($valid==true){
				array_push($_SESSION['CONTRATO_DATA']['product_list'],$data);
			}
			
		}else{
			array_push($_SESSION['CONTRATO_DATA']['product_list'],$data);	
		}	
	}
	
	public function editProducto($data){
		/*RECORRIENDO LA LISTA DE PRODUCTOS*/
		if (count($_SESSION['CONTRATO_DATA']['product_list'])>0){
			//$valid=false;
			foreach($_SESSION['CONTRATO_DATA']['product_list'] as $key =>$val){
				if ($val['producto']['product_id']==$data['producto']['product_id']){
					$_SESSION['CONTRATO_DATA']['product_list'][$key]=$data;
					//print_r($_SESSION['CONTRATO_DATA']['product_list'][$key]);
				}
			}
			
		}	
	}
	
	public function addServicio($data){
		/*RECORRIENDO LA LISTA DE PRODUCTOS*/ 
		if (count($_SESSION['CONTRATO_DATA']['servicio_list'])>0){
		/*	$valid=true;
			foreach($_SESSION['CONTRATO_DATA']['servicio_list'] as $key =>$val){
				if ($val['servicio']['servicio_id']==$data['servicio']['servicio_id']){
					$valid=false;
				}
			}
			if ($valid==true){*/
				array_push($_SESSION['CONTRATO_DATA']['servicio_list'],$data);
		//	}
			
		}else{
			array_push($_SESSION['CONTRATO_DATA']['servicio_list'],$data);	
		}	
		 
	}
	 
	public function generarSolicitud(){
		SystemHtml::getInstance()->includeClass("contratos","Carrito"); 
		 
		$this->message['error']=false;
		
		$_data=$_SESSION['CONTRATO_DATA'];
		$_prospecto=array("valid"=>false,"data"=>array());
		$_contratante=array("valid"=>false,"data"=>array());
		$_beneficiario=array("valid"=>false,"data"=>array());
		$_representante=array("valid"=>false,"data"=>array());
		$_producto=array("valid"=>false,"data"=>array());
		$_asesor=array("asesor"=>"", "director"=>"", "gerente"=>"");
	

 	 	 
		 
 		/*Validando los datos del prospecto*/
		if ((validateField($_data,"prospecto") &&
			validateField($_data['prospecto'],"idnit") && validateField($_data['prospecto'],"id_documento"))){
			$_prospecto['valid']=true;
			$_prospecto['data']=$_data['prospecto'];
		}
		
		/*VALIDANDO CONTRATANTE*/
		if ((validateField($_data,"contratante") &&
			validateField($_data['contratante'],"idnit") && validateField($_data['contratante'],"id_documento"))){
			$_contratante['valid']=true;
			$_contratante['data']=$_data['contratante'];
		}else{
			$this->message['error']=true;
			$this->message['mensaje']="Debe de seleccionar el contratante!";
		}		
	 
		/*VALIDANDO BENEFICIARIO 1*/
		if ((validateField($_data,"beneficiario1") && validateField($_data['beneficiario1'],"parentesco_id"))){
			$_beneficiario['valid']=true;
			array_push($_beneficiario['data'],$_data['beneficiario1']);
		}else{ 
			$this->message['error']=true;
			$this->message['mensaje']="Debe de seleccionar el beneficiario #1!"; 
		}
		
		/*VALIDANDO BENEFICIARIO 2*/ 
		if ((validateField($_data,"beneficiario2") && validateField($_data['beneficiario2'],"parentesco_id"))){
			$_beneficiario['valid']=true; 
			array_push($_beneficiario['data'],$_data['beneficiario2']);
		}  
	  
		/*VALIDANDO REPRESENTANTE 1 */
		if ((validateField($_data,"representante1") &&
			validateField($_data['representante1'],"idnit") && validateField($_data['representante1'],"id_documento"))){
			$_representante['valid']=true;
			array_push($_representante['data'],$_data['representante1']);
		}else{
			$this->message['error']=true;
			$this->message['mensaje']="Debe de seleccionar el representante #1!";			
		}
		/*VALIDANDO REPRESENTANTE 2 */
		
		if ((validateField($_data,"representante2") &&
			validateField($_data['representante2'],"idnit") && validateField($_data['representante2'],"id_documento"))){
			$_representante['valid']=true;
			array_push($_representante['data'],$_data['representante2']);
		} 
	
		$carrito = new Carrito($this->db_link);
		$items=$carrito->getListItem();   
		$data_g=$carrito->getDetalleGeneral();
		/*VALIDO SI SE PUEDE PROCESAR LA SOLICITUD*/
		if (!$data_g['doProcesarSolicitud']){
			$this->message['error']=true;
			$this->message['mensaje']="Debe de completar el inicial para poder procesar la solicitud!";		
		}
			
		if (count($items)<=0){  
			$this->message['error']=true;
			$this->message['mensaje']="Debe de seleccionar un producto/servicio!";		
		}
 

		/*VALIDANDO REPRESENTANTE 1 */
		
		if ((validateField($_REQUEST,"empresa") &&
			validateField($_REQUEST,"serie_contrato")&&
			validateField($_REQUEST,"situacion")&&
			validateField($_REQUEST,"tipo_ingreso") )){ 
			/*SI LA SITUACION ES PRENESECIDAD ENTONCES LA EMPRESA POR DEFAULT ES
			SERIVICIOS MEMORIALES*/
			if ($_REQUEST['situacion']=="PRE"){
				$this->_EMP_ID="SJM";
			}else{
				$this->_EMP_ID=System::getInstance()->Decrypt($_REQUEST['empresa']);
			}
			
			$this->_serie_contrato=$_REQUEST['serie_contrato'];	
			$this->_no_contrato=$_REQUEST['no_contrato'];	
			$this->_prosp_parentesco=System::getInstance()->Decrypt($_REQUEST['prosp_parentesco']);	
		}else{
			$this->message['error']=true;
			$this->message['mensaje']="Debe de llenar los datos de la empresa!";	
		}
		   
		$this->_prospecto=$_prospecto;
		$this->_contratante=$_contratante;
		$this->_beneficiario=$_beneficiario;
		$this->_representante=$_representante;
		$this->_producto=$_producto;
		 
		/*VERIFICO SI LA SOLICITUD TIENE UN PROSPECTO Y DEL PROSPECTO 
		CAPTURO EL ASESOR*/ 
		if (validateField($_prospecto,'valid')){
			if ($_prospecto['valid']=="1"){ 
				$this->_asesor=$this->getAsesoresByProspectoIDNit($this->_prospecto['data']['idnit']);
			}
		}else if (validateField($_data,"asesor")){ 
			$this->_asesor=$this->getAsesores($_data['asesor']['idnit']);		
		}else{
			$this->message['error']=true;
			$this->message['mensaje']="Debe de seleccionar el asesor!";	
		} 
		
		if (!$this->message['error']){
			$this->fillReresentantes();
			$this->fillBeneficiarios(); 
			$this->fill_products();	 
			$this->fill_descuentos();
 
			if ($this->create_contrato()){	
				$this->message['error']=false;
				$this->message['mensaje']="Solicitud creada";
			}
		} 
		 
		return $this->message;
	}
	
	private function fill_descuentos(){
		$carrito = new Carrito($this->db_link);
		$items=$carrito->getDescuento();    
		foreach($items as $key =>$val){  
			$descuento=json_decode(System::getInstance()->Decrypt($val->descuento_id)); 
			$obj= new ObjectSQL();
			$obj->serie_contrato='';
			$obj->no_contrato='';
			$obj->EM_ID='';
			$obj->codigo_descuento=$descuento->codigo;
			if (isset($val->monto)){
				$obj->monto_descuento=$val->monto;		
			}
			if (isset($val->porcentaje)){
				$obj->porc_descuento=$val->porcentaje;		
			}			 
			$obj->persona_autorizacion='';												
			$obj->setTable("descuentos_contratos");
			array_push($this->_descuento_list,$obj);
		} 
	}
	
	/*AGREGA LA MONEDA EN QUE SE VA A FACTURAR EL CONTRATO*/
	public function addMonedaAndPlazo($moneda,$plazo,$enganche){
		$_SESSION['CONTRATO_DATA']['financiamiento']=array(
															"moneda"=>$moneda,
															"plazo"=>$plazo,
															"enganche"=>$enganche
														); 
	}
	public function getMonedaAndPlazo(){
		return $_SESSION['CONTRATO_DATA']['financiamiento'];
	}
	public function create_contrato(){
		// SystemHtml::getInstance()->includeClass("financiamiento","PlanFinanciamiento");		 
		if ($this->contractExist($this->_serie_contrato,$this->_no_contrato)>0){
			$this->message['error']=true;
			$this->message['mensaje']="Este numero de contrato existe, el sistema no acepta duplicados!";			
			return false;
			exit;
		} 
		$contrato= new ObjectSQL();
		$contrato->serie_contrato=strtoupper($this->_serie_contrato);	
		$contrato->EM_ID=$this->_EMP_ID;	
		
		$contrato->no_contrato=$this->_no_contrato; //$this->getContractCount();	
		$contrato->id_nit_cliente=$this->_contratante['data']['idnit'];	
		$contrato->correspondencia="R"; //DEFINIR
		$contrato->observaciones=mysql_real_escape_string($_REQUEST['observaciones']);
  
		$contrato->codigo_asesor=$this->_asesor[0]['id_comercial']; 
		$contrato->codigo_gerente=$this->_asesor[1]['id_comercial'];
		
		$carrito = new Carrito($this->db_link);
		$dmontos=$carrito->getDetalleGeneral();  
		$contrato->precio_lista=$dmontos['precio_lista'];	 
		$contrato->porc_enganche=$dmontos['porciento_enganche'];
		$contrato->porc_interes=$dmontos['interes_anual'];
		$contrato->cuotas=$dmontos['plazo'];
		//$contrato->CODIGO_TP=trim($this->_producto['data'][0]['financiamiento']['data']['codigo']); 
		$contrato->no_productos=count($this->_product_list);	
		$contrato->precio_neto=$dmontos['capital_neto_a_pagar'];	
		$contrato->porc_descuento=$dmontos['sum_descuento_por'];
		$contrato->descuento=$dmontos['total_descuentos']; 
		$contrato->enganche=$dmontos['monto_pago_caja']; 
		$contrato->porc_impuesto=0;//$dmontos['sum_descuento_por']; //DEFINIR
		$contrato->impuesto="0";//definir
		$contrato->interes=$dmontos['monto_total_interes_a_pagar']; 
		$contrato->valor_cuota=$dmontos['mensualidades'];
		 
		$contrato->capital_pagado=0;//PENDIENTE POR DEFINIR
		$contrato->intereses_pagados=0; //PENDIENTE POR DEFINIR
		$contrato->impuesto_pagado=0;//PENDIENTE POR DEFINIR
		$contrato->fecha_primer_pago="";  
		
		$contrato->estatus=13;
		$contrato->id_nit_reiterador=UserAccess::getInstance()->getIDNIT();//PENDIENTE POR DEFINIR
		$contrato->id_nit_ingreso=UserAccess::getInstance()->getIDNIT();//
		$contrato->situacion=$_REQUEST['situacion'];
		$contrato->tipo_ingreso=$_REQUEST['tipo_ingreso'];
		 
		//		$obj->fecha_primer_pago=
		//		$obj->fecha_venta="STR_TO_DATE('".$this->data['fecha_venta']."','%d-%m-%Y')";
		//		$obj->dia_pago="DAY(STR_TO_DATE('".$this->data['fecha_pago']."','%d-%m-%Y'))";
		 
		if (trim($_REQUEST['fecha_venta'])==""){
			$_REQUEST['fecha_venta']=date('d-m-Y');
			$_REQUEST['fecha_primer_pago']=$_REQUEST['fecha_venta'];
		}
				//"STR_TO_DATE('".$_REQUEST['fecha_primer_pago']."','%d')";		
		$contrato->fecha_primer_pago="DATE_ADD(STR_TO_DATE('".$_REQUEST['fecha_primer_pago']."','%d-%m-%Y'),INTERVAL 30 DAY)";
		//"STR_TO_DATE('".$_REQUEST['fecha_primer_pago']."','%d-%m-%Y')";
		$contrato->fecha_venta="STR_TO_DATE('".$_REQUEST['fecha_venta']."','%d-%m-%Y')";
		$contrato->dia_pago="STR_TO_DATE('".$_REQUEST['fecha_primer_pago']."','%d')";

		$contrato->prospecto=$this->_prospecto['data']['idnit'];
		
//		$contrato->prospecto=$this->_prospecto['valid']==true?$this->_prospecto['data']['idnit']:0;
		
		$financiamiento=$this->getMonedaAndPlazo();
		$tasa=$this->getTasaCambio($financiamiento['moneda']); 
		$tipo_cambio=$tasa['cambio']; 
		if (!validateField($tasa,"cambio")){
			$this->message['error']=true;
			$this->message['mensaje']="Error debe de elegir el plazo y la moneda de esta oferta!";			
			return false;
			exit;
		} 		
	 
		$contrato->tipo_moneda=$financiamiento['moneda'];
		$contrato->tipo_cambio=$tipo_cambio;	
	 	$contrato->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$contrato->mes="DATE_FORMAT(CURDATE(), '%c')";
		$contrato->ano="DATE_FORMAT(curdate(), '%Y')";	 
		$SQL=$contrato->getSQL("insert","contratos"); 
   
	 	mysql_query($SQL); 
		 
 		foreach($this->_product_list as $key =>$producto){  
			/*SACO EL TOTAL DE ITEMS QUE VAN A SER PROCESADOS*/  
			$producto->no_contrato=$contrato->no_contrato;
			$producto->serie_contrato=strtoupper($contrato->serie_contrato);
			$producto->EM_ID=$contrato->EM_ID;
			  
			/*PROCESO QUE REGISTRA LA VENTA EN LA TABLA DE VENTAS*/
//			$this->registarVenta($contrato,$producto); 
			unset($producto->plazo);
			$SQL=$producto->getSQL("insert","producto_contrato");
		 	mysql_query($SQL);	 
	  
			if (isset($producto->id_jardin)){
				/*PROCESO PARA OCUPAR LAS PARCELAS*/
				$parcela=new ObjectSQL();
				$parcela->no_contrato=$producto->no_contrato; //LE ASIGNO EN # DE CONTRATO A LA PARCELA
				$parcela->serie_contrato=$producto->serie_contrato;
				$parcela->estatus=17;//ESTATUS CONTRATO 
				$SQL=$parcela->setTable("inventario_jardines")->toSQL("update","
					WHERE 
					 bloque='".mysql_real_escape_string($producto->bloque)."' AND
					 lote='".mysql_real_escape_string($producto->lote)."' AND
					 id_fases='".mysql_real_escape_string($producto->id_fases)."' AND
					 id_jardin='".mysql_real_escape_string($producto->id_jardin)."' AND
					 osario='".mysql_real_escape_string($producto->osario)."' ");
				  
			 	 mysql_query($SQL);	 
			 }		 
		}	  
	 	 
	 	/*SI HAY UN PROSPECTO ENTONCES LO PONGO EN ESTADO 4 => CLIENTE*/
	 	if ($this->_prospecto['valid']){ 
			$prosp= new ObjectSQL();
			$prosp->estatus=4; //estatus CLIENTE
			$prosp->no_contrato=$contrato->no_contrato;
			$prosp->serie_contrato=strtoupper($contrato->serie_contrato);
			$SQL=$prosp->setTable('prospectos')->toSQL("update","where id_nit='". mysql_real_escape_string($this->_prospecto['data']['idnit']) ."'");
			mysql_query($SQL); 
			 
			/*ACTUALIZO EL ESTATUS EN PROSPECTO COMERCIAL*/
			$prosp_c= new ObjectSQL();
			$prosp_c->estatus=4; //estatus CLIENTE
			$SQL=$prosp_c->setTable('prospecto_comercial')->toSQL("update","where id_nit='". mysql_real_escape_string($this->_prospecto['data']['idnit']) ."' and estatus in (5,6,7) ");
			mysql_query($SQL);
		
			/*TIPO DE PARENTESCO DEL PROSPECTO*/
			if ($this->_prosp_parentesco!=""){
				$obj= new ObjectSQL();
				$obj->id_nit=$this->_contratante['data']['idnit'];
				$obj->id_nit_parentesco=$this->_prospecto['data']['idnit'];
				$obj->id_parentesco=$this->_prosp_parentesco;
				$SQL=$obj->setTable("sys_parentesco")->toSQL("insert");
				mysql_query($SQL);
			}
		}
		 
		foreach($this->_descuento_list as $key =>$desc){
			$desc->no_contrato=$contrato->no_contrato;
			$desc->serie_contrato=$contrato->serie_contrato;
			$desc->EM_ID=$contrato->EM_ID;
			$SQL=$desc->getSQL("insert","descuentos_contratos");
		 	mysql_query($SQL);
		}		
		     
		foreach($this->_beneficiario_list as $key => $val){
			if ($val['isMayor']){
				$parent=$val['parentesco'];	 
				$SQL=$parent->toSQL("insert");
		  		mysql_query($SQL);				
			}
			
			$ben=$val['beneficiario'];
			$ben->no_contrato=$contrato->no_contrato;
			$ben->serie_contrato=$contrato->serie_contrato;
			$ben->empresa=$contrato->EM_ID;				 
			$SQL=$ben->toSQL("insert");
	 	  	mysql_query($SQL);				
		}
 
		foreach($this->_representante_list as $key => $val){
			$parent=$val['parentesco'];	
			$SQL=$parent->toSQL("insert");
	 		mysql_query($SQL);	
	
			$rep=$val['representante'];
			$rep->no_contrato=$contrato->no_contrato;
			$rep->serie_contrato=$contrato->serie_contrato;
			$rep->empresa=$contrato->EM_ID;				 
			$SQL=$rep->toSQL("insert");
	 		mysql_query($SQL);	
		}

		SysLog::getInstance()->Log($contrato->id_nit_cliente, 
								 $contrato->serie_contrato,
								 $contrato->no_contrato,
								 '',
								 '',
								 "CREACION DE SOLICITUD ".$contrato->serie_contrato." ".$contrato->no_contrato,
								 json_encode($contrato),
								 'CONTRATO');		
		
		$caja=new Caja($this->db_link);
		
 		$LOG=array(
			"SESSION"=>$_SESSION['CONTRATO_DATA'],
			"PRODUCTO"=>$this->_product_list,
			"PROSPECTO"=>$this->_prospecto,
			"PROSPECTO_PARENTESCO"=>$this->_prosp_parentesco,
			"DESCUENTOS"=>$this->_descuento_list,
			"BENEFICIARIOS"=>$this->_beneficiario_list,
			"REPRESENTANTES"=>$this->_representante_list,
			"CONTRATO"=>$contrato,
			"CONTRATANTE"=>$this->_contratante,
			"CARRITO_ITEM"=>$carrito->getListItem(),
			"CARRITO_DATA"=>$carrito->getCarritoData(),
			"DETALLE_CARRITO_GENERAL"=>$dmontos,
			"CAJA_ITEM"=>$caja->getItemListAbono()
			
		);		
					 
		SysLog::getInstance()->Log($contrato->id_nit_cliente, 
								 $contrato->serie_contrato,
								 $contrato->no_contrato,
								 '',
								 '',
								 "TRANSACCION CREACION SOLICITUD",
								 json_encode($LOG),
								 'TRANSACCION');
		 
		 $this->frizarInicialesCaja();
		/*proceso que actualiza el movimiento contrato con los abonos, INICIALES */
 		/*Creo los asientos contables*/
		//$this->MovimientoContableCreacionContrato($mov_contrato,$contrato);
		$this->session_restart();
		$this->actualiarVenta($contrato->serie_contrato,$contrato->no_contrato);
		return true;
	}
	
	private function actualiarVenta($serie_contrato,$no_contrato){
		
		mysql_query("DELETE FROM `contratos_ventas` WHERE serie_contrato='".$serie_contrato."' 
		and no_contrato='".$no_contrato."' ");
		
		$SQL="SELECT
		 contratos.*,
		 CONCAT(gerente.primer_nombre, ' ', gerente.segundo_nombre, ' ', gerente.primer_apellido) AS nombre_gerente,
		  CONCAT(asesor.primer_nombre, ' ', asesor.segundo_nombre, ' ', asesor.primer_apellido) AS nombre_asesor,
		 CONCAT(cliente.primer_nombre, ' ', cliente.segundo_nombre, ' ', cliente.primer_apellido) AS nombre_cliente,
		(SELECT (CASE
		 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo)
		 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS nombre_producto
		 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND
		  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) AS nombre_producto
		 FROM contratos
		 INNER JOIN sys_personas AS cliente ON (cliente.id_nit = contratos.id_nit_cliente)
		 INNER JOIN sys_asesor ON (sys_asesor.codigo_asesor = contratos.codigo_asesor)
		 INNER JOIN sys_personas AS asesor ON (asesor.id_nit = sys_asesor.id_nit)
		 INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus)
		 INNER JOIN sys_gerentes_grupos ON (sys_gerentes_grupos.codigo_gerente_grupo = contratos.codigo_gerente)
		  INNER JOIN sys_personas AS gerente ON (gerente.id_nit = sys_gerentes_grupos.id_nit)
		 WHERE  serie_contrato='".$serie_contrato."' and no_contrato='".$no_contrato."'  ";
		$rs=mysql_query($SQL);
		
		while($row=mysql_fetch_assoc($rs)){
			$obj = new ObjectSQL();
			$obj->push($row);
			$obj->setTable("contratos_ventas");
			$SQL=$obj->toSQL("insert"); 
			mysql_query($SQL);  
			  
			$SQL="SELECT * FROM `cierres`  WHERE ano=YEAR('".$row['fecha_venta']."')  
					AND mes=MONTH('".$row['fecha_venta']."') ";
			$rsX=mysql_query($SQL);
			while($rowx=mysql_fetch_assoc($rsX)){ 
				$ob=new ObjectSQL();
				$ob->periodos_cierre=$rowx['cierre_id'];
				$ob->setTable("contratos");
				$SQL=$ob->toSQL("update"," where serie_contrato='".$serie_contrato."' and no_contrato='".$no_contrato."' "); 
				mysql_query($SQL);
		 
				$ob=new ObjectSQL();
				$ob->periodos_cierre=$rowx['cierre_id'];
				$ob->setTable("contratos_ventas");
				$SQL=$ob->toSQL("update"," where serie_contrato='".$serie_contrato."' and no_contrato='".$no_contrato."' ");
				mysql_query($SQL); 
			}  
			
		} 
	}

	/*METODO QUE FRIZA LOS INICIALES PARA SER UTILIZADOS EN UN CONTRATO*/
	private function frizarInicialesCaja(){
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		SystemHtml::getInstance()->includeClass("contratos","Carrito"); 
		$mov_contrato= new ObjectSQL();
		$caja=new Caja($this->db_link); 
		$carrito = new Carrito($this->db_link); 
		$data_g=$carrito->getDetalleGeneral();
		/*VALIDO SI SE PUEDE PROCESAR LA SOLICITUD*/
		if ($data_g['doProcesarSolicitud']){
			$items=$carrito->getListItem();   
			$getitem=$caja->getItemListAbono(); 
			foreach($getitem as $key=> $val){
			//	$mov=$caja->getMovimientoCaja($val->SERIE,$val->NO_DOCTO);
				$mov_c= new ObjectSQL();
				$mov_c->NO_CONTRATO=$this->_no_contrato;
				$mov_c->SERIE_CONTRATO=$this->_serie_contrato;
				$mov_c->ID_ESTATUS=37;	
				$mov_c->setTable("movimiento_caja");
				$SQL=$mov_c->toSQL("update"," where  SERIE='".mysql_real_escape_string($val->CAJA_SERIE)."' and NO_DOCTO='".mysql_real_escape_string($val->CAJA_NO_DOCTO)."' and TIPO_DOC='".mysql_real_escape_string($val->TIPO_DOC)."' ");
				mysql_query($SQL);
			}
		}
	}
	
	
	private function registarVenta($contrato,$producto){ 
		$creado_por=UserAccess::getInstance()->getIDNIT(); 
		$venta= new ObjectSQL();
		$venta->serie=strtoupper($producto->serie_contrato);	
		$venta->contrato=strtoupper($producto->no_contrato);	
		$venta->fecha_ingreso="CURDATE()";
		$descripcion=""; 
		if (isset($producto->id_jardin)){
			$ds=$this->getDescripcionProducto($producto->id_fases,$producto->id_jardin);
			$descripcion=$ds['nombre']; 
			$venta->id_jardin=$producto->id_jardin;	
			$venta->id_fases=$producto->id_fases;	
			$venta->lote=$producto->lote;	
			$venta->osario=$producto->osario;	
			$venta->bloque=$producto->bloque;				
		}else{
			$ds=$this->getDescripcionServicio($producto->serv_codigo);
			$descripcion=$ds['nombre']; 
			$venta->serv_codigo=$producto->serv_codigo;	
		}
		$venta->producto=$descripcion; 
		$venta->cantidad_productos=1; 
		$venta->precio_lista=$producto->precio_lista;
		$venta->CODIGO_TP=$producto->CODIGO_TP;		 
		$venta->inicial=$producto->enganche;	
		$venta->enganche=$producto->porc_enganche;	
		$venta->precio_neto=$producto->precio_neto;	
		$venta->descuento_monto=$producto->total_descuento;	
		$venta->estatus=13;	//VERIFICACION
		$venta->cuotas=$producto->plazo;	
		$venta->codigo_asesor=$contrato->codigo_asesor;	
		$venta->codigo_gerente=$contrato->codigo_gerente;	
		$venta->creado_por=$creado_por;
   		  
		$SQL=$venta->getSQL("insert","ventas");  
		mysql_query($SQL);  
		SysLog::getInstance()->Log($contrato->id_nit_cliente, 
								 $contrato->serie_contrato,
								 $contrato->no_contrato,
								 '',
								 '',
								 "CREACION DE REGISTRO DE VENTAS",
								 json_encode($venta),
								 'VENTAS');			
	}
	
	public function getDescripcionProducto($id_fase,$id_jardin){ 
		$SQL="SELECT 
			CONCAT(jardines.jardin,' ',fases.fase) AS nombre
			 FROM `jardines_activos`
			INNER JOIN `jardines` ON (`jardines`.`id_jardin`=jardines_activos.`id_jardin`)
			INNER JOIN `fases` ON (`fases`.`id_fases`=jardines_activos.`id_fases`)
			WHERE jardines_activos.id_jardin='".$id_jardin."' 
				AND jardines_activos.id_fases='".$id_fase."'";
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){ 
			$data=$row;
		} 
		return $data;
	}
	public function getDescripcionServicio($serv_codigo){ 
		$SQL="SELECT serv_descripcion as nombre FROM `servicios` WHERE serv_codigo='".$serv_codigo."'";
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){ 
			$data=$row;
		} 
		return $data;
	}		
	private function MovimientoContableCreacionContrato($mov_contrato,$detalle_contrato){ 
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		/*REGISTRO CONTABLE */
		/*CAJA NO FISCAL*/ 	
		$ID_EMPRESA="SJMN";
		$documento="";
		$MOV_CONTABLE="CI"; //MOVIMIENTO CONTABLE INICIAL
		$contabilidad= new ModContable($this->db_link);
		$catalogo=$contabilidad->getCatalogo("NF");
		$i=1;
		
		$info_log=array(
			"ID_NIT"=>$detalle_contrato->id_nit_cliente,
			"SERIE_CONTRATO"=>$detalle_contrato->serie_contrato,
			"NO_CONTRATO"=>$detalle_contrato->no_contrato,
			"NO_RESERVA"=>'',
			"ID_RESERVA"=>''									
		); 	 
		
		$contrato= $detalle_contrato->serie_contrato." ".$detalle_contrato->no_contrato;
		 
		$MONTO_TOTAL=$mov_contrato->TOTAL_MOV+$detalle_contrato->precio_neto+$detalle_contrato->interes+$detalle_contrato->descuento; 
		$contabilidad->registrarAsientoC($info_log,
										$ID_EMPRESA,
										  date("Y"),
										  rand(), 
										  $i,
										  $catalogo['ANTICIPO_RECIBIDOS_CLIENTES']['cuenta'],  
										  "DEBITO",
										  $mov_contrato->TOTAL_MOV,
										  $documento,
										  "DEBITO CUENTA ".$catalogo['ANTICIPO_RECIBIDOS_CLIENTES']['cuenta'],
										  "N/A",
										  $contrato,
										  "",
										  "",
										  "",
										  "",
										  $catalogo['ANTICIPO_RECIBIDOS_CLIENTES']['tipo_cuenta'],
										  $MONTO_TOTAL); 
										  
				//print_r($mov_contrato);		
		
										  
		$i++;
		$contabilidad->registrarAsientoC($info_log,
										$ID_EMPRESA,
										  date("Y"),
										  rand(), 
										  $i,
										  $catalogo['MODULO_CLIENTES']['cuenta'],  
										  "DEBITO",
										  $detalle_contrato->precio_neto,
										  $documento,
										  "DEBITO CUENTA ".$catalogo['MODULO_CLIENTES']['cuenta'],
										  $CENTRO_COSTO="N/A",
										  $contrato,
										  "",
										  "",
										  "",
										  "",
										  $catalogo['MODULO_CLIENTES']['tipo_cuenta'],
										  $MONTO_TOTAL); 	
										  
		$i++;
		$contabilidad->registrarAsientoC($info_log,
										$ID_EMPRESA,
										  date("Y"),
										  rand(),  
										  $i,
										  $catalogo['MODULO_CLIENTE_INTERESES']['cuenta'],   
										  "DEBITO",
										  $detalle_contrato->interes,
										  $documento,
										  "DEBITO CUENTA ".$catalogo['MODULO_CLIENTE_INTERESES']['cuenta'],
										  $CENTRO_COSTO="N/A",
										  $contrato,
										  "",
										  "",
										  "",
										  "",
										  $catalogo['MODULO_CLIENTE_INTERESES']['tipo_cuenta'],
										  $MONTO_TOTAL); 

		$i++;				  
		$contabilidad->registrarAsientoC($info_log,
										$ID_EMPRESA,
										  date("Y"),
										  rand(),  
										  $i,
										  $catalogo['INGRESO_DIFERIDO_CAPITAL']['cuenta'],  
										  "CREDITO",
										  $detalle_contrato->precio_lista,
										  $documento,
										  "CREDITO CUENTA ". $catalogo['INGRESO_DIFERIDO_CAPITAL']['cuenta'],
										  $CENTRO_COSTO="N/A",
										  $contrato,
										  "",
										  "",
										  "",
										  "",
										  $catalogo['INGRESO_DIFERIDO_CAPITAL']['tipo_cuenta'],
										  $MONTO_TOTAL); 
										  
										  
		if ($detalle_contrato->descuento>0){
			$i++;
			$contabilidad->registrarAsientoC($info_log,
										$ID_EMPRESA,
											  date("Y"),
											  rand(),  
											  $i,
											  $catalogo['DESCUENTO_VENTAS_DIFERIDOS']['cuenta'], 
											  "DEBITO",
											  $detalle_contrato->descuento,
											  $documento,
											  "DEBITO CUENTA ".$catalogo['DESCUENTO_VENTAS_DIFERIDOS']['cuenta'],
											  $CENTRO_COSTO="N/A",
											  $contrato,
											  "",
											  "",
											  "",
											  "",
											  $catalogo['DESCUENTO_VENTAS_DIFERIDOS']['tipo_cuenta'],
											  $MONTO_TOTAL); 
		}
		$i++;
		$contabilidad->registrarAsientoC($info_log,
										$ID_EMPRESA,
										  date("Y"),
										  rand(),  
										  $i,
										  $catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['cuenta'],
										  "CREDITO",
										  $detalle_contrato->interes,
										  $documento,
										  "CREDITO CUENTA ".$catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['cuenta'],
										  $CENTRO_COSTO="N/A",
										  $contrato,
										  "",
										  "",
										  "",
										  "",
										  $catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['tipo_cuenta'],
										  $MONTO_TOTAL); 
 
				
	}
	

	private function createMovContrato($serie_contrato,$no_contrato){
		SystemHtml::getInstance()->includeClass("caja","Caja");   
		$mov_contrato= new ObjectSQL();
		
		$caja=new Caja($this->db_link); 

 		$items=$caja->getListadoRecibosPendientePorUsarOferta($serie_contrato,$no_contrato);  
		$mov_contrato= new ObjectSQL();
 
 		/*VALIDO SI SE PUEDE PROCESAR LA SOLICITUD*/
		if (count($items)>0){			
			$NO_DOC="";
			$SERIE="";
			$TIPO_DOC="";
			$monto_mov=0;
			$abono_mov=0;
			
 			foreach($items as $key=> $val){
				
				/*CAMBIO DE ESTATUS AL RECIBO DE CAJA*/
				$mov_c= new ObjectSQL();
				$mov_c->INICIAL='S';
				$mov_c->ID_ESTATUS=38;
				$mov_c->setTable("movimiento_caja");
				$SQL=$mov_c->toSQL("update"," where  SERIE='".mysql_real_escape_string($val->SERIE)."'
								 and NO_DOCTO='".mysql_real_escape_string($val->NO_DOCTO)."' 
								 and TIPO_DOC='".mysql_real_escape_string($val->TIPO_DOC)."' ");
			 
		 		mysql_query($SQL);  
			
				$mov_c= new ObjectSQL();
				$mov_c->ESTATUS=38;//UTLIZADO
				$mov_c->setTable("movimiento_factura");
				$SQL=$mov_c->toSQL("update"," where  CAJA_SERIE='".mysql_real_escape_string($val->SERIE)."' and CAJA_NO_DOCTO='".mysql_real_escape_string($val->NO_DOCTO)."' ");				
		 	 	mysql_query($SQL);	  
			 	/////////////////////////////////////
				/*ACTUALIZO EL RECIBO VIRTUAL*/	
				$mov_c= new ObjectSQL();
				$mov_c->INICIAL='S';
				$mov_c->ID_ESTATUS=38;
				$mov_c->setTable("movimiento_caja");
				$SQL=$mov_c->toSQL("update"," where  SERIE='".mysql_real_escape_string($val->MF_SERIE)."'
								 and NO_DOCTO='".mysql_real_escape_string($val->MF_NO_DOCTO)."' ");
	 			mysql_query($SQL); 
				/////////////////////////////////////////// 
				if (($val->MFTIPO_MOV=="INI") || ($val->MFTIPO_MOV=="CTI") || ($val->MFTIPO_MOV=="NC")){
					$NO_DOC=$val->NO_DOCTO;
					$SERIE=$val->SERIE;
					$TIPO_DOC=$val->TIPO_DOC;
					$TIPO_MOV=$val->MFTIPO_MOV; 
					$monto_mov=$monto_mov+$val->MONTO;
				}else{
					$abono_mov=$abono_mov+$val->MONTO;
				} 		
			}  
			 
			//$ID_CAJA=UserAccess::getInstance()->getCaja();
			$contrato=$this->getInfoContrato($serie_contrato,$no_contrato);
			/*PROCESO DE MOVIMIENTO CONTRATO*/
			
			$mov_contrato->ID_NIT=$contrato->id_nit_cliente;//$this->_contratante['data']['idnit'];	
			$mov_contrato->NO_DOCTO=$NO_DOC;
			$mov_contrato->CAJA_SERIE=$SERIE;
			$mov_contrato->FECHA='curdate()';
		//	$mov_contrato->ID_CAJA=$ID_CAJA['ID_CAJA'];
			$mov_contrato->EM_ID=$this->_EMP_ID;	
			$mov_contrato->TIPO_DOC=$TIPO_DOC;
			$mov_contrato->TIPO_MOV=$TIPO_MOV;
			$mov_contrato->MONTO_DOC=$monto_mov;
			$mov_contrato->TOT_ABONOS=$abono_mov;
			$mov_contrato->TOTAL_MOV=$mov_contrato->MONTO_DOC+$mov_contrato->TOT_ABONOS;
			$mov_contrato->CAPITAL_PAG='0';
			$mov_contrato->INTERESES_PAG='0';
			$mov_contrato->IMPUESTO_PAG='0';
			$mov_contrato->MORA_PAG='0';
			$mov_contrato->MANTENIMIENTO='0';
			$mov_contrato->INICIAL=$monto_mov;
			$mov_contrato->NO_CUOTA='';
			$mov_contrato->CUOTA='';
			$mov_contrato->OF_COBROS=UserAccess::getInstance()->getID();
			$mov_contrato->MOTORIZADO='';
			$mov_contrato->OBSERVACIONES='PRIMER MOVIMIENTO DEL CONTRATO PAGO DE INICIAL';
			$mov_contrato->TIPO_CAMBIO='1';	
			$mov_contrato->setTable("movimiento_contrato");
			$SQL=$mov_contrato->toSQL("insert");  
			mysql_query($SQL);  
		
			SysLog::getInstance()->Log($contrato->id_nit_cliente, 
									 $contrato->serie_contrato,
									 $contrato->no_contrato,
									 '',
									 '',
									 $mov_contrato->OBSERVACIONES,
									 json_encode($mov_contrato),
									 'CONTRATO'); 			
			 								  		 
		 }	 
		 return $mov_contrato;
	}
	
	private function createMovContrato2($contrato){
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		SystemHtml::getInstance()->includeClass("contratos","Carrito"); 
		$mov_contrato= new ObjectSQL();
		
		$caja=new Caja($this->db_link); 
		 print_r($_REQUEST);
		 exit;
 		/*VALIDO SI SE PUEDE PROCESAR LA SOLICITUD*/
		if ($data_g['doProcesarSolicitud']){
			$items=$carrito->getListItem();   
			
			$NO_DOC="";
			$SERIE="";
			$TIPO_DOC="";
			$monto_mov=0;
			$abono_mov=0;
			$getitem=$caja->getItemListAbono(); 
			foreach($getitem as $key=> $val){
			//	$mov=$caja->getMovimientoCaja($val->SERIE,$val->NO_DOCTO);
				$mov_c= new ObjectSQL();
				$mov_c->INICIAL='S';
				$mov_c->NO_CONTRATO=$this->_no_contrato;
				$mov_c->SERIE_CONTRATO=$this->_serie_contrato;	
				$mov_c->setTable("movimiento_caja");
				$SQL=$mov_c->toSQL("update"," where  SERIE='".mysql_real_escape_string($val->CAJA_SERIE)."' and NO_DOCTO='".mysql_real_escape_string($val->CAJA_NO_DOCTO)."' and TIPO_DOC='".mysql_real_escape_string($val->TIPO_DOC)."' ");
				 mysql_query($SQL);
			 
				$mov_c= new ObjectSQL();
				$mov_c->ESTATUS=37;//UTLIZADO
				$mov_c->setTable("movimiento_factura");
				$SQL=$mov_c->toSQL("update"," where  CAJA_SERIE='".mysql_real_escape_string($val->CAJA_SERIE)."' and CAJA_NO_DOCTO='".mysql_real_escape_string($val->CAJA_NO_DOCTO)."' ");				
			 	mysql_query($SQL);				
				 
				if ($val->TIPO_MOV=="INI"){
					$NO_DOC=$val->CAJA_NO_DOCTO;
					$SERIE=$val->CAJA_SERIE;
					$TIPO_DOC=$val->TIPO_DOC;
					$TIPO_MOV=$val->TIPO_MOV; 
					$monto_mov=$monto_mov+$val->MONTO;
				}else{
					$abono_mov=$abono_mov+$val->MONTO;
				}
			}
		 
			$ID_CAJA=UserAccess::getInstance()->getCaja();
		 
			/*PROCESO DE MOVIMIENTO CONTRATO*/
			$mov_contrato= new ObjectSQL();
			$mov_contrato->ID_NIT=$contrato->id_nit_cliente;//$this->_contratante['data']['idnit'];	
			$mov_contrato->NO_DOCTO=$NO_DOC;
			$mov_contrato->CAJA_SERIE=$SERIE;
			$mov_contrato->FECHA='curdate()';
			$mov_contrato->ID_CAJA=$ID_CAJA['ID_CAJA'];
			$mov_contrato->EM_ID=$this->_EMP_ID;	
			$mov_contrato->TIPO_DOC=$TIPO_DOC;
			$mov_contrato->TIPO_MOV=$TIPO_MOV;
			$mov_contrato->MONTO_DOC=$monto_mov;
			$mov_contrato->TOT_ABONOS=$abono_mov;
			$mov_contrato->TOTAL_MOV=$mov_contrato->MONTO_DOC+$mov_contrato->TOT_ABONOS;
			$mov_contrato->CAPITAL_PAG='0';
			$mov_contrato->INTERESES_PAG='0';
			$mov_contrato->IMPUESTO_PAG='0';
			$mov_contrato->MORA_PAG='0';
			$mov_contrato->MANTENIMIENTO='0';
			$mov_contrato->INICIAL=$monto_mov;
			$mov_contrato->NO_CUOTA='';
			$mov_contrato->CUOTA='';
			$mov_contrato->OF_COBROS=UserAccess::getInstance()->getID();
			$mov_contrato->MOTORIZADO='';
			$mov_contrato->OBSERVACIONES='PRIMER MOVIMIENTO DEL CONTRATO PAGO DE INICIAL';
			$mov_contrato->TIPO_CAMBIO='1';	
			$mov_contrato->setTable("movimiento_contrato");
			$SQL=$mov_contrato->toSQL("insert"); 
		  	mysql_query($SQL);  
	 		SysLog::getInstance()->Log($contrato->id_nit_cliente, 
									 $contrato->serie_contrato,
									 $contrato->no_contrato,
									 '',
									 '',
									 $mov_contrato->OBSERVACIONES,
									 json_encode($mov_contrato),
									 'CONTRATO'); 							  
											  		 
		 }	 
		 return $mov_contrato;
	}

	private function fillBeneficiarios(){  
		if ($this->_beneficiario['valid']){ 
			foreach($this->_beneficiario['data'] as $key => $val){
				
				if (validateField($val,'id_documento')){
					$obj= new ObjectSQL();
					$obj->id_nit=$this->_contratante['data']['idnit'];
					$obj->id_nit_parentesco=$val['numero_documento'];
					$obj->id_parentesco=System::getInstance()->Decrypt($val['parentesco_id']);
					$obj->setTable("sys_parentesco");
					  
					$ben= new ObjectSQL();
					$ben->id_nit=$val['numero_documento'];
					$ben->no_contrato='';
					$ben->serie_contrato='';
					$ben->empresa='';										
					$ben->id_parentesco=System::getInstance()->Decrypt($val['parentesco_id']);
					$ben->nombre_1=$val['primer_nombre'];
					$ben->nombre_2=$val['segundo_nombre'];
					$ben->apellido_1=$val['primer_apellido'];
					$ben->apelllido_2=$val['segundo_apellido'];
					$ben->fecha_nacimiento="STR_TO_DATE('".$val['fecha_nacimiento']."','%d-%m-%Y')";
					$ben->lugar_nacimiento=$val['lugar_nacimiento'];
					$ben->setTable("beneficiario");					
					
					$dt=array("isMayor"=>true,"beneficiario"=>$ben,'parentesco'=>$obj); 
					array_push($this->_beneficiario_list,$dt);
				}else{
					$ben= new ObjectSQL();
					//$ben->id_nit=$this->_contratante['data']['idnit'];
					$ben->no_contrato='';
					$ben->serie_contrato='';
					$ben->empresa='';	
					$ben->id_parentesco=System::getInstance()->Decrypt($val['parentesco_id']);
					$ben->nombre_1=$val['primer_nombre'];
					$ben->nombre_2=$val['segundo_nombre'];
					$ben->apellido_1=$val['primer_apellido'];
					$ben->apelllido_2=$val['segundo_apellido'];
					$ben->fecha_nacimiento="STR_TO_DATE('".$val['fecha_nacimiento']."','%d-%m-%Y')";
					$ben->lugar_nacimiento=$val['lugar_nacimiento'];
					$ben->setTable("beneficiario");							
					
					$dt=array("isMayor"=>false,"beneficiario"=>$ben); 
					array_push($this->_beneficiario_list,$dt);
				}
				
			}
				
		} 

	}
		
	private function fillReresentantes(){
		if ($this->_representante['valid']){ 
		//_representante_list
			foreach($this->_representante['data'] as $key => $val){
				//print_r($val);
				$obj=new ObjectSQL();
				$obj->no_contrato="";
				$obj->serie_contrato="";
				$obj->empresa="";
				$obj->id_nit_representante=$val['idnit'];
				$obj->setTable("representantes");
				
				
				$prt= new ObjectSQL();
				$prt->id_nit=$this->_contratante['data']['idnit'];
				$prt->id_nit_parentesco=$val['idnit'];
				$prt->id_parentesco=$val['id_parentesco'];
				$prt->setTable("sys_parentesco");
  
				$dt=array("representante"=>$obj,"parentesco"=>$prt);
	
				array_push($this->_representante_list,$dt);

			}	
		}
	}	
	/*AGREGA LOS PRODUCTOS A UN ARRAY*/
	public function fill_products(){
		$carrito = new Carrito($this->db_link);
		$dmontos=$carrito->getDetalleGeneral(); 
		$capital_a_financiar=$dmontos['capital_a_financiar_menos_descuento'];
	 
		$items=$carrito->getListItem();    
		$caja=new Caja($this->db_link);
		$getitem=$caja->getItemListAbono();
		$monto_caja=0;

		$desc=$carrito->getDescuento(); 
		
		foreach($getitem as $key=>$val){   
			$monto_caja=$monto_caja+$val->MONTO;
		}	
		
		$des_monto=0;
		$des_porciento=0;	
		$porciento_to_monto=0;
		
		$_descuento=array();
		foreach($desc as $key => $descuento){ 
			$descuento_id=json_decode(System::getInstance()->Decrypt($descuento->descuento_id));
			$_descuento[$descuento_id->prioridad]=$descuento;
		}
		
		foreach($items as $key =>$val){ 
			$carrito->setToken($key);
			$prod= $carrito->getProducto(); 
			$plan= $carrito->getFinanciamiento(); 
			
				if (validateField($prod,"serv_codigo")){ 
					for($i=0;$i<$prod->cantidad;$i++){
						$obj= new ObjectSQL();
						$obj->serv_codigo=$prod->serv_codigo;	
						
						$obj->CODIGO_TP=$plan->codigo; 											
						$obj->no_contrato="";
						$obj->serie_contrato="";
						$obj->EM_ID="";	 
						
						$obj->precio_lista=$plan->precio;
						$precio_lista_m_descuento=$obj->precio_lista;
						$porciento_to_monto=0;
						foreach($desc as $key => $descuento){ 
							if ($descuento->type=="MONTO"){
								$ds_monto=($descuento->monto/$prod->cantidad);
								$des_monto=$ds_monto;
								$precio_lista_m_descuento=($obj->precio_lista)-$ds_monto;
								$obj->total_descuento=$obj->total_descuento+$ds_monto;
							}
							if ($descuento->type=="PORCIENTO"){
								$_monto=$precio_lista_m_descuento;
								$porciento_to_monto=$porciento_to_monto+(($_monto*$descuento->porcentaje)/100);				
								$obj->total_descuento=$obj->total_descuento+(($_monto*$descuento->porcentaje)/100);
								$precio_lista_m_descuento=$_monto-(($_monto*$descuento->porcentaje)/100);
								$obj->porc_descuento+=$descuento->porcentaje;
							}					
						}	
						$obj->monto_porc_descuento=$porciento_to_monto;																			
						$obj->precio_neto=$precio_lista_m_descuento;
						$obj->porc_enganche=$plan->por_enganche;
						$obj->porc_intereses=$plan->por_interes;
						$obj->plazo=$plan->plazo;						
						$obj->enganche=($obj->precio_neto*$obj->porc_enganche)/100;
						$obj->intereses=((($obj->precio_neto-$obj->enganche)*$obj->porc_enganche)/100)*($obj->plazo/12); 
						$obj->monto_descuento=$des_monto;															
						array_push($this->_product_list,$obj);
					}
				}		

				foreach($prod as $kprod =>$pval){  
					$obj= new ObjectSQL();
					if (validateField($pval,"bloque")){  
						$obj->id_jardin=$pval->id_jardin;
						$obj->id_fases=$pval->id_fases;
						$obj->lote=$pval->lote;
						$obj->bloque=$pval->bloque;		
						$obj->osario=$pval->osario;	 
							 
						$obj->CODIGO_TP=trim($plan->codigo);
						$obj->precio_lista=$plan->precio;
						$precio_lista_m_descuento=$obj->precio_lista;
						$porciento_to_monto=0;
						foreach($desc as $key => $descuento){ 
							if ($descuento->type=="MONTO"){
								$ds_monto=($descuento->monto/count($prod));
								$des_monto=$ds_monto;
								$precio_lista_m_descuento=($obj->precio_lista)-$ds_monto;
								$obj->total_descuento=$obj->total_descuento+$ds_monto;
							}
							if ($descuento->type=="PORCIENTO"){
								$_monto=$precio_lista_m_descuento;
								$porciento_to_monto=$porciento_to_monto+(($_monto*$descuento->porcentaje)/100);				
								$obj->total_descuento=$obj->total_descuento+(($_monto*$descuento->porcentaje)/100);
								$precio_lista_m_descuento=$_monto-(($_monto*$descuento->porcentaje)/100);
								$obj->porc_descuento+=$descuento->porcentaje;
							}					
						}	

						$obj->monto_porc_descuento=$porciento_to_monto;																			
						$obj->precio_neto=$precio_lista_m_descuento;
						$obj->porc_enganche=$plan->por_enganche;
						$obj->porc_intereses=$plan->por_interes;
						$obj->plazo=$plan->plazo;
						$obj->enganche=($obj->precio_neto*$obj->porc_enganche)/100;
						$obj->intereses=((($obj->precio_neto-$obj->enganche)*$obj->porc_enganche)/100)*($obj->plazo/12); 						
						$obj->monto_descuento=$des_monto;
						

						$obj->no_contrato="";
						$obj->serie_contrato=""; 
						array_push($this->_product_list,$obj);
							
					}   
				}
		}
	}
	  
	public function getTasaCambio($moneda){ 
		$SQL="SELECT * FROM `tasa_cambio` WHERE moneda='".$moneda."' ORDER BY indice DESC LIMIT 1";
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){ 
			$data=$row;
		} 
		return $data;
	}
	
	public function getAsesoresByProspectoIDNit($idnit){   
		$SQL="SELECT codigo_asesor as  id_comercial FROM `prospecto_comercial` WHERE id_nit='".$idnit."' "; 
		$rs=mysql_query($SQL);
		$asesor=array();
		while($row=mysql_fetch_assoc($rs)){
			$asesor=$this->formatAsesor($row['id_comercial']); 
		}
		
		return $asesor;
	}
	
	public function getInfoContrato($serie_contrato,$no_contrato){ 
		$SQL="SELECT *,
		
		TIMESTAMPDIFF(MONTH,ct.fecha_primer_pago,last_dayF) AS month_diff,	
		(ct.cuotas-ct.total_pagos_realizados) AS plazo_restante,
	ROUND((((TIMESTAMPDIFF(MONTH,ct.fecha_primer_pago,
		last_dayF)))*ct.valor_cuota),2) AS balance_deuda_actual,
    
	ROUND((((TIMESTAMPDIFF(MONTH,ct.FECHA_NEW,
		last_dayF)))*ct.valor_cuota),2) AS balance_deuda_abono 
	
		 FROM (SELECT *,
				(SELECT  MAX(mv.FECHA)
						FROM `movimiento_caja` AS mv 
						INNER JOIN `movimiento_contrato` AS mf ON (mf.`CAJA_SERIE`=mv.`SERIE` AND 
						mf.`NO_DOCTO`=mv.`NO_DOCTO`)
						WHERE mf.TIPO_MOV IN ('CAPITAL')  
						AND (mv.SERIE_DOC_ANUL IS NULL AND mv.NO_DOC_ANUL IS NULL)
						AND mv.`NO_CONTRATO`=contratos.no_contrato AND
						 mv.`SERIE_CONTRATO`=contratos.serie_contrato AND
						 mv.ANULADO='N' 
						ORDER BY mv.FECHA DESC LIMIT 1) AS FECHA_NEW,		 
				CONCAT(`sys_personas`.`primer_nombre`,' ',sys_personas.`segundo_nombre`,
				`sys_personas`.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS nombre_titular,
				valor_cuota AS cuota,
				(CASE 
					WHEN TIMESTAMPDIFF(DAY,DATE_ADD(contratos.fecha_primer_pago, INTERVAL contratos.cuotas MONTH),LAST_DAY(CURDATE()))<0 
					THEN 
						LAST_DAY(CURDATE()) 
					ELSE
						LAST_DAY(DATE_ADD(contratos.fecha_primer_pago, INTERVAL contratos.cuotas MONTH))
				  END ) AS last_dayF,
		(SELECT SUM(mf.NO_CUOTA) AS NO_CUOTA
				FROM `movimiento_caja` AS mv 
				INNER JOIN `movimiento_contrato` AS mf ON (mf.`CAJA_SERIE`=mv.`SERIE` AND 
				mf.`NO_DOCTO`=mv.`NO_DOCTO`)
				WHERE mf.TIPO_MOV IN ('CUOTA')  
				AND (mv.SERIE_DOC_ANUL IS NULL AND mv.NO_DOC_ANUL IS NULL)
				AND mv.`NO_CONTRATO`=contratos.no_contrato AND
				 mv.`SERIE_CONTRATO`=contratos.serie_contrato AND
				 mv.ANULADO='N') AS total_pagos_realizados ,
				 (SELECT (CASE 
 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) AS producto				  				
 	 		FROM 
				`contratos`  
			INNER JOIN sys_personas ON (sys_personas.id_nit=contratos.id_nit_cliente)
			WHERE  serie_contrato='".$serie_contrato."' AND no_contrato='".$no_contrato."') AS ct ";
			
 		$rs=mysql_query($SQL);
		$contrato=array();
		while($row=mysql_fetch_object($rs)){
			$contrato=$row; 
		}
		return $contrato;
	}
	
	public function getBasicInfoContrato($serie_contrato,$no_contrato){ 
		$SQL="SELECT
				 *  
 	 		FROM 
				`contratos`   
			WHERE  serie_contrato='".$serie_contrato."' AND no_contrato='".$no_contrato."' ";
 		$rs=mysql_query($SQL);
		$contrato=array();
		while($row=mysql_fetch_object($rs)){
			$contrato=$row; 
		}
		return $contrato;
	}	
	/* RETORNA EL CAPITAL ,ABONOS PAGADOS Y INTERESES*/
	public function getCapitalInteresCuotaFromContrato($serie_contrato,$no_contrato){ 
		$cn=$this->getBasicInfoContrato($serie_contrato,$no_contrato);
		$interes=$cn->interes;//(($cn->precio_neto*$cn->porc_interes)/100)*($cn->cuotas/12);
		$sqlmv="";
		$sqlmc="";
		if (isset($_SESSION['MODE_EDIT'])){
			$fecha=$_SESSION['MODE_EDIT']['FECHA'];
			$sqlmv=" and  mv.FECHA <'2015/03/04'";
			$sqlmc=" and movimiento_caja.FECHA <'2015/03/04' ";			 
		}
		
	 
		$SQL=" SELECT 	SUM(CL.interes_pagado) AS interes_pagado,
					SUM(CL.monto_pagado) AS monto_pagado,
					SUM(CL.monto_abono) AS monto_abono, 
					SUM(CL.DEBITO) AS DEBITO, 
					SUM(CL.CREDITO) AS CREDITO, 
					SUM(CL.INICIAL) AS INICIAL,  
					(SELECT
						SUM(mf.capital_pag+mf.TOT_ABONOS) AS capital_pagado
					FROM `movimiento_caja` AS mv
					INNER JOIN `movimiento_contrato` AS mf ON (mf.`CAJA_SERIE`=mv.`SERIE` AND
				mf.`NO_DOCTO`=mv.`NO_DOCTO`)
					WHERE mf.TIPO_MOV IN ('CAPITAL','ABO','CUOTA','CT','CTI')
					AND (mv.SERIE_DOC_ANUL IS NULL AND mv.NO_DOC_ANUL IS NULL)
					AND mv.`NO_CONTRATO`=CL.`NO_CONTRATO` AND
					 mv.`SERIE_CONTRATO`=CL.`SERIE_CONTRATO` AND
					 mv.ANULADO='N' ".$sqlmv." ) AS capital_pagado 
					 
				 FROM (SELECT
					movimiento_contrato.TIPO_MOV,
					SUM(movimiento_contrato.intereses_pag) AS interes_pagado,
					SUM(movimiento_contrato.CUOTA) AS monto_pagado,
					SUM(movimiento_contrato.TOT_ABONOS) AS monto_abono, 
					IF (movimiento_contrato.TIPO_MOV='ND',SUM(movimiento_contrato.TOTAL_MOV),0) AS DEBITO,
					IF (movimiento_contrato.TIPO_MOV='NC',SUM(movimiento_contrato.TOTAL_MOV),0) AS CREDITO,
					IF (movimiento_contrato.TIPO_MOV='INI',SUM(movimiento_contrato.INICIAL),0) AS INICIAL, 
					movimiento_caja.`NO_CONTRATO`,
					movimiento_caja.`SERIE_CONTRATO` 
				FROM `movimiento_caja`
				INNER JOIN `movimiento_contrato` ON (movimiento_contrato.`CAJA_SERIE`=movimiento_caja.`SERIE`
				AND movimiento_contrato.`NO_DOCTO`=`movimiento_caja`.`NO_DOCTO`)
				WHERE (movimiento_caja.SERIE_DOC_ANUL IS NULL AND movimiento_caja.NO_DOC_ANUL IS NULL)
				AND movimiento_caja.`NO_CONTRATO`='".$no_contrato."' AND
				movimiento_caja.`SERIE_CONTRATO`='".$serie_contrato."'  AND
				movimiento_caja.ANULADO='N'
				AND movimiento_contrato.TIPO_MOV IN ('ABO','CUOTA','CAPITAL','INI','NC','ND','CTI') ".$sqlmc." 
				GROUP BY movimiento_contrato.TIPO_MOV ) AS CL ";	

		$SQL="SELECT 	SUM(CL.interes_pagado) AS interes_pagado,
					SUM(CL.monto_pagado) AS monto_pagado,
					SUM(CL.monto_abono) AS monto_abono, 
					SUM(CL.DEBITO) AS DEBITO, 
					SUM(CL.CREDITO) AS CREDITO, 
					SUM(CL.INICIAL) AS INICIAL,  
					(SELECT
						SUM(mf.capital_pag+mf.TOT_ABONOS) AS capital_pagado
					FROM `movimiento_caja` AS mv
					INNER JOIN `movimiento_contrato` AS mf ON (mf.`CAJA_SERIE`=mv.`SERIE` AND
				mf.`NO_DOCTO`=mv.`NO_DOCTO`)
					WHERE mf.TIPO_MOV IN ('CAPITAL','ABO','CUOTA','CT','CTI')
					AND (mv.SERIE_DOC_ANUL IS NULL AND mv.NO_DOC_ANUL IS NULL)
					AND mv.`NO_CONTRATO`=CL.`NO_CONTRATO` AND
					 mv.`SERIE_CONTRATO`=CL.`SERIE_CONTRATO` AND
					 mv.ANULADO='N'  ".$sqlmv."  ) AS capital_pagado 
					 
				 FROM (
				SELECT 
					CTL.TIPO_MOV,
					SUM(CTL.interes_pagado) AS interes_pagado,
					SUM(CTL.monto_pagado) AS monto_pagado,
					SUM(CTL.monto_abono) AS monto_abono, 
					SUM(CTL.DEBITO) AS DEBITO,
					SUM(CTL.CREDITO) AS CREDITO,
					SUM(CTL.INICIAL) AS INICIAL, 
					CTL.`NO_CONTRATO`,
					CTL.`SERIE_CONTRATO`
				 FROM (
				 SELECT
					movimiento_contrato.TIPO_MOV,
					IF (movimiento_contrato.ID_CAMBIOS_FINANCIEROS=0,
										(movimiento_contrato.intereses_pag),0) AS interes_pagado,
					(movimiento_contrato.CUOTA) AS monto_pagado,
					(movimiento_contrato.TOT_ABONOS) AS monto_abono, 
					IF (movimiento_contrato.TIPO_MOV='ND',(movimiento_contrato.TOTAL_MOV),0) AS DEBITO,
					IF (movimiento_contrato.TIPO_MOV='NC',(movimiento_contrato.TOTAL_MOV),0) AS CREDITO,
					IF (movimiento_contrato.TIPO_MOV='INI',(movimiento_contrato.INICIAL),0) AS INICIAL, 
					movimiento_caja.`NO_CONTRATO`,
					movimiento_caja.`SERIE_CONTRATO` 
				FROM `movimiento_caja`
				INNER JOIN `movimiento_contrato` ON (movimiento_contrato.`CAJA_SERIE`=movimiento_caja.`SERIE`
				AND movimiento_contrato.`NO_DOCTO`=`movimiento_caja`.`NO_DOCTO`)
				WHERE (movimiento_caja.SERIE_DOC_ANUL IS NULL AND movimiento_caja.NO_DOC_ANUL IS NULL)
				AND movimiento_caja.`NO_CONTRATO`='".$no_contrato."' AND
				movimiento_caja.`SERIE_CONTRATO`='".$serie_contrato."'  AND
				movimiento_caja.ANULADO='N'
				AND movimiento_contrato.TIPO_MOV IN ('ABO','CUOTA','CAPITAL','INI','NC','ND','CTI') ".$sqlmc." 
				) AS CTL
				GROUP BY CTL.TIPO_MOV ) AS CL ";

 
 		$rs=mysql_query($SQL);
		$contrato=array();
	
		while($row=mysql_fetch_object($rs)){
			//$row->INICIAL=$cn->enganche; 
			//$row->capital_pagado=round($row->capital_pagado+$row->INICIAL+$row->monto_abono,2); 
                        $row->capital_pagado=round($row->capital_pagado+$row->INICIAL,2);
			$row->capital_cancelado=($row->capital_pagado+$cn->monto_capitalizado); 
			//$row->capital_pendiente=round(((($cn->precio_lista-$cn->descuento))-($row->capital_pagado+$row->monto_abono)),2);
			//$row->capital_pendiente=round(((($cn->precio_lista-$cn->descuento))-($row->capital_pagado)),2);
			$row->capital_pendiente=round(((($cn->precio_lista-$cn->descuento))-($row->capital_cancelado)),2);
			  
			//echo (($cn->precio_lista-$cn->descuento)-$row->INICIAL)."\n";
		   
		//	$row->interes_pendiente=$interes-$cn->intereses_pagados;
			$row->interes_pendiente=$interes-$row->interes_pagado;		
			
			$row->capital_total=$cn->precio_lista-$cn->descuento;
			
			//$row->interes_pagado=$cn->intereses_pagados;
			//$row->interes_pagado=$row->interes_pagado;
			$contrato=$row;   	
			 
		}   
		  
		return $contrato;
	}	
	public function getSaldoVencido($serie_contrato,$no_contrato){ 
		$SQL="SELECT 
			  SUM(IF(saldo_0_30>0,saldo_0_30,0))+
			  SUM(IF(saldo_31_60>0,saldo_31_60,0))+
			  SUM(IF(saldo_61_90>0,saldo_61_90,0))+
			  SUM(IF(saldo_91_120>0,saldo_91_120,0))+
			  SUM(IF(saldo_mas_120>0,saldo_mas_120,0))
			  AS saldo_atraso 
		FROM `cobros_contratos` WHERE no_contrato='".$no_contrato."' 
			AND serie_contrato='".$serie_contrato."'" ;
		$rs=mysql_query($SQL);
		$rt=0;
		if (mysql_num_rows($rs)>0){	
			$row=mysql_fetch_assoc($rs);
			$rt=$row['saldo_atraso'];
		}
		return $rt;
	}	
	public function getAsesores($idnit){ 
		$SQL="SELECT codigo_asesor as id_comercial FROM `sys_asesor`  WHERE id_nit='".$idnit."' " ;
		$rs=mysql_query($SQL);
		$asesor=array();
		while($row=mysql_fetch_assoc($rs)){
			$asesor=$this->formatAsesor($row['id_comercial']); 
		}
		
		return $asesor;
	}	
	/*FUNCION QUE FORMATEA EL ID COMERCIAL Y LO DIVIDE EN ASESOR 
	DIRECTOR Y GERENTE*/
	public function formatAsesor($id_comercial){
		SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
		$ase=new Asesores($this->db_link);
		$asesor=$ase->getComercialParentData($id_comercial); 
		return $asesor;
	}
	
	public function calcular_precio_neto(){
		$precio_neto=0;
		foreach($this->_product_list as $key => $val){
			$precio_neto=$precio_neto+$val->precio_neto;	
		} 
		return $precio_neto;
	}
	
	public function getTotalPorcentDescuento(){
		$tt_discount=0;
		foreach($this->_product_list as $key => $val){
			$tt_discount=$tt_discount+$val->porc_descuento;	
		}  
		return $tt_discount;
	}
	
	public function getTotalMontoDescuentos(){
		$descuento=0;
		foreach($this->_product_list as $key => $val){
			$descuento=$descuento+$val->monto_descuento;	
		} 
 		return $descuento;
	}
	
	public function getTotalEnganche(){
		$enganche=0;
		foreach($this->_product_list as $key => $val){
			$enganche=$enganche+$val->enganche;	
		} 
 		return $enganche;
	}	
	 
	public function getContractCount(){
		$SQL="SELECT COUNT(*)+1 AS total FROM `contratos`";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return zerofill($row['total'],5);
	}
	
	public function validatePersonExist($tipo="",$identifiacion){
		$SQL="SELECT sys_personas.*,
			prospectos.estatus AS pros_estatus FROM `sys_personas`
			LEFT JOIN `prospectos` ON (prospectos.id_nit=sys_personas.id_nit)
			 WHERE (sys_personas.id_nit='".mysql_real_escape_string(trim($identifiacion))."'   ";
		if (trim($tipo)!=""){
			$SQL.="  and sys_personas.id_documento='".mysql_real_escape_string(trim($tipo))."' ";
		}
		$SQL.=" ) "; 
		$rs=mysql_query($SQL);
 
		$rt=array("addnew"=>true,"personal"=>array("id_nit"=>System::getInstance()->Encrypt($row['id_nit'])));
		while($row=mysql_fetch_assoc($rs)){ 
			$row['id_nit']=System::getInstance()->Encrypt($row['id_nit']);
			$rt['addnew']=false;
			$rt['personal']=$row; 
		}
		return $rt;
	}
	
	public function getBeneficiarios($serie_contrato,$no_contrato){
		$SQL="SELECT *,
					beneficiario.id_beneficiario as beneficiario,
					DATE_FORMAT(beneficiario.fecha_nacimiento,'%d-%m-%Y') as fecha_nacimiento 
		 FROM `beneficiario` 
			INNER JOIN `tipos_parentescos` ON (tipos_parentescos.`id_parentesco`=beneficiario.id_parentesco)
		WHERE serie_contrato='".$serie_contrato."' AND no_contrato='".$no_contrato."' AND
			beneficiario.estatus=1 ";
		
		
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$row['id_parentesco']=System::getInstance()->Encrypt($row['id_parentesco']);
			$row['nit']="";
			$row['beneficiario']=System::getInstance()->Encrypt($row['beneficiario']);
			
			if ($row['id_nit']!=""){
				$row['nit']=System::getInstance()->Encrypt($row['id_nit']);
			}
			array_push($data,$row);
		}
		return $data;
	}
	
	public function getRepresentantes($serie_contrato,$no_contrato){
		$SQL="SELECT
			 `id_nit_representante` as idnit,
			 sys_parentesco.id_parentesco,
			 parentesco,
			 sys_personas.fecha_nacimiento,
	  		 CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_completo
		
		 FROM `representantes` 
INNER JOIN `contratos` ON (contratos.serie_contrato=representantes.serie_contrato AND 
contratos.no_contrato=representantes.no_contrato )
INNER JOIN `sys_parentesco` ON (`sys_parentesco`.`id_nit_parentesco`=representantes.id_nit_representante 
AND sys_parentesco.`id_nit`=contratos.`id_nit_cliente`)
INNER JOIN `sys_personas` ON (sys_personas.id_nit=representantes.`id_nit_representante`) 	
INNER JOIN `tipos_parentescos` ON (tipos_parentescos.`id_parentesco`=sys_parentesco.id_parentesco)
	WHERE representantes.serie_contrato='".$serie_contrato."' AND representantes.no_contrato='".$no_contrato."'
	and representantes.status=1
GROUP BY CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido)	 ";
 
	  
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$row['id_nit']=$row['idnit'];
			$row['idnit']=System::getInstance()->Encrypt($row['idnit']);
			$row['id_parentesco']=System::getInstance()->Encrypt($row['id_parentesco']);
			
			array_push($data,$row);
		}
		return $data;
	}
	
	public function activar(){
		$mensaje=array(
			"error"=>false,
			"mensaje"=>""
		);
		if ((validateField($this->data,"serie_contrato") 
			&& validateField($this->data,"no_contrato") 
			&& validateField($this->data,"forma_pago"))){
				
			$serie_contrato=System::getInstance()->Decrypt($this->data['serie_contrato']);
			$no_contrato=System::getInstance()->Decrypt($this->data['no_contrato']);
			$forma_pago=System::getInstance()->Decrypt($this->data['forma_pago']);

	  
			if ($this->validateAddressCobro($serie_contrato,$no_contrato)>0){ 	
				$contrato=$this->getInfoContrato($serie_contrato,$no_contrato);		
				 
				if ($contrato->estatus!="13"){
					$mensaje['error']=false;
					$mensaje['mensaje']="El contrato se encuentra generado!";
					return $mensaje;
				} 
			
				$obj= new ObjectSQL();
				$obj->estatus="1";
												
				$obj->id_nit_reiterador=UserAccess::getInstance()->getIDNIT();
				$obj->fecha_reiterado="CONCAT(CURDATE(),' ',CURRENT_TIME())";

				$obj->forpago=$forma_pago;
				$obj->setTable("contratos");
				$SQL=$obj->toSQL("update"," where serie_contrato='".$serie_contrato."' and no_contrato='".$no_contrato."' and estatus=13 ");
			 	mysql_query($SQL);

				/*ACTUALIZO EL ESTATUS DE VENTAS*/
				$obj= new ObjectSQL();
				$obj->estatus=1; 
				$obj->setTable("ventas");
				$SQL=$obj->toSQL("update"," where serie='".$serie_contrato."' and contrato='".$no_contrato."' and estatus=13 ");
			 	mysql_query($SQL);
				 
				/*proceso que actualiza el movimiento contrato con los abonos, INICIALES */
				$mov_contrato=$this->createMovContrato($serie_contrato,$no_contrato);		
				
				 
				/*Creo los asientos contables*/
				$this->MovimientoContableCreacionContrato($mov_contrato,$contrato);
					 	
				SysLog::getInstance()->Log($id_nit, 
									 $serie_contrato,
									 $no_contrato,
									 '',
									 '',
									 "CONTRATO GENERADO ".$serie_contrato." ".$no_contrato,
									 json_encode($obj),
									 'CONTRATO');		 

				$this->actualiarVenta($serie_contrato,$no_contrato);
				$mensaje['error']=true;
				$mensaje['mensaje']="Contrato generado";
			}else{
				$mensaje['error']=false;
				$mensaje['mensaje']="Debe de ingresar una direccion de cobros";	
			}
		}else{
			$mensaje['error']=false;
			$mensaje['mensaje']="Fallo al generar el Contrato " ;	
		} 
		return $mensaje;
	}
	public function removerBeneficiario($beneficiario,$serie_contrato,$no_contrato,$comentario=""){
		$mensaje=array(
			"error"=>true,
			"mensaje"=>""
		);

		$contrato=$this->getInfoContrato($serie_contrato,$no_contrato);	
 						
		if ((validateField($contrato,"serie_contrato") 
				&& validateField($contrato,"no_contrato"))){
			
			$obj= new ObjectSQL();
			$obj->estatus="2";				  
			$obj->setTable("beneficiario");
			$SQL=$obj->toSQL("update"," where serie_contrato='".$serie_contrato."' and no_contrato='".$no_contrato."' and estatus=1 AND id_beneficiario='".$beneficiario."' ");
		 
			mysql_query($SQL);
			
			SysLog::getInstance()->Log($contrato->id_nit_cliente, 
									 $contrato->serie_contrato,
									 $contrato->no_contrato,
									 '',
									 '',
									 "REMOVIENDO BENEFICIARIO ",
									 $comentario. " ". $beneficiario,
									 'ANULACION');				
			$mensaje['error']=false;
			$mensaje['mensaje']="Beneficiario removido";
		}
		return $mensaje;
	}
	public function anularSolicitud($serie_contrato,$no_contrato,$comentario=""){
		$mensaje=array(
			"error"=>false,
			"mensaje"=>""
		);

		$contrato=$this->getInfoContrato($serie_contrato,$no_contrato);								
		if ((validateField($contrato,"serie_contrato") 
			&& validateField($contrato,"no_contrato"))){
			
			$obj= new ObjectSQL();
			$obj->estatus="26";				
			$obj->id_nit_reiterador=UserAccess::getInstance()->getIDNIT();
			$obj->fecha_reiterado="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->setTable("contratos");
			$SQL=$obj->toSQL("update"," where serie_contrato='".$serie_contrato."' and no_contrato='".$no_contrato."' and estatus=13 ");
			mysql_query($SQL);

			/*ACTUALIZO EL ESTATUS DE VENTAS*/
			$obj= new ObjectSQL();
			$obj->estatus=26; 
			$obj->setTable("ventas");
			$SQL=$obj->toSQL("update"," where serie='".$serie_contrato."' and contrato='".$no_contrato."' and estatus=13 ");
			mysql_query($SQL);
			
								 
			SystemHtml::getInstance()->includeClass("caja","Caja"); 
			$caja=new Caja($this->db_link); 

			$items=$caja->getListadoRecibosPendientePorUsarOferta($serie_contrato,$no_contrato);   
			/*VALIDO SI SE PUEDE PROCESAR LA SOLICITUD*/
			if (count($items)>0){			
	
				SysLog::getInstance()->Log($contrato->id_nit_cliente, 
									 $serie_contrato,
									 $no_contrato,
									 '',
									 '',
									 "ANULACION DE SOLICITUD ".$serie_contrato." ".$no_contrato. " ".$comentario,
									 json_encode($contrato),
									 'ANULACION');		 
				foreach($items as $key=> $val){
					$mov_c= new ObjectSQL();
					$mov_c->ID_ESTATUS=36;
					$mov_c->NO_CONTRATO="";
					$mov_c->SERIE_CONTRATO="";					
					$mov_c->setTable("movimiento_caja");
					$SQL=$mov_c->toSQL("update"," where  SERIE='".mysql_real_escape_string($val->CAJA_SERIE)."'
									 and NO_DOCTO='".mysql_real_escape_string($val->CAJA_NO_DOCTO)."' 
									 and TIPO_DOC='".mysql_real_escape_string($val->TIPO_DOC)."' ");
					mysql_query($SQL);
					$mov_c= new ObjectSQL();
					$mov_c->ESTATUS=36;//UTLIZADO
					$mov_c->setTable("movimiento_factura");
					$SQL=$mov_c->toSQL("update"," where  CAJA_SERIE='".mysql_real_escape_string($val->CAJA_SERIE)."' and CAJA_NO_DOCTO='".mysql_real_escape_string($val->CAJA_NO_DOCTO)."' ");				
					mysql_query($SQL);				 
				}								
			}			
			$this->AnularOrDesistirProductos($serie_contrato,$no_contrato,26);
			$this->actualiarVenta($serie_contrato,$no_contrato);
		
			$mensaje['error']=true;
			$mensaje['mensaje']="Solicitud anulada";

		}else{
			$mensaje['error']=false;
			$mensaje['mensaje']="Fallo al generar el Contrato " ;	
		} 
		return $mensaje;
	}	
	/*REALIZA EL CAMBIO DE PRODUCTOS A UN CONTRATO*/
	public function changeProduct($serie_contrato,$no_contrato,$product,$newproduct){
		$mensaje=array(
			"valid"=>true,
			"mensaje"=>"Fallo al tratar de realizar el cambio"
		);
		if ((validateField($product,"bloque") && validateField($product,"lote")
			&& validateField($product,"id_fases") && validateField($product,"id_jardin")
			&& validateField($product,"EM_ID") && validateField($product,"serie_contrato")
			&& validateField($product,"no_contrato") && validateField($product,"serie_contrato")) &&
			
			(validateField($newproduct,"bloque") && validateField($newproduct,"lote")
			&& validateField($newproduct,"id_fases") && validateField($newproduct,"id_jardin")) ){ 
			
			if (($product->no_contrato==$no_contrato)&& ($product->serie_contrato==$serie_contrato)){
				  
				if (($this->validateProductoWithContrato($serie_contrato,$no_contrato,$product)) && ($this->checkIFproductHasNotContract($newproduct))){
 					
					/*PRODUCTO CONTRATO ACTUALIZA*/
					$pr_contrato=new ObjectSQL();
					$pr_contrato->id_jardin=$newproduct->id_jardin;
					$pr_contrato->id_fases=$newproduct->id_fases;
					$pr_contrato->lote=$newproduct->lote;
					$pr_contrato->bloque=$newproduct->bloque;
					$pr_contrato->setTable('producto_contrato');
					$SQL=$pr_contrato->toSQL("update","  WHERE `bloque`='".$product->bloque."' AND `lote` ='".$product->lote."' AND `id_fases`='".$product->id_fases."' AND `id_jardin`='".$product->id_jardin."' AND serie_contrato='".$product->serie_contrato."'  AND no_contrato='".$product->no_contrato."' ");
					mysql_query($SQL);		
 				
					/* ACTUALIZA EL NUEVO PRODUCTO CON LOS DATOS DEL ANTIGUO PRODUCTO */
					$nProduct=new ObjectSQL();
					$nProduct->serie_contrato=$product->serie_contrato;
					$nProduct->no_contrato=$product->no_contrato;
					$nProduct->contrato=$product->contrato;
					$nProduct->estatus='17'; //CONTRATO
					$nProduct->setTable('inventario_jardines');
					$SQL=$nProduct->toSQL("update","  WHERE estatus IN (3,1) and `bloque`='".$newproduct->bloque."' AND `lote` ='".$newproduct->lote."' AND `id_fases`='".$newproduct->id_fases."' AND `id_jardin`='".$newproduct->id_jardin."'  ");
					mysql_query($SQL);			
					
					/*PONGO EL PRODUCTO COMO ACTIVO PARA QUE LO PUEDAN UTILIZAR*/
					$prd=new ObjectSQL();
					$prd->serie_contrato='';
					$prd->no_contrato='';
					$prd->contrato='';
					$prd->no_reserva='';
					$prd->id_reserva='';
					$prd->estatus='1'; //CONTRATO
					$prd->setTable('inventario_jardines');
					$SQL=$prd->toSQL("update","  WHERE estatus IN (17) and `bloque`='".$product->bloque."' AND `lote` ='".$product->lote."' AND `id_fases`='".$product->id_fases."' AND `id_jardin`='".$product->id_jardin."'  ");	
					mysql_query($SQL);				
					
					$mensaje['valid']=false;
					$mensaje['mensaje']="Cambio realizado satisfactoriamente!";
					
				}else{
					$mensaje['valid']=true;
					$mensaje['mensaje']="Error productos, mal formados!";	
				}
			} 
		}else{
			$mensaje['valid']=true;
			$mensaje['mensaje']="Error faltan datos para completar la operación!";	
		}
		
		return $mensaje;
	}
	
	/*VALIDA SI UN PRODUCTO TIENE UN CONTRATO AGREGAADO*/
	private function validateProductoWithContrato($serie_contrato,$no_contrato,$product){
		$SQL="SELECT count(*) as tt FROM `inventario_jardines` WHERE `serie_contrato`='".$serie_contrato."' AND `no_contrato`='".$no_contrato."' AND `bloque`='".$product->bloque."' AND `lote` ='".$product->lote."' AND `id_fases`='".$product->id_fases."' AND `id_jardin`='".$product->id_jardin."' ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['tt']>0){
			return true;
		}else{
			return false;	
		}
	}

	/*CHEQUEA SI UN PRODUCTO NO ESTA ENLAZADO CON UN CONTRATO*/
	private function checkIFproductHasNotContract($product){
		/*VALIDA SI ESTA ACTIVO O EN RESERVA*/
		$SQL="SELECT count(*) as tt FROM `inventario_jardines` WHERE estatus IN (3,1) and `bloque`='".$product->bloque."' AND `lote` ='".$product->lote."' AND `id_fases`='".$product->id_fases."' AND `id_jardin`='".$product->id_jardin."' ";
		 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['tt']>0){
			return true;
		}else{
			return false;	
		}
	}
	
	/*MUESTRA TODOS LOS CONTRATOS DE UNA PERSONA*/
	public function getContractListFromPerson($id_nit){
		$SQL="SELECT * FROM `contratos` WHERE `id_nit_cliente`='". mysql_real_escape_string($id_nit) ."'";
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){ 
			array_push($data,$row);
		}
		return $data;
	}
	
	/*DETERMINA SI UN CONTRATO SE LE A REALIZADO UN PAGO DE INICIAL A LA RESERVA */
	public function hasPagoDeIncial($serie_contrato,$no_contrato){
		$SQL="SELECT COUNT(*) AS tt FROM `movimiento_caja` WHERE TIPO_MOV='INI' AND `NO_CONTRATO`='".$no_contrato."' AND `SERIE_CONTRATO`='".$serie_contrato."' ";
	 
		  
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);	 
		return $row['tt']>0?true:false;
	}
	/*VALIDA SI UN CONTRATO EXISTE*/
	public function contractExist($serie_contrato,$no_contrato){
		$SQL="SELECT COUNT(*) AS total FROM contratos WHERE serie_contrato='".$serie_contrato."' AND no_contrato='". $no_contrato."'";  
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);	 
		return $row['total'];
	}	
	
	/*OPTIENE UN ESTADO DE CUENTA EXTERNO*/
  	public function getEstadoCuentaExterno($serie_contrato,$no_contrato){
		$SQL="SELECT no_cuota_pagada AS no_cuota,monto,fecha_movimiento FROM `movimiento_contrato` WHERE `no_contrato`='". mysql_real_escape_string($no_contrato)."' AND `serie_contrato`='". mysql_real_escape_string($serie_contrato)."' AND tipo_movimiento IN (11)";
		 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
	
  	public function getEstadoCuentaInterno($serie_contrato,$no_contrato){
		$SQL="SELECT no_cuota_pagada AS no_cuota,monto,fecha_movimiento FROM `movimiento_contrato` WHERE `no_contrato`='". mysql_real_escape_string($no_contrato)."' AND `serie_contrato`='". mysql_real_escape_string($serie_contrato)."' AND tipo_movimiento IN (5)";
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
	
  	public function getDetalleGeneralFromContrato($serie_contrato,$no_contrato){
		$SQL="SELECT  *,sys_status.descripcion AS ESTATUS FROM `contratos` 
			INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
WHERE  contratos.`serie_contrato`='". mysql_real_escape_string($serie_contrato)."' AND contratos.`no_contrato`='". mysql_real_escape_string($no_contrato)."' ";
 
 	
		$rs=mysql_query($SQL);
		$data=array(
			'capital_cancelado'=>0,
			'capital_pendiente'=>0,
			'interes_cancelado'=>0,
			'interes_pendiente'=>0
		);
		while($row=mysql_fetch_assoc($rs)){ 
			$row['valor_cuota']=$row['valor_cuota'];
			$data=$row;
		}
		return $data;
	}
		
		
  	public function getDetalleProductsFromContrato($serie_contrato,$no_contrato){
		$SQL="SELECT producto_contrato.* 
FROM `producto_contrato`
INNER JOIN   `jardines` ON (jardines.id_jardin=producto_contrato.id_jardin)
LEFT JOIN   `jardines_activos` ON (jardines_activos.id_fases=producto_contrato.id_fases AND jardines.id_jardin=jardines_activos.id_jardin)
WHERE  producto_contrato.`serie_contrato`='". mysql_real_escape_string($serie_contrato)."' AND producto_contrato.`no_contrato`='". mysql_real_escape_string($no_contrato)."' and producto_contrato.id_estatus=1 ";
  
		$rs=mysql_query($SQL);
		$data=array( 
		); 
		 
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}	
	public function getDetalleServicioFromContrato($serie_contrato,$no_contrato){
		$SQL="SELECT 
				*,
				sys_status.`descripcion` AS estatus
			FROM `producto_contrato`
			INNER JOIN   `servicios` ON (servicios.`serv_codigo`=producto_contrato.serv_codigo)
			INNER JOIN `sys_status` ON (sys_status.`id_status`=producto_contrato.id_estatus)
			WHERE  
					producto_contrato.`serie_contrato`='". mysql_real_escape_string($serie_contrato)."' 
					AND producto_contrato.`no_contrato`='". mysql_real_escape_string($no_contrato)."' ";
 
		$rs=mysql_query($SQL);
		$data=array( 
		); 
		 
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
	 
	public function removeDocument($doc,$descripcion){
		$return=array("mensaje"=>"Error datos incompletos","valid"=>false);
	 
		if (isset($doc->no_contrato) && isset($doc->serie_contrato) && isset($doc->num_documento)
			&& isset($doc->idtipo_scan)){
			$obj= new ObjectSQL(); 
			$obj->estatus=2;
			$obj->descripcion_remove=$descripcion;
			$obj->fecha_remove="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->setTable("scan_contratos");
			$SQL=$obj->toSQL("update"," where no_contrato='".$doc->no_contrato."' and  serie_contrato='".$doc->serie_contrato."' and num_documento='".$doc->num_documento."' and idtipo_scan='".$doc->idtipo_scan."'");
	 
			mysql_query($SQL); 
		}
	
	}
	/*CARGA LA DOCUMENTACION DE UN CONTRATO*/
	public function addDocument($document){ 
		$allowed = array('png', 'jpg', 'gif','zip','pdf','docx','doc'); 
		if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){ 
			$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION); 
			if(!in_array(strtolower($extension), $allowed)){
				echo '{"status":"error"}';
				exit;
			} 
			$cc=$this->getInfoContrato(System::getInstance()->Decrypt($document['serie_contrato']),System::getInstance()->Decrypt($document['no_contrato']));
			 
			$upload_dir="temp_uploads/";
			if (!is_dir($upload_dir.$dir)) {
				mkdir($upload_dir.$dir);         
			}
			
			if(move_uploaded_file($_FILES['upl']['tmp_name'],$upload_dir.$dir."/".$_FILES['upl']['name'])){
				
				$data=array(
					"serie_contrato"=>$cc->serie_contrato,
					"no_contrato"=>$cc->no_contrato,
					"temp_path"=>$upload_dir.$dir."/".$_FILES['upl']['name'],
					"extension"=>$extension
				);
				if (!isset($_SESSION['CONTRATO_DATA']['document'])){
					$_SESSION['CONTRATO_DATA']['document']=array();
				}
				array_push($_SESSION['CONTRATO_DATA']['document'],$data);				
				//print_r($_SESSION['CONTRATO_DATA']['document']);
				echo '{"status":"success"}';
				exit;
			}else{
				echo '{"status":"fail"}';
				exit;
			}
		}		
	}	
	public function saveDocuments($tipo_scan,$empresa,$descripcion=""){
		$return=array("mensaje"=>"Error datos incompletos","valid"=>false);
		 
		 
		if (isset($_SESSION['CONTRATO_DATA']['document'])){
			foreach($_SESSION['CONTRATO_DATA']['document'] as $key =>$val){
				
				if (file_exists($val['temp_path'])){ 
					$name=$val['serie_contrato'].$val['no_contrato']; 
					$upload_dir="up_loads_contratos/";
					if (!is_dir($upload_dir.$name)) {
						mkdir($upload_dir.$name);         
					}	  
					 
					if (file_exists($val['temp_path'])){
						$rand=rand(1,99999);
						$cc=$this->getInfoContrato($val['serie_contrato'],$val['no_contrato']);
 						$path=$upload_dir.$name."/".$tipo_scan."_".$name."_".$rand.".".$val['extension']; 
						copy($val['temp_path'],$path);
						unlink($val['temp_path']);
						$obj= new ObjectSQL();
						$obj->EM_ID=$empresa;
						$obj->no_contrato=$cc->no_contrato;
						$obj->serie_contrato=$cc->serie_contrato;
						$obj->idtipo_scan=$tipo_scan;
						$obj->num_documento=$rand;
						$obj->path=$path;
						$obj->descripcion=$descripcion;
						$obj->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
						$obj->setTable("scan_contratos");
						$SQL=$obj->toSQL("insert"); 
						mysql_query($SQL);  
						$return['mensaje']="Documento agregado";
						$return['valid']=true;
					}
				}
			}	
		}
		 
		return $return;
	}
	

	/*CALULAR ABONO A CAPITAL*/
  	public function calcularAbonoACapital($serie_contrato,
										  $no_contrato,
										  $monto_abono,
										  $plazo=0,
										  $por_financiamiento=0)
										  { 
		SystemHtml::getInstance()->includeClass("financiamiento","PlanFinanciamiento");   
		
		$financiamiento=new PlanFinanciamiento($this->db_link);
		$cdata=$this->getInfoContrato($serie_contrato,$no_contrato);
		$capital_interes=$this->getCapitalInteresCuotaFromContrato($serie_contrato,$no_contrato); 
		
		$capital_sin_inicial=$capital_interes->capital_pagado-$capital_interes->INICIAL;
		$capital_mas_abono=$capital_sin_inicial+$monto_abono;
 		$monto_saldo=$capital_interes->capital_pagado;
		//$capital_interes->capital_pagado+$capital_interes->INICIAL
//		$nuevo_precio_neto=(($cdata->precio_neto-$capital_interes->capital_pagado))-$monto_abono;
//		$nuevo_precio_neto=(((($cdata->precio_lista-$cdata->descuento)-$cdata->enganche)-$capital_interes->capital_pagado))-$monto_abono;	  
		$nuevo_precio_neto=$capital_interes->capital_pendiente-$monto_abono;	  
  
	  
		if ($plazo==0){
			$plazo=$cdata->cuotas;
		}
		/*OPTENGO EL FINANCIAMIENTO ACTUAL*/
		$finac=$financiamiento->getPlazoInteresC($cdata->situacion,$cdata->EM_ID,$plazo);
		//$porc_finnaciamiento=$cdata->porc_interes;
		$nfinanciamiento=0;
		if ($cdata->tipo_moneda=="LOCAL"){
			$nfinanciamiento=$finac['interes_local'];	
		}elseif ($cdata->tipo_moneda=="DOLARES"){
			$nfinanciamiento=$finac['interes_dolares'];	
		}				
	
		if ($por_financiamiento<=0){
			$por_financiamiento=$nfinanciamiento;
		}
		 
		if ($plazo>0){
			$cdata->cuotas=$plazo;
			if ($plazo>48){
				$cdata->cuotas=48;
			}
		} 
//		echo $por_financiamiento;

			  
		$interes=((($nuevo_precio_neto*$por_financiamiento)/100)*($cdata->cuotas/12)/$cdata->cuotas);
		 
		$interes_total=((($nuevo_precio_neto*$por_financiamiento)/100)*($cdata->cuotas/12));
		
	

		$monto_cuota_sin_interes=($nuevo_precio_neto/$cdata->cuotas);
		$monto_cuotas_interes=$monto_cuota_sin_interes+$interes;
	  	 
		$ret=array(
			"precio_neto"=>$nuevo_precio_neto,
			"plazo" => $cdata->cuotas,
			"monto_cuota"=>$monto_cuotas_interes,
			"interes"=>$interes,
			"monto_interes_total"=>$interes_total,
			"mensaje"=>$mensaje,
			"capital_cuota_pagado"=>$capital_sin_inicial,
			"capital_pagado"=>$capital_mas_abono,
			"interes_pagado"=>$capital_interes->interes_pagado,
			"monto_saldo"=>$monto_saldo,
			'interes_finaciamiento'=>$por_financiamiento,
			"error"=>false
		);  
		
		
		if ($nuevo_precio_neto<=0){
			$interes=0;
			$ret['monto_saldo']=0;
			$ret['precio_neto']=0;
			$ret['monto_cuota']=0;
			$ret['interes']=0; 
			$ret['capital_pagado']=0; 
			$ret['interes_pagado']=0;  
			$ret['mensaje']="El monto a abonar no puede ser mayor que el saldo";
			$ret['error']=true;
		} 
		return $ret;
	}	

 	
	/*FUNCION UTILIZADA PARA LA IMPORTACION DE ABONO A CAPITAL*/
	public function importGestionAbonoCapital($serie_contrato,$no_contrato,$monto_abono,$plazo=0,$comentario=""){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
     	
		$cobros=new Cobros($this->db_link); 
		
		$SQL="SELECT COUNT(*) AS total FROM `solicitud_gestion` 
			WHERE serie_contrato='". mysql_real_escape_string($serie_contrato)."' AND 
				no_contrato='". mysql_real_escape_string($no_contrato)."' AND estatus=33"; 
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);

		if ($row['total']>0){
			$retrun['error']=true;
			$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";
			return $retrun;
			exit;
		}	 
		
				
		$ret=$this->calcularAbonoACapital($serie_contrato,$no_contrato,$monto_abono,$plazo); 
	 
		$cdata=$this->getInfoContrato($serie_contrato,$no_contrato);
		
		$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
		
		if (!$ret['error']){
			$gestion=$cobros->createGestion("ABCAP",$cdata->EM_ID,$no_contrato,$serie_contrato,$comentario); 	
			$abo=new ObjectSQL();
			$abo->idtipogestion=$gestion->idtipogestion;
			$abo->idgestion=$gestion->idgestion;
			$abo->no_contrato=$cdata->no_contrato;
			$abo->serie_contrato=$cdata->serie_contrato;
			$abo->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";;
			$abo->precio_lista=$cdata->precio_lista;
			$abo->precio_neto=$ret['precio_neto']; //NUEVO PRECIO NETO
			$abo->por_descuento=$cdata->por_descuento;
			$abo->descuento=$cdata->descuento;
			$abo->porc_enganche=$cdata->porc_enganche;
			$abo->enganche=$cdata->enganche;
			$abo->porc_impuesto=$cdata->porc_impuesto;
			$abo->impuesto=$cdata->impuesto;
			$abo->porc_interes=$cdata->porc_interes;
			$abo->interes=$ret['interes']*$ret['plazo'];	
			$abo->cuotas=$ret['plazo'];	
			$abo->valor_cuota=$ret['monto_cuota'];
			$abo->capital_pagado=$cdata->capital_pagado;
			$abo->interes_pagado=$cdata->intereses_pagados;
			$abo->impuesto_pagado=$cdata->impuesto_pagado;	
			$abo->tipo_cambio=$cdata->tipo_cambio;		
			$abo->capital_aplicar=$ret['capital_pagado'];		
			$abo->monto_abonar=$monto_abono;	
			$abo->monto_saldo=$ret['monto_saldo'];
			$abo->solicitado_por=UserAccess::getInstance()->getIDNIT();
			$abo->autoriza='';			
			$abo->estatus=34;		
			$abo->setTable('solicitud_gestion');
			$SQL=$abo->toSQL("insert");  
		 
			mysql_query($SQL);	
			$SOLICITUD_GESTION_ID=mysql_insert_id();
			$cdata=$this->getInfoContrato($abo->serie_contrato,$abo->no_contrato);			
	 
			$retrun['error']=false;
			$retrun['mensaje']="Solicitud realizada";
			$retrun['solicitud']=$SOLICITUD_GESTION_ID; 
		}
		
		return $retrun;
	}	
	/*crea una gestion para el abono a capital*/
	public function crearGestionAbonoCapital($serie_contrato,$no_contrato,$monto_abono,$plazo=0,$comentario=""){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
     	
		
		$cobros=new Cobros($this->db_link); 
		
		$SQL="SELECT COUNT(*) AS total FROM `solicitud_gestion` 
			WHERE serie_contrato='". mysql_real_escape_string($serie_contrato)."' AND 
				no_contrato='". mysql_real_escape_string($no_contrato)."' AND estatus=33"; 
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);

		if ($row['total']>0){
			$retrun['error']=true;
			$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";
			return $retrun;
			exit;
		}	 
		
				
		$ret=$this->calcularAbonoACapital($serie_contrato,$no_contrato,$monto_abono,$plazo); 
	 
		$cdata=$this->getInfoContrato($serie_contrato,$no_contrato);
		
		$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
		
		if (!$ret['error']){
			$gestion=$cobros->createGestion("ABCAP",$cdata->EM_ID,$no_contrato,$serie_contrato,$comentario); 	
			$abo=new ObjectSQL();
			$abo->idtipogestion=$gestion->idtipogestion;
			$abo->idgestion=$gestion->idgestion;
			$abo->no_contrato=$cdata->no_contrato;
			$abo->serie_contrato=$cdata->serie_contrato;
			$abo->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";;
			$abo->precio_lista=$cdata->precio_lista;
			$abo->precio_neto=$ret['precio_neto']; //NUEVO PRECIO NETO
			$abo->por_descuento=$cdata->por_descuento;
			$abo->descuento=$cdata->descuento;
			$abo->porc_enganche=$cdata->porc_enganche;
			$abo->enganche=$cdata->enganche;
			$abo->porc_impuesto=$cdata->porc_impuesto;
			$abo->impuesto=$cdata->impuesto;
			$abo->porc_interes=$cdata->porc_interes;
			$abo->interes=$ret['interes']*$ret['plazo'];	
			$abo->cuotas=$ret['plazo'];	
			$abo->valor_cuota=$ret['monto_cuota'];
			$abo->capital_pagado=$cdata->capital_pagado;
			$abo->interes_pagado=$cdata->intereses_pagados;
			$abo->impuesto_pagado=$cdata->impuesto_pagado;	
			$abo->tipo_cambio=$cdata->tipo_cambio;		
			$abo->capital_aplicar=$ret['capital_pagado'];		
			$abo->monto_abonar=$monto_abono;	
			$abo->monto_saldo=$ret['monto_saldo'];
			$abo->solicitado_por=UserAccess::getInstance()->getIDNIT();
			$abo->autoriza='';			
			$abo->estatus=34;		
			$abo->setTable('solicitud_gestion');
			$SQL=$abo->toSQL("insert");  
		 
			mysql_query($SQL);	
			$SOLICITUD_GESTION_ID=mysql_insert_id();
			$cdata=$this->getInfoContrato($abo->serie_contrato,$abo->no_contrato);	
			/*    
					$log_descripcion="",
					$id_nit_motorizado="",
					$no_cuota=0
			*/
			 
			$docto=MVFactura::getInstance()->doCreateDocument($cdata->id_nit_cliente,
														$cdata->EM_ID,
														$cdata->no_contrato,
														$cdata->serie_contrato,
														'CAPITAL',
														RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
														'', //$id_reserva
														'', //$no_reserva
														$monto_abono, //MONTO
														1, //tipo_cambio
														0, //descuento
														"GENERANDO RECIBO ABONO A CAPITAL",  //OBSERVACIONES
														"GENERANDO RECIBO ABONO A CAPITAL", //LOG DESCRIPCION
														'', // ID_NIT MOTORIZADO
														0, // NO CUOTA
														$SOLICITUD_GESTION_ID //SOLICITUD DE GESTION
														);			
	 
			$retrun['error']=false;
			$retrun['mensaje']="Solicitud realizada";
			$retrun['solicitud']=$SOLICITUD_GESTION_ID; 
		}
		
		return $retrun;
	}
	/*REMUEVE UNA PARCELA*/
	public function removerParcela($producto_actual,$comentario){
		SystemHtml::getInstance()->includeClass("inventario","Inventario"); 
		
		$inv= new Inventario($this->db_link); 	
		$retrun=array();
		$retrun['error']=true;
		$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";		 		

		$SQL="SELECT * FROM `producto_contrato` WHERE id='".$producto_actual->id."' and id_estatus=1 ";
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
		if (count($row)>0){
 
			$ob=new ObjectSQL();
			$ob->id_estatus=2;
			$ob->setTable("producto_contrato");
			$SQL=$ob->toSQL("update"," where id='".$row['id']."' and id_estatus=1 "); 
			mysql_query($SQL);				

			$iv= new ObjectSQL();
			$iv->id_jardin=$row['id_jardin'];
			$iv->id_fases=$row['id_fases'];
			$iv->osario=$row['osario'];
			$iv->lote=$row['lote'];
			$iv->bloque=$row['bloque'];
			$iv->descripcion=$comentario;
			$iv->creado_por=UserAccess::getInstance()->getIDNIT();
			$iv->setTable("inventario_log");
			$SQL=$iv->toSQL("insert");
			mysql_query($SQL);				
			
			$cn=$this->getInfoContrato($row['serie_contrato'],
										$row['no_contrato']);
				
			
			$inv->liberar_parecela_contrato($row); 
			SysLog::getInstance()->Log($cn->id_nit_cliente,
										$row['serie_contrato'],
										$row['no_contrato'],
										0,
										0,
										"REMOVIENDO PRODUCTO DEL CONTRATO",
										json_encode($row),
										"INFO",
										"",
										"",
										$row['id']);
			
			$retrun['error']=false;
			$retrun['mensaje']="Proceso realizado";
		}else{
			$retrun['error']=true;
			$retrun['mensaje']="Error, no se puede procesar la solicitud!";
		}
		
		return $retrun;
	}	
	/*CAMBIO DE PRODUCTO*/
	public function cambioProducto($producto_actual,$producto_nuevo,$comentario){
		SystemHtml::getInstance()->includeClass("inventario","Inventario"); 
		
		$inv= new Inventario($this->db_link); 	
		$retrun=array();
		$retrun['error']=true;
		$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";		 		

		$SQL="SELECT * FROM `producto_contrato` WHERE id='".$producto_actual->id."' and id_estatus=1 ";
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
		if (count($row)>0){
			$np= new ObjectSQL();
			$np->push($row);
			unset($np->id);
			unset($np->fecha_creacion);			
 			
			$np->id_jardin=$producto_nuevo->id_jardin;
			$np->id_fases=$producto_nuevo->id_fases;
			$np->lote=$producto_nuevo->lote;
			$np->osario=$producto_nuevo->osario;
			$np->bloque=$producto_nuevo->bloque;	
			$np->id_producto_cambio=$row['id'];	
			$np->setTable("producto_contrato");	
			$SQL=$np->toSQL("insert");
			mysql_query($SQL);				
			
			$ob=new ObjectSQL();
			$ob->id_estatus=2;
			$ob->setTable("producto_contrato");
			$SQL=$ob->toSQL("update"," where id='".$row['id']."' and id_estatus=1 ");
		 
			mysql_query($SQL);				

			$iv= new ObjectSQL();
			$iv->id_jardin=$np->id_jardin;
			$iv->id_fases=$np->id_fases;
			$iv->osario=$np->osario;
			$iv->lote=$np->lote;
			$iv->bloque=$np->bloque;
			$iv->descripcion=$comentario;
			$iv->creado_por=UserAccess::getInstance()->getIDNIT();
			$iv->setTable("inventario_log");
			$SQL=$iv->toSQL("insert");
			mysql_query($SQL);				
			
			$cn=$this->getInfoContrato($row['serie_contrato'],
										$row['no_contrato']);
				
			
			$inv->liberar_parecela_contrato($row);
			
			/*ASIGNO NUEVA PARCELA*/			
			$inv_jardines= new ObjectSQL();
			$inv_jardines->serie_contrato=$row['serie_contrato'];
			$inv_jardines->no_contrato=$row['no_contrato'];;
			$inv_jardines->estatus="17"; 
			$SQL=$inv_jardines->getSQL("update","inventario_jardines"," where 
												bloque='". mysql_escape_string($np->bloque) ."' and
												lote='". mysql_escape_string($np->lote) ."' and
												id_fases='". mysql_escape_string($np->id_fases) ."' and
												id_jardin='". mysql_escape_string($np->id_jardin) ."' and 
												osario='". mysql_escape_string($np->osario) ."' ");
			mysql_query($SQL);			
			
			SysLog::getInstance()->Log($cn->id_nit_cliente,
										$row['serie_contrato'],
										$row['no_contrato'],
										0,
										0,
										"CAMBIO DE PRODUCTO",
										json_encode($row),
										"INFO",
										"",
										"",
										$row['id']);
			
			$retrun['error']=false;
			$retrun['mensaje']="Cambio realizado";
		}else{
			$retrun['error']=true;
			$retrun['mensaje']="Error, no se puede procesar la solicitud!";
		}
		
		return $retrun;
	}

	public function asignarProducto($contrato,$producto,$comentario){
		SystemHtml::getInstance()->includeClass("inventario","Inventario"); 
		
		$inv= new Inventario($this->db_link); 	
		$retrun=array();
		$retrun['error']=true;
		$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";		 		
		$SQL="SELECT COUNT(*) AS TOTAL FROM `producto_contrato` 
				WHERE bloque='".$producto->bloque."' and
				osario='".$producto->osario."' and
				lote='".$producto->lote."' and 
				id_fases='".$producto->id_fases."' and
				id_jardin='".$producto->id_jardin."' 
				 and id_estatus=1 ";
		
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
		if ($row['TOTAL']==0){
			
			$np= new ObjectSQL();
			$np->id_jardin=$producto->id_jardin;
			$np->id_fases=$producto->id_fases;
			$np->lote=$producto->lote;
			$np->osario=$producto->osario;
			$np->bloque=$producto->bloque;	
			$np->serie_contrato=$contrato->serie_contrato;				
			$np->no_contrato=$contrato->no_contrato;							
			$np->setTable("producto_contrato");	
			$SQL=$np->toSQL("insert");
			mysql_query($SQL);
			$producto_id=mysql_insert_id();
			
			$iv= new ObjectSQL();
			$iv->id_jardin=$producto->id_jardin;
			$iv->id_fases=$producto->id_fases;
			$iv->osario=$producto->osario;
			$iv->lote=$producto->lote;
			$iv->bloque=$producto->bloque;
			$iv->descripcion=$comentario;
			$iv->creado_por=UserAccess::getInstance()->getIDNIT();
			$iv->setTable("inventario_log");
			$SQL=$iv->toSQL("insert");
			mysql_query($SQL);				
			
			$cn=$this->getInfoContrato($contrato->serie_contrato,
												$contrato->no_contrato);
				
			
			/*ASIGNO NUEVA PARCELA*/			
			$inv_jardines= new ObjectSQL();
			$inv_jardines->serie_contrato=$contrato->serie_contrato;
			$inv_jardines->no_contrato=$contrato->no_contrato;
			$inv_jardines->estatus="17"; 
			$SQL=$inv_jardines->getSQL("update","inventario_jardines"," where 
												bloque='". mysql_escape_string($producto->bloque) ."' and
												lote='". mysql_escape_string($producto->lote) ."' and
												id_fases='". mysql_escape_string($producto->id_fases) ."' and
												id_jardin='". mysql_escape_string($producto->id_jardin) ."' and 
												osario='". mysql_escape_string($producto->osario) ."' ");
			mysql_query($SQL);			
			
			SysLog::getInstance()->Log($cn->id_nit_cliente,
										$contrato->serie_contrato,
										$contrato->no_contrato,
										0,
										0,
										"ASINGACION DE PRODUCTO",
										json_encode($producto),
										"INFO",
										"",
										"",
										$producto_id);
			
			$retrun['error']=false;
			$retrun['mensaje']="Producto asignado";
		}else{
			$retrun['error']=true;
			$retrun['mensaje']="Error, no se puede procesar la solicitud!";
		}
		
		return $retrun;
	}	
	
	/*crea una gestion para el abono a saldo*/
	public function doCSolicitudAbonoSaldo($serie_contrato,$no_contrato,$monto_abono,$comentario=""){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");   
		$cobros=new Cobros($this->db_link);  
		$SQL="SELECT
				 COUNT(*) AS total FROM 
				`solicitud_gestion` 
			WHERE idtipogestion='ABSAL' and serie_contrato='". mysql_real_escape_string($serie_contrato)."' AND 
				no_contrato='". mysql_real_escape_string($no_contrato)."' AND estatus=34"; 
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
	   	
		$ret=$this->calcularAbonoACapital($serie_contrato,$no_contrato,$monto_abono,$plazo); 
		$cdata=$this->getInfoContrato($serie_contrato,$no_contrato);
		
		$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
		
		if (!$ret['error']){
			$gestion=$cobros->createGestion("ABSAL",$cdata->EM_ID,$no_contrato,$serie_contrato,$comentario); 	
			$abo=new ObjectSQL();
			$abo->idtipogestion=$gestion->idtipogestion;
			$abo->idgestion=$gestion->idgestion;
			$abo->no_contrato=$cdata->no_contrato;
			$abo->serie_contrato=$cdata->serie_contrato;
			if ($this->_fecha==""){
				$abo->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";;
			}else{
				$abo->fecha_creacion=$this->_fecha;	
			}
			$abo->precio_lista=$cdata->precio_lista;
			$abo->precio_neto=$ret['precio_neto']; //NUEVO PRECIO NETO
			$abo->por_descuento=$cdata->por_descuento;
			$abo->descuento=$cdata->descuento;
			$abo->porc_enganche=$cdata->porc_enganche;
			$abo->enganche=$cdata->enganche;
			$abo->porc_impuesto=$cdata->porc_impuesto;
			$abo->impuesto=$cdata->impuesto;
			$abo->porc_interes=$cdata->porc_interes;
			$abo->interes=$ret['interes']*$ret['plazo'];	
			$abo->cuotas=$cdata->cuotas;	
			$abo->valor_cuota=$cdata->valor_cuota;
			$abo->capital_pagado=$cdata->capital_pagado;
			$abo->interes_pagado=$cdata->intereses_pagados;
			$abo->impuesto_pagado=$cdata->impuesto_pagado;	
			$abo->tipo_cambio=$cdata->tipo_cambio;		
			$abo->capital_aplicar=$ret['capital_pagado'];		
			$abo->monto_abonar=$monto_abono;	
			$abo->monto_saldo=$ret['monto_saldo'];
			$abo->solicitado_por=UserAccess::getInstance()->getIDNIT();
			$abo->autoriza='';			
			$abo->estatus=34;		
			$abo->comentario=mysql_real_escape_string($comentario);
			$abo->setTable('solicitud_gestion');
			$SQL=$abo->toSQL("insert");  
			mysql_query($SQL);	
			$SOLICITUD_GESTION_ID=mysql_insert_id();
			return $SOLICITUD_GESTION_ID; 
		}else{
			return 0;	
		}			
	}
	/*crea una gestion para el abono a saldo*/
	public function crearGestionReactivacion($serie_contrato,$no_contrato,$penalidad,$comentario=""){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
     	
		$cobros=new Cobros($this->db_link); 
		
		$SQL="SELECT
				 COUNT(*) AS total FROM 
				`solicitud_gestion` 
			WHERE idtipogestion='REACT' and serie_contrato='". mysql_real_escape_string($serie_contrato)."' AND 
				no_contrato='". mysql_real_escape_string($no_contrato)."' AND estatus=34"; 
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
		
		if ($row['total']>0){
			$retrun['error']=true;
			$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";
			return $retrun;
			exit;
		}	 
		
		$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>''); 	
		
		$cdata=$this->getInfoContrato($serie_contrato,$no_contrato);
		$capita_interes=$this->getCapitalInteresCuotaFromContrato($serie_contrato,$no_contrato);
		$monto_saldo=$capita_interes->capital_pagado+$capita_interes->INICIAL; 		    
		
//		$gestion=$cobros->createGestion("REACT",$cdata->EM_ID,$no_contrato,$serie_contrato,$comentario); 	
		$abo=new ObjectSQL();
	//	$abo->idtipogestion=$gestion->idtipogestion;
	//	$abo->idgestion=$gestion->idgestion;
		$abo->no_contrato=$cdata->no_contrato;
		$abo->serie_contrato=$cdata->serie_contrato;
		if ($this->_fecha==""){
			$abo->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";;
		}else{
			$abo->fecha_creacion=$this->_fecha;	
		}
		$abo->precio_lista=$cdata->precio_lista;
		$abo->precio_neto=$cdata->precio_neto;
		$abo->por_descuento=$cdata->por_descuento;
		$abo->descuento=$cdata->descuento;
		$abo->porc_enganche=$cdata->porc_enganche;
		$abo->enganche=$cdata->enganche;
		$abo->porc_impuesto=$cdata->porc_impuesto;
		$abo->impuesto=$cdata->impuesto;
		$abo->porc_interes=$cdata->porc_interes;
		$abo->interes=$cdata->interes;	
		$abo->cuotas=$cdata->cuotas;	
		$abo->valor_cuota=$cdata->valor_cuota;
		$abo->capital_pagado=$cdata->capital_pagado;
		$abo->interes_pagado=$cdata->intereses_pagados;
		$abo->impuesto_pagado=$cdata->impuesto_pagado;	
		$abo->tipo_cambio=$cdata->tipo_cambio;		
		$abo->capital_aplicar=$cdata->capital_pagado;		
		$abo->monto_reactivacion=$penalidad;//$monto_saldo*$penalidad/100;
		$abo->plazo_actual=$cdata->plazo_restante;
		$abo->monto_penalidad=(($monto_saldo*30/100)/$cdata->plazo_restante);//(($penalidad/$monto_saldo)*100);
		$abo->monto_saldo=$monto_saldo; 
		$abo->solicitado_por=UserAccess::getInstance()->getIDNIT();
		$abo->autoriza='';			
		$abo->estatus=35;		
		$abo->comentario=mysql_real_escape_string($comentario);
		$abo->setTable('solicitud_gestion');
		$SQL=$abo->toSQL("insert");     
		mysql_query($SQL);	
		$SOLICITUD_GESTION_ID=mysql_insert_id();		  
		$SOLICITUD_GESTION_ID=1;
		if ($SOLICITUD_GESTION_ID>0){				
			SystemHtml::getInstance()->includeClass("cobros","Cobros"); 				
/*			$oficial=Cobros::getInstance()->getOficialFromContato($cdata->serie_contrato,$cdata->no_contrato);		
			if (count($oficial)==0){
				$oficial['nit_motorizado']=0;
				$oficial['nit_oficial']=0;		
			}	 */
			
			$ob= new ObjectSQL();
			$ob->monto_penalizacion=$abo->monto_penalidad;
			$ob->estatus=1;
			$ob->setTable("contratos");
			$SQL=$ob->toSQL("update"," WHERE  serie_contrato='".$cdata->serie_contrato."' AND 
				no_contrato='".$cdata->no_contrato."'");
			
			mysql_query($SQL);	
			
			SysLog::getInstance()->Log($cdata->id_nit_cliente, 
												 $cdata->serie_contrato,
												 $cdata->no_contrato,
												 '',
												 '',
												 "REACTIVACION",
												 json_encode($ob),
												 'INFO');	
												 
			/*INSERTO LA LABOR DE COBRO*/
			$obj= new ObjectSQL(); 
			$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->EM_ID=$cdata->EM_ID;
			$obj->no_contrato=$cdata->no_contrato;
			$obj->serie_contrato=$cdata->serie_contrato; 
			$obj->comentario_cliente=mysql_real_escape_string($comentario);
 			$obj->idaccion='REACT';   
		//	$obj->fecha_cobro="STR_TO_DATE('".$fecha_req."','%d-%m-%Y')";		
			$obj->estatus=18; 
			$obj->oficial_cobro=UserAccess::getInstance()->getIDNIT(); 
			$obj->setTable('labor_cobro');
			$SQL=$obj->toSQL("insert");  
			mysql_query($SQL);	
														 			
			/*
				$docto=MVFactura::getInstance()->doCreateDocument($cdata->id_nit_cliente,
														$cdata->EM_ID,
														$cdata->no_contrato,
														$cdata->serie_contrato,
														'ABO',
														RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
														'', //$id_reserva
														'', //$no_reserva
														$monto_abono, //MONTO
														$tasa['cambio'], //tipo_cambio
														0, //descuento
														"GENERANDO RECIBO ABONO A SALDO",  //OBSERVACIONES
														"GENERANDO RECIBO ABONO A SALDO", //LOG DESCRIPCION
														$oficial['nit_motorizado'], // ID_NIT MOTORIZADO
														0, // NO CUOTA
														$SOLICITUD_GESTION_ID, //SOLICITUD DE GESTION
														0,
														"0",
														0, //CANTIDAD DE PRODUCTO/ SERVICIO
														0, //PRECIO DEL PRODUCTO/SERVICIO 
														"",
														"",
														"",
														"",
														 0, //EN CASO DE LOS RECIBOS MANUALES															
														 $identificar,
														 0 // $oficial['nit_oficial']
														);				
 			*/
				$retrun['error']=false;
				$retrun['mensaje']="Solicitud realizada";
				$retrun['solicitud']=$SOLICITUD_GESTION_ID; 
			}else{
				$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
			}			
 
		
		return $retrun;
	}	
	/*crea una gestion para el abono a saldo*/
	public function crearGestionAbonoASaldo($serie_contrato,$no_contrato,$monto_abono,$comentario="",$identificar=0){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
     	
		$cobros=new Cobros($this->db_link); 
		
		$SQL="SELECT
				 COUNT(*) AS total FROM 
				`solicitud_gestion` 
			WHERE idtipogestion='REACT' and serie_contrato='". mysql_real_escape_string($serie_contrato)."' AND 
				no_contrato='". mysql_real_escape_string($no_contrato)."' AND estatus=34"; 
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
		
		if ($row['total']>0){
			$retrun['error']=true;
			$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";
			return $retrun;
			exit;
		}	 
				
		$ret=$this->calcularAbonoACapital($serie_contrato,$no_contrato,$monto_abono,$plazo); 
		$cdata=$this->getInfoContrato($serie_contrato,$no_contrato);
		
		$tasa=$this->getTasaCambio($cdata->tipo_moneda);

		$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
 
		//if (!$ret['error']){
		 
			$SOLICITUD_GESTION_ID=$this->doCSolicitudAbonoSaldo($serie_contrato,$no_contrato,$monto_abono,$comentario);
			
			
	//		if ($SOLICITUD_GESTION_ID>0){				
				SystemHtml::getInstance()->includeClass("cobros","Cobros"); 				
				$oficial=Cobros::getInstance()->getOficialFromContato($cdata->serie_contrato,$cdata->no_contrato);		
				if (count($oficial)==0){
					$oficial['nit_motorizado']=0;
					$oficial['nit_oficial']=0;		
				}		
				/*		
				$docto=MVFactura::getInstance()->doCreateDocument($cdata->id_nit_cliente,
															$cdata->EM_ID,
															$cdata->no_contrato,
															$cdata->serie_contrato,
															'ABO',
															RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
															'', //$id_reserva
															'', //$no_reserva
															$monto_abono, //MONTO
															$tasa['cambio'], //tipo_cambio
															0, //descuento
															"GENERANDO RECIBO ABONO A SALDO",  //OBSERVACIONES
															"GENERANDO RECIBO ABONO A SALDO", //LOG DESCRIPCION
															$oficial['nit_motorizado'], // ID_NIT MOTORIZADO
															0, // NO CUOTA
															$SOLICITUD_GESTION_ID, //SOLICITUD DE GESTION
															0,
															"0",
															0, //CANTIDAD DE PRODUCTO/ SERVICIO
															0, //PRECIO DEL PRODUCTO/SERVICIO 
															"",
															"",
															"",
															"",
														 	 0, //EN CASO DE LOS RECIBOS MANUALES															
															 $identificar,
															 $oficial['nit_oficial']
															);		
															*/				
				$docto=MVFactura::getInstance()->doCreateReciboRequerimiento($cdata->id_nit_cliente,
																				$cdata->EM_ID,
																				$cdata->no_contrato,
																				$cdata->serie_contrato,
																				'CUOTA',
																				RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
																				$oficial['nit_motorizado'],
																				$oficial['nit_oficial'],
																				$monto_abono,
																				1,
																				date("Y-m-d"),
																				"GENERANDO RECIBO PARA COBRO POR VENTANILLA",
																				$tasa['cambio'],
																				$identificar,
																				$SOLICITUD_GESTION_ID
																			);																		
	 			
				$retrun['error']=false;
				$retrun['mensaje']="Solicitud realizada";
				$retrun['solicitud']=$SOLICITUD_GESTION_ID; 
	//		}else{
	//			$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
	//		}
//		}
		
		return $retrun;
	}	 
	/*crea una gestion para el abono a saldo*/
	public function crearGestionCancelacionTotal($serie_contrato,$no_contrato,$por_descuento,$comentario=""){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
     	
		$cobros=new Cobros($this->db_link);  
		$SQL="SELECT
				 COUNT(*) AS total FROM 
				`solicitud_gestion` 
			WHERE idtipogestion='CT' and serie_contrato='". mysql_real_escape_string($serie_contrato)."' AND 
				no_contrato='". mysql_real_escape_string($no_contrato)."' AND estatus=34"; 
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
	 
		if ($row['total']>0){
			$retrun['error']=true;
			$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";
			return $retrun;
			exit;
		}	 
		
		$capita_interes=$this->getCapitalInteresCuotaFromContrato($serie_contrato,$no_contrato);	
		$capital_pendiente=$capita_interes->capital_pendiente;		 
		$cdata=$this->getInfoContrato($serie_contrato,$no_contrato);
		 
		$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
		
		if (!$ret['error']){ 
		 	$descuento=($por_descuento*$capital_pendiente)/100;
			$capital_pendiente=$capital_pendiente-$descuento;
		 
			$docto=MVFactura::getInstance()->doCreateDocument($cdata->id_nit_cliente,
															$cdata->EM_ID,
															$cdata->no_contrato,
															$cdata->serie_contrato,
															'CT',
															RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
															'', //$id_reserva
															'', //$no_reserva
															$capital_pendiente, //MONTO
															1, //tipo_cambio
															$descuento, //descuento
															"GENERANDO RECIBO CANCELACION TOTAL",  //OBSERVACIONES
															"GENERANDO RECIBO CANCELACION TOTAL", //LOG DESCRIPCION
															'', // ID_NIT MOTORIZADO
															0, // NO CUOTA
															0 //SOLICITUD DE GESTION
															);				
	 
				$retrun['error']=false;
				$retrun['mensaje']="Solicitud realizada";
				$retrun['solicitud']=$SOLICITUD_GESTION_ID; 
			
		}else{
			$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
		}
		
		return $retrun;
	}	

	/*CREAR GESTION CAMBIO DE PLAN SIN ABONO A CAPITAL*/
	public function crearGestionAbonoACapSinCambioPlan($serie_contrato,$no_contrato,$plazo,$int_financiamiento,$comentario=""){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
     	
		$cobros=new Cobros($this->db_link); 
		
		$SQL="SELECT
				 COUNT(*) AS total FROM 
				`solicitud_gestion` 
			WHERE idtipogestion='CPLAN' and serie_contrato='". mysql_real_escape_string($serie_contrato)."' AND 
				no_contrato='". mysql_real_escape_string($no_contrato)."' AND estatus=34"; 
		$rs=mysql_query($SQL);  
		$row=mysql_fetch_assoc($rs);
		
		if ($row['total']>0){
			$retrun['error']=true;
			$retrun['mensaje']="Error, Existe una solicitud pendiente de procesar";
			return $retrun;
			exit;
		}	 
				
		$ret=$this->calcularAbonoACapital($serie_contrato,$no_contrato,0,$plazo,$int_financiamiento); 
		$cdata=$this->getBasicInfoContrato($serie_contrato,$no_contrato);
 
		$retrun=array("error"=>true,"mensaje"=>"Error no se pudo procesar la orden","solicitud"=>'');
	 
		if (!$ret['error']){ 
			$gestion=$cobros->createGestion("CPLAN",$cdata->EM_ID,$no_contrato,$serie_contrato,$comentario); 	
			$abo=new ObjectSQL();
			$abo->idtipogestion=$gestion->idtipogestion;
			$abo->idgestion=$gestion->idgestion;
			$abo->no_contrato=$cdata->no_contrato;
			$abo->serie_contrato=$cdata->serie_contrato;
			$abo->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$abo->precio_lista=$cdata->precio_lista;
			$abo->precio_neto=$cdata->precio_neto; //$ret['precio_neto']; 
			$abo->por_descuento=$cdata->por_descuento;
			$abo->descuento=$cdata->descuento;
			$abo->porc_enganche=$cdata->porc_enganche;
			$abo->enganche=$cdata->enganche;
			$abo->porc_impuesto=$cdata->porc_impuesto;
			$abo->impuesto=$cdata->impuesto;
			$abo->porc_interes=$ret['interes_finaciamiento'];	
			$abo->interes=$ret['interes']*$ret['plazo'];	
			$abo->cuotas=$ret['plazo'];	
			$abo->valor_cuota=$ret['monto_cuota'];	//$cdata->valor_cuota;
			$abo->capital_pagado=$cdata->capital_pagado;
			$abo->interes_pagado=$cdata->intereses_pagados;
			$abo->impuesto_pagado=$cdata->impuesto_pagado;	
			$abo->tipo_cambio=$cdata->tipo_cambio;		
			$abo->capital_aplicar=$ret['capital_pagado'];		
			$abo->monto_abonar=0;	
			$abo->monto_saldo=$ret['monto_saldo'];
			$abo->solicitado_por=UserAccess::getInstance()->getIDNIT();
			$abo->autoriza='';			
			$abo->estatus=35;		
			$abo->comentario=mysql_real_escape_string($comentario);
			$abo->setTable('solicitud_gestion');
			$SQL=$abo->toSQL("insert");  
			mysql_query($SQL);	 
			
			
			unset($cdata->asesor);							
			unset($cdata->director);	
			unset($cdata->gerente);
			unset($cdata->cuota); 
			
			/*ALMACENO LA FOTO DEL CONTRATO ANTES DE REALIZARLE LOS CAMBIOS FINANCIEROS*/
			$cf= new ObjectSQL();
			$cf->push($cdata);
			$cf->valor_reconocimiento=0;
			$cf->setTable("cambios_financieros");
			$SQL=$cf->toSQL("insert");	
		 	mysql_query($SQL);	
			$id_cambios_financieros=mysql_insert_id();	
			
			/*ACTUALIZO LOS MOVIMIENTOS PAGADOS ANTES DE LOS CAMBIOS EN EL CONTRATO*/
			$movc= new ObjectSQL();
			$movc->ID_CAMBIOS_FINANCIEROS=$id_cambios_financieros;
			$movc->setTable("movimiento_caja");
			$SQL=$movc->toSQL("update"," where serie_contrato='".$cdata->serie_contrato."' 
										and no_contrato='".$cdata->no_contrato."' and ID_CAMBIOS_FINANCIEROS=0 ");	
			mysql_query($SQL);	

			
			/*Actualizo mi contrato a la nueva informacion*/
			$cn= new ObjectSQL();
			$cn->push($cdata); 
			$cn->precio_neto=$abo->precio_neto;
			$cn->cuotas=$abo->cuotas;
			$cn->interes=$abo->interes;
			$cn->valor_cuota=$abo->valor_cuota;
			$cn->porc_interes=$abo->porc_interes;
			$cn->setTable("contratos");
			$SQL=$cn->toSQL("update"," where serie_contrato='".$cdata->serie_contrato."' 
										and no_contrato='".$cdata->no_contrato."'");	
			mysql_query($SQL);			
		  
			$retrun['error']=false;
			$retrun['mensaje']="Solicitud Aplicada!";
			$retrun['solicitud']=$SOLICITUD_GESTION_ID; 
 
		}
		
		return $retrun;
	}	
	/*CAMBIAR DE METODO DE COBRO*/
	function cambiar_metodo_cobro($serie_contrato,$no_contrato,$forma_pago){
		$mensaje=array();
		
		$SQL="SELECT COUNT(*) AS total FROM `contratos_metodo_cobro` 
			WHERE cmc_codigo='". mysql_escape_string($forma_pago) ."'";
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['total']==0){
			$mensaje['valid']=false;
			$mensaje['mensaje']="Forma de pago no existe!";
			return $mensaje;
		}
 		$contrato=$this->getInfoContrato($serie_contrato,$no_contrato);		
		
		if (!isset($contrato->estatus)){
			$mensaje['valid']=false;
			$mensaje['mensaje']="Error contrato no existe!";
			return $mensaje;
		}  
		$obj= new ObjectSQL();
		$obj->forpago=$forma_pago;
		$obj->setTable("contratos");
		$SQL=$obj->toSQL("update"," where serie_contrato='".$serie_contrato."' and no_contrato='".$no_contrato."'");
		mysql_query($SQL); 
		
		
		$mensaje['valid']=true;
		$mensaje['mensaje']="Datos actualizados!";
		SysLog::getInstance()->Log($contrato->id_nit_cliente, 
											 $serie_contrato,
											 $no_contrato,
											 '',
											 '',
											 "CAMBIO DE FORMA DE PAGO",
											 json_encode($contrato),
											 'INFO');	 		
		return $mensaje;		
	}	
	
	/* GUARDAR TARJETA DE CREDITO*/
	function guardar_tarjeta_de_credito($serie_contrato,
										$no_contrato,
										$tipo_tarjeta,
										$numero_tarjeta,
										$cvv_metodo,
										$month_venc,
										$year_venc,
										$dias_debito,
										$comentario=""){
		$mensaje=array();
		$mensaje['valid']=false;
		$mensaje['mensaje']="Error contrato no existe!";
			  
 		$ct=$this->getInfoContrato($serie_contrato,$no_contrato);		
		
		if (!isset($ct->estatus)){
			$mensaje['valid']=false;
			$mensaje['mensaje']="Error contrato no existe!";
			return $mensaje;
		}  	
		
		$key_code= rand(1000,1000000);
		
		$card_number=System::getInstance()->getEncrypt()->encrypt($numero_tarjeta,$key_code);
		$obj= new ObjectSQL();
		$obj->id_nit=$ct->id_nit_cliente;
		$obj->numero_tarjeta=$card_number;
		$obj->tjt_ulitmos_cuatros_digitos=substr($numero_tarjeta,strlen($numero_tarjeta)-4,strlen($numero_tarjeta));
		$obj->tipo_tarjeta=$tipo_tarjeta;
		$obj->cvv=$cvv_metodo;
		$obj->fecha_venc=$year_venc."-".$month_venc;
 		$obj->fecha_registro="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$obj->registrado_por=UserAccess::getInstance()->getIDNIT();
		$obj->comentario=$comentario;
		$obj->key_encode=$key_code;
		$obj->setTable("contratos_tarjetas_debito");
		$SQL=$obj->toSQL("insert");
		mysql_query($SQL);  
		
		if (($id=mysql_insert_id())>0){
			/*DESACTIVO LAS ASOCIACIONES A TARJETAS VIEJAS*/
			$SQL="update contratos_debito_automatico set estatus=2 where no_contrato='".$no_contrato."' and
			serie_contrato='".$serie_contrato."'";
			mysql_query($SQL);
			
			$obj= new ObjectSQL();
			$obj->serie_contrato=$serie_contrato;
			$obj->no_contrato=$no_contrato;
			$obj->dia_debito=$dias_debito;
			$obj->contratos_tarjetas_debito_id=$id;
	 		$obj->setTable("contratos_debito_automatico");
			$SQL=$obj->toSQL("insert");
			mysql_query($SQL);  
			
			$mensaje['valid']=true;
			$mensaje['mensaje']="Datos actualizados!";
			SysLog::getInstance()->Log($ct->id_nit_cliente, 
										 $serie_contrato,
										 $no_contrato,
										 '',
										 '',
										 "CAMBIO DE FORMA DE PAGO",
										 json_encode($contrato),
										 'INFO');	 	
		}
		return $mensaje;		
	}		
}
?>
