<?php

class Recibos{
	private $db_link;
	private $_data;
	
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	public function crearLote($documento,$tipo,$desde,$hasta){
		$rt=array(
			"valid"=>false,
			"mensaje"=>"Error,  ", 
		); 
		
		$lt_desc=$this->getDetalleLoteByTipoDoc($documento,$tipo);
		if ($desde<$lt_desc['desde']){
			$rt['mensaje']="El valor introducido es incorrecto!";
			return $rt;
		}
		if ($hasta<$lt_desc['hasta']){
			$rt['mensaje']="El valor introducido es incorrecto!";
			return $rt;
		}	
			
		 $SQL="SELECT count(*) as total FROM `pape_lote` WHERE `id_pap_documento`='".$documento."'  and id_tipo_prov_serv='".$tipo."'  AND `pap_desde`>='". mysql_real_escape_string($desde) ."' AND `pap_hasta`<='". mysql_real_escape_string($hasta) ."' ";	
		 
		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
		if ($row['total']>0){
			$rt['mensaje']="Error, EL rango introducido no ha sido creado, por favor intente con otro rango que no exista!";
			return $rt;
		}   
		
		 $SQL="SELECT count(*) as total FROM `pape_lote` WHERE `id_pap_documento`='".$documento."'  and id_tipo_prov_serv='".$tipo."'   ";	
		 
		$rs=mysql_query($SQL); 
		$row=@mysql_fetch_assoc($rs);
 		 
	 	$obj = new ObjectSQL();
		$obj->pap_codigo_lote="LT-".date('Ymd')."-". ($row['total']+1);
		$obj->id_pap_documento=$documento;
		$obj->id_tipo_prov_serv=$tipo;
		$obj->pap_desde=$desde;
		$obj->pap_hasta=$hasta;
		$obj->pap_correlativo=$desde;
		$obj->pap_creado_por=UserAccess::getInstance()->getIDNIT();
		$obj->setTable("pape_lote");
	 
		$SQL=$obj->toSQL("insert");  
		mysql_query($SQL);	 
		$rt['mensaje']="Lote generado!";
		$rt['valid']=true; 
		return $rt;
	}
	

	public function doAsignLoteToAsesor($lote,$cantidad,$asesor){
		SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
		$rt=array(
			"valid"=>false,
			"mensaje"=>"Error,  ", 
		); 
	
		if ($cantidad<0){
			$rt['mensaje']="El valor introducido es incorrecto!";
			return $rt;
		} 
		
		$cl_asesor= new Asesores($this->db_link);
		

		$SQL="SELECT  
					((plpa.pap_hasta+1)-plpa.pap_correlativo) AS DISPONIBLE
			FROM 
				`pape_lote_pendiente_asignar` AS plpa 
			WHERE plpa.pap_codigo_lote='".$lote->pap_codigo_lote."' 
				and pap_pasignado_id='".$lote->pap_pasignado_id."' ";
 
		$rs=mysql_query($SQL);
		$rt=array();
		$row=mysql_fetch_object($rs);   
		if ($row->DISPONIBLE<$cantidad){  
			$rt['mensaje']="No puede asignar un monto mayor al disponible";
			return $rt;
		}  
		
		$desde=(round($lote->pap_correlativo));
		$hasta=(round($lote->pap_correlativo))+($cantidad-1); 	 
		$asesor=$cl_asesor->getComercialParentDataByIDNit($asesor);
		 
		$rbc=array();
		$correlativo=$desde;
		
	 	for($i=$desde;$i<=$hasta;$i++){
			$obj = new ObjectSQL();
			$obj->pap_codigo_lote=$lote->pap_codigo_lote;
			$obj->pap_no_correlativo=$correlativo;
			$obj->id_nit_asignado=$asesor[0]['id_nit'];
			$obj->codigo_asesor=$asesor[0]['id_comercial']; 
			$obj->codigo_gerente_grupo=$asesor[0]['codigo_gerente_grupo']; 									
			$obj->pap_asig_creado_por=UserAccess::getInstance()->getIDNIT();
			$obj->setTable("pape_asignados");
			$SQL=$obj->toSQL("insert");  
			mysql_query($SQL); 
			array_push($rbc,$correlativo);
			$correlativo=$correlativo+1; 
		} 
	
		$obj = new ObjectSQL();
		$obj->pap_correlativo=$correlativo;
		$obj->setTable("pape_lote_pendiente_asignar");
		$SQL=$obj->toSQL("update"," where pap_pasignado_id='".$lote->pap_pasignado_id."'"); 
		mysql_query($SQL);
 		$rt['mensaje']="Lote asignado!";
		$rt['valid']=true; 
		$rt['recibos']=System::getInstance()->Encrypt(json_encode($rbc));  
		return $rt;
	}	
	public function doAsignLoteToDistribuidor($lote,$cantidad,$oficial){
		$rt=array(
			"valid"=>false,
			"mensaje"=>"Error,  ", 
		); 
	
		if ($cantidad<0){
			$rt['mensaje']="El valor introducido es incorrecto!";
			return $rt;
		} 
		
		
	
		$cantidad=$cantidad-1;
		$desde=(round($lote->pap_correlativo));
		$hasta=(round($lote->pap_correlativo))+$cantidad; 		
	 
	 	$obj = new ObjectSQL();
		$obj->pap_codigo_lote=$lote->pap_codigo_lote;
 		$obj->pap_desde=$desde;
		$obj->pap_hasta=$hasta;
		$obj->pap_correlativo=$desde; 
		$obj->id_nit_asignado=$oficial;
		$obj->pap_pasig_creado_por=UserAccess::getInstance()->getIDNIT();
		$obj->setTable("pape_lote_pendiente_asignar");
		$SQL=$obj->toSQL("insert");   
	  	mysql_query($SQL);  
		
		$obj = new ObjectSQL();
		$obj->pap_correlativo=$hasta+1;
		$obj->setTable("pape_lote");
		$SQL=$obj->toSQL("update"," where pap_codigo_lote='".$lote->pap_codigo_lote."'");  
		mysql_query($SQL);
 		$rt['mensaje']="Lote asignado!";
		$rt['valid']=true; 
		return $rt;
	}		
 
	/* */
	public function getListado(){
		$SQL="SELECT 
				pape_lote.*,
				pape_tipo_prod_serv.`pap_descripcion` AS prod_serv,
				pape_documento.`pap_descripcion` AS pap_documento,
				(pap_hasta-pap_desde) AS TOTAL,
				((pap_hasta)-pap_correlativo) AS DISPONIBLE,
				(pap_correlativo-pap_desde) AS USADA,
				(SELECT 
						CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
						OFI.`primer_apellido`,' ',
						OFI.segundo_apellido) AS nombre 
						FROM sys_personas AS OFI WHERE 
							OFI.id_nit=pape_lote.pap_creado_por) AS CREADO_POR 
				FROM `pape_lote`
				INNER JOIN `pape_tipo_prod_serv` ON (`pape_tipo_prod_serv`.id_tipo_prov_serv=pape_lote.id_tipo_prov_serv)
				INNER JOIN `pape_documento` ON (`pape_documento`.pap_doc=pape_lote.id_pap_documento)
				WHERE pape_lote.pap_estatus=1 ";
		   
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  		 
			array_push($rt,$row);
		}
		return $rt;
	}
	public function getListadoDetalle($lote){
		$SQL="SELECT plpa.*,
				(SELECT 
					CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
					OFI.`primer_apellido`,' ',
					OFI.segundo_apellido) AS nombre 
					FROM sys_personas AS OFI WHERE 
						OFI.id_nit=plpa.`id_nit_asignado`) AS ASIGNADO_A ,
				(SELECT 
					CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
					OFI.`primer_apellido`,' ',
					OFI.segundo_apellido) AS nombre 
					FROM sys_personas AS OFI WHERE 
						OFI.id_nit=plpa.`pap_pasig_creado_por`) AS CREADO_POR ,

				((plpa.pap_hasta+1)-plpa.pap_desde) AS TOTAL,
 				(plpa.pap_correlativo-plpa.pap_desde) AS USADA,	
				((plpa.pap_hasta+1)-plpa.pap_correlativo) AS DISPONIBLE

				FROM `pape_lote`  AS PL
				INNER JOIN `pape_lote_pendiente_asignar` AS plpa ON (plpa.`pap_codigo_lote`=PL.pap_codigo_lote)
 		WHERE PL.pap_codigo_lote='".$lote."' ";
	  
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			if (($row['pap_desde']-$row['pap_hasta'])==0){
				$row['DISPONIBLE']=($row['pap_desde']-$row['pap_hasta'])+1;
				$row['TOTAL']=($row['pap_desde']-$row['pap_hasta'])+1; 
			}		
			array_push($rt,$row);
		}
		return $rt;
	}	
	public function getListadoAsignado($id_nit){
		$SQL="SELECT plpa.*,
				((plpa.pap_hasta+1)-plpa.pap_desde) AS TOTAL,
 				(plpa.pap_correlativo-plpa.pap_desde) AS USADA,	
				((plpa.pap_hasta+1)-plpa.pap_correlativo) AS DISPONIBLE,	
				(SELECT 
					CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
					OFI.`primer_apellido`,' ',
					OFI.segundo_apellido) AS nombre 
					FROM sys_personas AS OFI WHERE 
						OFI.id_nit=plpa.`id_nit_asignado`) AS ASIGNADO_A ,
				(SELECT 
					CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
					OFI.`primer_apellido`,' ',
					OFI.segundo_apellido) AS nombre 
					FROM sys_personas AS OFI WHERE 
						OFI.id_nit=plpa.`pap_pasig_creado_por`) AS CREADO_POR  
				
				FROM `pape_lote`  AS PL
				INNER JOIN `pape_lote_pendiente_asignar` AS plpa ON (plpa.`pap_codigo_lote`=PL.pap_codigo_lote)
 		WHERE plpa.`id_nit_asignado`='".$id_nit."' ";

		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			if (($row['pap_desde']-$row['pap_hasta'])==0){
				$row['DISPONIBLE']=($row['pap_desde']-$row['pap_hasta'])+1;
				$row['TOTAL']=($row['pap_desde']-$row['pap_hasta'])+1; 
			}	
			array_push($rt,$row);
		}
		return $rt;
	}	
	/* */
	public function getTipoDocumento(){
		$SQL="SELECT * FROM `pape_tipo_documento` where estatus=1";
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			array_push($rt,$row);
		}
		return $rt;
	}
	public function getDocumento(){
		$SQL="SELECT * FROM `pape_documento` where estatus=1";
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			array_push($rt,$row);
		}
		return $rt;
	}	
	public function getTipoProductoServicio(){
		$SQL="SELECT * FROM `pape_tipo_prod_serv` where estatus=1";
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			array_push($rt,$row);
		}
		return $rt;
	}			
	
	public function getDetalleLoteByTipoDoc($documento,$tipo){
		$SQL="SELECT MAX(`pap_hasta`) AS `pap_desde`,MAX(`pap_hasta`)  AS `pap_hasta`  
			FROM `pape_lote` ";
	  //WHERE id_pap_documento='".$documento."' and id_tipo_prov_serv='".$tipo."'
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){   
			if ($row['pap_desde']>0 && $row['pap_hasta']>0){
				$rt['desde']=$row['pap_desde']+1;
				$rt['hasta']=$row['pap_hasta']+1;
			}else{
				$rt['desde']=1;
				$rt['hasta']=1;	
			}
		}
		return $rt;
	}
	public function crearDocumento($nombre,$tipo_moneda,$aplica_para){
		$rt=array(
			"valid"=>false,
			"mensaje"=>"Error,  ", 
		); 
		 
		if (trim($nombre)==""){
			$rt['mensaje']="Debe de ingresar un nombre valido";
			return $rt;
		}
		if (trim($tipo_moneda)==""){
			$rt['mensaje']="Debe de ingresar un nombre valido";
			return $rt;
		}
		if (trim($aplica_para)==""){
			$rt['mensaje']="Debe de ingresar un nombre valido";
			return $rt;
		}	 
		
	 	$obj = new ObjectSQL();
		$obj->NOMBRE_DOC=$nombre;
		$obj->TIPO_MONEDA=$tipo_moneda;
		$obj->APLICA_A=$aplica_para; 
		$obj->CREADO_POR=UserAccess::getInstance()->getIDNIT();
		$obj->setTable("pape_formato_documentos");
		$SQL=$obj->toSQL("insert");  
		mysql_query($SQL);	 
		$rt['mensaje']="Documento Creado!";
		$rt['valid']=true; 
		return $rt;
	}	
	public function doEditDocumento($id,$texto,$tipo_moneda,$aplica_para){
		$rt=array(
			"valid"=>false,
			"mensaje"=>"Error,  ", 
		); 
		 
		if (trim($texto)==""){
			$rt['mensaje']="Debe de ingresar un nombre valido";
			return $rt;
		}
		if (trim($tipo_moneda)==""){
			$rt['mensaje']="Debe de ingresar un nombre valido";
			return $rt;
		}
		if (trim($aplica_para)==""){
			$rt['mensaje']="Debe de ingresar un nombre valido";
			return $rt;
		}	 
		
	 	$obj = new ObjectSQL(); 
		$obj->TIPO_MONEDA=$tipo_moneda;
		$obj->APLICA_A=$aplica_para;  
		$obj->TEXTO=$texto;  
		$obj->setTable("pape_formato_documentos");
		$SQL=$obj->toSQL("update"," where ID='".$id."'");  	
		mysql_query($SQL);	 
		$rt['mensaje']="Documento editado!";
		$rt['valid']=true; 
		return $rt;
	}		
	public function getListadoDocumento(){
		$SQL="SELECT *, 
			(SELECT 
					CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
					OFI.`primer_apellido`,' ',
					OFI.segundo_apellido) AS nombre 
					FROM sys_personas AS OFI WHERE 
						OFI.id_nit=pape_formato_documentos.CREADO_POR) AS CREADO_POR 
		   FROM `pape_formato_documentos` WHERE DOC_ESTATUS=1 ";
		   
		$rs=mysql_query($SQL);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){  
			array_push($rt,$row);
		}
		return $rt;
	}			 
}

?>