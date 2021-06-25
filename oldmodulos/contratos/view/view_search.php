<?php
if (!isset($protect)){
	exit;
}	

//$data=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
?>

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="fsPage" style="padding:0px;margin:0px;1">
    <tr>
      <td align="center"><table width="400" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100" align="center"><strong>
            <label class="fsLabel fsRequiredLabel" for="label">Tipo de documento<span>*</span></label>
          </strong></td>
          <td width="100"><strong>
            <label class="fsLabel fsRequiredLabel" for="label">Numero de documento<span>*</span></label>
          </strong></td>
        </tr>
        <tr>
          <td width="100" align="center"><input type="hidden" name="form_submit" id="form_submit" value="1" />
            <select name="id_documento" id="id_documento" class="required" >
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `sys_documentos_identidad` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_documento'],$protect->getSessionID());
?>
              <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
              <?php } ?>
            </select></td>
          <td width="100"><span class="fsLabel fsRequiredLabel">
            <input type="text" id="numero_documento" name="numero_documento" size="20" value="" class="required"  disabled/>
          </span></td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center"><button type="button" id="_buscar" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">BUSCAR</span></button> 
      
      <button type="button" id="_cancel" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">CANCELAR</span></button> </td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
  </table>
 