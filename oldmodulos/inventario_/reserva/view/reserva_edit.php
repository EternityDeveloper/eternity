<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
  
$data=json_decode(System::getInstance()->Decrypt($_REQUEST['reserva_id']));
 
if (count($data)<=0){
	echo json_encode(array("error"=>"Error parser JSON"));
	exit;
}
 
//  print_r($data);

 
// print_r($forma_pago);

?>
<style>
 .fsPage2{
	width:900px; 
	}
	.dataTables_wrapper{
		min-height:80px;	
	}
	.fp_transferencia{
		display:none;	
	}
	.fp_efectivo{
		display:none;
	 }
	.fp_tipo_reserva{
		display:none;		
	}

#h_ span{
	float:right;
	margin:0;
	margin-right:10px;
	color:#FFF;
	border-radius:10px;
	font-size:20px;
	height:21px;
	width:21px;
	font-weight:bold;
	text-align:center;
	cursor:pointer;
}	
#h_ span:hover{
	background-color:#FFF;
	color:#000;
}
	
</style>
<form name="form_reserva_<?php echo $_REQUEST['rand']?>" id="form_reserva_<?php echo $_REQUEST['rand']?>" method="post">
 <div id="step_2" class="fsPage fsPage2"  >
 
   <table width="100%" border="1">
    <tr>
      <td valign="top"><table width="100%" border="1" style="border-spacing:10px;">
     
        <tr>
          <td colspan="2"><h2>Detalle reserva</h2></td>
          </tr>
        <tr >
          <td align="right"><strong>No. reserva:</strong></td>
          <td><?php echo $data->no_reserva;?></td>
        </tr>
        <tr >
          <td width="40%" align="right"><strong>Cliente:</strong></td>
          <td><?php echo $data->nombre_cliente;?></td>
        </tr>
        <tr >
          <td align="right"><strong>Asesor:</strong></td>
          <td><?php echo $data->nombre_asesor;?></td>
        </tr>
        <tr >
          <td align="right"><strong>Tipo de reserva: </strong></td>
          <td><?php echo $data->reserva_descrip;?>&nbsp;</td>
        </tr>
        <tr >
          <td align="right"><strong>Recibo Serie Reserva:</strong></td>
          <td><?php echo $data->serie_recibo_no;?></td>
        </tr>
        <tr >
          <td align="right"><strong>Inicia Reserva:</strong></td>
          <td><?php echo $data->fecha_reserva;?></td>
        </tr>
        <tr >
          <td align="right"><strong>Termina Reserva:</strong></td>
          <td><?php echo $data->fecha_fin;?></td>
        </tr>
        <tr >
          <td align="right"><strong>Dias restantes:</strong></td>
          <td><span class="day_restantes"><?php echo $data->day_restantes;?></span> Dias</td>
        </tr>
      </table></td>
      <td valign="top"><table  width="100%" border="1"  style="border-spacing:10px;">
        <tr>
          <td><h2 id="h_">Listado de items  reservados</h2></td>
        </tr>
        <tr>
          <td><table id="tb_items_reservados" width="100%" border="1" class="display">
            <thead>
              <tr>
                <td align="center"><strong>Jardin</strong></td>
                <td align="center"><strong>Fase</strong></td>
                <td align="center"><strong>Bloque</strong></td>
                <td align="center"><strong>Lote</strong></td>
                <td align="center"><strong>Cavidades</strong></td>
                <td align="center"><strong>Osarios</strong></td>
                <td align="center"><strong></strong></td>
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
            <td align="center" class="display" ><?php
            if ($total>1){
			?><a href="#" id="<?php echo $encryt;?>" alt="Remover prospecto" class="remove_items_rev"><img src="images/cross.png"></a><?php } ?></td>
            </tr>
              <?php  
	} ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="2" valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 id="h_2">Abonos realizados</h2></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
          <td><table id="tb_abonos_reservas" width="100%" border="1" class="display">
            <thead>
              <tr>
                <td align="center"><strong>Fecha</strong></td>
                <td align="center"><strong>Forma de pago</strong></td>
                <td align="center"><strong>Serie recibo</strong></td>
                <td align="center"><strong>No. Recibo</strong></td>
                <td align="center"><strong>No. Rerpote venta</strong></td>
                <td align="center"><strong>Monto</strong></td>
<!--                <td align="center"><strong>Reporte Venta</strong></td>
-->                </tr>
            </thead>
            <tbody>
              <?php
   
$SQL=" SELECT *,
	DATE_FORMAT(movimiento_caja.`FECHA_MOVIMIENTO_CAJA`, '%d-%m-%Y') AS fecha 
FROM `movimiento_caja` 
INNER JOIN `forma_pago_caja` ON (forma_pago_caja.`NO_DOCUMENTO_CAJA`=movimiento_caja.NO_DOCUMENTO_CAJA)
INNER JOIN `formas_pago` ON (formas_pago.`forpago`=forma_pago_caja.forpago)
WHERE 
movimiento_caja.`NO_RESERVA_CAJA`='".$data->no_reserva."'";
 		  
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_object($rs)){
		$id_pago=System::getInstance()->Encrypt(json_encode($row));
 
   ?> 		   <tr>
                <td align="center" class="display"><?php echo $row->fecha;?></td>
                <td align="center" class="display"><?php echo $row->descripcion_pago; ?></td>
                <td align="center" class="display"><?php echo $row->SERIE_DOCUMENTO_CAJA;?></td>
                <td align="center" class="display" ><?php echo $row->NO_DOCUMENTO_CAJA;?></td>
                <td align="center" class="display" ><?php echo $row->NO_RESERVA_CAJA ;?></td>
                <td align="center" class="display" ><?php echo number_format($row->MONTO_PAGO_CAJA,2);?></td>
<!--                <td align="center" class="display" ><a href="./?mod_inventario/reserva/reservar&amp;view_recibo_ventas=1&amp;reserva=<?php echo $_REQUEST['reserva_id'];?>&pago=<?php echo $id_pago;?>" style="text-decoration:none" target="new"><img src="images/document_preview.png" width="30" height="30" /></a></td>
                </tr>-->
 <?php 
	} ?>
            </tbody>
          </table></td>
        </tr>

      </table></td>
      </tr>
    <tr>
      <td colspan="2" align="center" valign="top"> 
         
        <button type="button" class="redButton" id="bt_rs_cancel">Cerrar</button>
&nbsp;</td>
      </tr>
    <tr>
      <td colspan="2" align="center" valign="top">&nbsp;</td>
    </tr>
   </table>
 
  
</div>
</form>