<?php
if (!isset($protect)){
	exit;
}
SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	
$actividad=json_decode(System::getInstance()->Decrypt($_REQUEST['id_actividad']));

if (isset($actividad->idtipo_prospecto)){
	
}
$detalle_pros=$prospecto->getActividad($actividad->id_actividad);
 

?>
<form name="frm_new_actividad" id="frm_new_actividad" method="post" action="">
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td colspan="2" align="center"><h2 id="h_">QUE LOGRO CON LA <?php echo $detalle_pros['actividad'];?>?</h2> </td>
    </tr>
  <tr>
    <td align="right" ><strong>Estatus:</strong></td>
    <td><select name="estatus" id="estatus">
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `sys_status` WHERE id_status IN (6,7,8) ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
      <option value="<?php echo $row['id_status']?>"><?php echo $row['descripcion'] ?></option>
      <?php } ?>
    </select></td>
  </tr>
  <?php if ($_REQUEST['edit']=="1"){?>
  <?php } ?>
  <tr>
    <td colspan="2" id="activity_question">&nbsp;</td>
  </tr>
  <tr class="activity_question_bt_view" style="display:none">
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_question_actividad_save"> Guardar</button>
      <button type="button" class="redButton" id="bt_question_actividad_cancel"> Cancel</button></td>
  </tr>
</table>

</form>
