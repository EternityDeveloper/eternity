<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 SystemHtml::getInstance()->includeClass("cobros","Zonas"); 
 

 	if (isset($_REQUEST['distribuccion'])){ 
		include("distribuccion.php");
		exit;	
	} 
 
 	if (isset($_REQUEST['zona_add'])){ 
		include("view/zona_add.php");
		exit;	
	}
 	if (isset($_REQUEST['zona_edit'])){ 
		include("view/zona_edit.php");
		exit;	
	}	
 
 	if (isset($_REQUEST['motorizados'])){  
 		$zonas= new Zonas($protect->getDBLink()); 
		echo json_encode($zonas->getMotorizadosList($_REQUEST['sSearch']));
		exit;	
	}
	
	if (validateField($_REQUEST,"calculateZonaCLICTT") ){
		$data=array("valid"=>true,"contratos"=>0,"clientes"=>0); 	
		if (validateField($_REQUEST,"polygon")){ 
			$zonas= new Zonas($protect->getDBLink());   
			$data['contratos']=$zonas->getTotalContratosFromPolygon(base64_decode($_REQUEST['polygon'])); 
			$data['clientes']=$zonas->getTotalClientesFromPolygon(base64_decode($_REQUEST['polygon'])); 	
		} 
		echo json_encode($data);
		exit;
	}
	
	if (validateField($_REQUEST,"addZona")){
		$data=array("valid"=>false,"mensaje"=>"No se puede completar la operacion debido a que no se han completados todos los cambios obligatorios"); 	
		if (validateField($_REQUEST,"polygon") && 
			validateField($_REQUEST,"motorizado") && 
			validateField($_REQUEST,"nombre_zona")&& 
			validateField($_REQUEST,"codigo_zona")&& 
			validateField($_REQUEST,"oficial_nit")  ){
			SystemHtml::getInstance()->includeClass("cobros","Zonas"); 
 			$zonas= new Zonas($protect->getDBLink()); 
			$data=$zonas->add($_REQUEST); 
		}
		echo json_encode($data);
		
		exit;
	}
	
	if (validateField($_REQUEST,"editZona")){
		$data=array("valid"=>false,"mensaje"=>"No se puede completar la operacion debido a que no se han completados todos los cambios obligatorios");  
		if (validateField($_REQUEST,"polygon") && 
			validateField($_REQUEST,"motorizado") && 
			validateField($_REQUEST,"nombre_zona")&& 
			validateField($_REQUEST,"codigo_zona")&& 
			validateField($_REQUEST,"oficial_nit")  ){
			SystemHtml::getInstance()->includeClass("cobros","Zonas"); 
 			$zonas= new Zonas($protect->getDBLink()); 
			$data=$zonas->edit($_REQUEST); 
		}
		echo json_encode($data);
		
		exit;
	}	
  

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.jqplot.min.js");  
	SystemHtml::getInstance()->addTagScript("script/Class.js"); 
	SystemHtml::getInstance()->addTagScript("script/select2.min.js");  
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js"); 
	
	SystemHtml::getInstance()->addTagScriptByModule("class.Zonas.js"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.TMap.js"); 
	
 	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	   
	SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.AbonoPersona.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoReserva.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoContrato.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
	
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
	
	/*BOOSTRAP SCRIPT*/
	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");
	
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css"); 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	SystemHtml::getInstance()->addTagStyle("script/jquery.jqplot.min.css");
	
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
	SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
	SystemHtml::getInstance()->addTagStyle("css/select2.css");
	
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	
	
	$zonas= new Zonas($protect->getDBLink()); 
?> 
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script type="text/javascript" src="resource/OpenLayers/OpenLayers.js"></script>
<style>
.dataTables_wrapper{
	font-size:12px;	
}
</style>
<script> 

var _dashboard= new Zonas("content_dialog");
 
$(document).ready(function(){ 
	_dashboard.doInit(); 
});

 
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
 
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="450"><span style="font-size: 20px">ZONIFICACION</span></td>
        </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><ul id="myTab" class="nav nav-tabs">
   <li class="active"><a href="#zona" data-toggle="tab" >Zonas</a> </li> 
   <li> <a href="#zone_mapa" id="z_map_tb"  data-toggle="tab">MAPA</a></ul></td>
      </tr>
      <tr>
        <td>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="zona"><span style="font-size:20px;">
                           <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td> <input type="submit" name="btz_agregar" id="btz_agregar" value="Agregar" class="btn btn-default btn-sm"  /></td>
                      </tr>
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td valign="top"><table width="100%" id="list_zonas" border="0" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;">
                              <thead>
                                <tr>
                                  <td><strong>CODIGO</strong></td>
                                  <td><strong>DESCRIPCION</strong></td>
                                  <td><strong>OFICIAL</strong></td>
                                  <td><strong>MOTORIZADO</strong></td>
                                  <td>&nbsp;</td>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
						 foreach($zonas->getZona() as $key =>$zone){ 
						 	$zid=System::getInstance()->Encrypt(json_encode($zone)); 	
						  ?>
                                <tr class="list_zona_item"  style="cursor:pointer" id="<?php echo $zid;?>" poligono="<?php echo base64_encode($zone['polygon'])?>" item_name="<?php echo "(".$zone['zona_id']. ") - ".$zone['zdescripcion']?>">
                                  <td><?php echo $zone['zona_id']?></td>
                                  <td><?php echo $zone['zdescripcion']?></td>
                                  <td><?php echo $zone['nombre_oficial']?></td>
                                  <td><?php echo $zone['nombre_motorizado']?></td>
                                  <td>&nbsp;</td>
                                </tr>
                                <?php } ?>
                              </tbody>
                            </table></td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                 
                </span></div> 
                <div class="tab-pane" id="zone_mapa">
                  <div id="main_map_zona" style="height:500px;width:100%"></div>
                </div>                                
                
            </div>
           </td>
      </tr>
    </table></td>
  </tr>
 
          <tr>
            <td valign="top">&nbsp;</td>
          </tr>
  </table>
 <div id="content_dialog" ></div>