<?php 
if (!isset($protect)){
	exit;	
} 
 
?>
<div class="modal fade" id="modal_devolucion_cuota" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:830px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="title_template">REGISTRO DE CHEQUE</h4>
      </div>
      <div class="modal-body">
 <?php
 /*
 if (!$protect->getIfAccessPageById(180)){ 
	echo "No tiene permiso para realizar esta operacion!";
	exit;
  } */
 ?>    
 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">   
          <tr>
            <td  ><button class="btn btn-sm btn-primary pull-left m-t-n-xs" type="button" id="_find_contrato"><strong>Agregar contrato</strong></button>
              &nbsp;</td>
          </tr>
          <tr>
            <td >&nbsp;</td>
          </tr>
          <tr>
            <td id="items_cheque2"><table width="338" border="0" cellspacing="0" cellpadding="0">
              <tr  >
                <td align="right">Banco debito:</td>
                <td><select name="banco_debito" id="banco_debito" class="form-control required" style="width:200px;" >
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
              <tr>
                <td>FECHA REGISTRO</td>
                <td><input type="text" name="fecha_cheque" id="fecha_cheque" class="form-control" style="width:200px;" value="<?php echo date("Y-m-d")?>"></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td id="items_cheque">&nbsp;</td>
          </tr>        
          <tr>
            <td><h2>OBSERVACION</h2></td>
          </tr>
          <tr>
            <td align="center"><textarea name="observacion" id="observacion" cols="45" rows="3" style="width:98%" class="textfield_input"></textarea></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><button type="button" class="greenButton" id="bt_registro_cheque">&nbsp;Procesar</button>
              <button type="button" class="redButton" id="bt_chk_cancel">Cancelar</button>
              &nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
  
      </div>
       
    </div>
  </div>
</div>