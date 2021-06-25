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
 
if (!(isset($data->id_usuario))){
	echo "error";	
	exit;
}

if (isset($_REQUEST['submit'])){
//	print_r($_REQUEST);
//	print_r($data);
	//print_r($data->Contrasena. " - ". md5($_REQUEST['old_password']));
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	
	if ((trim($_REQUEST['password'])=='') || (trim($_REQUEST['password_repeat'])=='')){
		$retur['mensaje']="La contraseña no debe de estar en blanco!";
		$retur['error']=true;
		echo json_encode($retur);
		exit;	
	}
	
	if ($_REQUEST['password']==$_REQUEST['password_repeat']){
		
		//if ($data->Contrasena==md5($_REQUEST['password'])){
		$obj= new ObjectSQL();
	//	$obj->email=$_REQUEST['usuario'];
		$obj->Contrasena=md5($_REQUEST['password']);
		$SQL=$obj->getSQL("update","Usuarios"," where id_usuario='".$data->id_usuario."'");
		mysql_query($SQL);
		
		
		$dencryt=System::getInstance()->Decrypt($_REQUEST['Roles']);
		$role=json_decode($dencryt);

		$obj= new ObjectSQL();
		$obj->id_role=$role->Id_role;
		$SQL=$obj->getSQL("update","usu_role"," where id_usuario='".$data->id_usuario."'");
		mysql_query($SQL);		
		
		$retur['mensaje']="Registro actualizado correctamente! ";
		$retur['error']=false;
		echo json_encode($retur);
		exit;
		/*}else{
			$retur['mensaje']="La contraseña anterior no coincide ";
			$retur['error']=true;	
		}*/
 
	}else{
		$retur['mensaje']="Las contraseñas no coinciden";
		$retur['error']=true;	
	}
	

	
	echo json_encode($retur);
	exit;
}


?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_user_edit" id="form_user_edit" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Usuario:</td>
    <td><label>
      <input name="usuario" type="text" id="usuario" value="<?php echo $data->email?>"  disabled="disabled"  autocomplete="off" />
      </label>    </td>
  </tr>
  <tr>
    <td>Contraseña nueva:</td>
    <td><input name="password" type="password" id="password" autocomplete="off" /></td>
  </tr>
  <tr>
    <td>Contraseña repita:</td>
    <td><input name="password_repeat" type="password" id="password_repeat" /></td>
  </tr>
  <tr>
    <td>Grupo Permisos</td>
    <td><select name="Roles" id="Roles"  class="textfield textfieldsize"  style="height:30px;">
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `Roles`";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
 
	  ?>
      <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>" <?php echo $data->id_role==$row['Id_role']?' selected="selected"':''?>><?php echo $row['Role'];?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><input name="request" type="hidden" id="request" value="<?php echo $_REQUEST['request']?>" />
      <input name="submit" type="hidden" id="submit" value="submit" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#"  class="negative" id="bt_cancel"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table>
</div>
</form>