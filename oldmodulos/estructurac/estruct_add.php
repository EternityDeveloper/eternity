<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (isset($_REQUEST['submit'])){
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	
	$edit=0;
	if (isset($_REQUEST['edit'])){
		$edit=$_REQUEST['edit'];
	}
 
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_empleado'],$protect->getSessionID());
	$data=json_decode($dencryt);
	
	if (isset($data->id_nit)){
		$SQL="select count(*) total from  sys_gerentes where (sys_personas_id_nit ='". mysql_escape_string($data->id_nit)."' or idgerentes ='". mysql_escape_string($_REQUEST['id_gerente'])."' )  and  sys_gerentes.status='1'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		
		if ($edit=="0"){
			if ($row['total']<=0){
				$obj= new ObjectSQL();
				$obj->idgerentes=$_REQUEST['id_gerente'];
				$obj->sys_personas_id_nit=$data->id_nit;
				$obj->status=1;
				$obj->descripcion=$_REQUEST['descripcion'];
				$SQL=$obj->getSQL("insert","sys_gerentes");
				mysql_query($SQL);
				$retur['mensaje']="Registro insertado correctamente!";
				$retur['error']=false;
				echo json_encode($retur);
			}else{
				$retur['mensaje']="El ID del gerente o el empleado ya esta registrado no se puede volver a asignar!";
				$retur['error']=true;
				echo json_encode($retur);	
			}
		}else if ($edit=="1"){ //Actualizando los datos
				$obj= new ObjectSQL();
	//			$obj->idgerentes=$_REQUEST['id_gerente'];
				$obj->sys_personas_id_nit=$data->id_nit;
				$obj->status=1;
				$obj->descripcion=$_REQUEST['descripcion'];
				$SQL=$obj->getSQL("update","sys_gerentes","where idgerentes='".  mysql_escape_string($_REQUEST['id_gerente']) ."'");
				mysql_query($SQL);
				$retur['mensaje']="Registro actualizado correctamente! ";
				$retur['error']=false;
				echo json_encode($retur);
		}else{
			echo json_encode($retur);
		}
	}else{
		echo json_encode($retur);
	}
	
	
	
	
	exit;
} 
/* en caso de elimniar a alguien */
if (isset($_REQUEST['remove'])){
	if ($_REQUEST['remove']=="true"){	
		
		$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
		$id_gerente=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_gerente'],$protect->getSessionID());
	
		$SQL="SELECT COUNT(idgerentes) AS total FROM `sys_directores_division` WHERE `idgerentes` = '".$id_gerente."' and status=1";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['total']<=0){
			
			$SQL="UPDATE `sys_gerentes` SET `status`=0 WHERE `idgerentes`='".$id_gerente."'";
			mysql_query($SQL);
			$retur['mensaje']="Registro removido correctamente!";
			$retur['error']=false;
			
		}else{
			$retur['mensaje']="El gerente tiene a cargo un director de divicion, por tal razon no se puede eliminar!";
			$retur['error']=true;
		}
		
		echo json_encode($retur);
		exit;
	}
}

$edit=0;
$data=array();

$id_gerente=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_gerente'],$protect->getSessionID());


if (isset($_REQUEST['edit'])){
	if ($_REQUEST['edit']>0){
		$edit=$_REQUEST['edit'];
	
		if ($id_gerente!=""){	
			$SQL="SELECT * FROM `sys_gerentes` WHERE status='1' and idgerentes='". mysql_escape_string($id_gerente) ."'";
			$rs=mysql_query($SQL);
			$data=mysql_fetch_assoc($rs);
		}else{
			echo "Error cliente ID fail";
			exit;	
		}
	}
}


?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_pantallas_gerente" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>ID Gerente:</td>
    <td><input name="id_gerente" type="text" id="id_gerente" value="<?php echo count($data)>0?$data['idgerentes']:''?>"
    <?php echo count($data)>0?' disabled="disabled"':''?> /></td>
  </tr>
  <tr>
    <td>Empleado:</td>
    <td><label>
      <select name="id_empleado" id="id_empleado">
        <option value="<?php 
		$row=array("nombre"=>"","id_nit"=>0);
		echo System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
		?>">Seleccione</option>
        <?php 

$SQL="SELECT id_nit,CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido) AS nombre FROM `sys_personas` ";

if ($edit=="0"){
	$SQL.=" WHERE id_nit NOT IN (SELECT `sys_personas_id_nit` FROM sys_gerentes WHERE status='1' ) ";
}

$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['id_nit']==$data['sys_personas_id_nit']){ 
							echo 'selected="selected"';
						}
					}?> ><?php echo $row['nombre']?></option>
        <?php } ?>
        </select>
      </label>
      <input name="submit" type="hidden" id="submit" value="submit" />
      <input name="edit" type="hidden" id="edit" value="<?php echo $edit?>" /></td>
  </tr>
  <tr>
    <td valign="top">Descripcion:</td>
    <td><textarea name="descripcion" id="descripcion" ><?php echo count($data)>0?$data['descripcion']:''?></textarea></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#" id="bt_cancel" class="negative"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table>
</div>
</form>