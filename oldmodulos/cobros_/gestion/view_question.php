<?php 
if (!isset($protect)){
	exit;	
} 
 

?>
<div class="modal fade" id="doViewQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:440px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Documento de intentificación</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="15" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="160" align="center"><strong>
                  <label class="fsLabel fsRequiredLabel" for="label">Tipo de documento<span>*</span></label>
                  </strong></td>
                <td><strong>
                  <label class="fsLabel fsRequiredLabel" for="label">Numero de documento<span>*</span></label>
                  </strong></td>
              </tr>
              <tr>
                <td width="160" align="center"> 
                  <select name="id_documento" id="id_documento" class="form-control required" style="width:160px;" >
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
                <td width="280"><span class="fsLabel fsRequiredLabel">
                  <input type="text" id="numero_documento_question" name="numero_documento_question" size="20" value="" class="form-control required form-control"  disabled/>
                </span></td>
              </tr>
            </table></td>
          </tr>
 
          <tr>
            <td height="15" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td height="15" align="center"><button type="button" id="consultar_doc" class="btn btn-primary" disabled>Consultar</button></td>
          </tr>
         
        </table>
<br>
      </div>
  
    </div>
  </div>
</div>
 