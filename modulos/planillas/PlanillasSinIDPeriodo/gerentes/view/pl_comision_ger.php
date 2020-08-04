<?php 
 if (!isset($protect)){
	exit;
 }
 /* http://localhost/Sandpit/?mod_planillas/gerentes/view/pl_comision_ger&mes=3&tipo_cierre=T&anio=2014*/
 
 $opc         = isset($_REQUEST['opc'])? isset($_REQUEST['opc']) : 0; 
 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
 $anio        = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0;
 
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
 
	 /* Equipo de Gerentes */
	  $sql  = "select a.codigo_gerente,
					 CONCAT(b.primer_nombre, ' ', b.segundo_nombre) as nombre,
					 CONCAT(b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as apellidos,
					 a.idgrupos
				from sys_gerentes_grupos a
		  inner join sys_personas b 
				  on a.id_nit = b.id_nit
				where a.status = 1
				order by CAST(a.idgrupos as unsigned), a.codigo_gerente";

 	$rsData  = mysql_query($sql);
 	$data    = array("aaData" => array());
  
 	while($row = mysql_fetch_array($rsData)){
  
    /* contabilizacion de contratos activos */
		$query    = "select count(1) as conteo 
					  from contratos 
					 where codigo_gerente = ".$row['codigo_gerente']." 
					   and estatus = 1
					   and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
					   
		$result    = mysql_query($query);
		$contratos = mysql_fetch_array($result);  
	
		/* porcentaje de comisiones */
		$qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
						   from cm_comision_gerente_tbl
						  where ".(int)$contratos['conteo']." between rangoini and rangofin";
                       
	
		$rsPorcentaje = mysql_query($qrPorcentaje);
		$porcentaje   = mysql_fetch_array($rsPorcentaje);
	
		/* negocios con su comision */
		$monto = 0;
		if( $contratos['conteo'] > 0){
		
		   $qrComisiones = "select ".(int)$anio." as anio,
								   ".(int)$mes." as mes,'
								   ".mysql_real_escape_string($tipo_cierre)."' as tipo,
								   codigo_gerente,
								   serie_contrato as serie,
								   no_contrato as contrato,
								   fecha_ingreso,
								   precio_lista,
								   porc_enganche,
								   ".(float)$porcentaje['porcentaje']." as porcentaje,
								   round( (precio_lista * ".(float)$porcentaje['porcentaje']."/100),2) as comision 
							  from contratos 
							 where codigo_gerente = ".$row['codigo_gerente']." 
							   and estatus = '1'
							   and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
		
	    	$rsComisiones = mysql_query($qrComisiones);
		
			while($rwComisiones = mysql_fetch_array($rsComisiones)){
		        
		    	$monto = $monto + (float)$rwComisiones['comision'];
			}	
		
        	array_push($data['aaData'], 
	               	array( "row-detail"    => '',
				          	"codigo_gerente" =>$row['codigo_gerente']."",
				          	"nombre"        => mysql_real_escape_string($row['nombre'])."",
				  		  	"apellidos"     => mysql_real_escape_string($row['apellidos'])."",
						  	"idgrupos"      => (int)$row['idgrupos']."",
						  	"contratos"     => (int)$contratos['conteo']."",
						  	"monto"         => number_format((float)$monto,2,'.',',') )
				   		 );
		
		
	}
	
	 
    }
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
	 $query    = "select count(1) as conteo 
				    from contratos
				   where codigo_gerente = ".$_REQUEST['codigo_gerente']."
					   and estatus = 1
					   and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
					   
	 $result    = mysql_query($query);
	 $contratos = mysql_fetch_array($result);  
	
	 /* porcentaje de comisiones */
	 $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
                        from cm_comision_gerente_tbl
                       where ".(int)$contratos['conteo']." between rangoini and rangofin";
                        
	
	 $rsPorcentaje = mysql_query($qrPorcentaje);
	 $porcentaje   = mysql_fetch_array($rsPorcentaje);

	 /* negocios con su comision */
	 $monto = 0;
	 $data = array();
	 $val = array();
	
	 if( $contratos['conteo'] > 0){
		
	   $qrComisiones = "select CONCAT(a.serie_contrato, '-', a.no_contrato) as contrato,
	                           concat(b.primer_nombre, ' ', b.segundo_nombre, ' ', b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as cliente,
							   DATE_FORMAT(a.fecha_ingreso, '%d/%m/%Y') as fecha_ingreso,
							   a.precio_lista,
							   a.porc_enganche as enganche,
							   ".(float)$porcentaje['porcentaje']." as porcentaje,
							   round( (a.precio_lista * ".(float)$porcentaje['porcentaje']."/100),2) as comision 
	                      from contratos a,
						       sys_personas b
			             where a.id_nit_cliente = b.id_nit  
						   and codigo_gerente = ".$_REQUEST['codigo_gerente']." 
			               and estatus = '1'
			               and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
		
	    $rsComisiones = mysql_query($qrComisiones);
		
		while($rwComisiones = mysql_fetch_array($rsComisiones)){
			$data['contrato']      = mysql_real_escape_string($rwComisiones['contrato']);
			$data['cliente']       = mysql_real_escape_string($rwComisiones['cliente']);
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