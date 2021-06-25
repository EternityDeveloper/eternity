<?php


class Prospectos{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
	private $_version='ProspectoV1';
	
  
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}

	/*CREA UNA PROSPECTACION DE MANERA DIRECTA*/
	public function createProspectacionDirect(){
		$pilar_origen=json_decode(System::getInstance()->Decrypt($_REQUEST['pilar_origen']));
		$pilar_final=json_decode(System::getInstance()->Decrypt($_REQUEST['pilar_final']));
		$person_data=$_REQUEST['person_data'];
		$asesor_data=$_REQUEST['asesor_data'];
 
		$estatus=5; //Estatus protegido
		$observacion=$_REQUEST['observacion']; 
		$proceder=false; 
		if (!isset($person_data['form_complete'])){
			$proceder=false;
		}else if ($person_data['form_complete']){
			$proceder=true;
		}
		
		if (!isset($tipo_prospecto['form_complete'])){
			$proceder=false;
		}else if ($tipo_prospecto['form_complete']){
			$proceder=true;
		}
		
		if (!isset($asesor_data['form_complete'])){
			$proceder=false;
		}else if ($asesor_data['form_complete']){
			$proceder=true;
		}
		
		if ($proceder){
	
			$asesor=json_decode(System::getInstance()->Decrypt($asesor_data['data']['code']));
			$person_id=System::getInstance()->Decrypt($person_data['data']['person_id']);
			 
			if (!isset($asesor->id_comercial)){
				$retur=array("mensaje"=>"El codigo de asesor es no es valido","error"=>false);
				return $retur;
			}  
			if ($asesor->id_comercial<=0){ 
				$retur=array("mensaje"=>"El codigo de asesor es no es valido","error"=>false);
				return $retur;
			}
			if ($this->prospectExist($person_id)>0){ 
				$prop=$this->getProspecto($person_id);   
			  	$this->remove(System::getInstance()->Encrypt($prop['correlativo']),"Removido por AUDITORIA");				
			}  	
			$id_comercial=$asesor->id_comercial;
			$nit_comercial=$asesor->idnit;
 			/*AGREGANDO EN LA TABLA DE PROSPECTO AL POSIBLE CLIENTE*/
			$prospectos= new ObjectSQL();
			$prospectos->id_nit=$person_id;
			$prospectos->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";  
			$prospectos->estatus=$estatus;
			$prospectos->observaciones=$observacion;
			$prospectos->tipo_prospecto='C';
                        $prospectos->pilar_final=' ';
                        $prospectos->fecha="now()";
                        $prospectos->fecha_fin="0000-00-00";
  
			$SQL=$prospectos->getSQL("insert","prospectos");
                   
			if (count($prop)==0){
		 		mysql_query($SQL);	
			}
			/////////////////////////////////////////////////////////
		 
			$prospectos_comercial= new ObjectSQL();
			$prospectos_comercial->id_nit=$person_id;
                        $prospectos_comercial->id_comercial=$id_comercial;			
			$prospectos_comercial->codigo_asesor=$id_comercial;
			$prospectos_comercial->pilar_inicial=$pilar_origen->idtipo_pilar;
			$prospectos_comercial->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";;
			$prospectos_comercial->fecha_fin="DATE_ADD(CONCAT(CURDATE(),' ',CURRENT_TIME()), INTERVAL ".$pilar_origen->dias_proteccion." DAY)";
			$prospectos_comercial->estatus=$estatus;

			$SQL=$prospectos_comercial->getSQL("insert","prospecto_comercial");  
		 	mysql_query($SQL);
		
			$comercial_correlativo=mysql_insert_id($this->db_link->link_id);
			
			$track = new ObjectSQL();
			$track->correlativo=$comercial_correlativo; 
			$track->id_nit=$person_id;
			$track->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$track->fecha_inicio_cliente="curdate()";
			$track->fecha_proxima="curdate()";
			$track->id_actividad="LLA";
                        $trak->actividad_proxima=" ";
			$track->detalle_actividad="PROCESO CREACION AUDITORIA";
			$track->hora="00:00:00";
			$track->lugar="";
			$track->apoyo=0;
			$SQL=$track->getSQL("insert","tracking_prospecto");   
			
		 	mysql_query($SQL);  
			$last_tracking_prospecto_id=mysql_insert_id();

			$track = new ObjectSQL();
			$track->correlativo=$comercial_correlativo; 
			$track->id_nit=$person_id;
			$track->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$track->fecha_inicio_cliente="curdate()";
			$track->fecha_proxima="curdate()";
			$track->id_actividad="CIE";
                        $trak->actividad_proxima=" ";
			$track->detalle_actividad="PROCESO CREACION AUDITORIA";
			$track->hora="00:00:00";
			$track->lugar="";
			$track->apoyo=0;
			$SQL=$track->getSQL("insert","tracking_prospecto");   
			mysql_query($SQL);
			
 		
			$obj = new ObjectSQL();
			$obj->estatus="6";
			$obj->last_tracking_prospecto_id=$last_tracking_prospecto_id;
			//print_r($obj);
			$SQL=$obj->getSQL("update","prospecto_comercial"," where id_nit='". mysql_real_escape_string($person_id) ."' AND correlativo='".$comercial_correlativo."'");	 
		 	mysql_query($SQL);				
			  
			$prospectos= new ObjectSQL();
			$prospectos->estatus=6;
			$prospectos->pilar_final=$pilar_final->idtipo_pilar;
			$SQL=$prospectos->getSQL("update","prospectos"," where id_nit='". mysql_real_escape_string($person_id) ."'  ");   			mysql_query($SQL);	 
			
			  
		 	
			$this->doCreateReportCache($prospectos_comercial->pilar_inicial,
										$id_comercial,
										$person_id,
										$comercial_correlativo);   									
													
			$retur=array("mensaje"=>"Registro agregado","error"=>false);
			return $retur;
		 
		}	
	}	
	public function createProspectacion(){
		
		$tipo_prospecto=$_REQUEST['tipo_prospecto'];
		$person_data=$_REQUEST['person_data'];
		$asesor_data=$_REQUEST['asesor_data'];
		
		
		$estatus=5; //Estatus protegido
		$observacion=$_REQUEST['observacion'];
		
		$proceder=false;
		 
		if (!isset($person_data['form_complete'])){
			$proceder=false;
		}else if ($person_data['form_complete']){
			$proceder=true;
		}
		
		if (!isset($tipo_prospecto['form_complete'])){
			$proceder=false;
		}else if ($tipo_prospecto['form_complete']){
			$proceder=true;
		}
		
		if (!isset($asesor_data['form_complete'])){
			$proceder=false;
		}else if ($asesor_data['form_complete']){
			$proceder=true;
		}
		
		if ($proceder){
			
			$asesor=json_decode(System::getInstance()->Decrypt($asesor_data['data']['code']));
			$person_id=System::getInstance()->Decrypt($person_data['data']['person_id']);
			 
			if (!isset($asesor->id_comercial)){
				$retur=array("mensaje"=>"El codigo de asesor es no es valido","error"=>false);
				return $retur;
			}  
			if ($asesor->id_comercial<=0){ 
				$retur=array("mensaje"=>"El codigo de asesor es no es valido","error"=>false);
				return $retur;
			}
			 
			if ($this->prospectExist($person_id)>0){ 
				$retur=array("mensaje"=>"Error el prospecto existe no se aceptan duplicados!","error"=>true);
				return $retur;
			}else{
			
				$id_comercial=$asesor->id_comercial;
				$nit_comercial=$asesor->idnit;
				$desc_prospecto=json_decode(System::getInstance()->Decrypt($tipo_prospecto['tipos_prospectos']));
				
				/*AGREGANDO EN LA TABLA DE PROSPECTO AL POSIBLE CLIENTE*/
				$prospectos= new ObjectSQL();
				$prospectos->id_nit=$person_id;
				$prospectos->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";  
				$prospectos->estatus=$estatus;
				$prospectos->observaciones=$observacion;
				$prospectos->tipo_prospecto='C';
                                $prospectos->pilar_final= ' ';
                                $prospectos->fecha= "now()";
                                $prospectos->fecha_fin="0000-00-00";
				$SQL=$prospectos->getSQL("insert","prospectos"); 
				mysql_query($SQL);
				/////////////////////////////////////////////////////////
			
				
				$prospectos_comercial= new ObjectSQL();
				$prospectos_comercial->id_nit=$person_id;			
				$prospectos_comercial->codigo_asesor=$id_comercial;
 				$prospectos_comercial->pilar_inicial=$desc_prospecto->idtipo_pilar;
				$prospectos_comercial->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";;
				$prospectos_comercial->fecha_fin="DATE_ADD(CONCAT(CURDATE(),' ',CURRENT_TIME()), INTERVAL ".$desc_prospecto->dias_proteccion." DAY)";
				$prospectos_comercial->estatus=$estatus;
                                $prospectos_comercial->id_comercial=$id_comercial;
	
				$SQL=$prospectos_comercial->getSQL("insert","prospecto_comercial"); 
	 
				mysql_query($SQL);
				
				$comercial_correlativo=mysql_insert_id($this->db_link->link_id);
				
	
				
				$this->doCreateReportCache($prospectos_comercial->pilar_inicial,
											$id_comercial,
											$person_id,
											$comercial_correlativo);	
																	
				
				/*AGREGANDO EL DETALLE DE LA PROSPECTACION*/
				if (isset($tipo_prospecto['data'])){
					foreach($tipo_prospecto['data'] as $key =>$val){
						$obj=json_decode(System::getInstance()->Decrypt($val['name']));
						$value=$val['value'];
						
						/*SI ES IGUAL A VALORES ENTONCES DESENCRIPTAR EL VALOR*/
						if ($obj->type=="valores"){
							$info=json_decode(System::getInstance()->Decrypt($val['value']));
							$value=$info->value;
						}
						if ($obj->type=="boolean"){
							if ($value=="1"){
								$value="SI";
							}else{
								$value="NO";
							}
						}
						
						$questions= new ObjectSQL();
						$questions->comercial_correlativo=$comercial_correlativo;
						$questions->idtipo_pilar=$desc_prospecto->idtipo_pilar;
						$questions->id_nit=$person_id;
						$questions->id_pregunta=$obj->id;
						$questions->respuesta=$value; 
						$questions->observaciones="";
						$SQL=$questions->getSQL("insert","detalle_prospecto"); 
						mysql_query($SQL);
					 
					}
				}
				
				
				
				$retur=array("mensaje"=>"Registro agregado desde ventas-todos","error"=>false);
				return $retur;
			}
		}	
	}
	
	public function doCreateReportCache($pilar,
										$id_comercial,
										$person_id,
										$comercial_correlativo,
										$pilar_final="",
										$actividad=""){
 
		 
		$SQL=" SELECT 
				prospectos.id_nit, 
				prospectos.estatus,
			 (SELECT sys_clasificacion_persona.`descripcion` FROM sys_clasificacion_persona 
			 WHERE `sys_clasificacion_persona`.`id_clasificacion`=sp.id_clasificacion) AS clasificacion,
			 (SELECT COUNT(*) FROM `sys_direcciones` WHERE sys_direcciones.id_nit=sp.id_nit )  AS direccion,
			 (SELECT COUNT(*) FROM `sys_telefonos` WHERE sys_telefonos.id_nit=sp.id_nit )  AS telefono
			 FROM `sys_personas` AS sp
			INNER JOIN `prospectos` ON (`prospectos`.id_nit=sp.id_nit)
			WHERE sp.id_nit='".$person_id."'";
		$rs=mysql_query($SQL);
		
		$clasificacion="C";
		/*ESTA VARIABLE ES LLENADA EN CASO DE HABER UN CIERRE, PRESENTACION, RESERVA O CITA */
		$tipo_act=""; 
		//////////////////////////////////////////
		while($row= mysql_fetch_assoc($rs)){ 
			$ob= new ObjectSQL();
			$ob->id_clasificacion=5;
			if ($row['direccion']>0 && $row['telefono']>0){
				$ob->id_clasificacion=4;
				$clasificacion="B";
			}
			if ($row['estatus']=="6"){
				$ob->id_clasificacion=3;
				$clasificacion="A";
				$tipo_act="CIE";
			}else if ($actividad!=""){
				$tipo_act=$actividad;
			} 
			$ob->setTable("sys_personas"); 
			$SQL=$ob->toSQL("update"," where id_nit='".$person_id."'"); 
			mysql_query($SQL); 
		}	
	 		
		 
		$SQL="SELECT
  `tipos_pilares`.`dscrip_tipopilar`    AS `dscrip_tipopilar`,
  `prospecto_comercial`.`pilar_inicial` AS `pilar_inicial`,
  `prospectos`.`pilar_final`            AS `pilar_final`,
  `sys_status`.`descripcion`            AS `estatus`,
  `sys_status`.`id_status`              AS `id_status`,
  `tipos_pilares`.`dias_proteccion`     AS `dias_proteccion`,
  `prospectos`.`observaciones`          AS `observaciones`,
  (SELECT
     `sys_clasificacion_persona`.`descripcion`
   FROM `sys_clasificacion_persona`
   WHERE (`sys_clasificacion_persona`.`id_clasificacion` = `sys_personas`.`id_clasificacion`)) AS `clasificacion_cliente`,
  `prospecto_comercial`.`codigo_asesor`  AS `codigo_asesor`,
  `prospecto_comercial`.`correlativo`   AS `correlativo`,
  `prospecto_comercial`.`id_nit`        AS `id_nit`,
  CONCAT(`sys_personas`.`primer_nombre`,' ',`sys_personas`.`segundo_nombre`,' ',`sys_personas`.`primer_apellido`,' ',`sys_personas`.`segundo_apellido`) AS `nombre_completo`,
  `prospecto_comercial`.`fecha_inicio`  AS `fecha_inicio`,
  `prospecto_comercial`.`fecha_fin`     AS `fecha_fin`,
  `prospecto_comercial`.`fecha_inicio`  AS `format_fecha_inicio`,
  `prospecto_comercial`.`fecha_fin`     AS `format_fecha_fin`,
  (TO_DAYS(`prospecto_comercial`.`fecha_fin`) - TO_DAYS(CURDATE())) AS `TIME_TO_END`,
  (SELECT
     CONCAT(`asesor`.`primer_nombre`,' ',`asesor`.`segundo_nombre`,' ',`asesor`.`primer_apellido`,' ',`asesor`.`segundo_apellido`) AS `nombre_asesor`
   FROM (`sys_asesor`
      JOIN `sys_personas` `asesor`
        ON ((`sys_asesor`.`id_nit` = `asesor`.`id_nit`)))
   WHERE  `sys_asesor`.`codigo_asesor`  = `prospecto_comercial`.`codigo_asesor`) AS `ASESOR`,
  (SELECT
     `sys_asesor`.`codigo_gerente_grupo`        AS `codigo_gerente`
   FROM (`sys_asesor`
      JOIN `sys_personas` `asesor`
        ON ((`sys_asesor`.`id_nit` = `asesor`.`id_nit`)))
   WHERE `sys_asesor`.`codigo_asesor`  = `prospecto_comercial`.`codigo_asesor`) AS `codigo_gerente`,
   
`tklp`.`id_actividad` AS `ultima_actividad`,
`tklp`.`fecha_ingreso`  AS `fecha_ultima_actividad`,
`tklp`.`detalle_actividad` AS `descrip_actividad`  
FROM (((((`prospectos`
      JOIN `prospecto_comercial`
        ON ((`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)))
     JOIN `tipos_pilares`
       ON ((`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)))
    JOIN `sys_status`
      ON ((`prospecto_comercial`.`estatus` = `sys_status`.`id_status`)))
   JOIN `sys_personas`
     ON ((`prospecto_comercial`.`id_nit` = CONVERT(`sys_personas`.`id_nit` USING utf8))))
    LEFT JOIN `tracking_prospecto` AS tklp  ON ((tklp.id=prospecto_comercial.`last_tracking_prospecto_id`)))
WHERE 
	prospecto_comercial.correlativo='".$comercial_correlativo."'";
	
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){ 
 		 
			$ob= new ObjectSQL();
			$ob->push($row);
			$ob->setTable("cache_listado_prospectos"); 
			
			$ob->A=0;
			$ob->B=0;
			$ob->C=0;
			 
			if ($tipo_act!=""){ 
				switch($tipo_act){
					case "PRE":
						$ob->PRESENTACION=1;
						$clasificacion="A";
						$ob->CITA=0;
 						$ob->CIERRE=0;
						$ob->RESERVAS=0; 						
					break;	
					case "CITA":
						$clasificacion="A";
						$ob->CITA=1;
 						$ob->PRESENTACION=0;
						$ob->CIERRE=0;
						$ob->RESERVAS=0; 		 
					break;	
					case "CIE":
						$ob->CIERRE=1;
						$ob->CITA=0;
						$ob->PRESENTACION=0;
 						$ob->RESERVAS=0; 	 
						$clasificacion="A";
					break;	
					case "RES":
						$ob->RESERVAS=1;
						$ob->CITA=0;
						$ob->PRESENTACION=0;
						$ob->CIERRE=0;						
						$clasificacion="A";
					break; 	
					case "VSP":
						$ob->RESERVAS=0;
						$ob->CITA=0;
						$ob->PRESENTACION=0;
						$ob->CIERRE=0;						
						$clasificacion="A";
					break; 														
				} 
			}
			 
			if ($pilar_final!=""){
				$ob->pilar_final=$pilar_final;
			}
			if ($clasificacion!=""){
				$ob->{$clasificacion}=1;
				$ob->clasificacion_cliente=$clasificacion;
			}		

 			$SQL="SELECT COUNT(*) AS total FROM cache_listado_prospectos 
					WHERE  id_nit='".$person_id."' and correlativo='".$comercial_correlativo."'";
  			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);

			if ($row['total']==0){		
				$SQL=$ob->toSQL("insert"); 	
				$prueba = mysql_query($SQL);
                                // if (!$prueba) {
                                    $message['mensaje']='error al grabar'.mysql_error().'Query'.$SQL;
                                    $message['error']=true;
                                   
                                 // } 
			}else{
				$SQL=$ob->toSQL("update","  where correlativo='".$comercial_correlativo."' 
							and id_nit='".$person_id."' "); 	
			 
				mysql_query($SQL);
   
                                   
			}
		 
			  
		}
  
	}
 
	public function registrarActividad(){
		$retur=array("mensaje"=>"Error agregado","error"=>true);
			
		if (isset($_REQUEST['data'])){
			$actividad=$_REQUEST['data'];
			$prospecto= json_decode(System::getInstance()->Decrypt($_REQUEST['prospecto_id']));
		    $datos_prospecto=$this->getDataProspecto($prospecto->id_nit,$prospecto->correlativo);
 			
		 
		 	if (isset($actividad['pilar'])){
				 			 
				/*SI ES CIERRE ENTONCES ACTUALIZO EL ULTIMO PILAR*/
				if ($actividad['estatus']=="6"){
					$actividad['id_actividad']="CIE";
					
					$pilar=json_decode(System::getInstance()->Decrypt($actividad['pilar']));
					
					$obj = new ObjectSQL();
					$obj->pilar_final=$pilar->idtipo_pilar;
					$obj->fecha="CURDATE()";
					$obj->fecha_fin="CURDATE()";
					$obj->estatus="6";
					//print_r($obj);
					$SQL=$obj->getSQL("update","prospectos"," where id_nit='". mysql_real_escape_string($prospecto->id_nit) ."' ");	 
					@mysql_query($SQL);

					$obj = new ObjectSQL();
					$obj->estatus="6";
					//print_r($obj);
					$SQL=$obj->getSQL("update","prospecto_comercial"," where id_nit='". mysql_real_escape_string($prospecto->id_nit) ."' AND correlativo='".$prospecto->correlativo."'");	 
					@mysql_query($SQL);
 
				  
				}			
				 
				if ($actividad['estatus']=="7"){
					$actividad['id_actividad']="RES";
				}
				 
				if ($actividad['id_actividad']=="CITA"){
					SystemHtml::getInstance()->includeClass("client","PersonalData");
					$person= new PersonalData(UserAccess::getInstance()->getDBLink());
					/*CONVIERTO UN CLIENTE EN TIPO A = 3*/
					$person->updateTipoClasificacion(3,$prospecto->id_nit);	
				} 
				 
											
				$last_actividad=$this->getLastActividad($prospecto->id_nit,$prospecto->correlativo);
				
				/*Si existe alguna actividad entonces actualizar*/
				if (count($last_actividad)>0){
					$obj = new ObjectSQL();
					$obj->actividad_proxima = $actividad['id_actividad'];
					$SQL=$obj->getSQL("update","tracking_prospecto"," where 
									correlativo='". mysql_real_escape_string($last_actividad['correlativo']) ."' 
								AND	fecha_ingreso='". mysql_real_escape_string($last_actividad['fecha_ingreso']) ."'
								AND	fecha_inicio_cliente='". mysql_real_escape_string($last_actividad['fecha_inicio_cliente']) ."'
								AND	id_actividad='". mysql_real_escape_string($last_actividad['id_actividad']) ."'  ");
		 
					 @mysql_query($SQL);
		 
				}  
				
				$track = new ObjectSQL();
				$track->correlativo=$prospecto->correlativo; 
				$track->id_nit=$prospecto->id_nit;
				$track->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";
				$track->fecha_inicio_cliente="STR_TO_DATE('".$actividad['fecha']."','%d-%m-%Y')";
				$track->fecha_proxima="STR_TO_DATE('".$actividad['fecha']."','%d-%m-%Y')";
				$track->id_actividad=$actividad['id_actividad'];
				$track->detalle_actividad=$actividad['detalle'];
				$track->hora=$actividad['hora'];
				$track->lugar=$actividad['lugar'];
                                $track->actividad_proxima=" ";
				$track->apoyo=$actividad['is_apoyo']=="true"?1:0;
				$SQL=$track->getSQL("insert","tracking_prospecto"); 
 			
				mysql_query($SQL);
				$last_tracking_prospecto_id=mysql_insert_id();
				
				/*SI ACTUALIZO LA UTLIMA ACTIVIDAD EN EL PROSPECTO COMERCIAL
				PARA PODER CONTEMPLARLO EN EL REPORTE */
				if ($last_tracking_prospecto_id>0){
					$pr = new ObjectSQL(); 
					$pr->last_tracking_prospecto_id=$last_tracking_prospecto_id;
					$SQL=$pr->getSQL("update","prospecto_comercial"," 
							WHERE id_nit='".$prospecto->id_nit."' AND
							 correlativo='".$prospecto->correlativo."' "); 
					mysql_query($SQL);
				} 	 

				/*VERIFICO QUE EL PROSPECTO NO SE ESTE VENCIENDO Y SI ES MENOR A DOS DIAS ENTONCES SE LE DA
				15 DIAS MAS DE GRACIA */
				if (2>=$datos_prospecto['dias_fin']){
					$pr = new ObjectSQL(); 
					$pr->fecha_fin=$datos_prospecto['grace_day']; 	
					$SQL=$pr->getSQL("update","prospecto_comercial"," WHERE id_nit='".$prospecto->id_nit."' AND correlativo='".$prospecto->correlativo."' "); 
					mysql_query($SQL);
			 
				}
					 
	 
				$this->doCreateReportCache($prospecto->pilar_inicial,
											$prospecto->codigo_asesor,
											$prospecto->id_nit,
											$prospecto->correlativo,
											$pilar->idtipo_pilar,
											$actividad['id_actividad']);	
												 
 
											
				$retur['mensaje']='Actividad registrada';
				$retur['error']=false;
			}
		}
		
		return $retur;
	}
	
	public function getTotalProspecto(){
		$SQL="SELECT (COUNT(*)+1) AS total FROM `prospectos`";
		$rs=mysql_query($SQL);
		$row= mysql_fetch_assoc($rs);
		return $row['total'];
	} 
	
	public function getActividad($id_actividad){
		$SQL="SELECT * FROM `actividades`  WHERE id_actividad='".$id_actividad."' ";
		$rs=mysql_query($SQL);
		$row= mysql_fetch_assoc($rs);
		return $row;
	}
	
	public function getLastActividad($prospecto_id,$id_correlativo){ 
		$SQL="SELECT *,DATEDIFF(CURDATE(),fecha_proxima) AS TIME_DIFERENCE FROM `tracking_prospecto` where tracking_prospecto.correlativo='".$id_correlativo."' and id_nit='".$prospecto_id."' AND  (actividad_proxima is null or actividad_proxima='' ) ORDER BY id DESC LIMIT 1 ";
 		 
		
		$rs=mysql_query($SQL);
		$row= mysql_fetch_assoc($rs);
		return $row;
	}
	
	public function getDataProspecto($prospecto_id,$id_correlativo){
		$SQL="SELECT *,DATEDIFF(fecha_fin,CURDATE()) AS  dias_fin,DATE_ADD(fecha_fin,INTERVAL 15 DAY) AS grace_day FROM `prospecto_comercial` WHERE id_nit='".$prospecto_id."' AND correlativo='".$id_correlativo."'";
	 
		$rs=mysql_query($SQL);
		$row= mysql_fetch_assoc($rs);
		return $row;
	}
	
	public function getList(){
		
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_real_escape_string($_REQUEST['sSearch']);
			$QUERY=" and (nombre_completo LIKE '%".$_REQUEST['sSearch']."%' OR dscrip_tipopilar LIKE '%".$_REQUEST['sSearch']."%' OR estatus LIKE '%".$_REQUEST['sSearch']."%' OR concat(dias_proteccion,' Dias') LIKE '%".$_REQUEST['sSearch']."%' OR observaciones LIKE '%".$_REQUEST['sSearch']."%' OR id_nit LIKE '%".$_REQUEST['sSearch']."%' OR fecha_inicio LIKE '%".$_REQUEST['sSearch']."%' OR fecha_fin LIKE '%".$_REQUEST['sSearch']."%' OR ASESOR LIKE '%".$_REQUEST['sSearch']."%')";
		 
		  }
		}

		$SQL="SELECT count(*) as total FROM cache_listado_prospectos as prospecto
				WHERE   DATEDIFF(prospecto.`fecha_fin`,CURDATE())>=0 and
				 `prospecto`.`id_status` IN (5,8,7,6) ";
				 
		if (UserAccess::getInstance()->getRoleId()!=5){
			//$SQL.=" and (prospecto.id_comercial  LIKE '%".UserAccess::getInstance()->getComercialID()."%') ";
		}
		$SQL.=" and (prospecto.codigo_gerente='".UserAccess::getInstance()->getComercialID()."' OR
			prospecto.codigo_asesor='".UserAccess::getInstance()->getComercialID()."') ";
			
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
			$SQL="SELECT * FROM cache_listado_prospectos as prospecto
				WHERE   DATEDIFF(prospecto.`fecha_fin`,CURDATE())>=0 and
				 `prospecto`.`id_status` IN (5,8,7,6) ";
		 	
			if (UserAccess::getInstance()->getRoleId()!=5){
		 		
			}
			$SQL.=" and (prospecto.codigo_gerente='".UserAccess::getInstance()->getComercialID()."' OR
			prospecto.codigo_asesor='".UserAccess::getInstance()->getComercialID()."') ";
			
			$SQL.=$QUERY;
		 
			if (isset($_REQUEST['iSortCol_0']) && isset($_REQUEST['iSortCol_0'])){
				$ncolumn=$_REQUEST['iSortCol_0'];
				$order='';
				
				$column=array(
					0=>"nombre_completo",
					1=>'pilar_inicial',
					2=>'fecha_inicio',
					3=>'fecha_fin',
					4=>'TIME_TO_END',
					5=>'ASESOR',
					6=>'estatus',
					7=>'observaciones',
					8=>'fecha_ultima_actividad',
					9=>'descrip_actividad',
					10=>'observaciones',
					11=>'observaciones',
					12=>'observaciones'
				);
				
				if ($_REQUEST['sSortDir_0']=="desc"){
					$order="desc";	
				}else{
					$order="asc";	
				}
				
				if (is_numeric($ncolumn)){
					 $column[$ncolumn];
				}
				$SQL.=" ORDER BY ".$column[$ncolumn]." ".$order;	
				
				
			}else{
				$SQL.=" ORDER BY `prospecto`.`estatus`,DATEDIFF(prospecto.`fecha_fin`,CURDATE()) asc ";	
			}
			//$SQL.=" ORDER BY `sys_status`.`id_status`,DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE()) asc ";
			
			
			if (!isset($_REQUEST['iDisplayStart'])){
				$_REQUEST['iDisplayStart']=0;	
			}
			if (!isset($_REQUEST['iDisplayLength'])){
				$_REQUEST['iDisplayLength']=1;	
			}			
			
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
		  
			
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			 
			$id_comercial=UserAccess::getInstance()->getComercialID();
		
		 
			while($row=mysql_fetch_assoc($rs)){	
				$row['nombre_completo']=utf8_encode($row['nombre_completo']);
				$encriptID=System::getInstance()->Encrypt(json_encode($row));
				$id_nit=System::getInstance()->Encrypt($row['id_nit']);
				$correlativo=System::getInstance()->Encrypt($row['correlativo']);
				
				$row['fecha_inicio']=$row['format_fecha_inicio'];
				$row['fecha_fin']=$row['format_fecha_fin'];
				unset($row['format_fecha_inicio']);
				unset($row['format_fecha_fin']);
				
				 
				$row['nombre_asesor']=utf8_encode($row['ASESOR']);
				unset($row['ASESOR']);
				//$row['nombre_asesor']=$data_p['primer_nombre']." ".$data_p['primer_apellido'];
				$row['bt_reserva']='';
				$row['bt_editar']='<a href="#" onclick="viewProspecto(\''.$encriptID.'\')"><img src="images/subtract_from_cart.png"  /></a>';
				$row['bt_editar_user']='<a href="#" id="'.$id_nit.'" class="edit_client_prosp"><img src="images/edit_user.png"  /></a>'; 
				
				$row['bt_reasing']='<a href="#" id="'.$encriptID.'" alt="Reasignar prospecto" class="reasign_prosp"><img src="images/change_user.png"  /></a>'; 
				
				
				if (($row['codigo_gerente']!=$id_comercial) || ($row['id_status']==6)){
					$row['bt_reasing']='';
				}
			  
				 
				$row['bt_editar_user_remove']='<a href="#" id="'.$correlativo.'" alt="Remover prospecto" class="remove_client_prosp"><img src="images/cross.png"  /></a>'; 

				array_push($data['aaData'],$row);
	 
			} 
			return $data;		
	}

	public function getListDetalleGestion($obj){
		
		$QUERY="";
		$HAVING="";
	 
		 
		if (isset($obj->id_comercial) && isset($obj->type)){ 
			if ($obj->type=="TOTAL"){ 
				$QUERY.=" and prospecto.codigo_asesor='".$obj->id_comercial."'";
			}	
		
			if ($obj->type!="TOTAL"){ 
				$QUERY.=" and prospecto.codigo_asesor='".$obj->id_comercial."' AND 
					prospecto.clasificacion_cliente='".$obj->type."'";
			}	
		}
		if (isset($obj->id_comercial)){
			
		}
		if (isset($obj->type)){ 
			if ($obj->type!="TOTAL"){ 
				$QUERY.=" AND prospecto.clasificacion_cliente='".$obj->type."'";
			}
		}
		
		if ( isset($obj->id_comercial_gerente)){
			if (isset($obj->type)){
				if ($obj->type=="TOTAL"){ 
					$QUERY.=" and prospecto.codigo_gerente='".$obj->id_comercial_gerente."'";
				}else if ($obj->type!="TOTAL"){ 
					$QUERY.=" and prospecto.codigo_gerente='".$obj->id_comercial_gerente."' AND 
						prospecto.clasificacion_cliente='".$obj->type."'";
				}
			}elseif(!isset($obj->type)){
				$QUERY.=" and prospecto.codigo_gerente='".$obj->id_comercial_gerente."'";
			} 	 
		}
		 
		
		if (isset($obj->fecha_desde) && isset($obj->fecha_hasta)){
			$QUERY.=" AND DATE_FORMAT(fecha_inicio, '%Y-%m-%d') BETWEEN '". mysql_real_escape_string($obj->fecha_desde) ."' AND '". mysql_real_escape_string($obj->fecha_hasta) ."'";
		}
		
		 
		if (isset($obj->ultima_actividad)){
			if (trim($obj->ultima_actividad)!=''){
			//print_r($obj->id_comercial);
				switch($obj->ultima_actividad){
					case "PRE";
						$QUERY.=" and prospecto.PRESENTACION='1'";
					break;	
					case "CITA";
						$QUERY.=" and prospecto.CITA='1'";
					break;
					case "CIE";
						$QUERY.=" and prospecto.CIERRE='1'";
					break;		
					case "RES";
						$QUERY.=" and prospecto.RESERVAS='1'";
					break;																							
				}
				
			}
		}
	
		
		if (isset($obj->PILAR)){
			$QUERY.=" and prospecto.pilar_inicial='".$obj->PILAR."'";
			if ($obj->type!="TOTAL"){ 
				$QUERY.=" and prospecto.clasificacion_cliente='".$obj->type."'";
			}	
		} 
 	 
			$SQL="SELECT count(*) as total FROM cache_listado_prospectos as prospecto
					WHERE  `prospecto`.`id_status` IN (4,5,8,7,6,9,11,16) ";
		 
				
			$SQL.=$QUERY;
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$total_row=$row['total'];
		
			$SQL="SELECT * FROM cache_listado_prospectos as prospecto
				WHERE  `prospecto`.`id_status` IN (4,5,8,7,6,9,11,16) ";
		 	 
			//$SQL.=" and (prospecto.id_comercial_gerente='".UserAccess::getInstance()->getComercialID()."') ";
			
			$SQL.=$QUERY;
 			
 			  
			if (!isset($_REQUEST['iDisplayStart'])){
				$_REQUEST['iDisplayStart']=0;	
			}
			if (!isset($_REQUEST['iDisplayLength'])){
				$_REQUEST['iDisplayLength']=1;	
			}	
			 
	  		
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
				$row['nombre_completo']=utf8_encode($row['nombre_completo']);
				$encriptID=System::getInstance()->Encrypt(json_encode($row));
				$id_nit=System::getInstance()->Encrypt($row['id_nit']);
				
				$row['fecha_inicio']=$row['format_fecha_inicio'];
				$row['fecha_fin']=$row['format_fecha_fin'];
				unset($row['format_fecha_inicio']);
				unset($row['format_fecha_fin']);
				
				 
				$row['nombre_asesor']=utf8_encode($row['ASESOR']);
				unset($row['ASESOR']);
 				$row['bt_editar_user']='';
				if (($row['id_status']==6) or ($row['id_status']==5) or ($row['id_status']==4))
 				$row['bt_editar_user']='<a href="#" id="'.$id_nit.'" class="edit_client_prosp"><img src="images/edit_user.png"  /></a>';  

				array_push($data['aaData'],$row);
	 
			} 
			return $data;		
	}
	/*Optiene los datos del prospecto que este siendo prospectando
	en el momento */
	public function getProspectoDataActivo($id_nit){
		$data=array("valid"=>false);
		$SQL="SELECT * FROM cache_listado_prospectos AS prospecto WHERE   DATEDIFF(prospecto.`fecha_fin`,CURDATE())>=0 AND
 		`prospecto`.`id_status` IN (5,8,7,6) AND id_nit='".$id_nit."'";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){	
			$data=$row;
			$data['valid']=true;
		}
		return $data;
	}
		
	public function getListAllProspecto(){
		
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_real_escape_string($_REQUEST['sSearch']);
			$QUERY=" and (nombre_completo LIKE '%".$_REQUEST['sSearch']."%' OR dscrip_tipopilar LIKE '%".$_REQUEST['sSearch']."%' OR estatus LIKE '%".$_REQUEST['sSearch']."%' OR concat(dias_proteccion,' Dias') LIKE '%".$_REQUEST['sSearch']."%' OR observaciones LIKE '%".$_REQUEST['sSearch']."%' OR id_nit LIKE '%".$_REQUEST['sSearch']."%' OR fecha_inicio LIKE '%".$_REQUEST['sSearch']."%' OR fecha_fin LIKE '%".$_REQUEST['sSearch']."%' OR ASESOR LIKE '%".$_REQUEST['sSearch']."%' 
			OR REPLACE(asesor,' ','') LIKE '%". str_replace(" ","",$_REQUEST['sSearch'])."%' )";
		 
		  }
		}

		$SQL="SELECT count(*) as total FROM cache_listado_prospectos as prospecto
				WHERE   DATEDIFF(prospecto.`fecha_fin`,CURDATE())>=0 and
				 `prospecto`.`id_status` IN (5,8,7,6) ";
				 
		if (UserAccess::getInstance()->getRoleId()!=5){
		//	$SQL.=" and (prospecto.id_comercial  LIKE '%".UserAccess::getInstance()->getComercialID()."%') ";
		}
			
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
			$SQL="SELECT * FROM cache_listado_prospectos as prospecto
				WHERE   DATEDIFF(prospecto.`fecha_fin`,CURDATE())>=0 and
				 `prospecto`.`id_status` IN (5,8,7,6) ";
		 	
			if (UserAccess::getInstance()->getRoleId()!=5){
		 		
			}
			//$SQL.=" and (prospecto.id_comercial  LIKE '%".UserAccess::getInstance()->getComercialID()."%') ";
			
			$SQL.=$QUERY;
		 
			
			if (isset($_REQUEST['iSortCol_0']) && isset($_REQUEST['iSortCol_0'])){
				$ncolumn=$_REQUEST['iSortCol_0'];
				$order='';
				
				$column=array(
					0=>"nombre_completo",
					1=>'pilar_inicial',
					2=>'fecha_inicio',
					3=>'fecha_fin',
					4=>'TIME_TO_END',
					5=>'ASESOR',
					6=>'estatus',
					7=>'observaciones',
					8=>'fecha_ultima_actividad',
					9=>'descrip_actividad',
					10=>'observaciones',
					11=>'observaciones',
					12=>'observaciones'
				);
				
				if ($_REQUEST['sSortDir_0']=="desc"){
					$order="desc";	
				}else{
					$order="asc";	
				}
				
				if (is_numeric($ncolumn)){
					 $column[$ncolumn];
				}
				$SQL.=" ORDER BY ".$column[$ncolumn]." ".$order;	
				
				
			}else{
				$SQL.=" ORDER BY `prospecto`.`estatus`,DATEDIFF(prospecto.`fecha_fin`,CURDATE()) asc ";	
			}
			//$SQL.=" ORDER BY `sys_status`.`id_status`,DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE()) asc ";
			
			
			if (!isset($_REQUEST['iDisplayStart'])){
				$_REQUEST['iDisplayStart']=0;	
			}
			if (!isset($_REQUEST['iDisplayLength'])){
				$_REQUEST['iDisplayLength']=1;	
			}			
			
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
		   
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			 
			//SystemHtml::getInstance()->includeClass("client","PersonalData");
			//$person= new PersonalData(UserAccess::getInstance()->getDBLink());
		
			while($row=mysql_fetch_assoc($rs)){	
				$row['nombre_completo']=utf8_encode($row['nombre_completo']);
				$encriptID=System::getInstance()->Encrypt(json_encode($row));
				$id_nit=System::getInstance()->Encrypt($row['id_nit']);
				
				$row['fecha_inicio']=$row['format_fecha_inicio'];
				$row['fecha_fin']=$row['format_fecha_fin'];
				unset($row['format_fecha_inicio']);
				unset($row['format_fecha_fin']);
				
				$row['nombre_asesor']=utf8_encode($row['ASESOR']);
				unset($row['ASESOR']);
								
				$row['bt_reserva']='';
				$row['bt_editar']='<a href="#" onclick="viewProspecto(\''.$encriptID.'\')"><img src="images/subtract_from_cart.png"  /></a>';
				
				$row['bt_editar_user']='<a href="#" id="'.$id_nit.'" class="edit_client_prosp"><img src="images/edit_user.png"  /></a>'; 
				
				$row['bt_editar_user_remove']='<a href="#" id="'.$id_nit.'" alt="Remover prospecto" class="remove_client_prosp"><img src="images/cross.png"  /></a>'; 

				array_push($data['aaData'],$row);
	 
			}
			 
			
			return $data;		
	}	
		
	public function getListCartera(){
		
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_real_escape_string($_REQUEST['sSearch']);
		$QUERY=" AND (sys_personas.id_nit LIKE '%".$_REQUEST['sSearch']."%' OR CONCAT(sys_personas.primer_nombre,' ' ,
	sys_personas.segundo_nombre) LIKE '%".$_REQUEST['sSearch']."%' OR sys_personas.fecha_nacimiento LIKE '%".$_REQUEST['sSearch']."%' OR sys_personas.id_nit  LIKE '%".$_REQUEST['sSearch']."%' or
MATCH(sys_personas.`primer_nombre`,sys_personas.`segundo_nombre`,sys_personas.tercer_nombre,sys_personas.primer_apellido,sys_personas.segundo_apellido) AGAINST ('".$_REQUEST['sSearch']."')
	OR sys_status.`descripcion` LIKE '%".$_REQUEST['sSearch']."%' 
	OR `prospecto_comercial`.`pilar_inicial`  LIKE '%".$_REQUEST['sSearch']."%' 
	) ";
		  }
		}

		$SQL="SELECT 
			count(*) as total
			FROM `prospectos`
			  INNER JOIN `prospecto_comercial` ON (`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)
			  INNER JOIN `tipos_pilares` ON (`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)
			  INNER JOIN `sys_status` ON (`prospecto_comercial`.`estatus` = `sys_status`.`id_status`)
			  INNER JOIN `sys_personas` ON (`prospecto_comercial`.`id_nit` = `sys_personas`.`id_nit`)
			WHERE  `prospecto_comercial`.`estatus` IN (6) ";
			$SQL.=" and (prospecto_comercial.id_comercial  LIKE '%".UserAccess::getInstance()->getComercialID()."%') ";
		 
		
		
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
			$SQL="SELECT 
				  `tipos_pilares`.`dscrip_tipopilar`,
				  `prospecto_comercial`.`pilar_inicial`, 
				  `sys_status`.`id_status`,
				  `tipos_pilares`.`dias_proteccion`,
				  `prospectos`.`observaciones`, 
				  `prospecto_comercial`.`id_comercial`,
				  `prospecto_comercial`.`correlativo`,
				  `prospecto_comercial`.`id_nit`,
				  CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_completo,
				  DATE_FORMAT(prospecto_comercial.`fecha_inicio`, '%d-%m-%Y') AS fecha_inicio,
				  DATE_FORMAT(prospecto_comercial.`fecha_fin`, '%d-%m-%Y') AS fecha_fin,
				  DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE()) AS TIME_TO_END
				FROM
				  `prospectos`
				  INNER JOIN `prospecto_comercial` ON (`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)
				  INNER JOIN `tipos_pilares` ON (`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)
				  INNER JOIN `sys_status` ON (`prospecto_comercial`.`estatus` = `sys_status`.`id_status`)
				  INNER JOIN `sys_personas` ON (`prospecto_comercial`.`id_nit` = `sys_personas`.`id_nit`)
				WHERE  `prospecto_comercial`.`estatus` IN (6) ";
		 	
			$SQL.=" and (prospecto_comercial.id_comercial  LIKE '%".UserAccess::getInstance()->getComercialID()."%') ";
		 
			$SQL.=$QUERY;
			 
			
			if (isset($_REQUEST['iSortCol_0']) && isset($_REQUEST['iSortCol_0'])){
				$ncolumn=$_REQUEST['iSortCol_0'];
				$order='';
				
				$column=array(
					0=>"CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido)",
					1=>'pilar_inicial',
					2=>'fecha_inicio',
					3=>'fecha_fin',
					4=>'pilar_inicial',
					5=>'descripcion',
					6=>'observaciones'
				);
				
				if ($_REQUEST['sSortDir_0']=="desc"){
					$order="desc";	
				}else{
					$order="asc";	
				}
				
				if (is_numeric($ncolumn)){
					 $column[$ncolumn];
				}
				$SQL.=" ORDER BY ".$column[$ncolumn]." ".$order;	
				
				
			}else{
				$SQL.=" ORDER BY `sys_status`.`id_status`,DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE()) asc ";	
			}
			 
			if (!isset($_REQUEST['iDisplayStart'])){
				$_REQUEST['iDisplayStart']=0;	
			}
			if (!isset($_REQUEST['iDisplayLength'])){
				$_REQUEST['iDisplayLength']=1;	
			}			 
			
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
				$id_nit=System::getInstance()->Encrypt($row['id_nit']);
				$row['nombre_completo']=utf8_encode($row['nombre_completo']);
				//$this->checkTipoClasificacion($row['id_nit']); 
				$data_p=$this->getAsesorByCode($row['id_comercial']);
				  
				$row['nombre_asesor']=utf8_encode($data_p['primer_nombre']." ".$data_p['primer_apellido']);
				$row['bt_reserva']='';
				$row['bt_editar']='<a href="#" onclick="viewProspecto(\''.$encriptID.'\')"><img src="images/subtract_from_cart.png"  /></a>';
				$row['bt_editar_user']='<a href="#" id="'.$id_nit.'" class="edit_client_prosp"><img src="images/edit_user.png"  /></a>';  

				array_push($data['aaData'],$row);
	 
			}
			 
			
			return $data;		
	}	
	
	/*OBTIENE LOS DATOS DE UN ASESOR POR EL CODIGO DE ASESOR*/
	public function getAsesorByCode($id_comercial){
		$this->setListAsesor();
		$retur=array(
				'primer_nombre'=>'',
				'primer_apellido'=>'',
				'segundo_nombre'=>'',
				'segundo_apellido'=>''
			);	
		if (isset($_SESSION[$this->ua_version]['asesor'][$id_comercial])){
			$retur=$_SESSION[$this->ua_version]['asesor'][$id_comercial]; 
		}   
		return $retur;
	}
	/*ALMACENA LOS ASEOSRES EN UNA SESSION*/
	private function setListAsesor(){
		$update=false;
		if (!isset($_SESSION[$this->_version]['time_to_update'])){
			$_SESSION[$this->_version]['time_to_update']=time();
			 $update=true;
		} 
		if (((time()-$_SESSION[$this->_version]['time_to_update'])/60)>10){
			$_SESSION[$this->_version]['time_to_update']=time();
			$update=true; 
		}  
		
		if ($update){	
			$SQL="SELECT 
			  `sys_personas`.`primer_nombre`,
			  `sys_personas`.`segundo_nombre`,
			  `sys_personas`.`primer_apellido`,
			  `sys_personas`.`segundo_apellido`,
			  `asesores_g_d_gg_view`.`id_comercial`,
			  `asesores_g_d_gg_view`.`id_nit`
			FROM
			  view_estructura_comercial as `asesores_g_d_gg_view`
			  INNER JOIN `sys_personas` ON (`asesores_g_d_gg_view`.`id_nit` = `sys_personas`.`id_nit`) ";
			$rs=mysql_query($SQL);  
			$_SESSION[$this->_version]['asesor']=array();
			while($row=mysql_fetch_assoc($rs)){
				$_SESSION[$this->ua_version]['asesor'][$row['id_comercial']]=$row;
			}
		} 
	}	

	/* OPTIENE EL LISTADO DE PROSPECTOS FRACASADOS*/
	public function getListFracasos(){
 
		$this->ChangeProspectoComercialEstatusFracasado();
		 
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_real_escape_string($_REQUEST['sSearch']);
			
				$QUERY=" AND  (sys_personas.id_nit LIKE '%".$_REQUEST['sSearch']."%' or	MATCH(sys_personas.`primer_nombre`,sys_personas.`segundo_nombre`,sys_personas.tercer_nombre,sys_personas.primer_apellido,sys_personas.segundo_apellido) AGAINST ('".$_REQUEST['sSearch']."') ) ";
		  }
		}

		$SQL="SELECT 
				count(*) as total
				FROM `prospectos`
				  INNER JOIN `prospecto_comercial` ON (`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)
				  INNER JOIN `tipos_pilares` ON (`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)
				  INNER JOIN `sys_status` ON (`prospecto_comercial`.`estatus` = `sys_status`.`id_status`)
				  INNER JOIN `sys_personas` ON (`prospecto_comercial`.`id_nit` = `sys_personas`.`id_nit`)
		WHERE   DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE())<0 and sys_status.id_status='11' 
				  ";
 		$SQL.=$QUERY;
 		
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
			$SQL="SELECT 
				  `tipos_pilares`.`dscrip_tipopilar`,
				  `prospecto_comercial`.`pilar_inicial`,
				  `sys_status`.`descripcion` as estatus,
				  `sys_status`.`id_status`,
				  `tipos_pilares`.`dias_proteccion`,
				  `prospectos`.`observaciones`, 
				  `prospecto_comercial`.codigo_asesor as `id_comercial`,
				  `prospecto_comercial`.`correlativo`,
				  `prospecto_comercial`.`id_nit`,
				  CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_completo,
				  DATE_FORMAT(prospecto_comercial.`fecha_inicio`, '%d-%m-%Y') AS fecha_inicio,
				  DATE_FORMAT(prospecto_comercial.`fecha_fin`, '%d-%m-%Y') AS fecha_fin,
				  DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE()) AS TIME_TO_END
				FROM
				  `prospectos`
				  INNER JOIN `prospecto_comercial` ON (`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)
				  INNER JOIN `tipos_pilares` ON (`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)
				  INNER JOIN `sys_status` ON (`prospecto_comercial`.`estatus` = `sys_status`.`id_status`)
				  INNER JOIN `sys_personas` ON (`prospecto_comercial`.`id_nit` = `sys_personas`.`id_nit`)
				WHERE   DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE())<0 and sys_status.id_status='11' ";
		 
		 	$SQL.=$QUERY;
			
			if (!isset($_REQUEST['iDisplayStart'])){
				$_REQUEST['iDisplayStart']=0;	
			}
			if (!isset($_REQUEST['iDisplayLength'])){
				$_REQUEST['iDisplayLength']=1;	
			}			
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			/*(prospecto_comercial.id_comercial  LIKE '%".UserAccess::getInstance()->getComercialID()."%')
				AND*/   
			 
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			
			//SystemHtml::getInstance()->includeClass("client","PersonalData");
			//$person= new PersonalData(UserAccess::getInstance()->getDBLink());
			while($row=mysql_fetch_assoc($rs)){	
				$id_nit=System::getInstance()->Encrypt($row['id_nit']);
				$row['nombre_completo']=utf8_encode($row['nombre_completo']);
				
				$data_p=$this->getAsesorByCode($row['id_comercial']);
				
				$row['nombre_asesor']=utf8_encode($data_p['primer_nombre'])." ". utf8_encode($data_p['primer_apellido']);
				
				
				$encriptID=System::getInstance()->Encrypt(json_encode($row));
				
				$row['bt_editar']='<input name="'.$row['correlativo'].'" type="checkbox" class="reasign" id="'.$row['correlativo'].'" value="'.$encriptID.'">'; 
				
				//$row['bt_editar_user']='<a href="#" id="'.$id_nit.'" class="edit_client_prosp"><img src="images/edit_user.png"  /></a>'; 
				array_push($data['aaData'],$row);
			
				 
			}
			
			return $data;		
	}
	
	/*CAMBIA EL ESTATUS DE UN PROSPECTO AL CUAL EL TIEMPO DE 
	PROTECCION HAYA LLEGADO AL LIMITE MENOR DE 0 DIAS LO CONVIERTE A FRACASADO*/
	public function ChangeProspectoComercialEstatusFracasado(){
		$obj = new ObjectSQL();
		$obj->estatus='11'; //ESTATUS FRACASADO
		$SQL=$obj->getSQL("update","prospecto_comercial"," WHERE DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE())<0 AND estatus=5  ");	
		 
		@mysql_query($SQL);	
	}
	
	/* PROCESO DE REASIGNACION DE PROSPECTOS A UN GERENTE */
	public function ReasignacionProspectoToGerente(){
		$message=array("mensaje"=>"","error"=>true);
		if (isset($this->data['list_reasign']) && isset($this->data['asesor_data'])){
			
		//	print_r($this->data['asesor_data']['data']['code']);

			foreach($this->data['list_reasign'] as $key =>$val){ 
				$prospecto=json_decode(System::getInstance()->Decrypt($val['val']));
				if ($this->validateProspectoReasign($prospecto->id_nit)>0){
					$asesor=json_decode(System::getInstance()->Decrypt($this->data['asesor_data']['data']['code']));
		 
					/*LO AGREGO AL TEMPORAL PROSPECTO PARA */
					$obj= new ObjectSQL();
					$obj->id_comercial=$asesor->id_comercial;
					$obj->codigo_asesor=$asesor->id_comercial;
					$obj->correlativo=$prospecto->correlativo;
					$obj->id_nit=$prospecto->id_nit;
					$obj->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";  
					$SQL=$obj->getSQL("insert","temporal_prospecto");
					@mysql_query($SQL);
					//print_r($SQL);
					/*--------------------------------------*/
					
					/*ACTUALIZO EL PROSPECTO Y LO PONGO EN ESTADO FRACASADO-INACTIVO*/
					$obj= new ObjectSQL();
					$obj->estatus=12;
					$SQL=$obj->getSQL("update","prospecto_comercial"," where correlativo='".$prospecto->correlativo."'");
					@mysql_query($SQL);
					
					$message['mensaje']='Prospecto reasignado!';
					$message['error']=false;	
				//	print_r($prospecto);
					
				}else{
					$message['mensaje']='Error el prospecto ya fue reasignado!';	
				}
				
			}
			 
		}else{
			$message['mensaje']='Error debe de llenar todos los campos';
		}
		
		return $message;
	}
	
	/*VALIDA SI UN PROSPECTO YA SE ENCUENTRA REASIGNADO*/
	private function validateProspectoReasign($id_nit){
		$SQL="SELECT COUNT(*) AS total FROM prospecto_comercial WHERE DATEDIFF(prospecto_comercial.`fecha_fin`,CURDATE())<0  AND prospecto_comercial.estatus='11' AND id_nit='".$id_nit."' ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['total'];
	}
	
	/*OPTIENE LA LISTA DE LOS PROSPECTOS REASIGNADO*/
	public function getReasignadoList(){
		
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_real_escape_string($_REQUEST['sSearch']);
			$QUERYS=" and `prospecto_comercial`.`id_nit` LIKE '%".$_REQUEST['sSearch']."%'  ";
		  }
		}

		$SQL="SELECT 
				count(*) as total
				FROM
				  `prospectos`
				  INNER JOIN `prospecto_comercial` ON (`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)
				  INNER JOIN `tipos_pilares` ON (`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)
				  INNER JOIN `sys_personas` ON (`prospecto_comercial`.`id_nit` = `sys_personas`.`id_nit`)
				  INNER JOIN `temporal_prospecto` ON (`prospecto_comercial`.`correlativo` = `temporal_prospecto`.`correlativo`)
				  AND (`prospecto_comercial`.`id_nit` = `temporal_prospecto`.`id_nit`)
				  INNER JOIN `sys_status` ON (`temporal_prospecto`.`estatus` = `sys_status`.`id_status`)
				  ";
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
			$SQL="SELECT 
				  `tipos_pilares`.`dscrip_tipopilar`,
				  `prospecto_comercial`.`pilar_inicial`,
				  `sys_status`.`descripcion` AS `estatus`,
				  `sys_status`.`id_status`,
				  `tipos_pilares`.`dias_proteccion`,
				  `prospectos`.`observaciones`,
				  `prospecto_comercial`.`id_comercial`,
				  `prospecto_comercial`.`correlativo`,
				  `prospecto_comercial`.`id_nit`,
				  CONCAT(`sys_personas`.`primer_nombre`, ' ', `sys_personas`.`primer_apellido`) AS `nombre_completo`,
				  DATE_FORMAT(`prospecto_comercial`.`fecha_inicio`, '%d-%m-%Y') AS `fecha_inicio`,
				  DATE_FORMAT(`prospecto_comercial`.`fecha_fin`, '%d-%m-%Y') AS `fecha_fin`,
				  DATEDIFF(`prospecto_comercial`.`fecha_fin`, CURDATE()) AS `TIME_TO_END`,
				  `temporal_prospecto`.`id_comercial`
				FROM
				  `prospectos`
				  INNER JOIN `prospecto_comercial` ON (`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)
				  INNER JOIN `tipos_pilares` ON (`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)
				  INNER JOIN `sys_personas` ON (`prospecto_comercial`.`id_nit` = `sys_personas`.`id_nit`)
				  INNER JOIN `temporal_prospecto` ON (`prospecto_comercial`.`correlativo` = `temporal_prospecto`.`correlativo`)
				  AND (`prospecto_comercial`.`id_nit` = `temporal_prospecto`.`id_nit`)
				    INNER JOIN `sys_asesor` ON (`sys_asesor`.`codigo_asesor`=temporal_prospecto.codigo_asesor)
				  INNER JOIN `sys_status` ON (`temporal_prospecto`.`estatus` = `sys_status`.`id_status`)
				WHERE sys_status.id_status='10' ";
 
			if (!isset($_REQUEST['iDisplayStart'])){
				$_REQUEST['iDisplayStart']=0;	
			}
			if (!isset($_REQUEST['iDisplayLength'])){
				$_REQUEST['iDisplayLength']=1;	
			}
			$SQL.=$QUERYS;
		 
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			  
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			
			//SystemHtml::getInstance()->includeClass("client","PersonalData");
			//$person= new PersonalData(UserAccess::getInstance()->getDBLink());
			while($row=mysql_fetch_assoc($rs)){	
				$id_nit=System::getInstance()->Encrypt($row['id_nit']);
				$data_p=$this->getAsesorByCode($row['id_comercial']);
				
				$encriptID=System::getInstance()->Encrypt(json_encode($row));  
 		 		$row['nombre_asesor']=utf8_encode($data_p['primer_nombre'])." ". utf8_encode($data_p['primer_apellido']);

				$row['bt_editar']='<input name="'.$row['correlativo'].'" type="checkbox" class="reasign" id="'.$row['correlativo'].'" value="'.$encriptID.'">'; 
				array_push($data['aaData'],$row);
			}
			
			return $data;		
	}
	 
	/*VALIDA SI UN PROSPECTO YA SE ENCUENTRA ASIGNADO A UN ASESOR*/
	private function validateProspectoReasignToAsesor($id_nit){
		$SQL="SELECT COUNT(*) AS total from `temporal_prospecto` 
				WHERE estatus=10  AND id_nit='".$id_nit."' ";
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['total'];
	}
	
	/* PROCESO DE REASIGNACION DE PROSPECTOS a un ASESOR */
	public function ReasignacionProspectoToAsesor(){
		$message=array("mensaje"=>"","error"=>true);
		if (isset($this->data['list_reasign']) && isset($this->data['asesor_data'])){
			
			//print_r($this->data);

			foreach($this->data['list_reasign'] as $key =>$val){ 
				$prospecto=json_decode(System::getInstance()->Decrypt($val['val']));
				if ($this->validateProspectoReasignToAsesor($prospecto->id_nit)>0){
					
					$asesor=json_decode(System::getInstance()->Decrypt($this->data['asesor_data']['data']['code']));
					//$id_comercial=$this->data['asesor_data']['data']['code']; //Codigo del asesor
					$id_comercial=$asesor->id_comercial;
					//print_r($prospecto);
					/*ACTUALIZO EL temporal_prospecto A ESTATUS REASIGNADO*/
					$temporal_prospecto= new ObjectSQL();
					$temporal_prospecto->estatus="9"; //Estatus Reasignado
					$SQL=$temporal_prospecto->getSQL("update","temporal_prospecto"," where id_nit='".$prospecto->id_nit."'
															and correlativo='".$prospecto->correlativo."'"); 
					@mysql_query($SQL); 
					/*------------------------------------------------------*/
					
					
					/*ACTUALIZO EL prospecto_comercial A ESTATUS REASIGNADO*/
					$temporal_prospecto= new ObjectSQL();
					$temporal_prospecto->estatus="9"; //Estatus Reasignado
					$SQL=$temporal_prospecto->getSQL("update","prospecto_comercial"," where id_nit='".$prospecto->id_nit."'
															and correlativo='".$prospecto->correlativo."'"); 
					@mysql_query($SQL); 
					/*------------------------------------------------------*/
					
					$prospectos_comercial= new ObjectSQL();
					$prospectos_comercial->id_nit=$prospecto->id_nit;			
					$prospectos_comercial->id_comercial=$id_comercial; 
					$prospectos_comercial->codigo_asesor=$id_comercial; 
					$prospectos_comercial->pilar_inicial=$prospecto->pilar_inicial;
				//	$prospectos_comercial->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";;
					$prospectos_comercial->fecha_inicio="CONCAT(CURDATE(),' ',CURRENT_TIME())";
					$prospectos_comercial->fecha_fin="DATE_ADD(CONCAT(CURDATE(),' ',CURRENT_TIME()), INTERVAL ".$prospecto->dias_proteccion." DAY)";
					$prospectos_comercial->estatus="5"; //Estatus Reasignado
					
					$SQL=$prospectos_comercial->getSQL("insert","prospecto_comercial"); 
					mysql_query($SQL);
					 
				  
					$message['mensaje']='Prospecto asignado al asesor!';
					$message['error']=false;	
					
					
				}else{
					$message['mensaje']='Error el prospecto ya fue reasignado!';	
				}
				
			}
			 
		}else{
			$message['mensaje']='Error debe de llenar todos los campos';
		}
		
		return $message;
	}
	
	/*OPTIENE LOS DATOS DE UN PROSPECTO*/
	private function getPropectoDatabyCorrelativo($correlativo){
		$SQL="SELECT * FROM `cache_listado_prospectos` WHERE correlativo='".$correlativo."' "; 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row;
		}
		return $data;
	}	
	/* PROCESO DE CAMBIO DE PROSPECTO DE UN ASESOR A OTRO*/
	public function changeOfProspectoToAsesor($id_comercial,$correlativo){
		$message=array("mensaje"=>"","error"=>true); 
		$p_comercial=$this->getPropectoDatabyCorrelativo($correlativo); 
		if (count($p_comercial)>0){
			$estatus=array(5=>1,9=>2);
			//echo $p_comercial['estatus'];
			/*FILTRO EL ESTATUS PARA SOLO ESCOGER LOS QUE ESTAN Protegidos y Reasignado*/
		 
			if (array_key_exists($p_comercial['id_status'],$estatus)){
			//	print_r($p_comercial);
				/*ACTUALIZO EL prospecto_comercial A ESTATUS REASIGNADO*/
				$temporal_prospecto= new ObjectSQL();
				$temporal_prospecto->estatus="21"; //Estatus Reasignado
				$SQL=$temporal_prospecto->getSQL("update","prospecto_comercial"," where id_nit='".$p_comercial['id_nit']."' and correlativo='".$p_comercial['correlativo']."'"); 
		 		
			 
				@mysql_query($SQL); 
		 
		 		$this->doCreateReportCache($p_comercial['pilar_inicial'],
											$p_comercial['codigo_asesor'],
											$p_comercial['id_nit'],
											$p_comercial['correlativo']);	  
				/*------------------------------------------------------*/
				
				/*---------------------------------------*/
				$c_p= new ObjectSQL();
				$c_p->push($p_comercial);
				unset($c_p->correlativo);
				unset($c_p->id_comercial);
				unset($c_p->last_tracking_prospecto_id);
				$c_p->estatus=5;
				$c_p->codigo_asesor=$id_comercial;
				$c_p->setTable("prospecto_comercial");
				$SQL_c = $c_p->toSQL("insert");
				@mysql_query($SQL_c); 
				$correlativo=mysql_insert_id();				
				/*------------------------*/
				
				/*-------------------------------*/
		 		$this->doCreateReportCache($c_p->pilar_inicial,
											$c_p->codigo_asesor,
											$c_p->id_nit,
											$correlativo);	  
				/*------------------------------*/											
				
				
				$message['mensaje'] ="Prospecto reasignado!"; 	
				$message['error']=false;
				
			}else{
				$message['mensaje'] ="Para poder realizar esta operacion el prospecto debe de estar protegido o reasignado!"; 				
 
			}
		}else{
			$message['mensaje'] ="Datos incompletos para procesar la información!"; 				
		}
		 
		return $message;
	}	
	
	/*Remover el prospecto */
	public function remove($id,$comentario=""){
		$correlativo=trim(System::getInstance()->Decrypt($id));
	 
		if ($correlativo!=""){
			$obj= new ObjectSQL();
			$obj->estatus=16;
			$SQL=$obj->getSQL("update","prospecto_comercial"," where correlativo='". mysql_real_escape_string($correlativo) ."' and estatus!=16 ");	 
			@mysql_query($SQL);
			
 			$id_comercial=UserAccess::getInstance()->getComercialID();
			$obj= new ObjectSQL();
			$obj->estatus="Propsecto Removido";
			$obj->id_status=16;
			$SQL=$obj->getSQL("update","cache_listado_prospectos"," where correlativo='". mysql_real_escape_string($correlativo) ."' and estatus!=16  AND (codigo_gerente='". $id_comercial ."' OR codigo_asesor='". $id_comercial ."') ");	 
 			@mysql_query($SQL);	 
			$retur=array("mensaje"=>"Prospecto removido","error"=>false);
			return $retur;
		}else{
			$retur=array("mensaje"=>"Fallo al tratar de remover el prospecto","error"=>true);
			return $retur;	
		}
	}
	
	public function prospectExist($id_nit){
		//$SQL="SELECT COUNT(*) AS tt FROM `prospectos` WHERE id_nit='".$id_nit."'";
		/*SI EL PROSPECTO EXISTE SOLO SI NO HA SIDO PUESTO EN ESTATUS 16 QUE ES ELIMINADO
		*/
		$SQL="SELECT COUNT(*) AS tt FROM 
			prospectos 
			INNER JOIN prospecto_comercial  ON (prospecto_comercial.id_nit=prospectos.id_nit)
			WHERE 
			prospectos.id_nit='".$id_nit."' AND prospecto_comercial.estatus!='16'";
 
			
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs);
		return $row['tt'];	
	}
	public function getProspecto($id_nit){ 
		$SQL="SELECT * FROM prospectos 
			INNER JOIN prospecto_comercial  ON (prospecto_comercial.id_nit=prospectos.id_nit)
			WHERE  prospectos.id_nit='".$id_nit."' AND prospecto_comercial.estatus!='16'"; 
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs);
		return $row;	
	} 	
	public function validateIsProspect($tipo,$identifiacion){
		/*$SQL="SELECT sys_personas.*,prospectos.estatus AS pros_estatus FROM `sys_personas`
			 LEFT JOIN `prospectos` ON (prospectos.id_nit=sys_personas.id_nit)
			 WHERE (sys_personas.id_nit='". mysql_real_escape_string(trim($identifiacion))."' 
			 and sys_personas.id_documento='".mysql_real_escape_string(trim($tipo))."' ) ";
			*/
		$SQL="SELECT sys_personas.*,
				(SELECT prospecto_comercial.estatus FROM 
					prospectos 
					INNER JOIN prospecto_comercial  ON (prospecto_comercial.id_nit=prospectos.id_nit)
				WHERE 
				(prospectos.id_nit=sys_personas.id_nit) 
				ORDER BY prospecto_comercial.`fecha_inicio` DESC LIMIT 1) AS pros_estatus 
				FROM `sys_personas`
				 WHERE (sys_personas.id_nit='". mysql_real_escape_string(trim($identifiacion))."' 
							 and sys_personas.id_documento='".mysql_real_escape_string(trim($tipo))."' ) ";
							 	  
		$rs=mysql_query($SQL);
		$rt=array("addnew"=>true,"personal"=>array());
		while($row=mysql_fetch_assoc($rs)){
			$row['id_nit']=System::getInstance()->Encrypt($row['id_nit']);
			if ($row['pros_estatus']==16){
				$row['pros_estatus']=null;
			}
			
			$rt['addnew']=false;	

			$rt['persona']=$row;
		}
		
		return $rt;
	}	
	 
	public function checkTipoClasificacion($id_nit){
		SystemHtml::getInstance()->includeClass("client","PersonalData");
 		$person= new PersonalData($this->db_link);
		$SQL="SELECT  
			  `prospecto_comercial`.`pilar_inicial`,
			  `sys_status`.`descripcion` AS estatus,
			  `sys_status`.`id_status`,
			  `tipos_pilares`.`dias_proteccion` 
			FROM
			  `prospectos`
			  INNER JOIN `prospecto_comercial` ON (`prospectos`.`id_nit` = `prospecto_comercial`.`id_nit`)
			  INNER JOIN `tracking_prospecto` ON (`tracking_prospecto`.`id_nit` = `prospecto_comercial`.`id_nit`)
			  INNER JOIN `tipos_pilares` ON (`prospecto_comercial`.`pilar_inicial` = `tipos_pilares`.`idtipo_pilar`)
			  INNER JOIN `sys_status` ON (`prospecto_comercial`.`estatus` = `sys_status`.`id_status`) 
			WHERE 
			 (`prospecto_comercial`.`estatus` IN (6) OR  tracking_prospecto.`id_actividad`='CITA') 
			 AND prospectos.id_nit='". mysql_real_escape_string($id_nit) ."' 
			 GROUP BY  prospecto_comercial.id_comercial ";
	 
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){
				/*Tipo de clasificacion B = 4*/  
				$person->updateTipoClasificacion(3,$id_nit); 
			}
 
					
	}	
}

?>
