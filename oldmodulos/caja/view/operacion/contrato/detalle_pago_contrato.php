<?php 
if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("caja","Caja");
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 

   
$caja=new Caja($protect->getDBLink());
$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
$data=$caja->getCuotaContratoActual($ct->no_contrato,$ct->serie_contrato);	

$_contratos=new Contratos($this->db_link);
$d_contrato=$_contratos->getInfoContrato($ct->serie_contrato,$ct->no_contrato);

//print_r($d_contrato->valor_cuota);
$interes=($d_contrato->valor_cuota*$d_contrato->porc_interes/100);
$capital=$d_contrato->valor_cuota-$interes;
 
$monto=$interes+$capital;

?><table width="600" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100" align="center" valign="middle"><strong>CANCELA</strong></td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
      <tr>
        <td align="center"><strong>NO. CUOTA</strong></td>
        <td align="center"><strong>CAPITAL</strong></td>
        <td align="center"><strong>INTERESES</strong></td>
        <td align="center"><strong>MORA</strong></td>
        <td align="center"><strong>MONTO</strong></td>
      </tr>
      <tr>
        <td align="center"><?php echo $data['NO_CUOTA']+1;?></td>
        <td align="center"><?php echo number_format($capital,2);?></td>
        <td align="center"><?php echo number_format($interes,2);?></td>
        <td align="center">0</td>
        <td align="center"><?php echo number_format($monto,2);?></td>
      </tr>
    </table></td>
  </tr>
</table>
