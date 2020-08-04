<?php 
if (!isset($protect)){
	exit;	
}
/*  
SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 

$reserva= new Reserva($protect->getDBLink(),$_REQUEST);*/

//$no_reserva=json_decode(System::getInstance()->Decrypt($_REQUEST['reserva'])); 
 
//$total=$reserva->getTotalReserva($no_reserva);


  
/*CUIDADO CON ESTE DATO
ES LA FORMA DE TRANSFERIR EL TOTAL DE RESERVA*/  
 
?>

<style>
.pay_item{
	float:right;
	margin-right:10px;
	cursor:pointer;
}
 
</style>
<div class="fsPage">
<form method="post"  action="" id="caja_payment"  name="caja_payment" class="fsForm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr>
          <td ><table width="100%" border="0" cellspacing="0" cellpadding="0">
            
            <tr style="display:none">
              <td width="65" align="left" valign="baseline"><strong>&nbsp;Empresa</strong>:</td>
              <td><select name="fact_empresa" id="fact_empresa" class="required">
                <option value="">Seleccione</option>
                <?php 

$SQL="SELECT * FROM empresa WHERE estatus=1 and EM_ID IN ('CJM','PJM')";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(trim($row['EM_ID']));
?>
                <option value="<?php echo $encriptID?>" ><?php echo $row['EM_NOMBRE']?></option>
                <?php } ?>
              </select></td>
              </tr>
  
          </table></td>
          </tr> 
          <tr>
          <td id="doc_serie_view"></td>
          </tr> 
          <tr>
          <td id="forma_pago_view">&nbsp;</td>
          </tr> 
          <tr>
          <td id="factura_view_">&nbsp;</td>
          </tr> 
        <tr>
          <td id="payment_mensaje_td" style="display:none"> </td>
        </tr>   
    <tr>
          <td align="center"><span class="label label-danger" id="p_err_message" style="display:none"></span></td>
        </tr>               
        <tr>
          <td><h2>OBSERVACION</h2></td>
        </tr>
        <tr>
          <td align="center"><textarea name="observacion" id="observacion" cols="45" rows="5" style="width:95%" class="textfield_input"></textarea></td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><button type="button" class="greenButton" id="bt_caja_process">&nbsp;Procesar</button>
            <button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button>&nbsp;</td>
          </tr>
        <tr>
          <td align="center">&nbsp;</td>
          </tr>
  </table>   
    </td>
    </tr>
</table>

</form>
</div>