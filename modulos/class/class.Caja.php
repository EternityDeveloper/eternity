<?php


class Caja{
	private $db_link;
	private $_data;
	private $_fecha="";
	
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	
	public function setFecha($fecha){
		$this->_fecha=$fecha;
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
			WHERE 1=1 ";
			
			$sp=explode(" ",$documento);				
			foreach($sp as $key=>$r){
				$SQL.=" AND ( CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ',
					sys_personas.primer_apellido,' ',
					sys_personas.segundo_apellido) Like '%".$r."%' OR sys_personas.id_nit LIKE '%".$r."%' 
					OR concat(contratos.serie_contrato,' ',contratos.no_contrato) like '%".$r."%') ";	
			}  
		$rs=mysql_query($SQL);
		$data=array("valid"=>false,"data"=>array());
		while($row=mysql_fetch_assoc($rs)){
			$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']);
 			$row['id_nit_']=$row['id_nit'];
			$row['contrato_id']=System::getInstance()->Encrypt(json_encode($contrato)); 
			$row['id_nit']=System::getInstance()->Encrypt($row['id_nit']);
			array_push($data['data'],$row);
			$data['valid']=true;
		}
		
		return $data;
	}

	public function session_restart(){
		$_SESSION['CARRITO_CAJA']=array();
		$_SESSION['CAJA_OBJ']=null;
		$_SESSION['CAJA_ABONO']=array();
		$_SESSION['CARRITO_FACTURA']=array(); 
		$_SESSION['CARRITO_NC']=array(); 
	}
	
	public function searchByPerson($documento){
		$documento=mysql_real_escape_string($documento);
		
		 
		$SQL="SELECT 
				ofi.id_nit,
				CONCAT(ofi.primer_nombre,' ' ,ofi.segundo_nombre) AS nombre,
				CONCAT(ofi.primer_apellido,' ',ofi.segundo_apellido) AS apellido 
			FROM 
				sys_personas as ofi 
			WHERE 
				1=1  ";
			/*"	
			(CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre) Like '%".$documento."%' 
			 OR  sys_personas.id_nit Like '%".$documento."%' 
			 OR CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) Like '%".$documento."%'  
			OR sys_personas.fecha_nacimiento LIKE '%".$documento."%'  
			OR CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) LIKE  '%".$documento."%' 
			OR CONCAT(sys_personas.primer_nombre,' ',sys_personas.primer_apellido) LIKE  '%".$documento."%' 
			OR CONCAT(sys_personas.segundo_nombre,' ',sys_personas.primer_apellido) LIKE  '%".$documento."%'  
			)";*/
		/*CONCAT(ofi.primer_nombre,' ',ofi.segundo_nombre,' ',ofi.primer_apellido,' ',
				ofi.segundo_apellido)*/	
		$sp=explode(" ",$documento);				
		foreach($sp as $key=>$r){
			$SQL.=" AND ( CONCAT(ofi.primer_nombre,' ',ofi.segundo_nombre,' ',ofi.primer_apellido,' ',
				ofi.segundo_apellido) Like '%".$r."%' OR ofi.id_nit LIKE '%".$r."%' ) ";	
		} 	 
		
		$rs=mysql_query($SQL);
		$data=array("valid"=>false,"data"=>array());
		while($row=mysql_fetch_assoc($rs)){
 			$row['encode_nit']=System::getInstance()->Encrypt(json_encode($row));
			array_push($data['data'],$row);
			$data['valid']=true;
		}
		
		return $data;
	}
	/*SETEA UN OBJETO PARA USARLO COMO PASARELA DE TRANSFERENCIA 
	PARA SER USADO COMO SE REQUIERA EN MI CASO LO UTILIZO PARA PASAR UN CONTRATO, RESERVA, SOLICITUD ETC.*/
	public function setObject($obj){
		$_SESSION['CAJA_OBJ']=$obj;
	}
	public function getCaja($id_caja){
		$SQL="SELECT * FROM `caja` where ID_CAJA='".$id_caja."'";
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			$rt=$row;
		}
		return $rt;
	}	 
	public function getObject(){
		return $_SESSION['CAJA_OBJ'];	
	} 
	/**/
	public function setToken($token){
		$this->token=$token;	
		if (!isset($_SESSION['CARRITO_CAJA'][$this->token]['PAGOS'])){
			$_SESSION['CARRITO_CAJA'][$this->token]['PAGOS']=array(); 
		}
		if (!isset($_SESSION['CARRITO_FACTURA'])){
			$_SESSION['CARRITO_FACTURA']=array(); 
		}		
	}	
	/*AGREGA LOS RECIBOS AL CARRITO*/
	public function doItemNotaCredito($recibo){ 
		if (!isset($_SESSION['CARRITO_NC'])){
			$_SESSION['CARRITO_NC']=array(); 
		}  
 
		/*FILTRO SI EXISTE UNA NOTA DE CREDITO VALIDA*/ 
		$cn=array(
			"recibo"=>1,
 			"serie_docto"=>$recibo->SERIE,
			"no_docto"=>$recibo->NO_DOCTO,			
		);	
		   
		$rcb=$this->getListadoNotaCredito($cn);	
		if ($rcb[0]['ID_ESTATUS']!=36){
			return 3;
		} 
		$no_recibo=$recibo->SERIE.$recibo->NO_DOCTO;  
 		if (!array_key_exists($no_recibo,$_SESSION['CARRITO_NC'])){ 
			$_SESSION['CARRITO_NC'][$no_recibo]=$recibo;
 			return 1;
		}else{
			return 2;	
		} 
	}
	public function getListCarritoNotaCredito(){ 
		return $_SESSION['CARRITO_NC'];
	} 
	public function doItemNotaCreditoRemove($recibo){ 
		if (!isset($_SESSION['CARRITO_NC'])){
			$_SESSION['CARRITO_NC']=array(); 
		} 
		$no_recibo=$recibo->SERIE.$recibo->NO_DOCTO;  
 		if (array_key_exists($no_recibo,$_SESSION['CARRITO_NC'])){ 
			unset($_SESSION['CARRITO_NC'][$no_recibo]);
 			return 1;
		}else{
			return 2;	
		} 
	}	
	/*AGREGA LOS RECIBOS AL CARRITO*/
	public function doItemRecibo($recibo){ 
		if (!isset($_SESSION['CARRITO_FACTURA'])){
			$_SESSION['CARRITO_FACTURA']=array(); 
		} 
		$no_recibo=$recibo->SERIE.$recibo->NO_DOCTO;  
 		if (!array_key_exists($no_recibo,$_SESSION['CARRITO_FACTURA'])){ 
			$_SESSION['CARRITO_FACTURA'][$no_recibo]=$recibo;
 			return 1;
		}else{
			return 2;	
		} 
	}
	public function doItemReciboRemove($recibo){ 
		if (!isset($_SESSION['CARRITO_FACTURA'])){
			$_SESSION['CARRITO_FACTURA']=array(); 
		} 
		$no_recibo=$recibo->SERIE.$recibo->NO_DOCTO;  
 		if (array_key_exists($no_recibo,$_SESSION['CARRITO_FACTURA'])){ 
			unset($_SESSION['CARRITO_FACTURA'][$no_recibo]);
 			return 1;
		}else{
			return 2;	
		} 
	}
	/*REMUEVE UN RECIBO DEL LISTADO DE COBROS*/
	public function doReciboRemove($recibo,$descripcion=""){ 
		$return=array("error"=>true,"mensaje"=>'Recibo anulado!');   
		if ((!isset($recibo->SERIE)) || (!isset($recibo->NO_DOCTO))){
			$return['mensaje']="Error no existe este documento";
			return $return;
		}
		$rw=array("recibo"=>1,"serie_docto"=>$recibo->SERIE,"no_docto"=>$recibo->NO_DOCTO);
		$mc=$this->getListadoReciboSinFacturar($rw);
		if (count($mc)>0){
			$recibo=$mc[0];
 
			$ob= new ObjectSQL();
			$ob->ANULADO_DESCRIPCION=$descripcion;
			$ob->ANULADO_DATE="concat(curdate(),' ',CURTIME())";			
			$ob->ANULADO_POR_ID_NIT=UserAccess::getInstance()->getIDNIT();
			$ob->ANULADO="S";
			$ob->setTable("movimiento_caja");
			$SQL=$ob->toSQL("update"," where SERIE='".$recibo['SERIE']."' and NO_DOCTO='".$recibo['NO_DOCTO']."'");	 

			mysql_query($SQL);
			
			$listado=$this->getItemFromRecibo($recibo['SERIE'],$recibo['NO_DOCTO']);
			foreach($listado as $key =>$row){
				if (trim($row['SOLICITUD_GESTION_ID'])!=""){
					$ob= new ObjectSQL();
					$ob->estatus=26; //ANULO LA PLANILLA
					$ob->setTable("solicitud_gestion");
					$SQL=$ob->toSQL("update"," where id_planilla_gestion='".$row['SOLICITUD_GESTION_ID']."'");	
					mysql_query($SQL);	  
					SysLog::getInstance()->Log($recibo['ID_NIT'], 
									 $recibo['SERIE_CONTRATO'],
									 $recibo['NO_CONTRATO'],
									 '',
									 '',
									 "ANULANDO SOLICITUD NO.: ".$row['SOLICITUD_GESTION_ID'],
									 json_encode($row),
									 'ANULACION');	 					
				}
			}
			  
			SysLog::getInstance()->Log($recibo['ID_NIT'], 
									 $recibo['SERIE_CONTRATO'],
									 $recibo['NO_CONTRATO'],
									 '',
									 '',
									 "ANULANDO RECIBO ".$recibo['SERIE']." ".$recibo['NO_DOCTO'],
									 json_encode($recibo),
									 'ANULACION');	 
			
			$this->session_restart();	 					 
			$return['error']=false;
		}else{
			$return['mensaje']="Error no existe este documento";
		}
		
		return $return; 
	}
		
	public function getListCarritoRecibo(){ 
		return $_SESSION['CARRITO_FACTURA'];
	} 
	
	
	/*OPTIENE LOS DETALLES DE UN RECIBO*/
	public function getDetalleRecibo($SERIE,$NO_DOCTO,$TIPO_DOC){
		$SQL="SELECT mf.*,tm.DESCRIPCION AS MOV FROM movimiento_factura AS mf
				INNER JOIN `tipo_movimiento` AS tm ON (`tm`.TIPO_MOV=mf.TIPO_MOV) WHERE ";

		if ($TIPO_DOC==RECIBO_CAJA){
			$SQL.=" mf.CAJA_SERIE='".$SERIE."' AND mf.CAJA_NO_DOCTO='".$NO_DOCTO."' ";
		}
		if ($TIPO_DOC==NOTA_CREDITO){
			$SQL.=" mf.CAJA_SERIE='".$SERIE."' AND mf.CAJA_NO_DOCTO='".$NO_DOCTO."' ";
		}
		if ($TIPO_DOC==NOTA_DEBITO){
			$SQL.=" mf.CAJA_SERIE='".$SERIE."' AND mf.CAJA_NO_DOCTO='".$NO_DOCTO."' ";
		}		

		/*RECIBO DE COBROS */
		if ($TIPO_DOC==RECIBO_VIRTUAL){
			$SQL.=" SERIE='".$SERIE."' AND NO_DOCTO='".$NO_DOCTO."' ";
		}	
		/*RECIBO DE COBROS VIRTUAL*/
		if ($TIPO_DOC=="RBCV"){
	//		$SQL.=" SERIE='".$SERIE."' AND NO_DOCTO='".$NO_DOCTO."' ";
		} 
		if ($TIPO_DOC=="RCA"){
			$SQL.=" mf.CAJA_SERIE='".$SERIE."' AND mf.CAJA_NO_DOCTO='".$NO_DOCTO."' ";
		}		
		 
		$rt=array();
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){  
			array_push($rt,$row);
		}			
		return $rt;
	}	
	
	/*OPTIENE LOS ITEMS DE UN RECIBO*/
	public function getItemFromRecibo($SERIE,$NO_DOCTO){
		$SQL="SELECT * FROM movimiento_factura WHERE ESTATUS=36 AND SERIE='".$SERIE."' 
				AND NO_DOCTO='".$NO_DOCTO."' order by ID_MOV_FACT ";
 
 		$rt=array();
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){  
			array_push($rt,$row);
		}			
		return $rt;
	}		
	public function getListadoRecibosPendientePorUsarOferta($serie_contrato,$no_contrato){
		$SQL="SELECT movimiento_caja.*,
					 movimiento_factura.NO_DOCTO AS MF_NO_DOCTO,
					 movimiento_factura.SERIE as MF_SERIE,
					 movimiento_factura.TIPO_MOV AS MFTIPO_MOV 
					FROM `movimiento_caja`
			INNER JOIN `movimiento_factura` ON (movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.`NO_DOCTO` AND
			movimiento_caja.`SERIE`=movimiento_factura.CAJA_SERIE) 
		 WHERE movimiento_caja.no_contrato='".$no_contrato."' 
			and movimiento_caja.serie_contrato='".$serie_contrato."' AND movimiento_caja.ID_ESTATUS='37'
			AND movimiento_caja.ANULADO='N' ";
		 
		$rt=array();
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_object($rs)){  
			array_push($rt,$row);
		}		
		return $rt;		
	}
	/*LISTA LOS RECIBOS ABONADOS SIN FACTURAR DE UN CLIENTE/CONTRATO/RESERVA */		
	public function getListadoReciboSinFacturar($consulta=array()){

		$SQL="SELECT 
				movimiento_caja.*,
				movimiento_caja.FECHA AS FECHA_REQUERIMIENTO,
				movimiento_caja.MONTO AS MONTO_TOTAL,
				(movimiento_caja.MONTO * movimiento_caja.TIPO_CAMBIO) AS MONTO_LOCAL,
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
			WHERE movimiento_caja.ANULADO='N'  AND movimiento_factura.estatus!=38  
			 and movimiento_caja.TIPO_DOC!='".RECIBO_CAJA."' 
			 	AND  movimiento_caja.TIPO_DOC NOT IN ('ND','NC') ";

		$rt=array();	
 		if (count($consulta)>0){
			if ($consulta['reserva']==1){
				$SQL.=" AND   movimiento_caja.`ID_RESERVA`='".$consulta['id_reserva']."' AND 
					movimiento_caja.`NO_RESERVA`='".$consulta['no_reserva']."'";
			}
			if ($consulta['recibo']==1){
				$SQL.=" AND   movimiento_caja.`SERIE`='".$consulta['serie_docto']."' AND 
					movimiento_caja.`NO_DOCTO`='".$consulta['no_docto']."'";
			}			
			if ($consulta['contrato']==1){
				$SQL.=" AND   
						movimiento_caja.`NO_CONTRATO`='".$consulta['no_contrato']."' AND 
						movimiento_caja.`SERIE_CONTRATO`='".$consulta['serie_contrato']."' ";
			}	
			if ($consulta['cliente']==1){
				$SQL.=" AND  movimiento_caja.`ID_NIT`='".$consulta['id_nit']."' ";
			}
										
			$SQL.=" GROUP BY movimiento_caja.SERIE,movimiento_caja.NO_DOCTO,movimiento_caja.`NO_CONTRATO`,movimiento_caja.`SERIE_CONTRATO`";
		 	 
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){  
				array_push($rt,$row);
			}			
		}
		
		return $rt;
	}	
	/**/
	public function getListadoNotaCredito($consulta=array()){

		$SQL="SELECT 
				movimiento_caja.*,
				movimiento_caja.FECHA AS FECHA_REQUERIMIENTO,
				movimiento_caja.MONTO AS MONTO_TOTAL,
				(movimiento_caja.MONTO * movimiento_caja.TIPO_CAMBIO) AS MONTO_LOCAL,
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
			WHERE movimiento_caja.ANULADO='N'  AND movimiento_factura.estatus!=38  
			 	AND  movimiento_caja.TIPO_DOC IN ('NC') ";
				

		$rt=array();	
 		if (count($consulta)>0){
			if ($consulta['reserva']==1){
				$SQL.=" AND   movimiento_caja.`ID_RESERVA`='".$consulta['id_reserva']."' AND 
					movimiento_caja.`NO_RESERVA`='".$consulta['no_reserva']."'";
			}
			if ($consulta['recibo']==1){
				$SQL.=" AND   movimiento_caja.`SERIE`='".$consulta['serie_docto']."' AND 
					movimiento_caja.`NO_DOCTO`='".$consulta['no_docto']."'";
			}			
			if ($consulta['contrato']==1){
				$SQL.=" AND   
						movimiento_caja.`NO_CONTRATO`='".$consulta['no_contrato']."' AND 
						movimiento_caja.`SERIE_CONTRATO`='".$consulta['serie_contrato']."' ";
			}	
			if ($consulta['cliente']==1){
				$SQL.=" AND  movimiento_caja.`ID_NIT`='".$consulta['id_nit']."' ";
			}
										
			$SQL.=" GROUP BY movimiento_caja.SERIE,movimiento_caja.NO_DOCTO,movimiento_caja.`NO_CONTRATO`,movimiento_caja.`SERIE_CONTRATO`";
		 	 
		 
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){  
				array_push($rt,$row);
			}			
		}
		
		return $rt;
	}	
	/*LISTA LOS RECIBOS ABONADOS SIN USAR DE UN CLIENTE/CONTRATO/RESERVA */		
	public function getListadoRecibo($consulta=array()){

		$SQL="SELECT 
				movimiento_caja.* ,
				movimiento_factura.*,
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
			INNER JOIN `movimiento_factura` ON (movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.`NO_DOCTO` 
				AND movimiento_caja.`SERIE`=movimiento_factura.`CAJA_SERIE`) 
			INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=movimiento_factura.TIPO_MOV)
			WHERE movimiento_caja.ANULADO='N'  ";
			

		$rt=array();	
 		if (count($consulta)>0){
			if ($consulta['reserva']==1){
				$SQL.=" AND   movimiento_caja.`ID_RESERVA`='".$consulta['id_reserva']."' AND 
					movimiento_caja.`NO_RESERVA`='".$consulta['no_reserva']."'";
			}
			if ($consulta['recibo']==1){
				$SQL.=" AND   
						movimiento_caja.`SERIE`='".$consulta['serie_docto']."' AND 
						movimiento_caja.`NO_DOCTO`='".$consulta['no_docto']."' ";
			}			
  
  		 
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){  
				array_push($rt,$row);
			}			
		}
		
		return $rt;
	}	 
	public function getItem($token){ 
		return $_SESSION['CARRITO_CAJA'][$token]['PAGOS'];
	}	
	/*OPTENER TASA DE CAMBIO*/ 
	public function getTasaActual($tipo_moneda){
		$SQL="SELECT * FROM `tasa_cambio` WHERE moneda='".$tipo_moneda."' ORDER BY indice DESC LIMIT 1";  		
		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
		return $row['cambio'];
	}		
	/*RETORNA UN ARRAY DE LOS MONTOS ACUMULADOS TANTO EL MONTO A PAGAR 
		COMO EL MONTO QUE ESTA ABONANDO EN CAJA EN EL MOMENTO*/
	public function getMontoApagarYCobrar($token){
		//$monto_a_cobrar=$this->getItemMontoACobrar($token);  
		$monto_a_cobrar=1;//$this->getItemMontoACobrar($token); 
		$this->setToken($token);
		$listado=$this->getListCarritoRecibo(); 
		$monto_c=array('monto_a_cobrar'=>0,'tasa_monto_a_cobrar'=>1);
		
		foreach($listado as $key =>$row){
			$monto_c['monto_a_cobrar']=$monto_c['monto_a_cobrar']+$row->MONTO_TOTAL;
			//$monto_a_cobrar=$row->MONTO_TOTAL;
		} 		
		$monto_acumulados=$this->getItemMontoAPagar($token);
		
		/*VALIDA SI EL MONTO ACUMULADO ES MAYOR O IGUAL AL MONTO A COBRAR*/
		$mc=0;
		if (round($monto_acumulados['monto_p_pagar'],2)>=$monto_a_cobrar){
			$mc=1;	
		}  
		return array(
						'monto_a_pagar'=>round($monto_c['monto_a_cobrar'],2),
						'tasa_monto_a_cobrar'=>$monto_c['tasa_monto_a_cobrar'],
						'monto_completo'=>$mc,
						'monto_acumulado'=>$monto_acumulados['monto_p_pagar'],
						'monto_acumulado_tasa'=>$monto_acumulados['tasa_cambio']						
					);
	}  
	public function getItemMontoAPagar($token){
		$list=$this->getItem($token); 
		$retur=array("monto_p_pagar"=>0,'tasa_cambio'=>1); 
		$monto_p_pagar=0;
		if (count($list)>0){ 
			foreach($list as $key=>$val){
			//	$monto_p_pagar=$monto_p_pagar+($val['tipo_cambio']*$val['monto_a_pagar']);	
				$retur['monto_p_pagar']=$retur['monto_p_pagar']+$val['monto_a_pagar'];
				$retur['tasa_cambio']=$val['tipo_cambio'];	
			}
		}
		return $retur;		
	} 
	public function getItemMontoACobrar($token){ 
		$monto_a_cobrar=0; 
		$objeto=$this->getObject();  
		/*VERIFICO EL TIPO DE OBJETO QUE ES*/		
		if ($objeto!=null){ 
			/*VERIFICO SI EL TIPO DE OBJETO ES UN CONTRATO*/
			if (isset($objeto->serie_contrato) && isset($objeto->no_contrato)){
				SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
				SystemHtml::getInstance()->includeClass("contratos","Contratos");  
				 
				$cobros= new Cobros($this->db_link);  
				$_contratos=new Contratos($this->db_link);
				$d_contrato=$_contratos->getInfoContrato($objeto->serie_contrato,$objeto->no_contrato);				
				/*
					OBTENGO EL AVISO DE COBRO GENERADO A ESE CONTRATO PARA ASI OBTENER 
					EL MONTO A COBRAR DE ESE AVISO
				*/
				$info=$cobros->getAvisoCobroData($objeto->serie_contrato,$objeto->no_contrato);
			
				$monto=0;
		 		foreach($info as $key=>$rw){
					//$monto=$monto+$rw['monto_acobrar']; 
				}
					
				if ($monto>0){
					$monto_a_cobrar= $monto;
				}else{
					/*
						EN ESTE PROCESO VALIDO SI HAN SELECCIONADO CUOTAS DE UN CLIENTE
						SI EL MONTO es mayor que 2 entonces cambio el valor de la cuota por los montos seleccionados
					*/
					$monto=$cobros->getMontoSeleccionadoCuotas($token);  
				//	$monto_a_cobrar=$d_contrato->valor_cuota;
					$monto_a_cobrar=$monto;
					  	
				}
			}  
		}  
		
		$desc=$this->getDescuento($token);  
		if (count($desc)>0){ 
			$descuentos=0;
			foreach($desc as $key=>$val){
				$id=System::getInstance()->Encrypt(json_encode($val));
				$desc=json_decode(System::getInstance()->Decrypt($val['descuento_id'])); 
				
				if ($desc->monto_ingresado=="S"){ 
					$desc->monto=$val['monto']; 
				}
				if ($desc->monto>0){
					$descuentos=$descuentos+$desc->monto;
				}else if ($desc->porcentaje>0){
					//echo $desc->porcentaje;
				}				
				//$monto_a_cobrar=$monto_a_cobrar+($val['tipo_cambio']*$val['monto_a_pagar']);	
			}
			$monto_a_cobrar=$monto_a_cobrar-$descuentos;
		}		
		
		return	$monto_a_cobrar;
	} 
	/*AGREGA LOS PAGOS AL CARRITO*/
	public function doItem($token,$data){ 
		if (!isset($_SESSION['CARRITO_CAJA'][$token]['PAGOS'])){
			$_SESSION['CARRITO_CAJA'][$token]['PAGOS']=array(); 
		}   
		if (!array_key_exists($data['forma_pago'],$_SESSION['CARRITO_CAJA'][$token]['PAGOS'])){ 
			$_SESSION['CARRITO_CAJA'][$token]['PAGOS'][$data['forma_pago']]=$data;
			return 1;
		}else{
			return 2;	
		} 
	}
	/*Elimina un item del listado del carrito de la caja*/
	public function removeItem($token,$data){   
		if (isset($_SESSION['CARRITO_CAJA'][$token]['PAGOS'])){
			if (array_key_exists($data->forma_pago,$_SESSION['CARRITO_CAJA'][$token]['PAGOS'])){ 
				unset($_SESSION['CARRITO_CAJA'][$token]['PAGOS'][$data->forma_pago]);
				return 1;
			}else{
				return 2;	
			} 
		}
	}	
	/*Agrega los descuentos a la caja*/
	public function addDescuento($token,$data){ 
		if (!isset($_SESSION['CARRITO_CAJA'][$token]['DECUENTO'])){
			$_SESSION['CARRITO_CAJA'][$token]['DECUENTO']=array(); 
		}     
		if (!array_key_exists($data['codigo'],$_SESSION['CARRITO_CAJA'][$token])){ 
			$_SESSION['CARRITO_CAJA'][$token]['DECUENTO'][$data['codigo']]=$data;
			return 1;
		}else{
			return 2;	
		} 
	}
	/*Obtiene los descuentos */
	public function getDescuento($token){
		return $_SESSION['CARRITO_CAJA'][$token]['DECUENTO'];
	}	
	/*Elimina los descuentos*/		
	public function removeDescuento($token,$data){  
		if (isset($_SESSION['CARRITO_CAJA'][$token]['DECUENTO'])){
			if (array_key_exists($data->codigo,$_SESSION['CARRITO_CAJA'][$token]['DECUENTO'])){ 
				unset($_SESSION['CARRITO_CAJA'][$token]['DECUENTO'][$data->codigo]);
				return 1;
			}else{
				return 2;	
			} 
		}
	}  
	/*Metodo que agrega los abonos seleccionados el cual seran aplicado a un contrato x*/
	public function doItemListAbono($item,$cmd){
		$_item=json_decode(System::getInstance()->Decrypt($item)); 
		
		if (!isset($_SESSION['CAJA_ABONO'])){
			$_SESSION['CAJA_ABONO']=array();
		}     
		
		/*REMUEVE UN ITEM*/
		if ($cmd=="remove"){
 			/*VERIFICO QUE EN EL ARRAY NO HALLA MAS ITEMS */
			$getitem=$this->getItemListAbono($token);
			if (array_key_exists($item,$getitem)){ 
				unset($_SESSION['CAJA_ABONO'][$item]);
				return 1;
			}
			
 			return true;
		}		
		
		/*AGREGA UN ITEM*/
		if ($cmd=="add"){
			$getitem=$this->getItemListAbono();
			if (count($getitem)>0){	
				if (!array_key_exists($item,$getitem)){ 
					$_SESSION['CAJA_ABONO'][$item]=$_item;
					return 1;
				}else{
					return 2;	
				} 			
			}else{
				$_SESSION['CAJA_ABONO'][$item]=$_item;
				return 1;
			}
			
		}

		return false;
	}
	public function getItemListAbono(){
		if (!isset($_SESSION['CAJA_ABONO'])){
			$_SESSION['CAJA_ABONO']=array();
		} 		
		return $_SESSION['CAJA_ABONO'];
	}
	public function updateItemListAbono($item_key,$obj){
		if (!isset($_SESSION['CAJA_ABONO'])){
			$_SESSION['CAJA_ABONO']=array();
		} 		
		$comparer=array();   
		foreach($_SESSION['CAJA_ABONO'] as $key=>$row){
			if ($row->SERIE.$row->NO_DOCTO==$item_key){
				$_SESSION['CAJA_ABONO'][$key]=$obj;
			}
		}	 
		return $_SESSION['CAJA_ABONO'];
	}	
	
	

	/*
		OPTIENE LOS MONTOS DE INCIALES Y ABONOS QUE SE HAN RESITRADO EN CAJA LOS CUALES
		EL CLIENTE NO HA UTILIZADO 
	*/
	public function getListSaldosAfavor($id_nit,$moneda){
		/*
			SI EN CAJA EL MONTO NO TIENE ASIGNADO CONTRATO ES POR QUE EL MONTO ESTA A FAVOR DEL CLIENTE
			EN CASO CONTRARIO ES QUE YA UTILIZO ESTE MONTO 
		*/
		$SQL="SELECT  
					movimiento_caja.TIPO_DOC,
					movimiento_caja.TIPO_MONEDA,
					movimiento_caja.FECHA, 
					movimiento_factura.*,
					movimiento_factura.MONTO AS MONTO_RD,
					(movimiento_factura.MONTO) AS MONTO,
					caja.DESCRIPCION_CAJA AS CAJA ,
					`tipo_documento`.`DOCUMENTO`,
					tipo_movimiento.DESCRIPCION AS TMOVIMIENTO,
					(SELECT cambio FROM `tasa_cambio` WHERE moneda='".$moneda."'  
					ORDER BY indice DESC LIMIT 1) as tasa_cambio_actual	
			FROM `movimiento_caja` 
		INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
		INNER JOIN `movimiento_factura` ON (movimiento_factura.`CAJA_SERIE`=movimiento_caja.SERIE 
			AND `movimiento_factura`.`CAJA_NO_DOCTO`=movimiento_caja.`NO_DOCTO`)	
		INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=movimiento_factura.TIPO_MOV)					
		INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
		WHERE movimiento_caja.id_nit='".$id_nit."'     
			AND  movimiento_caja.TIPO_DOC IN ('".NOTA_CREDITO."','".RECIBO_CAJA."','NC')
			AND movimiento_caja.ANULADO='N' AND ((movimiento_factura.serie_contrato IS NULL 
			OR movimiento_caja.serie_contrato='') 
			OR (movimiento_caja.no_contrato IS NULL OR movimiento_caja.no_contrato=''))
			AND movimiento_caja.ID_ESTATUS IN (36,37) ";
		  
		$rs=mysql_query($SQL);
		$data=array();
		$getitem=$this->getItemListAbono(); 
		$comparer=array();
		foreach($getitem as $key=>$row){
			$comparer[$row->SERIE.$row->NO_DOCTO]=$row;
		}
		while($row=mysql_fetch_assoc($rs)){  
			$row['SHOW_BOTTOM']=false;
			$row['MONTO_RD']= ($row['MONTO_RD']*$row['TIPO_CAMBIO']); 
			if ($moneda!=$row['TIPO_MONEDA']){
				$row['SHOW_BOTTOM']=true;  
				if (array_key_exists($row['SERIE'].$row['NO_DOCTO'],$comparer)){ 				 
					$row['TIPO_CAMBIO']=$comparer[$row['SERIE'].$row['NO_DOCTO']]->TIPO_CAMBIO;
					$row['MONTO']=($row['MONTO']/$row['TIPO_CAMBIO']); 		 		
				}else{
					$row['MONTO']=($row['MONTO']/$row['tasa_cambio_actual']);
					$row['TIPO_CAMBIO']=$row['tasa_cambio_actual'];
				 
				}
				
			}	 
			array_push($data,$row);
		}
		
		return $data;
	}

	/*
		OPTIENE LOS MONTOS DE ABONOS LOS CUALES NO ESTEN ASIGNADO
		INICIAL
	*/
	public function getListAbonoSinInicial($id_nit){
		/*
			SI EN CAJA EL MONTO NO TIENE ASIGNADO CONTRATO ES POR QUE EL MONTO ESTA A FAVOR DEL CLIENTE
			EN CASO CONTRARIO ES QUE YA UTILIZO ESTE MONTO 
		*/
		$SQL="SELECT 
			*,
			caja.DESCRIPCION_CAJA AS CAJA ,
			`tipo_documento`.`DOCUMENTO`
			FROM `movimiento_caja` 
		INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
		INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
		WHERE movimiento_caja.id_nit='".$id_nit."' AND ((serie_contrato IS NULL OR serie_contrato='') 
		OR (no_contrato IS NULL OR no_contrato='')) AND ((NO_DOCTO_INICIAL IS NULL OR NO_SERIE_INICIAL='') 
		OR (NO_SERIE_INICIAL IS NULL OR NO_DOCTO_INICIAL='') ) and movimiento_caja.TIPO_MOV in ('RES')"; 
	
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		
		return $data;
	}	
	
	/*BUSCA UNA PERSONA POR EL NUMERO DE RESERVA O # DE DOCUMENTO*/
	public function searchByReserva($documento){
		$documento=mysql_real_escape_string($documento);
		
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 
		$reserva= new Reserva($this->db_link,$_REQUEST);

		$SQL="SELECT 
					reserva_inventario.no_reserva,
					sys_personas.id_nit,
					concat(sys_personas.primer_nombre,' ' ,
					sys_personas.segundo_nombre) as nombre,
					concat(sys_personas.primer_apellido,' ',
					sys_personas.segundo_apellido) as apellido, 
					reserva_inventario.no_recibo,
					sys_status.descripcion as estatus
					 FROM 
				`reserva_inventario` 
				INNER JOIN `reserva_ubicaciones` ON(`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva)
				INNER JOIN sys_personas ON (sys_personas.id_nit=reserva_inventario.id_nit)
				INNER JOIN sys_status ON (sys_status.id_status=reserva_inventario.estatus)
			WHERE sys_status.id_status=1 ";
 
			$sp=explode(" ",$documento);				
			foreach($sp as $key=>$r){
				$SQL.=" AND ( CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ',sys_personas.primer_apellido,' ',
					sys_personas.segundo_apellido) Like '%".$r."%' OR sys_personas.id_nit LIKE '%".$r."%' 
					OR reserva_inventario.no_recibo LIKE '%".$r."%' 
					OR  reserva_inventario.no_reserva  LIKE '%".$r."%' ) ";	
			} 
			$SQL.=" GROUP BY reserva_inventario.no_reserva ";
			
		 
			$rs=mysql_query($SQL);
			$data=array("valid"=>false,"data"=>array());
			while($row=mysql_fetch_assoc($rs)){
				$row['enc_reserva']=System::getInstance()->Encrypt($row['no_reserva']);
				$row['total_reserva']=$reserva->getTotalReserva($row['no_reserva']);
				array_push($data['data'],$row);
				$data['valid']=true;
			}
		
		return $data;
	}	

	/*ARREGLAR ESTA FUNCION OJO*/
	public function getMontoFromReserva($serie_contrato,$no_contrato){
		$SQL="SELECT SUM(pago_reservas.monto) AS monto FROM `inventario_jardines` 
INNER JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva`)
INNER JOIN `pago_reservas` ON (pago_reservas.`no_reserva`=inventario_jardines.`no_reserva` AND 
pago_reservas.`id_reserva`=inventario_jardines.`id_reserva`)
WHERE inventario_jardines.`serie_contrato`='".$serie_contrato."' AND  inventario_jardines.`no_contrato`='".$no_contrato."' AND 
reserva_inventario.`estatus`='1' ";
 
		$rs=mysql_query($SQL);
		$rt=0;
		$row=mysql_fetch_assoc($rs);
		if ($row['monto']>0){
			$rt=$row['monto'];
		}
		
		return $rt;
	}
	
	/*OBTENER EL MONTO TOTAL DE LOS ABONOS A RESERVA DE UN CLIENTE*/
	public function getMontoAbonoFromReserva($no_reserva,$id_nit){
		$SQL="SELECT  SUM(MONTO) AS MONTO FROM movimiento_caja
WHERE movimiento_caja.`NO_RESERVA`='".mysql_real_escape_string($no_reserva)."' AND `ID_NIT`='".$id_nit."'";	
   
		$rs=mysql_query($SQL);
		$rt=0;
		$row=mysql_fetch_assoc($rs);
	
		if ($row['MONTO']>0){ 
			$rt=$row['MONTO'];
		}
		
		return $rt;
	}
	
	/*CAPTURA EL MONTO TOTAL ACUMULADO DEL ENGANCHE*/
	public function getMontoEnganche($serie_contrato,$no_contrato){
		$SQL="SELECT enganche FROM `contratos` WHERE `serie_contrato`='".$serie_contrato."' AND 
				`no_contrato`='".$no_contrato."'";	
		$rs=mysql_query($SQL);
		$rt=0;
		while($row=mysql_fetch_assoc($rs)){
			$rt=$row['enganche'];
		} 
		return $rt;
	}	
 
 	/*OBTIENE UN NUMERO DE DOCUMENTO PARA LOS CASOS DE RECIBO CAJA*/
	public function getNextNoDocument($SERIE_DOC){
		$SQL="SELECT (COUNT(*)+1) AS NO_DOCTO FROM `movimiento_caja` WHERE SERIE='".$SERIE_DOC."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['NO_DOCTO'];
 	}	
	/*GENERA UNA NOTA DE CREDITO CON COMPROBANTE*/
	public function doCreateNotaCreditoConComprobante($recibo,$comentario){
		$rt=array("mensaje"=>"Error no se puede procesar esta solicitud","valid"=>false);
		SystemHtml::getInstance()->includeClass("caja","Recibos"); 
		$filtro=array('SERIE'=>$recibo->SERIE,'NO_DOCTO'=>$recibo->NO_DOCTO,"action"=>"getRecibo");	 
		$rc=new Recibos($this->db_link);		
		$rbc=$rc->getListadoRecibo($filtro);
		$recibo= new ObjectSQL();
		$recibo->push($rbc[0]); 
		if (!isset($recibo->NO_CODIGO_BARRA)){
			$rt['valid']=false;
			$rt['mensaje']="Recibo no existe!";
			return $rt;
		}
 
/*	 	if ($recibo->ID_ESTATUS!=36){
			$rt['valid']=false;
			$rt['mensaje']="No se puede aplicar otra nota de credito a esta factura!";
			return $rt;
		}*/
		
		$rt['valid']=true;
		$rt['mensaje']="Documento generado!";		
	 	 
	 
		$DOCT=MVFactura::GI()->doCreateDocument($recibo->ID_NIT,
											$recibo->EM_ID,
											$recibo->NO_CONTRATO,
											$recibo->SERIE_CONTRATO,
											NOTA_DEBITO,
											NOTA_DEBITO,
											0,
											0,
											$recibo->MONTO,
											1,
											0);	
											
		 								
		/*ASIGNANDO LA NOTA DE DEBITO AL DOCUMENTO*/									
		$obj= new ObjectSQL();
		$obj->CAJA_NO_DOCTO=$DOCT->NO_DOCTO;
		$obj->CAJA_SERIE=$DOCT->SERIE;
		$obj->setTable("movimiento_factura");
		$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' and NO_DOCTO='".$DOCT->NO_DOCTO."'");
		mysql_query($SQL);	
		
		$obj= new ObjectSQL();
		$obj->NOTA_CD_NO_DOCTO=$recibo->NO_DOCTO;
		$obj->NOTA_CD_SERIE=$recibo->SERIE; 
		$obj->setTable("movimiento_caja");
		$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' and NO_DOCTO='".$DOCT->NO_DOCTO."'");		 
		mysql_query($SQL);		
		
		
		$obj= new ObjectSQL();
		$obj->TIPO_DOC_ANUL=$DOCT->TIPO_DOC;
		$obj->SERIE_DOC_ANUL=$DOCT->SERIE;
		$obj->NO_DOC_ANUL=$DOCT->NO_DOCTO; 
		$obj->setTable("movimiento_caja");
		$SQL=$obj->toSQL("update"," where NO_CODIGO_BARRA='".$recibo->NO_CODIGO_BARRA."'");		 
		mysql_query($SQL);					
		/* ---------------------------------------- */		 							
													
		$DOCT=MVFactura::GI()->doCreateDocument($recibo->ID_NIT,
											$recibo->EM_ID,
											$recibo->NO_CONTRATO,
											$recibo->SERIE_CONTRATO,
											NOTA_CREDITO,
											NOTA_CREDITO,
											0,
											0,
											$recibo->MONTO,
											1,
											0);												

		SysLog::getInstance()->Log($recibo->ID_NIT,
								  $recibo->SERIE_CONTRATO,
								  $recibo->NO_CONTRATO,
								 '',
								 '',
								 "APLICANDO NOTA DE CREDITO CLIENTE ".$recibo->ID_NIT." DOCUMENTO ".$recibo->SERIE."-".$recibo->NO_DOCTO,
								 json_encode($recibo),
								 'NOTA_CREDITO',
								 $recibo->SERIE,
								 $recibo->NO_DOCTO);	
 
										
		/*ASIGNANDO LA NOTA DE CREDITO AL DOCUMENTO*/									
		$obj= new ObjectSQL();
		$obj->CAJA_NO_DOCTO=$DOCT->NO_DOCTO;
		$obj->CAJA_SERIE=$DOCT->SERIE;
		$obj->setTable("movimiento_factura");
		$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' and NO_DOCTO='".$DOCT->NO_DOCTO."'");
		mysql_query($SQL);	
		
		$obj= new ObjectSQL();
		$obj->NOTA_CD_NO_DOCTO=$recibo->NO_DOCTO;
		$obj->NOTA_CD_SERIE=$recibo->SERIE; 
		$obj->setTable("movimiento_caja");
		$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' and NO_DOCTO='".$DOCT->NO_DOCTO."'");
		mysql_query($SQL);	
  
		$estatus=44;
		/*SI EL MONTO DEL RECIBO ES IGUAL A LAS NOTAS DE CREDITO ENTONCES 
		EL ESTATUS ES UTILIZADO EN NOTAS DE CREDITO*/
		if ($monto_total==$monto_recibo){
			$estatus=45;
		}			
		/*PASO EL DOCUMENTO A ESTATUS A APLICANDO NOTA DE CREDITO*/									
		$obj= new ObjectSQL();
		$obj->ID_ESTATUS=$estatus;
		$obj->setTable("movimiento_caja");
		$SQL=$obj->toSQL("update"," where SERIE='".$recibo->SERIE."' and NO_DOCTO='".$recibo->NO_DOCTO."'");	
		mysql_query($SQL);			
	
		 
		return $rt;
	}	
	public function doCreateNotaCredito($recibo,$data_montos,$comentario){
		$rt=array("mensaje"=>"Error no se puede procesar esta solicitud","valid"=>false);
		SystemHtml::getInstance()->includeClass("caja","Recibos"); 
		$rc=new Recibos($this->db_link);		
		$monto=$rc->getMontoNotaCD($recibo->SERIE,$recibo->NO_DOCTO);
	
		$dc_monto=0;
		if ($monto>0){
			$dc_monto=$monto;
		}
		$valor_in=0;
		foreach($data_montos as $key=>$valor_monto){
			$valor_in=$valor_in+$valor_monto;
		}
		$monto_recibo=$recibo->MONTO-$dc_monto;	
		if ($valor_in>round($monto_recibo,2)){
			return $rt;
		}
		
		$monto_total=round($valor_in+$monto_recibo,2);
		
		$rt['valid']=true;
		$rt['mensaje']="Documento generado!";
		
		foreach($data_montos as $key=>$valor_monto){
			$DOCT=MVFactura::GI()->doCreateDocument($recibo->ID_NIT,
												$recibo->EM_ID,
												$recibo->NO_CONTRATO,
												$recibo->SERIE_CONTRATO,
												"NC",
												"NC",
												0,
												0,
												$valor_monto,
												1,
												0);												

			SysLog::getInstance()->Log($recibo->ID_NIT,
									  $recibo->SERIE_CONTRATO,
									  $recibo->NO_CONTRATO,
									 '',
									 '',
									 "APLICANDO NOTA DE CREDITO CLIENTE ".$recibo->ID_NIT." DOCUMENTO ".$recibo->SERIE."-".$recibo->NO_DOCTO,
									 json_encode($recibo),
									 'NOTA_CREDITO',
									 $recibo->SERIE,
									 $recibo->NO_DOCTO);	
	
	
								 			
			/*ASIGNANDO LA NOTA DE CREDITO AL DOCUMENTO*/									
			$obj= new ObjectSQL();
			$obj->NOTA_CD_NO_DOCTO=$recibo->NO_DOCTO;
			$obj->NOTA_CD_SERIE=$recibo->SERIE;
			$obj->CAJA_NO_DOCTO=$DOCT->NO_DOCTO;
			$obj->CAJA_SERIE=$DOCT->SERIE;
			$obj->setTable("movimiento_caja");
			$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' and NO_DOCTO='".$DOCT->NO_DOCTO."'");	
			mysql_query($SQL);
			
			/*
			$obj= new ObjectSQL();
			$obj->setTable("movimiento_caja");
			$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' and NO_DOCTO='".$DOCT->NO_DOCTO."'");	
			mysql_query($SQL);			*/
			
			$estatus=44;
			/*SI EL MONTO DEL RECIBO ES IGUAL A LAS NOTAS DE CREDITO ENTONCES 
			EL ESTATUS ES UTILIZADO EN NOTAS DE CREDITO*/
			if ($monto_total==$monto_recibo){
				$estatus=45;
			}			
			/*PASO EL DOCUMENTO A ESTATUS A APLICANDO NOTA DE CREDITO*/									
			$obj= new ObjectSQL();
			$obj->ID_ESTATUS=$estatus;
			$obj->setTable("movimiento_caja");
			$SQL=$obj->toSQL("update"," where SERIE='".$recibo->SERIE."' and NO_DOCTO='".$recibo->NO_DOCTO."'");	
			mysql_query($SQL);			
	
		}
		return $rt;
	}
	/*
		Restaura un recibo a disponible cuando un 
		contrato es anulado o desistido	
	*/
	public function Reestablecer_pagos($serie_contrato,$no_contrato){
		
		$SQL="SELECT * FROM `movimiento_caja` WHERE no_contrato='".$no_contrato."' 
			AND serie_contrato='".$serie_contrato."' and ANULADO='N' AND ID_ESTATUS in (38,37,36) ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
 			/*VALIDO SI TIENE CIERRE DE CAJA*/
		//	if (trim($row['ID_CIERRE_CAJA'])==""){	
				/*ASIGNANDO LA NOTA DE CREDITO AL DOCUMENTO*/									
				$obj= new ObjectSQL();
				$obj->ID_ESTATUS=36;
				$obj->NO_CONTRATO='';
				$obj->SERIE_CONTRATO='';				
				$obj->setTable("movimiento_caja");
				$SQL=$obj->toSQL("update"," where SERIE='".$row['SERIE']."' and 
								NO_DOCTO='".$row['NO_DOCTO']."'");	
				mysql_query($SQL);				

				SysLog::getInstance()->Log($row['ID_NIT'],
										  $row['SERIE_CONTRATO'],
										  $row['NO_CONTRATO'],
										 '',
										 '',
										 "REVERCION DE INICIAL - ESTATUS DISPONIBLE PARA USO ".$row['ID_NIT']." DOCUMENTO ".$row['SERIE']."-".$row['NO_DOCTO'],
										 json_encode($row),
										 'TRANSACCION',
										 $row['SERIE'],
										 $row['NO_DOCTO']
										 );		
										 			
			}
			
			
	//	}		
	}
	
	/*VERIFICA SI UNA FACTURA FUE GENERADA*/
	public function checkExistFactura($serie,$no_documento){
		$SQL="SELECT COUNT(*) AS tt FROM `facturacion` WHERE  
				WHERE `NO_DOCTO`='". mysql_real_escape_string($no_documento) ."' and
				`SERIE`='". mysql_real_escape_string($serie) ."' and TIPO_DOC='".$tipo_doc."' ";
			 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['tt'];
	}	
	/*OPTIENE LA SIGUIENTE FACTURA*/
	public function getNextFactura($tipo_doc){
		
		$SQL="LOCK TABLES correlativo_doc WRITE;";
		mysql_query($SQL);	
			
		$cor = new ObjectSQL(); 
		$cor->CORRELATIVO="(CORRELATIVO+1)"; 
		$cor->setTable('correlativo_doc'); 
		$SQL=$cor->toSQL('update',"where TIPO_DOC='".$tipo_doc."'");
		mysql_query($SQL);		
		$SQL="SELECT `SERIE`,(`CORRELATIVO`)AS CORRELATIVO 
			FROM `correlativo_doc` WHERE `TIPO_DOC`='".$tipo_doc."' "; 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		
		$SQL="UNLOCK TABLES;"; 
		mysql_query($SQL);		
					
		if (count($row)>0){
			$cajero=UserAccess::getInstance()->getCaja();  
			$cor = new ObjectSQL();
			$cor->TIPO_DOC=$tipo_doc;
			$cor->CORRELATIVO=$row['CORRELATIVO'];
			$cor->SERIE=$row['SERIE'];			
 			$cor->ID_CAJA=$cajero['ID_CAJA'];  
			$cor->ID_USUARIO=$cajero['id_usuario'];
 			$cor->setTable('correlativo_doc_logs'); 
			$SQL=$cor->toSQL('insert');	
			mysql_query($SQL);	 			
		}
		return $row;
	}		 
	/*FACTURAR RECIBO*/
	public function doFacturarRecibo($serie_recibo="",$no_recibo=""){ 
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("caja","ModContable"); 
		SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 
		SystemHtml::getInstance()->includeClass("caja","Recibos");				

		$this->_data=$_REQUEST;
	 
		if (!validateField($this->_data,"recibo")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado un recibo!');
			return $data; 
			exit;	
		}
		$recibo=json_decode(System::getInstance()->Decrypt($this->_data['recibo']));
		if (!validateField($recibo,"SERIE")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado un recibo!');
			return $data; 
			exit;	
		}
		 
		if (!validateField($recibo,"NO_DOCTO")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado un recibo!');
			return $data; 
			exit;	
		}	
		/*FECHA EN QUE SE QUIERE QUE SALGA LA TRANSACCION EN CASO DE NO PODER CERRAR LA CAJA*/
		$fecha_requerimiento="";	
		if (validateField($this->_data,"fecha_requerimiento_especial_xx")){
			$fecha_requerimiento=$this->_data['fecha_requerimiento_especial_xx'];	
			$this->setFecha($fecha_requerimiento);
			MVFactura::getInstance()->setFecha($fecha_requerimiento);
		}

		$cn=array();
		$cn['serie']="";
		$cn['no_contrato']="";
		if (validateField($this->_data,"contrato")){
			$contr=json_decode(System::getInstance()->Decrypt($this->_data['contrato']));
			$cn['serie']=$contr->serie_contrato;
			$cn['no_contrato']=$contr->no_contrato;			
		}			
	
		$rc= new Recibos($this->db_link);
		$filtro=array(	
						"action"=>"getRecibo",
						"NO_DOCTO"=>$recibo->NO_DOCTO,
						"SERIE"=>$recibo->SERIE
					);
		$listado_recibos=$rc->getDetalleRecibo($filtro);
		foreach($listado_recibos as $keys =>$row){  
			$monto=$monto+$row['MONTO'];
		}
	  
		if (count($listado_recibos)>0){
		//	print_r($listado_recibos[0]);	
			$recib=$listado_recibos[0]; 
 			$cajero=UserAccess::getInstance()->getCaja(); 
			if ($fecha_requerimiento==""){
				$serie_doc=trim($cajero['ID_CAJA'])."-".date('Ymd');
			}else{
				$serie_doc=trim($cajero['ID_CAJA'])."-".str_replace("-","",$fecha_requerimiento);	
			}
			/*EN CASO DE INGRESAR EL RECIBO MANUALEMENTE*/
			if ($serie_recibo==""){
				$no_documento=$this->getNextNoDocument($serie_doc); 
			}else{
				$no_documento=$no_recibo;
				$serie_doc=$serie_recibo;
			}
 			$is_inicial=0;	
			if ($recib['TIPO_MOV']=="INI"){
				$is_inicial=1;	
			}
			MVFactura::getInstance()->setFecha($fecha_requerimiento);
			$mov=MVFactura::getInstance()->createMovCaja($no_documento,
														  $serie_doc,
														  $cn['no_contrato'],
														  $cn['serie'],
														  '', //ID_EMPRESA
														  $cajero['ID_CAJA'],
														  $cajero['id_usuario'],
														  $recib['ID_NIT'], 
														  RECIBO_CAJA, 
														  0,
														  0,
														  $monto,
														  $recib['TIPO_CAMBIO'],
														  0,
														  $is_inicial,
														  $_REQUEST['comentario'],
														  $this->_data['recibo']);
			
			$OBTMOV=new CTipoMovimiento($this->db_link); 
			/*OBTIENE LOS ITEM DE UN RECIBO*/
			$item=$this->getItemFromRecibo($recib['SERIE'],$recib['NO_DOCTO']);
			$mov_a=array("obj"=>array(),"forma_pago"=>array());
			$mov_a["obj"]=$mov;
			$mov_a["forma_pago"]=$rc->getReciboFormaPago($recib['SERIE'],$recib['NO_DOCTO']);
			
			
			foreach($item as $keys =>$row){   
				$TMO=$OBTMOV->getTIPO_MOV($row['TIPO_MOV']); 
				if (method_exists($this,$TMO['FUNCCION_ACCION'])){
					$r=$this->$TMO['FUNCCION_ACCION']($mov_a,
													   $row,
													   $_REQUEST['isNCF'],
													   $_REQUEST['rnc']); 	
				} 
			
			}  
	 		MVFactura::getInstance()->matarRecibos($mov->NO_DOCTO,
														 $mov->SERIE,
														 $mov->TIPO_DOC,
														 $recib['SERIE'],
														 $recib['NO_DOCTO'],
														 $recib['TIPO_DOC']); 		

		  	$this->session_restart();	 
			$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
			return $data; 
					
		}else{
			$data=array("error"=>false,"mensaje"=>'Error recibo invalido!'); 
			return $data;			
		}
	}
	
	/*GENERAR PAGO X*/
	public function doFacturarPago(){ 
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("caja","ModContable"); 
		SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 

		$this->_data=$_REQUEST;  
		
		$listado_nc=$this->getListCarritoNotaCredito();
  
		$montos=array();
		$forma_pago=array();
		/*VERIFICO SI EXISTE UNA NOTA DE CREDITO AFECTANDO EL DOCUMENTO*/
		if (count($listado_nc)<=0){ 
			if (!validateField($this->_data,"forma_pago_token")){
				$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
				return $data; 
				exit;	
			} 
			
			$forma_pago=$this->getItem($this->_data['forma_pago_token']); 
			if (!is_array($forma_pago)){
				$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
				return $data; 
				exit;
			} 
			
			$montos=$this->getMontoApagarYCobrar($this->_data['forma_pago_token']);				
					
		}else{ 
			$monto_nc=0;
			foreach($listado_nc as $key =>$row){
				$monto_nc=$monto_nc+$row->MONTO_TOTAL; 
			} 
			
			$listado=$this->getListCarritoRecibo();
			$monto_a_pagar=0;
			foreach($listado as $key =>$row){
				$monto_a_pagar=$monto_a_pagar+$row->MONTO_TOTAL;
			} 
			$monto_a_pagar=round($monto_a_pagar,2)-round($monto_nc,2);
			if ($monto_a_pagar<=0){
				$monto_a_pagar=0;	
			}			
			$montos=array(
					'monto_a_pagar'=>round($monto_a_pagar,2),
					'tasa_monto_a_cobrar'=>1,
					'monto_completo'=>$monto_a_pagar==0?1:0,
					'monto_acumulado'=>$monto_a_pagar,
					'monto_acumulado_tasa'=>1						
				);			
		} 
		
		/*VALIDO EN CASO QUE NECESITE NCF*/
		if (isset($this->_data['isNCF'])){
			if ($this->_data['isNCF']=="true"){
				if (isset($this->_data['rnc'])){
					if (trim($this->_data['rnc'])==""){
				//		$data=array("error"=>true,"mensaje"=>'Debe ingresar un RNC/CEDULA');
				//		return $data; 
				//		exit;
					}
				}
			}
		}else{
			$data=array("error"=>true,"mensaje"=>'Debe de indicar si contiene NCF');
			return $data; 
			exit;
		} 
  
		$OBTMOV=new CTipoMovimiento($this->db_link); 
		$listado_recibos=$this->getListCarritoRecibo();

		if (count($listado_recibos)<=0){
			if (!validateField($this->_data,'tipo_movimiento')){
				$data=array("error"=>true,"mensaje"=>'Debe de seleccionar un recibo!');
				return $data; 
			}
			 
			/*SI NO EXISTE RECIBO LO*/
			$tipo_movimiento=System::getInstance()->Decrypt($this->_data['tipo_movimiento']);
			 
			$reserva=json_decode(System::getInstance()->Decrypt($this->_data['reserva']));
	 		$cajero=UserAccess::getInstance()->getCaja();  
			$no_reserva=0;
			$id_reserva=0;
			if (isset($reserva->no_reserva) && isset($reserva->id_reserva)){
				$no_reserva=$reserva->no_reserva;
				$id_reserva=$reserva->id_reserva;			
			}		 
			if (validateField($this->_data,"id_nit")){ 
				$id_nit=System::getInstance()->Decrypt($this->_data['id_nit']);  			
			}		  
			if (!isset($cajero['ID_CAJA'])){
				$data=array("error"=>true,"mensaje"=>'El usuario no tiene caja asignada!');
				return $data; 
				exit;
			} 
			$serie_contrato="";
			$no_contrato=""	;	
			if (isset($this->_data['contrato'])){	 
				$contr=json_decode(System::getInstance()->Decrypt($this->_data['contrato']));
				if (isset($contr->serie_contrato) && isset($contr->no_contrato)){
					$serie_contrato=$contr->serie_contrato;
					$no_contrato=$contr->no_contrato;
				} 			
			}
			$TMO=$OBTMOV->getTIPO_MOV($tipo_movimiento); 
			$no_recibo_venta="";
			$reporte_venta="";			
			$asesor_nit="";
			$codigo_asesor="";
			/*VALIDO QUE EXISTA*/
			if (isset($this->_data['asesor']) && isset($this->_data['no_recibo_venta']) 
					&& isset($this->_data['reporte_venta'])){
				 $dase=json_decode(System::getInstance()->Decrypt($this->_data['asesor'])); 
				$no_recibo_venta=$this->_data['no_recibo_venta'];
				$reporte_venta=$this->_data['reporte_venta'];			
				if (isset($dase->id_comercial)){
					$asesor_nit=$dase->id_nit;
					$codigo_asesor=$dase->id_comercial;				
				}
			}

 	 		/*CREA EL RECIBO PARA SER FACTURADO*/
			$docto=MVFactura::getInstance()->doCreateDocument($id_nit,
														$em_id,
														$no_contrato,
														$serie_contrato,
														$tipo_movimiento,
														RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL
														$id_reserva,
														$no_reserva,
														$montos['monto_acumulado'],
														$montos['monto_acumulado_tasa'], //TIPO DE CAMBIO
														0, //descuento 
														$_REQUEST['observacion'],
														"GENERANDO DOCUMENTO ".$TMO['DESCRIPCION'],
														"", //MOTORISADO
														0,
														0,
														"",
														"",
														0, //CANTIDAD DE PRODUCTO/ SERVICIO
														0, //PRECIO DEL PRODUCTO/SERVICIO 
														$reporte_venta,
														$no_recibo_venta,
														$codigo_asesor,
														$asesor_nit);
 				 
			$cn=array(
				"recibo"=>1,
				"serie_docto"=>$docto->SERIE,
				"no_docto"=>$docto->NO_DOCTO		
			);
		
			$listado_r=$this->getListadoReciboSinFacturar($cn); 
			foreach($listado_r as $key =>$rcb){  
				$rc=new ObjectSQL();
				$rc->push($rcb);
				$this->doItemRecibo($rc); 
			}  
			$listado_recibos=$this->getListCarritoRecibo();	
			$montos=$this->getMontoApagarYCobrar($this->_data['forma_pago_token']);
			
 			if (count($listado_recibos)<=0){	
				$this->session_restart();							 
				$data=array("error"=>true,"mensaje"=>'Debe de seleccionar un documento para facturar!');
				return $data; 
				exit;	
			} 
		}  
 
		/*SI EL MONTO A FACTURAR ES MENOR*/			
		if (round($montos['monto_a_pagar'],2) > round($montos['monto_acumulado'],2)){
			$data=array("error"=>true,"mensaje"=>'El monto a pagar no puede ser menor que el valor de la cuota!');
			return $data; 
			exit; 
		} 
 
		/*VALIDO QUE LOS RECIBOS SEAN VALIDOS*/	
		foreach($listado_recibos as $key =>$recibo){  
			if ((!isset($recibo->SERIE)) && (!isset($recibo->NO_DOCTO))){
				$data=array("error"=>false,"mensaje"=>'No existen documentos para ser procesados!'); 
				return $data; 	
			}
		}  
		
		/*FECHA EN QUE SE QUIERE QUE SALGA LA TRANSACCION EN CASO DE NO PODER CERRAR LA CAJA*/
		$fecha_requerimiento="";	
		if (validateField($this->_data,"fecha_requerimiento_especial_xx")){
			$fecha_requerimiento=$this->_data['fecha_requerimiento_especial_xx'];	
			$this->setFecha($fecha_requerimiento);
			MVFactura::getInstance()->setFecha($fecha_requerimiento);
		}		
		 
		
		$needCF=false; //NECESITA COMPROBANTE FINAL?
		if (isset($this->_data['isCF'])){
			if ($this->_data['isCF']=="true"){
				$needCF=true;
			}
		} 
		 
		 
		/*CREO MI RECIBO*/
		//$_SESSION['test']=$this->doCrearReciboCaja(); 
	
		$inf=$this->doCrearReciboCaja();  
	 
		/*SI NO HAY ERROR ENTONCES CONTINUAR*/ 
		if (!$inf['error']){ 
			/*GENERO UN COMPROBANTE EN CASO DE SER NECESARIO*/
			$fact=$this->GenerarFacturaIfNeed($inf,$_REQUEST['isNCF'],$needCF);  
		 
			/*RECORRO LOS RECIBOS PARA MATARLO CON EL RECIBO DE CAJA*/
			foreach($listado_recibos as $key =>$recibo){ 
				/*OBTIENE LOS ITEM DE UN RECIBO*/
				$item=$this->getItemFromRecibo($recibo->SERIE,$recibo->NO_DOCTO); 
				
				foreach($item as $keys =>$row){    
					$TMO=$OBTMOV->getTIPO_MOV($row['TIPO_MOV']); 
					if (method_exists($this,$TMO['FUNCCION_ACCION'])){  
					  	$r=$this->$TMO['FUNCCION_ACCION']($inf,
														   $row, 
														   $_REQUEST['rnc'],
														   $fact); 	  
					} 
				
				}  
			 
  				$mov=$inf['obj']; 
			 	MVFactura::getInstance()->matarRecibos($mov->NO_DOCTO,
														 $mov->SERIE,
														 $mov->TIPO_DOC,
														 $recibo->SERIE,
														 $recibo->NO_DOCTO);  
														 		 
			}	  
			 
			
			if (count($listado_nc)>0){ 
				$iRec=(array)$inf['obj']; 
				foreach($listado_nc as $key =>$row){
					$obj=new ObjectSQL();
					$obj->ID_ESTATUS=38; //Uitlizado;
					$obj->NOTA_CD_SERIE=$row->SERIE; 
					$obj->NOTA_CD_NO_DOCTO=$row->NO_DOCTO; 
					$obj->setTable("movimiento_caja");
					$SQL=$obj->toSQL("update","where NO_DOCTO='".$iRec['NO_DOCTO']."' 
							and SERIE='".$iRec['SERIE']."'  and TIPO_DOC='".$iRec['TIPO_DOC']."'");
					mysql_query($SQL);	 
					/*PONGO LA NOTA DE CREDITO EN UTILIZADA*/
					$obj=new ObjectSQL();
					$obj->ID_ESTATUS=38; //Uitlizado; 
					$obj->setTable("movimiento_caja");
					$SQL=$obj->toSQL("update","where NO_CODIGO_BARRA='".$row->NO_CODIGO_BARRA."'");
					mysql_query($SQL);
					
					$obj=new ObjectSQL();
					$obj->ESTATUS=38; //Uitlizado; 
					$obj->setTable("movimiento_factura");
					$SQL=$obj->toSQL("update","where ID_MOV_FACT='".$row->ID_MOV_FACT."'");					
					mysql_query($SQL);
				} 
			}
			  
			
		  	$this->session_restart();	 
			$rdata=$this->getRecbioDataFromFactura($mov->NO_DOCTO,
														 $mov->SERIE,
														 $mov->TIPO_DOC);
			$data=array(
						"error"=>false,
						"mensaje"=>'Transacci??n realizada!',
						"recibo"=>System::getInstance()->Encrypt(json_encode($rdata))
					); 
			return $data; 
		}else{ 
			return $inf;	
		}
		
	}

	public function doImportRecibo($tipo_doc="RM",$serie_docto="",$no_docto=""){ 
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		$this->_data=$_REQUEST;   
		 
		if (!validateField($this->_data,"forma_pago_token")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;	
		}
	
		$forma_pago=$this->getItem($this->_data['forma_pago_token']);

 		if (!is_array($forma_pago)){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;
		}
				
		/*VALIDO EN CASO QUE NECESITE NCF*/
		if (isset($this->_data['isNCF'])){
			if ($this->_data['isNCF']=="true"){
				if (isset($this->_data['rnc'])){
					if (trim($this->_data['rnc'])==""){
						$data=array("error"=>true,"mensaje"=>'Debe ingresar un RNC/CEDULA');
						return $data; 
						exit;
					}
				}
			}
		}else{
			$data=array("error"=>true,"mensaje"=>'Debe de indicar si contiene NCF');
			return $data; 
			exit;
		} 
	 
		$_contratos=new Contratos($this->db_link);   
		$cajero=UserAccess::getInstance()->getCaja(); 
		$fecha_requerimiento=$this->_data['fecha_requerimiento'];
		 
		if ($serie_docto==""){
			$serie_doc=$tipo_doc."-".str_replace("-","",$fecha_requerimiento); 		
			$no_documento=$this->getNextNoDocument($serie_doc); 
		}else{
			$serie_doc=$serie_docto;
			$no_documento=$no_docto;
		}
		 
		if (!isset($cajero['ID_CAJA'])){
			$data=array("error"=>true,"mensaje"=>'El usuario no tiene caja asignada!');
			return $data; 
			exit;
		}
		
		if (validateField($this->_data,"id_nit")){ 
			$id_nit=System::getInstance()->Decrypt($this->_data['id_nit']);  			
		}
		
		$serie_contrato="";
		$no_contrato=""	;		 
		if (trim($id_nit)==""){
			$data=array("error"=>true,"mensaje"=>'Error no se puede procesar el pago, el NIT del cliente no existe!');
			return $data;
			exit;
		}
		  
		if ($this->checkExistRecibo($cajero['ID_CAJA'],$serie_doc,$no_documento)>0){
			$data=array("error"=>true,"mensaje"=>'Error no se puede volver a procesar el pago, la serie o el documento existe!'); 
			return $data;
			exit;
		}   
		if (!validateField($this->_data,'tipo_movimiento')){
			$data=array("error"=>true,"mensaje"=>'Debe de seleccionar un recibo!');
			return $data; 
		}
		 
		$tipo_movimiento=$this->_data['tipo_movimiento'];
		
		 
		$no_contrato=$this->_data['no_contrato'];
		$serie_contrato=$this->_data['serie_contrato'];
		
  
		$MONTO_PAGO_CAJA=0;  
		$forma_pago_array=array();
			
		/*VALIDANDO LA FORMA DE PAGO*/
		foreach($forma_pago as $key =>$val){
			$fpago=new ObjectSQL();
			$fpago->setTable('forma_pago_caja');  
			/*SI LA FORMA DE PAGO ES 
			2=> Tarjeta de credito*/ 
			if (($val['forma_pago']=="TC") || ($val['forma_pago']=="CK") || ($val['forma_pago']=="DP")){    
				if (!(validateField($val,"autorizacion"))){  
					$data=array("error"=>true,"mensaje"=>'La informaci??n proporcionada no esta completa!');  
					return $data; 
					exit;	
				} 
			} 	
			$fpago->FORMA_PAGO=$val['forma_pago'];
			$fpago->AUTORIZACION=$val['autorizacion'];  
			$fpago->TIPO_CAMBIO=$val['tipo_cambio'];
			$fpago->MONTO=$val['monto_a_pagar'];
			$fpago->FECHA=$fecha_requerimiento;
			$fpago->TIPO_DOC=$tipo_doc;
			$fpago->NO_DOCTO=$no_documento;
			$fpago->SERIE=$serie_doc;
			$fpago->EM_ID=$EM_ID;
			$fpago->ID_CAJA=$cajero['ID_CAJA'];
			$fpago->ID_NIT=$id_nit;
			$fpago->RC_NO_DOCTO=$no_documento;
			$fpago->RC_SERIE=$serie_doc;	 
 			$SQL=$fpago->toSQL('insert'); 
 	   	  	mysql_query($SQL);	 
			$tipo_cambio=$val['tipo_cambio'];
			$MONTO_PAGO_CAJA=$MONTO_PAGO_CAJA+$fpago->MONTO;  
			array_push($forma_pago_array,$fpago);
		/*	
			SysLog::getInstance()->Log($id_nit, 
									 $serie_contrato,
									 $no_contrato,
									 $no_reserva,
									 $id_reserva,
									 "TRANSACCION FORMA PAGO",
									 json_encode($fpago),
									 'FORMA_PAGO');	*/
		}  
			 
		$mov = new ObjectSQL();
		$mov->NO_DOCTO=$no_documento;
		$mov->EM_ID=$EM_ID;  
		$mov->ID_CAJA=$cajero['ID_CAJA'];  
		$mov->SERIE=$serie_doc;
		$mov->id_usuario=$cajero['id_usuario'];
		$mov->ID_NIT=$id_nit;
		$mov->TIPO_DOC=RECIBO_VIRTUAL;  
 		$mov->FECHA_DOC=$fecha_requerimiento;
		$mov->FECHA=$fecha_requerimiento;  
		$mov->ID_RESERVA=$id_reserva; 
		$mov->NO_RESERVA=$no_reserva;		 
		$mov->MONTO=$MONTO_PAGO_CAJA;
		$mov->TIPO_MONEDA=$tipo_cambio>1?'DOLARES':'LOCAL';
		$mov->TIPO_CAMBIO=$tipo_cambio; 
		$mov->NO_CONTRATO=$no_contrato;
		$mov->SERIE_CONTRATO=$serie_contrato;
	 	$mov->REP_VENTA=$this->_data['reporte_venta'];
		$mov->DESCUENTO=0;
		$mov->INICIAL="N"; 
		if ($tmovi->TIPO_MOV=="INI"){
			$mov->INICIAL="S"; 
		} 	  		
 		$mov->OBSERVACIONES=$this->_data['observacion']; 
		$mov->setTable('movimiento_caja');   
		$SQL=$mov->toSQL('insert');	 
	   	mysql_query($SQL); 
	 	
		foreach($tipo_movimiento as $key =>$row){ 
			MVFactura::getInstance()->setFecha($fecha_requerimiento);  
			MVFactura::getInstance()->movFactura($mov->NO_DOCTO,
												  $mov->SERIE,
												  $no_contrato,
												  $serie_contrato,								  
												  "",  
												  $id_nit,
												  $row->tipo_mov, 
												  0,
												  0,
												  $row->valor_cuota,
												  $row->tipo_cambio, 
												  36, //ESTATUS
												  '',
												  '',
												  $this->_data['solicitud'],
												  '',
												  '',
												  '',
												  '',
												  '',
												  '',
												  '',
												  '');	 	 
		  
		} 

		/*
		SysLog::getInstance()->Log($id_nit, 
									 '',
									 '',
									 '',
									 '',
									 "TRANSACCION RECIBO VIRTUAL CAJA",
									 json_encode($mov),
									 'MOVIMIENTO_CAJA',
									 $mov->SERIE,
									 $mov->NO_DOCTO
									 );	*/
								
   	 
 		return array("error"=>false,"mensaje"=>'',"obj"=>$mov,"forma_pago"=>$forma_pago_array);
	}
	/*IMPORTAR PAGOS AUTOMATICOS*/
	public function doImportPago(){ 
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("caja","ModContable"); 
		SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 

		$this->_data=$_REQUEST;    
		
		if (!validateField($this->_data,"forma_pago_token")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;	
		}
		
		$fecha_requerimiento=$this->_data['fecha_requerimiento_especial_xx'];
		
 		$forma_pago=$this->getItem($this->_data['forma_pago_token']);  	
	
 		if (!is_array($forma_pago)){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;
		} 	
		 
		/*VALIDO EN CASO QUE NECESITE NCF*/
		if (isset($this->_data['isNCF'])){
			if ($this->_data['isNCF']=="true"){
				if (isset($this->_data['rnc'])){
					if (trim($this->_data['rnc'])==""){
						$data=array("error"=>true,"mensaje"=>'Debe ingresar un RNC/CEDULA');
						return $data; 
						exit;
					}
				}
			}
		}else{
			$data=array("error"=>true,"mensaje"=>'Debe de indicar si contiene NCF');
			return $data; 
			exit;
		}  
		$montos=$this->getMontoApagarYCobrar($this->_data['forma_pago_token']);
		 
		$OBTMOV=new CTipoMovimiento($this->db_link); 
		$listado_recibos=$this->getListCarritoRecibo(); 
		

		/*CREO MI RECIBO*/
		$inf=$this->doCrearReciboCaja(); 
 
		 
		/*SI NO HAY ERROR ENTONCES CONTINUAR*/ 
		if (!$inf['error']){ 
			$fact=$this->GenerarFacturaIfNeed($inf,$_REQUEST['isNCF']);   
			 
			/*RECORRO LOS RECIBOS PARA MATARLO CON EL RECIBO DE CAJA*/
			foreach($listado_recibos as $key =>$recibo){ 
				/*OBTIENE LOS ITEM DE UN RECIBO*/
				$item=$this->getItemFromRecibo($recibo->SERIE,$recibo->NO_DOCTO); 
				
				foreach($item as $keys =>$row){   
					
					$TMO=$OBTMOV->getTIPO_MOV($row['TIPO_MOV']); 
					if (method_exists($this,$TMO['FUNCCION_ACCION'])){  
					  	$r=$this->$TMO['FUNCCION_ACCION']($inf,
														   $row, 
														   $_REQUEST['rnc'],
														   $fact); 	  
					} 
				
				}  
			 
  				$mov=$inf['obj'];
				
			 	MVFactura::getInstance()->matarRecibos($mov->NO_DOCTO,
														 $mov->SERIE,
														 $mov->TIPO_DOC,
														 $recibo->SERIE,
														 $recibo->NO_DOCTO); 
														 		 
			}	  
			
		  	$this->session_restart();	 
			$rdata=$this->getRecbioDataFromFactura($mov->NO_DOCTO,
														 $mov->SERIE,
														 $mov->TIPO_DOC);
			$data=array(
						"error"=>false,
						"mensaje"=>'Transacci??n realizada!',
						"recibo"=>System::getInstance()->Encrypt(json_encode($rdata))
					); 
			return $data; 
		}else{ 
			return $inf;	
		}
		
	}	
	public function getRecbioDataFromFactura($NO_DOCTO,$SERIE,$TIPO_DOC){
		$SQL="SELECT 
			*,
			caja.DESCRIPCION_CAJA AS CAJA ,
			`tipo_documento`.`DOCUMENTO`,
			(SELECT GROUP_CONCAT(DISTINCT tipo_movimiento.DESCRIPCION)  FROM `movimiento_factura` AS MF 
		INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=MF.TIPO_MOV)
		 WHERE MF.CAJA_SERIE=movimiento_caja.SERIE AND MF.CAJA_NO_DOCTO=movimiento_caja.NO_DOCTO) AS TMOVIMIENTO,
		 (SELECT 
				CONCAT(sp.`primer_nombre`,' ',sp.`segundo_nombre`,
				' ',sp.`primer_apellido`,' ',sp.segundo_apellido) FROM sys_personas AS sp 
				WHERE sp.id_nit=movimiento_caja.ANULADO_POR_ID_NIT) AS anulado_por 
		 
			FROM `movimiento_caja` 
		INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
		INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
		WHERE movimiento_caja.NO_DOCTO='".$NO_DOCTO."'
			AND movimiento_caja.SERIE='".$SERIE."'
			AND movimiento_caja.TIPO_DOC='".$TIPO_DOC."'   
			  AND  movimiento_caja.TIPO_DOC IN ('".RECIBO_CAJA."','".NOTA_CREDITO."') ORDER BY movimiento_caja.FECHA ";
		 $rs=mysql_query($SQL);		
		 $row=mysql_fetch_assoc($rs);
		 return $row;
	}
	/*GENERAR RECIBO PARA PRE-FACTURAR*/
	public function doPreFacturar(){ 
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("caja","ModContable"); 
		SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 

		$this->_data=$_REQUEST;    

		if (!validateField($this->_data,"forma_pago_token")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;	
		}
 		$forma_pago=$this->getItem($this->_data['forma_pago_token']);  	

 		if (!is_array($forma_pago)){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;
		} 	
		 
		/*VALIDO EN CASO QUE NECESITE NCF*/
		if (isset($this->_data['isNCF'])){
			if ($this->_data['isNCF']=="true"){
				if (isset($this->_data['rnc'])){
					if (trim($this->_data['rnc'])==""){
						$data=array("error"=>true,"mensaje"=>'Debe ingresar un RNC/CEDULA');
						return $data; 
						exit;
					}
				}
			}
		}else{
			$data=array("error"=>true,"mensaje"=>'Debe de indicar si contiene NCF');
			return $data; 
			exit;
		} 

		$montos=$this->getMontoApagarYCobrar($this->_data['forma_pago_token']);
		
		$OBTMOV=new CTipoMovimiento($this->db_link); 
		$listado_recibos=$this->getListCarritoRecibo();

		if (!validateField($this->_data,'tipo_movimiento')){
			$data=array("error"=>true,"mensaje"=>'Debe de seleccionar un recibo!');
			return $data; 
		}
		if (!validateField($this->_data,'asesor')){
			$data=array("error"=>true,"mensaje"=>'Debe de seleccionar un asesor!');
			return $data; 
		}		
		$tipo_movimiento=System::getInstance()->Decrypt($this->_data['tipo_movimiento']);
		/*CREO MI RECIBO*/
		$inf=$this->doGeneraRecibo(); 
		/*SI NO HAY ERROR ENTONCES CONTINUAR*/ 
		if (!$inf['error']){ 
		  	$this->session_restart();	 
			$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
			return $data; 
		}else{ 
			return $inf;	
		}
		
	}

	public function doPagoServiciosA($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		  
 
		$contabilidad= new ModContable($this->db_link);
		 
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		); 
		
		$MONTO_TOTAL_TRANS=$inf['obj']->MONTO;
		/*VALIDO SI ESTA TRANSACCION GENERA FACTURA
			EN CASO DE GENERAR FACTURA REGISTRO EL MOVIMIENTO CONTABLE
			EN LA EMPRESA FISCAL
		*/		
		if ($fact['validFF'] || $fact['validCF']){ 
			$catalogo=$contabilidad->getCatalogo("F"); 
			$ID_EMPRESA="SJMF";
			$documento=$fact['factura']->SERIE.sprintf("%08d",$fact['factura']->NO_DOCTO);  
			/*ACTUALIZA UN DOCUMENTO PARA SER USADO COMO FISCAL*/
			$this->doReciboFiscal($inf['obj']->SERIE,$inf['obj']->NO_DOCTO); 
			/*CAJA FISCAL*/	  
		}else{
			$catalogo=$contabilidad->getCatalogo("NF");
			$documento=$inf['obj']->SERIE."-".$inf['obj']->NO_DOCTO; 
			$ID_EMPRESA="SJMN"; 
			/*CAJA NO FISCAL*/ 	
		}
		 
	
		$mi= new ObjectSQL();
		$mi->setTable("sp_mov_inventarios");
		$mi->fecha="curdate()";
		$mi->id_tipo_mov='SI';
		$mi->id_producto=$data['ID_PRODUCTO'];
		$mi->idbodega=1;
		$mi->cantidad=$data['CANTIDAD'];
		$mi->costo=$data['PRECIO'];	
		$mi->registrado_por=UserAccess::getInstance()->getIDNIT();								
		$SQL=$mi->toSQL("insert");
		mysql_query($SQL); 
	//	print_r($data);

		$sp= new ObjectSQL();
		$sp->setTable("sp_inventario");
		$sp->existencia="(existencia-".$mi->cantidad.")"; 
		$SQL=$sp->toSQL("update"," where id_producto='".$data['ID_PRODUCTO']."'");	
		mysql_query($SQL); 			
	
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	}
	
	public function doPagoInhumacion($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		  
 
		$contabilidad= new ModContable($this->db_link);
		 
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		); 
		
		$MONTO_TOTAL_TRANS=$inf['obj']->MONTO;
		/*VALIDO SI ESTA TRANSACCION GENERA FACTURA
			EN CASO DE GENERAR FACTURA REGISTRO EL MOVIMIENTO CONTABLE
			EN LA EMPRESA FISCAL
		*/		
		if ($fact['validFF'] || $fact['validCF']){ 
			$catalogo=$contabilidad->getCatalogo("F"); 
			$ID_EMPRESA="SJMF";
			$documento=$fact['factura']->SERIE.sprintf("%08d",$fact['factura']->NO_DOCTO);  
			/*ACTUALIZA UN DOCUMENTO PARA SER USADO COMO FISCAL*/
			$this->doReciboFiscal($inf['obj']->SERIE,$inf['obj']->NO_DOCTO); 
			/*CAJA FISCAL*/	  
		}else{
			$catalogo=$contabilidad->getCatalogo("NF");
			$documento=$inf['obj']->SERIE."-".$inf['obj']->NO_DOCTO; 
			$ID_EMPRESA="SJMN"; 
			/*CAJA NO FISCAL*/ 	
		}
		
		$sp= new ObjectSQL();
		$sp->setTable("sp_servicios_prestados");
		$sp->estatus=39;
		$SQL=$sp->toSQL("update"," where no_servicio='".$data['NO_SERVICIO_PRESTADO']."'");
		mysql_query($SQL);
	/*	$mi= new ObjectSQL();
		$mi->setTable("sp_mov_inventarios");
		$mi->fecha="curdate()";
		$mi->id_tipo_mov="curdate()";
		$mi->id_producto="curdate()";
		$mi->idbodega="curdate()";
		$mi->cantidad="curdate()";
		$mi->costo="curdate()";										
		$SQL=$mi->toSQL("insert");
	*/ 
		 
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	}
	
	public function doAbonoReserva($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		  
 		
		$contabilidad= new ModContable($this->db_link);
		 
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		); 
		$MONTO_TOTAL_TRANS=$inf['obj']->MONTO;
		/*VALIDO SI ESTA TRANSACCION GENERA FACTURA
			EN CASO DE GENERAR FACTURA REGISTRO EL MOVIMIENTO CONTABLE
			EN LA EMPRESA FISCAL
		*/		
		if ($fact['validFF'] || $fact['validCF']){ 
			$catalogo=$contabilidad->getCatalogo("F"); 
			$ID_EMPRESA="SJMF";
			$documento=$fact['factura']->SERIE.sprintf("%08d",$fact['factura']->NO_DOCTO);  
			/*ACTUALIZA UN DOCUMENTO PARA SER USADO COMO FISCAL*/
			$this->doReciboFiscal($inf['obj']->SERIE,$inf['obj']->NO_DOCTO); 
			/*CAJA FISCAL*/
			//11110201	Caja	1,000.00	
			$contabilidad->registrarAsientoC($info_log,
											 $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA FISCAL
											  "DEBITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="ABONO DE CLIENTE ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);
			
 				  
			//21130401	Anticipo recibidos de clientes		1,000.00 								  
			$contabilidad->registrarAsientoC($info_log,
											 $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['ANTICIPO_RECIBIDOS_CLIENTES']['cuenta'], //Anticipo recibidos de clientes
											  "CREDITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="ABONO DE CLIENTE ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);												  
	  
		}else{
			$catalogo=$contabilidad->getCatalogo("NF");
			$documento=$inf['obj']->SERIE."-".$inf['obj']->NO_DOCTO; 
			$ID_EMPRESA="SJMN"; 
			/*CAJA NO FISCAL*/ 	
			$contabilidad->registrarAsientoC($info_log,
											 $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA NF
											  "DEBITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="ABONO DE CLIENTE ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS); 
											  
																			  
			$contabilidad->registrarAsientoC($info_log,
											 $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											   $catalogo['ANTICIPO_RECIBIDOS_CLIENTES']['cuenta'], //Anticipo recibidos de clientes
											  "CREDITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="ABONO DE CLIENTE ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);					
		}
		 
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	}
	
	public function doCancelacionTotal($inf,$data,$NO_RNC_CED,$fact){ 
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		
		$contabilidad= new ModContable($this->db_link); 
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		); 
		//$MONTO_TOTAL_TRANS=$inf['obj']->MONTO; 
		$obj=$inf['obj'];		
		$obj->NO_CONTRATO=$contr->no_contrato;
		$obj->SERIE_CONTRATO=$contr->serie_contrato;  
		
		$ct= new ObjectSQL();
		$ct->estatus=20;
		$ct->setTable("contratos");
		$SQL=$ct->toSQL("update"," where no_contrato='".$obj->NO_CONTRATO."' and 
				serie_contrato='".$obj->SERIE_CONTRATO."' ");
		mysql_query($SQL);
		
		
		$this->procesarMovimientoContrato($obj,$data);  	 
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	}	
	
	/*EFECTUA LA TRANSACCION DE UN INICIAL*/
	public function doInicial($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		  
 
		$contabilidad= new ModContable($this->db_link);
		 
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		); 
		
		$MONTO_TOTAL_TRANS=$inf['obj']->MONTO;
		/*VALIDO SI ESTA TRANSACCION GENERA FACTURA
			EN CASO DE GENERAR FACTURA REGISTRO EL MOVIMIENTO CONTABLE
			EN LA EMPRESA FISCAL
		*/		
		if ($fact['validFF'] || $fact['validCF']){ 
			$catalogo=$contabilidad->getCatalogo("F");
			
			$ID_EMPRESA="SJMF";
			$documento=$fact['factura']->SERIE.sprintf("%08d",$fact['factura']->NO_DOCTO);  
			/*ACTUALIZA UN DOCUMENTO PARA SER USADO COMO FISCAL*/
			$this->doReciboFiscal($inf['obj']->SERIE,$inf['obj']->NO_DOCTO);  
			/*CAJA FISCAL*/
			//11110201	Caja	1,000.00	
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA FISCAL
											  "DEBITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);
											  
			//21130401	Anticipo recibidos de clientes		1,000.00 								  
			$contabilidad->registrarAsientoC($info_log,
											  $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['ANTICIPO_RECIBIDOS_CLIENTES']['cuenta'],
											  "CREDITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);	
	 											  
		
	  
		}else{
			$catalogo=$contabilidad->getCatalogo("NF");
			$documento=$inf['obj']->SERIE."-".$inf['obj']->NO_DOCTO; 
			$ID_EMPRESA="SJMN"; 

			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA NF 
											  "DEBITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS); 
											  
																			  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['ANTICIPO_RECIBIDOS_CLIENTES']['cuenta'],
											  "CREDITO",
											  $inf['obj']->MONTO,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);			
						
		}
		 
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	}

	public function doAbonoASaldo($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
 	 	$cobro= new Cobros($this->db_link);   
		$_contratos=new Contratos($this->db_link);  		  
		 
		$sbc=$cobro->getSolicitud($data['SERIE_CONTRATO'],$data['NO_CONTRATO'],"ABSAL",34);
		$ret=$_contratos->calcularAbonoACapital($data['SERIE_CONTRATO'],
												$data['NO_CONTRATO'],
												$sbc[0]['monto_abonar'],
												$sbc[0]['cuotas']); 
														
		$d_contrato=$_contratos->getBasicInfoContrato($data['SERIE_CONTRATO'],$data['NO_CONTRATO']);	
	
		$monto_abonar=$inf['obj']->MONTO;
		
		$newcon_data=$sbc[0];
		
		unset($d_contrato->asesor);							
		unset($d_contrato->director);	
		unset($d_contrato->gerente);
		unset($d_contrato->cuota);
	 
		
		/*ALMACENO LA FOTO DEL CONTRATO ANTES DE REALIZARLE LOS CAMBIOS FINANCIEROS*/
		$cf= new ObjectSQL();
		$cf->push($d_contrato);
		$cf->valor_reconocimiento=$monto_abonar;
		$cf->setTable("cambios_financieros");
		$SQL=$cf->toSQL("insert");	
		mysql_query($SQL);	
		$obj=$inf['obj'];
 		$id_cambios_financieros=mysql_insert_id();	
		 
		/*ACTUALIZO EL CONTRATO CON LOS CAMBIOS FINANCIEROS*/
		$ncontrato= new ObjectSQL(); 
		$ncontrato->interes=$ret['interes']*$ret['plazo'];  
		$ncontrato->capital_pagado=0;
		$ncontrato->intereses_pagados=0;
		$ncontrato->capital_abonado=$d_contrato->capital_abonado+$monto_abonar;
		$ncontrato->setTable("contratos");
		$SQL=$ncontrato->toSQL("update"," where serie_contrato='".$d_contrato->serie_contrato."' 
									and no_contrato='".$d_contrato->no_contrato."'");
	 								
		mysql_query($SQL);			
		
		
		
		$this->procesarMovimientoContrato($obj,$data); 
		
		//print_r($ret); 
		/*ACTUALIZO EL CONTRATO CON LOS CAMBIOS FINANCIEROS*/
		$ncontrato= new ObjectSQL();
		$ncontrato->push($d_contrato); 
		$ncontrato->capital_abonado=$d_contrato->capital_abonado+$monto_abonar;
		$ncontrato->setTable("contratos");
		$SQL=$ncontrato->toSQL("update"," where serie_contrato='".$d_contrato->serie_contrato."' 
									and no_contrato='".$d_contrato->no_contrato."'");
		mysql_query($SQL);							
 		/*ACTUALIZO EL ESTATUS DE LA SOLICITUD*/
		$sgestion= new ObjectSQL();
		$sgestion->estatus=35; //SOLICITUD PROCESADA
		$sgestion->setTable("solicitud_gestion");
		$SQL=$sgestion->toSQL("update"," where id_planilla_gestion='".$newcon_data['id_planilla_gestion']."' 
												and estatus=34 ");	
		mysql_query($SQL);
		
		/*ACTUALIZO LOS MOVIMIENTOS PAGADOS ANTES DE LOS CAMBIOS EN EL CONTRATO*/
		
		$movc= new ObjectSQL();
		$movc->ID_CAMBIOS_FINANCIEROS=$id_cambios_financieros;
		$movc->setTable("movimiento_caja");
		$SQL=$movc->toSQL("update"," where serie_contrato='".$d_contrato->serie_contrato."' 
									and no_contrato='".$d_contrato->no_contrato."' and ID_CAMBIOS_FINANCIEROS=0 ");	
		mysql_query($SQL);	
		
		
			
		/* 
			ES IGUAL AL INTERES ANTES DE APLICAR EL NUEVO INTERES MENOS EL INTERES PAGADO EN CUOTAS MENOS EL INTERES 
			DEL NUEVO FINANCIAMIENTO
		*/
		if ($d_contrato->interes>0){
			$interes_diferido_financiamiento=($d_contrato->interes-$ret['interes_pagado'])-$ncontrato->interes;
		}else{
			$interes_diferido_financiamiento=0;
		}
		$contr=$d_contrato;
		$contrato=$contr->serie_contrato." ".$contr->no_contrato;			
 
 		SysLog::getInstance()->Log($contr->id_nit_cliente, 
								 $contr->serie_contrato,
								 $contr->no_contrato,
								 '',
								 '',
								 "ABONO A SALDO ".$contr->serie_contrato." ".$contr->no_contrato,
								 json_encode($contr),
								 'ABONO_A_SALDO');		
 
		$contabilidad= new ModContable($this->db_link);
		 
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		); 
		
		$MONTO_TOTAL_TRANS=$monto_abonar+$interes_diferido_financiamiento;
		
		/*VALIDO SI ESTA TRANSACCION GENERA FACTURA
			EN CASO DE GENERAR FACTURA REGISTRO EL MOVIMIENTO CONTABLE
			EN LA EMPRESA FISCAL
		*/		
		if ($fact['validFF'] || $fact['validCF']){ 
			$catalogo=$contabilidad->getCatalogo("F");
			
			$ID_EMPRESA="SJMF";
			$documento=$fact['factura']->SERIE.sprintf("%08d",$fact['factura']->NO_DOCTO);  
			/*ACTUALIZA UN DOCUMENTO PARA SER USADO COMO FISCAL*/
			$this->doReciboFiscal($inf['obj']->SERIE,$inf['obj']->NO_DOCTO); 
 			 
			/*CAJA FISCAL*/
			//11110201	Caja	1,000.00	
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA FISCAL
											  "DEBITO",
											  $monto_abonar,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);
											  
			//21130401	Anticipo recibidos de clientes		1,000.00 								  
			$contabilidad->registrarAsientoC($info_log,
											  $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['INGRESO_DIFERIDO_CAPITAL']['cuenta'],
											  "CREDITO",
											  $monto_abonar,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);	
	 											  
	  
		}else{
			$catalogo=$contabilidad->getCatalogo("NF");
			$documento=$inf['obj']->SERIE."-".$inf['obj']->NO_DOCTO; 
			$ID_EMPRESA="SJMN"; 
			/*CAJA NO FISCAL*/ 	
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA NF 
											  "DEBITO",
											  $monto_abonar,
											  $documento,
											  "ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS); 
											  
																			  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['MODULO_CLIENTES']['cuenta'],
											  "CREDITO",
											  $monto_abonar,
											  $documento,
											  "ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);	

			$contabilidad->registrarAsientoC($info_log,
											 $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  3,
											  $catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['cuenta'],
											  "DEBITO",
											  $interes_diferido_financiamiento,
											  $documento,
											  "ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);	
											  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  4,
											  $catalogo['MODULO_CLIENTE_INTERESES']['cuenta'],
											  "CREDITO",
											  $interes_diferido_financiamiento,
											  $documento,
											  "ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);												  											  		
						
		}
		 
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	}
	
	public function actualizarInteresCambiosFinancieros($serie_contrato,$no_contrato,$id_cambios_financieros){
		
		/*ACTUALIZO LOS MOVIMIENTOS PAGADOS ANTES DE LOS CAMBIOS EN EL CONTRATO*/
		$movc= new ObjectSQL();
		$movc->ID_CAMBIOS_FINANCIEROS=$id_cambios_financieros;
		$movc->setTable("movimiento_caja");
		$SQL=$movc->toSQL("update"," where serie_contrato='".$serie_contrato."' 
									and no_contrato='".$no_contrato."' and ID_CAMBIOS_FINANCIEROS=0 ");	
		mysql_query($SQL);	
				
		$SQL="SELECT
				movimiento_contrato.*
			FROM `movimiento_caja`
			INNER JOIN `movimiento_contrato` ON (movimiento_contrato.`CAJA_SERIE`=movimiento_caja.`SERIE`
			AND movimiento_contrato.`NO_DOCTO`=`movimiento_caja`.`NO_DOCTO`)
			WHERE (movimiento_caja.SERIE_DOC_ANUL IS NULL AND movimiento_caja.NO_DOC_ANUL IS NULL)
			AND movimiento_caja.`NO_CONTRATO`='".$no_contrato."' AND
			movimiento_caja.`SERIE_CONTRATO`='".$serie_contrato."'  AND
			movimiento_caja.ANULADO='N'
			AND movimiento_contrato.TIPO_MOV IN ('ABO','CUOTA','CAPITAL','INI','NC','ND','CTI')
			and movimiento_contrato.ID_CAMBIOS_FINANCIEROS=0  ";
	 
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$SQL="UPDATE 
						movimiento_contrato 
					SET 
						ID_CAMBIOS_FINANCIEROS='".$id_cambios_financieros."' 
					WHERE
					ID_MOV_C='".$row['ID_MOV_C']."'"; 
			mysql_query($SQL);
		
		}	
		
	}
	 	
	public function doAbonoCapital($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
 	 	$cobro= new Cobros($this->db_link);   
		$_contratos=new Contratos($this->db_link);  		  
		 
		$sbc=$cobro->getSlcAbonoCapital($data['SERIE_CONTRATO'],$data['NO_CONTRATO'],34);	 

		$ret=$_contratos->calcularAbonoACapital($data['SERIE_CONTRATO'],
												$data['NO_CONTRATO'],
												$inf['obj']->MONTO,
												$sbc[0]['cuotas']); 
 										
		$d_contrato=$_contratos->getBasicInfoContrato($data['SERIE_CONTRATO'],$data['NO_CONTRATO']);	

		$monto_abonar=$inf['obj']->MONTO;
		
		$newcon_data=$sbc[0];
		
		unset($d_contrato->asesor);							
		unset($d_contrato->director);	
		unset($d_contrato->gerente);
		unset($d_contrato->cuota);
	 
 		/*ALMACENO LA FOTO DEL CONTRATO ANTES DE REALIZARLE LOS CAMBIOS FINANCIEROS*/
		$cf= new ObjectSQL();
		$cf->push($d_contrato);
		$cf->valor_reconocimiento=$monto_abonar;
		$cf->setTable("cambios_financieros");
		$SQL=$cf->toSQL("insert");	 
		mysql_query($SQL);	 
		$id_cambios_financieros=mysql_insert_id();	
		
		//print_r($ret); 
		/*ACTUALIZO EL CONTRATO CON LOS CAMBIOS FINANCIEROS*/
		$ncontrato= new ObjectSQL();
		$ncontrato->push($d_contrato);
	//	$ncontrato->precio_neto=$newcon_data['precio_neto']; 
		$ncontrato->interes=$ret['interes']*$ret['plazo'];
		$ncontrato->cuotas=$ret['plazo'];
		$ncontrato->valor_cuota=$ret['monto_cuota'];
		$ncontrato->capital_pagado=0;
		$ncontrato->intereses_pagados=0;
		$ncontrato->capital_abonado=$d_contrato->capital_abonado+$monto_abonar;
		$ncontrato->setTable("contratos");
		$SQL=$ncontrato->toSQL("update"," where serie_contrato='".$d_contrato->serie_contrato."' 
									and no_contrato='".$d_contrato->no_contrato."'");
	 								
		mysql_query($SQL);		
					
 		/*ACTUALIZO EL ESTATUS DE LA SOLICITUD*/
		$sgestion= new ObjectSQL();
		$sgestion->estatus=35;
		$sgestion->setTable("solicitud_gestion");
		$SQL=$sgestion->toSQL("update"," where id_planilla_gestion='".$newcon_data['id_planilla_gestion']."' 
												and estatus=34 ");	
		mysql_query($SQL);
		 
		 
		$this->actualizarInteresCambiosFinancieros($d_contrato->serie_contrato,
													$d_contrato->no_contrato,
													$id_cambios_financieros);
		 	 
		/* 
			ES IGUAL AL INTERES ANTES DE APLICAR EL NUEVO INTERES MENOS EL INTERES PAGADO EN CUOTAS MENOS EL INTERES 
			DEL NUEVO FINANCIAMIENTO
		*/
		$interes_diferido_financiamiento=($d_contrato->interes-$ret['interes_pagado'])-$ncontrato->interes;

		$contrato=$d_contrato->serie_contrato." ".$d_contrato->no_contrato;			
 
		$contabilidad= new ModContable($this->db_link);
		 
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		); 
		
		$MONTO_TOTAL_TRANS=$monto_abonar+$interes_diferido_financiamiento;
		
		/*VALIDO SI ESTA TRANSACCION GENERA FACTURA
			EN CASO DE GENERAR FACTURA REGISTRO EL MOVIMIENTO CONTABLE
			EN LA EMPRESA FISCAL
		*/		
		if ($fact['validFF'] || $fact['validCF']){ 
			$catalogo=$contabilidad->getCatalogo("F");
			
			$ID_EMPRESA="SJMF";
			$documento=$fact['factura']->SERIE.sprintf("%08d",$fact['factura']->NO_DOCTO);  
			/*ACTUALIZA UN DOCUMENTO PARA SER USADO COMO FISCAL*/
			$this->doReciboFiscal($inf['obj']->SERIE,$inf['obj']->NO_DOCTO); 
 			 
			/*CAJA FISCAL*/
			//11110201	Caja	1,000.00	
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA FISCAL
											  "DEBITO",
											  $monto_abonar,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);
											  
			//21130401	Anticipo recibidos de clientes		1,000.00 								  
			$contabilidad->registrarAsientoC($info_log,
											  $ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['INGRESO_DIFERIDO_CAPITAL']['cuenta'],
											  "CREDITO",
											  $monto_abonar,
											  $documento,
											  $DESCRIPCION="PAGO INICIAL ".$inf['obj']->ID_NIT,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F",
											  $MONTO_TOTAL_TRANS);	
	 											  
	  
		}else{
			$catalogo=$contabilidad->getCatalogo("NF");
			$documento=$inf['obj']->SERIE."-".$inf['obj']->NO_DOCTO; 
			$ID_EMPRESA="SJMN"; 
			/*CAJA NO FISCAL*/ 	
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA NF 
											  "DEBITO",
											  $monto_abonar,
											  $documento,
											  "ABONO A CAPITAL ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS); 
											  
																			  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['MODULO_CLIENTES']['cuenta'],
											  "CREDITO",
											  $monto_abonar,
											  $documento,
											  "ABONO A CAPITAL ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);	

			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  3,
											  $catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['cuenta'],
											  "DEBITO",
											  $interes_diferido_financiamiento,
											  $documento,
											  "ABONO A CAPITAL ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);	
											  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  4,
											  $catalogo['MODULO_CLIENTE_INTERESES']['cuenta'],
											  "CREDITO",
											  $interes_diferido_financiamiento,
											  $documento,
											  "ABONO A CAPITAL ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS);												  											  		
						
		}

		$obj->NO_CONTRATO=$contr->no_contrato;
		$obj->SERIE_CONTRATO=$contr->serie_contrato;  
		$this->procesarMovimientoContrato($inf['obj'],$data);  		 
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	}
	
	public function doDescuentoEspecial($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		
		$obj= new ObjectSQL();
		$obj->setTable("movimiento_contrato"); 
		$obj->ID_NIT=$mov->ID_NIT;
		$obj->NO_DOCTO=$mov->NO_DOCTO;
		$obj->CAJA_SERIE=$mov->SERIE; 
		if ($this->_fecha==""){
			$obj->FECHA="curdate()";
		}else{
			$obj->FECHA=$this->_fecha;			
		} 
		$obj->ID_CAJA=$mov->ID_CAJA;
		$obj->EM_ID=$mov->EM_ID;
		$obj->TIPO_DOC=$mov->TIPO_DOC;
		$obj->TIPO_MOV=$trans['TIPO_MOV'];		
		$obj->MONTO_DOC=$trans['MONTO'];  
		$obj->INTERESES_PAG=0;
		$obj->CAPITAL_PAG=0;
 		$obj->TOT_ABONOS=0;  
		$obj->TOTAL_MOV=$trans['MONTO'];   
 		$obj->NO_CUOTA=$trans['NO_CUOTA']; 
		$obj->TIPO_CAMBIO=$trans['TIPO_CAMBIO'];
		$obj->IMPUESTO_PAG=0;
		$obj->MORA_PAG=0;
		$obj->MANTENIMIENTO=0;
		$obj->INICIAL=0; 
		$obj->OF_COBROS=$mov->id_usuario;
		$obj->MOTORIZADO="";
		$obj->OBSERVACIONES="";
		$SQL=$obj->toSQL("insert"); 
  		mysql_query($SQL); 	
		
	}
	/*EFECTUA LA TRANSACCION DEL PAGO DE UNA CUOTA AL CONTRATO*/
	public function doPagoCuota($inf,$data,$NO_RNC_CED,$fact){
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		SystemHtml::getInstance()->includeClass("caja","ModContable");
		   	  
		$contabilidad= new ModContable($this->db_link);
		$contabilidad->setFecha($this->_fecha);
		
		$info_log=array(
			"ID_NIT"=>$data['ID_NIT'],
			"SERIE_CONTRATO"=>$data['SERIE_CONTRATO'],
			"NO_CONTRATO"=>$data['NO_CONTRATO'],
			"NO_RESERVA"=>$data['NO_RESERVA'],
			"ID_RESERVA"=>$data['ID_RESERVA']									
		);  
		$_contratos=new Contratos($this->db_link);   
		$contr=json_decode(System::getInstance()->Decrypt($this->_data['contrato']));
		$d_contrato=$_contratos->getInfoContrato($contr->serie_contrato,$contr->no_contrato);	

		$interes_a_pagar=($d_contrato->interes/$d_contrato->cuotas);
		$cuota_a_pagar_sin_interes=$d_contrato->valor_cuota-$interes_a_pagar;
		$valor_cuota=$interes_a_pagar+$cuota_a_pagar_sin_interes;
   		  
		$contrato=$d_contrato->serie_contrato." ".$d_contrato->no_contrato;
		$contabilidad= new ModContable($this->db_link);	
		
		$montos=$this->getMontoApagarYCobrar($this->_data['forma_pago_token']);
		 
		$pendiente_por_pagar=$valor_cuota-$data['MONTO'];
		$MONTO_TOTAL_TRANS=$data['MONTO'];
		
		$valor_cuota=$data['MONTO'];
		$interes_a_pagar=$data['CAPITAL_PAG'];
		$cuota_a_pagar_sin_interes=$data['INTERES_PAG'];	

 
 /*		if (round($valor_cuota,2) > round($data['MONTO'],2)){
			$interes_a_pagar=((($interes_a_pagar*100)/$valor_cuota)*$MONTO_TOTAL_TRANS)/100;
			$cuota_a_pagar_sin_interes=((($cuota_a_pagar_sin_interes*100)/$valor_cuota)*$MONTO_TOTAL_TRANS)/100;
			$valor_cuota=$interes_a_pagar+$cuota_a_pagar_sin_interes;											
		}*/
	 	 
		/*VALIDO SI ESTA TRANSACCION GENERA FACTURA
			EN CASO DE GENERAR FACTURA REGISTRO EL MOVIMIENTO CONTABLE
			EN LA EMPRESA FISCAL
		*/		
		if ($fact['validFF'] || $fact['validCF']){ 
			
			$ID_EMPRESA="SJMF";
			$documento=$fact['factura']->SERIE.sprintf("%08d",$fact['factura']->NO_DOCTO);
			
			$NO_RNC_CED=$_REQUEST['rnc'];
			
			$catalogo=$contabilidad->getCatalogo("F");
		  
			/*ACTUALIZA UN DOCUMENTO PARA SER USADO COMO FISCAL*/
			$this->doReciboFiscal($inf['obj']->SERIE,$inf['obj']->NO_DOCTO);
			
			/*CAJA FISCAL*/
			//11110201	Caja	 
			$contabilidad->registrarAsientoC($ID_EMPRESA,
											  date("Y"),
											  $tmovi->COD_TRANSCONTABLE,
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA FISCAL
											  "DEBITO",
											  $valor_cuota,
											  $documento,
											  $DESCRIPCION="ABONO A SALDO CLIENTE ".$contrato,
											  $CENTRO_COSTO,
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  $documento,
											  $NO_RNC_CED,
											  "",
											  "F");
											  
			//Ingreso Diferido Capital
			$contabilidad->registrarAsientoC($ID_EMPRESA,
									  date("Y"),
									  $tmovi->COD_TRANSCONTABLE,
									  2,
									  $catalogo['INGRESO_DIFERIDO_CAPITAL']['cuenta'], //Anticipo recibidos de clientes
									  "CREDITO",
									  $cuota_a_pagar_sin_interes,
									  $documento,
									  $DESCRIPCION="ABONO A SALDO CLIENTE ".$contrato,
									  $CENTRO_COSTO="N/A",
									  $inf['obj']->SERIE,
									  $inf['obj']->NO_DOCTO,
									  $documento,
									  $NO_RNC_CED,
									  "",
									  "F");	
									  
			//Intereses diferidos Financiamiento								  
			$contabilidad->registrarAsientoC($ID_EMPRESA,
							  date("Y"),
							  $tmovi->COD_TRANSCONTABLE,
							  3,
							  $catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['cuenta'], //Anticipo recibidos de clientes
							  "CREDITO",
							  $interes_a_pagar,
							  $documento,
							  $DESCRIPCION="ABONO A SALDO CLIENTE ".$contrato,
							  $CENTRO_COSTO="N/A",
							  $inf['obj']->SERIE,
							  $inf['obj']->NO_DOCTO,
							  $documento,
							  $NO_RNC_CED,
							  "",
							  "F");		
			//Intereses diferidos Financiamiento								  
			$contabilidad->registrarAsientoC($ID_EMPRESA,
							  date("Y"),
							  $tmovi->COD_TRANSCONTABLE,
							  4,
							  $catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['cuenta'],  
							  "DEBITO",
							  $interes_a_pagar,
							  $documento,
							  $DESCRIPCION="ABONO A SALDO CLIENTE ".$contrato,
							  $CENTRO_COSTO="N/A",
							  $inf['obj']->SERIE,
							  $inf['obj']->NO_DOCTO,
							  $documento,
							  $NO_RNC_CED,
							  "",
							  "F");	
								
			//Ingresos por Interes							  
			$contabilidad->registrarAsientoC($ID_EMPRESA,
							  date("Y"),
							  $tmovi->COD_TRANSCONTABLE,
							  5,
							  $catalogo['INGRESO_POR_INTERES']['cuenta'], //Anticipo recibidos de clientes
							  "CREDITO",
							  $interes_a_pagar,
							  $documento,
							  $DESCRIPCION="ABONO A SALDO CLIENTE ".$contrato,
							  $CENTRO_COSTO="N/A",
							  $inf['obj']->SERIE,
							  $inf['obj']->NO_DOCTO,
							  $documento,
							  $NO_RNC_CED,
							  "",
							  "F");	 
																						  
	  
		}else{
			$documento=$inf['obj']->SERIE."-".$inf['obj']->NO_DOCTO; 
			$ID_EMPRESA="SJMN"; 
			$catalogo=$contabilidad->getCatalogo("NF");
			 
			/*CAJA NO FISCAL*/ 	
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  1,
											  $catalogo['CAJA']['cuenta'], //CAJA NF
											  "DEBITO",
											  $valor_cuota,
											  $documento,
											  $DESCRIPCION="ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											  $MONTO_TOTAL_TRANS); 
										  
			//Modulo de Clientes NF 
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  2,
											  $catalogo['MODULO_CLIENTES']['cuenta'], //Modulo de Clientes NF 
											  "CREDITO",
											  $cuota_a_pagar_sin_interes,
											  $documento,
											  $DESCRIPCION="ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											 $MONTO_TOTAL_TRANS); 												  
											  
			//Modulo de Clientes Intereses								  								  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  3,
											   $catalogo['MODULO_CLIENTE_INTERESES']['cuenta'],  
											  "CREDITO",
											  $interes_a_pagar,
											  $documento,
											  $DESCRIPCION="ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											 $MONTO_TOTAL_TRANS);	
			//Intereses diferidos Financiamiento 			  								  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  4,
											   $catalogo['INTERESES_DIFERIDOS_FINANCIAMIENTO']['cuenta'],  
											  "DEBITO",
											  $interes_a_pagar,
											  $documento,
											  $DESCRIPCION="ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											 $MONTO_TOTAL_TRANS);	
							  
			//Ingresos por Interes		  								  
			$contabilidad->registrarAsientoC($info_log,
											$ID_EMPRESA,
											  date("Y"),
											  rand(),
											  5,
											   $catalogo['INGRESO_POR_INTERES']['cuenta'],  
											  "CREDITO",
											  $interes_a_pagar,
											  $documento,
											  $DESCRIPCION="ABONO A SALDO ".$contrato,
											  $CENTRO_COSTO="N/A",
											  $inf['obj']->SERIE,
											  $inf['obj']->NO_DOCTO,
											  "",
											  "",
											  "",
											  "NF",
											 $MONTO_TOTAL_TRANS);	
				
			 
		} 
 
		$obj=$inf['obj'];		
		$obj->NO_CONTRATO=$contr->no_contrato;
		$obj->SERIE_CONTRATO=$contr->serie_contrato; 
		
		$this->procesarMovimientoContrato($obj,$data,$data['MONTO']);  
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  	
	} 
	
	public function doAnularReciboCaja($SERIE,$NO_DOCTO,$serie_contrato,$no_contrato){ 
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		$cn=new Contratos($this->db_link); 		
		$SQL="SELECT  
				tipo_movimiento.*,
				MCC.*,
				MF.*
			FROM 
			`movimiento_caja` AS MCC  
			INNER JOIN movimiento_factura AS MF ON (MF.CAJA_SERIE=MCC.SERIE AND MF.CAJA_NO_DOCTO=MCC.NO_DOCTO)
			INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=MF.TIPO_MOV)
		WHERE MCC.SERIE='".$SERIE."' AND MCC.NO_DOCTO='".$NO_DOCTO."' "; 
	 
		$rs=mysql_query($SQL); 
		while($row=@mysql_fetch_assoc($rs)){	    
			if (method_exists($this,trim($row['FUNCCION_ANULAC']))){ 
				$this->$row['FUNCCION_ANULAC']($row); 	
			} 
		}  
		$capital=$cn->getCapitalInteresCuotaFromContrato($serie_contrato,$no_contrato);
		$CAPITAL_PAG =$CAPITAL_PAG+$recibo['CAPITAL_PAG'];
		$INTERESES_PAG =$INTERESES_PAG+$recibo['INTERESES_PAG']; 
		/*PONGO LOS CAMPOS EN CONTRATO PARA SER ACTUALIZADOS*/ 
		$contratos = new ObjectSQL();
		$contratos->capital_pagado=$capital->capital_pagado;
		$contratos->intereses_pagados=$capital->interes_pagado;
		$contratos->setTable("contratos");
		$SQL=$contratos->toSQL("update"," where serie_contrato='".$serie_contrato."' 
			and no_contrato='".$no_contrato."'");	
		mysql_query($SQL); 		
		 
		$SQL="SELECT * FROM `siad_fncbdmaytrs` WHERE 
				SERIE='".$SERIE."' AND 
					NO_DOCTO='".$NO_DOCTO."' 
					AND (SELECT COUNT(*) FROM 
						`siad_fncbdmaytrs` WHERE
						 SERIE='".$SERIE."' AND 
						 NO_DOCTO='".$NO_DOCTO."' AND CONDICION=0)=0					
				ORDER BY FECHATRS,SECUENCIA DESC";  		 
		$rs=mysql_query($SQL); 
		while($row=@mysql_fetch_assoc($rs)){	   
			$obj = new ObjectSQL();
			$obj->push($row); 
			$obj->NUMEROTRS=rand();
			$obj->PERIODO=date("Y");
			$obj->CODIGOTRS=rand();
			$obj->DESCRIPCION="ANULACION ".$obj->DESCRIPCION;
			if ($row['DEBITO']>0){
				$obj->CREDITO=$row['DEBITO'];
				$obj->DEBITO=0;
			}
			if ($row['CREDITO']>0){
				$obj->DEBITO=$row['CREDITO'];
				$obj->CREDITO=0;				
			}	
			$obj->CONDICION=0; //ANULADO 
			unset($obj->FECHATRS);
			$obj->setTable("siad_fncbdmaytrs");
			$SQL=$obj->toSQL("insert");
			mysql_query($SQL);   		 
		}   
	}
	
	/*EFECTUA EL ROLL BACK DE UN ABONO A CAPITAL*/
	public function doAnularAbonoCapital($recibo){ 
		SystemHtml::getInstance()->includeClass("contratos","Contratos");  
		
		$SQL="SELECT * FROM `cambios_financieros` 
				WHERE `idcambios_financieros`='".$recibo['ID_CAMBIOS_FINANCIEROS']."' and estatus_solicitud='1' "; 
		$rs=mysql_query($SQL); 
		$cn = new ObjectSQL();
		while($row=@mysql_fetch_assoc($rs)){	    
			unset($row['idcambios_financieros']);
			unset($row['valor_reconocimiento']);
			unset($row['autoriza']);
			unset($row['nuevo_em_id']);
			unset($row['nuevo_serie_contrato']);
			unset($row['nuevo_no_contrato']);
			unset($row['estatus_solicitud']);
			unset($row['fecha_executado']);
			unset($row['nuevo_no_contrato']); 						
			$cn->push($row);
			
			$ccn=new Contratos($this->db_link);  
			$contrato=$ccn->getBasicInfoContrato($recibo['SERIE_CONTRATO'],$recibo['NO_CONTRATO']);	
			
			$cn->setTable("contratos");
			$SQL=$cn->toSQL("update"," where no_contrato='".$recibo['NO_CONTRATO']."' 
								and serie_contrato='".$recibo['SERIE_CONTRATO']."'");
			mysql_query($SQL);
								
			$cf = new ObjectSQL();
			$cf->estatus_solicitud=26; 
			$cf->setTable("cambios_financieros");
			$SQL=$cf->toSQL("update"," WHERE `idcambios_financieros`='".$recibo['ID_CAMBIOS_FINANCIEROS']."' ");
			mysql_query($SQL);
			 
			$mc = new ObjectSQL();
			$mc->ID_CAMBIOS_FINANCIEROS=0; 
			$mc->setTable("movimiento_caja");
			$SQL=$mc->toSQL("update"," WHERE `ID_CAMBIOS_FINANCIEROS`='".$recibo['ID_CAMBIOS_FINANCIEROS']."' ");		
			mysql_query($SQL);
			 
			SysLog::getInstance()->Log($recibo['ID_NIT'], 
										 $recibo['SERIE_CONTRATO'],
										 $recibo['NO_CONTRATO'],
										 '',
										 '',
										 "ANULANDO MOVIMIENTO ".$recibo['DESCRIPCION']." RECIBO NO.: ".
											$recibo['CAJA_SERIE']."-".$recibo['CAJA_NO_DOCTO'],
										 json_encode($contrato),
										 'ANULACION',
										 $recibo['CAJA_SERIE'],
										 $recibo['CAJA_NO_DOCTO']);				
		} 
		 				 		
		$data=array("error"=>false,"mensaje"=>'Transacci??n realizada!'); 
		return $data;  
	} 	 
	 
	public function procesarMovimientoContrato($mov,$trans,$monto_pagado=0){
		//$TMOV= new CTipoMovimiento($this->db_link);
		/*CUANDO EL TIPO MOVIMIENTO EN OPERACION ES UNA RESTA ES */
		$contratos= new ObjectSQL();
		$contratos->setTable("contratos"); 
	  
		$obj= new ObjectSQL();
		$obj->setTable("movimiento_contrato"); 
		
		if (isset($trans['ID_MOV_FACT'])){
			$obj->ID_MOV_FACT=$trans['ID_MOV_FACT'];
		}
		$obj->ID_NIT=$mov->ID_NIT;
		$obj->NO_DOCTO=$mov->NO_DOCTO;
		$obj->CAJA_SERIE=$mov->SERIE;

		if ($this->_fecha==""){
			$obj->FECHA="curdate()";
		}else{
			$obj->FECHA=$this->_fecha;			
		}
	
		$obj->ID_CAJA=$mov->ID_CAJA;
		$obj->EM_ID=$mov->EM_ID;
		$obj->TIPO_DOC=$mov->TIPO_DOC;
		$obj->TIPO_MOV=$trans['TIPO_MOV'];		
		$obj->MONTO_DOC=$trans['MONTO']; 
 
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		$contr=json_decode(System::getInstance()->Decrypt($this->_data['contrato']));
		$_contratos=new Contratos($this->db_link);
		$d_contrato=$_contratos->getInfoContrato($contr->serie_contrato,$contr->no_contrato);  
		$obj->INTERESES_PAG=0;
		$obj->CAPITAL_PAG=$trans['MONTO'];
		$obj->CUOTA=0;
		$obj->TOT_ABONOS=0; 
		$obj->CAPITAL_PAG=0;
		
		if ($trans['TIPO_MOV']=="CT"){ 
			$obj->CAPITAL_PAG=$trans['MONTO'];  			
			$obj->TOTAL_MOV=$trans['MONTO']; 
			/*PONGO LOS CAMPOS EN CONTRATO PARA SER ACTUALIZADOS*/ 
			$contratos->capital_pagado="(capital_pagado+$obj->CAPITAL_PAG)"; 
			$SQL_Contrato=$contratos->toSQL("update"," where serie_contrato='".$contr->serie_contrato."' 
				and no_contrato='".$contr->no_contrato."'");
 			mysql_query($SQL_Contrato);  
			$obj->CUOTA=$trans['MONTO']; 
			$obj->TOTAL_MOV=$obj->INTERESES_PAG+$obj->CAPITAL_PAG; //Este no funciona	
			
		}elseif ($trans['TIPO_MOV']=="CAPITAL"){
			$obj->CAPITAL_PAG=$trans['MONTO'];  			
			$obj->TOTAL_MOV=$trans['MONTO']; 
		}elseif ($trans['TIPO_MOV']=="ABO"){
			$obj->TOT_ABONOS=$trans['MONTO'];  			
			$obj->TOTAL_MOV=$trans['MONTO']; 
		}else if ($trans['TIPO_MOV']=="CUOTA"){  		
			//$obj->INTERESES_PAG=($d_contrato->interes/$d_contrato->cuotas);
			//$obj->CAPITAL_PAG=$d_contrato->valor_cuota-$obj->INTERESES_PAG;
			$obj->INTERESES_PAG=$trans['INTERES_PAG'];	
 			$obj->CAPITAL_PAG=$trans['CAPITAL_PAG'];
			/*
			if ($monto_pagado>0){			
				
				$interes=($d_contrato->interes/$d_contrato->cuotas);
 				$interes_a_pagar=((($interes*100)/$d_contrato->valor_cuota)*$monto_pagado)/100;
				
				$cuota_a_pagar_sin_interes=$d_contrato->valor_cuota-($d_contrato->interes/$d_contrato->cuotas);				
				$cuota_a_pagar_sin_interes=((($cuota_a_pagar_sin_interes*100)/$d_contrato->valor_cuota)*$monto_pagado)/100;
				
				$valor_cuota=$interes_a_pagar+$cuota_a_pagar_sin_interes;
				 
				
				$obj->INTERESES_PAG=$interes_a_pagar;
				$obj->CAPITAL_PAG=$cuota_a_pagar_sin_interes; 
				$obj->MONTO_DOC=$valor_cuota;  
				$trans['NO_CUOTA']=$valor_cuota/$d_contrato->valor_cuota;
				$trans['MONTO']=$valor_cuota;
							
			}
			 */
			 
//			$tasa=$this->getTasaActual($d_contrato->tipo_moneda);		
			/*PONGO LOS CAMPOS EN CONTRATO PARA SER ACTUALIZADOS*/ 
			$contratos->capital_pagado="(capital_pagado+$obj->CAPITAL_PAG)";
			$contratos->intereses_pagados="(intereses_pagados+$obj->INTERESES_PAG)";
			$SQL_Contrato=$contratos->toSQL("update"," where serie_contrato='".$contr->serie_contrato."' 
				and no_contrato='".$contr->no_contrato."'");
		 
 			mysql_query($SQL_Contrato);  
			$obj->CUOTA=$trans['MONTO']; 
			$obj->TOTAL_MOV=$obj->INTERESES_PAG+$obj->CAPITAL_PAG; //Este no funciona			
		}
		
		/*CACULO LA CUOTA QUE HA PAGADO*/
		$obj->NO_CUOTA=$trans['NO_CUOTA']; 
		$obj->TIPO_CAMBIO=$trans['TIPO_CAMBIO'];
		$obj->IMPUESTO_PAG=0;
		$obj->MORA_PAG=0;
		$obj->MANTENIMIENTO=0;
		$obj->INICIAL=0; 
		$obj->OF_COBROS=$mov->id_usuario;
		$obj->MOTORIZADO="";
		$obj->OBSERVACIONES="";
		$SQL=$obj->toSQL("insert"); 
  		mysql_query($SQL);  
	 	 
	}	
	
	/* Valida si hay que generarle comprobante  */
	public function validFormaPagoCPT($forma_pago){
		/* 
			TC=> Tarjeta de credito
			TB=> Transferencia Bancaria 
		*/ 
		/* TRANSAPCIONES QUE GENERAN FACTURA EN CONSUMIDOR FINAL */
		$trans_g=array('TC'=>1,'TB'=>1);
		$_do_genera_consumo_final=false; 
		
		if (count($forma_pago)>0){
			foreach($forma_pago as $key=>$val){   
				/*
					SI EXISTE UNA TRANSAPCION EN LA CUAL GENERE FACTURA ENTONCES PROCEDE
				*/
				if (array_key_exists($val->FORMA_PAGO,$trans_g)){  
					$_do_genera_consumo_final=true;
					break;
				}
				if ($val->FORMA_PAGO=="DP"){
					if ($this->checkIfBankNeedNCF($val->ID_BANCO)){
						$_do_genera_consumo_final=true;
						break;	
					}
				}
				
			}	 
		}   
		return $_do_genera_consumo_final;
	}
	
	public function isEstaRegistradoEnLaDGII($codigo){
		$SQL="SELECT factura_fiscal FROM `sys_personas` WHERE id_nit='".$codigo."'";
		$rs=mysql_query($SQL);
		$ret=array('check'=>false,'valid'=>false);
		if (mysql_num_rows($rs)>0){
			$row=mysql_fetch_assoc($rs); 
			if ($row['factura_fiscal']=="1"){
				$ret['check']=true;
				$ret['valid']=true;				
			}
		}else{
			$SQL="SELECT count(*) as total FROM `dgii_rnc` WHERE codigo='".$codigo."' "; 
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$ret['check']=false;
			$ret['valid']=true;				
		}
		return $ret; 
	}	
	/*VALIDA SI UN BANCO REQUIERE NUMERO DE COMPROBANTE FINAL*/
	public function checkIfBankNeedNCF($ban_id){
		$SQL="SELECT * FROM `bancos`  WHERE ban_id='".$ban_id."' limit 1 ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['ban_tipo']=="F"){
			return true;	
		}
		return false;
	}
	
	/*Proceso que valida si hay que generar una factura*/
	public function GenerarFacturaIfNeed($obj_recibo,$isNCF,$do_f_final=false){ 
	
			$_do_genera_consumo_final=false; 
			
			if (count($obj_recibo['forma_pago'])>0){
				$_do_genera_consumo_final=$this->validFormaPagoCPT($obj_recibo['forma_pago']);
				
				if ($do_f_final){
					$_do_genera_consumo_final=true;
				}
			}else{
				if ($do_f_final){
					$_do_genera_consumo_final=true;
				}				
			} 
			
			$obj=$obj_recibo['obj'];
			$_do_generar_factura_fiscal=false; 
			/* VERIFICO SI TENGO QUE GENERAR FACTURA FISCAL*/
			if ($isNCF=="true"){ 
				$_do_generar_factura_fiscal=true;
			}   
		 
			/*EN CASO DE GENERAR UNA FACTURA FISCAL*/
			if ($_do_generar_factura_fiscal || 
									$_do_genera_consumo_final){   
					
				if ($_do_genera_consumo_final){
					$isNCF=false;	
				}
				if ($_do_generar_factura_fiscal){
					$isNCF=true;	
				}				
								
			 	$factura=$this->generateFactura($isNCF, 
												$obj->EM_ID,
												$obj->ID_CAJA, 
												$obj->id_usuario,
												$obj->MONTO 
												);
												
				$mov_u = new ObjectSQL();
				$mov_u->NO_DOC_FACTURA=$factura->NO_DOCTO;
				$mov_u->SERIE_FACTURA=$factura->SERIE;
				$mov_u->TIPO_DOC_FACTURA=$factura->TIPO_DOC;
				$mov_u->setTable('movimiento_caja'); 
				$SQL=$mov_u->toSQL('update'," where ID_CAJA='".$obj->ID_CAJA."' AND NO_DOCTO='".$obj->NO_DOCTO."' 
				AND SERIE='".$obj->SERIE."'");	
				mysql_query($SQL);													
 		 
			}  
			
		/*RETORNO SI ES CONSUMIDOR FINA O FISCAL Y LA TRANSACCION DE LA FACTURA*/	
		return array("validCF"=>$_do_genera_consumo_final,"validFF"=>$_do_generar_factura_fiscal,"factura"=>$factura);
	}
  
	/*PROCESO QUE EMITE UNA FACTURA*/
	public function generateFactura($isNCF,$EM_ID,$ID_CAJA,$id_usuario,$MONTO){  
		$tipo_doc=FACTURA_CONSUMO;
		if ($isNCF){
			$tipo_doc=FACTURA_FISCAL;
		}
		$factura=$this->getNextFactura($tipo_doc); 
		$fac = new ObjectSQL();
		$fac->TIPO_DOC=$tipo_doc;
		$fac->NO_DOCTO=$factura['CORRELATIVO'];
		$fac->SERIE=$factura['SERIE'];
		$fac->EM_ID=$EM_ID;  
		$fac->ID_CAJA=$ID_CAJA;  
		$fac->ID_USUARIO=$id_usuario;
		$fac->MONTO=$MONTO;
		$fac->FACTURADO_A_NIT="";
		$fac->setTable('facturacion'); 
		$SQL=$fac->toSQL('insert');	
		mysql_query($SQL);
		return $fac;
	}
	
	/*HACER QUE UN RECIBO SEA FISCAL O NO FISCAL*/
	private function doReciboFiscal($SERIE_DOCTO,$NO_DOCTO){ 
		$mov = new ObjectSQL();
		$mov->TIPO_DOC_FISCAL=1; //CLASIFICA UN DOCUMENTO COMO FISCAL 
		$mov->setTable('movimiento_caja'); 
		$SQL=$mov->toSQL('update'," where NO_DOCTO='". $NO_DOCTO ."' and  SERIE='".$SERIE_DOCTO."' ");  
 	 	mysql_query($SQL); 	
	}

	public function doCrearReciboCaja(){ 
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		  
		$this->_data=$_REQUEST;   
		 
	 
		$forma_pago=array();
		$MONTO_PAGO_CAJA=0;  
		$listado_nc=$this->getListCarritoNotaCredito(); 
		/*VERIFICO SI EXISTE UNA NOTA DE CREDITO AFECTANDO EL DOCUMENTO*/
		if (count($listado_nc)<=0){ 
		
			if (!validateField($this->_data,"forma_pago_token")){
				$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
				return $data; 
				exit;	
			}			
			$forma_pago=$this->getItem($this->_data['forma_pago_token']);
			
			if (!is_array($forma_pago)){
				$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
				return $data; 
				exit;
			}
		}else{
			$tipo_cambio=1;
 			$monto_nc=0;
			foreach($listado_nc as $key =>$row){
				$monto_nc=$monto_nc+$row->MONTO_TOTAL; 
			} 	
			$MONTO_PAGO_CAJA=$monto_nc;		
		}
		 
		
		$needCF=false; //NECESITA COMPROBANTE FINAL?
		if (isset($this->_data['isNCF'])){
			if ($this->_data['isNCF']=="true"){
				$needCF=true;
			}
		} 
	
		/*VALIDO EN CASO QUE NECESITE NCF*/
	/*	if (isset($this->_data['isNCF'])){
			if ($this->_data['isNCF']=="true"){
				if (isset($this->_data['rnc'])){
					if (trim($this->_data['rnc'])==""){
						$data=array("error"=>true,"mensaje"=>'Debe ingresar un RNC/CEDULA');
						return $data; 
						exit;
					}
				}
			}
		}else{
			$data=array("error"=>true,"mensaje"=>'Debe de indicar si contiene NCF');
			return $data; 
			exit;
		} */
	 
		 $fecha_requerimiento=isset($this->_data['fecha_requerimiento_especial_xx'])?$this->_data['fecha_requerimiento_especial_xx']:'';
	 
		$_contratos=new Contratos($this->db_link);   
		$cajero=UserAccess::getInstance()->getCaja(); 
		$fecha_transapccion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		
		if ($fecha_requerimiento==""){
			$serie_doc=RECIBO_CAJA;
		}else{
			$serie_doc=RECIBO_CAJA;//trim($cajero['ID_CAJA'])."-".str_replace("-","",$fecha_requerimiento);
			$fecha_transapccion=$fecha_requerimiento;
		}

		$no_documento=$this->getNoCorrelativoDoc(RECIBO_CAJA,"RC");//$this->getNextNoDocument($serie_doc); 		 
		/*EN CASO DE QUE HAYA UNA RESERVA ENTONCES LLENOS LOS DATOS CORRESPONDIENTES*/
		$reserva=json_decode(System::getInstance()->Decrypt($this->_data['reserva']));
 
		$no_reserva=0;
		$id_reserva=0;
		if (isset($reserva->no_reserva) && isset($reserva->id_reserva)){
			$no_reserva=$reserva->no_reserva;
			$id_reserva=$reserva->id_reserva;			
		}		
		if (validateField($this->_data,"id_nit")){ 
			$id_nit=System::getInstance()->Decrypt($this->_data['id_nit']);  			
		}		 
   
		if (!isset($cajero['ID_CAJA'])){
			$data=array("error"=>true,"mensaje"=>'El usuario no tiene caja asignada!');
			return $data; 
			exit;
		}
		 
		$serie_contrato="";
		$no_contrato=""	;		 
		$contr=json_decode(System::getInstance()->Decrypt($this->_data['contrato']));
		if (isset($contr->serie_contrato) && isset($contr->no_contrato)){
			//$d_contrato=$_contratos->getInfoContrato($contr->serie_contrato,$contr->no_contrato);	
			$serie_contrato=$contr->serie_contrato;
			$no_contrato=$contr->no_contrato;
		} 
		if (trim($id_nit)==""){
			$data=array("error"=>true,"mensaje"=>'Error no se puede procesar el pago, el NIT del cliente no existe!');
			return $data;
			exit;
		}
		   
		if ($this->checkExistRecibo($cajero['ID_CAJA'],$serie_doc,$no_documento)>0){
			$data=array("error"=>true,"mensaje"=>'Error no se puede volver a procesar el pago, la serie o el documento existe!');
 
			return $data;
			exit;
		}  
		 
		$forma_pago_array=array();
		/*VALIDANDO LA FORMA DE PAGO*/
		foreach($forma_pago as $key =>$val){
			$fpago=new ObjectSQL();
			$fpago->setTable('forma_pago_caja'); 
			
			/*SI LA FORMA DE PAGO ES 
			2=> Tarjeta de credito*/
			if (($val['forma_pago']=="TC") || ($val['forma_pago']=="CK") || ($val['forma_pago']=="DP")){ 
				$banco=json_decode(System::getInstance()->Decrypt($val['banco']));	  
				if (!(validateField($val,"banco") 
					  && validateField($val,"autorizacion")
					   && validateField($banco,"ban_id") )){  
					$data=array("error"=>true,"mensaje"=>'La informaci??n proporcionada no esta completa!');
					 
					return $data; 
					exit;	
				}else{ 
					$fpago->ID_BANCO=$banco->ban_id; 	
				}
			}
				
			$fpago->FORMA_PAGO=$val['forma_pago'];
			$fpago->AUTORIZACION=$val['autorizacion'];  
			$fpago->TIPO_CAMBIO=$val['tipo_cambio'];
			$fpago->MONTO=$val['monto_a_pagar'];
			$fpago->FECHA=$fecha_transapccion;
			$fpago->TIPO_DOC=$tipo_doc;
			$fpago->NO_DOCTO=$no_documento;
			$fpago->SERIE=$serie_doc;
			$fpago->EM_ID=$EM_ID;
			$fpago->ID_CAJA=$cajero['ID_CAJA'];
			$fpago->ID_NIT=$id_nit;
			$fpago->RC_NO_DOCTO=$no_documento;
			$fpago->RC_SERIE=$serie_doc;			
 			$SQL=$fpago->toSQL('insert');  
 	 		mysql_query($SQL);	
			$tipo_cambio=$val['tipo_cambio'];
			$MONTO_PAGO_CAJA=$MONTO_PAGO_CAJA+$fpago->MONTO;  
			
			array_push($forma_pago_array,$fpago);
			
			SysLog::getInstance()->Log($id_nit, 
									 $serie_contrato,
									 $no_contrato,
									 $no_reserva,
									 $id_reserva,
									 "TRANSACCION FORMA PAGO",
									 json_encode($fpago),
									 'FORMA_PAGO');	
		}  
		   
		$mov = new ObjectSQL();
		$mov->NO_DOCTO=$no_documento;
		$mov->EM_ID=$EM_ID;  
		$mov->ID_CAJA=$cajero['ID_CAJA'];  
		$mov->SERIE=$serie_doc;
		$mov->id_usuario=$cajero['id_usuario'];
		$mov->ID_NIT=$id_nit;
		$mov->TIPO_DOC=RECIBO_CAJA;  
 		$mov->FECHA_DOC=$fecha_transapccion;
		$mov->FECHA=$fecha_transapccion;  
		$mov->ID_RESERVA=$id_reserva; 
		$mov->NO_RESERVA=$no_reserva;		 
		$mov->MONTO=$MONTO_PAGO_CAJA;
		$mov->TIPO_MONEDA=$tipo_cambio>1?'DOLARES':'LOCAL';
		$mov->TIPO_CAMBIO=$tipo_cambio; 
		$mov->NO_CONTRATO=$no_contrato;
		$mov->SERIE_CONTRATO=$serie_contrato;
		$mov->REP_VENTA=$this->_data['reporte_venta'];
		$mov->DESCUENTO=0;
		$mov->INICIAL="N"; 
		if ($tmovi->TIPO_MOV=="INI"){
			$mov->INICIAL="S"; 
		} 	  		
 		$mov->OBSERVACIONES=$this->_data['observacion']; 
		$mov->setTable('movimiento_caja'); 
  
		$SQL=$mov->toSQL('insert');

		mysql_query($SQL); 
		  
		SysLog::getInstance()->Log($mov->ID_NIT, 
									 $serie_contrato,
									 $no_contrato,
									 $mov->NO_RESERVA,
									 $mov->ID_RESERVA,
									 "TRANSACCION CAJA",
									 json_encode($mov),
									 'MOVIMIENTO_CAJA');	
		
   	 
 		return array("error"=>false,"mensaje"=>'',"obj"=>$mov,"forma_pago"=>$forma_pago_array);
	}

	 
	/*
		GENERAR UN RECIBO DE VENTAS PARA SER PROCESDO
		RM=>Recibo Manual
	*/
	public function doGeneraRecibo($tipo_doc="RM"){ 
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		$this->_data=$_REQUEST;   
		 
		if (!validateField($this->_data,"forma_pago_token")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;	
		}
	
		$forma_pago=$this->getItem($this->_data['forma_pago_token']);
		
 		if (!is_array($forma_pago)){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;
		}
				
		/*VALIDO EN CASO QUE NECESITE NCF*/
		if (isset($this->_data['isNCF'])){
			if ($this->_data['isNCF']=="true"){
				if (isset($this->_data['rnc'])){
					if (trim($this->_data['rnc'])==""){
						$data=array("error"=>true,"mensaje"=>'Debe ingresar un RNC/CEDULA');
						return $data; 
						exit;
					}
				}
			}
		}else{
			$data=array("error"=>true,"mensaje"=>'Debe de indicar si contiene NCF');
			return $data; 
			exit;
		} 
	 
		$_contratos=new Contratos($this->db_link);   
		$cajero=UserAccess::getInstance()->getCaja(); 
		$serie_doc=$tipo_doc."-".date('Ymd');
		$no_documento=$this->getNextNoDocument($serie_doc); 

		if (!isset($cajero['ID_CAJA'])){
			$data=array("error"=>true,"mensaje"=>'El usuario no tiene caja asignada!');
			return $data; 
			exit;
		}
		if (validateField($this->_data,"id_nit")){ 
			$id_nit=System::getInstance()->Decrypt($this->_data['id_nit']);  			
		}
		
		$serie_contrato="";
		$no_contrato=""	;		 
		if (trim($id_nit)==""){
			$data=array("error"=>true,"mensaje"=>'Error no se puede procesar el pago, el NIT del cliente no existe!');
			return $data;
			exit;
		}
		   
		if ($this->checkExistRecibo($cajero['ID_CAJA'],$serie_doc,$no_documento)>0){
			$data=array("error"=>true,"mensaje"=>'Error no se puede volver a procesar el pago, la serie o el documento existe!');
 
			return $data;
			exit;
		}  

		if (!validateField($this->_data,'tipo_movimiento')){
			$data=array("error"=>true,"mensaje"=>'Debe de seleccionar un recibo!');
			return $data; 
		}
		$tipo_movimiento=json_decode(System::getInstance()->Decrypt($this->_data['tipo_movimiento']));
		$asesor=json_decode(System::getInstance()->Decrypt($this->_data['asesor']));
		
		$MONTO_PAGO_CAJA=0;  
		$forma_pago_array=array();
		/*VALIDANDO LA FORMA DE PAGO*/
		foreach($forma_pago as $key =>$val){
			$fpago=new ObjectSQL();
			$fpago->setTable('forma_pago_caja'); 
			
			/*SI LA FORMA DE PAGO ES 
			2=> Tarjeta de credito*/
			if (($val['forma_pago']=="2") || ($val['forma_pago']=="3")){ 
				$banco=json_decode(System::getInstance()->Decrypt($val['banco']));	  
				if (!(validateField($val,"banco") 
					  && validateField($val,"autorizacion")
					   && validateField($banco,"ban_id") )){  
					$data=array("error"=>true,"mensaje"=>'La informaci??n proporcionada no esta completa!');
					 
					return $data; 
					exit;	
				}else{ 
					$fpago->ID_BANCO=$banco->ban_id; 	
				}
			}
				
			$fpago->FORMA_PAGO=$val['forma_pago'];
			$fpago->AUTORIZACION=$val['autorizacion'];  
			$fpago->TIPO_CAMBIO=$val['tipo_cambio'];
			$fpago->MONTO=$val['monto_a_pagar'];
			$fpago->FECHA="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$fpago->TIPO_DOC=$tipo_doc;
			$fpago->NO_DOCTO=$no_documento;
			$fpago->SERIE=$serie_doc;
			$fpago->EM_ID=$EM_ID;
			$fpago->ID_CAJA=$cajero['ID_CAJA'];
			$fpago->ID_NIT=$id_nit;
			$fpago->OLD_NO_DOCTO=$no_documento;
			$fpago->OLD_SERIE=$serie_doc;	
						
 			$SQL=$fpago->toSQL('insert'); 
 	 		mysql_query($SQL);	
			$tipo_cambio=$val['tipo_cambio'];
			$MONTO_PAGO_CAJA=$MONTO_PAGO_CAJA+$fpago->MONTO;  
			
			array_push($forma_pago_array,$fpago);
			
			SysLog::getInstance()->Log($id_nit, 
									 $serie_contrato,
									 $no_contrato,
									 $no_reserva,
									 $id_reserva,
									 "TRANSACCION FORMA PAGO",
									 json_encode($fpago),
									 'FORMA_PAGO');	
		}  
		
		$mov = new ObjectSQL();
		$mov->NO_DOCTO=$no_documento;
		$mov->EM_ID=$EM_ID;  
		$mov->ID_CAJA=$cajero['ID_CAJA'];  
		$mov->SERIE=$serie_doc;
		$mov->id_usuario=$cajero['id_usuario'];
		$mov->ID_NIT=$id_nit;
		$mov->TIPO_DOC=RECIBO_VIRTUAL;  
 		$mov->FECHA_DOC="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$mov->FECHA="CONCAT(CURDATE(),' ',CURRENT_TIME())";  
		$mov->ID_RESERVA=$id_reserva; 
		$mov->NO_RESERVA=$no_reserva;		 
		$mov->MONTO=$MONTO_PAGO_CAJA;
		$mov->TIPO_MONEDA=$tipo_cambio>1?'DOLARES':'LOCAL';
		$mov->TIPO_CAMBIO=$tipo_cambio; 
		$mov->NO_CONTRATO=$no_contrato;
		$mov->SERIE_CONTRATO=$serie_contrato;
	//	$mov->REP_VENTA=$this->_data['reporte_venta'];
		$mov->DESCUENTO=0;
		$mov->INICIAL="N"; 
		if ($tmovi->TIPO_MOV=="INI"){
			$mov->INICIAL="S"; 
		} 	  		
 		$mov->OBSERVACIONES=$this->_data['observacion']; 
		$mov->setTable('movimiento_caja');   
		$SQL=$mov->toSQL('insert');	 
		mysql_query($SQL); 

		MVFactura::getInstance()->movFactura($mov->NO_DOCTO,
											  $mov->SERIE,
											  $no_contrato,
											  $serie_contrato,								  
											  "",  
											  $id_nit,
											  $tipo_movimiento->TIPO_MOV, 
											  0,
											  0,
											  $mov->MONTO,
											  $tipo_cambio, 
											  36, //ESTATUS
											  '',
											  '',
											  '',
											  '',
											  '',
											  '',
											  '',
											  $this->_data['reporte_venta'],
											  $this->_data['no_recibo_venta'],
											  $asesor->id_comercial,
											  $asesor->id_nit);	 	
		  

		SysLog::getInstance()->Log($id_nit, 
									 '',
									 '',
									 '',
									 '',
									 "TRANSACCION RECIBO VIRTUAL CAJA",
									 json_encode($mov),
									 'MOVIMIENTO_CAJA',
									 $mov->SERIE,
									 $mov->NO_DOCTO
									 );	
								
   	 
 		return array("error"=>false,"mensaje"=>'',"obj"=>$mov,"forma_pago"=>$forma_pago_array);
	}
		
 
	
	public function getNoCorrelativoDoc($tipo_doc,$serie){
		
		$SQL="LOCK TABLES correlativo_doc WRITE;";
		mysql_query($SQL);	
			
		$cor = new ObjectSQL(); 
		$cor->CORRELATIVO="(CORRELATIVO+1)"; 
		$cor->setTable('correlativo_doc'); 
		$SQL=$cor->toSQL('update',"where TIPO_DOC='".$tipo_doc."'");
		mysql_query($SQL);	
			
		$SQL="SELECT `SERIE`,(`CORRELATIVO`)AS CORRELATIVO 
			FROM `correlativo_doc` WHERE `TIPO_DOC`='".$tipo_doc."' "; 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		
		$SQL="UNLOCK TABLES;"; 
		mysql_query($SQL);
 
		$corr=0;
		if (count($row)>0){
			$corr=$row['CORRELATIVO']; 
		}
		return $corr;
	}		
	  
	/*VALIDA LA INFORMACION BASICA PARA PODER PROCEDER A REALIZAR CUALQUIER TIPO DE MOVIMIENTO*/
	public function validateDataToPay(){
		$contrato=json_decode(System::getInstance()->Decrypt($this->_data['contrato'])); 
		$tipo_movimiento=json_decode(System::getInstance()->Decrypt($this->_data['tipo_movimiento']));
		$ID_TIPO_DOC=json_decode(System::getInstance()->Decrypt($this->_data['tipo_documento']));
		 
		if (!(validateField($contrato,"serie_contrato")&& validateField($contrato,"no_contrato") && validateField($this->_data,"motorizado")  && validateField($tipo_movimiento,"no_movimiento")&& validateField($ID_TIPO_DOC,"ID_TIPO_DOC")   
			)){
			$data=array("error"=>true,"mensaje"=>'La informaci??n proporcionada no esta completa!');
			return $data;
		
			exit; 
		}
		if (!validateField($this->_data,"list_forma_pago")){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;	
		}
		
		$forma_pago=$this->_data['list_forma_pago']; 
		
 		if (!is_array($forma_pago)){
			$data=array("error"=>true,"mensaje"=>'No ha seleccionado una forma de pago!');
			return $data; 
			exit;
		}	
	}
	
	/*GESTIONA LOS PAGOS AL CONTRATO, CUOTAS*/
	public function generarPagoToContrato(){  
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");	
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		$this->_data=$_REQUEST;  
		$this->validateDataToPay();
		$contrato=json_decode(System::getInstance()->Decrypt($this->_data['contrato'])); 
		$tipo_movimiento=json_decode(System::getInstance()->Decrypt($_REQUEST['doctype']));
		$ID_TIPO_DOC=json_decode(System::getInstance()->Decrypt($this->_data['tipo_documento'])); 
		$forma_pago=$this->getItem($this->_data['forma_pago_token']);
		 
		/*CARGO LA INFORMACION DEL CONTRATO*/
		$ct= new Contratos(UserAccess::getInstance()->getDBLink());
 		$info=$ct->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
		
		$tipo_documento=$ID_TIPO_DOC->ID_TIPO_DOC;
		
		$_AVICOB=Cobros::getInstance()->getAvisoCobroData($info->serie_contrato,$info->no_contrato);  
		/*VALIDO SI ES UN AVISO DE COBRO EL CUAL SERA FACTURADO*/
		if ($_AVICOB['cuotas_acobrar']==0){
			$no_documento=$this->_data['no_documento'];
			
			if (!(validateField($this->_data,"id_nit") &&   validateField($this->_data,"no_documento") )){
					
				$data=array("error"=>true,"mensaje"=>'La informaci??n proporcionada no esta completa!');
				return $data;
			
				exit;
			} 
		}else{
			$tipo_documento="TIP01"; //DOCUMENTO TIPO DE COBRO 
		}
		
		$MONTO_PAGO_CAJA=0;
		$f_pago=array();
		/*VALIDANDO LA FORMA DE PAGO*/
		foreach($forma_pago as $key =>$val){
			$fpago=new ObjectSQL();
			$fpago->setTable('forma_pago_contrato');  
			/*SI LA FORMA DE PAGO ES  2=> Tarjeta de credito*/
			if (($val['forma_pago']=="2") || ($val['forma_pago']=="3")){ 
				$banco=json_decode(System::getInstance()->Decrypt($val['banco']));	   
				if (!(validateField($val,"banco") 
					  && validateField($val,"autorizacion")
					   && validateField($banco,"ban_id") )){  
					$data=array("error"=>true,"mensaje"=>'La informaci??n proporcionada no esta completa!');
					 
					return $data; 
					exit;	
				}else{ 
					$fpago->ban_id=$banco->ban_id; 	
					$fpago->autorizacion=$val['autorizacion'];
				}
			}
			
			$fpago->fecha_pago="CONCAT(CURDATE(),' ',CURRENT_TIME())";	
		 	$fpago->forpago=$val['forma_pago']; 
			$fpago->tipo_cambio=$val['tipo_cambio'];
			$fpago->monto=$val['monto_a_pagar'];
			$fpago->EM_ID=$info->EM_ID;
			
			$tipo_cambio=$val['tipo_cambio'];
			$MONTO_PAGO_CAJA=$MONTO_PAGO_CAJA+$val['monto_a_pagar'];
			 
			array_push($f_pago,$fpago);
		}
	  
	
		$oficial_cobros=UserAccess::getInstance()->getUserData();
		   
  		print_r($tipo_movimiento);
		exit;
		$mov= new ObjectSQL();
		$mov->EM_ID=$info->EM_ID;
		$mov->no_contrato=$contrato->no_contrato;
		$mov->serie_contrato=$contrato->serie_contrato;
		$mov->fecha_movimiento="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$mov->tipo_cambio=$tipo_cambio;
		$mov->tipo_movimiento=$tipo_movimiento->no_movimiento;
		$mov->monto=$MONTO_PAGO_CAJA;
		$mov->monto_reserva=0;
		$mov->ID_TIPO_DOC=$tipo_documento;
		$mov->serie_factura=$this->_data['serie_factura'];
		$mov->no_factura=$this->_data['no_factura'];
		$mov->fecha_factura="STR_TO_DATE('".$this->_data['fecha_factura']."','%d-%m-%Y')";
		$mov->oficial_cobros=$oficial_cobros['email'];
		$mov->motorizado=$this->_data['motorizado'];										
		$mov->observaciones=$this->_data['observacion'];
		if ($tipo_movimiento->no_movimiento==11){
			
			$mov->no_cuota_pagada=($mov->monto/$info->cuota);
			$mov->intereses_pagados=((($info->interes*($info->cuotas/12)))/$info->cuotas)*$mov->no_cuota_pagada;
			$mov->capital_pagado=(($info->precio_neto)/$info->cuotas)*$mov->no_cuota_pagada;
			//$info->capital_pagado;
			
		}
		$mov->setTable('movimiento_contrato');
		$SQL=$mov->toSQL('insert');	 
 
	//	mysql_query($SQL);
		$documento=mysql_insert_id(UserAccess::getInstance()->getDBLink()->link_id);
    
	    $contrato= new ObjectSQL();
  		$contrato->capital_pagado="(capital_pagado+". $mov->capital_pagado .")";//PENDIENTE POR DEFINIR
		$contrato->intereses_pagados="(intereses_pagados+". $mov->intereses_pagados .")";; 
		//$contrato->impuesto_pagado=0; 
		  
		$SQL=$contrato->getSQL("update","contratos");
	 //	mysql_query($SQL); 
  
  
		$documento=$mov->NO_DOCUMENTO_CAJA;
  
		foreach($f_pago as $key =>$fpago){
			$fpago->no_documento=$documento;
			$fpago->serie_contrato=$mov->serie_contrato;
			$fpago->no_contrato=$mov->no_contrato; 
 			$SQL=$fpago->toSQL('insert'); 
		//	mysql_query($SQL);		
		}	 
 
   		
		exit;
		$data=array("error"=>false,"mensaje"=>'Pago realizado!');
			
		return $data;
	}	
 
	/*VERIFICA SI UN NUMERO DE RECIBO EXISTE EN CAJA*/
	public function checkExistRecibo($id_caja,$serie,$no_documento){
		$SQL="SELECT count(*) as tt FROM  `movimiento_caja` 
				WHERE `NO_DOCTO`='". mysql_real_escape_string($no_documento) ."' and
				`SERIE`='". mysql_real_escape_string($serie) ."'  and ID_CAJA='".$id_caja."'  ";
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['tt'];
	}
	 
	/*VERIFICA SI UN NUMERO DE DOCUMENTO EXISTE DENTRO DE LOS PAGOS DE CAJA*/
	public function checkExistPaymentOnCaja($tipo_doc,$serie,$no_documento){
		$SQL="SELECT count(*) as tt FROM  `movimiento_caja` 
				WHERE `NO_DOCTO`='". mysql_real_escape_string($no_documento) ."' and
				`SERIE`='". mysql_real_escape_string($serie) ."' and TIPO_DOC='".$tipo_doc."' ";
			 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['tt'];
	}
	
	/*VERIFICA SI UN NUMERO DE DOCUMENTO EXISTE DENTRO DE LOS PAGOS DE CONTRATOS*/
	public function checkExistPaymentOnContratos($no_documento){
		$SQL="SELECT count(*) as tt FROM  `movimiento_caja` 
				WHERE `NO_DOCUMENTO_CAJA`='". mysql_real_escape_string($no_documento) ."' ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['tt'];
	}	
	
	/* ME RETORNA EL MONTO ACUMULADO DE UNA RESERVA*/
	public function getMontoReservaFromCaja($no_reserva){
		$data=array(
					'monto'=>0,
					'no_reserva'=>$no_reserva,
					'code'=>'',
					'valid'=>true,
					'transapciones'=>''
				);
	//	$SQL="SELECT  SUM(MONTO) AS MONTO FROM movimiento_caja wHERE movimiento_caja.`NO_RESERVA`='".mysql_real_escape_string($no_reserva)."' ";	
		$SQL="SELECT  SUM(MONTO) AS MONTO,
					  SERIE,
					  NO_DOCTO,
					  TIPO_MOV 
		FROM movimiento_caja 
		WHERE 
			movimiento_caja.`NO_RESERVA`='".mysql_real_escape_string($no_reserva)."' AND INICIAL!='S'
		GROUP BY SERIE,NO_DOCTO,TIPO_MOV";
			 
		$rs=mysql_query($SQL); 
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){ 
			//$data['code']=System::getInstance()->Encrypt(json_encode($row));
			if (trim($row['MONTO'])!=""){
				$data['monto']=$data['monto']+$row['MONTO'];
			}
			$data['no_reserva']=$no_reserva; 
			$data['valid']=true;
			array_push($rt,$row);
		}
		$data['transapciones']=System::getInstance()->Encrypt(json_encode($rt));
		
		return $data;
	}
	
	/*OPTIENE UN NUMERO DE RESERVA DE UN PRODUCTO*/
	public function getNoReservaFromProduct($bloque,$lote,$id_fases,$id_jardin){
		$no=0;
		$SQL="SELECT  
				 reserva_inventario.`no_reserva`
			FROM `reserva_inventario` 
			INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
			INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
			INNER JOIN `inventario_jardines` ON (inventario_jardines.no_reserva=reserva_inventario.no_reserva)
			INNER JOIN `reserva_ubicaciones` ON (reserva_ubicaciones.`no_reserva`=reserva_inventario.`no_reserva`)
			WHERE 
				reserva_ubicaciones.`id_jardin`='".$id_jardin."' AND 
				reserva_ubicaciones.`id_fases`='".$id_fases."' AND 
				reserva_ubicaciones.`lote`='".$lote."' AND
				reserva_ubicaciones.`bloque`='".$bloque."'
			GROUP BY reserva_inventario.`no_reserva` ";
		 
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$no=$row['no_reserva'];	
		}
		return $no;
	}
	
	/* ME RETORNA EL MONTO ACUMULADO y PENDIENTE PARA COMPLETAR EL 10% DE UNA RESERVA*/
	public function getMontoReservaYpendiente($id_nit,$no_reserva){
		$monto_abono=$this->getMontoAbonoFromReserva($no_reserva,$id_nit);
		$por_monto=$this->getMontoPorcientoFromReserva($no_reserva);
   
		$data=array(
				'total_abonos'=>$monto_abono,
				'monto_total_pagar'=>$por_monto['MONTO_PORCENT'],
				'monto_pendiente'=>($por_monto['MONTO_PORCENT']-$monto_abono),
				'monto_minimo'=>$por_monto['MINIMO_RESERVA']
				);  
		 
		return $data;
	}
	
	/*OBTENER EL MONTO TOTAL DE LOS ABONOS A RESERVA DE UN CLIENTE*/
	public function getMontoPorcientoFromReserva($no_reserva){
		$SQL="SELECT (SUM(CAPITAL_TP)*(0.10)) AS MONTO_PORCENT,
(COUNT(CAPITAL_TP)*2000) AS MINIMO_RESERVA

 FROM `inventario_jardines` 
INNER JOIN  `jardines_activos` ON (inventario_jardines.`id_fases`=jardines_activos.id_fases AND 
				inventario_jardines.`id_jardin`=jardines_activos.id_jardin)
INNER JOIN tabla_precios ON (tabla_precios.`CODIGO_TP`=jardines_activos.`precio_venta_local_nec`)
WHERE inventario_jardines.no_reserva='".mysql_real_escape_string($no_reserva)."'";	
   
		$rs=mysql_query($SQL);
		$rt=0;
		$row=mysql_fetch_assoc($rs); 
		return $row;
	}	
	
	/*OBTENER EL MONTO TOTAL DE LOS ABONOS A RESERVA DE UN CLIENTE*/
	public function getMontoaPagarContrato($serie_contrato,$no_contrato){
		
		$SQL="SELECT valor_cuota,cuotas FROM `contratos` 
WHERE  contratos.`serie_contrato`='". mysql_real_escape_string($serie_contrato)."' AND contratos.`no_contrato`='". mysql_real_escape_string($no_contrato)."' ";	
   
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs); 
		$data=array(
				'valor_cuota'=>$row['valor_cuota'],
				'cuotas'=>$row['cuotas']
			);  
		return $data;
	}
 
	public function getPreviewMontoCierre($CAJA_ID){
		 $SQL="SELECT * FROM view_cierre_caja
		 INNER JOIN `formas_pago` ON (formas_pago.forpago=view_cierre_caja.forpago)
		  WHERE view_cierre_caja.`ID_CAJA`='".mysql_real_escape_string($CAJA_ID)."' 
		 AND  (SELECT COUNT(*) AS exist FROM cierre_caja WHERE 
		cierre_caja.ID_CAJA=view_cierre_caja.`ID_CAJA`
		AND PARCIAL_TOTAL='S' 
		AND CANTIDAD_DOC=view_cierre_caja.total
		AND TIPO_CAMBIO='1' 
		AND forpago=view_cierre_caja.forpago)=0 
		 ";	  
 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){	
			array_push($data,$row);
		} 
		return $data;
	}	
	 
	public function validIfExistCierreCaja($cc_obj){
		// AND FECHA=curdate()
		 $SQL="SELECT COUNT(*) AS exist FROM cierre_caja WHERE ID_CAJA='".$cc_obj->ID_CAJA."' AND PARCIAL_TOTAL='".$cc_obj->PARCIAL_TOTAL."' AND CANTIDAD_DOC='".$cc_obj->CANTIDAD_DOC."' AND TIPO_CAMBIO='".$cc_obj->TIPO_CAMBIO."' AND forpago='".$cc_obj->forpago."' AND MONTO='".$cc_obj->MONTO."' ";	  
	//	 echo $SQL."\n";
		$rs=mysql_query($SQL);
		$data=array();
		$row=mysql_fetch_assoc($rs);
		return $row['exist'];
	}
	
	/*Genera el Cierre total o parcial de una caja*/
	public function generarCierre($CAJA_ID,$TIPO_CIERRE){
		$data=$this->getPreviewMontoCierre($CAJA_ID);
		
		$rt=array("valid"=>false,"mensaje"=>"Error generando Cierre");
		
		switch($TIPO_CIERRE){
			case "S": 
				foreach($data as $key=>$preview){
					$obj= new ObjectSQL();
					$obj->FECHA="curdate()";
					$obj->ID_CAJA=$CAJA_ID;
					$obj->HORA="curtime()";
					$obj->forpago=$preview['forpago'];
					$obj->PARCIAL_TOTAL="S";
					$obj->CANTIDAD_DOC=$preview['total'];
					$obj->MONTO=$preview['MONTO'];
					$obj->TIPO_CAMBIO="1";
					$obj->setTable("cierre_caja");
					
					if (!$this->validIfExistCierreCaja($obj)>0){ 
						$SQL=$obj->toSQL("insert");
						mysql_query($SQL);
						$rt['valid']=true;
						$rt['mensaje']="Cierre parcial realizado!";
					}else{
						$rt['valid']=true;
						$rt['mensaje']="El cierre se encuentra procesado!";			
					}
					  
				}

			break;
			case "T": 
				foreach($data as $key=>$preview){
					$obj= new ObjectSQL();
					$obj->FECHA="curdate()";
					$obj->ID_CAJA=$CAJA_ID;
					$obj->HORA="curtime()";
					$obj->forpago=$preview['forpago'];
					$obj->PARCIAL_TOTAL="T";
					$obj->CANTIDAD_DOC=$preview['total'];
					$obj->MONTO=$preview['MONTO'];
					$obj->TIPO_CAMBIO="1";
					$obj->setTable("cierre_caja");
					
					if (!$this->validIfExistCierreCaja($obj)>0){ 
						$SQL=$obj->toSQL("insert");
						mysql_query($SQL);
						$rt['valid']=true;
						$rt['mensaje']="Cierre Total  realizado!";
					}else{
						$rt['valid']=true;
						$rt['mensaje']="El cierre se encuentra procesado!";			
					}	
				}
			break;
			
		}
		
		
		return $rt;
	}
	
	/*OPTENER LA CUOTA CORRESPONDIENTE DE UN CONTRATO*/
	public function getCuotaContratoActual($no_contrato,$serie_contrato){ 
		 $SQL="SELECT  SUM(movimiento_contrato.NO_CUOTA) AS NO_CUOTA  FROM `movimiento_caja`
INNER JOIN `movimiento_contrato` ON (movimiento_contrato.`NO_DOCTO`=movimiento_caja.NO_DOCTO AND movimiento_contrato.`CAJA_SERIE`=movimiento_caja.SERIE)
 WHERE movimiento_caja.`TIPO_MOV`='CUOTA'  AND  `NO_CONTRATO`='".$no_contrato."' AND 
`SERIE_CONTRATO` ='".$serie_contrato."' AND (movimiento_caja.`ANULADO` IS NULL OR movimiento_caja.`ANULADO`='N') ";	 
 
		$rs=mysql_query($SQL);
		$no_cuota=0;
		while($row=mysql_fetch_assoc($rs)){
			$no_cuota=$row['NO_CUOTA'];
		}
		return $no_cuota;
	}	
	/* OPTIENE EL PAGO ABONO A UN CLIENTE*/
	public function getAbonoCliente($id_nit){
		$SQL="SELECT 
					SUM(movimiento_caja.MONTO) AS MONTO 
				FROM `forma_pago_caja`
				INNER JOIN `movimiento_caja` ON (movimiento_caja.id_nit=forma_pago_caja.id_nit AND
				movimiento_caja.`TIPO_DOC`=forma_pago_caja.TIPO_DOC AND
				movimiento_caja.`NO_DOCTO`=forma_pago_caja.NO_DOCTO AND movimiento_caja.`SERIE`=forma_pago_caja.SERIE) 
				INNER JOIN `formas_pago` ON (formas_pago.`forpago`=forma_pago_caja.FORMA_PAGO)
			 WHERE 
			  movimiento_caja.id_nit='".$id_nit."' AND
			  movimiento_caja.NO_RESERVA=0 AND 
			  movimiento_caja.NO_CONTRATO ='' AND 
			  movimiento_caja.SERIE_CONTRATO='' AND 
			  movimiento_caja.INICIAL='N' ";
 
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$data=$row; 
		}
		return $data;
	}	

	public function getMovimientoCaja($serie,$no_doc,$tipo_mov=""){
		$SQL="SELECT * FROM `movimiento_caja` WHERE  movimiento_caja.SERIE='".mysql_real_escape_string($serie)."' and movimiento_caja.NO_DOCTO='".mysql_real_escape_string($no_doc)."'  ";
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);		
		return $row;
	}	
 	
		
	
	
}

?>
