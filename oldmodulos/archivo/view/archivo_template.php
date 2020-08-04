<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


if (isset($_REQUEST['template'])){

if ($_REQUEST['template']=="1"){
?>
<div style="background:#FFF;">
<table width="100%" border="0" cellspacing="5" cellpadding="5">
<tr>
<?php 

$nom=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','I','Z');

$f=0;
$j=0;
for($i=0;$i<10;$i++){
	if ($j==3){
		echo '</tr><tr>';
		$j=0;
	}
?>  
    <td align="center"> 
      <table width="200" border="1" cellspacing="0" cellpadding="0" class="archivo">
        <tr>
          <td height="200" align="center" valign="top" class="archimovil" id="<?php  echo System::getInstance()->Encrypt($nom[$f]);?>"><?php echo $nom[$f];?></td>
          <?php $f++; ?>
          <td width="60" align="center" style="background:#666666"><img src="images/timon_archivo.png" width="84" height="74"></td>
          <td align="center" valign="top"   class="archimovil" id="<?php echo System::getInstance()->Encrypt($nom[$f]);?>"><?php echo $nom[$f];?></td>
        </tr>
      </table>
      <br>
    </td>
<?php
	$f++; 
	$j++;
} ?> 
   </tr>

</table>
</div>
<p>
  <?php } 
 
 
if ($_REQUEST['template']=="2"){  
 ?>
<table width="800" border="1" cellspacing="0" cellpadding="0" class="archivo_detalle" style="background:#FFF">
<?php
for($i=1;$i<7;$i++){
?>  
  <tr>
    <td width="100" height="70" class="archiv" id="<?php  echo System::getInstance()->Encrypt(json_encode(array("f"=>$i,"c"=>"1")));?>" >&nbsp;</td>
    <td width="100" height="70" class="archiv" id="<?php echo System::getInstance()->Encrypt(json_encode(array("f"=>$i,"c"=>"2")));?>">&nbsp;</td>
  </tr>
<?php } ?>  
</table>
<?php } ?>

<?php } ?>