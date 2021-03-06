<?php

class Archivo{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
 
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	
	public function session_restart(){
		$_SESSION['CONTRATO_DATA']=array("product_list"=>array(),"servicio_list"=>array(),"financiamiento"=>array(),"document"=>array());
	}
        
	public function getListOfertas(){
        
		if (!validateField($_REQUEST,"iDisplayStart")){
			$_REQUEST['iDisplayStart']=0;
			$_REQUEST['iDisplayLength']=10;
		}

		// && isset($_REQUEST['iDisplayLength']))
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
			$QUERY="  AND (contratos.id_nit_cliente LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,' ',contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or  contratos.no_contrato LIKE '%".$_REQUEST['sSearch']."%' )   ";
		  }
		}

		$SQL="SELECT count(*) as total
				FROM `contratos`
				INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
				INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				INNER JOIN `asesores_g_d_gg_view` ON (`asesores_g_d_gg_view`.`id_comercial`=contratos.`asesor`)
				INNER JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=asesores_g_d_gg_view.`id_nit`)
				WHERE contratos.`estatus`='13' ";
			$SQL.=$QUERY;
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$total_row=$row['total'];
		
			$SQL="SELECT contratos.*,
				CONCAT(`sys_personas`.`primer_nombre`,' ',sys_personas.`segundo_nombre`,
				`sys_personas`.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS nombre_cliente,
				empresa.`EM_NOMBRE`,
				
				contratos.no_productos AS producto_total,
				sys_status.`descripcion` AS estatus,
				CONCAT(`asesor`.`primer_nombre`,' ',asesor.`segundo_nombre`,
				`asesor`.`primer_apellido`,' ',asesor.`segundo_apellido`) AS nombre_asesor,
				CONCAT( `serie_contrato`,' ',`no_contrato`) AS contrato_numero
				
				FROM `contratos`
				INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
				INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				INNER JOIN `asesores_g_d_gg_view` ON (`asesores_g_d_gg_view`.`id_comercial`=contratos.`asesor`)
				INNER JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=asesores_g_d_gg_view.`id_nit`)
				WHERE contratos.`estatus`='13' 
				 ";
		 	$SQL.=$QUERY;
			
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			   
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			 
		 
			while($row=mysql_fetch_assoc($rs)){	
				$encriptID=System::getInstance()->Encrypt(json_encode($row));
				$id_nit=System::getInstance()->Encrypt($data_p['serie_contrato']."_".$data_p['no_contrato']);
								  
			//	$row['nombre_asesor']=$data_p['primer_nombre']." ".$data_p['primer_apellido'];
				$row['contrato']=$data_p['serie_contrato']." ".$data_p['no_contrato'];
				$row['bt_reserva']='';
				$row['bt_editar']='<a href="#"  id="'.$encriptID.'" class="edit_solicitud"><img src="images/subtract_from_cart.png"  /></a>';
				$row['bt_editar_user']='<a href="#" id="'.$encriptID.'" class="edit_solicitud"><img src="images/edit_user.png"  /></a>'; 
				array_push($data['aaData'],$row);
			}
			
			return $data;		
	}
	
	public function getListContratos(){
		 
		if (!validateField($_REQUEST,"iDisplayStart")){
			$_REQUEST['iDisplayStart']=0;
			$_REQUEST['iDisplayLength']=10;
		}
		
		// && isset($_REQUEST['iDisplayLength']))
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
			$QUERY="  AND (contratos.id_nit_cliente LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,' ',contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or concat(contratos.serie_contrato,contratos.no_contrato) LIKE '%".$_REQUEST['sSearch']."%' or  contratos.no_contrato LIKE '%".$_REQUEST['sSearch']."%' )   ";
		  }
		}

			$SQL="SELECT count(*) as total 
				FROM `contratos` 
				INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
				INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
				INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				WHERE 1=1 ";
			$SQL.=$QUERY;
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$total_row=$row['total'];
		
			$SQL="SELECT contratos.*,
					concat(contratos.serie_contrato,' ',contratos.no_contrato) as contrato_numero,
					CONCAT(`sys_personas`.`primer_nombre`,' ',sys_personas.`segundo_nombre`,
					`sys_personas`.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS nombre_cliente,
					empresa.`EM_NOMBRE`,
					sys_status.`descripcion` AS estatus,
					(SELECT CONCAT(archivo_cliente.bloque,'-',archivo_cliente.fila,'-',archivo_cliente.columna) AS ubicacion FROM `archivo_cliente` WHERE archivo_cliente.id_nit=contratos.id_nit_cliente) AS ubicacion
					FROM `contratos` 
					INNER JOIN `sys_personas` ON (`sys_personas`.`id_nit`=contratos.`id_nit_cliente`)
					INNER JOIN `empresa` ON (`empresa`.`EM_ID`=contratos.`EM_ID`)
					INNER JOIN `sys_status` ON (`sys_status`.`id_status`=contratos.`estatus`)
				where 1=1 ";
		 	$SQL.=$QUERY;
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			   
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			 
			while($row=mysql_fetch_assoc($rs)){	 
				$contrato=System::getInstance()->Encrypt(json_encode(array(
																"serie_contrato"=>$row['serie_contrato'],
																"no_contrato"=>$row['no_contrato'])));
								   
				$row['contrato']=$data_p['serie_contrato']." ".$data_p['no_contrato']; 
				$row['bt_editar']='<a href="#"  id="'.$contrato.'" class="edit_archivo"><img src="images/add_files_to_archive.png"  width="32" heigth="32" /></a>';
				$row['bt_print']='<a href="#" id="'.$contrato.'"  class="view_archivo"><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a>';
 				array_push($data['aaData'],$row);
			}
			
			return $data;		
	} 
	function doPrintContrato($serie_contrato,$no_contrato,$tipo_documento,$comentario=""){
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		$cc= new Contratos($this->db_link); 
		$cc=$cc->getInfoContrato($serie_contrato,$no_contrato); 
		if (isset($cc->id_nit_cliente)){
			$obj= new ObjectSQL();
			$obj->serie_contrato=$cc->serie_contrato;
			$obj->no_contrato=$cc->no_contrato;
			$obj->creado_por=UserAccess::getInstance()->getIDNIT();		
			$obj->pool_estatus="50";//Pendiente por Imprimir						
			$obj->fecha_crecion="CONCAT(CURDATE(),' ',CURRENT_TIME())";;						
			$obj->comentario=$comentario; 
			$obj->pape_formato_documento_id=$tipo_documento; 
			$obj->setTable("pape_formato_documentos_pool_print");								  
			$SQL=$obj->toSQL("insert");
			mysql_query($SQL);	
			return  array('valid'=>1,'mensaje'=>"Pendiente por imprimir"); 					
		} 
	}
		
	function updateArchivo($serie_contrato,$no_contrato,$bloque,$fila,$columna){
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		$cc= new Contratos($this->db_link); 
		$cc=$cc->getInfoContrato($serie_contrato,$no_contrato); 
		if (isset($cc->id_nit_cliente)){
			 
			$obj= new ObjectSQL();
			$obj->id_nit=$cc->id_nit_cliente;
			$obj->Archivo="Archimovil";
			$obj->bloque=$bloque;						
			$obj->fila=$fila;						
			$obj->columna=$columna; 
			$obj->setTable("archivo_cliente");
								 
			/*VERIFICO SI EL ARCHIVO ESTA CREADO*/
			if ($this->checkIfExistArchivoOfClient($cc->id_nit_cliente)>0){
				$SQL=$obj->toSQL("update"," where `id_nit`='".$cc->id_nit_cliente."'");
				mysql_query($SQL);				
			}else{
				/*EN CASO DE NO ESTAR CREADO ENTONCES LO INSERTO*/
				$SQL=$obj->toSQL("insert");		 
				mysql_query($SQL);		
			}
		}
		
	}	
	public function checkIfExistArchivoOfClient($id_nit){
		$SQL="SELECT COUNT(*) AS total FROM `archivo_cliente` WHERE `id_nit`='".$id_nit."'"; 
		$rs=mysql_query($SQL,$this->db_link->link_id);
		$row=mysql_fetch_assoc($rs); 
		return $row['total'];
	}
	/*RETORNA UNO DE LOS ITEMS A IMPRIMIR EN EL POOL*/
	public function getPrintContrato($id_nit){
		$SQL="  SELECT  
				pp.`pool_id`, 
				PF.*, 
				sys_nacionalidad.Descripcion as CLI_NACIONALIDAD,
				CT.valor_cuota AS CONTRATO_VALOR_CUOTA,
				CT.enganche AS CONTRATO_INICIAL,
				CT.cuotas AS CONTRATO_CUOTAS,
				(CT.precio_lista-CT.descuento) AS CONTRATO_PRECIO_NETO,
				CT.fecha_venta AS CONTRATO_FECHA_VENTA,
				CONCAT(CT.serie_contrato,' ',CT.no_contrato) AS CONTRATO,
				CT.no_contrato AS CONTRATO_NO,
				CT.serie_contrato AS CONTRATO_SERIE,
				cli.id_nit AS CLI_NO_DOCUMENTO, 
				cli.fecha_nacimiento as CLI_FECHA_NACIMIENTO,
				(YEAR(CURDATE())-YEAR(cli.fecha_nacimiento))  as CLI_EDAD,
				CONCAT(cli.primer_nombre,' ',cli.segundo_nombre,
				' ',cli.`primer_apellido`,' ',cli.`segundo_apellido`) AS CLI_NOMBRE_COMPLETO,
				(SELECT (CASE 
				 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
				 WHEN pc.id_jardin!='' THEN 
				 CONCAT((SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin)
				 ) END ) 
				 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
				  pc.serie_contrato=CT.serie_contrato AND pc.no_contrato=CT.no_contrato LIMIT 1) AS PRODUCTO_NOMBRE,		
				  CT.no_productos AS CANTIDAD_PRODUCTO,
				  CT.fecha_venta AS CONTRATO_FECHA_VENTA, 
				  CT.TIPO_MONEDA,
				sys_status.descripcion AS estatus,
				CONCAT(gerente.primer_nombre,' ',gerente.segundo_nombre,
				' ',gerente.`primer_apellido`,' ',gerente.`segundo_apellido`) AS GERENTE,  
				CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,
				' ',sys_personas.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS ASESOR, 
				(SELECT GROUP_CONCAT(CONCAT('(',IFNULL(sys_telefonos.`area`,''),') ',IFNULL(sys_telefonos.numero,''),' Ext:',IFNULL(sys_telefonos.extencion,''))) AS PHONE 
				FROM `sys_telefonos` WHERE id_nit=cli.id_nit ) AS TELEFONO_1,
				''  AS TELEFONO_2,
				'' AS TELEFONO_3, 
				(SELECT
				 CONCAT(sys_ciudad.`Descripcion`,', ',sys_sector.`descripcion`,
				 ' MANZ ',sys_direcciones.manzana,' NUM ',sys_direcciones.numero,' Av.',sys_direcciones.avenida,
				 ' Calle ',sys_direcciones.calle )  AS direccion
				  FROM 
				`sys_direcciones` 
				INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
				INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
				INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
				WHERE id_nit=cli.id_nit  LIMIT 1 ) AS CLI_DIRECCION, 
				(
				SELECT   
					GROUP_CONCAT(CONCAT(CONCAT(cli.primer_nombre,' ',
						cli.segundo_nombre,' ',cli.`primer_apellido`,' ',
						cli.`segundo_apellido`)		
						) ) AS REPRESENTANTE 	
				 FROM `representantes` 
				INNER JOIN `sys_personas`  AS cli ON (cli.id_nit=representantes.id_nit_representante) 
				 WHERE  representantes.no_contrato=CT.no_contrato AND representantes.serie_contrato=CT.serie_contrato		
				) AS REPRESENTANTES,
				(
				SELECT   
					GROUP_CONCAT(CONCAT(representantes.id_nit_representante,','))  	
				 FROM `representantes`  
				 WHERE  representantes.no_contrato=CT.no_contrato AND representantes.serie_contrato=CT.serie_contrato		
				) AS REPRESENTATES_DOCUMENTOS,
				
				(SELECT  
					GROUP_CONCAT(IF ((SELECT  COUNT(*) FROM  `beneficiario` AS t 
						INNER JOIN `sys_personas`  AS cli ON (cli.id_nit=t.`id_nit`) 
						 WHERE  t.no_contrato=beneficiario.no_contrato AND t.serie_contrato=beneficiario.serie_contrato)>0,(SELECT 
							CONCAT(cli.primer_nombre,' ',cli.segundo_nombre,' ',cli.`primer_apellido`,' ',cli.`segundo_apellido`)  
						 FROM sys_personas AS cli WHERE cli.id_nit=beneficiario.id_nit LIMIT 1)
						 ,
							CONCAT(nombre_1,' ',nombre_2,' ',apellido_1,' ',apelllido_2)  
						 ),',') AS beneficiario
					  FROM 
						`beneficiario` 
					WHERE 
						beneficiario.no_contrato=CT.no_contrato AND 
						beneficiario.serie_contrato=CT.serie_contrato)	 AS BENEFICIARIOS,
						
					(SELECT GROUP_CONCAT(cli.id_nit,',') FROM  `beneficiario` 
					INNER JOIN `sys_personas`  AS cli ON (cli.id_nit=beneficiario.`id_nit`) 	
						WHERE 
							beneficiario.no_contrato=CT.no_contrato AND 
							beneficiario.serie_contrato=CT.serie_contrato) AS BENEFICIARIOS_DOCUMENTOS,

			(SELECT GROUP_CONCAT((CASE 
			 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
			 WHEN pc.id_jardin!='' THEN 
				CONCAT(pc.id_jardin,'-', pc.bloque,'-',pc.lote)
			  END )) 
			 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
			  pc.serie_contrato=CT.serie_contrato AND pc.no_contrato=CT.no_contrato) AS PRODUCTO_ITEMS 
								  		
		FROM contratos AS CT
			INNER JOIN `pape_formato_documentos_pool_print` AS pp ON (pp.serie_contrato=CT.serie_contrato 
			AND pp.no_contrato=CT.no_contrato)
			INNER JOIN `pape_formato_documentos` AS PF ON (PF.`ID`=PP.`pape_formato_documento_id`)
			INNER JOIN `sys_status` ON (sys_status.id_status=CT.estatus) 
			INNER JOIN `sys_asesor` ON (`sys_asesor`.codigo_asesor=CT.codigo_asesor) 
			INNER JOIN `sys_personas`  AS cli ON (cli.id_nit=CT.id_nit_cliente)	
			INNER JOIN `sys_nacionalidad` ON (`sys_nacionalidad`.`id_nacionalidad`=cli.id_nacionalidad)
			LEFT JOIN `sys_gerentes_grupos` ON (`sys_gerentes_grupos`.codigo_gerente_grupo=sys_asesor.codigo_gerente_grupo)
			LEFT JOIN `sys_personas` AS gerente ON (gerente.id_nit=sys_gerentes_grupos.id_nit)
			LEFT JOIN `sys_personas` ON (sys_personas.id_nit=sys_asesor.id_nit)		
		WHERE
			pp.creado_por='".$id_nit."' and pp.`pool_estatus`=50 limit 1 "; 
			
			
		$CANTIDAD=array(1=>"UN",2=>"DOS",3=>"TRES",4=>"CUATRO",5=>"CINCO",6=>"SEIS",7=>"SIETE",
			8=>"OCHO",9=>"NUEVE",10=>"DIEZ",11=>"ONCE",12=>"DOCE",13=>"TRECE",14=>"CATORCE",15=>"QUINCE",
			16=>"DIECISEIS",17=>"DIECISIETE",18=>"DIECIOCHO",19=>"DIECINUEVE",20=>"VEINTE",21=>"VEINTIUNO",
			22=>"VEINTIDOS",23=>"VEINTITRES");	
			
	
		$rs=mysql_query($SQL,$this->db_link->link_id);
		$row=mysql_fetch_assoc($rs); 
		if (mysql_num_rows($rs)>0){ 
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			
			/*CARGO TODAS LAS VARIABLES Y RELLENO*/
			$sql="SELECT VARIABLE FROM `pape_formato_documentos_variables`";
			$rsx=mysql_query($sql,$this->db_link->link_id);
			$info=array();
		    while($rowx=mysql_fetch_assoc($rsx)){
				if (!isset($row[$rowx['VARIABLE']])){
					$row[$rowx['VARIABLE']]="";
				}
			} 	
			/*-----------------------------------*/	

			/*CARGO LA DIRECCION*/
			$sql="SELECT
				  sys_ciudad.`Descripcion` AS provincia
				  ,sys_sector.`descripcion` AS sector,
				  sys_direcciones.manzana,
				  sys_direcciones.numero,
				  sys_direcciones.avenida,
				  sys_direcciones.calle 
				  FROM 
				`sys_direcciones` 
				INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
				INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
				INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
				WHERE serie_contrato='".$row['CONTRATO_SERIE']."' AND no_contrato='".$row['CONTRATO_NO']."'"; 
			$rsx=mysql_query($sql,$this->db_link->link_id);
			$direccion="";
		    while($rowx=mysql_fetch_assoc($rsx)){ 
				$row['CLI_DIRECCION']=trim($rowx['provincia'])!=""?$rowx['provincia']." ":'';
				$row['CLI_DIRECCION'].=trim($rowx['sector'])!=""?" ".$rowx['sector']:'';
				$row['CLI_DIRECCION'].=trim($rowx['manzana'])!=""?$rowx['manzana']:'';
				$row['CLI_DIRECCION'].=trim($rowx['numero'])!=""?" NUM ".$rowx['numero']:'';
				$row['CLI_DIRECCION'].=trim($rowx['avenida'])!=""?" Av. ".$rowx['avenida']:'';	
				$row['CLI_DIRECCION'].=trim($rowx['calle'])!=""?" Calle ".$rowx['calle']:'';	 														
			} 
			/*-----------------------------------*/				
			 
			$contrato= new Contratos($this->db_link); 
			$row['CONTRATO_VALOR_CUOTA']=number_format($row['CONTRATO_VALOR_CUOTA'],2);
			$row['CONTRATO_INICIAL']=number_format($row['CONTRATO_INICIAL'],2);
			$row['CONTRATO_PRECIO_NETO']=number_format($row['CONTRATO_PRECIO_NETO'],2);
			$row['MONEDA']=$row['TIPO_MONEDA']=="LOCAL"?'RD$':'US$';
			$ben=$contrato->getBeneficiarios($row['CONTRATO_SERIE'],$row['CONTRATO_NO']);
			$row['CANTIDAD_SERVICIOS_FUNERARIOS']=$row['CANTIDAD_PRODUCTO']==1?$CANTIDAD[$row['CANTIDAD_PRODUCTO']]." SERVICIO FUNERARIO":$CANTIDAD[$row['CANTIDAD_PRODUCTO']]." SERVICIOS FUNERARIOS";
			
			$i=1;
			foreach($ben as $key=>$val){
				$row['BENEF_'.$i.'_NOMBRE']=$val['nombre_1']." ".$val['nombre_2']." ".$val['apellido_1']." ".$val['apelllido_2'];
				$row['BENEF_'.$i.'_NO_DOCUMENTO']=$val['id_nit'];									
				$row['BENEF_'.$i.'_FECHA_NACIMIENTO']=$val['fecha_nacimiento'];									
				$i=$i+1;
			}
			
			$ben=$contrato->getRepresentantes($row['CONTRATO_SERIE'],$row['CONTRATO_NO']);
			$i=1;
			foreach($ben as $key=>$val){
				$row['REPRE_'.$i.'_NOMBRE']=$val['nombre_completo'];
				$row['REPRE_'.$i.'_NO_DOCUMENTO']=$val['id_nit'];									
				$row['REPRE_'.$i.'_FECHA_NACIMIENTO']=$val['fecha_nacimiento'];									
				$i=$i+1;
			}	
			$row['path']="http://testarossa.memorial.com.do/".$row['path'];
			$row['valid']=1;
		}else{
			$row=array('valid'=>0);	
		}
		 
		return $row;
	}
	function doChangePoolEstatus($pool_id,$comentario=""){
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		$obj= new ObjectSQL(); 
		$obj->impreso_por=UserAccess::getInstance()->getIDNIT();		
		$obj->pool_estatus="51";//Documento impreso				
		$obj->fecha_impresion="CONCAT(CURDATE(),' ',CURRENT_TIME())";;						
		$obj->comentario=$comentario;  
		$obj->setTable("pape_formato_documentos_pool_print");								  
		$SQL=$obj->toSQL("update"," where pool_id='".$pool_id."'");
 
		mysql_query($SQL);	
		return  array('valid'=>1,'mensaje'=>"Documento impreso!"); 						  
	}		
	
}