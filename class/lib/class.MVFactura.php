<?php
/*
	SE ENCARGA DE REGISTRAR LA FACTURA
*/

class MVFactura{
	private static $_db = null;
	private static $instance;
	private $fecha="";
	public static function GI(){
		 if (!MVFactura::$instance instanceof self) {
             MVFactura::$instance = new MVFactura();
        }
        return MVFactura::$instance;
	}
	public static function getInstance(){
		 if (!MVFactura::$instance instanceof self) {
             MVFactura::$instance = new MVFactura();
        }
        return MVFactura::$instance;
	}
	
	public function __construct($db_class){
		if ($db_class!=""){
			self::$_db=$db_class;
		}
		MVFactura::$instance=$this;
	}	
	public function setFecha($fecha){
		$this->fecha=$fecha;
	}
	/*CREA EL MOVIMIENTO DE LA RESERVA*/
	public function doMovReserva($id_nit,
								$id_reserva,
								$no_reserva,
								$monto){
									
		/*
			RECIBO DE COBROS VIRTUAL
		*/							
		$doc=$this->getNextDoct(RECIBO_VIRTUAL);
		
		$movf= new ObjectSQL();
		$ID_CAJA=UserAccess::getInstance()->getCaja();
		
 
		$DOC_GEN=$this->createMovCaja($doc['CORRELATIVO'],
							  $doc['SERIE'],
							  "",
							  "",								  
							  "", //ID_EMPRESA
							  $ID_CAJA['ID_CAJA'],
							  $ID_CAJA['id_usuario'],
							  $id_nit,
							  "", 
							  $id_reserva,
							  $no_reserva,
							  $monto,
							  1, 
							  0,
							  0,
							  "",
							  "GENERANDO DOCUMENTO PARA ABONO A RESERVA");
 
	 			  
	 
		$this->movFactura($DOC_GEN->NO_DOCTO,
						  $DOC_GEN->SERIE,
						  "",
						  "",								  
						  $DOC_GEN->EM_ID,  
						  $DOC_GEN->ID_NIT,
						  "RES", 
						  $id_reserva,
						  $no_reserva,
						  $monto,
						  1, 
						  36, //ESTATUS
						  0);	
	

		
		return $movf;
	}

	/*GENERA LOS RECIBOS DEL SISTEMA*/
	public function movFactura($no_docto,
								  $serie_doct,
								  $no_contrato,
								  $serie_contrato,								  
								  $em_id,  
								  $id_nit,
								  $tipo_mov, 
								  $id_reserva,
								  $no_reserva,
								  $monto,
								  $tipo_cambio, 
								  $estatus=36, //PAGO RECIBIDO
								  $nit_motorizado,
								  $no_cuota=0,
								  $id_planilla_gestion=0,
								  $no_servicio_prestado="",
								  $sp_id_producto="",
								  $cantidad=0, //CANTIDAD DE PRODUCTO/ SERVICIO
								  $precio=0, //PRECIO DEL PRODUCTO/SERVICIO
								  $reporte_venta="",
								  $recibo_venta="",
								  $codigo_asesor="",
								  $id_nit_asesor="",
								  $fecha_doc="", 
								  $id_oficial="",
								  $interes=0,
								  $capital=0
								  ){
									  
		/*PROCESO DE MOVIMIENTO FACTURA*/
		$movf= new ObjectSQL();
		$movf->EM_ID=$em_id;
		if ($this->fecha==""){
			$movf->FECHA="CONCAT(CURDATE(),' ',CURRENT_TIME())";  
		}else{
			$movf->FECHA=$this->fecha;  
		}
		$movf->SERIE=$serie_doct;
		$movf->NO_DOCTO=$no_docto;
		$movf->id_usuario=UserAccess::getInstance()->getID();
		$movf->ID_NIT=$id_nit;
		$movf->TIPO_MOV=$tipo_mov;
		$movf->MONTO=$monto; 
		$movf->TIPO_CAMBIO=$tipo_cambio;
		$movf->ID_RESERVA=$id_reserva;
                if (!$no_reserva){
                    $movf->NO_RESERVA=0;
                } else { 
		    $movf->NO_RESERVA=$no_reserva;
                }
		$movf->NO_CONTRATO=$no_contrato;
		$movf->SERIE_CONTRATO=$serie_contrato;		
		$movf->MONTO = $monto;
		$movf->ESTATUS=$estatus;
		$movf->ID_NIT_MOTORIZADO=$nit_motorizado;
		$movf->ID_NIT_OFICIAL=$id_oficial==""?UserAccess::getInstance()->getIDNIT():$id_oficial;
		$movf->NO_CUOTA=$no_cuota;
		$movf->SOLICITUD_GESTION_ID=$id_planilla_gestion;
		$movf->NO_SERVICIO_PRESTADO=$no_servicio_prestado;
		$movf->ID_PRODUCTO=$sp_id_producto;
		$movf->CANTIDAD=$cantidad;
		$movf->PRECIO=$precio;		
		$movf->REPORTE_VENTA=$reporte_venta;	
		$movf->RECIBO_VENTA=$recibo_venta;	
		$movf->CODIGO_ASESOR=$codigo_asesor;	
		$movf->ID_NIT_ASESOR=$id_nit_asesor;
		$movf->CAPITAL_PAG=$capital;
		$movf->INTERES_PAG=$interes;							
		$movf->setTable("movimiento_factura");
		$SQL=$movf->toSQL("insert");  
 		mysql_query($SQL);

             /*   $kmov = new ObjectSQL();
                $kmov->texto = $SQL;
                $kmov->setTable("prueba");
                $kSQL=$kmov->toSQL("insert");
                mysql_query($kSQL); */  

    
		return $movf;	 						 
	}

	public function getNextDoct($tipo_doc){
		 
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
			
	public function createMovCaja($no_docto,
								  $serie_doct,
								  $no_contrato,
								  $serie_contrato,								  
								  $em_id,
								  $id_caja,
								  $id_usuario,
								  $id_nit,
								  $tipo_doc, 
								  $id_reserva,
								  $no_reserva,
								  $monto,
								  $tipo_cambio, 
								  $descuento,
								  $is_inicial,
								  $observaciones,
								  $log_descripcion,
								  $fecha_doc=""){

		$mov = new ObjectSQL();
		$mov->NO_DOCTO=$no_docto;
		$mov->SERIE=$serie_doct;		
		$mov->EM_ID=$em_id;  
		$mov->ID_CAJA=$id_caja;  
		$mov->id_usuario=$id_usuario;
		$mov->ID_NIT=$id_nit;
		$mov->TIPO_DOC=$tipo_doc;  
	 
		if (trim($this->fecha)==""){
			$mov->FECHA=$fecha_doc==""?"CONCAT(CURDATE(),' ',CURRENT_TIME())":$fecha_doc; 
			$mov->FECHA_DOC=$fecha_doc==""?"CONCAT(CURDATE(),' ',CURRENT_TIME())":$fecha_doc;
		}else{
			$mov->FECHA=$this->fecha;  
			$mov->FECHA_DOC=$this->fecha;  
		}	 
		$mov->ID_RESERVA=$id_reserva;
                if (!$no_reserva) {
                   $mov->NO_RESERVA=0;
                } else { 
		      $mov->NO_RESERVA=$no_reserva;
                }		 
		$mov->MONTO=$monto;
		$mov->TIPO_CAMBIO=$tipo_cambio; 
 		$mov->DESCUENTO=$descuento;
		$mov->INICIAL=$is_inicial==1?'S':'N'; 
		$mov->NO_CONTRATO=$no_contrato;
		$mov->SERIE_CONTRATO=$serie_contrato; 
  		$mov->OBSERVACIONES=$observaciones; 
		$mov->TIPO_MONEDA=$tipo_cambio>1?'DOLARES':'LOCAL';
		$mov->setTable('movimiento_caja'); 
		$SQL=$mov->toSQL('insert');	
	 	mysql_query($SQL);


             /*   $kmov = new ObjectSQL();
                $kmov->texto=$SQL;
                $kmov->setTable('prueba');
                $kSQL=$kmov->toSQL('insert');
                mysql_query($kSQL); */ 
	  
	 
 		SysLog::getInstance()->Log($mov->ID_NIT, 
									 $mov->SERIE_CONTRATO,
									 $mov->NO_CONTRATO,
									 $mov->NO_RESERVA,
									 $mov->ID_RESERVA,
									 $log_descripcion,
									 json_encode($mov));
		return $mov;	
									 
	} 
	public function validRecibo($serie,$no_docto){
		$SQL="SELECT COUNT(*) AS total FROM `movimiento_caja` WHERE 
				no_docto='". mysql_real_escape_string($no_docto)."' AND SERIE='". mysql_real_escape_string($serie)."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['total'];
	}
	/*
		CREA EL RECIBO
	*/
	public function doCreateDocument($id_nit,
									$em_id,
									$no_contrato,
									$serie_contrato,
									$tipo_mov,
									$tipo_doc,
									$id_reserva,
									$no_reserva,
									$monto,
									$tipo_cambio,
									$descuento, 
									$observaciones="",
									$log_descripcion="",
									$id_nit_motorizado="",
									$no_cuota=0,
									$id_planilla_gestion=0, //ID DE PLANILLA DE GESTION
									$no_servicio_prestado="",
									$sp_id_producto="",
									$cantidad=0, //CANTIDAD DE PRODUCTO/ SERVICIO
  									$precio=0, //PRECIO DEL PRODUCTO/SERVICIO 
									$reporte_venta="",
									$recibo_venta="",
									$codigo_asesor="",
									$id_nit_asesor="",
									$numero_documento=0, //EN CASO DE LOS RECIBOS MANUALES,
									$identificar_recibos=0, //EN CASO DE QUE LA CUOTA A PAGAR ES DIFERENTE AL SISTEMA
									$id_nit_oficial=0
									){
	 	
		
		$doc=array(); 
		if ($numero_documento==0){
			$doc=$this->getNextDoct($tipo_doc);
		}else{
			$doc['CORRELATIVO']=$numero_documento;
			$doc['SERIE']=$tipo_doc;	 
			if ($this->validRecibo($doc['SERIE'],$doc['CORRELATIVO'])>0){
				$DOC_GEN=array("valid"=>false,"mensaje"=>"El numero de recibo existe, el sistema no acepta duplicados!");
				return $DOC_GEN;
			}
		} 
		$movf= new ObjectSQL();
		$ID_CAJA=UserAccess::getInstance()->getCaja();
		
		$is_inicial=0;	
		if ($tipo_mov=="INI"){
			$is_inicial=1;	
		} 
		$DOC_GEN=$this->createMovCaja($doc['CORRELATIVO'],
									  $doc['SERIE'],
									  $no_contrato,
									  $serie_contrato,								  
									  $em_id, //ID_EMPRESA
									  $ID_CAJA['ID_CAJA'],
									  $ID_CAJA['id_usuario'],
									  $id_nit, 
									  $tipo_doc, 
									  $id_reserva,
									  $no_reserva,
									  $monto,
									  $tipo_cambio, 
									  $descuento,
									  $is_inicial,
									  $observaciones,
									  $log_descripcion);
 
		/*
			EN CASO DE LOS PAGOS A CONTRATOS QUE NO HAYAN 
			PAGADOS
		*/
		if ($identificar_recibos>0){
			$mci = new ObjectSQL();
			$mci->SERIE=$doc['SERIE'];
			$mci->NO_DOCTO=$doc['CORRELATIVO'];
			$mci->setTable('movimiento_caja_idenficar'); 
			$SQL=$mci->toSQL('insert');	
			mysql_query($SQL);			
		} 			  
	  
		$this->movFactura($DOC_GEN->NO_DOCTO,
						  $DOC_GEN->SERIE,
						  $no_contrato,
						  $serie_contrato,								  
						  $DOC_GEN->EM_ID,  
						  $DOC_GEN->ID_NIT,
						  $tipo_mov, 
						  $id_reserva,
						  $no_reserva,
						  $monto,
						  $tipo_cambio, 
						  36, //ESTATUS DISPONIBLE
						  $id_nit_motorizado,
						  $no_cuota,
						  $id_planilla_gestion,
						  $no_servicio_prestado,
						  $sp_id_producto,
						  $cantidad,
						  $precio,
						  $reporte_venta,
						  $recibo_venta,
						  $codigo_asesor,
						  $id_nit_asesor,
						  "",
						  $id_nit_oficial);	 


		$DOC_GEN->valid=true;	
		$DOC_GEN->mensaje="Recibo generado";					  
		return $DOC_GEN;
	}
	
	public function doCreateNOTADB($id_nit,
									$em_id,
									$no_contrato,
									$serie_contrato,
									$tipo_mov,
									$tipo_doc,
									$monto,
									$tipo_cambio,
									$observaciones="",
									$log_descripcion=""
									){
	 	
		
		$doc=$this->getNextDoct($tipo_doc);
		
		$movf= new ObjectSQL();
		$ID_CAJA=UserAccess::getInstance()->getCaja();
		
		$is_inicial=0;	
		if ($tipo_mov=="INI"){
			$is_inicial=1;	
		} 
		$DOC_GEN=$this->createMovCaja($doc['CORRELATIVO'],
									  $doc['SERIE'],
									  $no_contrato,
									  $serie_contrato,								  
									  $em_id, //ID_EMPRESA
									  $ID_CAJA['ID_CAJA'],
									  $ID_CAJA['id_usuario'],
									  $id_nit, 
									  $tipo_doc, 
									  0,
									  0,
									  $monto,
									  $tipo_cambio, 
									  0,
									  $is_inicial,
									  $observaciones,
									  $log_descripcion);
 
	 			  
	  
		$this->movFactura($DOC_GEN->NO_DOCTO,
						  $DOC_GEN->SERIE,
						  $no_contrato,
						  $serie_contrato,								  
						  $DOC_GEN->EM_ID,  
						  $DOC_GEN->ID_NIT,
						  $tipo_mov, 
						  0,
						  0,
						  $monto,
						  $tipo_cambio, 
						  36, //ESTATUS DISPONIBLE
						  0,
						  0,
						  0,
						  0,
						  0,
						  0,
						  0,
						  0,
						  0,
						  0,
						  0);	 

		
	
		$mfact = new ObjectSQL();
		$mfact->CAJA_SERIE=$DOC_GEN->SERIE;
		$mfact->CAJA_NO_DOCTO=$DOC_GEN->NO_DOCTO;					
		$mfact->setTable('movimiento_factura'); 
		$SQL=$mfact->toSQL('update'," WHERE `ESTATUS`='36' AND SERIE='".$DOC_GEN->SERIE."' 
											AND NO_DOCTO='".$DOC_GEN->NO_DOCTO."' ");	
		mysql_query($SQL);
		
		$fp = new ObjectSQL();
		$fp->SERIE=$DOC_GEN->SERIE;
		$fp->NO_DOCTO=$DOC_GEN->NO_DOCTO;		
		$fp->setTable('forma_pago_caja'); 
		$SQL=$fp->toSQL('update'," WHERE SERIE='".$DOC_GEN->SERIE."' 
							AND NO_DOCTO='".$DOC_GEN->NO_DOCTO."' ");	
		mysql_query($SQL);

		$DOC_GEN->valid=true;	
		$DOC_GEN->mensaje="Recibo generado";					  
		return $DOC_GEN;
	}		
	
/*
		CREA EL RECIBO DE UN REQUERIMIENTO DADO
	*/
	public function doCreateReciboRequerimiento($id_nit,
												$em_id,
												$no_contrato,
												$serie_contrato,
												$tipo_mov,
												$tipo_doc,    
												$id_nit_motorizado,
												$id_nit_oficial,
												$monto_total,
												$cantidad_cuota,
												$fecha_requerimiento,
												$observaciones="",
												$tipo_cambio=1,
												$identificar_recibos=0,
												$SOLICITUD_GESTION_ID=0,
												$numero_documento=0//EN CASO DE SER UN REQUERIMIENTO MANUAL
												){
		 						 
		SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 
		SystemHtml::getInstance()->includeClass("contratos","Contratos");
		 
		$con=new Contratos($this->db_link); 
			 		
		$OBTMOV=new CTipoMovimiento($this->db_link); 
		$TMO=$OBTMOV->getTIPO_MOV($tipo_mov);
		$log_descripcion="RECIBO PARA COBRO CUOTA ".$TMO['DESCRIPCION'];	
		 			 
		$d_contrato=$con->getInfoContrato($serie_contrato,$no_contrato);
		$interes_a_pagar=$d_contrato->interes/$d_contrato->cuotas;
		$cuota_a_pagar_sin_interes=$d_contrato->valor_cuota-$interes_a_pagar;
 		$valor_cuota=round($interes_a_pagar+$cuota_a_pagar_sin_interes,2);	
		$penalizacion=$d_contrato->monto_penalizacion;
		$monto_sin_cuota=round($monto_total-$valor_cuota,2);  
		$diferencia=round($monto_sin_cuota-$penalizacion,2);
				
		if (round(($d_contrato->valor_cuota+$d_contrato->monto_penalizacion),2)>round($monto_total,2)){
			$cuota_a_pagar_sin_interes=($cuota_a_pagar_sin_interes/($d_contrato->valor_cuota+$d_contrato->monto_penalizacion))*$monto_total;
			$interes_a_pagar=($interes_a_pagar/($d_contrato->valor_cuota+$d_contrato->monto_penalizacion))*$monto_total;
			
			$valor_cuota=round($interes_a_pagar+$cuota_a_pagar_sin_interes,2); 
			
			$penalizacion=round(($d_contrato->monto_penalizacion/($d_contrato->valor_cuota+$d_contrato->monto_penalizacion))*$monto_total,2); 
			
			$diferencia=0;			
		} 
 
		$doc=array(); 
		if ($numero_documento==0){
			$doc=$this->getNextDoct($tipo_doc); 
		}else{
			$doc['CORRELATIVO']=$numero_documento;
			$doc['SERIE']=$tipo_doc;	 
			if ($this->validRecibo($doc['SERIE'],$doc['CORRELATIVO'])>0){
				$DOC_GEN=array("valid"=>false,"mensaje"=>"El numero de recibo existe, el sistema no acepta duplicados!");
				return $DOC_GEN;
			}
		} 
		/* RECIBO DE COBROS VIRTUAL */							
		//$doc=$this->getNextDoct($tipo_doc); 

		$movf= new ObjectSQL();
 		$ID_CAJA=UserAccess::getInstance()->getCaja();

		$is_inicial=0;	 
	
		$DOC_GEN=$this->createMovCaja($doc['CORRELATIVO'],
									  $doc['SERIE'],
									  $no_contrato,
									  $serie_contrato,								  
									  $em_id, //ID_EMPRESA
									  $ID_CAJA['ID_CAJA'],
									  $ID_CAJA['id_usuario'],
									  $id_nit, 
									  $tipo_doc, 
									  '',
									  '',
									  round($monto_total,2),
									  $tipo_cambio, 
									  0,
									  0,
									  $observaciones,
									  $log_descripcion,
									  $fecha_requerimiento); 
			 
		$movf=$this->movFactura($DOC_GEN->NO_DOCTO,
									  $DOC_GEN->SERIE,
									  $no_contrato,
									  $serie_contrato,								  
									  $DOC_GEN->EM_ID,  
									  $DOC_GEN->ID_NIT,
									  $tipo_mov, 
									  '',
									  '',
									  round($valor_cuota,2),
									  $tipo_cambio, 
									  36, //ESTATUS
									  $id_nit_motorizado,
									  $cantidad_cuota,
									  $SOLICITUD_GESTION_ID,
										"",
										"",
										0, //CANTIDAD DE PRODUCTO/ SERVICIO
										0, //PRECIO DEL PRODUCTO/SERVICIO
										"",
										"",
										"",
										"",
										$fecha_requerimiento,
										$id_nit_oficial,
										$interes_a_pagar,
										$cuota_a_pagar_sin_interes);	

		/*GENERO UN DOCUMENTO PARA PENALIZACION*/								
		if ($penalizacion>0){
			$con->setFecha($this->fecha);
		//	$id_planilla_gestion=$con->doCSolicitudAbonoSaldo($serie_contrato,$no_contrato,$diferencia,$observaciones);
			$this->movFactura($DOC_GEN->NO_DOCTO,
								$DOC_GEN->SERIE,
								$no_contrato,
								$serie_contrato,								  
								$DOC_GEN->EM_ID,  
								$DOC_GEN->ID_NIT,
								'PREACT', //PAGO POR PENALIZACION
								'',
								'',
								round($penalizacion,2),
								$tipo_cambio, 
								36, //ESTATUS
								$id_nit_motorizado,
								$cantidad_cuota,
								0,
								"",
								"",
								0, //CANTIDAD DE PRODUCTO/ SERVICIO
								0, //PRECIO DEL PRODUCTO/SERVICIO
								"",
								"",
								"",
								"",
								$fecha_requerimiento,
								$id_nit_oficial,
								0,
								0);	
			 
		}	
		
		/*GENERO UN DOCUMENTO SI EXISTE UNA DIFERENCIA EN PAGO*/
		if ($diferencia>0){
			$con->setFecha($this->fecha);
			$id_planilla_gestion=$con->doCSolicitudAbonoSaldo($serie_contrato,$no_contrato,$diferencia,$observaciones);
			$this->movFactura($DOC_GEN->NO_DOCTO,
								$DOC_GEN->SERIE,
								$no_contrato,
								$serie_contrato,								  
								$DOC_GEN->EM_ID,  
								$DOC_GEN->ID_NIT,
								'ABO', //Abono a capital la diferencia en el pago 
								'',
								'',
								round($diferencia,2),
								$tipo_cambio, 
								36, //ESTATUS
								$id_nit_motorizado,
								$cantidad_cuota,
								$id_planilla_gestion,
								"",
								"",
								0, //CANTIDAD DE PRODUCTO/ SERVICIO
								0, //PRECIO DEL PRODUCTO/SERVICIO
								"",
								"",
								"",
								"",
								$fecha_requerimiento,
								$id_nit_oficial,
								0,
								round($diferencia,2));	
			 
		}			
		/*
			EN CASO DE LOS PAGOS A CONTRATOS QUE NO HAYAN PAGADOS
		*/
		if ($identificar_recibos>0){
			$mci = new ObjectSQL();
			$mci->SERIE=$doc['SERIE'];
			$mci->NO_DOCTO=$doc['CORRELATIVO'];
			$mci->setTable('movimiento_caja_idenficar'); 
			$SQL=$mci->toSQL('insert');	
			mysql_query($SQL);			
		} 			
		
		return $DOC_GEN;
	}		
	/*CANCELA UN DOCUMENTO CON OTRO*/
	public function matarRecibos($no_docto,
								 $serie_doct,
								 $tipo_doc,
								 $serie_doct_anul,
								 $no_docto_anul,
								 $tipo_doc_anul=""){
			
			SystemHtml::getInstance()->includeClass("caja","Caja"); 
			$cj= new Caja($this->_db);
				
			$cn=array(
				"recibo"=>1,
				"serie_docto"=>$serie_doct_anul,
				"no_docto"=>$no_docto_anul
			);				   
			/*CAPTURA EL LISTADO DE RECIBOS SIN FACTURAR*/
			$rb_anular=$cj->getListadoReciboSinFacturar($cn); 
			
			if (count($rb_anular)>0){
				  
				$mov = new ObjectSQL();
				$mov->ANULADO="S";
				$mov->TIPO_DOC_ANUL=$tipo_doc;		
				$mov->SERIE_DOC_ANUL=$serie_doct;  
				$mov->NO_DOC_ANUL=$no_docto; 
				$mov->setTable('movimiento_caja'); 
				$SQLW=" WHERE `ANULADO`='N' AND SERIE='".$serie_doct_anul."' 
													AND NO_DOCTO='".$no_docto_anul."' ";
				if ($tipo_doc_anul!=""){
					$SQLW.=" AND TIPO_DOC='".$tipo_doc_anul."'";
				}
				$SQL=$mov->toSQL('update',$SQLW);	
				mysql_query($SQL);	
 
				$mfact = new ObjectSQL();
				$mfact->ESTATUS=38; //Recibo utilizado
				$mfact->CAJA_SERIE=$serie_doct;
				$mfact->CAJA_NO_DOCTO=$no_docto;					
				$mfact->setTable('movimiento_factura'); 
				$SQL=$mfact->toSQL('update'," WHERE `ESTATUS`='36' AND SERIE='".$serie_doct_anul."' 
													AND NO_DOCTO='".$no_docto_anul."' ");	
				mysql_query($SQL);
				
				$fp = new ObjectSQL();
 				$fp->SERIE=$serie_doct;
				$fp->NO_DOCTO=$no_docto;					
				$fp->setTable('forma_pago_caja'); 
				$SQL=$fp->toSQL('update'," WHERE SERIE='".$serie_doct_anul."' AND NO_DOCTO='".$no_docto_anul."' ");	
				mysql_query($SQL);				

				$recibo=$rb_anular[0]; 
				SysLog::getInstance()->Log($recibo['ID_NIT'], 
											 $recibo['SERIE_CONTRATO'],
											 $recibo['NO_CONTRATO'] ,
											 $recibo['NO_RESERVA'],
											 $recibo['ID_RESERVA'],
											 "PAGO RECIBO ".$serie_doct_anul." ".$no_docto_anul,
											 json_encode($mov));
			}
			 
	}

}




?>
