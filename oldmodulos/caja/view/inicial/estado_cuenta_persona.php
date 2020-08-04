<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
	$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);
 

if (isset($_REQUEST['id_nit'])){ 
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
      <td width="520" align="left" valign="top"><table width="500" border="1" style="border-spacing:10px;" class="tb_detalle table-striped">
        <tr>
          <td colspan="2" style="margin:0px;padding:0px;border:#FFF solid 0px;"><h2>Detalle cliente</h2></td>
        </tr>
        <tr >
          <td><strong>Cliente:</strong></td>
          <td><?php echo $peron_data['primer_nombre']." ".$peron_data['segundo_nombre']." ".$peron_data['primer_apellido']." ".$peron_data['segundo_apellido'];?></td>
        </tr>
        <tr >
          <td><strong>Telefono:</strong></td>
          <td><?php echo trim($person_data['telefono'])==""?'N/A':$person_data['telefono'];?></td>
        </tr>
        <tr >
          <td><strong>Fecha nacimiento: </strong></td>
          <td><?php echo $peron_data['fecha_nac'];?>&nbsp;</td>
        </tr>
        <tr >
          <td><strong>Lugar de nacimiento: </strong></td>
          <td><?php echo $peron_data['lugar_nacimiento'];?></td>
        </tr>
        <tr >
          <td width="173">&nbsp;</td>
          <td width="311">&nbsp;</td>
        </tr>
        <tr >
          <td colspan="2" align="center"><button type="button" class="btn btn-warning" id="bt_abono_reserva">Realizar Abono</button>
            &nbsp;&nbsp; </td>
          </tr>
      </table></td>
      <td align="left" valign="top">&nbsp;</td>
    </tr>
  
    <tr>
      <td colspan="2" valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 style="margin-top:5px;">Abonos realizados</h2></td>
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
   
$SQL=" SELECT *,
	DATE_FORMAT(movimiento_caja.`FECHA_MOVIMIENTO_CAJA`, '%d-%m-%Y') AS fecha 
FROM `movimiento_caja` 
INNER JOIN `forma_pago_caja` ON (forma_pago_caja.`NO_DOCUMENTO_CAJA`=movimiento_caja.NO_DOCUMENTO_CAJA)
INNER JOIN `formas_pago` ON (formas_pago.`forpago`=forma_pago_caja.forpago)
WHERE 
movimiento_caja.`ID_PERSONA`='".$id_nit."' and  movimiento_caja.TIPO_MOVIMIENTO='4' order by movimiento_caja.`FECHA_MOVIMIENTO_CAJA` DESC ";
 		  
	$rs=mysql_query($SQL);
	$acumulado=0;
	while($row=mysql_fetch_object($rs)){
		$id_pago=System::getInstance()->Encrypt(json_encode($row));
 		$acumulado=$acumulado+$row->MONTO_PAGO_CAJA;
   ?>
              <tr>
                <td align="center" class="display"><?php echo $row->fecha;?></td>
                <td align="center" class="display"><?php echo $row->descripcion_pago; ?></td>
                <td align="center" class="display"><?php echo $row->SERIE_DOCUMENTO_CAJA;?></td>
                <td align="center" class="display" ><?php echo $row->NO_DOCUMENTO_CAJA;?></td>
                <td align="center" class="display" ><?php echo $row->REPORTE_VENTA_CAJA ;?></td>
                <td align="center" class="display" ><?php echo number_format($row->MONTO_PAGO_CAJA,2);?></td>
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
      <td colspan="2" align="center" valign="top">&nbsp;</td>
    </tr>
   </table>
 
</form>