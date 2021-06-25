<?php
if (!isset($protect)){
	exit;
}	

SystemHtml::getInstance()->includeClass("contratos","Contratos"); 

//$data=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
if (!isset($_REQUEST['id'])){
	echo "Error contrato no existe!";
	exit;
}	 

$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (!isset($contrato->serie_contrato)){
	echo "Error contrato no existe!";
	exit;
}	
 
$ct= new Contratos(UserAccess::getInstance()->getDBLink());

$_asesor=new Asesores($protect->getDBLink());


$asesores=$_asesor->getComercialParentData($contrato->asesor);
//print_r($contrato);

$id_prospecto=System::getInstance()->Encrypt($contrato->prospecto);
$id_contrante=System::getInstance()->Encrypt($contrato->id_nit_cliente);

$cdata=$ct->getBasicInfoContrato($contrato->serie_contrato,$contrato->no_contrato);

$_REQUEST['no_contrato']=System::getInstance()->Encrypt($contrato->no_contrato);
$_REQUEST['serie_contrato']=System::getInstance()->Encrypt($contrato->serie_contrato);



//System::getInstance()->Decrypt($_REQUEST['serie_contrato']),System::getInstance()->Decrypt($_REQUEST['no_contrato'])
?>
<script>
 
var _contratos;

$(function(){
 						
  	_contratos= new Contratos('content_dialog'); 
	_contratos.setContrato('<?php echo System::getInstance()->Encrypt($contrato->serie_contrato);?>',
									'<?php echo System::getInstance()->Encrypt($contrato->no_contrato);?>');
	_contratos.editContratoView('<?php echo $id_contrante ?>','<?php echo $id_prospecto ?>');
	 
 
	
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

h2{
	margin:0px;
	
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
                  <td><strong>EMPRESA</strong></td>
                  <td><select name="empresa" id="empresa" disabled>
                    <option value="">Seleccione</option>
                    <?php 

$SQL="SELECT * FROM empresa WHERE estatus=1";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(trim($row['EM_ID']));
?>
                    <option value="<?php echo $encriptID?>" <?php echo $contrato->EM_ID==$row['EM_ID']?'selected':''; ?> ><?php echo $row['EM_NOMBRE']?></option>
                    <?php } ?>
                  </select></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><strong>SERIE CONTRATO:</strong></td>
                  <td><input name="serie_contrato" type="text" disabled id="serie_contrato" value="<?php echo $contrato->serie_contrato?>" /></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><strong>NO. CONTRATO:</strong></td>
                  <td><input name="no_contrato" type="text" disabled id="no_contrato" value="<?php echo $contrato->no_contrato?>" /></td>
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
                      <td colspan="3" id="contratante_nombre_completo"><?php echo $row['id_nit_cliente']?></td>
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
                  
                  </table></td>
                </tr>
              </table></td>
            </tr>
          </table>
          <br>
          <table width="100%" border="1" cellpadding="5"   class="fsPage">
            <tr>
              <td width="100%" align="left"><h2  style="background-color:#999">BENEFICIARIO DEL SEGURO DE VIDA</h2></td>
            </tr>
            <tr>
              <td align="center" id="beneficiarios_main"><table width="100%" border="1" cellpadding="5" class="fsPage" id="info_beneficiario1">
                <tr>
                  <td width="100%" align="left"><h2  style="background-color:#CCC">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>BENEFICIARIO I</td>
                        <td><span style="float:right;margin-right:10px;margin-top:0px;padding-top:0px;top:-30px;cursor:pointer" id="bt_remove_beneficiario1"><img src="images/cross.png"  /></span></td>
                      </tr>
                    </table></h2></td>
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
                  <td align="center"><button type="button" class="" id="bt_beneficiario1">Cambiar</button>&nbsp;&nbsp;<button type="button" id="beneficiario1_save" style="display:none">Guardar</button>&nbsp;&nbsp;<button type="button" id="beneficiario1_cancel" style="display:none">Cancelar</button></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td align="center"><table width="100%" border="1" cellpadding="5"   class="fsPage" id="info_beneficiario2">
                <tr>
                  <td width="100%" align="left"><h2  style="background-color:#CCC">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>BENEFICIARIO II </td>
                        <td><span style="float:right;margin-right:10px;margin-top:0px;padding-top:0px;top:-30px;cursor:pointer" id="bt_remove_beneficiario2"><img src="images/cross.png"  /></span></td>
                      </tr>
                    </table></h2></td>
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
                  <td align="center"><button type="button" class="" id="bt_beneficiario2">Cambiar</button>&nbsp;&nbsp;<button type="button" id="beneficiario2_save" style="display:none">Guardar</button>&nbsp;&nbsp;<button type="button" id="beneficiario2_cancel" style="display:none">Cancelar</button></td>
                </tr>
              </table></td>
            </tr>
          </table>
          <table width="100%" border="1" cellpadding="5"   class="fsPage">
            <tr>
              <td width="100%" align="left"><h2  style="background-color:#999">REPRESENTANTE EN CASO DE AUSENCIA DEL CONTRATANTE</h2></td>
            </tr>
            <tr>
              <td align="left"><table width="100%" border="1" cellpadding="5"   class="fsPage" id="info_representante1">
                <tr>
                  <td width="100%" align="left">
					<h2  style="background-color:#CCC;padding:5px 5px 5px 5px;" >
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>REPRESENTANTE I </td>
                        <td>&nbsp;</td>
                      </tr>
                    </table></h2>
                  </td>
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
                  <td align="center"><button type="button" class="" id="bt_grl_representante">Seleccionar</button>&nbsp;&nbsp;<button type="button" id="bt_grl_representante_save" style="display:none">Guardar</button>&nbsp;&nbsp;<button type="button" id="bt_grl_representante_cancel" style="display:none">Cancelar</button></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td align="left"><table width="100%" border="1" cellpadding="5"   class="fsPage" id="info_representante2">
                <tr>
                  <td align="left"><h2  style="background-color:#CCC;padding:5px 5px 5px 5px;" >
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>REPRESENTANTE II </td>
                        <td><span style="float:right;margin-right:10px;margin-top:0px;padding-top:0px;top:-30px;cursor:pointer" id="bt_remove_representante1"><img src="images/cross.png"  /></span>&nbsp;</td>
                      </tr>
                    </table></h2></td>
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
                  <td align="center"><button type="button" class="" id="bt_grl_representante2">Seleccionar</button>&nbsp;&nbsp;<button type="button" id="bt_grl_representante_save2" style="display:none">Guardar</button>&nbsp;&nbsp;<button type="button" id="bt_grl_representante_cancel2" style="display:none">Cancelar</button></td>
                </tr>
              </table>
                <table width="100%" border="1" cellpadding="5"   class="fsPage" id="c_asesor">
                  <tr>
                    <td align="left"><h2  style="background-color:#999" >ASESOR</h2></td>
                  </tr>
                  <tr>
                    <td align="left" style="padding-left:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
                      <tr style="display:none">
                        <td width="144"><strong>DIRECTOR DE DIVISION:</strong></td>
                        <td width="675" id="nombre_director" style="font-size:14px;"><?php echo $asesores[0]['nombre']." ". $asesores[3]['apellido']; ?></td>
                      </tr>
                      <tr>
                        <td><strong>GERENTE DE GRUPO:</strong></td>
                        <td id="nombre_gerente_g" style="font-size:14px;"><?php echo $asesores[1]['nombre']." ". $asesores[1]['apellido']; ?></td>
                      </tr>
                      <tr>
                        <td><strong>NOMBRE ASESOR:</strong></td>
                        <td id="nombre_asesor" style="font-size:14px;"><?php echo $asesores[2]['nombre']." ". $asesores[2]['apellido']; ?></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td align="center"><button type="button" style="display:none" id="bt_find_asesor">Buscar</button></td>
                  </tr>
                  <tr>
                    <td align="center">&nbsp;</td>
                  </tr>
                </table>
              <p>&nbsp;</p></td>
            </tr>
          </table>
          <table width="100%" border="1" cellpadding="5"   class="fsPage plan_jardin_memorial" id="<?php echo System::getInstance()->Encrypt("PJM")?>" >
            <?php 
			  
	$SQL="SELECT inventario_jardines.*,producto_contrato.*,sys_status.descripcion as estatus FROM `producto_contrato`
INNER JOIN `inventario_jardines` ON 
(inventario_jardines.`bloque`=producto_contrato.bloque AND
inventario_jardines.`lote`=producto_contrato.lote AND
inventario_jardines.`id_fases`=producto_contrato.id_fases AND
inventario_jardines.`id_jardin`=producto_contrato.id_jardin  AND
inventario_jardines.`osario`=producto_contrato.osario  )
INNER JOIN `sys_status` ON (sys_status.id_status=inventario_jardines.estatus)
 WHERE  producto_contrato.serie_contrato='".$contrato->serie_contrato."' AND producto_contrato.no_contrato='".$contrato->no_contrato."' AND producto_contrato.id_estatus=1 ";	
  
	 
$rs=mysql_query($SQL);
		
if  (mysql_num_rows($rs)>0){
?>
            <tr>
              <td width="100%" align="left"><h2  style="background-color:#999">Parcela de cementerio</h2></td>
            </tr>
            <tr style="display:none">
              <td align="left"><button type="button" class="" id="bt_add_propiedad">Agregar</button>
                &nbsp;</td>
            </tr>
            <tr>
              <td align="left" id="listado_productos"><?php 
		
		while($row=mysql_fetch_assoc($rs)){ 
 
			$rand=rand(10,1000);	
			$id=System::getInstance()->Encrypt(json_encode($row));
	  
			  ?>
                <table width="200" border="0" cellspacing="0" cellpadding="0" class="fsPage detalle_costo" id="producto_<?php echo $rand;?>" style="width:200px;">
                  <tr>
                    <td class="titlest"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="80%" id="title_<?php echo $rand;?>"><?php echo $row['id_jardin']."-". $row['id_fases']."-".$row['bloque']."-". $row['lote'] ;?></td>
                        <td>&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td ><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
                      <tr >
                        <td><strong>JARDIN</strong></td>
                        <td><?php echo $row['id_jardin']?></td>
                      </tr>
                      <tr >
                        <td><strong>FASE</strong></td>
                        <td ><?php echo $row['id_fases']?></td>
                      </tr>
                      <tr style="">
                        <td width="38%" style=""><strong>BLOQUE</strong></td>
                        <td width="62%" ><?php echo $row['bloque']?></td>
                      </tr>
                      <tr style="">
                        <td width="38%" style=""><strong>LOTE</strong></td>
                        <td width="62%" ><?php echo $row['lote']?></td>
                      </tr>
                    </table></td>
                  </tr>
                </table>
                <?php 		}
}

?></td>
            </tr>
          </table>
          <table width="100%" border="1" cellpadding="5"   class="fsPage plan_capillas" id="<?php echo System::getInstance()->Encrypt("CJM")?>" >
            <?php

			   
	$SQL="SELECT * FROM `producto_contrato`
INNER JOIN `servicios` ON (servicios.`serv_codigo`=producto_contrato.serv_codigo)
 WHERE  producto_contrato.serie_contrato='".$contrato->serie_contrato."' AND producto_contrato.no_contrato='".$contrato->no_contrato."' ";	
 
	$rs=mysql_query($SQL);
	
if  (mysql_num_rows($rs)>0){
?>
            <tr>
              <td width="100%" align="left"><h2 style="background-color:#999">Producto funerario</h2></td>
            </tr>
            <tr style="display:none">
              <td align="left"><button type="button" class="" id="bt_add_p_funerario">Agregar</button></td>
            </tr>
            <tr>
              <td align="left" id="listado_servicios"><?php

	 
		while($row=mysql_fetch_assoc($rs)){ 
			$rand=rand(10,1000); 
			$id=System::getInstance()->Encrypt(json_encode($row));
 
			  ?>
                <table width="200" border="0" cellspacing="0" cellpadding="0" class="fsPage detalle_costo" id="producto_<?php echo $rand;?>2" style="width:200px;">
                  <tr>
                    <td class="titlest"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="80%" id="title_<?php echo $rand;?>2"><?php echo $row['serv_descripcion']?></td>
                        <td>&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td ><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
                      <tr >
                        <td width="38%"><strong>PLAN</strong></td>
                        <td width="62%"><?php echo $row['serv_descripcion']?></td>
                      </tr>
                      <tr >
                        <td><strong>CODIGO</strong></td>
                        <td ><?php echo $row['serv_codigo']?></td>
                      </tr>
                    </table></td>
                  </tr>
                </table>
                <?php 		}
}
 ?></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-left:5px">
            <tr>
              <td><h2  style="background-color:#999">Documentos </h2>
                <button type="button" id="bt_add_document"  >Agregar</button></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td id="listado_documentos"><?php include("listar_documentos.php")?></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-left:5px">
            <tr>
              <td colspan="2"><h2  style="background-color:#999">Datos de cobro</h2></td>
            </tr>
            <tr>
              <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="5" style="">
                <tr>
                  <td width="215"><strong>FECHA PRIMER PAGO</strong></td>
                  <td width="685" style="font-size:14px;"><?php echo $contrato->fecha_primer_pago?></td>
                </tr>
            
                <tr>
                  <td><span style="font-size:14px;"><strong>FECHA VENTA</strong></span></td>
                  <td  style="font-size:14px;"><?php echo $contrato->fecha_venta?></td>
                </tr>
                <tr>
                  <td><strong>FORMA DE PAGO</strong></td>
                  <td  style="font-size:14px;"><?php echo $contrato->forma_de_pago?></td>
                </tr>
                <tr>
                  <td><strong>DIRECCION DE COBROS</strong></td>
                  <td  style="font-size:14px;" ><?php 		
		$SQL="SELECT
			 sys_direcciones.*,
		CONCAT(sys_provincia.`descripcion`,',',sys_ciudad.Descripcion,',',sys_sector.`descripcion`) AS direccion
FROM 
`sys_direcciones` 
INNER JOIN `sys_sector` ON (`sys_sector`.`idsector`=sys_direcciones.idsector)
INNER JOIN `sys_ciudad` ON (`sys_sector`.`idciudad`=sys_ciudad.idciudad)
INNER JOIN `sys_municipio` ON (`sys_municipio`.`idmunicipio`=sys_ciudad.idmunicipio) 
INNER JOIN `sys_provincia` ON (`sys_provincia`.`idprovincia`=sys_municipio.idprovincia)
WHERE sys_direcciones.serie_contrato='".$contrato->serie_contrato."' AND sys_direcciones.no_contrato='".$contrato->no_contrato."' ";
 
		$rs=mysql_query($SQL);
		$cant=mysql_num_rows($rs);
		$id_dir="";
		while($row=mysql_fetch_assoc($rs)){
			 echo $row['direccion'];
		}
 	 
		?></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><h2  style="background-color:#999">Observaciones</h2></td>
            </tr>
            <tr>
              <td colspan="2"><textarea name="observaciones" id="observaciones" disabled="disabled" style="width:99%" cols="45" rows="5"><?php echo $contrato->observaciones?></textarea></td>
            </tr>
          </table>
          <p>&nbsp;</p>
          <br />
          <br>
        </form>
      </td>
      <td valign="bottom"><table width="300"  border="0" align="right" cellpadding="0" cellspacing="0"  style="background:#FFF;border:#CCC solid 1px;">
        <tr class="detalle_costo">
          <td height="25" style="color:#FFF;background:#009900;padding-left:10px;"><strong>DETALLE TOTALES</strong></td>
        </tr>
        <tr class="detalle_costo">
          <td style="padding-left:8px;padding-right:5px;padding-bottom:5px;" ><table width="100%" border="0" cellspacing="2" cellpadding="2" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;">
            <tr>
              <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>MONEDA</strong></td>
              <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" ><?php echo $contrato->tipo_moneda?></td>
            </tr>
            <tr>
              <td width="73%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PRECIO  LISTA</strong></td>
              <td width="27%" style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="dt_capital_total_a_pagar"><?php echo number_format($contrato->precio_lista,2);?></td>
            </tr>
            <tr>
              <td align="right" style="padding-left:8px;padding-right:10px;"><strong>TOTAL DESCUENTO </strong>(-)</td>
              <td height="0" id="dt_total_descuento" style="padding-left:5px;border-bottom-style:double;"><span style="padding-top:10px;padding-left:5px;border-bottom-style:double;"><?php echo number_format($contrato->descuento,2);?></span></td>
            </tr>
            <tr>
              <td align="right" style="padding-left:8px;padding-right:10px;"><strong> INCIAL</strong> (-)</td>
              <td id="dt_monto_inicial" style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;"><span style="padding-top:10px;padding-left:5px;border-bottom-style:double;"><?php echo number_format($contrato->enganche,2);?></span></td>
            </tr>
            <tr>
              <td height="0" align="right" style="padding-top:2px;padding-left:8px;padding-right:10px;"><strong>TASA</strong></td>
              <td id="dt_capital_financiar_menos_descuentos2" style="padding-top:2px;padding-left:5px;border-bottom-style:double;"><span style="padding-top:10px;padding-left:5px;border-bottom-style:double;"><?php echo number_format($cdata->porc_interes,2);?></span></td>
            </tr>
            <tr>
              <td height="0" align="right" style="padding-top:2px;padding-left:8px;padding-right:10px;"><strong>CAPITAL NETO A FINANCIAR</strong></td>
              <td id="dt_capital_financiar_menos_descuentos" style="padding-top:2px;padding-left:5px;border-bottom-style:double;"><span style="padding-top:10px;padding-left:5px;border-bottom-style:double;"><?php echo number_format($contrato->precio_neto,2);?></span></td>
            </tr>
            <tr>
              <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>INTERES TOTAL A FINANCIAR</strong></td>
              <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="dt_total_interes_a_pagar"><?php echo number_format($contrato->interes,2);?></td>
            </tr>
            <tr>
              <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>MENSUALIDADES</strong></td>
              <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="dt_mensualidades"><?php
              echo number_format($contrato->valor_cuota,2);
			  ?></td>
            </tr>
            <tr>
              <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong> TOTAL DE LA DEUDA</strong></td>
              <td style="padding-top:10px;padding-left:5px;" id="dt_total_a_pagar"><?php
              echo number_format($contrato->interes+$contrato->precio_neto,2);
			  ?></td>
            </tr>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center" valign="top">&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" valign="top"> <button type="button" class="greenButton"  id="bt_imprimir" contrato="<?php echo $_REQUEST['id'] ?>">Imprimir</button> &nbsp;<button type="button" class="redButton" id="bt_contrato_cancel">Cancelar</button></td>
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