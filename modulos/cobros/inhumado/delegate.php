<?php
SystemHtml::getInstance()->includeClass("cobros","Servicios"); 
SystemHtml::getInstance()->includeClass("cobros","FacturarPS"); 
 
if (isset($_REQUEST['view_facturar_product'])){  
	include("view_facturar.php");
	exit;	
}

if (isset($_REQUEST['doRemoveItemFromCar']) && (validateField($_REQUEST,"producto"))){  
 	$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
	$producto=$producto->producto;
	if (!isset($producto->id_producto)){
		$return=array("valid"=>false,"mensaje"=>"Producto invalido"); 
		echo json_encode($return);
	}
	$rt=FacturarPS::getInstance()->doRemoveCarProduct($producto,$cantidad);	
	include("detalle_factura_pro.php");
	$html=ob_get_contents();
	ob_clean();
	$rt['html']=$html;
	echo json_encode($rt);
	exit;	
}

if (isset($_REQUEST['doSaveFactura']) && (validateField($_REQUEST,"cantidad"))
&& (validateField($_REQUEST,"producto")) && (validateField($_REQUEST,"costo"))){  
 	$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
	$cantidad=$_REQUEST['cantidad'];
	if (!isset($producto->id_producto)){
		$return=array("valid"=>false,"mensaje"=>"Producto invalido"); 
		echo json_encode($return);
	}
	$rt=FacturarPS::getInstance()->doPutCarProducto($producto,$_REQUEST['costo'],$cantidad);
	include("detalle_factura_pro.php");
	$html=ob_get_contents();
	ob_clean();
	$rt['html']=$html;
	echo json_encode($rt);
	exit;	
}

if (isset($_REQUEST['getCementerio'])){  
	$servicios= new Servicios($protect->getDBLink()); 
	echo json_encode($servicios->getCementerio($_REQUEST['sSearch']));
	exit;	
}

if (isset($_REQUEST['getFuneraria'])){  
	$servicios= new Servicios($protect->getDBLink()); 
	echo json_encode($servicios->getFuneraria($_REQUEST['sSearch']));
	exit;	
}

if (isset($_REQUEST['getAtendidoPor'])){  
	$servicios= new Servicios($protect->getDBLink()); 
	echo json_encode($servicios->getAtendidoPor($_REQUEST['sSearch']));
	exit;	
}

if (isset($_REQUEST['getPreparadoPor'])){  
	$servicios= new Servicios($protect->getDBLink()); 
	echo json_encode($servicios->getPreparadoPor($_REQUEST['sSearch']));
	exit;	
}
 
if (isset($_REQUEST['doSaveServicio'])){  
	$servicios= new Servicios($protect->getDBLink()); 
	echo json_encode($servicios->saveServicioToSession($_REQUEST));
	exit;	
}

if (isset($_REQUEST['doGenerateServices'])){  
	$servicios= new Servicios($protect->getDBLink());  
	$rt=$servicios->saveServicioToSession($_REQUEST);
	if ($rt['valid']==1){
		echo json_encode($servicios->generateSolicitud($_REQUEST));
	}else{
		echo json_encode($rt);
	}
	exit;	
}


if (isset($_REQUEST['getInfoParcela'])){  
	$servicios= new Servicios($protect->getDBLink()); 
	echo json_encode($servicios->getInfoParcela($_REQUEST));
	exit;	
}

if (isset($_REQUEST['orden_inhumacion_pdf'])){  
	include("pdf_orden_servicio_inh.php");
	exit;	
}



?>