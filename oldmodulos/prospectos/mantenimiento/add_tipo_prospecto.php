<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
if (isset($_REQUEST['submit_tipo_prospecto'])){
	
	$retur=array("mensaje"=>"Registro agregado","error"=>false);
 
	$obj = new ObjectSQL();
	$obj->idtipo_prospecto=$_REQUEST['idtipo_prospecto'];
	$obj->Descrip_tipoprospecto=$_REQUEST['Descrip_tipoprospecto'];
	$obj->Dias_proteccion=$_REQUEST['Dias_proteccion'];
	
	$SQL=$obj->getSQL("insert","tipos_prospectos");
	mysql_query($SQL);
	//print_r($obj);
	echo json_encode($retur);
	exit;	
}


 
?>
<form name="form_interfase" id="form_interfase" method="post" action="" class="fsForm  fsSingleColumn">
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td align="right"><strong>Codigo:</strong></td>
    <td><input name="idtipo_prospecto" type="text" class="textfield textfieldsize" id="idtipo_prospecto" value="<?php if (count($data)>0){echo $data->id_reserva;}?>" <?php if (count($data)>0){echo "disabled";}?> maxlength="5" ></td>
  </tr>
  <tr>
    <td align="right"><strong>Descripcion:</strong></td>
    <td>
      
      <input type="text" class="textfield textfieldsize"  name="Descrip_tipoprospecto" id="Descrip_tipoprospecto" value="<?php if (count($data)>0){echo $data->reserva_descrip;}?>"></td>
  </tr>
  <tr>
    <td align="right"><strong>Dias de proteccion:</strong></td>
    <td><input type="text" class="textfield textfieldsize"  name="Dias_proteccion" id="Dias_proteccion" value="<?php if (count($data)>0){echo $data->horas;}?>"></td>
  </tr>
<?php if ($_REQUEST['edit']=="1"){?>
<?php } ?>  
  <tr>
    <td colspan="2"><input name="submit_tipo_prospecto" type="hidden" id="submit_tipo_prospecto" value="1" />&nbsp;
      <input name="add_tipo_prospecto" type="hidden" id="add_tipo_prospecto" value="1"></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="greenButton" id="bt_save">
                        Guardar</button>
                       <button type="button" class="redButton" id="bt_cancel">
                        Cancel</button>  
        </td>
    </tr>
</table>
</form>