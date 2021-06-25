<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['tipo_acta_cierre'])){
	include("view/tipo_acta_cierre.php");
	exit;
}

if (isset($_REQUEST['doViewActa'])){
	include("view/preview_acta.php");
	exit;
}
/*VISTA DE COMENTARIOS*/
if (isset($_REQUEST['doViewComentary'])){
	include("view/view_acta_comentary.php");
	exit;
}

if (isset($_REQUEST['tipo_acta'])){
	include("view/tipo_acta_selection.php");
	exit;
}


if (isset($_REQUEST['showViewActa'])){
	//include("view/preview_acta.php");
	$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['id_acta']));
	SystemHtml::getInstance()->includeClass("cobros","Actas");    
	$actas=new Actas($protect->getDBLink());	 
	$rt=$actas->downloadActa($acta->idacta);  
	exit;
}


/*Agrega un acta nueva*/
if (isset($_REQUEST['create_new_acta'])){
	$rt= array("valid"=>false,"mensaje"=>"No es posible realizar esta operacion");	
	if (validateField($_REQUEST,"periodo")){	 
	
		SystemHtml::getInstance()->includeClass("cobros/actas","Actas");    
		$actas=new Actas($protect->getDBLink());	 
		$actas->agregar_acta($_REQUEST['periodo']); 
		$rt['valid']=true;
		$rt['mensaje']="Datos agregados";
	}
	
	echo json_encode($rt);
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
	
	 
?> 
<script> 

var _actas= new cActas("content_dialog");
 
$(function(){  
	_actas.doInitListadoActa();
});

 
</script>
<br>
<div class="fsPage" style="width:990px;float:left">
  <h2 style="color:#FFF;margin-top:0px;">LISTADO DE ACTAS</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td><button type="button" class="btn btn-primary" id="crear_acta">crear ACTA</button></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover" style="font-size:12px">
      <thead>
        <tr>
          <td><strong> ACTA</strong></td>
          <td align="center"><strong>ESTATUS</strong></td>
          <td align="center"><strong>TOTAL CONTRATOS</strong></td>
          <td align="center"><strong>FECHA CREACION</strong></td>
          <td align="center"><strong>FECHA CIERRE</strong></td>
          <td align="center"><strong>GENERADO POR</strong></td>
          <td align="center"><strong>AGREGAR</strong></td>
          <td align="center"><strong>VER  </strong></td>
          <td align="center"><strong>CIERRE</strong></td>
          <td align="center">&nbsp;</td>
        </tr>
      </thead>
      <tbody>
        <?php  
SystemHtml::getInstance()->includeClass("cobros/actas","Actas");    
$actas=new Actas($protect->getDBLink());	 
$desistidos=$actas->getListadoActasGeneradas(); 
 
foreach($desistidos as $key =>$row){ 
	$id=System::getInstance()->Encrypt(json_encode($row));  
?>
        <tr class="cierre_acta" style="cursor:pointer" id="<?php echo $id?>">
          <td  ><?php echo $row['idacta']."-".$row['secuencia'];?></td>
          <td align="center"  ><?php echo $row['estatus']; ?></td>
          <td align="center" ><?php echo $row['total']; ?></td>
          <td align="center" ><?php echo $row['fecha_creacion'];?></td>
          <td align="center" ><?php echo $row['fecha_cierre'];?></td>
          <td align="center"  ><?php echo $row['operado_por'];?></td>
          <td align="center"  ><?php if ($row['id_status']!=27){?><a href="#" class="listado_acta_css" acta_id="<?php echo $id;?>"><img src="images/netvibes.png"   width="24" height="24" /></a><?php } ?></td>
          <td align="center"  ><a href="./?mod_cobros/delegate&acta&view_detalle=1&id=<?php echo $id;?>"><img src="images/document_preview.png" alt="" width="24" height="24" /></a></td>
          <td align="center"  ><?php if ($row['id_status']!=27){?>
            <a href="#" onclick="cerrar_acta('<?php echo $id;?>')"><img src="images/finish.png" alt="" width="24" height="24" /></a>
            <?php } ?></td>
          <td align="center"  > <a href="#" onclick="imprimir_acta('<?php echo $id;?>')"><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26"></a></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
      </tfoot>
    </table></td>
  </tr>
</table>
 
</div>

<div id="content_dialog" ></div>