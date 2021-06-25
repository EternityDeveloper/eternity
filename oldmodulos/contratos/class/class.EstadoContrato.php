<?php

class EstadoContrato{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
	private $_EMP_ID="";
	private $_serie_contrato="";
	private $_no_contrato="";
  
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	/*
		Obtiene el inicial que se le ha pagado a un contrato
	*/
	public function getMontosInicial($serie,$no_contrato){ 
		$inicial=array();
		if ($no_contrato>0){
			$SQL="SELECT 	
							movimiento_factura.*
						FROM `movimiento_factura`  
						INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_factura.`CAJA_SERIE` 
						AND movimiento_caja.`NO_DOCTO`=movimiento_factura.`CAJA_NO_DOCTO`) 			
					WHERE movimiento_caja.TIPO_DOC='RBC' AND  
						movimiento_caja.`NO_CONTRATO`='".$no_contrato."' AND 
						movimiento_caja.`SERIE_CONTRATO`='".$serie."'    
						 AND  movimiento_factura.TIPO_MOV='INI'";
		  
			$rs=mysql_query($SQL); 
			while($row=mysql_fetch_assoc($rs)){    
				$row['MONTO_RD']=$row['MONTO']*$row['TIPO_CAMBIO'];
				$inicial=$row;
			}	
		} 
		return $inicial;		
	}
	
	public function removerMoviemientos($serie,$no_contrato){ 
		$inicial=array();
		if ($no_contrato>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 	 
			$con=new Contratos($this->db_link); 
			$detalle=$con->getCapitalInteresCuotaFromContrato($serie,$no_contrato);	
			
			$SQL="SELECT 
					movimiento_caja.*
				FROM `movimiento_factura`  
				INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_factura.`CAJA_SERIE` 
							AND movimiento_caja.`NO_DOCTO`=movimiento_factura.`CAJA_NO_DOCTO`) 			
				WHERE  
				movimiento_caja.`NO_CONTRATO`='".$no_contrato."' AND 
				movimiento_caja.`SERIE_CONTRATO`='".$serie."'    
				 AND  movimiento_factura.TIPO_MOV NOT IN('INI','SER-FUN') 
				 AND movimiento_factura.id_usuario NOT IN ( SELECT id_usuario FROM `caja` WHERE CAJA_OFICIAL=1)  ";
				 
		 
		  	$mc=array();
			$rs=mysql_query($SQL); 
			while($row=mysql_fetch_assoc($rs)){     
				array_push($mc,$row);
				SysLog::getInstance()->Log(UserAccess::getInstance()->getIDNIT(), 
								 $row['SERIE_CONTRATO'],
								 $row['NO_CONTRATO'],
								 '',
								 '',
								 "REMOVIMIENTO RECIBOS movimiento_caja",
								 json_encode($row),
								 'ANULACION',
								 $row['SERIE'],
								 $row['NO_DOCTO']);				
			}	
			
			$SQL="SELECT 
					movimiento_factura.*
				FROM `movimiento_factura`  
				INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_factura.`SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_factura.`NO_DOCTO`) 			
				WHERE  
				movimiento_caja.`NO_CONTRATO`='".$no_contrato."' AND 
				movimiento_caja.`SERIE_CONTRATO`='".$serie."'    
				 AND  movimiento_factura.TIPO_MOV NOT IN('INI','SER-FUN') 
				 AND movimiento_factura.id_usuario NOT IN (SELECT id_usuario FROM `caja` WHERE CAJA_OFICIAL=1)  ";
			 
		  	$mf=array();
			$rs=mysql_query($SQL); 
			while($row=mysql_fetch_assoc($rs)){     
				array_push($mf,$row);
				
				SysLog::getInstance()->Log(UserAccess::getInstance()->getIDNIT(), 
								 $row['SERIE_CONTRATO'],
								 $row['NO_CONTRATO'],
								 '',
								 '',
								 "REMOVIMIENTO RECIBOS movimiento_factura",
								 json_encode($row),
								 'ANULACION',
								 $row['SERIE'],
								 $row['NO_DOCTO']);	
			}	
			
			
			 
			foreach($mc as $key=>$row){
				$SQL="DELETE FROM movimiento_caja where NO_CODIGO_BARRA='".$row['NO_CODIGO_BARRA']."'";
			 	mysql_query($SQL);				
			}			
			foreach($mf as $key=>$row){
				$SQL="SELECT * FROM `forma_pago_caja` WHERE no_docto='".$row['CAJA_NO_DOCTO']."' 
						AND SERIE='".$row['CAJA_SERIE']."'  ";  
				$rsx=mysql_query($SQL); 
				while($rowx=mysql_fetch_assoc($rsx)){ 
					SysLog::getInstance()->Log(UserAccess::getInstance()->getIDNIT(), 
									 $serie,
									 $no_contrato,
									 '',
									 '',
									 "REMOVIMIENTO RECIBOS forma_pago_caja",
									 json_encode($rowx),
									 'ANULACION',
									 $rowx['SERIE'],
									 $rowx['no_docto']);
				}
				
				$SQL="DELETE FROM movimiento_factura where ID_MOV_FACT='".$row['ID_MOV_FACT']."'";
			 	mysql_query($SQL);				
				if ($row['CAJA_NO_DOCTO']>0){
					$SQL="DELETE FROM `forma_pago_caja` WHERE no_docto='".$row['CAJA_NO_DOCTO']."' 
						AND SERIE='".$row['CAJA_SERIE']."'";
				 	mysql_query($SQL);					
					
					$SQL="DELETE FROM movimiento_contrato where no_docto='".$row['CAJA_NO_DOCTO']."' and
							CAJA_SERIE='".$row['CAJA_SERIE']."'";
				 	mysql_query($SQL);
							
					if ($row['SOLICITUD_GESTION_ID']>0){	 
						$SQL="DELETE FROM solicitud_gestion where 
							id_planilla_gestion='".$row['SOLICITUD_GESTION_ID']."' ";
					 	mysql_query($SQL);							
					}
				
				} 
				
			} 	
			
			$obj= new ObjectSQL();
			$obj->enganche=$detalle->INICIAL;
			$obj->capital_pagado=$detalle->capital_pagado;
			$obj->intereses_pagados=0;
			$obj->setTable('contratos');	
			$SQL=$obj->toSQL('update'," where no_contrato='".$no_contrato."' and serie_contrato='".$serie."'");						
			mysql_query($SQL); 
			
		}  
		
		
			
		
		
		
		return array("valid"=>true);		
	}	
	
}


?>