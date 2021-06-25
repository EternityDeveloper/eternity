<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}		
	SystemHtml::getInstance()->includeClass("papeleria","Recibos"); 


if (isset($_REQUEST["print_dialog"]) && validateField($_REQUEST,"id")){
	include("print/print_dialog.php");
	exit;
}


/*EJECUTA LA CREACION DEL DOCUMENTO*/ 
if (isset($_REQUEST["print_oferta"])){
	if (validateField($_REQUEST,"id")){ 
		include("print/print_recibo_manual.php");
	}
	exit;
}

/*EJECUTA LA CREACION DEL DOCUMENTO*/ 
if (isset($_REQUEST["createDocument"])){
	if (validateField($_REQUEST,"aplica_para")  && validateField($_REQUEST,"tipo_moneda")  
		&& validateField($_REQUEST,"nombre")){
		$pap= new Recibos($protect->getDBLINK());   
		$aplica_para= $_REQUEST['aplica_para'];
		$tipo_moneda= $_REQUEST['tipo_moneda'];
		$nombre= $_REQUEST['nombre']; 
	 
		echo json_encode($pap->crearDocumento($nombre,$tipo_moneda,$aplica_para)); 
	}
	exit;
}
/*EJECUTA LA EDICION DEL DOCUMENTO*/ 
if (isset($_REQUEST["doEditDocument"])){
	if (validateField($_REQUEST,"aplica_para")  && validateField($_REQUEST,"tipo_moneda")  
		&& validateField($_REQUEST,"text") && validateField($_REQUEST,"id")){
		$pap= new Recibos($protect->getDBLINK());   
		$aplica_para= $_REQUEST['aplica_para'];
		$tipo_moneda= $_REQUEST['tipo_moneda'];
		$text= $_REQUEST['text']; 
		$doc=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
  
		echo json_encode($pap->doEditDocumento($doc->ID,$text,$tipo_moneda,$aplica_para)); 
	}
	exit;
}

/*VISTA DE IMPRESION DE DOCUMENTO*/
if (isset($_REQUEST['doViewPrintDocto'])){
 	include("print/document.php");
	exit;
}
/*VISTA PARA LISTAR EL DETALLE DE LOTES*/
if (isset($_REQUEST['viewEditDocument'])){
 	include("view/modal/viewEditDocument.php");
	exit;
}

/*VISTA PARA CREACION DOCUMENTOS PARA IMPRESION*/
if (isset($_REQUEST['viewAddDocument'])){
 	include("view/modal/viewCrearDocument.php");
	exit;
}
/*VISTA PARA ASIGNACION DE LOTES AL ASESOR*/
if (isset($_REQUEST['listarDocumentos'])){
 	include("view/listar_documentos.php");
	exit;
}

/*VISTA PARA ASIGNACION DE LOTES AL ASESOR*/
if (isset($_REQUEST['viewAsignarAsesor'])){
 	include("view/modal/viewAsignarAsesor.php");
	exit;
}

/*VISTA PARA LISTAR LA PAPELERIA */
if (isset($_REQUEST['listar_asignar'])){
 	include("view/listar_reasignacion.php");
	exit;
}
/*VISTA PARA LISTAR LA PAPELERIA */
if (isset($_REQUEST['listar'])){
 	include("view/listar_pap.php");
	exit;
}

/*VISTA PARA CREACION DE LOTES*/
if (isset($_REQUEST['viewLoteAdd'])){
 	include("view/modal/viewCrearLote.php");
	exit;
}
/*VISTA PARA ASIGNACION DE LOTES*/
if (isset($_REQUEST['viewAsignarLote'])){
 	include("view/modal/viewAsignDistribuidor.php");
	exit;
}

/*VISTA PARA LISTAR EL DETALLE DE LOTES*/
if (isset($_REQUEST['viewDetalleLote'])){
 	include("view/modal/viewDetalleLote.php");
	exit;
}

if (isset($_REQUEST["doAsignarToAsesor"]) && validateField($_REQUEST,"lote")
	&& validateField($_REQUEST,"cantidad")&& validateField($_REQUEST,"asesor")){ 
	$pap= new Recibos($protect->getDBLINK());   
	$lote=json_decode(System::getInstance()->Decrypt($_REQUEST['lote'])); 
	$cantidad=$_REQUEST['cantidad'];
	$asesor=System::getInstance()->Decrypt($_REQUEST['asesor']); 
 
	echo json_encode($pap->doAsignLoteToAsesor($lote,$cantidad,$asesor)); 
	exit;
}

if (isset($_REQUEST["getLoteDetalle"]) && validateField($_REQUEST,"lote") && validateField($_REQUEST,"oficial")){ 
	$pap= new Recibos($protect->getDBLINK());   
	$lote=json_decode(System::getInstance()->Decrypt($_REQUEST['lote']));  
	print_r($lote);
	exit;
	echo json_encode($pap->doAsignLoteToDistribuidor($lote)); 
	exit;
}
	
/*ASIGNACION FINAL AL ASESOR QUE ENTRA LA VENTA*/	
if (isset($_REQUEST["doAsignLote"]) && validateField($_REQUEST,"lote")
	&& validateField($_REQUEST,"cantidad")){ 
	$pap= new Recibos($protect->getDBLINK());   
	$lote=json_decode(System::getInstance()->Decrypt($_REQUEST['lote'])); 
	$cantidad=$_REQUEST['cantidad'];
	$oficial=System::getInstance()->Decrypt($_REQUEST['oficial']); 
	 
	echo json_encode($pap->doAsignLoteToDistribuidor($lote,$cantidad,$oficial)); 
	exit;
}

/*EJECUTA EL PAGO */ 
if (isset($_REQUEST["doGetLote"]) && validateField($_REQUEST,"tipo_lote")
 && validateField($_REQUEST,"documento")){
 	$pap= new Recibos($protect->getDBLINK());   
	$tipo_lote=json_decode(System::getInstance()->Decrypt($_REQUEST['tipo_lote'])); 
	$documento=json_decode(System::getInstance()->Decrypt($_REQUEST['documento'])); 
	 
	echo json_encode($pap->getDetalleLoteByTipoDoc($documento->pap_doc,$tipo_lote->id_tipo_prov_serv)); 
	exit;
}

/*EJECUTA */ 
if (isset($_REQUEST["doCalcularAsignLote"]) && validateField($_REQUEST,"lote") && validateField($_REQUEST,"cantidad")){
 	$pap= new Recibos($protect->getDBLINK());   
	$lote=json_decode(System::getInstance()->Decrypt($_REQUEST['lote'])); 
	$cantidad=$_REQUEST['cantidad'];
	$desde=(round($lote->pap_correlativo));
	$hasta=(round($lote->pap_correlativo))+($cantidad-1);
	  
	$valid=true; 
	
	if ($lote->DISPONIBLE<$cantidad){ 
		$hasta=0;
		$valid=false;
		$msg="No puede asignar un monto mayor al disponible";
	} 
 
	echo json_encode(array("desde"=>$desde,"hasta"=>$hasta,"valid"=>$valid,"mensaje"=>$msg)); 
	exit;
}


/*EJECUTA EL PAGO */  
if (isset($_REQUEST["createLote"]) && validateField($_REQUEST,"documento")
	 && validateField($_REQUEST,"tipo_serv_prod")){
 	$pap= new Recibos($protect->getDBLINK());   
	$documento=json_decode(System::getInstance()->Decrypt($_REQUEST['documento']));
	$tipo_serv_prod=json_decode(System::getInstance()->Decrypt($_REQUEST['tipo_serv_prod']));	
	$lt_desc=$pap->getDetalleLoteByTipoDoc($documento->pap_doc,$tipo_serv_prod->id_tipo_prov_serv);
	
	
	$desde=$lt_desc['desde'];
	$hasta=$_REQUEST['pap_hasta'];  
	echo json_encode($pap->crearLote($documento->pap_doc,$tipo_serv_prod->id_tipo_prov_serv,$desde,$hasta)); 
	exit;
}



?>