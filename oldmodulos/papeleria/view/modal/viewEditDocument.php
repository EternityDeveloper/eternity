<?php 
if (!isset($protect)){
	exit;	
}

if (!validateField($_REQUEST,"id")){
	exit;
}
$doc=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
if (!validateField($doc,"ID")){
	exit;
}   

$SQL="SELECT *, 
	(SELECT 
			CONCAT(OFI.`primer_nombre`,' ', OFI.`segundo_nombre`,' ', 
			OFI.`primer_apellido`,' ',
			OFI.segundo_apellido) AS nombre 
			FROM sys_personas AS OFI WHERE 
				OFI.id_nit=pape_formato_documentos.CREADO_POR) AS CREADO_POR 
   FROM `pape_formato_documentos` WHERE DOC_ESTATUS=1 and pape_formato_documentos.ID='".$doc->ID."'";
  
$rs=mysql_query($SQL); 
$doc=mysql_fetch_object($rs);	
 
		
STCSession::GI()->setSubmit("DOC_TO_PRINT",$doc);
 
?>
<div class="modal fade" id="view_modal_edit_document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DETALLE LOTE</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="110"><strong>NOMBRE:</strong></td>
                      <td><?php echo $doc->NOMBRE_DOC?></td>
                    </tr>
                    <tr class="table">
                      <td align="left"><strong>TIPO MONEDA:</strong></td>
                      <td><select name="tipo_moneda" id="tipo_moneda" class="form-control" style="width:300px;"> 
                        <option value="LOCAL" <?php echo $doc->TIPO_MONEDA=="LOCAL"?'selected="selected"':''?>>LOCAL</option>
                        <option value="DOLARES" <?php echo $doc->TIPO_MONEDA=="DOLARES"?'selected="selected"':''?>>DOLARES</option>
                      </select></td>
                    </tr>
                    <tr class="table">
                      <td align="left"><strong>APLICA PARA:</strong></td>
                      <td><select name="aplica_para" id="aplica_para" class="form-control" style="width:300px;"> 
                        <option value="PRODUCTO"  <?php echo $doc->APLICA_A=="PRODUCTO"?'selected="selected"':''?>>PRODUCTO</option>
                        <option value="SERVICIO"  <?php echo $doc->APLICA_A=="SERVICIO"?'selected="selected"':''?>>SERVICIO FUNERARIO</option>
                      </select></td>
                    </tr>
                    <tr class="table">
                      <td align="left">&nbsp;</td>
                      <td><button type="button" id="pap_print_doct" class="btn btn-primary">Imprimir</button></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td><textarea name="pap_input" id="pap_input" style="width:900px; height:200px"><?php echo $doc->TEXTO?></textarea></td>
                </tr>
                <tr>
                  <td align="right"><button type="button" id="pap_edit_document" class="btn btn-primary">GUARDAR</button></td>
                </tr>
                <tr>
                  <td align="left">&nbsp;</td>
                </tr>
              </table></td>
            </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
           
          </table>
      </div>
   
    </div>
  </div>
</div>