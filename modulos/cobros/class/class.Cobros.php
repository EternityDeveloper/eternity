<?php


class Cobros{
	private static $db_link;
	private $_data;
	private $token;
	private $fecha="";
	private static $instance;
	
	public function __construct($db_link=""){
		if ($db_link!=""){
			self::$db_link=$db_link;
			Cobros::$instance = $this;
		}
	} 
	public static function getInstance(){
		 if (!Cobros::$instance instanceof self) {
             Cobros::$instance = new Cobros();
        }
        return Cobros::$instance;
	}	
	
	public function setDB($db_link){
		self::$db_link=$db_link;
	}
	
	/*UTILIZADO EN CASO DE QUERER REALIZAR TRANSAPCIONES UN DIA DESPUES*/
	public function setFecha($fecha){
		$this->fecha=$fecha;
	}
	
	public function searchByContrato($documento){
		$documento=mysql_real_escape_string($documento);
		$SQL="SELECT sys_personas.id_nit,
				concat(sys_personas.primer_nombre,' ' ,
				sys_personas.segundo_nombre) as nombre,
				concat(sys_personas.primer_apellido,' ',
				sys_personas.segundo_apellido) as apellido,
				contratos.serie_contrato,
				contratos.no_contrato,
				concat(contratos.serie_contrato,' ',contratos.no_contrato) as contrato,
				empresa.EM_NOMBRE as empresa,
				sys_status.descripcion as estatus
			FROM `contratos`
			INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
			INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
			INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`) 
			WHERE (contratos.id_nit_cliente='".$documento."' or concat(contratos.serie_contrato,' ',contratos.no_contrato)='".$documento."' or concat(contratos.serie_contrato,contratos.no_contrato)='".$documento."' or  contratos.no_contrato='".$documento."' ) ";
		$rs=mysql_query($SQL);
		$data=array("valid"=>false,"data"=>array());
		while($row=mysql_fetch_assoc($rs)){
			$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']);
			unset($row['no_contrato']);
			unset($row['serie_contrato']);
			$row['contrato_id']=System::getInstance()->Encrypt(json_encode($contrato));
			$row['id_nit']=System::getInstance()->Encrypt($row['id_nit']);
			array_push($data['data'],$row);
			$data['valid']=true;
		}
		
		return $data;
	}
	
	public function validarCodigoGestion($codigo_gestion){
		$SQL="SELECT COUNT(*) AS total FROM `tipos_gestiones` WHERE `idtipogestion`='".mysql_real_escape_string($codigo_gestion)."'";
		$rs=mysql_query($SQL);
		$data=false;
		while($row=mysql_fetch_assoc($rs)){
			if ($row['total']>0){
				$data=true;
			} 
		}	 
		return $data;
	}
	
	public function validarCodigoAcccion($accion){
		$SQL="SELECT COUNT(*) AS total FROM `acciones_cobros` WHERE `idaccion`='".mysql_real_escape_string($accion)."'";
		$rs=mysql_query($SQL);
		$data=false;
		while($row=mysql_fetch_assoc($rs)){
			if ($row['total']>0){
				$data=true;
			} 
		}	 
		return $data;
	}	
	
	public function validarCodigoActividad($id_actividad,$id_gestion){
		$SQL="SELECT COUNT(*) AS total FROM `tipo_actividades` WHERE `idtipogestion`='".mysql_real_escape_string($id_gestion)."' and idtipoact='".mysql_real_escape_string($id_actividad)."'";
		 
		$rs=mysql_query($SQL);
		$data=false;
		while($row=mysql_fetch_assoc($rs)){
			if ($row['total']>0){
				$data=true;
			} 
		}	 
		return $data;
	}
	
	public function doGenerarRequerimiento($contrato,$mov,$cantidad,$motorisado,$oficial,
											$fecha_req,$direccion_cobro,$comentario=""){
		$msg=array("valid"=>false,"mensaje"=>"Error debe de completar los datos"); 
		if ($cantidad<0){
			return $msg;	
		} 
		if ((!isset($contrato->valor_cuota)) || (!isset($contrato->serie_contrato))
			|| (!isset($contrato->no_contrato))){
			return $msg;	
		}
		if ((!isset($mov->TIPO_MOV)) ){
			return $msg;	
		}	
		if ((!isset($motorisado->motorizado)) ){
			return $msg;	
		}	
		if ((!isset($oficial->oficial_nit)) ){
			return $msg;	
		}	
		SystemHtml::getInstance()->includeClass("caja","Caja");
		$caja= new Caja($this->db_link); 
		$tasa=$caja->getTasaActual($contrato->tipo_moneda);
 
		//$direccion_cobro=System::getInstance()
		//exit; 
		/*OFICIAL*/
		$obj_actividad=array();
		$auto=$this->getTotalActividadGestion();
		$id_gestion='AVICO'.$auto;
		$obj=new ObjectSQL();
		$obj->idtipogestion='AVICO';
		$obj->idgestion=$id_gestion;
		$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$obj->idtipoact='AVICO';
		$obj->autorizado="";
		$obj->responsable=$oficial->oficial_nit;
		$obj->id_status="1";	
		$obj->fecha_realizar="STR_TO_DATE('".$fecha_req."','%d-%m-%Y')";	
		$obj->orden_actividad=1;	
		$obj->setTable("actividades_gestion"); 
		$SQL=$obj->toSQL("insert"); 
	  	mysql_query($SQL);	 
 		
		/*MOTORISADO*/
		$id_gestion='MOTO'.$auto;
		$obj=new ObjectSQL();
		$obj->idtipogestion='AVICO';
		$obj->idgestion=$id_gestion;
		$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$obj->idtipoact='MOTO';
		$obj->autorizado="";
		$obj->responsable=$motorisado->motorizado;
		$obj->id_status="1";	
		$obj->fecha_realizar="STR_TO_DATE('".$fecha_req."','%d-%m-%Y')";	
		$obj->orden_actividad=2;	
		$obj->setTable("actividades_gestion"); 
		$SQL=$obj->toSQL("insert"); 
	 	mysql_query($SQL);	
  
		$docto=MVFactura::getInstance()->doCreateReciboRequerimiento($contrato->id_nit_cliente,
														$contrato->EM_ID,
														$contrato->no_contrato,
														$contrato->serie_contrato,
														$mov->TIPO_MOV,
														RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
														$motorisado->motorizado,
														$oficial->oficial_nit,
														($cantidad*($contrato->valor_cuota+$contrato->monto_penalizacion)),
														$cantidad,
														"STR_TO_DATE('".$fecha_req."','%d-%m-%Y')",
														$comentario,
														$tasa
													);	
 	 
		/*INSERTO LA LABOR DE COBRO*/
		$obj= new ObjectSQL(); 
		$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$obj->EM_ID=$contrato->EM_ID;
		$obj->no_contrato=$contrato->no_contrato;
		$obj->serie_contrato=$contrato->serie_contrato;
		$obj->contacto='';		
		$obj->observaciones='';	
		$obj->comentario_cliente=$comentario;
		//$obj->proximo_contacto="CONCAT(STR_TO_DATE('".$_REQUEST['lb_fecha_contacto']."','%d-%m-%Y'),' ','".$hora."')";		
		$obj->idaccion='MOTO'; 
		$obj->cuotas_acobrar=$cantidad;
		$obj->monto_acobrar=($cantidad*($contrato->valor_cuota+$contrato->monto_penalizacion));
		$obj->mora_acobrar='0';
		$obj->mante_acobrar=0;
		$obj->aviso_cobro=$docto->NO_DOCTO;
		$obj->serie=$docto->SERIE;
		$obj->fecha_cobro="STR_TO_DATE('".$fecha_req."','%d-%m-%Y')";		
		$obj->estatus=19; 
		$obj->oficial_cobro=UserAccess::getInstance()->getIDNIT();
		$obj->id_nit_cliente=$contrato->id_nit_cliente;
		$obj->id_direcciones=$direccion_cobro;
		$obj->setTable('labor_cobro');
		$SQL=$obj->toSQL("insert");  
		mysql_query($SQL);		
		
		
		$_obj= new ObjectSQL(); 
		$_obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$_obj->idgestion='AVICO'.$auto;
		$_obj->idtipogestion='AVICO';
		$_obj->responsable=UserAccess::getInstance()->getIDNIT();
 		$_obj->EM_ID=$contrato->EM_ID;
		$_obj->no_contrato=$contrato->no_contrato;
		$_obj->serie_contrato=$contrato->serie_contrato;
		$_obj->id_status="1";
		$_obj->descrip_general=$comentario; 
		$_obj->setTable('gestiones');
		$SQL=$_obj->toSQL("insert");   
		mysql_query($SQL);
									
		SysLog::getInstance()->Log($contrato->id_nit_cliente, 
									 $contrato->serie_contrato,
									 $contrato->no_contrato,
									 '',
									 '',
									 "REQUERIMIENTO DE COBRO ".$docto->SERIE." ".$docto->NO_DOCTO,
									 json_encode($docto),
									 'REQUERIMIENTO');	
										 									
		$msg=array("valid"=>true,"mensaje"=>"Requerimiento creado!"); 
		return $msg;
	}
	
	public function getListActividad($id_gestion){
		$SQL="SELECT * FROM `tipo_actividades` WHERE `idtipogestion`='".mysql_real_escape_string($id_gestion)."'";
		 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}
		
	public function getTotalActividadGestion(){
		$SQL="SELECT COUNT(*)+1 AS total FROM `actividades_gestion`";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			return $row['total']; 
		}	 
		return 0;
	}
	function getTotalOficialCall($extension,$fecha_actual,$fecha_final){  
 	
		$last_day=date('Y-m-d');  
		$db_elaxtix = mysql_connect("192.168.0.17","asterisk","H3lpd3sk34$");
		mysql_select_db("asteriskcdrdb",$db_elaxtix);
		if (strtotime($fecha_actual)>strtotime(date('Y-m-d'))){
			$fecha_actual=date('Y-m-d');
		}
		if (strtotime($fecha_final)<strtotime(date('Y-m-d'))){
			$last_day=$fecha_final;
		}		
				
		$SQL="SELECT  count(*) as llamadas ,
					(SUM(duration)/60) AS tiempo
			 FROM `cdr` 
			 WHERE  calldate BETWEEN '".$fecha_actual." 00:00:00' AND DATE_ADD('".$last_day." 23:59:59', INTERVAL 1 DAY) AND disposition='ANSWERED'  and src='".$extension."' GROUP BY src ";
	
		$rs=mysql_query($SQL,$db_elaxtix); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	
		 
		return $data; 
	}	
	public function getOficialesC(){
		$SQL="SELECT  
				oficial.id_nit,
				CONCAT(oficial.`primer_nombre`,' ',oficial.`segundo_nombre`,
					' ',oficial.`primer_apellido`,' ',oficial.segundo_apellido) AS nombre_oficial 
				 FROM `cobros_zona`
				INNER JOIN `sys_personas` AS oficial ON (`oficial`.id_nit=cobros_zona.oficial_nit) 
			GROUP BY oficial.id_nit	"; 
  
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;			
	}
	public function getMotorizadoC(){
		$SQL="SELECT  
				oficial.id_nit,
				CONCAT(oficial.`primer_nombre`,' ',oficial.`segundo_nombre`,
					' ',oficial.`primer_apellido`,' ',oficial.segundo_apellido) AS nombre_oficial 
				 FROM `cobros_zona`
				INNER JOIN `sys_personas` AS oficial ON (`oficial`.id_nit=cobros_zona.`motorizado`) 
			GROUP BY oficial.id_nit	"; 
  
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;			
	}		
	public function getGerentes(){
		$SQL="SELECT  
			gg.`codigo_gerente_grupo` AS codigo_gerente,
			CONCAT(oficial.`primer_nombre`,' ',oficial.`segundo_nombre`,
				' ',oficial.`primer_apellido`,' ',oficial.segundo_apellido) AS nombre_gerente 
			 FROM sys_gerentes_grupos AS gg
			INNER JOIN `sys_personas` AS oficial ON (`oficial`.id_nit=gg.id_nit AND gg.status=1)
			GROUP BY oficial.id_nit ";  
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;			
	}	
	public function getCobrosOficialXDia($filtro=array()){
		$SQL="SELECT  
			SUM(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MONTO,
			(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
			of.id_nit=cc.nit_oficial) AS OFICIAL,
			movimiento_factura.TIPO_MOV
			FROM 
				`movimiento_caja`	
			LEFT JOIN `cobros_contratos` AS cc ON (cc.serie_contrato=movimiento_caja.serie_contrato AND
			cc.no_contrato=movimiento_caja.no_contrato)
			INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
			INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV)
			WHERE  
				movimiento_caja.TIPO_DOC='RBC' AND 	
			movimiento_caja.ANULADO='N' 
			AND movimiento_factura.TIPO_MOV IN ('CT','CUOTA','DESESP','PREACT')
			 AND movimiento_caja.ID_CAJA!='C06'
			 AND movimiento_caja.id_usuario NOT IN ('dbatista','rsandoval','1','IVALDEZ','JEMERA','EHERRERA','DBAUTISTA') "; 
			
			if (count($filtro)==0){
				$SQL.=" AND movimiento_caja.FECHA=CURDATE()  ";	
			}else{
				if (isset($filtro['action'])){ 
					if ($filtro['action']=="filter_range_date"){		 
						if (isset($filtro['desde']) && isset($filtro['hasta'])){
							$SQL.=" AND movimiento_caja.FECHA  between'".$filtro['desde']."' AND '".$filtro['hasta']."'";
						}
					}				
				}
			} 
		$SQL.="	GROUP BY cc.nit_oficial";	

		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 

		return $data;			
	}	
	public function getCobrosMotorizadoXDia($filtro=array()){
		$SQL="SELECT  
			SUM(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MONTO,
			(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
			of.id_nit=cc.nit_motorizado) AS MOTORIZADO,
			movimiento_factura.TIPO_MOV
			FROM 
				`movimiento_caja`	
			LEFT JOIN `cobros_contratos` AS cc ON (cc.serie_contrato=movimiento_caja.serie_contrato AND
			cc.no_contrato=movimiento_caja.no_contrato)
			INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
			INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV)
			WHERE  
				movimiento_caja.TIPO_DOC='RBC' AND 	
			movimiento_caja.ANULADO='N' 
			AND movimiento_factura.TIPO_MOV IN ('CT','CUOTA','DESESP','PREACT')
			 AND movimiento_caja.ID_CAJA!='C06'
			 AND movimiento_caja.id_usuario NOT IN ('dbatista','rsandoval','1','IVALDEZ','JEMERA','EHERRERA','DESESP','DBAUTISTA') "; 
			
			if (count($filtro)==0){
				$SQL.=" AND movimiento_caja.FECHA=CURDATE()  ";	
			}else{
				if (isset($filtro['action'])){ 
					if ($filtro['action']=="filter_range_date"){		 
						if (isset($filtro['desde']) && isset($filtro['hasta'])){
							$SQL.=" AND movimiento_caja.FECHA  between'".$filtro['desde']."' AND '".$filtro['hasta']."'";
						}
					}				
				}
			} 
		$SQL.="	 GROUP BY cc.nit_motorizado  ";	
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 

		return $data;			
	}
	public function cambioDeOficialRecibo($serie,$no_docto,$oficial,$motorizado){
		SystemHtml::getInstance()->includeClass("caja","Recibos"); 
		$rcb= new Recibos($this->db_link); 
		$drb=$rcb->getMFReciboCaja($serie,$no_docto); 		
		if ($drb['valid']){
			$ob= new ObjectSQL();
			$ob->ID_NIT_MOTORIZADO=$motorizado;
			$ob->ID_NIT_OFICIAL=$oficial;
			$ob->setTable("movimiento_factura");
			$SQL=$ob->toSQL("update"," where `CAJA_NO_DOCTO`='". mysql_real_escape_string($no_docto) ."'
			 AND `CAJA_SERIE`='". mysql_real_escape_string($serie) ."'");
			mysql_query($SQL);
			SysLog::getInstance()->Log($drb['ID_NIT'], 
							 $drb['SERIE_CONTRATO'],
							 $drb['NO_CONTRATO'],
							 '',
							 '',
							 "CAMBIO DE MOTORIZADO/OFICIAL ",
							 json_encode($row),
							 'INFO',
							 $serie,
							 $no_docto);	 			
		}   
		return $drb['valid'];	
	}		
	public function cambioDeOficialContrato($contrato,$oficial,$motorizado){
		$valid=false;
		$SQL="SELECT COUNT(*) AS total FROM `cobros_contratos` WHERE  serie_contrato='".$contrato->serie_contrato."' 
				AND no_contrato='".$contrato->no_contrato."' ";	
		$rs=mysql_query($SQL);	
		$row=mysql_fetch_assoc($rs); 
		if ($row['total']>0){
			$ct= new ObjectSQL();
			$ct->nit_oficial=$oficial;
			if ($motorizado!=""){
				$ct->nit_motorizado=$motorizado;				
			}
			$ct->setTable("cobros_contratos");
			$SQL=$ct->toSQL('update',"  WHERE  serie_contrato='".$contrato->serie_contrato."' AND no_contrato='".$contrato->no_contrato."' ");
			mysql_query($SQL);
			$valid=true;
		}else{
			$ct= new ObjectSQL();
			$ct->nit_oficial=$oficial;
			if ($motorizado!=""){
				$ct->nit_motorizado=$motorizado;				
			}			
			$ct->serie_contrato=$contrato->serie_contrato;
			$ct->no_contrato=$contrato->no_contrato;
			$ct->setTable("cobros_contratos");
			$SQL=$ct->toSQL('insert');
			mysql_query($SQL);
			$valid=true;
		} 
		return $valid;	
	}		
	public function getOficial(){
		$SQL="SELECT 
		cobros_zona.oficial_nit,
		(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
		of.id_nit=cobros_zona.oficial_nit) AS OFICIAL,
		cobros_zona.`motorizado`,
		(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
		of.id_nit=cobros_zona.`motorizado`) AS MOTORIZADO
		FROM `cobros_zona` ";	
		$rs=mysql_query($SQL);
		$oficial=array();
		$moto=array();		
		while($row=mysql_fetch_assoc($rs)){
			$oficial[$row['oficial_nit']]=$row['OFICIAL'];
			$moto[$row['motorizado']]=$row['MOTORIZADO'];			
		}
		return array("oficial"=>$oficial,"motorizado"=>$moto);	
	}
	public function getOficialFromContato($serie_contrato,$no_contrato){
		$SQL="SELECT * FROM `cobros_contratos` WHERE CONCAT(serie_contrato,' ',no_contrato)='".$serie_contrato." ".$no_contrato."' limit 1";	
		$rs=mysql_query($SQL);
		$oficial=array(); 		
		while($row=mysql_fetch_assoc($rs)){
			$oficial=$row;	
		}
		return $oficial;	
	}

       public function getTodito(){

              $SQL="select * from todito";
              $rs=mysql_query($SQL);
              $data=array();
              while($row=mysql_fetch_assoc($rs)){
                    array_push($data,$row);
              }
              return $data;
        }     


	
	public function getDetalleCobroOficialMotorizado($filtro=array()){
		$SQL="SELECT 
		cobros_zona.oficial_nit,
		(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
		of.id_nit=cobros_zona.oficial_nit) AS OFICIAL,
		cobros_zona.`motorizado`,
		(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
		of.id_nit=cobros_zona.`motorizado`) AS MOTORIZADO
		FROM `cobros_zona` ";	
		$rs=mysql_query($SQL);
		$oficial=array();
		$moto=array();		
		while($row=mysql_fetch_assoc($rs)){
			$oficial[$row['oficial_nit']]=$row['OFICIAL'];
			$moto[$row['motorizado']]=$row['MOTORIZADO'];			
		}		 
			
		$SQL="SELECT * FROM (SELECT  
				(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MONTO,
 				CONCAT(ofi.primer_nombre,' ',ofi.segundo_nombre,' ',ofi.primer_apellido,' ',
				ofi.segundo_apellido) AS NOMBRE_CLIENTE,
				movimiento_factura.ID_NIT,
				movimiento_factura.serie_contrato,
				movimiento_factura.no_contrato,
				CONCAT(movimiento_factura.serie_contrato,' ',movimiento_factura.no_contrato) AS contrato,
				tipo_movimiento.`DESCRIPCION` AS TIPO_MOV,
				movimiento_factura.TIPO_MOV as TMOV,
				CONCAT(movimiento_caja.SERIE,'-',movimiento_caja.NO_DOCTO) AS DOCUMENTO,
				movimiento_factura.ID_NIT_MOTORIZADO as `nit_motorizado`,
				movimiento_factura.ID_NIT_OFICIAL as nit_oficial,
				movimiento_caja.SERIE,
				movimiento_caja.NO_DOCTO,
				cc.total_cliente 
				FROM 
					`movimiento_caja`	
				INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
	movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
				LEFT JOIN `cobros_contratos` AS cc ON (cc.serie_contrato=movimiento_factura.serie_contrato AND
	cc.no_contrato=movimiento_factura.no_contrato) 
				INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV)
				INNER JOIN  sys_personas AS ofi ON (ofi.id_nit=movimiento_caja.ID_NIT)
			WHERE  
				movimiento_caja.TIPO_DOC='RBC' AND 	
			movimiento_caja.ANULADO='N' 
			AND movimiento_factura.TIPO_MOV IN ('CT','CUOTA','DESESP','ABO','CAPITAL','PREACT')
			 AND movimiento_caja.ID_CAJA!='C06'
			 AND movimiento_caja.`NOTA_CD_NO_DOCTO` IS NULL
			 AND movimiento_caja.id_usuario NOT IN ('rsandoval','IVALDEZ','JEMERA','EHERRERA','SYNC','DBAUTISTA') 				 "; 
			
			if (count($filtro)==0){
				$SQL.=" AND movimiento_caja.FECHA=CURDATE()  ";	
			}else{
				if (isset($filtro['action'])){ 
					if ($filtro['action']=="filter_range_date"){	 	
						if (isset($filtro['desde']) && isset($filtro['hasta'])){
							$SQL.=" AND movimiento_caja.FECHA  between '".$filtro['desde']."' AND '".$filtro['hasta']."'";
						}
					}				
				}
			} 
		
		$SQL.="
	 
		 ) AS CT	  
			ORDER  BY CT.nit_motorizado,CT.nit_oficial,CT.serie_contrato,CT.no_contrato ";
			
			 
	 	  /*AND CONCAT(cc.serie_contrato,' ',cc.no_contrato) NOT in (SELECT CONCAT(serie_contrato,' ',no_contrato )FROM cobros_contratos)*/
/*		$SQL="SELECT * FROM ( 
SELECT  
(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MONTO,
CONCAT(ofi.primer_nombre,' ',ofi.segundo_nombre,' ',ofi.primer_apellido,' ',
ofi.segundo_apellido) AS NOMBRE_CLIENTE,
movimiento_factura.ID_NIT,
movimiento_factura.serie_contrato,
movimiento_factura.no_contrato,
CONCAT(movimiento_factura.serie_contrato,' ',movimiento_factura.no_contrato) AS contrato,
tipo_movimiento.`DESCRIPCION` AS TIPO_MOV,
movimiento_factura.TIPO_MOV AS TMOV,
CONCAT(movimiento_caja.SERIE,'-',movimiento_caja.NO_DOCTO) AS DOCUMENTO,
movimiento_factura.ID_NIT_MOTORIZADO AS `nit_motorizado`,
movimiento_factura.ID_NIT_OFICIAL AS nit_oficial,
movimiento_caja.SERIE,
movimiento_caja.NO_DOCTO
FROM 
	`movimiento_caja`	
INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV)
INNER JOIN  sys_personas AS ofi ON (ofi.id_nit=movimiento_caja.ID_NIT)
WHERE  
movimiento_caja.TIPO_DOC='RBC' AND 	
movimiento_caja.ANULADO='N' 
AND movimiento_factura.TIPO_MOV IN ('CT','CUOTA','DESESP','ABO','CAPITAL','PREACT')
AND movimiento_caja.ID_CAJA!='C06'
AND movimiento_caja.`NOTA_CD_NO_DOCTO` IS NULL
AND movimiento_caja.id_usuario NOT IN ('rsandoval','1','IVALDEZ','JEMERA','EHERRERA','SYNC','DBAUTISTA') 				
AND movimiento_caja.FECHA  BETWEEN '2015-06-01' AND '2015-06-30'
AND CONCAT(movimiento_caja.serie_contrato,' ',movimiento_caja.no_contrato)  NOT IN (SELECT CONCAT(serie_contrato,' ',no_contrato )FROM cobros_contratos)   
) AS CT";  */
		$rs=mysql_query($SQL);
		$data=array();
		$motorizado=array();
		$i=0;
		while($row=mysql_fetch_assoc($rs)){
			$oficial_n="";
			$motorizado_n="";
			if (isset($oficial[$row['nit_oficial']])){
				$oficial_n=$oficial[$row['nit_oficial']];
			}

			if (isset($moto[$row['nit_motorizado']])){
				$motorizado_n=$moto[$row['nit_motorizado']];
			}	 
			if (!isset($motorizado[trim($motorizado_n)])){
				$motorizado[trim($motorizado_n)]=array();
			}			
			 
			if (!isset($data[trim($oficial_n)])){
				$data[trim($oficial_n)]=array();   
			}  
			if (!isset($data[trim($oficial_n)][trim($motorizado_n)])){
				$data[trim($oficial_n)][trim($motorizado_n)]=array();				
			}	
						
			array_push($data[trim($oficial_n)][trim($motorizado_n)],$row); 
			array_push($motorizado[trim($motorizado_n)],$row);
			
		}	 
		return array("data"=>$data,"motorizado"=>$motorizado);			
	}			
	public function getDetalleLBC($oficial,$filtro=array()){
		
		
		$SQL="SELECT  
			SUM(mc.TIPO_CAMBIO*mc.MONTO) AS MONTO,
			mf.`ID_NIT_OFICIAL`,
			COUNT(*) AS cantidad,
			(SELECT asterisk_extension FROM usuarios WHERE 
				usuarios.id_nit=mf.ID_NIT_OFICIAL LIMIT 1) AS extension 
		FROM contratos 
		INNER JOIN `movimiento_caja` AS mc ON (contratos.serie_contrato=mc.serie_contrato AND 
			mc.no_contrato=contratos.no_contrato)
		INNER JOIN `labor_cobro` ON (labor_cobro.aviso_cobro=mc.NO_DOCTO AND labor_cobro.serie=mc.SERIE)	 
		INNER JOIN `movimiento_factura` AS mf ON (mf.SERIE=mc.SERIE AND mf.NO_DOCTO=mc.NO_DOCTO)
 		INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=mf.TIPO_MOV)
		WHERE  	mf.ID_NIT_OFICIAL IN (SELECT oficial_nit FROM `cobros_zona`) ";	
	 
		if (count($filtro)==0){
			$SQL.=" AND mc.FECHA_DOC=CURDATE()  ";	
		}else{
			if (isset($filtro['action'])){ 
				if ($filtro['action']=="filter_range_date"){		 
					if (isset($filtro['desde']) && isset($filtro['hasta'])){
						$SQL.=" AND date_format(mc.FECHA_DOC,'%Y-%m-%d') between'".$filtro['desde']."' AND '".$filtro['hasta']."'";
 					}
				}				
			}
		} 
		$SQL.=" 	GROUP BY mf.ID_NIT_OFICIAL ";
		$rs=mysql_query($SQL);
		$requermiento=array();
		while($row=mysql_fetch_assoc($rs)){
			$oficial_n="";  
			if (isset($row['ID_NIT_OFICIAL'])){
				$oficial_n=$row['ID_NIT_OFICIAL'];
				
			}  	 
			$requermiento[trim($oficial_n)]=$row;  
		} 
	 
 	 
		$SQL="SELECT 
			cobros_zona.oficial_nit,
			(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
			of.id_nit=cobros_zona.oficial_nit) AS OFICIAL,
			cobros_zona.`motorizado`,
			(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
			of.id_nit=cobros_zona.`motorizado`) AS MOTORIZADO 
			FROM `cobros_zona` ";	
		$rs=mysql_query($SQL);
		$oficial=array();
		$moto=array();		
		while($row=mysql_fetch_assoc($rs)){
			$oficial[$row['oficial_nit']]=$row['OFICIAL'];
			$moto[$row['motorizado']]=$row['MOTORIZADO'];			
		}	  
  
		$SQL="SELECT 
					SUM(MONTO) AS MONTO, 
					nit_oficial,
					extension
				FROM (SELECT  
				(SELECT asterisk_extension FROM usuarios WHERE
					 usuarios.id_nit=movimiento_factura.ID_NIT_OFICIAL LIMIT 1) AS extension,
				SUM(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MONTO, 
				movimiento_factura.ID_NIT,
				movimiento_factura.serie_contrato,
				movimiento_factura.no_contrato,
				CONCAT(movimiento_factura.serie_contrato,' ',movimiento_factura.no_contrato) AS contrato,
				tipo_movimiento.`DESCRIPCION` AS TIPO_MOV,
				movimiento_factura.TIPO_MOV as TMOV,
				CONCAT(movimiento_caja.SERIE,'-',movimiento_caja.NO_DOCTO) AS DOCUMENTO,
				movimiento_factura.ID_NIT_MOTORIZADO as `nit_motorizado`,
				movimiento_factura.ID_NIT_OFICIAL as nit_oficial,
				movimiento_caja.SERIE,
				movimiento_caja.NO_DOCTO
				FROM 
					`movimiento_caja`	
				INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
	movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
				LEFT JOIN `cobros_contratos` AS cc ON (cc.serie_contrato=movimiento_factura.serie_contrato AND
	cc.no_contrato=movimiento_factura.no_contrato)
				INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV) 
			WHERE  
				movimiento_caja.TIPO_DOC='RBC' AND 	
			movimiento_caja.ANULADO='N' 
			AND movimiento_factura.TIPO_MOV IN ('CT','CUOTA','DESESP','ABO','PREACT')
			 AND movimiento_caja.ID_CAJA!='C06'
			 AND movimiento_caja.id_usuario NOT IN ('rsandoval','1','IVALDEZ','JEMERA','EHERRERA','SYNC','DBAUTISTA') "; 
			
			if (count($filtro)==0){
				$SQL.=" AND movimiento_caja.FECHA=CURDATE()  ";	
			}else{
				if (isset($filtro['action'])){ 
					if ($filtro['action']=="filter_range_date"){	 	
						if (isset($filtro['desde']) && isset($filtro['hasta'])){
							$SQL.=" AND movimiento_caja.FECHA  between '".$filtro['desde']."' AND '".$filtro['hasta']."'";
						}
					}				
				}
			} 
		
		$SQL.="
			 GROUP BY movimiento_caja.id_nit
		 ) AS CT
			 GROUP BY   
				nit_oficial	 ";  
 	 
		$rs=mysql_query($SQL);
		$data=array();
		$motorizado=array();
		$i=0;
		while($row=mysql_fetch_assoc($rs)){
			$oficial_n="";  
			if (isset($oficial[$row['nit_oficial']])){
				$oficial_n=$oficial[$row['nit_oficial']];
			}  	 
			if (!isset($motorizado[trim($motorizado_n)])){
				$motorizado[trim($motorizado_n)]=array();
			}	 
			if (!isset($data[trim($oficial_n)])){
				//$data[trim($oficial_n)]=array();   
				$data[$row['nit_oficial']]=array(); 
			}   
			 
			$row['TOTAL_REQUERIMIENTO']=$requermiento[$row['nit_oficial']]['cantidad'];
			$row['NOMBRE_OFICIAL']=$oficial[$row['nit_oficial']];
		//	$data[trim($oficial_n)]=$row; 			
			$data[$row['nit_oficial']]=$row; 
		//	array_push($data[trim($oficial_n)][trim($motorizado_n)],$row); 
		//	array_push($motorizado[trim($motorizado_n)],$row); 
		} 
		
		$SQL="SELECT  
					usuarios.asterisk_extension,
					of.id_nit,
					CONCAT(of.primer_nombre,' ',
						of.segundo_nombre,' ',
						of.primer_apellido,' ',
						of.segundo_apellido) AS nombre	
				FROM usuarios 
				INNER JOIN `sys_personas` AS of ON (`of`.id_nit=usuarios.id_nit)
				INNER JOIN `cobros_zona` ON (`cobros_zona`.OFICIAL_NIT=of.id_nit)
				WHERE
					usuarios.asterisk_extension!=''
				GROUP BY 
					usuarios.asterisk_extension";
		$rs= mysql_query($SQL);
		$_data=array();
		//$data[$row['nit_oficial']]
		while($row=mysql_fetch_assoc($rs)){ 
			if (!isset($_data[$row['id_nit']])){
				$_data[$row['nombre']]=array(
								"MONTO"=>0,
								"nit_oficial"=>0,
								"extension"=>0,
								"TOTAL_REQUERIMIENTO"=>0,
								"NOMBRE_OFICIAL"=>$row['nombre']
							);
			}
			
			if (isset($data[$row['id_nit']]['NOMBRE_OFICIAL'])){
				$_data[$row['nombre']]['MONTO']=$data[$row['id_nit']]['MONTO'];
				$_data[$row['nombre']]['nit_oficial']=$data[$row['id_nit']]['nit_oficial'];
				$_data[$row['nombre']]['extension']=$data[$row['id_nit']]['extension']; 	
				$_data[$row['nombre']]['TOTAL_REQUERIMIENTO']=$data[$row['id_nit']]['TOTAL_REQUERIMIENTO']; 											
			} 
		} 
		return $data;			
	}
	public function getGestiones($oficial){
		$SQL="SELECT 
					gestiones.*,
					empresa.*,
					CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido) AS nombre_oficial,
					CONCAT(CLIENTE.`primer_nombre`,' ',CLIENTE.`segundo_nombre`,' ',CLIENTE.`primer_apellido`,' ',CLIENTE.segundo_apellido) AS nombre_cliente,
					CONCAT(`contratos`.`serie_contrato`,' ',contratos.no_contrato) AS contrato,
					sys_status.`descripcion` AS estatus
			 FROM `gestiones`
				INNER JOIN sys_personas  AS OFICIAL ON (`OFICIAL`.`id_nit`=gestiones.`responsable`) 
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=gestiones.`EM_ID`) 
				INNER JOIN `contratos` ON (`contratos`.`serie_contrato`=gestiones.serie_contrato 
							AND contratos.no_contrato=gestiones.no_contrato)
				INNER JOIN sys_personas  AS CLIENTE ON (`CLIENTE`.`id_nit`=contratos.`id_nit_cliente`) 
				INNER JOIN `sys_status` ON (sys_status.`id_status`=gestiones.id_status)	
			WHERE gestiones.id_status!=18  and gestiones.responsable='".$oficial."' ";
		$SQL="SELECT 
					actividades_gestion.*,
					empresa.*, 
					contratos.id_nit_cliente,
					CONCAT(`contratos`.`serie_contrato`,' ',contratos.no_contrato) AS contrato,
					sys_status.`descripcion` AS estatus,
					tipo_actividades.`actividad`,
					CONCAT(CLIENTE.`primer_nombre`,' ',CLIENTE.`segundo_nombre`,' ',CLIENTE.`primer_apellido`,' ',CLIENTE.segundo_apellido) AS cliente
				FROM `gestiones`
				INNER JOIN actividades_gestion ON (`actividades_gestion`.`idgestion`=gestiones.`idgestion`)
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=gestiones.`EM_ID`) 
				INNER JOIN `contratos` ON (`contratos`.`serie_contrato`=gestiones.serie_contrato 
							AND contratos.no_contrato=gestiones.no_contrato)
				INNER JOIN sys_personas AS CLIENTE ON (CLIENTE.id_nit=contratos.id_nit_cliente)			
				 INNER JOIN `sys_status` ON (sys_status.`id_status`=gestiones.id_status)
				INNER JOIN `tipo_actividades` ON (tipo_actividades.`idtipoact`=actividades_gestion.idtipoact)
				WHERE   gestiones.responsable='".$oficial."'  AND gestiones.id_status!=18 AND actividades_gestion.id_status!=18
				GROUP BY actividades_gestion.idgestion ";	 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}
	/*META DE COBROS DE UN OFICIAL*/
	public function getMetaOficial($oficial,$filter=array()){
		$SQL="SELECT cobros_contratos.*,
empresa.*,
CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido) AS nombre_oficial,
CONCAT(CLIENTE.`primer_nombre`,' ',CLIENTE.`segundo_nombre`,' ',CLIENTE.`primer_apellido`,' ',CLIENTE.segundo_apellido) AS nombre_cliente,
CONCAT(`contratos`.`serie_contrato`,' ',contratos.no_contrato) AS contrato,
contratos.* 
FROM cobros_contratos 
INNER JOIN `contratos` ON (`contratos`.`serie_contrato`=cobros_contratos.serie_contrato 
AND contratos.`no_contrato`=cobros_contratos.no_contrato)
INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`) 
INNER JOIN sys_personas  AS OFICIAL ON (`OFICIAL`.`id_nit`=cobros_contratos.`nit_oficial`) 
INNER JOIN sys_personas  AS CLIENTE ON (`CLIENTE`.`id_nit`=contratos.`id_nit_cliente`)	
LEFT JOIN sys_personas  AS MOTORIZADO ON (`MOTORIZADO`.`id_nit`=cobros_contratos.`nit_motorizado`)		
WHERE cobros_contratos.nit_oficial='232323232323' ";
		 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}			
	
	public function getActividades($oficial){
		$SQL="SELECT 
			actividades_gestion.*,
			empresa.*, 
			(SELECT CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido)
			FROM sys_personas  AS OFICIAL WHERE `OFICIAL`.`id_nit`=gestiones.`responsable`)  AS nombre_oficial,
			 
			(SELECT CONCAT(CLIENTE.`primer_nombre`,' ',CLIENTE.`segundo_nombre`,' ',CLIENTE.`primer_apellido`,' ',CLIENTE.segundo_apellido)
			FROM sys_personas  AS CLIENTE WHERE `CLIENTE`.`id_nit`=contratos.`id_nit_cliente`)  AS nombre_cliente,
			CONCAT(`contratos`.`serie_contrato`,' ',contratos.no_contrato) AS contrato,
			sys_status.`descripcion` AS estatus,
			tipo_actividades.`actividad`
		FROM `gestiones`
		INNER JOIN actividades_gestion ON (`actividades_gestion`.`idgestion`=gestiones.`idgestion`)
		INNER JOIN `empresa` ON (`empresa`.`EM_ID`=gestiones.`EM_ID`) 
		INNER JOIN `contratos` ON (`contratos`.`serie_contrato`=gestiones.serie_contrato 
					AND contratos.no_contrato=gestiones.no_contrato)
		 INNER JOIN `sys_status` ON (sys_status.`id_status`=gestiones.id_status)
		INNER JOIN `tipo_actividades` ON (tipo_actividades.`idtipoact`=actividades_gestion.idtipoact)
		where   gestiones.responsable='".$oficial."' AND gestiones.id_status!=18 AND actividades_gestion.id_status!=18
		GROUP BY actividades_gestion.idgestion  ";
		 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}

	public function getDetalleCarteraCobrada($oficial){
		$SQL="SELECT 
			SUM(MONTO) AS MONTO,
			SUM(NO_CONTRATO) AS NO_CONTRATO,
			(NO_CLIENTE) AS NO_CLIENTES
		FROM (SELECT  
			SUM(MONTO) AS MONTO,
			COUNT(contrato) AS NO_CONTRATO,
			count(NO_CLIENTE)  AS NO_CLIENTE
		 FROM  (SELECT  
			(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MONTO, 
			movimiento_factura.ID_NIT,
			movimiento_factura.serie_contrato,
			movimiento_factura.no_contrato,
			CONCAT(movimiento_factura.serie_contrato,' ',movimiento_factura.no_contrato) AS contrato,
			tipo_movimiento.`DESCRIPCION` AS TIPO_MOV,
			CONCAT(movimiento_caja.SERIE,'-',movimiento_caja.NO_DOCTO) AS DOCUMENTO,
			movimiento_factura.ID_NIT_MOTORIZADO AS `nit_motorizado`,
			movimiento_factura.ID_NIT_OFICIAL AS nit_oficial,
			movimiento_caja.SERIE,
			movimiento_caja.NO_DOCTO,
			cc.total_cliente AS NO_CLIENTE
			FROM 
				`movimiento_caja`	
			INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
		movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
			LEFT JOIN `cobros_contratos` AS cc ON (cc.serie_contrato=movimiento_factura.serie_contrato AND
		cc.no_contrato=movimiento_factura.no_contrato)
			INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV) 
		WHERE  
			movimiento_caja.TIPO_DOC='RBC' AND movimiento_caja.ANULADO='N' 
		AND movimiento_factura.TIPO_MOV IN ('CT','CUOTA','DESESP','ABO','CAPITAL','PREACT')
		 AND movimiento_caja.ID_CAJA!='C06'
		 AND movimiento_caja.`NOTA_CD_NO_DOCTO` IS NULL
		 AND movimiento_caja.id_usuario NOT IN ('rsandoval','IVALDEZ','JEMERA','EHERRERA','SYNC','DBAUTISTA')  ";
		  
		//WHERE contratos.serie_contrato not in ('CO','CT') 
		if (UserAccess::getInstance()->getIfAccessPageById(184)){
			
		}else{
			$SQL.=" AND movimiento_factura.ID_NIT_OFICIAL='".$oficial."' ";
			//$SQL.=" AND   cobros_contratos.nit_oficial='".$oficial."' ";
		}	
             /* AQUI CAMBIO EL RANGO DE FECHA QUE MUESTRA EL DASHBOARD DE COBROS */
		 $SQL.=" AND movimiento_caja.FECHA  BETWEEN DATE_FORMAT(CURDATE(),'%Y-%m-02')  AND LAST_DAY(CURDATE()) ) AS DT
                ) AS FT ";
//                   $SQL.=" AND movimiento_caja.FECHA  BETWEEN DATE_FORMAT('2020-07-01','%Y-%m-01') AND DATE_FORMAT('2020-07-31','%Y-%m-31') ) AS DT
//		 ) AS FT ";
		
	  
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row;
		}	 
		return $data;
			
	}
	
	public function detalleCartera($oficial){
		//cobros_contratos_oct as 
		$SQL="SELECT 
				SUM(contrato) AS total_contratos,
				COUNT(*) AS total_clientes,
				SUM(monto_meta) AS monto_meta
			 FROM (SELECT COUNT(*) AS contrato,
				  contratos.`id_nit_cliente`,
		 (SUM(cobros_contratos.`saldo_0_30`)+SUM(cobros_contratos.`saldo_31_60`)+SUM(cobros_contratos.`saldo_61_90`)+
				  SUM(cobros_contratos.`saldo_91_120`)+SUM(cobros_contratos.`saldo_mas_120`)) AS monto_meta
			FROM  cobros_contratos
			INNER JOIN contratos ON  (cobros_contratos.serie_contrato=contratos.serie_contrato AND 
			cobros_contratos.no_contrato=contratos.no_contrato )
		  where  1=1 ";
		   
		if (UserAccess::getInstance()->getIfAccessPageById(184)){
			$SQL.=" ";
		}else{
			$SQL.=" AND   cobros_contratos.nit_oficial='".$oficial."' ";
		}	 
		$SQL.="
			GROUP BY contratos.`id_nit_cliente` ) AS clientes ";
		 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row;
		}	 
		return $data;
			
	}
	public function detalle_desestimiento_anulacion($oficial){
		//cobros_contratos_oct as 
		$SQL="SELECT 
				SUM(ACTIVOS) AS ACTIVOS, 
				SUM(POSIBLE_DES) AS POSIBLE_DES, 
				SUM(POSIBLE_ANUL) AS POSIBLE_ANUL
			 FROM (
			SELECT 
				IF (contratos.estatus=1,COUNT(*),0) AS ACTIVOS,
				IF (contratos.estatus=23,COUNT(*),0) AS POSIBLE_DES,
				IF (contratos.estatus=28,COUNT(*),0) AS POSIBLE_ANUL
			FROM  cobros_contratos
			INNER JOIN contratos ON  (cobros_contratos.serie_contrato=contratos.serie_contrato AND 
			cobros_contratos.no_contrato=contratos.no_contrato )
			WHERE  1=1";
		   
		if (UserAccess::getInstance()->getIfAccessPageById(184)){
			$SQL.=" ";
		}else{
			$SQL.=" AND   cobros_contratos.nit_oficial='".$oficial."' ";
		}	 
		$SQL.=" GROUP BY contratos.estatus ) AS clientes ";
	 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row;
		}	 
		return $data;
			
	}	
	public function getCarteraAsignadaOficial($oficial_,$filter=array()){
		/*
		$SQL="SELECT 
		cobros_zona.oficial_nit,
		(SELECT CONCAT(of.primer_nombre,' ',of.segundo_nombre,' ',of.primer_apellido,' ',of.segundo_apellido) AS nombre FROM `sys_personas` AS of WHERE 
		of.id_nit=cobros_zona.oficial_nit) AS OFICIAL,
		cobros_zona.`motorizado`
		FROM `cobros_zona` ";	
		$rs=mysql_query($SQL);
		$oficial=array();
		$moto=array();		
		while($row=mysql_fetch_assoc($rs)){
			//
			$oficial[$row['oficial_nit']]=$row['OFICIAL'];
			$moto[$row['motorizado']]=$row['motorizado'];			
		}	*/



                $actualiza="update cobros_contratos c1 set c1.fecha_ultimo_pago=(
                            select date_format(max(c2.fecha_doc),'%y-%m-%d') from movimiento_caja c2 inner join movimiento_contrato c3 on c3.caja_serie = c2.serie and c3.no_docto = c2.no_docto
                            where c2.serie_contrato=c1.serie_contrato and c2.no_contrato = c1.no_contrato and c2.anulado = 'N' )";
                mysql_query($actualiza);

                 $actualiza2="update cobros_contratos
                             set total_pagos_realizado = 1
                             where month(now()) = month(fecha_ultimo_pago) and year(now()) = year(fecha_ultimo_pago)";
                mysql_query($actualiza2); 
				
		$SQL="SELECT 
				*,
				DATEDIFF(fecha_proximo_contacto,CURDATE()) AS TIME_DIFERENCE
				FROM (SELECT * FROM (SELECT 
				nombre_cliente,
				nombre_motorizado,
				nombre_oficial,
				dias_vencidos,
				cobros_contratos.categoria,
				cobros_contratos.`total_cliente`,
				cobros_contratos.`nit_oficial`,
				cobros_contratos.`nit_motorizado`,
				cobros_contratos.`zona_id`,
				cobros_contratos.`ano` AS cb_ano,
				cobros_contratos.`mes` AS cb_mes,
				cobros_contratos.`iddistintivo`,
				cobros_contratos.`monto_cobro`,
				cobros_contratos.`cuotas_cobro`,
				cobros_contratos.`saldo_0_30`,
				cobros_contratos.`saldo_31_60`,
				cobros_contratos.`saldo_61_90`,
				cobros_contratos.`saldo_91_120`,
				cobros_contratos.`saldo_mas_120`,
				cobros_contratos.`saldo_mora`,
				cobros_contratos.`saldo_mante`,
				cobros_contratos.`mora_cobro`,
				cobros_contratos.`mante_cobro`,
				cobros_contratos.total_pagos_realizado as pagos_realiz,
				contratos.*, 
				sys_status.descripcion AS estatus_name,
				cmc.cmc_descripcion  AS forpago_name ,
				(`cobros_contratos`.`saldo_0_30`+
				`cobros_contratos`.`saldo_31_60`+
				`cobros_contratos`.`saldo_61_90`+ 
				`cobros_contratos`.`saldo_91_120`+
				`cobros_contratos`.`saldo_mas_120`+
				`cobros_contratos`.`saldo_mora`+
				`cobros_contratos`.`saldo_mante`+
				`cobros_contratos`.`mora_cobro`+
				`cobros_contratos`.`mante_cobro` ) AS monto_a_cobrar,
				(SELECT (CASE 
				WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
				WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
				FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
				pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) AS producto,				
				empresa.`EM_NOMBRE`,
				cobros_contratos.cuotas_acobrar  AS cuotas_cobradas, 
				(CASE 
					WHEN cobros_contratos.proximo_contacto  IS NOT NULL THEN cobros_contratos.proximo_contacto     
					WHEN cobros_contratos.proximo_contacto  IS NULL THEN STR_TO_DATE(CONCAT(DATE_FORMAT(CURDATE(),'%Y-%m-'),DAY(fecha_primer_pago)),'%Y-%m-%d') 
				  END ) AS fecha_proximo_contacto,
		  
				cobros_contratos.comentario_cliente,
		
				STR_TO_DATE(CONCAT(DATE_FORMAT(CURDATE(),'%Y-%m-'),DAY(fecha_primer_pago)),'%Y-%m-%d') AS FECHA_PAGO,
				(CASE 
					WHEN fecha_ultimo_pago IS NULL THEN TIMESTAMPDIFF(MONTH,fecha_primer_pago,LAST_DAY(CURDATE()))   
					ELSE
					TIMESTAMPDIFF(MONTH,fecha_ultimo_pago,LAST_DAY(CURDATE()))
				  END ) AS diferencia_en_mes,
				  
				  (CASE 
					WHEN fecha_ultimo_pago IS NULL THEN fecha_primer_pago  
					ELSE fecha_ultimo_pago
				  END ) AS fecha_ultimo_pago,
				  DAY(contratos.fecha_primer_pago) as DIA_DE_COMPROMISO,
				  TIMESTAMPDIFF(MONTH,contratos.fecha_venta,LAST_DAY(CURDATE())) as fecha_v 
			FROM  cobros_contratos 
			INNER JOIN contratos ON  (cobros_contratos.serie_contrato=contratos.serie_contrato AND 
			cobros_contratos.no_contrato=contratos.no_contrato ) 
 			INNER JOIN `sys_status` ON (sys_status.`id_status`=contratos.estatus) 
			LEFT JOIN `contratos_metodo_cobro` AS cmc ON (cmc.`cmc_codigo`=contratos.forpago)
			LEFT JOIN `empresa` ON (empresa.EM_ID=contratos.EM_ID)
			where  1=1 ";
			
			if (isset($filter['por_estatus'])){
				$SQL.=" AND contratos.estatus in ('".$filter['por_estatus']	."') ";
			}
			if (count($filter['oficial'])>0){
				$str="";
				foreach($filter['oficial'] as $key=>$val){
					$str.="'".$val."',";
				}
	 
				$str=substr($str,0,strlen($str)-1);
				if ($str!=""){
					$SQL.=" AND cobros_contratos.nit_oficial IN (".$str.") ";
				}
			}
			
			if (count($filter['motorizado'])>0){
				$str="";
				foreach($filter['motorizado'] as $key=>$val){
					$str.="'".$val."',";
				}
	 
				$str=substr($str,0,strlen($str)-1);
				if ($str!=""){
					$SQL.=" AND cobros_contratos.nit_motorizado IN (".$str.") ";
				}
			}	
					
			if (count($filter['gerente'])>0){
				$str="";
				foreach($filter['gerente'] as $key=>$val){
					$str.="'".$val."',";
				}
	 
				$str=substr($str,0,strlen($str)-1);
				if ($str!=""){
					$SQL.=" AND contratos.codigo_gerente IN (".$str.") ";
				}
			}		
		 
			
			if (count($filter['por_saldos'])>0){
				$QUERY=""; 
				foreach($filter['por_saldos'] as $key=>$val){ 
					if (isset($val)){  
						switch($val){
							case "saldo_0_30":
								$QUERY.=" (saldo_0_30>0 and saldo_31_60<=0 and saldo_61_90<=0 and  saldo_91_120<=0 and saldo_mas_120<=0)  or";
							break;	
							case "saldo_31_60":
								$QUERY.=" (saldo_31_60>0 and saldo_61_90<=0 and  saldo_91_120<=0 and saldo_mas_120<=0)  or";
							break;	
							case "saldo_61_90":
								$QUERY.=" (saldo_61_90>0  and saldo_91_120<=0 and saldo_mas_120<=0)  or";
							break;	
							case "saldo_91_120":
								$QUERY.=" (saldo_91_120>0 and saldo_mas_120<=0) or";
							break;	
							case "saldo_mas_120":
								$QUERY.=" (saldo_mas_120>0) or";
							break;	 																					
						}
						
					} 						
				}
				$QUERY=substr($QUERY,0,strlen($QUERY)-2);
				$SQL.=" and (".$QUERY.")";
 			}			
			 
			if (UserAccess::getInstance()->getIfAccessPageById(184)){
				$SQL.=" ";
			}else{
				/*SI NO TIENE EL PERMISO DE GERENTES DE VER LA CARTERA ENTONCES FILTRALO POR
				OFICIAL*/
				if (!UserAccess::getInstance()->getIfAccessPageById(99)){
					$SQL.=" and  cobros_contratos.nit_oficial='".$oficial_."' ";
				}
			}
			
			if (isset($filter['tipo_cuota'])){
				$SQL.=" and  cobros_contratos.categoria='".$filter['tipo_cuota']."' ";	
			}
		  
			 	 
			if (isset($filter['por_compromiso']) && isset($filter['monto_compromiso'])){ 
				switch($filter['por_compromiso']){
					case "MQ":
						$SQL.=" AND contratos.valor_cuota >'".$filter['monto_compromiso']."' ";
					break;	
					case "MIGQ":
						$SQL.=" AND contratos.valor_cuota >='".$filter['monto_compromiso']."' ";
					break;	
					case "MNQ":
						$SQL.=" AND contratos.valor_cuota <'".$filter['monto_compromiso']."' ";
					break;	
					case "MNIGQ":
						$SQL.=" AND contratos.valor_cuota <='".$filter['monto_compromiso']."' ";
					break;	
					case "IQ":
						$SQL.=" AND contratos.valor_cuota='".$filter['monto_compromiso']."' ";
					break;	
				}
			
			}  
			if (isset($filter['por_forma_pago'])){
				$SQL.=" AND cmc.cmc_codigo='". mysql_real_escape_string($filter['por_forma_pago']) ."' ";
			}
			
			if (isset($filter['pendiente_de_pago'])){
				$SQL.=" AND total_pagos_realizado<=0 ";
			}
			if (isset($filter['contrato_condicion'])){
				switch($filter['contrato_condicion']){
					case "MQ":
						$SQL.=" AND contratos.cuotas >'".$filter['contrato_cuota']."' ";
					break;	
					case "MIGQ":
						$SQL.=" AND contratos.cuotas >='".$filter['contrato_cuota']."' ";
					break;	
					case "MNQ":
						$SQL.=" AND contratos.cuotas <'".$filter['contrato_cuota']."' ";
					break;	
					case "MNIGQ":
						$SQL.=" AND contratos.cuotas <='".$filter['contrato_cuota']."' ";
					break;	
					case "IQ":
						$SQL.=" AND contratos.cuotas='".$filter['contrato_cuota']."' ";
					break;	
				}			
			//	$SQL.=" AND cmc.cmc_codigo='". mysql_real_escape_string($filter['por_forma_pago']) ."' ";
			}			
			
			
		 //	$SQL.=" AND contratos.serie_contrato not in ('CO','CT') ";
			
			$SQL.=" 
			) AS COBROS ) AS dt
			";
		  
		if (isset($filter['desde']) && isset($filter['hasta'])){
			 $SQL.=" WHERE  STR_TO_DATE(fecha_proximo_contacto,'%Y-%m-%d')  BETWEEN STR_TO_DATE('".$filter['desde']."','%d-%m-%Y') AND  STR_TO_DATE('".$filter['hasta']."','%d-%m-%Y') ";
		}  
		$SQL.="  
 			 ORDER BY fecha_proximo_contacto desc  "; 
 	 
	 
		$rs=mysql_query($SQL);
		$data=array();
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		$person= new PersonalData($this->db_link,array());
		while($row=mysql_fetch_assoc($rs)){ 
 			$phoneData=$person->getPhone($row['id_nit_cliente']);
			$phone="";
			foreach($phoneData as $key=>$val){   
				$val=(array)$val;
				$phone.=$val['area'].$val['numero']; 
				if ($val['tipo']==2){
					$phone.=" Ext.".$val['extencion'];
				}
				$phone.=", ";  
			}
			$phone=substr($phone,0, strlen($phone)-2);	
			$addressData=$person->getAddress($row['id_nit_cliente']);	
 			foreach($addressData as $key=>$val){  
				$val=(array)$val; 
  				$direccion=$val['sector'];
				$direccion.=trim($val['avenida'])!=""?",".$val['avenida']:'';
				$direccion.=trim($val['calle'])!=""?",".$val['calle']:'';
				$direccion.=trim($val['zona'])!=""?",".$val['zona']:'';
				$direccion.=trim($val['manzana'])!=""?",".$val['manzana']:'';
				$direccion.=trim($val['numero'])!=""?",".$val['numero']:'';
				$direccion.=trim($val['referencia'])!=""?",".$val['referencia']:'';
				$direccion.=trim($val['observaciones'])!=""?",".$val['observaciones']:''; 
				if ($val['tipo']=="Cobro"){
					break;	
				}
			}	 
					
			$row['NOMBRE_OFICIAL']= $row['nombre_oficial'];
			$row['NOMBRE_MOTORIZADO']=$row['nombre_motorizado'];
			$row['TELEFONO']=$phone;
			$row['direccion_cobro']=$direccion;
			 
			array_push($data,$row);
		}	 
		
		
		return $data;
	}	
	/*Retorna un array de las cuotas que estan pendientes por cobrar*/
	function getListCuotasContratos($contrato_obj){ 
		$cuota=$this->getLastNumeroCuotaCobrada($contrato_obj->serie_contrato,$contrato_obj->no_contrato);
		$c_list=$this->getAllCuotasPagas($contrato_obj->serie_contrato,$contrato_obj->no_contrato);
 		
		$sp=explode("-",$contrato_obj->fecha_primer_pago);
 		
		$data=array(
			"fecha"=>"",
			"fecha_vence"=>"",
			"no_cuota"=>"",
			"monto_neto"=>"",
			"capital"=>"0",
			"intfincmt"=>"0",
			"intcuota"=>"0",
			"intmora"=>"0",
			"monto_ref"=>0,
			"monto"=>"0",
			"gestion"=>"0",
			'tipo_cambio'=>1
		);
		$rt=array(); 
		
		for($i=1;$i<=$contrato_obj->cuotas;$i++){  

			if ($i>$cuota['nocuota']){ 
				$data["fecha"]=date("Y-m-d",strtotime($contrato_obj->fecha_primer_pago." ".$i." month"));
				$data["fecha_vence"]=date("Y-m-d",strtotime($contrato_obj->fecha_primer_pago." ".($i+1)." month"));
				$data["no_cuota"]=$i;
				$data["monto_neto"]=$contrato_obj->valor_cuota;  
				$data["monto"]=$contrato_obj->valor_cuota+$contrato_obj->monto_penalizacion; 
				$data["tipo_cambio"]=$contrato_obj->tipo_cambio; 
				$data['monto_ref']=$contrato_obj->monto_penalizacion; 
				
			
				$interes=$contrato_obj->interes/$contrato_obj->cuotas;
				$capital=$contrato_obj->valor_cuota-$interes;
				$monto=$interes+$capital;
				 				
				if (isset($c_list[$i])){
					$data["monto_neto"]=$contrato_obj->valor_cuota-$c_list[$i]['MONTO'];
					$n_capital=$capital*$cuota['nocuota'];
					$capital=$capital-$n_capital;
				}

		 		$data["capital"]=$capital;
				$data["intfincmt"]=$interes; 

				//$interes=($contrato_obj->valor_cuota*$contrato_obj->porc_interes/100);
			//	$datetime1 = date_create('2014-10-31');
			//	$datetime2 = date_create(date("Y-m-d",strtotime($contrato_obj->fecha_ingreso." ".$i." month")));
			//	$interval = date_diff($datetime1, $datetime2);

				$date1 = date(strtotime('2014-10-31'));
				$date2 = date(strtotime($contrato_obj->fecha_primer_pago." ".$i." month")); 
				$difference = $date1 - $date2;
				$months = floor($difference / 86400 / 30.4166 );
			//	echo '2014-10-31'."\n";
			//	echo date("Y-m-d",strtotime($contrato_obj->fecha_ingreso." ".$i." month"))."\n"; 
				$data['time_diff']=$months;
				//print_r($data); 
				array_push($rt,$data); 
			}
		}
		return $rt;
	}
	
	private function convertToUnixTime($date,$format){
		$date=split("-",$date);
		$rt="";
		if ($format=="ddmmyy"){ 
			$rt= mktime(1, 0, 0,$date[1],$date[0]+$day,$date[2]);	
		}
		if ($format=="yymmdd"){
			$rt= mktime(1, 0, 0,$date[1],$date[2]+$day,$date[0]);	
		}		
		return $rt;
	} 
	
	private function convertToUnixTimeAddDay($date,$day,$format){
		$date=split("-",$date);
		$rt="";
		if ($format=="ddmmyy"){ 
			$rt= mktime(1, 0, 0,$date[1],$date[0]+$day,$date[2]);	
		}
		if ($format=="yymmdd"){
			$rt= mktime(1, 0, 0,$date[1],$date[2]+$day,$date[0]);	
		}		
		return $rt;
	}
	
	public function addMetaCar($meta){
		mysql_query("DELETE FROM `log_metas_cache`");
	 
		$obj=new ObjectSQL();
		foreach($meta['por_contratos'] as $key =>$val){
			$obj->push($val);
			$obj->setTable("log_metas_cache");
			//echo $obj->toSQL("insert");
			$id=mysql_query($obj->toSQL("insert"));
			 
		}
		//$_SESSION['META_C_COBRO']=$meta;
	} 
	
	public function getMetaCar($filter){
		/*if (!isset($_SESSION['META_C_COBRO'])){
			$_SESSION['META_C_COBRO']=array('por_contratos'=>array());
 		}*/
		$SQL="SELECT * FROM `log_metas_cache` where cartera_asignada=0 ";

		if (validateField($filter,'f_oficial')){
			$oficial=System::getInstance()->Decrypt($filter['f_oficial']);
			$SQL.=" AND nit_oficial='".$oficial."'";
		}		
		
		if (validateField($filter,'cuota_numero') && validateField($filter,'cuota_condition')){
			$condicion=$this->formatCondition($filter['cuota_condition']);  
			$SQL.=" AND cuotas_acobrar ".$condicion." '".$filter['cuota_numero']."'";
			
		}

		if (validateField($filter,'monto_cuota')  && validateField($filter,'monto_c_condicion')){
			$condicion=$this->formatCondition($filter['monto_c_condicion']); 
			$SQL.=" AND monto_neto ".$condicion." '".$filter['monto_cuota']."'"; 
		} 
		if (validateField($filter,'f_oficial')){ 
			if ($_row['nit_oficial']!=$oficial){    
				$valid=false;
			}
		} 
		$rs=mysql_query($SQL); 
		$row=array(); 
		while($cartera=mysql_fetch_assoc($rs)){  
			array_push($row,$cartera);
		}
		return $row;
	}
	
	public function getMetaAsignada($filter){
		/*if (!isset($_SESSION['META_C_COBRO'])){
			$_SESSION['META_C_COBRO']=array('por_contratos'=>array());
 		}*/
		$SQL="SELECT * FROM `log_metas_cache` where cartera_asignada=1 ";

		if (validateField($filter,'f_oficial')){
			$oficial=System::getInstance()->Decrypt($filter['f_oficial']);
			$SQL.=" AND nit_oficial='".$oficial."'";
		}		
		
		if (validateField($filter,'cuota_numero') && validateField($filter,'cuota_condition')){
			$condicion=$this->formatCondition($filter['cuota_condition']);  
			$SQL.=" AND cuotas_acobrar ".$condicion." '".$filter['cuota_numero']."'";
			
		}

		if (validateField($filter,'monto_cuota')  && validateField($filter,'monto_c_condicion')){
			$condicion=$this->formatCondition($filter['monto_c_condicion']); 
			$SQL.=" AND monto_neto ".$condicion." '".$filter['monto_cuota']."'"; 
		} 
		if (validateField($filter,'f_oficial')){ 
			if ($_row['nit_oficial']!=$oficial){    
				$valid=false;
			}
		} 
		$rs=mysql_query($SQL); 
		$row=array(); 
		while($cartera=mysql_fetch_assoc($rs)){  
			array_push($row,$cartera);
		}
		return $row;
	}
	
	/*PROCESO QUE GENERA UNA META*/
	public function generarMeta(){
		$SQL="SELECT * FROM `log_metas_cache` where cartera_asignada=1 "; 
		$rs=mysql_query($SQL);  
		while($cartera=mysql_fetch_assoc($rs)){  
			 $obj=new ObjectSQL();
			 $obj->EM_ID=$cartera['id_empresa'];
			 $obj->no_contrato=$cartera['no_contrato'];
			 $obj->serie_contrato=$cartera['serie_contrato'];
			 $obj->nit_oficial=$cartera['nit_oficial_asig'];
			 $obj->nit_motorizado=$cartera['nit_motorizado'];	 
			 $obj->zona_id=$cartera['zona_id'];	 
			 $obj->ano=date("Y");
			 $obj->mes=date("m");
			 $obj->iddistintivo="";			 			 			 			 			 
			 $obj->monto_cobro=$cartera['monto_acobrar'];
			 $obj->cuotas_cobro=$cartera['cuotas_acobrar'];
			 $obj->dia_pago=$cartera['fecha_ingreso'];
			 $obj->saldo_0_30=$cartera['saldo_0_30'];
			 $obj->saldo_31_60=$cartera['saldo_31_60'];
			 $obj->saldo_61_90=$cartera['saldo_61_90'];
			 $obj->saldo_91_120=$cartera['saldo_91_120'];
			 $obj->saldo_mas_120=$cartera['saldo_mas_120'];
			 $obj->saldo_mora=$cartera['mora_acobrar'];
			 $obj->saldo_mante=$cartera['mante_acobrar']; 
			 $obj->setTable("cobros_contratos");
			 $SQL=$obj->toSQL("insert"); 
			 mysql_query($SQL);  			 
		}
		mysql_query("DELETE FROM `log_metas_cache`");
	}
	
	private function formatCondition($value){
		$format=""; 
		switch($value){
			case "MQ": //MAYOR QUE
				$format=">";
			break;	
			case "MIGQ": //MAYOR IGUAL
				$format=">=";
			break;	
			case "MNQ": //MENOR QUE
				$format="<";
			break;	
			case "MNIGQ": //MENOR IGUAL
				$format="<=";
			break;	
			case "IQ": //IGUAL
				$format="=";
			break;			 
		} 
		return $format;
	} 
	
	public function filterMetaCar($filter){
		$data=$this->getMetaCar($filter); 		
		return $data;
	}
	
	public function getCAsignadaByOficial(){
		$SQL="SELECT 
				SUM(`cuotas_acobrar`) AS cuotas_acobrar,
				SUM(`monto_neto`) AS monto_neto,
				SUM(`capital`) AS capital,
				SUM(`intfincmt`) AS intfincmt,
				SUM(`intcuota`) AS intcuota,
				SUM(`mora_acobrar`) AS mora_acobrar,
				SUM(`monto_acobrar`) AS monto_acobrar,
				SUM(`mante_acobrar`) AS mante_acobrar, 
				SUM(`monto_pendiente`) AS monto_pendiente,
				SUM(`monto_futuro`) AS monto_futuro,
				`nombre_cliente`, 
				SUM(`valor_cuota`) AS valor_cuota,
				`nombre_oficial_asing` ,
				`nit_oficial_asig` 
			FROM `log_metas_cache` WHERE cartera_asignada=1 
			GROUP BY `nit_oficial_asig`,`nombre_oficial_asing` ";
		$rs=mysql_query($SQL); 
		$row=array(); 
		while($cartera=mysql_fetch_assoc($rs)){  
			$cartera['total_clientes']=$this->getTotalClienteFromCarteraByOficial($cartera['nit_oficial_asig']);
			$cartera['total_contratos']=$this->getTotalContratoFromCarteraByOficial($cartera['nit_oficial_asig']);
			array_push($row,$cartera);
		}
		 
		
		return $row;
	}
	
	public function getTotalClienteFromCarteraByOficial($nit_oficial){
		$SQL="SELECT COUNT(*) AS total FROM  (SELECT 
	COUNT(*) AS total 
FROM `log_metas_cache` WHERE cartera_asignada=1 and nit_oficial_asig='".$nit_oficial."'
GROUP BY `nombre_cliente`) AS TOTAL_CLIENTE";
		$rs=mysql_query($SQL); 
		$total=0;
		while($cartera=mysql_fetch_assoc($rs)){  
			$total=$cartera['total'];
		}
		return $total;
	}
	public function getTotalContratoFromCarteraByOficial($nit_oficial){
		$SQL="SELECT COUNT(*) AS total FROM  (SELECT 
	COUNT(*) AS total 
FROM `log_metas_cache` WHERE cartera_asignada=1 and nit_oficial_asig='".$nit_oficial."'
GROUP BY `serie_contrato`,`no_contrato`)  AS contratos";
 
		$rs=mysql_query($SQL); 
		$total=0;
		while($cartera=mysql_fetch_assoc($rs)){  
			$total=$cartera['total'];
		}
		return $total;
	}	
	
	public function getCAsignadaByMotorizado(){
		$SQL="SELECT 
				SUM(`cuotas_acobrar`) AS cuotas_acobrar,
				SUM(`monto_neto`) AS monto_neto,
				SUM(`capital`) AS capital,
				SUM(`intfincmt`) AS intfincmt,
				SUM(`intcuota`) AS intcuota,
				SUM(`mora_acobrar`) AS mora_acobrar,
				SUM(`monto_acobrar`) AS monto_acobrar,
				SUM(`mante_acobrar`) AS mante_acobrar, 
				SUM(`monto_pendiente`) AS monto_pendiente,
				SUM(`monto_futuro`) AS monto_futuro,
				`nombre_cliente`, 
				SUM(`valor_cuota`) AS valor_cuota,
				`nombre_motorizado`,
				`nit_motorizado`
			FROM `log_metas_cache` WHERE cartera_asignada=1 
			GROUP BY `nombre_motorizado`,`nit_motorizado` ";
		$rs=mysql_query($SQL); 
		$row=array(); 
		while($cartera=mysql_fetch_assoc($rs)){  
			$cartera['total_clientes']=$this->getTotalClienteFromCarteraByMotorizado($cartera['nit_motorizado']);
			$cartera['total_contratos']=$this->getTotalContratoFromCarteraByMotorizado($cartera['nit_motorizado']);
			array_push($row,$cartera);
		}
		 
		
		return $row;
	}	
	public function getTotalClienteFromCarteraByMotorizado($nit){
		$SQL="SELECT COUNT(*) AS total FROM  (SELECT 
	COUNT(*) AS total 
FROM `log_metas_cache` WHERE cartera_asignada=1 and nit_motorizado='".$nit."'
GROUP BY `nombre_cliente`) AS TOTAL_CLIENTE";
		$rs=mysql_query($SQL); 
		$total=0;
		while($cartera=mysql_fetch_assoc($rs)){  
			$total=$cartera['total'];
		}
		return $total;
	}
	public function getTotalContratoFromCarteraByMotorizado($nit){
		$SQL="SELECT COUNT(*) AS total FROM  (SELECT 
	COUNT(*) AS total 
FROM `log_metas_cache` WHERE cartera_asignada=1 and nit_motorizado='".$nit."'
GROUP BY `serie_contrato`,`no_contrato`)  AS contratos";
 
		$rs=mysql_query($SQL); 
		$total=0;
		while($cartera=mysql_fetch_assoc($rs)){  
			$total=$cartera['total'];
		}
		return $total;
	}	
	/*PONE A 0 LA CARTERA ASIGNAD*/
	public function createCAsignada(){
		$_SESSION['META_C_ASIGNADA']=array();	
	}
	public function asignarCartera($meta,$nit_oficial){
		//print_r($meta);
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		$person= new PersonalData($this->db_link);
		$_person=$person->getClientData($nit_oficial);
  
		foreach($meta as $mkey =>$mval){
			print_r($mval);
			$obj= new ObjectSQL();
			$obj->cartera_asignada=1;
			$obj->nit_oficial_asig=$nit_oficial;
			$obj->nombre_oficial_asing=$_person['nombre_completo'];	
			$obj->setTable('log_metas_cache');
			$SQL=$obj->toSQL("update"," where cuotas_acobrar='".$mval['cuotas_acobrar']."'
			AND  serie_contrato='".$mval['serie_contrato']."'
			AND  no_contrato='".$mval['no_contrato']."'
			AND  fecha_ingreso='".$mval['fecha_ingreso']."'
			AND  id_empresa='".$mval['id_empresa']."'
			AND  nit_oficial='".$mval['nit_oficial']."'");
			mysql_query($SQL); 
		}
		//	print_r($_SESSION['META_C_ASIGNADA']);	
	}
	

   /*REVISA LA META DE COBROS*/
	function checkMetaCobros2($fecha_inicio="",$fecha_fin=""){ 
		//$cuota=$this->getLastNumeroCuotaCobrada($contrato_obj->serie_contrato,$contrato_obj->no_contrato);
		
		$fecha_meta_ini=$this->convertToUnixTime($fecha_inicio,"ddmmyy");
		$fecha_meta_fin=$this->convertToUnixTime($fecha_fin,"ddmmyy");
		
		$SQL="";
		 
		$rs=mysql_query($SQL); 
		$_META=array(
			"pasado"=>array(),
			"presente"=>array(),
			"futuro"=>array(),
			"agrupado"=>array()
		);
		
		while($contrato_obj=mysql_fetch_object($rs)){  
			//$sp=explode("-",$contrato_obj->fecha_ingreso);
			$cuota_paga=$this->getLastNumeroCuotaCobrada($contrato_obj->serie_contrato,$contrato_obj->no_contrato);
			$cuotas_c=$this->getListCuotasContratos($contrato_obj);
			
		}
	}	
	
   /*REVISA LA META DE COBROS*/
	function checkMetaCobros($fecha_inicio="",$fecha_fin=""){ 
		//$cuota=$this->getLastNumeroCuotaCobrada($contrato_obj->serie_contrato,$contrato_obj->no_contrato);
		
		$fecha_meta_ini=$this->convertToUnixTime($fecha_inicio,"ddmmyy");
		$fecha_meta_fin=$this->convertToUnixTime($fecha_fin,"ddmmyy");
		
 		
	 
		$SQL="SELECT 
			(SELECT cobros_zona.zona_id FROM `cobros_zona` 
			WHERE CONTAINS(GEOMETRYFROMTEXT(cobros_zona.polygon),POINT(sys_sector.`longitud`, sys_sector.`latitud`)) LIMIT 1 ) AS zona_id,
			(SELECT cobros_zona.`oficial_nit` FROM `cobros_zona` 
WHERE CONTAINS(GEOMETRYFROMTEXT(cobros_zona.polygon),POINT(sys_sector.`longitud`, sys_sector.`latitud`)) LIMIT 1 ) AS nit_oficial,
		(SELECT CONCAT(oficial.`primer_nombre`,' ',oficial.`segundo_nombre`,' ',
		oficial.`primer_apellido`,' ',oficial.segundo_apellido) AS nombre_oficial 
FROM `cobros_zona` 
INNER JOIN `sys_personas` AS oficial ON (`oficial`.id_nit=cobros_zona.`oficial_nit`) 
WHERE CONTAINS(GEOMETRYFROMTEXT(cobros_zona.polygon),POINT(sys_sector.`longitud`, sys_sector.`latitud`)) LIMIT 1 ) AS nombre_oficial,
	(SELECT cobros_zona.`motorizado` FROM `cobros_zona` 
WHERE CONTAINS(GEOMETRYFROMTEXT(cobros_zona.polygon),POINT(sys_sector.`longitud`, sys_sector.`latitud`)) LIMIT 1 ) AS nit_motorizado,
(SELECT CONCAT(oficial.`primer_nombre`,' ',oficial.`segundo_nombre`,' ',
oficial.`primer_apellido`,' ',oficial.segundo_apellido) AS nombre_oficial 
FROM `cobros_zona` 
INNER JOIN `sys_personas` AS oficial ON (`oficial`.id_nit=cobros_zona.`motorizado`) 
WHERE CONTAINS(GEOMETRYFROMTEXT(cobros_zona.polygon),POINT(sys_sector.`longitud`, sys_sector.`latitud`)) LIMIT 1 ) AS nombre_motorizado,
			contratos.*,
				TIMESTAMPDIFF(MONTH,contratos.fecha_ingreso,LAST_DAY(CURDATE())) AS month_diff,
			(SELECT 
				CONCAT(sys_personas.primer_nombre,' ',
				sys_personas.segundo_nombre,' ',
				sys_personas.primer_apellido,' ',
				sys_personas.segundo_apellido) 
			FROM `sys_personas` WHERE sys_personas.id_nit=contratos.`id_nit_cliente`) AS nombre_cliente, 
			(SELECT empresa.`EM_NOMBRE` FROM `empresa` WHERE empresa.`EM_ID`=contratos.`EM_ID`) AS empresa
			FROM `sys_direcciones` 
			INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
			INNER JOIN `contratos` ON (`contratos`.`serie_contrato`=sys_direcciones.serie_contrato 
						AND contratos.`no_contrato`=sys_direcciones.`no_contrato`) ";
		 
		$rs=mysql_query($SQL); 
		
		$_META=array(
			"pasado"=>array(),
			"presente"=>array(),
			"futuro"=>array(),
			"agrupado"=>array()
		);
		
		while($contrato_obj=mysql_fetch_object($rs)){  
			//$sp=explode("-",$contrato_obj->fecha_ingreso);
			$cuota_paga=$this->getLastNumeroCuotaCobrada($contrato_obj->serie_contrato,$contrato_obj->no_contrato);
			$cuotas_c=$this->getListCuotasContratos($contrato_obj);
		
			foreach($cuotas_c as $ckey =>$cval){ 
				$cval['zona_id']=$contrato_obj->zona_id;
				/*VALIDO SI ES UNA CUOTA FUTURA*/
		 		$cval['serie_contrato']=$contrato_obj->serie_contrato;
				$cval['no_contrato']=$contrato_obj->no_contrato;	
				 
				if (!isset($_META['por_cliente'][$contrato_obj->id_nit_cliente])){
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]=array(  
																"cuotas_acobrar"=>"0",
																"monto_neto"=>"0",
																"capital"=>"0",
																"intfincmt"=>"0",
																"intcuota"=>"0",
																"mora_acobrar"=>"0",
																"monto_acobrar"=>"0", 
																"mante_acobrar"=>0,
																"estatus"=>"N/A",
																"serie_contrato"=>"",
																"no_contrato"=>"",
																"monto_pendiente"=>0,
																"monto_futuro"=>0
															);
															
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['serie_contrato']=$contrato_obj->serie_contrato;
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['no_contrato']=$contrato_obj->no_contrato;				
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['nombre_cliente']=$contrato_obj->nombre_cliente;							
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['fecha_ingreso']=$contrato_obj->dia_pago;					
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['valor_cuota']=$contrato_obj->valor_cuota;										
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['EM_NOMBRE']=$contrato_obj->empresa; 
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['zona_id']=$contrato_obj->zona_id;
					 										
				}	 
				 
				$_META['por_cliente'][$contrato_obj->id_nit_cliente]['cuotas_acobrar']++;

				$_META['por_cliente'][$contrato_obj->id_nit_cliente]['capital']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['capital']+$cval['capital'];
				$_META['por_cliente'][$contrato_obj->id_nit_cliente]['intfincmt']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['intfincmt']+$cval['intfincmt'];
				
				
				$_META['por_cliente'][$contrato_obj->id_nit_cliente]['intcuota']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['intcuota']+$cval['intcuota'];
				
				
				$_META['por_cliente'][$contrato_obj->id_nit_cliente]['mora_acobrar']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['mora_acobrar']+$cval['intmora'];
				$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_acobrar']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_acobrar']+$cval['monto']; 	
				 
				 
				$cval['nombre_cliente']=$contrato_obj->nombre_cliente;							
				$cval['EM_NOMBRE']=$contrato_obj->empresa;
				$cval['zona_id']=$contrato_obj->zona_id;	
				$cval['nit_oficial']=$contrato_obj->nit_oficial;	
				 
			 

				if (!isset($_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']])){
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]=array(  
																"cuotas_acobrar"=>"0",
																"monto_neto"=>"0",
																"capital"=>"0",
																"intfincmt"=>"0",
																"intcuota"=>"0",
																"mora_acobrar"=>"0",
																"monto_acobrar"=>"0", 
																"mante_acobrar"=>0,
																"estatus"=>"N/A",
																"serie_contrato"=>"",
																"no_contrato"=>"",
																"monto_pendiente"=>0,
																"monto_futuro"=>0
															);
															
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['serie_contrato']=$contrato_obj->serie_contrato;
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['no_contrato']=$contrato_obj->no_contrato;				
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['nombre_cliente']=$contrato_obj->nombre_cliente;							
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['fecha_ingreso']=$contrato_obj->dia_pago;					
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['valor_cuota']=$contrato_obj->valor_cuota;										
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['EM_NOMBRE']=$contrato_obj->empresa;
					
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['zona_id']=$contrato_obj->zona_id;
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['nit_oficial']=$contrato_obj->nit_oficial;
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['nombre_oficial']=$contrato_obj->nombre_oficial; 		
					
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_0_30']=0;
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_31_60']=0;
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_61_90']=0;
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_91_120']=0;
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_mas_120']=0;		
															
				}
				
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['cuotas_acobrar']++;

				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['capital']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['capital']+$cval['capital'];
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['intfincmt']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['intfincmt']+$cval['intfincmt'];
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['intcuota']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['intcuota']+$cval['intcuota'];
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['mora_acobrar']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['mora_acobrar']+$cval['intmora'];
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_acobrar']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_acobrar']+$cval['monto']; 	
				
				
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['fecha_vencimiento']=$cval['fecha'];
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['id_empresa']=$contrato_obj->EM_ID;
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['forpago']=$contrato_obj->forpago;
				$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['tipo_cambio']=$contrato_obj->tipo_cambio;
				   
				 $_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['nombre_motorizado']=$contrato_obj->nombre_motorizado;
				 $_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['nit_motorizado']=$contrato_obj->nit_motorizado;				 
				 
			  
		  			  			  			  
 
				if ($this->convertToUnixTime($cval['fecha'],"yymmdd")>$fecha_meta_fin){  				
					//array_push($_META['futuro'],$cval); 
					if (!isset($_META['futuro'][$cval['serie_contrato'].$cval['no_contrato']])){
						$_META['futuro'][$cval['serie_contrato'].$cval['no_contrato']]=array();
					}
					//
					 
					array_push($_META['futuro'][$cval['serie_contrato'].$cval['no_contrato']],$cval); 
					
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_futuro']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_futuro']+$cval['monto']; 
					
					/*SALADO DE 0 A 30 DIAS*/
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_0_30']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_0_30']+$cval['monto'];
					  
					  
					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_futuro']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_futuro']+$cval['monto']; 	 
						 

					$_META['futuro'][$cval['serie_contrato'].$cval['no_contrato']]['nombre_cliente']=$contrato_obj->nombre_cliente;							
					$_META['futuro'][$cval['serie_contrato'].$cval['no_contrato']]['fecha_ingreso']=$contrato_obj->fecha_ingreso;					
					$_META['futuro'][$cval['serie_contrato'].$cval['no_contrato']]['EM_NOMBRE']=$contrato_obj->empresa;
					$_META['futuro'][$cval['serie_contrato'].$cval['no_contrato']]['zona_id']=$contrato_obj->zona_id;	
					
 					 				
					break;
				}else 
				if (($this->convertToUnixTime($cval['fecha'],"yymmdd")>=$fecha_meta_ini) 
						&& ($this->convertToUnixTime($cval['fecha'],"yymmdd")<=$fecha_meta_fin)){
					/*SI LA FECHA DE LOS CONTRATO ESTA ENTRE LA FECHA DE META ENTONCES ES UNA CUOTA PRESENTE*/ 				
					//array_push($_META['presente'],$cval); 
					if (!isset($_META['presente'][$cval['serie_contrato'].$cval['no_contrato']])){
						$_META['presente'][$cval['serie_contrato'].$cval['no_contrato']]=array(); 
					} 
					array_push($_META['presente'][$cval['serie_contrato'].$cval['no_contrato']],$cval);   

					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_neto']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_neto']+$cval['monto_neto'];
					
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_neto']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_neto']+$cval['monto_neto'];
					
					
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_0_30']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_0_30']+$cval['monto'];

					
									
					break; 
				}else
				if (($this->convertToUnixTime($cval['fecha'],"yymmdd")<$fecha_meta_ini) ){
					/*VALIDO SI ES UNA CUOTA ATRASADA*/  
					if (!isset($_META['pasado'][$cval['serie_contrato'].$cval['no_contrato']])){
						$_META['pasado'][$cval['serie_contrato'].$cval['no_contrato']]=array();
					} 
 					  
					array_push($_META['pasado'][$cval['serie_contrato'].$cval['no_contrato']],$cval); 
					  
					$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_pendiente']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['monto_pendiente']+$cval['monto']; 

					$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_pendiente']=$_META['por_cliente'][$contrato_obj->id_nit_cliente]['monto_pendiente']+$cval['monto']; 
					
					//print_r($cval);
					/*SI ES UN MES DE DIFERENCIA*/  
					if ($cval['time_diff']==1){
						$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_0_30']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_0_30']+$cval['monto'];

					}
					/*DOS MESES DE DIFERENCIA*/
					if ($cval['time_diff']==2){
						$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_31_60']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_31_60']+$cval['monto'];

					}
					if ($cval['time_diff']==3){
						$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_61_90']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_61_90']+$cval['monto'];

					}
					if ($cval['time_diff']==4){
						$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_91_120']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_91_120']+$cval['monto'];

					}
					/*SI ES MAYOR DE 4 MESES*/
					if ($cval['time_diff']>4){
						$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_mas_120']=$_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]['saldo_mas_120']+$cval['monto'];

					}
				//	print_r($_META['por_contratos'][$cval['serie_contrato'].$cval['no_contrato']]);  					

				//	break;
				}  
			}  			
		}
		   
		return $_META;
	}	
	
	/*Retorna el ultimo numero y fecha de la cuota cobrada*/
	function getLastNumeroCuotaCobrada($con_serie,$con_codigo){
		$SQL="SELECT 
			  sum(movimiento_contrato.NO_CUOTA) AS NO_CUOTA,
  			  MAX(movimiento_contrato.fecha) AS fecha 
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`)
			INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_contrato.TIPO_DOC)
			WHERE 
				movimiento_caja.TIPO_DOC IN ('RBC','NC','ND','RCA','NCA','NDA') AND
				movimiento_caja.`NO_CONTRATO`='".$con_codigo."' 
				AND movimiento_caja.`SERIE_CONTRATO`='".$con_serie."'
				AND movimiento_contrato.TIPO_MOV='CUOTA' and 
					movimiento_caja.ID_CAMBIOS_FINANCIEROS=0 AND 
				movimiento_caja.ANULADO='N' ";
	
		$rs=mysql_query($SQL);
		$data=array("nocuota"=>0,"fecha"=>"");
		while($row=mysql_fetch_assoc($rs)){
			$data['nocuota']=$row['NO_CUOTA'];
			$data['fecha']=$row['fecha'];
			
		}	 
		return $data;
	}
	/*Retorna todas las cuotas cobradas en el mes*/
	function getAllCuotasPagas($con_serie,$con_codigo){
		$SQL="SELECT (movimiento_contrato.MONTO_DOC*movimiento_contrato.TIPO_CAMBIO) AS MONTO,
					movimiento_contrato.FECHA,
					movimiento_contrato.NO_DOCTO,
					movimiento_contrato.CAJA_SERIE
				FROM `movimiento_contrato` 
				INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
				INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
						AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
				WHERE 
					movimiento_caja.`NO_CONTRATO`='".$con_codigo."' AND movimiento_caja.`SERIE_CONTRATO`='".$con_serie."'
				AND movimiento_contrato.TIPO_MOV='CUOTA'  and 
				movimiento_caja.ID_CAMBIOS_FINANCIEROS=0 AND movimiento_caja.ANULADO='N'
				ORDER BY movimiento_contrato.FECHA DESC ";
				
		$SQL="SELECT 
				SUM(MONTO) AS MONTO,
				FECHA,
				NO_DOCTO,
				CAJA_SERIE
				
			 FROM (SELECT (movimiento_contrato.MONTO_DOC*movimiento_contrato.TIPO_CAMBIO) AS MONTO,
				movimiento_contrato.FECHA,
				movimiento_contrato.NO_DOCTO,
				movimiento_contrato.CAJA_SERIE,
				movimiento_contrato.CUOTA
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			WHERE 
				movimiento_caja.`NO_CONTRATO`='".$con_codigo."' 
				AND movimiento_caja.`SERIE_CONTRATO`='".$con_serie."'
			AND movimiento_contrato.TIPO_MOV='CUOTA'  AND 
			movimiento_caja.ID_CAMBIOS_FINANCIEROS=0 AND movimiento_caja.ANULADO='N'
			) AS TB
			GROUP BY 
				TB.NO_DOCTO,
				TB.CAJA_SERIE
			ORDER BY TB.FECHA DESC";		
			
		$rs=mysql_query($SQL);
		$data=array();
		$i=1;
		while($row=mysql_fetch_assoc($rs)){
			$data[$i]=$row;
			$i++;
		}	 
		return $data;
	}	
	
	function getZona(){
		$SQL="SELECT *,
			(SELECT 
				CONCAT(sys_personas.primer_nombre,' ',
				sys_personas.segundo_nombre,' ',
				sys_personas.primer_apellido,' ',
				sys_personas.segundo_apellido) 
			FROM `sys_personas` WHERE sys_personas.id_nit=cobros_zona.`oficial_nit`) AS nombre_oficial,
			(SELECT 
				CONCAT(sys_personas.primer_nombre,' ',
				sys_personas.segundo_nombre,' ',
				sys_personas.primer_apellido,' ',
				sys_personas.segundo_apellido) 
			FROM `sys_personas` WHERE sys_personas.id_nit=cobros_zona.`motorizado`) AS motorizado
			 FROM `cobros_zona` "; 
		$rs=mysql_query($SQL); 
		$zona=array();
		while($row=mysql_fetch_assoc($rs)){
			$zona[$row['zona_id']]=$row;
		}	 
		return $zona;
	}	

	public function setToken($token){
		$this->token=$token;	
		if (!isset($_SESSION['CARRITO_COBRO'][$this->token])){
			$_SESSION['CARRITO_COBRO'][$this->token]=array();
		}
	}
	/*AGREGA LOS ITEMS PARA SER COBRADOS*/
	public function doItem($token,$cuota,$cmd){
		$_cuota=json_decode(System::getInstance()->Decrypt($cuota)); 
		
		if (!isset($_SESSION['CARRITO_COBRO'][$token])){
			$_SESSION['CARRITO_COBRO'][$token]=array(); 
		}    
		
		/*REMUEVE UN ITEM*/
		if ($cmd=="remove"){
 			/*VERIFICO QUE EN EL ARRAY NO HALLA MAS ITEMS */
			$item=$this->getItem($token);
			foreach($item as $key=>$val){
				if ($val->no_cuota>$_cuota->no_cuota){
					unset($_SESSION['CARRITO_COBRO'][$token][$val->no_cuota]); 
				}
			}
			unset($_SESSION['CARRITO_COBRO'][$token][$_cuota->no_cuota]);
 			return true;
		}		
		
		/*AGREGA UN ITEM*/
		if ($cmd=="add"){
			if (!array_key_exists($_cuota->no_cuota,$_SESSION['CARRITO_COBRO'][$token])){ 
	 			$_SESSION['CARRITO_COBRO'][$token][$_cuota->no_cuota]=$_cuota; 
				return true;
			}else{
				return true;	
			}
		}

		return false;
	}
	/*OBTIENE EL MONTO DE LAS CUOTAS SELECCIONADAS */
	public function getMontoSeleccionadoCuotas($token){ 
		$info=$this->getItem($token);    
		$monto=0;
		if (count($info)>0){ 
			foreach($info as $key=>$val){ 
				$monto=$monto+$val->monto_neto;	
			} 	 
		}
		return $monto;
	}	
	
	public function getItem($token){
		return $_SESSION['CARRITO_COBRO'][$token];
	}
	
	public function session_restart(){
		$_SESSION['CARRITO_COBRO']=array();   
	}	
	
	/*OPTIENE EL COBRADOR Y EL MOTORIZADO DE ASIGNADO A UN CONTRATO POR SU AREA DE COBRO*/
	function getCobradorMotorizadoAreaC($con_serie,$con_codigo){
		$SQL="SELECT  
				(SELECT CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido)
				FROM sys_personas  AS OFICIAL WHERE `OFICIAL`.`id_nit`=cobros_contratos.nit_oficial)  AS nombre_oficial, 
				(SELECT CONCAT(CLIENTE.`primer_nombre`,' ',CLIENTE.`segundo_nombre`,' ',CLIENTE.`primer_apellido`,' ',CLIENTE.segundo_apellido)
				FROM sys_personas  AS CLIENTE WHERE `CLIENTE`.`id_nit`=cobros_contratos.`nit_motorizado`)  AS nombre_motorizado,
				cobros_contratos.nit_oficial AS nitoficial,
				cobros_contratos.nit_motorizado AS nitmotorizado
			FROM `cobros_contratos` 
			WHERE  cobros_contratos.`serie_contrato`='".$con_serie."'
			 AND cobros_contratos.`no_contrato`='".$con_codigo."'  ";
		 		 
 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row;
		}	 
		return $data;
	}	
	
	function updateEstatusGestion($con_serie,$no_contrato,$date_time,$estatus){
		$gest=new ObjectSQL();
		$gest->setTable('gestiones');  
		$gest->id_status=$estatus; 
		$SQL=$gest->toSQL('update'," where fecha='".$date_time."' AND `no_contrato`='". mysql_real_escape_string($no_contrato) ."' and `serie_contrato`='". mysql_real_escape_string($con_serie) ."'"); 
		mysql_query($SQL);	 
		
		$SQL="SELECT * FROM `gestiones` WHERE fecha='".$date_time."' AND `no_contrato`='". mysql_real_escape_string($no_contrato) ."' and `serie_contrato`='". mysql_real_escape_string($con_serie) ."'";
		$rs=mysql_query($SQL);  
		while($row=mysql_fetch_assoc($rs)){
			$act=new ObjectSQL();
			$act->setTable('actividades_gestion');  
			$act->id_status=$estatus; 
			$SQL=$act->toSQL('update'," where idgestion='".$row['idgestion']."'"); 
			mysql_query($SQL);	
		}
		
	}
	/*OPTIENE UN AVISO DE COBRO GENERADO A UN CONTRATO */
	function getAvisoCobroData($con_serie,$no_contrato){
		$SQL="SELECT 
			`aviso_cobro`,
			`serie`,
			labor_cobro.`fecha_cobro`,
			labor_cobro.*,
			CONCAT(oficial.`primer_nombre`,' ',oficial.`segundo_nombre`,' ',oficial.`primer_apellido`,' ',oficial.segundo_apellido) AS nombre_oficial,
			(SELECT CONCAT(MOTO.`primer_nombre`,' ',
			MOTO.`segundo_nombre`,' ',
			MOTO.`primer_apellido`,' ',
			MOTO.segundo_apellido) AS nombre
	FROM sys_personas AS MOTO 
		WHERE MOTO.id_nit=gestiones.responsable) AS motorizado
		FROM `gestiones`
		INNER JOIN `labor_cobro` ON (labor_cobro.`no_contrato`=gestiones.no_contrato AND labor_cobro.`serie_contrato`=gestiones.serie_contrato)
		INNER JOIN `sys_personas` as oficial ON (`oficial`.id_nit=labor_cobro.oficial_cobro)
		WHERE gestiones.`no_contrato`='". mysql_real_escape_string($no_contrato) ."' AND gestiones.`serie_contrato`='". mysql_real_escape_string($con_serie) ."' AND `gestiones`.id_status IN ('19') AND labor_cobro.idaccion='MOTO'
		GROUP BY labor_cobro.aviso_cobro
		ORDER BY labor_cobro.`fecha_cobro`  ";
	 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}	
	/*LAS LABORES DE COBROS DE UN CONTRATO */
	function getListLaborCobro($id_nit_cliente){
		$SQL="SELECT 
				labor_cobro.*,
				date_format(fecha,'%d-%m-%Y') as fecha_,
				date_format(proximo_contacto,'%d-%m-%Y') as proximo_contacto_,								
				acciones_cobros.`accion`,
				CONCAT(motorizado.`primer_nombre`,' ',motorizado.`segundo_nombre`,' ',motorizado.`primer_apellido`,' ',motorizado.segundo_apellido) AS nombre_motorizado,
						motorizado.id_nit as nitmotorizado 
				FROM labor_cobro
				INNER JOIN `acciones_cobros` ON (`acciones_cobros`.`idaccion`=labor_cobro.idaccion)
				INNER JOIN `sys_personas` AS motorizado ON (`motorizado`.`id_nit`=labor_cobro.`oficial_cobro`)
				INNER JOIN contratos AS ct ON (ct.serie_contrato=labor_cobro.serie_contrato 
				AND ct.no_contrato=labor_cobro.no_contrato)
		 WHERE ct.id_nit_cliente='".$id_nit_cliente."'  and acciones_cobros.mostrar_historial_lb=1
		  ORDER BY  labor_cobro.fecha desc ";
		 /*
		 
				and labor_cobro.idaccion in ('LA','DOC')
		 */
		 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}
	/*OPTIENE LAS GESTIONES DE UN CONTRATO */
	function getListGestionCobro($con_serie,$no_contrato){
		$SQL="SELECT 
		labor_cobro.*,
		sys_status.descripcion AS estatus,
		acciones_cobros.`accion`,
		CONCAT(motorizado.`primer_nombre`,' ',motorizado.`segundo_nombre`,' ',motorizado.`primer_apellido`,' ',motorizado.segundo_apellido) AS responsable,
				motorizado.id_nit as nitmotorizado 
		FROM labor_cobro
		INNER JOIN `acciones_cobros` ON (`acciones_cobros`.`idaccion`=labor_cobro.idaccion)
		INNER JOIN `sys_personas` AS motorizado ON (`motorizado`.`id_nit`=labor_cobro.`oficial_cobro`)
		INNER JOIN `sys_status` ON (`sys_status`.id_status=labor_cobro.estatus)
 WHERE no_contrato='". mysql_real_escape_string($no_contrato) ."' AND serie_contrato='". mysql_real_escape_string($con_serie) ."' and labor_cobro.idaccion='MOTO' ORDER BY  labor_cobro.fecha desc ";
		 
		 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}	
	/*OPTIENE UN AVISO DE COBRO GENERADO A UN CONTRATO */
	function getListBitacora($con_serie,$no_contrato){
		$SQL="SELECT 
			labor_cobro.*,
			acciones_cobros.`accion`,
			CONCAT(motorizado.`primer_nombre`,' ',motorizado.`segundo_nombre`,' ',motorizado.`primer_apellido`,' ',motorizado.segundo_apellido) AS nombre_motorizado,
					motorizado.id_nit as nitmotorizado 
			FROM labor_cobro
			INNER JOIN `acciones_cobros` ON (`acciones_cobros`.`idaccion`=labor_cobro.idaccion)
			INNER JOIN `sys_personas` AS motorizado ON (`motorizado`.`id_nit`=labor_cobro.`oficial_cobro`)
			WHERE 
				labor_cobro.incluirBitacora=1 
				and no_contrato='". mysql_real_escape_string($no_contrato) ."' AND 
				serie_contrato='". mysql_real_escape_string($con_serie) ."' 
		ORDER BY  labor_cobro.fecha desc ";
		 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}	
	/*OPTIENE UNa GESTION DE UN CONTRATO */
	function getListGestion($con_serie,$no_contrato){
		
		$SQL="SELECT 
		gestiones.*,
		tipos_gestiones.`gestion`,
		CONCAT(reponsabe.`primer_nombre`,' ',reponsabe.`segundo_nombre`,' ',reponsabe.`primer_apellido`,' ',reponsabe.segundo_apellido) AS responsable,
		sys_status.`descripcion` AS estatus 
		FROM gestiones
		INNER JOIN `tipos_gestiones` ON (`tipos_gestiones`.`idtipogestion`=gestiones.`idtipogestion`)
		INNER JOIN `sys_personas` AS reponsabe ON (`reponsabe`.`id_nit`=gestiones.`responsable`)
		INNER JOIN `sys_status` ON (sys_status.`id_status`=gestiones.`id_status`)

		WHERE gestiones.no_contrato='". mysql_real_escape_string($no_contrato) ."'
		 AND gestiones.serie_contrato='". mysql_real_escape_string($con_serie) ."'  ORDER BY  gestiones.fecha DESC  ";
	 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}	

	/*OPTIENE EL LISTADO DE SOLICITUDES DE UN CONTRATO  */
	function getSolicitud($con_serie,$no_contrato,$tipo_gestion,$estatus=""){ 
		$SQL="SELECT 
				DATE_FORMAT(solicitud_gestion.fecha_creacion,'%d/%m/%Y') AS fecha_c,
				solicitud_gestion.*, 
				gestiones.*,
				tipos_gestiones.`gestion`,
				CONCAT(reponsabe.`primer_nombre`,' ',reponsabe.`segundo_nombre`,' ',reponsabe.`primer_apellido`,' ',reponsabe.segundo_apellido) AS responsable,
				sys_status.`descripcion` AS estatus 
			FROM `solicitud_gestion` 
			INNER JOIN `gestiones` ON (gestiones.`idgestion`=solicitud_gestion.`idgestion`) 
		INNER JOIN `tipos_gestiones` ON (`tipos_gestiones`.`idtipogestion`=gestiones.`idtipogestion`)
		INNER JOIN `sys_personas` AS reponsabe ON (`reponsabe`.`id_nit`=gestiones.`responsable`)
		INNER JOIN `sys_status` ON (sys_status.`id_status`=solicitud_gestion.`estatus`)				
		WHERE gestiones.no_contrato='". mysql_real_escape_string($no_contrato) ."'
		 AND gestiones.serie_contrato='". mysql_real_escape_string($con_serie) ."' and
		 	solicitud_gestion.idtipogestion='".$tipo_gestion."' ";		
		if (trim($estatus)!=""){
			$SQL.=" and solicitud_gestion.estatus='".$estatus."' "; 
		}
		$SQL.="ORDER BY  gestiones.fecha DESC  "; 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}
	/*OPTIENE EL LISTADO DE SOLICITUDES ABONO A CAPITAL DE UN CONTRATO  */
	function getSolicitudesAbonoCapital($con_serie,$no_contrato,$estatus=""){ 
		$SQL="SELECT 
				DATE_FORMAT(solicitud_gestion.fecha_creacion,'%d/%m/%Y') AS fecha_c,
				solicitud_gestion.*, 
				gestiones.*,
				tipos_gestiones.`gestion`,
				CONCAT(reponsabe.`primer_nombre`,' ',reponsabe.`segundo_nombre`,' ',reponsabe.`primer_apellido`,' ',reponsabe.segundo_apellido) AS responsable,
				sys_status.`descripcion` AS estatus 
			FROM `solicitud_gestion` 
			INNER JOIN `gestiones` ON (gestiones.`idgestion`=solicitud_gestion.`idgestion`) 
		INNER JOIN `tipos_gestiones` ON (`tipos_gestiones`.`idtipogestion`=gestiones.`idtipogestion`)
		INNER JOIN `sys_personas` AS reponsabe ON (`reponsabe`.`id_nit`=gestiones.`responsable`)
		INNER JOIN `sys_status` ON (sys_status.`id_status`=solicitud_gestion.`estatus`)				
		WHERE gestiones.no_contrato='". mysql_real_escape_string($no_contrato) ."'
		 AND gestiones.serie_contrato='". mysql_real_escape_string($con_serie) ."' ";		
		if (trim($estatus)!=""){
			$SQL.=" and solicitud_gestion.estatus='".$estatus."' "; 
		}
		$SQL.="ORDER BY  gestiones.fecha DESC  "; 
 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}
	/*OPTIENE EL LISTADO DE SOLICITUDES ABONO A CAPITAL DE UN CONTRATO  */
	function getSlcAbonoCapital($con_serie,$no_contrato,$estatus=""){ 
		$SQL=" SELECT 
				DATE_FORMAT(solicitud_gestion.fecha_creacion,'%d/%m/%Y') AS fecha_c,
				solicitud_gestion.*    
			FROM `solicitud_gestion` 
		WHERE solicitud_gestion.no_contrato='". mysql_real_escape_string($no_contrato) ."'
		 AND solicitud_gestion.serie_contrato='". mysql_real_escape_string($con_serie) ."' ";		
		if (trim($estatus)!=""){
			$SQL.=" and solicitud_gestion.estatus='".$estatus."' "; 
		}
		$SQL.="ORDER BY  solicitud_gestion.fecha_creacion DESC  ";  
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}	
	/*OPTIENE EL LISTADO DE SOLICITUDES ABONO A CAPITAL DE UN CONTRATO POR SOLICITUD  */
	function getSolicitudesAbonoCapitalBy($id_solicitud){ 
		$SQL="SELECT 
				DATE_FORMAT(solicitud_gestion.fecha_creacion,'%d/%m/%Y') AS fecha_c,
				solicitud_gestion.*, 
				gestiones.*,
				tipos_gestiones.`gestion`,
				CONCAT(reponsabe.`primer_nombre`,' ',reponsabe.`segundo_nombre`,' ',reponsabe.`primer_apellido`,' ',reponsabe.segundo_apellido) AS responsable,
				sys_status.`descripcion` AS estatus 
			FROM `solicitud_gestion` 
			INNER JOIN `gestiones` ON (gestiones.`idgestion`=solicitud_gestion.`idgestion`) 
		INNER JOIN `tipos_gestiones` ON (`tipos_gestiones`.`idtipogestion`=gestiones.`idtipogestion`)
		INNER JOIN `sys_personas` AS reponsabe ON (`reponsabe`.`id_nit`=gestiones.`responsable`)
		INNER JOIN `sys_status` ON (sys_status.`id_status`=solicitud_gestion.`estatus`)				
		WHERE solicitud_gestion.id_planilla_gestion='". mysql_real_escape_string($id_solicitud) ."'  
		ORDER BY  gestiones.fecha DESC  ";
	 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row;
		}	 
		return $data;
	}			
	public function createGestion($tipo_gestion,$empresa,$no_contrato,$serie_contrato,$descripcion){
		$SQL="SELECT COUNT(*)+1 AS total FROM `gestiones` WHERE idtipogestion='".$tipo_gestion."'";
		$rs=mysql_query($SQL); 
		$data=array();
		$row=mysql_fetch_assoc($rs);
		$numero_gestion=$row['total'];
		$_obj= new ObjectSQL(); 
		if ($this->fecha==""){
			$_obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		}else{
			$_obj->fecha=$this->fecha;
		}
		$_obj->idgestion=$tipo_gestion.$numero_gestion;
		$_obj->idtipogestion=$tipo_gestion;
		$_obj->responsable=UserAccess::getInstance()->getIDNIT();
 		$_obj->EM_ID=$empresa;
		$_obj->no_contrato=$no_contrato;
		$_obj->serie_contrato=$serie_contrato;
		$_obj->id_status="1";
		$_obj->descrip_general=$descripcion; 
		$_obj->setTable('gestiones');
		$SQL=$_obj->toSQL("insert");   
		mysql_query($SQL); 
		return $_obj;
	}
}

?>
