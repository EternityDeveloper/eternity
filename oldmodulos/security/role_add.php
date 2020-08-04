<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (isset($_REQUEST['submit']) && isset($_REQUEST['roles'])){
	//print_r($_REQUEST);
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	$roles= new ObjectSQL();
	$roles->Role=$_REQUEST['roles'];
	$SQL=$roles->getSQL("insert","Roles");
	mysql_query($SQL);
	if (mysql_insert_id($protect->getDBLink()->link_id)>0){
		$retur['mensaje']="Registro insertado correctamente!";
		$retur['error']=false;
		echo json_encode($retur);
	}else{
		echo json_encode($retur);
	}
	exit;
} 



?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_roles" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Nombre:</td>
    <td>
      <label>
        <input name="roles" type="text" id="roles">
        </label>    </td>
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