<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


if (isset($_REQUEST['main_view'])){
	include("mainModulo.php");
	exit;
}

if (isset($_REQUEST['beneficiario_view'])){
	if ($_REQUEST['beneficiario_view']=="create"){
		include("beneficiario_view_create.php");
	}
	if ($_REQUEST['beneficiario_view']=="edit"){
		include("beneficiario_view_edit.php");
	}
	exit;
}

if (isset($_REQUEST['view_person_inhu'])){
	if ($_REQUEST['view_person_inhu']=="create"){
		include("create_view_person_inhumado.php");
	}
	if ($_REQUEST['view_person_inhu']=="edit"){
		include("edit_view_person.php");
	}
	exit;
}
if (isset($_REQUEST['view_person'])){
	if ($_REQUEST['view_person']=="create"){
		include("create_view_person.php");
	}
	if ($_REQUEST['view_person']=="edit"){
		include("edit_view_person.php");
	}
	exit;
}

if (isset($_REQUEST['view_address'])){
	include("direccion.php");
}

if (isset($_REQUEST['view_empresa'])){
	include("empresa.php");
}
if (isset($_REQUEST['view_telefono'])){
	if ($_REQUEST['view_telefono']=="create"){
		include("create_view_telefono.php");
	}
	if ($_REQUEST['view_telefono']=="edit"){
		include("edit_view_person.php");
	}
}

if (isset($_REQUEST['view_email'])){
	include("create_view_email.php");
}

if (isset($_REQUEST['view_referencia'])){
	include("create_view_referencia.php");
}

if (isset($_REQUEST['view_contacto'])){
	include("create_view_contactos.php");
}

if (isset($_REQUEST['getPersonData'])){
 	$message=array("mensaje"=>"","error"=>true,"data"=>array());
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	
	$client_id=System::getInstance()->Decrypt($_REQUEST['id']);
	
	$person= new PersonalData($protect->getDBLink());
	/* VERIFICO SI EL CLIENTE EXISTE  */
	if (!$person->existClient($client_id)){
	//	echo "ID no existe!";
		//header("location:index.php?mod_client/client_list");
		exit;
	}
	
	$message['error']=false;
	$data_p=$person->getClientData($client_id);	
	$message['data']=array(
		"primer_nombre"=>$data_p['primer_nombre'],
		"primer_apellido"=>$data_p['primer_apellido'],
		"direccion"=>$data_p['direccion'],
		"telefono"=>$data_p['telefono'],
		"email"=>$data_p['email']
	);
	
	echo json_encode($message);
	exit;
}

if (isset($_REQUEST['validate_empresa'])){
	$SQL="SELECT * FROM `dgii_rnc` WHERE codigo='".mysql_real_escape_string($_REQUEST['nit'])."'  limit 1 ";
	$rs=mysql_query($SQL);
	$data=array(
		"valid"=>false,
		"data"=>""
	);
	while($row= mysql_fetch_assoc($rs)){
		$data['valid']=true;
		$row['nombre_empresa']=utf8_encode($row['nombre_empresa']);
		$data['data']=$row;
	} 
	
	echo json_encode($data);
	 exit;
}
 
if (isset($_REQUEST['view_referidos'])){
	include("create_view_referidos.php");
}

?>