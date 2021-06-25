<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


SystemHtml::getInstance()->includeClass("client","PersonalData");
			
$nameForm=$_REQUEST['form'];

 
$client_id=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (isset($client_id->id_nit)){
	$client_id=$client_id->id_nit;
}else{
	$client_id=System::getInstance()->Decrypt($_REQUEST['id']);	
}
//$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());


$person= new PersonalData($protect->getDBLink());

/* VERIFICO SI EL CLIENTE EXISTE  */
if (!$person->existClient($client_id)){
	header("location:index.php?mod_client/client_list");
	exit;
}

$data_p=$person->getClientData($client_id);

$_permisos=$protect->getPermisosByPage(System::getInstance()->getCurrentModulo());
//print_r($data_p);
?>
<form method="post"  action="" id="<?php echo $nameForm?>"  name="<?php echo $nameForm?>" class="fsForm">
<table width="100%" border="0" cellpadding="5" cellspacing="5" >
  <tr>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">RNC Empresa<span>*
      <input name="submit_empresa" type="hidden" id="submit_empresa" value="1" />
      <input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']; ?>" />
    </span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Empresa <span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Pagina web</label></td>
    </tr>
  <tr>
    <td><input type="text" id="nitempresa" name="nitempresa" size="20" value="<?php echo $data_p['nitempresa']; ?>" class="required" /></td>
    <td><input type="text" id="empresalabora" name="empresalabora" size="20" value="<?php echo $data_p['empresalabora']; ?>" class="required" /></td>
    <td><input type="text" id="paginaweb" name="paginaweb" size="20" value="<?php echo $data_p['paginaweb']; ?>"  /></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php // if (trim($_permisos['Cambios'])=="1"){ ?>
  <tr>
    <td colspan="3" align="center"><button type="button" id="update_empresa" name="update_empresa" >Actualizar informacion</button></td>
  </tr>
  <?php // } ?>
</table>
</form> 
