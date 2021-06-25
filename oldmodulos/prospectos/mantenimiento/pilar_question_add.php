 <?php
if (!isset($protect)){
	exit;
}	

$tipo_prospecto=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

//print_r($tipo_prospecto);

?>
<div class="modal fade" id="modal_pilar_question" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:630px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Estructura comercial</h4>
      </div>
      <div class="modal-body">
<form name="from_propectacion" id="from_propectacion" method="post" action="" class="fsForm  fsSingleColumn">

<table width="500" border="1" cellpadding="5"  style="border-spacing:8px;width:500px;">
  <tr>
    <td colspan="2" align="center"><strong><?php echo $tipo_prospecto->Descrip_tipoprospecto ?></strong></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><strong>Preguntas</strong></td>
  </tr>
<?php 
	$SQL="SELECT * FROM `detalle_tipos_pilar` WHERE `idtipo_pilar`='".$tipo_prospecto->idtipo_pilar."' and estatus='1' ";
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){
	  ?>
  <tr>
    <td align="right"><?php echo trim($row['pregunta_det_prospec'])?>:</td>
    <td>
    <?php if ($row['tipo_resp_det_prospec']=="valores"){
?>
	
    <select name="<?php echo System::getInstance()->Encrypt(json_encode(array("id"=>$row['id_pregunta'],"type"=>"valores"))); ?>" id="<?php echo System::getInstance()->Encrypt(json_encode(array("id"=>$row['id_pregunta'],"type"=>"valores"))); ?>" class="form-control required valid"  style="height:30px;" >
      <option value="">Seleccionar</option>
      <?php 
	  		for($i=1;$i<6;$i++){ 
				$field=$row['valor'.$i.'_det_prospec'];
				if ($field!=""){
	   ?> 
      	<option value="<?php echo System::getInstance()->Encrypt(json_encode(array("id"=>$row['id_pregunta'],"value"=>ucwords($field)))); ?>"><?php echo ucwords($field); ?></option>      
		<?php   } //FIN DEL FIELD ?>          
      	<?php } ?>
    </select>
<?php		
	}?>

<?php if ($row['tipo_resp_det_prospec']=="abierta"){ ?> 
	<input class="form-control required"  name="<?php echo System::getInstance()->Encrypt(json_encode(array("id"=>$row['id_pregunta'],"type"=>"abierta"))); ?>" id="<?php echo System::getInstance()->Encrypt(json_encode(array("id"=>$row['id_pregunta'],"type"=>"input"))); ?>" type="text" value="">
<?php  }  //FIN DE SELECCION DE VALOR?>    

<?php if ($row['tipo_resp_det_prospec']=="boolean"){ ?> 
	<input name="<?php echo System::getInstance()->Encrypt(json_encode(array("id"=>$row['id_pregunta'],"type"=>"boolean"))); ?>" type="checkbox" id="<?php echo System::getInstance()->Encrypt(json_encode(array("id"=>$row['id_pregunta'],"type"=>"boolean"))); ?>" value="1">
<?php  }  //FIN DE SELECCION DE VALOR?>        
    </td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_prospect_save"> Guardar</button>
      <button type="button" class="redButton" id="bt_prospect_cancel">Cancel</button></td>
  </tr>
</table>
</form>
      </div>
       
    </div>
  </div>
</div>