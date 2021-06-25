<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (!isset($_REQUEST['id'])){
	echo json_encode(array("error"=>"Error parametro invalido o falta!","error"=>true));
	exit;
}

$data=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (!isset($data->idtipo_pilar)){
	echo json_encode(array("error"=>"error de codigo","error"=>true));
	exit;
}

if (isset($_REQUEST['submit_tipo_prospecto'])){
	/* CARGO LA LIBRERIA DE PILAR */
	SystemHtml::getInstance()->includeClass("prospectos","Pilar");
	
	$pilar= new Pilar($protect->getDBLink());
	$result=$pilar->addPilarQuestion($data,$_REQUEST);
	echo json_encode($result);
	exit;	
}

 
?>
<form name="form_interfase_question" id="form_interfase_question" method="post" action="" class="fsForm  fsSingleColumn">
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td align="right" valign="top"><strong>Pregunta:</strong></td>
    <td><textarea name="pregunta_det_prospec" class="textfield textfieldsize" id="pregunta_det_prospec" style="height:50px;"></textarea></td>
  </tr>
  <tr>
    <td align="right"><strong>Tipo de respuesta:</strong></td>
    <td>
      <select name="tipo_resp_det_prospec" id="tipo_resp_det_prospec">
        <option value="">Seleccionar</option>
         <option value="boolean">Verdadero/Falso</option>
         <option value="abierta">Abierta</option>	
         <option value="valores">Valores</option>	
      </select></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center" id="tipo_respuesta_dt" style="display:none"><table width="300" border="0" align="center" style="border-spacing:5px">
      <tr>
        <td align="right"><strong>Valor 1:</strong></td>
        <td><input name="valor1" type="text" class="textfield textfieldsize" id="valor1" value="" ></td>
      </tr>
      <tr>
        <td align="right"><strong>Valor 2:</strong></td>
        <td><input name="valor2" type="text" class="textfield textfieldsize" id="idtipo_prospecto3" value="" ></td>
      </tr>
      <tr>
        <td align="right"><strong>Valor 3:</strong></td>
        <td><input name="valor3" type="text" class="textfield textfieldsize" id="idtipo_prospecto4" value="" ></td>
      </tr>
      <tr>
        <td align="right"><strong>Valor 4:</strong></td>
        <td><input name="valor4" type="text" class="textfield textfieldsize" id="idtipo_prospecto5" value="" ></td>
      </tr>
      <tr>
        <td align="right"><strong>Valor 5:</strong></td>
        <td><input name="valor5" type="text" class="textfield textfieldsize" id="idtipo_prospecto6" value="" ></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_tipo_prospecto" type="hidden" id="submit_tipo_prospecto" value="1" />&nbsp;
      <input name="add_pregunta" type="hidden" id="add_pregunta" value="1">
      <input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id'];?>"></td>
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