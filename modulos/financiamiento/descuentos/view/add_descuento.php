<?php
if (!isset($protect)){
	exit;
}	
 
?>
<form name="form_descuento" id="form_descuento" method="post" action="" class="fsForm  fsSingleColumn">

<table id="plan_load" width="400" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td align="right"><strong>Codigo:</strong></td>
    <td>
      <input type="text" class=" required" name="codigo" id="codigo"  autocomplete="off" /> </td>
  </tr>
  <tr>
    <td align="right"><strong>Descripcion:</strong></td>
    <td> 
      <input name="descripcion" type="text" class="" id="descripcion"  autocomplete="off" /> </td>
  </tr>
  <tr>
    <td align="right"><strong>Monto:</strong></td>
    <td><span class="finder">
      <input type="text" class="" name="monto" id="monto"  autocomplete="off" />
    </span></td>
  </tr>
  <tr>
    <td align="right"><strong>Porcentaje:</strong></td>
    <td><span class="finder">
      <input type="text" class="" name="porcentaje" id="porcentaje"  autocomplete="off" />
      </span></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>ingresado %:</strong></td>
    <td align="left"><input name="ingresado" type="checkbox" id="ingresado" value="S" /></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Ingresado monto:</strong></td>
    <td align="left"><span class="finder">
      <input name="monto_ingresado" type="checkbox" id="monto_ingresado" value="S" />
    </span></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Negocios:</strong></td>
    <td align="left"><input name="desde" type="text"   class="" id="desde" size="5" style="width:50px;" placeholder="Desde"/>
-
  <input name="hasta" type="text"  class="" id="hasta" size="5"  style="width:50px;" placeholder="Hasta"/></td>
  </tr>
  <tr >
    <td align="right"><strong>Moneda:</strong></td>
    <td><select name="moneda" id="moneda"  class=""  style="height:30px;width:120px;">
      <option value="">Seleccionar</option>
      <option value="LOCAL">LOCAL</option>
      <option value="DOLARES">DOLARES</option>
    </select></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Prioridad:</strong></td>
    <td align="left"><span class="finder">
      <select name="prioridad" id="prioridad"  class=""  style="height:30px;width:120px;">
        <option value="">Seleccionar</option>
        <?php for($i=0;$i<=10;$i++){?>
        <option value="<?php echo $i;?>"><?php echo $i;?></option>
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
      <input name="autorizacion" type="checkbox" id="autorizacion" value="S" />
      </span></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="desc_bt_save"> Guardar</button>
      <button type="button" class="redButton" id="desc_bt_cancel">Cancel</button></td>
  </tr>
</table>
</form>