<?php

function verificarEscanneo($serie,$no_contrato){
	$SQL="SELECT count(*) as total
			 FROM `scan_contratos` 
			WHERE serie_contrato ='".$serie."' AND no_contrato='".$no_contrato."'";
	 
	$rs=mysql_query($SQL);
	$row=mysql_fetch_assoc($rs);
	return $row['total'];	
} 


if (isset($_REQUEST['listado'])){  
	include("view/listado_archivo.php");
	exit;
}

if (isset($_REQUEST['procesar_asignar_correlativo'])){  
	$data=array("valid"=>false,"mensaje"=>"","numero_de_archivo"=>"0");
	if (!isset($_REQUEST['id'])){
		$data['valid']=true;
		$data['mensaje']='Debe de seleccionar un cliente valido!';		
		echo json_encode($data);
		exit;	
	}
	SystemHtml::getInstance()->includeClass("client","PersonalData");	
	$id=trim(System::getInstance()->Decrypt($_REQUEST['id'])); 
	$person= new PersonalData($protect->getDBLink());	 
	$detalle=$person->getClientData($id);
	if (count($detalle)>0){
		$SQL="SELECT CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) AS contrato,
				contratos.serie_contrato,contratos.no_contrato,id_nit_cliente
		  FROM `contratos` WHERE id_nit_cliente='".$id."'";
		$rs=mysql_query($SQL);
		$total_escanneado=true;
		while($row=mysql_fetch_assoc($rs)){  
			if (verificarEscanneo($row['serie_contrato'],$row['no_contrato'])<=0){
				$total_escanneado=false;
			}
		}
		if ($total_escanneado){ 
			$SQL="UPDATE  `sys_personas` SET numero_de_archivo=
					(SELECT ct.n  FROM (SELECT (MAX(numero_de_archivo)+1) AS n FROM `sys_personas`)  AS ct )  
				 WHERE id_nit='".$id."' AND numero_de_archivo=0 "; 
			mysql_query($SQL);
			
			$SQL="SELECT `numero_de_archivo` FROM  `sys_personas`  WHERE id_nit='".$id."'";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$data['valid']=false;
			$data['mensaje']='';	
			$data['numero_de_archivo']=$row['numero_de_archivo']; 
		}else{
			$data['valid']=true;
			$data['mensaje']='Error, debe de escanear documentos a todos los contratos!';	
		}
	}else{
		$data['valid']=true;
		$data['mensaje']='Debe de seleccionar un cliente valido!';		
	}
	
	echo json_encode($data);	
	exit;
}

if (isset($_REQUEST['listado_persona'])){  
	include("view/listado_personas.php");
	exit;
}
if (isset($_REQUEST['detalle_asignar_correlativo'])){  
	include("view/detalle_asignar_cliente.php");
	exit;
}

if (isset($_REQUEST['detall_imprimir_label_contrato'])){  
	include("view/detalle_imprimir_label_contrato.php");
	exit;
}

if (isset($_REQUEST['procesar_impresion_label_contrato'])){  
	$data=array("valid"=>false,"mensaje"=>"");
	if (!isset($_REQUEST['id'])){
		$data['valid']=true;
		$data['mensaje']='Debe de seleccionar un cliente valido!';		
		echo json_encode($data);
		exit;	
	}
	if (!isset($_REQUEST['printer'])){
		$data['valid']=true;
		$data['mensaje']='Debe de seleccionar una impresora valida!';		
		echo json_encode($data);
		exit;	
	}
	if (!isset($_REQUEST['formato'])){
		$data['valid']=true;
		$data['mensaje']='Debe de seleccionar un formato de impresion valido!';		
		echo json_encode($data);
		exit;	
	}
	
	SystemHtml::getInstance()->includeClass("client","PersonalData");	
	$ct=json_decode(trim(System::getInstance()->Decrypt($_REQUEST['id'])));
	$printer=json_decode(trim(System::getInstance()->Decrypt($_REQUEST['printer'])));
 
	$person= new PersonalData($protect->getDBLink());	 
	$detalle=$person->getClientData($ct->id_nit);
	if (count($detalle)>0){ 
		$ud= UserAccess::getInstance()->getUserData();
		$obj=new ObjectSQL();
		$obj->creado_por= $ud['id_nit'];
		$obj->formato= $_REQUEST['formato'];
		$obj->nombre_completo= $detalle['nombre_completo'];
		$obj->numero_de_archivo=$detalle['numero_de_archivo'];	
		$obj->id_nit_cliente=$detalle['id_nit'];
		$obj->estatus="PENDIENTE";
		$obj->system_printers_id=$printer->id;
		$obj->printer_name=$printer->nombre."  ";
		$obj->no_contrato=$ct->no_contrato;
		$obj->serie_contrato=$ct->serie_contrato;
		$obj->fecha_registro="concat(curdate(),' ',CURTIME())"; 
		$obj->role_id=$ud['id_role'];		
		$obj->setTable("pape_formato_label_printer");
		$SQL=$obj->toSQL("insert");
		mysql_query($SQL);

		$obj->contrato=$obj->serie_contrato." ".$obj->no_contrato;
		$SQL="SELECT * FROM `system_formatos_impresion` WHERE formato='". 
			mysql_escape_string($_REQUEST['formato']) ."' AND estatus=1";
		$rs=mysql_query($SQL);
		$formato="";
		while($row=mysql_fetch_assoc($rs)){
			$formato=$row['valor'];
			foreach($obj as $key=>$val){
				$formato=str_replace($key,$val,$formato);
			}
 		}		
		/*NUMERO DE DOCUMENTO EL CUAL SERA EL ID PARA IDENTIFICARLO EN EL PROCESO*/
		$obj->documento_id=mysql_insert_id();
		$obj->texto=$formato;
		
		unset($obj->creado_por);
		unset($obj->fecha_registro);
		//print_r($obj);
		 
		$data['valid']=false;
		$data['mensaje']='Documento pendiente de imprimir!';	
		$data['data']=$obj;
		 
	}else{
		$data['valid']=true;
		$data['mensaje']='Debe de seleccionar un cliente valido!';		
	}
	
	echo json_encode($data);	
	exit;
}

if (isset($_REQUEST['archivo'])){  
	include("view/archivo_template.php");
	exit;
} 
if (isset($_REQUEST['ViewACEdit'])){  

	include("view/view_detalle.php");
	exit;
}



if (isset($_REQUEST['doPrintContrato'])){ 
 
	if (validateField($_REQUEST,"contrato")&& validateField($_REQUEST,"tipo_documento")){ 
		SystemHtml::getInstance()->includeClass("archivo","Archivo"); 
   		 
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		$tipo_documento=System::getInstance()->Decrypt($_REQUEST['tipo_documento']); 

		$_archivo= new Archivo($protect->getDBLink()); 
		$result=$_archivo->doPrintContrato($contrato->serie_contrato,
											$contrato->no_contrato,
											$tipo_documento,
											$_REQUEST['comentario']);
		 
		echo json_encode($result); 
	}
	 
	exit;
} 
if (isset($_REQUEST['update_archivo'])){ 
 
	if (validateField($_REQUEST,"archivo_id")&& validateField($_REQUEST,"contrato")
		&& validateField($_REQUEST,"ubicacion")){
		 
		SystemHtml::getInstance()->includeClass("archivo","Archivo"); 
   		
		$bloque=System::getInstance()->Decrypt($_REQUEST['archivo_id']);
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
		$ubicacion=json_decode(System::getInstance()->Decrypt($_REQUEST['ubicacion']));
	 
   	
		$_archivo= new Archivo($protect->getDBLink()); 
		$result=$_archivo->updateArchivo($contrato->serie_contrato,
										$contrato->no_contrato,
										$bloque,
										$ubicacion->f,
										$ubicacion-c);
		 
		//echo json_encode($result);
		
	 
	}
	 
	exit;
} 

if (isset($_REQUEST['processIdentificado'])){  
	if (validateField($_REQUEST,"id")&& validateField($_REQUEST,"direccion")
		&& validateField($_REQUEST,"contrato")  ){
	 
	}
	 
	exit;
} 


?>