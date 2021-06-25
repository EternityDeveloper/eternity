<?php
if (!isset($protect)){
	exit;
}
 
if (!isset($_REQUEST['contrato'])){
	exit;
} 

SystemHtml::getInstance()->includeClass("contratos","Contratos");
SystemHtml::getInstance()->includeClass("cobros","Zonas");
SystemHtml::getInstance()->includeClass("cobros","Cobros");
SystemHtml::getInstance()->includeClass("client","PersonalData");
$person= new PersonalData($protect->getDBLink());	
$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));

 
/*DATOS DEL OFICIAL Y MOTORIZADO*/
$ofi_moto=Cobros::getInstance()->getCobradorMotorizadoAreaC($contrato->serie_contrato,$contrato->no_contrato);

	
$zonas=new Zonas($protect->getDBLink()); 
$listado=$zonas->getZona();

$con=new Contratos($protect->getDBLink()); 
$data=$con->getDetalleGeneralFromContrato($contrato->serie_contrato,$contrato->no_contrato);
$data['valor_cuota']=$data['valor_cuota']+$data['monto_penalizacion'];
$capita_interes=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);

$addressData=$person->getAddress($data['id_nit_cliente']);
 
 
?><div class="modal fade" id="modal_requerimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Requerimiento</h4>
      </div>
      <div class="modal-body">
      <form id="frm_cobro" name="frm_cobro">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td width="150"><strong>Movimiento:   </strong></td>
            <td><select name="tipo_movimiento_rmc" id="tipo_movimiento_rmc" class="form-control required">
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `tipo_movimiento` WHERE TIPO_MOV IN ('CUOTA')  ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $encriptID?>" selected><?php echo $row['DESCRIPCION']?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Saldo:</strong></td>
            <td><strong><?php echo $data['tipo_moneda']=="LOCAL"?'RD$':'US$'; ?><?php echo number_format(($capita_interes->capital_pendiente+$capita_interes->interes_pendiente),2);?></strong></td>
          </tr>
          <tr>
            <td><strong>Min. compromiso</strong></td>
            <td><input name="compromiso2" type="text" disabled="disabled" class="form-control" id="compromiso2" value="<?php echo number_format($data['valor_cuota']>0?$data['valor_cuota']:1,2);?>" /></td>
          </tr>
<?php 
	if ($data['tipo_moneda']!="LOCAL"){
		$tasa=$con->getTasa(); 
?>
          <tr>
            <td><strong>Tasa</strong></td>
            <td><select name="tipo_movimiento_rmc2" id="tipo_movimiento_rmc2" class="form-control required">
              <option value="">Seleccione</option>
              <?php 
	foreach($tasa as $key =>$row){
	 	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $encriptID?>" <?php if ($row['moneda']==$data['tipo_moneda']){ echo 'selected="selected"';}?> ><?php echo $row['moneda']?></option>
              <?php } ?>
            </select></td>
          </tr>
 <?php   
 	} ?>         
          <tr>
            <td><strong>Compromiso:</strong></td>
            <td><input name="compromiso" type="text" class="form-control" id="compromiso" value="<?php echo number_format($data['valor_cuota']>0?$data['valor_cuota']:1,2);?>">
            </td>
          </tr>
          <tr>
            <td><strong>Cantidad cuotas:</strong></td>
            <td><input type="text" name="cantidad_cuotas" id="cantidad_cuotas" class="form-control"></td>
          </tr>
          <tr>
            <td><strong>Monto abono:</strong></td>
            <td><input name="monto_abono" type="text" disabled class="form-control" id="monto_abono"></td>
          </tr>
          <tr>
            <td><strong>Nuevo Saldo</strong>:</td>
            <td><input name="nuevo_saldo" type="text" disabled class="form-control" id="nuevo_saldo"></td>
          </tr>
          <tr>
            <td><strong>Fecha requerimiento</strong>:</td>
            <td><input  name="fecha_requerimiento" type="text" class="form-control" style="width:120px; font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;padding-right:10px;" id="fecha_requerimiento"   value="<?php echo date("d-m-Y");?>" /></td>
          </tr>
          <tr>
            <td valign="top"><strong>Direccion:</strong></td>
            <td><select name="req_direccion" id="req_direccion" class="form-control required">
              <option value="">Seleccione</option>
              <option value="add">Agregar</option>
              <?php  
foreach($addressData as $key=>$val){  
	$direccion=$val['provincia'].", ".$val['ciudad'] .", ".$val['sector'];
	$direccion.=trim($val['avenida'])!=""?",".$val['avenida']:'';
	$direccion.=trim($val['calle'])!=""?",".$val['calle']:'';
	$direccion.=trim($val['zona'])!=""?",".$val['zona']:'';
	$direccion.=trim($val['manzana'])!=""?",".$val['manzana']:'';
	$direccion.=trim($val['numero'])!=""?",".$val['numero']:'';
	$direccion.=trim($val['referencia'])!=""?",".$val['referencia']:'';
	$direccion.=trim($val['observaciones'])!=""?",".$val['observaciones']:'';
	$encriptID=System::getInstance()->Encrypt(json_encode($val));
?>
              <option value="<?php echo $encriptID?>" <?php  if ($val['tipo']=="Cobro"){ echo 'selected="selected"';}?> ><?php echo $direccion?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Oficial:</strong></td>
            <td><select name="txt_oficial" id="txt_oficial" class="form-control required">
              <option value="">Seleccione</option>
              <?php 
 
foreach($listado as $key =>$row){
 
 ?>
              <option value="<?php echo $row['encID']?>" <?php if ($row['oficial_nit']==$ofi_moto['nitoficial']){echo 'selected="selected"';}?> ><?php echo $row['nombre_oficial']?></option>
 <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Motorisado:</strong></td>
            <td><select name="txt_motorisado" id="txt_motorisado" class="form-control required">
              <option value="">Seleccione</option>
              <?php 
foreach($listado as $key =>$row){
 
 ?>
              <option value="<?php echo $row['encID']?>" <?php if ($row['motorizado']==$ofi_moto['nitmotorizado']){echo 'selected="selected"';}?>><?php echo $row['nombre_motorizado']?></option>
 <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Comentarios:</strong></td>
            <td><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
        </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_requerimientoc" class="btn btn-primary" disabled>Procesar</button>
      </div>
    </div>
  </div>
</div>
<?php 
$info=ob_get_contents();
ob_clean();
echo json_encode(array("html"=>utf8_encode($info),"compromiso"=>$data['valor_cuota'],"saldo_actual"=>($capita_interes->capital_pendiente+$capita_interes->interes_pendiente)));
?>