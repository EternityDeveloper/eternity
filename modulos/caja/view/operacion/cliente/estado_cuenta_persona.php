<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

$id_nit=System::getInstance()->Decrypt($_REQUEST['no']);
  
if (isset($_REQUEST['no'])){ 
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	 
	$person= new PersonalData($protect->getDBLink(),$_REQUEST); 
	$peron_data=$person->getClientData($id_nit); 
 
}
 
 
?> 
<style>
h2{
	margin-top:0px;	
}

 
.tb_detalle > tbody > tr > th,
.tb_detalle > tfoot > tr > th,
.tb_detalle > thead > tr > td,
.tb_detalle > tbody > tr > td,
.tb_detalle > tfoot > tr > td {
  padding: 3px;
  line-height: 1.42857143;
  vertical-align: top;
  border-top: 1px solid #ddd;
}
	
</style>
<form name="form_reserva_" id="form_reserva_" method="post">
 
 <ul class="breadcrumb" style="margin:0px;">
                <li class="active"><a href="#" id="back_r">IR ATRAS</a></li> 
              </ul>
       
  <table width="100%" border="1">
 
    <tr>
      <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="tb_detalle table-striped">
          <tr>
            <td colspan="5" style="margin:0px;padding:0px;border:#FFF solid 0px;color:#FFF"><h2>Inf cliente</h2></td>
          </tr>
          <tr>
            <td><strong>IDNIT</strong></td>
            <td><strong>Cliente</strong></td>
            <td><strong>Telefono</strong></td>
            <td><strong>Fecha nacimiento</strong></td>
            <td><strong>Lugar de nacimiento</strong></td>
          </tr>
          <tr>
            <td><?php echo $peron_data['id_nit'];?></td>
            <td><?php echo $peron_data['primer_nombre']." ".$peron_data['segundo_nombre']." ".$peron_data['primer_apellido']." ".$peron_data['segundo_apellido'];?></td>
            <td><?php echo trim($person_data['telefono'])==""?'N/A':$person_data['telefono'];?></td>
            <td><?php echo $peron_data['fecha_nac'];?></td>
            <td><?php echo $peron_data['lugar_nacimiento'];?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="5" align="center"><button type="button" class="btn btn-warning" id="bt_abono_reserva">Realizar Transaccion</button></td>
          </tr>
          <tr>
            <td colspan="5">&nbsp;</td>
          </tr>
      </table></td>
    </tr>
  
    <tr>
      <td valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 style="margin-top:5px;color:#FFF">Transacciones realizadas</h2></td>
        </tr>
        <tr>
          <td><table id="tb_abonos_reservas" width="100%" border="1" class="table table-striped table-hover">
            <thead>
              <tr>
                <td align="center"><strong>Fecha</strong></td>
                <td align="center"><strong>Forma de pago</strong></td>
                <td align="center"><strong>Serie recibo</strong></td>
                <td align="center"><strong>No. Recibo</strong></td>
                <td align="center"><strong>No. Rerpote venta</strong></td>
                <td align="center"><strong>Monto</strong></td>
                <!--                <td align="center"><strong>Reporte Venta</strong></td>
-->
              </tr>
            </thead>
            <tbody>
              <?php
   

 $SQL="SELECT formas_pago.descripcion_pago, forma_pago_caja.MONTO,
 movimiento_caja.`REP_VENTA`,
 movimiento_caja.`NO_DOCTO`,  movimiento_caja.`SERIE`,
	DATE_FORMAT(movimiento_caja.`FECHA`, '%d-%m-%Y') AS fecha FROM forma_pago_caja
INNER JOIN `movimiento_caja` ON (movimiento_caja.id_nit=forma_pago_caja.id_nit AND
movimiento_caja.`ID_CAJA`=movimiento_caja.ID_CAJA AND movimiento_caja.`TIPO_DOC`=forma_pago_caja.TIPO_DOC AND
movimiento_caja.`NO_DOCTO`=forma_pago_caja.NO_DOCTO AND movimiento_caja.`SERIE`=forma_pago_caja.SERIE) 
INNER JOIN `formas_pago` ON (formas_pago.`forpago`=forma_pago_caja.FORMA_PAGO)
WHERE forma_pago_caja.id_nit='".$id_nit."' AND movimiento_caja.`INICIAL`='N'  ";	  
	$rs=mysql_query($SQL);
	$acumulado=0;
	while($row=mysql_fetch_object($rs)){
		$id_pago=System::getInstance()->Encrypt(json_encode($row));
 		$acumulado=$acumulado+$row->MONTO;
   ?>
              <tr>
                <td align="center" class="display"><?php echo $row->fecha;?></td>
                <td align="center" class="display"><?php echo $row->descripcion_pago; ?></td>
                <td align="center" class="display"><?php echo $row->SERIE;?></td>
                <td align="center" class="display" ><?php echo $row->NO_DOCTO;?></td>
                <td align="center" class="display" ><?php echo $row->REP_VENTA ;?></td>
                <td align="center" class="display" ><?php echo number_format($row->MONTO,2);?></td>
                <!--                <td align="center" class="display" ><a href="./?mod_inventario/reserva/reservar&amp;view_recibo_ventas=1&amp;reserva=<?php echo $_REQUEST['reserva_id'];?>&pago=<?php echo $id_pago;?>" style="text-decoration:none" target="new"><img src="images/document_preview.png" width="30" height="30" /></a></td>
                </tr>-->
                <?php 
	} ?>
              </tr>
              <tr>
                <td colspan="5" align="right" class="display"><strong>Monto Acumulado:</strong></td>
                <td align="center" class="display" ><?php echo number_format($acumulado,2);?></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </table></td>
      </tr>
 
    <tr>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
   </table>
 
</form>