<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
if (isset($_REQUEST['view_detalle_asesor'])){
	include("view/detalle_gestion.php");
	exit;	 
}
 

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.prospectos.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.ActividadProspectos.js");
	
	//SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	

	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js"); 
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
		
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
	
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
	
	
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");

	
	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");

	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");

	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
 
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css");
	

 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

//echo $protect->getComercialID(); 
$MES=array(
	"1"=>'ENE',
	"2"=>'FEB',
	"3"=>'MAR',
	"4"=>'ABR',
	"5"=>'MAY',
	"6"=>'JUN',
	"7"=>'JUL',
	"8"=>'AGO',
	"9"=>'SEP',
	"10"=>'OC',
	"11"=>'NOV',
	"12"=>'DIC'
	);
	
$fecha_desde="";
$fecha_hasta="";
if (validateField($_REQUEST,"p_fecha_desde") && validateField($_REQUEST,"p_fecha_desde") ){
	$fecha_desde=$_REQUEST['p_fecha_desde'];
 	$fecha_hasta=$_REQUEST['p_fecha_hasta']; 
 
} 	
?>
<style>
.dataTables_filter{
	width:80%;	
	margin-top:0px;
	margin-left:10px;
}
.fsPage{
	width:99%;
}
.fields_hidden{
	display:none;	
}

.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

.ui-timepicker-rtl{ direction: rtl; }
.ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
.ui-timepicker-rtl dl dt{ float: right; clear: right; }
.ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }


.AlertColor5 td
{
 color:#000;
 background-color: #FFD24D !important;
}
.AlertColorDanger td
{
 color:#FFF;
 background-color: #D90000 !important;
}
.even{
 background-color: #E2E4FF !important;
}
.greenButton{
	font-size:12px;	
	text-decoration:none;
}
</style>
<script>
 
 
var pros=null;
$(function(){ 			
  	$(".date_pick").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1900:2050',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
			monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
			dateFormat: 'yy-mm-dd',  
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
			dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
			dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
				
		});	 
 	pros = new Prospectos('content_dialog');
	pros.view_table_gestion();
});

 
 
</script>
 
<div id="inventario_page" class="fsPage">
  <h2 style="color:#FFF;margin-top:0px;">Gestión de Ventas por Asesor</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <?php 

	$SQL=" SELECT * FROM `cierres` where ano=2015 ";
	$rs=mysql_query($SQL);
	while($row= mysql_fetch_assoc($rs)){
?>
          <td width="100" height="30" ><strong><a href=".?mod_prospectos/listar&reporte_asesor&amp;type=<?php echo $_REQUEST['type'];?>&amp;p_fecha_desde=<?php echo $row['fecha_inicio_ventas'];?>&amp;p_fecha_hasta=<?php echo $row['fecha_fin_ventas'];?>"><?php echo $MES[$row['mes']].$row['ano'];?></a></strong></td>
          <?php } ?>
          <td align="center">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center">
      <form method="post" action="./?mod_prospectos/listar&reporte_asesor" > 
      <table width="450" border="0" cellspacing="0" cellpadding="0" style="font-size:9px;">
        <tr>
          <td width="98" align="right">PERIODO DE:</td>
          <td><input  name="p_fecha_desde" type="text" class="filter_ textfield date_pick" style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="p_fecha_desde" readonly="readonly" value="<?php echo $fecha_desde;?>" /></td>
          <td><input  name="p_fecha_hasta" type="text" class="filter_ textfield date_pick" id="p_fecha_hasta" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" value="<?php echo $fecha_hasta;?>" readonly="readonly" /></td>
          <td>&nbsp;
            <input type="submit" name="bt_filter" id="bt_filter" value="Filtrar"  class="btn btn-primary bt-sm"  /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      </form></td>
    </tr>
    <tr>
      <td>
  <ul id="myTab" class="nav nav-tabs">
   <li class="active"><a href="#asesor" data-toggle="tab" >Gestion  Asesor</a> </li> 
    <li ><a href="#pilar" data-toggle="tab" >Gestion  Pilar</a> </li> 
    <li ><a href="#sin_ventas" data-toggle="tab" >Asesores sin prospectaci&oacute;n</a> </li>     
    
</ul>    
      
      </td>
    </tr>
    <tr>
      <td><div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="asesor"> 
                	  
                	  <table width="700" border="0" cellspacing="0" cellpadding="0" class="table table-hover">
                      <thead>
                	    <tr>
                	      <td><strong>ASESOR</strong></td>
                	      <td align="center"><strong>TOTAL</strong></td>
                	      <td align="center"><strong>A</strong></td>
                	      <td align="center"><strong>B</strong></td>
                	      <td align="center"><strong>C</strong></td>
                	      <td align="center"><strong>CITA</strong></td>
                	      <td align="center"><strong>PRESENTACION</strong></td>
                	      <td align="center"><strong>CIERRE</strong></td>
                	      <td align="center"><strong>RESERVAS</strong></td>
              	      </tr>
                      </thead>
                      <tbody>
<?php 



$SQL="SELECT  
	ASESOR AS `ASESOR_VENTAS`, 
	(SUM(`A`)+SUM(`B`)+SUM(`C`)) AS CONTACTO,
	SUM(`A`) AS A,
	SUM(`B`) AS B,
	SUM(`C`) AS C,
	SUM(`CITA`) AS CITA,
	SUM(`PRESENTACION`) AS PRESENTACION,
	SUM(`CIERRE`) AS CIERRE,
	SUM(`RESERVAS`) AS RESERVAS,
	sys_asesor.codigo_asesor AS id_comercial,
	reporte_cache_pilar.codigo_gerente as id_comercial_gerente
 FROM `cache_listado_prospectos` AS reporte_cache_pilar
 INNER JOIN `sys_asesor` ON (`sys_asesor`.`codigo_asesor`=reporte_cache_pilar.`codigo_asesor`)
WHERE 	reporte_cache_pilar.id_status in (5,4,6,9) and 
	(reporte_cache_pilar.`codigo_gerente`='".$protect->getComercialID()."' )
		AND DATE_FORMAT(fecha_inicio, '%Y-%m-%d') BETWEEN '". mysql_escape_string($fecha_desde) ."' AND '". mysql_escape_string($fecha_hasta) ."'  
GROUP BY `ASESOR` ";
 
$rs=mysql_query($SQL);
$CONTACTO=0;
$A=0;
$B=0;
$C=0;
$CITA=0;
$PRE=0;
$CIER=0;
$RESER=0;
while($row= mysql_fetch_assoc($rs)){
	$CONTACTO=$CONTACTO+$row['CONTACTO'];
	$A=$A+$row['A'];
	$B=$B+$row['B'];
	$C=$C+$row['C'];
	$CITA=$CITA+$row['CITA'];
	$PRE=$PRE+$row['PRESENTACION'];	
	$CIER=$CIER+$row['CIERRE'];	
	$RESER=$RESER+$row['RESERVAS'];	
	
	$inf=array(
			"back_url"=>"?mod_prospectos/listar&reporte_asesor&type=&p_fecha_desde=".$fecha_desde."&p_fecha_hasta=".$fecha_hasta,
			"nombre"=>$row['ASESOR_VENTAS'],
			"type"=>"TOTAL",
			"id_comercial"=>$row['id_comercial'],
			"id_comercial_gerente"=>$row['id_comercial_gerente'],
			'fecha_desde'=>$fecha_desde,
			'fecha_hasta'=>$fecha_hasta,
			'ultima_actividad'=>''
			);
				
?>                      
                	    <tr >
                	      <td  class="asesor_detalle"  style="cursor:pointer"  id="<?php echo base64_encode(json_encode($inf));?>"><?php echo $row['ASESOR_VENTAS'];?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php echo base64_encode(json_encode($inf));?>"><?php echo $row['CONTACTO'];?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='A'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['A']; ?></td>
                	      <td align="center" style="cursor:pointer"  id="<?php 
						  $inf['type']='B'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php echo $row['B'];?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta"  id="<?php 
						  $inf['type']='C'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['C'];?></td>
                	      <td align="center" style="cursor:pointer"  id="<?php 
						  $inf['ultima_actividad']='CITA'; 
						  $inf['type']='TOTAL'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php 
						   echo $row['CITA'];?></td>
                	      <td align="center" style="cursor:pointer"  id="<?php 
						 	$inf['ultima_actividad']='PRE'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php  
						   echo $row['PRESENTACION'];?></td>
                	      <td align="center" style="cursor:pointer"  id="<?php 
						  $inf['ultima_actividad']='CIE'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php 
						    echo $row['CIERRE'];?></td>
                	      <td align="center" style="cursor:pointer"  id="<?php 
						 $inf['ultima_actividad']='RES'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php 
						  
						  echo $row['RESERVAS'];?></td>
              	      </tr>
<?php } ?></tbody>
<tfoot>
                	    <tr>
                	      <td>&nbsp;</td>
                	      <td align="center"><strong>TOTAL</strong></td>
                	      <td align="center"><strong>A</strong></td>
                	      <td align="center"><strong>B</strong></td>
                	      <td align="center"><strong>C</strong></td>
                	      <td align="center"><strong>CITA</strong></td>
                	      <td align="center"><strong>PRESENTACION</strong></td>
                	      <td align="center"><strong>CIERRE</strong></td>
                	      <td align="center"><strong>RESERVAS</strong></td>
              	      </tr>
                	    <tr>
                	      <td><strong>TOTALES DEL PERIODO <?php echo date("d/m/Y",strtotime($fecha_desde));?> AL <?php echo date("d/m/Y",strtotime($fecha_hasta));;?></strong></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']); 
						  $inf['id_comercial_gerente']=$protect->getComercialID();
						  echo base64_encode(json_encode($inf));?>"><?php echo $CONTACTO;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="A";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $A;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="B";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $B;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="C";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $C;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CITA'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CITA;?></td>
                	      <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='PRE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $PRE;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CIE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CIER;?></td>
                	      <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='RES'; 						 
						  echo base64_encode(json_encode($inf));?>"><?php echo $RESER;?></td>
              	      </tr>
</tfoot>

       	          </table>
          </div>
          
          <div class="tab-pane fade in" id="pilar">
            <table width="700" border="0" cellspacing="0" cellpadding="0" class="table table-hover">
              <thead>
                <tr>
                  <td><strong>PILAR</strong></td>
                  <td align="center"><strong>TOTAL</strong></td>
                  <td align="center"><strong>A</strong></td>
                  <td align="center"><strong>B</strong></td>
                  <td align="center"><strong>C</strong></td>
                  <td align="center"><strong>CITA</strong></td>
                  <td align="center"><strong>PRESENTACION</strong></td>
                  <td align="center"><strong>CIERRE</strong></td>
                  <td align="center"><strong>RESERVAS</strong></td>
                </tr>
              </thead>
              <tbody>
<?php 

$SQL="SELECT  
	pilar_inicial as `PILAR`, 
	(SUM(`A`)+SUM(`B`)+SUM(`C`)) AS CONTACTO,
	SUM(`A`) AS A,
	SUM(`B`) AS B,
	SUM(`C`) AS C,
	SUM(`CITA`) AS CITA,
	SUM(`PRESENTACION`) AS PRESENTACION,
	SUM(`CIERRE`) AS CIERRE,
	SUM(`RESERVAS`) AS RESERVAS
 FROM cache_listado_prospectos as `reporte_cache_pilar`
WHERE reporte_cache_pilar.id_status in (5,4,6,9) and  
(reporte_cache_pilar.codigo_gerente='".$protect->getComercialID()."' ) and  
	DATE_FORMAT(fecha_inicio, '%Y-%m-%d')  BETWEEN '". mysql_escape_string($fecha_desde) ."' AND '". mysql_escape_string($fecha_hasta) ."'  
  GROUP BY `pilar_inicial` ";
   
   
$rs=mysql_query($SQL);
$CONTACTO=0;
$A=0;
$B=0;
$C=0;
$CITA=0;
$PRE=0;
$CIER=0;
$RESER=0;
while($row= mysql_fetch_assoc($rs)){
	$CONTACTO=$CONTACTO+$row['CONTACTO'];
	$A=$A+$row['A'];
	$B=$B+$row['B'];
	$C=$C+$row['C'];
	$CITA=$CITA+$row['CITA'];
	$PRE=$PRE+$row['PRESENTACION'];	
	$CIER=$CIER+$row['CIERRE'];	
	$RESER=$RESER+$row['RESERVAS'];	
	
		$inf=array(
			"type"=>"TOTAL",
			"id_comercial_gerente"=>$protect->getComercialID(),
			'fecha_desde'=>$fecha_desde,
			'fecha_hasta'=>$fecha_hasta,
			'ultima_actividad'=>'',
			'PILAR'=>''
			);			
?>
                <tr>
                  <td><?php echo $row['PILAR'];?></td>
                  <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php
				   $inf['PILAR']=$row['PILAR']; 
				   echo base64_encode(json_encode($inf));?>"><?php echo $row['CONTACTO'];?></td>
                  <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='A'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['A'];?></td>
                  <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='B'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['B'];?></td>
                  <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='C'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['C'];?></td>
                  <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
				  		  $inf['type']='TOTAL'; 
						  $inf['ultima_actividad']='CITA'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['CITA'];?></td>
                  <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['ultima_actividad']='PRE'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['PRESENTACION'];?></td>
                  <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['ultima_actividad']='CIE'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['CIERRE'];?></td>
                  <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['ultima_actividad']='RES'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['RESERVAS'];?></td>
                </tr>
                <?php } ?>
              </tbody>
				<tfoot>
                	    <tr>
                	      <td>&nbsp;</td>
                	      <td align="center"><strong>TOTAL</strong></td>
                	      <td align="center"><strong>A</strong></td>
                	      <td align="center"><strong>B</strong></td>
                	      <td align="center"><strong>C</strong></td>
                	      <td align="center"><strong>CITA</strong></td>
                	      <td align="center"><strong>PRESENTACION</strong></td>
                	      <td align="center"><strong>CIERRE</strong></td>
                	      <td align="center"><strong>RESERVAS</strong></td>
              	      </tr>
                	    <tr>
                	      <td><strong>TOTALES DEL PERIODO <?php echo date("d/m/Y",strtotime($fecha_desde));?> AL <?php echo date("d/m/Y",strtotime($fecha_hasta));;?></strong></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['PILAR']);
							 
							 unset($inf['ultima_actividad']); 
						  $inf['id_comercial_gerente']=$protect->getComercialID();
						  echo base64_encode(json_encode($inf));?>"><?php echo $CONTACTO;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="A";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $A;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="B";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $B;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="C";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $C;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CITA'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CITA;?></td>
                	      <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='PRE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $PRE;?></td>
                	      <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CIE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CIER;?></td>
                	      <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='RES'; 						 
						  echo base64_encode(json_encode($inf));?>"><?php echo $RESER;?></td>
              	      </tr>
				</tfoot>
            </table>
          </div>
<div class="tab-pane fade in" id="sin_ventas"> 
                	  
                	  <table width="700" border="0" cellspacing="0" cellpadding="0" class="table table-hover">
                      <thead>
                	    <tr>
                	      <td><strong>ASESOR</strong></td>
               	        </tr>
                      </thead>
                      <tbody>
<?php 



$SQL="SELECT CONCAT(sys_personas.primer_nombre,' ',
	sys_personas.segundo_nombre,' ',
	sys_personas.primer_apellido) AS nombre FROM sys_asesor
INNER JOIN `sys_personas` ON (sys_personas.id_nit=sys_asesor.id_nit)
WHERE sys_asesor.status=1 AND sys_asesor.codigo_asesor NOT IN (
SELECT rp.codigo_asesor FROM cache_listado_prospectos AS rp 
WHERE rp.codigo_gerente='".$protect->getComercialID()."' AND rp.id_status IN (5,4,6,9) AND
DATE_FORMAT(rp.fecha_inicio, '%Y-%m-%d')  BETWEEN '". mysql_escape_string($fecha_desde) ."' AND '". mysql_escape_string($fecha_hasta) ."' ) and  sys_asesor.`codigo_gerente_grupo`='".$protect->getComercialID()."' ";
 
$rs=mysql_query($SQL);
while($row= mysql_fetch_assoc($rs)){
 
				
?>                      
                	    <tr >
                	      <td><?php echo $row['nombre'];?></td>
               	        </tr>
<?php } ?></tbody>
<tfoot>
                	    <tr>
                	      <td>&nbsp;</td>
               	      </tr>
                	    <tr>
                	      <td><strong>TOTALES DEL PERIODO <?php echo date("d/m/Y",strtotime($fecha_desde));?> AL <?php echo date("d/m/Y",strtotime($fecha_hasta));;?></strong></td>
               	      </tr>
</tfoot>

       	          </table>
          </div>          
           </div></td>
    </tr>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>