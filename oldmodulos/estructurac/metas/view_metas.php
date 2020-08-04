<?php
 
SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 

$asesor=new Asesores($protect->getDBLink(),$_REQUEST);
$comercial=json_decode(System::getInstance()->Decrypt($_REQUEST['id'])); 

 
if (validateField($_REQUEST,"submit")){
 	
//	print_r($_REQUEST);
// negocios_
	//echo "Dos guardados!";
	//$comercial=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	//print_r($comercial);
	for($i=1;$i<=12;$i++){
		if ((isset($_REQUEST['monto_'.date("Y")."_".$i])) && (isset($_REQUEST['negocios_'.date("Y")."_".$i]))){
			//echo $_REQUEST['negocios_'.date("Y")."_".$i]." ".$_REQUEST['monto_'.date("Y")."_".$i]."\n";	
			
			$asesor->updateMetas(
									$i,$comercial->id_comercial,
									$_REQUEST['negocios_'.date("Y")."_".$i],
									$_REQUEST['monto_'.date("Y")."_".$i]
								);
								
		}
		
	}
	 
	echo "Datos actualizados!";					
	 
	 
	 
	exit;
}


$asesor->validateMetas($comercial->id_comercial);
$data=$asesor->getMetas($comercial->id_comercial); 
 
?>
<div class="fsPage" style="width:100%">
<form action="" method="post" id="metas_form">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']; ?>"></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
     <thead>
      <tr style="background-color:#393;color:#FFF ">
        <th height="30" align="center"><strong>Año</strong></th>
        <th align="center"><strong>Mes</strong></th>
        <th align="center"><strong>Negocios</strong></th>
        <th align="center"><strong>Monto</strong></th>
      </tr>
      </thead>
<?php
	foreach($data as $key =>$row){
 ?>      
      <tr>
        <td align="center"><?php echo $row['ANO_META']?></td>
        <td height="35" align="center"><?php echo $row['MES_META']?></td>
        <td align="center"><input type="text" name="negocios_<?php echo $row['ANO_META']."_".$row['MES_META']?>" id="negocios_<?php echo $row['ANO_META']."_".$row['MES_META']?>" class="textfield_input" style="width:50px;" value="<?php echo $row['NEGOCIOS_META']?>"></td>
        <td align="center"><input type="text" name="monto_<?php echo $row['ANO_META']."_".$row['MES_META']?>" id="monto_<?php echo $row['ANO_META']."_".$row['MES_META']?>" class="textfield_input" style="width:80px;" value="<?php echo $row['MONTO_META']?>"></td>
      </tr>
<?php } ?>        
      <tr>
        <td>&nbsp;</td>
        <td height="30" align="center">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30" colspan="4" align="center">
        <button type="button" class="greenButton" id="bt_m_save" >Guardar</button>
          <button type="button" class="redButton" id="bt_m_cancel">Cancel</button></td>
        </tr>
      <tr>
        <td height="30" colspan="4">&nbsp;</td>
      </tr>
    
    </table></td>
    </tr>
</table>
</form>
</div>