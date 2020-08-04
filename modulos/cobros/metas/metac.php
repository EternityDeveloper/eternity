<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 SystemHtml::getInstance()->includeClass("cobros","Zonas"); 
 SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
 
 
if (isset($_REQUEST['distribucion'])){
	include("carrito_meta.php");
	exit;
}  


$fecha_desde="";
$fecha_hasta="";
if (validateField($_REQUEST,"p_fecha_desde") && validateField($_REQUEST,"p_fecha_desde") ){
	$fecha_desde=$_REQUEST['p_fecha_desde'];
 	$fecha_hasta=$_REQUEST['p_fecha_hasta']; 
	
	$zonas= new Zonas($protect->getDBLink()); 
	
 	$cobro= new Cobros($protect->getDBLink()); 
	$cobro->createCAsignada();
	$_zona=$cobro->getZona();
	$meta=$cobro->checkMetaCobros2($fecha_desde,$fecha_hasta); 
 
	$cobro->addMetaCar($meta); 
	 
	header("location: ./?mod_cobros/delegate&metas&distribucion");
	exit;
} 	
 
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");  
	SystemHtml::getInstance()->addTagScript("script/Class.js");  
	    
	SystemHtml::getInstance()->addTagScriptByModule("class.Zonas.js"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.TMap.js");  
	SystemHtml::getInstance()->addTagScriptByModule("class.GenerarMeta.js");  
 	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
   
	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js"); 
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css"); 
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	
	

?> 
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script type="text/javascript" src="resource/OpenLayers/OpenLayers.js"></script>
<style>
.dataTables_wrapper{
	font-size:12px;	
}
.nav{
	font-size:12px;	
}
</style>
<script> 

var meta= new GenerarMeta("content_dialog");
 
$(document).ready(function(){ 
	meta.doInit(); 
});

 
</script>

<div id="content_dialog" >
<form method="post" action="./?mod_cobros/delegate&metas" > 
  <table width="100%" border="0" cellspacing="0" cellpadding="0"> 
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" valign="top"><table width="450" border="0" cellspacing="0" cellpadding="0" style="font-size:9px;">
              <tr>
                <td width="98" align="right">PERIODO DE:</td>
                <td><input  name="p_fecha_desde" type="text" class="filter_ textfield date_pick" style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="p_fecha_desde" readonly="readonly" value="<?php echo $fecha_desde;?>" /></td>
                <td><input  name="p_fecha_hasta" type="text" class="filter_ textfield date_pick" id="p_fecha_hasta" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" value="<?php echo $fecha_hasta;?>" readonly="readonly" /></td>
                <td>&nbsp;
                  <input type="submit" name="bt_filter" id="bt_filter" value="Generar"  class="btn btn-primary bt-sm"  /></td>
                </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table> 
</form>
</div>