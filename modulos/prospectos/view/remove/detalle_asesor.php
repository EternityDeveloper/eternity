<?php
if (!isset($protect)){
	exit;
}


if (!isset($_REQUEST['id'])){
	exit;
}

$asesor=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

?>
<div class="fsPage">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
        <form name="form_prospecto" id="form_prospecto" method="post" action="" class="fsForm  fsSingleColumn">
          <table width="100%" border="1" cellpadding="5"  style="border-spacing:10px;">
            <tr >
              <td><h2>ASESOR</h2></td>
            </tr>
            <tr>
              <td style="font-size:16px;"><center><strong><?php echo $asesor->nombre_completo?></strong></center></td>
            </tr>
            <tr>
              <td align=""><h2>Comentario:&nbsp;</h2></td>
            </tr>
            <tr>
              <td align="center"><textarea name="asesor_comentario" id="asesor_comentario" cols="45" rows="5"></textarea></td>
            </tr>

            
          </table>
        </form>
      </td>
    </tr>
     
    <tr>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" valign="top"><button type="button" class="redButton" id="bt_remove_asesor">Eliminar</button>
        &nbsp;&nbsp;
        <button type="button" class="greenButton" id="bt_asesor_f_cancel">Cancelar</button>&nbsp;</td>
      </tr>
    <tr>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
  </table>

</div>