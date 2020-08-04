<?php 
if (!isset($protect)){
	exit;	
}
$ud= UserAccess::getInstance()->getUserData();
$role_id=$ud['id_role']; 
?>
<div class="modal fade" id="view_modal_label_contrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">IMPRIMIR LABEL</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="detalle_contrato_asignacion"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="142"><strong>IMPRESORA:</strong></td>
                      <td width="458"><select name="printer_list" id="printer_list" class="form-control">
                       <option>Seleccione</option>
                      <?php 
					  $SQL="SELECT  
							sp.*,
							sgp.id AS group_printer_id,
							sgp.nombre AS main
						 FROM `system_printers` AS sp
						INNER JOIN `system_printer_rel_group` AS sg ON (sg.`system_printers_id`=sp.id)
						INNER JOIN `system_group_printers` AS sgp ON (sgp.`id`=sg.`system_group_printers_id`)
						WHERE sg.`role_id`='".$role_id."' and sp.categoria='LABEL_DOCUMENTACION'";
						$rs=mysql_query($SQL);
						$num=mysql_num_rows($rs);
						while($row=mysql_fetch_array($rs)){
							$enid=System::getInstance()->Encrypt(json_encode(array("id"=>$row['id'],"nombre"=>$row['nombre'])));
					  ?>
                        <option id="<?php echo $enid;?>" <?php if ($num==1){?>selected<?php } ?>><?php echo $row['nombre']." (".$row['main'].")"?></option>
                       <?php } ?> 
                      </select></td>
                    </tr>
                  </table></td>
                </tr> 
                <tr >
                  <td align="center">&nbsp;</td>
                </tr>
                <tr  >
                  <td align="center"><button type="button" id="doImprimir" class="btn btn-blue">IMPRIMIR</button></td>
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