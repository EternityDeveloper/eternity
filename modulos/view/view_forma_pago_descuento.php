<?php

$moneda="LOCAL";
if (validateField($_REQUEST,"moneda")){
	$moneda=$_REQUEST['moneda'];
}
?>
<style>
.fsPage{
 margin-bottom:10px;	
}
</style>
<div class="modal fade" id="modal_forma_pago_descuento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:430px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DESCUENTOS</h4>
      </div>
      <div class="modal-body">
<div id="contrato_div"  >
  <table width="100%" border="0" cellspacing="0" cellpadding="0" >
 
    <tr>
      <td><table width="100%" cellpadding="5">
        <tr>
          <td align="right"><strong>Tipo descuento:&nbsp;</strong></td>
          <td><select name="tipo_descuento" id="tipo_descuento" class="textfield textfieldsize required" style="height:30px;">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM descuentos WHERE moneda='". mysql_real_escape_string($moneda) ."'";
//echo $SQL;
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encript=System::getInstance()->Encrypt(json_encode($row));
	unset($row['estatus']);	
?>
            <option value="<?php echo $encript;?>" alt='<?php  echo json_encode($row)?>' ><?php echo $row['descripcion']?></option>
            <?php } ?>
            </select></td>
        </tr>
<?php
if ($_REQUEST['type']=='MONTO'){ 
?> 
        <tr  class="cl_monto" style="display:none" >
          <td align="right"><strong>Monto:</strong></td>
          <td height="40"><span class="finder">
            <input name="monto" type="text" class="textfield textfieldsize" id="monto"  autocomplete="off" maxlength="5" style="width:60px;height:30px;" />
          </span></td>
        </tr>
<?php } ?>           
<?php
if ($_REQUEST['type']=='PORCIENTO'){ 
?>       
        <tr class="cl_porcentaje" style="display:none" >
          <td align="right"><strong>Porcentaje:</strong></td>
          <td><span class="finder">
            <input name="porcentaje" type="text" class="textfield textfieldsize" id="porcentaje"  autocomplete="off" maxlength="5" style="width:60px;"  />
          %</span></td>
        </tr>
<?php } ?>        
        <tr style="display:none" id="autorizacion_tr">
          <td align="right"><strong>Autorizado por:</strong></td>
          <td><input type="text" name="autorizado_por" id="autorizado_por" class="textfield textfieldsize" style="height:35px;" /> 
            <input type="hidden" name="autorizado_por_id" id="autorizado_por_id" value="" />
         </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center"><button type="button" class="greenButton" id="desc_apply" disabled>Aplicar</button>&nbsp;&nbsp;<button type="button" class="redButton" id="cancel_decuento">Cerrar</button></td>
          </tr>
        <tr>
          <td colspan="2" align="center">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
  </table>

</div>
      </div>
 
    </div>
  </div>
</div>