<?php 
if (!isset($protect)){
	exit;	
}
 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">ASIGNAR TARJETA DE CREDITO</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
       
            <tr>
              <td><STRONG>NUMERO DE TARJETA</strong></td>
            </tr>
            <tr>
              <td><label for="numero_tarjeta"></label>
              <input type="text" name="numero_tarjeta" id="numero_tarjeta" class="form-control"></td>
            </tr>
            <tr>
              <td id="tipo_tarjeta">&nbsp;</td>
            </tr>
            <tr>
              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><strong>FECHA VENC.</strong></td>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><strong>Dia</strong></td>
                        <td><strong>Año</strong></td>
                      </tr>
                      <tr>
                        <td><select name="month_venc" id="month_venc" class="form-control">
                            <?php for($i=1;$i<=12;$i++){?>
                            <option value="<?php echo $i;?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT);?></option>
                            <?php } ?>
                        </select></td>
                        <td><select name="year_venc" id="year_venc" class="form-control">
                          <?php for($i=1990;$i<=date('Y')+10;$i++){?>
                          <option value="<?php echo $i;?>"><?php echo str_pad($i,4,"0",STR_PAD_LEFT);?></option>
                          <?php } ?>
                        </select></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td><strong>CVV:</strong> </td>
                  <td><input type="text" name="cvv_metodo" id="cvv_metodo" class="form-control" style="width:100px;"></td>
                </tr>
                <tr>
                  <td valign="top"><strong>DIA DE  DEBITO:</strong></td>
                  <td><select name="dias_debito" id="dias_debito" class="form-control">
                    <?php for($i=1;$i<=28;$i++){?>
                    <option value="<?php echo $i;?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT);?></option>
                    <?php } ?>
                  </select></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td><strong>COMENTARIO:</strong></td>
            </tr>
            <tr>
              <td><textarea name="comentario" id="comentario" class="form-control" cols="45" rows="5"></textarea></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="guardar_cambio" class="btn btn-primary">Proceder</button>
      </div>
    </div>
  </div>
</div>