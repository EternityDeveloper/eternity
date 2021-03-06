<?php

class Recibos{
	private $db_link;
	private $_data;
	
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	
	public function validarSiElReciboTieneLaborDeCobro($serie,$no_docto){
		$SQL="SELECT count(*) as total FROM `labor_cobro` 
			WHERE `aviso_cobro`='". mysql_real_escape_string($no_docto) ."'
			 AND `serie`='". mysql_real_escape_string($serie) ."'";  
		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
		return $row['total'];
	}
	public function getMontoNotaCD($serie,$no_docto){
		$SQL="SELECT SUM(MONTO) AS MONTO FROM `movimiento_caja`   
			WHERE `NOTA_CD_NO_DOCTO`='". mysql_real_escape_string($no_docto) ."'
			 AND `NOTA_CD_SERIE`='". mysql_real_escape_string($serie) ."'";  
		
		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
		return $row['MONTO'];
	}
	public function getMFReciboCaja($serie,$no_docto){
		$SQL="SELECT * FROM `movimiento_factura`  
			WHERE `CAJA_NO_DOCTO`='". mysql_real_escape_string($no_docto) ."'
			 AND `CAJA_SERIE`='". mysql_real_escape_string($serie) ."'";   
		$rs=mysql_query($SQL); 
		$data=array("valid"=>false);
		while($row=@mysql_fetch_assoc($rs)){
			$data=$row;
			$data['valid']=true;
		}
		return $data;
	}		
	public function getReciboFormaPago($serie,$no_docto){ 
		$SQL="SELECT forma_pago_caja.*,
					formas_pago.*,
					bancos.*
				FROM  `forma_pago_caja`
				INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO)
				LEFT JOIN `bancos` ON (`bancos`.ban_id=forma_pago_caja.ID_BANCO)
 			WHERE 
				(forma_pago_caja.`SERIE`='".$serie."' AND forma_pago_caja.`NO_DOCTO`='".$no_docto."') "; 
	
		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			$row['MONTO_RD']=$row['MONTO']*$row['TIPO_CAMBIO'];
			array_push($data,$row);
 		}  
		return $data;
	}		
 
	public function getListadoRecibo($filtro=array()){ 
		$SQL="SELECT 
				*,
			caja.DESCRIPCION_CAJA AS CAJA ,
			`tipo_documento`.`DOCUMENTO`,
			(SELECT GROUP_CONCAT(tipo_movimiento.DESCRIPCION) FROM `movimiento_factura` AS MF 
		INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=MF.TIPO_MOV)
		 WHERE MF.CAJA_SERIE=movimiento_caja.SERIE and MF.CAJA_NO_DOCTO=movimiento_caja.NO_DOCTO) as TMOVIMIENTO,
		 CONCAT(sp.`primer_nombre`,' ',sp.`segundo_nombre`, ' ',sp.`primer_apellido`,' ',sp.segundo_apellido) AS nombre_cliente 
			FROM `movimiento_caja` 
		INNER JOIN sys_personas AS sp ON (sp.id_nit=movimiento_caja.ID_NIT)				
		INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
		INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
		WHERE  movimiento_caja.TIPO_DOC IN ('RCA','RBC','".RECIBO_CAJA."','".NOTA_CREDITO."','".NOTA_DEBITO."') "; 

		if (count($filtro)==0){
			$SQL.=" AND movimiento_caja.fecha=CURDATE() ";	
		}else{
			if (isset($filtro['action'])){
				if ($filtro['action']=="getRecibo"){		
					if (isset($filtro['NO_DOCTO']) && isset($filtro['SERIE'])){
						$SQL.=" AND movimiento_caja.SERIE='".$filtro['SERIE']."' AND 
							 movimiento_caja.NO_DOCTO='".$filtro['NO_DOCTO']."'";
 					}
				}
				if ($filtro['action']=="filter_range_date"){		
					if (isset($filtro['day_from']) && isset($filtro['day_to'])){
						$SQL.=" AND movimiento_caja.FECHA between'".$filtro['day_from']."' AND '".$filtro['day_to']."'";
 					}
				}				
			}
		}
		$SQL.=" ORDER BY movimiento_caja.SERIE,
						movimiento_caja.NO_DOCTO ";		
	 			
		 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			array_push($data,$row);
 		}  
		return $data;
	}
	
	/*DETALLE DE UN RECIBO*/
	public function getDetalleRecibo($filtro=array()){ 
		$SQL="SELECT * FROM `movimiento_caja` 
			INNER JOIN movimiento_factura AS MF ON (MF.SERIE=movimiento_caja.SERIE AND MF.NO_DOCTO=movimiento_caja.NO_DOCTO)
			INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=MF.TIPO_MOV)
			INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
			WHERE  movimiento_caja.ANULADO='N' "; 

		if (count($filtro)==0){
			$SQL.=" AND movimiento_caja.fecha=CURDATE() ";	
		}else{
			if (isset($filtro['action'])){
				if ($filtro['action']=="getRecibo"){		
					if (isset($filtro['NO_DOCTO']) && isset($filtro['SERIE'])){
						$SQL.=" AND  movimiento_caja.SERIE='".$filtro['SERIE']."' AND 
							 movimiento_caja.NO_DOCTO='".$filtro['NO_DOCTO']."'";
 					}
				}			
			}
		}  
		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			array_push($data,$row);
 		}  
		return $data;
	}	
	public function getListadoNRecibo($filtro=array()){ 
		$SQL="SELECT 
				movimiento_caja.* 
			FROM `movimiento_caja` 
		WHERE  movimiento_caja.TIPO_DOC IN ('RBCV','RBC')  ";  
		if (count($filtro)==0){
			$SQL.=" AND movimiento_caja.fecha=CURDATE() ";	
		}else{
			if (isset($filtro['action'])){
				if ($filtro['action']=="getRecibo"){		
					if (isset($filtro['NO_DOCTO']) && isset($filtro['SERIE'])){
						$SQL.=" AND movimiento_caja.SERIE='".$filtro['SERIE']."' AND 
							 movimiento_caja.NO_DOCTO='".$filtro['NO_DOCTO']."'";
 					}
				}
				if ($filtro['action']=="filter_range_date"){		
					if (isset($filtro['day_from']) && isset($filtro['day_to'])){
						$SQL.=" AND movimiento_caja.FECHA between'".$filtro['day_from']."' AND '".$filtro['day_to']."'";
 					}
				}				
			}
		} 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=@mysql_fetch_assoc($rs)){	
			array_push($data,$row);
 		}  
		return $data;
	}	
	public function doChangeTasaFormaPagoRecibo($serie,$no_docto,$lastforma_pago,$new_forma_pago,$tasa){
	 
	 	if (trim($new_forma_pago)==""){
			return false;	
		}
		if ($tasa>0){ 
 			$fpdetalle=$this->getReciboFormaPago($serie,$no_docto);
			$filter=array("action"=>'getRecibo',"SERIE"=>$serie,"NO_DOCTO"=>$no_docto);
			$rc_detalle=$this->getListadoRecibo($filter);
			if (count($rc_detalle)<=0){
				return false;	
			}
			$recibo=$rc_detalle[0]; 
			 
				
			$obj= new ObjectSQL();
			$obj->TIPO_CAMBIO=$tasa;
			$obj->FORMA_PAGO=$new_forma_pago;
			$obj->setTable('forma_pago_caja');
			$SQL=$obj->toSQL("update"," where (RC_SERIE='".$serie."' and RC_NO_DOCTO='".$no_docto."')"); 
			mysql_query($SQL); 
			
			$obj= new ObjectSQL();
			$obj->TIPO_CAMBIO=$tasa;
			$obj->FORMA_PAGO=$new_forma_pago;
			$obj->setTable('forma_pago_caja');
			$SQL=$obj->toSQL("update"," where (SERIE='".$serie."' and NO_DOCTO='".$no_docto."')"); 
			mysql_query($SQL); 		 
			
			SysLog::getInstance()->Log($recibo['ID_NIT'], 
										 $recibo['SERIE_CONTRATO'],
										 $recibo['NO_CONTRATO'],
										 '',
										 '',
										 "CAMBIO DE TASA AJUSTE FORMA DE PAGO ".$lastforma_pago." CAMBIO A ".$new_forma_pago." RECIBO NO.: ".$recibo['SERIE']." ".$recibo['NO_DOCTO'],
										 json_encode($fpdetalle),
										 'INFO',
										 $recibo['SERIE'],
										 $recibo['NO_DOCTO']);				
		}
		return false;
	}	
	public function getReciboByBarCode($barcode){
		$SQL="SELECT 
				movimiento_caja.*,
				movimiento_caja.MONTO AS MONTO_TOTAL,
				(movimiento_factura.MONTO * movimiento_factura.TIPO_CAMBIO) AS MONTO_LOCAL,
				movimiento_factura.*,
				count(movimiento_factura.NO_CUOTA) AS TOTAL_CUOTAS,	
				(SELECT 
					CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
					OFI.`primer_apellido`,' ',
					OFI.segundo_apellido) AS nombre 
					FROM sys_personas AS OFI WHERE 
						OFI.id_nit=movimiento_factura.ID_NIT_OFICIAL) AS oficial,
				(SELECT 
					CONCAT(MOTO.`primer_nombre`,' ', MOTO.`segundo_nombre`,' ', 
					MOTO.`primer_apellido`,' ',
					MOTO.segundo_apellido) AS nombre FROM sys_personas AS MOTO WHERE 
					MOTO.id_nit=movimiento_factura.ID_NIT_MOTORIZADO) AS motorizado,
					
					GROUP_CONCAT(tipo_movimiento.DESCRIPCION) AS tmovimiento
			FROM 
				`movimiento_caja` 
			INNER JOIN `movimiento_factura` ON (movimiento_factura.`NO_DOCTO`=movimiento_caja.`NO_DOCTO` AND
			movimiento_caja.`SERIE`=movimiento_factura.SERIE) 
			INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=movimiento_factura.TIPO_MOV)
			WHERE movimiento_caja.ANULADO='N' 
				and movimiento_caja.TIPO_DOC!='RBC' AND
				movimiento_caja.NO_CODIGO_BARRA='".$barcode."'  
		 GROUP BY  movimiento_caja.SERIE,
				 movimiento_caja.NO_DOCTO,
				 movimiento_caja.`NO_CONTRATO`,
				 movimiento_caja.`SERIE_CONTRATO`";
		 
		$rt=array("data"=>array(),"valid"=>false,"mensaje"=>"No existe el recibo"); 
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){  
			$rt['valid']=true;
			$rt['data']=$row;
		}	 
		return $rt;
	}	
	
	public function doChangeTasaRecbio($serie,$no_docto,$tasa){
	 
		if ($tasa>0){ 
			$filter=array("action"=>'getRecibo',"SERIE"=>$serie,"NO_DOCTO"=>$no_docto);
			$rc_detalle=$this->getListadoRecibo($filter);
			if (count($rc_detalle)<=0){
				return false;	
			}
			$recibo=$rc_detalle[0]; 
				
			$obj= new ObjectSQL();
			$obj->TIPO_CAMBIO=$tasa;
			$obj->setTable('movimiento_caja');
			$SQL=$obj->toSQL("update"," where SERIE='".$serie."' and NO_DOCTO='".$no_docto."'"); 
			mysql_query($SQL);
			
			$obj= new ObjectSQL();
			$obj->TIPO_CAMBIO=$tasa;
			$obj->setTable('movimiento_factura');
			$SQL=$obj->toSQL("update"," where CAJA_SERIE='".$serie."' and CAJA_NO_DOCTO='".$no_docto."'");
			mysql_query($SQL); 
			
			SysLog::getInstance()->Log($recibo['ID_NIT'], 
										 $recibo['SERIE_CONTRATO'],
										 $recibo['NO_CONTRATO'],
										 '',
										 '',
										 "CAMBIO DE TASA AJUSTE POR ERROR INGRESO RECIBO NO.: ".$recibo['SERIE']." ".$recibo['NO_DOCTO'],
										 json_encode($recibo),
										 'INFO',
										 $recibo['SERIE'],
										 $recibo['NO_DOCTO']);				
		}
		return false;
	}

	public function doAsignarRecibo($serie,$no_docto,$reporte_venta,$id_nit_oficial,$id_nit_motorizado){
		
			$filter=array("action"=>'getRecibo',"SERIE"=>$serie,"NO_DOCTO"=>$no_docto);
			
			$rc_detalle=$this->getListadoRecibo($filter);
			if (count($rc_detalle)<=0){
				return false;	
			}
			$recibo=$rc_detalle[0]; 
				
			$obj= new ObjectSQL();
			$obj->REPORTE_VENTA=$reporte_venta;
			$obj->ID_NIT_MOTORIZADO=$id_nit_motorizado;			
			$obj->ID_NIT_OFICIAL=$id_nit_oficial;						
			$obj->setTable('movimiento_factura');
			$SQL=$obj->toSQL("update"," where CAJA_SERIE='".$serie."' and CAJA_NO_DOCTO='".$no_docto."'"); 
			mysql_query($SQL);
			
			
			
			SysLog::getInstance()->Log($recibo['ID_NIT'], 
										 $recibo['SERIE_CONTRATO'],
										 $recibo['NO_CONTRATO'],
										 '',
										 '',
										 "ASIGNACION DE RECIBO PARA CUADRE DE COBROS.: ".$recibo['SERIE']." ".$recibo['NO_DOCTO'],
										 json_encode($recibo),
										 'INFO',
										 $recibo['SERIE'],
										 $recibo['NO_DOCTO']);		
		
		return false;
	}	
		
	public function AnularReciboCaja($serie,$no_docto,$comentario=""){
		$filtro=array("NO_DOCTO"=>$no_docto,"SERIE"=>$serie,"action"=>"getRecibo");
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		$rt=array(
			"valid"=>true,
			"mensaje"=>"Error, debe de seleccionar una caja", 
		);
		
		
		$rcb=$this->getListadoRecibo($filtro);
		$recibo=$rcb[0]; 
  
		$cj= new Caja($this->db_link); 
		if (($recibo['ANULADO']=="N") ){ 
			 
 			$obj=new ObjectSQL();
			$obj->ANULADO="S";
			$obj->ANULADO_DESCRIPCION=$comentario;
			$obj->ANULADO_DATE="concat(curdate(),' ',CURTIME())";
			$obj->ANULADO_POR_ID_NIT=UserAccess::getInstance()->getIDNIT();
			$obj->setTable("movimiento_caja");
			$SQL=$obj->toSQL("update"," WHERE NO_DOCTO='".$no_docto."' and SERIE='".$serie."' and ANULADO='N' ");
		 	mysql_query($SQL);
			
			$cj->doAnularReciboCaja($recibo['SERIE'],
									$recibo['NO_DOCTO'],
									$recibo['SERIE_CONTRATO'],
									$recibo['NO_CONTRATO']);
			
			SysLog::getInstance()->Log($recibo['ID_NIT'], 
										 $recibo['SERIE_CONTRATO'],
										 $recibo['NO_CONTRATO'],
										 '',
										 '',
										 "ANULANDO RECIBO NO.: ".$recibo['SERIE']." ".$recibo['NO_DOCTO'],
										 json_encode($recibo),
										 'ANULACION',
										 $recibo['SERIE'],
										 $recibo['NO_DOCTO']);	
										 				
			$rt['valid']=false;
			$rt['mensaje']="Recibo anulado!";										 
		}else{
			$rt['valid']=false;
			$rt['mensaje']="El recibo esta anulado!";		
		}
		
		return $rt;
	}
	public function doMarcarComoImpreso($serie,$no_docto){ 
		$filter=array("action"=>'getRecibo',"SERIE"=>$serie,"NO_DOCTO"=>$no_docto);
		$rc_detalle=$this->getListadoNRecibo($filter);
	 
		if (count($rc_detalle)<=0){
			return false;	
		}
		
		$recibo=$rc_detalle[0];  
	  
		$obj= new ObjectSQL();
		$obj->DOCTO_IMPRESO=1;
		$obj->setTable('movimiento_caja');
		$SQL=$obj->toSQL("update"," where SERIE='".$serie."' and NO_DOCTO='".$no_docto."'"); 
		mysql_query($SQL);  
		
		$testo= "RECIBO IMPRESO ".$recibo['SERIE']." ".$recibo['NO_DOCTO'];
		if ($recibo['DOCTO_IMPRESO']=="1"){
			$testo= "RE-IMPRESION RECIBO ".$recibo['SERIE']." ".$recibo['NO_DOCTO'];
		}
		
		  
		SysLog::getInstance()->Log($recibo['ID_NIT'], 
									 $recibo['SERIE_CONTRATO'],
									 $recibo['NO_CONTRATO'],
									 '',
									 '',
									 "RECIBO IMPRESO ".$recibo['SERIE']." ".$recibo['NO_DOCTO'],
									 json_encode($recibo),
									 'INFO',
									 $recibo['SERIE'],
									 $recibo['NO_DOCTO']);				
		 
		return true;
	}	
}

?>