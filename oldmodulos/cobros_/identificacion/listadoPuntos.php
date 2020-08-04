<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


if (!$protect->getIfAccessPageById(101)){exit;}
 

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");  

	SystemHtml::getInstance()->addTagScript("script/Class.js");    
	
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("class.CIdentifciado.js"); 


	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");  
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");


	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("contratos","Contratos");   
		  
//	$cobros= new Cobros($protect->getDBLINK()); 	
?> <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<style>
.fsPage{
	width:99%;	
}
 #map-canvas {
        height: 500px;
        margin: 0px;
        padding: 0px
      }
#panel {
	position: absolute;
	top: 105px;
	left: 60%;
	margin-left: -180px;
	width: 350px;
	z-index: 5;
	background-color: #fff;
	padding: 5px;
	border: 1px solid #999;
	display:none;
  }	  
</style>
<script> 

var infowindow = new google.maps.InfoWindow();
var marker;
function initialize() {
  geocoder = new google.maps.Geocoder();
  var latlng = new google.maps.LatLng(40.730885,-73.997383);
  var mapOptions = {
	zoom: 8,
	center: latlng,
	mapTypeId: 'roadmap'
  }
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

var _ident= new CIdentifciado("content_dialog");
$(document).ready(function(){
	_ident.doInit();
});
 

google.maps.event.addDomListener(window, 'load', initialize);


function openClient(obj){
	_ident.doCreatePoint(obj);
}

</script>
<div class="fsPage">
<h2>Listado de clientes no identificados</h2>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
	    <td width="33%" valign="top"><table width="346" align="left" border="0" class="display" id="role_list" style="font-size:13px">
	      <thead>
	        <tr>
	          <th width="61">FECHA</th>
	          <th width="88" height="25">CONTRATO</th>
	          <th width="59">DIRECCION</th>
	          <th width="89">MOTORIZADO</th>
	          <th width="27">&nbsp;</th>
            </tr>
          </thead>
	      <tbody>
	        <?php

	$SQL="SELECT * FROM `localizacion_cobro_cliente` WHERE estatus not in ('IDENTIFICADO','INVALIDO') order by fecha_insert DESC ";
	$rs=mysql_query($SQL); 
	while($row=mysql_fetch_assoc($rs)){  
?>
	        <tr>
	          <th><?php echo $row['fecha_insert']?></th>
	          <th height="20"><?php echo $row['contrato']?></th>
	          <th><?php echo $row['direccion']?></th>
	          <th><?php echo $row['motorizado']?></th>
	          <th><a href="#" class="option" data="<?php echo base64_encode(json_encode($row));?>" id="<?php echo System::getInstance()->Encrypt($row['id']);?>" onclick="openClient(this);" >Elegir</a></th>
            </tr>
	        <?php } ?>
          </tbody>
        </table></td>
	    <td width="67%" valign="top">
         <div id="panel">
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="80" align="right"><strong>Contrato</strong></td>
              <td id="_id_field"><input name="contrato" type="text" id="contrato" /></td>
              <td  ><input type="button" value="Verificado" id="_verificar" /></td>
             </tr>
            <tr>
              <td align="right"><strong>Direccion</strong></td>
              <td id="_id_field2"><input name="direccion" type="text" id="direccion" readonly="readonly" /></td>
              <td align="right">&nbsp;</td>
             </tr>
            <tr>
              <td align="right"><strong>Descripcion:</strong></td>
              <td id="_id_field3"><input name="descripcion" type="text" id="descripcion" /></td>
              <td align="left"><input type="button" id="_cancelar" value="No Cliente" /></td>
             </tr>
         
           </table>
         </div>
        <div id="map-canvas"></div></td>
    </tr>
  </table>
</div>
<?php SystemHtml::getInstance()->addModule("footer");?>