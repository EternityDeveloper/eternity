<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}


if (validateField($_REQUEST,"validate_user") & validateField($_REQUEST,'validate_user')){
	 
	
	$SQL="SELECT COUNT(*) AS total FROM usuarios WHERE email='". mysql_escape_string($_REQUEST['user']) ."' ";
	$rs=mysql_query($SQL);
	$row=mysql_fetch_assoc($rs);
	echo json_encode(array('userExist'=>$row['total']));
	
	 
	
exit;
}
 
if (!isset($_REQUEST['group_id'])){
	echo "error";	
	exit;
}

 
if (isset($_REQUEST['submit'])){
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_empleado'],$protect->getSessionID());
	$data=json_decode($dencryt);
 
	
	$group=json_decode(System::getInstance()->getEncrypt()->decrypt($_REQUEST['group_id'],$protect->getSessionID()));

	$tipo_usuario=System::getInstance()->getEncrypt()->decrypt($_REQUEST['tipo_usuario'],$protect->getSessionID());
//	print_r($group->Id_role);
//	print_r($data);
	
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	
	if (($_REQUEST['usuario']!="") && ($_REQUEST['password']!="")){
		
		if ($_REQUEST['password']==$_REQUEST['new_password']){
			$obj= new ObjectSQL();
			$obj->email=$_REQUEST['usuario'];
			$obj->Contrasena=md5($_REQUEST['password']);
			$obj->id_nit=$data->id_nit;
                        $obj->idpaises=1;
                        $obj->id_empresa=$data->EM_ID;
			$obj->Nombres=$data->nombre;
			$obj->id_usuario=$_REQUEST['usuario'];
			$obj->status="1";
			$obj->idtipo_usuario=$tipo_usuario;
			$SQL=$obj->getSQL("insert","usuarios");
			mysql_query($SQL);
			$id=mysql_insert_id($protect->getDBLink()->link_id);


                      /*  $registro = new ObjectSQL();
                        $registro->texto=$SQL;
                        $registro->SetTable("prueba");
                        $mSQL=$registro->toSQL("insert");
                        mysql_query($mSQL) */
			
			//if ($id>0){
			/*Agrego un grupo al usuario*/
			$obj= new ObjectSQL();
			$obj->id_role=$group->Id_role;
                        $obj->id_empresa=$data->EM_ID;
                        $obj->idpaises=1;
			$obj->id_usuario=$_REQUEST['usuario'];
			$SQL=$obj->getSQL("insert","usu_role");
			mysql_query($SQL);
		//	}
			
			$retur['mensaje']="Registro actualizado correctamente! ";
			$retur['error']=false;
			echo json_encode($retur);
			exit;
		}else{
			$retur['mensaje']="La contraseña anterior no coincide ";
			$retur['error']=true;	
		}
 
	}else{
		$retur['mensaje']="Usuario o Contraseña son obligatorias";
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
    <td>Datos de persona:</td>
    <td><input name="nombre_empleado" id="nombre_empleado" type="text" class="form-control"  value="<?php 
	if ($edit=="1"){
		echo $data['nombre'];
	}
	
	?>"/>
      <input type="hidden" name="id_empleado" id="id_empleado"  /></td>
  </tr>
  <tr>
    <td>Tipo de usuario:</td>
    <td><select name="tipo_usuario" id="tipo_usuario" class="form-control">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `tipo_usuario`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idtipo_usuario'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>"><?php echo $row['tipousuario']?></option>
<?php } ?>
    </select></td>
  </tr>
  <tr>
    <td>Usuario:</td>
    <td><input name="usuario" type="text" id="usuario"  autocomplete="off" class="form-control"/></td>
  </tr>
  <tr>
    <td>Contraseña:</td>
    <td><input name="password" type="password" id="password" autocomplete="off" class="form-control"/></td>
  </tr>
  <tr>
    <td>Repetir contraseña:</td>
    <td><input name="new_password" type="password" id="new_password" autocomplete="off" class="form-control"/></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><input name="request" type="hidden" id="request" value="<?php echo $_REQUEST['request']?>" />
      <input name="group_id" type="hidden" id="group_id" value="<?php echo $_REQUEST['group_id']?>" /><input name="submit" type="hidden" id="submit" value="submit" /></td>
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
