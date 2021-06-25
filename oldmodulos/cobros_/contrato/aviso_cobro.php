<?php
if (!isset($protect)){
	exit;
}
if (!isset($_REQUEST['contrato'])){
	exit;
} 
?>  
<table width="500" border="0" cellspacing="0" cellpadding="0" class="fsPage fsDivPage">
  <tr>
    <td>
    <form name="frm_actividad_" id="frm_actividad_" method="post" action="">
<table width="500" border="1" cellpadding="5"  style="border-spacing:8px;">
      <tr >
        <td align="right"><strong>Contacto:</strong></td>
        <td><input  name="lb_contacto" type="text" class="required" style="width:200px;padding-right:10px;" id="lb_contacto" />
          <input name="contrato" type="hidden" id="contrato" value="<?php echo $_REQUEST['contrato'];?>" />
          <input name="isTipoGestion" type="hidden" id="isTipoGestion" value="N" /></td>
      </tr>
      <tr >
        <td align="right" valign="top"><strong>Comentario cliente:</strong></td>
        <td><textarea name="lb_comentario_cliente" class="" id="lb_comentario_cliente" style="height:50px;"></textarea></td>
      </tr>
      <tr >
        <td align="right" ><strong>Proximo contacto:</strong></td>
        <td><p>
          <input  name="lb_fecha_contacto" type="text" class="required" style="cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="lb_fecha_contacto" readonly />
        </p></td>
      </tr>
      <tr >
        <td align="right" ><strong>Hora contacto:</strong></td>
        <td><input  name="lb_hora" type="text" class="required" style="cursor:pointer;width:100px;padding-right:10px;" id="lb_hora" /></td>
      </tr>
      <tr >
        <td align="right" >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
 

    </table>
    </form></td>
  </tr>
      <tr >
        <td colspan="2" valign="top">
		
		<?php include("view/actividad_n.php");?>
        
        
        </td>
      </tr>
  <tr>
        <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="act_add">Crear</button>
      <button type="button" class="redButton" id="act_cancel"> Cancel</button></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
</table>
 
