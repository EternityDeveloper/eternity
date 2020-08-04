<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['wizard'])){
	include("view/wizard_cierre.php");
	exit;
}
 
if (isset($_REQUEST['createActa']))
{ 
	if (validateField($_REQUEST,"id_acta") && validateField($_REQUEST,"contratos") ){	  
		$id_acta=json_decode(System::getInstance()->Decrypt($_REQUEST['id_acta'])); 
		SystemHtml::getInstance()->includeClass("cobros","Actas");    
		$actas=new Actas($protect->getDBLink());	 
		echo json_encode($actas->generarActa($id_acta,$_REQUEST['descripcion']));
		exit;
	}
}

/* GUARDAR COMENTARIO DEL ACTA */
if (isset($_REQUEST['saveComentary']))
{ 
	if (validateField($_REQUEST,"descripcion") && validateField($_REQUEST,"contrato") ){	  
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
		SystemHtml::getInstance()->includeClass("cobros","Actas");    
		$actas=new Actas($protect->getDBLink());	 
 
		$rt=$actas->saveComentaryActa($contrato->id_acta,
								  $contrato->serie_contrato,
								  $contrato->no_contrato,
								  $_REQUEST['descripcion']);
		echo json_encode($rt);
		exit;
	}
}

if (isset($_REQUEST['validate_upload']))
{
	SystemHtml::getInstance()->includeClass("cobros","Actas");    
	$actas=new Actas($protect->getDBLink());	
	echo json_encode($actas->validate_upload());
	exit;
}
 

if (isset($_REQUEST['upload_doc'])){
 	if (validateField($_REQUEST,"info_acta")){	  
		$listado_c=array();
 		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['info_acta'])); 
	 	if (isset($contrato->id_acta->idacta)){ 
			SystemHtml::getInstance()->includeClass("cobros","Actas");    
			$actas=new Actas($protect->getDBLink());	
			$actas->addDocument($contrato->id_acta->idacta,$_REQUEST);
		}
	} 
	exit;
}

if (isset($_REQUEST['download_ctr'])){
 	if (validateField($_REQUEST,"inf")){	 
 		$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['inf'])); 
		include("view/download_pdf_acta.php"); 
	} 
	exit;
}

 

$id_acta=json_decode(System::getInstance()->Decrypt($_REQUEST['id_acta']));

if (!isset($id_acta->idacta)){
	exit;
}
 
	
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");   
SystemHtml::getInstance()->addTagScript("script/Class.js"); 
 
SystemHtml::getInstance()->addTagScript("script/bootstrap-wizard.js");    
SystemHtml::getInstance()->addTagScript("css/chosen/chosen.jquery.js");    
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
SystemHtml::getInstance()->addTagStyle("css/bootstrap-wizard.css");
SystemHtml::getInstance()->addTagStyle("css/chosen/chosen.css");

SystemHtml::getInstance()->addTagScript("script/jquery.ui.widget.js","contratos");  
SystemHtml::getInstance()->addTagScript("script/jquery.iframe-transport.js","contratos");  
SystemHtml::getInstance()->addTagScript("script/jquery.fileupload.js","contratos");   
 
SystemHtml::getInstance()->addTagStyle("css/stl_upload.css");	
	 
SystemHtml::getInstance()->addTagStyle("css/select2.css");
SystemHtml::getInstance()->addTagScriptByModule("class.cActas.js","cobros"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
SystemHtml::getInstance()->addTagScript("script/jquery.base64.min.js");

 
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
	
 
// print_r($id_acta);
?> 
<script> 

var _actas= new cActas("content_dialog");
 
$(function(){ 
 	_actas.doInitCierre()	  
});

function validateServerLabel(el) {
	var name = el.val();
	var retValue = {};

	if (name == "") {
		retValue.status = false;
		retValue.msg = "Please enter a label";
	} else {
		retValue.status = true;
	}

	return retValue;
};
 
</script>
<style type="text/css">
	.wizard-modal p {
		margin: 0 0 10px;
		padding: 0;
	}

	#wizard-ns-detail-servers, .wizard-additional-servers {
		font-size: 12px;
		margin-top: 10px;
		margin-left: 15px;
	}
	#wizard-ns-detail-servers > li, .wizard-additional-servers li {
		line-height: 20px;
		list-style-type: none;
	}
	#wizard-ns-detail-servers > li > img {
		padding-right: 5px;
	}

	.wizard-modal .chzn-container .chzn-results {
		max-height: 150px;
	}
	.wizard-addl-subsection {
		margin-bottom: 40px;
	}
	.create-server-agent-key {
		margin-left: 15px; 
		width: 90%;
	}
</style>
<input type="submit" name="lb_regresar" id="lb_regresar" value="REGRESAR" class="btn btn-default" onclick="window.history.back();">
<div id="inventario_page" class="fsPage" style="width:100%">

  <h2 style="color:#FFF;margin-top:0px;">CERRAR ACTA ::: <?php echo $id_acta->idacta;?></h2>
<?php 
	if ($id_acta->id_status!=27){
?>
 <table width="100%" border="0" cellspacing="0" cellpadding="0"> 
  <tr >
    <td id="crear_ac" ><button type="button" class="btn btn-primary" id="procesar_cierre_acta" value="<?php echo $_REQUEST['id_acta']?>">Procesar Cierre</button></td>
    </tr>
</table>
<?php } ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover" style="font-size:9px">
  <thead>
    <tr>
      <td><strong>CONTRATO</strong></td>
      <td align="center"><strong>CLIENTE</strong></td>
      <td align="center"><strong>FECHA VENTA</strong></td>
      <td align="center"><strong>PRODUCTO</strong></td>
      <td align="center"><strong>PRECIO NETO</strong></td>
      <td align="center"><strong>PRECIO N.COBRADO</strong></td>
      <td align="center"><strong>UBICACION</strong></td>
      <td align="center"><strong>CUOTAS</strong></td>
      <td align="center"><strong>PAG.</strong></td>
      <td align="center"><strong>MORA</strong></td>
      <td align="center"><strong>MTO.MORA</strong></td>
      <td align="center"><strong>TELEFONO</strong></td>
      <td align="center"><strong>OFICIAL COBROS</strong></td>
      <td align="center"><strong>ASESOR</strong></td>
      <td align="center"><strong>GTE. VENTAS</strong></td>
      <td align="center"><strong>COMENTARIO</strong></td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      </tr>
  </thead>
  <tbody>
<?php  


SystemHtml::getInstance()->includeClass("cobros","Actas");    
$actas=new Actas($protect->getDBLink());
$actas->restart();
 
$desistidos=$actas->getActasCerradaOrPorCerrar($id_acta->tipo,$id_acta->idacta,$id_acta->id_status); 
$listado_c=array();
foreach($desistidos as $key =>$row){
 
	$data=array(
				"id_acta"=>$id_acta->idacta,
				"serie_contrato"=>$row['serie_contrato'],
				"no_contrato"=>$row['no_contrato'],
				"precio_neto"=>$row['precio_neto'],
				"monto_no_cobrado"=>$row['monto_no_cobrado'],
				"cuotas_pagas"=>$row['cuotas_pagas'],
				"cuotas_atraso"=>$row['CUOTAS_EN_ATRASO'],
				"monto_atraso"=>$row['MONTO_PENDIENTE_ATRASO']
			);
	array_push($listado_c,$data);		
	$id=System::getInstance()->Encrypt(json_encode($id_acta));
 
?>
    <tr  class="cC_list" id="<?php echo $id;  ?>">
      <td  ><?php echo $row['serie_contrato']." ".$row['no_contrato'];?></td>
      <td align="center"  ><?php echo $row['nombre_completo'];?></td>
      <td align="center"  ><?php echo $row['fecha_venta']; ?></td>
      <td align="center" ><?php echo $row['producto_total'];?></td>
      <td align="center"  ><?php echo number_format($row['precio_neto'],2);?></td>
      <td align="center"  ><?php echo number_format($row['monto_no_cobrado'],2);?></td>
      <td align="center" ><?php echo $row['PROPIEDAD'];?></td>
      <td align="center" ><?php echo $row['cuotas'];?></td>
      <td align="center"  ><?php echo $row['cuotas_pagas'];?></td>
      <td align="center"  ><?php echo $row['CUOTAS_EN_ATRASO'];?></td>
      <td align="center"  ><?php echo number_format($row['MONTO_PENDIENTE_ATRASO'],2);?></td>
      <td align="center"  ><?php echo $row['CITA'];?></td>
      <td align="center"  ><?php echo $row['OFICIAL'];?></td>
      <td align="center" ><?php echo $row['ASESOR'];?></td>
      <td align="center" ><?php echo $row['GERENTEV'];?></td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  ><?php if ($id_acta->id_status==27){?><img src="images/edit_user.png" class="commentary_add" width="24" height="24" style="cursor:pointer" /><?php } ?></td>
      <td align="center"  ><a href="?mod_cobros/delegate&contrato_view&id=<?php echo $id;?>" target="new"><img src="images/document_preview.png" width="24" height="24"></a></td>
      </tr>
<?php } 

STCSession::GI()->setSubmit("_info_acta",array("id_acta"=>$_acta,"listado_c"=>$listado_c));

?>
  </tbody>
  <tfoot>
  </tfoot>
</table>
 
</div>

<div id="content_dialog" ></div>