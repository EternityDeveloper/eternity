<?php


class ChequesDevuelto{
	private $db_link;
	private $_data; 
	private static $instance;
	
	public function __construct($db_link=null){
		$this->db_link=$db_link;
	}
	public static function GI(){
		 if (!ChequesDevuelto::$instance instanceof self) {
             ChequesDevuelto::$instance = new ChequesDevuelto();
        }
        return ChequesDevuelto::$instance;
	}	
	public function getHeaderChequeDevuelto($id){
		$SQL="SELECT * FROM `caja_cheque_devuelto` AS cd where id='".$id."' ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs); 
		return $row;
	}	
	public function contieneChequeDevuelto($serie_contrato,$no_contrato){
		$SQL="SELECT COUNT(cd.ID) AS total FROM 
			`caja_cheque_devuelto` AS cd
			INNER JOIN `caja_cheque_devuelto_detalle` AS cdd ON (cdd.`id_caja_cheque_devuelto`=cd.id)
			 WHERE cd.ESTATUS='POR_REPONER' AND 
			 cdd.serie_contrato='".$serie_contrato."' 
			 AND cdd.no_contrato='".$no_contrato."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['total']==0){
			return false;
		}
		return true;
	}
	
	/*Permite registrar los cheques devueltos*/
	public function registrarChequeDevuelto($fecha_registro,$banco_debito,$comentario){
		$msg=array("mensaje"=>"Forma de pago no existe!","error"=>true); 

		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("caja","Caja");   
		SystemHtml::getInstance()->includeClass("caja","Recibos");  
		$con=new Contratos($this->db_link); 
		$caja= new Caja($this->db_link);  
					
		$id="put_cheques_devuelto";
		$rt = STCSession::GI()->getSubmit($id);  
		$error=false; 
		$detalle_chk=array();
		$numero_cheque="";
		
	
 		foreach($rt as $keys => $rw){
			foreach($rw as $kex => $item){ 
				$id="";
				/*VALIDO QUE EXISTA UN CONTRATO*/
				if (isset($item->contrato)){
					if (trim($item->contrato)==""){ 
						if (!isset($detalle_chk[$item->ID_NIT])){
							$id=$item->ID_NIT;
							$detalle_chk[$id]=array();
						} 
					}else{
						$id=$item->contrato;
						if (!isset($detalle_chk[$item->contrato])){	
							$detalle_chk[$id]=array();
						}					
					} 
				}else{
					$id=$item->contrato;
					if (!isset($detalle_chk[$item->contrato])){	
						$detalle_chk[$id]=array();
					}	
				}
		
				array_push($detalle_chk[$id],$item);  
				$SQL="SELECT COUNT(*) AS total  FROM `caja_cheque_devuelto` as  ccd
					INNER JOIN caja_cheque_devuelto_detalle AS cd ON  (cd.`id_caja_cheque_devuelto`=ccd.id)
					WHERE cd.`id_mov_fact`='".$item->ID_MOV_FACT."'   "; 
				$rs=mysql_query($SQL);
				while($row=mysql_fetch_assoc($rs)){ 
					if ($row['total']>0){
						$error=true;
					} 
				} 
				$numero_cheque=trim($item->AUTORIZACION);
 
			}
		}
		
		if ($error){
			$msg=array("mensaje"=>"Error, Este documento ya ha sido registrado ","error"=>true);
			return $msg;
		} 
		if (trim($numero_cheque)==""){
			$msg=array("mensaje"=>"Error, no existe documento! ","error"=>true);
			return $msg;
		}
		  
		$SQL="SELECT COUNT(*) AS TOTAL FROM `caja_cheque_devuelto` WHERE  NUMERO_CHEQUE ='".$numero_cheque."'  ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);  
 	
		if ($row['TOTAL']==0){

			/*SUMO EL VALOR DEL CHEQUE DEVUELTO*/
			foreach($detalle_chk as $key =>$ck){
				foreach($ck as $ky =>$dt){
					$monto_cheque=$monto_cheque+$dt->_MONTO;
				}
			}
 
			$obj=new ObjectSQL(); 
			$obj->MONTO=$monto_cheque;
			$obj->NUMERO_CHEQUE=$numero_cheque; 
			$obj->REGISTRADO_POR=UserAccess::getInstance()->getIDNIT();
			$obj->FECHA_REGISTRO=$fecha_registro;//"concat(curdate(),' ',CURTIME())";	 
			$obj->COMENTARIO=$comentario;
			$obj->BANCO_DEBITO=$banco_debito;
			$obj->setTable("caja_cheque_devuelto");
			$SQL=$obj->toSQL("insert"); 
			mysql_query($SQL);  
			$id_cheque = mysql_insert_id();
 
			$detalle_mov=array();
			/*DETALLE DEL CHEQUE DEVUELTO*/		 
		
			foreach($detalle_chk as $key =>$ck){ 
				foreach($ck as $ky =>$dt){ 
					$obj=new ObjectSQL(); 
					$obj->id_caja_cheque_devuelto=$id_cheque;
					$obj->id_nit_cliente=$dt->ID_NIT;
					$obj->serie_contrato=$dt->serie_contrato;
					$obj->no_contrato=$dt->no_contrato;
					$obj->id_mov_fact=$dt->ID_MOV_FACT;
					$obj->no_docto=$dt->RC_NO_DOCTO;
					$obj->serie=$dt->RC_SERIE;
					$obj->monto=$dt->_MONTO;
					$obj->id_caja=$dt->ID_CAJA; 
					$obj->setTable("caja_cheque_devuelto_detalle");
					$SQL=$obj->toSQL("insert"); 
					mysql_query($SQL);
					$id=$item->SERIE.$item->NO_DOCTO;
		
					if (!isset($detalle_mov[$id])){	
						$detalle_mov[$id]=array();
					}	  
					if ($dt->_MONTO<=0){
						if (isset($detalle_mov[$id])){	
							$rtl=array();
							foreach($detalle_mov[$id] as $k =>$mv){
								$rtl=$mv;
								$rtl['_MONTO']=$rtl['_MONTO']+$dt->_MONTO;  
								break;
							}
							$detalle_mov[$id][0]=$rtl; 
							
						}else{
							array_push($detalle_mov[$id],(array)$dt);
						} 
					}else{    
						array_push($detalle_mov[$id],(array)$dt);
					}
 
 
				}  
			} 
			
			/*PROCEDO A REGISTRAR LOS CAMBIOS EN MOVIMIENTO CONTRATOS*/
			foreach($detalle_mov as $key =>$ck){ 
				foreach($ck as $ky =>$dt){ 		
					$rl=new ObjectSQL();
					$rl->push($dt);
					$dt=$rl; 
					$cdata=$con->getInfoContrato($dt->serie_contrato,$dt->no_contrato);	
					//$tipo_cambio=$caja->getTasaActual($cdata->tipo_moneda);	
					$tipo_cambio=$dt->TIPO_CAMBIO;	  
					$recibos= new Recibos($this->db_link);   
					$filter=array("action"=>'getRecibo',
										"SERIE"=>$dt->SERIE,
										"NO_DOCTO"=>$dt->NO_DOCTO
										);
					$rc_detalle=$recibos->getListadoRecibo($filter);	
					if (count($rc_detalle)<=0){
						return $msg;
					}
					$rc_detalle=$rc_detalle[0];	
			 
					MVFactura::GI()->setFecha($fecha_registro);
					$DOCT=MVFactura::GI()->doCreateDocument($cdata->ID_NIT,
															$cdata->EM_ID,
															$dt->no_contrato,
															$dt->serie_contrato,
															'NCC',
															NOTA_CREDITO,
															0,
															0,
															round(($dt->_MONTO/$tipo_cambio),2),
															$tipo_cambio,
															0);	
								
					/*ASIGNANDO LA NOTA DE DEBITO AL DOCUMENTO*/									
					$obj= new ObjectSQL();
					$obj->CAJA_NO_DOCTO=$DOCT->NO_DOCTO;
					$obj->CAJA_SERIE=$DOCT->SERIE;
					$obj->ESTATUS=38;
					$obj->setTable("movimiento_factura");
					$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' and NO_DOCTO='".$DOCT->NO_DOCTO."'");
					mysql_query($SQL);	  
					/* ---------------------------------------- */
					
					$mov_contrato= new ObjectSQL();
					$mov_contrato->ID_NIT=$dt->ID_NIT;
					$mov_contrato->NO_DOCTO=$DOCT->NO_DOCTO;
					$mov_contrato->CAJA_SERIE=$DOCT->SERIE;
					$mov_contrato->FECHA=$fecha_registro; 
					$mov_contrato->EM_ID=$dt->ID_CAJA;
					$mov_contrato->TIPO_DOC=NOTA_CREDITO; //RECIBO DE CAJA
					$mov_contrato->TIPO_MOV='NCC'; //Nota de debito chueque
					$mov_contrato->MONTO_DOC=round($dt->_MONTO/$tipo_cambio,2);
					$mov_contrato->TOT_ABONOS=0;
					$mov_contrato->TOTAL_MOV=$mov_contrato->MONTO_DOC+$mov_contrato->TOT_ABONOS;
					$mov_contrato->CAPITAL_PAG='(0)';
					$mov_contrato->INTERESES_PAG='0';
					$mov_contrato->IMPUESTO_PAG='0';
					$mov_contrato->MORA_PAG='0';
					$mov_contrato->MANTENIMIENTO='0';
					$mov_contrato->INICIAL=0;
					$mov_contrato->NO_CUOTA='';
					$mov_contrato->CUOTA='';
					$mov_contrato->OF_COBROS=UserAccess::getInstance()->getID();
					$mov_contrato->MOTORIZADO='';
					$mov_contrato->OBSERVACIONES='NOTA DE CREDITO';
					$mov_contrato->TIPO_CAMBIO=$tipo_cambio;	
					$mov_contrato->setTable("movimiento_contrato");
					$SQL=$mov_contrato->toSQL("insert");   
					mysql_query($SQL); 
				}
			} 
			$msg=array("mensaje"=>"Cheque registrado!","error"=>false);	
			
		}else{
			$msg=array("mensaje"=>"Error, existe cheque devuelto regitrado!","error"=>true);	
		}
		return $msg;
	}
	
	/*Permite reponer un cheque devuelto*/
	public function reposicionChequeDevuelto($cheque_devuelto_id,
											 $banco_credito,
											 $fecha,
											 $token,
											 $id_nit,
											 $serie_contrato,
											 $no_contrato){
		$msg=array("mensaje"=>"Forma de pago no existe!","error"=>false); 
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
 
		$caja= new Caja($this->db_link);
		$list=$caja->getItem($token);  
		/*Cargo los recibos del carrito*/
		$listado=$caja->getListCarritoRecibo();
	 
		$monto_a_pagar=0;
		if (count($listado)>0){ 
			foreach($listado as $key =>$row){
				$monto_a_pagar=$monto_a_pagar+$row->MONTO_TOTAL;
			} 	 
		} 
		
		$monto_p_pagar=0;
			if (count($list)>0){ 
				foreach($list as $key=>$val){
					if (($val['forma_pago']=="TC") || ($val['forma_pago']=="CK") || ($val['forma_pago']=="DP")){   
						if (!(validateField($val,"banco") && validateField($val,"autorizacion"))){  
							$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!'); 
							return $data;  
						} 
					}			
					 
					$monto_p_pagar=$monto_p_pagar+($val['monto_a_pagar']*$val['tipo_cambio']); 
				}
			}
	 	 
		$SQL="SELECT cd.* FROM 
				caja_cheque_devuelto AS cd 
			WHERE cd.ESTATUS='POR_REPONER' AND cd.ID='".$cheque_devuelto_id."'  ";   
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){  
		
			$OCD=new ObjectSQL();
			$OCD->ESTATUS="REPUESTO"; //CAMBIO DE ESTATUS EL CHEQUE REPUESTO
			$OCD->FECHA_REPOSICION=$fecha;//"CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$OCD->REPOSICION_REGISTRADA_POR=UserAccess::getInstance()->getIDNIT();
			$OCD->BANCO_CREDITO=$banco_credito;
			$OCD->setTable("caja_cheque_devuelto"); 
			
			 
			if (round($monto_p_pagar,2)<round(($row['MONTO']+$monto_a_pagar),2)){
				$msg['mensaje']="El monto a pagar es menor que el monto a reponer!";
				$msg['error']=true;
				return $msg; 
			}

			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("caja","Caja");   
			$con=new Contratos($this->db_link); 
			$caja= new Caja($this->db_link);    
			$ID_CAJA=UserAccess::getInstance()->getCaja(); 
			
			$cdata=$con->getInfoContrato($serie_contrato,$no_contrato);	
			$tipo_cambio=$caja->getTasaActual($cdata->tipo_moneda);
			$forma_pago_array=array(); 
			MVFactura::GI()->setFecha($fecha);

			$doc=MVFactura::GI()->getNextDoct(NOTA_DEBITO);				 
			$movf= new ObjectSQL(); 
			$DOCT=MVFactura::GI()->createMovCaja($doc['CORRELATIVO'],
												  $doc['SERIE'],
												  $cdata->no_contrato,
												  $cdata->serie_contrato,								  
												  "", //ID_EMPRESA
												  $ID_CAJA['ID_CAJA'],
												  $ID_CAJA['id_usuario'],
												  $cdata->id_nit_cliente, 
												  NOTA_DEBITO, 
												  0,
												  0,
												  $row['MONTO']+$monto_a_pagar,
												  1, 
												  0,
												  0,
												  $comentario,
												  $comentario);					
													  			
			$SQL="SELECT cd.*,ROUND(SUM(monto),2) AS _monto FROM `caja_cheque_devuelto_detalle` AS cd WHERE 
 				`id_caja_cheque_devuelto`='".$cheque_devuelto_id."' 
				GROUP BY `id_nit_cliente`,`serie_contrato`,`no_contrato`  ";   
			$rsx=mysql_query($SQL);
			while($rowx=mysql_fetch_assoc($rsx)){ 
				$cdata=$con->getInfoContrato($rowx['serie_contrato'],$rowx['no_contrato']);	
				$tipo_cambio=$caja->getTasaActual($cdata->tipo_moneda);
				  
				$MF=MVFactura::GI()->movFactura($DOCT->NO_DOCTO,
											  $DOCT->SERIE,
											  $rowx['no_contrato'],
											  $rowx['serie_contrato'],								  
											  $DOCT->EM_ID,  
											  $DOCT->ID_NIT,
											  "NCD", 
											  0,
											  0,
											  $row['MONTO'],
											  1, 
											  38, //ESTATUS UTLIZADO
											  0);
											   
				/*ASIGNANDO LA NOTA DE DEBITO AL DOCUMENTO*/									
				$obj= new ObjectSQL();
				$obj->CAJA_NO_DOCTO=$DOCT->NO_DOCTO;
				$obj->CAJA_SERIE=$DOCT->SERIE;
				$obj->ESTATUS=38;
				$obj->setTable("movimiento_factura");
				$SQL=$obj->toSQL("update"," where SERIE='".$DOCT->SERIE."' 
													and NO_DOCTO='".$DOCT->NO_DOCTO."'");
				mysql_query($SQL);	 
				/* ---------------------------------------- */
				/*ASIGNO LOS RECIBOS*/
				foreach($listado as $key =>$rcbl){ 
					MVFactura::getInstance()->matarRecibos($DOCT->NO_DOCTO,
															 $DOCT->SERIE,
															 $DOCT->TIPO_DOC,
															 $rcbl->SERIE,
															 $rcbl->NO_DOCTO); 				
		 
				} 	

				$cajero=UserAccess::getInstance()->getCaja();  
				$mov_contrato= new ObjectSQL();
				$mov_contrato->ID_MOV_FACT=$MF->ID_MOV_FACT;
				$mov_contrato->ID_NIT=$rowx['id_nit_cliente'];
				$mov_contrato->NO_DOCTO=$DOCT->NO_DOCTO;
				$mov_contrato->CAJA_SERIE=$DOCT->SERIE;
				$mov_contrato->FECHA=$fecha;//'curdate()'; 
				$mov_contrato->EM_ID=$ID_CAJA['ID_CAJA'];
				$mov_contrato->TIPO_DOC=NOTA_DEBITO;
				$mov_contrato->TIPO_MOV='NCD'; //Nota de debito cheque
				$mov_contrato->MONTO_DOC=$row['MONTO']/$tipo_cambio;
				$mov_contrato->TOT_ABONOS=0;
				$mov_contrato->TOTAL_MOV=$row['MONTO']/$tipo_cambio;
				$mov_contrato->CAPITAL_PAG='(0)';
				$mov_contrato->INTERESES_PAG='0';
				$mov_contrato->IMPUESTO_PAG='0';
				$mov_contrato->MORA_PAG='0';
				$mov_contrato->MANTENIMIENTO='0';
				$mov_contrato->INICIAL=0;
				$mov_contrato->NO_CUOTA='';
				$mov_contrato->CUOTA='';
				$mov_contrato->OF_COBROS=UserAccess::getInstance()->getID();
				$mov_contrato->MOTORIZADO='';
				$mov_contrato->OBSERVACIONES='NOTA DE CREDITO';
				$mov_contrato->TIPO_CAMBIO=$tipo_cambio;	
				$mov_contrato->setTable("movimiento_contrato");
				$SQL=$mov_contrato->toSQL("insert");   
				mysql_query($SQL);					
															  					
			} 
			/*VALIDANDO LA FORMA DE PAGO*/
			foreach($list as $key =>$val){ 
				
				$fpago=new ObjectSQL();
				$fpago->setTable('forma_pago_caja');  
				/*SI LA FORMA DE PAGO ES => Tarjeta de credito*/
				if (($val['forma_pago']=="TC") || ($val['forma_pago']=="CK") || ($val['forma_pago']=="DP")){ 
					$banco=json_decode(System::getInstance()->Decrypt($val['banco']));	  
					if (!(validateField($val,"banco") 
						  && validateField($val,"autorizacion")
						   && validateField($banco,"ban_id") )){  
						$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!'); 
						return $data;  
					}else{  
						$fpago->ID_BANCO=$banco->ban_id; 	
					}
				}
		
				$fpago->FORMA_PAGO=$val['forma_pago'];
				$fpago->AUTORIZACION=$val['autorizacion'];  
				$fpago->TIPO_CAMBIO=$val['tipo_cambio'];
				$fpago->MONTO=$val['monto_a_pagar'];
				$fpago->FECHA="curdate()";
				$fpago->TIPO_DOC=NOTA_CREDITO;
				$fpago->NO_DOCTO=$DOCT->NO_DOCTO;
				$fpago->SERIE=$DOCT->SERIE;
				$fpago->EM_ID=$EM_ID;
				$fpago->ID_CAJA=$cajero['ID_CAJA'];
				$fpago->ID_NIT=$id_nit;
				$fpago->RC_NO_DOCTO=$DOCT->NO_DOCTO;
				$fpago->RC_SERIE=$DOCT->SERIE;		
				$SQL=$fpago->toSQL('insert');  
				mysql_query($SQL);	
				$tipo_cambio=$val['tipo_cambio'];
				$MONTO_PAGO_CAJA=$MONTO_PAGO_CAJA+$fpago->MONTO;   
 
				array_push($forma_pago_array,$fpago); 
			 	SysLog::getInstance()->Log($row['ID_NIT'], 
										 $row['SERIE_CONTRATO'],
										 $row['NO_CONTRATO'],
										 0,
										 0,
										 "TRANSACCION FORMA PAGO NOTA DE CREDITO CHEQUE DEVUELTO",
										 json_encode($fpago),
										 'FORMA_PAGO');  	
			}  
 
			/*ACTUALIZO EL ESTATUS DEL CHEQUE EN REPOSICION*/
			$SQL=$OCD->toSQL("update"," where ID='".$row['ID']."'");   
			mysql_query($SQL);			
			$msg=array("mensaje"=>"Cheque repuesto!","error"=>false);	
			
		} 
		return $msg;
	}
	/*OPTIENE EL LISTADO DE FORMA DE PAGOS DE LOS PARA SER FILTRADOS POR EL NUMERO DE CHQUE*/
	public function getListFormaPagoCheque($_DATA=array()){
		
		$str="";
		if (validateField($_DATA,'sSearch')){
			$str=" AND autorizacion='".mysql_escape_string($_DATA['sSearch'])."' ";	
		}
   
		$SQL="SELECT  COUNT(*) AS total
			FROM  `forma_pago_caja`
			INNER JOIN `movimiento_factura` ON (movimiento_factura.CAJA_NO_DOCTO=forma_pago_caja.RC_NO_DOCTO 
			AND movimiento_factura.CAJA_SERIE=forma_pago_caja.RC_SERIE)
			INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO)
			LEFT JOIN `bancos` ON (`bancos`.ban_id=forma_pago_caja.ID_BANCO)
		WHERE 
			forma_pago_caja.FORMA_PAGO='CK' ";
		
		if (trim($str)!=""){
			$SQL.=$str;	
		}	 
		$SQL.=" GROUP BY movimiento_factura.no_contrato,movimiento_factura.serie_contrato";
		
		$SQL.=$QUERY; 
	$rs=mysql_query($SQL);
	$row=mysql_fetch_assoc($rs);
	$total_row=$row['total'];
	
		$SQL="SELECT forma_pago_caja.*,
			movimiento_factura.serie_contrato,
			movimiento_factura.no_contrato,
			CONCAT(movimiento_factura.serie_contrato,' ',movimiento_factura.no_contrato) AS contrato,
			CONCAT(movimiento_factura.CAJA_SERIE,' ',movimiento_factura.CAJA_NO_DOCTO) doc_id,
			forma_pago_caja.FECHA,
			forma_pago_caja.AUTORIZACION,
			(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS _MONTO,
			(movimiento_factura.MONTO*movimiento_factura.TIPO_CAMBIO) AS MF_MONTO,
			movimiento_factura.ID_MOV_FACT,
			bancos.ban_descripcion AS banco
		FROM  `forma_pago_caja`
		INNER JOIN `movimiento_factura` ON (movimiento_factura.CAJA_NO_DOCTO=forma_pago_caja.RC_NO_DOCTO 
		AND movimiento_factura.CAJA_SERIE=forma_pago_caja.RC_SERIE)
		INNER JOIN `formas_pago` ON (formas_pago.forpago=forma_pago_caja.FORMA_PAGO)
		LEFT JOIN `bancos` ON (`bancos`.ban_id=forma_pago_caja.ID_BANCO)
		WHERE forma_pago_caja.FORMA_PAGO='CK' 
		";
		//AND autorizacion='1735'
		if (trim($str)!=""){
			$SQL.=$str;	
		}	
		
		$SQL." GROUP BY movimiento_factura.no_contrato,movimiento_factura.serie_contrato";

		$SQL.="  LIMIT ". mysql_real_escape_string($_DATA['iDisplayStart']).",".mysql_real_escape_string($_DATA['iDisplayLength']).""; 
		$data=array(
			'sEcho'=>$_DATA['sEcho'],
			'iTotalRecords'=>10,
			'iTotalDisplayRecords'=>$total_row,
			'aaData' =>array()
		);
		
		if ((!validateField($_DATA,"iDisplayStart")) && (!validateField($_DATA,"iDisplayLength"))){ 
			echo json_encode($data);
			exit;
		} 
		$rs=mysql_query($SQL);
		$result=array();  
		while($row=mysql_fetch_assoc($rs)){	
			$id=System::getInstance()->Encrypt(json_encode($row));
			$row['_MONTO']=number_format($row['_MONTO'],2); 
			$row['url']='<a href="#" id="'.$id.'" class="_do_agregar">AGREGAR</a>';
			array_push($data['aaData'],$row);
		}  
		return $data;		
	}
	
 	public function agregar_cheque($item){
		$msg=array("mensaje"=>"No se pudo agregar al listado","error"=>true);
		$id="put_cheques_devuelto";
		$rt = STCSession::GI()->getSubmit($id);  
		if ((validateField($item,"AUTORIZACION")) && (validateField($item,"ID_MOV_FACT"))){
			
			$SQL="SELECT COUNT(*) AS total 
				FROM 
					`caja_cheque_devuelto` as  ccd
				INNER JOIN caja_cheque_devuelto_detalle AS cd ON  (cd.`id_caja_cheque_devuelto`=ccd.id)
				WHERE cd.`id_mov_fact`='".$item->ID_MOV_FACT."'   "; 
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){ 
				if ($row['total']>0){
					$msg=array("mensaje"=>"Error, Este documento ya ha sido registrado ","error"=>true);
				}else{ 
					if (count($rt)>0){ 
						if (!isset($rt[$item->AUTORIZACION])){
							$msg=array("mensaje"=>"Error, debe de ser de un mismo numero de autorización de cheque!","error"=>true);
						}else{
							$rt[$item->AUTORIZACION][$item->ID_MOV_FACT]=$item;
							STCSession::GI()->setSubmit($id,$rt);
							$msg=array("mensaje"=>"Agregado","error"=>false);	
						}
					}else{  
						$rt[$item->AUTORIZACION][$item->ID_MOV_FACT]=$item;
						STCSession::GI()->setSubmit($id,$rt);
						$msg=array("mensaje"=>"Agregado","error"=>false);
					} 
				} 
			} 
		}
		return $msg;
	}
	/*LISTADO DE ITEMS AGREGADO AL CARRITO*/
	public function getListItemAdded(){
		$id="put_cheques_devuelto";
		$rt = STCSession::GI()->getSubmit($id);  
		return $rt;
	}
}
	
?>