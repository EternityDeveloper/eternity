<?php 
if (!isset($protect)){
	exit;	
} 
 SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
 $oficial=Cobros::getInstance()->getOficial();
?>
<div class="modal fade" id="ModaReciboManual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Recibos Manual</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="table table-hover">
          <tr>
            <td><strong>Contrato</strong></td>
            <td><input type="text" name="txt_contrato" id="txt_contrato" class="form-control"  /></td>
          </tr>
          <tr>
            <td colspan="2" align="center" id="detalle_contrato_v" style="display:none;background-color:#F2F2F2">&nbsp;</td>
          </tr>
          <tr>
            <td><strong>Oficial</strong></td>
            <td><select name="oficial_n" id="oficial_n" class="form-control">
              <option value="">Seleccione</option>
              <?php foreach($oficial['oficial'] as $key =>$row){?>
              <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $key==$meta->nitoficial ?' selected ':''?>><?php echo $row?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Motorizado</strong></td>
            <td><select name="motorizado_n" id="motorizado_n" class="form-control">
              <option value="">Seleccione</option>
              <?php foreach($oficial['motorizado'] as $key =>$row){?>
              <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $key==$meta->nitmotorizado?' selected ':''?>><?php echo $row?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Moneda</strong></td>
            <td id="tipo_moneda_rm">&nbsp;</td>
          </tr>
          <tr>
            <td><strong>Compromiso</strong></td>
            <td id="compromiso_rm">&nbsp;</td>
          </tr>
          <tr>
            <td><strong>Plazo</strong></td>
            <td id="plazo_rm">&nbsp;</td>
          </tr>
          <tr>
            <td width="150"><strong>Gestión:   </strong></td>
            <td><select name="ID_GESTION" id="ID_GESTION" class="form-control" style="width:200px;">
              <option value="0" >Seleccionar</option>
<?php 
                    
			$SQL="SELECT * FROM `tipo_movimiento` where TIPO_MOV in ('CUOTA')";
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){
				$enct=System::getInstance()->Encrypt(json_encode($row));
 
            ?> 
              <option value="<?php echo $enct?>" selected ><?php echo $row['DESCRIPCION'];?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Fecha</strong></td>
            <td><input type="text" name="fecha_requerimiento_especial_xx" id="fecha_requerimiento_especial_xx" class="form-control" style="width:150px;" /></td>
          </tr>
          <tr>
            <td><strong>Monto  </strong></td>
            <td><input type="text" name="monto_a_abonar" id="monto_a_abonar" class="form-control" style="width:150px;"></td>
          </tr>
          <tr>
            <td><strong>Comentarios</strong></td>
            <td><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_saldos" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>