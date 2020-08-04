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

$data=$person->getEmails($client_id,$contact_id);
// print_r($data);
?>
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm">
<table border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Tipo correo </th>
          <th><strong>Direccion</strong></th>
          <th><strong>Observacion</strong></th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
<?php
 
foreach($data as $key => $row){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_emails'],$protect->getSessionID());
?>
        <tr>
          <td height="25"><?php echo $row['tipo'];?></td>
          <td><?php echo $row['direccion'];?></td>
          <td align="center" ><?php echo $row['observaciones'];?></td>
          <td align="center" ><a href="#" class="emailView" id="<?php echo $encriptID?>" contact="<?php echo $_REQUEST['contact_id']?>" ><img src="images/view.png" width="27" height="28" /></a> </td>
        </tr>
        <?php 
}
 ?>
      </tbody>
  </table>
</form>