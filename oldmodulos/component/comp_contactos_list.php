<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

SystemHtml::getInstance()->includeClass("client","PersonalData");

$client_id=$_REQUEST['client_id'];

$person= new PersonalData($protect->getDBLink());

$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['client_id'],$protect->getSessionID());

$comp="";
if (isset($_REQUEST['comp'])){
	$comp=$_REQUEST['comp'];
}
	
	
$data=$person->getContactList($client_id);
 
?>
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm">
<table border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Tipo contacto</th>
          <th><strong>Nombre</strong></th>
          <th><strong>Apellido</strong></th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
<?php
 
foreach($data as $key => $row){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_contactos'],$protect->getSessionID());
?>
        <tr>
          <td height="25"><?php echo $row['tipo'];?></td>
          <td><?php echo $row['Nombres'];?></td>
          <td align="center" ><?php echo $row['Apellidos'];?></td>
          <td align="center" ><a href="#" class="contact_list_c"  id="<?php echo $encriptID?>" contact_id="<?php echo $encriptID ?>"><img src="images/view.png" width="27" height="28" /></a> </td>
        </tr>
        <?php 
}
 ?>
      </tbody>
  </table>
</form>