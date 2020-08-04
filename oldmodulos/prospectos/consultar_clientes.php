<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

 
if (isset($_REQUEST['view_account_detail'])){ 
	$info=consultar_cliente(2,$_REQUEST['contrato']);
	print_r($info);
	exit;
} 
 
 $info=consultar_cliente(2,$_REQUEST['x_search']);
 
 print_r($info);

exit;
function consultar_cliente($type,$contrato,$token=""){
	$dat="consult=".$type."&search=". urlencode($contrato)."&contrato=".$token;
	$url="http://wmserver.memorial.com.do/xmservices.php?".$dat; 
	$headers=array(
		0=>'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		1=>'Connection:keep-alive',
		2=>'Accept-Language:es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3',
		3=>'Accept-Encoding:gzip, deflate',
		4=>'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0',
		5=>'Cache-Control: max-age=0'
	); 
	$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0";
	$cookie="test.txt";
    $rs = curl_init($url);
	curl_setopt($rs, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($rs, CURLOPT_POST, 1);  
    curl_setopt($rs, CURLOPT_HEADER, 0);
	curl_setopt($rs, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($rs, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($rs, CURLOPT_FOLLOWLOCATION, 1);
    $raw=curl_exec($rs);
  	$contentType = curl_getinfo($rs, CURLINFO_CONTENT_TYPE); 
    curl_close($rs);   
	//$raw=explode("\n",$raw);
    return $raw;
}	
 


	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.prospectos.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.ActividadProspectos.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
	
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
	
	
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");

	

	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");

	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 
 
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

//echo $protect->getComercialID(); 
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
#prospecto_list_fracasado_wrapper.dataTables_wrapper{
	height:50px; !important;
}
</style>
<script>
var consulta;
 
$(function(){ 
	$("#cliente_list").dataTable({
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		  "oLanguage": {
				"sLengthMenu": "Mostrar _MENU_ registros por pagina",
				"sZeroRecords": "No se ha encontrado - lo siento",
				"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
				"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
				"sInfoFiltered": "(filtrado de _MAX_ total registros)",
				"sSearch":"Buscar"
			}
	});
	

	var consultar_cliente = new Class({
		initialize : function(){
			$("#_search").click(function(){
			//	alert($('#txt_search').val());
				window.location.href="?mod_prospectos/consultar_clientes&x_search="+$('#txt_search').val()	 
			});
		},
		_search : function(contrato){
			var instance= this;
			instance.post("./?mod_prospectos/consultar_clientes",{
				"view_account_detail":'1',
				"contrato":contrato 
			},function(data){ 
				var dialog=instance.createDialog("content_dialog","Estado de cuenta",data,900);
			//	instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/950), 0]); 
				 
			});
		}
	});
	
	
	consulta= new consultar_cliente();
});
 
function openEstado(id){
	 consulta._search(id);
} 
  
</script> 
<div id="inventario_page" class="fsPage">
  <h2>Consulta de cliente</h2>
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
 	  <tr>
 	    <td><form id="form1" name="form1" method="post" action="return false;">
 	      <input type="text" name="txt_search" id="txt_search" />
 	      <input type="button" name="_search" id="_search" value="Buscar" />
        </form></td>
      </tr>
 	  <tr>
 	    <td><table border="0" align="left" class="display" id="cliente_list" style="font-size:13px;width:50%">
 	      <thead>
 	        <tr>
 	          <th>Contrato</th>
 	          <th>Nombre cliente</th>
            </tr>
          </thead>
 	      <tbody>
 	        <?php 

if (isset($_REQUEST['x_search'])){
	$info=consultar_cliente(1,$_REQUEST['x_search']);
	$data=json_decode(trim($info));	 
	print_r($info);
	if (isset($data->valid)){
		if ($data->valid=="1"){ 
			foreach($data->data as $key =>$val){
?>
 	        <tr>
 	          <th height="25"><a href="#" onclick="openEstado('<?php echo $val->DESCRIPCION?>')" style="text-decoration:none"><?php echo $val->DESCRIPCION?></a></th>
 	          <th><a href="#"  onclick="openEstado('<?php echo $val->DESCRIPCION?>')"  style="text-decoration:none"><?php echo $val->CLI_DESCRIPCION?></a></th>
            </tr>
 	        <?php
			}
		}
	}
} ?>
          </tbody>
        </table></td>
      </tr>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>