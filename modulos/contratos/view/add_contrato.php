<?php
if (!isset($protect)){
	exit;
}	
  
?>
<script>
 
var _contratos;

$(function(){					
  	_contratos= new Contratos('content_dialog'); 
	_contratos.createView('<?php echo $_REQUEST['prospectacion']?>');
	//_contratos.questionView();
});
 
 
</script>
<style>
.fsPage{
 margin-bottom:10px;	
}
 
 
table.display2 thead tr th{
	background-color: #CCC; !important
	color:#000; !important
	margin:10px 2px 2px 2px; !important
}
table.display2 tbody tr td{
	width:20px;	 !important
	margin:10px 2px 2px 2px; !important
}
table.display2 {
 width:100%;	  !important
}
table.display2 td {
	padding: 3px 10px; !important
}
.prospect_person_data{
	display:none;	
}
.contrato_contratante{
	display:none;	
}
</style>
<div id="contrato_div" style="height:400px;width:1200px;"  >
  <table width="98%" border="0" cellpadding="0" cellspacing="0"  id="contrato_main_page">
    <tr>
      <td valign="top">
        <form name="form_prospecto" id="form_prospecto" method="post" action="" class="fsForm  fsSingleColumn" onsubmit="return false;">
          <table width="900" border="1" cellpadding="5"   class="fsPage">
            <tr>
              <td align="left"><h2 style="background-color:#999 ">DATOS DEL CONTRATO</h2></td>
            </tr>
            <tr>
              <td align="left"><table width="400" border="0" cellspacing="5" style="padding-left:15px;" cellpadding="0">
                <tr>
                  <td><strong>TIPO INGRESO</strong></td>
                  <td><select name="tipo_ingreso" id="tipo_ingreso" class="form-control">
                    <option value="">Seleccione</option>
                    <option value="NUEVO" selected="selected">NUEVO</option>
                    <option value="MEJORA">MEJORA</option>
                    <option value="REACTIVACION">REACTIVACION</option>
                  </select></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><strong>SITUACION</strong></td>
                  <td><select name="situacion" id="situacion" class="form-control">
                    <option value="">Seleccione</option> 
                    <option value="PRE">PRENECESIDAD</option>
                    <option value="NSD">NECESIDAD</option>
                  </select></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><strong>EMPRESA</strong></td>
                  <td id="crtl_empresa">&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><strong>SERIE CONTRATO:</strong></td>
                  <td><input name="serie_contrato" type="text" id="serie_contrato" maxlength="2" class="form-control" /></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><strong>NO. CONTRATO:</strong></td>
                  <td><input name="no_contrato" type="text" id="no_contrato" maxlength="9"  class="form-control"/></td>
                  <td>&nbsp;</td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td width="100%" align="left"><h2 style="background-color:#999 ">DATOS DEL CONTRATANTE</h2></td>
            </tr>
            <tr>
              <td align="center">
         
              <table width="100%" border="0" cellspacing="0" cellpadding="0" id="main_prospecto"  class="fsPage" <?php if (!isset($_REQUEST['prospectacion'])){ echo 'style="display:none"'; }?> >
                <tr>
                  <td colspan="4" align="left"><h2  style="background-color:#CCC">INFORMACION GENERAL DEL PROSPECTO&nbsp;</h2></td>
                </tr>
                <tr>
                  <td><table width="100%" border="1" cellpadding="5"  style="border-spacing:10px;" id="info_prospecto">
                    <tr class="prospect_person_data">
                      <td width="34%" align="right"><strong>Nombre del Prospecto:</strong></td>
                      <td colspan="3" id="pros_nombre_completo">&nbsp;</td>
                    </tr>
                    <tr  class="prospect_person_data">
                      <td align="right"><strong>Documento de indentidad del Prospecto:</strong></td>
                      <td colspan="3"  id="pros_documento">&nbsp;</td>
                    </tr>
                    <tr  class="prospect_person_data">
                      <td align="right"><strong>Telefono:</strong></td>
                      <td width="15%"  id="pros_telefono">&nbsp;</td>
                      <td width="23%"><strong>Celular:</strong> <tt id="pros_celular"></tt></td>
                      <td width="28%"><strong>Oficina: </strong><tt  id="pros_oficina"></tt></td>
                    </tr>
                    <tr  class="prospect_person_data" id="pros_parentezco">
                      <td align="right"><strong>Parentesco</strong>:</td>
                      <td  id="pros_telefono2"><select name="prospect_parentesco" id="prospect_parentesco" class="required" title="Seleccione el parentezco!">
                        <option value="">Seleccione</option>
                        <?php 

$SQL="SELECT * FROM `tipos_parentescos` WHERE estatus=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_parentesco']);
?>
                        <option <?php echo $data_p['id_parentesco']==$row['id_parentesco']?'selected':''?> value="<?php echo $encriptID;?>"><?php echo $row['parentesco']?></option>
                        <?php } ?>
                      </select></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="4" align="center"><button type="button" class="" id="bt_grl_propecto">Seleccionar</button></td>
                    </tr>
                  </table></td>
                </tr>
              </table>

              </td>
            </tr>
            <tr>
              <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="fsPage" id="info_contratante">
                <tr>
                  <td colspan="4" align="left"><h2  style="background-color:#CCC" >INFORMACION GENERAL DEL CONTRATANTE</h2></td>
                </tr>
                <tr>
                  <td><table width="100%" border="1" cellpadding="5"  style="border-spacing:10px;">
                    <tr class="contrato_contratante">
                      <td align="right"><strong>Nombre del Titular:</strong></td>
                      <td colspan="3" id="contratante_nombre_completo">&nbsp;</td>
                    </tr>
                    <tr  class="contrato_contratante">
                      <td align="right"><strong>Documento de indentidad del Titular:</strong></td>
                      <td colspan="2" id="contrato_titular">&nbsp;</td>
                      <td width="30%">&nbsp;</td>
                    </tr>
                    <tr  class="contrato_contratante">
                      <td align="right"><strong>Telefono:</strong></td>
                      <td width="14%" id="contrato_telefono">&nbsp;</td>
                      <td width="22%"><strong>Celular:</strong><tt  id="contract_celular"></tt></td>
                      <td><strong>Oficina:</strong><tt  id="contract_oficina"></tt></td>
                    </tr>
                    <tr class="contrato_contratante">
                      <td width="34%" align="right"><strong>Direccion de cobro:</strong></td>
                      <td colspan="3" id="contrato_direccion">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="4" align="center"> 
                        <button type="button" class="" id="bt_find_person">Buscar</button></td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
          </table>
          <br>
          <table width="100%" border="1" cellpadding="5"   class="fsPage">
            <tr>
              <td width="100%" align="left"><h2  style="background-color:#999">REPRESENTANTE EN CASO DE AUSENCIA DEL CONTRATANTE</h2></td>
            </tr>
            <tr>
              <td align="left"><table width="100%" border="1" cellpadding="5"   class="fsPage" id="info_representante1">
                <tr>
                  <td width="100%" align="left"><h2  style="background-color:#CCC">REPRESENTANTE I</h2></td>
                </tr>
                <tr>
                  <td align="center"><table width="100%" border="0" cellspacing="5" cellpadding="0">
                    <tr>
                      <td><strong>PRIMER NOMBRE</strong></td>
                      <td><strong>SEGUNDO NOMBRE</strong></td>
                      <td><strong>TERCER NOMBRE</strong></td>
                      <td><strong>PRIMER APELLIDO</strong></td>
                      <td><strong>SEGUNDO APELLIDO</strong></td>
                      <td><strong>APELLIDO DE CASADA</strong></td>
                    </tr>
                    <tr>
                      <td id="con1_primer_nombre">&nbsp;</td>
                      <td id="con1_segundo_nombre">&nbsp;</td>
                      <td id="con1_tercer_nombre">&nbsp;</td>
                      <td id="con1_primer_apellido">&nbsp;</td>
                      <td id="con1_segundo_apellido">&nbsp;</td>
                      <td id="con1_apellido_casado">&nbsp;</td>
                    </tr>
                    <tr>
                      <td><strong>NO. CEDULA</strong></td>
                      <td><strong>PARENTESCO</strong></td>
                      <td><strong>FECHA DE NACIMIENTO</strong></td>
                      <td colspan="2"><strong>LUGAR NACIMIENTO</strong></td>
                      <td><strong>NUMERO DE TELEFONO</strong></td>
                    </tr>
                    <tr>
                      <td id="con1_cedula">&nbsp;</td>
                      <td id="con1_parentesco">&nbsp;</td>
                      <td id="con1_fecha_nacimiento">&nbsp;</td>
                      <td colspan="2" id="con1_nacionalidad">&nbsp;</td>
                      <td id="con1_numero_telefono">&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td align="center"><button type="button" class="" id="bt_grl_representante">Seleccionar</button></td>
                </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td align="left"><table width="100%" border="1" cellpadding="5"   class="fsPage" id="info_representante2">
                <tr>
                  <td align="left"><h2  style="background-color:#CCC" >REPRESENTANTE II</h2></td>
                </tr>
                <tr>
                  <td align="center"><table width="100%" border="0" cellspacing="5" cellpadding="0">
                    <tr>
                      <td><strong>PRIMER NOMBRE</strong></td>
                      <td><strong>SEGUNDO NOMBRE</strong></td>
                      <td><strong>TERCER NOMBRE</strong></td>
                      <td><strong>PRIMER APELLIDO</strong></td>
                      <td><strong>SEGUNDO APELLIDO</strong></td>
                      <td><strong>APELLIDO DE CASADA</strong></td>
                    </tr>
                    <tr>
                      <td id="con2_primer_nombre">&nbsp;</td>
                      <td id="con2_segundo_nombre">&nbsp;</td>
                      <td id="con2_tercer_nombre">&nbsp;</td>
                      <td id="con2_primer_apellido">&nbsp;</td>
                      <td id="con2_segundo_apellido">&nbsp;</td>
                      <td id="con2_apellido_casado">&nbsp;</td>
                    </tr>
                    <tr>
                      <td><strong>NO. CEDULA</strong></td>
                      <td><strong>PARENTESCO</strong></td>
                      <td><strong>FECHA DE NACIMIENTO</strong></td>
                      <td colspan="2"><strong>LUGAR NACIMIENTO</strong></td>
                      <td><strong>NUMERO DE TELEFONO</strong></td>
                    </tr>
                    <tr>
                      <td id="con2_cedula">&nbsp;</td>
                      <td id="con2_parentesco" >&nbsp;</td>
                      <td id="con2_fecha_nacimiento">&nbsp;</td>
                      <td colspan="2" id="con2_nacionalidad">&nbsp;</td>
                      <td id="con2_numero_telefono">&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td align="center"><button type="button" class="" id="bt_grl_representante2">Seleccionar</button></td>
                </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
              </table>
                <p>&nbsp;</p></td>
            </tr>
          </table>
          <table width="100%" border="1" cellpadding="5"   class="fsPage">
            <tr>
              <td width="100%" align="left"><h2  style="background-color:#999">BENEFICIARIO DEL SEGURO DE VIDA</h2></td>
            </tr>
            <tr>
              <td align="center"><table width="100%" border="1" cellpadding="5" class="fsPage" id="info_beneficiario1">
                <tr>
                  <td width="100%" align="left"><h2  style="background-color:#CCC">BENEFICIARIO I</h2></td>
                </tr>
                <tr>
                  <td align="center"><table width="100%" border="0" cellspacing="5" cellpadding="0">
                    <tr>
                      <td><strong>PRIMER NOMBRE</strong></td>
                      <td><strong>SEGUNDO NOMBRE</strong></td>
                      <td><strong>PRIMER APELLIDO</strong></td>
                      <td><strong>SEGUNDO APELLIDO</strong></td>
                      </tr>
                    <tr>
                      <td id="ben1_primer_nombre">&nbsp;</td>
                      <td id="ben1_segundo_nombre">&nbsp;</td>
                      <td id="ben1_primer_apellido">&nbsp;</td>
                      <td id="ben1_segundo_apellido">&nbsp;</td>
                      </tr>
                    <tr>
                      <td ><strong>DOCUMENTO</strong></td>
                       <td ><strong>PARENTESCO</strong></td>
                      <td><strong>FECHA DE NACIMIENTO</strong></td>
                      <td><strong>LUGAR NACIMIENTO</strong></td>
                      </tr>
                    <tr>
                      <td id="ben1_cedula">&nbsp;</td>
                       <td id="ben1_parentesco" >&nbsp;</td>
                      <td id="ben1_fecha_nacimiento">&nbsp;</td>
                      <td id="ben1_nacionalidad">&nbsp;</td>
                      </tr>
                    <tr>
                      <td>&nbsp;</td>
                       <td >&nbsp;</td>
                      <td>&nbsp;</td>
                      <td id="ben1_nacionalidad3">&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td align="center"><button type="button" class="" id="bt_beneficiario1">Crear</button></td>
                </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td align="center"><table width="100%" border="1" cellpadding="5"   class="fsPage" id="info_beneficiario2">
                <tr>
                  <td width="100%" align="left"><h2  style="background-color:#CCC">BENEFICIARIO II</h2></td>
                </tr>
                <tr>
                  <td align="center"><table width="100%" border="0" cellspacing="5" cellpadding="0">
                    <tr>
                      <td><strong>PRIMER NOMBRE</strong></td>
                      <td><strong>SEGUNDO NOMBRE</strong></td>
                      <td><strong>PRIMER APELLIDO</strong></td>
                      <td><strong>SEGUNDO APELLIDO</strong></td>
                      </tr>
                    <tr>
                      <td id="ben2_primer_nombre">&nbsp;</td>
                      <td id="ben2_segundo_nombre">&nbsp;</td>
                      <td id="ben2_primer_apellido">&nbsp;</td>
                      <td id="ben2_segundo_apellido">&nbsp;</td>
                      </tr>
                    <tr>
                      <td ><strong>DOCUMENTO</strong></td>
                       <td ><strong>PARENTESCO</strong></td>
                      <td><strong>FECHA DE NACIMIENTO</strong></td>
                      <td><strong>LUGAR NACIMIENTO</strong></td>
                      </tr>
                    <tr>
                      <td  id="ben2_cedula">&nbsp;</td>
                       <td id="ben2_parentesco" >&nbsp;</td>
                      <td id="ben2_fecha_nacimiento">&nbsp;</td>
                      <td id="ben2_nacionalidad">&nbsp;</td>
                      </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td align="center"><button type="button" class="" id="bt_beneficiario2">Crear</button></td>
                </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
              </table>
                <table width="100%" border="1" cellpadding="5"   class="fsPage" id="c_asesor">
                  <tr>
                    <td align="left"><h2  style="background-color:#999" >ASESOR</h2></td>
                  </tr>
                  <tr>
                    <td align="left" style="padding-left:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
                      <tr style="display:none">
                        <td width="144" ><strong>DIRECTOR DE DIVISION:</strong></td>
                        <td width="675" id="nombre_director" style="font-size:14px;"></td>
                      </tr>
                      <tr>
                        <td><strong>GERENTE DE GRUPO:</strong></td>
                        <td id="nombre_gerente_g" style="font-size:14px;"></td>
                      </tr>
                      <tr>
                        <td><strong>NOMBRE ASESOR:</strong></td>
                        <td id="nombre_asesor" style="font-size:14px;"></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td align="center"><button type="button" class="" id="bt_find_asesor">Buscar</button></td>
                  </tr>
                  <tr>
                    <td align="center">&nbsp;</td>
                  </tr>
              </table></td>
            </tr>
          </table>
          <table width="100%" border="1" cellpadding="5"   class="fsPage">
            <tr>
              <td width="100%" align="left" id="ch_financiamiento"><h2  style="background-color:#999">Financiamiento</h2></td>
            </tr>
            
            <tr>
              <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
                  <tbody>
                    <tr>
                      <td align="center"><strong>MONEDA</strong></td>
                      <td align="center"><strong>PLAZO</strong></td>
                      <td align="center"><strong>ENGANCHE</strong></td>
                    </tr>
                    <tr>
                      <td align="center" id="pln_moneda">&nbsp;</td>
                      <td align="center" id="pln_plazo">&nbsp;</td>
                      <td align="center" id="pln_enganche">&nbsp;</td>
                    </tr>
                  </tbody>
              </table></td>
            </tr>
            <tr>
              <td align="center" ><button type="button"  id="bt_ch_financiamiento" >Cambiar</button></td>
            </tr>
            <tr>
              <td align="center">&nbsp;</td>
            </tr>
          </table>
          <p>&nbsp;</p>
          <table width="100%" border="1" cellpadding="5"   class="fsPage plan_jardin_memorial" id="<?php echo System::getInstance()->Encrypt("PJM")?>"  >
            <tr>
              <td width="100%" align="left"><h2  style="background-color:#999">Parcela de cementerio</h2></td>
            </tr>
            <tr>
              <td align="left"><button type="button" class="" id="bt_add_propiedad">Agregar</button>
                &nbsp;</td>
            </tr>
            <tr>
              <td align="left" id="listado_productos">&nbsp;</td>
            </tr>
          </table>
          <table width="100%" border="1" cellpadding="5"   class="fsPage plan_capillas" id="<?php echo System::getInstance()->Encrypt("CJM")?>" >
            <tr>
              <td width="100%" align="left"><h2 style="background-color:#999">Producto funerario</h2></td>
            </tr>
            <tr>
              <td align="left"><button type="button" class="" id="bt_add_p_funerario">Agregar</button>
                &nbsp;</td>
            </tr>
            <tr>
              <td align="left" id="listado_servicios">&nbsp;</td>
            </tr>
          </table>
          <?php if ($protect->getIfAccessPageById(175)){ ?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
            <tr  >
              <td style="color:#F00"><strong>FECHA DE VENTA</strong></td>
              <td  style="font-size:14px;"><input type="text" name="fecha_venta" id="fecha_venta" size="10" class="textfield_input required"/></td>
            </tr>
            <tr>
              <td width="215"><strong>FECHA PRIMER PAGO</strong></td>
              <td width="685" style="font-size:14px;"><input type="text" name="fecha_primer_pago" id="fecha_primer_pago" size="10" class="textfield_input required"/></td>
            </tr>
          </table> 
         <?php } ?> 
          <table width="99%" border="0" cellspacing="0" cellpadding="0" style="padding-left:5px">
            <tr>
              <td colspan="2"><h2  style="background-color:#999">Observaciones</h2></td>
            </tr>
            <tr>
              <td colspan="2"><textarea name="observaciones" id="observaciones" style="width:99%" cols="45" rows="5"></textarea></td>
            </tr>
          </table>
          <p>&nbsp;</p>
          <br />
          <br>
        </form>
      </td>
      <td valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0"  style="background:#FFF;border:#CCC solid 1px;">
        <tr>
          <td height="25" style="color:#FFF;background:#009900;padding-left:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
            <tr>
              <td style="color:#FFF;"><strong>DATOS FINANCIEROS</strong></td>
              <td ><button type="button" class="orangeButton" id="bt_datos_financieros" style="float:right;margin-right:10%;">&nbsp;Agregar&nbsp;</button></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="25" style="padding-left:10px;">&nbsp;</td>
        </tr>
        <tr>
          <td height="25" style="color:#FFF;background:#009900;padding-left:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
            <tr>
              <td style="color:#FFF;"><strong>DETALLE CAJA</strong></td>
              <td ><button type="button" class="orangeButton" id="bt_caja_add_pagos" style="float:right;margin-right:10%;">&nbsp;Agregar&nbsp;</button></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="center" id="detalle_caja">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><table width="301"  border="0" align="right" cellpadding="0" cellspacing="0"  style="background:#FFF;">
            <tr class="detalle_costo">
              <td width="301" height="25" style="color:#FFF;background:#009900;padding-left:10px;"><strong>DESCUENTOS</strong></td>
            </tr>
            <tr class="detalle_costo">
              <td valign="baseline" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;"><table width="100%" border="0" cellspacing="1" cellpadding="0" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;">
                <tr>
                  <td width="100%" align="left"  ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="detalle_costo">
                      <td valign="baseline" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;">&nbsp;</td>
                    </tr>
                    <tr class="detalle_costo">
                      <td valign="baseline" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;"><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
                        <tr>
                          <td><strong>DESCUENTO x MONTO</strong></td>
                          <td><button type="button" class="orangeButton" id="bt_c_descuento_x_monto" style="float:right;margin-right:20%;">&nbsp;Agregar&nbsp;</button></td>
                        </tr>
                      </table></td>
                    </tr>
                    <tr class="detalle_costo">
                      <td id="descuento_x_monto">&nbsp;</td>
                    </tr>
                    <tr class="detalle_costo">
                      <td style="padding-left:8px;">&nbsp;</td>
                    </tr>
                    <tr class="detalle_costo">
                      <td style="padding-left:8px;padding-right:5px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><strong>DESCUENTO x PORCIENTO</strong></td>
                          <td><button type="button" class="orangeButton" id="bt_c_descuento_x_porciento" style="float:right;margin-right:20%;">&nbsp;Agregar&nbsp;</button></td>
                        </tr>
                      </table></td>
                    </tr>
                    <tr class="detalle_costo">
                      <td id="descuento_x_prociento">&nbsp;</td>
                    </tr>
                    <tr  >
                      <td >&nbsp;</td>
                    </tr>
                    </table></td>
                </tr>
              </table></td>
            </tr>
   
          </table></td>
        </tr>
      </table>
      
        <table width="300"  border="0" align="right" cellpadding="0" cellspacing="0"  style="background:#FFF;border:#CCC solid 1px;">
        <tr class="detalle_costo">
          <td height="25" style="color:#FFF;background:#009900;padding-left:10px;"><strong>DETALLE TOTALES</strong></td>
        </tr>
        <tr class="detalle_costo">
          <td style="padding-left:8px;padding-right:5px;padding-bottom:5px;" ><table width="100%" border="0" cellspacing="2" cellpadding="2" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;">
               <tr>
              <td width="59%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PRECIO  LISTA</strong></td>
              <td width="41%" style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="gdt_precio_lista">0</td>
            </tr>     
 
               <tr>
                 <td align="right" style="padding-left:8px;padding-right:10px;" id="gdt_detalle_monto_inicial"><strong>MONTO INCIAL (10.00%)</strong></td>
                 <td id="gdt_monto_inicial" style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;">0</td>
               </tr>
               <tr>
              <td align="right" style="padding-left:8px;padding-right:10px;"><strong>ABONOS CAJA</strong>(-)</td>
              <td id="gdt_monto_inicial_caja" style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;">0</td>
            </tr>
            <tr>
              <td align="right" style="padding-left:8px;padding-right:10px;"><strong>TOTAL DESCUENTO </strong>(-)</td>
              <td height="0" id="dt_total_descuento" style="padding-left:5px;border-bottom-style:double;border-bottom:#333 solid 1px;">0</td>
            </tr>  
            <tr>
              <td height="0" align="right" style="padding-top:2px;padding-left:8px;padding-right:10px;"> <strong>CAPITAL NETO A FINANCIAR</strong> </td>
              <td id="dt_capital_financiar_menos_descuentos" style="padding-top:2px;padding-left:5px;border-bottom-style:double;">0</td>
            </tr>
            <tr>
              <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>INTERES TOTAL A FINANCIAR</strong></td>
              <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="dt_total_interes_a_pagar">0</td>
            </tr>
            <tr>
              <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>MENSUALIDADES</strong></td>
              <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="dt_mensualidades">0</td>
            </tr>
            <tr>
              <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong> TOTAL DE LA DEUDA</strong></td>
              <td style="padding-top:10px;padding-left:5px;" id="dt_total_a_pagar">0</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="center" valign="top" id="alert_monto" style="display:none"><div style="background-color:#C30;color:#FFF;width:90%;padding:5px;margin:5px;border-radius:2px;font-size:18px;" id="alert_monto_mensaje">El Monto inicial es menor al 10%</div></td>
        </tr>
        <tr  >
          <td  >&nbsp;</td>
        </tr>
        <tr >
          <td  >&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center" valign="top">&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" valign="top"><button type="button" class="greenButton" id="bt_contrato_generar">Generar Solicitud</button>&nbsp; <button type="button" class="redButton" id="bt_contrato_cancel">Cancelar</button></td>
      <td align="center" valign="top">&nbsp;</td>
      </tr>
    <tr>
      <td align="center" valign="top">&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
  </table>

</div>
<div id="content_dialog" ></div>
<?php // SystemHtml::getInstance()->addModule("footer");?>