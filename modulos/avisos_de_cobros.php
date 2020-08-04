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
<style type="text/css">
.fsPage {	width:99%;
}
</style>
<div id="inventario_page" class="fsPage">
  <h2 style="color:#FFF;margin-top:0px;">Gestión de Ventas por Asesor</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <?php 

	$SQL=" SELECT * FROM `cierres` ";
	$rs=mysql_query($SQL);
	while($row= mysql_fetch_assoc($rs)){
?>
          <td width="100" height="30" ><strong><a href=".?mod_prospectos/listar&amp;reporte_asesor&amp;type=<?php echo $_REQUEST['type'];?>&amp;p_fecha_desde=<?php echo $row['fecha_inicio_ventas'];?>&amp;p_fecha_hasta=<?php echo $row['fecha_fin_ventas'];?>"><?php echo $MES[$row['mes']].$row['ano'];?></a></strong></td>
          <?php } ?>
          <td align="center">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center"><form method="post" action="./?mod_prospectos/listar&amp;reporte_asesor" >
        <table width="450" border="0" cellspacing="0" cellpadding="0" style="font-size:9px;">
          <tr>
            <td width="98" align="right">PERIODO DE:</td>
            <td><input  name="p_fecha_desde" type="text" class="filter_ textfield date_pick" style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="p_fecha_desde" readonly="readonly" value="<?php echo $fecha_desde;?>" /></td>
            <td><input  name="p_fecha_hasta" type="text" class="filter_ textfield date_pick" id="p_fecha_hasta" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" value="<?php echo $fecha_hasta;?>" readonly="readonly" /></td>
            <td>&nbsp;
              <input type="submit" name="bt_filter" id="bt_filter" value="Filtrar"  class="btn btn-primary bt-sm"  /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form></td>
    </tr>
    <tr>
      <td><ul id="myTab" class="nav nav-tabs">
        <li class="active"><a href="#asesor" data-toggle="tab" >Gestion  Asesor</a></li>
        <li ><a href="#pilar" data-toggle="tab" >Gestion  Pilar</a></li>
      </ul></td>
    </tr>
    <tr>
      <td><div id="myTabContent" class="tab-content">
        <div class="tab-pane fade in active" id="asesor">
          <table width="700" border="0" cellspacing="0" cellpadding="0" class="table table-hover">
            <thead>
              <tr>
                <td><strong>ASESOR</strong></td>
                <td align="center"><strong>TOTAL</strong></td>
                <td align="center"><strong>A</strong></td>
                <td align="center"><strong>B</strong></td>
                <td align="center"><strong>C</strong></td>
                <td align="center"><strong>CITA</strong></td>
                <td align="center"><strong>PRESENTACION</strong></td>
                <td align="center"><strong>CIERRE</strong></td>
                <td align="center"><strong>RESERVAS</strong></td>
              </tr>
            </thead>
            <tbody>
              <?php 



$SQL="SELECT  
	ASESOR AS `ASESOR_VENTAS`, 
	(SUM(`A`)+SUM(`B`)+SUM(`C`)) AS CONTACTO,
	SUM(`A`) AS A,
	SUM(`B`) AS B,
	SUM(`C`) AS C,
	SUM(`CITA`) AS CITA,
	SUM(`PRESENTACION`) AS PRESENTACION,
	SUM(`CIERRE`) AS CIERRE,
	SUM(`RESERVAS`) AS RESERVAS,
	sys_asesor.sys_gerentes_grupos_idgrupos AS id_comercial,
	sys_asesor.idgerente_grupo as id_comercial_gerente
 FROM `cache_listado_prospectos` AS reporte_cache_pilar
 INNER JOIN `sys_asesor` ON (`sys_asesor`.`sys_gerentes_grupos_idgrupos`=reporte_cache_pilar.`id_comercial`)
WHERE 	
	(sys_asesor.`idgerente_grupo`='".$protect->getComercialID()."' 
		AND reporte_cache_pilar.id_comercial!='".$protect->getComercialID()."')
		AND DATE_FORMAT(fecha_inicio, '%Y-%m-%d') BETWEEN '". mysql_escape_string($fecha_desde) ."' AND '". mysql_escape_string($fecha_hasta) ."'  
GROUP BY `ASESOR` ";
    
$rs=mysql_query($SQL);
$CONTACTO=0;
$A=0;
$B=0;
$C=0;
$CITA=0;
$PRE=0;
$CIER=0;
$RESER=0;
while($row= mysql_fetch_assoc($rs)){
	$CONTACTO=$CONTACTO+$row['CONTACTO'];
	$A=$A+$row['A'];
	$B=$B+$row['B'];
	$C=$C+$row['C'];
	$CITA=$CITA+$row['CITA'];
	$PRE=$PRE+$row['PRESENTACION'];	
	$CIER=$CIER+$row['CIERRE'];	
	$RESER=$RESER+$row['RESERVAS'];	
	
	$inf=array(
			"back_url"=>"?mod_prospectos/listar&reporte_asesor&type=&p_fecha_desde=".$fecha_desde."&p_fecha_hasta=".$fecha_hasta,
			"nombre"=>$row['ASESOR_VENTAS'],
			"type"=>"TOTAL",
			"id_comercial"=>$row['id_comercial'],
			"id_comercial_gerente"=>$row['id_comercial_gerente'],
			'fecha_desde'=>$fecha_desde,
			'fecha_hasta'=>$fecha_hasta,
			'ultima_actividad'=>''
			);
				
?>
              <tr >
                <td  class="asesor_detalle"  style="cursor:pointer"  id="<?php echo base64_encode(json_encode($inf));?>"><?php echo $row['ASESOR_VENTAS'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php echo base64_encode(json_encode($inf));?>"><?php echo $row['CONTACTO'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='A'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['A']; ?></td>
                <td align="center" style="cursor:pointer"  id="<?php 
						  $inf['type']='B'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php echo $row['B'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta"  id="<?php 
						  $inf['type']='C'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['C'];?></td>
                <td align="center" style="cursor:pointer"  id="<?php 
						  $inf['ultima_actividad']='CITA'; 
						  $inf['type']='TOTAL'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php 
						   echo $row['CITA'];?></td>
                <td align="center" style="cursor:pointer"  id="<?php 
						 	$inf['ultima_actividad']='PRE'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php  
						   echo $row['PRESENTACION'];?></td>
                <td align="center" style="cursor:pointer"  id="<?php 
						  $inf['ultima_actividad']='CIE'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php 
						    echo $row['CIERRE'];?></td>
                <td align="center" style="cursor:pointer"  id="<?php 
						 $inf['ultima_actividad']='RES'; 
						  echo base64_encode(json_encode($inf));?>"  class="total_cuenta"><?php 
						  
						  echo $row['RESERVAS'];?></td>
              </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td>&nbsp;</td>
                <td align="center"><strong>TOTAL</strong></td>
                <td align="center"><strong>A</strong></td>
                <td align="center"><strong>B</strong></td>
                <td align="center"><strong>C</strong></td>
                <td align="center"><strong>CITA</strong></td>
                <td align="center"><strong>PRESENTACION</strong></td>
                <td align="center"><strong>CIERRE</strong></td>
                <td align="center"><strong>RESERVAS</strong></td>
              </tr>
              <tr>
                <td><strong>TOTALES DEL PERIODO <?php echo date("d/m/Y",strtotime($fecha_desde));?> AL <?php echo date("d/m/Y",strtotime($fecha_hasta));;?></strong></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']); 
						  $inf['id_comercial_gerente']=$protect->getComercialID();
						  echo base64_encode(json_encode($inf));?>"><?php echo $CONTACTO;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="A";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $A;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="B";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $B;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="C";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $C;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CITA'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CITA;?></td>
                <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='PRE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $PRE;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CIE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CIER;?></td>
                <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='RES'; 						 
						  echo base64_encode(json_encode($inf));?>"><?php echo $RESER;?></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="tab-pane fade in" id="pilar">
          <table width="700" border="0" cellspacing="0" cellpadding="0" class="table table-hover">
            <thead>
              <tr>
                <td><strong>PILAR</strong></td>
                <td align="center"><strong>TOTAL</strong></td>
                <td align="center"><strong>A</strong></td>
                <td align="center"><strong>B</strong></td>
                <td align="center"><strong>C</strong></td>
                <td align="center"><strong>CITA</strong></td>
                <td align="center"><strong>PRESENTACION</strong></td>
                <td align="center"><strong>CIERRE</strong></td>
                <td align="center"><strong>RESERVAS</strong></td>
              </tr>
            </thead>
            <tbody>
              <?php 

$SQL="SELECT  
	pilar_inicial as `PILAR`, 
	(SUM(`A`)+SUM(`B`)+SUM(`C`)) AS CONTACTO,
	SUM(`A`) AS A,
	SUM(`B`) AS B,
	SUM(`C`) AS C,
	SUM(`CITA`) AS CITA,
	SUM(`PRESENTACION`) AS PRESENTACION,
	SUM(`CIERRE`) AS CIERRE,
	SUM(`RESERVAS`) AS RESERVAS
 FROM cache_listado_prospectos as `reporte_cache_pilar`
WHERE (reporte_cache_pilar.id_comercial_gerente='".$protect->getComercialID()."' AND reporte_cache_pilar.id_comercial!='".$protect->getComercialID()."') and  DATE_FORMAT(fecha_inicio, '%Y-%m-%d')  BETWEEN '". mysql_escape_string($fecha_desde) ."' AND '". mysql_escape_string($fecha_hasta) ."'  
  GROUP BY `pilar_inicial` ";
   
$rs=mysql_query($SQL);
$CONTACTO=0;
$A=0;
$B=0;
$C=0;
$CITA=0;
$PRE=0;
$CIER=0;
$RESER=0;
while($row= mysql_fetch_assoc($rs)){
	$CONTACTO=$CONTACTO+$row['CONTACTO'];
	$A=$A+$row['A'];
	$B=$B+$row['B'];
	$C=$C+$row['C'];
	$CITA=$CITA+$row['CITA'];
	$PRE=$PRE+$row['PRESENTACION'];	
	$CIER=$CIER+$row['CIERRE'];	
	$RESER=$RESER+$row['RESERVAS'];	
	
		$inf=array(
			"type"=>"TOTAL",
			"id_comercial_gerente"=>$protect->getComercialID(),
			'fecha_desde'=>$fecha_desde,
			'fecha_hasta'=>$fecha_hasta,
			'ultima_actividad'=>'',
			'PILAR'=>''
			);			
?>
              <tr>
                <td><?php echo $row['PILAR'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php
				   $inf['PILAR']=$row['PILAR']; 
				   echo base64_encode(json_encode($inf));?>"><?php echo $row['CONTACTO'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='A'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['A'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='B'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['B'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['type']='C'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['C'];?></td>
                <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
				  		  $inf['type']='TOTAL'; 
						  $inf['ultima_actividad']='CITA'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['CITA'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['ultima_actividad']='PRE'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['PRESENTACION'];?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['ultima_actividad']='CIE'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['CIERRE'];?></td>
                <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  $inf['ultima_actividad']='RES'; 
						  echo base64_encode(json_encode($inf));?>"><?php echo $row['RESERVAS'];?></td>
              </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td>&nbsp;</td>
                <td align="center"><strong>TOTAL</strong></td>
                <td align="center"><strong>A</strong></td>
                <td align="center"><strong>B</strong></td>
                <td align="center"><strong>C</strong></td>
                <td align="center"><strong>CITA</strong></td>
                <td align="center"><strong>PRESENTACION</strong></td>
                <td align="center"><strong>CIERRE</strong></td>
                <td align="center"><strong>RESERVAS</strong></td>
              </tr>
              <tr>
                <td><strong>TOTALES DEL PERIODO <?php echo date("d/m/Y",strtotime($fecha_desde));?> AL <?php echo date("d/m/Y",strtotime($fecha_hasta));;?></strong></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['PILAR']);
							 
							 unset($inf['ultima_actividad']); 
						  $inf['id_comercial_gerente']=$protect->getComercialID();
						  echo base64_encode(json_encode($inf));?>"><?php echo $CONTACTO;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="A";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $A;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="B";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $B;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						 	 unset($inf['id_comercial']);
							 unset($inf['ultima_actividad']);
						  $inf['type']="C";
 						  echo base64_encode(json_encode($inf));?>"><?php echo $C;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CITA'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CITA;?></td>
                <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='PRE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $PRE;?></td>
                <td align="center" style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='CIE'; 
 						  echo base64_encode(json_encode($inf));?>"><?php echo $CIER;?></td>
                <td align="center"  style="cursor:pointer"  class="total_cuenta" id="<?php 
						  unset($inf['id_comercial']);
						  unset($inf['type']);						  
						  $inf['ultima_actividad']='RES'; 						 
						  echo base64_encode(json_encode($inf));?>"><?php echo $RESER;?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div></td>
    </tr>
  </table>
</div>
