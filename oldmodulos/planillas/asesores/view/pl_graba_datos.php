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
 
 $estatus="1,20"; //ESTATUS DE VENTAS 

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
 
   while($row = mysql_fetch_assoc($rsData)){

      /* contabilizacion de contratos activos */
       $query    = "select SUM(contratos.`no_productos`) AS conteo
	                  from contratos_ventas as contratos
			         where codigo_asesor = ".(int)$row['codigo_asesor']." 
			           and estatus in (".$estatus.")
			           and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'  ";
					   
		 if($tipo_cierre=="T"){
			$query.=" AND (SELECT (CASE 
 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1)!='OSARIOS' "; 
		 }					   
	/*
	 AND (SELECT (CASE 
 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1)!='OSARIOS'
	*/
			   
	   $result    = mysql_query($query);
	   $contratos = mysql_fetch_array($result);  
	
	   /* porcentaje de comisiones */
	   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
                          from cm_comisiones_tbl
                         where ".(int)$contratos['conteo']." between rangoini and rangofin
                           and tipo = 1
						   and estatus = 1
						   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' ";
		 
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
								  enganche,
								  (SELECT (CASE 
					 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
					 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
					 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
					  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) as producto,
								  
							      fecha_venta as fecha_ingreso,
								 round((((enganche-monto_capitalizado)/((precio_lista-monto_capitalizado)-descuento))*100),2) as por_enganche,
							     (((precio_lista-monto_capitalizado)-descuento)*tipo_cambio) AS precio_lista, 
								  cuotas,
							      ".(float)$porcentaje['porcentaje']." as porcentaje,
							      round( ( (((precio_lista-monto_capitalizado)-descuento)*tipo_cambio) * ".(float)$porcentaje['porcentaje']."/100),2) as comision,

							(SELECT COUNT(*) AS total 
								FROM 
							`pl_planillas_asesores` WHERE serie=serie_contrato 
							AND contrato=no_contrato AND `tipo_cierre`='P'
							AND anio='".(int)$anio."' AND mes='".(int)$mes."') as TOTAL_PRE,
							
							(SELECT pl_planillas_asesores.porcentaje
								FROM 
							`pl_planillas_asesores` WHERE serie=serie_contrato 
							AND contrato= no_contrato AND `tipo_cierre`='P'
							AND anio='".(int)$anio."' AND mes='".(int)$mes."' limit 1 ) as comision_precierre
						 
	                         from contratos_ventas as contratos
			                where codigo_asesor = ".(int)$row['codigo_asesor']." 
			                  and estatus in (".$estatus.")
			                  and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
  			
			 
	      $rsComisiones = mysql_query($qrComisiones);
		  $monto = 0;
		  $gtDiferido1 = 0;
 		  $bono_x_plan=0;
		  while($rwComisiones = mysql_fetch_assoc($rsComisiones)){

                /* Verificacion de diferido correspondiente por negocio */
				if ( $rwComisiones['cuotas'] > 0 ){
				    $sql = "select diferido_1, diferido_2, diferido_3 
				              from cm_diferidos_tbl
						     where estatus = 1 
							   and " .$rwComisiones['por_enganche']. " between rangoini and rangofin";
							  
				
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
	  
 				if (($tipo_cierre=="T") && ($rwComisiones['producto']!='OSARIOS')){ 
			
					if ($rwComisiones['cuotas']<=6){		
						   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
											  from cm_comisiones_tbl
											 where ".$rwComisiones['por_enganche']." between rangoini and rangofin
											   and tipo = 3  and estatus = 1
											   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' "; 
						   $rsPorcentaje = mysql_query($qrPorcentaje);
						   $porcentanje_int   = mysql_fetch_assoc($rsPorcentaje);	
						    
						   /*SI E PORCENTAJE DE ENGANCHE PAGADO  ES MAYOR QUE 15% O es un 
						   contrato de contado aplico el bono por plan*/
						   if (mysql_num_rows($rsPorcentaje)>0){
						   //if (count($porcentanje_int)>0){ 		 
							   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
												  from cm_comisiones_tbl
												 where ".$rwComisiones['cuotas']." between rangoini and rangofin
												   and tipo = 2
												   and estatus = 1
												   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' "; 
							   $rsPorcentaje = mysql_query($qrPorcentaje);
							   $comi_bono_por_plan   = mysql_fetch_assoc($rsPorcentaje);
							   
						
							 //  if (count($comi_bono_por_plan)>0){
							   if (mysql_num_rows($rsPorcentaje)>0){   
//								   $rwComisiones['porcentaje']=0;
				 			       $procentaje=$comi_bono_por_plan['porcentaje']-
								   											$rwComisiones['comision_precierre'];			
				 		 		   $bono_x_plan=$bono_x_plan+round((($rwComisiones['precio_lista']*$procentaje)/100),2);	

								    
									/* Calculo si existe este contrato se a comisionado en el precierre */			
								   $rwComisiones['porcentaje']=0;//$procentaje;
								   $rwComisiones['comision']=0;//$bono_x_plan; 
							   }
						   }else{
							   /*SI EL PORCENTAJE DE ENGANCHE ES MENOR DEL 15% ENTOCNES NO COMISIONA DE ESTA VENTA*/
//							    $rwComisiones['porcentaje']=0;
//							    $rwComisiones['comision']=0;								
 								$rwComisiones['porcentaje']= $rwComisiones['porcentaje']-$rwComisiones['comision_precierre'];
								if ($rwComisiones['porcentaje']>0){
 									$rwComisiones['comision']=round((($rwComisiones['precio_lista']* $rwComisiones['porcentaje'])/100),2);
								}else{
									$rwComisiones['porcentaje']=0;
									$rwComisiones['comision']=0;
								}
						   }
									
					 //  print_r($rwComisiones);
			 
				   }else{
					   	/*EN CASO DE LOS CONTRATOS A CREDITO*/
						if ($rwComisiones['TOTAL_PRE']>0){
						/*
							Calculo si existe este contrato se a comisionado en el precierre
						*/					   
						  $rwComisiones['porcentaje']= $rwComisiones['porcentaje']-$rwComisiones['comision_precierre'];
						  $rwComisiones['comision']=round((($rwComisiones['precio_lista']* $rwComisiones['porcentaje'])/100),2);
						  
					   }					   
					   
				   }
			    
				} 
				/*EN CASO DE SER UN PRODUCTO OSARIO Y SEA EL CIERRE TOTAL*/
			 	if (($tipo_cierre=="T") && ($rwComisiones['producto']=='OSARIOS')){ 
					   $procentaje=$rwComisiones['porcentaje']-$rwComisiones['comision_precierre']; 			 
					 //  $bono_x_plan=$bono_x_plan+round((($rwComisiones['precio_lista']*$procentaje)/100),2);	
					   $rwComisiones['porcentaje']=$procentaje;
					   $rwComisiones['comision']=round((($rwComisiones['precio_lista']*$procentaje)/100),2);	 
					  // print_r($rwComisiones);
					  // echo round((($rwComisiones['precio_lista']*$procentaje)/100),2)."\n";					   
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
		  		 
		  /*BONO POR PLAN*/
		  $idConcepto=2;
		  $insDatos = "insert into cm_detplanilla_asesor_tbl (
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
								 .(int)$idConcepto.","
								 .(float)$bono_x_plan.",'"
								 .mysql_real_escape_string(UserAccess::getInstance()->getID())."','"
								 .$fechau."')";  				 
		  $rsUpd = mysql_query($insDatos);	
 

		  
		  /*COMISIONES*/
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
   
   /* header('Location: ?mod_planillas/asesores/pl_genera_calculos&noview');*/
  } // Fin del If Principal
  
?>