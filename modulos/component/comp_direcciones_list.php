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

$contact_id=0;
if (isset($_REQUEST['contact_id'])){
	$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],$protect->getSessionID());
} 

	
$data_address=$person->getAddress($client_id,$contact_id);


?>
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm">
<table border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th><span class="fsLabel fsRequiredLabel">Provincia</span></th>
          <th><span class="fsLabel fsRequiredLabel">Ciudad</span></th>
          <th><span class="fsLabel fsRequiredLabel">Sector</span></th>
          <th><span class="fsLabel fsRequiredLabel">Avenida</span></th>
          <th><span class="fsLabel fsRequiredLabel">Calle</span></th>
          <th><span class="fsLabel fsRequiredLabel">Tipo de direccion</span></th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
<?php
 
foreach($data_address as $key => $row){
	//print_r($row);
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_direcciones'],$protect->getSessionID());
?>
        <tr>
          <td><?php echo $row['provincia'];?></td>
          <td><?php echo $row['ciudad'];?></td>
          <td align="center" ><?php echo $row['sector'];?></td>
          <td align="left" ><?php echo $row['avenida']?></td>
          <td height="25" align="left" ><?php echo $row['calle']?></td>
          <td align="left" ><?php echo $row['tipo']?></td>
          <td align="center" ><a href="#" class="direccion_edit" id="<?php echo $encriptID?>" contact_id="<?php echo $_REQUEST['contact_id']?>"><img src="images/view.png" width="27" height="28" /></a> </td>
        </tr>
        <?php 
}
 ?>
      </tbody>
  </table>
</form>