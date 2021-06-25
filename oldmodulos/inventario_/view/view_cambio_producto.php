<?php 
if (!isset($protect)){
	exit;	
}
 
if (!isset($_REQUEST['producto'])){
	exit;	
}
$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));

if (!isset($producto->id)){
	exit;	
}
if (!isset($producto->id_jardin)){
	exit;	
}


// print_r($producto);
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">CAMBIO DE UBICACION</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="100%" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td width="150" align="left"><strong>PARCELA ACTUAL:</strong></td>
                  <td width="324"><?php echo $producto->jardin." ".$producto->id_jardin."-".$producto->id_fases."-".$producto->bloque."-".$producto->lote;?></td>
                </tr>
                <tr>
                  <td align="left"><strong>PARCELA CAMBIO:</strong></td>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="150" id="producto_name">&nbsp;</td>
                      <td><button class="btn btn-default" id="buscar_producto" type="button" >Buscar</button></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td colspan="2" align="left"><strong>COMENTARIO</strong></td>
                </tr>
                <tr>
                  <td colspan="2" align="left">
                  <textarea name="cu_comentario" id="cu_comentario" class="form-control" cols="45" rows="5"></textarea></td>
                </tr>
            </table></td>
            </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="prod_realizar_cambio" disabled="disabled" class="btn btn-primary">Realizar cambio</button>
      </div>
    </div>
  </div>
</div>