<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}


if (isset($_REQUEST['doViewAsignarCartera'])){
	include("asignar_meta.php");
	exit;
} 

if (validateField($_REQUEST,"asignarCartera") && validateField($_REQUEST,"request") 
&& validateField($_REQUEST,"oficial")){
	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	$oficial=System::getInstance()->Decrypt($_REQUEST['oficial']);
	$cartera=json_decode(base64_decode($_REQUEST['request']));
	//print_r($cartera);
	$cb= new Cobros($protect->getDBLink());
	$request=array();
	foreach($cartera as $key =>$val){
		$request[$val->name]=$val->value;  
	} 
	$meta=$cb->filterMetaCar($request);  
	$cb->asignarCartera($meta,$oficial);
	//print_r($meta);	
	exit;
}  

	
SystemHtml::getInstance()->includeClass("cobros","Zonas"); 
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 

SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");  
SystemHtml::getInstance()->addTagScript("script/Class.js"); 
      
SystemHtml::getInstance()->addTagScriptByModule("class.Zonas.js"); 
SystemHtml::getInstance()->addTagScriptByModule("class.TMap.js");  
SystemHtml::getInstance()->addTagScriptByModule("class.GenerarMeta.js");  
SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	 
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
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");	 

//SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
//SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); 

SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");

SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
SystemHtml::getInstance()->addTagStyle("css/select2.css");


/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");


$zonas= new Zonas($protect->getDBLink()); 

$cobro= new Cobros($protect->getDBLink()); 
$_zona=$cobro->getZona();

 
$meta=$cobro->filterMetaCar($_REQUEST); 
  
?> 
<style>
.dataTables_wrapper{
	font-size:12px;	
}
.nav{
	font-size:12px;	
}

.fixed-table-container {
  width: 98%;
  height:400px; 
  margin: 10px auto;
  background-color: white; 
  position: relative; /* could be absolute or relative */
  padding-top: 13px; /* height of header */
}

.fixed-table-container-inner {
  overflow-x: hidden;
  overflow-y: auto;
  height: 100%;
}
 
.header-background {
  background-color: #D5ECFF;
  height: 30px; /* height of header */
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
} 
table {
  background-color: white;
  width: 100%;
  overflow-x: hidden;
  overflow-y: auto;
}

.th-inner {
  position: absolute;
  top: 0;
  line-height: 30px; /* height of header */
  text-align: left;
  border-left: 1px solid black;
  padding-left: 5px;
  margin-left: -5px; 
}

.th-footer {
  position: absolute;
  top: 1;
  line-height: 30px; /* height of header */
  text-align: left; 
  padding-left: 5px;
  margin-left: -5px; 
}
.first .th-inner {
	border-left: none;
	padding-left: 6px;
}
 
</style>
<script> 

var meta= new GenerarMeta("content_dialog");
 
$(document).ready(function(){ 
	meta.doInit(); 
	
	$("#bt_asignar").click(function(){
		meta.doAsignarView(JSON.stringify($("#list_meta").serializeArray())); 
	});
	
	$("#cuota_condition").val('<?php echo $_REQUEST['cuota_condition'] ?>');
	$("#monto_c_condicion").val('<?php echo $_REQUEST['monto_c_condicion'] ?>');
	$("#f_forma_pago").val('<?php echo $_REQUEST['f_forma_pago'] ?>');
	$("#fecha_p_condicion").val('<?php echo $_REQUEST['fecha_p_condicion'] ?>');
	$("#f_area_de_cobro").val('<?php echo $_REQUEST['f_area_de_cobro'] ?>');
	$("#f_empresa").val('<?php echo $_REQUEST['f_empresa'] ?>');
	$("#f_estatus").val('<?php echo $_REQUEST['f_estatus'] ?>');
	$("#f_oficial").val('<?php echo $_REQUEST['f_oficial'] ?>');
	
});
 
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
 
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
       
      <tr>
        <td>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="zona">
                  <form id="list_meta" method="post" action="./?mod_cobros/delegate&metas&distribucion" >
                   <span style="font-size:20px;">
             
                  <table width="100%" border="0" cellspacing="0" cellpadding="0"> 
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td valign="top"><table width="99%" border="0" cellspacing="0" cellpadding="0" style="margin-left:5px;">
                              <tr>
                                <td width="500" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><strong>FILTRO</strong></td>
                                  </tr>
                                  <tr>
                                    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                      <tr>
                                        <td width="1100"><table width="100%" style="font-size:9px;padding:10px;">
                                          <tr>
                                            <td width="100"><strong>NOMBRES/APELLIDOS</strong></td>
                                            <td colspan="2"><input type="text" name="nombre_completo" id="nombre_completo" class="form-control" style="width:320px;" /></td>
                                            <td><strong>FORMA DE PAGO:</strong></td>
                                            <td><select name="f_forma_pago" id="f_forma_pago"  class="form-control"  style="width:200px;">
                                              <option value="">Seleccione</option>
                                              <?php 

$SQL="SELECT forpago,`descripcion_pago` FROM `formas_pago` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                              <option value="<?php echo $encriptID?>" <?php echo $forpago==$row['forpago']?'selected':''?> ><?php echo $row['descripcion_pago']?></option>
                                              <?php } ?>
                                            </select></td>
                                            <td>&nbsp;</td>
                                            <td><strong>AREA DE COBRO</strong>:</td>
                                            <td><select name="f_area_de_cobro" id="f_area_de_cobro"  class="form-control"  style="width:200px;">
                                              <option value="">Seleccione</option>
                                              <?php 

$SQL="SELECT codigo_sector FROM `areas_d_cobro` GROUP BY `codigo_sector` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                              <option value="<?php echo $encriptID?>" <?php echo $area_cobro==$row['codigo_sector']?'selected':''?> ><?php echo $row['codigo_sector']?></option>
                                              <?php } ?>
                                            </select></td>
                                          </tr>
                                          <tr>
                                            <td><strong>NO. DE CUOTAS</strong></td>
                                            <td width="50"><table width="100" border="0" cellspacing="0" cellpadding="0">
                                              <tr>
                                                <td align="left"><select name="cuota_condition" id="cuota_condition" class="form-control" style="width:130px;">
                                                  <option value="">Seleccione</option>
                                                  <option value="MQ">Mayor</option>
                                                  <option value="MIGQ">Mayor igual</option>
                                                  <option value="MNQ">Menor</option>
                                                  <option value="MNIGQ">Menor igual</option>
                                                  <option value="IQ">Igual</option>
                                                </select></td>
                                                <td><input  name="cuota_numero" type="text" class="form-control" style="cursor:pointer;width:80px;padding-right:10px;" id="cuota_numero" value="<?php echo isset($_REQUEST['cuota_numero'])?$_REQUEST['cuota_numero']:''?>"  /></td>
                                              </tr>
                                            </table></td>
                                            <td width="50">&nbsp;</td>
                                            <td><strong>ESTATUS</strong></td>
                                            <td><select name="f_estatus" id="f_estatus" class="form-control"  style="width:200px;">
                                              <option value="">Seleccione</option>
                                              <?php  
	$SQL="SELECT id_status,descripcion FROM `sys_status` WHERE id_status IN (1,2)";
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){
		$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                              <option value="<?php echo $encriptID?>" <?php echo $id_status==$row['id_status']?'selected':''?>  ><?php echo $row['descripcion']?></option>
                                              <?php } ?>
                                            </select></td>
                                            <td>&nbsp;</td>
                                            <td><strong>EMPRESA</strong>:</td>
                                            <td><select name="f_empresa" id="f_empresa"  class="form-control"  style="width:200px;">
                                              <option value="">Seleccione</option>
                                              <?php 

$SQL=" SELECT `EM_ID`,`EM_NOMBRE` FROM `empresa` WHERE `estatus`=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                              <option value="<?php echo $encriptID?>"  <?php //echo $empresa==$row['EM_ID']?'selected':''?>><?php echo $row['EM_NOMBRE']?></option>
                                              <?php } ?>
                                            </select></td>
                                          </tr>
                                          <tr>
                                            <td><strong>MONTO CUOTAS</strong></td>
                                            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              <tr>
                                                <td><select name="monto_c_condicion" id="monto_c_condicion" class="form-control" style="width:130px;">
                                                  <option value="">Seleccione</option>
                                                  <option value="MQ">Mayor</option>
                                                  <option value="MIGQ">Mayor igual</option>
                                                  <option value="MNQ">Menor</option>
                                                  <option value="MNIGQ">Menor igual</option>
                                                  <option value="IQ">Igual</option>
                                                </select></td>
                                                <td><input name="monto_cuota" type="text" class="form-control" id="monto_cuota" style="width:100px;" value="<?php echo isset($_REQUEST['monto_cuota'])?$_REQUEST['monto_cuota']:''?>"/></td>
                                              </tr>
                                            </table></td>
                                            <td>&nbsp;</td>
                                            <td><strong>FECHA PAGO:</strong></td>
                                            <td><table width="100" border="0" cellspacing="0" cellpadding="0">
                                              <tr>
                                                <td><select name="fecha_p_condicion" id="fecha_p_condicion" class="form-control" style="width:130px;">
                                                  <option value="">Seleccione</option>
                                                  <option value="MQ">Mayor</option>
                                                  <option value="MIGQ">Mayor igual</option>
                                                  <option value="MNQ">Menor</option>
                                                  <option value="MNIGQ">Menor igual</option>
                                                  <option value="IQ">Igual</option>
                                                </select></td>
                                                <td><input  name="f_fecha_pago" type="text" class="form-control" style="cursor:pointer;width:80px;padding-right:10px;" id="f_fecha_pago" value="<?php echo isset($_REQUEST['fecha_contrato'])?$_REQUEST['fecha_contrato']:''?>"  /></td>
                                              </tr>
                                            </table></td>
                                            <td>&nbsp;</td>
                                            <td><strong>OFICIAL</strong>:</td>
                                            <td><select name="f_oficial" id="f_oficial"  class="form-control"  style="width:200px;">
                                              <option value="">Seleccione</option>
                                              <?php 

$SQL="SELECT 
oficial.id_nit AS nitoficial,
CONCAT(oficial.`primer_nombre`,' ',oficial.`segundo_nombre`,' ',oficial.`primer_apellido`,' ',oficial.segundo_apellido) AS nombre_oficial
FROM `cobros_zona`
INNER JOIN `sys_personas` AS oficial ON (`oficial`.id_nit=cobros_zona.`oficial_nit`) ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['nitoficial']);
?>
                                              <option value="<?php echo $encriptID?>"  <?php // echo $empresa==$row['EM_ID']?'selected':''?>><?php echo $row['nombre_oficial']?></option>
                                              <?php } ?>
                                            </select></td>
                                            </tr>
                                        </table></td>
                                        <td colspan="-3" valign="top">
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td><input type="submit" name="bt_filter" id="bt_filter" value="Filtrar"  class="btn btn-primary bt-sm"  /></td>
                                            </tr>
                                            <tr>
                                              <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                              <td><input type="button" name="bt_asignar" id="bt_asignar" value="Asignar"  class="btn btn-danger bt-sm"  /></td>
                                            </tr>
                                          </table></td>
                                      </tr>
                                      <tr>
                                        <td colspan="2"><strong>CARTERA DISTRIBUIDA</strong></td>
                                      </tr>
                                      </table></td>
                                    </tr>
                                </table>
                                
                                <ul id="myTab2" class="nav nav-tabs">
                                    <li class="active"> <a href="#cliente" data-toggle="tab" >POR CLIENTE</a> </li>
                                    <li  > <a href="#por_contrato"   data-toggle="tab">POR CONTRATO</a></li> 
                                    <li> <a href="#por_triangulo"    data-toggle="tab">TRIANGULO DE COBROS</a></li>
                                    <li> <a href="#zone_mapa" id="z_map_tb"  data-toggle="tab">MAPA</a></li>
                                </ul>
                                 <div id="myTabContent" class="tab-content">
                                        <div class="tab-pane fade in active" id="cliente">
                                	  <table id="list_cartera" width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-bordered"  style="font-size:9px;border-spacing:1px;">
                                	    <thead>
                                	      <tr style="background-color:#CCC;height:30px;">
                                	        <td width="276" align="center"><strong>NOMBRES/APELLIDOS</strong></td>
                                	        <td align="center"><strong>ESTATUS</strong></td>
                                	        <td width="276" align="center"><strong>FECHA PAGO</strong></td>
                                	        <td width="276" align="center"><strong>NO. CUOTAS A COBRAR</strong></td>
                                	        <td width="276" align="center"><strong>MONTO CUOTAS</strong></td>
                                	        <td width="276" align="center"><strong>MONTO PENDIENTE</strong></td>
                                	        <td width="276" align="center"><strong>MONTO FUTURO</strong></td>
                                	        <td width="276" align="center"><strong>MONTO A COBRAR</strong></td>
                                	        <td width="276" align="center"><strong>MORA A COBRAR</strong></td>
                                	        <td width="276" align="center"><strong>MANTE. A COBRAR</strong></td>
                                	        <td width="276" align="center"><strong>EMPRESA</strong></td>
                                	        <td width="276" align="center"><strong>OFICIAL</strong></td>
                                	        <td width="276" align="center"><strong>MOTORIZADO</strong></td>
                                	        <td width="276" align="center"><strong>ZONA</strong></td>
                              	        </tr>
                              	      </thead>
                                	    <tbody>
<?php


	


//print_r($meta['agrupado']);
$meta_asignada=$cobro->getCAsignada();
$cuotas_acobrar=0;
$valor_cuota=0;
$monto_acobrar=0;
$monto_pendiente=0;
$monto_futuro=0; 

foreach($meta_asignada as $key=>$_row){
	$row=$meta_asignada[$key];
	$cuotas_acobrar=$cuotas_acobrar+$row['cuotas_acobrar'];
	$valor_cuota=$valor_cuota+$row['monto_neto'];
	$monto_acobrar=$monto_acobrar+$row['monto_acobrar'];
	$monto_pendiente=$monto_pendiente+$row['monto_pendiente'];
	$monto_futuro=$monto_futuro+$row['monto_futuro'];	
	//$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']);
	//$encriptID=System::getInstance()->Encrypt(json_encode($contrato)); 

	
	
?>
                                	      <tr class="list_contract_cartera">
                                	        <td align="center"><?php  echo $row['nombre_cliente'];?></td>
                                	        <td align="center"><?php  echo $row['estatus'];?></td>
                                	        <td align="center"><?php  echo $row['fecha_ingreso'];?></td>
                                	        <td align="center"><?php  echo $row['cuotas_acobrar'];?></td>
                                	        <td align="center"><?php  echo number_format($row['monto_neto'],2);?></td>
                                	        <td align="center"><?php  echo number_format($row['monto_pendiente'],2);?></td>
                                	        <td align="center"><?php  echo $row['monto_futuro'];?></td>
                                	        <td align="center"><?php  echo number_format($row['monto_acobrar'],2);?></td>
                                	        <td align="center"><?php  echo $row['mora_acobrar'];?></td>
                                	        <td align="center"><?php  echo $row['mante_acobrar'];?></td>
                                	        <td align="center"><?php  echo $row['EM_NOMBRE'];?></td>
                                	        <td align="center"><?php  echo $row['nombre_oficial'];?></td>
                                	        <td align="center"><?php  echo $_zona[$row['zona_id']]['motorizado'];?></td>
                                	        <td align="center"><?php  echo $_zona[$row['zona_id']]['zdescripcion'];?></td>
                               				</tr>
                                	      <?php } ?>
                              	      </tbody>
                                      <tfoot>
	       								
                                	      <tr >
                                	        <td colspan="3" align="right"><strong>TOTALES</strong></td>
                                	        <td align="center"><strong><?php echo $cuotas_acobrar;?></strong></td>
                                	        <td align="center"><strong><?php echo number_format($valor_cuota,2);?></strong></td>
                                	        <td align="center"><strong><?php echo number_format($monto_pendiente,2);?></strong></td>
                                	        <td align="center"><strong><?php echo number_format($monto_futuro,2);?></strong></td>
                                	        <td align="center"><strong><?php echo number_format($monto_acobrar,2);?></strong></td>
                                	        <td align="center"><strong><?php echo number_format($mora_acobrar,2);?></strong></td>
                                	        <td align="center"><strong><?php echo number_format($mante_acobrar,2);?></strong></td>
                                	        <td align="center">&nbsp;</td>
                                	        <td align="center">&nbsp;</td>
                                	        <td align="center">&nbsp;</td>
                                	        <td align="center">&nbsp;</td>
                              	        </tr>                                      
                                      </tfoot>
                              	    </table>
                                	</div>
                                    <div  class="tab-pane fade in" id="por_contrato">
                                      <table id="list_cartera2" width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-bordered"  style="font-size:9px;border-spacing:1px;">
                                        <thead>
                                          <tr style="background-color:#CCC;height:30px;">
                                            <td width="276" align="center"><strong>CONTRATO</strong></td>
                                            <td width="276" align="center"><strong>NOMBRES/APELLIDOS</strong></td>
                                            <td align="center"><strong>ESTATUS</strong></td>
                                            <td width="276" align="center"><strong>FECHA PAGO</strong></td>
                                            <td width="276" align="center"><strong>CUOTAS  A COBRAR</strong></td>
                                            <td width="276" align="center"><strong>MONTO CUOTAS</strong></td>
                                            <td width="276" align="center"><strong>MONTO PENDIENTE</strong></td>
                                            <td width="276" align="center"><strong>MONTO FUTURO</strong></td>
                                            <td width="276" align="center"><strong>MONTO A COBRAR</strong></td>
                                            <td width="276" align="center"><strong>MORA A COBRAR</strong></td>
                                            <td width="276" align="center"><strong>MANTE. A COBRAR</strong></td>
                                            <td width="276" align="center"><strong>EMPRESA</strong></td>
                                            <td width="276" align="center"><strong>OFICIAL</strong></td>
                                            <td width="276" align="center"><strong>MOTORIZADO</strong></td>
                                            <td width="276" align="center"><strong>ZONA</strong></td>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php


	

 
foreach($meta['por_contratos'] as $key=>$_row){
	$row=$meta['por_contratos'][$key];
  
?>
                  <tr class="list_contract_cartera">
                    <td align="center"><?php  echo $row['serie_contrato']." ".$row['no_contrato'];?></td>
                    <td align="center"><?php  echo $row['nombre_cliente'];?></td>
                    <td align="center"><?php  echo $row['estatus'];?></td>
                    <td align="center"><?php  echo $row['fecha_ingreso'];?></td>
                    <td align="center"><?php  echo number_format($row['cuotas_acobrar'],2);?></td>
                    <td align="center"><?php  echo number_format($row['monto_neto'],2);?></td>
                    <td align="center"><?php  echo $row['monto_pendiente'];?></td>
                    <td align="center"><?php  echo $row['monto_futuro'];?></td>
                    <td align="center"><?php  echo number_format($row['monto_acobrar'],2);?></td>
                    <td align="center"><?php  echo $row['mora_acobrar'];?></td>
                    <td align="center"><?php  echo $row['mante_acobrar'];?></td>
                    <td align="center"><?php  echo $row['EM_NOMBRE'];?></td>
                    <td align="center"><?php  echo $row['nombre_oficial'];?></td>
                    <td align="center"><?php  echo $_zona[$row['zona_id']]['motorizado'];?></td>
                    <td align="center"><?php  echo $_zona[$row['zona_id']]['zdescripcion'];?></td>
                  </tr>
                  <?php } ?>
                                        </tbody>
                                        <tfoot>
                                          <tr >
                                            <td colspan="4" align="right"><strong>TOTALES</strong></td>
                                            <td align="center"><strong><?php echo $cuotas_acobrar;?></strong></td>
                                            <td align="center"><strong><?php echo number_format($valor_cuota,2);?></strong></td>
                                            <td align="center"><strong><?php echo number_format($monto_pendiente,2);?></strong></td>
                                            <td align="center"><strong><?php echo number_format($monto_futuro,2);?></strong></td>
                                            <td align="center"><strong><?php echo number_format($monto_acobrar,2);?></strong></td>
                                            <td align="center"><strong><?php echo number_format($mora_acobrar,2);?></strong></td>
                                            <td align="center"><strong><?php echo number_format($mante_acobrar,2);?></strong></td>
                                            <td align="center">&nbsp;</td>
                                            <td align="center">&nbsp;</td>
                                            <td align="center">&nbsp;</td>
                                            <td align="center">&nbsp;</td>
                                          </tr>
                                        </tfoot>
                                      </table>
                                    </div>
                                    <div  class="tab-pane fade in" id="por_triangulo">
                                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td><table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
                                              <tr>
                                                <td>&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td>&nbsp;</td>
                                              </tr>
                                              <tr>
                                                <td>&nbsp;</td>
                                                <td align="center"><strong><?php echo number_format($valor_cuota,2);?></strong></td>
                                                <td>&nbsp;</td>
                                              </tr>
                                              <tr>
                                                <td>&nbsp;</td>
                                                <td width="200" align="center"><img src="images/Triangulo.png" width="245" height="231" /></td>
                                                <td>&nbsp;</td>
                                              </tr>
                                              <tr>
                                                <td align="right"><strong><?php echo number_format($monto_pendiente,2);?></strong></td>
                                                <td align="center">&nbsp;</td>
                                                <td><strong><?php echo number_format($monto_futuro,2);?></strong></td>
                                              </tr>
                                              <tr>
                                                <td align="right">&nbsp;</td>
                                                <td align="center"><strong>TOTAL A COBRAR</strong></td>
                                                <td>&nbsp;</td>
                                              </tr>
                                              <tr>
                                                <td align="right">&nbsp;</td>
                                                <td align="center"><strong><?php echo number_format($monto_acobrar,2);?></strong></td>
                                                <td>&nbsp;</td>
                                              </tr>
                                            </table></td>
                                        </tr>
                                      </table>
                                    </div>
                                    
                                    <div class="tab-pane" id="zone_mapa">
                                   	 xxxxxxxxxx
                                    </div>  
                                    
                                </div>
                                
                                
                                
                                
                                </td>
                              </tr>
                            </table>
                              
                            </td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                 
                </span>
                </form></div> 
                                         
                
            </div>
           </td>
      </tr>
    </table></td>
  </tr>
 
          <tr>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top">&nbsp;</td>
          </tr>
  </table>
 <div id="content_dialog" ></div>