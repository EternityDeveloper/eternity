<?php 
 if (!isset($protect)){
	exit;
 }
 
 if (isset($_REQUEST['view'])){
	include ("view/pl_periodo_cierre.php");
	exit;	
 }
 
 SystemHtml::getInstance()->addTagScript("script/Class.js");  
 SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
 SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");

 SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
 

 SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");

 SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
 SystemHtml::getInstance()->addTagScriptByModule("class.opPlanilla.js","planillas/asesores"); 
 
 
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
              modalWindow = new opPlanilla('content_dialog','<?=$_REQUEST['choice']?>');
              modalWindow.doViewQuestion();
           });
    </script>

<?php 
	 }
   }
 ?>


<div id="content_dialog" >
  <!-- Este DIV lo usan las ventanas emergentes -->
</div>
<?php SystemHtml::getInstance()->addModule("footer");?>