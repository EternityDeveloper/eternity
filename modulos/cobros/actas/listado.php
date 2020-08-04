<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
 
 
if (isset($_REQUEST['putOnActaList'])){
	$rt= array("valid"=>false,"mensaje"=>"No es posible realizar esta operacion");	
 
	if (validateField($_REQUEST,"acta") && validateField($_REQUEST,"accion")
		&& validateField($_REQUEST,"items")  ){	
		SystemHtml::getInstance()->includeClass("cobros/actas","Actas");  
		$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['acta']));
		$accion=$_REQUEST['accion'];
		$items=json_decode(System::getInstance()->Decrypt($_REQUEST['items'])); 
		$actas=new Actas($protect->getDBLink());	
	 
		switch($accion){
			case "add":
				$rt=$actas->agregar_pull_acta($acta,$items);
			break;
			case "remove":
				$rt=$actas->remove_pull_acta($acta,$items);
			break;
		}  
	}
	
	echo json_encode($rt);
	exit;
}
/*Agrega los contratos al acta antes de ser cerrado*/ 
if (isset($_REQUEST['addActa'])){
	$rt= array("valid"=>false,"mensaje"=>"No es posible realizar esta operacion");	
	if (validateField($_REQUEST,"addActa") && validateField($_REQUEST,"acta") 
	&& validateField($_REQUEST,"tipo")){	   
		SystemHtml::getInstance()->includeClass("cobros/actas","Actas");    
		$actas=new Actas($protect->getDBLink());	 
		$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['acta']));  
		if (isset($acta->id)){
			$rt=$actas->agregar_contratos_al_acta($_REQUEST['tipo'],$acta);  
		}
	}
	
	echo json_encode($rt);
	exit;
}

/* remueve del acta antes de ser cerrada*/
if (isset($_REQUEST['remover_del_acta'])){
	$rt= array("valid"=>false,"mensaje"=>"No es posible realizar esta operacion");	
	if (validateField($_REQUEST,"acta") && validateField($_REQUEST,"id")){	   
		SystemHtml::getInstance()->includeClass("cobros/actas","Actas");    
		$actas=new Actas($protect->getDBLink());	 
		$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['acta']));  
		$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
		  
		if (isset($acta->id)){
			$rt=$actas->remover_contratos_al_acta($acta,$ct,$_REQUEST['comentarios']);  
		}
	}
	
	echo json_encode($rt);
	exit;
}

/* CIERRA EL ACTA */
if (isset($_REQUEST['procesar_cierre_acta'])){
	$rt= array("valid"=>false,"mensaje"=>"No es posible realizar esta operacion");	
	if (validateField($_REQUEST,"acta")){	   
		SystemHtml::getInstance()->includeClass("cobros/actas","Actas");    
		$actas=new Actas($protect->getDBLink());	 
		$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['acta']));  
		if (isset($acta->id)){
			$rt=$actas->doCerrarActa($acta,$_REQUEST['comentarios']);  
		}
	}
	
	echo json_encode($rt);
	exit;
}






if (isset($_REQUEST['tipo_acta'])){
	include("view/tipo_periodo.php");
	exit;
}
/*vista de cerrar el acta*/
if (isset($_REQUEST['doViewCloseActa'])){
	include("view/close_acta.php");
	exit;
}

/*vista de remover acta*/
if (isset($_REQUEST['remover_from_acta'])){
	include("view/question_remove.php");
	exit;
}

if (isset($_REQUEST['view_detalle'])){
	include("view_listado_acta.php");
	exit;
}

if ((!isset($_REQUEST['id'])) || (!isset($_REQUEST['tipo']))){
	echo "No existe el acta!";
	exit;	
}



SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");   
SystemHtml::getInstance()->addTagScript("script/Class.js"); 
      
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
SystemHtml::getInstance()->addTagStyle("css/select2.css");
SystemHtml::getInstance()->addTagScriptByModule("class.cActas.js","cobros/actas"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");

SystemHtml::getInstance()->includeClass("cobros/actas","Actas");    
$actas=new Actas($protect->getDBLink());
$actas->restart();

$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['id'])); 	
$tipo_acta=$_REQUEST['tipo'];   	

$_tipo=24; //Posible a desistir	
if ($tipo_acta=="A"){
	$_tipo=28; //Posible a anular	
} 
$listado=$actas->getActasAnuladoOrDesistidos($_tipo);  
?> 
<script> 

var _actas= new cActas("content_dialog");
 
$(function(){ 
	_actas.doInitListdoContratos('<?php echo $tipo_acta;?>','<?php echo $_REQUEST['id'];?>');
});
 
</script>
<div id="inventario_page" class="fsPage" style="width:98%">
  <h2 style="color:#FFF;margin-top:0px;">ACTA <?php echo $tipo_acta."-".$acta->idacta;?></h2>
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr >
    <td id="crear_ac" style="display:none"><button type="button" class="btn btn-primary" id="agregar_acta">Agregar al  ACTA</button></td>
    </tr>
  <tr >
    <td ><strong>TOTAL CONTRATOS:</strong>&nbsp;<?php echo count($listado);?></td>
  </tr>
 </table>

<table width="100%" id="listado_de_contratos" border="0" cellspacing="0" cellpadding="0" class="table table-hover" style="font-size:9px">
  <thead>
    <tr style="background:#CCC">
      <td><input type="checkbox" name="select_all_ct" id="select_all_ct"></td>
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
      <td align="center"><strong>OFICIAL COBROS</strong></td>
      <td align="center"><strong>ASESOR</strong></td>
      <td align="center"><strong>GTE. VENTAS</strong></td>
      <td align="center">&nbsp;</td>
      </tr>
  </thead>
  <tbody>
<?php   

 
foreach($listado as $key =>$row){ 
	$data=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato'],"id_nit"=>$row['id_nit']);
	$id=System::getInstance()->Encrypt(json_encode($data));
 
?>
    <tr  >
      <td  ><input type="checkbox" name="checkbox" id="checkbox" class="cC_list" value="<?php echo $id;  ?>"></td>
      <td  ><a href="?mod_cobros/delegate&contrato_view&id=<?php echo $id;?>" target="new"><?php echo $row['serie_contrato']." ".$row['no_contrato'];?></a></td>
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
      <td align="center"  ><?php echo $row['OFICIAL'];?></td>
      <td align="center" ><?php echo $row['ASESOR'];?></td>
      <td align="center" ><?php echo $row['GERENTEV'];?></td>
      <td align="center"  ><a href="?mod_cobros/delegate&contrato_view&id=<?php echo $id;?>" target="new"><img src="images/document_preview.png" width="24" height="24"></a></td>
      </tr>
    <?php } ?>
  </tbody>
  <tfoot>
  </tfoot>
</table>
 
</div>

<div id="content_dialog" ></div>
