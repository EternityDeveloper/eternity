<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
if (!isset($_REQUEST['id'])){
	echo "Debe de seleccionar un item!";
	exit;
}

$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());
$data=json_decode($dencryt);
 
// print_r($data);

?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<div class="modal fade" id="LotesEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DETALLE PARCELA</h4>
      </div>
      <div class="modal-body">
<form name="form_user_edit" id="form_user_edit" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1" class="table">
 
  <tr>
    <td>Jardin:</td>
    <td><strong><?php echo  $data->id_jardin ." - ".$data->jardin?></strong></td>
  </tr>
  <tr>
    <td>Fase:</td>
    <td><strong><?php echo $data->id_fases ." - ".$data->fase?></strong></td>
  </tr>
  <tr>
    <td>Bloque:</td>
    <td><strong><?php echo  $data->bloque;?></strong></td>
  </tr>

  <tr>
    <td>Lote:</td>
    <td><strong><?php echo  $data->lote;?></strong></td>
  </tr>
  <tr>
    <td>Cavidades </td>
    <td><select name="cavidades" id="cavidades" class="form-control">
      <option value="">Seleccione</option>
		<?php
        	for($i=1;$i<=2;$i++){
		?>
        	 <option value="<?php echo $i;?>" <?php
             	if (count($data)>0){
					if ($data->cavidades==$i){
						echo "selected";	
					}	
				}
			 
			 ?>><?php echo $i;?></option>
     <?php }?>
    </select></td>
  </tr>
  <tr>
    <td>Osarios</td>
    <td><select name="osarios" id="osarios"  class="form-control">
      <option value="">Seleccione</option>
		<?php
        	for($i=0;$i<=10;$i++){
		?>
        	 <option value="<?php echo $i;?>" <?php
             	if (count($data)>0){
					if ($data->osarios==$i){
						echo "selected";	
					}	
				}
			 
			 ?>><?php echo $i;?></option>
     <?php }?>
    </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="button" name="liberar_parcela" id="liberar_parcela" class="btn btn-primary" value="Liberar"   /></td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_lote_edit" type="hidden" id="submit_lote_edit" value="1" />
      <input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id'];?>"></td>
  </tr>
 
</table>
</div>
</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="bt_save"   class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>