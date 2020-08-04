<?php 
/* CALCULA COMISIONES ASESORES -- SIN GUARDARLOS EN LA TABLA --*/
/* http://localhost/Sandpit/?mod_planillas/view/calcula_comision_ase&mes=3&tipo_cierre=T&anio=2014 */ 

 if (!isset($protect)){
	exit;
 }
 //localhost/Sandpit/?mod_planillas/asesores/view/calc_comi_ase&periodo=TnZYQjdHN2JkK0YyK2YxQ1I3Q0JTUT09&type=UGYrUHNhaFJSOGtLNytEaXZ3WFY0UT09&opc=1
 //localhost/sandpit/?mod_planillas/asesores/view/calc_comi_ase&periodo=OHZ1b2l5NTZxeGYrZEEyV1FTYkQzdz09&type=U2xRdGJ6WXNqNDlWQ2FLdStnREEyZz09
 //localhost/sandpit/?mod_planillas/asesores/view/calc_comi_ase&anio=M2Fwc2RTVW1Ga01FZW0xL1FYV0F0QT09&periodo=NUkzejdLNG1aODlPUjJmWHh5UG81QT09&type=REFkYVBIU21tSWVsUjVicjcwWWMyZz09&codigo_asesor=1362
 //localhost/sandpit/?mod_planillas/asesores/view/calc_comi_ase&opc=1&anio=d2FsQU55ZHNhUEpSSUlJSTBMdXhsQT09&periodo=eWN1VEd4WmZjaVppemJza3hWaW9CZz09&type=RWxGZTBESWRRcXArNTBGd3ozQ0ExUT09&codigo_asesor=1362
 $opc         = isset($_REQUEST['opc'])    ? $_REQUEST['opc']                                     : 0;
 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
 $anio        = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0;
 
 
 // If principal //
 if( (in_array($mes, range(1,12))) && (($tipo_cierre=="P" || $tipo_cierre=="T")) ){
   // Periodo de Trabajo //
    $sql = "select fecha_inicio_ventas as fechaini, "
                  .(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
	  	     from cierres
		    where mes = " .(int)$mes. "
		      and ano = " .(int)$anio;

   
   			
   $result    = mysql_query($sql);
   $rsPeriodo = mysql_fetch_array($result);
 
 
   if($opc == 0) {
	 /* Si opc = 0 Es para la Table Data */	 
	  
	 /* Equipo de Asesores */
	$sql  = "select a.codigo_asesor,
					 concat(b.primer_nombre, ' ', b.segundo_nombre) as nombre,
					 concat(b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as apellidos,
					 a.idgrupos
				from sys_asesor a
		  inner join sys_personas b 
				  on a.id_nit = b.id_nit
				where a.status = 1
				  and a.codigo_asesor is not null
				order by a.idgrupos, cast(a.codigo_asesor as unsigned)";
	
	 
	 $rsData  = mysql_query($sql);
	 $data    = array("aaData" => array());
	  
	 while($row = mysql_fetch_array($rsData)){
	  
		/* contabilizacion de contratos activos */
		$query    = "select count(1) as conteo 
					  from contratos 
					 where codigo_asesor = ".(int)$row['codigo_asesor'] ." 
					   and estatus = 1
					   and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
		
					   
		$result    = mysql_query($query);
		$contratos = mysql_fetch_array($result);  
		
		/* porcentaje de comisiones */
		$qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
						   from cm_comisiones_tbl
						  where ".(int)$contratos['conteo']." between rangoini and rangofin
							and tipo = 1
							and estatus = 1";
	
		
		$rsPorcentaje = mysql_query($qrPorcentaje);
		$porcentaje   = mysql_fetch_array($rsPorcentaje);
		
		/* negocios con su comision */
		$monto       = 0;
		$gtDiferido1 = 0;
		$gtDiferido2 = 0;
		$gtDiferido3 = 0;
		
		if( $contratos['conteo'] > 0){
			
		    $qrComisiones = "select ".(int)$anio." as anio,
								   ".(int)$mes." as mes,'
								   ".mysql_real_escape_string($tipo_cierre)."' as tipo,
								   a.codigo_asesor,
								   a.serie_contrato as serie,
								   a.no_contrato as contrato,
								   a.fecha_ingreso,
								   a.precio_lista,
								   a.porc_enganche,
								   ".(float)$porcentaje['porcentaje']." as porcentaje,
								   round( (a.precio_lista * ".(float)$porcentaje['porcentaje']."/100),2) as comision,
								   a.cuotas 
							 from contratos a
							where a.codigo_asesor = ".(int)$row['codigo_asesor']." 
							  and a.estatus = '1'
							  and a.fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
			
			
			$rsComisiones = mysql_query($qrComisiones);
			
			while($rwComisiones = mysql_fetch_array($rsComisiones)){
				/* Verificacion de diferido correspondiente por negocio */
				if ( $rwComisiones['cuotas'] > 0 ){
				
				    $sql = "select diferido_1, diferido_2, diferido_3 
				             from cm_diferidos_tbl
						    where estatus = 1 
							  and " .$rwComisiones['porc_enganche']. " between rangoini and rangofin";
				   
				   				   
				   $rsDiferido     = mysql_query($sql);
				   $rowDiferido    = mysql_fetch_array($rsDiferido);
				   
				    $diferido_1 = round($rwComisiones['comision'] * $rowDiferido['diferido_1']/100,2);
				    $diferido_2 = round($rwComisiones['comision'] * $rowDiferido['diferido_2']/100,2);
				    $diferido_3 = $rwComisiones['comision'] - ( $diferido_1 + $diferido_2 );
				   
				}else{
					
				   	$diferido_1 = 0;
					$diferido_2 = 0;
					$diferido_3 = 0;
					
				}
				
				$monto       = $monto + (float)$rwComisiones['comision'];
				$gtDiferido1 = (float)$gtDiferido1 + (float)$diferido_1;
				$gtDiferido2 = (float)$gtDiferido2 + (float)$diferido_2;
				$gtDiferido3 = (float)$gtDiferido3 + (float)$diferido_3;
				
				/* $gtDiferido = (float)$gtDiferido + (float)$monto_diferido; */
				 
			}	
			
			array_push($data['aaData'], 
					   array( "row-detail"    => '',
							  "codigo_asesor" => (int)$row['codigo_asesor']."",
							  "nombre"        => mysql_real_escape_string($row['nombre'])."",
							  "apellidos"     => mysql_real_escape_string($row['apellidos'])."",
							  "idgrupos"      => (int)$row['idgrupos']."",
							  "contratos"     => (int)$contratos['conteo']."",
							  "monto"         => number_format((float)$monto,2,'.',',')."",
							  "diferido_1"    => number_format((float)$gtDiferido1,2,'.',',')."",
							  "diferido_2"    => number_format((float)$gtDiferido2,2,'.',',')."",
							  "diferido_3"    => number_format((float)$gtDiferido3,2,'.',',') )
							  
					   );
			
			
		
		}
		
		 
	  }
	 
	 echo json_encode($data);
	 exit;

	 
   } else { 	
		 /* Periodo de trabajo */
		 $sql     = "select fecha_inicio_ventas as fechaini, " .(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
				       from cierres
				      where mes = " .(int)$mes. "
					    and ano = " .(int)$anio;	 
		
		
		 $result    = mysql_query($sql);
		 $rsPeriodo = mysql_fetch_array($result);
 
		 /* contabilizacion de contratos activos */
		$query    = "select count(1) as conteo 
					  from contratos 
					 where codigo_asesor = ".(int)$_REQUEST['codigo_asesor'] ." 
					   and estatus = 1
					   and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";

		
					   
	    $result    = mysql_query($query);
	    $contratos = mysql_fetch_array($result);  
	
		/* porcentaje de comisiones */
		 $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
						   from cm_comisiones_tbl
						  where ".(int)$contratos['conteo']." between rangoini and rangofin
							and tipo = 1
							and estatus = 1";
		
		
		
		$rsPorcentaje = mysql_query($qrPorcentaje);
		$porcentaje   = mysql_fetch_array($rsPorcentaje);

		/* negocios con su comision */
		
		$data = array();
		$val = array();
	
	    if( $contratos['conteo'] > 0){
	
	       $qrComisiones = "select CONCAT(a.serie_contrato, '-', a.no_contrato) as contrato,
	                              concat(b.primer_nombre, ' ', b.segundo_nombre, ' ', b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as cliente,
							      DATE_FORMAT(a.fecha_ingreso, '%d/%m/%Y') as fecha_ingreso,
							      a.precio_lista,
							      a.porc_enganche as enganche,
							      ".(float)$porcentaje['porcentaje']." as porcentaje,
							      round( (a.precio_lista * ".(float)$porcentaje['porcentaje']."/100),2) as comision,
								  a.cuotas
	                         from contratos a, sys_personas b
			                where a.id_nit_cliente = b.id_nit 
							  and a.codigo_asesor = ".(int)$_REQUEST['codigo_asesor']." 
			                  and a.estatus = '1'
			                  and a.fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
		
		  
	      $rsComisiones = mysql_query($qrComisiones);
		  
		  $monto = 0;
		  $diferido_1 = 0;
		  $diferido_2 = 0;
		  $diferido_3 = 0;
		
		  while($rwComisiones = mysql_fetch_array($rsComisiones)){

                /* Verificacion de diferido correspondiente por negocio */
				if ( $rwComisiones['cuotas'] > 0 ){
				
				  $sql = "select diferido_1, diferido_2, diferido_3
				             from cm_diferidos_tbl
							where estatus = 1 
							  and " .$rwComisiones['enganche']. " between rangoini and rangofin";
				
				  
				   
				   
				   $rsDiferido     = mysql_query($sql);
				   $rowDiferido    = mysql_fetch_array($rsDiferido);
				   
				
				   $diferido_1 = round($rwComisiones['comision'] * $rowDiferido['diferido_1']/100,2);
				   $diferido_2 = round($rwComisiones['comision'] * $rowDiferido['diferido_2']/100,2);
				   $diferido_3 = $rwComisiones['comision'] - ( $diferido_1 + $diferido_2 );				   
				  
				}else{
				   $diferido_1 = 0;
				   $diferido_2 = 0;
				   $diferido_3 = 0;	
				   	
				}
				
			  
			   $data['contrato']      = mysql_real_escape_string($rwComisiones['contrato']);
			   $data['cliente']       = mysql_real_escape_string($rwComisiones['cliente']);
			   $data['fecha_ingreso'] = mysql_real_escape_string($rwComisiones['fecha_ingreso']);
			   $data['precio_lista']  = number_format((float)$rwComisiones['precio_lista'],2,'.',',');
			   $data['enganche']      = number_format((float)$rwComisiones['enganche'],2,'.',',');
			   $data['porcentaje']    = number_format((float)$rwComisiones['porcentaje'],2,'.',',');
			   $data['comision']      = number_format((float)$rwComisiones['comision'],2,'.',',');
			   $data['cuotas']        = (int)$rwComisiones['cuotas'];
			   $data['diferido_1']    = number_format((float)$diferido_1,2,'.',',');
			   $data['diferido_2']    = number_format((float)$diferido_2,2,'.',',');
			   $data['diferido_3']    = number_format((float)$diferido_3,2,'.',',');
			 
			   array_push($val, $data);		        
		   
		  }	
			
		  
		  
	   }
	   
	   
	    $result = $val; 
	   		 // Set the JSON header
		 header("Content-type: text/json");
	
		 print json_encode($result);	
		 exit;

     
	 
   } //Fin opc==0
   
 } // Fin If principal
 
?>