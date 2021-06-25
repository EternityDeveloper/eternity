<?php
/*CALCULA Y GRABA DATOS DE COMISIONES ASESORES */ 
/* http://localhost/Sandpit/?mod_planillas/view/pl_graba_datos&mes=3&tipo_cierre=T&anio=2014 */

 if (!isset($protect)){
	exit;
 }
 
//periodo=TnZYQjdHN2JkK0YyK2YxQ1I3Q0JTUT09&type=UGYrUHNhaFJSOGtLNytEaXZ3WFY0UT09 
//localhost/sandpit/?mod_planillas/asesores/view/pl_graba_datos&periodo=TnZYQjdHN2JkK0YyK2YxQ1I3Q0JTUT09&type=UGYrUHNhaFJSOGtLNytEaXZ3WFY0UT09 

 
 $retur       = array( "mensaje" => "No se pudo completar la operacion!", 
                       "error"   => true ); 
				  
 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
 $anio        = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0;
 $usuario     = UserAccess::getInstance()->getID();
 $fechau      = date("Y/m/d");
 

  if( (in_array($mes, range(1,12)) ) && ( ($tipo_cierre=="P" || $tipo_cierre=="T") ) ){
	  
	$sql = "select count(1) as conteo
			  from pl_planillas_asesores
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
		
      $result    = mysql_query($sql);
      $rsPeriodo = mysql_fetch_array($result);
 
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
 
  
   while($row = mysql_fetch_array($rsData)){
  
      /* contabilizacion de contratos activos */
       $query    = "select count(1) as conteo 
	                  from contratos
			         where codigo_asesor = ".(int)$row['codigo_asesor']." 
			           and estatus = '1'
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
	   $monto = 0;
	   if( $contratos['conteo'] > 0){
		
	     $qrComisiones = "select ".(int)$anio." as anio,
		                          ".(int)$mes. " as mes,'".mysql_real_escape_string($tipo_cierre)."' as tipo_cierre,
							      codigo_asesor,
							      serie_contrato as serie,
							      no_contrato as contrato,
							      fecha_ingreso,
							      precio_lista,
							      porc_enganche as enganche,
								  cuotas,
							      ".(float)$porcentaje['porcentaje']." as porcentaje,
							      round( (precio_lista * ".(float)$porcentaje['porcentaje']."/100),2) as comision 
	                         from contratos
			                where codigo_asesor = ".(int)$row['codigo_asesor']." 
			                  and estatus = '1'
			                  and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
		
	      $rsComisiones = mysql_query($qrComisiones);
		  $monto = 0;
		  $gtDiferido1 = 0;
		  
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
			
			  
			    $difPagados = 1;  
		        $insDatos = "insert into pl_planillas_asesores (
			                        anio,
								    mes,
								    tipo_cierre,
								    codigo_asesor,
								    serie,
								    contrato,
								    fecha_ingreso,
								    precio_lista,
								    enganche,
								    porcentaje,
								    comision,
								    cuotas,
									diferido_1,
									diferido_2,
									diferido_3,
								    diferidos_pagados )
		                    values(" .(int)$rwComisiones['anio']. ","
					  	             .(int)$rwComisiones['mes'].  ",'"
						 		     .mysql_real_escape_string($rwComisiones['tipo_cierre']). "',"
								     .(int)$rwComisiones['codigo_asesor']. ",'"
								     .mysql_real_escape_string($rwComisiones['serie']). "','"
								     .mysql_real_escape_string($rwComisiones['contrato']). "','"
								     .mysql_real_escape_string($rwComisiones['fecha_ingreso']). "',"
								     .(float)$rwComisiones['precio_lista']. ","
								     .(float)$rwComisiones['enganche']. ","
								     .(float)$rwComisiones['porcentaje']. ","
								     .(float)$rwComisiones['comision']. ","
									 .(int)$rwComisiones['cuotas']. ","
									 .(float)$diferido_1.","
									 .(float)$diferido_2.","
									 .(float)$diferido_3.","
									 .(int)$difPagados.
							          ")";
				
			 				  
		      $inserta     = mysql_query($insDatos);
		      $monto       = $monto + $rwComisiones['comision']; 	
			  $gtDiferido1 = (float)$gtDiferido1 + (float)$diferido_1;	
						
		  }
		
		  /*Insertamos el registro de comision*/	
		  $insDatosA = "insert into cm_planilla_asesor_tbl (
		                      anio,
			  				  mes,
							  tipo_cierre,
							  codigo_asesor,
							  usuario,
							  fechau) 
		              values (".(int)$anio.","
					           .(int)$mes.",'"
							   .mysql_real_escape_string($tipo_cierre)."',"
							   .(int)$row['codigo_asesor'].",'"
							   .mysql_real_escape_string($usuario)."','"
							   .$fechau."')";
		
		  $insMaestro = mysql_query($insDatosA);
		  
		  $idconcepto = 1;
		  /*Insertamos el detalle del rubro COMISIONES = 1 */  
		  $insDatosB = "insert into cm_detplanilla_asesor_tbl (
		                        anio,
								mes,
								tipo_cierre,
								codigo_asesor,
								idconcepto,
								monto,
								usuario,
								fechau)
						values (".(int)$anio.","       
		                         .(int)$mes. ",'"
								 .mysql_real_escape_string($tipo_cierre)."',"
								 .(int)$row['codigo_asesor'].","
								 .(int)$idconcepto.","
								 .(float)$gtDiferido1.",'"
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
   
   /* header('Location: ?mod_planillas/asesores/pl_genera_calculos&noview');*/
  } // Fin del If Principal
  
?> 