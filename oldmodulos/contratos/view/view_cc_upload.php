<?php

if (!isset($protect)){
	exit;
}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #FFF; font-weight: bold;">
  <tr>
    <td align="right">Tipo documento:</td>
    <td width="300"><select name="tipo_scan" id="tipo_scan" class="required form-control"  >
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `tipo_scan` WHERE id_status=1";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$id=System::getInstance()->Encrypt($row['idtipo_scan']);
?>
      <option value="<?php echo $id?>"><?php echo $row['tipo_documento']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right" valign="top">Empresa:</td>
    <td valign="top"><select name="doc_empresa" id="doc_empresa"  class="required form-control"  >
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM empresa WHERE estatus=1";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(trim($row['EM_ID']));
?>
      <option value="<?php echo $encriptID?>" ><?php echo $row['EM_NOMBRE']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right" valign="top">Descripcion:</td>
    <td valign="top"><textarea name="doc_descripcion" id="doc_descripcion" cols="45" rows="5" class="form-control"></textarea></td>
  </tr>
  <tr>
    <td colspan="2">Documento:</td>
  </tr>
  <tr>
    <td colspan="2">
		  
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
    <form id="upload" method="post" action="./?mod_contratos/listar&upload_doc&serie_contrato=<?php echo $_REQUEST['serie_contrato'];?>&no_contrato=<?php echo $_REQUEST['no_contrato'];?>" enctype="multipart/form-data">
    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td width="200">
			<div id="drop">
				Arrojar aqui 
				<a>Navegar</a>
				<input type="file" name="upl" multiple />
			</div>


		 </td>
        <td valign="top">
			<ul>
			</ul></td>
      </tr>
    </table>
    </form> </td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button"class="greenButton" id="bt_cargar_documento">Guardar</button>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
</table>
