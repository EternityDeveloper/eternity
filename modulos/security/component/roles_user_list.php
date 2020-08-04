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

if (!(isset($data->Id_role) && isset($data->Role))){
	echo "error";	
	exit;
}
?>
<div><h2>Role: <?php echo $data->Role?></h2></div>
<button type="button" class="positive" name="add_user"  id="add_user" onclick="openDialogNewUser()" >
                        <img src="images/apply2.png" alt=""/> 
                        Agregar nuevo
                      </button>
<table  border="0" class="display" id="user_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Nombre completo</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <?php
$SQL="SELECT 
	usuarios.*,
	CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS Nombres
 FROM usuarios 
 INNER JOIN sys_personas ON (sys_personas.id_nit=usuarios.id_nit)
	INNER JOIN usu_role ON (usuarios.id_usuario=usu_role.id_usuario)
	WHERE usu_role.id_role='".$data->Id_role."'  ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	 $user_data=System::getInstance()->Encrypt(json_encode($row));

?>
        <tr>
          <td ><?php echo $row['email']?></td>
          <td><?php echo $row['Nombres']?></td>
          <td height="30" ><a href="#" class="edit_user_role" onclick="editUserRole('<?php echo $user_data;?>')"  ><img src="images/subtract_from_cart.png" width="27" height="28" /></a></td>
        </tr>
        <?php  
}
 ?>
      </tbody>
    </table>
</div>	</td>
  </tr>
</table>
