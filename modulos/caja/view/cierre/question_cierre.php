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
        <h4 class="modal-title" id="myModalLabel">CIERRE DE CAJA</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="444" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td align="left"><strong>TIPO CIERRE:</strong></td>
                  <td width="324"> 
                    <select name="tipo_cierre" id="tipo_cierre" class="form-control" style="width:200px;">
                      <option value="0" selected="selected">Seleccionar</option>
                      <option value="P">PARCIAL</option>
                      <option value="T">TOTAL</option>
                  </select></td>
                </tr>
                <tr>
                  <td align="left"><strong>CAJA:</strong></td>
                  <td><select name="ID_CAJA" id="ID_CAJA" class="form-control" style="width:200px;">
                  <option value="0" selected="selected">Seleccionar</option>
                    <?php 

$SQL="SELECT caja.* FROM caja
	INNER JOIN `Usuarios` ON (`caja`.id_usuario=Usuarios.id_usuario) ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                    <option value="<?php echo $encriptID?>" ><?php echo ($row['DESCRIPCION_CAJA'])?></option>
                    <?php } ?>
                  </select></td>
                </tr>
                <tr>
                  <td width="120" align="left"><strong>PERIODO DE:</strong></td>
                  <td><input  name="p_fecha_desde" type="text" class="filter_ textfield date_pick" style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="p_fecha_desde" readonly /></td>
                </tr>
            </table></td>
            </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_acta" class="btn btn-primary">Procesar</button>
      </div>
    </div>
  </div>
</div>