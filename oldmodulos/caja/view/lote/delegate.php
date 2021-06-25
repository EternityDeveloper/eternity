<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}		
 
if (isset($_REQUEST['view_main'])){
	include("main_view.php");
exit;	
}

if (isset($_REQUEST['view_create_recibo_manual'])){
	include("view/viewReciboManual.php");
	exit;	
}
if (isset($_REQUEST['info_detalle_contrato'])){
	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	 
	$con=new Contratos($protect->getDBLink());   
	$data=$con->getDetalleGeneralFromContrato($contrato->serie_contrato,$contrato->no_contrato);
	if (count($data)<=0){
		exit;	
	}
	$caja= new Caja($protect->getDBLINK());
	$ofi_moto=Cobros::getInstance()->getCobradorMotorizadoAreaC($contrato->serie_contrato,$contrato->no_contrato);
	$tasa_cambio=$caja->getTasaActual($data['tipo_moneda']);	
	 
	$retur=array(
		"valid"=>true,
		"tipo_moneda"=>$data['tipo_moneda'],
		"motorizado"=>System::getInstance()->Encrypt($ofi_moto['nitmotorizado']),
		"oficial"=>System::getInstance()->Encrypt($ofi_moto['nitoficial']),
		"compromiso"=>number_format($data['valor_cuota']*$tasa_cambio,2),
		"plazo"=>$data['cuotas']
		
	);
	echo json_encode($retur);
	exit;	
}

if (isset($_REQUEST['doAddRecibo']) &&   validateField($_REQUEST,"id") 
	&&     validateField($_REQUEST,"action")){ 
	SystemHtml::getInstance()->includeClass("caja","Recibos"); 
	$rt=array("data"=>array(),"valid"=>false,"mensaje"=>"No existe el recibo"); 
	$recibos= new Recibos($protect->getDBLINK());   
	$action=$_REQUEST['action'];
	$id=substr($_REQUEST['id'],0,6); 
 	$ret=$recibos->getReciboByBarCode($id);
 	 
	if ($ret['valid']){ 
		$sb=STCSession::GI()->getSubmit("doCarLote");
		if (!is_array($sb)){
			$sb=array();
		} 
		if ($action=="add"){
			$sb[$ret['data']['SERIE'].$ret['data']['NO_DOCTO']]=$ret['data']; 
		}
		if ($action=="remove"){
			unset($sb[$ret['data']['SERIE'].$ret['data']['NO_DOCTO']]);
		}
		STCSession::GI()->setSubmit("doCarLote",$sb);		
		unset($ret['data']);
		include("view/listado_recibos.php");
		$data=ob_get_contents();
		ob_clean(); 
		$ret['html']=$data;
		$monto=0;
		$cantidad=0;
		foreach($sb as $key =>$row){  
			$monto=$monto+$row['MONTO_LOCAL'];
			$cantidad=$cantidad+1;
		}
		$ret['MONTO_TOTAL']=round($monto,2);
		$ret['cantidad_total']=round($cantidad,2);
		$ret['mensaje']='Proceso realizado!';
	}	 

 	echo json_encode($ret);
}

if (isset($_REQUEST['procesar_anulado_recibos']) &&  validateField($_REQUEST,"observacion")){ 
	SystemHtml::getInstance()->includeClass("caja","Recibos"); 
	$rt=array("data"=>array(),"valid"=>false,"mensaje"=>"No existe el recibo"); 
	$recibos= new Recibos($protect->getDBLINK());    

	$sb=STCSession::GI()->getSubmit("doCarLote");
	if (!is_array($sb)){
		$sb=array();
	}  
	SystemHtml::getInstance()->includeClass("caja","Caja");   
	$caja= new Caja($protect->getDBLINK());  
	$cantidad=0;
	$monto=0;
	foreach($sb as $key =>$row){    
		$obj=new ObjectSQL();
		$obj->push($row);
		$rt=$caja->doReciboRemove($obj,$_REQUEST['observacion']);		
		if (!$rt['error']){
			$cantidad=$cantidad+1;	
			$monto=$monto+$row['MONTO_LOCAL'];
		} 
	}
	 
	$ret['MONTO_TOTAL']=round($monto,2);
	$ret['cantidad_total']=round($cantidad,2);
	$ret['mensaje']='Recibos anulados ('.$cantidad.') monto de ('.number_format($monto,2).')!';	 
	
 	echo json_encode($ret);
}

?>