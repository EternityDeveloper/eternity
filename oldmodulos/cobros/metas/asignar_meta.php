<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}

if (!isset($_REQUEST['request'])){
	exit;
}
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobro= new Cobros($protect->getDBLink()); 

$cdata=json_decode(base64_decode($_REQUEST['request']));
$cartera=array();
foreach($cdata as $key=>$_row){
	$cartera[$_row->name]=$_row->value;
}

$meta=$cobro->filterMetaCar($cartera); 
 
$monto_a_cobrar=0;
$total_contratos=0;

foreach($meta as $key =>$val){
	$monto_a_cobrar+=$val['monto_acobrar'];
	$total_contratos++;
}
 
?><div style="background:#FFF">
<form method="post" action="./?mod_cobros/delegate&metas" > 
  <table width="100%" border="0" cellspacing="0" cellpadding="0"> 
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" valign="top"><table width="450" border="1" cellpadding="5"  style="border-spacing:8px;">
              <tr >
                <td align="left" >&nbsp;</td>
                <td width="3">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr >
                <td width="130" align="right" ><strong>OFICIAL:</strong></td>
                <td>&nbsp;</td>
                <td><input  name="txt_oficial" type="hidden" class="required" id="txt_oficial" style="width:300px;" /></td>
              </tr>
              <tr >
                <td align="right" ><strong>MONTO COBRAR:</strong></td>
                <td>&nbsp;</td>
                <td><strong><?php echo number_format($monto_a_cobrar,2);?></strong></td>
              </tr>
              <tr >
                <td width="100" align="right" ><strong>TOTAL CLIENTE:</strong></td>
                <td>&nbsp;</td>
                <td><strong><?php echo $total_contratos;?></strong></td>
              </tr>
              <tr >
                <td colspan="3" align="center" >&nbsp;</td>
              </tr>
              <tr >
                <td colspan="3" align="center" ><input type="button" name="bt_asignar_create" id="bt_asignar_create" class="btn btn-primary bt-sm" value="ASIGNAR" /></td>
              </tr>
              <tr >
                <td colspan="3" align="center" >&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table> 
</form>
</div>