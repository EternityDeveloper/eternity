<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

 

?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_user_edit" id="form_user_edit" method="post" action="" class="fsForm  fsSingleColumn">
 
<table width="100%" border="1" class="fsPage fsPage2">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="right">Jardin:</td>
    <td><select name="jardin" id="jardin">
      <option value="<?php 
	//$row=array("nombre"=>"","id_nit"=>0);
   // echo System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
		?>">Seleccione</option>
      <?php 

$SQL="SELECT  * FROM `jardines`   ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" ><?php echo $row['id_jardin']?> - <?php echo $row['jardin']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right">Fase:</td>
    <td><select name="fase" id="fase">
      <option value="<?php 
	//$row=array("nombre"=>"","id_nit"=>0);
   // echo System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
		?>">Seleccione</option>
      <?php 

$SQL="SELECT * FROM fases ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" ><?php echo $row['id_fases']?> - <?php echo $row['fase']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td align="right"> 
      <p>
        <label>
          <input name="sbloque" type="radio"  id="sbloque" onclick="change_bloque('0')" value="0" checked="checked"/>
          Modulo</label>
        <label>
          <input type="radio" name="sbloque"  id="sbloque" value="1"  onclick="change_bloque('1')"/>
          Modulos multiples</label>
   
      </p></td>
    <td>
      <div id="bloques_1" style="display:none">
        <input name="bloque_from" type="text" id="bloque_from" style="width:30px" maxlength="3" /> - 
        <input name="bloque_to" type="text" id="bloque_to" style="width:30px" maxlength="3"  />
        </div>
      <div id="bloques_2">
        <input name="bloque" type="text" id="bloque" style="width:50px" maxlength="3" /> 
        </div>   
      </td>
  </tr>

  <tr>
    <td align="right">Parcelas:</td>
    <td><select name="lotes_from" id="lotes_from">
   	  <option value="">Seleccione</option>
		<?php
        	for($i=0;$i<=60;$i++){
		?>
        	 <option value="<?php echo $i;?>" <?php
             	if (count($data)>0){
					if ($data->cavidades==$i){
						echo "selected";	
					}	
				}
			 
			 ?>><?php echo $i;?></option>
     <?php }?>
       
    </select>
      
      -
      <select name="lotes_to" id="lotes_to">
         	  <option value="">Seleccione</option>
		<?php
        	for($i=0;$i<=60;$i++){
		?>
        	 <option value="<?php echo $i;?>" <?php
             	if (count($data)>0){
					if ($data->cavidades==$i){
						echo "selected";	
					}	
				}
			 
			 ?>><?php echo $i;?></option>
     <?php }?>
      </select></td>
  </tr>
  <tr>
    <td align="right">Cavidades </td>
    <td><select name="cavidades" id="cavidades">
      <option value="">Seleccione</option> 
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
    </select></td>
  </tr>
  <tr>
    <td align="right">Osarios</td>
    <td><select name="osarios" id="osarios">
      <option value="">Seleccione</option> 
      	<option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
    </select></td>
  </tr>
  <tr>
    <td align="right">Empresa</td>
    <td><select name="empresa" id="empresa">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM empresa WHERE estatus=1";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['EM_ID'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" ><?php echo $row['EM_NOMBRE']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_lotes_add" type="hidden" id="submit_lotes_add" value="1" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#"  class="negative" id="bt_cancel"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table>
 
</form>