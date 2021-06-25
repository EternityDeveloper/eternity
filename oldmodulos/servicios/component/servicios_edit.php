<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
if (!isset($_REQUEST['id'])){
	echo "Debe seleccionar un componente!";
	exit;
}

$id=System::getInstance()->Request("id");

$SQL="SELECT * FROM `servicios` where serv_codigo='". mysql_escape_string($id)."' limit 1";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
 
 ?>
<form action="" method="post" enctype="multipart/form-data" name="form_user_edit" class="fsForm  fsSingleColumn" id="form_user_edit" onSubmit="return false;">
  <table width="100%" border="1">
    <tr>
      <td valign="top"><table width="100%" border="1" class="fsPage">
        <tr>
          <td align="right"><strong>Codigo:</strong></td>
          <td><input name="serv_codigo_" type="text" disabled="disabled" id="serv_codigo_" value="<?php echo $row['serv_codigo']?>">
            <input name="submit_services_edit" type="hidden" id="submit_services_edit" value="1" />
            <input name="serv_codigo" type="hidden" id="serv_codigo" value="<?php echo $row['serv_codigo']?>" /></td>
        </tr>
        <tr >
          <td align="right"><strong>Empresa:</strong></td>
          <td><select name="EM_ID" id="EM_ID"  class="textfield "  style="height:30px;">
            <option value="">Seleccionar</option>
            <?php 
	 	 $SQL="SELECT EM_ID,`EM_NOMBRE`,`por_interes_local`,`por_interes_dolares`,`por_enganche`,`por_impuesto` FROM `empresa` ";
		$rs=mysql_query($SQL);
		while($rowx=mysql_fetch_assoc($rs)){  
	  ?>
            <option value="<?php echo System::getInstance()->Encrypt($rowx['EM_ID']);?>" <?php echo $rowx['EM_ID']==$row['EM_ID']?'selected':''; ?>><?php echo $rowx['EM_NOMBRE'] ?></option>
            <?php } ?>
          </select></td>
        </tr>
        <tr>
          <td align="right" valign="top"><strong>Descripción:</strong></td>
          <td><textarea name="serv_descripcion" id="serv_descripcion"><?php echo $row['serv_descripcion']?></textarea></td>
        </tr>
        <tr>
          <td align="right"><strong>Costo:</strong></td>
          <td><input name="serv_costo" type="text" id="serv_costo" value="<?php echo $row['serv_costo']?>"></td>
        </tr>
        <tr>
          <td align="right"><strong>Cta contable:</strong></td>
          <td><input name="serv_cta_contable" type="text" id="serv_cta_contable" value="<?php echo $row['serv_cta_contable']?>"></td>
        </tr>
        <tr>
          <td align="right"><strong>Promocion:</strong></td>
          <td><input name="serv_promocion" type="text" id="serv_promocion" value="<?php echo $row['serv_promocion']?>" /></td>
        </tr>
        <tr>
          <td align="right"><strong>Lista Precio  <br />
            Pre-necesidad Local</strong></td>
          <td><select name="serv_precio_venta_local_pre" id="serv_precio_venta_local_pre">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($rowx['CODIGO_TP']);
?>
            <option value="<?php echo $encriptID?>" <?php  
						if ($rowx['CODIGO_TP']==$row['serv_precio_venta_local_pre']){ 
							echo 'selected="selected"';
						}
					 ?>><?php echo $rowx['CODIGO_TP']?></option>
            <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td align="right"><strong>Lista Precio <br />
            Pre-necesidad Dolar</strong></td>
          <td><select name="serv_precio_venta_dolares_pre" id="serv_precio_venta_dolares_pre">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($rowx['CODIGO_TP']);
?>
            <option value="<?php echo $encriptID?>" <?php  
						if ($rowx['CODIGO_TP']==$row['serv_precio_venta_dolares_pre']){ 
							echo 'selected="selected"';
						}
					 ?>><?php echo $rowx['CODIGO_TP']?></option>
            <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td align="right"><strong>Lista Precio  <br />
            Necesidad Local</strong></td>
          <td><select name="serv_precio_venta_local_nec" id="serv_precio_venta_local_nec">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($rowx['CODIGO_TP']);
?>
            <option value="<?php echo $encriptID?>" <?php  
						if ($rowx['CODIGO_TP']==$row['serv_precio_venta_local_nec']){ 
							echo 'selected="selected"';
						}
					 ?>><?php echo $rowx['CODIGO_TP']?></option>
            <?php } ?>
          </select></td>
        </tr>
        <tr>
          <td align="right"><strong>Lista Precio <br />
            Necesidad Dolar</strong></td>
          <td><select name="serv_precio_venta_dolares_nec" id="serv_precio_venta_dolares_nec">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($rowx['CODIGO_TP']);
?>
            <option value="<?php echo $encriptID?>" <?php  
						if ($rowx['CODIGO_TP']==$row['serv_precio_venta_dolares_nec']){ 
							echo 'selected="selected"';
						}
					 ?>><?php echo $rowx['CODIGO_TP']?></option>
            <?php } ?>
          </select></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center"><button type="button" class="positive" id="bt_save"> <img src="images/apply2.png" alt=""/> Guardar</button>
            <button type="button" class="positive" id="bt_cancel"> <img src="images/cross.png" alt=""/> Cancel</button></td>
        </tr>
      </table></td>
      <td valign="top"><table width="100%" border="1"  class="fsPage">
        <tr>
          <td><h2>Componentes del servicio</h2></td>
        </tr>
        <tr>
          <td><button type="button" class="positive" name="adicionar_componente"  id="adicionar_componente" > <img src="images/apply2.png" alt=""/> Adicionar componente</button>&nbsp;</td>
        </tr>
        <tr>
          <td><table border="0" class="display" id="servicios_table" style="font-size:13px">
            <thead>
              <tr>
                <th>Componente</th>
                <th>Sub-Componente</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              <?php
$SQL="SELECT *,
	componentes_servicio.serv_codigo as codigo,
	componentes_servicio.id_componente as idcomponent,
	componentes_servicio.sub_subcomponente as s_subcomponente
	
 FROM `componentes_servicio` 
INNER JOIN `componentes` ON (`componentes`.`id_componente`=componentes_servicio.id_componente)
LEFT JOIN `subcomponentes` ON (`subcomponentes`.`sub_subcomponente`=componentes_servicio.`sub_subcomponente`)
WHERE componentes_servicio.`serv_codigo`='".mysql_escape_string($id)."' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$data=array(	
			"serv_codigo"=>$row['codigo'],
			"id_componente"=>$row['idcomponent'],
			"sub_subcomponente"=>$row['s_subcomponente']
		);
//print_r($data);
			
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($data),$protect->getSessionID());
?>
              <tr>
                <td height="25"><?php echo $row['descripcion_comp']?></td>
                <td><?php echo $row['sub_descripcion']?></td>
                <td align="center" ><a href="#" onclick="removeComponent('<?php echo $encriptID;?>')"><img src="images/cross.png"  /></a></td>
              </tr>
              <?php 
}
 ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
<?php 
} ?>