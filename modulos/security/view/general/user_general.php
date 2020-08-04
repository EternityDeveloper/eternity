<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

$dencryt=System::getInstance()->Decrypt($_REQUEST['request']);
$data=json_decode($dencryt);
 
if (!(isset($data->id_usuario))){
	echo json_encode(array('error'=>true,'mensaje'=>'Usuario no logeado!'));	
	exit;
}


if (validateField($_REQUEST,"update_role")){

    $dencryt=System::getInstance()->Decrypt($_REQUEST['roles']);
    $role=json_decode($dencryt);

    $obj= new ObjectSQL();
    $obj->id_role=$role->Id_role;
    $SQL=$obj->getSQL("update","usu_role"," where id_usuario='".$data->id_usuario."'");
    mysql_query($SQL);		

    $retur['mensaje']="Registro actualizado correctamente! ";
    $retur['error']=false;
    echo json_encode($retur);
    
    
    exit;
}

/*ACTUALIZA EL TIPO DE USUARIO*/
if (validateField($_REQUEST,"update_tipo_user")){
 
    $obj= new ObjectSQL();
    $obj->idtipo_usuario=System::getInstance()->Decrypt($_REQUEST['idtipo_usuario']);
    $SQL=$obj->getSQL("update","Usuarios"," where id_usuario='".$data->id_usuario."'");
    mysql_query($SQL);		

    $retur['mensaje']="Registro actualizado correctamente! ";
    $retur['error']=false;
    echo json_encode($retur);
    
    
    exit;
}

if (validateField($_REQUEST,"update_estatus") && validateField($_REQUEST,"estatus")){ 
    $estatus=json_decode(System::getInstance()->Decrypt($_REQUEST['estatus']));

    $obj= new ObjectSQL();
    $obj->status=$estatus->id_status;
    $SQL=$obj->getSQL("update","Usuarios"," where id_usuario='".$data->id_usuario."'");
    mysql_query($SQL);		

    $retur['mensaje']="Registro actualizado correctamente! ";
    $retur['error']=false;
    echo json_encode($retur); 
    exit;
}

if (isset($_REQUEST['change_password'])){
     //   print_r($_REQUEST);
    if (validateField($_REQUEST,"password") && validateField($_REQUEST,"password_repeat")){
        if ($_REQUEST['password']==$_REQUEST['password_repeat']){
       //     print_r($_REQUEST);
            $obj= new ObjectSQL();
            $obj->Contrasena=md5($_REQUEST['password']);
            $SQL=$obj->getSQL("update","Usuarios"," where id_usuario='".$data->id_usuario."'");
            mysql_query($SQL);
            $rt=array();
            $rt['mensaje']="Registro actualizado correctamente! ";
            $rt['error']=false;
        }else{
            $rt=array(
                'error'=>true,
                'mensaje'=>'La contraseña introducida no esta correcta, por favor asegúrese que ha introducido bien la contraseña.'
             );
        }
    }else{
        $rt=array(
            'error'=>true,
            'mensaje'=>'Error: La contraseña no puede estar vacia!'
         );
    }
    echo json_encode($rt);
    exit;
}

if (isset($_REQUEST['change_code_figs'])){
     //   print_r($_REQUEST);
    if (validateField($_REQUEST,"codigo_figs")){
        if (is_numeric($_REQUEST['codigo_figs'])){
       //     print_r($_REQUEST);
            $obj= new ObjectSQL();
            $obj->codigo_figs=$_REQUEST['codigo_figs'];
            $SQL=$obj->getSQL("update","Usuarios"," where id_usuario='".$data->id_usuario."'");
            mysql_query($SQL);
            $rt=array();
            $rt['mensaje']="Registro actualizado correctamente! ";
            $rt['error']=false;
        }else{
            $rt=array(
                'error'=>true,
                'mensaje'=>'El codigo debe de ser numerico!'
             );
        }
    }else{
        $rt=array(
            'error'=>true,
            'mensaje'=>'Error: el codigo no puede estar vacia!'
         );
    }
    echo json_encode($rt);
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
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="30"><strong>Nombre:</strong></td>
        <td><?php echo $data->Nombres;?></td>
        <td>&nbsp;</td>
        </tr>
      <tr>
        <td height="30"><strong>Usuario:</strong></td>
        <td><?php echo $data->email?></td>
        <td>&nbsp;</td>
        </tr>
      <tr>
        <td><strong>Tipo de usuario:</strong></td>
        <td><select name="tipo_usuario" id="tipo_usuario" class="textfield textfieldsize"  style="height:30px;">
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `tipo_usuario`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['idtipo_usuario']);
?>
          <option value="<?php echo $encriptID?>"  <?php echo $data->idtipo_usuario==$row['idtipo_usuario']?' selected="selected"':''?>><?php echo $row['tipousuario']?></option>
          <?php } ?>
        </select></td>
        <td><div class="buttons"><button type="button" class="positive" id="bt_tipo_user_save" style="display:none">Guardar</button></div></td>
      </tr>
      <tr>
        <td height="30"><strong>Contraseña:</strong></td>
        <td>***********</td>
        <td><div class="buttons"><button type="button" class="positive" id="bt_change_password">Cambiar</button></div></td>
        </tr>
      <tr>
        <td height="30"><strong>Grupo Permisos:</strong></td>
        <td><select name="Roles" id="Roles"  class="textfield textfieldsize"  style="height:30px;"  >
          <option value="">Seleccionar</option>
          <?php 
	 	 $SQL="SELECT * FROM `Roles`";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
 
	  ?>
          <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>" <?php echo $data->id_role==$row['Id_role']?' selected="selected"':''?>><?php echo $row['Role'];?></option>
          <?php } ?>
        </select></td>
        <td><div class="buttons"><button type="button" class="positive" id="bt_role_save" style="display:none">Guardar</button></div></td>
        </tr>
      <tr>
        <td height="30"><strong>Codigo figs:</strong></td>
        <td><?php echo $data->codigo_figs;?></td>
        <td><div class="buttons"><button type="button" class="positive" id="bt_codigo_figs">Cambiar</button></div></td>
      </tr>
      <tr>
        <td height="30"><strong>Estatus:</strong></td>
        <td><select name="g_estatus" id="g_estatus"  class="textfield textfieldsize"  style="height:30px;"  >
          <option value="">Seleccionar</option>
          <?php 
	 	 $SQL="SELECT id_status,descripcion FROM `sys_status` WHERE id_status IN (1,2) ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
 
	  ?>
          <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>" <?php echo $data->status==$row['id_status']?' selected="selected"':''?>><?php echo $row['descripcion'];?></option>
          <?php } ?>
        </select></td>
        <td><div class="buttons">
          <button type="button" class="positive" id="bt_save_disable"  style="display:none">Guardar</button>
        </div></td>
      </tr>
      </table></td>
  </tr>
  <tr>
    <td><input name="request" type="hidden" id="request" value="<?php echo $_REQUEST['request']?>" />
      <input name="submit" type="hidden" id="submit" value="submit" /></td>
  </tr>
  <tr>
    <td align="center"><div class="buttons" style="width:100px"><a href="#"  class="negative" id="bt_general_cancel"><img src="images/cross.png" alt=""/> Cerrar</a> </div></td>
    </tr>
</table>
</div>
</form>