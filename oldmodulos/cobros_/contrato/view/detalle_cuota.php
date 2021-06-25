<?php 
if (!isset($protect)){
	exit;	
}  
SystemHtml::getInstance()->includeClass("contratos","Contratos");  
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 


$cobros= new Cobros($protect->getDBLINK());  
$info=$cobros->getItem($_REQUEST['token']); 
//$monto=$cobros->getItemMontoACobrar($_REQUEST['token']); 

$monto=0;
foreach($info as $key =>$val){
	$monto=$monto+$val->monto_neto;	
}
 
$total=count($info);

$data=array("monto"=>$monto, "no_cuota"=>$total);
$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
 
$_contratos=new Contratos($this->db_link);
$d_contrato=$_contratos->getInfoContrato($ct->serie_contrato,$ct->no_contrato);
 
$interes=($data['monto']*$d_contrato->porc_interes/100);
$capital=$data['monto']-$interes;
 
//$monto=$interes+$capital; 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
  <tr>
    <td align="center"><strong>CANT. CUOTAS</strong></td>
    <td align="center"><strong>CAPITAL</strong></td>
    <td align="center"><strong>INTERESES</strong></td>
    <td align="center"><strong>MORA</strong></td>
    <td align="center"><strong>MONTO</strong></td>
  </tr>
  <tr>
    <td align="center"><?php echo $data['no_cuota'];?></td>
    <td align="center"><?php echo number_format($capital,2);?></td>
    <td align="center"><?php echo number_format($interes,2);?></td>
    <td align="center">0</td>
    <td align="center"><?php echo number_format($monto,2);?></td>
  </tr>
</table>
