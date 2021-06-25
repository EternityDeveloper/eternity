<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}

 
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobro= new Cobros($protect->getDBLink()); 
 
 
$monto_a_cobrar=0;
$total_contratos=0;
 
 
?><div style="background:#FFF">
<form method="post" action="./?mod_cobros/delegate&metas" > 
  <table width="100%" border="0" cellspacing="0" cellpadding="0"> 
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" valign="top"><table width="100%" border="1" cellpadding="5"  style="border-spacing:8px;">
              <tr >
                <td align="left" ><strong>DISTRIBUCION:</strong></td>
              </tr>
              <tr >
                <td align="center" ><table id="list_cartera" width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-bordered"  style="font-size:12px;border-spacing:1px;">
                  <thead>
                    <tr style="background-color:#CCC;height:30px;">
                      <td align="center"><strong>OFICIAL</strong></td>
                      <td width="276" align="center"><strong>NO. CONTRATO</strong></td>
                      <td width="276" align="center"><strong>NO. CLIENTES</strong></td>
                      <td width="276" align="center"><strong>NO. CUOTAS A COBRAR</strong></td>
                      <td width="276" align="center"><strong>MONTO A COBRAR</strong></td>
                      </tr>
                  </thead>
                  <tbody>
                    <?php
 
$meta_asignada=$cobro->getCAsignadaByOficial();
$cuotas_acobrar=0;
$valor_cuota=0;
$monto_acobrar=0;
$monto_pendiente=0;
$monto_futuro=0; 
$total_cliente=0;
$total_contratos=0;
foreach($meta_asignada as $key=>$_row){
	$row=$meta_asignada[$key];
	$cuotas_acobrar=$cuotas_acobrar+$row['cuotas_acobrar'];
	$valor_cuota=$valor_cuota+$row['monto_neto'];
	$monto_acobrar=$monto_acobrar+$row['monto_acobrar'];
	$monto_pendiente=$monto_pendiente+$row['monto_pendiente'];
	$monto_futuro=$monto_futuro+$row['monto_futuro'];	
	$total_cliente=$row['total_clientes'];
	$total_contratos=$row['total_contratos']; 
	 
?>
                    <tr class="list_contract_cartera">
                      <td width="400" align="center"><?php  echo $row['nombre_oficial_asing'];?></td>
                      <td align="center"><?php  echo $row['total_contratos'];?></td>
                      <td align="center"><?php  echo $row['total_clientes'];?></td>
                      <td align="center"><?php  echo $row['cuotas_acobrar'];?></td>
                      <td align="center"><?php  echo number_format($row['monto_acobrar'],2);?></td>
                      </tr>
                    <?php } ?>
                    <tr >
                      <td align="center"><strong>TOTALES</strong></td>
                      <td align="center"><strong><?php echo $total_cliente;?></strong></td>
                      <td align="center"><strong><?php echo $total_contratos;?></strong></td>
                      <td align="center"><strong><?php echo $cuotas_acobrar;?></strong></td>
                      <td align="center"><strong><?php echo number_format($monto_acobrar,2);?></strong></td>
                      </tr>
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table></td>
              </tr>
              <tr >
                <td width="133" align="center" ><table id="list_cartera4" width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-bordered"  style="font-size:12px;border-spacing:1px;">
                  <thead>
                    <tr style="background-color:#CCC;height:30px;">
                      <td width="450" align="center"><strong>MOTORIZADO</strong></td>
                      <td width="276" align="center"><strong>NO. CONTRATO</strong></td>
                      <td width="276" align="center"><strong>NO. CLIENTES</strong></td>
                      <td width="276" align="center"><strong>NO. CUOTAS A COBRAR</strong></td>
                      <td width="276" align="center"><strong>MONTO A COBRAR</strong></td>
                      </tr>
                  </thead>
                  <tbody>
                    <?php
 
$meta_asignada=$cobro->getCAsignadaByMotorizado();
$cuotas_acobrar=0;
$valor_cuota=0;
$monto_acobrar=0;
$monto_pendiente=0;
$monto_futuro=0; 
$total_cliente=0;
$total_contratos=0;

foreach($meta_asignada as $key=>$_row){
	$row=$meta_asignada[$key];
	$cuotas_acobrar=$cuotas_acobrar+$row['cuotas_acobrar'];
	$valor_cuota=$valor_cuota+$row['monto_neto'];
	$monto_acobrar=$monto_acobrar+$row['monto_acobrar'];
	$monto_pendiente=$monto_pendiente+$row['monto_pendiente'];
	$monto_futuro=$monto_futuro+$row['monto_futuro'];	
	$total_cliente=$row['total_clientes'];
	$total_contratos=$row['total_contratos'];
	
	
?>
                    <tr class="list_contract_cartera">
                      <td align="center"><?php  echo $row['nombre_motorizado'];?></td>
                      <td align="center"><?php  echo $row['total_contratos'];?></td>
                      <td align="center"><?php  echo $row['total_clientes'];?></td>
                      <td align="center"><?php  echo $row['cuotas_acobrar'];?></td>
                      <td align="center"><?php  echo number_format($row['monto_acobrar'],2);?></td>
                      </tr>
                    <?php } ?>
                    <tr class="list_contract_cartera">
                      <td align="center"><strong>TOTALES</strong></td>
                      <td align="center"><strong><?php echo $total_cliente;?></strong></td>
                      <td align="center"><strong><?php echo $total_contratos;?></strong></td>
                      <td align="center"><strong><?php echo $cuotas_acobrar;?></strong></td>
                      <td align="center"><strong><?php echo number_format($monto_acobrar,2);?></strong></td>
                      </tr>
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table></td>
              </tr>
              <tr >
                <td align="center" ><input type="button" name="bt_asignar_create" id="bt_asignar_create" class="btn btn-primary bt-sm" value="GENERAR" /></td>
              </tr>
              <tr >
                <td align="center" >&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table> 
</form>
</div>