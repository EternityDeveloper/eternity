<?php 
if (!isset($protect)){
	exit;	
}  

SystemHtml::getInstance()->includeClass("cobros","FacturarPS"); 
//$servicios= new Servicios($protect->getDBLink()); 
$producto=FacturarPS::getInstance()->getProductos(); 
?>
<div class="modal fade" id="facturar_pro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:440px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Facturar Productos/Servicios</h4>
      </div>
      <div class="modal-body">
 <form id="detalle_inhumado_form" >
   <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tb_detalle ">
     <tr>
       <td width="100">Producto:</td>
       <td><select name="sv_inh_producto" id="sv_inh_producto" class="form-control required"  >
         <option value="">Seleccione</option>
         <?php  
		foreach($producto as $key=>$row){
			$id=System::getInstance()->Encrypt(json_encode($row));
			$inf=array("existencia"=>$row['existencia'],
						"costo"=>$row['costo']);
		?>
         <option value="<?php echo $id."__".base64_encode(json_encode($inf));?>" ><?php echo $row['producto']?></option>
         <?php } ?>
       </select></td>
     </tr>
     <tr>
       <td>Precio:</td>
       <td><input name="inh_precio" type="text" class="form-control" id="inh_precio" /></td>
     </tr>
     <tr>
       <td>Cantidad:</td>
       <td><input type="text" name="inh_cantidad" id="inh_cantidad" class="form-control required" /></td>
     </tr>
   </table>	
 
  </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="agregar_producto_inhu" class="btn btn-primary">Agregar</button>
      </div>
    </div>
  </div>
</div>
 