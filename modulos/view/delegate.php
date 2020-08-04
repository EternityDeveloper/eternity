<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['view_registar_cheque'])){
 	include("view/chequedevuelto/view_registar.php");
	exit;
}

if (isset($_REQUEST['doPrintReciboChequeDevuelto']) && validateField($_REQUEST,"id")){
	include("print/recibo_cheque_devuelto.php");
	exit;
}
/*
	Agrego el requerimiento de cobro Comision bancaria por cheque devuelto
*/
if (isset($_REQUEST['doGererarReciboComisionChequeDevuelto']) && validateField($_REQUEST,"id")
	&& validateField($_REQUEST,"contrato") && validateField($_REQUEST,"id_nit")){   
	$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['id'])); 
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
	$id_nit=json_decode(System::getInstance()->Decrypt($_REQUEST['id_nit'])); 	
	$msg=array("mensaje"=>"No se pudo generar el documento por favor volver a intentar no existe un numero de contrato!","error"=>true); 
 
 
	if (!$contrato->serie_contrato){
		echo json_encode($msg);
		exit;
	}    
	$cn=array(
		"contrato"=>1,
		"serie_contrato"=>$contrato->serie_contrato,
		"no_contrato"=>$contrato->no_contrato,			
	);	    
	
	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("caja","Caja");  
	$cb=new Cobros($protect->getDBLink()); 	
	$caja= new Caja($protect->getDBLINK()); 	
	$recibos=$caja->getListadoReciboSinFacturar($cn); 
	foreach($recibos as $key =>$row){ 
		if ($row['TIPO_MOV'] =="MULTCDRE")	{
			$msg=array("mensaje"=>"Error, existe un documento generado!","error"=>true); 
			echo json_encode($msg);
			exit;	
		}
	}	
	 
	/*GENERANDO EL DOCUMNETO*/
	$DOCT=MVFactura::GI()->doCreateDocument($id_nit,
											"",
											$contrato->no_contrato,
											$contrato->serie_contrato,
											'MULTCDRE',
											RECIBO_VIRTUAL,
											0,
											0,
											300,//comision x cheque devuetlo
											1,
											0);
											
	if (count($DOCT)>0){
		$msg=array("mensaje"=>"Documento generado","error"=>false);
		echo json_encode($msg);
	}else{
		echo json_encode($msg);
	} 
	exit;
}


if (isset($_REQUEST['doReponerChequeDV'])){  
	if (validateField($_REQUEST,'rand_id') && 
							validateField($_REQUEST,"reposicion_id")&& 
							validateField($_REQUEST,"fecha_reposicion")&& 
							validateField($_REQUEST,"id_nit")&& 
							validateField($_REQUEST,"contrato")&& 
							validateField($_REQUEST,"banco_credito")
							){
		SystemHtml::getInstance()->includeClass("caja","ChequesDevuelto");
		
		$token=$_REQUEST['rand_id'];	 
		$reposicion_id=json_decode(System::getinstance()->Decrypt($_REQUEST['reposicion_id']));	
		$ct=json_decode(System::getinstance()->Decrypt($_REQUEST['contrato']));
		$id_nit=json_decode(System::getinstance()->Decrypt($_REQUEST['id_nit']));	
	  	$banco_id=json_decode(System::getInstance()->Decrypt($_REQUEST['banco_credito']));
 
		$comentario=$_REQUEST['comentario'];  
		$fecha=$_REQUEST['fecha_reposicion'];
		$cdv= new ChequesDevuelto($protect->getDBLink());	
 
		$rt=$cdv->reposicionChequeDevuelto($reposicion_id->cd_id,
											$banco_id->ban_id,
											$fecha,
											$token,
											$id_nit,
											$ct->serie_contrato,
											$ct->no_contrato);				
	  	echo json_encode($rt);							
	}else{
		$rt=array("mensaje"=>"Debe de llanar todos los campos!","error"=>true); 
		echo json_encode($rt);				
	}
	
	exit;
}
 
if (isset($_REQUEST['doRegistrarChequeDV'])){ 
	if (validateField($_REQUEST,'token') && validateField($_REQUEST,'banco_debito')){ 
		SystemHtml::getInstance()->includeClass("caja","ChequesDevuelto"); 						
		$comentario=$_REQUEST['comentario']; 
		$banco_id=json_decode(System::getInstance()->Decrypt($_REQUEST['banco_debito']));
 
		$cdv= new ChequesDevuelto($protect->getDBLink());	
		 
		$rt=$cdv->registrarChequeDevuelto($_REQUEST['fecha_cheque'],
										  $banco_id->ban_id,
										  $comentario);				
	  	echo json_encode($rt);							
	}
	exit;
}

if (isset($_REQUEST['cheque_devueltos_list'])){ 
	include("view/chequedevuelto/listado_cheque_devuelto.php"); 
	exit;
}

if (isset($_REQUEST['doRegistrarChequeDevuelto'])){ 
	include("view/listado_recibos/view/registarCD.php"); 
	exit;
}


/*
	AGREGA LOS RECIBOS
*/
if (validateField($_REQUEST,'doNCRecibosAdd') && 
							validateField($_REQUEST,"token")&& 
									validateField($_REQUEST,"id_recibo")  ){
										
	SystemHtml::getInstance()->includeClass("caja","Caja");   
  	$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['id_recibo']));
	$caja= new Caja($protect->getDBLINK()); 
	$caja->setToken($_REQUEST['token']);
	
	if ($_REQUEST['doNCRecibosAdd']=="1"){
		$caja->doItemNotaCredito($recibo);
	}else{
		$caja->doItemReciboRemove($recibo);		
	}
	$listado_nc=$caja->getListCarritoNotaCredito();
	$monto_nc=0;
	foreach($listado_nc as $key =>$row){
		$monto_nc=$monto_nc+$row->MONTO_TOTAL; 
	} 
	
	$listado=$caja->getListCarritoRecibo();
	$monto_a_pagar=0;
	foreach($listado as $key =>$row){
		$monto_a_pagar=$monto_a_pagar+$row->MONTO_TOTAL;
	} 
	$monto_a_pagar=round($monto_a_pagar,2)-round($monto_nc,2);
	if ($monto_a_pagar<=0){
		$monto_a_pagar=0;	
	}
	echo json_encode(array("monto_a_pagar"=>$monto_a_pagar));
	exit;
}


/*INCLUYE LA NOTA DE CREDITO PARA PAGOS*/
if (isset($_REQUEST['view_nota_credito_pago'])){ 
	include("view/view_nota_credito.php");
	exit;
}	

if (isset($_REQUEST["includeFacturaNCScript"])){  
 	include("script/class.FacturarNC.js");
	exit;
}

/*incluye un script*/
if (isset($_REQUEST["includeScript"])){  
	if (validateField($_REQUEST,"script_name")){ 
 		include("script/class.".$_REQUEST['script_name'].".js");
	}
	exit;
}
 

if (isset($_REQUEST["print_dialog"]) && validateField($_REQUEST,"id")){
	include("print/print_dialog.php");
	exit;
}

if (isset($_REQUEST["doDialogSendMailRecibo"]) && validateField($_REQUEST,"id")){
	include("print/dialogReciboMail.php");
	exit;
}
if (isset($_REQUEST["print_dialog_cobro"])){
	include("print/print_dialog_cobro.php");
	exit;
}
 

if (isset($_REQUEST["ProcesarNCcomprobante"]) && validateField($_REQUEST,"rand") && validateField($_REQUEST,"documento")){
	$rcb=json_decode(System::getInstance()->Decrypt($_REQUEST['documento']));
	$valid=false;
	if ($rcb==""){
		echo json_encode(array("html"=>$html,"mensaje"=>$msg,"valid"=>$valid));	
		exit;
	} 	
	$data=STCSession::GI()->isSubmit($_REQUEST['rand']);
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
 
	echo json_encode($caja->doCreateNotaCreditoConComprobante($rcb,$_REQUEST['comentario'])); 	
	exit;
} 

if (isset($_REQUEST["ProcesarNC"]) && validateField($_REQUEST,"rand") && validateField($_REQUEST,"documento")){
	$rcb=json_decode(System::getInstance()->Decrypt($_REQUEST['documento']));
	$valid=false;
	if ($rcb==""){
		echo json_encode(array("html"=>$html,"mensaje"=>$msg,"valid"=>$valid));	
		exit;
	} 	
	$data=STCSession::GI()->isSubmit($_REQUEST['rand']);
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doCreateNotaCredito($rcb,$data,$_REQUEST['comentario'])); 	
	exit;
}

if (isset($_REQUEST["cCaddMonto"]) && validateField($_REQUEST,"valor_monto") 
	&& validateField($_REQUEST,"rand") && validateField($_REQUEST,"documento")){
	
	$rcb=json_decode(System::getInstance()->Decrypt($_REQUEST['documento']));
	
	if ($rcb==""){
		echo json_encode(array("html"=>"Error"));	
		exit;
	} 	
	$data=STCSession::GI()->isSubmit($_REQUEST['rand']);
	$valor_in=$_REQUEST['valor_monto'];

	if (!is_array($data)){
		$data=array();
	}else{
		foreach($data as $key=>$valor_monto){
		$valor_in=$valor_in+$valor_monto;
	}		
	}
	$valid=true;
	
	if ($valor_in<=round($rcb->MONTO,2)){
		array_push($data,$_REQUEST['valor_monto']);	 
		STCSession::GI()->setSubmit($_REQUEST['rand'],$data);
	}else{
		$valid=false;
		$msg="Error, no se puede introducir un valor mayor al monto del recibo!";
	}
 
	include("view/gestion/listar_transaccion.php");
	$html=ob_get_contents();
	ob_clean();
	echo json_encode(array("html"=>$html,"mensaje"=>$msg,"valid"=>$valid));	 
	exit;
}

/*VISTA DE GESTION*/
if (isset($_REQUEST['view_notas_add_cd'])){ 
	include("view/gestion/view_addMontos.php"); 
	exit;
}
/*VISTA DE GESTION*/
if (isset($_REQUEST['view_notascd'])){  
	if (isset($_REQUEST['question'])){
		include("view/gestion/view_question_nota_c.php"); 
	}elseif (isset($_REQUEST['generar_comprobante'])){
		include("view/gestion/view_nota_credito_comprobante.php"); 
	}else{
		include("view/gestion/view_notascd.php"); 	
	}
	exit;
} 
/*VISTA PROCESAR RECIBO*/
if (isset($_REQUEST['view_process_recibo_vr'])){ 
	include("view/gestion/view_process_recibo.php"); 
	exit;
} 


/*VISTA DE GESTION*/
if (isset($_REQUEST['view_generar_nota_cd'])){ 
	include("view/gestion/view_notoascd_cliente.php"); 
	exit;
} 
/*VISTA DE GESTION*/
if (isset($_REQUEST['view_generar_gestion_pago'])){ 
	include("view/gestion/view_gestion.php"); 
	exit;
} 


/*EJECUTA LA CREACION DEL RECIBO */ 
if (validateField($_REQUEST,"doPreFacturar") && validateField($_REQUEST,"forma_pago_token")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doPreFacturar()); 
	
	exit;
}


/*EJECUTA EL PAGO */ 
if (validateField($_REQUEST,"facturar_recibo") ){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doFacturarRecibo()); 
	exit;
}

/*EJECUTA EL PAGO */ 
if (validateField($_REQUEST,"dofacturar")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doFacturarPago()); 
	exit;
}

/*VISTA DE PREGUNTA PARA REMOVER LOS RECIBOS */
if (isset($_REQUEST['doViewQuestionRemove'])){
 	include("view/pago_cuota/view_remove_recibo.php");
	exit;
}
/*REMUEVE LOS RECIBOS */
if (isset($_REQUEST['doRecibosRemove']) && 
							validateField($_REQUEST,"token")&& 
									validateField($_REQUEST,"id_recibo")  ){

	SystemHtml::getInstance()->includeClass("caja","Caja");   
  	$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['id_recibo']));
	$caja= new Caja($protect->getDBLINK()); 
	$caja->setToken($_REQUEST['token']);

	$rt=$caja->doReciboRemove($recibo,$_REQUEST['descripcion']);
 
	echo json_encode($rt);
	exit;
}

/*
	AGREGA LOS RECIBOS
*/
if (validateField($_REQUEST,'doRecibosAdd') && 
							validateField($_REQUEST,"token")&& 
									validateField($_REQUEST,"id_recibo")  ){
										
	SystemHtml::getInstance()->includeClass("caja","Caja");   
  	$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['id_recibo']));
	$caja= new Caja($protect->getDBLINK()); 
	$caja->setToken($_REQUEST['token']);
	if ($_REQUEST['doRecibosAdd']=="1"){
		$caja->doItemRecibo($recibo);
	}else{
		$caja->doItemReciboRemove($recibo);		
	}
	$listado_nc=$caja->getListCarritoNotaCredito();
	$monto_nc=0;
	foreach($listado_nc as $key =>$row){
		$monto_nc=$monto_nc+$row->MONTO_TOTAL; 
	} 
	
	$listado=$caja->getListCarritoRecibo();
	$monto_a_pagar=0;
	foreach($listado as $key =>$row){
		$monto_a_pagar=$monto_a_pagar+$row->MONTO_TOTAL;
	} 
	$monto_a_pagar=round($monto_a_pagar,2)-round($monto_nc,2);
	if ($monto_a_pagar<=0){
		$monto_a_pagar=0;	
	}
	echo json_encode(array("monto_a_pagar"=>$monto_a_pagar));
	exit;
}

/*VISTA DE RECIBOS EN CAJA*/
if (isset($_REQUEST['view_listados_recibos'])){
 	include("view/pagos/listar_recibos.php");
	exit;
}


/*GESTION REALIZADA PARA UN ABONO A CAPITAL*/
if (isset($_REQUEST['view_listado_requerimientos'])){
 	include("view/pago_cuota/listado_requerimientos.php");
	exit;
}


/*GESTION REALIZADA PARA UN ABONO A CAPITAL*/
if (isset($_REQUEST['view_solicitud_gestion_abono'])){
 	include("view/abono_capital/view_solicitud_gestion.php");
	exit;
}


/*VISTA PAGO DE CUOTA*/
if (isset($_REQUEST['view_pago_cuota'])){
 	include("view/pago_cuota/main_view_pago_c.php");
	exit;
}
/*VISTA ABONO A CAPITAL*/
if (isset($_REQUEST['view_abono_capital'])){
 	include("view/abono_capital/main_view_abo_capital.php");
	exit;
}

/*VISTA DE RESERVA*/
if (isset($_REQUEST['mov_reserva'])){ 

	include("view/reserva/caja_pago_reserva.php"); 
	exit;
}
  

if (isset($_REQUEST['mov_inicial'])){  
	include("view/inicial/caja_pago_inicial.php"); 
	exit;
}

if (isset($_REQUEST['mov_contrato'])){ 
	include("view/contrato/ingreso.php"); 
	exit;
}

/*CARGA LA VISTA DEL TOP DE LA VENTANA DE TRANSAPCION*/
if (validateField($_REQUEST,"viewFactura")){
	include("view/operacion/transaccion/view_factura.php");
	exit;
}

 
if (validateField($_REQUEST,"payment_client")&& validateField($_REQUEST,"id_nit")){
	include("view/operacion/cliente/payment_persona.php");
	exit;
}
/*VISTA DETALLE DE ABONOS DE UNA PERSONA*/
if (validateField($_REQUEST,"view_person_abono")&& validateField($_REQUEST,"id_nit")){
	include("view/cliente/listado_abono_inicial.php");
	exit;
}

/*VISTA DETALLE DE ABONOS DE UNA PERSONA QUE NO ESTAN AMARRADOS A UN INICIAL*/
if (validateField($_REQUEST,"view_person_abono_resumen")&& validateField($_REQUEST,"id_nit")){
	include("view/cliente/listado_abonos_cliente.php");
	exit;
}
/*
	
*/
if (isset($_REQUEST['processSelectAbono'])){  
	if (validateField($_REQUEST,"items") && validateField($_REQUEST,"cmd")  && validateField($_REQUEST,"id_nit") ){
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		$caja= new Caja($protect->getDBLINK());   
 
		if ($caja->doItemListAbono($_REQUEST['items'],$_REQUEST['cmd'])){ 
			//$getitem=$caja->getItemListAbono(); 
			$data=array(
						"valid"=>false,
						"mensaje"=>"Datos actualizados" 
					);  
		}else{
			$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
		} 
			
	}else{
		$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
	}
	echo json_encode($data);
	 
	exit;
}

/*
	EN CONTRATO MUESTR
*/ 
if (validateField($_REQUEST,"view_detalle_select_by_caja")&& validateField($_REQUEST,"id_nit")){
	include("view/cliente/listado_abono_view_detail.php");
	exit;
}


/*GENERAR GESTION*/
if (isset($_REQUEST['DoCrearGestionGenerica'])){  
 	if (  validateField($_REQUEST,"monto_abono")
	&& validateField($_REQUEST,"gestion") && validateField($_REQUEST,"id_nit") 
		&& validateField($_REQUEST,"monto_descuento")
		&& validateField($_REQUEST,"tipo_movimiento")){ 
		
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		$gestion=json_decode(System::getInstance()->Decrypt($_REQUEST['gestion'])); 
		$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']); 
		$tipo_movimiento=json_decode(System::getInstance()->Decrypt($_REQUEST['tipo_movimiento'])); 
 		$no_documento=isset($_REQUEST['no_documento'])?$_REQUEST['no_documento']:0;
	 
		$motorizado=0;
	 
		if (validateField($_REQUEST,"motorizado_n")){
			$motorizado=System::getInstance()->Decrypt($_REQUEST['motorizado_n']);
		}	
 
 		if ($tipo_movimiento->TIPO_DOC=="RM"){  
			if (!is_numeric(trim($no_documento))){
				echo  json_encode(array(
														"valid"=>false,
														"mensaje"=>"Error debe de seleccionar un numero de documento!"
														));	
				exit;
			}
		}
		
		if (!isset($gestion->TIPO_MOV)){	
			exit;
		}
		$monto_abono=$_REQUEST['monto_abono'];	
		$monto_descuento=$_REQUEST['monto_descuento'];	

		if($monto_abono>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("caja","Caja");  
			SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
			
			$cb=new Caja($protect->getDBLink()); 
			$con=new Contratos($protect->getDBLink());  
			$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
			
			$tipo_moneda="LOCAL"; 
			if (isset($cdata->tipo_moneda)){
				$tipo_moneda=$cdata->tipo_moneda;
			}
			if (isset($cdata->id_nit_cliente)){
				$id_nit=$cdata->id_nit_cliente;
			}			
  
			$tasa=$cb->getTasaActual($tipo_moneda);	
			 
			if (isset($_REQUEST['fecha_requerimiento_especial_xx'])){
				MVFactura::getInstance()->setFecha($_REQUEST['fecha_requerimiento_especial_xx']);	
			}
			
			$TMOV=$gestion->TIPO_MOV;
			//$_RECIBO=RECIBO_VIRTUAL;
			if ($TMOV=="NC"){
				$_RECIBO=NOTA_CREDITO;
			}
			if ($tipo_movimiento->TIPO_DOC=="RM"){  
				if (($motorizado=="0")){				
					echo  json_encode(array(
										"valid"=>false,
										"mensaje"=>"Error debe de seleccionar un motorizado/oficial!"
										));	
					exit;	
				}
			}
					
			$_RECIBO=$tipo_movimiento->TIPO_DOC;

			/*EN CASO DE CUOTAS*/
			if ($TMOV=="CUOTA"){
				$oficial=Cobros::getInstance()->getOficialFromContato($cdata->serie_contrato,$cdata->no_contrato);
	 
				$docto=MVFactura::getInstance()->doCreateReciboRequerimiento($cdata->id_nit_cliente,
																				$cdata->EM_ID,
																				$cdata->no_contrato,
																				$cdata->serie_contrato,
																				$TMOV,
																				$_RECIBO, //RECIBO CAJA VIRTUAL  
																				$motorizado,
																				$oficial['nit_oficial'],
																				$monto_abono,
																				1,
																				date("Y-m-d"),
																				"GENERANDO RECIBO PARA COBRO POR VENTANILLA",
																				$tasa,
																				0,
																				0,
																				$no_documento
																			);	
																			
				echo  json_encode(array(
										"valid"=>true,
										"mensaje"=>"Documento creado!"
										));																			
																			
				exit;
			} 			
		  
			$docto=MVFactura::getInstance()->doCreateDocument($id_nit,
																$cdata->EM_ID,
																$cdata->no_contrato,
																$cdata->serie_contrato,
																$TMOV,
																$_RECIBO, //RECIBO CAJA VIRTUAL  
																'', //$id_reserva
																'', //$no_reserva
																$monto_abono, //MONTO
																$tasa, //tipo_cambio
																$monto_descuento, //descuento
																$_REQUEST['comentario'],  //OBSERVACIONES
																"GENERANDO DOCUMENTO NOTA DE DEBITO", //LOG DESCRIPCION
																$motorizado, // ID_NIT MOTORIZADO
																0, // NO CUOTA
																0,
																0,
																0,
																0, //CANTIDAD DE PRODUCTO/ SERVICIO
																0, //PRECIO DEL PRODUCTO/SERVICIO 
																0,
																0,
																0,
																0,
																$no_documento);	
	 											
			if ($docto->valid){												
				if ($monto_descuento>0){
					$docto=MVFactura::getInstance()->doCreateDocument($cdata->id_nit_cliente,
																		$cdata->EM_ID,
																		$cdata->no_contrato,
																		$cdata->serie_contrato,
																		'DESESP',
																		$_RECIBO, //RECIBO CAJA VIRTUAL  
																		'', //$id_reserva
																		'', //$no_reserva
																		($monto_descuento*-1), //MONTO
																		$tasa, //tipo_cambio
																		0, //descuento
																		$_REQUEST['comentario'],  //OBSERVACIONES
																		"GENERANDO DESCUENTO ESPECIAL", //LOG DESCRIPCION
																		$motorizado, // ID_NIT MOTORIZADO
																		0, // NO CUOTA
																		0  
																		);		
				}													
			}
			 
			echo json_encode($docto);
			
			exit;
		}
		
	}
	exit;
} 

/*GENERAR GESTION NOTA DE CREDITO Y DEBITO*/
if (isset($_REQUEST['doCrearGestionND'])){  
 	if (validateField($_REQUEST,"monto_abono")
			&& validateField($_REQUEST,"gestion") 
			&& validateField($_REQUEST,"id_nit")){ 
	
		$gestion=json_decode(System::getInstance()->Decrypt($_REQUEST['gestion'])); 
		$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']); 
		
		if (!isset($gestion->TIPO_MOV)){	
			exit;
		}
			
		$monto_abono=$_REQUEST['monto_abono'];	
		if($monto_abono>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("caja","Caja");  
			$cb=new Caja($protect->getDBLink()); 
			if (isset($cdata->id_nit_cliente)){
				$id_nit=$cdata->id_nit_cliente;
			}			
  
			$tasa=$cb->getTasaActual('LOCAL');	
			 
			if (isset($_REQUEST['fecha_requerimiento_especial_xx'])){
				MVFactura::getInstance()->setFecha($_REQUEST['fecha_requerimiento_especial_xx']);	
			}
			
			$TMOV=$gestion->TIPO_MOV;
			$_RECIBO=$gestion->TIPO_MOV;

			$docto=MVFactura::getInstance()->doCreateNOTADB($id_nit,
															$cdata->EM_ID,
															$cdata->no_contrato,
															$cdata->serie_contrato,
															$TMOV,
															$_RECIBO, //RECIBO CAJA VIRTUAL  
															$monto_abono, //MONTO
															$tasa, //tipo_cambio
															$_REQUEST['comentario'],  //OBSERVACIONES
															"GENERANDO DOCUMENTO ".trim($gestion->DESCRIPCION) 
															//LOG DESCRIPCION
															);	
	 		
			$doc=array("valid"=>$docto->valid,"mensaje"=>$docto->mensaje);
			echo json_encode($doc);			
			exit;
		}
		
	}
	exit;
} 

/*OPTIENE LA CONFIGURACION DE UN TIPO DE DOCUMENTO*/
if (validateField($_REQUEST,"getSerieDoc")&& validateField($_REQUEST,"doc")){
	SystemHtml::getInstance()->includeClass("caja","CTipoDocumento"); 
	$doc= new CTipoDocumento($protect->getDBLink());
	$tdoc=json_decode(System::getInstance()->Decrypt($_REQUEST['doc']));
	$data=$doc->getSerieDoc($tdoc->TIPO_DOC);
	$rt=array("document"=>$tdoc,"correlativo"=>$data);
	echo json_encode($rt);
	exit;
}

/*CARGA LA VISTA DEL TOP DE LA VENTANA DE TRANSAPCION*/
if (validateField($_REQUEST,"tipoDocSerieRventaView")){
	include("view/operacion/transaccion/view_doc_serie.php");
	exit;
}
  
if (isset($_REQUEST['operacion'])){ 
	include("view/buscador.php"); 
	exit;
}

/*EJECUTA UN PAGO CONTRATO DE ABONO A CAPITAL AL CONTRATO*/ 
if (validateField($_REQUEST,"caja_submit")&& validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"pago_abono_capital")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doAbonoACapital()); 
	exit;
}
 

/*EJECUTA UN PAGO DE CUOTA AL CONTRATO*/ 
if (validateField($_REQUEST,"caja_submit")&& validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"pago_cuota")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doPagoCuota()); 
	exit;
}


 
/*EJECUTA UN PAGO ABONO*/ 
if (validateField($_REQUEST,"caja_submit")&& 
		validateField($_REQUEST,"no_reserva")&&  
		validateField($_REQUEST,"pago_abono")&& 
						validateField($_REQUEST,"id_nit")){
							
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doPagoAbono()); 
	exit;
}
 
 /*EJECUTA UN PAGO DE UN UNICIAL*/ 
if (validateField($_REQUEST,"caja_submit")&& 
		validateField($_REQUEST,"no_reserva")&& 
		validateField($_REQUEST,"pago_inicial")&& 
						validateField($_REQUEST,"id_nit")){
							
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->doPagoInicial()); 
	exit;
}  

/*AGREGO VALORES A LA FORMA DE PAGO*/
if (isset($_REQUEST['forma_pago_put']) && validateField($_REQUEST,"token")&& validateField($_REQUEST,"monto_a_cobrar")
		&& validateField($_REQUEST,"monto_a_pagar")&& validateField($_REQUEST,"forma_pago")  ){
			
	SystemHtml::getInstance()->includeClass("caja","Caja");  
  
	$caja= new Caja($protect->getDBLINK()); 
	$caja->setToken($_REQUEST['token']);
	$caja->doItem($_REQUEST['token'],$_REQUEST); 
	
	$items=$caja->getItem($_REQUEST['token']);
 
	$_do_generar_factura=false;
	$_REQUEST['payment_view']='payment_list_detalle';
	include("view/view_forma_pago.php");
	 
	$montos=$caja->getMontoApagarYCobrar($_REQUEST['token']); 
	
	if (round($montos['monto_acumulado'],2)>=round($montos['monto_a_pagar'],2)){
		$do_process=true;	
	}
	
	echo json_encode(array("html"=>ob_get_clean(),"detalle"=>$montos,"doGenerateFactura"=>$_do_generar_factura,"proceder"=>$do_process));
	exit;
}

/*REMOVER VALORES A LA FORMA DE PAGO*/
if (isset($_REQUEST['forma_pago_remove']) && validateField($_REQUEST,"token")&& validateField($_REQUEST,"id")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja= new Caja($protect->getDBLINK());  
	$id=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	$caja->removeItem($_REQUEST['token'],$id);  
	$items=$caja->getItem($_REQUEST['token']); 
 
	$_do_generar_factura=false; 
	$montos=$caja->getMontoApagarYCobrar($_REQUEST['token']); 
	$do_process=false;
	if ($montos['monto_acumulado']>=$montos['monto_a_pagar']){
		$do_process=true;	
	}
	  
	echo json_encode(array("detalle"=>$montos,"doGenerateFactura"=>$_do_generar_factura,"proceder"=>$do_process));
	exit;
}

/*REMOVER UN DESCUENTO*/
if (isset($_REQUEST['forma_pago_descuento_remove']) && validateField($_REQUEST,"token")&& validateField($_REQUEST,"id")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja= new Caja($protect->getDBLINK());  
	$id=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	$caja->removeDescuento($_REQUEST['token'],$id); 
	$montos=$caja->getMontoApagarYCobrar($_REQUEST['token']); 
	echo json_encode(array("detalle"=>$montos));
	exit;
} 

/*AGREGO VALORES DE LOS DESCUENTOS*/
if (isset($_REQUEST['forma_pago_add_descuento']) 
			&& validateField($_REQUEST,"token")
				&& validateField($_REQUEST,"descuento_id")  ){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja= new Caja($protect->getDBLINK());  
	$caja->addDescuento($_REQUEST['token'],$_REQUEST);
	
	$montos=$caja->getMontoApagarYCobrar($_REQUEST['token']); 
	
	$_REQUEST['payment_view']='payment_list_detalle';
	include("view/view_forma_pago.php");
 
	echo json_encode(array("html"=>ob_get_clean(),"detalle"=>$montos));
 
	exit;
}


/*
	CONTRATOS
	cambia la tasa de un contrato
*/
if (isset($_REQUEST['processChangeTasa'])){  
	if (validateField($_REQUEST,"item") && validateField($_REQUEST,"tasa")  && validateField($_REQUEST,"id_nit") ){
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		$caja= new Caja($protect->getDBLINK());   
 		$item=json_decode(System::getInstance()->Decrypt($_REQUEST['item']));
		$tasa=$_REQUEST['tasa'];
		$getitem=$caja->getItemListAbono(); 
		$comparer=array();
		foreach($getitem as $key=>$row){
			$comparer[$row->SERIE.$row->NO_DOCTO]=$row;
		}	 
 
		if (array_key_exists($item->SERIE.$item->NO_DOCTO,$comparer)){ 
			$monto=0;
			$monto_rd=0; 
			$comparer[$item->SERIE.$item->NO_DOCTO]->MONTO=($comparer[$item->SERIE.$item->NO_DOCTO]->MONTO_RD/$tasa); 
			$comparer[$item->SERIE.$item->NO_DOCTO]->TIPO_CAMBIO=$tasa; 
 
			$caja->updateItemListAbono($item->SERIE.$item->NO_DOCTO,$comparer[$item->SERIE.$item->NO_DOCTO]);
			foreach($getitem as $key=>$val){
				$monto=$monto+$val->MONTO;
				$monto_rd=$monto_rd+$val->MONTO_RD;				
			} 	 
			include("view/cliente/listado_abono_view_detail.php");
			 
			$data=array(
						"valid"=>true,
						"mensaje"=>"Datos actualizados",
						"monto"=>$monto,
						"monto_rd"=>$monto_rd,
						"html"=>ob_get_clean()
					);  
		}else{
			$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
		} 
			
	}else{
		$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
	}
	echo json_encode($data);
	exit;
}
/*
	CONTRATOS
	Agrega una cuota abonada al carrito de cuotas
*/
if (isset($_REQUEST['processPutAbono'])){  
	if (validateField($_REQUEST,"items") && validateField($_REQUEST,"cmd")  && validateField($_REQUEST,"id_nit") ){
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		$caja= new Caja($protect->getDBLINK());   
 
		if ($caja->doItemListAbono($_REQUEST['items'],$_REQUEST['cmd'])){ 
			$getitem=$caja->getItemListAbono();
			$monto=0;
			$monto_rd=0;
			foreach($getitem as $key=>$val){
				$monto=$monto+$val->MONTO;
				$monto_rd=$monto_rd+$val->MONTO_RD;				
			}			
			
			include("view/cliente/listado_abono_view_detail.php");
			$data=array(
						"valid"=>false,
						"mensaje"=>"Datos actualizados",
						"monto"=>$monto,
						"monto_rd"=>$monto_rd,
						"html"=>ob_get_clean()
					);  
		}else{
			$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
		} 
			
	}else{
		$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
	}
	echo json_encode($data);
	exit;
}
 
if (isset($_REQUEST['forma_pago'])){ 
	include("view/view_forma_pago.php");
	exit;
}




if (isset($_REQUEST['doPutCobroMotorizado']) && isset($_REQUEST['id'])){ 
	SystemHtml::getInstance()->includeClass("caja","Recibos"); 
	$recibos= new Recibos($protect->getDBLINK());   
	$id=substr($_REQUEST['id'],0,6); 
 
	$SQL="SELECT * FROM `movimiento_caja` WHERE `NO_CODIGO_BARRA`='".$id."'";
	$rs=mysql_query($SQL);
	$ret=array("valid"=>false);
	while($row=mysql_fetch_array($rs)){
		$ret['contrato']=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$row['serie_contrato'],
								"no_contrato"=>$row['no_contrato'])));	
		$ret['id_nit']=System::getInstance()->Encrypt($row['ID_NIT']);	
		$ret['valid']=true;				
	}
 	echo json_encode($ret);
}

if (isset($_REQUEST['forma_pago_descuento'])){ 
	include("view/view_forma_pago_descuento.php");
	exit;
}

/*ACESO A CAJA*/
if (isset($_REQUEST['caja'])){ 
	include("view/mantenimiento/caja.php"); 
	exit;
}

/*CIERRES DE COBROS*/
if (isset($_REQUEST['cierre_cobros'])){ 
	include("view/cierre/cobro_cierre.php"); 
	exit;
}
/*CIERRES DE CAJA*/
if (isset($_REQUEST['cierres'])){ 
	include("view/cierre/listar_cierres.php"); 
	exit;
}
/*DELEGADO DE LA FACTURACION EN LOTE*/
if (isset($_REQUEST['lote_delegate'])){ 
	include("view/lote/delegate.php"); 
	exit;
}
/*Facturar en lote*/
if (isset($_REQUEST['facturar_lote'])){ 
	include("view/lote/lote.php"); 
	exit;
}
/*LISTAR LOS RECIBOS*/
if (isset($_REQUEST['listado_recibo_motorizado'])){ 
	include("view/listado_recibos/facturar_recibos_moto.php"); 
	exit;
}
/*LISTAR LOS RECIBOS*/
if (isset($_REQUEST['recibos_list'])){ 
	include("view/listado_recibos/listado_recibo.php"); 
	exit;
}

/*VENTANA DE ANULACION DE RECIBO*/
if (isset($_REQUEST['viewReciboRemove'])){ 
	include("view/listado_recibos/question_remove.php"); 
	exit;
}

/*PREGUNTA DE CIERRE DE CAJA*/
if (isset($_REQUEST['cierres_question'])){ 
	include("view/cierre/question_cierre.php"); 
	exit;
}


/**/
if (isset($_REQUEST['doAddToCierre'])){ 
	if (isset($_REQUEST["cCaddMonto"]) && validateField($_REQUEST,"valor_monto") 
		&& validateField($_REQUEST,"rand") && validateField($_REQUEST,"documento")){
		
		$rcb=json_decode(System::getInstance()->Decrypt($_REQUEST['documento']));
		
		if ($rcb==""){
			echo json_encode(array("html"=>"Error"));	
			exit;
		} 	
		$data=STCSession::GI()->isSubmit($_REQUEST['rand']);
		$valor_in=$_REQUEST['valor_monto'];
	
		if (!is_array($data)){
			$data=array();
		}else{
			foreach($data as $key=>$valor_monto){
			$valor_in=$valor_in+$valor_monto;
		}		
		}
		$valid=true;
		
		if ($valor_in<=round($rcb->MONTO,2)){
			array_push($data,$_REQUEST['valor_monto']);	 
			STCSession::GI()->setSubmit($_REQUEST['rand'],$data);
		}else{
			$valid=false;
			$msg="Error, no se puede introducir un valor mayor al monto del recibo!";
		}
	 
		include("view/gestion/listar_transaccion.php");
		//include("view/cierre/view/cierre_detalle_montos.php"); 	
		$html=ob_get_contents();
		ob_clean();
		echo json_encode(array("html"=>$html,"mensaje"=>$msg,"valid"=>$valid));	 
		exit;
	}
}

/*VISTA DE EDICION DE TASA CAMBIO CUOTA Y EL TIPO DE DOCUEMENTO*/
if (isset($_REQUEST['doViewDepositarBanco'])){ 
	include("view/cierre/view/view_cierre.php"); 
	exit;
}

/*VISTA DE EDICION DE TASA CAMBIO CUOTA Y EL TIPO DE DOCUEMENTO*/
if (isset($_REQUEST['doEditRecibo'])){ 
	include("view/cierre/view/edit_recibo.php"); 
	exit;
}

/*VISTA DE EDICION DE TASA CAMBIO CUOTA Y EL TIPO DE DOCUEMENTO*/
if (isset($_REQUEST['doAsignarRecibo'])){ 
	include("view/cierre/view/asignar_recibo.php"); 
	exit;
}

/* ANULA LOS RECIBO DE CAJA */
if (isset($_REQUEST['doAnularReciboCaja'])){ 
	if (validateField($_REQUEST,"id")){ 
		$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
		$rt=array(
			"valid"=>true,
			"mensaje"=>"Error, debe de seleccionar una caja", 
		);  
		if (isset($recibo->NO_DOCTO) && isset($recibo->SERIE)){ 
			SystemHtml::getInstance()->includeClass("caja","Recibos"); 
			$rcb= new Recibos($protect->getDBLINK());   
			
 			$rt=$rcb->AnularReciboCaja($recibo->SERIE,$recibo->NO_DOCTO,$_REQUEST['descripcion']); 
		}
		echo json_encode($rt);
		exit;
	}
}
/* ALMACENA LOS CIERRES */
if (isset($_REQUEST['savePeriodoCierre'])){ 
	if (validateField($_REQUEST,"id_caja") && validateField($_REQUEST,"tipo") 
			&& validateField($_REQUEST,"periodo")){ 
			
		$id_caja=json_decode(System::getInstance()->Decrypt($_REQUEST['id_caja']));
		$rt=array(
			"valid"=>true,
			"mensaje"=>"Error, debe de seleccionar una caja", 
		);  
		if (isset($id_caja->ID_CAJA)){ 
			SystemHtml::getInstance()->includeClass("caja","Cierre"); 
			$cierre= new Cierre($protect->getDBLINK());   
 			$cierre->setCierre($_REQUEST['tipo'],$id_caja->ID_CAJA,$_REQUEST['periodo']); 
			$rt['valid']=false;
			$rt['mensaje']="Agregado";
		}
		echo json_encode($rt);
		exit;
	}
}



/* CAMBIA UNA TASA DEL RECIBO EN CASO DE ERROR  AL INGRESARLO */
if (isset($_REQUEST['doSaveAsignacionRecibo'])){ 
 
	if (validateField($_REQUEST,"recibo") && validateField($_REQUEST,"reporte_venta")){ 
			
		$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['recibo']));
		$rt=array(
			"valid"=>true,
			"mensaje"=>"Error, debe de seleccionar una caja", 
		);  
		
		if (isset($recibo->SERIE)){ 
			$id_nit_oficial="";
			$motorizado="";			
			if (validateField($_REQUEST,"oficial")  && validateField($_REQUEST,"motorizado")){
				$id_nit_oficial=System::getInstance()->Decrypt($_REQUEST['oficial']);
				$motorizado=System::getInstance()->Decrypt($_REQUEST['motorizado']);			
			}
			
			SystemHtml::getInstance()->includeClass("caja","Recibos");  
			$recibos= new Recibos($protect->getDBLINK());   
			$recibos->doAsignarRecibo($recibo->SERIE,$recibo->NO_DOCTO,
										$_REQUEST['reporte_venta'],$id_nit_oficial,$motorizado);
			$rt['valid']=$rc_detalle;
			$rt['mensaje']="Cambio aplicado";
		}
		echo json_encode($rt);
		exit;
	}
} 


/* CAMBIA UNA TASA DEL RECIBO EN CASO DE ERROR  AL INGRESARLO */
if (isset($_REQUEST['doSaveChangeTasaRecibo'])){ 
	if (validateField($_REQUEST,"recibo") && validateField($_REQUEST,"tasa")){ 
			
		$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['recibo']));
		$rt=array(
			"valid"=>true,
			"mensaje"=>"Error, debe de seleccionar una caja", 
		);  
		if (isset($recibo->SERIE)){ 
			
			SystemHtml::getInstance()->includeClass("caja","Recibos");  
			$recibos= new Recibos($protect->getDBLINK());   
 			$rc_detalle=$recibos->doChangeTasaRecbio($recibo->SERIE,$recibo->NO_DOCTO,$_REQUEST['tasa']);		
		 
			$rt['valid']=$rc_detalle;
			$rt['mensaje']="Cambio aplicado";
		}
		echo json_encode($rt);
		exit;
	}
} 
 
/* CAMBIA UNA TASA Y EL TIPO DE FORMA DE PAGO EN CASO DE ERROR*/
if (isset($_REQUEST['doSaveTipoCambioFormaPago'])){ 
	
	if (validateField($_REQUEST,"recibo") && validateField($_REQUEST,"tasa") && validateField($_REQUEST,"_forma_pago")){ 
			
		$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['recibo']));
		$rt=array(
			"valid"=>true,
			"mensaje"=>"Error, debe de seleccionar una caja", 
		);   
		if (isset($recibo->SERIE)){ 
			 
			SystemHtml::getInstance()->includeClass("caja","Recibos");  
		 
			$recibos= new Recibos($protect->getDBLINK());   
 			$rc_detalle=$recibos->doChangeTasaFormaPagoRecibo($recibo->SERIE,$recibo->NO_DOCTO,
																$recibo->FORMA_PAGO,
																$_REQUEST['_forma_pago'],
																$_REQUEST['tasa']);		
		 
			$rt['valid']=$rc_detalle;
			$rt['mensaje']="Cambio aplicado";
		}
		echo json_encode($rt);
		exit;
	}
}
/* 
	Procesa el cierre
*/
if (isset($_REQUEST['procesarCierre'])){    
	SystemHtml::getInstance()->includeClass("caja","Cierre"); 
	$cierre= new Cierre($protect->getDBLINK());   
	$rt=$cierre->generarCierre();   
	echo json_encode($rt);
	exit; 
}

if (isset($_REQUEST['doc_av_cobro'])){ 
	include("print/aviso_cobro.php"); 
	exit;
}
 
if (isset($_REQUEST['doc_reserva_fact'])){ 
	include("print/factura_reserva.php"); 
	exit;
}


if (isset($_REQUEST['recibo_nfactura_cobro'])){ 
	include("print/recibo_fp_cobro.php"); 
	exit;
}

if (isset($_REQUEST['recibo_nfactura'])){ 
	include("print/recibo_fp.php"); 
	exit;
}

if (isset($_REQUEST['recibo_factura'])){ 
	include("print/recibo_factura.php"); 
	exit;
}

if (isset($_REQUEST['aviso_descuento'])){ 
	include("print/email_descuento.php"); 
	exit;
}
 
  
 
?>