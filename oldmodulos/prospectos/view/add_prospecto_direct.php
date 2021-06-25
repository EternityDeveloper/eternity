<?php
if (!isset($protect)){
	exit;
}	

?><div class="modal fade" id="modal_add_prospecto" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:530px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar prospecto</h4>
      </div>
      <div class="modal-body">
<form name="form_prospecto" id="form_prospecto" method="post" action=""  >

<table width="500" border="1" cellpadding="5"   style="border-spacing:8px;">
  <tr class="finder">
    <td align="right"><strong>Documento:</strong></td>
    <td><span class="finder">
      <select name="id_documento" id="id_documento"  style="width:160px;"  class="form-control required" >
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_documentos_identidad` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_documento'],$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
        <?php } ?>
      </select>
    </span></td>
  </tr>
  <tr style="display:none" id="client_prospecto">
    <td align="right"><strong>Prospecto:</strong></td>
    <td><table border="1"  id="prospect_validator">
      <tr>
        <td>&nbsp;</td>
        <td style="background-color:#CCCCCC;color:#C30;border-radius:2px;padding:2px;display:none" id="pros_rs_cliente">&nbsp;</td>
        <td class="finder" id="rs_find_cliente"><input type="text" class="form-control" name="indentification" id="indentification" placeholder="numero de identificación" autocomplete="off"  /></td>
        <td id="prosp_loading" style="display:none"><input type="image" src="images/loading.gif" width="20" height="20"/></td>
        <td  class="finder" id="prosp_find"><button type="button" class="greenButton" id="bt_find_person">Validar</button></td>
      </tr>
      </table></td>
  </tr>
  <tr>
    <td align="right"><strong> Asesor:</strong></td>
    <td><table border="1">
      <tr>
        <td style="background-color:#CCCCCC;color:#C30;border-radius:2px;padding:2px;display:none" id="pros_rs_asesor">&nbsp;</td>
        <td  id="codigo_asesor">&nbsp;</td>
        <td  id="prosp_find_asesor"><button type="button" class="greenButton" id="bt_find_asesor">Elegir</button></td>
      </tr>
    </table>
       <select name="asesor_list" id="asesor_list" style="display:none" >
      </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Fuente origen <br />
      (Pilar):</strong></td>
    <td><select name="pilar_origen" id="pilar_origen"  class="form-control"  style="height:30px;">
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `tipos_pilares` WHERE estatus IN (1,2)";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
      <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>" ><?php echo $row['dscrip_tipopilar'] ?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Fuente final <br />
      (Pilar):</strong></td>
    <td><select name="pilar_final" id="pilar_final"  class="form-control"  style="height:30px;">
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `tipos_pilares` WHERE estatus IN (1,2)";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
      <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>" ><?php echo $row['dscrip_tipopilar'] ?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Observacion:</strong></td>
    <td align="left"><textarea name="observacion" id="observacion" cols="30" rows="3" class="form-control"></textarea></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_pro_f_save"> Guardar</button>
      <button type="button" class="redButton" id="bt_pro_f_cancel">Cancel</button></td>
  </tr>
</table>
</form>
      </div>
       
    </div>
  </div>
</div>