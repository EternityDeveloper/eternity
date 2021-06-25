<?php 
if (!isset($protect)){
	exit;	
} 
SystemHtml::getInstance()->includeClass("caja","Caja");  
$caja= new Caja($protect->getDBLINK());
$caja->session_restart();
?>
<div class="modal fade" id="modal_pago_cuota" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:830px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="title_template">PAGO DE CUOTA</h4>
      </div>
      <div class="modal-body">
 <?php
 
 if (!$protect->getIfAccessPageById(180)){ 
	echo "No tiene permiso para realizar esta operacion!";
	exit;
  }
 
 
 ?>    
      <form method="post"  action="" id="caja_payment"  name="caja_payment" class="fsForm"> 
        <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td id="detalle_recibo"  style="display:none"  ><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr style="display:none" id="pf_fdocumento">
                <td><strong> DOCUMENTO</strong></td>
                <td><label for="movimiento"></label>
                  <select name="tipo_movimiento" id="tipo_movimiento" class="form-control required"  style="width:350px;">
                    <option value="">Seleccione</option>
                    <?php 

$SQL="SELECT * FROM `tipo_movimiento` where TIPO_MOV in ('RES','INI') ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                    <option value="<?php echo $encriptID?>"><?php echo $row['DESCRIPCION']?></option>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr >
                <td width="150"><strong>RECIBO VENTA</strong></td>
                <td><input type="text" name="recibo_venta" id="recibo_venta" class="form-control" style="width:150px;" /></td>
              </tr>
              <tr style="display:none">
                <td><strong>REPORTE VENTA</strong></td>
                <td><input type="text" name="reporte_venta" id="reporte_venta" class="form-control" style="width:150px;" /></td>
              </tr>
              <tr>
                <td><strong>ASIGNADO A:</strong></td>
                <td><input type="text" name="txt_asesor" id="txt_asesor" class="form-control" style="width:350px;" /></td>
              </tr>
            </table></td>
          </tr>  
          <tr>
            <td >
            <table id="banco_credito_view" width="500" border="0" cellspacing="0" cellpadding="0" style="display:none">
              <tr  >
                <td width="80" align="left"><strong>BANCO:</strong></td>
                <td width="351"><select name="banco_credito" id="banco_credito" class="form-control required" style="width:200px;" >
                  <option value="">Seleccione</option>
                  <?php 

$SQL="SELECT * FROM `bancos`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                  <option value="<?php echo $encriptID?>" ><?php echo $row['ban_descripcion']?></option>
                  <?php } ?>
                </select></td>
              </tr>
            </table>
            <?php if ($protect->getIfAccessPageById(179)){ ?>
            <table width="500" border="0" cellspacing="0" cellpadding="0">
                <tr id="fecha_atraso">
                  <td width="80"><strong>FECHA</strong></td>
                  <td width="351"><input type="text" name="fecha_requerimiento_especial_xx" id="fecha_requerimiento_especial_xx" class="form-control" style="width:150px;" readonly="readonly" /></td>
                </tr>
              </table>
             <?php } ?> 
              </td>
          </tr>
          <tr>
            <td id="detalle_general" style="display:none">&nbsp;</td>
          </tr>   
          <tr>
            <td id="detalle_nota_credito" style="display:none">&nbsp;</td>
          </tr>                   
          <tr>
            <td align="center" id="forma_pago_view">&nbsp;</td>
          </tr>

          <tr>
            <td><h2>OBSERVACION</h2></td>
          </tr>
          <tr>
            <td align="center"><textarea name="observacion" id="observacion" cols="45" rows="3" style="width:95%" class="textfield_input"></textarea></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><button type="button" class="greenButton" id="bt_caja_process">&nbsp;Procesar</button>
              <button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button>
              &nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
        </form>
      </div>
       
    </div>
  </div>
</div>