<?php
$_PATH="";
include("includes/protect.php");

if (isset($_REQUEST['tipo'])){
	$mqtt= new MQTTConection("m3morial1/comunicator");
	if (isset($_REQUEST['accion']) && isset($_REQUEST['capilla'])){
		$capilla=$_REQUEST['_capilla'];
		$accion=$_REQUEST['accion'];
		$device=array();
		foreach($capilla as $key=>$val){
			array_push($device,$val);
		//	echo $val;
		}  
	 
		$data=array("device"=>$device,
					"info"=>getNewAction($accion));
  		$mqtt->send2("comunicator/mensaje",json_encode($data));	
		//$mqtt->send2("comunicator/mensaje","fdsad");	
		echo "Ejecutado ".$accion;
	}
}
 //$rs=getNewAction();
 //print_r($rs);
 


function getNewAction($id){
	$SQL="SELECT *,
			dt.id AS ctemp 
	FROM capillas_devices_template as dt
	WHERE  
 		 dt.id='".$id."' ";
//27,28
	$rs=mysql_query($SQL);
	$dt=array("valid"=>"0","data"=>array());
	while($row=mysql_fetch_assoc($rs)){  
		$dt['data']=$row; 
		$dt['valid']="1"; 
	} 
	return $dt;
} 

?>
<html>
<head>
</head>
<script type="text/javascript" src="script/jquery/jquery-1.9.1.js"></script>
<script type="text/javascript" src="script/jquery.cookie.js"></script>
<script type="text/javascript" src="script/mosquitto-1.1.min.js"></script>
<script type="text/javascript" src="script/Class.js"></script>
<script type="text/javascript" src="script/Class.MemorialMQTT.js"></script>
<script type="text/javascript" src="script/select2.min.js"></script>
<link type="text/css" href="css/select2-bootstrap.css" rel="stylesheet"/>
<link type="text/css" href="css/select2.css" rel="stylesheet"/>

<script>
var mqtt=null;
$(function(){
	mqtt= new MemorialMQTT();
/*	mqtt.connect();
	mqtt.addListener("incomingData",function(data){
	//	$('#mensajes').after('<tr><td>'+data+'</td></tr>');
	});*/
	var capilla="";
	
	var accion ="";
	$("#accion").select2(); 
	$("#accion").on("change",function(e) { 
		accion=e.val;
	});	
		
	
	$("#bottom_").click(function(){ 

	});	
	
})

function enviar(){ 
	$.post("./mqtt.php",{"capilla":capilla,"accion":accion,"tipo":true},function(){
		alert('fdsad');
	},"text");	
}
</script>
<title>TEST</title>
<body>
<form name="form1" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>CAPILLA</td>
        <td>&nbsp;</td>
        <td>ACCION</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><select name="_capilla[]" multiple="multiple" id="_capilla[]" size="10" style="width:300px">
          <?php 
			$SQL="SELECT * FROM `capillas_devices`  ";
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_array($rs)){
 					?>
          <option value="<?php echo $row['device_id'] ?>"><?php echo utf8_encode($row['device_descripcion']);?></option>
          <?php } ?>
        </select>
          <input type="hidden" name="capilla" id="capilla"></td>
        <td>&nbsp;</td>
        <td valign="top"><select name="accion" id="accion" style="height:50px; width:400px">
          <?php 
			$SQL="SELECT * FROM `capillas_devices_template`    ";
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_array($rs)){
				print_r($row);
					?>
          <option value="<?php echo $row['id']?>"><?php echo $row['id']." ".$row['nombre_archivo']." ".$row['tipo']." ".$row['objeto'];?></option>
          <?php } ?>
        </select></td>
      </tr>
      <tr>
        <td>:</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="3" align="center">  
          
          <input type="submit" name="button" id="button" value="Submit">
          <input type="hidden" name="tipo" id="tipo" value="true">        </td>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mensajes">
  <tr>
    <td align="center"><strong>MENSAJES LLEGADOS</strong></td>
  </tr>
  
</table>

</body>
</html>
