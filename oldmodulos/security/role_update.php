<?php

$eID = $_POST['eID'];
$DecryptID=json_decode(System::getInstance()->getEncrypt()->decrypt($eID,$protect->getSessionID()));


if (!isset($protect)){
	echo "Security error!";
	exit;
}
 
if (isset($_REQUEST['submit']) && isset($_REQUEST['roles'])){
	$retur=array("mensaje"=>"Registro actualizado","error"=>false);
	$roles= new ObjectSQL();
	$roles->Role=$_REQUEST['roles'];

	$SQL=$roles->getSQL("update","Roles"," where Id_role = '".$DecryptID->Id_role."'");
	mysql_query($SQL);
	
	echo json_encode($retur);


exit;

}
?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_update_role_2" id="form_update_role_2" method="post" action="" class="fsForm  fsSingleColumn">
<input name="submit" id="submit" type="hidden" value="1">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Nombre:</td>
    <td>
      <label>
        <input name="roles" type="text" id="roles" value="<?php echo $DecryptID->Role; ?>">
        </label><input type="hidden" name="eID" id="eID" value="<?php echo $eID; ?> "></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttons">   
                      <button type="button" class="positive" id="bt_update">
                        <img src="images/apply2.png" alt=""/> 
                        Actualizar</button>
                      <a href="#" onclick="_reload()" class="negative"><img src="images/cross.png" alt=""/> Cancelar</a>
                  </div></td>
    </tr>
</table>
</div>
</form>