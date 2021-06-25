<?php

if (!isset($protect)){
	exit;
}

$drow=array();
if ((validateField($_REQUEST,"serie_contrato") && validateField($_REQUEST,"no_contrato"))){
	$serie_contrato=System::getInstance()->Decrypt($_REQUEST['serie_contrato']);
	$no_contrato=System::getInstance()->Decrypt($_REQUEST['no_contrato']);	
	$SQL="SELECT * FROM `sys_direcciones` WHERE `serie_contrato`='". mysql_real_escape_string($serie_contrato)."' AND `no_contrato`='". mysql_real_escape_string($no_contrato)."'";
	$rs= mysql_query($SQL); 
	$drow=mysql_fetch_assoc($rs); 	
}
 
  
?>
<div class="modal fade" id="modal_create_address_contrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:750px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Direccion de cobro</h4>
      </div>
      <div class="modal-body">
<form method="post"  action="" id="addess_c_view"  name="addess_c_view" class="fsForm fsPage"  >
  <table width="100%" border="0" cellpadding="0" cellspacing="0" id="master_direction" >
    <tr  >
    <td colspan="3"><label class="fsLabel fsRequiredLabel" for="label">Busqueda rapida por sector:</label><span class="fsLabel fsRequiredLabel">
      <input type="text" id="faster_search" name="faster_search" size="30" value=""  placeholder="Buscador rapido de direcciones" />
      </span>  </td>
  </tr>
  <tr>
    <td colspan="3" id="_address"><table width="100%" border="0" cellpadding="5" cellspacing="5" >
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Provincia<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label4">Ciudad<span>*</span></label></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><select name="provincia_id" id="provincia_id" class="required"  >
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `sys_provincia` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idprovincia'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>" <?php echo $drow['idprovincia']==$row['idprovincia']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        <td colspan="2"  id="ciudad_charge"><select name="cuidad_id" id="cuidad_id" class="required"   >
          <option value="">Seleccione</option>
          <?php 
		
		$SQL="SELECT * FROM `sys_ciudad` WHERE status_2='A' and idmunicipio='". mysql_escape_string($drow['idmunicipio']) ."'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idciudad'],$protect->getSessionID());
		?>
          <option value="<?php echo $encriptID?>"  <?php echo $drow['idciudad']==$row['idciudad']?'selected="selected"':''?>><?php echo $row['Descripcion']?></option>
          <?php } ?>
        </select></td>
      </tr>
      <tr>
        <td><stron><span class="fsLabel fsRequiredLabel">Sector<span>*</span></span></strong></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" id="sector_charge"><select name="sector_id" id="sector_id" class="required"  >
          <option value="">Seleccione</option>
          <option value="-2">Agregar nuevo</option>
          <?php 
		
		$SQL="SELECT * FROM `sys_sector` WHERE status='A' and idciudad='". mysql_escape_string($drow['idciudad']) ."'";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idsector'],$protect->getSessionID());
		?>
          <option value="<?php echo $encriptID?>"  <?php echo $drow['idsector']==$row['idsector']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Avenida:<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Calle:</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Zona:</label></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_avenida" name="direccion_avenida" size="20" value="<?php echo $drow['avenida']?>"  />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_calle" name="direccion_calle" size="20" value="<?php echo $drow['calle']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_zona" name="direccion_zona" size="20" value="<?php echo $drow['zona']?>"    />
        </span></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Numero:</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Manzana:</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Departamento:</label></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_numero" name="direccion_numero" size="20" value="<?php echo $drow['numero']?>"  />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_manzana" name="direccion_manzana" size="20" value="<?php echo $drow['manzana']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_departamento" name="direccion_departamento" size="20" value="<?php echo $drow['departamento']?>"  />
        </span></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Torre/ Residencial / Colonia / Condominio :</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Referencia:</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label2">Observaciones:</label></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_recidencia" name="direccion_recidencia" size="20" value="<?php echo $drow['residencia_colonia_condominio']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_referencia" name="direccion_referencia" size="20" value="<?php echo $drow['referencia']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <textarea name="direccion_observacion" cols="20"  id="direccion_numero" ><?php echo $drow['observaciones']?></textarea>
        </span></td>
      </tr>
      <tr >
        <td>&nbsp;</td>
        <td></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" align="center"><button type="button" id="add_addres_buttom"  >Guardar </button>
          </td> 
      </tr>
      <tr>
        <td colspan="3" align="center">&nbsp;</td>
      </tr>
 
    </table></td>
  </tr>
 
</table>
 </form>      
 </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>