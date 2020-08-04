<?php
if (!isset($protect)){
	exit;
}


if (isset($_REQUEST["doViewDireccionOnMap"]) && validateField($_REQUEST,"id_direccion")){ 
	include("map/direccionOnMap.php");
	exit;
} 

if (isset($_REQUEST["doRemoveMovimientos"]) && validateField($_REQUEST,"id")){ 
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id'])); 
	SystemHtml::getInstance()->includeClass("contratos","EstadoContrato"); 	 
	/*Estados financieros entre otros*/
	$EF=new EstadoContrato($protect->getDBLink());  
	echo json_encode($EF->removerMoviemientos($contrato->serie_contrato,$contrato->no_contrato));	 
	exit;
} 

if (isset($_REQUEST["doRepararContrato"]) && validateField($_REQUEST,"id")){ 
	include("contrato/view/viewfixContratos.php");
	exit;
}

if (isset($_REQUEST['print_estado_de_cuenta'])){ 
	include("print/pdf_estado_de_cuenta.php"); 
	exit;
}


/* VENTANA DE AGREGAR EDITAR BENEFICIARIO -- ROBERTO ROJAS */
if (isset($_REQUEST['view_beneficiario'])) {
    include("contrato/view_beneficiario/dt_listado_beneficiario.php");
    exit;
}

/* VENTANA DE AGREGAR EDITAR REPRESENTANTE -- ROBERTO ROJAS */
if (isset($_REQUEST['view_representante'])) {
    include("contrato/view_representante/dt_listado_representante.php");
    exit;
}

/* NUEVA INCLUSION PROCESAR BENEFICIARIO Y REPRESENTANTE -- ROBERTO ROJAS*/
if (isset($_REQUEST['procesar_beneficiario']) && validateField($_REQUEST, "contrato") && validateField($_REQUEST, "beneficiario") && validateField($_REQUEST, "new_beneficiario")) {
    $contrato = (array) json_decode((System::getInstance()->Decrypt($_REQUEST["contrato"])));
    $new_beneficiario = ($_REQUEST["new_beneficiario"]);
    $id = System::getInstance()->Decrypt($_REQUEST["beneficiario"]);
    $accion = $_REQUEST["accion"];

    
    $contrato_actual = mysql_escape_string($contrato['no_contrato']);
    $serie_contrato = mysql_escape_string($contrato['serie_contrato']);
    $id_nit = mysql_escape_string($contrato['id_nit']);
    $valid = false;
    $data = "Lo sentimos, No se pudo guardar el beneficiario";
    if ($accion == "desactiva") {
       
        $obj = new ObjectSQL();
        $obj->estatus = 2;
        $obj->setTable("beneficiario");
        $SQL_DESACTIVA = $obj->toSQL("update", " where `id_beneficiario` = '$id'");
        mysql_query($SQL_DESACTIVA);
        
    }

    if (validateField($new_beneficiario, "data") && ($new_beneficiario['tipo'] == "menor")) {

        $ben = new ObjectSQL();
        $ben->no_contrato = $contrato_actual;
        $ben->serie_contrato = $serie_contrato;
        $ben->id_parentesco = System::getInstance()->Decrypt($new_beneficiario['data']['parentesco_id']);
        $ben->nombre_1 = $new_beneficiario['data']['primer_nombre'];
        $ben->nombre_2 = $new_beneficiario['data']['segundo_nombre'];
        $ben->apellido_1 = $new_beneficiario['data']['primer_apellido'];
        $ben->apelllido_2 = $new_beneficiario['data']['segundo_apellido'];
        $ben->fecha_nacimiento = "STR_TO_DATE('" . $new_beneficiario['data']['fecha_nacimiento'] . "','%d-%m-%Y')";
        $ben->lugar_nacimiento = $new_beneficiario['data']['lugar_nacimiento'];
        $ben->estatus = 1;
        $ben->setTable("beneficiario");
        $SQL = $ben->toSQL("insert");
        if (mysql_query($SQL)) {
            $data = "beneficiario Agregado correctamente";
            $valid = true;
        }
    } else if ($new_beneficiario['tipo'] == "mayor") {

        $parent = new ObjectSQL();
        $parent->id_parentesco = System::getInstance()->Decrypt($new_beneficiario['data']['parentesco_id']);
        $parent->id_nit = $id_nit;
        $parent->id_nit_parentesco = $new_beneficiario['data']['numero_documento'];
        $parent->setTable("sys_parentesco");
        $OBJECT_SQL = $parent->toSQL("insert");
       
        mysql_query($OBJECT_SQL);
       
        $ben = new ObjectSQL();
        $ben->no_contrato = $contrato_actual;
        $ben->serie_contrato = $serie_contrato;
        $ben->empresa = "SJM";
        $ben->id_parentesco = System::getInstance()->Decrypt($new_beneficiario['data']['parentesco_id']);
        $ben->id_nit = $new_beneficiario['data']['numero_documento'];
        $ben->nombre_1 = $new_beneficiario['data']['primer_nombre'];
        $ben->nombre_2 = $new_beneficiario['data']['segundo_nombre'];
        $ben->apellido_1 = $new_beneficiario['data']['primer_apellido'];
        $ben->apelllido_2 = $new_beneficiario['data']['segundo_apellido'];
        $ben->fecha_nacimiento = "STR_TO_DATE('" . $new_beneficiario['data']['fecha_nacimiento'] . "','%d-%m-%Y')";
        $ben->lugar_nacimiento = $new_beneficiario['data']['lugar_nacimiento'];
	$ben->estatus = 1;
        $ben->setTable("beneficiario");
        $SQL_OBJETO = $ben->toSQL("insert");
        
        if (mysql_query($SQL_OBJETO)) {
            $data = "beneficiario Agregado correctamente";
            $valid = true;
        }
    }

    echo json_encode(array("valid" => $valid, "mensaje" => $data));
    exit;
}



// CASO REPRESENTANTE --- 
if (isset($_REQUEST['procesar_representante']) && validateField($_REQUEST, "contrato") && validateField($_REQUEST, "representante") && validateField($_REQUEST, "new_representante")) {
    $contrato = (array) json_decode((System::getInstance()->Decrypt($_REQUEST["contrato"])));
    $new_representante = ($_REQUEST["new_representante"]);
    $id = System::getInstance()->Decrypt($_REQUEST["representante"]);
    $accion = $_REQUEST["accion"];
    $contrato_actual = mysql_escape_string($contrato['no_contrato']);
    $serie_contrato = mysql_escape_string($contrato['serie_contrato']);
    $id_nit = mysql_escape_string($contrato['id_nit']);
    $valid = false;
    $data = "Lo sentimos, No se pudo guardar el representante";

    if ($new_representante['tipo'] == "mayor") {

        $ben = new ObjectSQL();
        $ben->no_contrato = $contrato_actual;
        $ben->serie_contrato = $serie_contrato;
        $ben->empresa = "SJM";
        $ben->id_nit_representante = $new_representante['data']['numero_documento'];
        $ben->status=1;
        $ben->setTable("representantes");
        $SQL_OBJETO = $ben->toSQL("insert");
		 
        if (mysql_query($SQL_OBJETO)) {
            $data = "representante Agregado correctamente";
            $valid = true;

            if ($accion == "desactiva") {
                $obj = new ObjectSQL();
                $obj->status = 2;
                $obj->setTable("representantes");
                $SQL_DESACTIVA = $obj->toSQL("update", " where `no_contrato` = '$contrato_actual' AND `serie_contrato`='$serie_contrato' AND`id_nit_representante` = '$id'");
                mysql_query($SQL_DESACTIVA);
                
            }

            $parent = new ObjectSQL();
            $parent->id_parentesco = System::getInstance()->Decrypt($new_representante['data']['parentesco_id']);
            $parent->id_nit = $id_nit;
            $parent->id_nit_parentesco = $new_representante['data']['numero_documento'];
            $parent->setTable("sys_parentesco");
            $OBJECT_SQL = $parent->toSQL("insert");
            mysql_query($OBJECT_SQL);
        } else {
            $data = "Lo sentimos no puede hacer este cambio";
        }
    }

    echo json_encode(array("valid" => $valid, "mensaje" => $data));
    exit;
} 
/* FIN DE INCLUSION BLOQUE DE CODIGO PARA FUNCIONES DE BENEFICIARIO Y REPRESENTANTE */


if (isset($_REQUEST["exportToExcel"])){
	SystemHtml::getInstance()->includeClass("cobros","Cobros");    	  
	$cobros= new Cobros($protect->getDBLINK()); 
	$filter=array();
	$filter['action']='filter_range_date';
	$filter['desde']=date('Y-m-d');
	$filter['hasta']=date('Y-m-d');
	if (validateField($_REQUEST,"fdesde") && validateField($_REQUEST,"fhasta") ){ 
	   $filter['desde']=$_REQUEST['fdesde'];
	   $filter['hasta']=$_REQUEST['fhasta'];   
	} 
	
	$detalle=$cobros->getDetalleCobroOficialMotorizado($filter); 
	
	$assoc = array();  
	
	foreach($detalle['data'] as $key =>$ase){
		$inf=array();
		$inf['OFICIAL']= utf8_encode($key);
			foreach($ase as $key =>$oficial){
				$inf['MOTORIZADO']= utf8_encode($key);
				foreach($oficial as $key =>$row){		
					$inf['CONTRATO']=$row['contrato'];
					$inf['NOMBRE_CLIENTE']=$row['NOMBRE_CLIENTE'];					
					$inf['MOVIMIENTO']=$row['TIPO_MOV'];
					$inf['DOCUMENTO']=$row['DOCUMENTO'];					
					$inf['MONTO']=round($row['MONTO'],2);
					array_push($assoc,$inf);					
				}
			}
	
	}

	createExcel("reporte_". $filter['desde']."_". $filter['hasta'].".xls", $assoc);	
	exit;
}


if (isset($_REQUEST['screen_meta'])){  
	include("dashboard/scree_display.php"); 
	exit;
} 



if (isset($_REQUEST["exportMetaToExcel"])){
	ini_set('display_errors', 1);
	set_time_limit(300);
 	$filter=array();

	if (validateField($_REQUEST,"p_fecha_desde") && validateField($_REQUEST,"p_fecha_hasta")){
	   $filter['desde']=$_REQUEST['p_fecha_desde'];
	   $filter['hasta']=$_REQUEST['p_fecha_hasta'];   
	}    
	if (validateField($_REQUEST,"por_forma_pago")){
		$filter['por_forma_pago']=$_REQUEST['por_forma_pago'];   
	}
	if (validateField($_REQUEST,"pendiente_de_pago") ){
		$filter['pendiente_de_pago']=$_REQUEST['pendiente_de_pago'];   
	}   
	$filter['pendiente_de_pago']=1;
	if (validateField($_REQUEST,"por_compromiso") && validateField($_REQUEST,"monto_compromiso")){
	   $filter['monto_compromiso']=$_REQUEST['monto_compromiso'];
	   $filter['por_compromiso']=$_REQUEST['por_compromiso'];   
	} 

	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	
	$cobros= new Cobros($protect->getDBLINK()); 
	
	$oficial=array();
	if (isset($_REQUEST['oficial'])){
		$sp=explode(",",$_REQUEST['oficial']);
		if (is_array($sp)>0){ 
			foreach($sp as $key =>$val){ 
				if ($val!=''){
					$valor=System::getInstance()->Decrypt($val);
					$oficial[$valor]=$valor;
				}
			}	
			$filter['oficial']=$oficial;
		}
	}
	  
	$filter['por_saldos']=array();
	if (validateField($_REQUEST,"por_saldos")){
		$sp=explode(",",$_REQUEST['por_saldos']);
		if (is_array($sp)>0){
			foreach($sp as $key =>$val){			
				array_push($filter['por_saldos'],$val); 
			}	
	 
		}	   
	} 
	 	
	$my_cartera=$cobros->getCarteraAsignadaOficial($protect->getIDNIT(),$filter);
//	print_r($my_cartera);
//	exit; 	
	$n_cartera=array(
		0=>array(
				"CATEGORIA",
				"NOMBRE_CLIENTE",
				"NOMBRE_OFICIAL",
				"NOMBRE_MOTORIZADO",
				"MONTO_TOTAL",
				"CONTRATO",
				"PRODUCTO",
				"TOTAL_CLIENTE",
				"DIRECCION"
			)
	);
	foreach($my_cartera as $key =>$row){
		if (trim($row['categoria'])==''){
			$row['categoria']='CUOTA';	
		}
	 	$my_cartera[$key]['MONTO_TOTAL']=$row['saldo_0_30']+$row['saldo_31_60']+$row['saldo_61_90']+$row['saldo_91_120']+$row['saldo_mas_120'];
		array_push($n_cartera,array(
								$row['categoria'],
								$row['nombre_cliente'],
								$row['NOMBRE_OFICIAL'],
								$row['NOMBRE_MOTORIZADO'],
								$my_cartera[$key]['MONTO_TOTAL'],	
								$row['serie_contrato']." ".$row['no_contrato'],
								$row['producto'],
								$row['total_cliente'],
								$row['direccion_cobro'],										
							));
		
 
	}	  
	
	/*
		$fp = fopen("meta_cobros.csv", 'w')or die("Can't open php://output");
		header("Content-Type:application/csv"); 
		header("Content-Disposition:attachment;filename=meta_cobros.csv"); 		
		foreach ($n_cartera as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);	 */
	//	print_r($n_cartera);
	createExcel("META_COBRO.xls",$n_cartera);	
	exit;
}


if (isset($_REQUEST['getDetalleCobro'])){  
	include("dashboard/report/detalle_cobro.php");  
	exit;
} 
if (isset($_REQUEST['getCarteraAsignada'])){  
	include("dashboard/cartera_cobro_asignada.php");  
	exit;
} 
 
if (isset($_REQUEST['getCobrosXoficial'])){  
	include("dashboard/cobros_por_oficial.php");  
	exit;
} 
 
if (isset($_REQUEST['getDetalleGestion'])){  
	include("dashboard/detalle_requerimiento.php");  
	exit;
} 
if (isset($_REQUEST['inhumado'])){  
	include("inhumado/delegate.php");  
	exit;
} 

/*OPTIENE EL DETALLE DEL INHUMADO*/
if (isset($_REQUEST['view_detalle_inhumado'])){  
	if (!isset($_REQUEST['id_nit'])){
		exit;
	}
	$id_init=System::getInstance()->Decrypt($_REQUEST['id_nit']);
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
	 
	if ((!isset($contrato->serie_contrato)) || (!isset($contrato->no_contrato))){
		exit;
	}
	if (trim($id_init)==""){
		exit;
	}
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	$person= new PersonalData($protect->getDBLink(),$_REQUEST);
	if (!$person->existClient($id_init)){
		exit;	
	}
	SystemHtml::getInstance()->includeClass("cobros","Servicios"); 
	$srv=new Servicios($protect->getDBLink()); 
	$srv->clearServicioSession();
	$data=$person->getClientData($id_init);
	$data['id_nit_enc']=System::getInstance()->Encrypt($data['id_nit']);
 	echo json_encode(array("valid"=>true,"data"=>$data));
	exit;
} 
/*VISTA */
if (isset($_REQUEST['view_question_data'])){  
	include("gestion/view_question.php");  
	exit;
} 
/*VISTA DE ABONO A CAPITAL*/
if (isset($_REQUEST['view_gestion_inhumacion'])){  
	include("inhumado/view_inhumacion.php"); 
	exit;
} 
if (isset($_REQUEST['cierre_acta_listado'])){  
	include("actas/cierre_acta_listado.php"); 
	exit;
} 

if (isset($_REQUEST['cierre_acta'])){  
	include("actas/listado_acta.php"); 
	exit;
} 
if (isset($_REQUEST['acta'])){  
	include("actas/listado.php"); 
	exit;
} 

/*GENERA PDF DE LA SOLICITUD DE GESTION DE ABONO A CAPITAL*/
if (isset($_REQUEST['solicitud_gestion_abono'])){  
	include("gestion/pdf_planilla_solicitud_gestion.php"); 
	exit;
} 


/*VISTA ABONO A SALDO*/
if (isset($_REQUEST['view_cancelacion_total'])){  
	include("gestion/view_cancelacion_total.php"); 
	exit;
} 
/*VISTA ABONO A SALDO*/
if (isset($_REQUEST['view_abono_a_saldo'])){  
	include("gestion/view_abono_a_saldo.php"); 
	exit;
} 

/*VISTA REACTIVACION*/
if (isset($_REQUEST['view_reactivacion'])){  
	include("gestion/view_reactivacion.php"); 
	exit;
} 


/*VISTA NOTA DE DEBITO*/
if (isset($_REQUEST['view_nota_credito'])){  
	include("gestion/view_nota_credito.php"); 
	exit;
} 


/*VISTA DE CAMBIO DE PLAN*/
if (isset($_REQUEST['view_cambio_plan_sn_ab'])){  
	include("gestion/view_cambio_plan_sn_ab.php"); 
	exit;
} 

/*VISTA DE ABONO A CAPITAL*/
if (isset($_REQUEST['view_abono_capital'])){  
	include("gestion/view_abono_capital.php"); 
	exit;
} 


/*
	Agrega una cuota al carrito de cuotas
*/

if (isset($_REQUEST['doGenerateRequerimiento']) && validateField($_REQUEST,"contrato")
&& validateField($_REQUEST,"token")){   
	$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
	if (!$ct->serie_contrato){
		exit;
	}

	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("contratos","Contratos");   
	$cb=new Cobros($protect->getDBLink()); 	
	$caja= new Caja($protect->getDBLINK());    
	$con=new Contratos($protect->getDBLink()); 
	$info=$cb->getItem($_REQUEST['token']); 

	$cn=array(
		"contrato"=>1,
		"serie_contrato"=>$ct->serie_contrato,
		"no_contrato"=>$ct->no_contrato,			
	);	    
	
	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 

	$recibos=$caja->getListadoReciboSinFacturar($cn);
	foreach($recibos as $key =>$row){
		if ($row['TIPO_MOV'] =="CUOTA")	{
			echo "Existe!";
			exit;	
		}
	}  
	$cdata=$con->getInfoContrato($ct->serie_contrato,$ct->no_contrato);	
	$tipo_cambio=$caja->getTasaActual($cdata->tipo_moneda);

	$oficial=Cobros::getInstance()->getOficialFromContato($cdata->serie_contrato,$cdata->no_contrato);		
	if (count($oficial)==0){
	//	echo "Error no existe el oficial/motorizado!";
	//	exit;	
		$oficial['nit_motorizado']=0;
		$oficial['nit_oficial']=0;		
	}
	 
	foreach($info as $key =>$row){
 
		$docto=MVFactura::getInstance()->doCreateReciboRequerimiento($cdata->id_nit_cliente,
																		$cdata->EM_ID,
																		$cdata->no_contrato,
																		$cdata->serie_contrato,
																		'CUOTA',
																		RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
																		$oficial['nit_motorizado'],
																		$oficial['nit_oficial'],
																		$row->monto,
																		1,
																		date("Y-m-d"),
																		"GENERANDO RECIBO PARA COBRO POR VENTANILLA",
																		$tipo_cambio
																	);	
		
	}

	echo "valid"; 
	exit;
}

/*CALCULO DEL ABONO A CAPITAL PARA LA VISTA DE ABONO_CAPITAL*/
if (isset($_REQUEST['DoSaveServicioInhumacion'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"monto_a_abonar")
			&& validateField($_REQUEST,"plazo")){ 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
		
		$monto_a_abonar=$_REQUEST['monto_a_abonar'];
		
		if($monto_a_abonar>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos");   
			$con=new Contratos($protect->getDBLink());  
			$ret=$con->calcularAbonoACapital($contrato->serie_contrato,$contrato->no_contrato,$monto_a_abonar,$_REQUEST['plazo']);
	 		echo json_encode($ret);
		}
		
	}
	exit;
} 

/*CALCULO DEL ABONO A CAPITAL PARA LA VISTA DE ABONO_CAPITAL*/
if (isset($_REQUEST['calc_abono_saldo'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"monto_a_abonar")){ 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
		$monto_a_abonar=$_REQUEST['monto_a_abonar'];

		if($monto_a_abonar>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos");   
			$con=new Contratos($protect->getDBLink());
			$cc=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
  
			$ret=$con->calcularAbonoACapital($contrato->serie_contrato,
												$contrato->no_contrato,
												$monto_a_abonar,
												$cc->cuotas);
	 		echo json_encode($ret);
		}
		
	}
	exit;
} 

/*CALCULO MONTO DESPUES DEL CAMBIO DE PLAN*/
if (isset($_REQUEST['calc_cambio_plan'])){  
	if (validateField($_REQUEST,"contrato") && validateField($_REQUEST,"plazo")
		&& validateField($_REQUEST,"int_financiamiento")){ 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
 
		SystemHtml::getInstance()->includeClass("contratos","Contratos");   
		$con=new Contratos($protect->getDBLink());  
		$ret=$con->calcularAbonoACapital($contrato->serie_contrato,$contrato->no_contrato,0,
										$_REQUEST['plazo'],$_REQUEST['int_financiamiento']);
		echo json_encode($ret);
	 
		
	}
	exit;
} 

/*CALCULO DEL ABONO A CAPITAL PARA LA VISTA DE ABONO_CAPITAL*/
if (isset($_REQUEST['calc_abono_capital'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"monto_a_abonar")
			&& validateField($_REQUEST,"plazo")){ 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
		
		$monto_a_abonar=$_REQUEST['monto_a_abonar'];
		
		if($monto_a_abonar>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos");   
			$con=new Contratos($protect->getDBLink());  
			$ret=$con->calcularAbonoACapital($contrato->serie_contrato,$contrato->no_contrato,$monto_a_abonar,$_REQUEST['plazo']);
	 		echo json_encode($ret);
		}
		
	}
	exit;
} 

/*CREA LA GESTION PARA UNA CANCELACION TOTAL*/
if (isset($_REQUEST['CGCanelacionTotal'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"por_descuento")){ 
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		
		if (!$contrato->serie_contrato){
			exit;
		}
		
		$por_descuento=$_REQUEST['por_descuento'];		
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
		$cb=new Cobros($protect->getDBLink()); 
		$con=new Contratos($protect->getDBLink());  
		$ret=$con->crearGestionCancelacionTotal($contrato->serie_contrato,
											$contrato->no_contrato,
											$por_descuento,
											$_REQUEST['comentario']); 
		if (!$ret['error']){
			$ret['id_solicitud']=System::getInstance()->Encrypt(json_encode($cb->getSolicitudesAbonoCapitalBy($ret['solicitud'])));	
		}
 
		echo json_encode($ret);
		
		exit;
		 
		
	}
	exit;
} 

/*CREA UN DOCUMENTO PARA SER FACTURADO COMO UNA CUOTA MENOR*/
if (isset($_REQUEST['CGPagoMenor'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"monto_abono")){ 
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
	 
		$monto_abono=$_REQUEST['monto_abono'];		
		if($monto_abono>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("caja","Caja");  
			SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
			
			$cb=new Caja($protect->getDBLink()); 
			$con=new Contratos($protect->getDBLink());  
			$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
			$tasa=$cb->getTasaActual($cdata->tipo_moneda);	
			 
 			/*
			$docto=MVFactura::getInstance()->doCreateDocument($cdata->id_nit_cliente,
														$cdata->EM_ID,
														$cdata->no_contrato,
														$cdata->serie_contrato,
														'CUOTA',
														RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
														'', //$id_reserva
														'', //$no_reserva
														$monto_abono, //MONTO
														$tasa, //tipo_cambio
														0, //descuento
														"GENERANDO RECIBO PAGO MENOR AL COMPROMISO",  //OBSERVACIONES
														"GENERANDO RECIBO PAGO MENOR AL COMPROMISO", //LOG DESCRIPCION
														'', // ID_NIT MOTORIZADO
														0, // NO CUOTA
														0  
														); */
 
			$oficial=Cobros::getInstance()->getOficialFromContato($cdata->serie_contrato,$cdata->no_contrato);
 
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
																			$tasa );	
	 
			echo json_encode(array("mensaje"=>"Recibo generado","error"=>false));
			
			exit;
		}
		
	}
	exit;
} 


/*NOTA DE DEBITO*/
if (isset($_REQUEST['CGNotaDebito'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"monto_abono")){ 
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
		
		$monto_abono=$_REQUEST['monto_abono'];		
		if($monto_abono>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("caja","Caja");  
			$cb=new Caja($protect->getDBLink()); 
			$con=new Contratos($protect->getDBLink());  
			$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
			$tasa=$cb->getTasaActual($cdata->tipo_moneda);	
 
			$docto=MVFactura::getInstance()->doCreateDocument($cdata->id_nit_cliente,
																$cdata->EM_ID,
																$cdata->no_contrato,
																$cdata->serie_contrato,
																'ND',
																RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
																'', //$id_reserva
																'', //$no_reserva
																$monto_abono, //MONTO
																$tasa, //tipo_cambio
																0, //descuento
																$_REQUEST['comentario'],  //OBSERVACIONES
																"GENERANDO DOCUMENTO NOTA DE DEBITO", //LOG DESCRIPCION
																'', // ID_NIT MOTORIZADO
																0, // NO CUOTA
																0  
																);	
	 
			echo json_encode(array("mensaje"=>"Recibo generado","error"=>false));
			
			exit;
		}
		
	}
	exit;
} 

/*CREA LA GESTION DE REACTIVACION*/
if (isset($_REQUEST['CGReactivacion'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"penalidad")){ 
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
	
		$penalidad=$_REQUEST['penalidad'];		
		if($penalidad>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("cobros","Cobros");  
			$cb=new Cobros($protect->getDBLink()); 
			$con=new Contratos($protect->getDBLink());  
 
			$ret=$con->crearGestionReactivacion($contrato->serie_contrato,
												$contrato->no_contrato,
												$penalidad,
												$_REQUEST['comentario']); 
 			if (!$ret['error']){
				$ret['id_solicitud']=System::getInstance()->Encrypt(json_encode($cb->getSolicitudesAbonoCapitalBy($ret['solicitud'])));	
			}
	 
			echo json_encode($ret);
			
			exit;
		}
		
	}
	exit;
} 

/*CREA LA GESTION PARA REALIZAR EL ABONO A CAPITAL*/
if (isset($_REQUEST['CGAbonoASaldo'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"monto_abono")){ 
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
		
		$monto_abono=$_REQUEST['monto_abono'];		
		if($monto_abono>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("cobros","Cobros");  
			$cb=new Cobros($protect->getDBLink()); 
			$con=new Contratos($protect->getDBLink());  
			
			$ret=$con->crearGestionAbonoASaldo($contrato->serie_contrato,
												$contrato->no_contrato,
												$monto_abono,
												$_REQUEST['comentario'],
												$_REQUEST['identificar']=='1'?1:0); 
 			if (!$ret['error']){
				$ret['id_solicitud']=System::getInstance()->Encrypt(json_encode($cb->getSolicitudesAbonoCapitalBy($ret['solicitud'])));	
			}
	 
			echo json_encode($ret);
			
			exit;
		}
		
	}
	exit;
} 

/*CREA LA GESTION PARA REALIZAR EL CAMBIO DE PLAN SIN ABONO A CAPITAL*/
if (isset($_REQUEST['CGCambioPlan'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"int_financiamiento")
			&& validateField($_REQUEST,"plazo")){ 
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
		 
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("cobros","Cobros");  
		$cb=new Cobros($protect->getDBLink()); 
		$con=new Contratos($protect->getDBLink());  
		$ret=$con->crearGestionAbonoACapSinCambioPlan($contrato->serie_contrato,
											$contrato->no_contrato, 
											$_REQUEST['plazo'],
											$_REQUEST['int_financiamiento'],
											$_REQUEST['comentario']); 
		if (!$ret['error']){
			$ret['id_solicitud']=System::getInstance()->Encrypt(json_encode($cb->getSolicitudesAbonoCapitalBy($ret['solicitud'])));	
		}
 
		echo json_encode($ret);
		
		exit;
	 
		
	}
	exit;
} 

/*CREA LA GESTION PARA REALIZAR EL ABONO A CAPITAL*/
if (isset($_REQUEST['CGAbonoCapital'])){  
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"monto_abono")
			&& validateField($_REQUEST,"plazo")){ 
			
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		if (!$contrato->serie_contrato){
			exit;
		}
		
		$monto_abono=$_REQUEST['monto_abono'];
		
		if($monto_abono>0){
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("cobros","Cobros");  
			$cb=new Cobros($protect->getDBLink()); 
			$con=new Contratos($protect->getDBLink());  
			$ret=$con->crearGestionAbonoCapital($contrato->serie_contrato,
												$contrato->no_contrato,
												$monto_abono,
												$_REQUEST['plazo'],
												$_REQUEST['comentario']); 
 			if (!$ret['error']){
				$ret['id_solicitud']=System::getInstance()->Encrypt(json_encode($cb->getSolicitudesAbonoCapitalBy($ret['solicitud'])));	
			}
	 
			echo json_encode($ret);
			
			exit;
		}
		
	}
	exit;
} 


if (isset($_REQUEST['desitir_contrato'])){  
	/*VERIFICO QUE TENGA PERMISO PARA DESISTIR EL CONTRATO*/
 	if ($protect->getIfAccessPageById(153)){ 
		if (validateField($_REQUEST,"contrato")){
			SystemHtml::getInstance()->includeClass("contratos","Contratos");  
			$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));  
			
			$con=new Contratos($protect->getDBLink());
			echo json_encode($con->doPosibleDesistir($contrato->serie_contrato,$contrato->no_contrato)); 
			exit;
		}
	}else{
		$rt= json_encode(array("valid"=>false,"mensaje"=>"No tiene privilegios para realizar esta operacion!"));	
	}
	echo $rt;
	exit;
}

if (isset($_REQUEST['doChangeOficial'])){  
	/*VERIFICO QUE TENGA PERMISO PARA ANULAR EL CONTRATO*/
	$rt= json_encode(array("valid"=>false,"mensaje"=>"No puede volver a realizar esta operacion!"));
 	if ($protect->getIfAccessPageById(176)){ 
		if (validateField($_REQUEST,"contrato") && validateField($_REQUEST,"oficial") 
			&& validateField($_REQUEST,"motorizado")){
			SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
			$cobros= new Cobros($protect->getDBLINK());
			$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
			$oficial=System::getInstance()->Decrypt($_REQUEST['oficial']);	
			$motorizado=System::getInstance()->Decrypt($_REQUEST['motorizado']);	
			
			$valid=$cobros->cambioDeOficialContrato($contrato,$oficial,$motorizado); 
			$rt= json_encode(array(
									"valid"=>$valid,
									"mensaje"=>"Datos cambiados!"));
		}else{
			$rt= json_encode(array("valid"=>false,"mensaje"=>"No tiene privilegios para realizar esta operacion!"));	
		}
	}else{
		$rt= json_encode(array("valid"=>false,"mensaje"=>"No tiene privilegios para realizar esta operacion!"));
	}
	echo $rt;
	exit;
}

if (isset($_REQUEST['doChangeOficialToRecibo'])){  
	/*VERIFICO QUE TENGA PERMISO PARA ANULAR EL CONTRATO*/
	$rt= json_encode(array("valid"=>false,"mensaje"=>"No puede volver a realizar esta operacion!"));
 //	if ($protect->getIfAccessPageById(176)){ 
 
		if (validateField($_REQUEST,"recibo") && validateField($_REQUEST,"oficial") 
			&& validateField($_REQUEST,"motorizado")){
			SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
			$cobros= new Cobros($protect->getDBLINK());
			
			$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
			$oficial=System::getInstance()->Decrypt($_REQUEST['oficial']);	
			$motorizado=System::getInstance()->Decrypt($_REQUEST['motorizado']);	
 			$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['recibo']));
 
			$valid=$cobros->cambioDeOficialRecibo($recibo->SERIE,$recibo->NO_DOCTO,$oficial,$motorizado); 
			$rt= json_encode(array(
									"valid"=>$valid,
									"mensaje"=>"Datos cambiados!"));
		}else{
			$rt= json_encode(array("valid"=>false,"mensaje"=>"No tiene privilegios para realizar esta operacion!"));	
		}
/*	}else{
		$rt= json_encode(array("valid"=>false,"mensaje"=>"No tiene privilegios para realizar esta operacion!"));
	}*/
	echo $rt;
	exit;
}


if (isset($_REQUEST['doGenerarComentario'])){  
	/*VERIFICO QUE TENGA PERMISO PARA ANULAR EL CONTRATO*/

	if (validateField($_REQUEST,"id_nit")  &&  validateField($_REQUEST,"contrato") ){
		SystemHtml::getInstance()->includeClass("contratos","Contratos");   
		$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);  
		$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));   
		$con=new Contratos($protect->getDBLink());
		echo json_encode($con->doDejarComentario(trim($id_nit),$ct,$_REQUEST['comentario'])); 
		exit;
	 
	}
 
	echo $rt;
	exit;
}

if (isset($_REQUEST['anular_contrato'])){  
	/*VERIFICO QUE TENGA PERMISO PARA ANULAR EL CONTRATO*/
 	if ($protect->getIfAccessPageById(152)){ 
		if (validateField($_REQUEST,"contrato") && validateField($_REQUEST,"motivo")){
			SystemHtml::getInstance()->includeClass("contratos","Contratos");  
			
			if (STCSession::GI()->isSubmit("anular_form")){
				
				$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));   
				$con=new Contratos($protect->getDBLink());
				 
				echo json_encode($con->doPosibleAnular($contrato->serie_contrato,
													   $contrato->no_contrato,
													   $_REQUEST['motivo'],
													   $_REQUEST['comentario'])); 
				exit;
			}else{
				$rt= json_encode(array("valid"=>false,"mensaje"=>"No puede volver a realizar esta operacion!"));	
			}
		}
	}else{
		$rt= json_encode(array("valid"=>false,"mensaje"=>"No tiene privilegios para realizar esta operacion!"));	
	}
	echo $rt;
	exit;
}

/*VENTANA QUE GENERA UNA GESTION*/
if (isset($_REQUEST['doGenerateGestion'])){  
	include("contrato/view/view_create_gestion.php"); 
	exit;
} 

/*VENTANA POR DESISTIR*/
if (isset($_REQUEST['view_desistir'])){  
	include("contrato/view/view_desisitir.php"); 
	exit;
} 

if (isset($_REQUEST['doViewChangeOficial'])){  
	include("contrato/view/view_change_oficial.php"); 
	exit;
} 

if (isset($_REQUEST['doViewChangeOficialMotoFromRecibo'])){  
	include("contrato/view/view_change_of_moto_recibo.php"); 
	exit;
} 
/*VENTANA DE COMENTARIO*/
if (isset($_REQUEST['view_comentario'])){  
	include("contrato/view/view_comentary.php"); 
	exit;
} 

/*VENTANA ANULAR*/
if (isset($_REQUEST['view_anular'])){  
	include("contrato/view/view_anular.php"); 
	exit;
} 

 /*MODULO DE UBICACION DE CLIENTES*/
if (isset($_REQUEST['ubicacion'])){  
	include("identificacion/listadoPuntos.php"); 
	exit;
} 

 /*MODULO DE UBICACION DE CLIENTES*/
if (isset($_REQUEST['listarRuta'])){  
	include("identificacion/listar_todos_puntos.php"); 
	exit;
} 
/*MAIN DASHBOARD COBRO*/
if (isset($_REQUEST['dashboard'])){  
	include("dashboard/dashboard.php"); 
	exit;
} 

/*MODULO DE ZONIFICACION*/
if (isset($_REQUEST['metas'])){  
	include("metas/metac.php"); 
	exit;
} 

/*MODULO DE ZONIFICACION*/
if (isset($_REQUEST['zonas'])){  
	include("zona/zonas.php"); 
	exit;
} 

/*DETALLE CONTRATO*/
if (isset($_REQUEST['detalle_contrato_view'])){ 
	include("contrato/info.php"); 
	exit;
} 
/*MAIN CONTRATO COBRO*/
if (isset($_REQUEST['contrato_view'])){  
	include("contrato/detalle_contrato.php"); 
	exit;
} 

/*MAIN labor COBRO*/
if (isset($_REQUEST['labor_cobro'])){  
	include("contrato/labor_cobro.php"); 
	exit;
} 

/*MAIN REQUERIMIENTO DE COBRO*/
if (isset($_REQUEST['crequerimiento'])){  
	include("contrato/view/requerimiento_c.php"); 
	exit;
} 
/*MAIN AVISO COBRO*/
if (isset($_REQUEST['aviso_cobro'])){  
	include("contrato/aviso_cobro.php"); 
	exit;
} 
/*VENTANA PARA APLICAR EL FILTRO DE COBRO*/
if (isset($_REQUEST['filtro_cobro'])){  
	include("dashboard/filtro.php"); 
	exit;
} 


/*CARTERA DE COBRO ASIGNADA*/
if (isset($_REQUEST['listado_cartera'])){  
	include("dashboard/cartera_cobro_asignada.php"); 
	exit;
} 



 /*MAIN GENERAR ACTIVIDAD*/
if (isset($_REQUEST['get_act'])){  
	include("mantenimiento/gestion_actividad.php"); 
	exit;
}
 /*MAIN TIPO DE COBRO*/
if (isset($_REQUEST['accion_cobro'])){  
	include("mantenimiento/acciones_cobro.php"); 
	exit;
}

/* VIEW CREAR GESTION*/
if (isset($_REQUEST['gestion_add'])){  
	include("mantenimiento/view/gestion_add.php"); 
	exit;
}

/* VIEW EDITAR GESTION*/
if (isset($_REQUEST['gestion_edit'])){  
	include("mantenimiento/view/gestion_edit.php"); 
	exit;
}

/* VIEW CREAR ACTIVIDAD*/
if (isset($_REQUEST['actividad_add'])){  
	include("mantenimiento/view/actividad_add.php"); 
	exit;
}
/* Listado de requerimiento de  cobros*/
if (isset($_REQUEST['requerimiento'])){  
	include("dashboard/listado_requerimiento_c.php"); 
	exit;
}

/* VIEW EDITAR ACTIVIDAD*/
if (isset($_REQUEST['actividad_edit'])){  
	include("mantenimiento/view/actividad_edit.php"); 
	exit;
}

/*CARGAR LISTADO DE ACTIVIDADES*/
if (isset($_REQUEST['charge_list_actividad'])){  
	include("contrato/view/actividad_n.php"); 
	exit;
} 

/*MANTENIMIENTO PROCESO DE CREACION GESTION*/
if (isset($_REQUEST['processGestion'])){   
	if (validateField($_REQUEST,"idtipogestion")&& validateField($_REQUEST,"gestion")
		&& validateField($_REQUEST,"Tiempo_max") ){
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		$cobros= new Cobros($protect->getDBLINK()); 
		if (!$cobros->validarCodigoGestion($_REQUEST['idtipogestion'])){
 		 
			$escala=json_decode(System::getInstance()->Decrypt($_REQUEST['escalamiento1_code']));
			$escala2=json_decode(System::getInstance()->Decrypt($_REQUEST['escalamiento2_code']));
			
			$obj= new ObjectSQL();
			$obj->idtipogestion=$_REQUEST['idtipogestion'];
			$obj->gestion=$_REQUEST['gestion'];
			$obj->genera_actividad=$_REQUEST['genera_actividad'];
			$obj->Tiempo_max=$_REQUEST['Tiempo_max'];
			$obj->escalamiento1=$escala->id_nit;
			$obj->escalamiento2=$escala2->id_nit;
			$obj->setTable('tipos_gestiones');
			$SQL=$obj->toSQL("insert"); 
			 
			mysql_query($SQL);
			$data=array("valid"=>false,"mensaje"=>"Gestión Creada"); 
		}else{
			$data=array("valid"=>true,"mensaje"=>"Este codigo de gestion existe, el sistema no acepta duplicados"); 
		}
		echo json_encode($data); 
		
	}
	exit;
}

/*MANTENIMIENTO PROCESO DE EDITAR GESTION*/
if (isset($_REQUEST['processEditGestion'])){   

	if (validateField($_REQUEST,"id_gestion")&& validateField($_REQUEST,"gestion")&& validateField($_REQUEST,"genera_actividad")&& validateField($_REQUEST,"Tiempo_max") ){
		
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		$cobros= new Cobros($protect->getDBLINK()); 
		$row=json_decode(System::getInstance()->Decrypt($_REQUEST['id_gestion']));
		 
		if ($cobros->validarCodigoGestion($row->idtipogestion)){
			$data=array("valid"=>false,"mensaje"=>"Este codigo de gestion existe, el sistema no acepta duplicados");
		 
			$escala=json_decode(System::getInstance()->Decrypt($_REQUEST['escalamiento1_code']));
			$escala2=json_decode(System::getInstance()->Decrypt($_REQUEST['escalamiento2_code']));
			
			if (isset($escala->id_nit)){
				$escala	=$escala->id_nit;
			}
			if (isset($escala2->id_nit)){
				$escala2=$escala2->id_nit;
			} 
			 
			$obj= new ObjectSQL();
		//	$obj->idtipogestion=$_REQUEST['idtipogestion'];
			$obj->gestion=$_REQUEST['gestion'];
			$obj->genera_actividad=$_REQUEST['genera_actividad'];
			$obj->Tiempo_max=$_REQUEST['Tiempo_max'];
			$obj->escalamiento1=$escala;
			$obj->escalamiento2=$escala2;
			$obj->setTable('tipos_gestiones');
			$SQL=$obj->toSQL("update"," where idtipogestion='".$row->idtipogestion."'"); 
 
			mysql_query($SQL);
			$data=array("valid"=>false,"mensaje"=>"Registro Actualizado"); 
		}else{
			$data=array("valid"=>true,"mensaje"=>"Este codigo de gestion existe, el sistema no acepta duplicados"); 
		}
		echo json_encode($data); 
		
	}
	exit;
}

 /*MODULO DE UBICACION DE CLIENTES PROCESAR LOS DATOS*/
if (isset($_REQUEST['processIdentificado'])){  
	if (validateField($_REQUEST,"id")&& validateField($_REQUEST,"direccion")
		&& validateField($_REQUEST,"contrato")  ){
		$id=System::getInstance()->Decrypt($_REQUEST['id']);
		
		$SQL="SELECT * FROM `localizacion_cobro_cliente` WHERE id='". mysql_real_escape_string($id) ."' AND estatus!='IDENTIFICADO'"; 
		$rs=mysql_query($SQL); 
		while($row=mysql_fetch_assoc($rs)){  
			$obj= new ObjectSQL();
			$obj->contrato=$_REQUEST['contrato'];
			$obj->direccion=$_REQUEST['direccion'];
			$obj->estatus='IDENTIFICADO';
			$obj->fecha="CURDATE()";
			$obj->usuario=$protect->getIDNIT();
			$obj->setTable("localizacion_cobro_cliente");
			$SQL=$obj->toSQL("update"," WHERE id='". mysql_real_escape_string($id) ."'");
			mysql_query($SQL);
		}
		$data=array("valid"=>false,"mensaje"=>"Datos actualizados");  
			
	}else{
		$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
	}
	echo json_encode($data);
	 
	exit;
} 
 /*MODULO DE UBICACION DE CLIENTES PROCESAR LOS DATOS NO ES PARTE DE LA RUTA*/
if (isset($_REQUEST['processNoIdentificado'])){  
	if (validateField($_REQUEST,"id") && validateField($_REQUEST,"descripcion")  ){
		$id=System::getInstance()->Decrypt($_REQUEST['id']); 
		
		$SQL="SELECT * FROM `localizacion_cobro_cliente` WHERE id='". mysql_real_escape_string($id) ."' AND estatus!='IDENTIFICADO'"; 
		$rs=mysql_query($SQL); 
		while($row=mysql_fetch_assoc($rs)){  
			$obj= new ObjectSQL(); 
			$obj->estatus='INVALIDO';
			$obj->fecha="CURDATE()";
			$obj->descripcion=$_REQUEST['descripcion'];
			$obj->usuario=$protect->getIDNIT();
			$obj->setTable("localizacion_cobro_cliente");
			$SQL=$obj->toSQL("update"," WHERE id='". mysql_real_escape_string($id) ."'");
			mysql_query($SQL);
		}
		$data=array("valid"=>false,"mensaje"=>"Datos actualizados");  
			
	}else{
		$data=array("valid"=>false,"mensaje"=>"No se pudo realizar la operacion"); 	
	}
	echo json_encode($data);
	 
	exit;
}

/*
	Agrega una cuota al carrito de cuotas
*/
if (isset($_REQUEST['processPutPago'])){ 
	if (validateField($_REQUEST,"cuota") && validateField($_REQUEST,"cmd")  
		&& validateField($_REQUEST,"token") && validateField($_REQUEST,"contrato") ){
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		$cobros= new Cobros($protect->getDBLINK());   
		$cobros->setToken($_REQUEST['token']);
		if ($cobros->doItem($_REQUEST['token'],$_REQUEST['cuota'],$_REQUEST['cmd'])){
			$info=$cobros->getItem($_REQUEST['token']);
			 
			$monto=0; 
			foreach($info as $key=>$val){
				$monto=$monto+$val->monto_neto;	
			}
			
			$total=count($info);
			$data=array(
						"valid"=>true,
						"mensaje"=>"Datos actualizados",
						"data"=>array(
										"monto"=>$monto,
									  	"no_cuota"=>$total
									  )
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



/*PROCESO DE CREACION DE ACTIVIDADES MANTENIMIENTO*/
if (isset($_REQUEST['processActividadAdd'])){ 

	if (validateField($_REQUEST,"idtipoact")&& validateField($_REQUEST,"actividad")
		&& validateField($_REQUEST,"act_int_ext")&& validateField($_REQUEST,"orden")
		&& validateField($_REQUEST,"act_tiempo_max")&& validateField($_REQUEST,"act_id_gestion")
		&& validateField($_REQUEST,"asignar_actividad_a") ){
		
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		$cobros= new Cobros($protect->getDBLINK()); 
		$gestion=json_decode(System::getInstance()->Decrypt($_REQUEST['act_id_gestion']));
		 
		if (!$cobros->validarCodigoActividad($_REQUEST['idtipoact'],$gestion->idtipogestion)){
		  
			$escala=json_decode(System::getInstance()->Decrypt($_REQUEST['act_escalamiento1_code']));
			$escala2=json_decode(System::getInstance()->Decrypt($_REQUEST['act_escalamiento2_code']));
			
			$obj= new ObjectSQL();
			$obj->idtipogestion=$gestion->idtipogestion;
			$obj->idtipoact=$_REQUEST['idtipoact'];
			$obj->actividad=$_REQUEST['actividad'];
			$obj->asignar_actividad_a=$_REQUEST['asignar_actividad_a'];
			$obj->orden=$_REQUEST['orden'];
			$obj->tiempo_max=$_REQUEST['act_tiempo_max'];
			$obj->escalamiento1=$escala->id_nit;
			$obj->escalamiento2=$escala2->id_nit;
			$obj->act_int_ext=$_REQUEST['act_int_ext'];
			$obj->setTable('tipo_actividades');
			$SQL=$obj->toSQL("insert"); 
			 
		 	mysql_query($SQL);
			$data=array("valid"=>false,"mensaje"=>"Actividad Creada"); 
		}else{
			$data=array("valid"=>true,"mensaje"=>"Este codigo de gestion existe, el sistema no acepta duplicados"); 
		}
		echo json_encode($data); 
		
	}
	exit;
}

/*PROCESO DE EDITAR DE ACTIVIDADES MANTENIMIENTO*/
if (isset($_REQUEST['processActividadEdit'])){ 

	if (validateField($_REQUEST,"actividad")&& validateField($_REQUEST,"act_int_ext")
		&& validateField($_REQUEST,"orden")&& validateField($_REQUEST,"act_tiempo_max")
		&& validateField($_REQUEST,"act_id_gestion") && validateField($_REQUEST,"asignar_actividad_a")){
			
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		$cobros= new Cobros($protect->getDBLINK()); 
		$gestion=json_decode(System::getInstance()->Decrypt($_REQUEST['act_id_gestion']));
		 
		if ($cobros->validarCodigoActividad($gestion->idtipoact,$gestion->idtipogestion)){
		  
			$escala=json_decode(System::getInstance()->Decrypt($_REQUEST['act_escalamiento1_code']));
			$escala2=json_decode(System::getInstance()->Decrypt($_REQUEST['act_escalamiento2_code']));
			
			if (isset($escala->id_nit)){
				$escala=$escala->id_nit;	
			}else{
				$escala=System::getInstance()->Decrypt($_REQUEST['act_escalamiento1_code']);
			}
			if (isset($escala2->id_nit)){
				$escala2=$escala2->id_nit;	
			}else{
				$escala2=System::getInstance()->Decrypt($_REQUEST['act_escalamiento2_code']);
			}			 
			
			$obj= new ObjectSQL(); 
		 
			$obj->actividad=$_REQUEST['actividad'];
			$obj->orden=$_REQUEST['orden'];
			$obj->tiempo_max=$_REQUEST['act_tiempo_max'];
			$obj->asignar_actividad_a=$_REQUEST['asignar_actividad_a'];
			$obj->escalamiento1=$escala;
			$obj->escalamiento2=$escala2;
			$obj->act_int_ext=$_REQUEST['act_int_ext'];
			$obj->setTable('tipo_actividades');
			$SQL=$obj->toSQL("update"," where idtipoact='".$gestion->idtipoact."'"); 
		 
		 	mysql_query($SQL);
			$data=array("valid"=>false,"mensaje"=>"Actividad actualizada"); 
		}else{
			$data=array("valid"=>true,"mensaje"=>"Este codigo de gestion no existe!"); 
		}
		echo json_encode($data); 
		
	}
	exit;
}


/* VIEW CREAR ACCION*/
if (isset($_REQUEST['accion_add'])){  
	include("mantenimiento/view/accion_add.php"); 
	exit;
}

/* VIEW EDITAR ACCION*/
if (isset($_REQUEST['accion_edit'])){  
	include("mantenimiento/view/accion_edit.php"); 
	exit;
}

/*MANTENIMIENTO PROCESO DE CREACION Accion*/
if (isset($_REQUEST['processAccion'])){   
	if (validateField($_REQUEST,"idaccion")&& validateField($_REQUEST,"accion") ){
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		$cobros= new Cobros($protect->getDBLINK()); 
		
		if (!$cobros->validarCodigoAcccion($_REQUEST['idaccion'])){
		 	$gen_gestion="N";
	 		if (isset($_REQUEST['gen_gestion'])){
				$gen_gestion=$_REQUEST['gen_gestion']==1?'S':'N';	
			}
	 
			$obj= new ObjectSQL();
			$obj->idaccion=$_REQUEST['idaccion'];
			$obj->accion=$_REQUEST['accion'];
			$obj->gen_gestion=$gen_gestion;
			$obj->setTable('acciones_cobros');
			$SQL=$obj->toSQL("insert");  
			mysql_query($SQL);
			$data=array("valid"=>false,"mensaje"=>"Accion Creada"); 
		}else{
			$data=array("valid"=>true,"mensaje"=>"Este codigo de Accion existe, el sistema no acepta duplicados"); 
		}
		echo json_encode($data); 
		
	}
	exit;
}


/*MANTENIMIENTO PROCESO DE EDITAR Accion*/
if (isset($_REQUEST['processAccionEdit'])){   
	if (validateField($_REQUEST,"id_accion")&& validateField($_REQUEST,"accion") ){
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		$cobros= new Cobros($protect->getDBLINK()); 
		$accion=json_decode(System::getInstance()->Decrypt($_REQUEST['id_accion'])); 

		
		if ($cobros->validarCodigoAcccion($accion->idaccion)){
		 	$gen_gestion="N";
	 		if (isset($_REQUEST['gen_gestion'])){
				$gen_gestion=$_REQUEST['gen_gestion']==1?'S':'N';	
			}
	 		

			$obj= new ObjectSQL(); 
			$obj->accion=$_REQUEST['accion'];
			$obj->gen_gestion=$gen_gestion;
			$obj->setTable('acciones_cobros');
			$SQL=$obj->toSQL("update"," where idaccion='".$accion->idaccion."'");  
			mysql_query($SQL);
		 
			$data=array("valid"=>false,"mensaje"=>"Editada Creada"); 
		}else{
			$data=array("valid"=>true,"mensaje"=>"Este codigo de Accion existe, el sistema no acepta duplicados"); 
		}
		echo json_encode($data); 
		
	}
	exit;
}


/* PROCESAR LABOR DE COBRO */
if (isset($_REQUEST['processLaborCobro'])){    
	if (validateField($_REQUEST,"accion") && validateField($_REQUEST,"lb_contacto") 
	&& validateField($_REQUEST,"lb_comentario_cliente") && validateField($_REQUEST,"lb_fecha_contacto")
	 && validateField($_REQUEST,"contrato") ){
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		SystemHtml::getInstance()->includeClass("contratos","Contratos");   
		
		$con=new Contratos($protect->getDBLink()); 
		$cobros= new Cobros($protect->getDBLINK()); 
		
		$accion=json_decode(System::getInstance()->Decrypt($_REQUEST['accion'])); 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
		
		$tipo_gestion=json_decode(System::getInstance()->Decrypt($_REQUEST['tipo_gestion'])); 
		
		$doBitacora=$_REQUEST['tobitacora']=="1"?1:0; 
	 
		if (!$cobros->validarCodigoAcccion($accion->idaccion)){
			$data=array("valid"=>true,"mensaje"=>"Error: este tipo de acción no existe!");  
			echo json_encode($data); 	
			exit;	
		}
		$lleva_gestion=false;
		/*VERIFICO SI LA ACCION LLEVA OBLIGATORIO UNA GESTION */
		if ($accion->gen_gestion=="S"){
			$lleva_gestion=true;
			if (isset($_REQUEST['isTipoGestion'])){
				if ($_REQUEST['isTipoGestion']!="S"){
					$data=array("valid"=>true,"mensaje"=>"Error: esta accion debe de ir acompañada de una gestion!");  
					echo json_encode($data); 	
					exit;
				}
			}else{
				$data=array("valid"=>true,"mensaje"=>"Error: esta accion debe de ir acompañada de una gestion!");  
				echo json_encode($data); 	
				exit;
			}
		}
		   
		//print_r($_REQUEST);
		/*PROCESO SI LA ACTIVIDAD SE LE GENERA UNA ACTIVIDAD*/
		if ($_REQUEST['isTipoGestion']=="S"){
			$lleva_gestion=true;
			if (!isset($tipo_gestion->idtipogestion)){
				$data=array("valid"=>true,"mensaje"=>"Error: debe de seleccionar un tipo de gestion");  
				echo json_encode($data); 	
				exit;
			} 
			
			if (!$cobros->validarCodigoGestion($tipo_gestion->idtipogestion)){
				$data=array("valid"=>true,"mensaje"=>"Error: este tipo de gestion no existe!");  
				echo json_encode($data); 	
				exit;	
			}
			 
			/*CAPTURO EL LISTADO DE ACTIVIADES*/
			$list_actividad=$cobros->getListActividad($tipo_gestion->idtipogestion);	 
			$obj_actividad=array();
			if (isset($_REQUEST['actividad_responsable'])){ 
				$actv=json_decode($_REQUEST['actividad_responsable']);
				$error=0;
			 
				if (!(count($actv)==count($list_actividad))){
					$error=11;
				}
				if ((!is_array($actv))){
					$error=11;
				} 
//				print_r($actv);
				$auto=$cobros->getTotalActividadGestion();
 				foreach($actv as $key =>$val){ 
					if (!isset($val->responsable)){
						$error=11;
						break;	
					}
					if (!isset($val->actividad)){
						$error=11;	
					}
					if (!isset($val->fecha)){
						$error=11;	
					}
					if ($val->fecha==""){
						$error=11;	
					}	
									 
					$act_de=json_decode(System::getInstance()->Decrypt($val->actividad->actividad)); 
					$responsable=json_decode(System::getInstance()->Decrypt($val->responsable)); 
				 
					 
					/*VALIDO LOS RESPONSABLES*/
					if (!isset($responsable->id_nit)){
						$error=11;
						break;
					}  
					/*VALIDO QUE LA ACTIVIDAD EXISTA EN EL LISTADO*/
					$act_exist=true;
					foreach($list_actividad as $nkey =>$nval){ 
						if ($act_de->idtipoact==$nval->idtipoact){
							$act_exist=false;
							break;
						}
					}
					
					/*SI FALTA ALGUNA ACTIVIADA NO PROCEDER*/
					if (!$act_exist){
						$error=11;
					}else{  
						$obj=new ObjectSQL();
						$obj->idtipogestion=$act_de->idtipogestion;
						$obj->idgestion=$act_de->idtipogestion.$auto;
						$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
						$obj->idtipoact=$act_de->idtipoact;
						$obj->autorizado="";
						$obj->responsable=$responsable->id_nit;
						$obj->id_status="1";	
						$obj->fecha_realizar="STR_TO_DATE('".$val->fecha."','%d-%m-%Y')";	
						$obj->orden_actividad=$act_de->orden;	
						$obj->setTable("actividades_gestion");
						array_push($obj_actividad,$obj);   
						$auto=$auto+1;
					}
					

				}	
				
			}else{ $error=11;}
			
			if ($error==11){
				$data=array("valid"=>true,"mensaje"=>"Error: no ha completado de llenar el listado de actividades!");
				echo json_encode($data); 	 
				exit;
			}
		
		}
 
		$hora="00:00:00";
		if (isset($_REQUEST['lb_hora'])){
			$hora=str_replace("AM","",$_REQUEST['lb_hora']);
			$hora=str_replace("PM","",$hora);			
		}
		
		/*INSERTO LA LABOR DE COBRO*/
		$obj= new ObjectSQL(); 
		$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$obj->EM_ID=$cdata->EM_ID;
		$obj->no_contrato=$contrato->no_contrato;
		$obj->serie_contrato=$contrato->serie_contrato;
		$obj->contacto=$_REQUEST['lb_contacto'];		
		$obj->observaciones=mysql_escape_string($_REQUEST['lb_observacion']);	
		$obj->comentario_cliente=mysql_escape_string($_REQUEST['lb_comentario_cliente']);			
		$obj->proximo_contacto="CONCAT(STR_TO_DATE('".$_REQUEST['lb_fecha_contacto']."','%d-%m-%Y'),' ','".$hora."')";		
		$obj->id_nit_cliente=$contrato->id_nit;
		$obj->idaccion=$accion->idaccion; 
		$obj->estatus=19; 
		$obj->incluirBitacora=$doBitacora;
		$obj->oficial_cobro=UserAccess::getInstance()->getIDNIT();
		$obj->setTable('labor_cobro');
		$SQL=$obj->toSQL("insert");  
		mysql_query($SQL);
		
		$ob=new ObjectSQL();
		$ob->comentario_cliente= mysql_escape_string($_REQUEST['lb_comentario_cliente']);
		$ob->proximo_contacto="CONCAT(STR_TO_DATE('".$_REQUEST['lb_fecha_contacto']."','%d-%m-%Y'),' ','".$hora."')";
	//	$ob->cuotas_acobrar=$rowx['cuotas_acobrar'];
		$ob->setTable("cobros_contratos");
	/*	$SQL=$ob->toSQL("update"," where serie_contrato='".$contrato->serie_contrato."' and 
			no_contrato='".$contrato->no_contrato."' ");*/
		$SQL=$ob->toSQL("update"," where id_nit_cliente='".$contrato->id_nit."' ");  			
		mysql_query($SQL);			
		///////////////////////////////////////////////////
	//LABOR_COBRO_2
		if ($lleva_gestion){
			
			$_obj= new ObjectSQL(); 
			$_obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$_obj->idgestion=$tipo_gestion->idtipogestion;
			$_obj->idtipogestion=$tipo_gestion->idtipogestion;
			$_obj->responsable=UserAccess::getInstance()->getIDNIT();
			//$_obj->id_nit=UserAccess::getInstance()->getIDNIT();
			$_obj->EM_ID=$cdata->EM_ID;
			$_obj->no_contrato=$contrato->no_contrato;
			$_obj->serie_contrato=$contrato->serie_contrato;
			$_obj->id_status="1";
			$_obj->descrip_general=""; 
			$_obj->setTable('gestiones');
			$SQL=$_obj->toSQL("insert");   
			mysql_query($SQL);
		//	print_r($_obj);
			
			foreach($obj_actividad as $key =>$obj){
				$SQL=$obj->toSQL("insert"); 
				mysql_query($SQL);	
			}
		
		}			
		$data=array("valid"=>false,"mensaje"=>"Labor Creada");  
		echo json_encode($data); 
		
	}else{
		$data=array("valid"=>true,"mensaje"=>"Error debe de completar los datos");  
		echo json_encode($data); 	
	}
	exit;
}


if (isset($_REQUEST['procesarRequerimientoc'])){  
	$msg=array("valid"=>false,"mensaje"=>"Error debe de completar los datos"); 
	if (validateField($_REQUEST,"abono") && validateField($_REQUEST,"cantidad")
	&& validateField($_REQUEST,"tmov") && validateField($_REQUEST,"contrato") && validateField($_REQUEST,"motorisado")
	&& validateField($_REQUEST,"oficial") 	&& validateField($_REQUEST,"fecha_requerimiento")
		&& validateField($_REQUEST,"req_direccion") ){
		SystemHtml::getInstance()->includeClass("cobros","Cobros");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		$cobros= new Cobros($protect->getDBLINK()); 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		$con=new Contratos($protect->getDBLink()); 
		$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);	 
		$mov=json_decode(System::getInstance()->Decrypt($_REQUEST['tmov'])); 
		$motorisado=json_decode(System::getInstance()->Decrypt($_REQUEST['motorisado'])); 
		$direccion_cobro=json_decode(System::getInstance()->Decrypt($_REQUEST['req_direccion']));  
		$oficial=json_decode(System::getInstance()->Decrypt($_REQUEST['oficial']));  
		echo json_encode($cobros->doGenerarRequerimiento($cdata,$mov,$_REQUEST['cantidad'],
														$motorisado,$oficial,
														$_REQUEST['fecha_requerimiento'],
														$direccion_cobro->id_direcciones,
														$_REQUEST['comentario']));
	}else{
		echo json_encode($msg);
	}
	exit;
}
/* PROCESAR AVISO DE COBRO */
if (isset($_REQUEST['processAvisoCobro'])){   
		$data=array("valid"=>true,"mensaje"=>"Error debe de completar los datos");  
 
	if (validateField($_REQUEST,"lb_contacto") 
	&& validateField($_REQUEST,"lb_comentario_cliente") && validateField($_REQUEST,"lb_fecha_contacto")
	 && validateField($_REQUEST,"contrato")  && validateField($_REQUEST,"token")){
		SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		SystemHtml::getInstance()->includeClass("contratos","Contratos");   
		
		
	
		$con=new Contratos($protect->getDBLink()); 
		$cobros= new Cobros($protect->getDBLINK()); 
		$caja= new Caja($protect->getDBLINK()); 
		 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
    
		/*CAPTURO EL LISTADO DE ACTIVIADES*/
		$list_actividad=$cobros->getListActividad('AVICO');	 
		$obj_actividad=array();
	
		if (isset($_REQUEST['actividad_responsable'])){ 
			$actv=json_decode($_REQUEST['actividad_responsable']);
			$error=0;
		 
			if (!(count($actv)==count($list_actividad))){
				$error=11;
			}
			if ((!is_array($actv))){
				$error=11;
			} 
 
			$auto=$cobros->getTotalActividadGestion();
			$id_gestion='AVICO'.$auto;
			
			foreach($actv as $key =>$val){ 
			
				if (!isset($val->responsable)){
					$error=11;
					break;	
				}
				if (!isset($val->actividad)){
					$error=11;	
				}
				if (!isset($val->fecha)){
					$error=11;	
				}
				if ($val->fecha==""){
					$error=11;	
				}	
				 
			 	
				$act_de=System::getInstance()->Decrypt($val->actividad); 
				$responsable=System::getInstance()->Decrypt($val->responsable); 
				  
				/*VALIDO LOS RESPONSABLES*/
				if (!isset($responsable)){
					$error=11;
					break;
				}  
				/*VALIDO QUE LA ACTIVIDAD EXISTA EN EL LISTADO*/
				$act_exist=false;  
				foreach($list_actividad as $nkey =>$nval){  
					if ($act_de==$nval['idtipoact']){
						$act_exist=true;
						break; 
					} 
				}
				 
				/*SI FALTA ALGUNA ACTIVIADA NO PROCEDER*/
				if (!$act_exist){
					$error=11;
				}else{  
				 	
					$obj=new ObjectSQL();
					$obj->idtipogestion='AVICO';
					$obj->idgestion=$id_gestion;
					$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
					$obj->idtipoact=$act_de;
					$obj->autorizado="";
					$obj->responsable=$responsable;
					$obj->id_status="1";	
					$obj->fecha_realizar="STR_TO_DATE('".$val->fecha."','%d-%m-%Y')";	
					$obj->orden_actividad=$act_de->orden;	
					$obj->setTable("actividades_gestion"); 
					array_push($obj_actividad,$obj);     
				}
				
		
			}	
			
		}else{ $error=11;}
		
		if ($error==11){
			$data=array("valid"=>true,"mensaje"=>"Error: no ha completado de llenar el listado de actividades!");
			echo json_encode($data); 	 
			exit;
		} 
 
		$hora="00:00:00";
		if (isset($_REQUEST['lb_hora'])){
			$hora=str_replace("AM","",$_REQUEST['lb_hora']);
			$hora=str_replace("PM","",$hora);			
		}
		 
		$info=$cobros->getItem($_REQUEST['token']); 
		$monto=0; 
		if (count($info)>0){
			print_r($obj_actividad);
			
			exit;
			
			$docto=MVFactura::getInstance()->doCreateReciboRequerimiento($cdata->id_nit_cliente,
														$cdata->EM_ID,
														$cdata->no_contrato,
														$cdata->serie_contrato,
														'CUOTA',
														RECIBO_VIRTUAL, //RECIBO CAJA VIRTUAL  
														$_REQUEST['lb_observacion'], 
														'',
														$info
														);				
			exit;
			foreach($info as $key=>$val){ 
				$monto=$monto+$val->monto_neto;	  
			} 
			$total=count($info); 
			$interes=($data['monto']*$cdata->porc_interes/100);
			$capital=$data['monto']-$interes;
			
			//$correlativo=$caja->getNoCorrelativoDoc(RECIBO_VIRTUAL,'RC');
			print_r($contrato);
			exit;
			/*INSERTO LA LABOR DE COBRO*/
			$obj= new ObjectSQL(); 
			$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->EM_ID=$cdata->EM_ID;
			$obj->no_contrato=$contrato->no_contrato;
			$obj->serie_contrato=$contrato->serie_contrato;
			$obj->contacto=$_REQUEST['lb_contacto'];		
			$obj->observaciones=$_REQUEST['lb_observacion'];	
			$obj->comentario_cliente=$_REQUEST['lb_comentario_cliente'];			
			$obj->proximo_contacto="CONCAT(STR_TO_DATE('".$_REQUEST['lb_fecha_contacto']."','%d-%m-%Y'),' ','".$hora."')";		
			$obj->idaccion='MOTO'; 
			$obj->cuotas_acobrar=$total;
			$obj->monto_acobrar=$monto;
			$obj->mora_acobrar='0';
			$obj->mante_acobrar=0;
			$obj->aviso_cobro=$correlativo;
			$obj->serie='RC';
			$obj->fecha_cobro="CONCAT(STR_TO_DATE('".$_REQUEST['lb_fecha_contacto']."','%d-%m-%Y'),' ','".$hora."')";		
			$obj->estatus=19; 
			$obj->oficial_cobro=UserAccess::getInstance()->getIDNIT();
			$obj->setTable('labor_cobro');
			$SQL=$obj->toSQL("insert");   
			print_r($obj);
	//	 	mysql_query($SQL);	
			SysLog::getInstance()->Log($cdata->id_nit_cliente, 
										 $contrato->serie_contrato,
										 $contrato->no_contrato,
										 '',
										 '',
										 "REQUERIMIENTO DE COBRO GENERADO",
										 json_encode($obj),
										 'REQUERIMIENTO');			 
	 		
		}else{
			$data=array("valid"=>true,"mensaje"=>"Error: no ha completado de llenar el listado de actividades!");
			echo json_encode($data); 	 
			exit;	
		}
	 
		///////////////////////////////////////////////////
		exit;
		$_obj= new ObjectSQL(); 
		$_obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$_obj->idgestion=$id_gestion;
		$_obj->idtipogestion='AVICO';
		$_obj->responsable=UserAccess::getInstance()->getIDNIT();
		//$_obj->id_nit=UserAccess::getInstance()->getIDNIT();
		$_obj->EM_ID=$cdata->EM_ID;
		$_obj->no_contrato=$contrato->no_contrato;
		$_obj->serie_contrato=$contrato->serie_contrato;
		$_obj->id_status="19";
		$_obj->descrip_general=""; 
		$_obj->setTable('gestiones');
		$SQL=$_obj->toSQL("insert");   
		mysql_query($SQL);

		foreach($obj_actividad as $key =>$obj){
			$SQL=$obj->toSQL("insert"); 
			mysql_query($SQL);	
		}
		
	 			
		$data=array("valid"=>false,"mensaje"=>"Aviso de cobro generado!");  
		echo json_encode($data); 
		
	}else{
		$data=array("valid"=>true,"mensaje"=>"Error debe de completar los datos");  
		echo json_encode($data); 	
	}
	exit;
}

 
 
  
  
?>