<?php // error_reporting(-1); error_reporting(E_ALL);
 //ini_set("display_errors", 1);	
$_PATH="../";
include("../includes/config.inc.php"); 
include("../includes/function.php"); 
include($_PATH."class/lib/class.userAccess.php");
include($_PATH."class/lib/class.ObjectSQL.php"); 
include($_PATH."class/lib/excel/class_excel.php"); 
include($_PATH."class/lib/Database.class.php"); 
 
$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect();  
$db_link = $db->link_id;



$ext=array(
	'DURENA'=>'224',
	'LPENA'=>'225',
	'THERNANDEZ'=>'223',
	'NSANTOS'=>'227'
);

/*
Dileysi 224
Leidy 225
Tania Hernandez 223
Nathalie 227
*/

if (isset($_REQUEST['button'])){
 if ($_REQUEST['button']=="Generar CSV"){
	$mid_excel = new MID_SQLPARAExel;
	 
	$sql = "SELECT * FROM asesor_reagenda";
	 
	$mid_excel->mid_sqlparaexcel("", "alunos", $sql, "listado"); 
	exit;
 }
}




function getTimeIncall($oficial,$date_from,$date_to){
	$SQL="SELECT COUNT(*) AS total,SUM(`time_in_call`) AS minutos,is_call FROM `asesor_reagenda` 
		WHERE fecha_reagenda between '".$date_from."' and '".$date_to."' and oficial='".$oficial."' GROUP BY is_call "; 
	$rs=mysql_query($SQL);  
	$data=array();
	while($row= mysql_fetch_assoc($rs)){	
		if ($row['is_call']==0){
			$data['no_call']=$row;
		}else{
			$data['call']=$row;
		}
	}
	return $data;
}

function getDateGestion($mysql_db){
	$SQL="SELECT fecha_reagenda FROM `asesor_reagenda` GROUP BY fecha_reagenda order by fecha_reagenda desc  ";
	$rs=mysql_query($SQL,$mysql_db);
	$data=array();
	while($row=mysql_fetch_assoc($rs)){
		array_push($data,$row);
	}
 	return $data;
}

 
function getTimeIncallActual($db_elaxtix,$oficial,$date_from,$date_to){


	$SQL="SELECT SUM(duration) AS duraction, 
				 src AS ext 
		FROM `cdr` WHERE calldate between '".$date_from." 00:00:00' and '".$date_to." 23:59:59'
		and src='".$oficial."' GROUP BY src "; 
	//echo $SQL;
	$rs=mysql_query($SQL,$db_elaxtix);
  
	$data=0;
	while($row= mysql_fetch_assoc($rs)){	
		$data=$row['duraction'];
		print_r($row);
	}
	return $data;
}

?>
<div style="padding-left:20px;">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center"><form id="form1" name="form1" method="post" action="">
        <input name="value" type="hidden" id="value" value="1" />
        <input type="submit" name="button" id="button" value="Generar CSV" />
      </form></td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
<?php

$data= getDateGestion($db_link);
foreach($data as $key =>$val){ 

?>  
    <tr>
      <td height="50" align="center" bgcolor="#0066CC" style="color:#FFF;font-size:22px;"><strong><?php echo $val['fecha_reagenda'];?></strong></td>
    </tr>
    <tr>
      <td width="600"><table width="561" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" bgcolor="#CCCCCC"><strong>RESUMEN</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
<table width="561" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-bordered">
  <?php

//$day_from='2014-05-22';
//$day_to='2014-05-23';
$SQL="SELECT oficial FROM asesor_reagenda  WHERE oficial!=3 GROUP BY oficial"; 
$rs=mysql_query($SQL,$db_link);  
while($row= mysql_fetch_assoc($rs)){	

	$data_oficial=getTimeIncall($row['oficial'],$val['fecha_reagenda'],$val['fecha_reagenda']);
 
	//print_r($data_oficial);
?>
  <tr>
          <td align="center" bgcolor="#CCCCCC">&nbsp;<strong><?php echo $row['oficial']?></strong></td>
          </tr>
        <tr>
          <td height="40" align="center"><table width="561" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="237" align="center"><strong>#Cartera de Oficial (Por Gestionar)</strong></td>
              <td width="162" align="center" bgcolor="#0099CC"><strong>#Cartera Gestionada</strong></td>
              <td width="162" align="center"><strong>Total de llamadas Realizadas</strong></td>
              </tr>
            <tr>
              <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center">&nbsp;<?php echo $data_oficial['no_call']['total']?></td>
                  </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                  </tr>
                </table></td>
              <td align="center" bgcolor="#0099CC"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" bgcolor="#FFFFFF"><strong>Gestionado</strong></td>
                  <td align="center" bgcolor="#FFFFFF"><strong>Minutos</strong>&nbsp;</td>
                  </tr>
                <tr>
                  <td align="center" bgcolor="#FFFFFF"><?php echo $data_oficial['call']['total']?></td>
                  <td align="center" bgcolor="#FFFFFF"><?php echo gmdate("H:i:s", $data_oficial['call']['minutos']);?></td>
                  </tr>
                </table></td>
              <td align="center"><?php //echo getTimeIncallActual($db_elaxtix,$ext[trim($row['oficial'])],$val['fecha_reagenda'],$val['fecha_reagenda']);?></td>
              </tr>
            </table></td>
          </tr>
        <?php } ?>
    </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
 <?php } ?>
  </table>
</div>