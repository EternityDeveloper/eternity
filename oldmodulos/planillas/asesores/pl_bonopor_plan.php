<?php 
 if (!isset($protect)){
	exit;
 }
 
 if (isset($_REQUEST['view'])){
	include ("view/pl_periodo_cierre.php");
	exit;	
 }
 
 $retur  = array("mensaje" => "No se pudo completar la operacion!", 
                  "error"   => true); 
				  
 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
 $anio        = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0;
 $usuario     = UserAccess::getInstance()->getID();
 $fechau      = date("Y/m/d");
 
 /* IdConcepto para Bono por Plan = 2 */
 $idConcepto  = 2;
 
 if( (in_array($mes, range(1,12))) && (($tipo_cierre=="P" || $tipo_cierre=="T")) ){
    
	$sql = "select count(1) as conteo
	          from cm_detplanilla_asesor_tbl
			 where anio = " .(int)$anio. "
			   and mes  = " .(int)$mes. "
			   and idconcepto = ".(int)$idConcepto." 
			   and tipo_cierre = '".mysql_real_escape_string($tipo_cierre)."'";
	
	$rsVerifica  = mysql_query($sql);
	$rowVerifica = mysql_fetch_array($rsVerifica);
	
	if($rowVerifica['conteo'] == 0){ 

	   /*Seleccionamos el tipo de Fecha de Cierre*/
		$sql = "select fecha_inicio_ventas as fechaini, "
					  .(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
				 from cierres
				where mes = " .(int)$mes. "
				  and ano = " .(int)$anio;
				
		$result    = mysql_query($sql);
		$rsPeriodo = mysql_fetch_array($result);
		/*
		$sql = "select c.codigo_asesor,
					   SUM( ROUND((c.neto * c.porcentaje/100),2) )as bonoxplan
				  from ( 
						 select a.codigo_asesor,
								ROUND(a.precio_lista/(1 + a.enganche/100),2) as neto,
								a.cuotas,
								b.porcentaje
						  from pl_planillas_asesores a,
							   cm_comisiones_tbl b
						 where a.anio          = " .(int)$anio."
						   and a.mes           = " .(int)$mes ."
						   and a.fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."' 
						   and a.tipo_cierre  = '" .mysql_real_escape_string($tipo_cierre)."'
						   and a.cuotas between b.rangoini and b.rangofin
						   and b.tipo = 2
						   and b.estatus = 1
						 ) c
				  group by c.codigo_asesor";*/

	       $sql = "select 
				a.serie_contrato,
				 a.no_contrato,
				 a.enganche as inicial,
				CONCAT(a.serie_contrato, ' ', a.no_contrato) as contrato,
			  concat(b.primer_nombre, ' ', b.segundo_nombre, ' ', b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as cliente,
			  round(((a.enganche/a.precio_lista)*100),2) as por_enganche,
			  DATE_FORMAT(a.fecha_venta, '%d/%m/%Y') as fecha_ingreso,
			  ((a.precio_lista-a.descuento)*a.tipo_cambio) AS precio_lista,
			  a.porc_enganche as enganche,
			  ".(float)$porcentaje['porcentaje']." as porcentaje,
			  round( (((a.precio_lista-a.descuento)*a.tipo_cambio) * ".(float)$porcentaje['porcentaje']."/100),2) as comision,
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
			AND anio='".(int)$anio."' AND mes='".(int)$mes."' limit 1 ) as comision_precierre				  
								  
			 from contratos a, sys_personas b
			where a.id_nit_cliente = b.id_nit 
			  and a.codigo_asesor = ".(int)$_REQUEST['codigo_asesor']." 
			  and a.estatus  in (1,20)
			  and a.fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
			  		
					 	  
		$rsBonoPlan = mysql_query($sql);
		

		while($row = mysql_fetch_array($rsBonoPlan)){
			
			print_r($row);
		
		exit;
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
								 .(float)$row['bonoxplan'].",'"
								 .mysql_real_escape_string($usuario)."','"
								 .$fechau."')";
						
			$rsUpd = mysql_query($insDatos);
		
		
		} 
		
		$retur['mensaje']="Datos Generados Correctamente."; 
		echo json_encode($retur);
		exit; 
	
	}else{
		
		$retur['mensaje']="Datos Ya Fueron Generados Anteriormente."; 
		echo json_encode($retur);
		exit; 
	}
		
 }
 
 
 SystemHtml::getInstance()->addTagScriptByModule("jquery.dataTables.js","planillas"); 
 /*SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); */
 SystemHtml::getInstance()->addTagStyle("css/jquery.dataTables.css"); 
 
 SystemHtml::getInstance()->addTagScript("script/Class.js");  
  
/* SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
   SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js"); */

 SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
 SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");

 SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
  
 /*SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");*/

 SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");

 SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
 SystemHtml::getInstance()->addTagScriptByModule("class.opBonoplan.js","planillas/asesores"); 
 
 
 /*Cargo el Header*/
 SystemHtml::getInstance()->addModule("header");
 SystemHtml::getInstance()->addModule("header_logo");
	
 /*Top Menu*/
 SystemHtml::getInstance()->addModule("main/topmenu");
 
 if (!(isset($_REQUEST['mes']) && isset($_REQUEST['tipo_cierre']))){
 	if(!isset($_REQUEST['noview'])){     
?>
   <!-- Despliega la Ventana Modal para Seleccionar los Datos de Periodo -->
	<script>
	  
       var modalWindow;
          $(function(){
              modalWindow = new opBonoplan('content_dialog', '<?=$_REQUEST['choice']?>');
              modalWindow.doViewQuestion();
           });
    </script>

 <?php }
 }
?>


<div id="content_dialog" >
  <!-- Este DIV lo usan las ventanas emergentes -->
</div>
<?php SystemHtml::getInstance()->addModule("footer");?>