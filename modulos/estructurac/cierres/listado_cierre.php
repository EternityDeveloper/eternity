<?php
 
if (!isset($protect)){
	exit;
}
 
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/jquery.jqplot.min.js"); 

SystemHtml::getInstance()->addTagScript("script/Class.js");  

SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");

SystemHtml::getInstance()->addTagScriptByModule("class.CierreCobroVentas.js"); 


SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
SystemHtml::getInstance()->addTagStyle("script/jquery.jqplot.min.css");

/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
?>
<script> 

var _cierres= new CierreCobroVentas("content_dialog");
$(document).ready(function(){
	_cierres.doInit();
});
 
</script>
<div class="fsPage" style="width:98%">
<form action="" method="post" id="cierre_form">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" style="font-size:18px;">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" style="font-size:18px;"><strong>CIERRES DE VENTAS Y COBROS</strong></td>
  </tr>
  <tr>
    <td align="center" style="font-size:18px;">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
     <thead>
      <tr style="background-color:#CCC;color:#FFF ">
        <th height="30" align="center"><strong>Año</strong></th>
        <th align="center"><strong>Mes</strong></th>
        <th align="center"><strong>FECHA INICIO - VENTAS</strong></th>
        <th align="center"><strong>FECHA CIERRE- VENTAS</strong></th>
        <th align="center">FECHA DE INICIO - COBROS</th>
        <th align="center">FECHA DE CIERRE - COBROS</th>
      </tr>
      </thead>
<?php
$SQL="SELECT 
	mes,
	ano,
	DATE_FORMAT(fecha_inicio_ventas, '%d-%m-%Y') AS fecha_inicio_ventas,
	DATE_FORMAT(fecha_fin_ventas, '%d-%m-%Y') AS fecha_fin_ventas,
	DATE_FORMAT(fecha_inicio_cobros, '%d-%m-%Y') AS fecha_inicio_cobros,
	DATE_FORMAT(fecha_fin_cobros, '%d-%m-%Y') AS fecha_fin_cobros
 FROM `cierres` WHERE ano='2014'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){ 
	$fecha=$row['ano']."_".$row['mes'];
 ?>      
      <tr>
        <td align="center"><?php echo $row['ano']?></td>
        <td height="35" align="center"><?php echo $row['mes']?></td>
        <td align="center"><input  name="fecha_inicio_ventas_<?php echo $fecha;?>" type="text" class="textfield textfieldsize required fecha" style="width:130px;padding-right:10px;cursor:pointer;background:url(images/calendar.png) no-repeat;;background-position:95% 50%;" id="fecha_inicio_ventas_<?php echo $fecha;?>" value="<?php echo $row['fecha_inicio_ventas']?>"  /></td>
        <td align="center"><input  name="fecha_cierre_ventas_<?php echo $fecha;?>" type="text" class="textfield textfieldsize required fecha" style="width:130px;padding-right:10px;cursor:pointer;background:url(images/calendar.png) no-repeat;;background-position:95% 50%;" id="fecha_cierre_ventas_<?php echo $fecha;?>"  value="<?php echo $row['fecha_fin_ventas']?>"/></td>
        <td align="center"><input  name="fecha_inicio_cobros_<?php echo $fecha;?>" type="text" class="textfield textfieldsize required fecha" style="width:130px;padding-right:10px;cursor:pointer;background:url(images/calendar.png) no-repeat;;background-position:95% 50%;" id="fecha_inicio_cobros_<?php echo $fecha;?>"  value="<?php echo $row['fecha_inicio_cobros']?>" /></td>
        <td align="center"><input  name="fecha_cierre_cobro_<?php echo $fecha;?>" type="text" class="textfield textfieldsize required fecha" style="width:130px;padding-right:10px;cursor:pointer;background:url(images/calendar.png) no-repeat;;background-position:95% 50%;" id="fecha_cierre_cobro_<?php echo $fecha;?>"  value="<?php echo $row['fecha_fin_cobros']?>"/></td>
      </tr>
<?php } ?>        
      <tr>
        <td>&nbsp;</td>
        <td height="30" align="center">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="6" align="center">
        <button type="button" class="greenButton" id="bt_m_save" >Guardar</button> </td>
        </tr>
      <tr>
        <td height="30" colspan="6">&nbsp;</td>
      </tr>
    
    </table></td>
    </tr>
</table>
</form>
</div>