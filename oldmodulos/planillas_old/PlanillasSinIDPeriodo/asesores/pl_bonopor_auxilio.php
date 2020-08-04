<?php 
//http://localhost/sandpit/?mod_planillas/asesores/pl_bonopor_auxilio&noview&periodo=QXE0Wm11WmVzWXczUmhKVXdVaHNjdz09&type=dy81c0kzTzJnVXExTkMzaXlteGhWQT09
 //http://localhost/sandpit/?mod_planillas/asesores/pl_bonopor_auxilio&noview&periodo=S3RpSDlUQTMzSkhMY0E0YkljYnh1QT09&type=YnZsWThEeEpoamUxTTJmc2EyQ2gvUT09
 if (!isset($protect)){
	exit;
 }
 
 function dias_transcurridos($fecha_i,$fecha_f)
  {
	
	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias 	= abs($dias)/30; 
	$dias   = floor($dias);
			
	return $dias;
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
 
 /* IdConcepto para Bono por Auxilio = 3 */
 $idConcepto  = 3;
  
 if( (in_array($mes, range(1,12))) && (($tipo_cierre=="P" || $tipo_cierre=="T")) ){
 	
	 $sql = "select count(1) as bono
	          from cm_detplanilla_asesor_tbl
			 where anio = " .(int)$anio. "
			   and mes  = " .(int)$mes.  "
			   and idconcepto  = ".(int)$idConcepto."
			   and tipo_cierre = '".mysql_real_escape_string($tipo_cierre)."'";

	$rsVerifica  = mysql_query($sql);
	$rowVerifica = mysql_fetch_array($rsVerifica);
	
	if($rowVerifica['bono'] == 0){
		/*Fecha de Periodo de Trabajo*/
	    $sql = "select fecha_inicio_ventas as fechaini, "
					  .(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
				 from cierres
				where mes = " .(int)$mes. "
				  and ano = " .(int)$anio;
	  			
		$result    = mysql_query($sql);
		$rsPeriodo = mysql_fetch_array($result);
		
	    /* Equipo de Asesores*/
	     $sql = "select a.codigo_asesor,
		               b.fecha_ingreso_cia
			      from sys_asesor a,
				       sys_personas b			       
			     where a.id_nit = b.id_nit 
				   and a.status = 1
				   and a.codigo_asesor is not null
				   and b.fecha_ingreso_cia is not null
                 order by CAST(a.codigo_asesor as unsigned)";
		
				 
		$rsAsesores = mysql_query($sql);
		
		while($rowAsesores = mysql_fetch_array($rsAsesores)){
		   
		   $antiguedad = dias_transcurridos($rowAsesores['fecha_ingreso_cia'], $rsPeriodo['fechafin']);
	        
		    if(in_array($antiguedad, range(1,6))){
				
				$query = "select cumplimiento, 
                                 bonoprimario,
                                 ventacontrato,
                                 bonoadicional
                            from cm_bonoauxilio_tbl
                           where mes = " .(int)$antiguedad;
				
				$rsMeses  = mysql_query($query);
				$rowMeses = mysql_fetch_array($rsMeses);
								
				$query    = "select count(1) as conteo 
					           from contratos
					          where codigo_asesor = ".$rowAsesores['codigo_asesor']." 
					            and estatus = 1
					            and fecha_ingreso between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'";
				
								
		        $result       = mysql_query($query);
	            $contratos    = mysql_fetch_array($result);

				$montoBonoAux = $rowMeses['bonoprimario'];
				 
				if($contratos['conteo'] > 0){
				   $montoBonoAdic = (float)$rowMeses['bonoadicional'];  						
				}else{
				   $montoBonoAdic = (float)0 ;	
				} 		
								
		        $montoTotal = $montoBonoAux + $montoBonoAdic;
				
				
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
								 .(int)$rowAsesores['codigo_asesor'].","
								 .(int)$idConcepto.","
								 .(float)$montoTotal.",'"
								 .mysql_real_escape_string($usuario)."','"
								 .$fechau."')";
					
			$rsUpd = mysql_query($insDatos);				
				
			}	
		
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
 SystemHtml::getInstance()->addTagScriptByModule("class.opBonoauxilio.js","planillas/asesores"); 
 
 
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
              modalWindow = new opBonoauxilio('content_dialog','<?=$_REQUEST['choice']?>');
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