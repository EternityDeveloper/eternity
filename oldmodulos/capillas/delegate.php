<?php
if (!isset($protect)){
	exit;
}

/*Vista de listado de obituarios*/
if (isset($_REQUEST['obituario'])){  
	include("obituario/listar.php"); 
	exit;
} 

/*Vista de listado de obituarios*/
if (isset($_REQUEST['agregar_obituario'])){  
	include("obituario/view/agregar_ob.php"); 
	exit;
} 

/*Vista de listado de obituarios*/
if (isset($_REQUEST['editar_obituario'])){  
	include("obituario/view/editar_ob.php"); 
	exit;
} 


/*REGISTRAR LOS OBITUARIOS*/
if (isset($_REQUEST['doActualizarObituario'])){   
	if (validateField($_REQUEST,'fecha_velacion_obi') && 
							validateField($_REQUEST,"fecha_salida_obi")&& 
							validateField($_REQUEST,"hora_exposicion_obi")&& 
							validateField($_REQUEST,"hora_salida_obi")&& 
							validateField($_REQUEST,"nombre_completo_obi")&& 
							validateField($_REQUEST,"capillas_obi")&& 
							validateField($_REQUEST,"estatus")&&  
							validateField($_REQUEST,"id")
							){
		$id=System::getInstance()->Decrypt($_REQUEST['id']);
		$estatus=System::getInstance()->Decrypt($_REQUEST['estatus']);					
		$capillas_obi=json_decode(System::getInstance()->Decrypt($_REQUEST['capillas_obi'])); 		
		 
		$obj = new ObjectSQL();
		$obj->nombre_completo=$_REQUEST['nombre_completo_obi'];
		$obj->estatus=$estatus;
		$obj->capillas_devices_id=$capillas_obi->id;	
		$obj->fecha_exposicion=$_REQUEST['fecha_velacion_obi'];	
		$obj->hora_exposicion=$_REQUEST['hora_exposicion_obi'];	
		$obj->fecha_salida=$_REQUEST['fecha_salida_obi'];	
		$obj->hora_salida=$_REQUEST['hora_salida_obi'];	
		$obj->detalle_inhumacion=$_REQUEST['detalle_inhumacion'];	
		$obj->cementerio_ihumacion=$_REQUEST['cementerio_obi'];	
		$obj->visita_residencia=$_REQUEST['visita_a_la_residencia_obi'];	
		$obj->lectura_palabra=$_REQUEST['lectura_palabra_obi'];	
		$obj->misa_en_capillas=$_REQUEST['misa_en_capillas'];	
//		$obj->registrado_por=UserAccess::getInstance()->getIDNIT();
//		$obj->fecha_registro="concat(curdate(),' ',CURTIME())";	
		$obj->setTable("capillas_obituario");
		$SQL=$obj->toSQL("update"," where id='".$id."'");	
		mysql_query($SQL);	 
		echo json_encode(array("mensaje"=>"Obituario actualizado","error"=>false)); 	
	}else{
		echo json_encode(array("mensaje"=>"Error, faltan datos para procesar!","error"=>true));		
	}
	exit;
} 

/*REGISTRAR LOS OBITUARIOS*/
if (isset($_REQUEST['doRegistrarObituario'])){   
	if (validateField($_REQUEST,'fecha_velacion_obi') && 
							validateField($_REQUEST,"fecha_salida_obi")&& 
							validateField($_REQUEST,"hora_exposicion_obi")&& 
							validateField($_REQUEST,"hora_salida_obi")&& 
							validateField($_REQUEST,"nombre_completo_obi")&& 
							validateField($_REQUEST,"capillas_obi")
							){
								
		$capillas_obi=json_decode(System::getInstance()->Decrypt($_REQUEST['capillas_obi'])); 		
		 
		$obj = new ObjectSQL();
		$obj->nombre_completo=$_REQUEST['nombre_completo_obi'];
		$obj->capillas_devices_id=$capillas_obi->id;	
		$obj->fecha_exposicion=$_REQUEST['fecha_velacion_obi'];	
		$obj->hora_exposicion=$_REQUEST['hora_exposicion_obi'];	
		$obj->fecha_salida=$_REQUEST['fecha_salida_obi'];	
		$obj->hora_salida=$_REQUEST['hora_salida_obi'];	
		$obj->detalle_inhumacion=$_REQUEST['detalle_inhumacion'];	
		$obj->cementerio_ihumacion=$_REQUEST['cementerio_obi'];	
		$obj->visita_residencia=$_REQUEST['visita_a_la_residencia_obi'];	
		$obj->lectura_palabra=$_REQUEST['lectura_palabra_obi'];	
		$obj->misa_en_capillas=$_REQUEST['misa_en_capillas'];	
		$obj->registrado_por=UserAccess::getInstance()->getIDNIT();
		$obj->fecha_registro="concat(curdate(),' ',CURTIME())";	
		$obj->setTable("capillas_obituario");
		$SQL=$obj->toSQL("insert");	
		mysql_query($SQL);	
		if (mysql_insert_id()>0){
			echo json_encode(array("mensaje"=>"Obituario creado","error"=>false));
			exit;	
		}else{
			echo json_encode(array("mensaje"=>"Error, no ha creado el obituario","error"=>true));	
		}
	}else{
		echo json_encode(array("mensaje"=>"Error, faltan datos para procesar!","error"=>true));		
	}
	exit;
} 



?>