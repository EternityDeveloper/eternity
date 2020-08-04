<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}


/*Llegan los datos POST*/
if ( isset($_REQUEST['submit']) )
{
	include($_PATH."class/modulos/roles/class.ManagerRoles.php");
	$check_value="0";
	
	if (isset($_REQUEST['checked'])){
		if ($_REQUEST['checked']=="true" || $_REQUEST['checked']=="1"){
			$check_value="1";
		}
	}
	
	
	if (isset($_REQUEST['id'])){
		$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());
		$data=json_decode($dencryt);
		//print_r($_REQUEST);
		$ret=ManagerRoles::getInstance()->updatePermiso($data->roleID,$data->pantallaID,$data->campo,$check_value);
		echo json_encode($ret); 
	}
	
	exit;
}

if (!isset($_REQUEST['request'])){
	echo "error";	
	exit;
}



$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['request'],$protect->getSessionID());

$data=json_decode($dencryt);

if (!(isset($data->Id_role) && isset($data->Role))){
	echo "error";	
	exit;
}

//print_r(session_id());
$Role_data=chargeRoles($data->Id_role);

function field_permisos_is_check($permiso,$field){
	if (isset($permiso[$field])){
		if ($permiso[$field]=="1"){
			return ' checked="checked"';
		}
	}
	return "";
}

function chargeRoles($roleId){
	$data=array();
	$SQL="SELECT * FROM Seguridad 
		INNER JOIN Roles ON (Seguridad.Id_role=Roles.Id_role)
		INNER JOIN Pantallas ON (Pantallas.id_pantalla=Seguridad.id_pantalla)
		WHERE Roles.id_role='".$roleId."'";
		$rs = mysql_query($SQL);
		if (mysql_num_rows($rs)>0){		
			while($row=mysql_fetch_assoc($rs)){
				$data[$row['id_pantalla']]=$row;
			}
		}
	return $data;	
}
?>
<div><h2>Listado de permisos: <?php echo $data->Role?></h2></div>
<form id="form1" name="form1" method="post" action="">

<br>
<br>
<h2>SISTEMA</h2>

<table  border="0" class="display dtPermisos" id="user_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Code</th>
          <th>Pantalla</th>
          <th>Modulo</th>
          <th>Descripcion</th>
          <th>Permisos</th>
        </tr>
      </thead>
      <tbody>
<?php

$SQL="SELECT * FROM Pantallas WHERE `id_pantalla` NOT IN (SELECT `Pantallas_id_pantalla` FROM clasificacion_pantallas) ";

$rs=mysql_query($SQL);
while($row=mysql_fetch_array($rs)){
	$settings=$Role_data[$row['id_pantalla']];
	/* Codifico el 
		ROLE_ID,ID_PANTALLA,NOMBRE_CAMPO
		para asi poder realizar la actualizacion mas segura*/
	$stuct=array(
			"roleID"=>$data->Id_role,
			"pantallaID"=>$row['id_pantalla'],
			"campo"=>"alta"
		);	
		
	$_alta=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="baja";
	$_baja=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="Cambios";
	$_Cambios=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="Consulta";
	$_Consulta=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="acceso";
	$_acceso=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	
?>
        <tr>
          <td><?php echo $row['id_pantalla']?></td>
          <td><?php echo $row['Pantalla']?></td>
          <td><?php echo $row['URL']?></td>
          <td >&nbsp;</td>
          <td ><table width="100%" border="0">
            <tr>
              <td><strong>Alta</strong></td>
              <td><strong>Baja</strong></td>
              <td><strong>Cambios</strong></td>
              <td><strong>Consulta</strong></td>
              <td><strong>Acceso</strong></td>
            </tr>
            <tr>
              <td align="center">
                <input name="<?php echo $_alta?>" type="checkbox" id="<?php echo $_alta?>" value="1" <?php echo field_permisos_is_check($settings,"alta");?> onclick="_saveConfig(this)" />              </td>
              <td align="center"><input name="<<?php echo $_baja?>" type="checkbox" id="<?php echo $_baja?>" value="1"  <?php echo field_permisos_is_check($settings,"baja");?> onclick="_saveConfig(this)"/></td>
              <td align="center"><input name="<?php echo $_Cambios?>" type="checkbox" id="<?php echo $_Cambios?>" value="1"  <?php echo field_permisos_is_check($settings,"Cambios");?> onclick="_saveConfig(this)" /></td>
              <td align="center"><input name="<?php echo $_Consulta?>" type="checkbox" id="<?php echo $_Consulta?>" value="1"  <?php echo field_permisos_is_check($settings,"Consulta");?> onclick="_saveConfig(this)"/></td>
              <td align="center"><input name="<?php echo $_acceso?>" type="checkbox" id="<?php echo $_acceso?>" value="1"  <?php echo field_permisos_is_check($settings,"acceso");?> onclick="_saveConfig(this)"/></td>
            </tr>
          </table></td>
        </tr>
        <?php  
}
 ?>
      </tbody>
    </table>


<?php 
/*AGRUPANDO LOS PERMISOS POR SUS RESPECTIVOS MODULOS
 	PARA PODER ENTENDER MEJOR COMO APLICARLOS*/
$SQL="SELECT * FROM clasificacion_pantallas WHERE tipo_menu='Menu' ";
$rsX=mysql_query($SQL);
while($rowX=mysql_fetch_array($rsX)){
?>

<h2><?php echo $rowX['nombre'];?></h2>
<table  border="0" class="display dtPermisos" id="user_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Code</th>
          <th>Pantalla</th>
          <th>Modulo</th>
          <th>Descripcion</th>
          <th>Permisos</th>
        </tr>
      </thead>
      <tbody>
<?php
$SQL="SELECT * FROM Pantallas WHERE `id_pantalla` IN (SELECT `Pantallas_id_pantalla` FROM clasificacion_pantallas WHERE `hijo_de_id_clas_pantallas`='".$rowX['hijo_de_id_clas_pantallas']."' ) ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_array($rs)){
	$settings=$Role_data[$row['id_pantalla']];
	/* Codifico el 
		ROLE_ID,ID_PANTALLA,NOMBRE_CAMPO
		para asi poder realizar la actualizacion mas segura*/
	$stuct=array(
		"roleID"=>$data->Id_role,
		"pantallaID"=>$row['id_pantalla'],
		"campo"=>"alta"
		);	
		
	$_alta=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="baja";
	$_baja=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="Cambios";
	$_Cambios=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="Consulta";
	$_Consulta=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	$stuct['campo']="acceso";
	$_acceso=System::getInstance()->getEncrypt()->encrypt(json_encode($stuct),$protect->getSessionID());
	
?>
        <tr>
          <td><?php echo $row['id_pantalla']?></td>
          <td><?php echo $row['Pantalla']?></td>
          <td><?php echo $row['URL']?></td>
          <td >&nbsp;</td>
          <td ><table width="100%" border="0">
            <tr>
              <td><strong>Alta</strong></td>
              <td><strong>Baja</strong></td>
              <td><strong>Cambios</strong></td>
              <td><strong>Consulta</strong></td>
              <td><strong>Acceso</strong></td>
            </tr>
            <tr>
              <td align="center">
                <input name="<?php echo $_alta?>" type="checkbox" id="<?php echo $_alta?>" value="1" <?php echo field_permisos_is_check($settings,"alta");?> onclick="_saveConfig(this)" />              </td>
              <td align="center"><input name="<<?php echo $_baja?>" type="checkbox" id="<?php echo $_baja?>" value="1"  <?php echo field_permisos_is_check($settings,"baja");?> onclick="_saveConfig(this)"/></td>
              <td align="center"><input name="<?php echo $_Cambios?>" type="checkbox" id="<?php echo $_Cambios?>" value="1"  <?php echo field_permisos_is_check($settings,"Cambios");?> onclick="_saveConfig(this)" /></td>
              <td align="center"><input name="<?php echo $_Consulta?>" type="checkbox" id="<?php echo $_Consulta?>" value="1"  <?php echo field_permisos_is_check($settings,"Consulta");?> onclick="_saveConfig(this)"/></td>
              <td align="center"><input name="<?php echo $_acceso?>" type="checkbox" id="<?php echo $_acceso?>" value="1"  <?php echo field_permisos_is_check($settings,"acceso");?> onclick="_saveConfig(this)"/></td>
            </tr>
          </table></td>
        </tr>
        <?php  
}
 ?>
      </tbody>
    </table>
<?php } ?>
</form>