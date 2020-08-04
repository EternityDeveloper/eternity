<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
SystemHtml::getInstance()->includeClass("contratos","Carrito");  
SystemHtml::getInstance()->includeClass("caja","Caja"); 


/*APLICA UN CAMBIO DE AJUSTE A UN CONTRATO QUE ESTA MAL CARGADO*/
if (isset($_REQUEST['doAplicarAjusteContrato']) && 
						validateField($_REQUEST,'id') &&
						validateField($_REQUEST,'cuotas')&&
						validateField($_REQUEST,'por_interes')){

	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	 
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	$con=new Contratos($protect->getDBLink());  
	$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
	
	$por_interes=$_REQUEST['por_interes'];
	$cuotas=$_REQUEST['cuotas'];	
	
	if (count($cdata)<=0){
		exit;	
	}	 
	$ci=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);		
	$capital_financiar=$ci->capital_total-$ci->INICIAL;  
	$interes=(($capital_financiar*$por_interes/100)*($cuotas/12)); 
	$monto_cuota=round(($capital_financiar/$cuotas)+($interes/$cuotas),2);
	
	$obj= new ObjectSQL(); 
	$obj->valor_cuota=$monto_cuota;
 	$obj->interes=$interes;
	$obj->cuotas=$cuotas;
	$obj->porc_interes=$por_interes;
	
	$obj->setTable('contratos');	
	$SQL=$obj->toSQL('update'," where no_contrato='".$contrato->no_contrato."' 
					and serie_contrato='".$contrato->serie_contrato."'");						
	mysql_query($SQL); 
	
	
	echo json_encode(array("capital_financiar"=>$capital_financiar,"interes"=>$interes,"monto_cuota"=>$monto_cuota));
	exit;
}	
 
if (isset($_REQUEST['doCalcularAjusteContrato']) && 
						validateField($_REQUEST,'id') &&
						validateField($_REQUEST,'cuotas')&&
						validateField($_REQUEST,'por_interes')){

	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	 
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	$con=new Contratos($protect->getDBLink());  
	$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
	
	$por_interes=$_REQUEST['por_interes'];
	$cuotas=$_REQUEST['cuotas'];	
	
	if (count($cdata)<=0){
		exit;	
	}	 
	$ci=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);		
	 
	$capital_financiar=$ci->capital_total-$ci->INICIAL;  

	$interes=(($capital_financiar*$por_interes/100)*($cuotas/12)); 
	$monto_cuota=round(($capital_financiar/$cuotas)+($interes/$cuotas),2);
	 
	echo json_encode(array("capital_financiar"=>$capital_financiar,"interes"=>$interes,"monto_cuota"=>$monto_cuota));
	exit;
}
if (isset($_REQUEST['doViewAjustarContrato']) && 
						validateField($_REQUEST,'id')){
	include("view_fix/view_ajustar_contrato.php");
	exit;
}	

if (isset($_REQUEST['view_remove_window'])){
	include("view/view_window_remove.php");
	exit;
}

if (isset($_REQUEST['remover_beneficiario'])){ 
	if (validateField($_REQUEST,'serie_contrato') && 
		validateField($_REQUEST,'no_contrato') && 
		validateField($_REQUEST,'beneficiario') && 
						validateField($_REQUEST,'beneficiario')){
		
		$serie_contrato=System::getInstance()->Decrypt($_REQUEST['serie_contrato']); 
		$no_contrato=System::getInstance()->Decrypt($_REQUEST['no_contrato']); 
		$beneficiario=System::getInstance()->Decrypt($_REQUEST['beneficiario']); 
 
		$_contratos=new Contratos($protect->getDBLink()); 
		echo json_encode($_contratos->removerBeneficiario($beneficiario,$serie_contrato,$no_contrato,$_REQUEST['comentario']));		

	}
	exit;
}
	
	

		
if (isset($_REQUEST['getSelectContrato'])){ 
	if (validateField($_REQUEST,'sSearch') ){    
		$_contratos=new Contratos($protect->getDBLink()); 
		echo json_encode($_contratos->getListSelectContrato($_REQUEST['sSearch']));		 
	}
	exit;
}


		
if (isset($_REQUEST['print_contrato'])){
	$_contratos=new Contratos($protect->getDBLink()); 
	$_contratos->printr();
	exit;
}


if (isset($_REQUEST['anularSolicitud'])){
	if (validateField($_REQUEST,'serie_contrato') && validateField($_REQUEST,'no_contrato')
		&& validateField($_REQUEST,'comentario')){
		
		$serie=System::getInstance()->Decrypt($_REQUEST['serie_contrato']);
		$no_contrato=System::getInstance()->Decrypt($_REQUEST['no_contrato']);
		$_contratos=new Contratos($protect->getDBLink()); 
		echo json_encode($_contratos->anularSolicitud($serie,$no_contrato,$_REQUEST['comentario']));		

	}

	exit;
}		


if (isset($_REQUEST['view_anular_solicitud'])){
//if (validateField($_REQUEST,'view_anular_solicitud')){
	include("view/view_anular_solicitud.php"); 
	exit;
}		
 
if (validateField($_REQUEST,'comentario_remove_doc')){
	include("view/view_remove_doc.php"); 
	exit;
}	
 
if (isset($_REQUEST['download']) && validateField($_REQUEST,'id')){ 
	$ctt=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	
	if (isset($ctt->no_contrato) && isset($ctt->serie_contrato)){
		
		$ext = pathinfo($ctt->path, PATHINFO_EXTENSION); 
		$name=$ctt->tipo_documento."_".$ctt->serie_contrato.$ctt->no_contrato.".".$ext;
	 
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$name);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($ctt->path));
		readfile($ctt->path);   
	}
	exit;
}	 

/* CARGAR UN  DOCUMENTO */
if (isset($_REQUEST['upload_doc'])&&isset($_REQUEST['serie_contrato'])&&isset($_REQUEST['no_contrato'])){
	$_contratos=new Contratos($protect->getDBLink()); 
	$_contratos->addDocument($_REQUEST);  
	exit;
}	
/* AGREGAR UN DOCUMENTO */
if (isset($_REQUEST['save_document']) && isset($_REQUEST['tipo_scan'])
	&& isset($_REQUEST['serie_contrato']) && isset($_REQUEST['no_contrato'])&& isset($_REQUEST['empresa'])){
	$_contratos=new Contratos($protect->getDBLink()); 
	$rt=$_contratos->saveDocuments(System::getInstance()->Decrypt($_REQUEST['tipo_scan']),
									System::getInstance()->Decrypt($_REQUEST['empresa']),
									$_REQUEST['descripcion']);  	
 
	include("view/listar_documentos.php");
	exit;
}

/* REMOVER DOCUMENTO */
if (validateField($_REQUEST,'remove_document') && validateField($_REQUEST,'doc_descripcion')
		 && validateField($_REQUEST,'scan_id')){
	$_contratos=new Contratos($protect->getDBLink()); 
	$doc=json_decode(System::getInstance()->Decrypt($_REQUEST['scan_id']));
	$rt=$_contratos->removeDocument($doc,$_REQUEST['doc_descripcion']);  	
	
	$_REQUEST['serie_contrato']=System::getInstance()->Encrypt($doc->serie_contrato);
	$_REQUEST['no_contrato']=System::getInstance()->Encrypt($doc->no_contrato); 
	include("view/listar_documentos.php");
	exit;
}

if (isset($_REQUEST['print_oferta'])){
	include("view/pdf_planilla.php");
	exit;
}	

if (isset($_REQUEST['add_document'])){
	include("view/view_cc_upload.php");
	exit;
}

if (isset($_REQUEST['view_direccion'])){
	include("view/view_direcciones.php");
	exit;
}

if (isset($_REQUEST['list_client_direccion'])){
	include("view/view_direcciones_list.php");
	exit;
}



/*PROCESA LA DIRECCION DE COBRO DE UN CONTRATO*/
if (isset($_REQUEST['process_address'])){	
	$data=array("error"=>true,"mensaje"=>"No se pudo procesar la informacion","address"=>"");
 
	if (validateField($_REQUEST,"serie_contrato")&& validateField($_REQUEST,"no_contrato")&& validateField($_REQUEST,"provincia_id")&& validateField($_REQUEST,"cuidad_id")&& validateField($_REQUEST,"sector_id")){
		
		$_contratos=new Contratos($protect->getDBLink()); 
		$data=$_contratos->addCobroAddress($_REQUEST); 
	}
	echo json_encode($data);
	exit;
}

/*OPTIENE LAS PROVINCIA, CIUDAD, SECTOR*/
if (isset($_REQUEST['request_address'])){	
	include("view/request_direcciones.php");
	exit;
}

/*BUSCA LAS DIRECCIONES*/
if (isset($_REQUEST['search_direccion'])){	

		$find=mysql_escape_string($_REQUEST['sSearch']);
		
		$SQL="SELECT sys_sector.idsector,
		    sys_provincia.idprovincia,
			sys_municipio.idmunicipio,
			sys_ciudad.idciudad, 
			sys_sector.idsector,
			CONCAT(sys_provincia.descripcion,' / ',sys_ciudad.descripcion,' / ', sys_sector.descripcion) AS descripcion
		 FROM `sys_sector`
		INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_sector.`idciudad`)
		INNER JOIN `sys_municipio` ON (sys_municipio.`idmunicipio`=sys_ciudad.`idmunicipio`)
		INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_municipio.`idprovincia`)
		WHERE sys_sector.descripcion LIKE '%".$find."%' LIMIT 15 ";
		
		
		$result=array("results"=>array());  
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			
			$idsector=System::getInstance()->Encrypt($row['idsector']); 
			$idprovincia=System::getInstance()->Encrypt($row['idprovincia'],$protect->getSessionID());
			$idmunicipio=System::getInstance()->Encrypt($row['idmunicipio'],$protect->getSessionID());
			$idciudad=System::getInstance()->Encrypt($row['idciudad'],$protect->getSessionID());
			$sectorID=System::getInstance()->Encrypt($row['idsector'],$protect->getSessionID());
			
			$ubicacion=$idprovincia." / ".$idmunicipio." / ".$idciudad." / ".$sectorID;
			
			$ubicacion=array(
				"provincia"=>$idprovincia,
				"municipio"=>$idmunicipio,
				"ciudad"=>$idciudad,
				"sector"=>$sectorID
			);
			
			array_push($result['results'],array("id"=>base64_encode(json_encode($ubicacion)),"text"=>$row['descripcion']));
		
		}
		echo json_encode($result);
	exit;
}
	
	
if (isset($_REQUEST['contrato_data'])){
	$_contratos=new Contratos($protect->getDBLink(),$_REQUEST);
	
	if ($_REQUEST['contrato_data']=="prospecto"){
		if (isset($_REQUEST['id_nit'])){
			//$_SESSION['CONTRATO_DATA']['prospecto']=System::getInstance()->Decrypt($_REQUEST['id_nit']); 
			$_contratos->addProspecto(System::getInstance()->Decrypt($_REQUEST['id_nit']),$_REQUEST['id_documento']);
		}
	}
	
	if ($_REQUEST['contrato_data']=="contratante"){
		if (isset($_REQUEST['id_nit'])){
			$_contratos->addContratante(System::getInstance()->Decrypt($_REQUEST['id_nit']),$_REQUEST['id_documento']);
		}
	}
	
	if ($_REQUEST['contrato_data']=="representante1"){
		if (isset($_REQUEST['id_nit'])){
		//	$_SESSION['CONTRATO_DATA']['representante1']=
			$_contratos->addRepresentante1(System::getInstance()->Decrypt($_REQUEST['id_nit']),System::getInstance()->Decrypt($_REQUEST['id_documento']),System::getInstance()->Decrypt($_REQUEST['parentesco']));
		}
	}
	
	if ($_REQUEST['contrato_data']=="representante2"){
		if (isset($_REQUEST['id_nit'])){
		//	$_SESSION['CONTRATO_DATA']['representante2']=
			$_contratos->addRepresentante2(System::getInstance()->Decrypt($_REQUEST['id_nit']),System::getInstance()->Decrypt($_REQUEST['id_documento']),System::getInstance()->Decrypt($_REQUEST['parentesco']));
		}
	}
	
	if ($_REQUEST['contrato_data']=="beneficiario1"){
		if (isset($_REQUEST['data'])){
			//$_SESSION['CONTRATO_DATA']['beneficiario1']=$_REQUEST['data'];
			$_contratos->addBeneficiario1($_REQUEST['data']);
		}
	}
	
	if ($_REQUEST['contrato_data']=="beneficiario1save"){
  
			echo json_encode($_contratos->changeBeneficiario("beneficiario1",$_REQUEST['last_beneficiario_nit'],System::getInstance()->Decrypt($_REQUEST['no_contrato']),System::getInstance()->Decrypt($_REQUEST['serie_contrato'])));
 
	}
	
	if ($_REQUEST['contrato_data']=="beneficiario2save"){
			echo json_encode($_contratos->changeBeneficiario("beneficiario2",$_REQUEST['last_beneficiario_nit'],System::getInstance()->Decrypt($_REQUEST['no_contrato']),System::getInstance()->Decrypt($_REQUEST['serie_contrato'])));
 
	}
	
	if ($_REQUEST['contrato_data']=="representante1save"){
  
			echo json_encode($_contratos->changeRepresentante("representante1",System::getInstance()->Decrypt($_REQUEST['representante_nit']) ,System::getInstance()->Decrypt($_REQUEST['no_contrato']),System::getInstance()->Decrypt($_REQUEST['serie_contrato'])));
 
	}	
	
	if ($_REQUEST['contrato_data']=="representante2save"){
 
			echo json_encode($_contratos->changeRepresentante("representante2",System::getInstance()->Decrypt($_REQUEST['representante_nit']) ,System::getInstance()->Decrypt($_REQUEST['no_contrato']),System::getInstance()->Decrypt($_REQUEST['serie_contrato'])));
 
	} 
	
	if ($_REQUEST['contrato_data']=="beneficiario2"){
		if (isset($_REQUEST['data'])){
			//$_SESSION['CONTRATO_DATA']['beneficiario2']=$_REQUEST['data'];
			$_contratos->addBeneficiario2($_REQUEST['data']);
		}
	}
	
	if ($_REQUEST['contrato_data']=="producto"){ 
		if (isset($_REQUEST['data'])){	
			$_contratos->addProducto($_REQUEST['data']);
		}
	}
	
	if ($_REQUEST['contrato_data']=="producto_edit"){
		 
		if (isset($_REQUEST['data'])){	
			$_contratos->editProducto($_REQUEST['data']);
		}
	}	
	
	if ($_REQUEST['contrato_data']=="servicio"){
		 
		if (isset($_REQUEST['data'])){ 
			$_contratos->addServicio($_REQUEST['data']);
		}
	}
	if ($_REQUEST['contrato_data']=="asesor"){
		if (isset($_REQUEST['idnit'])){	
			$_contratos->addAsesor(System::getInstance()->Decrypt($_REQUEST['idnit']));
		}
	}	
 
	if (isset($_REQUEST['contrato_data'])){
		if (isset($_REQUEST['submit'])){ 
			echo json_encode($_contratos->generarSolicitud());
		}
	}
	if ($_REQUEST['contrato_data']=="changeProduct"){
		if (isset($_REQUEST['product']) && isset($_REQUEST['newproduct'])){	
		 
			$rt=$_contratos->changeProduct(System::getInstance()->Decrypt($_REQUEST['serie_contrato']),
										System::getInstance()->Decrypt($_REQUEST['no_contrato']),
										json_decode(System::getInstance()->Decrypt($_REQUEST['product'])),
										json_decode(System::getInstance()->Decrypt($_REQUEST['newproduct'])));
										
			echo json_encode($rt);							
		}
	}
	
	if ($_REQUEST['contrato_data']=="activarContrato"){ 
	
		echo json_encode($_contratos->activar());
	 
	}
	
	exit;
}



/*OPTIENE EL MONTO DE UNA PROPIEDAD QUE ESTA RESERVADA */
if (validateField($_REQUEST,"getMontoFromProduct")&& validateField($_REQUEST,"producto")){
	if (validateField($_REQUEST['producto'],"product_id")){
		$product=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']['product_id']));
		if (validateField($product,"bloque") && validateField($product,"lote")
			&& validateField($product,"id_fases") && validateField($product,"id_jardin")){
			SystemHtml::getInstance()->includeClass("caja","Caja"); 
			$caja=new Caja($protect->getDBLink());
			$no_reserva=$caja->getNoReservaFromProduct($product->bloque,
														$product->lote,
														$product->id_fases,
														$product->id_jardin);
														
			echo json_encode($reserva=$caja->getMontoReservaFromCaja($no_reserva));
			
		}else{
			
		}
	
	} 
	exit;
}

if (isset($_REQUEST['validarPersona'])){
	$_contratos=new Contratos($protect->getDBLink());
 
	$rt=$_contratos->validatePersonExist(System::getInstance()->Decrypt($_REQUEST['tipo_documento']),
					$_REQUEST['numero_documento']);
	echo json_encode($rt);
	exit;
} 

if (isset($_REQUEST['getParentesco'])){
	
	$_contratos=new Contratos($protect->getDBLink()); 
	$rt=$_contratos->getParentesco(System::getInstance()->Decrypt($_REQUEST['idnit']),
					System::getInstance()->Decrypt($_REQUEST['idnit_parentesco']));
	echo json_encode($rt);
	exit;
}


if (isset($_REQUEST['getBeneficiarios'])){
	
	$_contratos=new Contratos($protect->getDBLink());
 
	$rt=$_contratos->getBeneficiarios(System::getInstance()->Decrypt($_REQUEST['serie_contrato']),
					System::getInstance()->Decrypt($_REQUEST['no_contrato']));
	echo json_encode($rt);
	exit;
}
if (isset($_REQUEST['getRepresentantes'])){
	
	$_contratos=new Contratos($protect->getDBLink());
 
	$rt=$_contratos->getRepresentantes(System::getInstance()->Decrypt($_REQUEST['serie_contrato']),
					System::getInstance()->Decrypt($_REQUEST['no_contrato']));
	echo json_encode($rt);
	exit;
}

if (isset($_REQUEST['getAsesor'])){
	
	$_asesor=new Asesores($protect->getDBLink());
	$id_comercial=$_asesor->getIDComercialFromProspecto(System::getInstance()->Decrypt($_REQUEST['id_nit']));
 	
	$asesores=$_asesor->getComercialParentData($id_comercial['codigo_asesor']); 
	echo json_encode($asesores);
	exit;
}

if (isset($_REQUEST['printOferta'])){
	include("view/print_planilla.php");
	exit;
}
 
/*
	Buscar por tipo de documento
*/
if (isset($_REQUEST['view_search'])){
	include("view/view_search.php");
	exit;
}

/*
	Carga los controles necesarios para visualiar
*/
if (isset($_REQUEST['request'])){
	include("view/request.php");
	exit;
}


if (isset($_REQUEST['add_producto'])){
	include("view/producto.php");
	exit;
}

/*
*
*/
if (isset($_REQUEST['view_carrito_main'])){

	$carrito = new Carrito($protect->getDBLink());
	/*Proceso que agrega el financiamiento al carrito de compras*/
	if ((validateField($_REQUEST,"process_financiamiento"))){
		if ((validateField($_REQUEST,"financiamiento")) && (validateField($_REQUEST,"token"))){
			$carrito->setToken($_REQUEST['token']);
			$plan=json_decode(base64_decode($_REQUEST['financiamiento']));
 			$rt=$carrito->addFinanciamiento($plan);  
			echo json_encode($rt);
		}
		exit;
	} 	

	/*Proceso que Actualiza el financiamiento al carrito de compras*/
	if ((validateField($_REQUEST,"process_update_financiamiento"))){
		if ((validateField($_REQUEST,"financiamiento")) && (validateField($_REQUEST,"token"))
			&& (validateField($_REQUEST,"monto_precio_lista"))){
			$carrito->setToken($_REQUEST['token']);
			$plan=json_decode(base64_decode($_REQUEST['financiamiento']));
			//print_r($_REQUEST['monto_precio_lista']);
 			$rt=$carrito->updateMontoFinanciamiento($plan,$_REQUEST['monto_precio_lista']); 
	  
			echo json_encode($rt);
		}
		exit;
	} 	

	if ((validateField($_REQUEST,"calcular_monto")) && 
		(validateField($_REQUEST,"token"))){
			$carrito->setToken($_REQUEST['token']);
			echo json_encode($carrito->getDetalle());
		exit;
	}
	if ((validateField($_REQUEST,"calcular_monto_general")) && 
		(validateField($_REQUEST,"token"))){
			$carrito->setToken($_REQUEST['token']);
			echo json_encode($carrito->getDetalleGeneral());
		exit;
	}	
	if ((validateField($_REQUEST,"validar_item")) && 
		(validateField($_REQUEST,"token"))){
			$carrito->setToken($_REQUEST['token']);
			$valid=$carrito->validarItems(); 
			if ($valid['code']=="valid"){ 
				$carrito->saveItem($_REQUEST['token'],true); 
			}
			echo json_encode($valid);
		exit;
	}	
	
	include("view/carrito/template_main.php");
	exit;
}

 
/*Proceso que agrega el tipo de moneda elegido del plan de financiamiento*/
if ((validateField($_REQUEST,"process_add_finaciamiento")) && (validateField($_REQUEST,"financiamiento")) ){
	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	$_contratos= new Contratos($protect->getDBLink(),$_REQUEST); 
	$plan=json_decode(base64_decode($_REQUEST['financiamiento']));
	unset($plan->bt_editar);
	unset($plan->plan); 
	
	if (validateField($plan,"moneda")){ 
    	$_contratos->addMonedaAndPlazo($plan->moneda,$plan->plazo,$plan->enganche);
		echo json_encode(array("mensaje"=>"agregado","valid"=>true));
	}else{
		echo json_encode(array("mensaje"=>"Error datos incompleto","valid"=>false));
	}
	exit;
}
 
 
/*Proceso que agrega el financiamiento al carrito de compras*/
if ((validateField($_REQUEST,"removeItemFromServer"))){
	if (validateField($_REQUEST,"token")){ 
		$carrito = new Carrito($protect->getDBLink()); 
		$carrito->removeItem($_REQUEST['token']);
		echo json_encode(array("mensaje"=>"Removido"));
	}
	exit;
} 

if (isset($_REQUEST['edit_producto'])){
	include("view/producto_edit.php");
	exit;
}

if (isset($_REQUEST['add_descuento'])){
	include("view/add_descuento.php");
	exit;
}

 
 
 
	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['x_search'])){
		SystemHtml::getInstance()->includeClass("contratos","Contratos");
			
		$_contratos= new Contratos($protect->getDBLink(),$_REQUEST);
	 
		$result=$_contratos->getListOfertas();
		 
		echo json_encode($result);
		
		exit;
		
	}
	
 	$_contratos=new Contratos($protect->getDBLink());
	$_contratos->session_restart();
	$carrito = new Carrito($protect->getDBLink());
	$carrito->session_restart();
	$caja= new Caja($protect->getDBLINK());   
	$caja->session_restart();
	
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.widget.js");  
	SystemHtml::getInstance()->addTagScript("script/jquery.iframe-transport.js");  
	SystemHtml::getInstance()->addTagScript("script/jquery.fileupload.js");   
	
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.Contratos.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.PlanProductos.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.PlanServicios.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.Descuentos.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.PlanTotalDescuentos.js");  
	SystemHtml::getInstance()->addTagScriptByModule("class.Captura.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.ContratoCaja.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.Carrito.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.CProducto.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFinanciamiento.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.CDireccion.js");
	 
	SystemHtml::getInstance()->addTagScriptByModule("class.Facturar.js","caja"); 
  	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoComponent.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.PreFacturar.js","caja"); 
	
	
	SystemHtml::getInstance()->addTagScriptByModule("class.CDetalle.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.CDocument.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("class.CServicio.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("Class.prospectos.js","prospectos");   
	SystemHtml::getInstance()->addTagScriptByModule("class.inventario.js","inventario");  
	SystemHtml::getInstance()->addTagScriptByModule("class.Servicios.js","servicios");  
	  
	SystemHtml::getInstance()->addTagScriptByModule("Class.financiamiento.js","financiamiento");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
	
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.beneficiario.js");
	
	
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/personalData.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reservas.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	
	SystemHtml::getInstance()->addTagScript("script/select2.min.js"); 
	  
 
	SystemHtml::getInstance()->addTagScript("script/jquery.base64.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");

	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/qtip/jquery.qtip.min.js");
	SystemHtml::getInstance()->addTagStyle("script/qtip/jquery.qtip.min.css");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.fileupload.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.knob.js"); 
 
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");

	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
	SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
	SystemHtml::getInstance()->addTagStyle("css/select2.css");	
	
	SystemHtml::getInstance()->addTagStyle("css/stl_upload.css");	
	
	
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");
 
  
?>
<style>
.dataTables_filter{
	width:80%;	
	margin-top:0px;
	margin-left:10px;
}
.fsPage{
	width:99%;
}
.fields_hidden{
	display:none;	
}

.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

.ui-timepicker-rtl{ direction: rtl; }
.ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
.ui-timepicker-rtl dl dt{ float: right; clear: right; }
.ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }


.AlertColor5 td
{
 color:#000;
 background-color: #FFD24D !important;
}
.AlertColorDanger td
{
 color:#FFF;
 background-color: #D90000 !important;
}
.even{
 background-color: #E2E4FF !important;
}



.ui-tabs-vertical { width: 60em; }
.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 15em; }
.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
.ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}

table.detalle_costo{
	font-size:12px;	
}
.ui-dialog-content{
 padding:0px;
 margin:0px;
 	
}

</style>
<?php

	if (isset($_REQUEST['add_contrato'])){
		include("view/add_contrato.php");
		exit;
	}
	if (isset($_REQUEST['edit_contrato'])){
		include("view/edit_contrato.php");
		exit;
	}	

	
?>
<script>
 
var _contratos;

$(function(){
 						
  	_contratos= new Contratos('content_dialog');
	_contratos.createTableViewOferta('contratos_list');
	 
});
 
 
</script>
 
<div  class="fsPage">
  <h2>Listado de Ofertas</h2>
 	<table border="0" class="display" id="contratos_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Contrato</th>
          <th>Cliente</th>
          <th>Fecha</th>
          <th>Fecha venta</th>
          <th>Empresa</th>
          <th>No. Productos</th>
          <th>Asesor</th>
          <th>Estatus </th>
          <th>Observaciones</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>