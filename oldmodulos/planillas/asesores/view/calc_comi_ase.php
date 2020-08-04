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
 
 
 $estatus="1,20"; //ESTATUS DE VENTAS 
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
		

?>

<table border="0" class="display" id="ventas" width="100%" style="font-size:12px;width:100%">
  <thead>
    <tr>
      <th width="1%"></th>
      <th width="7%">No. Asesor</th>
      <th width="19%">Asesor </th>
      <th width="17%">Gerente</th>
      <th width="7%">Grupo</th>
      <th width="8%">Contratos</th>
      <th width="11%">Precio neto</th>
      <th width="11%">Comision Total</th>
      <th width="9%">Bono-Auxilio </th>
      <th width="9%">Diferido 1</th>
      <th width="10%">Diferido 2</th>
      <th width="11%">Diferido 3</th>
    </tr>
     </thead>
 <tbody>     
<?	
	  
	 $tt_contratos=0;
	 $tt_comision_total=0;
	 $tt_diferido1=0;
	 $tt_diferido2=0;
	 $tt_diferido3=0; 
	 $tt_montos=0; 
	 $tt_contratos=0; 
	 $tt_precio_neto=0;
	 $tt_bono_x_auxilio=0;
	 /* Equipo de Asesores */
	$sql  = "select a.codigo_asesor,
					 concat(b.primer_nombre, ' ', b.segundo_nombre,' ',b.primer_apellido, ' ', b.segundo_apellido) as nombre,
		 CONCAT(gerente.primer_nombre,' ',gerente.segundo_nombre,
		' ',gerente.`primer_apellido`,' ',gerente.`segundo_apellido`) AS _GERENTE, 
					 a.idgrupos
				from sys_asesor a
		  inner join sys_personas b 
				  on a.id_nit = b.id_nit
		INNER JOIN `sys_gerentes_grupos` AS sgg ON (`sgg`.codigo_gerente_grupo=a.codigo_gerente_grupo)
		INNER JOIN `sys_personas` AS gerente ON (gerente.id_nit=sgg.id_nit)					  
				where  
				    a.codigo_asesor is not null    
				order by a.codigo_gerente_grupo, cast(a.codigo_asesor as unsigned)";
 /*
 in (
				  SELECT codigo_asesor FROM contratos WHERE fecha_venta BETWEEN '2015-04-09' AND '2015-05-05'
GROUP BY codigo_asesor
				  )
 */
 
	 $rsData  = mysql_query($sql);
	 $data    = array("aaData" => array());
	  
	 while($row = mysql_fetch_array($rsData)){
	  
		/* contabilizacion de contratos activos */
		$query    = "select SUM(contratos.`no_productos`) AS conteo
					  from contratos_ventas as  contratos 
					 where codigo_asesor = ".(int)$row['codigo_asesor'] ." 
					   and estatus  in (".$estatus.")
					   and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."' 
					   ";
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
							and estatus = 1 AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."'";
  
		$rsPorcentaje = mysql_query($qrPorcentaje);
		$porcentaje   = mysql_fetch_array($rsPorcentaje);
 
		/* negocios con su comision */
		$monto       = 0;
		$gtDiferido1 = 0;
		$gtDiferido2 = 0;
		$gtDiferido3 = 0;
		$gtPrecioNeto= 0;
		
		 
		if( $contratos['conteo'] > 0){

		
		    $qrComisiones = "select ".(int)$anio." as anio,
					   ".(int)$mes." as mes,'
					   ".mysql_real_escape_string($tipo_cierre)."' as tipo,
					   (SELECT (CASE 
 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
  pc.serie_contrato=a.serie_contrato AND pc.no_contrato=a.no_contrato LIMIT 1) as producto,
					   a.codigo_asesor,
					   a.serie_contrato as serie,
					   a.no_contrato as contrato,
					   a.fecha_venta as fecha_ingreso,
					   round((((a.enganche-monto_capitalizado)/((a.precio_lista-monto_capitalizado)-a.descuento))*100),2) as por_enganche,			   
					   (((a.precio_lista-monto_capitalizado)-a.descuento)*a.tipo_cambio) AS precio_lista, 
					   ".(float)$porcentaje['porcentaje']." as porcentaje,
					   round( ((((a.precio_lista-monto_capitalizado)-a.descuento)*a.tipo_cambio) * ".(float)$porcentaje['porcentaje']."/100),2) as comision,
					   a.cuotas ,
						(SELECT COUNT(*) AS total 
							FROM 
						`pl_planillas_asesores` WHERE serie=a.serie_contrato 
						AND contrato= a.no_contrato AND `tipo_cierre`='P'
						AND anio='".(int)$anio."' AND mes='".(int)$mes."') as TOTAL_PRE,
						
						(SELECT pl_planillas_asesores.porcentaje
							FROM 
						`pl_planillas_asesores` WHERE serie=a.serie_contrato 
						AND contrato= a.no_contrato AND `tipo_cierre`='P'
						AND anio='".(int)$anio."' AND mes='".(int)$mes."' limit 1 ) as comision_precierre
				 from contratos_ventas a
				where a.codigo_asesor = ".(int)$row['codigo_asesor']." 
				  and a.estatus in (".$estatus.") 
				  and a.fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
 	  
			$rsComisiones = mysql_query($qrComisiones); 
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
 		
				/*SI ES EL CIERRE TOTAL*/
				if (($tipo_cierre=="T") && ($rwComisiones['producto']!='OSARIOS')){ 
			
					if ($rwComisiones['cuotas']<=6){	
							if (trim($rwComisiones['por_enganche'])==""){
								$rwComisiones['por_enganche']=0;	
							}	 
						   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
											  from cm_comisiones_tbl
											 where ".$rwComisiones['por_enganche']." between rangoini and rangofin
											   and tipo = 3  and estatus = 1
											   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' "; 
									 
						   $rsPorcentaje = mysql_query($qrPorcentaje);
						   $porcentanje_int   = mysql_fetch_assoc($rsPorcentaje);	
						    
						   /*SI E PORCENTAJE DE ENGANCHE PAGADO  ES MAYOR QUE 15% O es un 
						   contrato de contado aplico el bono por plan*/
						  // if (count($porcentanje_int)>0){ 		 
						  if (mysql_num_rows($rsPorcentaje)>0){
							   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
												  from cm_comisiones_tbl
												 where ".$rwComisiones['cuotas']." between rangoini and rangofin
												   and tipo = 2
												   and estatus = 1
												   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' "; 
							   $rsPorcentaje = mysql_query($qrPorcentaje);
							   $comi_bono_por_plan   = mysql_fetch_assoc($rsPorcentaje);
							   
							
							  // if (count($comi_bono_por_plan)>0){
 							   if (mysql_num_rows($rsPorcentaje)>0){	  
//								   $rwComisiones['porcentaje']=0;
				 			       $procentaje=$comi_bono_por_plan['porcentaje']-
								   											$rwComisiones['comision_precierre'];			
				 		 		   $comision=round((($rwComisiones['precio_lista']*$procentaje)/100),2);	
 									 
								   if ($rwComisiones['TOTAL_PRE']>0){
										/* Calculo si existe este contrato se a comisionado en el precierre */			
										 $rwComisiones['porcentaje']=$procentaje;
										 $rwComisiones['comision']=$comision;	
								   }else{
										 $rwComisiones['porcentaje']=$procentaje;
										 $rwComisiones['comision']=$comision;	
								   } 
							   }
						   }else{
							   /*SI EL PORCENTAJE DE ENGANCHE ES MENOR DEL 15% ENTOCNES NO COMISIONA DE ESTA VENTA*/
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
					   $comision=round((($rwComisiones['precio_lista']*$procentaje)/100),2);	
						 
					   $rwComisiones['porcentaje']=$procentaje;
					   $rwComisiones['comision']=$comision;	  
				}				
			  		 
				$gtPrecioNeto=$gtPrecioNeto+$rwComisiones['precio_lista'];
				$monto       = $monto + (float)$rwComisiones['comision'];
		 
				$gtDiferido1 = (float)$gtDiferido1 + (float)$diferido_1;
				$gtDiferido2 = (float)$gtDiferido2 + (float)$diferido_2;
				$gtDiferido3 = (float)$gtDiferido3 + (float)$diferido_3;
				
				$tt_diferido1=$tt_diferido1+$gtDiferido1;
				$tt_diferido2=$tt_diferido2+$gtDiferido2;
				$tt_diferido3=$tt_diferido3+$gtDiferido3;	
				$tt_montos=$tt_montos+$rwComisiones['comision'];
				
				/* $gtDiferido = (float)$gtDiferido + (float)$monto_diferido; */
				 
			}	
			$tt_precio_neto=$tt_precio_neto+$gtPrecioNeto;
			$tt_contratos=$tt_contratos+$contratos['conteo'];
			
			
			/*BONO X AUXILIO*/
			$SQL="select  SUM(IF(a.idconcepto = 3, a.monto,0)) as bonoaux,
						  SUM(IF(a.idconcepto = 2, a.monto,0)) as bonoplan
			   from cm_detplanilla_asesor_tbl a,
					sys_asesor b 
			 where a.codigo_asesor = b.codigo_asesor
			   and  a.codigo_asesor='".$row['codigo_asesor']."' 
			   and a.anio = ".(int)$anio."
			   and a.mes = ".(int)$mes. "
			   and a.idconcepto in (3,2)
			   and a.tipo_cierre = '".mysql_real_escape_string($tipo_cierre)."' 
			 group by a.codigo_asesor";
			$rsC = mysql_query($SQL); 
			$bono_x_auxilio=0;
			$bono_x_plan=0;
			while($rwBxPlan = mysql_fetch_assoc($rsC)){
				$bono_x_auxilio=$rwBxPlan['bonoaux']; 
				$bono_x_plan=$rwBxPlan['bonoplan']; 
				$tt_bono_x_auxilio=$tt_bono_x_auxilio+$bono_x_auxilio;
			}
			
			
?>
 
      <tr>
      <td class="details-control" style="cursor:pointer"></td>
      <td><?php echo (int)$row['codigo_asesor'];?></td>
      <td><?php echo utf8_encode($row['nombre'])."";?></td>
      <td><?php echo utf8_encode($row['_GERENTE']);?></td>
      <td><?php echo (int)$row['idgrupos'];?></td>
      <td><?php echo (int)$contratos['conteo'];?></td>
      <td><?php echo  number_format((float)$gtPrecioNeto,2,'.',',')."";?></td>
      <td><?php echo  number_format((float)$monto,2,'.',',')."";?></td>
      <td><?php echo  number_format((float)$bono_x_auxilio,2,'.',',')."";?></td>
      <td><?php echo number_format((float)$gtDiferido1,2,'.',',')."";?></td>
      <td><?php echo number_format((float)$gtDiferido2,2,'.',',')."";?></td>
      <td><?php echo number_format((float)$gtDiferido3,2,'.',',') ;?></td>
    </tr>

<?			
	
			array_push($data['aaData'], 
					   array( "row-detail"    => '',
							  "codigo_asesor" => (int)$row['codigo_asesor']."",
							  "nombre"        => mysql_real_escape_string($row['nombre'])."",
							  "apellidos"     => mysql_real_escape_string($row['apellidos'])."",
							  "cliente"		  => mysql_real_escape_string($row['nombre'])." ".mysql_real_escape_string($row['apellidos'])."",
							  "idgrupos"      => (int)$row['idgrupos']."",
							  "contratos"     => (int)$contratos['conteo']."",
							  "monto"         => number_format((float)$monto,2,'.',',')."",
							  "diferido_1"    => number_format((float)$gtDiferido1,2,'.',',')."",
							  "diferido_2"    => number_format((float)$gtDiferido2,2,'.',',')."",
							  "diferido_3"    => number_format((float)$gtDiferido3,2,'.',',') )
							  
					   ); 
		} 
		
		 
	  }

?>
  </tbody>
  <tfoot>
      <tr>
        <th></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th><?php echo (int)$tt_contratos;?></th>
        <th><?php echo  number_format((float)$tt_precio_neto,2,'.',',')."";?></th>
        <th><?php echo  number_format((float)$tt_montos,2,'.',',')."";?></th>
        <th><?php echo  number_format((float)$tt_bono_x_auxilio,2,'.',',')."";?></th>
        <th><?php echo number_format((float)$tt_diferido1,2,'.',',')."";?></th>
        <th><?php echo number_format((float)$tt_diferido2,2,'.',',')."";?></th>
        <th><?php echo number_format((float)$tt_diferido3,2,'.',',') ;?></th>
      </tr>  
  </tfoot>
</table>
<?		

exit;
	 
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
		$query    = "select SUM(contratos.`no_productos`) as conteo 
					  from contratos_ventas as contratos 
					 where codigo_asesor = ".(int)$_REQUEST['codigo_asesor'] ." 
					   and estatus  in (".$estatus.")
					   and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."' 
					   ";
					   
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
		 $qrPorcentaje = "select 
		 						orden, 
								porcentaje, 
								rangoini, 
								rangofin
						   from cm_comisiones_tbl
						  where ".(int)$contratos['conteo']." between rangoini and rangofin
							and tipo = 1
							and estatus = 1  AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."'";
		 
		$rsPorcentaje = mysql_query($qrPorcentaje);
		$porcentaje   = mysql_fetch_array($rsPorcentaje);

		
		/* PORCENTAJE DE COMISION POR */
/*		 $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
						   from cm_comisiones_tbl
						  where ".(int)$contratos['conteo']." between rangoini and rangofin
							and tipo = 3
							and estatus = 1  AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."'";
		 
		$rsPorcentaje = mysql_query($qrPorcentaje);
		$porcentaje   = mysql_fetch_array($rsPorcentaje);	*/	

		/* negocios con su comision */ 
		$data = array();
		$val = array();
	
	    if( $contratos['conteo'] > 0){
	
	       $qrComisiones = "select 
				a.serie_contrato,
				 a.no_contrato,  
			(SELECT (CASE 
			 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
			 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
			 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
			  pc.serie_contrato=a.serie_contrato AND pc.no_contrato=a.no_contrato LIMIT 1) as producto,				 
				ROUND((((enganche-monto_capitalizado)/((precio_lista-monto_capitalizado)-descuento))*100),2) AS inicial,
				CONCAT(a.serie_contrato, ' ', a.no_contrato) as contrato,
			  concat(b.primer_nombre, ' ', b.segundo_nombre, ' ', b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as cliente,
			  round((((a.enganche-monto_capitalizado)/((a.precio_lista-monto_capitalizado)-a.descuento))*100),2) as por_enganche,
			  DATE_FORMAT(a.fecha_venta, '%d/%m/%Y') as fecha_ingreso,
			  (((a.precio_lista-monto_capitalizado)-a.descuento)*a.tipo_cambio) AS precio_lista,
			  round((((enganche-monto_capitalizado)/((precio_lista-monto_capitalizado)-descuento))*100),2) as enganche,
			  ".(float)$porcentaje['porcentaje']." as porcentaje,
			  round( ((((a.precio_lista-monto_capitalizado)-a.descuento)*a.tipo_cambio) * ".(float)$porcentaje['porcentaje']."/100),2) as comision,
			  a.cuotas,
			(SELECT COUNT(*) AS total 
				FROM 
			`pl_planillas_asesores` WHERE serie=a.serie_contrato 
			AND contrato= a.no_contrato AND `tipo_cierre`='P'
			AND anio='".(int)$anio."' AND mes='".(int)$mes."') as TOTAL_PRE,
			
			(SELECT pl_planillas_asesores.porcentaje
				FROM 
			`pl_planillas_asesores` WHERE serie=a.serie_contrato 
			AND contrato= a.no_contrato AND `tipo_cierre`='P'
			AND anio='".(int)$anio."' AND mes='".(int)$mes."' limit 1 ) as comision_precierre	,
			
			a.nombre_producto as producto			  
								  
			 from contratos_ventas a, sys_personas b
			where a.id_nit_cliente = b.id_nit 
			  and a.codigo_asesor = ".(int)$_REQUEST['codigo_asesor']." 
			  and a.estatus  in (".$estatus.")
			  and a.fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
			 
	      $rsComisiones = mysql_query($qrComisiones);
		  
		  $monto = 0;
		  $diferido_1 = 0;
		  $diferido_2 = 0;
		  $diferido_3 = 0;
		 
		  while($rwComisiones = mysql_fetch_assoc($rsComisiones)){
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
				/*
					Calculo si existe este contrato se a comisionado en el precierre
				*/
			   
			   if ($rwComisiones['por_enganche']>0){
				   
			   }
				/*SI ES EL CIERRE TOTAL*/
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
				//         if (count($porcentanje_int)>0){
						   if (mysql_num_rows($rsPorcentaje)>0){
	 
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
																			
								 
				 		 		   $comision=round((($rwComisiones['precio_lista']*$procentaje)/100),2);	
								     
								   if ($rwComisiones['TOTAL_PRE']>0){
										/* Calculo si existe este contrato se a comisionado en el precierre */			
										 $rwComisiones['porcentaje']=$procentaje;
										 $rwComisiones['comision']=$comision;	
								   } else{
										 $rwComisiones['porcentaje']=$procentaje;
										 $rwComisiones['comision']=$comision;	
								   }  
							   }
						   }else{
							   /*SI EL PORCENTAJE DE ENGANCHE ES MENOR DEL 15% ENTOCNES NO COMISIONA DE ESTA VENTA*/
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
					   $comision=round((($rwComisiones['precio_lista']*$procentaje)/100),2);	
					   $rwComisiones['porcentaje']=$procentaje;
					   $rwComisiones['comision']=$comision;	  
				}
				 
			   $data['tipo_cliente']	  = $rwComisiones['cuotas']==0?'CONTADO':'CREDITO A '.$rwComisiones['cuotas'];
			   $data['contrato']      = mysql_real_escape_string($rwComisiones['contrato']);
			   $data['cliente']       = mysql_real_escape_string(utf8_encode($rwComisiones['cliente']));
			   $data['producto']       = mysql_real_escape_string(utf8_encode($rwComisiones['producto']));
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