<?php
if (!isset($protect)){
	exit;
}	


$MES=array(
	"1"=>'ENE',
	"2"=>'FEB',
	"3"=>'MAR',
	"4"=>'ABR',
	"5"=>'MAY',
	"6"=>'JUN',
	"7"=>'JUL',
	"8"=>'AGO',
	"9"=>'SEP',
	"10"=>'OC',
	"11"=>'NOV',
	"12"=>'DIC'
	);

$SQL=" SELECT * FROM `cierres` where mes ='".date("m")."' and ano=2015 ";

$rs=mysql_query($SQL);
$row= mysql_fetch_assoc($rs);
 
$day_from 	=	isset($_REQUEST['day_from'])?date("Y-m-d", strtotime($_REQUEST['day_from'])):$row['fecha_inicio_ventas'];
$day_to		=	isset($_REQUEST['day_to'])?date("Y-m-d", strtotime($_REQUEST['day_to'])):$row['fecha_fin_ventas'];	

 

?>
<table width="100%" border="0" class="header_day">

  <tr>
    <td height="30" colspan="2" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <?php 

	$SQL=" SELECT * FROM `cierres` WHERE ano=2015";
	$rs=mysql_query($SQL);
	while($row= mysql_fetch_assoc($rs)){
?>
        <td width="100" height="30" ><strong><a href=".?mod_reportes/report&type=<?php echo $_REQUEST['type'];?>&amp;day_from=<?php echo $row['fecha_inicio_ventas'];?>&amp;day_to=<?php echo $row['fecha_fin_ventas'];?>"><?php echo $MES[$row['mes']].$row['ano'];?></a></strong></td>
        <?php } ?>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="30" colspan="2" align="center">
<form method="post" >    
    <table width="400" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><strong>PERIODO</strong></td>
        <td><input type="text" name="day_from" id="day_from" class="form-control textfield"   value="<?php echo date("d-m-Y", strtotime($day_from))?>" /></td>
        <td><input type="text" name="day_to" id="day_to" class="form-control textfield"  value="<?php echo date("d-m-Y", strtotime($day_to))?>" /></td>
        <td><button type="submit" class="greenButton" id="filtrar">Filtrar</button></td>
      </tr>
    </table>
</form>   
    </td>
    </tr>
  <tr>
    <td height="30" colspan="2" align="center">&nbsp;</td>
  </tr>
</table>
