<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
if (isset($_REQUEST['no_reserva'])){
	SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 
	$_reserva=new Reserva($protect->getDBLink());
	//$data=$caja->searchByReserva($_REQUEST['field']);
	$reserva=System::getInstance()->Decrypt($_REQUEST['no_reserva']);

		$SQL="SELECT 
			*, 
			`reserva_inventario`.no_reserva AS no_reserva_id,
			reserva_inventario.no_recibo AS serie_recibo_no,
			DATEDIFF(reserva_inventario.`fecha_fin`,CURDATE()) AS day_restantes,
			DATE_FORMAT(reserva_inventario.fecha_reserva, '%d-%m-%Y %h:%m:%s') AS  fecha_reserva,
			DATE_FORMAT(reserva_inventario.fecha_fin, '%d-%m-%Y %h:%m:%s') AS  fecha_fin,
			CONCAT(asesor.`primer_nombre`,' ',asesor.`primer_apellido`) AS nombre_asesor,
			CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) AS nombre_cliente,
			sys_personas.id_nit AS nit
		FROM reserva_inventario
		INNER JOIN `reserva_ubicaciones` ON (`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva) 
		INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
		INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
		LEFT JOIN `sys_personas` ON (sys_personas.`id_nit`=reserva_inventario.`id_nit`)
		LEFT JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=reserva_inventario.`nit_comercial`)
		WHERE reserva_ubicaciones.estatus=1 AND reserva_inventario.no_reserva='". mysql_real_escape_string($reserva) ."' ";
 
	//	echo $SQL;
		$rs=mysql_query($SQL);
		$data=mysql_fetch_object($rs);
		
 
	//	$isApplyIncial=$_reserva->getIfReservaContainTipoMovimiento(1,$reserva);
 
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
<?php  if ($isApplyIncial>0){  ?>            
              <br />
<div  class="alert alert-dismissable alert-info" >
        <button type="button" class="close" id="close_alert" data-dismiss="alert">×</button>
       <strong>A esta reserva se le realizó un pago de Inicial. Debes de proceder a generar el contrato!</strong></div>
<?php } ?>        
  <table width="100%" border="1">
 
    <tr>
      <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
        <tr>
          <td colspan="8" style="margin:0px;padding:0px;border:#FFF solid 0px;color:#FFF"><h2>Detalle reserva</h2></td>
          </tr>
        <tr>
          <td><strong>No. reserva:</strong></td>
          <td><strong>Cliente:</strong></td>
          <td><strong>Asesor:</strong></td>
          <td><strong>Tipo de reserva: </strong></td>
          <td><strong>Recibo Serie Reserva:</strong></td>
          <td><strong>Inicia Reserva:</strong></td>
          <td><strong>Termina Reserva:</strong></td>
          <td><strong>Dias restantes:</strong></td>
        </tr>
        <tr>
          <td><?php echo $data->no_reserva;?></td>
          <td><?php echo $data->nombre_cliente;?></td>
          <td><?php echo $data->nombre_asesor;?></td>
          <td><?php echo $data->reserva_descrip;?></td>
          <td><?php echo $data->serie_recibo_no;?></td>
          <td><?php echo $data->fecha_reserva;?></td>
          <td><?php echo $data->fecha_fin;?></td>
          <td><span class="day_restantes"><?php echo $data->day_restantes;?></span> Dias</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="left" valign="top"><table  width="100%" border="1"  style="border-spacing:10px;">
        <tr>
          <td><h2 id="h_" style="color:#FFF">Listado de items  reservados</h2></td>
        </tr>
        <tr>
          <td><table id="tb_items_reservados" width="100%" border="1" class="table table-striped table-hover">
            <thead>
              <tr>
                <td align="center"><strong>Jardin</strong></td>
                <td align="center"><strong>Fase</strong></td>
                <td align="center"><strong>Bloque</strong></td>
                <td align="center"><strong>Lote</strong></td>
                <td align="center"><strong>Cavidades</strong></td>
                <td align="center"><strong>Osarios</strong></td>
              </tr>
            </thead>
            <tbody>
              <?php
   
$SQL="SELECT  
	reserva_inventario.`no_reserva`,
	inventario_jardines.id_jardin, 
	inventario_jardines.id_fases, 
	inventario_jardines.`lote`, 
	inventario_jardines.`bloque`, 
	inventario_jardines.`cavidades`,
	inventario_jardines.`osarios`,
	reserva_inventario.`no_recibo` AS serie_recibo_no,		
	DATE_FORMAT(reserva_inventario.fecha_reserva, '%d-%m-%Y') AS  fecha_reserva,
	DATE_FORMAT(reserva_inventario.fecha_fin, '%d-%m-%Y') AS  fecha_fin
 FROM `reserva_inventario` 
INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
INNER JOIN `inventario_jardines` ON (inventario_jardines.no_reserva=reserva_inventario.no_reserva)
 WHERE reserva_inventario.no_reserva='".$data->no_reserva."'";
		  
	$rs=mysql_query($SQL);
	
	$total=mysql_num_rows($rs);
	
	while($row=mysql_fetch_object($rs)){
		$encryt=System::getInstance()->Encrypt(json_encode($row));
   ?>
              <tr>
                <td align="center" class="display"><?php echo $row->id_jardin; ?></td>
                <td align="center" class="display"><?php echo $row->id_fases;?></td>
                <td align="center" class="display"><?php echo $row->bloque;?></td>
                <td align="center" class="display" ><?php echo $row->lote;?></td>
                <td align="center" class="display" ><?php echo $row->cavidades?></td>
                <td align="center" ><?php echo $row->osarios?></td>
              </tr>
              <?php  
	} ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center" valign="top"><button type="button" class="btn btn-warning" id="bt_abono_reserva">Realizar Transaccion</button>
&nbsp;&nbsp;
 </td>
    </tr>
    <tr>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
  
    <tr>
      <td valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 style="margin-top:5px;color:#FFF">Abonos realizados</h2></td>
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
                <td align="center"><strong>Sub-Total</strong></td>
                <td align="center"><strong>Descuento</strong></td>
                <td align="center"><strong>Monto Pagado</strong></td>
                <td align="center"><strong>Monto</strong></td>
                <!--                <td align="center"><strong>Reporte Venta</strong></td>
-->
              </tr>
            </thead>
            <tbody>
              <?php
   

 $SQL="SELECT movimiento_caja.SERIE,
 			  movimiento_caja.NO_DOCTO,
			  formas_pago.descripcion_pago, 
			  forma_pago_caja.MONTO,
			 movimiento_caja.`REP_VENTA`,
			 movimiento_caja.`NO_DOCTO`,  
			 movimiento_caja.`SERIE`,
			 movimiento_caja.DESCUENTO,
	DATE_FORMAT(movimiento_caja.`FECHA`, '%d-%m-%Y') AS fecha FROM forma_pago_caja
INNER JOIN `movimiento_caja` ON (movimiento_caja.id_nit=forma_pago_caja.id_nit AND
movimiento_caja.`ID_CAJA`=movimiento_caja.ID_CAJA AND movimiento_caja.`TIPO_DOC`=forma_pago_caja.TIPO_DOC AND
movimiento_caja.`NO_DOCTO`=forma_pago_caja.NO_DOCTO AND movimiento_caja.`SERIE`=forma_pago_caja.SERIE) 
INNER JOIN `formas_pago` ON (formas_pago.`forpago`=forma_pago_caja.FORMA_PAGO)
WHERE forma_pago_caja.id_nit='".$data->nit."' and movimiento_caja.NO_RESERVA='".$data->no_reserva."' AND movimiento_caja.`INICIAL` in ('N','S')  ";	
 
	$rs=mysql_query($SQL);
	$acumulado=0;
	while($row=mysql_fetch_object($rs)){
		$id_pago=System::getInstance()->Encrypt(json_encode($row));
 		$acumulado=$acumulado+($row->MONTO-$row->DESCUENTO);
   ?>
              <tr>
                <td align="center" class="display"><?php echo $row->fecha;?></td>
                <td align="center" class="display"><?php echo $row->descripcion_pago; ?></td>
                <td align="center" class="display"><?php echo $row->SERIE;?></td>
                <td align="center" class="display" ><?php echo $row->NO_DOCTO;?></td>
                <td align="center" class="display" ><?php echo $row->REP_VENTA ;?></td>
                <td align="center" class="display" ><?php echo number_format($row->MONTO,2);?></td>
                <td align="center" class="display" ><?php echo number_format($row->DESCUENTO,2);?></td>
                <td align="center" class="display" ><?php echo number_format($row->MONTO-$row->DESCUENTO,2);?></td>
                <td align="center" class="display" ><a href="./?mod_caja/delegate&doc_reserva_fact&reserva=<?php echo $_REQUEST['no_reserva'];?>&pago=<?php echo $id_pago;?>" target="dsa" ><img src="images/preferences_desktop_printer.png" width="22" height="26" /></a></td>
               
                <?php 
	} ?>
              </tr>
              <tr>
                <td colspan="5" align="right" class="display"><strong>Monto Acumulado:</strong></td>
                <td align="center" class="display" >&nbsp;</td>
                <td align="center" class="display" >&nbsp;</td>
                <td align="center" class="display" ><?php echo number_format($acumulado,2);?></td>
                <td align="center" class="display" >&nbsp;</td>
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