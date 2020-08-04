<?php 
if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("contratos","Contratos");   
SystemHtml::getInstance()->includeClass("caja","Caja"); 

$caja= new Caja($protect->getDBLINK());  


 
if (validateField($_REQUEST,"payment_view")){
	  	
	if ($_REQUEST['payment_view']=="payment"){
		$tasa_cambio=1;
		if (validateField($_REQUEST,"contrato")){
			$cn=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
			$con=new Contratos($protect->getDBLink()); 
			$cdata=$con->getBasicInfoContrato($cn->serie_contrato,$cn->no_contrato);
			$tasa_cambio=$caja->getTasaActual($cdata->tipo_moneda);
		}

		 	
?> 
<div class="modal fade" id="modal_forma_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:430px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">FORMA DE PAGO</h4>
      </div>
      <div class="modal-body">

<table width="200" border="0" cellspacing="0" cellpadding="0"  >
 
  <tr>
    <td><form name="form_forma_pago" id="form_forma_pago" method="post" action="">
    <table width="400" border="0" cellspacing="5" cellpadding="5" class="tb_forma_pago">
      <tr>
        <td align="right"><strong>Forma de pago:</strong></td>
        <td><select name="forma_pago" id="forma_pago" class="form-control required" style="width:200px;"   >
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `formas_pago`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	 
?>
          <option value="<?php echo $row['forpago']?>" ><?php echo $row['descripcion_pago']?></option>
          <?php } ?>
        </select></td>
      </tr>
      <tr  class="tipo_trans_tarjeta" style="display:none">
        <td align="right"><strong>Banco:</strong></td>
        <td><select name="banco" id="banco" class="form-control required" style="width:200px;" >
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
        <td align="right"><strong>Monto:</strong></td>
        <td><input type="text" name="monto" id="monto"  class="form-control required" style="width:200px;" /></td>
      </tr>
      <tr  class="tipo_trans_tarjeta" style="display:none">
        <td align="right"><strong>Autorización:</strong></td>
        <td><input type="text" name="autorizacion" id="autorizacion"  class="form-control required" style="width:200px;" /></td>
      </tr>
      <tr  >
        <td align="right"><strong>Tipo de cambio:</strong></td>
        <td><input name="tipo_cambio" type="text"   class="form-control required" id="tipo_cambio" style="width:200px;" value="<?php echo $tasa_cambio;?>" /></td>
      </tr>
      <tr  >
        <td colspan="2" align="center">&nbsp;</td>
        </tr>
      <tr  >
        <td colspan="2" align="center"><button type="button" class="orangeButton" id="bt_fp_save">Agregar</button>&nbsp;<button type="button" class="redButton" id="bt_fp_cancel" style="padding:3px 6px;">Cancelar</button></td>
      </tr>
      <tr  >
        <td colspan="2" align="center">&nbsp;</td>
      </tr>
    </table>
    
        </form></td>
  </tr>
</table>
      </div>
 
    </div>
  </div>
</div>
<?php } 
 if ($_REQUEST['payment_view']=="payment_list"){
	 
	 $show_factura_fiscal=true;
	 $check_factura_fiscal=false;
 		if (validateField($_REQUEST,"contrato")){
			$cn=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
			 
			$con=new Contratos($protect->getDBLink()); 
			$cdata=$con->getBasicInfoContrato($cn->serie_contrato,$cn->no_contrato);
			$tasa_cambio=$caja->getTasaActual($cdata->tipo_moneda);
			$ret=$caja->isEstaRegistradoEnLaDGII($cdata->id_nit_cliente);  
			$show_factura_fiscal=$ret['valid'];
			$check_factura_fiscal=$ret['check'];
		}
		
	
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><h2 style="color:#FFF;font-size:14px;">FORMA DE PAGO</h2></td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;
      <button type="button" class="orangeButton" id="bt_agrega_fp">Agregar</button>  <button type="button" class="orangeButton" id="bt_agregar_descuento" style="display:none">Agregar descuento</button>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td id="detalle_pagos"><table id="list_formas_pagos" width="200%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
            <thead>
              <tr>
                  <td  style="font-size:14px;"><strong>Forma de pago</strong></td>
                  <td  style="font-size:14px;"><strong>Banco</strong></td>
                  <!--    <td><strong>Abono reserva</strong></td>-->
                  <td style="font-size:14px;"><strong>Autorización</strong></td>
                  <td style="font-size:14px;"><strong>Tipo de cambio</strong></td>
                  <td style="font-size:14px;"><strong>Monto</strong></td>
                  <td style="font-size:14px;"><strong>Monto en Q.</strong></td>
                  <td style="font-size:14px;">&nbsp;</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr style="display:none">
                <td valign="baseline"><strong>TIPO DESCUENTO</strong></td>
                <td colspan="2" valign="baseline">&nbsp;</td>
                <td align="right" valign="baseline"><strong>DESCUENTO</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td colspan="7" valign="baseline">
                  <table id="descuento_field_for_pay" width="500" border="0" align="right" cellpadding="0" cellspacing="0">
                  </table></td>
              </tr>
              <tr id="total_a_pagar_fp"  >
                <td colspan="5" align="right" style="font-size:14px;"><strong>TOTAL A PAGAR:&nbsp;</strong></td>
                <td id="f_pago_total_a_pagar"><span class="badge alert-danger"><?php
	  $monto=$caja->getItemMontoACobrar($_REQUEST['token']); 
	  if ($monto==0){
		$monto=$monto_a_pagar;  
	  }
	   echo number_format($monto,2);?>
                </span></td>
                <td>&nbsp;</td>
              </tr>
            </tfoot>
          </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td>
        <div id="alert_payment" class="alert alert-dismissable alert-danger" style="width:300px;margin: 0 auto;display:none">
        <button type="button" class="close" id="close_alert" data-dismiss="alert">×</button>
         Error, el monto pagado es menor de <strong id="vtotal_pago">0</strong>
        </div>    
    </td>
  </tr>
  <tr id="fiscal_question">
    <td id="bottom_question_cf"><input name="IneedCF" type="checkbox" id="IneedCF" value="1" />
    <label for="IneedCF">Desea comprobante final?</label></td>
  </tr>    
    <td id="bottom_question_ncf" style="<?php if (!$show_factura_fiscal){ echo 'display:none';} ?>"><input name="IneedFF" type="checkbox" id="IneedFF" value="1" <?php if ($check_factura_fiscal=="1"){?>checked="checked"<?php } ?>/>
    <label for="IneedFF">Desea factura fiscal?</label></td>
  </tr>          
  <tr>
    <td id="factura_view_">&nbsp;</td>
  </tr>
  <tr>
    <td id="payment_mensaje_td" style="display:none"></td>
  </tr>
  <tr>
    <td align="center"><span class="label label-danger" id="p_err_message" style="display:none"></span></td>
  </tr>
  
</table>
<?php } ?>
<?php } 
 if ($_REQUEST['payment_view']=="payment_list_detalle"){
	 
 	 
?>
<table id="list_formas_pagos2" width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
  <thead>
    <tr>
      <td  style="font-size:14px;"><strong>Forma de pago</strong></td>
      <td  style="font-size:14px;"><strong>Banco</strong></td>
      <!--    <td><strong>Abono reserva</strong></td>-->
      <td style="font-size:14px;"><strong>Autorización</strong></td>
      <td style="font-size:14px;"><strong>Tipo de cambio</strong></td>
      <td style="font-size:14px;"><strong>Monto</strong></td>
      <td style="font-size:14px;"><strong>Monto en Q</strong></td>
      <td style="font-size:14px;">&nbsp;</td>
    </tr>
  </thead>
  <tbody id="t_body_fpago2">
<?php 

	$list=$caja->getItem($_REQUEST['token']);

$monto_a_pagar=0;
if (count($list)>0){  
	foreach($list as $key=>$val){
		$id=System::getInstance()->Encrypt(json_encode($val));
		$monto_a_pagar=$monto_a_pagar+($val['tipo_cambio']*$val['monto_a_pagar']);
?>
    <tr>
      <td><?php echo $val['label_forma_pago']?></td>
      <td  ><?php echo $val['label_banco']?></td>
      <td><?php echo $val['autorizacion']?></td>
      <td align="center"><?php echo $val['tipo_cambio']?></td>
      <td><?php echo number_format($val['monto_a_pagar'],2);?></td>
      <td><?php echo number_format($val['monto_a_pagar']*$val['tipo_cambio'],2);?></td>
	  <td><span class="fp_remove_dt ui-button-icon-primary ui-icon ui-icon-closethick" id="<?php echo $id?>" style="cursor:pointer;">X</span></td>      
    </tr>
    <?php }
 } ?>
  </tbody>
  <tfoot>
    <tr id="tipo_descuento_tr2" style="display:none">
      <td valign="baseline" style="font-size:12px;"><strong>TIPO DESCUENTO</strong></td>
      <td colspan="2" valign="baseline"  style="font-size:12px;">&nbsp;</td>
      <td align="right" valign="baseline"  style="font-size:12px;"><strong>DESCUENTO</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
<?php 
/*OBTENGO LOS DESCUENTOS*/
$list=  $caja->getDescuento($_REQUEST['token']);
$monto_total=0;
if (count($list)>0){  
	foreach($list as $key=>$val){  
		$id=System::getInstance()->Encrypt(json_encode($val));
		$desc=json_decode(System::getInstance()->Decrypt($val['descuento_id'])); 
	
		if ($desc->monto_ingresado=="S"){ 
			$desc->monto=$val['monto']; 
		}
?>  

    <tr>
      <td valign="baseline" style="font-size:12px;"><?php echo $desc->descripcion?></td>
      <td colspan="2" valign="baseline"  style="font-size:12px;">&nbsp;</td>
      <td align="right" valign="baseline"  style="font-size:12px;"><?php 
	  	if ($desc->monto>0){echo number_format($desc->monto,2);}else
		if ($desc->porcentaje>0){echo $desc->porcentaje."%";}
		?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><span class="fp_remove_desc ui-button-icon-primary ui-icon ui-icon-closethick"  id="<?php echo $id?>" style="cursor:pointer;">X</span></td>
    </tr>
<?php } 
}?> 
    <tr >
      <td colspan="5" align="right"  style="font-size:14px;"><strong>TOTAL A PAGAR:&nbsp;</strong></td>
      <td  style="font-size:14px;" ><span class="badge alert-danger" id="total_a_pagar"><?php
	  $monto=$caja->getItemMontoACobrar($_REQUEST['token']); 
	  if ($monto==0){
		$monto=$monto_a_pagar;  
	  }
	   echo number_format($monto,2);?></span></td>
      <td>&nbsp;</td>
    </tr>
  </tfoot>
</table>
<?php } ?>
