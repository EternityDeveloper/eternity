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
 $anio        = date("Y");
 $usuario     = UserAccess::getInstance()->getID();
 $fechau      = date("Y/m/d");
 
 /* IdConcepto para Diferidos = 4 */
 $idConcepto  = 4;
 
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

		$sql = "select a.codigo_asesor, SUM(monto_diferido) as monto
				  from ( 
						select codigo_asesor,
							   anio,
							   mes,
							   tipo_cierre,
							   serie,
							   contrato,
							   monto_diferido,
							   diferidos_pagados
						  from pl_planillas_asesores
						 where diferidos > diferidos_pagados
						   and anio = " .(int)$anio."
						   and tipo_cierre = '" .mysql_real_escape_string($tipo_cierre)."'
						   and mes < " .(int)$mes ."
						   ) a
				 group by a.codigo_asesor";
		
				 			  
		$rsDiferidos = mysql_query($sql);
		
		while($row = mysql_fetch_array($rsDiferidos)){
			
			/*Inserta si no estan*/
			 $maestro = "select count(1) as conteo
			              from cm_planilla_asesor_tbl
						 where anio = ".(int)$anio." 
						   and mes = ".(int)$mes."
						   and tipo_cierre = '".mysql_real_escape_string($tipo_cierre)."' 
						   and codigo_asesor = ".(int)$row['codigo_asesor'];
			
						   
			$rsMaestro = mysql_query($maestro);
			$rsConteo  = mysql_fetch_array($rsMaestro);
			
			if($rsConteo['conteo']==0){
			   $sqlMaestro = "Insert into cm_planilla_asesor_tbl( anio,
			                                                      mes,
																  tipo_cierre,
																  codigo_asesor,
																  usuario,
																  fechau)
														 values(".(int)$anio.","  
														         .(int)$mes. ",'" 
																 .mysql_real_escape_string($tipo_cierre)."',"
																 .(int)$row['codigo_asesor'].",'"
																 .mysql_real_escape_string($usuario)."','"
																 .$fechau."')";
			  
			  $insMaestro = mysql_query($sqlMaestro);
			}
			
			
			
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
								 .(float)$row['monto'].",'"
								 .mysql_real_escape_string($usuario)."','"
								 .$fechau."')";
			
						
			$rsUpd = mysql_query($insDatos);
		} 
		
		/*Actualizamos la tabla donde esta el historico de diferidos*/
		$sql = "select codigo_asesor,
							   anio,
							   mes,
							   tipo_cierre,
							   serie,
							   contrato,
							   monto_diferido,
							   diferidos_pagados
						  from pl_planillas_asesores
						 where diferidos > diferidos_pagados
						   and anio <= " .(int)$anio."
						   and tipo_cierre = '" .mysql_real_escape_string($tipo_cierre)."'
						   and mes < " .(int)$mes;
		
		$rsActualiza = mysql_query($sql);
		
		while($rowDif = mysql_fetch_array($rsActualiza)){
		   $sqlUpd = "Update pl_planillas_asesores
		                 set diferidos_pagados = diferidos_pagados + 1
					   where codigo_asesor = ".$rowDif['codigo_asesor']." 
					     and anio          = ".$rowDif['anio']. " 
						 and mes           = ".$rowDif['mes']. "
						 and tipo_cierre   = '".$rowDif['tipo_cierre']."'
						 and serie         = '".$rowDif['serie']."'
						 and contrato      = '" .$rowDif['contrato']. "'";	
		
		  $rsUpdDif = mysql_query($sqlUpd);
		  	
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
 SystemHtml::getInstance()->addTagScriptByModule("class.opDiferidos.js","planillas/asesores"); 
 
 
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
              modalWindow = new opDiferidos('content_dialog', '<?=$_REQUEST['choice']?>');
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