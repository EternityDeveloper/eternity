<?php 
 if (!isset($protect)){
	exit;
 }
 /* http://localhost/Sandpit/?mod_planillas/gerentes/view/pl_comision_ger&mes=3&tipo_cierre=T&anio=2014*/
 
 $opc         = isset($_REQUEST['opc'])? isset($_REQUEST['opc']) : 0; 
 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
 $anio        = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0;
 
 
  $estatus="1,20"; //ESTATUS DE VENTAS 
  
 if( (in_array($mes, range(1,12))) && (($tipo_cierre=="P" || $tipo_cierre=="T")) ){
 
	 /* Periodo de trabajo */
	$sql = "select fecha_inicio_ventas as fechaini, "
                  .(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
	  	     from cierres
		    where mes = " .(int)$mes. "
		      and ano = " .(int)$anio;
		 
   $result    = mysql_query($sql);
   $rsPeriodo = mysql_fetch_array($result);
 
	 /* Encabezado */
   if($opc == 0) {
 
 ?>
<table border="0" class="display" id="ventas" width="100%" style="font-size:12px;width:100%">
  <thead>
    <tr>
      <th></th>
      <th width="11%">No. Gerente</th>
      <th width="28%">Nombre </th>
      <th width="25%">Apellidos</th>
      <th width="11%">Grupo</th>
      <th width="12%">Contratos</th>
      <th width="13%">Monto Pre</th>
      <th width="13%">Monto</th>
    </tr>
</thead>
<tbody>
<?php
 		$_contratos=0;
		$_monto=0;
		$_monto_pre=0;
 
	 /* Equipo de Gerentes */
	  $sql  = "select 
	  				 a.codigo_gerente_grupo AS codigo_gerente,
					 CONCAT(b.primer_nombre, ' ', b.segundo_nombre) as nombre,
					 CONCAT(b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as apellidos,
					 a.idgrupos,  
					 (SELECT SUM(`pl_planillas_gerentes`.comision) AS comision FROM 
							`pl_planillas_gerentes` WHERE  `tipo_cierre`='P'
							AND anio='".(int)$anio."' AND mes='".(int)$mes. "'  
						AND codigo_gerente=a.codigo_gerente_grupo) as comision_pre	
				from sys_gerentes_grupos a
		  inner join sys_personas b 
				  on a.id_nit = b.id_nit
				where a.status = 1    
				order by CAST(a.idgrupos as unsigned), a.codigo_gerente_grupo";
				 
 	$rsData  = mysql_query($sql);
 	$data    = array("aaData" => array());
  
 	while($row = mysql_fetch_assoc($rsData)){
  
    /* contabilizacion de contratos activos */
		$query    = "select SUM(contratos.`no_productos`) AS conteo
					  from contratos_ventas as contratos  
					 where  contratos.codigo_gerente = ".$row['codigo_gerente']."   
					   and estatus in (".$estatus.")
					   and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."' ";
	   
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
		$porcentaje   = mysql_fetch_assoc($rsPorcentaje);
	 

    /* contabilizacion de contratos activos */
		$query    = "select SUM(contratos.`no_productos`) AS conteo
					  from contratos_ventas as contratos  
					 where  contratos.codigo_gerente = ".$row['codigo_gerente']."   
					   and estatus in (".$estatus.")
					   and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."' ";
	  
		$result    = mysql_query($query);
		$contratos = mysql_fetch_assoc($result);
			 
		/* negocios con su comision */
		$monto = 0;
		$monto_pre = 0;

		if( $contratos['conteo'] > 0){
		
		   $qrComisiones = "select ".(int)$anio." as anio,
								   ".(int)$mes." as mes,'
								   ".mysql_real_escape_string($tipo_cierre)."' as tipo,
								   contratos.codigo_gerente AS codigo_gerente,
								   serie_contrato as serie,
								   no_contrato as contrato,
								   fecha_venta as fecha_ingreso,
								   (SELECT   
							        (((b.precio_lista-b.monto_capitalizado)-b.descuento)*b.tipo_cambio) AS pl 
										FROM contratos AS b WHERE 
								     b.`serie_contrato`=contratos.anul_por_serie_contrato  
							 	     and b.`no_contrato`=contratos.anul_por_no_contrato  ) as plista_anterior, 
								   (((precio_lista-monto_capitalizado)-descuento)*tipo_cambio) as precio_lista,
								   porc_enganche,
								   (".(float)$porcentaje['porcentaje'].") as porcentaje,
								   round(((((precio_lista-monto_capitalizado)-descuento)*tipo_cambio)),2) as precio_neto ,
						
						(SELECT pl_planillas_gerentes.porcentaje
							FROM 
						`pl_planillas_gerentes` WHERE serie=contratos.serie_contrato 
						AND contrato= contratos.no_contrato AND `tipo_cierre`='P'
						AND anio='".(int)$anio."' AND mes='".(int)$mes."' limit 1 ) as comision_precierre					
						
							  from contratos_ventas as contratos  
							 where  contratos.codigo_gerente = '".$row['codigo_gerente']."'
							   and estatus in (".$estatus.")
							   and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
			 
		 
	    	$rsComisiones = mysql_query($qrComisiones);
	 
			while($rwComisiones = mysql_fetch_assoc($rsComisiones)){
				$comision_precierre=0;
				$monto_pre=$rwComisiones['comision_pre'];
				$porcent=$porcentaje['porcentaje'];
				$rwComisiones['tipo']=trim($rwComisiones['tipo']);
				
				if (($rwComisiones['comision_precierre']>0) && ($tipo_cierre=="T")){
 					$porcent=$porcent-$rwComisiones['comision_precierre'];  
				}
				
				$rwComisiones['comision']=round(($rwComisiones['precio_neto']*($porcent))/100,2);
				
			 	
		        $_monto=$_monto+(float)$rwComisiones['comision'];
		    	$monto = $monto + (float)$rwComisiones['comision'];
				 
				
			}	
			
			
		 
			if ($tipo_cierre=="T"){ 
		 		$_monto=$_monto+$row['comision_pre'];
				$monto_pre=$monto_pre+$row['comision_pre'];
				$_monto_pre=$_monto_pre+$row['comision_pre'];
			}
			 
		 	$_contratos=$_contratos+(int)$contratos['conteo'];
        	array_push($data['aaData'], 
	               	array( "row-detail"    => '',
				          	"codigo_gerente" =>$row['codigo_gerente']."",
				          	"nombre"        => mysql_real_escape_string($row['nombre'])."",
				  		  	"apellidos"     => mysql_real_escape_string($row['apellidos'])."",
						  	"idgrupos"      => (int)$row['idgrupos']."",
						  	"contratos"     => (int)$contratos['conteo']."",
						  	"monto"         => number_format((float)$monto,2,'.',',') )
				   		 );
			 		 
?>
    <tr>
      <td class="details-control" style="cursor:pointer"></td>
      <td><?php echo $row['codigo_gerente'];?></td>
      <td><?php echo mysql_real_escape_string($row['nombre']);?></td>
      <td><?php echo mysql_real_escape_string($row['apellidos']);?></td>
      <td><?php echo  (int)$row['idgrupos'];?></td>
      <td><?php echo (int)$contratos['conteo'];?></td>
      <td><?php echo number_format((float)$monto_pre,2,'.',',');?></td>
      <td><?php echo number_format((float)$monto,2,'.',',');?></td>
    </tr>

<?php		
		
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
      <th><?php echo (int)$_contratos;?></th>
      <th><?php echo number_format((float)$_monto_pre,2,'.',',');?></th>
      <th><?php echo number_format((float)$_monto,2,'.',',');?></th>
    </tr>  
  </tfoot>
</table>
 <?php	
	exit;
  echo json_encode($data);
  exit;
  
  } else {
 
	 /* Detalle */
	 /* Periodo de trabajo */
	 $sql     = "select fecha_inicio_ventas as fechaini, "
					.(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
			   from cierres
			  where mes = " .(int)$mes. "
				and ano = " .(int)$anio;	 
	
	 $result    = mysql_query($sql);
	 $rsPeriodo = mysql_fetch_array($result);
 
     /* contabilizacion de contratos activos */
	 $query    = "select SUM(contratos.`no_productos`) AS conteo
				    from contratos_ventas as contratos
				   INNER JOIN `sys_asesor` ON (`sys_asesor`.codigo_asesor=contratos.codigo_asesor)
 				where  contratos.codigo_gerente= '".$_REQUEST['codigo_gerente']."'
					   and estatus in (".$estatus.")
					   and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."' ";
 
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
						WHERE 
                       ".(int)$contratos['conteo']." between rangoini and rangofin
						  AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."'";
         
	 $rsPorcentaje = mysql_query($qrPorcentaje);
	 $porcentaje   = mysql_fetch_array($rsPorcentaje);

	 /* negocios con su comision */
	 $monto = 0;
	 $data = array();
	 $val = array();
	
	 if( $contratos['conteo'] > 0){
		
	   $qrComisiones = "select CONCAT(a.serie_contrato, ' ', a.no_contrato) as contrato,
	                           concat(b.primer_nombre, ' ', b.segundo_nombre, ' ', b.primer_apellido, ' ', b.segundo_apellido) as cliente, 
	                           concat(c.primer_nombre, ' ', c.segundo_nombre, ' ', c.primer_apellido, ' ', c.segundo_apellido) as asesor,
							   DATE_FORMAT(a.fecha_venta, '%d/%m/%Y') as fecha_ingreso,

							(SELECT   
							  (((b.precio_lista-b.monto_capitalizado)-b.descuento)*b.tipo_cambio) AS pl 
							FROM contratos AS b WHERE 
								 b.`anul_por_serie_contrato`=a.serie_contrato 
							 	and b.`anul_por_no_contrato`=a.no_contrato ) as plista_anterior, 
							   
							   (((a.precio_lista-monto_capitalizado)-a.descuento)*a.tipo_cambio)  as precio_lista,
							   a.porc_enganche as enganche,
							   ".(float)$porcentaje['porcentaje']." as porcentaje,
							   round( (a.precio_lista * ".(float)$porcentaje['porcentaje']."/100),2) as comision 
	                      from 	contratos_ventas  a,
								sys_personas b,
								sys_asesor AS ase,
								sys_personas c 					   
			             where 
						`ase`.codigo_asesor=a.codigo_asesor AND 
						 ase.id_nit=c.id_nit AND a.id_nit_cliente = b.id_nit   
						   AND a.codigo_gerente = '".$_REQUEST['codigo_gerente']."'   
			               and estatus IN (".$estatus.")
			               and fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
	  
	    $rsComisiones = mysql_query($qrComisiones);
		
		while($rwComisiones = mysql_fetch_assoc($rsComisiones)){
			$data['contrato']      = mysql_real_escape_string($rwComisiones['contrato']);
			$data['asesor']      = mysql_real_escape_string(utf8_encode($rwComisiones['asesor']));
			$data['cliente']       = mysql_real_escape_string(utf8_encode($rwComisiones['cliente']));
			$data['fecha_ingreso'] = mysql_real_escape_string($rwComisiones['fecha_ingreso']);
			 
			$data['precio_lista']  = number_format((float)$rwComisiones['precio_lista'],2,'.',',');
			$data['enganche']      = number_format((float)$rwComisiones['enganche'],2,'.',',');
			$data['porcentaje']    = number_format((float)$rwComisiones['porcentaje'],2,'.',',');
			$data['comision']      = number_format((float)$rwComisiones['comision'],2,'.',',');
			
			array_push($val, $data);		        
		   
		}	
			
		$result = $val;

 
		// Set the JSON header
		header("Content-type: text/json");
	
		print json_encode($result);	
		exit;
	}
 
	 
 }
 
 }
?>