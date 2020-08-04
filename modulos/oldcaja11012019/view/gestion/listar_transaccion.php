<?php 
if (!isset($protect)){
	exit;	
} 
?><table style="font-size:12px;border-spacing:1px;width:250px;" class="search_list table table-striped table-hover">
  <thead>
    <tr  style="background-color:#CCC;height:30px;" >
      <td width="208" align="center"><strong>ITEM</strong></td>
      <td width="276" align="center"><strong>MONTO</strong></td>
      <td width="276" align="center">&nbsp;</td>
    </tr>
  </thead>
  <tbody>
<?php
$data=STCSession::GI()->isSubmit($_REQUEST['rand']);
$i=0;
$monto=0;
if (count($data)>0){
foreach($data as $key=>$valor_cuota){
	$i++;
	$monto=$monto+$valor_cuota;
?> 
    <tr>
      <td align="center"><?php echo $key+1;?></td>
      <td align="center"><?php echo number_format($valor_cuota,2);?></td>
      <td align="center"><img src="images/cross.png" style="cursor:pointer" id="<?php echo $key;?>" class="trans_remove_item" width="16" height="16"></td>
    </tr>
<?php } 
}?>
    <tr>
      <td align="center"><strong>TOTAL</strong></td>
      <td align="center"><strong><?php echo number_format($monto,2);?></strong></td>
      <td align="center">&nbsp;</td>
    </tr>
  </tbody>
</table>