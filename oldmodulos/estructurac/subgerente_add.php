<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}


//print_r($_REQUEST);
if (isset($_REQUEST['submit'])){
	 
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	
	$edit=0;
	if (isset($_REQUEST['edit'])){
		$edit=$_REQUEST['edit'];
	}
	
	$id_gerente=System::getInstance()->Decrypt($_REQUEST['id_gerente']);
	$id_empleado=json_decode(System::getInstance()->Decrypt($_REQUEST['id_empleado']));
	$id_director=System::getInstance()->Decrypt($_REQUEST['director_id']);
	$id_grupos=json_decode(System::getInstance()->Decrypt($_REQUEST['id_grupos']));
  

	if ((isset($id_empleado->id_nit) || $id_gerente_grupo!="")){

		$SQL="select count(*) total from  sys_gerentes_grupos where 
			(id_nit ='". mysql_escape_string($id_empleado->id_nit)."' or codigo_gerente_grupo ='". mysql_escape_string($id_gerente_grupo)."') and status='1' ";
		
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);

		if ($edit=="0"){
			if ($row['total']<=0){ 
				
				$obj= new ObjectSQL();  
				$obj->status=1;
				$obj->codigo_gerente=$id_gerente;
				$obj->codigo_director=$id_director;
				$obj->idgrupos=$id_grupos->idgrupos;
				$obj->id_nit=$id_empleado->id_nit;
                                $obj->idgerente_grupo=" ";
                                $obj->idgerentes=" ";
                                $obj->iddirectores=" ";
                                $obj->idgrupos=" ";
				
				$SQL=$obj->getSQL("insert","sys_gerentes_grupos");
			 	mysql_query($SQL);  
				
				$retur['mensaje']="Registro insertado correctamente!";
				$retur['error']=false;
				echo json_encode($retur);
			}else{
				$retur['mensaje']="El ID del gerente de grupo o el empleado ya esta registrado no se puede volver a asignar!";
				$retur['error']=true;
				echo json_encode($retur);	
			}
		}else if ($edit=="1"){ //Actualizando los datos
		 		$id_gerente_grupo=$_REQUEST['id_gerente_grupo'];
				$obj= new ObjectSQL();
				$obj->status=1;
				$obj->idgrupos=$id_grupos->idgrupos;
				$obj->id_nit=$id_empleado->id_nit;

				$SQL=$obj->getSQL("update","sys_gerentes_grupos"," where idgerente_grupo='".$id_gerente_grupo."'");
				 
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
		$id_gerente_grupo=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_gerente_grupo'],$protect->getSessionID());
	
		/*Certifico que no tenga a alguien agregado debajo de el */
		$SQL="SELECT COUNT(idgerente_grupo) AS total FROM `sys_asesor` WHERE `idgerente_grupo`='".$id_gerente_grupo."' AND status=1";

		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['total']<=0){
			
			$SQL="UPDATE `sys_gerentes_grupos` SET `status`=0 WHERE `idgerente_grupo`='".$id_gerente_grupo."'";
			mysql_query($SQL);
		
			$retur['mensaje']="Registro removido correctamente!";
			$retur['error']=false;
			
		}else{
			$retur['mensaje']="Existen asesores de familia que estan asignado a este Gerente grupo de ventas, por tal razon no se puede eliminar!";
			$retur['error']=true;
		}
		
		echo json_encode($retur);
		exit;
	}
}

 
$edit=0;
$data=array();
 
if (isset($_REQUEST['edit'])){
	if ($_REQUEST['edit']>0){
		$edit=$_REQUEST['edit'];
	
	//	$id_gerente=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_gerente'],$protect->getSessionID());
	//	$id_director=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_director'],$protect->getSessionID());
	
		$id_gerente_grupo=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_gerente_grupo'],$protect->getSessionID());

		if ($id_gerente_grupo!=""){	
			$SQL="SELECT *,
				sys_personas.id_nit,
				CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido) AS nombre 
			 FROM `sys_gerentes_grupos`
			LEFT JOIN sys_personas ON (sys_personas.id_nit=sys_gerentes_grupos.id_nit)
			 WHERE status='1' and idgerente_grupo='". mysql_escape_string($id_gerente_grupo) ."' ";
	 
 
			$rs=mysql_query($SQL);
			$data=mysql_fetch_assoc($rs);
		 
		}else{
			echo "Error cliente ID fail";
			exit;	
		}
	}
}


?>
<div class="modal fade" id="moda_agregar_gerente_venta" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:430px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">AGREGAR GERENTE DE VENTAS</h4>
      </div>
      <div class="modal-body">
<form name="form_pantallas" id="form_pantallas" method="post" action="" class="fsForm  fsSingleColumn"> 
<table width="100%" border="1">
 
  <tr <?php echo count($data)<=0?' style="display:none"':''?>>
    <td>ID Gerente de Grupo:</td>
    <td><?php echo $data['idgerente_grupo']; ?></td>
  </tr>
  <tr>
    <td>Grupo:</td>
    <td><label>
      <select name="id_grupos" id="id_grupos"  class="form-control required" >
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_grupos` where status='1'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['idgrupos']==$data['idgrupos']){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['grupos']?></option>
        <?php } ?>
      </select>
    </label></td>
  </tr>
  <tr style="display:none">
    <td>División:</td>
    <td><label>
      <select name="id_division" id="id_division"  class="required" >
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_divisiones` where status='1'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['iddivision']==$data['iddivision']){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['division']?></option>
        <?php } ?>
        </select>
      </label></td>
  </tr>
  <tr>
    <td>Empleado:</td>
    <td><input name="nombre_empleado" id="nombre_empleado" type="text" class="form-control" value="<?php 
	if ($edit=="1"){
		echo $data['nombre'];
	}
	
	?>"/>
    <input name="id_empleado" id="id_empleado" type="hidden"  value="<?php echo System::getInstance()->Encrypt(json_encode($data));?>" />
      <input name="id_gerente" type="hidden" id="id_gerente" value="<?php echo $_REQUEST['id_gerente'] ?>" />
      <input name="director_id" type="hidden" id="director_id" value="<?php echo $_REQUEST['director_id'] ?>" />
      <input name="edit" type="hidden" id="edit" value="<?php echo $_REQUEST['edit'] ?>" /><input name="id_gerente_grupo" type="hidden" id="id_gerente_grupo" value="<?php echo $data['idgerente_grupo']; ?>" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#"  id="bt_cancel" class="negative"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table> 
</form>
      </div>
       
    </div>
  </div>
</div>
