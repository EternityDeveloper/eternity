<?php
/*CALCULA Y GRABA DATOS DE COMISIONES ASESORES */ 
/* http://localhost/Sandpit/?mod_planillas/view/pl_graba_datos&mes=3&tipo_cierre=T&anio=2014 */

 if (!isset($protect)){
	exit;
 }
 
 $retur       = array( "mensaje" => "No se pudo completar la operacion!", 
                       "error"   => true ); 
 /*periodo=aUlFcjJ4bzJoaUZzb0o0U0o4WlIyQT09&type=R0FRUmZUNEptWGJnYTVJZWpndzg5dz09*/
 /* http://localhost/Sandpit/?mod_planillas/gerentes/view/pl_graba_comiger&periodo=aUlFcjJ4bzJoaUZzb0o0U0o4WlIyQT09&type=R0FRUmZUNEptWGJnYTVJZWpndzg5dz09*/
 
 
 $opc         = isset($_REQUEST['opc'])? isset($_REQUEST['opc']) : 0; 
 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
 $anio        = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0;
 $usuario     = UserAccess::getInstance()->getID();
 $fechau      = date("Y/m/d");
  $estatus="1,20"; //ESTATUS DE VENTAS 


if( (in_array($mes, range(1,12))) && (($tipo_cierre=="P" || $tipo_cierre=="T")) ){ 

 
	$sql = "select count(1) as conteo
			  from pl_planillas_gerentes
			 where anio = " .(int)$anio. "
			   and mes  = " .(int)$mes. "
			   and tipo_cierre = '".mysql_real_escape_string($tipo_cierre)."'";
			   
	$verificar = mysql_query($sql);
	$rsVerificar = mysql_fetch_array($verificar);
	
	if($rsVerificar['conteo'] == 0){ 
			   		    
	 $sql = "select fecha_inicio_ventas as fechaini, "
					.(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
			   from cierres
			  where mes = " .(int)$mes. "
				and ano = " .(int)$anio;
			
	 $result  = mysql_query($sql);
 	 $rsPeriodo = mysql_fetch_array($result);
 
	 /* Equipo de Gerentes */
	 $sql  = "select a.codigo_gerente_grupo as codigo_gerente,
					 CONCAT(b.primer_nombre, ' ', b.segundo_nombre) as nombre,
					 CONCAT(b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as apellidos,
					 a.idgrupos
				from sys_gerentes_grupos a
		  inner join sys_personas b 
				  on a.id_nit = b.id_nit
				where a.status = 1
				order by CAST(a.idgrupos as unsigned), a.codigo_gerente";
 
 
 	 $rsData  = mysql_query($sql);
  
 	 while($row = mysql_fetch_array($rsData)){
  
		/* contabilizacion de contratos activos */
		$query    = "select SUM(contratos.`no_productos`) AS conteo
					  from contratos_ventas as contratos 
					 where codigo_gerente = ".addslashes($row['codigo_gerente'])." 
					   and estatus in (".$estatus.")
					   and fecha_venta between '" .addslashes($rsPeriodo['fechaini'])."' and '".addslashes($rsPeriodo['fechafin'])."'  ";
		
		if($tipo_cierre=="T"){
			$query.=" AND (SELECT (CASE 
 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1)!='OSARIOS' "; 
		 }	
			   
		$result    = mysql_query($query);
		$contratos = mysql_fetch_array($result);  
	
		/* porcentaje de comisiones */
		$qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
						   from cm_comision_gerente_tbl
						  where ".(int)$contratos['conteo']." between rangoini and rangofin
						  AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."'";
                       
	
	
		$rsPorcentaje = mysql_query($qrPorcentaje);
		$porcentaje   = mysql_fetch_array($rsPorcentaje);
	
		/* negocios con su comision */
		$monto = 0;
		if( $contratos['conteo'] > 0){
		
		  $qrComisiones = "select ".(int)$anio." as anio,
								   ".(int)$mes." as mes,'"
								   .mysql_real_escape_string($tipo_cierre)."' as tipo_cierre,
								   codigo_gerente,
								   serie_contrato as serie,
								   no_contrato as contrato,
								   fecha_venta as fecha_ingreso,
								    (((precio_lista-monto_capitalizado)-descuento)*tipo_cambio) as precio_lista,
								   porc_enganche,
								   ".(float)$porcentaje['porcentaje']." as porcentaje,
								   round(((((precio_lista-monto_capitalizado)-descuento)*tipo_cambio)),2) as precio_neto ,
						
						(SELECT 
								pl_planillas_gerentes.porcentaje
							FROM 
						`pl_planillas_gerentes` WHERE serie=contratos.serie_contrato 
						AND contrato= contratos.no_contrato AND `tipo_cierre`='P'
						AND anio='".(int)$anio."' AND mes='".(int)$mes."' limit 1 ) as comision_precierre
						
							  from contratos_ventas as contratos 
							 where codigo_gerente = ".$row['codigo_gerente']." 
							   and estatus in (".$estatus.")
							   and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
		
	 
	      $rsComisiones = mysql_query($qrComisiones);
		  
		  $monto = 0.00;
		  
		  while($rwComisiones = mysql_fetch_assoc($rsComisiones)){
			   
				$comision_precierre=0;
				$porcent=$porcentaje['porcentaje'];
				//if ($rwComisiones['comision_precierre']>0){ 
				if (($rwComisiones['comision_precierre']>0) && ($tipo_cierre=="T")){
					$porcent=$porcent-$rwComisiones['comision_precierre'];  
				} 
				$rwComisiones['comision']=round(($rwComisiones['precio_neto']*($porcent))/100,2);			  
			   
		      $insDatos = "insert into pl_planillas_gerentes (
			                       anio,
								   mes,
								   tipo_cierre,
								   codigo_gerente,
								   serie,
								   contrato,
								   fecha_ingreso,
								   precio_lista,
								   enganche,
								   porcentaje,
								   comision ) 
		                     values(" .(int)$rwComisiones['anio']. ","
						              .(int)$rwComisiones['mes'].
						 		      ",'".$rwComisiones['tipo_cierre']. "',"
								      .$rwComisiones['codigo_gerente']. ",'"
								      .addslashes($rwComisiones['serie']). "','"
								      .addslashes($rwComisiones['contrato']). "','"
								      .addslashes($rwComisiones['fecha_ingreso']). "',"
								      .(float)$rwComisiones['precio_lista']. ","
								      .(float)$rwComisiones['porc_enganche']. ","
								      .(float)$rwComisiones['porcentaje']. ","
								      .(float)$rwComisiones['comision'].
							  ")";
		
		    							  
		     $inserta = mysql_query($insDatos);
		     $monto = (float)$monto + (float)$rwComisiones['comision']; 			
						
		  }
		
		 /*Insertamos el registro de comision*/
		
		 $insDatosA = "insert into cm_planilla_gerente_tbl (
		                     anio,
							 mes,
							 tipo_cierre,
							 codigo_gerente,
							 usuario,
							 fechau ) 
		             values (".(int)$anio.","
					          .(int)$mes.",'"
							  .addslashes($tipo_cierre)."',"
							  .(int)$row['codigo_gerente'].",'"
							  .$usuario."','"
							  .$fechau."')";
		 $insMaestro = mysql_query($insDatosA);
		 
		 $idconcepto = 1;
		  /*Insertamos el detalle del rubro COMISIONES = 1 */  
		 $insDatosB = "insert into cm_detplanilla_gerente_tbl (
		                        anio,
								mes,
								tipo_cierre,
								codigo_gerente,
								idconcepto,
								monto,
								usuario,
								fechau)
						values (".(int)$anio.","       
		                         .(int)$mes. ",'"
								 .mysql_real_escape_string($tipo_cierre)."',"
								 .(int)$row['codigo_gerente'].","
								 .(int)$idconcepto.","
								 .(float)$monto.",'"
								 .mysql_real_escape_string($usuario)."','"
								 .$fechau."')";
								 
		$insDetalle = mysql_query($insDatosB);
		 
		 
		              	
	   }
	
    }
    
	$retur['mensaje']="Datos Generados Correctamente."; 
	 echo json_encode($retur);
	 exit;
	 
	}else{
	  $retur['mensaje']="Periodo Ya Fue Generado Anteriormente."; 
	    echo json_encode($retur);
	    exit;	
	}
}
		
 
?> 