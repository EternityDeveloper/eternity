<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
 
if (!isset($_REQUEST['question'])){
	echo json_encode(array("error"=>"Error parametro invalido o falta!","error"=>true));
	exit;
}
 
$data=json_decode(System::getInstance()->Decrypt($_REQUEST['question']));
 
if (!isset($data->id_pregunta)){
	echo json_encode(array("error"=>"error de codigo","error"=>true));
	exit;
}

if (isset($_REQUEST['submit_tipo_prospecto'])){
	/* CARGO LA LIBRERIA DE PILAR */
	SystemHtml::getInstance()->includeClass("prospectos","Pilar");
	
	$pilar= new Pilar($protect->getDBLink());
	$result=$pilar->editPilarQuestion($data,$_REQUEST);
	echo json_encode($result);
 
	exit;	
}

 
?>
<form name="form_interfase_question" id="form_interfase_question" method="post" action="" class="fsForm  fsSingleColumn">
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td align="right"><strong>ID Pregunta:</strong></td>
    <td><input name="id_pregunta" type="text" class="textfield textfieldsize" id="id_pregunta" value="<?php echo $data->id_pregunta;?>" disabled ></td>
  </tr>
  <tr>
    <td align="right" valign="top"><strong>Pregunta:</strong></td>
    <td><textarea name="pregunta_det_prospec" class="textfield textfieldsize" id="pregunta_det_prospec" style="height:50px;"><?php echo $data->pregunta_det_prospec;?></textarea></td>
  </tr>
  <tr>
    <td align="right"><strong>Tipo de respuesta:</strong></td>
    <td>
      <select name="tipo_resp_det_prospec" id="tipo_resp_det_prospec">
        <option value="">Seleccionar</option>
         <option value="boolean" <?php echo $data->tipo_resp_det_prospec=="boolean"?'selected':''?>>Verdadero/Falso</option>
         <option value="abierta" <?php echo $data->tipo_resp_det_prospec=="abierta"?'selected':''?>>Abierta</option>	
         <option value="valores" <?php echo $data->tipo_resp_det_prospec=="valores"?'selected':''?> >Valores</option>	
      </select></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center" id="tipo_respuesta_dt" <?php echo $data->tipo_resp_det_prospec=="valores"?'':'style="display:none"'?> ><table width="300" border="0" align="center" style="border-spacing:5px">
      <tr>
        <td align="right"><strong>Valor 1:</strong></td>
        <td><input name="valor1" type="text" class="textfield textfieldsize" id="valor1" value="<?php echo $data->valor1_det_prospec;?>" ></td>
        </tr>
      <tr>
        <td align="right"><strong>Valor 2:</strong></td>
        <td><input name="valor2" type="text" class="textfield textfieldsize" id="valor2" value="<?php echo $data->valor2_det_prospec;?>" ></td>
        </tr>
      <tr>
        <td align="right"><strong>Valor 3:</strong></td>
        <td><input name="valor3" type="text" class="textfield textfieldsize" id="valor3" value="<?php echo $data->valor3_det_prospec;?>" ></td>
        </tr>
      <tr>
        <td align="right"><strong>Valor 4:</strong></td>
        <td><input name="valor4" type="text" class="textfield textfieldsize" id="valor4" value="<?php echo $data->valor4_det_prospec;?>" ></td>
        </tr>
      <tr>
        <td align="right"><strong>Valor 5:</strong></td>
        <td><input name="valor5" type="text" class="textfield textfieldsize" id="valor5" value="<?php echo $data->valor5_det_prospec;?>" ></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="right"><strong>Estatus:</strong></td>
    <td><select name="estatus" id="estatus">
      <option value="0">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `sys_status` WHERE id_status IN (1,2)";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
      <option value="<?php echo $row['id_status']?>" <?php echo $row['id_status']==$data->estatus?'selected':'' ?>><?php echo $row['descripcion'] ?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_tipo_prospecto" type="hidden" id="submit_tipo_prospecto" value="1" />&nbsp;
      <input name="edit_pregunta" type="hidden" id="edit_pregunta" value="1">
      <input name="question" type="hidden" id="question" value="<?php echo $_REQUEST['question'];?>"></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="greenButton" id="bt_question_save">
                        Guardar</button>
                       <button type="button" class="redButton" id="bt_question_cancel">
                        Cancel</button>  
        </td>
    </tr>
</table>
</form>