<?php
if (!isset($protect)){
	exit;
}	
 $desc=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
?>
<form name="form_descuento" id="form_descuento" method="post" action="" class="fsForm  fsSingleColumn">
  <table id="plan_load" width="400" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
    <tr>
      <td align="right"><strong>Codigo:</strong></td>
      <td><input name="codigo" type="text" disabled="disabled" class=" required" id="codigo"  autocomplete="off" value="<?php echo $desc->codigo?>" readonly="readonly" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Descripcion:</strong></td>
      <td><input name="descripcion" type="text" class="" id="descripcion"  autocomplete="off" value="<?php echo $desc->descripcion?>"  /></td>
    </tr>
    <tr>
      <td align="right"><strong>Monto:</strong></td>
      <td><span class="finder">
        <input type="text" class="" name="monto" id="monto"  autocomplete="off" value="<?php echo $desc->monto;?>" />
      </span></td>
    </tr>
    <tr>
      <td align="right"><strong>Porcentaje:</strong></td>
      <td><span class="finder">
        <input type="text" class="" name="porcentaje" id="porcentaje"  autocomplete="off" value="<?php echo $desc->porcentaje;?>" />
      </span></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>ingresado %:</strong></td>
      <td align="left"><span class="finder">
        <input name="ingresado" type="checkbox" id="ingresado" value="S" <?php echo $desc->ingresado=="S"?'checked="checked"':''?> />
      </span></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>Ingresado monto:</strong></td>
      <td align="left"><span class="finder">
        <input name="monto_ingresado" type="checkbox" id="monto_ingresado" value="S" <?php echo $desc->monto_ingresado=="S"?'checked="checked"':''?> />
      </span></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>Negocios:</strong></td>
      <td align="left">
<?php $sp=explode("-",$desc->negocios); ?>
      <input name="desde" type="text"   class="" id="desde" placeholder="Desde" style="width:50px;" value="<?php echo $sp[0];?>" size="5"/>
      -
      <input name="hasta" type="text"  class="" id="hasta" placeholder="Hasta"  style="width:50px;" value="<?php echo $sp[1];?>" size="5"/></td>
    </tr>
    <tr >
      <td align="right"><strong>Moneda:</strong></td>
      <td><?php echo $desc->moneda?></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>Prioridad:</strong></td>
      <td align="left"><span class="finder">
        <select name="prioridad" id="prioridad"  class=""  style="height:30px;width:120px;">
          <option value="">Seleccionar</option>
          <?php for($i=0;$i<=10;$i++){?>
       	  <option value="<?php echo $i;?>" <?php echo $desc->prioridad==$i?'selected="selected"':''?> ><?php echo $i;?></option>
          <?php } ?>
        </select>
      </span></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>Necesidad:</strong></td>
      <td align="left"><span class="finder">
        <input name="necesidad" type="checkbox" id="necesidad" value="S" <?php echo $desc->necesidad=="S"?'checked="checked"':''?> />
      </span></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>Pre-Necesidad:</strong></td>
      <td align="left"><span class="finder">
        <input name="prenecidad" type="checkbox" id="prenecidad" value="S" <?php echo $desc->prenecidad=="S"?'checked="checked"':''?> />
      </span></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>Autorización:</strong></td>
      <td align="left"><span class="finder">
        <input name="autorizacion" type="checkbox" id="autorizacion" value="S" <?php echo $desc->autorizacion=="S"?'checked="checked"':''?> />
      </span></td>
    </tr>
    <tr >
      <td align="right" valign="middle"><strong>Estatus:</strong></td>
      <td align="left"><select name="estado" id="estado" class="required">
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_status']);
?>
        <option value="<?php echo $encriptID?>"  <?php echo $desc->estatus==$row['id_status']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
        <?php } ?>
      </select></td>
    </tr>
    <tr>
      <td colspan="2"><input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']; ?>" /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><button type="button" class="greenButton" id="desc_bt_save"> Guardar</button>
        <button type="button" class="redButton" id="desc_bt_cancel">Cancel</button></td>
    </tr>
  </table>
</form>