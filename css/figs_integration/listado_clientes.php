<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover" >
  <thead>
    <tr style="background-color:#999;height:20px;">
      <td bgcolor="#999999">CONTRATO</td>
      <td bgcolor="#999999">NOMBRE CLIENTE</td>
      <td bgcolor="#999999">ASESOR</td>
    </tr>
  </thead>
  <tbody>
    <?php

$SQL="select TOP 10 VIEW_AUDITORES.NOMBRE_CLIENTE,
	VIEW_AUDITORES.CONTRATO,
	VIEW_AUDITORES.CLI_TLF_1,VIEW_AUDITORES.CLI_TLF_2,
	VIEW_AUDITORES.CLI_TLF_3,
	VIEW_AUDITORES.PRODUCTO,
	VIEW_AUDITORES.ASESOR,
	VIEW_AUDITORES.CTT_DIRECCION  from 
	VIEW_AUDITORES where   VIEW_AUDITORES.CTT_COD_TD in ('CO','CM','CT') AND
	 VIEW_AUDITORES.ASESOR in (select MAE_TRABAJADORES.TRA_DESCRIPCION from 
	 MAE_TRABAJADORES where MAE_TRABAJADORES.TRA_COD_SUPERVISOR =784)  "; 	
	
$rsx=mssql_query($SQL,$ms_db); 
$data=array(
	"valid"=>false,
	"data"=>array()
);
while($row=mssql_fetch_assoc($rsx)){
?>
    <tr>
      <td><?php echo $row['CONTRATO'];?></td>
      <td height="16"><?php echo $row['NOMBRE_CLIENTE'];?></td>
      <td><?php echo $row['ASESOR'];?></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </tfoot>
</table>
</body>
</html>