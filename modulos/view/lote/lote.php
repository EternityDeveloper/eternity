<?php 
if (!isset($protect)){
	exit;	
} 
 
SystemHtml::getInstance()->addTagScript("script/Class.js");   

SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");  
SystemHtml::getInstance()->addTagScriptByModule("class.PagoComponent.js","caja");  
 SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
 SystemHtml::getInstance()->addTagScriptByModule("lote/class.AnularRecibos.js","caja");
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
?>
<style>
.recibo_remove{}
</style>
<script>
$(function(){
	var ft= new AnularRecibos('content_dialog');
	ft.doView();
});
</script>


 <div id="content_dialog" ></div>