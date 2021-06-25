<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (!isset($_REQUEST['request'])){
	echo "error";	
	exit;
}

$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['request'],$protect->getSessionID());
$data=json_decode($dencryt);

if (isset($_REQUEST['submit'])){
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	$Pantallas= new ObjectSQL();
	$Pantallas->Pantalla=$_REQUEST['Pantalla'];
	$Pantallas->URL=$_REQUEST['URL'];
	$SQL=$Pantallas->getSQL("update","Pantallas"," where id_pantalla='".$data->id_pantalla."'");
	mysql_query($SQL);
	$retur['mensaje']="Registro actualizado!";
	$retur['error']=false;
	echo json_encode($retur);
	exit;
} 



?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_pantallas" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Nombre:</td>
    <td><label>
      <input name="Pantalla" type="text" id="Pantalla" value="<?php echo $data->Pantalla?>" />
      </label>    </td>
  </tr>
  <tr>
    <td>Modulo:</td>
    <td><label>
      <input name="URL" type="text" id="URL"  value="<?php echo $data->URL?>" />
      </label>   <input name="request" type="hidden" id="request" value="<?php echo $_REQUEST['request']?>" />  <input name="submit" type="hidden" id="submit" value="submit" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#" onclick="_reload()" class="negative"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table>
</div>
</form>