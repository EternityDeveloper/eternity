<?php 
if (!isset($protect)){
	exit;	
} 

if (!isset($_REQUEST['nit'])){
	exit;
}


$nit=json_decode(System::getInstance()->Decrypt($_REQUEST['nit']));
$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
 
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$oficial=Cobros::getInstance()->getOficial();
$con=new Contratos($protect->getDBLink());  
$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato); 
 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Gestión</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td width="150"><strong>Documento:   </strong></td>
            <td><select name="tipo_movimiento" id="tipo_movimiento" class="form-control required"  style="width:200px;">
                    <option value="">Seleccione</option>
                    <?php 

$SQL="SELECT * FROM `tipo_documento` WHERE TIPO_DOC IN ('RBCV','RM')";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                    <option value="<?php echo $encriptID?>"><?php echo $row['DOCUMENTO']?></option>
                    <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>No. Documento:</strong></td>
            <td><input type="text" name="no_documento" id="no_documento"  class="textfield_input required"/></td>
          </tr>
          <tr>
            <td><strong>Gestión:   </strong></td>
            <td><select name="ID_GESTION" id="ID_GESTION" class="form-control" style="width:200px;">
              <option value="0" selected="selected">Seleccionar</option>
              <?php 
                    
			$SQL="SELECT * FROM `tipo_movimiento` where (TIPO_MOV in ('NC','ND','MANTE','CUOTA','REACTV','REACTV','SER-FUN','SER-INH','CT','MANT','CTLD','PCREST','SA') or tipo_movimiento.FACTURA_CLIENTE=1) ";
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){
				$enct=System::getInstance()->Encrypt(json_encode($row));
 
            ?>
              <option value="<?php echo $enct?>" ><?php echo $row['DESCRIPCION'];?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr class="table table-hover">
            <td><strong>Motorizado</strong></td>
            <td><select name="motorizado_n" id="motorizado_n" class="form-control">
              <option value="">Seleccione</option>
              <?php foreach($oficial['motorizado'] as $key =>$row){?>
              <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $key==$meta->nitmotorizado?' selected ':''?>><?php echo $row?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Fecha:</strong></td>
            <td><input type="text" name="fecha_requerimiento_especial_xx" id="fecha_requerimiento_especial_xx" class="form-control" style="width:150px;" /></td>
          </tr>
          <tr>
            <td><strong>Monto en <?php echo $cdata->tipo_moneda?> </strong></td>
            <td><input type="text" name="monto_a_abonar" id="monto_a_abonar" class="form-control"></td>
          </tr>
          <tr>
            <td><strong>Descuento Especial:  </strong></td>
            <td><input name="monto_descuento" type="text" class="form-control" id="monto_descuento" value="0" /></td>
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