<?php

class Cierre{
	private $db_link;
	private $_data;
	
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
 
	public function setCierre($tipo_cierre,$id_caja,$periodo){
		$_SESSION['CIERRE_OBJ']=array(
									"tipo_cierre"=>$tipo_cierre,
									"id_caja"=>$id_caja,
									"periodo"=>$periodo
								);
	}	
	public function getCierre(){
		return $_SESSION['CIERRE_OBJ'];
	}

	public function getMontoCierreParcial($CAJA_ID){
		 $SQL="SELECT SUM(movimiento_caja.MONTO) AS MONTO,COUNT(*) AS TOTAL FROM `cierre_caja`   
				INNER JOIN movimiento_caja ON (movimiento_caja.`ID_CIERRE_CAJA`=cierre_caja.`ID_CIERRE`)
				WHERE cierre_caja.`PARCIAL_TOTAL`='P'  AND cierre_caja.`FECHA`='".$CAJA_ID->periodo."'";	  
 
		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
		$monto=0;
		if ($row['TOTAL']>0){
			$monto=$row['MONTO'];
		}
		return $monto;
	}
	
	public function getMontoCierreTotal($CAJA_ID){
		 $SQL="SELECT SUM(MONTO) as MONTO FROM view_cierre_caja WHERE view_cierre_caja.`ID_CAJA`='".mysql_real_escape_string($CAJA_ID)."'  AND  (SELECT COUNT(*) AS exist FROM cierre_caja WHERE cierre_caja.ID_CAJA=view_cierre_caja.`ID_CAJA` AND PARCIAL_TOTAL='T' AND CANTIDAD_DOC=view_cierre_caja.total AND TIPO_CAMBIO='1'  AND forpago=view_cierre_caja.forpago)>0
 ";	  

		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
		return $row['MONTO'];
	}

	public function generarCierre(){
		$rt=array(
			"valid"=>true,
			"mensaje"=>"Error, debe de seleccionar una caja", 
		);  
		$cierre=$this->getCierre(); 
			  
		if ((!isset($cierre['tipo_cierre'])) || (!isset($cierre['id_caja'])) || (!isset($cierre['periodo']))){
			$rt['mensaje']="Faltan datos por completar para poder procesar el cierre";
			return $rt;
		}
		
		 $SQL="SELECT 
		 			COUNT(*) AS T_CIERRE FROM 
		 	`cierre_caja` WHERE `FECHA`='".$cierre['periodo']."' AND `PARCIAL_TOTAL`='T' ";	
	 
		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
		if ($row['T_CIERRE']>0){
			$rt['mensaje']="No se puede volver a cerrar la caja!";
			return $rt;
		}   
		 $SQL="SELECT 
			movimiento_caja.*
			FROM `movimiento_caja`
			INNER JOIN `forma_pago_caja` ON (`forma_pago_caja`.SERIE=movimiento_caja.SERIE AND 
			 forma_pago_caja.NO_DOCTO=movimiento_caja.NO_DOCTO)	
			 INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			 movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
			INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV)
			INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO) 
			WHERE 	movimiento_caja.ANULADO='N'	 AND 
					movimiento_caja.ID_CAJA='".$cierre['id_caja']."' and
				 movimiento_caja.`FECHA`='".$cierre['periodo']."' and (ID_CIERRE_CAJA='' or ID_CIERRE_CAJA is null) ";	
	
		$rs=mysql_query($SQL);
		$data=array();
		$monto=0;
		while($row=@mysql_fetch_assoc($rs)){	
			array_push($data,$row);
			$monto=$monto+$row['MONTO'];
 		}    
	 	$obj = new ObjectSQL();
		$obj->ID_CIERRE="DATE_FORMAT(CONCAT('".$cierre['periodo']." ',CURTIME()),'%Y%m%d%H%i%s')";
		$obj->ID_CAJA=$cierre['id_caja'];
		$obj->FECHA=$cierre['periodo'];
		$obj->HORA="CURTIME()";
		$obj->PARCIAL_TOTAL=$cierre['tipo_cierre'];
		$obj->MONTO=$monto;
		$obj->ID_NIT=UserAccess::getInstance()->getIDNIT();
		$obj->ESTATUS=$cierre['tipo_cierre']=="P"?29:30; 
		$obj->setTable("cierre_caja");
		$SQL=$obj->toSQL("insert");
		 mysql_query($SQL);	
				
		foreach($data as $key =>$val)
		{
			$mcaja = new ObjectSQL();
			$mcaja->ID_CIERRE_CAJA=$obj->ID_CIERRE;
			$mcaja->setTable("movimiento_caja");
			$SQL=$mcaja->toSQL("update"," where ID_CAJA='".$val['ID_CAJA']."' and SERIE='".$val['SERIE']."' and NO_DOCTO='".$val['NO_DOCTO']."'"); 
			mysql_query($SQL); 		 
		} 
		
		$rt['mensaje']="Cierre realizado!";
		$rt['valid']=false; 
		return $rt;
	}	

	public function listarDesgloseCierreFormaPago($filtro=array()){  
		$SQL="SELECT 
				formas_pago.descripcion_pago AS forma_pago,
				SUM(forma_pago_caja.MONTO*forma_pago_caja.TIPO_CAMBIO) AS MONTO,
				COUNT(*) AS TOTALES 
				FROM `movimiento_caja` 
				INNER JOIN `forma_pago_caja` ON (`forma_pago_caja`.SERIE=movimiento_caja.SERIE AND 
				 forma_pago_caja.NO_DOCTO=movimiento_caja.NO_DOCTO)	
				INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO) 
			WHERE 	movimiento_caja.ANULADO='N' and  movimiento_caja.TIPO_DOC='RBC' 	";

		if (count($filtro)==0){
			$SQL.=" AND movimiento_caja.fecha=CURDATE() ";	
		}else{
			if (isset($filtro['action'])){
				if ($filtro['action']=="filter"){		
					if (isset($filtro['day_from'])){
						$SQL.=" AND DATE_FORMAT(movimiento_caja.FECHA,'%Y-%m-%d') between '".$filtro['day_from']."' and '".$filtro['day_to']."'";
 					}
					if ($filtro['fc']=="F"){
						$SQL.=" AND movimiento_caja.TIPO_DOC_FISCAL=1";
					}
					if ($filtro['fc']=="N"){
						$SQL.=" AND movimiento_caja.TIPO_DOC_FISCAL=0";
					}
					if ($filtro['forma_pago']!=""){
						$SQL.=" AND formas_pago.forpago='".$filtro['forma_pago']."'";
					}										
					if (count($filtro['cajas'])>0){
						$str="";
						foreach($filtro['cajas'] as $key=>$val){
							$str.="'".$val."',";
						}
						$str=substr($str,0,strlen($str)-1);
						if ($str!=""){
							$SQL.=" AND movimiento_caja.ID_CAJA IN (".$str.")";
						}
					}
					if (count($filtro['oficial_cobros'])>0){
						$str="";
						foreach($filtro['oficial_cobros'] as $key=>$val){
							$str.="'".$val."',";
						}
						$str=substr($str,0,strlen($str)-1);
						if ($str!=""){
							$SQL.=" AND concat(movimiento_caja.SERIE,movimiento_caja.NO_DOCTO) IN (
							SELECT CONCAT(serie,no_docto) FROM `movimiento_factura` WHERE id_nit_oficial IN (".$str.")
							)";
						}
					}					
					
				}				
			}
		}		
		$SQL.="  AND (movimiento_caja.ID_CIERRE_CAJA='' OR movimiento_caja.ID_CIERRE_CAJA IS NULL)
			GROUP BY forma_pago_caja.FORMA_PAGO "	;
	 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			if (!isset($data[$row['tipo_movimiento']])){
				$data[$row['tipo_movimiento']]=array();
			}
			array_push($data[$row['tipo_movimiento']],$row);
 		}  
		return $data;
	}
	
	public function getPreviewCierre($filtro){
 
		$SQL="SELECT 
				tipo_movimiento.TIPO_MOV,
				tipo_movimiento.`DESCRIPCION` AS tipo_movimiento, 
				SUM(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MONTO,
				COUNT(*) AS TOTALES 
			FROM `movimiento_caja`
			 INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			 movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
			INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV)
						WHERE  movimiento_caja.TIPO_DOC='RBC' AND 	movimiento_caja.ANULADO='N' 
				and movimiento_factura.TIPO_MOV !='NC' 	";
		if (count($filtro)==0){
			$SQL.=" AND movimiento_caja.fecha=CURDATE() ";	
		}else{
			if (isset($filtro['action'])){
				if ($filtro['action']=="filter"){		
					if (isset($filtro['day_from'])){
						$SQL.=" AND DATE_FORMAT(movimiento_caja.FECHA,'%Y-%m-%d') between '".$filtro['day_from']."' and '".$filtro['day_to']."' ";
 					}
					if ($filtro['fc']=="F"){
						$SQL.=" AND movimiento_caja.TIPO_DOC_FISCAL=1";
					}
					if ($filtro['fc']=="N"){
						$SQL.=" AND movimiento_caja.TIPO_DOC_FISCAL=0";
					}	
						
						/*			
					if ($filtro['forma_pago']!=""){
						$SQL.=" AND formas_pago.forpago='".$filtro['forma_pago']."'";
					}															
						*/
					if (count($filtro['cajas'])>0){
						$str="";
						foreach($filtro['cajas'] as $key=>$val){
							$str.="'".$val."',";
						}
						$str=substr($str,0,strlen($str)-1);
						if ($str!=""){
							$SQL.=" AND movimiento_caja.ID_CAJA IN (".$str.")";
						}
					}
					if (count($filtro['oficial_cobros'])>0){
						$str="";
						foreach($filtro['oficial_cobros'] as $key=>$val){
							$str.="'".$val."',";
						}
						$str=substr($str,0,strlen($str)-1);
						if ($str!=""){
							$SQL.=" AND concat(movimiento_caja.SERIE,movimiento_caja.NO_DOCTO) IN (
							SELECT CONCAT(serie,no_docto) FROM `movimiento_factura` WHERE id_nit_oficial IN (".$str.")
							)";
						}
					}					
					
				}				
			}
		}		
		$SQL.="  AND (movimiento_caja.ID_CIERRE_CAJA='' OR movimiento_caja.ID_CIERRE_CAJA IS NULL)
			GROUP BY tipo_movimiento.TIPO_MOV
			ORDER BY tipo_movimiento.TIPO_MOV "	;
		
		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			if (!isset($data[$row['tipo_movimiento']])){
				$data[$row['tipo_movimiento']]=array();
			}
			array_push($data[$row['tipo_movimiento']],$row);
 		}  
		return $data;
	}
	public function listarDesgloseCierre($filtro=array()){ 
		$SQL="SELECT 
				*,
				(forma_pago_caja.MONTO*forma_pago_caja.TIPO_CAMBIO) AS MONTO_PAGO,
			caja.DESCRIPCION_CAJA AS CAJA ,
			`tipo_documento`.`DOCUMENTO`,
			(SELECT GROUP_CONCAT(tipo_movimiento.DESCRIPCION) FROM `movimiento_factura` AS MF 
		INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=MF.TIPO_MOV)
		 WHERE MF.CAJA_SERIE=movimiento_caja.SERIE and MF.CAJA_NO_DOCTO=movimiento_caja.NO_DOCTO) as TMOVIMIENTO,
		 CONCAT(sp.`primer_nombre`,' ',sp.`segundo_nombre`, ' ',sp.`primer_apellido`,' ',sp.segundo_apellido) AS nombre_cliente 
			FROM `movimiento_caja` 
		INNER JOIN `forma_pago_caja` ON (`forma_pago_caja`.SERIE=movimiento_caja.SERIE AND 
		 forma_pago_caja.NO_DOCTO=movimiento_caja.NO_DOCTO)	
		INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO) 			
		INNER JOIN sys_personas AS sp ON (sp.id_nit=movimiento_caja.ID_NIT)				
		INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
		INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
		WHERE  movimiento_caja.TIPO_DOC='RBC' AND movimiento_caja.ANULADO='N' "; 
		
		/*
		$SQL="SELECT 
				*,
				SUM(movimiento_factura.MONTO*forma_pago_caja.TIPO_CAMBIO) AS MONTO_PAGO,
			caja.DESCRIPCION_CAJA AS CAJA ,
			`tipo_documento`.`DOCUMENTO`,
			tipo_movimiento.DESCRIPCION as TMOVIMIENTO,
		 CONCAT(sp.`primer_nombre`,' ',sp.`segundo_nombre`, ' ',sp.`primer_apellido`,' ',sp.segundo_apellido) AS nombre_cliente 
			FROM `movimiento_caja` 
			INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			 movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
			 INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=movimiento_factura.TIPO_MOV)
			INNER JOIN `forma_pago_caja` ON (`forma_pago_caja`.SERIE=movimiento_caja.SERIE AND 
			forma_pago_caja.NO_DOCTO=movimiento_caja.NO_DOCTO)	
			INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO) 			
			INNER JOIN sys_personas AS sp ON (sp.id_nit=movimiento_caja.ID_NIT)				
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
			INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC) "; 		*/

		if (count($filtro)==0){
			$SQL.=" AND movimiento_caja.fecha=CURDATE() ";	
		}else{
			if (isset($filtro['action'])){
				if ($filtro['action']=="filter"){		
					if (isset($filtro['day_from'])){
						$SQL.=" AND DATE_FORMAT(movimiento_caja.FECHA,'%Y-%m-%d') between '".$filtro['day_from']."' and '".$filtro['day_to']."'";
 					}
					if ($filtro['fc']=="F"){
						$SQL.=" AND movimiento_caja.TIPO_DOC_FISCAL=1";
					}
					if ($filtro['fc']=="N"){
						$SQL.=" AND movimiento_caja.TIPO_DOC_FISCAL=0";
					}					
					if ($filtro['forma_pago']!=""){
						$SQL.=" AND formas_pago.forpago='".$filtro['forma_pago']."'";
					}															
					if (count($filtro['cajas'])>0){
						$str="";
						foreach($filtro['cajas'] as $key=>$val){
							$str.="'".$val."',"; 
						}
						$str=substr($str,0,strlen($str)-1);
						if ($str!=""){
							$SQL.=" AND movimiento_caja.ID_CAJA IN (".$str.")";
						}
					}
					
				}				
			}
		}    
	/*	$SQL.=" GROUP BY 			movimiento_caja.no_docto,
									movimiento_caja.serie,
							caja.DESCRIPCION_CAJA,
							tipo_movimiento.DESCRIPCION,
							sp.id_nit,forma_pago_caja.FORMA_PAGO  ";*/
		$SQL.=" ORDER BY movimiento_caja.SERIE,
					movimiento_caja.NO_DOCTO ";					
		
		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			array_push($data,$row);
 		}  
		return $data;
	}		
	public function ListarCierres(){
		$SQL="SELECT 
			cierre_caja.*,
			caja.`DESCRIPCION_CAJA`,
			CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',
			sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS EJECUTOR,
			sys_status.`descripcion` AS estatus,
			DATE_FORMAT(cierre_caja.FECHA,'%d-%m-%Y') AS fecha_cierre
		FROM 
			`cierre_caja` 
		INNER JOIN `sys_personas` ON (cierre_caja.ID_NIT=sys_personas.id_nit)
		INNER JOIN `caja` ON (`caja`.ID_CAJA=cierre_caja.ID_CAJA)
		INNER JOIN `sys_status` ON (sys_status.`id_status`=cierre_caja.`ESTATUS`)";
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){ 
			$id=System::getInstance()->Encrypt(json_encode($row));
			$row['id']=$id;
			array_push($rt,$row);
		}
		return $rt;
	}
	/*VALIDA SI SE HA QUEDADO ALGUN CIERRE SIN REALIZAR*/
	public function validateCierres(){
		$SQL=" SELECT caja.`DESCRIPCION_CAJA`,
			cierre_caja.FECHA,
			(SELECT COUNT(*) AS total FROM cierre_caja AS cie 
			WHERE cie.`FECHA`=cierre_caja.FECHA AND `PARCIAL_TOTAL`='T' ) AS TOTAL,
			DATEDIFF(CURDATE(),cierre_caja.FECHA) AS diff
			 FROM `cierre_caja`
			INNER JOIN `caja` ON (`caja`.ID_CAJA=cierre_caja.ID_CAJA)
			 WHERE `PARCIAL_TOTAL`='P' limit 1";
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			$rt=$row;
		}
		return $rt;
	}	
	public function getDetalleCierre($C_CIERRE){ 
		 $SQL="SELECT 
			tipo_movimiento.TIPO_MOV,
			tipo_movimiento.`DESCRIPCION` AS tipo_movimiento, 
			formas_pago.descripcion_pago AS forma_pago,
			SUM(forma_pago_caja.MONTO) AS MONTO,
			count(*) as TOTALES
			FROM `movimiento_caja`
			INNER JOIN `cierre_caja` ON (`cierre_caja`.`ID_CIERRE`=movimiento_caja.`ID_CIERRE_CAJA`)
			INNER JOIN `forma_pago_caja` ON (`forma_pago_caja`.SERIE=movimiento_caja.SERIE AND 
			forma_pago_caja.NO_DOCTO=movimiento_caja.NO_DOCTO)	
			INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
			INNER JOIN `tipo_movimiento` ON (tipo_movimiento.TIPO_MOV=movimiento_factura.TIPO_MOV)
			INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO) 
			WHERE 	movimiento_caja.ID_CAJA='".$C_CIERRE->ID_CAJA."'  AND 
					movimiento_caja.`FECHA`='".$C_CIERRE->FECHA."' AND 
					cierre_caja.ID_CIERRE='".$C_CIERRE->ID_CIERRE."' 
			GROUP BY 
				forma_pago_caja.FORMA_PAGO,
				tipo_movimiento.TIPO_MOV
			ORDER BY tipo_movimiento.TIPO_MOV ";	 

		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			if (!isset($data[$row['tipo_movimiento']])){
				$data[$row['tipo_movimiento']]=array();
			}
			array_push($data[$row['tipo_movimiento']],$row);
 		}  
		return $data;
	}	
}

?>